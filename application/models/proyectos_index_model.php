<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Modelo que se encarga de manejar la informacion de del modulo de presupuesto
 */
class proyectos_index_model extends CI_Model {

/**
 * Se encarga de guardar los datos que se le pasen por el controlador en la tabla indicada.
 * @param Array $data 
 * @param String $tabla 
 * @return Int
 */
    public function guardar_datos($data, $tabla,$tipo = 1)
    {
      if ($tipo == 2) {
        $this->db->insert_batch($tabla, $data);
      }else{
        $this->db->insert($tabla,$data);
      }
      $error = $this->db->_error_message(); 
      if ($error) {
        return -1;
      }
      return 1;
    }
/**
 * Se encarga de modificar los datos que se le pasen por el controlador en la tabla indicada.
 * @param Array $data 
 * @param String $tabla 
 * @param Int $id 
 * @return Int
 */
    public function modificar_datos($data, $tabla , $id)
    {
        $this->db->where('id', $id);
        $this->db->update($tabla, $data);
      $error = $this->db->_error_message(); 
      if ($error) {
        return -1;
      }
      return 1;
    }

    public function modificar_valor_parametro($data, $id_aux) {
      $this->db->where('id_aux', $id_aux);
      $this->db->update('valor_parametro', $data);
      $error = $this->db->_error_message(); 
      if ($error) {
        return -1;
      }
      return 1;
    }

    public function traer_valor_parametro ($id, $tipo = 1) {
      $this->db->select('*');
      $this->db->from('valor_parametro');
      if ($tipo == 1) {
        $this->db->where('id', $id);
      } else {
        $this->db->where('id_aux', $id);
      }
      $query = $this->db->get();
      return $query->row();
    }

    public function listar_comites($id = null,$tipo = 'get')
    {
      $usuario = $_SESSION['persona'];
      $perfil = $_SESSION['perfil'];
      $estado =  $perfil != 'Per_Admin' && $perfil != 'Per_Adm_index' && $perfil != 'Per_index' ? "(c.id_estado_comite = 'Com_Not' OR c.id_estado_comite = 'Com_Ter' )" : null;
      $this->db->select("c.*, IF(c.fecha_registra < '2020-01-01 00:00:00', 1, 0) antiguo,e.valor estado,(SELECT COUNT(pc.id) FROM comite_proyectos pc WHERE pc.id_comite = c.id AND pc.id_estado_proyecto <> 'Proy_Can') as total,CONCAT(p.nombre,' ', p.apellido, ' ',p.segundo_apellido) creado_por",false);
      $this->db->from('comites c');
      $this->db->join('valor_parametro e', 'c.id_estado_comite= e.id_aux');
      $this->db->join('personas p', 'c.usuario_registra= p.id');
      $this->db->where('c.estado', 1);
      $this->db->where('c.tipo', 'index');
      if(!is_null($id))$this->db->where('c.id', $id);
      if($tipo == 'list' && !is_null($estado))$this->db->where($estado);
      $this->db->_protect_identifiers = false;
      $this->db->order_by("FIELD (c.id_estado_comite,'Com_Ini','Com_Not','Com_Ter')");
      $this->db->_protect_identifiers = true;
      $query = $this->db->get();
      return $query->result_array();
    }

    public function listar_comites_cbx() {
      $this->db->select('c.id, c.nombre, c.id_estado_comite, vp.valor estado_comite');
      $this->db->from('comites c');
      $this->db->join('valor_parametro vp', 'c.id_estado_comite = vp.id_aux');
      $this->db->where('c.estado', 1);
      $this->db->where('c.tipo', 'index');
      $this->db->_protect_identifiers = false;
      $this->db->order_by("FIELD (c.id_estado_comite,'Com_Ini','Com_Not','Com_Ter')");
      $this->db->_protect_identifiers = true;
      $query = $this->db->get();
      return $query->result_array();
    }

    public function traer_comite_mas_reciente() {
      $this->db->select('c.*');
      $this->db->from('comites c');
      $this->db->where('c.id_estado_comite', 'Com_Ini');
      $this->db->where('c.estado', 1);
      $this->db->where('c.tipo', 'index');
      $this->db->where('c.fecha_cierre > ' . date("Y-m-d"));
      $this->db->order_by('c.id', 'desc');
      $this->db->limit(1);
      $query = $this->db->get();
      return $query->row();
    }

    public function buscar_postulante($where)
		{
			$this->db->select("p.identificacion,p.id,p.nombre,p.segundo_nombre,p.apellido,p.segundo_apellido,p.correo,p.id_tipo_identificacion,p.fecha_expedicion,p.lugar_expedicion,p.fecha_nacimiento,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,u2.valor tipo_identificacion", false);
			$this->db->from('personas p');
			$this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
			$this->db->where($where);
			$query = $this->db->get();
			return $query->result_array();
	}

