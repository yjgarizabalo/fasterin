<?php
class bienestar_control extends CI_Controller
{
  var $Super_estado = false;
  var $Super_elimina = 0;
  var $Super_modifica = 0;
  var $Super_agrega = 0;
  var $admin = false;
  var $super_admin = false;
  var $administra = false;
  var $funcionario = false;

  public function __construct()
  {
    parent::__construct();
    include('application/libraries/festivos_colombia.php');
    $this->load->model('bienestar_model');
    $this->load->model('genericas_model');
    date_default_timezone_set('America/Bogota');
    session_start();
    if (isset($_SESSION["usuario"])) {
      $this->Super_estado = true;
      $this->Super_elimina = 1;
      $this->Super_modifica = 1;
      $this->Super_agrega = 1;
      $this->administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Bin' ? true : false;
      $this->funcionario = $_SESSION['perfil'] == 'Bin_Fun' ? true : false;
    }
  }
  public function index($id = '')
  {

    if ($this->Super_estado) {
      $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'bienestar');

      if (!empty($datos_actividad)) {
        $data['Bin_Cla'] = !$this->administra ? $this->validar_fechas('Bin_Cla', null, 'Y-m-d')['total'] : 0;
        $pages = "bienestar";
        $data['js'] = "Bienestar";
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

  public function obtener_departamentos()
  {
    $buscar = $this->input->post('buscar');
    $departamentos = $this->Super_estado == true ? $this->bienestar_model->obtener_departamentos($buscar) : array();
    echo json_encode($departamentos);
  }

  public function obtener_programas_departamento()
  {
    $id = $this->input->post("id");
    $programas = $this->Super_estado == true ? $this->bienestar_model->obtener_programas_departamento($id) : array();
    echo json_encode($programas);
  }

  public function listar_ubicaciones()
  {
    $id_lugar = $this->input->post("id_lugar");
    $resp = $this->Super_estado == true ? $this->bienestar_model->listar_ubicaciones($id_lugar) : array();
    echo json_encode($resp);
  }

  public function get_tematicas_disponibles(){
    if (!$this->Super_estado) {
      $resp = [];
    } else {
      $fecha = $this->input->post("fecha");
      $duracion = $this->input->post("duracion");
      $fecha_i = $this->validateDate($fecha, 'Y-m-d H:i:s');
      $duracion_min = $this->genericas_model->obtener_valor_parametro_id($duracion)[0]['valory'];
      $fecha_fin = $this->obtener_fecha_fin($fecha, $duracion_min);
      if (!$fecha_i) $resp = [];
      else $resp = $this->bienestar_model->get_tematicas_disponibles($fecha, $fecha_fin);
    }
    echo json_encode($resp);
  }

  public function obtener_estrategias()
  {
    $filtro = $this->input->post("filtro");
    $id_estrategia = $this->input->post("id_estrategia");
    $estrategias = $this->Super_estado == true ? $this->bienestar_model->obtener_estrategias($id_estrategia, $filtro) : array();
    echo json_encode($estrategias);
  }

  function get_nombre_dia($fecha)
  {
    $fechats = strtotime($fecha);
    //lo devuelve en numero 0 domingo, 1 lunes,....
    switch (date('w', $fechats)) {
      case 0:
        return "Dia_Dom";
        break;
      case 1:
        return "Dia_Lun";
        break;
      case 2:
        return "Dia_Mar";
        break;
      case 3:
        return "Dia_Mie";
        break;
      case 4:
        return "Dia_Jue";
        break;
      case 5:
        return "Dia_Vie";
        break;
      case 6:
        return "Dia_Sab";
        break;
    }
  }

  public function guardar_solicitud()
  {

    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $fecha_inicio = $this->input->post('fecha_inicio');
        $duracion = $this->input->post('duracion');
        $id_ubicacion = $this->input->post('id_ubicacion');
        $id_lugar = $this->input->post('id_lugar');
        $cod_materia = $this->input->post('cod_materia');
        $materia_grupo = $this->input->post('materia_grupo');
        $cod_programa = $this->input->post('cod_programa');
        $programa = $this->input->post('programa');
        $semestre = $this->input->post('semestre');
        $id_tematica = $this->input->post('id_tematica');
        $id_usuario_registra = $_SESSION['persona'];
        $estudiantes = $this->input->post('estudiantes');
        $id_tipo_solicitud = $this->input->post('id_tipo_solicitud');
        $id_anterior = $this->input->post('id_anterior');
        $duracion = $this->input->post('id_duracion');
        $telefono = $this->input->post('telefono');
        // $id_tipo_solicitud = $this->input->post('id_tipo_solicitud');
        $solicitante_anterior = $this->input->post('solicitante_anterior');
        $solicitante_anterior = $solicitante_anterior ? $solicitante_anterior : null;
        $data_estudiantes = array();
        $id_programa = null;

        if ($id_tipo_solicitud == "Bin_Cla") {
          $str = $this->verificar_campos_string(['Teléfono' => $telefono, 'Fecha Inicio' => $fecha_inicio, 'Duración' => $duracion, 'Asignatura/Grupo' => $materia_grupo, 'Temática' => $id_tematica, 'Lugar' => $id_lugar, 'Ubicación' => $id_ubicacion, 'Programa' => $programa, 'Semestre' => $semestre, 'Teléfono' => $telefono]);

          foreach ($estudiantes as $estudiante) {
            array_push($data_estudiantes, [
              'id_solicitud' => 0,
              'id_persona' => $estudiante['id'],
              'id_usuario_registra' => $id_usuario_registra
            ]);
          }

          $consulta = $this->bienestar_model->programasGenericas($cod_programa);
          if ($consulta) $id_programa = $consulta->{'id'};
          else {
            $data_vp = [
              'idparametro' => 3,
              'id_aux' => $cod_programa,
              'valor' => $programa,
              'valorx' => 'Ninguna',
              'valory' => 2,
              'usuario_registra' => $id_usuario_registra,
            ];
            $add_vp = $this->bienestar_model->guardar_datos($data_vp, 'valor_parametro');
            if ($add_vp == 1) {
              $resp = ['mensaje' => "Error al guardar la información del parametro, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
              $solicitud_vp = $this->bienestar_model->traer_ultima_solicitud($id_usuario_registra, 'valor_parametro', 'usuario_registra');
              $id_programa = $solicitud_vp->{'id'};
            }
          }

          if (is_array($str)) {
            $resp = ['mensaje' => "El campo " . $str['field'] . "  no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
          } else {

            $fecha_i = $this->validateDate($fecha_inicio, 'Y-m-d H:i:s');
            $duracion_min = $this->genericas_model->obtener_valor_parametro_id($duracion)[0]['valory'];
            $fecha_fin = $this->obtener_fecha_fin($fecha_inicio, $duracion_min);
            $id_estrategia = $this->bienestar_model->obtener_id_permiso($id_tematica);
            $val_fecha = !$this->administra ? $this->validar_fechas($id_tipo_solicitud, $fecha_inicio, 'Y-m-d') : ['sw' => true];
            $disponibilidad = $this->bienestar_model->fechaDisponible($fecha_inicio, $fecha_fin, $id_tematica);

            if (!$fecha_i) {
              $resp = ['mensaje' => "Por favor seleccione fechas validas y superior a la fecha actual.", 'tipo' => "info", 'titulo' => "Oops."];
            } else if (!$val_fecha['sw']) {
              $resp = ['mensaje' => "Su solicitud debe tener " . $val_fecha['dias_solicitud'] . "  dias de anticipacion.", 'tipo' => "info", 'titulo' => "Oops."];
            } else if ($disponibilidad) {
              $resp = ['mensaje' => "La fecha seleccionada no se encuentra disponible.", 'tipo' => "info", 'titulo' => "Oops.!", 'disponibilidad' => $disponibilidad];
            } else if (empty($data_estudiantes)) {
              $resp = ['mensaje' => "No tiene estudiantes asignados.", 'tipo' => "info", 'titulo' => "Oops.!"];
            } else if (is_null($id_programa)) {
              $resp = ['mensaje' => "Error al guardar la información del parametro, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else if (!$id_estrategia) {
              $resp = ['mensaje' => "Error al guardar la información de la estrategia a realizar, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else if (is_null($duracion_min)) {
              $resp = ['mensaje' => "Error al guardar la duracion de la clase, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
              $dia = $this->get_nombre_dia($fecha_inicio);
              $dia_f = $this->get_nombre_dia($fecha_fin);
              $funcionarioDisponible = $this->funcionarioDisponibilidad($id_tematica, $fecha_inicio, $fecha_fin, $cod_materia, $dia, $dia_f); // para asignacion de funcionario automático
              // $funcionarioDisponible = $this->bienestar_model->funcionariosTematicas($id_tematica,$cod_materia,$fecha_inicio,$fecha_fin); 
              if ($funcionarioDisponible) {
                $id_estrategia = $id_estrategia->{'vp_secundario_id'};
                $id_anterior = $id_anterior == '' ? null : $id_anterior;

                $data = [
                  'fecha_inicio' => $fecha_inicio,
                  'fecha_fin' => $fecha_fin,
                  'id_ubicacion' => $id_ubicacion,
                  'id_lugar' => $id_lugar,
                  'materia' => $materia_grupo,
                  'cod_materia' => $cod_materia,
                  'id_programa' => $id_programa,
                  'semestre' => $semestre,
                  'id_estrategia' => $id_estrategia,
                  'id_tematica' => $id_tematica,
                  'id_usuario_registra' => $id_usuario_registra,
                  'id_solicitante' => $id_usuario_registra,
                  'id_anterior' => $id_anterior,
                  'telefono' => $telefono,
                  'id_tipo_solicitud' => $id_tipo_solicitud,
                ];

                $add = $this->bienestar_model->guardar_datos($data, 'bienestar_solicitudes');
                if ($add == 1) {
                  $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                } else {
                  $solicitud = $this->bienestar_model->traer_ultima_solicitud($id_usuario_registra, 'bienestar_solicitudes', 'id_usuario_registra');
                  for ($i = 0; $i < count($data_estudiantes); $i++)  $data_estudiantes[$i]['id_solicitud'] = $solicitud->{'id'};
                  $datos_solicitud = $this->bienestar_model->consulta_solicitud_id($solicitud->{'id'});

                  $data_estado  = ['id_solicitud' => $solicitud->{'id'}, 'id_estado' => 'Bin_Sol_E', 'id_usuario_registra' => $id_usuario_registra];
                  $add_estado = $this->bienestar_model->guardar_datos($data_estado, 'bienestar_estados');

                  $resp = ['mensaje' => "La solicitud fue guardada de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'tipo_solicitud' => $datos_solicitud->{'tipo_solicitud'}, 'solicitante' => $datos_solicitud->{'solicitante'}, 'correo' => $datos_solicitud->{'correo'}, 'fecha' => $fecha_inicio, 'solicitud_id' => $solicitud->{'id'}];

                  if (!empty($data_estudiantes)) {
                    $add_estudiantes = $this->bienestar_model->guardar_datos($data_estudiantes, 'bienestar_estudiantes', 2);
                    //exit(json_encode($add_estudiantes));
                    if ($add_estudiantes == 1) $resp = ['mensaje' => "Error al guardar los estudiantes, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                  }
                  if ($id_anterior) {
                    $data_anterior = ['id_estado_sol' => 'Bin_Rep_E'];
                    $mod = $this->bienestar_model->modificar_datos($data_anterior, 'bienestar_solicitudes', $id_anterior);
                    if ($mod == 1) {
                      $resp = ['mensaje' => "Error al guardar la información anterior, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                    }
                    $data_estado  = ['id_solicitud' => $id_anterior, 'id_estado' => 'Bin_Rep_E', 'id_usuario_registra' => $id_usuario_registra];
                    $add_estado = $this->bienestar_model->guardar_datos($data_estado, 'bienestar_estados');
                  }
                  $funcionarioDisponible['id_solicitud'] = $solicitud->{'id'};    // para asignacion de funcionario automático                        
                  $funcionarioDisponible['id_usuario_asigna'] = $id_usuario_registra;

                  $add_funcionario = $this->bienestar_model->guardar_datos($funcionarioDisponible, 'bienestar_funcionarios');

                  $data_sol = ['id_estado_sol' => 'Bin_Rev_E'];
                  $estado_funcionario_sol = $this->bienestar_model->modificar_datos($data_sol, 'bienestar_solicitudes', $solicitud->{'id'});

                  $data_estado_fun  = ['id_solicitud' => $solicitud->{'id'}, 'id_estado' => 'Bin_Rev_E', 'id_usuario_registra' => $id_usuario_registra];
                  $add_estado = $this->bienestar_model->guardar_datos($data_estado_fun, 'bienestar_estados');
                }
              } else $resp = ['mensaje' => "No hay funcionarios disponibles para atender su solicitud en la hora y fecha seleccionada, intente de nuevo.", 'tipo' => "info", 'titulo' => "Oops..!"];
            }
          }
        } else $resp = ['mensaje' => "Error con el tipo de solicitud, contacte al administrador.", 'tipo' => "error", 'titulo' => "Oops..!", 'id_tipo_solicitud' => $id_tipo_solicitud];
      }
    }
    echo json_encode($resp);
  }

  public function modificar_solicitud()
  {

    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $fecha_inicio = $this->input->post('fecha_inicio');
        $duracion = $this->input->post('id_duracion');
        $id_solicitud = $this->input->post('id_solicitud');
        $id_tipo_solicitud = $this->input->post('id_tipo_solicitud');
        $id_ubicacion = $this->input->post('id_ubicacion');
        $id_lugar = $this->input->post('id_lugar');
        $id_tematica = $this->input->post('id_tematica');
        $id_usuario_registra = $_SESSION['persona'];
        $telefono = $this->input->post('telefono');
        $tipo = $this->input->post('tipo');
        $observaciones = $this->input->post('observaciones');
        $id_estado_solicitud = $this->input->post('id_estado_solicitud');
        $admin = $_SESSION['perfil'];
        $sw = true;
        $str = $this->verificar_campos_string(['Lugar' => $id_lugar, 'Ubicación' => $id_ubicacion, 'Teléfono' => $telefono, 'Temática' => $id_tematica]);
        $solicitud = $this->bienestar_model->consulta_solicitud_id($id_solicitud);
        $fecha_inicio_db = date('Y-m-d H:i', strtotime($solicitud->{'fecha_inicio'}));

        if (is_array($str)) {
          $resp = ['mensaje' => "El campo " . $str['field'] . "  no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else if (empty($observaciones) && $this->administra) {
          $resp = ['mensaje' => "El campo observaciones no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $id_estrategia = $this->bienestar_model->obtener_id_permiso($id_tematica);

          if (!$id_estrategia) {
            $resp = ['mensaje' => "Error al guardar la información de la estrategia a realizar, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            $sw = false;
          } else if (($fecha_inicio_db == $fecha_inicio) && ($solicitud->{'id_duracion'} == $duracion) && ($solicitud->{'id_lugar'} == $id_lugar) && ($solicitud->{'id_ubicacion'} == $id_ubicacion) && ($solicitud->{'id_tematica'} == $id_tematica) && ($solicitud->{'telefono'} == $telefono)) {
            $resp = ['mensaje' => "Debe realizar alguna modificacion en la solicitud.", 'tipo' => "info", 'titulo' => "Oops.!"];
            $sw = false;
          } else {
            $id_estrategia = $id_estrategia->{'vp_secundario_id'};

            if ($tipo == 'modificar' && $sw) {
              if ($id_estado_solicitud == 'Bin_Sol_E') {
                $data = [
                  'id_ubicacion' => $id_ubicacion,
                  'id_lugar' => $id_lugar,
                  'id_estrategia' => $id_estrategia,
                  'id_tematica' => $id_tematica,
                  'telefono' => $telefono,
                  'id_usuario_modifica' => $id_usuario_registra,
                  'observacion_mod' => $observaciones ? $observaciones : 'Cambio del solicitante'
                ];
              } else $sw = false;
            } else if ($tipo == 'reprogramar' && $sw) {

              $str_rep = $this->verificar_campos_string(['Fecha Inicio' => $fecha_inicio, 'Duracion' => $duracion, 'Observaciones' => $observaciones, 'Tematica' => $id_tematica]);

              if (is_array($str_rep)) {
                $resp = ['mensaje' => "El campo " . $str_rep['field'] . "  no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
                $sw = false;
              } else {
                $fecha_i = $this->validateDate($fecha_inicio, 'Y-m-d H:i:s');
                $val_fecha = !$this->administra ? $this->validar_fechas($id_tipo_solicitud, $fecha_inicio, 'Y-m-d') : ['sw' => true];
                $valido = $this->validar_estado($id_solicitud, 'Bin_Rep_E');
                $duracion_min = $this->genericas_model->obtener_valor_parametro_id($duracion)[0]['valory'];
                $fecha_fin = $this->obtener_fecha_fin($fecha_inicio, $duracion_min);
                $disponibilidad = $this->bienestar_model->fechaDisponible($fecha_inicio, $fecha_fin, $id_tematica);
                //validar disponibilidad del funcionario asignado        
                $dia = $this->get_nombre_dia($fecha_inicio);
                $dia_f = $this->get_nombre_dia($fecha_fin);
                $disponibilidad_func = $this->funcionarioDisponibilidad($id_tematica, $fecha_inicio, $fecha_fin, $solicitud->{'cod_materia'}, $dia, $dia_f, $id_solicitud);
                if ($disponibilidad) {
                  $resp = ['mensaje' => "No hay disponibilidad en la fecha seleccionada: ", 'tipo' => "no_disponible", 'disponibilidad' => $disponibilidad];
                  $sw = false;
                } else if (!$fecha_i) {
                  $resp = ['mensaje' => "Por favor seleccione fechas validas y superior a la fecha actual.", 'tipo' => "info", 'titulo' => "Oops."];
                  $sw = false;
                } else if (!$val_fecha['sw']) {
                  $resp = ['mensaje' => "Su solicitud debe tener " . $val_fecha['dias_solicitud'] . "  dias de anticipacion.", 'tipo' => "info", 'titulo' => "Oops."];
                  $sw = false;
                } else if ($disponibilidad_func == 0) {
                  $resp = ['mensaje' => "No hay funcionario disponible para atender la solicitud en la fecha seleccionada!", 'tipo' => "info", 'titulo' => "Oops."];
                  $sw = false;
                }
                if ($valido && $sw) {
                  $data = [
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin,
                    'id_estado_sol' => 'Bin_Rep_E',
                    'id_tematica' => $id_tematica,
                    'telefono' => $telefono,
                    'observacion_mod' => $observaciones,
                    'id_usuario_modifica' => $id_usuario_registra,
                  ];
                } else $sw = false;
              }
            }
            if ($sw) {
              $add = $this->bienestar_model->modificar_datos($data, 'bienestar_solicitudes', $id_solicitud);
              if ($add != 0) {
                $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
              } else {
                $data_estado  = ['id_solicitud' => $id_solicitud, 'id_estado' => 'Bin_Rep_E', 'id_usuario_registra' => $id_usuario_registra];
                $add_estado = $this->bienestar_model->guardar_datos($data_estado, 'bienestar_estados');

                $funcionarioDisponible = array();
                $data_fun  = ['id_usuario_retira' => $id_usuario_registra, 'estado' => 0];
                $funcionario_solicitud = $this->bienestar_model->validar_funcionario_asignado($id_solicitud, $fecha_inicio, $fecha_fin, $dia, $dia_f);
                if ($funcionario_solicitud) { // se consulta si funcionario asignado tiene disponibilidad y se desahabilita los que no lo estan 
                  $filtro = 1;
                  foreach ($funcionario_solicitud as $fun) {
                    $funcionarioDisponible['id'] = $fun['id'];
                  }
                  if ($funcionarioDisponible) $mod_funcionario = $this->bienestar_model->eliminar_funcionarios_solicitud($data_fun, $id_solicitud, $funcionarioDisponible, $filtro);
                } else { // se asigna funcionario nuevo y se desahabilita funcionario anterior
                  $filtro = 2;
                  $funcionarioDisponible = $this->funcionarioDisponibilidad($id_tematica, $fecha_inicio, $fecha_fin, $solicitud->{'cod_materia'}, $dia, $dia_f);
                  if ($funcionarioDisponible > 0) {
                    $funcionarioDisponible['id_solicitud'] = $id_solicitud;
                    $funcionarioDisponible['id_usuario_asigna'] = $id_usuario_registra;
                    $add_funcionario = $this->bienestar_model->guardar_datos($funcionarioDisponible, 'bienestar_funcionarios');
                    if ($add_funcionario == 0) {
                      $fun_asignado = $this->bienestar_model->traer_ultima_solicitud($id_usuario_registra, 'bienestar_funcionarios', 'id_usuario_asigna');
                      $mod = $this->bienestar_model->eliminar_funcionarios_solicitud($data_fun, $id_solicitud, $fun_asignado->{'id'}, $filtro);
                    }
                  } else {
                    $resp = ['mensaje' => "No hay funcionario disponible para atender la solicitud en la fecha seleccionada!", 'tipo' => "info", 'titulo' => "Oops."];
                  }
                }

                $resp = ['mensaje' => "La solicitud fue modificada de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'motivos' => $observaciones, 'tipo_solicitud' => $solicitud->{'tipo_solicitud'}];
              }
            } else {
              if (!$resp) $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            }
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function verificar_campos_numericos($array)
  {
    foreach ($array as $row) {
      if (!is_numeric($row)) {
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

  function validateDate($date, $format = 'Y-m-d H:i:s')
  {
    $fecha_actual = date($format);
    $d = DateTime::createFromFormat($format, $date);
    return ($d->format($format) < $fecha_actual) ? false : $d && $d->format($format) == $date;
  }
  public function validateFechaMayor($fecha_inicio, $fecha_fin)
  {
    ($fecha_fin >= $fecha_inicio) ? $resp = 1 : $resp = -1;
    return $resp;
  }

  public function listar_solicitudes()
  {
    if ($this->Super_estado == false) {
      echo json_encode("sin_session");
      return;
    }
    $id = $this->input->post("id");
    $estrategia = $this->input->post("estrategia");
    $estado = $this->input->post("estado");
    $fecha = $this->input->post("fecha");
    $fecha_2 = $this->input->post("fecha_2");
    $excel = 0;
    $data = $this->bienestar_model->listar_solicitudes($id, $estrategia, $estado, $fecha, $fecha_2, $excel);

    $perfil = $_SESSION['perfil'];
    $persona = $_SESSION['persona'];
    $solicitudes = array();
    $ver_solicitado = '<span  style="background-color: #ffff;color: #000;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
    $ver_azul = '<span  style="background-color: #f0ad4e;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
    $ver_amarillo = '<span  style="background-color: #f0ad4e;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
    $ver_rojo = '<span  style="background-color: #d9534f;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
    $ver_finalizado = '<span  style="background-color: #39b23b;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
    $ver_tramite = '<span  style="background-color: #6e1f7c;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
    $btn_cerrada = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
    $btn_aprobar = '<span title="Finalizar" data-toggle="popover" data-trigger="hover" style="color: #00cc00;margin-left: 5px" class="pointer fa fa-check btn btn-default aprobar"></span>';
    $btn_tramitar = '<span title="Tramitar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px" class="pointer fa fa-retweet btn btn-default tramitar"></span>';
    $btn_asistencia = '<span title="Tomar Asistencia" data-toggle="popover" data-trigger="hover" style="color: #6e1f7c;margin-left: 5px" class="pointer fa fa-list-ul btn btn-default asistencia"></span>';
    $btn_cancelar = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px" class="pointer fa fa-remove btn btn-default cancelar"></span>';
    $btn_negar = '<span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px" class="pointer fa fa-ban btn btn-default negar"></span>';
    $btn_disponibilidad = '<span title="Revisar" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-calendar btn btn-default disponibilidad"></span>';
    $btn_reprogramar = '<span title="Reprogramar" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;margin-left: 5px" class="pointer fa fa-calendar-times-o btn btn-default reprogramar"></span>';
    $btn_finalizado = '<span title="Finalizar" data-toggle="popover" data-trigger="hover" style="color: #39b23b;margin-left: 5px" class="pointer fa fa-check btn btn-default finalizar"></span>';
    // $btn_encuesta = '<span title="Encuestas" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;margin-left: 5px" class="pointer fa fa-star btn btn-default encuesta"></span>';
    $btn_modificar = '<span title="Modificar Temática" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';

    foreach ($data as $row) {
      if ($row['id_tipo_solicitud'] == 'Bin_Cla') {
        $row['ver'] = $ver_solicitado;
        $row['accion'] = $btn_cerrada;
        $estado = $row['id_estado_sol'];
        if ($estado == 'Bin_Sol_E') {
          if ($perfil == 'Per_Admin' || $perfil == 'Per_Bin') $row['accion'] = $btn_disponibilidad . ' ' . $btn_cancelar;
          else if ($row['id_solicitante'] == $persona) $row['accion'] = $btn_modificar . ' ' . $btn_cancelar;
          else $row['accion'] = $btn_cerrada;
        } else if ($estado == 'Bin_Can_E') {
          $row['ver'] = $ver_rojo;
        } else if ($estado == 'Bin_Neg_E') {
          $row['ver'] = $ver_rojo;
        } else if ($estado == 'Bin_Rev_E') {
          $row['ver'] = $ver_amarillo;
          if ($perfil == 'Per_Admin' || $perfil == 'Per_Bin') $row['accion'] = $btn_tramitar . ' ' . $btn_reprogramar . ' ' . $btn_cancelar;
          else if ($perfil == 'Bin_Fun')  $row['accion'] = $btn_tramitar;
          else if ($row['id_solicitante'] == $persona) $row['accion'] = $btn_modificar . ' ' . $btn_cancelar;
          // else $row['accion'] = $btn_cancelar;
        } else if ($estado == 'Bin_Rep_E') {
          $row['ver'] = $ver_amarillo;
          if ($perfil == 'Per_Admin' || $perfil == 'Per_Bin') $row['accion'] = $btn_tramitar . ' ' . $btn_reprogramar . ' ' . $btn_cancelar;
          else if ($perfil == 'Bin_Fun')  $row['accion'] = $btn_tramitar;
          else if ($row['id_solicitante'] == $persona) $row['accion'] = $btn_modificar . ' ' . $btn_cancelar;
          // else $row['accion'] = $btn_cancelar;
        } else if ($estado == 'Bin_Tra_E') {
          $row['ver'] = $ver_tramite;
          if ($perfil == 'Per_Admin' || $perfil == 'Per_Bin' || $perfil == 'Bin_Fun') $row['accion'] = $btn_finalizado . ' ' . $btn_asistencia;
          else $row['accion'] = $btn_cerrada;
        } else if ($estado == 'Bin_Fin_E') {
          $row['ver'] = $ver_finalizado;
          $row['accion'] = $btn_cerrada;
        }
      }
      array_push($solicitudes, $row);
    }
    echo json_encode($solicitudes);
  }

  public function obtener_materias_por_docente()
  {
    $this->load->model('personas_model');
    $this->load->model('pages_model');
    $materias = array();
    $newdata = [];
    $id = $this->input->post('id');
    $resp = $this->Super_estado ? $this->personas_model->obtener_Datos_persona($id) : array();
    if (!empty($resp)) {
      $identificacion = $resp[0]['identificacion'];
      //$materias = $this->Super_estado ? $this->bienestar_model->obtener_materias_por_docente($identificacion) : array();
      $materias = $this->Super_estado ? $this->pages_model->get_materias_por_docente_sicuc($identificacion) : array();
      if ($materias) {
        foreach ($materias['data'] as $row) {
          $row['valor'] = $row['materia'] . " / " . $row['grupo'];
          array_push($newdata, $row);
        }
      }
    }
    echo json_encode($newdata);
  }

  public function obtener_estudiantes_por_materia()
  {
    $this->load->model('personas_model');
    $this->load->model('pages_model');
    $estudiantes = array();
    if ($this->Super_estado == true) {
      $materia = $this->input->post('materia');
      if (empty($materia) || $materia == "") {
        $estudiantes = [];
      } else {
        //$estudiantes = $this->Super_estado ? $this->bienestar_model->obtener_estudiantes_por_materia($materia) : array();
        $data = $this->pages_model->obtener_estudiantes_por_materia_sicuc($materia); // obtiene los estudiantes por materia en sicuc
        //exit(json_encode($data));
        $info = $data[1]; // identificacion de los estudiantes obtenidos de sicuc
        $estudiantes = $this->pages_model->obtener_id_estudiantes($info); // consulta los visitantes para obtener el id de cada uno
      }
    }
    echo json_encode($estudiantes);
  }

  public function buscar_estudiante()
  {
    $personas = array();
    if ($this->Super_estado == true) {
      $dato = $this->input->post('dato');
      if (!empty($dato)) $personas = $this->bienestar_model->buscar_estudiante($dato);
    } else {
      $personas = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }
    echo json_encode($personas);
  }

  public function buscar_persona()
  {
    $personas = array();
    if ($this->Super_estado == true) {
      $dato = $this->input->post('dato');
      $id_solicitud = $this->input->post('id_solicitud');
      $filtro = $this->input->post('filtro_funcionario');
      $id_tematica = $this->input->post('id_tematica');
      $fecha_inicio = $this->input->post('fecha_inicio');
      $fecha_fin = $this->input->post('fecha_fin');
      $dia = $this->get_nombre_dia($fecha_inicio);
      $dia_f = $this->get_nombre_dia($fecha_fin);
      if (!empty($dato) || $filtro) $personas = $this->bienestar_model->buscar_persona($dato, $filtro, $id_solicitud, $id_tematica, $fecha_inicio, $fecha_fin, $dia, $dia_f);
    } else {
      $personas = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }
    echo json_encode($personas);
  }

  public function listar_estudiantes_solicitud()
  {
    $asistencia_ = $this->input->post("asistencia_");
    $resp = array();
    if ($this->Super_estado == true || $asistencia_ == 'si') {
      $id = $this->input->post("id");
      $data = $this->bienestar_model->listar_estudiantes_solicitud($id);

      $estudiantes = array();

      $btn_cerrada = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off pointer"></span>';
      $btn_eliminar = '<span style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>';
      $btn_firmar = '<span title="Firmar asistencia" data-toggle="popover" style="color: #00cc00;margin-left: 5px" data-trigger="hover" class="fa fa-check btn btn-default pointer firmar"></span>';
      $btn_asistio = '<span title="Asistio" data-toggle="popover" style="color: ##337ab7;margin-left: 5px" data-trigger="hover" class="fa fa-pencil"></span>';

      foreach ($data as $row) {
        if (!$asistencia_) {
          $perfil = $_SESSION['perfil'];
          $persona = $_SESSION['persona'];
          if (($row['id_estado_sol'] == "Bin_Sol_E" || $row['id_estado_sol'] == "Bin_Rev_E") && ($this->administra || $this->funcionario || $row['usuario_registra'] == $persona)) $row['accion'] = $btn_eliminar;
          else $row['accion'] = $btn_cerrada;
        } else {
          if (!$row['codigo_acceso']) $row['accion'] = $btn_firmar;
          else $row['accion'] = $btn_asistio;
        }
        array_push($estudiantes, $row);
      }

      echo json_encode($estudiantes);
    }
  }

  public function eliminar_estudiante_solicitud()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_elimina == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $id_solicitud = $this->input->post("id_solicitud");
        $solicitud = $this->bienestar_model->consulta_solicitud_id($id_solicitud);

        $estado_actual = $solicitud->{'id_estado_sol'};
        if ($estado_actual == 'Bin_Sol_E' || $estado_actual == 'Bin_Rev_E' || $estado_actual == 'Bin_Rep_E') {
          $estudiantes =  count($this->bienestar_model->listar_estudiantes_solicitud($id_solicitud));
          if ($estudiantes == 1) {
            $resp = ['mensaje' => "Su solicitud debe tener por lo menos un estudiante.", 'tipo' => "info", 'titulo' => "Oops.!"];
          } else {
            $fecha = date("Y-m-d H:i");
            $usuario = $_SESSION["persona"];
            $data = [
              "id_usuario_elimina" => $usuario,
              "fecha_elimina" => $fecha,
              "estado" => 0,
            ];
            $resp = ['mensaje' => "El estudiante fue eliminado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            $del = $this->bienestar_model->modificar_datos($data, 'bienestar_estudiantes', $id);
            if ($del != 0) $resp = ['mensaje' => "Error al eliminar al estudiante, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          }
        } else {
          $resp = ['mensaje' => "No es posible realizar esta acción ya que La solicitud se encuentra en tramite o terminada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }
  public function guardar_estudiante_nuevo()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_persona = $this->input->post('id_persona');
        $id_solicitud = $this->input->post('id_solicitud');
        $identificacion = $this->input->post('identificacion');
        $id_usuario_registra = $_SESSION['persona'];
        $solicitud = $this->bienestar_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud->{'id_estado_sol'};

        if ($estado_actual == 'Bin_Sol_E' || $estado_actual == 'Bin_Rev_E' || $estado_actual == 'Bin_Rep_E') {
          $existe = $this->bienestar_model->estudiante_solicitud($identificacion, $id_solicitud);
          if ($existe) {
            $resp = ['mensaje' => "El estudiante ya se encuentra asignado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          } else {
            $data_persona = [
              'id_solicitud' => $id_solicitud,
              'id_persona' => $id_persona,
              'id_usuario_registra' => $id_usuario_registra,
            ];
            $add = $this->bienestar_model->guardar_datos($data_persona, 'bienestar_estudiantes');
            $resp = ['mensaje' => "El estudiante fue asignado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            if ($add == 1) $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          }
        } else {
          $resp = ['mensaje' => "No puede realizar esta acción debido a que la solicitud se encuentra en proceso o ya fue finalizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function consulta_solicitud_id()
  {
    $id = $this->input->post("id");
    $resp = $this->Super_estado ? $this->bienestar_model->consulta_solicitud_id($id) : array();
    echo json_encode($resp);
  }

  public function validar_fechas($id_tipo_solicitud, $date = '', $format = 'Y-m-d H:i')
  {
    $data_tiempo = $this->genericas_model->obtener_valores_parametro_aux($id_tipo_solicitud, 123);
    $fecha_actual = date($format);
    $dias_solicitud = empty($data_tiempo) ? 0 : $data_tiempo[0]['valory'];
    $fecha_inicio = $date ? date($format, strtotime($date)) : date($format, strtotime($fecha_actual . " + $dias_solicitud days"));
    $resp = 0;
    $hoy = date($format);
    while ($hoy <= $fecha_inicio) {
      if (!$this->es_habil($hoy)) $resp += 1;
      $hoy = date("Y-m-d", strtotime($hoy . " +1 days"));
    }
    $total = ($dias_solicitud + $resp);
    $fecha_inicio_valida = date($format, strtotime($fecha_actual . " + $total days"));
    $sw = $fecha_inicio < $fecha_inicio_valida ? false : true;
    return ['total' => $total, 'dias_solicitud' => $dias_solicitud, 'sw' => $sw];
  }
  public function es_habil($c_day, $sabados = '')
  {
    $festivos = new festivos_colombia;
    $festivos->festivos(date("Y", strtotime($c_day)));
    $c_weekDay = (int) $this->getWeekDay($c_day);
    if ($c_weekDay == 0 || $festivos->esFestivo($c_day)) {
      return false;
    } else if ($sabados) {
      if ($c_weekDay == 0) return false;
    }
    return true;
  }

  public function getWeekDay($date)
  {
    return date("w", strtotime($date));
  }

  public function gestionar_solicitud()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $estado = $this->input->post("estado");
        $mensaje = trim($this->input->post("mensaje"));
        $usuario = $_SESSION["persona"];
        $funcionarios = $this->input->post('funcionarios');
        $data_funcionarios = array();
        $valido = $this->validar_estado($id, $estado);
        $solicitud = $this->bienestar_model->consulta_solicitud_id($id);
        $sw = true;

        if ($valido) {
          if ($estado == 'Bin_Neg_E') {
            if (empty($mensaje) || $mensaje == "") {
              $resp = ['mensaje' => "Debe ingresar el motivo.", 'tipo' => "info", 'titulo' => "Oops.!"];
              $sw = false;
            } else $data['motivo'] = $mensaje;
          } else if ($estado == 'Bin_Can_E') {
            if (empty($mensaje) || $mensaje == "") {
              $resp = ['mensaje' => "Debe ingresar el motivo.", 'tipo' => "info", 'titulo' => "Oops.!"];
              $sw = false;
            } else $data['motivo'] = $mensaje;
          } else if ($estado == 'Bin_Rev_E') {

            foreach ($funcionarios as $funcionario) {
              array_push($data_funcionarios, [
                'id_solicitud' => $id,
                'id_persona' => $funcionario['id'],
                'id_usuario_asigna' => $usuario
              ]);
            }
            if (empty($data_funcionarios)) {
              $resp = ['mensaje' => "No tiene funcionarios asisnados.", 'tipo' => "info", 'titulo' => "Oops.!"];
              $sw = false;
            } else {
              $add_funcionarios = $this->bienestar_model->guardar_datos($data_funcionarios, 'bienestar_funcionarios', 2);
              if ($add_funcionarios == 1) {
                $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                $sw = false;
              } else {
                $resp = ['mensaje' => "La asignacion se realizo con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
              }
            }
          } else if ($estado == 'Cancelar_Docente') {
            if (trim($mensaje) == "" || empty(trim($mensaje))) {
              $r = ["mensaje" => "¡Debe ingresar una observación valida!", "tipo" => "warning", "titulo" => "¡Atención!"];
              exit(json_encode($r));
            } else {
              $estado = 'Bin_Can_E';
              $data['motivo'] = $mensaje;
            }
          }
          $data['id_estado_sol'] = $estado;
          if ($sw) {
            $resp = ['mensaje' => "La solicitud fue gestionada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'tipo_solicitud' => $solicitud->{'tipo_solicitud'}];
            $mod = $this->bienestar_model->modificar_datos($data, 'bienestar_solicitudes', $id);

            $data_estado  = ['id_solicitud' => $id, 'id_estado' => $estado, 'id_usuario_registra' => $usuario];
            $add_estado = $this->bienestar_model->guardar_datos($data_estado, 'bienestar_estados');

            if ($mod != 0) $resp = ['mensaje' => "Error al gestionar la solicitud, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          } else {
            $resp = ['mensaje' => "La solicitud ya fue gestionada anteriormente o no esta autorizado para realizar esta operación.", 'tipo' => "info", 'titulo' => "Oops.!", 'refres' => 1];
          }
        } else $resp = ['mensaje' => "La solicitud ya fue gestionada anteriormente o no tiene permiso para realizar esta operacion.", 'tipo' => "info", 'titulo' => "Proceso Exitoso.!"];
      }
    }
    echo json_encode($resp);
  }

  public function validar_estado($id, $estado_nuevo)
  {
    $solicitud = $this->bienestar_model->consulta_solicitud_id($id);
    $tipo_solicitud = $solicitud->{'id_tipo_solicitud'};
    $estado_actual = $solicitud->{'id_estado_sol'};
    $solicitante = $solicitud->{'id_usuario_registra'};
    $persona = $_SESSION["persona"];
    $valido = false;
    if ($tipo_solicitud == 'Bin_Cla') {
      if ($this->administra && $estado_actual == 'Bin_Sol_E' && ($estado_nuevo == 'Bin_Rev_E' || $estado_nuevo == 'Bin_Neg_E' || $estado_nuevo == 'Bin_Can_E')) {
        $valido = true;
      } else if ($this->administra && $estado_actual == 'Bin_Rev_E' && ($estado_nuevo == 'Bin_Tra_E' || $estado_nuevo == 'Bin_Rep_E' || $estado_nuevo == 'Bin_Can_E')) {
        $valido = true;
      } else if ($this->administra && $estado_actual == 'Bin_Tra_E' && ($estado_nuevo == 'Bin_Fin_E'  || $estado_nuevo == 'Bin_Neg_E' || $estado_nuevo == 'Bin_Can_E')) {
        $valido = true;
      } else if ($this->funcionario && ($estado_actual == 'Bin_Rev_E' || $estado_actual == 'Bin_Rep_E') && ($estado_nuevo == 'Bin_Tra_E')) {
        $valido = true;
      } else if ($this->funcionario && $estado_actual == 'Bin_Tra_E' && ($estado_nuevo == 'Bin_Fin_E')) {
        $valido = true;
      } else if ($persona && $estado_actual == 'Bin_Sol_E' && ($estado_nuevo == 'Cancelar_Docente')) {
        $valido = true;
      } else if ($persona && $estado_actual == 'Bin_Rev_E' && ($estado_nuevo == 'Bin_Can_E' || $estado_nuevo == 'Cancelar_Docente')) {
        $valido = true;
      } else if ($persona && $estado_actual == 'Bin_Tra_E' && ($estado_nuevo == 'Bin_Can_E')) {
        $valido = true;
      } else if ($persona && $estado_actual == 'Bin_Rep_E' && ($estado_nuevo == 'Cancelar_Docente')) {
        $valido = true;
      } else if ($this->administra && $estado_actual == 'Bin_Rep_E' && ($estado_nuevo == 'Bin_Tra_E' || $estado_nuevo == 'Bin_Rep_E' || $estado_nuevo == 'Bin_Can_E')) {
        $valido = true;
      }
    }
    return $valido;
  }

  public function verificarDisponibilidad()
  {
    if ($this->Super_estado == true) {
      $fecha_inicio = $this->input->post("fecha_inicio");
      $fecha_fin = $this->input->post("fecha_fin");
      $estrategia = $this->input->post("estrategia");
      $funcionario = $this->input->post("funcionario");
      $resp = $this->bienestar_model->verificarDisponibilidad($fecha_inicio, $fecha_fin, $estrategia, $funcionario);
      $solicitudes = array();

      $ver_solicitado = '<span  style="background-color: #ffff;color: #000;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
      foreach ($resp as $row) {
        $row['ver'] = $ver_solicitado;
        array_push($solicitudes, $row);
      }
    }
    echo json_encode($solicitudes);
  }

  public function encuesta($id, $usuario = "")
  {
    $data['id_final'] =  $id;
    $data['usuario'] = $usuario;
    $this->load->view('pages/bienestar_encuesta', $data);
  }

  public function asistencia($id)
  {
    $data['id_solicitud'] =  $id;
    $this->load->view('pages/bienestar_asistencia', $data);
  }

  public function verificar_credenciales()
  {
    $usuario = $this->input->post('usuario');
    $codigo = $this->input->post('codigo');
    $contrasena = $this->input->post('contrasena');

    $existe = $this->logear($usuario, $contrasena);
    if ($existe) {
      $valido = $this->bienestar_model->verificar_codigo_acceso($codigo);
      if (!empty($valido) && is_null($valido->{'realizo'})) {
        $id_estudiante = $valido->{'id_estudiante'};
        $usuario_valido = $this->bienestar_model->verificar_usuario($id_estudiante);
        if (!empty($usuario_valido) && ($usuario_valido->{'correo'} == "$usuario@cuc.edu.co")) return $this->encuesta($codigo, $usuario);
      }
    }
    redirect("/bienestar/encuesta/ingresar/$codigo/invalido");
  }

  public function ingresar($codigo, $invalido = "")
  {
    $data['usuario'] = '';
    $data['nombre'] = '';
    $data['ingreso'] = '';
    $data['codigo'] = '';
    $data['id'] = '';
    $data['mensaje'] = $invalido;
    $valido = $this->bienestar_model->verificar_codigo_acceso($codigo);
    if (!empty($valido) && is_null($valido->{'realizo'})) {
      $id_estudiante = $valido->{'id_estudiante'};
      $id = $valido->{'id'};
      $usuario = $this->bienestar_model->verificar_usuario($id_estudiante);
      if (!empty($usuario)) {
        $data['usuario'] = $usuario->{'correo'};
        $data['nombre'] = $usuario->{'nombre'};
        $data['codigo'] = $codigo;
        $data['id'] = $id;
      }
    }
    $this->load->view("pages/bienestar_encuesta", $data);
  }

  public function listar_funcionarios_solicitud()
  {
    $id = $this->input->post("id");
    $resp = $this->Super_estado == true ? $this->bienestar_model->listar_funcionarios_solicitud($id) : array();
    echo json_encode($resp);
  }

  public function eliminar_funcionario_solicitud()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_elimina == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $id_solicitud = $this->input->post("id_solicitud");
        $solicitud = $this->bienestar_model->consulta_solicitud_id($id_solicitud);

        $estado_actual = $solicitud->{'id_estado_sol'};
        if ($estado_actual == 'Bin_Sol_E' || $estado_actual == 'Bin_Rev_E' || $estado_actual == 'Bin_Rep_E') {
          $estudiantes =  count($this->bienestar_model->listar_funcionarios_solicitud($id_solicitud));
          if ($estudiantes == 1) {
            $resp = ['mensaje' => "Su solicitud debe tener por lo menos un funcionario.", 'tipo' => "info", 'titulo' => "Oops.!"];
          } else {
            $fecha = date("Y-m-d H:i");
            $usuario = $_SESSION["persona"];
            $data = [
              "id_usuario_retira" => $usuario,
              "fecha_retira" => $fecha,
              "estado" => 0,
            ];
            $resp = ['mensaje' => "El estudiante fue eliminado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            $del = $this->bienestar_model->modificar_datos($data, 'bienestar_funcionarios', $id);
            if ($del != 0) $resp = ['mensaje' => "Error al eliminar al estudiante, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          }
        } else {
          $resp = ['mensaje' => "No es posible realizar esta acción ya que La solicitud se encuentra en tramite o terminada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function guardar_funcionario_nuevo()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_persona = $this->input->post('id_persona');
        $id_solicitud = $this->input->post('id_solicitud');
        $identificacion = $this->input->post('identificacion');
        $id_usuario_registra = $_SESSION['persona'];
        $solicitud = $this->bienestar_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud->{'id_estado_sol'};
        if ($estado_actual == 'Bin_Sol_E' ||  $estado_actual == 'Bin_Rev_E' ||  $estado_actual == 'Bin_Rep_E') {
          $existe = $this->bienestar_model->funcionario_solicitud($identificacion, $id_solicitud);

          if ($existe) {
            $resp = ['mensaje' => "El funcionario ya se encuentra asignado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          } else {
            $data_persona = [
              'id_solicitud' => $id_solicitud,
              'id_persona' => $id_persona,
              'id_usuario_asigna' => $id_usuario_registra,
            ];
            $add = $this->bienestar_model->guardar_datos($data_persona, 'bienestar_funcionarios');
            $resp = ['mensaje' => "El funcionario fue asignado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            if ($add == 1) $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          }
        } else {
          $resp = ['mensaje' => "No puede realizar esta acción debido a que la solicitud se encuentra en proceso o ya fue finalizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function listar_estados()
  {
    $id_solicitud = $this->input->post("id_solicitud");
    $resp = $this->Super_estado == true ? $this->bienestar_model->listar_estados($id_solicitud) : array();
    echo json_encode($resp);
  }

  public function guardar_encuesta()
  {
    $code = $this->input->post('codigo');
    $actividad = $this->input->post('actividad');
    $servicio = $this->input->post('servicio');
    $apropiado = $this->input->post('apropiado');
    $integral = $this->input->post('integral');
    $metodologia = $this->input->post('metodologia');
    $otros = $this->input->post('otros');
    $valido = $this->bienestar_model->verificar_codigo_acceso($code);

    if (!empty($valido) && is_null($valido->{'realizo'})) {
      $id_estudiante = $valido->{'id'};
      $datos = [
        "id_estudiante" => $id_estudiante,
        "actividad" => $actividad,
        "servicio" => $servicio,
        "apropiado" => $apropiado,
        "integral" => $integral,
        "metodologia" => $metodologia,
        'otros' => $otros,
      ];

      $str = $this->verificar_campos_string(['1' => $actividad, '2' => $servicio, '5' => $metodologia, '6' => $otros]);
      if (is_array($str)) {
        $resp = ['mensaje' => "La respuesta numero " . $str['field'] . " no puede estar vacia.", 'tipo' => "info", 'titulo' => "Oops.!"];
      } else {
        $add = $this->bienestar_model->guardar_datos($datos, 'bienestar_encuesta');
        if ($add != 0) $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        else $resp = ['mensaje' => "Encuesta relalizada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      }
    } else {
      $resp = ['mensaje' => "El codigo de acceso es invalido o la encuesta ya fue realizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
    }
    echo json_encode($resp);
  }

  public function encuesta_success()
  {
    $data['success'] = 'si';
    $this->load->view("pages/bienestar_encuesta", $data);
  }

  public function generar_codigo($id)
  {
    $solicitud = $this->bienestar_model->consulta_solicitud_id($id);
    $tipo_solicitud = $solicitud->{'id_tipo_solicitud'};
    if ($tipo_solicitud == 'Bin_Cla') $estudiantes = $this->bienestar_model->listar_estudiantes_solicitud($id);
    foreach ($estudiantes as $row) {
      $code = $row['id_solicitud'] . $row['id'];
      $data = [
        'codigo_acceso' => $code
      ];
      $mod = $this->bienestar_model->modificar_datos($data, 'bienestar_estudiantes', $row['id']);
    }
    return true;
  }

  public function ver_detalle_encuesta($id)
  {
    $solicitud = $this->bienestar_model->consulta_solicitud_id($id);
    $tipo_solicitud = $solicitud->{'id_tipo_solicitud'};
    if ($tipo_solicitud == 'Bin_Cla') $estudiantes = $this->bienestar_model->listar_estudiantes_solicitud($id);
    foreach ($estudiantes as $row) {
      $code = $row['id_solicitud'] . $row['id'];
      $data = [
        'codigo_acceso' => $code
      ];
      $mod = $this->bienestar_model->modificar_datos($data, 'bienestar_estudiantes', $row['id']);
    }
    return true;
  }

  public function obtener_fecha_fin($fecha_i, $duracion_min, $format = 'Y-m-d H:i:s')
  {
    $fecha_fin = date($format, strtotime($fecha_i . " + $duracion_min minutes"));
    return $fecha_fin;
  }

  public function logear($usuario, $password)
  {
    require_once(APPPATH . '../LDAP/ldap.php');
    $existe = mailboxpowerloginrd($usuario, $password);
    if ($existe == "0" || $existe == '') return 0;
    else return 1;
  }

  public function verificar_firma()
  {
    $usuario = $this->input->post("usuario");
    $password = $this->input->post("password");
    $id_estudiante = $this->input->post("id_estudiante");
    $id_solicitud = $this->input->post("id_solicitud");

    $estudiante = $this->bienestar_model->consulta_estudiante_id($id_estudiante);
    $solicitud = $this->bienestar_model->consulta_solicitud_id($id_solicitud);
    $nombre_completo = $estudiante->{'nombre_completo'};
    $correo = $estudiante->{'correo'};
    $estado_sol = $estudiante->{'id_estado_sol'};
    $tematica = $estudiante->{'tematica'};

    $valido = $this->logear($usuario, $password);
    if ($valido) {
      $code = $id_estudiante;
      $data = ['codigo_acceso' => $code];
      $mod = $this->bienestar_model->modificar_datos($data, 'bienestar_estudiantes', $id_estudiante);
      $resp = ['tipo' => "success", 'codigo' => $code, 'id_estado_sol' => $estado_sol, 'nombre_completo' => $nombre_completo, 'correo' => $correo, 'tematica' => $tematica, 'tipo_solicitud' => $solicitud->{'tipo_solicitud'}];
    } else {
      $resp = ['mensaje' => "Error al ingresar la contraseña.", 'tipo' => "info", 'titulo' => "Oops..!"];
    }
    echo json_encode($resp);

    // $resp = $this->Super_estado == true ? $this->bienestar_model->verificar_firma( $id ) : array();
    // echo json_encode($resp);
  }

  public function listar_valor_parametro()
  {
    $id_parametro = $this->input->post("id_parametro");
    $filtro = $this->input->post("filtro");
    $id_tematica = $this->input->post("id_tematica");
    $data = $this->Super_estado == true ? $this->bienestar_model->listar_valor_parametro($id_parametro, $id_tematica) : array();
    $btn_config = '<span title="Asignar Funcionario" data-toggle="popover" data-trigger="hover" style="color: #39b23b;margin-left: 5px" class="pointer fa fa-user btn btn-default asignar"></span>';
    $btn_modificar = '<span title="Modificar Temática" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
    $btn_eliminar = '<span title="Eliminar Temática" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
    $valores = array();
    if ($filtro == 2) {
      echo json_encode($data);
    } else {
      foreach ($data as $row) {
        ($row['idparametro'] != 3) ? $row['accion'] = $btn_config . ' ' . $btn_modificar . ' ' . $btn_eliminar : $row['accion'] = $btn_config;
        array_push($valores, $row);
      }
      echo json_encode($valores);
    }
  }

  public function listar_funcionarios_tematicas()
  {

    $id_tematica = $this->input->post("id_tematica");
    $data = $this->Super_estado == true ? $this->bienestar_model->listar_funcionarios_tematicas($id_tematica) : array();
    $btn_eliminar = '<span title="Eliminar Temática" data-toggle="popover" data-trigger="hover" style="color: #ca3e33;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
    $funcionarios = array();

    foreach ($data as $row) {
      $row['accion'] = $btn_eliminar;
      array_push($funcionarios, $row);
    }
    echo json_encode($funcionarios);
  }

  public function eliminar_funcionario_tematica()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_elimina == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $id_tematica = $this->input->post("id_tematica");

        $funcionarios =  count($this->bienestar_model->listar_funcionarios_tematicas($id_tematica));
        if ($funcionarios == 1) {
          $resp = ['mensaje' => "Su solicitud debe tener por lo menos un funcionario.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $fecha = date("Y-m-d H:i");
          $usuario = $_SESSION["persona"];
          $data = [
            "id_usuario_elimina" => $usuario,
            "fecha_elimina" => $fecha,
            "estado" => 0,
          ];
          $resp = ['mensaje' => "El funcionario fue eliminado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          $del = $this->bienestar_model->modificar_datos($data, 'bienestar_funcionarios_relacion', $id);
          if ($del != 0) $resp = ['mensaje' => "Error al eliminar al estudiante, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function guardar_funcionario_tematica()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_persona = $this->input->post('id_persona');
        $id_tematica = $this->input->post('id_tematica');
        $identificacion = $this->input->post('identificacion');
        $id_usuario_registra = $_SESSION['persona'];
        $existe = $this->bienestar_model->consulta_tematicas_funcionarios($id_tematica, $id_persona);
        if ($existe) {
          $resp = ['mensaje' => "El Funcionario ya se encuentra asignado.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $data_persona = [
            'id_relacion' => $id_tematica,
            'id_persona' => $id_persona,
            'id_usuario_registra' => $id_usuario_registra,
          ];
          $add = $this->bienestar_model->guardar_datos($data_persona, 'bienestar_funcionarios_relacion');
          $resp = ['mensaje' => "El funcionario fue asignado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          if ($add == 1) $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function listar_horarios_funcionarios()
  {
    $data = $this->Super_estado == true ? $this->bienestar_model->listar_horarios_funcionarios() : array();
    $btn_eliminar = '<span title="Eliminar Horario" data-toggle="popover" data-trigger="hover" style="color: #ca3e33;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
    $btn_modificar = '<span title="Modificar Horario" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
    $btn_config = '<span title="Asignar Funcionario" data-toggle="popover" data-trigger="hover" style="color: #39b23b;margin-left: 5px" class="pointer fa fa-user btn btn-default funcionario"></span>';
    $horarios = array();
    foreach ($data as $row) {
      $row['accion'] = $btn_config . ' ' . $btn_modificar . ' ' . $btn_eliminar;
      array_push($horarios, $row);
    }
    echo json_encode($horarios);
  }

  public function listar_funcionarios_horarios()
  {
    $id_horario = $this->input->post("id_horario");
    $data = $this->Super_estado == true ? $this->bienestar_model->listar_funcionarios_horarios($id_horario) : array();
    $btn_eliminar = '<span title="Eliminar Funcionario" data-toggle="popover" data-trigger="hover" style="color: #ca3e33;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
    $funcionarios = array();
    foreach ($data as $row) {
      $row['accion'] = $btn_eliminar;
      array_push($funcionarios, $row);
    }
    echo json_encode($funcionarios);
  }

  public function eliminar_horario_funcionario()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_elimina == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $fecha = date("Y-m-d H:i");
        $usuario = $_SESSION["persona"];
        $data = [
          "id_usuario_elimina" => $usuario,
          "fecha_elimina" => $fecha,
          "estado" => 0,
        ];
        $resp = ['mensaje' => "El horario fue eliminado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        $del = $this->bienestar_model->modificar_datos($data, 'bienestar_horario', $id);
        if ($del != 0) $resp = ['mensaje' => "Error al eliminar al horario, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }
    }
    echo json_encode($resp);
  }

  public function guardar_horario_funcionario()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_dia = $this->input->post('id_dia');
        $hora_inicio = $this->input->post('hora_inicio');
        $hora_fin = $this->input->post('hora_fin');
        $observacion = $this->input->post('descripcion');
        $id_horario = $this->input->post('id_horario');
        $id_usuario_registra = $_SESSION['persona'];
        $str = $this->verificar_campos_string(['Día' => $id_dia, 'Hora Inicio' => $hora_inicio, 'Hora Fin' => $hora_fin]);
        if (is_array($str)) {
          $resp = ['mensaje' => "El campo " . $str['field'] . " no puede estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          if ($id_horario) {
            $validar = $this->bienestar_model->traer_ultima_solicitud($id_horario, 'bienestar_horario', 'id');
            if (($validar->{'id_dia'} == $id_dia) && ($validar->{'hora_inicio'} == $hora_inicio) && ($validar->{'hora_fin'} == $hora_fin) && ($validar->{'observacion'} == $observacion)) {
              $resp = ['mensaje' => "Debe realizar alguna modificación en el horario.", 'tipo' => "info", 'titulo' => "Oops.!"];
            } else {
              $data_horario = ['id_dia' => $id_dia, 'hora_inicio' => $hora_inicio, 'hora_fin' => $hora_fin, 'observacion' => $observacion];
              $mod = $this->bienestar_model->modificar_datos($data_horario, 'bienestar_horario', $id_horario);
              $resp = ['mensaje' => "El horario fue gestionado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
              if ($mod == 1) $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            }
          } else {
            $data_horario = [
              'id_dia' => $id_dia,
              'hora_inicio' => $hora_inicio,
              'hora_fin' => $hora_fin,
              'observacion' => $observacion,
              'id_usuario_registra' => $id_usuario_registra
            ];
            $add = $this->bienestar_model->guardar_datos($data_horario, 'bienestar_horario');
            $resp = ['mensaje' => "El horario fue guardado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            if ($add == 1) $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function guardar_funcionario_horario()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_persona = $this->input->post('id_persona');
        $id_horario = $this->input->post('id_horario');
        $id_usuario_registra = $_SESSION['persona'];
        $existe = $this->bienestar_model->validar_funcionario_horario($id_persona, $id_horario);
        if ($existe) {
          $resp = ['mensaje' => "El funcionario ya se encuentra registrado.!", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $data = [
            'id_horario' => $id_horario,
            'id_persona	' => $id_persona,
            'id_usuario_registra' => $id_usuario_registra
          ];
          $add = $this->bienestar_model->guardar_datos($data, 'bienestar_funcionarios_horario');
          $resp = ['mensaje' => "El funcionario fue guardado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          if ($add == 1) $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function eliminar_funcionario_horario()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_elimina == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $fecha = date("Y-m-d H:i");
        $usuario = $_SESSION["persona"];
        $data = [
          "id_usuario_elimina" => $usuario,
          "fecha_elimina" => $fecha,
          "estado" => 0,
        ];
        $resp = ['mensaje' => "El funcionario fue eliminado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        $del = $this->bienestar_model->modificar_datos($data, 'bienestar_funcionarios_horario', $id);
        if ($del != 0) $resp = ['mensaje' => "Error al eliminar al funcionario, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }
    }
    echo json_encode($resp);
  }

  public function obtener_coordinadores_por_programa()
  {
    $id = $this->input->post('id');
    $resp = $this->Super_estado ? $this->bienestar_model->obtener_coordinadores_por_programa($id) : array();
    echo json_encode($resp);
  }

  public function listar_encuestas()
  {
    $id = $this->input->post("id");
    $resp = $this->Super_estado == true ? $this->bienestar_model->listar_encuestas($id) : array();
    echo json_encode($resp);
  }

  public function exportar_excel_bienestar($id)
  {
    $resp = $this->bienestar_model->listar_encuestas($id);
    $datos["datos"] = $resp;
    $datos["nombre"] = "Encuesta_BienestarAtuClase";
    $datos["titulo"] = "REPORTE DE ENCUESTAS";
    $datos["version"] = "VERSION: 07";
    $datos["trd"] = "TRD: 500-520-90";
    $datos["fecha"] = "2019-2";
    $i = 0;
    foreach ($resp as $row) {
      foreach ($row as $key => $val) $i++;
      break;
    }
    $datos["col"] = $i;
    $this->load->view('templates/exportar_excel_bienestar', $datos);
  }

  public function exportar_excel_solicitudes($id, $estrategia, $estado, $fecha, $fecha_2)
  {
    $excel = 1;
    $resp = $this->bienestar_model->listar_solicitudes($id, $estrategia, $estado, $fecha, $fecha_2, $excel);
    $datos["datos"] = $resp;
    $datos["nombre"] = "Solicitudes_BienestarAtuClase";
    $datos["titulo"] = "REPORTE DE SOLICITUDES";
    $datos["version"] = "VERSION: 07";
    $datos["trd"] = "TRD: 500-520-90";
    $datos["fecha"] = "2019-2";
    $i = 0;
    foreach ($resp as $row) {
      foreach ($row as $key => $val) $i++;
      break;
    }
    $datos["col"] = $i;
    $this->load->view('templates/exportar_excel_solicitudes', $datos);
  }

  public function exportar_excel_encuestas()
  {
    $resp = $this->bienestar_model->listar_encuestas_exportar();
    $datos["datos"] = $resp;
    $datos["nombre"] = "Encuestas_BienestarAtuClase";
    $datos["titulo"] = "REPORTE DE SOLICITUDES";
    $datos["version"] = "VERSION: 07";
    $datos["trd"] = "TRD: 500-520-90";
    $datos["fecha"] = "2019-2";
    $i = 0;
    foreach ($resp as $row) {
      foreach ($row as $key => $val) $i++;
      break;
    }
    $datos["col"] = $i;
    $this->load->view('templates/exportar_excel_bienestar', $datos);
  }

  public function data_sesion()
  {
    echo json_encode(["perfil" => $_SESSION['perfil'], "persona" => $_SESSION['persona']]);
  }
  public function listar_modificaciones()
  {
    $id = $this->input->post("id");
    $resp = $this->Super_estado == true ? $this->bienestar_model->listar_modificaciones($id) : array();
    echo json_encode($resp);
  }


  public function listar_bloqueos()
  {
    $asistencia_ = $this->input->post("asistencia_");
    $resp = array();
    if ($this->Super_estado == true) {
      $data = $this->bienestar_model->listar_bloqueos();

      $bloqueos = array();
      $ver_solicitado = '<span  style="background-color: #ffff;color: #000;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
      $btn_eliminar = '<span style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>';
      $btn_modificar = '<span title="Modificar Temática" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
      foreach ($data as $row) {
        $row['ver'] = $ver_solicitado;
        $row['accion'] =  $btn_modificar . ' ' . $btn_eliminar;
        array_push($bloqueos, $row);
      }
      echo json_encode($bloqueos);
    }
  }
  public function guardar_bloqueo()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $tematica = $this->input->post('idtematica');
        $nombre = $this->input->post('nombre');
        $descripcion = $this->input->post('descripcion');
        $fecha_inicio = $this->input->post('bloqueo_fecha_inicio');
        $fecha_fin = $this->input->post('bloqueo_fecha_fin');
        $id_usuario_registra = $_SESSION['persona'];
        $sw = true;

        $fecha_i = $this->validateDate($fecha_inicio, 'Y-m-d H:i:s');
        $fecha_f = $this->validateDate($fecha_fin, 'Y-m-d H:i:s');
        $str = $this->verificar_campos_string(['Nombre' => $nombre, 'Descripcion' => $descripcion, 'Fecha Inicio' => $fecha_inicio, 'Fecha Fin' => $fecha_fin]);
        // $val_bloqueo = $this->bienestar_model->fechaDisponible($fecha_inicio, $fecha_fin, $tematica);
        if (empty($tematica)) {
          $tematica = 0;
        }

        if (is_array($str)) {
          $resp = ['mensaje' => "El campo " . $str['field'] . "  no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else if (!$fecha_i) {
          $sw = false;
          $resp = ['mensaje' => "Digite una fecha inicio valida.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else if (!$fecha_f) {
          $sw = false;
          $resp = ['mensaje' => "Digite una fecha fin valida.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $fechas_info = $this->validateFechaMayor($fecha_inicio, $fecha_fin);
          if ($fechas_info == -1) {
            $sw = false;
            $resp = ['mensaje' => "La fecha inicio no puede ser mayor a la fecha fin.", 'tipo' => "info", 'titulo' => "Oops.!"];
          }
        }
        // if ($val_bloqueo) {
        //   $sw = false;
        //   $resp = ['mensaje' => "Digite una fecha valida, ya se encuentra bloqueo en ese rango de fechas.", 'tipo' => "info", 'titulo' => "Oops.!"];
        // }
        if ($sw) {
          $data = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'id_tematica' => $tematica,
            'id_usuario_registra' => $id_usuario_registra,
          ];
          $add = $this->bienestar_model->guardar_datos($data, 'bienestar_bloqueos');
          $resp = ['mensaje' => "El Bloqueo fue creado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          if ($add == 1) $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function ver_detalle_bloqueo($id)
  {
    $solicitud = $this->bienestar_model->consulta_solicitud_bloqueo_id($id);
    $tipo_solicitud = $solicitud->{'id_tipo_solicitud'};
    if ($tipo_solicitud == 'Bin_Cla') $estudiantes = $this->bienestar_model->listar_estudiantes_solicitud($id);
    foreach ($estudiantes as $row) {
      $code = $row['id_solicitud'] . $row['id'];
      $data = [
        'codigo_acceso' => $code
      ];
      $mod = $this->bienestar_model->modificar_datos($data, 'bienestar_estudiantes', $row['id']);
    }
    return true;
  }


  public function eliminar_bloqueo()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_elimina == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $fecha = date("Y-m-d H:i");
        $usuario = $_SESSION["persona"];
        $data = [
          "id_usuario_elimina" => $usuario,
          "fecha_elimina" => $fecha,
          "estado" => 0,
        ];
        $resp = ['mensaje' => "El Bloqueo fue eliminado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        $del = $this->bienestar_model->modificar_datos($data, 'bienestar_bloqueos', $id);
        if ($del != 0) $resp = ['mensaje' => "Error al eliminar el bloqueo, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }
    }
    echo json_encode($resp);
  }


  public function modificar_bloqueo()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id_bloqueo');
        $id_tematica = $this->input->post('idtematica');
        $nombre = $this->input->post('nombre');
        $descripcion = $this->input->post('descripcion');
        $fecha_inicio = $this->input->post('bloqueo_fecha_inicio');
        $fecha_fin = $this->input->post('bloqueo_fecha_fin');
        $id_usuario_registra = $_SESSION['persona'];
        $sw = true;

        $str = $this->verificar_campos_string(['Nombre' => $nombre, 'Descripcion' => $descripcion, 'Fecha Inicio' => $fecha_inicio, 'Fecha Fin' => $fecha_fin]);
        $fecha_i = $this->validateDate($fecha_inicio, 'Y-m-d H:i:s');
        $fecha_f = $this->validateDate($fecha_fin, 'Y-m-d H:i:s');

        if (is_array($str)) {
          $resp = ['mensaje' => "El campo " . $str['field'] . "  no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        } else if (!$fecha_i) {
          $sw = false;
          $resp = ['mensaje' => "Digite una fecha inicio valida.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else if (!$fecha_f) {
          $sw = false;
          $resp = ['mensaje' => "Digite una fecha fin valida.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $fechas_info = $this->validateFechaMayor($fecha_inicio, $fecha_fin);
          if ($fechas_info == -1) {
            $sw = false;
            $resp = ['mensaje' => "La fecha inicio no puede ser mayor a la fecha fin.", 'tipo' => "info", 'titulo' => "Oops.!"];
          }
        }
        if ($sw) {
          $data = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'id_tematica' => $id_tematica,
            'id_usuario_registra' => $id_usuario_registra,
          ];
          $del = $this->bienestar_model->modificar_datos($data, 'bienestar_bloqueos', $id);
          $resp = ['mensaje' => "El bloqueo fue modificado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          if ($del == 1) $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function listar_fechaDisponible($fecha_inicio, $fecha_fin)
  {
    $resp = $this->Super_estado == true ? $this->bienestar_model->fechaDisponible($fecha_inicio, $fecha_fin) : array();
    echo json_encode($resp);
  }

  public function fechasDisponibilidad()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $fecha = $this->input->post('fecha');
      $duracion = $this->input->post('duracion');
      $bloqueo = $this->input->post('bloqueo');
      $tematica = $this->input->post('tematica');
      $fecha_i = $this->validateDate($fecha, 'Y-m-d H:i:s');
      $duracion_min = $this->genericas_model->obtener_valor_parametro_id($duracion)[0]['valory'];
      $fecha_fin = $this->obtener_fecha_fin($fecha, $duracion_min);
      $val_fecha = !$this->administra ? $this->validar_fechas('Bin_Cla', $fecha, 'Y-m-d') : ['sw' => true];
      if ($bloqueo > 0) {
        $disponibilidad = $this->bienestar_model->consulta_bloqueos($fecha, $fecha_fin, $tematica);
      } else {
        $disponibilidad = $this->bienestar_model->fechaDisponible($fecha, $fecha_fin, $tematica);
      }
      $str = $this->verificar_campos_string(['Fecha Inicio' => $fecha, 'Duración' => $duracion]);
      $fecha_i = $this->validateDate($fecha, 'Y-m-d H:i:s');
      $fecha_f = $this->validateDate($fecha_fin, 'Y-m-d H:i:s');

      if (is_array($str)) {
        $resp = ['mensaje' => "El campo " . $str['field'] . "  no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
      } else if (!$fecha_i) {
        $resp = ['mensaje' => "Digite una fecha inicio valida.", 'tipo' => "info", 'titulo' => "Oops.!"];
      } else if (!$fecha_f) {
        $resp = ['mensaje' => "Digite una fecha fin valida.", 'tipo' => "info", 'titulo' => "Oops.!"];
      } else if ($disponibilidad) {
        // $resp = ['mensaje'=>"La fecha seleccionada no se encuentra disponible por los siquientes motivos: ", 'tipo'=>"no_disponible", 'disponibilidad'=>$disponibilidad];
        $resp = ['mensaje' => "No hay disponibilidad en la fecha seleccionada por los siquientes motivos", 'tipo' => "no_disponible", 'disponibilidad' => $disponibilidad];
      } else {
        $resp = ['mensaje' => "Fecha Disponible", 'tipo' => "success"];
      }
    }
    echo json_encode($resp);
  }
  public function funcionarioDisponibilidad($id_tematica, $fecha_inicio, $fecha_fin, $cod_materia, $dia, $dia_f, $id_solicitud = '')
  {
    $int = $this->genericas_model->obtener_valores_parametro_aux("Bin_Clas_Int", 20);
    $max_intervencion = (int) $int[0]['valorx'];
    $funcionarios = $this->bienestar_model->funcionariosTematicas($id_tematica, $cod_materia, $fecha_inicio, $fecha_fin, $dia, $dia_f);
    if ($funcionarios) {
      $valor = 1000;
      $funcionarioAsignado = 0;
      $data_funcionarios = array();
      $data_funcionarios = [];
      foreach ($funcionarios as $funcionario) {
        $fun = $funcionario['funcionario'];
        $fun_cantidad = (int) $funcionario['cantidad'];
        $solicitudes = $this->bienestar_model->consultaSolicitudes($fun, $fecha_inicio, $fecha_fin, $id_solicitud);

        if (!$solicitudes) {
          if ($fun_cantidad < $valor) {
            $valor = $fun_cantidad;
          }
          array_push($data_funcionarios, [
            'id_persona' => $fun, 'cantidad' => $fun_cantidad
          ]);
        }
      }
      $cantArray = count($data_funcionarios);

      if ($cantArray >= 1) {
        $data = [];
        foreach ($data_funcionarios as $fun) { // recorre array de los funcionarios disponibles con la cantidad de solicitudes del día
          if ($fun['cantidad'] < $max_intervencion) { // halla los funcionarios con menor solicitud en el día
            $valor = $fun['cantidad'];
            array_push($data, ['id_persona' => $fun['id_persona'],]);
          }
        }
        $cantdata = count($data); // cantidad de funcionarios con menor solicitud en el día
        if ($cantdata > 1) { // se escoje un funcionario entre los disponibles 
          $rand = rand(0, $cantdata - 1);
          $funcionarioAsignado = $data[$rand];
        }else if ($cantdata == 1) {
          $funcionarioAsignado = $data[0];
        }
      }
      return $funcionarioAsignado;
    }
  }

  // public function Reasignarfuncionario(){ //funcion para asignar funcionario automático desde botón asignar funcionario
  //     if(!$this->Super_estado){
  //         $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
  //     }else{
  //         $id_tematica = $this->input->post('id_tematica');
  //         $id_solicitud  = $this->input->post('id_solicitud');
  //         $id_usuario_registra = $_SESSION['persona']; 
  //         $datos_solicitud = $this->bienestar_model->consulta_solicitud_id($id_solicitud);
  //         $funcionarioDisponible = $this->funcionarioDisponibilidad($id_tematica,$datos_solicitud->{'fecha_inicio'},$datos_solicitud->{'fecha_fin'},$datos_solicitud->{'cod_materia'});
  //         if($funcionarioDisponible){           
  //             $funcionarioDisponible['id_solicitud'] = $id_solicitud;                            
  //             $funcionarioDisponible['id_usuario_asigna'] = $id_usuario_registra; 
  //             $add_funcionario = $this->bienestar_model->guardar_datos($funcionarioDisponible, 'bienestar_funcionarios');
  //             if($add_funcionario == 1){ 
  //                 $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];                        
  //             }else{
  //                 $resp = ['mensaje'=>"El funcionario fue asignado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
  //             }

  //         }else{
  //             $resp = ['mensaje'=>"No hay funcionarios disponibles para atender su solicitud en la fecha seleccionada.",'tipo'=>"info",'titulo'=> "Oops..!"];
  //         }           
  //     }
  //     echo json_encode($resp);
  // }
  public function validar_funcionario()
  {
    $id_solicitud = $this->input->post('id_solicitud');
    $identificacion = $this->input->post('identificacion_fun');
    $validar_funcionario = $this->bienestar_model->validar_funcionario($identificacion, $id_solicitud);
    if ($validar_funcionario > 0) {
      $resp = ['mensaje' => "El funcionario se encuentra bloqueado, contacte con el administrador.", 'tipo' => "info", 'titulo' => "Oops.!"];
    } else {
      $resp = ['tipo' => "success"];
    }
    echo json_encode($resp);
  }

  public function listar_disponibilidad()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $bloqueos = $this->input->post('disponibilidad');
      $fecha_inicio_solicitud = $this->input->post('fecha');
      $duracion = $this->input->post('duracion');
      $duracion_min = $this->genericas_model->obtener_valor_parametro_id($duracion)[0]['valory'];
      $fecha_fin_solicitud = $this->obtener_fecha_fin($fecha_inicio_solicitud, $duracion);

      $horario = array();

      $f = date("Y-m-d", strtotime($fecha_inicio_solicitud));
      $duracion_min = '30';
      $horario_inicio = date("H:i", strtotime("6:00"));
      $horario_fin = date("H:i", strtotime("21:30"));

      $horario_nuevo = $horario_inicio;
      $listar_disponibilidad = array();
      $lista_dis = array();
      $horario_inicio_b = date("H:i", strtotime("6:30"));;
      while ($horario_nuevo < $horario_fin) {
        $horario_nuevo = $this->obtener_fecha_fin($horario_nuevo, $duracion_min, 'H:i');
        foreach ($bloqueos as $bloqueo) {
          $fecha_inicio_b = $this->obtener_fecha_fin($bloqueo['fecha_inicio'], 0, 'H:i');
          $fecha_fin_b = $this->obtener_fecha_fin($bloqueo['fecha_fin'], 0, 'H:i');
          if ($horario_nuevo > $fecha_inicio_b && $horario_nuevo < $fecha_fin_b) {
            if ($horario_inicio_b != $fecha_inicio_b)
              array_push($listar_disponibilidad, ["fecha_inicio" => $horario_inicio_b, "fecha_fin" =>  $fecha_inicio_b]);
            $horario_inicio_b =  $fecha_fin_b;
            $horario_nuevo =  $fecha_fin_b;
            break;
          }
        }
        if ($horario_nuevo > $horario_inicio_b  && $horario_nuevo == $horario_fin) {
          array_push($listar_disponibilidad, ["fecha_inicio" => $horario_inicio_b, "fecha_fin" =>  $horario_fin]);
        }
      }

      if (count($listar_disponibilidad) > 0) {
        $resp = ['tipo' => "success", 'lista_disponibilidad' => $listar_disponibilidad];
      } else {
        $resp = ['mensaje' => "No hay disponibilidad para atender su solicitud en la fecha seleccionada.", 'tipo' => "no_disponible"];
      }
    }
    echo json_encode($resp);
  }

  public function tematica_estrategia()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $resp = ['mensaje' => "Datos Guardados.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        $estrategias = $this->input->post('estrategias');
        $nombre = $this->input->post('nombre');
        $descripcion = $this->input->post('descripcion');
        $idparametro = $this->input->post('idparametro');
        // $tematica = $this->bienestar_model->consulta_id_tematica($_SESSION['persona']);
        $tematica = $this->bienestar_model->traer_ultima_solicitud($_SESSION['persona'], 'valor_parametro','usuario_registra');
        $data = array();
        if (!empty($tematica)) {
          $id_tematica = $tematica->{'id'};
          foreach ($estrategias as $key)  array_push($data, ['vp_principal_id' => $id_tematica, 'vp_secundario_id' => $key['id']]);
          $add = $this->bienestar_model->guardar_datos($data, 'permisos_parametros', 2);
          if ($add == 1) $resp = ['mensaje' => "Error al asignar las estrategías, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        } else $resp = ['mensaje' => "Error al obtener la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }
    }
    echo json_encode($resp);
  }

  public function get_funcionario_solicitud()
  {
    $funcionarios = [];
    if (!$this->Super_estado) $funcionarios = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id_solicitud = $this->input->post('id');
      $funcionarios = $this->bienestar_model->get_funcionario_solicitud($id_solicitud);
    }
    echo json_encode($funcionarios);
  }

  public function modificar_tematica()
  {
    $funcionarios = [];
    if (!$this->Super_estado) $funcionarios = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $id_solicitud = $this->input->post('id_solicitud');
      $id_tematica = $this->input->post('id_tematica');
      $id_usuario_registra = $_SESSION['persona'];
      $info = $this->bienestar_model->traer_ultima_solicitud($id_solicitud, 'bienestar_solicitudes', 'id');
      $fecha_inicio = $info->{'fecha_inicio'};
      $fecha_fin = $info->{'fecha_fin'};
      $dia = $this->get_nombre_dia($fecha_inicio);
      $dia_f = $this->get_nombre_dia($fecha_fin);
      $sw = false;
      $id_estrategia = $this->bienestar_model->obtener_id_permiso($id_tematica);
      $val_fecha = $this->validar_fechas($info->{'id_tipo_solicitud'}, $fecha_inicio, 'Y-m-d');
      if (!$val_fecha['sw']) {
        $resp = ['mensaje' => "Su solicitud debe tener " . $val_fecha['dias_solicitud'] . " días de anticipación a la fecha de inicio para realizar modificación.", 'tipo' => "info", 'titulo' => "Oops.!"];
        $sw = false;
      } else if (!$id_estrategia) {
        $resp = ['mensaje' => "Error al guardar la información de la estrategia a realizar, contacte con el administrador.", 'tipo' => "info", 'titulo' => "Oops.!"];
        $sw = false;
      } else {
        $filtro = 2;
        $funcionarioDisponible = $this->funcionarioDisponibilidad($id_tematica, $fecha_inicio, $fecha_fin, $info->{'cod_materia'}, $dia, $dia_f, $id_solicitud);
        if ($funcionarioDisponible > 0) {
          $funcionarioDisponible['id_solicitud'] = $id_solicitud;
          $funcionarioDisponible['id_usuario_asigna'] = $id_usuario_registra;
          $add_funcionario = $this->bienestar_model->guardar_datos($funcionarioDisponible, 'bienestar_funcionarios');
          if ($add_funcionario == 0) {
            $sw = true;
            $data_fun  = ['id_usuario_retira' => $id_usuario_registra, 'estado' => 0];
            $fun_asignado = $this->bienestar_model->traer_ultima_solicitud($id_solicitud, 'bienestar_funcionarios', 'id_solicitud');
            $mod = $this->bienestar_model->eliminar_funcionarios_solicitud($data_fun, $id_solicitud, $fun_asignado->{'id'}, $filtro);
          }
        } else {
          $resp = ['mensaje' => "No hay funcionario disponible para atender la solicitud en la fecha seleccionada!", 'tipo' => "info", 'titulo' => "Oops."];
          $sw = false;
        }
      }
      if ($sw) {
        $id_estrategia = $id_estrategia->{'vp_secundario_id'};
        $data = [
          'id_estrategia' => $id_estrategia,
          'id_tematica' => $id_tematica,
          'id_usuario_modifica' => $id_usuario_registra
        ];
        $add = $this->bienestar_model->modificar_datos($data, 'bienestar_solicitudes', $id_solicitud);
        if ($add != 0) {
          $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        } else  $resp = ['mensaje' => "La solicitud fue modificada de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      }
    }
    echo json_encode($resp);
  }
}
