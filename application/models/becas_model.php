<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH')) exit('No direct script access allowed');

class becas_model extends CI_Model {
    

    public function obtener_valores_parametro($parametro){
        $this->db->select("vp.id, vp.valor, vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro", false);
        $this->db->from("valor_parametro vp");
        $this->db->where("vp.idparametro = $parametro");
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function listar_solicitudes_becas($estado, $admitido, $nivel_formacion, $fecha_i, $fecha_f, $departamento, $programa, $vinculacion,  $tipo, $fil_persona, $id){

        $admin = $_SESSION["perfil"] === "Per_Admin" ? true : false;
        $adm_bec = $_SESSION["perfil"] === "Per_Adm_Bec" ? true : false;
        $persona = $_SESSION["persona"];
        $filtro = $estado || $admitido || $nivel_formacion || $fecha_i || $fecha_f || $departamento || $programa || $vinculacion || $tipo || $fil_persona || $id ? true : false;

        $this->db->select("bs.*, bpe.id tieneP, bpe1.id tieneE, vn.valor nivel_formacion, vs.valor semestre, ve.valor estado_soli, vy.valor year, vcom.valor tipo_comision, vbeca.valor beca, vt.valor tipo_solicitud, CONCAT(vu.nombre, ' ', vu.apellido, ' ', vu.segundo_apellido) AS fullname, vu.correo, ((SELECT COUNT(*) FROM becas_estado WHERE id_usuario_registra = $persona AND id_solicitud = bs.id AND estado = 1 AND (id_estado = 'Bec_Vis_Buen'))) AS persona_d", false);
        $this->db->from("becas_solicitudes bs"); 
        $this->db->join('becas_permisos_solicitudes bps', "bps.id_tipo = bs.id_tipo  And bps.id_persona = $persona", 'left');
        $this->db->join('becas_permisos_estados bpe', 'bpe.id_permiso_solicitud = bps.id and bpe.id_estado = bs.id_programa_persona','left');
        $this->db->join('becas_permisos_estados bpe1', 'bpe1.id_permiso_solicitud = bps.id and bpe1.id_estado = bs.id_estado_solicitud','left');
        $this->db->join('valor_parametro vn', 'bs.id_nivel_formacion = vn.id', 'left');
        $this->db->join('valor_parametro vs', 'bs.id_semestre = vs.id', 'left');
        $this->db->join('valor_parametro ve', 'bs.id_estado_solicitud = ve.id_aux', 'left');
        $this->db->join('valor_parametro vy', 'bs.id_duracion = vy.id', 'left');
        $this->db->join('valor_parametro vcom', 'bs.id_comision = vcom.id_aux', 'left');
        $this->db->join('valor_parametro vbeca', 'bs.id_beca = vbeca.id_aux', 'left');
        $this->db->join('valor_parametro vt', 'bs.id_tipo = vt.id_aux', 'left');
        $this->db->join('personas vu', 'bs.id_usuario_registro = vu.id', 'left');

        //FILTROS
        if ($fil_persona) $this->db->where("(vu.nombre like '%$fil_persona%' || vu.apellido like '%$fil_persona%' || vu.segundo_apellido like '%$fil_persona%' || vu.usuario like '%$fil_persona%' || vu.identificacion like '%$fil_persona%' || CONCAT(vu.nombre, ' ', vu.apellido) like '%$fil_persona%')");
        if ($id) $this->db->where('bs.id', $id);
        if ($estado) $this->db->where('bs.id_estado_solicitud', "$estado");
        if ($admitido) $this->db->where('bs.admitido_al_programa', "$admitido");
        if ($nivel_formacion) $this->db->where('bs.id_nivel_formacion', $nivel_formacion);
        if ($tipo) $this->db->where('bs.id_tipo', $tipo);
        if ($departamento) $this->db->where('bs.id_departamento_persona', $departamento);
        if ($programa) $this->db->where('bs.id_programa_persona', $programa);
        if ($vinculacion) $this->db->where('bs.id_vinculacion_persona', $vinculacion);
        if ($fecha_i && $fecha_f) $this->db->where("(DATE_FORMAT(bs.fecha_registro,'%Y-%m-%d') >= DATE_FORMAT('$fecha_i','%Y-%m-%d') AND DATE_FORMAT(bs.fecha_registro,'%Y-%m-%d') <= DATE_FORMAT('$fecha_f','%Y-%m-%d'))");
        
        if(!$admin && !$adm_bec){ 
            if (!$id) $this->db->where("((bs.id_usuario_registro = $persona) OR (bps.id IS NOT NULL AND bpe.id IS NOT NULL AND bpe1.id IS NOT NULL))");
        }
        else{
            if (!$filtro)$this->db->where("( bs.id_estado_solicitud <> 'Bec_Canc' AND bs.id_estado_solicitud <> 'Bec_Rech' AND bs.id_estado_solicitud <> 'Bec_Fina' AND bs.id_estado_solicitud <> 'Bec_Apro')");
        }

        $this->db->where('bs.estado', 1);
        $this->db->_protect_identifiers = false;
        // $this->db->order_by("FIELD (bs.id_estado_solicitud, 'Bec_Form', 'Bec_Corr', 'Bec_Envi', 'Bec_Revi', 'Bec_Gest', 'Bec_Gest_Inve', 'Bec_Tram', 'Bec_Acep', 'Bec_Apro', 'Bec_Fina','Bec_Rech', 'Bec_Canc')");
        $this->db->_protect_identifiers = true;
        // $this->db->order_by("bs.id_tipo");
        $this->db->order_by("bs.fecha_registro", "desc");
        $this->db->group_by('bs.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function info_solicitud_renovacion($id){
        $this->db->select("bsr.id, bs.institucion, bs.pais_institucion, bs.ciudad_institucion, bs.programa, bs.ranking, bs.tipo_duracion_programa, bs.admitido_al_programa, bs.linea_investigacion, bs.pin, bs.fecha_inicio, bs.fecha_termina, bsr.id_estado_solicitud, bs.id_semestre, bs.id_duracion, bs.id_nivel_formacion, bsr.id_usuario_registro, bsr.id_usuario_elimino, bs.id_comision, bs.id_beca, bsr.id_tipo, bsr.id_departamento_persona, bsr.id_programa_persona, bsr.id_vinculacion_persona, bsr.salario_persona, bsr.fecha_registro, bsr.fecha_elimino, bsr.estado, bsr.id_renovacion, bsr.observaciones, vn.valor nivel_formacion,vs.valor semestre, ve.valor estado_soli, vy.valor year, vcom.valor tipo_comision,  vbeca.valor beca, vt.valor tipo_solicitud, CONCAT(vu.nombre, ' ', vu.apellido, ' ', vu.segundo_apellido) AS fullname, vu.correo", false);
        $this->db->from('becas_solicitudes bs');
        $this->db->join('becas_solicitudes bsr', 'bsr.id_renovacion = bs.id');
        $this->db->join('valor_parametro vn', 'bs.id_nivel_formacion = vn.id');
        $this->db->join('valor_parametro vs', 'bs.id_semestre = vs.id');
        $this->db->join('valor_parametro ve', 'bsr.id_estado_solicitud = ve.id_aux');
        $this->db->join('valor_parametro vy', 'bs.id_duracion = vy.id');
        $this->db->join('valor_parametro vcom', 'bs.id_comision = vcom.id_aux');
        $this->db->join('valor_parametro vbeca', 'bs.id_beca = vbeca.id_aux', 'left');
        $this->db->join('valor_parametro vt', 'bs.id_tipo = vt.id_aux');
        $this->db->join('personas vu', 'bsr.id_usuario_registro = vu.id');
        $this->db->where('bsr.id', $id);
        $this->db->where('bsr.estado', 1);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function listar_conceptos($id_solicitud){
        $this->db->select("cp.*, vc.valor concepto", false);
        $this->db->from('becas_concepto cp');
        $this->db->join('valor_parametro vc', 'cp.id_concepto = vc.id_aux', 'left');
        $this->db->where('cp.id_solicitud', $id_solicitud);
        $this->db->where('cp.estado', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_herramientas($id_solicitud){
        $this->db->select("bmt.*", false);
        $this->db->from("becas_manejo_tech bmt");
        $this->db->where("id_solicitud", $id_solicitud);
        $this->db->where('bmt.estado', 1); 
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_produccion_intelectual($id_solicitud){
        $this->db->select("bpi.*, vn.valor nombreProducto", false);
        $this->db->from("becas_prod_intelectual bpi");
        $this->db->join('valor_parametro vn', 'bpi.id_producto = vn.id');
        $this->db->where("id_solicitud", $id_solicitud);
        $this->db->where("bpi.estado", 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_experiencia_sector($id_solicitud){
        $this->db->select("bes.*", false);
        $this->db->from("becas_sector_productivo bes");
        $this->db->where("id_solicitud", $id_solicitud);
        $this->db->where("bes.estado", 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_plan_accion($id_solicitud){
        $this->db->select("bpa.*", false);
        $this->db->from("becas_plan_accion bpa");
        $this->db->where("id_solicitud", $id_solicitud);
        $this->db->where("bpa.estado", 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_actividades($id_plan){
        $this->db->select('bpag.*', false);
        $this->db->from('becas_plan_accion_gestion bpag');
        $this->db->where('id_plan', $id_plan);
        $this->db->where('bpag.estado', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_entregables($id_solicitud){
        $this->db->select("bce.*", false);
        $this->db->from("becas_compromiso_entregable bce");
        $this->db->where("id_solicitud", $id_solicitud);
        $this->db->where("bce.estado", 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_compromisos($id_entrega){
        $this->db->select('bc.*', false);
        $this->db->from('becas_compromisos bc');
        $this->db->where('id_entregable', $id_entrega);
        $this->db->where('bc.estado', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_datos_solicitante_p($id_solicitante){
        $this->db->select("p.*, CONCAT(p.nombre, ' ', p.segundo_nombre, ' ', p.apellido, ' ', p.segundo_apellido)AS fullname, p.fecha_nacimiento edad, cc.valor contrato, cv.valor vinculacion, cd.valor departamento,cg.valor grupo_investigacion, cll.valor linea, cp.fecha_inicio, p.correo correo_inst, p.telefono telefono, cprog.valor programa, cp.cvlac cvlac, (SELECT SUM(cantidad) FROM `csep_profesor_horas` as cp INNER JOIN valor_parametro as vp on vp.id = cp.id_hora and vp.id_aux = 'Hor_Inv' WHERE `id_profesor` = p.id) as cantidad", false);
        $this->db->from('personas p');
        $this->db->join('csep_profesores cp', 'p.id = cp.id_persona AND cp.estado_registro = 1', 'left');
        $this->db->join('valor_parametro cc', 'cc.id = cp.id_contrato', 'left');
        $this->db->join('valor_parametro cprog', 'cprog.id = cp.id_programa', 'left');
        $this->db->join('valor_parametro cd', 'cd.id = cp.id_departamento', 'left');
        $this->db->join('valor_parametro cv', 'cv.id = cp.id_dedicacion', 'left');
        $this->db->join('valor_parametro cg', 'cg.id = cp.id_grupo', 'left');
        $this->db->join('csep_profesores_lineas cl', 'cl.id_profesor = cp.id', 'left');
        $this->db->join('valor_parametro cll', 'cll.id = cl.id_linea', 'left');
        $this->db->where('p.id', $id_solicitante);
        $this->db->order_by("cp.id", "desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function informacion_profesor($id_persona){
        $this->db->select('cp.id, cp.id_persona, cp.id_programa,cp.id_departamento,cp.id_dedicacion, p.sueldo salario', false);
        $this->db->from('csep_profesores cp');
        $this->db->join('personas p', 'p.id = cp.id_persona');
        $this->db->where('cp.id_persona', $id_persona);
        $this->db->where('cp.estado_registro', 1);
        $this->db->order_by("cp.id", "desc");
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function formacion_solicitante($id_solicitante){
        $this->db->select('cff.valor nivel, cf.nombre formacion', false);
        $this->db->from('csep_profesores cp');
        $this->db->join('csep_profesor_formacion cf', 'cf.id_profesor = cp.id', 'left');
        $this->db->join('valor_parametro cff', 'cff.id = cf.id_formacion', 'left');
        $this->db->where('cp.id_persona', $id_solicitante);
        $this->db->where('cp.estado_registro', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_estados($id){
        $this->db->select("be.*, vs.valor estado, CONCAT(vu.nombre, ' ', vu.apellido) AS fullname", false);
        $this->db->from('becas_estado be');
        $this->db->join('valor_parametro vs', 'be.id_estado = vs.id_aux');
        $this->db->join('personas vu', 'be.id_usuario_registra = vu.id');
        $this->db->where('id_solicitud', $id); 
        $query = $this->db->get();
        return $query->result_array();
    }

    public function estado($id_solicitud){
        $this->db->select("be.id_estado, (SELECT bs.id_tipo FROM becas_solicitudes bs WHERE id = $id_solicitud) id_tipo ", false);
        $this->db->from('becas_estado be');
        $this->db->where('id_solicitud', $id_solicitud); 
        $this->db->order_by("id", "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }


    public function traer_ultima_solicitud($person){
        $this->db->select("bs.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS fullname, p.correo", false);
        $this->db->from('becas_solicitudes bs');
        $this->db->join('personas p', 'bs.id_usuario_registro = p.id');
        $this->db->where('id_usuario_registro', $person);
        $this->db->order_by("id", "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function traer_ultimo_plan_accion($person){
        $this->db->select("bpa.*", false);
        $this->db->from('becas_plan_accion bpa');
        $this->db->join('personas p', 'bpa.id_usuario_registra = p.id');
        $this->db->order_by("id", "desc");
        $this->db->where('id_usuario_registra', $person);
        $this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function traer_ultima_entrega($person){
        $this->db->select("bce.*", false);
        $this->db->from('becas_compromiso_entregable bce');
        $this->db->join('personas p', 'bce.id_usuario_registro = p.id');
        $this->db->order_by("id", "desc");
        $this->db->where('id_usuario_registro', $person);
        $this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function consulta_solicitud_id($id_solicitud)
    {
        $this->db->select("bs.* ", false);
        $this->db->from('becas_solicitudes bs');
        $this->db->where('bs.id', $id_solicitud);
        $this->db->where('bs.estado', 1);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function filtrar_solicitud($fecha_i, $fecha_f, $admitido, $nivel_formacion, $estado){
        $this->db->select("sol.*", false);
        $this->db->from("becas_solicitudes sol");
        $this->db->where("admitido_al_programa = '$admitido' AND id_nivel_formacion = $nivel_formacion AND estado = '$estado'");
        $this->db->where("(DATE_FORMAT(sol.fecha_inicio,'%Y-%m-%d') >= DATE_FORMAT('$fecha_i','%Y-%m-%d') AND DATE_FORMAT(sol.fecha_termina,'%Y-%m-%d') <= DATE_FORMAT('$fecha_f','%Y-%m-%d'))");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function traer_tipo_vinculacion($id_solicitud){
        $this->db->select("vp.id_aux vinculacion, vp.valory valor", false);
        $this->db->from("becas_solicitudes bs");
        $this->db->join("valor_parametro vp", 'bs.id_vinculacion_persona = vp.id');
        $this->db->where("bs.id", $id_solicitud);
        $this->db->where("bs.estado", 1);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function traer_smlv(){
        $this->db->select("vp.valor smlv", false);
        $this->db->from("valor_parametro vp");
        $this->db->where("vp.id_aux", 'Sal_Min_Leg_Vig');
        $this->db->where("vp.estado", 1);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function cargar_estados($id_parametro){
        $this->db->select('vp.*', false);
        $this->db->from('valor_parametro vp');
        $this->db->where("vp.idparametro = $id_parametro AND vp.valory = 1 AND vp.estado = 1");
        $this->db->order_by("vp.valor", "ASC");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function validar_cantidad_de_solicitud($id){
        $this->db->select("bs.id, bs.id_tipo, COUNT(*) cantidad", false); 
        $this->db->from('becas_solicitudes bs');
        $this->db->where("(bs.id_tipo = 'Soli_Tip_Ini' or bs.id_tipo = 'Soli_Tip_Ren')"); 
        $this->db->where("bs.id_usuario_registro", $id);
        $this->db->where("bs.id_estado_solicitud <> 'Bec_Canc' AND bs.id_estado_solicitud <> 'Bec_Rech' AND bs.id_estado_solicitud <> 'Bec_Fina' AND IF(bs.id_tipo = 'Soli_Tip_Ren', bs.id_estado_solicitud <> 'Bec_Apro', 1) ");
        $this->db->where("bs.estado", 1 ); 
        $this->db->group_by("bs.id_tipo");
        $query = $this->db->get();
        return $query->result();
    }

    public function listar_solicitud_a_renovar($id_persona){
        $this->db->select('bs.*', false);
        $this->db->from('becas_solicitudes bs');
        $this->db->where("bs.id_usuario_registro", $id_persona);    
        $this->db->where("bs.id_estado_solicitud != 'Bec_Form' AND bs.id_estado_solicitud != 'Bec_Corr' AND bs.id_estado_solicitud != 'Bec_Canc' AND bs.id_estado_solicitud != 'Bec_Rech' AND bs.id_estado_solicitud != 'Bec_Fina'");    
        $this->db->where("bs.id_tipo", "Soli_Tip_Ini" );    
        $this->db->where("bs.estado", 1 );    
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function listar_solicitudes_notificaciones($id_persona){
        $admin = $_SESSION["perfil"] === "Per_Admin" || $_SESSION["perfil"] === "Per_Adm_Bec" ? true : false;

        $this->db->select("bs.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido)AS fullname", false);
        $this->db->from("becas_solicitudes bs");
        $this->db->join("personas p", "p.id = bs.id_usuario_registro");
        if($admin){
            $this->db->where("((SELECT COUNT(*) FROM becas_soportes_fin bsf WHERE bsf.id_solicitud = bs.id AND bsf.estado = 1) > 0 OR bs.id_usuario_registro = $id_persona)");
        }else{
            $this->db->where("bs.id_usuario_registro", $id_persona);
        }
        $this->db->where("(SELECT COUNT(*) FROM becas_solicitudes bs2 WHERE bs2.id_renovacion = bs.id AND bs2.id_tipo = 'Soli_Tip_Ren' AND bs2.estado = 1  AND (bs2.id_estado_solicitud != 'Bec_Fina' AND bs2.id_estado_solicitud != 'Bec_Rech' AND bs2.id_estado_solicitud != 'Bec_Apro')) = 0 ");
        $this->db->where("bs.id_tipo", 'Soli_Tip_Ini'); 
        $this->db->where("bs.id_estado_solicitud", 'Bec_Apro');
        $this->db->where("bs.estado", 1);
        $query = $this->db->get(); 
        return $query->result_array();
    }
    
    public function traer_ids_renovaciones($id_sol_ini){
        $this->db->select("bs.id", false);
        $this->db->from("becas_solicitudes bs");
        $this->db->where("bs.id_renovacion", $id_sol_ini);
        $this->db->where("bs.id_estado_solicitud", 'Bec_Apro');
        $this->db->where("bs.estado", 1);
        $query = $this->db->get(); 
        return $query->result_array();
    }

    public function validar_conceptos($id_solicitud, $id_concepto){
        $this->db->select('bc.*', false);
        $this->db->from('becas_concepto bc');
        $this->db->where("bc.id_solicitud", $id_solicitud);
        $this->db->where("bc.id_concepto", $id_concepto);
        $this->db->where("estado", 1); 
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function listar_archivos_adjuntos($id_sol, $tabla = 'becas_archivos_adj'){
        $this->db->select("las.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname", false);
        $this->db->from("$tabla las");
        $this->db->join('personas p', 'p.id = las.id_usuario_registro');
        $this->db->where("las.id_solicitud", $id_sol);
        $this->db->where("las.estado", 1);
        $query = $this->db->get(); 
        return $query->result_array();
    }

    public function validar_permisos_administrar($id_persona, $estado_soli, $tipo_soli){
        $this->db->select("bps.*, bpe.id_permiso_solicitud, bpe.id_estado, bpe.estado", false);
        $this->db->from('becas_permisos_solicitudes bps');
        $this->db->join('becas_permisos_estados bpe', 'bps.id = bpe.id_permiso_solicitud');
        $this->db->where("bpe.id_estado", $estado_soli);
        $this->db->where("bps.id_persona", $id_persona);
        $this->db->where("bps.id_tipo", $tipo_soli);
        $this->db->where("bps.estado", 1);
        $this->db->where("bpe.estado", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function revisiones($id_solicitud){
        $this->db->select('COUNT(*) n_revisiones', false);
        $this->db->from('becas_estado be');
        $this->db->where("be.id_solicitud", $id_solicitud);
        $this->db->where("(be.id_estado = 'Bec_Vis_Buen')");
        $this->db->where("be.estado", 1); 
        $query = $this->db->get();
        return $query->row()->n_revisiones;
    }

    public function traer_minimo_revisiones(){
        $this->db->select("vp.valor min_revi", false);
        $this->db->from("valor_parametro vp");
        $this->db->where("vp.id_aux", 'Bec_Min_Revi');
        $this->db->where("vp.estado", 1);
        $query = $this->db->get();
        return $query->row()->min_revi;
    }

    public function listar_personas($persona){
		$this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) AS fullname", false);
		$this->db->from('personas p');
		$this->db->where("p.nombre like '%$persona%' || p.apellido like '%$persona%' || p.segundo_apellido like '%$persona%' || p.usuario like '%$persona%' || p.identificacion like '%$persona%' || CONCAT(p.nombre, ' ', p.apellido) like '%$persona%'");
		$query = $this->db->get();
		return $query->result_array();
    }
    
    public function listar_tipo_solicitud($persona){
        $this->db->select("vp.id_aux as id, vp.valor as nombre, bp.id as asignado",false);
        $this->db->from("valor_parametro vp");
        $this->db->join("becas_permisos_solicitudes bp","vp.id_aux = bp.id_tipo AND bp.id_persona = $persona",'left');
        $this->db->join("parametros p","p.id = vp.idparametro");
        $this->db->where("vp.idparametro", 201);
        $this->db->where("vp.estado", 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_estados_permisos($id_permiso){
        $query = $this->db->query("
			SELECT IF(vp.idparametro = 193, vp.id_aux, vp.id ) id_estado,  p.nombre parametro, vp.valor nombre, be.id asignado
			FROM valor_parametro vp
			INNER JOIN parametros p ON p.id = vp.idparametro
			LEFT JOIN becas_permisos_estados be ON (be.id_estado = vp.id OR be.id_estado = vp.id_aux) AND be.id_permiso_solicitud = $id_permiso
			WHERE ((vp.idparametro = 193 AND vp.valory = 1) OR vp.idparametro = 86)
			AND vp.estado = 1
			ORDER BY p.nombre desc
		");
        return $query->result_array();
	}

    public function validar_permisos_asignados($id_tipo, $persona){
		$this->db->select("IF(COUNT(id) > 0, 0, 1) asignado", false);
		$this->db->from('becas_permisos_solicitudes');
		$this->db->where('id_tipo', $id_tipo);
		$this->db->where('id_persona', $persona);
		$this->db->where('estado', 1);
		$query = $this->db->get();
		return $query->row()->asignado;
    }

    public function validar_estados_asignados($id_permiso, $id_estado){
		$this->db->select("IF(COUNT(be.id) > 0, 0, 1) asignado",false);
		$this->db->from('becas_permisos_estados be');
		$this->db->where('be.id_permiso_solicitud', $id_permiso);
        $this->db->where('be.id_estado', $id_estado);
        $this->db->where('be.estado', 1);
		$query = $this->db->get();
		return $query->row()->asignado;
    }

    public function obtener_personas_permisos($tipo_sol, $estado, $programa){
        $this->db->select("p.correo, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) fullname, bps.id_persona, bps.id_tipo ,bpe.id_estado permiso_estado, bpe1.id_estado permiso_programa", false);
        $this->db->from('becas_permisos_solicitudes bps');
        $this->db->join('becas_permisos_estados bpe', 'bpe.id_permiso_solicitud = bps.id');
        $this->db->join('becas_permisos_estados bpe1', 'bpe1.id_permiso_solicitud = bps.id');
        $this->db->join('personas p', 'bps.id_persona = p.id');
        $this->db->where("bps.id_tipo", $tipo_sol);
        $this->db->where("(bpe.id_estado = '$estado' and bpe1.id_estado = $programa)");
        $this->db->where("bps.estado", 1);
        $this->db->where("bpe.estado", 1);
        $this->db->where("bpe1.estado", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function eliminar_registro($id, $tabla, $col = 'id'){
        $this->db->where($col, $id);
        $this->db->delete($tabla);
        $error = $this->db->_error_message(); 
        return $error ? -1 : 1;
    }

    public function obtener_ultimo_estado($id){
        $this->db->select("be.*", false);
        $this->db->from("becas_estado be");
        $this->db->join('valor_parametro vp', 'vp.id_aux = be.id_estado');
        $this->db->where("be.id_solicitud", $id);
        $this->db->where("be.id_estado !=", "Bec_Corr");
        $this->db->where("vp.valory", 1);
        $this->db->order_by("id", "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row()->id_estado;
    }

    public function finalizar_solicitudes($data, $id){
		$this->db->where('id_renovacion', $id);
		$this->db->where('id_estado_solicitud', 'Bec_Apro');
		$this->db->update('becas_solicitudes', $data);
		$error = $this->db->_error_message(); 
		return $error ? -1 : 1;
	}
}
