<?php

date_default_timezone_set('America/Bogota');
class mantenimiento_control extends CI_Controller
{

	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
	var $admin = false;
	var $super_admin = false;
	var $ruta_evidencia = "archivos_adjuntos/mantenimiento/evidencias/";

	const ESTADOS = ['solicitado' => 'Man_Sol', 'cancelado' => 'Man_Can', 'rechazado' => 'Man_Rec', 'ejecutado' => 'Man_Eje', 'finalizado' => 'Man_Fin', 'recibido' => 'Man_Rcbd', 'pausa' => 'Man_Pau'];

	public function __construct()
	{
		parent::__construct();
		$this->load->model('mantenimiento_model');
		$this->load->model('almacen_model');
		$this->load->model('genericas_model');
		$this->load->model('pages_model');
		include('application/libraries/festivos_colombia.php');
		session_start();
		if (isset($_SESSION["usuario"])) {
			$this->Super_estado = true;
			$this->Super_elimina = 1;
			$this->Super_modifica = 1;
			$this->Super_agrega = 1;
			if ($_SESSION['perfil'] == 'Per_Admin') {
				$this->super_admin = true;
				$this->admin = true;
			}
			if ($_SESSION['perfil'] == 'Per_Admin_Man') $this->admin = true;
		}
	}

