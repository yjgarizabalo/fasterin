<?php

/* idparametro 37 es de proveedores. */

date_default_timezone_set('America/Bogota');
class contrataciones_control extends CI_Controller
{
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
	var $admin = false;
	var $super_admin = false;
	var $ruta_adjuntos = "archivos_adjuntos/contrataciones";
	public function __construct()
	{
		parent::__construct();
		$this->load->model('contrataciones_model');
		$this->load->model('compras_model');
		$this->load->model('genericas_model');
		$this->load->model('pages_model');
		include('application/libraries/festivos_colombia.php');
		session_start();
		if (isset($_SESSION["usuario"])) {
			$this->Super_estado = true;
			$this->Super_elimina = 1;
			$this->Super_modifica = 1;
			$this->Super_agrega = 1;
			if ($_SESSION['perfil'] == 'Per_Admin' || $_SESSION['perfil'] == 'Admin_Cont') {
				$this->super_admin = true;
				$this->admin = true;
			}
		}
	}

	public function index($id = '')
	{
		$pages = "contrataciones";
		if ($this->Super_estado) {
			$datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $pages);
			$actividad = $this->obtener_permisos_actividades("", "", "", $_SESSION['persona']);	
			$perms = false;		
			$perms = $perms = $actividad ?  true : false;		
			
			if (!empty($datos_actividad)) {
				$data['js'] = "contrataciones";
				$data['id'] = $id;
				$data['actividad'] = $datos_actividad[0]["id_actividad"];
				$data['perm'] = $perms;				
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

	/* Buscar Numero de contrato macro "ncm" */
	public function buscar_ncm()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$ncm_seach = $this->input->post('dato_buscar');
			$r = $this->contrataciones_model->buscar_ncm($ncm_seach, $this->find_idParametro('ncm')->idpa);
			echo json_encode($r);
		}
	}

	/* Buscar CodSAP */
	public function buscar_codsap()
	{
		if (!$this->Super_estado) {
			$resul_sap = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$codsap = $this->input->post('dato_buscar');
			$resul_sap = $this->contrataciones_model->Buscar_CodSap($codsap);
			echo json_encode($resul_sap);
		}
	}

	/* Buscar contratantes */
	public function buscar_contratante()
	{
		$resul_contratante = array();
		if (!$this->Super_estado) {
			$resul_contratante = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$contratante = $this->input->post('dato_buscar');
			$resul_contratante = $this->contrataciones_model->Buscar_Contratante($contratante, $this->find_idParametro('Corp_Unicuc')->idpa);
			echo json_encode($resul_contratante);
		}
	}

	/* Buscar contratistas */
	public function buscar_contratista()
	{
		$resul_contratista = [];
		if (!$this->Super_estado) {
			$resul_contratista = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$idpa = '';
			$dato_buscar = $this->input->post('dato_buscar');
			$valor_parametro = $this->find_idParametro('contra_tistas');
			if (!empty($valor_parametro)) {
				$idpa = $valor_parametro->idpa;
				$resul_contratista = $this->contrataciones_model->Buscar_Contratista($dato_buscar, $idpa);
			}else{
				$resul_contratista = $this->contrataciones_model->Buscar_Contratista($dato_buscar);
			}
			echo json_encode($resul_contratista);
		}
	}

	public function listar_administrar_contratistas (){
		$resul_contratista = [];
		if (!$this->Super_estado) {
			$resul_contratista = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$admin = $_SESSION['perfil'] == "Per_Admin" || $_SESSION['perfil'] == "Admin_Cont" ? true : false;
			$btn_invalido = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="action-buttons fa fa-toggle-off"></span>';
			$idparametro = $this->input->post('idparametro');
			$result = $this->contrataciones_model->listar_administrar_contratistas($idparametro);
			foreach ($result as $row) {
				$row['accion'] = $admin ? '<span title="Eliminar" style="color: #DE4D4D;" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o pointer btn btn-default" onclick="confirmar_eliminar_parametro('.$row['id'].',0, 1)"></span><span style="color: #2E79E5;" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench pointer btn btn-default" onclick="mostrar_contratista_modificar('.$row['id'].')"></span>' : "$btn_invalido";
				array_push($resul_contratista, $row);
			}
			echo json_encode($resul_contratista);
		}
	}

	public function guardar_contrato()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$num_contrato = $this->input->post('ncm');
			$num_contra_auto = rand(100, 50000);
			$contratante = $this->input->post('tante_select');
			$codsap = $this->input->post('codSap_id');
			$contratista = $this->input->post('tista_id');
			$objetivo = $this->input->post('objetivo');
			$pago_valor = $this->input->post('pago_valor');
			$plazo = $this->input->post('plazo');
			$garantia = $this->input->post('contrato_garantia');
			$fecha_ini = $this->input->post('fecha_inicio');
			$fecha_ter = $this->input->post('fecha_termina');
			$tipo_contrato = $this->input->post("tipo_contrato");
			$tipos_de_adjs = json_decode($this->input->post("tipos_adj"));
			$adjs_names = json_decode($this->input->post("adjs_names"));
			$id_tipo_persona = $this->input->post("tipo_persona");
			$usuario_registra = $_SESSION['persona'];

			/*$check_fi = $this->pages_model->validateDate($fecha_ini, 'Y-m-d');
			if ($check_fi == false) {
				$r = ['mensaje' => "Verifique la Fecha de Inicio esté diligenciada correctamente!", 'tipo' => "error", 'titulo' => "Oops"];
				echo json_encode($r);
				exit();
			}*/

			$check_fter = $this->pages_model->validateDate($fecha_ter, 'Y-m-d');
			if ($check_fter == false) {
				$r = ['mensaje' => "Verifique la Fecha de Finalización esté diligenciada correctamente", 'tipo' => "error", 'titulo' => "Oops"];
				exit(json_encode($r));
			}

			/* Subir los archivos adjuntos */
			$archivos = [];
			if (empty($_FILES)) {
				$r = ['mensaje' => "No se ha enviado ningún adjunto, verifique e intente nuevamente.", 'tipo' => "error", 'titulo' => "Oops"];
				exit(json_encode($r));
			} else {
				for ($i = 0; $i < count($_FILES); $i++) {
					$cargar = $this->cargar_archivo($adjs_names[$i], "archivos_adjuntos/contrataciones", "Cont");
					if ($cargar[0] == -1) {
						$r = ['mensaje' => "Error al cargar el adjunto: " . $tipos_de_adjs[$i] . ".", 'tipo' => "error", 'titulo' => "Oops"];
						exit(json_encode($r));
					}
					array_push($archivos, $cargar[1]);
				}
			}

