<?php

class publicaciones_control extends CI_Controller
{
var $Super_estado = false;
var $Super_elimina = 0;
var $Super_modifica = 0;
var $Super_agrega = 0;
var $admin = false;
var $ruta_archivos = "archivos_adjuntos/publicaciones/";
var $ruta_evidencia = "archivos_adjuntos/bonificaciones/evidencias/";
var $ruta_modulo = "";
public function __construct()
{
  parent::__construct();
  include('application/libraries/festivos_colombia.php');
  $this->load->model('publicaciones_model');
  $this->load->model('genericas_model');
  $this->load->model('pages_model');
  session_start();
  if (isset($_SESSION["usuario"])) {
    $this->Super_estado = true;
    $this->Super_elimina = 1;
    $this->Super_modifica = 1;
    $this->Super_agrega = 1;
    $_SESSION['perfil'] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Pub" ? $this->admin = true : $this->admin = false;
  }
}

public function index($pages = 'publicaciones', $id = '')
{
  $ruta_modulo = $pages;
  if ($this->Super_estado) {
    $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $pages);
    if (!empty($datos_actividad)) {
      //$pages = "publicaciones";
      $data['js'] = "Publicaciones";
      $data['id'] = $id;
      $data['tipo_modulo'] = $ruta_modulo;
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

public function obtener_indicadores()
{
  $parametro = $this->input->post('buscar');
  $indicadores = $this->Super_estado == true ? $this->publicaciones_model->obtener_indicadores($parametro) : array();
  echo json_encode($indicadores);
}

public function obtener_ranking()
{
  $parametro = $this->input->post('buscar');
  $ranking = $this->Super_estado == true ? $this->publicaciones_model->obtener_ranking($parametro) : array();
  echo json_encode($ranking);
}

public function obtener_estados()
{
  $parametro = $this->input->post('buscar');
  $estados = $this->Super_estado == true ? $this->publicaciones_model->obtener_estados($parametro) : array();
  echo json_encode($estados);
}

public function obtener_cuartiles()
{
  $cuartiles = $this->Super_estado == true ? $this->publicaciones_model->obtener_cuartiles() : array();
  echo json_encode($cuartiles);
}

public function obtener_inst_ext()
{
  $instituciones = $this->Super_estado == true ? $this->publicaciones_model->obtener_inst_ext() : array();
  echo json_encode($instituciones);
}

public function obtener_idioma()
{
  $instituciones = $this->Super_estado == true ? $this->publicaciones_model->obtener_idioma() : array();
  echo json_encode($instituciones);
}

public function buscar_autor()
{
  $personas = array();
  if ($this->Super_estado == true) {
    $dato = $this->input->post('dato');
    $tabla = $this->input->post('tabla');
    if (!empty($dato)) $personas = $this->publicaciones_model->buscar_autor($tabla, $dato);
  } else {
    $personas = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
  }
  echo json_encode($personas);
  return;
}
public function buscar_autor_bon()
{
  $personas = array();
  if ($this->Super_estado == true) {
    $dato = $this->input->post('dato');
    $tabla = $this->input->post('tabla');
    if (!empty($dato)) $personas = $this->publicaciones_model->buscar_autor_bon($tabla, $dato);
  } else {
    $personas = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
  }
  echo json_encode($personas);
  return;
}

public function guardar_publicacion()
{
  if (!$this->Super_estado == true) {
    $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
  } else {
    if ($this->Super_agrega == 0) {
      $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
    } else {
      $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Pub" ? true : false;
      $titulo = $this->input->post('titulo');
      $indicador = $this->input->post('indicador');
      $id_ranking = $this->input->post('id_ranking');
      $id_usuario_registra = $_SESSION['persona'];
      $autores = json_decode($this->input->post('autores'));
      $id_proyecto = $this->input->post('id_proyecto');
      $id_revista = $this->input->post('id_revista');
      $pub_year = $this->input->post('pub_year');
      $idiomas = json_decode($this->input->post('idiomas'));
      $pub_status = $this->input->post('pubs_status');
      $id_aux = $this->input->post('idaux');
      $tipos_adjs = json_decode($this->input->post('tipos_adjs'));
      $campo_fecha = $this->input->post('campo_fecha');
      $cuartil = $this->input->post('cuartil');
      $pub_link = $this->input->post('pub_link');
      $fecha_r = $this->input->post('fecha_r');
      $id_aux === 'Pub_Ace_Pub_E' ? $url_check = $this->validad_links($pub_link) : null;
      
      if ($campo_fecha == 'fecha_postulacion') {
        $req = 'fecha de postulación';
      } else if ($campo_fecha == 'fecha_aceptacion') {
        $req = 'fecha de aceptación';
      } else if ($campo_fecha == 'fecha_publicacion') {
        $req = 'fecha de aceptación';
      }
      
      $dataToCheck = [
        'ID del usuario' => $id_usuario_registra,
        'Titulo del Articulo' => $titulo,
        'Indicador' => $indicador,
        'Ranking' => $id_ranking,
        'Estado de publicación' => $pub_status,
        'Comite de proyecto' => $id_proyecto,
        'revista' => $id_revista,
        'Año de publicación' => $pub_year,
        'Cuartil' => $cuartil
      ];
      
      $id_aux == "Pub_Ace_Pub_E" ? $dataToCheck += ["URL/Link de la publicación" => $pub_link, $campo_fecha => $fecha_r] : null;
      $id_aux != "Pub_Red_E" ? $dataToCheck += ["Fecha de $req" => $campo_fecha] : null;
      $check = $this->verificar_campos_string($dataToCheck);
      if (is_array($check)) {
        $resp = ["mensaje" => "Verifique que el campo: " . $check['field'] . ", esté diligenciado correctamente.", "tipo" => 'error', 'titulo' => 'Oops'];
        exit(json_encode($resp));
      }
      
      if ($id_aux == "Pub_Ace_Pub_E" && $url_check == 0) {
        exit(json_encode(["mensaje" => "Verifique que el Link proporcionado, esté escrito correctamente.", "tipo" => 'error', 'titulo' => 'Oops']));
      }
      
      if ($id_aux === "Pub_Red_E") {
        $cargar = null;
      }
      
      for ($x = 0; $x < count($tipos_adjs); $x++) {
        if ($_FILES[$tipos_adjs[$x]]['name'] == "") {
          exit(json_encode(["mensaje" => "Verifique que haya adjuntado los documentos requeridos.", "tipo" => 'error', 'titulo' => 'Oops']));
        }
      }
      
      $data_idiomas = [];
      $data_autores = [];
      
      if (!$id_proyecto) $resp = ['mensaje' => "Por favor seleccione un proyecto", 'tipo' => "info", 'titulo' => "Oops..!"];
      else if (!$id_revista) $resp = ['mensaje' => "Por favor seleccione una revista", 'tipo' => "info", 'titulo' => "Oops..!"];
      if ($idiomas && count($idiomas) > 0) {
        if ($autores && count($autores) > 0) {
          
          $data = [
            'id_usuario_registra' => $id_usuario_registra,
            'titulo_articulo' => $titulo,
            'indicador' => $indicador,
            'id_ranking' => $id_ranking,
            'id_estado' => $id_aux,
            'id_comite_proyecto' => $id_proyecto,
            'id_revista' => $id_revista,
            'pub_year' => $pub_year,
            'id_cuartil_selected' => $cuartil,
          ];
          
          $id_aux == "Pub_Ace_Pub_E" ? $data += ["url_articulo" => $pub_link, "$campo_fecha" => $fecha_r] : null;
          $add = $this->publicaciones_model->guardar_datos($data, 'publicaciones_solicitudes');
          
          if ($add != 0) {
            $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          } else {
            $solicitud = $this->publicaciones_model->traer_ultima_solicitud($id_usuario_registra);
            foreach ($idiomas as $idioma) {
              array_push($data_idiomas, [
                'id_idioma' => $idioma->{'id'},
                'id_publicacion' => $solicitud->{'id'},
                'id_usuario_registra' => $id_usuario_registra,
                ]);
              }
              
              if (!empty($data_idiomas)) {
                $add2 = $this->publicaciones_model->guardar_datos($data_idiomas, 'publicaciones_idiomas', 2);
                $resp = ['mensaje' => "La publicación fue guardada de forma exitosa.", 'tipo' => "success", 'titulo' => "titulo"];
                if ($add2 != 0) {
                  $resp = ['mensaje' => "Error al guardar los idiomas, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                } else {
                  foreach ($autores as $autor) {
                    $data_autor = [
                      'id_autor' => $autor->{'id'},
                      'id_publicacion' => $solicitud->{'id'},
                      'tabla' => $autor->{'tabla'},
                      'id_usuario_registra' => $id_usuario_registra,
                      'id_grupo' => NULL,
                      'id_linea' => NULL,
                      'id_sublinea' => NULL,
                      'puntos' => 0,
                    ];
                    
                    if ($autor->{'tabla'} == 'personas') {
                      $data_autor['id_grupo'] = $autor->{'grupo'};
                      $data_autor['id_linea'] = $autor->{'linea'};
                      $data_autor['id_sublinea'] = $autor->{'sublinea'};
                    }
                    
                    array_push($data_autores, $data_autor);
                  }
                  
                  $add3 = $this->publicaciones_model->guardar_datos($data_autores, 'publicaciones_autores', 2);
                  if ($add3 != 0) {
                    $resp = ['mensaje' => "Error al guardar autores, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                  } else {
                    if ($id_aux == "Pub_Red_E") {
                      $obser = null;
                    } elseif ($id_aux == "Pub_Red_Pos_E") {
                      $obser = "En espera de visto bueno por parte del administrador y proceder al estado: Postulado.";
                    } elseif ($id_aux == "Pub_Pos_Ace_E") {
                      $obser = "En espera de visto bueno por parte del administrador y proceder al estado: Aceptado.";
                    } elseif ($id_aux == "Pub_Ace_Pub_E") {
                      $obser = "En espera de visto bueno por parte del administrador y proceder al estado: Publicado.";
                    }
                    $data_estado = [
                      'id_publicacion' => $solicitud->{'id'},
                      'id_estado' => $id_aux,
                      'id_usuario_registra' => $_SESSION['persona'],
                      'observacion' => $obser
                    ];
                    $add4 = $this->publicaciones_model->guardar_datos($data_estado, 'publicaciones_estados');
                    if ($add4 != 0) {
                      $resp = ['mensaje' => "Error al guardar la publicación apropiadamente, contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
                    }
                  }
                  for ($x = 0; $x < count($tipos_adjs); $x++) {
                    $cargar = $this->cargar_archivo($tipos_adjs[$x], "archivos_adjuntos/publicaciones/", "Pub");
                    if ($cargar[0] == -1) {
                      if ($x == 1) {
                        exit(json_encode(['mensaje' => "Archivo adjunto no valido o campo vacío,   rectifique e intente nuevamente.", 'tipo' => "error", 'titulo' => "Error al cargar archivo!"]));
                      }
                    }
                    $datos_pa = [
                      'id_usuario_registra' => $id_usuario_registra,
                      'id_publicacion' => $this->last_pub()->{'id'},
                      'nombre_real' => $_FILES[$tipos_adjs[$x]]['name'],
                      'nombre_guardado' => $cargar[1],
                      'tipo' => $tipos_adjs[$x]
                    ];
                    $add5 = $this->publicaciones_model->guardar_datos($datos_pa, 'publicaciones_adjuntos');
                    
                    if ($add5 != 0) {
                      $resp = ['mensaje' => "Error al guardar estados de publicación, contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
                    }
                  }
                }
              }
            }
          } else {
            $resp = ['mensaje' => "Debe tener por lo menos un autor", 'tipo' => "info", 'titulo' => "Oops..!"];
          }
        } else {
          $resp = ['mensaje' => "Debe seleccionar por lo menos un idioma", 'tipo' => "info", 'titulo' => "Oops..!"];
        }
      }
    }
    echo json_encode($resp);
  }
  
  public function guardar_nuevo_autor()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Pub"  ? true : false;
        $apellido = $this->input->post("apellido");
        $segundoApellido = $this->input->post("segundoapellido");
        $nombre = $this->input->post("nombre");
        $segundoNombre = $this->input->post("segundonombre");
        $nombre_completo = strtoupper("{$apellido} {$segundoApellido} {$nombre} {$segundoNombre}");
        $afiliacion = $this->input->post("afiliacion");
        $id_referencia = $this->input->post("id_referencia");
        $data_aut = [
          'valor_1' => $nombre_completo,
          'valor_2' => $afiliacion,
          'id_referencia' => $id_referencia,
          'tipo' => "autor_publicacion",
          'id_usuario_registro' => $_SESSION['persona']
        ];
        $add = $this->publicaciones_model->guardar_datos($data_aut, 'info_general');
        if ($add != 0) {
          $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        } else if (!empty($data_aut)) {
          $resp = ['mensaje' => "El autor fue agregado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }
    }
    echo json_encode($resp);
  }

  public function guardar_nuevo_autor_bon()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_agrega == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Pub"  ? true : false;
        $apellido = strtoupper($this->input->post("apellido"));
        $segundoApellido = strtoupper($this->input->post("segundoapellido"));
        $nombre = strtoupper($this->input->post("nombre"));
        $segundoNombre = strtoupper($this->input->post("segundonombre"));
        $documento = $this->input->post("txtdocumento");
        $tipo_documento = $this->input->post("tipo_documento");
        $id_afiliacion = $this->input->post("id_afiliacion");
        
        if($id_afiliacion == 'estudiante' && !$documento){
          $resp = ['mensaje' => "El campo documento es obligatorio, por favor intente nuevamente.", 'tipo' => "error", 'titulo' => "Oops.!"];
          echo json_encode($resp);
          return false;
        }else if($id_afiliacion == 'externo' && !$documento){
          $documento = $documento ? $documento : rand();
        }
        $data_aut = [
          'tipo_identificacion' => $tipo_documento,
          'identificacion' => $documento,
          'nombre' => $nombre,
          'segundo_nombre' => $segundoNombre,
          'apellido' => $apellido,
          'segundo_apellido' => $segundoApellido,
          'usuario_registra' => $_SESSION['persona']
        ];
        $existe = $this->publicaciones_model->verificar_identificacion($documento);
        if($existe){
          $resp = ['mensaje' => "Documento de identidad ya existe en sistema.", 'tipo' => "error", 'titulo' => "Oops.!"];
          echo json_encode($resp);
          return false;
        }
        if(!$existe)$add = $this->publicaciones_model->guardar_datos($data_aut, 'visitantes');
        if ($add != 0) {
          $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        } else {
          $resp = ['mensaje' => "El autor fue agregado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", "documento" => $documento];
        }
      }
    }
    echo json_encode($resp);
  }
  
  public function listar_publicaciones()
  {
    if (!$this->Super_estado) {
      $publicaciones = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $id_estado = $this->input->post('id_estado');
      $id_ranking = $this->input->post('id_ranking');
      $fecha_inicial = $this->input->post('fecha_inicial');
      $fecha_final = $this->input->post('fecha_final');
      
      $admin = $_SESSION["perfil"] == "Per_Admin" ? true : false;
      $admin_pub = $_SESSION["perfil"] == "Per_Adm_Pub" ? true : false;
      $admin_th = $_SESSION["perfil"] == "Per_Admin_Tal" ? true : false;
      $persona = $_SESSION['persona'];
      $resp = $this->Super_estado ? $this->publicaciones_model->listar_publicaciones($id, $id_estado, $id_ranking, $fecha_inicial, $fecha_final) : array();
      $publicaciones = array();
      
      $ver_redactado = '<span style="background-color: white; width: 100%; ;" class="pointer form-control pub" ><span >ver</span></span>';
      $ver_postulado = '<span style="background-color: #428bca;color: white; width: 100%; ;" class="pointer form-control pub" ><span >ver</span></span>';
      $ver_aceptado = '<span style="background-color: #5bc0de;color: white; width: 100%; ;" class="pointer form-control pub" ><span >ver</span></span>';
      $ver_rechazado = '<span style="background-color: #d9534f;color: white; width: 100%; ;" class="pointer form-control pub" ><span >ver</span></span>';
      $ver_publicado = '<span style="background-color: #5cb85c;color: white; width: 100%; ;" class="pointer form-control pub" ><span >ver</span></span>';
      $ver_pub_pag = '<span style="width: 100%; ;" class="pointer form-control btn-danger pag_pep"><span >ver</span></span>';
      $ver_Pub_Bon = '<span style="width: 100%; background-color: #ffffff;" class="pointer form-control bonificaciones"><span >ver</span></span>';
      
      
      $btn_negar = '<span title="Cerrar publicacion" data-toggle="popover" data-trigger="hover" class="fa fa-ban btn btn-default negar" style="color:#d9534f"></span>';
      $btn_postular = '<span title="Postular" data-toggle="popover" data-trigger="hover" class="fa fa-file btn btn-default postular" style="color:#2E79E5"></span>';
      $btn_aceptar = '<span title="Aceptar" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn btn-default aceptar" style="color:#5cb85c"></span>';
      $btn_rechazar = '<span title="Rechazar" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn btn-default rechazar" style="color:#d9534f"></span>';
      $btn_rechazarP = '<span title="Rechazar" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn btn-default rechazarP" style="color:#d9534f"></span>';
      $btn_publicar = '<span title="Publicar" data-toggle="popover" data-trigger="hover" class="fa fa-file btn btn-default publicar" style="color:#2E79E5"></span>';
      $btn_visto_bueno = '<span title="Dar visto bueno" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default visto_bueno" style="color:#5cb85c"></span>';
      $btn_visto_buenoP = '<span title="Dar visto bueno" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default visto_buenoP" style="color:#5cb85c"></span>';
      $btn_invalido = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

      $btn_validar = '<span title="Validar solicitud" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default revisar_bon" style="color:#5cb85c"></span>';
      $btn_firmar = '<span title="Firmar" data-toggle="popover" data-trigger="hover" class="fa fa-list-ol btn btn-default firmar_bon" style="color:#5cb85c"></span>';
      $btn_editar_sol = '<span title="Editar Solicitud" data-toggle="popover" data-trigger="hover" class="fa fa-pencil btn btn-default editar_bon" style="color:#5cb85c"></span>';
      $btn_VoBo_Gest = '<span title="Visto Bueno" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn btn-default VoBo_Gest" style="color:#5cb85c"></span>';
      $btn_VoMo_Gest = '<span title="Visto Malo" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn btn-default VoMo_Gest" style="color:red"></span>';
      $btn_VoBo_Aut = '<span title="Visto Bueno" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn btn-default VoBo_Autor" style="color:#5cb85c"></span>';
      $btn_VoMo_Aut = '<span title="Visto Malo" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn btn-default VoMo_Autor" style="color:red"></span>';
      $btn_Env_Cons_Acad = '<span title="Enviar a consejo" data-toggle="popover" data-trigger="hover" class="fa fa-users btn btn-default Env_Cons_Acad" style="color:#5cb85c"></span>';
      foreach ($resp as $row) {
        if ($row['id_tipo_solicitud'] == 'Pub_Pub') {
          if ($row['id_estado'] == 'Pub_Red_E' || $row['id_estado'] == 'Pub_Red_Pos_E' || $row['id_estado'] == 'Pub_Pos_Ace_E' || $row['id_estado'] == 'Pub_Ace_Pub_E') $row['ver'] = $ver_redactado;
          else if ($row['id_estado'] == 'Pub_Pos_E') $row['ver'] = $ver_postulado;
          else if ($row['id_estado'] == 'Pub_Ace_E') $row['ver'] = $ver_aceptado;
          else if ($row['id_estado'] == 'Pub_Rec_E') $row['ver'] = $ver_rechazado;
          else if ($row['id_estado'] == 'Pub_Pub_E') $row['ver'] = $ver_publicado;
          else if ($row['id_estado'] == 'Pub_Neg_E') $row['ver'] = $ver_rechazado;
          
          if ($row['id_estado'] == 'Pub_Red_E') $row['accion'] = $admin ? "$btn_postular $btn_visto_bueno $btn_negar" : "$btn_postular";
          else if ($row['id_estado'] == 'Pub_Pos_E') $row['accion'] = $admin ? "$btn_aceptar $btn_rechazar $btn_visto_bueno $btn_negar" : "$btn_aceptar $btn_rechazar";
          else if ($row['id_estado'] == 'Pub_Ace_E') $row['accion'] = $admin ? "$btn_publicar $btn_visto_bueno $btn_negar" : "$btn_publicar";
          else if ($row['id_estado'] == 'Pub_Rec_E') $row['accion'] = "$btn_invalido";
          else if ($row['id_estado'] == 'Pub_Neg_E') $row['accion'] = "$btn_invalido";
          else if ($row['id_estado'] == 'Pub_Pub_E') $row['accion'] = "$btn_invalido";
          else if ($row['id_estado'] == 'Pub_Red_Pos_E') $row['accion'] = $admin ? "$btn_visto_bueno $btn_negar" : "$btn_invalido";
          else if ($row['id_estado'] == 'Pub_Pos_Ace_E') $row['accion'] = $admin ? "$btn_visto_bueno $btn_negar" : "$btn_invalido";
          else if ($row['id_estado'] == 'Pub_Ace_Pub_E') $row['accion'] = $admin ? "$btn_visto_bueno $btn_negar" : "$btn_invalido";
          
          
          if ($row['id_estado'] == 'Pub_Arc_E') $row['accion'] .= $admin ? " $btn_visto_bueno" : "";
          
        } else if ($row['id_tipo_solicitud'] == 'Pub_Pag') {
          if ($row['id_estado'] == "Pub_Env_E") $admin || $admin_pub ? $row['accion'] = "$btn_negar" : $row['accion'] = "$btn_aceptar $btn_rechazar";
          else if ($row['id_estado'] == "Pub_Ace_E") $admin || $admin_pub ? $row['accion'] = "$btn_visto_buenoP $btn_negar" : $row['accion'] = "$btn_invalido";
          else if ($row['id_estado'] == "Pag_En_Tram") $admin || $admin_th ? $row['accion'] = "$btn_visto_buenoP $btn_negar" : $row['accion'] = "$btn_invalido";
          else if ($row['id_estado'] == "Pag_Vice_Check") $admin ? $row['accion'] = "$btn_visto_buenoP $btn_negar" : $row['accion'] = "$btn_invalido";
          else if ($row['id_estado'] == "Pag_Ace_Fin") $admin ? $row['accion'] = "$btn_invalido" : $row['accion'] = "$btn_invalido";
          else if ($row['id_estado'] == 'Pub_Neg_E') $row['accion'] = "$btn_invalido";
          $row['ver'] = $ver_pub_pag;
          
        }else if($row['id_tipo_solicitud'] == 'Pub_Bon'){
          if($row['id_estado'] == 'Bon_Sol_Regis')
          $admin || $admin_pub ? $row['accion'] = "$btn_firmar" : $row['accion'] = "$btn_firmar";
          else if($row['id_estado'] == 'Bon_Sol_Rev_Aprob') $admin || $admin_pub ? $row['accion'] = "$btn_validar" : $row['accion'] = "$btn_validar";
          else if($row['id_estado'] == 'Bon_Sol_Env'){
            $permisos = $this->publicaciones_model->verificar_permisos_persona($persona ,$row['id_estado']);
            $admin || $admin_pub ? $row['accion'] = "$btn_validar" : (($permisos->{'permiso'} > 0) ? $row['accion'] = "$btn_validar" : $row['accion'] = "$btn_invalido" );
          } 
          else if($row['id_estado'] == 'Bon_Sol_Creado') {
            $permisos = $this->publicaciones_model->verificar_permisos_persona($persona ,$row['id_estado']);
            $admin || $admin_pub ? $row['accion'] = "$btn_editar_sol" : (($permisos->{'permiso'} > 0) ? $row['accion'] = "$$btn_editar_sol" : $row['accion'] = "$btn_invalido" );
          }
          else if($row['id_estado'] == 'Bon_Sol_Aprob_Aux_Pub'){
            $permisos = $this->publicaciones_model->verificar_permisos_persona($persona ,$row['id_estado']);
            $admin || $admin_pub ? $row['accion'] = "$btn_validar" : (($permisos->{'permiso'} > 0) ? $row['accion'] = "$btn_validar" : $row['accion'] = "$btn_invalido" );
          } 
          else if($row['id_estado'] == 'Bon_Sol_Rev_Aprob'){
            $permisos = $this->publicaciones_model->verificar_permisos_persona($persona ,$row['id_estado']);
            $admin || $admin_pub ? $row['accion'] = "$btn_validar" : (($permisos->{'permiso'} > 0) ? $row['accion'] = "$btn_validar" : $row['accion'] = "$btn_invalido" );
          } 

          else if($row['id_estado']  == 'Bon_Sol_Aprob_Direct_Pub'){
            $permisos = $this->publicaciones_model->verificar_permisos_persona($persona ,$row['id_estado']);
            $vba = $this->publicaciones_model->obtener_vistos_buenos_aut($row['id'], $persona);
            if($vba == 0 && $row['id_usuario_registra'] == $persona ){
              $admin || $admin_pub ? $row['accion'] = "$btn_VoBo_Aut $btn_VoMo_Aut" : (($permisos->{'permiso'} > 0) ? $row['accion'] = "$btn_VoBo_Aut $btn_VoMo_Aut" : $row['accion'] = "$btn_invalido" ) ;
            }else if(($vba != 0 && $row['id_usuario_registra'] != $persona) || ($vba == 0 && $row['id_usuario_registra'] != $persona)) {
              $admin || $admin_pub ? $row['accion'] = "$btn_VoBo_Gest $btn_VoMo_Gest" : (($permisos->{'permiso'} > 0) ? $row['accion'] = "$btn_VoBo_Gest $btn_VoMo_Gest" : $row['accion'] = "$btn_invalido");
            }else if($vba != 0 ){
              $admin || $admin_pub ? $row['accion'] = $btn_invalido : $row['accion'] = $btn_invalido;
            }
          }

          else if($row['id_estado'] == 'Bon_Sol_Revi_Gestor'){
            $permisos = $this->publicaciones_model->verificar_permisos_persona($persona ,$row['id_estado']);
            $admin || $admin_pub ? $row['accion'] = $btn_Env_Cons_Acad : (($permisos->{'permiso'} > 0) ? $row['accion'] = $btn_Env_Cons_Acad : $row['accion'] = $btn_invalido);
          }
          else $row['accion'] = $btn_invalido;
          $row['ver'] = $ver_Pub_Bon;
        }
        
        array_push($publicaciones, $row);
      }
    }
    echo json_encode($publicaciones);
  }
  
  public function listar_autores_publicacion()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $publicaciones = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Pub" ? true : false;
      $autores = $this->publicaciones_model->listar_autores_publicacion($id);
      foreach ($autores as $row) {
        $row['ver'] = '<span style="background-color: white;color: black; width: 100%;" class="pointer form-control ver" ><span >ver</span></span>';
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }
  
  /*listar autores de pago paper*/
  
  public function listar_autores_pagos()
  {
    if (!$this->Super_estado) {
      $publicaciones = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Pub" ? true : false;
      $autores = $this->publicaciones_model->Listar_Autores_Pagos($id);
    }
    echo json_encode($autores);
  }
  
  public function validar_issn($issn)
  {
    $numbers = preg_replace("/[0-9]{7}[X0-9]/", "", $issn);
    return $numbers == '';
  }
  
  public function buscar_proyecto()
  {
    $proyectos = array();
    if ($this->Super_estado == true) {
      $dato = $this->input->post('dato');
      // echo json_encode($dato);
      $buscar = $dato != "" ? "(cp.nombre_proyecto LIKE '%" . $dato . "%' OR CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) LIKE '%" . $dato . "%') AND cp.id_estado_proyecto =  'Proy_Apr' AND cp.id_estado_proyecto = 'Proy_Apr'" : "cp.id = 0";
      $proyectos = $this->publicaciones_model->buscar_proyecto($buscar);
    } else {
      $proyectos = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }
    echo json_encode($proyectos);
  }
  
  public function buscar_revista()
  {
    $revistas = array();
    if ($this->Super_estado == true) {
      $dato = $this->input->post('dato');
      $buscar = $dato != "" ? "(vp.valor LIKE '%" . $dato . "%' OR vp.valorx LIKE '%" . $dato . "%')" : "vp.id = 0";
      $revistas = $this->publicaciones_model->buscar_revista($buscar);
    } else {
      $revistas = ['mensaje' => "", 'tipo' => "sin_session", ""];
    }
    echo json_encode($revistas);
  }
  
  public function buscar_afiliacion()
  {
    $afiliaciones = array();
    if ($this->Super_estado == true) {
      $dato = $this->input->post('dato');
      $buscar = "(vp.valor LIKE '%" . $dato . "%')";
      $afiliaciones = $this->publicaciones_model->buscar_afiliacion($buscar);
    } else {
      $afiliaciones = ['mensaje' => "", 'tipo' => "sin_session", ""];
    }
    echo json_encode($afiliaciones);
  }
  
  public function gestionar_publicacion()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0 || !$this->admin) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $message = $this->input->post("mensaje");
        $publicacion = $this->publicaciones_model->consulta_publicacion_id($id);
        $estado_actual = $publicacion->{'id_estado'};
        $estado_intermedio = $this->publicaciones_model->consulta_estado_intermedio($id)->{'id_estado'};
        $valido = $this->validar_estado($id, $estado_actual, $estado_intermedio);
        if ($valido['archivos']) {
          $data = [
            'id_estado' => $valido['nuevo_estado']
          ];
          
          $mod = $this->publicaciones_model->modificar_datos($data, "publicaciones_solicitudes", $id);
          
          if ($mod != 0) {
            $resp = ['mensaje' => "Error al almacenar la información, contacte con el administrador", 'tipo' => "error", "Oops.!"];
          } else {
            $data_estado = [
              'id_publicacion' => $id,
              'id_estado' => $valido['nuevo_estado'],
              'id_usuario_registra' => $_SESSION['persona'],
              'observacion' => $message
            ];
            $add = $this->publicaciones_model->guardar_datos($data_estado, "publicaciones_estados");
            if ($add != 0) {
              $resp = ['mensaje' => "Error al almacenar la información, contacte con el administrador", 'tipo' => "error", "Oops.!"];
            } else {
              $resp = ['mensaje' => "La publicación fue gestionada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'data' => $publicacion];
            }
          }
        } else {
          $resp = ['mensaje' => "Esta publicación ya fue gestionada o los archivos correspondientes no han sido cargados", 'tipo' => "info", "Oops.!"];
        }
      }
    }
    echo json_encode($resp);
  }
  
