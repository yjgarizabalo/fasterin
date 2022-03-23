<?php
date_default_timezone_set('America/Bogota');
class calidad_control extends CI_Controller
{
  var $Super_estado = false;
  var $Super_elimina = 0;
  var $Super_modifica = 0;
  var $Super_agrega = 0;
  var $ruta_archivos = "archivos_adjuntos/calidad/";

  public function __construct()
  {
    parent::__construct();
    include('application/libraries/festivos_colombia.php');
    $this->load->model('calidad_model');
    $this->load->model('genericas_model');
    $this->load->model('pages_model');
    session_start();
    if (isset($_SESSION["usuario"])) {
      $this->Super_estado = true;
      $this->Super_elimina = 1;
      $this->Super_modifica = 1;
      $this->Super_agrega = 1;
    }
  }

  public function index($url = 'calidad', $id = '')
  {
    if ($this->Super_estado) {
      $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'calidad');
      if (!empty($datos_actividad)) {
        $pages = "calidad";
        $data['js'] = "Calidad";
        $data['id'] = $id;
        $data['actividad'] = $datos_actividad[0]["id_actividad"];
      } else {
        $pages = "sin_session";
        $data['js'] = "";
        $data['actividad'] = "Permisos";
      }
    } else {
      $pages = "inicio";
      $data['js'] = "";
      $data['actividad'] = "Ingresar";
    }
    $this->load->view('templates/header', $data);
    $this->load->view("pages/" . $pages);
    $this->load->view('templates/footer');
  }

  public function asignacion($id = '')
  {
    $data['solicitud'] = $this->calidad_model->consultar_solicitud_id($id);
    $this->load->view("pages/calidad_solicitud", $data);
  }

  public function crear_solicitud()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $cantidad = $this->input->post('cantidad');
        $tipo_cantidad = $this->input->post('cantidad_residuo');
        $presentacion = $this->input->post('presentacion_residuo');
        $activo = $this->input->post('activo');
        $carta_activo = $this->input->post('carta_activo');
        $descripcion = $this->input->post('descripcion');
        $estado_residuo = $this->input->post('estado_residuo');
        $bloque = $this->input->post('id_bloque');
        $salon = $this->input->post('id_salon');
        $data = [];

        $ver_str = $this->verificar_campos_string(['Cantidad' => $cantidad, 'Presentacion' => $presentacion, 'Descripcion' => $descripcion]);
        $ver_num = $this->verificar_campos_numericos(['Estado del residuo' => $estado_residuo]);
        $sw = true;

        if (is_array($ver_str)) {
          $resp = ['mensaje' => "El campo " . $ver_str['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser seleccionado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $data = [
            'cantidad' => $cantidad,
            'tipo_cantidad' => $tipo_cantidad,
            'presentacion' => $presentacion,
            'activo' => 0,
            'estado_residuo' => $estado_residuo,
            'descripcion' => $descripcion,
            'id_estado' => 'Est_Cal_Sol',
            'id_usuario_registra' => $_SESSION['persona'],
            'bloque' => $bloque,
            'salon' => $salon
          ];
          if ($activo == 1) {
            if (empty($_FILES["carta_activo"]["size"])) $resp = ['mensaje' => "Falta adjuntar la carta de Activo.", 'tipo' => "info", 'titulo' => "Oops.!"];
            else {
              $nombre = $_FILES["carta_activo"]["name"];
              $archivo_carta = $this->cargar_archivo("carta_activo", $this->ruta_archivos, "cal");
              if ($archivo_carta[0] == -1) {
                $resp = ['mensaje' => "Error al subir la carta de Activo, por favor verificar.", 'tipo' => "info", 'titulo' => "Oops.!"];
                $sw = false;
              } else {
                $data = [
                  'cantidad' => $cantidad,
                  'tipo_cantidad' => $tipo_cantidad,
                  'presentacion' => $presentacion,
                  'activo' => $activo,
                  'estado_residuo' => $estado_residuo,
                  'descripcion' => $descripcion,
                  'carta_activo' => $archivo_carta[1],
                  'id_estado' => 'Est_Cal_Sol',
                  'id_usuario_registra' => $_SESSION['persona'],
                  'bloque' => $bloque,
                  'salon' => $salon
                ];
              }
            }
          }
        }
        if ($sw) {
          $res = $this->calidad_model->guardar_datos($data, 'calidad_solicitudes');
          if ($res != 0) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
          else {
            $id_solicitud = $this->calidad_model->consultar_ultima_solicitud($_SESSION['persona']);
            $data = [
              'id_solicitud' => $id_solicitud->{'id'},
              'id_estado' => 'Est_Cal_Sol',
              'id_usuario_registro' => $_SESSION['persona'],
            ];
            $res_estado = $this->calidad_model->guardar_datos($data, 'calidad_estados');
            if ($res_estado != 0) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
            else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function modificar_solicitud()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id_solicitud');
        $cantidad = $this->input->post('cantidad');
        $tipo_cantidad = $this->input->post('cantidad_residuo');
        $presentacion = $this->input->post('presentacion_residuo');
        $activo = $this->input->post('activo');
        $carta_activo = $this->input->post('carta_activo');
        $descripcion = $this->input->post('descripcion');
        $estado_residuo = $this->input->post('estado_residuo');
        $bloque = $this->input->post('id_bloque');
        $salon = $this->input->post('id_salon');
        $data = [];
        $solicitud = $this->calidad_model->consultar_solicitud_id($id);

        $ver_str = $this->verificar_campos_string(['Cantidad' => $cantidad, 'Presentacion' => $presentacion, 'Descripcion' => $descripcion]);
        $ver_num = $this->verificar_campos_numericos(['Estado del residuo' => $estado_residuo]);
        $sw = true;

        if (is_array($ver_str)) {
          $resp = ['mensaje' => "El campo " . $ver_str['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser seleccionado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          if ($activo == 1) {
            $data = [
              'cantidad' => $cantidad,
              'tipo_cantidad' => $tipo_cantidad,
              'presentacion' => $presentacion,
              'activo' => $activo,
              'estado_residuo' => $estado_residuo,
              'descripcion' => $descripcion,
              'id_estado' => 'Est_Cal_Sol',
              'bloque' => $bloque,
              'salon' => $salon
            ];
            if (!empty($_FILES["carta_activo"]["size"])) {
              $nombre = $_FILES["carta_activo"]["name"];
              $archivo_carta = $this->cargar_archivo("carta_activo", $this->ruta_archivos, "cal");
              if ($archivo_carta[0] == -1) {
                $resp = ['mensaje' => "Error al subir la carta de Activo, por favor verificar.", 'tipo' => "info", 'titulo' => "Oops.!"];
                $sw = false;
              } else $data['carta_activo'] = $archivo_carta[1];
            } else {
              if (!$solicitud->{'carta_activo'}) {
                $sw = false;
                $resp = ['mensaje' => "El campo de carta de solicitud no puede ir vacio, por favor verificar.", 'tipo' => "info", 'titulo' => "Oops.!"];
              }
            }
          } else {
            $data = [
              'cantidad' => $cantidad,
              'tipo_cantidad' => $tipo_cantidad,
              'presentacion' => $presentacion,
              'activo' => 0,
              'estado_residuo' => $estado_residuo,
              'descripcion' => $descripcion,
              'id_estado' => 'Est_Cal_Sol',
              'bloque' => $bloque,
              'salon' => $salon
            ];
          }
        }
        if ($sw) {
          $res = $this->calidad_model->modificar_datos($data, 'calidad_solicitudes', $id);
          if ($res != 0) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
          else {
            $data = [
              'id_solicitud' => $id,
              'id_estado' => 'Est_Cal_Sol',
              'id_usuario_registro' => $_SESSION['persona'],
            ];
            $res_estado = $this->calidad_model->guardar_datos($data, 'calidad_estados');
            if ($res_estado != 0) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
            else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function listar_solicitudes()
  {
    $resp = [];

    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $id_lote = $this->input->post('id_lote');
      $id_tipo_residuo = $this->input->post('id_tipo_residuo');
      $id_estado_solicitud = $this->input->post('id_estado_solicitud');
      $id_presentacion_residuo = $this->input->post('id_presentacion_residuo');
      $id_cantidad_residuo = $this->input->post('id_cantidad_residuo');
      $id_tipo_proceso = $this->input->post('id_tipo_proceso');
      $id_origen_proceso = $this->input->post('id_origen_proceso');
      $fecha_inicial = $this->input->post('fecha_inicial');
      $fecha_final = $this->input->post('fecha_final');
      $id_tipo_solicitud = $this->input->post('id_tipo_solicitud');

      $administra = $_SESSION["perfil"] == "Per_Admin" ? true : false;
      $admin_mod = $_SESSION["perfil"] == "Per_Adm_Cal" ? true : false;

      $ver_enviado = '<span style="background-color: white; width: 100%;" class="pointer form-control"><span >ver</span></span>';
      $ver_cancelado = '<span style="background-color: #d9534f;color: white; width: 100%;" class="pointer form-control" ><span >ver</span></span>';
      $ver_asignado = '<span style="background-color: #2E79E5; color: white; width: 100%;" class="pointer form-control" ><span >ver</ span>';
      $ver_confirmado = '<span style="background-color: #17a2b8; color: white; width: 100%;" class="pointer form-control" ><span >Ver </span>';
      $ver_finalizado = '<span style="background-color: #5cb85c; color: white; width: 100%;" class="pointer form-control" ><span >Ver </span>';
      $ver_lote = '<span style="background-color: #f0ad4e; color: white; width: 100%;" class="pointer form-control" ><span >Ver </span>';


      $btn_sin_accion = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
      $btn_espera = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn" style="color:#428bca"></span>';
      $btn_cancelar = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default cancelar" style="color:#d9534f"></span>';
      $btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-calendar-check-o btn btn-default asignar" style="color:#2E79E5"></span>';
      $btn_confirmar = '<span title="Confirmar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default confirmar" style="color:#5cb85c"></span>';
      $btn_negar = '<span title="Negar" data-toggle="popover" data-trigger="hover" class="fa fa-ban btn btn-default negar" style="color:#d9534f"></span>';
      $btn_agrupar = '<span title="Agrupar" data-toggle="popover" data-trigger="hover" class="fa fa-inbox btn btn-default agrupar" style="color:#17a2b8"></span>';
      $btn_eliminar = '<span title="Quitar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      $btn_gestionar = '<span title="Gestionar" data-toggle="popover" data-trigger="hover" class="fa fa-retweet btn btn-default gestionar" style="color:#2E79E5"></span>';
      $btn_proceso = '<span title="En Proceso" data-toggle="popover" data-trigger="hover" class="pointer fa fa-edit btn btn-default en_proceso" style="color:#2E79E5;"></span>';
      $btn_finalizar = '<span title="Finalizar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default finalizar" style="color:#5cb85c"></span>';

      $solicitudes = $this->calidad_model->listar_solicitudes_ambiental($id, $id_lote, $id_tipo_residuo, $id_estado_solicitud, $id_presentacion_residuo, $id_cantidad_residuo, $id_tipo_proceso, $id_origen_proceso, $fecha_inicial, $fecha_final, $id_tipo_solicitud);

      
      foreach ($solicitudes as $row) {
        $permiso_estado = $row['permiso_estado'];
          switch ($row['id_estado']) {
            case 'Est_Cal_Sol':
              $row['ver'] = $ver_enviado;
              if (!$id_lote && $row['tipo_solicitud'] == 'Tip_Cal_Re') {
                $row['accion'] = $administra || $admin_mod || $permiso_estado ? "$btn_asignar $btn_negar" : "$btn_cancelar";
              }else if ($row['tipo_solicitud'] == 'Tip_Cal_Aud'){
                $row['accion'] = $administra || $admin_mod || $permiso_estado ? "$btn_confirmar $btn_cancelar" : ($row['tipo_persona_proceso'] == 1 ? $btn_confirmar : $btn_espera);
              }
              break;
            case 'Est_Cal_Can':
              $row['ver'] = $ver_cancelado;
              $row['accion'] = $btn_sin_accion;
              break;
            case 'Est_Cal_Asig':
              $row['ver'] = $ver_asignado;
              if (!$id_lote && $row['tipo_solicitud'] == 'Tip_Cal_Re'){
                  $row['accion'] = $_SESSION['persona'] == $row['id_auxiliar'] || ($administra || $admin_mod || $permiso_estado) ? "$btn_confirmar $btn_negar" : $btn_espera;
              }
              break;
            case 'Est_Cal_Rec':
            case 'Est_Cal_Conf':
              $row['ver'] = $ver_confirmado;
              if ($row['tipo_solicitud'] == 'Tip_Cal_Re' && $row['id_estado'] == 'Est_Cal_Rec') {
                  if($id_lote) $row['accion'] = $administra || $admin_mod || $permiso_estado ? $btn_eliminar : $btn_sin_accion;
                  else $row['accion'] = $administra || $admin_mod || $permiso_estado ? $btn_agrupar : $btn_sin_accion;
              }else if ($row['tipo_solicitud'] == 'Tip_Cal_Aud' && $row['id_estado'] == 'Est_Cal_Conf') {
                  $row['accion'] = $btn_gestionar;
              }
              break;
            case 'Est_Cal_Neg':
              $row['ver'] = $ver_cancelado;
              $row['accion'] = $btn_sin_accion;
              break;
            case 'Est_Cal_Pro':
                $row['ver'] = $ver_confirmado;
                if($row['tipo_solicitud'] == 'Tip_Cal_Aud') $row['accion'] = $administra || $admin_mod || $permiso_estado ? "$btn_proceso $btn_finalizar" : $btn_proceso;
              break;
            case 'Est_Cal_Fin':
              $row['ver'] = $ver_finalizado;
              if($row['tipo_solicitud'] == 'Tip_Cal_Aud') $row['accion'] = $btn_sin_accion;
              break;
            default:
              $row['ver'] = $ver_lote;
              $row['accion'] = $btn_sin_accion;
              break;

            }
            array_push($resp, $row);
        }
    }
    echo json_encode($resp);
  }
  


  public function listar_lotes()
  {
    $resp = [];

    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {

      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Cal" ? true : false;

      $ver_activo = '<span style="background-color: white; width: 100%;" class="pointer form-control"><span >ver</span></span>';
      $ver_enviado = '<span style="background-color: #2E79E5; color: white; width: 100%;" class="pointer form-control" ><span >ver</ span>';
      $ver_remitido = '<span style="background-color: #17a2b8; color: white; width: 100%;" class="pointer form-control" ><span >Ver </span>';
      $ver_finalizado = '<span style="background-color: #5cb85c; color: white; width: 100%;" class="pointer form-control" ><span >Ver </span>';

      $btn_enviar = '<span title="Enviar" data-toggle="popover" data-trigger="hover" class="fa fa-paper-plane btn btn-default enviar" style="color:#2E79E5"></span>';
      $btn_remitir = '<span title="Enviar" data-toggle="popover" data-trigger="hover" class="fa fa-barcode btn btn-default remitir" style="color:#17a2b8"></span>';
      $btn_finalizar = '<span title="Finalizar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default finalizar" style="color:#5cb85c"></span>';
      $btn_sin_accion = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

      $lotes_activos = $this->calidad_model->listar_lotes();

      foreach ($lotes_activos as $row) {
        if ($row['id_estado'] == 'Est_Cal_Act') $row['ver'] = $ver_activo;
        if ($row['id_estado'] == 'Est_Cal_Env') $row['ver'] = $ver_enviado;
        if ($row['id_estado'] == 'Est_Cal_Rem') $row['ver'] = $ver_remitido;
        if ($row['id_estado'] == 'Est_Cal_Fin') $row['ver'] = $ver_finalizado;

        if ($row['id_estado'] == 'Est_Cal_Act') $row['accion'] = $btn_enviar;
        if ($row['id_estado'] == 'Est_Cal_Env') $row['accion'] = $btn_remitir;
        if ($row['id_estado'] == 'Est_Cal_Rem') $row['accion'] = $btn_finalizar;
        if ($row['id_estado'] == 'Est_Cal_Fin') $row['accion'] = $btn_sin_accion;

        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function obtener_valor_parametro()
  {
    $parametro = $this->input->post('id');
    $valores = $this->Super_estado == true ? $this->calidad_model->obtener_valor_parametro($parametro) : array();
    echo json_encode($valores);
  }

  public function obtener_permisos_parametro()
  {
    $parametro = $this->input->post('id');
    $permisos = $this->Super_estado == true ? $this->calidad_model->obtener_permisos_parametro($parametro) : array();
    echo json_encode($permisos);
  }

  public function verificar_campos_numericos($array)
  {
    foreach ($array as $row) {
      if (empty($row) || ctype_space($row) || !is_numeric($row)) {
        return ['type' => -1, 'field' => array_search($row, $array, true)];
      }
    }
    return 1;
  }

  public function verificar_campos_string($array)
  {
    foreach ($array as $row) {
      if (empty($row) || ctype_space($row)) {
        return ['type' => -2, 'field' => array_search($row, $array, true)];
      }
    }
    return 1;
  }

  public function cargar_archivo($mi_archivo, $ruta, $nombre)
  {
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

  public function listar_historial_estados()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id_solicitud');
      $resp = $this->calidad_model->listar_historial_estados($id);
    }
    echo json_encode($resp);
  }

  public function listar_historial_estados_lote()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id_lote');
      $resp = $this->calidad_model->listar_historial_estados_lote($id);
    }
    echo json_encode($resp);
  }

  public function gestionar_solicitud()
  {
    $resp = [];
    $sw = true;
    $origen = $this->input->post('origen');
    if (!$this->Super_estado == true && !$origen) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $estado_nuevo = $this->input->post('estado_nuevo');
      $fecha_asignacion = $this->input->post('fecha_recoleccion');
      $id_auxiliar = $this->input->post('id_auxiliar');
      $observacion = $this->input->post('observacion');
      $val = $this->validar_estado($id, 0, $estado_nuevo);
      if (!$val) $resp = ['mensaje' => 'La solicitud ya ha sido gestionada o no es posible realizar esta acción.', 'tipo' => "info", 'titulo' => "Oops!"];
      else {
        $data = [
          'id_estado' => $estado_nuevo
        ];
        $solicitud = $this->calidad_model->consultar_solicitud_id($id);

        if ($estado_nuevo == 'Est_Cal_Asig') {
          $data['fecha_asignacion'] = $fecha_asignacion;
          $data_auxiliar = [
            'id_solicitud' => $id,
            'id_auxiliar' => $id_auxiliar,
            'id_usuario_registro' => $_SESSION['persona']
          ];
          $res_auxiliar = $this->calidad_model->guardar_datos($data_auxiliar, 'calidad_auxiliares');
          if ($res_auxiliar != 0) $resp = ['mensaje' => "Error al almacenar información, contacte con el administrado.", 'tipo' => "error", 'titulo' => "Oops!"];
          else $sw = true;
        }

        if ($sw) {
          $mod = $this->calidad_model->modificar_datos($data, 'calidad_solicitudes', $id);
          if ($mod != 0) $resp = ['mensaje' => "Error al almacenar información, contacte con el administrado.", 'tipo' => "error", 'titulo' => "Oops!"];
          else {
            $data = [
              'id_solicitud' => $id,
              'id_estado' => $estado_nuevo,
              'id_usuario_registro' => $origen == 1 ? $solicitud->{'id_auxiliar'} : $_SESSION['persona'],
              'observacion' => $estado_nuevo == 'Est_Cal_Neg' ? $observacion : ''
            ];
            $res_estado = $this->calidad_model->guardar_datos($data, 'calidad_estados');
            if ($res_estado != 0) $resp = ['mensaje' => "Error al almacenar información, contacte con el administrado.", 'tipo' => "error", 'titulo' => "Oops!"];
            else $resp = ['mensaje' => "Información almacenada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function validar_estado($id, $id_lote, $estado_nuevo)
  {
    $resp = false;
    $data = $id ? $this->calidad_model->consultar_solicitud_id($id) : $this->calidad_model->consultar_lote_id($id_lote);
    if ($data->{'id_estado'} == 'Est_Cal_Sol' && ($estado_nuevo == 'Est_Cal_Can' || $estado_nuevo == 'Est_Cal_Asig' || $estado_nuevo == 'Est_Cal_Conf' || $estado_nuevo == 'Est_Cal_Neg')) $resp = true;
    if ($data->{'id_estado'} == 'Est_Cal_Asig' && ($estado_nuevo == 'Est_Cal_Rec' || $estado_nuevo == 'Est_Cal_Neg')) $resp = true;
    if ($data->{'id_estado'} == 'Est_Cal_Act' && $estado_nuevo == 'Est_Cal_Env') $resp = true;
    if ($data->{'id_estado'} == 'Est_Cal_Env' && $estado_nuevo == 'Est_Cal_Rem') $resp = true;
    if ($data->{'id_estado'} == 'Est_Cal_Rem' && $estado_nuevo == 'Est_Cal_Fin') $resp = true;
    if ($data->{'id_estado'} == 'Est_Cal_Conf' && $estado_nuevo == 'Est_Cal_Pro') $resp = true;
    if ($data->{'id_estado'} == 'Est_Cal_Pro' && $estado_nuevo == 'Est_Cal_Fin') $resp = true;
    return $resp;
  }

  public function buscar_empleado()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $dato = $this->input->post("dato");
      $filtro = $this->input->post("filtro");
      $id = $this->input->post("id_persona");
      $buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%')";
      $resp = ($dato != "" || $id != "") ? $this->calidad_model->buscar_empleado($buscar, $filtro, $id) : [];
    }
    echo json_encode($resp);
  }

  public function consultar_lote_id()
  {
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id = $this->input->post('id');
      $resp = $this->calidad_model->consultar_lote_id($id);
    }
    echo json_encode($resp);
  }

  public function crear_lote()
  {
    $resp = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $empresa = $this->input->post("empresa");

      $data = [
        'id_empresa' => $empresa,
        'id_estado' => 'Est_Cal_Act',
        'id_persona_registra' => $_SESSION['persona']
      ];

      $add = $this->calidad_model->guardar_datos($data, 'calidad_lotes');
      if ($add != 0) ['mensaje' => "Error al almacenar información, contacte con el administrado.", 'tipo' => "error", 'titulo' => "Oops!"];
      else {
        $id_lote = $this->calidad_model->consultar_ultimo_lote($_SESSION['persona'])->{'id'};

        $data = [
          'id_lote' => $id_lote,
          'id_estado' => 'Est_Cal_Act',
          'id_usuario_registro' => $_SESSION['persona'],
        ];

        $add_estado = $this->calidad_model->guardar_datos($data, 'calidad_lotes_estados');
        if ($add_estado != 0) ['mensaje' => "Error al almacenar información, contacte con el administrado.", 'tipo' => "error", 'titulo' => "Oops!"];
        else $resp = ['mensaje' => "Información almacenada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
      }
    }
    echo json_encode($resp);
  }

  public function listar_lotes_activos()
  {
    $resp = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id_solicitud = $this->input->post("id");
      $id_lote = $this->input->post("id_lote");

      $solicitud = $this->calidad_model->consultar_solicitud_id($id_solicitud);

      $lotes_activos = $this->calidad_model->listar_lotes(0, 'Est_Cal_Act');

      $btn_eliminar = '<span title="Quitar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      $btn_agregar = '<span title="Agregar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default agregar" style="color:#5cb85c"></span>';

      foreach ($lotes_activos as $row) {
        $row['accion'] = $solicitud->{'id_lote'} == $row['id'] ? $btn_eliminar : $btn_agregar;
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function agrupar_solicitud()
  {
    $resp = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id_solicitud = $this->input->post("id_solicitud");
      $id_lote = $this->input->post("id_lote");
      $accion = $this->input->post("accion");

      $data = $accion ? ['id_lote' => $id_lote] : ['id_lote' => NULL];

      $solicitud = $this->calidad_model->consultar_solicitud_id($id_solicitud);


      $mod = $this->calidad_model->modificar_datos($data, 'calidad_solicitudes', $id_solicitud);
      if ($mod != 0) $resp = ['mensaje' => "Error al almacenar información, contacte con el administrado.", 'tipo' => "error", 'titulo' => "Oops!"];
      else {
        $solicitud = $this->calidad_model->consultar_solicitud_id($id_solicitud);
        $resp = ['mensaje' => "Información almacenada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso!", 'id_lote_actual' => $solicitud->{'id_lote'}];
      }
    }
    echo json_encode($resp);
  }

  public function enviar_lote()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id');
        $sw = true;
        $list_estados = [];

        $lote = $this->calidad_model->consultar_lote_id($id);

        $val = $this->validar_estado(0, $id, 'Est_Cal_Env');

        if (!$val) $resp = ['mensaje' => "Este lote ya fue gestionado anteriormente, por favor actualice.", 'tipo' => "info", 'titulo' => "Oops.!"];
        else {
          if ($lote->{'no_solicitudes'} < 1) $resp = ['mensaje' => "No puede enviar un lote sin solicitudes asignadas", 'tipo' => "info", 'titulo' => "Oops.!"];
          else {
            $nombre = $_FILES["formulario_empresa"]["name"];
            $formulario_empresa = $this->cargar_archivo("formulario_empresa", $this->ruta_archivos, "cal");
            if ($formulario_empresa[0] == -1) {
              $resp = ['mensaje' => "Error al subir el formulario, por favor verificar.", 'tipo' => "info", 'titulo' => "Oops.!"];
              $sw = false;
            } else {
              $data = [
                'formulario' => $formulario_empresa[1],
                'id_estado' => 'Est_Cal_Env',
              ];
            }
            if ($sw) {
              $res = $this->calidad_model->modificar_datos($data, 'calidad_lotes', $id);
              if ($res != 0) $resp = ['mensaje' => 'Error al guardar infromación de lote, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
              else {
                $data = [
                  'id_lote' => $id,
                  'id_estado' => 'Est_Cal_Env',
                  'id_usuario_registro' => $_SESSION['persona'],
                ];
                $res_estado = $this->calidad_model->guardar_datos($data, 'calidad_lotes_estados');
                if ($res_estado != 0) ['mensaje' => 'Error al guardar infromación de estado, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
                else {
                  $res_mod = $this->calidad_model->actualizar_solicitudes(['id_estado' => 'Est_Cal_Env'], 'calidad_solicitudes', $id);
                  if ($res_mod != 0) ['mensaje' => 'Error al guardar infromación de solicitud, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
                  else {
                    $solicitudes = $this->calidad_model->listar_solicitudes_ambiental(0, $id, null, null, null, null, null, null);
                    foreach ($solicitudes as $row) {
                      array_push($list_estados, [
                        'id_solicitud' => $row['id'],
                        'id_estado' => 'Est_Cal_Env',
                        'id_usuario_registro' => $_SESSION['persona']
                      ]);
                    }
                    $res_estados = $this->calidad_model->guardar_datos($list_estados, 'calidad_estados', 2);
                    if ($res_estados != 0) ['mensaje' => 'Error al guardar infromación de estados de solicitudes, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
                    else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'archivo' => $formulario_empresa[1]];
                  }
                }
              }
            }
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function gestionar_lote()
  {
    $resp = [];
    $list_estados = [];
    $data = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id = $this->input->post('id');
      $estado_nuevo = $this->input->post('estado_nuevo');
      $numero_remision = $this->input->post('numero_remision');
      $sw = true;

      $val = $this->validar_estado(0, $id, $estado_nuevo);
      if (!$val) {
        $resp = ['mensaje' => "Este lote ya fue gestionado o no tiene permisos para realizar esta acción", 'tipo' => "info", 'titulo' => "Oops!"];
        $sw = false;
      }

      if ($sw) {
        if ($estado_nuevo == 'Est_Cal_Rem') {
          $data = [
            'numero_remision' => $numero_remision,
            'id_estado' => $estado_nuevo,
          ];
        } elseif ($estado_nuevo == 'Est_Cal_Fin') {
          $nombre = $_FILES["certificado"]["name"];
          $certificado = $this->cargar_archivo("certificado", $this->ruta_archivos, "cal");
          if ($certificado[0] == -1) {
            $resp = ['mensaje' => "Error al subir el certificado por favor, verificar.", 'tipo' => "info", 'titulo' => "Oops.!"];
            $sw = false;
          } else {
            $data = [
              'certificado' => $certificado[1],
              'id_estado' => $estado_nuevo,
            ];
          }
        }
      }

      if ($sw) {
        $add = $this->calidad_model->modificar_datos($data, 'calidad_lotes', $id);
        if ($add != 0) $resp = ['mensaje' => "Error al almacenar información, contacte con el administrado.", 'tipo' => "error", 'titulo' => "Oops!"];
        else {
          $data = [
            'id_estado' => $estado_nuevo,
            'id_lote' => $id,
            'id_usuario_registro' => $_SESSION['persona']
          ];
          $add_estado = $this->calidad_model->guardar_datos($data, 'calidad_lotes_estados');
          if ($add_estado != 0) $resp = ['mensaje' => "Error al almacenar información de estado, contacte con el administrado.", 'tipo' => "error", 'titulo' => "Oops!"];
          else {
            $res_mod = $this->calidad_model->actualizar_solicitudes(['id_estado' => $estado_nuevo], 'calidad_solicitudes', $id);
            if ($res_mod != 0) ['mensaje' => 'Error al guardar infromación de solicitud, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
            else {
              $solicitudes = $this->calidad_model->listar_solicitudes_ambiental(0, $id, null, null, null, null, null, null);
              foreach ($solicitudes as $row) {
                array_push($list_estados, [
                  'id_solicitud' => $row['id'],
                  'id_estado' => $estado_nuevo,
                  'id_usuario_registro' => $_SESSION['persona']
                ]);
              }
              $res_estados = $this->calidad_model->guardar_datos($list_estados, 'calidad_estados', 2);
              if ($res_estados != 0) ['mensaje' => 'Error al guardar infromación de estados de solicitudes, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
              else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            }
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function guardar_proceso()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $nombre = $this->input->post('nombre');
        $descripcion = $this->input->post('descripcion');
        $personas = $this->input->post('personas_agregadas');
        $id_usuario_registra = $_SESSION['persona'];
        $idparametro = 209;
        $data = [
          'idparametro' => $idparametro,
          'valor' => $nombre,
          'valorx' => $descripcion,
          'usuario_registra' => $id_usuario_registra,
        ];
        $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        $add = $this->pages_model->guardar_datos($data, 'valor_parametro');
        if ($add == 1) {
          $id_proceso = $this->calidad_model->consulta_id_proceso($id_usuario_registra, $idparametro);
          $data_personas = array();
          foreach ($personas as $key) {
            array_push($data_personas, ['id_persona' => $key['id'], 'id_tipo' =>  $key['tipo_persona'], 'id_proceso' => $id_proceso->{'id'}, 'id_usuario_registra' => $id_usuario_registra]);
          }
          $add_per = $this->pages_model->guardar_datos($data_personas, 'calidad_personas_procesos', 2);
          if ($add_per != 1) $resp = ['mensaje' => "Error al guardar responsables del proceso, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        } else {
          $resp = ['mensaje' => "Error al guardar, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function modificar_proceso()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $nombre = $this->input->post('nombre');
        $descripcion = $this->input->post('descripcion');
        $id_usuario_registra = $_SESSION['persona'];
        $id_proceso = $this->input->post('id_proceso');

        $valor = $this->calidad_model->consulta_actividad_id($id_proceso, 'valor_parametro');
        if ($valor->{'valor'} == $nombre && $valor->{'valorx'} == $descripcion) {
          $resp = ['mensaje' => "Debe realizar alguna modificación ", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $data = ['valor' => $nombre, 'valorx' => $descripcion];
          $mod = $this->pages_model->modificar_datos($data, 'valor_parametro', $id_proceso);
          if ($mod != 1) $resp = ['mensaje' => "Error al guardar, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function listar_procesos()
  {
    $resp = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $idparametro = $this->input->post('idparametro');
      $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      $btn_modificar = '<span title="Modificar" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
      $btn_config = '<span title="Funcionario" data-toggle="popover" data-trigger="hover" style="color: #39b23b;margin-left: 5px" class="pointer fa fa-user btn btn-default funcionario"></span>';
      $procesos = $this->calidad_model->listar_procesos($idparametro);
      foreach ($procesos as $row) {
        $row['accion'] = $btn_config . ' ' . $btn_modificar . ' ' . $btn_eliminar;
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function guardar_funcionario_proceso()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_persona = $this->input->post('id_persona');
        $tipo_persona = $this->input->post('tipo_persona');
        $id_proceso = $this->input->post('id_proceso');
        $id_usuario_registra = $_SESSION['persona'];
        $fun = $this->calidad_model->listar_funcionario_proceso($id_proceso, $id_persona);
        if ($fun) {
          $resp = ['mensaje' => "El funcionario ya se encuentra asignado.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $data_personas = [
            'id_persona' => $id_persona,
            'id_tipo' => $tipo_persona,
            'id_proceso' => $id_proceso,
            'id_usuario_registra' => $id_usuario_registra
          ];
          $add_per = $this->pages_model->guardar_datos($data_personas, 'calidad_personas_procesos');
          if ($add_per != 1) $resp = ['mensaje' => "Error al guardar, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function listar_funcionarios()
  {
    $resp = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id_proceso = $this->input->post('id_proceso');
      $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      $fun = $this->calidad_model->listar_funcionario_proceso($id_proceso);
      foreach ($fun as $row) {
        $row['accion'] = $btn_eliminar;
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function crear_solicitud_auditoria()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $proceso = $this->input->post('proceso_auditoria');
        $observacion = $this->input->post('observacion');
        $data = [];
        $ver_num = $this->verificar_campos_numericos(['Proceso' => $proceso]);
        $ver_str = $this->verificar_campos_string(['Descripcion' => $observacion]);
        $sw = true;

        if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else if (is_array($ver_str)) {
          $resp = ['mensaje' => "El campo " . $ver_str['field'] . " debe ser seleccionado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $data = [
            'tipo_solicitud' => 'Tip_Cal_Aud',
            'id_estado' => 'Est_Cal_Sol',
            'descripcion' => $observacion,
            'id_proceso' => $proceso,
            'id_usuario_registra' => $_SESSION['persona']
          ];
        }

        if ($sw) {
          $res = $this->pages_model->guardar_datos($data, 'calidad_solicitudes');
          if ($res != 1) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
          else {
            $id_solicitud = $this->calidad_model->consultar_ultima_solicitud($_SESSION['persona']);
            $data = [
              'id_solicitud' => $id_solicitud->{'id'},
              'id_estado' => 'Est_Cal_Sol',
              'id_usuario_registro' => $_SESSION['persona'],
            ];
            $res_estado = $this->pages_model->guardar_datos($data, 'calidad_estados');
            if ($res_estado != 1) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
            else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id' => $id_solicitud->{'id'}, 'proceso' => $proceso];
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function listar_correccion()
  {
    $resp = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id_solicitud = $this->input->post('id_solicitud');
      $id_proceso = $this->input->post('id_proceso');
      $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      $btn_modificar = '<span title="Modificar" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
      $ver_activo = '<span style="background-color: white; width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
      $btn_sin_accion = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
      $data = $this->calidad_model->listar_correccion($id_solicitud, $id_proceso);

      $administra = $_SESSION["perfil"] == "Per_Admin" ? true : false;
      $admin_mod = $_SESSION["perfil"] == "Per_Adm_Cal" ? true : false;
      foreach ($data as $row) {
        if ($_SESSION['persona'] == $row['id_usuario_registra']) $user = true;
        else $user = false;
        $row['ver'] = $ver_activo;
        $row['accion'] = $administra || $admin_mod || $user ? "$btn_modificar $btn_eliminar" : " $btn_sin_accion";
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function guardar_correccion()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_solicitud = $this->input->post('id_solicitud');
        $actividad = $this->input->post('actividad');
        $fecha_actividad = $this->input->post('fecha_actividad');
        $id_persona = $this->input->post('persona_actividad');
        $data = [];
        $ver_num = $this->verificar_campos_string(['Actividad' => $actividad, 'Fecha Actividad' => $fecha_actividad, 'Responsable' => $id_persona]);
        $sw = true;
        if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $data = [
            'id_solicitud' => $id_solicitud,
            'actividad' => $actividad,
            'id_persona' => $id_persona,
            'fecha_actividad' => $fecha_actividad,
            'id_usuario_registra' => $_SESSION['persona']
          ];
        }
        if ($sw) {
          $add_actividad = $this->pages_model->guardar_datos($data, 'calidad_correcciones');
          if ($add_actividad != 1) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
          else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id_responsable' => $id_persona];
        }
      }
    }
    echo json_encode($resp);
  }

  public function modificar_correccion()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id_data');
        $id_solicitud = $this->input->post('id_solicitud');
        $actividad = $this->input->post('actividad');
        $fecha_actividad = $this->input->post('fecha_actividad');
        $id_persona = $this->input->post('persona_actividad');
        $data = [];
        $ver_num = $this->verificar_campos_string(['Actividad' => $actividad, 'Fecha Actividad' => $fecha_actividad, 'Responsable' => $id_persona]);
        $sw = true;
        if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $data = $this->calidad_model->consulta_actividad_id($id, 'calidad_correcciones');
          if ($data->{'actividad'} == $actividad && $data->{'fecha_actividad'} == $fecha_actividad && $data->{'id_persona'} == $id_persona) {
            $resp = ['mensaje' => "Debe realizar alguna modificación ", 'tipo' => "info", 'titulo' => "Oops.!"];
            $sw = false;
          }
        }
        if ($sw) {
          $data = [
            'actividad' => $actividad,
            'fecha_actividad' => $fecha_actividad,
            'id_persona' => $id_persona
          ];

          $mod = $this->pages_model->modificar_datos($data, 'calidad_correcciones', $id);
          if ($mod != 1) $resp = ['mensaje' => "Error al almacenar información, contacte con el administrado.", 'tipo' => "error", 'titulo' => "Oops!"];
          else {
            if ($data['id_persona'] != $id_persona) $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id_responsable' => $id_persona];
            else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function listar_plan_accion()
  {
    $resp = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id_solicitud = $this->input->post('id_solicitud');
      $id_proceso = $this->input->post('id_proceso');
      $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      $btn_modificar = '<span title="Modificar" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
      $ver_activo = '<span style="background-color: white; width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
      $btn_sin_accion = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
      $data = $this->calidad_model->listar_plan_accion($id_solicitud, $id_proceso);

      $administra = $_SESSION["perfil"] == "Per_Admin" ? true : false;
      $admin_mod = $_SESSION["perfil"] == "Per_Adm_Cal" ? true : false;
      foreach ($data as $row) {
        if ($_SESSION['persona'] == $row['id_usuario_registra']) $user = true;
        else $user = false;
        $row['ver'] = $ver_activo;
        $row['accion'] = $administra || $admin_mod || $user ? "$btn_modificar $btn_eliminar" : " $btn_sin_accion";
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function guardar_plan_accion()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_solicitud = $this->input->post('id_solicitud');
        $actividad = $this->input->post('actividad');
        $fecha_actividad = $this->input->post('fecha_actividad');
        $id_persona = $this->input->post('persona_actividad');
        $data = [];
        $ver_num = $this->verificar_campos_string(['Actividad' => $actividad, 'Fecha Actividad' => $fecha_actividad, 'Responsable' => $id_persona]);
        $sw = true;
        if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $data = [
            'id_solicitud' => $id_solicitud,
            'actividad' => $actividad,
            'id_persona' => $id_persona,
            'fecha_actividad' => $fecha_actividad,
            'id_usuario_registra' => $_SESSION['persona']
          ];
        }
        if ($sw) {
          $add_actividad = $this->pages_model->guardar_datos($data, 'calidad_plan_accion');
          if ($add_actividad != 1) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
          else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id_responsable' => $id_persona];
        }
      }
    }
    echo json_encode($resp);
  }

  public function modificar_plan_accion()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id_data');
        $id_solicitud = $this->input->post('id_solicitud');
        $actividad = $this->input->post('actividad');
        $fecha_actividad = $this->input->post('fecha_actividad');
        $id_persona = $this->input->post('persona_actividad');
        $data = [];
        $ver_num = $this->verificar_campos_string(['Actividad' => $actividad, 'Fecha Actividad' => $fecha_actividad, 'Responsable' => $id_persona]);
        $sw = true;
        if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $data = $this->calidad_model->consulta_actividad_id($id, 'calidad_plan_accion');
          if ($data->{'actividad'} == $actividad && $data->{'fecha_actividad'} == $fecha_actividad && $data->{'id_persona'} == $id_persona) {
            $resp = ['mensaje' => "Debe realizar alguna modificación ", 'tipo' => "info", 'titulo' => "Oops.!"];
            $sw = false;
          }
        }
        if ($sw) {
          $data = [
            'actividad' => $actividad,
            'fecha_actividad' => $fecha_actividad,
            'id_persona' => $id_persona
          ];

          $mod = $this->pages_model->modificar_datos($data, 'calidad_plan_accion', $id);
          if ($mod != 1) $resp = ['mensaje' => "Error al almacenar información, contacte con el administrado.", 'tipo' => "error", 'titulo' => "Oops!"];
          else {
            if ($data['id_persona'] != $id_persona) $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id_responsable' => $id_persona];
            else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function listar_participantes()
  {
    $resp = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id_solicitud = $this->input->post('id_solicitud');
      $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      $data = $this->calidad_model->listar_participantes($id_solicitud);
      foreach ($data as $row) {
        $row['accion'] = $btn_eliminar;
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function guardar_participante()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $fecha_actividad = $this->input->post('fecha_actividad');
        $id_persona = $this->input->post('persona_actividad');
        $id_solicitud = $this->input->post('id_solicitud');
        $data = [];
        $ver_num = $this->verificar_campos_string(['Fecha Actividad' => $fecha_actividad, 'Participante' => $id_persona]);
        $fun = $this->calidad_model->listar_participantes($id_solicitud, $id_persona);

        $sw = true;
        if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } elseif ($fun) {
          $resp = ['mensaje' => "El funcionario ya se encuentra asignado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $data = [
            'id_solicitud' => $id_solicitud,
            'id_persona' => $id_persona,
            'fecha_actividad' => $fecha_actividad,
            'id_usuario_registra' => $_SESSION['persona']
          ];
        }
        if ($sw) {
          $add_persona = $this->pages_model->guardar_datos($data, 'calidad_participantes');
          if ($add_persona != 1) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
          else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function guardar_datos_nc()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_solicitud = $this->input->post('id_solicitud');
        $tipo_accion = $this->input->post('tipo_accion');
        $tipo_hallazgo = $this->input->post('tipo_hallazgo');
        $origen_fuente = $this->input->post('origen_fuente');
        $descripcion = $this->input->post('descripcion');
        $data = [];
        $ver_num = $this->verificar_campos_numericos(['Tipo de Accion' => $tipo_accion, 'Tipo de Hallazgo' => $tipo_hallazgo, 'Origen de la Fuente' => $origen_fuente]);
        $ver_str = $this->verificar_campos_string(['Descripción' => $descripcion]);
        $sw = true;
        if (is_array($ver_str)) {
          $resp = ['mensaje' => "El campo " . $ver_str['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $data_nc = $this->calidad_model->consultar_nc($id_solicitud);
          if ($data_nc) {
            if ($data_nc->{'id_tipo_accion'} == $tipo_accion && $data_nc->{'id_tipo_hallazgo'} == $tipo_hallazgo && $data_nc->{'id_origen'} == $origen_fuente && $data_nc->{'descripcion'} == $descripcion) {
              $resp = ['mensaje' => "Debe realizar alguna modificación ", 'tipo' => "info", 'titulo' => "Oops.!"];
              $sw = false;
            } else {
              if ($sw) {
                $data = [
                  'id_solicitud' => $id_solicitud,
                  'id_tipo_accion' => $tipo_accion,
                  'id_tipo_hallazgo' => $tipo_hallazgo,
                  'id_origen' => $origen_fuente,
                  'descripcion' => $descripcion,
                  'id_usuario_modifica' => $_SESSION['persona'],
                  'fecha_modifica' => date("Y-m-d H:i:s")
                ];

                $mod = $this->calidad_model->modificar_datos($data, 'calidad_datos_nc', $data_nc->{'id'});
                if ($mod != 1) $resp = ['mensaje' => "Error al almacenar información, contacte con el administrado.", 'tipo' => "error", 'titulo' => "Oops!"];
                else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
              }
            }
          } else {
            if ($sw) {
              $data = [
                'id_solicitud' => $id_solicitud,
                'id_tipo_accion' => $tipo_accion,
                'id_tipo_hallazgo' => $tipo_hallazgo,
                'id_origen' => $origen_fuente,
                'descripcion' => $descripcion,
                'id_usuario_registra' => $_SESSION['persona']
              ];
              $add_persona = $this->pages_model->guardar_datos($data, 'calidad_datos_nc');
              if ($add_persona != 1) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
              else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            }
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function consultar_nc()
  {
    $id_solicitud = $this->input->post('id_solicitud');
    $data = $this->Super_estado == true ? $this->calidad_model->consultar_nc($id_solicitud) : array();
    echo json_encode($data);
  }

  public function recibir_archivos()
  {
    $resp = ['mensaje' => "Todos Los archivos fueron cargados.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
    $id_solicitud = $_POST['id_solicitud'];
    $nombre = [];
    $tipo = $_POST['tipo'];
    $id_usuario_registra = $_SESSION['persona'];
    $nombre_real = $_FILES["file"]["name"];
    $cargo = $this->pages_model->cargar_archivo("file", $this->ruta_archivos, "cal");
    if ($cargo[0] == -1) {
      header("HTTP/1.0 400 Bad Request");
      echo ($nombre);
      return;
    }
    $data = [
      'id_solicitud' => $id_solicitud,
      'nombre_real' => $nombre_real,
      'nombre_guardado' => $cargo[1],
      'tipo' => $tipo,
      'id_usuario_registra' => $id_usuario_registra,
    ];
    $add = $this->pages_model->guardar_datos($data, 'calidad_adjuntos_nc');
    echo json_encode($resp);
  }

  public function listar_herramienta()
  {
    $resp = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id_solicitud = $this->input->post('id_solicitud');
      $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      $btn_modificar = '<span title="Modificar" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
      $data = $this->calidad_model->listar_herramienta($id_solicitud);
      foreach ($data as $row) {
        $row['accion'] = $btn_modificar . ' ' . $btn_eliminar;
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function guardar_herramienta()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $idea = $this->input->post('idea');
        $porque1 = $this->input->post('porque1');
        $porque2 = $this->input->post('porque2');
        $porque3 = $this->input->post('porque3');
        $porque4 = $this->input->post('porque4');
        $porque5 = $this->input->post('porque5');
        $id_solicitud = $this->input->post('id_solicitud');
        $data = [];
        $ver_num = $this->verificar_campos_string(['LLuvia de Idea' => $idea, 'Por Qué 1' => $porque1, 'Por Qué 2' => $porque2, 'Por Qué 3' => $porque3, 'Por Qué 4' => $porque4, 'Por Qué 5' => $porque5]);
        $sw = true;
        if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $data = [
            'id_solicitud' => $id_solicitud,
            'idea' => $idea,
            'porque1' => $porque1,
            'porque2' => $porque2,
            'porque3' => $porque3,
            'porque4' => $porque4,
            'porque5' => $porque5,
            'id_usuario_registra' => $_SESSION['persona']
          ];
        }
        if ($sw) {
          $add_persona = $this->pages_model->guardar_datos($data, 'calidad_herramienta_nc');
          if ($add_persona != 1) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
          else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function modificar_herramienta()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $idea = $this->input->post('idea');
        $porque1 = $this->input->post('porque1');
        $porque2 = $this->input->post('porque2');
        $porque3 = $this->input->post('porque3');
        $porque4 = $this->input->post('porque4');
        $porque5 = $this->input->post('porque5');
        $id_solicitud = $this->input->post('id_solicitud');
        $id = $this->input->post('id_data');
        $data = [];
        $ver_num = $this->verificar_campos_string(['LLuvia de Idea' => $idea, 'Por Qué 1' => $porque1, 'Por Qué 2' => $porque2, 'Por Qué 3' => $porque3, 'Por Qué 4' => $porque4, 'Por Qué 5' => $porque5]);
        $sw = true;
        if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $data_hr = $this->calidad_model->consulta_actividad_id($id, 'calidad_herramienta_nc');
          if ($data_hr->{'idea'} == $idea && $data_hr->{'porque1'} == $porque1 && $data_hr->{'porque2'} == $porque2 && $data_hr->{'porque3'} == $porque3 && $data_hr->{'porque4'} == $porque4 && $data_hr->{'porque5'} == $porque5) {
            $resp = ['mensaje' => "Debe realizar alguna modificación ", 'tipo' => "info", 'titulo' => "Oops.!"];
            $sw = false;
          } else {
            $data = [
              'idea' => $idea,
              'porque1' => $porque1,
              'porque2' => $porque2,
              'porque3' => $porque3,
              'porque4' => $porque4,
              'porque5' => $porque5
            ];
            $sw = true;
          }
        }
        if ($sw) {
          $mod = $this->pages_model->modificar_datos($data, 'calidad_herramienta_nc', $id);
          if ($mod != 1) $resp = ['mensaje' => 'Error al guardar infromación, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
          else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function listar_archivos_adjuntos()
  {
    $resp = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id_solicitud = $this->input->post('id_solicitud');
      $tipo = $this->input->post('tipo');
      $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      $data = $this->calidad_model->listar_archivos_adjuntos($id_solicitud, $tipo);
      foreach ($data as $row) {
        $row['accion'] = $btn_eliminar;
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function listar_avances_actividad()
  {
    $resp = [];
    if (!$this->Super_estado == true) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id_data = $this->input->post('id_data');
      $id_solicitud = $this->input->post('id_solicitud');
      $tipo = $this->input->post('tipo');
      $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      $btn_modificar = '<span title="Modificar" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
      $data = $this->calidad_model->listar_avances_actividad($id_data, $id_solicitud, $tipo);
      foreach ($data as $row) {
        $row['accion'] =  $btn_modificar . ' ' . $btn_eliminar;
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function guardar_avance()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $observacion = $this->input->post('observacion_act');
        $id_solicitud = $this->input->post('id_solicitud');
        $id_actividad = $this->input->post('id_data');
        $tipo = $this->input->post('tipo');
        $fecha_fin = $this->input->post('fecha_finactividad');
        $data = [];
        $sw = true;
        $ver_num = $this->verificar_campos_string(['Fecha Fin de Actividad' => $fecha_fin]);

        if (empty($_FILES["evidencia"]["size"])) {
          $resp = ['mensaje' => "Falta adjuntar evidencia.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $nombre = $_FILES["evidencia"]["name"];
          $archivo = $this->cargar_archivo("evidencia", $this->ruta_archivos, "cal");
          if ($archivo[0] == -1) {
            $resp = ['mensaje' => "Error al subir archivo, por favor verificar.", 'tipo' => "info", 'titulo' => "Oops.!"];
            $sw = false;
          } else {
            $data = [
              'id_solicitud' => $id_solicitud,
              'id_actividad' => $id_actividad,
              'nombre_real' => $nombre,
              'nombre_guardado' => $archivo[1],
              'observacion' => $observacion,
              'tipo' => $tipo,
              'fecha_fin' => $fecha_fin,
              'id_usuario_registra' => $_SESSION['persona'],
            ];
          }
        }
        if ($sw) {
          $res = $this->pages_model->guardar_datos($data, 'calidad_avances_actividad');
          if ($res != 1) $resp = ['mensaje' => 'Error al guardar información, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
          else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function modificar_avance()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $observacion = $this->input->post('observacion_act');
        $id = $this->input->post('id_data');
        $fecha_fin = $this->input->post('fecha_finactividad');
        $data = [];
        $sw = true;
        $ver_num = $this->verificar_campos_string(['Fecha Fin de Actividad' => $fecha_fin]);
        $dato = $this->calidad_model->consulta_actividad_id($id, 'calidad_avances_actividad');
        if (is_array($ver_num)) {
          $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser diligenciado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else if ($dato->{'observacion'} == $observacion && $dato->{'fecha_fin'} == $fecha_fin) {
          $resp = ['mensaje' => "Debe realizar alguna modificación ", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else {
          $nombre = $_FILES["evidencia"]["name"];
          if ($nombre) {
            $archivo = $this->cargar_archivo("evidencia", $this->ruta_archivos, "cal");
            if ($archivo[0] == -1) {
              $resp = ['mensaje' => "Error al subir archivo, por favor verificar.", 'tipo' => "info", 'titulo' => "Oops.!"];
              $sw = false;
            } else {
              $data = [
                'nombre_real' => $nombre,
                'nombre_guardado' => $archivo[1],
                'observacion' => $observacion,
                'fecha_fin' => $fecha_fin,
              ];
            }
          } else {
            $data = [
              'observacion' => $observacion,
              'fecha_fin' => $fecha_fin,
            ];
          }
        }
        if ($sw) {
          $res = $this->pages_model->modificar_datos($data, 'calidad_avances_actividad', $id);
          if ($res != 1) $resp = ['mensaje' => 'Error al guardar información, por favor contacte con el administrador', 'tipo' => "error", 'titulo' => "Oops.!"];
          else $resp = ['mensaje' => 'Información almacenda con exito!', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function eliminar_datos()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if (!$this->Super_elimina) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $tabla = $this->input->post("tabla");
        $id_usuario_elimina = $_SESSION['persona'];
        if (empty($id)) {
          $resp = ['mensaje' => "Error al cargar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        } else {
          if ($tabla == 'valor_parametro') {
            $data = ['estado' => 0];
          } else {
            $data = ['fecha_elimina' => date("Y-m-d H:i:s"), 'id_usuario_elimina' => $id_usuario_elimina, 'estado' => 0,];
          }
          $query = $this->pages_model->modificar_datos($data, $tabla, $id);
          $resp = ['mensaje' => "Los datos fueron eliminados con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          if ($query == -1) $resp = ['mensaje' => "Error al eliminar los datos, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function validar_usuario()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id_proceso = $this->input->post('id_proceso');
      $id_persona = $_SESSION['persona'];
      $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Cal" ? true : false;
      $data = $this->calidad_model->listar_funcionario_proceso($id_proceso, $id_persona);
      if ($admin || $data) {
        $resp = ['tipo' => 1];
      } else {
        $resp = ['tipo' => 2];
      }
    }
    echo json_encode($resp);
  }


  public function obtener_estado_informes()
  {
    $resp = [];
    $fecha_inicial = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");

    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }
    $cantidades = $this->calidad_model->obtener_estado_informes($fecha_inicial, $fecha_fin);

    $total = 0;
    $abiertas = 0;

    foreach ($cantidades as $row) {
      $total += $row['cantidad'];
      if ($row['id_estado'] == 'Est_Cal_Sol' || $row['id_estado'] == 'Est_Cal_Pro') {
        $abiertas += $row['cantidad'];
      }
    }

    foreach ($cantidades as $row) {

      if ($row["id_estado"] == 'Est_Cal_Fin') {
        $porcentaje = ($row['cantidad'] / $total) * 100;
        $datos = ['nombre' =>  "Cerradas", "cantidad" => $row["cantidad"], "porcentaje" => round($porcentaje)];
        array_push($resp, $datos);
      }

      if ($row['id_estado'] == 'Est_Cal_Pro') {
        $porcentaje = ($abiertas / $total) * 100;
        $datos = ['nombre' =>  "Abiertas", "cantidad" => $abiertas, "porcentaje" => round($porcentaje)];
        array_push($resp, $datos);
      }
    }
    echo json_encode($resp);
    return;
  }

  public function obtener_detalle_estado()
  {
    $resp = [];
    $fecha_inicial = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");

    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }

    $cantidades = $this->calidad_model->obtener_detalle_estado($fecha_inicial, $fecha_fin);

    foreach ($cantidades as $row) {
      if ($row["id_estado"] == 'Est_Cal_Fin') {
        $datos = ['nombre' =>  "Acciones Ejecutadas", "cantidad" => $row["cantidad"]];
        array_push($resp, $datos);
      }
      if ($row["id_estado"] == 'Est_Cal_Pro') {
        $datos = ['nombre' =>  "Acciones en Proceso", "cantidad" => $row["cantidad"]];
        array_push($resp, $datos);
      }

      if ($row["id_estado"] == 'Est_Cal_Sol') {
        $datos = ['nombre' =>  "Acciones Abiertas", "cantidad" => $row["cantidad"]];
        array_push($resp, $datos);
      }
    }



    echo json_encode($resp);
    return;
  }

  public function obtener_tipo_accion()
  {
    $resp = [];
    $sin_cla = 0;
    $total = 0;
    $fecha_inicial = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");

    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }

    $cantidades = $this->calidad_model->obtener_tipo_accion($fecha_inicial, $fecha_fin);
    $sin_clasificar = $this->calidad_model->obtener_sin_clasificar($fecha_inicial, $fecha_fin);

    foreach ($sin_clasificar as $row) {
      $sin_cla =  $row["sin_clasificar"];
    }

    foreach ($cantidades as $row) {
      $total = $row["total"];
      $datos = ['nombre' =>  $row["nombre"], "cantidad" => $row["cantidad"], "porcentaje" =>  round(($row["cantidad"] / $total) * 100, PHP_ROUND_HALF_UP)];
      array_push($resp, $datos);
    }

    $datos = ['nombre' =>  "SIN CLASIFICAR", "cantidad" => $sin_cla, "porcentaje" =>  round(($sin_cla / $total) * 100, PHP_ROUND_HALF_UP)];
    array_push($resp, $datos);

    echo json_encode($resp);
  }

  public function obtener_tipo_hallazgo()
  {
    $resp = [];
    $sin_cla = 0;
    $total = 0;
    $fecha_inicial = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");

    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }

    $cantidades = $this->calidad_model->obtener_tipo_hallazgo($fecha_inicial, $fecha_fin);
    $sin_clasificar = $this->calidad_model->obtener_sin_clasificar($fecha_inicial, $fecha_fin);

    foreach ($sin_clasificar as $row) {
      $sin_cla =  $row["sin_clasificar"];
    }

    foreach ($cantidades as $row) {
      $total = $row["total"];
      $datos = ['nombre' =>  $row["nombre"], "cantidad" => $row["cantidad"], "porcentaje" =>  round(($row["cantidad"] / $total) * 100, PHP_ROUND_HALF_UP)];
      array_push($resp, $datos);
    }

    $datos = ['nombre' =>  "SIN CLASIFICAR", "cantidad" => $sin_cla, "porcentaje" =>  round(($sin_cla / $total) * 100, PHP_ROUND_HALF_UP)];
    array_push($resp, $datos);

    echo json_encode($resp);
    return;
  }

  public function obtener_cumplimiento_estados()
  {

    $procesos = [];
    $final = [];
    $fecha_inicial = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");

    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }

    $cantidades = $this->calidad_model->obtener_cumplimiento_estados($fecha_inicial, $fecha_fin);
    $id = 0;
    foreach ($cantidades as $row) {
      if ($row['id_proceso'] != $id) {
        array_push($procesos, ['id_proceso' => $row['id_proceso'], 'proceso' => $row['proceso'],]);
      }
      $id = $row['id_proceso'];
    }


    foreach ($procesos as $row) {

      $total = 0;
      $finalizada = 0;
      $en_proceso = 0;
      $solicitada = 0;

      foreach ($cantidades as $datos) {

        if ($row['id_proceso'] == $datos['id_proceso']) {

          if ($datos['id_estado'] == 'Est_Cal_Fin') {
            $finalizada = $datos['cantidad'];
            $total += $finalizada;
          }
          if ($datos['id_estado'] == 'Est_Cal_Pro') {
            $en_proceso = $datos['cantidad'];
            $total += $en_proceso;
          }
          if ($datos['id_estado'] == 'Est_Cal_Sol') {
            $solicitada = $datos['cantidad'];
            $total += $solicitada;
          }
        }
      }
      $porcentaje = ($finalizada / $total) * 100;
      array_push($final, ['proceso' => $row['proceso'], 'ejecutada' => $finalizada, 'en_proceso' => $en_proceso, 'solicitada' => $solicitada, 'total' => $total, 'porcentaje' => $porcentaje]);
    }

    echo json_encode($final);
  }

  public function obtener_tipos_procesos()
  {

    $procesos = [];
    $final = [];
    $fecha_inicial = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");

    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }

    $cantidades = $this->calidad_model->obtener_tipos_procesos($fecha_inicial, $fecha_fin);

    $id = 0;
    foreach ($cantidades as $row) {

      if ($row['id_proceso'] != $id) {
        array_push($procesos, ['id_proceso' => $row['id_proceso'], 'proceso' => $row['proceso']]);
      }
      $id = $row['id_proceso'];
    }

    foreach ($procesos as $row) {

      $total = 0;
      $correctiva = 0;
      $preventiva = 0;
      $mejora = 0;

      foreach ($cantidades as $datos) {
        if ($row['id_proceso'] == $datos['id_proceso']) {
          if ($datos['accion'] == 'Acc_Cal_Corr') {
            $correctiva = $datos['cantidad'];
            $total += $correctiva;
          }
          if ($datos['accion'] == 'Acc_Cal_Prev') {
            $preventiva = $datos['cantidad'];
            $total += $preventiva;
          }
          if ($datos['accion'] == 'Acc_Cal_Mej') {
            $mejora = $datos['cantidad'];
            $total += $mejora;
          }
        }
      }
      array_push($final, ['proceso' => $row['proceso'], 'correctiva' => $correctiva, 'preventiva' => $preventiva, 'mejora' => $mejora, 'total' => $total]);
    }

    echo json_encode($final);
  }

  public function obtener_hallazgos_procesos()
  {
    $procesos = [];
    $final = [];
    $fecha_inicial = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");

    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }

    $cantidades = $this->calidad_model->obtener_hallazgos_procesos($fecha_inicial, $fecha_fin);


    $id = 0;
    foreach ($cantidades as $row) {
      if ($row['id_proceso'] != $id) {
        array_push($procesos, ['id_proceso' => $row['id_proceso'], 'proceso' => $row['proceso']]);
      }
      $id = $row['id_proceso'];
    }

    foreach ($procesos as $row) {
      $total = 0;
      $no_conformidad = 0;
      $op_mejora = 0;
      $observacion = 0;

      foreach ($cantidades as $datos) {
        if ($row['id_proceso'] == $datos['id_proceso']) {
          if ($datos['hallazgo'] == 'Hal_Cal_NoCon') {
            $no_conformidad = $datos['cantidad'];
            $total += $no_conformidad;
          }
          if ($datos['hallazgo'] == 'Hal_Cal_Obs') {
            $op_mejora = $datos['cantidad'];
            $total += $op_mejora;
          }
          if ($datos['hallazgo'] == 'Hal_Cal_Mej') {
            $observacion = $datos['cantidad'];
            $total += $observacion;
          }
        }
      }
      array_push($final, ['proceso' => $row['proceso'], 'no_conformidad' => $no_conformidad, 'op_mejora' => $op_mejora, 'observacion' => $observacion, 'total' => $total]);
    }

    echo json_encode($final);
  }

  public function obtener_estados_auditoria()
  {

    $origen = [];
    $final = [];
    $fecha_inicial = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");

    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }

    $cantidades = $this->calidad_model->obtener_estados_auditoria($fecha_inicial, $fecha_fin);

    $id = 0;
    foreach ($cantidades as $row) {
      if ($row['id_origen'] != $id) {
        array_push($origen, ['id_origen' => $row['id_origen'], 'origen' => $row['origen']]);
      }
      $id = $row['id_origen'];
    }

    foreach ($origen as $row) {

      $total = 0;
      $finalizada = 0;
      $en_proceso = 0;
      $solicitada = 0;

      foreach ($cantidades as $datos) {

        if ($row['id_origen'] == $datos['id_origen']) {

          if ($datos['id_estado'] == 'Est_Cal_Fin') {
            $finalizada = $datos['cantidad'];
            $total += $finalizada;
          }
          if ($datos['id_estado'] == 'Est_Cal_Pro') {
            $en_proceso = $datos['cantidad'];
            $total += $en_proceso;
          }
          if ($datos['id_estado'] == 'Est_Cal_Sol') {
            $solicitada = $datos['cantidad'];
            $total += $solicitada;
          }
        }
      }
      $porcentaje = ($finalizada / $total) * 100;
      array_push($final, ['origen' => $row['origen'], 'ejecutada' => $finalizada, 'en_proceso' => $en_proceso, 'solicitada' => $solicitada, 'total' => $total, 'porcentaje' => $porcentaje]);
    }

    echo json_encode($final);
  }

  public function obtener_tipos_origen()
  {

    $origen = [];
    $final = [];
    $fecha_inicial = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");

    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }

    $cantidades = $this->calidad_model->obtener_tipos_origen($fecha_inicial, $fecha_fin);

    $id = 0;
    foreach ($cantidades as $row) {

      if ($row['id_origen'] != $id) {
        array_push($origen, ['id_origen' => $row['id_origen'], 'origen' => $row['origen']]);
      }
      $id = $row['id_origen'];
    }

    foreach ($origen as $row) {

      $total = 0;
      $correctiva = 0;
      $preventiva = 0;
      $mejora = 0;

      foreach ($cantidades as $datos) {
        if ($row['id_origen'] == $datos['id_origen']) {
          if ($datos['accion'] == 'Acc_Cal_Corr') {
            $correctiva = $datos['cantidad'];
            $total += $correctiva;
          }
          if ($datos['accion'] == 'Acc_Cal_Prev') {
            $preventiva = $datos['cantidad'];
            $total += $preventiva;
          }
          if ($datos['accion'] == 'Acc_Cal_Mej') {
            $mejora = $datos['cantidad'];
            $total += $mejora;
          }
        }
      }
      array_push($final, ['origen' => $row['origen'], 'correctiva' => $correctiva, 'preventiva' => $preventiva, 'mejora' => $mejora, 'total' => $total]);
    }

    echo json_encode($final);
  }

  public function obtener_hallazgos_origen()
  {
    $origen = [];
    $final = [];
    $fecha_inicial = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");

    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }

    $cantidades = $this->calidad_model->obtener_hallazgos_origen($fecha_inicial, $fecha_fin);

    $id = 0;
    foreach ($cantidades as $row) {
      if ($row['id_origen'] != $id) {
        array_push($origen, ['id_origen' => $row['id_origen'], 'origen' => $row['origen']]);
      }
      $id = $row['id_origen'];
    }

    foreach ($origen as $row) {
      $total = 0;
      $no_conformidad = 0;
      $op_mejora = 0;
      $observacion = 0;

      foreach ($cantidades as $datos) {
        if ($row['id_origen'] == $datos['id_origen']) {
          if ($datos['hallazgo'] == 'Hal_Cal_NoCon') {
            $no_conformidad = $datos['cantidad'];
            $total += $no_conformidad;
          }
          if ($datos['hallazgo'] == 'Hal_Cal_Obs') {
            $op_mejora = $datos['cantidad'];
            $total += $op_mejora;
          }
          if ($datos['hallazgo'] == 'Hal_Cal_Mej') {
            $observacion = $datos['cantidad'];
            $total += $observacion;
          }
        }
      }
      array_push($final, ['origen' => $row['origen'], 'no_conformidad' => $no_conformidad, 'op_mejora' => $op_mejora, 'observacion' => $observacion, 'total' => $total]);
    }

    echo json_encode($final);
  }

  public function obtener_sin_clasificar()
  {

    $fecha_inicial = $this->input->post("fecha_inicio");
    $fecha_fin = $this->input->post("fecha_fin");

    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }

    $cantidades = $this->calidad_model->obtener_sin_clasificar($fecha_inicial, $fecha_fin);
    echo json_encode($cantidades);
  }

  public function listar_personas(){
		$texto = $this->input->post('texto');
		$data = $texto ? $this->calidad_model->listar_personas($texto) : [];
		echo json_encode($data);
	}

  public function listar_actividades_adm(){
		$persona = $this->input->post('persona');
		$data = (isset($persona) && !empty($persona))
			? $this->calidad_model->listar_actividades_adm($persona)
			: [];
		echo json_encode($data);
	}

  public function asignar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_agrega) {
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			$ok = $this->calidad_model->validar_asignacion_actividad($actividad, $persona);
			if ($ok) {
				$data = ['actividad_id'=>$actividad, 'persona_id'=>$persona, 'usuario_registra'=>$_SESSION['persona']];
				$resp = $this->calidad_model->guardar_datos2($data, 'actividad_persona_calidad');
				$res = $resp ? [
					'mensaje' => "Actividad asignada exitosamente.",
					'tipo' => "success",
					'titulo' => "Proceso Exitoso!"
				] : [
					'mensaje' => "Ha ocurrido un error al asignar la actividad.",
					'tipo' => "info",
					'titulo' => "Ooops!"
				];
			} else $res = [
				'mensaje' => "El usuario ya tiene asignada esta actividad.",
				'tipo' => "info",
				'titulo' => "Ooops!"
			];
		} else $res = [
			'mensaje' => 'No tiene Permisos Para Realizar Esta operación.',
			'tipo' => 'error',
			'titulo' => 'Oops.!'
		];
		echo json_encode($res);
	}

	public function quitar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_modifica) {
			$id = $this->input->post('asignado');
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			// Verifico si actividad ya está asignada o no. Esta función retorna 0 si no está asignada la actividad y 1 si lo está.
			$ok = $this->calidad_model->validar_asignacion_actividad($actividad, $persona);
			if (!$ok) {
				$resp = $this->calidad_model->quitar_actividad($id);
				if ($resp) {
					$res = $resp ? [
						'mensaje' => "Actividad Desasignada exitosamente.",
						'tipo' => "success",
						'titulo' => "Proceso Exitoso!"
					] : [
						'mensaje' => "Ha ocurrido un error al desasignar la actividad.",
						'tipo' => "info",
						'titulo' => "Ooops!"
					];
				} else $res = [
					'mensaje' => "Ha ocurrido un error al desasignar la actividad.",
					'tipo' => "info",
					'titulo' => "Ooops!"
				];
			} else $res = [
				'mensaje' => "El usuario no tiene asignada esta actividad.",
				'tipo' => "info",
				'titulo' => "Ooops!"
			];
		} else $res = [
			'mensaje' => 'No tiene Permisos Para Realizar Esta operación.',
			'tipo' => 'error',
			'titulo' => 'Oops.!'
		];
		echo json_encode($res);
	}

	public function listar_estados(){
		if (!$this->Super_estado) $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$actividad = $this->input->post('actividad');
			$data = $this->calidad_model->listar_estados($actividad);
		}
		echo json_encode($data);
	}

  public function activar_notificacion(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Cal") {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->calidad_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$id = $this->calidad_model->get_where('estados_actividades_calidad', ['actividad_id' => $actividad, 'estado_id' => $estado])->row()->id;
					$resp = $this->calidad_model->modificar_datos2(['notificacion' => 1], 'estados_actividades_calidad', $id);
					$res = !$resp ? [
						'mensaje' => "Notificación activada exitosamente.",
						'tipo' => "success",
						'titulo' => "Proceso Exitoso!"
					] : [
						'mensaje' => "Ha ocurrido un error al desasignar el estado.",
						'tipo' => "info",
						'titulo' => "Ooops!"
					];
				} else $res = [
					'mensaje' => "El usuario no tiene asignado este estado.",
					'tipo' => "info",
					'titulo' => "Ooops!"
				];
			}else $resp = [
				'mensaje' => 'No cuenta con permisos para realizar esta acción.',
				'tipo' => 'info',
				'titulo' => 'Ooops!'
			];
		}
		echo json_encode($res);
	}

	public function desactivar_notificacion() {
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Cal") {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->calidad_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$id = $this->calidad_model->get_where('estados_actividades_calidad', ['actividad_id' => $actividad, 'estado_id' => $estado])->row()->id;
					$resp = $this->calidad_model->modificar_datos2(['notificacion' => 0], 'estados_actividades_calidad', $id);
					$res = !$resp
						? ['mensaje'=>"Estado Desasignada exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						: ['mensaje'=>"Ha ocurrido un error al desasignar el estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
				} else $res = ['mensaje'=>"El usuario no tiene asignado este estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
			}else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		}
		echo json_encode($res);
	}

  public function asignar_estado(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->calidad_model->validar_asignacion_estado($estado, $actividad, $persona);
				if ($ok) {
					$data = [
						'estado_id' => $estado,
						'actividad_id' => $actividad,
						'usuario_registra' => $_SESSION['persona']
					];
					$resp = $this->calidad_model->guardar_datos2($data, 'estados_actividades_calidad');
					$res = $resp ? [
						'mensaje' => "Estado asignado exitosamente.",
						'tipo' =>"success",
						'titulo' => "Proceso Exitoso!",
					] : [
						'mensaje' => "Ha ocurrido un error al asignar el estado.",
						'tipo' => "info",
						'titulo' => "Ooops!"
					];
				} else $res = [
					'mensaje' => "El usuario ya tiene asignada esta actividad.",
					'tipo' => "info",
					'titulo' => "Ooops!"
				];
			} else $res = [
				'mensaje' => 'No tiene Permisos Para Realizar Esta operación.',
				'tipo' => 'error',
				'titulo' => 'Oops.!',
			];
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
				$ok = $this->calidad_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$resp = $this->calidad_model->quitar_estado($id);
					$res = $resp ? [
						'mensaje' => "Estado Desasignada exitosamente.",
						'tipo' => "success",
						'titulo' => "Proceso Exitoso!"
					] : [
						'mensaje' => "Ha ocurrido un error al desasignar el estado.",
						'tipo' => "info",
						'titulo' => "Ooops!"
					];
				}else $res = [
					'mensaje' => "El usuario no tiene asignado este estado.",
					'tipo' => "info",
					'titulo' => "Ooops!"
				];
			}else $resp = [
				'mensaje' => 'No cuenta con permisos para realizar esta acción.',
				'tipo' => 'info',
				'titulo' => 'Ooops!'
			];
		}
		echo json_encode($res);
	}


}
