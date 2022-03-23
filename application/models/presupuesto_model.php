<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Modelo que se encarga de manejar la informacion de del modulo de presupuesto
 */
class presupuesto_model extends CI_Model {





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
    public function modificar_datos($data, $tabla , $id)
    {
        $this->db->where('id', $id);
        $this->db->update($tabla, $data);
      $error = $this->db->_error_message(); 
      if ($error) {
        return "error";
      }
      return 0;
    }
      /**
     * Trae la ultima solicitud ingresada por una persona en la tabla visitantes
     * @param Integer $persona 
     * @return Id
     */
    public function traer_ultima_solicitud_usuario($persona)
	{ 
		$this->db->select("id");
		$this->db->from("solicitudes_presupuestos");
		$this->db->order_by("id", "desc");
		$this->db->where('usuario_registra', $persona);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row->id;
    }

    public function listar_traslados_solicitudes($id_solicitud,$estado,$fecha)
    {
      $persona = $_SESSION['persona'];
      $administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Admin_Pre'? true : false; 
      $this->db->select("pt.*,oo.valor nombre_orden_origen,oo.valorx des_orden_origen,od.valor nombre_orden_destino,od.valorx des_orden_destino,co.valor nombre_cuenta_origen,co.valorx des_cuenta_origen,cd.valor nombre_cuenta_destino,cd.valorx des_cuenta_destino,es.valor estado_traslado,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS nombre_solicitante,a.valor nombre_ano,ceno.valor centro_origen,ceno.valorx des_centro_origen,cend.valor centro_destino,cend.valorx des_centro_destino,p.correo,CONCAT(pa.nombre, ' ', pa.apellido, ' ', pa.segundo_apellido) AS nombre_avala",false);
      $this->db->from('presupuesto_traslados pt');
      $this->db->join('personas p', 'pt.usuario_registra = p.id');
      $this->db->join('personas pa', 'pt.id_usuario_vb = pa.id','left');
      $this->db->join('valor_parametro oo', 'pt.id_orden_origen = oo.id','left');
      $this->db->join('valor_parametro ceno', 'oo.valory = ceno.id','left');
      $this->db->join('valor_parametro od', 'pt.id_orden_destino = od.id','left');
      $this->db->join('valor_parametro cend', 'od.valory = cend.id','left');
      $this->db->join('valor_parametro co', 'pt.id_cuenta_origen = co.id','left');
      $this->db->join('valor_parametro cd', 'pt.id_cuenta_destino = cd.id','left');
      $this->db->join('valor_parametro a', 'pt.id_ano = a.id');
      $this->db->join('valor_parametro es', 'pt.id_estado_traslado = es.id_aux');
      if(!$administra){
        if($id_solicitud > 0) $this->db->where('pt.id_solicitud', $id_solicitud);
       
         $this->db->where("pt.usuario_registra = $persona OR pt.id_usuario_vb = $persona" );
         $this->db->where("pt.id_estado_traslado LIKE '$estado' AND pt.fecha_registra LIKE '$fecha'");
      } else{
        if($id_solicitud > 0){
          $this->db->where('pt.id_solicitud', $id_solicitud);
        }else{
          if($estado == '%%' && $fecha == '%%')$this->db->where('pt.id_estado_traslado <> "Tras_Apro" AND pt.id_estado_traslado <> "Tras_Neg" AND pt.id_estado_traslado <> "Tras_Can"');
          else $this->db->where("pt.id_estado_traslado LIKE '$estado' AND pt.fecha_registra LIKE '$fecha'");
        }

      } 
      $this->db->_protect_identifiers = false;
      $this->db->order_by("FIELD (pt.id_estado_traslado,'Tras_Soli','Tras_Pros','Tras_Com','Tras_Apro','Tras_Neg','Tras_Can')");
      $this->db->order_by("pt.fecha_registra");
      $this->db->_protect_identifiers = true;
      $this->db->where('pt.estado', 1);
      $query = $this->db->get();
      return $query->result_array();
    }

