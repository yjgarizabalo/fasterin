<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Modelo que se encarga de manejar la informacion de las tablas inventario, perifericos_recursos, responsables y detalle_inventario.
 */
class inventario_model extends CI_Model {

    var $table_inventario = "inventario";
    var $table_perifericos_recursos = "perifericos_recursos";
    var $table_detalle_inventario = "detalle_inventario";
    var $select_column = "*";

    /**
     * Lista de forma general toda la informacion del inventario registrado
     * @return Void
     */
    public function guardar_datos($data, $tabla, $tipo = 1){
		$tipo == 2 ? $this->db->insert_batch($tabla, $data) : $this->db->insert($tabla,$data);
		$error = $this->db->_error_message(); 
		return $error ? 0 : ($tipo == 1 ? $this->db->insert_id() : 1);
    }

    public function make_query() {
        $this->db->select($this->select_column);
        $this->db->from($this->table_inventario);
	}
	
	public function get_where($tabla, $data){
		return $this->db->get_where($tabla, $data);
    }
    
	public function modificar_datos($data, $tabla , $id, $col = 'id'){
		$this->db->where($col, $id);
		$this->db->update($tabla, $data);
		$error = $this->db->_error_message(); 
		return $error ? "error" : 0;
	}
	
	public function Listar($tipo_modulo, $buscar, $ubicacion, $aux, $estado = '', $admin, $en_fecha, $lugar){
        $responsable = $aux == 'responsable' ? $buscar : $_SESSION['persona'];
		$this->db->select("i.nombre_activo, i.fecha_inicio_proyecto, i.fecha_fin_proyecto, cs.valor codigo_sap, vl.id lugar_id, vl.valor lugar,vu.valor ubicacion, vu.id_aux cod,u2.valor modelo,u.valor recurso,u1.valor marca,i.serial,i.descripcion,i.fecha_ingreso,i.fecha_garantia,i.estado,i.id, u3.valor estado_recurso,i.estado_recurso estado_aux,i.codigo_interno,i.valor,u.id_aux tipo, i.motivo_baja", false);
        $this->db->from('inventario i');
		if(!$admin || $aux == 'responsable') $this->db->join('inventario_responsables ir',"i.id = ir.id_inventario AND ir.id_persona = $responsable");
		$this->db->join('valor_parametro u', 'i.tipo=u.id');
        $this->db->join('valor_parametro u1', 'i.id_marca=u1.id');
        $this->db->join('valor_parametro u3', 'i.estado_recurso=u3.id_aux');
        $this->db->join('valor_parametro u2', 'i.id_modelo=u2.id');
        $this->db->join('responsables r','i.id = r.id_dispositivo AND r.estado_responsable = "ResAct"', 'left');
        $this->db->join('inventario_lugares il', 'i.id = il.id_inventario AND il.estado = "ResAct"', 'left');
        $this->db->join('valor_parametro vl', 'il.id_lugar = vl.id', 'left');
        $this->db->join('valor_parametro vu', 'il.id_ubicacion = vu.id', 'left');
        $this->db->join('valor_parametro cs', 'i.id_codigo_sap = cs.id', 'left');
        if($en_fecha == 'mantenimiento') $this->db->join('mantenimientos_laboratorios mlab', 'i.id = mlab.inventario_id');
		$this->db->where("i.tipo_modulo", $tipo_modulo);
		$this->db->where("il.estado", 'ResAct');
        if($estado) $this->db->where("i.estado_recurso", $estado);
        if($aux == 'serial') $this->db->where("i.serial", $buscar);
		if($en_fecha == 'proyectos') $this->db->where("i.fecha_fin_proyecto BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)");
        else if($en_fecha == 'garantia') $this->db->where("i.fecha_garantia BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)");
        else if($en_fecha == 'mantenimiento') $this->db->where("mlab.fecha_prox_mtto BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)");
        if($ubicacion) $this->db->where("il.id_ubicacion", $ubicacion);
        if($lugar) $this->db->where("il.id_lugar", $lugar);
        if($buscar && !$aux) $this->db->where("i.tipo", $buscar);
		$this->db->group_by("i.id");
        $query = $this->db->get();
        return $query->result_array();
	}

	public function Listar_tipo_articulos($tipo_modulo, $ubicacion, $aux, $admin, $lugar=""){
		$this->db->select("rec.id, rec.id_aux, rec.valor nombre, COUNT(rec.id) cantidad");
		$this->db->from('valor_parametro rec');
		$this->db->join("inventario i", "i.tipo = rec.id");
		if(!$admin) $this->db->join("inventario_responsables ir", "i.id = ir.id_inventario AND ir.estado = 1 AND id_persona = " . $_SESSION['persona']);
		$this->db->join('valor_parametro tr', 'i.tipo=tr.id');
		$this->db->join("permisos_parametros pp", "pp.vp_secundario_id = rec.id");
		$this->db->join("inventario_lugares il", "il.id_inventario = i.id");
		$this->db->where("pp.vp_principal", $tipo_modulo);
		$this->db->where("i.tipo_modulo", $tipo_modulo);
		$this->db->where('rec.idparametro', 6);
		$this->db->where('il.id_ubicacion', $ubicacion);
		if($lugar && $tipo_modulo === 'Inv_Aud') $this->db->where('il.id_lugar', $lugar);
		$this->db->where('il.estado', "ResAct");
		$this->db->where('rec.estado', '1');
		if($aux != 'Bod_Tec') $this->db->where("((tr.id_aux <> 'Monitor' AND tr.id_aux <> 'Teclado' AND tr.id_aux <> 'Mouse') OR tr.id_aux IS NULL)");
        $this->db->group_by("rec.id");
		$this->db->order_by("cantidad", "desc");
        $query = $this->db->get();
        return $query->result_array();
	}

