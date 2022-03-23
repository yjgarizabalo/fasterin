
<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
  exit('No direct script access allowed');
class biblioteca_model extends CI_Model
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

  public function eliminar_registro($id, $tabla)
  {
    $this->db->where('id', $id);
    $this->db->delete($tabla);
    $error = $this->db->_error_message();
    if ($error) {
      return "error";
    }
    return 0;
  }

  public function buscar_empleado($where, $tipo, $id_sol, $month)
  {
    if ($tipo  == "aux_bib") return $this->cargas_empleados($month, $where);
    else {
      $this->db->select("p.identificacion,p.id,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
      $this->db->from('personas p');
      $this->db->where($where);
      $query = $this->db->get();
      return $query->result_array();
    }
  }

  public function obtener_empleados($where)
  {
    $this->db->select("p.*,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
    $this->db->from('personas p');
    $this->db->where($where);
    $this->db->where('p.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function buscar_estudiante($where, $tabla)
  {
    $this->db->select("p.identificacion,p.id,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
    if ($tabla == "personas") $this->db->from('personas p');
    else if ($tabla == "visitantes") $this->db->from('visitantes p');
    $this->db->where($where);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function traer_ultima_solicitud($person)
  {
    $this->db->select("bs.*, vp.valor tipo_solicitud, vp.valory tipo_solicitud_full,  CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as solicitante, p.correo correo", false);
    $this->db->from('biblioteca_solicitudes bs');
    $this->db->join('personas p', 'bs.id_solicitante = p.id');
    $this->db->join('valor_parametro vp', 'bs.id_tipo_solicitud = vp.id_aux', 'left', false);
    $this->db->order_by("id", "desc");
    $this->db->where('bs.id_usuario_registra', $person);
    $this->db->where('bs.estado', 1);
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function traer_ultimo_aux($person)
  {
    $this->db->select("bx.*", false);
    $this->db->from('biblioteca_auxiliar bx');
    $this->db->order_by("bx.id", "desc");
    $this->db->where('bx.id_usuario_registro', $person);
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function listar_solicitud($id, $id_tipo_solicitud, $id_estado_solicitud, $fecha_inicial, $fecha_final)
  {
    $admin = $_SESSION["perfil"] == "Per_Admin" ? true : false;
    $adm_bib = $_SESSION["perfil"] == "Per_Adm_Bib" ? true : false;
    $aux_bib = $_SESSION["perfil"] == "Per_Aux_Bib" ? true : false;
    $persona  = $_SESSION["persona"];
    $filtro = $id || $id_tipo_solicitud || $id_estado_solicitud || ($fecha_inicial && $fecha_final) ? true : false;
    $permisos = !$adm_bib && !$aux_bib ? 'null permiso' : 'bep.id permiso';
    $permisos_asig = !$adm_bib && !$aux_bib ? 'null asignado' : 'bx.id asignado';

    $this->db->select("$permisos,$permisos_asig,bs.*,v.valor estado_solicitud,v.id_aux,CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) solicitante,vp.valor tipo_solicitud, vp.valory tipo_solicitud_full,p.correo correo,vb.valor bloque, vs.valor salon,(SELECT vbl.valor FROM biblioteca_estado_solicitud beso INNER JOIN valor_parametro vbl ON vbl.id = beso.id_bloque WHERE beso.id_solicitud = bs.id AND beso.id_estado = 'Bib_Rev_E' ORDER BY beso.id DESC LIMIT 1) bloque_log, (SELECT vsl.valor FROM biblioteca_estado_solicitud beso INNER JOIN valor_parametro vsl ON vsl.id = beso.id_salon WHERE beso.id_solicitud = bs.id AND beso.id_estado = 'Bib_Rev_E' ORDER BY beso.id DESC LIMIT 1) salon_log, COALESCE(bs.materia_sol, 'N/A') materia, COALESCE(bs.programa_sol, 'N/A') programa, (SELECT COUNT(bes.id) FROM biblioteca_estudiante_sol bes WHERE bes.id_solicitud = bs.id) num_est", false);
    $this->db->from('biblioteca_solicitudes bs');
    $this->db->join('valor_parametro vb', 'bs.id_bloque = vb.id');
    $this->db->join('valor_parametro vs', 'bs.id_salon = vs.id');
    $this->db->join('personas p', 'bs.id_solicitante = p.id');
    $this->db->join('valor_parametro v', 'bs.id_estado_solicitud = v.id_aux', 'left', false);
    $this->db->join('valor_parametro vp', 'bs.id_tipo_solicitud = vp.id_aux', 'left', false);
    if (!$admin && !$adm_bib && !$aux_bib) $this->db->where('bs.id_solicitante', $persona);
    else if ($adm_bib || $aux_bib) {
      $this->db->join('biblioteca_procesos_personas bpp', " bpp.id_tipo_sol = bs.id_tipo_solicitud AND bpp.id_auxiliar = $persona", 'left');
      $this->db->join('biblioteca_estados_procesos bep', 'bep.id_procesos_persona = bpp.id AND bep.id_estado = bs.id_estado_solicitud', 'left');
      $this->db->join('biblioteca_auxiliar bx', "bx.id_solicitud = bs.id AND bx.id_auxiliar = $persona", 'left');
    }
    if ($id) $this->db->where('bs.id', $id);
    if ($id_tipo_solicitud) $this->db->where('bs.id_tipo_solicitud', $id_tipo_solicitud);
    if ($id_estado_solicitud) $this->db->where('bs.id_estado_solicitud', $id_estado_solicitud);
    if ($fecha_inicial && $fecha_final) $this->db->where("(DATE_FORMAT(bs.fecha_inicio,'%Y-%m-%d') >= DATE_FORMAT('$fecha_inicial','%Y-%m-%d') AND DATE_FORMAT(bs.fecha_inicio,'%Y-%m-%d') <= DATE_FORMAT('$fecha_final','%Y-%m-%d'))");
    if (!$filtro && ($admin || $adm_bib || $aux_bib)) $this->db->where("(bs.id_estado_solicitud = 'Bib_Sol_E' OR bs.id_estado_solicitud = 'Bib_Rev_E' OR bs.id_estado_solicitud = 'Bib_Pre_E' OR bs.id_estado_solicitud = 'Bib_Ent_E')");
    $this->db->where('bs.estado', 1);
    $this->db->_protect_identifiers = false;
    $this->db->order_by("FIELD (bs.id_estado_solicitud,'Bib_Sol_E','Bib_Rev_E','Bib_Pre_E','Bib_Ent_E','Bib_Can_E','Bib_Rec_E','Bib_Fin_E')");
    $this->db->order_by("bs.fecha_inicio");
    $this->db->group_by("bs.id");
    $this->db->_protect_identifiers = true;
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_materia($id)
  {
    $this->db->select("md.materia materia, md.nombre_programa programa, md.grupo");
    $this->db->from('materias_docentes md');
    $this->db->where('md.id', $id);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function listar_estudiante_solicitud($id)
  {
    $this->db->select("be.*,v.identificacion, CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) nombre_completo, v.correo", false);
    $this->db->from('biblioteca_estudiante_sol be');
    $this->db->join('visitantes v', 'v.id = be.id_estudiante_sol', 'left');
    $this->db->where('be.id_solicitud', $id);
    $this->db->where('be.estado', 1);
    $this->db->where('be.tabla', 'visitantes');
    $query1 = $this->db->get()->result_array();

    $this->db->select("be.*,p.identificacion, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre_completo, p.correo", false);
    $this->db->from('biblioteca_estudiante_sol be');
    $this->db->join('personas p', 'p.id = be.id_estudiante_sol');
    $this->db->where('be.id_solicitud', $id);
    $this->db->where('be.estado', 1);
    $this->db->where('be.tabla', 'personas');
    $query2 = $this->db->get()->result_array();

    $query = array_merge($query1, $query2);
    return $query;
  }

  public function listar_estudiantes_asignados($id)
  {
    $this->db->select("bes.*", false);
    $this->db->from("biblioteca_estudiante_sol bes");
    $this->db->join('biblioteca_libros bl', 'bl.id_asignado = bes.id');
    $this->db->where('bes.id_solicitud', $id);
    $this->db->where('bes.estado', 1);
    $query = $this->db->get()->result_array();
    return $query;
  }

  public function listar_historial_solicitud($id)
  {
    $this->db->select("bs.*, COALESCE(bs.observacion, ''), CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido)nombre_persona_registra, vp.valor estado, vpb.valor bloque, vps.valor salon", false);
    $this->db->from('biblioteca_estado_solicitud bs');
    $this->db->join('biblioteca_solicitudes bsol', 'bsol.id = bs.id_solicitud AND bsol.estado = 1');
    $this->db->join('personas p', 'p.id = bs.id_usuario_registro');
    $this->db->join('valor_parametro vp', 'vp.id_aux = bs.id_estado');
    $this->db->join('valor_parametro vpb', 'vpb.id = bs.id_bloque', 'left');
    $this->db->join('valor_parametro vps', 'vps.id = bs.id_salon', 'left');
    $this->db->where('bs.id_solicitud', $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_historial_libro($id)
  {
    $this->db->select("bla.*,CONCAT(pr.nombre,' ',pr.apellido,' ',pr.segundo_apellido)nombre_persona_registra,bes.tabla,bl.nombre_libro,bes.id_estudiante_sol,IF(bes.tabla = 'personas',(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM personas pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1),(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM visitantes pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1)) AS estudiante_asignado", false);
    $this->db->from('biblioteca_libros_asignados bla');
    $this->db->join("biblioteca_libros bl", "bl.id = bla.id_libro");
    $this->db->join("biblioteca_estudiante_sol bes", "bes.id = bla.id_asignado AND bes.id_solicitud = bl.id_solicitud", 'left');
    $this->db->join("personas pr", 'pr.id = bla.id_usuario_registro');
    $this->db->where('bla.id_libro', $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_historial_auxiliar($id)
  {
    $this->db->select("bha.*,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido)modificador,CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) nombre_auxiliar, vp.valor carga", false);
    $this->db->from("biblioteca_historial_auxiliares bha");
    $this->db->join("personas p", "p.id = bha.id_usuario_registro", 'left', false);
    $this->db->join("personas pa", "pa.id = bha.id_auxiliar", 'left', false);
    $this->db->join("valor_parametro vp", "vp.id_aux = bha.accion");
    $this->db->where("bha.id_asignacion", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consulta_solicitud_id($id_solicitud)
  {
    $this->db->select("v.valor estado_solicitud,v.id_aux,t.valor tipo_solicitud, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) solicitante,p.correo correo,vb.valor bloque, vs.valor salon, bs.*, (SELECT vbl.valor FROM biblioteca_estado_solicitud beso INNER JOIN valor_parametro vbl ON vbl.id = beso.id_bloque WHERE beso.id_solicitud = bs.id AND beso.id_estado = 'Bib_Rev_E' ORDER BY beso.id DESC LIMIT 1) bloque_log, (SELECT vsl.valor FROM biblioteca_estado_solicitud beso INNER JOIN valor_parametro vsl ON vsl.id = beso.id_salon WHERE beso.id_solicitud = bs.id AND beso.id_estado = 'Bib_Rev_E' ORDER BY beso.id DESC LIMIT 1) salon_log", false);
    $this->db->from('biblioteca_solicitudes bs');
    $this->db->join('valor_parametro vb', 'bs.id_bloque = vb.id');
    $this->db->join('valor_parametro vs', 'bs.id_salon = vs.id');
    $this->db->join('personas p', 'bs.id_solicitante = p.id');
    $this->db->join('valor_parametro v', 'bs.id_estado_solicitud = v.id_aux');
    $this->db->join('valor_parametro t', 'bs.id_tipo_solicitud = t.id_aux');
    $this->db->where('bs.id', $id_solicitud);
    $this->db->where('bs.estado', 1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function consulta_aux_id($id)
  {
    $this->db->select("bx.*", false);
    $this->db->from('biblioteca_auxiliar bx');
    $this->db->where('bx.id', $id);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function estudiante_solicitud($identificacion, $id_solicitud)
  {
    $this->db->select("p.*");
    $this->db->from('personas p');
    $this->db->join('biblioteca_estudiante_sol be', "be.id_estudiante_sol = p.id AND be.estado = 1 AND be.id_solicitud = $id_solicitud");
    $this->db->where('p.identificacion', $identificacion);
    $query1 = $this->db->get()->result_array();

    $this->db->select("v.*");
    $this->db->from('visitantes v');
    $this->db->join('biblioteca_estudiante_sol be', "be.id_estudiante_sol = v.id AND be.estado = 1 AND be.id_solicitud = $id_solicitud");
    $this->db->where('v.identificacion', $identificacion);
    $query2 = $this->db->get()->result_array();

    $query = array_merge($query1, $query2);
    return $query;
  }

  public function auxiliar_solicitud($id_auxiliar, $id_solicitud, $accion)
  {
    $this->db->select("bx.*");
    $this->db->from('biblioteca_auxiliar bx');
    $this->db->where('bx.id_auxiliar', $id_auxiliar);
    $this->db->where('bx.id_solicitud', $id_solicitud);
    $this->db->where('bx.accion', $accion);
    $this->db->where('bx.estado', 1);
    $query = $this->db->get()->result_array();
    return $query;
  }

  public function detalle_libros_a_tu_clase($id)
  {
    $this->db->select("b.*, vp.valor bloque, v.valor salon", false);
    $this->db->from('biblioteca_solicitudes b');
    $this->db->join('valor_parametro vp', 'b.id_bloque = vp.id');
    $this->db->join('valor_parametro v', 'b.id_salon = v.id');
    $this->db->where('b.id', $id);
    $this->db->where('b.estado', 1);
    $query = $this->db->get();
    return $query->row();
  }

  public function obtener_libro($id)
  {
    $this->db->select("bl.*,CONCAT(pr.nombre,' ',pr.apellido,' ',pr.segundo_apellido)nombre_persona_registra,CONCAT(pd.nombre,' ',pd.apellido,' ',pd.segundo_apellido) persona_retira,bes.tabla,bes.id_estudiante_sol,IF(bes.tabla = 'personas',(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM personas pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1),(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM visitantes pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1)) AS estudiante_asignado", false);
    $this->db->from('biblioteca_libros bl');
    $this->db->join('biblioteca_estudiante_sol bes', "bes.id = bl.id_asignado AND bes.id_solicitud = '{$id}'", 'left');
    $this->db->join('personas pr', 'pr.id = bl.id_usuario_registra', 'left', FALSE);
    $this->db->join('personas pd', 'pd.id = bl.id_persona_retira', 'left', FALSE);
    $this->db->where('bl.id_solicitud', $id);
    $this->db->where('bl.id_estado != 0');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_capacitaciones($id)
  {
    $this->db->select("bcs.*, vp.valor nombre, vp.valorx nivel, vp.valory duracion", false);
    $this->db->from('biblioteca_capacitaciones_solicitud bcs');
    $this->db->join('valor_parametro vp', "vp.id_aux = bcs.id_capacitacion");
    $this->db->where('bcs.id_solicitud', $id);
    $this->db->where('bcs.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_auxiliares($id)
  {
    $this->db->select("bx.*,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre_completo, vp.id_aux carga", false);
    $this->db->from("biblioteca_auxiliar bx");
    $this->db->join("personas p", 'p.id = bx.id_auxiliar');
    $this->db->join("valor_parametro vp", "vp.id_aux = bx.accion");
    $this->db->where("bx.id_solicitud", $id);
    $this->db->where("bx.estado", 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_correos($id)
  {
    $this->db->select("bl.*,bes.tabla,bes.id_estudiante_sol,IF(bes.tabla = 'personas',(SELECT pa.correo FROM personas pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1),(SELECT pa.correo FROM visitantes pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1)) AS correo, IF(bes.tabla = 'personas',(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM personas pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1),(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM visitantes pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1)) AS persona", false);
    $this->db->from('biblioteca_libros bl');
    $this->db->join('biblioteca_estudiante_sol bes', "bes.id = bl.id_asignado AND bes.id_solicitud = '{$id}'");
    $this->db->where('bl.id_solicitud', $id);
    $this->db->where('bl.id_estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_correos_sol($id)
  {
    $this->db->select("bes.tabla,bes.id_estudiante_sol,IF(bes.tabla = 'personas',(SELECT pa.correo FROM personas pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1),(SELECT pa.correo FROM visitantes pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1)) AS correo, IF(bes.tabla = 'personas',(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM personas pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1),(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM visitantes pa WHERE pa.id = bes.id_estudiante_sol LIMIT 1)) AS persona", false);
    $this->db->from('biblioteca_estudiante_sol bes');
    $this->db->where('bes.id_solicitud', $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_correos_aux($id)
  {
    $this->db->select("bx.*,p.correo correo,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) persona, vp.valor carga", false);
    $this->db->from("biblioteca_auxiliar bx");
    $this->db->join("personas p", "p.id = bx.id_auxiliar");
    $this->db->join("valor_parametro vp", "bx.accion = vp.id_aux");
    $this->db->where("bx.id_solicitud", $id);
    $this->db->where("bx.estado", 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function carga_empleado($id, $tipo = 'sol', $f_inicio = '', $f_fin = '', $estado = '')
  {
    $this->db->select("bs.id, (UNIX_TIMESTAMP(bs.fecha_inicio)*1000) start, (UNIX_TIMESTAMP(bs.fecha_fin)*1000) end,t.valor tipo_solicitud, bs.id_tipo_solicitud, DATE_FORMAT(bs.fecha_inicio, '%H:%i') hora_inicio, DATE_FORMAT(bs.fecha_fin, '%H:%i') hora_fin", false);
    $this->db->from("biblioteca_solicitudes bs");
    $this->db->join("biblioteca_auxiliar bx", "bx.id_solicitud = bs.id", 'left');
    $this->db->join('valor_parametro t', 'bs.id_tipo_solicitud = t.id_aux');
    if ($id) $this->db->where("bx.id_auxiliar", $id);
    if (!$f_inicio && !$f_fin) $this->db->where("MONTH(bs.fecha_inicio)", Date('m'));
    else $this->db->where("DATE_FORMAT(bs.fecha_inicio,'%Y-%m-%d') BETWEEN '$f_inicio' AND '$f_fin'");
    $this->db->where("(bx.estado = 1 OR bx.estado IS NULL)");
    if ($estado) $this->db->where("(bs.id_estado_solicitud <> 'Bib_Fin_E' AND bs.id_estado_solicitud <> 'Bib_Can_E' AND bs.id_estado_solicitud <> 'Bib_Rec_E')");
    $this->db->where('bs.estado', 1);
    if ($tipo == 'sol')  $this->db->group_by('bs.id');
    $query = $this->db->get()->result_array();
    return $query;
  }

  public function verificar_codigo_acceso($code)
  {
    $this->db->select("be.id,be.tabla indice,be.id_estudiante_sol id_estudiante,bel.id realizo");
    $this->db->from("biblioteca_estudiante_sol be");
    $this->db->join("biblioteca_encuesta_libros_a_tu_clase bel", "bel.id_estudiante = be.id", 'left');
    $this->db->where("be.codigo_acceso", $code);
    $query = $this->db->get()->row();
    return $query;
  }

  public function verificar_usuario($id, $tabla)
  {
    $this->db->select("p.correo,p.nombre");
    if ($tabla == "personas") $this->db->from("personas p");
    else if ($tabla == "visitantes") $this->db->from("visitantes p");
    $this->db->where("p.id", $id);
    $query = $this->db->get()->row();
    return $query;
  }

  public function obtener_usuario($usuario)
  {
    $this->db->select("p.id");
    $this->db->from('personas p');
    $this->db->where('p.correo', $usuario);
    $query1 = $this->db->get()->result_array();

    $this->db->select("v.id");
    $this->db->from('visitantes v');
    $this->db->where('v.correo', $usuario);
    $query2 = $this->db->get()->result_array();

    $query = array_merge($query1, $query2);
    return $query;
  }

  public function listar_recibidos($id, $usuario)
  {
    $this->db->select("bl.*");
    $this->db->from('biblioteca_libros bl');
    $this->db->where('bl.id_solicitud', $id);
    $this->db->where('bl.id_asignado', $usuario);
    $this->db->where('bl.id_estado', 1);

    $query = $this->db->get()->result_array();
    return $query;
  }

  public function verificar_codigo($id_solicitud, $codigo)
  {
    $this->db->select("bl.*", false);
    $this->db->from('biblioteca_libros bl');
    $this->db->where('bl.id_solicitud', $id_solicitud);
    $this->db->where('bl.codigo_de_barras', $codigo);
    $this->db->where('bl.id_estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function verificar_identificacion($identificacion)
  {
    $this->db->select("vs.*", false);
    $this->db->from('visitantes vs');
    $this->db->where('vs.identificacion', $identificacion);
    $this->db->where('vs.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function verificar_correo($correo)
  {
    $this->db->select("vs.*", false);
    $this->db->from('visitantes vs');
    $this->db->where('vs.correo', $correo);
    $this->db->where('vs.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_causas($buscar)
  {
    $this->db->select("vp.*, vp.valor causa");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", $buscar);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_bloque($buscar)
  {
    $this->db->select("vp.id ,vp.valor,vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro", FALSE);
    $this->db->from('valor_parametro vp');
    $this->db->where("vp.idparametro = $buscar");
    $this->db->order_by("vp.valor");
    // $this->db->where("vp.valory",'Bib_Acd');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_bloque_cap($buscar)
  {
    $this->db->select("vp.id, vp.valor, vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro", FALSE);
    $this->db->from('permisos_parametros pp');
    $this->db->join('valor_parametro vp', "vp.id = pp.vp_secundario_id");
    $this->db->where('pp.vp_principal', $buscar);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_recursos($buscar)
  {
    $this->db->select("vp.id_aux id,vp.valor,vp.valorx, vp.estado, vp.valory, vp.idparametro,re.valor relacion", FALSE);
    $this->db->from('valor_parametro vp');
    $this->db->join('valor_parametro re', 'vp.valory = re.id', 'left');
    $this->db->where("vp.idparametro = $buscar");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_programas($buscar)
  {
    $this->db->select("vp.id, vp.valor, vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro, re.valor relacion", false);
    $this->db->from("valor_parametro vp");
    $this->db->join('valor_parametro re', 'vp.valory = re.id', 'left');
    $this->db->where("vp.idparametro", $buscar);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_acciones($buscar)
  {
    $this->db->select("vp.id_aux id, vp.valor", false);
    $this->db->from("permisos_parametros pp");
    $this->db->join("valor_parametro vp", "vp.id_aux = pp.vp_secundario");
    $this->db->where("pp.vp_principal", $buscar);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_auxiliares($id)
  {
    $this->db->select("bx.*,p.identificacion,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre_completo, vp.valor as carga,CONCAT(pr.nombre,' ',pr.apellido,' ',pr.segundo_apellido) persona_retira", false);
    $this->db->from("biblioteca_auxiliar bx");
    $this->db->join("personas p", "p.id = bx.id_auxiliar", 'left', false);
    $this->db->join("personas pr", "pr.id = bx.id_usuario_elimina", 'left', false);
    $this->db->join("valor_parametro vp", "vp.id_aux = bx.accion");
    $this->db->where("bx.id_solicitud", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_bloque_salon($id)
  {
    $this->db->select("pp.vp_secundario_id id, vp.valor");
    $this->db->from('permisos_parametros pp');
    $this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
    $this->db->where("pp.vp_principal_id = '$id'");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function cargas_empleados($month, $where)
  {
    $sql = "SELECT p.id,p.identificacion,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre_completo, 
              (SELECT COALESCE(sum(vp.valorx),0) total 
                FROM biblioteca_auxiliar ba 
                INNER JOIN biblioteca_solicitudes bes ON bes.id = ba.id_solicitud 
                INNER JOIN valor_parametro vp ON vp.id_aux = ba.accion 
                WHERE bes.id_estado_solicitud != 'Bib_Fin_E' AND bes.id_estado_solicitud != 'Bib_Rec_E' AND bes.id_estado_solicitud != 'Bib_Can_E' AND MONTH(bes.fecha_fin) = $month AND ba.id_auxiliar = p.id AND ba.estado = 1 AND bes.estado = 1) total 
              FROM biblioteca_solicitudes bs 
              INNER JOIN biblioteca_turnos bt ON ((DATE_FORMAT(bs.fecha_inicio,'%H:%i:%s') BETWEEN bt.hora_entrada AND bt.hora_salida) AND (DATE_FORMAT(bs.fecha_fin,'%H:%i:%s') BETWEEN bt.hora_entrada AND bt.hora_salida)) 
              INNER JOIN biblioteca_turnos_auxiliar bta ON bta.id_turno = bt.id 
              INNER JOIN personas p ON p.id = bta.id_auxiliar AND (p.id_perfil = 'Per_Aux_Bib' OR p.id_perfil = 'Per_Adm_Bib')
              INNER JOIN biblioteca_procesos_personas bpp ON bpp.id_auxiliar = p.id AND bpp.id_tipo_sol = bs.id_tipo_solicitud 
              WHERE $where
              GROUP BY p.identificacion";
    $query = $this->db->query($sql);

    return $query->result_array();
  }

  public function listar_nivel_capa()
  {
    $this->db->select("vp.id_aux id, vp.valor nombre, vp.valorx nivel, vp.valory duracion", false);
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", 130);
    $this->db->where("vp.estado", 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_procesos_bib($id)
  {
    if (!$id) {
      return array();
    } else {
      $this->db->select("vp.*, vp.valor nombre, vp.valorx descripcion, bpp.id tipo", false);
      $this->db->from("valor_parametro vp");
      $this->db->join("biblioteca_procesos_personas bpp", "bpp.id_tipo_sol = vp.id_aux AND bpp.id_auxiliar = $id", 'left', false);
      $this->db->where("vp.idparametro", 126);
      $query = $this->db->get();
      return $query->result_array();
    }
  }

  public function listar_estados_bib($id)
  {
    $this->db->select("vp.*,vp.valor nombre, bep.id tipo", false);
    $this->db->from("valor_parametro vp");
    $this->db->join("biblioteca_estados_procesos bep", "bep.id_estado = vp.id_aux AND bep.id_procesos_persona = $id", 'left', false);
    $this->db->where("vp.idparametro", 125);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_causas()
  {
    $this->db->select("vp.*,vp.valor causa", false);
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", 122);
    return $query = $this->db->get()->result_array();
  }

  public function listar_empleados_turnos($id)
  {
    $this->db->select("p.*,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre_completo, bta.id tipo", false);
    $this->db->from("personas p");
    $this->db->join("biblioteca_turnos_auxiliar bta", "bta.id_auxiliar = p.id AND bta.id_turno = $id", 'left', false);
    $this->db->where("p.id_perfil = 'Per_Adm_Bib' OR p.id_perfil = 'Per_Aux_Bib'");
    $this->db->where("p.estado", 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function verificar_turnos_aux($id, $id_auxiliar)
  {
    $this->db->select("bta.*");
    $this->db->from("biblioteca_turnos_auxiliar bta");
    $this->db->where("bta.id_turno", $id);
    $this->db->where("bta.id_auxiliar", $id_auxiliar);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_asignaciones($id)
  {
    $this->db->select("bep.*,bpp.id_tipo_sol tipo_sol", false);
    $this->db->from("biblioteca_estados_procesos bep");
    $this->db->join("biblioteca_procesos_personas bpp", "bpp.id = bep.id_procesos_persona");
    $this->db->where("bpp.id_auxiliar", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_asigacion_aux($id, $id_tipo_sol, $estado)
  {
    $this->db->select("*", false);
    $this->db->from("biblioteca_procesos_personas bpp");
    if ($estado) $this->db->join("biblioteca_estados_procesos bep", "bpp.id = bep.id_procesos_persona AND bep.id_estado = '$estado'");
    if ($id_tipo_sol) $this->db->where("bpp.id_tipo_sol", $id_tipo_sol);
    $this->db->where("bpp.id_auxiliar", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function exist($id, $tabla)
  {
    $this->db->select("*");
    $this->db->from($tabla);
    $this->db->where("id", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_turnos_bib()
  {
    $this->db->select("t.*", false);
    $this->db->from("biblioteca_turnos t");
    $this->db->where("t.estado", 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function verificar_conflictos_turnos($id, $hora_entrada, $hora_salida)
  {
    $sql = "SELECT bta.* FROM biblioteca_turnos_auxiliar bta JOIN biblioteca_turnos bt ON bt.id = bta.id_turno WHERE ('$hora_entrada' BETWEEN bt.hora_entrada AND bt.hora_salida OR '$hora_salida' BETWEEN bt.hora_entrada AND bt.hora_salida OR bt.hora_entrada BETWEEN '$hora_entrada' AND '$hora_salida' OR  bt.hora_salida BETWEEN '$hora_entrada' AND '$hora_salida') AND bta.id_auxiliar = $id";
    $query = $this->db->query($sql);
    return $query->result_array();
  }

  public function consulta_turno_id($id)
  {
    $this->db->select("bt.*");
    $this->db->from("biblioteca_turnos bt");
    $this->db->where("bt.id", $id);
    $this->db->where("bt.estado", 1);
    $query = $this->db->get();
    return $query->row();
  }

  public function consultar_accion_id($id)
  {
    $this->db->select("vp.id_aux");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.id", $id);
    $query = $this->db->get();
    return $query->row();
  }

  public function verificar_turno_ent($id_auxiliar, $id_solicitud)
  {
    $sql = "SELECT * FROM biblioteca_turnos_auxiliar bta JOIN biblioteca_turnos bt ON bt.id = bta.id_turno JOIN biblioteca_solicitudes bs ON bs.id = $id_solicitud AND bs.estado = 1 WHERE (DATE_FORMAT(bs.fecha_inicio,'%H:%i:%s') BETWEEN bt.hora_entrada AND bt.hora_salida) AND bta.id_auxiliar = $id_auxiliar";
    $query = $this->db->query($sql);
    return $query->result_array();
  }

  public function verificar_turno_ret($id_auxiliar, $id_solicitud)
  {
    $sql = "SELECT * FROM biblioteca_turnos_auxiliar bta JOIN biblioteca_turnos bt ON bt.id = bta.id_turno JOIN biblioteca_solicitudes bs ON bs.id = $id_solicitud AND bs.estado = 1 WHERE (DATE_FORMAT(bs.fecha_fin,'%H:%i:%s') BETWEEN bt.hora_entrada AND bt.hora_salida) AND bta.id_auxiliar = $id_auxiliar";
    $query = $this->db->query($sql);
    return $query->result_array();
  }

  public function verificar_turno_cap($id_auxiliar, $id_solicitud)
  {
    $sql = "SELECT * FROM biblioteca_turnos_auxiliar bta JOIN biblioteca_turnos bt ON bt.id = bta.id_turno JOIN biblioteca_solicitudes bs ON bs.id = $id_solicitud AND bs.estado = 1 WHERE (DATE_FORMAT(bs.fecha_inicio,'%H:%i:%s') BETWEEN bt.hora_entrada AND bt.hora_salida) AND (DATE_FORMAT(bs.fecha_fin,'%H:%i:%s') BETWEEN bt.hora_entrada AND bt.hora_salida) AND bta.id_auxiliar = $id_auxiliar";
    $query = $this->db->query($sql);
    return $query->result_array();
  }

  public function obtener_materias_por_docente($identificacion)
  {
    $this->db->select("md.*,CONCAT(md.cod_materia,md.cod_grupo) id,CONCAT(md.materia,' - ',md.grupo) valor, md.id id_mat, CONCAT(md.materia,' - ',md.grupo) materia", FALSE);
    $this->db->from("materias_docentes md");
    $this->db->where("md.identificacion_doc", $identificacion);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_estudiantes_por_materia($materia)
  {
    $this->db->select("est.id,CONCAT(est.nombre,' ',est.apellido,' ',est.segundo_apellido) nombre_completo,me.identificacion_est identificacion, 'visitantes' tabla", FALSE);
    $this->db->from("materias_estudiantes me");
    $this->db->join("visitantes est", "me.identificacion_est = est.identificacion", 'left');
    $this->db->where("CONCAT(me.cod_materia,me.cod_grupo)", $materia);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_encuestas($id)
  {
    $this->db->select("bal.*, IF(bes.tabla = 'personas', (SELECT vpr.valor rol_principal FROM personas pr INNER JOIN valor_parametro vpr ON vpr.id_aux = pr.id_perfil WHERE pr.id = bes.id_estudiante_sol LIMIT 1), (SELECT vpr.valor rol_principal FROM visitantes pr INNER JOIN valor_parametro vpr ON vpr.id_aux = pr.tipo WHERE pr.id = bes.id_estudiante_sol LIMIT 1)) AS rol_principal, IF(bes.tabla = 'personas', 'N/A', (SELECT vpp.valor programa FROM visitantes pp INNER JOIN valor_parametro vpp ON vpp.id = pp.id_programa LIMIT 1)) AS programa", false);
    $this->db->from("biblioteca_encuesta_libros_a_tu_clase bal");
    $this->db->join("biblioteca_estudiante_sol bes", "bes.id = bal.id_estudiante");
    $this->db->join("biblioteca_solicitudes bs", "bs.id = bes.id_solicitud AND bs.estado = 1");
    $this->db->where("bs.id", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_ubicacion($id)
  {
    $this->db->select("vpb.valor bloque, vps.valor salon, vpb.id id_bloque, vps.id id_salon");
    $this->db->from("biblioteca_estado_solicitud bes");
    $this->db->join("valor_parametro vpb", "vpb.id = bes.id_bloque");
    $this->db->join("valor_parametro vps", "vps.id = bes.id_salon");
    $this->db->where("bes.id_solicitud", $id);
    $this->db->order_by("bes.id", "desc");
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_niveles_sol($id)
  {
    $this->db->select(
      "bs.id, bs.fecha_inicio, bs.fecha_fin, vp.valor capacitacion, vp.id_aux tipo, vp.valory tiempo, 
      (SELECT vbl.valor 
      FROM biblioteca_estado_solicitud beso 
      INNER JOIN valor_parametro vbl ON vbl.id = beso.id_bloque 
      WHERE beso.id_solicitud = bs.id 
      ORDER BY beso.id DESC LIMIT 1) bloque_log, 
      (SELECT vsl.valor 
      FROM biblioteca_estado_solicitud beso 
      INNER JOIN valor_parametro vsl ON vsl.id = beso.id_salon 
      WHERE beso.id_solicitud = bs.id 
      ORDER BY beso.id DESC LIMIT 1) salon_log"
    );
    $this->db->from("biblioteca_capacitaciones_solicitud bcs");
    $this->db->join("biblioteca_solicitudes bs", "bs.id = bcs.id_solicitud AND bs.estado = 1");
    $this->db->join("valor_parametro vp", "vp.id_aux = bcs.id_capacitacion");
    $this->db->where("bcs.id_solicitud", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consultar_solicitud_codigo($id)
  {
    $this->db->select("bs.id_tipo_solicitud, vp.valory tipo_solicitud");
    $this->db->from("biblioteca_estudiante_sol bes");
    $this->db->join("biblioteca_solicitudes bs", "bs.id = bes.id_solicitud AND bs.estado = 1");
    $this->db->join("valor_parametro vp", "vp.id_aux = bs.id_tipo_solicitud");
    $this->db->where("bes.codigo_acceso", $id);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function consulta_info_codigo($id)
  {
    $this->db->select("IF(bes.tabla = 'personas',(SELECT vpr.valor rol_principal FROM personas pa INNER JOIN valor_parametro vpr ON vpr.id_aux = pa.id_perfil WHERE pa.id = bes.id_estudiante_sol LIMIT 1),(SELECT vpr.valor rol_principal FROM visitantes pa INNER JOIN valor_parametro vpr ON vpr.id = pa.tipo WHERE pa.id = bes.id_estudiante_sol LIMIT 1)) AS rol_principal, IF(bes.tabla = 'personas',(SELECT vpp.id programa FROM personas pa INNER JOIN valor_parametro vpp ON vpp.id_aux = 'dflt_P' WHERE pa.id = bes.id_estudiante_sol LIMIT 1),(SELECT vpp.id programa FROM visitantes pa INNER JOIN valor_parametro vpp ON vpp.id = pa.id_programa WHERE pa.id = bes.id_estudiante_sol LIMIT 1)) AS programa");
    $this->db->from("biblioteca_estudiante_sol bes");
    $this->db->where("bes.codigo_acceso", $id);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function consolidado_encuestas($tipo)
  {
    $this->db->select("COUNT(be.id) encuestas, COALESCE(AVG(be.utilidad), 0) q1, COALESCE(AVG(be.puntualidad), 0) q2, COALESCE(AVG(be.auxiliar), 0) q3, COALESCE(AVG(be.recomendacion), 0) q4", false);
    $this->db->from("biblioteca_encuesta_libros_a_tu_clase be");
    $this->db->join("biblioteca_estudiante_sol bes", "bes.id = be.id_estudiante");
    $this->db->join("biblioteca_solicitudes bs", "bs.id = bes.id_solicitud AND bs.estado = 1");
    $this->db->where("bs.id_tipo_solicitud", $tipo);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consolidado_roles($tipo)
  {
    $this->db->select("IF(bes.tabla = 'personas',(SELECT vpr.valor rol_principal FROM personas pa INNER JOIN valor_parametro vpr ON vpr.id_aux = pa.id_perfil WHERE pa.id = bes.id_estudiante_sol LIMIT 1),(SELECT vpr.valor rol_principal FROM visitantes pa INNER JOIN valor_parametro vpr ON vpr.id_aux = pa.tipo WHERE pa.id = bes.id_estudiante_sol LIMIT 1)) AS roles, COUNT(*) cantidad", false);
    $this->db->from("biblioteca_encuesta_libros_a_tu_clase be");
    $this->db->join("biblioteca_estudiante_sol bes", "bes.id = be.id_estudiante");
    $this->db->join("biblioteca_solicitudes bs", "bs.id = bes.id_solicitud AND bs.estado = 1");
    $this->db->where("bs.id_tipo_solicitud", $tipo);
    $this->db->group_by("roles");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consolidado_programas($tipo)
  {
    $this->db->select("IF(bes.tabla = 'personas',('N/A'),(SELECT vpp.valor programa FROM visitantes pa INNER JOIN valor_parametro vpp ON vpp.id = pa.id_programa WHERE pa.id = bes.id_estudiante_sol LIMIT 1)) AS programas, COUNT(*) cantidad", false);
    $this->db->from("biblioteca_encuesta_libros_a_tu_clase be");
    $this->db->join("biblioteca_estudiante_sol bes", "bes.id = be.id_estudiante");
    $this->db->join("biblioteca_solicitudes bs", "bs.id = bes.id_solicitud AND bs.estado = 1");
    $this->db->where("bs.id_tipo_solicitud", $tipo);
    $this->db->group_by("programas");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consolidado_departamentos($tipo)
  {
    $this->db->select("IF(bes.tabla = 'personas',(SELECT vpp.valor departamentos FROM personas pa INNER JOIN cargos_departamentos cd ON cd.id = pa.id_cargo INNER JOIN valor_parametro vpp ON vpp.id = cd.id_departamento WHERE pa.id = bes.id_estudiante_sol LIMIT 1),(SELECT vpp.valor departamentos FROM visitantes pa INNER JOIN permisos_parametros pp ON pp.vp_secundario_id = pa.id_programa INNER JOIN valor_parametro vpp ON vpp.id = pp.vp_principal_id WHERE pa.id = bes.id_estudiante_sol LIMIT 1)) AS departamentos,  COUNT(*) cantidad", false);
    $this->db->from("biblioteca_encuesta_libros_a_tu_clase be");
    $this->db->join("biblioteca_estudiante_sol bes", "bes.id = be.id_estudiante");
    $this->db->join("biblioteca_solicitudes bs", "bs.id = bes.id_solicitud AND bs.estado = 1");
    $this->db->where("bs.id_tipo_solicitud", $tipo);
    $this->db->group_by("departamentos");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function validar_estudiante_solicitud($id_solicitud, $usuario)
  {
    $this->db->select('bes.id id, bes.id_solicitud id_solicitud, bes.id_estudiante_sol id_estudiante, bes.tabla tabla, bes.codigo_acceso codigo,bs.id_tipo_solicitud id_tipo_solicitud, vp.valor tipo_solicitud', false);
    $this->db->from("biblioteca_estudiante_sol bes");
    $this->db->join("biblioteca_solicitudes bs", "bs.id = bes.id_solicitud");
    $this->db->join("valor_parametro vp", "vp.id_aux = bs.id_tipo_solicitud");
    $this->db->_protect_identifiers = false;
    $this->db->join('personas p', "p.usuario = '$usuario'");
    $this->db->_protect_identifiers = true;
    $this->db->where("bes.id_solicitud", $id_solicitud);
    $this->db->where("bes.id_estudiante_sol = p.id");
    $this->db->where("bes.tabla", 'personas');
    $query = $this->db->get()->row();
    if (empty($query)) {
      $this->db->select('bes.id id, bes.id_solicitud id_solicitud, bes.id_estudiante_sol id_estudiante, bes.tabla tabla, bes.codigo_acceso codigo, bs.id_tipo_solicitud id_tipo_solicitud, vp.valor tipo_solicitud', false);
      $this->db->from("biblioteca_estudiante_sol bes");
      $this->db->join("biblioteca_solicitudes bs", "bs.id = bes.id_solicitud");
      $this->db->join("valor_parametro vp", "vp.id_aux = bs.id_tipo_solicitud");
      $this->db->_protect_identifiers = false;
      $this->db->join("visitantes p", "SUBSTRING_INDEX(p.correo,'@',1) = '$usuario'");
      $this->db->_protect_identifiers = true;
      $this->db->where("bes.id_solicitud", $id_solicitud);
      $this->db->where("bes.id_estudiante_sol = p.id");
      $this->db->where("bes.tabla", 'visitantes');
      $query = $this->db->get()->row();
    }
    return $query;
  }

  public function validar_encuesta($id)
  {
    $this->db->select("bel.*", false);
    $this->db->from("biblioteca_encuesta_libros_a_tu_clase bel");
    $this->db->where("bel.id_estudiante", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function valor_parametro_id_aux($id)
  {
    $this->db->select("v.*");
    $this->db->from("valor_parametro v");
    $this->db->where("v.id_aux", $id);
    return $this->db->get()->row();
  }
}
