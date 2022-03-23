<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

  class ascensos_model extends CI_Model
  {
    /**
   * Se encarga de guardar los datos que se le pasen por el controlador en la tabla indicada.
   * @param Array $data 
   * @param String $tabla 
   * @return Int
   */

   public function guardar_datos($data, $tabla, $tipo = 1 )
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

    public function verificar_solicitud($id)
    {
      $this->db->select("aso.id, aso.id_tipo, aso.id_estado");
      $this->db->from("ascenso_solicitudes aso");
      $this->db->where("aso.id_docente", $id);
      $this->db->where("aso.estado",1);
      $this->db->where("aso.id_estado != 'Asc_Ace_E'");
      $this->db->where("aso.id_estado != 'Asc_Neg_E'");
      $query = $this->db->get();
      return $query->result_array();
    }

    public function listar_solicitudes($id, $id_tipo_solicitud = '', $id_estado_solicitud = '', $fecha_inicial = '', $fecha_final = '')
    {
      $admin = $_SESSION["perfil"] == "Per_Admin"  || $_SESSION["perfil"] == "Per_Csep"? true : false;
      $admin_mod = $_SESSION["perfil"] == "Per_Csep" ? true : false;
      $docente = $_SESSION['persona'];
      $filtro = $id || $id_tipo_solicitud || $id_estado_solicitud || ($fecha_inicial && $fecha_final) ? true : false;

      $this->db->select("aso.*, ve.valor estado, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) docente, vt.valor tipo, p.correo correo, vcn.valor cargo_nuevo_valor, vca.valor cargo_actual_valor, vpc.valor colciencias", false);
      $this->db->from("ascenso_solicitudes aso");
      $this->db->join('valor_parametro ve', "ve.id_aux = aso.id_estado");
      $this->db->join('valor_parametro vt', "vt.id_aux = aso.id_tipo");
      $this->db->join('personas p', "p.id = aso.id_docente");
      $this->db->join('valor_parametro vcn', "vcn.id = aso.cargo_nuevo", 'left');
      $this->db->join('valor_parametro vca', "vca.id = aso.cargo_actual", 'left');
      $this->db->join('valor_parametro vpc', "vpc.id = aso.id_colciencias", 'left');
      if($id) $this->db->where("aso.id",$id);
      if(!$admin) $this->db->where("aso.id_docente",$docente);
      if(!$admin) $this->db->where("aso.id_docente",$docente);
      if(!$filtro && $admin) $this->db->where("(aso.id_estado = 'Asc_Bor_E' OR aso.id_estado = 'Asc_Env_E')");
      if($admin_mod) $this->db->where("(aso.id_estado != 'Asc_Bor_E')");
      if($id_tipo_solicitud) $this->db->where("aso.id_tipo", $id_tipo_solicitud);
      if($id_estado_solicitud) $this->db->where("aso.id_estado", $id_estado_solicitud);
      if ($fecha_inicial && $fecha_final) $this->db->where("(DATE_FORMAT(aso.fecha_registro, '%Y-%m-%d') >= DATE_FORMAT('$fecha_inicial', '%Y-%m-%d') AND DATE_FORMAT(aso.fecha_registro, '%Y-%m-%d') <= DATE_FORMAT('$fecha_final', '%Y-%m-%d'))");
      $this->db->where("aso.estado", 1);
      $this->db->_protect_identifiers = false;
      $this->db->order_by("FIELD (aso.id_estado, 'Asc_Env_E', 'Asc_Bor_E', 'Asc_Ace_E', 'Asc_Rec_E', 'Asc_Can_E', 'Asc_Neg_E')");
      $this->db->order_by("aso.fecha_registro", "desc");
      $this->db->_protect_identifiers = true;
      $query = $this->db->get();
      return $query->result_array();
    }

    public function obtener_items($tipo)
    {
      $this->db->select("vpi.id, vpi.valor nombre, vpi.valorx adicional, vpi.valory imagen, vpi.id_aux, pps.vp_principal seccion, vpi.valorz require_inf");
      $this->db->from("permisos_parametros pp");
      $this->db->join('valor_parametro vpi', "vpi.id = pp.vp_secundario_id");
      $this->db->join('permisos_parametros pps', "pps.vp_secundario_id = vpi.id");
      $this->db->where("pp.vp_principal", $tipo);
      $this->db->where("pp.vp_secundario");
      $this->db->order_by("vpi.valora", "desc");
      $query = $this->db->get();   
      return $query->result_array();
    }

    public function obtener_secciones($tipo)
    {
      $this->db->select("vps.id_aux, vps.valor nombre, vps.valorx adicional");
      $this->db->from("permisos_parametros pp");
      $this->db->join('valor_parametro vps', "vps.id = pp.vp_secundario_id");
      $this->db->where("pp.vp_principal", $tipo);
      $this->db->where("pp.vp_secundario IS NOT NULL");
      $query = $this->db->get();
      return $query->result_array();
    }

    public function listar_archivos_item($id_sol, $id_item, $id_seccion, $columna)
    {
      $this->db->select("aa.id, aa.nombre_real archivo, aa.nombre_guardado, aa.fecha_registra", false);
      $this->db->from("ascensos_adjuntos aa");
      $this->db->where("aa.id_solicitud", $id_sol);
      if($id_item) $this->db->where("aa.id_referencia", $id_item);
      if($id_seccion) $this->db->where("aa.id_seccion", $id_seccion);
      if($columna) $this->db->where("aa.columna", $columna);
      $this->db->where("aa.estado", 1);
      $query = $this->db->get(); 
      return $query->result_array();
    }

    public function listar_formacion_solicitud($id_sol, $tipo)
    {
      $this->db->select("af.id, af.nombre, vp.valor nivel_formacion, af.tipo, af.fecha_registro", false);
      $this->db->from("ascensos_formacion af");
      $this->db->join("valor_parametro vp","vp.id = af.id_formacion");
      $this->db->where("af.id_solicitud", $id_sol);
      $this->db->where("af.tipo", $tipo);
      $this->db->where("af.estado",1);
      $query = $this->db->get();
      return $query->result_array();
    }

    public function obtener_valores_parametro($parametro)
    {
      $this->db->select("vp.*",false);
      $this->db->from("valor_parametro vp");
      $this->db->where("vp.idparametro", $parametro);
      $this->db->where("vp.estado", 1);
      $query = $this->db->get(); 
      return $query->result_array();
    }

    public function consulta_solicitud_id($id)
    {
      $this->db->select("aso.*, CONCAT(p.nombre,' ',p.apellido,' ', p.segundo_apellido) AS nombre_completo ,vpa.valor cargo_actual_valor, vpn.valor cargo_nuevo_valor",false);
      $this->db->from("ascenso_solicitudes aso");
      $this->db->join('personas p', 'p.id = aso.id_docente');
      $this->db->join("valor_parametro vpa", "vpa.id = aso.cargo_actual", 'left');
      $this->db->join("valor_parametro vpn", "vpn.id = aso.cargo_nuevo", 'left');
      $this->db->where("aso.id", $id);
      //$this->db->where("aso.estado", 1);
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
    }

    public function ultima_formacion($id_solicitud)
    {
      $this->db->select("af.*");
      $this->db->from("ascensos_formacion af");
      $this->db->where("af.id_solicitud", $id_solicitud);
      $this->db->order_by("af.id", "desc");
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
    }

    public function consultar_ultima_solicitud($id_docente)
    {
      $this->db->select("aso.*");
      $this->db->from("ascenso_solicitudes aso");
      $this->db->where("aso.id_docente", $id_docente);
      $this->db->order_by("aso.id", "desc");
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
    }

    public function buscar_cargo($buscar)
    {
      $this->db->select("vp.*");
      $this->db->from("valor_parametro vp");
      $this->db->where($buscar);
      $query = $this->db->get(); 
      return $query->result_array();
    }

    public function obtener_observacion($id)
    {
      $this->db->select("COALESCE(ae.observacion, '') observacion", false);
      $this->db->from("ascensos_estados ae");
      $this->db->where("ae.id_solicitud",$id);
      $this->db->order_by("ae.id", "desc");
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
    }

    public function obtener_info_docente($id)
    {
      $this->db->select("p.*, vpp.valor departamento", false);
      $this->db->from("personas p");
      $this->db->join("cargos_departamentos cd", "cd.id = p.id_cargo");
      $this->db->join("valor_parametro vpp","vpp.id = cd.id_departamento");
      $this->db->where("p.id", $id);
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
    }

    public function obtener_correo_talento_humano($id_aux)
    {
      $this->db->select("vp.*");
      $this->db->from("valor_parametro vp");
      $this->db->where("vp.id_aux", $id_aux);
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
    }

    public function listar_historial_estados($id)
    {
      $this->db->select("vp.valor estado, ae.fecha_registra, COALESCE(ae.observacion, '') observaciones, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre_completo", false);
      $this->db->from("ascensos_estados ae");
      $this->db->join("valor_parametro vp","vp.id_aux = ae.id_estado");
      $this->db->join("personas p","p.id = ae.id_usuario_registro");
      $this->db->where("ae.id_solicitud",$id);
      $query = $this->db->get(); 
      return $query->result_array();
    }

  }
  
?>