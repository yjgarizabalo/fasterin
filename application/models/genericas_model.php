<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class genericas_model extends CI_Model {

    var $table_cargos_departamentos = "cargos_departamentos";
    var $table_parametro = "parametros";
    var $actividades_perfil = "actividades_perfil";
    var $table_valor_parametro = "valor_parametro";
    var $select_column = array("id", "nombre", "descripcion", "estado");
    var $select_column_valor = array("id", "valor", "valorx", "estado", "id_aux", "valory", "idparametro", "valorz", "valora", "valorb");

    public function make_query() {
        $this->db->select($this->select_column);
        $this->db->from($this->table_parametro);
    }

    public function make_query_valor() {
        $this->db->select($this->select_column_valor);
        $this->db->from($this->table_valor_parametro);
	}
	
	public function asignar_jefe($dep, $jefe, $where){
		$this->db->set('id_cargo_jefe', $jefe);
		$this->db->where('id_departamento', $dep);
		if (!is_null($where)) {
			$this->db->where($where);
		}
        $this->db->update($this->table_cargos_departamentos);
        return 1;
	}

	public function asignar_jefe_individual($dep, $jefe, $cargo){
		$this->db->set('id_cargo_jefe', $jefe);
		$this->db->where('id', $cargo);
        $this->db->update($this->table_cargos_departamentos);
        return 1;
	}

    public function Listar() {
        $this->make_query();
        $this->db->where('estado', "1");
        $query = $this->db->get();
        return $query->result();
    }
     //funcion publicas de los  permiso en prueba
      public function listadepermiso(){
        $this->make_query();
        $this->db->where('estado', "1");
        $query = $this->db->get();
        return $query->result();
    }

    public function obtener_valor_parametro_id($id) {
        $this->db->select("vp.id ,vp.valor,vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro,re.valor relacion, vp.valorz, vp.valora, vp.valorb");
        $this->db->from('valor_parametro vp');
        $this->db->join('valor_parametro re', 'vp.valory = re.id','left');
        $this->db->where('vp.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function obtener_valor_parametro_id_2($id) {
        $this->make_query_valor();
        $this->db->where('estado', "1");
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();
        return $row->id_aux;
    }

    public function obtener_valores_parametro($idparametro) {
        $this->make_query_valor();
        $this->db->where('estado', "1");
        $this->db->where('idparametro', $idparametro);
        $this->db->order_by("valor", "asc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function obtener_valores_parametro_aux($id_aux, $idparametro) {
        $this->make_query_valor();
        $this->db->where('estado', "1");
        $this->db->where('idparametro', $idparametro);
        $this->db->where('id_aux', $id_aux);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function obtener_valores_parametro_aux_2($id_aux, $idparametro) {
        $this->make_query_valor();
        $this->db->where('estado', "1");
        $this->db->where('idparametro', $idparametro);
        $this->db->where('id_aux', $id_aux);
        $query = $this->db->get();
        $row = $query->row();
        return $row->id;
    }

    public function traer_datos_cargo_departamento_id($id) {
        $this->db->select("*");
        $this->db->from("cargos_departamentos c");
        $this->db->where('c.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function traer_datos_cargo_audioviduales_id() {
        $this->db->select("c.id,c.id_cargo,c.id_departamento");
        $this->db->from("cargos_departamentos c");
        $this->db->join('valor_parametro p', 'p.id=c.id_cargo ');
        $this->db->where('p.id_aux', "ResAud");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function traer_datos_cargo_departamento_completo_id($id) {
        $this->db->select("c.id,c.id_cargo,c.id_departamento,p.id id_valor,p.id_aux");
        $this->db->from("cargos_departamentos c");
        $this->db->join('valor_parametro p', 'p.id=c.id_cargo ');
        $this->db->where('c.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function obtener_valores_parametro_valoy($idparametro, $valory) {
        $this->make_query_valor();
        $this->db->where('estado', "1");
        $this->db->where('idparametro', $idparametro);
        $this->db->where('valory', $valory);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function obtener_valores_parametro_valox($idparametro, $valorx) {
        $this->make_query_valor();
        $this->db->where('estado', "1");
        $this->db->where('idparametro', $idparametro);
        $this->db->where('valor', $valorx);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function obtener_datos_valor_parametro($id, $tipo) {
        $this->db->select("vp.id, vp.id_aux, vp.idparametro, vp.valor, vp.valorx, vp.valory, vp.valorz", false);
        $this->db->from('valor_parametro vp');
        if ($tipo == 1) {
            $this->db->where('vp.id', $id);
        } else {
            $this->db->where('vp.id_aux', $id);
        }
        $query = $this->db->get();
        return $query->row();
    }

    public function Listar_valor($idparametro,$estado,$mas = null) {
        $this->db->select("vp.id ,vp.valor,vp.valorx, vp.estado, vp.id_aux, vp.valory, vp.idparametro,re.valor relacion,re.valorx des_relacion, vp.valorz, vp.valora, vp.valorb");
        $this->db->from('valor_parametro vp');
        $this->db->join('valor_parametro re', 'vp.valory = re.id','left');
        $this->db->where('vp.idparametro', $idparametro);
        if (!is_null($mas)) {
            $this->db->or_where('vp.idparametro',$mas);
        }
        if (!is_null($estado)) {
            $this->db->where('vp.estado', $estado);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Listar_permisos_perfil($idperfil) {
        $this->db->select("a.agrega,a.modifica,a.elimina,u.valor id_actividad,a.id");
        $this->db->from("actividades_perfil a");
        $this->db->join('valor_parametro u', 'a.id_actividad=u.id_aux');
        $this->db->where('a.id_perfil', $idperfil);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Listar_Actividades_perfil($idperfil,$sw = null) {
		$where = is_null($sw) ? "pp.vp_principal IS NULL" : "pp.vp_principal = '$sw'";
        $this->db->select("ap.id_Actividad actividad,u.valory icono,u.valor nombre,u.id,ap.*, u.valora, u.valorb");
		$this->db->from("actividades_perfil ap");
		$this->db->join('permisos_parametros pp', 'pp.vp_secundario = ap.id_actividad', 'left');
        $this->db->join('valor_parametro u', 'ap.id_actividad = u.id_aux');
		$this->db->where('ap.id_perfil', $idperfil);
		$this->db->where($where);
        $this->db->order_by("ap.id", "");
        $query = $this->db->get();
        $actividades1 = $query->result_array();
        $actividades2 = $this->Listar_Actividades_persona($idperfil,$sw);
        return array_merge($actividades1, $actividades2);
    }
    public function Listar_Actividades_persona($idperfil,$sw = null) {
        $where = is_null($sw) ? "pp.vp_principal IS NULL" : "pp.vp_principal = '$sw'";
        $persona = $_SESSION['persona'];
        $this->db->select("ap.id_Actividad actividad,u.valory icono,u.valor nombre,u.id,ap.*, u.valora, u.valorb");
		$this->db->from("actividades_personas ap");
		$this->db->join('permisos_parametros pp', 'pp.vp_secundario = ap.id_actividad', 'left');
        $this->db->join('valor_parametro u', 'ap.id_actividad = u.id_aux');
		$this->db->where("ap.id_persona = $persona  AND ap.id_actividad NOT IN (SELECT ape.id_actividad FROM actividades_perfil ape WHERE ape.id_perfil = '$idperfil')");
		$this->db->where($where);
        $this->db->order_by("ap.id", "");
        $query = $this->db->get();
        return $query->result_array();
    }


    public function Listar_permisos_perfil_actividad($idperfil, $actividad) {
        $this->db->select("u.valor id_actividad,a.id,u.id_aux, u.valora, u.valorb");
        $this->db->from("actividades_perfil a");
        $this->db->join('valor_parametro u', 'a.id_actividad=u.id_aux');
        $this->db->where('a.id_perfil', $idperfil);
        $this->db->where('a.id_actividad', $actividad);
        $query = $this->db->get();
        $resp = $query->result_array();
        if (empty($resp)) {
            $resp = $this->Listar_permisos_actividad_persona($actividad,$_SESSION['persona']);
        }
        return $resp ;
    }
    public function Listar_permisos_actividad_persona($actividad, $persona) {
        $this->db->select("u.valor id_actividad,a.id,u.id_aux");
        $this->db->from("actividades_personas a");
        $this->db->join('valor_parametro u', 'a.id_actividad=u.id_aux');
        $this->db->where('a.id_persona', $persona);
        $this->db->where('a.id_actividad', $actividad);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Listar_cargos_departamento($iddepartamento, $general) {
        $this->db->select("cd.id,vp.valor,vpj.valor AS jefe, cd.estado");
        $this->db->from("cargos_departamentos cd");
		$this->db->join('valor_parametro vp', 'cd.id_cargo = vp.id');
		$this->db->join('cargos_departamentos cdj', 'cd.id_cargo_jefe = cdj.id', 'left');
		$this->db->join('valor_parametro vpj', 'cdj.id_cargo = vpj.id', 'left');
        $this->db->where('cd.id_departamento', $iddepartamento);
        if ($general == 1) {
            $this->db->where('cd.estado', "1");
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Agregar_permisos_perfil($idPerfil, $id_Actividad) {
        $this->db->insert($this->actividades_perfil, array(
            "id_perfil" => $idPerfil,
            "id_actividad" => $id_Actividad,
        ));
        return 2;
    }

    public function Agregar_cargos_departamento($idcargo, $iddepartamento) {
        $this->db->insert($this->table_cargos_departamentos, array(
            "id_cargo" => $idcargo,
            "id_departamento" => $iddepartamento,
        ));
        return 0;
    }

    function Eliminar_Actividad($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->actividades_perfil);
        return 4;
    }

    public function Existe_Nombre_Parametro($nombre) {
        $this->db->select('count(*) as cantidad');
        $this->db->from($this->table_parametro);
        $this->db->where('nombre', $nombre);
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

    public function Existe_Nombre_valor_Parametro($nombre, $idparametro) {
        $this->db->select('count(*) as cantidad');
        $this->db->from($this->table_valor_parametro);
        $this->db->where('valor', $nombre);
        $this->db->where('idparametro', $idparametro);
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

    public function Existe_Id_Aux($id_aux, $id = null) {
        $this->db->select('count(*) as cantidad');
        $this->db->from($this->table_valor_parametro);
        $this->db->where('id_aux', $id_aux);
        if ($id) $this->db->where("id <> $id");
        $this->db->where('estado', 1);
        $result = $this->db->get();
        $row = $result->row();
        return $row->cantidad == 0 ? false : true;
    }

    public function TieneActividad($perfil, $actividad) {
        $this->db->select('count(*) as cantidad');
        $this->db->from($this->actividades_perfil);
        $this->db->where('id_perfil', $perfil);
        $this->db->where('id_actividad', $actividad);
        $result = $this->db->get();
        $cantidad = $result->result_array();
        if ($cantidad[0]["cantidad"] == 0) {
            return false;
        } else {
            return true;
            ;
        }
    }

    public function guardar($nombre, $descripcion) {

        $this->db->insert($this->table_parametro, array(
            "nombre" => $nombre,
            "descripcion" => $descripcion,
        ));

        return 2;
    }

    public function agregar_valor_parametro($data) {
        $this->db->insert($this->table_valor_parametro, $data);
        $error = $this->db->_error_message();
        if ($error) {
          return -1;
        }
        return 2;
    }

    public function editar_valor_parametro($data, $id) {
        $this->db->where('id', $id);
        $this->db->update($this->table_valor_parametro, $data);
        $error = $this->db->_error_message(); 
        if ($error) {
          return -1;
        }
        return 1;
    }

    public function guardar_valor($valor, $valorx, $idparametro, $id_aux, $valory) {

        $this->db->insert($this->table_valor_parametro, array(
            "valor" => $valor,
            "valorx" => $valorx,
            "idparametro" => $idparametro,
            //"id_aux" => $id_aux,
            "valory" => $valory,
            "usuario_registra" => $_SESSION['persona'],
        ));

        return 2;
    }

    public function cambio_estado_parametro($id, $estado) {
        $this->db->set('estado', $estado);
        $this->db->where('id', $id);
        $this->db->update($this->table_valor_parametro);
        return 1;
    }

    public function cambiar_estado_cargo($id, $estado) {
        $this->db->set('estado', $estado);
        $this->db->where('id', $id);
        $this->db->update($this->table_cargos_departamentos);
        return 1;
    }

    public function Modificar_Valor_parametro($id, $valor, $valorx,$valory) {
        if (!is_null($valory)) $this->db->set('valory', $valory);
        $this->db->set('valor', $valor);
        $this->db->set('valorx', $valorx);
        $this->db->where('id', $id);
        $this->db->update($this->table_valor_parametro);
        return 1;
    }

    public function Listar_Actividades_Sin_Asignar_Perfil($perfil) {
        $this->db->select("v.valor,v.id_aux,a.id_actividad");
        $this->db->from("valor_parametro v");
        $this->db->join("actividades_perfil a", "a.id_actividad = v.id_aux  AND a.id_perfil='$perfil'", "left");
        $this->db->where('v.estado', "1");
        $this->db->where('v.idparametro', "18");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Listar_cargos_sin_Asignar_Departamento($iddepartamento) {

        $this->db->select("v.valor,v.id,a.id_cargo");
        $this->db->from("valor_parametro v");
        $this->db->join("cargos_departamentos a", "a.id_cargo = v.id  AND a.id_departamento='$iddepartamento'", "left");
        $this->db->where('v.estado', "1");
        $this->db->where('v.idparametro', "2");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Cambiar_estado_Permiso($id, $estado, $col) {

        $this->db->set("$col", $estado);
        $this->db->where('id', $id);
        $this->db->update($this->actividades_perfil);
        return 4;
    }

    public function Administra_estado_Permiso($id) {
        $this->db->set("elimina", "1");
        $this->db->set("modifica", "1");
        $this->db->set("agrega", "1");
        $this->db->where('id', $id);
        $this->db->update($this->actividades_perfil);
        return 4;
    }

    public function consultar_generica($where) {
        $this->make_query_valor();
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function guardar_datos($data, $tabla, $tipo = 1){   
      if ($tipo == 2) {
        $this->db->insert_batch($tabla, $data);
      }else{
        $this->db->insert($tabla,$data);
      }
      $error = $this->db->_error_message(); 
      if ($error) {
        return "error";
      }
      return 2;
    }
  
    function eliminar_datos($id, $tabla){
        $this->db->where('id', $id);
        $this->db->delete($tabla);
        $error = $this->db->_error_message(); 
        if ($error) {
          return "error";
        }
        return 2;
  }
    
    public function traer_ultima_registro_usuario($persona)
	{ 
		$this->db->select("id");
		$this->db->from("valor_parametro");
		$this->db->order_by("id", "desc");
		$this->db->where('usuario_registra', $persona);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row->id;
    }

    // treae los  valores  invocar funcion traer valor parametro
    public function traer_valores_permisos($idparametro,  $idvalorparametro){
        $this->db->select("vp.id,vp.id_aux,vp.valor,vp.valorx,pp.id id_permiso");
        $this->db->from("valor_parametro vp");
        $this->db->join("permisos_parametros pp", "pp.vp_secundario_id = vp.id AND pp.vp_principal_id =".$idvalorparametro, "left");
        $this->db->where('vp.estado', 1);
        $this->db->where('vp.idparametro', $idparametro, $idvalorparametro);
        $query = $this->db->get();
        return $query->result_array();           
    }
    // treae los  valores  invocar funcion traer valor parametro
    public function verificar_permiso($vp_principal_id,  $vp_secundario_id){
        $this->db->select("*");
        $this->db->from("permisos_parametros vp");
        $this->db->where('vp.vp_principal_id', $vp_principal_id);
        $this->db->where('vp.vp_secundario_id', $vp_secundario_id);
        $query = $this->db->get();
        return $query->result_array();           
    }
    
    //function traer_valores_permisos_2($idparametro, $idvalorparametro){
       // $this->db->where('vp.estado',1);
       // $this->db->delete('vp.idparametro',$idparametro, $idparametro); 
       // $query = $this->db->get();
       // return $query->result_array();              
     //}
     
    /* 
    public function habilitar($idparametro){         
    $this->db->join("valor_parametro","permisos_parametros",$id); 
       $this->db->where('estado',1);
       $this->db->where("id","id_permiso");
       $query = $this->db->get();
        
    }
    function desabilitar($idparametro){   
       $this->db->where("valor_parametro",$idparametro);
       $this->db->delete("id_permiso");
       $query = $this->db->get(); 
      
    }
*/
    public function buscar_parametro($nombre_p){
        $this->db->select('p.nombre, p.id');
        $this->db->from('parametros p');
        $this->db->like('p.nombre',$nombre_p);
        $this->db->or_like('p.id',$nombre_p);
        $query = $this->db->get();
        return $query->result_array(); 
    }
}