	public function index($id = '')
	{
		$pages = $this->get_route();
		if ($this->Super_estado) {
			$datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $pages);
			if (!empty($datos_actividad)) {
				$pages = "mantenimiento";
				$data['js'] = "Mantenimiento";
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

	public function guardar_solicitud()
	{
		$descripcion = $this->input->post('descripcion_servicio');
		$ubicacion = $this->input->post('ubicacion');
		$telefono = $this->input->post('telefono');
		$persona = $this->input->post('persona');
		$fecha_inicio = $this->input->post('fecha_inicio_evento');
		$fecha_fin = $this->input->post('fecha_fin_evento');
		if (!empty($fecha_inicio) && !empty($fecha_fin)) {
			$participantes = $this->input->post('participantes');
			$f1 = $this->validateDate($fecha_inicio, 'Y-m-d H:i');
			$f2 = $this->validateDate($fecha_fin, 'Y-m-d H:i');
			if ($f1 && $f2) {
				$fecha_lim = date("Y-m-d", strtotime(date("Y-m-d") . "+ 2 days"));
				if (!$this->admin && $fecha_inicio < $fecha_lim) {
					echo json_encode(['mensaje' => 'La solicitud se debe realizar con dos dias hábiles de anticipación .', 'tipo' => 'info', 'titulo' => 'ooops!!']);
					return;
				}
				$data = ['descripcion' => $descripcion, 'ubicacion' => $ubicacion, 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'participantes' => $participantes];
			} else {
				echo json_encode(['mensaje' => 'Por favor digite fechas válidas de inicio y fín para el evento.', 'tipo' => 'info', 'titulo' => 'ooops!!']);
				return;
			}
		} else $data = ['descripcion' => $descripcion, 'ubicacion' => $ubicacion];
		$str = $this->verificar_campos_string($data);
		if (is_array($str)) {
			echo json_encode($str);
			return;
		}
		$num = $this->verificar_campos_numericos(['telefono' => $telefono]);
		if (is_array($num)) {
			echo json_encode($num);
			return;
		}
		if (isset($persona) && !empty($persona) && is_numeric($persona)) $data['solicitante_id'] = $persona;
		$data['telefono'] = $telefono;
		// $articulos = $this->input->post('articulos');
		$sw = false;
		// if ($articulos) {
		// 	foreach ($articulos as $articulo) {
		// 		$res = $this->almacen_model->get_existencia_articulo($articulo['id']);
		// 		if ($articulo['cantidad'] > $res) {
		// 			$info = ['mensaje'=>'Uno de los artículos no se encuentra disponible.', 'tipo'=>'info', 'titulo'=>'Ooops!'];
		// 			echo json_encode($info);
		// 			return;
		// 		}
		// 	}
		// }
		$resp = $this->mantenimiento_model->guardar_solicitud($data/*, $articulos*/);
		$info = "";
		if ($resp) {
			$info = ['mensaje' => 'Solicitud registrada exitosamente.', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!', 'id' => (int)$resp];
		} else {
			$info = ['mensaje' => 'Por favor comuníquese con el administrador del sistema.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
		}
		echo json_encode($info);
		return;
	}

	public function Listar_solicitudes()
	{
		$id = $this->input->post('id');
		$cat = $this->input->post('categoria');
		$dep = $this->input->post('departamento');
		$fecha_i = $this->input->post('fecha_inicio');
		$fecha_f = $this->input->post('fecha_fin');
		//Si id = x se cargarán las solicitudes pendientes por calificar
		$estado = $id == 'x' ? 'Man_Eje' : $this->input->post('estado');

		$solicitudes_almacen = array();
		$lim = 0;
		if ($this->Super_estado == false) {
			echo json_encode($solicitudes_almacen);
			return;
		}
		$date_i = (!empty($fecha_i)) ? $date_i = $this->validateMonth($fecha_i, 'Y-m') : false;
		$date_f = (!empty($fecha_f)) ? $date_f = $this->validateMonth($fecha_f, 'Y-m') : false;
		$datos = $this->mantenimiento_model->Listar_solicitudes($estado, $cat, $dep, $fecha_i, $fecha_f, $id);
		// $solicitudes_almacen['filtro'] = ($estado != '%%' || $cat != '%%' || $dep != '%%' || is_numeric($id))  ? 1 : 0;
		$bg = 'white';
		$color = 'black';
		if (count($datos) == 0) {
			$solicitudes_almacen['data'] = array();
			echo json_encode($solicitudes_almacen);
			return;
		}
		$btn_gestionar  = '<span title="Gestionar" data-toggle="popover" data-trigger="hover" style="color:#337ab7;" class="fa fa-calendar pointer btn btn-default gestionar"></span> ';
		$btn_rechazar   = '<span title="Rechazar" data-toggle="popover" data-trigger="hover" style="color:#d9534f;" class="fa fa-ban pointer btn btn-default denegar"><span class="oculto">-----</span></span> ';
		$btn_pausar     = '<span title="Pausar" data-toggle="popover" data-trigger="hover" style="color:#573F7D;" class="fa fa-hand-stop-o pointer btn btn-default pausar"></span>';
		$btn_cancelar   = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" style="color:#d9534f;" class="fa fa-close pointer btn btn-default cancelar"><span class="oculto">-----</span></span>';
		$btn_pausa      = '<span title="Solicitud en pausa" data-toggle="popover" data-trigger="hover" style="color:#573F7D;" class="fa fa-hourglass-half pointer btn"><span class="oculto">-----</span></span>';
		$btn_ejecutar   = '<span title="Gestionar" data-toggle="popover" data-trigger="hover" style="color:#5bc0de;" class="fa fa-exchange pointer btn btn-default ejecutar"></span>';
		$btn_en_proceso = '<span title="Solicitud en Proceso" data-toggle="popover" data-trigger="hover" style="color:#5bc0de;" class="fa fa-hourglass-half pointer btn"><span class="oculto">-----</span></span>';
		$btn_encuesta = '<span title="Solicitud Ejecutada" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;" class="fa fa-star pointer btn btn-default encuesta"></span> ';
		$btn_ok = "<span title='Gestionada a tiempo' data-toggle='popover' data-trigger='hover' style='color:#5cb85c;' class='fa fa-thumbs-up pointer btn'><span class='oculto'>-----</span></span>";
		$btn_bad = "<span title='Fuera de tiempo' data-toggle='popover' data-trigger='hover' style='color:#d9534f;' class='fa fa-thumbs-down pointer btn'><span class='oculto'>-----</span></span>";
		$btn_finalizado = '<span title="Proceso Finalizado" data-toggle="popover" data-trigger="hover" style="color: #000000;" class="fa fa-toggle-off pointer btn"><span class="oculto">-----</span></span>';
		$btn_cargar_evidencia = '<span title="En Formulación" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="pointer fa fa-pencil-square-o btn btn-default formulacion"></span>';
		foreach ($datos as $row) {
			$style = '';
			if ($row['state'] == SELF::ESTADOS['solicitado']) {
				$this->admin
					? $row['gestion'] = "$btn_gestionar $btn_pausar $btn_rechazar "
					: $row['gestion'] = $btn_cancelar;
			} else if ($row['state'] == SELF::ESTADOS['cancelado'] || $row['state'] == SELF::ESTADOS['rechazado']) {
				$style = "background-color: #d9534f;color: white;";
				$row['gestion'] = '<span title="Proceso ' . $row["estado"] . '" data-toggle="popover" data-trigger="hover" style="color: #d9534f;" class="fa fa-toggle-off pointer btn"><span class="oculto">-----</span></span> ';
			} else if ($row['state'] == SELF::ESTADOS['pausa']) {
				$style = "background-color: #573F7D;color: white;";
				$this->admin
					? $row['gestion'] = "$btn_gestionar $btn_rechazar"
					: $row['gestion'] = $btn_en_proceso;
			} elseif ($row['state'] == SELF::ESTADOS['recibido']) {
				$style = "background-color: #5bc0de;color: white;";
				$this->admin
					? $row['gestion'] = "$btn_ejecutar $btn_rechazar"
					: $row['gestion'] = $btn_en_proceso;
			} elseif ($row['state'] == SELF::ESTADOS['ejecutado']) {
				$style = "background-color: #5cb85c;color: white;";
				$row['gestion'] = '';
				if ($this->admin) {
					if (!$row['calificacion']) $row['gestion'] = $btn_encuesta;
					if (!empty($row['tiempo_habil']) && $row['tiempo_habil'] > 0) {
						$row['tiempo'] >= $row['tiempo_habil']
							? $row['gestion'] .= ''
							: $row['gestion'] .= '';
					} else 
					if ($row['tiempo_habil'] < 0) {
						$row['gestion'] = '<span title="Proceso Finalizado" data-toggle="popover" data-trigger="hover" style="color: #000000;" class="fa fa-toggle-off pointer btn"><span class="oculto">-----</span></span>';
					} else $row['gestion'] = $btn_finalizado;
				} else if ($this->admin) {
					$row['gestion'] = '<span title="Esperando Encuesta" data-toggle="popover" data-trigger="hover" style="color:#5bc0de;" class="fa fa-hourglass-half pointer btn"><span class="oculto">-----</span></span> ';
					if ($_SESSION['persona'] == $row['resp']) $lim++;
				} else {
					!$row['calificacion']
						? $row['gestion'] = $btn_encuesta
						: $row['gestion'] = $btn_finalizado;
				}
			}
			$row['ver'] = "<span style='width: 100%;$style' class='pointer form-control'><span>ver</span></span>";
			$solicitudes_almacen['data'][] = $row;
		}
		$solicitudes_almacen['lim'] = $lim > 0 ? 1 : 0;
		echo json_encode($solicitudes_almacen);
		return;
	}

	public function buscar_articulo()
	{
		if ($this->Super_estado == false) {
			echo json_encode([]);
			return;
		}
		$tipo_modulo = $this->input->post('tipo_modulo');
		$art = $this->input->post('art');
		$data = $this->mantenimiento_model->buscar_articulo($art, $tipo_modulo);
		echo json_encode($data);
		return;
	}

	/**
	 * Recibe un array con clave-valor con los campos a verificar. 
	 * En caso de que uno de los campos esté vacio retorna el error -2 y el nombre del campo respectivo.
	 * @param Array $array 
	 * @return Integer
	 */
	public function verificar_campos_string($array)
	{
		foreach ($array as $row) {
			if (empty($row) || ctype_space($row)) {
				return ['type' => -2, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
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

	public function articulos_solicitados()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id = $this->input->post('sol_id');
		$articulos = $this->mantenimiento_model->articulos_solicitados($id);
		echo json_encode($articulos);
		return;
	}

	public function cancelar_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id = $this->input->post('id');
		$solicitud = $this->mantenimiento_model->traer_solicitud($id);
		$data = [
			'estado_mtto' => 1
		];
		$add = $this->pages_model->modificar_datos($data, 'laboral_solicitudes', $solicitud->{'id_seguridad'});
		$resp = $this->mantenimiento_model->cambiar_estado_solicitud($id, 'Man_Can');
		if ($resp == 1 || $add == -1) {
			$info = ['mensaje' => 'Solicitud cancelada exitosamente.', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
		} else if ($resp == 0) {
			$info = ['mensaje' => 'No se puede cambiar el estado de la solicitud!', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		} else {
			$info = ['mensaje' => 'Por favor comuníquese con el administrador del sistema.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
		}
		echo json_encode($info);
		return;
	}

	public function rechazar_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id = $this->input->post('id');
		$solicitud = $this->mantenimiento_model->traer_solicitud($id);
		$data = [
			'estado_mtto' => 1
		];
		$add = $this->pages_model->modificar_datos($data, 'laboral_solicitudes', $solicitud->{'id_seguridad'});
		$obs = $this->input->post('obs');
		$resp = $this->mantenimiento_model->cambiar_estado_solicitud($id, 'Man_Rec', $obs);
		if ($resp == 1 || $add == -1) {
			$info = ['mensaje' => 'Solicitud denegada exitosamente.', 'tipo' => 'success', 'titulo' => 'Proceso exitoso!'];
		} else {
			$info = ['mensaje' => 'Por favor comuníquese con el administrador del sistema.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
		}
		echo json_encode($info);
		return;
	}

	public function traer_operarios()
	{
		if ($this->Super_estado == false) {
			echo json_encode([]);
			return;
		}
		$categoria = $this->input->post('categoria');
		if ($categoria == '') {
			echo json_encode([]);
			return;
		}
		$resp = $this->mantenimiento_model->traer_operarios($categoria);
		echo json_encode($resp);
		return;
	}

	public function gestionar_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$prioridad = $this->input->post('prioridad');
		$categoria = $this->input->post('categoria');
		$solicitud = $this->input->post('solicitud');
		$comentario = $this->input->post('comentario');
		$fecha_inicio = $this->input->post('fecha_inicio_servicio');
		$fecha_fin = $this->input->post('fecha_fin_servicio');
		$tiempo = $this->input->post('tiempo');
		if (!empty($fecha_inicio) && !empty($fecha_fin)) {
			$f_i = $this->validateDate($fecha_inicio, 'Y-m-d H:i');
			$f_f = $this->validateDate($fecha_fin, 'Y-m-d H:i');
			if (!$f_i || !$f_f) {
				echo json_encode(['mensaje' => 'Una de las fechas ingresadas no es válida.', 'tipo' => 'info', 'titulo' => 'Ooops!']);
				return;
			}
		} else if ((!empty($fecha_inicio) && empty($fecha_fin)) || (!empty($fecha_inicio) && !empty($fecha_fin))) {
			echo json_encode(['mensaje' => 'Una de las fechas no fue ingresada.', 'tipo' => 'info', 'titulo' => 'Ooops!']);
			return;
		}
		$num = $this->verificar_campos_numericos(['solicitud' => $solicitud]);
		$str = $this->verificar_campos_string(['prioridad' => $prioridad, 'categoria' => $categoria]);
		if (!is_array($num)) {
			if (!is_array($str)) {
				$tiempo_habil = $tiempo === 'tercero' ? -1 : $this->mantenimiento_model->get_tiempo_habil($categoria);
				$last_num = $this->mantenimiento_model->traer_ultimo_num_solicitud();
				$last_num ? $last_num++ : $last_num = 1;
				$res = $this->mantenimiento_model->gestionar_solicitud($solicitud, SELF::ESTADOS['recibido'], $categoria, $prioridad, $last_num, $comentario, $fecha_inicio, $fecha_fin, $tiempo_habil);
				if ($res == 1) {
					$operarios = $this->input->post('operarios');
					foreach ($operarios as $row) $data[] = ['persona_id' => $row['id'], 'solicitud_id' => $solicitud, 'usuario_asigna' => $_SESSION['persona']];
					$res = $this->mantenimiento_model->guardar_datos($data, 'operario_solicitud', 2);
					$res == 1
						? $resp = ['mensaje' => 'Solicitud gestionada exitosamente.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
						: $resp = ['mensaje' => 'Error al asignar los operarios a la solicitud.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
				} else $resp = ['mensaje' => 'No se puede cambiar el estado de la solicitud.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
			} else $resp = ['mensaje' => 'El campo ' . $str['field'] . ' no debe estar vacío.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		} else $resp = ['mensaje' => 'Por favor seleccione una solicitud.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		echo json_encode($resp);
		return;
	}

	public function traer_operarios_solicitud()
	{
		$id = $this->input->post('id');
		$operarios = $this->mantenimiento_model->traer_operarios_solicitud($id);
		echo json_encode($operarios);
		return;
	}

	public function retirar_operario()
	{
		$id = $this->input->post('id');
		$id_solicitud = $this->input->post('id_solicitud');
		$res = $this->mantenimiento_model->retirar_operario($id, $id_solicitud);
		$resp = $res == 1
			? ['mensaje' => 'Operario retirado exitosamente.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
			: ['mensaje' => 'Por favor comuníquese con el administrador del sistema.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
		echo json_encode($resp);
		return;
	}

	function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$fecha_actual = date($format);
		$d = DateTime::createFromFormat($format, $date);
		return ($d->format($format) < $fecha_actual) ? false : $d && $d->format($format) == $date;
	}

	function validateMonth($date, $format = 'Y-m')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d->format($format) == $date;
	}

	public function agregar_nuevo_operario()
	{
		$id = $this->input->post('id');
		$solicitud = $this->input->post('id_solicitud');
		$data = ['persona_id' => $id, 'solicitud_id' => $solicitud, 'usuario_asigna' => $_SESSION['persona']];
		$res = $this->mantenimiento_model->guardar_datos($data, 'operario_solicitud');
		$resp = $res == 1
			? ['mensaje' => 'Operario asignado exitosamente.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
			: ['mensaje' => 'Por favor comuníquese con el administrador del sistema.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
		echo json_encode($resp);
		return;
	}

	public function cambiar_prioridad()
	{
		$id = $this->input->post('id_solicitud');
		$prioridad = $this->input->post('prioridad');
		$data = ['persona_id' => $id, 'prioridad' => $prioridad, 'usuario_asigna' => $_SESSION['persona']];
		$res = $this->pages_model->modificar_datos(['prioridad' => $prioridad], 'solicitudes_mantenimiento', $id);
		$resp = $res == 1
			? ['mensaje' => 'Prioridad actualizada con exito.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
			: ['mensaje' => 'Por favor comuníquese con el administrador del sistema.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
		echo json_encode($resp);
		return;
	}

	public function calificar()
	{
		$info = '';
		$id = $this->input->post('id');
		// $sw = (int)$this->input->post('sw');
		$sw = 1;
		$rating = (int)$this->input->post('rating');
		$observacion = $this->input->post('observacion');
		if ($rating <= 3 && empty($observacion) && $sw != 0) {
			echo json_encode(['mensaje' => 'Por favor digite una observación para terminar la calificación de la solicitud!', 'tipo' => 'info', 'titulo' => 'Ooops!']);
			return;
		}
		// $image = $this->input->post('image');
		// $name_firma = $this->adjuntar_firma("image"); 
		$num = $this->verificar_campos_numericos(['calificacion' => $rating]);
		if (!is_array($num) || $sw == 0) {
			if ($sw == -1) {
				$res = $this->mantenimiento_model->calificar_solicitud($id, $rating, $observacion);
			} else if ($sw == 1) {
				$res = $this->mantenimiento_model->calificar_solicitud($id, $rating, $observacion/*, $name_firma*/);
			} else if ($sw == 0) {
				$res = $this->mantenimiento_model->calificar_solicitud($id, 0, $observacion/*, $name_firma*/);
			}

			$resp = $res === 1
				? ['mensaje' => 'Solicitud calificada exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
				: ['mensaje' => 'Por favor comuníquese con el administrador del sistema.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
		}
		echo json_encode($resp);
		return;
	}

	// public function adjuntar_firma($name){
	// 	if ( isset($_POST[$name]) && !empty($_POST[$name]) ) {    
	// 		$dataURL = $_POST[$name];  
	// 		$parts = explode(',', $dataURL);  
	// 		$data = $parts[1];  
	// 		$data = base64_decode($data);  
	// 		$file =  uniqid() . '.png';
	// 		$success = file_put_contents('archivos_adjuntos/almacen/firmas/'.$file, $data);
	// 		return $success ? $file : -3;
	// 	}
	// 	  return -2;
	// }

	public function get_categorias()
	{
		if ($this->Super_estado == false) {
			echo json_encode([]);
			return;
		}
		$categorias = $this->mantenimiento_model->get_categorias();
		echo json_encode($categorias);
		return;
	}

	// public function get_categorias(){
	// 	if ($this->Super_estado == false) {
	//         echo json_encode([]);
	//         return;
	// 	}
	// 	$categorias = $this->mantenimiento_model->get_categorias();
	// 	echo json_encode($categorias);
	// 	return;
	// }

	public function get_operarios()
	{
		if ($this->Super_estado == false) {
			echo json_encode([]);
			return;
		}
		$cat = $this->input->post('categoria_s');
		$operarios = $this->mantenimiento_model->get_operarios($cat);
		echo json_encode($operarios);
		return;
	}

	public function get_operarios_categoria()
	{
		if ($this->Super_estado == false) {
			echo json_encode([]);
			return;
		}
		$cat = $this->input->post('categoria_s');
		$operarios = $this->mantenimiento_model->get_operarios_categoria($cat);
		echo json_encode($operarios);
		return;
	}

	public function add_operario_categoria()
	{
		if ($this->Super_estado == false) {
			echo json_encode([]);
			return;
		}
		$id = $this->input->post('id');
		$cat = $this->input->post('categoria_s');
		$data = ['persona_id' => $id, 'categoria_id' => $cat];
		$existe = $this->mantenimiento_model->validar_operario($id, $cat);
		if (!$existe) {
			$res = $this->mantenimiento_model->guardar_datos($data, 'operario_categoria');
			$resp = $res
				? ['mensaje' => 'Operario asignado exitosamente.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
				: ['mensaje' => 'Ha ocurrido un problema al asignar al operario.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
		} else $resp = ['mensaje' => 'El operario ya está asignado a esta categoría.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		echo json_encode($resp);
		return;
	}

	public function quitar_operario()
	{
		if ($this->Super_estado == false) {
			echo json_encode([]);
			return;
		}
		$id = $this->input->post('id');
		$cat = $this->input->post('categoria_s');
		$data = ['persona_id' => $id, 'categoria_id' => $cat];
		$res = $this->mantenimiento_model->quitar_operario($id, $cat);
		$resp = $res
			? ['mensaje' => 'Operario retirado exitosamente.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
			: ['mensaje' => 'Ha ocurrido un problema al asignar al operario.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
		echo json_encode($resp);
		return;
	}

	public function verificar_cantidad()
	{
		$id = $this->input->post('id');
		$cant = $this->input->post('cantidad');
		$existencia = $this->almacen_model->get_existencia_articulo($id);
		echo json_encode(($existencia > $cant) ? 1 : 0);
		return;
	}

	public function get_route()
	{
		$pages = $_SERVER['REQUEST_URI'];
		$pos = strrpos($pages, "index.php/");
		$pages =  preg_replace('/[0-9]+/', '', substr($pages, $pos + 10, strlen($pages)));
		$cant = strlen($pages);
		if ($pages[$cant - 1] == '/') $pages = substr($pages, 0, -1);
		return $pages;
	}

	public function ejecutar_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id = $this->input->post('id');
		$solicitud = $this->mantenimiento_model->traer_solicitud($id);
		$data = [
			'estado_mtto' => 1
		];
		$add = $this->pages_model->modificar_datos($data, 'laboral_solicitudes', $solicitud->{'id_seguridad'});
		$num = $this->verificar_campos_numericos(['Solicitud' => $id]);
		if (!is_array($num)) {
			$resp = $this->mantenimiento_model->cambiar_estado_solicitud($id, SELF::ESTADOS['ejecutado']);
			$dias = $this->onTime($id);
			if ($resp == 1 || $add == 1) {
				$resp = ['mensaje' => 'Solicitud ejecutada exitosamente.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
				$this->mantenimiento_model->calificarTiempo($id, $dias);
			} else $resp = ['mensaje' => 'Ha ocurrido un error al ejecutar la solicitud.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
		} else $resp = ['mensaje' => 'Por favor seleccione una solicitud.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		echo json_encode($resp);
		return;
	}

	public function onTime($id)
	{
		$fechas = $this->mantenimiento_model->tiempo_ejecucion($id);
		#Se instancia el objeto festivos
		$festivos = new festivos_colombia;
		# Se obtienen los festivos del año actual
		$festivos->festivos(date("Y"));
		# Fecha de recibido de la solicitud
		$recibido = $fechas->{'f_recibido'};
		# Fecha de entregado de la solicitud
		$ejecutado = $fechas->{'f_ejecutado'};
		# Se obtiene el dia de la semana del dia en que fue recibida la solicitud
		$weekDay = (int)$this->getWeekDay($recibido);
		# Se obtiene el dia siguiente
		$c_day = date("Y-m-d", strtotime($recibido));
		$strEntregado = date("Y-m-d", strtotime($ejecutado));
		$strRecibido = date("Y-m-d", strtotime($recibido));
		$aux = true;
		$dias = 0;
		while ($aux) {
			if ($this->es_habil($c_day)) $dias++;
			$c_day = date("Y-m-d", strtotime("$c_day + 1 days"));
			if ($c_day >= $ejecutado) $aux = false;
		}
		return $dias;
	}

	public function es_habil($c_day)
	{
		$festivos = new festivos_colombia;
		$festivos->festivos(date("Y"));
		$c_weekDay = (int) $this->getWeekDay($c_day);
		return ($c_weekDay == 0 || $c_weekDay == 6 || $festivos->esFestivo($c_day)) ? false : true;
	}

	public function getWeekDay($date)
	{
		return date("w", strtotime($date));
	}

	public function getDay($date)
	{
		$timestamp = strtotime($date);
		return date('d', $timestamp);
	}

	public function getMonth($date)
	{
		$timestamp = strtotime($date);
		return date('m', $timestamp);
	}

	public function getHour($date)
	{
		$timestamp = strtotime($date);
		return date('H', $timestamp);
	}

	public function traer_historial_solicitud()
	{
		$id = $this->input->post('id');
		$estados = $this->mantenimiento_model->traer_historial_solicitud($id);
		echo json_encode($estados);
		return;
	}

	public function pausar_solicitud()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id = $this->input->post('id');
		$obs = $this->input->post('obs');
		$num = $this->verificar_campos_numericos(['Solicitud' => $id]);
		if (!is_array($num)) {
			$res = $this->mantenimiento_model->cambiar_estado_solicitud($id, SELF::ESTADOS['pausa']);
			$res = $res
				? ['mensaje' => 'Solicitud pausada exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
				: ['mensaje' => 'Ha ocurrido un error al cambiar el estado de la solicitud!', 'tipo' => 'error', 'titulo' => 'Ooops!'];
		} else $res = ['mensaje' => 'Por favor seleccione una solicitud.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		echo json_encode($res);
		return;
	}

	public function cargar_personas()
	{
		$opt = $this->input->post('opt');
		if (!$this->Super_estado || empty($opt)) {
			echo json_encode([]);
			return;
		}
		$personas = $this->mantenimiento_model->cargar_personas($opt);
		echo json_encode($personas);
		return;
	}

	public function listar_permisos_parametros()
	{
		$id_principal = $this->input->post("id_principal");
		$resp = $this->Super_estado == true ? $this->mantenimiento_model->listar_permisos_parametros($id_principal) : array();
		echo json_encode($resp);
	}

	public function traer_objetos()
	{
		if ($this->Super_estado == false) $resp = [];
		else {
			$id_ubicacion = $this->input->post("id_ubicacion");
			$resp = $this->mantenimiento_model->traer_objetos($id_ubicacion);
		}
		echo json_encode($resp);
	}

	public function guardar_sol_mantenimiento()
	{
		$resp = [];
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$nombre_mantenimiento = $this->input->post('nombre_mantenimiento');
		$periodicidad = $this->input->post('periodicidad');
		$lugar = $this->input->post('lugar');
		$ubicacion = $this->input->post('ubicacion');
		$numero_notificaciones = $this->input->post('numero_notificaciones');
		$mes_inicio_notificacion = $this->input->post('mes_inicio_notificacion');
		$dia_entre_notificacion = $this->input->post('dia_entre_notificacion');
		$observacion_mantenimiento = $this->input->post('observacion_mantenimiento');
		$tipo_mantenimiento = $this->input->post('tipo_mantenimiento');
		$objeto_asignado = $this->input->post('objeto');
		$id_solicitud_mantenimiento = $this->input->post('id_solicitud_mantenimiento');
		$tipo_de_registro = $this->input->post('tipo_de_registro');

		if ($tipo_mantenimiento == 'T_Matt_anual') {

			$data = [
				'nombre_mantenimiento' => $nombre_mantenimiento,
				'id_periodicidad' => $periodicidad,
				'numero_notificaciones' => $numero_notificaciones,
				'mes_inicio_notificacion' => $mes_inicio_notificacion,
				'dia_entre_notificacion' => $dia_entre_notificacion,
				'tipo_mantenimiento' => $tipo_mantenimiento,
				'id_usuario_registra' => $_SESSION['persona'],
				'observacion_mantenimiento' => $observacion_mantenimiento,
			];
		} else {
			$data = [
				'id_usuario_registra' => $_SESSION['persona'],
				'id_lugar' => $lugar,
				'id_ubicacion' => $ubicacion,
				'tipo_mantenimiento' => $tipo_mantenimiento,
				'observacion_mantenimiento' => $observacion_mantenimiento,
			];
		}
		
		if($tipo_de_registro == "guardar") {
			$add = $this->pages_model->guardar_datos($data, "solicitud_mantenimiento");
		}elseif($tipo_de_registro =="modificar") {
			$add = $this->pages_model->modificar_datos($data, "solicitud_mantenimiento",$id_solicitud_mantenimiento);
		}
		if ($resp == 1 || $add == 1) {
			$resp = ['mensaje' => 'Registro guarado exitosamente.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
			if ($tipo_mantenimiento == 'T_Matt_preventivo') {
				$info = $this->mantenimiento_model->traer_solicitud_id($_SESSION['persona'], 'solicitud_mantenimiento', 'id_usuario_registra');
				$data_objeto = [];
				foreach ($objeto_asignado as $row) {
					array_push($data_objeto, [
						'id_mantenimiento' => $info->{'id'},
						'id_objeto_mantenimiento' => $row['id'],
						'cantidad' => $row['cantidad'],
						'id_ubicacion' => $ubicacion,
						'usuario_registra' => $_SESSION['persona']
					]);
				}
				$add = $this->pages_model->guardar_datos($data_objeto, "objetos_mantenimiento", 2);
			}
		} else $resp = ['mensaje' => 'Ha ocurrido un error al ejecutar la solicitud, por favor contacte al administrador', 'tipo' => 'error', 'titulo' => 'Ooops!'];

		echo json_encode($resp);
	}

	//  MATENIMIENTO ANUALES

	public function get_solicitud_matenimiento()
	{
		$resp = [];
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$tipo_mantenimiento = $this->input->post('tipo_mantenimiento');
		// $btn_agregar_lugar = '<span title="Agregar lugar" data-toggle="popover" data-trigger="hover" style="color:#337ab7" class="btn btn-default fa fa-plus agregar_lugar"></span>';
		$mantenimiento = $this->mantenimiento_model->get_solicitud_matenimiento();
		foreach ($mantenimiento as $row) {
			if ($row['id_estado_lugar'] == 'Pro_Matto') {
				$btn_iniciar = '<span title="Gestionar Mantenimiento 1" data-toggle="popover" data-trigger="hover" style="color:#337ab7" class="btn btn-default fa fa-play gestionar_mantenimiento"></span>';
			} else if ($row['id_estado_lugar'] == 'Fin_Matto') {
				$btn_iniciar = '<span title="Iniciar Mantenimiento" data-toggle="popover" data-trigger="hover" style="color:#5cb85c" class="btn btn-default fa fa-play iniciar_mantenimiento"></span>';
			} else $btn_iniciar = '<span title="Iniciar Mantenimiento" data-toggle="popover" data-trigger="hover" style="color:#9B9B9B" class="btn btn-default fa fa-play iniciar_mantenimiento"></span>';
			$row['accion'] =  $row['cantidad_mantenimiento'] > 0  || $row['id_estado_lugar'] == 'Pro_Matto' || $row['id_estado_lugar'] == 'Fin_Matto'  ? $btn_iniciar . ' '  : $btn_iniciar;
			array_push($resp, $row);
		}
		echo json_encode($resp);
	}

	public function listar_periodo_mantenimiento()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$resp = $this->mantenimiento_model->listar_periodo_mantenimiento();
		echo json_encode($resp);
	}

	public function buscar_lugar_mantenimiento_periodico()
	{
		$resp = array();
		// El idparametro = 272 es utilizado para listar  parametros de meses del año en ambientes de Development
		// El idparametro = 346 es utilizado para listar  parametros de meses del año en ambientes de Productions
		if ($this->Super_estado) {
			$dato = $this->input->post('dato');
			$buscar = "(vp.valor LIKE '%" . $dato . "%' OR vp.id_aux LIKE '%" . $dato . "%') AND vp.estado=1 AND vp.idparametro = 115 OR vp.idparametro = 346";
			if (!empty($dato)) $resp = $this->mantenimiento_model->buscar_lugar_mantenimiento_periodico($buscar);
		}
		echo json_encode($resp);
	}

	public function listar_ubicaciones_mantenimiento_periodico()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id_lugar_periodico  = $this->input->post('id_lugar_periodico');
		$resp = $this->mantenimiento_model->listar_ubicaciones_mantenimiento_periodico($id_lugar_periodico);
		echo json_encode($resp);
	}

	// GUARDAR LUGARES MATENIMIENTOS PERIODICOS
	public function guardar_lugar_mantenimiento()
	{
		$resp = array();
		if ($this->Super_estado) {

			$id_mantenimiento_periodico = $this->input->post('id_mantenimiento_periodico');
			$id_historial_periodico = $this->mantenimiento_model->obtener_historial($id_mantenimiento_periodico);
			$id_lugar = $this->input->post('id_lugar');

			$num = $this->verificar_campos_numericos(['id_mantenimiento_periodico' => $id_mantenimiento_periodico, 'id_lugar' => $id_lugar]);

			if (is_array($num)) {
				$resp = ['mensaje' => 'Debe seleccionar ' . $num['field'], 'tipo' => 'info', 'titulo' => 'Ooops!'];
			} else {
				$id = $id_historial_periodico[0]['id'];
				// $buscar = "(lm.id_mantenimiento_periodico = '" . $id_mantenimiento_periodico . "' AND lm.id_lugar='" . $id_lugar . "') AND lm.estado=1";
				$buscar = "lm.id_mantenimiento_periodico = $id_mantenimiento_periodico AND lm.id_lugar = $id_lugar AND lm.id_historial_periodico = $id AND lm.estado = 1";

				$info = $this->mantenimiento_model->validar_lugar_mantenimiento($buscar);
				if (!$info) {
					$data = [
						'id_mantenimiento_periodico' => $id_mantenimiento_periodico,
						'id_lugar' => $id_lugar,
						'usuario_registra' => $_SESSION['persona'],
						'id_historial_periodico' => $id,
					];
					$resp = $this->mantenimiento_model->guardar_datos($data, 'mantenimiento_periodicos_lugares');
					$resp = $resp == 1
						? ['mensaje' => 'Objeto guardado exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
						: ['mensaje' => 'Ha ocurrido un error, contacte el administrador!', 'tipo' => 'error', 'titulo' => 'Ooops!'];
				} else $resp = ['mensaje' => 'Este lugar ya existe', 'tipo' => 'info', 'titulo' => 'Ooops!'];
			}
		}
		echo json_encode($resp);
	}

	public function validar_existencia_mantenimiento_periodico()
	{
		$id_mantenimiento_periodico = $this->input->post("id_mantenimiento_periodico");
		$datos = $this->Super_estado ? $this->mantenimiento_model->validar_existencia_mantenimiento_periodico($id_mantenimiento_periodico) : array();
		echo json_encode($datos);
	}

	// LISTAR LUGARES MANTENIMIENTO PERIODICO
	public function listar_lugares_mantenimientos_periodico()
	{
		$resp = [];
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id_mantenimiento_periodico = $this->input->post('id_mantenimiento_periodico');
		$id_historial_periodico = $this->mantenimiento_model->obtener_historial($id_mantenimiento_periodico);
		$tipo = $this->input->post('tipo');
		$btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color:#d9534f;" class="fa fa-trash pointer btn btn-default eliminar"></span>';
		$btn_cargar = '<span title="Cargar evidencia" data-toggle="popover" data-trigger="hover" style="color:#337ab7" class="btn btn-default fa fa-upload evidencia"></span>';
		$btn_estado_lugar = '<span title="Estado objeto mantenimiento" data-toggle="popover" data-trigger="hover" style="color:#337ab7" class="btn btn-default fa fa-pencil estado"></span>';
		if (!$id_historial_periodico) {
			echo json_encode($resp);
			return false;
		}
		$data = $this->mantenimiento_model->listar_lugares_mantenimientos_periodico($id_mantenimiento_periodico, $id_historial_periodico[0]['id']);
		foreach ($data as $row) {
			if ($tipo == "iniciar") {
				$row['accion'] = "$btn_eliminar $btn_cargar $btn_estado_lugar";
			} else {
				$row['accion'] = $btn_eliminar;
			}
			array_push($resp, $row);
		}
		echo json_encode($resp);
	}

	// GUARDAR ESTADO LUGARES

	public function guardar_estado_lugares_mantenimientos()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$estado = $this->input->post('estado');
			$id_mantenimiento_periodico = $this->input->post('id_mantenimiento_periodico');
			$id_mantenimiento_periodico_matto = $this->input->post('id_mantenimiento_periodico_matto');
			$resp = ['mensaje' => 'Objeto guardado exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];

			// $data = $this->mantenimiento_model->obtener_data_solicitud('mpl.id', 'mantenimiento_periodicos_lugares mpl', "mpl.id_mantenimiento_periodico = $id_mantenimiento_periodico AND mph.estado = 1", 1, 'mph.fecha_registra', 'DESC');
			// echo json_encode($data);
			// return false;
			$datos = [
				'tipo_mtto' => $estado,
			];

			$update = $this->pages_model->modificar_datos($datos, 'mantenimiento_periodicos_lugares', $id_mantenimiento_periodico_matto);
			if ($update == -1) $resp = ['mensaje' => 'Ha ocurrido un error, contacte el administrador!', 'tipo' => 'error', 'titulo' => 'Ooops!'];
		}
		echo json_encode($resp);
	}

	// GUARDAR EVIDENCIA MANTENIMIENTO PERIODICO

	public function guardar_evidencias_mantenimientos_periodicos()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$id_solicitud = $_POST['id_solicitud'];
			$comentario_evidencia = $_POST['comentario_evidencia'];
			$nombre = $_FILES["file"]["name"];
			$tabla = 'mantenimiento_periodico_evidencia';
		
			$cargo = $this->pages_model->cargar_archivo("file", $this->ruta_evidencia, "mantenimiento_periodico_");
			if ($cargo[0] == -1) {
				header("HTTP/1.0 400 Bad Request");
				echo ($nombre);
				return;
			}

			$data = [
				"id_mantenimiento" => $id_solicitud,
				"comentario" => $comentario_evidencia,
				"nombre_archivo" => $cargo[1],
				"id_usuario_registra" => $_SESSION['persona']
			];

			$res = $this->pages_model->guardar_datos($data, $tabla);
			if ($res == -1) {
				header("HTTP/1.0 400 Bad Request");
				echo ($nombre);
				return;
			}
			$resp = ['mensaje' => "Todos Los archivos fueron cargados.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];

		}
		echo json_encode($resp);
	}


	// FINALIZAR MANTENIMIENTO PERIODICO

	public function finalizar_solicitud_mantenimiento_periodico()
	{
		if ($this->Super_estado == false) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			if ($this->Super_modifica == 0) {
				$resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
			} else {
				$id_mantenimiento_periodico = $this->input->post("id_mantenimiento_periodico");
				$fecha_fin = date("Y-m-d H:i:s");

				$validar_adjuntos = $this->mantenimiento_model->validar_adjuntos_periodico($id_mantenimiento_periodico);
				// echo json_encode($validar_adjuntos);
				// return false;
				foreach ($validar_adjuntos as $adjuntos) {
					if ($adjuntos['cant_evidencias'] < 1 &&  $adjuntos['estado'] && $adjuntos['estado'] != "sin_reparaciones") {
						$resp = ['mensaje' => "Antes de finalizar la solicitud, es necesario que adjuntes por lo menos una evidencia por cada lugar en esta solicitud.", 'tipo' => "info", 'titulo' => "Oops.!"];
						echo json_encode($resp);
						return false;
					}
				}
				$traer_obj_sin_estados = $this->mantenimiento_model->listar_lugares_mantenimientos_periodico($id_mantenimiento_periodico, 'iniciar');
				$datos = [];
				foreach ($traer_obj_sin_estados as $sin_estados) {
					if (!$sin_estados['id_tipo_mtto']) {
						$datos = array(
							'id_lugar' => $sin_estados['id_lugar'],
							'id_mantenimiento_periodico' => $id_mantenimiento_periodico,
							'tipo_mtto' => 'sin_reparaciones',
							'usuario_registra' => $_SESSION['persona'],

						);
						$add = $this->mantenimiento_model->guardar_datos($datos, "mantenimiento_periodicos_lugares");
						if ($add != 1) {
							$resp = ['mensaje' => 'Error al asignar los estados a la solicitud.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
							echo json_encode($resp);
							return false;
						}
					}
				}

				$data = array(
					"fecha_fin" => $fecha_fin,
					"id_estado_lugar" => 'Fin_Matto',
				);
				$mod = $this->pages_model->modificar_datos($data, "mantenimiento_periodico_historial", $id_mantenimiento_periodico, 'id_mantenimiento_periodico');
				if ($mod) $resp = ['mensaje' => "El mantenimiento fue finalizado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
				else $resp = ['mensaje' => "Error al finalizar el mantenimiento, contacte con el administrador del sistema.", 'tipo' => "error", 'titulo' => "Oops.!"];
			}
		}
		echo json_encode($resp);
		return;
	}

	// LISTAR EVIDENCIAS MANTENIMIENTOS PERIODICOS
	public function evidencias_mantenimiento_periodico()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id = $this->input->post('id');
		// $id_historial_periodico = $this->input->post('id_historial_periodico');

		$resp = $this->mantenimiento_model->evidencias_mantenimiento_periodico($id);
		echo json_encode($resp);
	}

	// LISTAR LUGARES SOLICITUD MANTENIMIENTOS

	public function listar_lugares_detalles_mantenimiento_periodico()
	{
		$resp = [];
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id_mantenimiento = $this->input->post('id_mantenimiento');
		$id = $this->input->post('id');

		$resp = $this->mantenimiento_model->listar_lugares_mantenimientos_periodico($id_mantenimiento, $id);

		echo json_encode($resp);
	}


	public function listar_detalle_mantenimiento()
	{
		$resp = [];
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id  = $this->input->post('id');
		$id_periodico  = $this->input->post('id_periodico');
		$datos = $this->mantenimiento_model->listar_detalle_mantenimiento($id, $id_periodico);
		if (!empty($datos)) {
			$count = count($datos);
			foreach ($datos as $dato) {
				$cant = $count--;
				$dato['nombre'] = 'Mantenimiento ' . $cant;
				array_push($resp, $dato);
			}
		} else $resp = [];
		echo json_encode($resp);
	}

	// MANTENIMIENTOS PREVENTIVOS

	public function listar_lugares_mantenimiento()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		// $tipo_mantenimiento = $this->input->post('tipo_mantenimiento');
		$resp = $this->mantenimiento_model->listar_lugares_mantenimiento();
		echo json_encode($resp);
	}

	public function listar_ubiaciones_mantenimiento()
	{
		$resp = [];
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id_lugar = $this->input->post('id_lugar');
		$btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
		$data = $this->mantenimiento_model->listar_ubiaciones_mantenimiento($id_lugar);
		foreach ($data as $row) {
			if ($row['id_estado_matto'] == 'Pro_Matto') {
				$btn_iniciar = '<span title="Iniciar Mantenimiento" data-toggle="popover" data-trigger="hover" style="color:#337ab7" class="btn btn-default fa fa-play iniciar_mantenimiento"></span>';
			} else if ($row['id_estado_matto'] == 'Fin_Matto') {
				$btn_iniciar = '<span title="Iniciar Mantenimiento" data-toggle="popover" data-trigger="hover" style="color:#5cb85c" class="btn btn-default fa fa-play iniciar_mantenimiento"></span>';
			} else $btn_iniciar = '<span title="Iniciar Mantenimiento" data-toggle="popover" data-trigger="hover" style="color:#9B9B9B" class="btn btn-default fa fa-play iniciar_mantenimiento"></span>';
			$row['accion'] =  $row['cantidad_objetos'] > 0  || $row['id_estado_matto'] == 'Pro_Matto' || $row['id_estado_matto'] == 'Fin_Matto'  ? $btn_iniciar :  $btn_inhabil;
			array_push($resp, $row);
		}
		echo json_encode($resp);
	}

	public function listar_evidencia_matto_gest()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id_objeto = $this->input->post('id_objeto');
		$id_historial = $this->input->post('id_historial');

		$resp = $this->mantenimiento_model->listar_evidencia_matto_gest($id_objeto, $id_historial);
		echo json_encode($resp);
	}

	public function listar_objetos_matto()
	{
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id_ubicacion = $this->input->post('id_ubicacion');
		$resp = $this->mantenimiento_model->listar_objetos_matto($id_ubicacion);
		echo json_encode($resp);
	}

	public function listar_objetos_matto_gest()
	{
		$resp = [];
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}
		$id_ubicacion = $this->input->post('id_ubicacion');
		$id_lugar = $this->input->post('id_lugar');
		$id_historial = $this->input->post('id_historial');
		$estado = true;
		$objetos_sin_estado = [];
		$btn_cargar = '<span title="Cargar evidencia" data-toggle="popover" data-trigger="hover" style="color:#337ab7" class="btn btn-default fa fa-upload evidencia"></span>&nbsp';
		$btn_estado_objeto = '<span title="Estado objeto mantenimiento" data-toggle="popover" data-trigger="hover" style="color:#337ab7" class="btn btn-default fa fa-pencil estado"></span>&nbsp;';
		$btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
		$info = $this->mantenimiento_model->traer_solicitud_id($id_historial, 'mantenimiento_preventivo_historial', 'id');
		$estado = $info->{'id_estado_matto'} == "Fin_Matto" ? false : true;
		$traer_evidencia = $this->mantenimiento_model->consultar_evidencia($id_lugar, $id_ubicacion, $id_historial);

		$data = $this->mantenimiento_model->listar_objetos_matto_gest($id_ubicacion, $id_lugar, $id_historial);
		foreach ($data as $row) {
			if (!$row['estado_obj']) {
				$this->iniciar_mantenimiento($id_ubicacion, $id_lugar, $id_historial);
				return false;
			} else if ($row['estado_obj'] && $row['id_estado_matto'] != "Fin_Matto") {
				$row['accion'] = $btn_estado_objeto;
			} else if ($row['estado_obj'] && $row['id_estado_matto'] == "Fin_Matto") {
				$row['accion'] = '';
			}
			$row['accion'] .=  $row['id_estado_matto'] && $row['id_estado_matto'] == "Fin_Matto" ? $btn_inhabil :  $btn_cargar;
			array_push($resp, $row);
		}

		echo json_encode([$resp, $estado, count($traer_evidencia), $objetos_sin_estado]);
	}

	public function buscar_objetos_inspeccion_mantenimiento()
	// El idparametro = 267 es utilizado para listar  parametros de lugares en ambientes de Development
	// El idparametro = 342 es utilizado para listar  parametros de lugares en ambientes de Productions
	{
		$resp = array();
		if ($this->Super_estado) {
			$dato = $this->input->post('dato');
			$buscar = "(vp.valor LIKE '%" . $dato . "%' OR vp.id_aux LIKE '%" . $dato . "%') AND vp.estado=1 AND vp.idparametro = 342";
			if (!empty($dato)) $resp = $this->mantenimiento_model->buscar_objetos_inspeccion_mantenimiento($buscar);
		}
		echo json_encode($resp);
	}

	public function guardar_objeto_mantenimiento()
	{
		$resp = array();
		if ($this->Super_estado) {
			$id_lugar = $this->input->post('id_lugar');
			$id_ubicacion = $this->input->post('id_ubicacion');
			$id_objeto = $this->input->post('id_objeto');
			$num = $this->verificar_campos_numericos(['lugar' => $id_lugar, 'ubicacion' => $id_ubicacion, 'objeto' => $id_objeto]);
			if (is_array($num)) {
				$resp = ['mensaje' => 'Debe seleccionar ' . $num['field'], 'tipo' => 'info', 'titulo' => 'Ooops!'];
			} else {
				$buscar = "(om.id_objeto_mantenimiento = '" . $id_objeto . "' AND om.id_lugar='" . $id_lugar . "' AND om.id_ubicacion = '" . $id_ubicacion . "') AND om.estado=1";
				$info = $this->mantenimiento_model->validar_objeto_mtto($buscar);
				if (!$info) {
					$data = [
						'id_objeto_mantenimiento' => $id_objeto,
						'id_lugar' => $id_lugar,
						'id_ubicacion' => $id_ubicacion,
						'usuario_registra' => $_SESSION['persona']
					];
					$resp = $this->mantenimiento_model->guardar_datos($data, 'objetos_mantenimiento');
					$resp = $resp == 1
						? ['mensaje' => 'Objeto guardado exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
						: ['mensaje' => 'Ha ocurrido un error, contacte el administrador!', 'tipo' => 'error', 'titulo' => 'Ooops!'];
				} else $resp = ['mensaje' => 'El objeto ' . $info->{'valor'} . ' ya existe', 'tipo' => 'info', 'titulo' => 'Ooops!'];
			}
		}
		echo json_encode($resp);
	}

	public function guardar_objeto_valor_parametro()
	{
		$resp = array();
		if ($this->Super_estado) {
			$nombre_objeto_nuevo = $this->input->post('nombre_objeto_nuevo');
			$descripcion = $this->input->post('descripcion');
			if (empty($nombre_objeto_nuevo)) {
				$resp = ['mensaje' => 'El campo nombre no debe estar vacío :)', 'tipo' => 'info', 'titulo' => 'Ooops!'];
			} else {
				// El idparametro = 267 es utilizado para listar  parametros de lugares en ambientes de Development
				// El idparametro = 342 es utilizado para listar  parametros de lugares en ambientes de Productions
				$buscar = "(vp.valor='" . $nombre_objeto_nuevo . "') AND vp.estado=1 AND vp.idparametro = 342";
				$info = $this->mantenimiento_model->buscar_objetos_inspeccion_mantenimiento($buscar);
				if ($info) {
					$resp = ['mensaje' => 'El objeto ' . $nombre_objeto_nuevo, ' ya existe', 'tipo' => 'info', 'titulo' => 'Ooops!'];
				} else {
					// El idparametro = 267 es utilizado para listar  parametros de lugares en ambientes de Development
					// El idparametro = 342 es utilizado para listar  parametros de lugares en ambientes de Productions
					$data = [
						'idparametro' => 342,
						'valor' => $nombre_objeto_nuevo,
						'valorx' => $descripcion,
						'usuario_registra' => $_SESSION['persona']
					];
					$add = $this->mantenimiento_model->guardar_datos($data, 'valor_parametro');
					$resp = $add == 1
						? ['mensaje' => 'Objeto guardado exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
						: ['mensaje' => 'Ha ocurrido un error, contacte el administrador!', 'tipo' => 'error', 'titulo' => 'Ooops!'];
				}
			}
		}
		echo json_encode($resp);
	}

	public function recibir_archivos()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {

			$id_solicitud = $_POST['id_mantenimiento'];
			$nombre = $_FILES["file"]["name"];
			$fecha_matt = $_POST['fecha_mantenimiento'];
			$ruta = $this->ruta_evidencia;

			$archivo = $this->cargar_archivo("file", $ruta, "evidence_");
			if ($archivo[0] == -1) {
				header("HTTP/1.0 400 Bad Request");
				echo ($nombre);
				return;
			}
			$data = [
				"id_mantenimiento" => $id_solicitud,
				"nombre_archivo" => $archivo[1],
				"fecha_mantenimiento" => $fecha_matt,
				"id_usuario_registra" => $_SESSION['persona']
			];

			$res = $this->pages_model->guardar_datos($data, 'evidencias_mantenimiento');
			if ($res == -1) {
				header("HTTP/1.0 400 Bad Request");
				echo ($nombre);
				return;
			}
			$resp = ['mensaje' => "Todos Los archivos fueron cargados.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
		}
		echo json_encode($resp);
	}

	public function guardar_estado_objetos_mantenimiento()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$data = $this->input->post('data');
			$id_objeto = $this->input->post('id_objeto');
			$id_lugar = $this->input->post('id_lugar');
			$id_ubicacion = $this->input->post('id_ubicacion');
			$id_estado_matto = $this->input->post('id_estado_mtto');
			$estado_objeto = $this->genericas_model->obtener_valores_parametro_aux($data['estado_objetos_matto'], 269);
			$id_historial = $this->input->post('id_historial_mtto');

			$existe = $this->mantenimiento_model->existe_estado_objeto($id_objeto, $id_lugar, $id_ubicacion, $id_historial);

			$resp = ['mensaje' => 'Objeto guardado exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];

			if ($existe[0]['cantidad'] == 0) {
				$datos = [
					'id_estado_objeto' => $estado_objeto[0]['id'],
					'id_lugar' => $id_lugar,
					'id_ubicacion' => $id_ubicacion,
					'id_objeto_mantenimiento' => $id_objeto,
					'id_historial_preventivo' => $id_historial,
					'usuario_registra' => $_SESSION['persona']
				];

				$add = $this->pages_model->guardar_datos($datos, 'mantenimiento_estados_objetos');
				if ($add != 0) ['mensaje' => 'Ha ocurrido un error, contacte el administrador!', 'tipo' => 'error', 'titulo' => 'Ooops!'];
			} else if ($existe[0]['cantidad'] > 0) {

				$update = $this->pages_model->modificar_datos(['id_estado_objeto' => $estado_objeto[0]['id']], 'mantenimiento_estados_objetos', $existe[0]['id']);
				if ($update == -1) $resp = ['mensaje' => 'Ha ocurrido un error, contacte el administrador!', 'tipo' => 'error', 'titulo' => 'Ooops!'];
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

	public function listar_objetos_mantenimiento()
	{
		$id = $this->input->post("id");
		$resp = $this->Super_estado == true ? $this->mantenimiento_model->listar_valor_parametro($id) : array();
		echo json_encode($resp);
	}

	public function traer_lugares()
	{
		$resp = [];
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$btn_ver  = "<span style='width: 100%;' class='pointer form-control ver'><span>ver</span></span>";

			$datos = $this->mantenimiento_model->traer_lugares();

			foreach ($datos as $dato) {
				$dato['ver'] = $btn_ver;
				array_push($resp, $dato);
			}
		};
		echo json_encode($resp);
	}

	public function traer_ubicacion()
	{
		$resp = [];
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$id_lugar = $this->input->post('id_lugar');
			$btn_ver  = "<span style='width: 100%;' class='pointer form-control ver'><span>ver</span></span>";

			$datos = $this->mantenimiento_model->traer_ubicacion($id_lugar);

			foreach ($datos as $dato) {
				$dato['ver'] = $btn_ver;
				array_push($resp, $dato);
			}
		};
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
				$tabla = $this->input->post("tabla_bd");
				$usuario_elimina = $_SESSION['persona'];
				if (empty($id)) {
					$resp = ['mensaje' => "Error al cargar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
				} else {
					if ($tabla == 'valor_parametro') {
						$data = ['estado' => 0];
					} else {
						$data = ['fecha_elimina' => date("Y-m-d H:i:s"), 'usuario_elimina' => $usuario_elimina, 'estado' => 0,];
					}
					$query = $this->pages_model->modificar_datos($data, $tabla, $id);
					$resp = ['mensaje' => "Los datos fueron eliminados con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
					if ($query == -1) $resp = ['mensaje' => "Error al eliminar los datos, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
				}
			}
		}
		echo json_encode($resp);
	}

	public function guardar_evidencia_mtto()
	{
		if ($this->Super_estado == false) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			if ($this->Super_agrega == 0) {
				$resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
			} else {
				$usuario_registra = $_SESSION["persona"];
				$observacion_evidencia = $this->input->post('observacion_evidencia');
				$id_objeto = $this->input->post('id_objeto');
				$id_historial_preventivo = $this->input->post('id_historial_preventivo');
				$evidencia = isset($_POST['foto']) ? 'Evidencia' . uniqid() : 'objeto_evidencia.png';

				$data = array(
					"foto" => $evidencia,
					// "fecha_evidencia_mantenimiento"=> $fecha_evidencia_mantenimiento, // Por lo pronto no usaremos la fecha tomaremos la fecha regista
					"observacion_evidencia" => $observacion_evidencia,
					"id_objeto" => $id_objeto,
					"id_historial_preventivo" => $id_historial_preventivo,
					"usuario_registra" => $usuario_registra,
				);
				$add = $this->mantenimiento_model->guardar_datos($data, "mantenimiento_preventivo_evidencias");
				if ($add) {
					if (isset($_POST['foto'])) {
						$datos = base64_decode(preg_replace('/^[^,]*,/', '', $_POST['foto']));
						file_put_contents('archivos_adjuntos/mantenimiento/evidencias/' . $evidencia . '.png', $datos);
					}
					$resp = ['mensaje' => "La evidencia fue guardar de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
				} else $resp = ['mensaje' => "Error al guardar la evidencia, contacte con el administrador del sistema.", 'tipo' => "error", 'titulo' => "Oops.!"];
			}
		}

		echo json_encode($resp);
		return;
	}

	public function validar_existencia_mantenimiento()
	{
		$id_lugar = $this->input->post("id_lugar");
		$id_ubicacion = $this->input->post("id_ubicacion");
		$datos = $this->Super_estado ? $this->mantenimiento_model->validar_existencia_mantenimiento($id_lugar, $id_ubicacion) : array();
		echo json_encode($datos);
	}

	public function generar_mantenimiento()
	{
		if ($this->Super_estado == false) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			if ($this->Super_agrega == 0) {
				$resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
			} else {
				$usuario_registra = $_SESSION["persona"];
				$id_lugar = $this->input->post("id_lugar") ?  $this->input->post("id_lugar") : NULL;
				$id_ubicacion = $this->input->post("id_ubicacion");
				$tipo_sol = $this->input->post("tipo_sol");
				$fecha_inicio = date("Y-m-d H:i:s");

				$existe = $this->mantenimiento_model->validar_existencia_mantenimiento_periodico($id_ubicacion);
				if ($existe) {
					$resp = ['id_historial' => $existe->{'id'}, 'existe' => true];
					echo json_encode($resp);
					return false;
				}
				if ($tipo_sol == "periodico") {
					$data = array(
						"fecha_inicio" => $fecha_inicio,
						"id_mantenimiento_periodico" => $id_ubicacion,
						"usuario_registra" => $usuario_registra,
						"id_estado_lugar" => 'Pro_Matto'
					);
					$table = "mantenimiento_periodico_historial";
				} else {
					$data = array(
						"fecha_inicio" => $fecha_inicio,
						"id_lugar" => $id_lugar,
						"id_ubicacion" => $id_ubicacion,
						"usuario_registra" => $usuario_registra,
						"id_estado_matto" => 'Pro_Matto'
					);
					$table = "mantenimiento_preventivo_historial";
				}

				$add = $this->mantenimiento_model->guardar_datos($data, $table);
				if ($add) {
					$info = $this->mantenimiento_model->traer_solicitud_id($_SESSION['persona'], $table, 'usuario_registra');
					$data_estado = array(
						"id_historial" => $info->{'id'},
						"usuario_registra" => $usuario_registra,
						"id_estado" => 'Pro_Matto'
					);
					$add = $this->mantenimiento_model->guardar_datos($data_estado, "estado_mantenimiento_gestion");
					if ($add) {

						$id_ult_sol = $this->mantenimiento_model->obtener_data_solicitud("mph.id", "mantenimiento_periodico_historial mph", "mph.id_mantenimiento_periodico = $id_ubicacion AND mph.estado = 1 AND mph.id_estado_lugar = 'Fin_Matto'", '1', 'mph.id', "DESC");
						if ($id_ult_sol) {
							$lugares = $this->mantenimiento_model->obtener_ultimos_lugares($id_ubicacion, $id_ult_sol[0]['id']);

							foreach ($lugares as $lugar) {
								$date_to_save = array(
									'id_mantenimiento_periodico' => $id_ubicacion,
									'id_historial_periodico' => $info->{'id'},
									'usuario_registra' => $usuario_registra,
									'id_lugar' => $lugar['id_lugar'],
								);
								$add_lugar = $this->mantenimiento_model->guardar_datos($date_to_save, "mantenimiento_periodicos_lugares");
								# code...
							}
							if ($add_lugar) $resp = ['mensaje' => "El mantenimiento fue guardado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", "id_historial" => $info->{'id'}, 'existe' => false];
							else {
								$resp = ['mensaje' => "Error al guardar el mantenimiento, contacte con el administrador del sistema.", 'tipo' => "error", 'titulo' => "Oops.!"];
							}
						} else {
							$resp = ['mensaje' => "El mantenimiento fue guardado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", "id_historial" => $info->{'id'}, 'existe' => false];
						}
					}
				}
			}
		}
		echo json_encode($resp);
		return;
	}


	public function finalizar_solicitud_mantenimiento()
	{
		if ($this->Super_estado == false) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			if ($this->Super_modifica == 0) {
				$resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
			} else {
				$id_historial = $this->input->post("id_historial");
				$objetos_sin_estado = $this->input->post("objetos_sin_estado");
				$id_ubicacion = $this->input->post("id_ubicacion");
				$id_lugar = $this->input->post("id_lugar");
				$fecha_fin = date("Y-m-d H:i:s");

				$validar_adjuntos = $this->mantenimiento_model->validar_adjuntos($id_historial);
				foreach ($validar_adjuntos as $adjuntos) {
					if ($adjuntos['cant_evidencias'] < 2 &&  $adjuntos['estado'] != "Sin_Reparaciones") {
						$resp = ['mensaje' => "Antes de finalizar la solicitud, es necesario que adjuntes por lo menos dos evidencias por cada objeto en esta solicitud.", 'tipo' => "info", 'titulo' => "Oops.!"];
						echo json_encode($resp);
						return false;
					}
				}
				$traer_obj_sin_estados = $this->mantenimiento_model->iniciar_mantenimiento($id_ubicacion, $id_lugar, $id_historial);
				$datos = [];
				foreach ($traer_obj_sin_estados as $sin_estados) {
					if (!$sin_estados['estado_obj']) {
						$datos = array(
							'id_objeto_mantenimiento' => $sin_estados['id_objeto_mantenimiento'],
							'id_estado_objeto' => 120974,
							'id_historial_preventivo' => $id_historial,
							'id_ubicacion' => $id_ubicacion,
							'id_lugar' => $id_lugar,
							'usuario_registra' => $_SESSION['persona'],

						);
						$add = $this->mantenimiento_model->guardar_datos($datos, "mantenimiento_estados_objetos");
						if ($add != 1) {
							$resp = ['mensaje' => 'Error al asignar los operarios a la solicitud.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
							echo json_encode($resp);
							return false;
						}
					}
				}

				$data = array(
					"fecha_fin" => $fecha_fin,
					"id_estado_matto" => 'Fin_Matto',
					"usuario_finaliza" => $_SESSION['persona']
				);
				$add = $this->pages_model->modificar_datos($data, "mantenimiento_preventivo_historial", $id_historial);
				if ($add) {
					$data_estado = array(
						"id_historial" => $id_historial,
						"usuario_registra" => $_SESSION['persona'],
						"id_estado" => 'Fin_Matto'
					);
					$add = $this->mantenimiento_model->guardar_datos($data_estado, "estado_mantenimiento_gestion");
					if ($objetos_sin_estado && count($objetos_sin_estado) > 0) $add = $this->mantenimiento_model->guardar_datos($objetos_sin_estado, "mantenimiento_estados_objetos", 2);
					$resp = ['mensaje' => "El mantenimiento fue finalizado de forma exitosa.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
				} else $resp = ['mensaje' => "Error al finalizar el mantenimiento, contacte con el administrador del sistema.", 'tipo' => "error", 'titulo' => "Oops.!"];
			}
		}
		echo json_encode($resp);
		return;
	}

	public function listar_detalles_solictud_mantenimiento_periodico()
	{
		$resp = [];
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$id_mantenimiento_periodico = $this->input->post('id_mantenimiento_periodico');
			$btn_detalle  = "<a class='ver_evidencia' target='_blank'><span title='Ver evidencia' class='pointer form-control'><span style='color: #008BFF;' class='fa fa-eye'></span></span></a>";
			$btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
			$datos = $this->mantenimiento_model->listar_detalles_solictud_mantenimiento_periodico($id_mantenimiento_periodico);
			if (!empty($datos)) {
				$count = count($datos);
				foreach ($datos as $dato) {
					$cant = $count--;
					$dato['nombre'] = 'Mantenimiento ' . $cant;
					array_push($resp, $dato);
				}
			} else $resp = [];
		}
		echo json_encode($resp);
	}


	public function listar_detalles_solictud()
	{
		$resp = [];
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$id_lugar = $this->input->post('id_lugar');
			$id_ubicacion = $this->input->post('id_ubicacion');
			$btn_detalle  = "<a class='ver_evidencia' target='_blank'><span title='Ver evidencia' class='pointer form-control'><span style='color: #008BFF;' class='fa fa-eye'></span></span></a>";
			$btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
			$datos = $this->mantenimiento_model->listar_detalles_solictud($id_lugar, $id_ubicacion);
			if (!empty($datos)) {
				$count = count($datos);
				foreach ($datos as $dato) {
					$cant = $count--;
					$dato['nombre'] = 'Mantenimiento ' . $cant;
					array_push($resp, $dato);
				}
			} else $resp = [];
		}
		echo json_encode($resp);
	}

	public function listar_mantenimiento_periodico_filtro()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$id_lugar = $this->input->post('id_lugar');
			$id_periodicidad = $this->input->post('id_periodicidad');
			$id_estado = $this->input->post('estado');
			$id_tipo = $this->input->post('tipo');
			$fecha_inicio = $this->input->post('fecha_inicio');
			$fecha_fin = $this->input->post('fecha_fin');

			if (empty($id_lugar) && empty($id_periodicidad) && empty($id_estado) && empty($id_tipo) && empty($fecha_inicio) && empty($fecha_fin)) $resp = [];

			else $resp = $this->mantenimiento_model->listar_mantenimiento_periodico_filtro($id_lugar, $id_periodicidad, $id_estado, $id_tipo, $fecha_inicio, $fecha_fin);
		}
		echo json_encode($resp);
	}

	public function listar_mantenimiento_gestion_filtro()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		} else {
			$id_lugar = $this->input->post('id_lugar');
			$id_ubicacion = $this->input->post('id_ubicacion');
			$id_estado = $this->input->post('estado');
			$fecha_inicio = $this->input->post('fecha_inicio');
			$fecha_fin = $this->input->post('fecha_fin');
			$id_estado_objeto = $this->input->post('id_estado_objeto');
			$id_estado_obj = $id_estado_objeto ? $this->genericas_model->obtener_valores_parametro_aux($id_estado_objeto, 269)[0]['id'] : [];
			if (empty($id_lugar) && empty($id_ubicacion) && empty($id_estado) && empty($fecha_inicio) && empty($fecha_fin) && empty($id_estado_objeto)) $resp = [];
			else $resp = $this->mantenimiento_model->listar_mantenimiento_gestion_filtro($id_lugar, $id_ubicacion, $id_estado, $fecha_inicio, $fecha_fin, $id_estado_obj);
		}
		echo json_encode($resp);
	}

	public function iniciar_mantenimiento()
	{
		$resp = [];
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}

		$id_ubicacion = $this->input->post('id_ubicacion');
		$id_lugar = $this->input->post('id_lugar');
		$id_historial = $this->input->post('id_historial');
		$data = $this->mantenimiento_model->iniciar_mantenimiento($id_ubicacion, $id_lugar, $id_historial);

		$estado = true;
		$objetos_sin_estado = [];
		$btn_cargar = '<span title="Cargar evidencia" data-toggle="popover" data-trigger="hover" style="color:#337ab7" class="btn btn-default fa fa-upload evidencia"></span>&nbsp';
		$btn_estado_objeto = '<span title="Estado objeto mantenimiento" data-toggle="popover" data-trigger="hover" style="color:#337ab7" class="btn btn-default fa fa-pencil estado"></span>&nbsp;';
		$btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
		$btn_eliminar =	'<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color:#d9534f;" class="fa fa-trash pointer btn btn-default eliminar"></span>';
		$traer_evidencia = $this->mantenimiento_model->consultar_evidencia($id_lugar, $id_ubicacion, $id_historial);

		// echo json_encode($data);
		// return false;
		foreach ($data as $row) {
			$row['accion'] = "$btn_estado_objeto  $btn_cargar $btn_eliminar";
			array_push($resp, $row);
		}
		echo json_encode([$resp, $estado, count($traer_evidencia), $objetos_sin_estado]);
	}

	// public function iniciar_mantenimiento_periodico () {
	// 	$resp = [];
	// 	if ($this->Super_estado == false) {
	// 		echo json_encode(['mensaje'=>'','tipo'=>'sin_session','titulo'=> '']);
	// 		return;
	// 	}

	// 	$id_ubicacion = $this->input->post('id_ubicacion');

	// 	$data = $this->mantenimiento_model->iniciar_mantenimiento_periodico($id_ubicacion);

	// 	$estado = true;
	// 	$objetos_sin_estado = [];
	// 	$btn_cargar = '<span title="Cargar evidencia" data-toggle="popover" data-trigger="hover" style="color:#337ab7" class="btn btn-default fa fa-upload evidencia"></span>&nbsp';
	// 	$btn_estado_objeto = '<span title="Estado objeto mantenimiento" data-toggle="popover" data-trigger="hover" style="color:#337ab7" class="btn btn-default fa fa-pencil estado"></span>&nbsp;';
	// 	$btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
	// 	$btn_eliminar =	'<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color:#d9534f;" class="fa fa-trash pointer btn btn-default eliminar"></span>';
	// 	$traer_evidencia = $this->mantenimiento_model->consultar_evidencia($id_ubicacion, );

	// 	// echo json_encode($data);
	// 	// return false;
	// 	foreach ($data as $row){
	// 		$row['accion'] = "$btn_estado_objeto  $btn_cargar $btn_eliminar";
	// 		array_push($resp, $row);
	// 	}
	// 	echo json_encode([$resp, $estado, count($traer_evidencia), $objetos_sin_estado]);
	// }

	public function add_new_mant()
	{
		$resp = [];
		if ($this->Super_estado == false) {
			echo json_encode(['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => '']);
			return;
		}

		$id_lugar = $this->input->post('id_lugar');
		$id_periodo = $this->input->post('id_periodico');

		$data = $this->mantenimiento_model->validar_existencia($id_lugar, $id_periodo);

		if (!$id_lugar || !$id_periodo) {
			$resp = ['mensaje' => "Error al obtener información necesaria", 'tipo' => "error", 'titulo' => "Oops.!"];
			echo json_encode($resp);
			return false;
		} else if ($id_lugar && $id_periodo) {
			if ($data[0]['cantidad'] == 0 || $data[0]['estado_solicitud'] == 'Fin_Matto') {
				$datos = [
					'id_periodo' => $id_periodo,
					'id_lugar' => $id_lugar,
					'estado_solicitud' => "Pro_Matto",
					'id_usuario_registra' => $_SESSION['persona']
				];
				$add = $this->mantenimiento_model->guardar_datos($datos, 'mantenimiento_periodicos');

				if ($add == 1) {
					$resp = ['mensaje' => 'Proceso exitoso', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
				} else {
					$resp = ['mensaje' => 'Ha ocurrido un error por favor comuníquese con el administrador del sistema.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
				}
			} else {
				$resp = ['mensaje' => 'Ya existe una solicitud iniciada!.', 'tipo' => 'error', 'titulo' => 'Ha ocurrido un error!'];
			}
			echo json_encode($resp);
		}
	}
}
