<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use function PHPSTORM_META\type;

class compras_control extends CI_Controller
{

	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
	var $ruta_archivos_solicitudes = "archivos_adjuntos/compras/solicitudes";
	var $ruta_archivos_proveedores = "archivos_adjuntos/compras/proveedores";
	public function __construct()
	{
		parent::__construct();
		include('application/libraries/festivos_colombia.php');
		$this->load->model('compras_model');
		$this->load->model('genericas_model');
		$this->load->model('pages_model');
		$this->load->model('almacen_model');
		session_start();
		if (isset($_SESSION["usuario"])) {
			$this->Super_estado = true;
			$this->Super_elimina = 1;
			$this->Super_modifica = 1;
			$this->Super_agrega = 1;
		}
	}

	public function index($pages = "compras", $comit = -1)
	{

		if ($this->Super_estado) {
			$datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $pages);
			if (!empty($datos_actividad)) {
				$data['js'] = "Compras";
				$data['comite'] = $comit;
				$data['permiso'] = $this->compras_model->listar_tipos_permisos($_SESSION["persona"]);
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

	public function guardar_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode(array("sin_session"));
			return;
		} else {
			if ($this->Super_agrega == 0) {
				echo json_encode(array(-1302));
			} else {

				//$fecha_solicitud = $this->validar_fecha();
				//$depar = $this->input->post("departamento");
				//$jefe = $this->input->post("idjefe");
				// $nombre_solicitud = $this->input->post("nombre_solicitud");
				// $tipo_compra = $this->input->post("tipo_compra");
				$fecha_solicitud = date("Y-m-d H:i");
				$depar = null;
				$jefe = $this->input->post("jefe") != '0' ? $this->input->post("jefe") : null;
				$adjunto = null;
				$nombre_solicitud = null;
				$tipo_compra = "Soli_Sin";
				$observaciones = $this->input->post("observaciones");
				$conadjuntos = $this->input->post("conadjuntos");
				$usuario = $_SESSION['persona'];

				/* if (ctype_space($nombre_solicitud) || empty($nombre_solicitud)) {
					echo json_encode(array(-1));
					return;
				}
				if (ctype_space($tipo_compra) || empty($tipo_compra)) {
					echo json_encode(array(-2));
					return;
				}
				if (ctype_space($jefe) || empty($jefe)) {
					echo json_encode(array(-3));
					return;
				}
				if (ctype_space($depar) || empty($depar)) {
					echo json_encode(array(-4));
					return;
				} */

				$resp = $this->compras_model->guardar_solicitud($nombre_solicitud, $tipo_compra, $usuario, $observaciones, $jefe, $depar, $fecha_solicitud, $adjunto);
				if ($resp > 0) {
					$arts = array();
					$data = json_decode(stripslashes($this->input->post("data")));
					foreach ($data as $d) {
						array_push($arts, array(
							"id_solicitud" => $resp,
							"cod_sap" => $d->{'codigo_orden'},
							"nombre_articulo" => $d->{'nombre_art'},
							"marca" => $d->{'marca_art'},
							"referencia" => $d->{'referencia_art'},
							"cantidad" => $d->{'cantidad_art'},
							"observaciones" => $d->{'observaciones'},
							"fecha_compra_tarjeta" => !empty($d->{'fecha_compra_tarjeta'}) ? $d->{'fecha_compra_tarjeta'} : null,
							"usuario_crea" => $usuario,
						));
					}
					$resp1 = $this->compras_model->guardar_general($arts, "articulos_solicitud");
					if ($resp1 == "error") {
						echo json_encode(array('error'));
						return;
					}
					$info = $this->compras_model->obtener_correo_solicitante($resp);
					echo json_encode(array(0, $resp, $info));
					return;
				}
				echo json_encode(array('error'));
				return;
			}
		}
	}
	public function asignar_solicitud_usuario()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_agrega == 0) {
				echo json_encode(-1302);
			} else {

				$data = array();
				$id_usuario_soli = $this->input->post("id_usuario");
				$id_tipo_solicitud = $this->input->post("id_tipo_solicitud");
				$solicitudes_exceptuadas = $this->input->post("solicitudes_exceptuadas");
				$persona = $_SESSION['persona'];

				if (ctype_space($id_usuario_soli) || empty($id_usuario_soli)) {
					echo json_encode(-1);
					return;
				}
				if (ctype_space($id_tipo_solicitud) || empty($id_tipo_solicitud)) {
					echo json_encode(-2);
					return;
				}
				if ($id_tipo_solicitud == "excepto") {
					if (ctype_space($solicitudes_exceptuadas) || empty($solicitudes_exceptuadas)) {
						echo json_encode(-2);
						return;
					}
					for ($i = 0; $i < count($solicitudes_exceptuadas); $i++) {
						array_push($data, array(
							'id_tipo_solicitud' => $solicitudes_exceptuadas[$i],
							'id_usuario' => $id_usuario_soli,
							'usuario_registra' => $persona,
						));
					}
				} else {
					array_push($data, array(
						'id_tipo_solicitud' => $id_tipo_solicitud,
						'id_usuario' => $id_usuario_soli,
						'usuario_registra' => $persona,
					));
				}

				$resp = $this->compras_model->guardar_general($data, "solicitudes_usuarios_com");
				echo json_encode($resp);
				return;
			}
		}
	}


	public function asignar_estado_usuario()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_agrega == 0) {
				echo json_encode(-1302);
			} else {

				$data = array();
				$id = $this->input->post("id");
				$estado = $this->input->post("estado");
				$estados_exceptuados = $this->input->post("estados_exceptuadas");
				$persona = $_SESSION['persona'];

				if (ctype_space($id) || empty($id)) {
					echo json_encode(-1);
					return;
				}
				if (ctype_space($estado) || empty($estado)) {
					echo json_encode(-2);
					return;
				}
				if ($estado == "excepto") {
					if (ctype_space($estados_exceptuados) || empty($estados_exceptuados)) {
						echo json_encode(-2);
						return;
					}
					for ($i = 0; $i < count($estados_exceptuados); $i++) {
						array_push($data, array(
							'id_estado' => $estados_exceptuados[$i],
							'id_solicitud_usuario' => $id,
							'usuario_registra' => $persona,
						));
					}
				} else {
					array_push($data, array(
						'id_estado' => $estado,
						'id_solicitud_usuario' => $id,
						'usuario_registra' => $persona,
					));
				}

				$resp = $this->compras_model->guardar_general($data, "estados_sol_usuarios");
				echo json_encode($resp);
				return;
			}
		}
	}


	public function guardar_articulo()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_agrega == 0) {
				echo json_encode(-1302);
			} else {
				$id_solicitud = $this->input->post("id_solicitud");
				$codigo_orden = $this->input->post("codigo_orden");
				$tipo = $this->input->post("tipo");
				$nombre_art = $this->input->post("nombre_art");
				$marca_art = $this->input->post("marca_art");
				$referencia_art = $this->input->post("referencia_art");
				$cantidad_art = $this->input->post("cantidad_art");
				$observaciones = $this->input->post("observaciones");
				$fecha_compra_tarjeta = $this->input->post("fecha_compra_tarjeta");


				if ((ctype_space($id_solicitud) || empty($id_solicitud)) && $tipo == 2) {
					echo json_encode(-1);
					return;
				}
				if (ctype_space($codigo_orden) || empty($codigo_orden)) {
					echo json_encode(-2);
					return;
				}
				if (ctype_space($nombre_art) || empty($nombre_art)) {
					echo json_encode(-8);
					return;
				}

				if (ctype_space($cantidad_art) || empty($cantidad_art)) {
					echo json_encode(-3);
					return;
				}
				if (ctype_space($observaciones) || empty($observaciones)) {
					echo json_encode(-10);
					return;
				}
				$fecha_valida = $this->validateDate($fecha_compra_tarjeta, 'Y-m-d');
				if (!empty($fecha_compra_tarjeta) && !$fecha_valida) {
					echo json_encode(-11);
					return;
				}
				/*if (ctype_space($marca_art) || empty($marca_art)) {
                    echo json_encode(-9);
                    return;
                }
                if (ctype_space($referencia_art) || empty($referencia_art)) {
                    echo json_encode(-4);
                    return;
                }*/
				if (!is_numeric($cantidad_art)) {
					echo json_encode(-5);
					return;
				}
				if ($cantidad_art < 1) {
					echo json_encode(-6);
					return;
				}
				if ($tipo == 2) {
					$solicitud = $this->compras_model->traer_solicitud($id_solicitud);
					if (empty($solicitud)) {
						echo json_encode(-1);
						return;
					}
					if ($solicitud[0]["id_estado_solicitud"] != "Soli_Rev" || ($solicitud[0]["id_solicitante"] != $_SESSION['persona'] && $_SESSION['perfil'] != 'Per_Admin')) {
						echo json_encode(-7);
						return;
					}
				} else {
					echo json_encode(2);
					return;
				}


				$resp = $this->compras_model->guardar_articulo($id_solicitud, $codigo_orden, $nombre_art, $marca_art, $referencia_art, $cantidad_art, $observaciones);
				echo json_encode($resp);
			}
		}
	}

	public function Listar_solicitudes()
	{ //Listar solicitudes
		$solicitudes_compra = array();
		if ($this->Super_estado == false) {
			echo json_encode($solicitudes_compra);
			return;
		}

		$tipo = $this->input->post("tipo");
		$estado = $this->input->post("estado");
		$departamento = $this->input->post("departamento");
		$fecha = $this->input->post("fecha");
		$fecha2 = $this->input->post("fecha2");
		$proveedor = $this->input->post("proveedor");
		$per_en_session = $_SESSION["persona"];

		$consulta = $this->input->post("consulta");
		$sinencusta = $this->input->post("sinencusta");
		$datos = $this->compras_model->Listar_solicitudes($tipo, $estado, $departamento, $fecha, $consulta, $sinencusta, $fecha2, $proveedor);
		$dato = $this->compras_model->find_idParametro('tipos_pregRP'); //Obtiene el id de critico alto para llevar a cabo la RP
		$critico = $this->compras_model->find_idParametro('critico_alto'); //Obtiene el id de critico alto para llevar a cabo la RP

		/* btns para la gestion */
		$btn_ver = '<span style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>';
		$btn_off = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
		$check = $this->compras_model->traer_permisos_encuestas($_SESSION['persona'], '', $dato->idpa);
		/* Fin de pintar btns */

		$i = 1;

		$sw = false;
		if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Com" || $_SESSION["perfil"] == "Per_Com" || $_SESSION["perfil"] == "Per_Alm") {
			$sw = true;
		}
		$value_ges = -1;
		$per_compras =  $_SESSION["perfil"] == "Per_Adm_Com" || $_SESSION["perfil"] == "Per_Com" || $_SESSION["perfil"] == "Per_Alm" ? true : false;

		$perRp = '';
		$rpPermiso = $this->compras_model->permisos_compra_info($per_en_session);
		if ($rpPermiso) {
			foreach ($rpPermiso as $permi) {
				if ($permi['id_tipo_encuesta'] != 'Tip_Ser') {
					$perRp = $permi['id_persona'];
					break;
				}
			}
		}
		$perm = false;
		$perm_crono = $this->obtener_permisos_cronogramas('', $_SESSION['persona']);
		$perm = !empty($perm_crono) ?  true : false;
		$solicitudes_crono = $this->obtener_solicitudes_cronogramas_gestionar($_SESSION['persona']);
		foreach ($datos as $row) {
			//$this->ejecutar($row["id"]);
			$compra_inf = $this->compras_model->solicitud_compras_inf($row['id'], "row");
			$encs_pendientes = $this->check_encuestas_finalizadas($row["id"]);
			if ($compra_inf) {
				$btn_render = $this->pintar_btns($encs_pendientes, $row['id_solicitante'], $check, $row['id']);
			}

			$row["sw_add"] = $row['id_solicitante'] == $_SESSION['persona'] || $_SESSION['perfil'] == 'Per_Admin' ? 1 : 0;
			if (!is_null($row["tiempo_gestion"])) {
				if ($row["tiempo_gestion"] > $row["tiempo_habil"]) {
					$value_ges = 'Fuera Tiempo';
				} else {
					$value_ges = 'OK';
				}
			} else {
				$value_ges = 'Pendiente';
			}
			$sw_alt = true;
			$row["codigo"] = '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>';
			$row["gestion"] = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
			if ($row["estado_general"] == "Soli_Rev") {
				$row["codigo"] = '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(0)" ></span>';
			} else if ($row["estado_general"] == "Soli_Rec") {
				$row["codigo"] = '<span  style="background-color: #004078;color: white; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(1)" ></span>';
			} else if ($row["estado_general"] == "Soli_Dev") {
				$sw_alt = false;
				$row["codigo"] = '<span  style="background-color: #d9534f;color: white; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				$row["gestion"] = '<span title="Crear Copiar" style="color: #d9534f" data-toggle="popover" data-trigger="hover" class="fa fa-copy  btn btn-default pointer" onclick="crear_copia(' . $row['id'] . ')" ></span>';
			} else if ($row["estado_general"] == "Soli_Cot") {
				$row["codigo"] = '<span style="background-color: white;color: black; width: 100%;" class="pointer form-control" ><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(2)" ></span>';
			} else if ($row["estado_general"] == "Soli_Cac") {
				$row["codigo"] = '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(3)" ></span>';
			} else if ($row["estado_general"] == "Soli_Mon") {
				$row["codigo"] = '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				// $row["codigo"] = '<span  style="background-color: #f0ad4e;color: white; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(15)" ></span>';
			} else if ($row["estado_general"] == "Soli_Pre") {
				$row["codigo"] = '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(4)" ></span>';
			} else if ($row["estado_general"] == "Soli_Pro") {
				$row["codigo"] = '<span style="background-color: white;color: black; width: 100%;" class="pointer form-control" ><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(5)" ></span>';
			} else if ($row["estado_general"] == "Soli_Com") {
				$row["codigo"] = '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				$row["gestion"] = '<span title="Abrir solicitud en comité" style="color:#428bca;" data-toggle="popover" data-trigger="hover" class="fa fa-folder-open  btn btn-default pointer" onclick="listar_proveedores_solicitud(' . $row["id"] . ')" ></span>';
			} else if ($row["estado_general"] == "Soli_Oco") {
				$row["codigo"] = '<span style="background-color: white;color: black; width: 100%;" class="pointer form-control" ><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(7)" ></span>';
			} else if ($row["estado_general"] == "Soli_Lib") {
				$row["codigo"] = '<span  style="background-color: #0FA2AB;color: white; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer pasar_pendiente"></span>';
			} else if ($row["estado_general"] == "Soli_Ord") {
				$row["codigo"] = '<span  style="background-color: #EA7200;color: white; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(9)" ></span>';
			}
			if ($row["estado_general"] == "Soli_Pdoc") {
				$row["codigo"] = '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(12)" ></span>';
			} else if ($row["estado_general"] == "Soli_Pen") {
				if ($row["id_tipo_orden"] == "Tip_Ser") {						
					if ($perm || $_SESSION['persona'] == $row["id_solicitante"] or ($_SESSION["perfil"] == "Per_Admin" or $_SESSION["perfil"] == "Per_Adm_Com")) {
						#Para activar los cronogramas, descomentar la linea a continuacion y comentar la que tiene el onlick()
						//$row["gestion"] = '<span title="Marcar Recibido" style="color: #39B23B" data-toggle="popover" data-trigger="hover" class="fa fa-check-square-o btn btn-default pointer admin_crono_check"></span>';
						$row["gestion"] = '<span title="Marcar Recibido" style="color: #39B23B" data-toggle="popover" data-trigger="hover" class="fa fa-check-square-o btn btn-default pointer" onclick="Mostrar_estados_siguientes(14)" ></span>';
					} else {
						$row["gestion"] = '<span title="Esperando Recibido" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn" style="color:#428bca"></span>';
					}

					if ($_SESSION["perfil"] == "Per_Admin" or $_SESSION["perfil"] == "Per_Adm_Com") {
						$entregables_status = $this->check_estado_entregables($row["id"]);
						$validar_estado_soli = $this->validar_estado_siguiente($row["id"], 'Ser_Rec');
						if ($entregables_status == 1 && $validar_estado_soli == 1) {
							$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(14)"></span>';
						}
					}					
				} else {
					$row["codigo"] = '<span  style="background-color: #8000FF;color: white; width: 100%;" class="pointer form-control"><span >ver</span></span>';
					$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(10)"></span>';
				}
			} else if ($row["estado_general"] == "Ser_Rec") {
				$row["codigo"] = '<span  style="background-color: #8000FF;color: white; width: 100%;" class="pointer form-control"><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(10)"></span>';
			} else if ($row["estado_general"] == "Soli_Par") {
				$row["codigo"] = '<span style="background-color: white;color: black; width: 100%;" class="pointer form-control" ><span >ver</span></span>';
				$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Listar_articulos_parciales(' . $row["id"] . ')" ></span>';
			} else if ($row["estado_general"] == "Soli_Fin") {

				//fin1 Comienzo de soli_fin - Este es el primer finalizado sin la encuesta hecha, encuesta por default
				$row["codigo"] = '<span style="background-color: #39B23B;color: white; width: 100%;" class="pointer form-control" ><span >ver</span></span>';
				$sw_alt = false;

				if ($row["fecha_fin_encuesta"] == null && $row["id_solicitante"] == $_SESSION["persona"]) {
					empty($btn_render) ? $btn_render = $btn_off : false;
					$row["gestion"] = $btn_render;
					/* Traemos las encuestas que esten pendientes para saber que btns renderizar */
					if ($compra_inf) {
						if ($compra_inf->id_clasificacion == $critico->id) {
							if ($check) {
								if ($encs_pendientes) {
									foreach ($encs_pendientes as $key => $encs) {
										if ($encs['tipo_encuesta'] != '') {
											empty($btn_render) ? $btn_render = $btn_off : false;
											$row["gestion"] = $btn_render;
										} else {
											empty($btn_render) ? $btn_render = $btn_off : false;
											$row["gestion"] = $btn_render;
											break;
										}
									}
								}
							}
						} else {
							empty($btn_render) ? $btn_render = $btn_off : false;
							$row["gestion"] = $btn_render;
						}
					}
				} else {
					if ($row["tiempo_gestion"] > $row["tiempo_habil"]) {
						$fueraTiempo = '<span title="Gestion Fuera de tiempo" style="color: #d9534f;" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down  btn btn-default"  onclick="Listar_historial_estado(' . $row["id"] . ',' . $row["tiempo_habil"] . ',' . $row["tiempo_gestion"] . ')"></span>';
						$row["gestion"] = $fueraTiempo;
						if ($compra_inf) {
							if ($compra_inf->id_clasificacion == $critico->id) {
								if ($check) {
									if ($encs_pendientes) {
										foreach ($encs_pendientes as $key => $encs) {
											if ($encs['tipo_encuesta'] != '') {
												empty($btn_render) ? $btn_render = "" : false;
												$row["gestion"] = "$btn_render $fueraTiempo";
											} else {
												empty($btn_render) ? $btn_render = "" : false;
												$row["gestion"] = "$btn_render $fueraTiempo";
												break;
											}
										}
									}
								}
							} else {
								empty($btn_render) ? $btn_render = "" : false;
								$row["gestion"] = "$btn_render $fueraTiempo";
							}
						}
					} else {

						/* Traemos las encuestas que esten pendientes para saber que btns renderizar */
						if ($compra_inf) {
							if ($compra_inf->id_clasificacion == $critico->id) {
								if ($check) {
									if ($encs_pendientes) {
										foreach ($encs_pendientes as $key => $encs) {
											if ($encs['tipo_encuesta'] != '') {
												empty($btn_render) ? $btn_render = $btn_off : false;
												$row["gestion"] = $btn_render;
											} else {
												empty($btn_render) ? $btn_render = $btn_off : false;
												$row["gestion"] = $btn_render;
												break;
											}
										}
									}
								}
							} else {
								empty($btn_render) ? $btn_render = $btn_off : false;
								$row["gestion"] = $btn_render;
							}
						}
					}
				}
				//fin1 Fin del bloque 1 de soli fin
			}
			$row['gestion'] .= '<span style="color: white;display:none">' . $value_ges . '</span>';

			if (!$sw) {
				if ($sw_alt) {
					if ($row["estado_general"] == "Soli_Pen" && $row["id_tipo_orden"] == "Tip_Ser" && ($row["id_solicitante"] == $_SESSION["persona"] or $_SESSION['perfil'] == $per_compras || $perm)) {
						#Para reacivar el cronograma de compras, solo es descomentar la linea a continuacion y comenntar la de arriba.
						$row["gestion"] = '<span title="Marcar Recibido" style="color: #39B23B" data-toggle="popover" data-trigger="hover" class="fa fa-check-square-o btn btn-default pointer" onclick="Mostrar_estados_siguientes(14)" ></span>';
						//$row["gestion"] = '<span title="Marcar Recibido" style="color: #39B23B" data-toggle="popover" data-trigger="hover" class="fa fa-check-square-o btn btn-default pointer fechas_check"></span>';
						if ($_SESSION['perfil'] == $per_compras) {
							$entregables_status = $this->check_estado_entregables($row["id"]);
							$validar_estado_soli = $this->validar_estado_siguiente($row["id"], 'Ser_Rec');
							if ($entregables_status == 1 && $validar_estado_soli == 1) {
								$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-history  btn btn-default pointer" onclick="Mostrar_estados_siguientes(14)"></span>';
							}
						}
					} else {
						$row["gestion"] = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn" style="color:#428bca"></span>';
					}
				} else {
					//fin2 Bloque donde la solicitud esta en estado finalizado
					if ($row["estado_general"] == "Soli_Fin" && $row["fecha_fin_encuesta"] == null && $row["id_solicitante"] == $_SESSION["persona"]) {

						/* Traemos las encuestas que esten pendientes para saber que btns renderizar */
						if ($compra_inf) {
							if ($compra_inf->id_clasificacion == $critico->id) {
								$encs_pendientes = $this->check_encuestas_finalizadas($row["id"]);
								if ($check) {
									if ($encs_pendientes) {
										foreach ($encs_pendientes as $key => $encs) {
											if ($encs['tipo_encuesta'] != '') {
												empty($btn_render) ? $btn_render = $btn_off : false;
												$row["gestion"] = $btn_render;
											} else {
												empty($btn_render) ? $btn_render = $btn_off : false;
												$row["gestion"] = $btn_render;
												break;
											}
										}
									}
								}
							} else {
								empty($btn_render) ? $btn_render = $btn_off : false;
								$row["gestion"] = $btn_render;
							}
						}
					} else if ($row["estado_general"] == "Soli_Dev") {
						$row["gestion"] = '<span title="Crear Copiar" style="color: #d9534f" data-toggle="popover" data-trigger="hover" class="fa fa-copy  btn btn-default pointer" onclick="crear_copia(' . $row['id'] . ')" ></span>';
					} else {

						/* Traemos las encuestas que esten pendientes para saber que btns renderizar aquiii */
						if ($compra_inf) {
							if ($compra_inf->id_clasificacion == $critico->id) {
								$encs_pendientes = $this->check_encuestas_finalizadas($row["id"]);
								if ($check) {
									if ($encs_pendientes) {
										foreach ($encs_pendientes as $key => $encs) {
											if ($encs['tipo_encuesta'] != '') {
												empty($btn_render) ? $btn_render = $btn_off : false;
												$row["gestion"] = $btn_render;
											} else {
												empty($btn_render) ? $btn_render = $btn_off : false;
												$row["gestion"] = $btn_render;
												break;
											}
										}
									}
								}
							} else {
								empty($btn_render) ? $btn_render = $btn_off : false;
								$row["gestion"] = $btn_render;
							}
						}
					}
				}
				//fin2 Fin del Bloque donde la solicitud esta en estado finalizado
			}
			$add = true;
			if ($per_compras && ($row["permiso"] == null && $row["id_solicitante"] != $_SESSION["persona"])) $add = false;
			if ($rpPermiso) {
				if($_SESSION['persona'] == $perRp){
					foreach ($rpPermiso as $permi) {
						if ($permi['id_tipo_encuesta'] == 'sst_enc') {
							if ($per_compras && $row["estado_general"] == "Soli_Fin" && $row["estado_encuesta_sst"] >= 0) $add = true;
						}
		
						if ($permi['id_tipo_encuesta'] == 'sga_enc') {
							if ($per_compras && $row["estado_general"] == "Soli_Fin" && $row["estado_encuesta_sga"] >= 0) $add = true;
						}
		
						if ($permi['id_tipo_encuesta'] == 'Tip_Mat') {
							if ($per_compras && $row["estado_general"] == "Soli_Fin" && $row["estado_encuesta_tipmat"] >= 0) $add = true;
						}
					}			
				}
			}	
			if ($row["estado_general"] != "Soli_Pen") {
				if ($row["id_tipo_orden"] == "Tip_Ser") {						
					if ($perm && $this->search_in_array($row["id"], $solicitudes_crono)) {
						#Para activar los cronogramas, descomentar la linea a continuacion y comentar la que tiene el onlick()
						//$row["gestion"] = '<span title="Marcar Recibido" style="color: #39B23B" data-toggle="popover" data-trigger="hover" class="fa fa-check-square-o btn btn-default pointer admin_crono_check"></span>';
						$row["gestion"] = '<span title="Marcar Recibido" style="color: #39B23B" data-toggle="popover" data-trigger="hover" class="fa fa-check-square-o btn btn-default pointer" onclick="Mostrar_estados_siguientes(14)" ></span>';
					}
				}
			}	
			if ($add) $solicitudes_compra["data"][] = $row;
			$i++;
		}
		echo json_encode($solicitudes_compra);
	}

	/* Funcion para pintar btns de RP cuando sea necesario */
	public function pintar_btns($encs_pendientes, $id_solicitante, $permisos, $idsol)
	{
		$btns_return = [];
		$enc_to_do = "";
		$compra_inf = $this->compras_model->solicitud_compras_inf($idsol, 'row');

		foreach ($encs_pendientes as $encs) {
			if (!empty($encs['tipo_encuesta'])) {
				array_push($btns_return, $encs);
			}
		}
		
		if ($btns_return) {
			foreach ($btns_return as $btn) {
				if ($permisos) {
					foreach ($permisos as $per) {
						if ($btn['tipo_encuesta'] == 'tipmat_enc') {
							$btn['tipo_encuesta'] = 'Tip_Mat';
						} else if ($btn['tipo_encuesta'] == 'tipser_enc') {
							$btn['tipo_encuesta'] = 'Tip_Ser';
						}

						//Render de btns para calificar el servicio recibido si es una solicitud de tipo servicio
						if ($compra_inf->order_type == 'Tip_Ser' and $id_solicitante == $_SESSION['persona'] and $per['tipo_encuesta'] == $compra_inf->order_type and $btn['tipo_encuesta'] == $compra_inf->order_type) {
							if ($per['id_persona'] == $_SESSION['persona']) {
								$enc_to_do = '<span class="fa fa-clipboard btn btn-default do_' . strtolower($per['tipo_encuesta']) . '_enc' . ' red" style="margin: 3px;" data-enctype="' . $per['tipo_encuesta'] . '" title="' . $per['nombre_encuesta'] . '" data-toggle="popover" data-trigger="hover" style="color:#6E1F7C;"></span>';
								break;
							}
						}

						//Render de btns para realizar la calificacion de las demas encuestas dependiendo de las encuestas asignadas
						if (empty($enc_to_do)) {
							if ($btn['tipo_encuesta'] == $per['tipo_encuesta']) {
								if ($btn['tipo_encuesta'] != 'Tip_Ser' and $per['id_persona'] == $_SESSION['persona']) {
									if ($btn['tipo_encuesta'] == 'Tip_Mat') {
										$enc_to_do = '<span class="fa fa-clipboard btn btn-default do_' . strtolower($per['tipo_encuesta']) . '_enc' . ' red" style="margin: 3px;" data-enctype="' . $per['tipo_encuesta'] . '" title="' . $per['nombre_encuesta'] . '" data-toggle="popover" data-trigger="hover" style="color:#6E1F7C;"></span>';
									} else {
										$enc_to_do = '<span class="fa fa-clipboard btn btn-default do_' . strtolower($per['tipo_encuesta']) . ' red" style="margin: 3px;" data-enctype="' . $per['tipo_encuesta'] . '" title="' . $per['nombre_encuesta'] . '" data-toggle="popover" data-trigger="hover" style="color:#6E1F7C;"></span>';
									}
								}
							}
						}
					}
				}
			}
		}

		$btns_return = $enc_to_do;

		if (empty($btns_return)) {
			$enc_to_do = $this->pintar($idsol);
			$btns_return = $enc_to_do;
		}

		return $btns_return;
	}

	/* Retornar btns segun */
	public function pintar($idsol)
	{
		if (!$this->Super_estado) {
			return false;
		} else {
			$compra_inf = $this->compras_model->solicitud_compras_inf($idsol, 'row');
			if ($compra_inf) {
				$btns_return = "";
				$btn_off = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn" style="margin: 3px;"></span>';
				$btn_star_quiz = '<span title="Realizar encuesta de satisfacción" data-enctype="satis_enc" data-toggle="popover" data-trigger="hover" class="fa fa-star btn btn-default pointer do_satis_enc red" style="margin: 3px;"></span>';

				if ($compra_inf->res1_encuesta == null or $compra_inf->res2_encuesta == null or $compra_inf->res3_encuesta == null) {
					if ($compra_inf->id_solicitante == $_SESSION['persona']) {
						$btns_return = $btn_star_quiz;
					} else {
						$btns_return = $btn_off;
					}
				} else {
					$btns_return = $btn_off;
				}
			}
		}
		return $btns_return;
	}

	public function calificacionTiempoEntrega($compra_info){
		$dias_no_habiles = $compra_info->dias_no_habiles;
		$fecha_entrega_real = $compra_info->fecha_entrega_real;

		/* Se realiza la resta entre fechas para obtener, según la tabla de ponderados, el porcentaje que debe asignarse según
		los días en los que se exceda el cumplimiento o si de plano, no se excede ningún día */
		$dnh = date_create($dias_no_habiles);
		$fer = date_create($fecha_entrega_real);
		$resul = date_diff($dnh, $fer);
		$resta_tiempo = $resul->format("%R%a");

		$ponderados = $this->compras_model->listar_ponderados_rp();
		
		foreach ($ponderados as $val) {
			if ($resta_tiempo < 0) {
				$porcentaje_obtenido = $val["porcentaje"];
				break;
			}
			if ($resta_tiempo >= $val["valor_inicial"] && $resta_tiempo <= $val["valor_final"]) {
				$porcentaje_obtenido = $val["porcentaje"];
				break;
			}
		}
		/* Fin de la resta y sus condiciones. */
		return $porcentaje_obtenido;
	}

	/* Funcion para traer todas las solicitudes y promediar */
	public function promediar_proveedores()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$admin = false;
			$compra_user = false;

			if ($_SESSION["perfil"] == "Per_Admin" or $_SESSION["perfil"] == "Per_Adm_Com" or $_SESSION['perfil'] == "Per_Com") {
				$admin = true;
			} else {
				$admin = false;
			}

			if ($admin) {
				$fecha_ini = $this->input->post("fecha_ini");
				$fecha_fin = $this->input->post("fecha_fin");
				$imprevistos = [];
				$organizar_ids = "";
				$idprovs_organizados = "";
				$totales = [];
				$query = $this->compras_model->promediar_proveedores($id_proveedor = "", $fecha_ini, $fecha_fin);

				foreach ($query as $row) {
					array_push($imprevistos, $row["proveedor"]);
				}

				$organizar_ids = array_count_values($imprevistos); //Organizo los ID de los proveedores que no se repitan en un array.
				$idprovs_organizados = array_keys($organizar_ids); //Obtengo el valor asociativo del array, los cuales son los IDS de los proveedores.

				/* Busco los promedios obtenidos del proveedor en todas las solicitudes exitentes. Los proveedores se buscan por ID. */
				foreach ($idprovs_organizados as $id) {
					$request_info = $this->compras_model->promediar_proveedores($id);
					$num = 0;
					$prov_name = "";
					$res_sga = 0;
					$res_tipmat = 0;
					$res_tipserv = 0;
					$res_sst = 0;
					$res_del = 0;
					$num_soli_ser = 0;
					$num_soli_mat = 0;
					$num_soli_sga = 0;
					$num_soli_sst = 0;

					foreach ($request_info as $solicitudes) {
						$idproveedor  = $solicitudes["proveedor"];
						$num         += $solicitudes["resultado_final"];
						$res_sga     += $solicitudes["res_sga"];
						$res_tipmat  += $solicitudes["res_tipmat"];
						$res_tipserv += $solicitudes["res_tipserv"];
						$res_sst     += $solicitudes["res_sst"];
						$prov_name    = $solicitudes["nombre_proveedor"];
						$res_del += $this->calificacionTiempoEntrega((object)$solicitudes);

						$num_soli_ser += (!empty($solicitudes["res_tipserv"])) ? 1 : 0;
						$num_soli_mat += (!empty($solicitudes["res_tipmat"])) ? 1 : 0;
						$num_soli_sga += (!empty($solicitudes["res_sga"])) ? 1 : 0;
						$num_soli_sst += (!empty($solicitudes["res_sst"])) ? 1 : 0;
					}

					array_push($totales, [
						"idproveedor" => $idproveedor, 
						"prov_name"   => $prov_name, 
						"canti_sol"   => count($request_info), 
						"res_sga"     => (!empty($num_soli_sga)) ? round($res_sga / $num_soli_sga, 1): null, 
						"res_tipmat"  => (!empty($num_soli_mat)) ? round($res_tipmat / $num_soli_mat, 1): null, 
						"res_tipserv" => (!empty($num_soli_ser)) ? round($res_tipserv / $num_soli_ser, 1): null, 
						"res_sst"     => (!empty($num_soli_sst)) ? round($res_sst / $num_soli_sst, 1): null,
						"res_del"     => round($res_del / count($request_info), 1),
						"puntaje"     => round($num / count($request_info), 1)
					]);
				}

				$r = $totales;
			} else {
				$r = ["mensaje" => "No tiene permisos suficientes.", "tipo" => "error", "titulo" => ""];
			}
		}
		exit(json_encode($r));
	}

	public function solicitudes_promedo_proveedores(){
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$admin = false;
			$compra_user = false;

			if ($_SESSION["perfil"] == "Per_Admin" or $_SESSION["perfil"] == "Per_Adm_Com" or $_SESSION['perfil'] == "Per_Com") {
				$admin = true;
			} else {
				$admin = false;
			}

			if ($admin) {
				$idproveedor = $this->input->post("idproveedor");
				$r = $this->compras_model->solicitudes_promedo_proveedores($idproveedor);
			} else {
				$r = ["mensaje" => "No tiene permisos suficientes.", "tipo" => "error", "titulo" => ""];
			}
			exit(json_encode($r));
		}
	}

	/* Traer solicitudes que tengan solicitudes que tengan encuestas pendientes por realizr */
	public function encuestas_rp_faltantes()
	{
		if ($this->Super_estado == false) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {

			//AVISO SEGUN FILTROS DE SOLICITUDES CON ENCUESTAS PENDIENTES POR REALIZAR.
			$fecha_ini = $this->input->post("fecha_ini");
			$fecha_fin = $this->input->post("fecha_fin");
			$data_final = [];
			if (!empty($fecha_ini) and !empty($fecha_fin)) { //Corregir el cuando pasa a estado finalizado sin ser critico alto, no actualiza el listar solicitudes

				$check = $this->compras_model->proveedores_sin_encuesta($fecha_ini, $fecha_fin);
				$na = "N/A";

				foreach ($check as $row) {
					if ($row['sga'] == NULL) {
						$row['sga'] = $na;
					} else if ($row['sga'] == 0) {
						$row['sga'] = "no";
					} else if ($row['sga'] > 0) {
						$row['sga'] = "si";
					}

					if ($row['sst'] == NULL) {
						$row['sst'] = $na;
					} else if ($row['sst'] == 0) {
						$row['sst'] = "no";
					} else if ($row['sst'] > 0) {
						$row['sst'] = "si";
					}

					if ($row['tipser'] == NULL and $row['tipmat'] == 0) {
						$row['mat_ser'] = "no";
					} else if ($row['tipser'] == 0 and $row['tipmat'] == NULL) {
						$row['mat_ser'] = "no";
					} else if ($row['tipser'] == NULL and $row['tipmat'] == NULL) {
						$row['mat_ser'] = $na;
					} else if ($row['tipser'] > 0 or $row['tipmat'] > 0) {
						$row['mat_ser'] = "si";
					}

					array_push($data_final, $row);
				}
			}
			$r = $data_final;
		}
		exit(json_encode($r));
	}

	/* Check de encuestas si existen para dicha solicitud */
	public function check_encuestas_finalizadas($id_solicitud)
	{
		if ($this->Super_estado == false) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			if (empty($id_solicitud)) {
				$r = ['mensaje' => "Error interno; numero del error: " . __LINE__ . "", 'tipo' => "error", 'titulo' => "Error!"];
			} else {
				$encuesta_pendiente = [];
				$compra_inf = $this->compras_model->solicitud_compras_inf($id_solicitud, "row");

				if ($compra_inf) {
					$sst_enc_status = $compra_inf->sst_quiz;
					$sga_enc_status = $compra_inf->sga_quiz;
					$tipmat_enc_status = $compra_inf->tipmat_quiz;
					$tipser_enc_status = $compra_inf->tipser_quiz;

					$sst_enc_status == "0" && $sst_enc_status != NULL ? array_push($encuesta_pendiente, ["tipo_encuesta" => "sst_enc"]) : array_push($encuesta_pendiente, ["tipo_encuesta" => ""]);
					$sga_enc_status == "0" && $sga_enc_status != NULL ? array_push($encuesta_pendiente, ["tipo_encuesta" => "sga_enc"]) : array_push($encuesta_pendiente, ["tipo_encuesta" => ""]);
					$tipmat_enc_status == "0" && $tipmat_enc_status != NULL ? array_push($encuesta_pendiente, ["tipo_encuesta" => "tipmat_enc"]) : array_push($encuesta_pendiente, ["tipo_encuesta" => ""]);
					$tipser_enc_status == "0" && $tipser_enc_status != NULL ? array_push($encuesta_pendiente, ["tipo_encuesta" => "tipser_enc"]) : array_push($encuesta_pendiente, ["tipo_encuesta" => ""]);
				}

				return $encuesta_pendiente;
			}
			exit(json_encode($r));
		}
	}

	/* Listar areas de seleccion */
	public function listar_seleccion_area()
	{
		if ($this->Super_estado == false) {
			echo json_encode([]);
			return;
		} else {
			$datos = [];
			$area = $this->compras_model->find_idParametro('no_aplica');
			$query = $this->compras_model->listar_seleccion_area($area->idpa);
			$btn_ver = '<span title="Ver Preguntas" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default ver_preguntas" style="color:#2E79E5"></span>';
			$btn_asing = '<span title="Asignar permisos" data-toggle="popover" data-trigger="hover" class="fa fa-cogs btn btn-default asig_permiso" style="color: rgba(0,0,0,0.8)"></span>';
			foreach ($query as $row) {
				if ($row['idaux'] != "" && $row['idaux'] != "sst_sga_enc") {
					$row['accion'] = "$btn_asing $btn_ver";
					array_push($datos, $row);
				}
			}
			exit(json_encode($datos));
		}
	}

	/* Listar tipos de preguntas RP */
	public function listar_tipos_preguntasRP()
	{
		if ($this->Super_estado == false) {
			echo json_encode([]);
			return;
		} else {
			$datos = [];
			$newdata = [];
			$btn_ver = '<span title="Ver Preguntas" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default ver_preguntas" style="color:#6E1F7C"></span>';
			$btn_asig = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default admin_enc" style="color: #5CB85C"></span>';
			$btn_retirar = '<span class="fa fa-times btn btn-default retirar_enc" title="Retirar Encuesta" data-toggle="popover" data-trigger="hover" style="color:#BB3747;"></span>';

			$id_persona_selected = $this->input->post("idps");
			$dato = $this->compras_model->find_idParametro('tipos_pregRP');
			$tipos_encuestas = $this->compras_model->listar_tipos_preguntasRP('', $dato->idpa);

			foreach ($tipos_encuestas as $row) {
				$row["accion"] = "$btn_asig $btn_ver";
				array_push($datos, $row);
			}

			if (count($tipos_encuestas) > 0) {
				for ($x = 0; $x < count($tipos_encuestas); $x++) {
					$check = $this->check_encuesta_asignada($id_persona_selected, $tipos_encuestas[$x]["idaux"]);
					if ($check) {
						$datos[$x]["accion"] = "$btn_retirar $btn_ver";
					} else {
						$datos[$x]["accion"] = "$btn_asig $btn_ver";
					}
				}
			}

			foreach ($datos as $dato) {
				if ($dato['idaux'] != 'time_delivery') {
					array_push($newdata, $dato);
				}
			}
			exit(json_encode($newdata));
		}
	}

	/* Listar encuestas ya realizadas RP */
	public function list_finished_rp_encs()
	{
		if ($this->Super_estado == false) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$datos = [];
			$btn_ver = '<span title="Ver encuesta realizada!" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default ver_preguntas" style="color:#6E1F7C"></span>';
			$tipo_orden = $this->input->post('tipo_orden');
			$dato = $this->compras_model->find_idParametro('tipos_pregRP');
			$tipos_encuestas = $this->compras_model->listar_tipos_preguntasRP('', $dato->idpa);

			foreach ($tipos_encuestas as $row) {
				//if ($row['idaux'] != 'time_delivery') {
					$row["accion"] = "$btn_ver";
					array_push($datos, $row);
				//}
			}
			$r = $datos;
		}
		exit(json_encode($r));
	}

	/* Listar preguntas de encueestas segun el area seleccionada */
	public function listar_preguntas_encuestas()
	{
		$r = [];
		if ($this->Super_estado == false) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
			exit(json_encode($r));
		} else {
			$tipo_encuesta = $this->input->post("tipo_encuesta");
			if (empty($tipo_encuesta) || $tipo_encuesta == "") {
				exit(json_encode($tipo_encuesta));
			} else {
				$dato = $this->compras_model->find_idParametro('preguntas_rp');
				$query = $this->compras_model->listar_preguntas_encuestas($tipo_encuesta, $dato->idpa);
				$btn_del = '<span class="fa fa-trash btn btn-default del_pregunta_enc" title="Eliminar pregunta!" data-toggle="popover" data-trigger="hover" style="color:#EE4B61;"></span>';
				$btn_upd = '<span class="fa fa-refresh btn btn-default upd_pregunta_enc" title="Actualizar pregunta!" data-toggle="popover" data-trigger="hover" style="color:#3CCA40;"></span>';
				foreach ($query as $row) {
					$row['accion'] = "$btn_del $btn_upd";
					array_push($r, $row);
				}
			}
		}
		exit(json_encode($r));
	}

	public function Listar_codigos()
	{
		$codigos = array();
		if ($this->Super_estado == false) {
			echo json_encode($codigos);
			return;
		}
		$datos = $this->compras_model->Listar_codigos();
		foreach ($datos as $row) {
			$codigos["data"][] = $row;
		}
		echo json_encode($codigos);
	}
	public function Listar_historial_estado()
	{
		$historial = array();
		if ($this->Super_estado == false) {
			echo json_encode($historial);
			return;
		}
		$i = 1;
		$idsolicitud = $this->input->post("id");
		$datos = $this->compras_model->Listar_historial_estado($idsolicitud);
		foreach ($datos as $row) {
			$row["indice"] = $i;
			$historial["data"][] = $row;
			$i++;
		}
		echo json_encode($historial);
	}
	public function modificar_cod_orden_solicitud()
	{
		$historial = array();
		if ($this->Super_estado == false) {
			echo json_encode($historial);
			return;
		}
		$i = 1;
		$idsolicitud = $this->input->post("id");
		$codigo = $this->input->post("codigo");
		$datos = $this->compras_model->traer_solicitud($idsolicitud);
		if (empty($datos)) {
			echo json_encode(-1);
			return;
		}
		if ($datos[0]["id_estado_solicitud"] == "Soli_Fin") {
			echo json_encode(-2);
			return;
		}
		$res = $this->compras_model->modificar_cod_orden_solicitud($idsolicitud, $codigo);
		echo json_encode($res);
	}

	public function historial_articulos_entregas_parciales()
	{
		$historial_entregas = array();
		if ($this->Super_estado == false) {
			echo json_encode($historial_entregas);
			return;
		}
		$i = 1;
		$idsolicitud = $this->input->post("id");
		$id_articulo = $this->input->post("id_articulo");
		$datos = $this->compras_model->historial_articulos_entregas_parciales($idsolicitud, $id_articulo);
		foreach ($datos as $row) {
			$historial_entregas["data"][] = $row;
			$i++;
		}
		echo json_encode($historial_entregas);
	}
	public function Listar_solicitudes_en_comite($directivos = -1)
	{
		$solicitudes = array();
		if ($this->Super_estado == false) {
			echo json_encode($solicitudes);
			return;
		}
		$i = 1;
		$idcomite = $this->input->post("id");
		$datos = $this->compras_model->Listar_solicitudes_en_comite($idcomite, $directivos);
		$min_apro = $this->genericas_model->obtener_valores_parametro_aux("Min_Apro", 20);
		$limite = !empty($min_apro) ? $min_apro[0]["valor"] : 3;

		$adm =  ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Com" || $_SESSION["perfil"] == "Per_Com" || $_SESSION["perfil"] == "Per_Alm") ? true : false;
		$btn_cancelar = '<span title="Retirar" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default retirar" style="color:#d9534f"></span>';
		$btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
		$btn_notificacion = '<span title="Notificar Directivos" data-toggle="popover" data-trigger="hover" class="fa fa-bell btn btn-default notificar_dir" style="color:#3874A8"></span>';
		foreach ($datos as $row) {
			$row["gestion"] = $btn_cerrada;
			$row["no"] = $i;
			if ($directivos == 1)  $row["codigo"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" onclick="listar_proveedores_solicitud_comite_directivos(' . $row["id"] . ')"><span>ver</span></span>';
			else {
				if ($adm && $row["id_estado_comite"] != 'Com_Ter' && $row["vb"] < $limite) $row["gestion"] = "$btn_cancelar";
				if ($adm && $row["id_estado_comite"] == 'Com_Not' && $row["vb"] < $limite) $row["gestion"] .= " $btn_notificacion";
				$row["codigo"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" onclick="listar_proveedores_solicitud_comite(' . $row["id"] . ')"><span>ver</span></span>';
			}
			$i++;
			$solicitudes["data"][] = $row;
		}
		echo json_encode($solicitudes);
	}

	public function Listar_articulos($parciales = -1)
	{

		$articulos = array();
		if ($this->Super_estado == false) {
			echo json_encode($articulos);
			return;
		}
		$idsolicitud = $this->input->post("id");
		$datos = $this->compras_model->Listar_articulos($idsolicitud, $parciales);
		$sw = false;
		$admin = false;
		if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Com" || $_SESSION["perfil"] == "Per_Com" || $_SESSION["perfil"] == "Per_Alm") {
			$admin = true;
		}
		foreach ($datos as $row) {
			$row["con_tarjeta"] =  $row["fecha_compra_tarjeta"] ? 'SI' : 'NO';
			$row["codigo"] = '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>';
			$row["gestion"] = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
			if ($this->Super_elimina == 1) {
				if ($row["id_estado_solicitud"] == "Soli_Rev" && ($row["id_solicitante"] == $_SESSION['persona'] || $_SESSION['perfil'] == 'Per_Admin')) {
					$row["gestion"] = '<span title="Eliminar" style="color: #DE4D4D;"  data-toggle="popover" data-trigger="hover" class="fa fa-trash-o pointer btn btn-default" onclick="eliminar_articulo(' . $row["id"] . ')"></span>';
				}
				$sw = true;
			}

			if ($this->Super_modifica == 1) {
				if ($row["id_estado_solicitud"] == "Soli_Rev" && ($row["id_solicitante"] == $_SESSION['persona'] || $_SESSION['perfil'] == 'Per_Admin')) {
					$row["gestion"] = $row["gestion"] . ' ' . '<span style="color: #2E79E5;" title="Editar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench pointer btn btn-default" onclick="traer_articulo(' . $row["id"] . ')"></span>';
				}
				$sw = true;
			}

			if (!$sw) {
				$row["gestion"] = '<span title="Sin Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
			}
			$articulos["data"][] = $row;
		}
		echo json_encode($articulos);
	}
	public function traer_articulos_copia()
	{
		$articulos = array();
		if ($this->Super_estado == false) {
			echo json_encode($articulos);
			return;
		}
		$idsolicitud = $this->input->post("id");
		$datos = $this->compras_model->Listar_articulos($idsolicitud);
		echo json_encode($datos);
	}


	public function Listar_articulos_parciales($parciales = -1)
	{

		$articulos = array();
		if ($this->Super_estado == false) {
			echo json_encode($articulos);
			return;
		}
		$idsolicitud = $this->input->post("id");
		$datos = $this->compras_model->Listar_articulos_parciales($idsolicitud, $parciales);

		foreach ($datos as $row) {

			if ($this->Super_modifica == 1) {
				if (($row["id_estado_solicitud"] == "Soli_Pen" || $row["id_estado_solicitud"] == "Ser_Rec") &&  $parciales == 1) {
					if (empty($row["entregada"])) {
						$row["entregada"]  = "0";
					}
					$pendiente = $row["cantidad"] - $row["entregada"];
					$row["pendiente"] = '<input style="width:100%;border: 0;" class="form-control text-center"   name="' . $row["id"] . '" type="number" step="1" min="1" max="' . $pendiente . '" value="' . $pendiente . '" placeholder="Cantidad" required> ';

					$articulos["data"][] = $row;
				} else if ($row["id_estado_solicitud"] == "Soli_Par" &&  $parciales == 1) {
					if (!empty($row["entregada"])) {
						// $row["entregada"]  = $row["cantidad"] ;
						//   $row["pendiente"] = 'Ninguno';
						// }else{
						$pendiente = $row["cantidad"] - $row["entregada"];
						if ($pendiente > 0) {
							$row["pendiente"] = '<input style="width:100%;border: 0;" class="form-control text-center"   name="' . $row["id"] . '" type="number" step="1" min="1" max="' . $pendiente . '" value="' . $pendiente . '" placeholder="Cantidad" required> ';
							$articulos["data"][] = $row;
						}
					}
				}
				$sw = true;
			}

			if (!$sw) {
				$row["pendiente"] = '<span title="Sin Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
			}
		}
		echo json_encode($articulos);
	}

	public function eliminar_articulo()
	{
		if ($this->Super_estado == false) {
			echo json_encode('sin_session');
			return;
		}
		if ($this->Super_elimina == 0) {
			echo json_encode(-1302);
			return;
		}
		$id_solicitud = $this->input->post("idsolicitud");
		$idarticulo = $this->input->post("id");
		$solicitud = $this->compras_model->traer_solicitud($id_solicitud);
		if ($solicitud[0]["id_estado_solicitud"] != "Soli_Rev" || ($solicitud[0]["id_solicitante"] != $_SESSION['persona'] && $_SESSION['perfil'] != 'Per_Admin')) {
			echo json_encode(-7);
			return;
		}
		$datos = $this->compras_model->Listar_articulos($id_solicitud);
		if (count($datos) == 1) {
			echo json_encode(-6);
			return;
		}
		$datos = $this->compras_model->Eliminar_articulo($idarticulo);
		echo $datos;
	}
	public function cancelar_negado_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode('sin_session');
			return;
		}
		if ($this->Super_elimina == 0) {
			echo json_encode(-1302);
			return;
		}
		$id = $this->input->post("id");
		$datos = $this->compras_model->cancelar_negado_solicitud($id);
		if ($datos == 1) {
			$datos = $this->compras_model->cancelar_comentario_negado_solicitud($id);
		}
		echo $datos;
	}

	public function traer_articulo()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		$idarticulo = $this->input->post("id");
		$articulo = $this->compras_model->Traer_articulo($idarticulo);
		echo json_encode($articulo);
	}
	public function traer_proveedor_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		$id = $this->input->post("id");
		$datos = $this->compras_model->traer_proveedor_solicitud($id);
		$datos[0]["valor_dolar"] = $this->convertir_moneda($datos[0]["valor_dolar"], true, 2);
		$datos[0]["valor_total"] = $this->convertir_moneda($datos[0]["valor_total"], true, 0);
		echo json_encode($datos);
	}

	public function modificar_articulo()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(-1302);
			return;
		}
		$id_solicitud = $this->input->post("id_solicitud");
		$id_articulo = $this->input->post("id_articulo");
		$idcodigo_sap = $this->input->post("codigo_sap");
		$nombre_articulo = $this->input->post("nombre_articulo");
		$marca = $this->input->post("marca_art");
		$referencia = $this->input->post("referencia_art");
		$cantidad = $this->input->post("cantidad_art");
		$observaciones = $this->input->post("observaciones");
		$con_tarjeta = $this->input->post("con_tarjeta");
		$fecha_compra_tarjeta = $con_tarjeta ? $this->input->post("fecha_compra_tarjeta") : null;

		if (ctype_space($id_articulo) || empty($id_articulo)) {
			echo json_encode(-1);
			return;
		}
		if (empty($id_solicitud)) {
			echo json_encode(-1);
			return;
		}

		$solicitud = $this->compras_model->traer_solicitud($id_solicitud);
		if ($solicitud[0]["id_estado_solicitud"] != "Soli_Rev" || ($solicitud[0]["id_solicitante"] != $_SESSION['persona'] && $_SESSION['perfil'] != 'Per_Admin')) {
			echo json_encode(-7);
			return;
		}

		if (ctype_space($idcodigo_sap) || empty($idcodigo_sap)) {
			echo json_encode(-2);
			return;
		}
		if (ctype_space($nombre_articulo) || empty($nombre_articulo)) {
			echo json_encode(-8);
			return;
		}

		if (ctype_space($cantidad) || empty($cantidad)) {
			echo json_encode(-3);
			return;
		}
		if (ctype_space($observaciones) || empty($observaciones)) {
			echo json_encode(-10);
			return;
		}
		/*if (ctype_space($marca_art) || empty($marca_art)) {
            echo json_encode(-9);
            return;
        }
        if (ctype_space($referencia_art) || empty($referencia_art)) {
            echo json_encode(-4);
            return;
        }*/
		if (!is_numeric($cantidad)) {
			echo json_encode(-5);
			return;
		}
		if ($cantidad < 1) {
			echo json_encode(-6);
			return;
		}
		$fecha_valida = $this->validateDate($fecha_compra_tarjeta, 'Y-m-d');
		if ($con_tarjeta && !$fecha_valida) {
			echo json_encode(-11);
			return;
		}


		$resp = $this->compras_model->Modificar_articulo($id_articulo, $idcodigo_sap, $nombre_articulo, $marca, $referencia, $cantidad, $observaciones, $fecha_compra_tarjeta);
		echo json_encode($resp);
	}

	public function buscar_codigo_sap()
	{
		$codigos = array();
		if ($this->Super_estado == false) {
			echo json_encode($codigos);
			return;
		}
		$codigo = $this->input->post("nom_cod");
		$datos = $this->compras_model->buscar_codigo_sap($codigo);
		foreach ($datos as $row) {
			$codigos["data"][] = $row;
		}
		echo json_encode($codigos);
	}

	public function solicitudes_usuario()
	{
		$solicitudes = array();
		if ($this->Super_estado == false) {
			echo json_encode($solicitudes);
			return;
		}
		$sw = false;
		$i = 1;
		$id = $this->input->post("id");
		$datos = $this->compras_model->solicitudes_usuario($id);
		foreach ($datos as $row) {
			$row["indice"] = $i;
			$row["gestion"] = '';
			if ($this->Super_modifica == 1) {
				$row["gestion"] = '<span style="color: #d9534f;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-remove btn btn-default" onclick="confirmar_cambiar_estado(1,' . $row["id"] . ',1,' . $row["id_usuario"] . ')"></span>';
			}
			$row["estado"] = "Visible";
			if ($row["id_perfil"] == "Per_Adm_Com" || $row["id_perfil"] == "Per_Com" || $row["id_perfil"] == "Per_Alm") {
				$row["gestion"] .= ' <span style="color: black;" title="Administrar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-cog btn btn-default" onclick="estados_solicitudes_usuario(' . $row["id"] . ')"></span>';
			}
			$solicitudes["data"][] = $row;
			$i++;
		}

		echo json_encode($solicitudes);
	}
	public function estados_solicitudes_usuario()
	{
		$estados = array();
		if ($this->Super_estado == false) {
			echo json_encode($estados);
			return;
		}

		$i = 1;
		$id = $this->input->post("id");
		$datos = $this->compras_model->estados_solicitudes_usuario($id);
		foreach ($datos as $row) {
			$row["indice"] = $i;
			$row["gestion"] = '<span title="Sin Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
			if ($this->Super_modifica == 1) {
				$row["gestion"] = '<span style="color: #d9534f;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-remove btn btn-default" onclick="confirmar_cambiar_estado(2,' . $row["id"] . ',1,' . $row["id_solicitud_usuario"] . ')"></span>';
			}
			$estados["data"][] = $row;
			$i++;
		}
		echo json_encode($estados);
	}
	public function cargar_articulos($normal = -1)
	{
		$articulos = array();
		if ($this->Super_estado == false) {
			echo json_encode($articulos);
			return;
		}
		$idsolicitud = $this->input->post("idsolicitud");
		$datos = $this->compras_model->cargar_articulos($idsolicitud);
		if ($normal == 1) {
			echo json_encode($datos);
			return;
		}
		foreach ($datos as $row) {
			$articulos["data"][] = $row;
		}
		echo json_encode($articulos);
	}
	public function listar_solicitudes_sin_asignar()
	{
		$solicitudes = array();
		if ($this->Super_estado == false) {
			echo json_encode($solicitudes);
			return;
		}
		$idusuario = $this->input->post("idusuario");
		$datos = $this->compras_model->listar_solicitudes_sin_asignar($idusuario);
		foreach ($datos as $row) {
			if ($row["asig"] == null) {
				array_push($solicitudes, $row);
			}
		}
		echo json_encode($solicitudes);
	}

	public function listar_estados_sin_asignar()
	{
		$estados = array();
		if ($this->Super_estado == false) {
			echo json_encode($estados);
			return;
		}
		$id = $this->input->post("id");
		$datos = $this->compras_model->listar_estados_sin_asignar($id);
		foreach ($datos as $row) {
			if ($row["asig"] == null) {
				array_push($estados, $row);
			}
		}
		echo json_encode($estados);
	}
	public function get_fecha_habil()
	{

		if ($this->Super_estado == false) {
			echo json_encode(-1);
			return;
		}

		$rango_habil = $this->genericas_model->obtener_valores_parametro_aux("FecHab", 20);
		if (empty($rango_habil)) {
			$rango_habil = 28;
		} else {
			$rango_habil = $rango_habil[0]["valor"];
		}
		return $rango_habil;
	}

	public function validar_fecha()
	{
		$fecha_habil = '';
		$estado = -1;
		$hoy = date("d");
		$fh = $this->get_fecha_habil();
		if (intval($hoy . '') >= intval(1) && intval($hoy . '') <= intval($fh)) {
			$fecha_habil = date("Y-m-d H:i");
			$estado = 1;
		} else {
			$mes = date("Y-m");
			$fecha_habil = $mes . "-" . "1" . ' ' . date("H:i");

			$fecha_habil = strtotime('+1 month', strtotime($fecha_habil));
			$fecha_habil = date('Y-m-d H:i', $fecha_habil);
		}
		return array($estado, $fecha_habil);
	}

	/* Gestionar Solicitud */
	public function Gestionar_solicitud($idsolicitud = '', $estado = '', $motivo = '', $json = false)
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_modifica == 0) {
				echo json_encode(-1302);
				return;
			} else {
				empty($idsolicitud) ? $idsolicitud = $this->input->post("id") : false;
				empty($estado) ? $estado = $this->input->post("estado") : false;
				empty($motivo) ? $motivo = $this->input->post("motivo") : false;
				$criticoAlto =  $this->compras_model->traer_valor_parametro('', 'critico_alto');
				/* $idsolicitud = $this->input->post("id");
				$estado = $this->input->post("estado");
				$motivo = $this->input->post("motivo"); */
				$tipo_compra = null;
				$id_tipo_orden = null;
				$proveedor = null;
				$codigo = null;
				$fecha_estimada = null;
				$fecha_real = null;
				$clasificacion_proveedores = null;
				$area_selected = null;
				$tipmat_tip_ser = NULL;
				$sga_or_sst = NULL;
				$sst_sga_enc = false;

				$id_comite =  null;
				$observaciones =  null;
				$descripcion =  null;
				$usuario_registra = $_SESSION["persona"];
				$fecha_asig_com = null;
				$con_parciales = null;
				$causal = null;
				if (empty($idsolicitud)) {
					echo json_encode(-1);
					return;
				}
				$estado = $_SESSION['perfil'] == 'Per_Admin' || $_SESSION['perfil'] == 'Per_Adm_Com' || $_SESSION['perfil'] == 'Per_Com' || $_SESSION['perfil'] == 'Per_Alm' ? $estado : 'Ser_Rec';
				if (empty($estado)) {
					echo json_encode(-2);
					return;
				}
				$estado_valido = $this->validar_estado_siguiente($idsolicitud, $estado);

				//exit(json_encode($estado));

				if ($estado_valido == 1) {

					if ($estado != "Soli_Dev") {
						$motivo = null;
					}

					if ($estado == "Soli_Oco") {
						$proveedor = $this->input->post("proveedor");
						$codigo = $this->input->post("orden_comp");
						$fecha_estimada = $this->input->post("fecha_entrega_est");
						$id_tipo_orden = $this->input->post("id_tipo_orden");
						$clasificacion_proveedores = $this->input->post("clasi_proveedor");

						//buscar id en base de datos produccion
						$dato = $this->compras_model->find_idParametro('critico_alto');
						if ($clasificacion_proveedores == $dato->id) {
							$area_selected = $this->input->post("seleccion_area");
							$person = $this->input->post("id_solicitante");
							$tipmat_tip_ser = NULL;
							$sga_or_sst = NULL;
							$sst_sga_enc = false;
							$asignar_tipser_enc = false;

							//setear en 0 el campo que estara por encuesta por relaizar.
							$id_tipo_orden == "Tip_Mat" ? $tipmat_tip_ser = "estado_encuesta_tipmat" : $tipmat_tip_ser = "estado_encuesta_tipserv";
							$check_asigned_encs = $this->check_encuesta_asignada($person, $id_tipo_orden);

							/* Si la encuesta es tipo servicio, se le debe asignar la encuesta Tip_Ser al solicitante para que la realice
						una vez la encuesta llegue a su final */
							if ($id_tipo_orden == "Tip_Ser") {
								$asignar_tipser_enc = true;
								if ($asignar_tipser_enc) {
									if (!$check_asigned_encs) {
										$this->asignar_encuesta_rp($person, $id_tipo_orden, true);
									}
								}
							}

							//setear en 0 los campos que requieran realizar encuesta segun el area escogida
							$traer_idaux = $this->compras_model->traer_idaux_vp($area_selected);
							if ($traer_idaux->idaux == "sst_enc") {
								$sga_or_sst = "estado_encuesta_sst";
							} else if ($traer_idaux->idaux == "sga_enc") {
								$sga_or_sst = "estado_encuesta_sga";
							} else if ($traer_idaux->idaux == "sst_sga_enc") {
								$sst_sga_enc = true;
							}

							if ($area_selected == false) {
								$r = ["mensaje" => "No ha seleccionado ningún área, seleccione una e intente nuevamente.", "tipo" => "error", "titulo" => "Oops"];
								exit(json_encode($r));
							}

							$arrayTocheck = [
								"Selección de proveedor" => $proveedor,
								"Número de orden" => $codigo,
								"Días de entrega estimados" => $fecha_estimada,
								"Tipo de orden" => $id_tipo_orden,
								"Clasificación de proveedores" => $clasificacion_proveedores,
								"Selección de Área" => $area_selected
							];

							$check = $this->pages_model->verificar_campos_string($arrayTocheck);

							if (is_array($check)) {
								$r = ["mensaje" => "El campo: " . $check['field'] . ", está vacío o contiene información invalida.", "tipo" => "error", "titulo" => "Oops"];
								exit(json_encode($r));
							}

							$arrayNumsCheck = ["Días de entrega estimados" => $fecha_estimada];
							$check_numeros = $this->pages_model->verificar_campos_numericos($arrayNumsCheck);

							if (is_array($check_numeros)) {
								$r = ["mensaje" => "En el campo: " . $check_numeros['field'] . ", sólo acepta valores numéricos.", "tipo" => "error", "titulo" => "Oops"];
								exit(json_encode($r));
							}
						}
					} else if ($estado == "Soli_Fin") {

						$arts = $this->compras_model->Listar_articulos($idsolicitud);
							foreach ($arts as $art) {
									if($art['id_almacen']){
											$agre = $this->almacen_model->cambiar_cant_articulo($art['id_almacen'], $art['cantidad'], "Adición automatica Integración Almacen-Compras", "sum");
											if ($agre != 1) {
													echo json_encode(-1);
													return;
											}
									}
							}

						$see = $this->compras_model->solicitud_compras_inf($idsolicitud, 'row');

						if ($see) {
							$dato = $this->compras_model->find_idParametro('critico_alto');
							if ($see->id_clasificacion == $dato->id) {
								//Enviamos correo de notificación a quienes tengan encuestas asiganadas
								$dato = $this->compras_model->find_idParametro('tipos_pregRP');
								$permisos_encuestas = $this->compras_model->traer_permisos_encuestas('', '', $dato->idpa);
								$newarray = [];

								if ($see->enc_type == "sst_sga_enc") {

									foreach ($permisos_encuestas as $valor) {
										$valor['num_orden'] = $see->no_orden;
										$valor['solicitante'] = $see->solicitante;
										$valor['fecha_registra'] = $see->fecha_registra;
										if ($see->order_type == "Tip_Ser") {
											if ($valor['id_persona'] == $see->id_solicitante or ($valor['tipo_encuesta'] == "sga_enc" or $valor['tipo_encuesta'] == 'sst_enc')) {
												array_push($newarray, $valor);
											}
										} else {
											if ($valor['tipo_encuesta'] != 'Tip_Ser') {
												array_push($newarray, $valor);
											}
										}
									}
								} else if ($see->enc_type == "sst_enc") {

									foreach ($permisos_encuestas as $valor) {
										$valor['num_orden'] = $see->no_orden;
										$valor['solicitante'] = $see->solicitante;
										$valor['fecha_registra'] = $see->fecha_registra;
										if ($see->order_type == "Tip_Ser") {
											if ($valor['tipo_encuesta'] == $see->enc_type or ($valor['tipo_encuesta'] == $see->order_type and $valor['id_persona'] == $see->id_solicitante)) {
												array_push($newarray, $valor);
											}
										} else {
											if ($valor['tipo_encuesta'] != 'Tip_Ser' and $valor['tipo_encuesta'] != 'sga_enc') {
												array_push($newarray, $valor);
											}
										}
									}
								} else if ($see->enc_type == "sga_enc") {

									foreach ($permisos_encuestas as $valor) {
										$valor['num_orden'] = $see->no_orden;
										$valor['solicitante'] = $see->solicitante;
										$valor['fecha_registra'] = $see->fecha_registra;
										if ($see->order_type == "Tip_Ser") {
											if ($valor['tipo_encuesta'] == $see->enc_type or ($valor['tipo_encuesta'] == $see->order_type and $valor['id_persona'] == $see->id_solicitante)) {
												array_push($newarray, $valor);
											}
										} else {
											if ($valor['tipo_encuesta'] != 'Tip_Ser' and $valor['tipo_encuesta'] != 'sst_enc') {
												array_push($newarray, $valor);
											}
										}
									}
								} else if ($see->enc_type == "no_aplica") {

									foreach ($permisos_encuestas as $valor) {
										$valor['num_orden'] = $see->no_orden;
										$valor['solicitante'] = $see->solicitante;
										$valor['fecha_registra'] = $see->fecha_registra;
										if ($see->order_type == "Tip_Ser") {
											if ($valor['tipo_encuesta'] == $see->enc_type or ($valor['tipo_encuesta'] == $see->order_type and $valor['id_persona'] == $see->id_solicitante)) {
												array_push($newarray, $valor);
											}
										} else {
											if ($valor['tipo_encuesta'] != 'Tip_Ser' and $valor['tipo_encuesta'] != 'sst_enc' and $valor['tipo_encuesta'] != 'sga_enc') {
												array_push($newarray, $valor);
											}
										}
									}
								}

								$send = $this->enviar_correo($see->order_type, $newarray);
								if ($send != 1) {
									exit(json_encode($send));
								} else {
									$fecha_real = date('Y-m-d');
								}
							} else {
								$fecha_real = date('Y-m-d');
							}
						} else {
							$fecha_real = date('Y-m-d');
						}
					} else if ($estado == "Soli_Com") {
						$id_comite = $this->input->post("comite");
						$observaciones = $this->input->post("observaciones");
						$descripcion = $this->input->post("descripcion");
						if (ctype_space($id_comite) || empty($id_comite)) {
							echo json_encode(-1);
							return;
						}
						if (ctype_space($descripcion) || empty($descripcion)) {
							echo json_encode(-7);
							return;
						}
					} else if ($estado == "Soli_Par") {
						$con_parciales = $this->entregas_parciales(1);

						if ($con_parciales[0] != 0) {
							echo json_encode($con_parciales[0]);
							return;
						}
						if ($con_parciales[2] == 0) {
							echo json_encode(-12);
							return;
						}
					} else if ($estado == "Soli_Rec") {
						$tipo_compra = $this->input->post("tipo_compra");
						if (empty($tipo_compra) || ctype_space($tipo_compra)) {
							echo json_encode(-13);
							return;
						}
					} else if ($estado == "Soli_Dev") {
						$causal = $this->input->post("causal_compra");
						if (empty($causal) || ctype_space($causal)) {
							echo json_encode(-14);
							return;
						}
					}

					$res = $this->compras_model->Gestionar_solicitud($idsolicitud, $estado, $codigo, $fecha_estimada, $proveedor, $fecha_real, $descripcion, $observaciones, $id_comite, $motivo, $tipo_compra, $id_tipo_orden, $causal, $clasificacion_proveedores, $area_selected, $tipmat_tip_ser, $sga_or_sst, $sst_sga_enc);

					if ($res == 1 && $estado == "Soli_Fin") {
						$datos_gestion_final = $this->calcular_tiempo_solicitud($idsolicitud);
						//$res = $this->compras_model->guardar_tiempo_gestion($idsolicitud, $datos_gestion_final[0], $datos_gestion_final[1]);
						$res = 1;
						if ($res == 1 && $datos_gestion_final[2] == false) {
							$articulos = $this->compras_model->Listar_articulos($idsolicitud);
							$sin_parciales = array();
							foreach ($articulos as $row) {
								array_push($sin_parciales, array('cantidad' => $row["cantidad"], 'id_articulo' => $row["id"], 'usuario_registra' => $usuario_registra));
							}
							$res = $this->compras_model->entregas_parciales($sin_parciales);
						}
						echo json_encode($res);
						return;
					} else if ($res == 1 && $estado == "Soli_Par" && !is_null($con_parciales)) {
						$res = $this->compras_model->entregas_parciales($con_parciales[1]);
						echo json_encode($res);
						return;
					} else if ($estado == "Soli_Pen") {
						$datos_gestion_final = $this->calcular_tiempo_solicitud($idsolicitud);
						$res = $this->compras_model->guardar_tiempo_gestion($idsolicitud, $datos_gestion_final[0], $datos_gestion_final[1]);
						if ($res == 1) {
							//Dias no habiles, hace referencia a la fecha en que el proveedor debe entregar el material o el servicio teniendo en cuenta los dias no habiles
							$fecha_proveedor = $this->calcular_tiempo_proveedor($idsolicitud);
							$res = $this->compras_model->modificar_datos(array("dias_no_habiles" => $fecha_proveedor), "solicitud_compra", $idsolicitud);
						}
					}
					if ($json == false) {
						echo json_encode($res);
						return;
					} else {
						return $res;
					}
				}
				echo json_encode($estado_valido);
				return;
			}
		}
		echo json_encode(-1);
		return;
	}

	/* Funcion para enviar los correos de notificacion segun las encuestas y quienes las tengan asignadas AQUII */
	public function enviar_correo($tipo_orden = "", $permisos_encuestas = [])
	{
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "tipo" => ""];
		} else {
			$baseurl = base_url();
			foreach ($permisos_encuestas as $valor) {
				$correo = $valor["correo"];
				$nombre = $valor["full_name"];
				//$correo = "jpena41@cuc.edu.co";
				//$nombre = "Fabio Jaramillo";
				$msg = "La reevaluación de proveedor <strong>" . $valor['nombre_encuesta'] . "</strong> está pendiente por realizar.<br><br>
				<i><u>Detalles se la solicitud</u></i>: <br>
				<small><strong>Numero de solicitud: </strong>" . $valor['num_orden'] . ".</small><br>" .
					"<small><strong>Solicitante: </strong>" . $valor['solicitante'] . ".<br></small>" .
					"<small><strong>Fecha de solicitud: </strong>" . $valor['fecha_registra'] . ".<br><br></small>" .
					"Ingrese a <a href='" . $baseurl . "index.php/compras' target='new_black'>AGIL</a> y de clic en el ítem llamado <strong>'Estados de Solicitudes'</strong> para más información.";
				$desde = "Compras CUC";
				$asunto = "¡Realización de encuesta!";
				$codigoo = "ParCodAdm";
				$notificar = $this->enviar_correo_personalizado("comp", $msg, $correo, $nombre, $desde, $asunto, $codigoo, 1);
				//$notificar = 1;
				if ($notificar != 1) {
					exit(json_encode(["mensaje" => "No se pudo enviar el correo de notificación.", "tipo" => "warning", "titulo" => "Oops"]));
				} else {
					$r = $notificar;
				}
			}
		}
		return $r;
	}

	/* Listar preguntas RP */
	public function preguntas_rp($tipo_encuesta = "", $idsolicitud = "")
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			exit();
		} else {

			if (empty($tipo_encuesta) and empty($idsolicitud)) {
				$tipo_encuesta = $this->input->post("tipo_encuesta");
				$idsolicitud = $this->input->post("idsolicitud");
			}

			$check = $this->check_finished_encs($idsolicitud, $tipo_encuesta);
			if ($check["res"] == "no") {
				$idparametro = $this->compras_model->traer_valor_parametro('', '', '', 'preguntas_rp');
				$query = $this->compras_model->preguntas_rp($tipo_encuesta, $idparametro->idparametro);
				$r = $query;
			} else {
				$r = $check;
			}
			exit(json_encode($r));
		}
	}

	/* Listar respuestas con sus porcentajes */
	public function listar_respuestas_rp()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			exit();
		} else {
			$r = [];
			$tipoResp = $this->compras_model->find_idParametro('tipos_resp');
			$query = $this->compras_model->listar_respuestas_rp($tipoResp->idpa);
			if ($query) {
				$r = $query;
			} else {
				$r = ["mensaje" => "No se encontraron resultados.", "tipo" => "error", "titulo" => "Oops"];
			}
			exit(json_encode($r));
		}
	}

	public function check_area_selected($ids)
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			exit();
		} else {
			if (empty($ids) || $ids == "" || $ids ==  false) {
				$r = ["mensaje" => "Error" . __LINE__ . ". Consulte con el administrador del sistema.", "tipo" => "error", "titulo" => "Oops"];
			} else {
				$query = $this->compras_model->traer_area_selected($ids);
				if ($query) {
					$r = $query;
				}
			}
			exit(json_encode($r));
		}
	}

	/* Fin de check encuestas */

	public function asignar_solicitud_comite()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_modifica == 0) {
				echo json_encode(-1302);
			} else {
				//REVISAR
				// $res = $this->compras_model->asignar_solicitud_comite($id_solicitud, $descripcion,$observaciones, $id_comite,$usuario_registra,$fecha_asig_com);
				// echo json_encode($res);
				return;
			}
		}
	}





	public function cambiar_permisos_usuarios_solicitudes()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_modifica == 0) {
				echo json_encode(-1302);
				return;
			} else {
				$id = $this->input->post("id");
				$estado = $this->input->post("estado");
				$tipo = $this->input->post("tipo");
				if (empty($id)) {
					echo json_encode(-1);
					return;
				}

				$res = $this->compras_model->cambiar_permisos_usuarios_solicitudes($id, $estado, $tipo);
				echo json_encode($res);
				return;
			}
		}
		echo json_encode(-1);
		return;
	}
	public function traer_solicitud()
	{
		$solicitudes = array();
		if ($this->Super_estado == false) {
			echo json_encode($solicitudes);
			return;
		}
		if ($this->Super_agrega == 0) {
			echo json_encode(array(-1302));
		} else {
			$idSolicitud = $this->input->post("id");
			$tipo = $this->input->post("tipo");
			if ($tipo == 1) {
				$solicitud = $this->compras_model->traer_solicitud_completa($idSolicitud);
			} else {
				$solicitud = $this->compras_model->traer_solicitud($idSolicitud);
			}

			echo json_encode($solicitud);
		}
	}

	public function calcular_tiempo_solicitud($idsolicitud)
	{

		$historial_estados = $this->compras_model->Listar_historial_estado($idsolicitud);
		$tiempo_habil = $this->genericas_model->obtener_valores_parametro_aux("Tim_Hab", 20);
		if (empty($tiempo_habil)) {
			$tiempo_habil = 8;
		} else {
			$tiempo_habil = $tiempo_habil[0]["valor"];
		}
		$fecha_inicial = null;
		$fecha_final = null;
		$sw_parcial = false;
		$tiempo_muerto = 0;
		for ($i = 0; $i < count($historial_estados); $i++) {
			$row = $historial_estados[$i];
			if ($row["id_estado"] == "Soli_Rec") {
				$fecha_inicial = $row["fecha_cambio"];
			}
			if ($row["id_estado"] == "Soli_Pen") {
				$fecha_final = $row["fecha_cambio"];
			}
			if ($row["id_estado"] == "Soli_Par") {
				$sw_parcial = true;
			}
		}
		$dias = null;
		$aux = true;
		if (!is_null($fecha_inicial) && !is_null($fecha_final)) {
			$strInicio = date("Y-m-d", strtotime($fecha_inicial));
			$strFinal = date("Y-m-d", strtotime($fecha_final));
			$c_day = date("Y-m-d", strtotime($fecha_inicial));
			while ($aux) {
				if ($this->es_habil($c_day)) {
					$dias += 1;
				}
				$c_day = date("Y-m-d", strtotime("$c_day + 1 days"));
				if ($c_day > $strFinal) {
					$aux = false;
				}
			}
		}
		return array($dias, $tiempo_habil, $sw_parcial);
	}

	public function calcular_tiempo_gestion()
	{
		if ($this->Super_estado == false) {
			echo json_encode(array("sin_session"));
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(array(-1302));
			return;
		}
		$id = $this->input->post("id");
		$datos_gestion_final = $this->calcular_tiempo_solicitud($id);
		//$res = $this->compras_model->guardar_tiempo_gestion($id, $datos_gestion_final[0], null);
		echo json_encode(array(1, $datos_gestion_final[0]));
		return;
	}

	public function modificar_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		if ($this->Super_modifica == 0  || ($_SESSION["perfil"] != "Per_Admin"  && $_SESSION["perfil"] != "Per_Adm_Com")) {
			echo json_encode(-1302);
			return;
		}
		$observaciones = $this->input->post("observaciones");
		$id_solicitud = $this->input->post("id_solicitud");
		$tipo_compra = $this->input->post("tipo_compra");
		$usuario = $_SESSION['persona'];

		if (ctype_space($id_solicitud) || empty($id_solicitud)) {
			echo json_encode(-8);
			return;
		}
		if (ctype_space($tipo_compra) || empty($tipo_compra)) {
			echo json_encode(-1);
			return;
		}
		if (ctype_space($observaciones) || empty($observaciones)) {
			echo json_encode(-3);
			return;
		}
		$solicitud = $this->compras_model->traer_solicitud($id_solicitud);
		$id_tipo_actual = $solicitud[0]['id_tipo_compra'];

		if ($id_tipo_actual == $tipo_compra) {
			echo json_encode(-2);
			return;
		}
		if ($id_tipo_actual == "Soli_Sin" || $tipo_compra == 'Soli_Sin') {
			echo json_encode(-4);
			return;
		}


		$resp = $this->compras_model->Modificar_solicitud($id_solicitud, $tipo_compra, $observaciones);
		echo json_encode($resp);
	}
	/**
	 * Muestra las personas que tienen asignado el perfil de compras  
	 * @return Array
	 */
	function listar_responsables_procesos()
	{
		$personas = array();

		if ($this->Super_estado == false) {
			echo json_encode($personas);
			return;
		}
		$sw = false;
		$i = 1;
		$hey = $this->input->post("persona_selected");
		$datos = $this->compras_model->listar_responsables_procesos($hey);

		foreach ($datos as $row) {
			$row["indice"] = $i;

			if ($row["id_perfil"] == "Per_Dir" || $row["id_perfil"] == "Per_Dir_t2") {
				$row["tipo"] = '<span   style="background-color: #428bca;color: white; width: 100%;" class="pointer form-control" ><span>' . $row["tipo"] . '</span></span>';
			} else {
				$row["tipo"] = '<span  style="background-color: #5cb85c;color: white; width: 100%;" class="pointer form-control" ><span>' . $row["tipo"] . '</span></span>';
			}
			$row["gestion"] = '<span style="color: black;" title="Administrar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-cog btn btn-default" onclick="solicitudes_usuario(' . $row["id"] . ')"></span>';
			$personas["data"][] = $row;
			$i++;
		}

		echo json_encode($personas);
		return;
	}


	function cargar_archivo($mi_archivo, $ruta, $nombre)
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


	public function cargar_archivo_2()
	{
		$id_compra = $this->input->post("id");
		$id_cronograma = $this->input->post("idCrono");
		if (!isset($id_cronograma) || $id_cronograma <= 0) {
			$id_cronograma = NULL;
		}
		$nombre = $_FILES["file"]["name"];
		$cargo = $this->cargar_archivo("file", $this->ruta_archivos_solicitudes, "compdw");
		if ($cargo[0] == -1) {
			header("HTTP/1.0 400 Bad Request");
			echo ($nombre);
			return;
		}
		$res = $this->compras_model->guardar_archivo_compra($id_compra, $nombre, $cargo[1], $id_cronograma);
		if ($res == "error") {
			header("HTTP/1.0 400 Bad Request");
			echo ($nombre);
			return;
		}
		echo json_encode($res);
		return;
	}

	public function listar_archivo_compra()
	{
		$historial = array();
		if ($this->Super_estado == false) {
			echo json_encode($historial);
			return;
		}
		$i = 1;
		$id_compra = $this->input->post("id");
		$datos = $this->compras_model->listar_archivo_compra($id_compra);
		foreach ($datos as $row) {
			$row["indice"] = $i;
			$historial["data"][] = $row;
			$i++;
		}
		echo json_encode($historial);
	}

	public function validar_estado_siguiente($idsolicitud, $estado_asp)
	{
		$solicitud = $this->compras_model->traer_solicitud($idsolicitud);
		$estado = $solicitud[0]['id_estado_solicitud'];
		$comite = $solicitud[0]['id_comite'];
		$tipo_orden = $solicitud[0]['id_tipo_orden'];
		if ($estado_asp == $estado) {
			return -11;
		}
		if ($estado_asp == "Soli_Com" && !is_null($comite)) {
			return -122;
		}
		//revision
		if ($estado == "Soli_Rev") {
			if ($estado_asp == "Soli_Rec" || $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		} //recibido
		else if ($estado == "Soli_Rec") {
			if ($estado_asp == "Soli_Cot" || $estado_asp == "Soli_Cac" || $estado_asp == "Soli_Pre" || $estado_asp == "Soli_Pro" || $estado_asp == "Soli_Com" ||  $estado_asp == "Soli_Oco" || $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		} //cotizacion
		else if ($estado == "Soli_Cot") {
			if ($estado_asp == "Soli_Pro" || $estado_asp == "Soli_Pre" || $estado_asp == "Soli_Com" || $estado_asp == "Soli_Cac" || $estado_asp == "Soli_Oco" || $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		} //creacion de activo
		else if ($estado == "Soli_Cac") {
			if ($estado_asp == "Soli_Oco" || $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		} //presupuesto
		else if ($estado == "Soli_Pre") {
			if ($estado_asp == "Soli_Cot" || $estado_asp == "Soli_Com" || $estado_asp == "Soli_Pro" || $estado_asp == "Soli_Cac" || $estado_asp == "Soli_Oco" || $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		} //creacion proveedores
		else if ($estado == "Soli_Pro") {
			if ($estado_asp == "Soli_Pre" || $estado_asp == "Soli_Com" || $estado_asp == "Soli_Cac" ||  $estado_asp == "Soli_Oco" || $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		} //comite compras
		else if ($estado == "Soli_Com") {
			if ($estado_asp == "Soli_Pro" || $estado_asp == "Soli_Pre" || $estado_asp == "Soli_Cac" ||  $estado_asp == "Soli_Oco" || $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		} //orden de compras
		else if ($estado == "Soli_Oco") {
			if ($estado_asp == "Soli_Mon" || $estado_asp == "Soli_Lib" || $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		} //liberacion 
		else if ($estado == "Soli_Lib") {
			if ($estado_asp == "Soli_Ord" || $estado_asp == "Soli_Pen" || $estado_asp == "Soli_Dev" || $estado_asp == "Soli_Pdoc") {
				return 1;
			}
			return -11;
		} //pendiente documento
		else if ($estado == "Soli_Pdoc") {
			if ($estado_asp == "Soli_Ord" || $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		} //pendiente anticipo
		else if ($estado == "Soli_Ord") {
			if ($estado_asp == "Soli_Pen" || $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		} //pendiente entrega
		else if ($estado == "Soli_Pen") {

			if ($tipo_orden == 'Tip_Ser') {
				if ($estado_asp == "Ser_Rec" ||  $estado_asp == "Soli_Dev" || $estado_asp == "Soli_Par") {
					return 1;
				}
				return -123;
			} else {
				if ($estado_asp == "Soli_Fin" || $estado_asp == "Soli_Par" ||  $estado_asp == "Soli_Dev") {
					return 1;
				}
				return -11;
			}
		} //pendiente entrega
		else if ($estado == "Ser_Rec") {
			if ($estado_asp == "Soli_Fin" || $estado_asp == "Soli_Par" ||  $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		} //entrega parcial
		else if ($estado == "Soli_Par") {
			if ($estado_asp == "Soli_Fin") {
				return 1;
			}
			return -11;
		}
		//moneda extranjera
		else if ($estado == "Soli_Mon") {
			if ($estado_asp == "Soli_Lib" || $estado_asp == "Soli_Dev") {
				return 1;
			}
			return -11;
		}
		return -11;
	}
	function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$fecha_actual = date($format);
		$d = DateTime::createFromFormat($format, $date);
		$valida = $d && $d->format($format) == $date;
		if ($valida && ($d->format($format) < $fecha_actual)) return false;
		return $valida;
	}
	public function guardar_encuesta()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_agrega == 0) {
				echo json_encode(-1302);
			} else {
				$id = $this->input->post("id");
				$resp1 = $this->input->post("respuesta1");
				$resp2 = $this->input->post("respuesta2");
				$resp3 = $this->input->post("respuesta3");
				$observaciones = $this->input->post("observaciones_encu");

				if (ctype_space($id) || empty($id)) {
					echo json_encode(-11);
					return;
				}
				if (ctype_space($resp1) || empty($resp1)) {
					echo json_encode(-1);
					return;
				}
				if (ctype_space($resp2) || empty($resp2)) {
					echo json_encode(-2);
					return;
				}
				if (ctype_space($resp3) || empty($resp3)) {
					echo json_encode(-3);
					return;
				}
				if (($resp1 == 1 || $resp1 == 2 || $resp2 == 1 || $resp2 == 2 || $resp3 == 1 || $resp3 == 2) && empty($observaciones)) {
					echo json_encode(-4);
					return;
				}

				$resp = $this->compras_model->guardar_encuesta($id, $resp1, $resp2, $resp3, $observaciones);
				echo json_encode($resp);
				return;
			}
		}
	}

	/* guardar_encuestas rp */
	public function guardar_encuestas_rp()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			$r = [];
			$preg = $this->input->post("preguntas_array");
			$resp = $this->input->post("respuestas_array");

			$idsol = $this->input->post("ids");
			$ids = [];
			if (is_array($idsol)) {
				$ids = $idsol;
			} else {
				array_push($ids, $idsol);
			}

			$id_enc_type = $this->input->post("id_enc_type");
			$tipo = $this->input->post("tipo");
			$obs = $this->input->post("obs");
			$seguir = false;

			/* Validamos que la respuesta enviada no sea mayor a 3 y se envie una observacion no deseada. */
			for ($x = 0; $x < count($resp); $x++) {
				$res_chck = $this->traer_valor_parametro($resp[$x]);
				if ($res_chck->valor > 3 and $obs[$x]["obs"] != "") {
					$r = ["mensaje" => "No se puede enviar la observación de la pregunta #" . ($x + 1) . ", debido a que la respuesta seleccionada es mayor a 3.", "tipo" => "error", "titulo" => ""];
					$seguir = false;
					break;
				} else {
					$seguir = true;
				}
			}

			$campo = "";
			if ($tipo == "sst_enc") {
				$campo = "estado_encuesta_sst";
			} else if ($tipo == "sga_enc") {
				$campo = "estado_encuesta_sga";
			} else if ($tipo == "Tip_Mat") {
				$campo = "estado_encuesta_tipmat";
			} else if ($tipo == "Tip_Ser") {
				$campo = "estado_encuesta_tipserv";
			}

			if ($seguir) { //Si las observaciones y las respuestas cumplen con lo requerido, todo continua normal.
				if (count($preg) <= 0) {
					$r = ["mensaje" => "Error " . __LINE__ . ". Consulte con el administrador del sistema.", "tipo" => "error", "titulo" => "Oops"];
				} else {
					if (count($resp) != count($preg)) {
						$r = ["mensaje" => "Verifique que todas las preguntas estén resueltas.", "tipo" => "error", "titulo" => "Oops"];
					} else {
						for ($i = 0; $i < count($ids); $i++) {
							for ($x = 0; $x < count($preg); $x++) {
								if ($resp[$x] == "" || $resp[$x] == NULL || empty($resp[$x]) || $resp[$x] == false) {
									$r = ["mensaje" => "Error " . __LINE__ . ". Consulte con el administrador del sistema.", "tipo" => "error", "titulo" => "Oops"];
								} else {

									if (empty($obs[$x]["obs"])) {
										$obs[$x]["obs"] = "N/A";
									}

									$dataToSend = [
										"id_solicitud" => $ids[$i],
										"id_enc_type" => $id_enc_type,
										"id_pregunta" => $preg[$x],
										"id_respuesta" => $resp[$x],
										"id_tipo_encuesta" => $tipo,
										"id_usuario_registra" => $_SESSION['persona'],
										"observaciones" => $obs[$x]["obs"]
									];

									$query = $this->compras_model->guardar_info("compras_encuestas", $dataToSend);

									if (!empty($query)) {
										$r = ["mensaje" => "La operación no se ha podido completar. Codigo del error: " . __LINE__ . ".", "tipo" => "error", "titulo" => "Oops"];
									}
								}
							}

							$num = 0;

							for ($x = 0; $x < count($resp); $x++) {
								$valor = $this->traer_valor_parametro($resp[$x])->valor;
								$num += $valor;
							}

							$promedio = ($num / count($resp));

							$dataToUpd = [$campo => round($promedio, 1), "id_usuario_registra" => $_SESSION['persona']];
							$status_upd = $this->compras_model->modificar_datos($dataToUpd, "solicitud_compra", $ids[$i]);
							if ($status_upd) {

								$r = ["mensaje" => "¡La operación se ha realizado correctamente!", "tipo" => "success", "titulo" => "Bien!"];

								$check_encs_restantes = $this->check_encuestas_finalizadas($ids[$i]);

								if ($check_encs_restantes) {
									$calcular_rp_final = true;
									foreach ($check_encs_restantes as $val) {
										if ($val["tipo_encuesta"] != "" or $val["tipo_encuesta"] != null) {
											$calcular_rp_final = false;
										}
									}
									if ($calcular_rp_final) {
										$resolve = $this->calcular_porcentaje_rp($ids[$i]);
										if ($resolve) {
											$r = ["mensaje" => "¡La operación se ha realizado correctamente!", "tipo" => "success", "titulo" => "Bien!"];
										} else {
											exit(json_encode(["mensaje" => "La encuesta se guardó correctamente, pero, no se pudo realizar el calculo final. Contacte con el área de sistemas.", "tipo" => "info", "titulo" => "Oops"]));
										}
									}
								}
							} else {
								$r = ["mensaje" => "La operación no se pudo completar. Error " . __LINE__ . ".", "tipo" => "error", "titulo" => "Oops"];
							}
						}
					}
				}
			}
			exit(json_encode($r));
		}
	}

	/* Funcion para traer el valor de un valor_parametro por su ID */
	public function traer_valor_parametro($id = "", $idaux = "")
	{
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$query = $this->compras_model->traer_valor_parametro($id, $idaux);
			if ($query) {
				$r = $query;
			} else {
				$r = [];
			}
		}
		return $r;
	}

	function dateDifference($date_1, $date_2, $differenceFormat = '%a')
	{
		$datetime1 = date_create($date_1);
		$datetime2 = date_create($date_2);
		$interval = date_diff($datetime1, $datetime2);
		return $interval->format($differenceFormat);
	}

	public function guardar_comite()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_agrega == 0) {
				echo json_encode(-1302);
			} else {

				$nombre = $this->input->post("nombre");
				//$fecha = $this->input->post("fecha");
				$fecha = null;
				$descripcion = $this->input->post("descripcion");
				$usuario = $_SESSION['persona'];

				if (ctype_space($nombre) || empty($nombre)) {
					echo json_encode(1);
					return;
				}
				$existe = $this->compras_model->existe_nombre_comite($nombre);

				if (!empty($existe)) {
					echo json_encode(4);
					return;
				}

				/* if (ctype_space($fecha) || empty($fecha)) {
                    echo json_encode(2);
                    return;
                }

                $fecha_valida = $this->validateDate($fecha, 'Y-m-d');
                if (!$fecha_valida) {
                    echo json_encode(3);
                    return;
                }*/
				$res = $this->compras_model->guardar_comite($nombre, $fecha, $descripcion, $usuario);
				echo json_encode($res);
				return;
			}
		}
	}
	public function listar_comites($normal = -1)
	{
		$comites = array();
		if ($this->Super_estado == false) {
			echo json_encode($comites);
			return;
		}

		if ($normal == 1) {
			$datos = $this->compras_model->listar_comites(0, "Com_Ini");
			echo json_encode($datos);
			return;
		}
		$datos = $this->compras_model->listar_comites();
		$i = 1;
		$sw = false;
		foreach ($datos as $row) {

			$row["gestion"] = '<span title="" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';

			$row["codigo"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span>ver</span></span>';
			if ($this->Super_elimina == 1) {
				if ($row["id_estado_comite"] == "Com_Ini") {
					$row["gestion"] = '<span title="cambiar a En Curso" style="color: #39B23B;"  data-toggle="popover" data-trigger="hover" class="fa fa-share-square-o pointer btn btn-default" onclick="confirm_eliminar_comite(' . $row["id"] . ')"></span>';
				}
				$sw = true;
			}
			if ($this->Super_modifica == 1) {
				if ($row["id_estado_comite"] == "Com_Ini") {
					$row["gestion"] = $row["gestion"] . ' ' . '<span style="color: #2E79E5;" title="Editar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench pointer btn btn-default" onclick="traer_comite(' . $row["id"] . ')"></span>';
				}
				$sw = true;
			}
			if ($row["id_estado_comite"] == "Com_Ter") {
				$row["codigo"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: #39B23B;color: white; width: 100%;" class="pointer form-control" ><span>ver</span></span>';
			} else if ($row["id_estado_comite"] == "Com_Not") {
				$row["codigo"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover"style="background-color: #004078;color: white; width: 100%;" class="pointer form-control" ><span>ver</span></span>';
			} else {
				$row["codigo"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%;" class="pointer form-control" ><span>ver</span></span>';
			}
			if (!$sw) {
				$row["gestion"] = '<span title="Sin Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
			}
			$comites["data"][] = $row;
			$i++;
		}
		echo json_encode($comites);
	}

	public function listar_comites_directivos()
	{
		$comites = array();
		if ($this->Super_estado == false) {
			echo json_encode($comites);
			return;
		}
		$id = $this->input->post("id");
		$datos = $this->compras_model->listar_comites($id, null, 1);
		$i = 1;
		foreach ($datos as $row) {
			$row["gestion"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span>ver</span></span>';
			if ($row["id_estado_comite"] == "Com_Ter") {
				$row["gestion"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: #5cb85c;color: white; width: 100%;" class="pointer form-control" ><span>ver</span></span>';
			} else if ($row["id_estado_comite"] == "Com_Ini") {
				$row["gestion"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: #428bca;color: white; width: 100%;" class="pointer form-control" ><span>ver</span></span>';
			}
			$row["codigo"] = '<span title="Abrir" data-toggle="popover" data-trigger="hover" class="fa fa-folder-open"></span>';
			$comites["data"][] = $row;
			$i++;
		}
		echo json_encode($comites);
	}

	public function modificar_comite()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_modifica == 0) {
				echo json_encode(-1302);
			} else {
				$estado = null;
				$id = $this->input->post("id");
				$nombre = $this->input->post("nombre");
				// $fecha = $this->input->post("fecha");
				$descripcion = $this->input->post("descripcion");
				$delete = $this->input->post("delete");

				if (!empty($delete)) {
					$estado = "Com_Not";
				} else {
					$estado = null;
					if (ctype_space($nombre) || empty($nombre)) {
						echo json_encode(1);
						return;
					}
					/* if (ctype_space($fecha) || empty($fecha)) {
                        echo json_encode(2);
                        return;
                    }
                        $fecha_valida = $this->validateDate($fecha, 'Y-m-d');

                    if (!$fecha_valida) {
                        echo json_encode(3);
                        return;
                    }*/
				}

				$res = $this->compras_model->modificar_comite($id, $nombre, $descripcion, $estado);
				echo json_encode($res);
				return;
			}
		}
	}

	public function traer_comite()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		$idcomite = $this->input->post("id");
		$comite = $this->compras_model->traer_comite($idcomite);
		echo json_encode($comite);
	}


	public function guardar_proveedor_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_agrega == 0) {
				echo json_encode(-1302);
			} else {
				$id_solicitud = $this->input->post("id_solicitud");
				$nombre = $this->input->post("nombre");
				$valor_total = $this->input->post("valor_total");
				$precio_dolar = $this->input->post("precio_dolar");
				$iva = $this->input->post("iva");
				$moneda = $this->input->post("moneda");
				$administracion = $this->input->post("administracion");
				$imprevistos = $this->input->post("imprevistos");
				$utilidad = $this->input->post("utilidad");
				$coceptos = $this->input->post("coceptos");
				$valor_dolar = null;

				$adjunto = null;
				$usuario_registra = $_SESSION['persona'];
				$aprobo = $this->compras_model->traer_proveedor_aprobados_persona($id_solicitud, null);
				if (empty($aprobo)) {
					if (ctype_space($nombre) || empty($nombre)) {
						echo json_encode(1);
						return;
					}
					if (ctype_space($valor_total) || empty($valor_total)) {
						echo json_encode(2);
						return;
					}



					$valor_total = $this->convertir_moneda($valor_total, false);
					if (!is_numeric($valor_total)) {
						echo json_encode(4);
						return;
					}
					if (!is_numeric($iva)) {
						echo json_encode(5);
						return;
					}

					if ($moneda == "usd") {
						if (ctype_space($precio_dolar) || empty($precio_dolar)) {
							echo json_encode(11);
							return;
						}
						$precio_dolar = $this->convertir_moneda($precio_dolar, false);
						if (!is_numeric($precio_dolar)) {
							echo json_encode(6);
							return;
						}
						$valor_dolar = $valor_total;
						$valor_total = $valor_total * $precio_dolar;

						if (!is_numeric($valor_dolar)) {
							echo json_encode(10);
							return;
						}

						if (!is_numeric($valor_total)) {
							echo json_encode(4);
							return;
						}
					} else {
						$precio_dolar = null;
						$valor_dolar = null;
					}



					if ($coceptos == 1) {
						if (ctype_space($administracion) || empty($administracion)) {
							$administracion = null;
						} else if (!is_numeric($administracion)) {
							echo json_encode(7);
							return;
						}
						if (ctype_space($imprevistos) || empty($imprevistos)) {
							$imprevistos = null;
						} else if (!is_numeric($imprevistos)) {
							echo json_encode(8);
							return;
						}
						if (ctype_space($utilidad) || empty($utilidad)) {
							$utilidad = null;
						} else if (!is_numeric($utilidad)) {
							echo json_encode(9);
							return;
						}
					} else {
						$administracion = null;
						$imprevistos = null;
						$utilidad = null;
					}


					$cargo = $this->cargar_archivo("adjunto", $this->ruta_archivos_proveedores, "prop");
					if ($cargo[0] == -1) {
						if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
							echo json_encode($cargo[1]);
							return;
						}
					} else {
						$adjunto = $cargo[1];
					}

					$res = $this->compras_model->guardar_proveedor_solicitud($nombre, $id_solicitud, $valor_total, $precio_dolar, $iva, $administracion, $imprevistos, $utilidad, $valor_dolar, $usuario_registra, $adjunto);
					echo json_encode($res);
					return;
				} else {
					echo json_encode(15);
					return;
				}
			}
		}
		return;
	}
	public function listar_proveedores_solicitud($comite = -1)
	{
		$proveedores = array();
		if ($this->Super_estado == false) {
			echo json_encode($proveedores);
			return;
		}
		$i = 1;
		$sw = false;
		$id_solicitud = $this->input->post("id");
		$usuario = null;
		$datos = $this->compras_model->listar_proveedores_solicitud($id_solicitud);
		if ($comite == 2) {
			$usuario = $_SESSION['persona'];
			//$aprobo = $this->compras_model->traer_proveedor_aprobados_persona($id_solicitud,$usuario);
			$aprobo_persona = $this->compras_model->traer_proveedor_aprobados_persona($id_solicitud, $usuario);
			$estado = $this->compras_model->traer_solicitud($id_solicitud)[0]["id_estado_solicitud"];
			$datos_negada = $this->compras_model->esta_negada_usuario($id_solicitud, $_SESSION["persona"]);
		} else if ($comite == -1) {
			$aprobo = $this->compras_model->traer_proveedor_aprobados_persona($id_solicitud, $usuario);
		}
		foreach ($datos as $row) {

			$row["gestion"] = "";
			$imp = 0;
			$adm = 0;
			$utl = 0;
			$imp_dolar  = 0;
			$adm_dolar  = 0;
			$utl_dolar  = 0;
			$iva = $row["valor_total"] * ($row["iva"] / 100);
			$iva_dolar = $row["valor_dolar"] * ($row["iva"] / 100);
			if (!is_null($row["utilidad"]) || !is_null($row["administracion"]) || !is_null($row["imprevistos"])) {
				$adm = $row["valor_total"] * ($row["administracion"] / 100);
				$imp = $row["valor_total"] * ($row["imprevistos"] / 100);
				$utl = $row["valor_total"] * ($row["utilidad"] / 100);
				$iva = $utl * ($row["iva"] / 100);

				$adm_dolar = $row["valor_dolar"] * ($row["administracion"] / 100);
				$imp_dolar = $row["valor_dolar"] * ($row["imprevistos"] / 100);
				$utl_dolar = $row["valor_dolar"] * ($row["utilidad"] / 100);
				$iva_dolar = $utl_dolar * ($row["iva"] / 100);
			}
			$x = $row["valor_total"] + $imp + $adm + $iva + $utl;
			$row["cal_iva"] = $this->convertir_moneda($iva, true, 0);
			$row["cal_imp"] = $this->convertir_moneda($imp, true, 0);
			$row["cal_adm"] = $this->convertir_moneda($adm, true, 0);
			$row["cal_utl"] = $this->convertir_moneda($utl, true, 0);
			$row["total_compra"] = $this->convertir_moneda($x, true, 0);

			$x_dolar = $row["valor_dolar"] + $imp_dolar + $adm_dolar + $iva_dolar + $utl_dolar;
			$row["cal_iva_dolar"] = $this->convertir_moneda($iva_dolar, true, 2);
			$row["cal_imp_dolar"] = $this->convertir_moneda($imp_dolar, true, 2);
			$row["cal_adm_dolar"] = $this->convertir_moneda($adm_dolar, true, 2);
			$row["cal_utl_dolar"] = $this->convertir_moneda($utl_dolar, true, 2);
			$row["total_compra_dolar"] = $this->convertir_moneda($x_dolar, true, 2);


			//$redondeo = (round($row["valor_total"]/100.0,0)*100);
			$row["valor_total_alt"] = $this->convertir_moneda($row["valor_total"], true, 0);
			$row["valor_dolar_alt"] = $this->convertir_moneda($row["valor_dolar"], true, 2);
			$row["precio_dolar"] = $this->convertir_moneda($row["precio_dolar"], true, 2);
			$row["indice"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span>ver</span></span>';
			$row["gestion"] = '<span  class="">Terminado</span>';
			if ($comite == -1) {



				if ($this->Super_elimina == 1) {
					if (empty($aprobo)) {
						$row["gestion"] = '<span title="Eliminar" style="color: #DE4D4D;"  data-toggle="popover" data-trigger="hover" class="fa fa-trash-o pointer btn btn-default" onclick="eliminar_proveedor_solicitud(' . $row["id"] . ')"></span>';
					}
					$sw = true;
				} else {
					$row["gestion"] = '';
				}
				if ($this->Super_modifica == 1) {
					if (empty($aprobo)) {
						$row["gestion"] = $row["gestion"] . ' ' . '<span style="color: #2E79E5;" title="Editar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench pointer btn btn-default" onclick="mostrar_datos_proveedor_modi(' . $row["id"] . ')"></span>';
					}
					$sw = true;
				}
			} else if ($comite == 2) {
				if (empty($datos_negada)) {
					if ($estado == "Soli_Com" || empty($aprobo_persona)) {
						$row["gestion"] = '<span  class="btn btn-default pointer red" onclick="aprobar_proveedor(' . $row["id"] . ')"> Aprobar</span>';
					}
				}
				$sw = true;
			}

			if (!$sw) {
				$row["gestion"] = '<span title="Sin Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
			}
			$i++;
			$proveedores["data"][] = $row;
		}
		echo json_encode($proveedores);
	}

	public function convertir_moneda($number, $format, $decimal = 2)
	{

		if (!$format) {
			$number = str_replace(".", "", $number);
			$number = str_replace(",", ".", $number);
			return $number;
		}
		return number_format($number, $decimal, ",", ".");
	}

	public function entregas_parciales($inicio = -1)
	{
		$usuario_registra = $_SESSION['persona'];
		$idsolicitud = $this->input->post('id');
		$articulos_solicitud = $this->compras_model->Listar_articulos_parciales($idsolicitud);
		$total_menores = 0;
		$con_parciales = array();
		foreach ($_POST as $name => $value) {
			$idarticulo = $name;
			$cantidad_entregada = $this->input->post($name);

			foreach ($articulos_solicitud as $row) {
				if ($row["id"] == $idarticulo) {
					if (empty($cantidad_entregada)) {

						return array(-8);
					}

					if (!is_numeric($cantidad_entregada)) {

						return array(-9);
					}
					if ($row["entregada"] + $cantidad_entregada > $row["cantidad"]) {
						return array(-10);
					}

					if ($row["entregada"] + $cantidad_entregada <= $row["cantidad"]) {
						array_push($con_parciales, array('cantidad' => $cantidad_entregada, 'id_articulo' => $idarticulo, 'usuario_registra' => $usuario_registra));
					}
					if ($row["entregada"] + $cantidad_entregada < $row["cantidad"]) {
						$total_menores++;
					}
				}
			}
		}
		return array(0, $con_parciales, $total_menores);
	}

	public function guardar_entregas_parciales()
	{
		$con_parciales = $this->entregas_parciales();

		if ($con_parciales[0] != 0) {
			echo json_encode($con_parciales[0]);
			return;
		}
		if (empty($con_parciales[1])) {
			echo json_encode(-12);
			return;
		}
		$res = $this->compras_model->entregas_parciales($con_parciales[1]);
		if ($con_parciales[2] == 0 && $res == 1) {
			echo json_encode(-12);
			return;
		}
		echo json_encode($res);
		return;
	}

	public function Modificar_solicitud_comite()
	{

		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(-1302);
			return;
		}
		$idsolicitud = $this->input->post("id");
		$id_comite = $this->input->post("comite");
		$observaciones = $this->input->post("observaciones");
		$descripcion = $this->input->post("descripcion");
		$aprobo = $this->compras_model->traer_proveedor_aprobados_persona($idsolicitud, null);
		if (empty($aprobo)) {
			if (ctype_space($id_comite) || empty($id_comite)) {
				echo json_encode(-1);
				return;
			}
			if (ctype_space($descripcion) || empty($descripcion)) {
				echo json_encode(-2);
				return;
			}

			$res = $this->compras_model->Modificar_solicitud_comite($idsolicitud, $id_comite, $descripcion, $observaciones);
			echo json_encode($res);
			return;
		}
		echo json_encode(15);
		return;
	}

	public function modificar_proveedor_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_modifica == 0) {
				echo json_encode(-1302);
			} else {
				$idproveedor = $this->input->post("id");
				$nombre = $this->input->post("nombre");
				$valor_total = $this->input->post("valor_total");
				$precio_dolar = $this->input->post("precio_dolar");
				$iva = $this->input->post("iva");
				$moneda = $this->input->post("moneda");
				$administracion = $this->input->post("administracion");
				$imprevistos = $this->input->post("imprevistos");
				$utilidad = $this->input->post("utilidad");
				$coceptos = $this->input->post("coceptos");
				$valor_dolar = null;

				$adjunto = null;
				$idsolicitud = $this->input->post("idsolicitud");
				$aprobo = $this->compras_model->traer_proveedor_aprobados_persona($idsolicitud, null);
				if (!empty($aprobo)) {
					echo json_encode(15);
					return;
				}
				if (ctype_space($nombre) || empty($nombre)) {
					echo json_encode(1);
					return;
				}
				if (ctype_space($valor_total) || empty($valor_total)) {
					echo json_encode(2);
					return;
				}

				$valor_total = $this->convertir_moneda($valor_total, false);
				if (!is_numeric($valor_total)) {
					echo json_encode(4);
					return;
				}
				if (!is_numeric($iva)) {
					echo json_encode(5);
					return;
				}

				if ($moneda == "usd") {
					if (ctype_space($precio_dolar) || empty($precio_dolar)) {
						echo json_encode(11);
						return;
					}
					$precio_dolar = $this->convertir_moneda($precio_dolar, false);
					if (!is_numeric($precio_dolar)) {
						echo json_encode(6);
						return;
					}
					$valor_dolar = $valor_total;
					$valor_total = $valor_total * $precio_dolar;

					if (!is_numeric($valor_dolar)) {
						echo json_encode(10);
						return;
					}

					if (!is_numeric($valor_total)) {
						echo json_encode(4);
						return;
					}
				} else {
					$precio_dolar = null;
					$valor_dolar = null;
				}



				if ($coceptos == 1) {
					if (ctype_space($administracion) || empty($administracion)) {
						$administracion = null;
					} else  if (!is_numeric($administracion)) {
						echo json_encode(7);
						return;
					}
					if (ctype_space($imprevistos) || empty($imprevistos)) {
						$imprevistos = null;
					} else  if (!is_numeric($imprevistos)) {
						echo json_encode(8);
						return;
					}
					if (ctype_space($utilidad) || empty($utilidad)) {
						$utilidad = null;
					} else if (!is_numeric($utilidad)) {
						echo json_encode(9);
						return;
					}
				} else {
					$administracion = null;
					$imprevistos = null;
					$utilidad = null;
				}


				$cargo = $this->cargar_archivo("adjunto", $this->ruta_archivos_proveedores, "prop");
				if ($cargo[0] == -1) {
					if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
						echo json_encode($cargo[1]);
						return;
					}
				} else {
					$adjunto = $cargo[1];
				}

				$data = array(
					'nombre' => $nombre,
					'valor_total' => $valor_total,
					'precio_dolar' => $precio_dolar,
					'iva' => $iva,
					'administracion' => $administracion,
					'imprevistos' => $imprevistos,
					'utilidad' => $utilidad,
					'valor_dolar' => $valor_dolar,
					'adjunto' => $adjunto,
				);

				$res = $this->compras_model->modificar_proveedor_solicitud($idproveedor, $data);
				echo json_encode($res);
				return;
			}
		}
		return;
	}

	function eliminar_proveedor_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode('sin_session');
			return;
		}
		if ($this->Super_elimina == 0) {
			echo json_encode(-1302);
			return;
		}

		$idsolicitud = $this->input->post("idsolicitud");
		$id = $this->input->post("id");
		$aprobo = $this->compras_model->traer_proveedor_aprobados_persona($idsolicitud, null);
		if (empty($aprobo)) {
			$data = array(
				'estado' => '0',
			);
			$datos = $this->compras_model->modificar_proveedor_solicitud($id, $data);
			echo $datos;
			return;
		}
		echo 15;
		return;
	}
	function aprobar_proveedor()
	{
		$this->load->model('personas_model');
		if ($this->Super_estado == false) {
			echo json_encode(array('sin_session'));
			return;
		}
		if ($this->Super_agrega == 0) {
			echo json_encode(array(-1302));
			return;
		}
		$id = $this->input->post("id");
		$usuario_registra = $_SESSION['persona'];
		//$id_comite = $this->input->post("id_comite_dire");
		$id_sol_comi = $this->input->post("id_sol_comi");
		$data_solicitud = $this->compras_model->traer_solicitud($id_sol_comi);
		$estado = $data_solicitud[0]["id_estado_solicitud"];
		$id_comite = $data_solicitud[0]['id_comite'];
		$con_aprobado = $this->compras_model->traer_proveedor_aprobados_persona($id_sol_comi, $usuario_registra);
		$data = array('id_proveedor' => $id, 'usuario_registra' => $usuario_registra);
		$where = "";
		$x = 0;
		foreach ($con_aprobado as $val) {
			if ($val["id_proveedor"] == $id) {
				echo json_encode(array(-1));
				return;
			}
			$where .= "id = " . $val["id"];
			if ($x < count($con_aprobado) - 1) $where .= " OR ";
			$x++;
		}

		$resp = $this->compras_model->aprobar_proveedor($data);
		$estados_modi = 0;
		if ($resp == 0) {


			if (!empty($con_aprobado)) {
				$estados_modi = $this->compras_model->modificar_estados_provedores_aprobados($where);
			}

			if ($estados_modi == 0) {
				$comites = $this->compras_model->Listar_solicitudes_en_comite($id_comite, -1, 1);
				$miembros_comite = $this->personas_model->Listar_personas_por_perfil("Per_Dir", 'comite');
				$limite = count($miembros_comite);


				if (empty($comites)) {
					echo json_encode(array(1, $resp, 1));
					return;
				}
				$terminar = true;
				$aux = true;
				foreach ($comites as $key) {
					if ($key["vb"] != ($limite - 1) && $aux == true) $aux = false;
					if ($key["vb"] < $limite && $terminar == true) $terminar = false;
				}

				if ($aux) {
					$fecha_cierre = date("Y-m-d");
					$data_fecha = ['fecha_cierre' => $fecha_cierre];
					$add = $this->compras_model->modificar_datos($data_fecha, "comites", $id_comite);
				}

				if ($terminar) {
					$resp = $this->compras_model->modificar_comite($id_comite, null, null, "Com_Ter");
					echo json_encode(array(1, $resp, 2));
				} else {
					echo json_encode(array(1, $resp, 1));
				}
			} else {
				echo json_encode(array($estados_modi));
			}
		} else {
			echo json_encode(array($resp));
		}
	}


	public function traer_proveedor_aprobados()
	{
		$aprobados = array();
		if ($this->Super_estado == false) {
			echo json_encode($aprobados);
			return;
		}
		$id = $this->input->post("id");
		$datos = $this->compras_model->traer_proveedor_aprobados($id);
		$i = 1;
		foreach ($datos as $row) {
			if ($row["id_perfil"] == "Per_Dir") {
				$row["tipo"] = '<span  style="background-color: #5cb85c;color: white; width: 100%;" class="pointer form-control" ><span> Aprobado</span></span>';
			} else {
				$row["tipo"] = '<span  style="background-color: #428bca;color: white; width: 100%;" class="pointer form-control" ><span> Sugerido</span></span>';
			}
			$row["codigo"] = $i;
			$aprobados["data"][] = $row;
			$i++;
		}
		echo json_encode($aprobados);
	}
	public function validar_aprobados_comite()
	{
		$id = $this->input->post("id");
		$datos = $this->compras_model->traer_proveedor_aprobados_persona($id, null, 2);
		$data_limite = $this->genericas_model->obtener_valores_parametro_aux("Num_Apro", 20);
		$data_restringe = $this->genericas_model->obtener_valores_parametro_aux("Rest_Comp", 20);
		$restringe = empty($data_restringe) ? "si" : $data_restringe[0]["valor"];
		$limite = empty($data_limite) ? 3 :  $data_limite[0]["valor"];

		if (count($datos) >= $limite) {
			echo json_encode(1);
			return;
		}
		echo json_encode($restringe);
		return;
	}
	public function guardar_comentario()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_agrega == 0) {
				echo json_encode(-1302);
			} else {
				$id_solicitud = $this->input->post("id");
				$comentario = $this->input->post("comentario");
				$id_pregunta = $this->input->post("id_pregunta");
				if ($id_pregunta == -1) {
					$id_pregunta = null;
				}
				$usuario = $_SESSION["persona"];
				if (empty($comentario) || ctype_space($comentario)) {
					echo json_encode(-2);
					return;
				}
				if (empty($id_solicitud) || ctype_space($id_solicitud) || $id_solicitud == 0) {
					echo json_encode(-5);
					return;
				}
				$resultado = $this->compras_model->guardar_comentario($comentario, $usuario, $id_solicitud, $id_pregunta);
				echo json_encode($resultado);
				return;
			}
		}
	}

	function listar_comentario()
	{
		$comentarios = array();

		if ($this->Super_estado == false) {
			echo json_encode($comentarios);
			return;
		}
		$id = $this->input->post('id');
		$id_coment = $this->input->post('id_coment');
		$datos = $this->compras_model->listar_comentario($id, $id_coment);
		$i = 1;
		foreach ($datos as $row) {
			$row["indice"] = '<span  style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';
			$comt = $row["id_pregunta"] == null ? $row["id"] : $row["id_pregunta"];
			$row["codigo"] = '<span style="color: #5cb85c;"title="Responder Comentario" data-toggle="popover" data-trigger="hover" class="fa fa-commenting pointer btn btn-default" onclick="responder_preguntas(' . $comt . ',' . $row["id_compra"] . ')" ></span>';
			$comentarios["data"][] = $row;
			$i++;
		}

		echo json_encode($comentarios);
	}
	function listar_comentario_tipo2()
	{
		$comentarios = array();

		if ($this->Super_estado == false) {
			echo json_encode($comentarios);
			return;
		}
		$id = $this->input->post('id');
		$id_coment = $this->input->post('id_coment');
		$datos = $this->compras_model->listar_comentario($id, $id_coment);
		echo json_encode($datos);
	}
	public function negar_compra()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			if ($this->Super_agrega == 0) {
				echo json_encode(-1302);
			} else {
				$id_solicitud = $this->input->post("id");
				//$comentario = $this->input->post("comentario");
				$usuario = $_SESSION["persona"];
				/* if (empty($comentario) || ctype_space($comentario)) {
                    echo json_encode(-2);
                    return;
                }*/
				if (empty($id_solicitud) || ctype_space($id_solicitud) || $id_solicitud == 0) {
					echo json_encode(-5);
					return;
				}
				$estado = $this->compras_model->traer_solicitud($id_solicitud)[0]["id_estado_solicitud"];
				if ($estado != "Soli_Com") {
					echo json_encode(-7);
					return;
				}
				$datos_negada = $this->compras_model->esta_negada_usuario($id_solicitud, $usuario);
				if (!empty($datos_negada)) {
					echo json_encode(-6);
					return;
				}
				$add_negar = array();
				array_push($add_negar,  array(
					"usuario_registro" => $usuario,
					"id_compra" => $id_solicitud,
				));
				$resultado = $this->compras_model->guardar_general($add_negar, "solicitud_negadas_comite");
				/*if ($resultado == 1) {
                    $datos_negada = $this->compras_model->esta_negada_usuario($id_solicitud,$usuario);
                    $add_coment = array();
                    array_push($add_coment,  array(
                        "usuario_registra" => $usuario,
                        "comentario" => $comentario,
                        "id_compra" => $id_solicitud,
                        "estado" => 1,
                        "id_negada" =>$datos_negada->{'id'},
                    ));
    
                    $resultado = $this->compras_model->guardar_general($add_coment,"comentarios_compras");
                }*/

				echo json_encode($resultado);
				return;
			}
		}
	}

	public function esta_negada_usuario()
	{
		if ($this->Super_estado == false) {
			echo json_encode(array("sin_session"));
			return;
		} else {
			$id_solicitud = $this->input->post("id");
			$usuario = $_SESSION["persona"];
			if (empty($id_solicitud) || ctype_space($id_solicitud) || $id_solicitud == 0) {
				echo json_encode(array(-1));
				return;
			}
			$datos_negada = $this->compras_model->esta_negada_usuario($id_solicitud, $usuario);
			$datos_general = $this->compras_model->esta_negada_usuario($id_solicitud, null);
			echo json_encode(array(1, $datos_negada, $datos_general));
			return;
		}
	}


	public function Listar_personas_por_perfil()
	{
		if ($this->Super_estado == false) {
			echo json_encode('sin_session');
			return;
		}
		$this->load->model('personas_model');
		$perfil = $this->input->post("perfil");
		$datos = $this->personas_model->Listar_personas_por_perfil(null);
		echo json_encode($datos);
	}

	public function solicitudes_por_encuestar_persona()
	{
		if ($this->Super_estado == false) {
			echo json_encode('sin_session');
			return;
		}
		$persona = $_SESSION["persona"];
		$pendientes = $this->compras_model->solicitudes_por_encuestar_persona($persona);
		$limite = $this->genericas_model->obtener_valores_parametro_aux("Lim_Enc", 20);
		if (empty($limite)) {
			$limite = 3;
		} else {
			$limite = $limite[0]["valor"];
		}
		if ($pendientes >= $limite) {
			echo json_encode(-1);
			return;
		}
		echo json_encode(1);
		return;
	}
	public function mostrar_notificaciones_comentario()
	{
		if ($this->Super_estado == false) {
			echo json_encode('sin_session');
			return;
		}
		$data = array();
		$tipo = $this->input->post("tipo");
		$notificacion = $this->compras_model->mostrar_notificaciones_comentario($tipo);
		foreach ($notificacion as $noti) {
			if ($noti["idfin"] != $_SESSION['persona']) {
				array_push($data, $noti);
			}
		}

		echo json_encode($data);
	}
	public function terminar_comentario()
	{
		if ($this->Super_estado == false) {
			echo json_encode('sin_session');
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(-1302);
			return;
		}

		$fecha = date("Y-m-d H:i");
		$usuario = $_SESSION['persona'];
		$id = $this->input->post('id');
		$resp = $this->compras_model->terminar_comentario($id, $usuario, $fecha);
		echo json_encode($resp);
	}
	public function traer_correos_comite_compras_tipo2()
	{
		if ($this->Super_estado == false) {
			echo json_encode('sin_session');
			return;
		}
		$id = $this->input->post('id');
		$tipo = $this->compras_model->traer_solicitud($id)[0]["id_tipo_compra"];
		$resp = $this->compras_model->traer_correos_comite_compras_tipo2($tipo);
		echo json_encode($resp);
	}

	function listar_personas_compra_negados()
	{
		$negados = array();

		if ($this->Super_estado == false) {
			echo json_encode($negados);
			return;
		}
		$id = $this->input->post('id');
		$datos = $this->compras_model->listar_personas_compra_negados($id);
		$i = 1;
		foreach ($datos as $row) {
			$negados["data"][] = $row;
			$i++;
		}

		echo json_encode($negados);
	}
	public function es_habil($c_day)
	{
		$festivos = new festivos_colombia;
		$festivos->festivos(date("Y", strtotime($c_day)));
		$c_weekDay = (int) $this->getWeekDay($c_day);
		if ($c_weekDay == 0 || $c_weekDay == 6 || $festivos->esFestivo($c_day)) {
			return false;
		}
		return true;
	}
	public function getWeekDay($date)
	{
		return date("w", strtotime($date));
	}

	public function calcular_tiempo_proveedor($idsolicitud)
	{
		$res_p = 0;
		$dias_est = $this->compras_model->traer_solicitud($idsolicitud)[0]["fecha_entrega_est"];
		$historial_estados = $this->compras_model->Listar_historial_estado($idsolicitud);
		$fecha_inicial = null;
		$fecha_final = null;
		for ($i = 0; $i < count($historial_estados); $i++) {
			$row = $historial_estados[$i];
			if ($row["id_estado"] == "Soli_Pen") {
				$fecha_inicial = $row["fecha_cambio"];

				$strInicio = date("Y-m-d", strtotime($fecha_inicial));
				$c_day = date("Y-m-d", strtotime($fecha_inicial));
				$aux = true;
				$dias = 0;
				while ($dias < $dias_est) {
					$c_day = date("Y-m-d", strtotime("$c_day + 1 days"));
					if ($this->es_habil($c_day)) {
						$dias += 1;
					}
				}
				return $c_day;
			}
		}
		return null;
	}
	public function ejecutar($id)
	{
		if ($_SESSION["perfil"] == "Per_Admin") {
			$datos_gestion_final = $this->calcular_tiempo_solicitud($id);
			$res_1 = $this->compras_model->guardar_tiempo_gestion($id, $datos_gestion_final[0], $datos_gestion_final[1]);
			$fecha_proveedor = $this->calcular_tiempo_proveedor($id);
			$res_2 = $this->compras_model->modificar_datos(array("dias_no_habiles" => $fecha_proveedor), "solicitud_compra", $id);
		}
	}

	function listar_respuestas_comentario()
	{
		$comentarios = array();

		if ($this->Super_estado == false) {
			echo json_encode($comentarios);
			return;
		}
		$id = $this->input->post('id');
		$id_coment = $this->input->post('id_coment');
		$comentarios = $this->compras_model->listar_comentario($id, $id_coment);
		echo json_encode($comentarios);
	}

	public function retirar_solicitud_comite()
	{
		if ($this->Super_estado == false) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			if ($this->Super_modifica == 0) {
				$resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
			} else {
				$id = $this->input->post('id');
				$usuario_registra = $_SESSION['persona'];
				$resp = ['mensaje' => "Error al cargar el la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
				if (!empty($id)) {
					$solicitud = $this->compras_model->traer_solicitud($id);
					if (!empty($solicitud)) {
						$comite = $this->compras_model->traer_comite($solicitud[0]['id_comite']);
						if (!empty($comite)) {
							$estado = 'Soli_Rec';
							if (!empty($estado)) {
								if ($comite[0]['id_estado_comite'] != 'Com_Ter') {
									$data = ['id_estado_solicitud' => $estado, 'id_comite' => null, 'descripcion_cmt' => null, 'observaciones_cmt' => null, 'fecha_asigna_com' => null, 'usuario_asigna_com' => null];
									$set = $this->compras_model->setear_aprobados($id);
									$add = $this->compras_model->modificar_datos($data, "solicitud_compra", $id);
									$resp = ['mensaje' => "", 'tipo' => "success", 'titulo' => "Solicitud Retirada.!"];
									if ($add != 1 || $set != 1) $resp = ['mensaje' => "Error al retirar la solicitud, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
									else $this->compras_model->guardar_cambio_estado($id, "Soli_Com_Rev");
								} else {
									$resp = ['mensaje' => "No es posible continuar, el comité fue gestionado anteriormente.", 'tipo' => "info", 'titulo' => "Oops.!"];
								}
							}
						}
					}
				}
			}
		}
		echo json_encode($resp);
	}

	public function notificaciones_solicitudes()
	{
		$filtro = $this->input->post('filtro');
		$having = $this->input->post('having');
		$resp = $this->Super_estado ? $this->compras_model->notificaciones_solicitudes($filtro, $having) : array();
		echo json_encode($resp);
	}

	public function notificaciones_servicio_recibido()
	{
		$resp = $this->Super_estado ? $this->compras_model->notificaciones_servicio_recibido() : array();
		echo json_encode($resp);
	}

	public function modificar_fecha_entrega_est()
	{
		if ($this->Super_estado == false) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			if ($this->Super_modifica == 0) {
				$resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
			} else {
				$id_solicitud = $this->input->post('id_solicitud');
				$dia = $this->input->post('dia');
				$solicitud = $this->compras_model->traer_solicitud($id_solicitud);
				$resp = ['mensaje' => "Error al cargar  la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
				if (!empty($solicitud)) {
					$estado = $solicitud[0]['id_estado_solicitud'];
					$resp = ['mensaje' => "La solicitud ya fue gestionada, por tal motivo no es posible realizar esta acción.", 'tipo' => "info", 'titulo' => "Oops.!"];
					if ($estado != 'Soli_Fin' && $estado != 'Soli_Pen' && $estado != 'Ser_Rec' && $estado != 'Soli_Dev' && $estado != 'Soli_Par') {
						$data = ['fecha_entrega_est' => $dia];
						$add = $this->compras_model->modificar_datos($data, "solicitud_compra", $id_solicitud);
						$resp = ['mensaje' => "", 'tipo' => "success", 'titulo' => "Días Modificados.!"];
						if ($add != 1) $resp = ['mensaje' => "Error al modificar lso días de entrega de la solicitud, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
					}
				}
			}
		}
		echo json_encode($resp);
	}

	public function obtener_intevalo_fecha_entrega()
	{
		$data_dia = $this->genericas_model->obtener_valores_parametro_aux("Not_Dia", 20);
		$dia = empty($data_dia) ? 1 : $data_dia[0]["valor"];
		echo json_encode($dia);
	}

	public function obtener_solicitudes_comite_acta()
	{
		$id_comite = $this->input->post("id_comite");
		$datos = $this->Super_estado ? $this->compras_model->obtener_solicitudes_comite_acta($id_comite) : [];
		echo json_encode($datos);
	}

	public function obtener_correos_comite()
	{
		$this->load->model('personas_model');
		$datos = $this->Super_estado ? $this->personas_model->Listar_personas_por_perfil('Per_Dir', 'comite') : [];
		echo json_encode($datos);
	}

	public function validacion_cargo()
	{
		$datos = $this->Super_estado ? $this->compras_model->obtener_cargo($_SESSION['persona']) : [];
		echo json_encode($datos);
	}

	public function listar_aprobados_proveedor()
	{
		$id_solicitud = $this->input->post("id_solicitud");
		$total_compra = 0;
		$proveedor = '';
		$datos = $this->Super_estado ? $this->compras_model->listar_aprobados_proveedor($id_solicitud) : [];
		foreach ($datos as $row) {
			$imp = 0;
			$adm = 0;
			$utl = 0;
			$iva = $row["valor_total"] * ($row["iva"] / 100);
			if (!is_null($row["utilidad"]) || !is_null($row["administracion"]) || !is_null($row["imprevistos"])) {
				$adm = $row["valor_total"] * ($row["administracion"] / 100);
				$imp = $row["valor_total"] * ($row["imprevistos"] / 100);
				$utl = $row["valor_total"] * ($row["utilidad"] / 100);
				$iva = $utl * ($row["iva"] / 100);
			}
			$total = $row["valor_total"] + $imp + $adm + $iva + $utl;
			$proveedor = $row["proveedor"];
			$total_compra = $this->convertir_moneda($total, true, 0);
			break;
		}
		echo json_encode(['aprobados' => $datos, 'total_compra' =>  $total_compra, 'proveedor' => $proveedor]);
	}

	public function cambiar_proveedor()
	{
		if (!$this->Super_estado == true) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			if ($this->Super_agrega == 0) {
				$resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
			} else {
				$id_solicitud = $this->input->post('id_solicitud');
				$id_nuevo_prov = $this->input->post('id_nuevo_prov');
				$solicitud = $this->compras_model->traer_solicitud($id_solicitud);

				if (!empty($solicitud)) {
					$estado = $solicitud[0]['id_estado_solicitud'];
					if ($estado != 'Soli_Fin' && $estado != 'Soli_Pen' && $estado != 'Ser_Rec' && $estado != 'Soli_Dev' && $estado != 'Soli_Par') {
						$data = ['id_proveedor' => $id_nuevo_prov];
						$add = $this->compras_model->modificar_datos($data, "solicitud_compra", $id_solicitud);
						if ($add != 1) $resp = ['mensaje' => "Error cambiando el proveedor de la solicitud, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
						$resp = ['mensaje' => "", 'tipo' => "success", 'titulo' => "Proveedor Cambiado con exito.!"];
					} else {
						$resp = ['mensaje' => "La solicitud ya fue gestionada, por tal motivo no es posible realizar esta acción.", 'tipo' => "info", 'titulo' => "Oops.!"];
					}
				} else {
					$resp = ['mensaje' => "Error al cargar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
				}
			}
		}
		echo json_encode($resp);
	}

	public function listar_personas_compras()
	{
		$personas = array();

		if ($this->Super_estado == false) {
			echo json_encode($personas);
			return;
		}
		$persona = $this->input->post("persona_buscada");
		if (empty($persona)) {
			exit(json_encode([]));
		} else {
			$data = $this->compras_model->listar_personas_compras($persona);
			$btn_permisos = '<span class="fa fa-check red btn btn-default adm_permisos_com" style="color:#5CB85C;" title="Permisos" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
			$btn_encuestas_rp = '<span class="fa fa-check-square-o red btn btn-default adm_encuestas_rp" style="color:#6E1F7C;" title="Encuestas RP" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
			$btn_solicitudes = '<span class="fa fa-pencil-square-o red btn btn-default adm_solicitudes_com" style="color:#DB941C;" title="Solicitudes" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
			$btn_cronogramas = '<span class="fa fa-check-circle red btn btn-default adm_cronogramas_com" style="color:#088af1;" title="Permisos cronogramas" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
			foreach ($data as $row) {
				$row['accion'] = "$btn_encuestas_rp $btn_solicitudes $btn_permisos $btn_cronogramas";
				array_push($personas, $row);
			}
			echo json_encode($personas);
		}
	}

	/* Listar personas RP */
	public function listar_personas_general()
	{
		$personas = array();

		if ($this->Super_estado == false) {
			echo json_encode($personas);
			return;
		}
		$persona = $this->input->post("persona_buscada");
		$tipo_enc = $this->input->post("tipo_enc");
		if (empty($persona)) {
			exit(json_encode($personas));
		} else {
			$data = $this->compras_model->listar_personas_compras($persona);
			if (count($data) > 0) {
				$btn_accion = "";
				$btn_asignar = '<span class="fa fa-check btn btn-default asignar_enc" title="Asignar Encuesta" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
				$btn_retirar = '<span class="fa fa-times btn btn-default retirar_enc" title="Retirar Encuesta" data-toggle="popover" data-trigger="hover" style="color:#BB3747;"></span>';

				for ($x = 0; $x < count($data); $x++) {
					$check_encss = $this->check_encuesta_asignada($data[$x]['id'], $tipo_enc);
					if ($check_encss == true) {
						$btn_accion = $btn_retirar;
					} else if ($check_encss == false) {
						$btn_accion = $btn_asignar;
					}
					foreach ($data as $row) {
						if ($row["id"] == $data[$x]['id']) {
							$row['accion'] = $btn_accion;
							array_push($personas, $row);
						}
					}
				}
			}
			echo json_encode($personas);
		}
	}

	/* Check permiso asignado */
	public function check_encuesta_asignada($id_persona, $tipo_enc)
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
		} else {
			$check_asig = $this->compras_model->permisos_compra_info($id_persona);
			if ($check_asig) {
				for ($x = 0; $x < count($check_asig); $x++) {
					if ($check_asig[$x]["id_tipo_encuesta"] == $tipo_enc) {
						return true;
						break;
					}
				}
			} else {
				return false;
			}
		}
	}

	/* Guardar preguntas de encuestas */
	public function guardar_pregunta_encuesta()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
		} else {
			$preguntas = $this->input->post("pregunta");
			$areas_selected = $this->input->post("preg_catego");
			for ($x = 0; $x < count($preguntas); $x++) {
				if ($areas_selected[$x] == "" or empty($areas_selected[$x])) {
					$r = ['mensaje' => "Debe seleccionar la categoría para la pregunta #" . ($x + 1) . ".", 'tipo' => 'error', 'titulo' => "Oops"];
				} else {
					$area = $this->compras_model->find_idParametro('no_aplica');
					$arrayDatos = [
						"idparametro" => $area->idpa,
						"valor" => $preguntas[$x],
						"valorx" => $areas_selected
					];
					$query = $this->compras_model->guardar_info("valor_parametro", $arrayDatos);
					if (!empty($query)) {
						$r = ['mensaje' => "La consulta no se pudo realizar. Error " . __LINE__, 'tipo' => 'error', 'titulo' => "Oops"];
					} else {
						$r = ['mensaje' => "La operación se ha realizado exitosamente.", 'tipo' => 'success', 'titulo' => "Exitoso!"];
					}
				}
			}
		}
		exit(json_encode($r));
	}

	public function listar_tipos_permisos()
	{
		$resp = array();

		if ($this->Super_estado == false) {
			echo json_encode($resp);
			return;
		}

		$id = $this->input->post('id_persona');
		$btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar_per_com" style="color:#2E79E5"></span>';
		$btn_desasignar = '<span title="Desasignar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default retirar_per_com" style="color:#d9534f"></span>';
		$tipos = $this->compras_model->listar_tipos_permisos($id);

		//Permisos
		if (!$tipos) {
			$resp = [
				['nombre' => 'Permiso Solicitudes', 'accion' => $btn_asignar, 'persona' => $id, 'tipo' => 'solicitudes'],
				['nombre' => 'Permiso Comite', 'accion' => $btn_asignar, 'persona' => $id, 'tipo' => 'comite'],
				['nombre' => 'Permiso Proveedor', 'accion' => $btn_asignar, 'persona' => $id, 'tipo' => 'proveedores']
			];
		} else {
			$resp = [
				['nombre' => 'Permiso Solicitudes', 'accion' => ($tipos->solicitudes == 1) ? $btn_desasignar : $btn_asignar, 'persona' => $tipos->id_persona, 'tipo' => 'solicitudes'],
				['nombre' => 'Permiso Comite', 'accion' => ($tipos->comite == 1) ? $btn_desasignar : $btn_asignar, 'persona' => $tipos->id_persona, 'tipo' => 'comite'],
				['nombre' => 'Permiso Proveedor', 'accion' => ($tipos->proveedores == 1) ? $btn_desasignar : $btn_asignar, 'persona' => $tipos->id_persona, 'tipo' => 'proveedores']
			];
		}
		echo json_encode($resp);
	}

	public function asignar_permiso_com()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
		} else {
			$id_persona = $this->input->post('id_persona');
			$tipo_per = $this->input->post('tipo_per');
			$registrado = $this->compras_model->listar_tipos_permisos($id_persona);
			if ($registrado) {
				if ($registrado->$tipo_per == 1) {
					$resp = ['mensaje' => "La persona ya cuenta con el permiso asignado.", 'tipo' => "info", 'titulo' => "Oops.!"];
				} else {
					$id = $registrado->id;
					$data = [$tipo_per => 1];
					$add = $this->compras_model->modificar_datos($data, "permisos_compra", $id);
					$resp = ['mensaje' => "Permiso asignado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];

					if ($add == -1) {
						$resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
					}
				}
			} else {
				$data = [$tipo_per => 1, 'id_persona' => $id_persona];
				$add = $this->compras_model->agregar_permiso($data, "permisos_compra");
				$resp = ['mensaje' => "Permiso asignado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
				if ($add == -1) {
					$resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
				}
			}
		}
		echo json_encode($resp);
	}

	/* Asignar encuesta rp */
	public function asignar_encuesta_rp($id_personaa = "", $tipo_enc_selectedd = "", $return_sw = false)
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
		} else {
			$resp = [];

			empty($id_personaa) ? $id_persona = $this->input->post("id_persona") : $id_persona = $id_personaa;
			empty($tipo_enc_selectedd) ? $tipo_enc_selected = $this->input->post("tipo_enc_selected") : $tipo_enc_selected = $tipo_enc_selectedd;

			if (empty($id_persona) or empty($tipo_enc_selected)) {
				$resp = ['mensaje' => "Error intero numero " . __LINE__ . ". Contacte son el administrador del sitio.", 'tipo' => 'error', 'titulo' => ""];
			} else {
				$check_pa = $this->compras_model->permisos_compra_info($id_persona);
				for ($x = 0; $x < count($check_pa); $x++) {
					if ($check_pa[$x]["id_persona"] == $id_persona && $check_pa[$x]["id_tipo_encuesta"] == $tipo_enc_selected) {
						$resp = ['mensaje' => "Esta encuesta ya ha sido asignada anteriormente a esta persona.", 'tipo' => 'error', 'titulo' => "Oops"];
						if ($return_sw) {
							return $resp;
						} else {
							exit(json_encode($resp));
						}
					}
				}
				$dataToSend = ["id_persona" => $id_persona, "id_tipo_encuesta" => $tipo_enc_selected];
				$asig = $this->compras_model->guardar_info("compras_permisos_encuestas", $dataToSend);
				if (empty($asig)) {
					$resp = ['mensaje' => "Encuesta asiganada correctamente.", 'tipo' => 'success', 'titulo' => "Bien!"];
				} else {
					$resp = $asig;
				}
			}
		}

		/* Esta variable "$return_sw", me servirá para determinar si este método ha sido usado en el mismo controlador o no
		y así poder enviar la respuesta que necesito para no interrumpir el flujo de ejecución de la función donde la llame. */

		if ($return_sw) {
			return $resp;
		} else {
			exit(json_encode($resp));
		}
	}

	/* Retirar encuestas RP */
	public function retirar_encuesta_rp()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
		} else {
			$resp = [];
			$id_persona = $this->input->post("id_persona");
			$tipo_enc_selected = $this->input->post("tipo_enc_selected");
			$check_pa = $this->compras_model->permisos_compra_info($id_persona);
			if (count($check_pa) > 0) {
				for ($x = 0; $x < count($check_pa); $x++) {
					if ($check_pa[$x]["id_persona"] == $id_persona and $check_pa[$x]["id_tipo_encuesta"] == $tipo_enc_selected) {
						$query = $this->compras_model->eliminar_permiso($check_pa[$x]["id"], "compras_permisos_encuestas");
						if ($query) {
							$resp = ['mensaje' => "Encuesta retirada exitosamente.", 'tipo' => 'success', 'titulo' => "Bien!"];
						} else {
							$resp = ['mensaje' => "Error interno codigo: " . __LINE__ . ".", 'tipo' => 'error', 'titulo' => ""];
						}
						exit(json_encode($resp));
					}
				}
			}
		}
		echo (json_encode($resp));
		//return;
	}

	public function retirar_permiso_com()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
		} else {
			$id_persona = $this->input->post('id_persona');
			$tipo_per = $this->input->post('tipo_per');
			$registrado = $this->compras_model->listar_tipos_permisos($id_persona);
			if ($registrado) {
				if ($registrado->$tipo_per == 0) {
					$resp = ['mensaje' => "La persona no cuenta con el permiso asignado.", 'tipo' => "info", 'titulo' => "Oops.!"];
				} else {
					$id = $registrado->id;
					$data = [$tipo_per => 0];
					$add = $this->compras_model->modificar_datos($data, "permisos_compra", $id);
					$resp = ['mensaje' => "Permiso retirado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];

					if ($add == -1) {
						$resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
					}
					$registrado = $this->compras_model->listar_tipos_permisos($id_persona);
					if (!$registrado->solicitudes && !$registrado->comite && !$registrado->proveedores) {
						$del = $this->compras_model->eliminar_permiso($id, "permisos_compra");
						if ($del == -1) {
							$resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
						}
					}
				}
			} else {
				$resp = ['mensaje' => "La persona no cuenta con el permiso asignado.", 'tipo' => "info", 'titulo' => "Oops.!"];
			}
		}
		echo json_encode($resp);
	}

	public function obtener_correos_permiso()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
		} else {
			$tipo_solicitud = $this->input->post('tipo_solicitud');
			$estado_nuevo = $this->input->post('estado_nuevo');
			$resp = $this->compras_model->obtener_correos_permiso($tipo_solicitud, $estado_nuevo);
		}
		echo json_encode($resp);
	}

	public function recordatorio_directivos()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => 'sin_session', 'titulo' => ""];
		} else {
			$this->load->model('personas_model');
			$id = $this->input->post('id');
			$info_soli = $this->compras_model->info_solicitud_recordatorio($id);
			$pcomite = $this->compras_model->personas_comite();
			$paprobados = $this->compras_model->personas_aprobado($id);
			$pnegados = $this->compras_model->personas_negado($id);
			$gestionados = array_merge($paprobados, $pnegados);
			$resp = [];

			foreach ($pcomite as $com) {
				$sw = false;
				foreach ($gestionados as $ges) {
					if ($com['id'] == $ges['id']) {
						$sw = true;
						break;
					}
				}
				if ($sw == false) {
					array_push($resp, $com);
				}
			}
		}
		echo json_encode(array($resp, $info_soli));
	}

	public function buscar_jefe()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$persona = $this->input->post('persona');
			$resp = $persona ? $this->compras_model->buscar_jefe($persona) : [];
		}
		echo json_encode($resp);
	}

	/* Listar proveedores filtrados */
	public function listar_proveedores_filtrados()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$fd = $this->input->post("fecha_desde");
			$fh = $this->input->post("fecha_hasta");
			if (empty($fd) or empty($fh)) {
				$resp = ['mensaje' => "Debe diligenciar ambas fechas para obtener un resultado.", 'tipo' => "warning", 'titulo' => "Oops"];
			} else {
				$query = $this->compras_model->listar_proveedores_filtrados($fd, $fh);
				if ($query) {
					$resp = $query;
				} else {
					$resp = ['mensaje' => "No se han encontrado resultados.", 'tipo' => "error", 'titulo' => ""];
				}
			}
		}
		exit(json_encode($resp));
	}

	/* Listar criterios RP */
	public function listar_criterios_rp()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$dato = $this->compras_model->find_idParametro('2_criterios');
			$query = $this->compras_model->listar_criterios_rp($dato->idpa);
			if ($query) {
				$datos = [];
				$btn_ver = '<span style="background-color: white; color: black; width: 100%;" class="pointer btn btn-default see_details"><span>Ver</span></span>';
				$btn_asignar_perm = '<span title="Asignar Criterios" data-toggle="popover" data-trigger="hover" style="color: #39b23b;" class="pointer fa fa-gears btn btn-default asig_cri"></span>';
				$btn_modificar_cri = '<span title="Modificar Criterios" data-toggle="popover" data-trigger="hover" style="color: #337ab7;" class="pointer fa fa-wrench btn btn-default upd_cri"></span>';
				$btn_eliminar_cri = '<span title="Eliminar criterios" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;" class="pointer fa fa-trash btn btn-default del_cri"></span>';

				foreach ($query as $row) {
					$row["ver"] = "$btn_ver";
					$row["accion"] = "$btn_asignar_perm $btn_modificar_cri $btn_eliminar_cri";

					if ($row["porcentaje"] == NULL or $row["porcentaje"] == "") {
						$row["porcentaje"] = 0 . "%";
					} else {
						$row["porcentaje"] = $row["porcentaje"] . "%";
					}

					array_push($datos, $row);
				}
				$resp = $datos;
			} else {
				$resp = ['mensaje' => "No se encontraron resultados.", 'tipo' => "error", 'titulo' => "Oops"];
			}
		}
		exit(json_encode($resp));
	}

	/* Listar los tipos de encuesta RP para los criterios de evaluacion */
	public function listar_tipos_encuestas_RP()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$criterio = $this->input->post("criterio_id");
			$crit_idaux = $this->input->post("criterio_selected");
			$dato = $this->compras_model->find_idParametro('tipos_pregRP');
			$query = $this->compras_model->listar_tipos_preguntasRP('', $dato->idpa);
			$crit_asigned = $this->compras_model->criterios_asigned($criterio);

			if ($query) {
				$datos = [];
				$btn_enable = '<div class="btn-group btn-group-toggle" data-toggle="buttons"><label class="btn btn-primary active enable">Habilitar</label></div>';
				$btn_disable = '<div class="btn-group btn-group-toggle" data-toggle="buttons"><label class="btn btn-primary active disable">Deshabilitar</label></div>';
				$suma = 0;

				foreach ($query as $row) {
					$row['accion'] = "$btn_enable";

					for ($x = 0; $x < count($crit_asigned); $x++) {
						if ($row["id"] == $crit_asigned[$x]["id_encuesta"]) {
							$row += ["porcent" => $crit_asigned[$x]["porcentaje"] . "%"];
							$row['accion'] = "$btn_disable";
							$suma += $crit_asigned[$x]["porcentaje"];
						}
					}

					if ($crit_idaux == "3_criterios") {
						$row["idaux"] == "Tip_Mat" ? $row["area"] = "Calidad de los materiales o servicios." : false;
						$row["idaux"] == "sst_enc" ? $row["area"] = "Encuesta del área de seguridad (SST)	o Encuesta de Gestión Ambiental (SGA)" : false;
						$row["idaux"] != "sga_enc" && $row["idaux"] != "Tip_Ser" ? array_push($datos, $row) : false;
					} else if ($crit_idaux == "2_criterios") {
						$row["idaux"] == "Tip_Mat" ? $row["area"] = "Calidad de materiales o servicios" : false;
						$row["idaux"] != "sga_enc" && $row["idaux"] != "sst_enc" && $row["idaux"] != "Tip_Ser" ? array_push($datos, $row) : false;
					} else {
						$row["idaux"] == "Tip_Mat" ? $row["area"] = "Calidad de materiales o servicios" : false;
						$row["idaux"] != "Tip_Ser" ? array_push($datos, $row) : false;
					}
				}

				for ($x = 0; $x < count($datos); $x++) {
					if (!isset($datos[$x]["porcent"])) {
						$datos[$x] += ["porcent" => 0 . "%"];
					}
				}

				/* Armamos el array con el total incluido en cualquier posicion */
				$resul = [];
				foreach ($datos as $key) {
					$key["total_porcent"] = $suma;
					array_push($resul, $key);
				}

				$resp = $resul;
			} else {
				$resp = ['mensaje' => "No se encontraron resultados.", 'tipo' => "error", 'titulo' => "Oops"];
			}
		}
		exit(json_encode($resp));
	}

	/* Enable permissions - Para habilitar y settear los porcentajes requeridos */
	public function enable_permission()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$data = [];
			$idEnc = $this->input->post("id_cri");
			$porcent = $this->input->post("porcentaje");
			$encuesta = $this->input->post("id_encuesta");
			$id_usuario_registra = $_SESSION['persona'];
			$check = $this->compras_model->criterios_asigned($idEnc);
			$limite = 100;

			if (empty($idEnc) or empty($porcent)) {
				$resp = ["mensaje" => "Este campo no puede quedar vacío.", "tipo" => "error", "titulo" => ""];
			} else {
				if ($porcent > 100) {
					$resp = ["mensaje" => "No puede ingresar un valor mayor a 100%.", "tipo" => "error", "titulo" => ""];
				} else if (!is_numeric($porcent)) {
					$resp = ["mensaje" => "¡Debe ingresar un valor numerico valido!.", "tipo" => "error", "titulo" => ""];
				} else if ($porcent <= 0) {
					$resp = ["mensaje" => "Debe ingresar un valor mayor a 0% y menor de 100%.", "tipo" => "error", "titulo" => ""];
				} else {
					if (count($check) > 0) {
						$suma = 0;
						foreach ($check as $row) {
							if ($row["criterio_asignado"] == $idEnc && $row["id_encuesta"] == $encuesta) {
								$resp = ['mensaje' => "Este criterio ha sido gestionado anteriormente.", 'tipo' => "info", 'titulo' => "Oops!"];
								exit(json_encode(['mensaje' => "Este criterio ha sido gestionado anteriormente.", 'tipo' => "info", 'titulo' => "Oops!"]));
							} else {
								$suma += $row["porcentaje"];
							}
						}
						//exit(json_encode($suma+$porcent));
						$total = $suma + $porcent;
						if ($total > $limite || $suma > $limite) {
							$resp = ['mensaje' => "No se puede asignar más allá del 100%!", 'tipo' => "info", 'titulo' => "Oops!"];
							exit(json_encode($resp));
						}
					}
					$data = [
						"id_criterio" => $idEnc,
						"porcentaje" => $porcent,
						"id_encuesta" => $encuesta,
						"id_usuario_registra" => $id_usuario_registra
					];
					$query = $this->compras_model->guardar_info("criterios_asignados_compra", $data);
					if (empty($query)) {
						$resp = ['mensaje' => "Permiso habilitado y porcentaje guardado exitosamente!", 'tipo' => "success", 'titulo' => "Bien!"];
					} else {
						$resp = ['mensaje' => "No se pudo completar la operación! Error Nº: " . __LINE__ . ".", 'tipo' => "error", 'titulo' => ""];
					}
				}
			}
			exit(json_encode($resp));
		}
	}

	/* Disable permissions de porcentajes de criterios */
	public function disable_permission()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$criterio = $this->input->post("id_cri");
			$encuesta = $this->input->post("id_encuesta");
			if (!empty($criterio) && !empty($encuesta)) {
				$arrayToSend = [
					"id_criterio" => $criterio,
					"id_encuesta" => $encuesta
				];
				$query = $this->compras_model->del_info("criterios_asignados_compra", $arrayToSend);
				if (empty($query)) {
					$resp = ['mensaje' => "La operación, se ha realizado con éxito!", 'tipo' => "success", 'titulo' => "Bien!"];
				} else {
					$resp = ['mensaje' => "$query.", 'tipo' => "error", 'titulo' => ""];
				}
			}
		}
		exit(json_encode($resp));
	}

	/* Actualizar criterios en valor parametro */
	public function upd_valorp()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$idCriterio = $this->input->post("critId");
			$nombre_criterio = $this->input->post("valor");
			$criterio_descript = $this->input->post("valorx");

			$toCheck = [
				"Id Criterio" => $idCriterio,
				"Nombre del Criterio" => $nombre_criterio,
				"Descripción de Criterio" => $criterio_descript
			];

			$check = $this->pages_model->verificar_campos_string($toCheck);

			if (is_array($check)) {
				$r = ["mensaje" => "El campo " . $check["field"] . " contiene datos incorrectos o está vacío.", "tipo" => "warning", "titulo" => "Oops"];
			} else {

				$toSend = [
					"valor" => $nombre_criterio,
					"valorx" => $criterio_descript,
					"usuario_registra" => $_SESSION["persona"]
				];
				$query = $this->compras_model->upd_valorp($idCriterio, $toSend);

				if ($query) {
					$r = ["mensaje" => "La operación se ha realizado exitosamente!", "tipo" => "success", "titulo" => "Bien!"];
				} else {
					$r = ["mensaje" => "La operación no se ha realizado exitosamente!", "tipo" => "error", "titulo" => "Oops"];
				}
			}
		}
		exit(json_encode($r));
	}

	/* Eliminar criterios de valor_parametro */
	public function del_valorp()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$id_criterio = $this->input->post("cri_id");
			if (empty($id_criterio)) {
				$r = ['mensaje' => "El proceso no finalizó correctamente. Codigo del error: " . __LINE__, 'tipo' => "error", 'titulo' => ""];
			} else {
				$condicion = ["estado" => 0];
				$query = $this->compras_model->del_valorp($id_criterio, $condicion);
				if ($query) {
					$r = ['mensaje' => "La operación se realizó exitosamente!", 'tipo' => "success", 'titulo' => "Bien!"];
				} else {
					$r = ['mensaje' => "El proceso no finalizó correctamente", 'tipo' => "error", 'titulo' => ""];
				}
			}
		}
		exit(json_encode($r));
	}

	/*Funcion para adicionar criterios */
	public function add_valorp()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$nombre_criterio = $this->input->post("valor");
			$criterio_descript = $this->input->post("valorx");
			$usuario_regis = $_SESSION["persona"];
			$dato = $this->compras_model->find_idParametro('4_criterios');
			$id_parametro = $dato->idpa;

			$toCheck = [
				"Nombre del Criterio" => $nombre_criterio,
				"Descripción de Criterio" => $criterio_descript
			];

			$check = $this->pages_model->verificar_campos_string($toCheck);

			if (is_array($check)) {
				$r = ["mensaje" => "El campo " . $check["field"] . " contiene datos incorrectos o está vacío.", "tipo" => "warning", "titulo" => "Oops"];
			} else {

				$toSend = [
					"valor" => $nombre_criterio,
					"valorx" => $criterio_descript,
					"idparametro" => $id_parametro,
					"usuario_registra" => $usuario_regis
				];
				$query = $this->compras_model->add_valorp($toSend);

				if ($query) {
					$r = ["mensaje" => "La operación se ha realizado exitosamente!", "tipo" => "success", "titulo" => "Bien!"];
				} else {
					$r = ["mensaje" => "La operación no se ha realizado exitosamente!", "tipo" => "error", "titulo" => "Oops"];
				}
			}
			exit(json_encode($r));
		}
	}

	/* Funcion para listar ponderados rp */
	public function listar_ponderados_rp()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$query = $this->compras_model->listar_ponderados_rp();
			if ($query) {
				$r = [];
				$btn_upd = '<span title="Modificar Porcentaje" data-toggle="popover" data-trigger="hover" style="color: #337ab7;" class="pointer fa fa-wrench btn btn-default upd_porcentaje"></span>';
				$btn_del = '<span title="Eliminar Porcentaje" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;" class="pointer fa fa-trash btn btn-default del_porcentaje"></span>';
				foreach ($query as $val) {
					$val["accion"] = "$btn_upd $btn_del";
					array_push($r, $val);
				}
				exit(json_encode($r));
			} else {
				$r = [];
			}
		}
		exit(json_encode($r));
	}

	/* Funcion para actualizar porcentajes asignados */
	public function create_porcentajes()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$valor_ini = $this->input->post("valor_ini");
			$valor_fin = $this->input->post("valor_fin");
			$porcentaje = $this->input->post("porcentaje");

			$toCheck = [
				"Valor Inicial" => $valor_ini,
				"Valor Final" => $valor_fin,
				"Porcentaje" => $porcentaje
			];
			$check_nums = $this->pages_model->verificar_campos_numericos($toCheck);

			if (is_array($check_nums)) {
				$r = ['mensaje' => "El campo: " . $check_nums["field"] . ", solo debe contener valores numéricos!", 'tipo' => "error", 'titulo' => ""];
			} else {
				$dataToSet = ["valor_inicial" => $valor_ini, "valor_final" => $valor_fin, "porcentaje" => $porcentaje];
				$query = $this->compras_model->guardar_info("ponderacion_porcentaje_compra", $dataToSet);
				if (empty($query)) {
					$r = ['mensaje' => "La operación se ha realizado exitosamente!", 'tipo' => "success", 'titulo' => "Bien!"];
				} else {
					$r = ['mensaje' => "La operación no se ha realizado exitosamente!", 'tipo' => "error", 'titulo' => ""];
				}
			}
		}
		exit(json_encode($r));
	}

	/* Funcion para actualizar porcentajes asignados */
	public function upd_porcentajes()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$id_porcen = $this->input->post("id_ps");
			$valor_ini = $this->input->post("valor_ini");
			$valor_fin = $this->input->post("valor_fin");
			$porcentaje = $this->input->post("porcentaje");

			$toCheck = [
				"Valor Inicial" => $valor_ini,
				"Valor Final" => $valor_fin,
				"Porcentaje" => $porcentaje,
				"ID Destino" => $id_porcen
			];
			$check_nums = $this->pages_model->verificar_campos_numericos($toCheck);

			if (is_array($check_nums)) {
				$r = ['mensaje' => "El campo: " . $check_nums["field"] . ", solo debe contener valores numéricos!", 'tipo' => "error", 'titulo' => ""];
			} else {
				$dataToSet = ["valor_inicial" => $valor_ini, "valor_final" => $valor_fin, "porcentaje" => $porcentaje];
				$where = ["id" => $id_porcen];
				$query = $this->compras_model->upd_info("ponderacion_porcentaje_compra", $dataToSet, $where);
				if (empty($query)) {
					$r = ['mensaje' => "La operación se ha realizado exitosamente!", 'tipo' => "success", 'titulo' => "Bien!"];
				} else {
					$r = ['mensaje' => "$query.", 'tipo' => "error", 'titulo' => ""];
				}
			}
		}
		exit(json_encode($r));
	}

	/* Funcion para eliminar porcentajes asignados */
	public function del_porcentajes()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$id_porcen = $this->input->post("id_porcen");
			$condicion = ["id" => $id_porcen];
			$query = $this->compras_model->del_info("ponderacion_porcentaje_compra", $condicion);
			if (empty($query)) {
				$r = ['mensaje' => "La operación se ha completado con éxito.", 'tipo' => "success", 'titulo' => "Bien!"];
			} else {
				$r = ['mensaje' => "$query.", 'tipo' => "error", 'titulo' => ""];
			}
		}
		exit(json_encode($r));
	}

	/* Funcion para calcular evaluacion y su resultado final */
	public function calcular_porcentaje_rp($id_solicitud = "")
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$sst_porcent = 0;
			$sga_porcent = 0;
			$ser_o_mat_porcent = 0;
			$cumplimiento_porcent = 0;

			if (empty($id_solicitud) or $id_solicitud == "") {
				return false;
			} else {
				$compra_info = $this->compras_model->solicitud_compras_inf($id_solicitud, "row");

				if ($compra_info) {
					$criterio_buscado = "";
					$infToUpd = [];
					$datos_sumar = [];
					$sst_enc_inf = $compra_info->sst_quiz;
					$sga_enc_inf = $compra_info->sga_quiz;
					$tipser_enc_inf = $compra_info->tipser_quiz;
					$tipmat_enc_inf = $compra_info->tipmat_quiz;
					$dias_no_habiles = $compra_info->dias_no_habiles;
					$fecha_entrega_real = $compra_info->fecha_entrega_real;
					$area_selected = $compra_info->idaux_area_selected;
					$order_type = $compra_info->order_type;
					$calculo_total = 0;
					$dato = 0;
					$sw = false;

					if ($area_selected != "no_aplica") {
						if ($sst_enc_inf != null && $sga_enc_inf != null) {
							$criterio_buscado = "4_criterios";
						} else {
							$criterio_buscado = "3_criterios";
						}
					} else {
						$criterio_buscado = "2_criterios";
					}

					$criterio = $this->traer_valor_parametro("", $criterio_buscado);
					$ponder = $this->compras_model->criterios_asigned($criterio->id);

					//Aqui, verifico si el tipo de orden es servicio o material, para realizar la operacion con la info del campo necesario.
					$order_type == "Tip_Ser" ? $sw = true : $sw = false;
					$sw ? $dato = $tipser_enc_inf : $dato = $tipmat_enc_inf;

					/* Se realiza la resta entre fechas para obtener, según la tabla de ponderados, el porcentaje que debe asignarse según
					los días en los que se exceda el cumplimiento o si de plano, no se excede ningún día */
					$dnh = date_create($dias_no_habiles);
					$fer = date_create($fecha_entrega_real);
					$resul = date_diff($dnh, $fer);
					$resta_tiempo = $resul->format("%R%a");
					//$resta_tiempo = 5;

					$ponderados = $this->compras_model->listar_ponderados_rp();

					foreach ($ponderados as $val) {
						if ($resta_tiempo < 0) {
							$porcentaje_obtenido = $val["porcentaje"];
							break;
						}
						if ($resta_tiempo >= $val["valor_inicial"] && $resta_tiempo <= $val["valor_final"]) {
							$porcentaje_obtenido = $val["porcentaje"];
							break;
						}
					}
					/* Fin de la resta y sus condiciones. */

					/* Aquí realizamos las operaciones de multiplicar el promedio guardado de cada solicitud por el porcentaje que
					tengan asignados en la tabla de criterios y según los criterios a evaluar. */
					foreach ($ponder as $row) {
						if ($row["tipo_encuesta"] == "sst_enc") {
							if ($criterio_buscado == "3_criterios") {
								if ($sst_enc_inf == null && $sga_enc_inf != null) {
									$sst_porcent = $row['porcentaje'] / 100 * $sga_enc_inf;
								} else if ($sst_enc_inf != null && $sga_enc_inf == null) {
									$sst_porcent = $row['porcentaje'] / 100 * $sst_enc_inf;
								}
							} else {
								$sst_porcent = $row['porcentaje'] / 100 * $sst_enc_inf;
							}
							array_push($datos_sumar, $sst_porcent);
						}

						if ($row["tipo_encuesta"] == "sga_enc") {
							$sga_porcent = $row['porcentaje'] / 100 * $sga_enc_inf;
							array_push($datos_sumar, $sga_porcent);
						}

						if ($row["tipo_encuesta"] == "Tip_Ser") {
							$ser_o_mat_porcent = $row['porcentaje'] / 100 * $dato;
							array_push($datos_sumar, $ser_o_mat_porcent);
						}

						if ($row["tipo_encuesta"] == "Tip_Mat") {
							$ser_o_mat_porcent = $row['porcentaje'] / 100 * $dato;
							array_push($datos_sumar, $ser_o_mat_porcent);
						}

						if ($row["tipo_encuesta"] == "time_delivery") {
							$cumplimiento_porcent = $row['porcentaje'] / 100 * $porcentaje_obtenido;
							array_push($datos_sumar, $cumplimiento_porcent);
						}
					}

					foreach ($datos_sumar as $num) {
						$calculo_total += $num;
					}

					/* Aquí, enviamos los datos del calculo total a la solicitud gestionada, asignando el resultado final al campo:
					resultado_final_rp */
					$infToUpd = [
						"id_usuario_registra" => $_SESSION["persona"],
						"resultado_final_rp" => round($calculo_total, 1)
					];

					$tabla = "solicitud_compra";
					$where = ["id" => $id_solicitud];
					$query = $this->compras_model->upd_info($tabla, $infToUpd, $where);

					if (empty($query)) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			}
		}
	}

	/* Traer encuestas realizadas con sus respuestas RP */
	public function traer_encuestas_resueltas_rp()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {

			$idsol = $this->input->post("idsol");
			$idtipoenc = $this->input->post("id_tipo_enc");

			$query = $this->compras_model->traer_encuestas_resueltas_rp($idsol, $idtipoenc);

			if ($query) {
				$r = $query;
			} else {
				$r = [];
			}
		}
		exit(json_encode($r));
	}

	/* Verificar encuesta RP realizada */
	public function check_finished_encs($id_solicitud = "", $tipo_encuesta = "")
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$dato = $this->compras_model->find_idParametro('tipos_pregRP');
			$permiso = $this->compras_model->traer_permisos_encuestas($_SESSION["persona"], '', $dato->idpa);
			foreach ($permiso as $index => $row) {
				if ($tipo_encuesta == $row["tipo_encuesta"]) {
					$idpar = $this->compras_model->find_idParametro('tipos_pregRP');
					$id_enc_type = $this->compras_model->buscar_id_enctype($idpar->idpa, $row["tipo_encuesta"]);
					$finished = $this->compras_model->traer_encuestas_resueltas_rp($id_solicitud, $id_enc_type->id, "row"); // Verificamos que la encuesta este realizado
					if (empty($finished)) {
						$r = ["res" => "no"];
						break;
					} else {
						if ($finished->enc_idaux == $row["tipo_encuesta"]) {
							$r = ["mensaje" => "Usted ya ha realizado esta encuesta.", "tipo" => "error", "titulo" => "Oops", "res" => "si"];
							break;
						}
					}
				} else {
					$r = ["mensaje" => "Usted no puede realizar esta encuesta.", "tipo" => "error", "titulo" => "Oops", "res" => "si"];
				}
			}
		}
		return $r;
	}

	/* Funcion para listar las solicitudes de la persona en session que requiera hacer un masivo */
	public function massives_rp()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$tipo_encuesta = $this->input->post("tipo_encuesta");
			$proveedor = $this->input->post("proveedor");
			if (empty($tipo_encuesta)) {
				$r = [];
			} else {
				$solicitudes = [];
				$newdata = [];
				$dato = $this->compras_model->find_idParametro('tipos_pregRP');
				$permisos = $this->compras_model->traer_permisos_encuestas($_SESSION['persona'], '', $dato->idpa);
				$query = $this->compras_model->solicitud_compras_inf("", "array");
				$btnver = '<span style="background-color: white;color: black; width: 100%;" class="pointer form-control btn btn-default ver_detalles"><span>Ver</span></span>';

				if (count($query) > 0) {
					foreach ($query as $row) {
						if ($row['id_estado_solicitud'] == "Soli_Fin") {
							$row['ver'] = "$btnver";
							foreach ($permisos as $per) {
								if ($row['sst_quiz'] == "0" and $per['tipo_encuesta'] == "sst_enc") {
									$row['enc_type'] = $per['tipo_encuesta'];
									array_push($solicitudes, $row);
								} else if ($row['sga_quiz'] == "0" and $per['tipo_encuesta'] == "sga_enc") {
									$row['enc_type'] = $per['tipo_encuesta'];
									array_push($solicitudes, $row);
								} else if ($row['tipmat_quiz'] == "0" and $per['tipo_encuesta'] == "Tip_Mat") {
									$row['enc_type'] = $per['tipo_encuesta'];
									array_push($solicitudes, $row);
								} else if (($row['tipser_quiz'] == "0" and $per['tipo_encuesta'] == "Tip_Ser") and $row['id_solicitante'] == $_SESSION['persona']) {
									$row['enc_type'] = $per['tipo_encuesta'];
									array_push($solicitudes, $row);
								}
							}
						}
					}
				}
				foreach ($solicitudes as $soli) {
					if(empty($proveedor)) {
						if ($soli['enc_type'] == $tipo_encuesta) {
							array_push($newdata, $soli);
						}
					} else {
						if ($soli['enc_type'] == $tipo_encuesta && $soli['id_proveedor'] == $proveedor) {
							array_push($newdata, $soli);
						}
					}
				}
				$r = $newdata;
			}
		}
		exit(json_encode($r));
	}

	/* Filtrar encuestas por porveedor*/
	function listarProveedoresEnc(){
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$tipo_encuesta = $this->input->post("tipo_encuesta");
			if (empty($tipo_encuesta)) {
				$r = [];
			} else {
				$tipo_encuesta = str_replace("_enc", "", $tipo_encuesta);
				$r = $this->compras_model->listarProveedoresEnc($tipo_encuesta);
			}
		}
		exit(json_encode($r));
	}

	/* Listar tipo de preguntas para renderizar los titulos de los modales cuando se hace una encuesta */

	public function listar_catego_rp()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$criticoAlto =  $this->compras_model->traer_valor_parametro('', 'critico_alto');
			$newdata = [];
			$ids = $this->input->post("ids");
			$compra_inf = $this->compras_model->solicitud_compras_inf($ids, 'row');

			$tipoPreg = $this->compras_model->find_idParametro('tipos_pregRP');
			$permisos = $this->compras_model->traer_permisos_encuestas($_SESSION['persona'], '', $tipoPreg->idpa);
			$query = $this->compras_model->listar_tipos_preguntasRP('', $tipoPreg->idpa);

			$critico = $this->compras_model->find_idParametro('critico_alto');
			if ($query) {
				foreach ($query as $key => $row) {
					$row['id_solicitud'] = $ids;
					$finished_encs = $this->compras_model->traer_encuestas_resueltas_rp($ids, $row['idaux'], 'row');
					if ($row['idaux'] != 'time_delivery') {
						foreach ($permisos as $per) {
							if ($per['tipo_encuesta'] == $row['idaux']) {
								if ($compra_inf->id_clasificacion == $critico->id and $row['idaux'] == 'Tip_Ser' and $compra_inf->id_solicitante == $_SESSION['persona'] and $compra_inf->order_type == $row['idaux']) {
									$finished_encs ? $row['estado'] = 'complete' : $row['estado'] = 'incomplete';
									array_push($newdata, $row);
									break;
								} else if ($row['idaux'] != 'Tip_Ser' and $per['tipo_encuesta'] == $row['idaux']) {
									$finished_encs ? $row['estado'] = 'complete' : $row['estado'] = 'incomplete';
									array_push($newdata, $row);
								}
							}
						}
					}
				}
			}
			if (empty($compra_inf->res1_encuesta) or empty($compra_inf->res2_encuesta) or empty($compra_inf->res3_encuesta)) {
				if ($compra_inf->id_solicitante == $_SESSION['persona']) {
					array_push($newdata, ['id' => 'N/A', 'area' => 'Encuesta de satisfacción', 'idaux' => 'satis_enc', 'estado' => 'incomplete', 'id_solicitud' => $ids]);
				}
			}
			$newdata ? $r = $newdata : $r = [];
		}
		exit(json_encode($r));
	}

	/* Listar tipo de preguntas para el select de categorias en masivos */
	public function listar_categos_rp()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$newdata = [];
			$dato = $this->compras_model->find_idParametro('tipos_pregRP');
			$permisos = $this->compras_model->traer_permisos_encuestas($_SESSION['persona'], '', $dato->idpa);
			$query = $this->compras_model->listar_tipos_preguntasRP('', $dato->idpa);
			if ($query) {
				foreach ($query as $key => $row) {
					if ($row['idaux'] != 'time_delivery') {
						foreach ($permisos as $per) {
							if ($per['tipo_encuesta'] == $row['idaux']) {
								array_push($newdata, $row);
							}
						}
					}
				}
			}
			array_push($newdata, ['id' => 'N/A', 'area' => 'Encuesta de satisfacción', 'idaux' => 'satis_enc']);
			$newdata ? $r = $newdata : $r = [];
		}
		exit(json_encode($r));
	}

	/* Funcion para traer detalles de una solicitud, mostrar los articulos dependiendo del ID de la solicitud */
	public function detalles_articulos_masivos()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$idSol = $this->input->post("idSol");
			$query = $this->compras_model->detalles_articulos_masivos($idSol);
			$r = $query;
		}
		exit(json_encode($r));
	}

	/* Cronograma desde aqui - BORRAR COMENT */

	/* Listar Cronogramas */
	public function listar_cronograma()
	{
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$dato = $this->compras_model->find_idParametro('trime_time');
			$query = $this->compras_model->listar_cronograma($dato->idpa);
			$r = $query;
		}
		exit(json_encode($r));
	}

	/* Check para saber si se ha seteado el entregable de cronograma */
	public function check_entregable()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
		} else {
			$idSol = $this->input->post('id_Solicitud');
			$compra_inf = $this->compras_model->solicitud_compras_inf($idSol, 'row');
			//exit(json_encode($compra_inf));
			if ($compra_inf->id_tipo_orden == 'Tip_Ser') {
				$query = $this->compras_model->getInfoSoliCrono($idSol);
				if ($query) {
					$r = $query;
				} else {
					$r = [];
				}
			} else {
				$r = ['entregable' => 1];
			}
		}
		exit(json_encode($r));
	}

	/* Guardar entregable */
	public function guardar_entregable()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
		} else {
			$tabla = 'solicitud_compra';
			$numero_entregables = $this->input->post('numero_entregables');
			$tiempo_entregables = $this->input->post('tiempo_entregables');
			$idSoli = $this->input->post('id_Solicitud');

			//exit(json_encode($idSoli));

			if (!empty($numero_entregables) or !empty($tiempo_entregables) or !empty($idSoli)) {
				if (($numero_entregables <= 0 or $idSoli <= 0 or $tiempo_entregables <= 0) or (!is_numeric($numero_entregables) or !is_numeric($idSoli) or !is_numeric($tiempo_entregables))) {
					$r = ['mensaje' => 'Error al intentar guardar los datos. Codigo <span data-trigger="hover" data-placement="top" data-toggle="popover" data-content="El numero o tiempo de entregales tiene que ser mayor a 0." style="color:#337ab7;cursor: pointer;">#' . __LINE__.'</span>', 'tipo' => 'error', 'titulo' => 'Oops'];
				} else {
					$where = ['id' => $idSoli];
					$tosend = ['numero_entregables' => $numero_entregables, 'tiempo_entregables' => $tiempo_entregables];
					$resultado = $this->compras_model->upd_info($tabla, $tosend, $where);
					if (empty($resultado)) {
						$r = ['mensaje' => 'Los datos se han guardado correctamente', 'tipo' => 'success', 'titulo' => '¡Bien!'];
					} else {
						$r = ['mensaje' => "$resultado.", 'tipo' => 'error', 'titulo' => 'Error'];
					}
				}
			} else {
				$r = ['mensaje' => 'Error interno <span data-trigger="hover" data-placement="top" data-toggle="popover" data-content="El numero o tiempo de entregales tiene que ser mayor a 0." style="color:#337ab7;cursor: pointer;">#' . __LINE__.'</span>', 'tipo' => 'error', 'titulo' => 'Oops'];
			}
		}
		exit(json_encode($r));
	}

	public function verificarCronogramas($idSolicitud){
		$cronogramas = $this->compras_model->datosCronograma($idSolicitud);
		
		if (empty($cronogramas)) {
			$solicitud = $this->compras_model->getInfoSoliCrono($idSolicitud);
			$date_now = date('Y-m-d');			
			for ($i = 1; $i <= $solicitud->numero_entregables; $i++) {
				//Incrementando x dias			
				$date_future = strtotime("+ $solicitud->tiempo_entregables day", strtotime($date_now));
				$date_now = date('Y-m-d', $date_future);
				$datos = [
					"id_solicitud" => $idSolicitud,
					"item" => $i,
					"especificaciones" => $date_now,
					"estado_cronograma" => "Crono_No_Fin",
					"id_usuario_registra" => $_SESSION['persona']
				];
				$this->compras_model->guardar_info('compras_cronograma', $datos);
			}	
			return $this->compras_model->datosCronograma($idSolicitud);
		} else {
			return $cronogramas;
		}
	}

	/* Listar cronograma */
	public function traer_cronograma()
	{
		if (!$this->Super_estado) {
			$r = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
		} else {
			// Se identifica si el usuario es administrador o personal de compras
			$per_comp = false; $admin_comp = false;
			if ($_SESSION["perfil"] == "Per_Com" or $_SESSION["perfil"] == "Per_Alm") {
				$per_comp = true;
			} else if ($_SESSION["perfil"] == "Per_Admin" or $_SESSION["perfil"] == "Per_Adm_Com") {
				$admin_comp = true;
			}

			// Obtengo la información de la solicitud
			$idSoli = $this->input->post('id_Solicitud');
			$solicitud = $this->compras_model->getInfoSoliCrono($idSoli);
			
			// Verifico y guardo los cronogramas si estos no existen
			$cronogramas = $this->verificarCronogramas($idSoli);		

			// Botones a utilizar en la vista de los cronogramas
			$btn_like = '<span title="¡Diligenciar especificación!" style="color: #27B579;" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn btn-default btn_especificar"></span>';
			$btn_reloj = '<span title="¡En espera de que el solicitante apruebe!" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn" style="color:#428bca"></span>';
			$btn_off = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
			$btn_ver_detalles = '<span title="Ver Archivos" style="color: #27B579;" data-toggle="popover" data-trigger="hover" class="btn btn-default btn_ver_detalles_cronograma">Ver</span>';
			
			// Se define que vista se va a ver si la de el solicitande o la del personal de compras
			$alert = false;
			($admin_comp or $per_comp) ? $vista = '1' : $vista = '2';
			$alert = $solicitud->id_solicitante == $_SESSION['persona'] ? true : false;

			// Parte donde se empezara a pintar el cronograma
			$toview = [];
			foreach ($cronogramas as $key => $valor) {
				$especificaciones = $valor['especificaciones'];
				$detalles_estado = $valor['crono_status'];
				$estado_cronograma = $valor['estado_cronograma'];
				empty($valor['solicitante_comentario']) ? $cliente_coment = '<span style="opacity: 0.7;">---------------</span>' : $cliente_coment = $valor['solicitante_comentario'];

				// Se verifica si la persona tiene permisos para gestionar el cornograma
				$perm = false;
				$perm_crono = $this->obtener_permisos_cronogramas($valor['estado_cronograma'], $_SESSION['persona']);
				$perm = ($admin_comp || $perm_crono) ?  true : false;
				if ($valor['estado_cronograma'] == 'Crono_No_Fin') {
					$btn_accion = $perm || $solicitud->id_solicitante == $_SESSION['persona'] ? "$btn_like" : $btn_reloj;								
				} else if ($valor['estado_cronograma'] == 'Crono_En_Conta') {
					$btn_accion = $perm ? "$btn_like" : $btn_reloj;
				} else if ($valor['estado_cronograma'] == 'Crono_En_Revi') {
					$btn_accion = $perm ? "$btn_like" : $btn_reloj;
				} else if ($valor['estado_cronograma'] == 'Crono_En_Tes') {
					$btn_accion = $perm ? "$btn_like" : $btn_reloj;
				}else {
					$btn_accion = "$btn_off";
				}

				// Matrix de los datos a enviar al JS
				$compiled = [
					"ver" => $btn_ver_detalles,					
					"item" => 'Recibido '.$valor['item'],
					"fecha" => $especificaciones,					
					"cliente_coment" => $cliente_coment,										
					"detalles_estado" => $detalles_estado,					
					"acciones" => $btn_accion,	
					"id" => $valor['id'],
					"solicitante" => $solicitud->solicitante,
					"vista" => $vista,
					"estado_cronograma" => $estado_cronograma,				
					"alert" => $alert
				];
				array_push($toview, $compiled);
			}
			$r = $toview;
		}
		exit(json_encode($r));
	}

	/* Guardar cronogramas */
	public function guardar_cronograma()
	{
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$admin = false;
			($_SESSION["perfil"] == "Per_Admin" or $_SESSION["perfil"] == "Per_Adm_Com") ? $admin = true : $admin = false;
			$idSol = $this->input->post('id_Solicitud');
			$idCrono = $this->input->post('idCrono');
			$comentario = $this->input->post('comentario');
			$compra_inf = $this->compras_model->solicitud_compras_inf($idSol, 'row');
			$cronograma = $this->compras_model->datos_cronograma_filtered($idCrono);
			$usuario_registra = $compra_inf->id_solicitante;
			$perm_crono = $this->obtener_permisos_cronogramas($cronograma->estado, $_SESSION['persona']);
			$perm = $perm_crono ?  true : false;
			$table = "compras_cronograma";
			$datos = [
				"solicitante_comentario" => $comentario,
				"id_usuario_registra" => $_SESSION['persona']
			];

			if(!empty($usuario_registra) and ($usuario_registra == $_SESSION['persona'] or $admin || $perm)){
				if ($cronograma->estado == 'Crono_No_Fin') {
					$datos["estado_cronograma"] = 'Crono_En_Conta';
				} elseif ($cronograma->estado == 'Crono_En_Conta') {
					$datos["estado_cronograma"] = 'Crono_En_Revi';
				} else  if ($cronograma->estado == 'Crono_En_Revi') {
					$datos["estado_cronograma"] = 'Crono_En_Tes';
				} else if ($cronograma->estado == 'Crono_En_Tes') {
					$datos["estado_cronograma"] = 'Crono_Si_Fin';
				}
			}
			$where = ['id' => $idCrono];
			if ($_SESSION['persona'] == $compra_inf->id_solicitante || $admin || $perm) {
				$verificar = $this->verificar_estados_cronograma($idCrono, $datos["estado_cronograma"]);
				if($verificar){
					$r = ["mensaje" => "El cronograma no puede pasar dos veces por el mismo estado, contáctese con soporte técnico para mas información.", "tipo" => "error", "titulo" => "¡Error!"];
				}else{
					$this->enviar_correo_permiso_cronograma($datos["estado_cronograma"]);
					$resp = $this->guardar_estados_cronograma($idCrono, $datos["estado_cronograma"], $comentario);
					$query = $this->compras_model->upd_info($table, $datos, $where);
					if (empty($query) && $resp == 1) {
						$entregables_status = $this->check_estado_entregables($idSol);
						$validar_estado_soli = $this->validar_estado_siguiente($idSol, 'Ser_Rec');
						$r = ["mensaje" => "La especificación se ha guardado correctamente.", "tipo" => "success", "titulo" => "¡Bien!", 'entregables_status' => $entregables_status];
						if ($entregables_status == 1 && $validar_estado_soli == 1) {
							//Cambiamos el estado de la solicitud siempre y cuando se cumplan los checks de parte del usuario
							$cambio_estado = $this->Gestionar_solicitud($idSol, 'Ser_Rec', -1, true);
							if ($cambio_estado == 1) {
								$r = ["mensaje" => "La especificación se ha guardado correctamente.", "tipo" => "success", "titulo" => "¡Bien!", 'entregables_status' => $entregables_status, 'next_step' => 3];
							} else {
								$r = ["mensaje" => 'La información no se ha podido guardar; error #' . __LINE__ . '.', 'tipo' => 'error', 'titulo' => ''];
							}
						}										
					} else {
						$r = ["mensaje" => "$query.", "tipo" => "error", "titulo" => "", 'next_step' => -1];
					}
				}								
			} else {
				$r = ["mensaje" => "Usted no posee los permisos realizar acciones sobre este item.", "tipo" => "error", "titulo" => "", 'next_step' => -1];
			}
		}
		exit(json_encode($r));
	}

	/* Guardar programa pero si los datos vienen como array */
	public function save_cronos_array($datos, $idSol)
	{
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {

			//Envios
			foreach ($datos as $dato) {
				$tocheck = [
					"Identificador de la Solicitud" => $idSol,
					"Identificador de Entregable" => $dato['id_entregable'],
					"Especificaciones" => $dato['fecha'],
					"Codigo del Ítem" => $dato['codigo_item']
				];

				$check = $this->pages_model->verificar_campos_string($tocheck);

				if (is_array($check)) {
					$r = ["mensaje" => $check['field'] . " no ha sido diligenciado correctamente.", "tipo" => "warning", "titulo" => "¡Atención!"];
				} else {
					$table = "compras_cronograma";
					$datos = [
						"id_solicitud" => $idSol,
						"item" => $dato['id_entregable'],
						"codigo_item" => $dato['codigo_item'],
						"especificaciones" => $dato['fecha'],
						"estado_cronograma" => "Crono_No_Fin",
						"id_usuario_registra" => $_SESSION['persona']
					];

					$query = $this->compras_model->guardar_info($table, $datos);

					if (empty($query)) {
						$tabla = "solicitud_compra";
						$tosend = ["codigo_item" => $dato['codigo_item']];
						$where = ["id" => $idSol];
						$upd_meta = $this->compras_model->upd_info($tabla, $tosend, $where);
						//exit(json_encode($tosend));
						if (empty($upd_meta)) {
							$r = ["mensaje" => "La especificación se ha guardado correctamente.", "tipo" => "success", "titulo" => "¡Bien!"];
						} else {
							$r = ["mensaje" => "$upd_meta.", 'tipo' => 'error', 'titulo' => ''];
						}
					} else {
						$r = ["mensaje" => 'La información no se ha podido guardar; error #' . __LINE__ . '.', 'tipo' => 'error', 'titulo' => ''];
					}
				}
			}
			$estado_valido = $this->validar_estado_siguiente($idSol, 'Soli_Pen');
			if ($estado_valido == 1) {
				$cambio_estado = $this->Gestionar_solicitud($idSol, 'Soli_Pen', -1, true);
				if ($cambio_estado == 1) {
					$r = ["mensaje" => "La especificación se ha guardado correctamente.", "tipo" => "success", "titulo" => "¡Bien!", 'next_step' => 3];
				} else {
					$r = ["mensaje" => 'La información no se ha podido guardar; error #' . __LINE__ . '.', 'tipo' => 'error', 'titulo' => ''];
				}
			}
		}
		return $r;
	}

	/* Actualizar cronograma */
	public function upd_cronograma()
	{
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$idSoli = $this->input->post('id_Solicitud');
			$idEnt = $this->input->post('entregable');
			$espe = $this->input->post('especi');
			$codigo_item = $this->input->post('codigo_item');
			$idCrono = $this->input->post('idCrono');
			$crono_si = 'Crono_No_Fin';
			$seguir = true;

			$chk = $this->compras_model->datosCronograma($idSoli, '');

			if ($chk) {
				foreach ($chk as $roww) {
					if ($roww['id'] == $idCrono) {
						if ($roww['estado_cronograma'] == 'Crono_No_Fin') {
							$seguir = false;
							break;
						}
					}
				}
			}

			if ($seguir) {
				$tocheck = [
					"Identificador de la Solicitud" => $idSoli,
					"Identificador de Entregable" => $idEnt,
					"Especificaciones" => $espe,
					"Codigo del Ítem" => $codigo_item,
					"Identificador de Cronograma" => $idCrono
				];

				$check = $this->pages_model->verificar_campos_string($tocheck);

				if (is_array($check)) {
					$r = ["mensaje" => $check['field'] . " no ha sido diligenciado correctamente.", "tipo" => "warning", "titulo" => "¡Atención!"];
				} else {
					$table = "compras_cronograma";
					$datos = [
						"especificaciones" => $espe,
						'estado_cronograma' => $crono_si,
						'solicitante_comentario' => '',
						"id_usuario_registra" => $_SESSION['persona']
					];
					$where = ["id" => $idCrono];

					$upd = $this->compras_model->upd_info($table, $datos, $where);
					if (empty($upd)) {
						$r = ["mensaje" => "El cronograma se ha actualizado con exitosamente.", "tipo" => "success", "titulo" => "Bien!"];
					} else {
						$r = ["mensaje" => "$upd.", 'tipo' => 'error', 'titulo' => ''];
					}
				}
			} else {
				$r = ["mensaje" => "No puede realizar actualizaciones mientras el solicitante no haya aprobado la entrega del servicio.", "tipo" => "error", "titulo" => ""];
			}
		}
		exit(json_encode($r));
	}

	/* Denegar cronograma */
	public function denegar_cronograma()
	{
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$idSol = $this->input->post('idSol');
			$idEnt = $this->input->post('idEnt');
			$idCrono = $this->input->post('idCron');
			$coment = $this->input->post('coment');
			$tabla = 'compras_cronograma';
			$datos = ['estado_cronograma' => 'Crono_Dene', 'solicitante_comentario' => $coment];
			$where = ['id' => $idCrono, 'id_solicitud' => $idSol, 'item' => $idEnt];
			$query = $this->compras_model->upd_info($tabla, $datos, $where);

			if (empty($query)) {
				$r = ["mensaje" => "La información se ha guardado correctamente.", "tipo" => "success", "titulo" => "¡Bien!"];
			} else {
				$r = ["mensaje" => "$query.", "tipo" => "error", "titulo" => ""];
			}
		}
		exit(json_encode($r));
	}

	/* listar adjuntos cronograma */
	public function listar_adjuntos_cronograma()
	{
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$idCrono = $this->input->post('id');
			$r = $this->compras_model->listar_adjuntos_cronograma($idCrono);
			$arrayCrono = [];
			if (!empty($r)) {
				foreach ($r as $crono) {
					$crono['ver'] = '<a href="' . base_url() . $this->ruta_archivos_solicitudes . '/' . $crono['nombre_guardado'] . '" target="_blank" title="Ver Archivo" style="color: #27B579;" data-toggle="popover" data-trigger="hover" class="btn btn-default">Ver</a>';
					array_push($arrayCrono, $crono);
				}
			}
		}
		exit(json_encode($arrayCrono));
	}

	/* Check si todos los entregables estan correctamente diligenciados para poder pasar al siguiente estado */
	public function check_estado_entregables($idSol = '', $row_or_array = false)
	{
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {

			if (empty($idSol)) {
				$idSol = $this->input->post('idSol');
				$row_or_array = true;
			}

			$current_crono_status = -1;

			$query = $this->compras_model->datosCronograma($idSol, '');
			if ($query) {
				foreach ($query as $key => $row) {
					if ($row['estado_cronograma'] == 'Crono_No_Fin') {
						$current_crono_status = -1;
						break;
					} else {
						$current_crono_status = 1;
					}
				}
			}
			$r = $current_crono_status;
		}

		if ($row_or_array) {
			exit(json_encode($r));
		} else {
			return $r;
		}
	}

	/* Buscar datos de valor_parametro */
	public function traer_datos_valorp($idparametro = "", $id = "", $row = false)
	{
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$query = $this->compras_model->traer_datos_valorp($idparametro, $id, $row);
			$r = $query;
		}
		return $r;
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
				$query = $this->compras_model->find_idParametro($codigoo);
				$r = $query;
			}
		}
		if ($return) {
			return $r;
		} else {
			exit(json_encode($r));
		}
	}

	public function listar_permisos_cronogramas($persona_buscada = ''){
		$return = false;
		if (isset($persona_buscada) && !empty($persona_buscada) && $persona_buscada != '') {
			$return = true;
		}else{
			$persona_buscada = $this->input->post('persona_buscada');
		}

		if (!$this->Super_estado) {
			$data = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$arrayCrono = [];
			$desasignar = '<span class="btn btn-default desasignar_permiso_cronograma" title="Desasignar Estado"><span class="fa fa-toggle-on" style="color: #5cb85c"></span></span> ';
			$asignar = '<span class="btn btn-default asignar_permiso_cronograma" title="Asignar Estado"><span class="fa fa-toggle-off"></span></span> ';
			$data = (isset($persona_buscada) && !empty($persona_buscada)) ? $this->compras_model->listar_permisos_cronogramas($persona_buscada, $this->find_idParametro('Crono_No_Fin')->idpa) : [];
			foreach ($data as $array) {
				if ($array['gestion'] == 1) {
					$array['perm'] = $desasignar;
				}else{
					$array['perm'] = $asignar;
				}
				array_push($arrayCrono, $array);
			}
		}

		if ($return) {
			return $arrayCrono;
		}else{
			echo json_encode($arrayCrono);
		}
	}
	
	public function asignar_encuesta_cronogramas(){
		if (!$this->Super_estado) {
			$data = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$persona = $this->input->post('persona');
			$estado = $this->input->post('estado');
			$res = (isset($persona) && !empty($persona) && isset($estado) && !empty($estado)) ? $this->compras_model->asignar_encuesta_cronogramas($persona, $estado) : [];
			if($res == 1){
				$data = ["mensaje" => "¡El permiso se ha asignado correctamente!", "tipo" => "success", "titulo" => "Bien!"];
			}else{
				$data = ["mensaje" => "¡Error al asignar el permiso!", 'tipo' => 'error', 'titulo' => 'Error!'];
			}
		}
		echo json_encode($data);
	}

	public function desasignar_encuesta_cronogramas(){
		if (!$this->Super_estado) {
			$data = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$persona = $this->input->post('persona');
			$estado = $this->input->post('estado');
			$res = (isset($persona) && !empty($persona) && isset($estado) && !empty($estado)) ? $this->compras_model->desasignar_encuesta_cronogramas($persona, $estado) : [];
			if($res == 1){
				$data = ["mensaje" => "¡El permiso se ha desasignado correctamente!", "tipo" => "success", "titulo" => "Bien!"];
			}else{
				$data = ["mensaje" => "¡Error al desasignar el permiso!", 'tipo' => 'error', 'titulo' => 'Error!'];
			}
		}
		echo json_encode($data);
	}

	/* obtener permisos cronogramas*/
	public function obtener_permisos_cronogramas($estado_cronograma = "", $persona = ""){
		if (!$this->Super_estado) {
			$resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$permisos = $this->compras_model->obtener_permisos_cronogramas($estado_cronograma, $persona);
			$resp = isset($permisos) && !empty($permisos) ? $permisos: false;				
		}
		return $resp;
	}

	public function guardar_estados_cronograma($id_cronograma, $id_estado, $observacion){
		if (!$this->Super_estado) {
			$resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$resp = $this->compras_model->guardar_estados_cronograma($id_cronograma, $id_estado, $observacion);
			$resp = isset($resp) && !empty($resp) ? $resp: false;				
		}
		return $resp;
	}

	public function verificar_estados_cronograma($id_cronograma, $id_estado){
		if (!$this->Super_estado) {
			$resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$resp = $this->compras_model->verificar_estados_cronograma($id_cronograma, $id_estado);
			$resp = isset($resp) && !empty($resp) ? true : false;				
		}
		return $resp;
	}

	public function obtener_solicitudes_cronogramas_gestionar($persona){
		if (!$this->Super_estado) {
			$resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$resp = $this->compras_model->obtener_solicitudes_cronogramas_gestionar($persona);			
		}
		return $resp;
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

	public function enviar_correo_permiso_cronograma($estado_cronograma){
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$baseurl = base_url();
			$permisos_cronograma = $this->compras_model->obtener_permisos_cronogramas($estado_cronograma, '');
			if (!empty($permisos_cronograma)) {
				foreach ($permisos_cronograma as $valor) {
					$correo = $valor["correo"];
					$nombre = $valor["persona"];
					//$correo = "jpena41@cuc.edu.co";
					$msg = "Usted tiene cronogramas que están pendientes por aprobación, a partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud.<br><br>Mas información en: <a href='" . $baseurl . "index.php/compras' target='new_black'><b>agil.cuc.edu.co</b></a>.";
					$desde = "Compras CUC";
					$asunto = "¡Realización de cronogramas!";
					$codigoo = "ParCodAdm";
					$notificar = $this->enviar_correo_personalizado("comp", $msg, $correo, $nombre, $desde, $asunto, $codigoo, 1);
					//$notificar = 1;
					if ($notificar != 1) {
						exit(json_encode(["mensaje" => "No se pudo enviar el correo de notificación.", "tipo" => "warning", "titulo" => "Oops"]));
					} else {
						$r = $notificar;
					}
				}
			}else{
				$r = 0;
			}			
		}
		return $r;
	}

	public function listarEstadosConograma() {
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$idCronograma = $this->input->post('idCronograma');
			$r = $this->compras_model->listarEstadosConograma($idCronograma);
		}
		echo json_encode($r);
	}

	public function obtenerDatosSolicitud() {
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$idSolicitud = $this->input->post('idSolicitud');
			$r = $this->compras_model->obtenerDatosSolicitud($idSolicitud);
			$r[0]['valoracion'] = $this->calificacionTiempoEntrega($this->compras_model->solicitud_compras_inf($idSolicitud, 'row'));
		}
		echo json_encode($r);
	}

	public function listarPersConEncuestas() {
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$r = $this->compras_model->listarPersConEncuestas();
		}
		echo json_encode($r);
	}

	public function listarSoliConEncuestas() {
		if (!$this->Super_estado) {
			$r = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
		} else {
			$idper = $this->input->post('idper');
			$r = $this->compras_model->listarSoliConEncuestas($idper);
		}
		echo json_encode($r);
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