  public function corregir_publicacion()
  {
    if (!$this->Super_estado) {
      $publicaciones = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica != 1 || !$this->admin) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id");
        $message = $this->input->post("observacion");
        $publicacion = $this->publicaciones_model->consulta_publicacion_id($id);
        $publicacion->{"observacion"} = $message;
        $id_tipo_solcitud = $publicacion->{"id_tipo_solicitud"};
        $estado_intermedio = $this->publicaciones_model->consulta_estado_intermedio($id)->{'id_estado'};
        $sw = true;
        if ($estado_intermedio == 'Pub_Red_Pos_E') $estado = 'Pub_Pos_Cor_E';
        else if ($estado_intermedio == 'Pub_Pos_Ace_E') $estado = 'Pub_Ace_Cor_E';
        else if ($estado_intermedio == 'Pub_Pos_Rec_E') $estado = 'Pub_Rec_Cor_E';
        else if ($estado_intermedio == 'Pub_Ace_Pub_E') $estado = 'Pub_Pub_Cor_E';
        //Pago Paper - Coment
        else if ($id_tipo_solcitud == "Pub_Pag") {
          if ($estado_intermedio == 'Pub_Ace_E') $estado = 'Pub_Pub_Cor_E';
          
          if ($sw) {
            $mod_ps = $this->publicaciones_model->modificar_datos(["id_estado" => $estado], "publicaciones_solicitudes", $id);
            if ($mod_ps == 0) {
              $data_est = [
                'id_publicacion' => $id,
                'id_usuario_registra' => $_SESSION['persona'],
                'id_estado' => $estado,
                'observacion' => $message
              ];
              $add = $this->publicaciones_model->guardar_datos($data_est, "publicaciones_estados");
              if ($add != 0) {
                $resp = ['mensaje' => "Error al guardar informacion,  por favor contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
              } else {
                $resp = ['mensaje' => "Informacion almacenada con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'data' => $publicacion];
              }
              echo json_encode($resp);
              exit();
            }
          }
        } else {
          $resp = ['mensaje' => "Esta publicacion no cuenta con archivos cargados", 'tipo' => "info", 'titulo' => "Oops.!"];
          $sw = false;
        }
        if ($sw) {
          $data_est = [
            'id_publicacion' => $id,
            'id_usuario_registra' => $_SESSION['persona'],
            'id_estado' => $estado,
            'observacion' => $message
          ];
          $add = $this->publicaciones_model->guardar_datos($data_est, "publicaciones_estados");
          if ($add != 0) {
            $resp = ['mensaje' => "Error al guardar informacion,  por favor contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
          } else {
            $resp = ['mensaje' => "Informacion almacenada con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'data' => $publicacion];
          }
        }
      }
    }
    echo json_encode($resp);
  }
  
  public function subir_archivos()
  {
    if (!$this->Super_estado) {
      $publicaciones = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica != 1) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post("id_publicacion");
        $estado = $this->input->post("nuevo_estado");
        $sum = $this->input->post("total");
        $dist = json_decode($this->input->post("dist"));
        $fecha_postulacion = $this->input->post("fecha_postulacion");
        $fecha_aceptacion = $this->input->post("fecha_aceptacion");
        $fecha_publicacion = $this->input->post("fecha_publicacion");
        $url_articulo = $this->input->post("url_articulo");
        $pub = $this->publicaciones_model->consulta_publicacion_id($id);
        $ruta = $this->ruta_archivos;
        $data = [];
        $data_up = [];
        $data_adj = [];
        $data_est = [];
        $sw = true;
        $cambio = false;
        $cambio_estado = true;
        $campos = $this->obtener_campos($id, $estado);
        $ver = $this->verificar_info($campos);
        $archivos = $ver ? $this->validar_campos($campos) : [];
        $estado_int_actual = $this->publicaciones_model->consulta_estado_intermedio($id)->{'id_estado'};
        $ver_dist = $this->verificar_distribucion($id);
        exit(json_encode($ver));
        if (empty($archivos)) {
          $resp = ['mensaje' => "Debe cargar el archivo: {$ver['nombre']}", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          if ($estado == 'Pub_Pos_E') {
            $estado_int = "Pub_Red_Pos_E";
            if ($fecha_postulacion == '') {
              $resp = ['mensaje' => "Debe ingresar la fecha de postulación", 'tipo' => "info",  'titulo' => "Oops.!"];
              $sw = false;
            } else $data_date = ['fecha_postulacion' => $fecha_postulacion];
          } else if ($estado == 'Pub_Ace_E') {
            $estado_int = "Pub_Pos_Ace_E";
            if ($fecha_aceptacion == '') {
              $resp = ['mensaje' => "Debe ingresar la fecha de aceptación", 'tipo' => "info", 'titulo' => "Oops.!"];
              $sw = false;
            } else if ($sum != 100 && $estado_int_actual != 'Pub_Pos_Ace_E' && $estado_int_actual != 'Pub_Ace_Cor_E') {
              $resp = ['mensaje' => "La distribucion no concuerda con el 100% o los autores no ha aceptado su distribución, por favor verifique", 'tipo' => "info", 'titulo' => "Oops.!"];
              $sw = false;
            } else $data_date = ['fecha_aceptacion' => $fecha_aceptacion];
          } else if ($estado == 'Pub_Rec_E') {
            $estado_int = "Pub_Pos_Rec_E";
            if ($fecha_aceptacion == '') {
              $resp = ['mensaje' => "Debe ingresar la fecha de rechazo", 'tipo' => "info", 'titulo' => "Oops.!"];
              $sw = false;
            } else $data_date = ['fecha_aceptacion' => $fecha_aceptacion];
          } else if ($estado == 'Pub_Pub_E') {
            $estado_int = "Pub_Ace_Pub_E";
            if ($fecha_publicacion == '') {
              $resp = ['mensaje' => "Debe ingresar la fecha de publicacion", 'tipo' => "info", 'titulo' => "Oops.!"];
              $sw = false;
            } else if ($url_articulo == '') {
              $resp = ['mensaje' => "Debe ingresar la url del articulo", 'tipo' => "info", 'titulo' => "Oops.!"];
              $sw = false;
            } else $data_date = ['fecha_publicacion' => $fecha_publicacion, 'url_articulo' => $url_articulo];
          }
          if ($sw) {
            $data_estado = [
              'id_publicacion' => $id,
              'id_estado' => $estado_int,
              'id_usuario_registra' => $_SESSION['persona']
            ];
            foreach ($archivos as $row) {
              if (!$row['upload']) {
                continue;
              } else {
                $archivo = $this->cargar_archivo($row['name'], $ruta, "Pub");
                if ($archivo[0] == -1) {
                  $resp = ['mensaje' => "Error al subir el archivo: {$row['placeholder']}", 'tipo' => "info", 'titulo' => "Oops.!"];
                  $sw = false;
                  break;
                } else {
                  $cambio = true;
                  $nombre = $_FILES[$row['name']]['name'];
                  if ($row['modify']) {
                    $mod_arc = $this->actualizar_adjunto($id, $row['name']);
                    if (!$mod_arc) {
                      $resp = ['mensaje' => "Error actualizando infromación, por favor contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
                      $sw = false;
                      break;
                    }
                  }
                  $data_adj = [
                    'id_publicacion' => $id,
                    'nombre_real' => $nombre,
                    'nombre_guardado' => $archivo[1],
                    'id_usuario_registra' => $_SESSION['persona'],
                    'tipo' => $row['name']
                  ];
                  array_push($data_up, $data_adj);
                }
              }
            }
            if ($sw) {
              $date_esp = array_keys($data_date)[0];
              if ((array_values($data_date)[0] != $pub->{$date_esp}) || $url_articulo != $pub->{'url_articulo'}) {
                $mod = $this->publicaciones_model->modificar_datos($data_date, "publicaciones_solicitudes", $id);
                if ($mod != 0) {
                  $resp = ['mensaje' => "Error al guardar informacion, por favor constacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
                } else {
                  if ($estado_int == 'Pub_Pos_Ace_E') {
                    if ($ver_dist) $add = $this->publicaciones_model->guardar_datos($data_estado, "publicaciones_estados");
                    else {
                      $add = 0;
                      foreach ($dist as $row) {
                        $id_aut = $row->{'id_el'};
                        $data_dist = [
                          'puntos' => $row->{'puntos'}
                        ];
                        $mod_aut = $this->publicaciones_model->modificar_datos($data_dist, "publicaciones_autores", $id_aut);
                        if ($mod_aut != 0) $add = 1;
                      }
                    }
                  } else $add = $this->publicaciones_model->guardar_datos($data_estado, "publicaciones_estados");
                  if ($add != 0) {
                    $resp = ['mensaje' => "Error al almacenar información, contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
                    $cambio = false;
                  } else {
                    $cambio_estado = false;
                    $resp = ['mensaje' => "Informacion almacenada con exito!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'mod' => true];
                  }
                }
              } else {
                $resp = ['mensaje' => "Informacion almacenada con exito!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'mod' => false];
              }
              if ($cambio) {
                $add = $this->publicaciones_model->guardar_datos($data_up, "publicaciones_adjuntos", 2);
                if ($add != 0) {
                  $resp = ['mensaje' => "Error al guardar información de: {$row['placeholder']}", 'tipo' => "info", 'titulo' => "Oops.!"];
                } else {
                  $add = $cambio_estado ? $this->publicaciones_model->guardar_datos($data_estado, "publicaciones_estados") : 0;
                  if ($add != 0) {
                    $resp = ['mensaje' => "Error al almacenar información, contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
                  } else {
                    $resp = ['mensaje' => "Informacion almacenada con exito!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'mod' => true];
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
  
  public function cargar_archivo($mi_archivo, $ruta, $nombre)
  {
    $nombre .= uniqid();
    $tipo_archivos = $this->genericas_model->obtener_valores_parametro_aux("For_Adm", 20);
    $tipo_archivos = empty($tipo_archivos) ? "*" : $tipo_archivos[0]["valor"];
    $real_path = realpath(APPPATH . '../' . $ruta);
    $config['upload_path'] = $real_path;
    $config['file_name'] = $nombre;
    $config['allowed_types'] = $tipo_archivos;
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
  
  public function validar_estado($id, $estado_actual, $estado_intermedio = '')
  {
    if ($estado_actual == 'Pub_Env_E') $resp = true;
    else if ($estado_intermedio == 'Pub_Red_Pos_E') {
      $arc = $this->publicaciones_model->validar_archivos($id, "Pub_Pos_E");
      if (empty($arc)) $resp = ['archivos' => false];
      else $resp = ['archivos' => true, 'nuevo_estado' => "Pub_Pos_E"];
    } else if ($estado_intermedio == 'Pub_Pos_Ace_E') {
      $arc = $this->publicaciones_model->validar_archivos($id, "Pub_Ace_E");
      if (empty($arc)) $resp = ['archivos' => false];
      else $resp = ['archivos' => true, 'nuevo_estado' => 'Pub_Ace_E'];
    } else if ($estado_intermedio == 'Pub_Ace_Pub_E') {
      $arc = $this->publicaciones_model->validar_archivos($id, "Pub_Pub_E");
      if (empty($arc)) $resp = ['archivos' => false];
      else $resp = ['archivos' => true, 'nuevo_estado' => 'Pub_Pub_E'];
    } else if ($estado_intermedio == 'Pub_Pos_Rec_E') {
      $arc = $this->publicaciones_model->validar_archivos($id, "Pub_Rec_E");
      if (empty($arc)) $resp = ['archivos' => false];
      else $resp = ['archivos' => true, 'nuevo_estado' => 'Pub_Rec_E'];
    } else {
      $resp = ['archivos' => false];
    }
    return $resp;
  }
  
  public function listar_autores_distribucion()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id');
        $autores = $this->publicaciones_model->listar_autores_publicacion($id);
        
        $btn_aceptar = '<span title="Aceptar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default aceptar" style="color:#5cb85c"></span>';
        $btn_invalido = '<span title="En espera" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn" style="color:#428bca"></span>';
        $btn_aceptado = '<span title="Aceptado" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off pointer btn"></span>';
        $input_dist = '<input type="number" class="form-control puntos_dist" placeholder="Porcentaje">';
        
        foreach ($autores as $row) {
          if ($row['tabla'] != 'general') {
            if ($row['puntos'] == 0) $row['accion'] = '<input type="number" class="form-control puntos_dist" placeholder="Porcentaje" id="' . $row['id'] . '" value="' . $row['puntos'] . '">';
            else if ($row['id'] == $_SESSION['persona'] && $row['aprobo']) $row['accion'] = $btn_aceptado;
            else if (($row['id_autor'] == $_SESSION['persona'] && !$row['aprobo'] && $row['tabla'] == 'personas') || ($row['tabla'] == 'visitantes' && ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Pub"))) $row['accion'] = $btn_aceptar;
            else if ($row['aprobo'] == 1) $row['accion'] = $btn_aceptado;
            else $row['accion'] = $btn_invalido;
            
            array_push($resp, $row);
          }
        }
      }
    }
    echo json_encode($resp);
  }
  
  public function aceptar_distribucion()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!", 'ok' => false];
      } else {
        $id_autor = $this->input->post('id');
        $val = $this->publicaciones_model->validar_autor($id_autor);
        $id_publicacion = $val->{'id_publicacion'};
        $sw = $val->{'id_autor'} == $_SESSION['persona'] ? true : false;
        if ($sw) {
          $data = [
            'aprobo' => 1
          ];
          $mod = $this->publicaciones_model->modificar_datos($data, "publicaciones_autores", $id_autor);
          if ($mod != 0) {
            $resp = ['mensaje' => "Error al almacenar la información, contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!", 'ok' => false];
          } else {
            $val_d = $this->publicaciones_model->validar_distribucion_publicacion($id_publicacion);
            $ready = $val_d->{'ready'};
            $cont = $val_d->{'cont'};
            $ok = false;
            if ($ready == $cont) {
              $ok = $this->validar_distribucion($id_publicacion);
            }
            $resp = ['mensaje' => "Proceso realizado con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'ok' => $ok];
            $validar_avalP = $this->aval_docentesP($id_publicacion);
          }
        } else {
          $resp = ['mensaje' => "Esta tratando de aceptar un porcentaje que no le corresponde o ya ha aceptado su porcentaje anteriormente", 'tipo' => "info", 'titulo' => "Oops.!", 'ok' => false];
        }
      }
    }
    echo json_encode($resp);
  }
  
  /* Comprobacion estados pago papers */
  
  public function aval_docentesP($id_publicacion)
  {
    if (!$this->Super_estado) {
      echo json_encode(["mensaje" => "No tienes permisos suficientes.", "tipo" => "error", "titulo" => "Error"]);
    } else {
      $contador = 0;
      $estado_actual = $this->publicaciones_model->Validar_Estados_PublicacionesP($id_publicacion);
      $query = $this->publicaciones_model->Validar_Estados_AutoresP($id_publicacion);
      for ($x = 0; $x < count($query); $x++) {
        if ($query[$x]["aprobo"] == 0) {
          $contador++;
        }
      }
      if ($contador == 0) {
        if ($estado_actual[0]["id_estado"] == "Pub_Env_E") {
          $estado_nuevo = ["id_estado" => "Pub_Ace_E"];
          $this->publicaciones_model->modificar_datos($estado_nuevo, "publicaciones_solicitudes", $id_publicacion);
          $estado_nuevo += ["id_publicacion" => "$id_publicacion", "id_usuario_registra" => $_SESSION['persona'], "observacion" => "Los autores han aceptado los porcentajes que les corresponde."];
          $this->publicaciones_model->guardar_datos($estado_nuevo, "publicaciones_estados", 1);
        }
      } else {
        return false;
      }
    }
  }
  
  public function aval_adminP()
  {
    if (!$this->Super_estado) {
      echo json_encode(["mensaje" => "No tienes permisos suficientes.", "tipo" => "error", "titulo" => "Error"]);
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!", 'ok' => false];
      } else {
        $id_publicacion = $this->input->post('id');
        $aval_msg = $this->input->post('mensaje');
        
        if (empty($id_publicacion) || empty($aval_msg)) {
          $resp = ['mensaje' => "Debe enviar un mensaje para completar el aval.", 'tipo' => "error", 'titulo' => "Oops.!", 'ok' => false];
        } else {
          $mod_ps = $this->publicaciones_model->modificar_datos(["id_estado" => "Pag_En_Tram"], "publicaciones_solicitudes", $id_publicacion);
          
          if ($mod_ps == 0) {
            $datos = [
              "id_estado" => "Pag_En_Tram",
              "id_publicacion" => $id_publicacion,
              "id_usuario_registra" => $_SESSION['persona'],
              "observacion" => $aval_msg
            ];
            $aval_admin = $this->publicaciones_model->guardar_datos($datos, "publicaciones_estados", 1);
            if ($aval_admin == 0) {
              $resp = ['mensaje' => "El aval ha sido diligenciado correctamente!", 'tipo' => "success", 'titulo' => "Enhorabuena!", 'ok' => true];
              echo json_encode($resp);
              exit();
            }
          }
          $resp = ['mensaje' => "Hubo un error en el cambio de estado, favor contactar con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!", 'ok' => false];
          echo json_encode($resp);
        }
      }
    }
  }
  
  /* Fin de check avales pag papers */
  
  public function validar_distribucion($id)
  {
    if (!$this->Super_estado) {
      $resp = false;
    } else {
      if ($this->Super_modifica == 0) {
        $resp = false;
      } else {
        $cont = 0;
        $autores = $this->publicaciones_model->listar_autores_publicacion($id);
        $cuc = [];
        $publicacion = $this->publicaciones_model->consulta_publicacion_id($id);
        foreach ($autores as $row) {
          if ($row['tabla'] == 'personas') {
            $cont += $row['aprobo'];
            array_push($cuc, $row);
          }
        }
        $sw = count($cuc) == $cont ? true : false;
        if ($sw) {
          $val = $this->validar_estado($id, '', 'Pub_Pos_Ace_E');
          if ($val['archivos']) {
            $data_estado = [
              'id_publicacion' => $id,
              'id_estado' => 'Pub_Pos_Ace_E',
              'id_usuario_registra' => $_SESSION['persona'],
            ];
            $add = $this->publicaciones_model->guardar_datos($data_estado, "publicaciones_estados");
            if ($add != 0) {
              $resp = false;
            } else {
              $resp = true;
            }
          } else {
            $resp = false;
          }
        } else {
          $resp = false;
        }
      }
    }
    return $resp;
  }
  
  public function obtener_campos_archivos()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id');
        $estado = $this->input->post('estado_nuevo');
        $campos = $this->obtener_campos($id, $estado);
      }
    }
    echo json_encode($campos);
  }
  
  public function obtener_campos($id, $estado)
  {
    $resp = [];
    $valores = $this->publicaciones_model->verificar_archivos($id, $estado);
    $campos = $this->publicaciones_model->obtener_campos_archivos($estado);
    foreach ($campos as $row) {
      $carta = array_filter($valores, function ($valores) use (&$row) {
        return $valores['tipo'] == $row['name'];
      });
      $valor = reset($carta);
      $row['value'] = $valor['nombre_real'];
      $row['archivo'] = $valor['nombre_guardado'];
      array_push($resp, $row);
    }
    return $resp;
  }
  
  public function obtener_tipos_adjuntos()
  {
    if (!$this->Super_estado) {
      $r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $status_selected = $this->input->post('idaux');
      $tipo_archivos = [];
      empty($status_selected) || $status_selected == null ? $r = false : $r = $this->publicaciones_model->obtener_tipos_adjuntos();
      
      $url_input = '<input type="text" data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="Link de publicación" class="form-control rounded links" style="border-radius: 3px;" name="pub_link" id="pub_link" placeholder="Inserte link de publicación">';
      $fecha_post =
      '<span class="input-group-addon" style="background-color:white"><span class="fa fa-calendar red"></span> Fecha de postulación</span>
      <input type="date" name="fecha_r" id="fecha_post" class="form-control">';
      
      $fecha_acep =
      '<span class="input-group-addon" style="background-color:white"><span class="fa fa-calendar red"></span> Fecha de aceptación</span>
      <input type="date" name="fecha_r" id="fecha_acep" class="form-control">';
      
      $fecha_publ =
      '<span class="input-group-addon" style="background-color:white"><span class="fa fa-calendar red"></span> Fecha de publicación</span>
      <input type="date" name="fecha_r" id="fecha_publ" class="form-control">';
      
      if ($status_selected === "Pub_Red_Pos_E") {
        foreach ($r as $row) {
          if ($row['tipo'] === 'original_trabajo' || $row['tipo'] === 'carta_postulacion') {
            $row['fecha_request'] = $fecha_post;
            $row['campo_fecha'] = "fecha_postulacion";
            array_push($tipo_archivos, $row);
          }
        }
      } elseif ($status_selected == "Pub_Pos_Ace_E") {
        foreach ($r as $row) {
          if ($row['tipo'] === 'ultimo_trabajo' || $row['tipo'] === 'carta_aceptacion') {
            $row['fecha_request'] = $fecha_acep;
            $row['campo_fecha'] = "fecha_aceptacion";
            array_push($tipo_archivos, $row);
          }
        }
      } elseif ($status_selected == "Pub_Ace_Pub_E") {
        foreach ($r as $row) {
          if ($row['tipo'] === 'trabajo_publicado') {
            $row['fecha_request'] = $fecha_publ;
            $row['pub_link'] = $url_input;
            $row['campo_fecha'] = "fecha_publicacion";
            array_push($tipo_archivos, $row);
          }
        }
      }
    }
    echo json_encode($tipo_archivos);
  }
  
  public function listar_archivos()
  {
    $archivos = [];
    if (!$this->Super_estado) {
      $archivos = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $archivos = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id');
        $id_p = $this->input->post('id_p');
        if (empty($id) && !empty($id_p)) {
          $archivos = $this->publicaciones_model->listar_archivos_pagop($id_p);
          array_push($archivos, $this->publicaciones_model->correos_pagop($id_p));
        } else if (empty($id_p) && !empty($id)) {
          $archivos = $this->publicaciones_model->listar_archivos($id);
        }
      }
    }
    echo json_encode($archivos);
  }
  
  public function cerrar_publicacion()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0 || $this->admin==false) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id');
        $message = $this->input->post('mensaje');
        $vb = $this->input->post('vb');
        
        if (empty($vb)) { // Si esta vacio el visto bueno, quiere decir que es el cerrar publicacion normal.
          $data = [
            'id_estado' => 'Pub_Neg_E'
          ];
          $mod = $this->publicaciones_model->modificar_datos($data, "publicaciones_solicitudes", $id);
          if ($mod != 0) {
            $resp = ['mensaje' => "Error al cargar la informacion apropiadamente, contacte con el administrador", 'tipo' => 'info', 'titulo' => "Oops.!"];
          } else {
            $data_est = [
              'id_publicacion' => $id,
              'id_estado' => 'Pub_Cls_E',
              'observacion' => $message,
              'id_usuario_registra' => $_SESSION['persona']
            ];
            $add = $this->publicaciones_model->guardar_datos($data_est, "publicaciones_estados");
            if ($add != 0) {
              $resp = ['mensaje' => "Error al cargar la informacion apropiadamente, contacte con el administrador", 'tipo' => 'info', 'titulo' => "Oops.!"];
            } else {
              $resp = ['mensaje' => "Informacion almacenada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            }
          }
        } else if ($vb !== "") { //Evalua si el que va a dar el visto bueno es talento humano o vice administrativa.
          $nuevo_estado = "";
          if ($vb == "Pub_Ace_E") {
            $nuevo_estado = "Pag_En_Tram";
          } elseif ($vb == "Pag_En_Tram") {
            $nuevo_estado = "Pag_Vice_Check";
          } elseif ($vb == "Pag_Vice_Check") {
            $nuevo_estado = "Pag_Ace_Fin";
          }
          if ($vb == "Pub_Env_E") {
            $nuevo_estado = "Pub_Rec_E";
            $consul = $this->publicaciones_model->Validar_Estados_AutoresP($id);
            for ($x = 0; $x < count($consul); $x++) {
              if ($consul[$x]['id_estado'] == "Pub_Rec_E" && $_SESSION['persona'] == $consul[$x]['id_usuario_registra']) {
                echo json_encode(['mensaje' => "Ya ha enviado su motivo de rechazo previamente!", 'tipo' => 'info', 'titulo' => "Oops.!"]);
                exit();
              }
            }
            $mod = 0;
          } else {
            $data = [
              'id_estado' => "$nuevo_estado"
            ];
            $mod = $this->publicaciones_model->modificar_datos($data, "publicaciones_solicitudes", $id);
          }
          
          if ($mod != 0) {
            $resp = ['mensaje' => "Error al cargar la informacion apropiadamente, contacte con el administrador", 'tipo' => 'info', 'titulo' => "Oops.!"];
          } else {
            $data_est = [
              'id_publicacion' => $id,
              'id_estado' => "$nuevo_estado",
              'observacion' => $message,
              'id_usuario_registra' => $_SESSION['persona']
            ];
            $add = $this->publicaciones_model->guardar_datos($data_est, "publicaciones_estados");
            if ($add != 0) {
              $resp = ['mensaje' => "Error al cargar la informacion apropiadamente, contacte con el administrador", 'tipo' => 'info', 'titulo' => "Oops.!"];
            } else {
              $resp = ['mensaje' => "Informacion almacenada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            }
          }
        }
      }
    }
    echo json_encode($resp);
  }
  
  public function listar_estados()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id');
        $resp = $this->publicaciones_model->listar_estados($id);
      }
    }
    echo json_encode($resp);
  }
  
  public function verificar_info($campos)
  {
    $resp = ['validate' => true];
    foreach ($campos as $row) {
      if ($row['value'] == '' && $_FILES[$row['name']]['size'] == 0) {
        $resp = ['validate' => false, 'nombre' => $row['placeholder']];
        break;
      }
    }
    return $resp;
  }
  
  public function validar_campos($campos)
  {
    $resp = [];
    foreach ($campos as $row) {
      if ($_FILES[$row['name']]['size'] == 0 && $row['value'] != '') {
        $row['upload'] = false;
        $row['modify'] = false;
      } else if ($_FILES[$row['name']]['size'] != 0 && $row['value'] == '') {
        $row['upload'] = true;
        $row['modify'] = false;
      } else {
        $row['upload'] = true;
        $row['modify'] = true;
      }
      array_push($resp, $row);
    }
    return $resp;
  }
  
  public function actualizar_adjunto($id, $campo)
  {
    $arc = $this->publicaciones_model->obtener_archivo_esp($id, $campo);
    $id_arc = $arc->{'id'};
    $mod = $this->publicaciones_model->modificar_datos(['estado' => 0], "publicaciones_adjuntos", $id_arc);
    if ($mod != 0) return false;
    else return true;
  }
  
  public function obtener_publicaciones_pendientes()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = '';
        if ($_SESSION["perfil"] == "Per_Admin") {
          $where = "pe.id_estado IN ('Pub_Red_Pos_E', 'Pub_Pos_Ace_E', 'Pub_Pos_Rec_E', 'Pub_Ace_Pub_E', 'Pub_Ace_E', 'Pag_En_Tram', 'Pag_Vice_Check')"; //GESTIONAR PERMISOS AQUI
        } elseif ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Pub") {
          $where = "pe.id_estado IN ('Pub_Ace_E')";
        } elseif ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Admin_Tal") {
          $where = "pe.id_estado IN ('Pag_En_Tram')";
        } else $where = "ps.id = 0";
        $resp = $this->publicaciones_model->obtener_publicaciones_pendientes($where, $id);
      }
    }
    echo json_encode($resp);
  }
  
  public function informacion_publicacion()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id_pub_global');
        $resp = $this->publicaciones_model->informacion_publicacion($id);
      }
    }
    echo json_encode($resp);
  }
  
  public function modificar_publicacion()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      if ($this->Super_modifica == 0) {
        $resp = ['mensaje' => "No tiene Permisos Para Realizar esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $id = $this->input->post('id_publicacion');
        $titulo_articulo = $this->input->post('titulo');
        $id_proyecto = $this->input->post('id_proyecto');
        $id_revista = $this->input->post('revista_id');
        $id_ranking = $this->input->post('id_ranking');
        $indicador = $this->input->post('indicador');
        $pub = $this->publicaciones_model->consulta_publicacion_id($id);
        $estado = $pub->{'id_estado'};
        $str = $this->verificar_campos_string(['Titulo' => $titulo_articulo, 'Indicador' => $indicador]);
        $num = $this->verificar_campos_numericos(['Proyecto' => $id_proyecto, 'Ranking' => $id_ranking, 'Revista' => $id_revista]);
        if ($estado != 'Pub_Red_E') {
          $resp = ['mensaje' => "No se puede realizar esta acción debido a que la publicación se encuentra gestion o ya fue finalizada", 'tipo' => "info", 'titulo' => "Oops.!"];
        } else {
          if (is_array($str)) {
            $resp = ['mensaje' => "Debe diligenciar el campo {$str['field']}", 'tipo' => "info", 'titulo' => "Oops..!"];
          } else if (is_array($num)) {
            $resp = ['mensaje' => "Debe diligenciar el campo {$num['field']}", 'tipo' => "info", 'titulo' => "Oops..!"];
          } else {
            $data = [
              'titulo_articulo' => $titulo_articulo,
              'id_comite_proyecto' => $id_proyecto,
              'id_revista' => $id_revista,
              'id_ranking' => $id_ranking,
              'indicador' => $indicador
            ];
            $mod = $this->publicaciones_model->modificar_datos($data, "publicaciones_solicitudes", $id);
            if ($mod != 0) {
              $resp = ['mensaje' => "Error al guardar información, por favor contactar con el administrador", 'tipo' => "info", 'titulo' => "Oops..!"];
            } else {
              $resp = ['mensaje' => "Información almacenada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
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
      if (empty($row) || ctype_space($row) || !is_numeric($row)) {
        return ['type' => -1, 'field' => array_search($row, $array, true)];
      }
    }
    return 1;
  }
  
  public function verificar_campos_string($array)
  {
    foreach ($array as $row) {
      if (empty($row) || ctype_space($row) || $row == "undefined") {
        return ['type' => -2, 'field' => array_search($row, $array, true)];
      }
    }
    return 1;
  }
  
  public function listar_idiomas()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $dato = $this->input->post('dato');
      $idiomas = $this->input->post('nuevos_idiomas');
      $btn_agregar = '<span title="Agregar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default agregar" style="color:#2E79E5"></span>';
      $btn_retirar = '<span title="Quitar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
      
      $languages = $this->publicaciones_model->obtener_idiomas($dato);
      foreach ($languages as $row) {
        $sw = false;
        if ($idiomas) {
          foreach ($idiomas as $key) {
            if ($key['id'] == $row['id']) {
              $sw = true;
              break;
            }
          }
        }
        if ($sw) $row['accion'] = $btn_retirar;
        else $row['accion'] = $btn_agregar;
        
        array_push($resp, $row);
      }
    }
    
    echo json_encode($resp);
  }

  public function listar_idiomas_bon()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $dato = $this->input->post('dato');
      $btn_agregar = '<span title="Agregar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default agregar" style="color:#2E79E5"></span>';
      
      $languages = $this->publicaciones_model->obtener_idiomas($dato);
      foreach ($languages as $row) {
        $row['accion'] = $btn_agregar;
        
        array_push($resp, $row);
      }
    }
    
    echo json_encode($resp);
  }
  
  public function obtener_grupos()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $resp = $this->publicaciones_model->obtener_valor_parametro(89);
    }
    echo json_encode($resp);
  }
  
  public function obtener_lineas()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $resp = $this->publicaciones_model->obtener_valor_parametro(87);
    }
    echo json_encode($resp);
  }
  
  public function obtener_sublineas()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $resp = $this->publicaciones_model->obtener_valor_parametro(88);
    }
    echo json_encode($resp);
  }
  
  public function verificar_distribucion($id)
  {
    $autores = $this->publicaciones_model->autores_unicosta($id);
    $sw = true;
    $total = 0;
    foreach ($autores as $row) {
      if ($row['aprobo'] == 0) $sw = false;
      $total += $row['puntos'];
    }
    return $total == 0 ? false : $sw;
  }
  
  public function informacion_autor()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $resp = $this->publicaciones_model->informacion_autor($id);
    }
    echo json_encode($resp);
  }
  
  public function almacenar_revista()
  {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id_revista = $this->input->post('revista');
      $nombre = $this->input->post('nombre_revista');
      $issn = $this->input->post('issn');
      $isbn = $this->input->post('isbn');
      $cuartil = $this->input->post('cuartil');
      
      $data = [
        'idparametro' => 287,
        'valor' => $nombre ? $nombre : NULL,
        'valorx' => $issn ? $issn : NULL,
        'valorz' => $isbn ? $isbn : NULL,
        'valory' => $cuartil ? $cuartil : NULL,
        'usuario_registra' => $_SESSION['persona']
      ];
      
      $alm = $id_revista ? $this->publicaciones_model->modificar_datos($data, 'valor_parametro', $id_revista) : $this->publicaciones_model->guardar_datos($data, 'valor_parametro');
      
      
      if ($alm != 0) {
        $resp = ['mensaje' => "Error al guardar información, por favor contactar con el administrador", 'tipo' => "info", 'titulo' => "Oops..!"];
      } else {
        $revista = $id_revista ? $this->publicaciones_model->obtener_revista_id($id_revista) : $this->publicaciones_model->traer_ultima_revista();
        $resp = ['mensaje' => "Información almacenada con exito!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'revista_info' => $revista];
      }
      echo json_encode($resp);
    }
  }
  
  //Pago papers
  
  public function buscar_articulos()
  {
    $articulos = array();
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $titulo_articulo = $this->input->post('dato_buscar');
      $articulos = $this->publicaciones_model->Buscar_Articulos($titulo_articulo);
      echo json_encode($articulos);
    }
  }
  
  public function buscar_cuartil()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $query = $this->publicaciones_model->Buscar_Cuartil();
      echo json_encode($query);
    }
  }
  
  public function buscar_codsap()
  {
    $resul_sap = array();
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $codsap = $this->input->post('dato_buscar');
      $resul_sap = $this->publicaciones_model->Buscar_CodSap($codsap);
      echo json_encode($resul_sap);
    }
  }
  
  public function buscar_autores()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $articulo = $this->input->post('dato_buscar');
      $resul_autores = $this->publicaciones_model->Buscar_Autores($articulo);
      echo json_encode($resul_autores);
    }
  }
  
  public function solicitud_pago_papers()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      
      $campos_array = [];
      
      $nombre_articulo = $this->input->post("nombre_articulo");
      $fecha_max_pago = $this->input->post('fecha_maxima_pago');
      $rev_o_conf = $this->input->post("tipo_id");
      $id_cuartil_selected = $this->input->post("cuartil_id");
      $id_revista_selected = $this->input->post("nombre_revista");
      $id_codsap_selected = $this->input->post("codsap_id");
      $valor_pago = $this->input->post("pago_valor");
      $pago_selected = $this->input->post("tipo_pago_select");
      $banco_selected = $this->input->post("banco_select");
      $tipo_cuenta = $this->input->post("tipo_tarjeta_select");
      $moneda_selected = $this->input->post("moneda_id");
      $link_pago = $this->input->post("pago_link");
      $num_indenti_articulo = $this->input->post("num_identi_articulo");
      $id_articulo = $this->input->post("art_id");
      $id_user_registra = $_SESSION['persona'];
      $estados = $this->publicaciones_model->obtener_estados(285);
      for ($x = 0; $x < count($estados); $x++) {
        if ($estados[$x]["valor"] == "Enviado") {
          $estado = $estados[$x]["id"];
        }
      }
      
      array_push(
        $campos_array,
        $nombre_articulo,
        $fecha_max_pago,
        $rev_o_conf,
        $id_cuartil_selected,
        $id_revista_selected,
        $id_codsap_selected,
        $valor_pago,
        $pago_selected,
        $banco_selected,
        $tipo_cuenta,
        $moneda_selected,
        $link_pago,
        $id_articulo,
        $num_indenti_articulo,
        $id_user_registra,
        $estado
      );
      
      //CORREGIR con el verificar campos string
      for ($x = 0; $x < count($campos_array); $x++) {
        if ($campos_array[$x] == 219564) { //Pago nacional
          if (empty($banco_selected) || empty($tipo_cuenta) || empty($moneda_selected) || empty($valor_pago) || empty($num_indenti_articulo)) {
            echo json_encode(["estado" => "404", "mensaje" => "No se llenaron los campos suficientes segun el pago nacional", "tipo" => "warning", "titulo" => "Aviso"]);
            exit();
          }
        } else if ($campos_array[$x] == 219565) { //Pago mediante link
          if (empty($moneda_selected) || empty($valor_pago) || empty($link_pago)) {
            echo json_encode(["estado" => "404", "mensaje" => "No se llenaron los campos suficientes segun el pago mediante link/url", "tipo" => "error", "titulo" => "Aviso"]);
            exit();
          }
        } else if ($campos_array[$x] == 219566) { //Pago Internacional
          if (empty($moneda_selected) || empty($valor_pago)) {
            echo json_encode(["estado" => "404", "mensaje" => "No se llenaron los campos suficientes segun el pago internacional.", "tipo" => "error", "titulo" => "Aviso"]);
            exit();
          }
        }
      }
      
      $archivos = [];
      for ($i = 1; $i <= count($_FILES); $i++) {
        $archivo = $this->cargar_archivo("adj" . $i, "archivos_adjuntos/pago_papers/", "Pag");
        if ($archivo[0] == -1) {
          if ($i == 1) {
            exit(json_encode(["estado" => "404", 'mensaje' => "Error al subir el adjunto: Carta de Aceptación. El campo se ha dejado vacío o el documento NO es valido.", 'tipo' => "error", 'titulo' => "Error al cargar archivo!"]));
          } elseif ($i == 2) {
            exit(json_encode(["estado" => "404", 'mensaje' => "Error al subir el adjunto: Revisión de Cuartil. El campo se ha dejado vacío o el documento NO es valido.", 'tipo' => "error", 'titulo' => "Error al cargar archivo PDF!"]));
          } elseif ($i == 3) {
            exit(json_encode(["estado" => "404", 'mensaje' => "Error al subir el adjunto: Procedimientos para Pagos Internacionales. El campo se ha dejado vacío o el documento NO es valido.", 'tipo' => "error", 'titulo' => "Error al cargar archivo!"]));
          } else {
            exit(json_encode(["estado" => "404", 'mensaje' => "Error al subir el adjunto: Documento en caso de moneda internacional. El campo se ha dejado vacío o el documento NO es valido.", 'tipo' => "error", 'titulo' => "Error al cargar archivo!"]));
          }
        } else array_push($archivos, $archivo[1]);
      }
      
      $datos = [
        "carta_aceptacion" => $archivos[0],
        "revision_cuartil" => $archivos[1],
        "id_usuario_registra" => $id_user_registra,
        "id_articulo" => $id_articulo,
        "fecha_max_pago" => $fecha_max_pago,
        "rev_o_conf" => $rev_o_conf,
        "id_cuartil_selected" => $id_cuartil_selected,
        "id_revista_selected" => $id_revista_selected,
        "id_codsap_selected" => $id_codsap_selected,
        "id_pago_selected" => $pago_selected,
        "id_banco_selected" => $banco_selected,
        "id_tipocuentabnk_selected" => $tipo_cuenta,
        "id_moneda_selected" => $moneda_selected,
        "valor_pago" => $valor_pago,
        "link_pago" => $link_pago,
        "num_art_ide" => $num_indenti_articulo,
        "id_estado" => $estado,
        "id_tipo_solicitud" => "Pub_Pag"
      ];
      
      if (count($archivos) > 2) {
        $datos += ["adj_pag_inter" => $archivos[2], "adj_pag_extran" => $archivos[3]];
      }
      
      $query = $this->publicaciones_model->Solicitud_Pago_Papers($datos);
      
      if ($query == true) {
        echo json_encode(["estado" => "ok", "mensaje" => "Los datos se han diligenciado exitosamente", "tipo" => "success", "titulo" => "Muy bien!"]);
        $sendTo_pe = [
          "id_publicacion" => $this->last_pub()->{'id'},
          "id_estado" => "Pub_Env_E",
          "id_usuario_registra" => $_SESSION['persona'],
          "estado" => 1
        ];
        $this->publicaciones_model->guardar_datos($sendTo_pe, "publicaciones_estados", 1);
      } else {
        echo json_encode(["estado" => "404", "mensaje" => "Error al diligenciar el formulario. La operacion se cancelo.", "tipo" => "error", "titulo" => "Algo salio mal!"]);
      }
    }
  }
  
  public function buscar_tipo_moneda()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $moneda = $this->input->post('moneda');
      $moneda_result = $this->publicaciones_model->Buscar_Tipo_Moneda($moneda);
      echo json_encode($moneda_result);
    }
  }
  
  public function buscar_tipo_pago()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $pago = $this->publicaciones_model->Buscar_Tipo_Pago();
      echo json_encode($pago);
    }
  }
  
  public function buscar_tipo_cuentab()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $tipo_cta = $this->publicaciones_model->Buscar_Tipo_CuentaB();
      echo json_encode($tipo_cta);
    }
  }
  
  public function listar_bancos()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $bancos = $this->publicaciones_model->Listar_Bancos();
      echo json_encode($bancos);
    }
  }
  
  public function save_auths_procents()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $datos = $this->input->post("datos");
      $contador = 0;
      for ($x = 0; $x < count($datos); $x++) {
        $id_autor = $datos[$x]["id_autor"];
        $puntos = $datos[$x]["puntos"];
        if (empty($id_autor)) {
          $contador == 0 ? $contador = 0 : $contador--;
          exit(json_encode(["estado" => "404", "mensaje" => "Falta el ID de un(es) autor(es) para diligenciar correctamente la asignacion de porcentajes", "tipo" => "error", "titulo" => "Sin ID!"]));
        } else {
          $array_toSend = ["id_autor" => $id_autor, "id_publicacion" => $this->last_pub()->{'id'}, "puntos" => $puntos, "tabla" => "personas"];
          $query = $this->publicaciones_model->Save_Auths_Procents($array_toSend);
          $contador++;
        }
      }
      
      if ($contador < count($datos)) {
        echo json_encode(["estado" => "404", "mensaje" => "Error al diligencias los autores y sus porcentajes, rectifique el no dejar campos vacios.", "tipo" => "error", "titulo" => "Algo salio mal!"]);
      } else {
        echo json_encode(["estado" => "ok", "mensaje" => "Los autores y sus porcentajes correspondientes se han guardado con éxito.", "tipo" => "success", "titulo" => "Enhorabuena"]);
      }
    }
  }
  
  public function last_pub()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      return $this->publicaciones_model->Last_Pub();
    }
  }
  
  public function listar_archivos_pagop()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $id = $this->input->post("id_p");
      $datos = $this->publicaciones_model->Listar_Archivos_Pagop($id);
      echo json_encode($datos);
    }
  }
  
  public function tipos_de_publicacion()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $datos = $this->publicaciones_model->Listar_Tipos_De_Publicacion();
      echo json_encode($datos);
    }
  }
  
  public function obtener_estados_pub()
  {
    if (!$this->Super_estado) {
      $r = ["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $r = $this->publicaciones_model->obtener_estados_pub();
    }
    echo json_encode($r);
  }
  
  public function validad_links($link)
  {
    $er = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|](\.)[a-z]{2}/i";
    if (!empty($link)) {
      if (preg_match($er, $link)) {
        return 1;
      } else {
        return 0;
      }
    } else {
      return false;
    }
  }
  
  public function guardarBorrador(){
    if(!$this->Super_estado == true) {
      $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    }else{
      $id_usuario_registro = $_SESSION['persona'];
      
      $data = ['id_usuario_registra' => $id_usuario_registro, 'id_estado' => "Bon_Sol_Creado", 'id_tipo_solicitud' => "Pub_Bon"];

      $resp = ['mensaje' => "Solicitud de bonificación creada.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      $add_solicitud = $this->publicaciones_model->guardar_datos($data, 'publicaciones_solicitudes');
      if ($add_solicitud != -1){
        $id_solicitud = $this->publicaciones_model->Last_Pub()->{'id'};
        $add_estado = $this->publicaciones_model->guardar_datos(['id_usuario_registra' => $id_usuario_registro, 'id_estado' => "Bon_Sol_Creado", "id_publicacion" => $id_solicitud], "publicaciones_estados");
        if ($add_solicitud != -1 && $add_estado != -1){
          $solicitud = $this->publicaciones_model->traer_ultima_solicitud($id_usuario_registro);
          $resp['solicitud'] = $solicitud;
        }
      }else if ($add_solicitud == -1){
        $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }
      
    }
    echo json_encode($resp);
  }

  public function obtenerUltimaSolicitud(){
    if(!$this->Super_estado == true) {
      $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    }else{
      $id_usuario_registro = $_SESSION['persona'];
      $solicitud = $this->publicaciones_model->traer_ultima_solicitud($id_usuario_registro);
      if($solicitud){
        $resp = ['mensaje' => "Se encontró solicitud.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'solicitud' => $solicitud];
      }else{
        $resp = ['mensaje' => "No se ha encontrado una solicitud activa, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }
      
    }
    echo json_encode($resp);
  }
  
  public function update_bonificaciones() {
    if (!$this->Super_estado) {
      $resp = ["mensaje" => "No tienes permisos suficientes.", "tipo" => "error", "titulo" => "Error"];
    } else {
      $id_solicitud = $this->input->post("id_solicitud");
      $id_articulo = $this->input->post("art_id");
      $articulo_link = $this->input->post("articulo_link");
      $id_cuartil_scopus = $this->input->post("cuartil_scopus");
      $id_cuartil_wos = $this->input->post("cuartil_wos");
      //$id_cuartil_liq_bon = $this->input->post("cuartil_liq_bon");
      $fecha_publicacion = $this->input->post("date__initial");
      $id_revista = $this->input->post("id_revista");
      $isbn_bon = $this->input->post("isbn_bon");
      $issn_bon = $this->input->post("issn_bon");
      $id__doi = $this->input->post("id__doi");
      $editorial = $this->input->post("editorial");
      $id_proyecto = $this->input->post("id_proyecto_bon");
      $url_indexacion_scopus = $this->input->post("url_indexacion_scopus");
      $url_indexacion_wos = $this->input->post("url_indexacion_wos");
      $date__indexing = $this->input->post("date__indexing");
      $linea_inv = $this->input->post("lineaInv__bon");
      $sublinea = $this->input->post("SublineaInv__bon");
      $ods__bon = $this->input->post("ods__bon");
      $idioma = $this->input->post("idiomas");
      $data_tipos_escrituras =  json_decode($this->input->post('data_tipos_escrituras'));
      

      if(!$sublinea || !$linea_inv || !$data_tipos_escrituras || !$idioma || !$ods__bon || !$date__indexing || !$editorial){
        $resp = ['mensaje' => "Es necesario que ingrese todos los campos.", 'tipo' => "warning", 'titulo' => "Oops.!"];
        echo json_encode($resp);
        return false;
      }

      $ubicacion_proyecto = $this->publicaciones_model->obtener_informacion_principal($id_solicitud);
      if(($ubicacion_proyecto[0]['ubicacion_proyecto'] == 'index') && $id_proyecto == "null"){
        $resp = ['mensaje' => "Para continuar es necesario que seleccione un Proyecto.", 'tipo' => "warning", 'titulo' => "Oops.!"];
        echo json_encode($resp);
        return false;
      }
      if($ubicacion_proyecto[0]['ubicacion_proyecto'] == 'manual'){
        $id_proyecto = null;
      }

      $cantidad = $this->publicaciones_model->validar_existencia_datos('bonificaciones_solicitudes', $where="id_publicacion = $id_solicitud");
      
      if($cantidad != 0) {
        // $resp = ['mensaje' => "Ya existe una información almacenada para el módulo principal", 'tipo' => "warning", 'titulo' => "Oops.!"];
        $data_bonificaciones = [
          'url_articulo' => $articulo_link,
          'isbn' => $isbn_bon,
          'issn' => $issn_bon,
          'id_titulo_articulo' => $id_articulo,
          'id_publicacion' => $id_solicitud,
          'doi' => $id__doi ? $id__doi : NULL,
          'editorial' => $editorial,
          'id_proyecto' => $id_proyecto ? $id_proyecto : NULL,
          'id_cuartil_scopus' => $id_cuartil_scopus ? $id_cuartil_scopus : NULL,
          'id_cuartil_wos' => $id_cuartil_wos ? $id_cuartil_wos : NULL,
          'url_indexacion_scopus' => $url_indexacion_scopus,
          'url_indexacion_wos' => $url_indexacion_wos,
          'id_linea_inv' => $linea_inv,
          'id_sublinea_inv' => $sublinea,
          'ano_indexacion' => $date__indexing,
          'ods' => $ods__bon,
          'idioma' => $idioma,
        ];

        $data = [
          'fecha_publicacion' => $fecha_publicacion,
          'id_revista' => $id_revista,
        ];

        $update_pub = $this->publicaciones_model->modificar_datos($data, "publicaciones_solicitudes", $id_solicitud);
        if($update_pub != 0) {
          $resp = ['mensaje' => "Error al actualizar la información, por favor contacte al administrador.", 'tipo' => "warning", 'titulo' => "Oops.!"];
        }else{
          $id_bon = $this->publicaciones_model->obtenerIDSolicitud_bon($id_solicitud)[0]['id'];
          $update_bon = $this->publicaciones_model->modificar_datos($data_bonificaciones, "bonificaciones_solicitudes", $id_bon);
          if($update_bon == 0){
            $resp = ['mensaje' => "Información actualizada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          }else{
            $resp = ['mensaje' => "Error al actualizar la información, por favor contacte al administrador.", 'tipo' => "warning", 'titulo' => "Oops.!"];
          }
        }
      }else{ 

        $data = [
          'fecha_publicacion' => $fecha_publicacion,
          'id_revista' => $id_revista,
        ];
        
        $data_bonificacions = [
          'url_articulo' => $articulo_link,
          'isbn' => $isbn_bon,
          'issn' => $issn_bon,
          'id_titulo_articulo' => $id_articulo,
          'id_publicacion' => $id_solicitud,
          'doi' => $id__doi ? $id__doi : NULL,
          'editorial' => $editorial,
          'id_proyecto' => $id_proyecto,
          'id_cuartil_scopus' => $id_cuartil_scopus ? $id_cuartil_scopus : NULL,
          'id_cuartil_wos' => $id_cuartil_wos ? $id_cuartil_wos : NULL,
          //'id_cuartil_liq_bon' => $id_cuartil_liq_bon,
          'url_indexacion_scopus' => $url_indexacion_scopus,
          'url_indexacion_wos' => $url_indexacion_wos,
          'id_linea_inv' => $linea_inv,
          'id_sublinea_inv' => $sublinea,
          'ano_indexacion' => $date__indexing,
          'id_persona_registra' => $_SESSION['persona'],
          'idioma' => $idioma,
          'ods' => $ods__bon
        ];

        $existe = $this->publicaciones_model->validar_existencia_id_articulo($id_articulo);


        $existe == 0 ? $update_pub = $this->publicaciones_model->modificar_datos($data, "publicaciones_solicitudes", $id_solicitud) : $update_pub = 0;
        if($update_pub != -1 && $existe == 0) $add_bonificaciones = $this->publicaciones_model->guardar_datos($data_bonificacions, 'bonificaciones_solicitudes');
        if($existe == 1){
          $resp = ['mensaje' => "Nombre del articulo ya existe en una solicitud anterior de bonificacion.", 'tipo' => "warning", 'titulo' => "Oops.!"];
        }else{
          if($add_bonificaciones != -1) {

            foreach ($data_tipos_escrituras as $tipo_escritura) {
              $datos_tipos = [
                'categoria' => $tipo_escritura->{'categoria'},
                'id_bonificacion' => $this->publicaciones_model->obtenerIDSolicitud_bon($id_solicitud)[0]['id'],
                'id_usuario_registra' => $_SESSION['persona'],
              ];
              $insert_types = $this->publicaciones_model->guardar_datos($datos_tipos, 'bonificaciones_tipos_escrituras');
            };
            if($insert_types != -1){
              $resp = ['mensaje' => "Solicitud de bonificación actualizada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            }
          } else {
            $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          }
        }
       
      }
    }
    echo json_encode($resp);
  }
  
  
  public function validar_cantidad_de_solicitud(){
    $persona = $_SESSION['persona'];
    $inicial = ['id' => NULL, 'cantidad' => '0'];
    $cantidad_solicitud = $this->publicaciones_model->validar_cantidad_de_solicitud($persona);
    foreach ($cantidad_solicitud as $row) {
      if ($row->{'id_tipo_solicitud'} === 'Pub_Bon') {
        $inicial = ['id' => $row->{'id'}, 'cantidad' => $row->{'cantidad'}, 'estado' => $row->{'id_estado'}];
      }
    }
    $datos = ['inicial' => $inicial];
    echo json_encode($datos);
  }
  
  public function obtener_data__bonificaciones(){
    $persona = $_SESSION['persona'];
    $id_solicitud = $this->input->post("id");
    $datos = $this->publicaciones_model->obtener_data__bonificaciones($id_solicitud);
    echo json_encode($datos);
  }

  public function guardar_autores_bonificaciones2() {
    if(!$this->Super_estado){
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $data_autores = [];
      $info_autores = [];
      $persona = $_SESSION['persona'];
      $autores = $this->input->post('data');

      $cantidad = $this->publicaciones_model->validar_autores_existentes($autores['id'], $autores['afiliacion'], $autores['id_solicitud']);

      if($autores['afiliacion'] === "estudiante"){
        $validate = $this->verificar_campos_numericos(['Programa Academico' => $autores['programa_acad']]);
      }else if($autores['afiliacion'] === "externo"){
        $validate = $this->verificar_campos_numericos(['Institución Externa' => $autores['institucion_ext']]);
      }
          
      if($cantidad != 0){
        $resp = ['mensaje' => "Autor ya ha sido agregado con anterioridad.", 'tipo' => "warning", 'titulo' => "Oops.!"];
      }else if($autores['afiliacion'] != "profesor" && $validate != 1){
        $resp = ['mensaje' => "Debe diligenciar el campo {$validate['field']} antes de continuar", 'tipo' => "info", 'titulo' => "Oops..!"];
      }else{
        $sw1 = false;
        if($autores['afiliacion'] === "externo") $sw1 = true;
        $data_autor = [
          'id_persona' => $autores['id'],
          'id_bonificacion' => $autores['id_solicitud'],
          'afiliacion' => $autores['afiliacion'],
          'id_persona_registra' => $persona,
          'institucion_ext' => $sw1 ? $autores['institucion_ext'] : NULL,
          'id_programa' => $autores['afiliacion'] === "profesor" ? $autores['id_programa'] : NULL, 
        ];
        array_push($data_autores, $data_autor);
        $add = $this->publicaciones_model->guardar_datos($data_autores, "bonificaciones_autores",2);
  
        if($add != 0){
          $resp = ['mensaje' => "Error al guardar autores, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }else if($add == 0 && !$sw1){
          $sw = false;
          $documentoAutores = $this->publicaciones_model->obtenerAutoresCreados($autores['id'], $autores['id_solicitud'], 1);
          if($autores['afiliacion'] === "estudiante") $sw = true;
          else $sw = false;

          $info_autor = [
            "programa_acad" => $sw ? $autores['programa_acad'] : NULL,
            "id_bonificaciones_autores" => $documentoAutores->{'id'},
            "id_persona_registra" => $persona,
          ];
          array_push($info_autores, $info_autor);

          $add2 = $this->publicaciones_model->guardar_datos($info_autor, "bonificaciones_informacion_autores");
  
          if($add2 != 0){
            $resp = ['mensaje' => "Error al guardar autores, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          }else if($add2 == 0){
            $resp = ['mensaje' => "Información guardada con exito con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          }
        }else{
          $resp = ['mensaje' => "Información guardada con exito con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }
      
    }
    echo json_encode($resp);
  }
  
  public function guardar_autores_bonificaciones()
  {
    if (!$this->Super_estado){
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $data_autores = [];
      $info_autores = [];
      $info_suscrito = [];
      $info_cumplido = [];
      $persona = $_SESSION['persona'];
      $autores_profesores = json_decode($this->input->post('autores_profesores'), true);
      $autores_estudiantes = json_decode($this->input->post('autores_estudiantes'), true);
      $autores_externos = json_decode($this->input->post('autores_externos'), true);
      $articulos_suscritos = json_decode($this->input->post('articulos_suscritos'), true);
      $articulos_cumplidos = json_decode($this->input->post('articulos_cumplidos'), true);
      $id_solicitud = $this->input->post("id_solicitud");

      
        if(!$id_solicitud || $id_solicitud == null){
          $resp = ['mensaje' => "Para almacenar los autores, es necesario ingrese primero la información principal.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }else{
          $autores = array_merge($autores_profesores, $autores_estudiantes, $autores_externos);
          foreach ($autores as $autor) {
            $sw1 = false;
            if($autor['afiliacion'] === "externo") $sw1 = true;
            $data_autor = [
              'id_persona' => $autor['id'],
              'id_bonificacion' => $id_solicitud,
              'afiliacion' => $autor['afiliacion'],
              'id_persona_registra' => $persona,
              "institucion_ext" => $sw1 ? $autor['institucion_ext'] : NULL,
            ];
            array_push($data_autores, $data_autor);
          }
        }
      if(!$articulos_suscritos || !$articulos_cumplidos || $articulos_cumplidos == null || $articulos_suscritos == null){
        $resp = ['mensaje' => "Es necesario que agregue los articulos suscritos y artuculos cumplidos antes de continuar", 'tipo' => "error", 'titulo' => "Oops.!"];
        $add = null;
      }else{
        $cantidad_autores = (count($data_autores));
        $add = $this->publicaciones_model->guardar_datos($data_autores, "bonificaciones_autores",2);
        if ($add != 0) {
          $resp = ['mensaje' => "Error al guardar autores, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }else if ($add == 0){
          $sw = false;
          foreach ($autores as $autor){
            $documentoAutores = $this->publicaciones_model->obtenerAutoresCreados($autor['id'], $id_solicitud, 1);
            if($autor['afiliacion'] === "estudiante") $sw = true;
            else $sw = false;
            $info_autor = [
              "programa_acad" => $sw ? $autor['programa_acad'] : NULL,
              "departamento" => !$sw ? $autor['departamento'] : NULL,
              "research_gate" => !$sw ?  $autor['researchGate'] : NULL,
              "hi_Scopus" => !$sw ?  $autor['HiScopus'] : NULL ,
              "hi_scholar" => !$sw ?  $autor['HiScholar'] : NULL,
              "categ_minciencias" => !$sw ?  $autor['catMinciencias'] : NULL,
              "id_bonificaciones_autores" => $documentoAutores->{'id'},
              "id_persona_registra" => $persona,
            ];
            array_push($info_autores, $info_autor);
          }
          
          $add2 = $this->publicaciones_model->guardar_datos($info_autores, "bonificaciones_informacion_autores",2);
          if($add2 != 0){
            $resp = ['mensaje' => "Error al guardar autores, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          }else{
            $resp = ['mensaje' => "Información guardada con exito con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          }
        }
      };
      
    }
    echo json_encode($resp);
  }
  
  public function listar_autores_bonificacion()
  {
    $resp = [];
    if (!$this->Super_estado) {
      $publicaciones = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bon" ? true : false;
      $autores = $this->publicaciones_model->listar_autores_bonificacion($id);
      foreach ($autores as $row) {
        $row['ver'] = '<span style="background-color: white;color: black; width: 100%;" class="pointer form-control ver" ><span >ver</span></span>';
        array_push($resp, $row);
      }
    }
    echo json_encode($resp);
  }

  public function validateData (){
    $resp = [];
    $afiliacion = $this->input->post('afiliacion_inst');
    $datos = $this->input->post('datos');
    $val = [];
      if($afiliacion === "estudiante"){
        $val = [
          'Programa Academico' => $datos[0]['programa_est_bon'],
        ];
      }else if($afiliacion === "externo"){
        $val = [
          'Institución del Autor' => $datos[0]['inst_ext_bon'],
        ];
      }
      $result = $this->verificar_campos_numericos($val);
      if (is_array($result)) {
        $resp = ['mensaje'=>"El campo {$result['field']} no puede estar vacio o sin seleccionar.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
      }else{
        $resp = ['mensaje' => "Campos completos.", 'tipo' => "success", 'titulo' => "Validación exitosa.!"];
      };
      echo json_encode($resp);
  }

  public function asignar_porcentaje()
  {
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $articulo = $this->input->post('id_solicitud');
      $resul_autores = $this->publicaciones_model->asignar_porcentaje($articulo);
      echo json_encode($resul_autores);
    }
  }

  public function update_porcentaje() {
    if (!$this->Super_estado) {
      $resp = ["mensaje" => "No tienes permisos suficientes.", "tipo" => "error", "titulo" => "Error"];
    } else {
      $data = [];
      $suma = 0;
      $existe = 0;
      $datos = $this->input->post('datos');
      $id = $this->input->post('id_articulo');
      $porcentajes_almac =  $this->publicaciones_model->obtener_suma_porcentajes($id, "profesor");
    
      $add = 0;

      foreach ($datos as $dato) {
        foreach ($porcentajes_almac as $porcentaje) {
          if(count($porcentaje) != 0){
            $suma += $porcentaje['porcentaje'];
            if($porcentaje['id_persona'] == $dato['data']['usuario']) $existe = 1;
          }
        }
        if(((intval($suma) + intval($dato['data']['first_porcentage'])) > 100) && $existe == 0 ){
          $resp = ['mensaje' => "El porcentaje que intenta asignar es mayor a la suma de los porcentajes asignados anteriormente", 'tipo' => "warning", 'titulo' => "Oops.!"];
        }else{
          $id_bon = $this->publicaciones_model->getID($dato['data']['usuario'], $id, 'profesor');
          $data = [
            'first_porcentage' => $dato['data']['first_porcentage'],
            'second_porcentage' => $dato['data']['second_porcentage'],
            'third_porcentage' => $dato['data']['third_porcentage'],
            'first_porcentage_cp' => $dato['data']['first_porcentage'],
            'second_porcentage_cp' => $dato['data']['second_porcentage'],
            'third_porcentage_cp' => $dato['data']['third_porcentage'],
          ];
          $update_porc = $this->publicaciones_model->modificar_datos($data, "bonificaciones_autores", $id_bon->{'id'});
          if ($update_porc != 0) $add = 1;
          if($add != 0) {
            $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          } else {
            $resp = ['mensaje' => "Porcentajes almacenados con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          }
        }
      }
    }
    echo json_encode($resp);
  }

  public function update_otros_aspectos() {
    if (!$this->Super_estado) {
      $resp = ["mensaje" => "No tienes permisos suficientes.", "tipo" => "error", "titulo" => "Error"];
    } else {
      $id_solicitud = $this->input->post("id_solicitud");
      $data_respuestas = $this->input->post('data_respuestas');
      $sw = true;
      $datos = array();
      $id_sol = $this->publicaciones_model->obtenerIDSolicitud_bon($id_solicitud);

      
      if(!$id_sol || $id_sol == null){
        $resp = ['mensaje' => "Antes de continuar, es necesario que ingrese primero la información principal.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }else{

        if(!$data_respuestas){
          $resp = ['mensaje' => "No se ha encontrado respuestas a enviar, por favor valide e intente nuevamente.", 'tipo' => "error", 'titulo' => "Oops.!"];
          echo json_encode($resp);
          return false;
        }

        foreach ($data_respuestas as $row) {
          if($row['id_respuesta'] == ''){
            $sw = false;
            $id_pregunta = $row['id_pregunta'];
            break;
          }                       
        }

        if(!$sw){
          $q = $this->publicaciones_model->traer_registro_id($id_pregunta);
          $resp = ['mensaje'=>"No ha respondido: ". $q->{'valor'},'tipo'=>"info",'titulo'=> "Oops.!"];
        }else{
          foreach ($data_respuestas as $row) {
            $row['id_usuario_registra'] = $_SESSION['persona'];
            $row['id_bonificacion'] = $id_sol[0]['id'];
            array_push($datos, $row);
          }
          $dato = $id_sol[0]['id'];
          $existe = $this->publicaciones_model->validar_existencia_datos('bonificaciones_otros_aspectos', "id_bonificacion = $dato");
          if($existe == 0){
            $update = $this->pages_model->guardar_datos($datos, 'bonificaciones_otros_aspectos', 2);
            if($update != -1) {
              $resp = ['mensaje' => "Otros Aspectos guardado con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            } else {
              $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            }
          }else if($existe > 0){
            foreach ($data_respuestas as $row) {
              $pregunta = $row['id_pregunta'];
              $id = $this->publicaciones_model->obtener_valor('id', 'bonificaciones_otros_aspectos', "id_bonificacion = $dato AND id_pregunta = $pregunta");
              $update = $this->publicaciones_model->modificar_datos($row, "bonificaciones_otros_aspectos", $id[0]['id']);
            }
            if($update != 0) {
              $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
              $resp = ['mensaje' => "Valores actualizados con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            }
          }

        };

      };
      
    }
    echo json_encode($resp);
  }

  public function obtener_data_revista(){
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post("id");
      $datos = $this->publicaciones_model->obtener_data_revista($id);
    };
    echo json_encode($datos);
  }

  public function listar_autor_porTipo(){
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id_bonificacion = $this->input->post("id_solicitud");
      $afiliacion = $this->input->post("id_afiliacion");
      $datos = $this->publicaciones_model->listar_autor_porTipo($id_bonificacion, $afiliacion);
    };
    echo json_encode($datos);
  }
  
  public function guardar_evidencias() {
    if (!$this->Super_estado) {
      $resp = ["mensaje" => "No tienes permisos suficientes.", "tipo" => "error", "titulo" => "Error"];
    } else {
      $id_solicitud = $this->input->post("id_solicitud");
      $datos = $this->input->post();
      $evidencias = [];
      $sw = true;
      unset($datos['id_solicitud']);
      $id_sol = $this->publicaciones_model->obtenerIDSolicitud_bon($id_solicitud);
      if(!$id_sol || $id_sol == null){
        $resp = ['mensaje' => "Antes de continuar, es necesario que ingrese primero la información principal.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }else{
        $num = 0;
        foreach($datos as $info => $value){
          $num += 1;
          if(!empty($_FILES["adj_evidencia_bon_$num"]["size"])){
            $nombre = $_FILES["adj_evidencia_bon_$num"]["name"]; 
            $file_evidence = $this->cargar_archivo("adj_evidencia_bon_$num", $this->ruta_evidencia, 'evidencia_bon_');
            if ($file_evidence[0] == -1){
              $resp = ['mensaje'=>"Error al cargar al cargar la evidencia.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
              $adj_evi = '';	
              $sw = false;			
            }else{
              $adj_evi = $file_evidence[1];
            }
          }else{
            $adj_evi = '';	
          }
          if($sw){
            array_push($evidencias , [
              'id_bonificacion' => $id_sol[0]['id'],
              "comentario" => $value,
              'id_usuario_registra' => $_SESSION['persona'],
              'nombre_archivo' => $adj_evi,
            ]);
          }
        };
        $add = $this->publicaciones_model->guardar_datos($evidencias, "bonificaciones_evidencias",2);
        if($add != -1) {
          $resp = ['mensaje' => "Evidencias guardadas con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        } else {
          $resp = ['mensaje' => "Error al guardar las evidencias, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }; 
    }
    echo json_encode($resp);
  }
  
  public function ver_detalle_autor_bonificaciones() {
    $resp = array();
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $documento = $this->input->post('id');
      $id_solicitud = $this->input->post('id_solicitud');
      $id_afiliacion = $this->input->post('id_afiliacion');
      $resp = $this->publicaciones_model->ver_detalle_autor_bonificaciones($documento, $id_solicitud, $id_afiliacion);
    }
    echo json_encode($resp);
  }
  
  public function obtener_opc__bonific()
  {
    $select = $this->Super_estado == true ? $this->publicaciones_model->obtener_opc__bonific() : array();
    echo json_encode($select);
  }
  
  public function deshabilitar() {
    if (!$this->Super_estado) {
      $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    }else{
      $id = $this->input->post("id");
      $tabla = $this->input->post("tabla");
      $resp= ['mensaje'=>"Se ha eliminado satisfactoriamente",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
      $add = $this->publicaciones_model->deshabilitar($id , $tabla);
      if($add != 2) $resp= ['mensaje'=>"Ha ocurrido un error al eliminar",'tipo'=>"error",'titulo'=> "Oops.!"];
    }
    echo json_encode($resp);
  }

  public function guardar_comentario() {
    if (!$this->Super_estado) {
      $resp = ["mensaje" => "No tienes permisos suficientes.", "tipo" => "error", "titulo" => "Error"];
    } else {
      $id_bonificacion = $this->input->post("id_bonificacion");
      $id_persona = $this->input->post("id_persona");
      $id_afiliacion = $this->input->post("id_afiliacion");
      $comentario = $this->input->post("comment");
      $id = $this->publicaciones_model->getID($id_persona, $id_bonificacion, $id_afiliacion);
      $update = $this->publicaciones_model->modificar_datos(['comentario' => $comentario], "bonificaciones_autores", $id->{'id'});
      if($update != -1) {
        $resp = ['mensaje' => "Comentario agregado con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      } else {
        $resp = ['mensaje' => "Error al guardar el comentario, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }
      
    }
    echo json_encode($resp);
  }

  public function guardar_afiliacion_institucional() {
    if (!$this->Super_estado) {
      $resp = ["mensaje" => "No tienes permisos suficientes.", "tipo" => "error", "titulo" => "Error"];
    } else {
      $id_bonificacion = $this->input->post("id_solicitud");
      $id_afiliacion = $this->input->post("id_afiliacion");
      $pais = $this->input->post("pais");
      $nombre_inst = $this->input->post("institucion");
      $id_persona_afil_inst = $this->input->post("id_persona_afil_inst");
      $id_persona = $_SESSION['persona'];
      $data = [
       'id_bonificacion' => $id_bonificacion,
       'nombre_inst' => $nombre_inst,
       'pais' => $pais,
       'id_persona_registra' => $id_persona,
       'id_autor' => $this->publicaciones_model->obtenerAutoresCreados($id_persona_afil_inst, $id_bonificacion, 1, $id_afiliacion)->{'id'},
      ];
      $add = $this->publicaciones_model->guardar_datos($data, 'bonificaciones_afiliaciones_institucionales');
      if($add == 0) {
        $resp = ['mensaje' => "Afiliación agregada con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      } else {
        $resp = ['mensaje' => "Error al guardar la afiliación, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }
      
    }
    echo json_encode($resp);
  }

  public function actualizar_afiliaciones_institucionales() {
    if (!$this->Super_estado) {
      $resp = ["mensaje" => "No tienes permisos suficientes.", "tipo" => "error", "titulo" => "Error"];
    } else {
      $date = date('Y-m-d h:i:s');
      $id = $this->input->post("id");
      $tipo_actualizacion = $this->input->post("tipo_act");
      $institucion = $this->input->post("institucion");
      $id_persona = $_SESSION['persona'];
      $proceso = "";
      if($tipo_actualizacion == 'Eliminar'){
        $data = [
         'id_persona_elimina' => $id_persona,
         'fecha_elimina' => $date,
         'estado' => 0,
        ];
        $proceso = "eliminada";

      }else if($tipo_actualizacion == "Actualizacion"){
        $data = [
         'nombre_inst' => $nombre_inst,
         'id_persona_registra' => $id_persona,
        ];
        $proceso = "actualizada";
      }
      $edit = $this->publicaciones_model->modificar_datos($data, "bonificaciones_afiliaciones_institucionales", $id);
      if($edit != -1) {
        $resp = ['mensaje' => "Afiliación $proceso con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      } else {
        $resp = ['mensaje' => "Error al procesar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }
      
    }
    echo json_encode($resp);
  }

  public function obtener_porcentaje() {
    if (!$this->Super_estado) {
      $resp = ["mensaje" => "No tienes permisos suficientes.", "tipo" => "error", "titulo" => "Error"];
    } else {
      $id_persona = $this->input->post("id_persona");
      $id_bonificacion = $this->input->post("id_solicitud");
      $id_afiliacion = $this->input->post("id_afiliacion");
      $resp = $this->publicaciones_model->obtener_porcentaje($id_persona, $id_bonificacion, $id_afiliacion);
    }
    echo json_encode($resp);
  }

  public function obtener_afiliaciones_institucionales() {
    if (!$this->Super_estado) {
      $resp = ["mensaje" => "No tienes permisos suficientes.", "tipo" => "error", "titulo" => "Error"];
    } else {
      $id_bonificacion = $this->input->post("id_solicitud");
      $id_persona = $this->input->post("id_persona_afil_inst");
      $id_afiliacion = $this->input->post("id_afiliacion");
      $documentoAutores = $this->publicaciones_model->obtenerAutoresCreados($id_persona, $id_bonificacion, 1, $id_afiliacion)->{'id'};
      $resp = $this->publicaciones_model->obtener_afiliaciones_institucionales($documentoAutores, $id_afiliacion);
    }
    echo json_encode($resp);
  }

  public function suma_porcentajes(){
    if (!$this->Super_estado) {
      exit(json_encode(["estado" => "404", 'mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
    } else {
      $suma = 0;
      $id = $this->input->post('id_articulo');
      $porcentajes_almac = $this->publicaciones_model->obtener_suma_porcentajes($id, "profesor");
      foreach ($porcentajes_almac as $porcentaje) {
        if(count($porcentaje) != 0){
          $suma += $porcentaje['porcentaje'];
        }
      }
      echo json_encode($suma);
    }
  }

  public function obtener_preguntas_otros_aspectos(){
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $datos = $this->publicaciones_model->obtener_preguntas_otros_aspectos();
      echo json_encode($datos);
    }
  }

  public function obtener_respuestas_otros_aspectos(){
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $datos = $this->publicaciones_model->obtener_respuestas_otros_aspectos();
      echo json_encode($datos);
    }
  }

  public function traer_registro_id(){
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $datos = $this->publicaciones_model->traer_registro_id($id);
      echo json_encode($datos);
    }
  }

  public function cambiar_valor_corresponding(){
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $valor = $this->input->post('valor');
      $id = $this->input->post('id');
      $id_afiliacion = $this->input->post('id_afiliacion');
      $id_bonificacion = $this->input->post('id_solicitud');
      $documentoAutores = $this->publicaciones_model->obtenerAutoresCreados($id, $id_bonificacion, 1, $id_afiliacion)->{'id'};

      $update = $this->publicaciones_model->modificar_datos(['corresponding_author' => $valor], "bonificaciones_autores", $documentoAutores);

      if($update != -1) {
        $resp = ['mensaje' => "Corresponding Author modificado con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      } else {
        $resp = ['mensaje' => "Error al guardar el comentario, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }
    }
    echo json_encode($resp);
  }

  public function obtener_articulos_suscritos () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $resp = $this->publicaciones_model->obtener_articulos_suscritos($id);
      echo json_encode($resp);
    };
  }
  
  public function listar_articulos_cumplidos () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id = $this->input->post('id');
      $resp = $this->publicaciones_model->listar_articulos_cumplidos($id);
      echo json_encode($resp);
    };
  }

  public function obtener_lista_requerimiento_bon () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id_solicitud = $this->input->post('id_bon');
      $tipo_gestion = $this->input->post('tipo_gestion');
      $resp = $this->publicaciones_model->obtener_lista_requerimiento_bon($id_solicitud, $tipo_gestion );

      echo json_encode($resp);
    };
  }

  public function listar_respuestas_requerimientos () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    } else {
      $id_solicitud = $this->input->post('id_solicitud');
      $filtrar = $this->input->post('filtrar');
      $resp = $this->publicaciones_model->listar_respuestas_requerimientos($id_solicitud, $filtrar );
      echo json_encode($resp);
    };
  }

  public function almacenar_articulos_cumplidos () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $cantidad_autor = $this->input->post('cantidad_autor');
      $link_autor_cump = $this->input->post('link_autor_cump');
      $cuartil_autor = $this->input->post('cuartil_autor');
      $id = $this->input->post('id_autor_bon');
      $id_solicitud = $this->input->post('id_solicitud');
      $title_art = $this->input->post('title_art');

      $data_cumplido = [
        'id_autor' => $id,
        'id_bonificacion' => $id_solicitud,
        'cantidad_autor' => $cantidad_autor,
        'id_cuartil_autor' => $cuartil_autor,
        'titulo_articulo' => $title_art,
        'link' => $link_autor_cump,
        'id_persona_registra' => $_SESSION['persona'],
        'tipo_articulo' => "cumplido",
      ];

      $add = $this->publicaciones_model->guardar_datos($data_cumplido, "bonificaciones_info_articulos");
      if($add == 0) {
        $resp = ['mensaje' => "Articulo almacenado con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      } else {
        $resp = ['mensaje' => "Error al almacenar el articulo, por favor contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }
    }
    echo json_encode($resp);
            
  }
  
  public function modificar_articulos_cumplidos () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id = $this->input->post('id');

      $data = [
        'estado' => 0, 
        'id_persona_elimina' => $_SESSION['persona'], 
        'fecha_elimina' => date('Y-m-d h:i:s')
      ];

      $update = $this->publicaciones_model->modificar_datos($data, "bonificaciones_info_articulos", $id);

      if($update != -1) {
        $resp = ['mensaje' => "Articulo modificado con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      } else {
        $resp = ['mensaje' => "Error al guardar el comentario, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }

    };
    echo json_encode($resp);
  }
  
  public function almacenar_respuestas_requerimientos () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud = $this->input->post('id_solicitud');
      $id_pregunta = $this->input->post('id_pregunta');
      $id_respuesta = $this->input->post('id_respuesta');
      $comentario = $this->input->post('comentario');
      $tipo_gestion = $this->input->post('tipo_gestion');
      $existe = $this->publicaciones_model->obtener_respuestas_requerimientos($id_pregunta, $id_solicitud, $tipo_gestion);
      if($existe){ // si existe se actualiza la información
        $data = [
          'id_respuesta' => $id_respuesta,
          'comentario' => $comentario
        ];
        $update = $this->publicaciones_model->modificar_datos($data, "bonificaciones_respuestas_requerimientos", $existe->{'id'});

        if($update != -1) {
          $resp = ['mensaje' => "Respuesta almacenada con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        } else {
          $resp = ['mensaje' => "Error al guardar la respuesta, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }

      }else if(!$existe){ // Si no existe se almacena la información
        $data = [
          'id_pregunta' => $id_pregunta,
          'id_respuesta' => $id_respuesta,
          'comentario' => $comentario,
          'tipo_gestion' => $tipo_gestion,
          'id_bonificacion' => $id_solicitud,
          'id_persona_registra' => $_SESSION['persona']
        ];
        $add = $this->publicaciones_model->guardar_datos($data, "bonificaciones_respuestas_requerimientos");

        if($add == 0) {
          $resp = ['mensaje' => "Articulo almacenado con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        } else {
          $resp = ['mensaje' => "Error al almacenar el articulo, por favor contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }
      

    };
    echo json_encode($resp);
  }

  public function guardar_gestion_requerimiento () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $reqs = [];
      $id_solicitud = $this->input->post('id_solicitud');
      $estado = $this->input->post('estado');
      $mensaje = $this->input->post('mensaje');
      $datos = $this->input->post('datos');
      $id_persona_registra = $_SESSION['persona'];
      $comite = $this->publicaciones_model->obtener_id_comite();
      $nuevo_estado = "";
      $x = 0;
      $ult_est = $this->publicaciones_model->obtener_ultimo_estado($id_solicitud);
      $result = $this->tiene_permisos($id_persona_registra, $ult_est->{'id_estado'});
      if($result != 1){
        echo json_encode($result);
        return false;
      }

      if($ult_est->{'id_estado'} == "Bon_Sol_Env"){
        $reqs = $this->publicaciones_model->obtener_lista_requerimiento_bon($id_solicitud, "Gest_Ini");
      }else if($ult_est->{'id_estado'} == "Bon_Sol_Aprob_Aux_Pub"){
        $reqs = $this->publicaciones_model->obtener_lista_requerimiento_bon($id_solicitud, "Direct_Public");
      }elseif($ult_est->{'id_estado'} == "Bon_Sol_Rev_Aprob"){
        $reqs = $this->publicaciones_model->obtener_lista_requerimiento_bon($id_solicitud, "Gest_Aux_Public");
      }
      if(!$reqs && $estado != 'Bon_Sol_Cons_Acad'){
        $resp = ['mensaje' => "No se ha logrado encontrar los requerimientos, por favor valide e intente nuevamente", 'tipo' => "error", 'titulo' => "Oops.!"];
        echo json_encode($resp);
        return;
      }else if($reqs && $estado != 'Bon_Sol_Cons_Acad'){
        foreach($reqs as $req){
          if(!$req['id_respuesta']){
            $x += 1;
          }
        };
      }

      if($estado == 'Bon_Sol_Cons_Acad' && !$comite){
        $resp = ['mensaje' => "No se ha encontrado un comité activo, por favor registre uno e intente realizar el proceso nuevamente", 'tipo' => "error", 'titulo' => "Oops.!"];
        echo json_encode($resp);
        return;
      }

      if($x > 0){
        $resp = ['mensaje' => "Antes de continuar, es necesario que responda cada uno de los juicios evaluativos.", 'tipo' => "error", 'titulo' => "Oops.!"];
        echo json_encode($resp);
        return;
      }

			$id_sol = $this->publicaciones_model->obtenerIDSolicitud_bon($id_solicitud);
      if(!$id_sol){
        $resp = ['mensaje' => "Error al guardar, información inicial incompleta", 'tipo' => "error", 'titulo' => "Oops.!"];
        echo json_encode($resp);
        return;
      }

      if($id_solicitud && $estado){
        $update = $this->publicaciones_model->modificar_datos(['id_estado' => $estado], "publicaciones_solicitudes", $id_solicitud);
        if($update != -1) {
          $data = [
            'id_publicacion' => $id_solicitud,
            'id_estado' => $estado,
            'observacion' => $mensaje,
            'id_usuario_registra' => $id_persona_registra ,

          ];
          $add = $this->publicaciones_model->guardar_datos($data, "publicaciones_estados");
          if($add == 0) {
            if($estado == 'Bon_Sol_Cons_Acad'){
              // $id_estado = $estado == 'VoBo' ? 'Bon_Sol_Cons_Acad' : 'Bon_Sol_Vo_Mo_Gest'; 
              // $update2 = $this->publicaciones_model->modificar_datos(['id_estado' => $id_estado], "publicaciones_solicitudes", $id_solicitud);
              // if($update2 != -1 && $id_estado == 'Bon_Sol_Cons_Acad')

              $guardar = $this->publicaciones_model->guardar_datos(['id_usuario_registra' => $id_persona_registra, 'id_bonificacion' => $id_solicitud, 'id_comite' => $comite->{'id'}], "bonificaciones_comite");

              if($guardar == 0) $guardar2 = $this->publicaciones_model->guardar_datos(['id_usuario_registra' => $id_persona_registra, 'id_estado' => 'Bon_Sol_Cons_Acad', 'id_publicacion' => $id_solicitud], "publicaciones_estados");
              if ($guardar2 != 0){ $resp = ['mensaje' => "Error al guardar los datos, contacte con el    administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
              return;}
            }
            if($datos){
              $other_inf = [
                'in_press' => $datos['In_Press'],
                'Observaciones' => $datos['Observaciones'],
                'acuerdo' => $datos['acuerdo'],
                'afiliacion_cuc_correcta' => $datos['afiliacion'],
                'bonificado' => $datos['bonificado'],
                'coleccion_principal' => $datos['coleccion'],
                'ano_indexacion' => $datos['date__indexing'],
                'fecha_anticipada' => $datos['anticipated__date'],
                'profesor_cumple' => $datos['profe_cumple'],
              ];

              $id_sol = $this->publicaciones_model->obtenerIDSolicitud_bon($id_solicitud)[0]['id'];

              $update2 = $this->publicaciones_model->modificar_datos($other_inf, "bonificaciones_solicitudes", $id_sol);

              if(!$update != -1){ 
                $resp = ['mensaje' => "Error al guardar los datos, contacte con el    administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                return;
              }
            }
            
            if($estado == "Bon_Sol_Rev_Dev"){
              $nuevo_estado = "Bon_Sol_Creado";
            }else if($estado == "Bon_Sol_Dev_Aux_Pub"){
              $nuevo_estado = "Bon_Sol_Env"; 
            }else if($estado == "Bon_Sol_Dev_Direct_Pub"){
              $nuevo_estado = "Bon_Sol_Rev_Aprob"; 
            }
            
            if($nuevo_estado){
              $mod_state = $this->publicaciones_model->modificar_datos(['id_estado' => "$nuevo_estado"], "publicaciones_solicitudes", $id_solicitud);
              if($mod_state != -1){
                $data = [
                'id_publicacion' => $id_solicitud,
                'id_estado' => $nuevo_estado,
                'id_usuario_registra' => $id_persona_registra,
                ];
                $add_state = $this->publicaciones_model->guardar_datos($data, "publicaciones_estados");
                if ($add_state != 0) { $resp = ['mensaje' => "Error al guardar los datos, contacte con el    administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];}
              }
            }
            $resp = ['mensaje' => "Datos guardados exitosamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          } else {
            $resp = ['mensaje' => "Error al guardar los datos, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          }
        } else {
          $resp = ['mensaje' => "Error al guardar los datos, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
        }
      }else{
        $resp = ['mensaje' => "No se ha podido guardar la solicitud, por favor contecte al adminitrador", 'tipo' => 'alert', 'titulo' => 'Error al guardar'];
      }
    };
    echo json_encode($resp);
  }

  public function obtener_porcentajes_firma () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud = $this->input->post('id_solicitud');
      $resp = $this->publicaciones_model->obtener_porcentajes_firma($id_solicitud );
    };
    echo json_encode($resp);
  }

  public function verificar_firma_por_id () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud = $this->input->post('id_solicitud');
      $id_persona = $_SESSION['persona'];
      $resp = $this->publicaciones_model->verificar_firma_por_id($id_persona, $id_solicitud );
    };
    echo json_encode($resp);
  }

  public function obtener_ultimo_estado () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud = $this->input->post('id_solicitud');
      $resp = $this->publicaciones_model->obtener_ultimo_estado( $id_solicitud );
    };
    echo json_encode($resp);
  }

  public function obtener_id_parametro () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id = $this->input->post('id');
      $val_bus = $this->input->post('val_bus');
      $resp = $this->publicaciones_model->obtener_id_parametro( $id, $val_bus );
    };
    echo json_encode($resp);
  }

  public function obtener_comites () {
    $resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $tipo_modulo = $this->input->post('tipo_modulo');

      $datas = $this->publicaciones_model->obtener_comites($tipo_modulo);

      $ver_solicitado= '<span  style="background-color: white;color: black; width: 100%; " class="pointer form-control ver_sol" >Ver</span>';
      $ver_finalizado = '<span  style="background-color: #39B23B;color: white; width: 100%; ;" class="pointer form-control ver_fin" >Ver</span>';
      $ver_curso= '<span style="background-color: #EABD32;color: white; width: 100%; ;" class="pointer form-control en_proceso" >Ver</span>';

      $btn_modificar = '<span style="color: #2E79E5;" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench pointer btn btn-default modificar"></span>';
      $btn_enviar= '<span style="color: #EABD32;" title="Enviar" data-toggle="popover" data-trigger="hover" class="fa fa-send pointer btn btn-default enviar"></span>';
      $btn_terminar= '<span title="Terminar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default terminar" style="color:#39B23B"></span>';

      $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
      $btn_abierta = '<span title="Comité en espera..." data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half" style="color:#428bca"></span>';

      foreach($datas as $row){

        $estado_comite = $row['id_estado_comite'];
        $fecha_cierre =date("Y-m-d",strtotime($row['fecha_cierre']));
        $fecha_actual =date("Y-m-d");

        if ($estado_comite == 'Com_Ini') {
          $row['ver'] = $ver_solicitado;
          $row["accion"] = "$btn_modificar $btn_enviar";
        }else   if ($estado_comite == 'Com_Not') {
          $row['ver'] = $ver_curso;
          //$row["accion"] = $fecha_actual >= $fecha_cierre && $adm ? "$btn_modificar $btn_terminar" : $btn_abierta;
          $row["accion"] = "$btn_modificar $btn_terminar";
        }else   if ($estado_comite == 'Com_Ter') {
          $row['ver'] = $ver_finalizado;
          $row["accion"] = $btn_cerrada;
        }
        array_push($resp, $row);
      }
    };
    echo json_encode($resp);
  }

  public function firmar_porcentajes () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud = $this->input->post('id_solicitud');
      $id_persona_registra = $_SESSION['persona'];
      $date = date('Y-m-d h:i:s');
      $id_sol = $this->publicaciones_model->getID($id_persona_registra, $id_solicitud, 'profesor');
      if(!$id_sol){
        $resp = ['mensaje' => "No se ha encontrado el autor con esta solicitud de bonificación.", 'tipo' => "warning", 'titulo' => "Error al guardar"];
        echo json_encode($resp);
        return;
      }
      $data = [
        'firma' => 1,
        'id_persona_firma' => $id_persona_registra,
        'fecha_firma' => $date
      ];
      
      $update = $this->publicaciones_model->modificar_datos($data, "bonificaciones_autores", $id_sol->{'id'});
      $cant_firmas = $this->publicaciones_model->contar_firmas_por_solicitud($id_solicitud);
      
      if($update != -1) {
        if($cant_firmas == '0'){
          $add = $this->publicaciones_model->guardar_datos(['id_usuario_registra' => $id_persona_registra, 'id_estado' => 'Bon_Sol_Env', 'id_publicacion' => $id_solicitud], "publicaciones_estados");
          if($add == 0) $update2 = $this->publicaciones_model->modificar_datos(['id_estado' => 'Bon_Sol_Env'], "publicaciones_solicitudes", $id_solicitud);
          $update2 = $this->publicaciones_model->modificar_datos(['id_estado' => 'Bon_Sol_Env'], "publicaciones_solicitudes", $id_solicitud);
          if($update2 != -1){
            $resp = ['mensaje' => "Solicitud firmada y enviada satisfactoriamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          }
        }else{
          $resp = ['mensaje' => "Solicitud firmada satisfactoriamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }else{
        $resp = ['mensaje' => "No se ha podido firmar la solicitud, por favor contecte al adminitrador", 'tipo' => 'alert', 'titulo' => 'Error al guardar'];
      };
    };
    echo json_encode($resp);
  }

  public function recibir_archivos(){
    if(!$this->Super_estado){
      $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
    }else{
      $id_solicitud = $this->input->post('id_solicitud');
      $id_sol = $this->publicaciones_model->obtenerIDSolicitud_bon($id_solicitud)[0]['id'];
      $nombre = $_FILES["file"]["name"];
      $ruta = $this->ruta_evidencia;

      $archivo = $this->cargar_archivo("file", $ruta, "evidence_");
      if ($archivo[0] == -1) {
        header("HTTP/1.0 400 Bad Request");
        echo ($nombre);
        return;
      }
      $data = [
        "id_bonificacion" => $id_sol,
        "nombre_archivo" => $archivo[1],
        "id_usuario_registra" => $_SESSION['persona']
      ];

      $res = $this->pages_model->guardar_datos($data, 'bonificaciones_evidencias');
      if ($res == -1) {
        header("HTTP/1.0 400 Bad Request");
        echo ($nombre);
        return;
      }
      $resp = ['mensaje'=>"Todos Los archivos fueron cargados.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
    }
    echo json_encode($resp);
  }

  public function visto_bueno_aut () {
    $resp = [];
    if(!$this->Super_estado){
      $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
    }else{
      $id_persona_registra = $_SESSION['persona'];
      $id_solicitud = $this->input->post('id_solicitud');
      $accion = $this->input->post('accion');
      $date = date('Y-m-d h:i:s');
      
      $estados = $this->publicaciones_model->consultar_cambios_estados($id_persona_registra, $id_solicitud);
      foreach ($estados as $estado) {
        if ($estado){
          $val = $accion == 'VoBo' ? 'validacion_autor_bon' : 'no_validacion_autor_bon';
          $id_res = $this->publicaciones_model->consultar_validacion($val);
          $update2 = $this->publicaciones_model->modificar_datos(['id_usuario_valida' => $id_persona_registra, 'fecha_valida' => $date, 'tipo_valida' => $id_res], "publicaciones_estados", $estado['id']);
          if($update2 != -1) $resp = ['mensaje' => "Visto Bueno guardado con exito!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }else{
          $resp = ['mensaje' => "No se puede modificar la información!", 'tipo' => "warning", 'titulo' => "Error.!"];
        }
      }
    };
    echo json_encode($resp);
  }

  public function visto_bueno_ges () {
    if(!$this->Super_estado){
      $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
    }else{
      $id_persona_registra = $_SESSION['persona'];
      $id_solicitud = $this->input->post('id_solicitud');
      $accion = $this->input->post('accion');
      $date = date('Y-m-d h:i:s');
      $aprueba = 0;
      $deniega = 0;
      $cant_est = 0;
      $result = $this->tiene_permisos($id_persona_registra, "Bon_Sol_Aprob_Direct_Pub");
      if($result != 1){
        echo json_encode($result);
        return false;
      }

      $programas = $this->publicaciones_model->traer_programas($id_solicitud, 'Bon_Sol_Aprob_Direct_Pub');
      $cantidad = count($programas);
      $i = 0;
      if(count($programas) == 1){
        $val =  ($accion == 'VoBo') ? 'gestor_si_acepta' : 'gestor_no_acepta';
        $id_res = $this->publicaciones_model->consultar_validacion($val);
        $datos_estado = [
          'id_usuario_registra' => $id_persona_registra,
          'id_estado' => ($accion == 'VoBo') ? 'Bon_Sol_Gest_Aprob' : 'Bon_Sol_Gest_Deni',
          'id_publicacion' => $id_solicitud,
          'id_usuario_valida' =>  $_SESSION['persona'],
          'fecha_valida' => $date,
          'tipo_valida' => $id_res,
        ];
        $add = $this->publicaciones_model->guardar_datos($datos_estado, "publicaciones_estados");
        if($add == 0){
          $mod = $this->publicaciones_model->modificar_datos(['id_estado' => 'Bon_Sol_Gest_Aprob'], "publicaciones_solicitudes", $id_solicitud);
          if($mod != -1){
            $resp = ['mensaje' => "Respuesta almacenada con exito!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          }else{
            $resp = ['mensaje' => "Ha ocurrido un error, por favor contacte al administrador", 'tipo' => "warning", 'titulo' => "Upps.!"];
          }
        }else{
          $resp = ['mensaje' => "Ha ocurrido un error, por favor contacte al administrador", 'tipo' => "warning", 'titulo' => "Upps.!"];
        }
      }else if(count($programas) > 1){
        $sw = false;
        foreach ($programas as $programa) {
          $i += 1;
          if($programa['id_persona'] == $_SESSION['persona'] && $programa['cant_est'] == 0){
            $sw = true;
            # Aqui se cambia el estado
            $val =  ($accion == 'VoBo') ? 'gestor_si_acepta' : 'gestor_no_acepta';
            $id_res = $this->publicaciones_model->consultar_validacion($val);
            $datos = [
              'id_publicacion' => $id_solicitud,
              'id_estado' => ($accion == 'VoBo') ? 'Bon_Sol_Gest_Aprob' : 'Bon_Sol_Gest_Deni',
              'id_usuario_registra' =>  $_SESSION['persona'],
              'id_usuario_valida' =>  $_SESSION['persona'],
              'fecha_valida' => $date,
              'tipo_valida' => $id_res,
            ];

            $add = $this->publicaciones_model->guardar_datos($datos, "publicaciones_estados");
            if($add == 0){
              $resp = ['mensaje' => "Respuesta almacenada con exito!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            }else{
              $resp = ['mensaje' => "No se ha podido almacenar la información!", 'tipo' => "warning", 'titulo' => "Error.!"];
            }
          }
          else if($programa['id_persona'] != $_SESSION['persona'] && !$sw && $cantidad == $i){
            $sw = false;
          }
          else if($programa['id_persona'] == $_SESSION['persona'] && $programa['cant_est'] > 0){
            $sw = true;
            $resp = ['mensaje' => "El gestor ya ha brindado su aval a esta solicitud", 'tipo' => "warning", 'titulo' => "Error.!"];
          }
        }

        if(!$sw && $cantidad == $i ){
          $resp = ['mensaje' => "El gestor no tiene permisos para realizar esta acción", 'tipo' => "warning", 'titulo' => "Error.!"];
        }
      }else if(count($programas) == 0){
        $resp = ['mensaje' => "No se puede ejecutar la acción debido a que actualmente todos los gestores han dado su aprobación o denegación para esta solicitud", 'tipo' => "warning", 'titulo' => "Upps.!"];
      }

      $program = $this->publicaciones_model->traer_aprob_gestores($id_solicitud, 'Bon_Sol_Aprob_Direct_Pub');
        foreach ($program as $programa2) {
          if($programa2['id'] == 0) $cant_est += 1;
        };

        if($cant_est == 0){
          foreach ($program as $programa3) {
            if($programa3['id_estado'] == 'Bon_Sol_Gest_Aprob'){
              $aprueba += 1;
            }else if($programa3['id_estado'] == 'Bon_Sol_Gest_Deni'){
              $deniega += 1;
            }
          };
          if($aprueba > $deniega){
            $mod = $this->publicaciones_model->modificar_datos(['id_estado' => 'Bon_Sol_Revi_Gestor'], "publicaciones_solicitudes", $id_solicitud);
          }else if($aprueba < $deniega){
            $mod = $this->publicaciones_model->modificar_datos(['id_estado' => 'Bon_Sol_Revi_Gestor'], "publicaciones_solicitudes", $id_solicitud);
          }else if($aprueba == $deniega){
            $mod = $this->publicaciones_model->modificar_datos(['id_estado' => 'Bon_Sol_Revi_Gestor'], "publicaciones_solicitudes", $id_solicitud);
          }

          if($mod != -1){
            $datos = [
              'id_publicacion' => $id_solicitud,
              'id_estado' => 'Bon_Sol_Revi_Gestor',
              'id_usuario_registra' =>  $_SESSION['persona'],
            ];
  
            $add = $this->publicaciones_model->guardar_datos($datos, "publicaciones_estados");
  
            if($add == 0){
              $resp = ['mensaje' => "Respuesta almacenada con exito!", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
            }else {
              $resp = ['mensaje' => "No se ha podido almacenar la información!", 'tipo' => "warning", 'titulo' => "Error.!"];
            }
          }else{
            $resp = ['mensaje' => "Ha ocurrido un error, por favor contacte al administrador", 'tipo' => "warning", 'titulo' => "Error.!"];
          }

        }

    };
    echo json_encode($resp);
  }

  public function guardar_comite() {
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
            $comites = $this->publicaciones_model->obtener_comites('');
            if(!empty($comites)){
              foreach ($comites as $comite) {
                if($comite['id_estado_comite'] == 'Com_Ini'){
                  $resp = ['mensaje'=>"Antes de crear un nuevo comité, es necesario que envie los comités activos",'tipo'=>"error",'titulo'=> "Oops.!"];
                  echo json_encode($resp);
                  return;
                }
                //$modificar = $this->publicaciones_model->modificar_datos(['fecha_cierre' => date("Y-m-d H:i"), 'id_estado_comite' => 'Com_Ter'],'comites',$comite['id']);
              }
            }
            $data = array("nombre" => $nombre,"descripcion" => $descripcion,"tipo" => $tipo,"usuario_registra" => $usuario_registra,'fecha_cierre' => $fecha);
            $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Comité Guardado.!"];
            $add = $this->publicaciones_model->guardar_datos($data,'comites');
            if($add != 0) $resp= ['mensaje'=>"Error al guardar el comité, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
          }

        }
      }
    }
    echo json_encode($resp);
  }

  function validateDate($date, $format = 'Y-m-d H:i:s') {
    $fecha_actual = date($format);
    $d = DateTime::createFromFormat($format, $date);
    if ($d->format($format) < $fecha_actual) return false;
    return $d && $d->format($format) == $date;
  }

  public function obtener_id_comite () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $resp = $this->publicaciones_model->obtener_id_comite();
    };
    echo json_encode($resp);
  }
  public function listar_solicitudes_por_comite () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $persona = $_SESSION["persona"];
      $comite =  $this->input->post("id_comite");
      $result = $this->publicaciones_model->listar_solicitudes_por_comite($comite);
      $resp = [];
      $btn_aceptar = '<span title="Aceptar" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn btn-default aceptar" style="color:#5cb85c"></span>';
      $btn_rechazar = '<span title="Rechazar" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn btn-default rechazar" style="color:#d9534f"></span>';
      $btn_ver = '<span style="background-color: #EABD32;color: white; width: 100%;" class="pointer form-control btn_ver" >Ver</span>';
      $btn_invalido = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
      

      foreach ($result as $row) {
        if($row['id_usuario_registra'] == $persona && $row['estados_sols'] == 'Aprob_Cons_Acad' || $row['estados_sols'] == 'Neg_Cons_Acad'){
          $row['acciones'] = $btn_invalido;
          $row['ver'] = "$btn_ver";
          $row['page'] = $this->ruta_modulo;
        }else if($row['id_usuario_registra'] != $persona || !$row['id_usuario_registra'] && $row['estados_sols'] != 'Aprob_Cons_Acad' || $row['estados_sols'] != 'Neg_Cons_Acad'){
          $row['acciones'] = "$btn_aceptar $btn_rechazar";
          $row['ver'] = "$btn_ver";
          $row['page'] = $this->ruta_modulo;
        }
        array_push($resp, $row);
      }
    };
    echo json_encode($resp);
  }

  public function guardar_respuesta_consejo_acad () {
     if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $estado =  $this->input->post("estado");
      $id_solicitud =  $this->input->post("id_solicitud");
      $miembros = $this->publicaciones_model->miembros_comite();
      $persona = $_SESSION["persona"];
      $cant_miem = count($miembros);
      $permiso = 0;
      $aprueba = 0;
      $deniega = 0;
      foreach ($miembros as $miembro) {
        if($miembro['id_persona'] == $persona){
          $permiso = 1;
        }
      }

      $data = [
        "id_publicacion" => $id_solicitud,
        "id_estado" => $estado,
        "id_usuario_registra" => $_SESSION['persona']
      ];

      $add = $this->pages_model->guardar_datos($data, 'publicaciones_estados');

      if($add != -1) {
        $resp = ['mensaje' => "Se ha guardado su respuesta satisfactoriamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        foreach ($miembros as $miembro) {
          $respuestas = $this->publicaciones_model->obtener_respuesta_por_miembro($miembro['id_persona'], $id_solicitud);
          foreach ($respuestas as $respuesta) {
            if($respuesta['id_estado'] == 'Aprob_Cons_Acad'){
              $aprueba += 1;
            }else if($respuesta['id_estado'] == 'Neg_Cons_Acad'){
              $deniega += 1;
            }
          }
        };
        if($aprueba > ($cant_miem / 2) || $deniega > ($cant_miem / 2)){
          $res = ($aprueba > $deniega) ? "Aprob_Cons_Acad" : "Neg_Cons_Acad";
          $mod = $this->publicaciones_model->modificar_datos(['id_estado' => $res], "publicaciones_solicitudes", $id_solicitud);
          if($mod != 0)  $resp = ['mensaje' => "Error al almacenar la información , por favor contacte al administrador!", 'tipo' => "warning", 'titulo' => "Error.!"];
        }
      }else{
        $resp = ['mensaje' => "Error al almacenar la información, por favor contacte al administrador!", 'tipo' => "warning", 'titulo' => "Error.!"];
      }
    }
    echo json_encode($resp);
  }

  public function obtener_resultado_comite () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_comite =  $this->input->post("id_comite");
      $resp = $this->publicaciones_model->obtener_resultado_comite($id_comite);
    };
    echo json_encode($resp);
  }

  public function enviar_solicitud (){
    //$resp = [];
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud =  $this->input->post("id_solicitud");
      if($id_solicitud){
        $validacion = $this->publicaciones_model->obtener_info_solicitudes($id_solicitud);
        foreach ($validacion as $data) {
          if(!$data['autores']){
            $resp =  ['mensaje' => "No se ha encontrado autores asociados a esta solicitud, por favor agreguelos e intente de nuevo", 'tipo' => "warning", 'titulo' => "Oops!"];
            echo json_encode($resp);
            return false;
          }else if(($data['first_porcentage'] == null) || ($data['second_porcentage'] == null) || ($data['third_porcentage'] == null)){
            $resp =  ['mensaje' => "Uno o mas de los autores, falta por asignación de porcentaje, por favor verifique e intente de nuevo.", 'tipo' => "warning", 'titulo' => "Oops!"];
            echo json_encode($resp);
            return false;
          }else if(!$data['info_articulo']){
            $resp =  ['mensaje' => "No se ha encontrado información del articulo asociada a esta solicitud, por favor verifique e intente de nuevo", 'tipo' => "warning", 'titulo' => "Oops!"];
            echo json_encode($resp);
            return false;
          }else if(!$data['otros_aspectos']){
            $resp =  ['mensaje' => "No se ha encontrado información de otros aspectos asociada a esta solicitud, por favor verifique e intente de nuevo", 'tipo' => "warning", 'titulo' => "Oops!"];
            echo json_encode($resp);
            return false;
          }
          // else if(!$data['bonificaciones_evidencias']){
          //   $resp =  ['mensaje' => "No se ha encontrado evidencias asociadas a esta solicitud, por favor verifique e intente de nuevo", 'tipo' => "warning", 'titulo' => "Oops!"];
          //   echo json_encode($resp);
          //   return false;
          // }
        }
        $data = [
        "id_publicacion" => $id_solicitud,
        // "id_estado" => 'Bon_Sol_Regis',
        "id_estado" => 'Bon_Sol_Env',
        "id_usuario_registra" => $_SESSION['persona']
        ];
        $add = $this->pages_model->guardar_datos($data, 'publicaciones_estados');
        if($add != -1) {
          $modificar = $this->publicaciones_model->modificar_datos([
            // "id_estado" => 'Bon_Sol_Regis'
            "id_estado" => 'Bon_Sol_Env' //Se agrega solo para los gestores
          ],'publicaciones_solicitudes',$id_solicitud);
        };
        if($modificar  != -1) $resp = ['mensaje' => "Se ha registrado su solicitud satisfactoriamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        else $resp = ['mensaje' => "Ha ocurrido un error al enviar la solicitud, por favor contacte al administrador", 'tipo' => "error", 'titulo' => "Oops!"];
      } else{
        $resp = ['mensaje' => "No se ha podido obtener información necesaria para almacenar la información, por favor contacte al administrador", 'tipo' => "warning", 'titulo' => "Oops!"];
      }
    };
    echo json_encode($resp);
  }

  public function enviar_comite () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id =  $this->input->post("datos");
      $modificar = $this->publicaciones_model->modificar_datos(["id_estado_comite" => 'Com_Not'],'comites',$id);
      if($modificar  != -1) $resp = ['mensaje' => "Se ha enviado su solicitud satisfactoriamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      else $resp = ['mensaje' => "Ha ocurrido un error al enviar la solicitud, por favor contacte al administrador", 'tipo' => "error", 'titulo' => "Oops!"];
    };
    echo json_encode($resp);
  }

  public function terminar_comite () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id =  $this->input->post("datos");
      $modificar = $this->publicaciones_model->modificar_datos(["id_estado_comite" => 'Com_Ter'],'comites',$id);
      if($modificar  != -1) $resp = ['mensaje' => "Se ha finalizado el comité satisfactoriamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      else $resp = ['mensaje' => "Ha ocurrido un error al finalizar el comité, por favor contacte al administrador", 'tipo' => "error", 'titulo' => "Oops!"];
    };
    echo json_encode($resp);
  }

  public function buscar_pais () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $valor_buscado =  $this->input->post("valor_buscado");
      $resp = $this->publicaciones_model->buscar_pais($valor_buscado);
    }
    echo json_encode($resp);
  }

  public function obtener_sublineas_inv () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id =  $this->input->post("id");
      $resp = $this->publicaciones_model->obtener_sublineas_inv($id);
    }
    echo json_encode($resp);
  }
  
  public function guardar_links_aut () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud = $this->input->post("id_solicitud");
      $url_cvlac = $this->input->post("url_cvlac");
      $url_google_scholar = $this->input->post("url_google_scholar");
      $url_research_gate = $this->input->post("url_research_gate");
      $url_red_acad_disc = $this->input->post("url_red_acad_disc");
      $url_mendeley = $this->input->post("url_mendeley");
      $url_Gruplac = $this->input->post("url_Gruplac");
      $url_Publons = $this->input->post("url_Publons");
      $categoria_minciencias__bon = $this->input->post("categoria_minciencias__bon");
      $departamento_autor__bon = $this->input->post("departamento_autor__bon");
      $hindex_scholar__bon = $this->input->post("hindex_scholar__bon");
      $hindex_scopus__bon = $this->input->post("hindex_scopus__bon");
      $ResearchGate__bon = $this->input->post("ResearchGate__bon");
      $id_orcid = $this->input->post("id_orcid");
      $id_persona = $this->input->post("id_persona");
      
      if(!$id_orcid){
        $resp = ['mensaje' => "El campo ORCID es obligatorio.", 'tipo' => "info", 'titulo' => "Oops..!"];
        echo json_encode($resp);
        return false;
      }
      $validar = $this->verificar_campos_numericos(['H-Index Scholar' => $hindex_scholar__bon, 'H-Index Scopus' => $hindex_scopus__bon, 'Research Gate' => $ResearchGate__bon]);
      if(is_array($validar)){
        $resp = ['mensaje' => "Debe diligenciar el campo {$validar['field']} y este debe ser numerico", 'tipo' => "info", 'titulo' => "Oops..!"];
      }else{
        $data = [
          "url_cvlac" => $url_cvlac ?  $url_cvlac : NULL,
          "url_google_scholar" => $url_google_scholar ?  $url_google_scholar : NULL,
          "url_research_gate" => $url_research_gate ?  $url_research_gate : NULL,
          "url_red_acad_disc" => $url_red_acad_disc ?  $url_red_acad_disc : NULL,
          "url_mendeley" => $url_mendeley ?  $url_mendeley : NULL,
          "categ_minciencias" => $url_mendeley ?  $categoria_minciencias__bon : NULL,
          "departamento" => $url_mendeley ?  $departamento_autor__bon : NULL,
          "hi_scholar" => $url_mendeley ?  $hindex_scholar__bon : NULL,
          "hi_Scopus" => $url_mendeley ?  $hindex_scopus__bon : NULL,
          "research_gate" => $url_mendeley ?  $ResearchGate__bon : NULL,
          "publons" => $url_Publons ?  $url_Publons : NULL,
          "gruplac" => $url_Gruplac ?  $url_Gruplac : NULL,
          "orcid" => $id_orcid,
        ];
  
        $id = $this->publicaciones_model->obtenerAutoresCreados($id_persona, $id_solicitud, 1, 'profesor');
        if($id){
          $modificar = $this->pages_model->modificar_datos($data,'bonificaciones_informacion_autores',$id->{'id'}, 'id_bonificaciones_autores');
        }
  
        if($modificar  != -1) $resp = ['mensaje' => "Se han almacenado los links de forma exitosa", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        else $resp = ['mensaje' => "Ha ocurrido un error al almacenar los links, por favor contacte al administrador", 'tipo' => "error", 'titulo' => "Oops!"];
      }
    };
    echo json_encode($resp);
  }
  public function guardar_guardar_instituciones () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $name_inst = $this->input->post("name_inst");
      $name_country = $this->input->post("name_country");

      $data = [
        'idparametro' => 288,
        'valor' => $name_inst,
        'valory' => $name_country,
      ];

      $add = $this->pages_model->guardar_datos($data, 'valor_parametro');
      if($add != -1) {
        $resp = ['mensaje' => "Se han almacenado la información de forma exitosa", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      }else{
        $resp = ['mensaje' => "No se ha podido obtener información necesaria para almacenar la información, por favor contacte al administrador", 'tipo' => "warning", 'titulo' => "Oops!"];
      }
    };
    echo json_encode($resp);
  }

  public function obtener_links_aut () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud =  $this->input->post("id_solicitud");
      $id_persona =  $this->input->post("id_persona_afil_inst");
      $resp = $this->publicaciones_model->obtener_links_aut($id_persona, $id_solicitud, 'profesor');
    }
    echo json_encode($resp);
  }

  public function verificar_identificacion () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $identificacion =  $this->input->post("identificacion");
      $resp = $this->publicaciones_model->verificar_identificacion($identificacion);
    }
    echo json_encode($resp);
  }

  public function listar_personas(){
    if (!$this->Super_estado){
      $res = array();
    }else {
      $buscar = $this->input->post("texto");
      //$res = array();
      if (!empty($buscar)){
        $res = $this->publicaciones_model->listar_personas($buscar);
      }else{
        $res = [];
      }
		}
		echo json_encode($res);
  }

  public function pintar_respuestas_ot_as () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud =  $this->input->post("id_solicitud");
      $id_persona =  $this->input->post("id_persona_afil_inst");
      $resp = $this->publicaciones_model->pintar_respuestas_ot_as($id_persona, $id_solicitud, 'profesor');
    }
    echo json_encode($resp);
  }
  
  public function pintar_evidencias_existentes () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud =  $this->input->post("id_solicitud");
      $id_persona =  $this->input->post("id_persona_afil_inst");
      $resp = $this->publicaciones_model->pintar_evidencias_existentes($id_persona, $id_solicitud, 'profesor');
    }
    echo json_encode($resp);
  }

  public function listar_paises () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $resp = $this->publicaciones_model->listar_paises();
    }
    echo json_encode($resp);
  }
  
  public function listar_respuestas_otros_aspectos () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud =  $this->input->post("id_solicitud");
      $id_sol = $this->publicaciones_model->obtenerIDSolicitud_bon($id_solicitud);
      $resp = $id_sol ? $this->publicaciones_model->listar_respuestas_otros_aspectos($id_sol[0]['id']) : [];
    }
    echo json_encode($resp);
  }

  public function pintar_evidencias_ver () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud =  $this->input->post("id_solicitud");
      $id_sol = $this->publicaciones_model->obtenerIDSolicitud_bon($id_solicitud);
      $resp = $id_sol ? $this->publicaciones_model->pintar_evidencias_ver($id_sol[0]['id']) : [];
    }
    echo json_encode($resp);
  }

  public function obtener_informacion_principal () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud =  $this->input->post("id_solicitud");
      $resp = $this->publicaciones_model->obtener_informacion_principal($id_solicitud);
    }
    echo json_encode($resp);
  }

  public function obtener_tipos_escrituras () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $id_solicitud =  $this->input->post("id_solicitud");
      $id_sol = $this->publicaciones_model->obtenerIDSolicitud_bon($id_solicitud);
      if($id_sol){
        $resp = $this->publicaciones_model->obtener_tipos_escrituras($id_sol[0]['id']);
      }else{
        echo json_encode('');
        return null;
      }
    }
    echo json_encode($resp);
  }

  public function obtener_articulos_cumplidos () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $bonificacion =  $this->input->post("id_solicitud");
      $autor = $this->input->post("autor");
      $resp = $this->publicaciones_model->obtener_articulos_cumplidos($autor, $bonificacion);
    }
    echo json_encode($resp);
  }

  public function pintar_porcentajes_totales () {
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $bonificacion =  $this->input->post("id_solicitud");
      $resp = $this->publicaciones_model->pintar_porcentajes_totales($bonificacion);
    }
    echo json_encode($resp);
  }

  public function listar_tipos_solicitud(){
    if (!$this->Super_estado) {
      $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    }else{
      $persona = $this->input->post('persona');
      $resp = (isset($persona) && !empty($persona))  ? $this->publicaciones_model->listar_tipos_solicitud($persona) : [];
    }
		echo json_encode($resp);
	}

  public function asignar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_agrega) {
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			$check = $this->publicaciones_model->validar_asignacion_actividad($actividad, $persona);
			if ($check) {
				$data = [
          'id_actividad' => $actividad, 
          'id_persona' => $persona, 
          'id_persona_registra' => $_SESSION['persona']
        ];
				$resp = $this->pages_model->guardar_datos($data, 'bonificaciones_actividades_personas');
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

  public function listar_estados_adm(){
		if (!$this->Super_estado) $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$actividad = $this->input->post('actividad');
			$data = $this->publicaciones_model->listar_estados_adm($actividad);
		}
		echo json_encode($data);
	}

  public function listar_departamentos_adm(){
		if (!$this->Super_estado) $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$actividad = $this->input->post('actividad');
			$data = $this->publicaciones_model->listar_departamentos_adm($actividad);
		}
		echo json_encode($data);
	}

  public function quitar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_modifica) {
			$id = $this->input->post('asignado');
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			// Verifico si actividad ya está asignada o no. Esta función retorna 0 si no está asignada la actividad y 1 si lo está.
			$check = $this->publicaciones_model->validar_asignacion_actividad($actividad, $persona);
			if (!$check) {
				$resp = $this->publicaciones_model->quitar_actividad($id);
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

  public function asignar_estado(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$check = $this->publicaciones_model->validar_asignacion_estado($estado, $actividad, $persona);
				if ($check) {
					$data = [
            'estado_id' => $estado,
						'actividad_id' => $actividad,
						'usuario_registra' => $_SESSION['persona']
					];
					$resp = $this->pages_model->guardar_datos($data, 'bonificaciones_estados_actividades');
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
				$check = $this->publicaciones_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$check) {
					$resp = $this->publicaciones_model->quitar_estado($id);
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

  public function quitar_departamento(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_modifica) {
			$actividad = $this->input->post('actividad');
			$persona = $this->input->post('persona');
      $estado = $this->input->post('estado');
      $id = $this->input->post('id');
			// Verifico si actividad ya está asignada o no. Esta función retorna 0 si no está asignada la actividad y 1 si lo está.
			$ok = $this->publicaciones_model->validar_asignacion_departamento($estado, $actividad);
			if (!$ok) {
				$resp = $this->publicaciones_model->quitar_departamento($id);
				if ($resp) {
					$res = $resp ? [
						'mensaje' => "Departamento Desasignada exitosamente.",
						'tipo' => "success",
						'titulo' => "Proceso Exitoso!"
					] : [
						'mensaje' => "Ha ocurrido un error al desasignar el departamento.",
						'tipo' => "info",
						'titulo' => "Ooops!"
					];
				} else $res = [
					'mensaje' => "Ha ocurrido un error al desasignar el departamento.",
					'tipo' => "info",
					'titulo' => "Ooops!"
				];
			} else $res = [
				'mensaje' => "El usuario no tiene asignada este departamento.",
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

  public function activar_notificacion(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->admin || $this->admin_th) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->publicaciones_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$id = $this->publicaciones_model->get_where('bonificaciones_estados_actividades', ['actividad_id' => $actividad, 'estado_id' => $estado])->row()->id;
					$resp = $this->publicaciones_model->modificar_datos(['notificacion' => 1], 'bonificaciones_estados_actividades', $id);
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
			if ($this->admin || $this->admin_th) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->publicaciones_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$id = $this->publicaciones_model->get_where('bonificaciones_estados_actividades', ['actividad_id' => $actividad, 'estado_id' => $estado])->row()->id;
					$resp = $this->publicaciones_model->modificar_datos(['notificacion' => 0], 'bonificaciones_estados_actividades', $id);
					$res = !$resp
						? ['mensaje'=>"Estado Desasignada exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						: ['mensaje'=>"Ha ocurrido un error al desasignar el estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
				} else $res = ['mensaje'=>"El usuario no tiene asignado este estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
			}else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		}
		echo json_encode($res);
	}

  public function obtener_datos_solicitud () {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id = $this->input->post('id');
			$resp = $this->publicaciones_model->obtener_datos_solicitud($id);	
    };
    echo json_encode($resp);
  }

  public function obtener_autores () {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id = $this->input->post('id_solicitud');
      $afiliacion = $this->input->post('afiliacion');
			$resp = $this->publicaciones_model->obtener_autores($id, $afiliacion);	
    };
    echo json_encode($resp);
  }

  public function obtener_autores_internacionales () {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id = $this->input->post('id_solicitud');
			$resp = $this->publicaciones_model->obtener_autores_internacionales($id);	
    };
    echo json_encode($resp);
  }

  public function traer_correspondencia () {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id = $this->input->post('id_solicitud');
			$resp = $this->publicaciones_model->traer_correspondencia($id);	
    };
    echo json_encode($resp);
  }

  public function consultas_liquidaciones () {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $valory = $this->input->post('valory');
      $id_bonificacion = $this->input->post('id_solicitud');
			$resp = $this->publicaciones_model->consultas_liquidaciones($valory, $id_bonificacion);	
    };
    echo json_encode($resp);
  }

  public function obtener_autores_liquidacion () {
    $resp = [];
    $row = [];
    $suma = 0;
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_bonificacion = $this->input->post('id_solicitud');
			$data = $this->publicaciones_model->obtener_autores_liquidacion($id_bonificacion);
      foreach ($data as $row) {
        $info_final = $this->publicaciones_model->obtener_data_liq_final($row['id_autor'], $id_bonificacion);

        $liquidacion = $this->publicaciones_model->obtener_info_autor_liq($id_bonificacion, $row['id_autor'], $row['identificacion'], 'profesor');
        if(!$liquidacion) {
          $resp = ['mensaje'=>"No se ha podido encontrar una liquidación para " .$row['nombre_completo'],'tipo'=>"info",'titulo'=> "Ooops!"];
        }else{
          $suma += $liquidacion[0]['total_autor'];
          $row['liquidacion'] = "$".number_format($liquidacion[0]['total_autor']);
          $row['ver'] = '<span style="background-color: white;color: black; width: 100%;" class="pointer form-control ver" ><span >ver</span></span>';
          array_push($resp, $row);
        }
      };
    };
    echo json_encode([$resp,$suma]);
  }

  public function obtener_personas_liquidacion () {
    $resp = [];
    $row = [];
    $suma = 0;
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_bonificacion = $this->input->post('id_solicitud');
      $id_estado = $this->input->post('id_estado');
      $tipo = ($id_estado == 'Bon_Sol_Aprob_Direct_Pub') ? 'director' :'gestor';
			$data = $this->publicaciones_model->obtener_personas_liquidacion($id_bonificacion, $id_estado);
      foreach ($data as $row) {
        $liquidacion = $this->publicaciones_model->obtener_info_gestor_liq($id_bonificacion, $row['id'], $row['identificacion'], $tipo);
        if(!$liquidacion) {
          $resp = ['mensaje'=>"No se ha podido encontrar una liquidación para " .$row['nombre_completo'],'tipo'=>"info",'titulo'=> "Ooops!"];
        }else{
          $suma += $liquidacion[0]['total_autor'];
          $row['liquidacion'] = "$".number_format($liquidacion[0]['total_autor']);
          $row['ver'] = '<span style="background-color: white;color: black; width: 100%;" class="pointer form-control ver" ><span >ver</span></span>';
          array_push($resp, $row);
        }
      };
    };
    echo json_encode([$resp,$suma]);
  }

  public function obtener_director_liquidacion () {
    $resp = [];
    $row = [];
     $suma = 0;
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_bonificacion = $this->input->post('id_solicitud');
			$data = $this->publicaciones_model->obtener_director_liquidacion($id_bonificacion);
      foreach ($data as $row) {
        $liquidacion = $this->publicaciones_model->obtener_info_gestor_liq($id_bonificacion, $row['id'], $row['identificacion'], $tipo='director');
        if(!$liquidacion) {
          $resp = ['mensaje'=>"No se ha podido encontrar una liquidación para " .$row['nombre_completo'],'tipo'=>"info",'titulo'=> "Ooops!"];
        }else{
          $suma += $liquidacion[0]['total_autor'];
          $row['liquidacion'] = "$".number_format($liquidacion[0]['total_autor']);
          $row['ver'] = '<span style="background-color: white;color: black; width: 100%;" class="pointer form-control ver" ><span >ver</span></span>';
          array_push($resp, $row);
        }
      };
    };
    echo json_encode([$resp,$suma]);
  }

  public function obtener_info_autor_liq () {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_bonificacion = $this->input->post('id_solicitud');
      $id_persona = $this->input->post('id_persona');
      $identificacion = $this->input->post('identificacion');
      $tipo = $this->input->post('tipo');
			$resp = $this->publicaciones_model->obtener_info_autor_liq($id_bonificacion, $id_persona, $identificacion, $tipo);	
    };
    echo json_encode($resp);
  }

  public function guardar_info_liquid_final () {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_solicitud = $this->input->post('id_solicitud');
      $cuar_liq_final = $this->input->post('cuar_liq_final');
      $cat_liq_final = $this->input->post('cat_liq_final');

      
      $data = [
        'cuartil_final' => $cuar_liq_final,
        'categoria_final' => $cat_liq_final,
      ];
      $id = $this->publicaciones_model->obtenerIDSolicitud_bon($id_solicitud)[0]['id'];
      $existe = $this->publicaciones_model->validar_existencia_datos('bonificaciones_solicitudes bs',
      $where="id = $id AND bs.cuartil_final = $cuar_liq_final AND bs.categoria_final = $cat_liq_final");
      if($existe == 0){
        $mod = $this->publicaciones_model->modificar_datos($data, "bonificaciones_solicitudes", $id);
        if ($mod != 0) {
          $resp = ['mensaje' => "Error al almacenar la información, contacte con el administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
        }else{
          $resp = ['mensaje' => "Datos actualizados con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        }
      }else if($existe != 0 ){
        $resp = ['mensaje' => "Información ya existe", 'tipo' => "info", 'titulo' => "Atención.!"];
      }
    };
    echo json_encode($resp);
  }

  public function liquidar_bonificacion () {
    $mod = [];
    $row = [];
    $sol_problema = [];
    $visibilidad = [];
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_solicitud = $this->input->post('id_solicitud');
      $autores = $this->publicaciones_model->obtener_autores_liquidacion($id_solicitud);
      $liq_existentes = $this->publicaciones_model->obtener_liquidacion_por_tipo($id_solicitud, 'profesor');

      foreach($liq_existentes as $existentes){
        $mod = $this->publicaciones_model->modificar_datos(["estado" => 0], "bonificaciones_liquidacion_solicitud", $existentes['id']);
      }
      if ($mod && $mod != 0) {
        $resp = ['mensaje' => "Error al reliquidar solicitud, contacte con el administrador", 'tipo' => "error", "Oops.!"];
        echo json_encode($resp);
        return false;
      }
      foreach ($autores as $autor) {

        $info_final = $this->publicaciones_model->obtener_data_liq_final($autor['id_autor'], $id_solicitud);
        // Se obtiene la liquidación de cada persona
        if(!$info_final->{'categ_minciencias'} || !$info_final->{'categoria_final'} || !$info_final->{'cuartil_final'}){
          $resp = ['mensaje' => "No se encontró información necesaria para liquidar, por favor valide e intente de nuevo", 'tipo' => "error", 'titulo' => "Oops.!"];
          echo json_encode($resp);
          return false;
        }
        $base_liquidacion = $this->publicaciones_model->obtener_valor_bonificacion_autores($info_final->{'categ_minciencias'}, $info_final->{'categoria_final'}, $info_final->{'cuartil_final'});
        // Se valida la existencia de la base
        if(!$base_liquidacion){
          $resp = ['mensaje' => "No se ha encontrado una base de liquidación, por favor valide e intente de nuevo", 'tipo' => "error", 'titulo' => "Oops.!"];
          echo json_encode($resp);
          return false;
        }
        // Se obtiene la liquidacion base (teniendo en cuenta el porcentaje asignado)
        $bonificacion_base = (($base_liquidacion->{'valor'} * $autor['third_porcentage']) / 100);
        // Se valida si existen estudiantes en la solicitud 
        $existe_estudiante = $this->publicaciones_model->obtener_autores($id_solicitud, 'estudiante');
        // Se consulta respuestas visibilidad institucional
        $aporte_visibilidad = $this->publicaciones_model->consultas_liquidaciones('visibilidad_inst', $id_solicitud);
        // Se consulta respuestas de Solucion problematica local
        $solucion_problema = $this->publicaciones_model->consultas_liquidaciones('solucion_prob', $id_solicitud);

        // Se verifica si existe aprobación para visibilidad
        foreach ($aporte_visibilidad as $ap_vis) {
          if($ap_vis['respuesta'] == 'Aprobado' && $ap_vis['tipo_gestion'] == 'Direct_Public'){
            $visibilidad = 1;
          }else if($ap_vis['respuesta'] == 'Aprobado' && $ap_vis['tipo_gestion'] == 'Direct_Public'){
            $visibilidad = 0;
          }
        }
        foreach ($solucion_problema as $v_prob) {
          if($v_prob['respuesta'] == 'Aprobado' && $v_prob['tipo_gestion'] == 'Direct_Public'){
            $sol_problema = 1;
          }else if($v_prob['respuesta'] == 'Aprobado' && $v_prob['tipo_gestion'] == 'Direct_Public'){
            $sol_problema = 0;
          }
        }
        $coautoria_estudiante = (!$existe_estudiante) ? 0 : ((223923 * $autor['third_porcentage']) / 100);
        $visibilidad = (!$visibilidad) ? 0 : ((363410 * $autor['third_porcentage']) / 100);
        $solucion_problema = (!$sol_problema) ? 0 : ((363410 * $autor['third_porcentage']) / 100);
        array_push($row, 
        [
          'id_persona' => $autor['id_autor'],
          'id_bonificacion' => $id_solicitud,
          'tipo' => 'profesor',
          'base_liquidacion' => $base_liquidacion->{'valor'},
          'bonificacion_base' => $bonificacion_base,
          'coautoria_estudiante' => $coautoria_estudiante,
          'visibilidad' => $visibilidad,
          'solucion_problema' => $solucion_problema,
          'total_autor' => ($bonificacion_base + $coautoria_estudiante + $visibilidad + $solucion_problema),
          'id_persona_registra' => $_SESSION['persona'],
        ]);
      }
      $add = $this->publicaciones_model->guardar_datos($row, "bonificaciones_liquidacion_solicitud",2);

      if ($add != 0) { $resp = ['mensaje' => "Error al guardar liquidación, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $resp = ['mensaje' => "Liquidación generada y almacenada con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      };
    };
    echo json_encode($resp);
  }

  public function liquidar_bonificacion_gestor () {
    $mod = [];
    $row = [];
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_solicitud = $this->input->post('id_solicitud');
      $id_estado = 'Bon_Sol_Gest_Aprob';
      $autores = $this->publicaciones_model->obtener_personas_liquidacion($id_solicitud, $id_estado);
      $cantidad_autores = count($autores);
      $liq_existentes = $this->publicaciones_model->obtener_liquidacion_por_tipo($id_solicitud, 'gestor');

      foreach($liq_existentes as $existentes){
        $mod = $this->publicaciones_model->modificar_datos(["estado" => 0], "bonificaciones_liquidacion_solicitud", $existentes['id']);
      }
      if ($mod != 0 && $liq_existentes) {
        $resp = ['mensaje' => "Error al reliquidar solicitud, contacte con el administrador", 'tipo' => "error", "Oops.!"];
        echo json_encode($resp);
        return false;
      }

      if (!$autores) {
        $resp = ['mensaje' => "No se han encontrado personas a mostrar.", 'tipo' => "error", "Oops.!"];
        echo json_encode($resp);
        return false;
      }
      foreach ($autores as $autor) {
        $info_final = $this->publicaciones_model->obtener_data_liq_final($autor['id_autor'], $id_solicitud);

        $data = $this->publicaciones_model->validar_viabilidad_bonificacion($id_solicitud);
        if($data->{'corresponding_author'} && $data->{'idioma'} && $data->{'pais'}){
          $categoria = $this->publicaciones_model->obtener_categoria_liquidacion('Bon_Liq_Y_Ges', $tipo = 'Gestores');
        }else if($data->{'corresponding_author'} || $data->{'idioma'} || $data->{'pais'}){
          $categoria = $this->publicaciones_model->obtener_categoria_liquidacion('Bon_Liq_O_Ges', $tipo = 'Gestores');
        }else if(!$data->{'corresponding_author'} && !$data->{'idioma'} && !$data->{'pais'}){
          $categoria = $this->publicaciones_model->obtener_categoria_liquidacion('Bon_Liq_Bas_Ges', $tipo = 'Gestores');
        }

        $base_liquidacion = $this->publicaciones_model->obtener_valor_bonificacion($tipo = 'gestor', $info_final->{'cuartil_final'}, $categoria->{'id'});
        if(!$base_liquidacion){
          $resp = ['mensaje' => "Error al obtener la información de liquidación, por favor contacte al administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
          echo json_encode($resp);
          return false;
        }

        $bonificacion_base = (($base_liquidacion->{'valor'}) / $cantidad_autores );
        array_push($row, 
        [
          'id_persona' => $autor['id_autor'],
          'id_bonificacion' => $id_solicitud,
          'tipo' => 'gestor',
          'base_liquidacion' => $base_liquidacion->{'valor'},
          'bonificacion_base' => $bonificacion_base,
          'total_autor' => $bonificacion_base,
          'id_persona_registra' => $_SESSION['persona'],
        ]);
      }
      $add = $this->publicaciones_model->guardar_datos($row, "bonificaciones_liquidacion_solicitud",2);

      if ($add != 0) { $resp = ['mensaje' => "Error al guardar liquidación, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $resp = ['mensaje' => "Liquidación generada y almacenada con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      };
    };
    echo json_encode($resp);
  }
  public function liquidar_bonificacion_director () {
    $row = [];
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $mod = [];
      $id_solicitud = $this->input->post('id_solicitud');
      $id_estado = 'Bon_Sol_Aprob_Direct_Pub';
      $autores = $this->publicaciones_model->obtener_personas_liquidacion($id_solicitud, $id_estado);
      $cantidad_autores = count($autores);
      $liq_existentes = $this->publicaciones_model->obtener_liquidacion_por_tipo($id_solicitud, 'director');

      foreach($liq_existentes as $existentes){
        $mod = $this->publicaciones_model->modificar_datos(["estado" => 0], "bonificaciones_liquidacion_solicitud", $existentes['id']);
      }
      if ($mod != 0 && $liq_existentes) {
        $resp = ['mensaje' => "Error al reliquidar solicitud, contacte con el administrador", 'tipo' => "error", "Oops.!"];
        echo json_encode($resp);
        return false;
      }

      foreach ($autores as $autor) {
        $info_final = $this->publicaciones_model->obtener_data_liq_final($autor['id_autor'], $id_solicitud);

        $data = $this->publicaciones_model->validar_viabilidad_bonificacion($id_solicitud);
        if($data->{'corresponding_author'} && $data->{'idioma'} && $data->{'pais'}){
          $categoria = $this->publicaciones_model->obtener_categoria_liquidacion('Bon_Liq_Y_Direc', $tipo = 'Directores');
        }else if(($data->{'corresponding_author'} || $data->{'idioma'}) || $data->{'pais'}){
          $categoria = $this->publicaciones_model->obtener_categoria_liquidacion('Bon_Liq_O_Direc', $tipo = 'Directores');
        }else if(!$data->{'corresponding_author'} && !$data->{'idioma'} && !$data->{'pais'}){
          $categoria = $this->publicaciones_model->obtener_categoria_liquidacion('Bon_Liq_Bas_Direc', $tipo = 'Directores');
        }

        $base_liquidacion = $this->publicaciones_model->obtener_valor_bonificacion($tipo = 'director', $info_final->{'cuartil_final'}, $categoria->{'id'});

        if(!$base_liquidacion){
          $resp = ['mensaje' => "Error al obtener la información de liquidación, por favor contacte al administrador", 'tipo' => "error", 'titulo' => "Oops.!"];
          echo json_encode($resp);
          return false;
        }

        $bonificacion_base = (($base_liquidacion->{'valor'}) / $cantidad_autores );
        array_push($row, 
        [
          'id_persona' => $autor['id_autor'],
          'id_bonificacion' => $id_solicitud,
          'tipo' => 'director',
          'base_liquidacion' => $base_liquidacion->{'valor'},
          'bonificacion_base' => $bonificacion_base,
          'total_autor' => $bonificacion_base,
          'id_persona_registra' => $_SESSION['persona'],
        ]);
      }
      $add = $this->publicaciones_model->guardar_datos($row, "bonificaciones_liquidacion_solicitud",2);

      if ($add != 0) { $resp = ['mensaje' => "Error al guardar liquidación, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
      } else {
        $resp = ['mensaje' => "Liquidación generada y almacenada con exito", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
      };
    };
    echo json_encode($resp);
  }

  public function obtener_total_liquidacion () {
    $row = [];
    $resp = [];
    $total_liquidacion = 0;
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_solicitud = $this->input->post('id_solicitud');
      $liquidacion = $this->publicaciones_model->obtener_info_autor_liq($id_solicitud, '', '', '');
      foreach ($liquidacion as $dato) {
        $total_liquidacion += $dato['total_autor'];
      }
      $resp =  $total_liquidacion;
    };
    echo json_encode($resp);
  }

  public function obtener_liquidacion_total () {
    $total_liquidacion = 0;
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_solicitud = $this->input->post('id_solicitud');
      $liquidacion = $this->publicaciones_model->obtener_liquidacion_total($id_solicitud);
      foreach ($liquidacion as $dato) {
        $total_liquidacion += $dato['total_autor'];
      }
      $resp =  $total_liquidacion;
    };
    echo json_encode($resp);
  }

  public function gestionar_comites_masivo () {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $tipo = $this->input->post('tipo');
      $id_comite = $this->input->post('id_comite');
      $comites = $this->publicaciones_model->listar_solicitudes_por_comite($id_comite, $tipo);
      $miembros = $this->publicaciones_model->miembros_comite();
      $persona = $_SESSION["persona"];
      $cant_miem = count($miembros);
      $permiso = 0;
      $aprueba = 0;
      $deniega = 0;
      foreach ($miembros as $miembro) {
        if($miembro['id_persona'] == $persona){
          $permiso = 1;
        }
      }
      if($permiso == 0){
        $resp = ['mensaje' => "Actualmente no posee permisos para realizar esta acción, por favor contacte al administrador para mayor información.", 'tipo' => "warning", 'titulo' => "Error.!"];
        echo json_encode($resp);
        return false;
      }
      if(!$comites){
        $resp = ['mensaje' => "No se han encontrado solicitudes en espera de revisión. Por favor valide e intente nuevamente", 'tipo' => "warning", 'titulo' => "Error.!"];
        echo json_encode($resp);
        return false;
      }
      foreach ($comites as $comite) {
        $data = [
          "id_publicacion" => $comite['id'],
          "id_estado" => $tipo == "Aprobar" ? "Aprob_Cons_Acad" : "Neg_Cons_Acad",
          "id_usuario_registra" => $_SESSION['persona'],
        ];

        $add = $this->pages_model->guardar_datos($data, 'publicaciones_estados');
  
        if($add != -1) {
          $resp = ['mensaje' => "Se ha guardado su respuesta satisfactoriamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          foreach ($miembros as $miembro) {
            $respuestas = $this->publicaciones_model->obtener_respuesta_por_miembro($miembro['id_persona'], $comite['id']);
            foreach ($respuestas as $respuesta) {
              if($respuesta['id_estado'] == 'Aprob_Cons_Acad'){
                $aprueba += 1;
              }else if($respuesta['id_estado'] == 'Neg_Cons_Acad'){
                $deniega += 1;
              }
            }
          };
          if($aprueba > ($cant_miem / 2) || $deniega > ($cant_miem / 2)){
            $res = ($aprueba > $deniega) ? "Aprob_Cons_Acad" : "Neg_Cons_Acad";
            $mod = $this->publicaciones_model->modificar_datos(['id_estado' => $res], "publicaciones_solicitudes", $comite['id']);
            if($mod != 0)  $resp = ['mensaje' => "Error al almacenar la información , por favor contacte al administrador!", 'tipo' => "warning", 'titulo' => "Error.!"];
          }
        }else{
          $resp = ['mensaje' => "Error al almacenar la información , por favor contacte al administrador!", 'tipo' => "warning", 'titulo' => "Error.!"];
        }

      }
    };
    echo json_encode($resp);
  }

  public function listar_cuar_liq_final (){
    $resp = [];
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_solicitud = $this->input->post('id_solicitud');
      $cuartiles = $this->publicaciones_model->listar_cuar_liq_final($id_solicitud);
      $resp = $cuartiles ? $cuartiles : [];
    };
     echo json_encode($resp);
  }

  public function listar_cat_liq_final (){
    $resp = [];
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_solicitud = $this->input->post('id_solicitud');
      $categoria = $this->publicaciones_model->listar_cat_liq_final($id_solicitud);
      $resp = $categoria ? $categoria : [];
    };
    echo json_encode($resp);
  }

  public function modificar_porcentajes_dir () {
    $resp = [];
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $data = [];
      $suma = 0;
      $existe = 0;
      $datos = $this->input->post('datos');
      $id = $this->input->post('id_articulo');
      $porcentajes_almac =  $this->publicaciones_model->obtener_suma_porcentajes($id, "profesor");
    
      $add = 0;
      $ult_est = $this->publicaciones_model->obtener_ultimo_estado($id);
      $result = $this->tiene_permisos($_SESSION["persona"], $ult_est->{'id_estado'});
      if($result != 1){
        echo json_encode($result);
        return false;
      }

      foreach ($datos as $dato) {
        foreach ($porcentajes_almac as $porcentaje) {
          if(count($porcentaje) != 0){
            $suma += $porcentaje['porcentaje'];
            if($porcentaje['id_persona'] == $dato['data']['usuario']) $existe = 1;
          }
        }
        if(((intval($suma) + intval($dato['data']['first_porcentage_cp'])) > 100) && $existe == 0 ){
          $resp = ['mensaje' => "El porcentaje que intenta asignar es mayor a la suma de los porcentajes asignados anteriormente", 'tipo' => "warning", 'titulo' => "Oops.!"];
        }else{
          $id_bon = $this->publicaciones_model->getID($dato['data']['usuario'], $id, 'profesor');
          $data = [
            'first_porcentage' => $dato['data']['first_porcentage_cp'],
            'second_porcentage' => $dato['data']['second_porcentage_cp'],
            'third_porcentage' => $dato['data']['third_porcentage_cp'],
          ];
          $update_porc = $this->publicaciones_model->modificar_datos($data, "bonificaciones_autores", $id_bon->{'id'});
          if ($update_porc != 0) $add = 1;
          if($add != 0) {
            $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
          } else {
            $resp = ['mensaje' => "Porcentajes almacenados con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
          }
        }
      }
    };
    echo json_encode($resp);
  }

  public function tiene_permisos ($id_persona, $id_estado) {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $resp = 0;
      $admin = $_SESSION["perfil"] == "Per_Admin" ? true : false;
      $permisos = $this->publicaciones_model->tiene_permisos($id_persona, $id_estado);
      if (!$permisos && !$admin){
        $resp = ['mensaje' => "No puede ejecutar esta acción porque no cuenta con los permisos necesarios.", 'tipo' => "error", 'titulo' => "Oops.!"];
      }else if($permisos || $admin){
        $resp = 1;
      }
      return $resp;
    };
  }

  public function consultar_info_solicitud () {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $id_solicitud = $this->input->post('id_solicitud');
      $resp = $this->publicaciones_model->consultar_info_solicitud($id_solicitud);
    };
    echo json_encode($resp);
  }

  public function consultar_notificaciones_personas () {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
      $estado = $this->input->post('estado');
      $id_estado = $this->genericas_model->obtener_valores_parametro_aux($estado, 302);
      $resp = $this->publicaciones_model->consultar_notificaciones_personas($id_estado[0]['id']);
    };
    echo json_encode($resp);
  }

  public function save_new_project () {
    if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
    else {
      $title_project = $this->input->post('title_project');
      $project_serial = $this->input->post('project_serial') ? $this->input->post('project_serial') : NULL;
      $Project_date_initial = $this->input->post('Project_date_initial');
      $Project_date_end = $this->input->post('Project_date_end') ? $this->input->post('Project_date_end') : NULL;
      $id_solicitud = $this->input->post('id_solicitud');
      if(!$title_project || !$Project_date_initial){
        $resp = ['mensaje' => "Es necesario que ingrese el nombre del proyecto y la fecha inicial del proyecto antes de continuar.", 'tipo' => "error", 'titulo' => "Oops.!"];
        echo json_encode($resp);
        return false;
      }

      $data = [
        'titulo_proyecto' => $title_project,
        'codigo_proyecto' => $project_serial,
        'fecha_i_proyecto' => $Project_date_initial,
        'fecha_f_proyecto' => $Project_date_end,
        'ubicacion_proyecto' => 'manual'
      ];

      $update = $this->publicaciones_model->modificar_datos($data, "publicaciones_solicitudes", $id_solicitud);
      if ($update != 0) {
        $resp = ['mensaje' => "Error al almacenar la información, contacte con el administrador", 'tipo' => "error", "Oops.!"];
      } else {
        $resp = ['mensaje' => "Información almacenada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'titulo_proyecto' => $title_project];
      }

    };
    echo json_encode($resp);
  }
}
?>