			$arrayTocheck = [];
			$arrayTocheck = [
				"Contrato Macro" => $num_contrato,
				"Código SAP" => $codsap,
				"Contratante" => $contratante,
				"Contratista" => $contratista,
				"Objetivo" => $objetivo,
				"Valor del Contrato" => $pago_valor,
				"Plazo determiado" => $plazo,
				"Garantía" => $garantia,
				"Fecha de Inicio" => $fecha_ini,
				"Fecha de Finalización" => $fecha_ter,
				"Usuario Responsable" => $usuario_registra,
				"Tipo de contrato" => $tipo_contrato,
				"Tipo de persona" => $id_tipo_persona
			];

			$check = $this->pages_model->verificar_campos_string($arrayTocheck);

			if (is_array($check)) {
				$r = ['mensaje' => "Verifique que el campo " . $check['field'] . " que esté diligenciado correctamente!", 'tipo' => "error", 'titulo' => "Oops"];
				echo json_encode($r);
				exit();
			}

			$dataSend = [
				"num_contrato_macro" => $num_contrato,
				"num_contrato" => $num_contra_auto,
				"cod_sap" => $codsap,
				"contratante" => $contratante,
				"contratista" => $contratista,
				"objetivo" => $objetivo,
				"valor" => $pago_valor,
				"plazo" => $plazo,
				"id_garantia" => $garantia,
				"fecha_inicio" => $fecha_ini,
				"fecha_terminacion" => $fecha_ter,
				"contrato_estado" => "Cont_Soli_E",
				"id_usuario_registra" => $usuario_registra,
				"tipo_contrato" => $tipo_contrato,
				"id_tipo_persona" => $id_tipo_persona
			];

			$save_in_contrataciones = $this->contrataciones_model->Guardar_Info("contrataciones", $dataSend);

