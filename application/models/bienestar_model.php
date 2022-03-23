<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class bienestar_model extends CI_Model
{
	/**
	 * Se encarga de guardar los datos que se le pasen por el controlador en la tabla indicada.
	 * @param Array $data 
	 * @param String $tabla 
	 * @return Int
	 */
	public function guardar_datos($data, $tabla, $tipo = 1)
	{
		$tipo == 2 ? $this->db->insert_batch($tabla, $data) : $this->db->insert($tabla, $data);
		$error = $this->db->_error_message();
		return $error ? 1 :  0;
		//return $error;
	}

	/**
	 * Se encarga de modificar los datos que se le pasen por el controlador en la tabla indicada.
	 * @param Array $data 
	 * @param String $tabla 
	 * @param Int $id 
	 * @return Int
	 */
	public function modificar_datos($data, $tabla, $id)
	{
		$this->db->where('id', $id);
		$this->db->update($tabla, $data);
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 0;
	}
	public function obtener_departamentos($buscar)
	{
		$this->db->select("vp.id ,vp.valor,vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro,re.valor relacion", FALSE);
		$this->db->from('valor_parametro vp');
		$this->db->join('valor_parametro re', 'vp.valory = re.id', 'left');
		$this->db->where("vp.idparametro = 3 AND vp.estado = 1 AND vp.valory = '$buscar'");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function obtener_programas_departamento($id)
	{
		$this->db->select("pp.vp_secundario_id id, vp.valor");
		$this->db->from('permisos_parametros pp');
		$this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
		$this->db->where("pp.vp_principal_id = '$id'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_ubicaciones($id_lugar)
	{
		$this->db->select("vp.valor, vp.id ");
		$this->db->from('permisos_parametros pp');
		$this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
		$this->db->where('pp.vp_principal_id', $id_lugar);
		$this->db->where('vp.estado', 1);
		$this->db->order_by('vp.valor');
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function get_tematicas_disponibles($fecha_inicio, $fecha_fin)
	{
		$this->db->select("vp.valor, vp.id ");
		$this->db->from('valor_parametro vp');
		$this->db->where("vp.estado = 1 AND vp.idparametro = 121 AND vp.id NOT IN (SELECT bb.id_tematica bloqueo FROM bienestar_bloqueos bb 
		WHERE (('$fecha_inicio' BETWEEN bb.fecha_inicio AND bb.fecha_fin) OR ('$fecha_fin' BETWEEN bb.fecha_inicio AND bb.fecha_fin) OR (bb.fecha_inicio BETWEEN '$fecha_inicio' AND '$fecha_fin') OR (bb.fecha_fin BETWEEN '$fecha_inicio' AND '$fecha_fin')) 
		AND ('$fecha_inicio' < bb.fecha_fin AND '$fecha_fin' > bb.fecha_inicio))");
		// AND ('$fecha_inicio' < bb.fecha_fin AND '$fecha_fin' > bb.fecha_inicio) AND (bb.id_tematica = vp.id OR bb.id_tematica = 0)) = 0");
		$this->db->order_by('vp.valor');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_estrategias($id_estrategia, $filtro)
	{
		if ($filtro) {
			$this->db->select("pp.id, vp.valor");
		} else $this->db->select("pp.vp_secundario_id id, vp.valor");
		$this->db->from('permisos_parametros pp');
		$this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
		$this->db->where("pp.vp_principal_id = '$id_estrategia'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_solicitudes($id, $estrategia, $estado, $fecha, $fecha_2, $excel)
	{
		if ($id == 0) $id = '';
		if ($estrategia == 0) $estrategia = '';
		if ($estado === 'vacio') $estado = '';
		if ($fecha == 0) $fecha = '';
		if ($fecha_2 == 0) $fecha_2 = '';

		$perfil = $_SESSION['perfil'];
		$persona = $_SESSION['persona'];
		$administra = $perfil == 'Per_Admin' || $perfil == 'Per_Bin'  ? true : false;
		$funcionario = $perfil == 'Bin_Fun' ? true : false;
		$filtro = empty($estado) && empty($estrategia) ? false : true;
		if (empty($fecha) && empty($fecha_2)) {
			$fecha =  '1900-05-02';
			$fecha_2 = $this->_data_last_month_day();
		}
		if ($excel == 1) {
			$this->db->select("bs.fecha_registra,CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) solicitante,bs.telefono,bs.fecha_inicio, bs.fecha_fin, pr.valor programa, bs.materia, DAYOFWEEK(bs.fecha_inicio) as dia_semana, TIMEDIFF(bs.fecha_fin,bs.fecha_inicio) as duracion,(SELECT COUNT(*) FROM bienestar_estudiantes be WHERE be.id_solicitud = bs.id AND be.estado = 1 ) cant_estudiantes,u.valor ubicacion,l.valor lugar, pa.valor estrategia, t.valor tematica,CONCAT(pf.nombre,' ',pf.apellido, ' ',pf.segundo_apellido) coordinador, es.valor estado_sol, bs.motivo as motivo ", false);
		} else {
			$this->db->select("bs.*,p.correo, bs.telefono, p.id_perfil, CONCAT(pf.nombre,' ',pf.apellido, ' ',pf.segundo_apellido) funcionario, ts.valor tipo_solicitud, es.valor estado_sol, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) solicitante,(SELECT COUNT(*) FROM bienestar_estudiantes be WHERE be.id_solicitud = bs.id AND be.estado = 1 ) cant_estudiantes ,pr.valor programa,l.valor lugar, pa.valor estrategia, t.valor tematica, u.valor ubicacion, TIMEDIFF(bs.fecha_fin,bs.fecha_inicio) as duracion, DAYOFWEEK(bs.fecha_inicio) as dia_semana", false);
		}
		$this->db->from('bienestar_solicitudes bs');
		$this->db->join('personas p', 'bs.id_solicitante = p.id');
		$this->db->join('valor_parametro l', 'bs.id_lugar = l.id');
		$this->db->join('valor_parametro pa', 'bs.id_estrategia = pa.id');
		$this->db->join('valor_parametro t', 'bs.id_tematica = t.id');
		$this->db->join('valor_parametro u', 'bs.id_ubicacion = u.id');
		$this->db->join('valor_parametro pr', 'bs.id_programa = pr.id');
		$this->db->join('valor_parametro es', 'bs.id_estado_sol = es.id_aux');
		$this->db->join('valor_parametro ts', 'bs.id_tipo_solicitud = ts.id_aux');
		$this->db->join('bienestar_funcionarios bf', 'bs.id = bf.id_solicitud AND bf.estado = 1', 'left');
		$this->db->join('bienestar_funcionarios_relacion bfr', 'bs.id_programa = bfr.id_relacion AND bfr.estado=1', 'left');
		$this->db->join('personas pf', 'bf.id_persona = pf.id', 'left');

		if ($administra || $funcionario) {
			if ($filtro || $fecha != '1900-05-02' || $fecha_2 != $this->_data_last_month_day()) {
				$this->db->where("(bs.id_estrategia LIKE '%$estrategia%' AND bs.id_estado_sol LIKE '%$estado%' AND (DATE_FORMAT(bs.fecha_registra,'%Y-%m-%d') >= '$fecha' AND DATE_FORMAT(bs.fecha_registra,'%Y-%m-%d') <= '$fecha_2'))");
			} else if (!empty($id)) $this->db->where("(bs.id_estado_sol = 'Bin_Sol_E' OR bs.id_estado_sol = 'Bin_Rev_E' OR bs.id_estado_sol = 'Bin_Tra_E' OR bs.id_estado_sol = 'Bin_Can_E' OR bs.id_estado_sol = 'Bin_Neg_E' OR bs.id_estado_sol = 'Bin_Rep_E')");
			else $this->db->where("(bs.id_estado_sol = 'Bin_Sol_E' OR bs.id_estado_sol = 'Bin_Rev_E' OR bs.id_estado_sol = 'Bin_Tra_E' OR bs.id_estado_sol = 'Bin_Rep_E')");

			if ($funcionario) $this->db->where("((bf.id_persona = $persona AND bf.estado = 1)  OR bs.id_solicitante = $persona)");
		} else {
			$this->db->where("(bfr.id_persona = $persona OR bs.id_solicitante = $persona)");
			if ($filtro && empty($id)) {
				$this->db->where("(bs.id_estrategia LIKE '%$estrategia%' AND bs.id_estado_sol LIKE '%$estado%' AND (DATE_FORMAT(bs.fecha_registra,'%Y-%m-%d') >= '$fecha' AND DATE_FORMAT(bs.fecha_registra,'%Y-%m-%d') <= '$fecha_2'))");
			} else if (!empty($id)) $this->db->where("(bs.id_estado_sol = 'Bin_Sol_E' OR bs.id_estado_sol = 'Bin_Rev_E' OR bs.id_estado_sol = 'Bin_Tra_E' OR bs.id_estado_sol = 'Bin_Can_E' OR bs.id_estado_sol = 'Bin_Neg_E' OR bs.id_estado_sol = 'Bin_Rep_E')");
			else $this->db->where("(bs.id_estado_sol = 'Bin_Sol_E' OR bs.id_estado_sol = 'Bin_Rev_E' OR bs.id_estado_sol = 'Bin_Tra_E' OR bs.id_estado_sol = 'Bin_Rep_E' OR bs.id_estado_sol = 'Bin_Can_E' OR bs.id_estado_sol = 'Bin_Neg_E')");
		}
		if (!empty($id)) $this->db->where('bs.id', $id);
		$this->db->group_by("bs.id");
		$this->db->where("bs.estado", 1);

		$this->db->_protect_identifiers = false;
		$this->db->order_by("FIELD (bs.id_estado_sol,'Bin_Sol_E','Bin_Rev_E','Bin_Rep_E','Bin_Tra_E','Bin_Can_E','Bin_Neg_E')");
		$this->db->order_by("bs.fecha_registra");
		$this->db->_protect_identifiers = true;
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_materias_por_docente($identificacion)
	{
		$this->db->select("md.*,CONCAT(md.cod_materia,md.cod_grupo) id,CONCAT(md.materia,' / ',md.grupo) valor", FALSE);
		$this->db->from("materias_docentes md");
		$this->db->where("md.identificacion_doc", $identificacion);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function obtener_estudiantes_por_materia($materia)
	{
		$this->db->select("est.id,CONCAT(est.nombre,' ',est.apellido,' ',est.segundo_apellido) nombre_completo,est.identificacion, 'visitantes' tabla", FALSE);
		$this->db->from("materias_estudiantes me");
		$this->db->join("visitantes est", "me.identificacion_est = est.identificacion");
		$this->db->where("CONCAT(me.cod_materia,me.cod_grupo) = '$materia'");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function buscar_estudiante($dato)
	{
		$this->db->select("v.identificacion,v.id,CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) as nombre_completo", false);
		$this->db->from('visitantes v');
		$this->db->where("(CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) LIKE '%" . $dato . "%' OR v.identificacion LIKE '%" . $dato . "%') AND v.estado=1");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function buscar_persona($dato, $filtro = "", $id_solicitud = 0, $id_tematica = '', $fecha_inicio, $fecha_fin, $dia = '', $dia_f = '')
	{
		$this->db->select("p.*,p.identificacion,p.id,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
		$this->db->from("personas p");
		if ($filtro) {
			if ($filtro == 2) {
				$this->db->join('bienestar_funcionarios_relacion ft', 'ft.id_persona = p.id');
				$this->db->where("ft.id_relacion", $id_tematica);
				$this->db->join('bienestar_funcionarios_horario fh', 'fh.id_persona = ft.id_persona');
				$this->db->join('bienestar_horario h', 'h.id = fh.id_horario');
				$this->db->join('valor_parametro vp', 'h.id_dia = vp.id');
				$this->db->where("vp.id_aux = '$dia' AND vp.id_aux = '$dia_f'");
				$this->db->where("(TIME('$fecha_inicio') >= h.hora_inicio AND TIME('$fecha_fin') <= h.hora_fin) AND h.estado=1");
				$this->db->where("fh.estado = 1 AND ft.estado = 1");
				// $this->db->where("ft.estado = 1");
			} else $this->db->where("p.id_perfil", "Bin_Fun");
		}
		// if($filtro) $this->db->where("p.id_perfil","Bin_Fun");
		// else if($filtro == 2){
		// 	$this->db->join('bienestar_solicitudes bs', "bs.id = $id_solicitud");
		// 	$this->db->join('bienestar_funcionarios_relacion ft', 'bs.id_relacion = ft.id_relacion AND ft.id_persona = p.id');
		// } 
		$this->db->where("(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function validar_funcionario($identificacion, $id_solicitud)
	{ //validar funcionario que no sea estudiante de la clase solicitada
		$bloq = 0;
		$this->db->select("v.*");
		$this->db->from('visitantes v');
		$this->db->join('materias_estudiantes me', 'me.identificacion_est=v.identificacion');
		$this->db->where('v.identificacion', $identificacion);
		$this->db->where("CONCAT(me.cod_materia,me.cod_grupo)=(select cod_materia from bienestar_solicitudes where id=" . $id_solicitud . ")");
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$bloq = 1;
		}
		return $bloq;
	}

	public function traer_ultima_solicitud($person, $tabla, $usuario)
	{
		$this->db->select("*");
		$this->db->from($tabla);
		$this->db->order_by("id", "desc");
		$this->db->where($usuario, $person);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}
	public function listar_estudiantes_solicitud($id)
	{
		$this->db->select("bs.id_estado_sol,bs.id_solicitante usuario_registra, be.*, est.correo, en.id realizo, CONCAT(est.nombre,' ',est.apellido,' ',est.segundo_apellido) nombre_completo,est.identificacion", false);
		$this->db->from('bienestar_estudiantes be');
		$this->db->join('bienestar_solicitudes bs', 'bs.id = be.id_solicitud');
		$this->db->join('visitantes est', 'be.id_persona = est.id');
		$this->db->join('bienestar_encuesta en', 'be.id = en.id_estudiante', 'left');
		$this->db->where('be.id_solicitud', $id);
		$this->db->where('be.estado', "1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function consulta_solicitud_id($id)
	{
		$this->db->select("bs.*,ts.valor tipo_solicitud, TIMESTAMPDIFF(MINUTE,bs.fecha_inicio,bs.fecha_fin) duracion_minutos,(SELECT vp.id FROM valor_parametro vp WHERE vp.idparametro = 124 AND vp.valory = duracion_minutos ) id_duracion,vpid.id_aux programa_id, p.correo,CONCAT(pf.nombre,' ',pf.apellido,' ',pf.segundo_apellido) coordinador, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) solicitante, ts.valor tipo_solicitud, DATE_FORMAT(bs.fecha_inicio, '%Y-%m-%d %H:%i:%s') as fecha_i, DATE_FORMAT(bs.fecha_fin, '%Y-%m-%d %H:%i:%s') as fecha_f,  l.valor lugar, pa.valor estrategia, t.valor tematica, u.valor ubicacion", false);
		$this->db->from('bienestar_solicitudes bs');
		$this->db->join('valor_parametro l', 'bs.id_lugar = l.id');
		$this->db->join('valor_parametro pa', 'bs.id_estrategia = pa.id');
		$this->db->join('valor_parametro t', 'bs.id_tematica = t.id');
		$this->db->join('valor_parametro u', 'bs.id_ubicacion = u.id');
		$this->db->join('valor_parametro ts', 'bs.id_tipo_solicitud = ts.id_aux');
		$this->db->join('personas p', 'bs.id_solicitante = p.id');
		$this->db->join('personas pf', 'bs.id_coordinador = pf.id', 'left');
		$this->db->join('valor_parametro vpid', 'bs.id_programa = vpid.id', 'left');
		$this->db->where("bs.estado", 1);
		$this->db->where('bs.id', $id);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function estudiante_solicitud($identificacion, $id_solicitud)
	{
		$this->db->select("v.*");
		$this->db->from('visitantes v');
		$this->db->join('bienestar_estudiantes be', "be.id_persona = v.id AND be.estado = 1 AND be.id_solicitud = $id_solicitud");
		$this->db->where('v.identificacion', $identificacion);
		$query = $this->db->get()->result_array();
		return $query;
	}

	public function programasGenericas($cod_programa)
	{
		$this->db->select("vp.id, vp.valor");
		$this->db->from('valor_parametro vp');
		$this->db->where('vp.id_aux', $cod_programa);
		$this->db->where('vp.valory', 2);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function verificarDisponibilidad($fecha_inicio, $fecha_fin, $estrategia, $funcionario)
	{
		$this->db->select("CONCAT(pf.nombre,' ',pf.apellido, ' ',pf.segundo_apellido) funcionario,bs.*, es.valor estado_sol, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) solicitante,(SELECT COUNT(*) FROM bienestar_estudiantes be WHERE be.id_solicitud = bs.id AND be.estado = 1 ) cant_estudiantes ,pr.valor programa,l.valor lugar, pa.valor estrategia, t.valor tematica, u.valor ubicacion, TIMEDIFF(bs.fecha_fin,bs.fecha_inicio) as duracion, DAYOFWEEK(bs.fecha_inicio) as dia_semana", false);
		$this->db->from('bienestar_solicitudes bs');
		$this->db->join('personas p', 'bs.id_solicitante = p.id');
		$this->db->join('valor_parametro l', 'bs.id_lugar = l.id');
		$this->db->join('valor_parametro pa', 'bs.id_estrategia = pa.id');
		$this->db->join('valor_parametro t', 'bs.id_tematica = t.id');
		$this->db->join('valor_parametro u', 'bs.id_ubicacion = u.id');
		$this->db->join('valor_parametro pr', 'bs.id_programa = pr.id');
		$this->db->join('valor_parametro es', 'bs.id_estado_sol = es.id_aux');
		$this->db->join('bienestar_funcionarios bf', 'bs.id = bf.id_solicitud');
		$this->db->join('personas pf', 'bf.id_persona = pf.id');
		$this->db->where("(bs.id_estrategia LIKE '%$estrategia%' AND p.id LIKE '%$funcionario%')");
		$this->db->where("('$fecha_inicio' BETWEEN bs.fecha_inicio AND bs.fecha_fin OR '$fecha_fin' BETWEEN bs.fecha_inicio AND bs.fecha_fin OR bs.fecha_inicio BETWEEN '$fecha_inicio' AND '$fecha_fin' OR bs.fecha_fin BETWEEN '$fecha_inicio' AND '$fecha_fin')");
		$this->db->where("(bs.id_estado_sol = 'Bin_Rev_E' OR bs.id_estado_sol = 'Bin_Tra_E')");
		$this->db->where("bs.estado", 1);
		$this->db->group_by('bs.id');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_funcionarios_solicitud($id)
	{
		$this->db->select("be.*, CONCAT(est.nombre,' ',est.apellido,' ',est.segundo_apellido) nombre_completo,est.identificacion", false);
		$this->db->from('bienestar_funcionarios be');
		$this->db->join('personas est', 'be.id_persona = est.id');
		$this->db->where('be.id_solicitud', $id);
		$this->db->where('be.estado', "1");
		$query = $this->db->get();
		return $query->result_array();
	}


	public function funcionario_solicitud($identificacion, $id_solicitud)
	{
		$this->db->select("p.*");
		$this->db->from('personas p');
		$this->db->join('bienestar_funcionarios be', "be.id_persona = p.id AND be.estado = 1 AND be.id_solicitud = $id_solicitud");
		$this->db->where('p.identificacion', $identificacion);
		$query = $this->db->get()->result_array();
		return $query;
	}

	public function listar_estados($id_solicitud)
	{
		$this->db->select("f.*, v.valor estado, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona", false);
		$this->db->from('bienestar_estados f');
		$this->db->join('valor_parametro v', 'f.id_estado = v.id_aux');
		$this->db->join('personas p', 'f.id_usuario_registra = p.id');
		$this->db->where('f.id_solicitud', $id_solicitud);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function verificar_codigo_acceso($code)
	{
		$this->db->select("ben.id realizo, be.id, be.id_persona id_estudiante");
		$this->db->from("bienestar_estudiantes be");
		$this->db->join("bienestar_encuesta ben", "ben.id_estudiante = be.id", 'left');
		$this->db->where("be.codigo_acceso", $code);
		$query = $this->db->get()->row();
		return $query;
	}

	public function verificar_usuario($id)
	{
		$this->db->select("p.correo,p.nombre, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS nombre_completo", false);
		$this->db->from("visitantes p");
		$this->db->where("p.id", $id);
		$query = $this->db->get()->row();
		return $query;
	}

	public function consulta_estudiante_id($id)
	{
		$this->db->select("vp.valor tematica, v.correo, bs.id_estado_sol, CONCAT(v.nombre, ' ', v.apellido, ' ', v.segundo_apellido) AS nombre_completo", false);
		$this->db->from("bienestar_estudiantes be");
		$this->db->join("visitantes v", "v.id = be.id_persona");
		$this->db->join("bienestar_solicitudes bs", "be.id_solicitud = bs.id");
		$this->db->join('valor_parametro vp', 'bs.id_tematica = vp.id');
		$this->db->where("be.id", $id);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function ver_detalle_encuesta($id)
	{
		$this->db->select("ben.id realizo, be.id");
		$this->db->from("bienestar_estudiantes be");
		$this->db->join("bienestar_encuesta ben", "ben.id_estudiante = be.id", 'left');
		$this->db->where("be.codigo_acceso", $id);
		$query = $this->db->get()->row();
		return $query;
	}

	public function obtener_id_permiso($vp_principal_id)
	{
		$this->db->select("vp.vp_secundario_id");
		$this->db->from("permisos_parametros vp");
		$this->db->where('vp.vp_principal_id', $vp_principal_id);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}
	public function listar_valor_parametro($parametro, $id_tematica)
	{
		$this->db->select("vp.id,vp.valor, vp.idparametro");
		$this->db->from('valor_parametro vp');
		$this->db->where("vp.idparametro = $parametro AND vp.estado = 1");
		if ($parametro == 3) $this->db->where("vp.valory = 2");
		if ($id_tematica > 0) $this->db->where("vp.id NOT IN(select vp_secundario_id from permisos_parametros where vp_principal_id = $id_tematica)");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_funcionarios_tematicas($id_relacion)
	{
		$this->db->select("bt.*, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) valor, p.identificacion", FALSE);
		$this->db->from("bienestar_funcionarios_relacion bt");
		$this->db->join('personas p', 'bt.id_persona = p.id');
		$this->db->where('bt.id_relacion', $id_relacion);
		$this->db->where("bt.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function consulta_tematicas_funcionarios($id_relacion, $id_persona)
	{
		$this->db->select("bt.*");
		$this->db->from('bienestar_funcionarios_relacion bt');
		$this->db->where('bt.id_persona', $id_persona);
		$this->db->where('bt.id_relacion', $id_relacion);
		$this->db->where("bt.estado", 1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function listar_horarios_funcionarios()
	{
		$this->db->select("hf.*, vp.valor as dia");
		$this->db->from("bienestar_horario hf");
		$this->db->join('valor_parametro vp', 'hf.id_dia = vp.id');
		$this->db->where("hf.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_funcionarios_horarios($id_horario)
	{
		$this->db->select("fh.*, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.identificacion", FALSE);
		$this->db->from("bienestar_funcionarios_horario fh");
		$this->db->join('personas p', 'fh.id_persona = p.id');
		$this->db->where("fh.id_horario", $id_horario);
		$this->db->where("fh.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function validar_funcionario_horario($id_persona, $id_horario)
	{
		$this->db->select("fh.*", FALSE);
		$this->db->from("bienestar_funcionarios_horario fh");
		$this->db->where("fh.id_horario = $id_horario AND fh.id_persona = $id_persona");
		$this->db->where("fh.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function validar_horario_funcionario($id_dia, $hora_inicio, $hora_fin, $id_horario = '', $observacion = '')
	{
		$this->db->select("hf.*");
		$this->db->from("bienestar_horario hf");
		$this->db->where('hf.id_dia', $id_dia);
		$this->db->where("('$hora_inicio' BETWEEN hf.hora_inicio AND hf.hora_fin OR hf.hora_inicio BETWEEN '$hora_inicio' AND '$hora_fin') AND '$hora_fin' BETWEEN hf.hora_inicio AND hf.hora_fin");
		$this->db->where("hf.observacion", $observacion);
		$this->db->where("hf.estado", 1);
		if ($id_horario) $this->db->where("hf.id <> $id_horario");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_coordinadores_por_programa($id)
	{
		$this->db->select("fr.id_persona id, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) valor", FALSE);
		$this->db->from('valor_parametro vp');
		$this->db->join('bienestar_funcionarios_relacion fr', 'fr.id_relacion = vp.id');
		$this->db->join('personas p', 'fr.id_persona= p.id');
		$this->db->where('fr.estado', 1);
		$this->db->where('vp.id_aux', $id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_encuestas($id_solicitud)
	{
		$this->db->select("pr.valor programa,te.valor tematica,pa.valor estrategia, be.actividad, be.servicio, be.apropiado, be.integral, be.metodologia, be.otros, be.fecha_registra");
		$this->db->from('bienestar_encuesta be');
		$this->db->join('bienestar_estudiantes bes', 'be.id_estudiante = bes.id');
		$this->db->join("bienestar_solicitudes bs", "bes.id_solicitud = bs.id");
		$this->db->join('valor_parametro te', 'bs.id_tematica = te.id');
		$this->db->join('valor_parametro pa', 'bs.id_estrategia = pa.id');
		$this->db->join('valor_parametro pr', 'bs.id_programa = pr.id');
		$this->db->where('bes.id_solicitud', $id_solicitud);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_encuestas_exportar()
	{
		$this->db->select("pr.valor programa,te.valor tematica,pa.valor estrategia, be.actividad, be.servicio, be.apropiado, be.integral, be.metodologia, be.otros, be.fecha_registra");
		$this->db->from('bienestar_encuesta be');
		$this->db->join('bienestar_estudiantes bes', 'be.id_estudiante = bes.id');
		$this->db->join("bienestar_solicitudes bs", "bes.id_solicitud = bs.id");
		$this->db->join('valor_parametro te', 'bs.id_tematica = te.id');
		$this->db->join('valor_parametro pa', 'bs.id_estrategia = pa.id');
		$this->db->join('valor_parametro pr', 'bs.id_programa = pr.id');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function _data_first_month_day()
	{
		$month = date('m');
		$year = date('Y');
		return date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
	}

	public function _data_last_month_day()
	{
		$month = date('m');
		$year = date('Y');
		$day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

		return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year + 1));
	}
	public function listar_modificaciones($id_solicitud)
	{
		$this->db->select("bm.*");
		$this->db->from('bitacora_modificaciones bm');
		$this->db->where("bm.id_solicitud = '$id_solicitud' AND bm.tabla = 'bienestar_solicitudes' AND (bm.nombre_campo = 'Fecha Inicio' OR bm.nombre_campo = 'Fecha Fin')");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_bloqueos()
	{
		$this->db->select("bb.*, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) usuario_registra,CONCAT(pp.nombre,' ',pp.apellido, ' ',pp.segundo_apellido) usuario_elimina, vp.valor as tematica", false);
		$this->db->from('bienestar_bloqueos bb');
		$this->db->join('personas p', 'bb.id_usuario_registra = p.id', 'left');
		$this->db->join('personas pp', 'bb.id_usuario_elimina = pp.id', 'left');
		$this->db->join('valor_parametro vp', 'bb.id_tematica = vp.id', 'left');
		$this->db->where('bb.estado', "1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function fechaDisponible($fecha_inicio, $fecha_fin, $tematica)
	{
		if ($tematica == '') $tematica = 0;
		$this->db->select("bb.nombre, bb.descripcion, bb.fecha_inicio, bb.fecha_fin");
		$this->db->from('bienestar_bloqueos bb');
		$this->db->where("(('$fecha_inicio' BETWEEN bb.fecha_inicio AND bb.fecha_fin) OR ('$fecha_fin' BETWEEN bb.fecha_inicio AND bb.fecha_fin) OR (bb.fecha_inicio BETWEEN '$fecha_inicio' AND '$fecha_fin') OR (bb.fecha_fin BETWEEN '$fecha_inicio' AND '$fecha_fin')) AND ('$fecha_inicio' < bb.fecha_fin AND '$fecha_fin' > bb.fecha_inicio)");
		$this->db->where("(bb.id_tematica = $tematica OR  bb.id_tematica = 0)");
		$this->db->where("bb.estado", 1);
		$this->db->order_by("bb.fecha_inicio, bb.fecha_fin");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function funcionariosTematicas($id_tematica, $cod_materia, $fecha_inicio, $fecha_fin, $dia = '', $dia_f = '')
	{
		$this->db->select("bt.id_relacion, bt.id_persona funcionario, (SELECT COUNT(*) FROM bienestar_funcionarios bf INNER JOIN bienestar_solicitudes bs ON bs.id = bf.id_solicitud WHERE bt.id_persona = bf.id_persona and bf.estado=1 and DATE_FORMAT(bs.fecha_inicio,'%Y-%m-%d')=DATE('$fecha_inicio') AND (bs.id_estado_sol = 'Bin_Sol_E' OR bs.id_estado_sol = 'Bin_Rev_E' OR bs.id_estado_sol = 'Bin_Tra_E' OR bs.id_estado_sol = 'Bin_Rep_E')) cantidad", false);
		$this->db->from('bienestar_funcionarios_relacion bt');
		$this->db->join('bienestar_funcionarios_horario fh', 'fh.id_persona = bt.id_persona');
		$this->db->join('bienestar_horario h', 'h.id = fh.id_horario');
		$this->db->join('valor_parametro vp', 'h.id_dia = vp.id');
		$this->db->where("fh.estado", 1);
		$this->db->where("vp.id_aux = '$dia' AND vp.id_aux = '$dia_f'");
		$this->db->where("(TIME('$fecha_inicio') >= h.hora_inicio AND TIME('$fecha_fin') <= h.hora_fin) AND h.estado=1");
		$this->db->where("bt.id_relacion", $id_tematica);
		$this->db->where("bt.estado", 1);
		$this->db->where("bt.id_persona NOT IN (SELECT p.id FROM personas p INNER JOIN materias_estudiantes me ON me.identificacion_est=p.identificacion WHERE CONCAT(me.cod_materia,me.cod_grupo)='$cod_materia')");
		$this->db->group_by("bt.id_persona");
		$query = $this->db->get();
		return $query->result_array();
	}


	public function consultaSolicitudes($funcionario, $fecha_inicio, $fecha_fin, $id_solicitud, $format = 'Y-m-d H:i:s')
	{
		$fecha_inicio = date($format, strtotime($fecha_inicio . " + 1 minutes"));
		$fecha_fin = date($format, strtotime($fecha_fin . " - 1 minutes"));
		$this->db->select("bs.fecha_inicio,bs.fecha_fin, bf.id_persona funcionario, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_funcionario, bs.id", FALSE);
		$this->db->from('bienestar_solicitudes bs');
		$this->db->join('bienestar_funcionarios bf', 'bs.id = bf.id_solicitud');
		$this->db->join('personas p', 'bf.id_persona= p.id');
		$this->db->where("bf.id_persona", $funcionario);
		$this->db->where("(('$fecha_inicio' BETWEEN bs.fecha_inicio AND bs.fecha_fin) OR ('$fecha_fin' BETWEEN bs.fecha_inicio AND bs.fecha_fin) OR (bs.fecha_inicio BETWEEN '$fecha_inicio' AND '$fecha_fin') OR (bs.fecha_fin BETWEEN '$fecha_inicio' AND '$fecha_fin') AND (bs.id_estado_sol = 'Bin_Sol_E' OR bs.id_estado_sol = 'Bin_Rev_E' OR bs.id_estado_sol = 'Bin_Tra_E' OR bs.id_estado_sol = 'Bin_Rep_E')) and bs.id_estado_sol <> 'Bin_Can_E'");
		if ($id_solicitud) $this->db->where("bs.id <> $id_solicitud");
		$this->db->where("bs.estado", 1);
		$this->db->where("bf.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function consulta_bloqueos($fecha_inicio, $fecha_fin)
	{
		$date = date_create($fecha_inicio);
		$fecha = date_format($date, 'Y-m-d');
		$this->db->select("bb.nombre, bb.descripcion, bb.fecha_inicio, bb.fecha_fin");
		$this->db->from('bienestar_bloqueos bb');
		// $this->db->where("DATE(bb.fecha_inicio)",$fecha);
		$this->db->where("('$fecha' BETWEEN DATE(bb.fecha_inicio) AND DATE(bb.fecha_fin))");
		$this->db->where("bb.estado", 1);
		$this->db->order_by("bb.fecha_inicio, bb.fecha_fin");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function validar_funcionario_asignado($id_solicitud, $fecha_inicio, $fecha_fin, $dia, $dia_f)
	{
		$this->db->select("bf.*", false);
		$this->db->from('bienestar_funcionarios bf');
		$this->db->join('bienestar_funcionarios_horario fh', 'fh.id_persona = bf.id_persona');
		$this->db->join('bienestar_horario h', 'h.id = fh.id_horario');
		$this->db->join('valor_parametro vp', 'h.id_dia = vp.id');
		$this->db->where("fh.estado", 1);
		$this->db->where("vp.id_aux = '$dia' AND vp.id_aux = '$dia_f'");
		$this->db->where("(TIME('$fecha_inicio') >= h.hora_inicio AND TIME('$fecha_fin') <= h.hora_fin) AND h.estado=1");
		$this->db->where("bf.id_solicitud", $id_solicitud);
		$this->db->where("bf.id_solicitud NOT IN (select id from bienestar_solicitudes bs where ('$fecha_inicio' BETWEEN bs.fecha_inicio AND bs.fecha_fin) OR ('$fecha_fin' BETWEEN bs.fecha_inicio AND bs.fecha_fin) OR (bs.fecha_inicio BETWEEN '$fecha_inicio' AND '$fecha_fin') OR (bs.fecha_fin BETWEEN '$fecha_inicio' AND '$fecha_fin') AND (bs.id_estado_sol = 'Bin_Sol_E' OR bs.id_estado_sol = 'Bin_Rev_E' OR bs.id_estado_sol = 'Bin_Tra_E' OR bs.id_estado_sol = 'Bin_Rep_E') AND bs.id <> $id_solicitud)");
		$this->db->where("bf.estado", 1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function eliminar_funcionarios_solicitud($data, $id_solicitud, $funcionario, $filtro)
	{
		$this->db->where("id_solicitud", $id_solicitud);
		if ($filtro == 1) $this->db->where_not_in('id', $funcionario);
		else $this->db->where("id <> $funcionario");
		$this->db->update('bienestar_funcionarios', $data);
		$error = $this->db->_error_message();
		if ($error) {
			return "error";
		}
		return 0;
	}

	public function get_funcionario_solicitud($id_solicitud)
	{
		$this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona, p.correo", false);
		$this->db->from('personas p');
		$this->db->join('bienestar_funcionarios be', 'be.id_persona = p.id');
		$this->db->where('be.id_solicitud', $id_solicitud);
		$query = $this->db->get()->result_array();
		return $query;
	}
}

// select bt.id_relacion, bt.id_persona, (SELECT COUNT(*) FROM bienestar_funcionarios bf WHERE bt.id_persona = bf.id_persona) cantidad
// from bienestar_funcionarios_relacion bt
// where bt.id_relacion = 19246 AND  bt.estado = 1