    public function listar_estados_tralados($id_traslado)
    {
      $this->db->select("pt.*,es.valor estado_traslado,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona,",false);
      $this->db->from('historial_estado_traslados pt');
      $this->db->join('personas p', 'pt.usuario_registro = p.id');
      $this->db->join('valor_parametro es', 'pt.id_estado= es.id_aux');
      $this->db->where('pt.id_traslado', $id_traslado);
      $query = $this->db->get();
      return $query->result_array();
    }

    public function traer_traslado_id($id,$tipo = 1)
    {
      $estado  = $tipo == 1 ? 'pt.id_estado_traslado' : '"Tras_Soli"';
      $query = $this->db->query("SELECT pt.*, vp.valor estado_traslado, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona FROM (`presupuesto_traslados` pt) JOIN `personas` p ON `pt`.`usuario_registra` = `p`.`id` JOIN `valor_parametro` vp ON `vp`.`id_aux` = $estado WHERE `pt`.`id` = $id",false);
      return $query->row();
    }
    public function listar_comites()
    {
      $this->db->select("c.*, COUNT(ps.id) solicitudes,DATE_FORMAT(c.fecha_registra,'%Y') as ano,DATE_FORMAT(c.fecha_registra,'%Y-%m-%d') as fecha_inicio,DATE_FORMAT(c.fecha_cierre,'%Y-%m-%d') as fecha_fin",false);
      $this->db->from('comites c');
      $this->db->join('presupuesto_traslados ps', 'ps.id_comite = c.id', 'left');
      $this->db->where('c.estado', "1");
      $this->db->where('c.tipo', "presupuesto");
      $this->db->group_by('c.id'); 
      $this->db->order_by("c.id",'DESC');
      $query = $this->db->get();
      return $query->result_array();
    }
  
