<?php


date_default_timezone_set('America/Bogota');
class ascensos_control extends CI_Controller
{
  var $Super_estado = false;
  var $Super_elimina = 0;
  var $Super_modifica = 0;
  var $Super_agrega = 0;
  var $ruta_archivos_items = "/archivos_adjuntos/ascensos/items";
  public function __construct()
  {
    parent::__construct();
    include('application/libraries/festivos_colombia.php');
    $this->load->model('pages_model');
    $this->load->model('ascensos_model');
    $this->load->model('genericas_model');
    session_start();
    if (isset($_SESSION["usuario"])) {
      $this->Super_estado = true;
      $this->Super_elimina = 1;
      $this->Super_modifica = 1;
      $this->Super_agrega = 1;
    }
  }

  public function index($id = '')
  {
    if ($this->Super_estado) {
      $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'ascensos');
      if (!empty($datos_actividad)) {
        $pages = "ascensos";
        $data['js'] = "Ascensos";
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

  public function crear_solicitud()
  {
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $tipo = $this->input->post('tipo');
        $id_docente = $_SESSION['persona'];
        $ver_sol = $this->verificar_solicitud($id_docente);
        if (empty($ver_sol)) {
          $data = [
            'id_docente' => $id_docente,
            'id_tipo' => $tipo,
            'id_usuario_registra' => $id_docente,
            'id_estado' => 'Asc_Bor_E'
          ];
          $add = $this->ascensos_model->guardar_datos($data, "ascenso_solicitudes");
          if ($add != 0) {
            $resp = ['mensaje' => "Error al guardar la información, contacte con el administrad", 'tipo' => "error", 'titulo' => "Error.!"];
          } else {
            $id_docente = $_SESSION['persona'];
            $solicitud = $this->ascensos_model->consultar_ultima_solicitud($id_docente);
            $id_solicitud = $solicitud->{'id'};
            $tipo_solicitud = $solicitud->{'id_tipo'};
            $id_estado = $solicitud->{'id_estado'};
            $data_est = [
              'id_solicitud' => $id_solicitud,
              'id_estado' => 'Asc_Bor_E',
              'id_usuario_registro' => $_SESSION['persona']
            ];
            $add_est = $this->ascensos_model->guardar_datos($data_est, "ascensos_estados");
            if ($add_est != 0) {
              $resp = ['mensaje' => "Error al guardar la información, contacte con el administrad", 'tipo' => "error", 'titulo' => "Error.!"];
            } else {
              $resp = ['mensaje' => "Informacion Almacenada con Exito.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id_solicitud' => $id_solicitud, 'tipo_solicitud' => $tipo_solicitud, 'estado' => $id_estado];
            }
          }
        } else {
          $resp = ['mensaje' => "Usted ya cuenta con una solicitud pendiente, por favor cancele la solicitud pendiente si desea registrar una nueva.", 'tipo' => "info", 'titulo' => "Oops..!"];
        }
      }
    }
    echo json_encode($resp);
  }

  /*Donde se pintan las acciones en la tabla segun el contenido extraido de la misma*/

  public function listar_solicitudes()
  {

    $resp = [];
    if (!$this->Super_estado) {
      $publicaciones = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Csep" ? true : false;
      $id = $this->input->post('id');
      $id_tipo_solicitud = $this->input->post('id_tipo_solicitud');
      $id_estado_solicitud = $this->input->post('id_estado_solicitud');
      $fecha_inicio = $this->input->post('fecha_inicio');
      $fecha_fin = $this->input->post('fecha_fin');

      $ver_borrador = '<span style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
      $ver_enviado = '<span style="background-color: #f0ad4e;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
      $ver_aceptado = '<span style="background-color: #5cb85c;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
      $ver_rechazado = '<span style="background-color: #d9534f;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';

      $btn_cancelar = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default cancelar" style="color:#d9534f"></span>';
      $btn_aceptar = '<span title="Aceptar" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn btn-default aceptar" style="color:#5cb85c"></span>';
      $btn_rechazar = '<span title="Negar" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn btn-default rechazar" style="color:#d9534f"></span>';
      $btn_adjuntar = '<span title="Adjuntar" data-toggle="popover" data-trigger="hover" class="fa fa-file btn btn-default adjuntar" style="color:#2E79E5"></span>';
      $btn_gen_pdf =  '<span id="" title="Obtener PDF" class="fa fa-file-pdf-o btn btn-default buttons-pdf buttons-html5 btn-danger2 obtener_pdf"></span>';
      $btn_enviado = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
      $btn_invalido = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
      $btn_enviar = '<span title="Enviar" data-toggle="popover" data-trigger="hover" class="fa fa-share btn btn-default enviar" style="color:#2E79E5"></span>';
      $btn_devolver = '<span title="Revisar" data-toggle="popover" data-trigger="hover" class="fa fa-share fa-flip-horizontal btn btn-default revisar" style="color:#d9534f"></span>';

      $solicitudes = $this->ascensos_model->listar_solicitudes($id, $id_tipo_solicitud, $id_estado_solicitud, $fecha_inicio, $fecha_fin);

      foreach ($solicitudes as $row) {

        if ($row['id_estado'] == 'Asc_Bor_E') $row['ver'] = $ver_borrador;
        else if ($row['id_estado'] == 'Asc_Env_E') $row['ver'] = $ver_enviado;
        else if ($row['id_estado'] == 'Asc_Neg_E') $row['ver'] = $ver_rechazado;
        else if ($row['id_estado'] == 'Asc_Ace_E') $row['ver'] = $ver_aceptado;
        else if ($row['id_estado'] == 'Asc_Can_E') $row['ver'] = $ver_rechazado;

        if ($row['id_estado'] == 'Asc_Bor_E') $row['accion'] = "$btn_adjuntar $btn_enviar $btn_cancelar";
        else if ($row['id_estado'] == 'Asc_Env_E') $row['accion'] = $administra ? "$btn_aceptar $btn_rechazar $btn_devolver" : "$btn_enviado";
        else if ($row['id_estado'] == 'Asc_Neg_E') $row['accion'] = "$btn_invalido";
        else if ($row['id_estado'] == 'Asc_Ace_E') $row['accion'] = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Csep" /* ||  $_SESSION["persona"] == $data->{'id_docente'} */ ? "$btn_gen_pdf" : "$btn_invalido";
        else if ($row['id_estado'] == 'Asc_Can_E') $row['accion'] = "$btn_invalido";

        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function verificar_solicitud($id)
  {
    $resp = $this->ascensos_model->verificar_solicitud($id);
    return $resp;
  }

  public function obtener_items()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $publicaciones = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $tipo = $this->input->post('tipo');
      $items = $this->ascensos_model->obtener_items($tipo);
      $secciones = $this->ascensos_model->obtener_secciones($tipo);
      foreach ($secciones as $row) {
        $arr_i = [];
        foreach ($items as $i) {
          if ($i['seccion'] == $row['id_aux']) {
            array_push($arr_i, $i);
          }
        }
        array_push($resp, [
          'nombre' => $row['nombre'],
          'items' => $arr_i
        ]);
      }
    }
    echo json_encode($resp);
  }

  public function listar_archivos_item()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id_item = $this->input->post('item');
      $id_sol = $this->input->post('id_solicitud');
      $id_seccion = $this->input->post('seccion');
      $columna = $this->input->post('id_columna');
      $resp = $this->ascensos_model->listar_archivos_item($id_sol, $id_item, $id_seccion, $columna);
    }
    echo json_encode($resp);
  }