    public function buscar_visitante($where)
		{
			$this->db->select("v.identificacion,v.id,v.nombre,v.segundo_nombre,v.apellido,v.segundo_apellido,v.correo,v.tipo_identificacion as id_tipo_identificacion,CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) as nombre_completo,u2.valor tipo_identificacion", false);
			$this->db->from('visitantes v');
			$this->db->join('valor_parametro u2', 'v.tipo_identificacion=u2.id');
			$this->db->where($where);
			$query = $this->db->get();
			return $query->result_array();
	}
    public function buscar_departamento($dato)
		{

      $query = $this->db->query("SELECT vp2.valor FROM valor_parametro vp2 WHERE (vp2.valor like 'VICERECTORIA%' OR vp2.valor like 'DPTO.%' OR vp2.valor = 'POSGRADO' OR vp2.valor = 'RECTORIA') AND vp2.idparametro = 3 AND vp2.estado = 1");
      $habilitados =  $query->result_array();
      $filtro = "'xxx'";
      foreach ($habilitados as $h) $filtro .= ","."'".$h["valor"]."'";
      $this->db->select("vp.id ,vp.valor,vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro,re.valor relacion",FALSE);
      $this->db->from('valor_parametro vp');
      $this->db->join('valor_parametro re', 'vp.valory = re.id','left');
      $this->db->where("vp.idparametro = 3 AND vp.estado = 1 AND vp.valory <> 2 AND vp.valor LIKE '%$dato%' AND vp.valor IN ($filtro)");
      $query = $this->db->get();
      return $query->result_array();
	}
  public function obtener_departamentos($buscar) {
    $this->db->select("vp.id ,vp.valor,vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro,re.valor relacion",FALSE);
    $this->db->from('valor_parametro vp');
    $this->db->join('valor_parametro re', 'vp.valory = re.id','left');
    $this->db->where("vp.idparametro = 3 AND vp.estado = 1 AND vp.valory = '$buscar'");
    $query = $this->db->get();
    return $query->result_array();
  }
  public function obtener_programas_departamento($id) {
    $this->db->select("pp.vp_secundario_id id, vp.valor");
    $this->db->from('permisos_parametros pp');
    $this->db->join('valor_parametro vp','pp.vp_secundario_id = vp.id');
    $this->db->where("pp.vp_principal_id = '$id'");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_valores_permisos($vp_principal, $idparametro, $tipo, $order = false){
    $this->db->select("pp.vp_secundario_id id, vp.id_aux, vp.valor, vp.valorx, vp.valory, vp.valorz, vp.valora, vp.valorb", false);
		$this->db->from("permisos_parametros pp");
    $this->db->join("valor_parametro vp", "vp.id = pp.vp_secundario_id AND vp.idparametro = $idparametro");
		if($tipo == 1){
			$this->db->where('pp.vp_principal_id', $vp_principal);
		} else {
			$this->db->where('pp.vp_principal', $vp_principal);
		} 
    $this->db->where('pp.estado', 1);
    if($order) $this->db->order_by('vp.valory * 1', 'asc');
    $query = $this->db->get();
    return $query->result_array();
}
public function validar_accion_proyecto ($tipo_proyecto, $estado_proyecto, $persona) {
  $this->db->select('IF(COUNT(*) > 0, 1, 0) as validar', false);
  $this->db->from('proyectos_index_persona pip');
  $this->db->join('proyectos_index_estados pie', 'pip.id = pie.id_actividad AND pie.gestion = 1 AND pie.estado = 1');
  $this->db->join('valor_parametro est', 'est.id = pie.id_estado');
  $this->db->where('pip.id_persona', $persona);
  $this->db->where('pip.id_tipo', $tipo_proyecto);
  $this->db->where('pip.estado', 1);
  $this->db->where('est.id_aux', $estado_proyecto);
  $query = $this->db->get();
  return $query->row()->validar;
}
public function traer_periodo_actual() {
  $this->db->select('vp.valor');
  $this->db->from('valor_parametro vp');
  $this->db->where("vp.id_aux = 'Per_Act_Prf'");
  $query = $this->db->get();
  return $query->row()->valor;
}
  public function listar_proyectos_usuario($id_usuario, $id = null, $tipo = null, $estado = null, $codigo_proyecto = null) {
    $perfil = $_SESSION['perfil'];
    $administra =  $perfil == 'Per_Admin';
    $this->db->select("pie.gestion, concat(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, p.identificacion, p.correo, ins.valor institucion, car.valor cargo, cp.*, tpr.valor nombre_tipo_proyecto, tpr.id_aux id_aux_tipo_proyecto, tre.valor nombre_tipo_recurso, est.valor estado_proyecto, vp.valor codigo_orden_sap, vp.valorx descripcion_orden_sap, vp2.valor centro_costo, vp2.valorx departamento_centro_costo,
                       (SELECT COUNT(*) FROM comite_proyectos cp2, comite_proyectos_solicitudes cps WHERE cp2.id = cp.id AND cp2.id = cps.id_proyecto AND cps.aprobado IS NULL AND cps.estado_registra = 1) solicitudes, (SELECT COUNT(*) FROM comite_proyectos cp2, comite_proyectos_solicitudes cps WHERE cp2.id = cp.id AND cp2.id = cps.id_proyecto AND cps.fecha_limite > NOW() AND cps.aprobado = 1 AND cps.estado_registra = 1) solicitudes_aprobadas", false);
    $this->db->from('comite_proyectos cp');
    $this->db->join('comite_proyectos_participantes cpp', "cpp.id_persona = cp.investigador and cpp.id_proyecto = cp.id", 'left');
    $this->db->join('personas p', 'p.id = cpp.id_persona', 'left');
    $this->db->join('valor_parametro est', 'cp.id_estado_proyecto = est.id_aux', 'left');
    $this->db->join("proyectos_index_persona pip","pip.id_tipo = cp.tipo_proyecto AND pip.estado = 1 AND pip.id_persona = $id_usuario", 'left');
    $this->db->join('proyectos_index_estados pie', 'pie.id_actividad = pip.id AND pie.id_estado = est.id AND pie.estado = 1', 'left');
    $this->db->join('proyectos_index_estados pied', 'pied.id_actividad = pip.id AND pied.id_estado = cp.id_departamento AND pied.estado = 1', 'left');
    $this->db->join('valor_parametro car', 'p.id_cargo_sap = car.id', 'left');
    $this->db->join('valor_parametro ins', 'cpp.id_institucion = ins.id', 'left');
    $this->db->join('valor_parametro tpr', 'cp.tipo_proyecto = tpr.id', 'left');
    $this->db->join('valor_parametro tre', 'cp.tipo_recurso = tre.id', 'left');
    $this->db->join('valor_parametro vp', 'cp.id_codigo_orden_sap = vp.id', 'left');
    $this->db->join('valor_parametro vp2', 'vp.valory = vp2.id', 'left');
    
    if ($id && !empty($id)) {
      $this->db->where('cp.id', $id);
    } else {
      if ($administra) {
        if (!$tipo && empty($tipo) && !$estado && empty($estado) && !$codigo_proyecto && empty($codigo_proyecto)) $this->db->where("cp.id_estado_proyecto NOT IN ('Proy_Neg', 'Proy_Can', 'Proy_Rec')");
      } else {
        $this->db->where("((cp.investigador = $id_usuario) OR (pip.id IS NOT NULL AND pie.id IS NOT NULL AND pied.id IS NOT NULL))");
      }

      if ($tipo && !empty($tipo)) $this->db->where('cp.tipo_proyecto', $tipo);
      if ($estado && !empty($estado)) $this->db->where('cp.id_estado_proyecto', $estado);
      if ($codigo_proyecto && !empty($codigo_proyecto)) $this->db->where("cp.codigo_proyecto LIKE '%$codigo_proyecto%'");
    }
    $this->db->where('cpp.tipo_tabla', 1);
    $this->db->where('cp.estado', 1);
    $this->db->_protect_identifiers = false;
    $this->db->order_by("FIELD (cp.id_estado_proyecto, 'Proy_For', 'Proy_Rev','Proy_Acp','Proy_Reg','Proy_Ban','Proy_Apr','Proy_Neg','Proy_Rec','Proy_Can')");
    $this->db->order_by("cp.fecha_registra");
    $this->db->_protect_identifiers = true;
    $query = $this->db->get();
    return $query->result_array();
  }
  public function informacion_completa_proyecto($id_proyecto) {
    $proyecto = $this->traer_proyecto($id_proyecto);
    $temp = $this->listar_proyecto_participantes($id_proyecto);
    $participantes = [];
    foreach ($temp as $aux) {
      $datos = $this->traer_informacion_participante($aux['id_persona'], $aux['tipo_tabla']);
      $aux['programa']    = $datos->programa ? $datos->programa : 'N/A';
      $aux['escalafon']   = $datos->escalafon ? $datos->escalafon : 'N/A';
      $aux['grupo']       = $datos->grupo ? $datos->grupo : 'N/A';
      $aux['contrato']    = $datos->contrato ? $datos->contrato : 'N/A';
      $aux['telefono']    = $datos->telefono ? $datos->telefono : 'N/A';
      $aux['correo']      = $datos->correo ? $datos->correo : 'N/A';
      $aux['vinculacion'] = $datos->vinculacion ? $datos->vinculacion : 'N/A';
      $aux['formacion']   = $datos->formacion ? $datos->formacion : 'N/A';
      $aux['usuario']     = $datos->usuario ? $datos->usuario : 'N/A';
      array_push($participantes, $aux);
    }
    $proyecto->{'participantes'}      = $participantes;
    $proyecto->{'instituciones'}      = $this->listar_instituciones($id_proyecto);
    $proyecto->{'programas'}          = $this->listar_proyecto_programas($id_proyecto, 1);
    $proyecto->{'asignaturas'}        = $this->listar_proyecto_asignaturas($id_proyecto, 1);
    $proyecto->{'lugares'}            = $this->listar_proyecto_lugares($id_proyecto);
    $proyecto->{'sublineas'}          = $this->listar_proyecto_sublineas($id_proyecto);
    $proyecto->{'ods'}                = $this->listar_proyecto_ods($id_proyecto);
    $proyecto->{'objetivos'}          = $this->listar_proyecto_objetivos($id_proyecto);
    $proyecto->{'impactos'}           = $this->obtener_valores_permisos($proyecto->tipo_proyecto, 173, 1);
    $proyecto->{'impactos_digitados'} = $this->listar_proyecto_impactos($id_proyecto);
    $temp = $this->listar_proyecto_productos($id_proyecto);
    $productos = [];
    foreach ($temp as $aux) {
      $aux['participantes'] = $this->listar_proyecto_productos_participantes($id_proyecto, $aux['id']);
      array_push($productos, $aux);
    }
    $proyecto->{'productos'} = $productos;
    $temp = $this->listar_proyecto_cronogramas($id_proyecto);
    $cronogramas = [];
    foreach ($temp as $aux) {
      $aux['participantes'] = $this->listar_proyecto_cronogramas_participantes($id_proyecto, $aux['id']);
      array_push($cronogramas, $aux);
    }
    $proyecto->{'cronogramas'} = $cronogramas;
    $proyecto->{'presupuestos'} = $this->informacion_completa_presupuestos($id_proyecto, $proyecto->tipo_proyecto);
    $temp = $this->informacion_completa_presupuesto_discriminado($id_proyecto);
    $proyecto->{'presupuesto_entidad'} = $temp['presupuesto_entidad'];
    $proyecto->{'presupuesto_entidad_rubro'} = $temp['presupuesto_entidad_rubro'];
    $proyecto->{'bibliografias'} = $this->listar_proyecto_bibliografias($id_proyecto);
    $proyecto->{'vicerrectores'} = $this->listar_vicerectores($proyecto->id_aux_tipo_proyecto);
    return $proyecto;
  }
  public function listar_proyecto_participantes($id_proyecto, $tipo = null, $id = null) {
    if ($tipo == 1) {
      $this->db->select("IF(cpp.tipo_tabla = 1, (SELECT concat(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) FROM personas p WHERE p.id = cpp.id_persona), (SELECT concat(v.nombre, ' ', v.apellido, ' ', v.segundo_apellido) FROM visitantes v WHERE v.id = cpp.id_persona)) nombre_completo, tpa.valor tipo_participante, ins.valor institucion", false);
    } else {
      $this->db->select("cpp.id, cpp.id_persona, IF(cpp.tipo_tabla = 1, (SELECT concat(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) FROM personas p WHERE p.id = cpp.id_persona), (SELECT concat(v.nombre, ' ', v.apellido, ' ', v.segundo_apellido) FROM visitantes v WHERE v.id = cpp.id_persona)) nombre_completo, IF(cpp.tipo_tabla = 1, (SELECT p.identificacion FROM personas p WHERE p.id = cpp.id_persona), (SELECT v.identificacion FROM visitantes v WHERE v.id = cpp.id_persona)) identificacion, cpp.id_tipo_participante, cpp.id_institucion, tpa.valor tipo_participante, tpa.id_aux id_aux_tipo_participante, ins.valor institucion, cpp.tipo_tabla", false);
    }
    $this->db->from('comite_proyectos_participantes cpp');
    $this->db->join('comite_proyectos cp', 'cpp.id_proyecto = cp.id', 'left');
    $this->db->join('valor_parametro tpa', 'cpp.id_tipo_participante = tpa.id', 'left');
    $this->db->join('valor_parametro ins', 'cpp.id_institucion = ins.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cpp.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cpp.id', $id);
    $query = $this->db->get();
    $temp = $query->result_array();
    return $temp;
  }
  public function traer_informacion_participante($id, $tipo_tabla) {
    $periodo_actual = $this->traer_periodo_actual();
    $this->db->select("IF($tipo_tabla = 1, (SELECT dep.valor FROM csep_profesores csp, valor_parametro dep WHERE csp.id_persona = $id and dep.id = csp.id_departamento and csp.estado_registro = 1 and csp.periodo = '$periodo_actual'), '') departamento,
                       IF($tipo_tabla = 1, (SELECT csp.id_departamento FROM csep_profesores csp WHERE csp.id_persona = $id and csp.estado_registro = 1 and csp.periodo = '$periodo_actual'), '') id_departamento,
                       IF($tipo_tabla = 1, (SELECT pro.valor FROM csep_profesores csp, valor_parametro pro WHERE csp.id_persona = $id and pro.id = csp.id_programa and csp.estado_registro = 1 and csp.periodo = '$periodo_actual'), (SELECT pro.valor FROM visitantes v, valor_parametro pro WHERE v.id = $id and pro.id = v.id_programa)) programa,
                       IF($tipo_tabla = 1, (SELECT csp.id_programa FROM csep_profesores csp WHERE csp.id_persona = $id and csp.estado_registro = 1 and csp.periodo = '$periodo_actual'), (SELECT v.id_programa FROM visitantes v WHERE v.id = $id )) id_programa,
                       IF($tipo_tabla = 1, (SELECT gru.valor FROM csep_profesores csp, valor_parametro gru WHERE csp.id_persona = $id and gru.id = csp.id_grupo and csp.estado_registro = 1 and csp.periodo = '$periodo_actual'), '') grupo,
                       IF($tipo_tabla = 1, (SELECT csp.id_grupo FROM csep_profesores csp WHERE csp.id_persona = $id and csp.estado_registro = 1 and csp.periodo = '$periodo_actual'), '') id_grupo,
                       IF($tipo_tabla = 1, (SELECT esc.valor FROM csep_profesores csp, valor_parametro esc WHERE csp.id_persona = $id and esc.id = csp.id_escalafon and csp.estado_registro = 1 and csp.periodo = '$periodo_actual'), '') escalafon,
                       IF($tipo_tabla = 1, (SELECT gru.valor FROM csep_profesores csp, valor_parametro gru WHERE csp.id_persona = $id and gru.id = csp.id_grupo and csp.estado_registro = 1 and csp.periodo = '$periodo_actual'), '') grupo,
                       IF($tipo_tabla = 1, (SELECT con.valor FROM csep_profesores csp, valor_parametro con WHERE csp.id_persona = $id and con.id = csp.id_dedicacion and csp.estado_registro = 1 and csp.periodo = '$periodo_actual'), '') vinculacion,
                       IF($tipo_tabla = 1, (SELECT p.telefono FROM personas p WHERE p.id = $id), (SELECT v.celular FROM visitantes v WHERE v.id = $id)) telefono,
                       IF($tipo_tabla = 1, (SELECT p.correo FROM personas p WHERE p.id = $id), (SELECT v.correo FROM visitantes v WHERE v.id = $id)) correo,
                       IF($tipo_tabla = 1, (SELECT con.valor FROM csep_profesores csp, valor_parametro con WHERE csp.id_persona = $id and con.id = csp.id_contrato and csp.estado_registro = 1 and csp.periodo = '$periodo_actual'), '') contrato,
                       IF($tipo_tabla = 1, (SELECT p.usuario FROM personas p WHERE p.id = $id), '') usuario,
                       IF($tipo_tabla = 1, (SELECT vp.valor FROM csep_profesor_formacion csp, valor_parametro vp WHERE csp.id_profesor = $id and csp.id_formacion = vp.id order by csp.id desc limit 1), '') formacion", false);
    $this->db->limit(1);
    $query = $this->db->get();
    return $query->row();
  }
  public function traer_participante_id($id_proyecto, $id_participante) {
    $this->db->select("IF(cpp.tipo_tabla = 1, (SELECT concat(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) FROM personas p WHERE p.id = cpp.id_persona), (SELECT concat(v.nombre, ' ', v.apellido, ' ', v.segundo_apellido) FROM visitantes v WHERE v.id = cpp.id_persona)) nombre_completo, IF(cpp.tipo_tabla = 1, (SELECT p.identificacion FROM personas p WHERE p.id = cpp.id_persona), (SELECT v.identificacion FROM visitantes v WHERE v.id = cpp.id_persona)) identificacion", false);
    $this->db->from('comite_proyectos_participantes cpp');
    $this->db->join('comite_proyectos cp', 'cpp.id_proyecto = cp.id', 'left');
    $this->db->where('cpp.id', $id_participante);
    $this->db->where('cp.id', $id_proyecto);
    $this->db->where('cp.estado', 1);
    $this->db->where('cpp.estado_registra', 1);
    $query = $this->db->get();
    return $query->row();
  }
  public function listar_instituciones_bdd($tipo = null) {
  $this->db->select('vp.id, vp.id_aux, vp.idparametro, vp.valor nombre, vp.valorx nit, vp.valory pais_origen, vp.valorb correo, vp.valorz telefono_contacto, vp.valora nombre_contacto');
  $this->db->from('valor_parametro vp');
  $this->db->where('vp.estado', 1);
  $this->db->where('vp.idparametro', '178');
  if ($tipo == 1) {
    $this->db->where("vp.valory NOT LIKE 'Colombia'");
  } elseif ($tipo == 2) {
    $this->db->where("vp.valory LIKE 'Colombia'");
  }
  $query = $this->db->get();
  return $query->result_array();
  }
  public function listar_proyecto_lugares($id_proyecto, $tipo = null, $id = null) {
    if ($tipo == 1) {
      $this->db->select('cpl.pais, cpl.ciudad');
    } else {
      $this->db->select('cpl.id, cpl.pais, cpl.ciudad');
    }
    $this->db->from('comite_proyectos_lugares cpl');
    $this->db->join('comite_proyectos cp', 'cpl.id_proyecto = cp.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cpl.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cpl.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_instituciones($id_proyecto, $tipo = null, $id = null) {
    if ($tipo == 1) {
      $this->db->select('ins.valor nombre_institucion, ins.valorb correo, ins.valorz telefonos, ins.valora persona_contacto, cpi.responsabilidad_contraparte, cpi.responsabilidad_cuc', false);
    } else {
      $this->db->select('cpi.id, cpi.id_institucion, ins.valor nombre_institucion, ins.valorb correo, ins.valorz telefonos, ins.valora persona_contacto, cpi.responsabilidad_contraparte, cpi.responsabilidad_cuc', false);
    }
    $this->db->from('comite_proyectos_instituciones cpi');
    $this->db->join('comite_proyectos cp', 'cpi.id_proyecto = cp.id', 'left');
    $this->db->join('valor_parametro ins', 'cpi.id_institucion = ins.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cpi.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cpi.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_programas($id_proyecto, $tipo = null, $id = null) {
    if ($tipo == 1) {
      $this->db->select('pro.valor programa, tip.valor tipo_interaccion', false);
    } else {
      $this->db->select('cpp.id, cpp.id_programa, pro.valor programa, cpp.id_tipo_interaccion, tip.valor tipo_interaccion', false);
    }
    $this->db->from('comite_proyectos_programas cpp');
    $this->db->join('comite_proyectos cp', 'cpp.id_proyecto = cp.id', 'left');
    $this->db->join('valor_parametro pro', 'cpp.id_programa = pro.id', 'left');
    $this->db->join('valor_parametro tip', 'cpp.id_tipo_interaccion = tip.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cpp.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cpp.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_asignaturas($id_proyecto, $tipo = null, $id = null) {
    if ($tipo == 1) {
      $this->db->select('cpa.asignatura', false);
    } else {
      $this->db->select('cpa.id, cpa.asignatura', false);
    }
    $this->db->from('comite_proyectos_asignaturas cpa');
    $this->db->join('comite_proyectos cp', 'cpa.id_proyecto = cp.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cpa.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cpa.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_sublineas($id_proyecto, $tipo = null, $id = null) {
    if ($tipo == 1) {
      $this->db->select('gru.valor grupo, lin.valor linea, sub.valor sub_linea');
    } else {
      $this->db->select('cps.id, cps.id_grupo, cps.id_linea, cps.id_sublinea, gru.valor grupo, lin.valor linea, sub.valor sub_linea');
    }
    $this->db->from('comite_proyectos_sublineas cps');
    $this->db->join('comite_proyectos cp', 'cps.id_proyecto = cp.id', 'left');
    $this->db->join('valor_parametro gru', 'cps.id_grupo = gru.id', 'left');
    $this->db->join('valor_parametro lin', 'cps.id_linea = lin.id', 'left');
    $this->db->join('valor_parametro sub', 'cps.id_sublinea = sub.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cps.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cps.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_ods($id_proyecto, $tipo = null, $id = null) {
    if ($tipo == 1) {
      $this->db->select('ods.valor ods');
    } else {
      $this->db->select('cpo.id, cpo.id_ods, ods.valor ods, ods.valorx ods_completo');
    }
    $this->db->from('comite_proyectos_ods cpo');
    $this->db->join('comite_proyectos cp', 'cpo.id_proyecto = cp.id', 'left');
    $this->db->join('valor_parametro ods', 'cpo.id_ods = ods.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cpo.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cpo.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_objetivos($id_proyecto, $tipo = null, $id = null) {
    if ($tipo == 1) {
      $this->db->select('cpo.tipo_objetivo, cpo.descripcion');
    } else {
      $this->db->select('cpo.id, cpo.tipo_objetivo, cpo.descripcion');
    }
    $this->db->from('comite_proyectos_objetivos cpo');
    $this->db->join('comite_proyectos cp', 'cpo.id_proyecto = cp.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cpo.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cpo.id', $id);
    $this->db->order_by('cpo.tipo_objetivo', 'asc');
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_impactos($id_proyecto, $tipo = null, $id = null){
    if ($tipo == 1) {
      $this->db->select('imp.valor tipo_impacto, cpi.descripcion');
    } else {
      $this->db->select('cpi.id, cpi.id_tipo_impacto, imp.valor tipo_impacto, cpi.descripcion');
    }
    $this->db->from('comite_proyectos_impactos cpi');
    $this->db->join('comite_proyectos cp', 'cpi.id_proyecto = cp.id', 'left');
    $this->db->join('valor_parametro imp', 'cpi.id_tipo_impacto = imp.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cpi.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cpi.id', $id);
    $this->db->order_by('tipo_impacto', 'asc');
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_productos($id_proyecto, $tipo = null, $id = null){
    if ($tipo == 1) {
      $this->db->select('cpp.id, cpp.observaciones, imp.valor tipo_producto, imp2.valor producto');
    } else {
      $this->db->select('cpp.id, cpp.id_tipo_producto, cpp.id_producto, cpp.observaciones, imp.valor tipo_producto, imp2.valor producto');
    }
    $this->db->from('comite_proyectos_productos cpp');
    $this->db->join('comite_proyectos cp', 'cpp.id_proyecto = cp.id', 'left');
    $this->db->join('valor_parametro imp', 'cpp.id_tipo_producto = imp.id', 'left');
    $this->db->join('valor_parametro imp2', 'cpp.id_producto = imp2.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cpp.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cpp.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_productos_participantes($id_proyecto, $id_producto){
    $this->db->select("cppp.id id_producto_participante, cppa.id, IF(cppa.tipo_tabla = 1, (SELECT concat(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) FROM personas p WHERE p.id = cppa.id_persona), (SELECT concat(v.nombre, ' ', v.apellido, ' ', v.segundo_apellido) FROM visitantes v WHERE v.id = cppa.id_persona)) nombre_completo, IF(cppa.tipo_tabla = 1, (SELECT p.identificacion FROM personas p WHERE p.id = cppa.id_persona), (SELECT v.identificacion FROM visitantes v WHERE v.id = cppa.id_persona)) identificacion", false);
    $this->db->from('comite_proyectos_productos_participantes cppp');
    $this->db->join('comite_proyectos_productos cpp', 'cppp.id_producto_proyecto = cpp.id', 'left');
    $this->db->join('comite_proyectos cp', 'cpp.id_proyecto = cp.id', 'left');
    $this->db->join('comite_proyectos_participantes cppa', 'cppa.id = cppp.id_participante', 'left');
    $this->db->where('cp.estado', 1);
    $this->db->where('cpp.estado_registra', 1);
    $this->db->where('cppp.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    $this->db->where('cpp.id', $id_producto);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_cronogramas($id_proyecto, $tipo = null, $id = null){
    if ($tipo == 1) {
      $this->db->select('cpc.id, cpo.descripcion objetivo_especifico, cpc.fecha_inicial, cpc.fecha_final, cpc.actividad');
    } else {
      $this->db->select('cpc.id, cpc.id_objetivo_especifico, cpo.descripcion obj_especifico, cpc.fecha_inicial, cpc.fecha_final, cpc.actividad');
    }
    $this->db->from('comite_proyectos_cronogramas cpc');
    $this->db->join('comite_proyectos cp', 'cpc.id_proyecto = cp.id', 'left');
    $this->db->join('comite_proyectos_objetivos cpo', 'cpc.id_objetivo_especifico = cpo.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cpc.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cpc.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_cronogramas_participantes($id_proyecto, $id_cronograma){
    $this->db->select("cpcp.id id_cronograma_participante, cpp.id, IF(cpp.tipo_tabla = 1, (SELECT concat(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) FROM personas p WHERE p.id = cpp.id_persona), (SELECT concat(v.nombre, ' ', v.apellido, ' ', v.segundo_apellido) FROM visitantes v WHERE v.id = cpp.id_persona)) nombre_completo, IF(cpp.tipo_tabla = 1, (SELECT p.identificacion FROM personas p WHERE p.id = cpp.id_persona), (SELECT v.identificacion FROM visitantes v WHERE v.id = cpp.id_persona)) identificacion", false);
    $this->db->from('comite_proyectos_cronogramas_participantes cpcp');
    $this->db->join('comite_proyectos_cronogramas cpc', 'cpcp.id_cronograma = cpc.id', 'left');
    $this->db->join('comite_proyectos cp', 'cpc.id_proyecto = cp.id', 'left');
    $this->db->join('comite_proyectos_participantes cpp', 'cpcp.id_participante = cpp.id', 'left');
    $this->db->where('cp.estado', 1);
    $this->db->where('cpc.estado_registra', 1);
    $this->db->where('cpcp.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    $this->db->where('cpc.id', $id_cronograma);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_presupuestos($id_proyecto, $id_presupuesto = null) {
    // Se trae los presupuestos con el valor total calculado, con el tipo de valor y el id del tipo de valor
    // Si se le pasa el id del presupuesto, filtrará por ese tipo de presupuesto, sino, los traerá todos
    $this->db->select("cpp.id, cpp.id_tipo_presupuesto, tip.valor tipo_presupuesto,
                      (select cppd2.valor from comite_proyectos_presupuestos_datos cppd2, comite_proyectos_presupuestos cpp2 where cpp2.id = cpp.id and cppd2.id_aux_dato = 'Pre_Val_Uni' and cppd2.id_presupuesto = cpp.id) valor_unitario,
                      (select tval.valor from comite_proyectos_presupuestos_datos cppd2, comite_proyectos_presupuestos cpp2, valor_parametro tval where cpp2.id = cpp.id and cppd2.id_presupuesto = cpp.id and tval.id = cppd2.valor and cppd2.id_aux_dato = 'Pre_Tipo_Val') tipo_valor,
                      (select tval.id_aux from comite_proyectos_presupuestos_datos cppd2, comite_proyectos_presupuestos cpp2, valor_parametro tval where cpp2.id = cpp.id and cppd2.id_presupuesto = cpp.id and tval.id = cppd2.valor and cppd2.id_aux_dato = 'Pre_Tipo_Val') id_tipo_valor,
                      (IF((select cppd2.valor from comite_proyectos_presupuestos_datos cppd2, comite_proyectos_presupuestos cpp2 where cpp2.id = cpp.id and cppd2.multiplica = 1 and cppd2.id_presupuesto = cpp.id) = 0, 0, (select round(EXP(SUM(LOG(cppd2.valor))),0) from comite_proyectos_presupuestos_datos cppd2, comite_proyectos_presupuestos cpp2 where cpp2.id = cpp.id and cppd2.multiplica in (1, 2) and cppd2.id_presupuesto = cpp.id))) valor_total", false);
    $this->db->from('comite_proyectos_presupuestos cpp');
    $this->db->join('comite_proyectos cp', 'cpp.id_proyecto = cp.id', 'left');
    $this->db->join('valor_parametro tip', 'cpp.id_tipo_presupuesto = tip.id', 'left');
    $this->db->where('cp.estado', 1);
    $this->db->where('cpp.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id_presupuesto) $this->db->where('cpp.id_tipo_presupuesto', $id_presupuesto);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_presupuestos_datos($id_proyecto, $id, $tipo = null) {
    $this->db->select('cppd.id, cppd.id_aux_dato, cppd.valor, vr.valor valor_select, cppd.nombre_dato, cppd.tipo_dato, cppd.dato_requerido, cppd.id_datos, cppd.multiplica');
    $this->db->from('comite_proyectos_presupuestos cpp');
    $this->db->join('comite_proyectos cp', 'cpp.id_proyecto = cp.id', 'left');
    $this->db->join('comite_proyectos_presupuestos_datos cppd', 'cpp.id = cppd.id_presupuesto', 'left');
    $this->db->join('valor_parametro vr', 'cppd.valor = vr.id', 'left');
    $this->db->where('cp.estado', 1);
    if (!$tipo) {
      $this->db->where('cpp.estado_registra', 1);
      $this->db->where('cppd.estado_registra', 1);
    }
    $this->db->where('cp.id', $id_proyecto);
    $this->db->where('cpp.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_campos_presupuestos($id_proyecto, $tipo_presupuesto) {
    $this->db->select('cppd.nombre_dato');
    $this->db->from('comite_proyectos_presupuestos_datos cppd');
    $this->db->join('comite_proyectos_presupuestos cpp', 'cpp.id = cppd.id_presupuesto');
    $this->db->join('comite_proyectos cp', 'cp.id = cpp.id_proyecto');
    $this->db->where('cp.id', $id_proyecto);
    $this->db->where('cpp.id_tipo_presupuesto', $tipo_presupuesto);
    $this->db->where('cppd.estado_registra', 1);
    $this->db->where('cpp.estado_registra', 1);
    $this->db->where('cp.estado', 1);
    $this->db->group_by('cppd.nombre_dato');
    $this->db->order_by('cppd.id');
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_presupuesto_discriminado($id_proyecto, $tipo=0) {
    $this->db->select("(IF((select vp2.valor from comite_proyectos_presupuestos_datos cpp2, valor_parametro vp2 where cpp.id = cpp2.id_presupuesto and cpp2.valor = vp2.id and cpp2.id_aux_dato = 'Pre_Ent_Res' and cpp2.estado_registra = 1 and vp2.estado = 1) IS NULL, (select vp.valor from valor_parametro vp where vp.id_aux = 'Pro_Ins_CUC'), (select vp2.valor from comite_proyectos_presupuestos_datos cpp2, valor_parametro vp2 where cpp.id = cpp2.id_presupuesto and cpp2.valor = vp2.id and cpp2.id_aux_dato = 'Pre_Ent_Res' and cpp2.estado_registra = 1 and vp2.estado = 1))) entidad_responsable,
                       (select vp2.id_aux from comite_proyectos_presupuestos_datos cpp2, valor_parametro vp2 where cpp.id = cpp2.id_presupuesto and cpp2.valor = vp2.id and cpp2.id_aux_dato = 'Pre_Ent_Res' and cpp2.estado_registra = 1 and vp2.estado = 1) id_aux_entidad_responsable,
                       vp.valor rubro,
                       (select vp2.id_aux from comite_proyectos_presupuestos_datos cpp2, valor_parametro vp2 where cpp.id = cpp2.id_presupuesto and cpp2.valor = vp2.id and cpp2.id_aux_dato = 'Pre_Tipo_Val' and cpp2.estado_registra = 1 and vp2.estado = 1) tipo_valor,
                       (IF((select cpp2.valor from comite_proyectos_presupuestos_datos cpp2 where cpp.id = cpp2.id_presupuesto and cpp2.multiplica = 1 and cpp2.estado_registra = 1) = 0, 0, (select round(EXP(SUM(LOG(cpp2.valor))),0) from comite_proyectos_presupuestos_datos cpp2 where cpp.id = cpp2.id_presupuesto and cpp2.multiplica in (1, 2) and cpp2.estado_registra = 1))) valor_total", false);
    $this->db->from('comite_proyectos cp');
    $this->db->join('comite_proyectos_presupuestos cpp', 'cpp.id_proyecto = cp.id');
    $this->db->join('valor_parametro vp', 'vp.id = cpp.id_tipo_presupuesto');
    $this->db->where('cp.estado', 1);
    $this->db->where('cpp.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($tipo == 1) {
      $this->db->group_by('entidad_responsable');
    } else if ($tipo == 2) {
      $this->db->group_by('entidad_responsable, rubro');
    }
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_presupuesto_discriminado_financiacion($id_proyecto) {
    $this->db->select("(select vp.valor from comite_proyectos_presupuestos_datos cpp2, valor_parametro vp where cpp.id = cpp2.id_presupuesto and cpp2.valor = vp.id and cpp2.id_aux_dato = 'Pre_Tipo_Fin' and cpp2.estado_registra = 1 and vp.estado = 1) tipo_financiacion,
                       (select vp.valor from comite_proyectos_presupuestos_datos cpp2, valor_parametro vp where cpp.id = cpp2.id_presupuesto and cpp2.valor = vp.id and cpp2.id_aux_dato in ('Pre_Fin_Int', 'Pre_Fin_Nac') and vp.estado = 1) financiacion,
                       (select vp.id_aux from comite_proyectos_presupuestos_datos cpp2, valor_parametro vp where cpp.id = cpp2.id_presupuesto and cpp2.valor = vp.id and cpp2.id_aux_dato = 'Pre_Tipo_Val' and cpp2.estado_registra = 1 and vp.estado = 1 ) tipo_valor,
                       (IF((select cpp2.valor from comite_proyectos_presupuestos_datos cpp2 where cpp.id = cpp2.id_presupuesto and cpp2.multiplica = 1 and cpp2.estado_registra = 1) = 0, 0, (select round(EXP(SUM(LOG(cpp2.valor))),0) from comite_proyectos_presupuestos_datos cpp2 where cpp.id = cpp2.id_presupuesto and cpp2.multiplica in (1, 2) and cpp2.estado_registra = 1))) valor_total", false);
    $this->db->from('comite_proyectos cp');
    $this->db->join('comite_proyectos_presupuestos cpp', 'cpp.id_proyecto = cp.id');
    $this->db->where('cp.estado', 1);
    $this->db->where('cpp.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_soportes($id_proyecto, $tipo = null, $id = null) {
    if ($tipo == 1) {
      $this->db->select("cps.nombre_real, cps.nombre_guardado", false);
    } else {
      $this->db->select("cps.id, cps.nombre_real, cps.nombre_guardado, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre_completo, cps.fecha_registra", false);
    }
    $this->db->from('comite_proyectos_soportes cps');
    $this->db->join('comite_proyectos cp', 'cps.id_proyecto = cp.id', 'left');
    $this->db->join('personas p', 'cp.investigador = p.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cps.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cps.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyecto_bibliografias($id_proyecto, $tipo = null, $id = null) {
    if ($tipo == 1) {
      $this->db->select('cpb.bibliografia');
    } else {
      $this->db->select('cpb.id, cpb.bibliografia');
    }
    $this->db->from('comite_proyectos_bibliografias cpb');
    $this->db->join('comite_proyectos cp', 'cpb.id_proyecto = cp.id', 'left');
    $this->db->where('cp.estado', 1);
    if ($tipo != 1) $this->db->where('cpb.estado_registra', 1);
    $this->db->where('cp.id', $id_proyecto);
    if ($id) $this->db->where('cpb.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_motivos_solicitud($id_proyecto, $tipo = 1) {
    $this->db->select('cps.id, cps.grupo_solicitud, cps.fecha_limite, cps.razones, cps.aprobado, vp.id vp_id, vp.valor, vp.valorz, vp.valora');
    $this->db->from('comite_proyectos_solicitudes cps');
    $this->db->join('comite_proyectos cp', 'cps.id_proyecto = cp.id');
    $this->db->join('valor_parametro vp', 'cps.id_item = vp.id');
    $this->db->where('cp.id', $id_proyecto);
    if ($tipo == 1) {
      $this->db->where('cps.aprobado IS NULL');
    } else {
      $this->db->where('cps.fecha_limite > NOW()');
      $this->db->where('cps.aprobado', 1);
    }
    $this->db->where('cp.estado', 1);
    $this->db->where('cps.estado_registra', 1);
    $this->db->where('vp.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_items_motivos($id_proyecto) {
    $this->db->select('vp.*');
    $this->db->from('valor_parametro vp');
    $this->db->join('permisos_parametros pp', 'pp.vp_secundario_id = vp.id');
    $this->db->join('valor_parametro vp2', 'pp.vp_principal_id = vp2.id');
    $this->db->join('comite_proyectos cp', 'cp.tipo_proyecto = vp2.id');
    $this->db->where('vp.estado', 1);
    $this->db->where('vp.idparametro', 184);
    $this->db->where('cp.id', $id_proyecto);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_vicerectores($id_tipo_proyecto) {
    $this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre_completo, vp.valor cargo", false);
    $this->db->from('personas p');
    $this->db->join('cargos_departamentos cd', 'cd.id = p.id_cargo', 'left');
    $this->db->join('valor_parametro vp', 'vp.id = cd.id_cargo', 'left');
    $this->db->join('valor_parametro vp2', 'vp2.id = cd.id_departamento', 'left');
    if ($id_tipo_proyecto == 'Pro_Ges') {
      $this->db->where('vp.id_aux', 'Car_Vic_Adm');
      $this->db->where('vp2.id_aux', 'Dep_Vic_Adm');
    } else if ($id_tipo_proyecto == 'Pro_Ext') {
      $this->db->where('vp.id_aux', 'Car_Vic_Ext');
      $this->db->where('vp2.id_aux', 'Dep_Vic_Ext');
    } else if ($id_tipo_proyecto == 'Pro_Lab') {
      $this->db->where("vp.id_aux IN ('Car_Vic_Acad', 'Car_Vic_Inve')");
      $this->db->where("vp2.id_aux IN ('Dep_Vic_Aca', 'Dep_Vic_Inv')");
    } else if ($id_tipo_proyecto == 'Pro_Bien') {
      $this->db->where('vp.id_aux', 'Car_Vic_Bien');
      $this->db->where('vp2.id_aux', 'Dep_Vic_Bien');
    } else if ($id_tipo_proyecto == 'Pro_Doc') {
      $this->db->where('vp.id_aux', 'Car_Vic_Acad');
      $this->db->where('vp2.id_aux', 'Dep_Vic_Aca');
    } else if ($id_tipo_proyecto == 'Pro_Inv') {
      $this->db->where('vp.id_aux', 'Car_Vic_Inve');
      $this->db->where('vp2.id_aux', 'Dep_Vic_Inv');
    } else if ($id_tipo_proyecto == 'Pro_Int') {
      $this->db->where('vp.id_aux', 'Car_Dir_Int');
      $this->db->where('vp2.id_aux', 'Dep_Vic_Ext');
    } else if ($id_tipo_proyecto == 'Pro_Gra') {
      $this->db->where('vp.id_aux', 'Car_Vic_Acad');
      $this->db->where('vp2.id_aux', 'Dep_Vic_Aca');
    }
    
    $this->db->where('p.estado', 1);
    $this->db->where('vp.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_proyectos($id_comite, $persona, $estado = null, $id_departamento = null, $id_programa = null, $nombre_grupo = null, $tipo_proyecto = null, $tipo_recurso = null, $estado_proyecto = null, $codigo_proyecto = null){
    $perfil = $_SESSION['perfil'];
    $this->db->select("c.fecha_cierre,cp.*, dep.valor departamento, pro.valor programa, gru.valor grupo, vs.valor nombre_tipo_proyecto, vs.id_aux id_tipo_proyecto,vq.valor tipo_recurso_name, CONCAT(p.nombre,' ', p.apellido, ' ',p.segundo_apellido) investigador_name,c.id_estado_comite,(SELECT app.id FROM accion_proyectos_personas app WHERE app.id_proyecto = cp.id AND app.id_usuario_registro = $persona AND app.id_tipo IN ('Proy_Apr', 'Proy_Neg') AND app.estado = 1 ORDER by app.id DESC limit 1) as gestionado,(SELECT COUNT(appa.id) as total FROM accion_proyectos_personas appa WHERE appa.id_proyecto = cp.id AND appa.id_tipo = 'Proy_Apr' AND appa.estado = 1) as aprobados,(SELECT COUNT(appa.id) as total FROM accion_proyectos_personas appa WHERE appa.id_proyecto = cp.id AND appa.id_tipo = 'Proy_Neg' AND appa.estado = 1) as negados, est.id_aux id_estado_proyecto, est.valor estado_proyecto", false);
    $this->db->from('comite_proyectos cp');
    $this->db->join('comites c','cp.id_comite = c.id', 'left');
    $this->db->join('valor_parametro dep','cp.id_departamento = dep.id', 'left');
    $this->db->join('valor_parametro pro','cp.id_programa = pro.id', 'left');
    $this->db->join('valor_parametro gru','cp.nombre_grupo = gru.id', 'left');
    $this->db->join('valor_parametro vs','cp.tipo_proyecto = vs.id', 'left');
    $this->db->join('valor_parametro vq','cp.tipo_recurso = vq.id', 'left');
    $this->db->join('valor_parametro est','cp.id_estado_proyecto= est.id_aux', 'left');
    $this->db->join('personas p', 'cp.investigador = p.id', 'left');
    if ($perfil == 'Per_Adm_index' || $perfil == 'Per_index' || $perfil == 'Per_Adm_Proy')$this->db->join("proyectos_index_persona pip","pip.id_tipo = cp.tipo_proyecto AND pip.estado = 1 AND pip.id_persona = $persona", 'left');
    if(!is_null($estado))$this->db->where("($estado)");
    if(!empty($id_departamento)) $this->db->where("cp.id_departamento", $id_departamento);
    if(!empty($id_programa)) $this->db->where("cp.id_programa", $id_programa);
    if(!empty($nombre_grupo)) $this->db->where("cp.nombre_grupo", $nombre_grupo);
    if(!empty($tipo_proyecto)) $this->db->where("cp.tipo_proyecto", $tipo_proyecto);
    if(!empty($tipo_recurso)) $this->db->where("cp.tipo_recurso", $tipo_recurso);
    if(!empty($estado_proyecto)) $this->db->where("cp.id_estado_proyecto", $estado_proyecto);
    if(!empty($codigo_proyecto)) $this->db->where("cp.codigo_proyecto LIKE '%$codigo_proyecto%'");
    $this->db->where("cp.id_comite = '$id_comite'");
    $this->db->where("cp.id_estado_proyecto <> 'Proy_Can'");
    $this->db->where('cp.estado', 1);
    $this->db->order_by("gestionado");
    $this->db->order_by("dep.valor");
    $query = $this->db->get();
    return $query->result_array();
  }


  public function listar_estados_proyecto($id){
    $this->db->select("vp.valor, app.id_tipo, CONCAT(p.nombre,' ', p.apellido, ' ',p.segundo_apellido) persona,app.*", false);
    $this->db->from('accion_proyectos_personas app');
    $this->db->join('personas p', 'app.id_usuario_registro = p.id');
    $this->db->join('valor_parametro vp','app.id_tipo = vp.id_aux');
    $this->db->where("app.id_proyecto",$id);
    $this->db->where('app.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function get_proyecto_id($id, $persona = null){
    $this->db->select("cp.*,c.id_estado_comite,(SELECT app.id FROM accion_proyectos_personas app WHERE app.id_proyecto = cp.id AND app.id_usuario_registro = $persona AND app.estado = 1 ORDER by app.id DESC  limit 1) as gestionado", false);
    $this->db->from('comite_proyectos cp');
    $this->db->join('comites c','cp.id_comite = c.id');
    $this->db->join('personas p', 'cp.investigador = p.id');    
    $this->db->where("cp.id = '$id'");
    $this->db->where('cp.estado', 1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function traer_datos_parametros_generales() {
    $this->db->select('vp.*');
    $this->db->from('valor_parametro vp');
    $this->db->where("vp.id_aux IN ('Por_Iva')");
    $this->db->where('vp.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_personas_index() 
	{
		$this->db->select("p.id,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,p.cod_encargado aprueba",FALSE);
		$this->db->from('actividades_personas ap');
		$this->db->join('personas p', 'p.id = ap.id_persona');
		$this->db->join('personas_aprueban_index api', 'ap.id_persona = api.id_persona AND api.estado = 1','left');
		$this->db->where("ap.id_actividad",'comite_index');
		$this->db->where("api.id IS NULL");
		$query = $this->db->get();
		return $query->result_array();
  }
  public function persona_asignada_aprueba($persona) 
	{
		$this->db->select("*");
		$this->db->from('personas_aprueban_index pc');
		$this->db->where("pc.id_persona",$persona);
		$this->db->where("pc.estado",1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
  }
  public function personas_aprueban_index() 
	{
		$this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,p.identificacion,ap.*",FALSE);
		$this->db->from('personas_aprueban_index ap');
    $this->db->join('personas p', 'p.id = ap.id_persona');
    $this->db->where("ap.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
  }

  public function listar_personas_adm_index() {
		$this->db->select("p.id,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona, p.correo",FALSE);
		$this->db->from('personas p');
		$this->db->where("p.id_perfil IN ('Per_Adm_index', 'Per_index', 'Per_Adm_Proy')");
		$query = $this->db->get();
		return $query->result_array();
  }

  public function listar_actividades($persona) {
    $this->db->select("vp.id, vp.valor as nombre, pip.id as asignado");
    $this->db->from('valor_parametro vp');
    $this->db->join('proyectos_index_persona pip', "vp.id = pip.id_tipo AND pip.id_persona = $persona AND pip.estado = 1", 'left');
    $this->db->where('vp.idparametro', 76);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function validar_asignacion_actividad($id, $persona) {
    $this->db->select("IF(COUNT(*) > 0, 0, 1) asignado", false);
    $this->db->from('proyectos_index_persona');
    $this->db->where('id_tipo', $id);
    $this->db->where('id_persona', $persona);
    $this->db->where('estado', 1);
    $query = $this->db->get();
    return $query->row()->asignado;
  }

  public function listar_estados($actividad) {
		$query = $this->db->query("(
			SELECT CONCAT(UCASE(LEFT(p.nombre, 1)), SUBSTRING(p.nombre, 2)) AS parametro, vp.id AS estado, vp.valor AS nombre, pie.id AS asignado, pie.gestion, pie.notificacion
      FROM proyectos_index_persona pip
      LEFT JOIN valor_parametro vp ON vp.idparametro IN (79, 91)
      LEFT JOIN parametros p ON p.id = vp.idparametro
      LEFT JOIN proyectos_index_estados pie ON vp.id = pie.id_estado AND pip.id = pie.id_actividad AND pie.estado = 1
      WHERE pip.id = $actividad
      ORDER BY p.nombre, vp.id
		)");
    return $query->result_array();
  }
  
  public function validar_asignacion_estado($estado, $actividad, $persona){
    $query = $this->db->query("
      SELECT IF(COUNT(pip.id) > 0, 0, 1) asignado
      FROM proyectos_index_persona pip
      INNER JOIN proyectos_index_estados pie ON pie.estado = 1 AND pie.id_actividad = $actividad AND pie.id_estado = $estado
      WHERE pip.id_persona = $persona
      AND pip.estado = 1
    ");
    return $query->row()->asignado;
	}

  public function aprobar_proyectos_masivo($id_comite,$usuario)
	{
      $sql = "INSERT INTO accion_proyectos_personas (`id_proyecto`, `id_tipo`, `id_usuario_registro`, `observaciones`)
      SELECT cp.id,'Proy_Apr',$usuario,'Aprobado por que Cuenta con los avales mínimos requeridos.' FROM comite_proyectos cp  WHERE cp.id_comite = $id_comite AND cp.id_estado_proyecto = 'Proy_Reg'
      AND (SELECT COUNT(appa.id) as total FROM accion_proyectos_personas appa INNER JOIN personas_aprueban_index pai ON pai.id_persona = appa.id_usuario_registro WHERE appa.id_proyecto = cp.id AND appa.id_tipo = 'Proy_Apr' AND appa.estado = 1) = (SELECT COUNT(pai.id) FROM personas_aprueban_index pai WHERE pai.estado = 1)";
      $this->db->query($sql);
      $n = $this->db->affected_rows();
      if ($n > 0) {
          $sql = "UPDATE comite_proyectos cp  SET cp.id_estado_proyecto = 'Proy_Apr' 
          WHERE cp.id_comite = $id_comite AND cp.id_estado_proyecto = 'Proy_Reg' AND (SELECT COUNT(appa.id) as total FROM accion_proyectos_personas appa INNER JOIN personas_aprueban_index pai ON pai.id_persona = appa.id_usuario_registro WHERE appa.id_proyecto = cp.id AND appa.id_tipo = 'Proy_Apr' AND appa.estado = 1) = (SELECT COUNT(pai.id) FROM personas_aprueban_index pai WHERE pai.estado = 1)";
          $this->db->query($sql);
          $n = $this->db->affected_rows();
      }
		return $n;
  }

  public function mostrar_notificaciones_proyectos($id_usuario) {
    $this->db->select("app.*, cp.nombre_proyecto, concat(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo", false);
    $this->db->from('accion_proyectos_personas app');
    $this->db->join('comite_proyectos cp', 'app.id_proyecto = cp.id', 'left');
    $this->db->join('personas p', 'app.id_usuario_registro = p.id', 'left');
    $this->db->where('cp.investigador', $id_usuario);
    $this->db->where("app.id_tipo IN ('Proy_For', 'Proy_Can')");
    $this->db->where('app.visto', 0);
    $this->db->where('app.observaciones IS NOT NULL');
    $this->db->where('app.estado', 1);
    $this->db->order_by('app.fecha_registro', 'desc');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function mostrar_notificaciones_solicitudes($tipo = 1, $id_usuario = null) {
    $this->db->select("app.*, cp.nombre_proyecto, concat(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo", false);
    $this->db->from('accion_proyectos_personas app');
    $this->db->join('comite_proyectos cp', 'app.id_proyecto = cp.id', 'left');
    $this->db->join('personas p', 'app.id_usuario_registro = p.id', 'left');
    if ($tipo == 1) {
      $this->db->where("app.id_tipo = 'Proy_Sol'");
    } else {
      $this->db->where("app.id_tipo IN ('Proy_Sol_Apr', 'Proy_Sol_Neg')");
      $this->db->where('cp.investigador', $id_usuario);
    }
    $this->db->where('app.visto', 0);
    $this->db->where('app.estado', 1);
    $this->db->order_by('app.fecha_registro', 'desc');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_proyecto_id($id){
    $persona = $_SESSION['persona'];
    $this->db->select("c.fecha_cierre,cp.*, vp.valor departamento2, vpp.valor programa2, vr.valor nombre_grupo_name2,vs.valor nombre_tipo_proyecto,vq.valor tipo_recurso_name, CONCAT(p.nombre,' ', p.apellido, ' ',p.segundo_apellido) investigador_name,c.id_estado_comite,(SELECT app.id FROM accion_proyectos_personas app WHERE app.id_proyecto = cp.id AND app.id_usuario_registro = $persona AND app.estado = 1 ORDER by app.id DESC  limit 1) as gestionado,(SELECT COUNT(appa.id) as total FROM accion_proyectos_personas appa WHERE appa.id_proyecto = cp.id AND appa.id_tipo = 'Proy_Apr' AND appa.estado = 1) as aprobados,(SELECT COUNT(appa.id) as total FROM accion_proyectos_personas appa WHERE appa.id_proyecto = cp.id AND appa.id_tipo = 'Proy_Neg' AND appa.estado = 1) as negados,est.valor estado_proyecto", false);
    $this->db->from('comite_proyectos cp');
    $this->db->join('comites c','cp.id_comite = c.id');
    $this->db->join('valor_parametro vp','cp.id_departamento = vp.id');
    $this->db->join('valor_parametro vpp','cp.id_programa = vpp.id');
    $this->db->join('valor_parametro vr','cp.nombre_grupo = vr.id');
    $this->db->join('valor_parametro vs','cp.tipo_proyecto = vs.id');
    $this->db->join('valor_parametro vq','cp.tipo_recurso = vq.id');
    $this->db->join('valor_parametro est','cp.id_estado_proyecto= est.id_aux');
    $this->db->join('personas p', 'cp.investigador = p.id');
    $this->db->where("cp.id",$id);
    $this->db->where('cp.estado', 1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
  }

  public function traer_ultimo_postulante_usuario($persona)
	{ 
		$this->db->select("identificacion,id,nombre,segundo_nombre,apellido,segundo_apellido,correo,id_tipo_identificacion,fecha_expedicion,lugar_expedicion,fecha_nacimiento,CONCAT(nombre,' ',apellido,' ',segundo_apellido) as nombre_completo",FALSE);
		$this->db->from("personas");
		$this->db->order_by("id", "desc");
		$this->db->where('usuario_registra', $persona);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
    }
  
  public function traer_ultimo_proyecto_usuario($persona) {
		$this->db->select("cp.*, concat(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, concat(v.nombre, ' ', v.apellido, ' ', v.segundo_apellido) as nombre_completo_responsable_externo, tpr.valor nombre_tipo_proyecto, tre.valor nombre_tipo_recurso, est.valor estado_proyecto",FALSE);
    $this->db->from("comite_proyectos cp");
    $this->db->join('personas p', 'p.id = cp.investigador', 'left');
    $this->db->join('visitantes v', 'v.id = cp.id_responsable_externo', 'left');
    $this->db->join('valor_parametro tpr', 'cp.tipo_proyecto = tpr.id', 'left');
    $this->db->join('valor_parametro tre', 'cp.tipo_recurso = tre.id', 'left');
    $this->db->join('valor_parametro est', 'cp.id_estado_proyecto = est.id_aux', 'left');
    $this->db->where('cp.estado', 1);
    $this->db->where('cp.id_estado_proyecto', 'Proy_For');
		$this->db->where('cp.investigador', $persona);
		$this->db->order_by("id", "desc");
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
  }

  public function traer_proyecto($id_proyecto) {
		$this->db->select("cp.*, concat(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, concat(v.nombre, ' ', v.apellido, ' ', v.segundo_apellido) as nombre_completo_responsable_externo, tpr.valor nombre_tipo_proyecto, tpr.id_aux id_aux_tipo_proyecto, tre.valor nombre_tipo_recurso, est.valor estado_proyecto, vp.valor codigo_orden_sap, vp.valorx descripcion_orden_sap, vp2.valor centro_costo, vp2.valorx departamento_centro_costo",FALSE);
    $this->db->from("comite_proyectos cp");
    $this->db->join('personas p', 'p.id = cp.investigador', 'left');
    $this->db->join('visitantes v', 'v.id = cp.id_responsable_externo', 'left');
    $this->db->join('valor_parametro tpr', 'cp.tipo_proyecto = tpr.id', 'left');
    $this->db->join('valor_parametro tre', 'cp.tipo_recurso = tre.id', 'left');
    $this->db->join('valor_parametro est', 'cp.id_estado_proyecto = est.id_aux', 'left');
    $this->db->join('valor_parametro vp', 'cp.id_codigo_orden_sap = vp.id', 'left');
    $this->db->join('valor_parametro vp2', 'vp.valory = vp2.id', 'left');
    $this->db->where('cp.id', $id_proyecto);
    $this->db->where('cp.estado', 1);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
  }

    public function traer_ultimo_item($id_proyecto, $tabla) {
      $this->db->select('tb.*', false);
      $this->db->from("$tabla tb");
      $this->db->join('comite_proyectos cp', 'tb.id_proyecto = cp.id', 'left');
      $this->db->where('cp.estado', 1);
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('tb.estado_registra', 1);
      $this->db->order_by('id', 'desc');
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
    }

    public function traer_datos_convenio_proceedings($id_proyecto) {
      $this->db->select('cp.codigo_convenio, cp.proceedings, cp.verificado_por, cp.id_codigo_orden_sap, vp.valor codigo_orden_sap');
      $this->db->from('comite_proyectos cp');
      $this->db->join('valor_parametro vp', 'cp.id_codigo_orden_sap = vp.id', 'left');
      $this->db->where('cp.id', $id_proyecto);
      $query = $this->db->get();
      return $query->row();
    }

    public function traer_grupo_solicitudes($id_proyecto, $grupo) {
      $this->db->select('vp.valor nombre, cps.fecha_limite, cps.aprobado');
      $this->db->from('comite_proyectos_solicitudes cps');
      $this->db->join('comite_proyectos cp', 'cps.id_proyecto = cp.id');
      $this->db->join('valor_parametro vp', 'cps.id_item = vp.id');
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('cps.grupo_solicitud', $grupo);
      $this->db->where('cp.estado', 1);
      $this->db->where('cps.estado_registra', 1);
      $query = $this->db->get();
      return $query->result_array();
    }

    public function ultimo_grupo_solicitud($id_proyecto) {
      $this->db->select('cps.grupo_solicitud');
      $this->db->from('comite_proyectos_solicitudes cps');
      $this->db->join('comite_proyectos cp', 'cp.id = cps.id_proyecto');
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('cp.estado', 1);
      $this->db->where('cps.estado_registra', 1);
      $this->db->group_by('cps.grupo_solicitud');
      $this->db->order_by('cps.grupo_solicitud', 'desc');
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return isset($row->grupo_solicitud) ? $row->grupo_solicitud : 0;
    }

    public function verificar_item_proyecto($id_proyecto, $item) {
      $this->db->select('vp.*');
      $this->db->from('valor_parametro vp');
      $this->db->join('permisos_parametros pp', 'pp.vp_secundario_id = vp.id');
      $this->db->join('valor_parametro vp2', 'pp.vp_principal_id = vp2.id');
      $this->db->join('comite_proyectos cp', 'cp.tipo_proyecto = vp2.id');
      $this->db->where('vp.estado', 1);
      $this->db->where('vp.idparametro', 184);
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where("vp.valora LIKE '$item'");
      $query = $this->db->get();
      return $query->result_array();
    }
    public function verificar_participante($id_proyecto, $id_persona, $tipo_tabla) {
      $this->db->select("cpp.*");
      $this->db->from('comite_proyectos_participantes cpp');
      $this->db->join('comite_proyectos cp', 'cpp.id_proyecto = cp.id', 'left');
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('cp.estado', 1);
      $this->db->where('cpp.estado_registra', 1);
      $this->db->where('cpp.id_persona', $id_persona);
      $this->db->where('cpp.tipo_tabla', $tipo_tabla);
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return isset($row->id) ? $row : false;
    }

    public function verificar_lim_participante_principal($id_proyecto) {
      $this->db->select('lim.valor limite');
      $this->db->from('valor_parametro lim');
      $this->db->where('lim.id_aux', 'Lim_Ppr');
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();

      $this->db->select("count(*) as numero_ppr, cpp.id");
      $this->db->from('comite_proyectos_participantes cpp');
      $this->db->join('comite_proyectos cp', 'cpp.id_proyecto = cp.id', 'left');
      $this->db->join('valor_parametro vp', 'cpp.id_tipo_participante = vp.id', 'left');
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('cp.estado', 1);
      $this->db->where('cpp.estado_registra', 1);
      $this->db->where('vp.id_aux', 'Pro_Inv_Pri');
      $this->db->limit(1);
      $query = $this->db->get();
      $row2 = $query->row();
      return $row2->numero_ppr == $row->limite ? $row2 : false;
    }

    public function verificar_lim_ods($id_proyecto) {
      $this->db->select('lim.valor limite');
      $this->db->from('valor_parametro lim');
      $this->db->where('lim.id_aux', 'Lim_Ods');
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();

      $this->db->select("count(*) as numero_ods");
      $this->db->from('comite_proyectos_ods cpo');
      $this->db->join('comite_proyectos cp', 'cpo.id_proyecto = cp.id', 'left');
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('cp.estado', 1);
      $this->db->where('cpo.estado_registra', 1);
      $this->db->limit(1);
      $query = $this->db->get();
      $row2 = $query->row();
      return $row2->numero_ods < $row->limite ? false : true;
    }

    public function verificar_ods($id_proyecto, $id_ods) {
      $this->db->select("cpo.id");
      $this->db->from('comite_proyectos_ods cpo');
      $this->db->join('comite_proyectos cp', 'cpo.id_proyecto = cp.id', 'left');
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('cp.estado', 1);
      $this->db->where('cpo.estado_registra', 1);
      $this->db->where('cpo.id_ods', $id_ods);
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return isset($row->id) ? $row : false;
    }
    
    public function verificar_sublinea($id_proyecto, $sublinea) {
      $this->db->select("cpi.id");
      $this->db->from('comite_proyectos_sublineas cpi');
      $this->db->join('comite_proyectos cp', 'cpi.id_proyecto = cp.id', 'left');
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('cp.estado', 1);
      $this->db->where('cpi.estado_registra', 1);
      $this->db->where('cpi.id_sublinea', $sublinea);
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return isset($row->id) ? $row : false;
    }

    public function verificar_objetivo_general($id_proyecto) {
      $this->db->select("cpo.id");
      $this->db->from('comite_proyectos_objetivos cpo');
      $this->db->join('comite_proyectos cp', 'cpo.id_proyecto = cp.id', 'left');
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('cp.estado', 1);
      $this->db->where('cpo.estado_registra', 1);
      $this->db->where('cpo.tipo_objetivo', 'General');
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return isset($row->id) ? $row : false;
    }

    public function verificar_impacto($id_proyecto, $tipo_impacto) {
      $this->db->select('cpi.id');
      $this->db->from('comite_proyectos_impactos cpi');
      $this->db->join('comite_proyectos cp', 'cpi.id_proyecto = cp.id', 'left');
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('cp.estado', 1);
      $this->db->where('cpi.estado_registra', 1);
      $this->db->where('cpi.id_tipo_impacto', $tipo_impacto);
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return isset($row->id) ? $row : false;
    }

    public function verificar_producto($id_proyecto, $id_producto) {
      $this->db->select('cpp.id');
      $this->db->from('comite_proyectos_productos cpp');
      $this->db->join('comite_proyectos cp', 'cpp.id_proyecto = cp.id', 'left');
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('cp.estado', 1);
      $this->db->where('cpp.estado_registra', 1);
      $this->db->where('cpp.id_producto', $id_producto);
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return isset($row->id) ? $row : false;
    }

    public function listar_instituciones($id_proyecto) {
      $sql = "
        SELECT vp.valor nombre, vp.valorx nit, vp.valory pais, vp.valorz telefono, vp.valora persona_contacto, vp.valorb correo
        FROM (
            SELECT cppd.valor id
            FROM comite_proyectos_presupuestos_datos cppd, comite_proyectos_presupuestos cpp, comite_proyectos cp
            WHERE cppd.id_aux_dato = 'Pre_Ent_Res'
            AND cppd.id_presupuesto = cpp.id
            AND cpp.id_proyecto = cp.id
            AND cp.id = ?
            AND cppd.estado_registra = 1
            AND cpp.estado_registra = 1
            AND cp.estado = 1

            UNION ALL

            SELECT cpp.id_institucion id
            FROM comite_proyectos_participantes cpp, comite_proyectos cp
            WHERE cpp.id_proyecto = cp.id
            AND cp.id = ?
            AND cpp.estado_registra = 1
            AND cp.estado = 1

            UNION ALL

            SELECT cpi.id_institucion id
            FROM comite_proyectos_instituciones cpi, comite_proyectos cp
            WHERE cpi.id_proyecto = cp.id
            AND cp.id = ?
            AND cpi.estado_registra = 1
            AND cp.estado = 1
        ) inst
        inner join valor_parametro vp on inst.id = vp.id
        group by vp.valor
      ";
      $query = $this->db->query($sql, array($id_proyecto, $id_proyecto, $id_proyecto));
      return $query->result_array();
    }

    public function verificar_motivos_solicitud($id_proyecto, $informacion){
      $this->db->select('cps.*');
      $this->db->from('comite_proyectos_solicitudes cps');
      $this->db->join('comite_proyectos cp', 'cps.id_proyecto = cp.id');
      $this->db->join('valor_parametro vp', 'cps.id_item = vp.id');
      $this->db->where('cp.id', $id_proyecto);
      $this->db->where('cps.fecha_limite > NOW()');
      $this->db->where("vp.valora LIKE '$informacion'");
      $this->db->where('cps.aprobado', 1);
      $this->db->where('cp.estado', 1);
      $this->db->where('cps.estado_registra', 1);
      $this->db->where('vp.estado', 1);
      $query = $this->db->get();
      return $query->row();
    }

    public function informacion_completa_presupuesto_discriminado($id_proyecto) {
      $temp = $this->listar_presupuesto_discriminado($id_proyecto);
      $temp2 = $this->listar_presupuesto_discriminado($id_proyecto, 1);
      $temp3 = $this->listar_presupuesto_discriminado($id_proyecto, 2);
      $total_presupuesto = 0;
      $presupuesto_entidad = [];
      $presupuesto_entidad_rubro = [];

      foreach ($temp3 as $aux) {
        $total_efectivo = 0;
        $total_especie = 0;
        foreach ($temp as $aux2) {
            if ($aux['entidad_responsable'] == $aux2['entidad_responsable'] && $aux['rubro'] == $aux2['rubro']) {
                if ($aux2['tipo_valor'] == 'Pre_Efec') {
                    $total_efectivo += $aux2['valor_total'];
                } else {
                    $total_especie += $aux2['valor_total'];
                }
            }
        }
        array_push($presupuesto_entidad_rubro, array(
            'entidad_responsable' => $aux['entidad_responsable'],
            'rubro'               => $aux['rubro'],
            'efectivo'            => '$ ' . number_format($total_efectivo, 0, ',', '.'),
            'especie'             => '$ ' . number_format($total_especie, 0, ',', '.'),
            'total'               => '$ ' . number_format($total_especie + $total_efectivo, 0, ',', '.')
        ));
      }

      foreach ($temp as $aux) {
        $total_presupuesto += $aux['valor_total'];
      }

      foreach ($temp2 as $aux) {
        $total_efectivo = 0;
        $total_especie = 0;
        foreach ($temp as $aux2) {
            if ($aux['entidad_responsable'] == $aux2['entidad_responsable']) {
                if ($aux2['tipo_valor'] == 'Pre_Efec') {
                    $total_efectivo += $aux2['valor_total'];
                } else {
                    $total_especie += $aux2['valor_total'];
                }
            }
        }
        array_push($presupuesto_entidad, array(
            'entidad_responsable' => $aux['entidad_responsable'],
            'efectivo'            => '$ ' . number_format($total_efectivo, 0, ',', '.'),
            'especie'             => '$ ' . number_format($total_especie, 0, ',', '.'),
            'total'               => '$ ' . number_format($total_especie + $total_efectivo, 0, ',', '.'),
            'porcentaje'          => (($total_especie + $total_efectivo) * 100) / $total_presupuesto . ' %'
        ));
      }

      return array('presupuesto_entidad' => $presupuesto_entidad, 'presupuesto_entidad_rubro' => $presupuesto_entidad_rubro);
    }

    public function informacion_completa_presupuestos($id_proyecto, $tipo_proyecto) {
      $temp = $this->obtener_valores_permisos($tipo_proyecto, 176, 1);
      $presupuestos = [];
      foreach ($temp as $aux) {
        // $aux['campos'] = $this->listar_campos_presupuestos($id_proyecto, $aux['id']);
        $aux['campos'] = $this->obtener_valores_permisos($aux['id'], 177, 1);
        $temp2 = $this->listar_proyecto_presupuestos($id_proyecto, $aux['id']);
        $informacion = [];
        foreach ($temp2 as $aux2) {
          $temp3 = $this->listar_proyecto_presupuestos_datos($id_proyecto, $aux2['id']);
          $informacion_detallada = [];
          foreach ($temp3 as $aux3) {
            if ($aux3['multiplica'] == 1) {
              $aux3['valor'] = '$' . number_format($aux3['valor'], 2, ',', '.');
            }
            if ($aux3['id_datos'] == 'Pre_Inv') {
              $aux3['valor_select'] = $this->traer_participante_id($id_proyecto, $aux3['valor'])->nombre_completo;
            }
            array_push($informacion_detallada, $aux3);
          }
          $aux2['informacion_detallada'] = $informacion_detallada;
          array_push($informacion, $aux2);
        }
        $aux['informacion'] = $informacion;
        array_push($presupuestos, $aux);
      }
      return $presupuestos;
    }

    public function listar_informacion_proyecto($tabla, $id) {
      $this->db->select('t.*');
      $this->db->from("$tabla t");
      $this->db->join('comite_proyectos cp', 'cp.id = t.id_proyecto', 'left');
      $this->db->where('cp.id', $id);
      $this->db->where('cp.estado', 1);
      if ($tabla == 'comite_proyectos_objetivos') $this->db->where('tipo_objetivo', 'General');
      $this->db->where('t.estado_registra', 1);
      $query = $this->db->get();
      return $query->result_array();
    }

    public function listar_cambios_proyecto($id_proyecto) {
      $this->db->select('bm.*');
      $this->db->from('bitacora_modificaciones bm');
      $this->db->where("((bm.id_alterno = $id_proyecto AND bm.tabla LIKE 'comite_proyectos_%') OR (bm.id_solicitud = $id_proyecto AND bm.tabla = 'comite_proyectos'))");
      $query = $this->db->get();
      return $query->result_array();
    }
}