    public function traer_comite($where = null)
    {
      $this->db->select("*");
      $this->db->from("comites");
      !is_null($where) ? $this->db->where($where):'';
      $this->db->order_by("id",'DESC');
      $this->db->limit(1);
      $query = $this->db->get();
      return $query->row();
  }
  public function traer_aprobado_persona($persona, $id_traslado)
  {
    $this->db->select("*");
    $this->db->from("traslados_aprobados_comite");
    $this->db->where('usuario_registra',$persona);
    $this->db->where('id_traslado',$id_traslado);
    $this->db->where('estado',1);
    $query = $this->db->get();
    return $query->result_array();
}
  public function listar_traslados_por_comite($id_comite,$usuario,$tipo = 1)
  {
    $this->db->select("pt.*,de.valor departamento,CONCAT('Orden: ',oo.valor,' ',oo.valorx,', Cuenta: ',co.valor,' ',co.valorx) nombre_orden_origen,CONCAT('Orden: ',od.valor,' ',od.valorx,', Cuenta: ',cd.valor,' ',cd.valorx) nombre_orden_destino,es.valor estado_traslado,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS nombre_solicitante,tcu.id aprobo,(SELECT COUNT(tc.id) FROM traslados_aprobados_comite tc WHERE tc.id_traslado = pt.id AND tc.estado = 1 AND tc.tipo = 'Aprobado') as aprobados,(SELECT COUNT(tc2.id) FROM traslados_aprobados_comite tc2 WHERE tc2.id_traslado = pt.id AND tc2.estado = 1 AND tc2.tipo = 'Negado') as negados",false);
    $this->db->from('presupuesto_traslados pt');
    $this->db->join('personas p', 'pt.usuario_registra = p.id');
    $this->db->join('cargos_departamentos c', 'p.id_cargo=c.id','left');
		$this->db->join('valor_parametro de', 'c.id_departamento=de.id','left');
    $this->db->join('valor_parametro oo', 'pt.id_orden_origen = oo.id','left');
    $this->db->join('valor_parametro od', 'pt.id_orden_destino = od.id','left');
    $this->db->join('valor_parametro co', 'pt.id_cuenta_origen = co.id','left');
    $this->db->join('valor_parametro cd', 'pt.id_cuenta_destino = cd.id','left');
    $this->db->join('valor_parametro es', 'pt.id_estado_traslado = es.id_aux');
    $this->db->join('traslados_aprobados_comite tcu', "tcu.id_traslado = pt.id AND tcu.usuario_registra = $usuario  AND tcu.estado = 1",'left');
    $this->db->where('pt.id_comite', $id_comite);
    $this->db->where('pt.estado', 1);
    if($tipo == 2)$this->db->where("pt.id_estado_traslado <> 'Tras_Neg'");
    $this->db->group_by('pt.id'); 
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_aprobados_traslado_comite($id_traslado)
  {
    $this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS nombre,tac.*",false);
    $this->db->from('traslados_aprobados_comite tac');
    $this->db->join('personas p', 'tac.usuario_registra = p.id');
    $this->db->where('tac.id_traslado', $id_traslado);
    $this->db->where('tac.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function contar_aprobado_negados($id_traslado,$tipo)
  {
    $this->db->select("COUNT(tac.id) as total");
    $this->db->from('traslados_aprobados_comite tac');
    $this->db->where('tac.id_traslado', $id_traslado);
    $this->db->where('tac.tipo', $tipo);
    $this->db->where('tac.estado', 1);
    $query = $this->db->get();
    return $query->row()->total;
}
public function listar_comentarios($id_comite)
{
  $this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona,c.*",false);
  $this->db->from('comentarios_comite c');
  $this->db->join('personas p', 'c.usuario_registra = p.id');
  $this->db->where('c.id_comite', $id_comite);
  $this->db->where('c.estado', 1);
  $this->db->where('c.id_comentario IS NULL');
  $query = $this->db->get();
  return $query->result_array();
}
public function listar_respuestas_comentarios($id)
{
  $this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona,c.*",false);
  $this->db->from('comentarios_comite c');
  $this->db->join('personas p', 'c.usuario_registra = p.id');
  $this->db->where('c.estado', 1);
  $this->db->where('c.id_comentario', $id);
  $query = $this->db->get();
  return $query->result_array();
}
public function mostrar_notificaciones_comentarios_comite($tipo)
{
  $perfil = $_SESSION["perfil"];
  $persona = $_SESSION["persona"];
  if ($perfil == "Per_Admin" || $perfil == "Per_Csep" || $perfil == "Per_Admin_Pre") {
    $query = $this->db->query("SELECT CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona,cc.*,(SELECT COALESCE((SELECT cres.usuario_registra FROM comentarios_comite cres WHERE cres.id_comentario = cc.id ORDER by cres.id DESC LIMIT 1), 0)) res FROM comentarios_comite cc INNER JOIN comites c on c.id = cc.id_comite INNER JOIN personas p ON p.id=cc.usuario_registra left join comentarios_comite ccr on cc.id = ccr.id_comentario WHERE  cc.estado_notificacion = 1 AND cc.id_comentario IS null AND c.tipo = '$tipo'  AND cc.usuario_registra <> $persona GROUP BY cc.id HAVING res <> $persona");
  }else{
    $query = $this->db->query("SELECT CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona,cc.*,(SELECT COALESCE((SELECT cres.usuario_registra FROM comentarios_comite cres WHERE cres.id_comentario = cc.id ORDER by cres.id DESC LIMIT 1), cc.usuario_registra)) res FROM comentarios_comite cc INNER JOIN comites c on c.id = cc.id_comite INNER JOIN personas p ON p.id=cc.usuario_registra left join comentarios_comite ccr on cc.id = ccr.id_comentario WHERE  cc.estado_notificacion = 1 AND cc.id_comentario IS null AND c.tipo = '$tipo'  AND cc.usuario_registra = $persona GROUP BY cc.id HAVING res <> $persona");
  }
  return $query->result_array();
}

}
