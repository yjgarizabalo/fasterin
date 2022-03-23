<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class facturacion_model extends CI_Model
{
	/**
	 * Se encarga de guardar los datos que se le pasen por el controlador en la tabla indicada.
	 * @param Array $data 
	 * @param String $tabla 
	 * @return Int
	 */
	public function guardar_datos($data, $tabla, $tipo = 1)
	{
		if ($tipo == 2) {
			$this->db->insert_batch($tabla, $data);
		} else {
			$this->db->insert($tabla, $data);
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

	public function buscar_valor_parametro($codigo, $idparametro)
	{
		$this->db->select('vp.id, vp.valor as nombre, vp.valorx as descripcion');
		$this->db->from('valor_parametro vp');
		$this->db->where('vp.idparametro', $idparametro);
		$this->db->where('vp.estado', 1);
		$this->db->like('vp.valor', $codigo, 'after');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_facturas($id, $estado,$empresa, $banco, $plazo, $fecha)
	{
		$perfil = $_SESSION['perfil'];
		$adm = $perfil == 'Per_Admin' || $perfil == 'Per_Fac' ? true : false;
		$filtro = empty($estado) && empty($fecha) && empty($banco) && empty($empresa) && empty($plazo) ? '' : "(f.id_estado_solicitud LIKE '%$estado%' AND f.fecha_registra LIKE '%$fecha%'  AND f.id_plazo LIKE '%$plazo%')";
		$filtro = $id  ? "f.id = $id" : $filtro;
		$this->db->select("f.*, ent.valor as tipo_entrega, ent.id_aux as tipo_entrega_aux,f.id, ban.id_aux,f.id_estado_solicitud, f.adj_banco, f.valor, f.concepto, f.num_cuenta, tip.valor as tipo, emp.valor as empresa, emp.valorx as nit, sap.valor as sap, pla.valor as plazo, ban.valor as banco, f.id_estado_solicitud as estado, est.valor as state, f.fecha_registra, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona, p.correo, IF(f.id_estado_solicitud = 'Fact_Fin', (
			SELECT TIMESTAMPDIFF(DAY,fe.fecha,NOW()) FROM factura_estados fe WHERE fe.id_solicitud = f.id AND fe.id_estado = 'Fact_Fin' LIMIT 1), 0) as dias_trans", false);
		$this->db->from('facturas f');
		$this->db->join('valor_parametro ban', 'f.id_banco = ban.id', 'left');
		$this->db->join('valor_parametro tip', 'f.id_tipo_cuenta = tip.id', 'left');
		$this->db->join('valor_parametro emp', 'f.id_empresa = emp.id', 'left');
		$this->db->join('valor_parametro sap', 'f.id_codigo_sap = sap.id');
		$this->db->join('valor_parametro pla', 'f.id_plazo = pla.id');
		$this->db->join('valor_parametro est', 'f.id_estado_solicitud = est.id_aux');
		$this->db->join('valor_parametro ent', 'f.id_tipo_entrega = ent.id');
		$this->db->join('personas p', 'f.id_usuario_registra = p.id');
		if(!$adm){
			if ($filtro) 	$this->db->where($filtro);
			$this->db->where('f.id_usuario_registra',$_SESSION['persona']);
		  }else{
				if ($filtro){
					$this->db->where($filtro);
				}else $this->db->where("f.id_estado_solicitud = 'Fact_Sol' OR f.id_estado_solicitud = 'Fact_Tra'");	
		}
		//$this->db->where('f.id',$id);
		$this->db->_protect_identifiers = false;
		$this->db->order_by("FIELD (f.id_estado_solicitud,'Fact_Sol','Fact_Tra','Fact_Neg','Fact_Can','Fact_Fin')");
		$this->db->order_by("f.fecha_registra");
		$this->db->_protect_identifiers = true;
		$this->db->where('f.estado',1);
    $query = $this->db->get();
    return $query->result_array();

	}
	public function listar_estados($id_solicitud)
	{
		$this->db->select("f.*, v.valor estado, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona", false);
		$this->db->from('factura_estados f');
		$this->db->join('valor_parametro v', 'f.id_estado = v.id_aux');
		$this->db->join('personas p', 'f.id_usuario_registra = p.id');
		$this->db->where('f.id_solicitud', $id_solicitud); 
		$query = $this->db->get();
		return $query->result_array();
	}
	public function consulta_solicitud_id($id)
	{
		$this->db->select("f.*, f.id, ban.id_aux,f.id_estado_solicitud, f.adj_banco, f.valor, f.concepto, f.num_cuenta, tip.valor as tipo, emp.valor as empresa, emp.valorx as nit, sap.valor as sap, pla.valor as plazo, ban.valor as banco, f.id_estado_solicitud as estado, est.valor as state, f.fecha_registra, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona, ent.id_aux tipo_entrega_aux", false);
		$this->db->from('facturas f');
		$this->db->join('valor_parametro ban', 'f.id_banco = ban.id', 'left');
		$this->db->join('valor_parametro tip', 'f.id_tipo_cuenta = tip.id', 'left');
		$this->db->join('valor_parametro emp', 'f.id_empresa = emp.id','left');
		$this->db->join('valor_parametro sap', 'f.id_codigo_sap = sap.id');
		$this->db->join('valor_parametro pla', 'f.id_plazo = pla.id');
		$this->db->join('valor_parametro est', 'f.id_estado_solicitud = est.id_aux');
		$this->db->join('valor_parametro ent', 'f.id_tipo_entrega = ent.id');
		$this->db->join('personas p', 'f.id_usuario_registra = p.id');
		$this->db->where('f.id', $id); 

		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function traer_ultima_solicitud($person){
		$this->db->select("f.*");
		$this->db->from('facturas f');
		$this->db->order_by("id", "desc");
		$this->db->where('id_usuario_registra', $person);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	  }
		public function consulta_solicitud_id_estado($id_solicitud, $id_aux)
	{
		$this->db->select("f.*");
		$this->db->from('factura_estados f');
		$this->db->where('f.id_solicitud', $id_solicitud); 
		$this->db->where('f.id_estado', $id_aux); 
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}
}
