<?php
	class tickets_control extends CI_Controller {
	//Variables encargadas de los permisos que tiene el usuario en session
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
	var $ruta_evidencia = "archivos_adjuntos/tickets/ev_solucion/";
	var $horas_h = 0;




	//Construtor del controlador, se importa el modelo inventario_model y se inicia la session
	public function __construct() {
		parent::__construct();
		include('application/libraries/festivos_colombia.php');
		$this->load->model('genericas_model');
        $this->load->model('tickets_model');
        $this->load->model('pages_model');
		session_start();
		date_default_timezone_set("America/Bogota");
		//la variable Super_estado es la encargada de notificar si el usuario esta en sesion, si no esta en sesion no podra ejecutar ninguna funcion, cuando pasa eso se retorna sin_session en la funcion que se esta ejecutando,por otro lado las variables Super_elimina, Super_modifica, Super_agrega se encarga de delimitar los permisos que tiene el perfil del usuario en la actividad que esta trabajando, si no tiene permiso las variables toman un valor de 0 y no les permite ejecutar la funcion retornando -1302.
		if (isset($_SESSION["usuario"])) {
			$this->Super_estado = true;
			$this->Super_elimina = 1;
			$this->Super_modifica = 1;
			$this->Super_agrega = 1;
			$this->administra = $_SESSION['perfil'] == 'Per_Admin' ? true : false;
			$this->soporte = $_SESSION['perfil'] == 'Per_Sop' ? true : false;
		}
	}

	/**
	 * Se encarga de pintar el modulo de talento humano, se carga el header alterno y la ventana inventario
	 * @return Void
	 */
	public function index($id = 0) {
		$pages = "inicio";
		$data['js'] = "";
		$data['actividad'] = "Ingresar";
		if ($this->Super_estado) {
			$datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'tecnologia/tickets');

			if (!empty($datos_actividad)) {
				$pages = 'tickets';
				$data['vista'] = 'tickets';
				$data['id'] = $id;
				$data['js'] = 'tickets';
				$data['actividad'] = $datos_actividad[0]["id_actividad"];
				$data['sin_asignar'] = $this->administra || $this->soporte ? $this->tickets_model->ver_sin_asignar() : 0;
				$data['sin_solucion'] = $this->administra || $this->soporte ? $this->tickets_model->ver_sin_solucion() : 0;
			}else{
				$pages = "sin_session";
				$data['js'] = "";
				$data['actividad'] = "Permisos";    
			}
		}
		$this->load->view('templates/header',$data);
		$this->load->view("pages/".$pages);
		$this->load->view('templates/footer');
	}


	public function verificar_campos_string($array){
		foreach ($array as $row) {
			if (empty($row) || ctype_space($row)) {
				return ['type' => -2, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
	}

	public function cargar_archivo($mi_archivo, $ruta, $nombre){
		$nombre .= uniqid();
		$tipo_archivos = $this->genericas_model->obtener_valores_parametro_aux("For_Adm", 20);
		$tipo_archivos = empty($tipo_archivos) ? "*": $tipo_archivos[0]["valor"];
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


	public function guardar_ticket(){
		if (!$this->Super_estado) $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega == 0) $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else {
				$admin =  $_SESSION['perfil'] == 'Per_Admin' ? true : false;
				$soporte =  $_SESSION['perfil'] == 'Per_Sop' ? true : false;
					$asunto = $this->input->post('asunto');
					$descripcion =  $this->input->post('descripcion');
					$id_tipo_solicitud =  $this->input->post('tipo_solicitud');
					$id_usuario_registra = $_SESSION['persona'];
					$solicitante = $this->input->post('id_persona');
					if(!empty($solicitante)){
						$id_solicitante = ($admin ? $this->input->post('id_persona') : ($soporte ? $this->input->post('id_persona') : $id_usuario_registra)); 
					}else{
						$id_solicitante = $id_usuario_registra;
					}
					$str = $this->verificar_campos_string(['Solicitante' => $id_solicitante, 'Asunto' => $asunto,'Descripcion' => $descripcion, 'Tipo Solicitud' => $id_tipo_solicitud]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					if($id_tipo_solicitud == 'TP_Sol_Agil' || $id_tipo_solicitud == 'TP_Sol_Emma'){
						if(!empty($_FILES["adj_evidencia_sol"]["size"])){
							$nombre = $_FILES["adj_evidencia_sol"]["name"]; 
							$file_evidence = $this->cargar_archivo("adj_evidencia_sol", $this->ruta_evidencia, 'evidencia');
							if ($file_evidence[0] == -1){
								$resp = ['mensaje'=>"Error al cargar al cargar la evidencia.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
								$adj_evi = '';			
							}else{
								$adj_evi = $file_evidence[1];
							}
						}else{
							$adj_evi = '';	
						}
						$data = [
						'asunto' => $asunto,
						'descripcion' => $descripcion,
						'id_usuario_registra' => $id_usuario_registra,
						'id_solicitante' => $id_solicitante,
						'id_tipo_solicitud' => $id_tipo_solicitud,
						'id_estado_ticket' => 'TIK_Soluc',
						'file_name' => $adj_evi,
						];
					}else{
						$data = [
							'asunto' => $asunto,
							'descripcion' => $descripcion,
							'id_usuario_registra' => $id_usuario_registra,
							'id_solicitante' => $id_solicitante,
							'id_tipo_solicitud' => $id_tipo_solicitud,
						];
					}
						$add = $this->pages_model->guardar_datos($data, "tickets_solicitudes");	
					//($admin ? $btn_negar.' '.$btn_anular.' '.$btn_asignar : ($soporte ? $btn_negar.' '.$btn_asignar: $btn_anular));
					if ($add) {
						$solicitud = $this->tickets_model->traer_registro_id('tickets_solicitudes', 'id_usuario_registra', $id_usuario_registra);
						$resp= ['mensaje' => "Requerimiento guardado con exito.",'tipo' => "success",'titulo' => "Proceso Exitoso.!"];
							if($id_tipo_solicitud == 'TP_Sol_Agil' || $id_tipo_solicitud == 'TP_Sol_Emma'){
								$estado_fin = [];
								array_push($estado_fin,'TIK_Regis');
								array_push($estado_fin,'TIK_Soluc');
								foreach ($estado_fin as $est_fin) {
									$data_estado = [
										'id_solicitud' => $solicitud->{'id'},
										'id_estado_ticket' => $est_fin,
										'id_usuario_registra' => $id_usuario_registra,
										'descripcion' => $descripcion,
										'id_solicitante' => $id_solicitante,
									];
									$add = $this->pages_model->guardar_datos($data_estado, "tickets_estados");
								}
								
							}else{
								$data_estado = [
									'id_solicitud' => $solicitud->{'id'},
									'id_estado_ticket' => 'TIK_Regis',
									'id_usuario_registra' => $id_usuario_registra,
									'descripcion' => $descripcion,
									'id_solicitante' => $id_solicitante,
								];
								$add = $this->pages_model->guardar_datos($data_estado, "tickets_estados");
							}	
					}else $resp = ['mensaje' => "Error al guardar el ticket, contacte con el administrador.",'tipo' => "error",'titulo' => "Oops.!"];
				}
			}
		}
		echo json_encode($resp);
	}
	public function suspender() {
	  if(!$this->Super_estado){
		$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
	}else{
		$id_solicitud = $this->input->post("id_solicitud");
		$anotaciones = $this->input->post('id_motivo');
		$motivo = $this->input->post('id_motivo');
		$id_usuario_registra = $_SESSION["persona"];
		$descripcion_suspender = $this->input->post('descripcion_suspender');
		$id_solicitante = $this->tickets_model->obtenerSolicitante($id_solicitud);
		$str = $this->verificar_campos_string(['Motivo' => $motivo]);
		if (is_array($str)) {
			$campo = $str['field'];
			$resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
		}else{
		$data_fac = [
			'id_anotaciones' => $anotaciones,
			'id_estado_ticket' => "TIK_Suspen",
		];
		$add = $this->tickets_model->modificar_datos($data_fac, 'tickets_solicitudes', $id_solicitud);
		$data = [
			'id_solicitud' => $id_solicitud,
			'id_estado_ticket' => "TIK_Suspen",
			'id_usuario_registra' => $id_usuario_registra,
			'id_motivo' => $motivo,
			'descripcion' => $descripcion_suspender,
			"id_solicitante" => $id_solicitante->{'id_solicitante'},
		];
		$add = $this->genericas_model->guardar_datos($data, 'tickets_estados');
		//$solicitud = $this->facturacion_model->consulta_solicitud_id($id_solicitud);
		//$id_solicitud_correo = $solicitud -> {'id'};

		$resp = ['mensaje' => "Proceso finalizado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
	}
	} 
	echo json_encode($resp);
	}

	public function listado_solicitudes(){
		$admin =  $_SESSION['perfil'] == 'Per_Admin' ? true : false;
		$soporte =  $_SESSION['perfil'] == 'Per_Sop' ? true : false;
		$admin_soporte = $_SESSION['perfil'] == 'Admin_Sopor' ? true : false;
		$id = $this->input->post("id");
		$fecha_inicial = $this->input->post("fecha_inicial");
		$fecha_final = $this->input->post("fecha_final");
		$hora_inicio = $this->input->post("hora_inicio_filtro");
		$hora_fin = $this->input->post("hora_fin_filtro");
		$id_tipo_solicitud = $this->input->post("id_tipo_solicitud");
		$id_estado_sol = $this->input->post("id_estado_sol");
		$id_estado_solicitud = $this->input->post('id_estado_solicitud');
		$permiso_asignar = $this->tickets_model->permiso_asignar() ? true : [];
		$data = $this->Super_estado == true ? $this->tickets_model->listado_solicitudes($id, $fecha_inicial, $fecha_final, $hora_inicio, $hora_fin, $id_tipo_solicitud, $id_estado_sol, $permiso_asignar) : array();
		$ver_finalizado = '<span  style="background-color: #39b23b;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';      
        $ver_rojo = '<span  style="background-color: #d9534f;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';      
		$ver_naranja = '<span  style="background-color: #f0ad4e;color: white;width: 100%;" class="pointer form-control ver" id="ver_detalle"><span >ver</span></span>';      
		$ver_solicitado = '<span  style="background-color: #ffff;color: #000;width: 100%;" class="pointer form-control ver" id="ver_detalle"><span >ver</span></span>';
        $ver_formulacion = '<span  style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" ><span >ver</span></span>';
		$ver_enviado = '<span  style="background-color: #232f85; color: white; width: 100%" class="pointer form-control ver" ><span >ver</span></span>';
		$ver_revision = '<span  style="background-color: #777;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
		$ver_tramitado = '<span  style="background-color: #f0ad4e;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
		$ver_aceptado = '<span  style="background-color: #428bca;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
		$ver_cancelada = '<span  style="background-color: #d9534f;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
		$ver_finilizada = '<span style="background-color: #5cb85c;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';

		$btn_modificar = '<span title="Modificar Atención" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
		$btn_MalRegis = '<span title="Mal Registrada" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default mregis"></span>';
		$btn_anular = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px" class="pointer fa fa-remove btn btn-default cancelar"></span>';
        $btn_cerrada = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
		$btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
		$btn_revisar  = '<span title="Revisar" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn btn-default fa fa-check revisar"></span>';
		$btn_formulacion = '<span title="En Formulación" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="pointer fa fa-pencil-square-o btn btn-default formulacion"></span>';
		$btn_enviar = '<span title="Enviar" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;" class="pointer fa fa-send btn btn-default enviar"></span>';
		$btn_revision = '<span title="Convertir a requerimiento de servicio" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="pointer fa fa-retweet btn btn-default revision"></span>';            
		$btn_aprobar = '<span title="Aprobar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;"class="pointer fa fa-check btn btn-default aprobar"></span>';
		$btn_finalizar = '<span title="Finalizar" data-toggle="popover" data-trigger="hover" style="color: #00cc00;"class="pointer fa fa-check btn btn-default finalizar"></span>';
		$btn_negar = '<span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;" class="pointer fa fa-ban btn btn-default negar"></span>';
		$btn_tramitar = '<span title="Tramitar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px" class="pointer fa fa-retweet btn btn-default tramitar"></span>';
		//$btn_cancelar = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default cancelar" style="color:#d9534f"></span>';
		$btn_abierta = '<span title="En proceso..." data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half" style="color:#428bca"></span>';
		$btn_duplicado = '<span title="Duplicado" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="pointer fa fa-pencil-square-o btn btn-default duplicado"></span>';
		$btn_proceso  = '<span title="En Proceso" data-toggle="popover" data-trigger="hover" style="color: #232f85" class="btn btn-default fa fa-tasks proceso"></span>';
		$btn_reanudar  = '<span title="En Proceso" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e" class="btn btn-default fa fa-tasks proceso"></span>';
		$btn_FAlcance = '<span title="Fuera de Alcance" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default FAlcance" style="color:#d9534f"></span>';
		$btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;" class="pointer fa fa-user btn btn-default asignar"></span>';
		$btn_solucionado = '<span title="Solucionado" data-toggle="popover" data-trigger="hover" style="color: #00cc00;"class="pointer fa fa-check-square btn btn-default solucionado"></span>';
		$btn_cerrado = '<span title="Cerrar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;"class="pointer fa fa-check btn btn-default cerrar"></span>';
		$btn_suspender = '<span title="Suspender" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;margin-left: 5px" class="pointer fa fa-pause btn btn-default suspender"></span>';
		$btn_escalar = '<span title="Escalar a Proveedor" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;" class="pointer fa fa-send btn btn-default escalar"></span>';
		$btn_esperarUsu = '<span title="En espera por Usuario" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="pointer fa fa-retweet btn btn-default esperati"></span>';            
		$btn_esperarSuper = '<span title="En espera por TI o Coordinador" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="pointer fa fa-retweet btn btn-default esperausu1"></span>';            
		$btn_esperarSuper = '<span title="En espera por TI o Coordinador" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="pointer fa fa-pencil-square-o btn btn-default esperausu"></span>';
		$btn_negar = '<span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px" class="pointer fa fa-minus-circle btn btn-default negar"></span>';

		$resp = array();
        foreach ($data as $row) {
			$item['ver'] = $ver_rojo;
			$item['accion'] = $btn_revisar;
			switch ($row['id_estado_ticket']) {
					// case ($_SESSION['perfil'] == 'Per_Sop'):
					case 'TIK_Regis':
						$row['ver'] = $ver_formulacion;
						$row['accion'] = ($admin ? $btn_negar.' '.$btn_anular.' '.$btn_asignar : ($soporte || $admin_soporte ? $btn_negar.' '.$btn_asignar: $btn_anular));
					break;
					case 'TIK_Anul':
						$row['ver'] = $ver_cancelada;
						$row['accion'] =  $btn_inhabil;
					break;
					case 'TIK_Negar':
						$row['ver'] = $ver_cancelada;
						$row['accion'] =  $btn_inhabil;
					break;
					case 'TIK_Asig':
						$row['ver'] = $ver_enviado;
						$row['accion'] = ($admin ? $btn_proceso : ($soporte ? $btn_proceso: $btn_inhabil));
					break;
					case 'TIK_Proce':
						$row['ver'] = $ver_aceptado;
						$row['accion'] = ($admin ? $btn_suspender.' '.$btn_solucionado : ($soporte ? $btn_suspender.' '.$btn_solucionado: $btn_inhabil));
					break;
					case 'TIK_Soluc':
						$row['ver'] = $ver_finilizada;
						$row['accion'] = $btn_inhabil;
					break;
					case 'TIK_Suspen':
						$row['ver'] = $ver_naranja;
						$row['accion'] = ($admin ? $btn_reanudar : ($soporte ? $btn_reanudar: $btn_inhabil));
					break;	
			}
					array_push($resp,$row);
		}
		echo json_encode($resp);
	}
	public function fecha_cierre(){
		$id = $this->input->post("id");
		$data = $this->Super_estado == true ? $this->tickets_model->fecha_cierre($id) : array();
        echo json_encode($data);
	}

	public function cancelar_datos() {
		if (!$this->Super_estado) {
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		}else{
			if (!$this->Super_elimina) {
				$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			}else{            
				$id = $this->input->post("id");
				$id_usuario_elimina = $_SESSION['persona']; 
				$id_estado_ticket = 'TIK_Canc';
				$data = ['estado' => 0, 
				'fecha_elimina' => date("Y-m-d H:i"),
				'id_usuario_elimina' => $id_usuario_elimina,
				'id_estado_ticket' => $id_estado_ticket];
				$query = $this->pages_model->modificar_datos($data, 'tickets_solicitudes' ,$id);
				$resp= ['mensaje'=>"El ticket fue cancelado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];	
				if ($query) {
					$data_estado = [
						'id_solicitud' => $id,
						'id_estado_ticket' => $id_estado_ticket ,
						'fecha_registro' => date("Y-m-d H:i"),
						'id_solicitante' => $_SESSION['persona'],
					];
					//echo json_encode($data_estado);
					//return $data_estado;
					$add = $this->pages_model->guardar_datos( $data_estado, "tickets_estados");
					if(!$add) $resp= ['mensaje'=>"Error al actualizar estado de del ticket, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
				}else $resp= ['mensaje'=>"Error al eliminar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
			}
	  }
	  echo json_encode($resp);
	}

	public function listar_funcionarios(){		
		  $id = $this->input->post('id');
		  $resp = $this->Super_estado ?  $this->tickets_model->listar_funcionario_id($id) : [];
		  echo json_encode($resp);
	}

	public function listar_historial_estados()
	{
		$resp = [];
		if (!$this->Super_estado == true) {
		  $resp = ['mensaje'=>"", 'tipo'=>"sin_session", 'titulo'=>""];
		} else {
		  $id = $this->input->post('id_solicitud');
		  $resp = $this->tickets_model->listar_historial_estados($id);
		}
		echo json_encode($resp);
	}

	public function cambiarEstado()
	{
		if(!$this->Super_estado){
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		}else{
			$id = $this->input->post("id");
			$id_estado_ticket = $this->input->post("estado");
			$descripcion = $this->input->post("mensaje");
			$id_usuario_registra = $_SESSION["persona"];
			$id_solicitante = $this->tickets_model->obtenerSolicitante($id);
			$data_fac = [
				'id_estado_ticket' => $id_estado_ticket,
			];
			$add = $this->tickets_model->modificar_datos($data_fac, 'tickets_solicitudes', $id);
					$data = [
						'id_solicitud' => $id,
						'id_estado_ticket' => $id_estado_ticket,
						'id_usuario_registra' => $id_usuario_registra,
						'descripcion' => $descripcion,
						"id_solicitante" => $id_solicitante->{'id_solicitante'},
					];
					$add = $this->genericas_model->guardar_datos($data, 'tickets_estados');

					$resp = ['mensaje' => "Proceso finalizado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
		} 
		echo json_encode($resp);
	}

	
	public function SolucionarTicket()
	{
		if(!$this->Super_estado){
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		}else{

			$id = $this->input->post("id_solicitud");
			$id_estado_ticket = "TIK_Soluc";
			$descripcion = $this->input->post("descripcion");
			$id_usuario_registra = $_SESSION["persona"];
			$id_solicitante = $this->tickets_model->obtenerSolicitante($id);
			$sw = true;			
			$data = [
				'id_solicitud' => $id,
				'id_estado_ticket' => $id_estado_ticket,
				'id_usuario_registra' => $id_usuario_registra,
				'descripcion' => $descripcion,
				"id_solicitante" => $id_solicitante->{'id_solicitante'},
			];

			if(!empty($_FILES["adj_evidencia"]["size"])){
				$nombre = $_FILES["adj_evidencia"]["name"]; 
				$file_evidence = $this->cargar_archivo("adj_evidencia", $this->ruta_evidencia, 'evidencia');
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
				$add = $this->genericas_model->guardar_datos($data, 'tickets_estados');
				if($add){
					$id = $this->input->post("id_solicitud");
					$data_fac = [
						'id_estado_ticket' => $id_estado_ticket,
						'file_name' => $adj_evi,
					];
					$mod = $this->tickets_model->modificar_datos($data_fac, 'tickets_solicitudes', $id);
					$resp = ['mensaje' => "Proceso finalizado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
					if($mod == 1) $resp = ['mensaje'=>"Error al actualizar el tickets, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
				}
				
			}
			$this->obtenerTiempoRestante($id);
		} 
		echo json_encode($resp);
	}

	public function asignar_especialista()
	{
		if(!$this->Super_estado){
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		}else{
			$id_solicitud = $this->input->post("id_solicitud");
			$id_persona = $this->input->post("id_persona");
			$id_estado_ticket = $this->input->post("estado");
			$id_especialista = $this->input->post("especialista");
			$id_subcategoria = $this->input->post("subcategoria");
			$id_categoria = $this->input->post("categoria");
			$id_usuario_registra = $_SESSION["persona"];
			$id_impacto = $this->input->post("impacto");
			$id_urgencia = $this->input->post("urgencia");
			$id_prioridad = $this->input->post("prioridad");
			$id_solicitante = $this->tickets_model->obtenerSolicitante($id_solicitud);
			$str = $this->verificar_campos_string(
				['Urgencia' => $id_urgencia,
				'Prioridad' => $id_prioridad,
				'Impacto' => $id_impacto,
				'Categoria' => $id_categoria,
				'Subcategoria' => $id_subcategoria,
				'Especialista' => $id_especialista]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					$data_fac = [
						'id_especialista' => $id_persona,
						'id_estado_ticket' => "TIK_Asig",
						'id_subcategoria' => $id_subcategoria,
						'id_categoria' => $id_categoria,
						'id_prioridad' => $id_prioridad,
						'id_urgencia' => $id_urgencia,
						'id_impacto' => $id_impacto,

					];
					$add = $this->tickets_model->modificar_datos($data_fac, 'tickets_solicitudes', $id_solicitud);
						$data = [
							'id_solicitud' => $id_solicitud,
							'id_estado_ticket' => "TIK_Asig",
							'id_usuario_registra' => $id_usuario_registra,
							'descripcion' => "Asignado a $id_especialista",
							"id_solicitante" => $id_solicitante->{'id_solicitante'},
						];
					$add = $this->genericas_model->guardar_datos($data, 'tickets_estados');
					$resp = ['mensaje' => "Se ha asignado un especialista satisfactoriamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
				}
		} 
		echo json_encode($resp);
	}
	public function buscar_especialista()
    {
        $personas = array();
        if ($this->Super_estado == true) {
			$dato = $this->input->post('dato');
			$tipo_solicitud_id = $this->input->post('tipo_solicitud_id');
			$id_estado_solicitud = $this->input->post('id_estado_solicitud');
			$id_nvl_impacto = $this->input->post('id_nvl_impacto');
			$id_nvl_urgencia = $this->input->post('id_nvl_urgencia');
			$id_nvl_prioridad = $this->input->post('id_nvl_prioridad');
			$rpb = $this->SumarHoras($id_nvl_impacto, $id_nvl_urgencia);
			$rsb = $this->SumarHoras($rpb, $id_nvl_prioridad);
			//$minutos = $this->convertir_minutos($rsb);
			$fecha_actual = time();
			$hoy = date("H:i:s", $fecha_actual);
            $personas = $this->tickets_model->buscar_especialista($dato, $tipo_solicitud_id, $id_estado_solicitud, $rsb, $hoy); 
		}
        echo json_encode($personas);
	}
	public function buscar_empleado()
    {
        $personas = array();
        if ($this->Super_estado == true) {
            $dato = $this->input->post('dato');
			if (!empty($dato)) $personas = $this->tickets_model->buscar_empleado($dato);
			 
		}
        echo json_encode($personas);
	}
	function obtener_permisos_parametros() {
		$parametro = $this->input->post('id');
		$permisos = $this->Super_estado == true ? $this->tickets_model->obtener_permisos_parametro($parametro) : array();
		echo json_encode($permisos);
	}
	function obtener_valory() {
		$parametro = $this->input->post('id');
		$permisos = $this->tickets_model->obtener_valory($parametro);
		echo json_encode($permisos);
	}
	public function listar_personas(){
		$texto = $this->input->post('texto');
		$data = $texto ? $this->tickets_model->listar_personas($texto) : [];
		echo json_encode($data);
	}
	public function listar_actividades(){
		$persona = $this->input->post('persona');
		$data = (isset($persona) && !empty($persona))
			? $this->tickets_model->listar_actividades($persona)
			: [];
		echo json_encode($data);
	}
	public function asignar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_agrega) {
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			$ok = $this->tickets_model->validar_asignacion_actividad($actividad, $persona);
			if ($ok) {
				$data = ['id_tipo'=>$actividad, 'id_persona'=>$persona, 'id_usuario_registro'=>$_SESSION['persona']];
				$resp = $this->tickets_model->guardar_datos($data, 'tickets_permisos_solicitudes');
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

	public function listar_valor_parametro(){
        $id_parametro = $this->input->post("idparametro"); 
        $data = $this->Super_estado == true ? $this->tickets_model->listar_valor_parametro($id_parametro) : array();
        $btn_config = '<span title="Asignar" data-toggle="popover" data-trigger="hover" style="color: #39b23b;margin-left: 5px" class="pointer fa fa-user btn btn-default asignar"></span>';
        $btn_modificar = '<span title="Modificar Servicio" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
        $btn_eliminar = '<span title="Eliminar Servicio" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
        $valores = array();
        foreach ($data as $row) {
            ($row['idparametro'] == 148) ? $row['accion'] = $btn_config : $row['accion'] = $btn_modificar .' '. $btn_eliminar; 
            array_push($valores,$row);
        }
        echo json_encode($valores);         
	}
	public function listar_estados(){
		if (!$this->Super_estado) $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$actividad = $this->input->post('actividad');
			$data = $this->tickets_model->listar_estados($actividad);
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
				$ok = $this->tickets_model->validar_asignacion_estado($estado, $actividad, $persona);
				if ($ok) {
					$data = [
						'id_estado' => $estado,
						'id_permiso_solicitud' => $actividad,
						'id_usuario_registro' => $_SESSION['persona']
					];
					$resp = $this->tickets_model->guardar_datos($data, 'tickets_permisos_estados');
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
	public function quitar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_modifica) {
			$estado = $this->input->post('estado');
			$id = $this->input->post('asignado');
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			// Verifico si actividad ya está asignada o no. Esta función retorna 0 si no está asignada la actividad y 1 si lo está.
			$ok = $this->tickets_model->validar_asignacion_actividad($actividad, $persona);
			if (!$ok) {
				$resp = $this->tickets_model->quitar_actividad($id);
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

	public function quitar_estado(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$id = $this->input->post('id');
				$ok = $this->tickets_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$resp = $this->tickets_model->quitar_estado($id);
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

	public function activar_notificacion(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
                $persona = $this->input->post('persona');
                $id = $this->input->post('id');
				$ok = $this->tickets_model->validar_asignacion_notificacion($estado, $actividad, $persona);
				if (!$ok) {
					$resp = $this->tickets_model->modificar_datos(['notificacion' => 1], 'tickets_permisos_estados', $id);
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
				$ok = $this->tickets_model->validar_asignacion_notificacion($estado, $actividad, $persona);
				if (!$ok) {
					$resp = $this->tickets_model->modificar_datos(['notificacion' => 0], 'tickets_permisos_estados', $id);
					$res = $resp == 1
						? ['mensaje'=>"Notificaciones desactivadas exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						: ['mensaje'=>"Ha ocurrido un error al desactivar las notificaciones.",'tipo'=>"info",'titulo'=> "Ooops!"];
				} else $res = ['mensaje'=>"El usuario no tiene asignado este estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
			}else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		}
		echo json_encode($res);
	}
	

	public function listar_horarios_funcionarios(){
        $data = $this->Super_estado == true ? $this->tickets_model->listar_horarios_funcionarios() : array();
        $btn_eliminar = '<span title="Eliminar Horario" data-toggle="popover" data-trigger="hover" style="color: #ca3e33;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
        $btn_modificar = '<span title="Modificar Horario" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
        $btn_config = '<span title="Asignar Funcionario" data-toggle="popover" data-trigger="hover" style="color: #39b23b;margin-left: 5px" class="pointer fa fa-user btn btn-default funcionario"></span>';
        $horarios = array();
        foreach ($data as $row) {
            $row['accion'] = $btn_config.' '.$btn_modificar.' '.$btn_eliminar;
            array_push($horarios,$row);
        }
        echo json_encode($horarios);
	}
	
	public function guardar_horario_funcionario(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_dia = $this->input->post('id_dia');
                $hora_inicio = $this->input->post('hora_inicio');
				$hora_fin = $this->input->post('hora_fin');
				$tiempo_break = $this->input->post('tiempo_break');
				$hora_break = $this->input->post('hora_break');
                $observacion = $this->input->post('descripcion');
                $id_horario = $this->input->post('id_horario');
                $id_usuario_registra = $_SESSION['persona'];                 
                $str = $this->verificar_campos_string(['Día'=>$id_dia, 'Hora Inicio'=>$hora_inicio, 'Hora Fin'=>$hora_fin, 'Hora Break'=>$hora_break, 'Tiempo Break' => $tiempo_break]);
                if(is_array($str)){
                    $resp = ['mensaje'=>"El campo ".$str['field']." no puede estar vacio.",'tipo'=>"info", 'titulo'=>"Oops.!"];
                }else{
                    if($id_horario){
                        $validar = $this->tickets_model->traer_ultima_solicitud($id_horario,'tickets_horario','id');
                        if(($validar->{'tiempo_break'} == $tiempo_break) && ($validar->{'hora_break'} == $hora_break) && ($validar->{'id_dia'} == $id_dia) &&($validar->{'hora_inicio'} == $hora_inicio) && ($validar->{'hora_fin'} == $hora_fin) && ($validar->{'observacion'} == $observacion)) {
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en el horario.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }else{
                            $data_horario = ['tiempo_break' => $tiempo_break, 'hora_break' => $hora_break, 'id_dia' => $id_dia,'hora_inicio' => $hora_inicio, 'hora_fin' => $hora_fin, 'observacion' => $observacion ];
                            $mod = $this->tickets_model->modificar_datos($data_horario, 'tickets_horario',$id_horario);
                            $resp = ['mensaje'=>"El horario fue gestionado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($mod == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];    
                        }
                    }else{
                        $data_horario = [
                            'id_dia' => $id_dia,
                            'hora_inicio' => $hora_inicio,
							'hora_fin' => $hora_fin,
							'hora_break' => $hora_break,
							'tiempo_break' => $tiempo_break,
                            'observacion' => $observacion,
                            'id_usuario_registra' => $id_usuario_registra ];
                        $add = $this->tickets_model->guardar_datos($data_horario, 'tickets_horario');
                        $resp = ['mensaje'=>"El horario fue guardado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }                
                }    
            }   
        }
        echo json_encode($resp); 
	}
	
	public function eliminar_horario_funcionario(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_elimina == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post("id");
                $fecha = date("Y-m-d H:i");
                $usuario = $_SESSION["persona"];
                $data = [
                    "id_usuario_elimina" => $usuario,
                    "fecha_elimina" => $fecha,
                    "estado" => 0,
                    ];
                $resp= ['mensaje'=>"El horario fue eliminado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                $del = $this->tickets_model->modificar_datos($data,'tickets_horario',$id);
                if($del != 0)$resp= ['mensaje'=>"Error al eliminar al horario, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        echo json_encode($resp);
	}
	
	public function listar_funcionarios_horarios(){
        $id_horario = $this->input->post("id_horario");
        $data = $this->Super_estado == true ? $this->tickets_model->listar_funcionarios_horarios($id_horario) : array();
        $btn_eliminar = '<span title="Eliminar Funcionario" data-toggle="popover" data-trigger="hover" style="color: #ca3e33;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
        $funcionarios = array();
        foreach ($data as $row) {
            $row['accion'] = $btn_eliminar;
            array_push($funcionarios,$row);
        }
        echo json_encode($funcionarios);
	}
	public function buscar_persona(){
        $personas = array();
        if ($this->Super_estado == true) {
            $dato = $this->input->post('dato');
            if(empty($dato))$personas = $this->tickets_model->buscar_persona($dato);  
        }else{
            $personas = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }
        echo json_encode($personas);
	}
	function get_nombre_dia($fecha){
        $fechats = strtotime($fecha);
     //lo devuelve en numero 0 domingo, 1 lunes,....
     switch (date('w', $fechats)){
         case 0: return "Dia_Dom"; break;
         case 1: return "Dia_Lun"; break;
         case 2: return "Dia_Mar"; break;
         case 3: return "Dia_Mie"; break;
         case 4: return "Dia_Jue"; break;
         case 5: return "Dia_Vie"; break;
         case 6: return "Dia_Sab"; break;
        }
	}
	
	public function guardar_funcionario_horario(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_persona = $this->input->post('id_persona');
                $id_horario = $this->input->post('id_horario');
                $id_usuario_registra = $_SESSION['persona'];
                $existe = $this->tickets_model->validar_funcionario_horario($id_persona,$id_horario);
                if($existe){
                    $resp = ['mensaje'=>"El funcionario ya se encuentra registrado.!",'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data = [
                        'id_horario' => $id_horario,
                        'id_persona	' => $id_persona,
                        'id_usuario_registra' => $id_usuario_registra];
                    $add = $this->tickets_model->guardar_datos($data, 'tickets_funcionarios_horarios');
                    $resp = ['mensaje'=>"El funcionario fue guardado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if($add == -1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp); 
	}
	
	public function eliminar_funcionario_horario(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_elimina == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post("id");
                $fecha = date("Y-m-d H:i");
                $usuario = $_SESSION["persona"];
                $data = [
                    "id_usuario_elimina" => $usuario,
                    "fecha_elimina" => $fecha,
                    "estado" => 0,
                    ];
                $resp= ['mensaje'=>"El funcionario fue eliminado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                $del = $this->tickets_model->modificar_datos($data,'tickets_funcionarios_horarios',$id);
                if($del != 0)$resp= ['mensaje'=>"Error al eliminar al funcionario, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        echo json_encode($resp);
	}
	public function obtener_valor_parametro()
  	{
    	$parametro = $this->input->post('id');
    	$valores = $this->Super_estado == true ? $this->tickets_model->obtener_valor_parametro($parametro) : array();
    	echo json_encode($valores);
	}
	

	public function es_habil($c_day, $sabados =''){
		$festivos = new festivos_colombia;
		$festivos->festivos(date("Y",strtotime($c_day)));
		$c_weekDay = (int) $this->getWeekDay($c_day);
		if ($c_weekDay == 0 || $festivos->esFestivo($c_day)) {
			return false;
		}else if($sabados){
			if($c_weekDay == 0) return false;
		}
		return true;
	}

	
	Public function getWeekDay($date){
		return date("w", strtotime($date));
	}
	Public function getWeekDay_($date){
		return date("l", strtotime($date));
	}

	Public function getDay($date){
		$timestamp = strtotime($date);
		return date('d', $timestamp);
	}

	Public function getMonth($date){
		$timestamp = strtotime($date);
		return date('m', $timestamp);
	}

	Public function getHour($date){
		$timestamp = strtotime($date);
		return date('H', $timestamp);
	}
	Public function getTime($date){
		$timestamp = strtotime($date);
		return date('H:i:s', $timestamp);
	}
	public function convertir_minutos($hora){
		$hour = new DateTime($hora);
		$hours = $hour->format('H');
		$minutes = $hour->format('i');

		$minutos = (($hours * 60) + $minutes);
		return $Outcome;
	}
	public function SumarHoras($timeOne, $TimeTwo){
		$hourTwo = new DateTime($TimeTwo);
		$HourOne = new DateTime($timeOne);
		//Se formatea los valores por separados
		$hours = $hourTwo->format('H');
		$minutes = $hourTwo->format('i');
		$seconds = $hourTwo->format('s');
		//Sumar Horas, Minutos y Segundos
		$result = $HourOne->modify('+' .$minutes.'minute');
		$result = $HourOne->modify('+' .$hours.'hours');
		$result = $HourOne->modify('+' .$seconds.'seconds');

		$Outcome = $result->format('H:i:s');
		return $Outcome;
	}
	public function obtenerTiempoRestante($id){
		$tiempoRestante = $this->tickets_model->obtenerTiempoRestante($id);
		$var1 = count($tiempoRestante);
		$horarios = $this->tickets_model->horario($id);
		$valores = $this->tickets_model->calificarTiempo();
		$dateUltProce = null;
		$dateFin = null;
		$datePriProce = null;
		#obtener tus estados

		#sacar la fecha de registro y la fecha de asignación
		$fechaRegistro = new DateTime($tiempoRestante[array_search('TIK_Regis', array_column($tiempoRestante, 'id_estado_ticket'))]['fecha_registro']);
		$fechaAsignacion = new DateTime($tiempoRestante[array_search('TIK_Asig', array_column($tiempoRestante, 'id_estado_ticket'))]['fecha_registro']);
		$dateRegistro =  $fechaRegistro->format('Y-m-d H:i:s');
		$dateAsignacion =  $fechaAsignacion->format('Y-m-d H:i:s');

		#sacar fecha de asignación y fecha del primer en proceso
		$fechaPrimerAsignacion =  $fechaAsignacion->format('Y-m-d H:i:s');
		if ($tiempoRestante[2]['id_estado_ticket'] == 'TIK_Proce') {
			$pri_proce = new DateTime($tiempoRestante[2]['fecha_registro']);
			$datePriProce = $pri_proce->format('Y-m-d H:i:s');
		}

		#sacar fecha de ultimo en proceso  y fecha de finalizado
		if ($tiempoRestante[$var1 - 2]['id_estado_ticket'] == 'TIK_Proce') {
			$ult_proce = new DateTime($tiempoRestante[$var1 - 2]['fecha_registro']);
			$dateUltProce = $ult_proce->format('Y-m-d H:i:s');
		}
		if ($tiempoRestante[$var1 - 1]['id_estado_ticket'] == 'TIK_Soluc') {
			$ult_soluc = new DateTime($tiempoRestante[$var1 - 1]['fecha_registro']);
			$dateFin = $ult_soluc->format('Y-m-d H:i:s');
		}
		# horas asigancion = calcularHoras(fecha_registro, fecha_asignacion)
		$tiempo_asignacion = $this->calcularHoras($dateRegistro, $dateAsignacion, $horarios);
		# horas ejucución = calcularHoras(fecha_asignacion, fecha_primer_proceso) + #calcularHoras(fecha_utlimo_proceso, fecha_finalizado)
		$calculo_soluc1 = $this->calcularHoras($fechaPrimerAsignacion, $datePriProce, $horarios);
		$calculo_soluc2 = $this->calcularHoras($dateUltProce, $dateFin, $horarios);
		$tiempo_solucionado = $this->SumarMinutos($calculo_soluc1, $calculo_soluc2);
		if($tiempo_solucionado && $tiempo_asignacion){
			$data = [
				"tiempo_solucionado" => $tiempo_solucionado,
				"tiempo_asignacion" => $tiempo_asignacion,
				"tiempo_solucion" => $valores[array_search('TIK_hour_serv', array_column($valores, 'id_aux'))]['valor'],
				"tiempo_asignado" => $valores[array_search('TIK_hour_asig', array_column($valores, 'id_aux'))]['valor'],
			];
		if($data){ $del = $this->tickets_model->modificar_datos($data,'tickets_solicitudes',$id);}
		}
	}

	public function calcularHoras($fecha_inicial, $fecha_final, $horario){
		$festivos = new festivos_colombia;
		# Se obtienen los festivos del año actual
		$festivos->festivos(date("Y"));
		# Se obtiene el dia de la semana del dia en que fue recibida la solicitud
		$dia_inicial = $this->getWeekDay_($fecha_inicial);

		# Se obtiene el dia siguiente
		$c_day = date('Y-m-d',strtotime($fecha_inicial));
		$strInicial = date('Y-m-d',strtotime($fecha_inicial));
		$strFinal = date('Y-m-d',strtotime($fecha_final));
		$aux = true;
		$horas_h = 0;
		$timeStart = null;
		$fecha_inicial_ = new DateTime($fecha_inicial);
		$fecha_final_ = new DateTime($fecha_final);
		$horas = [];
		while($aux){
			$dia = (int)$this->getWeekDay($c_day);
			if($this->es_habil($c_day)){
				if($strInicial == $strFinal){
					$strHoraInicial = $fecha_inicial_ ->format('H:i:s');
					$strHoraFinal = $fecha_final_ ->format('H:i:s');
					$h = $this->horasHorario($dia,$strHoraInicial,$strHoraFinal,$horario);;
					$horas_h = $this->CalcularMinutos([$h]);
				}else if($strInicial == $c_day && $strFinal != $c_day){
					$strHoraInicial = $fecha_inicial_ ->format('H:i:s');
					$h = $this->horasHorario($dia,$strHoraInicial,null,$horario);
					if($h) array_push($horas, $h);
					$horas_h = $this->CalcularMinutos($horas);
				}else if($strInicial != $c_day && $strFinal != $c_day){
					$h = $this->horasHorario($dia,null,null,$horario);
					if($h) array_push($horas, $h);
					$horas_h = $this->CalcularMinutos($horas);
				}else if($strFinal == $c_day){
					$strHoraFinal = $fecha_final_ ->format('H:i:s');
					$h = $this->horasHorario($dia,null,$strHoraFinal,$horario);
					if($h) array_push($horas, $h);
					$horas_h = $this->CalcularMinutos($horas);
				}
					
			}
			$c_day = date('Y-m-d',strtotime("$c_day + 1 days"));
			if ($c_day > $fecha_final){
				$aux = false;
			}
		}
		return $horas_h;
	}

	// public function sumarLasHoras($horas) {
	// 	$total = 0;
	// 	foreach ($horas as $h) {
	// 		$parts = explode(":", $h);
	// 		$total += $parts[2] + $parts[1]*60 + $parts[0]*3600;
	// 	}
	// 	return gmdate("H:i:s", $total);
	// }
	public function CalcularMinutos($minutos) {
		$arraySize = count($minutos);
		$total_minutes = 0;
		$divided_array = [];
		for ($i=0; $i <= $arraySize - 1; $i++) { 
			$divided_array = explode(":",$minutos[$i]);
			$total_minutes += (int)$divided_array[0]*60;
			$total_minutes += (int)$divided_array[1];
		}
		return $total_minutes;
	}
	public function SumarMinutos($res1, $res2) {
		$total_minutes = 0;
		$total_minutes = $res1 + $res2;
		return $total_minutes;
	}

	public function horasHorario($dia,$hora_inicio,$hora_fin, $horario){
		$time_start = new DateTime($hora_inicio);
		$time_end = new DateTime($hora_fin);
		for ($i=0; $i < count($horario); $i++) {
			if($horario[$i]['DayWeek'] == $dia ){
				$hib1 =  $horario[$i]['hora_inicio'];
				$hfb1 =  $horario[$i]['hora_break'];
				$fin_break =  $horario[$i]['tiempo_break'];
				$hfb2 =  $horario[$i]['hora_fin'];
				$hib2 = $this->SumarHoras($hfb1,$fin_break);
				//-----------------------------//
				$hib1_ =  new DateTime($horario[$i]['hora_inicio']);
				$hfb1_ =  new DateTime($horario[$i]['hora_break']);
				$hib2_ =  new DateTime($hib2);
				$hfb2_ =  new DateTime($horario[$i]['hora_fin']);
				if ($hora_inicio && !$hora_fin) {
					if (($hora_inicio >= $hib1 && $hora_inicio < $hfb1)) {
						#retornamos diferencia entre hora inicio y hora de fin bloque 1
						$diferencia_hib1 = $time_start->diff($hfb1_);
						$diferencia_hib2 = $hib2_->diff($hfb2_);
						$var_ = $diferencia_hib2->format('%H:%i:%s');
						$hb1 = $diferencia_hib1->format('%H:%i:%s');
						$horas = $this->SumarHoras($hb1, $var_);
						return $horas;
					}else if (($hora_inicio > $hib2 && $hora_inicio < $hfb2)) {
						#retornamos diferencia entre hora inicio y hora de fin bloque 2
						$calculate = $time_start->diff($hfb2_);
						$horas = $calculate->format('%H:%i:%s');
						return $horas;
					}
				}else if (!$hora_inicio && $hora_fin) {
					if (($hora_fin > $hib1 && $hora_fin < $hfb1)) {
						#retornamos diferencia entre hora de inicio bloque 1 y  hora fin
						$calculate = $hib1_->diff($time_end);
						$horas = $calculate->format('%H:%i:%s');
						return $horas;
					}else if (($hora_fin > $hib2 && $hora_fin < $hfb2)) {
						#retornamos diferencia entre hora inicio bloque 2 y hora de fin y sumamos las horas del bloque 1
						$diferencia_hib2 = $hib2_->diff($time_end);
						$diferencia_hib1 = $hib1_->diff($hfb1_);
						$var = $diferencia_hib2->format('%H:%i:%s');
						$var_ = $diferencia_hib1->format('%H:%i:%s');
						$calculate = $this->SumarHoras($var, $var_);
						//$horas = $calculate->format('%H:%i:%s');
						return $calculate;
					}
				}else if(!$hora_inicio && !$hora_fin){
					$start = $hib1_->diff($hfb1_);
					$end = $hib2_->diff($hfb2_);
					$varStart = $start->format('%H:%i:%s');
					$varEnd = $end->format('%H:%i:%s');
					$calculate = $this->SumarHoras($varStart, $varEnd);
					return $calculate;

				}else{
					if((($hora_inicio > $hib1 && $hora_inicio < $hfb1) && ($hora_fin > $hib1 && $hora_fin < $hfb1) || ($hora_inicio > $hib2 && $hora_inicio < $hfb2) && ($hora_fin > $hib2 && $hora_fin < $hfb2))){
					#retornar diferencia entre hora inicio y hora fin
						$calculate = $time_start->diff($time_end);
						$horas = $calculate->format('%H:%i:%s');
						return $horas;
					}else{
						$tiempo = 0;
						if (($hora_inicio > $hib1 && $hora_inicio < $hfb1)) {
							#sumamos a horas la  diferencia entre hora inicio y hora de fin bloque 1
							$calculate = $time_start->diff($time_end);
							$tiempo = $calculate->format('%H:%i:%s');
						}else if (($hora_inicio > $hib2 && $hora_inicio < $hfb2)) {
							#sumamos a horas laornamos diferencia entre hora inicio y hora de fin bloque 2
							$calculate = $time_start->diff($time_end);
							$tiempo = $calculate->format('%H:%i:%s');
						}
						if (($hora_fin > $hib1 && $hora_fin < $hfb1)) {
							#sumamos a horas la diferencia entre hora de inicio bloque 1 y  hora fin
							$calculate = $hib1_->diff($time_end);
							$tiempo = $calculate->format('%H:%i:%s');
						}else if (($hora_fin > $hib2 && $hora_fin < $hfb2)) {
							#sumamos a horas la diferencia entre hora inicio bloque 2 y hora de fin
							$calculate = $hib2_->diff($time_end);
							$tiempo = $calculate->format('%H:%i:%s');
						}
						return $tiempo;
					}
				}

			break;
			}
		}
	}
}
?>
