<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class supervisor_model extends CI_Model
{

  /**
	 * Se encarga de guardar los datos que se le pasen por el controlador en la tabla indicada.
	 * @param Array $data 
	 * @param String $tabla 
	 * @return Int
	 */
	public function guardar_datos($data, $tabla, $tipo = 1){
		$tipo == 2 ? $this->db->insert_batch($tabla, $data) : $this->db->insert($tabla,$data);
		$error = $this->db->_error_message(); 
		return $error ? 1 :  0;
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
  /* */
  public function traer_registro_id($person, $tabla, $usuario){
		$this->db->select("*");
		$this->db->from($tabla);
		$this->db->order_by("id", "desc");
		$this->db->where($usuario, $person);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}
  /* Funcion para obtener los supervisores */
  public function estadoSupervisor($perfil, $dia)
  {
    $this->db->select("st.*,tss.hora_entrada,tss.hora_salida, p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre, p.identificacion, p.correo", false);
    $this->db->from('supervisor_turnos st');
    $this->db->join('personas p', 'st.id_persona = p.id');
    $this->db->join('turnos_supervisor_sala tss', 'st.id_turno = tss.id');
    $this->db->join('valor_parametro vp', 'tss.id_dia = vp.id');  
    $this->db->where("vp.valory",$dia);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function filtrar_supervisor()
  {
    $this->db->select("p.id as id_persona,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre, p.identificacion, p.correo", false);
    $this->db->from('personas p');
    $this->db->where("p.id_perfil = 'Per_Sup'");
    $query = $this->db->get();
    return $query->result_array();
  }

    /* Funcion para obtener los supervisores */
    public function obtenerSupervisores($perfil)
    {
      $this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre, p.identificacion, p.correo", false);
      $this->db->from('personas p');
      $this->db->where("p.id_perfil",$perfil);
      $query = $this->db->get();
      return $query->result_array();
    }

  /* Funcion para obtener la sala */
  public function obtenerSalas($idparametro)
  {
    $this->db->select("vp.id,vp.id_aux,vp.valorx, vp.valor nombre");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.idparametro", $idparametro);
    $query = $this->db->get();
    return $query->result_array();
  }

  /* Funcion para buscar parametros */
  public function buscarParametro($valorb)
  {
    $this->db->select("vp.idparametro");
    $this->db->from("valor_parametro vp");
    $this->db->where("vp.valorb", $valorb);
    $query = $this->db->get();
    return $query->row();
  }

    /* Funcion para buscar parametros */
    public function buscarid($valor)
    {
      $this->db->select("vp.id");
      $this->db->from("valor_parametro vp");
      $this->db->where("vp.id_aux", $valor);
      $query = $this->db->get();
      return $query->row();
    }

  public function traer_ultima_solicitud_hoy($person, $usuario, $fecha){
    $this->db->select("ss.*, vp.valor as estado_nombre");
    $this->db->from('supervisor_solicitud ss');
    $this->db->join('valor_parametro vp', 'ss.id_estado_proceso = vp.id_aux');
    $this->db->order_by("id", "desc");
    $this->db->where($usuario, $person);
    $this->db->like('fecha_registro', $fecha);
    $this->db->limit(1);
    $query = $this->db->get();
    $row = $query->row();
    return $row;
    }

    public function traer_ultima_solicitud($person, $tabla, $usuario){
      $this->db->select("*");
      $this->db->from($tabla);
      $this->db->order_by("id", "desc");
      $this->db->where($usuario, $person);
      $this->db->limit(1);
      $query = $this->db->get();
      $row = $query->row();
      return $row;
      }

    public function ValidarPosicion($id){
      $this->db->select("st.*, vp.valor as proceso");
      $this->db->from("supervisor_solicitud_estados st");
      $this->db->join('valor_parametro vp', 'st.id_estado = vp.id_aux');
      $this->db->where("st.estado",1);
      $this->db->where("st.id_solicitud",$id);
      $this->db->order_by("st.fecha_registro", "asc");
      $query = $this->db->get();
      return $query->result_array();
      }

  public function listar_spv($tabla)
  {
    $this->db->select("t.*",false);
    $this->db->from("$tabla t");
    $this->db->where("t.estado",1);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function listar_turnos_spv(){
		$this->db->select("hf.*, vp.valor as dia");
		$this->db->from("turnos_supervisor_sala hf");
		$this->db->join('valor_parametro vp', 'hf.id_dia = vp.id');
		$this->db->where("hf.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	}

  public function traer_novedades($solicitud, $tipo){
		$this->db->select("sn.*, vp.valor as sala");
		$this->db->from("supervisor_solicitud_novedades sn");
    $this->db->join('valor_parametro vp', 'sn.id_sala = vp.id');
		$this->db->where("sn.id_solicitud",$solicitud);
    $this->db->where("sn.tipo",$tipo);
    $query = $this->db->get();
    return $query->result_array();
	}

  public function SalasSupervisor($id){
		$this->db->select("ss.*, vp.id id_sala,vp.id_aux,vp.valorx descripcion, vp.valor nombre");
		$this->db->from("supervisor_salas ss");
		$this->db->join('valor_parametro vp', 'ss.id_sala = vp.id');
		$this->db->where("ss.estado",1);
    $this->db->where("ss.id_persona",$id);
        $query = $this->db->get();
        return $query->result_array();
	}

  public function TurnosSupervisor($id){
		$this->db->select("st.*,tsp.*, vp.valor as dia");
		$this->db->from("supervisor_turnos st");
		$this->db->join('turnos_supervisor_sala tsp', 'st.id_turno = tsp.id');
    $this->db->join('valor_parametro vp', 'tsp.id_dia = vp.id');
		$this->db->where("st.estado",1);
    $this->db->where("st.id_persona",$id);
        $query = $this->db->get();
        return $query->result_array();

        $this->db->select("hf.*, vp.valor as dia");
		$this->db->from("turnos_supervisor_sala hf");
		$this->db->join('valor_parametro vp', 'hf.id_dia = vp.id');
		$this->db->where("hf.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	}

  
  public function delete_spv($id,$tabla)
  {
    $this->db->where('id',$id);
    $this->db->delete($tabla);
    $error = $this->db->_error_message();
    if ($error) {
      return "error";
    }
    return 0;
  }

  
  public function exist_spv($id,$tabla)
  {
    $this->db->select("*");
    $this->db->from($tabla);
    $this->db->where("id",$id);
    $query = $this->db->get();
    return $query->result_array();
  }

  public function validar_turno_supervisor($id_turno,$id_persona){
		$this->db->select("fh.id",FALSE);
		$this->db->from("supervisor_turnos fh");
		$this->db->where("fh.id_turno = $id_turno AND fh.id_persona = $id_persona");
		$this->db->where("fh.estado",1);
        $query = $this->db->get();
        return $query->row();
	}

  public function ValidarNombre($idparametro,$valor){
		$this->db->select("vp.id",FALSE);
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.valor", $valor);
    $this->db->where("vp.idparametro", $idparametro);
		$this->db->where("vp.estado",1);
        $query = $this->db->get();
        return $query->row();
	}

  public function validar_sala_supervisor($sala,$id_persona){
		$this->db->select("fh.id",FALSE);
		$this->db->from("supervisor_salas fh");
		$this->db->where("fh.id_persona = $id_persona AND fh.id_sala = $sala");
		$this->db->where("fh.estado",1);
        $query = $this->db->get();
        return $query->row();
	}


  // Funciones para las vistas del supervisor normal
  public function ultimo_registro_dia($id_persona){
		$this->db->select("s.*, vp.valor tipo, vp.id_aux cod, vp.valory posicion",false);
		$this->db->from('supervisor_registros s');
		$this->db->join('valor_parametro vp', 's.tipo = vp.id_aux', 'left');
		$this->db->where('s.supervisor', $id_persona);
    $this->db->where("DATE_FORMAT(s.fecha_registro,'%Y-%m-%d') = CURDATE()");
		$this->db->where("fecha_registro=(SELECT MAX(fecha_registro) from supervisor_registros)");
		$query = $this->db->get();
		return $query->result_array();
	}

  public function DatosSupervisor($id_persona = null){
		$this->db->select("p.*, p.identificacion id_persona, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) nombre_completo, vp.valor cargo",false);
		$this->db->from('personas p');
		$this->db->join('valor_parametro vp', 'p.id_cargo_sap = vp.id', 'left');
	  $this->db->where('p.id', $id_persona);
		$query = $this->db->get();
		return $query->result_array();
	}

  public function Novedades($id_solicitud){
    $this->db->select("COUNT(id_sala) as novedades, id_sala");
    $this->db->from("supervisor_solicitud_novedades");
    $this->db->where("id_solicitud", $id_solicitud);
    $this->db->group_by("id_sala");
    $query = $this->db->get();
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

  public function VerificarTurno($persona, $dia){
		$this->db->select("st.*, vp.*");
		$this->db->from("supervisor_turnos st");
		$this->db->join('turnos_supervisor_sala tss', 'st.id_turno = tss.id');
    $this->db->join('valor_parametro vp', 'tss.id_dia = vp.id');
		$this->db->where("st.estado",1);
    $this->db->where("st.id_persona", $persona);
    $this->db->where("vp.valory", $dia);
        $query = $this->db->get();
        return $query->result_array();
	}

}
