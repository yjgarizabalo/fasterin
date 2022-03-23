<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class proyectos_index_control extends CI_Controller {
//Variables encargadas de los permisos que tiene el usuario en session
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
    var $Super_agrega = 0;
    var $ruta_adjunto = "archivos_adjuntos/proyectos/adjuntos/";
    var $ruta_archivos_proyectos = "/archivos_adjuntos/proyectos_index/";
 //Construtor del controlador, se importa el modelo personas_model y se inicia la session
	public function __construct()
	{
        parent::__construct();
        $this->load->model('proyectos_index_model');
        $this->load->model('genericas_model');
        $this->load->model('pages_model');
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

  public function index($pages = "proyectos_index",$id = '')
  {
    
      if ($this->Super_estado) {
          $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $pages);
          if (!empty($datos_actividad)) {
          $data['js'] = "Proyectos_index";
          $data['id'] =$id;
          $data['actividad'] = $datos_actividad[0]["id_actividad"];
          }else{
              $pages = "sin_session";
              $data['js'] = "";
              $data['actividad'] = "Permisos";
          }
      }else{
          $pages = "inicio";
          $data['js'] = "";
          $data['actividad'] = "Ingresar";
      }
      $this->load->view('templates/header', $data);
      $this->load->view("pages/" . $pages);
      $this->load->view('templates/footer');
  }
  public function listar_comites_cbx() {
    $resp = $this->Super_estado == true ? $this->proyectos_index_model->listar_comites_cbx() : array();
    echo json_encode($resp);
  }
  public function listar_comites()
  {
    $resp = $this->Super_estado == true ? $this->proyectos_index_model->listar_comites(null,'list') : array();
    $comites = array();
    $ver_solicitado= '<span  style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
    $ver_finalizado = '<span  style="background-color: #39B23B;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
    $ver_curso= '<span style="background-color: #EABD32;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';

    $btn_modificar = '<span style="color: #2E79E5;" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench pointer btn btn-default modificar"></span>';
    $btn_enviar= '<span style="color: #EABD32;" title="Enviar" data-toggle="popover" data-trigger="hover" class="fa fa-send pointer btn btn-default enviar"></span>';
    $btn_terminar= '<span title="Terminar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default terminar" style="color:#39B23B"></span>';

    $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
    $btn_abierta = '<span title="Comité en espera..." data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half" style="color:#428bca"></span>';
    $adm = $_SESSION['perfil'] == 'Per_Admin' || $_SESSION['perfil'] == 'Per_Adm_index' ? true: false;
      foreach ($resp as $row) {

        $estado_comite = $row['id_estado_comite'];
        $fecha_cierre =date("Y-m-d",strtotime($row['fecha_cierre']));
        $fecha_actual =date("Y-m-d");

        if ($estado_comite == 'Com_Ini') {
            $row['ver'] = $ver_solicitado;
            $row["accion"] = $adm ? "$btn_modificar $btn_enviar" : $btn_abierta;
        }else   if ($estado_comite == 'Com_Not') {
            $row['ver'] = $ver_curso;
            $row["accion"] = $fecha_actual >= $fecha_cierre && $adm ? "$btn_modificar $btn_terminar" : $btn_abierta;
        }else   if ($estado_comite == 'Com_Ter') {
            $row['ver'] = $ver_finalizado;
            $row["accion"] = $btn_cerrada;
        }
        array_push($comites,$row);
      }
      echo json_encode($comites);
  }
  public function obtener_programas_departamento()
  {        
    $id = $this->input->post("id");
    $programas = $this->Super_estado == true ? $this->proyectos_index_model->obtener_programas_departamento($id) : array();
    echo json_encode($programas);
  }

  public function cambiar_estado_comite()
  {
      if ($this->Super_estado == false) {
          $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
      } else {
          if ($this->Super_modifica == 0) {
              $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
          } else {
            $id = $this->input->post('id');
            $estado = $this->input->post('estado');
            $usuario_registra = $_SESSION['persona'];
            $str = $this->verificar_campos_string(['ID'=>$id,'Estado'=>$estado,]);
            if (is_array($str)) {
              $campo = $str['field'];
              $resp = ['mensaje'=>"Error al cargar el $campo, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
            }else{
              $valido = $this->validar_estado_comite($estado,$id);
              if($valido){
                  $data = array('id_estado_comite'=>$estado,);
                  $add = $this->proyectos_index_model->modificar_datos($data, "comites",$id);
                  $resp= ['mensaje'=>"El estado fue modificado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                  if ($add != 1) $resp = ['mensaje'=>"Error al cambiar el estado, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                  else{
                      if ($estado == 'Com_Ter') {
                        $ges = $this->proyectos_index_model->aprobar_proyectos_masivo($id,$usuario_registra);
                        $proyectos =  $this->proyectos_index_model->listar_proyectos($id, $usuario_registra,"cp.id_estado_proyecto = 'Proy_Reg'");
                        if (!empty($proyectos)) $resp= ['mensaje'=>"El estado fue modificado con exito, pero algunos proyectos estan pendientes.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!","abrir"=> true];
                      }
                  }
              }else{
                  $resp = ['mensaje'=>"No es posible continuar, el comité fue gestionado anteriormente o no cuenta con proyectos asignados.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
              }
            }
          }

      }
      echo json_encode($resp);
  }

  public function validar_estado_comite($estado_nue,$id)
  {
      $comite = $this->proyectos_index_model->listar_comites($id)[0];
      $estado_actual =  $comite['id_estado_comite'];
      $total =  $comite['total'];
      if($total > 0){
          if($estado_actual == 'Com_Ini' && $estado_nue == 'Com_Not' ){
              return true;
          }else if($estado_actual == 'Com_Not' && $estado_nue == 'Com_Ter'){
              return true;
          }
      } 
      return false;
  }

  private function verificar_campos_numericos($array, $tipo = 1){
		foreach ($array as $row) {
			if (($tipo == 1 && empty($row)) || ctype_space($row) || !is_numeric($row)) {
				return ['type' => -1, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
	}
	// Recibe un array con clave-valor con los campos a verificar. 
	// En caso de que uno de los campos esté vacio retorna el error -2 y el nombre del campo respectivo.
	private function verificar_campos_string($array){
		foreach ($array as $row) {
			if (empty($row) || ctype_space($row)) {
				return ['type' => -2, 'field' => array_search($row, $array, true)];
			}
		}
  }

    private function validateDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function buscar_postulante()
    {
        $personas = array();
        if ($this->Super_estado == true) {
            $dato = $this->input->post('dato');
            $tabla = $this->input->post('tabla');
            if (!empty($dato)) {
                if ($tabla == 'personas') {
                    $buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1";
                    $personas = $this->proyectos_index_model->buscar_postulante($buscar);
                } else {
                    $buscar = "(CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) LIKE '%" . $dato . "%' OR v.identificacion LIKE '%" . $dato . "%') AND v.estado=1";
                    $personas = $this->proyectos_index_model->buscar_visitante($buscar);
                }
            }
        }
        echo json_encode($personas);
        return;
    }

    public function obtener_departamentos()
    {
        $buscar = $this->input->post('buscar');
        $programas = $this->Super_estado == true ? $this->proyectos_index_model->obtener_departamentos($buscar) : array();
        echo json_encode($programas);
    }
    public function asignar_proyecto_solicitud()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else {
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else {
                $resp= ['mensaje'=>"El proyecto ha sido agregado a la solicitud.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                $id_proyecto = $this->input->post('id_proyecto');
                $id_comite = $this->input->post('id_comite');
                $observaciones = $this->input->post('observaciones');
                $comite = $this->proyectos_index_model->listar_comites($id_comite)[0];
                $guardar_observaciones = $comite['id_estado_comite'] != 'Com_Ini' ? true : false;

                $str = $this->verificar_campos_string(['Observaciones' => $observaciones]);

                if ($guardar_observaciones && is_array($str)) {
                    $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                } else {
                    $id_estado_proyecto = $comite['id_estado_comite'] == 'Com_Ter' ? 'Proy_Apr' : 'Proy_Reg';
                    $data = [
                        'id_comite' => $id_comite,
                        'id_estado_proyecto' => $id_estado_proyecto
                    ];

                    $agregar_proyecto = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos', $id_proyecto);
                    if ($agregar_proyecto != -1) {
                        $data_log = array(
                            "id_usuario_registro" => $_SESSION['persona'],
                            "id_proyecto"         => $id_proyecto,
                            "id_tipo"             => $id_estado_proyecto,
                            "observaciones"       => $guardar_observaciones ? $observaciones : ''
                        );

                        $guardar_log = $this->proyectos_index_model->guardar_datos($data_log, 'accion_proyectos_personas');
                        if ($guardar_log == -1) {
                            $resp = ['mensaje'=>'Error al guardar la información, contacte con el administrador.', 'tipo'=>'error', 'Titulo'=>'Oops!'];
                        }
                    } else {
                        $resp = ['mensaje'=>'Error al guardar la información, contacte con el administrador.', 'tipo'=>'error', 'Titulo'=>'Oops!'];
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_proyectos()
    {
        $id =  $this->input->post('id');
        $id_departamento =  $this->input->post('id_departamento');
        $id_programa =  $this->input->post('id_programa');
        $nombre_grupo =  $this->input->post('nombre_grupo');
        $tipo_proyecto =  $this->input->post('tipo_proyecto');
        $tipo_recurso =  $this->input->post('tipo_recurso');
        $estado_proyecto =  $this->input->post('estado_proyecto');
        $codigo_proyecto =  $this->input->post('codigo_proyecto');
        $persona =  $_SESSION['persona'];
        $resp = $this->Super_estado == true ? $this->proyectos_index_model->listar_proyectos($id, $persona, null, $id_departamento, $id_programa, $nombre_grupo, $tipo_proyecto, $tipo_recurso, $estado_proyecto, $codigo_proyecto) : array();
        $proyectos = array();

        $adm_modulo = $_SESSION['perfil'] == 'Per_index' || $_SESSION['perfil'] == 'Per_Adm_index' ? true: false;
        $adm = $_SESSION['perfil'] == 'Per_Admin' ? true: false;
       
        $ver_solicitado= '<span  style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
        $ver_aprobado = '<span  style="background-color: #39B23B;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
        $ver_cancelado= '<span   style="background-color: #d9534f;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
        $btn_aprobar = "<span title='Aprobar' data-toggle='popover' data-trigger='hover' class='fa fa-check btn btn-default aprobar' style='color:#39B23B'></span>";
        $btn_aprobar_t2 = "<span title='Aprobar' data-toggle='popover' data-trigger='hover' class='fa fa-check btn btn-default aprobar2' style='color:#39B23B'></span>";
        $btn_quitar = '<span class="fa fa-minus-circle pointer btn btn-default quitar" title="Quitar de Comité el proyecto" style="color: #6e1f7c; margin: 0 1px;"></span>';
        $btn_consulta= '<span title="Consulta" data-toggle="popover" data-trigger="hover" class="fa fa-question btn btn-default consulta" style="color:#428bca"></span>';
        $btn_negar= '<span title="Negar" data-toggle="popover" data-trigger="hover" class="fa fa-ban btn btn-default negar" style="color:#d9534f"></span>';
        $btn_revertir= '<span title="Revertir" data-toggle="popover" data-trigger="hover" class="fa fa-refresh btn btn-default revertir" style="color:#428bca"></span>';
        $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
        $btn_abierta = '<span title="Proyecto en espera..." data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half" style="color:#428bca"></span>'; 

        foreach ($resp as $row) {
            $row['ver'] = $ver_solicitado;
            $row["accion"] = $btn_cerrada;
            $row['total_con2'] = '$ ' . $this->convertir_moneda($row['efectivo'] + $row['especie'] + $row['externo'],true,0);
            $row['efectivo_con2'] = '$ ' . $this->convertir_moneda($row['efectivo'],true,0);
            $row['externo_con'] = '$ ' . $this->convertir_moneda($row['externo'],true,0);
            $row['especie_con'] = '$ ' . $this->convertir_moneda($row['especie'],true,0);

            $datos = $this->proyectos_index_model->listar_proyecto_presupuestos($row['id']);
            $efectivo_con = null;
            $total_con = null;
            if (!empty($datos)) {
                foreach ($datos as $dato) {
                    $total_con += $dato['valor_total'];
                    if ($dato['id_tipo_valor'] == 'Pre_Efec') {
                        $efectivo_con += $dato['valor_total'];
                    }
                }
                $efectivo_con = '$ ' . $this->convertir_moneda($efectivo_con,true,0);
                $total_con = '$ ' . $this->convertir_moneda($total_con,true,0);
            }
            $row['efectivo_con'] = $efectivo_con;
            $row['total_con'] = $total_con;
            
            $estado_comite = $row['id_estado_comite'];
            $estado_proyecto = $row['id_estado_proyecto'];
            $gestionado = $row['gestionado'];


            if ($estado_proyecto == 'Proy_Apr')  $row['ver'] = $ver_aprobado;
            else if ($estado_proyecto == 'Proy_Can' || $estado_proyecto == 'Proy_Neg')  $row['ver'] = $ver_cancelado;

            if ($estado_comite == 'Com_Ini') {
                if ($estado_proyecto == 'Proy_Reg' && empty($gestionado)) {
                    $row["accion"] = $adm || $adm_modulo ? "$btn_quitar" : $btn_abierta;
                }
            }else if ($estado_comite == 'Com_Not') {
                if ($estado_proyecto == 'Proy_Reg' &&  empty($gestionado)) {
                    $row["accion"] = !$adm_modulo ? "$btn_aprobar $btn_negar" :$btn_abierta;
                }else if ($estado_proyecto == 'Proy_Reg' && !empty($gestionado)) {
                    $row["accion"] = $btn_revertir;
                }
            }else{
                if ($adm_modulo || $adm ) {
                    if ($estado_proyecto == 'Proy_Reg' &&  empty($gestionado)) {
                        $row["accion"] = "$btn_aprobar_t2 $btn_negar";
                    } else if (empty($row['codigo_proyecto'])) {
                        $tipo = strtoupper(str_replace('Pro_', '', $row['id_tipo_proyecto']));
                        $row['accion'] = "<span title='Agregar código del proyecto' data-toggle='popover' data-trigger='hover' class='btn btn-default codigo' style='color:#2E79E5'><span class='fa fa-plus'></span> <em>$tipo...</em></span>";
                    } else {
                        $row['accion'] = $btn_cerrada;
                    }
                }
            }
            array_push($proyectos,$row);
        }
        echo json_encode($proyectos);
    }

    public function gestionar_proyecto()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
                $estado = $this->input->post('estado');
                $observaciones = $this->input->post('observaciones');
                $id = $this->input->post('id');
                $gestionado = $this->input->post('gestionado');
                $id_usuario_registro = $_SESSION["persona"];
                
              if (empty($estado) || empty($id)) {
                  $resp= ['mensaje'=>"Error al cargar la información del proyecto, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
              }else {
                  $estado_valido = $this->validar_estado($id,$estado);
                  if ($estado_valido['valido']) {
                      $data_solicitud = ['id_estado_proyecto' => $estado];
                      if ($estado == 'Proy_Acp') $data_solicitud['id_comite'] = null;
                      $data_estado = array("id_usuario_registro"=>$id_usuario_registro,"id_proyecto"=>$id, "id_tipo"=>$estado,"observaciones"=>$observaciones,);

                      if ($estado_valido['mod_estado']){
                          $data_estado = ['estado' => 0];
                          $this->proyectos_index_model->modificar_datos($data_estado , "accion_proyectos_personas" , $gestionado);
                          $resp = ['mensaje'=>"La accíon realizada por usted anteriormente fue revertida con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                      }
                      if($estado_valido['add_estado']){
                        $resp = ['mensaje'=>"El proyecto ya fue gestionado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"]; 
                        $add = $this->proyectos_index_model->guardar_datos($data_estado, "accion_proyectos_personas");
                        if ($add != 1) $resp = ['mensaje'=>"Error al gestionar el proyecto, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                        else{
                            if($estado_valido['mod_solicitud']) $this->proyectos_index_model->modificar_datos($data_solicitud , "comite_proyectos" , $id);
                        }
                      }

                  }else{
                      $resp = $estado_valido['mensaje']; 
                  }
         
              }
                
            }
        }
        echo json_encode($resp);
    }
    public function listar_estados_proyecto()
    {
        $id =  $this->input->post('id');
        $resp = $this->Super_estado == true ? $this->proyectos_index_model->listar_estados_proyecto($id) : array();
        echo json_encode($resp);
    }

    private function validar_estado($id,$estado)
    {
        $resp = ["valido"=>true,"add_estado" => true,"mod_estado" =>false,"mod_solicitud" =>true];
        $persona = $_SESSION['persona'];
        $perfil = $_SESSION['perfil'];
        $proyecto = $this->proyectos_index_model->traer_proyecto($id);
        $estado_actual_proy = $proyecto->{'id_estado_proyecto'};

        if ($proyecto->{'id_comite'} == null) {
            if ($estado_actual_proy == 'Proy_For') {
                if ($estado == 'Proy_Ban' || $estado == 'Proy_Can' || $estado == 'Proy_Rec') {
                    $resp = ['valido'=>true, 'add_estado'=>true, 'mod_estado'=>false, 'mod_solicitud'=>true];
                } else if ($estado == 'Proy_Rev') {
                    $temp = $this->validar_proyecto($id);
                    if ($temp['valido']) {
                        $resp = ['valido'=>true, 'add_estado'=>true, 'mod_estado'=>false, 'mod_solicitud'=>true];
                    } else {
                        $mensaje = (count($temp['datos_faltantes']) == 1) ? 'Te falta un ítem: ' . $temp['datos_faltantes'][0] : 'Te faltan varios items: ' . implode(', ', $temp['datos_faltantes']);
                        $resp = ['valido'=>false, 'mensaje'=>['mensaje'=>$mensaje , 'tipo'=>'info', 'titulo'=>'Oops!']];
                    }
                } else {
                    $resp = ['valido'=>false, 'mensaje'=>['mensaje'=>'No es posible continuar', 'tipo'=>'info', 'titulo'=>'Oops!']];
                }
            } else if ($estado_actual_proy == 'Proy_Rev') {
                if ($estado == 'Proy_Acp' || $estado == 'Proy_For' || $estado == 'Proy_Rec') {
                    $resp = ['valido'=>true, 'add_estado'=>true, 'mod_estado'=>false, 'mod_solicitud'=>true];
                } else {
                    $resp = ['valido'=>false, 'mensaje'=>['mensaje'=>'No es posible continuar', 'tipo'=>'info', 'titulo'=>'Oops!']];
                }
            } else if ($estado_actual_proy == 'Proy_Ban') {
                if ($estado == 'Proy_For') {
                    $resp = ['valido'=>true, 'add_estado'=>true, 'mod_estado'=>false, 'mod_solicitud'=>true];
                } else {
                    $resp = ['valido'=>false, 'mensaje'=>['mensaje'=>'No es posible continuar', 'tipo'=>'info', 'titulo'=>'Oops!']];
                }
            } else if ($estado_actual_proy == 'Proy_Acp') {
                if ($estado == 'Proy_Reg' || $estado == 'Proy_For' || $estado == 'Proy_Rec') {
                    $resp = ['valido'=>true, 'add_estado'=>true, 'mod_estado'=>false, 'mod_solicitud'=>true];
                } else {
                    $resp = ['valido'=>false, 'mensaje'=>['mensaje'=>'No es posible continuar', 'tipo'=>'info', 'titulo'=>'Oops!']];
                }
            }  else if ($estado_actual_proy == 'Proy_Can' || $estado_actual_proy == 'Proy_Rec') {
                $resp = ['valido'=>false, 'mensaje'=>['mensaje'=>'El proyecto ha sido Cancelado o Rechazado, por tal motivo no es posible continuar', 'tipo'=>'info', 'titulo'=>'Oops!']];
            }
        } else {
            $proyecto = $this->proyectos_index_model->get_proyecto_id($id,$persona);
            $comite = $this->proyectos_index_model->listar_comites($proyecto->{'id_comite'})[0];
            $estado_actual_com = $comite['id_estado_comite'];

            if ($estado == 'Proy_For') {
                $resp = ['valido'=>false, 'mensaje'=>['mensaje'=>'El proyecto ya está en comité, por tal motivo no es posible continuar', 'tipo'=>'info', 'titulo'=>'Oops!']];
            } else {
                if ($estado_actual_com == 'Com_Ini') {
                    if ($estado_actual_proy == 'Proy_Reg') {
                        if ($estado == 'Proy_Can') {
                            $tipo_proyecto =$proyecto->{'tipo_proyecto'};
                            $gestiona_tipo = $this->proyectos_index_model->proyecto_asignado_persona($persona,$tipo_proyecto);
                            if (empty($gestiona_tipo) && $perfil != 'Per_Admin') {
                                $resp = ["valido"=>false,"mensaje" => ['mensaje'=>'No cuenta con los permisos para gestionar el proyecto.','tipo'=>'info','titulo'=>'Oops.!']];
                            }else{
                                $resp = ["valido"=>true,"add_estado" => true,"mod_estado" =>false,"mod_solicitud" =>true];
                            }
                        }
                    }else{
                        $resp = ["valido"=>false,"mensaje" => ['mensaje'=>'El proyecto ya fue cerrado por tal motivo no es posible continuar.','tipo'=>'info','titulo'=>'Oops.!']];
                    }
                }else if ($estado_actual_com == 'Com_Not') {
                    if ($estado_actual_proy == 'Proy_Reg') {
                        if ($estado == 'Proy_Rev') {
                            $resp = ["valido"=>true,"add_estado" => false,"mod_estado" =>true,"mod_solicitud" =>false];
                        }else if ($estado == 'Proy_Con' || $estado == 'Proy_Neg' || $estado == 'Proy_Apr') {
                             $resp = ["valido"=>true,"add_estado" => true,"mod_estado" =>false,"mod_solicitud" =>false];
                        }
                    }else{
                        $resp = ["valido"=>false,"mensaje" => ['mensaje'=>'El proyecto ya fue cerrado por tal motivo no es posible continuar.','tipo'=>'info','titulo'=>'Oops.!']];
                    }
                }else{
                    $adm = $_SESSION['perfil'] == 'Per_index' || $_SESSION['perfil'] == 'Per_Adm_index' || $_SESSION['perfil'] == 'Per_Admin' ? true: false;
                    if ($estado_actual_proy == 'Proy_Reg' && $adm) {
                        if ($estado == 'Proy_Neg' || $estado == 'Proy_Apr') {
                            $resp = ["valido"=>true,"add_estado" => true,"mod_estado" =>false,"mod_solicitud" =>true];
                        }
                    } elseif ($estado_actual_proy == 'Proy_Apr') {
                        if ($estado == 'Proy_Sol') {
                            $resp = ['valido'=>true, 'add_estado'=>true, 'mod_estado'=>false, 'mod_solicitud'=>true];
                        }
                    } else{
                        $resp = ["valido"=>false,"mensaje" => ['mensaje'=>'El Comite ya fue cerrado por tal motivo no es posible continuar.','tipo'=>'info','titulo'=>'Oops.!']];
                    }
                }
            }
        }

        return $resp;
    }
    
    private function convertir_moneda($number,$format , $decimal = 2){
        if (!$format) {
            $number= str_replace(".", "", $number);
            $number= str_replace(",", ".", $number);
           return $number;
        }
        return number_format($number,$decimal ,",", ".");
    }

    public function listar_personas_index()
    {
        $personas = $this->Super_estado == true ? $this->proyectos_index_model->listar_personas_index() : array();
        echo json_encode($personas);
    }

    public function asignar_persona_aprueba()
    {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
                $id_persona = (int) $this->input->post('id_persona');
                $id_usuario_registra = $_SESSION['persona'];

                $str = $this->verificar_campos_numericos(['Persona'=>$id_persona]);
				if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"Seleccione $campo.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
                    $tiene = $this->proyectos_index_model->persona_asignada_aprueba($id_persona);
                    if (empty($tiene)) {
                        $data = array(
                            'id_persona'=>$id_persona,
                            'id_usuario_registra'=>$id_usuario_registra,
                        );
                        $add = $this->proyectos_index_model->guardar_datos($data, "personas_aprueban_index");
                        $resp= ['mensaje'=>"El permiso fue asignado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if ($add != 1) $resp = ['mensaje'=>"Error al asignar el permiso, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];    
                    }else{
                        $resp= ['mensaje'=>"No es posible continuar, ya que La persona seleccionada cuenta con este permiso asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }
       
                }
            }
            
        
        }
        echo json_encode($resp);

    }

    public function personas_aprueban_index()
    {
        $personas = $this->Super_estado == true ? $this->proyectos_index_model->personas_aprueban_index() : array();
        echo json_encode($personas);
    }

    public function retirar_persona_aprueba(){
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_elimina== 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
                $id = $this->input->post('id');
                $id_usuario_elimina = $_SESSION['persona'];
                $fecha_elimina = Date('Y-m-d H:i:s');
                $str = $this->verificar_campos_numericos(['Persona'=>$id]);
                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"Seleccione $campo.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else{
                    $add = $this->proyectos_index_model->modificar_datos(['fecha_elimina' => $fecha_elimina,'id_usuario_elimina' => $id_usuario_elimina,'estado' => 0], "personas_aprueban_index", $id);
                    $resp= ['mensaje'=>"El permiso fue retirado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if ($add != 1) $resp = ['mensaje'=>"Error al retirar el permiso, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];    
        
                }
            }         
        }
        echo json_encode($resp);
    }

    public function listar_personas_adm_index()
    {
        $personas = $this->Super_estado == true ? $this->proyectos_index_model->listar_personas_adm_index() : array();
        echo json_encode($personas);
    }

    public function listar_actividades(){
		$persona = $this->input->post('persona');
		$data = ($this->Super_estado == true && isset($persona) && !empty($persona))
			? $this->proyectos_index_model->listar_actividades($persona)
			: [];
		echo json_encode($data);
    }
    
    public function asignar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_agrega) {
			$actividad = (int) $this->input->post('id');
            $persona = (int) $this->input->post('persona');
            
            $num = $this->verificar_campos_numericos(['Persona'=>$persona,'Actividad'=>$actividad]);
			if (is_array($num)) {
                $campo = $num['field'];
                $resp = ['mensaje'=>"Seleccione $campo.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
			}else{
                $ok = $this->proyectos_index_model->validar_asignacion_actividad($actividad, $persona);
                if ($ok) {
                    $data = ['id_tipo'=>$actividad, 'id_persona'=>$persona, 'id_usuario_registra'=>$_SESSION['persona']];
                    $resp = $this->proyectos_index_model->guardar_datos($data, 'proyectos_index_persona');
                    $res = $resp == 1
                    ? ['mensaje'=>"Actividad asignada exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
                    : ['mensaje'=>"Ha ocurrido un error al asignar la actividad.",'tipo'=>"info",'titulo'=> "Ooops!"];
                } else $res = ['mensaje'=>"El usuario ya tiene asignada esta actividad.",'tipo'=>"info",'titulo'=> "Ooops!"];
            }
		} else $res = ['mensaje' => 'No tiene Permisos Para Realizar Esta operación.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
		echo json_encode($res);
    }
    
    public function quitar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_modifica) {
			$id = (int) $this->input->post('asignado');
			$actividad = (int) $this->input->post('id');
            $persona = (int) $this->input->post('persona');
            $num = $this->verificar_campos_numericos(['Persona'=>$persona,'Actividad'=>$actividad]);
			if (is_array($num)) {
                $campo = $num['field'];
                $resp = ['mensaje'=>"Seleccione $campo.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
			}else{
                // Verifico si actividad ya está asignada o no. Esta función retorna 0 si no está asignada la actividad y 1 si lo está.
                $ok = $this->proyectos_index_model->validar_asignacion_actividad($actividad, $persona);
                if (!$ok) {
                    $data = ['id_usuario_elimina'=>$_SESSION['persona'], 'fecha_elimina'=>date("Y-m-d H:i:s"), 'estado'=>0];
                    $resp = $this->proyectos_index_model->modificar_datos($data, 'proyectos_index_persona', $id);
                    if ($resp) {
                        $res = $resp == 1
                            ? ['mensaje'=>"Actividad removida exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
                            : ['mensaje'=>"Ha ocurrido un error al desasignar la actividad.",'tipo'=>"info",'titulo'=> "Ooops!"];
                    } else $res = ['mensaje'=>"Ha ocurrido un error al desasignar la actividad.",'tipo'=>"info",'titulo'=> "Ooops!"];
                } else $res = ['mensaje'=>"El usuario no tiene asignada esta actividad.",'tipo'=>"info",'titulo'=> "Ooops!"];
            }
		} else $res = ['mensaje' => 'No tiene Permisos Para Realizar Esta operación.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
		echo json_encode($res);
    }
    
    public function listar_estados(){
		if (!$this->Super_estado) $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$actividad = $this->input->post('actividad');
			$data = $this->proyectos_index_model->listar_estados($actividad);
		}
		echo json_encode($data);
    }
    
    public function asignar_estado(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->proyectos_index_model->validar_asignacion_estado($estado, $actividad, $persona);
				if ($ok) {
					$data = ['id_estado'=>$estado, 'id_actividad'=>$actividad, 'id_usuario_registra'=>$_SESSION['persona']];
					$resp = $this->proyectos_index_model->guardar_datos($data, 'proyectos_index_estados');
					$res = $resp == 1
						? ['mensaje'=>"Estado asignado exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						: ['mensaje'=>"Ha ocurrido un error al asignar el estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
				} else $res = ['mensaje'=>"El usuario ya tiene asignada esta actividad.",'tipo'=>"info",'titulo'=> "Ooops!"];
			} else $res = ['mensaje' => 'No tiene Permisos Para Realizar Esta operación.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
		}
		echo json_encode($res);
	}

	public function quitar_estado(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$id = $this->input->post('id');
				$ok = $this->proyectos_index_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
                    $data = ['id_usuario_elimina'=>$_SESSION['persona'], 'fecha_elimina'=>date("Y-m-d H:i:s"), 'estado'=>0];
					$resp = $this->proyectos_index_model->modificar_datos($data, 'proyectos_index_estados', $id);
					$res = $resp == 1
						? ['mensaje'=>"Estado Desasignada exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						: ['mensaje'=>"Ha ocurrido un error al desasignar el estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
				}else $res = ['mensaje'=>"El usuario no tiene asignado este estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
			}else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		}
		echo json_encode($res);
	}

	public function activar_notificacion(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
                $persona = $this->input->post('persona');
                $id = $this->input->post('id');
				$ok = $this->proyectos_index_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$resp = $this->proyectos_index_model->modificar_datos(['notificacion' => 1], 'proyectos_index_estados', $id);
					$res = $resp == 1
						? ['mensaje'=>"Notificaciones activadas exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						: ['mensaje'=>"Ha ocurrido un error al activar las notificaciones.",'tipo'=>"info",'titulo'=> "Ooops!"];
				} else $res = ['mensaje'=>"El usuario no tiene asignado este estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
			}else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		}
		echo json_encode($res);
	}

	public function desactivar_notificacion() {
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
                $persona = $this->input->post('persona');
                $id = $this->input->post('id');
				$ok = $this->proyectos_index_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$resp = $this->proyectos_index_model->modificar_datos(['notificacion' => 0], 'proyectos_index_estados', $id);
					$res = $resp == 1
						? ['mensaje'=>"Notificaciones desactivadas exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						: ['mensaje'=>"Ha ocurrido un error al desactivar las notificaciones.",'tipo'=>"info",'titulo'=> "Ooops!"];
				} else $res = ['mensaje'=>"El usuario no tiene asignado este estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
			}else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		}
		echo json_encode($res);
    }
    
    public function activar_gestion(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
                $persona = $this->input->post('persona');
                $id = $this->input->post('id');
				$ok = $this->proyectos_index_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$resp = $this->proyectos_index_model->modificar_datos(['gestion' => 1], 'proyectos_index_estados', $id);
					$res = $resp == 1
						? ['mensaje'=>"Gestión activada exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						: ['mensaje'=>"Ha ocurrido un error al activar la gestión.",'tipo'=>"info",'titulo'=> "Ooops!"];
				} else $res = ['mensaje'=>"El usuario no tiene asignado este estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
			}else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		}
		echo json_encode($res);
	}

	public function desactivar_gestion() {
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
                $persona = $this->input->post('persona');
                $id = $this->input->post('id');
				$ok = $this->proyectos_index_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$resp = $this->proyectos_index_model->modificar_datos(['gestion' => 0], 'proyectos_index_estados', $id);
					$res = $resp == 1
						? ['mensaje'=>"Gestión desactivada exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						: ['mensaje'=>"Ha ocurrido un error al desactivar la gestión.",'tipo'=>"info",'titulo'=> "Ooops!"];
				} else $res = ['mensaje'=>"El usuario no tiene asignado este estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
			}else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		}
		echo json_encode($res);
	}

    public function listar_proyecto_id() {
        $id = $this->input->post('id');
        $proyecto = $this->Super_estado == true && !empty($id) ? $this->proyectos_index_model->listar_proyecto_id($id) : array();
        if (!empty($proyecto)){
            $proyecto->{'total_con'}= $this->convertir_moneda($proyecto->{'efectivo'} + $proyecto->{'especie'},true,0);
            $proyecto->{'efectivo_con'} = $this->convertir_moneda($proyecto->{'efectivo'},true,0);
            $proyecto->{'especie_con'} = $this->convertir_moneda($proyecto->{'especie'},true,0);
            $proyecto->{'externo_con'} = $this->convertir_moneda($proyecto->{'externo'},true,0);
        }
        echo json_encode($proyecto);
    }
    public function buscar_departamento()
    {
        $dato = $this->input->post('dato');
        $departamentos = $this->Super_estado == true ? $this->proyectos_index_model->buscar_departamento($dato) : array();
        echo json_encode($departamentos);
    }

    public function agregar_persona()
	{
		if (!$this->Super_estado) $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega == 0) $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else {
				$identificacion = $this->input->post('identificacion');
				$id_tipo_identificacion =  $this->input->post('id_tipo_identificacion');
				$id_tipo_persona = 'PerExt';
				$nombre = $this->input->post('nombre');
				$apellido = $this->input->post('apellido');
				$segundo_nombre = $this->input->post('segundo_nombre');
				$segundo_apellido = $this->input->post('segundo_apellido');
				$usuario_registra = $_SESSION['persona'];

				$str = $this->verificar_campos_string(['Nombre'=>$nombre,'Apellido'=>$apellido,'Segundo Apellido'=>$segundo_apellido,]);
				$num = $this->verificar_campos_numericos(['Identificacion'=>$identificacion,'Tipo identificacion'=>$id_tipo_identificacion]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"El campo $campo no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else if (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
                    $buscar = "p.identificacion = '$identificacion'";
                    $existe = $this->proyectos_index_model->buscar_postulante($buscar);
                    if(!empty($existe)){
                        $resp = ['mensaje'=>"El numero de cedula ya se encuentra registrado en el sistema.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                    }else{
                        $data = array(
                            'identificacion'=>$identificacion,
                            'tipo_identificacion'=>$id_tipo_identificacion,
                            'tipo'=>$id_tipo_persona,
                            'nombre'=>$nombre,
                            'apellido'=>$apellido,
                            'segundo_nombre'=>$segundo_nombre,
                            'segundo_apellido'=>$segundo_apellido,
                            'usuario_registra'=>$usuario_registra,
                        );
                        $add = $this->proyectos_index_model->guardar_datos($data, "visitantes");
                        if ($add != 1) {
                            $resp = ['mensaje'=>"Error al guardar la persona, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                        }else{
                            $postulante = $this->proyectos_index_model->traer_ultimo_postulante_usuario($usuario_registra);
                            $resp= ['mensaje'=>"La persona fue registrada con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!",'postulante' => $postulante];
                        }
					}

				}
			}
		}
		echo json_encode($resp);
    }

    public function obtener_valores_permisos() {
        $idparametro = $this->input->post('idparametro');
        $id_valor = $this->input->post('id_valor');
        $tipo = $this->input->post('tipo');
        $datos = $this->Super_estado == true ? $this->proyectos_index_model->obtener_valores_permisos($id_valor, $idparametro, $tipo) : array();
        echo json_encode($datos);
    }

    public function inicializar_proyecto() {
        if (!$this->Super_estado == true) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_usuario_registra = $_SESSION['persona'];
                $id_tipo_proyecto = $this->input->post('tipo_proyecto');
                $id_tipo_participante = $this->input->post('tipo_participante');
                $id_institucion = $this->input->post('institucion');
                $id_departamento = $this->input->post('id_departamento');
                $iva = $this->proyectos_index_model->traer_valor_parametro('Por_Iva', 2)->valor;
                // $id_departamento = $this->proyectos_index_model->traer_valor_parametro('Dep_NA',2)->id;
                $id_programa = $this->proyectos_index_model->traer_valor_parametro('Pro_NA', 2)->id;
                $id_grupo = $this->proyectos_index_model->traer_valor_parametro('Gru_NA', 2)->id;
                $info_participante = $this->proyectos_index_model->traer_informacion_participante($id_usuario_registra, 1);
                
                if (!$info_participante->id_departamento && !$id_departamento) {
                    $resp = ['mensaje' => '', 'tipo' => 'sin_departamento', 'titulo' => ''];
                } else {
                    $data = [
                        'id_usuario_registra' => $id_usuario_registra,
                        'id_departamento'     => $info_participante->id_departamento == null ? $id_departamento : $info_participante->id_departamento,
                        'id_programa'         => $info_participante->id_programa == null ? $id_programa : $info_participante->id_programa,
                        'nombre_grupo'        => $info_participante->id_grupo == null ? $id_grupo : $info_participante->id_grupo,
                        'investigador'        => $id_usuario_registra,
                        'tipo_proyecto'       => $id_tipo_proyecto,
                        'iva'                 => $iva
                    ];
                    $proyecto_inicializado = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos');
                    if ($proyecto_inicializado != 1) {
                        $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops!'];
                    } else {
                        $proyecto = $this->proyectos_index_model->traer_ultimo_proyecto_usuario($id_usuario_registra);
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!', 'id' => $proyecto->id];

                        $data = [
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto' => $proyecto->id,
                            'id_persona' => $id_usuario_registra,
                            'id_tipo_participante' => $id_tipo_participante,
                            'tipo_tabla' => 1,
                            'id_institucion' => $id_institucion
                        ];

                        $agregar_participante = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_participantes');
                        if ($agregar_participante == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                }
        
            }
            
        }
        echo json_encode($resp);
    }

    public function guardar_proyecto() {
        if (!$this->Super_estado == true) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id, '', 2)) {
                    $nombre_proyecto          = $this->input->post('nombre_proyecto');
                    $tipo_recurso             = $this->input->post('id_tipo_recurso');
                    $tipo_movilidad           = $this->input->post('tipo_movilidad');
                    $otro_tipo_movilidad      = $this->input->post('otro_tipo_movilidad');
                    $id_formacion_responsable = $this->input->post('formacion_responsable');
                    $tipo_responsable         = $this->input->post('tipo_responsable');
                    $id_responsable_externo   = $this->input->post('id_responsable_externo');
                    $fecha_inicial            = $this->input->post('fecha_inicial');
                    $fecha_final              = $this->input->post('fecha_final');
                    $no_beneficiarios         = $this->input->post('no_beneficiarios');
                    $resumen                  = $this->input->post('resumen');
                    $justificacion            = $this->input->post('justificacion');
                    $planteamiento_problema   = $this->input->post('planteamiento_problema');
                    $marco_teorico            = $this->input->post('marco_teorico');
                    $estado_arte              = $this->input->post('estado_arte');
                    $diseno_metodologico      = $this->input->post('diseno_metodologico');
                    $resultados_esperados     = $this->input->post('resultados_esperados');
                    $laboratorio              = $this->input->post('id_laboratorio');
                    $tipo_proyecto_grado      = $this->input->post('tipo_proyecto_grado');
                    $id_aux_proyecto          = $this->input->post('id_aux_proyecto');
                    $id_usuario_modifica      = $_SESSION['persona'];


                    $campos_verificar = ['Nombre del Proyecto' => $nombre_proyecto, 'Tipo de Recurso' => $tipo_recurso];
                    if ($id_aux_proyecto != 'Pro_Lab') {
                        $campos_verificar['Fecha Inicial'] = $fecha_inicial;
                        $campos_verificar['Fecha Final']   = $fecha_final;
                    }
    
                    $str = $this->verificar_campos_string($campos_verificar);
                    $num = $this->verificar_campos_numericos(['No Beneficiarios' => $no_beneficiarios]);
                    $fecha_i = $this->validateDate($fecha_inicial);
                    $fecha_f = $this->validateDate($fecha_final);
    
                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops.!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                    } else if((!$fecha_i || !$fecha_f) && $id_aux_proyecto != 'Pro_Lab'){
                        $resp = ['mensaje'=> "Por favor seleccione fechas validas y superior a la fecha actual.", 'tipo'=>"info", 'titulo'=> "Oops."];
                    } else if(($fecha_final <= $fecha_inicial) && $id_aux_proyecto != 'Pro_Lab'){
                        $resp = ['mensaje'=> "La fecha de inicio no debe ser superior a la fecha de terminación.", 'tipo'=>"info", 'titulo'=> "Oops."];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'nombre_proyecto'          => $nombre_proyecto ? $nombre_proyecto : null,
                            'tipo_recurso'             => $tipo_recurso  ? $tipo_recurso : null,
                            'id_tipo_movilidad'        => $tipo_movilidad  ? $tipo_movilidad  : null,
                            'otro_tipo_movilidad'      => $otro_tipo_movilidad  ?  $otro_tipo_movilidad: null,
                            'id_formacion_responsable' => $id_formacion_responsable  ?  $id_formacion_responsable: null,
                            'tipo_responsable'         => $tipo_responsable ? $tipo_responsable : null,
                            'id_responsable_externo'   => $id_responsable_externo ? $id_responsable_externo : null,
                            'fecha_inicial'            => $fecha_inicial ? $fecha_inicial : null,
                            'fecha_final'              => $fecha_final ? $fecha_final : null,
                            'no_beneficiarios'         => $no_beneficiarios ? $no_beneficiarios : null,
                            'resumen'                  => $resumen ? $resumen : null,
                            'justificacion'            => $justificacion ? $justificacion : null,
                            'planteamiento_problema'   => $planteamiento_problema ? $planteamiento_problema : null,
                            'marco_teorico'            => $marco_teorico ? $marco_teorico : null,
                            'estado_arte'              => $estado_arte ? $estado_arte : null,
                            'diseno_metodologico'      => $diseno_metodologico ? $diseno_metodologico : null,
                            'resultados_esperados'     => $resultados_esperados ? $resultados_esperados : null,
                            'laboratorio'              => $laboratorio ? $laboratorio : null,
                            'tipo_proyecto_grado'      => $tipo_proyecto_grado ? $tipo_proyecto_grado : null,
                            'id_usuario_modifica'      => $id_usuario_modifica,
                        ];
    
                        $agregar_proyecto = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos', $id);
                        if ($agregar_proyecto != 1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este proyecto', 'tipo' => 'error', 'titulo' => 'Oops!'];
                }
            }
            
        }
        echo json_encode($resp);
    }

    public function ultimo_proyecto() {
        if (!$this->Super_estado == true) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $proyecto = $this->proyectos_index_model->traer_ultimo_proyecto_usuario($_SESSION['persona']);
                if (isset($proyecto)) {
                    $resp = ['mensaje' => '', 'tipo' => 'success', 'titulo' => '', 'proyecto' => $proyecto];
                } else {
                    $resp = ['mensaje' => '', 'tipo' => 'success', 'titulo' => ''];
                }
            }
        }
        echo json_encode($resp);
    }

    public function guardar_preguntas_convenio_proceedings() {
        if (!$this->Super_estado == true) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, '', 2)) {
                    $operacionaliza   = $this->input->post('operacionaliza');
                    $codigo_convenio  = $this->input->post('codigo_convenio');
                    $proceedings      = $this->input->post('proceedings');
                    $verificado_por   = $this->input->post('verificado_por');
                    $id_codigo_sap    = $this->input->post('id_codigo_sap');
    
                    $str = $this->verificar_campos_string(['Operacionaliza' => $operacionaliza, 'Proceedings' => $proceedings, 'Verificado Por' => $verificado_por]);
                    $num = $this->verificar_campos_numericos(['ID del Proyecto' => $id_proyecto]);
    
                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops.!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                    } else {
                        $agrega = true;
                        if ($operacionaliza == 'Sí') {
                            $str = $this->verificar_campos_string(['Codigo del Convenio' => $codigo_convenio]);
                            if (is_array($str)) {
                                $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops.!'];
                                $agrega = false;
                            }
                        }
    
                        if ($agrega) {
                            $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                            $data = [
                                'codigo_convenio'     => $codigo_convenio == '' ? null : $codigo_convenio,
                                'proceedings'         => $proceedings,
                                'verificado_por'      => $verificado_por,
                                'id_codigo_orden_sap' => $id_codigo_sap
                            ];
        
                            $agregar_proyecto = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos', $id_proyecto);
                            if ($agregar_proyecto != 1) {
                                $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops!'];
                            }
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este proyecto', 'tipo' => 'error', 'titulo' => 'Oops!'];
                }
            }
        }
        echo json_encode($resp);
    }

    public function guardar_codigo_proyecto() {
        if (!$this->Super_estado == true) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id');
                if ($this->verificar_guardar_item($id_proyecto, '', 2)) {
                    $codigo_proyecto = $this->input->post('codigo_proyecto');

                    $str = $this->verificar_campos_string(['Código del Proyecto' => $codigo_proyecto]);
                    $num = $this->verificar_campos_numericos(['ID del Proyecto' => $id_proyecto]);

                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops.!'];
                    } else if(is_array($num)) {
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'codigo_proyecto' => $codigo_proyecto,
                        ];

                        $agregar_proyecto = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos', $id_proyecto);
                        if ($agregar_proyecto != 1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este proyecto.', 'tipo' => 'error', 'titulo' => 'Oops!'];
                }
            }
        }
        echo json_encode($resp);
    }

    public function guardar_participante() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_participantes')) {
                    $id_postulante            = $this->input->post('id_postulante');
                    $tipo_participante        = $this->input->post('tipo_participante');
                    $id_aux_tipo_participante = $this->input->post('id_aux_tipo_participante');
                    $tipo_tabla               = $this->input->post('tipo_tabla');
                    $institucion              = $this->input->post('institucion');
                    $id_usuario_registra      = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Postulante' => $id_postulante, 'Tipo de Participante' => $tipo_participante, 'Institución' => $institucion]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if($id_aux_tipo_participante == 'Pro_Inv_Pri' && $this->proyectos_index_model->verificar_lim_participante_principal($id_proyecto)) {
                        $resp = ['mensaje'=>"Ya has alcanzado el límite de Participantes Principales que se pueden agregar", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else if($this->proyectos_index_model->verificar_participante($id_proyecto, $id_postulante, $tipo_tabla)) {
                        $resp = ['mensaje'=>"Esta persona ya ha sido agregada al proyecto", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra'  => $id_usuario_registra,
                            'id_proyecto'          => $id_proyecto,
                            'id_persona'           => $id_postulante,
                            'id_tipo_participante' => $tipo_participante,
                            'tipo_tabla'           => $tipo_tabla,
                            'id_institucion'       => $institucion
                        ];

                        $agregar_participante = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_participantes');
                        if ($agregar_participante == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_participante() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_participantes')) {
                    $id                       = $this->input->post('id');
                    $id_postulante            = $this->input->post('id_postulante');
                    $tipo_participante        = $this->input->post('tipo_participante');
                    $id_aux_tipo_participante = $this->input->post('id_aux_tipo_participante');
                    $tipo_tabla               = $this->input->post('tipo_tabla');
                    $institucion              = $this->input->post('institucion');
                    $id_usuario_modifica      = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Tipo de Participante' => $tipo_participante, 'Institución' => $institucion]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if($id_aux_tipo_participante == 'Pro_Inv_Pri' && $this->proyectos_index_model->verificar_lim_participante_principal($id_proyecto) && $tipo_participante == 'principal' && $this->proyectos_index_model->verificar_lim_participante_principal($id_proyecto)->id != $id) {
                        $resp = ['mensaje'=>"Ya has alcanzado el límite de Participantes Principales que se pueden agregar", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else if($this->proyectos_index_model->verificar_participante($id_proyecto, $id_postulante, $tipo_tabla) && $this->proyectos_index_model->verificar_participante($id_proyecto, $id_postulante, $tipo_tabla)->id != $id) {
                        $resp = ['mensaje'=>"Esta persona ya ha sido agregada al proyecto", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_tipo_participante' => $tipo_participante,
                            'id_institucion'       => $institucion,
                            'id_usuario_modifica'  => $id_usuario_modifica
                        ];

                        $modificar_participante = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_participantes', $id);
                        if ($modificar_participante == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_participante() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_participantes')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];

                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $eliminar_participante = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_participantes', $id);
                        if ($eliminar_participante == -1) {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_lugar() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_lugares')) {
                    $pais                = $this->input->post('pais');
                    $ciudad              = $this->input->post('ciudad');
                    $id_usuario_registra = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Proyecto' => $id_proyecto, 'País' => $pais, 'Ciudad' => $ciudad]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto]);

                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto'         => $id_proyecto,
                            'pais'                => $pais,
                            'ciudad'              => $ciudad
                        ];

                        $agregar_lugar = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_lugares');
                        if ($agregar_lugar == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_lugar() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_lugares')) {
                    $id                  = $this->input->post('id');
                    $pais                = $this->input->post('pais');
                    $ciudad              = $this->input->post('ciudad');
                    $id_usuario_modifica = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Proyecto' => $id_proyecto, 'País' => $pais, 'Ciudad' => $ciudad]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto]);

                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'pais'                => $pais,
                            'ciudad'              => $ciudad,
                            'id_usuario_modifica' => $id_usuario_modifica
                        ];

                        $modificar_lugar = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_lugares', $id);
                        if ($modificar_lugar == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_lugar() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_lugares')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];

                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $eliminar_lugar = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_lugares', $id);
                        if ($eliminar_lugar == -1) {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_institucion() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_instituciones')) {
                    $id_institucion              = $this->input->post('id_institucion');
                    $responsabilidad_contraparte = $this->input->post('responsabilidad_contraparte_institucion');
                    $responsabilidad_cuc         = $this->input->post('responsabilidad_cuc_institucion');
                    $id_usuario_registra         = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra'         => $id_usuario_registra,
                            'id_proyecto'                 => $id_proyecto,
                            'id_institucion'              => $id_institucion,
                            'responsabilidad_contraparte' => $responsabilidad_contraparte,
                            'responsabilidad_cuc'         => $responsabilidad_cuc
                        ];

                        $agregar_institucion = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_instituciones');
                        if ($agregar_institucion == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_institucion() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_instituciones')) {
                    $id                          = $this->input->post('id');
                    $id_institucion              = $this->input->post('id_institucion');
                    $responsabilidad_contraparte = $this->input->post('responsabilidad_contraparte_institucion');
                    $responsabilidad_cuc         = $this->input->post('responsabilidad_cuc_institucion');
                    $id_usuario_modifica         = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_institucion'              => $id_institucion,
                            'responsabilidad_contraparte' => $responsabilidad_contraparte,
                            'responsabilidad_cuc'         => $responsabilidad_cuc,
                            'id_usuario_modifica'         => $id_usuario_modifica
                        ];

                        $modificar_institucion = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_instituciones', $id);
                        if ($modificar_institucion == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_institucion() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_instituciones')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];

                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $eliminar_institucion = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_instituciones', $id);
                        if ($eliminar_institucion == -1) {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_institucion_bdd() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $nombre_institucion            = $this->input->post('nombre_institucion');
                $nit_institucion               = $this->input->post('nit_institucion');
                $pais_origen_institucion       = $this->input->post('pais_origen_institucion');
                $correo_institucion            = $this->input->post('correo_institucion_bdd');
                $telefono_contacto_institucion = $this->input->post('telefono_contacto_institucion');
                $nombre_contacto_institucion   = $this->input->post('nombre_contacto_institucion');
                $id_usuario_registra           = $_SESSION['persona'];

                $str = $this->verificar_campos_string(['Nombre de la Institución' => $nombre_institucion, 'NIT de la Institución' => $nit_institucion, 'País de Origen' => $pais_origen_institucion, 'Nombre de Contacto' => $nombre_contacto_institucion, 'Correo de la Institución' => $correo_institucion]);
                $num = $this->verificar_campos_numericos(['Teléfono de Contacto' => $telefono_contacto_institucion]);

                if (is_array($str)) {
                    $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                } else if(is_array($num)){
                    $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                } else {
                    $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                    $data = [
                        'usuario_registra' => $id_usuario_registra,
                        'idparametro'      => '178',
                        'valor'            => $nombre_institucion,
                        'valorx'           => $nit_institucion,
                        'valory'           => $pais_origen_institucion,
                        'valorz'           => $telefono_contacto_institucion,
                        'valora'           => $nombre_contacto_institucion,
                        'valorb'           => $correo_institucion
                    ];

                    $agregar_institucion = $this->proyectos_index_model->guardar_datos($data, 'valor_parametro');
                    if ($agregar_institucion == -1) {
                        $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                    }
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_institucion_bdd() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id                            = $this->input->post('id');
                $nombre_institucion            = $this->input->post('nombre_institucion');
                $nit_institucion               = $this->input->post('nit_institucion');
                $pais_origen_institucion       = $this->input->post('pais_origen_institucion');
                $correo_institucion            = $this->input->post('correo_institucion_bdd');
                $telefono_contacto_institucion = $this->input->post('telefono_contacto_institucion');
                $nombre_contacto_institucion   = $this->input->post('nombre_contacto_institucion');

                $str = $this->verificar_campos_string(['Nombre de la Institución' => $nombre_institucion, 'NIT de la Institución' => $nit_institucion, 'País de Origen' => $pais_origen_institucion, 'Nombre de Contacto' => $nombre_contacto_institucion, 'Correo de la Institución' => $correo_institucion]);
                $num = $this->verificar_campos_numericos(['ID' => $id, 'Teléfono de Contacto' => $telefono_contacto_institucion]);

                if (is_array($str)) {
                    $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                } else if(is_array($num)){
                    $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                } else {
                    $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                    $data = [
                        'valor'  => $nombre_institucion,
                        'valorx' => $nit_institucion,
                        'valory' => $pais_origen_institucion,
                        'valorz' => $telefono_contacto_institucion,
                        'valora' => $nombre_contacto_institucion,
                        'valorb' => $correo_institucion
                    ];

                    $modificar_institucion = $this->proyectos_index_model->modificar_datos($data, 'valor_parametro', $id);
                    if ($modificar_institucion == -1) {
                        $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                    }
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_institucion_bdd() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id = $this->input->post('id');

                $num = $this->verificar_campos_numericos(['ID' => $id]);

                if(is_array($num)){
                    $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                } else {
                    $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];

                    $data = [
                        'estado' => 0
                    ];

                    $eliminar_institucion = $this->proyectos_index_model->modificar_datos($data, 'valor_parametro', $id);
                    if ($eliminar_institucion == -1) {
                        $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                    }
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_programa() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_programas')) {
                    $id_programa         = $this->input->post('programa');
                    $id_tipo_interaccion = $this->input->post('id_tipo_interaccion');
                    $id_usuario_registra = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Programa' => $id_programa, 'Tipo de Interacción' => $id_tipo_interaccion]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto'         => $id_proyecto,
                            'id_programa'         => $id_programa,
                            'id_tipo_interaccion' => $id_tipo_interaccion
                        ];

                        $agregar_programa = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_programas');
                        if ($agregar_programa == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_programa() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_programas')) {
                    $id                  = $this->input->post('id');
                    $id_programa         = $this->input->post('programa');
                    $id_tipo_interaccion = $this->input->post('id_tipo_interaccion');
                    $id_usuario_modifica = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Programa' => $id_programa, 'Tipo de Interacción' => $id_tipo_interaccion, 'ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_programa'         => $id_programa,
                            'id_tipo_interaccion' => $id_tipo_interaccion,
                            'id_usuario_modifica' => $id_usuario_modifica
                        ];

                        $modificar_programa = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_programas', $id);
                        if ($modificar_programa == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_programa() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_programas')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];

                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $eliminar_programa = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_programas', $id);
                        if ($eliminar_programa == -1) {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_asignatura() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_asignaturas')) {
                    $asignatura          = $this->input->post('asignatura_proyecto');
                    $id_usuario_registra = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Asignatura' => $asignatura]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto]);

                    if (is_array($str)) {
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto'         => $id_proyecto,
                            'asignatura'          => $asignatura
                        ];

                        $agregar_asignatura = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_asignaturas');
                        if ($agregar_asignatura == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_asignatura() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_asignaturas')) {
                    $id                  = $this->input->post('id');
                    $asignatura          = $this->input->post('asignatura_proyecto');
                    $id_usuario_modifica = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Asignatura' => $asignatura]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'ID' => $id]);

                    if (is_array($str)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'asignatura'          => $asignatura,
                            'id_usuario_modifica' => $id_usuario_modifica
                        ];

                        $modificar_asignatura = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_asignaturas', $id);
                        if ($modificar_asignatura == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_asignatura() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_asignaturas')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];

                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $eliminar_asignatura = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_asignaturas', $id);
                        if ($eliminar_asignatura == -1) {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_sublinea() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_sublineas')) {
                    $grupo               = $this->input->post('grupo_investigacion');
                    $linea               = $this->input->post('linea_investigacion');
                    $sublinea            = $this->input->post('sublinea_investigacion');
                    $id_usuario_registra = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Grupo de Investigación' => $grupo, 'Línea de Investigación' => $linea, 'Sub-Línea de Investigación' => $sublinea]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if ($this->proyectos_index_model->verificar_sublinea($id_proyecto, $sublinea)) {
                        $resp = ['mensaje'=>"Esta Sub-Línea ya existe", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto'         => $id_proyecto,
                            'id_grupo'            => $grupo,
                            'id_linea'            => $linea,
                            'id_sublinea'         => $sublinea
                        ];

                        $agregar_sublinea = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_sublineas');
                        if ($agregar_sublinea == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_sublinea() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_sublineas')) {
                    $id                  = $this->input->post('id');
                    $grupo               = $this->input->post('grupo_investigacion');
                    $linea               = $this->input->post('linea_investigacion');
                    $sublinea            = $this->input->post('sublinea_investigacion');
                    $id_usuario_modifica = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Grupo de Investigación' => $grupo, 'Línea de Investigación' => $linea, 'Sub-Línea de Investigación' => $sublinea]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if ($this->proyectos_index_model->verificar_sublinea($id_proyecto, $sublinea) && $this->proyectos_index_model->verificar_sublinea($id_proyecto, $sublinea)->id != $id) {
                        $resp = ['mensaje'=>"Esta Sub-Línea ya existe", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_grupo'            => $grupo,
                            'id_linea'            => $linea,
                            'id_sublinea'         => $sublinea,
                            'id_usuario_modifica' => $id_usuario_modifica
                        ];

                        $modificar_sublinea = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_sublineas', $id);
                        if ($modificar_sublinea == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_sublinea() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_sublineas')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];

                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $eliminar_sublinea = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_sublineas', $id);
                        if ($eliminar_sublinea == -1) {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_ods() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_ods')) {
                    $ods                 = $this->input->post('ods');
                    $id_usuario_registra = $_SESSION['persona'];
    
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'ODS' => $ods]);
    
                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if ($this->proyectos_index_model->verificar_lim_ods($id_proyecto)) {
                        $resp = ['mensaje'=>"Ya has alcanzado el límite de ODS que se pueden agregar", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else if ($this->proyectos_index_model->verificar_ods($id_proyecto, $ods)) {
                        $resp = ['mensaje'=>"Ya has agregado este ODS", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto'         => $id_proyecto,
                            'id_ods'              => $ods
                        ];
    
                        $agregar_ods = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_ods');
                        if ($agregar_ods == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_ods() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_ods')) {
                    $id                  = $this->input->post('id');
                    $ods                 = $this->input->post('ods');
                    $id_usuario_modifica = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'ODS' => $ods]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if ($this->proyectos_index_model->verificar_ods($id_proyecto, $ods) && $this->proyectos_index_model->verificar_ods($id_proyecto, $ods)->id != $id) {
                        $resp = ['mensaje'=>"Ya has agregado este ODS", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_ods' => $ods,
                            'id_usuario_modifica' => $id_usuario_modifica
                        ];

                        $modificar_ods = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_ods', $id);
                        if ($modificar_ods == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_ods() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_ods')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];
    
                    $num = $this->verificar_campos_numericos(['ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
    
                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $eliminar_ods = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_ods', $id);
                        if ($eliminar_ods == -1) {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_objetivo() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_objetivos')) {
                    $tipo_objetivo       = $this->input->post('tipo_objetivo');
                    $descripcion         = $this->input->post('descripcion_objetivo');
                    $id_usuario_registra = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Proyecto' => $id_proyecto, 'Tipo de Objetivo' => $tipo_objetivo, 'Descripción' => $descripcion]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto]);

                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if ($tipo_objetivo == 'general' && $this->proyectos_index_model->verificar_objetivo_general($id_proyecto)) {
                        $resp = ['mensaje'=>"El objetivo general ya existe", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto'         => $id_proyecto,
                            'tipo_objetivo'       => $tipo_objetivo,
                            'descripcion'         => $descripcion
                        ];

                        $agregar_objetivo = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_objetivos');
                        if ($agregar_objetivo == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_objetivo() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_objetivos')) {
                    $id                  = $this->input->post('id');
                    $tipo_objetivo       = $this->input->post('tipo_objetivo');
                    $descripcion         = $this->input->post('descripcion_objetivo');
                    $id_usuario_modifica = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Proyecto' => $id_proyecto, 'Tipo de Objetivo' => $tipo_objetivo, 'Descripción' => $descripcion]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto]);

                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if ($tipo_objetivo == 'general' && ($this->proyectos_index_model->verificar_objetivo_general($id_proyecto) && $this->proyectos_index_model->verificar_objetivo_general($id_proyecto)->id != $id)) {
                        $resp = ['mensaje'=>"El objetivo general ya existe", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'tipo_objetivo'       => $tipo_objetivo,
                            'descripcion'         => $descripcion,
                            'id_usuario_modifica' => $id_usuario_modifica
                        ];

                        $modificar_objetivo = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_objetivos', $id);
                        if ($modificar_objetivo == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_objetivo() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_objetivos')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];

                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $eliminar_objetivo = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_objetivos', $id);
                        if ($eliminar_objetivo == -1) {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_impacto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_impactos')) {
                    $tipo_impacto        = $this->input->post('tipo_impacto');
                    $descripcion         = $this->input->post('descripcion_impacto');
                    $id_usuario_registra = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Proyecto' => $id_proyecto, 'Descripción' => $descripcion]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Tipo de Impacto' => $tipo_impacto]);

                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if ($this->proyectos_index_model->verificar_impacto($id_proyecto, $tipo_impacto)) {
                        $resp = ['mensaje'=>"Ese Impacto y/o Efecto ya existe", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto'         => $id_proyecto,
                            'id_tipo_impacto'     => $tipo_impacto,
                            'descripcion'         => $descripcion
                        ];

                        $agregar_impacto = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_impactos');
                        if ($agregar_impacto == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_impacto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_impactos')) {
                    $id                  = $this->input->post('id');
                    $tipo_impacto        = $this->input->post('tipo_impacto');
                    $descripcion         = $this->input->post('descripcion_impacto');
                    $id_usuario_modifica = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Proyecto' => $id_proyecto, 'Descripción' => $descripcion]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Tipo de Impacto' => $tipo_impacto]);

                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if ($this->proyectos_index_model->verificar_impacto($id_proyecto, $tipo_impacto) && $this->proyectos_index_model->verificar_impacto($id_proyecto, $tipo_impacto)->id != $id) {
                        $resp = ['mensaje'=>"Ese Impacto y/o Efecto ya existe", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_tipo_impacto'     => $tipo_impacto,
                            'descripcion'         => $descripcion,
                            'id_usuario_modifica' => $id_usuario_modifica
                        ];

                        $modificar_impacto = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_impactos', $id);
                        if ($modificar_impacto == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_impacto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_impactos')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];

                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $eliminar_impacto = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_impactos', $id);
                        if ($eliminar_impacto == -1) {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_producto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_productos')) {
                    $participantes       = $this->input->post('participantes');
                    $tipo_producto       = $this->input->post('tipo_producto');
                    $producto            = $this->input->post('producto');
                    $observaciones       = $this->input->post('observaciones');
                    $id_usuario_registra = $_SESSION['persona'];
    
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Tipo de Producto' => $tipo_producto, 'Producto' => $producto]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if ($this->proyectos_index_model->verificar_producto($id_proyecto, $producto)) {
                        $resp = ['mensaje'=>"Ya has agregado este producto", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto'         => $id_proyecto,
                            'id_tipo_producto'    => $tipo_producto,
                            'id_producto'         => $producto,
                            'observaciones'       => $observaciones
                        ];

                        $agregar_producto = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_productos');
                        if ($agregar_producto == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        } else {
                            $producto = $this->proyectos_index_model->traer_ultimo_item($id_proyecto, 'comite_proyectos_productos');
                            $datos_completos = [];
                            foreach ($participantes as $participante) {
                                array_push($datos_completos, [
                                    'id_usuario_registra'  => $id_usuario_registra,
                                    'id_producto_proyecto' => $producto->id,
                                    'id_participante'      => $participante
                                ]);
                            }

                            $agregar_participante = $this->proyectos_index_model->guardar_datos($datos_completos, 'comite_proyectos_productos_participantes', 2);
                            if ($agregar_participante == -1) {
                                $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                            }
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_producto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_productos')) {
                    $id                  = $this->input->post('id');
                    $participantes       = $this->input->post('participantes');
                    $tipo_producto       = $this->input->post('tipo_producto');
                    $producto            = $this->input->post('producto');
                    $observaciones       = $this->input->post('observaciones');
                    $id_usuario_modifica = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Tipo de Producto' => $tipo_producto, 'Producto' => $producto]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if ($this->proyectos_index_model->verificar_producto($id_proyecto, $producto) && $this->proyectos_index_model->verificar_producto($id_proyecto, $producto)->id != $id) {
                        $resp = ['mensaje'=>"Ya has agregado este producto", 'tipo'=>'info', 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_tipo_producto' => $tipo_producto,
                            'id_producto'      => $producto,
                            'observaciones'    => $observaciones,
                            'id_usuario_modifica' => $id_usuario_modifica
                        ];

                        $modificar_producto = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_productos', $id);
                        if ($modificar_producto == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        } else {
                            $participantes_actuales = $this->proyectos_index_model->listar_proyecto_productos_participantes($id_proyecto, $id);
                            $id_participantes_actuales = [];
                            foreach ($participantes_actuales as $participante) array_push($id_participantes_actuales, $participante['id']);
                            sort($participantes);
                            sort($id_participantes_actuales);

                            if ($participantes != $id_participantes_actuales) {
                                $temp = array_diff($participantes, $id_participantes_actuales);
                                $temp2 = array_diff($id_participantes_actuales, $participantes);

                                if (!empty($temp)) {
                                    $datos_agregar = [];
                                    foreach ($temp as $aux) {
                                        array_push($datos_agregar, array(
                                            'id_usuario_registra'  => $id_usuario_modifica,
                                            'id_producto_proyecto' => $id,
                                            'id_participante'      => $aux
                                        ));
                                    }
                                    $this->proyectos_index_model->guardar_datos($datos_agregar, 'comite_proyectos_productos_participantes', 2);
                                }

                                if (!empty($temp2)) {
                                    foreach ($temp2 as $aux) {
                                        $id = null;
                                        foreach ($participantes_actuales as $participante) {
                                            if ($participante['id'] == $aux) {
                                                $id = $participante['id_producto_participante'];
                                                break;
                                            }
                                        }
                                        $datos_eliminar = array(
                                            'estado_registra' => 0,
                                            'id_usuario_elimina' => $id_usuario_modifica
                                        );
                                        $this->proyectos_index_model->modificar_datos($datos_eliminar, 'comite_proyectos_productos_participantes', $id);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_producto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_productos')) {
                    $id          = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id, 'ID del Proyecto' => $id_proyecto]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $participantes_antiguos = $this->proyectos_index_model->listar_proyecto_productos_participantes($id_proyecto, $id);
                        $aux = true;
                        foreach ($participantes_antiguos as $participante) {
                            $eliminar_participante = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_productos_participantes', $participante['id_producto_participante']);
                            if ($eliminar_participante == -1) {
                                $aux = false;
                            }
                        }

                        if ($aux) {
                            $eliminar_producto = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_productos', $id);
                            if ($eliminar_producto == -1) {
                                $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                            }
                        } else {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_cronograma() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_cronograma')) {
                    $participantes       = $this->input->post('participantes');
                    $objetivo_especifico = $this->input->post('objetivo_especifico');
                    $fecha_inicial       = $this->input->post('fecha_inicial_cronograma');
                    $fecha_final         = $this->input->post('fecha_final_cronograma');
                    $actividad           = $this->input->post('actividad');
                    $id_usuario_registra = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Proyecto' => $id_proyecto, 'Fecha Inicial' => $fecha_inicial, 'Fecha Final' => $fecha_final, 'Actividad' => $actividad]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Objetivo Específico' => $objetivo_especifico]);
                    $fecha_i = $this->validateDate($fecha_inicial);
                    $fecha_f = $this->validateDate($fecha_final);

                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if(!$fecha_i || !$fecha_f){
                        $resp = ['mensaje'=> "Por favor seleccione fechas validas y superior a la fecha actual.", 'tipo'=>"info", 'titulo'=> "Oops."];
                    } else if($fecha_final <= $fecha_inicial){
                        $resp = ['mensaje'=> "La fecha de inicio no debe ser superior a la fecha de terminación.", 'tipo'=>"info", 'titulo'=> "Oops."];
                    } else if ($fecha_final > $this->proyectos_index_model->traer_proyecto($id_proyecto)->fecha_final) {
                        $resp = ['mensaje'=>'La fecha final no puede ser superior a la fecha final del proyecto', 'tipo'=>'info', 'titulo'=>'Oops.'];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra'    => $id_usuario_registra,
                            'id_proyecto'            => $id_proyecto,
                            'id_objetivo_especifico' => $objetivo_especifico,
                            'fecha_inicial'          => $fecha_inicial,
                            'fecha_final'            => $fecha_final,
                            'actividad'              => $actividad
                        ];

                        $agregar_cronograma = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_cronogramas');

                        if ($agregar_cronograma == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        } else {
                            $cronograma = $this->proyectos_index_model->traer_ultimo_item($id_proyecto, 'comite_proyectos_cronogramas');
                            $datos_completos = [];
                            foreach ($participantes as $participante) {
                                array_push($datos_completos, [
                                    'id_usuario_registra' => $id_usuario_registra,
                                    'id_cronograma'       => $cronograma->id,
                                    'id_participante'     => $participante
                                ]);
                            }
                            $agregar_participante = $this->proyectos_index_model->guardar_datos($datos_completos, 'comite_proyectos_cronogramas_participantes', 2);
                            if ($agregar_participante == -1) {
                                $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                            }
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_cronograma() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_cronograma')) {
                    $id                  = $this->input->post('id');
                    $participantes       = $this->input->post('participantes');
                    $objetivo_especifico = $this->input->post('objetivo_especifico');
                    $fecha_inicial       = $this->input->post('fecha_inicial_cronograma');
                    $fecha_final         = $this->input->post('fecha_final_cronograma');
                    $actividad           = $this->input->post('actividad');
                    $id_usuario_modifica = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Proyecto' => $id_proyecto, 'Fecha Inicial' => $fecha_inicial, 'Fecha Final' => $fecha_final, 'Actividad' => $actividad]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Objetivo Específico' => $objetivo_especifico]);
                    $fecha_i = $this->validateDate($fecha_inicial);
                    $fecha_f = $this->validateDate($fecha_final);

                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else if(!$fecha_i || !$fecha_f){
                        $resp = ['mensaje'=> "Por favor seleccione fechas validas y superior a la fecha actual.", 'tipo'=>"info", 'titulo'=> "Oops."];
                    } else if($fecha_final <= $fecha_inicial){
                        $resp = ['mensaje'=> "La fecha de inicio no debe ser superior a la fecha de terminación.", 'tipo'=>"info", 'titulo'=> "Oops."];
                    } else if ($fecha_final > $this->proyectos_index_model->traer_proyecto($id_proyecto)->fecha_final) {
                        $resp = ['mensaje'=>'La fecha final no puede ser superior a la fecha final del proyecto', 'tipo'=>'info', 'titulo'=>'Oops.'];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_objetivo_especifico' => $objetivo_especifico,
                            'fecha_inicial'          => $fecha_inicial,
                            'fecha_final'            => $fecha_final,
                            'actividad'              => $actividad,
                            'id_usuario_modifica'    => $id_usuario_modifica
                        ];

                        $modificar_cronograma = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_cronogramas', $id);

                        if ($modificar_cronograma == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        } else {
                            $participantes_actuales = $this->proyectos_index_model->listar_proyecto_cronogramas_participantes($id_proyecto, $id);
                            $id_participantes_actuales = [];
                            foreach ($participantes_actuales as $participante) array_push($id_participantes_actuales, $participante['id']);
                            sort($participantes);
                            sort($id_participantes_actuales);

                            if ($participantes != $id_participantes_actuales) {
                                $temp = array_diff($participantes, $id_participantes_actuales);
                                $temp2 = array_diff($id_participantes_actuales, $participantes);

                                if (!empty($temp)) {
                                    $datos_agregar = [];
                                    foreach ($temp as $aux) {
                                        array_push($datos_agregar, array(
                                            'id_usuario_registra' => $id_usuario_modifica,
                                            'id_cronograma'       => $id,
                                            'id_participante'     => $aux
                                        ));
                                    }
                                    $this->proyectos_index_model->guardar_datos($datos_agregar, 'comite_proyectos_cronogramas_participantes', 2);
                                }

                                if (!empty($temp2)) {
                                    foreach ($temp2 as $aux) {
                                        $id = null;
                                        foreach ($participantes_actuales as $participante) {
                                            if ($participante['id'] == $aux) {
                                                $id = $participante['id_cronograma_participante'];
                                                break;
                                            }
                                        }
                                        $datos_eliminar = array(
                                            'estado_registra' => 0,
                                            'id_usuario_elimina' => $id_usuario_modifica
                                        );
                                        $this->proyectos_index_model->modificar_datos($datos_eliminar, 'comite_proyectos_cronogramas_participantes', $id);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_cronograma() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_cronograma')) {
                    $id                 = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id, 'ID del Proyecto' => $id_proyecto]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $participantes_antiguos = $this->proyectos_index_model->listar_proyecto_cronogramas_participantes($id_proyecto, $id);
                        $aux = true;
                        foreach ($participantes_antiguos as $participante) {
                            $eliminar_participante = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_cronogramas_participantes', $participante['id_cronograma_participante']);
                            if ($eliminar_participante == -1) {
                                $aux = false;
                            }
                        }

                        if ($aux) {
                            $eliminar_cronograma = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_cronogramas', $id);
                            if ($eliminar_cronograma == -1) {
                                $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                            }
                        } else {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_presupuesto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_presupuestos')) {
                    $tipo_presupuesto    = $this->input->post('tipo_presupuesto');
                    $id_usuario_registra = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Tipo de Presupuesto' => $tipo_presupuesto]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto' => $id_proyecto,
                            'id_tipo_presupuesto' => $tipo_presupuesto
                        ];

                        $datos = $this->proyectos_index_model->obtener_valores_permisos($tipo_presupuesto, '177', 1);
                        $agregar = true;
                        foreach ($datos as $dato) {
                            $nombre = str_replace(' ', '_', strtolower($dato['valor']));
                            $valor = $this->input->post($nombre);
                            if ($dato['valory'] == '1') {
                                if ($dato['valorx'] == 'Texto') {
                                    $str = $this->verificar_campos_string([$dato['valor']  => $valor]);
                                    if (is_array($str)) {
                                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                                        $agregar = false;
                                        break;
                                    }
                                } else if ($dato['valorx'] == 'Numerico' || $dato['valorx'] == 'Select') {
                                    if ($dato['id_aux'] == 'Pre_Val_Uni') {
                                        $num = $this->verificar_campos_numericos([$dato['valor'] => $valor], 0);
                                    } else {
                                        $num = $this->verificar_campos_numericos([$dato['valor'] => $valor]);
                                    }

                                    if (is_array($num)) {
                                        $resp = ['mensaje' => "Debe diligenciar el campo {$num['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                                        $agregar = false;
                                        break;
                                    }
                                }
                            }

                            // $aux = true;
                            // if ($dato['valor'] == 'CONSIGNADO A LA CUC' && $this->input->post('tipo') != '11199') {
                            //     $aux = false;
                            // } else if($dato['valor'] == 'GRUPO INVESTIGACION' && $this->input->post('tipo') != '11199') {
                            //     $aux = false;
                            // } else if ($dato['valor'] == 'GRUPO RECEPTOR' && $this->input->post('tipo') != '11199') {
                            //     $aux = false;
                            // }

                            // if ($aux) {
                            //     $num = $this->verificar_campos_numericos([$dato['valor']  => $valor]);
                            //     if (is_array($num)) {
                            //         $resp = ['mensaje' => "Debe diligenciar el campo {$num['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                            //         $agregar = false;
                            //         break;
                            //     }
                            // }
                        }

                        if ($agregar) {
                            $agregar_presupuesto = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_presupuestos');

                            if ($agregar_presupuesto == -1) {
                                $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                            } else {
                                $presupuesto = $this->proyectos_index_model->traer_ultimo_item($id_proyecto, 'comite_proyectos_presupuestos');

                                $datos_completos = [];
                                foreach ($datos as $dato) {
                                    $nombre = str_replace(' ', '_', strtolower($dato['valor']));
                                    $valor = $this->input->post($nombre);
                                    array_push($datos_completos, array(
                                        'id_presupuesto'      => $presupuesto->id,
                                        'id_aux_dato'         => $dato['id_aux'],
                                        'valor'               => $valor,
                                        'nombre_dato'         => $dato['valor'],
                                        'tipo_dato'           => $dato['valorx'],
                                        'dato_requerido'      => $dato['valory'],
                                        'id_datos'            => $dato['valorz'],
                                        'multiplica'          => $dato['valora'],
                                        'id_usuario_registra' => $id_usuario_registra
                                    ));
                                }

                                $agregar_datos = $this->proyectos_index_model->guardar_datos($datos_completos, 'comite_proyectos_presupuestos_datos', 2);
                                if ($agregar_datos == -1) {
                                    $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                                }
                            }
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_presupuesto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_presupuestos')) {
                    $id = $this->input->post('id');
                    $id_usuario_modifica = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $datos = $this->proyectos_index_model->listar_proyecto_presupuestos_datos($id_proyecto, $id);
                        $agregar = true;
                        foreach ($datos as $dato) {
                            $nombre = str_replace(' ', '_', strtolower($dato['nombre_dato']));
                            $valor = $this->input->post($nombre);

                            if ($dato['dato_requerido'] == '1') {
                                if ($dato['tipo_dato'] == 'Texto') {
                                    $str = $this->verificar_campos_string([$dato['nombre_dato']  => $valor]);
                                    if (is_array($str)) {
                                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                                        $agregar = false;
                                        break;
                                    }
                                } else if ($dato['tipo_dato'] == 'Numerico' || $dato['tipo_dato'] == 'Select') {
                                    if ($dato['id_aux'] == 'Pre_Val_Uni') {
                                        $num = $this->verificar_campos_numericos([$dato['nombre_dato'] => $valor], 0);
                                    } else {
                                        $num = $this->verificar_campos_numericos([$dato['nombre_dato'] => $valor]);
                                    }

                                    if (is_array($num)) {
                                        $resp = ['mensaje' => "Debe diligenciar el campo {$num['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                                        $agregar = false;
                                        break;
                                    }
                                }
                            }

                            // $aux = true;
                            // if ($dato['valor'] == 'CONSIGNADO A LA CUC' && $this->input->post('tipo') != '11199') {
                            //     $aux = false;
                            // } else if($dato['valor'] == 'GRUPO INVESTIGACION' && $this->input->post('tipo') != '11199') {
                            //     $aux = false;
                            // } else if ($dato['valor'] == 'GRUPO RECEPTOR' && $this->input->post('tipo') != '11199') {
                            //     $aux = false;
                            // }

                            // if ($aux) {
                            //     $num = $this->verificar_campos_numericos([$dato['valor']  => $valor]);
                            //     if (is_array($num)) {
                            //         $resp = ['mensaje' => "Debe diligenciar el campo {$num['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                            //         $agregar = false;
                            //         break;
                            //     }
                            // }
                        }

                        if ($agregar) {
                            foreach ($datos as $dato ) {
                                $nombre = str_replace(' ', '_', strtolower($dato['nombre_dato']));
                                $valor = $this->input->post($nombre);

                                $data = [
                                    'valor' => $valor,
                                    'id_usuario_modifica' => $id_usuario_modifica
                                ];

                                $modificar_presupuesto = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_presupuestos_datos', $dato['id']);
                                if ($modificar_presupuesto == -1) {
                                    $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                                }
                            }
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_presupuesto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_presupuestos')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $datos = $this->proyectos_index_model->listar_proyecto_presupuestos_datos($id_proyecto, $id);
                        $aux = true;
                        foreach ($datos as $dato) {
                            $eliminar_dato = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_presupuestos_datos', $dato['id']);
                            if ($eliminar_dato == -1) {
                                $aux = false;
                            }
                        }

                        if ($aux) {
                            $eliminar_presupuesto = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_presupuestos', $id);
                            if ($eliminar_presupuesto == -1) {
                                $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops!'];
                            }
                        } else {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function recibir_archivos(){
        $id_proyecto = $_POST['id_proyecto'];
        $estado_proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto)->id_estado_proyecto;
        if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_soportes')) {
            $nombre = $_FILES["file"]["name"];
            $cargo = $this->cargar_archivo_soporte("file", $this->ruta_archivos_proyectos, "soporte_");
            if ($cargo[0] == -1) {
                header("HTTP/1.0 400 Bad Request");
                echo ($nombre);
                return;
            }
            $data = [
                'id_proyecto'         => $id_proyecto,
                'nombre_real'         => $nombre,
                'nombre_guardado'     => $cargo[1],
                'id_usuario_registra' => $_SESSION['persona']
            ];
            $res = $this->pages_model->guardar_datos($data, 'comite_proyectos_soportes');
            if ($res == -1) {
                header("HTTP/1.0 400 Bad Request");
                echo ($nombre);
                return;
            }
        }else{
            header("HTTP/1.0 400 Bad Request");
            echo ("Este proyecto no tiene permitido ser modificado");
            return;
        }
        echo json_encode($res);
        return;
    }

    function cargar_archivo_soporte($mi_archivo, $ruta, $nombre){
        $nombre .= uniqid();
        $real_path = realpath(APPPATH . '../' . $ruta);
        $config['upload_path'] = $real_path;
        $config['file_name'] = $nombre;
        $config['allowed_types'] = "*";
        $config['max_size'] = "0";
        $config['max_width'] = "0";
        $config['max_height'] = "0";
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload($mi_archivo)) {
            $data['uploadError'] = $this->upload->display_errors();
            return array(-1, $data['uploadError']);
        }
        $data['uploadSuccess'] = $this->upload->data();
        return array(1, $data['uploadSuccess']["file_name"]);
    }

    public function eliminar_soporte() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_soportes')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];
    
                    $num = $this->verificar_campos_numericos(['ID' => $id]);
    
                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];
    
                        $eliminar_soporte = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_soportes', $id);
                        if ($eliminar_soporte == -1) {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }
        }
        echo json_encode($resp);
    }

    public function guardar_bibliografia() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_bibliografias')) {
                    $bibliografia        = $this->input->post('bibliografia');
                    $id_usuario_registra = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Proyecto' => $id_proyecto, 'Bibliografía' => $bibliografia]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto]);

                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto'         => $id_proyecto,
                            'bibliografia'        => $bibliografia
                        ];

                        $agregar_bibliografia = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_bibliografias');

                        if ($agregar_bibliografia == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para guardar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function modificar_bibliografia() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_bibliografias')) {
                    $id                  = $this->input->post('id');
                    $bibliografia        = $this->input->post('bibliografia');
                    $id_usuario_modifica = $_SESSION['persona'];

                    $str = $this->verificar_campos_string(['Proyecto' => $id_proyecto, 'Bibliografía' => $bibliografia]);
                    $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto]);

                    if (is_array($str)) {
                        $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => 'info', 'titulo'=>'Oops!'];
                    } else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'bibliografia' => $bibliografia,
                            'id_usuario_modifica' => $id_usuario_modifica
                        ];

                        $modificar_bibliografia = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_bibliografias', $id);

                        if ($modificar_bibliografia == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para modificar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function eliminar_bibliografia() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_elimina == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                if ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_bibliografias')) {
                    $id = $this->input->post('id');
                    $id_usuario_elimina = $_SESSION['persona'];

                    $num = $this->verificar_campos_numericos(['ID' => $id]);

                    if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                    } else {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                        $data = [
                            'estado_registra' => 0,
                            'id_usuario_elimina' => $id_usuario_elimina
                        ];

                        $eliminar_bibliografia = $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_bibliografias', $id);
                        if ($eliminar_bibliografia == -1) {
                            $resp = ['mensaje' => 'Error al eliminar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops!'];
                        }
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permiso para eliminar este registro', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }

        }
        echo json_encode($resp);
    }

    public function guardar_solicitud_proyecto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id_proyecto = $this->input->post('id_proyecto');
                $motivos_solicitud   = $this->input->post('motivos_enviar');
                $id_usuario_registra = $_SESSION['persona'];

                $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto]);

                if(is_array($num)){
                    $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                } else {
                    $grupo_solicitud = $this->proyectos_index_model->ultimo_grupo_solicitud($id_proyecto) + 1;
                    $data = [];
                    foreach ($motivos_solicitud as $motivo) {
                        $temp = array(
                            'id_usuario_registra' => $id_usuario_registra,
                            'id_proyecto'         => $id_proyecto,
                            'grupo_solicitud'     => $grupo_solicitud,
                            'id_item'             => $motivo['id'],
                            'razones'             => $motivo['razones']
                        );

                        array_push($data, $temp);
                    }

                    $agregar_solicitudes = $this->proyectos_index_model->guardar_datos($data, 'comite_proyectos_solicitudes', 2);

                    if ($agregar_solicitudes != -1) {
                        $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];

                        $data_estado = array(
                            "id_usuario_registro" => $id_usuario_registra,
                            "id_proyecto"         => $id_proyecto,
                            "id_tipo"             => 'Proy_Sol',
                            "observaciones"       => $grupo_solicitud
                        );

                        $agregar_log = $this->proyectos_index_model->guardar_datos($data_estado, 'accion_proyectos_personas');
                        if ($agregar_log == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    } else {
                        $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                    }
                }
            }

        }
        echo json_encode($resp);
    }

    public function aprobar_negar_solicitud_proyecto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $administra = $_SESSION['perfil'] == 'Per_Admin' || $_SESSION['perfil'] == 'Per_Adm_index' || $_SESSION['perfil'] == 'Per_Adm_Proy';

                if ($administra) {
                    $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
    
                    $id_proyecto         = $this->input->post('id_proyecto');
                    $motivos_solicitud   = $this->input->post('motivos_solicitud');
                    $id_usuario_registra = $_SESSION['persona'];
                    $estado              = 'Proy_Sol_Neg';
                    $grupo_solicitud     = $motivos_solicitud[0]['grupo_solicitud'];
    
                    foreach ($motivos_solicitud as $motivo) {
                        if ($motivo['aprobado']) $estado = 'Proy_Sol_Apr';
                        $data = array(
                            'aprobado'     => $motivo['aprobado'],
                            'fecha_limite' => ($motivo['aprobado']) ? $motivo['fecha_limite'] : null
                        );
    
                        $this->proyectos_index_model->modificar_datos($data, 'comite_proyectos_solicitudes', $motivo['id']);
                    }
    
                    $data_log = array(
                        "id_usuario_registro" => $id_usuario_registra,
                        "id_proyecto"         => $id_proyecto,
                        "id_tipo"             => $estado,
                        "observaciones"       => $grupo_solicitud
                    );
    
                    $estados = $this->proyectos_index_model->listar_estados_proyecto($id_proyecto);
                    $id_estado = end($estados)['id'];
                    $data_visto = array('visto' => 1);
    
                    $guardar_log = $this->proyectos_index_model->guardar_datos($data_log, 'accion_proyectos_personas');
                    if ($guardar_log != -1) {
                        $guardar_visto = $this->proyectos_index_model->modificar_datos($data_visto, 'accion_proyectos_personas', $id_estado);
                        if ($guardar_visto == -1) {
                            $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                        }
                    } else {
                        $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                    }
                } else {
                    $resp = ['mensaje' => 'No tienes permitido realizar esta acción', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_proyectos_usuario() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $estado = $this->input->post('estado');
            $codigo_proyecto = $this->input->post('codigo_proyecto');
            $proyectos = $this->proyectos_index_model->listar_proyectos_usuario($_SESSION['persona'], $id, $tipo, $estado, $codigo_proyecto);
            $administra = $_SESSION['perfil'] == 'Per_Admin';
            $persona = $_SESSION['persona'];

            $modificar = '<span class="fa fa-pencil-square-o pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $revision = '<span class="fa fa-send pointer btn btn-default enviar" title="Enviar a Revisión" style="color: #EABD32; margin: 0 1px;"></span>';
            $reanudar = '<span class="fa fa-book pointer btn btn-default reanudar" title="Retomar Proyecto" style="color: #6e1f7c; margin: 0 1px;"></span>';
            $banco = '<span class="fa fa-archive pointer btn btn-default banco" title="Enviar a Banco de Proyecto" style="color: rgb(87, 55, 7); margin: 0 1px;"></span>';
            $aceptar = '<span class="fa fa-check pointer btn btn-default aceptar" title="Aceptar Proyecto" style="color: #39B23B; margin: 0 1px;"></span>';
            $agregar_comite = '<span class="fa fa-plus-circle pointer btn btn-default comite" title="Agregar al comité más reciente" style="color: #d57e1c; margin: 0 1px;"></span>';
            $quitar_comite = '<span class="fa fa-minus-circle pointer btn btn-default quitar" title="Quitar de Comité el proyecto" style="color: #6e1f7c; margin: 0 1px;"></span>';
            $devolver = '<span class="fa fa-reply pointer btn btn-default devolver" title="Devolver Proyecto" style="color: #2E79E5; margin: 0 1px;"></span>';
            $rechazar = '<span class="glyphicon glyphicon-ban-circle pointer btn btn-default rechazar" title="Rechazar el Proyecto" style="color: #CA3E33; margin: 0 1px;"></span>';
            $cancelar = '<span class="glyphicon glyphicon-remove pointer btn btn-default cancelar" title="Cancelar el Proyecto" style="color: #CA3E33; margin: 0 1px;"></span>';
            $solicitud_correccion = '<span class="fa fa-refresh pointer btn btn-default solicitud_correccion" title="Solicitar corrección del proyecto" style="color: #2E79E5; margin: 0 1px;"></span>';
            $ver_solicitudes = '<span class="fa fa-eye pointer btn btn-default ver_solicitudes" title="Ver solicitudes del proyecto" style="color: #2E79E5; margin: 0 1px;"></span>';
            $btn_cerrada = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
            $btn_abierta = '<span title="Proyecto en espera..." data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half" style="color:#428bca"></span>';
            $back = 'white';
            $color = 'black';
            foreach ($proyectos as $proyecto) {
                $solicitante = $proyecto["investigador"];
                $gestion = $proyecto['gestion'];
                $estado = $proyecto['id_estado_proyecto'];
                if ($estado == 'Proy_For') {
                    $back = 'white';
                    $color = 'black';
                    $proyecto['acciones'] = ($persona == $solicitante || $administra || $gestion) ? "$modificar $revision $banco $cancelar" : $btn_cerrada;
                } else if ($estado == 'Proy_Rev') {
                    $back = '#EABD32';
                    $color = 'white';
                    $proyecto['acciones'] = $administra || $gestion ? "$aceptar $devolver $rechazar" : $btn_abierta;
                } else if ($estado == 'Proy_Ban') {
                    $back = 'rgb(87, 55, 7)';
                    $color = 'white';
                    $proyecto['acciones'] = $administra || $persona == $solicitante || $gestion ? "$reanudar" : $btn_cerrada;
                } else if ($estado == 'Proy_Acp') {
                    $back = '#39B23B';
                    $color = 'white';
                    $proyecto['acciones'] = $administra || $gestion ? "$agregar_comite $devolver $rechazar" : $btn_abierta;
                } else if ($estado == 'Proy_Reg') {
                    $back = '#2E79E5';
                    $color = 'white';
                    $comite = $this->proyectos_index_model->listar_comites($proyecto['id_comite'])[0];
                    $estado_actual_com = $comite['id_estado_comite'];
                    $proyecto['acciones'] = (($administra || $gestion) && ($estado_actual_com == 'Com_Ini')) ? "$quitar_comite" : $btn_abierta;
                } else if ($estado == 'Proy_Neg' || $estado == 'Proy_Can' || $estado == 'Proy_Rec') {
                    $back = '#d9534f';
                    $color = 'white';
                    $proyecto['acciones'] = $btn_cerrada;
                } else if ($estado == 'Proy_Apr') {
                    $back = '#39B23B';
                    $color = 'white';
                    if ($proyecto['solicitudes'] == 0) {
                        $proyecto['acciones'] = ($persona == $solicitante || $administra || $gestion) ? $solicitud_correccion : $btn_cerrada;
                    } else {
                        if ($gestion || $administra) {
                            $proyecto['acciones'] = $ver_solicitudes;
                        } else if ($persona == $solicitante) {
                            $proyecto['acciones'] = $btn_abierta;
                        } else {
                            $proyecto['acciones'] = $btn_cerrada;
                        }
                    }
                }
                $proyecto['ver'] = "<span style='background-color: $back; color: $color; width: 100%' class='pointer form-control ver' title='Ver el proyecto'><span>ver</span></span>";
                if ($proyecto['solicitudes_aprobadas'] > 0 && ($persona == $solicitante || $administra)) $proyecto['acciones'] = $modificar;

                array_push($resp, $proyecto);
            }
        }
        echo json_encode($resp);
    }

    public function traer_informacion_proyecto () {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id = $this->input->post('id');
            $resp = $this->proyectos_index_model->traer_proyecto($id);
        }
        echo json_encode($resp);
    }

    public function mostrar_notificaciones_proyectos() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_usuario = $_SESSION['persona'];
            $resp = $this->proyectos_index_model->mostrar_notificaciones_proyectos($id_usuario);
        }
        echo json_encode($resp);
    }

    public function mostrar_notificaciones_solicitudes() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $perfil = $_SESSION['perfil'] == 'Per_Adm_index' || $_SESSION['perfil'] == 'Per_Adm_Proy' || $_SESSION['perfil'] == 'Per_Admin';
            $notificaciones = $this->proyectos_index_model->mostrar_notificaciones_solicitudes();
            if ($perfil) {
                foreach ($notificaciones as $notificacion) {
                    $notificacion['items'] = $this->proyectos_index_model->traer_grupo_solicitudes($notificacion['id_proyecto'], (int) $notificacion['observaciones']);
                    array_push($resp, $notificacion);
                }
            }
        }
        echo json_encode($resp);
    }

    public function mostrar_notificaciones_solicitudes_respuestas() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_usuario = $_SESSION['persona'];
            $notificaciones = $this->proyectos_index_model->mostrar_notificaciones_solicitudes(2, $id_usuario);
            foreach ($notificaciones as $notificacion) {
                $notificacion['items'] = $this->proyectos_index_model->traer_grupo_solicitudes($notificacion['id_proyecto'], (int) $notificacion['observaciones']);
                array_push($resp, $notificacion);
            }
        }
        echo json_encode($resp);
    }

    public function marcar_visto() {
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!'];
            } else {
                $id = $this->input->post('id');

                $num = $this->verificar_campos_numericos(['ID' => $id]);

                if(is_array($num)){
                    $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops!"];
                } else {
                    $resp = ['mensaje' => 'Información almacenada con exito', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
                    $data = [
                        'visto' => 1
                    ];

                    $modificar_log = $this->proyectos_index_model->modificar_datos($data, 'accion_proyectos_personas', $id);

                    if ($modificar_log == -1) {
                        $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                    }
                }
            }
            
        }
        echo json_encode($resp);
    }

    public function traer_datos_convenio_proceedings() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $resp = $this->proyectos_index_model->traer_datos_convenio_proceedings($id_proyecto);
        }
        echo json_encode($resp);
    }

    public function guardar_datos_parametros_generales() {
        if (!$this->Super_estado == true) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            if ($this->Super_agrega == 0 && $_SESSION['perfil'] != 'Per_Admin' && $_SESSION['perfil'] != 'Per_Adm_index' && $_SESSION['perfil'] != 'Per_Adm_Proy') {
                $resp = ['mensaje'=>'No Tiene Permisos Para Realizar Esta Operación.', 'tipo'=>'error', 'titulo'=>'Oops!', 'perfil' => $_SESSION['perfil']];
            } else {
                $resp = ['mensaje'=>'Información almacenada con éxito.', 'tipo'=>'success', 'titulo'=>'Proceso exitoso!'];
                $iva = $this->input->post('iva');

                $guardar_iva = $this->proyectos_index_model->modificar_valor_parametro(['valor' => $iva], 'Por_Iva');
                if ($guardar_iva == -1) {
                    $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
                }
            }
        }
        echo json_encode($resp);
    }

    public function traer_datos_parametros_generales() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $datos = $this->proyectos_index_model->traer_datos_parametros_generales();
            $iva = null;
            foreach ($datos as $dato) {
                if ($dato['id_aux'] == 'Por_Iva') $iva = $dato['valor'];
            }
            $resp = array(
                'iva' => $iva
            );
        }
        echo json_encode($resp);
    }

    public function listar_instituciones_bdd() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $tipo = $this->input->post('tipo');
            $instituciones = $this->proyectos_index_model->listar_instituciones_bdd($tipo);

            if ($tipo == null) {
                $modificar_institucion = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
                $eliminar_institucion = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';

                foreach ($instituciones as $institucion) {
                    $institucion['acciones'] = $modificar_institucion;
                    $institucion['acciones'] .= $eliminar_institucion;

                    array_push($resp, $institucion);
                }
            } else {
                $resp = $instituciones;
            }
        }
        echo json_encode($resp);
    }

    public function listar_participantes() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $participantes = $this->proyectos_index_model->listar_proyecto_participantes($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $ver_participante = '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver el participante"><span>ver</span></span>';
            $modificar_participante = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar_participante = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar_participante $eliminar_participante";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($participantes as $participante) {
                if ($tipo != 1) {
                    $participante['ver'] = $ver_participante;
                    if ($participante['id_persona'] == $proyecto->investigador && $participante['tipo_tabla'] == 1) {
                        $participante['acciones'] = $sin_acciones;
                    } else {
                        $participante['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_participantes')) ? $acciones : $sin_acciones;
                    }
                }
                array_push($resp, $participante);
            }
        }
        echo json_encode($resp);
    }

    public function traer_informacion_participante() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id = $this->input->post('id');
            $tipo_tabla = $this->input->post('tipo_tabla');
            $resp = $this->proyectos_index_model->traer_informacion_participante($id, $tipo_tabla);
        }
        echo json_encode($resp);
    }

    public function listar_lugares() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $lugares = $this->proyectos_index_model->listar_proyecto_lugares($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $modificar_lugar = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar_lugar = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar_lugar $eliminar_lugar";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($lugares as $lugar) {
                if ($tipo != 1) {
                    $lugar['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_lugares')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $lugar);
            }
        }
        echo json_encode($resp);
    }

    public function listar_instituciones() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $instituciones = $this->proyectos_index_model->listar_proyecto_instituciones($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $ver_institucion = '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver la institución"><span>ver</span></span>';
            $modificar_institucion = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar_institucion = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar_institucion $eliminar_institucion";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($instituciones as $institucion) {
                if ($tipo != 1) {
                    $institucion['ver'] = $ver_institucion;
                    $institucion['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_instituciones')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $institucion);
            }
        }
        echo json_encode($resp);
    }

    public function listar_programas() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $programas = $this->proyectos_index_model->listar_proyecto_programas($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $modificar = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar $eliminar";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($programas as $programa) {
                if ($tipo != 1) {
                    $programa['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_programas')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $programa);
            }
        }
        echo json_encode($resp);
    }

    public function listar_asignaturas() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $asignaturas = $this->proyectos_index_model->listar_proyecto_asignaturas($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $ver = '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver la asignatura"><span>ver</span></span>';            
            $modificar = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar $eliminar";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($asignaturas as $asignatura) {
                $asignatura['ver'] = $ver;
                if ($tipo != 1) {
                    $asignatura['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_asignaturas')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $asignatura);
            }
        }
        echo json_encode($resp);
    }

    public function listar_sublineas() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $sublineas = $this->proyectos_index_model->listar_proyecto_sublineas($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $modificar_sublinea = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar_sublinea = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar_sublinea $eliminar_sublinea";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($sublineas as $sublinea) {
                if ($tipo != 1) {
                    $sublinea['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_sublineas')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $sublinea);
            }
        }
        echo json_encode($resp);
    }

    public function listar_ods() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $ods_total = $this->proyectos_index_model->listar_proyecto_ods($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $modificar_ods = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar_ods = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar_ods $eliminar_ods";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($ods_total as $ods) {
                if ($tipo != 1) {
                    $ods['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_ods')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $ods);
            }
        }
        echo json_encode($resp);
    }

    public function listar_objetivos() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $objetivos = $this->proyectos_index_model->listar_proyecto_objetivos($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $ver_objetivo = '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver el objetivo"><span>ver</span></span>';
            $modificar_objetivo = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar_objetivo = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar_objetivo $eliminar_objetivo";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($objetivos as $objetivo) {
                if ($tipo != 1) {
                    $objetivo['ver'] = $ver_objetivo;
                    $objetivo['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_objetivos')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $objetivo);
            }
        }
        echo json_encode($resp);
    }

    public function listar_impactos() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $impactos = $this->proyectos_index_model->listar_proyecto_impactos($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $ver_impacto = '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver el impacto"><span>ver</span></span>';
            $modificar_impacto = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar_impacto = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar_impacto $eliminar_impacto";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($impactos as $impacto) {
                if ($tipo != 1) {
                    $impacto['ver'] = $ver_impacto;
                    $impacto['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_impactos')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $impacto);
            }
        }
        echo json_encode($resp);
    }

    public function listar_productos() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $productos = $this->proyectos_index_model->listar_proyecto_productos($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $ver_producto = '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver el producto"><span>ver</span></span>';
            $modificar_producto = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar_producto = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar_producto $eliminar_producto";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($productos as $producto) {
                $producto['participantes'] = $this->proyectos_index_model->listar_proyecto_productos_participantes($id_proyecto, $producto['id']);
                if ($tipo != 1) {
                    $producto['ver'] = $ver_producto;
                    $producto['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_productos')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $producto);
            }
        }
        echo json_encode($resp);
    }

    public function listar_cronogramas() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $cronogramas = $this->proyectos_index_model->listar_proyecto_cronogramas($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $ver_cronograma = '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver el cronograma"><span>ver</span></span>';
            $modificar_cronograma = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar_cronograma = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar_cronograma $eliminar_cronograma";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($cronogramas as $cronograma) {
                $cronograma['participantes'] = $this->proyectos_index_model->listar_proyecto_cronogramas_participantes($id_proyecto, $cronograma['id']);
                if ($tipo != 1) {
                    $cronograma['ver'] = $ver_cronograma;
                    $cronograma['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_cronograma')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $cronograma);
            }
        }
        echo json_encode($resp);
    }

    public function listar_resumen_presupuestos() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $id_tipo_proyecto = $this->input->post('id_tipo_proyecto');
            // Se trae todos los presupuestos asociados al tipo de proyecto
            $valorparametro = $this->proyectos_index_model->traer_valor_parametro($id_tipo_proyecto);
            $order = $valorparametro->{'id_aux'} == 'Pro_Int' ? true : false;            
            $presupuestos = $this->proyectos_index_model->obtener_valores_permisos($id_tipo_proyecto, 176, 1, $order);
            // Se trae todos los datos de presupuestos que hayan sido digitados del proyecto
            $datos = $this->proyectos_index_model->listar_proyecto_presupuestos($id_proyecto);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $ver_presupuesto = '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver el presupuesto"><span>ver</span></span>';
            $agregar_presupuesto = '<span class="fa fa-plus pointer btn btn-default agregar" title="Agregar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($presupuestos as $presupuesto) {
                $total_efectivo = 0;
                $total_especie = 0;
                foreach ($datos as $dato) {
                    if (strtoupper($dato['id_tipo_presupuesto']) == $presupuesto['id']) {
                        if ($dato['id_tipo_valor'] == 'Pre_Efec') {
                            $total_efectivo += $dato['valor_total'];
                        } else {
                            $total_especie += $dato['valor_total'];
                        }
                    }
                }
                $temp = $this->verificar_guardar_item($id_proyecto, 'mod_proyecto_presupuestos');
                array_push($resp, array(
                    'id'       => $presupuesto['id'],
                    'ver'      => $ver_presupuesto,
                    'rubro'    => $presupuesto['valor'],
                    'efectivo' => '$ ' . $this->convertir_moneda($total_efectivo, true, 0),
                    'especie'  => '$ ' . $this->convertir_moneda($total_especie, true, 0),
                    'acciones' => ($temp) ? $agregar_presupuesto : $sin_acciones
                ));
            }
        }
        echo json_encode($resp);
    }

    public function listar_presupuesto_discriminado_entidad_rubro() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $presupuestos = $this->proyectos_index_model->listar_presupuesto_discriminado($id_proyecto, 2);
            $datos = $this->proyectos_index_model->listar_presupuesto_discriminado($id_proyecto);

            foreach ($presupuestos as $presupuesto) {
                $total_efectivo = 0;
                $total_especie = 0;
                foreach ($datos as $dato) {
                    if ($presupuesto['entidad_responsable'] == $dato['entidad_responsable'] && $presupuesto['rubro'] == $dato['rubro']) {
                        if ($dato['tipo_valor'] == 'Pre_Efec') {
                            $total_efectivo += $dato['valor_total'];
                        } else {
                            $total_especie += $dato['valor_total'];
                        }
                    }
                }
                array_push($resp, array(
                    'entidad_responsable' => $presupuesto['entidad_responsable'],
                    'rubro'               => $presupuesto['rubro'],
                    'efectivo'            => '$ ' . $this->convertir_moneda($total_efectivo, true, 0),
                    'especie'             => '$ ' . $this->convertir_moneda($total_especie, true, 0),
                    'total'               => '$ ' . $this->convertir_moneda($total_especie + $total_efectivo, true, 0)
                ));
            }
        }
        echo json_encode($resp);
    }

    public function listar_presupuesto_discriminado_entidad() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $presupuestos = $this->proyectos_index_model->listar_presupuesto_discriminado($id_proyecto, 1);
            $datos = $this->proyectos_index_model->listar_presupuesto_discriminado($id_proyecto);

            $total = 0;
            foreach ($datos as $dato) {
                $total += $dato['valor_total'];
            }
            foreach ($presupuestos as $presupuesto) {
                $total_efectivo = 0;
                $total_especie = 0;
                foreach ($datos as $dato) {
                    if ($presupuesto['entidad_responsable'] == $dato['entidad_responsable']) {
                        if ($dato['tipo_valor'] == 'Pre_Efec') {
                            $total_efectivo += $dato['valor_total'];
                        } else {
                            $total_especie += $dato['valor_total'];
                        }
                    }
                }
                array_push($resp, array(
                    'entidad_responsable' => $presupuesto['entidad_responsable'],
                    'efectivo'            => '$ ' . $this->convertir_moneda($total_efectivo, true, 0),
                    'especie'             => '$ ' . $this->convertir_moneda($total_especie, true, 0),
                    'total'               => '$ ' . $this->convertir_moneda($total_especie + $total_efectivo, true, 0),
                    'porcentaje'          => round((($total_especie + $total_efectivo) * 100) / $total, 3) . ' %'
                ));
            }
        }
        echo json_encode($resp);
    }

    public function listar_presupuesto_discriminado_financiacion() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $presupuestos = $this->proyectos_index_model->listar_presupuesto_discriminado_financiacion($id_proyecto);
            $internacionales = $this->genericas_model->obtener_valores_parametro(205);
            $nacionales = $this->genericas_model->obtener_valores_parametro(206);
            $financiacion_internacional = [];
            $financiacion_nacional = [];

            foreach ($internacionales as $internacional) {
                $total_efectivo = 0;
                $total_especie = 0;
                foreach ($presupuestos as $presupuesto) {
                    if ($internacional['valor'] == $presupuesto['financiacion']) {
                        if ($presupuesto['tipo_valor'] == 'Pre_Efec') {
                            $total_efectivo += $presupuesto['valor_total'];
                        } else {
                            $total_especie += $presupuesto['valor_total'];
                        }
                    }
                }
                array_push($financiacion_internacional, array(
                    'financiacion' => $internacional['valor'],
                    'efectivo'     => '$ ' . $this->convertir_moneda($total_efectivo, true, 0),
                    'especie'      => '$ ' . $this->convertir_moneda($total_especie, true, 0),
                    'total'        => '$ ' . $this->convertir_moneda($total_especie + $total_efectivo, true, 0)
                ));
            }

            foreach ($nacionales as $nacional) {
                $total_efectivo = 0;
                $total_especie = 0;
                foreach ($presupuestos as $presupuesto) {
                    if ($nacional['valor'] == $presupuesto['financiacion']) {
                        if ($presupuesto['tipo_valor'] == 'Pre_Efec') {
                            $total_efectivo += $presupuesto['valor_total'];
                        } else {
                            $total_especie += $presupuesto['valor_total'];
                        }
                    }
                }
                array_push($financiacion_nacional, array(
                    'financiacion' => $nacional['valor'],
                    'efectivo'     => '$ ' . $this->convertir_moneda($total_efectivo, true, 0),
                    'especie'      => '$ ' . $this->convertir_moneda($total_especie, true, 0),
                    'total'        => '$ ' . $this->convertir_moneda($total_especie + $total_efectivo, true, 0)
                ));
            }
        }
        $resp = array(
            'financiacion_internacional' => $financiacion_internacional,
            'financiacion_nacional'      => $financiacion_nacional
        );
        echo json_encode($resp);
    }

    public function listar_presupuestos() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id_presupuesto = $this->input->post('id_presupuesto');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $presupuestos = $this->proyectos_index_model->listar_proyecto_presupuestos($id_proyecto, $id_presupuesto);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $ver_presupuesto = '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver el presupuesto"><span>ver</span></span>';
            $modificar_presupuesto = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar_presupuesto = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar_presupuesto $eliminar_presupuesto";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($presupuestos as $presupuesto) {
                $presupuesto['ver'] = $ver_presupuesto;
                $presupuesto['valor_unitario_convertido'] = '$ ' . $this->convertir_moneda($presupuesto['valor_unitario'], true, 0);
                $presupuesto['valor_total']               = '$ ' . $this->convertir_moneda($presupuesto['valor_total'], true, 0);
                $presupuesto['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_presupuestos')) ? $acciones : $sin_acciones;

                array_push($resp, $presupuesto);
            }
        }
        echo json_encode($resp);
    }

    public function traer_presupuesto() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id_presupuesto = $this->input->post('id_presupuesto');
            $tipo = $this->input->post('tipo');

            $datos = $this->proyectos_index_model->listar_proyecto_presupuestos_datos($id_proyecto, $id_presupuesto, $tipo);

            foreach ($datos as $dato) {
                if ($dato['multiplica'] == 1) {
                    $dato['valor'] = '$ ' . $this->convertir_moneda($dato['valor'], true, 0);
                }
                if ($dato['id_datos'] == 'Pre_Inv') {
                    $dato['valor_select'] = $this->proyectos_index_model->traer_participante_id($id_proyecto, $dato['valor'])->nombre_completo;
                }
                array_push($resp, $dato);
            }
        }
        echo json_encode($resp);
    }

    public function listar_soportes() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $soportes = $this->proyectos_index_model->listar_proyecto_soportes($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $eliminar_soporte = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$eliminar_soporte";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($soportes as $soporte) {
                if ($tipo != 1) {
                    $soporte['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_soportes')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $soporte);
            }
        }
        echo json_encode($resp);
    }

    public function listar_bibliografias() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $id = $this->input->post('id');
            $tipo = $this->input->post('tipo');
            $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
            $bibliografias = $this->proyectos_index_model->listar_proyecto_bibliografias($id_proyecto, $tipo, $id);
            $persona = $_SESSION['persona'];
            $administra = $_SESSION['perfil'] == 'Per_Admin';

            $ver_bibliografia = '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" title="Ver la bibliografía"><span>ver</span></span>';
            $modificar_bibliografia = '<span class="fa fa-wrench pointer btn btn-default modificar" title="Modificar" style="color: #2E79E5; margin: 0 1px;"></span>';
            $eliminar_bibliografia = '<span class="fa fa-trash-o pointer btn btn-default eliminar" title="Eliminar" style="color: #cc0000; margin: 0 1px;"></span>';
            $acciones = "$modificar_bibliografia $eliminar_bibliografia";
            $sin_acciones = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            foreach ($bibliografias as $bibliografia) {
                if ($tipo != 1) {
                    $bibliografia['ver'] = $ver_bibliografia;
                    $bibliografia['acciones'] = ($this->verificar_guardar_item($id_proyecto, 'mod_proyecto_bibliografias')) ? $acciones : $sin_acciones;
                }

                array_push($resp, $bibliografia);
            }
        }
        echo json_encode($resp);
    }

    public function listar_motivos_solicitud() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');
            $tipo = $this->input->post('tipo');

            $items = $this->proyectos_index_model->listar_motivos_solicitud($id_proyecto, $tipo);
            $aprobar = '<span style="color: rgb(57, 178, 59);" title="Aprobar" data-toggle="popover" data-trigger="hover" class="btn btn-default aprobar fa fa-check"></span>';
            $negar = '<span style="color: #CA3E33;" title="Negar" data-toggle="popover" data-trigger="hover" class="btn btn-default negar fa fa-times"></span>';

            foreach ($items as $item) {
                $item['acciones'] = "$aprobar $negar";

                array_push($resp, $item);
            }
        }
        echo json_encode($resp);
    }

    public function listar_items_motivos() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');

            $items = $this->proyectos_index_model->listar_items_motivos($id_proyecto);
            $seleccionar = '<span style="color: rgb(57, 178, 59);" title="Seleccionar Ítem" data-toggle="popover" data-trigger="hover" class="btn btn-default seleccionar fa fa-toggle-off"></span>';

            foreach ($items as $item) {
                $item['acciones'] = $seleccionar;

                array_push($resp, $item);
            }
        }
        echo json_encode($resp);
    }

    public function listar_cambios() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $id_proyecto = $this->input->post('id_proyecto');

            $resp = $this->proyectos_index_model->listar_cambios_proyecto($id_proyecto);
        }
        echo json_encode($resp);
    }

    public function descargar_proyecto_index($id) {
        $data = [];
        $pages = "sin_session";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $render = false;
        $permiso = true;

        if ($this->Super_estado) {
            $proyecto = $this->proyectos_index_model->informacion_completa_proyecto($id);
            if ($permiso || $_SESSION['perfil'] == 'Per_Admin' || $_SESSION['perfil'] == 'Per_Adm_Proy' || $_SESSION['perfil'] == 'Per_Adm_index' || $_SESSION["persona"] == $proyecto->investigador) {
                $render = true;
                $pages = "descargar_proyecto_index";
                $data['actividad'] = 'proyectos_index';
                $data['js'] ="Proyectos_index";
                $data['permiso'] = $permiso;
                $data['datos'] = $proyecto;
            }
        }
        if ($render) $this->load->view("pages/".$pages,$data);
        else{
            $this->load->view('templates/header',$data);
            $this->load->view("pages/".$pages);
            $this->load->view('templates/footer'); 
        }
    }

    private function validar_proyecto($id) {
        $proyecto = $this->proyectos_index_model->traer_proyecto($id);
        $datos_validar = $this->proyectos_index_model->obtener_valores_permisos($proyecto->tipo_proyecto, 184, 1);
        $datos_faltantes = [];

        foreach ($datos_validar as $dato) {
            if ($dato['valorb'] == 1) {
                $dato_faltante = ucwords(mb_strtolower($dato['valor']));
                if ($dato['valory'] == 'Tabla') {
                    $datos = $this->proyectos_index_model->listar_informacion_proyecto($dato['valorx'], $id);
                    if(empty($datos)) array_push($datos_faltantes, $dato_faltante);
                } else {
                    if (empty($proyecto->{$dato['valorx']})) array_push($datos_faltantes, $dato_faltante);
                }
            }
        }

        if (empty($datos_faltantes)) {
            return ['valido' => true];
        } else {
            return ['valido' => false, 'datos_faltantes' => $datos_faltantes];
        }
    }

    private function verificar_guardar_item($id_proyecto, $item, $tipo = 1) {
        $proyecto = $this->proyectos_index_model->traer_proyecto($id_proyecto);
        $permiso_externo = $this->proyectos_index_model->validar_accion_proyecto($proyecto->tipo_proyecto, $proyecto->id_estado_proyecto, $_SESSION['persona']) == 1;
        $guardar = false;
        if ($_SESSION['perfil'] == 'Per_Admin' || $proyecto->investigador == $_SESSION['persona'] || $permiso_externo) {
            if ($tipo == 1) {
                if ($proyecto->id_estado_proyecto == 'Proy_For' && $this->proyectos_index_model->verificar_item_proyecto($id_proyecto, $item)) {
                    $guardar = true;
                } else if (!empty($this->proyectos_index_model->listar_motivos_solicitud($id_proyecto, 2)) && !empty($this->proyectos_index_model->verificar_motivos_solicitud($id_proyecto, $item))) {
                    $guardar = true;
                }
            } else {
                $guardar = true;
            }
        }
        return $guardar;
    }

}
