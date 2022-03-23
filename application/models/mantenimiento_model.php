<?php
	date_default_timezone_set('America/Bogota');
	if (!defined('BASEPATH'))
		exit('No direct script access allowed');
	class mantenimiento_model extends CI_Model{
		var $tabla = 'solicitudes_mantenimiento';
		var $tabla_articulos = 'articulos_solicitudes_man';
		var $tabla_estados = 'estados_solicitudes_mantenimiento';
		var $tabla_operarios = 'operario_solicitud';

		public function buscar_articulo($art = '', $tipo_modulo){
			$this->db->select("aa.id, aa.codigo, aa.nombre_articulo AS nombre, aa.marca, aa.referencia", false);
			$this->db->from('articulos_almacen aa');
			$this->db->where("aa.nombre_articulo LIKE '%$art%'");
			$this->db->where('aa.tipo_modulo', $tipo_modulo);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function Listar_solicitudes($estado, $cat, $dep, $fecha_i, $fecha_f, $id){
			$this->db->select("sm.start_date,end_date,sm.id, p.id as resp,sm.num_solicitud as num, sm.ubicacion, sm.descripcion, sm.fecha_registra as fecha, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname, sm.estado_solicitud as state, esm1.fecha as f_recibido, esm2.fecha as f_ejecutado, est.valor AS estado, d.valor as departamento, sm.observacion, cat.valor AS categoria, pri.valor AS prioridad, sm.fecha_inicio, sm.fecha_fin, sm.categoria as cat, sm.calificacion, p.usuario, sm.firma, p.correo, sm.comentario, sm.tiempo, sm.tiempo_habil, cat.valory as dias, sm.telefono, sm.id_evento_com, sm.participantes, sm.fecha_calificacion", false);
			$this->db->from('solicitudes_mantenimiento sm');
			$this->db->join('estados_solicitudes_mantenimiento esm1','sm.id = esm1.solicitud_id and esm1.estado_id = "Man_Rcbd"', 'left');
			$this->db->join('estados_solicitudes_mantenimiento esm2','sm.id = esm2.solicitud_id and esm2.estado_id = "Man_Eje"', 'left');
			$this->db->join('personas p','sm.solicitante_id = p.id');
			$this->db->join("valor_parametro est", "sm.estado_solicitud = est.id_aux");
			$this->db->join('cargos_departamentos cd', 'p.id_cargo = cd.id', 'left');
			$this->db->join('valor_parametro d', 'cd.id_departamento = d.id', 'left');
			$this->db->join('valor_parametro cat', 'sm.categoria = cat.id_aux', 'left');
			$this->db->join('valor_parametro pri', 'sm.prioridad = pri.id_aux', 'left');
			$admin = ($_SESSION['perfil'] === 'Per_Admin' || $_SESSION['perfil'] === 'Per_Admin_Man') ? 1 : 0;
			
			if($admin){
				if(!empty($fecha_i) || !empty($fecha_f)){
					if (!empty($fecha_i) && !empty($fecha_f)) {
						$this->db->where('DATE_FORMAT(sm.fecha_registra, "%Y-%m") >=', $fecha_i);
						$this->db->where('DATE_FORMAT(sm.fecha_registra, "%Y-%m") <=', $fecha_f);
					}else if(!empty($fecha_i) && empty($fecha_f)) $this->db->where('DATE_FORMAT(sm.fecha_registra, "%Y-%m") >=', $fecha_i);
					else if (empty($fecha_i) && !empty($fecha_f)) $this->db->where('DATE_FORMAT(sm.fecha_registra, "%Y-%m") <=', $fecha_f);
					else $this->db->where('sm.fecha_registra >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)');
					if(!empty($estado)) $this->db->where("sm.estado_solicitud", $estado);
				}else if(!$id){
					!empty($estado) 
					? $this->db->where("sm.estado_solicitud", $estado)
					: $this->db->where("(sm.estado_solicitud <> 'Man_Rec') AND (sm.estado_solicitud <> 'Man_Can') AND (sm.estado_solicitud <> 'Man_Eje')");
				}
			}else {
				$this->db->where('p.id', $_SESSION['persona']);
				if(is_numeric($id) && $id > 0) $this->db->where('sm.id', $id);
			}
			if (!empty($cat)) $this->db->where("sm.categoria", $cat);
			if(!empty($dep)) $this->db->where("cd.id_departamento", $dep);
			
			$this->db->where('sm.estado', 1);
			$this->db->_protect_identifiers = false;
			$this->db->order_by("FIELD (sm.estado_solicitud, 'Man_Sol', 'Man_Pau', 'Man_Rcbd', 'Man_Eje', 'Man_Rec', 'Man_Can')");
			$this->db->_protect_identifiers = true;
			$query = $this->db->get();
			return $query->result_array();
		}

		public function guardar_solicitud($data/*, $articulos*/){
			(isset($data['solicitante_id']) && !empty($data['solicitante_id'])) 
				? $data['usuario_asigna'] = $_SESSION['persona']
				: $data['solicitante_id'] = $_SESSION['persona'];
			$this->db->insert($this->tabla, $data);
			$error = $this->db->_error_message(); 
			if ($error) return 'error';
			$arts = [];
			$id = $this->ultima_solicitud_por_usuario($data['solicitante_id']);
			// if ($articulos) {
			// 	foreach ($articulos as $art) {
			// 		array_push($arts, array('solicitud_id'=>$id,'articulo_id'=>$art['id'],'cantidad'=>$art['cantidad'],));
			// 	}
			// 	$this->db->insert_batch($this->tabla_articulos, $arts);
			// 	$error = $this->db->_error_message(); 
			// 	if ($error) return 0;
			// }
			$this->guardar_datos(array("solicitud_id" => $id,"estado_id" => 'Man_Sol',"usuario_id" => $_SESSION['persona'],), $this->tabla_estados);
			return $id;
		}


		public function ultima_solicitud_por_usuario($id){
			$this->db->select('id');
			$this->db->from('solicitudes_mantenimiento');
			$this->db->order_by('fecha_registra', 'DESC');
			$this->db->where('solicitante_id', $id);
			$this->db->limit(1);
			$query = $this->db->get();
			$row = $query->row();
			return $row->id;
		}

		public function traer_solicitud($id){
			$this->db->select('sm.id_seguridad', false);
			$this->db->from('solicitudes_mantenimiento sm');
			$this->db->where('sm.id', $id);
			$this->db->limit(1);
			$query = $this->db->get();
			$row = $query->row();
			return $row;
		}

		public function articulos_solicitados($id){
			$this->db->select("asm.*,asm.id, aa.nombre_articulo as nombre, asm.cantidad,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS solicitante",false);
			$this->db->from('articulos_solicitudes_man asm');
			$this->db->join('personas p', 'p.id = asm.id_usuario_registra');
			$this->db->join('articulos_almacen aa','asm.articulo_id = aa.id');
			$this->db->where('asm.solicitud_id', $id);
			$this->db->where('asm.estado', 1);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function traer_operarios($categoria){
			$this->db->select("p.id AS p_id, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname, p.telefono", false);
			$this->db->from('operario_categoria oc');
			$this->db->join('personas p', 'p.id = oc.persona_id');
			$this->db->join('valor_parametro cat', 'cat.id_aux = oc.categoria_id');
			$this->db->where('oc.categoria_id', $categoria);
			$this->db->where('oc.estado', 1);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function traer_ultimo_num_solicitud(){
			$this->db->select("MAX(num_solicitud) as num");
			$this->db->from('solicitudes_mantenimiento sm');
			$query = $this->db->get();
			return $query->row()->num;
		}

		public function gestionar_solicitud($id, $estado, $categoria, $prioridad, $num, $obs='', $fecha_inicio, $fecha_fin, $tiempo_habil){
			$this->db->set('categoria', $categoria);
			$this->db->set('prioridad', $prioridad);
			$this->db->set('tiempo_habil', $tiempo_habil);
			$this->db->set('comentario', $obs);
			$this->db->set('num_solicitud', $num);
			if (!empty($fecha_inicio) && !empty($fecha_fin)) {
				$this->db->set('start_date', $fecha_inicio);
				$this->db->set('end_date', $fecha_fin);
			}
			$this->db->where('id', $id);
			$this->db->update($this->tabla);
			$error = $this->db->_error_message(); 
			if ($error) return "error";
			$resp = $this->cambiar_estado_solicitud($id, $estado);
			return $resp;
		}

		public function cambiar_estado_solicitud($id, $estado, $obs = ''){
			$info = $this->get_estado_solicitud($id);
			$state = $info->{'state'};
			if (($state == 'Man_Sol' && ($estado == 'Man_Rcbd' || $estado == 'Man_Can' || $estado == 'Man_Rec' || $estado == 'Man_Pau')) || ($state == 'Man_Rcbd' && ($estado == 'Man_Eje' || $estado == 'Man_Rec')) || ($state == 'Man_Eje' && ($estado == 'Man_Fin')) || ($state == 'Man_Pau' && ($estado == 'Man_Rcbd' || $estado == 'Man_Rec' || $estado == 'Man_Can'))) {
				$this->db->set('estado_solicitud', $estado);
				$this->db->set('observacion', $obs);
				$this->db->where('id', $id);
				$this->db->update($this->tabla);
				$error = $this->db->_error_message(); 
				if ($error) return "error";
				$resp = $this->guardar_datos(array("solicitud_id" => $id,"estado_id" => $estado,"comment" => $obs,"usuario_id" => $_SESSION['persona'],), $this->tabla_estados);
				return $resp;
			}else return 0;
		}

		public function guardar_datos($data, $tabla, $tipo = 1){
			$tipo == 2 
				? $this->db->insert_batch($tabla, $data)
				: $this->db->insert($tabla, $data);
			$error = $this->db->_error_message(); 
			return $error ? 0 : 1;
		}

		public function traer_operarios_solicitud($id){
			$this->db->select("p.id, p.identificacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname, p.correo, p.usuario, p.telefono, p.foto", false);
			$this->db->from('operario_solicitud os');
			$this->db->join('personas p', 'os.persona_id = p.id');
			$this->db->where('os.solicitud_id', $id);
			$this->db->where('os.estado', 1);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function retirar_operario($id, $id_solicitud){
			$this->db->set('estado', 0);
			$this->db->set('fecha_asigna', date("Y-m-d H-m-s"));
			$this->db->set('usuario_desasigna', $_SESSION['persona']);
			$this->db->where('persona_id', $id);
			$this->db->where('solicitud_id', $id_solicitud);
			$this->db->update($this->tabla_operarios);
			$error = $this->db->_error_message(); 
			if ($error) return "error";
			return 1;
		}

		public function get_estado_solicitud($id){
			$this->db->select("estado_solicitud as state, solicitante_id as user");
			$this->db->from($this->tabla);
			$this->db->where('id', $id);
			$query = $this->db->get();
			return $query->row();
		}

		public function calificar_solicitud($id, $rating, $observacion/*, $firma = ''*/){
			$this->db->set('calificacion', $rating);
			// if ($firma != '') $this->db->set('firma', $firma);
			$this->db->set('observacion', $observacion);
			$this->db->set("fecha_calificacion", date("Y-m-d H-m-s"));
			$this->db->where('id', $id);
			$this->db->update($this->tabla);
			$error = $this->db->_error_message(); 
			if ($error) return 'error';
			$data = [
				"solicitud_id" => $id,
				"estado_id" => "Man_Fin",
				"comment" => $observacion,
				"usuario_id" => $_SESSION['persona'],
			];
			$resp = $this->guardar_datos($data, $this->tabla_estados);
			return $resp;
		}

		public function get_categorias(){
			$this->db->select('id, id_aux, valor AS nombre');
			$this->db->from('valor_parametro');
			$this->db->where('idparametro', 58);
			$this->db->order_by('nombre', 'asc');
			$query = $this->db->get();
			return $query->result_array();
		}

		public function get_operarios($cat){
			$this->db->select("p.id, p.identificacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname", false);
			$this->db->from('personas p');
			// $this->db->join('cargos_departamentos cd', 'p.id_cargo = cd.id');
			$this->db->join('valor_parametro vp', 'vp.id = p.id_cargo_sap');
			$this->db->join('operario_categoria oc', 'p.id = oc.persona_id', 'left');
			$this->db->where("oc.categoria_id IS NULL  OR (SELECT oc1.id FROM operario_categoria oc1 WHERE p.id = oc1.persona_id AND oc1.categoria_id = '$cat') IS NULL ");
			$this->db->group_by("p.id");
			$query = $this->db->get();
			return $query->result_array();
		}

		public function get_operarios_categoria($cat){
			$this->db->select("p.id, p.identificacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname", false);
			$this->db->from('operario_categoria oc');
			$this->db->join('personas p', 'p.id = oc.persona_id');
			$this->db->where("oc.categoria_id", "$cat");
			$query = $this->db->get();
			return $query->result_array();
		}

		public function validar_operario($id, $cat){
			$this->db->select("COUNT(*) existe", false);
			$this->db->from('operario_categoria');
			$this->db->where("persona_id", $id);
			$this->db->where("categoria_id", $cat);
			$res = $this->db->get()->row()->existe;
			return ($res > 0) ? 1 : 0;
		}

		public function quitar_operario($id, $cat){
			$this->db->where("persona_id", $id);
			$this->db->where("categoria_id", $cat);
			$this->db->delete('operario_categoria');
			$error = $this->db->_error_message(); 
			if ($error) return 0;
			return 1;
		}

		public function tiempo_ejecucion($id){
			$query = $this->db->query("(SELECT sm.id, sm.fecha_registra, cat.valor AS categoria, cat.valory AS tiempo, esm.fecha AS f_recibido, esm1.fecha AS f_ejecutado
			FROM solicitudes_mantenimiento sm
			INNER JOIN estados_solicitudes_mantenimiento esm ON (sm.id = esm.solicitud_id AND esm.estado_id = 'Man_Rcbd')
			INNER JOIN estados_solicitudes_mantenimiento esm1 ON (sm.id = esm1.solicitud_id AND esm1.estado_id = 'Man_Eje')
			LEFT JOIN valor_parametro cat ON cat.id_aux = sm.categoria
			WHERE sm.id = $id)");
			return $query->row();
		}

		public function calificarTiempo($id, $time){
			$this->db->set('tiempo', $time);
			$this->db->where('id', $id);
			$this->db->update($this->tabla);
			$error = $this->db->_error_message(); 
			if ($error) return 'error';
			return 1;
		}

		public function traer_historial_solicitud($id){
			$this->db->select("esm.solicitud_id, esm.fecha, vp.valor AS estado, CONCAT(p.nombre, ' ', p.apellido) AS fullname", false);
			$this->db->from('estados_solicitudes_mantenimiento esm');
			$this->db->join('valor_parametro vp','vp.id_aux = esm.estado_id');
			$this->db->join('personas p', 'p.id = esm.usuario_id');
			$this->db->where('esm.solicitud_id', $id);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function cargar_personas($opt){
			$this->db->select("p.id, p.identificacion, CONCAT(p.apellido, ' ', p.segundo_apellido, ' ', p.nombre) AS fullname, vp.valor as departamento", false);
			$this->db->from('personas p');
			$this->db->join('cargos_departamentos cd', 'p.id_cargo = cd.id');
			$this->db->join('valor_parametro vp', 'vp.id = cd.id_departamento');
			$this->db->where("CONCAT(p.apellido, ' ', p.segundo_apellido, ' ', p.nombre) LIKE '%$opt%' || p.identificacion LIKE '%$opt%' || p.usuario LIKE '%$opt%'");
			$query = $this->db->get();
			return $query->result_array();
		}

		public function articulos_solicitados_fecha($id, $fecha_inicial, $fecha_final){
			$query = $this->db->query("SELECT SUM(rs.cantidad) cantidades FROM solicitudes_mantenimiento r 
			INNER JOIN articulos_solicitudes_man rs ON r.id = rs.solicitud_id
			WHERE  ('$fecha_inicial' BETWEEN r.fecha_inicio AND r.fecha_fin OR '$fecha_final' BETWEEN r.fecha_fin AND r.fecha_fin OR r.fecha_inicio BETWEEN '$fecha_inicial' AND '$fecha_final' OR  r.fecha_fin BETWEEN '$fecha_inicial' AND '$fecha_final') AND (r.estado_solicitud <> 'Man_Fin' AND r.estado_solicitud <> 'Man_Can'  AND r.estado_solicitud <> 'Man_Rec') AND rs.articulo_id = $id AND rs.estado = 1");
			$row = $query->row();
			return $row->cantidades;			
		}

		public function get_tiempo_habil($categoria){
			$this->db->select("valory");
			$this->db->from('valor_parametro');
			$this->db->where("idparametro", 58);
			$this->db->where("id_aux", $categoria);
			return $this->db->get()->row()->valory;
		}

		public function get_mantenimiento_por_evaluar($id_persona){
			$this->db->select("sm.id, count(sm.id) cantidad");
			$this->db->from('solicitudes_mantenimiento sm');
			$this->db->where("sm.calificacion", NULL);
			$this->db->where('sm.estado_solicitud = "Man_Eje" AND sm.estado = 1');
			$this->db->where('sm.solicitante_id', $id_persona);
			$query = $this->db->get();
			return $query->result_array();
		}

		
		public function listar_permisos_parametros ($id_principal){
			$this->db->select("vp.valor nombre, vp.id ");
			$this->db->from('permisos_parametros pp');
			$this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
			$this->db->where('pp.vp_principal_id', $id_principal);
			$this->db->where('vp.estado', 1);
			$this->db->order_by('vp.valor');
			$query = $this->db->get();
			return $query->result_array();
		}
		
		public function listar_valor_parametro($id_parametro){
			$this->db->select("vp.*");
			$this->db->from('valor_parametro vp');
			$this->db->where('vp.idparametro', $id_parametro);
			$this->db->where('vp.estado', 1);
			$this->db->order_by('vp.valor');
			$query = $this->db->get();
			return $query->result_array();
		}

		public function traer_solicitud_id($valor_columna, $tabla, $col) {
			$this->db->select('*');
			$this->db->from($tabla);
			$this->db->order_by("id", "desc");
			$this->db->where($col, $valor_columna);
			$this->db->limit(1);
			$query =$this->db->get();
			$row = $query->row();
			return $row;
			
		}
		
		public function get_solicitud_matenimiento() {
			$this->db->select("sm.id, sm.nombre_mantenimiento, sm.numero_notificaciones, vp4.valor mes_inicio_notificacion, sm.dia_entre_notificacion, sm.observacion_mantenimiento, sm.fecha_registra, vp3.valor periodicidad, vp3.id id_periodicidad, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname,  (SELECT COUNT(pp.id) FROM mantenimiento_periodicos_lugares pp WHERE pp.estado = 1 AND pp.id_mantenimiento_periodico = sm.id) cantidad_mantenimiento, (SELECT vp4.valor FROM mantenimiento_periodico_historial mph2 INNER JOIN valor_parametro vp4 ON vp4.id_aux = mph2.id_estado_lugar AND vp4.estado = 1 WHERE mph2.id_mantenimiento_periodico = sm.id AND mph2.estado = 1 ORDER BY mph2.fecha_registra DESC LIMIT 1) estado_mantenimiento, (SELECT vp5.id_aux FROM mantenimiento_periodico_historial mph2 INNER JOIN valor_parametro vp5 ON vp5.id_aux = mph2.id_estado_lugar AND vp5.estado = 1 WHERE mph2.id_mantenimiento_periodico = sm.id AND mph2.estado = 1 ORDER BY mph2.fecha_registra DESC LIMIT 1) id_estado_lugar, (SELECT mph3.fecha_fin FROM mantenimiento_periodico_historial mph3 WHERE mph3.id_mantenimiento_periodico = sm.id AND mph3.estado = 1 ORDER BY mph3.fecha_registra DESC LIMIT 1) fecha_fin, (SELECT mph4.fecha_inicio FROM mantenimiento_periodico_historial mph4 WHERE mph4.id_mantenimiento_periodico = sm.id AND mph4.estado = 1 ORDER BY mph4.fecha_registra DESC LIMIT 1) fecha_inicio, vp4.id id_mes_inicio_not", false);
			$this->db->from('solicitud_mantenimiento sm');
			$this->db->join('valor_parametro vp3', 'vp3.id=sm.id_periodicidad');
			$this->db->join('personas p', 'p.id=sm.id_usuario_registra');
			$this->db->join('valor_parametro vp4', 'vp4.id=sm.mes_inicio_notificacion');
			$this->db->group_by("sm.id");
			$query = $this->db->get();
			return $query->result_array();
		}
		
		// public function listar_detalle_mantenimiento ($id, $id_periodico) {
		// 	$this->db->select("vp1.valor periodo, vp2.valor lugar, mp.fecha_registra, mp.id");
		// 	$this->db->from('mantenimiento_periodicos mp');
		// 	$this->db->join("valor_parametro vp1", "vp1.id = mp.id_periodo AND vp1.estado = 1", 'left');
		// 	$this->db->join("valor_parametro vp2", "vp2.id = mp.id_lugar AND vp2.estado = 1", 'left');
		// 	$this->db->where("mp.id_periodo = $id_periodico AND mp.id_lugar = $id ANd mp.estado = 1");
		// 	$this->db->order_by("mp.id", "DESC");
		// 	$query = $this->db->get();
		// 	return $query->result_array();
			
		// }
		
		public function listar_lugares_mantenimientos_periodico ($id_mantenimiento_periodico, $id_historial_periodico) {
			$this->db->select("mpl.id, vp.valor lugar, mpl.id_lugar, vp2.valor tipo_mtto, vp2.id_aux id_tipo_mtto, mpl.id_mantenimiento_periodico, mpl.id_historial_periodico");
			$this->db->from('mantenimiento_periodicos_lugares mpl');
			$this->db->join("valor_parametro vp", "vp.id = mpl.id_lugar AND vp.estado = 1", 'left');
			$this->db->join("valor_parametro vp2", "vp2.id_aux = mpl.tipo_mtto AND vp2.estado = 1 AND vp2.id_aux != '' AND vp2.id_aux IS NOT NULL", 'left');
			$this->db->where("mpl.estado = 1 AND mpl.id_mantenimiento_periodico =  $id_mantenimiento_periodico AND mpl.id_historial_periodico = '$id_historial_periodico'");
			$query = $this->db->get();
			return $query->result_array();	
		}

		public function obtener_historial($id_mantenimiento_periodico) {
			$this->db->select("mph.id");
			$this->db->from('mantenimiento_periodico_historial mph');
			$this->db->where("mph.estado = 1 AND mph.id_mantenimiento_periodico =  $id_mantenimiento_periodico");
			$this->db->order_by("mph.id", "DESC");
			$this->db->limit(1);
			$query = $this->db->get();
			return $query->result_array();
		}
		
		public function buscar_lugar_mantenimiento_periodico($buscar){
		$this->db->select("vp.*");
			$this->db->from('valor_parametro vp');
			$this->db->where($buscar);
			$query = $this->db->get();
			return $query->result_array();
		}
		public function validar_lugar_mantenimiento($buscar){
			$this->db->select("lm.*, vp.valor");
			$this->db->from('mantenimiento_periodicos_lugares lm');
			$this->db->join('valor_parametro vp', "lm.id_mantenimiento_periodico = vp.id", 'left');
			$this->db->where($buscar);
			$query = $this->db->get();
			return $query->row();
		}


		public function validar_existencia_mantenimiento_periodico($id_mantenimiento_periodico) {
			$this->db->select("mp.*");
			$this->db->from('mantenimiento_periodico_historial mp');
			$this->db->where('mp.id_mantenimiento_periodico', $id_mantenimiento_periodico);
			$this->db->where("mp.fecha_inicio IS NOT NULL AND mp.fecha_fin IS NULL AND mp.estado = 1");
			$query = $this->db->get();
			return $query->row();
		}


		public function validar_adjuntos_periodico ($id_mantenimiento_periodico) {
			$this->db->select("mpl.id, mpl.id_mantenimiento_periodico cant_evidencias, (SELECT COUNT(mpe2.id) FROM mantenimiento_periodico_evidencia mpe2 WHERE mpe2.id_mantenimiento = mpl.id AND mpe2.estado = 1) cant_evidencias, mpl.tipo_mtto estado");
			$this->db->from("mantenimiento_periodicos_lugares mpl");
			$this->db->join("mantenimiento_periodico_evidencia mpe", "mpe.id_mantenimiento = mpl.id AND mpe.estado = 1", "left");
			// $this->db->join("valor_parametro vp", "vp.id = mpl.tipo_mtto AND vp.estado = 1", "left");
			$this->db->where("mpl.id_mantenimiento_periodico = $id_mantenimiento_periodico AND mpl.estado = 1");
			$this->db->group_by("mpl.id");
			$query = $this->db->get();
			return $query->result_array();
		}

		public function listar_detalles_solictud_mantenimiento_periodico($id_mantenimiento_periodico) {
			$this->db->select("mph.*");
			$this->db->from('mantenimiento_periodico_historial mph');
			$this->db->where("mph.id_mantenimiento_periodico = $id_mantenimiento_periodico");
			$this->db->where("mph.estado", 1);
			$this->db->order_by("mph.fecha_inicio", "DESC");
			$this->db->order_by("mph.fecha_fin", "DESC");
			$query = $this->db->get();
			return $query->result_array();
		}


		public function traer_objetos ($id_ubicacion) {
			$this->db->select("sm.id_mantenimiento, sm.cantidad, vp.valor nombre_objeto, vp.id id_objeto ");
			$this->db->from('objetos_mantenimiento sm');
			$this->db->join('valor_parametro vp', 'vp.id = sm.id_objeto_mantenimiento');
			$this->db->where("sm.id_ubicacion", $id_ubicacion);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function listar_lugares_mantenimiento () {
			$this->db->select("vp.*, vp.valor lugar, (SELECT COUNT(pp.id) FROM permisos_parametros pp INNER JOIN valor_parametro ub ON ub.id = pp.vp_secundario_id WHERE pp.vp_principal_id = vp.id AND ub.idparametro = 116) cantidad_ubicaciones");
			$this->db->from('valor_parametro vp');
			$this->db->where("vp.idparametro", 115);
			$this->db->where("vp.estado", 1);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function listar_ubiaciones_mantenimiento ($id_lugar) {
			$this->db->select("vp.*, vp.valor ubicacion, (SELECT COUNT(om.id) FROM objetos_mantenimiento om WHERE om.id_lugar = $id_lugar AND om.id_ubicacion = vp.id AND om.estado = 1) cantidad_objetos, mph.id_estado_matto, lug.valor lugar, mph.id id_historial, (SELECT mph3.id_estado_matto FROM mantenimiento_preventivo_historial mph3 WHERE mph3.id_lugar = $id_lugar AND mph3.id_ubicacion = vp.id AND mph3.estado = 1 ORDER BY mph3.fecha_registra DESC limit 1) id_estado_matto, (SELECT vp2.valor FROM mantenimiento_preventivo_historial vp4 INNER JOIN valor_parametro vp2 ON vp2.id_aux = vp4.id_estado_matto AND vp2.estado = 1 WHERE vp4.id_lugar = $id_lugar AND vp4.id_ubicacion = vp.id AND vp4.estado = 1 ORDER BY vp4.fecha_registra DESC limit 1) estado_mtto, (SELECT mph4.fecha_inicio FROM mantenimiento_preventivo_historial mph4 WHERE mph4.id_ubicacion = vp.id AND mph4.id_lugar = $id_lugar AND mph4.estado= 1 ORDER BY mph4.fecha_inicio DESC LIMIT 1) fecha_inicio, (SELECT mph5.fecha_fin FROM mantenimiento_preventivo_historial mph5 WHERE mph5.id_ubicacion = vp.id AND mph5.id_lugar = $id_lugar AND mph5.estado= 1 ORDER BY mph5.fecha_inicio DESC LIMIT 1) fecha_fin", false);
			$this->db->from('valor_parametro vp');
			$this->db->join('permisos_parametros p', "p.vp_secundario_id = vp.id and p.vp_principal_id = $id_lugar");
			$this->db->join('mantenimiento_preventivo_historial mph', "mph.id_ubicacion = vp.id and mph.id_lugar = $id_lugar and mph.estado=1", "left");
			$this->db->join('valor_parametro est', "mph.id_estado_matto = est.id_aux", "left");
			$this->db->join('valor_parametro lug', "p.vp_principal_id = lug.id", "left");
			$this->db->where("vp.idparametro", 116);
			$this->db->where("vp.estado", 1);
			$this->db->group_by("vp.id");
			$this->db->order_by("vp.valory", "ASC");
			$query = $this->db->get();
			return $query->result_array();
		}


		public function listar_objetos_matto ($id_ubicacion) {
			$this->db->select("om.*, vp.valor objeto");
			$this->db->from('valor_parametro vp');
			$this->db->join('objetos_mantenimiento om', "om.id_objeto_mantenimiento = vp.id and om.id_ubicacion = $id_ubicacion");
			// $this->db->where("vp.idparametro", 267); // developement objetos mantenimiento
			$this->db->where("vp.idparametro", 344); // developement objetos mantenimiento
			$this->db->where("om.estado", 1);
			$query = $this->db->get();
			return $query->result_array();	
		}

		public function listar_objetos_matto_gest($id_ubicacion, $id_lugar, $id_historial){
			$this->db->select("vp2.valor nombre_obj, vp1.id_aux id_estado, mph.id_estado_matto, vp1.valor estado_obj, meo.id_objeto_mantenimiento");
			$this->db->from('mantenimiento_estados_objetos meo');
			$this->db->join("valor_parametro vp1", "vp1.id = meo.id_estado_objeto AND vp1.estado = 1");
			$this->db->join("valor_parametro vp2", "vp2.id = meo.id_objeto_mantenimiento AND vp2.estado = 1");
			$this->db->join("mantenimiento_preventivo_historial mph", "mph.id = meo.id_historial_preventivo AND mph.estado = 1");
			$this->db->where("meo.id_lugar = $id_lugar AND meo.id_ubicacion = $id_ubicacion AND meo.id_historial_preventivo = $id_historial AND meo.estado = 1");

			$query = $this->db->get();
			return $query->result_array();	
		}

		public function buscar_objetos_inspeccion_mantenimiento($buscar){
			$this->db->select("vp.*");
			$this->db->from('valor_parametro vp');
			$this->db->where($buscar);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function validar_objeto_mtto($buscar){
			$this->db->select("om.*, vp.valor");
			$this->db->from('objetos_mantenimiento om');
			$this->db->join('valor_parametro vp', "om.id_objeto_mantenimiento = vp.id");
			$this->db->where($buscar);
			$query = $this->db->get();
			return $query->row();
		}

		public function validar_existencia_mantenimiento($id_lugar, $id_ubicacion) {
			$this->db->select("mp.*");
			$this->db->from('mantenimiento_preventivo_historial mp');
			$this->db->where('mp.id_lugar',$id_lugar);
			$this->db->where('mp.id_ubicacion', $id_ubicacion);
			$this->db->where("mp.fecha_inicio IS NOT NULL AND mp.fecha_fin IS NULL AND mp.estado = 1");
			$query = $this->db->get();
			return $query->row();
		}

		public function listar_detalles_solictud($id_lugar, $id_ubicacion) {
			$this->db->select("om.*");
			$this->db->from('mantenimiento_preventivo_historial om');
			$this->db->where("om.id_lugar = $id_lugar and om.id_ubicacion = $id_ubicacion");
			$this->db->where("om.estado", 1);
			$this->db->order_by("om.fecha_inicio", "DESC");
			$query = $this->db->get();
			return $query->result_array();
		}

		public function evidencias_mantenimiento_periodico($id) {
			$this->db->select("mpe.id, mpe.nombre_archivo nombre, mpe.comentario, mpe.fecha_registra");
			$this->db->from("mantenimiento_periodico_evidencia mpe");
			$this->db->where("mpe.id_mantenimiento = $id AND mpe.estado = 1");
			$query = $this->db->get();
			return $query->result_array();
		}

		public function listar_evidencia_matto_gest($id_objeto, $id_historial){
			$this->db->select("om.*, vp.valor objeto");
			$this->db->from('mantenimiento_preventivo_evidencias om');
			$this->db->join('valor_parametro vp', 'om.id_objeto = vp.id');
			if(!empty($id_objeto)) $this->db->where("om.id_objeto = $id_objeto");
			$this->db->where("om.id_historial_preventivo = $id_historial and om.estado = 1");
			$query = $this->db->get();
			return $query->result_array();
		}

		public function listar_mantenimiento_periodico_filtro($id_lugar='', $id_periodicidad='', $estado='', $id_tipo='', $fecha_inicio='', $fecha_fin=''){
			$this->db->select("vp1.valor lugar, vp2.valor periodicidad, est.valor estado, tipo.valor tipo, mph.fecha_inicio, mph.fecha_fin, mph.id");
			$this->db->from('mantenimiento_periodico_historial mph');
			$this->db->join("valor_parametro est", "est.id_aux = id_estado_lugar AND est.estado = 1", "left");
			$this->db->join("mantenimiento_periodicos_lugares mpl", "mpl.id_historial_periodico = mph.id AND mpl.estado = 1", "left");
			$this->db->join("valor_parametro vp1", "mpl.id_lugar = vp1.id", "left");
			$this->db->join("solicitud_mantenimiento sm1", "sm1.id = mph.id_mantenimiento_periodico AND sm1.estado = 1", "left");
			$this->db->join("valor_parametro vp2", "sm1.id_periodicidad = vp2.id", "left");
			$this->db->join("solicitud_mantenimiento sm2", "sm2.id = mph.id_mantenimiento_periodico AND sm2.estado = 1", "left");
			$this->db->join("valor_parametro tipo", "tipo.id = mph.id_mantenimiento_periodico AND tipo.estado = 1", "left");
			if (!empty($id_lugar)) $this->db->where("mpl.id_lugar", $id_lugar);
			if (!empty($id_periodicidad)) $this->db->where("sm1.id_periodicidad", $id_periodicidad);
			if (!empty($id_tipo)) $this->db->where("sm2.tipo_mantenimiento",$id_tipo);
			if (!empty($estado)) $this->db->where('mph.id_estado_lugar', $estado);
			
			if (!empty($fecha_inicio) && !empty($fecha_fin)){
				 $this->db->where("DATE_FORMAT(mph.fecha_inicio, '%Y-%m') >= '$fecha_inicio' AND DATE_FORMAT(mph.fecha_inicio, '%Y-%m') <= '$fecha_fin'");
			}
			$this->db->where('mph.estado', 1);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function listar_mantenimiento_gestion_filtro($id_lugar = '', $id_ubicacion= '', $estado= '', $fecha_inicio= '', $fecha_fin= '', $id_estado_objeto=''){
			$this->db->select("mp.*, vp.valor lugar, ub.valor ubicacion, es.valor estado");
			$this->db->from('mantenimiento_preventivo_historial mp');
			$this->db->join('valor_parametro vp', 'mp.id_lugar = vp.id', 'left');
			$this->db->join('valor_parametro ub', 'mp.id_ubicacion = ub.id', 'left');
			$this->db->join('valor_parametro es', 'mp.id_estado_matto = es.id_aux', 'left');
			if (!empty($id_lugar)) $this->db->where('mp.id_lugar', $id_lugar);
			if (!empty($id_ubicacion)) $this->db->where('mp.id_ubicacion', $id_ubicacion);
			if (!empty($estado)) $this->db->where('mp.id_estado_matto', $estado);
			if (!empty($fecha_inicio) && !empty($fecha_fin)){
				 $this->db->where("DATE_FORMAT(mp.fecha_inicio, '%Y-%m') >= '$fecha_inicio' AND DATE_FORMAT(mp.fecha_inicio, '%Y-%m') <= '$fecha_fin'");
			}
			if(!empty($id_estado_objeto)){
				$this->db->join("mantenimiento_estados_objetos meo", "meo.id_historial_preventivo = mp.id AND meo.id_estado_objeto = $id_estado_objeto");
			}
			$this->db->where('mp.estado', 1);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function consultar_evidencia($id_lugar, $id_ubicacion, $id_historial) {
			$this->db->select("ev.*");
			$this->db->from('mantenimiento_preventivo_evidencias ev');
			$this->db->join('objetos_mantenimiento ob', 'ob.id_objeto_mantenimiento = ev.id_objeto');
			$this->db->where("ob.id_lugar = $id_lugar and ob.id_ubicacion = $id_ubicacion and ob.estado = 1");
			$this->db->where('ev.id_historial_preventivo',$id_historial);
			$query = $this->db->get();
			return $query->result_array();			
		}

		public function existe_estado_objeto ($id_objeto, $id_lugar, $id_ubicacion, $id_historial){
			$this->db->select("count(meo.id_estado_objeto) cantidad, meo.id", false);
			$this->db->from("mantenimiento_estados_objetos meo");
			$this->db->where("meo.estado = 1 AND meo.id_lugar = $id_lugar AND meo.id_ubicacion = $id_ubicacion AND meo.id_objeto_mantenimiento = $id_objeto AND meo.id_historial_preventivo = $id_historial");
			$query = $this->db->get();
			return $query->result_array();
		}

		public function validar_adjuntos ($id_historial) {
			$this->db->select("meo.id_objeto_mantenimiento, (SELECT COUNT(mpe2.id)FROM mantenimiento_preventivo_evidencias mpe2 WHERE mpe2.id_historial_preventivo = meo.id_historial_preventivo AND mpe2.id_objeto = meo.id_objeto_mantenimiento AND mpe2.estado = 1) cant_evidencias, vp.id_aux estado");
			$this->db->from("mantenimiento_estados_objetos meo");
			$this->db->join("mantenimiento_preventivo_evidencias mpe", "mpe.id_historial_preventivo = meo.id_historial_preventivo AND mpe.id_objeto = meo.id_objeto_mantenimiento AND mpe.estado = 1", "left");
			$this->db->join("valor_parametro vp", "vp.id = meo.id_estado_objeto AND vp.estado = 1", "left");
			$this->db->where("meo.id_historial_preventivo = $id_historial");
			$this->db->group_by("meo.id");
			$query = $this->db->get();
			return $query->result_array();
		}

		public function iniciar_mantenimiento ($id_ubicacion, $id_lugar, $id_historial) {
			$this->db->select("om.id, vp1.valor nombre_obj, vp2.valor estado_obj, om.id_objeto_mantenimiento");
			$this->db->from("objetos_mantenimiento om");
			$this->db->join("valor_parametro vp1", "vp1.id = om.id_objeto_mantenimiento AND vp1.estado = 1");
			$this->db->join("mantenimiento_estados_objetos meo", "meo.id_objeto_mantenimiento = om.id_objeto_mantenimiento AND meo.estado = 1 AND meo.id_historial_preventivo = $id_historial", 'left');
			$this->db->join("valor_parametro vp2", "vp2.id = meo.id_estado_objeto AND vp2.estado = 1", 'left');
			$this->db->where("om.id_ubicacion = $id_ubicacion AND om.id_lugar = $id_lugar ANd om.estado = 1");
			$query = $this->db->get();
			return $query->result_array();
		}

		// public function validar_existencia ($id_lugar, $id_periodo) {
		// 	$this->db->select("count(mp.id) cantidad,mp.estado_solicitud, mp.id");
		// 	$this->db->from("mantenimiento_periodicos mp");
		// 	$this->db->where("mp.id_periodo = $id_periodo AND mp.id_lugar = $id_lugar AND mp.estado = 1");
		// 	$this->db->order_by("id", "DESC");
		// 	$this->db->limit(1);
		// 	$query = $this->db->get();
		// 	return $query->result_array();
		// }

		public function obtener_data_solicitud ($select, $table, $where, $limit, $order, $tipo){
			$this->db->select($select);
			$this->db->from($table);
			$this->db->where($where);
			if (!empty($order) && !empty($tipo)) $this->db->order_by("$order", "$tipo");
			if (!empty($limit)) $this->db->limit($limit);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function obtener_ultimos_lugares ($id_mantenimiento_periodico, $id_historial_periodico) {
			$this->db->select("mpl.id, mpl.id_lugar");
			$this->db->from("mantenimiento_periodicos_lugares mpl");
			$this->db->where("mpl.id_mantenimiento_periodico = $id_mantenimiento_periodico AND mpl.id_historial_periodico = $id_historial_periodico ANd mpl.estado = 1");
			$query = $this->db->get();
			return $query->result_array();
		}
	}
