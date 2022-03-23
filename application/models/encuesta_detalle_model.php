<?php

date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class encuesta_detalle_model extends CI_Model
{

  public function listar_pasos($id_parametro, $id_aux = '')
  {
    $this->db->select('pm.vp_secundario_id, vp.id as id_paso, vp.valor, vp.valorx, vp.valory');
    $this->db->from('permisos_parametros pm');
    $this->db->join('valor_parametro vp', 'pm.vp_secundario_id = vp.id AND pm.estado = 1 AND vp.estado = 1', 'left');
    if (!empty($id_aux)) $this->db->where('pm.vp_principal_id', $id_aux);
    if ($id_parametro) $this->db->where('pm.vp_principal_id', $id_parametro);
    $this->db->where('pm.vp_principal_id', $id_parametro);
    //$this->db->order_by('vp.valory', 'asc');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_preguntas($id_paso)
  {
    $this->db->select('vp.id as id_pregunta, vp.valor, pm.vp_principal_id, pm.vp_secundario_id, vp.valorx, vp.valory, vp.valora as id_respuesta, vp.id_aux');
    $this->db->from('permisos_parametros pm');
    $this->db->join('valor_parametro vp', 'pm.vp_secundario_id = vp.id AND pm.estado = 1 AND vp.estado = 1', 'left');
    $this->db->where('pm.vp_principal_id', $id_paso);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_respuestas($id_pregunta)
  {
    $this->db->select('vp.id, vp.valor, pm.vp_principal_id, vp.id_aux, vp.valorx');
    $this->db->from('permisos_parametros pm');
    $this->db->join('valor_parametro vp', 'pm.vp_secundario_id = vp.id AND pm.estado = 1 AND vp.estado = 1', 'left');
    $this->db->where('pm.vp_principal_id', $id_pregunta);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function get_description($id_encuesta = null)
  {
    $this->db->select('valorx');
    $this->db->from('valor_parametro vp');
    if ($id_encuesta != null) {
      $this->db->where('vp.estado', 1);
      $this->db->where('vp.id', $id_encuesta);
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
        return $query->row_array();
      } else {
        return false;
      }
    }
  }

  public function get_encuesta_detalle($id_usuario)
  {
    $this->db->select('ed.*', 'vp.id');
    $this->db->from('encuesta_detalle ed');
    $this->db->join('valor_parametro vp', 'ed.id_encuesta = vp.id AND vp.estado = 1');
    $this->db->where('ed.id_usuario_registra', $id_usuario);
    $this->db->where('ed.estado', 1);
    $this->db->group_by("ed.id_encuesta");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function traer_registro_id($id_usuario, $id_encuesta)
  {
    $this->db->select('ed.*', 'vp.id');
    $this->db->from('encuesta_detalle ed');
    $this->db->join('valor_parametro vp', 'ed.id_encuesta = vp.id AND vp.estado = 1');
    $this->db->where('ed.id_encuesta', $id_encuesta);
    $this->db->where('ed.id_usuario_registra', $id_usuario);
    $this->db->where('ed.estado', 1);
    $this->db->group_by("ed.id_encuesta");
    $query = $this->db->get();
    return $query->row_array();
  }

  public function listar_encuestas_usuario()
  {
    $this->db->select("CONCAT(pe.nombre,' ', pe.apellido, ' ', pe.segundo_apellido) as nombre_completo, pe.identificacion as identificacion, en.fecha_registra as fecha, en.id_persona", false);
    $this->db->from('personas pe');
    $this->db->join('encuestas en', 'en.id_persona = pe.id');
    $this->db->join('valor_parametro vp', 'vp.id = en.id_encuesta_detalle');
    $this->db->where('en.estado', 1);
    $this->db->where('vp.estado', 1);
    //$this->db->group_by("en.id_encuesta");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_encuestas()
  {
    $this->db->select("vp.id, vp.valor, vp.fecha_registra");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro = 332 AND vp.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
  }

  public function detalle_encuesta($id_usuario)
  {
    $this->db->select('pm.vp_secundario_id, vp.id as id_paso, vp.valor as paso, vp.valorx, vp.valory, en.id_usuario_registra as id_persona');
    $this->db->from('permisos_parametros pm');
    $this->db->join('valor_parametro vp', 'pm.vp_secundario_id = vp.id AND pm.estado = 1 AND vp.estado = 1', 'left');
    $this->db->join('encuesta_detalle en', 'en.id_encuesta = pm.vp_principal_id AND pm.estado = 1 AND en.estado = 1', 'left');
    $this->db->join('valor_parametro vp1', 'vp1.id = en.id_respuesta AND en.estado = 1 AND vp.estado = 1', 'left');
    $this->db->where("en.id_usuario_registra = $id_usuario");
    $this->db->group_by("vp.id");
    $query = $this->db->get();
		return $query->result_array();
  }

  public function ver_respuesta($id_paso, $id_usuario)
  {
    $this->db->select("vp.valor as respuesta, ed.id_respuesta, vpp.valor as pregunta, ed.respuesta_abierta");
    $this->db->from('encuesta_detalle ed');
    $this->db->join('valor_parametro vp', 'vp.id = ed.id_respuesta', 'left');
    $this->db->join('valor_parametro vpp', 'vpp.id = ed.id_pregunta');
    $this->db->where("ed.id_paso = $id_paso and ed.id_usuario_registra = $id_usuario");
    /* $this->db->group_by("vp.valor"); */
    $query = $this->db->get();
    return $query->result_array();
  }

  public function ultimo_registro($usuario)
  {
    $this->db->select('id');
    $this->db->from('encuesta_detalle');
    $this->db->order_by('id', 'DESC');
    $this->db->where('id_usuario_registra', $usuario);
    $this->db->limit(1);
    $query = $this->db->get();
		$row = $query->row();
		return $row;
  }  

}
