<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class salud_model extends CI_Model{

	public function guardar_datos($data, $tabla, $tipo = 1){
		$tipo == 2 ? $this->db->insert_batch($tabla, $data) : $this->db->insert($tabla,$data);
		$error = $this->db->_error_message(); 
		return $error ? 1 :  0;
	}

	
	public function modificar_datos($data, $tabla, $id){
		$this->db->where('id', $id);
		$this->db->update($tabla, $data);
		$error = $this->db->_error_message();
		return $error ? 1 :  0;
	}
	public function modificar_datoscv($data, $tabla, $id){
		$this->db->where('id_solicitud', $id);
		$this->db->update($tabla, $data);
		$error = $this->db->_error_message();
		return $error ? 1 :  0;
	}

	public function listar_permisos_funcionario($admin,$persona,$id_parametro){
        $this->db->select("vp.*");
		$this->db->from('valor_parametro vp');
		if(!$admin){
			$this->db->join('salud_profesional_relacion f', 'f.id_relacion = vp.id','left');
			$this->db->where("f.id_persona = $persona and f.estado=1");
		}
		$this->db->where("vp.idparametro = $id_parametro and vp.estado = 1");
        $query = $this->db->get();
        return $query->result_array();
	}
	
    public function listar_valor_parametro($id_parametro) {
		$this->db->select("vp.id,vp.valor,vp.valorx,vp.valory,vp.idparametro,vp.id_aux");
		$this->db->from('valor_parametro vp');
		$this->db->where("vp.idparametro = $id_parametro AND vp.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function listar_observaciones($solicitud){
        $this->db->select("so.*, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) profesional",false);
		$this->db->from('salud_observaciones so');
		$this->db->join('personas p', 'so.usuario_registra = p.id', 'left');
		$this->db->where("so.id_solicitud = $solicitud");
		$this->db->order_by("fecha_registro", "asc");
        $query = $this->db->get();
        return $query->result_array();
	}

	public function valor_parametro_id_aux($id_aux, $id_parametro=185) {
		$this->db->select("vp.*");
		$this->db->from('valor_parametro vp');
		$this->db->where('vp.id_aux',$id_aux);
		$this->db->where("vp.estado = 1 AND vp.idparametro = $id_parametro");
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function listar_profesional_servicio($id_relacion) {
		$this->db->select("bt.id_persona as id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) valor, p.identificacion, bt.id as idrelacion", FALSE);
		$this->db->from("salud_profesional_relacion bt");
		$this->db->join('personas p', 'bt.id_persona = p.id');
		$this->db->where('bt.id_relacion', $id_relacion);
		$this->db->where("bt.estado",1);
		$this->db->order_by('p.nombre,p.apellido,p.segundo_apellido');
        $query = $this->db->get();
        return $query->result_array(); 
	}

	public function listar_atenciones($tipo_solicitud,$estado,$servicio,$tipo_persona,$fecha,$fecha_2){
		$perfil = $_SESSION['perfil'];
		$id_persona = $_SESSION['persona'];
		$administra = $perfil == 'Per_Admin' || $perfil == 'Per_salud'  ? true : false;
		$this->db->select("sa.*, sa.fecha_registra as fecha_solicitud,id_tipo_solicitud,  CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) profesional,motivo_consulta,proto.act_protocolos,proto.id_solicitud,proto.id as id_covid,proto.tipo_reporte, vpts.valor as tipo_solicitud, es.valor as estado, val.valor as valoracion_examen,observacion_mod, tp.valor as tipopersona",false);
		$this->db->select("IF(sa.tipo_solicitante = 1,(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM personas pa WHERE pa.id = sa.id_persona),(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM visitantes pa WHERE pa.id = sa.id_persona)) AS nombre_completo",false);
		$this->db->from("salud_solicitudes sa");
		$this->db->join('personas p', 'sa.id_profesional = p.id', 'left');
		$this->db->join('salud_protocolo_covid proto', 'sa.id = proto.id_solicitud','left');
		$this->db->join('valor_parametro vpts', 'sa.id_tipo_solicitud = vpts.id_aux');
		$this->db->join('valor_parametro es', 'sa.id_estado_sol = es.id_aux');
		$this->db->join('valor_parametro val', 'sa.valoracion = val.id','left');
		$this->db->join('valor_parametro tp', 'sa.tipo_persona_sol = tp.id_aux','left');
		$this->db->where('sa.estado', 1);
		if($tipo_solicitud){
			 $this->db->where('sa.id_tipo_solicitud',$tipo_solicitud);
		}else{
			if(!$administra){
				$this->db->join('salud_profesional_relacion f', 'f.id_relacion = vpts.id','left');
				$this->db->where("f.id_persona = $id_persona and f.estado=1");
			}
		}
		if($estado) $this->db->where('sa.id_estado_sol',$estado);
		if($servicio) $this->db->where('sa.id_servicio',$servicio);
		if($tipo_persona) $this->db->where('sa.tipo_persona_sol',$tipo_persona);
		if(!empty($fecha) || !empty($fecha_2)) $this->db->where("(DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') >= '$fecha' AND DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') <= '$fecha_2')");
		else $this->db->where("DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') = CURDATE()");
		$query = $this->db->get();
        return $query->result_array();
	}

	public function listar_atenciones_excel($tipo_solicitud,$servicio,$estado,$tipo_persona,$fecha,$fecha2,$id){
		$perfil = $_SESSION['perfil'];
		$id_persona = $_SESSION['persona'];
		$administra = $perfil == 'Per_Admin' || $perfil == 'Per_salud'  ? true : false;
		$this->db->select("sa.*, sa.fecha_registra as fecha_solicitud, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) profesional, vp.valor as servicio, vpts.valor as tipo_solicitud, es.valor as estado, val.valor as valoracion_examen",false);
		$this->db->select("IF(sa.tipo_solicitante = 1,(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM personas pa WHERE pa.id = sa.id_persona),(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM visitantes pa WHERE pa.id = sa.id_persona)) AS nombre_completo",false);
		$this->db->select("IF(sa.tipo_solicitante = 1,(SELECT DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(fecha_nacimiento)), '%Y')+0 FROM personas pa WHERE sa.id_persona = pa.id),(SELECT DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(fecha_nacimiento)), '%Y')+0 FROM visitantes pa WHERE sa.id_persona = pa.id)) AS edad",false);
        $this->db->select("IF(sa.tipo_solicitante = 1,(SELECT pa.identificacion FROM personas pa WHERE sa.id_persona = pa.id),(SELECT pa.identificacion FROM visitantes pa WHERE sa.id_persona = pa.id)) AS identificacion",false);
        $this->db->select("IF(sa.tipo_solicitante = 1,(SELECT gn.valor genero FROM personas pa LEFT JOIN valor_parametro gn ON gn.id = pa.genero WHERE sa.id_persona = pa.id),(SELECT gn.valor genero FROM visitantes pa LEFT JOIN valor_parametro gn ON gn.id = pa.genero WHERE sa.id_persona = pa.id)) AS genero",false);
		$this->db->select("IF(sa.tipo_solicitante = 1,(SELECT vpd.valor FROM personas pa LEFT JOIN cargos_departamentos c ON pa.id_cargo=c.id LEFT JOIN valor_parametro vpd ON c.id_departamento=vpd.id WHERE sa.id_persona = pa.id),(SELECT vpd.valor FROM visitantes pa LEFT JOIN valor_parametro vpd ON pa.id_programa = vpd.id WHERE sa.id_persona = pa.id)) AS dependencia",false);
		$this->db->from("salud_solicitudes sa");
		$this->db->join('personas p', 'sa.id_profesional = p.id', 'left');
		$this->db->join('valor_parametro vp', 'sa.id_servicio = vp.id', 'left');
		$this->db->join('valor_parametro vpts', 'sa.id_tipo_solicitud = vpts.id_aux', 'left');
		$this->db->join('valor_parametro es', 'sa.id_estado_sol = es.id_aux', 'left');
		$this->db->join('valor_parametro val', 'sa.valoracion = val.id','left');
		$this->db->where('sa.estado', 1);
		if($tipo_solicitud){
			$this->db->where('sa.id_tipo_solicitud',$tipo_solicitud);
	   }else{
		   if(!$administra){
			   $this->db->join('salud_profesional_relacion f', 'f.id_relacion = vpts.id','left');
			   $this->db->where("f.id_persona = $id_persona and f.estado=1");
		   }
	   }
		if($estado != 0) $this->db->where('sa.id_estado_sol',$estado);
		if($servicio != 0) $this->db->where('sa.id_servicio',$servicio);
		if($tipo_persona) $this->db->where('sa.tipo_persona_sol',$tipo_persona);
		if(($fecha != 0) || ($fecha2 != 0)) $this->db->where("(DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') >= '$fecha' AND DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') <= '$fecha2')");
		else $this->db->where("DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') = CURDATE()");
		if($id != 0) $this->db->where('sa.id',$id);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function listar_protocolo_excel($tipo_solicitud,$servicio,$estado,$tipo_persona,$fecha,$fecha2,$id){
		$perfil = $_SESSION['perfil'];
		$id_persona = $_SESSION['persona'];
		$administra = $perfil == 'Per_Admin' || $perfil == 'Per_salud'  ? true : false;
		$this->db->select("sa.*, sa.fecha_registra as fecha_solicitud, CONCAT(p.nombre,' ',p.apellido, ' ',p.segundo_apellido) profesional,vptd.valor as tipo_identificacion,vptpr.valor as tipo_reporte,vpesi.valor as estado_inicial,vpesf.valor as estado_final,vpmdr.valor as med_reporte,vpmtr.valor as mot_reporte,vpsub.valor as subclasificacion,vpeps.valor as eps,slp.barrio as barrio, slp.fecha_sintomas as fecha_sintomas,slp.sintomas as sintomas, slp.act_protocolos as act_protocolos, vp.valor as servicio, vpts.valor as tipo_solicitud, es.valor as estado, val.valor as valoracion_examen",false);
		$this->db->select("IF(sa.tipo_solicitante = 1,(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM personas pa WHERE pa.id = sa.id_persona),(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM visitantes pa WHERE pa.id = sa.id_persona)) AS nombre_completo",false);
		$this->db->select("IF(sa.tipo_solicitante = 1,(SELECT pa.identificacion FROM personas pa WHERE sa.id_persona = pa.id),(SELECT pa.identificacion FROM visitantes pa WHERE sa.id_persona = pa.id)) AS identificacion",false);
		$this->db->select("IF(sa.tipo_solicitante = 1,(SELECT c.valor cargo FROM personas pa LEFT JOIN valor_parametro c ON pa.id_cargo_sap=c.id WHERE sa.id_persona = pa.id),(SELECT c.valor cargo FROM visitantes pa LEFT JOIN valor_parametro c ON pa.id_programa =c.id WHERE sa.id_persona = pa.id)) AS cargo",false);
		$this->db->select("IF(sa.tipo_solicitante = 1,(SELECT vpd.valor FROM personas pa LEFT JOIN cargos_departamentos c ON pa.id_cargo=c.id LEFT JOIN valor_parametro vpd ON c.id_departamento=vpd.id WHERE sa.id_persona = pa.id),(SELECT vpd.valor FROM personas pa LEFT JOIN cargos_departamentos c ON pa.id_cargo=c.id LEFT JOIN valor_parametro vpd ON c.id_departamento=vpd.id WHERE sa.id_persona = pa.id)) AS dependencia",false);
		$this->db->from("salud_solicitudes sa");
		$this->db->join('personas p', 'sa.id_profesional = p.id', 'left');
		$this->db->join('visitantes vs', 'sa.id_persona = vs.id', 'left');
		$this->db->join('salud_protocolo_covid slp', 'sa.id = slp.id_solicitud', 'left');
		$this->db->join('valor_parametro vptd', 'vptd.id= vs.tipo_identificacion', 'left');
		$this->db->join('valor_parametro vpeps', 'vpeps.id= slp.eps', 'left');
		$this->db->join('valor_parametro vpsub', 'vpsub.id= slp.subclasificacion', 'left');
		$this->db->join('valor_parametro vpmdr', 'vpmdr.id= slp.med_reporte', 'left');
		$this->db->join('valor_parametro vpmtr', 'vpmtr.id= slp.mot_reporte', 'left');
		$this->db->join('valor_parametro vpesi', 'vpesi.id= slp.estado_inicial', 'left');
		$this->db->join('valor_parametro vpesf', 'vpesf.id= slp.estado_final', 'left');
		$this->db->join('valor_parametro vptpr', 'vptpr.id= slp.tipo_reporte', 'left');
		$this->db->join('valor_parametro vp', 'sa.id_servicio = vp.id', 'left', 'left');
		$this->db->join('valor_parametro vpts', 'sa.id_tipo_solicitud = vpts.id_aux', 'left');
		$this->db->join('valor_parametro es', 'sa.id_estado_sol = es.id_aux', 'left');
		$this->db->join('valor_parametro val', 'sa.valoracion = val.id','left');
		if($tipo_solicitud){
			$this->db->where('sa.id_tipo_solicitud',$tipo_solicitud);
	   }else{
		   if(!$administra){
			   $this->db->join('salud_profesional_relacion f', 'f.id_relacion = vpts.id','left');
			   $this->db->where("f.id_persona = $id_persona and f.estado=1");
		   }
	   }
		if($estado != 0) $this->db->where('sa.id_estado_sol',$estado);
		if($servicio != 0) $this->db->where('sa.id_servicio',$servicio);
		if($tipo_persona) $this->db->where('sa.tipo_persona_sol',$tipo_persona);
		if(($fecha != 0) || ($fecha2 != 0)) $this->db->where("(DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') >= '$fecha' AND DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') <= '$fecha2')");
		else $this->db->where("DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') = CURDATE()");
		if($id != 0) $this->db->where('sa.id',$id);
		$query = $this->db->get();
        return $query->result_array();
	}
	

	public function buscar_persona($tabla, $dato){
		$this->db->select("t.*,identificacion,CONCAT(nombre,' ',apellido,' ',segundo_apellido) as nombre_completo, DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(fecha_nacimiento)), '%Y')+0 AS edad, vp.valor as dependencia", false);
		$this->db->from("$tabla t");
		if($tabla == 'personas'){
			$this->db->join('cargos_departamentos c', 't.id_cargo=c.id','left');
			$this->db->join('valor_parametro vp', 'c.id_departamento=vp.id','left');
		}else{
			$this->db->join('valor_parametro vp', 't.id_programa = vp.id', 'left');
		}
		$this->db->where("(CONCAT(nombre,' ',apellido,' ',segundo_apellido) LIKE '%" . $dato . "%' OR identificacion LIKE '%" . $dato . "%') AND t.estado=1");
		$this->db->order_by('nombre,apellido,segundo_apellido');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function consulta_servicios_profesional($id_relacion, $id_persona){
		$this->db->select("bt.*");
		$this->db->from('salud_profesional_relacion bt');
		$this->db->where('bt.id_persona',$id_persona);
		$this->db->where('bt.id_relacion',$id_relacion);
		$this->db->where("bt.estado",1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}

	public function buscar_paciente($id_persona, $tipo_solicitante){
        $this->db->select("t.*,CONCAT(t.nombre,' ',t.apellido,' ',t.segundo_apellido) as nombre_completo, DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(fecha_nacimiento)), '%Y')+0 AS edad, vp.valor as dependencia, g.valor n_genero, g.valory genero_y", false);
		if($tipo_solicitante == 1){
			$this->db->from('personas as t');
			$this->db->join('cargos_departamentos c', 't.id_cargo=c.id','left');
			$this->db->join('valor_parametro vp', 'c.id_departamento=vp.id','left');
		}else{
			$this->db->from('visitantes as t');
			$this->db->join('valor_parametro vp', 't.id_programa = vp.id', 'left');
		}		
		$this->db->join('valor_parametro g', 't.genero = g.id', 'left');
        $this->db->where('t.id', $id_persona);
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

	  public function validar_ultima_atencion($person, $tipo_solicitud){
		$this->db->select("*");
		$this->db->from('salud_solicitudes');
		$this->db->order_by("id", "desc");
		$this->db->where('id_persona', $person);
		$this->db->where('id_tipo_solicitud', $tipo_solicitud);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	  }

	  public function validar_dato_paciente($valor_col, $tabla, $col, $col2, $usuario){
		$this->db->select("*");
		$this->db->from($tabla);
		$this->db->where($col, $valor_col);
		$this->db->where($col2, $usuario);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	  }

	  public function listar_escolaridad($id_persona){
		$this->db->select("ep.*,vp.valor,vpx.valor as valorx", FALSE);
		$this->db->from("salud_escolaridad_paciente ep");
		$this->db->join('valor_parametro vp', 'vp.id = ep.id_escolaridad');
		$this->db->join('valor_parametro vpx', 'vpx.id = ep.id_tipo_estado');
		$this->db->where('vp.idparametro', 151);
		$this->db->where('vpx.idparametro', 166);
		$this->db->where('ep.id_persona', $id_persona);
		$this->db->where("ep.estado",1);
        $query = $this->db->get();
        return $query->result_array(); 
	  }

	  public function listar_historia_laboral($id_persona){
		$this->db->select("hl.*, hl.cargo as valor, hl.proteccion as valorx, hl.tiempo as valory, hl.cantidad as valorz", FALSE);
		$this->db->from("salud_historia_laboral hl");
		$this->db->where('hl.id_persona', $id_persona);
		$this->db->where("hl.estado",1);
        $query = $this->db->get();
        return $query->result_array(); 
	  }

	  public function ver_riesgo_laboral ($id){
		$this->db->select("rl.*,vp.valor", FALSE);
		$this->db->from("salud_riesgo_laboral rl");
		$this->db->join('valor_parametro vp', 'vp.id = rl.id_riesgo');
		$this->db->where('rl.id_historia_laboral', $id);
		$this->db->where("rl.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function listar_accidentes($id_persona){
		$this->db->select("al.*, hl.empresa, al.dias_incapacidad as valor, al.lesion as valorx, al.	arp as valory, al.enfermedad_profesional as valorz", FALSE);
		$this->db->from("salud_accidentes_laborales al");
		$this->db->join('salud_historia_laboral hl', 'hl.id = al.id_historia_laboral');
		$this->db->where('al.id_persona', $id_persona);
		$this->db->where("al.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function cargar_empresas($id_persona){
		$this->db->select("hl.id, hl.empresa as valor");
		$this->db->from("salud_historia_laboral hl");
		$this->db->where('hl.id_persona', $id_persona);
        $query = $this->db->get();
        return $query->result_array(); 
	  }
	
	  public function listar_antfamiliar($id_persona){
		$this->db->select("af.*,af.observacion as valorz,vp.valor, vpx.valor as valorx", FALSE);
		$this->db->from("salud_antecedentes_familiares af");
		$this->db->join('valor_parametro vp', 'vp.id = af.id_tipo_enfermedad');
		$this->db->join('valor_parametro vpx', 'vpx.id = af.id_parentesco');
		$this->db->where('vp.idparametro', 154);
		$this->db->where('vpx.idparametro', 155);
		$this->db->where('af.id_persona', $id_persona);
		$this->db->where("af.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function listar_antpersonal($id_persona){
		$this->db->select("ap.*,vp.valor, ap.observacion as valorx", FALSE);
		$this->db->from("salud_antecedentes_personales ap");
		$this->db->join('valor_parametro vp', 'vp.id = ap.id_tipo_antecedente');
		$this->db->where('vp.idparametro', 156);
		$this->db->where('ap.id_persona', $id_persona);
		$this->db->where("ap.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function listar_vacunas($id_persona){
		$this->db->select("v.*,vp.valor, v.observacion as valorx", FALSE);
		$this->db->from("salud_vacuna_paciente v");
		$this->db->join('valor_parametro vp', 'vp.id = v.id_vacuna');
		$this->db->where('vp.idparametro', 157);
		$this->db->where('v.id_persona', $id_persona);
		$this->db->where("v.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function listar_ant_gineco($id_persona){
		$this->db->select("ag.*,vp.valor as tipo, ag.observacion as observacion_gineco", FALSE);
		$this->db->from("salud_antecedentes_gineco ag");
		$this->db->join('valor_parametro vp', 'vp.id = ag.id_tipo_planificacion','left');
		$this->db->where('vp.idparametro', 158);
		$this->db->where('ag.id_persona', $id_persona);
		$this->db->where("ag.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function listar_habitos($id_persona){
		$this->db->select("h.*,h.tipo as valory, vp.valor, vpf.valor as valorx, YEAR(h.fecha_hasta)-YEAR(h.fecha_desde) as valorz", FALSE);
		$this->db->from("salud_habitos_paciente h");
		$this->db->join('valor_parametro vp', 'vp.id_aux = h.id_habito');
		$this->db->join('valor_parametro vpf', 'vpf.id = h.id_frecuencia');
		$this->db->where('vp.idparametro', 165);
		$this->db->where('vpf.idparametro', 159);
		$this->db->where('h.id_persona', $id_persona);
		$this->db->where("h.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function listar_revision_sistema($id_solicitud){
		$this->db->select("rs.*, vp.valor, rs.observacion as valorx", FALSE);
		$this->db->from("salud_revision_sistema as rs");
		$this->db->join('valor_parametro vp', "vp.id = rs.id_tipo_sistema");
		$this->db->where('vp.idparametro',160);
		$this->db->where('rs.id_solicitud', $id_solicitud);
		$this->db->where("rs.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function listar_examen_fisico($id_solicitud){
		$this->db->select("ef.*, vp.valor, vpx.valor as valorx, ef.observacion as valorz", FALSE);
		$this->db->from("salud_examen_fisico as ef");
		$this->db->join('valor_parametro vp', "vp.id = ef.id_tipo_examen");
		$this->db->join('valor_parametro vpx', "vpx.id = ef.id_tipo_estado");
		$this->db->where('vp.idparametro',161);
		$this->db->where('vpx.idparametro',164);
		$this->db->where('ef.id_solicitud', $id_solicitud);
		$this->db->where("ef.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function listar_resultado_examenes($id_solicitud){
		$this->db->select("ep.*, vp.valor, vpx.valor as valorx, ep.observacion as valorz", FALSE);
		$this->db->from("salud_examenes_paraclinicos as ep");
		$this->db->join('valor_parametro vp', "vp.id = ep.id_tipo_examen_par");
		$this->db->join('valor_parametro vpx', "vpx.id = ep.id_estado_examen");
		$this->db->where('vp.idparametro',162);
		$this->db->where('vpx.idparametro',164);
		$this->db->where('ep.id_solicitud', $id_solicitud);
		$this->db->where("ep.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function listar_diagnosticos($id_solicitud){
		$this->db->select("dp.*, vp.valorx, vp.valor", FALSE);
		$this->db->from("salud_diagnosticos_paciente as dp");
		$this->db->join('valor_parametro vp', "vp.id = dp.id_diagnostico");
		$this->db->where('vp.idparametro',163);
		$this->db->where('dp.id_solicitud', $id_solicitud);
		$this->db->where("dp.estado",1);
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function buscar_diagnostico($dato,$id_solicitud){
		$this->db->select("vp.*,vp.valorx as descripcion, vp.valor as codigo", FALSE);
		$this->db->from("valor_parametro as vp");
		$this->db->where('vp.idparametro',163);
		$this->db->where("(valor LIKE '%" . $dato . "%' OR valorx LIKE '%" . $dato . "%') AND vp.estado=1");
		if($id_solicitud != '') $this->db->where("vp.valor NOT IN (SELECT id_diagnostico FROM salud_diagnosticos_paciente WHERE id_solicitud=$id_solicitud)"); 
		$this->db->order_by('vp.valor');
        $query = $this->db->get();
        return $query->result_array();
	  }

	  public function eliminar_datos($id, $tabla){
		$this->db->where('id', $id);
        $this->db->delete($tabla);
        $error = $this->db->_error_message(); 
        return $error ? 1 :  0;
	  }

	  public function modificar_editando($data, $id_persona,$tipo_solicitud){
		$this->db->where('id_persona', $id_persona);
		$this->db->where('id_tipo_solicitud', $tipo_solicitud);
		$this->db->update('salud_solicitudes', $data);
		$error = $this->db->_error_message();
		return $error ? 1 :  0;
	}

	public function listar_historial_ocupacional($id_persona){
		$this->db->select("sa.*, vp.valor as tipo_examen, vpv.valor as valoracion_examen, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as profesional",false);
		$this->db->from("salud_solicitudes sa");
		$this->db->join('valor_parametro vp', 'sa.id_servicio = vp.id');
		$this->db->join('valor_parametro vpv', 'sa.valoracion = vpv.id', 'left');
		$this->db->join('personas p', 'sa.id_profesional = p.id');
		$this->db->where('sa.id_tipo_solicitud', 'Sal_His_Ocup');
		$this->db->where('sa.estado', 1);
		$this->db->where('sa.id_persona',$id_persona);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function listar_historial_mgeneral($id_persona){
		$this->db->select("sa.*, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as profesional",false);
		$this->db->from("salud_solicitudes sa");
		$this->db->join('personas p', 'sa.id_profesional = p.id');
		$this->db->where('sa.id_tipo_solicitud', 'Sal_His_Med_Gen');
		$this->db->where('sa.estado', 1);
		$this->db->where('sa.id_persona',$id_persona);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function filtrar_pacientes($habito,$antecedente,$diagnostico,$fecha_inicio,$fecha_fin){
		$perfil = $_SESSION['perfil'];
		$id_persona = $_SESSION['persona'];
		$administra = $perfil == 'Per_Admin' || $perfil == 'Per_salud'  ? true : false;
		$this->db->select("sa.*, vpts.valor as tipo_examen",false);
		$this->db->from("salud_solicitudes sa");		
		$this->db->join('valor_parametro vpts', 'sa.id_servicio = vpts.id','left');
		$this->db->select("IF(sa.tipo_solicitante = 1,(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM personas pa WHERE pa.id = sa.id_persona),(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM visitantes pa WHERE pa.id = sa.id_persona)) AS nombre_completo",false);
		$this->db->select("IF(sa.tipo_solicitante = 1,(SELECT vpd.valor FROM personas pa LEFT JOIN cargos_departamentos c ON pa.id_cargo=c.id LEFT JOIN valor_parametro vpd ON c.id_departamento=vpd.id WHERE sa.id_persona = pa.id),(SELECT vpd.valor FROM visitantes pa LEFT JOIN valor_parametro vpd ON pa.id_programa = vpd.id WHERE sa.id_persona = pa.id)) AS dependencia",false);
	   
		if($habito != ''){
			$this->db->join('salud_habitos_paciente ha', 'sa.id_persona = ha.id_persona');
			$this->db->where('ha.id_habito',$habito);
		}
		if($antecedente != ''){
			$this->db->join('salud_antecedentes_personales ap', 'sa.id_persona = ap.id_persona');
			$this->db->where('ap.id_tipo_antecedente',$antecedente);
		}
		if($diagnostico != ''){
			$this->db->join('salud_diagnosticos_paciente d', 'sa.id = d.id_solicitud');
			$this->db->where('d.id_diagnostico',$diagnostico);
		}
		if(!empty($fecha_inicio) || !empty($fecha_fin)) $this->db->where("(DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') >= '$fecha_inicio' AND DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') <= '$fecha_fin')");
		else $this->db->where("DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') = CURDATE()");
		$this->db->where('sa.estado', 1);
		if(!$administra){
			$this->db->join('valor_parametro per', 'sa.id_tipo_solicitud = per.id_aux');
			$this->db->join('salud_profesional_relacion f', 'f.id_relacion = per.id');
			$this->db->where("f.id_persona = $id_persona and f.estado=1");
		}
		$this->db->group_by("sa.id_persona, vpts.valor");
		$query = $this->db->get();
        return $query->result_array();
	}

	public function consultar_bitacoras($id_persona){			
		$this->db->select("sa.*,sa.id as idsolicitud, sb.*, sb.id as id_bitacora, sa.fecha_registra as fecha_solicitud, vpts.valor as tipo_solicitud, ser.valor as servicio",false);
		$this->db->from("salud_solicitudes sa");
		$this->db->join('valor_parametro vpts', 'sa.id_tipo_solicitud = vpts.id_aux');
		$this->db->join('valor_parametro ser', 'sa.id_servicio = ser.id','left');
		$this->db->join('salud_bitacora sb', 'sa.id = sb.id_solicitud', 'left');
		$this->db->where('sa.id_estado_sol','Sal_Fin_E');
		$this->db->where('sa.id_persona',$id_persona);
		$this->db->where("DATE_FORMAT(sa.fecha_registra,'%Y-%m-%d') = CURDATE()");
		$query = $this->db->get();
        return $query->result_array();
	}

	//Agregado Por Neyla
	public function guardar_reporte($data, $tabla){
		$this->db->insert($tabla,$data);
      	$error = $this->db->_error_message(); 
      	if ($error) {
        	return "error";
      	}
      		return 0;
	}
	//
		
}