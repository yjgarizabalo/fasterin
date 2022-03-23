<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class presupuesto_control extends CI_Controller {
//Variables encargadas de los permisos que tiene el usuario en session
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
 //Construtor del controlador, se importa el modelo personas_model y se inicia la session
	public function __construct()
	{
        parent::__construct();
        $this->load->model('presupuesto_model');
        $this->load->model('genericas_model');
        session_start();
        date_default_timezone_set("America/Bogota");
//la variable Super_estado es la encargada de notificar si el usuario esta en sesion, si no esta en sesion no podra ejecutar ninguna funcion, cuando pasa eso se retorna sin_session en la funcion que se esta ejecutando,por otro lado las variables Super_elimina, Super_modifica, Super_agrega se encarga de delimitar los permisos que tiene el perfil del usuario en la actividad que esta trabajando, si no tiene permiso las variables toman un valor de 0 y no les permite ejecutar la funcion retornando -1302.
      if (isset($_SESSION["usuario"])) {
          $this->Super_estado = true;
          $this->Super_elimina = 1;
          $this->Super_modifica = 1;
          $this->Super_agrega = 1;

      }
  }
/**
 * Mustra la ventana de presupuesto
 * 
 * @return void
 */
	public function index($page = 'presupuesto',$id = -1)
	{
    $pages = "inicio";
    $data['js'] = "";
    $data['actividad'] = "Ingresar";
    if ($this->Super_estado) {
        $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $page);
        if (!empty($datos_actividad)) {
            $pages = $page;
            $data['js'] = "presupuesto";
            $data['id'] = $id;
            $data['actividad'] = $datos_actividad[0]["id_actividad"];
        }else{
            $pages = "sin_session";
            $data['js'] = "";
            $data['actividad'] = "Permisos";
        }
    }
    $this->load->view('templates/header', $data);
    $this->load->view("pages/" . $pages);
    $this->load->view('templates/footer');
  }

  public function buscar_codigo_sap()
  {
      $buscar = $this->input->post("buscar");
      $idparametro = $this->input->post("idparametro");
      $resp = array();
      if ($this->Super_estado && !empty($buscar)) {
        $datos = $this->genericas_model->consultar_generica("(idparametro = '$idparametro' AND estado = 1) AND (valor LIKE '%$buscar%' OR valorx LIKE '%$buscar%')");
        foreach ($datos as $row) {
            $row["gestion"] = '<span style="color: #39B23B;" title="Seleccionar Orden" data-toggle="popover" data-trigger="hover" class="pointer btn btn-default fa fa-check-square-o"></span>';
            $resp["data"][] = $row;
      }
    }
      echo json_encode($resp);
  }
  public function guardar_traslado()
  {
      if ($this->Super_estado == false) {
          $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
      } else {
          if ($this->Super_agrega == 0) {
              $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
          } else {
              $id =(int) $this->input->post('id');
              $id_ano = (int) $this->input->post('id_ano');
              $id_orden_origen = (int) $this->input->post('id_orden_origen');
              $id_cuenta_origen = (int) $this->input->post('id_cuenta_origen');
              $id_orden_destino = (int)$this->input->post("id_orden_destino");
              $id_cuenta_destino = (int)$this->input->post('id_cuenta_destino');
              $valor =(int) $this->input->post('valor');
              $justificacion = $this->input->post('justificacion');
              $tipo_add = $this->input->post('tipo');
              $tipo_traslado = $this->input->post('tipo_traslado');
              $usuario_registra = $_SESSION["persona"];
              
            if ($tipo_add == 4 && empty($id)) {
                $resp= ['mensaje'=>"Error al cargar el ID de la solicitud, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else if (empty($id_orden_origen) && ($tipo_traslado != 'CA' && $tipo_traslado != 'OA')) {
                $resp= ['mensaje'=>"Debe seleccionar la orden o centro de origen.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (empty( $id_cuenta_origen) && ($tipo_traslado != 'CA' && $tipo_traslado != 'OA')) {
                $resp= ['mensaje'=>"Debe seleccionar la cuenta de origen.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (empty( $id_orden_destino) && ($tipo_traslado != 'CD' && $tipo_traslado != 'OD')) {
                $resp= ['mensaje'=>"Debe seleccionar la orden o centro de destino.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (empty($id_cuenta_destino) && ($tipo_traslado != 'CD' && $tipo_traslado != 'OD')) {
                $resp= ['mensaje'=>"Debe seleccionar la cuenta de destino.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (empty($id_ano)) {
                $resp= ['mensaje'=>"Debe seleccionar el años para el traslado.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($id_cuenta_destino == $id_cuenta_origen && $id_orden_origen == $id_orden_destino) {
                $resp= ['mensaje'=>"No se puede realizar un traslado a la misma cuenta.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if(empty($valor)){
                $resp= ['mensaje'=>"El valor no puede estar vacio y debe ser un numero.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if($valor < 1){
                $resp= ['mensaje'=>"El valor debe ser un numero mayor a 1.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if(empty($justificacion)){
                $resp= ['mensaje'=>"Debe justificar el motivo del traslado.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else {
                $men =  $tipo_add == 3 ||  $tipo_add == 4 ? 'Modificado' :'Agregado';
                $resp= ['mensaje'=>"El traslado fue $men de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!",'tipo_traslado' => $tipo_add];
                $add = 0;
                if($tipo_add == 4){
                    $estado_valido = $this->validar_estado($id,'Tras_Modi');
                    if($estado_valido){
                        $data = array(
                            "id_ano"=>$id_ano,
                            'id_orden_origen'=>$tipo_traslado != 'CA' && $tipo_traslado != 'OA' ? $id_orden_origen:NULL,
                            'id_cuenta_origen'=>$tipo_traslado != 'CA' && $tipo_traslado != 'OA' ? $id_cuenta_origen:NULL,
                            'id_orden_destino'=>$tipo_traslado != 'CD' && $tipo_traslado != 'OD' ? $id_orden_destino:NULL,
                            'id_cuenta_destino'=>$tipo_traslado != 'CD' && $tipo_traslado != 'OD' ? $id_cuenta_destino:NULL,
                            "valor"=>$valor,
                            "justificacion"=>$justificacion,
                        ); 
                        $add = $this->presupuesto_model->modificar_datos($data, "presupuesto_traslados",$id);
                    }else{
                        $resp = ['mensaje'=>"El traslado ya fue gestionado anteriormente o no esta autorizado para realizar esta operación..",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                    }
                }
                if ($add != 0) $resp = ['mensaje'=>"Error al $men el traslado, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];     
            }
              
          }
      }
      echo json_encode($resp);
  }

  public function agregar_solicitud()
  {
      if ($this->Super_estado == false) {
          $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
      } else {
          if ($this->Super_agrega == 0) {
              $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
          } else {
              $traslados =  $this->input->post('traslados');
              $usuario_registra = $_SESSION["persona"];
            if (empty($traslados)) {
                $resp= ['mensaje'=>"No se encontraron traslados en la solicitud.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else {
              
                $data = array("usuario_registra"=>$usuario_registra);
                $add = $this->presupuesto_model->guardar_datos($data, "solicitudes_presupuestos");
                $data_traslados = array();
                if ($add == 0) {
                    $id = $this->presupuesto_model->traer_ultima_solicitud_usuario($usuario_registra);
                    foreach ($traslados as $tras) {
                        array_push($data_traslados,array(
                            'tipo_traslado'=>$tras['tipo_traslado'],
                            'id_solicitud'=>$id,
                            'id_ano'=>$tras['id_ano'],
                            'id_orden_origen'=>$tras['tipo_traslado'] != 'CA' && $tras['tipo_traslado'] != 'OA' ? $tras['id_orden_origen']:NULL,
                            'id_cuenta_origen'=>$tras['tipo_traslado'] != 'CA' && $tras['tipo_traslado'] != 'OA' ? $tras['id_cuenta_origen']:NULL,
                            'id_orden_destino'=>$tras['tipo_traslado'] != 'CD' && $tras['tipo_traslado'] != 'OD' ? $tras['id_orden_destino']:NULL,
                            'id_cuenta_destino'=>$tras['tipo_traslado'] != 'CD' && $tras['tipo_traslado'] != 'OD' ? $tras['id_cuenta_destino']:NULL,
                            'valor'=>$tras['valor'],
                            'justificacion'=>$tras['justificacion'],
                            'usuario_registra'=>$usuario_registra,
                             //'id_estado_traslado'=>$tras['tipo_traslado'] == 'OO' || $tras['tipo_traslado'] == 'CA' || $tras['tipo_traslado'] == 'OA'?'Tras_Soli':'Tras_Apro',
                             'id_estado_traslado'=>'Tras_Soli',
                            ));
                    }
                    $add = $this->presupuesto_model->guardar_datos($data_traslados, "presupuesto_traslados",2);
                    $resp= ['mensaje'=>"Los tarslados fueron registrados de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!",'id' => $id];
                    if ($add != 0) {
                        $resp = ['mensaje'=>"Error al guardar los traslados, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                    }
                  
                }else{
                    $resp = ['mensaje'=>"Error al guardar la solicitud, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                }      
            }
              
          }
      }
      echo json_encode($resp);
  }

  public function listar_traslados_solicitudes()
  {
    $traslados = array();
    if ($this->Super_estado == true) {
        $id_solicitud =  $this->input->post('id_solicitud');
      $estado =  $this->input->post('estado');
      $fecha =  $this->input->post('fecha');
      $usuario = $_SESSION['persona'];  
      if ($this->Super_estado == true) {
        $administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Admin_Pre'? true : false; 
        $datos = $this->presupuesto_model->listar_traslados_solicitudes($id_solicitud,$estado,$fecha);
        $solicitado= '<span  style="background-color: #EABD32;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
        $comite = '<span  style="background-color: #2E79E5;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
        $aprobado = '<span  style="background-color: #39B23B;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
        $cancelado= '<span   style="background-color: #d9534f;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
        $procesando= '<span   style="background-color: #8F1899;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';

        $cerrada = '<span  title="Traslado Cerrado" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
        $abierta = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn" style="color:#428bca"></span>';
    
        foreach ($datos as $row) {
            $row["valor_final"] =  is_null($row["valor_aprobado"] ) ? $row["valor"] :$row["valor_aprobado"];
            $row["valor_final"] = $this->convertir_moneda($row["valor_final"],true,0);
            $row["valor_format"] = $this->convertir_moneda($row["valor"],true,0);
            $row["valor_aprobado_format"] = $this->convertir_moneda($row["valor_aprobado"],true,0);
            if($row['id_estado_traslado'] == 'Tras_Com'){
                $row["gestion"] = $administra ?" <span style='color: #2E79E5;' title='Abrir Traslado Comité' data-toggle='popover' data-trigger='hover' class='fa fa-folder-open pointer btn btn-default' onclick='abrir_traslado_comite(".json_encode($row).")'></span>":$abierta;
                $row["ver"] =  $comite;
            }else  if($row['id_estado_traslado'] == 'Tras_Neg' || $row['id_estado_traslado'] == 'Tras_Can') {
                $row["ver"] =  $cancelado;
                $row["gestion"] = $cerrada;
            }else  if($row['id_estado_traslado'] == 'Tras_Apro'){
                $row["ver"] =  $aprobado;
                $row["gestion"] = $cerrada;
            }else if($row['id_estado_traslado'] == 'Tras_Soli'){
                $gestionar = '<span title="Procesar" style="color: #8F1899;" data-toggle="popover" data-trigger="hover" class="fa fa-refresh pointer btn btn-default" onclick="gestionar_solicitud(`Tras_Pros`,' . $row["id"] . ',`Procesar Traslado .?`)"></span> <span title="Enviar Para Aprobación" style="color: #24C6E3;" data-toggle="popover" data-trigger="hover" class="fa fa-edit pointer btn btn-default" onclick="gestionar_solicitud_texto(`Tras_Vis`,' . $row["id"] . ',`Requiere Aprobación .?`,`Correo o Numero Identificación`)"></span>';
                $normal  = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-remove btn btn-default" onclick="gestionar_solicitud(`Tras_Can`,' . $row["id"] . ',`Cancelar Traslado .?`)"></span>'. " <span style='color: #2E79E5;' title='Editar' data-toggle='popover' data-trigger='hover' class='fa fa-wrench pointer btn btn-default' onclick='mostrar_traslado_modificar(".json_encode($row).",4)'></span>";;
                if ($administra)  $row["gestion"] = $gestionar;
                else if ( $usuario == $row['usuario_registra'])  $row["gestion"] = $normal;
                if ($_SESSION['perfil'] == 'Per_Admin')  $row["gestion"] = $gestionar.$normal;
                $row["ver"] =  $solicitado;
            }else if($row['id_estado_traslado'] == 'Tras_Pros' || $row['id_estado_traslado'] == 'Tras_Acep'){
                $row["gestion"] = $administra ? '<span title="Aprobar" data-toggle="popover" data-trigger="hover" style="color: #39B23B;margin-left: 5px" class="fa fa-check btn btn-default" onclick="gestionar_solicitud(`Tras_Apro`,' . $row["id"] . ',`Traslado Aprobado .?`)"></span><span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-ban btn btn-default" onclick="gestionar_solicitud_texto(`Tras_Neg`,' . $row["id"] . ',`Traslado Negado .?`)"></span><span title="Comité" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px"class="fa fa-folder-open btn btn-default" onclick="gestionar_solicitud_texto(`Tras_Com`,' . $row["id"] . ',`Traslado a comité .?`)"></span>' : $abierta;
                $row["ver"] =  $solicitado;
            }else if($row['id_estado_traslado'] == 'Tras_Vis'){
                $row["gestion"] = $_SESSION['perfil'] == 'Per_Admin' || $row['id_usuario_vb'] ==  $_SESSION['persona']  ? '<span title="Aceptar" data-toggle="popover" data-trigger="hover" style="color: #39B23B;margin-left: 5px" class="fa fa-thumbs-up btn btn-default" onclick="gestionar_solicitud(`Tras_Acep`,' . $row["id"] . ',`Aceptar Traslado.?`)"></span><span title="Descartar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-thumbs-down btn btn-default" onclick="gestionar_solicitud_texto(`Tras_Des`,' . $row["id"] . ',`Descartar Traslado .?`)"></span>' : $abierta;
                $row["ver"] =  $solicitado;
            }else if($row['id_estado_traslado'] == 'Tras_Des'){
                $row["gestion"] = $administra ? '</span><span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-ban btn btn-default" onclick="gestionar_solicitud_texto(`Tras_Neg`,' . $row["id"] . ',`Traslado Negado .?`)"></span>' : $abierta;
                $row["ver"] =  $cancelado;
            }
            $traslados["data"][] = $row;
        }
      }
    }
      echo json_encode($traslados);
  }

  public function gestionar_solicitud()
  {
      if ($this->Super_estado == false) {
          $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
      } else {
          if ($this->Super_modifica == 0) {
              $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
          } else {
              $estado =  $this->input->post('estado');
              $mensaje =  $this->input->post('mensaje');
              $id =  (int) $this->input->post('id');
              $id_alt =  (int) $this->input->post('id_alt');
              $usuario_registra = $_SESSION["persona"];
              $administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Admin_Pre'? true : false; 
              
            if (empty($estado) || empty($id)) {
                $resp= ['mensaje'=>"Error al cargar la información del traslado, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else {
                $estado_valido = $this->validar_estado($id,$estado);
                if ($estado_valido) {
                    $data_estado = array(
                        "usuario_registro"=>$usuario_registra,
                        "id_traslado"=>$id,
                        "id_estado"=>$estado
                    );
                    $aprobados_val =  $estado == 'Tras_Apro' ? $this->validar_aprobados_traslado_comite($id) : true;
                    if($aprobados_val){
                        $sw = true; 
                        $data = array( "id_estado_traslado"=>$estado);
                        if($estado == 'Tras_Com'){
                            if (empty($mensaje)) {
                                $sw = false;
                                $resp = ['mensaje'=>"Debe justificar el motivo del traslado.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                            }else{
                                $data['justificacion_comite'] = $mensaje;
                                $data_estado['observaciones'] = $mensaje;
                                $comite = $this->presupuesto_model->traer_comite('tipo = "presupuesto"');
                                if (!empty($comite)) {
                                    $data['id_comite'] = $comite->{'id'};
                                }else{
                                    $sw = false;
                                    $resp = ['mensaje'=>"No se encontro comité activo para asignar el traslado.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                                }
                            }
                        }else if($estado == 'Tras_Vis'){
                            if (empty($id_alt)) {
                                $sw = false;
                                $resp = ['mensaje'=>"Debe seleccionar la persona encargada de dar el AVAL para el traslado.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                            }else{
                                $data['id_usuario_vb'] = $id_alt;
                            }
                        }else if($estado == 'Tras_Neg'){
                            if (empty($mensaje)) {
                                $sw = false;
                                $resp = ['mensaje'=>"Debe justificar el motivo por el cual es negado el traslado.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                            }else{
                                $data['mensaje'] = $mensaje;
                                $data_estado['observaciones'] = $mensaje;
                            }
                        }else if($estado == 'Tras_Des'){
                            if (empty($mensaje)) {
                                $sw = false;
                                $resp = ['mensaje'=>"Debe justificar el motivo por el cual es descartado el traslado.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                            }else{
                                $data_estado['observaciones'] = $mensaje;
                            }
                        }
                        
                        if($sw){
                            $add = $this->presupuesto_model->guardar_datos($data_estado, "historial_estado_traslados");
                            if ($add == 0) {
                                $mod = $this->presupuesto_model->modificar_datos($data, "presupuesto_traslados",$id);
                                $traslado = $this->presupuesto_model->traer_traslado_id($id);
                                $resp= ['mensaje'=>"El estado fue modificado con éxito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!", 'traslado' => $traslado];
                                if ($mod != 0) {
                                    $resp = ['mensaje'=>"Error al modificar el estado del traslado, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                                }
                            }else{
                                $resp = ['mensaje'=>"Error al gestionar el traslado, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                            }
                        }                        
                    }else{
                        $resp = ['mensaje'=>"No es posible continuar, el traslado no cuenta con el Numero de avales mínimo por parte del comité de presupuesto.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                    }
                   
                }else{
                    $resp = ['mensaje'=>"El traslado ya fue gestionado anteriormente o no esta autorizado para realizar esta operación..",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }
       
            }
              
          }
      }
      echo json_encode($resp);
  }

  public function listar_estados_tralados()
  {
      $estados = array();
      $id_traslado =  $this->input->post('id_traslado');
      if ($this->Super_estado == true) {
        $estado_inicial = $this->presupuesto_model->traer_traslado_id($id_traslado,2);
        $row = ['id_traslado'=>$id_traslado,'persona'=>$estado_inicial->{'persona'},'fecha_registro'=>$estado_inicial->{'fecha_registra'},'estado_traslado'=>$estado_inicial->{'estado_traslado'},'observaciones'=>''];
        $estados["data"][] = $row;
        $datos = $this->presupuesto_model->listar_estados_tralados($id_traslado);
        foreach ($datos as $row) {
             $estados["data"][] = $row;
        }
      }
      echo json_encode($estados);
  }

  public function validar_estado($id,$estado_nuevo){
    $traslado = $this->presupuesto_model->traer_traslado_id($id);
    $administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Admin_Pre'? true : false; 
    $persona = $_SESSION["persona"];
    if ($administra && $traslado->{'id_estado_traslado'} == 'Tras_Soli' && ($estado_nuevo == 'Tras_Pros' || $estado_nuevo == 'Tras_Vis')) {
        return true; 
    }else if ($administra && ($traslado->{'id_estado_traslado'} == 'Tras_Pros' || $traslado->{'id_estado_traslado'} == 'Tras_Acep') && ($estado_nuevo == 'Tras_Apro' || $estado_nuevo == 'Tras_Neg' ||  $estado_nuevo == 'Tras_Com')) {
        return true; 
    }else if($administra && $traslado->{'id_estado_traslado'} == 'Tras_Com' && ($estado_nuevo == 'Tras_Apro' || $estado_nuevo == 'Tras_Neg')){
        return true;
    }else if(($_SESSION['perfil'] == 'Per_Admin' || $persona == $traslado->{'usuario_registra'}) && $traslado->{'id_estado_traslado'} == 'Tras_Soli' && $estado_nuevo == 'Tras_Can'){
        return true;
    }else if(($_SESSION['perfil'] == 'Per_Admin' || $persona == $traslado->{'usuario_registra'}) && $traslado->{'id_estado_traslado'} == 'Tras_Soli' && $estado_nuevo == 'Tras_Modi'){
        return true;
    }else if(($_SESSION['perfil'] == 'Per_Admin' || $persona == $traslado->{'id_usuario_vb'}) && $traslado->{'id_estado_traslado'} == 'Tras_Vis' && ($estado_nuevo == 'Tras_Acep' || $estado_nuevo == 'Tras_Des')){
        return true;
    }else if ($administra && $traslado->{'id_estado_traslado'} == 'Tras_Des' &&  $estado_nuevo == 'Tras_Neg') {
        return true; 
    }
    return false;
  }

  public function listar_comites()
  {
      $comites = array();
      if ($this->Super_estado == true) {
        $datos = $this->presupuesto_model->listar_comites();
        $hoy = date("Y-m-d");  
        $cerrado = '<span title="" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>'; 
        $activo = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: #39B23B;color: white; width: 100%;" class="pointer form-control" ><span >ver</span></span>';
        $inactivo = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%;" class="pointer form-control" ><span>ver</span></span>';
        $i = 1;
        foreach ($datos as $row) {
            $row["estado_alt"] =  $i > 1 ? 'Cerrado' : 'Activo';
            $row["gestion"] =  $i > 1 ? $cerrado :'<span style="color: #2E79E5;" title="Editar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench pointer btn btn-default" onclick="mostrar_datos_comite_modificar(' . $row["id"] . ')"></span>';
            $row["codigo"] = $i > 1 ? $inactivo :$activo;  
            $comites["data"][] = $row;
            $i++;
        }
    }
      echo json_encode($comites);
  }

  public function traer_comite()
  {
    if ($this->Super_estado == false) {
        $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    }else{
        $id = $this->input->post("id");
        $comite = $this->presupuesto_model->traer_comite("id = $id");
        if (empty($comite)) {
            $resp= ['mensaje'=>"No se encontro la información del comité seleccionado.",'tipo'=>"info",'titulo'=> "Oops.!"];
        }else{
            $resp= ['mensaje'=>"Comité encontrado.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!",'data' => $comite];
        }
    }
    echo json_encode($resp);
  }

  public function modificar_comite()
  {
    if ($this->Super_estado == false) {
        $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    } else {
        if ($this->Super_modifica == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        } else {
            $id= (int) $this->input->post("id");
            $nombre= $this->input->post("nombre");
            $fecha = $this->input->post("fecha");
            $descripcion = $this->input->post("descripcion");
            if (empty($id)) {
             $resp= ['mensaje'=>"Error al cargar el ID del comité, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else  if (ctype_space($nombre) || empty($nombre)) {
                $resp= ['mensaje'=>"Ingrese nombre.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $sw = true;
                if ($fecha) {
                  $fecha_valida = $this->validateDate($fecha, 'Y-m-d');
                  if (!$fecha_valida) {
                      $resp= ['mensaje'=>"Ingrese una fecha de cierre con formato valido y debe no puede ser menor a la fecha actual.",'tipo'=>"info",'titulo'=> "Oops.!"];
                      $sw = false;
                  }
                }else $fecha = null;
                if ($sw) {
                    $data = array("nombre" => $nombre,"descripcion" => $descripcion,'fecha_cierre' => $fecha);
                    $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Comité Modificado.!"];
                    $mod = $this->presupuesto_model->modificar_datos($data,'comites',$id);
                    if($mod != 0) $resp= ['mensaje'=>"Error al modificar el comité, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
    }
      echo json_encode($resp);
  }
  public function guardar_comite()
  {
    if ($this->Super_estado == false) {
        $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    } else {
        if ($this->Super_agrega == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        } else {
              $nombre= $this->input->post("nombre");
              $tipo= $this->input->post("tipo");
              $fecha = $this->input->post("fecha");
              $descripcion = $this->input->post("descripcion");
              $usuario_registra = $_SESSION['persona'];
  
              if (ctype_space($nombre) || empty($nombre)) {
                $resp= ['mensaje'=>"Ingrese nombre.",'tipo'=>"info",'titulo'=> "Oops.!"];
              }else{
                  $sw = true;
                  if ($fecha) {
                    $fecha_valida = $this->validateDate($fecha, 'Y-m-d');
                    if (!$fecha_valida) {
                        $resp= ['mensaje'=>"Ingrese una fecha de cierre con formato valido y debe no puede ser menor a la fecha actual.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        $sw = false;
                    }
                  }else $fecha = null;
                  
                  if ($sw) {
                    $comite = $tipo == 'presupuesto' ? $this->presupuesto_model->traer_comite("tipo = '$tipo'"):'';
                    $data = array("nombre" => $nombre,"descripcion" => $descripcion,"tipo" => $tipo,"usuario_registra" => $usuario_registra,'fecha_cierre' => $fecha);
                    $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Comité Guardado.!"];
                    $add = $this->presupuesto_model->guardar_datos($data,'comites');
                    if($add != 0) $resp= ['mensaje'=>"Error al guardar el comité, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    else if(!empty($comite)) $this->presupuesto_model->modificar_datos(['fecha_cierre' => date("Y-m-d H:i")],'comites',$comite ->{'id'});
                  }
   
              }
          }
      }
      echo json_encode($resp);
  }

    public function listar_traslados_por_comite()
    {
        $traslados = array();
        $id_comite= $this->input->post("id_comite");
        $tipo = $this->input->post("tipo");
        $usuario= $_SESSION['persona'];
        if ($this->Super_estado == true) {
            $datos = $this->presupuesto_model->listar_traslados_por_comite($id_comite, $usuario, $tipo);
            foreach ($datos as $row) {
                $id =$row['id'];
                $aprobo = $row["aprobo"];
                $row["ver"] =  '<span  style="color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
                $row["valor_format"] =  is_null($row["valor_aprobado"]) ? $row["valor"] :$row["valor_aprobado"];
                $row["valor_format"] = $this->convertir_moneda($row["valor"],true,0);
                if($tipo == 2){
                    if(is_null($aprobo)) {
                        $row["gestion"] = "<span class='btn btn-default' onclick='aprobar_revertir_traslado_comite($id,1,`Aprobado`)'><span  class='fa fa-check' style='color: #39B23B' ></span> Aprobar</span> <span class='btn btn-default' onclick='aprobar_revertir_traslado_comite($id,1,`Negado`)'><span  class='fa fa-ban' style='color: #d9534f' ></span> Negar</span>";
                    }else{
                        if ($row["id_estado_traslado"] == 'Tras_Com') {
                            $row["gestion"] = "<span class='btn btn-default' onclick='aprobar_revertir_traslado_comite($id,0,``,$aprobo)'><span  class='fa fa-reply-all red'></span> Revertir</span>";
                        }else{
                            $row["gestion"] = "<span>Cerrado</span>";
                        }                
                    }
                }else{
                    $row["gestion"] = "<span>Cerrado</span>";
                }
                    $traslados["data"][] = $row;
            }
        }
        echo json_encode($traslados);
    }

    public function aprobar_revertir_traslado_comite()
    {
      if ($this->Super_estado == false) {
          $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
      } else {
          if ($this->Super_agrega == 0) {
              $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
          } else {
                $id_traslado= (int) $this->input->post("id_traslado");
                $estado= (int) $this->input->post("estado");
                $id= (int) $this->input->post("id");
                $tipo_ap= $this->input->post("tipo_ap");
                $usuario_registra = $_SESSION['persona']; 
                if (empty($id_traslado)) {
                  $resp= ['mensaje'=>"Error al cargar el ID del traslado, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    if($estado == 0){
                        $traslado = $this->presupuesto_model->traer_traslado_id($id_traslado);
                        if ($traslado ->{"id_estado_traslado"} == 'Tras_Com') {
                            $data = array("estado" => 0);
                            $resp= ['mensaje'=>"El visto bueno al traslado fue retirado con éxito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            $mod = $this->presupuesto_model->modificar_datos($data,'traslados_aprobados_comite',$id);
                            if($mod != 0) $resp= ['mensaje'=>"Error al revertir el aprobado, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }else{
                            $resp= ['mensaje'=>"El traslado ya fue gestionado por el administrador del proceso.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }
                    }else{
                        $con_aprobado = $this->presupuesto_model->traer_aprobado_persona($usuario_registra, $id_traslado);
                        if (!empty($con_aprobado)) {
                            $resp= ['mensaje'=>"Este traslado ya fue aprobado o negado anteriormente.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }else{
                            $data = array("id_traslado" => $id_traslado,"tipo" => $tipo_ap,"usuario_registra" => $usuario_registra);
                            $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Traslado Aprobado.!"];
                            $add = $this->presupuesto_model->guardar_datos($data,'traslados_aprobados_comite');
                            if($add != 0) $resp= ['mensaje'=>"Error al aprobar el traslado, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                      
                }  
            }
        }
        echo json_encode($resp);
    }

    public function listar_aprobados_traslado_comite()
    {
        $aprobados = array();
        $id_traslado= $this->input->post("id_traslado");
        if ($this->Super_estado == true) {
            $datos = $this->presupuesto_model->listar_aprobados_traslado_comite($id_traslado);
            $aprobado = '<span  style="background-color: #39B23B;color: white; width: 100%; ;" class="pointer form-control" ><span >Aprobado</span></span>';
            $negado= '<span   style="background-color: #d9534f;color: white; width: 100%; ;" class="pointer form-control" ><span >Negado</span></span>';
            foreach ($datos as $row) {
                $row['tipo'] = $row['tipo'] == 'Aprobado' ? $aprobado : $negado;
                $aprobados["data"][] = $row;
            }
        }
        echo json_encode($aprobados);
    }

    public function modificar_valor_aprobado()
    {
      if ($this->Super_estado == false) {
          $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
      } else {
          if ($this->Super_modifica == 0) {
              $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
          } else {
                $id_traslado= (int) $this->input->post("id_traslado");
                $valor = $this->input->post("valor");
                if (empty($id_traslado)) {
                  $resp= ['mensaje'=>"Error al cargar el ID del traslado, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else if (empty($valor)) {
                    $resp= ['mensaje'=>"Debe Ingresar el valor.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if (!is_numeric($valor)) {
                    $resp= ['mensaje'=>"Debe Ingresar solo numeros en el valor.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if ($valor < 1) {
                    $resp= ['mensaje'=>"Debe Ingresar solo numeros mayores a 0 en el valor.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else{
                    $traslado = $this->presupuesto_model->traer_traslado_id($id_traslado);
                    if ($traslado ->{"id_estado_traslado"} == 'Tras_Com') {
                        $data = array("valor_aprobado" => $valor);
                        $resp= ['mensaje'=>"El valor aprobado para el traslado fue modificado con éxito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        $mod = $this->presupuesto_model->modificar_datos($data,'presupuesto_traslados',$id_traslado);
                        if($mod != 0) $resp= ['mensaje'=>"Error al modificar el valor aprobado, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }else{
                        $resp= ['mensaje'=>"El traslado ya fue gestionado por tal motivo no es posible continuar.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }          
                }  
            }
        }
        echo json_encode($resp);
    }

    public function validar_aprobados_traslado_comite($id_traslado)
    {
        $traslado = $this->presupuesto_model->traer_traslado_id($id_traslado);
        if($traslado->{'id_estado_traslado'} == 'Tras_Com'){
            $num_aprobados = 3;
            $data = $this->genericas_model->obtener_valores_parametro_aux("Num_Apro_Tras", 20);
            if (!empty($data))$num_aprobados = $data[0]["valor"];
            $total = $this->presupuesto_model->contar_aprobado_negados($id_traslado,'Aprobado');
            return  $total >= $num_aprobados ? true :false;
        }
        return true;

    }

    public function guardar_comentario()
    {
      if ($this->Super_estado == false) {
          $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
      } else {
          if ($this->Super_agrega == 0) {
              $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
          } else {
                $id_comite= (int) $this->input->post("id_comite");
                $comentario = $this->input->post("comentario");
                $id = $this->input->post("id");
                $usuario_registra = $_SESSION['persona'];
                if(empty($id_comite)){
                    $resp= ['mensaje'=>"Error al cargar el ID del comité, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else if (ctype_space($comentario) || empty($comentario)) {
                  $resp= ['mensaje'=>"Ingrese Comentario.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else{
                  if (!isset($id) || empty($id)) $id = null;
                  $data = array("id_comite" => $id_comite,"comentario" => $comentario,"usuario_registra" =>$usuario_registra,"id_comentario" => $id,);
                  $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Comentario Guardado.!"];
                  $add = $this->presupuesto_model->guardar_datos($data,'comentarios_comite');
                  if($add != 0) $resp= ['mensaje'=>"Error al guardar el comentario, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_comentarios()
    {
        $datos = array();
        $id_comite= $this->input->post("id_comite");
        if ($this->Super_estado == true) {
            $datos = $this->presupuesto_model->listar_comentarios($id_comite);
        }
        echo json_encode($datos);
    }
    public function listar_respuestas_comentarios()
    {
        $datos = array();
        $id= $this->input->post("id");
        if ($this->Super_estado == true) {
            $datos = $this->presupuesto_model->listar_respuestas_comentarios($id);
        }
        echo json_encode($datos);
    }
    public function mostrar_notificaciones_comentarios_comite()
    {
        $datos = array();
        $tipo= $this->input->post("tipo");
        if ($this->Super_estado == true) {
            $datos = $this->presupuesto_model->mostrar_notificaciones_comentarios_comite($tipo);
        }
        echo json_encode($datos);
    }
    public function obtener_correos_comite()
    {
        $datos = array();
        if ($this->Super_estado == true) {
            $this->load->model('personas_model');
            $datos = $this->personas_model->Listar_personas_por_perfil('Per_Dir','comite_presupuesto');   
        }
        echo json_encode($datos);
    }
    public function obtener_traslados_comite()
    {
        $datos = array();
        $id_comite= $this->input->post("id_comite");
        if ($this->Super_estado == true) {
            $datos = $this->presupuesto_model->listar_traslados_por_comite($id_comite,0);   
        }
        echo json_encode($datos);
    }

    public function terminar_comentario()
    {
      if ($this->Super_estado == false) {
          $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
      } else {
          if ($this->Super_modifica == 0) {
              $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
          } else {
                $id = $this->input->post("id");
                $usuario_termina = $_SESSION['persona'];
                $fecha_termina = date("Y-m-d H:i:s"); 
                if(empty($id)){
                    $resp= ['mensaje'=>"Error al cargar el ID del comentario, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                  $data = array("estado_notificacion" => 0,"usuario_termina" => $usuario_termina,"fecha_termina" =>$fecha_termina);
                  $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Comentario Terminado.!"];
                  $add = $this->presupuesto_model->modificar_datos($data,'comentarios_comite',$id);
                  if($add != 0) $resp= ['mensaje'=>"Error al terminar el comentario, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }
    public function convertir_moneda($number,$format , $decimal = 2){

        if (!$format) {
            $number= str_replace(".", "", $number);
            $number= str_replace(",", ".", $number);
           return $number;
        }
        return number_format($number,$decimal ,",", ".");
    }

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $fecha_actual = date($format);
        $d = DateTime::createFromFormat($format, $date);
        if ($d->format($format) < $fecha_actual) return false;
        return $d && $d->format($format) == $date;
    }
}
