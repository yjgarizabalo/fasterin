
<?php
date_default_timezone_set('America/Bogota');
class biblioteca_control extends CI_Controller
{
  var $Super_estado = false;
  var $Super_elimina = 0;
  var $Super_modifica = 0;
  var $Super_agrega = 0;
  var $ruta_archivos_solicitudes = "archivos_adjuntos/biblioteca/solicitudes";
  public function __construct()
  {
    parent::__construct();
    include('application/libraries/festivos_colombia.php');
    $this->load->model('biblioteca_model');
    $this->load->model('pages_model');
    $this->load->model('genericas_model');
    session_start();
    if (isset($_SESSION["usuario"])) {
      $this->Super_estado = true;
      $this->Super_elimina = 1;
      $this->Super_modifica = 1;
      $this->Super_agrega = 1;
    }
  }

  public function index($url = 'biblioteca', $id = '')
  {
    if ($this->Super_estado) {
      $datos_actividad =  $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'biblioteca');
      if (!empty($datos_actividad)) {
        $dias = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bib" ?  0 : $this->genericas_model->obtener_valores_parametro_aux("Dias_LB", 20)[0]["valor"];
        $tiempos = $this->genericas_model->obtener_valores_parametro(57);
        $data['tiempos'] = $tiempos;
        $data['dias'] = $dias;
        $pages = "biblioteca";
        $data['js'] = "biblioteca";
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
      $data['actividad'] = "ingresar";
    }
    $this->load->view('templates/header', $data);
    $this->load->view("pages/" . $pages);
    $this->load->view('templates/footer');
  }

  public function descargar_nivel($id = '')
  {
    $niveles = $this->biblioteca_model->obtener_niveles_sol($id);
    $todo = [];
    foreach ($niveles as $row) {
      $nombre = strtok($row['capacitacion'], '-');
      $nivel = strtok('-');
      $fecha = strtok($row['fecha_inicio'], ' ');
      $hora_inicio = strtok(' ');
      $fecha_fin = strtok($row['fecha_fin'], ' ');
      $hora_fin = strtok(' ');
      $duracion = $row['tiempo'];
      $bloque = $row['bloque_log'];
      $salon = $row['salon_log'];
      $tipo = $row['tipo'];
      array_push(
        $todo,
        [
          'nombre' => $nombre,
          'nivel' => $nivel,
          'duracion' => $duracion,
          'fecha' => $fecha,
          'hora_inicio' => $hora_inicio,
          'hora_fin' => $hora_fin,
          'bloque' => $bloque,
          'salon' => $salon,
          'tipo' => $tipo
        ]
      );
    }
    $data = [
      'niveles' => $todo
    ];
    $this->load->view("pages/descargar_nivel", $data);
  }

  public function ingresar($id, $invalido = '')
  {
    $data['id'] = '';
    $valido = $this->biblioteca_model->consulta_solicitud_id($id);
    if (!empty($valido)) {
      $data['id'] = $id;
      if ($invalido != '') $data['mensaje'] = 3;
    }
    $this->load->view("pages/validar_codig_bib", $data);
  }

  public function encuesta($id_solicitud, $usuario)
  {
    $data['id_solicitud'] =  $id_solicitud;
    $data['usuario'] = $usuario;
    $info_estudiante = $this->biblioteca_model->validar_estudiante_solicitud($id_solicitud, $usuario);
    if (empty($info_estudiante)) {
      $data['mensaje'] = 2;
    } else {
      $encuesta = $this->biblioteca_model->validar_encuesta($info_estudiante->{'id'});
      if (!empty($encuesta)) {
        $data['mensaje'] = 1;
      } else {
        $data['codigo'] = $info_estudiante->{'codigo'};
        $data['tipo_solicitud'] = $info_estudiante->{'tipo_solicitud'};
        $data['id_tipo_solicitud'] = $info_estudiante->{'id_tipo_solicitud'};
      }
    }
    $this->load->view("pages/validar_codig_bib", $data);
  }

  public function encuesta_success()
  {
    $data['success'] = 'si';
    $this->load->view("pages/validar_codig_bib", $data);
  }

  public function logear()
  {
    require_once(APPPATH . '../LDAP/ldap.php');
    $usuario = $this->input->post('usuario');
    $contrasena = $this->input->post('contrasena');
    $id_solicitud = $this->input->post('id_solicitud');
    $existe = mailboxpowerloginrd($usuario, $contrasena);
    if ($existe == "0" || $existe == '') {
      redirect("/biblioteca/libros_a_tu_clase/ingresar/$id_solicitud/invalido");
    } else $this->encuesta($id_solicitud, $usuario);
  }

  public function buscar_empleado()
  {
    $personas = array();
    if ($this->Super_estado == true) {
      $dato = $this->input->post('dato');
      $tipo = $this->input->post('tipo_busqueda');
      $month = $this->input->post('month');
      $id_sol = $this->input->post('id_solicitud');
      $buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1";
      if ($tipo == 'aux_bib') $buscar .= " AND bs.id = $id_sol AND bs.estado = 1";
      $personas = $tipo != 'aux_bib' && empty($dato) ? array() : $this->biblioteca_model->buscar_empleado($buscar, $tipo, $id_sol, $month);
    } else {
      $personas = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }
    echo json_encode($personas);
    return;
  }

  public function buscar_estudiante()
  {
    $personas = array();
    if ($this->Super_estado == true) {
      $dato = $this->input->post('dato');
      $tabla = $this->input->post('tabla');
      $buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1";
      if (!empty($dato)) $personas = $this->biblioteca_model->buscar_estudiante($buscar, $tabla);
    } else {
      $personas = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }
    echo json_encode($personas);
    return;
  }

  public function asignar_hora_fin($hora_inicio, $capacitaciones)
  {
    $sum = 0;
    if ($capacitaciones) {
      foreach ($capacitaciones as $row) {
        $sum += $row['duracion'];
      }
    }
    $sum *= 60;
    $hora_fin = strtotime($hora_inicio) + $sum;
    return date('H:i', $hora_fin);
  }

  public function guardar_solicitud()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bib" ? true : false;
        $fecha_prestamo = $this->input->post('fecha_prestamo');
        $hora_entrega = $this->input->post('hora_entrega');
        $celular = $this->input->post('celular');
        $capacitaciones = $this->input->post('capacitaciones');
        $hora_retiro = $this->input->post('hora_retiro') ? $this->input->post('hora_retiro') : $this->asignar_hora_fin($hora_entrega, $capacitaciones);
        $id_bloque = $this->input->post('id_bloque');
        $id_salon = $this->input->post('id_salon');
        $libro = $this->input->post('libro');
        $estudiantes = $this->input->post('estudiantes');
        $libros = $this->input->post('libros');
        $id_estado_solicitud = $this->input->post('id_estado_solicitud');
        $id_tipo_solicitud = $this->input->post('id_tipo_solicitud');
        $aux = $this->input->post('solicitante');
        $id_materia_doc = $this->input->post('id_materia_doc');
        $fecha_inicio = $fecha_prestamo . '-' . $hora_entrega;
        $fecha_fin = $fecha_prestamo . '-' . $hora_retiro;
        $id_usuario_registra = $_SESSION['persona'];
        $solicitante = $administra && !empty($aux) ? $aux['id']  : $id_usuario_registra;
        $data_estudiantes = array();
        $data_libros_sol = array();
        $data_libros = array();
        $data_capa = array();

