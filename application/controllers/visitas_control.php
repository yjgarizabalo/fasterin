<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class visitas_control extends CI_Controller {
//Variables encargadas de los permisos que tiene el usuario en session
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
 //Construtor del controlador, se importa el modelo personas_model y se inicia la session
	public function __construct()
	{
        parent::__construct();
        $this->load->model('visitas_model');
        $this->load->model('genericas_model');
        $this->load->model('personas_model');
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
 * Mustra la ventana de visitas
 * 
 * @return void
 */
	public function index()
	{
    $pages = "inicio";
    $data['js'] = "";
    $data['actividad'] = "Ingresar";
    if ($this->Super_estado) {
        $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], "visitas");
        if (!empty($datos_actividad)) {
            $pages = "visitas";
            $data['js'] = "visitas";
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

   /**
     * Muestra los daatos de los departamentos por empresa
     * @return Array
     */
  
  function listar_departamentos() { 

    $parametros = array();
    if ($this->Super_estado == false) {
        echo json_encode($parametros);
        return;
    }
    $datos = $this->genericas_model->Listar_valor(3,1,42);

    $i = 1;
    foreach ($datos as $row) { 
        $row["empresa"] = "UNIVERSIDAD DE LA COSTA";
        $row["valor"] = strtoupper($row["valor"]);
        $row["valory"] = $i;
        if ($row["idparametro"] == 42)$row["empresa"] = "CORPORACIÓN UNIVERSITARIA LATINOAMERICANA";
        $row["indice"] = '<span title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
        $parametros["data"][] = $row;
        $i++;
    }

    echo json_encode($parametros);
  }

      /**
     * Muestra los datos de una visitante, la consulta se realiza por el numero de identificacion
     * @return Array
     */

    public function buscar_visitante()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{

            $identificacion = $this->input->post("identificacion");
            $departamento = $this->input->post("id_parametro");
            $existe_codigo = '';

            if (empty($identificacion) || ctype_space($identificacion)||empty($departamento) || ctype_space($departamento)) {
                $resp = ['mensaje'=>"Ingrese numero de identificación y código del departamento para continuar.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (!is_numeric($identificacion)) {
                $resp = ['mensaje'=>"Ingrese solo datos numericos en el campo identificación.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $existe_codigo = $this->genericas_model->consultar_generica("id = '$departamento'");
                if (empty($existe_codigo)) {
                    $resp = ['mensaje'=>"El Codigo del departamento no se encuentra registrado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else{
                    $existe_visitante = $this->visitas_model->buscar_visitante($identificacion);
                    $aux_code =  $existe_codigo[0]['id_aux'];
                    $datos = $this->visitas_model->is_estudiante_is_empleado($identificacion);
                    $sw = true;
                    if ($aux_code == 'dep_est' || $aux_code == 'dep_emp') {
                        if (empty($datos)) {
                            $resp = ['mensaje'=>"La persona no se encuentra registrado como estudiante o empleado activo, por favor cambiar el tipo de ingreso.",'tipo'=>"info",'titulo'=> "Oops.!"];
                            $sw = false;
                        }
                    }
                    if($sw){
                        if(empty($existe_visitante)){
                            $resp = ['mensaje'=>"El visitante no se encuentra registrado.",'tipo'=>"si_registrar",'titulo'=> "Oops.!","datos" => $datos,"departamento" => $existe_codigo[0]];
                        }else{
                            $resp = ['mensaje'=>"El visitante fue encontrado.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!","departamento" => $existe_codigo[0],"datos" => $existe_visitante];
                        }
                    }
                }
            }
               
      
            echo json_encode($resp);
            return;
        }
      
    }

    /**
     * Muestra los datos de una visitante, la consulta se realiza por el numero de identificacion
     * @return Array
     */

    function guardar_visita_departamento()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }else{
          if ($this->Super_agrega == 0) {
            echo json_encode(-1302);
            return;
        } 
        $visitante = $this->input->post("visitante");
        $departamento = $this->input->post("departamento");
        $usuario_registra = $_SESSION["persona"];
        $data = array(
          "id_visitante"=>$visitante,
          "id_departamento"=>$departamento,
          "usuario_registra"=>$usuario_registra,
        );
        $res = $this->visitas_model->guardar_datos($data, "visitas_departamento");
        echo json_encode($res);
        return;
        }
    }
    /**
     * Guarda los visitantes en la aplicacion, la funcion valida por numero de identificacion que no exista otra persona registrada
     * @return Integer
     */

    public function guardar_datos_visitante()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
                $tipo =  $this->input->post('tipo');
                $identificacion =  $this->input->post('identificacion');
                $tipo_identificacion = (int) $this->input->post('tipo_identificacion');
                $departamento = $this->input->post("departamento");
                $nombre = $this->input->post('nombre');
                $apellido = $this->input->post('apellido');
                $segundo_nombre = $this->input->post('segundonombre');
                $segundo_apellido = $this->input->post('segundoapellido');
                $usuario_registra = $_SESSION["persona"];
                $correo = $this->input->post('correo') ;
                $celular =  $this->input->post('celular');
                $id_programa = $tipo == 'PerEst' ? (int) $this->input->post('id_programa'):null;
                
                $existe = $this->visitas_model->existe_visitante($identificacion);

                if (!empty($existe)) {
                    $resp= ['mensaje'=>"El No. de identificación ya se encuentra en el sistema.",'tipo'=>"info",'titulo'=> "Oops.!"];
                } else {
                    if (empty($tipo)) {
                        $resp= ['mensaje'=>"Debe seleccionar el tipo de visitante.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else if ( ctype_space($nombre) || ctype_space($apellido) ||ctype_space($tipo_identificacion) || empty($nombre) || empty($apellido)|| empty($tipo)) {
                        $resp= ['mensaje'=>"Debe ingresar al menos el primer nombre, el primer apellido y el numero de identificación.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    } else if(empty($id_programa) && $tipo == 'PerEst'){
                        $resp= ['mensaje'=>"Debe seleccionar el programa.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }/* else if(empty($celular) && $tipo == 'PerEst'){
                        $resp= ['mensaje'=>"Debe ingresar el numero de celular.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else if(empty($correo) && $tipo == 'PerEst'){
                        $resp= ['mensaje'=>"Debe ingresar el correo.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }*/ else if(empty($identificacion)){
                        $resp= ['mensaje'=>"Debe ingresar solo datos numéricos en el #identificación.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    } else {
                        $data = array(
                            "tipo"=>$tipo,
                            "identificacion"=>$identificacion,
                            "tipo_identificacion"=>$tipo_identificacion,
                            "nombre"=>$nombre,
                            "segundo_nombre"=>$segundo_nombre,
                            "apellido"=>$apellido,
                            "segundo_apellido"=>$segundo_apellido,
                            "usuario_registra"=>$usuario_registra,
                            "foto"=>isset($_POST['foto']) ? "$identificacion.png":'visitante.png',
                            "correo"=>!empty($correo) ? $correo : null,
                            "celular"=>!empty($celular) ? $celular : null,
                            "id_programa"=> $id_programa,
                          );
                          $add = $this->visitas_model->guardar_datos($data, "visitantes");
                          if ($add == 0) {
                            if (isset($_POST['foto'])) {
                                $datos = base64_decode( preg_replace('/^[^,]*,/', '', $_POST['foto']));
                                file_put_contents(APPPATH . '../archivos_adjuntos/visitas/fotos_visitantes/'.$identificacion.'.png', $datos);
                              }
                            $visitante = $this->visitas_model->traer_id_ultimo_visitante($usuario_registra);
                            $resp= ['mensaje'=>"El visitante fue registrado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!",'visitante'=>$visitante]; 
                            }else{
                                $resp= ['mensaje'=>"Error al registrar al visitante, contacte con el administrador del sistema.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                            }
                         
                    }
                }
            }
        }
        echo json_encode($resp);
        return;
    }

    public function modificar_datos_visitante(){
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
                $id =  $this->input->post('id_visitante');
                $tipo =  $this->input->post('tipo');
                $identificacion =  $this->input->post('identificacion');
                $tipo_identificacion = (int) $this->input->post('tipo_identificacion');
                $departamento = $this->input->post("departamento");
                $nombre = $this->input->post('nombre');
                $apellido = $this->input->post('apellido');
                $segundo_nombre = $this->input->post('segundonombre');
                $segundo_apellido = $this->input->post('segundoapellido');
                $usuario_registra = $_SESSION["persona"];
                $correo = $this->input->post('correo') ;
                $celular =  $this->input->post('celular');
                $id_programa = $tipo == 'PerEst' ? (int) $this->input->post('id_programa'):null;

                $visitante = $this->visitas_model->buscar_visitante_id($id);
                if(($tipo == $visitante->{'tipo'}) && ($identificacion == $visitante->{'identificacion'}) && ($tipo_identificacion == $visitante->{'tipo_identificacion'}) && ($nombre == $visitante->{'nombre'}) && ($apellido == $visitante->{'apellido'}) && ($segundo_nombre == $visitante->{'segundo_nombre'}) && ($segundo_apellido == $visitante->{'segundo_apellido'}) && ($correo == $visitante->{'correo'}) && ($celular == $visitante->{'celular'})){
                    $resp= ['mensaje'=>"Debe realizar alguna modificación en el visitante.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else{
                    if (empty($tipo)) {
                        $resp= ['mensaje'=>"Debe seleccionar el tipo de visitante.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else if ( ctype_space($nombre) || ctype_space($apellido) ||ctype_space($tipo_identificacion) || empty($nombre) || empty($apellido)|| empty($tipo)) {
                        $resp= ['mensaje'=>"Debe ingresar al menos el primer nombre, el primer apellido y el numero de identificación.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    } else if(empty($id_programa) && $tipo == 'PerEst'){
                        $resp= ['mensaje'=>"Debe seleccionar el programa.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else if(empty($identificacion)){
                        $resp= ['mensaje'=>"Debe ingresar solo datos numéricos en el #identificación.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    } else {
                        $existe = $this->visitas_model->buscar_visitante($identificacion);
                        if ((!empty($existe)) && ($existe->{'id'} != $id)) {
                            $resp= ['mensaje'=>"El No. de identificación ya se encuentra en el sistema.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }else{
                            $data = array(
                                "tipo"=>$tipo,
                                "identificacion"=>$identificacion,
                                "tipo_identificacion"=>$tipo_identificacion,
                                "nombre"=>$nombre,
                                "segundo_nombre"=>$segundo_nombre,
                                "apellido"=>$apellido,
                                "segundo_apellido"=>$segundo_apellido,
                                "usuario_registra"=>$usuario_registra,
                                "foto"=>isset($_POST['foto']) ? "$identificacion.png":'visitante.png',
                                "correo"=>!empty($correo) ? $correo : null,
                                "celular"=>!empty($celular) ? $celular : null,
                                "id_programa"=> $id_programa,
                            );
                            $mod = $this->visitas_model->modificar_datos($data, "visitantes", $id);
                            if ($mod == 0) {
                                if (isset($_POST['foto'])) {
                                    $datos = base64_decode( preg_replace('/^[^,]*,/', '', $_POST['foto']));
                                    file_put_contents(APPPATH . '../archivos_adjuntos/visitas/fotos_visitantes/'.$identificacion.'.png', $datos);
                                }
                                $resp= ['mensaje'=>"El visitante fue modificado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"]; 
                            }else{
                                $resp= ['mensaje'=>"Error al modificar al visitante, contacte con el administrador del sistema.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                            }
                        }
                    }
                    
                }
            }
        }
        echo json_encode($resp);
    }

   /**
     * Muestra el historial de ingresos por departamento o por el numero de identificacion
     * @return Array
     */
  
    function listar_ingresos_departamentos() 
    { 
        $ingresos = array();
        if ($this->Super_estado == false) {
            echo json_encode($ingresos);
            return;
        }
        $dato = $this->input->post('dato');
        $tipo = $this->input->post('tipo');

        $fechas = null;
        $inicial = date("Y-m-d");
        $formato = 0;
        $final = null;
        if ($tipo != 1) {
            $formato = $this->input->post('formato');
            $fechas = $this->input->post('entre_fechas');
            $inicial = $this->input->post('fecha_inicial');
            if ($fechas == 1) {
                $final = $this->input->post('fecha_final');
            }
        }
      


        $datos = $this->visitas_model->listar_ingresos_departamentos($dato, $tipo, $inicial, $final,$formato);
        $i = 1;
        foreach ($datos as $row) {
            $row["indice"] = '<span title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
            $ingresos["data"][] = $row;
            $i++;
        }
    
        echo json_encode($ingresos);
      }

      
    /**
     * Marca la  ultima salida de una persona
     * @return Array
     */

    function traer_ultimo_ingreso_visitante()
    {     
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $identificacion = $this->input->post("identificacion");
            if (empty($identificacion) || ctype_space($identificacion)) {
                $resp = ['mensaje'=>"Ingrese numero de identificación.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (!is_numeric($identificacion)) {
                $resp = ['mensaje'=>"Ingrese solo datos numericos en el campo identificación.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{ 
                $datos_ingreso = $this->visitas_model->traer_ultimo_ingreso_visitante($identificacion);
                if (empty($datos_ingreso)) {
                    $resp = ['mensaje'=>"No se encontro ningun ingreso del visitante para el dia de hoy.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if (!is_null($datos_ingreso ->{"hora_salida"})) {
                    $resp = ['mensaje'=>"No se encontro ningun ingreso activo del visitante.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else{
                    $resp = ['mensaje'=>"ultimo ingreso encontrado.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!",'datos' =>$datos_ingreso];
                }
            }
        }
        echo json_encode ($resp);
        return;
    }
    function marcar_salida_visitante()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }else{
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {                
                $id = $this->input->post("id");
                $usuario_registra = $_SESSION["persona"];
                $fecha_actual = date("Y-m-d H:i:s");
                $data = array(
                "hora_salida"=>$fecha_actual,
                "usuario_marca_salida"=>$usuario_registra,
                );

                $res = $this->visitas_model->modificar_datos($data, "visitas_departamento",$id);
                echo json_encode($res);
                return;
        }
    }

    }

        /**
     * Funcion encargada de registrar los eventos.
     * @return Array
     */

    function guardar_evento()
    {

        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else if ($this->Super_agrega == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{
           
            $nombre = $this->input->post("nombre");
            $fecha_inicio = $this->input->post("fecha_inicio");
            $fecha_fin = $this->input->post("fecha_fin");
            $con_cupos = $this->input->post("con_cupos");
            $cupos = $this->input->post("cupos");
            $pre_inscripcion = $this->input->post("pre_inscripcion");
            $ubicacion = $this->input->post("ubicacion");
            $descripcion = $this->input->post("descripcion");
            $tipo = $this->input->post("tipo");
            $firma =  $pre_inscripcion == 2 ? $this->input->post("firma") : null;
            $usuario_registro = $_SESSION["persona"];
           
            
            if (ctype_space($nombre) || empty($nombre)) {
                $resp = ['mensaje'=>"Ingrese nombre.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (ctype_space($fecha_inicio) || empty($fecha_inicio)) {
                $resp = ['mensaje'=>"Ingrese fecha de inicio.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (ctype_space($fecha_fin) || empty($fecha_fin)) {
                $resp = ['mensaje'=>"Ingrese fecha final.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($this->validateDate($fecha_inicio,'Y-m-d H:i') == 'false') {
                $resp = ['mensaje'=>"Ingrese una fecha de inicio valida.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($this->validateDate($fecha_fin,'Y-m-d H:i') == 'false') {
                $resp = ['mensaje'=>"Ingrese una fecha de fin valida.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $es_correcta = $this->Validar_fechas($fecha_inicio,$fecha_fin);
                if ($es_correcta != 1) {
                    $resp = ['mensaje'=>$es_correcta ,'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if ((ctype_space($cupos) || empty($cupos)) && $con_cupos == 1) {
                    $resp = ['mensaje'=>"Ingrese #Cupos disponibles.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if (!is_numeric($cupos)  && $con_cupos == 1) {
                    $resp = ['mensaje'=>"El #cupos debe ser un numero.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if ($cupos < 1  && $con_cupos == 1) {
                    $resp = ['mensaje'=>"El #cupos debe ser mayor a 0.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if (ctype_space($pre_inscripcion) || empty($pre_inscripcion)) {
                    $resp = ['mensaje'=>"Seleccione tipo de ingreso.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if (ctype_space($ubicacion) || empty($ubicacion)) {
                    $resp = ['mensaje'=>'Ingrese ubicación' ,'tipo'=>"info",'titulo'=> "Oops.!"];
                }else{
                    $data = array(
                        "nombre"=>$nombre,
                        "fecha_inicio"=>$fecha_inicio,
                        "fecha_fin"=>$fecha_fin,
                        "cupos"=>$con_cupos == 1 ? $cupos : null,
                        "firma"=>$firma == 1 ? $firma : null,
                        "pre_inscripcion"=>$pre_inscripcion,
                        "ubicacion"=>$ubicacion,
                        "descripcion"=>$descripcion,
                        "usuario_registro"=>$usuario_registro,
                        "tipo"=>$tipo,
                    );
                    $add = $this->visitas_model->guardar_datos($data, "eventos");
                    if($add != 0){ 
                        $resp = ['mensaje'=>'Error al registrar los datos, contacte con el administrador del sistema.' ,'tipo'=>"error",'titulo'=> "Oops.!"];
                    }else{
                        $evento = $this->visitas_model->traer_id_ultimo_evento($usuario_registro);
                        $resp= ['mensaje'=> "Los datos fueron registrados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!",'evento' =>$evento];
                    }
                    }
            }
                
     
        }
        echo json_encode($resp);
        return ;
    }

    public function Validar_fechas($strStart, $strEnd)
    {
        $fecha_actual = date("Y-m-d H:i");
        $fecha_inicio = date_create($strStart);
        $fecha_fin = date_create($strEnd);
        $forma = date_format($fecha_inicio, 'Y-m-d H:i');
        if ($forma <= $fecha_actual) {
            return 'La fecha de inicio no puede ser menor que la fecha actual';
        }else if ($fecha_fin <= $fecha_inicio) {
            return 'La fecha final no puede ser menor que la fecha de inicio';
        }
        return 1;
    }

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return json_encode($d && $d->format($format) == $date);
    }
       /**
     * Trae  los eventos dependiento del usuario que este en sesion
     * @return Array
     */
  
  function listar_eventos() { 

    $eventos = array();
    if ($this->Super_estado == false) {
        echo json_encode($eventos);
        return;
    }

    $fecha_inicio = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");
    $datos = $this->visitas_model->listar_eventos($fecha_inicio, $fecha_fin);
    $estados_eventos = $this->genericas_model->obtener_valores_parametro(43);
    foreach ($datos as $row) {
        $estado =$this->obtener_estado($row['fecha_inicio'], $row['fecha_fin'], $row['usuario_elimina']);
        $bgcolor = 'white';
        $row["gestion"] = '<span  title="Evento Cerrado" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';

        if($row['pre_inscripcion'] == 1) $row['ingreso'] = 'Entrada Libre';
        else if($row['pre_inscripcion'] == 3) $row['ingreso'] = 'Entrada Libre - Multiple' ;
        else $row['ingreso'] = 'Pre-inscripción';
        $btn_codigo = ' <span title="Generar Codigo" data-toggle="popover" data-trigger="hover" style="color: #39B23B;margin-left: 5px"class="fa fa-external-link-square btn btn-default" onclick="generar_codigo_evento('.$row["id"].',`'.$row["codigo"].'`)"></span> ';
        if($estado == 'Eve_Reg'){
            $bgcolor ='#EABD32';
            $row["gestion"] = '<span title="Agregar Participante" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px"class="fa fa-user-plus btn btn-default" onclick="abrir_modal_participantes(`evento`)"></span> ';
            if ($row['usuario_registro'] == $_SESSION['persona'] || $_SESSION['perfil'] == 'Per_Admin')  $row["gestion"] .= '<span title="Cancelar Evento" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="fa fa-close btn btn-default" onclick="confirmar_cambiar_estado_evento('.$row["id"].')"></span>'; ;
            if (is_null($row['codigo'])) $row["gestion"] .= $btn_codigo;
        }else if($estado == 'Eve_Cur'){
            $bgcolor ='#2E79E5';
            $row["gestion"] = '<span title="Agregar Participante" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px"class="fa fa-user-plus btn btn-default" onclick="abrir_modal_participantes(`evento`)"></span>';
            if (is_null($row['codigo'])) $row["gestion"] .= $btn_codigo;
        }else if($estado== 'Eve_Ter'){
            $bgcolor ='#39B23B';
        }else{
            $bgcolor ='#d9534f';
        }
        $row['estado'] = $this->buscar_estados_evento_nombre($estados_eventos,$estado);
        $row["indice"] = '<span title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: '.$bgcolor.';color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
       
        $eventos["data"][] = $row;
    
    }
    echo json_encode($eventos);
    return;
    }
    
    function obtener_estado($fecha_inicio , $fecha_fin, $eliminado = null){

        if (!is_null($eliminado)) {
            return 'Eve_Can';
        }
        $fecha_actual = date("Y-m-d H:i");
        $fecha_inicio = date_format(date_create($fecha_inicio), 'Y-m-d H:i');
        $fecha_fin =date_format(date_create($fecha_fin), 'Y-m-d H:i');

        if ($fecha_actual < $fecha_inicio) {
            return 'Eve_Reg';
        }else if ($fecha_actual >= $fecha_inicio && $fecha_actual < $fecha_fin) {
            return 'Eve_Cur';
        }else{
            return 'Eve_Ter';
        }
    }
    function cambiar_estado_evento()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else if ($this->Super_modifica == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        } else {         
            $resp= ['mensaje'=>"El estado fue modificado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];       
            $id = $this->input->post("id");
            $usuario_registra = $_SESSION["persona"];
            $fecha_actual = date("Y-m-d H:i:s");
            $evento = $this->visitas_model->buscar_evento("id = '$id'")[0];
            if ($evento['usuario_registro'] != $usuario_registra && $_SESSION['perfil']!='Per_Admin') {
                $resp= ['mensaje'=>"No esta autorizado para realizar esta acción.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($evento['usuario_elimina'] != null) {
                $resp= ['mensaje'=>"El evento ya fue cancelado anteriormente.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $data = array(
                    "fecha_elimina"=>$fecha_actual,
                    "usuario_elimina"=>$usuario_registra,
                    );
                    $add =  $this->visitas_model->modificar_datos($data, "eventos",$id);
                    if($add != 0) $resp = ['mensaje'=>'Error al modificar el estado del evento, contacte con el administrador del sistema.' ,'tipo'=>"error",'titulo'=> "Oops.!"];
            }
            echo json_encode($resp);
            return ;
        }
    }

    

            /**
     * Funcion encargada de modifcar un evento.
     * @return Array
     */

    function modificar_evento()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else if ($this->Super_modifica == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{
            $resp= ['mensaje'=>"Datos modificados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
            $id = $this->input->post("id");
            $nombre = $this->input->post("nombre");
            $fecha_inicio = $this->input->post("fecha_inicio");
            $fecha_fin = $this->input->post("fecha_fin");
            $con_cupos = $this->input->post("con_cupos");
            $cupos = $this->input->post("cupos");
            $pre_inscripcion = $this->input->post("pre_inscripcion");
            $ubicacion = $this->input->post("ubicacion");
            $descripcion = $this->input->post("descripcion");
            $firma =  $pre_inscripcion == 2 ? $this->input->post("firma") : null;
            $usuario_registro = $_SESSION["persona"];

            $evento = $this->visitas_model->buscar_evento("id = '$id'")[0];
            $estado =  $this->obtener_estado($evento['fecha_inicio'], $evento['fecha_fin'],$evento['usuario_elimina']);
            if ($evento['usuario_registro'] != $usuario_registro && $_SESSION['perfil']!='Per_Admin') {
                $resp= ['mensaje'=>"No esta autorizado para realizar esta acción.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($estado != "Eve_Reg") {
                $resp= ['mensaje'=>"El evento o la visita se encuentra en curso o terminado, por tal motivo no es posible continuar.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (ctype_space($nombre) || empty($nombre)) {
                $resp = ['mensaje'=>"Ingrese nombre.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (ctype_space($fecha_inicio) || empty($fecha_inicio)) {
                $resp = ['mensaje'=>"Ingrese fecha de inicio.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (ctype_space($fecha_fin) || empty($fecha_fin)) {
                $resp = ['mensaje'=>"Ingrese fecha final.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($this->validateDate($fecha_inicio,'Y-m-d H:i') == 'false') {
                $resp = ['mensaje'=>"Ingrese una fecha de inicio valida.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($this->validateDate($fecha_fin,'Y-m-d H:i') == 'false') {
                $resp = ['mensaje'=>"Ingrese una fecha de fin valida.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else
                $es_correcta = $this->Validar_fechas($fecha_inicio,$fecha_fin);
            if ($es_correcta != 1) {
                $resp = ['mensaje'=>$es_correcta ,'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ((ctype_space($cupos) || empty($cupos)) && $con_cupos == 1) {
                $resp = ['mensaje'=>"Ingrese #Cupos disponibles.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (!is_numeric($cupos)  && $con_cupos == 1) {
                $resp = ['mensaje'=>"El #cupos debe ser un numero.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($cupos < 1  && $con_cupos == 1) {
                $resp = ['mensaje'=>"El #cupos debe ser mayor a 0.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (ctype_space($pre_inscripcion) || empty($pre_inscripcion)) {
                $resp = ['mensaje'=>"Seleccione tipo de ingreso.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (ctype_space($ubicacion) || empty($ubicacion)) {
                $resp = ['mensaje'=>'Ingrese ubicación' ,'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $data = array(
                    "nombre"=>$nombre,
                    "fecha_inicio"=>$fecha_inicio,
                    "fecha_fin"=>$fecha_fin,
                    "cupos"=>$con_cupos == 1 ? $cupos : null,
                    "firma"=>$firma == 1 ? $firma : null,
                    "pre_inscripcion"=>$pre_inscripcion,
                    "ubicacion"=>$ubicacion,
                    "descripcion"=>$descripcion,
                  );
                $add =  $this->visitas_model->modificar_datos($data, "eventos",$id);
                if($add != 0) $resp = ['mensaje'=>'Error al modificar los datos, contacte con el administrador del sistema.' ,'tipo'=>"error",'titulo'=> "Oops.!"];
            }
        }
        echo json_encode($resp);
        return ;
    }
   
    function buscar_evento()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }else{

            $id = $this->input->post("id");
            $res = $this->visitas_model->buscar_evento( "id = '$id'")[0];
            $res['estado'] = $this->obtener_estado($res['fecha_inicio'], $res['fecha_fin'],$res['usuario_elimina']);
            $res['modifica'] = $res['usuario_registro'] != $_SESSION["persona"] && $_SESSION['perfil']!='Per_Admin' ? 0 : 1;
            $res["fecha_inicio"] = date_format(date_create($res["fecha_inicio"]), 'Y-m-d H:i');
            $res["fecha_fin"] = date_format(date_create($res["fecha_fin"]), 'Y-m-d H:i');
            echo json_encode($res);
            return;
        }
    }
    function buscar_estados_evento_nombre($estados_eventos,$estado)
    {
        foreach ($estados_eventos as $es) {
            if ($es["id_aux"] == $estado) {
                return $es["valor"];
            }
        }
        return "Sin estado";
    }

    /**
     * Muestra los visitantes por nombre o numero de identificacion
        * @return Array
     */
  
    function listar_participantes() 
    { 
        $dato = $this->input->post('dato');
        $participantes = array();
        if ($this->Super_estado == false || empty($dato)) {
            echo json_encode($participantes);
            return;
        }
        
        $datos = $this->visitas_model->listar_participantes($dato);
        foreach ($datos as $row) {
            $row["gestion"] = '<span style="color: #39B23B;" title="Seleccionar Participante" data-toggle="popover" data-trigger="hover" class="pointer btn btn-default fa fa-check-square-o"></span>';
            $participantes["data"][] = $row;
        }
        echo json_encode($participantes);
      }

                  /**
     * Funcion encargada de guadar los participantes a los eventos.
     * @return Array
     */

    public function guardar_participante_evento()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else if ($this->Super_agrega == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{
            $resp= ['mensaje'=>"El participante fue asignado con exito al evento.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
            $id_evento = $this->input->post("id_evento");
            $id_persona = $this->input->post("id_persona");
            $con_vehiculo = $this->input->post("con_vehiculo");
            $placa_vehiculo = $this->input->post("placa_vehiculo");
            $acom_vehiculo = $this->input->post("acom_vehiculo");
            $observaciones = $this->input->post("observaciones");
            $id_hijo = $this->input->post("id_hijo");
            $id_tipo = $this->input->post("id_tipo");
            $usuario_registro = $_SESSION["persona"];
           

            $evento = $this->visitas_model->buscar_evento("id = '$id_evento'")[0];
            $estado =  $this->obtener_estado($evento['fecha_inicio'], $evento['fecha_fin'],$evento['usuario_elimina']);
           
            if ($evento['pre_inscripcion'] == 2 && $evento['usuario_registro'] != $usuario_registro && $_SESSION['perfil']!='Per_Admin') {
                $resp= ['mensaje'=>"No esta autorizado para realizar esta acción, ya que requiere una Pre-inscripción por parte del responsable del evento.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ( $evento['usuario_registro'] != $usuario_registro && $_SESSION['perfil']!='Per_Admin' && $_SESSION['perfil']!='Per_Admin_vis') {
                $resp= ['mensaje'=>"No esta autorizado para realizar esta acción.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($estado != "Eve_Reg" && $estado != "Eve_Cur") {
                $resp= ['mensaje'=>"El evento o la visita se encuentra en curso o terminado, por tal motivo no es posible continuar.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($con_vehiculo == 1 && empty($placa_vehiculo)) {
                $resp= ['mensaje'=>"Ingrese Numero de placa del vehículo.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($con_vehiculo == 1 && (!is_numeric($acom_vehiculo) || $acom_vehiculo < 0)) {
                $resp= ['mensaje'=>"Ingrese solo datos numericos mayor o igual a 0 en el #Acompañantes.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                if ($con_vehiculo != 1) {
                    $placa_vehiculo = null;
                    $acom_vehiculo = null;
                }
                $en_evento = $this->visitas_model->verificar_ingreso_evento($id_evento,$id_persona);
                if ($evento['pre_inscripcion'] != 3 && !empty($en_evento)){
                    $resp= ['mensaje'=>"El participante ya se encuentra en el evento.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if (empty($id_tipo)) {
                    $resp= ['mensaje'=>"Seleccione Tipo participante.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if (!is_numeric($id_evento) || !is_numeric($id_persona)) {
                    $resp = ['mensaje'=>"Error al cargar la informacion el evento, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $inicio_evento = date_format( date_create($evento['fecha_inicio']), 'Y-m-d');
                    $inicio_ingreso_evento = date("Y-m-d"); 
                    $fecha_entrada_evento =   $inicio_ingreso_evento >= $inicio_evento && ($evento['tipo'] !='visita' && $evento['pre_inscripcion'] != 2) ?  date("Y-m-d H:i:s") :null;
                    
                    $data = array(
                        "id_tipo"=>$id_tipo,
                        "id_evento"=>$id_evento,
                        "id_persona"=>$id_persona,
                        "fecha_entrada_evento"=>$fecha_entrada_evento,
                        "usuario_marca_entrada"=>is_null($fecha_entrada_evento) ? null: $usuario_registro,
                        "placa_vehiculo"=>$placa_vehiculo,
                        "acom_vehiculo"=>$acom_vehiculo,
                        "observaciones "=>$observaciones,
                        "usuario_registra"=>$usuario_registro,
                        "id_hijo"=>empty($id_hijo) ? null : $id_hijo,
                        );
                    $add =  $this->visitas_model->guardar_datos($data, "participantes_evento");
                    if($add != 0) $resp = ['mensaje'=>'Error al guardar el participante, contacte con el administrador del sistema.' ,'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            } 
        }
        echo json_encode($resp);
        return ;
    }

    /**
     * Muestra los participantes registrado a un evento
     * @return Array
     */
  
    public function listar_participantes_en_evento() 
    { 
        $participantes = array();
        if ($this->Super_estado == false) {
            echo json_encode($participantes);
            return;
        }
        $id_evento = $this->input->post('id_evento');
        $buscar = $this->input->post('buscar');
        $datos = $this->visitas_model->listar_participantes_en_evento($id_evento, $buscar);
        
        foreach ($datos as $row) {
            $row["gestion"] ='';
            $estado =$this->obtener_estado($row['fecha_inicio_evento'], $row['fecha_fin_evento'], $row['usuario_elimina_evento']);
            $cerrado= '<span  title="Gestion Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
            $elimina = '<span onclick="confirmar_marcar_participantes_evento('.$row["id"].','.$id_evento.',3)" style="color:#d9534f" title="Cancelar Ingreso" data-toggle="popover" data-trigger="hover" class="pointer btn btn-default fa fa-remove"></span>';
            $entrada = '<span onclick="confirmar_marcar_participantes_evento('.$row["id"].','.$id_evento.',1,'.$row["firma"].')" style="color:#2E79E5" title="Marcar Ingreso" data-toggle="popover" data-trigger="hover" class="pointer btn btn-default fa fa-sign-in"></span>';
            $salida = '<span onclick="confirmar_marcar_participantes_evento('.$row["id"].','.$id_evento.',2,'.$row["firma"].')" style="color:#5cb85c" title="Marcar Salida" data-toggle="popover" data-trigger="hover" class="pointer btn btn-default fa fa-sign-out"></span>';
            
            $inicio_evento = date_format( date_create($row['fecha_inicio_evento']), 'Y-m-d');
            $fin_evento = date_format( date_create($row['fecha_fin_evento']), 'Y-m-d');
            $hoy= date("Y-m-d"); 
            $puede_entrar =   $hoy >= $inicio_evento &&  $hoy <= $fin_evento ?  true : false;
            
            if(($estado == 'Eve_Reg' || $estado == 'Eve_Cur') && !empty($id_evento)){
                if (is_null($row['fecha_entrada_evento']) && $puede_entrar) {
                    $row["gestion"] .= $entrada.' '.$elimina;
                }else if (is_null($row['fecha_salida_evento']) && !is_null($row['fecha_entrada_evento']) ) {
                    $row["gestion"] .= $salida;
                }else{
                    if (is_null($row['fecha_entrada_evento']) &&  is_null($row['fecha_salida_evento'])) {
                        $row["gestion"] .= $elimina;
                    }else{
                        $row["gestion"] =$cerrado;
                    }
                }
            }else{
                $row["gestion"] =$cerrado;
            }
           
            $row["indice"] = '<span title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
            $participantes["data"][] = $row;
        }
    
        echo json_encode($participantes);
      }

     public function marcar_participantes_evento()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else if ($this->Super_modifica == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        } else {    
        $id = $this->input->post("id");    
        $id_evento = $this->input->post("id_evento");    
        $tipo = $this->input->post("tipo"); 
        $fecha_actual = date("Y-m-d H:i:s");
        $usuario_marca= $_SESSION["persona"];
            if (!is_numeric($id) || !is_numeric($id_evento)) {
            $resp = ['mensaje'=>"Error al gestionar al participante, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{
            $sw = true;
            $data = array();
            $hoy= date("Y-m-d"); 
            $en_evento = $this->visitas_model->buscar_ingreso_evento_id($id);
            if($tipo==1){ 
                $firma = $en_evento->{'firma'} == 1 ? $this->adjuntar_firma("firma") : null;
                if($firma == -2 ){
                    $sw = false;
                    $mensaje = "El evento requiere firma.";
                }else if($firma == -1){
                    $sw = false;
                    $mensaje = "Error al cargar la firma.";
                }else{
                    $data = array("fecha_entrada_evento"=>$fecha_actual,"usuario_marca_entrada"=>$usuario_marca,"con_firma" => $firma);
                    $inicio_evento = date_format(date_create($en_evento->{'fecha_inicio_evento'}), 'Y-m-d');
                    $fin_evento = date_format( date_create($en_evento->{'fecha_fin_evento'}), 'Y-m-d');

                    if(($hoy >= $inicio_evento &&  $hoy <= $fin_evento) && is_null($en_evento->{'fecha_entrada_evento'})){ 
                        $sw = true; 
                        $mensaje = "La entrada fue marcada con exito.";
                    }else{
                        $sw = false;
                        $mensaje = "La entrada solo puede ser marcada el dia del evento o ya fue marcada anteriormente.";
                    }
                }
            }else if($tipo==2){ 
                $data = array("fecha_salida_evento"=>$fecha_actual,"usuario_marca_salida"=>$usuario_marca);
                if(!is_null($en_evento->{'fecha_entrada_evento'}) && is_null($en_evento->{'fecha_salida_evento'})){ 
                    $sw = true; 
                    $mensaje = "La salida fue marcada con exito.";
                }else{
                    $sw = false;
                    $mensaje = "La salida ya fue marcada anteriormente o no hay una entrada registrada.";
                }
            }else if($tipo==3){ 
                $data = array("fecha_elimina"=>$fecha_actual,"usuario_elimina"=>$usuario_marca,"estado"=>0,);
                if(is_null($en_evento->{'usuario_elimina'})){ 
                    $sw = true; 
                    $mensaje = "El ingreso al participante fue cancelado con exito.";
                    }else{
                        $sw = false;
                        $mensaje = "El ingreso del participante fue cancelado anteriormente.";
                }
            }else{  
                $sw = false;
                $mensaje = "Opción invalidada, contacte con el administrador.";
            } 
            
            if ($sw) {
                $evento = $this->visitas_model->buscar_evento("id = '$id_evento'")[0];
                $estado =  $this->obtener_estado($evento['fecha_inicio'], $evento['fecha_fin'],$evento['usuario_elimina']);
                if ( ($evento['usuario_registro'] != $usuario_marca && $_SESSION['perfil']!='Per_Admin' && $_SESSION['perfil'] != 'Per_Admin_vis' )) {
                    $resp= ['mensaje'=>"No esta autorizado para realizar esta acción.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if ($estado != "Eve_Reg" && $estado != "Eve_Cur") {
                    $resp= ['mensaje'=>"El evento o la visita se encuentra en curso o terminado, por tal motivo no es posible continuar.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else{
                    $resp= ['mensaje'=>$mensaje,'tipo'=>"success",'titulo'=> "Proceso Exitoso.!", "evento" => $en_evento];
                    $add =  $this->visitas_model->modificar_datos($data, "participantes_evento",$id);
                }
            }else{
                $resp= ['mensaje'=>$mensaje,'tipo'=>"info",'titulo'=> "Oops.!"];
            }      
            
        }
        echo json_encode($resp);
        return ;
        }
    }

    public function listar_visitantes()
    {
        $dato = $this->input->post("dato");
        $visitantes = $this->Super_estado == true ? $this->visitas_model->listar_visitantes($dato) : array();
        echo json_encode($visitantes);
    }

    public function sancionar_visitante()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else if ($this->Super_agrega == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{
            $id = $this->input->post("id");
            $motivo = $this->input->post("motivo");
            $id_usuario_registra = $_SESSION["persona"];
            if (empty($id)) {
                    $resp= ['mensaje'=>"Error al cargar la información del visitante.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else  if (empty($motivo)) {
                $resp= ['mensaje'=>"Ingrese el motivo.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $data = array("id_visitante"=>$id,"motivo"=>$motivo,"id_usuario_registra"=>$id_usuario_registra,);
                $resp= ['mensaje'=>"La sanción fue asignada con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                $add =  $this->visitas_model->guardar_datos($data, "sanciones_visitante");
                if($add != 0) $resp = ['mensaje'=>'Error al asignar la sanción, contacte con el administrador del sistema.' ,'tipo'=>"error",'titulo'=> "Oops.!"];
            } 
        }
        echo json_encode($resp);
    }

    public function obtener_ingresos_visitante()
    {
        $id = $this->input->post("id");
        $ingresos = $this->Super_estado == true ? $this->visitas_model->obtener_ingresos_visitante($id) : array();
        echo json_encode($ingresos);
    }
    public function obtener_sanciones_visitante()
    {
        $id = $this->input->post("id");
        $sanciones = $this->Super_estado == true ? $this->visitas_model->obtener_sanciones_visitante($id) : array();
        echo json_encode($sanciones);
    }

    public function eliminar_sancion_visitante()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else if ($this->Super_elimina == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{
            $id = $this->input->post("id");
            $id_usuario_registra = $_SESSION["persona"];
            $fecha_elimina = date("Y-m-d H:i:s");
            if (empty($id)) {
                    $resp= ['mensaje'=>"Error al cargar la información del visitante.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $data = array("estado"=>0,"fecha_elimina"=>$fecha_elimina,"id_usuario_elimina"=>$id_usuario_registra,);
                $resp= ['mensaje'=>"La sanción fue eliminada con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                $add =  $this->visitas_model->modificar_datos($data, "sanciones_visitante", $id);
                if($add != 0) $resp = ['mensaje'=>'Error al eliminar la sanción, contacte con el administrador del sistema.' ,'tipo'=>"error",'titulo'=> "Oops.!"];
            } 
        }
        echo json_encode($resp);
    }

    
    public function remplazar_foto()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
                $resp= ['mensaje'=>"La foto fue cargada con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                $identificacion = $this->input->post('identificacion');
                if (isset($_POST['foto'])) {
                    $datos = base64_decode( preg_replace('/^[^,]*,/', '', $_POST['foto']));
                    file_put_contents(APPPATH . '../archivos_adjuntos/visitas/fotos_visitantes/'.$identificacion.'.png', $datos);
                }
            }
        }
        echo json_encode($resp);
    }

    public function obtener_data_tipo_participante() { 
        $id = $this->input->post('id');
        $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
        $resp["data"] = $this->Super_estado ? $this->genericas_model->obtener_valor_parametro_id_2($id) : array() ;
        echo json_encode($resp);
      }
    public function obtener_hijos() { 
        $id = $this->input->post('id');
        $resp= $this->Super_estado ? $this->visitas_model->obtener_hijos($id) : array() ;
        echo json_encode($resp);
      }

    public function asignar_hijo()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
                $id_hijo = $this->input->post('id_hijo');
                $id_padre = $this->input->post('id_padre');
                $tipo = $this->input->post('tipo');
                if (empty($id_hijo) || (empty($id_padre) && $tipo == 'asignado')) {
                    $resp= ['mensaje'=>"Error al cargar la información del visitante.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $resp= ['mensaje'=>"La hijo fue $tipo con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    $id_padre = $tipo == 'asignado' ? $id_padre : null;
                    $data = array("id_padre"=>$id_padre);
                    $add =  $this->visitas_model->modificar_datos($data, "visitantes", $id_hijo);
                    if($add != 0) $resp = ['mensaje'=>'Error al realizar la acción, contacte con el administrador del sistema.' ,'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function exportar_excel($id)
    {
        $resp = $this->visitas_model->listar_participantes_exporte($id);
        $datos["datos"] = $resp;
        $datos["nombre"] = "participantes_eventos";
        $datos["titulo"] = "ASISTENCIA DE EVENTOS";
        $datos["version"] = "VERSION: 07";
        $datos["trd"] = "TRD: 500-520-90";
        $datos["leyenda"] = "Al diligenciar este formato usted otorga su autorización a UNIVERSIDAD DE LA COSTA - CUC para que utilice sus datos informales, con la única finalidad de prestarles a los usuarios una mejor atención contacto e información sobre nuestros productos, servicios, ofertas y promociones para mantener canales de comunicación, así como noticias relacionadas con el desarrollo de las actividades académicas (fotografías y videos). Si desea revocar esta autorización, envíe un correo electrónico a la dirección buzon@cuc.edu.co, o contáctenos en la página web www.cuc.edu.co, o a la Dirección Cll. 58 #55-66 - Barranquilla, Colombia. No cedemos datos personales a terceros sin su debida autorización, cumplimos con el principio de circulación restringida, necesidad y finalidad de la Ley 1581 de 2012 y sus decretos reglamentarios.";
        $datos["fecha"] = "JUNIO DEL 2019";
        $i = 0;
        foreach($resp as $row) {
            foreach($row as $key => $val) $i++;
            break;
          }
        $datos["col"] = $i;
        $this->load->view('templates/exportar_excel', $datos);
    }

    public function generar_codigo_evento()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
                $id = $this->input->post('id');
                $id_padre = $this->input->post('id_padre');
                $tipo = $this->input->post('tipo');
                if (empty($id)) {
                    $resp = ['mensaje'=>'Error al cargar la información, contacte con el administrador del sistema.' ,'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $sw = false;
                    $codigo = ''; 
                    while (!$sw) {
                        $codigo = $this->traer_codigo_evento();
                        $evento = $this->visitas_model->buscar_evento("codigo = '$codigo' AND  DATE_FORMAT(fecha_vigente_codigo,'%Y-%m-%d %H:%i:%s') > DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m-%d %H:%i:%s') ");
                        if (empty($evento))  $sw = true;
                    }
                    $vence = strtotime ( '+7 days' , strtotime(date("Y-m-d H:i:s"))) ;
                    $vence = date ('Y-m-d H:i:s' , $vence); 
                    $data = array("codigo"=>$codigo, "fecha_vigente_codigo" => $vence);
                    $resp= ['mensaje'=>"Codigo Generado con exito, este codigo vence $vence.",'tipo'=>"success",'titulo'=> "codigo:  $codigo"];
                    $add =  $this->visitas_model->modificar_datos($data, "eventos", $id);
                    if($add != 0) $resp = ['mensaje'=>'Error al realizar la acción, contacte con el administrador del sistema o intente de nuevo.' ,'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

	public function auto_ingreso() {
        $data['actividad'] = "Eventos";
        $data['js'] = "visitas";
        $this->load->view('templates/header', $data);
        $this->load->view("pages/eventos_auto_ingreso");
        $this->load->view('templates/footer');
  }

  public function auto_guardar_participante_evento()
  {

    $codigo = $this->input->post("codigo");
    $identificacion = $this->input->post("identificacion");
    $observaciones = "Ingreso por codigo";

    if (empty($codigo) || empty($identificacion)) {
        $resp= ['mensaje'=>"Ingrese código y numero de identificación.",'tipo'=>"info",'titulo'=> "Oops.!"];
    }else{
        $resp= ['mensaje'=>"El participante fue asignado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
        $usuario_registro = $this->personas_model->obtener_Datos_persona_identificacion("11","1");
        $evento = $this->visitas_model->buscar_evento("codigo = '$codigo' AND  DATE_FORMAT(fecha_vigente_codigo,'%Y-%m-%d %H:%i:%s') > DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m-%d %H:%i:%s')");

        $existe_visitante = $this->visitas_model->buscar_visitante($identificacion);
        $tipo_normal = $this->genericas_model->consultar_generica("id_aux = 'Tip_Nor'");

        if(empty($usuario_registro)){
            $resp = ['mensaje'=>"Error al cargar el usuario que registra, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else if (empty($tipo_normal)) {
            $resp = ['mensaje'=>"Error al cargar el tipo de participante, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else if (empty($evento)) {
            $resp= ['mensaje'=>"El código es incorrecto o ya se encuentra desactivado.",'tipo'=>"info",'titulo'=> "Oops.!"];
        }else  if (empty($existe_visitante)) {
            $resp= ['mensaje'=>"El numero de identificación no se encuentra registrado.",'tipo'=>"info",'titulo'=> "Oops.!"];
        }else{
            $evento = $evento[0];
            $tipo_normal = $tipo_normal[0];
            $usuario_registro = $usuario_registro[0];
            $estado =  $this->obtener_estado($evento['fecha_inicio'], $evento['fecha_fin'],$evento['usuario_elimina']);
            if ($estado != "Eve_Reg" && $estado != "Eve_Cur") {
                $resp= ['mensaje'=>"El evento o la visita ya se encuentra terminado(a), por tal motivo no es posible continuar.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if ($evento['pre_inscripcion'] == 2) {
                $resp= ['mensaje'=>"No esta autorizado para realizar esta acción, ya que requiere una Pre-inscripción por parte del responsable del evento.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $id_persona = $existe_visitante->{'id'};
                $id_evento = $evento['id'];
                $usuario_registro =  $usuario_registro["id"];
                $id_tipo = $tipo_normal['id'];
                $fecha_entrada_evento =   date("Y-m-d H:i:s");
                
                $en_evento = $this->visitas_model->verificar_ingreso_evento($id_evento,$id_persona);

                if ($evento['pre_inscripcion'] != 3 && !empty($en_evento)){
                    $resp= ['mensaje'=>"El participante ya se encuentra en el evento.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else{
                    $data = array(
                        "id_tipo"=>$id_tipo,
                        "id_evento"=>$id_evento,
                        "id_persona"=>$id_persona,
                        "fecha_entrada_evento"=>$fecha_entrada_evento,
                        "usuario_marca_entrada"=>$usuario_registro,
                        "observaciones "=>$observaciones,
                        "usuario_registra"=>$usuario_registro,
                        );
                    $add =  $this->visitas_model->guardar_datos($data, "participantes_evento");
                    if($add != 0) $resp = ['mensaje'=>'Error al guardar el participante, contacte con el administrador del sistema.' ,'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
    }
      echo json_encode($resp);
  }

  public function traer_codigo_evento (){
    $caracteres = "abcdefghijklmnopqrstuvwxyz1234567890";
    $codigo = "";
    $longitud = 6;
    $carac_desordenada = str_shuffle($caracteres);
    for($i=0;$i < $longitud;$i++) {
        $codigo .= $carac_desordenada[$i]; 
    }
    return $codigo;
    
  }

  public function adjuntar_firma($name){
    if ( isset($_POST[$name]) && !empty($_POST[$name]) ) {    
        $dataURL = $_POST[$name];  
        $parts = explode(',', $dataURL);  
        $data = $parts[1];  
        $data = base64_decode($data);  
        $file =  uniqid() . '.png';
        $success = file_put_contents('archivos_adjuntos/visitas/firmas/'.$file, $data);
        return $success ? $file : -1;
    }
      return -2;
    }

    public function generar_acta($id){
		if ($this->Super_estado){
            $en_evento = $this->visitas_model->buscar_ingreso_evento_id($id);
            if($en_evento){
                $data['nombre_completo'] = $en_evento->{'nombre_completo'};
                $data['nombre_archivo'] = $id . '.pdf';
                $data['firma'] = $en_evento->{'con_firma'};
                $this->load->view("templates/descargar_acta_table", $data);
                return;
            }
        } 
        redirect('/', 'refresh');
	}

}
