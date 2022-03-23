<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class comunicaciones_model extends CI_Model
{
  
  
  var $comunicaciones_solicitudes = "comunicaciones_solicitudes";
  var $comunicaciones_servicios = "comunicaciones_servicios";
  var $comunicaciones_adjunto = "comunicaciones_adjunto";
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
    return "error";
    }
    return 0;
  }
  /**
  * Se encarga de modificar los datos que se le pasen por el controlador en la tabla indicada.
  * @param Array $data 
  * @param String $tabla 
  * @param Int $id 
  * @return Int
  */
  public function modificar_datos($data, $tabla , $where,$tipo = 1)
  {
    if($tipo == 1)$this->db->where('id', $where);
    else $this->db->where($where);
    $this->db->update($tabla, $data);
    $error = $this->db->_error_message(); 
    if ($error) {
    return "error";
    }
    return 0;
  }

  public function listar_solicitud($id, $tipo, $estado, $fecha)
  {
    $perfil = $_SESSION['perfil'];
    $this->db->select("cs.*, vp.valor id_codigo_sap, t.valor tipo_solicitud, e.valor estado_solicitud, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) solicitante, te.valor tipo_evento, cd.valor categoria_divulgacion,cd.valory tiempo_cat_div, p.correo,IF(cs.presupuesto = 1,(SELECT sa.estado_solicitud FROM solicitudes_adm sa WHERE sa.id_evento_com = cs.id LIMIT 1),(SELECT sm.estado_solicitud FROM solicitudes_mantenimiento sm WHERE sm.id_evento_com = cs.id LIMIT 1)) estado_ext", false);
    $this->db->from('comunicaciones_solicitudes cs');
    $this->db->join('valor_parametro t', 'cs.id_tipo_solicitud = t.id_aux');
    $this->db->join('valor_parametro e', 'cs.id_estado_solicitud = e.id_aux');
    $this->db->join('personas p', 'cs.id_usuario_registra = p.id');
    $this->db->join('valor_parametro te', 'cs.id_tipo_evento = te.id','left');
    $this->db->join('valor_parametro cd', 'cs.id_categoria_divulgacion = cd.id_aux','left');
    $this->db->join('valor_parametro vp', 'cs.id_codigo_sap = vp.id',"left");
    $this->db->where('cs.estado',"1");

    if($perfil != 'Per_Admin' && $perfil != 'Per_Admin_Com'){ 
      $this->db->where('cs.id_usuario_registra',$_SESSION['persona']);
      if (empty($id)) {
        $this->db->where("cs.id_estado_solicitud LIKE '%$estado%' AND cs.fecha_registra LIKE '%$fecha%' AND cs.id_tipo_solicitud LIKE '%$tipo%'");
      }else{
        $this->db->where('cs.id',$id);
      }
    }else{
      if (empty($id)) {
        if (empty($estado) && empty($fecha) && empty($tipo)) {
          $this->db->where("cs.id_estado_solicitud = 'Com_Sol_E' OR cs.id_estado_solicitud = 'Com_Rev_E' OR cs.id_estado_solicitud = 'Com_Des_E' OR cs.id_estado_solicitud = 'Com_Ent_E' OR cs.id_estado_solicitud = 'Com_Ace_E' OR cs.id_estado_solicitud = 'Com_Cor_E'");
        }else{
          $this->db->where("cs.id_estado_solicitud LIKE '%$estado%' AND cs.fecha_registra LIKE '%$fecha%' AND cs.id_tipo_solicitud LIKE '%$tipo%'");
        } 
      }else{
        $this->db->where('cs.id',$id);
      }
    
    } 

    $this->db->_protect_identifiers = false;
		$this->db->order_by("FIELD (cs.id_estado_solicitud,'Com_Sol_E','Com_Cor_E','Com_Ent_E','Com_Rev_E','Com_Ace_E','Com_Des_E','Com_Rec_E','Com_Can_E','Com_Fin_E')");
		$this->db->order_by("cs.fecha_registra");
		$this->db->_protect_identifiers = true;
    $query = $this->db->get();
    return $query->result_array();
  }
  
  public function listar_archivos_adjuntos($id_solicitud){
    $this->db->select("ca.*,cs.id_estado_solicitud estado_solicitud , p.nombre, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) solicitante", false );
    $this->db->from('comunicaciones_adjunto ca');
    $this->db->join('personas p', 'ca.usuario_registra = p.id');
    $this->db->join('comunicaciones_solicitudes cs', 'ca.id_solicitud = cs.id');
		$this->db->where('ca.id_solicitud', $id_solicitud); 
    $this->db->where('ca.estado', "1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function traer_ultima_solicitud($person){

      $this->db->select("cs.*");
      $this->db->from('comunicaciones_solicitudes cs');
      $this->db->order_by("id", "desc");
      $this->db->where('id_usuario_registra', $person);
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
    }

  public function listar_servicios ($id_tipo_solicitud , $tipo = '' , $con_aux = ''){
    
    $this->db->select("pp.*, vp.id_aux id_aux,vp.valory, pp.vp_principal tipo_solicitud, pp.vp_secundario_id id_servicio, vp.valor nombre, 0 as estado",false);
    $this->db->from('permisos_parametros pp');
    $this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
    if(!empty($tipo))$this->db->where('vp.valory', $tipo);
    if(!empty($con_aux))$this->db->where('vp.id_aux IS NOT NULL');
    $this->db->where('pp.vp_principal', $id_tipo_solicitud);
    $this->db->where('vp.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_servicios_nuevos ($id,$con_aux){
    $query = $this->db->query("(SELECT pp.*,vp.valory,vp.id_aux id_aux,pp.vp_principal tipo_solicitud, pp.vp_secundario_id id_servicio, vp.valor nombre, 0 as estado
    FROM permisos_parametros pp 
    JOIN valor_parametro vp ON  pp.vp_secundario_id = vp.id
    JOIN comunicaciones_solicitudes cs ON cs.id = '$id'
    LEFT JOIN comunicaciones_servicios cser ON cser.id_solicitud = cs.id AND pp.vp_secundario_id = cser.id_servicio AND cser.estado = 1
    WHERE pp.vp_principal = cs.id_tipo_solicitud AND cser.id IS NULL)");    
    return $query->result_array();
  }
  public function listar_servicios_nuevos2 ($id){
    $this->db->select("pp.*,vp.valory,vp.id_aux id_aux,pp.vp_principal tipo_solicitud, pp.vp_secundario_id id_servicio, vp.valor nombre, 0 as estado");
    $this->db->from('permisos_parametros pp');
    $this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
    $this->db->join('comunicaciones_solicitudes cs', 'cs.id ='.$id);
    $this->db->join('comunicaciones_servicios cser', ' cser.id_solicitud = cs.id AND pp.vp_secundario_id = cser.id_servicio AND cser.estado = 1','left');
    $this->db->where(" pp.vp_principal = cs.id_tipo_solicitud AND cser.id IS NULL");
    $query = $this->db->get();
    return $query->result_array();
  }
  
  public function listar_servicios_solicitud ($id, $esp = null,$tipo = null){
    $this->db->select("vp.valory tipo_ser,vte.valor tipo_entrega,vt.valor tipo, vp.id_aux,cs.*,csol.id_tipo_solicitud, csol.id_estado_solicitud estado_solicitud, vp.valor nombre, cs.fecha_registra fecha, cs.estado estadoSolicitud, CONCAT(p.nombre,' ', p.apellido, ' ',p.segundo_apellido) solicitante,csol.presupuesto", false);
    $this->db->from('comunicaciones_servicios cs');
    $this->db->join('valor_parametro vp', 'cs.id_servicio = vp.id');
    $this->db->join('valor_parametro vt', 'cs.id_tipo = vt.id','left');
    $this->db->join('valor_parametro vte', 'cs.id_tipo_entrega = vte.id','left');
    $this->db->join('personas p', 'cs.id_usuario_registra = p.id');    
    $this->db->join('comunicaciones_solicitudes csol', 'cs.id_solicitud = csol.id');
    $this->db->where('cs.id_solicitud', $id);    
    $this->db->where('cs.estado', "1");
    if (!is_null($esp))$this->db->where("vp.id_aux <> 'Ser_Staff' AND vp.id_aux <> 'Ser_Dif'");
    if (!is_null($tipo))$this->db->where('vp.valory',$tipo);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_servicio_solicitud ($id_servicio, $id_solicitud){
    $this->db->select("vp.valor nombre, cs.fecha_registra fecha, cs.estado estadoSolicitud, CONCAT(p.nombre,' ', p.apellido, ' ',p.segundo_apellido) solicitante", false);
    $this->db->from('comunicaciones_servicios cs');
    $this->db->join('valor_parametro vp', 'cs.id_servicio = vp.id');
    $this->db->join('personas p', 'cs.id_usuario_registra = p.id');
    $this->db->where('cs.id_solicitud', $id_solicitud);    
    $this->db->where('cs.id_servicio', $id_servicio);    
    $this->db->where('cs.estado', "1");
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function consulta_solicitud_id($id){
    $this->db->select("cs.*, cs.presupuesto, vp.valor id_codigo_sap, t.valor tipo_solicitud, e.valor estado_solicitud, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) solicitante, te.valor tipo_evento, cd.valor categoria_divulgacion, DATE_FORMAT(cs.fecha_inicio_evento, '%Y-%m-%d %H:%i') fecha_inicio_evento,DATE_FORMAT(cs.fecha_fin_evento, '%Y-%m-%d %H:%i') fecha_fin_evento,p.correo", false);
    $this->db->from('comunicaciones_solicitudes cs');
    $this->db->join('valor_parametro t', 'cs.id_tipo_solicitud = t.id_aux');
    $this->db->join('valor_parametro e', 'cs.id_estado_solicitud = e.id_aux');
    $this->db->join('personas p', 'cs.id_usuario_registra = p.id');
    $this->db->join('valor_parametro te', 'cs.id_tipo_evento = te.id','left');
    $this->db->join('valor_parametro cd', 'cs.id_categoria_divulgacion = cd.id_aux','left');
    $this->db->join('valor_parametro vp', 'cs.id_codigo_sap = vp.id',"left");
    $this->db->where('cs.id',$id);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function traer_dias_solicitud($id_tipo_solicitud){
    $this->db->select("vp.valory");
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.id_aux',$id_tipo_solicitud);
    $q = $this->db->get('valor_parametro');
    $data = $q->result_array();
    return $data[0]['valory'];
  }
  public function listar_estados($id_solicitud){
    $this->db->select("ce.*,vp.valor estado_solicitud, p.nombre, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) solicitante", false );
    $this->db->from('comunicaciones_estados_sol ce');
    $this->db->join('personas p', 'ce.id_usuario_registro = p.id');
    $this->db->join('valor_parametro vp', 'ce.id_estado = vp.id_aux');
		$this->db->where('ce.id_solicitud', $id_solicitud); 
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consulta_adjunto_id($id){
    $this->db->select("ca.*");
    $this->db->from('comunicaciones_adjunto ca');
    $this->db->where('ca.id',$id);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
    }

    public function obtener_info_servicio ($id){
      $this->db->select("pp.*");
      $this->db->from('valor_parametro pp');
      $this->db->where('pp.id', $id);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
    }

    public function traer_solicitud_externa($id,$tabla)
    {
        $this->db->select("*");
        $this->db->from($tabla);
        $this->db->where('id_evento_com', $id);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function traer_solicitudes_terminadas_man()
    {
      $query = $this->db->query("SELECT c.*,m.observacion obs_man,v.valor estado_man,m.estado_solicitud estado_solicitud_man, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) persona FROM comunicaciones_solicitudes c INNER JOIN personas p on p.id = c.id_usuario_registra INNER JOIN solicitudes_mantenimiento m on m.id_evento_com = c.id AND (m.estado_solicitud = 'Man_Eje' || m.estado_solicitud = 'Man_Fin' || m.estado_solicitud = 'Man_Rec' || m.estado_solicitud = 'Man_Can') INNER JOIN valor_parametro v on v.id_aux = m.estado_solicitud WHERE c.id_estado_solicitud = 'Com_Ent_E' AND c.presupuesto = 0");    
      return $query->result_array();
    }
    public function traer_solicitudes_terminadas_adm()
    {
      $query = $this->db->query("SELECT c.*,m.motivo_den obs_man,m.estado_solicitud estado_solicitud_man,v.valor estado_man,CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) persona FROM comunicaciones_solicitudes c INNER JOIN personas p on p.id = c.id_usuario_registra INNER JOIN solicitudes_adm m on m.id_evento_com = c.id AND (m.estado_solicitud = 'Sol_Apro' || m.estado_solicitud = 'Sol_Den') INNER JOIN valor_parametro v on v.id_aux = m.estado_solicitud WHERE c.id_estado_solicitud = 'Com_Ent_E' AND c.presupuesto = 1");    
      return $query->result_array();
    }
    
    public function traer_ultima_solicitud_estados(){
      $this->db->select("cs.id");
      $this->db->from('comunicaciones_estados_sol cs');
      $this->db->order_by("id", "desc");
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
    }

    public function listar_estados_solicitud($id_solicitud){
      $this->db->select("ce.*");
      $this->db->from('comunicaciones_estados_sol ce');
      $this->db->where('ce.id_solicitud', $id_solicitud); 
      $query = $this->db->get();
      return $query->result_array();
    }

    public function get_personas_notificar() {
      $this->db->select("p.correo, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona", false);
      $this->db->from("personas p");
      $this->db->where("p.id_perfil = 'Per_Admin_Com'");
      $query = $this->db->get();
      return $query->result_array();
    }
}