        $num = $this->verificar_campos_numericos(['Bloque' => $id_bloque, 'Salon' => $id_salon, 'Celular' => $celular]);
        $str = $this->verificar_campos_string(['Fecha de prestamo' => $fecha_prestamo, 'Hora de Entrega' => $hora_entrega, 'Hora de Retiro' => $hora_retiro]);
        if (is_array($str)) {
          $resp = ['mensaje' => "El campo " . $str['field'] . "  no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else if (is_array($num)) {
          $resp = ['mensaje' => "El campo " . $num['field'] . "  no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else if (strlen((string)$celular) != 10) {
          $resp = ['mensaje' => "El numero de celular ingresado no es valido.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $fecha = $this->validateDate($fecha_prestamo, 'Y-m-d');
          $val_fecha  = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bib"  ?  [true, 0] : $this->validar_fechas($fecha_prestamo, 'Y-m-d');
          $hora_inicio = $this->validateTime($hora_entrega);
          $hora_fin = $this->validateTime($hora_retiro);
          $val_horas = $this->validarMayor($hora_entrega, $hora_retiro);

          if (!$fecha) {
            $resp = ['mensaje' => "Por favor seleccione fechas validas y superior a la fecha actual.", 'tipo' => "info", 'titulo' => "Oops."];
          } else if (!$val_fecha[0]) {
            $resp = ['mensaje' => "La fecha de prestamo se solicita con $val_fecha[1] dias de anticipación", 'tipo' => "info", 'titulo' => "Oops."];
          } else if (!$hora_inicio || $hora_entrega == 'NaN:NaN') {
            $resp = ['mensaje' => "Por favor seleccione una hora de entrega valida", 'tipo' => "info", 'titulo' => "Oops."];
          } else if (!$hora_fin || $hora_retiro == 'NaN:NaN') {
            $resp = ['mensaje' => "Por favor seleccione una hora de retiro valida", 'tipo' => "info", 'titulo' => "Oops."];
          } else if (!$val_horas) {
            $resp = ['mensaje' => "La hora de entrega es superior o igual a la hora de retiro.", 'tipo' => "info", 'titulo' => "Oops."];
          } else if (!$estudiantes) {
            $resp = ['mensaje' => "Debe ingresar por lo menos un estudiante.", 'tipo' => "info", 'titulo' => "Oops."];
          } else if ($id_tipo_solicitud == 'Bib_Lib' && empty($libros)) {
            $resp = ['mensaje' => "ingrese por lo menos un libro o una temática.", 'tipo' => "info", 'titulo' => "Oops.!"];
          } else {

            //$materia_info = $id_materia_doc ? $this->biblioteca_model->obtener_materia($id_materia_doc) : null;
            $cedula_docente = '';
            $materia_selecta = [];
            $_where = ['p.id' => $solicitante];
            $consulta = $this->pages_model->buscar_persona($_where);
            $cedula_docente = $consulta[0]['identificacion'];
            $materia_info = $this->pages_model->get_materias_por_docente_sicuc($cedula_docente);
            if ($materia_info['data']) {
              foreach ($materia_info['data'] as $row) {
                if ($row['id'] == $id_materia_doc) {
                  $materia_selecta = $row;
                }
              }
            }

            $data = [
              'id_usuario_registra' => $id_usuario_registra,
              'id_solicitante' => $solicitante,
              'id_tipo_solicitud' => $id_tipo_solicitud,
              'id_estado_solicitud' => 'Bib_Sol_E',
              'fecha_inicio' => $fecha_inicio,
              'fecha_fin' => $fecha_fin,
              'id_bloque' => $id_bloque,
              'id_salon' => $id_salon,
              'materia_sol' => $id_materia_doc ? $materia_selecta['materia'] . " - " . $materia_selecta['grupo']  : null,
              'programa_sol' => $id_materia_doc ? $materia_selecta['nombre_programa'] : null,
              'celular' => $celular
            ];
            $add = $this->biblioteca_model->guardar_datos($data, 'biblioteca_solicitudes');
            if ($add != 0) {
              $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
              $solicitud = $this->biblioteca_model->traer_ultima_solicitud($id_usuario_registra);
              $data_solicitud_estado = [
                'id_solicitud' => $solicitud->{'id'},
                'id_estado' => 'Bib_Sol_E',
                'id_usuario_registro' => $id_usuario_registra,
                'id_bloque' => $id_bloque,
                'id_salon' => $id_salon,
                'observacion' => null
              ];
              $add_estado = $this->biblioteca_model->guardar_datos($data_solicitud_estado, 'biblioteca_estado_solicitud');
              if ($add != 0) {
                $resp = ['mensaje' => "Error al guardar la información de los libros, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
              } else {
                foreach ($estudiantes as $estudiante) {
                  array_push($data_estudiantes, [
                    'id_estudiante_sol' => $estudiante['id'],
                    'id_solicitud' => $solicitud->{'id'},
                    'id_usuario_registra' => $id_usuario_registra,
                    'tabla' => $estudiante['tabla']
                  ]);
                }

                if (!empty($data_estudiantes)) {
                  $add2 = $this->biblioteca_model->guardar_datos($data_estudiantes, 'biblioteca_estudiante_sol', 2);
                  $resp = ['mensaje' => "La solicitud fue guardada de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                  if ($add2 != 0) $resp = ['mensaje' => "Error al guardar los estudiantes, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                }

                if ($id_tipo_solicitud == 'Bib_Lib') {
                  foreach ($libros as $libro) {
                    array_push($data_libros, [
                      'id_solicitud' => $solicitud->{'id'},
                      'id_usuario_registra' => $id_usuario_registra,
                      'nombre_libro' => $libro
                    ]);
                  }

                  if (!empty($data_libros)) {
                    $add2 = $this->biblioteca_model->guardar_datos($data_libros, 'biblioteca_libros', 2);
                    $solicitud = $this->biblioteca_model->traer_ultima_solicitud($id_usuario_registra);
                    $resp = ['mensaje' => "La solicitud fue guardada de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'data_sol' => $solicitud];
                    if ($add2 != 0) $resp = ['mensaje' => "Error al guardar los libros, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                  }
                } elseif ($id_tipo_solicitud == 'Bib_Cap') {
                  foreach ($capacitaciones as $row) {
                    array_push($data_capa, [
                      'id_solicitud' => $solicitud->{'id'},
                      'id_usuario_registro' => $id_usuario_registra,
                      'id_capacitacion' => $row['id_capacitacion']
                    ]);
                  }

                  if (!empty($data_capa)) {
                    $add2 = $this->biblioteca_model->guardar_datos($data_capa, 'biblioteca_capacitaciones_solicitud', 2);
                    $solicitud = $this->biblioteca_model->traer_ultima_solicitud($id_usuario_registra);
                    $resp = ['mensaje' => "La solicitud fue guardada de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'data_sol' => $solicitud];
                    if ($add2 != 0) $resp = ['mensaje' => "Error al guardar las capacitaciones, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
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

  public function guardar_nuevo_estudiante()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bib" ? true : false;
        $identificacion = $this->input->post("identificacion");
        $tipo_identificacion = $this->input->post("tipo_identificacion");
        $apellido = $this->input->post("apellido");
        $segundoApellido = $this->input->post("segundoapellido");
        $nombre = $this->input->post("nombre");
        $segundoNombre = $this->input->post("segundonombre");
        $celular = $this->input->post("celular");
        $correo = $this->input->post("correo");
        $tipo = $this->input->post("tipo");
        $id_solicitud = $this->input->post("id_solicitud");
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud ? $solicitud->{'id_estado_solicitud'} : null;
        if ($estado_actual != 'Bib_Sol_E' && $estado_actual != null) {
          $resp = ['mensaje' => "No puede realizar esta acción debido a que la solicitud se encuentra en tramite o ya fue finalizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $data_est = [
            'identificacion' => $identificacion,
            'tipo_identificacion' => $tipo_identificacion,
            'nombre' => $nombre,
            'segundo_nombre' => $segundoNombre,
            'apellido' => $apellido,
            'segundo_apellido' => $segundoApellido,
            'celular' => $celular,
            'correo' => $correo,
            'tipo' => $tipo,
          ];
          $ver = $this->biblioteca_model->verificar_identificacion($identificacion);
          $cor = $this->biblioteca_model->verificar_correo($correo);
          if (empty($ver)) {
            if (empty($cor)) {
              $add = $this->biblioteca_model->guardar_datos($data_est, 'visitantes');
              if ($add != 0) {
                $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
              } else if (!empty($data_est)) {
                $resp = ['mensaje' => "El estudiante fue agregado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
              }
            } else {
              $resp = ['mensaje' => "Ya existe un estudiante con ese correo.", 'tipo' => "info", 'titulo' => "Oops.!"];
            }
          } else {
            $resp = ['mensaje' => "Ya existe un estudiante con ese numero de identificación.", 'tipo' => "info", 'titulo' => "Oops.!"];
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function modificar_solicitud()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $fecha_prestamo = $this->input->post('fecha_prestamo');
        $hora_entrega = $this->input->post('hora_entrega');
        $capacitaciones = $this->input->post('capacitaciones');
        $hora_retiro = $this->input->post('hora_retiro') ? $this->input->post('hora_retiro') : $this->asignar_hora_fin($hora_entrega, $capacitaciones);
        $id_bloque = $this->input->post('id_bloque');
        $id_salon = $this->input->post('id_salon');
        $id = $this->input->post('id_solicitud');
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id);
        $estado_actual = $solicitud->{'id_estado_solicitud'};
        $tipo_solicitud = $solicitud->{'id_tipo_solicitud'};
        $id_usuario_registra = $_SESSION['persona'];
        $fecha_inicio = $fecha_prestamo . '-' . $hora_entrega;
        $fecha_fin = $fecha_prestamo . '-' . $hora_retiro;
        $str = $this->verificar_campos_string(['Fecha de prestamo' => $fecha_prestamo, 'Hora de Entrega' => $hora_entrega, 'Hora de Retiro' => $hora_retiro]);
        $num = $this->verificar_campos_numericos(['Bloque' => $id_bloque, 'Salon' => $id_salon]);
        if (is_array($str)) {
          $resp = ['mensaje' => "El campo " . $str['field'] . "  no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else if (is_array($num)) {
          $resp = ['mensaje' => "El campo " . $num['field'] . "  no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
        }
        if ($estado_actual != 'Bib_Sol_E') {
          $resp = ['mensaje' => "No se puede modificar la solicitud, debido que se encuentra en procesamiento o ya fue finalizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $data_sol = [
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'id_bloque' => $id_bloque,
            'id_salon' => $id_salon
          ];
          $data_log = [
            'id_solicitud' => $id,
            'id_estado' => $estado_actual,
            'id_bloque' => $id_bloque,
            'id_salon' => $id_salon,
            'id_usuario_registro' => $id_usuario_registra
          ];
          $resp = ['mensaje' => "La solicitud fue modificada de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          $mod_sol = $this->biblioteca_model->modificar_datos($data_sol, 'biblioteca_solicitudes', $id);
          $add_log = $this->biblioteca_model->guardar_datos($data_log, 'biblioteca_estado_solicitud');
          if ($add_log != 0) $resp = ['mensaje' => "Error al modificar solicitud, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          if ($mod_sol != 0)  $resp = ['mensaje' => "Error al modificar solicitud, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          if ($tipo_solicitud == 'Bib_Cap') {
            $capas = $this->biblioteca_model->obtener_capacitaciones($id);
            $sw = false;
            foreach ($capacitaciones as $row) {
              foreach ($capas as $key) {
                if ($row['id_capacitacion'] == $key['id_capacitacion']) {
                  $sw = true;
                  break;
                } else $sw = false;
              }
              if (!$sw) {
                $data_capa = [
                  "id_solicitud" => $id,
                  "id_capacitacion" => $row['id_capacitacion'],
                  "id_usuario_registro" => $_SESSION['persona']
                ];
                $add = $this->biblioteca_model->guardar_datos($data_capa, 'biblioteca_capacitaciones_solicitud');
                if ($add != 0) $resp = ['mensaje' => "Error al modificar solicitud, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
              }
            }
            $sw = false;
            foreach ($capas as $row) {
              foreach ($capacitaciones as $key) {
                if ($row['id_capacitacion'] == $key['id_capacitacion']) {
                  $sw = true;
                  break;
                } else $sw = false;
              }
              if (!$sw) {
                $data_capa = [
                  "estado" => 0,
                  "id_usuario_elimina" => $_SESSION['persona'],
                  "fecha_elimina" => date("Y-m-d H:i:s")
                ];
                $mod = $this->biblioteca_model->modificar_datos($data_capa, 'biblioteca_capacitaciones_solicitud', $row['id']);
                if ($mod != 0) $resp = ['mensaje' => "Error al modificar solicitud, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
              }
            }
          }
        }
      }
    }
    echo json_encode($resp);
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

  //VALIDACION DE LAS FECHAS
  public function validateDate($date, $format = 'Y-m-d')
  {
    $fecha_actual = date($format);
    $d = DateTime::createFromFormat($format, $date);
    if ($d->format($format) < $fecha_actual) return false;
    return $d && $d->format($format) == $date;
  }

  public function validateTime($time)
  {
    $pattern = "/^([0-1][0-9]|[2][0-3])[\:]([0-5][0-9])$/";
    if (preg_match($pattern, $time))
      return true;
    return false;
  }

  public function validarMayor($time1, $time2)
  {
    $hora1 = strtotime($time1);
    $hora2 = strtotime($time2);
    if ($hora1 >= $hora2)
      return false;
    return true;
  }
  public function listar_solicitud()
  {
    if (!$this->Super_estado) {
      $solicitudes = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id =  $this->input->post('id');
      $id_tipo_solicitud =  $this->input->post('id_tipo_solicitud');
      $id_estado_solicitud =  $this->input->post('id_estado_solicitud');
      $fecha_inicial = $this->input->post('fecha_inicial');
      $fecha_final = $this->input->post('fecha_final');
      $admin = $_SESSION["perfil"] == "Per_Admin" ? true : false;
      $adm_bib = $_SESSION["perfil"] == "Per_Adm_Bib" ? true : false;
      $aux_bib = $_SESSION["perfil"] == "Per_Aux_Bib" ? true : false;
      $persona  = $_SESSION["persona"];
      $resp = $this->Super_estado ? $this->biblioteca_model->listar_solicitud($id, $id_tipo_solicitud, $id_estado_solicitud, $fecha_inicial, $fecha_final) : array();
      $solicitudes = array();

      $ver_solicitado = '<span style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
      $ver_revisado = '<span style="background-color: #2E79E5;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
      $ver_preparado = '<span style="background-color: #EABD32;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
      $ver_entregado = '<span style="background-color: #17a2b8;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';


      $ver_cancelado = '<span style="background-color: #d9534f;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
      $ver_negado = '<span style="background-color: #d9534f;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
      $ver_finalizado = '<span style="background-color: #39B23B;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';

      $btn_revisar = '<span title="Revisar" data-toggle="popover" data-trigger="hover" class="fa fa-reply btn btn-default revisar" style="color:#2E79E5"></span>';
      $btn_entregar = '<span title="Entregar" data-toggle="popover" data-trigger="hover" class="glyphicon glyphicon-edit btn btn-default entregar" style="color:#17a2b8"></span>';
      $btn_preparar = '<span title="Preparar" data-toggle="popover" data-trigger="hover" class="fa fa-retweet btn btn-default preparar" style="color:#EABD32"></span>';
      $btn_cancelar = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default cancelar" style="color:#d9534f"></span>';
      $btn_negar = '<span title="Negar" data-toggle="popover" data-trigger="hover" class="fa fa-ban btn btn-default negar" style="color:#d9534f"></span>';
      $btn_finalizar = '<span title="Finalizar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default finalizar" style="color:#39B23B"></span>';
      $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
      $btn_abierta = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn" style="color:#428bca"></span>';

      foreach ($resp as $row) {
        $row['ver'] = $ver_solicitado;
        $row['accion'] = $btn_cerrada;
        $permiso = $row['permiso'];
        $asignado = $row['asignado'];
        $solicitante = $row['id_solicitante'];
        $estado_solicitud = $row['id_estado_solicitud'];
        $solicitante = $row['id_solicitante'];
        $tipo_solicitud = $row['id_tipo_solicitud'];
        $push = true;
        if ($tipo_solicitud == 'Bib_Lib') {
          if ($estado_solicitud == 'Bib_Sol_E') {
            $row['accion'] = $persona == $solicitante || $admin ? "$btn_cancelar " : '';
            $row['accion'] .= $admin  || !is_null($permiso) || !is_null($asignado) ? "$btn_revisar $btn_negar " : '';
          } else if ($estado_solicitud == 'Bib_Rev_E') {
            $row['ver'] = $ver_revisado;
            $row['accion'] = $admin  || !is_null($permiso) || !is_null($asignado) ? "$btn_preparar $btn_negar" : $btn_abierta;
          } else if ($estado_solicitud == 'Bib_Pre_E') {
            $row['ver'] = $ver_preparado;
            $row['accion'] = $admin  || !is_null($permiso) || !is_null($asignado) ? "$btn_entregar $btn_negar" : $btn_abierta;
          } else if ($estado_solicitud == 'Bib_Ent_E') {
            $row['ver'] = $ver_entregado;
            $row['accion'] = $admin  || !is_null($permiso) || !is_null($asignado) ? "$btn_finalizar" : $btn_abierta;
          } else if ($estado_solicitud == 'Bib_Can_E' || $estado_solicitud == 'Bib_Rec_E') $row['ver'] = $ver_cancelado;
          else if ($estado_solicitud == 'Bib_Fin_E')  $row['ver'] = $ver_finalizado;
        } else if ($tipo_solicitud == 'Bib_Cap') {
          if ($estado_solicitud == 'Bib_Sol_E') {
            $row['accion'] = $persona == $solicitante || $admin ? "$btn_cancelar " : '';
            $row['accion'] .= $admin || !is_null($permiso) || !is_null($asignado) ? "$btn_revisar $btn_negar " : '';
          } else if ($estado_solicitud == 'Bib_Rev_E') {
            $row['ver'] = $ver_revisado;
            $row['accion'] = $admin  || !is_null($permiso) || !is_null($asignado) ? "$btn_finalizar $btn_negar" : $btn_abierta;
          } else if ($estado_solicitud == 'Bib_Can_E' || $estado_solicitud == 'Bib_Rec_E') $row['ver'] = $ver_cancelado;
          else if ($estado_solicitud == 'Bib_Fin_E') $row['ver'] = $ver_finalizado;
        }

        if (($adm_bib || $aux_bib) && is_null($permiso) && is_null($asignado) && $solicitante != $persona) $push = false;
        if ($push) array_push($solicitudes, $row);
      }
    }
    echo json_encode($solicitudes);
  }

  public function verificar_empleado_bib()
  {
    $empleados = $this->biblioteca_model->obtener_empleados('Bib_dep');
    $sw = false;
    foreach ($empleados as $row) {
      $sw = $row['id'] == $_SESSION['persona'] ? true : false;
      if ($sw) break;
    }
    return $sw;
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
        $observacion = $this->input->post("mensaje");
        $bloque_cap = $this->input->post("bloque_cap");
        $salon_cap = $this->input->post("salon_cap");
        $recursos = $this->input->post("recursos");
        $usuario = $_SESSION["persona"];
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id);
        $tipo_solicitud = $solicitud->{'id_tipo_solicitud'};
        $seguir = true;

        if ($estado == "Bib_Rec_E") {
          trim($observacion) == "" || empty(trim($observacion)) ? $seguir = false : false;
        }

        if ($seguir) {
          $data = [];
          if ($tipo_solicitud == 'Bib_Cap') {
            $data = ["id_estado_solicitud" => $estado, "recurso" => $recursos];
          } else {
            $data = $estado == 'Bib_Pre_E' ? ["id_estado_solicitud" => $estado, "peso" => $observacion] : ["id_estado_solicitud" => $estado];
          }
          $month = date('m');
          $sw = true;
          $ent = false;
          $ret = false;
          $cap = false;
          $valido = $this->validar_estado($id, $estado);
          $ver_num = $this->verificar_campos_numericos(['Bloque' => $bloque_cap, 'Salon' => $salon_cap]);
          $ver_str = $this->verificar_campos_string(['Recursos' => $recursos]);
          $ver_peso = $estado == 'Bib_Pre_E' ? $this->verificar_campos_numericos(['Peso' => $observacion]) : 0;
          if ($valido) {
            if ($estado == 'Bib_Rev_E') {
              if ($tipo_solicitud == 'Bib_Lib') {
                $auxiliares = $this->biblioteca_model->obtener_auxiliares($id);
                foreach ($auxiliares as $row) {
                  if ($row['carga'] == "Acc_Ent") $ent = true;
                  if ($row['carga'] == "Acc_Ret") $ret = true;
                }
                if ($ent == false) {
                  $resp = ['mensaje' => "Debe seleccionar por lo menos un auxiliar de entrega.", 'tipo' => "info", 'titulo' => "Oops.!"];
                  $sw = false;
                } else if ($ret == false) {
                  $resp = ['mensaje' => "Debe seleccionar por lo menos un auxiliar de retiro.", 'tipo' => "info", 'titulo' => "Oops.!"];
                  $sw = false;
                }
              } else if ($tipo_solicitud == 'Bib_Cap') {
                $auxiliares = $this->biblioteca_model->obtener_auxiliares($id);
                foreach ($auxiliares as $row) {
                  if ($row['carga'] == "Acc_Cap") $cap = true;
                }
                if ($cap == false) {
                  $resp = ['mensaje' => "Debe seleccionar por lo menos un auxiliar encargado de capacitar.", 'tipo' => "info", 'titulo' => "Oops.!"];
                  $sw = false;
                }
                if (is_array($ver_num)) {
                  $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser seleccionado o calificado.", 'tipo' => "info", 'titulo' => "Oops.!"];
                  $sw = false;
                } else if (is_array($ver_str)) {
                  $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser seleccionado o calificado.", 'tipo' => "info", 'titulo' => "Oops.!"];
                  $sw = false;
                } else if ($estado == 'Bib_Pre_E' && is_array($ver_peso)) {
                  $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser seleccionado o calificado.", 'tipo' => "info", 'titulo' => "Oops.!"];
                }
              }
            }

            if ($sw) {
              $resp = ['mensaje' => "La solicitud fue gestionada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
              $mod = $this->biblioteca_model->modificar_datos($data, 'biblioteca_solicitudes', $id);
              if ($mod != 0) $resp = ['mensaje' => "Error al gestionar la solicitud, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
              else {
                if ($tipo_solicitud == 'Bib_Lib') {
                  $data_solicitud_estado = [
                    'id_solicitud' => $id,
                    'id_estado' => $estado,
                    'observacion' => $observacion ? $observacion : NULL,
                    'id_usuario_registro' => $usuario
                  ];
                  $add = $this->biblioteca_model->guardar_datos($data_solicitud_estado, 'biblioteca_estado_solicitud');
                  if ($estado == 'Bib_Fin_E') {
                    $gen = $this->generar_codigo($id);
                  }
                } else if ($tipo_solicitud == 'Bib_Cap') {
                  if ($estado == 'Bib_Fin_E') {
                    $gen = $this->generar_codigo($id);
                    $ubicacion = $this->biblioteca_model->obtener_ubicacion($id);
                    $bloque_cap = $ubicacion->{'id_bloque'};
                    $salon_cap = $ubicacion->{'id_salon'};
                    $data_solicitud_estado = [
                      'id_solicitud' => $id,
                      'id_estado' => $estado,
                      'observacion' => $observacion ? $observacion : NULL,
                      'id_usuario_registro' => $usuario,
                      'id_bloque' => $bloque_cap,
                      'id_salon' => $salon_cap
                    ];
                    $add = $this->biblioteca_model->guardar_datos($data_solicitud_estado, 'biblioteca_estado_solicitud');
                  } else {
                    $data_solicitud_estado = [
                      'id_solicitud' => $id,
                      'id_estado' => $estado,
                      'observacion' => $observacion ? $observacion : NULL,
                      'id_usuario_registro' => $usuario,
                      'id_bloque' => $bloque_cap,
                      'id_salon' => $salon_cap
                    ];
                    $add = $this->biblioteca_model->guardar_datos($data_solicitud_estado, 'biblioteca_estado_solicitud');
                  }
                }
              }
            }
          } else  $resp = ['mensaje' => "La solicitud ya fue gestionada anteriormente o no tiene permisos para realizar esta acción.", 'tipo' => "info", 'titulo' => "Oops.!", "recargar" => true];
        } else {
          $resp = ['mensaje' => "¡Debe ingresar una observación valida para poder continuar!", 'tipo' => "warning", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function generar_codigo($id)
  {
    $solicitud = $this->biblioteca_model->consulta_solicitud_id($id);
    $tipo_solicitud = $solicitud->{'id_tipo_solicitud'};
    if ($tipo_solicitud == 'Bib_Lib') $estudiantes = $this->biblioteca_model->listar_estudiantes_asignados($id);
    else if ($tipo_solicitud == 'Bib_Cap') $estudiantes = $this->biblioteca_model->listar_estudiante_solicitud($id);
    foreach ($estudiantes as $row) {
      $code = $row['id_solicitud'] . $row['id'];
      $data = [
        'codigo_acceso' => $code
      ];
      $mod = $this->biblioteca_model->modificar_datos($data, 'biblioteca_estudiante_sol', $row['id']);
    }
    return true;
  }

  public function obtener_estudiantes_cod()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('buscar');
      $resp = $this->biblioteca_model->listar_estudiante_solicitud($id);
    }
    echo json_encode($resp);
  }



  public function listar_estudiante_solicitud()
  {
    if (!$this->Super_estado) {
      $data = ['tipo' => "sin_session"];
    } else {
      $btnEliminar = '<span title="Eliminar" style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>';
      $btnAsignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" style="color:#2E79E5"></span>';
      $btn_asignado = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
      $btn_observacion = '<span title="Observación" style="color:#2E79E5" class="fa fa-pencil btn btn-default pointer observacion"></span>';

      $id = $this->input->post("id");
      $table = $this->input->post("tabla");
      $solicitud = $this->biblioteca_model->consulta_solicitud_id($id);
      $estado = $solicitud->{'id_estado_solicitud'};
      $data = array();
      $resp = $this->Super_estado ? $this->biblioteca_model->listar_estudiante_solicitud($id) : array();

      foreach ($resp as $row) {
        $row['accion'] = $btn_asignado;
        if ($estado != 'Bib_Rec_E' && $estado != 'Bib_Can_E' && $estado != 'Bib_Fin_E') {
          if ($table == '#tabla_estudiante_solicitudes') $row['accion'] = "$btnEliminar $btn_observacion";
        }
        if ($table == '#tabla_estudiante_asignacion' || $table == '#tabla_estudiante_entrega') $row['accion'] = $btnAsignar;
        array_push($data, $row);
      }
    }
    echo json_encode($data);
  }

  public function listar_auxiliares()
  {
    if (!$this->Super_estado) {
      $data = ['tipo' => "sin_session"];
    } else {
      $btn_retirar = '<span title="Retirar" data-toggle="popover" data-trigger="hover" class="glyphicon glyphicon-remove btn btn-default retirar" style="color:#d9534f"></span>';
      $btn_cambiar = '<span title="Cambiar" data-toggle="popover" data-trigger="hover" class="fa fa-retweet btn btn-default cambiar" style="color:#2E79E5"></span>';
      $btn_inactivo = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

      $ver = '<span style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
      $ver_retirado = '<span style="background-color: #d9534f;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';

      $id = $this->input->post("id");
      $data = array();
      $resp = $this->Super_estado ? $this->biblioteca_model->listar_auxiliares($id) : array();
      $solicitud = $this->biblioteca_model->consulta_solicitud_id($id);
      $estado_solicitud = $solicitud->{'id_estado_solicitud'};
      foreach ($resp as $row) {
        if ($row['estado'] == 1) {
          $row['ver'] = $ver;
          if ($estado_solicitud != 'Bib_Sol_E' && $estado_solicitud != 'Bib_Rev_E') {
            $row['botones'] = $btn_inactivo;
          } else {
            $row['botones'] = "$btn_cambiar $btn_retirar";
          }
        } else {
          $row['ver'] = $ver_retirado;
          $row['botones'] = $btn_inactivo;
        }
        array_push($data, $row);
      }
      echo json_encode($data);
    }
  }

  public function listar_historial_solicitud()
  {
    $id = $this->input->post("id");
    $resp = $this->Super_estado ? $this->biblioteca_model->listar_historial_solicitud($id) : ['tipo' => "sin_session"];
    echo json_encode($resp);
  }

  public function listar_historial_libro()
  {
    $id = $this->input->post("id");
    $resp = $this->Super_estado ? $this->biblioteca_model->listar_historial_libro($id) : ['tipo' => "sin_session"];
    echo json_encode($resp);
  }

  public function listar_historial_auxiliar()
  {
    $id = $this->input->post("id");
    $resp = $this->Super_estado ? $this->biblioteca_model->listar_historial_auxiliar($id) : ['tipo' => "sin_session"];
    echo json_encode($resp);
  }

  public function listar_encuestas()
  {
    $id = $this->input->post("id_solicitud");
    $resp = $this->Super_estado ? $this->biblioteca_model->listar_encuestas($id) : ['tipo' => "sin_session"];
    echo json_encode($resp);
  }

  function eliminar_estudiante_solicitud()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_elimina == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $id_solicitud = $this->input->post("id_solicitud");
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud->{'id_estado_solicitud'};

        if ($estado_actual == 'Bib_Sol_E' || $estado_actual == 'Bib_Rev_E') {
          $fecha = date("Y-m-d H:i:s");
          $usuario = $_SESSION["persona"];
          $data = [
            "id_usuario_elimina" => $usuario,
            "fecha_elimina" => $fecha,
            "estado" => 0,
          ];
          $resp = ['mensaje' => "El estudiante fue eliminado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          $del = $this->biblioteca_model->modificar_datos($data, 'biblioteca_estudiante_sol', $id);

          if ($del != 0) $resp = ['mensaje' => "Error al eliminar el servicio, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        } else {
          $resp = ['mensaje' => "No es posible realizar esta acción ya que La solicitud se encuentra en tramite o terminada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function eliminar_libro_solicitud()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_elimina == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $id_solicitud = $this->input->post("id_solicitud");
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud->{'id_estado_solicitud'};
        if ($estado_actual == 'Bib_Sol_E' || $estado_actual == 'Bib_Rev_E') {
          $fecha = date("Y-m-d H:i:s");
          $usuario = $_SESSION["persona"];
          $data = [
            "id_usuario_elimina" => $usuario,
            "fecha_elimina" => $fecha,
            "id_estado" => 0,
          ];
          $resp = ['mensaje' => "El Libro fue eliminado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          $del = $this->biblioteca_model->modificar_datos($data, 'biblioteca_libros', $id);
          if ($del != 0) $resp = ['mensaje' => "Error al eliminar el servicio, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        } else {
          $resp = ['mensaje' => "No es posible realizar esta acción ya que La solicitud se encuentra en tramite o terminada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function asignar_libro()
  {
    $administra  = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bib" || $_SESSION["perfil"] == "Per_Aux_Bib"  ?  true : false;
    $persona  = $_SESSION["persona"];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_libro = $this->input->post("id_libro");
        $id = $this->input->post("id");
        $id_solicitud = $this->input->post("id_solicitud");
        $observacion = $this->input->post("mensaje");
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud->{'id_estado_solicitud'};
        if ($estado_actual != 'Bib_Rev_E' && $estado_actual != 'Bib_Pre_E') {
          $resp = ['mensaje' => "No puede realizar esta acción debido a que la solicitud se encuentra en tramite o ya fue finzalizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $data = [
            "id_asignado" => $id,
          ];
          $data_asig = [
            "id_libro" => $id_libro,
            "id_asignado" => $id,
            "id_usuario_registro" => $persona,
            "observacion" => $observacion ? $observacion : NULL,
          ];
          $mod = $administra ? $this->biblioteca_model->modificar_datos($data, 'biblioteca_libros', $id_libro) : false;
          $resp = ['mensaje' => "El libro fue asignado correctamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          if ($mod != 0) {
            $resp = ['mensaje' => "Error al asignar el libro, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          } else {
            $add = $administra ? $this->biblioteca_model->guardar_datos($data_asig, "biblioteca_libros_asignados") : false;
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function desasignar_libro()
  {
    $administra  = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bib"  ?  true : false;
    $persona  = $_SESSION["persona"];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_libro = $this->input->post('id_libro');
        $id_asignado = $this->input->post('id_asignado');
        $observacion = $this->input->post('mensaje');
        $id_solicitud = $this->input->post("id_solicitud");
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud->{'id_estado_solicitud'};
        if ($estado_actual != 'Bib_Rev_E' && $estado_actual != 'Bib_Pre_E') {
          $resp = ['mensaje' => "No puede realizar esta acción debido a que la solicitud se encuentra en tramite o ya fue finzalizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $data = [
            "id_estado" => 2,
            "nota_retiro" => $observacion ? $observacion : NULL,
            "id_persona_retira" => $persona
          ];
          $mod = $administra ? $this->biblioteca_model->modificar_datos($data, 'biblioteca_libros', $id_libro) : false;
          $resp = ['mensaje' => "El libro fue desasignado correctamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          if ($mod != 0) $resp = ['mensaje' => "Error al asignar el libro, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
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
        $tabla = $this->input->post('tabla');
        $identificacion = $this->input->post('identificacion');
        $id_usuario_registra = $_SESSION['persona'];
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud->{'id_estado_solicitud'};

        if ($estado_actual == 'Bib_Fin_E' && $estado_actual == 'Bib_Rec_E' && $estado_actual == 'Bib_Can_E') {
          $resp = ['mensaje' => "No puede realizar esta acción debido a que la solicitud se encuentra en tramite o ya fue finalizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $existe = $this->biblioteca_model->estudiante_solicitud($identificacion, $id_solicitud);
          if ($existe) {
            $resp = ['mensaje' => "El estudiante ya se encuentra asignado.", 'tipo' => "info", 'titulo' => "Oops.!"];
          } else {
            $data_persona = [
              'id_solicitud' => $id_solicitud,
              'id_estudiante_sol' => $id_persona,
              'id_usuario_registra' => $id_usuario_registra,
              'tabla' => $tabla,
            ];
            $add = $this->biblioteca_model->guardar_datos($data_persona, 'biblioteca_estudiante_sol');
            if ($add != 0) {
              $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            }
            if (!empty($data_persona)) {
              $resp = ['mensaje' => "El estudiante fue asignado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            }
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function guardar_libro_nuevo()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id_solicitud = $this->input->post('id_solicitud');
        $id_usuario_registra = $_SESSION['persona'];
        $nombre_libro = $this->input->post('nombre_libro');
        $codigo_de_barra = $this->input->post('codigo_de_barra');
        $formulario = $this->input->post('formulario');
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud->{'id_estado_solicitud'};
        if ($estado_actual != 'Bib_Sol_E' && $estado_actual != 'Bib_Rev_E' && $estado_actual && 'Bib_Pre_E') {
          $resp = ['mensaje' => "No puede realizar esta acción debido a que la solicitud se encuentra en tramite o ya fue finalizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $data_libro = [
            'nombre_libro' => $nombre_libro,
            'id_solicitud' => $id_solicitud,
            'id_usuario_registra' => $id_usuario_registra,
            'codigo_de_barras' => $codigo_de_barra,
          ];
          $ver = $this->biblioteca_model->verificar_codigo($id_solicitud, $codigo_de_barra);
          if (empty($ver) || $formulario == 'form_agregar_libro_nuevo') {
            $add = $this->biblioteca_model->guardar_datos($data_libro, 'biblioteca_libros');
            if ($add != 0) {
              $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            }
            if (!empty($data_libro)) {
              $resp = ['mensaje' => "El libro fue asignado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            }
          } else {
            $resp = ['mensaje' => "Ya existe un libro con ese codigo de barras.", 'tipo' => "error", 'titulo' => "Oops.!"];
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function detalle_libros_a_tu_clase()
  {
    $id = $this->input->post("id");
    $resp = $this->Super_estado ? $this->biblioteca_model->detalle_libros_a_tu_clase($id) : array();
    echo json_encode($resp);
  }

  public function obtener_capacitaciones()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bib" ? true : false;
      $persona = $_SESSION["persona"];

      $resp = array();
      $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o btn btn-default eliminar" style="color:#d9534f"></span>';
      $btn_sinaccion = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

      $buscar = $this->input->post('buscar');
      $solicitud = $this->biblioteca_model->consulta_solicitud_id($buscar);
      $estado = $solicitud->{'id_estado_solicitud'};
      $capacitaciones = $this->biblioteca_model->obtener_capacitaciones($buscar);
      foreach ($capacitaciones as $row) {
        if ($estado == 'Bib_Sol_E' || $estado == 'Bib_Rev_E') {
          $row['accion'] = $btn_eliminar;
        } else {
          $row['accion'] = $btn_sinaccion;
        }
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function obtener_libro()
  {
    if (!$this->Super_estado) {
      $librosA = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $administra  = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bib" || "Per_Aux_Bib" ?  true : false;
      $persona  = $_SESSION["persona"];

      $librosA = array();
      $btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" style="color:#2E79E5"></span>';
      $btn_reasignar = '<span title="Reasignar" data-toggle="popover" data-trigger="hover" class="fa fa-retweet btn btn-default reasignar" style="color:#2E79E5"></span>';
      $btn_asignado = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
      $btn_desasignar = '<span title="Desasignar" data-toggle="popover" data-trigger="hover" class="glyphicon glyphicon-remove btn btn-default desasignar" style="color:#d9534f"></span>';

      $ver_asignar = '<span style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
      $ver_asignado = '<span style="background-color: #39B23B;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
      $ver_desasignado = '<span style="background-color: #d9534f;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';

      $buscar = $this->input->post('buscar');
      $solicitud = $this->biblioteca_model->consulta_solicitud_id($buscar);
      $estado = $solicitud->{'id_estado_solicitud'};
      $libros =  $this->biblioteca_model->obtener_libro($buscar);
      foreach ($libros as $row) {
        $row['ver'] = $ver_asignar;
        $row['accion'] = $btn_asignado;
        $estado_libro = $row['id_asignado'];

        if ($estado == 'Bib_Ent_E' || $estado == 'Bib_Can_E' || $estado == 'Bib_Rec_E' || $estado == 'Bib_Fin_E') {
          $row['accion'] = $btn_asignado;
          $row['ver'] = $row['id_estado'] == 2 ? $ver_desasignado : $ver_asignado;
        } else {
          $row['accion'] = $administra ? "$btn_asignar" : $btn_asignado;
          if ($row['id_estado'] == 2) {
            $row['ver'] = $ver_desasignado;
            $row['accion'] = $btn_asignado;
          } else {
            if ($row['id_asignado']) {
              $row['ver'] = $ver_asignado;
              $row['accion'] = $administra ? "$btn_reasignar $btn_desasignar" : $btn_asignado;
            }
          }
        }
        array_push($librosA, $row);
      }
      echo json_encode($librosA);
    }
  }

  public function obtener_correos()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $administra  = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bib"  ?  true : false;
      $persona  = $_SESSION["persona"];

      $resp = array();
      $buscar = $this->input->post('buscar');
      $solicitud = $this->biblioteca_model->consulta_solicitud_id($buscar);
      $tipo_solicitud = $solicitud->{'id_tipo_solicitud'};
      if ($tipo_solicitud == 'Bib_Lib') $data = $this->biblioteca_model->obtener_correos($buscar);
      else if ($tipo_solicitud == 'Bib_Cap') $data = $this->biblioteca_model->obtener_correos_sol($buscar);
      foreach ($data as $row) {
        if(!empty($row['correo'])){
          array_push($resp, $row);
        }
      }    
    }
    echo json_encode($resp);
  }

  public function obtener_empleados()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bib" ? true : false;
      $persona = $_SESSION["persona"];

      $resp = array();
      $where = "p.id_perfil = 'Per_Adm_Bib' OR p.id_perfil = 'Per_Aux_Bib'";
      $resp = $this->biblioteca_model->obtener_empleados($where);
    }
    echo json_encode($resp);
  }

  public function obtener_correos_aux()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $persona = $_SESSION["persona"];
      $resp = array();
      $buscar = $this->input->post('buscar');
      $resp = $this->biblioteca_model->obtener_correos_aux($buscar);
    }
    echo json_encode($resp);
  }

  public function listar_nivel_capa()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $resp = array();
      $btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" style="color:#2E79E5"></span>';
      $capacitaciones = $this->biblioteca_model->listar_nivel_capa();
      foreach ($capacitaciones as $row) {
        $row['accion'] = $btn_asignar;
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function listar_procesos_bib()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $resp = array();
      $btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" style="color:#2E79E5"></span>';
      $btn_desasignar = '<span title="Desasignar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default retirar" style="color:#d9534f"></span>';
      $btn_administrar = '<span class="fa fa-cog btn btn-default administrar" title="Administrar" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $id = $this->input->post('id');
      $procesos = $administra ? $this->biblioteca_model->listar_procesos_bib($id) : array();
      foreach ($procesos as $row) {
        $row['accion'] = is_null($row['tipo']) ? $btn_asignar : "$btn_desasignar $btn_administrar";
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function listar_estados_bib()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $resp = array();
      $btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" style="color:#2E79E5"></span>';
      $btn_desasignar = '<span title="Desasignar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default retirar" style="color:#d9534f"></span>';
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $id = $this->input->post('id');
      $estados = $administra ? $this->biblioteca_model->listar_estados_bib($id) : array();
      foreach ($estados as $row) {
        $row['accion'] = is_null($row['tipo']) ? $btn_asignar : $btn_desasignar;
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function verificar_causas()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $resp = array();
      $btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" style="color:#2E79E5"></span>';
      $btn_desasignar = '<span title="Desasignar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $causas = $this->input->post('causas');
      foreach ($causas as $row) {
        $row['accion'] = $row['agregado'] == 1 ? $btn_desasignar : $btn_asignar;
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function listar_empleados_turnos()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $resp = array();
      $id = $this->input->post('id');
      $btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" style="color:#2E79E5"></span>';
      $btn_desasignar = '<span title="Desasignar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default retirar" style="color:#d9534f"></span>';
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $empleados = $administra ? $this->biblioteca_model->listar_empleados_turnos($id) : array();
      foreach ($empleados as $row) {
        $row['accion'] = is_null($row['tipo']) ? $btn_asignar : $btn_desasignar;
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function asignar_turno()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $persona = $_SESSION["persona"];
      $id = $this->input->post('id');
      $id_auxiliar = $this->input->post('id_auxiliar');
      $ver = $this->biblioteca_model->verificar_turnos_aux($id, $id_auxiliar);
      $turno = $this->biblioteca_model->consulta_turno_id($id);
      $turno_entrada = $turno->{'hora_entrada'};
      $turno_salida = $turno->{'hora_salida'};
      $turnos = $this->biblioteca_model->verificar_conflictos_turnos($id_auxiliar, $turno_entrada, $turno_salida);
      $data = [
        'id_turno' => $id,
        'id_auxiliar' => $id_auxiliar,
        'id_usuario_registra' => $persona
      ];
      if (empty($ver)) {
        if (empty($turnos)) {
          $add = $this->biblioteca_model->guardar_datos($data, 'biblioteca_turnos_auxiliar');
          $resp = ['mensaje' => "Asignacion exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          if ($add != 0) {
            $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          }
        } else {
          $resp = ['mensaje' => "El auxiliar ya cuenta con un turno en el mismo rango horario.", 'tipo' => "info", 'titulo' => "Oops.!"];
        }
      } else {
        $resp = ['mensaje' => "El auxiliar ya se encuentra asignado a este turno.", 'tipo' => "info", 'titulo' => "Oops.!"];
      }
    }
    echo json_encode($resp);
  }

  public function listar_turnos_bib()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $resp = array();
      $btn_administrar = '<span class="fa fa-cog btn btn-default administrar" title="Administrar" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
      $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o btn btn-default eliminar" style="color:#d9534f"></span>';
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $turnos = $administra ? $this->biblioteca_model->listar_turnos_bib() : array();
      foreach ($turnos as $row) {
        $row['accion'] = "$btn_administrar $btn_eliminar";
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function asignar_proceso_persona()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $persona = $_SESSION["persona"];
      $id_auxiliar = $this->input->post('id');
      $id_proceso = $this->input->post('id_aux');
      $ver = $this->biblioteca_model->obtener_asigacion_aux($id_auxiliar, $id_proceso, false);
      $data = [
        'id_tipo_sol' => $id_proceso,
        'id_auxiliar' => $id_auxiliar,
        'id_usuario_registra' => $persona
      ];
      if (empty($ver)) {
        $add = $this->biblioteca_model->guardar_datos($data, 'biblioteca_procesos_personas');
        $resp = ['mensaje' => "Asignacion exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        if ($add != 0) {
          $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      } else {
        $resp = ['mensaje' => "El auxiliar ya se encuentra asignado a este proceso.", 'tipo' => "info", 'titulo' => "Oops.!"];
      }
    }
    echo json_encode($resp);
  }

  public function asignar_estado_proceso()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $persona = $_SESSION["persona"];
      $id_proceso_persona = $this->input->post('id');
      $id_estado = $this->input->post('id_aux');
      $id_auxiliar = $this->input->post('id_auxiliar');
      $id_tipo_sol = $this->input->post('tipo_sol');
      $ver = $this->biblioteca_model->obtener_asigacion_aux($id_auxiliar, $id_tipo_sol, $id_estado);
      $data = [
        'id_procesos_persona' => $id_proceso_persona,
        'id_estado' => $id_estado,
        'id_usuario_registro' => $persona
      ];
      if (empty($ver)) {
        $add = $this->biblioteca_model->guardar_datos($data, 'biblioteca_estados_procesos');
        $resp = ['mensaje' => "Asignacion exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        if ($add != 0) {
          $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      } else {
        $resp = ['mensaje' => "El auxiliar ya se encuentra asignado a este estado.", 'tipo' => "info", 'titulo' => "Oops.!"];
      }
    }
    echo json_encode($resp);
  }

  public function retirar_procesos_persona()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $id = $this->input->post('tipo');
      $ver = $this->biblioteca_model->exist($id, 'biblioteca_procesos_personas');
      if (empty($ver)) {
        $resp = ['mensaje' => "El proceso ya le fue retirado al auxiliar.", 'tipo' => "info", 'titulo' => "Oops.!"];
      } else {
        $ret = $this->biblioteca_model->eliminar_registro($id, 'biblioteca_procesos_personas');
        $resp = ['mensaje' => "Retiro exitoso.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        if ($ret != 0) {
          $resp = ['mensaje' => "Error al retirar persona, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function retirar_estado_proceso()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $id = $this->input->post('tipo');
      $ver = $this->biblioteca_model->exist($id, 'biblioteca_estados_procesos');
      if (empty($ver)) {
        $resp = ['mensaje' => "El estado ya le fue retirado al auxiliar.", 'tipo' => "info", 'titulo' => "Oops.!"];
      } else {
        $ret = $this->biblioteca_model->eliminar_registro($id, 'biblioteca_estados_procesos');
        $resp = ['mensaje' => "Retiro exitoso.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        if ($ret != 0) {
          $resp = ['mensaje' => "Error al retirar persona, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function retirar_turno()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $id = $this->input->post('tipo');
      $ver = $this->biblioteca_model->exist($id, 'biblioteca_turnos_auxiliar');
      if (empty($ver)) {
        $resp = ['mensaje' => "El turno ya le fue retirado al auxiliar.", 'tipo' => "info", 'titulo' => "Oops.!"];
      } else {
        $ret = $this->biblioteca_model->eliminar_registro($id, 'biblioteca_turnos_auxiliar');
        $resp = ['mensaje' => "Retiro exitoso.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        if ($ret != 0) {
          $resp = ['mensaje' => "Error al retirar del turno, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function eliminar_turno()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Bib" ? true : false;
      $id = $this->input->post('id');
      $ver = $this->biblioteca_model->exist($id, 'biblioteca_turnos');
      if (empty($ver)) {
        $resp = ['mensaje' => "El turno ya le fue eliminado.", 'tipo' => "info", 'titulo' => "Oops.!"];
      } else {
        $ret = $this->biblioteca_model->eliminar_registro($id, 'biblioteca_turnos');
        $resp = ['mensaje' => "Turno eliminado correctamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        if ($ret != 0) {
          $resp = ['mensaje' => "Error al eliminar el turno, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function obtener_causas()
  {
    $buscar = $this->input->post('buscar');
    $causas = $this->Super_estado == true ? $this->biblioteca_model->obtener_causas($buscar) : array();
    $resp = [];
    foreach ($causas as $row) {
      $row['agregado'] = 0;
      array_push($resp, $row);
    }
    echo json_encode($resp);
  }

  public function obtener_recursos()
  {
    $buscar = $this->input->post('buscar');
    $recursos = $this->Super_estado == true ? $this->biblioteca_model->obtener_recursos($buscar) : array();
    echo json_encode($recursos);
  }

  public function obtener_bloque()
  {
    $buscar = $this->input->post('buscar');
    $bloques = $this->Super_estado == true ? $this->biblioteca_model->obtener_bloque($buscar) : array();
    echo json_encode($bloques);
  }

  public function obtener_bloque_cap()
  {
    $buscar = $this->input->post('buscar');
    $bloques = $this->Super_estado == true ? $this->biblioteca_model->obtener_bloque_cap($buscar) : array();
    echo json_encode($bloques);
  }

  public function obtener_programas()
  {
    $buscar = $this->input->post('buscar');
    $programas = $this->biblioteca_model->obtener_programas($buscar);
    echo json_encode($programas);
  }

  public function obtener_auxiliares()
  {
    $buscar = $this->input->post('buscar');
    $auxiliares = $this->biblioteca_model->obtener_auxiliares($buscar);
    echo json_encode($auxiliares);
  }

  public function obtener_acciones()
  {
    $buscar = $this->input->post('buscar');
    $acciones = $this->biblioteca_model->obtener_acciones($buscar);
    echo json_encode($acciones);
  }

  public function carga_empleado()
  {
    $id_empleado = $this->input->post('id');
    $tipo = $this->input->post('tipo');
    $fecha_inicio = $this->input->post('fecha_inicio');
    $fecha_fin = $this->input->post('fecha_fin');
    $estado = $this->input->post('estado');
    $sw = $this->input->post('sw');
    if ($sw == 1) {
      $resp = $this->Super_estado ? $this->biblioteca_model->carga_empleado($id_empleado, $tipo) : array();
    } else if ($sw == 2) {
      if ($_SESSION['perfil'] == 'Per_Aux_Bib') {
        $resp = $this->Super_estado ? $this->biblioteca_model->carga_empleado($_SESSION['persona'], $tipo, $fecha_inicio, $fecha_fin, $estado) : array();
      } else {
        $resp = $this->Super_estado ? $this->biblioteca_model->carga_empleado($id_empleado, $tipo, $fecha_inicio, $fecha_fin, $estado) : array();
      }
    } else {
      $resp = $this->Super_estado ? $this->biblioteca_model->carga_empleado($id_empleado, $tipo, $fecha_inicio, $fecha_fin, $estado) : array();
    }
    echo json_encode($resp);
  }

  public function obtener_carga_empleado($id_empleado, $tipo = 'sol', $fecha_inicio = '', $fecha_fin = '', $estado)
  {
    $resp = $this->Super_estado ? $this->biblioteca_model->carga_empleado($id_empleado, $tipo, $fecha_inicio, $fecha_fin, $estado) : array();
    return ($resp);
  }

  public function ubicacion_actual()
  {
    $id = $this->input->post('id');
    $ubicacion = $this->biblioteca_model->obtener_ubicacion($id);
    echo json_encode($ubicacion);
  }

  public function modificar_ubicacion()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $id_bloque = $this->input->post('bloque_cap');
      $id_salon = $this->input->post('salon_cap');
      $recursos = $this->input->post('recursos');
      $solicitud = $this->biblioteca_model->consulta_solicitud_id($id);
      $estado_solicitud = $solicitud->{'id_estado_solicitud'};
      $administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Adm_Bib' || $_SESSION['perfil'] == 'Per_Adm_Bib' ? true : false;
      $ver_num = $this->verificar_campos_numericos(['Bloque' => $id_bloque, 'Salon' => $id_salon]);
      $ver_str = $this->verificar_campos_string(['Recursos' => $recursos]);
      $sw = true;
      if (is_array($ver_num)) {
        $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser seleccionado o calificado.", 'tipo' => "info", 'titulo' => "Oops.!"];
        $sw = false;
      } else if (is_array($ver_str)) {
        $resp = ['mensaje' => "El campo " . $ver_num['field'] . " debe ser seleccionado o calificado.", 'tipo' => "info", 'titulo' => "Oops.!"];
        $sw = false;
      } else {
        $data = [
          'id_solicitud' => $id,
          'id_estado' => $estado_solicitud,
          'id_bloque' => $id_bloque,
          'id_salon' => $id_salon,
          'id_usuario_registro' => $_SESSION['persona']
        ];
        $data_sol = [
          'recurso' => $recursos
        ];
        $add = $administra && $sw ? $this->biblioteca_model->guardar_datos($data, 'biblioteca_estado_solicitud') : 1;
        $mod = $administra && $sw ? $this->biblioteca_model->modificar_datos($data_sol, 'biblioteca_solicitudes', $id) : 1;
        $resp = ['mensaje' => "Cambios guardados correctamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        if ($add != 0) {
          $resp = ['mensaje' => "Error al guardar cambios, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops..!"];
        } else if ($mod != 0) {
          $resp = ['mensaje' => "Error al guardar cambios, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops..!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function obtener_bloque_salon()
  {
    $id = $this->input->post("id");
    $programas = $this->Super_estado == true ? $this->biblioteca_model->obtener_bloque_salon($id) : array();
    echo json_encode($programas);
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

  public function validar_fechas($fecha_prestamo, $format = 'Y-m-d H:i:s')
  {
    $fecha_actual = date($format);
    $dias = $this->genericas_model->obtener_valores_parametro_aux("Dias_LB", 20)[0]["valor"];
    $fecha_inicio_valida = date($format, strtotime($fecha_actual . " + " . $dias . " days"));
    $sw = $fecha_prestamo < $fecha_inicio_valida ? false : true;
    return [$sw, $dias];
  }

  public function guardar_auxiliares()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $admin  = $_SESSION["perfil"] == "Per_Admin" ?  true : false;
        $id_auxiliar = $this->input->post("id");
        $carga = $this->input->post("accion");
        $id_solicitud = $this->input->post("id_solicitud");
        $gestion = $this->input->post("gestion");
        $persona  = $_SESSION["persona"];
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud->{'id_estado_solicitud'};
        $tipo_solicitud = $solicitud->{'id_tipo_solicitud'};
        $asignaciones = $this->biblioteca_model->obtener_asigacion_aux($persona, $tipo_solicitud, $estado_actual);
        $permiso = empty($asignaciones) ? false : true;
        $month = date('m');
        $turnos = $this->verificar_turnos($id_auxiliar, $id_solicitud, $carga);
        $ver_bal = $this->balanceo_cargas($id_auxiliar, $month, $id_solicitud, $tipo_solicitud);
        $existe = $this->biblioteca_model->auxiliar_solicitud($id_auxiliar, $id_solicitud, $carga);
        $str = $this->verificar_campos_string(['Acción' => $carga]);
        if (is_array($str)) {
          $resp = ['mensaje' => "El campo " . $str['field'] . " debe ser seleccionado.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else if ($estado_actual != 'Bib_Sol_E' && $estado_actual != 'Bib_Rev_E') {
          $resp = ['mensaje' => "No puede realizar esta acción debido a que la solicitud se encuentra en tramite o ya fue finalizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          if ($ver_bal['validate'] == false) {
            $resp = ['mensaje' => "El auxiliar cuenta con carga suficiente", 'tipo' => "info", 'titulo' => "Oops.!"];
          } else if ($carga == "") {
            $resp = ['mensaje' => "Seleccione la carga del auxiliar.", 'tipo' => "info", 'titulo' => "Oops..!"];
          } else if ($existe) {
            $resp = ['mensaje' => "El auxiliar ya cuenta con suficientes asignaciones en esta solicitud.", 'tipo' => "info", 'titulo' => "Oops..!"];
          } else if (empty($turnos)) {
            $resp = ['mensaje' => "El auxiliar no se encuentra disponible en el horario de la solicitud.", 'tipo' => "info", 'titulo' => "Oops..!"];
          } else {
            $data = [
              'id_auxiliar' => $id_auxiliar,
              'id_solicitud' => $id_solicitud,
              'accion' => $carga,
              'id_usuario_registro' => $persona
            ];
            $add = $admin || $permiso ? $this->biblioteca_model->guardar_datos($data, 'biblioteca_auxiliar') : 1;
            $resp = ['mensaje' => "Auxiliar agregado correctamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            if ($add != 0) {
              $resp = ['mensaje' => "Error al guardar auxiliar, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops..!"];
            } else {
              $aux = $this->biblioteca_model->traer_ultimo_aux($persona);
              $data_log = [
                'id_asignacion' => $aux->{'id'},
                'id_auxiliar' => $id_auxiliar,
                'id_solicitud' => $id_solicitud,
                'accion' => $carga,
                'id_usuario_registro' => $persona,
                'observacion' => 'Asignado'
              ];
              $log = $admin || $permiso ? $this->biblioteca_model->guardar_datos($data_log, "biblioteca_historial_auxiliares") : false;
            };
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function verificar_turnos($id_auxiliar, $id_solicitud, $carga)
  {
    if ($carga == 'Acc_Ent') {
      $ver = $this->biblioteca_model->verificar_turno_ent($id_auxiliar, $id_solicitud);
    } else if ($carga == 'Acc_Ret') {
      $ver = $this->biblioteca_model->verificar_turno_ret($id_auxiliar, $id_solicitud);
    } else if ($carga == 'Acc_Cap') {
      $ver = $this->biblioteca_model->verificar_turno_cap($id_auxiliar, $id_solicitud);
    }
    return $ver;
  }

  public function modificar_auxiliares()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Adm_Bib' ? true : false;
        $id = $this->input->post("id");
        $id_auxiliar = $this->input->post("new_id_aux");
        $carga = $this->input->post("accion");
        $id_solicitud = $this->input->post("id_solicitud");
        $nota = $this->input->post("observacion");
        $persona  = $_SESSION["persona"];
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud->{'id_estado_solicitud'};
        $tipo_solicitud = $solicitud->{'id_tipo_solicitud'};
        $month = date('m');
        $ver_bal = $this->balanceo_cargas($id_auxiliar, $month, $id_solicitud, $tipo_solicitud);
        if ($estado_actual != 'Bib_Sol_E' && $estado_actual != 'Bib_Rev_E') {
          $resp = ['mensaje' => "No puede realizar esta acción debido a que la solicitud se encuentra en tramite o ya fue finalizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          if ($ver_bal['validate'] == false) {
            $resp = ['mensaje' => "El auxiliar cuenta con carga suficiente", 'tipo' => "info", 'titulo' => "Oops.!"];
          } else if ($carga == "") {
            $resp = ['mensaje' => "Seleccione la carga del auxiliar.", 'tipo' => "info", 'titulo' => "Oops..!"];
          } else {
            $data = [
              'id_auxiliar' => $id_auxiliar,
              'accion' => $carga
            ];
            $mod = $this->biblioteca_model->modificar_datos($data, 'biblioteca_auxiliar', $id);
            $resp = ['mensaje' => "Auxiliar agregado correctamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            if ($mod != 0) {
              $resp = ['mensaje' => "Error al guardar auxiliar, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops..!"];
            } else {
              $aux = $this->biblioteca_model->consulta_aux_id($id);
              $data_log = [
                'id_asignacion' => $aux->{'id'},
                'id_auxiliar' => $id_auxiliar,
                'id_solicitud' => $id_solicitud,
                'accion' => $carga,
                'id_usuario_registro' => $persona,
                'observacion' => $nota ? $nota : NULL
              ];
              $log = $administra ? $this->biblioteca_model->guardar_datos($data_log, "biblioteca_historial_auxiliares") : false;
            };
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function retirar_auxiliar()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Adm_Bib' ? true : false;
        $id = $this->input->post('id');
        $id_solicitud = $this->input->post('id_solicitud');
        $nota = $this->input->post('message');
        $persona = $_SESSION['persona'];
        $solicitud = $this->biblioteca_model->consulta_solicitud_id($id_solicitud);
        $estado_actual = $solicitud->{'id_estado_solicitud'};
        if ($estado_actual != 'Bib_Sol_E' && $estado_actual != 'Bib_Rev_E') {
          $resp = ['mensaje' => "No puede realizar esta acción debido a que la solicitud se encuentra en tramite o ya fue finalizada.", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          $data = [
            'id_usuario_elimina' => $persona,
            'nota_retiro' => $nota ? $nota : NULL,
            'estado' => 0
          ];
          $mod = $this->biblioteca_model->modificar_datos($data, 'biblioteca_auxiliar', $id);
          $resp = ['mensaje' => "Auxiliar retirado correctamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          if ($mod != 0) {
            $resp = ['mensaje' => "Error al retirar auxiliar, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops..!"];
          } else {
            $aux = $this->biblioteca_model->consulta_aux_id($id);
            $carga = $aux->{'accion'};
            $id_auxiliar = $aux->{'id_auxiliar'};
            $data_log = [
              'id_asignacion' => $aux->{'id'},
              'id_auxiliar' => $id_auxiliar,
              'id_solicitud' => $id_solicitud,
              'accion' => $carga,
              'id_usuario_registro' => $persona,
              'observacion' => $nota ? $nota : NULL
            ];
            $log = $administra ? $this->biblioteca_model->guardar_datos($data_log, "biblioteca_historial_auxiliares") : false;
          };
        }
      }
    }
    echo json_encode($resp);
  }

  public function guardar_nuevo_turno()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Adm_Bib' ? true : false;
        $persona = $_SESSION['persona'];
        $hora_entrada = $this->input->post('hora_entrada');
        $hora_salida = $this->input->post('hora_salida');
        $data = [
          'hora_entrada' => $hora_entrada,
          'hora_salida' => $hora_salida,
          'id_usuario_registro' => $persona
        ];
        $add = $administra ? $this->biblioteca_model->guardar_datos($data, 'biblioteca_turnos') : 1;
        $resp = ['mensaje' => "Turno guardado correctamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        if ($add != 0) {
          $resp = ['mensaje' => "Error al guardar turno, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops..!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function validar_estado($id, $estado_nuevo)
  {
    $solicitud = $this->biblioteca_model->consulta_solicitud_id($id);
    $persona = $_SESSION["persona"];
    $solicitante = $solicitud->{'id_solicitante'};
    $estado_actual = $solicitud->{'id_estado_solicitud'};
    $tipo_solicitud = $solicitud->{'id_tipo_solicitud'};
    $admin = $_SESSION['perfil'] == 'Per_Admin' ? true : false;
    $asignaciones = $this->biblioteca_model->obtener_asigacion_aux($persona, $tipo_solicitud, $estado_actual);
    $auxiliares = $this->biblioteca_model->obtener_auxiliares($id);
    $permiso_auxiliar = false;
    foreach ($auxiliares as $auxiliar) {
      if ($persona == $auxiliar['id_auxiliar']) {
        $permiso_auxiliar = true;
        break;
      }
    }
    $permiso = empty($asignaciones) ? false : true;
    $resp = false;
    if ($tipo_solicitud == 'Bib_Lib') {
      if (($admin || $permiso) && $estado_actual == 'Bib_Sol_E' && ($estado_nuevo == 'Bib_Rev_E' || $estado_nuevo == 'Bib_Rec_E')) $resp = true;
      else if (($admin || $permiso || $permiso_auxiliar) && $estado_actual == 'Bib_Rev_E' && ($estado_nuevo == 'Bib_Pre_E' || $estado_nuevo == 'Bib_Rec_E')) $resp = true;
      else if (($admin || $permiso || $permiso_auxiliar) && $estado_actual == 'Bib_Pre_E' && ($estado_nuevo == 'Bib_Ent_E' || $estado_nuevo == 'Bib_Rec_E')) $resp = true;
      else if (($admin || $permiso || $permiso_auxiliar) && $estado_actual == 'Bib_Ent_E' && $estado_nuevo == 'Bib_Fin_E') $resp = true;
      else if (($solicitante == $persona || $admin) && $estado_actual == 'Bib_Sol_E' && $estado_nuevo == 'Bib_Can_E') $resp = true;
    } else if ($tipo_solicitud == 'Bib_Cap') {
      if (($admin || $permiso) && $estado_actual == 'Bib_Sol_E' && ($estado_nuevo == 'Bib_Rev_E' || $estado_nuevo == 'Bib_Rec_E')) $resp = true;
      else if (($admin || $permiso || $permiso_auxiliar) && $estado_actual == 'Bib_Rev_E' && ($estado_nuevo == 'Bib_Fin_E' || $estado_nuevo == 'Bib_Rec_E')) $resp = true;
      else if (($solicitante == $persona || $admin) && $estado_actual == 'Bib_Sol_E' && $estado_nuevo == 'Bib_Can_E') $resp = true;
    }
    return $resp;
  }

  public function consulta_solicitud_id()
  {
    $id = $this->input->post('id');
    $resp = $this->Super_estado ? $this->biblioteca_model->consulta_solicitud_id($id) : array();
    echo json_encode($resp);
  }

  public function guardar_encuesta()
  {
    $auxiliar = $this->input->post('auxiliar');
    $puntualidad = $this->input->post('puntualidad');
    $recomendacion = $this->input->post('recomendacion');
    $utilidad = $this->input->post('utilidad');
    $autorizo = $this->input->post('autorizo');
    $code = $this->input->post('codigo');
    $valido = $this->biblioteca_model->verificar_codigo_acceso($code);
    if (!empty($valido) && is_null($valido->{'realizo'})) {
      $id_estudiante = $valido->{'id'};
      $datos = [
        "puntualidad" => $puntualidad,
        "utilidad" => $utilidad,
        "auxiliar" => $auxiliar,
        "recomendacion" => $recomendacion,
        'id_estudiante' => $id_estudiante,
        "autorizo" => 'si',
      ];
      $num = $this->verificar_campos_numericos(['Puntualidad' => $puntualidad, 'Recomendacion' => $recomendacion, 'Utilidad' => $utilidad, 'Auxiliar' => $auxiliar]);
      if (is_array($num)) {
        $resp = ['mensaje' => "El campo " . $num['field'] . " debe ser seleccionado o calificado.", 'tipo' => "info", 'titulo' => "Oops.!"];
      } else {
        $add = $this->biblioteca_model->guardar_datos($datos, 'biblioteca_encuesta_libros_a_tu_clase');
        if ($add != 0) $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        else $resp = ['mensaje' => ".", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      }
    } else {
      $resp = ['mensaje' => "El codigo de acceso es invalido o la encuesta ya fue realizada.", 'tipo' => "info", 'titulo' => "Proceso Exitoso.!"];
    }
    echo json_encode($resp);
  }

  public function balanceo_cargas($id, $month, $id_solciitud, $tipo_solicitud)
  {
    $buscar = "p.estado = 1 AND bs.id = $id_solciitud";
    $resp = $this->biblioteca_model->cargas_empleados($month, $buscar);
    $limit = $this->biblioteca_model->valor_parametro_id_aux($tipo_solicitud)->{'valora'};
    $mayor = ['total' => 0];
    $candidatos = [];
    $recomendaciones = [];
    $alternativa = [];
    $mayor_men = ['total' => 0];
    $sw = false;
    foreach ($resp as $row) {
      $mayor = $row['total'] >= $mayor['total'] ? $row : $mayor;
    }
    foreach ($resp as $row) {
      if ($row['total'] + $limit <= $mayor['total']) {
        array_push($candidatos, $row);
      }
    }
    foreach ($candidatos as $row) {
      $mayor_men = $row['total'] >= $mayor_men['total'] ? $row : $mayor_men;
    }
    foreach ($resp as $row) {
      if ($row['total'] + $limit <= $mayor_men['total']) {
        array_push($recomendaciones, $row);
      } else if ($row['total'] == $mayor_men['total']) {
        array_push($alternativa, $row);
      }
    }
    if (empty($candidatos)) {
      $sw = true;
    } else if (empty($recomendaciones)) {
      foreach ($alternativa as $row) {
        $sw = $row['id'] == $id ? true : false;
        if ($sw == true) {
          break;
        }
      }
    } else {
      foreach ($recomendaciones as $row) {
        $sw = $row['id'] == $id ? true : false;
        if ($sw == true) {
          break;
        }
      }
    }
    return ["validate" => $sw, "candidatos" => empty($recomendaciones) ?  $alternativa : $recomendaciones];
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
      //$materias = $this->Super_estado ? $this->biblioteca_model->obtener_materias_por_docente($identificacion) : array();
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
    $newdata = [];
    if ($this->Super_estado == true) {
      $materia = $this->input->post('materia');
      if (!empty($materia) || $materia != "") {
        //$estudiantes = $this->Super_estado ? $this->biblioteca_model->obtener_estudiantes_por_materia($materia) : array();
        $data = $this->pages_model->obtener_estudiantes_por_materia_sicuc($materia); // obtiene los estudiantes por materia en sicuc
        if ($data) {
          foreach ($data[0] as $row) {
            $row['tabla'] = "visitantes";
            $row['codigo'] = $materia;
            array_push($newdata, $row);
          }
        }
        $info = $data[1]; // identificacion de los estudiantes obtenidos de sicuc
        $estudiantes = $this->pages_model->obtener_id_estudiantes($info); // consulta los visitantes para obtener el id de cada uno
      }
    }
    echo json_encode($estudiantes);
  }

  public function almacenar_observacion()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id');
        $observacion = $this->input->post('message');
        $data = [
          'observacion' => $observacion ? $observacion : NULL
        ];
        $mod = $this->biblioteca_model->modificar_datos($data, "biblioteca_estudiante_sol", $id);
        if ($mod != 0) {
          $resp = ['mensaje' => "Error al almacenar información infromación", 'tipo' => "erro", 'titulo' => "Oops.!"];
        } else {
          $resp = ['mensaje' => "Observación guardada con exito.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function consolidado_encuestas()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $tipo = $this->input->post('tipo');
        $encuestas = $this->biblioteca_model->consolidado_encuestas($tipo);
        foreach ($encuestas as $row) {
          $row['roles'] = '<span style="background-color: #39B23B;color: white; width: 100%; ;" class="pointer form-control roles"><span >Abrir</span></span>';
          $row['programas'] = '<span style="background-color: #39B23B;color: white; width: 100%; ;" class="pointer form-control programas" ><span >Abrir</span></span>';
          $row['departamentos']  = '<span style="background-color: #39B23B;color: white; width: 100%; ;" class="pointer form-control departamentos" ><span >Abrir</span></span>';
          array_push($resp, $row);
        }
      }
    }
    echo json_encode($resp);
  }

  public function consolidado_roles()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $tipo = $this->input->post('tipo');
        $resp = $this->biblioteca_model->consolidado_roles($tipo);
      }
    }
    echo json_encode($resp);
  }

  public function consolidado_programas()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $tipo = $this->input->post('tipo');
        $resp = $this->biblioteca_model->consolidado_programas($tipo);
      }
    }
    echo json_encode($resp);
  }

  public function consolidado_departamentos()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $tipo = $this->input->post('tipo');
        $resp = $this->biblioteca_model->consolidado_departamentos($tipo);
      }
    }
    echo json_encode($resp);
  }
}