  public function recibir_archivos()
  {
    $solicitud = $this->ascensos_model->consulta_solicitud_id($_POST['solicitud']);
    $ver_estado = $this->verificar_estado($solicitud->{'id_estado'});
    if ($ver_estado) {
      $nombre = $_FILES["file"]["name"];
      $cargo = $this->cargar_archivo("file", $this->ruta_archivos_items, "asc");
      $tipo = $_POST['tipo'];
      if ($cargo[0] == -1) {
        header("HTTP/1.0 400 Bad Request");
        echo ($nombre);
        return;
      }

      if ($tipo == 'item') {
        $data = [
          "id_solicitud" => $_POST['solicitud'],
          "id_referencia" => $_POST['item'],
          "id_seccion" => $_POST['seccion'],
          "tipo" => $tipo,
          "nombre_real" => $nombre,
          "nombre_guardado" => $cargo[1],
          "id_usuario_registra" => $_SESSION['persona'],
        ];
      } else if ($tipo == 'solicitud') {
        $data = [
          "id_solicitud" => $_POST['solicitud'],
          "tipo" => $tipo,
          "columna" => $_POST['columna'],
          "nombre_real" => $nombre,
          "nombre_guardado" => $cargo[1],
          "id_usuario_registra" => $_SESSION['persona']
        ];
      } else if ($tipo == 'formacion') {
        $data = [
          "id_solicitud" => $_POST['solicitud'],
          "tipo" => $tipo,
          "id_referencia" => $_POST['item'],
          "nombre_real" => $nombre,
          "nombre_guardado" => $cargo[1],
          "id_usuario_registra" => $_SESSION['persona']
        ];
      }
      $res = $this->ascensos_model->guardar_datos($data, 'ascensos_adjuntos');

      if ($res != 0) {
        header("HTTP/1.0 400 Bad Request");
        echo ($nombre);
        return;
      }
    } else {
      header("HTTP/1.0 400 Bad Request");
      echo ("Solicitud finalizada");
      return;
    }
    echo json_encode($res);
    return;
  }

