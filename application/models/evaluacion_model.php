<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class evaluacion_model extends CI_Model
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

	public function listar_valorparametro($idparametro){
		$this->db->select("vp.*,vpp.valor as tipo_respuesta");
    	$this->db->from('valor_parametro vp');
		$this->db->where('vp.idparametro', $idparametro);
		$this->db->join('valor_parametro vpp', 'vp.valorz = vpp.id', 'left');
		$this->db->where("vp.estado",1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function valor_parametro($idparametro, $valor=null, $id=null, $periodo=null, $val=null) {
		$this->db->select("vp.*");
		$this->db->from('valor_parametro vp');
		if($valor) $this->db->where('vp.valor',$valor);
		if($id) $this->db->where('vp.id',$id);
		if($periodo && $val) $this->db->where("vp.$val = '$periodo'");
		elseif(!$periodo) $this->db->where("vp.valorz = 1");
		$this->db->where("vp.estado = 1 AND vp.idparametro = $idparametro");
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function listar_valor_parametro($idparametro, $periodo=null, $val=null) {
		$this->db->select("vp.*");
		$this->db->from('valor_parametro vp');
		if($periodo && $val) $this->db->where("vp.$val = '$periodo'");
		$this->db->where("vp.estado = 1 AND vp.idparametro = $idparametro");
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

	public function obtener_peso_porcentual($id_aux) {
		$this->db->select("SUM(ep.porcentaje) as peso");
		$this->db->from('evaluacion_permisos ep');
		$this->db->join('permisos_parametros pp', 'ep.id_permiso_parametro = pp.id');
		$this->db->where("pp.vp_principal", $id_aux);
		$this->db->where("ep.estado", 1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function traer_valores_permisos($idparametro,  $idvalorparametro){
        $this->db->select("vp.id,vp.id_aux,vp.valor,vp.valorx,pp.id id_permiso, ep.porcentaje", false);
        $this->db->from("valor_parametro vp");
		$this->db->join("permisos_parametros pp", "pp.vp_secundario_id = vp.id AND pp.vp_principal_id =".$idvalorparametro, "left");
		$this->db->join('evaluacion_permisos ep', 'ep.id_permiso_parametro = pp.id', 'left');
        $this->db->where('vp.estado', 1);
        $this->db->where('vp.idparametro', $idparametro, $idvalorparametro);
        $query = $this->db->get();
        return $query->result_array();           
	}

	public function obtener_permisos_parametro($id, $id_aux = ''){
     $this->db->select('pp.vp_secundario_id id, vp.valor, vp.valorx, vp.valory');
     $this->db->from('permisos_parametros pp');
     $this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
	 if(!empty($id_aux)) $this->db->where("pp.vp_principal = '$id_aux'");
	 if($id) $this->db->where("pp.vp_principal_id = '$id'");
	//  $this->db->order_by("pp.id", "ASC");
	 $this->db->order_by("vp.valorx", "DESC");
     $query = $this->db->get();
     return $query->result_array();
	}

	public function obtener_tipo_evaluador($id_solicitud){
        $this->db->select("vp.id,vp.valor,vp.id_aux,vp.valorx,vp.valory, ep.porcentaje");
        $this->db->from("valor_parametro vp");
		$this->db->join("permisos_parametros pp", "pp.vp_secundario_id = vp.id");
		$this->db->join("evaluacion_permisos ep", "pp.id = ep.id_permiso_parametro");
		$this->db->join("evaluacion_solicitud es", "es.id_metodo_eval = pp.vp_principal");
		$this->db->where("vp.estado = 1 and es.id = $id_solicitud and vp.idparametro=215");
		$this->db->order_by("vp.valorz", "ASC");
        $query = $this->db->get();
        return $query->result_array();           
	}
	

	  public function listar_solicitudes($id, $estado, $tipo, $fecha_i, $fecha_f, $periodo, $resultado=''){
		if($id == 0) $id = '';
		if($estado === 'vacio') $estado = '';
		if($fecha_i == 0) $fecha_i = '';
		if($fecha_f == 0) $fecha_f = '';
		$persona = $_SESSION['persona'];
		$perfil = $_SESSION['perfil'];
		$this->db->select("es.*, pf.id as idpersona_evaluado, CONCAT(pf.nombre,' ',pf.apellido, ' ',pf.segundo_apellido) evaluado, pf.identificacion as cc_evaluado, pf.correo, vpe.valor as state, t.valor as tipo, cr.valor as cargo,CONCAT(pji.nombre,' ',pji.apellido, ' ',pji.segundo_apellido) nombre_jefe,CONCAT(pc.nombre,' ',pc.apellido, ' ',pc.segundo_apellido) nombre_coevaluado, pji.id id_jefe_inmediato,pc.id id_coevaluado,erf.puntuacion_directa puntuacion", false);
		$this->db->from('evaluacion_solicitud es');
		$this->db->join('personas pf', 'pf.identificacion = es.id_evaluado');
		$this->db->join('personas pji', 'pji.identificacion = es.jefe_inmediato');
		$this->db->join('personas pc', 'pc.identificacion = es.coevaluacion');
		// $this->db->join('cargos_departamentos cd', 'pf.id_cargo = cd.id','left');
		// $this->db->join('valor_parametro cr', 'cd.id_cargo = cr.id','left');
		$this->db->join('valor_parametro cr', 'cr.id = pf.id_cargo_sap','left');
		$this->db->join('valor_parametro vpe', 'es.id_estado_eval = vpe.id_aux');
		$this->db->join('valor_parametro t', 'es.id_metodo_eval = t.id_aux');
		$this->db->join('evaluacion_resultado_final erf', 'es.id = erf.id_solicitud','left');
		if (!empty($periodo)) $this->db->where("es.periodo = '$periodo'");
		if (!empty($resultado)) $this->db->where("es.resultado = '$resultado'");
		if (!empty($tipo)) $this->db->where("es.id_metodo_eval = '$tipo'");
		if (!empty($id)) $this->db->where('es.id',$id);
		if (!empty($estado)){
			if($estado === 'Eval_Act_Fin') $this->db->where("(es.id_estado_eval = '$estado' OR es.recibido = 1)");
			else $this->db->where('es.id_estado_eval',$estado);
		}
		if ($fecha_i && $fecha_f) $this->db->where("(DATE_FORMAT(es.fecha_registra,'%Y-%m-%d') >= DATE_FORMAT('$fecha_i','%Y-%m-%d') AND DATE_FORMAT(es.fecha_registra,'%Y-%m-%d') <= DATE_FORMAT('$fecha_f','%Y-%m-%d'))");
		if ($perfil != 'Per_Admin' && $perfil != 'Per_Adm_Eval' && $perfil != 'Per_Admin_Tal') $this->db->where("pf.id = $persona and (es.id_estado_eval = 'Eval_Env' or es.id_estado_eval = 'Eval_Pro')");
		$this->db->where("es.estado",1);
		$this->db->order_by("es.fecha_registra", "DESC");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_solicitud($id,$persona='',$filtro='', $periodo =''){
		$this->db->select("es.*, t.id id_metodo, t.valor tipo, p.correo, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, cr.valor departamento, CONCAT(pj.nombre,' ',pj.apellido, ' ',pj.segundo_apellido) nombre_jefe, crj.valor departamento_jefe, pj.id id_jefe_inmediato, CONCAT(pc.nombre,' ',pc.apellido, ' ',pc.segundo_apellido) nombre_coevaluado,pc.id id_coevaluado, vpe.valor state", false);
		$this->db->from('evaluacion_solicitud es');
		$this->db->join('valor_parametro t', 'es.id_metodo_eval = t.id_aux');
		$this->db->join('personas p', 'p.identificacion = es.id_evaluado');
		$this->db->join('valor_parametro cr', 'p.id_cargo_sap = cr.id','left');
		$this->db->join('personas pj', 'pj.identificacion = es.jefe_inmediato');
		$this->db->join('valor_parametro crj', 'pj.id_cargo_sap = crj.id','left');
		$this->db->join('personas pc', 'pc.identificacion = es.coevaluacion');
		$this->db->join('valor_parametro vpe', 'es.id_estado_eval = vpe.id_aux');
		if (!empty($persona)) $this->db->where("p.id = $persona");
		if ($id) $this->db->where('es.id', $id);
		if (!empty($periodo)) $this->db->where('es.periodo', $periodo);
		if (!empty($filtro)) $this->db->where("(es.id_estado_eval = 'Eval_Env' OR es.id_estado_eval = 'Eval_Pro' OR es.id_estado_eval = 'Eval_Act_Env' OR es.id_estado_eval = 'Eval_Act_Fin' OR es.id_estado_eval = 'Eval_Act_Pro')");
		$query = $this->db->get();
		return $query->row();
	}

	public function get_solicitudes_persona($persona){
		$this->db->select("es.*, me.valor metodo");
		$this->db->from('evaluacion_solicitud es');		
		$this->db->join('personas p', 'p.identificacion = es.id_evaluado');
		$this->db->join('valor_parametro me', 'es.id_metodo_eval = me.id_aux');
		$this->db->where("p.id = $persona and es.estado = 1 and es.id_estado_eval <> 'Eval_Can'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_preguntas($tipo_evaluador, $id_aux='', $evaluado='', $id_aux_evaluado=''){
		$this->db->select('vp.id as id_pregunta, vp.valor as pregunta, vp.valorz as id_tipo_pregunta, vpc.id as id_competencia,vpc.valor as competencia, vp.valorb as respuesta, ppa.vp_principal_id as id_area_competencia,vpa.valor as area, ppte.vp_principal_id as id_tipo_evaluador');
		$this->db->from('valor_parametro vp');
		$this->db->join('permisos_parametros ppc', 'ppc.vp_secundario_id = vp.id');
		$this->db->join('permisos_parametros ppa', 'ppc.vp_principal_id = ppa.vp_secundario_id');
		$this->db->join('permisos_parametros ppte', 'ppa.vp_principal_id = ppte.vp_secundario_id');
		$this->db->join('valor_parametro vpc', 'ppc.vp_principal_id = vpc.id');
		$this->db->join('valor_parametro vpa', 'ppa.vp_principal_id = vpa.id');
		$this->db->where("ppte.vp_principal_id = '$tipo_evaluador'");
		$query = $this->db->get();
		return $query->result_array();
	   }
	
	public function obtener_indicadores($id_solicitud,$estado='',$filtro='',$ind=''){
		$this->db->select("CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_evaluado, p.identificacion evaluado, eap.periodo periodo_evaluado, eap.id_estado, eap.id id_asignacion_persona, eap.evaluacion, p.id id_persona",false);
		$this->db->from('personas p');
		$this->db->join('evaluacion_asignacion_persona eap', 'p.identificacion = eap.evaluado', 'left');
		if($estado != '') $this->db->where("eap.evaluacion", $estado);
		if($filtro != ''){
			if($filtro != 'vacio') $this->db->where("eap.id_estado", $filtro);
			else $this->db->where("eap.id_estado", null);
		}
		$this->db->join('evaluacion_solicitud es', 'eap.evaluador = es.id_evaluado');
		$this->db->where("es.id", $id_solicitud);
		$this->db->where("eap.periodo = es.periodo and eap.estado = 1");
		if($ind != ''){
			 $this->db->where("((SELECT COUNT(pre.id) FROM talentocuc_indicadores pre WHERE pre.evaluado = eap.evaluado AND pre.estado=1 AND pre.periodo = es.periodo) > 0 OR 
			 (SELECT COUNT(fun.id) FROM talentocuc_funciones fun WHERE fun.evaluado = eap.evaluado AND fun.estado=1 AND fun.periodo = es.periodo) > 0)");
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_personal_sinindicadores($id_solicitud){
		$this->db->select("eap.id id_asignacion");
		$this->db->from('evaluacion_asignacion_persona eap');
		$this->db->join('evaluacion_solicitud es', 'eap.evaluador = es.id_evaluado');
		$this->db->where("es.id = $id_solicitud and eap.periodo = es.periodo and eap.estado = 1 and es.parte2 = 0 and es.parte1 = 1");
		// $this->db->where("eap.evaluado NOT IN (SELECT pre.evaluado FROM talentocuc_indicadores pre WHERE pre.estado=1)");
		$this->db->where("(SELECT COUNT(pre.id) FROM talentocuc_indicadores pre WHERE pre.evaluado = eap.evaluado AND pre.estado=1 AND pre.periodo = es.periodo) = 0 and 
		(SELECT COUNT(fun.id) FROM talentocuc_funciones fun WHERE fun.evaluado = eap.evaluado AND fun.estado=1 AND fun.periodo = es.periodo) = 0");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_preguntas_indicador($id_evaluado, $periodo=null){
		$this->db->select("pre.id id_pregunta, pre.evaluado, pre.pregunta, pre.periodo, pre.id_tipo_respuesta, pre.id_respuesta respuesta, pre.meta, vpr.valorx puntaje, vpr.valor descripcion, pre.id_tipo_meta, tm.valor tipo_meta, pre.cumplimiento",false);
		$this->db->from('talentocuc_indicadores pre');
		$this->db->join('valor_parametro vpr', 'pre.id_respuesta = vpr.id', 'left');
		$this->db->join('valor_parametro tm', 'pre.id_tipo_meta = tm.id');
		$this->db->where("pre.evaluado = '$id_evaluado' and pre.estado=1");
		if($periodo) $this->db->where('pre.periodo', $periodo);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function validar_funcionario_evalucion($id_evaluado,$periodo){
		$this->db->select("es.*");
		$this->db->from('evaluacion_solicitud es');
		$this->db->join('personas p', 'es.id_evaluado = p.identificacion');
		$this->db->where('es.id_evaluado', $id_evaluado);
		$this->db->where('es.periodo', $periodo);
		$this->db->where("(es.id_estado_eval = 'Eval_Sol' or es.id_estado_eval = 'Eval_Env' or es.id_estado_eval = 'Eval_Pro')");
		$query = $this->db->get();
		return $query->row();
	}

	public function listar_personal_cargo($id_solicitud){
		$this->db->select("CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, p.identificacion, eap.id id_asignacion_persona, es.periodo",false);
		$this->db->from('personas p');
		$this->db->join('evaluacion_asignacion_persona eap', 'p.identificacion = eap.evaluado', 'left');
		$this->db->join('evaluacion_solicitud es', 'eap.evaluador = es.id_evaluado');
		// $this->db->join('evaluacion_solicitud esp', 'p.identificacion = esp.id_evaluado and eap.periodo = esp.periodo');
		$this->db->where("es.id", $id_solicitud);
		$this->db->where("eap.periodo = es.periodo and eap.estado=1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_evaluacion_respuestas($id_solicitud, $tipoevaluador=null, $id_evaluado=null){
		$this->db->select("er.id, vp.valor pregunta, vpr.valor respuesta, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_evaluado, vpa.valor area, vpc.valor competencia, eap.evaluado, vpte.valor tipo_evaluador, vpte.id_aux id_aux_evaluador, er.id_asignacion_persona, vpap.valor area_apreciacion", false);
		$this->db->from('evaluacion_respuesta er');
		$this->db->join('valor_parametro vpte', 'er.id_tipo_evaluador = vpte.id','left');
		$this->db->join('valor_parametro vp', 'er.id_pregunta = vp.id','left');
		$this->db->join('valor_parametro vpr', 'er.id_respuesta = vpr.id','left');
		$this->db->join('valor_parametro vpa', 'er.id_area_competencia = vpa.id','left');
		$this->db->join('valor_parametro vpc', 'er.id_competencia = vpc.id','left');
		$this->db->join('valor_parametro vpap', 'vpc.valorx = vpap.id','left');
		$this->db->join('evaluacion_asignacion_persona eap', 'eap.id = er.id_asignacion_persona and eap.estado = 1', 'left');
		$this->db->join('personas p', 'p.identificacion = eap.evaluado','left');
		if($tipoevaluador) $this->db->where("vpte.valory = '$tipoevaluador'");
		$this->db->where("er.id_solicitud = $id_solicitud");
		if($id_evaluado) $this->db->where("eap.evaluado = '$id_evaluado'");
		$this->db->group_by('er.id_pregunta');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_respuestas_indicadores($id_solicitud, $id_persona='', $periodo=''){
		$this->db->select("pre.pregunta, vpr.valor respuesta, vpr.valorx puntaje, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_evaluado, pre.evaluado, pre.cumplimiento", false);
		$this->db->from('talentocuc_indicadores pre');
		$this->db->join('personas p', 'p.identificacion = pre.evaluado');
		$this->db->join('valor_parametro vpr', 'pre.id_respuesta = vpr.id', 'left');
		$this->db->join('evaluacion_asignacion_persona ap', 'pre.evaluado = ap.evaluado and pre.periodo = ap.periodo');
		$this->db->join('evaluacion_solicitud es', 'ap.evaluador = es.id_evaluado');
		$this->db->where("es.id = $id_solicitud and pre.estado=1");
		if($id_persona) $this->db->where("pre.evaluado = '$id_persona'");
		else $this->db->order_by("pre.evaluado ASC");
		if(!empty($periodo)) $this->db->where("pre.periodo = '$periodo'");
		$this->db->group_by('pre.id');
		$query = $this->db->get();
		return $query->result_array();
	}

	// public function get_correos_evaluaciones($id_estado, $fecha_inicio='', $fecha_fin=''){
	// 	$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona, p.correo, es.id id_solicitud", false);
	// 	$this->db->from('evaluacion_solicitud es');
	// 	$this->db->join('personas p', 'p.identificacion = es.id_evaluado');
	// 	$this->db->where('es.id_estado_eval',$id_estado);
	// 	if($fecha_inicio && $fecha_fin) $this->db->where("(DATE_FORMAT(es.fecha_registra,'%Y-%m-%d') >= DATE_FORMAT('$fecha_inicio','%Y-%m-%d') AND DATE_FORMAT(es.fecha_registra,'%Y-%m-%d') <= DATE_FORMAT('$fecha_fin','%Y-%m-%d'))");
	// 	$query = $this->db->get()->result_array();
	// 	return $query;
	// }

	public function obtener_area_apreciacion($id_aux, $area_apreciacion=''){
		$this->db->select("ep.porcentaje as peso, vp.id id_area_apreciacion, vp.id_aux id_aux_area, vp.valor area_apreciacion");
		$this->db->from('evaluacion_permisos ep');
		$this->db->join('permisos_parametros pp', 'ep.id_permiso_parametro = pp.id');
		$this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
		$this->db->where("pp.vp_principal = '$id_aux' and ep.estado = 1");
		$this->db->where("vp.idparametro", 223);
		if($area_apreciacion){
			$this->db->where("pp.vp_secundario = '$area_apreciacion'");
			$query = $this->db->get()->row();
		}else $query = $this->db->get()->result_array();
		return $query;
	}

	public function obtenerPreguntasAreaAprec_auto($identificacion,$periodo){
		$this->db->select("er.id_tipo_evaluador,aa.id id_competencia, aa.valor competencia, aa.valorx area_apre, ap.valor area_apreciacion, vr.valor, vr.valorx rs, er.id_pregunta, pre.valor pregunta");
		$this->db->from('evaluacion_respuesta er');
		$this->db->join('evaluacion_solicitud es', 'es.id = er.id_solicitud');
		$this->db->join('valor_parametro aa', 'aa.id = er.id_competencia'); 
		$this->db->join('valor_parametro vr','vr.id = er.id_respuesta'); 
		$this->db->join('valor_parametro ap','aa.valorx = ap.id');
		$this->db->join('valor_parametro pre','er.id_pregunta = pre.id');
		$this->db->where("es.id_evaluado = '$identificacion' AND es.periodo = '$periodo' AND es.estado = 1 AND er.id_tipo_evaluador = (
			SELECT vpp.id
			FROM evaluacion_solicitud evj 
			INNER JOIN valor_parametro vpm ON vpm.id_aux = evj.id_metodo_eval 
			INNER JOIN permisos_parametros pp ON pp.vp_principal_id = vpm.id
			INNER JOIN valor_parametro vpp ON vpp.id = pp.vp_secundario_id AND vpp.idparametro = 215 AND vpp.valory = 1
			WHERE evj.id_evaluado = '$identificacion' AND evj.periodo = '$periodo' and evj.estado = 1 LIMIT 1 )");
		$this->db->group_by('er.id_pregunta');
		$this->db->order_by('area_apre', 'ASC');		
		$query = $this->db->get()->result_array();
		return $query;
	}

	public function obtenerPreguntasAreaAprec_jefe($identificacion,$periodo){
		$this->db->select("(SELECT vpp.id FROM evaluacion_solicitud evjf 
		INNER JOIN valor_parametro vpm ON vpm.id_aux = evjf.id_metodo_eval 
		INNER JOIN permisos_parametros pp ON pp.vp_principal_id = vpm.id 
		INNER JOIN valor_parametro vpp ON vpp.id = pp.vp_secundario_id 
		INNER JOIN evaluacion_solicitud evp ON evp.jefe_inmediato = evjf.id_evaluado and evp.id_evaluado = '$identificacion' and evp.periodo = '$periodo'
		WHERE vpp.idparametro = 215 AND vpp.valory = 3 and evjf.periodo = '$periodo' and evjf.estado = 1 LIMIT 1) as id_tipo_evaluador, 
		aa.id id_competencia, aa.valor competencia, aa.valorx area_apre, ap.valor area_apreciacion, vr.valor, vr.valorx rs, er.id_pregunta, pre.valor pregunta");
		$this->db->from('evaluacion_respuesta er');
		$this->db->join('evaluacion_solicitud es','es.id = er.id_solicitud'); 
		$this->db->join('valor_parametro aa','aa.id = er.id_competencia');
		$this->db->join('valor_parametro vr','vr.id = er.id_respuesta');
		$this->db->join('valor_parametro ap','aa.valorx = ap.id');
		$this->db->join('evaluacion_asignacion_persona asp','asp.id = er.id_asignacion_persona');
		$this->db->join('valor_parametro pre','er.id_pregunta = pre.id');
		$this->db->where("es.id_evaluado = (SELECT evj.jefe_inmediato FROM evaluacion_solicitud evj WHERE evj.id_evaluado = '$identificacion' and evj.periodo = '$periodo' and evj.estado = 1 LIMIT 1) AND asp.evaluado = '$identificacion' AND es.periodo = '$periodo' AND es.estado = 1");
		$this->db->group_by('er.id_pregunta');
		$this->db->order_by('area_apre', 'ASC');
		$query = $this->db->get()->result_array();
		return $query;
	}

	public function obtenerPreguntasAreaAprec_Met($identificacion, $per){
		$periodo = $per == '2020' ? $per : null;
		$puntos = $this->obtener_puntuacion_mayor($periodo);
		// $this->db->select("(SELECT v.id from valor_parametro v where v.id_aux = 'Eval_Jef') as id_tipo_evaluador, (SELECT v.id from valor_parametro v where v.id_aux = 'Eval_Met') area_apre,vr.valor, vr.valorx rs");
		// $this->db->from('talentocuc_indicadores evp');
		// $this->db->join('valor_parametro vr','vr.id = evp.id_respuesta');
		$this->db->select("(SELECT v.id from valor_parametro v where v.id_aux = 'Eval_Jef') as id_tipo_evaluador, (SELECT v.id from valor_parametro v where v.id_aux = 'Eval_Met') area_apre, ((evp.promedio_general*$puntos)/100) rs");
		$this->db->from('evaluacion_asignacion_preguntas evp');
		$this->db->where("evp.evaluado = '$identificacion' and evp.periodo = '$per'");
		$query = $this->db->get()->result_array();
		return $query;
	}
	public function obtenerPreguntasAreaAprec_coe($identificacion,$periodo){
		$this->db->select("er.id_tipo_evaluador, aa.id id_competencia, aa.valor competencia, aa.valorx area_apre, ap.valor area_apreciacion, vr.valor, vr.valorx rs, er.id_pregunta, pre.valor pregunta");
		$this->db->from('evaluacion_respuesta er');
		$this->db->join('evaluacion_solicitud es','es.id = er.id_solicitud');
		$this->db->join('valor_parametro aa','aa.id = er.id_competencia');
		$this->db->join('valor_parametro vr','vr.id = er.id_respuesta');
		$this->db->join('valor_parametro ap','aa.valorx = ap.id');
		$this->db->join('valor_parametro pre','er.id_pregunta = pre.id');
		$this->db->where("es.id_evaluado = (SELECT evj.id_evaluado FROM evaluacion_solicitud evj WHERE evj.coevaluacion = '$identificacion' and evj.periodo = '$periodo' and evj.estado = 1 limit 1) AND es.estado = 1 AND er.id_tipo_evaluador = (
				SELECT vpp.id FROM evaluacion_solicitud evj 
				INNER JOIN valor_parametro vpm ON vpm.id_aux = evj.id_metodo_eval 
				INNER JOIN permisos_parametros pp ON pp.vp_principal_id = vpm.id 
				INNER JOIN valor_parametro vpp ON vpp.id = pp.vp_secundario_id 
				AND vpp.idparametro = 215 AND vpp.valory = 2 and evj.periodo = '$periodo' and evj.id_evaluado = '$identificacion' and evj.estado = 1 LIMIT 1 ) and es.periodo = '$periodo'");
		$this->db->group_by('er.id_pregunta');
		$query = $this->db->get()->result_array();
		return $query;
	}

	public function obtenerPreguntasAreaAprec_per($identificacion,$periodo){
		$this->db->select("(SELECT v.id from valor_parametro v where v.id_aux = 'Eval_Per') as id_tipo_evaluador, aa.id id_competencia, aa.valor competencia, aa.valorx area_apre, ap.valor area_apreciacion, vr.valor, vr.valorx rs, es.id_evaluado, er.id_pregunta, pre.valor pregunta");
		$this->db->from('evaluacion_respuesta er');
		$this->db->join('evaluacion_solicitud es','es.id = er.id_solicitud');
		$this->db->join('valor_parametro aa','aa.id = er.id_competencia');
		$this->db->join('valor_parametro vr','vr.id = er.id_respuesta');
		$this->db->join('valor_parametro ap','aa.valorx = ap.id');
		$this->db->join('valor_parametro pre','er.id_pregunta = pre.id');
		$this->db->join('evaluacion_asignacion_persona eap',"eap.evaluado = es.id_evaluado and eap.evaluador = '$identificacion' and eap.periodo = '$periodo' and eap.estado = 1 and es.estado = 1");
		$this->db->where("es.jefe_inmediato = '$identificacion' AND es.periodo = '$periodo'");
		$this->db->where("er.id_tipo_evaluador = ( SELECT vpp.id FROM evaluacion_solicitud evj 
			INNER JOIN valor_parametro vpm ON vpm.id_aux = evj.id_metodo_eval 
			INNER JOIN permisos_parametros pp ON pp.vp_principal_id = vpm.id 
			INNER JOIN valor_parametro vpp ON vpp.id = pp.vp_secundario_id 
			AND vpp.idparametro = 215 AND vpp.valory = 3 AND evj.id_evaluado = '$identificacion' AND evj.periodo = '$periodo' and evj.estado = 1 LIMIT 1 )");
		$query = $this->db->get();
		return $query->result_array();  	
	}

	public function obtenerPorcentajes($id_solicitud){
		$this->db->select("vp.id area_apre, vp.valor area_apreciacion, ep.porcentaje porcentaje_area, vpt.id_aux id_aux_tipo_evaluador, vpt.id id_tipo_evaluador, vpt.valor tipo_evaluador, epp.porcentaje porcentaje_tipo_evaluador, vp.id_aux id_aux_area_apre");
		$this->db->from('valor_parametro vp');
		$this->db->join('permisos_parametros ppa','ppa.vp_secundario_id = vp.id');
		$this->db->join('evaluacion_permisos ep','ppa.id = ep.id_permiso_parametro');
		$this->db->join('permisos_parametros pp','pp.vp_secundario_id = ppa.vp_principal_id');
		$this->db->join('evaluacion_permisos epp','pp.id = epp.id_permiso_parametro');
		$this->db->join('valor_parametro vpt','vpt.id = ppa.vp_principal_id');
		$this->db->join('evaluacion_solicitud es','es.id_metodo_eval = pp.vp_principal');
		$this->db->where("vp.estado = 1 and vpt.estado = 1 and es.id = $id_solicitud");  
		$this->db->order_by('tipo_evaluador', 'ASC');
        $query = $this->db->get();
        return $query->result_array();  
	}

	public function obtenerResultado($id_solicitud){
		$this->db->select("rf.*");
		$this->db->from('evaluacion_resultado_final rf');
		$this->db->where("rf.id_solicitud = $id_solicitud");  
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

	public function exportar_evaluaciones($id, $estado, $tipo, $fecha_i, $fecha_f, $periodo){		
		$this->db->select("CONCAT(p.nombre,' ',p.apellido, ' ', p.segundo_apellido) AS nombre_evaluador,
		p.identificacion AS cc_evaluador,
		ve.valor AS estado,
		te.valor AS tipo_evaluador,
		CASE te.valory  WHEN '1' THEN 'NO APLICA'  WHEN '2' THEN CONCAT(pc.nombre,' ',pc.apellido, ' ', pc.segundo_apellido)  WHEN '3' THEN CONCAT(pj.nombre,' ',pj.apellido, ' ', pj.segundo_apellido)  WHEN '4' THEN CONCAT(pe.nombre,' ',pe.apellido, ' ', pe.segundo_apellido) END AS nombre_evaluado,
		CASE te.valory  WHEN '1' THEN 'NO APLICA'  WHEN '2' THEN pc.identificacion  WHEN '3' THEN pj.identificacion  WHEN '4' THEN pe.identificacion END AS identificacion_evaluado,
		acp.valor AS area_co,
		cp.valor AS competencia,
		pr.valor AS pregunta, 		
		vr.valor AS respuesta,
		ap.valor AS area_apre,
		cp.id AS id_competencia,
		vr.valorx AS num_respuesta,
		dep.valor AS departamento,
		ev.periodo", false);
		$this->db->from('evaluacion_solicitud ev');
		$this->db->join('personas p','ev.id_evaluado = p.identificacion');
		$this->db->join('personas pj','ev.jefe_inmediato = pj.identificacion');
		$this->db->join('personas pc','ev.coevaluacion = pc.identificacion');
		$this->db->join('valor_parametro ve','ve.id_aux = ev.id_estado_eval');
		$this->db->join('evaluacion_respuesta er','ev.id = er.id_solicitud','left');
		$this->db->join('valor_parametro vr','vr.id = er.id_respuesta','left');
		$this->db->join('valor_parametro pr','pr.id = er.id_pregunta','left');
		$this->db->join('valor_parametro te','te.id = er.id_tipo_evaluador','left');
		$this->db->join('valor_parametro acp','acp.id = er.id_area_competencia','left');
		$this->db->join('valor_parametro cp','cp.id = er.id_competencia','left');
		$this->db->join('valor_parametro ap','ap.id = cp.valorx','left');
		$this->db->join('evaluacion_asignacion_persona eap','eap.id = er.id_asignacion_persona and eap.estado = 1 AND eap.periodo = ev.periodo','left');
		$this->db->join('personas pe','eap.evaluado = pe.identificacion','left');
		$this->db->join('valor_parametro dep', 'p.id_cargo_sap = dep.id','left');
		$this->db->where("ev.id_estado_eval <> 'Eval_Can' and ev.estado = 1");
		if (!empty($periodo)) $this->db->where("ev.periodo = '$periodo'");
		if (!empty($tipo)) $this->db->where("ev.id_metodo_eval = '$tipo'");
		if (!empty($id)) $this->db->where('ev.id',$id);
		if (!empty($estado)) $this->db->where('ev.id_estado_eval',$estado);
		if ($fecha_i && $fecha_f) $this->db->where("(DATE_FORMAT(ev.fecha_registra,'%Y-%m-%d') >= DATE_FORMAT('$fecha_i','%Y-%m-%d') AND DATE_FORMAT(ev.fecha_registra,'%Y-%m-%d') <= DATE_FORMAT('$fecha_f','%Y-%m-%d'))");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function exportar_evaluaciones_indicadores($id, $estado, $tipo, $fecha_i, $fecha_f, $periodo){
		$this->db->select("CONCAT(pj.nombre,' ',pj.apellido, ' ', pj.segundo_apellido) AS nombre_evaluador,
		pj.identificacion AS cc_evaluador,
		ve.valor AS estado,
		'INDICADORES' AS tipo_evaluador,
		CONCAT(p.nombre,' ',p.apellido, ' ', p.segundo_apellido) AS nombre_evaluado,
		p.identificacion AS cc_evaluado,
		'Metas de Desempeño' AS area_co,
		'NO APLICA' AS competencia,
		eap.pregunta,
		vr.valor AS respuesta,
		dep.valor AS departamento,
		ev.periodo, eap.meta, eap.resultado, eap.cumplimiento", false);
		$this->db->from("evaluacion_solicitud ev");
		$this->db->join('valor_parametro ve','ve.id_aux = ev.id_estado_eval');
		$this->db->join('personas p','ev.id_evaluado = p.identificacion');
		$this->db->join('personas pj','ev.jefe_inmediato = pj.identificacion');
		$this->db->join('talentocuc_indicadores eap','eap.evaluado = ev.id_evaluado and eap.estado = 1 and eap.periodo = ev.periodo');
		$this->db->join('valor_parametro vr','vr.id = eap.id_respuesta','left');
		$this->db->join('valor_parametro dep', 'p.id_cargo_sap = dep.id','left');
		$this->db->where("ev.id_estado_eval <> 'Eval_Can' and ev.estado = 1");
		if (!empty($periodo)) $this->db->where("ev.periodo = '$periodo'");
		if (!empty($tipo)) $this->db->where("ev.id_metodo_eval = '$tipo'");
		if (!empty($id)) $this->db->where('ev.id',$id);
		if (!empty($estado)) $this->db->where('ev.id_estado_eval',$estado);
		if ($fecha_i && $fecha_f) $this->db->where("(DATE_FORMAT(ev.fecha_registra,'%Y-%m-%d') >= DATE_FORMAT('$fecha_i','%Y-%m-%d') AND DATE_FORMAT(ev.fecha_registra,'%Y-%m-%d') <= DATE_FORMAT('$fecha_f','%Y-%m-%d'))");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_personal_actas($id_solicitud){
		$this->db->select("CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, p.identificacion, eap.id id_asignacion_persona, esp.id id_solicitud_evaluado, esp.periodo, esp.acta acta_retro, dep.valor cargo_funcionario, 
		CONCAT(jef.nombre,' ',jef.apellido, ' ',jef.segundo_apellido) nombre_jefe, jef.identificacion identificacion_jefe, cj.valor cargo_jefe, eap.periodo, esp.id_estado_eval, esp.resultado, esp.fecha_retroalimentacion, esp.sugerencias, vp.valor metodo",false);
		$this->db->from('personas p');
		$this->db->join('evaluacion_asignacion_persona eap', 'p.identificacion = eap.evaluado and eap.estado = 1', 'left');
		$this->db->join('valor_parametro dep', 'p.id_cargo_sap = dep.id','left');
		$this->db->join('evaluacion_solicitud esp', 'p.identificacion = esp.id_evaluado and eap.periodo = esp.periodo and esp.estado=1');
		$this->db->join('valor_parametro vp', 'vp.id_aux = esp.id_metodo_eval');
		$this->db->join('personas jef', 'jef.identificacion = esp.jefe_inmediato', 'lef');
		$this->db->join('valor_parametro cj', 'jef.id_cargo_sap = cj.id','left');
		$this->db->join('evaluacion_solicitud es', 'eap.evaluador = es.id_evaluado and eap.periodo = es.periodo');		
		$this->db->where("es.id", $id_solicitud);
		$this->db->where("esp.id_estado_eval <> 'Eval_Can'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_oportunidades_mejora($id_evaluado,$id_solicitud){
		$this->db->select("ec.id_evaluado identificacion, ec.id id_compromiso, ec.actividad compromiso, ec.id_solicitud solicitud_evaluado");
		$this->db->from('evaluacion_compromisos ec');
		$this->db->where("ec.id_evaluado", $id_evaluado);
		$this->db->where("ec.id_solicitud", $id_solicitud);
		$this->db->where("ec.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_sugerencias_formacion($id_evaluado,$id_solicitud){
		$this->db->select("es.id_evaluado identificacion, es.id id_sugerencia, es.observacion, es.id_solicitud solicitud_evaluado");
		$this->db->from('evaluacion_sugerencias_formacion es');
		$this->db->where("es.id_evaluado", $id_evaluado);
		$this->db->where("es.id_solicitud", $id_solicitud);
		$this->db->where("es.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

	// public function listar_detalle_resultados($id_evaluado, $id_solicitud_evaluado=''){
	// 	$this->db->select("ec.*, cp.valor competencia, acp.valor area_apreciacion, acp.id_aux, pre.valor pregunta");
	// 	$this->db->from('evaluacion_resultado_competencia ec');
	// 	$this->db->join('valor_parametro cp','cp.id = ec.id_competencia');
	// 	$this->db->join('valor_parametro acp','acp.id = cp.valorx');
	// 	$this->db->join('valor_parametro pre','pre.id = ec.id_pregunta', 'left');
	// 	$this->db->join('evaluacion_solicitud es','es.id = ec.id_solicitud');
	// 	if($id_solicitud_evaluado) $this->db->where('es.id', $id_solicitud_evaluado);
	// 	$this->db->where("ec.id_persona", $id_evaluado);
	// 	$this->db->where("ec.estado = 1");
	// 	if($filtro == 1) $this->db->where("ec.estado_formacion", $filtro);
	// 	$this->db->order_by("ec.puntaje", "desc");
	// 	$query = $this->db->get();
	// 	return $query->result_array();
	// }

	public function listar_detalle_resultados($id_evaluado,$id_solicitud,$filtro=null){
		$this->db->select("ec.*, cp.valor competencia, cp.valorz icono, acp.valor area_apreciacion, acp.id_aux, pre.valor pregunta, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
		$this->db->from('evaluacion_resultado_competencia ec');
		$this->db->join('valor_parametro cp','cp.id = ec.id_competencia');
		$this->db->join('valor_parametro acp','acp.id = cp.valorx','left');
		$this->db->join('valor_parametro pre','pre.id = ec.id_pregunta', 'left');
		$this->db->join('evaluacion_solicitud es', 'es.id=ec.id_solicitud and es.estado = 1');
		$this->db->join('personas p','ec.id_persona = p.identificacion');
		$this->db->where("ec.id_persona", $id_evaluado);
		$this->db->where("ec.estado = 1");
		if($id_solicitud) $this->db->where('es.id', $id_solicitud);
		if($filtro == 1) $this->db->where("ec.estado_formacion = $filtro and cp.valory <> 1");
		$this->db->order_by("ec.puntaje", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_personal_sinActas($id_solicitud){
		$this->db->select("eap.id id_asignacion");
		$this->db->from('evaluacion_asignacion_persona eap');
		$this->db->join('evaluacion_solicitud es', 'eap.evaluador = es.id_evaluado and eap.periodo = es.periodo');
		$this->db->join('evaluacion_solicitud esv', 'eap.evaluado = esv.id_evaluado and eap.periodo = esv.periodo and esv.estado=1');
		$this->db->where("es.id = $id_solicitud and eap.estado = 1 and esv.acta = 0");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function validar_personal_aCargo($id_persona,$jefe_inmediato,$periodo){
		$this->db->select("eap.*");
		$this->db->from('evaluacion_asignacion_persona eap');
		$this->db->where("eap.evaluador = '$jefe_inmediato' and eap.periodo = '$periodo' and eap.evaluado = '$id_persona' and eap.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_jefe_asignacionPersonas($id_evaluado, $periodo){
		$this->db->select("jef.identificacion cc_jefe, CONCAT(jef.nombre,' ',jef.apellido, ' ',jef.segundo_apellido) nombre_jefe, crj.valor departamento_jefe",false);
		$this->db->from('evaluacion_asignacion_persona eap');
		$this->db->join('personas jef', 'jef.identificacion = eap.evaluador');
		$this->db->join('valor_parametro crj', 'jef.id_cargo_sap = crj.id','left');
		$this->db->where("eap.periodo = '$periodo' and eap.evaluado = '$id_evaluado' and eap.estado = 1");
		$query = $this->db->get();
		return $query->row();
	}

	public function listar_asignacion_personas($id_persona){
		$this->db->select("eap.id, eap.evaluador, eap.periodo, p.identificacion id_evaluado, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo",false);
		$this->db->from('evaluacion_asignacion_persona eap');
		$this->db->join('personas p', 'p.identificacion = eap.evaluado');
		$this->db->where("eap.evaluador = '$id_persona' and eap.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

	// public function listar_planformacion_personal($id_competencia){
	// 	$this->db->select("pf.*, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) funcionario, vp.valor competencia, vpl.valor lugar",false);
	// 	$this->db->from('evaluacion_plan_formacion pf');
	// 	$this->db->join('personas p', 'p.identificacion = pf.id_funcionario');
	// 	$this->db->join('valor_parametro vp', 'pf.id_competencia = vp.id');
	// 	$this->db->join('valor_parametro vpl', 'pf.id_lugar = vpl.id');
	// 	$this->db->where("pf.estado = 1 and pf.id_competencia = $id_competencia");
	// 	$query = $this->db->get();
	// 	return $query->result_array();
	// }

	public function listar_planformacion_personal($id_persona, $id_competencia){
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

	public function listar_informe_resultados($metodo, $periodo){
		$this->db->select("es.id id_evaluacion, p.identificacion, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, er.puntuacion_centil puntuacion, er.valoracion", false);
		$this->db->from('evaluacion_solicitud es');
		$this->db->join('personas p', 'p.identificacion = es.id_evaluado');
		$this->db->join('evaluacion_resultado_final er', 'er.id_solicitud = es.id');
		$this->db->where("es.id_metodo_eval = '$metodo' and es.periodo = '$periodo' AND es.estado = 1 AND es.resultado = 1 AND (es.id_estado_eval <> 'Eval_Sol' && es.id_estado_eval <> 'Eval_Cer' && es.id_estado_eval <> 'Eval_Can' && es.id_estado_eval <> 'Eval_Env')");
		$query = $this->db->get()->result_array();		
		$sql = $this->get_resultados_tipo_evaluador($metodo, $periodo);
		return ['resfinal' => $query, 'evaluador' => $sql];
	}
	public function get_resultados_tipo_evaluador($metodo, $periodo){
		$this->db->select("es.id id_evaluacion, vp.valor tipo_evaluador, te.suma resultado");
		$this->db->from('evaluacion_solicitud es');
		$this->db->join('evaluacion_resultado_tipo_evaluador te', 'te.id_solicitud = es.id');
		$this->db->join('valor_parametro vp', 'vp.id = te.id_tipo_evaluador');
		$this->db->where("es.id_metodo_eval = '$metodo' and es.periodo = '$periodo' AND es.estado = 1 AND es.resultado = 1 AND (es.id_estado_eval <> 'Eval_Sol' && es.id_estado_eval <> 'Eval_Cer' && es.id_estado_eval <> 'Eval_Can' && es.id_estado_eval <> 'Eval_Env')");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_informe_compromisos($metodo, $periodo){
		$this->db->select("p.identificacion IDENTIFICACION, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) NOMBRE, ec.actividad COMPROMISO", false);
		$this->db->from('evaluacion_compromisos ec');
		$this->db->join('evaluacion_solicitud es', 'es.id = ec.id_solicitud');
		$this->db->join('personas p', 'p.identificacion = es.id_evaluado');
		$this->db->where("ec.estado = 1 and es.id_metodo_eval = '$metodo' and es.periodo = '$periodo'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_informe_sugerencias($metodo, $periodo){
		$this->db->select("p.identificacion IDENTIFICACION, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) NOMBRE, vp.valor DEPARTAMENTO, sf.observacion SUGERENCIA", false);
		$this->db->from('evaluacion_sugerencias_formacion sf');
		$this->db->join('evaluacion_solicitud es', 'es.id = sf.id_solicitud');
		$this->db->join('personas p', 'p.identificacion = es.id_evaluado');
		$this->db->join('valor_parametro vp', 'vp.id = p.id_cargo_sap');
		$this->db->where("sf.estado = 1 and es.id_metodo_eval = '$metodo' and es.periodo = '$periodo'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_informe_competencias($metodo, $periodo){
		$this->db->select("p.identificacion identificacion, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, vp.valor competencia, ec.fortaleza, ec.mejora", false);
		$this->db->from('evaluacion_resultado_competencia ec');
		$this->db->join('evaluacion_solicitud es', 'es.id = ec.id_solicitud');
		$this->db->join('personas p', 'p.identificacion = es.id_evaluado');
		$this->db->join('valor_parametro vp', 'vp.id = ec.id_competencia');
		$this->db->where("ec.estado = 1 and es.id_metodo_eval = '$metodo' and es.periodo = '$periodo'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_cantidad_personas($metodo, $periodo, $tabla){
		$this->db->select("DISTINCT(es.id_evaluado)");
		$this->db->from("$tabla t");
		$this->db->join('evaluacion_solicitud es', "es.id = t.id_solicitud and es.id_metodo_eval = '$metodo' and es.periodo = '$periodo'");
		$this->db->where("t.estado = 1");
		$this->db->group_by('es.id_evaluado');
		return COUNT($this->db->get()->result_array());
	}	

	public function obtener_funciones($id_evaluado, $periodo){
		$this->db->select("fun.id id_pregunta, fun.evaluado, fun.pregunta, fun.periodo, fun.respuesta, fun.id_tipo_respuesta");
		$this->db->from('talentocuc_funciones fun');
		// $this->db->join('personas p', 'p.id = fun.id_persona');
		$this->db->where("fun.evaluado = '$id_evaluado' and fun.periodo = '$periodo' and fun.estado=1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_formacion_esencial($id_evaluado, $periodo, $resp=null){
		$this->db->select("fun.id id_pregunta, fun.evaluado, fun.pregunta, fun.periodo, fun.respuesta id_respuesta, vp.valor respuesta, fun.id_tipo_respuesta");
		$this->db->from('talentocuc_formacion_esencial fun');
		// $this->db->join('personas p', 'p.id = fun.id_persona');
		$this->db->join('valor_parametro vp', 'vp.id = fun.respuesta');
		$this->db->where("fun.evaluado = '$id_evaluado' and fun.periodo = '$periodo' and fun.estado=1");
		if($resp) $this->db->where('vp.valorx', $resp);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_puntuacion_mayor($periodo=null){
		$this->db->select("vp.valor puntos");
		$this->db->from('valor_parametro vp');
		$this->db->where("vp.estado = 1 and vp.idparametro = 224 and vp.valorx = '100.0'");
		if($periodo) $this->db->where("vp.valory = '$periodo'");
		else $this->db->where("vp.valorz = 1");
		$this->db->order_by("vp.valor", "desc");
		$this->db->limit(1);
		$query = $this->db->get()->row()->puntos;
		return $query;
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

	public function obtener_resultado_metas($id_evaluado, $periodo=null){
		$this->db->select("pre.* ");
		$this->db->from('evaluacion_asignacion_preguntas pre');
		$this->db->where("pre.evaluado = '$id_evaluado' and pre.periodo = '$periodo' and pre.estado=1");
		$query = $this->db->get();
		return $query->row()->puntuacion_meta;
	}

	public function evaluacion_respuestas_formacionForm($id_evaluado, $periodo, $tabla){
		$this->db->select("fun.id id_pregunta, fun.pregunta, fun.id_tipo_respuesta, fun.respuesta id_respuesta, vp.valor respuesta");
		$this->db->from("$tabla fun");
		// $this->db->join('personas p', 'p.id = fun.id_persona');
		$this->db->join('valor_parametro vp', 'vp.id = fun.respuesta','left');
		$this->db->where("fun.evaluado = '$id_evaluado' and fun.periodo = '$periodo' and fun.estado=1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_evaluaciones_anotificar( $estado, $tipo, $fecha_i, $fecha_f, $periodo, $resultado=''){
		if($estado === 'vacio') $estado = '';
		if($fecha_i == 0) $fecha_i = '';
		if($fecha_f == 0) $fecha_f = '';
		$this->db->select("CONCAT(pf.nombre,' ',pf.apellido, ' ',pf.segundo_apellido) persona, pf.correo, es.id_metodo_eval", false);
		$this->db->from('evaluacion_solicitud es');
		$this->db->join('personas pf', 'pf.identificacion = es.id_evaluado');
		if (!empty($periodo)) $this->db->where("es.periodo = '$periodo'");
		if (!empty($resultado)) $this->db->where("es.resultado = '$resultado'");
		if (!empty($tipo)) $this->db->where("es.id_metodo_eval = '$tipo'");
		if (!empty($estado)){
			if($estado === 'Eval_Act_Fin') $this->db->where("(es.id_estado_eval = '$estado' OR es.recibido = 1)");
			else $this->db->where('es.id_estado_eval',$estado);
		}
		if ($fecha_i && $fecha_f) $this->db->where("(DATE_FORMAT(es.fecha_registra,'%Y-%m-%d') >= DATE_FORMAT('$fecha_i','%Y-%m-%d') AND DATE_FORMAT(es.fecha_registra,'%Y-%m-%d') <= DATE_FORMAT('$fecha_f','%Y-%m-%d'))");
		$this->db->where("es.estado",1);
		$this->db->order_by("es.fecha_registra", "DESC");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_indicadores_funciones_formacion($id_asignacion_persona){
		$this->db->select("COUNT(ind.id) indicadores,COUNT(fun.id) funciones");
		$this->db->from("evaluacion_asignacion_persona eap");
		$this->db->join('talentocuc_indicadores ind', 'ind.evaluado = eap.evaluado AND eap.periodo = ind.periodo and ind.estado=1','left');
		// $this->db->join('talentocuc_formacion_esencial form', 'form.evaluado = eap.evaluado','left');
		$this->db->join('talentocuc_funciones fun', 'fun.evaluado = eap.evaluado AND eap.periodo = fun.periodo and fun.estado=1','left');
		$this->db->where("eap.id = $id_asignacion_persona and eap.estado=1");
		$query = $this->db->get();
		$cantidad = $query->row()->indicadores + $query->row()->funciones;
		return $cantidad;
	}

	public function validar_indicadores_funciones($identificacion,$periodo){
		$this->db->select("ev.id, count(ind.id) indicadores, COUNT(fun.id) funciones, eap.promedio_general");
		$this->db->from("evaluacion_solicitud ev");
		$this->db->join('evaluacion_asignacion_preguntas eap', 'eap.evaluado = ev.id_evaluado AND eap.periodo = ev.periodo','left');
		$this->db->join('talentocuc_indicadores ind', 'ind.evaluado = ev.id_evaluado AND ev.periodo = ind.periodo AND ind.estado=1','left');
		$this->db->join('talentocuc_funciones fun', 'fun.evaluado = ev.id_evaluado AND ev.periodo = fun.periodo AND fun.estado=1','left');
		$this->db->where("ev.id_evaluado = '$identificacion' AND ev.periodo = '$periodo' AND ev.estado=1");
		$query = $this->db->get();
		return $query->row();
	}

	public	function eliminar_datos($where, $tabla){
		$this->db->where("$where");
		$this->db->delete($tabla);
		$error = $this->db->_error_message();
		return $error ? -1 : 1;
	}

	public function validar_resultado_competencia($id_solicitud){
		$this->db->select("ec.*");
		$this->db->from('evaluacion_resultado_competencia ec');
		$this->db->where("ec.id_solicitud", $id_solicitud);
		$this->db->where("ec.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}
}