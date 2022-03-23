<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class compras_model extends CI_Model
{
	var $table_solicitud_compra = "solicitud_compra";
	var $table_articulos_solicitud = "articulos_solicitud";

	public function guardar_solicitud($nombre_solicitud, $tipo_compra, $usuario, $observaciones, $jefe, $departamento, $fecha_solicitud, $adjunto)
	{
		$indice = $this->traer_indice_disponible_solicitud();
		$this->db->insert($this->table_solicitud_compra, array(
			"nombre_solicitud" => $nombre_solicitud,
			"id_tipo_compra" => $tipo_compra,
			"id_solicitante" => $usuario,
			"id_usuario_registra" => $usuario,
			"observaciones" => $observaciones,
			"fecha_solicitud" => $fecha_solicitud,
			"archivo_adjunto" => $adjunto,
			"indice_fecha" => $indice,
			"id_jefe_area" => $jefe
		));
		$solicitud = $this->traer_id_ultima_solicitud($usuario);
		$cambio = $this->guardar_cambio_estado($solicitud, "Soli_Rev");
		return $solicitud;
	}

	public function guardar_articulo($id_solicitud, $codigo_orden, $nombre_art, $marca_art, $referencia_art, $cantidad_art, $observaciones)
	{
		$this->db->insert($this->table_articulos_solicitud, array(
			"id_solicitud" => $id_solicitud,
			"cod_sap" => $codigo_orden,
			"nombre_articulo" => $nombre_art,
			"marca" => $marca_art,
			"referencia" => $referencia_art,
			"cantidad" => $cantidad_art,
			"observaciones" => $observaciones,
			"usuario_crea" => $_SESSION['persona'],
		));
		return 0;
	}

	public function Listar_solicitudes($tipo, $estado, $departamento, $fecha_filtro, $consulta, $sinencuesta, $fecha_filtro_2, $proveedor)
	{
		$persona = $_SESSION["persona"];

		/* Verifico que permisos de RP tiene la persona en session para mostrar las solicitudes o no. */
		$perRp = '';
		$rpPermiso = $this->permisos_compra_info($persona);
		if ($rpPermiso) {
			foreach ($rpPermiso as $permi) {
				if ($permi['id_tipo_encuesta'] != 'Tip_Ser') {
					$perRp = $permi['id_persona'];
					break;
				}
			}
		}
		$perm_crono = $this->obtener_permisos_cronogramas("", $persona);

		$per_compras =  $_SESSION["perfil"] == "Per_Adm_Com" || $_SESSION["perfil"] == "Per_Com" || $_SESSION["perfil"] == "Per_Alm" ? true : false;
		$per_admin =  $_SESSION["perfil"] == "Per_Admin" ? true : false;
		if (empty($fecha_filtro) && empty($fecha_filtro_2)) {
			$fecha_filtro = ($tipo == '%%' && $departamento == '%%' && $estado == '%%' && $consulta == -1  && $sinencuesta == -1 && ($per_admin || $per_compras)) ? $this->_data_first_month_day() : '1900-05-02';
			$fecha_filtro_2 = $this->_data_last_month_day();
		}
		$permiso = $per_compras ? 'esu.id as permiso' : 'NULL as permiso';
		$empleados_jefes = !$per_admin && !$per_compras ? $this->crear_query_jefes_solicitudes($_SESSION["persona"]) : '';
		$this->db->select("$permiso,sc.*, vptr.valor proveedor, vp.id_aux enc_type, tor.valor tipo_orden, u1.valor tipo_compra, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS solicitante, u.valor AS estado_solicitud,sc.id_estado_solicitud as estado_general, sc.id,pr.valor proveedor,sc.dias_no_habiles estimada_real,cmt.nombre nombre_comite,cmt.fecha_cierre fecha_cierre_comite,p.correo,csap.valor cargo_sap, CONCAT(pj2.nombre, ' ', pj2.apellido, ' ', pj2.segundo_apellido) AS jefe2, cdev.valor causal_dev", false);
		$this->db->from('solicitud_compra sc');
		$this->db->join('valor_parametro u', 'sc.id_estado_solicitud = u.id_aux');
		$this->db->join('valor_parametro vp', 'sc.id_area = vp.id', "left");
		$this->db->join('valor_parametro u1', 'sc.id_tipo_compra = u1.id_aux');
		$this->db->join('valor_parametro pr', 'sc.id_proveedor = pr.id', 'left');
		$this->db->join('valor_parametro tor', 'sc.id_tipo_orden= tor.id_aux', 'left');
		$this->db->join('valor_parametro cdev', 'sc.id_causal_dev = cdev.id_aux', 'left');
		$this->db->join('comites cmt', 'sc.id_comite = cmt.id', 'left');
		$this->db->join('personas p', 'sc.id_solicitante = p.id');
		$this->db->join('personas pj2', 'sc.id_jefe_area = pj2.id', 'left');
		$this->db->join('valor_parametro csap', 'csap.id = p.id_cargo_sap', 'left');
		$this->db->join('valor_parametro vptr', 'sc.id_proveedor=vptr.id', 'left');

		if ($per_compras || $per_admin) {
			$this->db->join('solicitudes_usuarios_com suc', 'sc.id_tipo_compra = suc.id_tipo_solicitud AND suc.id_usuario =' . $_SESSION['persona'], 'left');
			$this->db->join('estados_sol_usuarios esu', "suc.id = esu.id_solicitud_usuario AND esu.id_estado = sc.id_estado_solicitud", 'left');
		} else {
			$this->db->where('sc.id_solicitante', $persona);
		}

		$this->db->where('sc.estado_registro', "1");
		if (!$per_admin && !$per_compras) !empty($empleados_jefes) ? $this->db->where("($empleados_jefes OR sc.id_solicitante = $persona)") : $this->db->where('sc.id_solicitante', $persona);
		if ($consulta != -1)	$this->db->where("sc.id", $consulta);
		if ($sinencuesta == 1) $this->db->where("sc.id_estado_solicitud = 'Soli_Fin' AND sc.fecha_fin_encuesta IS NULL AND sc.id_solicitante = " . $persona);
		if (!empty($proveedor)) {
			$this->db->where("sc.id_proveedor", $proveedor);
		}
		$this->db->where("(sc.estado_registro = 1 AND sc.id_tipo_compra LIKE '$tipo' AND sc.id_estado_solicitud LIKE '$estado' AND (DATE_FORMAT(sc.fecha_solicitud,'%Y-%m-%d') >= '$fecha_filtro' AND DATE_FORMAT(sc.fecha_solicitud,'%Y-%m-%d') <= '$fecha_filtro_2'))");
		if($_SESSION['persona'] == $perRp){
			foreach ($rpPermiso as $permi) {
				if ($permi['id_tipo_encuesta'] == 'sst_enc') {
					$this->db->or_where('sc.estado_encuesta_sst IS NOT NULL');
					$this->db->where('sc.id_estado_solicitud', 'Soli_Fin');	
				}

				if ($permi['id_tipo_encuesta'] == 'sga_enc') {
					$this->db->or_where('sc.estado_encuesta_sga IS NOT NULL');
					$this->db->where('sc.id_estado_solicitud', 'Soli_Fin');	
				}

				if ($permi['id_tipo_encuesta'] == 'Tip_Mat') {
					$this->db->or_where('sc.estado_encuesta_tipmat IS NOT NULL');
				}
			}			
		}

		if(!empty($perm_crono)){
			$this->db->join('compra_permisos_cronograma cpc', 'cpc.estado = sc.estado_registro AND cpc.id_persona =' . $_SESSION['persona'], 'left');
			$this->db->join('compras_cronograma comp_crono', 'comp_crono.estado_cronograma = cpc.id_estado', 'left');
			$this->db->or_where('comp_crono.id_solicitud = sc.id');			
		}
		$this->db->_protect_identifiers = false;
		$this->db->order_by("FIELD (sc.id_estado_solicitud,'Soli_Rev','Soli_Rec','Soli_Cot','Soli_Lib','Soli_Ord','Soli_Pen','Soli_Cac','Soli_Pre','Soli_Com','Soli_Oco','Soli_Par','Soli_Pro','Soli_Fin','Soli_Dev')");
		//$this->db->order_by("sc.fecha_solicitud", "ASC");
		$this->db->_protect_identifiers = true;
		$this->db->group_by('sc.id');
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $query->result_array();
	}

	/* Sacar el promedio que lleva cada proveedor en total segun todas las solicitudes aquii */
	public function promediar_proveedores($id_proveedor, $fecha_ini = "", $fecha_fin = "")
	{
		$this->db->select("cs.id, cs.id_proveedor proveedor, cs.dias_no_habiles, cs.fecha_entrega_real, cs.resultado_final_rp resultado_final, cs.estado_encuesta_sga res_sga, cs.estado_encuesta_tipmat res_tipmat, cs.estado_encuesta_tipserv res_tipserv, cs.estado_encuesta_sst res_sst, vp.valor nombre_proveedor", false);
		$this->db->from("solicitud_compra cs");
		$this->db->join("valor_parametro vp", "cs.id_proveedor=vp.id");
		if (!empty($id_proveedor)) {
			$this->db->where("cs.id_proveedor", $id_proveedor);
		}
		if (!empty($fecha_ini) and !empty($fecha_fin)) {
			$this->db->where("(cs.fecha_registra >= '$fecha_ini')");
			$this->db->where("(cs.fecha_registra <= '$fecha_fin')");
		}
		$this->db->where("(cs.id_area IS NOT NULL)");
		$this->db->where("cs.id_estado_solicitud", "Soli_Fin");
		$this->db->where("(cs.resultado_final_rp IS NOT NULL)");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Sacar el promedio que lleva cada proveedor en total segun todas las solicitudes aquii */
	public function solicitudes_promedo_proveedores($id_proveedor)
	{
		$this->db->select("sc.*, vptr.valor proveedor, vp.id_aux enc_type, tor.valor tipo_orden, u1.valor tipo_compra, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS solicitante, u.valor AS estado_solicitud,sc.id_estado_solicitud as estado_general, sc.id,pr.valor proveedor,sc.dias_no_habiles estimada_real,cmt.nombre nombre_comite,cmt.fecha_cierre fecha_cierre_comite,p.correo,csap.valor cargo_sap,CONCAT(pj.nombre, ' ', pj.apellido, ' ', pj.segundo_apellido) AS jefe_encargado, CONCAT(pj2.nombre, ' ', pj2.apellido, ' ', pj2.segundo_apellido) AS jefe2, cdev.valor causal_dev", false);
		$this->db->from('solicitud_compra sc');
		$this->db->join('valor_parametro u', 'sc.id_estado_solicitud = u.id_aux');
		$this->db->join('valor_parametro vp', 'sc.id_area = vp.id', "left");
		$this->db->join('valor_parametro u1', 'sc.id_tipo_compra = u1.id_aux');
		$this->db->join('valor_parametro pr', 'sc.id_proveedor = pr.id', 'left');
		$this->db->join('valor_parametro tor', 'sc.id_tipo_orden= tor.id_aux', 'left');
		$this->db->join('valor_parametro cdev', 'sc.id_causal_dev = cdev.id_aux', 'left');
		$this->db->join('comites cmt', 'sc.id_comite = cmt.id', 'left');
		$this->db->join('personas p', 'sc.id_solicitante = p.id');
		$this->db->join('personas pj2', 'sc.id_jefe_area = pj2.id', 'left');
		$this->db->join('cargos_departamentos c', 'p.id_cargo=c.id', 'left');
		$this->db->join('valor_parametro csap', 'csap.id = p.id_cargo_sap', 'left');
		$this->db->join('valor_parametro vptr', 'sc.id_proveedor=vptr.id', 'left');
		$this->db->join('personas pj', 'c.id_cargo_jefe = pj.id_cargo', 'left');
		$this->db->where("sc.id_proveedor", $id_proveedor);
		$this->db->where("(sc.id_area IS NOT NULL)");
		$this->db->where("sc.id_estado_solicitud", "Soli_Fin");
		$this->db->where("(sc.resultado_final_rp IS NOT NULL)");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Sacar el promedio que lleva cada proveedor en total segun todas las solicitudes aquii */
	public function proveedores_sin_encuesta($fecha_ini = "", $fecha_fin = "")
	{
		$this->db->select("
		cs.id,
		cs.indice_fecha no_s,
		cs.id_usuario_registra solicitante,
		cs.fecha_registra fecha_sol,
		vp.valor tipo_orden,
		tc.valor tipoCompra,
		CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) solicitante,
		CONCAT(pp.nombre, ' ', pp.apellido, ' ', pp.segundo_apellido) solicitanteName,
		cs.id_proveedor proveedor,
		cs.estado_encuesta_sga sga,
		cs.estado_encuesta_tipmat tipmat,
		cs.estado_encuesta_tipserv tipser,
		cs.estado_encuesta_sst sst,
		cs.resultado_final_rp rp_final_resul", false);

		$this->db->from("solicitud_compra cs");
		$this->db->join("valor_parametro vp", "cs.id_tipo_orden = vp.id_aux AND vp.estado = 1", "left");
		$this->db->join("personas p", "cs.id_usuario_registra = p.id AND p.estado = 1", "left");
		$this->db->join("personas pp", "cs.id_solicitante = pp.id AND pp.estado = 1", "left");
		$this->db->join("valor_parametro tc", "tc.id_aux = cs.id_tipo_compra AND tc.estado = 1", "left");

		if (!empty($fecha_ini) and !empty($fecha_fin)) {
			$this->db->where("(cs.fecha_registra >= '$fecha_ini')");
			$this->db->where("(cs.fecha_registra <= '$fecha_fin')");
		}

		$this->db->where("cs.id_estado_solicitud", "Soli_Fin");
		$this->db->where("(cs.id_area IS NOT NULL)");
		$this->db->where("(cs.resultado_final_rp IS NULL)");
		$this->db->where("vp.idparametro", 72); //Aquii
		$this->db->group_by("cs.id", "DESC");
		$this->db->order_by("cs.fecha_registra", "DESC");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function Listar_solicitudes_en_comite($comite, $directivos = -1, $alt = -1)
	{
		$exec = '';
		$sw = false;
		if ($_SESSION['perfil'] == 'Per_Dir_t2') {
			$exec = $this->crear_query_lista_solicitudes_comite();
			$sw = true;
		}
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS solicitante,com.id_estado_comite,sc.*,(	SELECT COUNT(pb.id) vb FROM solicitud_proveedores sp LEFT JOIN proveedores_aprobados pb on pb.id_proveedor = sp.id AND pb.estado = 1 INNER JOIN personas p ON pb.usuario_registra = p.id AND p.id_perfil = 'Per_Dir' WHERE sp.id_solicitud = sc.id) as vb", false);
		$this->db->from('solicitud_compra sc');
		$this->db->join('comites com', 'com.id = sc.id_comite');
		$this->db->join('personas p', 'sc.id_solicitante = p.id');
		if ($sw) 	$this->db->where("($exec)");
		$this->db->where("sc.id_comite =$comite AND sc.id_estado_solicitud <> 'Soli_Dev'");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function crear_query_lista_solicitudes_comite()
	{

		$this->db->select("sc.id_usuario, sc.id_tipo_solicitud");
		$this->db->from('solicitudes_usuarios_com sc');
		$this->db->where('sc.id_usuario', $_SESSION["persona"]);
		$query = $this->db->get();
		$filtro_solicitudes = $query->result_array();
		$execute = "";
		for ($x = 0; $x < count($filtro_solicitudes); $x++) {
			$tipo_sol = $filtro_solicitudes[$x]["id_tipo_solicitud"];
			$execute .= "(sc.id_tipo_compra = '$tipo_sol')";
			if ($x < count($filtro_solicitudes) - 1) $execute .= " OR ";
		}
		return $execute;
	}
	public function crear_query_lista_solicitudes($fecha_filtro, $tipo, $estado, $departamento, $fecha_filtro_2)
	{

		$this->db->select("sc.id_usuario, sc.id_tipo_solicitud, es.id_estado");
		$this->db->from('solicitudes_usuarios_com sc');
		$this->db->join('estados_sol_usuarios es', 'sc.id = es.id_solicitud_usuario');
		$this->db->where("sc.id_tipo_solicitud LIKE '$tipo' AND es.id_estado LIKE '$estado'");
		$this->db->where('sc.id_usuario', $_SESSION["persona"]);
		$this->db->where('sc.estado', '1');
		$this->db->where('es.estado', '1');
		$query = $this->db->get();
		$filtro_solicitudes = $query->result_array();

		$execute = "";
		for ($x = 0; $x < count($filtro_solicitudes); $x++) {
			$tipo_sol = $filtro_solicitudes[$x]["id_tipo_solicitud"];
			$estado_sol = $filtro_solicitudes[$x]["id_estado"];
			$execute .= "(sc.id_tipo_compra = '$tipo_sol' AND sc.id_estado_solicitud = '$estado_sol' AND (DATE_FORMAT(sc.fecha_solicitud, '%Y-%m-%d') >= '$fecha_filtro' AND DATE_FORMAT(sc.fecha_solicitud, '%Y-%m-%d') <= '$fecha_filtro_2'))";
			if ($x < count($filtro_solicitudes) - 1) $execute .= " OR ";
		}
		return $execute;
	}
	public function crear_query_jefes_solicitudes($id)
	{
		$this->db->select("p2.id");
		$this->db->from('personas p');
		$this->db->join('cargos_departamentos dp', 'dp.id_cargo_jefe = p.id_cargo');
		$this->db->join('personas p2', 'p2.id_cargo = dp.id');
		$this->db->where('p.id', $id);
		$query = $this->db->get();
		$empleados_jefe = $query->result_array();

		$execute = "";
		for ($x = 0; $x < count($empleados_jefe); $x++) {
			$id = $empleados_jefe[$x]["id"];
			$execute .= "sc.id_solicitante = '$id'";
			if ($x < count($empleados_jefe) - 1) $execute .= " OR ";
		}
		return $execute;
	}
	public function Listar_codigos()
	{
		$this->db->select("id, valor as codigo, valorx as descripcion");
		$this->db->from('valor_parametro');
		$this->db->where('estado', "1");
		$this->db->where('idparametro', "25");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function Listar_articulos($idsolicitud)
	{
		$this->db->select("vp.valor, a.*, sc.id_estado_solicitud, sc.id_solicitante");
		$this->db->from('articulos_solicitud a');
		$this->db->join('valor_parametro vp', 'a.cod_sap = vp.id');
		$this->db->join('solicitud_compra sc', 'sc.id = a.id_solicitud');
		$this->db->where('a.estado', "1");
		$this->db->where('a.id_solicitud', $idsolicitud);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function Listar_articulos_parciales($idsolicitud)
	{
		$this->db->select("vp.valor, a.*,sc.id_estado_solicitud,SUM(ea.cantidad) entregada");
		$this->db->select("vp.valor, a.*,sc.id_estado_solicitud");
		$this->db->from('articulos_solicitud a');
		$this->db->join('valor_parametro vp', 'a.cod_sap = vp.id');
		$this->db->join('solicitud_compra sc', 'sc.id = a.id_solicitud');
		$this->db->join('entregas_articulo ea', 'a.id=ea.id_articulo', 'left');
		$this->db->where('a.estado', "1");
		$this->db->where('a.id_solicitud', $idsolicitud);
		$this->db->group_by('a.id');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function historial_articulos_entregas_parciales($idsolicitud, $id_articulo)
	{
		$this->db->select("ar.nombre_articulo,ar.marca,ar.cantidad,ant.cantidad entregada,ant.fecha_registro,CONCAT(p.nombre,' ',p.apellido) persona", false);
		$this->db->from('articulos_solicitud ar');
		$this->db->join('entregas_articulo ant', 'ar.id = ant.id_articulo');
		$this->db->join('personas p', 'ant.usuario_registra = p.id');
		$this->db->where('ar.id_solicitud', $idsolicitud);
		$this->db->where('ar.id', $id_articulo);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function solicitudes_usuario($id_usuario)
	{
		$this->db->select("vp.valor nombre,a.id,a.estado,a.id_usuario,a.id_tipo_solicitud,p.id_perfil");
		$this->db->from('solicitudes_usuarios_com a');
		$this->db->join('valor_parametro vp', 'a.id_tipo_solicitud = vp.id_aux');
		$this->db->join('personas p', 'a.id_usuario= p.id');
		$this->db->where('a.id_usuario', $id_usuario);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function estados_solicitudes_usuario($id)
	{
		$this->db->select("vp.valor nombre,a.id,a.estado,a.id_solicitud_usuario");
		$this->db->from('estados_sol_usuarios a');
		$this->db->join('valor_parametro vp', 'a.id_estado = vp.id_aux');
		$this->db->where('a.id_solicitud_usuario', $id);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function Listar_historial_estado($idsolicitud)
	{
		$this->db->select("vp.valor estado, t.fecha_cambio,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) usuario,t.*", false);
		$this->db->from('tiempos_est_compras t');
		$this->db->join('valor_parametro vp', 't.id_estado = vp.id_aux');
		$this->db->join('personas p', 't.usuario_cambio = p.id');
		$this->db->where('t.id_solicitud', $idsolicitud);
		$this->db->order_by("id", "");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function listar_solicitudes_sin_asignar($idusuario)
	{
		$this->db->select("a.id asig,v.valor,v.id_aux,a.id_usuario");
		$this->db->from('valor_parametro v ');
		$this->db->join('solicitudes_usuarios_com a', 'v.id_aux = a.id_tipo_solicitud AND a.id_usuario="' . $idusuario . '"', 'left');
		$this->db->where('v.idparametro', 34);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_estados_sin_asignar($id)
	{
		$this->db->select("a.id asig,v.valor,v.id_aux");
		$this->db->from('valor_parametro v');
		$this->db->join('estados_sol_usuarios a', 'v.id_aux = a.id_estado AND a.id_solicitud_usuario="' . $id . '"', 'left');
		$this->db->where('v.idparametro', 33);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function Eliminar_articulo($id)
	{
		$date = date('Y-m-d h:i:s');
		$this->db->set('estado', -1);
		$this->db->set('fecha_eliminacion', $date);
		$this->db->set('usuario_elimina', $_SESSION['persona']);
		$this->db->where('id', $id);
		$this->db->update($this->table_articulos_solicitud);
		return 1;
	}
	public function modificar_cod_orden_solicitud($id, $codigo)
	{

		$this->db->set('num_orden', $codigo);
		$this->db->where('id', $id);
		$this->db->update($this->table_solicitud_compra);
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 0;
	}
	public function guardar_encuesta($id, $res1, $res2, $res3, $obs)
	{
		$date = date('Y-m-d h:i:s');
		$this->db->set('res1_encuesta', $res1);
		$this->db->set('res2_encuesta', $res2);
		$this->db->set('res3_encuesta', $res3);
		$this->db->set('obs_encuesta', $obs);
		$this->db->set('fecha_fin_encuesta', $date);
		$this->db->where('id', $id);
		$this->db->update($this->table_solicitud_compra);
		return 0;
	}

	public function Traer_articulo($id)
	{
		$this->db->select("vp.valor, vp.id as vpid, a.*");
		$this->db->from('articulos_solicitud a');
		$this->db->join('valor_parametro vp', 'a.cod_sap = vp.id');
		$this->db->where('a.estado', "1");
		$this->db->where('a.id', $id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function traer_solicitud($id)
	{
		$this->db->select("sc.*", false);
		$this->db->from('solicitud_compra sc');
		$this->db->where('sc.id', $id);
		$query = $this->db->get();
		return $query->result_array();
	}


	public function traer_solicitud_completa($id)
	{

		$this->db->select(" sc.*, u1.valor tipo_compra, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS solicitante, u.valor AS estado_solicitud,sc.id_estado_solicitud as estado_general, sc.id,pr.valor proveedor,DATE_FORMAT(DATE_ADD(tc.fecha_cambio, INTERVAL sc.fecha_entrega_est DAY),'%Y-%m-%d') estimada_real,cmt.nombre nombre_comite,cmt.fecha_cierre fecha_cierre_comite,p.correo,de.valor departamento,CONCAT(pj.nombre, ' ', pj.apellido, ' ', pj.segundo_apellido) AS jefe_encargado", false);
		$this->db->from('solicitud_compra sc');
		$this->db->join('tiempos_est_compras tc', 'sc.id = tc.id_solicitud AND tc.id_estado="Soli_Pen"', 'left');
		$this->db->join('valor_parametro u', 'sc.id_estado_solicitud = u.id_aux');
		$this->db->join('valor_parametro u1', 'sc.id_tipo_compra = u1.id_aux');
		$this->db->join('valor_parametro pr', 'sc.id_proveedor = pr.id', 'left');
		$this->db->join('comites cmt', 'sc.id_comite = cmt.id', 'left');
		$this->db->join('personas p', 'sc.id_solicitante = p.id');
		$this->db->join('cargos_departamentos c', 'p.id_cargo=c.id', 'left');
		$this->db->join('valor_parametro de', 'c.id_departamento=de.id', 'left');
		$this->db->join('cargos_departamentos cj', 'c.id_cargo_jefe=cj.id', 'left');
		$this->db->join('personas pj', 'c.id_cargo_jefe = pj.id_cargo', 'left');
		$this->db->where('sc.estado_registro', "1");
		$this->db->group_by('sc.id');
		$this->db->where('sc.id', $id);
		$query = $this->db->get();
		return $query->row();
	}


	public function Modificar_articulo($id, $cod_sap, $nombre_articulo, $marca, $referencia, $cantidad, $observaciones, $fecha_compra_tarjeta)
	{
		$this->db->set('cod_sap', $cod_sap);
		$this->db->set('nombre_articulo', $nombre_articulo);
		$this->db->set('marca', $marca);
		$this->db->set('referencia', $referencia);
		$this->db->set('cantidad', $cantidad);
		$this->db->set('observaciones', $observaciones);
		$this->db->set('fecha_compra_tarjeta', $fecha_compra_tarjeta);
		$this->db->where('id', $id);
		$this->db->update($this->table_articulos_solicitud);
		return 0;
	}

	public function Modificar_solicitud($id, $tipo, $observaciones)
	{
		$this->db->set('id_tipo_compra', $tipo);
		$this->db->set('observaciones', $observaciones);
		$this->db->where('id', $id);
		$this->db->update($this->table_solicitud_compra);
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 1;
	}

	public function Modificar_solicitud_comite($id, $id_comite, $descripcion_cmt, $observaciones_cmt)
	{
		$this->db->set('id_comite', $id_comite);
		$this->db->set('descripcion_cmt', $descripcion_cmt);
		$this->db->set('observaciones_cmt', $observaciones_cmt);
		$this->db->where('id', $id);
		$this->db->update($this->table_solicitud_compra);
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 1;
	}

	public function Gestionar_solicitud($id, $estado, $codigo, $fecha_estimada, $proveedor, $fecha_real, $descripcion, $observaciones, $id_comite, $motivo, $tipo_compra, $id_tipo_orden, $causal, $clasificacion_proveedores, $area_selected, $tipmat_tip_ser, $sst_or_sga, $sst_sga_enc)
	{
		$cambio = $this->guardar_cambio_estado($id, $estado);
		if (!is_null($proveedor)) {
			$this->db->set('id_proveedor', $proveedor);
		}
		if (!is_null($sst_or_sga) && $sst_sga_enc == false) {
			$this->db->set($sst_or_sga, 0);
		}
		if (!is_null($tipmat_tip_ser)) {
			$this->db->set($tipmat_tip_ser, 0);
		}
		if ($sst_sga_enc == true) {
			$this->db->set("estado_encuesta_sga", 0);
			$this->db->set("estado_encuesta_sst", 0);
		}
		if (!is_null($area_selected)) {
			$this->db->set('id_area', $area_selected);
		}
		if (!is_null($tipo_compra)) {
			$this->db->set('id_tipo_compra', $tipo_compra);
		}
		if (!is_null($id_tipo_orden)) {
			$this->db->set('id_tipo_orden', $id_tipo_orden);
		}
		if (!is_null($codigo)) {
			$this->db->set('num_orden', $codigo);
		}
		if (!is_null($causal)) {
			$this->db->set('id_causal_dev', $causal);
		}
		if (!is_null($motivo)) {
			$this->db->set('obs_devolucion', $motivo);
		}
		if (!is_null($fecha_estimada)) {
			$this->db->set('fecha_entrega_est', $fecha_estimada);
		}
		if (!is_null($fecha_real)) {
			$this->db->set('fecha_entrega_real', $fecha_real);
		}
		if (!is_null($clasificacion_proveedores)) {
			$this->db->set('id_clasificacion', $clasificacion_proveedores);
		}
		if (!is_null($id_comite)) {
			$this->db->set('id_comite', $id_comite);
			$this->db->set('observaciones_cmt', $observaciones);
			$this->db->set('descripcion_cmt', $descripcion);
		}

		$this->db->set('id_estado_solicitud', $estado);
		$this->db->where('id', $id);
		$this->db->update($this->table_solicitud_compra);
		return 1;
	}

	/* Funcion para solicitar informacion de la tabla de compras */
	public function solicitud_compras_inf($ids = "", $row_or_array = "")
	{
		$this->db->select("
		sc.id,
		sc.id_solicitante,
		sc.id_contrato,
		sc.id_estado_solicitud,
		sc.id_clasificacion,
		sc.res1_encuesta,
		sc.res2_encuesta,
		sc.res3_encuesta,
		sc.id_tipo_orden,
		sc.indice_fecha no_orden,
		CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) solicitante,
		sc.fecha_registra,
		vptrr.valor proveedor_name,
		vp.valor area_selected,
		vp.id_aux idaux_area_selected,
		vp.id_aux enc_type,
		vpp.valor cp_sl,
		vptr.id_aux order_type,
		vptr.valor name_order_type,
		sc.id_area id_area_selected,
		sc.estado_encuesta_sga sga_quiz,
		sc.estado_encuesta_sst sst_quiz,
		sc.estado_encuesta_tipmat tipmat_quiz,
		sc.estado_encuesta_tipserv tipser_quiz,
		sc.dias_no_habiles,
		sc.fecha_entrega_real,
		sc.id_proveedor", false);
		$this->db->from("solicitud_compra sc");
		$this->db->join("valor_parametro vp", "sc.id_area = vp.id", "left");
		$this->db->join("valor_parametro vpp", "sc.id_clasificacion = vpp.id", "left");
		$this->db->join("valor_parametro vptr", "sc.id_tipo_orden = vptr.id_aux", "left");
		$this->db->join("valor_parametro vptrr", "sc.id_proveedor = vptrr.id", "left");
		$this->db->join("personas p", "sc.id_solicitante = p.id", "left");
		//$this->db->where("vp.idparametro", 1226);
		$this->db->where("vpp.idparametro", 252);
		$this->db->where("vptr.idparametro", 72);
		$this->db->where("vptrr.idparametro", 37);
		$this->db->where("vpp.estado", 1);
		$this->db->where("vptr.estado", 1);
		$this->db->where("p.estado", 1);
		if (!empty($ids)) {
			$this->db->where("sc.id", $ids);
		}

		$query = $this->db->get();
		if ($row_or_array == "row") {
			return $query->row();
		} else {
			return $query->result_array();
		}
	}

	/* Funcion para traer las preguntas de rp REVISAR */
	public function preguntas_rp($te)
	{
		$this->db->select("vp.id, vp.valorx tipo, vp.valor pregunta, vpt.id id_tipo_encuesta");
		$this->db->from("valor_parametro vp");
		$this->db->join("valor_parametro vpt", "vp.valorx=vpt.valorx", "left");
		$this->db->where("vp.valorx", $te);
		$this->db->where("vp.idparametro", 257);
		$this->db->where("vp.estado", 1);
		$this->db->where("vpt.idparametro", 254);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar respuestas rp con sus procentajes */
	public function listar_respuestas_rp($idpa)
	{
		$this->db->select("vp.id, vp.valor respuesta, vp.valorx procentaje");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.estado", 1);
		$this->db->where("vp.idparametro", $idpa);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Fin de check encuestas */

	/* Guardar info general revisar */
	public function guardar_info($tabla, $datos)
	{
		$this->db->insert($tabla, $datos);
		$query = $this->db->_error_message();
		return $query;
	}

	/* Actualizar info general revisar */
	public function upd_info($tabla, $set_array, $where)
	{
		$this->db->set($set_array);
		$this->db->where($where);
		$this->db->update($tabla);
		//$query = $this->db->affected_rows();
		$query = $this->db->_error_message();
		return $query;
	}

	/* Borrar info general revisar */
	public function del_info($tabla, $conditions)
	{
		$this->db->delete($tabla, $conditions);
		//$query = $this->db->affected_rows();
		$query = $this->db->_error_message();
		return $query;
	}

	public function guardar_tiempo_gestion($id, $tiempo, $tiempo_habil)
	{
		$this->db->set('tiempo_gestion', $tiempo);
		if (!is_null($tiempo_habil)) {
			$this->db->set('tiempo_habil', $tiempo_habil);
		}
		$this->db->where('id', $id);
		$this->db->update($this->table_solicitud_compra);
		return 1;
	}
	public function traer_id_ultima_solicitud($person)
	{

		$this->db->select("id");
		$this->db->from("solicitud_compra");
		$this->db->order_by("id", "desc");
		$this->db->where('id_usuario_registra', $person);
		//$this->db->where('fecha_registra', $usuario);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row->id;
	}

	public function traer_indice_disponible_solicitud()
	{

		$this->db->select("COUNT(id) +1 AS indice");
		$this->db->from("solicitud_compra");
		$this->db->where("DATE_FORMAT( fecha_registra ,'%Y-%m') = DATE_FORMAT(curdate() ,'%Y-%m')");
		$query = $this->db->get();
		$row = $query->row();
		return $row->indice;
	}

	public function buscar_codigo_sap($codigo)
	{
		$this->db->select("id, valor as codigo, valorx as descripcion");
		$this->db->from('valor_parametro');
		$this->db->where('idparametro', 25);
		$this->db->where('estado', "1");
		$this->db->like('valor', $codigo);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function cargar_articulos($id)
	{
		$this->db->select("a.*");
		$this->db->from('articulos_solicitud a');
		$this->db->where('a.estado', "1");
		$this->db->where('a.id_solicitud', $id);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function guardar_cambio_estado($id_solicitud, $idestado)
	{
		$this->db->insert("tiempos_est_compras", array(
			"id_solicitud" => $id_solicitud,
			"id_estado" => $idestado,
			"usuario_cambio" => $_SESSION['persona'],
		));
		return 1;
	}
	/**
	 * Realiza una consulta a la tabla persona y lista aquellas personas que se encuentran en estado activo Y tienen asignado los perfiles de compras
	 * @return Array
	 */
	public function listar_responsables_procesos($id)
	{
		$this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre,p.identificacion,p.correo,p.id,vp.valor tipo,p.id_perfil", false);
		$this->db->from('personas p');
		$this->db->join('valor_parametro vp', 'p.id_perfil = vp.id_aux');
		$this->db->where('p.estado', "1");
		$this->db->where("p.id", $id);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function guardar_general($data, $tabla)
	{
		$this->db->insert_batch($tabla, $data);
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 1;
	}

	public function asignar_estados_solicitud_usuario($id_solicitud, $estado)
	{
		$this->db->insert('estados_sol_usuarios', array(
			"id_solicitud_usuario" => $id_solicitud,
			"id_estado" => $estado,
			"usuario_registra" => $_SESSION['persona'],
		));
		return 1;
	}

	public function cambiar_permisos_usuarios_solicitudes($id, $estado, $tipo)
	{
		$table = "";
		$this->db->where('id', $id);
		if ($tipo == 1) {
			$table = "solicitudes_usuarios_com";
		} else {
			$table = "estados_sol_usuarios";
		}
		$this->db->delete($table);
		return 1;
	}
	public function traer_id_solicitud_asignada($person)
	{

		$this->db->select("id");
		$this->db->from("solicitudes_usuarios_com");
		$this->db->order_by("id", "desc");
		$this->db->where('usuario_registra', $person);
		//$this->db->where('fecha_registra', $usuario);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row->id;
	}
	public function esta_negada_usuario($id_compra, $usuario)
	{

		$this->db->select("*");
		$this->db->from("solicitud_negadas_comite");
		if (!is_null($usuario)) {
			$this->db->where('usuario_registro', $usuario);
		}
		$this->db->where('id_compra', $id_compra);
		$this->db->where('estado', 1);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function guardar_comite($nombre, $fecha, $descripcion, $usuario)
	{
		$this->db->insert("comites", array(
			"nombre" => $nombre,
			"descripcion" => $descripcion,
			"usuario_registra" => $usuario,
			"fecha_cierre" => $fecha,
		));
		return 0;
	}
	public function listar_comites($id = -1, $estado = null, $dir = -1)
	{
		$exec = '';
		$sw = false;
		if ($_SESSION['perfil'] == 'Per_Dir_t2') {
			$exec = $this->crear_query_lista_solicitudes_comite();
			$sw = true;
		}
		$this->db->select("c.*, COUNT(sc.id) solicitudes,v.valor estado_alt, DATE_FORMAT(c.fecha_registra,'%Y') as ano,DATE_FORMAT(c.fecha_registra,'%Y-%m-%d') as fecha_inicio,DATE_FORMAT(c.fecha_cierre,'%Y-%m-%d') as fecha_fin", false);
		$this->db->from('comites c');
		$this->db->join('valor_parametro v', 'v.id_aux = c.id_estado_comite');
		$this->db->join('solicitud_compra sc', 'sc.id_comite = c.id AND sc.id_estado_solicitud <> "Soli_Dev"', 'left');

		if ($dir == 1) {
			if ($sw) {
				if (!empty($exec)) {
					if ($id > 0) {
						$exec = "($exec) AND c.id_estado_comite <> 'Com_Ter'";
					}
					$this->db->where($exec);
				} else {
					$this->db->where("id_estado_comite = 'XXX'");
				}
			} else {
				if ($id > 0) {
					$this->db->where("id_estado_comite = 'Com_Not'");
				} else {
					$this->db->where("(id_estado_comite = 'Com_Not' OR id_estado_comite = 'Com_Ter')");
				}
			}
		} else {
			if ($id > 0) {
				$this->db->where('c.id_estado_comite', 'Com_Not');
			}
			if (!is_null($estado)) {
				$this->db->where('c.id_estado_comite', $estado);
			}
		}

		$this->db->where('c.estado', "1");
		$this->db->where('c.tipo', "compras");
		$this->db->group_by('c.id');
		$this->db->_protect_identifiers = false;
		$this->db->order_by("FIELD (c.id_estado_comite,'Com_Ini','Com_Not','Com_Ter')");
		$this->db->order_by("c.fecha_registra", "desc");
		$this->db->_protect_identifiers = true;
		$query = $this->db->get();
		return $query->result_array();
	}

	public function existe_nombre_comite($nombre)
	{

		$this->db->select("*");
		$this->db->from("comites");
		$this->db->where('nombre', $nombre);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function traer_comite($id)
	{
		$this->db->select("*");
		$this->db->from("comites");
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->result_array();
	}


	public function modificar_comite($id, $nombre, $descripcion, $estado)
	{
		if (is_null($estado)) {
			$this->db->set('nombre', $nombre);
			$this->db->set('descripcion', $descripcion);
		} else {
			$this->db->set('id_estado_comite', $estado);
		}
		$this->db->where('id', $id);
		$this->db->where('tipo', 'compras');
		$this->db->update("comites");
		return 0;
	}


	public function guardar_proveedor_solicitud($nombre, $id_solicitud, $valor_total, $precio_dolar, $iva, $administracion, $imprevistos, $utilidad, $otro, $usuario_registra, $adjunto)
	{
		$this->db->insert("solicitud_proveedores", array(
			"nombre" => $nombre,
			"valor_total" => $valor_total,
			"precio_dolar" => $precio_dolar,
			"iva" => $iva,
			"administracion" => $administracion,
			"imprevistos" => $imprevistos,
			"utilidad" => $utilidad,
			"valor_dolar" => $otro,
			"id_solicitud" => $id_solicitud,
			"usuario_registra" => $usuario_registra,
			"adjunto" => $adjunto,
		));
		return 0;
	}

	public function listar_proveedores_solicitud($id)
	{
		$this->db->select("sp.*,(SELECT count(pa.id) FROM proveedores_aprobados pa INNER JOIN personas p ON p.id = pa.usuario_registra WHERE pa.id_proveedor = sp.id AND pa.estado= 1 AND p.id_perfil = 'Per_Dir') as vb,(SELECT count(pas.id) FROM proveedores_aprobados pas INNER JOIN personas ps ON ps.id = pas.usuario_registra WHERE pas.id_proveedor = sp.id AND pas.estado= 1 AND ps.id_perfil <> 'Per_Dir') as vbs");
		$this->db->from('solicitud_proveedores sp');
		$this->db->where('sp.id_solicitud', $id);
		$this->db->where('sp.estado', "1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function traer_proveedor_solicitud($id)
	{
		$this->db->select("*");
		$this->db->from('solicitud_proveedores');
		$this->db->where('id', $id);
		$this->db->where('estado', "1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function entregas_parciales($data)
	{
		$this->db->insert_batch('entregas_articulo', $data);
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 1;
	}
	public function modificar_proveedor_solicitud($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('solicitud_proveedores', $data);
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 0;
	}
	public function aprobar_proveedor($data)
	{
		$this->db->insert("proveedores_aprobados", $data);
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 0;
	}

	public function traer_proveedor_aprobados($id)
	{
		$this->db->select("p.*,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona,pa.*", false);
		$this->db->from('proveedores_aprobados pa');
		$this->db->join('personas p', 'pa.usuario_registra = p.id');
		$this->db->where('pa.id_proveedor', $id);
		$this->db->where('pa.estado', "1");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function traer_proveedor_aprobados_persona($id_solicitud, $id_persona, $sw = 1)
	{
		$this->db->select("pa.*");
		$this->db->from('solicitud_proveedores sp');
		$this->db->join('proveedores_aprobados pa', 'pa.id_proveedor = sp.id AND pa.estado = 1');
		if ($sw != 1) {
			$this->db->join('personas p', 'pa.usuario_registra = p.id');
			$this->db->where('p.id_perfil', "Per_Dir");
		}
		$this->db->where('sp.id_solicitud', $id_solicitud);
		if (!is_null($id_persona)) {
			$this->db->where('pa.usuario_registra', $id_persona);
		}
		$query = $this->db->get();
		return $query->result_array();
	}
	public function guardar_comentario($comentario, $usuario_registra, $id_reserva, $id_pregunta = null)
	{
		$estado = 1;
		if (($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Com" || $_SESSION["perfil"] == "Per_Com") && is_null($id_pregunta)) {
			$estado = 0;
		}
		$this->db->insert('comentarios_compras', array(
			"usuario_registra" => $usuario_registra,
			"comentario" => $comentario,
			"id_compra" => $id_reserva,
			"id_pregunta" => $id_pregunta,
			"estado" => $estado,
		));
		return 1;
	}

	public function listar_comentario($id, $id_coment)
	{
		$this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona,p.usuario,v.*", false);
		$this->db->from("comentarios_compras v");
		$this->db->join('personas p', 'v.usuario_registra=p.id');
		if (!empty($id_coment)) {
			$this->db->where('v.id_pregunta', $id_coment);
		} else {
			$this->db->where('v.id_compra', $id);
			$this->db->where('v.estado >', -1);
			$this->db->where('v.id_pregunta IS NULL');
			/*if ($_SESSION["perfil"] != "Per_Admin" && $_SESSION["perfil"] != "Per_Adm_Com" && $_SESSION["perfil"] != "Per_Com") {
					$this->db->where('v.usuario_registra',$_SESSION['persona']);
			}*/
		}

		$this->db->order_by("v.fecha_registro");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function guardar_archivo_compra($id_compra, $nombre_real, $nombre_guardado, $idCrono = NULL)
	{
		$this->db->insert("archivos_adj_compras", array(
			"id_compra" => $id_compra,
			"id_cronograma" => $idCrono,
			"nombre_real" => $nombre_real,
			"nombre_guardado" => $nombre_guardado,
			"usuario_registra" => $_SESSION['persona'],
		));
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 1;
	}


	public function listar_archivo_compra($id_compra)
	{
		$this->db->select("*");
		$this->db->from("archivos_adj_compras");
		$this->db->where('id_compra', $id_compra);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function solicitudes_por_encuestar_persona($idpersona)
	{
		$this->db->select("COUNT(sc.id) pendientes");
		$this->db->from('solicitud_compra sc');
		$this->db->where('sc.id_estado_solicitud', "Soli_Fin");
		$this->db->where('sc.fecha_fin_encuesta IS NULL');
		$this->db->where('sc.id_solicitante', $idpersona);
		$query = $this->db->get();
		$row = $query->row();
		return $row->pendientes;
	}

	public function modificar_estados_provedores_aprobados($where)
	{
		$this->db->set('estado', -1);
		$this->db->where($where);
		$this->db->update("proveedores_aprobados");
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 0;
	}

	public function mostrar_notificaciones_comentario($tipo = 1)
	{

		if (($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Com" || $_SESSION["perfil"] == "Per_Com") && $tipo == 1) {
			$query = $this->db->query("SELECT CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona,cc.*,cr.comentario respuesta,cr.usuario_registra ur,(SELECT ca.usuario_registra ufin FROM comentarios_compras ca WHERE ca.id = max(cr.id)) idfin FROM comentarios_compras cc LEFT JOIN comentarios_compras cr ON cc.id = cr.id_pregunta INNER JOIN personas p ON p.id=cc.usuario_registra WHERE cc.estado = 1 AND cc.id_pregunta IS null GROUP BY cc.id");
		} else {
			$query = $this->db->query("SELECT CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona,cc.*,cr.comentario respuesta,cr.usuario_registra ur,(SELECT ca.usuario_registra ufin FROM comentarios_compras ca WHERE ca.id = max(cr.id)) idfin FROM comentarios_compras cc INNER JOIN comentarios_compras cr ON cc.id = cr.id_pregunta INNER JOIN personas p ON p.id=cc.usuario_registra WHERE cc.estado = 1 AND cc.id_pregunta IS null AND cc.usuario_registra=" . $_SESSION['persona'] . " GROUP BY cc.id");
		}

		return $query->result_array();
	}

	public function terminar_comentario($id, $usuario, $fecha)
	{

		$this->db->set('estado', "0");
		$this->db->set('usuario_termina', $usuario);
		$this->db->set('fecha_termina', $fecha);
		$this->db->where('id', $id);
		$this->db->update('comentarios_compras');
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 0;
	}

	public function traer_correos_comite_compras_tipo2($tipo)
	{
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona,p.correo", false);
		$this->db->from('solicitudes_usuarios_com sc');
		$this->db->join('personas p', 'sc.id_usuario = p.id');
		$this->db->where('sc.id_tipo_solicitud', $tipo);
		$this->db->where('p.id_perfil', 'Per_Dir_t2');
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar seleccion de areas */
	public function listar_seleccion_area($idpa)
	{
		$this->db->select("vp.id, vp.valor area, vp.id_aux idaux");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idpa);
		$this->db->where("vp.estado", 1);
		$this->db->order_by("vp.id", "DESC");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar tipo de preguntas RP */
	public function listar_tipos_preguntasRP($tipo_encuesta = '', $idpa)
	{
		$this->db->select("vp.id, vp.valor area, vp.valorx idaux");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idpa);
		!empty($tipo_encuesta) ? $this->db->where('vp.id', $tipo_encuesta) : false;
		$this->db->where("vp.estado", 1);
		$this->db->order_by("vp.id", "ASC");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Consulta de criterios asignados AQUI */
	public function criterios_asigned($criterio)
	{
		$this->db->select("cac.id_criterio as criterio_asignado, cac.porcentaje, cac.id_encuesta, vp.valorx tipo_encuesta");
		$this->db->from("criterios_asignados_compra cac");
		$this->db->join("valor_parametro vp", "vp.id=cac.id_encuesta");
		$this->db->where("cac.id_criterio", $criterio);
		$query = $this->db->get();
		return $query->result_array();
	}


	/* Listar preguntas preguntas segun area seleccionada para editar */
	public function listar_preguntas_encuestas($tipo_encuesta, $idpa)
	{
		$this->db->select("vp.id, vp.valor pregunta, vp.valorx area_asig");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idpa);
		if (!empty($tipo_encuesta)) {
			$this->db->where("vp.valorx", $tipo_encuesta);
		}
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_personas_compra_negados($id)
	{
		$this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona,v.*,c.*,v.fecha_registro fecha", false);
		$this->db->from("solicitud_negadas_comite v");
		$this->db->join('personas p', 'v.usuario_registro=p.id');
		$this->db->join('comentarios_compras c', 'v.id=c.id_negada', 'left');
		$this->db->where('v.estado', 1);
		$this->db->where('v.id_compra', $id);
		$this->db->order_by("v.fecha_registro");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function cancelar_negado_solicitud($id)
	{
		$date = date('Y-m-d h:i:s');
		$this->db->set('estado', -1);
		$this->db->set('fecha_elimina', $date);
		$this->db->set('usuario_elimina', $_SESSION['persona']);
		$this->db->where('id', $id);
		$this->db->update('solicitud_negadas_comite');
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 1;
	}
	public function cancelar_comentario_negado_solicitud($id)
	{
		$date = date('Y-m-d h:i:s');
		$this->db->set('estado', -1);
		$this->db->set('fecha_termina', $date);
		$this->db->set('usuario_termina', $_SESSION['persona']);
		$this->db->where('id_negada', $id);
		$this->db->update('comentarios_compras');
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 1;
	}
	public function modificar_datos($data, $tabla, $id)
	{
		$this->db->where('id', $id);
		$this->db->update($tabla, $data);
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 1;
	}

	/* Traer id del permiso asignado para encuestas RP */
	public function permisos_compra_info($id_persona)
	{
		$this->db->select("cpe.*");
		$this->db->from("compras_permisos_encuestas cpe");
		$this->db->where("cpe.id_persona", $id_persona);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function _data_last_month_day()
	{
		$month = date('m');
		$year = date('Y');
		$day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

		return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
	}

	/** Actual month first day **/
	public function _data_first_month_day()
	{
		$month = date('m');
		$year = date('Y');
		return date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
	}


	public function notificaciones_solicitudes($filtro = '', $having = '')
	{
		$this->db->select("sc.*,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS solicitante, u.valor AS estado_solicitud,cmt.nombre nombre_comite,(SELECT COUNT(pb.id) vb FROM solicitud_proveedores sp LEFT JOIN proveedores_aprobados pb on pb.id_proveedor = sp.id AND pb.estado = 1 INNER JOIN personas p ON pb.usuario_registra = p.id AND p.id_perfil = 'Per_Dir' WHERE sp.id_solicitud = sc.id) as vb", false);
		$this->db->from('solicitud_compra sc');
		$this->db->join('valor_parametro u', 'sc.id_estado_solicitud = u.id_aux');
		$this->db->join('comites cmt', 'sc.id_comite = cmt.id', 'left');
		$this->db->join('personas p', 'sc.id_solicitante = p.id');
		if ($_SESSION['perfil'] != 'Per_Admin') {
			$this->db->join('solicitudes_usuarios_com suc', 'sc.id_tipo_compra = suc.id_tipo_solicitud AND suc.id_usuario =' . $_SESSION['persona']);
			$this->db->join('estados_sol_usuarios esu', "suc.id = esu.id_solicitud_usuario AND esu.id_estado = sc.id_estado_solicitud");
			$this->db->where('sc.estado_registro', "1");
		}
		$this->db->where('sc.estado_registro', "1");
		if (!empty($filtro)) $this->db->where($filtro);
		if (!empty($having)) $this->db->having($having);
		$this->db->_protect_identifiers = false;
		$this->db->order_by("sc.fecha_solicitud");
		$this->db->_protect_identifiers = true;
		$this->db->group_by('sc.id');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_solicitudes_comite_acta($id_comite)
	{
		$this->db->select("vt.valor tipo, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS solicitante,com.id_estado_comite,sc.*", false);
		$this->db->from('solicitud_compra sc');
		$this->db->join('comites com', 'com.id = sc.id_comite');
		$this->db->join('valor_parametro vt', 'sc.id_tipo_compra = vt.id_aux');
		$this->db->join('personas p', 'sc.id_solicitante = p.id');
		$this->db->where("sc.id_comite = '$id_comite' AND sc.id_estado_solicitud <> 'Soli_Dev'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_aprobados_proveedor($id_solicitud)
	{
		$id_proveedor = $this->prooveedor_mayor($id_solicitud);
		$this->db->select("sp.nombre proveedor,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS aprueba, sp.valor_total, sp.iva, sp.imprevistos, sp.utilidad, sp.administracion", false);
		$this->db->from('proveedores_aprobados pa');
		$this->db->join('solicitud_proveedores sp', 'sp.id = pa.id_proveedor');
		$this->db->join('personas p', 'pa.usuario_registra = p.id');
		$this->db->where('pa.id_proveedor', $id_proveedor);
		$this->db->where('pa.estado', "1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function prooveedor_mayor($id_solicitud)
	{
		$query = $this->db->query("select sp.id from solicitud_proveedores sp where sp.id_solicitud = $id_solicitud ORDER by (SELECT count(pa.id) FROM proveedores_aprobados pa INNER JOIN personas p ON p.id = pa.usuario_registra WHERE pa.id_proveedor = sp.id AND pa.estado= 1 AND p.id_perfil = 'Per_Dir') DESC limit 1");
		$row = $query->row();
		return $row->id;
	}

	public function listar_personas_compras($persona)
	{
		$this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre, p.identificacion, p.correo", false);
		$this->db->from('personas p');
		$this->db->like("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido)", $persona);
		$this->db->or_like("p.usuario", $persona);
		$this->db->where('p.estado', "1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_tipos_permisos($id_persona)
	{
		$this->db->select("pc.*", false);
		$this->db->from("permisos_compra pc");
		$this->db->where("pc.id_persona", $id_persona);
		$query = $this->db->get();
		return $query->row();
	}

	public function eliminar_permiso($id, $tabla)
	{
		$this->db->where('id', $id);
		$this->db->delete($tabla);
		$error = $this->db->_error_message();
		if ($error) {
			return -1;
		}
		return 1;
	}

	public function agregar_permiso($data, $tabla)
	{
		$this->db->insert($tabla, $data);
		$error = $this->db->_error_message();
		if ($error) {
			return -1;
		}
		return 1;
	}

	public function obtener_correos_permiso($tipo_sol, $estado)
	{
		$this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.correo, suc.id_usuario", false);
		$this->db->from('solicitudes_usuarios_com suc');
		$this->db->join('estados_sol_usuarios esu', 'suc.id = esu.id_solicitud_usuario');
		$this->db->join('personas p', 'suc.id_usuario = p.id');
		$this->db->where("suc.id_tipo_solicitud", $tipo_sol);
		$this->db->where("esu.id_estado", $estado);
		$this->db->where("suc.estado", 1);
		$this->db->where("esu.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function setear_aprobados($id_solicitud)
	{
		//MEJORAR
		$this->db->query("UPDATE proveedores_aprobados pb LEFT JOIN solicitud_proveedores sp ON pb.id_proveedor = sp.id AND pb.estado = 1 SET pb.estado = 0 WHERE sp.id_solicitud = $id_solicitud");
		$this->db->query("UPDATE solicitud_negadas_comite sn SET sn.estado = 0 WHERE sn.id_compra = $id_solicitud");
		$error = $this->db->_error_message();
		if ($error) {
			return -1;
		}
		return 1;
	}

	public function personas_negado($id_solicitud)
	{
		$this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre", false);
		$this->db->from('personas p');
		$this->db->join('solicitud_negadas_comite v', 'v.usuario_registro=p.id');
		$this->db->where("v.estado", 1);
		$this->db->where("v.id_compra", $id_solicitud);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function personas_aprobado($id_solicitud)
	{
		$this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre", false);
		$this->db->from('proveedores_aprobados pa');
		$this->db->join('personas p', 'pa.usuario_registra = p.id AND pa.estado = 1');
		$this->db->join('solicitud_proveedores sp', 'pa.id_proveedor = sp.id AND pa.estado = 1');
		$this->db->where("sp.id_solicitud", $id_solicitud);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function personas_comite()
	{
		$this->db->select("p.id, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona, p.correo", false);
		$this->db->from("personas p");
		$this->db->join('actividades_personas ap', "ap.id_persona = p.id AND ap.id_actividad ='comite'");
		$this->db->join('cargos_departamentos c', 'p.id_cargo=c.id', 'left');
		$this->db->where('p.id_perfil', 'Per_Dir');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function buscar_jefe($persona)
	{
		$this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) AS fullname, p.identificacion, p.correo", false);
		$this->db->from('personas p');
		$this->db->where("(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $persona . "%' OR p.identificacion LIKE '%" . $persona . "%' OR p.usuario LIKE '%" . $persona . "%') AND p.estado=1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_correo_solicitante($id_solicitud)
	{
		$this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) AS fullname, p.correo", false);
		$this->db->from('personas p');
		$this->db->join('solicitud_compra sc', 'sc.id_usuario_registra = p.id');
		$this->db->where("sc.id", $id_solicitud);
		$query = $this->db->get();
		return $query->row();
	}

	public function info_solicitud_recordatorio($id_solicitud)
	{
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS solicitante, cmt.nombre nombre_comite", false);
		$this->db->from('solicitud_compra sc');
		$this->db->join('comites cmt', 'sc.id_comite = cmt.id', 'left');
		$this->db->join('personas p', 'sc.id_solicitante = p.id');
		$this->db->where('sc.estado_registro', "1");
		$this->db->group_by('sc.id');
		$this->db->where('sc.id', $id_solicitud);
		$query = $this->db->get();
		return $query->row();
	}

	public function obtener_cargo($id_persona)
	{
		$this->db->select("vp.*", false);
		$this->db->from('personas p');
		$this->db->join('valor_parametro vp', 'p.id_cargo_sap = vp.id');
		$this->db->where("p.id", $id_persona);
		$this->db->where("p.estado", 1);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function notificaciones_servicio_recibido()
	{
		$this->db->select("sc.*,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS solicitante", false);
		$this->db->from('solicitud_compra sc');
		$this->db->join('personas p', 'sc.id_solicitante = p.id');
		$this->db->join('tiempos_est_compras tc', 'sc.id = tc.id_solicitud AND tc.id_estado= sc.id_estado_solicitud');
		$this->db->join('solicitudes_usuarios_com suc', 'sc.id_tipo_compra = suc.id_tipo_solicitud AND suc.id_usuario =' . $_SESSION['persona']);
		$this->db->join('estados_sol_usuarios esu', "suc.id = esu.id_solicitud_usuario AND esu.id_estado = sc.id_estado_solicitud");
		$this->db->where('sc.estado_registro', "1");
		$this->db->where('sc.id_estado_solicitud', "Ser_Rec");
		$this->db->_protect_identifiers = false;
		$this->db->order_by("tc.fecha_cambio", 'DESC');
		$this->db->_protect_identifiers = false;
		$this->db->order_by("sc.fecha_solicitud");
		$this->db->_protect_identifiers = true;
		$this->db->group_by('sc.id');
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar proveedores filtrados segun fecha */
	public function listar_proveedores_filtrados($fd, $fh)
	{
		$this->db->select("
		u1.valor as tipo_compra,
		sc.num_orden,
		sc.fecha_registra,
		vp.valor proveedor,
		sc.fecha_entrega_est dias_estimados,
		sc.fecha_entrega_real entrega_real,
		sc.dias_no_habiles entrega_ideal,
		IF(sc.fecha_entrega_real <= sc.dias_no_habiles,'BIEN' , 'MAL' ) estado", false);
		$this->db->from("solicitud_compra sc");
		$this->db->join("valor_parametro vp", "sc.id_proveedor=vp.id", "left");
		$this->db->join('valor_parametro u1', 'sc.id_tipo_compra = u1.id_aux');
		$this->db->where('sc.fecha_solicitud BETWEEN "' . date('Y-m-d', strtotime($fd)) . '" and "' . date('Y-m-d', strtotime($fh)) . '"');
		$this->db->where("vp.idparametro", 37);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar personas con encuestas asignadas */
	public function check_permisos_encuestas($tipo_orden)
	{
		$this->db->select("pc.id_persona as persona");
		$this->db->from("permisos_compra pc");
		$this->db->where("pc.idparametro", $tipo_orden);
		$query = $this->db->get();
		return $query->row();
	}

	/* Traer id auxiliar de un valor_parametro */
	public function traer_idaux_vp($id)
	{
		$this->db->select("vp.id_aux idaux");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.id", $id);
		$query = $this->db->get();
		return $query->row();
	}

	/* Taer permisos encuestas */
	public function traer_permisos_encuestas($id_p = "", $tipo_encuesta = "", $idpa)
	{
		$this->db->select("
		cpe.id_persona,
		vp.valor nombre_encuesta,
		cpe.id_tipo_encuesta as tipo_encuesta,
		p.correo,
		CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as full_name", false);
		$this->db->from("compras_permisos_encuestas cpe");
		$this->db->join("personas p", "cpe.id_persona=p.id");
		$this->db->join("valor_parametro vp", "cpe.id_tipo_encuesta=vp.valorx");
		$this->db->where("vp.idparametro", $idpa);
		if (!empty($id_p)) {
			$this->db->where("cpe.id_persona", $id_p);
		} else if (!empty($tipo_encuesta)) {
			$this->db->where("cpe.id_tipo_encuesta", $tipo_encuesta);
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar criterios RP */
	public function listar_criterios_rp($idpa)
	{
		$this->db->select("
		vp.id, vp.id_aux as idaux,
		vp.valorx as descrip,
		vp.valor as nombre_criterio,
		SUM(cac.porcentaje) porcentaje", false);
		$this->db->from("valor_parametro vp");
		$this->db->join("criterios_asignados_compra cac", "vp.id=cac.id_criterio", "left");
		$this->db->where("vp.idparametro", $idpa);
		$this->db->where("vp.estado", 1);
		$this->db->group_by("vp.id");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Actualizar valor_parametro de criterios */
	public function upd_valorp($idp, $datos)
	{
		$this->db->set($datos);
		$this->db->where('id', $idp);
		$query = $this->db->update("valor_parametro");
		return $query;
	}

	/* Eliminar valor_parametro de criterios */
	public function del_valorp($idc, $condicion)
	{
		$this->db->set($condicion);
		$this->db->where('id', $idc);
		$query = $this->db->update("valor_parametro");
		return $query;
	}

	/* Funcion para adicionar criterios */
	public function add_valorp($datos)
	{
		$query = $this->db->insert("valor_parametro", $datos);
		return $query;
	}

	/* Traer valores de valor parametro a partir de su ID */
	public function traer_valor_parametro($id = "", $idaux = "", $idparametro = "", $valory = '', $row = true)
	{
		$this->db->select("vp.id, vp.idparametro, vp.valor, vp.id_aux, vp.valorx, vp.valory");
		$this->db->from("valor_parametro vp");

		if (empty($id) and empty($idaux)) {
			$this->db->where("vp.valory", $valory);
		} else {
			if (empty($id)) {
				$this->db->where("vp.id_aux", $idaux);
			}
			if (empty($idaux)) {
				$this->db->where("vp.id", $id);
			}
		}

		$this->db->where("vp.estado", 1);
		$query = $this->db->get();

		if ($row) {
			return $query->row();
		} else {
			return $query->result_array();
		}
	}

	public function buscar_id_enctype($idparametro = "", $valorx)
	{
		$this->db->select("vp.id, vp.id_aux, vp.valor, vp.valorx");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idparametro);
		$this->db->where("vp.valorx", $valorx);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	/* Listar ponderados RP */
	public function listar_ponderados_rp()
	{
		$this->db->select("ppc.*");
		$this->db->from("ponderacion_porcentaje_compra ppc");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Traer encuestas resueltas_rp */
	public function traer_encuestas_resueltas_rp($idsol = "", $idtipoenc = "", $row_or_array = "")
	{
		$this->db->select("ce.id, ce.id_enc_type id_enctype, vptr.valorx enc_idaux, ce.id_usuario_registra responsable, vp.valor pregunta, vpt.valor respuesta, ce.observaciones obs");
		$this->db->from("compras_encuestas ce");
		$this->db->join("valor_parametro vp", "vp.id=ce.id_pregunta");
		$this->db->join("valor_parametro vpt", "vpt.id=ce.id_respuesta");
		$this->db->join("valor_parametro vptr", "vptr.id=ce.id_enc_type");
		$this->db->where("ce.id_solicitud", $idsol);
		$this->db->where("(ce.id_enc_type = '" . $idtipoenc . "' OR ce.id_tipo_encuesta = '" . $idtipoenc . "')");
		$this->db->where("ce.estado", 1);
		$query = $this->db->get();
		if ($row_or_array == "row") {
			return $query->row();
		} else if ($row_or_array == "array" or empty($row_or_array)) {
			return $query->result_array();
		}
	}

	/* Compras encuestas inf */
	public function compras_encuestas_inf($ids, $row_or_array = false)
	{
		$this->db->select("ce.id, ce.id_solicitud, ce.id_enc_type, vp.valorx enc_idaux");
		$this->db->from("compras_encuestas ce");
		$this->db->join("valor_parametro vp", "vp.id=ce.id_enc_type");
		$this->db->where("ce.id_solicitud", $ids);
		$query = $this->db->get();

		if ($row_or_array == false) {
			return $query->row();
		} else {
			return $query->result_array();
		}
	}

	/* Listar articulos para detalles de las solicitudes en los masivos */
	public function detalles_articulos_masivos($idsol)
	{
		$this->db->select("a.nombre_articulo art_o_ser, vp.valorx codSap, a.fecha_creacion fecha_registra, a.observaciones obs, a.cantidad");
		$this->db->from("articulos_solicitud a");
		$this->db->join("valor_parametro vp", "a.cod_sap = vp.id");
		$this->db->where("a.id_solicitud", $idsol);
		$this->db->where("vp.idparametro", 25);
		$this->db->where("a.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Cronograma desde aqui - BORRAR COMENT */

	/* Listar cronogramas */
	public function listar_cronograma($idpa)
	{
		$this->db->select("vp.id, vp.valor crono_name, vp.valorx crono_ape");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idpa);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* listar adjuntos cronograma */
	public function listar_adjuntos_cronograma($idsol)
	{
		$this->db->select("nombre_real, nombre_guardado, fecha_registro");
		$this->db->from("archivos_adj_compras");
		$this->db->where("id_cronograma", $idsol);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function datosCronograma($idsol)
	{
		$this->db->select("cc.*, vp.valor crono_status, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) solicitante_name", false);
		$this->db->from("compras_cronograma cc");
		$this->db->join('personas p', 'p.id = cc.id_usuario_registra', 'left');
		$this->db->join('valor_parametro vp', 'vp.id_aux = cc.estado_cronograma', 'left');
		$this->db->where("cc.id_solicitud", $idsol);
		$this->db->where("cc.estado", 1);
		$this->db->where("p.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Traer datos cronogramas por ID */
	public function datos_cronograma_filtered($idCrono)
	{
		$this->db->select("cc.especificaciones as esp, cc.estado_cronograma as estado");
		$this->db->from("compras_cronograma cc");
		$this->db->join('personas p', 'p.id = cc.id_usuario_registra', 'left');
		$this->db->where("cc.id", $idCrono);
		$this->db->where("cc.estado", 1);
		$this->db->where("p.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	/* Traer datos de valor_parametro */
	public function traer_datos_valorp($idparametro = "", $id = "", $row = false)
	{
		$this->db->select("vp.id, vp.id_aux idaux, vp.valor dato, vp.valorx, vp.valory, vp.valorz");
		$this->db->from("valor_parametro vp");

		if (empty($idparametro) and !empty($id)) {
			$this->db->where("vp.id", $id);
		} else if (empty($id) and !empty($idparametro)) {
			$this->db->where("vp.idparametro", $idparametro);
		}

		$query = $this->db->get();
		if ($row) {
			return $query->row();
		} else {
			return $query->result_array();
		}
	}

	public function getInfoSoliCrono($idSol)
	{
		$this->db->select(
			"sc.id,
			sc.numero_entregables,
			sc.tiempo_entregables,
			sc.fecha_entrega_est fecha_limite,
			sc.id_solicitante,
			CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as solicitante",
			false
		);
		$this->db->from("solicitud_compra sc");
		$this->db->join("personas p", "sc.id_solicitante = p.id", "left");
		$this->db->where("sc.id", $idSol);
		$this->db->where("p.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	/* Esta funcion pretende traer un unico idparametro a partir de varias coincidencias, sea por id_aux, etc. */
	public function find_idParametro($codigo)
	{
		$this->db->select("vp.id, vp.id_aux idaux, vp.valor dato, vp.valorx vx, vp.idparametro idpa, vp.valory vy, vp.valorz vz, vp.valora va, vp.valorb vb");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.id", $codigo);
		$this->db->or_where("vp.id_aux", $codigo);
		$this->db->or_where("vp.valory", $codigo);
		$this->db->or_where("vp.valorz", $codigo);
		$this->db->or_where("vp.valora", $codigo);
		$this->db->or_where("vp.valorb", $codigo);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	public function listar_permisos_cronogramas($persona, $idparametro)
	{
		$this->db->select("vp.id_aux id, vp.valor nombre, pc.estado gestion, pc.id_persona");
		$this->db->from("valor_parametro vp");
		$this->db->join("compra_permisos_cronograma pc", "vp.id_aux=pc.id_estado AND pc.id_persona = $persona", "left");
		$this->db->where("vp.idparametro", $idparametro);
		$this->db->where("vp.id_aux != 'Crono_No_Fin'");
		$this->db->where("vp.id_aux != 'Crono_Si_Fin'");
		$this->db->where("vp.estado", 1);
		$this->db->group_by("vp.id_aux");
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $query->result_array();
	}

	public function asignar_encuesta_cronogramas($persona, $estado)
	{
		$datos = ['id_persona' => $persona, 'id_estado' => $estado, 'estado' => 1];
		$this->db->insert('compra_permisos_cronograma', $datos);
		$error = $this->db->_error_message();
		if ($error) {
			return -1;
		}
		return 1;
	}

	public function desasignar_encuesta_cronogramas($persona, $estado)
	{
		$this->db->where('id_persona', $persona);
		$this->db->where('id_estado', $estado);
		$this->db->delete('compra_permisos_cronograma');
		$error = $this->db->_error_message();
		if ($error) {
			return -1;
		}
		return 1;
	}

	public function obtener_permisos_cronogramas($estado_cronograma = "", $persona = "")
	{
		$this->db->select("pc.id_estado estado, p.correo, CONCAT(p.nombre, ' ', p.apellido, ' ',p.segundo_apellido) persona", false);
		$this->db->from('compra_permisos_cronograma pc');
		$this->db->join('personas p', 'p.id = pc.id_persona AND p.estado = 1', 'left');
		$this->db->where('pc.estado', 1);
		if($estado_cronograma != "") $this->db->where('pc.id_estado', $estado_cronograma);
		if($persona != "") $this->db->where('p.id', $persona);
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function guardar_estados_cronograma($id_cronograma, $id_estado, $observacion){
		$datos = [
			'id_cronograma' => $id_cronograma, 
			'id_estado' => $id_estado, 
			'observacion' => $observacion,
			'id_usuario_registra' => $_SESSION['persona'], 
			'estado' => 1
		];
		$this->db->insert('compra_cronograma_estados', $datos);
		$error = $this->db->_error_message();
		if ($error) {
			return -1;
		}
		return 1;
	}

	public function listarEstadosConograma($idCronograma) {
		$this->db->select('cce.*, vp.valor');
		$this->db->from('compra_cronograma_estados cce');
		$this->db->join('valor_parametro vp', 'cce.id_estado = vp.id_aux');
		$this->db->where('cce.id_cronograma', $idCronograma);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function verificar_estados_cronograma($id_cronograma, $id_estado){
		$this->db->select("*", false);
		$this->db->from('compra_cronograma_estados');
		$this->db->where('id_cronograma', $id_cronograma);
		$this->db->where('id_estado', $id_estado);
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function obtener_solicitudes_cronogramas_gestionar($persona){
		$this->db->select("cc.id_solicitud");
		$this->db->from('compras_cronograma cc');
		$this->db->join('compra_permisos_cronograma cpc', 'cc.estado_cronograma = cpc.id_estado', 'left');
		$this->db->where('cpc.id_persona', $persona);
		$this->db->group_by('cc.id_solicitud');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtenerDatosSolicitud($id){
		$this->db->select("sc.dias_no_habiles entrega_estimada, sc.fecha_entrega_real entrega_real");
		$this->db->from('solicitud_compra sc');
		$this->db->where('sc.id', $id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listarPersConEncuestas(){
		$this->db->select("p.id idper, p.correo, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS solicitante, COUNT(sc.id) num_soli", false);
		$this->db->from('solicitud_compra sc');
		$this->db->join('valor_parametro tc', 'sc.id_tipo_compra = tc.id_aux');
		$this->db->join('personas p', 'sc.id_solicitante = p.id');
		$this->db->where('sc.id_estado_solicitud', 'Soli_Fin');
		$this->db->where('(sc.res1_encuesta IS NULL OR sc.res2_encuesta IS NULL OR sc.res3_encuesta IS NULL)');
		$this->db->group_by('p.id');
		$this->db->order_by('solicitante', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listarSoliConEncuestas($idper){
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS solicitante, u.valor tipo_compra, sc.fecha_registra, sc.num_orden, sc.indice_fecha", false);
		$this->db->from('solicitud_compra sc');
		$this->db->join('valor_parametro u', 'sc.id_tipo_compra = u.id_aux');
		$this->db->join('personas p', 'sc.id_solicitante = p.id');
		$this->db->where('sc.id_estado_solicitud', 'Soli_Fin');
		$this->db->where('p.id', $idper);
		$this->db->where('(sc.res1_encuesta IS NULL OR sc.res2_encuesta IS NULL OR sc.res3_encuesta IS NULL)');
		$query = $this->db->get();
		return $query->result_array();
	}

	function listarProveedoresEnc($tipo_encuesta){
		$persona = $_SESSION['persona'];
		$this->db->select("vp.id, vp.valor");
		$this->db->from('valor_parametro vp');
		$this->db->join('solicitud_compra sc', 'vp.id = sc.id_proveedor', 'left');
		$this->db->join('valor_parametro vpa', 'sc.id_area = vpa.id', 'left');
		$this->db->where("sc.id_estado_solicitud =  'Soli_Fin' AND (vpa.id_aux LIKE '%$tipo_encuesta%' OR sc.id_tipo_orden LIKE '%$tipo_encuesta%')");
		if ($tipo_encuesta == 'sga') { $this->db->where("sc.estado_encuesta_sga = 0"); }
		if ($tipo_encuesta == 'sst') { $this->db->where("sc.estado_encuesta_sst = 0"); }
		if ($tipo_encuesta == 'Tip_Mat') { $this->db->where("sc.estado_encuesta_tipmat = 0"); }
		if ($tipo_encuesta == 'Tip_Ser') { $this->db->where("sc.estado_encuesta_tipserv = 0"); $this->db->where("sc.id_solicitante = $persona"); }
		$this->db->group_by('vp.id');
		$query = $this->db->get();
		return $query->result_array();
	}

	/* public function CAMBIAR()
	{
		$this->db->select("");
		$this->db->from("");
		$this->db->where("");
		$query = $this->db->get();
		return $query->result_array();
	} */
}
