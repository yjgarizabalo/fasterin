<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class publicaciones_model extends CI_Model
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

  public function guardar_solicitud($tabla, $data, $tipo = 1)
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

  public function obtener_indicadores($parametro)
  {
    $this->db->select("vp.id, vp.valor, vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro", false);
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro = $parametro");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_ranking($parametro)
  {
    $this->db->select("vp.id, vp.valor, vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro", false);
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro = $parametro");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_cuartiles()
  {
    $this->db->select("vp.id, vp.valor, vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro", false);
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro = 283");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_inst_ext()
  {
    $this->db->select("vp.id, vp.valor, vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro", false);
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro = 288");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_idioma()
  {
    $this->db->select("vp.id, vp.valor, vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro", false);
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro = 289");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_estados($parametro)
  {
    $estados_int = array('Pub_Ace_Pub_E', 'Pub_Pos_Rec_E', 'Pub_Pos_Ace_E', 'Pub_Red_Pos_E', 'Pub_Pub_Cor_E', 'Pub_Rec_Cor_E', 'Pub_Pos_Cor_E', 'Pub_Ace_Cor_E');
    $this->db->select("vp.valor, vp.valorx, vp.estado, vp.id_aux id, vp.valory, vp.idparametro", false);
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", $parametro);
    $this->db->where_not_in("vp.id_aux", $estados_int);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function buscar_autor($tabla, $dato)
  {
    if ($tabla == "personas") {
      //BUSQUEDA TABLA PERSONAS PARA EMPLEADOS DE LA CUC
      $this->db->select("p.id id,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, 'personas' as tabla", false);
      $this->db->from('personas p');
      $this->db->like("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido)", $dato);
      $this->db->or_like("p.identificacion", $dato);
      $this->db->where("p.estado", 1);
      $query1 = $this->db->get()->result();

      //BUSQUEDA TABLA VISTANTES PARA ESTUDIANTES
      $this->db->select("p.id id, CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, 'visitantes' as tabla", false);
      $this->db->from("visitantes p");
      $this->db->like("CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido)", $dato);
      $this->db->or_like("p.identificacion", $dato);
      $this->db->where("p.estado", 1);
      $query2 = $this->db->get()->result();

      $result = array_merge($query1, $query2);
    } else if ($tabla == "otro") {
      //BUSQUEDA TABLA GENERAL PARA AUTORES EXTERNOS
      $this->db->select("p.id id, p.valor_1 as nombre_completo, 'general' as tabla,
      p.valor_2 as afiliacion", false);
      $this->db->from('info_general p');
      $this->db->like("p.valor_1", $dato);
      $this->db->where("p.estado", 1);
      $query = $this->db->get();

      $result = $query->result_array();
    };
    // print_r($this->db->last_query());
    return $result;
  }

  public function traer_ultima_solicitud($person)
  {
    $this->db->select("ps.id, CONCAT(p.nombre, ' ', p.apellido, ' ',p.segundo_apellido) nombre_completo, p.correo", false);
    $this->db->from('publicaciones_solicitudes ps');
    $this->db->join('personas p', 'ps.id_usuario_registra = p.id');
    $this->db->order_by("id", "desc");
    $this->db->where('id_usuario_registra', $person);
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function traer_ultima_revista()
  {
    $this->db->select("vp.*");
    $this->db->from('valor_parametro vp');
    $this->db->order_by("id", "desc");
    $this->db->where('idparametro', 287);
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
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

  public function listar_publicaciones($id, $id_estado, $id_ranking, $fecha_inicial, $fecha_final)
  {
    $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Pub" || $_SESSION["perfil"] == "Per_Admin_Tal" ? true : false;
    $persona = $_SESSION["persona"];

    $this->db->select("ps.*, vaptro.id tipopub_id, vaptro.valor tipo_pub, vaptr.valor tcbancaria, vap.valor money_type, vpmtr.valor banck_name, vpmt.valor tpago_valor, vptr.valor nom_cod, vptro.valor rev_nom, pss.titulo_articulo ttlo_art, vp.valor nom_cuartil, vpt.valor tipo_solicitud, ps.titulo_articulo titulo, vr.valor ranking, ve.valor estado, CONCAT(p.nombre, ' ', p.apellido, ' ',p.segundo_apellido) persona_registra, p.correo correo_registra, cp.nombre_proyecto proyecto, (SELECT pe.id_estado FROM publicaciones_estados pe WHERE pe.id_publicacion = ps.id AND pe.id_estado <> 'Pub_Arc_E' ORDER BY pe.id DESC limit 1) AS estado_anterior, vpr.valor revista, COALESCE(vpr.valorx, 'N/A') issn, COALESCE(vpr.valorz, 'N/A') isbn, vpr.valory cuartil, vpn.valor indicador, GROUP_CONCAT(DISTINCT vpi.valor SEPARATOR ', ') AS idiomas, pes.id_usuario_valida, ves2.id_aux permisos, ps2.titulo_articulo titulo_articulo", false);
    $this->db->from("publicaciones_solicitudes ps");
    $this->db->join('valor_parametro vr', "ps.id_ranking = vr.id", "left");
    $this->db->join('valor_parametro ve', "ps.id_estado = ve.id_aux");
    $this->db->join('valor_parametro vpr', "ps.id_revista = vpr.id", "left");
    $this->db->join('valor_parametro vpn', "ps.indicador = vpn.id", "left");
    $this->db->join('publicaciones_idiomas pi', "ps.id = pi.id_publicacion", "left");
    $this->db->join('valor_parametro vpi', "pi.id_idioma = vpi.id", "left");
    $this->db->join('valor_parametro vpt', "ps.id_tipo_solicitud = vpt.id_aux");
    $this->db->join('personas p', 'p.id = ps.id_usuario_registra');
    $this->db->join('bonificaciones_solicitudes bs', 'bs.id_publicacion = ps.id', "left");
    $this->db->join('publicaciones_solicitudes ps2', 'ps2.id = bs.id_titulo_articulo', "left");
    $this->db->join('comite_proyectos cp', 'cp.id = ps.id_comite_proyecto', 'left');
    $this->db->join('publicaciones_estados pes', 'pes.id_publicacion = ps.id', 'left');
    $this->db->join('valor_parametro vp', 'ps.id_cuartil_selected = vp.id', "left");
    $this->db->join('publicaciones_solicitudes pss', 'pss.id = ps.id_articulo', 'left');
    $this->db->join('valor_parametro vptro', 'vptro.id = ps.id_revista_selected', 'left');
    $this->db->join('valor_parametro vptr', 'vptr.id = ps.id_codsap_selected', 'left');
    $this->db->join('valor_parametro vpmt', 'vpmt.id = ps.id_pago_selected', 'left');
    $this->db->join('valor_parametro vpmtr', 'vpmtr.id = ps.id_banco_selected', 'left');
    $this->db->join('valor_parametro vap', 'vap.id = ps.id_moneda_selected', 'left');
    $this->db->join('valor_parametro vaptro', 'vaptro.id = ps.rev_o_conf', 'left');
    $this->db->join('valor_parametro vaptr', 'vaptr.id = ps.id_tipocuentabnk_selected', 'left');
    $this->db->join('bonificaciones_actividades_personas apb', "apb.id_actividad = ps.id_tipo_solicitud AND apb.id_persona = $persona", 'left');
    $this->db->join('bonificaciones_estados_actividades eab1', "eab1.actividad_id = apb.id AND apb.estado = 1 AND eab1.estado = 1",'left');
    $this->db->join('bonificaciones_estados_actividades eab2', "eab2.actividad_id = apb.id AND apb.estado = 1 AND eab2.estado = 1",'left');
    $this->db->join('valor_parametro ves1', 'ves1.id = eab1.estado_id', 'left');
    $this->db->join('valor_parametro ves2', 'ves2.id = eab2.estado_id', 'left');
    $this->db->join('parametros par1', 'par1.id = ves1.idparametro', 'left');
    $this->db->join('parametros par2', 'par2.id = ves2.idparametro', 'left');
    if (!$admin){
      $this->db->join('publicaciones_autores pa', "pa.id_publicacion = ps.id AND pa.id_autor = $persona", "left");
      $this->db->join("bonificaciones_autores ba", "ba.id_bonificacion = ps.id AND ba.id_persona = $persona AND ba.estado = 1 OR ((ba.id_programa = eab1.estado_id AND ba.id_bonificacion = ps.id AND par1.id = 86 AND ba.estado = 1) AND (ba.id_bonificacion = ps.id AND ps.id_estado = ves2.id_aux AND par2.id = 302  AND ba.estado = 1))");
    }
    if ($id) $this->db->where('ps.id', $id);
    if ($id_estado) $this->db->where('ps.id_estado', $id_estado);
    if ($id_ranking) $this->db->where('ps.id_ranking', $id_ranking);
    if ($fecha_inicial && $fecha_final) $this->db->where("(DATE_FORMAT(ps.fecha_registra, '%Y-%m-%d') >= DATE_FORMAT('$fecha_inicial', '%Y-%m-%d') AND DATE_FORMAT(ps.fecha_registra, '%Y-%m-%d') <= DATE_FORMAT('$fecha_final', '%Y-%m-%d'))");
    $this->db->where('ps.estado', "1");
    $this->db->_protect_identifiers = false;
    $this->db->order_by("FIELD (pes.id_estado, 'Pub_Red_Pos_E', 'Pub_Pos_Ace_E', 'Pub_Pos_Rec_E', 'Pub_Ace_Pub_E')");
    $this->db->order_by("FIELD (ps.id_estado, 'Pub_Red_E', 'Pub_Pos_E', 'Pub_Ace_E', 'Pub_Rec_E', 'Pub_Pub_E', 'Pub_Cls_E')");
    $this->db->order_by("ps.fecha_registra");
    $this->db->group_by("ps.id");
    $this->db->_protect_identifiers = true;
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_autores_publicacion($id)
  {
    $this->db->select("pa.*,IF(pa.tabla = 'personas', 'Autor CUC', IF(pa.tabla = 'visitantes', 'Autor CUC', 
    (SELECT vp.valor FROM bonificaciones_autores p INNER JOIN valor_parametro vp ON vp.id = p.institucion_ext WHERE p.id = pa.id_autor AND p.afiliacion = 'externo' LIMIT 1))) AS tipo, IF(pa.tabla = 'personas',
    (SELECT CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) FROM personas p WHERE p.id = pa.id_autor LIMIT 1), 
    IF(pa.tabla = 'visitantes', (SELECT CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) FROM visitantes 
    p WHERE p.id = pa.id_autor LIMIT 1), (SELECT CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) FROM visitantes p WHERE p.id = pa.id_autor LIMIT 1))) AS persona, 
    IF(pa.tabla = 'personas', (SELECT p.correo from personas p WHERE p.id = pa.id_autor LIMIT 1), IF(pa.tabla = 'visitantes', 
    (SELECT p.correo FROM visitantes p WHERE p.id= pa.id_autor LIMIT 1), 'none')) AS correo, pc.identificacion identificacion", false);
    $this->db->from("publicaciones_autores pa");
    $this->db->join("personas pc", "pc.id = pa.id_autor");
    $this->db->where("pa.id_publicacion", $id);
    $this->db->_protect_identifiers = false;
    $this->db->order_by("FIELD (pa.tabla, 'personas', 'otro')");
    $this->db->_protect_identifiers = true;
    $query = $this->db->get();
    return $query->result_array();
  }

  /*lostar autores de pago papers*/

  public function Listar_Autores_Pagos($id)
  {
    $this->db->select("ps.id id_pag");
    $this->db->from("publicaciones_solicitudes ps");
    $this->db->where("ps.id", $id);
    $this->db->join("publicaciones_autores pa", "pa.id_publicacion=ps.id");
    $this->db->join("personas p", "p.id=pa.id_autor");
    $this->db->select("CONCAT(p.nombre, ' ', p.segundo_nombre, ' ',p.apellido, ' ' ,p.segundo_apellido) full_name", false);
    $this->db->select("pa.puntos puntos");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function buscar_proyecto($where)
  {
    $this->db->select("cp.*, CONCAT(p.nombre, ' ', p.apellido, ' ',p.segundo_apellido) as nombre_investigador", false);
    $this->db->from('comite_proyectos cp');
    $this->db->join("personas p", "p.id = cp.investigador");
    $this->db->where($where);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function buscar_revista($where)
  {
    $this->db->select("vp.*");
    $this->db->from('valor_parametro vp');
    $this->db->where("vp.idparametro", 287);
    $this->db->where($where);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function buscar_afiliacion($where)
  {
    $this->db->select("vp.*");
    $this->db->from('valor_parametro vp');
    $this->db->where("vp.idparametro", 288);
    $this->db->where($where);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consulta_publicacion_id($id)
  {
    $this->db->select("ps.*, CONCAT(p.nombre, ' ', p.apellido, ' ',p.segundo_apellido) as persona_registra, p.correo correo_registra, vp.valor estado", false);
    $this->db->from("publicaciones_solicitudes ps");
    $this->db->join("personas p", "ps.id_usuario_registra = p.id");
    $this->db->join("valor_parametro vp", "ps.id_estado = vp.id_aux");
    $this->db->where("ps.id", $id);
    $this->db->where("ps.estado", 1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function validar_autor($id)
  {
    $this->db->select("pa.*");
    $this->db->from("publicaciones_autores pa");
    $this->db->where("pa.id", $id);
    $this->db->where("pa.aprobo", 0);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function estado_anterior($id)
  {
    $this->db->select("pe.*");
    $this->db->from("publicaciones_estados pe");
    $this->db->where("pe.id_estado <> 'Pub_Arc_E'");
    $this->db->order_by("id", "desc");
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function validar_distribucion_publicacion($id)
  {
    $this->db->select("COUNT(pa.id) AS cont, SUM(pa.aprobo) AS ready", false);
    $this->db->from("publicaciones_autores pa");
    $this->db->where("pa.id_publicacion", $id);
    $this->db->where("pa.tabla", 'personas');
    $this->db->or_where("pa.tabla", 'visitantes');
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_campos_archivos($estado)
  {
    $this->db->select("vp.id_aux AS name, vp.valor AS placeholder", false);
    $this->db->from("permisos_parametros pp");
    $this->db->join("valor_parametro vp", "vp.id = pp.vp_secundario_id");
    $this->db->where("pp.vp_principal", $estado);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_tipos_adjuntos(){
    $this->db->select('vp.id_aux tipo, vp.valor placeholder');
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', 286);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function verificar_archivos($id, $estado)
  {
    $this->db->select("pad.nombre_real, pad.tipo, pad.nombre_guardado", false);
    $this->db->from("publicaciones_adjuntos pad");
    $this->db->where('pad.id_publicacion', $id);
    $this->db->where('pad.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function validar_archivos($id, $estado)
  {
    $this->db->select("pad.id", false);
    $this->db->from("publicaciones_adjuntos pad");
    $this->db->from("permisos_parametros pp");
    $this->db->where("pp.vp_principal", $estado);
    $this->db->where("pad.tipo = pp.vp_secundario");
    $this->db->where("pad.id_publicacion", $id);
    $query = $this->db->get();
    return $query->result_array();
  }
  public function listar_archivos($id)
  {
    $this->db->select("pad.*, vp.valor tipo_archivo", false);
    $this->db->from("publicaciones_adjuntos pad");
    $this->db->join("valor_parametro vp", "vp.id_aux = pad.tipo");
    $this->db->where("pad.id_publicacion", $id);
    $this->db->_protect_identifiers = false;
    $this->db->order_by("FIELD (pad.estado, '1', '0')");
    $this->db->order_by("pad.fecha_registro", "desc");
    $this->db->_protect_identifiers = true;
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consulta_estado_intermedio($id)
  {
    $this->db->select("pe.id_estado");
    $this->db->from("publicaciones_estados pe");
    $this->db->where("pe.id_publicacion", $id);
    $this->db->order_by("pe.id", "desc");
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_archivo_esp($id, $tipo)
  {
    $this->db->select("pad.id");
    $this->db->from("publicaciones_adjuntos pad");
    $this->db->where("pad.id_publicacion", $id);
    $this->db->where("pad.tipo", $tipo);
    $this->db->order_by("pad.id", "desc");
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function listar_estados($id)
  {
    $this->db->select("pes.*, CONCAT(p.apellido, ' ', p.segundo_apellido, ' ', p.nombre) nombre_completo, vp.valor estado_nombre", false);
    $this->db->from("publicaciones_estados pes");
    $this->db->join("personas p", "p.id = pes.id_usuario_registra");
    $this->db->join("valor_parametro vp", "vp.id_aux = pes.id_estado");
    $this->db->where("pes.id_publicacion", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_publicaciones_pendientes($where, $id = '')
  {
    $sql = "SELECT ps.id_tipo_solicitud, ps.carta_aceptacion, ps.revision_cuartil, ps.adj_pag_inter, ps.adj_pag_extran, ps.id id_publicacion, ps.titulo_articulo titulo, vp.valor estado_actual, CONCAT(p.apellido, ' ', p.segundo_apellido, ' ', p.nombre) docente
        FROM publicaciones_solicitudes ps 
        JOIN publicaciones_estados pe 
          ON pe.id_publicacion = ps.id AND pe.id_estado = 
            (SELECT pes.id_estado 
              FROM publicaciones_estados pes
              WHERE pes.id_publicacion = ps.id ORDER BY pes.id DESC LIMIT 1)
        JOIN valor_parametro vp
          ON vp.id_aux = ps.id_estado
        JOIN personas p
          ON p.id = pe.id_usuario_registra
        WHERE $where 
          GROUP BY ps.id";
    $query = $this->db->query($sql);
    return $query->result_array();
  }

  public function informacion_publicacion($id)
  {
    $this->db->select("ps.*,vpr.id revista_id, vpr.valor revista_nombre, vpr.valory cuartil, vpk.valor ranking, cp.nombre_proyecto nombre_proyecto");
    $this->db->from("publicaciones_solicitudes ps");
    $this->db->join("valor_parametro vpr", "vpr.id = ps.id_revista");
    $this->db->join("valor_parametro vpk", "vpk.id = ps.id_ranking");
    $this->db->join("valor_parametro vpi", "vpi.id = ps.indicador");
    $this->db->join("comite_proyectos cp", "cp.id = ps.id_comite_proyecto");
    $this->db->where("ps.id", $id);
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_idiomas($dato)
  {
    $this->db->select("vp.*");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", 289);
    $this->db->like("vp.valor", $dato);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_valor_parametro($dato)
  {
    $this->db->select("vp.*");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", $dato);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function autores_unicosta($id)
  {
    $this->db->select("pa.*");
    $this->db->from("publicaciones_autores pa");
    $this->db->where("pa.id_publicacion", $id);
    $this->db->where("pa.tabla", 'personas');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function informacion_autor($id)
  {
    $this->db->select("pa.*, IF(pa.tabla = 'personas', (SELECT CONCAT(p.apellido, ' ', p.segundo_apellido, ' ', p.nombre, ' ', p.segundo_nombre) AS nombre_completo FROM personas AS p WHERE p.id = pa.id_autor LIMIT 1), 
    (SELECT p.valor_1 AS nombre_completo FROM info_general AS p WHERE p.id = pa.id_autor LIMIT 1)) 
    AS nombre_completo,
    IF (pa.tabla = 'personas', (SELECT p.identificacion AS identificacion FROM personas AS p WHERE p.id = pa.id_autor LIMIT 1), '') AS identificacion, 
    IF (pa.tabla = 'personas', (SELECT vp.valor AS tipo_identificacion FROM personas AS p INNER JOIN valor_parametro AS vp ON p.id_tipo_identificacion WHERE p.id = pa.id_autor LIMIT 1), '') AS tipo_identificacion,
    IF (pa.tabla = 'personas', 'Autor CUC', (SELECT vp.valor AS afiliacion FROM info_general AS p INNER JOIN valor_parametro AS vp ON p.id_referencia = vp.id WHERE p.id = pa.id_autor LIMIT 1)) AS afiliacion, vpg.valor AS grupo, vpl.valor AS linea, vps.valor AS sublinea", false);
    $this->db->from("publicaciones_autores pa");
    $this->db->join("valor_parametro vpg", "vpg.id = pa.id_grupo", 'left');
    $this->db->join("valor_parametro vpl", "vpl.id = pa.id_linea", 'left');
    $this->db->join("valor_parametro vps", "vps.id = pa.id_sublinea", 'left');
    $this->db->where("pa.id", $id);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_revista_id($id)
  {
    $this->db->select("vp.*");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", 287);
    $this->db->where("vp.id", $id);
    return $this->db->get()->row();
  }

  public function obtener_estados_pub()
  {
    $this->db->select('vp.id id, vp.id_aux idaux, vp.valor estado');
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', 285);
    $this->db->limit(11);
    $query=$this->db->get();
    return $query->result_array();
  }

  public function Buscar_Articulos($titulo_articulo)
  {
    $this->db->select("ps.*");
    $this->db->from("publicaciones_solicitudes ps");
    $this->db->where_not_in("id_estado", "Pub_Neg_E");
    $this->db->where("id_tipo_solicitud", "Pub_Pub");
    $this->db->like("ps.titulo_articulo", $titulo_articulo);
    $this->db->join("valor_parametro vp", "ps.id_revista = vp.id", 'left');
    $this->db->select('vp.valor as nombre_revista, vp.valorx as cod_issn, vp.valory as rev_cuartil, vp.id idrevista, ps.id idpublicacion');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function Buscar_CodSap($valor)
  {
    $this->db->select('vp.id as cod_id, vp.valor as cod_sap, vp.valorx as cod_nombre, vp.estado, vp.fecha_registra as f_regis');
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', 25);
    $this->db->where("(vp.valor LIKE '%" . $valor . "%' OR vp.valorx LIKE '%" . $valor . "%')", NULL, FALSE);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function Buscar_Cuartil()
  {
    $this->db->select("vp.id idcuartil, vp.valor cuartil");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", 283);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function Buscar_Autores($articulo)
  {
    $this->db->select('pa.id_autor autorid, pa.id_publicacion idpub, pa.puntos, pa.aprobo, pa.fecha_registra f_regis');
    $this->db->from('publicaciones_autores pa');
    //$this->db->where('pa.aprobo', 1);
    $this->db->join('personas as p', 'pa.id_autor=p.id');
    $this->db->select("CONCAT(p.nombre, ' ', p.segundo_nombre, ' ',p.apellido, ' ' ,p.segundo_apellido) full_name", false);
    $this->db->join("publicaciones_solicitudes ps", "pa.id_publicacion = ps.id");
    $this->db->select('ps.titulo_articulo');
    $this->db->where("ps.titulo_articulo", $articulo);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function Buscar_Tipo_Moneda($moneda)
  {
    $this->db->select("vpp.id idmoneda, vpp.valor moneda, vpp.valorx abreviado, vpp.valory decimales");
    $this->db->from("valor_parametro vpp");
    $this->db->where("vpp.idparametro", 292);
    $this->db->where("(vpp.valor LIKE '%" . $moneda . "%' OR vpp.valorx LIKE '%" . $moneda . "%')", NULL, FALSE);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function Solicitud_Pago_Papers($datos_array)
  {
    return $this->db->insert("publicaciones_solicitudes", $datos_array);
  }

  public function Buscar_Tipo_Pago()
  {
    $this->db->select("vp.valor pago, vp.id id, vp.id_aux descripcion");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", 293);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function Buscar_Tipo_CuentaB()
  {
    $this->db->select("vp.valor tarjeta, vp.id id_tipo_tarjeta, vp.id_aux descripcion");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", 110);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function Listar_Bancos()
  {
    $this->db->select("vp.valor banco, vp.id idbanco, vp.id_aux descripcion");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", 109);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function Save_Auths_Procents($datos)
  {
    $query = $this->db->insert("publicaciones_autores", $datos);
    return $query;
  }

  public function Last_Pub()
  {
    $this->db->select_max("id");
    $this->db->from("publicaciones_solicitudes ps");
    $query = $this->db->get();
    return $query->row();
  }

  public function Listar_Archivos_Pagop($id)
  {
    $this->db->select("pss.id id_p, pss.carta_aceptacion carta_acept, pss.revision_cuartil cuartil_rev, pss.adj_pag_inter adj_inter, pss.adj_pag_extran adj_extran");
    $this->db->from("publicaciones_solicitudes pss");
    $this->db->where("pss.id", $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function Listar_Tipos_De_Publicacion()
  {
    $this->db->select("vp.id tipopub_id, vp.valor tipo_pub");
    $this->db->from("valor_parametro vp");
    $this->db->where("idparametro", 294);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Validaciones y cambios de estado de pago paper - puede ser temporal esto */

  public function Validar_Estados_AutoresP($id_publicacion)
  {
    $this->db->select("pa.aprobo, pa.id_publicacion id_pub, pe.id_estado, pe.id_usuario_registra");
    $this->db->from("publicaciones_autores pa");
    $this->db->where("pa.id_publicacion", $id_publicacion);
    $this->db->join("publicaciones_estados pe", "pe.id_publicacion=pa.id_publicacion", "left");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function Validar_Estados_PublicacionesP($id_publicacion)
  {
    $this->db->select("ps.id_estado");
    $this->db->from("publicaciones_solicitudes ps");
    $this->db->where("ps.id", $id_publicacion);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function correos_pagop($id)
  {
    $this->db->select("ps.id idp, ps.id_estado idestado,
    ps.id_tipo_solicitud tipo_solicitud,
    pe.id_usuario_registra usuario_registra, p.correo mail");
    $this->db->from("publicaciones_solicitudes ps");
    $this->db->where("ps.id", $id);
    $this->db->join("publicaciones_estados pe", "pe.id_publicacion=ps.id");
    $this->db->join("publicaciones_autores pa", "pa.id_autor=pe.id_usuario_registra");
    $this->db->where("pa.id_publicacion", $id);
    $this->db->join("personas p", "pa.id_autor=p.id");
    $query=$this->db->get();
    return $query->result_array();
  }

  /* public function funcname(){
    $this->db->select('');
    $this->db->from('');
    $this->db->where('');
    $query = $this->db->get();
    return $query->result_array();
  } */

    public function validar_cantidad_de_solicitud($id){
      $this->db->select("ps.id, ps.id_tipo_solicitud, COUNT(*) cantidad, ps.id_estado"); 
      $this->db->from('publicaciones_solicitudes ps');
      //$this->db->join('publicaciones_estados pe', 'pe.id_publicacion = ps.id');
      $this->db->where("ps.id_usuario_registra", $id);
      $this->db->where("ps.id_tipo_solicitud", 'Pub_Bon');
      $this->db->where("ps.id_estado", 'Bon_Sol_Creado');
      $this->db->where("ps.estado", 1 ); 
      $this->db->order_by('fecha_registra', 'ASC');
      $this->db->group_by("ps.id_tipo_solicitud");
      $query = $this->db->get();
      return $query->result();
    }
    
    public function obtener_data__bonificaciones($id){
      $this->db->select("tip_sol.valor id_tipo_solicitud, ps.fecha_registra, ps.id_estado, i_rev.valor id_revista,
      ps.fecha_publicacion, bn.issn, bn.isbn, bn.doi, pub_sol.titulo_articulo id_titulo_articulo,
      cs.valor cuartil_scopus, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,
      bn.url_articulo, bn.editorial, cp.nombre_proyecto, cw.valor cuartil_wos,
      cp.fecha_inicial , cp.fecha_final", false); 
      $this->db->from('publicaciones_solicitudes ps');
      $this->db->join("bonificaciones_solicitudes bn", "bn.id_publicacion = ps.id", "left");
      $this->db->join("publicaciones_solicitudes pub_sol", "pub_sol.id = bn.id_titulo_articulo", "left");
      $this->db->join("valor_parametro i_rev", "i_rev.id = ps.id_revista", "left");
      $this->db->join("valor_parametro cs", "cs.id = bn.id_cuartil_scopus", "left");
      $this->db->join("valor_parametro cw", "cw.id = bn.id_cuartil_wos", "left");
      $this->db->join("valor_parametro tip_sol", "tip_sol.id_aux = ps.id_tipo_solicitud", "left");
      $this->db->join("personas p", "p.id = ps.id_usuario_registra", "left");
      $this->db->join("comite_proyectos cp", "cp.id = bn.id_proyecto", "left");
      $this->db->where("ps.id", $id);
      $query = $this->db->get();
      return $query->result();
    }

    public function obtener_data_revista($id){
      $this->db->select("vp.id, vp.idparametro, vp.valor, vp.valorx, cuart.id cuartil", false); 
      $this->db->from('valor_parametro vp');
      $this->db->join("valor_parametro cuart", "cuart.valorb = vp.valor", "left");
      $this->db->where("vp.idparametro = 287 AND vp.estado = 1 AND vp.id = $id");
      $query = $this->db->get();
      return $query->result();
    }

    public function ver_detalle_autor_bonificaciones($documento, $id_solicitud, $id_afiliacion){
      if($id_afiliacion == "profesor"){
      $this->db->select(" p.identificacion identificacion,
      CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, 
      t_doc.valor tipo_identificacion, dep.valor departamento, ba.afiliacion, cm.valor categ_minciencias, d.valor depart_select, vin.valor vinculacion,iab.hi_scholar, iab.hi_scopus, iab.research_gate, iab.url_cvlac, iab.url_google_scholar, iab.url_research_gate, iab.url_red_acad_disc, iab.url_mendeley, ba.corresponding_author, iab.orcid, iab.publons, iab.gruplac
      ", false);
      $this->db->from('personas p' );
      $this->db->join('csep_profesores csp', 'csp.id_persona = p.id', 'left');
      $this->db->join("csep_profesores_lineas cpl", "cpl.id_profesor = csp.id", "left");
      $this->db->join("valor_parametro lin", "lin.id = cpl.id_linea", "left");
      $this->db->join("valor_parametro sublin", "sublin.id = cpl.id_sub_linea", "left");
      $this->db->join("valor_parametro t_doc", "t_doc.id = p.id_tipo_identificacion", "left");
      $this->db->join("cargos_departamentos cd", "cd.id = p.id_cargo", "left");
      $this->db->join("bonificaciones_autores ba", "ba.id_persona = csp.id_persona OR ba.id_persona = p.id", "left");
      $this->db->join("valor_parametro dep", "dep.id = cd.id_departamento", "left");
      $this->db->join("bonificaciones_informacion_autores iab", "iab.id_bonificaciones_autores = ba.id", "left");
      $this->db->join("valor_parametro cm", "cm.id = iab.categ_minciencias", "left");
      $this->db->join("valor_parametro d", "d.id = iab.departamento", "left");
      $this->db->join("valor_parametro vin", "vin.id = p.id_cargo_sap", "left");
      $this->db->where("p.id", $documento);
      $this->db->where("ba.id_bonificacion", $id_solicitud);
      $this->db->where("ba.afiliacion", $id_afiliacion);
      $this->db->where("ba.estado", 1);
      $query = $this->db->get();
    }else if($id_afiliacion != "profesor"){
      $this->db->select("ba.afiliacion,  CONCAT(v.nombre, ' ', v.segundo_nombre, ' ', v.apellido, ' ', v.segundo_apellido) 
      as nombre_completo, tdoc.valor tipo_identificacion, ie.valor institucion_ext, v.identificacion, 
      prog_acad.valor programa_academico", false);
      $this->db->from("bonificaciones_autores ba");
      $this->db->join("visitantes v", "v.id = ba.id_persona", "left");
      $this->db->join("valor_parametro tdoc", "tdoc.id = v.tipo_identificacion", "left");
      $this->db->join("valor_parametro ie", "ie.id = ba.institucion_ext", "left" );
      $this->db->join("bonificaciones_informacion_autores pa", "pa.id_bonificaciones_autores = ba.id", "left" );
      $this->db->join("valor_parametro prog_acad", "prog_acad.id = pa.programa_acad", "left" );
      $this->db->where("v.id", $documento);
      $this->db->where("ba.id_bonificacion", $id_solicitud);
      $this->db->where("ba.afiliacion", $id_afiliacion);
      $this->db->where("ba.estado", 1);
      $query = $this->db->get();
    }
    return $query->result_array();
    // $query = $this->db->get();
    // $row = $query->row();
    // return $row;
  }

  public function listar_autores_bonificacion($id)
  {
    $query = $this->db->query("SELECT ba.id, ba.afiliacion afil, ba.id_persona id_autor, IF(ba.afiliacion = 'profesor', 'Autor CUC (Profesor)', IF(ba.afiliacion = 'estudiante', 'Autor CUC (Estudiante)',
    'Autor Externo')) AS tabla, IF(ba.afiliacion = 'profesor', (SELECT CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido)
    FROM personas p WHERE p.id = ba.id_persona), IF(ba.afiliacion = 'estudiante', (SELECT CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) 
    FROM visitantes p WHERE p.id = ba.id_persona LIMIT 1), (SELECT CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) FROM visitantes p WHERE p.id = ba.id_persona LIMIT 1)))
    AS nombre_completo, IF(ba.afiliacion = 'profesor', (SELECT p.id FROM personas p WHERE p.id = ba.id_persona), IF(ba.afiliacion = 'estudiante', 
    (SELECT p.identificacion FROM visitantes p WHERE p.id = ba.id_persona), (SELECT p.identificacion FROM visitantes p WHERE p.id = ba.id_persona))) AS identificacion,
    CONCAT(sol.nombre, ' ', sol.apellido, ' ', sol.segundo_apellido) AS solicitante 
    FROM bonificaciones_autores ba INNER JOIN personas sol ON sol.id = ba.id_persona_registra WHERE ba.id_bonificacion = $id AND ba.estado = 1;");
     return $query->result_array();
  }

  public function obtener_opc__bonific()
  {
    $this->db->select("vp.id, vp.valor, vp.valorx, vp.valora, vp.estado, vp.id_aux, vp.valory, vp.idparametro", false);
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro = 20 AND vp.valora = 'select_si_no_bonif'");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function buscar_autor_bon($tabla, $dato)
  {
    if ($tabla == "personas") {
      //BUSQUEDA TABLA PERSONAS PARA EMPLEADOS DE LA CUC
      $this->db->select("p.id id,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, 'personas' as tabla, p.identificacion", false);
      $this->db->from('personas p');
      $this->db->like("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido)", $dato);
      $this->db->or_like("p.identificacion", $dato);
      $this->db->where("p.estado", 1);
      $result = $this->db->get()->result();
    } else if ($tabla == "estudiante" || $tabla == "externo") {
      //BUSQUEDA TABLA GENERAL PARA AUTORES EXTERNOS
      $this->db->select("p.id id, CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido) as nombre_completo, 'visitantes' as tabla, p.identificacion", false);
      $this->db->from("visitantes p");
      $this->db->like("CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido)", $dato);
      $this->db->or_like("p.identificacion", $dato);
      $this->db->where("p.estado", 1);
      $result = $this->db->get()->result();
    };
    return $result;
  }

  public function obtenerAutoresCreados($persona, $bonificacion, $cantidad, $id_afiliacion=''){
		$this->db->select('ba.id');
		$this->db->from("bonificaciones_autores ba");
    if($id_afiliacion) $this->db->where("ba.afiliacion", $id_afiliacion);
		$this->db->where("ba.id_persona = $persona AND ba.id_bonificacion = $bonificacion AND ba.estado = 1");
		$this->db->limit($cantidad);
		$query = $this->db->get();
    $row = $query->row();
    return $row;
	}

  public function asignar_porcentaje($articulo)
  {
    $this->db->select("CONCAT(p.nombre, ' ', p.segundo_nombre, ' ',p.apellido, ' ' ,p.segundo_apellido) full_name,
    ba.id id_bon, ba.id_persona autorid", false);
    $this->db->from('bonificaciones_autores ba');
    $this->db->join('personas p', 'ba.id_persona = p.id');
    $this->db->where("ba.id_bonificacion = $articulo AND ba.afiliacion = 'profesor'");
    $query = $this->db->get();
    return $query->result_array();
  }
  public function getID ($persona, $id_bonificacion, $afiliacion){
    $this->db->select("ba.id", false);
    $this->db->from('bonificaciones_autores ba');
    $this->db->where("ba.id_persona = $persona AND ba.id_bonificacion = $id_bonificacion AND ba.estado = 1");
    if ($afiliacion) $this->db->where("ba.afiliacion", $afiliacion);
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }
  public function obtenerIDSolicitud_bon ($id_solicitud){
    $this->db->select("bs.id");
    $this->db->from("bonificaciones_solicitudes bs");
    $this->db->where("bs.id_publicacion = $id_solicitud AND bs.estado = 1");
    $this->db->limit(1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_autor_porTipo ($id_bonificacion, $afiliacion="") {
    $this->db->select("ba.afiliacion, CONCAT(p.nombre, ' ',p.apellido, ' ' ,p.segundo_apellido) nombre_completo,
    p.identificacion documento, ba.id_persona id, ba.id id_autor, ba.corresponding_author", false);
    $this->db->from("bonificaciones_autores ba");
    if($afiliacion == "profesor"){
      $this->db->join("personas p", "p.id = ba.id_persona", "left");
    }else if($afiliacion == "externo" || $afiliacion == "estudiante"){
      $this->db->join("visitantes p", "p.id = ba.id_persona", "left");
    }
    $this->db->where("ba.id_bonificacion", $id_bonificacion);
    $this->db->where("ba.afiliacion", $afiliacion);
    $this->db->where("ba.estado", 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  function deshabilitar($id, $tabla){
    $date = date('Y-m-d h:i:s');
    $this->db->set('estado', "0");
    $this->db->set('fecha_elimina', $date);
		$this->db->set('id_persona_elimina', $_SESSION['persona']);
    $this->db->where('id', $id);
    $this->db->update($tabla);
    $error = $this->db->_error_message(); 
    if ($error) {
      return "error";
    }
    return 2;
  }

  public function validar_autores_existentes ($id_persona, $afiliacion, $id_bonificacion) {
    $this->db->select("COUNT(*) autores", false);
    $this->db->from("bonificaciones_autores ba");
    $this->db->where("ba.id_persona", $id_persona);
    $this->db->where("ba.afiliacion", $afiliacion);
    $this->db->where("ba.estado", 1);
    $this->db->where("ba.id_bonificacion", $id_bonificacion);
    return  $this->db->get()->row()->autores;
  }

  public function validar_existencia_datos ($tabla, $where){
    $this->db->select("COUNT(*) cantidad", false);
    $this->db->from($tabla);
    $this->db->where("$where");
    $this->db->where('estado', 1);
    return  $this->db->get()->row()->cantidad;
  }

  public function obtener_suma_porcentajes ($id, $afiliacion){
    $this->db->select("ba.first_porcentage porcentaje, ba.id_persona", false); 
    $this->db->from('bonificaciones_autores ba');
    $this->db->where("ba.id_bonificacion", $id);
    $this->db->where("ba.afiliacion", $afiliacion);
    $this->db->where("ba.first_porcentage <> 'null'");
    $this->db->where("ba.estado", 1 );
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_porcentaje ($id_persona, $id_bonificacion, $id_afiliacion) {
    $this->db->select("ba.first_porcentage, ba.second_porcentage, ba.third_porcentage, ba.comentario, ba.id_persona");
    $this->db->from("bonificaciones_autores ba");
    $this->db->where("ba.id_persona", $id_persona);
    $this->db->where("ba.id_bonificacion", $id_bonificacion);
    $this->db->where("ba.afiliacion", $id_afiliacion);
    $this->db->where("ba.estado", 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function validar_existencia_id_articulo ($id_articulo){
    $this->db->select("COUNT(*) cantidad", false);
    $this->db->from('bonificaciones_solicitudes bs');
    $this->db->where('bs.id_titulo_articulo', $id_articulo);
    $this->db->where("bs.estado", 1);
    if ($this->db->get()->row()->cantidad == 0){
      return 0;
    }else{
      return 1;
    }
  }

  public function obtener_afiliaciones_institucionales ($id_bonificacion, $id_afiliacion) {
    $this->db->select('aib.nombre_inst, aib.id, vp.valor pais');
    $this->db->from('bonificaciones_autores ba');
    $this->db->join('bonificaciones_afiliaciones_institucionales aib', "aib.id_autor = ba.id AND aib.estado = 1");
    $this->db->join('valor_parametro vp', 'vp.id = aib.pais');
    $this->db->where("ba.id", $id_bonificacion);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_preguntas_otros_aspectos() {
    $this->db->select("vp.id, vp.id_aux, vp.idparametro, vp.valor, vp.valory, vp.valorz");
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro = 298 AND vp.estado = 1');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_respuestas_otros_aspectos() {
    $this->db->select("vp.id_aux, vp.id, vp.idparametro, vp.valor");
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro = 299 AND vp.estado = 1');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function traer_registro_id($id){
		$this->db->select('vp.id, vp.valor, valory');
    $this->db->from('valor_parametro vp');
    $this->db->where("vp.id = $id");
    $this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

  public function obtener_articulos_suscritos($id){
		$this->db->select('cpi.id, cpi.estado_final, cpi.id_indicador');
    $this->db->from('csep_profesores cp');
    $this->db->join('csep_profesor_indicadores cpi', "cpi.id_profesor = cp.id");
    $this->db->join('valor_parametro vp', "vp.id = cpi.id_indicador AND vp.estado = 1 AND vp.id_aux = 'producto_invest'");
    $this->db->join('valor_parametro vp1', 'vp1.valor = cp.periodo');
    $this->db->where("cp.id_persona = $id");
    $this->db->order_by("cp.id", "desc");
		$query = $this->db->get();
    return $query->result_array();
	}

  public function listar_articulos_cumplidos($id){
		$this->db->select("iab.id, vp.valor cuartil, iab.cantidad_autor, iab.link, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, iab.titulo_articulo title", false);
    $this->db->from('bonificaciones_info_articulos iab');
    $this->db->join('valor_parametro vp', 'vp.id = iab.id_cuartil_autor');
    $this->db->join('personas p', 'p.id = iab.id_autor');
    $this->db->where("iab.id_autor = $id");
    $this->db->where("iab.estado", 1);
		$query = $this->db->get();
    return $query->result_array();
	}

  public function obtener_lista_requerimiento_bon($id_solicitud, $tipo_gestion){
		$this->db->select("vp.id, vp.valor, rrb.id id_req, vp2.valor id_respuesta, rrb.comentario");
    $this->db->from('permisos_parametros pp');
    $this->db->join('bonificaciones_respuestas_requerimientos rrb', "rrb.id_pregunta = pp.vp_secundario_id AND rrb.id_bonificacion = '$id_solicitud' AND rrb.tipo_gestion = '$tipo_gestion'", "left");
    $this->db->join('valor_parametro vp', 'vp.id = pp.vp_secundario_id');
    $this->db->join('valor_parametro vp2', 'vp2.id = rrb.id_respuesta', 'left');
    $this->db->where("pp.vp_principal = '$tipo_gestion' AND pp.estado = 1");
    $this->db->order_by('pp.id', 'ASC');
		$query = $this->db->get();
    return $query->result_array();
	}

  public function obtener_respuestas_requerimientos ($id_pregunta, $id_solicitud, $tipo_gestion){
    $this->db->select('rrb.id, rrb.id_pregunta, rrb.id_respuesta, rrb.comentario, vp.valor pregunta_cal, vp2.valor respuesta_val');
    $this->db->from('bonificaciones_respuestas_requerimientos rrb');
    $this->db->join('valor_parametro vp', 'vp.id = rrb.id_pregunta', "left");
    $this->db->join('valor_parametro vp2', 'vp2.id = rrb.id_respuesta' , "left");
    $this->db->where("rrb.id_pregunta = $id_pregunta AND rrb.id_bonificacion = $id_solicitud AND rrb.estado = 1 AND rrb.tipo_gestion = '$tipo_gestion'");
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_porcentajes_firma ($id_solicitud) {
    $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, ba.first_porcentage_cp first_porcentage, ba.second_porcentage_cp second_porcentage, ba.third_porcentage_cp third_porcentage", false);
    $this->db->from('bonificaciones_autores ba');
    $this->db->join('personas p', 'p.id = ba.id_persona');
    $this->db->where("ba.id_bonificacion = $id_solicitud AND ba.afiliacion = 'profesor' AND ba.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function contar_firmas_por_solicitud ($id_solicitud) {
    $this->db->select("COUNT(ba.id) cantidad");
    $this->db->from('bonificaciones_autores ba');
    $this->db->where("ba.id_bonificacion = $id_solicitud AND ba.estado = 1 AND ba.afiliacion = 'profesor' AND ba.firma IS NULL");
    return  $this->db->get()->row()->cantidad;
  }

  public function verificar_firma_por_id ($id_persona, $id_solicitud) {
    $this->db->select("COUNT(ba.id) cantidad");
    $this->db->from('bonificaciones_autores ba');
    $this->db->where("ba.id_bonificacion = $id_solicitud AND ba.estado = 1 AND ba.afiliacion = 'profesor' AND id_persona = $id_persona AND ba.firma = 1");
    return  $this->db->get()->row()->cantidad;
  }

  public function listar_respuestas_requerimientos ($id_solicitud, $filtrar) {
    $this->db->select("vp.valor requerimiento, vp2.valor respuesta, vp3.valor tipo_gestion, rrb.comentario, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
    $this->db->from('bonificaciones_respuestas_requerimientos rrb', "left");
    $this->db->join('personas p', 'p.id = rrb.id_persona_registra', "left");
    $this->db->join('valor_parametro vp', 'vp.id = rrb.id_pregunta', "left");
    $this->db->join('valor_parametro vp2', 'vp2.id = rrb.id_respuesta', "left");
    $this->db->join('valor_parametro vp3', 'vp3.id_aux = rrb.tipo_gestion', "left");
    $this->db->where('rrb.id_bonificacion', $id_solicitud);
    $this->db->where('rrb.tipo_gestion', $filtrar);
    $this->db->order_by('rrb.id', 'ASC');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_ultimo_estado ($id_solicitud) {
    $this->db->select('ps.id_estado');
    $this->db->from('publicaciones_solicitudes ps');
    $this->db->where("ps.id = $id_solicitud");
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_id_parametro ($id, $val_bus){
    $this->db->select('vp.*');
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', $id);
    $this->db->where('vp.id_aux', $val_bus);
    $this->db->where('vp.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consultar_cambios_estados ($id_usuario, $id_solicitud){
    $this->db->select('pe.id_estado, pe.id');
    $this->db->from('publicaciones_estados pe');
    $this->db->where("pe.id_usuario_registra = $id_usuario AND pe.id_publicacion = '$id_solicitud'");
    $this->db->where("pe.id_estado = 'Bon_Sol_Creado' AND pe.fecha_valida IS NULL");
    //$this->db->order_by('pe.fecha_registro', 'ASC');
    //$this->db->limit(1);
    $query = $this->db->get();
    return $query->result_array();
  }
  
  public function consultar_validacion ($id_aux) {
    $this->db->select('vp.id');
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.id_aux', $id_aux);
    $this->db->where('vp.estado', 1);
    return  $this->db->get()->row()->id;
  }

  public function obtener_comites ($tipo_modulo) {
    $this->db->select("c.nombre, c.descripcion, c.fecha_registra, c.fecha_cierre, vp.valor estado_comite, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, c.id_estado_comite, c.id, (SELECT count(cb.id) FROM bonificaciones_comite cb WHERE cb.id_comite = c.id) AS cantidad", false);
    $this->db->from('comites c');
    $this->db->join('personas p', 'p.id = c.usuario_registra');
    $this->db->join('valor_parametro vp', 'vp.id_aux = c.id_estado_comite');
    //$this->db->join('bonificaciones_comite cb', 'c.id = cb.id_comite', 'left');
    $this->db->where("c.tipo = 'bonificaciones' AND c.estado = 1");
    if($tipo_modulo == 'bonificaciones_comite'){
      $this->db->where("c.id_estado_comite = 'Com_Not'");
      $this->db->or_where("c.id_estado_comite = 'Com_Ter'");
    }
    $this->db->where("c.tipo = 'bonificaciones' AND c.estado = 1");
    $this->db->order_by('c.fecha_registra', 'DESC');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_id_comite () {
    $this->db->select("c.id");
    $this->db->from('comites c');
    $this->db->where("c.tipo = 'bonificaciones' AND c.estado = 1 AND c.id_estado_comite = 'Com_Ini'");
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }
  
  public function listar_solicitudes_por_comite ($comite, $estado = null) {
    $id_persona = $_SESSION['persona'];
    $this->db->select("vp.valor estado, vp2.valor tipo, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, ps.fecha_registra, cb.id_bonificacion, pe.id_usuario_registra, pe.id_estado estados_sols, ps.id", false);
    $this->db->from('bonificaciones_comite cb');
    $this->db->join('publicaciones_solicitudes ps', 'ps.id = cb.id_bonificacion');
    $this->db->join('valor_parametro vp', 'vp.id_aux = ps.id_estado');
    $this->db->join('valor_parametro vp2', 'vp2.id_aux = ps.id_tipo_solicitud');
    $this->db->join('personas p', 'p.id = ps.id_usuario_registra');
    $this->db->join('publicaciones_estados pe', "pe.id_publicacion = cb.id_bonificacion AND pe.id_usuario_registra = $id_persona AND pe.id_estado = 'Aprob_Cons_Acad' OR pe.id_estado = 'Neg_Cons_Acad'", 'left');
    if($estado){
      $this->db->where("ps.id_estado <> 'Aprob_Cons_Acad' AND ps.id_estado <> 'Neg_Cons_Acad'");
    }
    $this->db->where("cb.id_comite = $comite");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_resultado_comite ($id_comite) {
    $this->db->select("vp.valor estado, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
    $this->db->from('publicaciones_estados pe');
    $this->db->join('personas p', 'p.id = pe.id_usuario_registra');
    $this->db->join('valor_parametro vp', 'vp.id_aux = pe.id_estado');
    $this->db->where("pe.id_estado = 'Aprob_Cons_Acad' OR pe.id_estado = 'Neg_Cons_Acad' AND pe.id_publicacion = $id_comite");
    $query = $this->db->get();
    return $query->result_array();
  }
  
  public function obtener_vistos_buenos_aut ($id, $persona) {
    $this->db->select("COUNT('pe.fecha_valida') count, pe.id", false);
    $this->db->from('publicaciones_estados pe');
    $this->db->where("pe.id_publicacion = $id AND pe.id_usuario_registra = $persona AND pe.id_estado = 'Bon_Sol_Creado' AND pe.fecha_valida IS NOT NULL");
    $this->db->order_by('pe.fecha_registro', 'ASC');
    $this->db->limit(1);
    if ($this->db->get()->row()->count == 0){
      return 0;
    }else{
      return 1;
    }
  }

  public function buscar_pais ($nombre) {
    $this->db->select("vp.valor, vp.id");
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', 307);
    $this->db->like("vp.valor", $nombre);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_paises () {
    $this->db->select("vp.valor, vp.id");
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', 307);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_sublineas_inv ($id) {
    $this->db->select('vp.id, vp.valor');
    $this->db->from('permisos_parametros pp');
    $this->db->join('valor_parametro vp', 'vp.id = pp.vp_secundario_id');
    $this->db->where('pp.vp_principal_id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_links_aut ($id_persona, $id_bonificacion, $afiliacion) {
    $this->db->select("iab.id, iab.url_cvlac, iab.url_google_scholar, iab.url_research_gate, iab.url_red_acad_disc, iab.url_mendeley, iab.categ_minciencias, iab.hi_scholar, iab.hi_Scopus, iab.research_gate, iab.departamento, iab.gruplac, iab.publons");
    $this->db->from('bonificaciones_autores ba');
    $this->db->join('bonificaciones_informacion_autores iab', 'iab.id_bonificaciones_autores = ba.id');
    $this->db->where("ba.id_persona = $id_persona AND ba.id_bonificacion = $id_bonificacion AND ba.afiliacion = '$afiliacion' AND ba.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_info_solicitudes ($id) {
    $this->db->select('ba.id autores, ba.first_porcentage, ba.second_porcentage, ba.third_porcentage, COUNT(bs.id) info_articulo, 
    (SELECT COUNT(oab.id) FROM bonificaciones_otros_aspectos oab WHERE oab.id_bonificacion = bs.id) otros_aspectos, (SELECT COUNT(eb.id) FROM bonificaciones_evidencias eb WHERE eb.id_bonificacion = bs.id) bonificaciones_evidencias');
    $this->db->from('publicaciones_solicitudes ps');
    $this->db->join('bonificaciones_autores ba', "ba.id_bonificacion = ps.id AND ba.afiliacion = 'profesor' AND ba.estado = 1", 'left');
    $this->db->join('bonificaciones_solicitudes bs', 'bs.id_publicacion = ps.id', 'left');
    $this->db->where("ps.id = $id");
    $this->db->group_by('ba.id');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_personas($texto){
		$this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) AS fullname", false);
		$this->db->from('personas p');
		//$this->db->where("p.nombre like '%$texto%'");
    $this->db->like("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido)", $texto);
    $this->db->or_like("p.identificacion", $texto);
    $this->db->or_like("p.usuario", $texto);
		$query = $this->db->get();
		return $query->result_array();
	}

  public function pintar_respuestas_ot_as ($id_persona, $id_publicacion, $afiliacion) {
    $this->db->select("oab.id_pregunta, oab.id_respuesta, id_alcance, id_componente, id_objetivo, id_pacto, id_objetivo_alcance");
    $this->db->from('bonificaciones_solicitudes bs');
    $this->db->join('bonificaciones_otros_aspectos oab', 'oab.id_bonificacion = bs.id');
    $this->db->where("bs.id_publicacion = $id_publicacion AND bs.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function pintar_evidencias_existentes ($id_persona, $id_publicacion, $afiliacion) {
    $this->db->select("eb.tipo_dato, eb.dato");
    $this->db->from('bonificaciones_solicitudes bs');
    $this->db->join('bonificaciones_evidencias eb', 'eb.id_bonificacion = bs.id');
    $this->db->where("bs.id_publicacion = $id_publicacion AND bs.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_respuestas_otros_aspectos ($id){

    $this->db->select('oab.id, oab.comentario, pre.valor pregunta, res.valor respuesta');
    $this->db->from('bonificaciones_otros_aspectos oab');
    $this->db->join('valor_parametro res', 'res.id_aux = oab.id_respuesta');
    $this->db->join('valor_parametro pre', 'pre.id = oab.id_pregunta');
    $this->db->where('oab.id_bonificacion', $id);
    $this->db->where('oab.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function pintar_evidencias_ver ($id){

    $this->db->select("eb.nombre_archivo, eb.id, eb.comentario, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
    $this->db->from('bonificaciones_evidencias eb');
    $this->db->join('personas p', 'p.id = eb.id_usuario_registra');
    $this->db->where('eb.id_bonificacion', $id);
    $this->db->where('eb.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_informacion_principal ($id) {
    $this->db->select("vp.valor id_estado, revis.valor revista, ps.fecha_publicacion, ps.fecha_registra, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, bs.url_articulo, bs.issn, ics.valor cuartil_scopus, icw.valor cuartil_wos, bs.editorial, cp.nombre_proyecto proyecto_index, bs.doi, pub.titulo_articulo publicacion, bs.url_indexacion_scopus, bs.url_indexacion_wos, bs.in_press, lin.valor linea, sub.valor sublinea, bs.fecha_anticipada, bs.ano_indexacion, lin.id id_linea, bs.id_sublinea_inv, bs.id_proyecto, bs.id_titulo_articulo, bs.id_cuartil_scopus, bs.id_cuartil_wos, ps.id_revista, ps.ubicacion_proyecto, ps.titulo_proyecto", false);
    $this->db->from('publicaciones_solicitudes ps');
    $this->db->join('valor_parametro vp', 'vp.id_aux = ps.id_estado', 'left');
    $this->db->join('valor_parametro revis', 'revis.id = ps.id_revista', 'left');
    $this->db->join('personas p', 'p.id = ps.id_usuario_registra', 'left');
    $this->db->join('bonificaciones_solicitudes bs', 'bs.id_publicacion = ps.id', 'left');
    $this->db->join('valor_parametro ics', 'ics.id = bs.id_cuartil_scopus', 'left');
    $this->db->join('valor_parametro icw', 'icw.id = bs.id_cuartil_wos', 'left');
    $this->db->join('comite_proyectos cp', 'cp.id = bs.id_proyecto', 'left');
    $this->db->join('publicaciones_solicitudes pub', 'pub.id =  bs.id_titulo_articulo', 'left');
    $this->db->join('valor_parametro lin', 'lin.id = bs.id_linea_inv', 'left');
    $this->db->join('valor_parametro sub', 'sub.id = bs.id_sublinea_inv', 'left');
    $this->db->where('ps.id', $id);
    $this->db->where('ps.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_tipos_escrituras ($id) {
    $this->db->select("vp.valor tipo_escritura, vp.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
    $this->db->from('bonificaciones_tipos_escrituras teb');
    $this->db->join('valor_parametro vp', 'vp.id = teb.categoria');
    $this->db->join('personas p', 'p.id = teb.id_usuario_registra');
    $this->db->where('teb.id_bonificacion', $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_articulos_cumplidos ($autor, $bonificacion) {
    $this->db->select('iab.cantidad_autor, iab.link, iab.titulo_articulo, vp.valor cuartil');
    $this->db->from('bonificaciones_info_articulos iab');
    $this->db->join('valor_parametro vp', 'vp.id = iab.id_cuartil_autor', 'left');
    $this->db->where("iab.id_autor = $autor AND iab.id_bonificacion = $bonificacion AND iab.estado = 1 AND iab.tipo_articulo = 'cumplido' ");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function pintar_porcentajes_totales ($id_bonificacion) {
    $this->db->select("ba.id, ba.first_porcentage_cp, ba.second_porcentage_cp, ba.third_porcentage_cp, p.identificacion, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre_completo", false);
    $this->db->from('bonificaciones_autores ba');
    $this->db->join('personas p', 'p.id = ba.id_persona');
    $this->db->where('ba.id_bonificacion', $id_bonificacion);
    $this->db->where('ba.afiliacion', 'profesor');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_valor($select, $from, $where){
    $this->db->select("$select");
    $this->db->from($from);
    $this->db->where($where);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_actividades($persona){
    $this->db->select('vp.id_aux as id, vp.valor as nombre, ap.id as asignado');
    $this->db->from('valor_parametro vp');
    $this->db->join('bonificaciones_actividades_personas apb', "apb.id_actividad = vp.id AND apb.id_persona = $persona");
    $this->db->where('vp.idparametro = 0');
    $query = $this->db->get();
    return $query->result_array();
	}

  public function listar_tipos_solicitud($persona){
    $this->db->select('vp.id_aux as id, vp.valor as nombre, apb.id as asignado');
    $this->db->from('valor_parametro vp');
    $this->db->join('bonificaciones_actividades_personas apb', "apb.id_actividad = vp.id_aux AND apb.id_persona = $persona", 'left');
    $this->db->where('vp.idparametro = 291');
    $query = $this->db->get();
    return $query->result_array();
	}

  public function validar_asignacion_actividad($id, $persona){
		$this->db->select("IF(COUNT(id) > 0, 0, 1) asignado", false);
		$this->db->from('bonificaciones_actividades_personas');
		$this->db->where('id_actividad', $id);
		$this->db->where('id_persona', $persona);
		$query = $this->db->get();
		return $query->row()->asignado;
	}

  public function listar_estados_adm($actividad){
    $this->db->select('p.nombre parametro, vp.id estado, vp.valor nombre, eab.id asignado, eab.notificacion');
    $this->db->from('bonificaciones_actividades_personas apb');
    $this->db->join('permisos_parametros pp', 'pp.vp_principal = apb.id_actividad', 'left');
    $this->db->join('valor_parametro vp', 'vp.id = pp.vp_secundario_id', 'left');
    $this->db->join('parametros p', 'p.id = vp.idparametro', 'left');
    $this->db->join('bonificaciones_estados_actividades eab', 'eab.estado_id = vp.id AND apb.id = eab.actividad_id', 'left');
    $this->db->where("apb.id = $actividad AND apb.estado = 1 AND pp.estado = 1 AND vp.estado = 1");
    $this->db->order_by('vp.idparametro, vp.valor');
    $query = $this->db->get();
    return $query->result_array();
	}

  public function quitar_actividad($id){
		$this->db->where('id', $id);
		$this->db->delete('bonificaciones_actividades_personas');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}

  public function validar_asignacion_estado($estado, $actividad, $persona){
		$this->db->select("IF(COUNT(eab.id) > 0, 0, 1) asignado",false);
		$this->db->from('bonificaciones_estados_actividades eab');
		$this->db->where('eab.actividad_id', $actividad);
		$this->db->where('eab.estado_id', $estado);
		$query = $this->db->get();
		return $query->row()->asignado;
	}

  public function quitar_estado($id){
		$this->db->where('id', $id);
		$this->db->delete('bonificaciones_estados_actividades');
		$error = $this->db->_error_message(); 
		if ($error) return 0;
		return 1;
	}

  public function get_where($tabla, $data){
		return $this->db->get_where($tabla, $data);
	}

  public function obtener_csep ($persona) {
    $this->db->select('cp.id_programa, cp.id_departamento');
    $this->db->from('csep_profesores cp');
    $this->db->where("cp.id_persona = $persona AND cp.estado_registro = 1");
    $this->db->order_by('cp.fecha_registra', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_datos_solicitud ($id) {
    $this->db->select('tt.titulo_articulo, bs.issn, rev.valor revista, bs.url_indexacion_scopus, bs.url_indexacion_wos, sco.valor id_cuartil_scopus, wos.valor id_cuartil_wos, bs.cuartil_final, bs.categoria_final');
    $this->db->from('publicaciones_solicitudes ps');
    $this->db->join('bonificaciones_solicitudes bs', 'bs.id_publicacion = ps.id');
    $this->db->join('publicaciones_solicitudes tt', 'tt.id = bs.id_titulo_articulo');
    $this->db->join('valor_parametro sco', 'sco.id = bs.id_cuartil_scopus', 'left');
    $this->db->join('valor_parametro wos', 'wos.id = bs.id_cuartil_wos', 'left');
    $this->db->join('valor_parametro rev', 'rev.id = ps.id_revista');
    $this->db->where('ps.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_autores ($id, $afiliacion) {
    $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.identificacion, ba.id, vp.valor programa",false);
    $this->db->from('bonificaciones_autores ba');
    if($afiliacion == 'profesor'){
      $this->db->join('personas p', 'p.id = ba.id_persona', 'left');
    }else{
      $this->db->join('visitantes p', 'p.id = ba.id_persona', 'left');
    }
    $this->db->join('valor_parametro vp', 'vp.id = ba.id_programa', 'left');
    $this->db->where("ba.id_bonificacion = $id AND ba.afiliacion = '$afiliacion' AND ba.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_autores_internacionales ($id) {
    $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.identificacion, ba.id, pais.valor pais, vp.valor institucion",false);
    $this->db->from('bonificaciones_autores ba');
    $this->db->join('valor_parametro vp', 'vp.id = ba.institucion_ext', 'left');
    $this->db->join('valor_parametro pais', 'pais.id = vp.valory', 'left');
    $this->db->join('visitantes p', 'p.id = ba.id_persona', 'left');
    $this->db->where("ba.afiliacion = 'externo' and ba.estado = 1 AND pais.valor <> 'COLOMBIA' AND ba.id_bonificacion =  $id");
    $query = $this->db->get();
    return $query->result_array();
  } 

  public function traer_correspondencia ($id) {
    $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, ba.identificacion, ba.id");
    $this->db->from('bonificaciones_autores ba');
    $this->db->join('personas p', 'p.id = ba.id_persona', 'left');
    $this->db->where("ba.estado = 1 AND ba.afiliacion =  'profesor' AND ba.corresponding_author = 1 AND ba.id_bonificacion = $id");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consultas_liquidaciones ($valory, $id_bonificacion) {
    $this->db->select("res.valor respuesta, rrb.tipo_gestion, rrb.id_pregunta");
    $this->db->from('valor_parametro vp');
    $this->db->join('bonificaciones_respuestas_requerimientos rrb', 'rrb.id_pregunta =  vp.id');
    $this->db->join('valor_parametro res', 'res.id = rrb.id_respuesta');
    $this->db->where("vp.valory = '$valory' AND rrb.id_bonificacion =  $id_bonificacion");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_autores_liquidacion ($id_bonificacion) {
    $this->db->select("p.identificacion, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.id id_autor, ba.id, ba.second_porcentage, ba.third_porcentage",false);
    $this->db->from('bonificaciones_autores ba');
    $this->db->join('personas p', 'p.id = ba.id_persona', 'left');
    $this->db->where("ba.id_bonificacion = $id_bonificacion and ba.estado = 1 AND ba.afiliacion = 'profesor'");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_personas_liquidacion ($id_bonificacion, $id_estado) {
    $this->db->select("p.identificacion, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.id id_autor, ba.id",false);
    $this->db->from('bonificaciones_autores ba');
    $this->db->join('bonificaciones_estados_actividades bea1', 'bea1.estado_id = ba.id_programa AND bea1.estado = 1');
    $this->db->join('bonificaciones_estados_actividades bea2', "bea2.actividad_id = bea1.actividad_id AND bea2.estado = 1");
    $this->db->join('valor_parametro vp1', 'vp1.id = bea1.estado_id');
    $this->db->join('valor_parametro vp2', "vp2.id = bea2.estado_id AND vp2.id_aux = '$id_estado'");
    $this->db->join('parametros p1', 'p1.id = vp1.idparametro');
    $this->db->join('parametros p2', 'p2.id = vp2.idparametro');
    $this->db->join('bonificaciones_actividades_personas bap1', "bap1.id = bea1.actividad_id AND bap1.estado = 1");
    $this->db->join('publicaciones_solicitudes ps', "ps.id_tipo_solicitud = bap1.id_actividad AND ps.estado = 1 AND ps.id = $id_bonificacion");
    $this->db->join('personas p', 'p.id = bap1.id_persona');
    $this->db->where("ba.id_bonificacion = $id_bonificacion AND ba.afiliacion = 'profesor'");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_info_autor_liq ($id_bonificacion, $id_persona, $identificacion, $tipo) {
    $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, vp.valor cargo, cm.valor categoria_minciencias, p.identificacion, ba.second_porcentage porc_pdt, ba.third_porcentage porc_bon, bls.base_liquidacion, bls.bonificacion_base, bls.coautoria_estudiante, bls.visibilidad, bls.solucion_problema, bls.total_autor", false);
    $this->db->from('personas p');
    $this->db->join('valor_parametro vp', 'vp.id = p.id_cargo_sap', 'left');
    $this->db->join('bonificaciones_autores ba', "ba.id_persona = p.id AND ba.afiliacion = 'profesor' AND ba.id_bonificacion = $id_bonificacion", 'left');
    $this->db->join('bonificaciones_informacion_autores iab', 'iab.id_bonificaciones_autores = ba.id', 'left');
    $this->db->join('bonificaciones_liquidacion_solicitud bls', "bls.id_persona = ba.id_persona AND bls.id_bonificacion = ba.id_bonificacion AND bls.estado = 1", 'left');
    $this->db->join('valor_parametro cm', 'cm.id = iab.categ_minciencias', 'left');
    if ($id_persona || $identificacion) $this->db->where("p.id =  $id_persona OR p.identificacion = $identificacion");
    if($tipo) $this->db->where("bls.tipo = '$tipo'");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_info_gestor_liq ($id_bonificacion, $id_persona, $identificacion, $tipo) {
    $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, vp.valor cargo, p.identificacion, bls.base_liquidacion, bls.bonificacion_base, bls.total_autor", false);
    $this->db->from('personas p');
    $this->db->join('valor_parametro vp', 'vp.id = p.id_cargo_sap', 'left');
    $this->db->join('bonificaciones_liquidacion_solicitud bls', "bls.id_persona = p.id AND bls.estado = 1 AND bls.tipo = '$tipo' AND bls.id_bonificacion = $id_bonificacion", 'left');
    if ($id_persona && $identificacion) $this->db->where("p.id =  $id_persona OR p.identificacion = $identificacion");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_data_liq_final ($persona, $id) {
    $this->db->select('bs.cuartil_final, bs.categoria_final, iab.categ_minciencias, ba.id, vp.valor cuartil');
    $this->db->from('bonificaciones_solicitudes bs');
    $this->db->join('bonificaciones_autores ba', "ba.id_bonificacion = bs.id_publicacion AND ba.afiliacion = 'profesor' AND ba.id_persona = $persona", 'left');
    $this->db->join('bonificaciones_informacion_autores iab', 'iab.id_bonificaciones_autores = ba.id AND iab.estado = 1', 'left');
    $this->db->join('valor_parametro vp', 'vp.id = bs.cuartil_final', 'left');
    $this->db->where("bs.id_publicacion = $id AND bs.estado = 1");
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_valor_bonificacion_autores ($categ_minciencias, $categoria_final, $cuartil_final) {
    $this->db->select('bva.valor, bva.id');
    $this->db->from('bonificaciones_valores_autores bva');
    $this->db->where("bva.categoria_minciencias = $categ_minciencias AND bva.cuartil = $cuartil_final AND bva.categoria_docente = $categoria_final AND bva.estado = 1");
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_valor_bonificacion ($tipo, $cuartil, $categoria) {
    $this->db->select('bvgd.valor, bvgd.id');
    $this->db->from('bonificacion_valores_ges_dir bvgd');
    $this->db->where("bvgd.tipo = '$tipo' AND bvgd.cuartil = $cuartil AND bvgd.categoria = $categoria AND bvgd.estado = 1");
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_categoria_liquidacion ($id_aux, $tipo) {
    $this->db->select('vp.valor, vp.id');
    $this->db->from('valor_parametro vp');
    $this->db->like("vp.id_aux", $id_aux);
    $this->db->like("vp.valory", $tipo);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function validar_viabilidad_bonificacion ($id_bonificacion) {
    $this->db->select("(SELECT COUNT(vp2.id) FROM valor_parametro vp2 WHERE vp2.id = bs.idioma AND vp2.valor LIKE '%ingles%') idioma, (SELECT COUNT(vp3.id) FROM valor_parametro vp3 WHERE vp3.id = inst.valory AND vp3.estado = 1 AND vp3.valor NOT LIKE '%colombia%') pais, (SELECT COUNT(ba2.id) FROM bonificaciones_autores ba2 WHERE ba2.id_bonificacion = $id_bonificacion AND ba2.afiliacion = 'profesor' AND ba2.estado = 1 AND ba2.corresponding_author = 1) corresponding_author", false);
    $this->db->from('bonificaciones_solicitudes bs');
    $this->db->join('valor_parametro vp', 'vp.id = bs.idioma', 'left');
    $this->db->join('bonificaciones_autores ba', "ba.id_bonificacion = bs.id_publicacion AND ba.afiliacion = 'externo' AND ba.estado = 1", 'left');
    $this->db->join('valor_parametro inst', 'inst.id = ba.institucion_ext', 'left');
    $this->db->where("bs.id_publicacion = $id_bonificacion");
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function obtener_liquidacion_total ($id_bonificacion) {
    $this->db->select('bls.id_persona, bls.total_autor');
    $this->db->from('bonificaciones_liquidacion_solicitud bls');
    $this->db->where("bls.id_bonificacion = $id_bonificacion AND bls.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_liquidacion_por_tipo ($id_bonificacion, $tipo) {
    $this->db->select('bls.id');
    $this->db->from('bonificaciones_liquidacion_solicitud bls');
    $this->db->where("bls.id_bonificacion = $id_bonificacion AND bls.tipo = '$tipo' AND bls.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function traer_programas ($id_bonificacion, $estado) {
    $this->db->select("ba.id_programa, pe.id, bap.id_persona, pe.id_estado, (select COUNT(pe2.id) from publicaciones_estados pe2 WHERE pe2.id_publicacion = $id_bonificacion AND pe2.id_usuario_valida = bap.id_persona AND pe2.id_estado IN ('Bon_Sol_Gest_Aprob', 'Bon_Sol_Gest_Deni')) cant_est", false);
    $this->db->from('bonificaciones_autores ba');
    $this->db->join('bonificaciones_estados_actividades bea', 'bea.estado_id = ba.id_programa AND bea.estado = 1', 'left');
    $this->db->join('bonificaciones_actividades_personas bap', "bap.id = bea.actividad_id AND bap.estado = 1", 'left');
    $this->db->join('bonificaciones_estados_actividades bea1', "bea.actividad_id = bap.id AND bea.estado = '$estado'", 'left');
    $this->db->join('publicaciones_estados pe', "pe.id_publicacion = ba.id_bonificacion AND pe.id_usuario_valida <> bap.id_persona");
    $this->db->where("ba.id_bonificacion =  $id_bonificacion AND pe.id_usuario_valida <> bap.id_persona  AND ba.estado = 1 AND ba.afiliacion = 'profesor' AND (pe.id_estado <> 'Bon_Sol_Gest_Aprob' OR pe.id_estado <> 'Bon_Sol_Gest_Deni')");
    $this->db->group_by("ba.id");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function traer_aprob_gestores ($id_bonificacion) {
    $this->db->select("pe.id_estado, pe.id");
    $this->db->from('publicaciones_estados pe');
    $this->db->where("pe.id_publicacion = $id_bonificacion AND pe.id_estado IN ('Bon_Sol_Gest_Aprob', 'Bon_Sol_Gest_Deni') AND pe.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function verificar_permisos_persona ($id, $estado){
    $this->db->select("COUNT(vp.id) permiso, vp.id_aux estado");
    $this->db->from('bonificaciones_actividades_personas bap');
    $this->db->join('bonificaciones_estados_actividades bea', "bea.actividad_id = bap.id", 'left');
    $this->db->join('valor_parametro vp', "vp.id = bea.estado_id AND vp.id_aux = '$estado'", 'left');
    $this->db->where("bap.id_persona = $id AND bap.estado = 1");
    $query = $this->db->get();
    $row = $query->row();
    return $row;
  }

  public function listar_cuar_liq_final ($id_solicitud) {
     $sql = "SELECT vp.id, vp.valor 
     FROM valor_parametro vp 
     JOIN bonificaciones_solicitudes bs 
     ON vp.id IN (bs.id_cuartil_scopus, bs.id_cuartil_wos) 
     and bs.id_publicacion = $id_solicitud 
     AND bs.estado = 1
     GROUP BY vp.id";
    $query = $this->db->query($sql);
    return $query->result_array();

  }
  
  public function listar_cat_liq_final ($id_solicitud) {
    $this->db->select();
    $this->db->from('bonificaciones_solicitudes bs');
    $this->db->join('bonificaciones_tipos_escrituras bte', "bte.id_bonificacion = bs.id AND bte.estado = 1");
    $this->db->join('valor_parametro vp', 'vp.id = bte.categoria AND vp.estado = 1');
    $this->db->where("bs.id_publicacion = $id_solicitud AND bs.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function miembros_comite(){
    $this->db->select("bap.id_persona");
    $this->db->from("bonificaciones_estados_actividades bea");
    $this->db->join("valor_parametro vp", "vp.id = bea.estado_id AND bea.estado = 1 AND vp.id_aux = 'Bon_Sol_Cons_Acad'");
    $this->db->join("bonificaciones_actividades_personas bap", "bap.id = bea.actividad_id AND bap.estado = 1 AND bap.id_actividad = 'Pub_Bon'");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_respuesta_por_miembro ($persona, $id_solicitud) {
    $this->db->select("pe.id_estado");
    $this->db->from("publicaciones_estados pe");
    $this->db->where("pe.id_usuario_registra = $persona AND pe.id_publicacion = $id_solicitud AND (pe.id_estado = 'Neg_Cons_Acad' OR pe.id_estado = 'Aprob_Cons_Acad') AND pe.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function tiene_permisos ($id_persona, $id_estado) {
    $this->db->select("*");
    $this->db->from("valor_parametro vp");
    $this->db->join("bonificaciones_estados_actividades bea", "bea.estado_id = vp.id AND bea.estado = 1");
    $this->db->join("bonificaciones_actividades_personas bap", "bap.id = bea.actividad_id AND bap.estado = 1 AND bap.id_persona =  $id_persona");
    $this->db->where("vp.id_aux = '$id_estado' AND vp.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consultar_info_solicitud ($id_solicitud) {
    $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.correo", false);
    $this->db->from("publicaciones_solicitudes ps");
    $this->db->join("personas p", "p.id = ps.id_usuario_registra AND p.estado=1");
    $this->db->where("ps.id =  $id_solicitud AND p.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }

  public function consultar_notificaciones_personas ($estado) {
    $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.correo", false);
    $this->db->from("bonificaciones_estados_actividades bea");
    $this->db->join("bonificaciones_actividades_personas  bap", "bap.id = bea.actividad_id AND bap.estado = 1");
    $this->db->join("personas p", "p.id = bap.id_persona AND p.estado = 1");
    $this->db->where("bea.estado_id = $estado AND bea.notificacion = 1 AND bea.estado = 1");
    $query = $this->db->get();
    return $query->result_array();
  }
}