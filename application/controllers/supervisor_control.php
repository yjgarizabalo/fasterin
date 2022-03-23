<?php

/* idparametro 37 es de proveedores. */

date_default_timezone_set('America/Bogota');
class supervisor_control extends CI_Controller
{
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
	var $admin = false;
	var $super_admin = false;
	var $ruta_adjuntos = "archivos_adjuntos/supervisor";
	public function __construct()
	{
		parent::__construct();
		$this->load->model('supervisor_model');
		$this->load->model('genericas_model');
		$this->load->model('pages_model');
		session_start();
		if (isset($_SESSION["usuario"])) {
			$this->Super_estado = true;
			$this->Super_elimina = 1;
			$this->Super_modifica = 1;
			$this->Super_agrega = 1;
		}
	}

	public function verificar_campos_string($array){
		foreach ($array as $row) {
			if (empty($row) || ctype_space($row)) {
				return ['type' => -2, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
	}

	public function index($pages = '',$id = '')
	{
		if ($this->Super_estado) {
			$datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $pages);
			if (!empty($datos_actividad)) {
				$data['js'] = "supervisor";
				$data['id'] = $id;
				$data['actividad'] = $datos_actividad[0]["id_actividad"];
			} else {
				$pages = "sin_session";
				$data['js'] = "";
				$data['actividad'] = "Permisos";
			}
		} else {
			$pages = "inicio";
			$data['js'] = "";
			$data['actividad'] = "Ingresar";
		}
			$this->load->view('templates/header', $data);
			$this->load->view("pages/" . $pages);
			$this->load->view('templates/footer');
	}

	public function estadoSupervisor()
	{
		$resultado = array();
		if (!$this->Super_estado) {
			echo json_encode($resultado);
			return;
		} else {
			$perfil = $this->input->post('perfil');
			$resulSupervisores = $this->supervisor_model->estadoSupervisor($perfil,date('w'));
			$btn_turno = '<span class="fa fa-toggle-off " data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
			foreach ($resulSupervisores as $row) {
				$ultima_solicitud= $this->supervisor_model->traer_ultima_solicitud_hoy($row["id_persona"],'id_supervisor',date('Y-m-d'));
				if(!empty($ultima_solicitud)){
					$row['id_solicitud'] =$ultima_solicitud->{'id'};
					$row['estado'] =$ultima_solicitud->{'estado_nombre'};
					$row['entrada'] =$ultima_solicitud->{'fecha_hora_entrada'};
					$row['salida'] =$ultima_solicitud->{'fecha_hora_salida'};
					if($ultima_solicitud->{'id_estado_proceso'}=="Ent_Sup"){
						$row['ver']='<span  style="background-color: #2962ff;color: white; width: 100%;" class="pointer form-control ver_detalle"><span >ver</span></span>';
					}else if($ultima_solicitud->{'id_estado_proceso'}=="Sal_Sup"){
						$row['ver']='<span  style="background-color: #004d40;color: white; width: 100%;" class="pointer form-control ver_detalle"><span >ver</span></span>';
					}else if($ultima_solicitud->{'id_estado_proceso'}=="Rev_Ent_Sup"){
						$row['ver']='<span  style="background-color: #00bcd4;color: white; width: 100%;" class="pointer form-control ver_detalle"><span >ver</span></span>';
					}else if($ultima_solicitud->{'id_estado_proceso'}=="Rev_Sal_Sup"){
						$row['ver']='<span  style="background-color: #ffab00;color: white; width: 100%;" class="pointer form-control ver_detalle"><span >ver</span></span>';
					}
				}else{
					$row['entrada'] ="";
					$row['salida'] ="";
					$row['estado']="SIN INGRESO";
					$row['ver']='<span  style="background-color: #ffff;color: #000;width: 100%;" class="pointer form-control ver_detalle"><span >ver</span></span>';
				}
				$row['accion'] = $btn_turno;
				array_push($resultado, $row);
			}
		}
		echo json_encode($resultado);
	}

	public function filtrar_supervisor()
	{
		$resultado = array();
		if (!$this->Super_estado) {
			echo json_encode($resultado);
			return;
		} else {
			$fecha = $this->input->post('fecha_registro');
			$resulSupervisores = $this->supervisor_model->filtrar_supervisor();
			$btn_turno = '<span class="fa fa-toggle-off " data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
			foreach ($resulSupervisores as $row) {
				$ultima_solicitud= $this->supervisor_model->traer_ultima_solicitud_hoy($row["id_persona"],'id_supervisor',$fecha);
				if(!empty($ultima_solicitud)){
					if($ultima_solicitud->{'id_estado_proceso'}=="Ent_Sup"){
						$row['ver']='<span  style="background-color: #2962ff;color: white; width: 100%;" class="pointer form-control detalle"><span >ver</span></span>';
					}else if($ultima_solicitud->{'id_estado_proceso'}=="Sal_Sup"){
						$row['ver']='<span  style="background-color: #004d40;color: white; width: 100%;" class="pointer form-control detalle"><span >ver</span></span>';
					}else if($ultima_solicitud->{'id_estado_proceso'}=="Rev_Ent_Sup"){
						$row['ver']='<span  style="background-color: #00bcd4;color: white; width: 100%;" class="pointer form-control detalle"><span >ver</span></span>';
					}else if($ultima_solicitud->{'id_estado_proceso'}=="Rev_Sal_Sup"){
						$row['ver']='<span  style="background-color: #ffab00;color: white; width: 100%;" class="pointer form-control detalle"><span >ver</span></span>';
					}	
					$row['id_solicitud'] =$ultima_solicitud->{'id'};
					$row['estado'] =$ultima_solicitud->{'estado_nombre'};				
					$row['entrada'] =$ultima_solicitud->{'fecha_hora_entrada'};
					$row['salida'] =$ultima_solicitud->{'fecha_hora_salida'};
				}else{
					$row['id_solicitud'] =null;
					$row['entrada'] ="";
					$row['salida'] ="";
					$row['estado']="SIN INGRESO";
					$row['ver']='<span  style="background-color: #ffff;color: #000;width: 100%;" class="pointer form-control detalle"><span >ver</span></span>';
				}
				$row['accion'] = $btn_turno;
				array_push($resultado, $row);
				}
			}
			
		echo json_encode($resultado);
		}

	public function traer_detalles()
	{
		$resultado = array();
		if (!$this->Super_estado) {
			echo json_encode($resultado);
			return;
		} else {
			$id_persona = $this->input->post('id_persona');
			$fecha= ($this->input->post('fecha')=="")? date('Y-m-d') : $this->input->post('fecha');
			$ultima_solicitud= $this->supervisor_model->traer_ultima_solicitud_hoy($id_persona,'id_supervisor',$fecha);
			$validarposicion = (empty($ultima_solicitud)) ? [] : $this->supervisor_model->ValidarPosicion($ultima_solicitud->{'id'}); 
			$novedades='<span class="fa fa-picture-o red btn btn-default ver_novedad" style="color:#5CB85C;" title="Ver novedades" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
				if(sizeof($validarposicion)!=0){
					foreach ($validarposicion as $estado){ 
						if(($estado["id_estado"]=="Ent_Sup")){
							$estado['accion']='<span class="fa fa-picture-o red btn btn-default ver_evidencia" style="color:#5CB85C;" title="Ver evidencia" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
							$estado['imagen']=$ultima_solicitud->{'foto_entrada'};
						}else if($estado["id_estado"]=="Sal_Sup"){
							$estado['accion']='<span class="fa fa-picture-o red btn btn-default ver_evidencia" style="color:#5CB85C;" title="Ver evidencia" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
							$estado['imagen']=$ultima_solicitud->{'foto_salida'};
						}else if($estado["id_estado"]=="Rev_Ent_Sup"){
								$novedad=$this->supervisor_model->traer_novedades($ultima_solicitud->{'id'},$estado["id_estado"]);
								$estado['accion']= (empty($novedad)) ? "Sin Novedad": $novedades;
								$estado['novedad']=$novedad;
							}else if($estado["id_estado"]=="Rev_Sal_Sup"){
								$novedad=$this->supervisor_model->traer_novedades($ultima_solicitud->{'id'},$estado["id_estado"]);
								$estado['accion']= (empty($novedad)) ? "Sin Novedad": $novedades;
								$estado['novedad']=$novedad;
							}
							$estado['btn_accion']='<span class="fa fa-picture-o red btn btn-default ver_evidencia" style="color:#5CB85C;" title="Ver evidencia" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
							array_push($resultado, $estado);
						}
						
					}
		}
		echo json_encode($resultado);
	}

	public function traer_novedades()
	{
		$resultado = array();
		if (!$this->Super_estado) {
			echo json_encode($resultado);
			return;
		} else {
			$id_solicitud = $this->input->post('id_solicitud');
			$id_estado = $this->input->post('id_estado');
			$novedad=$this->supervisor_model->traer_novedades($id_solicitud,$id_estado);
					foreach ($novedad as $estado){
						if($estado['evidencia']==null){
							$estado['accion']="Sin evidencia";
						}else{
							$estado['accion']='<span class="fa fa-picture-o red btn btn-default ver_evidencia" style="color:#5CB85C;" title="Ver evidencia" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
						}
							array_push($resultado, $estado);
						}
		}
		echo json_encode($resultado);
	}
	/* Obtener Supervisores */
	public function obtenerSupervisores()
	{
		$resultado = array();
		if (!$this->Super_estado) {
			echo json_encode($resultado);
			return;
		} else {
			$perfil = $this->input->post('perfil');
			$resulSupervisores = $this->supervisor_model->obtenerSupervisores($perfil);
			$btn_turno = '<span class="fa fa-clock-o red btn btn-default turno" style="color:#5CB85C;" title="Asignar turno" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
			$btn_sala = '<span class="fa fa-desktop red btn btn-default sala" style="color: #337ab7;margin-left: 5px" title="Asignar Sala" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
			foreach ($resulSupervisores as $row) {
				$row['accion'] = $btn_turno.' '.$btn_sala;
				array_push($resultado, $row);
			}
		}
		echo json_encode($resultado);
	}

	/* Obtener Salas */
	public function obtenerSalas()
	{
		$resultado = array();
		if (!$this->Super_estado) {
			echo json_encode($resultado);
		} else {
			$idparametro = $this->buscarParametro('sala_supervisor')->idparametro;
			$resulSalas = $this->supervisor_model->obtenerSalas($idparametro);
			$btn_eliminar = '<span title="Eliminar Sala" data-toggle="popover" data-trigger="hover" style="color: #ca3e33;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
        	$btn_modificar = '<span title="Modificar Sala" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
        	foreach ($resulSalas as $row) {
				$row['accion'] = $btn_modificar.' '.$btn_eliminar;
			 	array_push($resultado, $row);
			 }
		}
		echo json_encode($resultado);
	}

	public function SalasSupervisor()
	{
		$resultado = array();
		if (!$this->Super_estado) {
			echo json_encode($resultado);
		} else {
			$id = $_SESSION['persona'];  
			$resulSalas = $this->supervisor_model->SalasSupervisor($id);
		}
		echo json_encode($resulSalas);
	}

	public function TurnosSupervisor()
	{
		$resultado = array();
		if (!$this->Super_estado) {
			echo json_encode($resultado);
		} else {
			$id = $_SESSION['persona'];  
			$resulTurnos = $this->supervisor_model->TurnosSupervisor($id);
		}
		echo json_encode($resulTurnos);
	}

	/* buscar el parametro */
	public function buscarParametro($valorb)
	{
		if (!$this->Super_estado) {
			$resulParametro = [];
		} else {
			$resulParametro = $this->supervisor_model->buscarParametro($valorb);
		}
		return $resulParametro;
	}
	/*Buscar id por id_aux*/
	public function buscarid($valor)
	{
		if (!$this->Super_estado) {
			$resulParametro = [];
		} else {
			$resulParametro = $this->supervisor_model->buscarid($valor);
		}
		return $resulParametro;
	}
//Guardar Turno
public function guardar_turno_spv(){
	if(!$this->Super_estado){
		$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
	}else{
		if ($this->Super_agrega == 0) {
			$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
		}else{
			$id_dia = $this->input->post('id_dia_spv');
			$hora_inicio = $this->input->post('hora_inicio_spv');
			$hora_fin = $this->input->post('hora_fin_spv');
			$observacion = $this->input->post('descripcion_spv');
			$id_turno = $this->input->post('id_turno');
			$id_usuario_registra = $_SESSION['persona'];                 
			$str = $this->verificar_campos_string(['Día'=>$id_dia, 'Hora Inicio'=>$hora_inicio, 'Hora Fin'=>$hora_fin]);
			if(is_array($str)){
				$resp = ['mensaje'=>"El campo ".$str['field']." no puede estar vacio.",'tipo'=>"info", 'titulo'=>"Oops.!"];
			}else{
				if($id_turno){
					$validar = $this->supervisor_model->traer_ultima_solicitud($id_turno,'turnos_supervisor_sala','id');
					if(($validar->{'id_dia'} == $id_dia) &&($validar->{'hora_entrada'} == $hora_inicio) && ($validar->{'hora_salida'} == $hora_fin) && ($validar->{'observacion'} == $observacion)) {
						$resp = ['mensaje'=>"Debe realizar alguna modificación en el horario.",'tipo'=>"info",'titulo'=> "Oops.!"];
					}else{
						$data_turno = ['id_dia' => $id_dia,'hora_entrada' => $hora_inicio, 'hora_salida' => $hora_fin, 'observacion' => $observacion ];
						$mod = $this->supervisor_model->modificar_datos($data_turno, 'turnos_supervisor_sala',$id_turno);
						$resp = ['mensaje'=>"El horario fue gestionado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
						if($mod == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];    
					}
				}else{
					$data_turno = [
						'id_dia' => $id_dia,
						'hora_entrada' => $hora_inicio,
						'hora_salida' => $hora_fin,
						'observacion' => $observacion,
						'id_usuario_registra' => $id_usuario_registra ];
					$add = $this->supervisor_model->guardar_datos($data_turno, 'turnos_supervisor_sala');
					$resp = ['mensaje'=>"El horario fue guardado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
					if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
				}                
			}    
		}   
	}
	echo json_encode($resp); 
}

//Guardar Sala
public function guardar_sala_spv(){
	if(!$this->Super_estado){
		$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
	}else{
		if ($this->Super_agrega == 0) {
			$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
		}else{
			$nombre = $this->input->post('nombre_sala');
			$descripcion = $this->input->post('descripcion_sala');   
			$id_sala = $this->input->post('id_sala'); 
			$id_usuario_registra = $_SESSION['persona']; 
			$idparametro=333;               
			$str = $this->verificar_campos_string(['Código'=>$nombre,'Nombre'=>$nombre, 'Descripción'=>$descripcion]);
			$existe = $this->supervisor_model->ValidarNombre(333,$nombre);
			if(is_array($str)){
				$resp = ['mensaje'=>"El campo ".$str['field']." no puede estar vacio.",'tipo'=>"info", 'titulo'=>"Oops.!"];
			}else{
				if($id_sala){
					$validar = $this->supervisor_model->traer_ultima_solicitud($id_sala,'valor_parametro','id');
					if(($validar->{'valor'} == $nombre) && ($validar->{'valorx'} == $descripcion)) {
						$resp = ['mensaje'=>"Debe realizar alguna modificación en sala.",'tipo'=>"info",'titulo'=> "Oops.!"];
					}else{
						if(($validar->{'valor'} != $nombre)&& $existe){
							$resp = ['mensaje'=>"Ya hay una sala registrada con este nombre.!",'tipo'=>"info", 'titulo'=> "Oops.!"];
						}else{
							$data_turno = ['valor' => $nombre, 'valorx' => $descripcion];
							$mod = $this->supervisor_model->modificar_datos($data_turno, 'valor_parametro',$id_sala);
							$resp = ['mensaje'=>"La información fue modificada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
							if($mod == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];   
						} 
					}
				}else{
					if($existe){
						$resp = ['mensaje'=>"Ya hay una sala registrada con este nombre.!",'tipo'=>"info", 'titulo'=> "Oops.!"];
					}else{
					$data_turno = [
						'valor' => $nombre,
						'valorx' => $descripcion,
						'valorb' => 'sala_supervisor',
						'idparametro' => $idparametro,
						'usuario_registra' => $id_usuario_registra ];
					$add = $this->supervisor_model->guardar_datos($data_turno, 'valor_parametro');
					$resp = ['mensaje'=>"La sala fue creada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
					if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
				}
					
				}                
			}    
		}   
	}
	echo json_encode($resp); 
}

//Listar Todos los Turnos
	public function listar_turnos_spv()
    {
        $data = $this->Super_estado == true ? $this->supervisor_model->listar_turnos_spv() : array();
        $btn_eliminar = '<span title="Eliminar Turno" data-toggle="popover" data-trigger="hover" style="color: #ca3e33;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
        $btn_modificar = '<span title="Modificar Turno" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
        $turnos = array();
        foreach ($data as $row) {
            $row['accion'] = $btn_modificar.' '.$btn_eliminar;
            array_push($turnos,$row);
        }
        echo json_encode($turnos);
    }

	public function delete_turno()
    {
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $id = $this->input->post('id');
            $ver = $this->supervisor_model->exist_spv($id,'turnos_supervisor_sala');
            if(empty($ver)){
                $resp = ['mensaje'=>"El turno ya le fue eliminado.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $del = $this->supervisor_model->delete_spv($id,'turnos_supervisor_sala');
                $resp = ['mensaje'=>"Turno eliminado correctamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                if ($del != 0) {
                    $resp = ['mensaje'=>"Error al eliminar el turno, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }
	//Eliminar Sala
	public function delete_sala()
    {
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $id = $this->input->post('id');
            $ver = $this->supervisor_model->exist_spv($id,'valor_parametro');
            if(empty($ver)){
                $resp = ['mensaje'=>"La sala ya le fue eliminada.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $del = $this->supervisor_model->delete_spv($id,'valor_parametro');
                $resp = ['mensaje'=>"Sala eliminada correctamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                if ($del != 0) {
                    $resp = ['mensaje'=>"Error al eliminar la sala, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

//Asignar Turno a Supervisores
public function asignar_turno_supervisor(){
	if(!$this->Super_estado){
		$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
	}else{
		if ($this->Super_agrega == 0) {
			$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
		}else{
			$id_persona = $this->input->post('id_persona');
			$id_turno = $this->input->post('id_turno');
			$id_usuario_registra = $_SESSION['persona'];
			$existe = $this->supervisor_model->validar_turno_supervisor($id_persona,$id_turno);
			if($existe){
				$resp = ['mensaje'=>"El supervisor ya se encuentra registrado.!",'tipo'=>"info", 'titulo'=> "Oops.!"];
			}else{
				$data = [
					'id_turno' => $id_turno,
					'id_persona	' => $id_persona,
					'id_usuario_registra' => $id_usuario_registra];
				$add = $this->supervisor_model->guardar_datos($data, 'supervisor_turnos');
				$resp = ['mensaje'=>"El supervisor fue guardado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
				if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
			}
		}
	}
	echo json_encode($resp); 
}

//Asignar Sala a Supervisores
public function asignar_sala_supervisor(){
	if(!$this->Super_estado){
		$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
	}else{
		if ($this->Super_agrega == 0) {
			$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
		}else{
			$id_persona = $this->input->post('id_persona');
			$id_sala = $this->input->post('id_sala');
			$id_usuario_registra = $_SESSION['persona'];
			$existe = $this->supervisor_model->validar_sala_supervisor($id_persona,$id_sala);
			if(!empty($existe)){
				$resp = ['mensaje'=>"La sala ya se encuentra asignada.!",'tipo'=>"info", 'titulo'=> "Oops.!"];
			}else{
					$data_turno = [
						'id_sala' => $id_sala,'id_persona' => $id_persona,'id_usuario_registra' => $id_usuario_registra];
					$add = $this->supervisor_model->guardar_datos($data_turno, 'supervisor_salas');
					$resp = ['mensaje'=>"La Sala fue asignada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
					if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];					
				 
			}
		}
	}
	echo json_encode($resp); 
}
//Listar los turnos para asignar a supervisor
public function listar_asignar_turnos()
{
	$turnos_td = $this->Super_estado == true ? $this->supervisor_model->listar_turnos_spv() : array();
	$id_persona = $this->input->post('id_persona');
	$btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" style="color:#5CB85C"></span>';
	$btn_desasignar = '<span title="Desasignar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default desasignar" style="color:#d9534f"></span>';
	$turnos = array();
        foreach ($turnos_td as $row) {
			$mis_turnos = $this->supervisor_model->validar_turno_supervisor($row['id'], $id_persona);
			if (!$mis_turnos) {
				$row['accion'] = $btn_asignar;
			} else {
				$row['id_desasignar']=$mis_turnos->id;
				$row['accion'] = $btn_desasignar;
			}
            
            array_push($turnos,$row);
        }
	echo json_encode($turnos);
}

//Listar las salas para asignar a supervisor
public function listar_asignar_salas()
{
	$idparametro = $this->Super_estado == true ? $this->buscarParametro('sala_supervisor')->idparametro: array();
	$salas_td = $this->Super_estado == true ? $this->supervisor_model->obtenerSalas($idparametro): array();
	$id_persona = $this->input->post('id_persona');
	$btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" style="color:#5CB85C"></span>';
	$btn_desasignar = '<span title="Desasignar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default desasignar" style="color:#d9534f"></span>';
	$turnos = array();
        foreach ($salas_td as $row) {
			$mis_salas = $this->supervisor_model->validar_sala_supervisor($row['id'], $id_persona);
			if (!$mis_salas) {
				$row['accion'] = $btn_asignar;
			} else {
				$row['id_desasignar']=$mis_salas->id;
				$row['accion'] = $btn_desasignar;
			}
            
            array_push($turnos,$row);
        }
	echo json_encode($turnos);
}


//Quitar Supervisor de ese turnos
public function desasignar_turno()
{
	if(!$this->Super_estado) {
		$resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
	}else {
		$id = $this->input->post('id');
		$ver = $this->supervisor_model->exist_spv($id,'supervisor_turnos');
		if(empty($ver)){
			$resp = ['mensaje'=>"El supervisor ya fue retirado del turno.",'tipo'=>"info",'titulo'=> "Oops.!"];
		}else{
			$del = $this->supervisor_model->delete_spv($id,'supervisor_turnos');
			$resp = ['mensaje'=>"Supervisor retirado correctamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
			if ($del != 0) {
				$resp = ['mensaje'=>"Error al retirar el supervisor, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
			}
		}
	}
	echo json_encode($resp);
}

//Quitar Sala de ese Supervisor
public function delete_sala_turno()
{
	if(!$this->Super_estado) {
		$resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
	}else {
		$id = $this->input->post('id');
			$del = $this->supervisor_model->delete_spv($id,'supervisor_salas');
			$resp = ['mensaje'=>"Sala retirada correctamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
			if ($del != 0) {
				$resp = ['mensaje'=>"Error al retirar el supervisor, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
			}
	}
	echo json_encode($resp);
}

//Funciones de la Vista Supervisor Normal

//Función para ver que actividad tiene pendiente el supervisor
public function supervisor() {
	$data = [];
    $pages = "sin_session";
    $data['js'] = "";
    $data['actividad'] = "Ingresar";
    $render = false;
	$id="";
	if ($this->Super_estado) {
		$render = true;
		$pages =  "supervisor";
		$datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $pages);
			if (!empty($datos_actividad)) {
				$data['actividad'] = $datos_actividad[0]["id_actividad"];
				$data['js'] ="supervisor";
				$data['id'] = $id;
			} else {
				$pages = "sin_session";
				$data['js'] = "";
				$data['actividad'] = "Permisos";
			}
			$mis_datos = $this->supervisor_model->DatosSupervisor($_SESSION["persona"]);
            $data['nombre'] = $mis_datos[0]['nombre_completo'];
			$data['cargo'] = $mis_datos[0]['cargo'];
			$data['foto'] = $mis_datos[0]['foto'] == 'Myfoto.png' || $mis_datos[0]['foto'] == 'User.png' ? 'empleado.png' : $mis_datos[0]['foto'];
			$mis_salas = $this->supervisor_model->SalasSupervisor($_SESSION["persona"]);
            $data['mis_salas'] = $mis_salas;
			$turno = $this->supervisor_model->VerificarTurno($_SESSION["persona"],date('w'));
			$data['turno'] = $turno;
			$ultima_solicitud= $this->supervisor_model->traer_ultima_solicitud_hoy($_SESSION["persona"],'id_supervisor',date('Y-m-d'));
			$result=0; $validarposicion=[];
			if(!empty($ultima_solicitud)){
				$validarposicion = $this->supervisor_model->ValidarPosicion($ultima_solicitud->{'id'});
				$result=sizeof($validarposicion);
			}
			$data['ultimo_estado'] = $result;
			$data['ver_estados']=$validarposicion;
			$entrada=array(); $salida=array(); $rev_entrada=array(); $rev_salida=array();
			if(sizeof($validarposicion)!=0){
                foreach ($validarposicion as $estado){ 
					if($estado["id_estado"]=="Ent_Sup"){ array_push($entrada,$estado);
					}else if($estado["id_estado"]=="Sal_Sup"){ array_push($salida,$estado);
					}else if($estado["id_estado"]=="Rev_Ent_Sup"){ 
						array_push($rev_entrada,$estado);
						$data['novedades_ent']= $this->supervisor_model->traer_novedades($ultima_solicitud->{'id'},"Rev_Ent_Sup");
					}else if($estado["id_estado"]=="Rev_Sal_Sup"){
						 array_push($rev_salida,$estado);
						 $data['novedades_sal']= $this->supervisor_model->traer_novedades($ultima_solicitud->{'id'},"Rev_Sal_Sup");
					}
                }
            }
			$data['entrada']=$entrada;
			$data['salida']=$salida;
			$data['rev_entrada']=$rev_entrada;
			$data['rev_salida']=$rev_salida;
	}
	if ($render)  $this->load->view("pages/".$pages,$data);
        else{
            $this->load->view('templates/header',$data);
            $this->load->view("pages/".$pages);
            $this->load->view('templates/footer'); 
        }
}

public function guardar_entrada_salida(){
	$error=""; $mensaje="";	$novedades="";
	if ($this->Super_estado == false) {
		$resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
	} else {
		if ($this->Super_agrega == 0) {
			$resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
		} else {
			$observacion = $this->input->post('observacion_entrada');
			$tipo = $this->input->post('tipo');
			$id_persona=$_SESSION['persona'];
			$evidencia = isset($_POST['foto_entrada']) ? 'Evidencia' . uniqid() : null;
			$ultima_solicitud= $this->supervisor_model->traer_ultima_solicitud_hoy($id_persona,'id_supervisor',date('Y-m-d'));
			if(($evidencia==null) && ($tipo=="Ent_Sup"||$tipo=="Sal_Sup")){
					$resp = ['mensaje'=>"Recuerde tomar la foto para continuar con el proceso",'tipo'=>"info", 'titulo'=>"Oops.!"];
			}else{
				if($tipo=="Ent_Sup"){
					if(empty($ultima_solicitud)){
						$data = array('foto_entrada' => $evidencia,'id_estado_proceso' => $tipo,'id_supervisor'=>$id_persona,'id_usuario_registro'=>$id_persona);
						$add = $this->pages_model->guardar_datos($data,'supervisor_solicitud');
						$mensaje="Entrada registrada de forma exitosa.";
					}else{ $error="Usted ya realizo el registro de su entrada";	}
					}else if(empty($ultima_solicitud)){
						$error="Antes de realizar cualquier paso recuerde registrar su entrada";
					}else if($ultima_solicitud->{'id_estado_proceso'} ==$tipo){
						$error="Usted ya realizo este paso";
					}else if($tipo=="Sal_Sup"){
						if($ultima_solicitud->{'id_estado_proceso'} !='Rev_Sal_Sup'){
							$error="Para poder registrar su salida, recuerde realizar cada uno de los pasos anteriores";
						}else{
							$format = 'Y-m-d H:i:s';
							$fecha_actual = date($format);
							$data = array('foto_salida' => $evidencia,'id_estado_proceso' => $tipo, 'fecha_hora_salida'=>$fecha_actual);
							$add = $this->pages_model->modificar_datos($data,'supervisor_solicitud',$ultima_solicitud->{'id'});
							$mensaje="Salida registrada de forma exitosa.";
						}
					}else if($tipo=="Rev_Ent_Sup"||$tipo=="Rev_Sal_Sup"){
						if(($tipo=="Rev_Sal_Sup")&&($ultima_solicitud->{'id_estado_proceso'} !='Rev_Ent_Sup')){
							$error="Para poder registrar la revisión, recuerde realizar cada uno de los pasos anteriores";
						}else{
							if(($tipo=="Rev_Ent_Sup")&&($ultima_solicitud->{'id_estado_proceso'} !='Ent_Sup')){
								$error="Usted ya realizo el registro de está revisión";
							}else{
								$data = array('id_estado_proceso' => $tipo);
								$add = $this->pages_model->modificar_datos($data,'supervisor_solicitud',$ultima_solicitud->{'id'});
								$mensaje="La revisión fue registrada de forma exitosa.";
								$novedades=$this->supervisor_model->traer_novedades($ultima_solicitud->{'id'}, $tipo);
							}
						}
					}
					if($error){
						$resp = ['mensaje' => $error, 'tipo' => "info", 'titulo' => "Oops.!"];
					}else{
						if ($add) {
							$ultima_solicitud= $this->supervisor_model->traer_ultima_solicitud_hoy($id_persona,'id_supervisor',date('Y-m-d'));
							$mis_datos = $this->supervisor_model->DatosSupervisor($_SESSION["persona"]);
							//$estado=$this->buscarid($tipo)->id;
							$data = array('id_solicitud'=>$ultima_solicitud->{'id'},'id_estado' => $tipo,'id_usuario_registro'=>$id_persona);
							$add = $this->pages_model->guardar_datos($data,'supervisor_solicitud_estados');
							if (isset($_POST['foto_entrada'])) {
								$datos = base64_decode(preg_replace('/^[^,]*,/', '', $_POST['foto_entrada']));
								file_put_contents('archivos_adjuntos/supervisor/' . $evidencia . '.png', $datos);
							}
							$resp = ['mensaje' => $mensaje, 'tipo' => "success",'id_solicitud'=>$ultima_solicitud->{'id'},'id_persona'=>$ultima_solicitud->{'id_supervisor'},'novedades'=>$novedades, 'supervisor'=>$mis_datos[0]['nombre_completo'],'titulo' => "Proceso Exitoso.!"];
						} else $resp = ['mensaje'=>"Error al realizar esta acción, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
					}	
			}
		}
	}
	echo json_encode($resp);
	return;
}
	public function guardar_revision()
    {
		$error="";
        if ($this->Super_estado == false) {
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
                $observacion = $this->input->post('observacion_revision');
				$tipo = $this->input->post('tipo_revision');
				$id_persona=$_SESSION['persona'];
				$sala_sup =$this->input->post('sala_sup');
                $evidencia = isset($_POST['foto_revision']) ? 'Evidencia' . uniqid() : null;
				$str = $this->verificar_campos_string(['Observación'=>$observacion]);
				if(is_array($str)){
					$resp = ['mensaje'=>"El campo ".$str['field']." no puede estar vacio.",'tipo'=>"info", 'titulo'=>"Oops.!"];
				}else{
					$ultima_solicitud= $this->supervisor_model->traer_ultima_solicitud_hoy($id_persona,'id_supervisor',date('Y-m-d'));
					if(empty($ultima_solicitud)){
						$error="Antes de registrar una novedad, recuerde realizar el registro de su entrada";
					}else{	
						$data = array('id_sala' => $sala_sup, 'tipo' => $tipo, 'id_solicitud'=>$ultima_solicitud->{'id'},'evidencia' => $evidencia,'descripcion' => $observacion,'id_usuario_registro' => $id_persona);
						if(($ultima_solicitud->{'id_estado_proceso'} ==$tipo)||($ultima_solicitud->{'id_estado_proceso'} != "Ent_Sup" && $tipo=="Rev_Ent_Sup")){
							$error="Usted ya finalizo está revisión, por lo cual no puede registrar más novedades";
						}else if($ultima_solicitud->{'id_estado_proceso'} != "Rev_Ent_Sup" && $tipo=="Rev_Sal_Sup"){
							if($ultima_solicitud->{'id_estado_proceso'} == "Ent_Sup"){
								$error="Antes de registrar una novedad recuerde realizar los pasos anteriores";
							}else $error="Usted ya finalizo está revisión, por lo cual no puede registrar más novedades";
						}else{
							$add = $this->pages_model->guardar_datos($data,'supervisor_solicitud_novedades');
						}
					}
					if($error){
						$resp = ['mensaje' => $error, 'tipo' => "info", 'titulo' => "Oops.!"];
					}else{
						if ($add) {
						if (isset($_POST['foto_revision'])) {
							$datos = base64_decode(preg_replace('/^[^,]*,/', '', $_POST['foto_revision']));
							file_put_contents('archivos_adjuntos/supervisor/' . $evidencia . '.png', $datos);
						}
						$resp = ['mensaje' => 'Novedad registrada de forma  exitosa', 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
					} else $resp = ['mensaje'=>"Error al registrar novedad, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
					}
					
				}	
			}
        }
        echo json_encode($resp);
        return;
    }

	public function obtener_estado_supervisor(){
		if ($this->Super_estado == false) {
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        } else {
			$nov_ent=[];
			$nov_sal=[];
			$id_persona = $this->input->post('id_persona');
			$ultima_solicitud= $this->supervisor_model->traer_ultima_solicitud_hoy($_SESSION["persona"],'id_supervisor',date('Y-m-d'));
			$result=0; $validarposicion=[];
			if(!empty($ultima_solicitud)){
				$validarposicion = $this->supervisor_model->ValidarPosicion($ultima_solicitud->{'id'});
				$result=sizeof($validarposicion);
			}
			$data['ultimo_estado'] = $result;
			$data['ver_estados']=$validarposicion;
			$entrada=array(); $salida=array(); $rev_entrada=array(); $rev_salida=array();
			if(sizeof($validarposicion)!=0){
                foreach ($validarposicion as $estado){ 
					if($estado["id_estado"]=="Ent_Sup"){ array_push($entrada,$estado);
					}else if($estado["id_estado"]=="Sal_Sup"){ array_push($salida,$estado);
					}else if($estado["id_estado"]=="Rev_Ent_Sup"){ 
						array_push($rev_entrada,$estado);
						$nov_ent =$this->supervisor_model->traer_novedades($ultima_solicitud->{'id'},"Rev_Ent_Sup");
					}else if($estado["id_estado"]=="Rev_Sal_Sup"){
						 array_push($rev_salida,$estado);
						 $nov_sal =$this->supervisor_model->traer_novedades($ultima_solicitud->{'id'},"Rev_Sal_Sup");
					}
                }
            }
			$mis_salas = $this->supervisor_model->SalasSupervisor($_SESSION["persona"]);
			$resp = ['entrada' => $entrada,'salida' => $salida, 'rev_entrada' => $rev_entrada, 'rev_salida' => $rev_salida,'mis_salas' => $mis_salas,'novedad_salida' => $nov_sal,'novedad_entrada' => $nov_ent,'posicion' => $result];
		}
		echo json_encode($resp);
	}
}
