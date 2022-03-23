<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH')) exit('No direct script access allowed');

class profesores_eval_model extends CI_Model {
    public function obtener_valores_parametro($parametro){
        $this->db->select("vp.id, vp.valor, vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro", false);
        $this->db->from("valor_parametro vp");
        $this->db->where("vp.idparametro = $parametro");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_solicitudes($fecha_inicial, $fecha_final, $id_estado_sol){
        $persona = $_SESSION["persona"];
        $admin = $_SESSION["perfil"] === "Per_Admin" ? true : false;
        $this->db->select("pe.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, fecha_registro, vpcar.valor cargo, vpes.valor estado", false);
        $this->db->from('profesores_evaluacion pe');
        $this->db->join('personas p', 'p.id = pe.id_persona');
        $this->db->join('valor_parametro vpcar', 'vpcar.id = p.id_cargo', 'left');
        $this->db->join('valor_parametro vpes', 'vpes.id_aux = pe.estado_verificacion', 'left');
        if ($fecha_inicial && $fecha_final) $this->db->where("(DATE_FORMAT(pe.fecha_registro,'%Y-%m-%d') >= DATE_FORMAT('$fecha_inicial','%Y-%m-%d') AND DATE_FORMAT(pe.fecha_registro,'%Y-%m-%d') <= DATE_FORMAT('$fecha_final','%Y-%m-%d'))");
        if($id_estado_sol) $this->db->where("pe.estado_verificacion", $id_estado_sol);
        if(!$admin){
            $this->db->where('pe.id_persona', $persona);
        }else{
            $this->db->where('pe.estado', 1);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_detalle_evaluacion($persona){
        $this->db->select("pi.*, vpi.valor indicador", false);
        $this->db->from('profesores_indicadores pi');
        $this->db->join('valor_parametro vpi', 'vpi.id = pi.id_indicador');
        $this->db->where('pi.id_evaluacion', $persona);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_categorias($persona){
        $this->db->select("pi. categoria, porcentaje, calificacion_cat", false);
        $this->db->from('profesores_indicadores pi');
        $this->db->group_by('pi.categoria');
        $this->db->where('pi.id_evaluacion', $persona);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function consulta_solicitud_id($id){
        $this->db->select("pe.*, p.id persona", false);
        $this->db->from('profesores_evaluacion pe');
        $this->db->join('personas p', 'p.id = pe.id_persona');
        $this->db->where('pe.id', $id);
        $this->db->where('pe.estado', 1);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function obtener_valor_parametro($id)
   	{
    	$this->db->select('vp.valor');
     	$this->db->from('valor_parametro vp');
        $this->db->where("vp.idparametro = '20'");
        $this->db->where('vp.id_aux', $id); 

     	$query = $this->db->get();
     	$row = $query->row();
        return $row;
    }
    
    public function obtener_vParametro($id)
   	{
    	$this->db->select('vp.*, vp.id_aux');
     	$this->db->from('valor_parametro vp');
     	$this->db->where("vp.idparametro = $id");

     	$query = $this->db->get();
     	return $query->result_array();
    }
    
    public function consulta_evaluacion_id($id)
    {
      $this->db->select("pe.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, p.identificacion identificacion", false);
      $this->db->from("profesores_evaluacion pe");
      $this->db->join('personas p', 'p.id = pe.id_persona');
      $this->db->where("pe.id", $id);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
    }
}