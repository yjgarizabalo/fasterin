<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class calidad_model extends CI_Model
{
  /**
   * Se encarga de guardar los datos que se le pasen por el controlador en la tabla indicada.
   * @param Array $data 
   * @param String $tabla 
   * @return Int
   **/

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
   **/

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

 

  public function listar_solicitudes_ambiental($id, $id_lote, $id_tipo_residuo, $id_estado_solicitud, $id_presentacion_residuo, $id_cantidad_residuo, $id_tipo_proceso, $id_origen_proceso, $fecha_inicial, $fecha_final, $id_tipo_solicitud = null)
   {
     $admin = $_SESSION['perfil'] == 'Per_Admin' ? true : false;
     $admin_cal = $_SESSION['perfil'] == 'Per_Adm_Cal' ? true : false;
     $persona = $_SESSION['persona'];
     $filtro = (!empty($id) || !empty($id_lote) || !empty($id_tipo_residuo) || !empty($id_estado_solicitud) || !empty($id_presentacion_residuo) || !empty($id_cantidad_residuo) || !empty($id_tipo_proceso) || !empty($id_origen_proceso) || !empty($fecha_inicial) || !empty($fecha_final) || !empty($id_tipo_solicitud)) ? true : false;
     $this->db->select("cs.*, vpe.valor estado_solicitud, vpr.valor residuo_estado, 
     CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) solicitante, vpp.valor presentacion_text, vpc.valor tipo_cantidad_text, 
     CONCAT(pa.nombre, ' ', pa.apellido, ' ', pa.segundo_apellido) auxiliar, pa.correo correo_auxiliar, vpb.valor ubicacion_bloque, 
     vps.valor ubicacion_salon, pa.id id_auxiliar, vpt.valor tipo_solicitud_nombre, vpa.valor proceso, cpp.id_persona persona_proceso, 
     cpp.id_tipo tipo_persona_proceso, apc.id aptid, eac.id permiso_estado ", false);
     $this->db->from('calidad_solicitudes cs');
     $this->db->join("valor_parametro vpt", "vpt.id_aux = cs.tipo_solicitud");
     $this->db->join("valor_parametro vpe", "vpe.id_aux = cs.id_estado");
     $this->db->join("valor_parametro vpr", "vpr.id = cs.estado_residuo", "left");
     $this->db->join("valor_parametro vpp", "vpp.id = cs.presentacion", "left");
     $this->db->join("valor_parametro vpc", "vpc.id = cs.tipo_cantidad", "left");
     $this->db->join("valor_parametro vpb", "vpb.id = cs.bloque", "left");
     $this->db->join("valor_parametro vps", "vps.id = cs.salon", "left");
     $this->db->join("valor_parametro vpa", "vpa.id = cs.id_proceso", "left");
     $this->db->join("calidad_personas_procesos cpp", "cpp.id_proceso = cs.id_proceso", "left");
     $this->db->join("personas p", "p.id = cs.id_usuario_registra");
     $this->db->join("calidad_auxiliares ca", "ca.id_solicitud = cs.id", "left");
     $this->db->join("personas pa", "pa.id = ca.id_auxiliar", "left");
     $this->db->join("calidad_correcciones cc", "cs.id = cc.id_solicitud", "left");
     $this->db->join("calidad_plan_accion cpa", "cs.id = cpa.id_solicitud", "left");
     $this->db->join('actividad_persona_calidad apc', 'cs.tipo_solicitud = apc.actividad_id AND apc.persona_id = '.$persona,'left');
     $this->db->join('valor_parametro esta', 'esta.id_aux = cs.id_estado and esta.estado = 1','left');
     $this->db->join('estados_actividades_calidad eac', 'esta.id = eac.estado_id AND apc.id = eac.actividad_id','left');
     if ($filtro){
       if($id) $this->db->where("cs.id", $id);
       if($id_lote) $this->db->where("cs.id_lote", $id_lote);
       if($id_tipo_residuo) $this->db->where("cs.estado_residuo", $id_tipo_residuo);
       if($id_estado_solicitud) $this->db->where("cs.id_estado", $id_estado_solicitud);
       if($id_presentacion_residuo) $this->db->where("cs.presentacion", $id_presentacion_residuo);
       if($id_cantidad_residuo) $this->db->where("cs.tipo_cantidad", $id_cantidad_residuo);
       if($id_tipo_solicitud) $this->db->where("cs.tipo_solicitud", $id_tipo_solicitud);
       if($id_tipo_proceso) $this->db->where("cs.id_proceso", $id_tipo_proceso);
       if($id_origen_proceso){
         $this->db->join("calidad_datos_nc cnc", "cs.id = cnc.id_solicitud", "left");
         $this->db->where("cnc.id_origen", $id_origen_proceso);
        }
        if ($fecha_inicial && $fecha_final) $this->db->where("(DATE_FORMAT(cs.fecha_registra,'%Y-%m-%d') >= DATE_FORMAT('$fecha_inicial','%Y-%m-%d') AND DATE_FORMAT(cs.fecha_registra,'%Y-%m-%d') <= DATE_FORMAT('$fecha_final','%Y-%m-%d'))");
      }else{
        $this->db->where("(cs.id_estado <> 'Est_Cal_Fin' AND cs.estado = 1)");
      }
      
    if(!$admin && !$admin_cal) $this->db->where("(cs.id_usuario_registra = $persona || pa.id = $persona || cpp.id_persona = $persona || cc.id_persona = $persona || cpa.id_persona = $persona || eac.id IS NOT NULL)");
    else if($admin_cal) $this->db->where("(eac.id IS NOT NULL)");
    $this->db->where('cs.estado = 1');
     $this->db->_protect_identifiers = false;
     $this->db->order_by("FIELD (cs.id_estado,'Est_Cal_Env','Est_Cal_Sol','Est_Cal_Asig','Est_Cal_Conf','Est_Cal_Pro','Est_Cal_Can')");
     $this->db->order_by("cs.fecha_registra");
     $this->db->group_by("cs.id");
     $this->db->_protect_identifiers = true;

     $query = $this->db->get();
     return $query->result_array();
   }

  public function listar_lotes($id = 0, $estado = '')
  {
    $this->db->select("cl.*, COALESCE(cl.numero_remision, 'N/A') numero_remision, vpe.valor estado_lote, vpm.valor empresa, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona_registra, (SELECT COUNT(*) FROM calidad_solicitudes WHERE id_lote = cl.id) no_solicitudes, vpm.valorx correo_empresa", false);
    $this->db->from("calidad_lotes cl");
    $this->db->join("valor_parametro vpe", "vpe.id_aux = cl.id_estado");
    $this->db->join("valor_parametro vpm", "vpm.id = cl.id_empresa");
    $this->db->join("personas p", "p.id = cl.id_persona_registra");
    $this->db->where("cl.estado = 1");
    if ($estado) $this->db->where("cl.id_estado = 'Est_Cal_Act'");
    $this->db->_protect_identifiers = false;
    $this->db->order_by("FIELD (cl.id_estado, 'Est_Cal_Act', 'Est_Cal_Env', 'Est_Cal_Fin')");
    $this->db->order_by("cl.fecha_registro");
    $this->db->_protect_identifiers = true;

    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_valor_parametro($id)
  {
    $this->db->select('vp.*');
    $this->db->from('valor_parametro vp');
    $this->db->where("vp.idparametro = $id");

    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_permisos_parametro($id)
  {
    $this->db->select('pp.vp_secundario_id id, vp.valor');
    $this->db->from('permisos_parametros pp');
    $this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
    $this->db->where("pp.vp_principal_id = '$id'");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consultar_ultima_solicitud($id_solicitante)
  {
    $this->db->select("cs.*");
    $this->db->from("calidad_solicitudes cs");
    $this->db->where("cs.id_usuario_registra", $id_solicitante);
    $this->db->order_by("cs.id", "desc");
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function consultar_ultimo_lote($id_solicitante)
  {
    $this->db->select("cl.*");
    $this->db->from("calidad_lotes cl");
    $this->db->where("cl.id_persona_registra", $id_solicitante);
    $this->db->order_by("cl.id", "desc");
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function listar_historial_estados($id)
  {
    $this->db->select("vp.valor estado, ce.fecha_registro, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre_completo, ce.observacion", false);
    $this->db->from("calidad_estados ce");
    $this->db->join("valor_parametro vp", "vp.id_aux = ce.id_estado");
    $this->db->join("personas p", "p.id = ce.id_usuario_registro");
    $this->db->where("ce.id_solicitud", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_historial_estados_lote($id)
  {
    $this->db->select("vp.valor estado, cle.fecha_registro, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre_completo", false);
    $this->db->from("calidad_lotes_estados cle");
    $this->db->join("valor_parametro vp", "vp.id_aux = cle.id_estado");
    $this->db->join("personas p", "p.id = cle.id_usuario_registro");
    $this->db->where("cle.id_lote", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consultar_solicitud_id($id)
  {
    $this->db->select("cs.*, vpe.valor estado_solicitud, vpr.valor residuo_estado, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) solicitante, vpp.valor presentacion, vpc.valor tipo_cantidad, CONCAT(pa.nombre, ' ', pa.apellido, ' ', pa.segundo_apellido) auxiliar, pa.id id_auxiliar, vpb.valor ubicacion_bloque, vps.valor ubicacion_salon, vpa.valor proceso", false);
    $this->db->from('calidad_solicitudes cs');
    $this->db->join("valor_parametro vpe", "vpe.id_aux = cs.id_estado");
    $this->db->join("valor_parametro vpr", "vpr.id = cs.estado_residuo", "left");
    $this->db->join("valor_parametro vpp", "vpp.id = cs.presentacion", "left");
    $this->db->join("valor_parametro vpc", "vpc.id = cs.tipo_cantidad", "left");
    $this->db->join("valor_parametro vpb", "vpb.id = cs.bloque", "left");
    $this->db->join("valor_parametro vps", "vps.id = cs.salon", "left");
    $this->db->join("personas p", "p.id = cs.id_usuario_registra");
    $this->db->join("calidad_auxiliares ca", "ca.id_solicitud = cs.id", "left");
    $this->db->join("personas pa", "pa.id = ca.id_auxiliar", "left");
    $this->db->join("valor_parametro vpa", "vpa.id = cs.id_proceso", "left");
    $this->db->where("cs.id", $id);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function consultar_lote_id($id)
  {
    $this->db->select("cl.*, vpe.valor estado_lote, vpm.valor empresa, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) persona_registra, (SELECT COUNT(*) FROM calidad_solicitudes WHERE id_lote = cl.id) no_solicitudes, vpm.valorx correo_empresa", false);
    $this->db->from("calidad_lotes cl");
    $this->db->join("valor_parametro vpe", "vpe.id_aux = cl.id_estado");
    $this->db->join("valor_parametro vpm", "vpm.id = cl.id_empresa");
    $this->db->join("personas p", "p.id = cl.id_persona_registra");
    $this->db->where("cl.estado = 1");
    $this->db->where("cl.id", $id);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function buscar_empleado($where, $filtro, $id)
  {
    $this->db->select("p.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre_completo", false);
    $this->db->from("personas p");
    if ($filtro) {
      $this->db->join("cargos_departamentos cd", "cd.id = p.id_cargo AND cd.id_departamento = 5069");
      $this->db->where("$where AND p.id_cargo = cd.id AND p.estado = 1");
    } else if ($id) {
      $this->db->where('p.id', $id);
    } else {
      $this->db->where("$where");
    }
    $query = $this->db->get();
    if ($id) return $row = $query->row();
    else return $query->result_array();
  }

  public function actualizar_solicitudes($data, $tabla, $id_lote)
  {
    $this->db->where('id_lote', $id_lote);
    $this->db->update($tabla, $data);
    $error = $this->db->_error_message();
    if ($error) {
      return "error";
    }
    return 0;
  }

  public function listar_procesos($idparametro)
  {
    $this->db->select("vp.*");
    $this->db->from("valor_parametro vp");
    $this->db->where('vp.idparametro', $idparametro);
    $this->db->where('vp.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_funcionario_proceso($id_proceso, $id_persona = '')
  {
    $this->db->select("cp.*,CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre_completo, p.identificacion, p.correo", false);
    $this->db->from("calidad_personas_procesos cp");
    $this->db->join("personas p", "p.id = cp.id_persona");
    $this->db->where('cp.id_proceso', $id_proceso);
    $this->db->where('cp.estado', 1);
    if ($id_persona) $this->db->where('cp.id_persona', $id_persona);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consulta_id_proceso($id_usuario_registra, $idparametro)
  {
    $this->db->select("vp.*");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", $idparametro);
    $this->db->where("vp.usuario_registra", $id_usuario_registra);
    $this->db->order_by("vp.id", "desc");
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function consulta_actividad_id($id, $tabla)
  {
    $this->db->select("ca.*");
    $this->db->from("$tabla ca");
    $this->db->where("ca.id", $id);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function listar_correccion($id_solicitud, $id_proceso)
  {
    $admin = $_SESSION['perfil'] == 'Per_Admin' || $_SESSION['perfil'] == 'Per_Adm_Cal' ? true : false;
    $persona = $_SESSION['persona'];
    $this->db->select("cc.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) responsable", false);
    $this->db->from('calidad_correcciones cc');
    $this->db->join("personas p", "p.id = cc.id_persona");
    $this->db->where("cc.id_solicitud", $id_solicitud);
    $this->db->where('cc.estado', 1);
    if (!$admin) $this->db->where("((select id_persona from calidad_personas_procesos where id_proceso = $id_proceso and id_persona = $persona)) OR (cc.id_persona = $persona)");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_plan_accion($id_solicitud, $id_proceso)
  {
    $admin = $_SESSION['perfil'] == 'Per_Admin' || $_SESSION['perfil'] == 'Per_Adm_Cal' ? true : false;
    $persona = $_SESSION['persona'];
    $this->db->select("cp.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) responsable", false);
    $this->db->from('calidad_plan_accion cp');
    $this->db->join("personas p", "p.id = cp.id_persona");
    $this->db->where("cp.id_solicitud", $id_solicitud);
    $this->db->where('cp.estado', 1);
    if (!$admin) $this->db->where("((select id_persona from calidad_personas_procesos where id_proceso = $id_proceso and id_persona = $persona)) OR (cp.id_persona = $persona)");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_participantes($id_solicitud, $id_persona = 0)
  {
    $this->db->select("cp.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre", false);
    $this->db->from('calidad_participantes cp');
    $this->db->join("personas p", "p.id = cp.id_persona");
    $this->db->where("cp.id_solicitud", $id_solicitud);
    if ($id_persona) $this->db->where("cp.id_persona = $id_persona");
    $this->db->where('cp.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consultar_nc($id_solicitud)
  {
    $this->db->select("nc.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre, vpa.valor accion, vph.valor hallazgo, vpo.valor origen", false);
    $this->db->from("calidad_datos_nc nc");
    $this->db->join("personas p", "p.id = nc.id_usuario_registra");
    $this->db->join("valor_parametro vpa", "vpa.id = nc.id_tipo_accion", "left");
    $this->db->join("valor_parametro vph", "vph.id = nc.id_tipo_hallazgo", "left");
    $this->db->join("valor_parametro vpo", "vpo.id = nc.id_origen", "left");
    $this->db->where("nc.id_solicitud", $id_solicitud);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function listar_herramienta($id_solicitud)
  {
    $this->db->select("ch.*", false);
    $this->db->from('calidad_herramienta_nc ch');
    $this->db->where("ch.id_solicitud", $id_solicitud);
    $this->db->where('ch.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_archivos_adjuntos($id_solicitud, $tipo)
  {
    $this->db->select("ca.*", false);
    $this->db->from('calidad_adjuntos_nc ca');
    $this->db->where("ca.id_solicitud", $id_solicitud);
    $this->db->where("ca.tipo", $tipo);
    $this->db->where('ca.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_avances_actividad($id_data, $id_solicitud, $tipo)
  {
    $this->db->select("ca.*", false);
    $this->db->from('calidad_avances_actividad ca');
    $this->db->where("ca.id_solicitud", $id_solicitud);
    $this->db->where("ca.id_actividad", $id_data);
    $this->db->where("ca.tipo", $tipo);
    $this->db->where('ca.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_estado_informes($fecha_inicial, $fecha_fin)
  {
    $this->db->select("vp.valor, cs.id_estado, (SELECT count(cs.id) FROM calidad_solicitudes cs  WHERE cs.id_estado = vp.id_aux AND DATE(cs.fecha_registra) >= '$fecha_inicial' AND DATE(cs.fecha_registra) <= '$fecha_fin') cantidad", false);
    $this->db->from('calidad_solicitudes cs');
    $this->db->join("valor_parametro vp", "vp.id_aux = cs.id_estado");
    $this->db->where("cs.id_estado <> 'Est_Cal_Can'");
    $this->db->group_by('cs.id_estado');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_detalle_estado($fecha_inicial, $fecha_fin)
  {
    $this->db->select("vp.valor, cs.id_estado, (SELECT count(cs.id) FROM calidad_solicitudes cs  WHERE cs.id_estado = vp.id_aux AND DATE(cs.fecha_registra) >= '$fecha_inicial' AND DATE(cs.fecha_registra) <= '$fecha_fin') cantidad", false);
    $this->db->from('calidad_solicitudes cs');
    $this->db->join("valor_parametro vp", "vp.id_aux = cs.id_estado");
    $this->db->where("cs.id_estado <> 'Est_Cal_Can'");
    $this->db->group_by('cs.id_estado');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_tipo_accion($fecha_inicial, $fecha_fin)
  {
    $this->db->select("vp.valor nombre, vp.id_aux accion, count(nc.id_tipo_accion) cantidad,  nc.id_tipo_accion, 
      (SELECT count(cs.id_estado) FROM calidad_solicitudes cs  WHERE cs.id_estado <> 'Est_Cal_Can' AND DATE(cs.fecha_registra) >= '$fecha_inicial' AND DATE(cs.fecha_registra) <= '$fecha_fin') total");
    $this->db->from('calidad_datos_nc nc');
    $this->db->join('valor_parametro vp', 'vp.id = nc.id_tipo_accion');
    $this->db->where("DATE(nc.fecha_ingreso) >= '$fecha_inicial'");
    $this->db->where("DATE(nc.fecha_ingreso) <= '$fecha_fin'");
    $this->db->group_by('nc.id_tipo_accion');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_tipo_hallazgo($fecha_inicial, $fecha_fin)
  {
    $this->db->select("vp.valor nombre, vp.id_aux hallazgo, count(nc.id_tipo_hallazgo) cantidad, nc.id_tipo_hallazgo,
      (SELECT count(cs.id_estado) FROM calidad_solicitudes cs  WHERE cs.id_estado <> 'Est_Cal_Can' AND DATE(cs.fecha_registra) >= '$fecha_inicial' AND DATE(cs.fecha_registra) <= '$fecha_fin') total");
    $this->db->from('calidad_datos_nc nc');
    $this->db->join('valor_parametro vp', 'vp.id = nc.id_tipo_hallazgo');
    $this->db->where("DATE(nc.fecha_ingreso) >= '$fecha_inicial'");
    $this->db->where("DATE(nc.fecha_ingreso) <= '$fecha_fin'");
    $this->db->group_by('nc.id_tipo_hallazgo');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_cumplimiento_estados($fecha_inicial, $fecha_fin)
  {
    $this->db->select('vp.valor estado, cs.id_estado, cs.id_proceso,  vp1.valor proceso, (SELECT count(cs.id) FROM calidad_solicitudes cs  WHERE cs.id_estado = vp.id_aux AND cs.id_proceso = vp1.id) cantidad');
    $this->db->from('calidad_solicitudes cs');
    $this->db->join('valor_parametro vp', 'vp.id_aux = cs.id_estado');
    $this->db->join('valor_parametro vp1', 'vp1.id = cs.id_proceso');
    $this->db->where("DATE(cs.fecha_registra) >= '$fecha_inicial'");
    $this->db->where("DATE(cs.fecha_registra) <= '$fecha_fin'");
    $this->db->where('cs.estado', 1);
    $this->db->group_by('cs.id_proceso');
    $this->db->group_by('cs.id_estado');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_tipos_procesos($fecha_inicial, $fecha_fin)
  {
    $this->db->select('vp.valor nombre, vp.id_aux accion, count(nc.id_tipo_accion) cantidad, cs.id_proceso, vp1.valor proceso, nc.id_tipo_accion ');
    $this->db->from('calidad_datos_nc nc');
    $this->db->join('valor_parametro vp', 'vp.id = nc.id_tipo_accion');
    $this->db->join('calidad_solicitudes cs', 'cs.id = nc.id_solicitud');
    $this->db->join('valor_parametro vp1', 'vp1.id = cs.id_proceso');
    $this->db->where("DATE(nc.fecha_ingreso) >= '$fecha_inicial'");
    $this->db->where("DATE(nc.fecha_ingreso) <= '$fecha_fin'");
    $this->db->group_by('cs.id_proceso');
    $this->db->group_by('nc.id_tipo_accion');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_hallazgos_procesos($fecha_inicial, $fecha_fin)
  {
    $this->db->select('vp.valor nombre, vp.id_aux hallazgo, count(nc.id_tipo_hallazgo) cantidad, vp1.valor proceso, nc.id_tipo_hallazgo, cs.id_proceso');
    $this->db->from('calidad_datos_nc nc');
    $this->db->join('valor_parametro vp', 'vp.id = nc.id_tipo_hallazgo');
    $this->db->join('calidad_solicitudes cs', 'cs.id = nc.id_solicitud');
    $this->db->join('valor_parametro vp1', 'vp1.id = cs.id_proceso');
    $this->db->where("DATE(nc.fecha_ingreso) >= '$fecha_inicial'");
    $this->db->where("DATE(nc.fecha_ingreso) <= '$fecha_fin'");
    $this->db->group_by('cs.id_proceso');
    $this->db->group_by('nc.id_tipo_hallazgo');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_estados_auditoria($fecha_inicial, $fecha_fin)
  {

    $this->db->select('vp.valor valor, nc.id_origen id_origen, vp1.valor origen, cs.id_estado id_estado, count(cs.id_estado) cantidad');
    $this->db->from('calidad_solicitudes cs');
    $this->db->join('valor_parametro vp', 'vp.id_aux = cs.id_estado');;
    $this->db->join('calidad_datos_nc nc', 'cs.id = nc.id_solicitud');;
    $this->db->join('valor_parametro vp1', 'vp1.id = nc.id_origen');;
    $this->db->where("DATE(nc.fecha_ingreso) >= '$fecha_inicial'");
    $this->db->where("DATE(nc.fecha_ingreso) <= '$fecha_fin'");
    $this->db->group_by('nc.id_origen');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_tipos_origen($fecha_inicial, $fecha_fin)
  {

    $this->db->select('vp.valor nombre, vp1.valor origen, count(nc.id_tipo_accion) cantidad, nc.id_origen, vp.id_aux accion');
    $this->db->from('calidad_datos_nc nc');
    $this->db->join('valor_parametro vp', 'vp.id = nc.id_tipo_accion');
    $this->db->join('valor_parametro vp1', 'vp1.id = nc.id_origen');
    $this->db->where("DATE(nc.fecha_ingreso) >= '$fecha_inicial'");
    $this->db->where("DATE(nc.fecha_ingreso) <= '$fecha_fin'");
    $this->db->group_by('nc.id_origen');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_hallazgos_origen($fecha_inicial, $fecha_fin)
  {

    $this->db->select('vp.valor nombre, vp1.valor origen, count(nc.id_tipo_hallazgo) cantidad, nc.id_origen, vp.id_aux hallazgo');
    $this->db->from('calidad_datos_nc nc');
    $this->db->join('valor_parametro vp', 'vp.id = nc.id_tipo_hallazgo');
    $this->db->join('valor_parametro vp1', 'vp1.id = nc.id_origen');
    $this->db->where("DATE(nc.fecha_ingreso) >= '$fecha_inicial'");
    $this->db->where("DATE(nc.fecha_ingreso) <= '$fecha_fin'");
    $this->db->group_by('nc.id_origen');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_sin_clasificar($fecha_inicial, $fecha_fin)
  {
    $this->db->select("count(cs.id_estado) sin_clasificar", false);
    $this->db->from('calidad_solicitudes cs');
    $this->db->join("valor_parametro vp", "vp.id_aux = cs.id_estado");
    $this->db->where("cs.id_estado = 'Est_Cal_Sol'");
    $this->db->where("DATE(cs.fecha_registra) >= '$fecha_inicial'");
    $this->db->where("DATE(cs.fecha_registra) <= '$fecha_fin'");

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

  public function listar_actividades_adm($persona){
		$query = $this->db->query("(SELECT vp.id_aux as id, vp.valor as nombre, ap.id as asignado
		FROM valor_parametro vp
		LEFT JOIN actividad_persona_calidad ap ON (vp.id_aux = ap.actividad_id AND ap.persona_id = $persona)
		WHERE idparametro = 142)");
		return $query->result_array();
	}

  public function quitar_actividad($id){
		$this->db->where('id', $id);
		$this->db->delete('actividad_persona_calidad');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}

  public function validar_asignacion_actividad($id, $persona){
		$this->db->select("IF(COUNT(id) > 0, 0, 1) asignado", false);
		$this->db->from('actividad_persona_calidad');
		$this->db->where('actividad_id', $id);
		$this->db->where('persona_id', $persona);
		$query = $this->db->get();
		return $query->row()->asignado;
	}


	public function listar_estados($actividad){
		$query = $this->db->query("(
			SELECT p.nombre parametro, vp.id AS estado, vp.valor AS nombre, ea.id AS asignado, ea.notificacion
			FROM actividad_persona_calidad ap
			INNER JOIN permisos_parametros pp ON pp.vp_principal = ap.actividad_id
			INNER JOIN valor_parametro vp ON vp.id = pp.vp_secundario_id
			INNER JOIN parametros p ON p.id = vp.idparametro
			LEFT JOIN estados_actividades_calidad ea ON vp.id = ea.estado_id AND ap.id = ea.actividad_id
			WHERE ap.id = $actividad 
			AND ap.estado = 1 AND pp.estado = 1 AND vp.estado = 1
			ORDER BY vp.idparametro, vp.valor
		)");
		return $query->result_array();
	}

  public function validar_asignacion_estado($estado, $actividad, $persona){
		$this->db->select("IF(COUNT(ea.id) > 0, 0, 1) asignado",false);
		$this->db->from('estados_actividades_calidad ea');
		$this->db->where('ea.actividad_id', $actividad);
		$this->db->where('ea.estado_id', $estado);
		$query = $this->db->get();
		return $query->row()->asignado;
	}

  public function get_where($tabla, $data){
		return $this->db->get_where($tabla, $data);
	}

  public function modificar_datos2($data, $tabla , $id, $col = 'id'){
		$this->db->where($col, $id);
		$this->db->update($tabla, $data);
		$error = $this->db->_error_message(); 
		return $error ? "error" : 0;
	}

  
  public function quitar_estado($id){
		$this->db->where('id', $id);
		$this->db->delete('estados_actividades_calidad');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}

  public function guardar_datos2($data, $tabla, $tipo = 1){
		$tipo == 2 ? $this->db->insert_batch($tabla, $data) : $this->db->insert($tabla,$data);
		$error = $this->db->_error_message(); 
		return ($error ? 0 : $tipo == 1) ? $this->db->insert_id() : 1;
	}
}
