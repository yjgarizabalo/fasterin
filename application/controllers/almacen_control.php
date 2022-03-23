
<?php

date_default_timezone_set('America/Bogota');
class almacen_control extends CI_Controller
{

	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
	var $admin = false;
	var $super_admin = false;
    public function __construct(){
			parent::__construct();
			$this->load->model('almacen_model');
			$this->load->model('genericas_model');
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
				if ($_SESSION['perfil'] == 'Per_Alm') $this->admin = true;
			}
  	}

    public function index($id = ''){
			$pages = $this->get_route();
      if ($this->Super_estado) {
				$datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $pages);
				if (!empty($datos_actividad)) {
					$pages = "almacen";
					$data['js'] = "Almacen";
					$data['id'] = $id;
					$data['actividad'] = $datos_actividad[0]["id_actividad"];
				}else{
					$pages = "sin_session";
					$data['js'] = "";
					$data['actividad'] = "Permisos";
        }
      }else{
				$pages = "inicio";
				$data['js'] = "";
				$data['actividad'] = "Ingresar";
			}
			$this->load->view('templates/header', $data);
			$this->load->view("pages/" . $pages);
			$this->load->view('templates/footer');
    }

    public function guardar_solicitud(){
			if ($this->Super_estado == false) {
				echo json_encode(array("sin_session"));
				return;
			} else {
				if ($this->Super_agrega == 0) {
						echo json_encode(array(-1302));
				} else {
					$tipo_modulo = $this->input->post("tipo_modulo");
					$data = json_decode(stripslashes($this->input->post("data")));
					$arts = Array();
					foreach ($data as $d) {
						$stock = $this->almacen_model->get_existencia_articulo($d->{'codigo'});
						$cant = $this->almacen_model->get_cantidad_solicitada($d->{'codigo'});
						if (($cant + $d->{'cantidad_art'}) > $stock) {
							echo json_encode(-1);
							return;
						}
					}
					$id = $this->almacen_model->guardar_solicitud($tipo_modulo);
					$resp = 0;
					if ($id > 0) {
						foreach ($data as $d) {
							array_push($arts, array(
								'id_solicitud'=>$id,
								'id_articulo'=>$d->{'codigo'},
								'cantidad'=>$d->{'cantidad_art'},
								'observacion'=>$d->{'observaciones'}
							));
						}
						$resp = $this->almacen_model->guardar_articulos($arts);
					}
					echo json_encode([$resp, $id]);
				}
			}
    }

    public function Listar_solicitudes(){
			$solicitudes_almacen = array();
			if ($this->Super_estado == false) {
				echo json_encode($solicitudes_almacen);
				return;
			}
			// Recibo los valores enviados desde js
			$estado = $this->input->post('estado');
			$mes = $this->input->post('mes');
			$id = $this->input->post('id');
			$tipo_modulo = $this->input->post('tipo_modulo');
			$admin = $_SESSION['perfil'] == 'Per_Admin' ? 1 : 0;
			$almacen = $_SESSION['perfil'] == 'Per_Alm' ? 1 : 0;
			
			$datos = $this->almacen_model->Listar_solicitudes($estado, $mes, $id, $tipo_modulo);
			$solicitudes_almacen['fil'] = ($estado != '%%' || $mes != '' || is_numeric($id)) ? 1 : 0;
			$i = 1;
			$bgcolor = '';
			$color = '';
			$cont = 0;
			foreach ($datos as $row) {
				$row["num"] = $i;
				$row['agregar'] = ((int)$row['resp'] == $_SESSION['persona'] || $admin) ? 1 : 0;
				if ($almacen) {
					if ($row['state'] == 'Alm_Rec') {
						$row["gestion"] = "
						<span style='color: #2E79E5;margin-left: 5px;' title='Entregar Solicitud' data-toggle='popover' data-trigger='hover' class='pointer fa fa-retweet btn btn-default gestionar'></span> 
						<span style='color: #DBAA04;' title='Mercancia en Almacen' data-toggle='popover' data-trigger='hover' class='pointer fa fa-archive btn btn-default en_almacen'></span> 
						<span style='color: #d9534f;' title='Denegar Solicitud' data-toggle='popover' data-trigger='hover' class='fa fa-ban pointer btn btn-default denegar'><div class='oculto'>Pendiente</div></span>";
						$bgcolor = 'white';
						$color = 'black';
					}else {
						if($row['state'] == 'Alm_Ent' && $row['resp'] == $_SESSION['persona']){
							$row["gestion"] = '<span style="color: #f0ad4e;margin: 5px;" class="fa fa-star btn btn-default calificar" title="Calificar Solicitud"></span>';
							$bgcolor = '#f0ad4e';
							$color = 'white';
						}else if($row['state'] == 'Alm_Ent'){
							$bgcolor = '#5cb85c';
							$color = 'white';
							$row["gestion"] = ($row['time'] <= 24)
								? '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn" style="color: #5cb85c;"><div class="oculto">Ok</div></span>'
								: '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn" style="color: #d9534f"><div class="oculto">Fuera de Tiempo</div></span>';
						}else{
							if ($row['state'] == 'Alm_Can' || $row['state'] == 'Alm_Den') {
								$bgcolor = '#d9534f';
								if ($row['state'] == 'Alm_Den') $row['obs'] = true;
								$row["gestion"] = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
							}else if ($row['state'] == 'Alm_Mer'){
								$bgcolor = '#F0AD4E';
								$row["gestion"] = ($row['time'] <= 24)
								? '<span title="En los tiempos" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn" style="color: #5cb85c;"></span><span style="color: #5CB85C;margin-left: 5px;" title="Entregar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-check btn btn-default finalizar"></span>'
								: '<span title="Fuera de tiempo" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn" style="color: #d9534f"></span>
								<span style="color: #5CB85C;margin-left: 5px;" title="Entregar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-check btn btn-default finalizar"></span>';
							}else{
								$bgcolor = '#5cb85c';
								$row["gestion"] = ($row['time'] <= 24)
									? '<span title="Sobre el tiempo" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn" style="color: #5cb85c;"><div class="oculto">Ok</div></span>'
									: '<span title="Bajo el tiempo" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn" style="color: #d9534f"><div class="oculto">Fuera de Tiempo</div></span>';
							}
							$color = 'white';
						}
					}
				}else if($admin){
					if ($row['state'] == 'Alm_Rec') {
						$row["gestion"] = "
						<span style='color: #2E79E5;margin-left: 5px;' title='Entregar Solicitud' data-toggle='popover' data-trigger='hover' class='pointer fa fa-retweet btn btn-default gestionar'></span>
						<span style='color: #DBAA04;' title='Mercancia en Almacen' data-toggle='popover' data-trigger='hover' class='pointer fa fa-archive btn btn-default en_almacen'></span> 
						<span style='color: #d9534f;' title='Denegar Solicitud' data-toggle='popover' data-trigger='hover' class='fa fa-ban pointer btn btn-default denegar'></span>
						<span style='color: #d9534f;margin-left: 5px' title='Cancelar Solicitud' data-toggle='popover' data-trigger='hover' class='pointer fa fa-close btn btn-default cancelar'><div class='oculto'>Pendiente</div></span>";
						$bgcolor = 'white';
						$color = 'black';
					}else if($row['state'] == 'Alm_Ent'){
						$bgcolor = '#5cb85c';
						$color = 'white';
						$row["gestion"] = ($row['time'] <= 24)
							? '<span title="Buena entrega" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn" style="color: #5cb85c;"></span><span style="color: #f0ad4e;margin: 5px;" class="fa fa-star btn btn-default calificar" title="Calificar Solicitud"><div class="oculto">Ok</div></span>'
							: '<span title="Mala entrega" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn" style="color: #d9534f"></span><span style="color: #f0ad4e;margin: 5px;" class="fa fa-star btn btn-default calificar" title="Calificar Solicitud"><div class="oculto">Fuera de Tiempo</div></span>';
						if ($_SESSION['persona'] == $row['resp']) $cont++;
					}else if ($row['state'] == 'Alm_Mer'){
						$bgcolor = '#F0AD4E';
						$color = 'white';
						$row["gestion"] = ($row['time'] <= 24)
						? '<span title="En los tiempos" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn" style="color: #5cb85c;"></span><span style="color: #5CB85C;margin-left: 5px;" title="Entregar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-check btn btn-default finalizar"></span>'
						: '<span title="Fuera de tiempo" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn" style="color: #d9534f"></span>
						<span style="color: #5CB85C;margin-left: 5px;" title="Entregar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-check btn btn-default finalizar"></span>';
					}else {
						if ($row['state'] == 'Alm_Can' || $row['state'] == 'Alm_Den') {
							$bgcolor = '#d9534f';
							$color = 'white';
							if ($row['state'] == 'Alm_Den') $row['obs'] = true;
							$row["gestion"] = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"><div class="oculto">N/A</div></span>';
						}else{
							$bgcolor = '#5cb85c';
							$row["gestion"] = ($row['time'] <= 24) 
								? '<span title="Sobre el tiempo" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn" style="color: #5cb85c;"><div class="oculto">Ok</div></span>'
								: '<span title="Bajo el tiempo" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn" style="color: #d9534f"><div class="oculto">Fuera de Tiempo</div></span>';
						}
						$color = 'white';
					}
					$row["ver"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: ' . $bgcolor . ';color: ' . $color . '; width: 100%;" class="pointer form-control"><span>ver</span></span>';
					$i++;
				}else{
					if ($row['state'] == 'Alm_Rec') {
						$row["gestion"] = "<span style='color: #d9534f;margin-left: 5px' title='Cancelar Solicitud' data-toggle='popover' data-trigger='hover' class='pointer fa fa-close btn btn-default cancelar'></span>";
						$bgcolor = 'white';
						$color = 'black';
					}else if($row['state'] == 'Alm_Ent'){
						if ($row['resp'] == $_SESSION['persona']) {
							$row["gestion"] = '<span style="color: #f0ad4e;margin-left: 5px" class="fa fa-star btn btn-default calificar" title="Calificar Solicitud"></span>';
							$bgcolor = 'f0ad4e';
							$color = 'white';
							$cont++;
						}
					}
					else if($row['state'] == 'Alm_Mer'){
						$bgcolor = 'f0ad4e';
						$color = 'white';
						$row["gestion"] = '<span title="En proceso" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half" style="color:#428bca"></span>';
					}else {
						$bgcolor = ($row['state'] == 'Alm_Can' || $row['state'] == 'Alm_Den') ? '#d9534f' : '#5cb85c';
						$row["gestion"] = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
						$color = 'white';
					}
				}
				$row["ver"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: ' . $bgcolor . ';color: ' . $color . '; width: 100%;" class="pointer form-control"><span>ver</span></span>';
				$solicitudes_almacen["data"][] = $row;
				$i++;
			}
			if ($admin) $cont = $this->almacen_model->solicitudes_sin_calificar($tipo_modulo);
			$limite = $this->genericas_model->obtener_valores_parametro_aux("Alm_Lim", 20);
			$limite = (empty($limite)) ? 1 : $limite[0]["valor"];
			$solicitudes_almacen['lim'] = (!$this->admin && $cont >= $limite) ? true : false;
      echo json_encode($solicitudes_almacen);
    }

	public function listar_historial(){
		$reg = array();
		if ($this->Super_estado == false) {
				echo json_encode($reg);
				return;
		}
		$id = $this->input->post('art');
		if (ctype_space($id) || empty($id) || !is_numeric($id)) {
			echo json_encode($reg);
			return; 
		}
		$datos = $this->almacen_model->listar_historial($id);
		foreach ($datos as $row) {
				$reg["data"][] = $row;
		}
		echo json_encode($reg);
	}

	public function historial_estados(){
		$reg = array();
		if ($this->Super_estado == false) {
				echo json_encode($reg);
				return;
		}
		$id = $this->input->post('id');
		if (ctype_space($id) || empty($id) || !is_numeric($id)) {
			echo json_encode($reg);
			return; 
		}
		$datos = $this->almacen_model->historial_estados($id);
		$i = 1;
		foreach ($datos as $row) {
			$row["num"] = $i;
			$reg["data"][] = $row;
			$i++;
		}
		echo json_encode($reg);
	}

	public function traer_articulos_solicitud(){
		$articulos = Array();
		if ($this->Super_estado == false) {
				echo json_encode($articulos);
				return;
		}
		$id = $this->input->post('id');
		$articulos = $this->almacen_model->Listar_articulos_solicitud($id);
		$info = $this->almacen_model->get_estado_solicitud($id);
		foreach ($articulos as $row) {
			$row['gestion'] = ($info->{'state'} == 'Alm_Rec' && $_SESSION['persona'] == $info->{'user'})
				? "<span style='color:#d9534f' title='Eliminar Artículo' data-toggle='popover' data-trigger='hover' class='btn btn-default pointer fa fa-trash-o' onclick='eliminar_articulo(". $row['id'] .")'></span>". ' ' . "<span style='color: #2E79E5;' title='Modificar Artículo' data-toggle='popover' data-trigger='hover' class='btn btn-default fa fa-wrench pointer' onclick='traer_articulo_solicitud(" . json_encode($row) . ")'></span>"
				: '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
			$row["ver"] = "<span title='Mas Información' data-toggle='popover' data-trigger='hover' style='width: 100%;color: black;' class='pointer form-control'>ver</span>";
			$articulos["data"][] = $row;
		}
		echo json_encode($articulos);
	}

    public function traer_solicitud(){
			if ($this->Super_estado == false) {
				echo json_encode(array());
				return;
			}
			if ($this->Super_agrega == 0) {
					echo json_encode(array(-1302));
			} else {
				$idSolicitud = $this->input->post("id");
				$solicitud = $this->almacen_model->Traer_solicitud($idSolicitud);
				echo json_encode($solicitud);
			}
    }

	public function traer_info_solicitud(){
		if ($this->Super_estado == false) {
			echo json_encode(array());
			return;
		}
		if ($this->Super_agrega == 0) {
			echo json_encode(array(-1302));
		} else {
			$id = $this->input->post("id");
			$data = $this->almacen_model->Traer_info_solicitud($id);
			echo json_encode($data);
		}
	}
	
	public function traer_info_articulo(){
		if ($this->Super_estado == false) {
			echo json_encode(array());
			return;
		}
		if ($this->Super_agrega == 0) {
			echo json_encode(array(-1302));
		} else {
			$id = $this->input->post("id");
			$data = $this->almacen_model->Traer_info_articulo($id);
			echo json_encode($data);
		}
	}

    //Función Modificar Solicitud
    public function modificar_solicitud(){
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(-1302);
			return;
		}
		$id = $this->input->post("id_solicitud");
		$nombre = $this->input->post("mod_nombre_solicitud");
		$departamento = $this->input->post("cbxmod_departamento");
		$observaciones = $this->input->post("mod_txtobservaciones");
		$estado = $this->input->post("cbxmod_estado");
		if (ctype_space($nombre) || empty($nombre)) {
				echo json_encode(-1);
				return;
		}
		if (ctype_space($estado) || empty($estado) || !is_numeric($estado)) {
				echo json_encode(-2);
				return;
		}
		if (ctype_space($departamento) || empty($departamento) || !is_numeric($departamento)) {
				echo json_encode(-3);
				return;
		}
		$resp = $this->almacen_model->Modificar_solicitud($id, $nombre, $departamento, $observaciones, $estado);
		echo json_encode($resp);
	}
	
	public function buscar_articulo(){
		$articulos = array();
		if ($this->Super_estado == false) {
				echo json_encode($articulos);
				return;
		}
		$articulo = $this->input->post("art");
		$tipo_modulo = $this->input->post("tipo_modulo");
		$datos = $this->almacen_model->buscar_articulo($articulo, $tipo_modulo);
		$i = 1;
        foreach ($datos as $row) {
			$row['num'] = $i;
			$articulos["data"][] = $row;
			$i++;
        }
        echo json_encode($articulos);
	}

	public function agregar_cant_articulo(){
		if ($this->Super_estado == false) {
				echo json_encode("sin_session");
				return;
		}
		if ($this->Super_modifica == 0) {
				echo json_encode(-1302);
				return;
		}
		$articulo = $this->input->post("id");
		$cantidad = $this->input->post("cant");
		$resp = $this->almacen_model->agregar_cant_articulo($articulo, $cantidad);
		echo json_encode($resp);
	}

	public function entregar_solicitud(){
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(-1302);
			return;
		}
		$id = $this->input->post("id");
		if (ctype_space($id) || empty($id) || !is_numeric($id)) {
			echo json_encode(-1);
			return;
		}
		$usuario = $this->input->post("usuario");
		if (ctype_space($usuario) || empty($usuario)) {
			echo json_encode($usuario);
			return;
		}
		$name_firma = $this->adjuntar_firma("image"); 
		if ($name_firma == -3 || $name_firma == -2) {
			echo json_encode(-4);
			return;
		}

		$articulos = Array();
		$art = $this->almacen_model->Listar_articulos_solicitud($id);
		foreach ($art as $row) {
			$cant = $this->almacen_model->get_existencia_articulo($row['codigo']);
			if ($cant < $row['cantidad']) {
				echo json_encode(-2);
				return;
			}
		}
		foreach ($art as $row) {
			$this->almacen_model->descontar_cantidad($row['codigo'], $row['cantidad']);
		}
		
		$resp = $this->almacen_model->cambiar_estado_solicitud($id, 'Alm_Ent', $name_firma);
		$this->almacen_model->persona_entrega($id, $usuario);
		if ($resp == 1) {
			$onTime = $this->onTime('Alm_Ent');
		}
		echo json_encode($resp);
		return;
	}

	public function onTime($estado){
		// Se importa libreria festivos_colombia
		$id = $this->input->post("id");
		if (ctype_space($id) || empty($id) || !is_numeric($id)) {
			echo json_encode(-1);
			return;
		}
		$fechas = $this->almacen_model->onTime($id, $estado);
		#Se instancia el objeto festivos
		$festivos = new festivos_colombia;
		# Se obtienen los festivos del año actual
		$festivos->festivos(date("Y"));
		# Fecha de recibido de la solicitud
		$recibido = $fechas[0]['f_recibido'];
		# Fecha de entregado de la solicitud
		$entregado = $fechas[0]['f_entregado'];
		# Se obtiene el dia de la semana del dia en que fue recibida la solicitud
		$weekDay = (int)$this->getWeekDay($recibido);
		# Diferencia de horas total
		$diferencia = (int)$fechas[0]['diff_h'];
		# Se obtiene el dia siguiente
		$c_day = date("Y-m-d",strtotime($recibido));
		$strEntregado = date("Y-m-d",strtotime($entregado));
		$strRecibido = date("Y-m-d",strtotime($recibido));
		$aux = true;
		$horas_h = 0;
		while($aux){
			$c_weekDay = (int)$this->getWeekDay($c_day);
			if ($strRecibido == $c_day && $this->es_habil($c_day)) {
				$horas_h += (24 - (int)$this->getHour($recibido));
			}else if ($strEntregado == $c_day && $this->es_habil($c_day)) {
				$horas_h += (int) $this->getHour($entregado);
			}else if ($this->es_habil($c_day)) {
				$horas_h +=24;
			}
			$c_day = date("Y-m-d",strtotime("$c_day + 1 days"));
			if ($c_day >= $entregado){
				$aux = false;
			}
		}
		$this->almacen_model->calificarTiempo($id, $diferencia);
		return $horas_h;
	}

	public function es_habil($c_day){
		$festivos = new festivos_colombia;
		$festivos->festivos(date("Y"));
		$c_weekDay = (int) $this->getWeekDay($c_day);
		if ($c_weekDay == 0 || $c_weekDay == 6 || $festivos->esFestivo($c_day)) {
			return false;
		}
		return true;
	}

	Public function getWeekDay($date){
		return date("w", strtotime($date));
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

	public function cancelar_solicitud() {
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(-1302);
			return;
		}
		$id = $this->input->post("id");
		if (ctype_space($id) || empty($id) || !is_numeric($id)) {
			echo json_encode(-1);
			return;
		}
		$resp = $this->almacen_model->cambiar_estado_solicitud($id, 'Alm_Can');
		echo json_encode($resp);
	}

	public function calificar_solicitud(){
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(-1302);
			return;
		}
		$id = $this->input->post("id");
		if (ctype_space($id) || empty($id) || !is_numeric($id)) {
			echo json_encode(-1);
			return;
		}
		$rating = $this->input->post("rating");
		if (ctype_space($rating) || empty($rating) || !is_numeric($rating)) {
			echo json_encode(-2);
			return;
		}
		$observacion = $this->input->post("observacion");
		$resp = $this->almacen_model->calificar_solicitud($id, $rating, $observacion);
		echo json_encode($resp);
		return;
	}

	public function eliminar_articulo(){
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(-1302);
			return;
		}
		$solicitud = $this->input->post("solicitud");
		$info = $this->almacen_model->get_estado_solicitud($solicitud);
		$estado = $info->{'state'};
		if ($estado != 'Alm_Rec') {
			echo json_encode(-1);
			return;
		}
		$id = $this->input->post("id");
		if (ctype_space($id) || empty($id) || !is_numeric($id)) {
			echo json_encode(-2);
			return;
		}
		$resp = $this->almacen_model->eliminar_articulo($id);
		echo json_encode($resp);
		return;
	}

	public function denegar_solicitud(){
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(-1302);
			return;
		}
		$id = $this->input->post("id");
		if (ctype_space($id) || empty($id) || !is_numeric($id)) {
			echo json_encode(-1);
			return;
		}
		$comment = trim($this->input->post("comment"));
		if (ctype_space($comment) || empty($comment)) {
			echo json_encode(-2);
			return;
		}
		$resp = $this->almacen_model->denegar_solicitud($id, $comment);
		echo json_encode($resp);
		return;
	}

	public function adjuntar_firma($name){
		if ( isset($_POST[$name]) && !empty($_POST[$name]) ) {    
			$dataURL = $_POST[$name];  
			$parts = explode(',', $dataURL);  
			$data = $parts[1];  
			$data = base64_decode($data);  
			$file =  uniqid() . '.png';
			$success = file_put_contents('archivos_adjuntos/almacen/firmas/'.$file, $data);
			return $success ? $file : -3;
		}
		  return -2;
	}

	public function listar_permisos_por_parametro(){
		$perfiles = array();
		if ($this->Super_estado == false) {
			echo json_encode($perfiles);
			return;
		}
		$vp_p = $this->input->post('vp_p');
		$id_p = $this->input->post('id_p');
		$aux = $this->input->post('aux');
		$datos = $this->almacen_model->listar_permisos_por_parametro($vp_p);
		$i = 0;
		foreach ($datos as $row) {
			if ($aux) {
				$i++;
				$row["num"] = $i;
				if (is_null($row["estado"])) {
					$class = "fa-toggle-off";
					$msj = "Quitar perfil";
				}else{
					$class = "fa-toggle-on";
					$msj = "Asignar perfil";
				}
				$x=$row['id'];
				$x1=$row['id_aux'];

				$row["opciones"] = "<span id='btn$i' style='color:green' title='$msj' data-toggle='popover' data-trigger='hover' class='btn btn-default pointer fa $class' onclick='gestionar_perfil($x,".json_encode($x1).",$id_p,".json_encode($vp_p).", $i)'></span>";
			}
			$perfiles["data"][] = $row;
		}
		echo json_encode($perfiles);
	}

	public function guardar_articulo(){
		if ($this->Super_estado == false) {
				echo json_encode("sin_session");
				return;
		} else {
			if ($this->Super_agrega == 0) {
					echo json_encode(-1302);
			} else {
				$tipo = $this->input->post("tipo");
				$tipo_modulo = $this->input->post("tipo_modulo");
				if ($tipo == 2 || $tipo == 3 || $tipo == 4 || $tipo == 5) {
					if ($tipo == 4 || $tipo == 5 || $tipo == 2) {
						if ($tipo != 2) {
							$id = $this->input->post("id");
							if (ctype_space($id) || empty($id) || !is_numeric($id)) {
								echo json_encode(-10);
								return; 
							}
						}
					}
					$articulo = $this->input->post("articulo");
					if (ctype_space($articulo) || empty($articulo) || !is_numeric($articulo)) {
						echo json_encode(-9);
						return; 
					}
				}
				$cantidad = $this->input->post("cantidad_art");
				$observaciones = $this->input->post("observaciones");
				$unidades = $this->input->post('unidades_art');
				
				//Se valida que la cantidad de artículos sea válido
				if (ctype_space($cantidad) || empty($cantidad) || !is_numeric($cantidad) || $cantidad < 1) {
					echo json_encode(-4);
					return;
				}
				//Si tipo es 2 solo se están validando los datos para agregar a la tabla de artículo para la solicitud a guardar.
				if ($tipo == 2 || $tipo == 3) {
					$stock = $this->almacen_model->get_existencia_articulo($articulo);
					$cant = $this->almacen_model->get_cantidad_solicitada($articulo);
					if (($cant + $cantidad) > $stock) {
						echo json_encode(-12);
						return;
					}
					if($tipo == 2) {
						echo json_encode(2); 
					}else{
						echo json_encode(3);
					};
					return;
					//Si tipo es 3 se está modificando el artículo dentro del array
				}else if($tipo == 4){
					$solicitud = $this->input->post("solicitud");
					$info = $this->almacen_model->get_estado_solicitud($solicitud);
					if ($info->{'state'} != 'Alm_Rec' || ($_SESSION['persona'] != $info->{'user'} && $_SESSION['perfil'] != 'Per_Admin')) {
						echo json_encode(-14);
						return;
					}
					$stock = (int)$this->almacen_model->get_existencia_articulo($articulo);
					$cant = (int)$this->almacen_model->get_cantidad_solicitada($articulo);
					$cant_res = (int)$this->almacen_model->get_cant_solicitada($id);
					if ((($cant - $cant_res) + $cantidad) > $stock) {
						echo json_encode(-12);
						return;
					}
					$resp = $this->almacen_model->modificar_articulo_solicitud($id, $cantidad, $observaciones);
					echo json_encode($resp);
					return;
				}else if ($tipo == 5) {
					$info = $this->almacen_model->get_estado_solicitud($id);
					if ($info->{'state'} != 'Alm_Rec' || ($_SESSION['persona'] != $info->{'user'} && $_SESSION['perfil'] != 'Per_Admin')) {
						echo json_encode(-14);
						return;
					}
					// Si un artículo es eliminado y se intenta agregar nuevamente se creará un registro nuevo
					// Por lo que quedarán uno con estado -1 y otro con estaddo 1
					$existe = $this->almacen_model->existe_articulo_solicitud($id, $articulo);
					if ($existe) {
						echo json_encode(-13);
						return;
					}
					$stock = $this->almacen_model->get_existencia_articulo($articulo);
					$cant = $this->almacen_model->get_cantidad_solicitada($articulo);
					if (($cant + $cantidad) > $stock) {
						echo json_encode(-12);
						return;
					}
					$resp = $this->almacen_model->agregar_articulo_solicitud($id, $articulo, $cantidad, $observaciones);
					echo json_encode($resp);
					return;
				}
      }
    }
	}

	public function get_route(){
		$pages = $_SERVER['REQUEST_URI'];
		$pos = strrpos($pages, "index.php/");
		$pages =  preg_replace('/[0-9]+/', '', substr($pages, $pos+10, strlen($pages)));
		$cant = strlen($pages);
		if($pages[$cant-1] == '/') $pages = substr($pages, 0, -1);
		return $pages;
	}

	public function mercancia_en_almacen() {
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(-1302);
			return;
		}
		$id = $this->input->post("id");
		if (ctype_space($id) || empty($id) || !is_numeric($id)) {
			echo json_encode(-1);
			return;
		}
		$art = $this->almacen_model->Listar_articulos_solicitud($id);
		foreach ($art as $row) {
			$cant = $this->almacen_model->get_existencia_articulo($row['codigo']);
			if ($cant < $row['cantidad']) {
				echo json_encode(-2);
				return;
			}
		}
		foreach ($art as $row) {
			$this->almacen_model->descontar_cantidad($row['codigo'], $row['cantidad']);
		}
		$resp = $this->almacen_model->cambiar_estado_solicitud($id, 'Alm_Mer');
		if ($resp == 1) {
			$onTime = $this->onTime('Alm_Mer');
		}
		echo json_encode($resp);
	}

	public function finalizar_solicitud(){
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		if ($this->Super_modifica == 0) {
			echo json_encode(-1302);
			return;
		}
		$id = $this->input->post("id");
		if (ctype_space($id) || empty($id) || !is_numeric($id)) {
			echo json_encode(-1);
			return;
		}
		$usuario = $this->input->post("usuario");
		if (ctype_space($usuario) || empty($usuario)) {
			echo json_encode($usuario);
			return;
		}
		$name_firma = $this->adjuntar_firma("image");
		if ($name_firma == -3 || $name_firma == -2) {
			echo json_encode(-4);
			return;
		}

		$resp = $this->almacen_model->cambiar_estado_solicitud($id, 'Alm_Ent', $name_firma);
		$this->almacen_model->persona_entrega($id, $usuario);
		echo json_encode($resp);
		return;
	}

	public function obtener_encuestas_soli_ent(){
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		} else {
			$fechaIni = $this->input->post("fechaInicio");
			$fechaFin = $this->input->post("FechaFin");
			$persona = $_SESSION['persona'];
			$res = $this->almacen_model->obtener_encuestas_soli_ent($persona, $fechaIni, $fechaFin);
			echo json_encode($res);
		}
	}

}
?>
