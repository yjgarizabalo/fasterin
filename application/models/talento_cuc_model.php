<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class talento_cuc_model extends CI_Model
{
	public function buscar_persona($where){
		$this->db->select("p.identificacion,p.id,p.correo,p.id_tipo_identificacion,p.fecha_expedicion,p.lugar_expedicion,p.fecha_nacimiento,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,u2.valor tipo_identificacion, p.genero, p.telefono,vp.valor cargo", false);
		$this->db->from('personas p');
		$this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
		$this->db->join('valor_parametro vp', 'vp.id = p.id_cargo_sap', 'left');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function traer_registro_id($person, $tabla, $usuario){
		$this->db->select("*");
		$this->db->from($tabla);
		$this->db->order_by("id", "desc");
		$this->db->where($usuario, $person);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}
	
	public function get_periodo_actual(){
		$this->db->select("vp.valor per");
		$this->db->from('valor_parametro vp');
		$this->db->where("vp.idparametro = 246 and vp.estado = 1");
		$query = $this->db->get();
		return $query->row()->per;
	}

	  public function listar_personas($buscar,$id_persona,$fecha_i,$fecha_f,$periodo){
		$perfil = $_SESSION['perfil'];
		$persona = $_SESSION['persona'];
		$per = $this->get_periodo_actual();
		if($periodo) $per = $periodo;
		$this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,
		p.identificacion,
		p.correo,
		c.valor cargo,
		es.id id_solicitud,
		es.id_estado_eval estado_eval, 
		es.periodo,
		(SELECT count(sop.id) 
		FROM talentocuc_soportes_plan_formacion sop 
		INNER JOIN talentocuc_actividad_persona ap ON ap.actividad_id = sop.id_competencia and ap.persona_id = $persona and ap.estado = 1
		WHERE sop.id_persona = p.identificacion and sop.estado = 1 and sop.estado_apro = 0) soportes,
		(SELECT COUNT(ctc.id) FROM competencias_talento_cuc ctc WHERE ctc.id_persona = p.id AND ctc.estado = 1) ingreso", false);
		$this->db->from('personas p');
        $this->db->join('valor_parametro c', 'p.id_cargo_sap=c.id','left');
		if($fecha_i && $fecha_f) $this->db->join('evaluacion_solicitud es', "es.id_evaluado=p.identificacion and es.estado = 1 and es.periodo = '$per' and DATE_FORMAT(es.fecha_registra, '%Y-%m-%d') >='$fecha_i' and DATE_FORMAT(es.fecha_registra, '%Y-%m-%d') <= '$fecha_f'");
        else $this->db->join('evaluacion_solicitud es', "es.id_evaluado=p.identificacion and es.estado = 1 and es.periodo = '$per'",'left');
		if(!empty($buscar)) $this->db->where("(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $buscar . "%' OR p.identificacion LIKE '%" . $buscar . "%' OR p.correo LIKE '%" . $buscar . "%')");
		if(!empty($id_persona)) $this->db->where('p.id', $id_persona);
		$this->db->group_by("p.id");
		$query = $this->db->get();
        return $query->result_array();
	}

	public function get_solicitud($id,$persona='',$filtro=''){
		$this->db->select("es.*, t.id id_metodo, t.valor tipo, p.correo, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, cr.valor departamento, vpe.valor state", false);
		$this->db->from('evaluacion_solicitud es');
		$this->db->join('valor_parametro t', 'es.id_metodo_eval = t.id_aux');
		$this->db->join('personas p', 'p.identificacion = es.id_evaluado');
		$this->db->join('valor_parametro cr', 'p.id_cargo_sap = cr.id','left');
		$this->db->join('valor_parametro vpe', 'es.id_estado_eval = vpe.id_aux');
		if ($persona) $this->db->where("p.id = $persona");
		if ($id) $this->db->where('es.id', $id);
		if ($filtro) $this->db->where("(es.id_estado_eval = 'Eval_Env' OR es.id_estado_eval = 'Eval_Pro' OR es.id_estado_eval = 'Eval_Act_Env' OR es.id_estado_eval = 'Eval_Act_Fin')");
		$query = $this->db->get();
		return $query->row();
	}

	public function get_resultados_detalles($id_solicitud){
		$this->db->select("rd.*, vpa.valor area_apreciacion, vpt.valor tipo_evaluador, 
		CASE vpt.valory  
		WHEN '1' THEN p.identificacion  
		WHEN '2' THEN pc.identificacion  
		WHEN '3' THEN pj.identificacion  
		WHEN '4' THEN rd.id_evaluador END AS identificacion_evaluado", false);
		$this->db->from('evaluacion_resultado_detalle rd');
		$this->db->join('evaluacion_solicitud ev','ev.id = rd.id_solicitud');
		$this->db->join('personas p','ev.id_evaluado = p.identificacion');
		$this->db->join('personas pj','ev.jefe_inmediato = pj.identificacion');
		$this->db->join('personas pc','ev.coevaluacion = pc.identificacion');
		$this->db->join('valor_parametro vpa','vpa.id = rd.area_apre');
		$this->db->join('valor_parametro vpt','vpt.id = rd.id_tipo_evaluador');
		$this->db->where("rd.id_solicitud = $id_solicitud");  
        $query = $this->db->get();
		return $query->result_array();
	}

	public function get_resultados_tipoevaluador($id_solicitud){
		$this->db->select("rte.*,vpt.valor tipo_evaluador");
		$this->db->from('evaluacion_resultado_tipo_evaluador rte');
		$this->db->join('valor_parametro vpt','vpt.id = rte.id_tipo_evaluador');
		$this->db->where("rte.id_solicitud = $id_solicitud");  
        $query = $this->db->get();
		return $query->result_array();
	}

	public function listar_detalle_resultados($id_evaluado,$filtro=null, $id_solicitud=''){
		$this->db->select("ec.*, cp.valor competencia, cp.valorz icono, acp.valor area_apreciacion, acp.id_aux, pre.valor pregunta, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
		$this->db->from('evaluacion_resultado_competencia ec');
		$this->db->join('valor_parametro cp','cp.id = ec.id_competencia');
		$this->db->join('valor_parametro acp','acp.id = cp.valorx','left');
		$this->db->join('valor_parametro pre','pre.id = ec.id_pregunta', 'left');
		$this->db->join('evaluacion_solicitud es', 'es.id=ec.id_solicitud and es.estado = 1','left');
		$this->db->join('personas p','ec.id_persona = p.identificacion');
		$this->db->where("ec.id_persona", $id_evaluado);
		$this->db->where("ec.estado = 1 and cp.valory <> 1");
		if($filtro == 1) $this->db->where("ec.estado_formacion", $filtro);
		if(!empty($id_solicitud)) $this->db->where("ec.id_solicitud", $id_solicitud);
		$this->db->order_by("ec.puntaje", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_planFormacion($fecha_i, $fecha_f, $texto, $id_lugar){
		$this->db->select("pf.*, vpl.valor lugar");
		$this->db->from('talentocuc_plan_formacion pf');
		$this->db->join('valor_parametro vpl', 'pf.id_lugar = vpl.id');
		$this->db->where("pf.estado = 1");
		if($id_lugar) $this->db->where("pf.id_lugar", $id_lugar);
		if($texto) $this->db->where("(pf.funcionario LIKE '%" . $texto . "%' OR pf.tema LIKE '%" . $texto . "%')");
		if ($fecha_i && $fecha_f) $this->db->where("(DATE_FORMAT(pf.fecha_formacion,'%Y-%m-%d') >= DATE_FORMAT('$fecha_i','%Y-%m-%d') AND DATE_FORMAT(pf.fecha_formacion,'%Y-%m-%d') <= DATE_FORMAT('$fecha_f','%Y-%m-%d'))");
		$this->db->order_by("pf.fecha_registra", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_plaformacion_personal($id_persona, $id_competencia){
		$this->db->select("pf.*, vpl.valor lugar, vc.valor competencia, af.id id_asistencia", false);
		$this->db->from('talentocuc_formacion_competencia fc');
		$this->db->join('talentocuc_plan_formacion pf', 'pf.id = fc.id_formacion');
		$this->db->join('valor_parametro vc', 'fc.id_competencia = vc.id');
		$this->db->join('valor_parametro vpl', 'pf.id_lugar = vpl.id');
		$this->db->join("talentocuc_asistencia_formacion af", "fc.id = af.id_formacion and af.id_persona = '$id_persona'", "left");
		$this->db->where("fc.id_competencia = $id_competencia and fc.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_tiempo_formacion($id_persona, $id_competencia){
		$this->db->select("af.id_formacion,
		(SELECT sum(epf.duracion) FROM talentocuc_soportes_plan_formacion sop
		INNER JOIN talentocuc_plan_formacion epf ON epf.id = sop.id_plan_formacion
		WHERE sop.id_persona = '$id_persona' and sop.id_competencia = $id_competencia and sop.estado_apro = 1) tiempo");
		$this->db->from('talentocuc_plan_formacion pf');
		$this->db->join('talentocuc_formacion_competencia fc','fc.id_formacion = pf.id');
		$this->db->join('talentocuc_asistencia_formacion af','af.id_formacion = pf.id');
		$this->db->where("af.id_persona = '$id_persona' and fc.id_competencia = $id_competencia");
		$query = $this->db->get();
		return $query->row();
	}

	public function get_tiempo_formacion_asistencia($id_persona, $id_competencia){
		$this->db->select("sum(tpf.duracion) tiempo");
		$this->db->from('talentocuc_asistencia_formacion eaf');
		$this->db->join('talentocuc_plan_formacion tpf','eaf.id_formacion = tpf.id');
		$this->db->join('talentocuc_formacion_competencia efc','tpf.id = efc.id_formacion');
		$this->db->where("eaf.id_persona = '$id_persona' AND efc.id_competencia = '$id_competencia'");
		$query = $this->db->get();
		return $query->row();
	}

	public function get_tiempo_formacion_soporte($id_persona, $id_competencia){
		$this->db->select("sum(sop.horas_formacion) tiempo");
		$this->db->from('talentocuc_soportes_plan_formacion sop');
		$this->db->where("sop.id_persona = '$id_persona' and sop.id_competencia = $id_competencia and sop.estado_apro = 1");
		$query = $this->db->get();
		return $query->row();
	}

	public function get_formacion($id_formacion){
		$persona = $_SESSION['persona'];
		$fecha_actual = date('Y-m-d H:i:s');
		$this->db->select("pf.*, (SELECT CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) FROM personas p WHERE p.id = $persona) nombre_completo",false);
		$this->db->from('talentocuc_plan_formacion pf');
		$this->db->where("pf.id = $id_formacion and pf.id NOT IN (SELECT af.id_formacion FROM talentocuc_asistencia_formacion af
		INNER JOIN personas pa ON pa.identificacion = af.id_persona WHERE pa.id = $persona) and pf.estado_encuesta = 1 and pf.fecha_cierre_link > '$fecha_actual'");
		$query = $this->db->get();
		return $query->row();
	}

	public function get_competencia($id_formacion){
		$this->db->select("vp.id id_valor_parametro, vp.valor competencia, vpp.valor pregunta,efc.id id_permiso", false);
		$this->db->from('valor_parametro vp');
		$this->db->join('permisos_parametros ppc', 'ppc.vp_principal_id = vp.id');
		$this->db->join('valor_parametro vpp', 'ppc.vp_secundario_id = vpp.id');
		$this->db->join("talentocuc_formacion_competencia efc", "efc.id_competencia = vp.id and efc.id_formacion = $id_formacion and efc.estado = 1", "left");
		$this->db->where("vp.idparametro = 217 and vp.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function listar_plan_entrenamiento($id_persona, $id = null, $enviado = null){
		$this->db->select("pe.*, vpl.valor lugar, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) facilitador, p.identificacion id_facilitador, vpo.valor oferta", false);
		$this->db->from('talentocuc_plan_entrenamiento pe');
		$this->db->join('valor_parametro vpl', 'pe.id_lugar = vpl.id');
		$this->db->join('valor_parametro vpo', 'pe.id_oferta = vpo.id');
		$this->db->join('personas p', 'p.identificacion = pe.id_funcionario');
		$this->db->where("pe.estado = 1 and pe.id_evaluado = '$id_persona'");
		if($id) $this->db->where('pe.id',$id);
		if($enviado) $this->db->where('pe.enviado',$enviado);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function entrenamiento_finalizado($id_persona){
		$this->db->select("COUNT(pe.id) aprobados");
		$this->db->from('talentocuc_plan_entrenamiento pe');
		$this->db->where("pe.enviado = 1 and pe.aprobacion = 1 and pe.estado = 1");
		$this->db->where('pe.id_evaluado', $id_persona);
		$info = $this->db->get()->row()->aprobados;
		return $info;
	}

	public function listar_valor_parametro($id_parametro, $selec){
		$this->db->select($selec);
		$this->db->from('valor_parametro vp');
		if($id_parametro == 243){
			$this->db->join('valor_parametro tr', 'vp.valorz = tr.id');
			$this->db->join('valor_parametro tp', 'vp.valora = tp.id');
		}
		$this->db->where("vp.estado = 1 and vp.idparametro = $id_parametro");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_info_persona($id_persona = null, $identificacion = null){
		$this->db->select("p.*, p.identificacion id_persona, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, vp.valor cargo",false);
		$this->db->from('personas p');
		$this->db->join('valor_parametro vp', 'p.id_cargo_sap = vp.id', 'left');
		if($id_persona) $this->db->where('p.id', $id_persona);
		if($identificacion) $this->db->where("p.identificacion = '$identificacion'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_formacion_academica($id_persona){
		$this->db->select("fap.*, vp.valor formacion");
		$this->db->from('formacion_academica_personas fap');
		$this->db->join('valor_parametro vp', 'fap.id_tipo_formacion = vp.id');
		$this->db->where("fap.id_persona = $id_persona and fap.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_soportes_formacion_academica($id_formacion){
		$this->db->select("fas.*, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo", false);
		$this->db->from('formacion_academica_soportes fas');
		$this->db->join('personas p', 'fas.usuario_registra = p.id', 'left');
		$this->db->where("fas.id_formacion = $id_formacion and fas.estado = 1");
		$this->db->order_by("fas.fecha_registra DESC");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_otros_soporte($id_persona){
		$this->db->select("ps.*, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo", false);
		$this->db->from('personas_soportes ps');
		$this->db->join('personas p', 'ps.id_persona = p.id', 'left');
		$this->db->where("ps.id_persona = $id_persona and ps.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_soportes_plan_formacion($id_persona, $id_competencia){
		$this->db->select("spf.*");
		$this->db->from('talentocuc_soportes_plan_formacion spf');
		$this->db->where("spf.id_persona = $id_persona and spf.id_competencia = $id_competencia and spf.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_avalar_soportes($id_persona,$admin){
		$persona = $_SESSION['persona'];
		$this->db->select("spf.*, vp.valor competencia, spf.nombre_archivo");
		$this->db->from('talentocuc_soportes_plan_formacion spf');
		$this->db->join('valor_parametro vp', 'vp.id = spf.id_competencia');
		if(!$admin) $this->db->join('talentocuc_actividad_persona ap',"ap.actividad_id = spf.id_competencia and ap.persona_id = $persona and ap.estado = 1");
		$this->db->where("spf.id_persona = $id_persona and spf.estado = 1 and spf.estado_apro = 0");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_observacion_perfil_persona($id_persona){
		$this->db->select("op.id, op.observacion");
		$this->db->from('observacion_perfil_persona op');
		$this->db->where("op.id_persona = $id_persona and op.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_soportes_Avalar(){
		$persona = $_SESSION['persona'];
		$this->db->select("count(sop.id) cantidad, p.id id_persona, p.identificacion, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo", false);
		$this->db->from('talentocuc_soportes_plan_formacion sop');
		$this->db->join('personas p','sop.id_persona = p.identificacion');
		$this->db->join('talentocuc_actividad_persona ap',"ap.actividad_id = sop.id_competencia and ap.persona_id = $persona and ap.estado = 1");
		$this->db->where("sop.estado = 1 and sop.estado_apro = 0");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function info_personas_notificar($id_competencia){
		$this->db->select("p.correo, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona", false);
		$this->db->from('personas p');
		$this->db->join('talentocuc_actividad_persona ap','ap.persona_id = p.id');
		$this->db->where('ap.actividad_id', $id_competencia);
		$this->db->where("ap.notificacion = 1 and ap.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_competencias_req($id_persona,$filtro=null){
		$this->db->select("ct.*, vp.valory puntaje, cp.valor competencia, p.identificacion");
		$this->db->from('competencias_talento_cuc ct');
		$this->db->join('valor_parametro cp','cp.id = ct.id_competencia');
		$this->db->join('valor_parametro vp','vp.id = ct.nivel');
		$this->db->join('personas p','ct.id_persona = p.id');
		$this->db->where("ct.id_persona = $id_persona and ct.estado = 1");
		if($filtro) $this->db->where("ct.estado_formacion = $filtro");
		else  $this->db->where("ct.estado_formacion = 0");
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function get_competencias_formacion($id_formacion){
		$this->db->select("vp.valor competencia, vpp.valor pregunta");
		$this->db->from('valor_parametro vp');
		$this->db->join('talentocuc_formacion_competencia fc', 'fc.id_competencia = vp.id');
		$this->db->join('permisos_parametros ppc', 'ppc.vp_principal_id = vp.id');
		$this->db->join('valor_parametro vpp', 'ppc.vp_secundario_id = vpp.id');
		$this->db->where("vp.idparametro = 217 and fc.id_formacion = $id_formacion");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_actividades($persona){
		$this->db->select("vp.id as id, vp.valor as nombre, ap.id as asignado, ap.notificacion");
		$this->db->from('valor_parametro vp');
		$this->db->join('talentocuc_actividad_persona ap', "vp.id = ap.actividad_id AND ap.persona_id = $persona", 'left');
		$this->db->where("vp.idparametro = 217");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function validar_asignacion_actividad($id, $persona){
		$this->db->select("IF(COUNT(id) > 0, 0, 1) asignado", false);
		$this->db->from('talentocuc_actividad_persona');
		$this->db->where('actividad_id', $id);
		$this->db->where('persona_id', $persona);
		$query = $this->db->get();
		return $query->row()->asignado;
	}

	public function validar_notificacion_actividad($id){
		$this->db->select("notificacion");
		$this->db->from('talentocuc_actividad_persona');
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row()->notificacion;
	}

	public function quitar_actividad($id){
		$this->db->where('id', $id);
		$this->db->delete('talentocuc_actividad_persona');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}
	
	public function obtener_entrenamiento($id_persona, $id_entrenamiento=null, $tipo=null){
		$this->db->select("pe.*, DATE_FORMAT(pe.fecha_entrenamiento, '%d-%m-%Y') fecha, DATE_FORMAT(pe.fecha_entrenamiento, '%H:%I:%S') hora, vpl.valory tipo_mod, vpl.valor lugar,
		 CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) facilitador, p.identificacion id_facilitador, p.correo, p.id facilitador_id, vpo.valor oferta, CONCAT(e.nombre,' ',e.apellido, ' ',e.segundo_apellido) nombre_persona, e.correo correo_persona, e.id persona_id", false);
		$this->db->from('talentocuc_plan_entrenamiento pe');
		$this->db->join('valor_parametro vpl', 'pe.id_lugar = vpl.id');
		$this->db->join('valor_parametro vpo', 'pe.id_oferta = vpo.id');
		$this->db->join('personas p', 'p.identificacion = pe.id_funcionario');
		$this->db->join('personas e', 'e.identificacion = pe.id_evaluado');
		$this->db->where("pe.enviado = 1 and pe.estado = 1");
		if($id_persona) $this->db->where('pe.id_evaluado', $id_persona);
		if($id_entrenamiento) $this->db->where('pe.id', $id_entrenamiento);
		if($tipo) $this->db->where('pe.asistencia', $tipo);
		$query = $this->db->get();
		if($id_entrenamiento) $resp = $query->row();
		else $resp = $query->result_array();
		return $resp;
	}

	public function cantidad_asistencias_entrenamiento($id_persona){
		$this->db->select("COUNT(pe.id) aprobadas");
		$this->db->from('talentocuc_plan_entrenamiento pe');
		$this->db->where("pe.id_funcionario = '$id_persona' and pe.asistencia = 1 and pe.aprobacion = 1");
		$this->db->where("pe.estado = 1");
		$query = $this->db->get();
		$aprobados = $query->row()->aprobadas;
		$sql = $this->listar_asistencias_entrenamiento($id_persona);
		$cantidad = count($sql);
		return ['cantidad' => $cantidad, 'aprobados' => (int)$aprobados];
	}

	public function get_encuesta_enviada($id_persona){
		$this->db->select("COUNT(pe.id) enviadas");
		$this->db->from('talentocuc_plan_entrenamiento pe');
		$this->db->where("pe.id_evaluado = '$id_persona' and pe.encuesta_enviada = 1");
		$this->db->where("pe.estado = 1");
		$query = $this->db->get();
		return $query->row()->enviadas;
	}

	public function listar_asistencias_entrenamiento($id_persona){
		$this->db->select("pe.*, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, p.identificacion, vp.valor oferta, pe.fecha_entrenamiento as fecha", false);
		$this->db->from('talentocuc_plan_entrenamiento pe');
		$this->db->join('personas p', 'p.identificacion = pe.id_evaluado');
		$this->db->join('valor_parametro vp', 'pe.id_oferta = vp.id');
		$this->db->where("pe.id_funcionario = '$id_persona' and pe.asistencia = 1");
		$this->db->where("pe.estado = 1");
		$this->db->order_by('pe.fecha_entrenamiento', 'DESC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_personas_notificar_th($idparametro){
		$this->db->select("vp.valor as correos");
		$this->db->from('valor_parametro vp');
		$this->db->where("vp.idparametro = $idparametro and vp.estado = 1");
		$query = $this->db->get()->row()->correos;
		return $query;
	}

	public function listar_ofertas_entrenamiento(){
		$this->db->select("vp.*, vp.valor tema, da.valor vice, dpto.valor departamento, area.valor area");
		$this->db->from('valor_parametro vp');
		$this->db->join('valor_parametro da', 'da.id = vp.valorx');
		$this->db->join('valor_parametro dpto', 'dpto.id = vp.valory');
		$this->db->join('valor_parametro area', 'area.id = vp.valorz');
		$this->db->where("vp.idparametro = 244 and vp.estado = 1");
		$query = $this->db->get()->result_array();
		return $query;
	}

	public function buscar_dependencia($buscar, $id_departamento){
		$this->db->select("id, valor nombre", false);
		$this->db->from('valor_parametro');
		$this->db->where("idparametro", $id_departamento);
		$this->db->like("valor", $buscar);
		$this->db->order_by("valor", 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function buscar_oferta($where){
		$this->db->select("vo.id, vo.valor nombre, vp.valor vicerrectoria, vd.valor departamento, va.valor area");
		$this->db->from('valor_parametro vo');
		$this->db->join('valor_parametro vp', 'vp.id = vo.valorx');
		$this->db->join('valor_parametro vd', 'vd.id = vo.valory');
		$this->db->join('valor_parametro va', 'va.id = vo.valorz');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function estado_entrenamiento($id_persona, $cargo_id = null){
		$this->db->select("te.*, p.id id_persona, p.identificacion, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, CONCAT(je.nombre,' ',je.apellido, ' ',je.segundo_apellido) nombre_jefe, je.correo correo_jefe, je.id id_jefe",false);
		$this->db->from('talentocuc_aceptacion_cargo te');
		$this->db->join('personas p', 'p.identificacion = te.id_evaluado');
		$this->db->join('personas je', 'je.id = te.jefe_inmediato','left');
		$this->db->where('p.id', $id_persona);
		$this->db->where("te.estado = 1");
		if($cargo_id) $this->db->where('te.id_cargo_sap', $cargo_id);
		$this->db->order_by('te.fecha_registra', 'DESC');
		$this->db->limit(1);
		$query = $this->db->get()->row();
		return $query;
	}

	public function listar_actas_personas($id_jefe){
		$this->db->select("te.*, p.codigo_cargo, p.identificacion, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, p.id persona_id",false);
		$this->db->from('talentocuc_aceptacion_cargo te');
		$this->db->join('personas p', 'p.identificacion = te.id_evaluado');
		$this->db->where('te.jefe_inmediato', $id_jefe);
		$this->db->where("te.estado = 1");
		$this->db->order_by('te.fecha_recibido', 'DESC');
		$query = $this->db->get()->result_array();

		$this->db->select("COUNT(en.id) terminados");
		$this->db->from('talentocuc_aceptacion_cargo en');
		$this->db->where("en.jefe_inmediato =  $id_jefe and en.estado = 1 and en.terminado = 1");
		$terminados = $this->db->get()->row()->terminados;

		$this->db->select("COUNT(en.id) pendientes");
		$this->db->from('talentocuc_aceptacion_cargo en');
		$this->db->where("en.jefe_inmediato =  $id_jefe and en.estado = 1 and en.terminado = 0 and en.solicitar_firma_jefe = 1 and en.fecha_recibido IS NOT NULL");
		$pendientes = $this->db->get()->row()->pendientes;
		return [$query, $terminados, $pendientes];
	}

	public function get_info_seleccion($id_persona){
		$this->db->select("s.cargo_id, vpc.valor cargo, p.id id_jefe, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_jefe",false);
		$this->db->from('seleccion s');
		$this->db->join('solicitudes_talento_hum sth', 'sth.id = s.solicitud_id');
		$this->db->join('candidatos_seleccion cs', 'cs.solicitud_id = sth.id');
		$this->db->join('personas p', 'p.id = sth.jefe_inmediato');
		$this->db->join('valor_parametro vpc', 'vpc.id = s.cargo_id');
		$this->db->where('cs.candidato_id', $id_persona);
		$this->db->where("cs.contratado = 1 and cs.estado = 1 and sth.estado = 1");
		$this->db->order_by("sth.id", "desc");
		$this->db->limit(1);
		$query = $this->db->get()->row();
		return $query;
	}

	public function obtener_preguntas_indicador($id_evaluado, $periodo=''){
		$this->db->select("pre.id id_pregunta, pre.evaluado, pre.pregunta, pre.periodo, pre.id_respuesta respuesta, pre.meta, vpr.valorx puntaje, vpr.valor descripcion, pre.id_tipo_meta, vpm.valor tipo_meta, pre.cumplimiento, pre.resultado");
		$this->db->from('talentocuc_indicadores pre');
		$this->db->join('valor_parametro vpr', 'pre.id_respuesta = vpr.id', 'left');
		$this->db->join('valor_parametro vpm', 'pre.id_tipo_meta = vpm.id');
		$this->db->where("pre.evaluado = $id_evaluado and pre.estado=1");
		if($periodo) $this->db->where("pre.periodo", $periodo);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_preguntas($persona_id, $tabla, $periodo=''){
		$this->db->select("pre.*, vp.valor");
		$this->db->from("$tabla pre");
		$this->db->join('valor_parametro vp', 'vp.id = pre.respuesta', 'left');
		$this->db->where("pre.evaluado = $persona_id and pre.estado=1");
		if($periodo) $this->db->where("periodo", $periodo);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_asistencias_formacion($filtro, $fecha_i='', $fecha_f='', $texto='', $id_lugar=''){
		$fecha = date('Y-m-d');
		$this->db->select("CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, af.id_formacion, pf.tema, pf.funcionario, DATE_FORMAT(af.fecha_registra, '%Y-%m-%d') fecha", false);
		$this->db->from("talentocuc_asistencia_formacion af");
		$this->db->join('personas p', 'p.identificacion = af.id_persona');
		$this->db->join('talentocuc_plan_formacion pf', 'pf.id = af.id_formacion');
		if($filtro) $this->db->where("DATE_FORMAT(af.fecha_registra, '%Y-%m-%d') = '$fecha'");
		if($id_lugar) $this->db->where("pf.id_lugar", $id_lugar);
		if($texto) $this->db->where("(pf.funcionario LIKE '%" . $texto . "%' OR pf.tema LIKE '%" . $texto . "%')");
		if ($fecha_i && $fecha_f) $this->db->where("(DATE_FORMAT(af.fecha_registra,'%Y-%m-%d') >= DATE_FORMAT('$fecha_i','%Y-%m-%d') AND DATE_FORMAT(af.fecha_registra,'%Y-%m-%d') <= DATE_FORMAT('$fecha_f','%Y-%m-%d'))");
		$this->db->where("af.estado = 1");
		$this->db->order_by('af.fecha_registra', 'DESC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function buscar_cargo($dato){
		$this->db->select("vp.*");
		$this->db->from('valor_parametro vp');
		$this->db->where("vp.idparametro = 188 and vp.valor LIKE '%" . $dato . "%'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_tipo_respuesta($id_aux){
		$this->db->select("vp.id, vp.id_aux, vp.valor, vp.valorx, vp.valorz");
        $this->db->from("valor_parametro vp");
		$this->db->join("permisos_parametros pp", "pp.vp_secundario_id = vp.id AND pp.vp_principal ='$id_aux'");
        $this->db->where('vp.estado', 1);
        $this->db->where('vp.idparametro', 220);
		$query = $this->db->get()->result_array();
		return $query;
	}
	
	public function listar_actas_cargo($id_persona){
		$this->db->select("te.id_evaluado, te.fecha_entrega, te.fecha_recibido, vp.valor cargo, te.codigo_cargo");
        $this->db->from("talentocuc_aceptacion_cargo te");
		$this->db->join("valor_parametro vp", "te.id_cargo_sap = vp.id", "left");
        $this->db->where('te.id_evaluado', $id_persona);
		$query = $this->db->get()->result_array();
		return $query;
	}

	public function modificar_datos_mult($data, $tabla, $where){
		$this->db->where($where);
		$this->db->update($tabla, $data);
		$error = $this->db->_error_message(); 
		return $error ? "error" : 0;
	}

	public function get_resultado_indicadores($id_persona, $periodo=''){
		$this->db->select("ap.*");
		$this->db->from('evaluacion_asignacion_preguntas ap');
		$this->db->where("ap.evaluado = '$id_persona' and ap.estado = 1");
		if($periodo) $this->db->where("ap.periodo = '$periodo'");
		$query = $this->db->get()->row();
		return $query;
	}

	public function obtener_permisos_parametro($id, $id_aux = ''){
		$this->db->select('pp.vp_secundario_id id, vp.valor, vp.valorx, vp.valory');
		$this->db->from('permisos_parametros pp');
		$this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
		if($id_aux) $this->db->where("pp.vp_principal = '$id_aux'");
		else $this->db->where("pp.vp_principal_id = '$id'");
		// $this->db->order_by("pp.id", "ASC");
		$this->db->order_by("vp.valorx", "DESC");
		$query = $this->db->get();
		return $query->result_array();
	   }

	public function get_periodos_evaluados($id_evaluado){
		$this->db->select('es.id, es.periodo');
		$this->db->from('evaluacion_solicitud es');
		$this->db->where("es.estado = 1 and es.id_evaluado = '$id_evaluado'");
		$query = $this->db->get();
		return $query->result_array();
	}

}