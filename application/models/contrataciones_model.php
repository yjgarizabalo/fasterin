<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class contrataciones_model extends CI_Model
{
  //var $table_valor_parametro = "valor_parametro";

  /* Buscar numero de contrato macro "ncm" */
  public function buscar_ncm($valor, $idparametro)
  {
    $this->db->select('vp.id as ncm_id, vp.valor as contrato, vp.valorx as entidad, vp.valory as codsap');
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', $idparametro);
    $this->db->where("(vp.valor LIKE '%" . $valor . "%'
    OR vp.valorx LIKE '%" . $valor . "%'
    OR vp.valory LIKE '%" . $valor . "%')", NULL, FALSE);
    $this->db->where('vp.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Buscar CodSAP */
  public function Buscar_CodSap($valor)
  {
    $this->db->select('vp.id as cod_id,
    vp.valor as cod_sap,
    vp.valorx as cod_nombre,
    vp.estado,
    vp.fecha_registra f_regis,
    vp.valorz tipo_contrato');
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', 25);
    $this->db->where("(vp.valor LIKE '%" . $valor . "%' OR vp.valorx LIKE '%" . $valor . "%')", NULL, FALSE);
    if ($valor != "undefined") {
      $this->db->where("vp.valorz", "cont_conv");
      $this->db->or_where("vp.valorz", "cont_preg");
      $this->db->or_where("vp.valorz", "cont_posg");
    }
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Funcion call_adjs */
  public function call_adjs($tps, $idparametro)
  {
    $this->db->select("vp.valor doc_name, vp.valorx name_and_id, vp.valory adj_required, vp.valorz popover");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", $idparametro);
    $this->db->where("vp.valory", "per_nj");
    $this->db->or_where("vp.valory", $tps);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Buscar Contratante */
  public function Buscar_Contratante($valor, $idparametro)
  {
    $this->db->select('vp.id as id, vp.valor as nombre, vp.valorx as nit_cedula, vp.valory representante, vp.estado estado, vp.fecha_registra as f_regis');
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', $idparametro);
    $this->db->where("(vp.valor LIKE '%" . $valor . "%' OR vp.valorx LIKE '%" . $valor . "%')", NULL, FALSE);
    $this->db->where('vp.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Buscar Contratista */
  public function Buscar_Contratista($valor, $idparametro = "", $id = "")
  {
    $this->db->select('vp.id id, vp.valor nombre, vp.valory identy, vp.valorz correo');
    $this->db->from("valor_parametro vp");
    $this->db->where("(vp.valor LIKE '%" . $valor . "%' OR vp.valory LIKE '%" . $valor . "%' OR vp.valorz LIKE '%" . $valor . "%')", NULL, FALSE);
    if (empty($idparametro) || $idparametro == "") {
      $this->db->where('vp.idparametro', 37);
    }else{
      $this->db->where_in('vp.idparametro', [37, $idparametro]);
    }
    
    if($id != "") $this->db->where('vp.id', $id);
    $this->db->where("vp.estado", 1);
    $query = $this->db->get();
    return $query->result_array();
  }

   /* Buscar Contratista */
   public function listar_administrar_contratistas($idparametro)
   {
     $this->db->select('vp.id id, vp.valor nombre, vp.valory identy, vp.valorz correo');
     $this->db->from("valor_parametro vp");
     $this->db->where('vp.idparametro', $idparametro);
     $this->db->where("vp.estado", 1);
     $query = $this->db->get();
     return $query->result_array();
   }

  /* Listar contratos */
  public function Listar_Contratos($dato, $id_contrato = "", $row = false)
  {
    $admin = false;
    $persona = $_SESSION['persona'];

    if ($_SESSION['perfil'] == "Per_Admin" || $_SESSION['perfil'] == "Admin_Cont") {
      $admin = true;
    }

    $this->db->select(
      "c.id id,
      c.id_usuario_registra soli,
      c.contrato_estado estado_cont,
      c.num_contrato num_con,
      c.adj_contrato,
      c.objetivo,
      c.valor,
      c.plazo,
      c.fecha_registra fecha_sus,
      c.fecha_inicio fecha_ini,
      c.fecha_terminacion fecha_ter,
      c.cod_sap codSAP,
      c.firma_contratante,
      c.firma_contratista,
      c.id_usuario_registra,
      c.modelo_contrato,
      c.tipo_contrato tipo_cont,
      c.contratista,
      CONCAT(p.nombre, ' ', p.apellido, ' ',p.segundo_apellido) solicitante,
      p.correo correo_inst,
      vp.valor estado_solicitud,
      vtr.valor contrato,
      vtro.valor nombre_tante,
      vptro.valor nombre_tista,
      vpttro.valorz tipo_contrato,
      vvtro.valor type_person,
      garantia.valor garantia,
      garantia.id_aux garantia_id,
      vptro.valory tista_cedula_nit,
      mc.valor modelo_contrato_valor",
      FALSE
    );
    $this->db->from('contrataciones c');
    $this->db->where('c.estado', 1);
    $this->db->join('personas p', 'c.id_usuario_registra=p.id');
    $this->db->join('valor_parametro vp', 'c.contrato_estado=vp.id_aux');
    $this->db->join('valor_parametro vtr', 'c.num_contrato_macro=vtr.id', "left");
    $this->db->join('valor_parametro vtro', 'c.contratante=vtro.id', "left");
    $this->db->join('valor_parametro vptro', 'c.contratista=vptro.id', "left");
    $this->db->join('valor_parametro vpttro', 'c.cod_sap=vpttro.id', "left");
    $this->db->join('valor_parametro vvtro', 'c.id_tipo_persona=vvtro.id', "left");
    $this->db->join('valor_parametro garantia', 'c.id_garantia=garantia.id', "left");
    $this->db->join('valor_parametro mc', 'c.modelo_contrato=mc.id_aux', "left");   
  
    if (!empty($id_contrato)) {
      $this->db->where("c.id", $id_contrato);
    } else {
      if (!$admin) {
        $this->db->join("actividad_persona_cont ap", "ap.actividad_id = c.tipo_contrato AND ap.persona_id = '$persona'", 'LEFT'); 
        $this->db->where("(c.id_usuario_registra = $persona OR ap.actividad_id = c.tipo_contrato)", null, false);
      }
      
      if (!empty($dato) && !is_numeric($dato)) {
        $this->db->where("(c.fecha_inicio LIKE '%" . $dato . "%') OR (c.id LIKE '%" . $dato . "%')", null, false);
      } elseif (is_numeric($dato)) {
        $this->db->or_like("c.id", $dato);
      }
    }
    
    $this->db->group_by('c.id');
    $this->db->order_by('c.id DESC');
    $query = $this->db->get();
    //echo $this->db->last_query();
    if ($row) {
      return $query->row();
    }else{
      return $query->result_array();
    }    
  }

  /* Obtener ultima contratacion */
  public function Last_Contra()
  {
    $this->db->select_max('id');
    $this->db->from('contrataciones');
    $query = $this->db->get();
    return $query->row();
  }

    /* Obtener ultima contratacion usuario registra */
    public function obtener_ultimo_contrato_usuario_registra($id_usuario)
    {
      $this->db->select('c.*');
      $this->db->from('contrataciones c');
      $this->db->where("id = (SELECT MAX(id) FROM contrataciones c2 WHERE c2.id_usuario_registra = $id_usuario)");
      $query = $this->db->get();
      return $query->row();
    }

  /* Verificar contratos */
  public function Verificar_Contratos($id, $last_id)
  {
    $this->db->select('cs.id_solicitud, cs.id_estado');
    $this->db->from('contrataciones_estados cs');
    $this->db->where('cs.id_solicitud', $id);
    $this->db->where('cs.id', $last_id);
    $this->db->where('cs.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function check_maxId($ids)
  {
    $this->db->select('id');
    $this->db->from('contrataciones_estados');
    $this->db->where('id_solicitud', $ids);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Listar estados para historial */
  public function listar_estados($id)
  {
    $this->db->select("CONCAT(pe.nombre, ' ',pe.apellido,' ',pe.segundo_apellido) persona_mod, ce.fecha_registra, ce.observacion, vp.valor estado, ce.id_estado", false);
    $this->db->from('contrataciones_estados ce');
    $this->db->join('valor_parametro vp', 'vp.id_aux=ce.id_estado');
    $this->db->join('personas pe', 'pe.id=ce.id_usuario_registra');
    $this->db->where('ce.id_solicitud', $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Listar estados para historial */
  public function listar_firmas($id)
  {
    $this->db->select("firma_contratante, firma_contratista", false);
    $this->db->from('contrataciones c');
    $this->db->where('c.id', $id);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Listar tipo de personas, juridica o natural */
  public function listar_tipo_personas($idparametro)
  {
    $this->db->select("vp.id, vp.id_aux idaux, vp.valor tipo_persona");
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', $idparametro);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Listar tipo de personas, juridica o natural */
  public function listar_tipo_garantia($idparametro)
  {
    $this->db->select("vp.id, vp.id_aux idaux, vp.valor tipo_persona");
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', $idparametro);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Obtener contratos solicitados - pendientes */
  public function obtener_contratos_pendientes()
  {
    if ($_SESSION['perfil'] == "Per_Admin" || $_SESSION['perfil'] == "Admin_Cont") {
      $this->db->select("c.id, CONCAT(pe.nombre, ' ', pe.apellido, ' ',pe.segundo_apellido) persona_solicita,
      vp.valor estado, c.id, c.num_contrato_macro ncm, c.num_contrato num_cont, vtr.valor contrato", false);
      $this->db->from('contrataciones c');
      $this->db->join('personas pe', 'pe.id=c.id_usuario_registra');
      $this->db->join('valor_parametro vp', 'vp.id_aux=c.contrato_estado');
      $this->db->join('valor_parametro vtr', 'vtr.id=c.num_contrato_macro', 'left');
      $this->db->where('c.contrato_estado', 'Cont_Soli_E');
      $query = $this->db->get();
      return $query->result_array();
    } else {
      return false;
    }
  }

  /* Listar archivos adjuntos de cada contrato */
  public function listar_archivos_contratos($ids)
  {
    $this->db->select('ca.nombre_real, ca.nombre_guardado, ca.fecha_registra');
    $this->db->from('contrataciones_adjuntos ca');
    $this->db->where('ca.id_solicitud', $ids);
    $this->db->where('ca.estado', 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Listar personas */
  public function listar_personas($texto)
  {
    $this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) AS fullname", false);
    $this->db->from('personas p');
    $this->db->where("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) like '%$texto%' or p.usuario like '%$texto%' or p.identificacion like '%$texto%'");
    $query = $this->db->get();
    //echo $this->db->last_query();
    return $query->result_array();
  }

  public function listar_actividades($persona, $idparametro)
  {
    $this->db->select("vp.id_aux id, vp.valor nombre, ap.id asignado");
    $this->db->from("valor_parametro vp");
    $this->db->join("actividad_persona_cont ap", "vp.id_aux=ap.actividad_id AND ap.persona_id = $persona", "left");
    $this->db->where("vp.idparametro", $idparametro);
    $this->db->order_by("vp.valor");
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Quitar actividad */
  public function quitar_actividad($id)
  {
    $this->db->where('actividad_id', $id);
    $this->db->delete('estados_actividades_cont');
    $this->db->where('id', $id);
    $this->db->delete('actividad_persona_cont');
    $error = $this->db->_error_message();
    if ($error) return 0;
    return 1;
  }

  /* Quitar estado */
  public function quitar_estado($id)
  {
    $this->db->where('id', $id);
    $this->db->delete('estados_actividades_cont');
    $error = $this->db->_error_message();
    if ($error) return 0;
    return 1;
  }

  /* Validar asiganciones */
  public function validar_asignacion_actividad($id, $persona)
  {
    $this->db->select("IF(COUNT(id) > 0, 0, 1) asignado", false);
    $this->db->from('actividad_persona_cont');
    $this->db->where('actividad_id', $id);
    $this->db->where('persona_id', $persona);
    $query = $this->db->get();
    return $query->row()->asignado;
  }

  /* listar estados permisos */
  public function listar_estados_permisos($actividad)
  {
    $this->db->select("p.nombre parametro, vp.id estado, vp.valor nombre, vp.id_aux, ea.id asignado, ea.notificacion");
    $this->db->from("actividad_persona_cont ap");
    $this->db->join("permisos_parametros pp", "pp.vp_principal = ap.actividad_id");
    $this->db->join("valor_parametro vp", "vp.id = pp.vp_secundario_id");
    $this->db->join("parametros p", "p.id = vp.idparametro");
    $this->db->join("estados_actividades_cont ea", "vp.id = ea.estado_id AND ap.id = ea.actividad_id", "left");
    $this->db->where("ap.id", $actividad);
    $this->db->where("ap.estado", 1);
    $this->db->where("vp.estado", 1);
    $this->db->order_by("vp.idparametro", "vp.valor");
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Validar asignar_estado */
  public function validar_asignacion_estado($estado, $actividad, $persona)
  {
    $this->db->select("IF(COUNT(ea.id) > 0, 0, 1) asignado", false);
    $this->db->from('estados_actividades_cont ea');
    $this->db->where('ea.actividad_id', $actividad);
    $this->db->where('ea.estado_id', $estado);
    $query = $this->db->get();
    return $query->row()->asignado;
  }

  /* Get where */
  public function get_where($tabla, $data)
  {
    return $this->db->get_where($tabla, $data);
  }

  public function modificar_datos_permisos($data, $tabla, $id, $col = 'id')
  {
    $this->db->where($col, $id);
    $this->db->update($tabla, $data);
    $error = $this->db->_error_message();
    return $error ? "error" : 0;
  }

  /* Buscar datos */
  public function Buscar_Info($tabla, $datos, $where)
  {
    $this->db->select($datos);
    $this->db->from($tabla);
    $this->db->where($where);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Guardar datos */
  public function Guardar_Info($tabla, $datos)
  {
    return $this->db->insert($tabla, $datos);
  }

  /* Actualizar datos */
  public function Actualizar_Info($tabla, $datos, $id)
  {
    $this->db->set($datos);
    $this->db->where('id', $id);
    return $this->db->update($tabla);
  }

  /* Listar tipos de contrato */
  public function listar_tipo_contratos($idparametro)
  {
    $this->db->select('vp.id id, vp.id_aux idaux, vp.valor tipo_contrato');
    $this->db->from('valor_parametro vp');
    $this->db->where('vp.idparametro', $idparametro);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Guardar archivos dropzone */
  public function guardar_archivo_contra($id_contrato, $nombre_real, $nombre_guardado)
  {
    $this->db->insert("contrataciones_adjuntos", array(
      "id_solicitud" => $id_contrato,
      "nombre_real" => $nombre_real,
      "nombre_guardado" => $nombre_guardado,
      "id_usuario_registra" => $_SESSION['persona'],
    ));
    $error = $this->db->_error_message();
    if ($error) {
      return "error";
    }
    return 1;
  }

  /* Buscar Contrato */
  public function buscar_contrato($valor)
  {
    $admin = false;
    $persona = $_SESSION['persona'];

    if ($_SESSION['perfil'] == "Per_Admin" || $_SESSION['perfil'] == "Admin_Cont") {
      $admin = true;
    }
    $this->db->select(
      "c.id id,
      c.id_usuario_registra soli,
      c.contrato_estado estado_cont,
      c.num_contrato num_con,
      c.adj_contrato,
      c.objetivo,
      c.valor,
      c.plazo,
      c.fecha_registra fecha_sus,
      c.fecha_inicio fecha_ini,
      c.fecha_terminacion fecha_ter,
      c.cod_sap codSAP,
      c.firma_contratante,
      c.firma_contratista,
      c.id_usuario_registra,
      c.modelo_contrato,
      CONCAT(p.nombre, ' ', p.apellido, ' ',p.segundo_apellido) solicitante,
      p.correo correo_inst,
      vp.valor estado_solicitud,
      vtr.valor contrato,
      vtro.valor nombre_tante,
      vptro.valor nombre_tista,
      vpttro.valorz tipo_contrato,
      vvtro.valor type_person,
      garantia.valor garantia,
      garantia.id_aux garantia_id,
      vptro.valory tista_cedula_nit,
      mc.valor modelo_contrato_valor", false
    );
    $this->db->from('contrataciones c');
    $this->db->where('c.estado', 1);
    $this->db->join('personas p', 'c.id_usuario_registra=p.id');
    $this->db->join('valor_parametro vp', 'c.contrato_estado=vp.id_aux');
    $this->db->join('valor_parametro vtr', 'c.num_contrato_macro=vtr.id', "left");
    $this->db->join('valor_parametro vtro', 'c.contratante=vtro.id', "left");
    $this->db->join('valor_parametro vptro', 'c.contratista=vptro.id', "left");
    $this->db->join('valor_parametro vpttro', 'c.cod_sap=vpttro.id', "left");
    $this->db->join('valor_parametro vvtro', 'c.id_tipo_persona=vvtro.id', "left");
    $this->db->join('valor_parametro garantia', 'c.id_garantia=garantia.id', "left");
    $this->db->join('valor_parametro mc', 'c.modelo_contrato=mc.id_aux', "left");   
    $this->db->where('c.contrato_estado', 'Cont_Ok_E');
    $this->db->where('c.modelo_contrato', 'tipo_contrato');
    $this->db->where("(c.id = '" . $valor . "' OR p.nombre LIKE '%" . $valor . "%' OR p.apellido LIKE '%" . $valor . "%' OR vptro.valor LIKE '%" . $valor . "%')", NULL, FALSE);
    if (!$admin) {
      $this->db->join('actividad_persona_cont ap', 'c.tipo_contrato = ap.actividad_id');
      $this->db->where('c.id_usuario_registra = ap.persona_id');
      $this->db->or_like("c.id_usuario_registra", $persona);
    }    
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Insertar consideracion o clausula */
  public function guardar_prorroga($datosArray)
  {
    $datosArray['id_usuario_registra'] = $_SESSION['persona'];
    $datosArray['estado'] = 1;
    $datosArray['contrato_estado'] = 'Cont_Ok_E';
    $this->db->insert('contrataciones', $datosArray);
    $error = $this->db->_error_message();
    if ($error) {
      return "error";
    }
    return 1;
  }

  //Contratos del contratista
  public function Listar_Contratos_Tistas($dato) //1233 tante 1234 tista
  {
    $this->db->select(
      "c.id id,
      c.id_usuario_registra soli,
      c.contrato_estado estado_cont,
      c.num_contrato num_con,
      c.objetivo,
      c.valor,
      c.plazo,
      c.fecha_registra fecha_sus,
      c.fecha_inicio fecha_ini,
      c.fecha_terminacion fecha_ter,
      c.cod_sap codSAP,
      c.firma_contratante,
      c.firma_contratista,
      CONCAT(p.nombre, ' ', p.apellido, ' ',p.segundo_apellido) solicitante,
      p.correo correo_inst,
      vp.valor estado_solicitud,
      vtr.valor contrato,
      vtro.valor nombre_tante,
      vptro.valor nombre_tista,
      vpttro.valorz tipo_contrato,
      vvtro.valor type_person,
      garantia.valor garantia,
      vptro.valory tista_cedula_nit",
      FALSE
    );

    $this->db->from('contrataciones c');
    $this->db->where('c.estado', 1);
    $this->db->join('personas p', 'c.id_usuario_registra=p.id');
    $this->db->join('valor_parametro vp', 'c.contrato_estado=vp.id_aux', "left");
    $this->db->join('valor_parametro vtr', 'c.num_contrato_macro=vtr.id', "left");
    $this->db->join('valor_parametro vtro', 'c.contratante=vtro.id', "left");
    $this->db->join('valor_parametro vptro', 'c.contratista=vptro.id', "left");
    $this->db->join('valor_parametro vpttro', 'c.cod_sap=vpttro.id', "left");
    $this->db->join('valor_parametro vvtro', 'c.id_tipo_persona=vvtro.id', "left");
    $this->db->join('valor_parametro garantia', 'c.id_garantia=garantia.id', "left");
    $this->db->join('valor_parametro mc', 'c.modelo_contrato=mc.id_aux', "left");   
    $this->db->where('c.modelo_contrato', 'tipo_contrato');
    $this->db->where('c.contratista', $dato);
    $this->db->or_where('c.id', $dato);
    $this->db->order_by('fecha_sus', 'DESC');
    $query = $this->db->get();
    //echo $this->db->last_query();
    return $query->result_array();
  }

  public function Listar_Cronogramas($id_soli_comp = "", $id_crono = "")
  {
    $this->db->select('cc.*, vp.valor as estado');
    $this->db->from('compras_cronograma cc');
    $this->db->join("valor_parametro vp", "cc.estado_cronograma = vp.id_aux", 'left');
    $this->db->where("vp.estado", 1);
    if (!empty($id_soli_comp)) {
      $this->db->where('id_solicitud', $id_soli_comp);
    } else {
      $this->db->where('cc.id', $id_crono);
    }
    $query = $this->db->get();
    //echo $this->db->last_query();
    return $query->result_array();
  }

  /* listar adjuntos cronograma */
  public function listar_adjuntos_cronograma($idsol)
  {
    $this->db->select("nombre_real, nombre_guardado, fecha_registro");
    $this->db->from("archivos_adj_compras");
    $this->db->where("id_cronograma", $idsol);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Esta funcion pretende traer un unico idparametro a partir de varias coincidencias, sea por id_aux, etc. */
	public function find_idParametro($codigo)
	{
		$this->db->select("vp.id, vp.id_aux idaux, vp.valor dato, vp.valorx vx, vp.idparametro idpa, vp.valory vy, vp.valorz vz, vp.valora va, vp.valorb vb");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.id", $codigo);
		$this->db->or_where("vp.id_aux", $codigo);
		$this->db->or_where("vp.valory", $codigo);
		$this->db->or_where("vp.valorz", $codigo);
		$this->db->or_where("vp.valora", $codigo);
		$this->db->or_where("vp.valorb", $codigo);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
    $res = $query->row();

    if(empty($res)){
      $this->db->select("*");
      $this->db->from("parametros p");
      $this->db->where("p.nombre", $codigo);
      $this->db->where("p.estado", 1);
      $query = $this->db->get();
      $res = $query->row();
    }
    
		return $res;
	}

  /* Buscar Contratista */
  public function verificar_Contratista($valor, $idparametro)
  {
    $this->db->select('vp.id, vp.valory identy, vp.valorz correo');
    $this->db->from("valor_parametro vp");
    $this->db->where("(vp.valory = '$valor' OR vp.valorz = '$valor')", NULL, FALSE);
    $this->db->where_in('vp.idparametro', [37, $idparametro]);
    $this->db->where("vp.estado", 1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function obtener_permisos_actividades($estado_contrato = "", $tipo_contrato = "", $notificacion = "", $persona = "")
  {
    $this->db->select("ap.actividad_id actividad, p.correo, ec.id_aux estado, ea.notificacion, CONCAT(p.nombre, ' ', p.apellido, ' ',p.segundo_apellido) persona", false);
    $this->db->from('actividad_persona_cont ap');
    $this->db->join('personas p', 'p.id = ap.persona_id AND p.estado = 1', 'left');
    $this->db->join('estados_actividades_cont ea', 'ea.actividad_id = ap.id AND ea.estado = 1', 'left');
    $this->db->join('valor_parametro ec', 'ec.id = ea.estado_id AND ec.estado = 1', 'left');
    $this->db->where('ap.estado', 1);
    if($estado_contrato != "") $this->db->where('ec.id_aux', $estado_contrato);
    if($notificacion != "") $this->db->where('ea.notificacion', $notificacion);
    if($tipo_contrato != "") $this->db->where('ap.actividad_id', $tipo_contrato);
    if($persona != "") $this->db->where('p.id', $persona);
    $query = $this->db->get();
    //echo $this->db->last_query();
    return $query->result_array();
  } 

  /* public function xx()
  {
    $this->db->select('');
    $this->db->from('');
    $this->db->where('');
    $query = $this->db->get();
    return $query->row();
  } */
}