    /**
     * Lista el inventario activo e inactivo  del departamento de audiovisuales con tada la informacion de sus relaciones
     * @return Array
     */
    public function Listar_audiovisual_general() {
       $this->db->select("u2.valor modelo,u.valor recurso,u1.valor marca,p.serial,p.descripcion,p.fecha_ingreso,p.fecha_garantia,p.estado,p.id,u3.valor estado_recurso,p.estado_recurso estado_aux,p.codigo_interno");
         $this->db->from('inventario p');
        $this->db->join('valor_parametro u', 'p.tipo=u.id');
        $this->db->join('valor_parametro u1', 'p.id_marca=u1.id');
        $this->db->join('valor_parametro u3', 'p.estado_recurso=u3.id_aux');
        $this->db->join('valor_parametro u2', 'p.id_modelo=u2.id');
        $this->db->where('p.estado', "1");
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Mustra los recursos que pertenecen al area de audiovisuales que se encuentren activos por tipo de recurso
     * @param Integer $tipo 
     * @return Array
     */
    public function Listar_audiovisual_general_tipo($tipo) {

        if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Aud" || $_SESSION["perfil"] == "Admin_Aud") {
            $query = $this->db->query("SELECT er.valor estado_gen, i.id,i.serial,i.tipo,i.codigo_interno,i.estado_recurso,cr.valor,il.estado estado_responsable 
            FROM inventario i 
            INNER JOIN valor_parametro tr ON tr.id = i.tipo
            INNER JOIN valor_parametro er ON er.id_aux = i.estado_recurso
            INNER JOIN inventario_lugares il ON il.id_inventario = i.id
            INNER JOIN valor_parametro cr ON cr.id = il.id_ubicacion
            WHERE  (i.estado_recurso = 'RecEsp' OR i.estado_recurso= 'RecAct') AND i.tipo_modulo = 'Inv_Aud' AND i.estado = 1 AND cr.id_aux = 'Rec_Res' AND i.tipo= $tipo AND il.estado = 'ResAct'
            ORDER by tr.valor ASC;");
            return $query->result_array();
        } 
        $query = $this->db->query("SELECT er.valor estado_gen, i.id,i.serial,i.tipo,i.codigo_interno,i.estado_recurso,cr.valor,il.estado estado_responsable 
        FROM inventario i 
        INNER JOIN valor_parametro tr ON tr.id = i.tipo
        INNER JOIN valor_parametro er ON er.id_aux = i.estado_recurso
        INNER JOIN inventario_lugares il ON il.id_inventario = i.id
        INNER JOIN valor_parametro cr ON cr.id = il.id_ubicacion
        WHERE  i.estado_recurso= 'RecAct' AND i.tipo_modulo = 'Inv_Aud' AND i.estado = 1 AND cr.id_aux = 'Rec_Res' AND i.tipo= $tipo
        ORDER by tr.valor ASC;
        ");
        return $query->result_array();
    }

    /**
     * Valida la existencia de un serial 
     * @param String $serial 
     * @return Booelan
     */
    public function Existe_serial($serial) {
        $this->db->select('count(*) as cantidad');
        $this->db->from($this->table_inventario);
        $this->db->where('serial', $serial);
        $this->db->where('estado', "1");
        $result = $this->db->get();
        $cantidad = $result->result_array();
        if ($cantidad[0]["cantidad"] == 0) {
            return false;
        } else {
            return true;
            ;
        }
    }

    /**
     * Valida la Existencia de un codigo interno
     * @param String $codigo 
     * @return Booelan
     */
    public function Existe_codigo_interno($codigo) {
        $this->db->select('count(*) as cantidad');
        $this->db->from($this->table_inventario);
        $this->db->where('codigo_interno', $codigo);
        $this->db->where('estado', "1");
        $result = $this->db->get();
        $cantidad = $result->result_array();
        if ($cantidad[0]["cantidad"] == 0) {
            return false;
        } else {
            return true;
            ;
        }
    }

    /**
     * Obtiene la informacion de general del inventario de un recurso en especifico
     * @param Integer $id 
     * @return Array
     */
    public function obtener_inventario_id($id) {
        $this->make_query();
        $this->db->where('estado', "1");
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }


    /**
     * Captura el responsable actual de un recurso en especifico
     * @param Integer $id_inventario 
     * @return Arary
     */
    public function obtener_responsable_actual($id_inventario) {
        $this->db->select("*");
        $this->db->from("responsables");
        $this->db->where('estado', "1");
        $this->db->where('id_dispositivo', $id_inventario);
        $this->db->where('estado_responsable', "ResAct");
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Muestra el historial de los responsables de un recurso en especifico
     * @param Integer $id 
     * @return Array
     */
    public function Cargar_responsables($id) {
        $this->db->select("r.*, v.valor cargo, vp.valor departamento,  CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona,  CONCAT(pa.nombre, ' ', pa.apellido, ' ', pa.segundo_apellido) AS persona_agrega,  CONCAT(pi.nombre, ' ', pi.apellido, ' ', pi.segundo_apellido) AS persona_elimina",false);
        $this->db->from('inventario_responsables r');
        $this->db->join('personas p', 'r.id_persona = p.id');
        $this->db->join('personas pa', 'r.id_usuario_asigna = pa.id','left');
        $this->db->join('personas pi', 'r.id_usuario_retira = pi.id','left');
        $this->db->join('cargos_departamentos c', 'p.id_cargo = c.id', 'left');
        $this->db->join('valor_parametro v', 'p.id_cargo_sap = v.id');
        $this->db->join('valor_parametro vp', 'c.id_departamento = vp.id', 'left');
        $this->db->where('r.id_inventario', $id);
        $this->db->_protect_identifiers = false;
        $this->db->order_by("FIELD (r.estado,1,0)");
        $this->db->_protect_identifiers = true;
        $this->db->order_by("r.fecha_asigna", "desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Muestra los mantenimeintos realizados a un recurso en especifico
     * @param Integer $id 
     * @return Array
     */
    public function listar_mantenimientos($id) {
        $this->db->select("r.fecha,p.usuario usuario,u.valor tipo,u.valorx descripcion,u1.valor estado,r.id,u1.id_aux estado_valor, r.descripcion descripcion_man");

        $this->db->from('mantenimiento r');
        $this->db->join('valor_parametro u', 'r.id_tipo=u.id');
        $this->db->join('valor_parametro u1', 'r.estado_mant=u1.id_aux');
        $this->db->join('personas p', 'r.id_usuario=p.id');
        $this->db->where('r.estado', "1");
        $this->db->where('r.id_inventario', $id);
        $this->db->_protect_identifiers = false;
        $this->db->order_by("FIELD (r.estado_mant,'Mat_Curs','Mat_Term')");
        $this->db->order_by('r.fecha_registra', "desc");
        $this->db->_protect_identifiers = true;
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Muestra los perifericos de un recurso en especifico
     * @param Integer $id 
     * @return Array
     */
    public function Listar_perifericos($id, $activos=false) {
        $this->db->select("i.serial,u2.valor marca,u3.valor modelo,u.valor recurso,p.id, p.estado, p.fecha_registra,i.codigo_interno, est.valor AS estado_periferico, CONCAT(pr.nombre, ' ', pr.apellido, ' ', pr.segundo_apellido) AS persona",false);
        $this->db->from('perifericos_recursos p');
        $this->db->join('inventario i', 'p.id_periferico=i.id');
        $this->db->join('valor_parametro u', 'i.tipo=u.id');
        $this->db->join('valor_parametro u2', 'i.id_marca=u2.id');
        $this->db->join('valor_parametro u3', 'i.id_modelo=u3.id');
        $this->db->join('personas pr', 'p.usuario_registra = pr.id');   
        $this->db->join('valor_parametro est', 'i.estado_recurso = est.id_aux');    
		$this->db->where('p.id_recurso', $id);
		if($activos) $this->db->where('p.estado', 1);
        $this->db->_protect_identifiers = false;
        $this->db->order_by("FIELD (p.estado,1,0)");
        $this->db->_protect_identifiers = true;
        $this->db->order_by("p.fecha_registra", "desc");
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Modifica el estado de un recurso ya sea activo, inactivo, manteniento
     * @param Integer $id 
     * @param String $estado 
     * @return Integer
     */
    public function Modificar_estado_recurso($id, $estado, $usuario, $fecha, $mensaje = '') {
        $this->db->set('estado_recurso', $estado);
        if ($estado == "RecBaja") {
            $this->db->set('motivo_baja', $mensaje);
            $this->db->set('fecha_de_baja', $fecha);
            $this->db->set('usuario_de_baja', $usuario);
        }
        $this->db->where('id', $id);
        $this->db->update("inventario");
        return 1;
    }

    /**
     * Termina un mantenimiento que se encontraba en curso, ademas valida que el recurso tenga mantenimientos asignados
     * @param Integer $id 
     * @param Integer $inventario 
     * @return type
     */
    public function Terminar_Mantenimiento($id, $inventario, $fecha, $usuario) {
        $this->db->set('estado_mant', 'Mat_Term');
        $this->db->set('fecha_termina', $fecha);
        $this->db->set('usuario_termina', $usuario);
        $this->db->where('id', $id);
        $this->db->update("mantenimiento");
        $total = $this->contar_Manteniminetos($inventario);
        if ($total == 0) {
            $this->Modificar_estado_recurso($inventario, "RecAct", "", "");
        }
        return 1;
	}

    /**
     * Cuenta el total de mantenimientos en curso de un recurso en especifico
     * @param Integer $id 
     * @return Integer
     */
    public function contar_Manteniminetos($id) {
        $this->db->select("COUNT(id) total");
        $this->db->from('mantenimiento r');
        $this->db->where('estado_mant', "Mat_Curs");
        $this->db->where('id_inventario', $id);
        $query = $this->db->get();
        $row = $query->row();
        return $row->total;
    }

    /**
     * Cuenta cuantos recursos tienen el mismo periferico asignado
     * @param Integer $id 
     * @return Integer
     */
    public function Periferico_ya_asignado($id) {
        $this->db->select("i.serial,i2.serial as serial_recurso");
        $this->db->from('perifericos_recursos p');
        $this->db->join('inventario i', 'p.id_periferico=i.id');
        $this->db->join('inventario i2', 'p.id_recurso=i2.id');
        $this->db->where('p.estado', "1");
        $this->db->where('p.id_periferico', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Muestra los datos generales de un recursos activo en especifico
     * @param Integer $id 
     * @return Array
     */
    public function obtener_Datos_inventario($id) {
        $this->db->select('i.*, u.valor tipo_valor, cs.valor codigo_sap, us.valor uso');
        $this->db->from('inventario i');
        $this->db->join('valor_parametro u', 'i.tipo = u.id');
        $this->db->join('valor_parametro cs', 'i.id_codigo_sap = cs.id','left');
        $this->db->join('valor_parametro us', 'i.uso_del_activo = us.id','left');
        $this->db->where('i.estado', "1");
        $this->db->where('i.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Obtiene el detalle de la informacion de los recursos de tipo portatil 
     * @param Integer $id 
     * @return Array
     */
    public function obtener_detalle_Recurso($id) {
        $this->db->select("i.id,i.sistema_operativo id_sistema_operativo,i.procesador id_procesador,i.id,i.disco_duro,i.memoria,u.valor procesador,u1.valor sistema_operativo, inv.serial, inv.codigo_interno");
        $this->db->from('detalle_inventario i');
        $this->db->join('valor_parametro u', 'i.procesador=u.id');
        $this->db->join('valor_parametro u1', 'i.sistema_operativo=u1.id');
        $this->db->join('inventario inv', 'inv.id = i.id_inventario');
        $this->db->where('inv.estado', "1");
        $this->db->where('inv.id', $id);
        $query = $this->db->get();
        return $query->result_array();
	}
	
	public function es_periferico($id){
		$this->db->select("pr.id_periferico, i.id, i.serial");
        $this->db->from('perifericos_recursos pr');
        $this->db->join('inventario i', 'i.id = pr.id_recurso');
        $this->db->where('pr.id_periferico', $id);
        $this->db->where('pr.estado', 1);
        $this->db->where('i.estado', 1);
        $query = $this->db->get();
        return [
			'data' => $query->result_array(),
			'periferico' => true,
		];
	}

    /**
     * Obtiene los perifericos de un recurso
     * @param Integer $id 
     * @param String $tipo 
     * @return Arary
     */
    public function obtener_perifericos($id) {
        $this->db->select("");
        $this->db->from('perifericos_recursos i');

        $this->db->where('i.id_recurso', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtiene la informacion en detalle con sus relaciones de un recurso en especifio
     * @param Integer $id 
     * @return Array
     */
    public function obtener_informacion_inventario($id) {
        $this->db->select("i.id,i.serial,i.descripcion,i.valor,i.tipo,u.valor modelo,u1.valor marca,fecha_ingreso,fecha_garantia,u2.valor tipo_valor,i.codigo_interno");
        $this->db->from('inventario i');
        $this->db->join('valor_parametro u', 'i.id_modelo=u.id');
        $this->db->join('valor_parametro u1', 'i.id_marca=u1.id');
        $this->db->join('valor_parametro u2', 'i.tipo=u2.id');
        $this->db->where('i.estado', "1");
        $this->db->where('i.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Retira un periferico de un recurso en especifico
     * @param Integer $id 
     * @param String $usuario 
     * @return Integer
     */
    public function retirar_periferico($id, $usuario, $fecha) {
        $this->db->set('usuario_elimina', $usuario);
        $this->db->set('fecha_elimina', $fecha);
        $this->db->set('estado', "0");
        $this->db->where('id', $id);
        $this->db->update($this->table_perifericos_recursos);

        return 1;
    }

    /**
     * Registra los perifericos de un recurso ejemplo torre (teclado,mouse,monitor etc)
     * @param Integer $id_recurso 
     * @param Integer $id_perifericos 

     * @return Integer
     */
    public function guardar_perifericos($id_recurso, $id_perifericos) {
        $this->db->insert($this->table_perifericos_recursos, array(
            "id_recurso" => $id_recurso,
            "id_periferico" => $id_perifericos,
            "usuario_registra" => $_SESSION['persona'],
        ));

        return 1;
    }


    /**
     * Obtiene el ultimo registro de un recurso guardado por un usuario en especifico
     * @param Integer $usuario 
     * @return Arary
     */
    public function obtener_ultimo_registro() {
        $this->make_query();
        $this->db->where('estado', "1");
        $this->db->where('usuario_registra', $_SESSION['persona']);
        $this->db->order_by("id", "desc");
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->result_array();
	}
	
	public function verificar_estado($recurso){
		$this->db->select("count(*) as cant");
        $this->db->from('mantenimiento');
        $this->db->where('id_inventario', $recurso);
        $this->db->where('estado_mant', 'Mat_Curs');
        $query = $this->db->get();
		return $query->row()->cant;
	}

    public function Traer_perifericos($text, $tipo){
		$this->db->select("tip.valor AS recurso, mar.valor AS marca, mo.valor AS modelo, est.valor AS estado,i.id, i.serial, i.codigo_interno");
		$this->db->from('inventario i');
		$this->db->join('valor_parametro tip', 'i.tipo = tip.id');
		$this->db->join('valor_parametro mar', 'i.id_marca = mar.id');
		$this->db->join('valor_parametro mo', 'i.id_modelo = mo.id');
        $this->db->join('valor_parametro est', 'i.estado_recurso = est.id_aux');    
        $this->db->join('permisos_parametros pp', " i.tipo = pp.vp_secundario_id AND pp.vp_principal_id = {$tipo} "); 
		$this->db->where('i.estado_recurso = "RecAct"');
		$this->db->where("(SELECT count(id) FROM perifericos_recursos pr WHERE pr.id_periferico = i.id AND pr.estado=1) = 0");
		$this->db->where("(tip.valor LIKE '%$text%' OR i.codigo_interno LIKE '%$text%' OR i.serial LIKE '%$text%')");
        $query = $this->db->get();
        // print_r($this->db->last_query());
        return $query->result_array();
	}
 
    public function listar_personas_cargos($id) {
		$this->db->select("p.id, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona",false);
        $this->db->from('personas p');
        $this->db->join('cargos_departamentos c', 'p.id_cargo = c.id');
        $this->db->where('c.id',$id);
        $this->db->where('p.estado', "1");
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        return $query->result_array();
	}
    public function listar_permisos_parametros ($id_principal){
        $this->db->select("vp.valor nombre, vp.id ");
        $this->db->from('permisos_parametros pp');
        $this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
        $this->db->where('pp.vp_principal_id', $id_principal);
        $this->db->where('vp.estado', 1);
        $this->db->order_by('vp.valor');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function buscar_persona($where)
    {
          $this->db->select("vc.valor cargo,cd.id_cargo id_cargo_dep,p.id_cargo, p.identificacion,p.id,p.nombre,p.segundo_nombre,p.apellido,p.segundo_apellido,p.correo,p.id_tipo_identificacion,p.fecha_expedicion,p.lugar_expedicion,p.fecha_nacimiento,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,u2.valor tipo_identificacion", false);
          $this->db->from('personas p');
          $this->db->join('valor_parametro u2', 'p.id_tipo_identificacion = u2.id');
          $this->db->join('cargos_departamentos cd', 'p.id_cargo = cd.id','left');
          $this->db->join('valor_parametro vc', 'cd.id_cargo = vc.id','left');
          $this->db->where($where);
          $query = $this->db->get();
          return $query->result_array();
    }
    public function listar_lugares($id_inventario){
        
        $this->db->select("il.*,ve.valor estado_v, vl.valor lugar,vu.valor ubicacion,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as usuario_asigna,CONCAT(pr.nombre,' ',pr.apellido,' ',pr.segundo_apellido) as usuario_retira", false);
        $this->db->from('inventario_lugares il');
        $this->db->join('valor_parametro vl', 'il.id_lugar = vl.id');
        $this->db->join('valor_parametro vu', 'il.id_ubicacion = vu.id');   
        $this->db->join('valor_parametro ve', 'il.estado = ve.id_aux');   
        $this->db->join('personas p', 'il.id_usuario_asigna = p.id');   
        $this->db->join('personas pr', 'il.id_usuario_retira = pr.id', 'left');   
        $this->db->where('il.id_inventario', $id_inventario);
        $this->db->order_by('il.fecha_asigna', "desc");

        $query = $this->db->get();
        return $query->result_array();
    }
    public function traer_ultimo_lugar_inventario($id_inventario){

        $this->db->select("il.*");
        $this->db->from('inventario_lugares il');
        $this->db->order_by("id", "desc");
        $this->db->where('il.id_inventario', $id_inventario);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->result_array();
      }
      public function traer_ultimo_lugar(){
          
          $this->db->select("il.*");
          $this->db->from('inventario_lugares il');
          $this->db->order_by("id", "desc");
          $this->db->limit(1);
          $query = $this->db->get();
          return $query->result_array();
        }
    public function buscar_responsable_id($id, $id_inventario){

        $this->db->select("r.*");
        $this->db->from('inventario_responsables r');
        $this->db->order_by("id", "desc");
        $this->db->where('r.estado', 1);
        $this->db->where('r.id_persona', $id);
        $this->db->where('r.id_inventario', $id_inventario);
        $query = $this->db->get();
        $row = $query->row();
        return is_null($row) || empty($row) ? null : $row->id;

        }
    public function cantidad_responsables($id_inventario)
    {
        $this->db->select("COUNT(r.id) total ");
        $this->db->from('inventario_responsables r');
        $this->db->where('r.estado', 1);
        $this->db->where("r.id_inventario",$id_inventario);
        $query = $this->db->get();
        $row = $query->row();
        return is_null($row) || empty($row) ? null :$row->total;
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
    public function listar_recursos_inventario ($id){
        $this->db->select("pp.*, vp.valor, vp.id");
        $this->db->from('permisos_parametros pp');
        $this->db->join('valor_parametro vp', 'pp.vp_secundario_id = vp.id');
        $this->db->where('pp.vp_principal_id', $id);
        $this->db->where('vp.estado', 1);
        $query = $this->db->get();
        return $query->result_array();
      }

    public function en_fecha_proyecto(){
        $this->db->select("count(*) total");
        $this->db->from('inventario i');
        $this->db->where("i.fecha_fin_proyecto BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)");
        $this->db->where('i.estado', 1);
        $query = $this->db->get();
        $row = $query->row();
        return $row->{'total'};
	}

	public function en_fecha_garantia($tipo_modulo){
        $this->db->select("count(*) total");
        $this->db->from('inventario i');
        $this->db->where("i.fecha_garantia BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)");
        $this->db->where('i.estado', 1);
        $this->db->where('i.tipo_modulo', $tipo_modulo);
        $query = $this->db->get();
        $row = $query->row();
        return $row->{'total'};
    }

    public function en_fecha_a_vencer($periodicidad){
        $this->db->select("count(*) total");
        $this->db->from('mantenimientos_laboratorios ml');
        $this->db->where("ml.fecha_prox_mtto BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)");
        $this->db->where('ml.estado', 1);
        //$this->db->where('m.id_inventario', $tipo_modulo);
        $query = $this->db->get();
        $row = $query->row();
        return $row->{'total'};
    }
	
    public function tiempo_investigacion(){
		$this->db->select("vp.valor");
        $this->db->from('valor_parametro vp');
		$this->db->where('vp.idparametro', '6');
        $this->db->where('vp.estado', '1');
        $this->db->order_by("cantidad", "desc");
        $this->db->order_by("nombre");
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function listar_modificaciones($id_solicitud) {
		$this->db->select("bm.*, po.valor parametro_anterior, pa.valor parametro_actual, CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS usuario_modifica", false);
		$this->db->from('bitacora_modificaciones bm');
        $this->db->where("bm.id_solicitud = '$id_solicitud' AND (bm.tabla = 'inventario' OR bm.tabla = 'detalle_inventario')");
        $this->db->join('personas p', 'bm.id_usuario_modifica = p.id');
        $this->db->join('valor_parametro po', 'bm.anterior = po.id', 'left');
        $this->db->join('valor_parametro pa', 'bm.actual = pa.id', 'left');
        $query = $this->db->get();
		return $query->result_array();
	}
	
	public function cargar_tipo_recursos($tipo_modulo){
		$this->db->select("rec.id, rec.valor recurso, rec.id_aux aux, rec.valorx");
		$this->db->from('permisos_parametros pp ');
        $this->db->join('valor_parametro rec', 'rec.id = pp.vp_secundario_id');
        $this->db->where('pp.vp_principal', $tipo_modulo);
        $this->db->where('rec.idparametro', 6);
        $this->db->where('pp.estado', 1);
        $query = $this->db->get();
		return $query->result_array();
	}

    public function recurso_serial($serial) {
        $this->db->select('i.id, i.tipo_modulo');
        $this->db->from('inventario i');
        $this->db->where('i.serial', $serial);
        $this->db->where('i.estado', "1");
        $query = $this->db->get();
        $row = $query->row();
        return $row;
	}
	
	public function listar_dependencias($tipo_modulo, $asignados=''){
        $where = '';
        if($tipo_modulo === 'Inv_Tec'){
            $where = " AND dep.id = vp.id AND tr.id_aux <> 'Monitor' AND tr.id_aux <> 'Teclado' AND tr.id_aux <> 'Mouse'";
        }
		$query = $this->db->query("SELECT vp.id, vp.valor dependencia, (SELECT COUNT(pp.id)
				FROM permisos_parametros pp 
				INNER JOIN valor_parametro dep ON dep.id = pp.vp_principal_id
				INNER JOIN valor_parametro ubi ON ubi.id = pp.vp_secundario_id
				INNER JOIN inventario_lugares il ON il.id_ubicacion = ubi.id
				INNER JOIN inventario i ON i.id = il.id_inventario
                Inner JOIN valor_parametro tr ON i.tipo = tr.id 
                WHERE dep.idparametro = 115
                AND dep.id = vp.id" .
				$where . " AND i.tipo_modulo = '$tipo_modulo'
				AND il.estado = 'ResAct') cantidad
			FROM valor_parametro vp
			WHERE vp.idparametro = 3
			HAVING cantidad > 0
			ORDER BY cantidad DESC;");
        return $query->result_array();
    }
    
    public function listar_ubicaciones($tipo_modulo){
        $where = $tipo_modulo === 'Inv_Tec'
            ? " AND tr.id_aux <> 'Monitor' AND tr.id_aux <> 'Teclado' AND tr.id_aux <> 'Mouse'"
            : "";
        $query = $this->db->query("SELECT lug.id, lug.valor dependencia, (SELECT COUNT(pp.id)
            FROM permisos_parametros pp 
            INNER JOIN valor_parametro dep ON dep.id = pp.vp_principal_id
            INNER JOIN valor_parametro ubi ON ubi.id = pp.vp_secundario_id
            INNER JOIN inventario_lugares il ON il.id_ubicacion = ubi.id AND il.id_lugar = dep.id
            INNER JOIN inventario i ON i.id = il.id_inventario
            INNER JOIN valor_parametro tr ON i.tipo = tr.id 
            WHERE dep.id = lug.id " 
            . $where . 
            " AND i.tipo_modulo = '$tipo_modulo'
            AND il.estado = 'ResAct') cantidad
        FROM valor_parametro lug
        WHERE lug.idparametro = 115
        HAVING cantidad > 0
        ORDER BY cantidad DESC;");
        return $query->result_array();
    }

	public function listar_dependencias_usuario($tipo_modulo, $asignados='', $persona){
		$query = $this->db->query("SELECT vp.id, vp.valor dependencia, (SELECT COUNT(pp.id)
			FROM permisos_parametros pp 
			INNER JOIN valor_parametro dep ON dep.id = pp.vp_principal_id
			INNER JOIN valor_parametro ubi ON ubi.id = pp.vp_secundario_id
			INNER JOIN inventario_lugares il ON il.id_ubicacion = ubi.id
			INNER JOIN inventario i ON i.id = il.id_inventario
			INNER JOIN valor_parametro tr ON i.tipo = tr.id
			INNER JOIN inventario_responsables ir ON ir.id_inventario = i.id AND ir.id_persona = ". $_SESSION['persona'] ."
			WHERE dep.idparametro = 3 AND dep.id = vp.id
			AND dep.id = vp.id
			AND tr.id_aux <> 'Monitor'
			AND tr.id_aux <> 'Teclado' 
			AND tr.id_aux <> 'Mouse'
			AND i.tipo_modulo = '$tipo_modulo'
			AND il.estado = 'ResAct') cantidad
		FROM valor_parametro vp
		WHERE vp.idparametro = 3
		HAVING cantidad > 0
		ORDER BY cantidad DESC");
        return $query->result_array();
	}
    
    
    public function listar_dependencias_usuario_lab($tipo_modulo){
		$where = $tipo_modulo === 'Inv_Tec'
            ? " AND tr.id_aux <> 'Monitor' AND tr.id_aux <> 'Teclado' AND tr.id_aux <> 'Mouse'"
            : "";
        $query = $this->db->query("SELECT lug.id, lug.valor dependencia, (SELECT COUNT(pp.id)
            FROM permisos_parametros pp 
            INNER JOIN valor_parametro dep ON dep.id = pp.vp_principal_id
            INNER JOIN valor_parametro ubi ON ubi.id = pp.vp_secundario_id
            INNER JOIN inventario_lugares il ON il.id_ubicacion = ubi.id AND il.id_lugar = dep.id
            INNER JOIN inventario i ON i.id = il.id_inventario
            INNER JOIN valor_parametro tr ON i.tipo = tr.id
            INNER JOIN inventario_responsables ir ON ir.id_inventario = i.id AND ir.estado = 1 AND ir.id_persona = ". $_SESSION['persona'] ."
            WHERE dep.id = lug.id " 
            . $where . 
            " AND i.tipo_modulo = '$tipo_modulo'
            AND il.estado = 'ResAct') cantidad
        FROM valor_parametro lug
        WHERE lug.idparametro = 115
        HAVING cantidad > 0
        ORDER BY cantidad DESC;");
        return $query->result_array();
	}

	public function ubicaciones_dependencias($dependencia, $tipo_modulo, $tipo_listar){
        $and = '';
        if($tipo_modulo === 'Inv_Tec')
            $and = " AND tr.id_aux <> 'Monitor' AND tr.id_aux <> 'Teclado' AND tr.id_aux <> 'Mouse' ";
        if($tipo_listar === 'ubi') $and .= " AND il.id_lugar = $dependencia ";
		$this->db->select("ubi.id, ubi.valor ubicacion, ubi.id_aux, IF(ubi.id_aux = 'Bod_Tec', 
		(SELECT COUNT(il.id) 
			FROM inventario_lugares il 
			INNER JOIN inventario i ON i.id = il.id_inventario
			INNER JOIN valor_parametro tr ON i.tipo = tr.id
            WHERE il.id_ubicacion = ubi.id
			AND il.estado = 'ResAct'
			AND i.tipo_modulo = '$tipo_modulo'), 
		(SELECT COUNT(il.id) 
			FROM inventario_lugares il 
			INNER JOIN inventario i ON i.id = il.id_inventario
			INNER JOIN valor_parametro tr ON i.tipo = tr.id
            WHERE il.id_ubicacion = ubi.id " .
            $and . " 
			AND i.tipo_modulo = '$tipo_modulo'
			AND il.estado = 'ResAct')) cantidad", false);
		$this->db->from('permisos_parametros pp');
        $this->db->join('valor_parametro ubi', 'ubi.id = pp.vp_secundario_id');
		$this->db->where("pp.vp_principal_id", $dependencia);
        $this->db->order_by("ubicacion");
        $this->db->having('cantidad >',  0);
        $query = $this->db->get();
		return $query->result_array();
	}

	public function ubicaciones_dependencias_usuario($dependencia, $tipo_modulo, $tipo_listar){
		$this->db->select("ubi.id, ubi.valor ubicacion, ubi.id_aux, IF(ubi.id_aux = 'Bod_Tec', 
		(SELECT COUNT(il.id) 
			FROM inventario_lugares il 
			INNER JOIN inventario i ON i.id = il.id_inventario
			INNER JOIN inventario_responsables ir ON i.id = ir.id_inventario AND ir.id_persona = " . $_SESSION['persona'] . "
			INNER JOIN valor_parametro tr ON i.tipo = tr.id
            WHERE il.id_ubicacion = ubi.id
            AND il.id_lugar = $dependencia
			AND il.estado = 'ResAct'
			AND i.tipo_modulo = '$tipo_modulo'), 
		(SELECT COUNT(il.id) 
			FROM inventario_lugares il 
			INNER JOIN inventario i ON i.id = il.id_inventario
			INNER JOIN inventario_responsables ir ON i.id = ir.id_inventario AND ir.id_persona = " . $_SESSION['persona'] . "
			INNER JOIN valor_parametro tr ON i.tipo = tr.id
            WHERE il.id_ubicacion = ubi.id
            AND il.id_lugar = $dependencia
			AND tr.id_aux <> 'Monitor' 
			AND tr.id_aux <> 'Teclado' 
			AND tr.id_aux <> 'Mouse'
			AND i.tipo_modulo = '$tipo_modulo'
			AND il.estado = 'ResAct')) cantidad", false);
		$this->db->from('permisos_parametros pp');
        $this->db->join('valor_parametro ubi', 'ubi.id = pp.vp_secundario_id');
		$this->db->where("pp.vp_principal_id", $dependencia);
		$this->db->having("cantidad > 0");
		$this->db->order_by("ubicacion");
        $query = $this->db->get();
		return $query->result_array();
	}

    public function ubicaciones_dependencias_usuario_lab ($dependencia, $tipo_modulo, $tipo_listar) {
        $and = '';
        if($tipo_modulo === 'Inv_Tec')
            $and = " AND tr.id_aux <> 'Monitor' AND tr.id_aux <> 'Teclado' AND tr.id_aux <> 'Mouse' ";
        if($tipo_listar === 'ubi') $and .= " AND il.id_lugar = $dependencia ";
		$this->db->select("ubi.id, ubi.valor ubicacion, ubi.id_aux, IF(ubi.id_aux = 'Bod_Tec', 
		(SELECT COUNT(il.id) 
			FROM inventario_lugares il 
			INNER JOIN inventario i ON i.id = il.id_inventario
			INNER JOIN valor_parametro tr ON i.tipo = tr.id
            WHERE il.id_ubicacion = ubi.id
			AND il.estado = 'ResAct'
			AND i.tipo_modulo = '$tipo_modulo'), 
		(SELECT COUNT(il.id) 
			FROM inventario_lugares il 
			INNER JOIN inventario i ON i.id = il.id_inventario
			INNER JOIN valor_parametro tr ON i.tipo = tr.id
            WHERE il.id_ubicacion = ubi.id " .
            $and . " 
			AND i.tipo_modulo = '$tipo_modulo'
			AND il.estado = 'ResAct')) cantidad", false);
		$this->db->from('permisos_parametros pp');
        $this->db->join('valor_parametro ubi', 'ubi.id = pp.vp_secundario_id');
		$this->db->where("pp.vp_principal_id", $dependencia);
        $this->db->order_by("ubicacion");
        $this->db->having('cantidad >',  0);
        $query = $this->db->get();
		return $query->result_array();
    }

	public function retirar_responsables_actuales($item){
		$this->db->set('estado', 0);
        $this->db->set('fecha_elimina', date("Y-m-d H:i:s"));
        $this->db->set('id_usuario_retira', $_SESSION['persona']);
        $this->db->where('id_inventario', $item);
        $this->db->update("inventario_responsables");
        return 1;
	}

	public function get_modelos_marca($marca){
		$this->db->select("vp.id, vp.valor nombre, pp.id permiso");
		$this->db->from("valor_parametro vp");
		$this->db->join("permisos_parametros pp", "vp.id = pp.vp_secundario_id AND pp.vp_principal_id = $marca", "left");
		$this->db->where('vp.idparametro', 5);
		$this->db->order_by('pp.id', 'desc');
        $query = $this->db->get();
        $row = $query->result_array();
        return $row;
	}

	public function listar_personas($text){
		$this->db->select("id, CONCAT(nombre, ' ', apellido, ' ', segundo_apellido) AS nombre", false);
		$this->db->from("personas");
		$this->db->like("nombre", "$text");
		$this->db->or_like("segundo_nombre", "$text");
		$this->db->or_like("apellido", "$text");
		$this->db->or_like("segundo_apellido", "$text");
		$this->db->or_like("identificacion", "$text");
		$this->db->or_like("usuario", "$text");
		$this->db->where("estado", 1);
        $query = $this->db->get();
        $row = $query->result_array();
        return $row;
	}

	public function get_permisos_asignados(){
		$this->db->select("permiso, agregar, modificar, gestionar, aux");
		$this->db->from("permisos_personas_inventario");
		$this->db->where('persona_id', $_SESSION['persona']);
        $query = $this->db->get();
		$row = $query->result_array();
		return $row;
	}

	public function listar_tipos_recursos_asignados($recurso){
		$this->db->select("vp.id, vp.valor nombre", FALSE);
		$this->db->from("permisos_parametros pp");
		$this->db->join("valor_parametro vp", "vp.id = pp.vp_secundario_id AND vp.idparametro = 6");
		$this->db->where('pp.vp_principal', $recurso);
		$this->db->where('pp.estado', 1);
		$this->db->where('vp.estado', 1);
        $query = $this->db->get();
		$row = $query->result_array();
		return $row;
    }
    
    public function cargar_requerimientos_tecnicos($id){
        $this->db->select("vp.id, vp.valor requerimiento, rr.id asignado", false);
		$this->db->from("valor_parametro vp");
		$this->db->join("requerimientos_recurso rr", "rr.requerimiento_id = vp.id AND rr.inventario_id = $id AND rr.estado = 1", "left");
		$this->db->where('vp.idparametro', 230);
		$this->db->where('vp.estado', 1);
        $query = $this->db->get();
		$row = $query->result_array();
		return $row;
    }

    public function get_datos_lab($id) {
        $this->db->select("dt.id datos_tecnicos, tec.valor tecnologia, fas.valor fase, dt.vida_util, dt.peso, dt.potencia, dt.voltaje, i.nombre_activo, i.referencia, i.lugar_origen, prov.valor proveedor, uso.valor uso_equipo, i.observaciones, i.valor, est.valor estado, uni.valor unidades");
		$this->db->from("inventario i");
		$this->db->join("datos_tecnicos dt", "dt.inventario_id = i.id", "left");
		$this->db->join("valor_parametro uso", "i.uso_del_activo = uso.id AND uso.estado = 1", "left");
		$this->db->join("valor_parametro prov", "i.proveedor = prov.id AND uso.estado = 1", "left");
		$this->db->join("valor_parametro tec", "tec.id = dt.tecnologia AND tec.estado = 1", "left");
        $this->db->join("valor_parametro fas", "dt.fase = fas.id AND fas.estado = 1", "left");
        $this->db->join("valor_parametro uni", "dt.unidades_id = uni.id AND uni.estado = 1", "left");
		$this->db->join("valor_parametro est", "est.id_aux = i.estado_recurso");
		$this->db->where('i.id', $id);
		$this->db->where('i.estado', 1);
        $query = $this->db->get();
        $data = $query->row();
        $this->db->select("req.id, req.valor requerimiento");
		$this->db->from("requerimientos_recurso rr");
		$this->db->join("valor_parametro req", "req.id = rr.requerimiento_id AND req.estado = 1");
		$this->db->where('rr.inventario_id', $id);
        $this->db->where('rr.estado', 1);
        $query = $this->db->get();
        $data->{"requerimientos"} = $query->result_array();
		return $data;
    }

    public function cargar_documentos_disponibles($articulo){
        $this->db->select("doc.id, doc.valor documento, di.id adjuntado, di.ruta_documento ruta");
		$this->db->from("valor_parametro doc");
		$this->db->join("documentos_inventario di", "doc.id = di.tipo_id AND di.articulo_id = $articulo", "left");
		$this->db->where('doc.idparametro', 229);
		$this->db->where('doc.estado', 1);
		$this->db->group_by('doc.id');
		$this->db->order_by('doc.valor');
        $query = $this->db->get();
        $documentos = $query->result_array();
		return $documentos;
    }

    public function get_mantenimientos_lab($id){
        $this->db->select("ml.ultima_fecha, per.valor periodicidad, ml.fecha_modifica, ml.fecha_registra, t.valor tipo, CONCAT(p.nombre, ' ', p.apellido) persona_modifica, CONCAT(p1.nombre, ' ', p1.apellido) persona_registra, ml.descripcion", false);
		$this->db->from("mantenimientos_laboratorios ml");
		$this->db->join("valor_parametro t", "t.id = ml.tipo_id");
		$this->db->join("valor_parametro per", "ml.periodicidad = per.id_aux AND per.estado = 1", "left");
		$this->db->join("personas p", "p.id = ml.usuario_modifica");
		$this->db->join("personas p1", "p1.id = ml.usuario_registra");
		$this->db->where('ml.inventario_id', $id);
		$this->db->where('ml.estado', 1);
		$this->db->where('t.estado', 1);
		$this->db->where('p.estado', 1);
		$this->db->where('p1.estado', 1);
        $query = $this->db->get();
        $documentos = $query->result_array();
		return $documentos;
	}
	
	public function get_accesorios($text, $tipo_modulo) {
		$this->db->select("i.id, tr.valor recurso, i.serial, i.codigo_interno, ma.valor marca, mo.valor modelo, er.valor estado");
		$this->db->from('inventario i');
        $this->db->join('valor_parametro ma', 'i.id_marca = ma.id');
        $this->db->join('valor_parametro mo', 'i.id_modelo = mo.id');
        $this->db->join('valor_parametro er', 'i.estado_recurso = er.id_aux');
        $this->db->join('valor_parametro tr', 'i.tipo = tr.id');
		$this->db->where('i.tipo_modulo', $tipo_modulo);
		$this->db->like("i.serial", "$text");
        $this->db->where('i.estado', 1);
        $query = $this->db->get();
		return $query->result_array();
    }

    public function buscar_proveedor($dato)
	{
		$this->db->select("vp.valor, vp.id",false);
		$this->db->from('valor_parametro vp');
		$this->db->where("(vp.valor LIKE '%" . $dato ."%') AND vp.idparametro=37 AND vp.estado=1");
		$query = $this->db->get();
		return $query->result_array();
    }

    public function mostrar_notificaciones_mtto($tipo_modulo) {
        $this->db->select("mlab.*, inv.nombre_activo nombre, mlab.fecha_prox_mtto prox_mtto, vp.valor periodicidad, mlab.descripcion descripcion, inv.serial serial, ", false);
        $this->db->from('mantenimientos_laboratorios mlab');
        $this->db->join("inventario inv", 'mlab.inventario_id = inv.id');
        $this->db->join("valor_parametro vp", 'mlab.periodicidad = vp.id_aux');
        $this->db->where('inv.tipo_modulo', $tipo_modulo);
        $this->db->where("mlab.fecha_prox_mtto BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)");
        $this->db->where("mlab.estado", 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function mostrar_notificaciones_garantia($tipo_modulo) {
        $this->db->select("inv.*, inv.nombre_activo nombre, inv.fecha_garantia garantia, inv.serial serial, ", false);
        $this->db->from('inventario inv');
        $this->db->where("inv.fecha_garantia BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)");
        $this->db->where('inv.tipo_modulo', $tipo_modulo);
        $this->db->where("inv.estado", 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function mostrar_notificaciones_investigacion($tipo_modulo) {
        $this->db->select("inv.*,inv.nombre_activo nombre, inv.fecha_fin_proyecto fin_proyecto, inv.serial serial, ", false);
        $this->db->from('inventario inv');
        $this->db->where("inv.fecha_fin_proyecto BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)");
        $this->db->where('inv.tipo_modulo', $tipo_modulo);
        $this->db->where("inv.estado", 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function buscar__nombre($id){
        $this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona", false);
        $this->db->from("personas p");
        $this->db->where("p.id", $id);
        $query = $this->db->get();
		$row = $query->result_array();
		return $row;

    }

    public function exportar_inventario(){		
		$this->db->select("CONCAT(p.nombre,' ',p.apellido, ' ', p.segundo_apellido) AS responsable,
        tip.valor tipo, model.valor modelo, mar.valor marca, est.valor estado_recurso, uact.valor uso_activo, 
        prov.valor proveedor, i.id, i.serial, i.descripcion, i.fecha_ingreso, i.fecha_garantia, i.valor,
        i.codigo_interno, i.nombre_activo, i.lugar_origen, i.observaciones, p.identificacion identificacion_responsable,
        i.referencia, i.valor valor_activo, vlug.valor nom_lugar, vubi.valor nom_ubicacion,
        (SELECT ml.ultima_fecha FROM mantenimientos_laboratorios ml WHERE ml.inventario_id = i.id order by ml.ultima_fecha DESC LIMIT 1) ultima_fecha_mantenimiento, 
        (SELECT vp_tip.valor FROM mantenimientos_laboratorios ml INNER JOIN valor_parametro vp_tip ON vp_tip.id = ml.tipo_id WHERE ml.inventario_id = i.id order by ml.ultima_fecha DESC LIMIT 1) tipo_mantenimiento", false);
		$this->db->from('inventario i');
        $this->db->join("inventario_responsables ir", "ir.id_inventario = i.id AND ir.estado = 1", "left");
        $this->db->join("personas p", "p.id = ir.id_persona", "left");
        $this->db->join("valor_parametro tip", "tip.id = i.tipo", "left");
        $this->db->join("valor_parametro mar", "mar.id = i.id_marca", "left");
        $this->db->join("valor_parametro model", "model.id = i.id_modelo", "left");
        $this->db->join("valor_parametro est", "est.id_aux = i.estado_recurso", "left");
        $this->db->join("valor_parametro uact", "uact.id = i.uso_del_activo", "left");
        $this->db->join("valor_parametro prov", "prov.id = i.proveedor", "left");
        $this->db->join("inventario_lugares ilug", "i.id = ilug.id_inventario", "left");
        $this->db->join("valor_parametro vlug", "vlug.id = ilug.id_lugar", "left");
        $this->db->join("valor_parametro vubi", "vubi.id = ilug.id_ubicacion", "left");
        $this->db->where("i.tipo_modulo", "Inv_Lab");
		$query = $this->db->get();
		return $query->result_array();
	}

    public function buscar__datos__tecnicos($id) {
        $this->db->select('dt.tecnologia, dt.fase, dt.vida_util, dt.peso, dt.potencia, dt.voltaje, dt.unidades_id, i.estado_recurso');
        $this->db->from('datos_tecnicos dt');
        $this->db->join("inventario i", "i.id = dt.inventario_id");
        $this->db->where('dt.estado', "1");
        $this->db->where('dt.inventario_id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function traer_ultimo_mantenimiento ($id) {
        $this->db->select("ml.ultima_fecha, vp.valor tipo_mtto");
        $this->db->from("mantenimientos_laboratorios ml");
        $this->db->join("valor_parametro vp", "vp.id = ml.tipo_id");
        $this->db->where("ml.inventario_id = $id");
        $this->db->order_by("ml.ultima_fecha", "desc");
        $this->db->group_by("ml.ultima_fecha");
        $this->db->limit(1);
        $query = $this->db->get();
		$row = $query->result_array();
		return $row;
    }
}
