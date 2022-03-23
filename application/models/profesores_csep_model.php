<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH')) exit('No direct script access allowed');

class profesores_csep_model extends CI_Model
{
  public function buscar_profesor($where, $permisos = false, $periodo = '', $filtro_firma = ''){
    $periodo = !$periodo ? $this->obtener_periodo_actual(): $periodo;
    $administra = $_SESSION["perfil"] == "Per_Admin"  || $_SESSION["perfil"] == "Per_Adm_plan" || $_SESSION["perfil"] == "Per_Csep" ? true: false;
    $id_persona = $_SESSION['persona'];
    $this->db->select("cp.*,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,p.identificacion,p.correo,dep.valor departamento,pro.valor programa,area.valor area_conocimiento,ded.valor dedicacion,esc.valor escalafon,cont.valor contrato, gru.valor grupo,est.valor estado_act, p.id id_persona, cpfp.fecha_registro fecha_firma, cpfp.firma firma_profesor, cpfd.firma firma_decano", false);
    $this->db->from('personas p');	
    $this->db->join('csep_profesores cp', 'p.id = cp.id_persona','left');
    $this->db->join('valor_parametro dep', 'cp.id_departamento = dep.id','left');
    $this->db->join('valor_parametro pro', 'cp.id_programa = pro.id','left');
    $this->db->join('valor_parametro area', 'cp.id_area = area.id','left');
    $this->db->join('valor_parametro ded', 'cp.id_dedicacion = ded.id','left');
    $this->db->join('valor_parametro esc', 'cp.id_escalafon = esc.id','left');
    $this->db->join('valor_parametro cont', 'cp.id_contrato = cont.id','left');
    $this->db->join('valor_parametro gru', 'cp.id_grupo = gru.id','left');
    $this->db->join('valor_parametro est', 'cp.id_estado = est.id','left');
    $this->db->join('csep_profesores_firmas cpfp', 'cpfp.id_plan = cp.id AND cpfp.tipo = "profesor" AND cpfp.estado = 1','left');
    $this->db->join('csep_profesores_firmas cpfd', 'cpfd.id_plan = cp.id AND cpfd.tipo = "decano" AND cpfd.estado = 1','left');
    $this->db->where($where);
    $this->db->where('periodo', $periodo);
    if ($filtro_firma) {
      if($filtro_firma == 'Firm_Comp') $this->db->where('cpfp.firma IS NOT NULL AND cpfd.firma IS NOT NULL');
      else if($filtro_firma == 'Firm_Falt') $this->db->where('cpfp.firma IS NULL AND cpfd.firma IS NULL');
      else if($filtro_firma == 'Firm_Deca') $this->db->where('cpfp.firma IS NOT NULL AND cpfd.firma IS NULL');
      else if($filtro_firma == 'Firm_Prof') $this->db->where('cpfp.firma IS NULL AND cpfd.firma IS NOT NULL');
    }
    if ($permisos) {
      if (!$administra) {
        $this->db->join('csep_parametros_persona cpp', "cp.id_departamento = cpp.id_parametro AND cpp.estado = 1 AND cpp.id_persona = ". $id_persona);
      }
    }
    $this->db->group_by('cp.id');
    $query = $this->db->get();
    return $query->result_array();
  }
  public function formacion_profesor($id_profesor){
    $this->db->select("pf.nombre,tipo.valor formacion,pf.id,pf.id_formacion,pf.id_profesor");
    $this->db->from('csep_profesor_formacion pf');	
    $this->db->join('valor_parametro tipo', 'pf.id_formacion = tipo.id');
    $this->db->where('pf.id_profesor',$id_profesor);
    $this->db->where('pf.estado',1);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function indicadores_profesor($id_profesor, $tipo = '',$fecha_inicio = '', $fecha_fin = ''){
    $this->db->select("pi.*,tipo.valor nombre");
    $this->db->from('csep_profesor_indicadores pi');	
    $this->db->join('valor_parametro tipo', 'pi.id_indicador = tipo.id');
    $this->db->where('pi.id_profesor',$id_profesor);
    $this->db->where('pi.estado',1);
    if(empty($tipo))$this->db->where('pi.tipo', 'Aplica');
    if(!empty($fecha_inicio) && !empty($fecha_fin))$this->db->where("(DATE_FORMAT(pi.fecha_inicial,'%Y-%m-%d') >= '$fecha_inicio' AND DATE_FORMAT(pi.fecha_final,'%Y-%m-%d') <= '$fecha_fin')");
    $this->db->order_by("tipo.valor");
    $this->db->order_by("pi.fecha_final");
    $query = $this->db->get();
    return $query->result_array();
  }
  public function perfiles_profesor($id_profesor){
    $this->db->select("pp.*,per.valor perfil,ro.valor rol,pp.id_cobertura cobertura");
    $this->db->from('csep_profesor_perfil pp');	
    $this->db->join('valor_parametro per', 'pp.id_perfil = per.id');
    $this->db->join('valor_parametro ro', 'pp.id_rol = ro.id');

    $this->db->where('pp.id_profesor',$id_profesor);
    $this->db->where('pp.estado',1);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function asignaturas_profesor($id_profesor){
    $this->db->select("pa.*,tipo.valor nombre,d.valor dia,pa.horario inicio, (INTERVAL pa.creditos HOUR + pa.horario) as fin");
    $this->db->from('csep_profesor_asignatura pa');	
    $this->db->join('valor_parametro tipo', 'pa.id_asignatura = tipo.id');
    $this->db->join('valor_parametro d', 'pa.id_dia = d.id');
    $this->db->where('pa.id_profesor',$id_profesor);
    $this->db->where('pa.estado',1);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function obtener_asignaturas_agrupadas($id_profesor){
    $this->db->select("pa.id_asignatura id, tipo.valor");
    $this->db->from('csep_profesor_asignatura pa');	
    $this->db->join('valor_parametro tipo', 'pa.id_asignatura = tipo.id');
    $this->db->join('valor_parametro d', 'pa.id_dia = d.id');
    $this->db->where('pa.id_profesor',$id_profesor);
    $this->db->where('pa.estado',1);
    $this->db->group_by('pa.id_asignatura');
    $query = $this->db->get();
    return $query->result_array();
  }
  public function atencion_profesor($id_profesor){
    $this->db->select("pa.*, tipo.valor nombre, ate.valor tipo_atencion, asi.valor asignatura");
    $this->db->from('csep_profesor_atencion pa');	
    $this->db->join('valor_parametro tipo', 'pa.id_dia = tipo.id');
    $this->db->join('valor_parametro ate', 'pa.id_tipo = ate.id_aux');
    $this->db->join('valor_parametro asi', 'pa.id_asignatura = asi.id', 'left');
    $this->db->where('pa.id_profesor',$id_profesor);
    $this->db->where('pa.estado',1);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function horas_programa_profesor($id_profesor){
    $this->db->select("pa.*,h.valor hora,p.valor programa");
    $this->db->from('csep_profesor_horas pa');	
    $this->db->join('valor_parametro h', 'pa.id_hora = h.id');
    $this->db->join('valor_parametro p', 'pa.id_programa = p.id');
    $this->db->where('pa.id_profesor',$id_profesor);
    $this->db->where('pa.estado',1);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function objetivos_profesor($id_profesor){
    $this->db->select("po.*");
    $this->db->from('csep_profesor_objetivos po');	
    $this->db->where('po.id_profesor',$id_profesor);
    $this->db->where('po.estado',1);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function lineas_profesor($id_profesor){
    $this->db->select("pa.*,l.valor linea,sl.valor sub_linea");
    $this->db->from('csep_profesores_lineas pa');	
    $this->db->join('valor_parametro l', 'pa.id_linea = l.id');
    $this->db->join('valor_parametro sl', 'pa.id_sub_linea = sl.id');
    $this->db->where('pa.id_profesor',$id_profesor);
    $this->db->where('pa.estado',1);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function obtener_parametros($filtro) {
    $this->db->select("*,upper(p.nombre) nombre");
    $this->db->from('parametros p');	
    if($filtro)foreach ($filtro as $key ) $this->db->or_where("p.id = $key");
    else $this->db->where("p.id = -1");
    $this->db->order_by("p.nombre", "asc");
    $query = $this->db->get();
    return $query->result_array();
  }
  public function obtener_valores_parametros($buscar, $tipo = 1) {
    $this->db->select("vp.id,vp.id_aux,vp.valor,vp.valorx,vp.idparametro,upper(vp.valor) nombre");
    $this->db->from('valor_parametro vp');	
    $this->db->where('vp.estado', "1");
    if($tipo == 1)$this->db->where('vp.idparametro ', $buscar);
    else $this->db->where($buscar);
    $this->db->order_by("vp.valor", "asc");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_valores_permisos($idparametro, $id_valor, $tipo){
    $this->db->select("vp.*,pp.id id_permiso,upper(vp.valor) nombre");
    $this->db->from("valor_parametro vp");
    $this->db->join("csep_relaciones pp", "pp.id_secundario = vp.id AND pp.id_principal =".$id_valor, "$tipo");
    $this->db->where('vp.estado', 1);
    $this->db->where('vp.idparametro', $idparametro);
    $query = $this->db->get();
    return $query->result_array();
}

  public function verificar_relacion($id_principal,  $id_secundario){
    $this->db->select("*");
    $this->db->from("csep_relaciones vp");
    $this->db->where('vp.id_principal', $id_principal);
    $this->db->where('vp.id_secundario', $id_secundario);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function parametros_persona($id, $id_persona = '', $limit = '', $tipo = 'pp.tipo = 1'){
    $this->db->select("pp.*,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,p.identificacion", false);
    $this->db->from("csep_parametros_persona pp");
    $this->db->join("personas p", "pp.id_persona = p.id");
    $this->db->where('pp.estado', 1);
    $this->db->where('pp.id_parametro', $id);
    if($id_persona)$this->db->where('pp.id_persona', $id_persona);
    if($tipo)$this->db->where($tipo);	
    if($limit)$this->db->limit($limit);	
    $this->db->order_by("pp.id", "desc");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function verificar_cruze_atencion_profesor($id_profesor, $hora_inicio, $hora_fin, $id_dia){
    $this->db->select("*");
    $this->db->from('csep_profesor_atencion pa');	
    $this->db->where('pa.id_profesor',$id_profesor);
    $this->db->where('pa.id_dia',$id_dia);
    $this->db->where("('$hora_inicio' BETWEEN pa.hora_inicio AND pa.hora_fin OR '$hora_fin' BETWEEN pa.hora_inicio AND pa.hora_fin OR pa.hora_inicio BETWEEN '$hora_inicio' AND '$hora_fin' OR  pa.hora_fin BETWEEN '$hora_inicio' AND '$hora_fin')");
    $this->db->where('pa.estado',1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function contar_horas( $id_profesor, $tipo){
    $this->db->select("SUM(pa.cantidad) total");
    $this->db->from('csep_profesor_horas pa');	
    $this->db->join('valor_parametro h', 'pa.id_hora = h.id');
    $this->db->where('pa.id_profesor',$id_profesor);
    $this->db->where("h.id_aux", $tipo);
    $this->db->where('pa.estado',1);
    $query = $this->db->get();
    $row = $query->row();
    return $row->total;
  }

  public function buscar_profesores_excel($periodo)
  {
    $this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, p.identificacion, dep.valor departamento, pro.valor programa, area.valor area_conocimiento, ded.valor dedicacion, esc.valor escalafon, cont.valor contrato, cp.fecha_inicio, cp.fecha_fin, gru.valor grupo, cp.cvlac, cp.google, cp.scopus, est.valor estado_act, cp.periodo periodo", false);
    $this->db->from('csep_profesores cp');
    $this->db->join('personas p', 'cp.id_persona = p.id');
    $this->db->join('valor_parametro dep', 'cp.id_departamento = dep.id','left');
    $this->db->join('valor_parametro pro', 'cp.id_programa = pro.id','left');
    $this->db->join('valor_parametro area', 'cp.id_area = area.id','left');
    $this->db->join('valor_parametro ded', 'cp.id_dedicacion = ded.id','left');
    $this->db->join('valor_parametro esc', 'cp.id_escalafon = esc.id','left');
    $this->db->join('valor_parametro cont', 'cp.id_contrato = cont.id','left');
    $this->db->join('valor_parametro gru', 'cp.id_grupo = gru.id','left');
    $this->db->join('valor_parametro est', 'cp.id_estado = est.id','left');
    $this->db->where('cp.estado_registro', 1);
    $this->db->where('cp.periodo', $periodo);
    return $this->db->get()->result_array();
  }

  public function buscar_profesores_lineas_excel($periodo)
  {
    $this->db->select("p.identificacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, lin.valor linea, sub.valor sub_linea", false);
    $this->db->from('csep_profesores_lineas cpl');
    $this->db->join('csep_profesores cp', 'cpl.id_profesor = cp.id');
    $this->db->join('personas p', 'cp.id_persona = p.id','left');
    $this->db->join('valor_parametro lin', 'cpl.id_linea = lin.id', 'left');
    $this->db->join('valor_parametro sub', 'cpl.id_sub_linea = sub.id', 'left');
    $this->db->where('cp.estado_registro', 1);
    $this->db->where('cp.periodo', $periodo);
    return $this->db->get()->result_array();
  }

  public function buscar_profesores_asignatura_excel($periodo)
  {
    $this->db->select("p.identificacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, asi.valor asignatura, cpa.creditos, cpa.cupo, dia.valor dia, cpa.grupo, cpa.horario, cpa.salon", false);
    $this->db->from('csep_profesor_asignatura cpa');
    $this->db->join('csep_profesores cp', 'cpa.id_profesor = cp.id');
    $this->db->join('personas p', 'cp.id_persona = p.id','left');
    $this->db->join('valor_parametro asi', 'cpa.id_asignatura = asi.id', 'left');
    $this->db->join('valor_parametro dia', 'cpa.id_dia = dia.id', 'left');
    $this->db->where('cp.estado_registro', 1);
    $this->db->where('cp.periodo', $periodo);
    return $this->db->get()->result_array();
  }

  public function buscar_profesores_atencion_excel($periodo)
  {
    $this->db->select("p.identificacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, dia.valor dia, cpat.hora_inicio, cpat.hora_fin, cpat.lugar, cpat.fecha_registra, cpat.fecha_elimina", false);
    $this->db->from('csep_profesor_atencion cpat');
    $this->db->join('csep_profesores cp', 'cpat.id_profesor = cp.id');
    $this->db->join('personas p', 'cp.id_persona = p.id','left');
    $this->db->join('valor_parametro dia', 'cpat.id_dia = dia.id', 'left');
    $this->db->where('cp.estado_registro', 1);
    $this->db->where('cp.periodo', $periodo);
    return $this->db->get()->result_array();
  }

  public function buscar_profesores_formacion_excel($periodo)
  {
    $this->db->select("p.identificacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, form.valor formacion, cpf.nombre", false);
    $this->db->from('csep_profesor_formacion cpf');
    $this->db->join('csep_profesores cp', 'cpf.id_profesor = cp.id');
    $this->db->join('personas p', 'cp.id_persona = p.id','left');
    $this->db->join('valor_parametro form', 'cpf.id_formacion = form.id', 'left');
    $this->db->where('cp.estado_registro', 1);
    $this->db->where('cp.periodo', $periodo);
    return $this->db->get()->result_array();
  }

  public function buscar_profesores_horas_excel($periodo)
  {
    $this->db->select("p.identificacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, pro.valor programa, hor.valor hora, cph.cantidad", false);
    $this->db->from('csep_profesor_horas cph');
    $this->db->join('csep_profesores cp', 'cph.id_profesor = cp.id');
    $this->db->join('personas p', 'cp.id_persona = p.id','left');
    $this->db->join('valor_parametro pro', 'cph.id_programa = pro.id', 'left');
    $this->db->join('valor_parametro hor', 'cph.id_hora = hor.id', 'left');
    $this->db->where('cp.estado_registro', 1);
    $this->db->where('cp.periodo', $periodo);
    return $this->db->get()->result_array();
  }

  public function buscar_profesores_indicadores_excel($periodo)
  {
    $this->db->select("p.identificacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, cpi.tipo, ind.valor indicador, cpi.estado_inicial, cpi.fecha_inicial, cpi.estado_final, cpi.fecha_final, cpi.estado_actual", false);
    $this->db->from('csep_profesor_indicadores cpi');
    $this->db->join('csep_profesores cp', 'cpi.id_profesor = cp.id');
    $this->db->join('personas p', 'cp.id_persona = p.id','left');
    $this->db->join('valor_parametro ind', 'cpi.id_indicador = ind.id', 'left');
    $this->db->where('cp.estado_registro', 1);
    $this->db->where('cp.periodo', $periodo);
    return $this->db->get()->result_array();
  }

  public function buscar_profesores_objetivos_excel($periodo)
  {
    $this->db->select("p.identificacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, cpo.objetivo", false);
    $this->db->from('csep_profesor_objetivos cpo');
    $this->db->join('csep_profesores cp', 'cpo.id_profesor = cp.id');
    $this->db->join('personas p', 'cp.id_persona = p.id','left');
    $this->db->where('cp.estado_registro', 1);
    $this->db->where('cp.periodo', $periodo);
    return $this->db->get()->result_array();
  }

  public function buscar_profesores_perfil_excel($periodo)
  {
    $this->db->select("p.identificacion, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, per.valor perfil, rol.valor rol, cpp.id_cobertura", false);
    $this->db->from('csep_profesor_perfil cpp');
    $this->db->join('csep_profesores cp', 'cpp.id_profesor = cp.id');
    $this->db->join('personas p', 'cp.id_persona = p.id','left');
    $this->db->join('valor_parametro per', 'cpp.id_perfil = per.id', 'left');
    $this->db->join('valor_parametro rol', 'cpp.id_rol = rol.id', 'left');
    $this->db->where('cp.estado_registro', 1);
    $this->db->where('cp.periodo', $periodo);
    return $this->db->get()->result_array();
  }

  public function listar_soportes($id_alterno, $tipo)
  {
    $this->db->select("cs.id,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, cs.nombre_real, cs.nombre_guardado, cs.fecha_registro", false);
    $this->db->from('csep_profesores_soportes cs');
    $this->db->join('personas p', 'cs.id_usuario_registra = p.id');
    $this->db->where('cs.estado', 1);
    $this->db->where('cs.id_alterno', $id_alterno);
    $this->db->where('cs.tipo', $tipo);
    return $this->db->get()->result_array();
  }

  public function obtener_periodo_actual()
	{ 
		$this->db->select("valor");
		$this->db->from("valor_parametro");
		$this->db->where('id_aux', 'Per_Act_Prf');
		$query = $this->db->get();
		$row = $query->row();
		return $row->valor;
    }

    public function obtener_periodos($id_persona, $tipo)
    {
      $this->db->select("cp.periodo id, cp.periodo nombre");
      $this->db->from('csep_profesores cp');
      $this->db->where('cp.estado_registro', 1);
      if($tipo == 2)$this->db->where('cp.id_persona', $id_persona);
      $this->db->group_by('cp.periodo');
      return $this->db->get()->result_array();
    }

    public function eliminar_firma($id_plan, $tipo_firma){
      $this->db->set('estado', '0', FALSE);
      $this->db->where("id_plan", $id_plan);
      $this->db->where("tipo", $tipo_firma);
      $this->db->update('csep_profesores_firmas');
      $error = $this->db->_error_message();
      return $error ? -1 : 1;
    }

}