			if ($save_in_contrataciones == true) {
				$sendToEstados = [
					"id_usuario_registra" => $usuario_registra,
					"id_estado" => "Cont_Soli_E",
					"id_solicitud" => $this->last_contra()->id,
					"observacion" => "Contrato solicitado."
				];
				$query_estado = $this->contrataciones_model->Guardar_Info("contrataciones_estados", $sendToEstados);
				if ($query_estado == true) {
					$sendToAdj = [];
					for ($x = 0; $x < count($tipos_de_adjs); $x++) {
						$sendToAdj = [
							"id_solicitud" => $this->last_contra()->id,
							"nombre_guardado" => $archivos[$x],
							"nombre_real" => $tipos_de_adjs[$x],
							"id_usuario_registra" => $_SESSION['persona']
						];
						$save_in_contsAdj = $this->contrataciones_model->Guardar_Info("contrataciones_adjuntos", $sendToAdj);
						if ($save_in_contsAdj == false) {
							$r = ["mensaje" => "El Adjunto: " . $tipos_de_adjs[$x] . " no se ha cargado correctamnete en base de datos.", "tipo" => "error", "titulo" => "Error!"];
						}
					}
				}
			}
			$r = ["mensaje" => "La operación se realizó correctamente!", "tipo" => "success", "titulo" => "Proceso  exitoso!"];
			exit(json_encode($r));
		}
	}

	/* Contrataciones adjuntos, funcion para pintar los inputs que van a requerir la info. */
	public function call_adjs()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$tipo_persona = $this->input->post("tps");
			$query = $this->contrataciones_model->call_adjs($tipo_persona, $this->find_idParametro('Contrataciones Adjuntos')->id);
			if ($query) {
				$r = $query;
			} else {
				$r = ['mensaje' => "No hay adjuntos disponibles para esta solicitud.", 'tipo' => "error", 'titulo' => "Oops"];
			}
		}
		echo json_encode($r);
	}

	/* Trae el ultimo id del contrato solicitado */
	public function last_contra()
	{
		if (!$this->Super_estado) {
			echo json_encode(['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]);
		} else {
			return $this->contrataciones_model->Last_Contra();
		}
	}

	public function obtener_ultimo_contrato_usuario_registra()
    {
		if (!$this->Super_estado) {
			echo json_encode(['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]);
		} else {
			$res = $this->contrataciones_model->obtener_ultimo_contrato_usuario_registra($_SESSION['persona']);
			echo json_encode($res);
		}
    }

	/* Listar contratos */
	public function listar_contratos()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$admin = $_SESSION['perfil'] == "Per_Admin" || $_SESSION['perfil'] == "Admin_Cont" ? true : false;
			$dato = $this->input->post('dato_buscar');
			$result = $this->contrataciones_model->Listar_Contratos($dato);						
			$contratos = [];

			$btn_ver = '<span style="width: 100%; color: black; border: 1px solid rgba(0,0,0, 0.3); background-color: rgba(255, 255, 255, 0.5);" class="action-buttons btn btn-default ver_contratos">ver</span>';
			$btn_ver_ela = '<span style="width: 100%; border: 1px solid rgba(0,0,0, 0.3); color: white; background-color: rgba(213,140,28,0.8);" class="action-buttons btn btn-default ol ver_contratos">ver</span>';
			$btn_ver_listo = '<span style="width: 100%; border: 1px solid rgba(0,0,0, 0.3); color: white; background-color: rgba(46,204,113,0.9);" class="action-buttons btn btn-default ver_contratos">ver</span>';
			$btn_ver_neg = '<span style="width: 100%; border: 1px solid rgba(0,0,0, 0.3); color: white; background-color: #DE3249;" class="action-buttons btn btn-default ver_contratos">ver</span>';
			$btn_ver_acep = '<span style="width: 100%; border: 1px solid rgba(0,0,0, 0.3); color: white; background-color: #35AB64;" class="action-buttons btn btn-default ver_contratos">ver</span>';
			$btn_invalido = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="action-buttons fa fa-toggle-off"></span>';
			$btn_aceptar = '<span title="Aceptar" data-toggle="popover" data-trigger="hover" class="action-buttons fa fa-thumbs-up btn btn-default aceptar" style="color:#5cb85c"></span>';
			$btn_rechazar = '<span title="Rechazar" data-toggle="popover" data-trigger="hover" class="action-buttons fa fa-thumbs-down btn btn-default rechazar" style="color:#d9534f"></span>';
			$btn_firma = '<span title="Firmar" data-toggle="popover" data-trigger="hover" class="action-buttons fa fa-pencil btn btn-default firma" style="color:#5cb85c"></span>';
			$btn_task = '<span title="Tareas" data-toggle="popover" data-trigger="hover" class="action-buttons fa fa-pencil btn btn-default fa fa-tasks listar_tareas" style="color:#5cb85c"></span>';
			$btn_adj_con = '<span title="Adjuntar contrato" data-toggle="popover" data-trigger="hover" class="action-buttons fa fa-file-pdf-o btn btn-default adj_contrato" style="color:#DE3249"></span>';
			$btn_espera = '<span title="Esperar firma" data-toggle="popover" data-trigger="hover" class="action-buttons fa fa-hourglass-start btn btn-default espera" style="color:#5cb85c"></span>';
			$btn_enviar_compras = '<span title="Enviar a compras" data-toggle="popover" data-trigger="hover" class="action-buttons fa fa-paper-plane btn btn-default enviar_compras" style="color:#5cb85c"></span>';			
			
			
			foreach ($result as $row) {
				$perm = false;
				$actividad = $this->obtener_permisos_actividades($row['estado_cont'], $row['tipo_contrato'], '', $_SESSION['persona']);					
				$perm = $actividad ?  true : false;	

				if ($row['estado_cont'] == "Cont_Soli_E"){										
					$row['ver'] = $btn_ver;
					$row['accion'] = $admin || $perm ? "$btn_aceptar $btn_rechazar" : "$btn_invalido";
				} else if ($row['estado_cont'] == "Cont_Rec_E") {
					$row['ver'] = $btn_ver_neg;
					$row['accion'] = $admin || $perm ? "$btn_invalido" : "$btn_invalido";
				} else if ($row['estado_cont'] == "Cont_En_Ela") {
					$row['ver'] = $btn_ver_acep;
					$row['accion'] = $admin || $perm ? "$btn_adj_con $btn_rechazar" : "$btn_invalido";
				} else if ($row['estado_cont'] == "Cont_Secr_Avl") {
					$row['ver'] = $btn_ver_ela;
					$row['accion'] = $admin || $perm ? "$btn_aceptar $btn_rechazar" : "$btn_invalido";					
				} else if ($row['estado_cont'] == "Cont_En_Firm") {
					$btns = !is_null($row['firma_contratante']) && $row['firma_contratante'] != "" ? $btn_espera : "$btn_firma $btn_rechazar";
					$row['ver'] = $btn_ver_ela;
					$row['accion'] = $admin || $perm ? $btns : "$btn_invalido";
				} else if ($row['estado_cont'] == "Cont_En_Ver") {
					$btn_gar = $row['garantia_id'] === 'con_g' ? $btn_task : $btn_aceptar;
					$row['ver'] = $btn_ver_ela;
					$row['accion'] = $admin || $perm ? "$btn_gar $btn_rechazar" : "$btn_invalido";
				} else if ($row['estado_cont'] == "Cont_En_Comp") {
					$row['ver'] = $btn_ver_ela;
					$row['accion'] = $admin || $perm ? "$btn_enviar_compras $btn_rechazar" : "$btn_invalido";
				} else if ($row['estado_cont'] == "Cont_Ok_E") {
					$row['ver'] = $btn_ver_listo;
					$row['accion'] = $admin || $perm ? "$btn_invalido" : "$btn_invalido";
				}
				array_push($contratos, $row);
			}
			$r = $contratos;
			echo json_encode($r);
		}
	}

	/* Aval contratos */
	public function aval_contratos()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$perm = false;
			$id_solicitud = $this->input->post('idc');
			$estado_actual = $this->input->post('ids');
			$msg = $this->input->post('msg');
			$accion = $this->input->post('accion');
			$id_solicitante = $this->input->post('soli');
			$nuevo_estado = "";			
			$result = $this->contrataciones_model->listar_contratos('', $id_solicitud);
			$actividad = $this->obtener_permisos_actividades($estado_actual, $result[0]['tipo_contrato'], '', $_SESSION['persona']);
			$perm = $perm = $actividad ?  true : false;	
			
			if ($accion == 1) {
				if ($estado_actual == "Cont_Soli_E") {
					$nuevo_estado = "Cont_En_Ela";
					$msg = "El contrato ha inciado la fase de elaboración.";
				} else if ($estado_actual == "Cont_En_Ela") {
					$nuevo_estado = "Cont_Secr_Avl";
					$msg = "Contrato elaborado satisfactoriamente. En espera del aval del Secretario General.";
				} else if ($estado_actual == "Cont_Secr_Avl") {
					$nuevo_estado = "Cont_En_Firm";
					$msg = "Aval del Secretario General. En espera de las firmas.";
				} else if ($estado_actual == "Cont_En_Firm") {					
					if (is_null($result[0]['firma_contratista']) || $result[0]['firma_contratista'] == "") {
						$r = ["mensaje" => "El contrato ha sido firmado con exito, en espera de la firma del contratista", "tipo" => "warning", "titulo" => "Muy bien!"];
						exit(json_encode($r));
					}
					$nuevo_estado = "Cont_En_Ver";
					$msg = "Contrato firmado por ambas partes. Pasa a verificacíon para adjuntar la garanita si se requiere.";
				} else if ($estado_actual == "Cont_En_Ver") {
					$nuevo_estado = "Cont_En_Comp";
					$msg = "Pasa a rectificacíon por parte del administrador para enlace con compras.";
				} else if ($estado_actual == "Cont_En_Comp") {	
					$r = $this->enviar_compras($id_solicitud);
					if ($r['tipo'] == "error") {
						die(json_encode($r));
					}				
					$nuevo_estado = "Cont_Ok_E";
					$msg = "El proceso de contratación ha pasado al departamento de Compras. Proceso finalizado correctamente.";
				}
			} elseif ($accion == 2) {
				$nuevo_estado = "Cont_Rec_E";
			} else {
				exit(json_encode(["mensaje" => "Opción invalida.", "tipo" => "error", "titulo" => "Error!"]));
			}

			$check = $this->verificar_estado($id_solicitud, $nuevo_estado);
			//die(json_encode(["mensaje" => "El contrato ha sido diligenciado correctamente!", "tipo" => "success", "titulo" => "Muy bien!"]));
			if ($check) {
				$r = ["mensaje" => "Los contratos no pueden pasar por un mismo estado dos veces, consulte con sistemas sobre este error.", "tipo" => "info", "titulo" => "Oops!"];
			} else if($perm || $this->admin) {										
				$arrayToUpd = [
					"id_solicitud" => $id_solicitud,
					"id_estado" => $nuevo_estado,
					"id_usuario_registra" => $_SESSION['persona'],
					"observacion" => $msg
				];
				$upd_status = $this->contrataciones_model->Guardar_Info("contrataciones_estados", $arrayToUpd);
				if ($upd_status) {					
					$r = ["mensaje" => "El contrato ha sido diligenciado correctamente!", "tipo" => "success", "titulo" => "Muy bien!"];
					$arrayToAval = ["contrato_estado" => $nuevo_estado];	
					$this->contrataciones_model->Actualizar_Info("contrataciones", $arrayToAval, $id_solicitud);											
				} else {
					$r = ["mensaje" => "Error al guardar el estado", "tipo" => "error", "titulo" => "Error!"];
				}
			} else {
				$r = ["mensaje" => "No eres usuario con privilegios suficientes.", "tipo" => "error", "titulo" => "Usuario no autorizado!"];
			}
		}
		echo json_encode($r);
	}

	/* Verificar estados de contratos por si ya esta diligenciado */
	public function verificar_estado($id_solicitud, $estado_nuevo)
	{
		if (!$this->Super_estado) {
			exit(json_encode(['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]));
		} else {
			$status_check = array();
			$check_cont = $this->contrataciones_model->check_maxId($id_solicitud);
			for ($x = 0; $x < count($check_cont); $x++) {
				$query = $this->contrataciones_model->Verificar_Contratos($id_solicitud, $check_cont[$x]['id']);
				foreach ($query as $row) {
					array_push($status_check, $row);
				}
			}

			for ($x = 0; $x < count($status_check); $x++) {
				if ($status_check[$x]['id_estado'] == $estado_nuevo) {
					return true;
				}
			}
		}
	}

	/* Listar estados de los contratos para el historial del mismo. */
	public function listar_estados()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$id = $this->input->post('id');
			$query = $this->contrataciones_model->listar_estados($id);
			if ($query) {
				$r = $query;
			}
			echo json_encode($r);
		}
	}

	/* Listar tipo de personas, naturales o juridicas */
	public function listar_tipo_personas()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$datosToShow = [];
			$query = $this->contrataciones_model->listar_tipo_personas($this->find_idParametro('Tipos Personas Contratos')->id);
			if ($query) {
				foreach ($query as $row) {
					if ($row['idaux'] != 'per_nj') {
						array_push($datosToShow, $row);
					}
				}
				$r = $datosToShow;
			}
			echo json_encode($r);
		}
	}

	/* Listar tipo de garantia, con garantia o sin garantia */
	public function listar_tipo_garantia()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$r = $this->contrataciones_model->listar_tipo_garantia($this->find_idParametro('Tipo garantia contrato')->id);
			echo json_encode($r);
		}
	}
	/* Obtener contratos pendientes - solicitados */
	public function obtener_contratos_pendientes()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$r = $this->contrataciones_model->obtener_contratos_pendientes();
		}
		echo json_encode($r);
	}

	/* Listar los tipos de contratos */
	public function listar_tipo_contratos()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$r = $this->contrataciones_model->listar_tipo_contratos($this->find_idParametro('Tipos de contratos')->id);
		}
		echo json_encode($r);
	}

	/* Listar adjuntos de contratos */
	public function listar_archivos_contratos()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$ids = $this->input->post("id_solicitud");
			$r = $this->contrataciones_model->listar_archivos_contratos($ids);
		}
		echo json_encode($r);
	}

	/* DROPZONE */
	public function dropzone_uploads()
	{
		$id_contrato = $this->last_contra()->{'id'};
		$nombre = $_FILES["file"]["name"];
		$cargo = $this->cargar_archivo("file", $this->ruta_adjuntos, "Cont");
		if ($cargo[0] == -1) {
			header("HTTP/1.0 400 Bad Request");
			echo ($nombre);
			return;
		}
		$res = $this->contrataciones_model->guardar_archivo_contra($id_contrato, $nombre, $cargo[1]);
		if ($res == "error") {
			header("HTTP/1.0 400 Bad Request");
			echo ($nombre);
			return;
		}
		echo json_encode($res);
		return;
	}

	/* Listar personas - para los permisos en administrar */
	public function listar_personas()
	{
		$texto = $this->input->post('texto');
		$data = $texto ? $this->contrataciones_model->listar_personas($texto) : [];
		echo json_encode($data);
	}

	public function listar_actividades($persona = "")
	{
		$return = false;
		if (isset($persona) && !empty($persona)) {
			$return = true;
		}else{
			$persona = $this->input->post('persona');
		}

		if (!$this->Super_estado) {
			$data = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$data = (isset($persona) && !empty($persona)) ? $this->contrataciones_model->listar_actividades($persona, $this->find_idParametro('tipos_contratos')->idpa) : [];
		}

		if ($return) {
			return $data;
		}else{
			echo json_encode($data);
		}
	}

	public function asignar_actividad()
	{
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else if ($this->Super_agrega && $this->admin) {
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			$ok = $this->contrataciones_model->validar_asignacion_actividad($actividad, $persona);
			if ($ok) {
				$data = ['actividad_id' => $actividad, 'persona_id' => $persona, 'usuario_registra' => $_SESSION['persona']];
				$resp = $this->contrataciones_model->Guardar_Info('actividad_persona_cont', $data);
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

	public function quitar_actividad()
	{
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else if ($this->Super_modifica) {
			$id = $this->input->post('asignado');
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			// Verifico si actividad ya está asignada o no. Esta función retorna 0 si no está asignada la actividad y 1 si lo está.
			$ok = $this->contrataciones_model->validar_asignacion_actividad($actividad, $persona);
			if (!$ok) {
				$resp = $this->contrataciones_model->quitar_actividad($id);
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

	/* Listar estados de permisos */
	public function listar_estados_permisos($actividad = "")
	{
		$return = false;
		if (isset($actividad) && !empty($actividad)) {
			$return = true;
		}else{
			$actividad = $this->input->post('actividad');
		}

		if (!$this->Super_estado) {
			$data = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$data = $this->contrataciones_model->listar_estados_permisos($actividad);
		}

		if ($return) {
			return $data;
		} else {
			echo json_encode($data);
		}
	}

	/* Asignar estados */
	public function asignar_estado()
	{
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega && $this->admin) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->contrataciones_model->validar_asignacion_actividad($estado, $actividad, $persona);
				if ($ok) {
					$data = [
						'estado_id' => $estado,
						'actividad_id' => $actividad,
						'usuario_registra' => $_SESSION['persona']
					];
					$resp = $this->contrataciones_model->Guardar_Info('estados_actividades_cont', $data);
					$res = $resp ? [
						'mensaje' => "Estado asignado exitosamente.",
						'tipo' => "success",
						'titulo' => "Proceso Exitoso!"
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
				'titulo' => 'Oops.!'
			];
		}
		echo json_encode($res);
	}

	/* Activar notificacion */
	public function activar_notificacion()
	{
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->admin) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->contrataciones_model->validar_asignacion_estado($estado, $actividad, $persona);
				if (!$ok) {
					$id = $this->contrataciones_model->get_where('estados_actividades_cont', ['actividad_id' => $actividad, 'estado_id' => $estado])->row()->id;
					$resp = $this->contrataciones_model->modificar_datos_permisos(['notificacion' => 1], 'estados_actividades_cont', $id);
					$res = !$resp ? [
						'mensaje' => "Estado Desasignada exitosamente.",
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
			} else $resp = [
				'mensaje' => 'No cuenta con permisos para realizar esta acción.',
				'tipo' => 'info',
				'titulo' => 'Ooops!'
			];
		}
		echo json_encode($res);
	}

	/* Desactivar notificacion */
	public function desactivar_notificacion()
	{
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->admin) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->contrataciones_model->validar_asignacion_estado($estado, $actividad, $persona);
				if (!$ok) {
					$id = $this->contrataciones_model->get_where('estados_actividades_cont', ['actividad_id' => $actividad, 'estado_id' => $estado])->row()->id;
					$resp = $this->contrataciones_model->modificar_datos_permisos(['notificacion' => 0], 'estados_actividades_cont', $id);
					$res = !$resp
						? ['mensaje' => "Estado Desasignada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"]
						: ['mensaje' => "Ha ocurrido un error al desasignar el estado.", 'tipo' => "info", 'titulo' => "Ooops!"];
				} else $res = ['mensaje' => "El usuario no tiene asignado este estado.", 'tipo' => "info", 'titulo' => "Ooops!"];
			} else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		}
		echo json_encode($res);
	}

	/* Quitar estado */
	public function quitar_estado()
	{
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega && $this->admin) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$id = $this->input->post('id');
				$ok = $this->contrataciones_model->validar_asignacion_estado($estado, $actividad, $persona);
				if (!$ok) {
					$resp = $this->contrataciones_model->quitar_estado($id);
					$res = $resp ? [
						'mensaje' => "Estado Desasignada exitosamente.",
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
			} else $resp = [
				'mensaje' => 'No cuenta con permisos para realizar esta acción.',
				'tipo' => 'info',
				'titulo' => 'Ooops!'
			];
		}
		echo json_encode($res);
	}

	/* Funcion cargar archivos */
	public function cargar_archivo($mi_archivo, $ruta, $nombre)
	{
		$nombre .= uniqid();
		$tipo_archivos = $this->genericas_model->obtener_valores_parametro_aux("For_Adm", 20);
		if (empty($tipo_archivos)) {
			$tipo_archivos = "*";
		} else {
			$tipo_archivos = $tipo_archivos[0]["valor"];
		}
		$real_path = realpath(APPPATH . '../' . $ruta);
		$config['upload_path'] = $real_path;
		$config['file_name'] = $nombre;
		$config['allowed_types'] = $tipo_archivos;
		$config['max_size'] = "0";
		$config['max_width'] = "0";
		$config['max_height'] = "0";

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload($mi_archivo)) {
			//*** ocurrio un error
			$data['uploadError'] = $this->upload->display_errors();

			return array(-1, $data['uploadError']);
		}

		$data['uploadSuccess'] = $this->upload->data();

		return array(1, $data['uploadSuccess']["file_name"]);
	}

	public function guardar_firma()
	{
		$id = $this->input->post('id');
		$tabla = 'contrataciones';
		$check_contra = $this->input->post('check_contra');
		if (isset($check_contra) && $check_contra == 1) {
			$datos = ['firma_contratante' => $check_contra];
		} else {
			$r = ['mensaje' => "Porfavor marque la casilla", 'tipo' => "error", 'titulo' => "Oops"];
			exit(json_encode($r));
		}

		$this->contrataciones_model->Actualizar_Info($tabla, $datos, $id);
		$r = ["mensaje" => "La firma se guardo correctamente!", "tipo" => "success", "titulo" => "Proceso  exitoso!"];
		exit(json_encode($r));
	}

	public function generar_token()
	{
		$date = date("Y-m-d");
		//Incrementando 5 dias
		$mod_date = strtotime($date . "+ 5 days");
		$exp_date = date("Y-m-d", $mod_date);
		$token = hash('crc32', bin2hex(openssl_random_pseudo_bytes(64)));
		$id = $this->input->post('id');
		$tabla = 'contrataciones';
		$datos = ['token' => $token, 'exp_token' => $exp_date];
		$this->contrataciones_model->Actualizar_Info($tabla, $datos, $id);

		/** se busca al contratista para verificar si tiene contraseña */
		$resul_contrato = $this->contrataciones_model->Buscar_Info($tabla, ['contratista'], "id = $id");
		$data = ['id', 'valor as nombre', 'valory as identity', 'valorz as correo', 'valora as password'];
		$where = "idparametro IN (37, ".$this->find_idParametro('Contratista')->id.") and id = " . $resul_contrato[0]['contratista'];
		$res = $this->contrataciones_model->Buscar_Info('valor_parametro', $data, $where);
		if (is_null($res[0]['password']) || $res[0]['password'] == '') {
			$this->contrataciones_model->Actualizar_Info('valor_parametro', ['valora' => md5($res[0]['identity'])], $res[0]['id']);
		}

		$data = ['mensaje' => 'Token creado correctamente', 'token' => $token];
		exit(json_encode($data));
	}

	public function guardar_prorroga()
	{
		$tipos_de_adjs = $this->input->post("tipos_adj");
		$adjs_names = $this->input->post("adjs_names");
		if (empty($_FILES)) {
			$r = ['mensaje' => "No se ha enviado ningún adjunto, verifique e intente nuevamente.", 'tipo' => "error", 'titulo' => "Oops"];
			exit(json_encode($r));
		} else {
			$cargar = $this->cargar_archivo($adjs_names, "archivos_adjuntos/contrataciones", "Cont");
			if ($cargar[0] == -1) {
				$r = ['mensaje' => "Error al cargar el adjunto: " . $tipos_de_adjs . ".", 'tipo' => "error", 'titulo' => "Oops"];
				exit(json_encode($r));
			}
			$datos = [
				"id_contrato" => $this->input->post('id'),
				"modelo_contrato" => 'tipo_prorroga',
				"fecha_inicio" => $this->input->post('fecha_inicio_prorroga'),
				"fecha_terminacion" => $this->input->post('fecha_termina_prorroga'),
				"prorroga_adj" => $cargar[1]
			];
			$this->contrataciones_model->guardar_prorroga($datos);
		}
		$r = ["mensaje" => "La operación se realizó correctamente!", "tipo" => "success", "titulo" => "Proceso  exitoso!"];
		exit(json_encode($r));
	}

	public function cargarContrato()
	{
		$tabla = 'contrataciones';
		$id = $this->input->post('id');
		$tipos_de_adjs = $this->input->post("tipos_adj");
		$adjs_names = $this->input->post("adjs_names");
		if (empty($_FILES)) {
			$r = ['mensaje' => "No se ha enviado ningún adjunto, verifique e intente nuevamente.", 'tipo' => "error", 'titulo' => "Oops"];
			exit(json_encode($r));
		} else {
			$cargar = $this->cargar_archivo($adjs_names, "archivos_adjuntos/contrataciones", "Cont");
			if ($cargar[0] == -1) {
				$r = ['mensaje' => "Error al cargar el adjunto: " . $tipos_de_adjs . ".", 'tipo' => "error", 'titulo' => "Oops"];
				exit(json_encode($r));
			}
			$datos = ['adj_contrato' => $cargar[1]];
			$adjunto = array();
			$adjunto = $this->contrataciones_model->Buscar_Info($tabla, ['id', 'adj_contrato'], ['id' => $id]);
			if (!empty($adjunto[0]['adj_contrato'])) {
				$archivo = realpath(APPPATH . '../archivos_adjuntos/contrataciones/' . $adjunto[0]['adj_contrato']);
				if (file_exists($archivo)) {
					unlink($archivo);
				}
			}
			$this->contrataciones_model->Actualizar_Info($tabla, $datos, $id);
		}
		$r = ["mensaje" => "La operación se realizó correctamente!", "tipo" => "success", "titulo" => "Proceso  exitoso!"];
		exit(json_encode($r));
	}

	public function cargarTareas()
	{
		$tabla = 'contrataciones_adjuntos';
		$id = $this->input->post('id');
		$adjs_names = json_decode($this->input->post("adj_names"));
		$tipos_de_adjs = json_decode($this->input->post("adj_tips"));
		$archivos = [];
		$cargar = ['names'];
		if (empty($_FILES)) {
			$r = ['mensaje' => "No se ha enviado ningún adjunto, verifique e intente nuevamente.", 'tipo' => "error", 'titulo' => "Oops"];
			exit(json_encode($r));
		} else {
			for ($i = 0; $i < count($_FILES); $i++) {
				$cargar[$i] = $this->cargar_archivo($adjs_names[$i], "archivos_adjuntos/contrataciones", "Cont");
				$cargar['names'][$i] = $adjs_names[$i];
				if ($cargar[$i][0] == -1) {
					$r = ['mensaje' => "Error al cargar el adjunto: " . $tipos_de_adjs[$i] . ".", 'tipo' => "error", 'titulo' => "Oops"];
					exit(json_encode($r));
				}
			}
			for ($i = 0; $i < count($_FILES); $i++) {
				if ($cargar['names'][$i] == 'adj_garantia') {
					$where = [
						'id_solicitud' => $id,
						'nombre_real' => 'Poliza de garantia'
					];
					$adjunto = array();
					$adjunto = $this->contrataciones_model->Buscar_Info($tabla, ['id', 'nombre_guardado'], $where);
					$sendToAdj = [
						"id_solicitud" => $id,
						"nombre_guardado" => $cargar[$i][1],
						"nombre_real" => 'Poliza de garantia',
						"id_usuario_registra" => $_SESSION['persona']
					];
					if (empty($adjunto)) {
						$save_in_contAdj = $this->contrataciones_model->Guardar_Info($tabla, $sendToAdj);
					} else {
						$save_in_contAdj = $this->contrataciones_model->Actualizar_Info($tabla, $sendToAdj, $adjunto[0]['id']);
					}

					if (!$save_in_contAdj) {
						$r = ['mensaje' => "Error al cargar la garantia", 'tipo' => "error", 'titulo' => "Oops"];
						exit(json_encode($r));
					}

				}

				if ($cargar['names'][$i] == 'adj_cont_firma') {
					$this->contrataciones_model->Actualizar_Info('contrataciones', ['adj_contrato' => $cargar[$i][1]], $id);
				}
			}
		}

		$r = ["mensaje" => "La operación se realizó correctamente!", "tipo" => "success", "titulo" => "Proceso  exitoso!"];
		exit(json_encode($r));
	}

	public function verificar_contrato_adj($tipo = '')
	{
		$id = $this->input->post('id');
		$tipo = empty($this->input->post('tipo')) ? $tipo : $this->input->post('tipo');
		$tabla = 'contrataciones';
		$datos = [$tipo];
		$where = ['id' => $id];
		$res = $this->contrataciones_model->Buscar_Info($tabla, $datos, $where);
		if (!is_null($res[0][$tipo])) {
			$r = ['status' => 1, 'data' => $res[0]];
		} else {
			$r = ['status' => 0, 'data' => $res[0]];
		}
		exit(json_encode($r));
	}

	/* Buscar Contrato */
	public function buscar_contrato()
	{
		if (!$this->Super_estado) {
			$resul_contrato = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$codContra = $this->input->post('dato_buscar');
			$resul_contrato = $this->contrataciones_model->buscar_contrato($codContra);
			echo json_encode($resul_contrato);
		}
	}

	/* Buscar Contratista */
	public function consultar_contratista()
	{
		if (!$this->Super_estado) {
			$resul_contrato = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$codContra = $this->input->post('id_solicitud');
			$data = ['id', 'num_contrato', 'contratista', 'objetivo', 'valor', 'fecha_inicio', 'fecha_terminacion', 'contrato_estado as estado_cont', 'firma_contratista'];
			$where = "id = $codContra";
			$resul_contrato = $this->contrataciones_model->Buscar_Info('contrataciones', $data, $where);
			$data = ['id', 'valor as nombre', 'valory as identity', 'valorz as correo'];
			$where = "idparametro IN (37, ".$this->find_idParametro('Contratista')->id.") and id = " . $resul_contrato[0]['contratista'];
			$res = $this->contrataciones_model->Buscar_Info('valor_parametro', $data, $where);
			$r = [$resul_contrato, $res];
			echo json_encode($r);
		}
	}

	/* enviar a compras*/
	public function enviar_compras($id)
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {			
			$ruta_compras = "archivos_adjuntos/compras/solicitudes/";
			$articulos_a_guardar = [];
			//se busca el contrato
			$soli_compra = $this->contrataciones_model->Buscar_Info('solicitud_compra', ['id_contrato'], ['id_contrato' => $id]);
			if (!empty($soli_compra) && $soli_compra[0]['id_contrato']) {
				$resp = ['mensaje' => "La información ya se encotraba almacenada", 'tipo' => "success", 'titulo' => "Proceso exitoso!"];
			} else {
				$cont = $this->contrataciones_model->Listar_Contratos($id);
				$cont = (object)$cont[0];
				$fecha_solicitud = date("Y-m-d H:i");
				$tipo_compra = "Soli_Sin";
				$observaciones = '';
				$usuario = $cont->id_usuario_registra;

				$add_solicitud = $this->compras_model->guardar_solicitud('Solicitud de contrato', $tipo_compra, $usuario, $observaciones, null, null, $fecha_solicitud, null);				
				if ($add_solicitud <= 0) {
					$resp = ['mensaje' => "Error al enviar la solicitud a compras contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
				} else {
					$this->contrataciones_model->Actualizar_Info("solicitud_compra", ['id_contrato' => $id], $add_solicitud);
					$resp = ['mensaje' => "Información almacenada con exito", 'tipo' => "success", 'titulo' => "Proceso exitoso!"];
					$articulos_a_guardar[] = array(
						"id_solicitud" => $add_solicitud,
						"id_almacen" => null,
						"cod_sap" => $cont->codSAP,
						"nombre_articulo" => 'Solicitud de contrato',
						"marca" => $cont->nombre_tista,
						"referencia" => $cont->num_con,
						"cantidad" => 1,
						"observaciones" => $cont->contrato,
						"usuario_crea" => $usuario,
					);
					$add_articulos = $this->compras_model->guardar_general($articulos_a_guardar, "articulos_solicitud");
					if ($add_articulos == "error") {
						$resp = ['mensaje' => "Error al guardar los articulos en la solicitud de compras, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
					}
					//se buscan y se copian los adjuntos a compras
					$adjs = $this->contrataciones_model->listar_archivos_contratos($id);
					$fileTmpPath = $this->ruta_adjuntos . '/' . $cont->adj_contrato;
					$fileName = $cont->adj_contrato;
					$fileNameCmps = explode(".", $fileName);
					$fileExtension = strtolower(end($fileNameCmps));
					$newFileName = 'compdw' . uniqid() . '.' . $fileExtension;
					$uploadFileDir = $ruta_compras;
					$dest_path = $uploadFileDir . $newFileName;
					copy($fileTmpPath, $dest_path);
					$this->compras_model->guardar_archivo_compra($add_solicitud, 'Contrato', $newFileName);
					foreach ($adjs as $FILES) {
						$fileTmpPath = $this->ruta_adjuntos . '/' . $FILES['nombre_guardado'];
						$fileName = $FILES['nombre_guardado'];
						$fileNameCmps = explode(".", $fileName);
						$fileExtension = strtolower(end($fileNameCmps));
						$newFileName = 'compdw' . uniqid() . '.' . $fileExtension;
						$uploadFileDir = $ruta_compras;
						$dest_path = $uploadFileDir . $newFileName;
						copy($fileTmpPath, $dest_path);
						$this->compras_model->guardar_archivo_compra($add_solicitud, $FILES['nombre_real'], $newFileName);
					}

				}				
			}
		}
		return $resp;
	}

	/* Guardar contratistas*/
	public function guardar_contratista()
	{
		$idpa = '';
		$valor_parametro = $this->find_idParametro('Contratista');
		if (!empty($valor_parametro)) {
			$idpa = $valor_parametro->id;
		}
		$identity = $this->input->post("identity");
		$email = $this->input->post("email");
		$name = $this->input->post("name");
		$dataSend = ['idparametro' => $idpa, 'valor' => $name, 'valory' => $identity, 'valorz' => $email, 'valorb' => 'contra_tistas'];
		$res_identity = $this->contrataciones_model->verificar_Contratista($identity, $idpa);
		$res_email = $this->contrataciones_model->verificar_Contratista($email, $idpa);
		if (!empty($res_identity) && !empty($res_email)) {
			if ($res_identity[0]['identy'] == $identity && $res_email[0]['correo'] == $email) {
				$tipo = "warning";
				$titulo = "Oops.!";
				$message = "El NIT o Cédula de Ciudadania y el correo ya se encuentran registrado";
			}
		} elseif (!empty($res_identity)) {
			if ($res_identity[0]['identy'] == $identity) {
				$tipo = "warning";
				$titulo = "Oops.!";
				$message = "El NIT o Cédula ya se encuentra registrado";
			}
		} elseif (!empty($res_email)) {
			if ($res_email[0]['correo'] == $email) {
				$tipo = "warning";
				$titulo = "Oops.!";
				$message = "El correo ya se encuentra registrado";
			}
		} else {
			if (strlen($identity) >= 10) {
				$save = $this->contrataciones_model->Guardar_Info("valor_parametro", $dataSend);
				if ($save) {
					$tipo = "success";
					$titulo = "Información almacenada con exito";
					$message = "El contratista se ha guardado correctamente";
				} else {
					$tipo = "error";
					$titulo = "Error.!";
					$message = "Ocurrio un error al guardar la información";
				}								
			} else {
				$tipo = "warning";
				$titulo = "Oops.!";
				$message = "El NIT o Cédula de Ciudadania debe ser de minimo 10 digitos";
			}
		}

		$resp = ['mensaje' => $message, 'tipo' => $tipo, 'titulo' => $titulo];
		echo json_encode($resp);
	}

	/* Guardar contratistas*/
	public function actualizar_contratista()
	{
		$admin = $_SESSION['perfil'] == "Per_Admin" || $_SESSION['perfil'] == "Admin_Cont" ? true : false;
		if ($admin) {
			$idpa = '';
			$valor_parametro = $this->find_idParametro('Contratista');
			if (!empty($valor_parametro)) {
				$idpa = $valor_parametro->id;
			}
			$id = $this->input->post("id");
			$identity = $this->input->post("identity");
			$email = $this->input->post("email");
			$name = $this->input->post("name");
			$dataSend = ['idparametro' => $idpa, 'valor' => $name, 'valory' => $identity, 'valorz' => $email, 'valorb' => 'contra_tistas'];
			$res_identity = $this->contrataciones_model->verificar_Contratista($identity, $idpa);
			$res_email = $this->contrataciones_model->verificar_Contratista($email, $idpa);
			//var_dump($res_identity);
			if ((!empty($res_identity) && $res_identity[0]['id'] != $id) || (!empty($res_email) && $res_email[0]['id'] != $id)) {
				if ($res_identity[0]['identy'] == $identity && $res_email[0]['correo'] == $email) {
					$tipo = "warning";
					$titulo = "Oops.!";
					$message = "El NIT o Cédula de Ciudadania y el correo ya se encuentran registrado";
				}
			
				if ($res_identity[0]['identy'] == $identity && $res_identity[0]['id'] != $id) {
					$tipo = "warning";
					$titulo = "Oops.!";
					$message = "El NIT o Cédula ya se encuentra registrado";
				}

				if ($res_email[0]['correo'] == $email && $res_email[0]['id'] != $id) {
					$tipo = "warning";
					$titulo = "Oops.!";
					$message = "El correo ya se encuentra registrado";
				}
			} else {
				if (strlen($identity) >= 10) {
					$save = $this->contrataciones_model->Actualizar_Info("valor_parametro", $dataSend, $id);
					if ($save) {
						$tipo = "success";
						$titulo = "Información almacenada con exito";
						$message = "El contratista se ha actualizado correctamente";
					} else {
						$tipo = "error";
						$titulo = "Error.!";
						$message = "Ocurrio un error al actualizado
						la información";
					}								
				} else {
					$tipo = "warning";
					$titulo = "Oops.!";
					$message = "El NIT o Cédula de Ciudadania debe ser de minimo 10 digitos";
				}
			}
		}else{
			$tipo = "warning";
			$titulo = "Oops.!";
			$message = "No posee los permisos suficientes para realizar esta accion";
		}
		$resp = ['mensaje' => $message, 'tipo' => $tipo, 'titulo' => $titulo];
		echo json_encode($resp);
	}

	/* Traer idparametro basados en su id_aux o codigo que identifique el conjunto de info deseado pero que tenga un mismo idparametro */
	public function find_idParametro($codigoo = '', $return = true)
	{
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			if (empty($codigoo)) {
				$codigoo = $this->input->post('codigo');
				$return =  false;
			}
			if (empty($codigoo)) {
				$r = [];
			} else {
				$query = $this->contrataciones_model->find_idParametro($codigoo);
				$r = $query;
			}
		}
		if ($return) {
			return $r;
		} else {
			exit(json_encode($r));
		}
	}

	/*buscar un dato en un array o matrix*/
	public function search_in_array($dato, $array){
		if (!$this->Super_estado) {
			$return = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$return = false;
			if (!empty($array) && is_array($array)) {
				foreach ($array as $val) {
					if (is_array($val)) {
						if (in_array($dato, $val)) {
							$return = $val;
						}
					} else {
						if (in_array($dato, $array)) {
							$return = $array;
						}
					}				
				}						
			}			
		}
		return $return;
	}

	/* obtener permisos actividades*/
	public function obtener_permisos_actividades($estado_contrato = "", $tipo_contrato = "", $notificacion = "", $persona = ""){
		if (!$this->Super_estado) {
			$resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$actividades = $this->contrataciones_model->obtener_permisos_actividades($estado_contrato, $tipo_contrato, $notificacion, $persona);
			$resp = isset($actividades) && !empty($actividades) ? $actividades: false;				
		}
		return $resp;
	}

	public function obtener_correos(){
		if (!$this->Super_estado) {
			$resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$id_solicitud = $this->input->post('id_solicitud');			
			$contrato = $this->contrataciones_model->listar_contratos('', $id_solicitud);
			$valor_parametro = $this->find_idParametro('contra_tistas');
			$estado_actual = $contrato[0]['estado_cont'];
			$correos = [];			
			$correos = $this->obtener_permisos_actividades($estado_actual, $contrato[0]['tipo_contrato'], 1);
			$usuario_registra = $this->contrataciones_model->Buscar_Info('personas', ["correo", "CONCAT(nombre, ' ', apellido, ' ', segundo_apellido) persona", false], 'id = '.$contrato[0]['id_usuario_registra']);
			if (!empty($valor_parametro) && ($estado_actual == "Cont_En_Firm" || $estado_actual == "Cont_Soli_E")) {
				$idpa = $valor_parametro->idpa;
				$resul_contratista = $this->contrataciones_model->Buscar_Contratista('', $idpa, $contrato[0]['contratista']);
				$correos['contratista'] = ['persona' => strtoupper($resul_contratista[0]['nombre']), 'correo' => $resul_contratista[0]['correo'], 'estado' => $estado_actual];
				/** se busca al contratista para verificar si tiene contraseña */
				$data = ['id', 'valor as nombre', 'valory as identity', 'valorz as correo', 'valora as password'];
				$where = "idparametro IN (37, ".$this->find_idParametro('Contratista')->id.") and id = " . $contrato[0]['contratista'];
				$res = $this->contrataciones_model->Buscar_Info('valor_parametro', $data, $where);
				if (is_null($res[0]['password']) || $res[0]['password'] == '') {
					$this->contrataciones_model->Actualizar_Info('valor_parametro', ['valora' => md5($res[0]['identity'])], $res[0]['id']);
				}
			}			
			$correos['usuario_registra'] = ['persona' => $usuario_registra[0]['persona'], 'correo' => $usuario_registra[0]['correo'], 'estado' => $estado_actual];
			$resp = $correos;
		}
		echo json_encode($resp);
	}
}
