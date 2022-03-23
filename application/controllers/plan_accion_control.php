<?php

use Mpdf\Tag\Em;
use Mpdf\Tag\P;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use function PHPSTORM_META\type;

class plan_accion_control extends CI_Controller
{
  private $super_estado = false;
  private $super_admin = false;
  private $module_admin = false;
  private $ruta_archivos = "archivos_adjuntos/plan_accion";
  private $formatoActivo = '';

  public function __construct()
  {
    parent::__construct();
    include('application/libraries/festivos_colombia.php');
    $this->load->model('pages_model');
    $this->load->model('plan_accion_model');
    $this->load->model('genericas_model');
    session_start();
    if (isset($_SESSION["usuario"])) {
      $this->super_estado = true;
      if ($_SESSION["perfil"] == "Per_Admin") {
        $this->super_admin = true;
      }
      if ($_SESSION["perfil"] == "Plan_Acc_Adm") {
        $this->module_admin = true;
      }
    }
  }

  public function index($id = '')
  {
    if ($this->super_estado) {
      $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'plan_accion');
      if (!empty($datos_actividad)) {
        $pages = "plan_accion";
        $data['js'] = "plan_accion";
        $data['id'] = $id;
        $data['formatoActivo'] = $this->formatoActivo;
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

  /* Guardar info - Reutilizable */
  public function save_inf($tabla = "", $datos = "",  $where = "")
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {

      if (!empty($tabla) or !empty($datos)) {
        $query = $this->plan_accion_model->save_inf($tabla, $datos, $where);
      } else {
        $query = false;
      }

      $r = $query;
    }
    return $r;
  }

  /* Actualizar info - Reutilizable */
  public function upd_inf($tabla = "", $datos = "", $where = "")
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {

      if (empty($tabla) and empty($datos) and empty($where)) {
        $datos = $this->input->post("datos");
        $tabla = $this->input->post("tabla");
        $where = $this->input->post("where");
      }
      $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);

      if (empty($query)) {
        $r = ["mensaje" => "La información, se ha actualizado con éxito!", "tipo" => "success", "titulo" => "Bien!"];
      } else {
        $r = ["mensaje" => $query . ".", "tipo" => "error", "titulo" => ""];
      }
    }
    return $r;
  }

  /* Eliminar info - Reutilizable */
  public function del_inf($tabla = "", $where = "")
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {

      if (empty($tabla) and empty($where)) {
        $tabla = $this->input->post("tabla");
        $where = $this->input->post("where");
      }
      $query = $this->plan_accion_model->del_inf($tabla, $where);

      if ($query) {
        $r = ["mensaje" => "La información, se ha eliminado con éxito!", "tipo" => "success", "titulo" => "Bien!"];
      } else {
        $r = ["mensaje" => "Error al eliminar. Error codigo: " . __LINE__ . ".", "tipo" => "error", "titulo" => ""];
      }
    }
    return $r;
  }

  /* Listar planes de accion */
  public function listar_formatos_planAccion()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idparametro = $this->plan_accion_model->find_idParametro('planAccion_formatos');
      if ($idparametro) {
        $query = $this->plan_accion_model->listar_formatos_planAccion($idparametro->idpa);
        $r = $query;
      } else {
        $r = [];
      }
    }
    exit(json_encode($r));
  }

  /* Listar formatos asignados segun persona */
  public function listarLidersAssigned()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $newdata = [];
      $btn_off = '<span style="color: #DF3C3C;" title="¡Retirar!" data-toggle="popover" data-trigger="hover" class="fa fa-user-times btn btn-default retirarLider"></span>';
      $btnAssingGroup = '<span style="color: #3BA439;" title="¡Asignar Directores!" data-toggle="popover" data-trigger="hover" class="fa fa-user-plus btn btn-default asignarPers red"></span>';
      $permisosAsigned = $this->plan_accion_model->permisosLideres();
      foreach ($permisosAsigned as $permiso) {
        $permiso['acciones'] = "$btn_off $btnAssingGroup";
        array_push($newdata, $permiso);
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }


  public function obtenerCronogramaInstitucional()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idMeta = $this->input->post('idMeta');
      $newdata = [];
      $btnSee = '<span class="fa fa-eye pointer btn btn-default red verDocsNames" title="¡Ver nombre de documentos soporte!"></span>';
      $permisosAsigned = $this->plan_accion_model->datos_cronograma($idMeta);
      //exit(json_encode($permisosAsigned));
      foreach ($permisosAsigned as $permiso) {
        $permiso['acciones'] = "$btnSee";
        if (!$permiso['cantidad']) $permiso['cantidad'] = '100%';
        $permiso['trimestreName'] .= " " . $permiso['codigo_item'];
        array_push($newdata, $permiso);
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Asignar formatos de plan de accion a usuarios */
  public function asignarFomato()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idPersona = $this->input->post('idPersona');
      $idFormato = $this->input->post('idFormato');
      if (empty($idPersona) or empty($idFormato)) {
        $r = [];
      } else {
        $nums = [
          'La identificación del formato contiene informacion no valida.' => $idFormato,
          'La identificación del responsable contiene información no valida.' => $idPersona
        ];
        $checkInts = $this->pages_model->verificar_campos_numericos($nums);
        if (is_array($checkInts)) {
          $r = ['mensaje' => $checkInts['field'], 'tipo' => 'warning', 'titulo' => '¡Atención!'];
        } else {
          $tabla = 'plan_accion_permisos_lideres';
          $tosend = [
            'id_usuario_registra' => $_SESSION['persona'],
            'id_formato' => $idFormato,
            'id_persona' => $idPersona
          ];
          $query = $this->plan_accion_model->save_inf($tabla, $tosend);
          if (empty($query)) {
            $r = ['mensaje' => 'El permiso ha sido asignado correctamente.', 'tipo' => 'success', 'titulo' => '¡Bien!'];
          } else {
            $r = ['mensaje' => $query, 'tipo' => 'error', 'titulo' => 'Oops'];
          }
        }
      }
    }
    exit(json_encode($r));
  }

  /* Remover lideres */
  public function delLider()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id = $this->input->post('idPrincial');
      $idLider = $this->input->post('liderId');

      if (empty($idLider) or empty($id)) {
        $r = [];
      } else {
        $intCheck = [
          'Error interno, por favor, contacte con el administrador del modulo' => $id,
          'No se pudo identificar al lider correctamente.' => $idLider
        ];
        $check = $this->pages_model->verificar_campos_numericos($intCheck);
        if (is_array($check)) {
          $r = ["mensaje" => $check['field'], "tipo" => "error", "titulo" => ""];
        } else {
          $tabla = 'plan_accion_permisos_lideres';
          $where = ['id' => $id, 'id_lider' => $idLider];
          $tosend = [
            'id_usuario_elimina' => $_SESSION['persona'],
            'estado' => 0
          ];
          $del = $this->plan_accion_model->upd_inf($tabla, $tosend, $where);
          if (empty($del)) {
            $tablaDir = 'plan_accion_permisos_equipos';
            $datos = ['estado' => 0, 'id_usuario_elimina' => $_SESSION['persona']];
            $where = ['id_lider' => $idLider];
            $consul = $this->plan_accion_model->upd_inf($tablaDir, $datos, $where);
            if (empty($consul)) {
              $r = ["mensaje" => "La gestión se ha realizado exitosamente.", "tipo" => "success", "titulo" => "¡Bien!"];
            } else {
              $r = ["mensaje" => $consul, "tipo" => "error", "titulo" => ""];
            }
          } else {
            $r = ["mensaje" => $del, "tipo" => "error", "titulo" => ""];
          }
        }
      }
    }
    exit(json_encode($r));
  }

  /* Listar planes de accion */
  public function listar_areas_estrategicas()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $r = [];
      $idparametro = $this->plan_accion_model->find_idParametro('areas_estrategicas');
      if (empty($idparametro)) {
        $r = [];
      } else {
        $query = $this->plan_accion_model->listar_areas_estrategicas($idparametro->idpa);
        $r = $query;
      }
    }
    exit(json_encode($r));
  }

  /* Listar solicitudes */
  public function listar_solicitudes()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $ps = $_SESSION['persona'];
      $tp = $_SESSION['perfil'];
      $administrar = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro" ? true : false;
      $superAdmin = $_SESSION["perfil"] == "Per_Admin" ? true : false;
      $query = [];
      $newdata = [];
      $idSol = '';
      $btn_ver = '<span style="background-color: white;color: black; width: 100%;" class="btn btn-default pointer form-control ver_detalles"><span>Ver</span></span>';
      $btn_aprobar = '<span title="¡Enviar!" style="color: #5cb85c;" data-toggle="popover" data-trigger="hover" class="fa fa-paper-plane btn btn-default aprobar"></span>';
      $btnApro2 = '<span title="¡Avalar!" style="color: #5cb85c;" data-toggle="popover" data-trigger="hover" class="fa fa-paper-plane-o btn btn-default aprobar2"></span>';
      $btnApro3 = '<span title="¡Aprobado Planeación!" style="color: #5cb85c;" data-toggle="popover" data-trigger="hover" class="fa fa-paper-plane-o btn btn-default aprobar3"></span>';
      $btn_reprobar = '<span title="Devolver" style="color: #d9534f;" data-toggle="popover" data-trigger="hover" class="fa fa-arrow-left btn btn-default corregir"></span>';
      $btn_sin_accion = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
      $btn_del = '<span data-id="btnAccion" title="¡Cancelar!" data-toggle="popover" data-trigger="hover" class="btn fa fa-remove pointer btn btn-default del_meta" style="color: #F25C74;"></span>';

      $formatoActivo = $this->plan_accion_model->formatoActivo(); //Buscamos el formato activo de la persona

      $fi = $this->plan_accion_model->find_idParametro('formato_institucional'); //Formato institucional inf
      $fp = $this->plan_accion_model->find_idParametro('formato_programa'); //Formato programa inf
      $hoy = date('Y-m-d H:i:s');

      $fa = "";

      if ($tp == "Plan_Accion_Admin") {
        $fa = $fi->id;
      } else if ($tp == "Plan_Accion_Admin_Pro") {
        $fa = $fp->id;
      }

      //exit(json_encode($formatoActivo));

      $query = $this->plan_accion_model->listar_solicitudes('', $fa);

      foreach ($query as $row) {
        $idSol = $row['id'];
        $idPermiso = $row['idPermiso'];
        $idusuarioregistra = $row['id_usuario_registra'];

        //Btns que requieren indexarle info que se genera dentro del bucle
        $btnAprobar = '<span id="' . $idSol . '" data-id="' . $idSol . '" title="¡Gestionar meta!" data-toggle="popover" data-trigger="hover" class="btn fa fa-pencil pointer btn btn-default setup_meta red"></span>';

        if ($row['id_formato'] == $fi->id) {
          if ($fi->vb > $hoy) {
            $cerrar = false;
          } else {
            $cerrar = true;
          }
        } else if ($row['id_formato'] == $fp->id) {
          if ($fp->vb > $hoy) {
            $cerrar = false;
          } else {
            $cerrar = true;
          }
        }

        //Dependiendo el estado de la meta, se pintan los btns indicados.
        if ($idusuarioregistra == $ps) {
          $row['propiedad'] = 'Propio';
        } else if ($row['id_responsable'] == $ps) {
          $row['propiedad'] = 'Corresponsable';
        } else {
          $row['propiedad'] = 'Gestor';
        }

        if (($row['meta_estado'] == 'Meta_En_Ela' || $row['meta_estado'] == 'Meta_En_Cor') && !$cerrar && ($idusuarioregistra == $ps || $administrar)) {

          $row['accion'] = "$btnAprobar $btn_del $btn_aprobar";
        } else if ($row['meta_estado'] == 'Meta_Cor_Planeacion' && !$cerrar && ($idusuarioregistra == $ps || $superAdmin || $tp == "Plan_Accion_Admin")) {

          $row['accion'] = "$btnAprobar $btn_del $btnApro3";
        } else if ($row['meta_estado'] == 'meta_enviada' && ($idPermiso || $superAdmin || $administrar)) { //Accion enviada

          $row['accion'] = "$btnApro2 $btn_reprobar";
        } else if ($row['meta_estado'] == 'meta_aprobada' && ($idPermiso || $superAdmin || $administrar)) { //Accion avalada

          if ($superAdmin || $tp == "Plan_Accion_Admin") {
            $row['accion'] = "$btnAprobar $btn_del $btnApro3 $btn_reprobar";
          } else if ($tp == "Plan_Accion_Admin_Pro") {
            $row['accion'] = $btn_reprobar;
          } else if ($idPermiso) {
            $row['accion'] = "$btn_reprobar";
          }
        } else if ($row['meta_estado'] == 'Meta_Ava_Planeacion' && ($administrar || $superAdmin)) { //Accion avalada por planeacion

          if ($tp == "Plan_Accion_Admin" || $tp == "Per_Admin") {
            $row['accion'] = $btn_reprobar;
          } else {
            $row['accion'] = $btn_sin_accion;
          }
        } else {
          $row['accion'] = $btn_sin_accion;
        }

        $row['ver'] = $btn_ver;
        if ($idPermiso || $idusuarioregistra == $ps || $administrar || !empty($row['id_responsable']) || ($ps == "894" && $row['liderProvi'] == "19555")) { //19556 - local, 19555 - Produccion
          array_push($newdata, $row);
        }
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Funcion temporal para cambiar de estados la solicitud - BORRAR Y MEJORAR */
  public function changeStatus()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $tp = $_SESSION['perfil'];
      $administrar = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro" ? true : false;
      $superAdmin = $_SESSION["perfil"] == "Per_Admin" ? true : false;
      $serverStatus = $this->timeCheck(true);

      $idMeta = $this->input->post('idMeta');
      $metaObs = $this->input->post('metaObs');
      $metas = $this->plan_accion_model->listar_solicitudes($idMeta, '', true);
      $query = [];

      if ($metas->meta_estado == 'Meta_En_Ela' || $metas->meta_estado == 'Meta_En_Cor') { //En elaboracion y en correcion

        $query = $this->pasarEstado($idMeta, $metaObs, "meta_enviada");
      } else if ($metas->meta_estado == "meta_enviada") {

        $query = $this->pasarEstado($idMeta, $metaObs, "meta_aprobada");
      } else if ($metas->meta_estado == "meta_aprobada") {

        $query = $this->pasarEstado($idMeta, $metaObs, "Meta_Ava_Planeacion");
      } else if ($metas->meta_estado == "Meta_Ava_Planeacion") {

        if (!empty($metaObs)) {
          $corregir = $this->enviarCorreccion($idMeta, $metaObs, 'Meta_Cor_Planeacion');
        }
      } else if ($metas->meta_estado == "Meta_Cor_Planeacion") { //Meta en correccion - Planeacion

        $query = $this->pasarEstado($idMeta, $metaObs, "Meta_Ava_Planeacion");
      } else {
        $r = ["mensaje" => "Esta meta, ya se ha gestionado anteriormente, consulte con el administrador del sistema.", "tipo" => "warning", "titulo" => "¡Atención!"];
      }

      if (empty($query)) {
        $r = ["mensaje" => "La Meta se ha enviado correctamente.", "tipo" => "success", "titulo" => "Bien!"];
      } else {
        $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* Funcion para pasar de estado una acción */
  public function pasarEstado($idmeta, $metaObs, $estadopasa)
  {
    if (empty($metaObs)) {
      $tabla = "metas_plan_accion";
      $where = ['id' => $idmeta];
      $datos = ['meta_estado' => $estadopasa];
      $verificarCampos = $this->plan_accion_model->verificarDatosMetas($idmeta);
      $hey = $this->datosVacios($verificarCampos);

      if ($hey != 1) {
        exit(json_encode(["mensaje" => "$hey", "tipo" => "warning", "titulo" => "¡Atención!"]));
      } else {
        $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
      }
    } else {
      $corregir = $this->enviarCorreccion($idmeta, $metaObs);
    }
  }

  /* Funcion para enviar a correccion una acción */
  public function enviarCorreccion($idMeta, $metaObs, $estadopasa = '')
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $hoy = date('Y-m-d H:i:s');
      $tabla = 'metas_plan_accion';
      $estadopass = empty($estadopasa) ? 'Meta_En_Cor' : $estadopasa;
      $datos = [
        "meta_estado" => $estadopass,
        "observaciones" => "$metaObs",
        "fecha_corrige" => $hoy
      ];
      $where = ['id' => $idMeta];

      $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
      if (empty($query)) {
        $queryy = $this->plan_accion_model->listar_solicitudes($idMeta, '', true);
        $estadopass == "Meta_Cor_Planeacion" ? $notificar = 1 : $notificar = $this->enviar_correos_estados($queryy);
        //$notificar = $this->enviar_correos_estados($queryy);
        //$notificar = 1;
        $r = ["mensaje" => "La Meta se ha enviado correctamente.", "tipo" => "success", "titulo" => "Bien!", "notificacion_enviada" => $notificar];
      } else {
        $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* Aviso de datos incompletos */
  public function datosVacios($datosMeta)
  {
    if (!empty($datosMeta->idIndicadorOp)) {
      $indicadorOp = $this->plan_accion_model->find_idParametro($datosMeta->idIndicadorOp);
      if ($indicadorOp->idaux == 'ind_porcent') {
        if (empty($datosMeta->cifraRef)) {
          return "Esta acción, tiene pendiente por diligenciar la cifra de referencia en el formulario principal.";
        }
      }
    }


    if (empty($datosMeta->idEntregable)) {
      return "Esta acción, debe tener establecido un formato de acción, sea de programa y/o institucional.";
    } else if (empty($datosMeta->idArea)) {
      return "Esta acción, debe tener por lo menos un área estratégica seleccionada.";
    } else if (empty($datosMeta->accion_responsables)) {
      return "Esta acción, debe tener por lo menos un responsable asignado para poder continuar.";
    } else if (empty($datosMeta->idReto)) {
      return "Para poder enviar esta acción, en el formaulario principal, debe seleccionar un Reto.";
    } else if (empty($datosMeta->idPlanDesarrollo)) {
      return "Para poder enviar esta acción, en el formaulario principal, debe seleccionar una Meta del Plan de Desarrollo.";
    } else if (empty($datosMeta->idIndicadorEst)) {
      return "Para poder enviar esta acción, en el formaulario principal, debe diligenciar el Indicador Estratégico.";
    } else if (empty($datosMeta->metaPlanAccion)) {
      return "Para poder enviar esta acción, en el formaulario principal, debe diligenciar el campo llamado: Meta de Plan de Acción 2022.";
    } else if (empty($datosMeta->indicadorOp)) {
      return "Para poder enviar esta acción, en el formaulario principal, debe diligenciar un Indicador Operativo.";
    } else if (empty($datosMeta->idIndicadorOp)) {
      return "Para poder enviar esta acción, en el formaulario principal, debe diligenciar un Tipo de Indicador Operativo.";
    } else if (empty($datosMeta->nombre_accion)) {
      return "Para poder enviar esta acción, en el formaulario principal, debe diligenciar el campo llamado: Acciones.";
    } else if (empty($datosMeta->factores_seleccionados)) {
      return "Esta acción, debe tener por lo menos un lineamiento de acreditación seleccionado.";
    } else if (empty($datosMeta->caracteristicas_factor)) {
      return "Cada factor seleccionado, debe tener por lo menos una característica selecta.";
    } else if (empty($datosMeta->meta_cronograma)) {
      return "Para poder enviar esta acción, en el apartado de 'Cronograma', debe tener seleccionado por lo menos un trimestre con toda la información requerida.";
    } else if (empty($datosMeta->meta_docs_soporte)) {
      return "Para poder enviar esta acción, en el apartado de 'Cronograma', una vez seleccionado el trimestre, debe guardar por lo menos un nombre de documentos soportes";
    } else if (empty($datosMeta->categoria_presupuestos)) {
      return "Esta acción, debe tener por lo menos un categoría de presupuesto.";
    } else if ($indicadorOp->idaux == 'ind_num') {

      //$ta = $datosMeta->totalAlcanzar;
      $mta = $datosMeta->meta;
      $ta = $this->plan_accion_model->sumacantidad($datosMeta->id);

      if ($ta->cantidad < $datosMeta->meta || $ta->cantidad > $datosMeta->meta) {
        return "La sumatoria de las cantidades de metas a alcanzar en los trimestres de su cronograma *($ta->cantidad), 
        debe coincidir con lo diligenciado en el en campo: Meta, del formulario principal *($mta).";
      }
    }

    $x = $this->plan_accion_model->find_idParametro($datosMeta->categoria_presupuestos);
    if ($x->idaux != "no_presupuesto") {
      if (empty($datosMeta->tipo_prespuesto)) {
        return "Para poder enviar esta acción, en el apartado de 'Presupuesto', debe seleccionar el tipo de presupuesto.";
      } else if (empty($datosMeta->item_presupuestos)) {
        return "Para poder enviar esta acción, en el apartado de 'Presupuestos' debe diligenciar el ítem de éste.";
      } else if (empty($datosMeta->descripcion_presupuesto)) {
        return "Para poder enviar esta acción, en el apartado de 'Presupuesto' debe diligenciar el campo: 'Descripción del presupuesto'.";
      } else if (empty($datosMeta->presupuesto_valor_solicitado)) {
        return "Para poder enviar esta acción, en el apartado de 'Presupuesto' debe ingresar el valor solicitado.";
      }
    }

    return 1;
  }

  /* Funcion para traer los area estrategicas aisgnadas en permisos parametros */
  public function buscar_retos_etc($area_est_id = "", $reto_meta_indicador = "")
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      if (empty($area_est_id) or empty($reto_meta_indicador)) {
        $area_est_id = $this->input->post("area_est_selected");
        $reto_meta_indicador = $this->input->post("reto_meta_indicador");
        $valora = $this->input->post("val_a");
        $valorb = $this->input->post("val_b");
        $ind_search = $this->input->post("ind_search");
        $reto_etc = "";
        $nombre_area_selected = "";
        $valora_or_b = "";
        $render_inds = false;

        if (!empty($area_est_id) or !empty($reto_meta_indicador)) {

          $btn_check = '<span class="btn btn-default select"><span class="fa fa-check" style="color: #2DAB36;"></span></span>';

          // Si es reto, metas o indicadores, se busca el result set acorde a lo solicitado.
          $codigo = '';
          if ($reto_meta_indicador == "Retos") {
            $codigo = 'retos_plan_accion';
            $idparametro = $this->plan_accion_model->find_idParametro($codigo);
            $reto_etc = $idparametro->idpa;
          } else if ($reto_meta_indicador == "Metas") {
            $codigo = 'planDes_plan_accion';
            $idparametro = $this->plan_accion_model->find_idParametro($codigo);
            $reto_etc = $idparametro->idpa;
          } else if ($reto_meta_indicador == "Indicadores") {
            $codigo = 'indicadorEst_plan_accion';
            $idparametro = $this->plan_accion_model->find_idParametro($codigo);
            $reto_etc = $idparametro->idpa;
          }

          $new_data = [];

          if ($codigo == 'planDes_plan_accion') {
            !empty($valora) ? $query = $this->plan_accion_model->buscar_plan_des($area_est_id, $valora, $reto_etc, $ind_search)
              : $query = [];
          } else if ($codigo == 'indicadorEst_plan_accion') {
            $ind_search == true ? $valora_or_b = $valorb : $valora_or_b = $valora;
            $ind_search == true ? $render_inds = true : $render_inds = false;
            $query = $this->plan_accion_model->buscar_indis($area_est_id, $valora_or_b, $reto_etc, $ind_search);
          } else {
            $query = $this->plan_accion_model->buscar_retos_etc($area_est_id, $reto_etc);
          }

          $consul = $this->plan_accion_model->buscar_idparametro($area_est_id);
          if ($consul) {
            $nombre_area_selected = $consul->valor;
          }
          foreach ($query as $row) {
            $row["acciones"] = "$btn_check";
            $row["area"] = $nombre_area_selected;
            $row["render_ind"] = $render_inds;
            array_push($new_data, $row);
          }

          $r = $new_data;
        } else {
          $r = [];
        }
      }
    }
    exit(json_encode($r));
  }

  /* Funcion buscar personas, responsables asignados segun id de meta aquii */
  public function buscar_responsables_asignados()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $r = [];
      $newdata = [];
      $btn_remove = '<span class="glyphicon glyphicon-remove pointer btn btn-default remove_res" title="¡Eliminar responsable!" style="color: #CA3E33; margin: 0 1px;"></span>';
      $metaId = $this->input->post("mtaId");

      $res_asigned = $this->plan_accion_model->responsables_asignados($metaId);

      foreach ($res_asigned as $row) {
        $row['acciones'] = $btn_remove;
        array_push($newdata, $row);
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Funcion buscar personas, responsables aquii */
  public function buscar_responsable()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $r = [];
      $newdata = [];
      $btn_off = '<span style="color: #39B23B;" title="¡Asignar!" data-status="ns" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default disable"></span>';
      $btn_on = '<span class="glyphicon glyphicon-remove pointer btn btn-default enable" title="¡Desasignar!" style="color: #CA3E33; margin: 0 1px;"></span>';
      $btn_accion = "";
      $persona = $this->input->post('busqueda');
      $metaId = $this->input->post('meta_slectaa');
      $estado = "";

      $query = $this->plan_accion_model->buscar_responsable($persona);
      $seleccionado = $this->plan_accion_model->responsables_asignados($metaId);

      foreach ($query as $row) {
        if ($seleccionado) {
          foreach ($seleccionado as $selected) {
            if ($row['id'] == $selected['id'] and $selected['id_meta'] == $metaId) {
              $btn_accion = $btn_on;
              $estado = "Asignado";
              break;
            } else {
              $btn_accion = $btn_off;
              $estado = "No asignado";
            }
          }
        } else {
          $btn_accion = $btn_off;
        }

        $row['acciones'] = $btn_accion;
        $row['estado'] = $estado;
        array_push($newdata, $row);
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Guardar los responsables seleccionados */
  public function save_responsable()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id_meta = $this->input->post('idmeta');
      $idpersona = $this->input->post('idper');
      //exit(json_encode($idpersona));
      $tabla = "responsables_plan_accion";
      $tosend = [
        'id_meta' => $id_meta,
        'id_responsable' => $idpersona,
        'id_usuario_registra' => $_SESSION['persona'],
      ];
      $where = ['id' => $idpersona];
      $query = $this->plan_accion_model->save_inf($tabla, $tosend, $where);
      if (empty($query)) {
        $r = true;
      } else {
        $r = false;
      }
    }
    exit(json_encode($r));
  }

  /* Guardar los responsables seleccionados */
  public function saveLideres()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idFormato = $this->input->post('idFormato');
      $viceRol = $this->input->post('viceRol');
      $idpersona = $this->input->post('idper');
      $assigned = $this->plan_accion_model->permisosLideres();
      $dirsAssig = $this->plan_accion_model->directoresAssigned($idpersona, $idFormato);
      if ($assigned) {
        foreach ($assigned as $row) {
          if ($row['id_lider'] == $idpersona && $row['id_formato'] == $idFormato && $row['estado'] == 1) {
            $r = ["mensaje" => "La persona seleccionada, ya tiene el (" . $row['formatoName'] . ") asignado.", "tipo" => "info", "titulo" => "¡Atención!"];
            exit(json_encode($r));
          }
        }
      }
      if ($dirsAssig) {
        foreach ($dirsAssig as $dir) {
          if ($dir['id_director'] == $idpersona) {
            $r = ["mensaje" => "La persona seleccionada, se encuentra asignada como director del Lider: " . $dir['liderName'] . ", para el " . $dir['formatoName'] . '.', "tipo" => "info", "titulo" => "¡Atención!"];
            exit(json_encode($r));
          }
        }
      }
      $tabla = "plan_accion_permisos_lideres";
      $tosend = [
        'id_formato' => $idFormato,
        'id_lider' => $idpersona,
        'id_rol' => $viceRol,
        'id_usuario_registra' => $_SESSION['persona']
      ];
      $query = $this->plan_accion_model->save_inf($tabla, $tosend);
      if (empty($query)) {
        $r = ["mensaje" => "La operacion se ha realizado exitosamente!", "tipo" => "success", "titulo" => "¡Bien!"];
      } else {
        $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* Listar directores asignador a un lider */
  public function listarDirectoresAssigned()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idLider = $this->input->post('idLider');
      $idFormato = $this->input->post('idFormato');
      //exit(json_encode($idFormato));
      $newdata = [];
      $btnDel = '<span class="glyphicon glyphicon-remove pointer btn btn-default delDirector" title="¡Desasignar!" style="color: #CA3E33;"></span>';
      $query = $this->plan_accion_model->directoresAssigned($idLider, $idFormato);
      if ($query) {
        foreach ($query as $row) {
          $row['acciones'] = "$btnDel";
          $row['presu'] = $row['presu'];
          array_push($newdata, $row);
        }
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Agregar directores a lider seleccionados */
  public function addDirectorsToLider()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idLider = $this->input->post('idLider');
      $idDirector = $this->input->post('idDirector');
      $presupuestoDirector = $this->input->post('presuDirect');
      $idFormato = $this->input->post('idFormato');
      if (empty($presupuestoDirector) or $presupuestoDirector == "0") {
        $r = ["mensaje" => "Debe asignar un presupuesto al director para poder continuar.", "tipo" => "info", "titulo" => ""];
      } else {
        $intCheck = [
          'El Líder no ha sido identificado correctamente.' => $idLider,
          'El Director no ha sido identificado correctamente.' => $idDirector
        ];
        $check = $this->pages_model->verificar_campos_numericos($intCheck);
        if (is_array($check)) {
          $r = ['mensaje' => $check['field'], 'tipo' => 'warning', 'titulo' => '¡Atención!'];
        } else {
          $directoresAssigned = $this->plan_accion_model->directoresAssigned($idDirector, $idFormato);
          //exit(json_encode($directoresAssigned));
          if ($directoresAssigned) {
            foreach ($directoresAssigned as $i => $director) {
              if ($director['id_director'] == $idDirector && $director['id_lider'] == $idLider) {
                $r = ["mensaje" => "El director seleccionado ya ha sido asignado anteriormente a este mísmo líder.", "tipo" => "info", "titulo" => "¡Atención!"];
                exit(json_encode($r));
              }
              if ($director['id_director'] == $idDirector || $director['id_director'] == $idLider && $directoresAssigned[$i]['id_formato'] == $idFormato) {
                $r = ["mensaje" => "El director que intenta seleccionar, ya esta asignado al " . $directoresAssigned[$i]['formatoName'], "tipo" => "info", "titulo" => "¡Atención!"];
                exit(json_encode($r));
              }
            }
          }

          $tabla = 'plan_accion_permisos_equipos';
          $datos = [
            'id_usuario_registra' => $_SESSION['persona'],
            'id_lider' => $idLider,
            'id_director' => $idDirector,
            'presupuesto' => $presupuestoDirector
          ];
          $query = $this->plan_accion_model->save_inf($tabla, $datos);
          if (empty($query)) {
            $r = ["mensaje" => "La operacion se ha realizado exitosamente!", "tipo" => "success", "titulo" => "¡Bien!"];
          } else {
            $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
          }
        }
      }
    }
    exit(json_encode($r));
  }

  /* Eliminar director de un lider */
  public function delDirectorsToLider()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id = $this->input->post('id');
      $idLider = $this->input->post('idLider');
      $idDirector = $this->input->post('idDirector');
      $intCheck = [
        'Error en indentificador principal. Contacte con el administrador del módulo' => $id,
        'Error al identificar al lider.' => $idLider,
        'Error al identificar al director' => $idDirector
      ];
      $check = $this->pages_model->verificar_campos_numericos($intCheck);
      if (is_array($check)) {
        $r = ["mensaje" => $check['field'], "tipo" => "error", "titulo" => ""];
      } else {
        $tabla = 'plan_accion_permisos_equipos';
        $datos = [
          'id_usuario_elimina' => 1,
          'estado' => 0
        ];
        $where = ['id' => $id, 'id_lider' => $idLider, 'id_director' => $idDirector];
        $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
        if (empty($query)) {
          $r = ["mensaje" => "La operacion se ha realizado exitosamente!", "tipo" => "success", "titulo" => "¡Bien!"];
        } else {
          $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
        }
      }
    }
    exit(json_encode($r));
  }

  /* Eliminar los responsables seleccionados */
  public function delete_responsable()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id_meta = $this->input->post('idmeta');
      $idpersona = $this->input->post('idper');
      $tabla = "responsables_plan_accion";
      $where = ['id_responsable' => $idpersona, "id_meta" => $id_meta];
      $query = $this->plan_accion_model->del_inf($tabla, $where);
      $r = $query;
    }
    exit(json_encode($r));
  }

  /* Buscar idparametro del reto, meta o indicador */
  public function buscar_idparametro($id = "", $idaux = "")
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $query = $this->plan_accion_model->buscar_idparametro($id, $idaux);
      $r = $query;
    }
    return $r;
  }

  /* Listar indicadores OP */
  public function listar_indicadores_operativos()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idparametro = $this->plan_accion_model->find_idParametro('indicador_operativo');
      $query = $this->traer_datos_valorp($idparametro->idpa);
      $r = $query;
    }
    exit(json_encode($r));
  }

  /* Buscar datos de valor_parametro */
  public function traer_datos_valorp($idparametro = "", $id = "", $row = false)
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $query = $this->plan_accion_model->traer_datos_valorp($idparametro, $id, $row);
      $r = $query;
    }
    return $r;
  }

  /* Funcion para listar metas despues de haber seleccionado un area estrategica */
  public function listar_metas($area_estrategica = "", $id_meta = "")
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {

      $newdata = [];
      $carta_def =
        '
      <div style="padding: 1%; display: inline-table;">
        <div class="card carta_styles pointer new_meta">
          <div class="img_card_container">
            <img src="' . base_url() . '/imagenes/plan_accion.png" alt="..." style="width: 48%; padding-top: 5px; margin: auto;">
          </div>
          <div class="card-body" style="height:auto; padding: 5%; padding-bottom: 15%;">
            <h4 class="card-title" style="font-weight: bold; font-size: 1.8em;">¡Nueva!</h4>
            <p class="card-text" style="font-size: 1.2em;">¡Aquí, puedes agregar nuevas metas!</p>
            <span class="btn btn-default cards_btns add_metas">
              <span class="fa fa-plus"></span> Agregar
            </span>
          </div>
        </div>
      </div>
      ';

      if (empty($area_estrategica)) {
        $area_estrategica = $this->input->post("area_est");
      }

      $query = $this->plan_accion_model->listar_metas($area_estrategica, $id_meta);

      if ($query) {
        foreach ($query as $row) {
          $serverStatus = $this->timeCheck(true, $row['id_formato']);
          $row["carta_defecto"] = $carta_def;
          if (!$serverStatus) {
            array_push($newdata, $row);
          } else {
            $newdata["carta_defecto"] = $carta_def;
          }
        }
      } else {
        $newdata["carta_defecto"] = $carta_def;
      }

      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Function para checkear que haya un formato definido para la meta creada */
  public function check_formato_selected()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idm = $this->input->post('idMeta');
      $query = $this->plan_accion_model->listar_solicitudes($idm, '', true);
      if ($query) {
        if ($query->id_formato and $query->id_formato != '') {
          $r = 1;
        } else {
          $r = -1;
        }
      } else {
        $r = -1;
      }
    }
    exit(json_encode($r));
  }

  /* Funcion para guardar_metas_accion */
  public function guardar_metas_accion()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      //El id formato, se traer para verificar en valorA de ese formato, si el servidor permite o no guardar metas/acciones.
      $idFormato = $this->input->post("idFormato");
      $cerrar = $this->timeCheck(true, $idFormato);

      //exit(json_encode($cerrar));

      $administrar = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro" ? true : false;
      $superAdmin = $_SESSION["perfil"] == "Per_Admin" ? true : false;

      if (!$cerrar || $administrar || $superAdmin) {
        $tosend = [];
        $meta_upd_id = $this->input->post("meta_upd_id");
        $id_area_est = $this->input->post("id_area_selected");

        $id_reto = $this->input->post("id_reto");
        $id_reto == 'undefined' ? $id_reto = '' : false;

        $id_meta_ins = $this->input->post("id_meta_institucional");
        $id_meta_ins == 'undefined' ? $id_meta_ins = '' : false;

        $id_plandesarrollo = $this->input->post("id_plandes");
        $id_plandesarrollo == 'undefined' ? $id_plandesarrollo = '' : false;

        $id_indicador_est = $this->input->post("id_indicador_es");
        $id_indicador_est == 'undefined' ? $id_indicador_est = '' : false;

        $meta_plan_accion = $this->input->post("meta_plan_accion");
        $indicador_operativo = $this->input->post("indicador_operativo");
        $num_or_porcents = $this->input->post("num_or_porcents");
        $cifra_ref = $this->input->post("cifra_referencia");

        $meta = $this->input->post("meta");
        if (!empty($meta)) {
          if (!is_numeric($meta)) {
            exit(json_encode(["mensaje" => "En el campo: Meta 2022, solo se aceptan valores numéricos.", "tipo" => "warning", "titulo" => "¡Atención!"]));
          }
        }

        $nombre_accion = $this->input->post("nombre_accion");
        $metaEstado = '';
        $meta_upd_id == '' or empty($meta_upd_id) ? $metaEstado = 'Meta_En_Ela' : $metaEstado = $metaEstado;
        empty($meta_plan_accion) ? $meta_plan_accion = '¡Nueva Meta Creada!' : false;

        /* Las verificaciones a continuacion se realizan debido a que en las dos primeras etapas de la creacion de la meta, la
        persona no va a diligenciar el formulario, por lo que, ya cuando la persona este gestionando la info del form, es cuando
        se deben hacer las verificaciones de si vienen vacios */
        $idparametro = $this->plan_accion_model->find_idParametro('trimestre_time');
        if (empty($meta_upd_id)) {
          $tosend = [
            'id_area' => $id_area_est,
            'id_usuario_registra' => $_SESSION['persona'],
            'meta_plan_accion' => $meta_plan_accion,
            'id_area' => $id_area_est,
            "id_entregable" => $idparametro->id,
            "id_formato" => $idFormato
          ];
          $check = 1;
        } else {
          $checkFormatState = $this->plan_accion_model->listar_solicitudes_crearmeta($meta_upd_id, '', true);

          /* Aplicar reset a cronograma si en caso tal cambian de tipo de indicador operativi y asi 
          evitar un cronograma mal gestionado */

          //exit(json_encode($checkFormatState));

          if ($checkFormatState) {
            if ($checkFormatState->tipo_indicador_id != $num_or_porcents) {
              $resetCrono = $this->resetCrono($meta_upd_id, true);
            }
          }

          /* Continua */
          if (empty($checkFormatState->id_formato) || $checkFormatState->id_formato == NULL) {
            $check = 1;
          } else {
            $tocheck = [
              "Área estratégica" => $id_area_est,
              "Reto" => $id_reto,
              "Meta del Plan de Desarrollo Institucional" => $id_plandesarrollo,
              "Identificador del Indicador Estratégico" => $id_indicador_est,
              "Meta Plan de Acción" => $meta_plan_accion,
              "Indicador Operativo" => $indicador_operativo,
              "Tipo de indicador operativo" => $num_or_porcents,
              "Meta" => $meta,
              "Acciones" => $nombre_accion
            ];
            $np = $this->plan_accion_model->find_idParametro($num_or_porcents);
            if ($np->idaux == 'ind_porcent') {
              if (!is_numeric($cifra_ref)) {
                exit(json_encode(["mensaje" => "En el campo: Cifra de referencia 2021, solo se aceptan valores numéricos.", "tipo" => "warning", "titulo" => "¡Atención!"]));
              }
              $cifra_ref <= 0 ? $cifra_ref = '' : false;
              $tocheck += ["Cifra de Referencia" => $cifra_ref];
            } else {
              $cifra_ref = intval(-1);
            }
            $check = $this->pages_model->verificar_campos_string($tocheck);
          }
        }

        if (is_array($check)) {
          $r = ['mensaje' => 'El campo: ' . $check['field'] . ', está vacío o contiene datos no permitidos.', 'tipo' => 'warning', 'titulo' => '¡Atención!'];
        } else {
          !empty($id_reto) and $id_reto != 'undefined' ? $tosend += ['id_reto' => $id_reto] : false;
          !empty($id_plandesarrollo) and $id_plandesarrollo != 'undefined' ? $tosend += ['id_plan_desarrollo' => $id_plandesarrollo] : false;
          !empty($id_indicador_est) and $id_indicador_est != 'undefined' ? $tosend += ['id_indicador_estrategico' => $id_indicador_est] : false;
          !empty($num_or_porcents) ? $tosend += ['id_indicador_operativo' => $num_or_porcents] : false;
          !empty($indicador_operativo) ? $tosend += ['indicador_operativo' => $indicador_operativo] : false;
          !empty($cifra_ref) ? $tosend += ['cifra_referencia' => $cifra_ref] : false;
          !empty($meta) ? $tosend += ['meta' => $meta] : false;
          !empty($nombre_accion) ? $tosend += ['nombre_accion' => $nombre_accion] : false;
          !empty($metaEstado) ? $tosend += ['meta_estado' => $metaEstado] : false;
          !empty($meta_plan_accion) ? $tosend += ['meta_plan_accion' => $meta_plan_accion] : false;
          !empty($id_meta_ins) ? $tosend += ['id_meta_institucional' => $id_meta_ins] : false;

          if (!empty($meta_upd_id)) {
            $chkFormato = $this->plan_accion_model->listar_solicitudes($meta_upd_id, '',  true);
            if ($chkFormato->id_formato == '' or $chkFormato->id_formato == NULL) {
              if (!empty($idFormato) and $idFormato != 'undefined' and $idFormato) $tosend += ['id_formato' => $idFormato];
            }
            $query = $this->plan_accion_model->upd_inf("metas_plan_accion", $tosend, ["id" => $meta_upd_id]);
          } else if (empty($meta_upd_id)) {
            $query = $this->plan_accion_model->save_inf("metas_plan_accion", $tosend);
          }

          if (empty($query)) {
            $r = ["mensaje" => "La información se ha guardado exitosamente", "tipo" => "success", "titulo" => "¡Bien!"];
          } else {
            $r = ["mensaje" => $query . ".", "tipo" => "warning", "titulo" => "Oops"];
          }
        }
      } else {
        $r = ["mensaje" => "El proceso para la creación de acciones ya se encuentra cerrado. Para más información puede comunicarse con el departamento de planeación.", "tipo" => "info", "titulo" => "¡Atención!"];
      }
    }
    exit(json_encode($r));
  }

  /* Eliminar metas */
  public function del_meta($meta_id = "")
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $administrar = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro" ? true : false;
      $superAdmin = $_SESSION["perfil"] == "Per_Admin" ? true : false;
      $cerrar = $this->timeCheck(true);

      if (true /*!$cerrar || ($administrar || $superAdmin)*/) {
        if (empty($meta_id)) {
          $meta_id = $this->input->post("meta_id");
          $tabla = "metas_plan_accion";
          $where = ["id" => $meta_id];
          $disable = ["estado" => 0];
          $del = $this->upd_inf($tabla, $disable, $where);
          if ($del) {
            $r = ["mensaje" => "La operación se ha realizado exitosamente!", "tipo" => "success", "titulo" => "Bien!"];
          } else {
            $r = ["mensaje" => $del . ".", "tipo" => "error", "titulo" => ""];
          }
        } else {
          $r = [];
        }
      } else {
        $r = ["mensaje" => "El proceso para la creación de acciones ya se encuentra cerrado. Para más información puede comunicarse con el departamento de planeación.", "tipo" => "info", "titulo" => "¡Atención!"];
      }
    }
    exit(json_encode($r));
  }

  /* Funcion para listar factores insitucionales aquii */
  public function listar_factores_ins($program_or_insti = '')
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $r = [];
      $newdata = [];
      $btn_off = '<span style="color: #39B23B;" title="¡Asignar!" data-status="ns" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default disable facts_details"></span>';
      $btn_on = '<span class="glyphicon glyphicon-remove pointer btn btn-default enable" title="¡Desasignar!" style="color: #CA3E33; margin: 0 1px;"></span>';
      $btn_ver = '<span style="background-color: white; width: 100%; color: black;" class="btn btn-default pointer form-control facts_details"><span>Ver</span></span>';
      $btn_ver_chked = '<span style="background-color: #3DB069; width: 100%; color: white;" class="btn btn-default pointer form-control facts_details"><span>Ver</span></span>';
      $btn_accion = "";
      $btn_ver_gen = "";
      $estado = "";
      $factor_selected = $this->input->post("meta_selecta");

      if (empty($program_or_insti)) {
        $idparametro = $this->plan_accion_model->find_idParametro('factores_insti');
        $program_or_insti = $idparametro->idpa;
      }

      $facts = $this->plan_accion_model->listar_factores_ins($program_or_insti);
      $saved_facts = $this->plan_accion_model->factores_checked($factor_selected);

      foreach ($facts as $row) {

        if ($saved_facts) {
          foreach ($saved_facts as $fact) {
            if ($row['id'] == $fact['id_factor'] and $fact['id_meta'] == $factor_selected) {
              $btn_accion = $btn_on;
              $btn_ver_gen = $btn_ver_chked;
              $estado = "Asignado";
              break;
            } else {
              $btn_accion = $btn_off;
              $btn_ver_gen = $btn_ver;
              $estado = "No asignado";
            }
          }
        } else {
          $btn_accion = $btn_off;
          $btn_ver_gen = $btn_ver;
          $btn_accion = $btn_off;
          $estado = "No asignado";
        }

        $row['ver'] = $btn_ver_gen;
        $row['acciones'] = $btn_accion;
        $row['estado'] = $estado;
        array_push($newdata, $row);
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Listar factores de programa */
  public function listar_programa_facts()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idparametro = $this->plan_accion_model->find_idParametro('factores_program');
      $r = $this->listar_factores_ins($idparametro->idpa);
    }
    exit(json_encode($r));
  }

  /* Guardar los factores seleccionados */
  public function save_factors()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id_meta = $this->input->post('idmeta');
      $id_fact = $this->input->post('idfact');

      $tabla = "factores_plan_accion";
      $tosend = [
        'id_meta' => $id_meta,
        'id_factor' => $id_fact,
        'id_usuario_registra' => $_SESSION['persona'],
      ];
      $where = ['id' => $id_fact];
      $query = $this->plan_accion_model->save_inf($tabla, $tosend, $where);
      if (empty($query)) {
        $r = true;
      } else {
        $r = false;
      }
    }
    exit(json_encode($r));
  }

  /* Eliminar los factores seleccionados */
  public function delete_factors()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id_meta = $this->input->post('idmeta');
      $id_fact = $this->input->post('idfact');
      $tabla = "factores_plan_accion";
      $where = ['id_factor' => $id_fact, "id_meta" => $id_meta];
      $query = $this->plan_accion_model->del_inf($tabla, $where);
      $r = $query;
    }
    exit(json_encode($r));
  }

  /* Guardar los responsables seleccionados */
  public function guardar_responsables()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id_meta = $this->input->post('idmeta');
      $id_fact = $this->input->post('idper');
      $tabla = "factores_plan_accion";
      $where = ['id_factor' => $id_fact, "id_meta" => $id_meta];
      $query = $this->plan_accion_model->del_inf($tabla, $where);
      $r = $query;
    }
    exit(json_encode($r));
  }

  /* Traer detalles del factor seleccionado */
  public function detalles_facts($id_factor = "")
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $r = [];
      $newdata = [];
      $btn_on = '<span class="glyphicon glyphicon-remove pointer btn btn-default enable" title="¡Desasignar!" style="color: #CA3E33; margin: 0 1px;"></span>';
      $btn_off = '<span style="color: #39B23B;" title="¡Asignar!" data-status="ns" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default disable"></span>';
      $estado = '';
      $btnAccion = '';

      if (empty($id_factor)) {
        $id_factor = $this->input->post("caract_buscada");
      }

      $idMeta = $this->input->post("idMeta");
      $place = $this->input->post("insti_or_pro");

      if ($place == 'insti') {
        $idparametro = $this->plan_accion_model->find_idParametro('caracts_factoresInsti');
        if ($idparametro) {
          $idParametro = $idparametro->idpa;
        }
      } else if ($place == 'program') {
        $idparametro = $this->plan_accion_model->find_idParametro('caracts_factoresProgram');
        if ($idparametro) {
          $idParametro = $idparametro->idpa;
        }
      } else {
        $idParametro = '';
      }

      $query = $this->plan_accion_model->detalles_facts($id_factor, $idParametro);

      if ($query) {
        $saved_caracts = $this->plan_accion_model->caracteristicas_checked($idMeta);
        foreach ($query as $key => $row) {

          if ($saved_caracts) {
            foreach ($saved_caracts as $fact) {

              if ($row['id'] == $fact['id_caracteristica'] and $fact['id_meta'] == $idMeta) {
                $btnAccion = $btn_on;
                $estado = "Asignado";
                break;
              } else {
                $btnAccion = $btn_off;
                $estado = "No asignado";
              }
            }
          } else {
            $btnAccion = $btn_off;
            $estado = "No asignado";
          }

          $row['accion'] = $btnAccion;
          $row['estado'] = $estado;
          array_push($newdata, $row);
        }

        $r = $newdata;
      }
    }
    exit(json_encode($r));
  }

  /* Guardar caracteristicas de factor o programa */
  public function save_caracts()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id_meta = $this->input->post('idmeta');
      $idCaract = $this->input->post('idcaracts');
      $idFactor = $this->input->post('idFactor');
      $res = true;

      $factor_checked = $this->plan_accion_model->factores_checked($id_meta);

      if ($factor_checked) {
        foreach ($factor_checked as $row) {
          if ($row['id_factor'] == $idFactor) {
            $res = false;
            break;
          }
        }
      }

      if (!$res) {
        $tabla = "plan_accion_caracteristicas";
        $tosend = [
          'id_meta' => $id_meta,
          'id_caracteristica' => $idCaract,
          'id_usuario_registra' => $_SESSION['persona'],
        ];
        $where = ['id' => $idCaract];
        $query = $this->plan_accion_model->save_inf($tabla, $tosend, $where);
        if (empty($query)) {
          $r = true;
        } else {
          $r = false;
        }
      } else {
        $r = ['mensaje' => 'No puede seleccionar una característica sin antes haber elegido un factor', 'tipo' => 'error', 'titulo' => ''];
      }
    }
    exit(json_encode($r));
  }

  /* Borrar caracteristicas seleccionadas */
  /* Eliminar los factores seleccionados */
  public function delete_caracts()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id_meta = $this->input->post('idmeta');
      $id_caracts = $this->input->post('idcaracts');
      $tabla = "plan_accion_caracteristicas";
      $where = ['id_caracteristica' => $id_caracts, "id_meta" => $id_meta];
      $query = $this->plan_accion_model->del_inf($tabla, $where);
      $r = $query;
    }
    exit(json_encode($r));
  }

  /* Listar la categorias de presupuestos - gestion de presupuesto */
  public function categorias_presupuestos()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idparametro = $this->plan_accion_model->find_idParametro('presu_catego');
      if ($idparametro) {
        $query = $this->plan_accion_model->categorias_presupuestos($idparametro->idpa);
        $r = $query;
      } else {
        $r = [];
      }
    }
    exit(json_encode($r));
  }

  /* Listar los tipos en presupuestos segun la categoria */
  public function tipos_presupuestos($categoria_selected = '')
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      empty($categoria_selected) ? $categoria_selected = $this->input->post('categoria_selected') : false;
      if (empty($categoria_selected)) {
        $r = ["mensaje" => "Error interno. Código del error: " . __LINE__ . ".", "tipo" => "warning", "titulo" => "Oops"];
      } else {
        $idparametro = $this->plan_accion_model->find_idParametro('tipo_presu');
        if ($idparametro) {
          $query = $this->plan_accion_model->tipos_presupuestos($categoria_selected, $idparametro->idpa);
          $r = $query;
        } else {
          $r = [];
        }
      }
    }
    exit(json_encode($r));
  }

  /* Listar los items segun el tipo seleccionado */
  public function items_presupuestos($tipo_selected = '')
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      empty($tipo_selected) ? $tipo_selected = $this->input->post('tipo_selected') : false;
      if (empty($tipo_selected)) {
        $r = ["mensaje" => "Error interno. Código del error: " . __LINE__ . ".", "tipo" => "warning", "titulo" => "Oops"];
      } else {
        $idparametro = $this->plan_accion_model->find_idParametro('items_presu');
        if ($idparametro) {
          $query = $this->plan_accion_model->items_presupuestos($tipo_selected, $idparametro->idpa);
          $r = $query;
        } else {
          $r = [];
        }
      }
    }
    exit(json_encode($r));
  }

  /* Traer presupuestos de los directores */
  public function presupuestosDirector()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idFormato = $this->input->post('idFormato');
      $newdata = [];
      $presupuestoDirector = 0;
      $sumaTotalSolicitado = 0;
      $restaPresuTotal = 0;
      $query = $this->plan_accion_model->presupuestosDirector();
      if ($query) {
        foreach ($query as $row) {
          $presupuestoDirector = $row['directorPresupuesto'];
          $sumaTotalSolicitado += $row['valorS'];
          $restaPresuTotal = $row['directorPresupuesto'] - $sumaTotalSolicitado;
        }
        array_push($newdata, ['topePresu' => $presupuestoDirector, 'totalSolicitado' => $sumaTotalSolicitado, 'topeDispo' => $restaPresuTotal]);
      } else {
        $direPresu = $this->plan_accion_model->directoresAssigned($_SESSION['persona'], $idFormato);
        if ($direPresu) {
          foreach ($direPresu as $presu) {
            $presupuestoDirector = $presu['presu'];
          }
        }
        array_push($newdata, ['topePresu' => $presupuestoDirector, 'totalSolicitado' => $sumaTotalSolicitado, 'topeDispo' => $restaPresuTotal]);
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Guardar presupuestos */
  public function guardar_presupuestos()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id_presu = $this->input->post('id_presu');
      $idmeta = $this->input->post('idmeta');
      $categoria_presupuesto = $this->input->post('categoria_presupuesto');
      $tipo_presupuesto = $this->input->post('tipo_presupuesto');
      $item_presupuesto = $this->input->post('item_presupuesto');
      $descripcion = $this->input->post('descripcion');
      $valor_solicitado = $this->input->post('valor_solicitado');
      $tabla = 'presupuestos_plan_accion';
      $checkear = true;

      if (!empty($categoria_presupuesto)) {
        $x = $this->plan_accion_model->find_idParametro($categoria_presupuesto);
        if ($x->idaux == "no_presupuesto") {
          $tipo_presupuesto = null;
          $item_presupuesto = null;
          $descripcion = "No Aplica";
          $valor_solicitado = 0;
          $checkear = false;
        }
      }

      if ($checkear) {
        $tocheck = [
          "Meta" => $idmeta,
          "Categoría de presupuesto" => $categoria_presupuesto,
          "Tipo de presupuesto" => $item_presupuesto,
          "Descripción" => $descripcion
        ];

        $int_check = ["Valor solicitado" => $valor_solicitado];
        $check = $this->pages_model->verificar_campos_string($tocheck);
        $check_ints = $this->pages_model->verificar_campos_numericos($int_check);
      } else {
        $check = 1;
        $check_ints = 1;
      }

      if (is_array($check)) {
        exit(json_encode(['mensaje' => 'El campo ' . $check['field'] . ' está vacío o contiene datos no permitidos.', 'tipo' => 'error', 'titulo' => '']));
      } else if (is_array($check_ints)) {
        exit(json_encode(['mensaje' => 'El campo ' . $check['field'] . ' está vacío o contiene datos no permitidos.', 'tipo' => 'error', 'titulo' => '']));
      } else {
        $tosend = [
          "id_meta" => $idmeta,
          "id_tipo" => $tipo_presupuesto,
          "id_categoria" => $categoria_presupuesto,
          "id_item" => $item_presupuesto,
          "descripcion" => $descripcion,
          "valor_solicitado" => $valor_solicitado,
          "id_usuario_registra" => $_SESSION['persona']
        ];

        if (!empty($id_presu)) {
          $query = $this->plan_accion_model->upd_inf($tabla, $tosend, ['id' => $id_presu]);
        } else {
          $query = $this->plan_accion_model->save_inf($tabla, $tosend);
        }

        if (empty($query)) {
          $r = ['mensaje' => 'La solicitud de presupuesto se ha guardado exitosamente.', 'tipo' => 'success', 'titulo' => '¡Bien!'];
        } else {
          $r = ['mensaje' => $query . '.', 'tipo' => 'success', 'titulo' => '¡Bien!'];
        }
      }
    }
    exit(json_encode($r));
  }

  /* Listar presupuestos */
  public function listar_presupuestos()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id_meta = $this->input->post('id_meta');
      $query = $this->plan_accion_model->listar_presupuestos($id_meta);
      if ($query) {
        $newdata = [];

        $btn_ver = '<span style="background-color: white; width: 100%; color: black;" class="btn btn-default pointer form-control presu_details"><span>Ver</span></span>';
        $btn_remove = '<span class="glyphicon glyphicon-remove pointer btn btn-default remove_presu" title="¡Eliminar presupuesto!" style="color: #CA3E33;"></span>';
        $btn_upd = '<span class="glyphicon glyphicon-refresh pointer btn btn-default upd_presu" title="¡Actualizar presupuesto!" style="color: #26A054;"></span>';

        foreach ($query as $row) {
          $row['ver'] = $btn_ver;
          $row['accion'] = " $btn_upd $btn_remove ";
          array_push($newdata, $row);
        }
        $r = $newdata;
      } else {
        $r = [];
      }
    }
    exit(json_encode($r));
  }

  /* Eliminar presupuestos */
  public function del_presupuestos()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $id_meta = $this->input->post('metasel');
      $id_presu = $this->input->post('id_presu');
      $tabla = "presupuestos_plan_accion";
      $datos = ['estado' => 0, 'id_usuario_elimina' => $_SESSION['persona']];
      $where = ['id' => $id_presu, "id_meta" => $id_meta];
      $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
      if (empty($query)) {
        $r = true;
      } else {
        $r = false;
      }
    }
    exit(json_encode($r));
  }

  /* Check para saber si se ha seteado el entregable de cronograma */
  public function check_entregable()
  {
    if (!$this->super_estado) {
      $r = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
    } else {
      $idm = $this->input->post('id_meta');
      $query = $this->plan_accion_model->check_entregable($idm);
      $r = $query;
    }
    exit(json_encode($r));
  }

  /* Listar cronograma */
  public function traer_cronograma()
  {
    if (!$this->super_estado) {
      $r = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
    } else {
      $btn_off = '<span class="fa fa-toggle-off pointer btn btn-default enable red" data-status="off" data-des="1" title="Asignar"></span>';
      $btn_on = '<span class="fa fa-toggle-on pointer btn btn-default disable red" data-status="off" data-des="1" title="Retirar"></span>';
      $btnCheck = '<span class="fa fa-check pointer btn btn-default checkCrono" style="color:#2ECC71;" title="¡Gestionar nombre de documentos soporte!"></span>';
      $btnCheck2 = '<span class="fa fa-cogs pointer btn btn-default red checkCrono" title="¡Actualizar documentos soporte!"></span>';
      $btnAccion = '';
      $aviso = '';
      $toRender = [];
      $toview = [];
      $id_meta = $this->input->post('idMeta');
      $fl = $this->plan_accion_model->find_idParametro('Plan_Accion_TL');
      $fecha_limite = $this->plan_accion_model->find_idParametro($fl->id); //Trae el limite de dias de parametros generales.
      $fe = $this->plan_accion_model->find_idParametro('trimestre_time');
      $fecha_entregable = $this->plan_accion_model->find_idParametro($fe->id); //Trae el tipo de entregable selecto = trimestral.
      $id = '';
      $cantidad = '';

      $codigo_item = $this->plan_accion_model->listar_solicitudes($id_meta, '', true);
      $saved_especify = $this->plan_accion_model->datos_cronograma($id_meta, $codigo_item);


      if (!empty($codigo_item->tipo_indicador_id)) {
        $i = 0;
        $item = 1;
        $suma = intval($fecha_entregable->vy);
        $texto = '';

        while ($i < intval($fecha_limite->dato)) {
          $texto = $fecha_entregable->vx . " " . $item;
          $i = ($i + $suma);
          $item = $item += 1;
          array_push($toRender, $texto);
        }

        $idparametro = $this->plan_accion_model->find_idParametro($codigo_item->tipo_indicador_id);
        if ($idparametro->idaux == 'ind_porcent') { //Si es porcentual
          $aviso = 'Cuando la meta es de cumplimiento porcentual, se debe seleccionar el trimestre en que va entregar el 100% de la meta.';
          $avisoPrograma = 'Por favor tener en cuenta el cronograma institucional, <span class="pointer verAviso"><i><u>Clic Aquí</u></i></span>';
          foreach ($toRender as $key => $trime) {
            $key++;
            if (count($saved_especify) > 0) {
              foreach ($saved_especify as $valor) {
                if ($key == $valor['codigo_item']) {
                  $id = $valor['id'];
                  $btnAccion = "$btn_on";
                  break;
                } else {
                  $id = '';
                  $btnAccion = $btn_off;
                }
              }
            } else {
              $btnAccion = $btn_off;
            }
            array_push(
              $toview,
              [
                'id' => $id,
                "idmi" => $codigo_item->idmi,
                "codigo_item" => $key,
                'id_meta' => $codigo_item->id,
                "entregable" => $codigo_item->entregable,
                "trime" => $trime,
                "acciones" => $btnAccion,
                "indicador_op" => $codigo_item->tipo_indicador_id,
                'aviso' => $aviso,
                'cronoAviso' => $avisoPrograma,
                'porcents' => true
              ]
            );
          }
          $r = $toview;
        } else if ($idparametro->idaux == 'ind_num') { //Si es numerico
          $aviso = 'Debe seleccionar los trimestres y las cantidades que irá desarrollando para el cumplimiento de la meta';
          $avisoPrograma = 'Por favor tener en cuenta el cronograma institucional, <span class="pointer verAviso"><i><u>Clic Aquí</u></i></span>';
          foreach ($toRender as $key => $trime) {
            $key++;
            if (count($saved_especify) > 0) {
              foreach ($saved_especify as $valor) {
                if ($key == $valor['codigo_item']) {
                  $id = $valor['id'];
                  $cantidad = $valor['cantidad'];
                  $btnAccion = "$btnCheck2";
                  break;
                } else {
                  $id = '';
                  $cantidad = '';
                  $btnAccion = $btnCheck;
                }
              }
            } else {
              $btnAccion = $btnCheck;
            }
            array_push(
              $toview,
              [
                'id' => $id,
                "idmi" => $codigo_item->idmi,
                "codigo_item" => $key,
                "cantidad" => $cantidad,
                'id_meta' => $codigo_item->id,
                "entregable" => $codigo_item->entregable,
                "trime" => $trime, "acciones" => $btnAccion,
                "indicador_op" => $codigo_item->tipo_indicador_id,
                'aviso' => $aviso, 'cronoAviso' => $avisoPrograma,
                'porcents' => false
              ]
            );
          }
          $r = $toview;
        }
      } else {
        $r = ['mensaje' => 'Para empezar la gestión del cronograma, debe diligenciar Tipo de Indicador Operativo primero.', 'tipo' => 'warning', 'titulo' => '¡Atención!'];
      }
    }
    exit(json_encode($r));
  }

  /* Guardar cronogramas */
  public function guardar_cronograma()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idm = $this->input->post('idMeta');
      $indicador_opeativo = $this->input->post('indiOp');
      $idEnt = $this->input->post('entregable');
      $espe = $this->input->post('especi');
      $cantidad = $this->input->post('cantidad');
      $codigo_item = $this->input->post('codigo_item');
      $acciones_array = $this->input->post('acciones');

      //exit(json_encode($idEnt));

      $tocheck = [
        "El identificador de la acción, es incorrecto, favor consulte con el administrador del sistema." => $idm,
        "Debe seleccionar por lo menos un timrestre, para poder continuar." => $idEnt
      ];

      //Verificamos que no viaje vacia la cantidad en caso tal sea numerico
      $tipoIndOp = $this->plan_accion_model->find_idParametro($indicador_opeativo);
      if ($tipoIndOp) {
        if ($tipoIndOp->idaux == 'ind_num') { //Si es numerico, debe checkear los campos de cantidad
          if (empty($cantidad)) {
            $r = ["mensaje" => 'El campo: Cantidad de la meta a alcanzar en el trimestre, no puede tener valores vacíos o iguales a cero (0).', "tipo" => "warning", "titulo" => "¡Atención!"];
            exit(json_encode($r));
          }
        }
      }

      $check = $this->pages_model->verificar_campos_numericos($tocheck);

      if (is_array($check)) {
        $r = ["mensaje" => $check['field'], "tipo" => "warning", "titulo" => "¡Atención!"];
      } else {

        //Verificamos que no haya documentos soportes vacios
        if (!$acciones_array) {
          $r = ["mensaje" => 'Debe diligenciar por lo menos un nombre de documento soporte, para poder conitnuar.', "tipo" => "warning", "titulo" => "¡Atención!"];
          exit(json_encode($r));
        }

        $metaDetails = $this->plan_accion_model->listar_solicitudes($idm, '', true);
        $conteo = $cantidad;
        $saved_cronos = $this->plan_accion_model->datos_cronograma($idm, $codigo_item);

        //Si es de tipo numerico, si se evalua la cantidad de metas con la meta puesta en el formulario
        if ($tipoIndOp->idaux == 'ind_num') {
          if ($saved_cronos) {
            foreach ($saved_cronos as $crono) {
              $conteo += $crono['cantidad'];
            }
          }

          //Obtenemos la diferencia de cuanto se pasó el usuario a la hora de diligenciar
          $diferencia = $conteo - $metaDetails->meta;
          if ($conteo > $metaDetails->meta) {
            $msg = "La cantidad de la meta diligenciada en el trimestre ha superado el total indicado en el campo: Meta *($metaDetails->meta), del formulario principal, con diferencia de: $diferencia.";
            $r = ["mensaje" => $msg, "tipo" => "warning", "titulo" => "¡Atención!"];
            exit(json_encode($r));
          }
        }

        //Aqui verificamos que el indicador op sea porcentual o numerico y se realizan las acciones acorde a lo seleccionado
        $idparametro = $this->plan_accion_model->find_idParametro($metaDetails->tipo_indicador_id);
        if ($saved_cronos and $idparametro->idaux == 'ind_porcent') {
          $tabla = 'cronograma_plan_accion';
          $datos = ['estado' => 0];
          $where = ['id' => $saved_cronos[0]['id'], 'id_meta' => $idm];
          $upd = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
        }

        $tocheck = [
          "No hay una meta seleccionada previamente." => $idm,
          "¡No hay trimistre seleccionado!" => $idEnt,
          "¡Error codigo: " . __LINE__ . "!" => $codigo_item
        ];

        $check = $this->pages_model->verificar_campos_string($tocheck);

        if (is_array($check)) {
          $r = ["mensaje" => $check['field'], "tipo" => "warning", "titulo" => "¡Atención!"];
        } else {
          $table = "cronograma_plan_accion";
          $datos = [
            "id_meta" => $idm,
            "item" => $idEnt,
            "codigo_item" => $codigo_item,
            "id_usuario_registra" => $_SESSION['persona']
          ];
          !empty($cantidad) ? $datos += ["cantidad" => $cantidad] : false;
          !empty($espe) ? $datos += ["especificaciones" => $espe] : false;
          $query = $this->save_inf($table, $datos);
          if (empty($query)) {
            $tabla = "metas_plan_accion";
            $tosend = ["codigo_item" => $codigo_item];
            $where = ["id" => $idm];
            $upd_meta = $this->plan_accion_model->upd_inf($tabla, $tosend, $where);

            if (empty($upd_meta)) {
              if ($acciones_array) {
                $crono_id = $this->plan_accion_model->last_id();
                foreach ($acciones_array as $val) {
                  $tabla = 'plan_accion_acciones';

                  $tosend = [
                    'id_meta' => $idm,
                    'id_cronograma' => $crono_id->id,
                    'accion' => $val,
                    'id_usuario_registra' => $_SESSION['persona']
                  ];
                  $consul = $this->plan_accion_model->save_inf($tabla, $tosend);

                  if (!empty($consul)) {
                    $r = ["mensaje" => $consul . '.', 'tipo' => 'error', 'titulo' => ''];
                    exit(json_encode($r));
                  }
                }
              }
              $r = ["mensaje" => "La especificación se ha guardado correctamente.", "tipo" => "success", "titulo" => "¡Bien!"];
            } else {
              $r = ["mensaje" => $upd_meta . '.', 'tipo' => 'error', 'titulo' => ''];
            }
          } else {
            $r = ["mensaje" => 'La información no se ha podido guardar; error #' . __LINE__ . '.', 'tipo' => 'error', 'titulo' => ''];
          }
        }
      }
    }
    exit(json_encode($r));
  }

  /* Actualizar cronograma */
  public function upd_cronograma()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idm = $this->input->post('idMeta');
      $idEnt = $this->input->post('entregable');
      $espe = $this->input->post('especi');
      $cantidad = $this->input->post('cantidad');
      $codigo_item = $this->input->post('codigo_item');
      $idCrono = $this->input->post('idCrono');
      $acciones = $this->input->post('acciones');
      $del_acciones = $this->input->post('actions_del');
      $indicador_opeativo = $this->input->post('indiOp');

      //exit(json_encode($acciones));

      $tocheck = [
        "Error interno #" . __LINE__ . ". Contacte al administrador del modulo." => $idm,
        "¡Seleccione un trimestre para poder proseguir!" => $idEnt,
        "Error interno #" . __LINE__ . ". Contacte al administrador del modulo." => $codigo_item,
        "No hay un cronograma seleccionado para continuar con la operación." => $idCrono,
      ];

      $check = $this->pages_model->verificar_campos_string($tocheck);

      if (is_array($check)) {
        $r = ["mensaje" => $check['field'], "tipo" => "warning", "titulo" => "¡Atención!"];
      } else {
        //Validamos la info que vamos a actualizar
        $validacion = $this->cronoDataUpdCheck($cantidad, $indicador_opeativo, $idm, $codigo_item, $idCrono, $acciones, $del_acciones);

        if ($validacion == 1) {
          $table = "cronograma_plan_accion";
          $datos = [
            "codigo_item" => $codigo_item,
            "especificaciones" => $espe,
            "id_usuario_registra" => $_SESSION['persona']
          ];
          !empty($cantidad) ? $datos += ["cantidad" => $cantidad] : false;
          $where = ["id" => $idCrono];

          $upd = $this->plan_accion_model->upd_inf($table, $datos, $where);

          if (empty($upd)) {
            /* Si acciones y del_acciones vienen con data, se debe actualizar y eliminar lo que el usuario desea */
            if ($acciones && $del_acciones) {
              //adicionamos las acciones
              foreach ($acciones as $val) {
                $tabla = 'plan_accion_acciones';
                $tosend = [
                  'id_meta' => $idm,
                  'id_cronograma' => $idCrono,
                  'accion' => $val,
                  'id_usuario_registra' => $_SESSION['persona']
                ];
                $consul = $this->plan_accion_model->save_inf($tabla, $tosend);
                if (!empty($consul)) {
                  $r = ["mensaje" => $consul . '.', 'tipo' => 'error', 'titulo' => ''];
                  exit(json_encode($r));
                }
              }

              //Ejecutamos las eliminaciones
              foreach ($del_acciones as $val) {
                $tabla = 'plan_accion_acciones';
                $tosend = [
                  'estado' => 0,
                  'id_usuario_elimina' => $_SESSION['persona']
                ];
                $where = [
                  'id_meta' => $idm,
                  'id_cronograma' => $idCrono,
                  'id' => $val
                ];
                $consul = $this->plan_accion_model->upd_inf($tabla, $tosend, $where);
                if (!empty($consul)) {
                  $r = ["mensaje" => $consul . '.', 'tipo' => 'error', 'titulo' => ''];
                  exit(json_encode($r));
                }
              }
            } else {
              /* Estas son las acciones que se adicionan */
              if ($acciones) {
                foreach ($acciones as $val) {
                  $tabla = 'plan_accion_acciones';
                  $tosend = [
                    'id_meta' => $idm,
                    'id_cronograma' => $idCrono,
                    'accion' => $val,
                    'id_usuario_registra' => $_SESSION['persona']
                  ];
                  $consul = $this->plan_accion_model->save_inf($tabla, $tosend);
                  if (!empty($consul)) {
                    $r = ["mensaje" => $consul . '.', 'tipo' => 'error', 'titulo' => ''];
                    exit(json_encode($r));
                  }
                }
              } else {
                //Aqui es para las acciones que se van a settear en 0 para "Eliminarlas"
                if (!$acciones and $del_acciones) {
                  foreach ($del_acciones as $val) {
                    $tabla = 'plan_accion_acciones';
                    $tosend = [
                      'estado' => 0,
                      'id_usuario_elimina' => $_SESSION['persona']
                    ];
                    $where = [
                      'id_meta' => $idm,
                      'id_cronograma' => $idCrono,
                      'id' => $val
                    ];
                    $consul = $this->plan_accion_model->upd_inf($tabla, $tosend, $where);
                    if (!empty($consul)) {
                      $r = ["mensaje" => $consul . '.', 'tipo' => 'error', 'titulo' => ''];
                      exit(json_encode($r));
                    }
                  }
                }
              }
            }
            /* Fin de update y delete */
            $r = ["mensaje" => "El cronograma se ha actualizado con exitosamente.", "tipo" => "success", "titulo" => "Bien!"];
          } else {
            $r = ["mensaje" => $upd . '.', 'tipo' => 'error', 'titulo' => ''];
          }
        } else {
          $r = $validacion;
        }
      }
    }
    exit(json_encode($r));
  }


  /* Funcion para verificar que los datos del cronograma esten diligenciados correctamente para actualizar */
  public function cronoDataUpdCheck($cantidad, $indicador_opeativo, $idm, $codigo_item, $idCrono, $acciones, $del_acciones)
  {
    $metaDetails = $this->plan_accion_model->listar_solicitudes($idm, '', true);
    $conteo = $cantidad;
    $saved_cronos = $this->plan_accion_model->datos_cronograma($idm, $codigo_item);

    //exit(json_encode($acciones));
    //exit(json_encode($del_acciones));

    //Verificamos que no viaje vacia la cantidad en caso tal sea numerico
    $tipoIndOp = $this->plan_accion_model->find_idParametro($indicador_opeativo);
    if ($tipoIndOp) {
      if ($tipoIndOp->idaux == 'ind_num') { //Si es numerico, debe checkear los campos de cantidad
        if (empty($cantidad) || !is_numeric($cantidad)) {
          $r = ["mensaje" => 'El campo: Cantidad de la meta a alcanzar en el trimestre, solo permite valores numéricos diferentes de cero (0).', "tipo" => "warning", "titulo" => "¡Atención!"];
          return $r;
        }
      }
    }

    //Si no hay nombre de documentos soporte guardados, se verifica que el array acciones, venga con al menos un dato.
    if (!$saved_cronos) {
      if (!$acciones) {
        $r = ["mensaje" => 'Debe diligenciar por lo menos un nombre de documento soporte para poder conitnuar.', "tipo" => "warning", "titulo" => "¡Atención!"];
        return $r;
      }
    } else {
      if (!empty($del_acciones)) {
        if (!$acciones) {
          $r = ["mensaje" => "Debe diligenciar por lo menos un nombre de documento soporte para poder conitnuar.", "tipo" => "warning", "titulo" => "¡Atención!"];
          return $r;
        }
      }
    }

    if ($tipoIndOp->idaux == "ind_num") {
      //Obtenemos por cuanto se pasa la persona que intenta actualizar la cantidad de la meta a alcanzar en el trimestre
      $diferencia = $conteo - $metaDetails->meta;

      //Aqui, validamos que el item que quiere actualizar el usuario, cumpla con la cantidad limite puesta en el formulario Principal
      foreach ($saved_cronos as $cronos) {
        if ($cronos['id'] == $idCrono) {
          if ($cantidad > $metaDetails->meta) {
            $r = [
              "mensaje" => "La cantidad de la meta diligenciada en el trimestre ha superado el total indicado en el campo: Meta *($metaDetails->meta), del formulario principal, con diferencia de: $diferencia.",
              "tipo" => "warning",
              "titulo" => "¡Atención!"
            ];
            return $r;
          }
        }
      }
    }
    return 1;
  }

  /* Eliminar cronograma */
  public function del_cronograma()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idCrono = $this->input->post('idCrono');
      $idEnt = $this->input->post('idEnt');
      $itemCod = $this->input->post('itemCod');
      $idMeta = $this->input->post('idMeta');

      $tocheck = [
        "Identificador de Cronograma" => $idCrono,
        "Identificador de Entregable" => $idEnt,
        "Identificador de Ítem" => $itemCod
      ];

      $check = $this->pages_model->verificar_campos_string($tocheck);

      if (is_array($check)) {
        $r = ["mensaje" => "El campo: " . $check['field'] . ", no ha sido diligenciado correctamente.", "tipo" => "warning", "titulo" => "¡Atención!"];
      } else {
        $table = "cronograma_plan_accion";
        $datos = [
          "id_usuario_elimina" => $_SESSION['persona'],
          "estado" => '0'
        ];
        $where = ["id" => $idCrono, "item" => $idEnt, "codigo_item" => $itemCod];

        $upd = $this->plan_accion_model->upd_inf($table, $datos, $where);
        if (empty($upd)) {
          $del_acciones = $this->plan_accion_model->acciones_inf($idMeta, $idCrono, true);
          if ($del_acciones) {
            $tabla = 'plan_accion_acciones';
            $tosend = ['estado' => 0];
            $where = ['id_cronograma' => $idCrono];
            $del = $this->plan_accion_model->upd_inf($tabla, $tosend, $where);
            if (empty($del)) {
              $r = ["mensaje" => "El cronograma se ha eliminado correctamente.", "tipo" => "success", "titulo" => "Bien!"];
            } else {
              $r = ["mensaje" => $del . '.', 'tipo' => 'error', 'titulo' => ''];
            }
          } else {
            $r = ["mensaje" => "El cronograma se ha eliminado correctamente.", "tipo" => "success", "titulo" => "Bien!"];
          }
        } else {
          $r = ["mensaje" => $upd . '.', 'tipo' => 'error', 'titulo' => ''];
        }
      }
    }
    exit(json_encode($r));
  }

  /* Listar Acciones segun cronograma */
  public function listar_acciones()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idCrono = $this->input->post('idCrono');
      if (!empty($idCrono)) {
        $query = $this->plan_accion_model->listar_acciones($idCrono);
        $r = $query;
      } else {
        $r = [];
      }
    }
    exit(json_encode($r));
  }

  /* Buscar personas */
  public function buscarPersonas()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $newdata = [];
      $btnCheck = '<span class="fa fa-check pointer btn btn-default perSel" style="color:#2ECC71;" title="¡Seleccionar Persona!"></span>';
      $r = [];
      $personaBuscada = $this->input->post('personaBuscada');
      $query = [];
      $query = $this->plan_accion_model->buscarPersonas($personaBuscada);
      if ($query) {
        foreach ($query as $row) {
          $row['acciones'] = $btnCheck;
          array_push($newdata, $row);
        }
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* listar formatos asignados a una persona - gestores permisos */
  public function listarFormatosPlanAccion()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idGestor = $this->input->post('idGestor');
      $btnAssig = '<span class="btn btn-default asignar"title="¡Asignar Formato!" data-shutDown="sdn" title="Asignar Formato"><span class="fa fa-toggle-off"></span></span>';
      $btnRetire = '<span class="btn btn-default quitar"title="¡Retirar Formato!" style="color: #5cb85c"><span class="fa fa-toggle-on"></span></span>';
      $btnSetup = '<span class="btn btn-default config" data-shutDown="sdn"><span class="fa fa-cog"></span></span>';
      $btnAccion = '';
      $formatoIdPrincial = '';
      if (empty($idGestor) or $idGestor == '') {
        $r = [];
      } else {
        $newdata = [];
        $idpar = $this->plan_accion_model->find_idParametro('planAccion_formatos');
        $query = $this->plan_accion_model->listarFormatosPlanAccion($idpar->idpa);
        $formatosAsignados = $this->plan_accion_model->gestoresAsignados($idGestor);

        //formatoIdAuto es el autoincremental de la tabla plan_accion_gestores
        if ($query) {
          foreach ($query as $row) {
            if ($formatosAsignados) {
              foreach ($formatosAsignados as $formato) {
                if ($row['id'] == $formato['idFormat']) {
                  $btnAccion = "$btnRetire $btnSetup";
                  $row['acciones'] = $btnAccion;
                  $row['formatoIdAuto'] = $formato['id'];
                  break;
                } else {
                  $btnAccion = $btnAssig;
                  $row['acciones'] = $btnAccion;
                  $row['formatoIdAuto'] = '';
                }
              }
            } else {
              $btnAccion = $btnAssig;
              $row['acciones'] = $btnAccion;
              $row['formatoIdAuto'] = '';
            }
            array_push($newdata, $row);
          }
        }
        $r = $newdata;
      }
    }
    exit(json_encode($r));
  }

  /* Listar roles de vicerecotres */
  public function listarRolesVices()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $viceRoles = $this->plan_accion_model->find_idParametro('vices_rols');
      //exit(json_encode($viceRoles));
      $query = $this->plan_accion_model->listarRolesVices($viceRoles->idpa);
      if ($query) {
        $r = $query;
      } else {
        $r = [];
      }
    }
    exit(json_encode($r));
  }

  /* Asignar formato a gestores - personas */
  public function asignarFormatoGestor()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $formatoId = $this->input->post('formatoId');
      $idGestor = $this->input->post('idGestor');
      if (empty($formatoId) || empty($idGestor)) {
        $r = [];
      } else {
        $tabla = 'plan_accion_gestores';
        $datos = [
          'id_usuario_registra' => $_SESSION['persona'],
          'id_formato' => $formatoId,
          'id_gestor' => $idGestor
        ];
        $formatosAsignados = $this->plan_accion_model->gestoresAsignados($idGestor);
        if ($formatosAsignados) {
          foreach ($formatosAsignados as $formato) {
            if ($formato['idGestor'] == $idGestor and $formato['idFormat'] == $formatoId) {
              $r = ["mensaje" => "No es posible asignar un formato dos veces.", "tipo" => "warning", "titulo" => "¡Atención!"];
              exit(json_encode($r));
            }
          }
        }
        $query = $this->plan_accion_model->save_inf($tabla, $datos);
        if (empty($query)) {
          $r = ["mensaje" => "¡Formato asignado correctaente!", "tipo" => "success", "titulo" => "¡Bien!"];
        } else {
          $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
        }
      }
    }
    exit(json_encode($r));
  }

  /* Retirar formato a gestor */
  public function retirarFormatoGestor()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $formatoId = $this->input->post('formatoId');
      $idGestor = $this->input->post('idGestor');
      if (empty($formatoId) || empty($idGestor || empty($id))) {
        $r = [];
      } else {
        $hoy = date('Y-m-d H:i:s');
        $tabla = 'plan_accion_gestores';
        $datos = ['id_usuario_elimina' => $_SESSION['persona'], 'estado' => 0, 'fecha_elimina' => $hoy];
        $where = ['id_formato' => $formatoId, 'id_gestor' => $idGestor];
        $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
        if (empty($query)) {
          $r = ["mensaje" => "¡Formato retirado correctaente!", "tipo" => "success", "titulo" => "¡Bien!"];
        } else {
          $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
        }
      }
    }
    exit(json_encode($r));
  }

  /* Listar metas estados */
  public function listarMetasEstados()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $newdata = [];
      $btnRetire = '<span class="btn btn-default desasignar" title="Desasignar Estado"><span class="fa fa-toggle-on" style="color: #5cb85c"></span></span>';
      $btnAsig = '<span class="btn btn-default asignar" title="Asignar Estado"><span class="fa fa-toggle-off"></span></span>';
      $btnNotiOn = '<span class="btn btn-default notificar" title="Activar Notificación"><span class="fa fa-bell-o"></span></span>';
      $btnNotiOff = '<span class="btn btn-default no_notificar" title="Desactivar Notificación"><span class="fa fa-bell red"></span></span>';
      $btnAccion = '';
      $id = $this->input->post('idGestor');
      $idPar = $this->plan_accion_model->find_idParametro('Meta_En_Ela');
      $query = $this->plan_accion_model->listarMetasEstados($idPar->idpa);
      $estadosAsignados = $this->plan_accion_model->estadosAsignados($id);
      if ($query) {
        foreach ($query as $row) {
          if ($estadosAsignados) {
            foreach ($estadosAsignados as $estado) {
              if ($estado['id_estado'] == $row['idaux']) {
                if ($estado['noti'] == 1) {
                  $btnAccion = "$btnRetire $btnNotiOff";
                } else {
                  $btnAccion = "$btnRetire $btnNotiOn";
                }
                $row['acciones'] = $btnAccion;
                break;
              } else {
                $btnAccion = $btnAsig;
                $row['acciones'] = $btnAccion;
              }
            }
          } else {
            $btnAccion = $btnAsig;
          }
          $row['acciones'] = $btnAccion;
          $row['estado'] = $row['statusName'];
          array_push($newdata, $row);
        }
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Asignar formato a gestores - personas */
  public function asignarEstadoGestor()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $metaEstado = $this->input->post('metaEstado');
      $idGestor = $this->input->post('idGestor');

      if (empty($metaEstado) || empty($idGestor)) {
        $r = [];
      } else {
        $tabla = 'plan_accion_metas_estados';
        $datos = [
          'id_usuario_registra' => $_SESSION['persona'],
          'id_estado' => $metaEstado,
          'id_formato' => $idGestor
        ];
        $estadosAsignados = $this->plan_accion_model->estadosAsignados($idGestor);
        if ($estadosAsignados) {
          foreach ($estadosAsignados as $estado) {
            if ($estado['idFormat'] == $idGestor and $estado['id_estado'] == $metaEstado) {
              $r = ["mensaje" => "No es posible asignar un estado dos veces.", "tipo" => "warning", "titulo" => "¡Atención!"];
              exit(json_encode($r));
            }
          }
        }
        $query = $this->plan_accion_model->save_inf($tabla, $datos);
        if (empty($query)) {
          $r = ["mensaje" => "¡Estado asignado correctaente!", "tipo" => "success", "titulo" => "¡Bien!"];
        } else {
          $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
        }
      }
    }
    exit(json_encode($r));
  }

  /* Retirar estado a gestor */
  public function retirarEstadoGestor()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idGestor = $this->input->post('idGestor');
      $metaEstado = $this->input->post('metaEstado');
      if (empty($idGestor) || empty($metaEstado)) {
        $r = [];
      } else {
        $hoy = date('Y-m-d H:i:s');
        $tabla = 'plan_accion_metas_estados';
        $datos = ['id_usuario_elimina' => $_SESSION['persona'], 'estado' => 0, 'fecha_elimina' => $hoy];
        $where = ['id_formato' => $idGestor, 'id_estado' => $metaEstado];
        $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
        if (empty($query)) {
          $r = ["mensaje" => "¡Formato retirado correctaente!", "tipo" => "success", "titulo" => "¡Bien!"];
        } else {
          $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
        }
      }
    }
    exit(json_encode($r));
  }

  /* Activar notificaciones para los gestores */
  public function activarNotificacionesGestores()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idFormatoPrin = $this->input->post('idFormatoPrin');
      $metaEstado = $this->input->post('metaEstado');
      $tabla = 'plan_accion_metas_estados';
      $datos = ['notificacion' => 1];
      $where = ['id_formato' => $idFormatoPrin, 'id_estado' => $metaEstado];
      $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
      if (empty($query)) {
        $r = ["mensaje" => "¡Notificación activada correctaente!", "tipo" => "success", "titulo" => "¡Bien!"];
      } else {
        $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* Desactivar notificaciones para los gestores */
  public function desactivarNotificacionesGestores()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idFormatoPrin = $this->input->post('idFormatoPrin');
      $metaEstado = $this->input->post('metaEstado');
      $tabla = 'plan_accion_metas_estados';
      $datos = ['notificacion' => 0];
      $where = ['id_formato' => $idFormatoPrin, 'id_estado' => $metaEstado];
      $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
      if (empty($query)) {
        $r = ["mensaje" => "¡Notificación activada correctaente!", "tipo" => "success", "titulo" => "¡Bien!"];
      } else {
        $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }


  /* Traer idparametro basados en su id_aux o codigo que identifique el conjunto de info deseado pero que tenga un mismo idparametro */
  public function find_idParametro($codigoo = '', $return = true)
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      if (empty($codigoo)) {
        $codigoo = $this->input->post('codigo');
        $return =  false;
      }
      if (empty($codigoo)) {
        $r = [];
      } else {
        $query = $this->plan_accion_model->find_idParametro($codigoo);
        $r = $query;
      }
    }
    if ($return) {
      return $r;
    } else {
      exit(json_encode($r));
    }
  }

  /* Aquii */
  public function listarLidersAssigned2()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $newdata = [];
      $btnSee = '<span title="¡Ver Meta!" data-toggle="popover" data-trigger="hover" class="fa fa-eye red btn btn-default verMetas"></span>';
      $permisosAsigned = $this->plan_accion_model->permisosLideres('formato_institucional');
      foreach ($permisosAsigned as $permiso) {
        $permiso['acciones'] = "$btnSee";
        array_push($newdata, $permiso);
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Listar metas segun director */
  public function listarMetasDirector()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $psession = $_SESSION['persona'];
      $idArea = $this->input->post('idArea');
      $idLider = $this->input->post('idLider');
      $newdata = [];
      $btnCheck = '<span class="fa fa-check pointer btn btn-default metaSelect" style="color:#2ECC71;" title="¡Seleccionar Meta!"></span>';
      $btn_ver = '<span style="background-color: white;color: black; width: 100%;" class="btn btn-default pointer form-control seeDetails"><span>Ver</span></span>';
      $acciones = $this->plan_accion_model->listar_metas_programa($idArea, $idLider);
      foreach ($acciones as $accion) {
        $accion['ver'] = $btn_ver;
        $accion['acciones'] = $btnCheck;
        array_push($newdata, $accion);
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Listar programas */
  public function listarProgramas()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $newdata = [];
      $idMeta = $this->input->post('idMeta');
      $btnCheck = '<span class="fa fa-check pointer btn btn-default selectProgram" style="color:#2ECC71;" title="¡Seleccionar programa!"></span>';
      $btnReco = '<span class="fa fa-eye pointer btn btn-default red verRecs" title="¡Ver Recomendaciones de programa!"></span>';
      $btnAspectos = '<span class="fa fa-outdent pointer btn btn-default red verAsp" title="¡Ver aspectos positivos de programa!"></span>';
      $btnDel = '<span class="fa fa-times pointer btn btn-default delProgram" title="¡Desasignar!" style="color: #CA3E33;"></span>';
      $query = $this->plan_accion_model->listarProgramas($idMeta);
      if ($query) {
        foreach ($query as $row) {
          if ($row['programachecked'] != '' and $row['codigoAcre'] == 'programa_acreditado' and $row['metachecked'] == $idMeta) {
            $row['acciones'] = "$btnDel $btnReco $btnAspectos";
            $row['estado'] = 'Asignado';
          } else if ($row['programachecked'] != '' and $row['codigoAcre'] != 'programa_acreditado' and $row['metachecked'] == $idMeta) {
            $row['acciones'] = "$btnDel";
            $row['estado'] = 'Asignado';
          } else {
            $row['acciones'] = "$btnCheck";
            $row['estado'] = 'No Asignado';
          }
          array_push($newdata, $row);
        }
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Listar recomendaciones de programa */
  public function listarRecsPrograms()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idPrograma = $this->input->post('idPrograma');
      $idMeta = $this->input->post('idMeta');
      $newdata = [];
      $btnCheck = '<span class="fa fa-check pointer btn btn-default selectReco" style="color:#2ECC71;" title="¡Seleccionar recomendación!"></span>';
      $btnDel = '<span class="glyphicon glyphicon-remove pointer btn btn-default delReco" title="¡Desasignar!" style="color: #CA3E33;"></span>';
      $btnOff = '<span class="fa fa-toggle-off pointer btn btn-default" title="¡Sin acciones!"></span>';
      $idpa = $this->plan_accion_model->find_idParametro('recs_programs');
      $query = $this->plan_accion_model->listarRecsPrograms($idpa->idpa, $idPrograma, $idMeta);
      $btnAccion = '';
      $estado = '';
      if ($query) {
        foreach ($query as $row) {
          if ($row['idmeta'] == $idMeta and $row['idprograma'] == $idPrograma) {
            $btnAccion = $btnDel;
            $estado = 'Asignado';
          } else {
            $btnAccion = $btnCheck;
            $estado = "No Asignado";
          }
          $row['acciones'] = $btnAccion;
          $row['estado'] = $estado;
          array_push($newdata, $row);
        }
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }


  /* Guardar programas acreditados en tabla */
  public function guardar_programa_recomendacion()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idPrograma = $this->input->post('idPrograma');
      $idMeta = $this->input->post('idMeta');
      if (!empty($idPrograma) and !empty($idMeta)) {
        $tabla = 'plan_accion_programas_acreditados';
        $datos = [
          "id_meta" => $idMeta,
          "id_programa" => $idPrograma,
          "id_usuario_registra" => $_SESSION['persona']
        ];
        $query = $this->plan_accion_model->save_inf($tabla, $datos);
        if (empty($query)) {
          $r = true;
        } else {
          $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
        }
      } else {
        $r = ["mensaje" => "No se ha podido realizar esta operación.", "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* eliminar programas acreditados en tabla */
  public function eliminar_programa_recomendacion()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idPrograma = $this->input->post('idPrograma');
      $idMeta = $this->input->post('idMeta');
      if (!empty($idPrograma) && !empty($idMeta)) {
        $tabla = 'plan_accion_programas_acreditados';
        $tosend = [
          'id_usuario_elimina' => 1,
          'estado' => '0'
        ];
        $where = [
          "id_meta" => $idMeta,
          "id_programa" => $idPrograma
        ];
        $query = $this->plan_accion_model->upd_inf($tabla, $tosend, $where);
        echo $query;
        if (empty($query)) {
          $r = true;
        } else {
          $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
        }
      } else {
        $r = ["mensaje" => "Error #" . __LINE__ . ".", "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* Guardar recomendacion del programa seleccionado */
  public function saveRecomendacionPrograma()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idReco = $this->input->post('idReco');
      $idMeta = $this->input->post('idMeta');

      if (!empty($idReco) and !empty($idMeta)) {
        /* Verificamos que la recomendacion que se intenta asignar, haya sido asignada anteriormente. */
        $rec = $this->plan_accion_model->verificarAspectos($idMeta);
        $recoPrograma = $this->plan_accion_model->recoPrograma($idReco);

        if ($rec) {
          foreach ($rec as $item) {
            if ($item['idPrograma'] == $recoPrograma->idPrograma) {
              $r = ["mensaje" => "No es posible seleccionar esta recomendación debido a que este programa, ya posee un aspecto positivo diligenciado.", "tipo" => "warning", "titulo" => "¡Atención!"];
              exit(json_encode($r));
            }
          }
        }

        $verificar = $this->verificarRecomendaciones($idMeta, $idReco);

        if ($verificar == 1) {
          $tabla = 'plan_accion_recomendaciones_programas';
          $datos = [
            "id_meta" => $idMeta,
            "id_recomendacion" => $idReco,
            "id_usuario_registra" => $_SESSION['persona']
          ];
          $query = $this->plan_accion_model->save_inf($tabla, $datos);
          if (empty($query)) {
            $r = true;
          } else {
            $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
          }
        } else {
          $r = $verificar;
        }
      } else {
        $r = ["mensaje" => "No se ha podido realizar esta operación.", "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* Funcion para la verificacion de recomendaciones de programa asignados a una accion/meta */
  public function verificarRecomendaciones($idMeta, $idReco)
  {
    $verificar = $this->plan_accion_model->verificarRecomendacion($idMeta, $idReco);
    $check = $this->plan_accion_model->programaAsignado($idReco);
    //exit(json_encode($check));

    if ($verificar) {
      foreach ($verificar as $row) {
        if ($check) {
          if ($check->idPrograma == $row['idPrograma']) {
            $r = ["mensaje" => "No es posible seleccionar más de una recomendación.", "tipo" => "warning", "titulo" => "¡Atención!"];
            return $r;
          }
        }
      }
    }

    return 1;
  }

  /* Eliminar recomendacion del programa seleccionado */
  public function delRecomendacionPrograma()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idReco = $this->input->post('idReco');
      $idMeta = $this->input->post('idMeta');
      if (!empty($idReco) and !empty($idMeta)) {
        $tabla = 'plan_accion_recomendaciones_programas';
        $datos = [
          "id_usuario_elimina" => $_SESSION['persona'],
          "estado" => '0'
        ];
        $where = [
          "id_meta" => $idMeta,
          "id_recomendacion" => $idReco
        ];
        $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
        if (empty($query)) {
          $r = true;
        } else {
          $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
        }
      } else {
        $r = ["mensaje" => "No se ha podido realizar esta operación.", "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* Guardar recomendacion del programa seleccionado */
  public function saveAspectosPositivos()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idAsp = $this->input->post('idAsp');
      $idMeta = $this->input->post('idMeta');

      $rec = $this->plan_accion_model->verificarRecomendacion($idMeta);
      $programaAspecto = $this->plan_accion_model->aspectoPrograma($idAsp);
      $aspCheck = $this->verificarAspectos($idMeta, $idAsp);

      //Verificamos que no haya mas de un aspecto seleccionado.
      if ($aspCheck != 1) {
        $r = $aspCheck;
        exit(json_encode($r));
      }

      if ($rec) {
        foreach ($rec as $item) {
          if ($item['idPrograma'] == $programaAspecto->idPrograma) {
            $r = ["mensaje" => "No es posible seleccionar un aspecto positivo debido a que este programa, ya posee una Recomendación diligenciada.", "tipo" => "warning", "titulo" => "¡Atención!"];
            exit(json_encode($r));
          }
        }
      }

      if (!empty($idAsp) and !empty($idMeta)) {
        $tabla = 'plan_accion_aspectos_positivos';
        $datos = [
          "id_meta" => $idMeta,
          "id_aspecto" => $idAsp,
          "id_usuario_registra" => $_SESSION['persona']
        ];
        $query = $this->plan_accion_model->save_inf($tabla, $datos);
        if (empty($query)) {
          $r = true;
        } else {
          $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
        }
      } else {
        $r = ["mensaje" => "No se ha podido realizar esta operación.", "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* Funcion para la verificacion de recomendaciones de programa asignados a una accion/meta */
  public function verificarAspectos($idMeta, $idReco)
  {
    $verificar = $this->plan_accion_model->verificarAspectos($idMeta, $idReco);
    $check = $this->plan_accion_model->programaAsignado($idReco);
    //exit(json_encode($check));

    if ($verificar) {
      foreach ($verificar as $row) {
        if ($check) {
          if ($check->idPrograma == $row['idPrograma']) {
            $r = ["mensaje" => "No es posible seleccionar más de un aspecto positivo.", "tipo" => "warning", "titulo" => "¡Atención!"];
            return $r;
          }
        }
      }
    }

    return 1;
  }

  /* Eliminar recomendacion del programa seleccionado */
  public function delAspectosPositivos()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idAsp = $this->input->post('idAsp');
      $idMeta = $this->input->post('idMeta');
      if (!empty($idAsp) and !empty($idMeta)) {
        $tabla = 'plan_accion_aspectos_positivos';
        $datos = [
          "id_usuario_elimina" => $_SESSION['persona'],
          "estado" => '0'
        ];
        $where = [
          "id_meta" => $idMeta,
          "id_aspecto" => $idAsp
        ];
        $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
        if (empty($query)) {
          $r = true;
        } else {
          $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
        }
      } else {
        $r = ["mensaje" => "No se ha podido realizar esta operación.", "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* Listar aspectos positivos AQUIIII*/
  public function listarAspectosPositivos()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idPrograma = $this->input->post('idPrograma');
      $idMeta = $this->input->post('idMeta');
      $newdata = [];
      $btnCheck = '<span class="fa fa-check pointer btn btn-default selectAsp" style="color:#2ECC71;" title="¡Seleccionar recomendación!"></span>';
      $btnDel = '<span class="glyphicon glyphicon-remove pointer btn btn-default delAsp" title="¡Desasignar!" style="color: #CA3E33;"></span>';
      $btnOff = '<span class="fa fa-toggle-off pointer btn btn-default" title="¡Sin acciones!"></span>';
      $idpa = $this->plan_accion_model->find_idParametro('aspectos_positivos');
      $query = $this->plan_accion_model->listarAspectosPositivos($idpa->idpa, $idPrograma, $idMeta);
      $btnAccion = '';
      $estado = '';
      if ($query) {
        foreach ($query as $row) {
          if ($row['idmeta'] == $idMeta) {
            $btnAccion = $btnDel;
            $estado = 'Asignado';
          } else {
            $btnAccion = $btnCheck;
            $estado = "No Asignado";
          }
          $row['acciones'] = $btnAccion;
          $row['estado'] = $estado;
          array_push($newdata, $row);
        }
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* Reset cronograma */
  public function resetCrono($idm = '', $row = false)
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      empty($idm) ? $idMeta = $this->input->post('idMeta') : $idMeta = $idm;

      //exit(json_encode($idm));

      if (!empty($idMeta)) {
        //Reset de cronograma completo en su tabla principal cronograma_plan_accion
        $tabla = 'cronograma_plan_accion';
        $tosend = ['estado' => 0, 'id_usuario_elimina' => $_SESSION['persona']];
        $where = ['id_meta' => $idMeta];
        $query = $this->plan_accion_model->upd_inf($tabla, $tosend, $where);

        if (empty($query)) {
          //Reset de acciones, donde guardan nombre de documentos soporte y lo demas.
          $tabla = 'plan_accion_acciones';
          $tosend = ['estado' => 0, 'id_usuario_elimina' => $_SESSION['persona']];
          $where = ['id_meta' => $idMeta];
          $query = $this->plan_accion_model->upd_inf($tabla, $tosend, $where);
          if (empty($query)) {
            $r = ["mensaje" => "El cronograma se ha restablecido correctamente!", "tipo" => "success", "titulo" => "¡Bien!"];
          } else {
            $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
          }
        } else {
          $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
        }
      } else {
        $r = ["mensaje" => "Error #" . __LINE__ . ".", "tipo" => "error", "titulo" => ""];
      }
    }
    if ($row) {
      return 1;
    } else {
      exit(json_encode($r));
    }
  }

  /* Listar programas CUC para los que se les va a asignar programas en el administrar al crear lider. */
  public function listarProgramasCuc()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idPersona = $this->input->post('idPersona');
      $newdata = [];
      $btnCheck = '<span class="fa fa-check pointer btn btn-default selectProgram" style="color:#2ECC71;" title="¡Seleccionar programa!"></span>';
      $btnDel = '<span class="fa fa-times pointer btn btn-default delProgram" title="¡Desasignar!" style="color: #CA3E33;"></span>';
      $query = $this->plan_accion_model->listarProgramasCuc($idPersona);
      if ($query) {
        foreach ($query as $row) {
          if (!empty($idPersona)) {
            if ($row['personaAsignado'] == $idPersona) {
              $row['acciones'] = $btnDel;
              $row['estado'] = 'Asignado';
              array_push($newdata, $row);
            } else {
              $row['acciones'] = $btnCheck;
              $row['estado'] = 'No Asignado';
              array_push($newdata, $row);
            }
          }
        }
      }
      $r = $newdata;
    }
    exit(json_encode($r));
  }

  /* MEJORAR */
  /* Asignar programas a las pesonas desde el administrar */
  public function asignarProgramasCuc()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idPrograma = $this->input->post('idPrograma');
      $idPersona = $this->input->post('idPersona');
      $tabla = 'plan_accion_personas_programas';
      $datos = [
        "id_programa" => $idPrograma,
        "id_persona" => $idPersona,
        "id_usuario_registra" => $_SESSION['persona']
      ];
      $query = $this->plan_accion_model->save_inf($tabla, $datos);
      if (empty($query)) {
        $r = true;
      } else {
        $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* MEJORAR */
  /* ELiminar pesonas de la tabla de programa, asignados en administrar */
  public function delProgramasCuc()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idPrograma = $this->input->post('idPrograma');
      $idPersona = $this->input->post('idPersona');
      $tabla = 'plan_accion_personas_programas';
      $datos = [
        "id_usuario_elimina" => $_SESSION['persona'],
        "estado" => '0'
      ];
      $where = [
        "id_programa" => $idPrograma,
        "id_persona" => $idPersona
      ];
      $query = $this->plan_accion_model->upd_inf($tabla, $datos, $where);
      if (empty($query)) {
        $r = true;
      } else {
        $r = ["mensaje" => $query, "tipo" => "error", "titulo" => ""];
      }
    }
    exit(json_encode($r));
  }

  /* Traer factores institucionales para los detalles de la meta */
  public function traerFactoresIns()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idmeta = $this->input->post('idMeta');
      $btnEye = '<span title="¡Ver características de este factor!" class="fa fa-eye btn btn-default verCaracts red"></span>';
      if (!empty($idmeta)) {
        $newdata = [];
        $query = $this->plan_accion_model->traerFactoresIns($idmeta);
        if ($query) {
          foreach ($query as $row) {
            $row['acciones'] = $btnEye;
            array_push($newdata, $row);
          }
        }
        $r = $newdata;
      } else {
        $r = [];
      }
    }
    exit(json_encode($r));
  }

  public function traerCaracteristicasFactoresIns()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idmeta = $this->input->post('idMeta');
      $valora = $this->input->post('valora');
      if (!empty($idmeta) and !empty($valora)) {
        $query = $this->plan_accion_model->traerCaractsFactoresIns($idmeta, $valora);
        $r = $query;
      } else {
        $r = [];
      }
    }
    exit(json_encode($r));
  }

  /* Traer repsonsables para el modal de detalles de la meta */
  public function traerResponsables()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idmeta = $this->input->post('idMeta');
      if (!empty($idmeta)) {
        $query = $this->plan_accion_model->responsables_asignados($idmeta);
        $r = $query;
      } else {
        $r = [];
      }
    }
    exit(json_encode($r));
  }

  /* Traer presupuesto de la meta seleccionada y renderizar en detalles de la meta */
  public function traerPresupuestos()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idmeta = $this->input->post('idMeta');
      if (!empty($idmeta)) {
        $query = $this->plan_accion_model->traerPresupuestos($idmeta);
        $r = $query;
      } else {
        $r = [];
      }
    }
    exit(json_encode($r));
  }

  /* Traer presupuesto de la meta seleccionada y renderizar en detalles de la meta */
  public function traerCronograma()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idmeta = $this->input->post('idMeta');
      $newdata = [];
      $btnEye = '<span title="¡Ver nombre de documentos soporte!" class="fa fa-eye btn btn-default verDocsName red"></span>';
      if (!empty($idmeta)) {
        $query = $this->plan_accion_model->traerCronograma($idmeta);
        if ($query) {
          foreach ($query as $row) {
            $row['acciones'] = $btnEye;
            array_push($newdata, $row);
          }
        }
        $r = $newdata;
      } else {
        $r = [];
      }
    }
    exit(json_encode($r));
  }

  /* Traer presupuesto de la meta seleccionada y renderizar en detalles de la meta */
  public function traerDocsSoporte()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $idmeta = $this->input->post('idMeta');
      $idCrono = $this->input->post('cronoId');
      if (!empty($idmeta) and !empty($idCrono)) {
        $query = $this->plan_accion_model->traerDocsSoporte($idmeta, $idCrono);
        $r = $query;
      } else {
        $r = [];
      }
    }
    exit(json_encode($r));
  }


  /* Check si el modulo esta abierto */
  public function timeCheck($return = false, $formatoActivo = '')
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $administrar = false;
      $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro" ? $administrar = true : $administrar = false;
      $cerrar = false;

      if ($administrar) {
        $cerrar = false;
        $r = $cerrar;
      } else {
        empty($formatoActivo) ? $formatoActivo = $this->input->post('idFormato') : false;
        //exit(json_encode($formatoActivo));
        $parametro = $this->plan_accion_model->find_idParametro($formatoActivo);
        $hoy = date('Y-m-d H:i:s');
        $cerrar = false;
        $hoy > $parametro->vb ? $cerrar = true : $cerrar = false;
        if ($cerrar) {
          $r = ["mensaje" => "El proceso para la creación de acciones ya se encuentra cerrado. Para más información puede comunicarse con el departamento de planeación.", "tipo" => "info", "titulo" => "¡Atención!"];
        } else {
          $r = $cerrar;
        }
      }
    }

    if (!$return) {
      exit(json_encode($r));
    } else {
      return $r;
    }
  }

  /* Listar formatos activos */
  public function formatosActivos()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $query = $this->plan_accion_model->formatoActivo();
      if ($query) {
        $r = $query;
      } else {
        $r = ["mensaje" => "Para crear una acción, debe tener una formato de plan de acción activo.", "tipo" => "warning", "titulo" => "¡Atención!"];
      }
    }
    exit(json_encode($r));
  }

  /* Funcion para verificar que al momento de cambiar de estado, se hayan diligenciado todos los datos como el formulario principal, cronograma y etc  */
  public function verificarDatosMetas($idMeta = '')
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $r = [];
      $verificar = $this->plan_accion_model->verificarDatosMetas($idMeta);
      $r = $verificar;
    }
    //exit(json_encode($r));
    return $r;
  }

  /* Enviar correos en cambio de estado (Solo cuando se manda a correccion por el momento) */
  public function enviar_correos_estados($datos)
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "tipo" => ""];
    } else {
      $baseurl = base_url();
      //$correo = "fjaramil@cuc.edu.co";
      //$nombre = "Fabio Jaramillo";
      $correo = $datos->usuario_regis_mail;
      $nombre = $datos->usuario_registra;
      $msg = "
        Cordial saludo.<br>
        Usted, posee una acción pendiente por corregir.<br><br>

        <strong>Código único de acción:</strong> <i>$datos->id.</i> <br>
        <strong>Observación:</strong> <i>$datos->obs.</i> <br><br>

        Para empezar la corrección favor entrar a: <a href='" . $baseurl . "index.php/plan_accion'>AGIL</a> y en el ítem llamado 'Lista de Acciones', en la parte superior derecha de la tabla acciones podrá filtrar por código único de Acción lo que debe corregir.<br>
        ¡Muchas gracias por la atención prestada!
      ";
      $desde = "Plan de Acción - CUC";
      $asunto = "¡PDA - Corrección de acción!";
      $codigoo = "ParCodAdm";
      $notificar = $this->enviar_correo_personalizado("comp", $msg, $correo, $nombre, $desde, $asunto, $codigoo, 1);
      if ($notificar != 1) {
        exit(json_encode(["mensaje" => "No se pudo enviar el correo de notificación.", "tipo" => "warning", "titulo" => "Oops"]));
      } else {
        $r = $notificar;
      }
    }
    return $r;
  }

  /* Funcion que permite saber desde el js el formato activo */
  public function formatoActivoPersona()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $query = $this->plan_accion_model->formatoActivo();
      $r = $query;
    }
    exit(json_encode($r));
  }

  /* Generar resumen de acciones unificado */
  public function generarDatosDBPro()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $admin = false;
      $tp = $_SESSION['perfil'];
      $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro" ? $admin = true : $admin = false;

      if ($admin) {
        $fi = $this->plan_accion_model->find_idParametro('formato_institucional');
        $fp = $this->plan_accion_model->find_idParametro('formato_programa');
        $fa = "";

        if ($tp == "Plan_Accion_Admin") {
          $fa = $fi->id;
        } else if ($tp == "Plan_Accion_Admin_Pro") {
          $fa = $fp->id;
        } else {
          $fa = "";
        }

        $query = $this->plan_accion_model->generarDatosDBPro($fa);
        $r = $query;
      } else {
        $r = ["mensaje" => "No posee permisos suficientes para generar esta información.", "tipo" => "warning", "titulo" => "¡Atención!"];
      }
    }
    exit(json_encode($r));
  }

  /* Generar resumen de presupuestos de todas las acciones */
  public function generarDatosDBpresu()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      $admin = false;
      $tp = $_SESSION['perfil'];
      $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro" ? $admin = true : $admin = false;

      if ($admin) {
        $fi = $this->plan_accion_model->find_idParametro('formato_institucional');
        $fp = $this->plan_accion_model->find_idParametro('formato_programa');
        $fa = "";

        if ($tp == "Plan_Accion_Admin") {
          $fa = $fi->id;
        } else if ($tp == "Plan_Accion_Admin_Pro") {
          $fa = $fp->id;
        } else {
          $fa = "";
        }

        $query = $this->plan_accion_model->generarDatosDBpresu($fa);
        $r = $query;
      } else {
        $r = ["mensaje" => "No posee permisos suficientes para generar esta información.", "tipo" => "warning", "titulo" => "¡Atención!"];
      }
    }
    exit(json_encode($r));
  }


  /* Funcion puesta aqui, para enviar correos electronicos mediante el backend si se va a trabajar con informacion confidencial */
  public function enviar_correo_personalizado($llama = "", $mensajee = "", $correoo = "", $nombre_recibe = "", $fromm = "", $adjuntoo = "", $codigoo = "", $tipoo = "", $archivoo = "", $externoo = false)
  {
    $estructura = "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'><html xmlns='http://www.w3.org/	1999/xhtml'>
			<head>
				<style>
					body{
						font-size: 16px;
						font-family: 'Helvetica Neue', 'Helvetica', 'Arial', 'sans-serif';
						line-height: 1.5;
					}
					table {
						border-collapse: collapse;
						width: 100%;
					}
					td, th {
						border: 1px solid #ddd;
						padding: 8px; 
					}
					tr:hover {background-color: #ddd;}
					.fila_principal {
						padding-top: 12px;
						padding-bottom: 12px;
						text-align: center;
						background-color: #337ab7;
						color: white;
					}
				</style>
			</head>
			<body>
				<p style='padding:0px;margin:0px;'>Señor(a)</p> ";
    $this->load->model('genericas_model');
    empty($mensajee) ? $mensaje = $this->input->post("mensaje") : $mensaje = $mensajee;
    empty($correoo) ? $correo = $this->input->post("correo") : $correo = $correoo;
    empty($codigoo) ? $cod = $this->input->post("codigo") : $cod = $codigoo;
    empty($fromm) ? $from = $this->input->post("from") : $from = $fromm;
    empty($adjuntoo) ? $adj = $this->input->post("adjunto") : $adj = $adjuntoo;
    empty($tipoo) ? $tipo = $this->input->post("tipo") : $tipo = $tipoo;
    empty($archivoo) ? $archivo = $this->input->post('archivo') : $archivo = $archivoo;
    empty($nombre_recibe) ? $nombre_completo = $this->input->post("nombre") : $nombre_completo = $nombre_recibe;
    empty($empty) ? $externo = $this->input->post("externo") : $externo = $externoo;
    if ($tipo == -1) {
      $nombre_completo = $_SESSION["nombre"] . " " . $_SESSION['apellido'];
      $correo = $_SESSION["correo"];
    }

    $estructura .= $externo ? "<h3>" . strtoupper($nombre_completo) . "</h3>" : "<h3>" . strtoupper($nombre_completo) . "</h3></br>";

    if (empty($correo)) {
      echo json_encode(-2);
      return;
    } else if (empty($mensaje)) {
      echo json_encode(-3);
      return;
    } else if (empty($cod)) {
      echo json_encode(-4);
      return;
    }
    $datos_correo = $this->genericas_model->obtener_valores_parametro_aux($cod, 20);
    if (empty($datos_correo)) {
      echo json_encode(-1);
      return;
    }
    $mensaje = $estructura . $mensaje . "<p>Estamos atentos a cualquier inquietud o sugerencia.</p></body></html>";
    $datos_correo = $datos_correo[0];
    $email = $datos_correo["valor"];
    $password = $datos_correo["valory"];
    require_once(APPPATH . 'libraries/phpmailer/autoload.php');
    $mail = new PHPMailer(true); // Passing `true` enables exceptions
    try {
      $mail->SMTPDebug = 0; // Enable verbose debug output
      $mail->isSMTP(); // Set mailer to use SMTP
      $mail->Host = 'smtp.office365.com'; // Specify main and backup SMTP servers
      $mail->SMTPAuth = true; // Enable SMTP authentication
      $mail->CharSet = 'utf8';
      $mail->Username = $email; // SMTP username
      $mail->Password = $password; // SMTP password
      $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587; // TCP port to connect to
      $mail->setFrom($email, $from);
      if ($tipo == 3) {
        foreach ($correo as $row) {
          $mail->addAddress($row["correo"], ucfirst(strtolower($row["persona"]))); // Add a recipient
        }
      } else $mail->addAddress($correo, ucfirst(strtolower($nombre_completo))); // Add a recipient
      $mail->Subject = $adj;
      $mail->Body = $mensaje;
      // 0 Path
      // 1 File Name
      if (!empty($archivo)) {
        if (is_array($archivo[0])) {
          foreach ($archivo as $row) {
            $mail->AddAttachment(realpath(APPPATH . $row[0]), $row[1]);
          }
        } else $mail->AddAttachment(realpath(APPPATH . $archivo[0]), $archivo[1]);
      }
      $mail->isHTML(true);                                  // Set email format to HTML
      //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
      $mail->send();
      return (json_encode(1));
    } catch (Exception $e) {
      exit(json_encode(0));
    }
  }
}

/* Funcion base */
  /* public function cam()
  {
    if (!$this->super_estado) {
      $r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
    } else {
      //code
    }
    exit(json_encode($r));
  } */

//Avisos por defecto

// $r = ["mensaje" => "", "tipo" => "warning", "titulo" => "¡Atención!"];
