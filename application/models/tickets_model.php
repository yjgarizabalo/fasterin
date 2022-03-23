<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class tickets_model extends CI_Model
{

	var $tickets_solicitudes = "tickets_solicitudes";


	public function listado_solicitudes($id, $fecha_inicial, $fecha_final, $hora_inicio, $hora_fin, $id_tipo_solicitud, $id_estado_sol, $permiso_asignar){
		$perfil = $_SESSION['perfil'];
		$id_persona = $_SESSION['persona'];
		$administra = $perfil == 'Per_Admin' ? true : false;
		$admin_soporte = $perfil == 'Admin_Sopor' ? true : false;
		$soporte = $perfil == 'Per_Sop' ? true : false;
		$this->db->select("ts.*, ts.fecha_registro as fecha_solicitud, p.correo correo, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) funcionario, vp.valor estado_ticket, vp.id id_estado, ts.file_name url_adjunto, ts.fecha_elimina, ts.tiempo_solucionado t_solucionado, ts.tiempo_asignacion t_asignacion, ts.tiempo_solucion t_solucion, ts.tiempo_asignado t_asignado",false);
		$this->db->from("tickets_solicitudes ts");
		$this->db->join('personas p', 'ts.id_solicitante = p.id');
		$this->db->join('valor_parametro vp', 'ts.id_estado_ticket = vp.id_aux and vp.estado = 1');
		// Se listan permisos
		if($soporte && $permiso_asignar == "true"){
			$this->db->join('tickets_permisos_solicitudes tps', 'tps.id_tipo = ts.id_tipo_solicitud AND tps.estado = 1 AND tps.id_persona = '.$id_persona);
			$this->db->join('tickets_permisos_estados tpe', 'tpe.id_permiso_solicitud = tps.id AND tpe.id_estado = vp.id AND tpe.estado = 1');	
		}else if($soporte && !$permiso_asignar){
			$this->db->where("ts.id_especialista = $id_persona");	
		}else if($administra || $admin_soporte){
			$this->db->join('tickets_permisos_solicitudes tps', 'tps.id_tipo = ts.id_tipo_solicitud AND tps.estado = 1 AND tps.id_persona = '.$id_persona);
			$this->db->join('tickets_permisos_estados tpe', 'tpe.id_permiso_solicitud = tps.id AND tpe.id_estado = vp.id AND tpe.estado = 1');	
		}
		# Fin de alistamiento de permisos
		if($id_tipo_solicitud) $this->db->where("ts.id_tipo_solicitud", $id_tipo_solicitud);
		if($id_estado_sol) $this->db->where("vp.id", $id_estado_sol);
		if ($fecha_inicial && $fecha_final) $this->db->where("(DATE_FORMAT(ts.fecha_registro,'%Y-%m-%d') >= DATE_FORMAT('$fecha_inicial','%Y-%m-%d') AND DATE_FORMAT(ts.fecha_registro,'%Y-%m-%d') <= DATE_FORMAT('$fecha_final','%Y-%m-%d'))");
		if ($hora_inicio && $hora_fin) $this->db->where("(TIME_FORMAT(ts.fecha_registro,'%H:%i') >= TIME_FORMAT('$hora_inicio','%H:%i') AND TIME_FORMAT(ts.fecha_registro,'%H:%i') <= TIME_FORMAT('$hora_fin','%H:%i'))");
		if($id) $this->db->where('ts.id',$id);
		if(!$soporte && !$administra) $this->db->where("ts.id_solicitante = $id_persona");
		$this->db->_protect_identifiers = false;
		$this->db->order_by("FIELD (ts.id_estado_ticket,'TIK_Regis','TIK_Asig','TIK_Proce','TIK_Suspen', 'TIK_Soluc','TIK_Anul','TIK_Negar')");
		$this->db->order_by("ts.fecha_registro" , "DESC");
		$this->db->_protect_identifiers = true;
		$query = $this->db->get();
        return $query->result_array();
	}
	public function fecha_cierre($id){
		$perfil = $_SESSION['perfil'];	
		$id_persona = $_SESSION['persona'];
		$this->db->select("te.*, te.fecha_registro as fecha_cierre",false);
		$this->db->from("tickets_estados te");
		$this->db->where("te.id_solicitud = $id AND (te.id_estado_ticket = 'TIK_Soluc' OR te.id_estado_ticket = 'TIK_Negar' OR te.id_estado_ticket = 'TIK_Anul') AND te.estado = 1");
		$this->db->order_by("id", "desc");
		$this->db->limit(1);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function traer_registro_id($tabla, $col, $valor){
		$this->db->select("*");
		$this->db->from($tabla);
		$this->db->order_by("id", "desc");
		$this->db->where($col, $valor);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}
	public function ver_detalle_ticket($id){
		$perfil = $_SESSION['perfil'];
		$id_persona = $_SESSION['persona'];
		$administra = $perfil == 'Per_Admin' ? true : false;
		$this->db->select("ts.*, ts.fecha_registro as fecha_solicitud CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) funcionario, ts.estado estado, ts.asunto asunto, vp.valor estado_ticket, ts.descripcion descripcion",false);
		$this->db->from("tickets_solicitudes ts");
		$this->db->join('personas p', 'ts.id_solicitante = p.id');
		$this->db->join('valor_parametro vp', 'ts.id_estado_ticket = vp.id_aux');
		if($id) $this->db->where('ts.id',$id);
		if(!$administra) $this->db->where("ts.id_solicitante = $id_persona");
		$query = $this->db->get();
        return $query->result_array();
	}
	public function listar_funcionario_id($id,$id_solicitante=''){
		$this->db->select("ts.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre_completo, p.identificacion", false);
		$this->db->from("tickets_solicitudes ts");
		$this->db->join("personas p", "p.id = ts.id_solicitante");
		$this->db->where('ts.id', $id);      
		$this->db->where('ts.estado', "*");
		if($id_solicitante) $this->db->where('ts.id_solicitante', $id_solicitante); 
		$query = $this->db->get();
		return $query->result_array();
	}
	
    public function listar_historial_estados($id)
    {
      $this->db->select("te.*, vp.valor estado, te.fecha_registro, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre_completo, te.descripcion observacion, vpm.valor motivo_suspencion", false);
	  $this->db->from("tickets_estados te");
	  $this->db->join("valor_parametro vp","vp.id_aux = te.id_estado_ticket");
	  $this->db->join("tickets_solicitudes ts", "ts.id = te.id_solicitud");
	  $this->db->join("personas p","p.id = te.id_usuario_registra");
	  $this->db->join("valor_parametro vpm","vpm.id = te.id_motivo", 'left');
	  $this->db->order_by("te.fecha_registro", "ASC");
	  $this->db->where("te.id_solicitud",$id);
      $query = $this->db->get(); 
      return $query->result_array();
	}
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
	public function buscar_especialista($dato, $tipo_solicitud_id, $id_estado_solicitud, $rsb, $fecha_actual)
	{
		$perfil = $_SESSION['perfil'];
		$id_persona = $_SESSION['persona'];
		$administra = $perfil == 'Per_Admin' ? true : false;
		$admin_soporte = $perfil == 'Admin_Sopor' ? true : false;
		$Soporte = $perfil == 'Per_Sop' ? true : false;
		if($administra || $Soporte || $admin_soporte){
			$this->db->select("th.hora_inicio hora_ini, th.hora_fin hora_fin, p.usuario,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.id_perfil perfil, p.id",false);
			$this->db->from('personas p');
			$this->db->join("tickets_permisos_solicitudes tps", "p.id = tps.id_persona");
			$this->db->join("tickets_permisos_estados tpe", "tps.id = tpe.id_permiso_solicitud");
			$this->db->join("tickets_funcionarios_horarios tfh", "p.id = tfh.id_persona");
			$this->db->join("tickets_horario th", "tfh.id_horario = th.id");	
			$this->db->join("valor_parametro vp", "th.id_dia = vp.id");
			$this->db->where("(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.usuario LIKE '%" . $dato . "%') AND p.estado=1");
			if($tipo_solicitud_id){
				$this->db->where("tps.id_tipo = '$tipo_solicitud_id'");
				$this->db->where("tpe.id_estado", $id_estado_solicitud);
			}
			$this->db->order_by("th.hora_inicio" , "ASC");
			$this->db->where("tfh.id_persona = tps.id_persona");
			$this->db->where("(DATE_FORMAT(now(),'%W')) = (vp.valorz) and DATE_FORMAT(now(), '%H:%i:%s') >= DATE_FORMAT(th.hora_inicio , '%H:%i:%s') and DATE_FORMAT(now(), '%H:%i:%s') <= DATE_FORMAT(th.hora_fin , '%H:%i:%s')");
			$this->db->where("th.hora_inicio >= '$fecha_actual' or th.hora_inicio < '$fecha_actual'");
			$this->db->where("TIMEDIFF(th.hora_fin, '$fecha_actual') >= '$rsb'");
			$this->db->group_by('p.id');
			$query = $this->db->get();
			return $query->result_array();
		}
	}
	public function buscar_empleado($dato)
	{
		$this->db->select("p.identificacion,p.usuario, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.id_perfil perfil, p.id",false);
		$this->db->from('personas p');
		$this->db->where("(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.usuario LIKE '%" . $dato ."%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_permisos_parametro($id){
		$this->db->select('pp.vp_secundario_id id, vp.valor, vp.valorx, vp.valory');
		$this->db->from('permisos_parametros pp');
		$this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
		$this->db->where("pp.vp_principal_id = '$id'");
		$this->db->order_by("pp.id", "ASC");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function obtener_valory($id){
		$this->db->select("vp.valory", false);
		$this->db->from('valor_parametro vp');
		$this->db->where("vp.id = '$id'");
		$this->db->order_by("vp.id", "ASC");
		$query = $this->db->get();
        return $query->row()->valory;
	}
	public function listar_personas($texto){
		$this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) AS fullname", false);
		$this->db->from('personas p');
		$this->db->where("p.nombre like '%$texto%' || p.apellido like '%$texto%' || p.segundo_apellido like '%$texto%' || p.usuario like '%$texto%' || p.identificacion like '%$texto%'");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function listar_valor_parametro($id_parametro) {
		$this->db->select("vp.id,vp.valor,vp.valorx,vp.valorz,vp.idparametro,vp.id_aux");
		$this->db->from('valor_parametro vp');
		$this->db->where("vp.idparametro = $id_parametro AND vp.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function listar_actividades($persona){
		$query = $this->db->query("(SELECT vp.id_aux as id, vp.valor as nombre, tps.id as asignado
		FROM valor_parametro vp
		LEFT JOIN tickets_permisos_solicitudes tps ON (vp.id_aux = tps.id_tipo AND tps.id_persona = $persona)
		WHERE idparametro = 237)");
		return $query->result_array();
	}
	public function guardar_datos($data, $tabla, $tipo = 1){
		$tipo == 2 ? $this->db->insert_batch($tabla, $data) : $this->db->insert($tabla,$data);
		$error = $this->db->_error_message(); 
		return ($error ? 0 : $tipo == 1) ? $this->db->insert_id() : 1;
	}
	public function validar_asignacion_actividad($id, $persona){
		$this->db->select("IF(COUNT(id) > 0, 0, 1) asignado", false);
		$this->db->from('tickets_permisos_solicitudes');
		$this->db->where('id_tipo', $id);
		$this->db->where('id_persona', $persona);
		$query = $this->db->get();
		return $query->row()->asignado;
	}
	public function listar_estados($actividad){
		$query = $this->db->query("(
			SELECT p.nombre parametro, vp.id AS estado, vp.valor AS nombre, tpe.id AS asignado, tpe.notificacion
			FROM tickets_permisos_solicitudes tps
			INNER JOIN permisos_parametros pp ON pp.vp_principal = tps.id_tipo 
			INNER JOIN valor_parametro vp ON vp.id = pp.vp_secundario_id
			INNER JOIN parametros p ON p.id = vp.idparametro
			LEFT JOIN tickets_permisos_estados tpe ON vp.id = tpe.id_estado AND tps.id = tpe.id_permiso_solicitud
			WHERE tps.id = $actividad 
			AND tps.estado = 1 AND pp.estado = 1 AND vp.estado = 1
			ORDER BY vp.idparametro, vp.valor
		)");
		return $query->result_array();
	}
	public function validar_asignacion_estado($estado, $actividad, $persona){
		$this->db->select("IF(COUNT(tpe.id) > 0, 0, 1) asignado",false);
		$this->db->from('tickets_permisos_estados tpe');
		$this->db->join('tickets_permisos_solicitudes tps', 'tpe.id_permiso_solicitud = tps.id');
		$this->db->where('tpe.id_permiso_solicitud', $actividad);
    	$this->db->where('tpe.id_estado', $estado);
    	$this->db->where('tps.id_persona', $persona);
		$query = $this->db->get();
		return $query->row()->asignado;
	}
	public function quitar_actividad($id){
		$this->db->where('id', $id);
		$this->db->delete('tickets_permisos_solicitudes');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}
	public function quitar_estado($id){
		$this->db->where('id', $id);
		$this->db->delete('tickets_permisos_estados');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}

	public function ver_sin_asignar(){
		$id_persona = $_SESSION['persona'];
        $this->db->select("count(ts.id) total");
		$this->db->from('tickets_solicitudes ts');
		$this->db->join('tickets_permisos_solicitudes tps', 'ts.id_tipo_solicitud = tps.id_tipo');
		$this->db->join('tickets_permisos_estados tpe', 'tps.id = tpe.id_permiso_solicitud');
		$this->db->where('tps.id_persona', $id_persona);
		$this->db->where("(DATE_ADD(ts.fecha_registro, INTERVAL 2 HOUR)) < (NOW())");
		$this->db->where("ts.id_estado_ticket = 'TIK_Regis' and tpe.notificacion = 1 and ts.estado = 1");
        $query = $this->db->get();
		$row = $query->row();
        return $row->{'total'};
	}
	public function ver_sin_solucion(){
		$id_persona = $_SESSION['persona'];
        $this->db->select("count(ts.id) total");
		$this->db->from('tickets_solicitudes ts');
		$this->db->join('tickets_permisos_solicitudes tps', 'ts.id_tipo_solicitud = tps.id_tipo');
		$this->db->join('tickets_permisos_estados tpe', 'tps.id = tpe.id_permiso_solicitud');
		$this->db->join('tickets_estados te', 'ts.id = te.id_solicitud');
		$this->db->where('tps.id_persona', $id_persona);
		$this->db->where("(DATE_ADD(te.fecha_registro, INTERVAL 4 HOUR)) < (NOW())");
		$this->db->where("ts.id_estado_ticket = 'TIK_Proce' OR ts.id_estado_ticket = 'TIK_Asig' and tpe.notificacion = 1 and ts.estado = 1");
        $query = $this->db->get();
        $row = $query->row();
        return $row->{'total'};
	}
	public function validar_asignacion_notificacion($estado, $actividad, $persona){
		$query = $this->db->query("
		  SELECT IF(COUNT(tps.id) > 0, 0, 1) asignado
		  FROM tickets_permisos_solicitudes tps
		  INNER JOIN tickets_permisos_estados tpe ON tpe.estado = 1 AND tpe.id_permiso_solicitud = $actividad AND tpe.id_estado = $estado
		  WHERE tps.id_persona = $persona
		  AND tps.estado = 1
		");
		return $query->row()->asignado;
	}
	public function listar_horarios_funcionarios(){
		$this->db->select("th_.*, vp.valor as dia");
		$this->db->from("tickets_horario th_");
		$this->db->join('valor_parametro vp', 'th_.id_dia = vp.id');
		$this->db->where("th_.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	}
	public function traer_ultima_solicitud($person, $tabla, $usuario){
		$this->db->select("*");
		$this->db->from($tabla);
		$this->db->order_by("id", "desc");
		$this->db->where($usuario, $person);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function listar_funcionarios_horarios($id_horario){
		$this->db->select("th.*, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.identificacion",FALSE);
		$this->db->from("tickets_funcionarios_horarios th");
		$this->db->join('personas p', 'th.id_persona = p.id');
		$this->db->where("th.id_horario",$id_horario);
		$this->db->where("th.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	}

	public function buscar_persona($dato){
		$this->db->select("p.*,p.identificacion,p.id,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
		$this->db->from("personas p");
		$this->db->where("(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1");
		$this->db->where("id_perfil = 'Per_Admin' || id_perfil = 'Per_Sop'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function validar_funcionario_horario($id_persona,$id_horario){
		$this->db->select("th.*",FALSE);
		$this->db->from("tickets_funcionarios_horarios th");
		$this->db->where("th.id_horario = $id_horario AND th.id_persona = $id_persona");
		$this->db->where("th.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	}

	public function obtener_valor_parametro($id)
   	{
    	$this->db->select('vp.*, vp.id_aux');
     	$this->db->from('valor_parametro vp');
     	$this->db->where("vp.idparametro = $id");

     	$query = $this->db->get();
     	return $query->result_array();
	}
	   
	public function traer_tiempo_solicitud($valor){
        $this->db->select("vp.valor tiempo", false);
        $this->db->from("valor_parametro vp");
        $this->db->where("vp.id_aux", $valor);
        $this->db->where("vp.estado", 1);
        $query = $this->db->get();
        return $query->row()->tiempo;
	}
	public function traer_tiempo_atencion($id){
		$this->db->select("TIMEDIFF(TIME_FORMAT(ter.fecha_registro, '%H:%i:%s'), TIME_FORMAT(tes.fecha_registro, '%H:%i:%s')) resultado", false);
		$this->db->from("tickets_solicitudes ts");
		$this->db->join('tickets_estados tes', 'tes.id_solicitud = ts.id');
		$this->db->join('tickets_estados ter', 'ter.id_solicitud = ts.id');
		$this->db->where("ts.id", $id);
		$this->db->where("tes.id_estado_ticket = 'TIK_Regis'");
		$this->db->where("ter.id_estado_ticket = 'TIK_Asig'");
		$this->db->where("ts.estado", 1);
		$this->db->group_by('ter.id');
		$query = $this->db->get();
		return $query->row();
	}
	public function traer_estados_tickets($id){
		$this->db->select("*", false);
		$this->db->from("tickets_estados te");
		$this->db->where("te.estado", 1);
		$this->db->where("te.id_solicitud", $id);
		$this->db->order_by("te.fecha_registro" , "ASC");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function obtenerTiempoRestante($id){
		$this->db->select("te.id_estado_ticket,te.fecha_registro");
		$this->db->from('tickets_estados te');
		$this->db->where('id_solicitud', $id);
		$this->db->order_by("te.fecha_registro" , "ASC");
		$this->db->where('estado', 1);
		$this->db->group_by('te.id');
		$query = $this->db->get();
        return $query->result_array();
	}
	public function horario($id){
		$this->db->select('th.hora_inicio AS hora_inicio, th.hora_fin AS hora_fin, vp.valory AS DayWeek, tiempo_break AS tiempo_break, hora_break AS hora_break');
		$this->db->from('tickets_funcionarios_horarios tfh');
		$this->db->join('tickets_estados te', "tfh.id_persona = te.id_usuario_registra");
		$this->db->join('tickets_horario th', "tfh.id_horario = th.id");
		$this->db->join("valor_parametro vp", "th.id_dia = vp.id");
		$this->db->where('te.id_solicitud', $id);  
		$this->db->group_by('tfh.id');
		$this->db->where('th.estado', 1); 
		$this->db->order_by("te.fecha_registro" , "ASC");
		$query = $this->db->get();
        return $query->result_array();
	}
	public function calificarTiempo(){
		$this->db->select('vp.id_aux, vp.valor');
		$this->db->from('valor_parametro vp');
		$this->db->where("vp.id_aux = 'TIK_hour_asig' OR vp.id_aux = 'TIK_hour_serv' AND vp.estado = 1");
		$query = $this->db->get();
        return $query->result_array();
	}
	public function obtenerSolicitante($id){
		$this->db->select('te.id_solicitante');
		$this->db->from("tickets_estados te");
		$this->db->where("te.id_solicitud = $id AND te.id_estado_ticket = 'TIK_Regis' AND te.estado = 1");
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row();
	}
	public function permiso_asignar(){
		$id_persona = $_SESSION['persona'];
		$this->db->select("count(tps.id) total");
		$this->db->from("tickets_permisos_solicitudes tps");
		$this->db->join("tickets_permisos_estados tpe", "tpe.id_permiso_solicitud = tps.id AND tpe.estado = 1");
		$this->db->join("valor_parametro vp", "vp.id = tpe.id_estado");
		$this->db->where("tps.id_persona = $id_persona AND tps.estado = 1 AND vp.id_aux = 'TIK_Regis'");
		$query = $this->db->get();
		$row = $query->row();
        return $row->{'total'};
	}
}