  function cargar_archivo($mi_archivo, $ruta, $nombre)
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

  public function listar_formacion_solicitud()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id_solicitud = $this->input->post('id_solicitud');
      $solicitud = $this->ascensos_model->consulta_solicitud_id($id_solicitud);
      $estado = $solicitud->{'id_estado'};
      $tipo = $this->input->post('formacion');
      $formaciones = $this->ascensos_model->listar_formacion_solicitud($id_solicitud, $tipo);
      $btn_eliminar = '<span style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>';
      $btn_inactivo = '<span class="fa fa-toggle-off"></span>';
      foreach ($formaciones as $row) {
        if ($estado == 'Asc_Bor_E') $row['accion'] = $btn_eliminar;
        else $row['accion'] = $btn_inactivo;

        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function obtener_tipo_formacion()
  {
    $parametro = $this->input->post('buscar');
    $formaciones = $this->Super_estado == true ? $this->ascensos_model->obtener_valores_parametro($parametro) : array();
    echo json_encode($formaciones);
  }

  public function obtener_tipos_solicitud()
  {
    $parametro = $this->input->post('id');
    $tipos = $this->Super_estado == true ? $this->ascensos_model->obtener_valores_parametro($parametro) : array();
    echo json_encode($tipos);
  }

  public function obtener_estados_solicitud()
  {
    $parametro = $this->input->post('id');
    $estados = $this->Super_estado == true ? $this->ascensos_model->obtener_valores_parametro($parametro) : array();
    echo json_encode($estados);
  }

  public function obtener_niveles_ingles()
  {
    $parametro = $this->input->post('buscar');
    $niveles  = $this->Super_estado == true ? $this->ascensos_model->obtener_valores_parametro($parametro) : array();
    echo json_encode($niveles);
  }

  public function obtener_categorias_colciencias()
  {
    $parametro = $this->input->post('buscar');
    $categorias = $this->Super_estado == true ? $this->ascensos_model->obtener_valores_parametro($parametro) : array();
    echo json_encode($categorias);
  }

  public function guardar_formacion()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_sol = $this->input->post('id_solicitud');
        $solicitud = $this->ascensos_model->consulta_solicitud_id($id_sol);
        $ver_estado = $this->verificar_estado($solicitud->{'id_estado'});
        $id_formacion = $this->input->post('id_formacion');
        $tipo = $this->input->post('id_tipo_formacion');
        $nombre = '';
        if ($ver_estado) {
          if ($tipo == 'estudio') {
            if ($this->input->post('nombre_formacion')) $nombre = $this->input->post('nombre_formacion');
            else if ($this->input->post('nivel_ingles')) $nombre = $this->input->post('nivel_ingles');
            else $resp = ['mensaje' => "Por favor ingresar o seleccionar el titulo del estudio", 'tipo' => "info", 'titulo' => "Oops.!"];
          } else if ($tipo == 'producto') {
            if ($this->input->post('nombre_formacion')) $nombre = $this->input->post('nombre_formacion');
            else $resp = ['mensaje' => "Debe ingresar el nombre del producto y seleccionar una formación valida", 'tipo' => "info", 'titulo' => "Oops.!"];
          } else {
            $resp = ['mensaje' => "Por favor seleccione un tipo de formación a crear", 'tipo' => "info", 'titulo' => "Oops.!"];
          }

          if ($nombre) {
            $data = [
              'id_solicitud' => $id_sol,
              'id_formacion' => $id_formacion,
              'tipo' => $tipo,
              'nombre' => $nombre,
              'id_usuario_registra' => $_SESSION['persona']
            ];

            $add = $this->ascensos_model->guardar_datos($data, "ascensos_formacion");
            if ($add != 0) {
              $resp = ['mensaje' => "Error al almacenar la información, por favor contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
              $formacion = $this->ascensos_model->ultima_formacion($id_sol);
              $id_form = $formacion->{'id'};
              $resp = ['mensaje' => "Formación guardada con exito.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id_form' => $id_form];
            }
          }
        } else {
          $resp = ['mensaje' => "No es posible realizar esta acción debido a que la solicitud ya ha finalizado", 'tipo' => "info", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function guardar_informacion_solicitud()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_sol = $this->input->post('id_solicitud');
        $solicitud = $this->ascensos_model->consulta_solicitud_id($id_sol);
        $ver_estado = $this->verificar_estado($solicitud->{'id_estado'});
        $experiencia = $this->input->post('a_experiencia');
        $colciencias = $this->input->post('cat_colciencias');
        $cargo_nuevo = $this->input->post('id_nuevo');
        $cargo_actual = $this->input->post('id_actual');
        $scopus = $this->input->post('ind_scopus') ? $this->input->post('ind_scopus') : NULL;
        $indice_scopus = $this->input->post('ind_value') ? $this->input->post('ind_value') : 0;
        $id_colciencias = $this->input->post('id_colciencias');
        $cvlac = $this->input->post('cvlac');
        $tipo = $solicitud->{'id_tipo'};

        if ($ver_estado) {
          if ($tipo == 'Asc_Doc' || $tipo == 'Asc_Ext') {
            if (!$experiencia) $resp = ['mensaje' => "Por favor digite cuantos años de experiencia posee", 'tipo' => "info", 'titulo' => "Oops.!"];
            else if (!$cvlac) $resp = ['mensaje' => "Por favor ingrese la url de su CVLac", 'tipo' => "info", 'titulo' => "Oops.!"];
            else {
              $data = [
                'experiencia' => $experiencia,
                'indice_scopus' => $scopus,
                'cargo_actual' => $cargo_actual,
                'cargo_nuevo' => $cargo_nuevo,
                'indice_scopus_valor' => $indice_scopus,
                'cvlac' => $cvlac
              ];
              $mod = $this->ascensos_model->modificar_datos($data, "ascenso_solicitudes", $id_sol);
              if ($mod != 0) {
                $resp = ['mensaje' => "Error al almacenar información, por favor contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
              } else {
                $resp = ['mensaje' => "Información almacenada con exito.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
              }
            }
          } else if ($tipo == 'Asc_Inv') {
            if (!$experiencia) $resp = ['mensaje' => "Por favor digite cuantos años de experiencia posee", 'tipo' => "info", 'titulo' => "Oops.!"];
            else if (!$colciencias)  $resp = ['mensaje' => "Por favor ingrese la url de verificacion de su categoria en COLCIENCIAS", 'tipo' => "info", 'titulo' => "Oops.!"];
            else if (!$cvlac) $resp = ['mensaje' => "Por favor ingrese la url de su CVLac", 'tipo' => "info", 'titulo' => "Oops.!"];
            else if (!$id_colciencias) $resp = ['mensaje' => "Por favor seleccione su categoria en COLCIENCIAS", 'tipo' => "info", 'titulo' => "Oops.!"];
            else {
              $data = [
                'experiencia' => $experiencia,
                'indice_scopus' => $scopus,
                'cargo_actual' => $cargo_actual,
                'cargo_nuevo' => $cargo_nuevo,
                'categoria_colciencias' => $colciencias,
                'id_colciencias' => $id_colciencias,
                'indice_scopus_valor' => $indice_scopus,
                'cvlac' => $cvlac
              ];
              $mod = $this->ascensos_model->modificar_datos($data, "ascenso_solicitudes", $id_sol);
              if ($mod != 0) {
                $resp = ['mensaje' => "Error al almacenar información, por favor contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
              } else {
                $resp = ['mensaje' => "Información almacenada con exito.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
              }
            }
          }
        } else {
          $resp = ['mensaje' => "No se puede realizar esta acción debido a que la solicitud ya ha finalizado", 'tipo' => "info", 'title' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function obtener_informacion_solicitud()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id_sol = $this->input->post('id_solicitud');
      $resp = $this->ascensos_model->consulta_solicitud_id($id_sol);
    }
    echo json_encode($resp);
  }

  public function cancelar_solicitud()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id_sol = $this->input->post('id');
      $solicitud = $this->ascensos_model->consulta_solicitud_id($id_sol);
      $ver_estado = $this->verificar_estado($solicitud->{'id_estado'});
      $solicitante = $solicitud->{'id_docente'};
      if ($ver_estado) {
        if ($solicitante != $_SESSION['persona'] && $_SESSION['perfil'] != 'Per_Admin') $resp = ['mensaje' => "No cuenta con los permisos para realizar esta acción", 'tipo' => "info", 'titulo' => "Oops.!"];
        else {
          $mod = $this->ascensos_model->modificar_datos(['id_estado' => 'Asc_Can_E', 'estado' => 0], "ascenso_solicitudes", $id_sol);
          if ($mod != 0) {
            $resp = ['mensaje' => "Error al almacenar información, por favor contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
          } else {
            $data_est = [
              'id_solicitud' => $id_sol,
              'id_estado' => 'Asc_Can_E',
              'id_usuario_registro' => $_SESSION['persona']
            ];
            $add_est = $this->ascensos_model->guardar_datos($data_est, "ascensos_estados");
            if ($add_est != 0) {
              $resp = ['mensaje' => "Error al almacenar información, por favor contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
              $resp = ['mensaje' => "Solicitud cancelada con exito.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            }
          }
        }
      } else {
        $resp = ['mensaje' => "No es posible realizar esta acción debido a que la solicitud ya ha finalizado", 'tipo' => "info", 'title' => "Oops.!"];
      }
    }
    echo json_encode($resp);
  }

  public function consultar_solicitudes_docente()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id_docente = $_SESSION['persona'];
      $ver_sol = $this->verificar_solicitud($id_docente);
      if (empty($ver_sol)) {
        $resp = ['pendiente' => 0];
      } else {
        $id_sol = $ver_sol[0]['id'];
        $resp = ['pendiente' => 1, 'solicitud' => $ver_sol[0]['id'], 'tipo' => $ver_sol[0]['id_tipo'], 'estado' => $ver_sol[0]['id_estado']];
      }
    }
    echo json_encode($resp);
  }

  public function buscar_cargo()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $dato = $this->input->post('dato');
      $buscar = "(vp.valor LIKE '%" . $dato . "%' AND vp.idparametro = 2)";
      $cargos = $this->ascensos_model->buscar_cargo($buscar);
      foreach ($cargos as $row) {
        $row['accion'] = '<span title="Elegir" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default elegir" style="color:#39B23B"></span>';

        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function gestionar_solicitud()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_solicitud = $this->input->post('id');
        $estado_nuevo = $this->input->post('estado');
        $mensaje = trim($this->input->post('mensaje'));
        //$mensaje = $this->input->post('mensaje') ? $this->input->post('mensaje') : NULL;
        $continua = true;

        if ($estado_nuevo == "Asc_Neg_E") {
          empty($mensaje) || $mensaje == " " ? $continua = false : false;
        }
        
        if ($continua) {
          $solicitud = $this->ascensos_model->consulta_solicitud_id($id_solicitud);
          $val_est = $this->validar_estado($id_solicitud, $estado_nuevo);
          if ($val_est) {
            $data = ['id_estado' => $estado_nuevo];
            $mod = $this->ascensos_model->modificar_datos($data, "ascenso_solicitudes", $id_solicitud);
            if ($mod != 0) {
              $resp = ['mensaje' => "Error al almacenar información, por favor contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
              $data_est = [
                'id_solicitud' => $id_solicitud,
                'id_estado' => $estado_nuevo,
                'observacion' => $mensaje,
                'id_usuario_registro' => $_SESSION['persona']
              ];
              $add_est = $this->ascensos_model->guardar_datos($data_est, "ascensos_estados");
              if ($add_est != 0) {
                $resp = ['mensaje' => "Error al almacenar información, por favor contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
              } else {
                $resp = ['mensaje' => "Solicitud gestionada con exito.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
              }
            }
          }
        } else {
          $resp = ['mensaje' => "Debe ingresar una observación que describa el motivo de la negación de esta solicitud.", 'tipo' => "warning", 'titulo' => "¡Atención!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function validar_estado($id, $estado)
  {
    $solicitud = $this->ascensos_model->consulta_solicitud_id($id);
    $estado_actual = $solicitud->{'id_estado'};
    $docente = $solicitud->{'id_docente'};
    $admin = $_SESSION['perfil'] == 'Per_Admin' || $_SESSION['perfil'] == 'Per_Csep' ? true : false;
    $resp = false;
    if ($estado_actual == 'Asc_Bor_E' && $estado == 'Asc_Env_E' && $docente == $_SESSION['persona']) $resp = true;
    else if ($estado_actual == 'Asc_Env_E' && $estado == 'Asc_Ace_E' && $admin) $resp = true;
    else if ($estado_actual == 'Asc_Env_E' && $estado == 'Asc_Neg_E' && $admin) $resp = true;
    else if ($estado_actual == 'Asc_Env_E' && $estado == 'Asc_Bor_E' && $admin) $resp = true;

    return $resp;
  }

  public function eliminar_formacion()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id');
        $mod = $this->ascensos_model->modificar_datos(['estado' => 0], "ascensos_formacion", $id);
        if ($mod != 0) {
          $resp = ['mensaje' => "Error al almacenar información, por favor contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
        } else {
          $resp = ['mensaje' => "Solicitud gestionada con exito.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function obtener_correos()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $resp = $this->ascensos_model->obtener_correo_talento_humano('ParCodTal')->{'valor'};
    }
    echo json_encode($resp);
  }

  public function obtener_observacion()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $resp = $this->ascensos_model->obtener_observacion($id)->{'observacion'};
    }
    echo json_encode($resp);
  }

  public function obtener_info_docente()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $resp = $this->ascensos_model->obtener_info_docente($id);
    }
    echo json_encode($resp);
  }

  public function listar_historial_estados()
  {
    $resp = [];
    if (!$this->Super_estado == true) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id_solicitud');
      $resp = $this->ascensos_model->listar_historial_estados($id);
    }
    echo json_encode($resp);
  }

  public function verificar_estado($estado)
  {
    if ($estado != 'Asc_Bor_E') return false;
    else return true;
  }


  public function descargar_acta($id)
  {
    if ($this->Super_estado == true) {
      $data = $this->ascensos_model->consulta_solicitud_id($id);
      if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Csep" /* ||  $_SESSION["persona"] == $data->{'id_docente'} */) {
        $array = ["persona" => $data->{'nombre_completo'}, "id" => $id];
        return $this->load->view('templates/descargar_acta_ascensos', $array);
      }
    }
    header('Location:' . base_url() . 'index.php/ascensos');
  }
}
