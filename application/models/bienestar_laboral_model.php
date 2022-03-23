<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH')) exit('No direct script access allowed');

class bienestar_laboral_model extends CI_Model {
    public function obtener_valores_parametro($parametro){
        $this->db->select("vp.id, vp.valor, vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro", false);
        $this->db->from("valor_parametro vp");
        $this->db->where("vp.idparametro = $parametro");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_solicitudes($estado, $tipo, $clasificacion, $fecha_i, $fecha_f, $id = ''){
        $admin = $_SESSION["perfil"] === "Per_Admin" ? true : false;
        $adm_lab = $_SESSION["perfil"] === "Per_Adm_Lab" ? true : false;
        $aux_lab = $_SESSION["perfil"] === "Per_Aux_Lab" ? true : false;
        $ase_lab = $_SESSION["perfil"] === "Per_Ase_Lab" ? true : false;
        $persona = $_SESSION["persona"];
        $filtro = $estado || $tipo || $clasificacion || $fecha_i || $fecha_f || $id ? true : false;
        $permisos = !$adm_lab && !$aux_lab && !$ase_lab? 'null permiso': 'bep.id permiso'; 
        $this->db->select("ls.*, $permisos, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname, vpl.valor lugar, vpp.valor pariente, vpu.valor ubicacion, vpt.valor TipoSolicitud, vps.valor estadoSolicitud, vpc.valor clasificacion, p.correo correo, vpcar.valor cargo", false);
        $this->db->from('laboral_solicitudes ls');
        $this->db->join('valor_parametro vpl', 'vpl.id = ls.id_lugar','left');
        $this->db->join('valor_parametro vpu', 'vpu.id = ls.id_ubicacion','left');
        $this->db->join('valor_parametro vpt', 'vpt.id_aux = ls.id_tipo','left');
        $this->db->join('valor_parametro vps', 'vps.id_aux = ls.id_estado_solicitud');
        $this->db->join('valor_parametro vpp', 'vpp.id_aux = ls.parentesco_persona', 'left');
        $this->db->join('valor_parametro vpc', 'vpc.id_aux = ls.id_clasificacion', 'left');
        $this->db->join('personas pc', 'pc.id = ls.id_usuario_registro', 'left');
        $this->db->join('valor_parametro vpcar', 'vpcar.id = pc.id_cargo', 'left');
        $this->db->join('personas p', 'p.id = ls.id_solicitante');
        if($adm_lab || $aux_lab){
            $this->db->join('laboral_personas_proceso bpp', " bpp.id_tipo_sol = ls.id_tipo AND bpp.id_auxiliar = $persona");
            $this->db->join('laboral_estados_procesos bep', 'bep.id_proceso_persona = bpp.id AND bep.id_estado = ls.id_estado_solicitud');
        }else if($ase_lab){
            $this->db->join('laboral_personas_proceso bpp', " bpp.id_tipo_sol = ls.id_clasificacion AND bpp.id_auxiliar = $persona");
            $this->db->join('laboral_estados_procesos bep', 'bep.id_proceso_persona = bpp.id AND bep.id_estado = ls.id_estado_solicitud');
        }
        if ($id) $this->db->where('ls.id', "$id");
        if ($estado) $this->db->where('ls.id_estado_solicitud', "$estado");
        if ($tipo) $this->db->where('ls.id_tipo', "$tipo");
        if ($clasificacion) $this->db->where('ls.id_clasificacion', $clasificacion);
        if ($fecha_i && $fecha_f) $this->db->where("(DATE_FORMAT(ls.fecha_registro,'%Y-%m-%d') >= DATE_FORMAT('$fecha_i','%Y-%m-%d') AND DATE_FORMAT(ls.fecha_registro,'%Y-%m-%d') <= DATE_FORMAT('$fecha_f','%Y-%m-%d'))");
        if($admin || $adm_lab || $aux_lab || $ase_lab){ 
            if (!$filtro)$this->db->where("(ls.id_estado_solicitud <> 'B_Lab_Canc' AND ls.id_estado_solicitud <> 'B_Lab_Rech' AND ls.id_estado_solicitud <> 'B_Lab_Fina')");
        }else{
            $this->db->where("(ls.id_usuario_registro = $persona)");
        }
        $this->db->where('ls.estado', 1);
        $this->db->_protect_identifiers = false;
        $this->db->order_by("FIELD (ls.id_estado_solicitud,'B_Lab_Soli','B_Lab_Prog','B_Lab_Tram','B_Lab_Fina','B_Lab_Rech','B_Lab_Canc')");
        $this->db->_protect_identifiers = true;
        $this->db->order_by("ls.fecha_registro");
        $this->db->group_by('ls.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
    INICIO FUNCIONES PARA EL MODULO DE SST
    */
    public function mostrar_notificaciones_seguridad(){
        $this->db->select("ls.*", false);
        $this->db->from('laboral_solicitudes ls');
        $this->db->where("(ls.id_estado_solicitud = 'B_Lab_Env' OR ls.id_estado_solicitud = 'B_Lab_Tram') AND ls.mtto = 1 AND ls.estado_mtto = 1"); 
        $query = $this->db->get();
        return $query->result_array();
    }

    public function consulta_ultima_solicitud_id($id_persona){
        $this->db->select("ls.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre, p.correo", false);
        $this->db->from('laboral_solicitudes ls');
        $this->db->join('personas p', 'ls.id_usuario_registro = p.id');
        $this->db->order_by("id", "desc");
        $this->db->where('id_usuario_registro', $id_persona);
        $this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }
    // 
    public function consulta_solicitud_id($id_soli){
        $this->db->select("ls.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) nombre_completo, p.identificacion cedula, p.correo correo, ls.id_estado_solicitud", false);
        $this->db->from('laboral_solicitudes ls');
        $this->db->join('personas p', 'p.id = ls.id_usuario_registro');
        $this->db->where('ls.id', $id_soli);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }

    public function listar_archivos_seguridad($id_sol){
        $this->db->select("las.*, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) fullname", false);
        $this->db->from("laboral_adj_seguridad las");
        $this->db->join('personas p', 'p.id = las.id_usuario_registra');
        $this->db->where("las.id_solicitud", $id_sol);
        $this->db->where("las.estado", 1);
        $query = $this->db->get(); 
        return $query->result_array();
    }
    
    public function listar_estados($id){
        $this->db->select("le.*, vs.valor estado, CONCAT(vu.nombre, ' ', vu.apellido) AS fullname", false);
        $this->db->from('laboral_estados le');
        $this->db->join('valor_parametro vs', 'le.id_estado = vs.id_aux');
        $this->db->join('personas vu', 'le.id_usuario_registro = vu.id');
        $this->db->where('id_solicitud', $id); 
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_estados_mtto($id){
        $this->db->select("sm.fecha_registra, vs.valor estado, CONCAT(vu.nombre, ' ', vu.apellido) AS fullname, sm.observacion, sm.estado_solicitud, sm.num_solicitud", false);
        $this->db->from('solicitudes_mantenimiento sm');
        $this->db->join('valor_parametro vs', 'sm.estado_solicitud = vs.id_aux');
        $this->db->join('personas vu', 'sm.solicitante_id = vu.id');
        $this->db->where('id_seguridad', $id); 
        $query = $this->db->get();
        return $query->result_array();
    }

    public function buscar_persona($where){
        $this->db->select("p.identificacion, p.id, CONCAT(p.nombre,' ',p.segundo_nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.correo", false);
        $this->db->from('personas p');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function buscar_razones($idparametro){
        $this->db->select("vp.id_aux id_aux, vp.valor causa", false);
        $this->db->from('valor_parametro vp');
        $this->db->where('vp.idparametro', $idparametro);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_razones($id){
        $this->db->select("lr.razon razon", false);
        $this->db->from('laboral_fina_razones lr');
        $this->db->where('lr.id_solicitud', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_personas($id){
        $this->db->select("CONCAT(p.nombre,' ',p.segundo_nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
        $this->db->from('laboral_personas lp');
        $this->db->join('personas p', 'lp.id_persona = p.id');
        $this->db->where('lp.id_solicitud', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function obtener_empleados($where){
        $this->db->select("p.*,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo",false);
        $this->db->from('personas p');
        $this->db->where($where);
        $this->db->where('p.estado',1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_procesos_lab($id){
        if(!$id){
            return array();
        }else{
            $this->db->select("vp.*, vp.valor nombre, vp.valorx descripcion, bpp.id tipo",false);
            $this->db->from("valor_parametro vp");
            $this->db->join("laboral_personas_proceso bpp","bpp.id_tipo_sol = vp.id_aux AND bpp.id_auxiliar = $id",'left',false);
            $this->db->where("vp.idparametro", 146);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function listar_asesorias_lab($id){
        if(!$id){
            return array();
        }else{
            $this->db->select("vp.*, vp.valor nombre, vp.valorx descripcion, bpp.id tipo",false);
            $this->db->from("valor_parametro vp");
            $this->db->join("laboral_personas_proceso bpp","bpp.id_tipo_sol = vp.id_aux AND bpp.id_auxiliar = $id",'left',false);
            $this->db->where("vp.idparametro", 144);
            $this->db->like('vp.id_aux', 'Ase_Tip_', 'after');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function listar_estados_lab($id){
        $this->db->select("vp.*,vp.valor nombre, bep.id tipo",false);
        $this->db->from("valor_parametro vp");
        $this->db->join("laboral_estados_procesos bep","bep.id_estado = vp.id_aux AND bep.id_proceso_persona = $id",'left',false);
        $this->db->where("vp.idparametro", 145);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function obtener_asigacion_aux($id, $id_tipo_sol, $estado){
        $this->db->select("*",false);
        $this->db->from("laboral_personas_proceso bpp");
        if($estado) $this->db->join("laboral_estados_procesos bep","bpp.id = bep.id_proceso_persona AND bep.id_estado = '$estado'" );
        if($id_tipo_sol)$this->db->where("bpp.id_tipo_sol",$id_tipo_sol);
        $this->db->where("bpp.id_auxiliar",$id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function eliminar_registro($id,$tabla){
        $this->db->where('id',$id);
        $this->db->delete($tabla);
        $error = $this->db->_error_message();
        if ($error) {
            return "error";
        }
        return 0;
    }

    public function exist($id,$tabla){ 
        $this->db->select("*");
        $this->db->from($tabla);
        $this->db->where("id",$id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function validar_permisos($id_persona, $tipo, $clasificacion, $estado){
        $this->db->select("bpp.*, bep.id_proceso_persona, bep.id_estado, bep.estado", false);
        $this->db->from('laboral_personas_proceso bpp');
        $this->db->join('laboral_estados_procesos bep', 'bpp.id = bep.id_proceso_persona');
        $this->db->where("bep.id_estado", $estado);
        $this->db->where("bpp.id_auxiliar", $id_persona);
        $this->db->where("(bpp.id_tipo_sol = '$tipo' OR bpp.id_tipo_sol = '$clasificacion')");
        $this->db->where("bpp.estado", 1);
        $this->db->where("bep.estado", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function obtener_personas_permisos($tipo_sol, $estado){
        $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo, p.correo, bpp.id_auxiliar id_persona, bpp.id_tipo_sol, bep.id_estado", false);
        $this->db->from('laboral_personas_proceso bpp');
        $this->db->join('laboral_estados_procesos bep', 'bpp.id = bep.id_proceso_persona');
        $this->db->join('personas p', 'bpp.id_auxiliar = p.id');
        $this->db->where("bep.id_estado", $estado);
        $this->db->where("bpp.id_tipo_sol = '$tipo_sol'");
        $this->db->where("bpp.estado", 1);
        $this->db->where("bep.estado", 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function obtener_parametros_generales($id_aux) {
        $this->db->select('vp.*');
        $this->db->from('valor_parametro vp');
        $this->db->where('id_aux', $id_aux);
        $this->db->where('idparametro', 20);
        $this->db->where('estado', 1);
        $query = $this->db->get();
        return $query->row();
    }

    public function buscar_solicitantes($tabla, $dato){
		$this->db->select("t.*,identificacion,CONCAT(nombre,' ',apellido,' ',segundo_apellido) as nombre_completo, DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(fecha_nacimiento)), '%Y')+0 AS edad, vp.valor as dependencia", false);
		$this->db->from("$tabla t");
	    $this->db->join('cargos_departamentos c', 't.id_cargo=c.id','left');
		$this->db->join('valor_parametro vp', 'c.id_departamento=vp.id','left');
		$this->db->where("(CONCAT(nombre,' ',apellido,' ',segundo_apellido) LIKE '%" . $dato . "%' OR identificacion LIKE '%" . $dato . "%') AND t.estado=1");
		$this->db->order_by('nombre,apellido,segundo_apellido');
		$query = $this->db->get();
		return $query->result_array();
	}
    /*
    FIN FUNCIONES PARA EL MODULO DE SST
    */
}
