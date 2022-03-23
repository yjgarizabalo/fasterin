<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class almacen_model extends CI_Model
{
	var $table_solicitud_almacen = "solicitudes_almacen";
	var $table_articulo_almacen = "articulos_almacen";
	var $table_articulos_solicitud_almacen = "articulos_solicitud_almacen";
	var $table_historial = "articulo_historial";
	var $table_permisos_parametros = "permisos_parametros";
	var $table_valor_parametro = "valor_parametro";
	
	// Guarda los artículo en el inventario
	public function guardar_articulo($codigo, $nombre, $categoria, $bodega, $cantidad, $stock, $marca, $referencia, $valor, $usuario, $observaciones, $tipo_modulo, $unidades)
	{
		$this->db->insert($this->table_articulo_almacen, array(
			"codigo" => strtoupper($codigo),
			"nombre_articulo" => $nombre,
			"cantidad" => $cantidad,
			"categoria" => $categoria,
			"bodega" => $bodega,
			"min_stock" => $stock,
			"marca" => $marca,
			"referencia" => $referencia,
			"valor" => $valor,
			"usuario_crea" => $usuario,
			"observacion" => $observaciones,
			"tipo_modulo" => $tipo_modulo,
			"unidades" => $unidades,
		));
		$error = $this->db->_error_message(); 
		if ($error) return "error";
		return 1;
	}

	//Función para guardar nuevas solicitudes
	public function guardar_solicitud($tipo_modulo)
	{
		$usuario = $_SESSION['persona'];
		$this->db->insert($this->table_solicitud_almacen, array(
		"id_solicitante" => $usuario,
		"usuario_crea" => $usuario,
		"tipo_modulo" => $tipo_modulo,
		//El estado de la solicitud por defecto es 'Recibida'
		));
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		$id = $this->traer_id_ultima_solicitud($usuario);
		$this->historial_solicitud($id, 'Alm_Rec', "");
		return $id;
	}

	// Guarda todos los artículos que sean agregados al crear la solicitud
	public function guardar_articulos($arts)
	{
		$this->db->insert_batch($this->table_articulos_solicitud_almacen, $arts);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return 1;
	}

	public function traer_id_ultima_solicitud($person)
	{
		$this->db->select("id");
		$this->db->from("solicitudes_almacen");
		$this->db->order_by("id", "desc");
		$this->db->where('usuario_crea', $person);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row->id;
	}

	public function Listar_solicitudes($estado, $mes, $id, $tipo_modulo){
		$sw = false;
		if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Alm") $sw = true;
		$this->db->select("sa.id, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname, sa.fecha_registra AS fecha, vp1.valor AS estado, d.valor as departamento, sa.id_estado_solicitud AS state, sa.stars, p.id AS resp, IF(sa.id_estado_solicitud = 'Alm_Den',(SELECT esa.comment FROM estados_solicitudes_almacen esa WHERE sa.id = esa.id_solicitud AND esa.id_estado = 'Alm_Den'), (SELECT esa.fecha FROM solicitudes_almacen sa1 LEFT JOIN estados_solicitudes_almacen esa ON esa.id_solicitud = sa1.id AND esa.id_estado = 'Alm_Ent' WHERE sa1.id = sa.id)) fecha_entrega, sa.comentario, sa.tiempo_entrega AS time,sa.firma, p.usuario, CONCAT(r.nombre, ' ', r.apellido, ' ', r.segundo_apellido) AS f_fullname, sa.observaciones", false);
		$this->db->from('solicitudes_almacen sa');
		$this->db->join('personas p', 'sa.id_solicitante = p.id');
		$this->db->join('personas r', 'sa.p_firma = r.id', 'left');
		$this->db->join('valor_parametro vp1', 'sa.id_estado_solicitud = vp1.id_aux');
		$this->db->join('valor_parametro d', 'p.id_cargo_sap = d.id', 'left');
		if (is_numeric($id)) {
			$id > -1 ? $this->db->where("sa.id", $id) : $this->db->where("sa.id_estado_solicitud = 'Alm_Ent'");
			if (!$sw) $this->db->where('sa.id_solicitante', $_SESSION['persona']);
		}else{
			if(!$sw){
				$this->db->where('sa.id_solicitante', $_SESSION['persona']);
				$this->db->where("sa.fecha_registra LIKE '%$mes%' AND sa.id_estado_solicitud LIKE '$estado'");
			}else{
				if ($estado == "%%" && $mes == "") {
					$this->db->where("sa.id_estado_solicitud LIKE 'Alm_Rec' OR sa.id_estado_solicitud LIKE 'Alm_Mer'");
				}else{
					$estado != "%%" ? $this->db->where("sa.id_estado_solicitud LIKE '$estado'"): $this->db->where("sa.id_estado_solicitud LIKE '%%'");
				}
				$this->db->where("sa.fecha_registra LIKE '%$mes%'");
			}
		}
		$this->db->where('sa.tipo_modulo', $tipo_modulo);
		$this->db->where('sa.estado', 1);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function Listar_articulos($accion, $categoria, $bodega, $tipo_modulo){
		$this->db->select("aa.id, aa.codigo, aa.min_stock AS stock, cat.valor AS categoria, aa.categoria id_categoria, nombre_articulo AS nombre, marca, referencia, cantidad, bod.valor AS bodega, aa.bodega id_bodega, aa.valor, aa.observacion, aa.min_stock, aa.unidades unidades_id, uni.valor unidades", false);
        $this->db->from('articulos_almacen aa');
		$this->db->join('valor_parametro bod', 'aa.bodega = bod.id');
		$this->db->join('valor_parametro uni', 'aa.unidades = uni.id');
		$this->db->join('valor_parametro cat', 'aa.categoria = cat.id_aux');
		if ($accion == 2) {
			$this->db->where('aa.cantidad <= aa.min_stock');
		}
		$this->db->where("aa.categoria LIKE '$categoria'");
		$this->db->where("aa.bodega LIKE '$bodega'");
		$this->db->where("aa.tipo_modulo", $tipo_modulo);
		$this->db->where("aa.estado", 1);
		$this->db->where("bod.estado", 1);
		$this->db->where("uni.estado", 1);
		$this->db->where("cat.estado", 1);
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result_array() : [];
	}

	public function Listar_articulos_solicitud($id){
		$this->db->select("aa.id AS codigo, aa.unidades unidades_id, uni.valor unidades, aa.codigo AS code, aa.nombre_articulo AS nombre, aa.marca, aa.referencia, asa.cantidad, asa.observacion, asa.id AS id");
        $this->db->from('articulos_solicitud_almacen asa');
		$this->db->join('articulos_almacen aa', 'asa.id_articulo = aa.id');
		$this->db->join('valor_parametro uni', 'uni.id = aa.unidades');
		$this->db->join('solicitudes_almacen sa', 'asa.id_solicitud = sa.id');
		$this->db->where('asa.id_solicitud', $id);
		$this->db->where('asa.estado', 1);
		$this->db->where('aa.estado', 1);
		$this->db->where('uni.estado', 1);
		$this->db->where('sa.estado', 1);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function Listar_historial($id){
		$this->db->select("ae.id, ae.cantidad, ae.cantidad_anterior AS c_anterior, ae.observacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname, ae.fecha", false);
        $this->db->from('articulo_historial ae');
		$this->db->join('personas p', 'p.id = ae.id_usuario');
		$this->db->where('ae.id_articulo', $id);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function historial_estados($id){
		$this->db->select("vp.valor AS estado, fecha, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname", false);
        $this->db->from('estados_solicitudes_almacen esa');
		$this->db->join('valor_parametro vp', 'esa.id_estado = vp.id_aux');
		$this->db->join('personas p', 'esa.id_usuario = p.id');
		$this->db->where('esa.id_solicitud', $id);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function Traer_solicitud($id){
		$this->db->select("sa.id, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname, sa.fecha_registra AS fecha, vp1.valor AS estado, d.valor as departamento", false);
        $this->db->from('solicitudes_almacen sa');
        $this->db->join('personas p', 'sa.id_solicitante = p.id');
		$this->db->join('valor_parametro vp1', 'sa.id_estado_solicitud = vp1.id_aux');
		$this->db->join('cargos_departamentos cd', 'p.id_cargo = cd.id');
		$this->db->join('valor_parametro d', 'cd.id_departamento = d.id');
		$this->db->where('sa.id', $id);
		$this->db->where('sa.estado', '1');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function Traer_articulo($id){
		$this->db->select("aa.id, aa.codigo, aa.categoria, nombre_articulo AS nombre, marca, aa.min_stock AS stock, referencia, cantidad, aa.bodega, aa.valor, aa.observacion", false);
        $this->db->from('articulos_almacen aa');
		$this->db->where('aa.id', $id);
		$this->db->where('aa.estado', 1);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function Traer_info_solicitud($id){
		$this->db->select("sa.id as codigo, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname, sa.fecha_registra AS fecha, vp1.valor AS estado, d.valor as departamento, sa.observaciones", false);
        $this->db->from('solicitudes_almacen sa');
        $this->db->join('personas p', 'sa.id_solicitante = p.id');
		$this->db->join('valor_parametro vp1', 'sa.id_estado_solicitud = vp1.id_aux');
		$this->db->join('cargos_departamentos cd', 'p.id_cargo = cd.id');
		$this->db->join('valor_parametro d', 'cd.id_departamento = d.id');
		$this->db->where('sa.id', $id);
		$this->db->where('sa.estado', 1);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function Traer_info_articulo($id){
		$this->db->select("aa.nombre_articulo AS nombre, aa.codigo, cat.valor AS categoria, aa.cantidad, bod.valor AS bodega, aa.marca, aa.referencia, aa.valor, aa.observacion", false);
        $this->db->from('articulos_almacen aa');
		$this->db->join('valor_parametro bod', 'aa.bodega = bod.id');
		$this->db->join('valor_parametro cat', 'aa.categoria = cat.id');
		$this->db->where('aa.id', $id);
		$this->db->where('aa.estado', 1);
		$this->db->where('bod.estado', 1);
		$this->db->where('cat.estado', 1);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function Modificar_solicitud($id, $nombre, $depar, $observaciones, $estado){
		$data = array(
			'nombre' => $nombre,
			'id_departamento' => $depar,
			'observaciones' => $observaciones,
			'id_estado_solicitud' => $estado,
		);
		$this->db->where('id', $id);
		$this->db->update($this->table_solicitud_almacen, $data);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return 1;
	}

	public function Modificar_articulo($id, $codigo, $nombre, $categoria, $bodega, $marca, $referencia, $stock, $valor, $observacion, $unidades=''){
		$data = array(
			'nombre_articulo' => $nombre,
			'codigo' => $codigo,
			'categoria' => $categoria,
			'bodega' => $bodega,
			'marca' => $marca,
			'min_stock' => $stock,
			'referencia' => $referencia,
			'valor' => $valor,
			'observacion' => $observacion,
			'unidades' => $unidades,
		);
		$this->db->where('id', $id);
		$this->db->update($this->table_articulo_almacen, $data);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return 1;
	}

	public function buscar_articulo($art, $tipo_modulo){
		if ($_SESSION['perfil'] == 'Per_Admin' || $_SESSION['perfil'] == 'Per_Alm') {
			$this->db->select("aa.id, aa.codigo, aa.nombre_articulo AS nombre, aa.marca, aa.referencia, uni.valor unidades", false);
			$this->db->from('articulos_almacen aa');
			$this->db->join('valor_parametro uni', 'uni.id = aa.unidades');
			$this->db->where("aa.nombre_articulo LIKE '%$art%'");
			$this->db->where('aa.tipo_modulo', $tipo_modulo);
			$this->db->where('aa.estado', 1);
			$this->db->where('uni.estado', 1);
			$query = $this->db->get();
			return $query->result_array();
		}else{
			$query = $this->db->query("(SELECT aa.id, aa.codigo, aa.nombre_articulo AS nombre, aa.marca, aa.referencia, uni.valor unidades
			FROM articulos_almacen aa
			INNER JOIN valor_parametro cat ON aa.categoria = cat.id_aux
			INNER JOIN valor_parametro uni ON uni.id = aa.unidades
			WHERE aa.categoria LIKE (SELECT IF ((vp.id_aux <> 'Dep_Man'), 'Art_Gen', '%%')
				FROM personas p 
				LEFT JOIN cargos_departamentos cd ON cd.id=p.id_cargo 
				LEFT JOIN valor_parametro vp ON vp.id=cd.id_departamento 
				WHERE p.id = " . $_SESSION['persona'] . "
				AND p.estado = 1)
			AND aa.tipo_modulo = '$tipo_modulo'
			AND aa.nombre_articulo LIKE '%$art%'
			AND aa.estado = 1
			AND cat.estado = 1
			AND uni.estado = 1)");
		}
		return $query->result_array();
	}

	public function validar_codigo($codigo){
		$this->db->select("count(id) as res");
		$this->db->from('articulos_almacen');
		$this->db->where('codigo', $codigo);
		$this->db->where('estado', "1");
		$query = $this->db->get();
		$row = $query->row();
		return $row->res;
	}

	public function modificar_articulo_solicitud($id, $cantidad, $observaciones){
		$data = array(
			'cantidad' => $cantidad,
			'observacion' => $observaciones
		);
		$this->db->where('id', $id);
		$this->db->update($this->table_articulos_solicitud_almacen, $data);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		$resp = $this->validar_existencia($id, $cantidad);
		if ($resp) {
			return 5;
		}
		return 4;
	}

	public function validar_existencia($id, $cantidad){
		$this->db->select("COUNT(id) AS res");
		$this->db->from('articulos_almacen');
		$this->db->where('id', 1);
		$this->db->where('cantidad < min_stock');
		$this->db->or_where('cantidad < ' . $cantidad);
		$this->db->where('estado', 1);
		$query = $this->db->get();
		$row = $query->row();
		if ($row->res > 0) {
			return true;
		}else{
			return false;
		}
	}

	public function cambiar_cant_articulo($articulo, $cant, $obs, $op){
		$stock_actual = $this->get_existencia_articulo($articulo);
		$usuario = $_SESSION['persona'];
		if ($op == 'sum') {
			$this->db->set('cantidad', "cantidad + '".$cant."'", false);
		}else{
			if ($stock_actual < $cant) return 0;
			$this->db->set('cantidad', "cantidad - '".$cant."'", false);
		}
		$this->db->where('id', $articulo);
		$this->db->update($this->table_articulo_almacen);
		$error = $this->db->_error_message(); 
		if ($error) return "error";
		$this->db->insert($this->table_historial, array(
			"id_articulo" => $articulo,
			"id_usuario" => $usuario,
			"cantidad_anterior" => $stock_actual,
			"cantidad" => ($op == 'res') ? ($stock_actual - $cant) : ($stock_actual + $cant),
			"observacion" => $obs,
		));
		$error = $this->db->_error_message(); 
		if ($error) return "error";
		return 1;
	}

	public function get_existencia_articulo($art){
		$this->db->select("cantidad");
		$this->db->from('articulos_almacen');
		$this->db->where('id', $art);
		$this->db->where('estado', 1);
		$query = $this->db->get();
		$row = $query->row();
		return (int)$row->cantidad;
	}

	public function get_estado_solicitud($id){
		$this->db->select("id_estado_solicitud as state, id_solicitante as user");
		$this->db->from('solicitudes_almacen');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function cambiar_estado_solicitud($id, $state, $firma = null, $comment = null){
		$info = $this->get_estado_solicitud($id);
		$estado = $info->{'state'};
		if (($estado == 'Alm_Rec' && ($state == 'Alm_Ent' || $state == 'Alm_Den' || $state == 'Alm_Can' || $state == 'Alm_Mer')) || ($estado == 'Alm_Ent' && ($state == 'Alm_Cer')) || ($estado == 'Alm_Mer' && $state == 'Alm_Ent')) {
			$this->db->set('id_estado_solicitud', $state);
			if(!is_null($firma)) $this->db->set('firma', $firma);
			if($state == 'Alm_Den') $this->db->set('observaciones', $comment);
			$this->db->where('id', $id);
			$this->db->update($this->table_solicitud_almacen);
			$error = $this->db->_error_message(); 
			if ($error) {
				return "error";
			}
			$this->historial_solicitud($id, $state, "");
			return 1;
		}
		return -3;
	}

	public function get_cantidad_solicitada($id){
		$this->db->select('SUM(asa.cantidad) AS cant');
		$this->db->from('articulos_solicitud_almacen asa');
		$this->db->join('solicitudes_almacen sa', 'asa.id_solicitud = sa.id');
		$this->db->where('asa.id_articulo', $id);
		$this->db->where('id_estado_solicitud', 'Alm_Rec');
		$this->db->where('asa.estado', 1);
		$this->db->where('sa.estado', 1);
		$query = $this->db->get();
		$row = $query->row();
		return $row->cant;
	}

	public function get_cant_solicitada($sol){
		$this->db->select('asa.cantidad AS cant');
		$this->db->from('articulos_solicitud_almacen asa');
		$this->db->where('asa.id', $sol);
		$this->db->where('asa.estado', 1);
		$query = $this->db->get();
		$row = $query->row();
		return $row->cant;
	}

	public function get_stock_minimo($id){
		$this->db->select('min_stock');
		$this->db->from('articulos_almacen');
		$this->db->where('id', $id);
		$this->db->where('estado', 1);
		$query = $this->db->get();
		$row = $query->row();
		return $row->min_stock;
	}

	public function descontar_cantidad($id, $cant){
		$this->db->set('cantidad', "cantidad - '".$cant."'", false);
		$this->db->where('id', $id);
		$this->db->update($this->table_articulo_almacen);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
	}

	public function cambiar_estado_articulo($id, $state){
		$this->db->set('estado', $state);
		$this->db->where('id', $id);
		$this->db->update($this->table_articulo_almacen);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
	}

	public function calificar_solicitud($id, $rating, $obs){
		$this->db->set('stars', $rating);
		$this->db->set('comentario', $obs);
		$this->db->set('id_estado_solicitud', "Alm_Cer");
		$this->db->where('id', $id);
		$this->db->update($this->table_solicitud_almacen);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		$this->historial_solicitud($id, 'Alm_Cer', "");
		return 1;
	}

	public function eliminar_articulo($id){
		$this->db->set('estado', -1);
		$this->db->where('id', $id);
		$this->db->update($this->table_articulos_solicitud_almacen);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return 1;
	}

	public function get_limite(){
		$this->db->select('valor AS limite');
		$this->db->from('valor_parametro');
		$this->db->where('id_aux', 'Alm_Lim');
		$query = $this->db->get();
		$row = $query->row();
		return $row->limite;
	}

	public function agregar_articulo_solicitud($id, $articulo, $cantidad, $observaciones){
		$data = array(
			'id_solicitud' => $id,
			'id_articulo' => $articulo,
			'cantidad' => $cantidad,
			'observacion' => $observaciones
		);
		$this->db->insert($this->table_articulos_solicitud_almacen, $data);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return 6;
	}

	public function historial_solicitud($id, $estado, $comment){
		$this->db->insert('estados_solicitudes_almacen', array(
            "id_solicitud" => $id,
			"id_estado" => $estado,
			"id_usuario" => $_SESSION['persona'],
			"comment" => $comment,
		));
		return 1;
	}

	public function existe_articulo_solicitud($sol, $art){
		$this->db->select('COUNT(id) AS cant');
		$this->db->from('articulos_solicitud_almacen');
		$this->db->where('id_solicitud', $sol);
		$this->db->where('id_articulo', $art);
		$this->db->where('estado', 1);
		$query = $this->db->get();
		$row = $query->row();
		$cant = $row->cant;
		return ($cant > 0 ) ? true : false;
	}

	public function denegar_solicitud($id, $comment){
		$state = 'Alm_Den';
		$res = $this->cambiar_estado_solicitud($id, $state, null, $comment);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return $res;
	}

	public function onTime($id, $estado_fin){
		$this->db->select("(SELECT esa1.fecha FROM estados_solicitudes_almacen esa1 WHERE esa1.id_estado = 'Alm_Rec' AND esa1.id_solicitud = $id) AS f_recibido, (SELECT esa2.fecha FROM estados_solicitudes_almacen esa2 WHERE esa2.id_estado = '$estado_fin' AND esa2.id_solicitud = $id) AS f_entregado, (SELECT TIMESTAMPDIFF(HOUR,f_recibido, f_entregado)) AS diff_h", FALSE);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function calificarTiempo($id, $tiempo_diff){
		$this->db->select('valor AS horas');
		$this->db->from('valor_parametro');
		$this->db->where('idparametro', 20);
		$this->db->where('id_aux', 'Alm_Hor');
		$query = $this->db->get();
		$horas = $query->row()->horas;
		$this->db->set('tiempo_entrega', $tiempo_diff);
		$this->db->set('limite', $horas);
		$this->db->where('id', $id);
		$this->db->update($this->table_solicitud_almacen);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return 1;
	}

	public function solicitudes_sin_calificar($tipo_modulo){
		$this->db->select('COUNT(*) AS sin_calificar');
		$this->db->from('solicitudes_almacen');
		$this->db->where('id_solicitante',$_SESSION['persona']);
		$this->db->where('id_estado_solicitud', 'Alm_Ent');
		$this->db->where('tipo_modulo', "$tipo_modulo");
		$query = $this->db->get();
		$sin_calificar = $query->row()->sin_calificar;
		return $sin_calificar;
	}

	public function persona_entrega($id, $usuario){
		$this->db->set('p_firma', $usuario);
		$this->db->where('id', $id);
		$this->db->update($this->table_solicitud_almacen);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return 1;
	}

	public function listar_categorias(){
		$this->db->select('id, id_aux, valor as nombre');
		$this->db->from('valor_parametro');
		$this->db->where('idparametro','46');
		$this->db->where('estado', 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_permisos_por_parametro($vp_principal){
		$this->db->select('vp.id, vp.id_aux, vp.valor as nombre, pp.estado');
		$this->db->from('valor_parametro vp');
		$this->db->join('permisos_parametros pp'," pp.vp_secundario_id = vp.id AND pp.vp_principal = '$vp_principal'",'left');
		$this->db->where('vp.idparametro','39');
		$this->db->where('vp.estado', 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function gestionar_permiso($vp_principal, $vp_secundario, $id_p, $id_s){
		$permiso = $this->verificar_permiso($id_p, $id_s);
		$res = $permiso ? $this->asignar_permiso($vp_principal, $vp_secundario, $id_p, $id_s) : $this->quitar_permiso($id_p, $id_s);
		return $res;
	}

	public function asignar_permiso($vp_principal, $vp_secundario, $id_p, $id_s){
		$cons = array(
			"vp_principal_id" => $id_p,
			"vp_secundario_id" => $id_s,
		);
		if (!is_null($vp_principal) && !empty($vp_principal)) {
			$cons["vp_principal"] = $vp_principal;
		}
		if (!is_null($vp_secundario) && !empty($vp_secundario)) {
			$cons["vp_secundario"] = $vp_secundario;
		}
		$this->db->insert($this->table_permisos_parametros, $cons);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return 1;
	}

	public function quitar_permiso($id_p, $id_s){
		$this->db->delete($this->table_permisos_parametros, array('vp_principal_id' => $id_p, 'vp_secundario_id' => $id_s));
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return 2;
	}

	public function verificar_permiso($id_p, $id_s){
		$this->db->select('COUNT(*) AS cant');
		$this->db->from($this->table_permisos_parametros);
		$this->db->where('vp_principal_id', $id_p);
		$this->db->where('vp_secundario_id', $id_s);
		$query = $this->db->get();
		$cant = $query->row()->cant;
		return $cant ? 0 : 1;
	}

	public function get_where($tabla, $data){
		return $this->db->get_where($tabla, $data);
	}

	public function guardar_datos($data, $tabla, $tipo = 1){
		$tipo == 2 ? $this->db->insert_batch($tabla, $data) : $this->db->insert($tabla,$data);
		$error = $this->db->_error_message(); 
		return $error ? 1 :  0;
	}

	public function eliminar_datos($tabla , $id){
		$this->db->where('id', $id);
		$this->db->delete($tabla);
		$error = $this->db->_error_message(); 
		return $error ? "error" : 0;
	}

	public function listar_articulos_compras_por_bodega($bodega, $porcentaje){
		$this->db->select("aa.id, aa.codigo, aa.min_stock AS stock, nombre_articulo AS nombre, cantidad, bod.valor AS bodega, (SELECT count(*) FROM articulos_solicitud acomp JOIN solicitud_compra scomp ON scomp.id = acomp.id_solicitud WHERE acomp.id_almacen = aa.id AND acomp.estado= 1 AND (scomp.id_estado_solicitud != 'Soli_Fin' AND scomp.id_estado_solicitud !='Soli_Dev')) en_sol", false);
        $this->db->from('articulos_almacen aa');
		$this->db->join('valor_parametro bod', 'aa.bodega = bod.id');
		$this->db->where("(aa.cantidad*$porcentaje)<= aa.min_stock");
		$this->db->where("aa.categoria LIKE '%'");
		$this->db->where("bod.id_aux", $bodega);
		$this->db->where("aa.tipo_modulo", 'Inv_Alm');
		$this->db->where("aa.estado", 1);
		$this->db->where("bod.estado", 1);
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result_array() : [];
	}

	public function obtener_valores_permiso($vp_principal, $idparametro){
		$this->db->select("pp.vp_secundario_id id, upper(concat(vp.valor, ' - ', vp.valorx)) valor", false);
		$this->db->from("permisos_parametros pp");
		$this->db->join("valor_parametro vp", "vp.id = pp.vp_secundario_id AND vp.idparametro = $idparametro");
		$this->db->where('pp.vp_principal', $vp_principal);
		$this->db->where('pp.estado', 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_encuestas_soli_ent($id_usuario, $fecha_ini, $fecha_fin){
		$this->db->select("sa.id, sa.stars, sa.fecha_registra, CONCAT(ps.nombre, ' ', ps.apellido, ' ', ps.segundo_apellido) AS solicitante", false);
		$this->db->from("solicitudes_almacen sa");
		$this->db->join("personas ps", "ps.id = sa.id_solicitante");
		$this->db->join("estados_solicitudes_almacen esa", "esa.id_solicitud = sa.id");
		$this->db->where('esa.id_estado', 'Alm_Ent');
		//$this->db->where('esa.id_usuario', $id_usuario);
		if($fecha_ini != '') $this->db->where("DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') >= '$fecha_ini'");
		if($fecha_fin != '')$this->db->where("DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') <= '$fecha_fin'");
		$this->db->order_by('sa.fecha_registra', 'DESC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_num_solicitudes_pendientes(){
		$this->db->select("count(sa.id) as num", false);
		$this->db->from('solicitudes_almacen sa');
		$this->db->where('sa.id_estado_solicitud = "Alm_Rec" OR sa.id_estado_solicitud = "Alm_Mer"');
		$this->db->where('sa.estado', 1);
		$query = $this->db->get();
        return $query->result_array();
	}
}
?>
