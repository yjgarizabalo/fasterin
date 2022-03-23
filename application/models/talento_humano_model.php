<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class talento_humano_model extends CI_Model
{
	var $tabla = 'solicitudes_talento_hum';
	var $tabla_estados = 'estados_solicitudes_talento';

	/**
	 * Se encarga de guardar los datos que se le pasen por el controlador en la tabla indicada.
	 * @param Array $data 
	 * @param String $tabla 
	 * @return Int
	 */
	public function guardar_datos($data, $tabla, $tipo = 1){
		$tipo == 2 ? $this->db->insert_batch($tabla, $data) : $this->db->insert($tabla,$data);
		$error = $this->db->_error_message(); 
		return ($error ? 0 : $tipo == 1) ? $this->db->insert_id() : 1;
	}
	/**
	* Se encarga de modificar los datos que se le pasen por el controlador en la tabla indicada.
	* @param Array $data 
	* @param String $tabla 
	* @param Int $id 
	* @return Int
	*/
	public function modificar_datos($data, $tabla , $id, $col = 'id'){
		$this->db->where($col, $id);
		$this->db->update($tabla, $data);
		$error = $this->db->_error_message(); 
		return $error ? "error" : 0;
	}
	public function modificar_datos_2($data, $tabla, $where){
		$this->db->where($where);
		$this->db->update($tabla, $data);
		$error = $this->db->_error_message(); 
		return $error ? "error" : 0;
	}
	/**
	* Se encarga de modificar los datos que se le pasen por el controlador en la tabla indicada.
	* @param Array $data 
	* @param String $tabla 
	* @param Int $id 
	* @return Int
	*/
	public function eliminar_datos($tabla , $id){
		$this->db->where('id', $id);
		$this->db->delete($tabla);
		$error = $this->db->_error_message(); 
		return $error ? "error" : 0;
	}
	/**
 * Trae el ultimo  postulante registro por un usuario
 * @param Integer $persona 
 * @return Id
 */
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
		
	/**
	 * Realiza una consulta a la tabla persona y lista aquellas personas que se encuentran en estado activo
	 * @return Array
	 */
		public function buscar_postulante($where)
		{
			$this->db->select("p.identificacion,p.id,p.nombre,p.segundo_nombre,p.apellido,p.segundo_apellido,p.correo,p.id_tipo_identificacion,p.fecha_expedicion,p.lugar_expedicion,p.fecha_nacimiento,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,u2.valor tipo_identificacion, p.genero, p.telefono, p.correo, p.eps", false);
			$this->db->from('personas p');
			$this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
			$this->db->where($where);
			$query = $this->db->get();
			return $query->result_array();
	}
	public function listar_postulantes_csep($vista = 'talento_humano',$id , $tipo = 'solicitud')
	{	
		$filtro = $tipo == 'solicitud' ? 'ps.id_solicitud' : 'ps.id_comite';
		$perfil = $_SESSION['perfil'];
		$usuario = $_SESSION['persona'];
		$administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Csep" ? true : false;
		$this->db->select("pr.valor programa,ps.id_departamento_actual_postulante id_departamento_actual ,cra.valor cargo_actual,dpa.valor departamento_actual,p.id id_postulante, p.identificacion,p.id_tipo_identificacion,p.fecha_expedicion,p.lugar_expedicion,p.fecha_nacimiento,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,u2.valor tipo_identificacion,ps.*,cr.valor cargo,dp.valor departamento,f.valor formacion,ct.valor categoria,est.valor estado_solicitud,dp.id id_departamento,(SELECT COUNT(ep.id) FROM estados_postulantes ep WHERE ep.id_postulante = ps.id AND (ep.id_estado ='Pos_Bue' OR ep.id_estado ='Pos_Apr') AND ep.estado = 1) vb,(SELECT COUNT(epp.id) FROM estados_postulantes epp WHERE epp.id_postulante = ps.id AND (epp.id_estado ='Pos_Bue' OR epp.id_estado ='Pos_Apr' OR epp.id_estado ='Pos_Mal') AND epp.estado = 1 AND epp.usuario_registra = $usuario) tiene,(SELECT COUNT(ep.id) FROM estados_postulantes ep WHERE ep.id_postulante = ps.id AND (ep.id_estado ='Pos_Mal' OR ep.id_estado ='Pos_Neg') AND ep.estado = 1) vm,ti.valor tipo", false);
		$this->db->from('postulantes_csep ps');
		$this->db->join('personas p','ps.id_postulante = p.id');
		//$this->db->join('cargos_departamentos c', 'ps.id_cargo=c.id','left');
		$this->db->join('valor_parametro ti', 'ps.id_tipo=ti.id_aux');
		$this->db->join('valor_parametro cr', 'ps.id_cargo_postulante=cr.id','left');
		$this->db->join('valor_parametro dp', 'ps.id_departamento_postulante=dp.id','left');
		$this->db->join('valor_parametro pr', 'ps.id_programa=pr.id','left');
		if($vista == 'comite_csep' && $perfil != 'Per_Admin')$this->db->join('permisos_personas_csep per', "per.id_permiso = ps.id_programa AND per.id_persona = ".$usuario);
		$this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
		$this->db->join('valor_parametro f', 'ps.id_formacion=f.id','left');
		$this->db->join('valor_parametro ct', 'ps.id_categoria=ct.id','left');
		$this->db->join('valor_parametro est', 'ps.id_estado_solicitud=est.id_aux');
		//$this->db->join('cargos_departamentos ca', 'ps.id_cargo_actual=ca.id','left');
		$this->db->join('valor_parametro cra', 'ps.id_cargo_actual_postulante=cra.id','left');
		$this->db->join('valor_parametro dpa', 'ps.id_departamento_actual_postulante=dpa.id','left');
		if($tipo == 'solicitud' && !$administra && $vista != 'talento_humano')	$this->db->where("ps.usuario_registra = $usuario");
	  $this->db->where("$filtro",$id);
	  $this->db->where("ps.estado",1);
		 $query = $this->db->get();
		return $query->result_array();
	}
	public function buscar_postulantes_csep_id($where)
	{	
		$this->db->select("cr.id_aux cargo_aux,ps.*,(SELECT COUNT(ep.id) FROM estados_postulantes ep WHERE ep.id_postulante = ps.id AND (ep.id_estado ='Pos_Bue' OR ep.id_estado ='Pos_Apr') AND ep.estado = 1) vb,(SELECT COUNT(ep.id) FROM estados_postulantes ep WHERE ep.id_postulante = ps.id AND (ep.id_estado ='Pos_Mal' OR ep.id_estado ='Pos_Neg') AND ep.estado = 1) vm");
		$this->db->from('postulantes_csep ps');
		//$this->db->join('cargos_departamentos c', 'ps.id_cargo=c.id','left');
		$this->db->join('valor_parametro cr', 'ps.id_cargo_postulante=cr.id','left');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_solicitudes_csep($id, $tipo_solicitud, $estado, $fecha_inicio, $fecha_fin){
		$persona = $_SESSION['persona'];
		$filtro = (empty($tipo_solicitud) && empty($estado) && empty($fecha_inicio) && empty($fecha_fin)) ? 0 : 1;
		$this->db->select("sth.*,t.valor tipo_solicitud, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as solicitante, p.correo, e.valor estado_solicitud, e.valor state", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join('valor_parametro t', 'sth.id_tipo_solicitud = t.id_aux');
		$this->db->join('valor_parametro e', 'sth.id_estado_solicitud = e.id_aux');
		$this->db->join('personas p','sth.usuario_registro = p.id');
		$this->db->where("sth.estado", 1);
		$this->db->where("(sth.id_tipo_solicitud = 'Hum_Csep' OR sth.id_tipo_solicitud = 'Hum_Prec')");
		if(!empty($id))$this->db->where("sth.id", $id);
		if($filtro){
			if(!empty($tipo_solicitud))$this->db->where('sth.id_tipo_solicitud', $tipo_solicitud);
			if(!empty($estado))$this->db->where('sth.id_estado_solicitud', $estado);
			if(!empty($fecha_inicio) && !empty($fecha_fin)) {
				$this->db->where('DATE_FORMAT(sth.fecha_registro, "%Y-%m") >=', $fecha_inicio);
				$this->db->where('DATE_FORMAT(sth.fecha_registro, "%Y-%m") <=', $fecha_fin);
			}else if(!empty($fecha_inicio) && empty($fecha_fin)) $this->db->where('DATE_FORMAT(sth.fecha_registro, "%Y-%m") >=', $fecha_inicio);
			else if(empty($fecha_inicio) && !empty($fecha_fin)) $this->db->where('DATE_FORMAT(sth.fecha_registro, "%Y-%m") <=', $fecha_fin);
		// } else $this->db->where("sth.id_estado_solicitud <> 'Tal_Can' AND sth.id_estado_solicitud <> 'Tal_Neg' AND sth.id_estado_solicitud <> 'Tal_Ter' AND sth.id_estado_solicitud <> 'Tal_Des'");
		}else $this->db->where("(sth.id_estado_solicitud = 'Env_Csea' AND sth.id_tipo_solicitud = 'Hum_Prec') OR (sth.id_estado_solicitud <> 'Tal_Can' AND sth.id_estado_solicitud <> 'Tal_Neg' AND sth.id_estado_solicitud <> 'Tal_Ter' AND sth.id_estado_solicitud <> 'Tal_Des' AND sth.id_tipo_solicitud = 'Hum_Csep')");
		$administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Csep" ? true : false;
		if (!$administra){
			$this->db->where("(sth.usuario_registro = $persona OR (sth.id_tipo_solicitud = 'Hum_Csep' AND  (SELECT COUNT(*) FROM postulantes_csep pc WHERE pc.usuario_registra = $persona AND pc.id_solicitud = sth.id ) > 0))");
		} 
		$this->db->order_by("sth.fecha_registro","desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_solicitudes($id, $estado,  $tipo, $fecha_i, $fecha_f){
		$persona = $_SESSION['persona'];
		$perfil = $_SESSION['perfil'];
		$es_decano = $this->get_decano();
		$decano = $es_decano ? $es_decano->{'departamento'} : 0;
		$filtro = (!empty($id) || !empty($estado) || !empty($tipo) || !empty($fecha_i) || !empty($fecha_f)) ? 1 : 0;
		$this->db->select("apt.id aptid, eat.id permiso_estado, sth.*, t.valor tipo_solicitud, t.id tipo_solicitud_id, est.valor state, 
		(SELECT COUNT(pc.id) total FROM postulantes_csep pc WHERE pc.id_solicitud = sth.id) total,
		CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as solicitante, r.id responsable_id, 
		CONCAT(r.nombre,' ', r.apellido,' ', r.segundo_apellido) as responsable, r.correo correo_responsable, p.correo, 
		sth.usuario_registro, pth.tipo_prestamo, s.tipo_cargo_id, s.numero_vacantes, s.cargo_id, s.perfil, s.nombre_vacante, s.cerrada, 
		s.nombre_vacante, pth.volante, sth.observacion, c.certificado, c.nombre_archivo, c.fecha_adjunto, sth.aux, v.tipo_cargo, 
		dr.id_departamento, v.departamento_id, esta.id estado_id, c.especificaciones, v.nombre_cargo, v.id vacante_id,
		CONCAT(j.nombre,' ', j.apellido,' ', j.segundo_apellido) as jefe_responsable, sth.jefe_inmediato id_jefe_inmediato", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join('valor_parametro t', 'sth.id_tipo_solicitud = t.id_aux');
		$this->db->join('personas p','sth.usuario_registro = p.id');
		$this->db->join('personas r','sth.responsable_id = r.id', 'left');
		$this->db->join('personas j','sth.jefe_inmediato = j.id', 'left');
		$this->db->join('prestamos_th pth','sth.id = pth.id_solicitud', 'left');
		$this->db->join('certificados c','sth.id = c.solicitud_id', 'left');
		$this->db->join('seleccion s','sth.id = s.solicitud_id', 'left');
		$this->db->join('vacantes v', 'sth.id = v.solicitud_id', 'left');
		$this->db->join('valor_parametro est', 'est.id_aux = sth.id_estado_solicitud');
		$this->db->join('actividad_persona_th apt', 'sth.id_tipo_solicitud = apt.actividad_id AND apt.persona_id = '.$persona,'left');
		$this->db->join('detalle_requisicion dr', 'dr.solicitud_id = sth.id','left');
		$this->db->join('valor_parametro esta', 'esta.id_aux = sth.id_estado_solicitud and esta.estado = 1','left');
		$this->db->join('estados_actividades_th eat', 'esta.id = eat.estado_id AND apt.id = eat.actividad_id','left');
		$this->db->join('valor_parametro dep', 'dep.id = dr.id_departamento and dep.idparametro = 91','left');
		if ($filtro) {
			if($id) $this->db->where('sth.id', $id);
			if($estado) $this->db->where('sth.id_estado_solicitud', $estado);
			if($tipo) $this->db->where('sth.id_tipo_solicitud', $tipo);
			if ($fecha_i && $fecha_f) {
				$this->db->where("(DATE_FORMAT(sth.fecha_registro, '%Y-%m') >= '$fecha_i' AND DATE_FORMAT(sth.fecha_registro, '%Y-%m') <= '$fecha_f')");
			} else if($fecha_i && !$fecha_f) $this->db->where('DATE_FORMAT(sth.fecha_registro, "%Y-%m") >=', $fecha_i);
			else if (!$fecha_i && $fecha_f) $this->db->where('DATE_FORMAT(sth.fecha_registro, "%Y-%m") <=', $fecha_f);
		} else {
			$this->db->where("(sth.id_estado_solicitud <> 'Tal_Can' AND sth.id_estado_solicitud <> 'Tal_Neg' AND sth.id_estado_solicitud <> 'Tal_Ter' AND sth.id_estado_solicitud <> 'Tal_Des' AND sth.estado = 1)");
		}

		if ($perfil != 'Per_Admin')	$this->db->where("(sth.usuario_registro = $persona) OR (((sth.responsable_id = $persona OR sth.jefe_inmediato = $persona) AND sth.id_tipo_solicitud = 'Hum_Sele')) OR (((sth.id_tipo_solicitud = 'Hum_Vac' OR sth.id_tipo_solicitud = 'Hum_Lic' OR sth.id_tipo_solicitud = 'Hum_Entr_Cargo') AND sth.jefe_inmediato = $persona)) OR (((sth.id_tipo_solicitud = 'Hum_Entr_Cargo') AND sth.id_solicitante = $persona)) OR (((sth.id_tipo_solicitud = 'Hum_Entr_Cargo') AND sth.jefe_inmediato2 = $persona)) OR   (apt.actividad_id = 'Hum_Entr_Cargo' AND apt.persona_id = $persona) OR ((eat.id IS NOT NULL))");
		$this->db->order_by('sth.fecha_registro', 'DESC');
		$this->db->where('sth.estado <>', 0);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function buscar_solicitud_hoy($tipo)
	{	
		$this->db->select("id");
		$this->db->from('solicitudes_talento_hum');
		$this->db->where("DATE_FORMAT(fecha_registro,'%Y-%m-%d') = CURDATE()");
		$this->db->order_by("id", "desc");
		$this->db->where("id_tipo_solicitud", $tipo);
		$this->db->where("id_estado_solicitud", 'Tal_Env');
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return is_null($row) || empty($row) ? null : $row->id;
	}
		/**
 * Trae el ultimo  postulante registro por un usuario
 * @param Integer $persona 
 * @return Id
 */
	public function traer_ultimo_registro_postulante_sol_usuario($persona)
	{ 
		$this->db->select("*");
		$this->db->from("postulantes_csep");
		$this->db->order_by("id", "desc");
		$this->db->where('usuario_registra', $persona);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
		}

	public function listar_estados_csep($id_postulante)
	{
		$perfil = $_SESSION['perfil'];
		$filtro = $perfil != 'Per_Csep' && $perfil != 'Per_Admin' && $perfil != 'Per_Admin_Tal' ? true : false;
		$this->db->select("pt.*,es.valor estado,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona,",false);
		$this->db->from('estados_postulantes pt');
		$this->db->join('personas p', 'pt.usuario_registra = p.id');
		$this->db->join('valor_parametro es', 'pt.id_estado= es.id_aux');
		$this->db->where('pt.id_postulante', $id_postulante);
		//if($filtro)$this->db->where("(pt.id_estado = 'Pos_Bue' OR pt.id_estado = 'Pos_Mal' OR pt.id_estado = 'Pos_Neg'  OR pt.id_estado = 'Pos_Apr')");
		$this->db->order_by("pt.fecha_registro");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_comites($id = null, $estado = null, $vista = '')
	{
		$usuario = $_SESSION['persona'];
		$perfil = $_SESSION['perfil'];
		if(($perfil != 'Per_Csep' && $perfil != 'Per_Admin' && $vista != 'talento_humano')){
			$this->db->select("c.*,e.valor estado,(SELECT COUNT(ps.id) FROM postulantes_csep ps INNER JOIN permisos_personas_csep per ON per.id_persona = $usuario AND per.id_permiso = ps.id_programa WHERE ps.id_comite = c.id) as total");
		}else{
			$this->db->select("c.*,e.valor estado,(SELECT COUNT(pc.id) FROM postulantes_csep pc WHERE pc.id_comite = c.id) as total");
		}
	
		$this->db->from('comites c');
		$this->db->join('valor_parametro e', 'c.id_estado_comite= e.id_aux');
		$this->db->where('c.estado', 1);
		$this->db->where('c.tipo', 'csep');
		if(($perfil != 'Per_Csep' && $perfil != 'Per_Admin'))$this->db->having('total > 0',false);
		if(!is_null($id))$this->db->where('c.id', $id);
		if(!is_null($estado) && !empty($estado))$this->db->where($estado);
		$this->db->_protect_identifiers = false;
		$this->db->order_by("FIELD (c.id_estado_comite,'Com_Ini','Com_Not','Com_Ter')");
		$this->db->_protect_identifiers = true;
		$query = $this->db->get();
		
		return $query->result_array();
	}
	public function aprobar_todos_postulantes_comite($id_comite,$usuario,$estado)
	{
		if($estado == 'Pos_Apr'){
			$sql = "UPDATE postulantes_csep pc 
			INNER JOIN permisos_personas_csep per ON per.id_persona = $usuario AND per.id_permiso = pc.id_programa
			SET pc.id_estado_solicitud = 'Pos_Apr'
			WHERE pc.id_comite = $id_comite AND pc.id_estado_solicitud = 'Pos_Act'";
			$this->db->query($sql);
			$n = $this->db->affected_rows();
			if ($n > 0) {
				$sql = "INSERT INTO estados_postulantes( id_estado, id_postulante, usuario_registra)
				SELECT 'Pos_Apr',pc.id,$usuario FROM postulantes_csep pc
				LEFT JOIN estados_postulantes ep ON pc.id = ep.id_postulante AND ep.id_estado = 'Pos_Apr'
				INNER JOIN permisos_personas_csep per ON per.id_persona = $usuario AND per.id_permiso = pc.id_programa
				WHERE pc.id_comite = $id_comite AND pc.id_estado_solicitud = 'Pos_Apr' AND ep.id_postulante IS NULL";
				$this->db->query($sql);
			}
		}else{
			$sql = "INSERT INTO estados_postulantes( id_estado, id_postulante, usuario_registra)
			SELECT 'Pos_Bue',pc.id,$usuario FROM postulantes_csep pc 
			LEFT JOIN estados_postulantes ep ON pc.id = ep.id_postulante AND ep.usuario_registra = $usuario AND (ep.id_estado = 'Pos_Bue' OR ep.id_estado = 'Pos_Mal')
			INNER JOIN permisos_personas_csep per ON per.id_persona = $usuario AND per.id_permiso = pc.id_programa
			WHERE pc.id_comite = $id_comite AND pc.id_estado_solicitud = 'Pos_Act' AND ep.id_postulante IS NULL";
			 $this->db->query($sql);
			$n = $this->db->affected_rows();
		}
		return $n;
		}
		
		public function obtener_programas($buscar) {
			$this->db->select("vp.id ,vp.valor,vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro,re.valor relacion",FALSE);
			$this->db->from('valor_parametro vp');
			$this->db->join('valor_parametro re', 'vp.valory = re.id','left');
			$this->db->where("vp.idparametro = 3 AND vp.estado = 1 AND vp.valory = '$buscar'");
			$query = $this->db->get();
			return $query->result_array();
	}

	public function aprueba_persona_csep($id){
		$this->db->select("*");
		$this->db->from('personas p');
		$this->db->where("p.id",$id);
		$this->db->where("p.estado",1);
		$query = $this->db->get();
		$row = $query->row();
		return is_null($row) || empty($row) ? null :$row->cod_encargado;
	}

	public function tiene_visto_bueno_persona($id_postulante,$id_persona)
	{
		$this->db->select("*");
		$this->db->from('estados_postulantes pt');
		$this->db->where('pt.id_postulante', $id_postulante);
		$this->db->where('pt.usuario_registra', $id_persona);
		$this->db->where("(pt.id_estado = 'Pos_Bue' OR pt.id_estado = 'Pos_Mal')");
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function listar_personas_vb_csep() 
	{
		$this->db->select("p.id,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,p.cod_encargado aprueba",FALSE);
		$this->db->from('actividades_personas ap');
		$this->db->join('personas p', 'p.id = ap.id_persona');
		$this->db->where("ap.id_actividad",'comite_csep');
		$query = $this->db->get();
		return $query->result_array();
	}
	public function obtener_programas_persona($id) 
	{
		$this->db->select("vp.id ,vp.valor,vp.valorx,pc.id estado,pc.id_tipo,t.valor tipo");
		$this->db->from('valor_parametro vp');
		$this->db->join('permisos_personas_csep pc', "vp.id = pc.id_permiso AND pc.id_persona = $id",'left');
		$this->db->join('valor_parametro t','t.id_aux = pc.id_tipo','left');
		$this->db->where("vp.idparametro = 3 AND vp.valory = 2 AND vp.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function tiene_programa_persona($id_permiso, $persona) 
	{
		$this->db->select("*");
		$this->db->from('permisos_personas_csep pc');
		$this->db->where("pc.id_persona",$persona);
		$this->db->where("pc.id_permiso",$id_permiso);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function asignar_todos_programas($id_persona,$usuario_registra,$tipo)
	{
			$sql = "INSERT INTO permisos_personas_csep( id_persona, id_permiso, usuario_registra , id_tipo)
			SELECT $id_persona,vp.id,$usuario_registra,'$tipo' FROM valor_parametro vp LEFT JOIN permisos_personas_csep pc ON vp.id = pc.id_permiso AND pc.id_persona = $id_persona WHERE vp.idparametro = 3 AND vp.valory = '2' AND vp.estado = 1 AND pc.id IS NULL";
			$this->db->query($sql);
			$n = $this->db->affected_rows();
			return $n;
	}
	public function retirar_todos_programas($id_persona) 
	{
		$this->db->where('id_persona', $id_persona);
		$this->db->delete('permisos_personas_csep');
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return 0;
	}
	public function faltantes_aprobar_comite($id)
	{
		$this->db->select("COUNT(c.id) total ");
		$this->db->from('comites c');
		$this->db->join('postulantes_csep pc',"c.id = pc.id_comite AND pc.id_estado_solicitud = 'Pos_Act'");
		$this->db->where("c.id",$id);
		$query = $this->db->get();
		$row = $query->row();
		return is_null($row) || empty($row) ? null :$row->total;
}
	public function traer_correo_notifica_comite($id){
		$this->db->select("p.correo,cr.id_aux,p.cod_encargado,cr.valor,pc.id_comite,pc.id_cargo");
		$this->db->from('postulantes_csep pc');
		//$this->db->join('cargos_departamentos c', 'pc.id_cargo = c.id','left');
		$this->db->join('valor_parametro cr', 'pc.id_cargo_postulante = cr.id','left');
		$this->db->join('permisos_personas_csep per',"per.id_permiso = pc.id_programa");
		$this->db->join('personas p'," p.id= per.id_persona");
		$this->db->where("pc.id_comite",$id);
		$this->db->where("(p.cod_encargado IS NULL OR (cr.id_aux IS NULL AND p.cod_encargado IS NOT NULL))");
		$this->db->group_by('p.id'); 
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function detalle_solicitud($id, $tipo){
		switch ($tipo) {
			case 'Hum_Pres':
				$this->db->select('pth.*, vp.valor AS tipo, est.comentario, est1.comentario msj_negado');
				$this->db->from('prestamos_th pth');
				$this->db->join('estados_solicitudes_talento est', "est.solicitud_id = pth.id_solicitud AND est.estado_id = 'Tal_Mal'", 'left');
				$this->db->join('estados_solicitudes_talento est1', "est1.solicitud_id = pth.id_solicitud AND est1.estado_id = 'Tal_Neg'", 'left');
				$this->db->join('valor_parametro vp', 'vp.id_aux = pth.tipo_prestamo', 'left');
				$this->db->where("pth.id_solicitud = $id");
				break;
			case 'Hum_Sele':
			case 'Hum_Prec':
			case 'Hum_Admi':
				$this->db->select('s.id seleccion_id, s.tipo_cargo_id, s.nombre_vacante, s.numero_vacantes, s.perfil, s.cargo_id, c.valor tipo_cargo, car.valor cargo, dep.id departamento_id, dep.valor departamento');
				$this->db->from('seleccion s');
				$this->db->join('valor_parametro c', 's.tipo_cargo_id = c.id_aux');
				$this->db->join('valor_parametro car', 's.cargo_id = car.id');
				$this->db->join('valor_parametro dep', 's.departamento_id = dep.id');
				$this->db->where('s.solicitud_id',$id);
				$this->db->where('s.estado', 1);
				break;
			case 'Hum_Cert':
				$this->db->select('c.id, c.especificaciones');
				$this->db->from('certificados c');
				$this->db->join('opciones_certificados oc', 'oc.certificado_id = c.id', 'left');
				$this->db->where('c.solicitud_id', $id);
				$this->db->where('c.estado', 1);
				break;
		}
		$query = $this->db->get();
		if($tipo !== 'Hum_Cert'){
			return $query->row();
		} else {
			$solicitud = $query->row();
			if(!is_array($solicitud)) $solicitud->opciones = $this->get_info_certificado($solicitud->id);
			return $solicitud;
		}
	}

	public function get_info_certificado($id){
		$this->db->select('oc.id, vp.valor opcion, oc.opcion_id');
		$this->db->from('opciones_certificados oc');
		$this->db->join('valor_parametro vp', 'oc.opcion_id = vp.id_aux');
		$this->db->where('oc.certificado_id', $id);
		$this->db->where('oc.estado', 1);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function get_info_ausentismo_vacaciones($id){
		$this->db->select("es.*, est.comentario, est1.comentario msj_negado",false);
		$this->db->from('solicitudes_ausentismo_vacaciones es');
		$this->db->join('estados_solicitudes_talento est', "est.solicitud_id = es.id_solicitud AND est.estado_id = 'Tal_Can'", 'left');
		$this->db->join('estados_solicitudes_talento est1', "est1.solicitud_id = es.id_solicitud AND est1.estado_id = 'Tal_Neg'", 'left');
		$this->db->where('es.id_solicitud', $id);
		$this->db->where('es.estado', 1);
		$query = $this->db->get();
		return $query->row();
	}
	public function get_info_ausentismo_licencia($id){
		$this->db->select("es.*, vp.valor tipo_lic, est.comentario, est1.comentario msj_negado",false);	
		$this->db->from('solicitudes_ausentismo_licencia es');
		$this->db->join('valor_parametro vp', 'vp.id= es.tipo_licencia');		
		$this->db->join('estados_solicitudes_talento est', "est.solicitud_id = es.id_solicitud AND est.estado_id = 'Tal_Can'", 'left');
		$this->db->join('estados_solicitudes_talento est1', "est1.solicitud_id = es.id_solicitud AND est1.estado_id = 'Tal_Neg'", 'left');
		$this->db->where('es.id_solicitud', $id);
		$this->db->where('es.estado', 1);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_estado_solicitud($id){
		$this->db->select('id_estado_solicitud');
		$this->db->from('solicitudes_talento_hum');
		$this->db->where("id", $id);
		$query = $this->db->get();
		return $query->row()->id_estado_solicitud;
	}

	public function get_descuentos($id){
		$this->db->select("dp.concepto, dp.valor, vp.valor AS tipo_descuento");
		$this->db->from('descuentos_prestamo dp');
		$this->db->join('valor_parametro vp', 'vp.id_aux = dp.tipo_descuento');
		$this->db->where('solicitud_id', $id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function faltantes_contratar_solicitud($id_postulante)
	{
		$this->db->select("COUNT(pc.id) total");
		$this->db->from('postulantes_csep pc');
		$this->db->where("pc.id_solicitud = (SELECT pc1.id_solicitud FROM postulantes_csep pc1 WHERE pc1.id = $id_postulante) AND (pc.id_estado_solicitud <> 'Pos_Con' AND id_estado_solicitud <> 'Pos_Neg' AND id_estado_solicitud <> 'Pos_Can' AND id_estado_solicitud <> 'Pos_Rev') AND pc.estado = 1");
		$query = $this->db->get();
		$row = $query->row();
		return is_null($row) || empty($row) ? null :$row->total;
	}

	public function buscar_ultima_postulacion($where)
	{	
		$this->db->select("p.id id_postulante,p.identificacion,p.id_tipo_identificacion,p.fecha_expedicion,p.lugar_expedicion,p.fecha_nacimiento,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,u2.valor tipo_identificacion,ps.*,cr.valor cargo,dp.valor departamento,f.valor formacion,ct.valor categoria,est.valor estado_solicitud,dp.id id_departamento,ti.valor tipo", false);
		$this->db->from('postulantes_csep ps');
		$this->db->join('personas p','ps.id_postulante = p.id');
		//$this->db->join('cargos_departamentos c', 'ps.id_cargo=c.id','left');
		$this->db->join('valor_parametro cr', 'ps.id_cargo_postulante=cr.id','left');
		$this->db->join('valor_parametro ti', 'ps.id_tipo=ti.id_aux');
		$this->db->join('valor_parametro dp', 'ps.id_departamento_postulante=dp.id','left');
		$this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
		$this->db->join('valor_parametro f', 'ps.id_formacion=f.id');
		$this->db->join('valor_parametro ct', 'ps.id_categoria=ct.id','left');
		$this->db->join('valor_parametro est', 'ps.id_estado_solicitud=est.id_aux');
		$this->db->order_by("id", "desc");
		$this->db->where($where);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}
  	// public function listar_cargos_departamento($id_departamento) {
	// 	$this->db->select("vp.id, vp.valor");
	// 	$this->db->from("cargos_departamentos cd");
	// 	$this->db->join('valor_parametro vp', 'cd.id_cargo = vp.id AND vp.valory = 1');
	// 	$this->db->where('cd.id_departamento', $id_departamento);
	// 	$this->db->where('cd.estado', "1");
	// 	$query = $this->db->get();
	// 	return $query->result_array();
	// }

	public function listar_cargos_departamento_nuevo($id_departamento, $tipo) {

		$this->db->select("vp2.id, vp2.valor");
		$this->db->from("permisos_parametros pp");
		$parametro = $tipo ? 91 : 208;
		$this->db->join('valor_parametro vp1', "vp1.id = pp.vp_principal_id AND vp1.idparametro = $parametro AND vp1.estado = 1");
		$this->db->join('valor_parametro vp2', 'vp2.id = pp.vp_secundario_id AND vp2.idparametro = 2 AND vp2.estado = 1');
		$this->db->where('vp1.id', $id_departamento);
		$this->db->where('pp.estado', "1");
		$query = $this->db->get();
		return $query->result_array();
	}

  	public function get_cargos_departamento($id_departamento) {
		$this->db->select("cd.id, vp.valor");
		$this->db->from("cargos_departamentos cd");
		$this->db->join('valor_parametro vp', 'cd.id_cargo = vp.id');
		$this->db->where('cd.id_departamento', $id_departamento);
		$this->db->where('cd.estado', 1);
		$this->db->where('vp.estado', 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_solicitud_prestamo($id){
		$this->db->select("pt.tipo_prestamo, st.*");
		$this->db->from('solicitudes_talento_hum st');
		$this->db->join('prestamos_th pt', 'pt.id_solicitud = st.id', 'left');
		$this->db->where('st.id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function cambiar_estado($id, $state){
		$this->db->set('id_estado_solicitud', $state);
		$this->db->where('id', $id);
		$this->db->update('solicitudes_talento_hum');
	}

	public function get_id_vacante($solicitud_id) {
		$this->db->select("v.id");
		$this->db->from('vacantes v');
		$this->db->join('solicitudes_talento_hum sth', 'sth.id = v.solicitud_id');
		$this->db->where('sth.id', $solicitud_id);
		$query = $this->db->get();
		return $query->row()->id;
	}
	
	public function get_solicitud_vacante($id){
		$this->db->select("v.tipo_vacante as tipo_solicitud, s.*");
		$this->db->from('vacantes v');
		$this->db->join('solicitudes_talento_hum s', 'v.solicitud_id = s.id');
		$this->db->where('s.id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function permiso_cancelar($id, $user){
		$this->db->select("IF(usuario_registro = $user, 1, 0) permiso", false);
		$this->db->from($this->tabla);
		$this->db->where('id', $id);
		$query = $this->db->get();
		return $query->row()->permiso;
	}

	public function modificar_porcentajes($salud, $pension){
		$this->db->set('valor', $salud);
		$this->db->where('id_aux', 'DescSalud');
		$this->db->update('valor_parametro');
		$error = $this->db->_error_message(); 
		if ($error) return 1;
		$this->db->set('valor', $pension);
		$this->db->where('id_aux', 'DescPension');
		$this->db->update('valor_parametro');
		$error = $this->db->_error_message(); 
		if ($error) return 1;
		return 0;
	}

	public function modificar_cuotas($libre, $cruce){
		$this->db->set('valory', $cruce);
		$this->db->where('id_aux', 'Pre_Cru');
		$this->db->update('valor_parametro');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		$this->db->set('valory', $libre);
		$this->db->where('id_aux', 'Pre_Lib');
		$this->db->update('valor_parametro');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}

	public function revisar_solicitud($id, $salario, $cupo, $saldo){
		$this->db->set('salario', $salario);
		$this->db->set('cupo', $cupo);
		$this->db->set('saldo', $saldo);
		$this->db->where('id_solicitud', $id);
		$this->db->update('prestamos_th');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}

	public function get_historial($id){
		$this->db->select("vp.valor AS estado, est.fecha,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) AS fullname", false);
		$this->db->from('estados_solicitudes_talento est');
		$this->db->join('valor_parametro vp', 'est.estado_id = vp.id_aux');
		$this->db->join('personas p', 'p.id = est.usuario_id');
		$this->db->where('est.solicitud_id ', $id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_personas($texto){
		$this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) AS fullname", false);
		$this->db->from('personas p');
		$this->db->where("p.nombre like '%$texto%' || p.apellido like '%$texto%' || p.segundo_apellido like '%$texto%' || p.usuario like '%$texto%' || p.identificacion like '%$texto%'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_actividades($persona){
		$query = $this->db->query("(SELECT vp.id_aux as id, vp.valor as nombre, ap.id as asignado
		FROM valor_parametro vp
		LEFT JOIN actividad_persona_th ap ON (vp.id_aux = ap.actividad_id AND ap.persona_id = $persona)
		WHERE idparametro = 54)");
		return $query->result_array();
	}

	public function quitar_actividad($id){
		$this->db->where('id', $id);
		$this->db->delete('actividad_persona_th');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}

	public function validar_asignacion_actividad($id, $persona){
		$this->db->select("IF(COUNT(id) > 0, 0, 1) asignado", false);
		$this->db->from('actividad_persona_th');
		$this->db->where('actividad_id', $id);
		$this->db->where('persona_id', $persona);
		$query = $this->db->get();
		return $query->row()->asignado;
	}

	public function cargar_actividades_persona($persona){
		$this->db->select("actividad_id");
		$this->db->from('actividad_persona_th');
		$this->db->where("persona_id", $persona);
		$query = $this->db->get();
		$actividades = $query->result_array();
		$permisos = [];
		foreach ($actividades as $actividad) {
			$permisos[$actividad['actividad_id']] = 1;
		}
		return $permisos;
	}

	public function listar_estados($actividad){
		$query = $this->db->query("(
			SELECT p.nombre parametro, vp.id AS estado, vp.valor AS nombre, ea.id AS asignado, ea.notificacion
			FROM actividad_persona_th ap
			INNER JOIN permisos_parametros pp ON pp.vp_principal = ap.actividad_id
			INNER JOIN valor_parametro vp ON vp.id = pp.vp_secundario_id
			INNER JOIN parametros p ON p.id = vp.idparametro
			LEFT JOIN estados_actividades_th ea ON vp.id = ea.estado_id AND ap.id = ea.actividad_id
			WHERE ap.id = $actividad 
			AND ap.estado = 1 AND pp.estado = 1 AND vp.estado = 1
			ORDER BY vp.idparametro, vp.valor
		)");
		return $query->result_array();
	}

	public function validar_asignacion_estado($estado, $actividad, $persona){
		$this->db->select("IF(COUNT(ea.id) > 0, 0, 1) asignado",false);
		$this->db->from('estados_actividades_th ea');
		$this->db->where('ea.actividad_id', $actividad);
		$this->db->where('ea.estado_id', $estado);
		$query = $this->db->get();
		return $query->row()->asignado;
	}

	public function quitar_estado($id){
		$this->db->where('id', $id);
		$this->db->delete('estados_actividades_th');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}

	public function info_solicitud($id){
		$this->db->select("pth.valor, pth.cuotas, pth.tipo_prestamo AS tipo, p.id, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS usuario, p.correo", false);
		$this->db->from('prestamos_th pth');
		$this->db->join('personas p', 'pth.id_usuario_registra = p.id');
		$this->db->where('id_solicitud', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function aprobar_prestamo($id, $valor, $cuotas){
		$this->db->set('valor_aprobado', $valor);
		$this->db->set('cuotas_aprobadas', $cuotas);
		$this->db->where('id_solicitud', $id);
		$this->db->update('prestamos_th');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}

	public function get_max_cuotas($tipo){
		$this->db->select("valory as max");
		$this->db->from('valor_parametro');
		$this->db->where('id_aux', $tipo);
		$query = $this->db->get();
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return $query->row()->max;
	}

	public function get_actividades(){
		$this->db->select("actividad_id actividad", false);
		$this->db->from('actividad_persona_th ap');
		$this->db->where("persona_id", $_SESSION['persona']);
		$query = $this->db->get()->result_array();
		$actividades = [];
		foreach ($query as $actividad) {
			array_push($actividades, $actividad['actividad']);
		}
		return $actividades;
	}

	public function get_actividades_asignadas(){
		$this->db->select("ap.actividad_id AS actividad, est.id_aux AS estado", false);
		$this->db->from('estados_actividades_th ea');
		$this->db->join('actividad_persona_th ap', 'ap.id = ea.actividad_id');
		$this->db->join('valor_parametro est', 'est.id = ea.estado_id');
		$this->db->where("persona_id", $_SESSION['persona']);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_actividades_ecargo($id_solicitud = null){
		$persona = $_SESSION['persona'];
		$this->db->select("ap.actividad_id AS actividad, est.id_aux AS estado, est.valor AS nombre_estado, est.valora AS valor_a, est.valorb AS valor_b, est.valorz AS valor_z", false);
		$this->db->from('estados_actividades_th ea');
		$this->db->join('actividad_persona_th ap', 'ap.id = ea.actividad_id');
		$this->db->join('valor_parametro est', 'est.id = ea.estado_id');
		$this->db->where("ap.actividad_id = 'Hum_Entr_Cargo' and ap.persona_id = $persona");
		if($id_solicitud) $this->db->where("est.valorz NOT IN (SELECT vp.valorz 
		FROM estados_solicitudes_talento es 
		JOIN valor_parametro vp ON vp.id_aux = es.estado_id  
		WHERE es.solicitud_id = $id_solicitud and vp.valorz = est.valorz )");
		$this->db->where("est.id_aux != 'Tal_Env' AND est.id_aux != 'Tal_TH' AND est.id_aux != 'Tal_Vb_Ter' AND est.id_aux != 'Tal_Ter'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_prestamos_activos($persona){
		$this->db->select("sth.id, p.valor_aprobado, p.cuotas_aprobadas, p.motivo, vp.id_aux AS tipo_prestamo, vp.valor AS tipo, e.valor AS estado, est.estado_id, p.fecha_registra", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join('prestamos_th p', 'sth.id = p.id_solicitud');
		$this->db->join('estados_solicitudes_talento est', 'p.id_solicitud = est.solicitud_id');
		$this->db->join('valor_parametro vp', 'p.tipo_prestamo = vp.id_aux');
		$this->db->join('valor_parametro e', 'est.estado_id= e.id_aux');
		$this->db->where("sth.usuario_registro", $persona);
		$this->db->where("est.estado_id", 'Tal_Des');
		/*Filtrar por tiempo. descartar prestamos ya cancelados*/
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_actividad_id($actividad, $persona){
		$query = $this->db->query('(SELECT id
		FROM actividad_persona_th
		WHERE actividad_id = ' . $actividad . '
		and usuario_registra = ' . $persona . ')');
		return $query->row();
	}
	
	public function get_cuotas(){
		$query = $this->db->query('(SELECT (SELECT valory FROM valor_parametro WHERE id_aux = "Pre_Cru") AS cruce, (SELECT valory FROM valor_parametro WHERE id_aux = "Pre_Lib") AS libre)');
		return $query->row();
	}

	public function listar_archivos_adjuntos($id){
    $this->db->select("ca.*,sth.id_estado_solicitud estado_solicitud, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) solicitante", false);
    $this->db->from('archivos_adj_th ca');
    $this->db->join('personas p', 'ca.usuario_registra = p.id');
    $this->db->join('solicitudes_talento_hum sth', 'ca.id_solicitud = sth.id');
		$this->db->where('sth.id', $id);
    $query = $this->db->get();
    return $query->result_array();
	}
	
	public function traer_correos_responsables_estado($state, $type){
		$this->db->select("p.correo, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) fullname", false);
		$this->db->from('estados_actividades_th eath');
		$this->db->join('actividad_persona_th apth', 'eath.actividad_id = apth.id');
		$this->db->join('personas p', 'apth.persona_id = p.id');
		$this->db->where('eath.estado_id', $state);
		$this->db->where('apth.actividad_id', $type);
    $query = $this->db->get();
    return $query->result_array();
	}

	public function obtener_postulacion_id($id)	{
		$perfil = $_SESSION['perfil'];
		$usuario = $_SESSION['persona'];
		$this->db->select("pr.valor programa,ps.id_departamento_actual_postulante id_departamento_actual, cra.valor cargo_actual,dpa.valor departamento_actual,p.id id_postulante, p.identificacion,p.id_tipo_identificacion,p.fecha_expedicion,p.lugar_expedicion,p.fecha_nacimiento,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,u2.valor tipo_identificacion,ps.*,cr.valor cargo,dp.valor departamento,f.valor formacion,ct.valor categoria,est.valor estado_solicitud,dp.id id_departamento,(SELECT COUNT(ep.id) FROM estados_postulantes ep WHERE ep.id_postulante = ps.id AND (ep.id_estado ='Pos_Bue' OR ep.id_estado ='Pos_Apr') AND ep.estado = 1) vb,(SELECT COUNT(epp.id) FROM estados_postulantes epp WHERE epp.id_postulante = ps.id AND (epp.id_estado ='Pos_Bue' OR epp.id_estado ='Pos_Apr' OR epp.id_estado ='Pos_Mal') AND epp.estado = 1 AND epp.usuario_registra = $usuario) tiene,(SELECT COUNT(ep.id) FROM estados_postulantes ep WHERE ep.id_postulante = ps.id AND (ep.id_estado ='Pos_Mal' OR ep.id_estado ='Pos_Neg') AND ep.estado = 1) vm,ti.valor tipo", false);
		$this->db->from('postulantes_csep ps');
		$this->db->join('personas p','ps.id_postulante = p.id');
		//$this->db->join('cargos_departamentos c', 'ps.id_cargo=c.id','left');
		$this->db->join('valor_parametro ti', 'ps.id_tipo=ti.id_aux');
		$this->db->join('valor_parametro cr', 'ps.id_cargo_postulante=cr.id','left');
		$this->db->join('valor_parametro dp', 'ps.id_departamento_postulante=dp.id','left');
		$this->db->join('valor_parametro pr', 'ps.id_programa=pr.id','left');
		$this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
		$this->db->join('valor_parametro f', 'ps.id_formacion=f.id','left');
		$this->db->join('valor_parametro ct', 'ps.id_categoria=ct.id','left');
		$this->db->join('valor_parametro est', 'ps.id_estado_solicitud=est.id_aux');
		//$this->db->join('cargos_departamentos ca', 'ps.id_cargo_actual=ca.id','left');
		$this->db->join('valor_parametro cra', 'ps.id_cargo_actual_postulante=cra.id','left');
		$this->db->join('valor_parametro dpa', 'ps.id_departamento_actual_postulante=dpa.id','left');
	  $this->db->where("ps.id",$id);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function cargar_permisos(){
		$this->db->select("id, actividad_id as tipo");
    	$this->db->from('actividad_persona_th ap');
		$this->db->where('ap.persona_id', $_SESSION['persona']);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_ultima_solicitud($tipo){
		$this->db->select("id, id_tipo_solicitud tipo, fecha_registro fecha, id_estado_solicitud estado, usuario_registro usuario");
		$this->db->from('solicitudes_talento_hum');
		$this->db->where('id_tipo_solicitud', $tipo);
		$this->db->where('usuario_registro', $_SESSION['persona']);
		$this->db->order_by('fecha_registro', 'desc');
		$this->db->limit(1);
		$query = $this->db->get();
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return $query->row();
	}

	public function guardar_vacante($data){
		$id = $this->guardar_datos($data['solicitud'], 'solicitudes_talento_hum');
		if ($id) {
			$data['vacante']['solicitud_id'] = $id;
			$res = $this->guardar_datos(array_filter($data['vacante'], function($var){ return $var !== ""; }), 'vacantes');
			$this->guardar_datos([
				'solicitud_id' => $id,
				'estado_id' => $data['solicitud']['id_estado_solicitud'],
				'usuario_id' => $_SESSION['persona'],
			], 'estados_solicitudes_talento');
			if($data['solicitud']['id_tipo_solicitud'] === 'Hum_Prec'){
				if (count($data["materias"]) > 0) {
					$materias = [];
					foreach ($data["materias"] as $materia) {
						$subject["solicitud_id"] = $id;
						$subject["materia"] = $materia["materia"];
						$materias[] = $subject;
					}
					if($res) $res = $this->guardar_datos($materias, 'materias_vacante', 2);
				}
				if (count($data["programas"]) > 0) {
					$programas = [];
					foreach ($data["programas"] as $programa) {
						$p["programa_id"] = $programa["id"];
						$p["solicitud_id"] = $id;
						$programas[] = $p;
					}
					if($res) $res = $this->guardar_datos($programas, 'programas_vacante', 2);
				}
			} else {
				$res = $this->guardar_datos([
					'solicitud_id' => $id,
					'programa_id' => $data['vacante']['departamento_id'],
				], 'programas_vacante');
			}
		}
		return $id;
	}

	public function get_detalle_vacante($id){
		$this->db->select("v.id, v.tipo_solicitud, v.tipo_vacante, v.horas, v.posgrado, v.pregrado, v.tipo_cargo, v.cargo_id, v.linea_investigacion, v.anos_experiencia, v.hoja_vida, ts.valor AS t_solicitud, tv.valor AS t_vacante, p.id persona_id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) AS fullname, CONCAT(p1.nombre,' ',p1.apellido,' ',p1.segundo_apellido) AS solicitante, car.valor as cargo, v.departamento_id id_departamento, v.plan_trabajo, dep.valor as departamento, sth.fecha_registro, sth.id_tipo_solicitud, v.plan_trabajo, v.experiencia_laboral, v.observaciones, v.nombre_cargo, v.tipo_contrato, vpt.valor nombre_tipo_contrato, v.duracion_contrato, v.vb_pedagogico", false);
		$this->db->from('vacantes v');
		$this->db->join('solicitudes_talento_hum sth', 'sth.id = v.solicitud_id');
		$this->db->join('personas p1', 'p1.id = sth.usuario_registro');
		$this->db->join('valor_parametro car', 'v.cargo_id = car.id');
		$this->db->join('valor_parametro ts', 'ts.id_aux = v.tipo_solicitud');
		$this->db->join('valor_parametro tv', 'tv.id_aux = v.tipo_vacante');
		$this->db->join('valor_parametro dep', 'v.departamento_id = dep.id');
		$this->db->join('valor_parametro vpt', 'v.tipo_contrato = vpt.id_aux','left');
		$this->db->join('personas p', 'p.id = v.reemplazado_id', 'left');
		$this->db->where('v.solicitud_id', $id);
		$query = $this->db->get();
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		$data['vacante'] = $query->row();
		$data['subjects'] = $this->get_materias_vacante($id);
		$data['programs'] = $this->get_programas_vacante($id);
		$data['user'] = $_SESSION['perfil'] === 'Per_Csep' ? 1 : 0;
		return $data;
	}

	public function get_materias_vacante($id){
		$this->db->select("mv.materia");
		$this->db->from('materias_vacante mv');
		$this->db->where('mv.solicitud_id', $id);
		$this->db->where('mv.estado', 1);
		$query = $this->db->get();
    	return $query->result_array();
	}

	public function get_programas_vacante($id){
		$this->db->select("pv.programa_id id, p.valor nombre");
		$this->db->from('programas_vacante pv');
		$this->db->join('valor_parametro p', 'pv.programa_id = p.id');
		$this->db->where('pv.solicitud_id', $id);
		$this->db->where('pv.estado', 1);
		$query = $this->db->get();
    	return $query->result_array();
	}

	public function buscar_dependencia($buscar){
		$this->db->select("id, valor nombre", false);
		$this->db->from('valor_parametro');
		$this->db->where("idparametro", 3);
		//$this->db->where("valory", 2);
		$this->db->like("valor", $buscar);
		$this->db->order_by("valor", 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_estados_solicitud($id){
		$this->db->select("est.estado_id, est.fecha, vp.valor estado, CONCAT(p.nombre, ' ' ,p.apellido, ' ', p.segundo_apellido) fullname ",  false);
		$this->db->from('estados_solicitudes_talento est');
		$this->db->join("valor_parametro vp", "vp.id_aux = est.estado_id");
		$this->db->join("personas p", "p.id = est.usuario_id");
		$this->db->where("est.solicitud_id", $id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_estados_ecargo($id){
		$this->db->select("est.estado_id, est.fecha,est.comentario, vp.valor estado, CONCAT(p.nombre, ' ' ,p.apellido, ' ', p.segundo_apellido) fullname ",  false);
		$this->db->from('estados_solicitudes_talento est');
		$this->db->join("valor_parametro vp", "vp.id_aux = est.estado_id");
		$this->db->join("personas p", "p.id = est.usuario_id");
		$this->db->where("est.solicitud_id", $id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_personas_notificar($actividad,$estado,$departamento=null) {
		$this->db->select("p.correo, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona", false);
		$this->db->from('actividad_persona_th ap');
		$this->db->join("estados_actividades_th ep", "ap.id = ep.actividad_id");
		$this->db->join("personas p", "ap.persona_id = p.id");
		$this->db->join("valor_parametro est", "est.id = ep.estado_id");
		$this->db->where('est.id_aux',$estado);
		$this->db->where("ap.actividad_id = '$actividad'");
		if($departamento){
			$this->db->where("(SELECT COUNT(ea_s.id)
			FROM estados_actividades_th ea_s
			INNER JOIN actividad_persona_th ap_s ON ap_s.id = ea_s.actividad_id
			WHERE ap_s.actividad_id = '$actividad'
			AND ap_s.persona_id = p.id
			AND ea_s.estado_id = $departamento
			AND ea_s.notificacion = 1) > 0");
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function cargar_materias_solicitud($id){
		$this->db->select("mv.id, mv.materia", false);
		$this->db->from('materias_vacante mv');
		$this->db->where('mv.solicitud_id', $id);
		$this->db->where('mv.estado', 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function cargar_programas_solicitud($id){
		$this->db->select("pv.id, vp.valor as programa");
		$this->db->from('programas_vacante pv');
		$this->db->join("valor_parametro vp", "vp.id = pv.programa_id");
		$this->db->where('pv.solicitud_id', $id);
		$this->db->where('pv.estado', 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_estados_asignados_actividad($actividad){
		$this->db->distinct();
		$this->db->select("pp.vp_secundario estado, e.valor nombre");
		$this->db->from('permisos_parametros pp');
		$this->db->join("valor_parametro vp", "pp.vp_principal = vp.id_aux");
		$this->db->join("valor_parametro e", "pp.vp_secundario = e.id_aux AND e.idparametro = 70");
		if($actividad) $this->db->where('pp.vp_principal', $actividad);
		$this->db->where('vp.estado', 1);
		$this->db->where('e.estado', 1);
		$this->db->where('pp.estado', 1);
		$this->db->order_by('e.valor');
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Recibe nombre del modulo actual de la URL y obtiene los tipos de solicitudes asociadas en la tabla permisos parametros
	 * @param String $vista
	 * @return Array $tipos_solicitudes 
	 */
	public function cargar_tipos_solicitudes_filtro($vista){
		$administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Admin_Tal" ? true :false;
		$this->db->select("vp.valor nombre_tipo, vp.id_aux id_tipo");
		$this->db->from('permisos_parametros pp');
		$this->db->join("valor_parametro vp", "vp.id_aux = pp.vp_secundario");
		if(!$administra) $this->db->join('actividad_persona_th ap', "ap.actividad_id = vp.id_aux AND ap.persona_id = ".$_SESSION['persona']);
		$this->db->where('pp.vp_principal', $vista);
		$this->db->where('vp.idparametro', 54);
		$tipos_solicitudes = $this->db->get();
		return $tipos_solicitudes->result_array();
	}


	public function candidato_asignado($solicitud, $candidato){
		$this->db->select("IF(COUNT(id) > 0, id, 0) existe", false);
		$this->db->from('candidatos_seleccion');
		$this->db->where('solicitud_id', $solicitud);
		$this->db->where('candidato_id', $candidato);
		$this->db->where('estado', 1);
		$existe = $this->db->get()->row()->existe;
		return $existe;
	}

	public function listar_candidatos($id){
		$this->db->select("cs.id candidatos_seleccion_id, cs.candidato_id id, ubi.valor ubicacion_entrevista, lug.valor lugar_entrevista, p.identificacion, CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, p.nombre, p.correo, p.telefono, vp.valor proceso_actual, vp.id_aux proceso_actual_id, cs.fecha_entrevista, cs.fecha_registra, cs.observacion, cs.hoja_vida, cs.aprobacion_jefe, cs.solicitar_vb_jefe, cs.contratado, car.valor cargo, car_a.valor cargo_actual, CONCAT(j.nombre,' ', j.apellido,' ', j.segundo_apellido) as jefe_responsable, cs.motivo_rechazo_jefe", false);
		$this->db->from('candidatos_seleccion cs');
		$this->db->join("seleccion s", "cs.solicitud_id = s.solicitud_id");
		// $this->db->join("cargos_departamentos cd", "cd.id = s.cargo_id");
		$this->db->join("valor_parametro car", "car.id = s.cargo_id");
		$this->db->join("personas p", "cs.candidato_id = p.id");
		$this->db->join("personas j", "cs.encargado_entrevista = j.id",'left');
		$this->db->join("valor_parametro car_a", "car_a.id = p.id_cargo_sap",'left');
		$this->db->join("permisos_parametros pp", "pp.id = cs.ubicacion_entrevista_id", 'left');
		$this->db->join("valor_parametro ubi", "ubi.id = pp.vp_secundario_id", 'left');
		$this->db->join("valor_parametro lug", "lug.id = pp.vp_principal_id", 'left');
		$this->db->join("valor_parametro vp", "vp.id_aux = proceso_actual_id");
		$this->db->where('cs.solicitud_id', $id);
		$this->db->where('cs.estado', 1);
		$candidatos = [];
		$data = $this->db->get()->result_array();
		$procesos = 0;
		foreach ($data as $candidato) {
			$candidato['procesos'] = $this->get_historial_procesos($id, $candidato['id']);
			$candidatos[] = $candidato;
		}
		return $candidatos;
	}

	public function cargar_ubicaciones($id){
		$this->db->select("vp.valor, pp.id");
		$this->db->from('permisos_parametros pp');
		$this->db->join('valor_parametro vp', 'vp.id = pp.vp_secundario_id');
		$this->db->where('pp.vp_principal_id', $id);
		$ubicaciones = $this->db->get();
		return $ubicaciones->result_array();
	}

	public function get_info_candidato($solicitud, $candidato){
		$this->db->select("id, solicitud_id, candidato_id, fecha_registra, proceso_actual_id, aprobacion_jefe", false);
		$this->db->from('candidatos_seleccion');
		$this->db->where('solicitud_id', $solicitud);
		$this->db->where('candidato_id', $candidato);
		$this->db->where('estado', 1);
		$info = $this->db->get()->row();
		return $info;
	}

	public function get_cantidad_candidatos($solicitud){
		$this->db->select("COUNT(*) candidatos", false);
		$this->db->from('candidatos_seleccion');
		$this->db->where('solicitud_id', $solicitud);
		$this->db->where('estado', 1);
		return  $this->db->get()->row()->candidatos;
	}

	public function get_pruebas_asignadas(){
		$this->db->select("id, id_aux, valor nombre", false);
		$this->db->from('valor_parametro');
		$this->db->where('(idparametro = 53) AND (valory = 1 OR valory = 2)');
		$this->db->where('estado', 1);
		$ubicaciones = $this->db->get();
		return $ubicaciones->result_array();
	}

	public function get_pruebas_seleccionadas($pruebas){
		$this->db->select("pp.vp_secundario route_file_name, vp.valor file_name, vp.valory prueba, pp.vp_principal_id tipo");
		$this->db->from('permisos_parametros pp');
		$this->db->JOIN('valor_parametro vp', 'vp.id = pp.vp_secundario_id');
		foreach ($pruebas as $prueba) $this->db->or_where('vp_principal_id', $prueba);
		$this->db->where('pp.estado', 1);
		$this->db->where('vp.estado', 1);
		$this->db->order_by("pp.vp_principal_id", "desc");
		$pruebas = $this->db->get();
		return $pruebas->result_array();
	}

	public function get_info_seleccion($solicitud_id, $candidato_id){
		$this->db->select("s.nombre_vacante, cs.candidato_id id, ubi.valor ubicacion_entrevista, lug.valor lugar_entrevista, CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, vp.valor proceso_actual, vp.id_aux proceso_actual_id, cs.fecha_entrevista, cs.fecha_registra, cs.observacion, cs.hoja_vida, CONCAT(p1.nombre, ' ', p1.segundo_nombre, ' ', p1.apellido, ' ', p1.segundo_apellido) encargado", false);
		$this->db->from('candidatos_seleccion cs');
		$this->db->join("personas p", "cs.candidato_id = p.id");
		$this->db->join("personas p1", "cs.encargado_entrevista = p1.id", 'left');
		$this->db->join("permisos_parametros pp", "pp.id = cs.ubicacion_entrevista_id");
		$this->db->join("valor_parametro ubi", "ubi.id = pp.vp_secundario_id");
		$this->db->join("valor_parametro lug", "lug.id = pp.vp_principal_id");
		$this->db->join("valor_parametro vp", "vp.id_aux = proceso_actual_id");
		$this->db->join("seleccion s", "s.solicitud_id = cs.solicitud_id");
		$this->db->where('cs.solicitud_id', $solicitud_id);
		$this->db->where('cs.candidato_id', $candidato_id);
		$this->db->where('cs.estado', 1);
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		$info = $this->db->get()->row();
		return $info;
	}

	public function get_info_entrevista_jefe($solicitud_id, $candidato_id){
		$this->db->select("s.nombre_vacante, cs.candidato_id id, ubi.valor ubicacion_entrevista, lug.valor lugar_entrevista, CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, vp.valor proceso_actual, vp.id_aux proceso_actual_id, cs.fecha_entrevista, cs.fecha_registra, cs.observacion, cs.hoja_vida, CONCAT(p1.nombre, ' ', p1.segundo_nombre, ' ', p1.apellido, ' ', p1.segundo_apellido) encargado, p1.correo correo_encargado", false);
		$this->db->from('candidatos_seleccion cs');
		$this->db->join("personas p", "cs.candidato_id = p.id");
		$this->db->join("personas p1", "cs.encargado_entrevista = p1.id", 'left');
		$this->db->join("permisos_parametros pp", "pp.id = cs.ubicacion_entrevista_jefe");
		$this->db->join("valor_parametro ubi", "ubi.id = pp.vp_secundario_id");
		$this->db->join("valor_parametro lug", "lug.id = pp.vp_principal_id");
		$this->db->join("valor_parametro vp", "vp.id_aux = proceso_actual_id");
		$this->db->join("seleccion s", "s.solicitud_id = cs.solicitud_id");
		$this->db->where('cs.solicitud_id', $solicitud_id);
		$this->db->where('cs.candidato_id', $candidato_id);
		$this->db->where('cs.estado', 1);
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		$info = $this->db->get()->row();
		return $info;
	}

	public function get_where($tabla, $data){
		return $this->db->get_where($tabla, $data);
	}

	public function get_info_persona($persona){
		if(empty($persona)) $persona = $_SESSION['persona'];
		$this->db->select("p.id persona_id, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, p.identificacion, p.correo, p.telefono, p.fecha_nacimiento", false);
		$this->db->from('personas p');
		$this->db->where('p.id', $persona);
		$this->db->where('p.estado', 1);
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		$info = $this->db->get()->row();
		return $info;
	}

	public function get_info_persona_cert($id){
		$this->db->select("p.id persona_id, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, p.identificacion, p.correo, p.telefono, p.fecha_nacimiento, vp.valor cargo, p.lugar_expedicion, p.tipo_contrato, p.fecha_inicio_contrato, p.sueldo", false);
		$this->db->from('personas p');
		$this->db->join("valor_parametro vp", "vp.id = p.id_cargo_sap");
		$this->db->where('p.id', $id);
		$this->db->where('p.estado', 1);
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		$info = $this->db->get()->row();
		return $info;
	}

	public function get_full_info_candidato($solicitud, $candidato){
		$perfil = $_SESSION['perfil'];
		$filtro = $perfil == 'Per_Csep' || $perfil == 'Per_Admin' || $perfil == 'Per_Admin_Tal' ? true : false;
		$this->db->select("p.id persona_id, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, p.identificacion, p.correo, p.telefono, p.fecha_nacimiento, 
		p.id_tipo_identificacion, p.lugar_expedicion, p.genero,p.nombre, p.segundo_nombre, p.apellido, p.segundo_apellido, 
		cs.categoria_colciencias, cs.indiceh, s.cargo_id, car.valor cargo, dep.id departamento_id, 
		dep.valor departamento, cs.id candidato_seleccion_id, cs.cvlac,cs.exp_docente,cs.exp_investigacion,
		exp_profesional,cs.produccion,cs.pruebas, cs.suficiencia_ingles,cs.concepto, s.tipo_cargo_id, s.nombre_vacante, 
		cs.hoja_vida, cs.aprobacion_jefe, cs.informe_seleccion, cs.formacion, cs.id_csep, cs.seleccion_aprobada, cs.motivo_exoneracion,
		cs.solicitar_vb_jefe, cs.solicitar_examenes_med, cs.fecha_ingreso, cs.salario, cs.duracion_contrato, cs.tipo_contrato_id, cs.reemplazado,
		CONCAT(pr.nombre, ' ', pr.apellido, ' ', pr.segundo_apellido) nombre_reemplazado, tc.valor nombre_tipo_contrato, cs.proceso_actual_id, cs.motivo_rechazo_jefe", false);
		$this->db->from('candidatos_seleccion cs');
		$this->db->join("personas p", "cs.candidato_id = p.id");
		$this->db->join("personas pr", "cs.reemplazado = pr.id",'left');
		$this->db->join("solicitudes_talento_hum sth", "sth.id = cs.solicitud_id");
		$this->db->join("seleccion s", "s.solicitud_id = cs.solicitud_id");
		$this->db->join("valor_parametro car", "car.id = s.cargo_id");
		$this->db->join("valor_parametro dep", "dep.id = s.departamento_id");
		$this->db->join("valor_parametro tc", "tc.id_aux = cs.tipo_contrato_id",'left');
		$this->db->where('cs.solicitud_id', $solicitud);
		$this->db->where('cs.candidato_id', $candidato);
		$this->db->where('cs.estado', 1);
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		$info = $this->db->get()->row();
		$info_candidato = $this->get_info_candidato($solicitud, $candidato);
		$info->{'estudios_candidato'} = $this->get_estudios_candidato($info_candidato->{'id'});
		$info->{'competencias_candidato'} = $this->info_candidato_competencias($solicitud, $candidato);
		$avales = $filtro ? $this->get_where('archivos_adj_th', ['id_persona' => $info_candidato->{'id'}, 'id_solicitud' => $solicitud])->result_array() : [];
		if($info->{'informe_seleccion'}) array_push($avales, ['nombre_archivo' => $info->{'informe_seleccion'}, 'nombre_real' => 'Informe de Selección', 'route' => 1]);
		if($info->{'hoja_vida'}) array_push($avales, ['nombre_archivo' => $info->{'hoja_vida'}, 'nombre_real' => 'Hoja de Vida', 'route' => 1]);
		$info->{'avales'} = $avales;
		return $info;
	}

	public function get_estudios_candidato($id){
		$this->db->select("formacion, universidad, tipo_formacion, fecha_graduacion");
		$this->db->from('estudios_candidatos');
		$this->db->where('candidato_seleccion_id', $id);
		$this->db->where('estado', 1);
		$estudios = $this->db->get()->result_array();
		return $estudios;
	}

	public function cargar_procesos_disponibles($solicitud, $candidato){
		$info_candidato = $this->get_info_candidato($solicitud, $candidato);
		$id = $info_candidato->{'id'};
		$this->db->select("vp.id_aux id, vp.valor nombre, pc.id ok, vp.valory tipo");
		$this->db->from('valor_parametro vp');
		$this->db->join('procesos_candidatos pc', "vp.id_aux = pc.proceso_id AND pc.candidato_seleccion_id = $id", 'left');
		$this->db->join('candidatos_seleccion cs', "cs.id = pc.candidato_seleccion_id", 'left');
		$this->db->where('vp.idparametro', 19);
		$this->db->where('vp.estado', 1);
		$this->db->order_by('CAST(vp.valorx AS UNSIGNED)', 'ASC');
		$procesos_seleccion = $this->db->get()->result_array();
		return $procesos_seleccion;
	}

	public function get_historial_procesos($solicitud, $candidato){
		$candidato_seleccion = $this->get_full_info_candidato($solicitud, $candidato);
		$this->db->select("proceso_id proceso");
		$this->db->from('procesos_candidatos');
		$this->db->where('candidato_seleccion_id', $candidato_seleccion->{'candidato_seleccion_id'});
		$this->db->where('estado', 1);
		$res = $this->db->get()->result();
		$procesos = [];
		foreach ($res as $row){
			$procesos[] = $row->proceso;
		}
		return $procesos;
	}

	public function info_candidato_reporte($id){
		$this->db->select("ec.formacion, ec.universidad, ec.fecha_graduacion, vp.valor tipo");
		$this->db->from('estudios_candidatos ec');
		$this->db->join('valor_parametro vp', "vp.id = ec.tipo_formacion");
		$this->db->where('candidato_seleccion_id', $id);
		$this->db->where('ec.estado', 1);
		$estudios = $this->db->get()->result_array();
		return $estudios;
	}

	public function get_historial_candidato($id){
		$this->db->select("pro.valor proceso, pc.fecha_registra fecha, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, pro.id_aux id_proceso, pc.notificacion", false);
		$this->db->from('procesos_candidatos pc');
		$this->db->join('valor_parametro pro', 'pro.id_aux = pc.proceso_id');
		$this->db->join('personas p', 'p.id = pc.usuario_registra');
		$this->db->where('candidato_seleccion_id', $id);
		$procesos = $this->db->get()->result_array();
		return $procesos;
	}

	public function solicitud_cerrada($id){
		$this->db->select("s.numero_vacantes, sth.id_estado_solicitud");
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join("seleccion s", "s.solicitud_id = sth.id");
		$this->db->where('sth.id', $id);
		$this->db->where('sth.estado', 1);
		$info = $this->db->get()->row();
		$vacantes = $info->numero_vacantes;
		$contratados = $this->candidatos_contratados($id);
		return [
			'cerrada' => (int)$vacantes <= (int)$contratados ? 1 : 0, 
			'estado' => $info->id_estado_solicitud
		];
	}

	public function candidatos_contratados($solicitud){
		$this->db->select("COUNT(id) cantidad", false);
		$this->db->from('candidatos_seleccion');
		$this->db->where('solicitud_id', $solicitud);
		$this->db->where('contratado', 1);
		$cantidad = $this->db->get()->row()->cantidad;
		return $cantidad;
	}

	public function get_correos_participantes_descartados($id){
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona, p.correo", false);
		$this->db->from('candidatos_seleccion cs');
		$this->db->join('personas p', 'p.id = cs.candidato_id');
		$this->db->where('cs.solicitud_id',  $id);
		$this->db->where('cs.contratado <> 1');
		$candidatos = $this->db->get()->result_array();
		return $candidatos;
	}

	public function esResponsable($id){
		// $this->db->select("responsable_id");
		$this->db->select("jefe_inmediato");
		$this->db->from('solicitudes_talento_hum');
		$this->db->where('id', $id);
		$responsable = $this->db->get()->row()->jefe_inmediato;
		return $responsable == $_SESSION['persona'];
	}

	public function esresponsable_proceso($id){
		$this->db->select("responsable_id,usuario_registro");
		$this->db->from('solicitudes_talento_hum');
		$this->db->where('id', $id);
		$query = $this->db->get();
		$row = $query->row();
		$responsable_id = $row->responsable_id;
		$usuario_registro = $row->usuario_registro;
		return $responsable_id == $_SESSION['persona'] || $usuario_registro == $_SESSION['persona'] ? true : false;
	}

	public function validar_csep($id){
		$this->db->select("COUNT(id) registrado", false);
		$this->db->from('procesos_candidatos');
		$this->db->where('candidato_seleccion_id',  $id);
		$this->db->where("(proceso_id = 'Sel_Cpre' OR proceso_id = 'Sel_CVir')");
		$registrado = $this->db->get()->row()->registrado;
		return $registrado > 0 ? 1 : 0;
	}

	public function get_usuarios_a_notificar($actividad, $estado, $motivo=null){
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona, p.correo", false);
		$this->db->from('estados_actividades_th ea');
		$this->db->join('actividad_persona_th ap', 'ap.id = ea.actividad_id');
		$this->db->join('personas p', 'ap.persona_id = p.id');
		$this->db->_protect_identifiers = false;
		$this->db->join('valor_parametro est', "est.id_aux = '$estado'");
		$this->db->_protect_identifiers = true;
		$this->db->where('ap.actividad_id',  $actividad);
		$this->db->where('ea.notificacion', 1);
		if($motivo != "renuncia_ecargo" && $actividad == "Hum_Entr_Cargo"){
            $this->db->where("(est.id = ea.estado_id OR ea.estado_id IN (SELECT PP.vp_secundario_id FROM permisos_parametros PP INNER JOIN valor_parametro vpx ON PP.vp_secundario_id = vpx.id WHERE PP.vp_principal = '$estado' and PP.estado = 1 AND vpx.valorz <> 'vb_contabilidad'))");
        }else{
			$this->db->where("(est.id = ea.estado_id OR ea.estado_id IN (SELECT PP.vp_secundario_id FROM permisos_parametros PP WHERE PP.vp_principal = '$estado' and PP.estado = 1))");
		}
		$candidatos = $this->db->get()->result_array();
		
		return $candidatos;
	}

	public function get_usuarios_a_notificar_estado_posgrado($estado, $departamento) {
		$sql = "(
			SELECT CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona, p.correo
			FROM estados_actividades_th ea
			INNER JOIN actividad_persona_th ap ON ap.id = ea.actividad_id
			INNER JOIN personas p ON ap.persona_id = p.id
			INNER JOIN valor_parametro est ON est.id_aux = '$estado' AND est.id = ea.estado_id			
			WHERE ap.actividad_id = 'Hum_Posg'
			AND ea.notificacion = 1
			AND (
				SELECT COUNT(ea_s.id)
				FROM estados_actividades_th ea_s
				INNER JOIN actividad_persona_th ap_s ON ap_s.id = ea_s.actividad_id
				WHERE ap_s.actividad_id = 'Hum_Posg'
				AND ap_s.persona_id = p.id
				AND ea_s.estado_id = $departamento
				AND ea_s.notificacion = 1
			) > 0
		)";
		return $this->db->query($sql)->result_array();
	}

	public function get_usuarios_a_notificar_estado_anteriores($id_solicitud, $personas_notificar){
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona, p.correo", false);
		$this->db->from('personas p');
		$this->db->join('estados_solicitudes_talento eth', 'eth.usuario_id = p.id');
		$this->db->where("eth.solicitud_id = $id_solicitud and (eth.estado_id = 'Tal_Env' or eth.estado_id = 'Tal_Pro')");
		foreach ($personas_notificar as $row) $this->db->where("p.correo <> '".$row['correo']."'");
		return $this->db->get()->result_array();
	}

	public function get_correos_decanos($departamento){
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona, p.correo", false);
		$this->db->from('estados_actividades_th ea');
		$this->db->join('actividad_persona_th ap', 'ap.id = ea.actividad_id');
		$this->db->join('personas p', 'ap.persona_id = p.id');
		$this->db->where('ea.estado_id',  $departamento);
		$this->db->where('ea.notificacion', 1);
		$candidatos = $this->db->get()->result_array();
		return $candidatos;
	}

	public function cargar_requisiciones(){
		$this->db->select("v.tipo_vacante, v.horas, v.pregrado, v.posgrado, v.linea_investigacion, v.anos_experiencia, v.hoja_vida, v.experiencia_laboral, v.observaciones, sth.id, sth.fecha_registro, sth.id_tipo_solicitud, v.cargo_id, v.tipo_cargo, p.id responsable_id, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, vp.valor as tipo_solicitud, dep.valor departamento, dep.id departamento_id, cd.id dd, car.valor cargo", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join('vacantes v', 'sth.id = v.solicitud_id');
		$this->db->join('personas p', 'sth.usuario_registro = p.id');
		$this->db->join('valor_parametro vp', 'sth.id_tipo_solicitud = vp.id_aux');
		$this->db->join('cargos_departamentos cd', 'cd.id = v.cargo_id');
		$this->db->join('valor_parametro dep', 'dep.id = cd.id_departamento');
		$this->db->join('valor_parametro car', 'car.id = cd.id_cargo');
		$this->db->where("(id_tipo_solicitud = 'Hum_Admi' OR id_tipo_solicitud = 'Hum_Prec')");
		$this->db->where('sth.id_estado_solicitud', 'Tal_Ter');
		$this->db->where('sth.estado', 1);
		$solicitudes = $this->db->get()->result_array();
		return $solicitudes;
	}

	public function cargar_dependencias(){
		$this->db->select("id, valor");
		$this->db->from('valor_parametro');
		$this->db->where('idparametro', 3);
		$this->db->where('estado', 1);
		$candidatos = $this->db->get()->result_array();
		return $candidatos;
	}

	public function get_correo_responsable_th($id){
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona, p.correo", false);
		$this->db->from('candidatos_seleccion cs');
		$this->db->join('solicitudes_talento_hum sth', 'sth.id = cs.solicitud_id');
		$this->db->join('personas p', 'p.id = sth.responsable_id');
		$this->db->where("cs.id", $id);
		$this->db->where('sth.estado', 1);
		$this->db->where('cs.estado', 1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function get_correo_solicitante($id){
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona, p.correo", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join('personas p', 'p.id = sth.usuario_registro');
		$this->db->where("sth.id", $id);
		$this->db->where('sth.estado', 1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function get_opciones_certificado($activos = false){
		$this->db->select("o.id, o.valor opcion, o.id_aux aux, IF(`pp`.`id` IS NOT NULL, TRUE, FALSE)  asignado", false);
		$this->db->from('valor_parametro o');
		$this->db->_protect_identifiers = false;
		if($activos) $this->db->join('permisos_parametros pp', "pp.vp_principal = 'Hum_Cert' AND o.id = pp.vp_secundario_id");
		else $this->db->join('permisos_parametros pp', "pp.vp_principal = 'Hum_Cert' AND o.id = pp.vp_secundario_id", 'left');
		$this->db->_protect_identifiers = true;
		$this->db->where('o.idparametro', 189);
		$this->db->where('o.estado', 1);
		$opciones = $this->db->get()->result_array();
		return $opciones;
	}

	public function detalle_requisicion_posgrado($id) {
		$this->db->select("dr.nombre_modulo, dr.horas_modulo, dr.numero_promocion, car.id cargo_id, car.valor cargo, dr.valor_hora, dr.ciudad_origen, dr.id_candidato, dr.tipo_vacante id_tipo_vacante, dr.id_departamento, dr.fecha_inicio, dr.fecha_terminacion, dr.observacion, dr.documentos, tc.valor tipo_vacante, CONCAT(r.nombre, ' ', r.apellido, ' ', r.segundo_apellido) AS reemplazado, r.id reemplazado_id, , CONCAT(c.nombre, ' ', c.apellido, ' ', c.segundo_apellido) AS candidato, dep.valor departamento, tp.valor tipo_programa, tp.id tipo_programa_id, th.id_estado_solicitud estado, t.valor tipo_orden, cs.valor codigo_sap, dr.id_programa, pro.valor programa", false);
		$this->db->from('detalle_requisicion dr');
		$this->db->join('solicitudes_talento_hum th', "th.id = dr.solicitud_id");
		$this->db->join('valor_parametro tc', "tc.id_aux = dr.tipo_vacante");
		$this->db->join('valor_parametro t', "t.id = dr.tipo_orden", 'left');
		$this->db->join('valor_parametro dep', "dep.id = dr.id_departamento");
		$this->db->join('valor_parametro tp', "tp.id = dr.tipo_programa");
		$this->db->join('valor_parametro car', "car.id = dr.id_cargo");
		$this->db->join('valor_parametro cs', "cs.id = dr.codigo_sap", 'left');
		$this->db->join('valor_parametro pro', "pro.id = dr.id_programa", 'left');
		$this->db->join('personas r', "r.id = dr.id_reemplazado", 'left');
		$this->db->join('personas c', "c.id = dr.id_candidato");
		$this->db->where('dr.solicitud_id', $id);
		$this->db->where('dr.estado', 1);
		$detalle = $this->db->get()->row();
		return $detalle;
	}

	public function get_permisos_rol(){
		$this->db->select("cp.id_parametro departamento");
		$this->db->from('csep_parametros_persona cp');
		$this->db->join("valor_parametro vp", "vp.id = cp.id_parametro");
		$this->db->where('cp.id_persona', $_SESSION['persona']);
		$this->db->where('cp.tipo', "decano");
		$this->db->where('vp.idparametro', 91);
		$this->db->where('vp.estado', 1);
		$this->db->where('cp.estado', 1);
		$decanatura = $this->db->get()->row();
		return $decanatura;
	}

	public function get_cargos($opt){
		$this->db->select("id, valor");
		$this->db->from('valor_parametro');
		$this->db->where('idparametro', 2);
		$this->db->where('valory', $opt);
		$this->db->where('estado', 1);
		$cargos = $this->db->get()->result_array();
		return $cargos;
	}

	public function get_decano(){
		$this->db->select("cp.id_parametro departamento");
		$this->db->from('csep_parametros_persona cp');
		$this->db->join("valor_parametro vp", "vp.id = cp.id_parametro");
		$this->db->where('cp.tipo', "decano");
		$this->db->where('vp.idparametro', 91);
		$this->db->where('vp.estado', 1);
		$this->db->where('cp.estado', 1);
		$decanatura = $this->db->get()->row();
    return $decanatura;
	}

	public function get_departamentos_asignados($dep) {
		$this->db->select("dep.id departamento");
		$this->db->from('estados_actividades_th ea');
		$this->db->join("actividad_persona_th ap", "ea.actividad_id = ap.id");
		$this->db->join("valor_parametro dep", "ea.estado_id = dep.id");
		$this->db->where('ap.actividad_id', $dep);
		$this->db->where('ap.persona_id', $_SESSION['persona']);
		$this->db->where('dep.idparametro', 91);
		$this->db->where('ea.estado', 1);
		$this->db->where('ap.estado', 1);
		$this->db->where('dep.estado', 1);
		$departamentos = $this->db->get()->result_array();
		$estados = $this->get_estados_departamento($dep);
    return [
			'departamentos' => $departamentos,
			'estados' => $estados
		];
	}

	public function get_estados_departamento($dep) {
		$this->db->select("est.id_aux estado");
		$this->db->from('estados_actividades_th ea');
		$this->db->join("actividad_persona_th ap", "ea.actividad_id = ap.id");
		$this->db->join("valor_parametro est", "ea.estado_id = est.id");
		$this->db->where('ap.actividad_id', $dep);
		$this->db->where('ap.persona_id', $_SESSION['persona']);
		$this->db->where('est.idparametro', 70);
		$this->db->where('ea.estado', 1);
		$this->db->where('ap.estado', 1);
		$this->db->where('est.estado', 1);
		$estados = $this->db->get()->result_array(); 
		return $estados;
	}
	

	public function get_detalle_solicitud_arl($id_solicitud, $tabla, $riesgo){
		$this->db->select("t.*, p.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS beneficiario $riesgo", false);
		$this->db->from("$tabla t");
		if($tabla == 'afiliacion_arl_th') $this->db->join("visitantes p", "t.id_persona = p.id");
		else $this->db->join("personas p", "t.id_persona = p.id");
		if($riesgo) $this->db->join("valor_parametro vp", "vp.id = t.id_nivel_riesgo");
		$this->db->where('t.id_solicitud', $id_solicitud);
		$row = $this->db->get()->row();
		return $row;
	}

	public function buscar_persona_arl($where, $tipo=''){
		if($tipo == 1){
			$this->db->select("p.identificacion,p.id,p.nombre,p.segundo_nombre,p.apellido,p.segundo_apellido,p.correo,p.tipo_identificacion as id_tipo_identificacion,p.fecha_nacimiento,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,u2.valor tipo_identificacion, p.genero, p.celular as telefono, p.correo, p.eps", false);
			$tabla = 'visitantes';
			$filtro = 'p.tipo_identificacion=u2.id';
		}else{
			$this->db->select("p.identificacion,p.id,p.nombre,p.segundo_nombre,p.apellido,p.segundo_apellido,p.correo,p.id_tipo_identificacion,p.fecha_expedicion,p.lugar_expedicion,p.fecha_nacimiento,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,u2.valor tipo_identificacion, p.genero, p.telefono, p.correo, p.eps", false);
			$tabla = 'personas';
			$filtro = 'p.id_tipo_identificacion=u2.id';
		}
		$this->db->from("$tabla p");
		$this->db->join('valor_parametro u2', $filtro);
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function buscar_persona_ausentismo($where){
		$this->db->select("p.*,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
		$this->db->from('personas p');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function traer_programas_req($tipo) {
		$this->db->select("vp.id, vp.valor");
		$this->db->from('valor_parametro vp');
		$this->db->where("vp.idparametro = 86 and vp.valory LIKE '%" . $tipo . "%'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_solicitud($id) {
		$this->db->select("sth.*, t.valor tipo_solicitud, est.valor state, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as solicitante", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join('valor_parametro t', 'sth.id_tipo_solicitud = t.id_aux');
		$this->db->join('personas p','sth.usuario_registro = p.id');
		$this->db->join('valor_parametro est', 'est.id_aux = sth.id_estado_solicitud');
		$this->db->where('sth.id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function buscar_requisiciones(){
		$this->db->select("v.tipo_vacante, sth.id, sth.fecha_registro, sth.id_tipo_solicitud, v.cargo_id, v.tipo_cargo, p.id responsable_id, 
		CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, vp.valor as tipo_solicitud, dep.valor departamento, dep.id id_departamento, cd.id dd, car.valor cargo", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join('vacantes v', 'sth.id = v.solicitud_id');
		$this->db->join('personas p', 'sth.usuario_registro = p.id');
		$this->db->join('valor_parametro vp', 'sth.id_tipo_solicitud = vp.id_aux');
		$this->db->join('cargos_departamentos cd', 'cd.id = v.cargo_id', 'left');
		$this->db->join('valor_parametro dep', 'dep.id = cd.id_departamento', 'left');
		$this->db->join('valor_parametro car', 'car.id = cd.id_cargo', 'left');
		$this->db->where('sth.id_estado_solicitud', 'Tal_Ter');
		$this->db->where('sth.estado', 1);
		$solicitudes = $this->db->get()->result_array();
		return $solicitudes;
	}

	public function buscar_competencias($buscar){
		$this->db->select("id id_competencia, valor nombre", false);
		$this->db->from('valor_parametro');
		$this->db->where("idparametro", 217);
		if($buscar != '') $this->db->like("valor", $buscar);
		else $this->db->where("valorb", 1);
		$this->db->order_by("valor", 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function info_candidato_competencias($solicitud, $candidato){
		$this->db->select("ct.observaciones, ct.tipo, vp.id id_competencia, vp.valor nombre, ct.nivel");
		$this->db->from('competencias_talento_cuc ct');
		$this->db->join('valor_parametro vp', "vp.id = ct.id_competencia");
		$this->db->where('ct.id_persona', $candidato);
		$this->db->where('ct.id_solicitud_th', $solicitud);
		$this->db->where('ct.estado', 1);
		$estudios = $this->db->get()->result_array();
		return $estudios;
	}

	public function get_correo_jefe_inmediato($solicitud, $candidato){
		$this->db->select("CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, p.correo", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join("personas p", "sth.jefe_inmediato = p.id",'left');
		$this->db->where('sth.id', $solicitud);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_correo_jefe_th(){
		$this->db->select("CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, p.correo", false);
		$this->db->from('personas p');
		$this->db->where('p.id_perfil','Per_Admin_Tal');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_reporte($tipo, $fecha_inicio, $fecha_fin, $sel){
		$filtro = !empty($fecha_i) || !empty($fecha_f) ? 1 : 0;
		$this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as SOLICITANTE, p.identificacion IDENTIFICACION, sth.fecha_registro FECHA_REGISTRA, est.valor ESTADO, $sel", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join('personas p','sth.usuario_registro = p.id');
		$this->db->join('valor_parametro est', 'est.id_aux = sth.id_estado_solicitud');
		$this->db->where('sth.id_tipo_solicitud', $tipo);
		if ($filtro) {
			if ($fecha_i && $fecha_f) $this->db->where("(DATE_FORMAT(sth.fecha_registro, '%Y-%m') >= '$fecha_i' AND DATE_FORMAT(sth.fecha_registro, '%Y-%m') <= '$fecha_f')");
			else if($fecha_i && !$fecha_f) $this->db->where('DATE_FORMAT(sth.fecha_registro, "%Y-%m") >=', $fecha_i);
			else if (!$fecha_i && $fecha_f) $this->db->where('DATE_FORMAT(sth.fecha_registro, "%Y-%m") <=', $fecha_f);
		} else {
			$this->db->where("(sth.id_estado_solicitud <> 'Tal_Can' AND sth.id_estado_solicitud <> 'Tal_Neg')");
		}
		$this->db->order_by('sth.fecha_registro', 'DESC');
		$this->db->where('sth.estado <>', 0);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function obtener_adjunto($id){
		$this->db->select("ad.nombre_archivo, ad.nombre_real");
		$this->db->from("archivos_adj_th ad");
		$this->db->where("ad.id_solicitud",$id);
		$query = $this->db->get();
		return $query->result_array();
	}
	public function detalle_inc_bene($id){
		$this->db->select("ic.*, vp.valor tipo, td.valor tipo_d");
		$this->db->from("talento_humano_inc_bene ic");
		$this->db->join('valor_parametro vp', 'vp.id_aux = ic.tipo_beneficiario');
		$this->db->join('valor_parametro td', 'td.id_aux = ic.tipo_identificacion');
		$this->db->where("ic.id_solicitud = $id");
		$query = $this->db->get();
		return $query->row();
	}
	public function detalle_entrecargo($id){
		$this->db->select("CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido) colaborador, vp.valor motivo,CONCAT(p1.nombre, ' ', p1.segundo_nombre, ' ', p1.apellido, ' ', p1.segundo_apellido) jefe", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join("personas p", "sth.id_solicitante = p.id",'left');
		$this->db->join("personas p1", "sth.jefe_inmediato2 = p1.id",'left');
		$this->db->join("valor_parametro vp", "vp.id_aux = sth.motivo_ec");
		$this->db->where('sth.id', $id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_cantidad_vb_ecargo($id_solicitud){
		$this->db->select("COUNT(est.valorz) cantidad");
		$this->db->from('estados_solicitudes_talento es');
		$this->db->join("valor_parametro est", "est.id_aux = es.estado_id");
		$this->db->join("solicitudes_talento_hum sth", "sth.id = es.solicitud_id");
		$this->db->join("permisos_parametros ps", "ps.vp_principal = sth.id_tipo_solicitud and ps.estado = 1 and vp_secundario = est.id_aux");
		$this->db->where('sth.id', $id_solicitud);
		$query = $this->db->get();
		return $query->row()->cantidad;
	}
	public function get_correo_colaborador_ecargo($id_solicitud){
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona, p.correo", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join("personas p", "sth.id_solicitante = p.id");
		$this->db->where('sth.id', $id_solicitud);
		$this->db->where('sth.estado', 1);
		$query = $this->db->get();
		return $query->row();
	}
	public function get_correo_jefe_inmediato2($id_solicitud){
		$this->db->select("CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona, p.correo", false);
		$this->db->from('solicitudes_talento_hum sth');
		$this->db->join("personas p", "sth.jefe_inmediato2 = p.id");
		$this->db->where('sth.id', $id_solicitud);
		$query = $this->db->get();
		return $query->row();
	}

	public function documentos_rc($motivo){
		$this->db->select("vp.valor nombre_tipo, vp.id_aux id_tipo");
		$this->db->from('permisos_parametros pp');
		$this->db->join("valor_parametro vp", "vp.id_aux = pp.vp_secundario");
		$this->db->where('pp.vp_principal', $motivo);
		$this->db->where('vp.idparametro', 1239);
		$query = $this->db->get();
		return $query->result_array();
	}
}
