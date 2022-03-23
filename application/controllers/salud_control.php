<?php

class salud_control extends CI_Controller
{

	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
    var $Super_agrega = 0;
    var $ruta_adjuntos = "archivos_adjuntos/salud/resultado_examen/";

    public function __construct(){
        parent::__construct();
        $this->load->model('genericas_model');
        $this->load->model('salud_model');
        date_default_timezone_set("America/Bogota");
        session_start();
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
            $this->administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_salud'? true : false;
        }
    }

    public function index($id = 0){
        $pages = "inicio";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $data["id"] = $id;
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], "salud");
            if (!empty($datos_actividad)) {
                $pages = "salud";
                $data['js'] = "Salud";
                $data['actividad'] = $datos_actividad[0]["id_actividad"];
            }else{
                $pages = "sin_session";
                $data['js'] = "";
                $data['actividad'] = "Permisos";
            }

            $id_parametro = 152;
            $tipo_examen = $this->salud_model->listar_valor_parametro($id_parametro);
            $data['tipo_examen'] = $tipo_examen;

            $admin = $this->administra;
            $id_parametro = 148;
            $tipo_solicitud = $this->salud_model->listar_permisos_funcionario($admin,$_SESSION["persona"],$id_parametro);
            $data['tipo_solicitud'] = $tipo_solicitud;
        }
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function verificar_campos_numericos($array){
		foreach ($array as $row) {
			if (!is_numeric($row)) {
				return ['type' => -1, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
    }
    
	public function verificar_campos_string($array){
		foreach ($array as $row) {
			if (empty($row) || ctype_space($row)) {
				return ['type' => -2, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
    }

    public function obtener_fecha_fin($fecha_i, $duracion_min, $format = 'Y-m-d H:i:s'){
        $fecha_fin = date($format,strtotime($fecha_i." + $duracion_min minutes")); 
        return $fecha_fin;
    }

    public function validar_tiempo_edicion($id_solicitud,$id_aux){
        $id_parametro = 20;  
        $valor = $this->salud_model->valor_parametro_id_aux($id_aux, $id_parametro);
        $duracion_min = $valor->{'valor'};
        $solicitud = $this->salud_model->traer_ultima_solicitud($id_solicitud,'salud_solicitudes','id');
        if(!empty($solicitud)){
            $fecha_registra = $solicitud->{'fecha_registra'};
            $fecha_actual = date("Y-m-d H:i:s");
            $fecha_fin = $this->obtener_fecha_fin($fecha_registra, $duracion_min,'Y-m-d H:i:s');
            if($fecha_fin >= $fecha_actual){
                return 1;
            }
        }
        return 0;
    }

    public function listar_permisos_funcionario(){
        $admin = $this->administra;
        $id_persona = $_SESSION["persona"]; 
        $id_parametro  = $this->input->post("idparametro"); 
        $resp = $this->Super_estado == true ? $this->salud_model->listar_permisos_funcionario($admin,$id_persona,$id_parametro) : array();
        echo json_encode($resp);
    }

    public function listar_valor_parametro(){
        $id_parametro = $this->input->post("idparametro"); 
        $data = $this->Super_estado == true ? $this->salud_model->listar_valor_parametro($id_parametro) : array();
        $btn_config = '<span title="Asignar" data-toggle="popover" data-trigger="hover" style="color: #39b23b;margin-left: 5px" class="pointer fa fa-user btn btn-default asignar"></span>';
        $btn_modificar = '<span title="Modificar Servicio" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
        $btn_eliminar = '<span title="Eliminar Servicio" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
        $valores = array();
        foreach ($data as $row) {
            ($row['idparametro'] == 148) ? $row['accion'] = $btn_config : $row['accion'] = $btn_modificar .' '. $btn_eliminar; 
            array_push($valores,$row);
        }
        echo json_encode($valores);         
    }

    public function listar_profesional_servicio(){
        $id_relacion = $this->input->post("id_idparametro"); 
        $data = $this->Super_estado == true ? $this->salud_model->listar_profesional_servicio($id_relacion) : array();
        $btn_eliminar = '<span title="Eliminar Profesional" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
        $resp = array();
        foreach ($data as $row) {
            $row['accion'] = $btn_eliminar; 
            array_push($resp,$row);
        }
        echo json_encode($resp);  
    }

    public function eliminar_profesional_servicio(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_elimina == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $resp = array();
                $id = $this->input->post('idrelacion');
                $id_usuario_elimina = $_SESSION['persona'];
                $fecha = date("Y-m-d H:i");
                $data = ['id_usuario_elimina' => $id_usuario_elimina,
                'fecha_elimina' => $fecha,
                'estado'  => 0];
                $del = $this->salud_model->modificar_datos($data, 'salud_profesional_relacion', $id);
                if($del == 1) $resp = ['mensaje'=>"Error al gestionar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }
        }
     echo json_encode($resp);
    }

    public function listar_atenciones(){
        $tipo_solicitud = $this->input->post("tipo_solicitud");
        $estado = $this->input->post("estado");
        $servicio = $this->input->post("servicio");
        $tipo_persona =  $this->input->post("tipo_persona");
        $fecha = $this->input->post("fecha");
        $fecha_2 = $this->input->post("fecha_2");
        $id = $this->input->post("id");
        $id_persona = $_SESSION['persona'];
        $admin = $this->administra;
        $excel = 0;
        $data = $this->Super_estado == true ? $this->salud_model->listar_atenciones($tipo_solicitud,$estado,$servicio,$tipo_persona,$fecha,$fecha_2,$excel,$id_persona,$admin) : array();
        $ver_finalizado = '<span  style="background-color: #39b23b;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';      
        $btn_modificar = '<span title="Modificar Atención" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
        
        $btn_cancelar = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px" class="pointer fa fa-remove btn btn-default cancelar"></span>';
        $ver_rojo = '<span  style="background-color: #d9534f;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';      
        $ver_solicitado = '<span  style="background-color: #ffff;color: #000;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
        $btn_cerrada = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
        $btn_abierta = '<span title="Solicitud Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-on btn pointer"></span>';
        $resp = array();
        foreach ($data as $row) {
            switch ($row['id_tipo_solicitud']) {
                case 'Sal_Sol_Cov':
                    $sw = false;
                    if ($row['act_protocolos'] == 1) {
                        if($row['id_estado_sol'] == 'Sal_Pro_E'){
                            $row['ver'] =$ver_solicitado;
                            $btn_seguimiento = '<input type="hidden" id="idsoli" name="idsoli" value="'.$row['id_covid'].'">
                                                <input type="hidden" id="idsolitu" name="idsolitu" value="'.$row['id_solicitud'].'">
                            <span title="Seguimiento Protocolo covid" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default seguimiento"></span>';
                            $row['accion'] = $btn_seguimiento;
                            $sw = true;
                        }
                    }

                    if($sw==false){
                        $row['ver'] = $ver_finalizado;
                        $row['accion'] = $btn_cerrada;
                    }
                    break;
                default:
                    $row['ver'] = $ver_finalizado;
                    $row['accion'] = $btn_cerrada;
                    break;
            }
            array_push($resp,$row);
        }
        echo json_encode($resp);
    }


    public function exportar_excel_solicitudes($tipo_solicitud,$servicio, $estado, $tipo_persona,$fecha, $fecha2){
        $data = array();
        $nombre="Solicitudes_SaludAtenciones"; $titulo="ASMEDIC-U <br/> PLANILLA DE USUARIOS ATENDIDOS";
        if($tipo_solicitud=="Sal_Sol_Cov"){
             $resp = $this->salud_model->listar_protocolo_excel($tipo_solicitud,$servicio, $estado, $tipo_persona, $fecha, $fecha2, $id=0);
                
          
          if($tipo_persona=="Per_emp"){
           foreach ($resp as $row) {
                    if($row['servicio']) $tipo_servicio = $row['servicio'];
                    else $tipo_servicio = $row['tipo_solicitud'];
                   
                    array_push($data, [ 
                        "NOMBRE COMPLETO" => $row['nombre_completo'],
                        "TIPO DOCUMENTO" => "Cedula de ciudadania",
                        "NUMERO DI" => $row['identificacion'],
                        "CARGO" => $row['cargo'],
                        "DEPARTAMENTO" => $row['dependencia'],
                        "SUBCLASIFICACIÓN" => $row['subclasificacion'],
                        "EPS" => $row['eps'],
                        "BARRIO" => $row['barrio'],
                        "FECHA REPORTE" => $row['fecha_solicitud'], 
                        "FECHA INICIO SINTOMAS" => $row['fecha_sintomas'], 
                        "SINTOMAS" => ($row['sintomas']== 1)? "SI" : "NO",
                        "MEDIO DE REPORTE" => $row['med_reporte'],
                        "MOTIVO DEL REPORTE" => $row['mot_reporte'],
                        "ACTIVA PROTOCOLO" => ($row['act_protocolos']== 1)? "ACTIVADO" : "DESACTIVADO",
                        "ESTADO INICIAL" => $row['estado_inicial'],
                        "ESTADO FINAL" => $row['estado_final'],
                        "ESTADO ACTUAL" => $row['estado'],
                        "TIPO DE REPORTE" => $row['tipo_reporte'],
                        "OBSERVACIONES" => $row['observacion'],
                        ]); 
                }$col=19;  $nombre= "LISTADO PROTOCOLO COVID-COLABORADORES"; $titulo="ASMEDIC-U <br/>REPORTE DE CASOS MANEJO PROTOCOLO INTERNO COVID19 - UNICOSTA - DIRECCION  DE BIENESTAR LABORAL";
                }else{ 
                foreach ($resp as $row) {
                    if($row['servicio']) $tipo_servicio = $row['servicio'];
                    else $tipo_servicio = $row['tipo_solicitud'];
                    array_push($data, [ 
                        "NOMBRE COMPLETO" => $row['nombre_completo'],
                        "TIPO DOCUMENTO" => $row['tipo_identificacion'],
                        "NUMERO DI" => $row['identificacion'],
                        "PROGRAMA" => $row['cargo'],
                        "FECHA REPORTE" => $row['fecha_solicitud'],
                        "MEDIO DE REPORTE" => $row['med_reporte'],
                        "MOTIVO DEL REPORTE" => $row['mot_reporte'],
                        "ACTIVA PROTOCOLO" => ($row['act_protocolos']== 1)? "ACTIVADO" : "DESACTIVADO",
                        "ESTADO INICIAL" => $row['estado_inicial'],
                        "ESTADO FINAL" => $row['estado_final'],
                        "ESTADO ACTUAL" => $row['estado'],
                        "TIPO DE REPORTE" => $row['tipo_reporte'],
                        "OBSERVACIONES" => $row['observacion'],
                        ]); 
                }  $col=13; $nombre="LISTADO PROTOCOLO COVID-ESTUDIANTES";$titulo="ASMEDIC-U <br/>REPORTE DE CASOS MANEJO PROTOCOLO INTERNO COVID19 - UNICOSTA - DIRECCION DE BIENESTAR ESTUDIANTIL";
            }
            
        }else{
            $resp = $this->salud_model->listar_atenciones_excel($tipo_solicitud,$servicio, $estado, $tipo_persona, $fecha, $fecha2, $id=0);
            foreach ($resp as $row) {
                if($row['servicio']) $tipo_servicio = $row['servicio'];
                else $tipo_servicio = $row['tipo_solicitud'];
                array_push($data, [ 
                    "FECHA" => $row['fecha_solicitud'], 
                    "NOMBRES Y APELLIDOS" => $row['nombre_completo'],
                    "SEXO" => $row['genero'],
                    "EDAD" => $row['edad'],
                    "DEPENDENCIA, FACULTAD O PROGRAMA" => $row['dependencia'],
                    "SERVICIO SOLICITADO" => $tipo_servicio,
                    "PROFESIONAL O TECNICO DE SALUD" => $row['profesional']
                    ]); 
            }
            $col=7;
        }
        $datos["nombre"] = $nombre;
        $datos["datos"] = $data;
        $datos["titulo"] = $titulo;
        $datos["version"] = "VERSION: 04";
        $datos["trd"] = "TRD: 500-504-90";
        $datos["leyenda"] = "Por medio de la presente manifiesto que he sido informado que la CORPORACIÓN UNIVERSITARIA DE LA COSTA - CUC, institución privada de educación superior, sin animo de lucro, con domicilio en la ciudad de Barranquilla Cll. 58 #55-66,
        con dirección electrónica: buzon@cuc.edu.co y teléfono 3362200, es el responsable del tratamiento de los datos personales obtenidos a través de las relaciones con los estudiantes. Por ello, consiento y autorizo de manera previa,
        expresa e inequívoca que mis datos personales sean tratados (recolectados, almacenados, usados, compartidos, procesados, transmitidos, transferidos, suprimidos o actualizados) para el cumplimiento de las siquientes finalidades:
        (i) Crear bases de datos de acuerdo a los características y perfiles de los titulares de Datos Personales, todo de acuerdo con lo dispuesto en la ley, (ii) Datos de salud. 
        <br/>
        La CORPORACIÓN UNIVERSITARIA DE LA COSTA me ha informado que como titular de datos personales sensibles no estoy obligado a otorgar autorización sobre esta clase de datos y que sólo se podrán tratar si se cuenta con mi consentimiento
        expreso el cual otorgo mediante la firma de este documento. Para obtener más información sobre los casos en que puedo autorizar el uso de mis datos sensibles y los canales para ejercer mis derechos son la dirección de correo electrónico
        o al teléfono en Barranquilla proporcionados previamente o me han recomendado consultar las políticas de tratamiento de datos personales en la página www.cuc.edu.co. De los datos que serán objeto de tratamiento se consideran sensibles los
        siguientes: información de salud.
        <br/> 
        Me permito manifestar que he leído el presente documento, y manifiesto mi consentimiento y autorización de forma voluntaria, verídica y completa para el tratamiento de los datos privados y sensibles por mí suministrados dentro de las finalidades
        aquí contempladas por CORPORACIÓN UNIVERSITARIA DE LA COSTA.";
        $datos["fecha"] = "2019-7";
        $datos["col"] = $col;
        $this->load->view('templates/exportar_excel', $datos);
    }

    public function buscar_persona(){
        $resp = array();
        $tipo = $this->input->post("tipo");
        $dato = $this->input->post("dato");
        $query = $this->salud_model->valor_parametro_id_aux($tipo);
        if($query->{'valorx'} == 1){ $tabla = 'personas';
	    }else $tabla = 'visitantes';
        if (!empty($dato)) $resp = $this->Super_estado == true ? $this->salud_model->buscar_persona( $tabla, $dato) : array();
        echo json_encode($resp);
    }

    public function buscar_paciente(){
        $resp = array();
        $tipo_solicitante = $this->input->post("tipo_solicitante");
        if(!is_numeric($tipo_solicitante)){
            $query = $this->salud_model->valor_parametro_id_aux($tipo_solicitante);
            $tipo_solicitante = $query->{'valorx'};
        }
        $id_persona = $this->input->post("id_persona");
        $resp = $this->Super_estado == true ? $this->salud_model->buscar_paciente( $id_persona,$tipo_solicitante) : array();
        echo json_encode($resp);
    }

    public function guardar_atencion(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_usuario_registra = $_SESSION['persona'];    
                $id_solicitante = $this->input->post("id_solicitante");            
                $id_servicio = $this->input->post("id_servicio");
                $observaciones = $this->input->post("observaciones");
                $tipo = $this->input->post("tipo_persona");
                $query = $this->salud_model->valor_parametro_id_aux($tipo);
                $tipo_solicitante = $query->{'valorx'};
                $sw = false;     
                
                $str = $this->verificar_campos_string(['Paciente' => $id_solicitante,'Servicio' => $id_servicio]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data_solicitud = [
                        'id_persona' => $id_solicitante,
                        'id_servicio' => $id_servicio,
                        'id_profesional' => $id_usuario_registra,
                        'id_usuario_registra' => $id_usuario_registra,
                        'observacion' => $observaciones,
                        'tipo_solicitante' => $tipo_solicitante,
                        'tipo_persona_sol' => $tipo,
                    ]; 

                    $add = $this->salud_model->guardar_datos($data_solicitud, 'salud_solicitudes');
                    $resp = ['mensaje'=>"La atención fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if($add == 1){
                         $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }else{
                        $id_solicitud = $this->salud_model->traer_ultima_solicitud($id_usuario_registra, 'salud_solicitudes', 'id_usuario_registra');
                        $data_estado  = [ 'id_solicitud' => $id_solicitud->{'id'},'id_estado' => 'Sal_Fin_E', 'id_usuario_registra' => $id_usuario_registra];
                        $add_estado = $this->salud_model->guardar_datos($data_estado,'salud_estados');
                    }
                }
            }
        }
        echo json_encode($resp);    
    }

    public function modificar_atencion(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_usuario_registra = $_SESSION['persona'];    
                $id_solicitud = $this->input->post("id_solicitud");            
                $id_servicio = $this->input->post("id_servicio_mod");
                $observaciones = $this->input->post("observaciones_mod");
                $str = $this->verificar_campos_string(['Servicio' => $id_servicio,'Observaciones' => $observaciones]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data_solicitud = [
                        'id_servicio' => $id_servicio,
                        'observacion' => $observaciones,
                        'id_usuario_modifica' => $id_usuario_registra,
                        'observacion_mod' => $observaciones,                      
                    ]; 
                    $mod = $this->salud_model->modificar_datos($data_solicitud, 'salud_solicitudes', $id_solicitud);
                    $resp = ['mensaje'=>"La solicitud fue gestionada con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if($mod == 1) $resp= ['mensaje'=>"Error al gestionar la solicitud, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];            
                }
            }
        }
        echo json_encode($resp); 
    }

    public function gestionar_solicitud(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id');
                $estado = $this->input->post('estado');
                $mensaje = $this->input->post('mensaje');
                $id_usuario_registra = $_SESSION['persona'];
                
                $data = ['id_estado_sol' => $estado,'motivo' => $mensaje];  
                $mod = $this->salud_model->modificar_datos($data, 'salud_solicitudes', $id_solicitud);
                $resp = ['mensaje'=>"La solicitud fue gestionada con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                if($mod == 1){
                     $resp= ['mensaje'=>"Error al gestionar la solicitud, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $data_estado  = [ 'id_solicitud' => $id_solicitud,'id_estado' => $estado, 'id_usuario_registra' => $id_usuario_registra];
                    $add = $this->salud_model->guardar_datos($data_estado, 'salud_estados');                    
                } 
            }
        }
        echo json_encode($resp);  
    }

    public function guardar_profesional_servicio(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_relacion = $this->input->post('id_idparametro');
                $id_persona = $this->input->post('id_persona');
                $id_usuario_registra = $_SESSION['persona'];                 
                $existe = $this->salud_model->consulta_servicios_profesional($id_relacion, $id_persona);
                if($existe){
                    $resp = ['mensaje'=>"El Profesional ya se encuentra asignado.",'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data_persona = [
                        'id_relacion' => $id_relacion,
                        'id_persona' => $id_persona,
                        'id_usuario_registra' => $id_usuario_registra,
                    ];
                    $add = $this->salud_model->guardar_datos($data_persona, 'salud_profesional_relacion');
                    $resp = ['mensaje'=>"El Profesional fue asignado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }     
        echo json_encode($resp); 
    }
    
    public function guardar_historia(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                 $id_usuario_registra = $_SESSION['persona'];  
                 $id_persona =  $this->input->post('id_persona');
                 $id_tipo_examen = $this->input->post('id_tipo_examen') == '' ? NULL : $this->input->post('id_tipo_examen');
                 $tipo_solicitud = $this->input->post('tipo_solicitud');
                 $id_tipo_persona = $this->input->post('id_tipo_persona');
                 $query = $this->salud_model->valor_parametro_id_aux($id_tipo_persona);
                 $tipo_solicitante = $query->{'valorx'};
                 $data_solicitud = [
                    'id_persona' => $id_persona,
                    'id_servicio' => $id_tipo_examen,
                    'id_profesional' => $id_usuario_registra,
                    'id_usuario_registra' => $id_usuario_registra,
                    'tipo_solicitante' => $tipo_solicitante,
                    'tipo_persona_sol' => $id_tipo_persona,
                    'id_tipo_solicitud' =>  $tipo_solicitud];
                    $add = $this->salud_model->guardar_datos($data_solicitud, 'salud_solicitudes');
                    if($add == 1){
                        $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }else{
                        $data = ['editando' => 0];  
                        $mod = $this->salud_model->modificar_editando($data,$id_persona,$tipo_solicitud);
                        $id_solicitud = $this->salud_model->traer_ultima_solicitud($id_usuario_registra, 'salud_solicitudes', 'id_usuario_registra');
                        $data_estado  = [ 'id_solicitud' => $id_solicitud->{'id'},'id_estado' => 'Sal_Fin_E', 'id_usuario_registra' => $id_usuario_registra];
                        $add_estado = $this->salud_model->guardar_datos($data_estado,'salud_estados');
                        $resp = ['mensaje'=>"La solicitud fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!","id" => $id_solicitud->{'id'}];                    
                    }
                }  
            }    
        echo json_encode($resp); 
    }

    public function obtener_valor_parametro(){
        $id_parametro = $this->input->post("idparametro"); 
        $resp = $this->Super_estado == true ? $this->salud_model->listar_valor_parametro($id_parametro) : array();
        echo json_encode($resp); 
    }

    public function calcular_rango_imc(){
        $id_parametro = 168;
        $imc = $this->input->post("imc"); 
        $data = $this->Super_estado == true ? $this->salud_model->listar_valor_parametro($id_parametro) : array();
        $resp = array();
        foreach ($data as $row) {
            if($row['valorx'] == 0){
                if($imc <= $row['valory']){
                    $rango = $row['valor'];
                    $resp = ['rango' => $rango];
                    break;
                }
            }else if($row['valorx'] != 0 && $row['valory'] != NULL){
                if($imc >= $row['valorx'] && $imc <= $row['valory']){
                    $rango = $row['valor'];
                    $resp = ['rango' => $rango];
                    break;
                }
            }else {
                if($imc >= $row['valory']){
                $rango = $row['valor'];
                $resp = ['rango' => $rango];
                break;
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_tablas_antecendetes(){
        $id_persona = $this->input->post('id_persona');
        $model = $this->input->post('model');
        $data = $this->Super_estado == true ? $this->salud_model->$model($id_persona) : array();
        $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
        $ver_finalizado = '<span  style="background-color: #39b23b;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
        $btn_modificar = '<span title="Modificar" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
        $valores = array();
        foreach ($data as $row) {
            $row['accion'] = $btn_modificar.' '.$btn_eliminar; 
            array_push($valores,$row);
        }
        echo json_encode($valores); 
    }    

    public function add_escolaridad(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_usuario_registra = $_SESSION['persona'];  
                $id_persona = $this->input->post('id_persona');
                $valor_param1 = $this->input->post('valor_param1');
                $valor_param2 = $this->input->post('valor_param2');
                $str = $this->verificar_campos_string(['Escolaridad' => $valor_param1,'Estado' => $valor_param2]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $val_datos = $this->salud_model->validar_dato_paciente($valor_param1, 'salud_escolaridad_paciente', 'id_escolaridad','id_persona',$id_persona);
                    if($val_datos){
                        $resp = ['mensaje'=>"El nivel de escolaridad ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        $data_solicitud = [                        
                            'id_escolaridad' => $valor_param1,
                            'id_persona' => $id_persona,
                            'id_tipo_estado' => $valor_param2,
                            'id_usuario_registra' => $id_usuario_registra];
                        $add = $this->salud_model->guardar_datos($data_solicitud, 'salud_escolaridad_paciente');
                        $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($add == 1){
                            $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }
            }
        }
        echo json_encode($resp); 
    }

    public function editar_escolaridad(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post('id_dato');
                $id_solicitud = $this->input->post('id_solicitud');
                $id_persona = $this->input->post('id_persona');
                $valor_param1 = $this->input->post('valor_param1');
                $valor_param2 = $this->input->post('valor_param2');
                $sw = false;
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Escolaridad' => $valor_param1,'Estado' => $valor_param2]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $examen = $this->salud_model->traer_ultima_solicitud($id,'salud_escolaridad_paciente','id');
                        if($examen->{'id_escolaridad'} != $valor_param1){
                            $val_datos = $this->salud_model->validar_dato_paciente($valor_param1, 'salud_escolaridad_paciente', 'id_escolaridad','id_persona',$id_persona);
                            if($val_datos){
                                $resp = ['mensaje'=>"El nivel de escolaridad ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                            }else{
                                $data['id_escolaridad'] = $valor_param1;
                                $sw = true;
                            }
                        }else if ($examen->{'id_tipo_estado'} != $valor_param2){
                            $data['id_tipo_estado'] = $valor_param2;
                            $sw = true;
                        }else{
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en la escolaridad del paciente.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }
                        if($sw){
                            $add = $this->salud_model->modificar_datos($data, 'salud_escolaridad_paciente', $id);
                            $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($add == 1)$resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }
            }
        }
        echo json_encode($resp); 
    }

    public function add_historia_laboral(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_usuario_registra = $_SESSION['persona'];  
                $id_persona = $this->input->post('id_persona');
                $empresa = $this->input->post('empresa');
                $cargo = $this->input->post('cargo');
                $fecha_hl = $this->input->post('fecha_hl');                
                $proteccion = $this->input->post('proteccion');
                $tiempo = $this->input->post('tiempo');
                $cantidad_tiempo = $this->input->post('cantidad_tiempo');
                $riesgos = $this->input->post('riesgos');
                $str = $this->verificar_campos_string(['Empresa' => $empresa,'Cargo' => $cargo,'Fecha' => $fecha_hl,'Protección' => $proteccion,'Tiempo' => $tiempo,'cantidad_tiempo' => $cantidad_tiempo]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{                    
                    $data = [                  
                        'id_persona' => $id_persona,
                        'empresa' => $empresa,
                        'cargo' => $cargo,
                        'fecha' => $fecha_hl,
                        'proteccion' => $proteccion,
                        'tiempo' => $tiempo,
                        'cantidad' => $cantidad_tiempo,
                        'id_usuario_registra' => $id_usuario_registra];
                    $add = $this->salud_model->guardar_datos($data, 'salud_historia_laboral');
                    $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    // else{
                    //     if(!empty($riesgos)){
                    //         $id_historia_laboral = $this->salud_model->traer_ultima_solicitud($id_persona, 'salud_historia_laboral', 'id_persona');
                    //         $data_riesgo = [];
                    //         if (!empty($id_historia_laboral)){
                    //             foreach ($riesgos as $key)  array_push($data_riesgo, ['id_historia_laboral' => $id_historia_laboral->{'id'},'id_riesgo' => $key['id'],'usuario_registra' => $id_usuario_registra]);
                    //             $add_riesgos = $this->salud_model->guardar_datos($data_riesgo, 'salud_riesgo_laboral',2);
                    //             if($add_riesgos == 1) $resp= ['mensaje'=>"Error al asignar los riesgos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    //         }else $resp= ['mensaje'=>"Error al obtener la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    //     }
                    // }
                }
            }
        }
        echo json_encode($resp);
    }

    public function editar_historia_laboral(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post('id_dato');
                $empresa = $this->input->post('empresa');
                $cargo = $this->input->post('cargo');
                $fecha_hl = $this->input->post('fecha_hl');                
                $proteccion = $this->input->post('proteccion');
                $tiempo = $this->input->post('tiempo');
                $cantidad_tiempo = $this->input->post('cantidad_tiempo');
                $id_persona = $this->input->post('id_persona');
                $id_solicitud = $this->input->post('id_solicitud');
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Empresa' => $empresa,'Cargo' => $cargo,'Fecha' => $fecha_hl,'Protección' => $proteccion,'Tiempo' => $tiempo,'cantidad_tiempo' => $cantidad_tiempo]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{   
                        $examen = $this->salud_model->traer_ultima_solicitud($id,'salud_historia_laboral','id');
                        if(($examen->{'empresa'} == $empresa) && ($examen->{'cargo'} == $cargo) && ($examen->{'fecha'} == $fecha_hl) && ($examen->{'proteccion'} == $proteccion) && ($examen->{'tiempo'} == $tiempo) && ($examen->{'cantidad_tiempo'} == $cantidad_tiempo)) {
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en la historia laboral.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }else{                 
                            $data = [                  
                                'empresa' => $empresa,
                                'cargo' => $cargo,
                                'fecha' => $fecha_hl,
                                'proteccion' => $proteccion,
                                'tiempo' => $tiempo,
                                'cantidad' => $cantidad_tiempo];
                            $add = $this->salud_model->modificar_datos($data, 'salud_historia_laboral', $id);
                            $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }   
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }
            }
        }
        echo json_encode($resp);
    }

    public function ver_riesgo_laboral(){
        $id = $this->input->post('id');
        $data = $this->Super_estado == true ? $this->salud_model->ver_riesgo_laboral($id) : array();
        $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
        $valores = array();
        foreach ($data as $row) {
            $row['accion'] = $btn_eliminar; 
            array_push($valores,$row);
        }
        echo json_encode($valores); 
    }

    public function guardar_riesgo(){        
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_riesgo = $this->input->post('id_riesgo'); 
                $id_historia_laboral = $this->input->post('id_historia_laboral');
                $id_usuario_registra = $_SESSION['persona']; 
                $val_datos = $this->salud_model->validar_dato_paciente($id_riesgo, 'salud_riesgo_laboral', 'id_riesgo','id_historia_laboral',$id_historia_laboral);
                if($val_datos){
                    $resp = ['mensaje'=>"El Riesgo laboral ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else{
                    $data_riesgo = [
                    'id_historia_laboral' => $id_historia_laboral,
                    'id_riesgo' => $id_riesgo,
                    'usuario_registra' => $id_usuario_registra];
                    $add_riesgos = $this->salud_model->guardar_datos($data_riesgo, 'salud_riesgo_laboral');
                    $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if($add_riesgos == 1) $resp= ['mensaje'=>"Error al asignar Riesgo, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp); 
    }

    public function add_accidente(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_usuario_registra = $_SESSION['persona'];  
                $id_persona = $this->input->post('id_persona');
                $fecha_al = $this->input->post('fecha_al');
                $id_historia_lab = $this->input->post('id_empresa');
                $incapacidad = $this->input->post('incapacidad');               
                $lesion = $this->input->post('lesion');
                $arp = $this->input->post('arp');
                $enfermedad = $this->input->post('enfermedad');
                $secuelas = $this->input->post('secuelas');
                $str = $this->verificar_campos_string(['Empresa' => $id_historia_lab,'Fecha' => $fecha_al,'Días de Incapacidad' => $incapacidad,'Lesíon' => $lesion,'ARP' => $arp,'Enfermedades Profesionales' => $enfermedad]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{                    
                    $data = [                  
                        'id_persona' => $id_persona,
                        'id_historia_laboral' => $id_historia_lab,
                        'fecha' => $fecha_al,
                        'dias_incapacidad' => $incapacidad,
                        'lesion' => $lesion,
                        'arp' => $arp,
                        'enfermedad_profesional' => $enfermedad,
                        'secuelas' => $secuelas,
                        'id_usuario_registra' => $id_usuario_registra];
                    $add = $this->salud_model->guardar_datos($data, 'salud_accidentes_laborales');
                    $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if($add == 1){
                        $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    } 
                }
            }
        }
        echo json_encode($resp); 
    }

    public function editar_accidente_laboral(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post('id_dato');  
                $id_persona = $this->input->post('id_persona');
                $id_solicitud = $this->input->post('id_solicitud');
                $fecha_al = $this->input->post('fecha_al');
                $id_historia_lab = $this->input->post('id_empresa');
                $incapacidad = $this->input->post('incapacidad');               
                $lesion = $this->input->post('lesion');
                $arp = $this->input->post('arp');
                $enfermedad = $this->input->post('enfermedad');
                $secuelas = $this->input->post('secuelas');
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Empresa' => $id_historia_lab,'Fecha' => $fecha_al,'Días de Incapacidad' => $incapacidad,'Lesíon' => $lesion,'ARP' => $arp,'Enfermedades Profesionales' => $enfermedad]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{ 
                        $examen = $this->salud_model->traer_ultima_solicitud($id,'salud_accidentes_laborales','id'); 
                        if(($examen->{'id_historia_laboral'} == $id_historia_lab) && ($examen->{'fecha'} == $fecha_al)  && ($examen->{'dias_incapacidad'} == $incapacidad) && ($examen->{'lesion'} == $lesion) && ($examen->{'arp'} == $arp) && ($examen->{'enfermedad_profesional'} == $enfermedad) && ($examen->{'secuelas'} == $secuelas)){
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en el accidente laboral.",'tipo'=>"info",'titulo'=> "Oops.!"];  
                        }else{ 
                            $data = [                  
                                'id_historia_laboral' => $id_historia_lab,
                                'fecha' => $fecha_al,
                                'dias_incapacidad' => $incapacidad,
                                'lesion' => $lesion,
                                'arp' => $arp,
                                'enfermedad_profesional' => $enfermedad,
                                'secuelas' => $secuelas];
                                $add = $this->salud_model->modificar_datos($data, 'salud_accidentes_laborales',$id);
                                $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                                if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                            }
                        }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }

            }

        }
        echo json_encode($resp);
    }

    public function cargar_empresas(){
        $id_persona = $this->input->post('id_persona');
        $data = $this->Super_estado == true ? $this->salud_model->cargar_empresas($id_persona) : array();
        echo json_encode($data);  
    }

    public function add_antfamiliar(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_usuario_registra = $_SESSION['persona'];  
                $id_persona = $this->input->post('id_persona');
                $id_solicitud = $this->input->post('id_solicitud');
                $enfermedad = $this->input->post('id_tipo_enfermedad');
                $parentesco = $this->input->post('id_parentesco');
                $observacion = $this->input->post('observacion_antf');
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Tipo de Enfermedad' => $enfermedad,'Parentesco' => $parentesco]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{   
                        $val_datos = $this->salud_model->validar_dato_paciente($enfermedad, 'salud_antecedentes_familiares', 'id_tipo_enfermedad','id_persona',$id_persona);
                        if(!empty($val_datos) && ($val_datos->{'id_parentesco'} == $parentesco)){
                            $resp = ['mensaje'=>"El antecendente y parentesco ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }else{               
                            $data = [                  
                                'id_persona' => $id_persona,
                                'id_tipo_enfermedad' => $enfermedad,
                                'id_parentesco' => $parentesco,
                                'observacion' => $observacion,
                                'id_usuario_registra' => $id_usuario_registra];
                            $add = $this->salud_model->guardar_datos($data, 'salud_antecedentes_familiares');
                            $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        } 
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }
            }
        }
        echo json_encode($resp); 
    }

    public function editar_antfamiliar(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post('id_dato');
                $id_persona = $this->input->post('id_persona');
                $id_solicitud = $this->input->post('id_solicitud');
                $enfermedad = $this->input->post('id_tipo_enfermedad');
                $parentesco = $this->input->post('id_parentesco');
                $observacion = $this->input->post('observacion_antf');
                $sw = false;
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Tipo de Enfermedad' => $enfermedad,'Parentesco' => $parentesco]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $examen = $this->salud_model->traer_ultima_solicitud($id,'salud_antecedentes_familiares','id'); 
                        if(($examen->{'id_tipo_enfermedad'} != $enfermedad) && ($examen->{'id_parentesco'} != $parentesco)){
                            $val_datos = $this->salud_model->validar_dato_paciente($enfermedad, 'salud_antecedentes_familiares', 'id_tipo_enfermedad','id_persona',$id_persona);
                            if(!empty($val_datos) && ($val_datos->{'id_parentesco'} == $parentesco)){
                                $resp = ['mensaje'=>"El antecendente y parentesco ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                            }else{
                                $data['id_tipo_enfermedad'] = $enfermedad;
                                $data['id_parentesco'] =  $parentesco;
                                $sw = true;
                            }
                        }else if($examen->{'id_tipo_enfermedad'} != $enfermedad){
                            $data['id_tipo_enfermedad'] =  $enfermedad;
                            $sw = true;
                        }else if($examen->{'id_parentesco'} != $parentesco){
                            $data['id_parentesco'] =  $parentesco;
                            $sw = true;
                        }else if($examen->{'observacion'} != $observacion){
                            $data['observacion'] =  $observacion;
                            $sw = true;
                        }else{
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en el antecendente familiar.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }
                        if($sw){
                            $add = $this->salud_model->modificar_datos($data, 'salud_antecedentes_familiares',$id);
                            $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }      
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }
            }
        }
        echo json_encode($resp);
    }

    public function add_antpersonal(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_usuario_registra = $_SESSION['persona'];  
                $id_persona = $this->input->post('id_persona');
                $antecedente = $this->input->post('id_tipo_antecedente');
                $observacion = $this->input->post('observacion_antp');
                $str = $this->verificar_campos_string(['Tipo de Antecedente' => $antecedente]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $val_datos = $this->salud_model->validar_dato_paciente($antecedente, 'salud_antecedentes_personales', 'id_tipo_antecedente','id_persona',$id_persona);
                    if($val_datos){
                        $resp = ['mensaje'=>"El antecedente ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{               
                        $data = [                  
                            'id_persona' => $id_persona,
                            'id_tipo_antecedente' => $antecedente,
                            'observacion' => $observacion,
                            'id_usuario_registra' => $id_usuario_registra];
                        $add = $this->salud_model->guardar_datos($data, 'salud_antecedentes_personales');
                        $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($add == 1){
                            $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        } 
                    }
                }
            }
        }
        echo json_encode($resp); 
    }

    public function editar_antpersonal(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post('id_dato');
                $id_persona = $this->input->post('id_persona');
                $id_solicitud = $this->input->post('id_solicitud');
                $antecedente = $this->input->post('id_tipo_antecedente');
                $observacion = $this->input->post('observacion_antp');
                $sw = false;
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Tipo de Antecedente' => $antecedente]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $examen = $this->salud_model->traer_ultima_solicitud($id,'salud_antecedentes_personales','id');
                        if($examen->{'id_tipo_antecedente'} != $antecedente){
                            $val_datos = $this->salud_model->validar_dato_paciente($antecedente, 'salud_antecedentes_personales', 'id_tipo_antecedente','id_persona',$id_persona);
                            if($val_datos){
                                $resp = ['mensaje'=>"El antecedente ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                            }else{ 
                                $data['id_tipo_antecedente'] = $antecedente;
                                $sw = true;
                            }
                        }else if($examen->{'observacion'} != $observacion){
                            $data['observacion'] = $observacion;
                            $sw = true;
                        }else{
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en el antecendente personal.",'tipo'=>"info",'titulo'=> "Oops.!"];                   
                        }                
                        if($sw){
                            $add = $this->salud_model->modificar_datos($data, 'salud_antecedentes_personales',$id);
                            $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($add == 1){
                                $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                            } 
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }
            }
        }
        echo json_encode($resp);
    }

    public function add_vacuna(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_usuario_registra = $_SESSION['persona'];  
                $id_persona = $this->input->post('id_persona');
                $vacuna = $this->input->post('id_vacuna');
                $observacion = $this->input->post('observacion_vacuna');
                $str = $this->verificar_campos_string(['Vacuna' => $vacuna]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{  
                    $val_datos = $this->salud_model->validar_dato_paciente($vacuna, 'salud_vacuna_paciente', 'id_vacuna','id_persona',$id_persona);
                    if($val_datos){
                        $resp = ['mensaje'=>"La vacuna ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{                
                        $data = [                  
                            'id_persona' => $id_persona,
                            'id_vacuna' => $vacuna,
                            'observacion' => $observacion,
                            'id_usuario_registra' => $id_usuario_registra];
                        $add = $this->salud_model->guardar_datos($data, 'salud_vacuna_paciente');
                        $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    } 
                }                
            }
        }
        echo json_encode($resp); 
    }

    public function editar_vacunas(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post('id_dato');
                $id_persona = $this->input->post('id_persona');
                $id_solicitud = $this->input->post('id_solicitud');
                $vacuna = $this->input->post('id_vacuna');
                $observacion = $this->input->post('observacion_vacuna');
                $sw = false;
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                   $str = $this->verificar_campos_string(['Vacuna' => $vacuna]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $examen = $this->salud_model->traer_ultima_solicitud($id,'salud_vacuna_paciente','id');
                        if($examen->{'id_vacuna'} != $vacuna) {
                            $val_datos = $this->salud_model->validar_dato_paciente($vacuna, 'salud_vacuna_paciente', 'id_vacuna','id_persona',$id_persona);
                            if($val_datos){
                                $resp = ['mensaje'=>"La vacuna ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                            }else{ 
                                $data['id_vacuna'] = $vacuna;
                                $sw = true;
                            }
                        }else if($examen->{'observacion'} != $observacion){  
                            $data['observacion'] = $observacion;
                            $sw = true;
                        }else{
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en las vacunas.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        } 
                        if($sw){               
                            $add = $this->salud_model->modificar_datos($data, 'salud_vacuna_paciente',$id);
                            $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }   
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }    
            }
        }
        echo json_encode($resp);
    }

    public function add_ant_gineco(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_usuario_registra = $_SESSION['persona'];  
                $id_persona = $this->input->post('id_persona');
                $menarquia = $this->input->post('menarquia');
                $ciclos = $this->input->post('ciclos');
                $fur = $this->input->post('fur');
                $cantidad_g = $this->input->post('cantidad_g');
                $cantidad_p = $this->input->post('cantidad_p');
                $cantidad_c = $this->input->post('cantidad_c');
                $cantidad_a = $this->input->post('cantidad_a');
                $cantidad_v = $this->input->post('cantidad_v');
                // $fup = $this->input->post('fup');
                $planifica = $this->input->post('planifica');
                $tipo_planificacion = $this->input->post('tipo_planificacion');
                $dismenorreas = $this->input->post('dismenorreas');
                $fecha_citologia = $this->input->post('fecha_citologia');
                $tipo_citologia = $this->input->post('tipo_citologia');
                $observacion = $this->input->post('observacion_gineco');
                $str = $this->verificar_campos_string(['Tipo Planificación' => $tipo_planificacion,'Menarquia' => $menarquia,'Ciclos' => $ciclos,'FUR' => $fur,'Planificación' => $planifica,'Dismenorreas' => $dismenorreas,]);
                $str = $this->verificar_campos_numericos(['Cantidad Gestaciones' => $cantidad_g,'Cantidad Partos' => $cantidad_p,'Cantidad Cesarea' => $cantidad_c,'Cantidad Abortos' => $cantidad_a,'Cantidad Vivo' => $cantidad_v ]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{                  
                    $data = [                  
                        'id_persona' => $id_persona,
                        'menarquia' => $menarquia,
                        'ciclos' => $ciclos,
                        'fur' => $fur,
                        // 'fup' => $fup,
                        'cantidad_gestaciones' => $cantidad_g,
                        'cantidad_partos' => $cantidad_p, 
                        'cantidad_cesarea' => $cantidad_c, 
                        'cantidad_abortos' => $cantidad_a, 
                        'cantidad_vivo' => $cantidad_v,
                        'planifica' => $planifica, 
                        'id_tipo_planificacion' => $tipo_planificacion,
                        'dismenorreas' => $dismenorreas,
                        'fecha_ultima_citologia' => $fecha_citologia,
                        'citologia_normal' => $tipo_citologia,
                        'observacion' => $observacion,
                        'id_usuario_registra' => $id_usuario_registra];
                    $add = $this->salud_model->guardar_datos($data, 'salud_antecedentes_gineco');
                    $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if($add == 1){
                        $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    } 
                }
            }
        }
        echo json_encode($resp); 
    }

    public function editar_ant_gineco(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post('id_dato');
                $id_persona = $this->input->post('id_persona');
                $id_solicitud = $this->input->post('id_solicitud');
                $menarquia = $this->input->post('menarquia');
                $ciclos = $this->input->post('ciclos');
                $fur = $this->input->post('fur');
                $cantidad_g = $this->input->post('cantidad_g');
                $cantidad_p = $this->input->post('cantidad_p');
                $cantidad_c = $this->input->post('cantidad_c');
                $cantidad_a = $this->input->post('cantidad_a');
                $cantidad_v = $this->input->post('cantidad_v');
                // $fup = $this->input->post('fup');
                $planifica = $this->input->post('planifica');
                $tipo_planificacion = $this->input->post('tipo_planificacion');
                $dismenorreas = $this->input->post('dismenorreas');
                $fecha_citologia = $this->input->post('fecha_citologia');
                $tipo_citologia = $this->input->post('tipo_citologia');
                $observacion = $this->input->post('observacion_gineco');
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Tipo Planificación' => $tipo_planificacion,'Menarquia' => $menarquia,'Ciclos' => $ciclos,'FUR' => $fur,'Planificación' => $planifica,'Dismenorreas' => $dismenorreas,]);
                    $str = $this->verificar_campos_numericos(['Cantidad Gestaciones' => $cantidad_g,'Cantidad Partos' => $cantidad_p,'Cantidad Cesarea' => $cantidad_c,'Cantidad Abortos' => $cantidad_a,'Cantidad Vivo' => $cantidad_v ]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $examen = $this->salud_model->traer_ultima_solicitud($id,'salud_antecedentes_gineco','id');
                        if(($examen->{'menarquia'} == $menarquia) && ($examen->{'ciclos'} == $ciclos) && ($examen->{'fur'} == $fur) && ($examen->{'cantidad_gestaciones'} == $cantidad_g) && ($examen->{'cantidad_partos'} == $cantidad_p) && ($examen->{'cantidad_cesarea'} == $cantidad_c) && ($examen->{'cantidad_abortos'} == $cantidad_a) && ($examen->{'cantidad_vivo'} == $cantidad_v)
                            && ($examen->{'planifica'} == $planifica) && ($examen->{'id_tipo_planificacion'} == $tipo_planificacion) && ($examen->{'dismenorreas'} == $dismenorreas) && ($examen->{'fecha_ultima_citologia'} == $fecha_citologia) && ($examen->{'citologia_normal'} == $tipo_citologia) && ($examen->{'observacion'} == $observacion)) {
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en el antecendete.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }else{                 
                            $data = [                  
                                'menarquia' => $menarquia,
                                'ciclos' => $ciclos,
                                'fur' => $fur,
                                // 'fup' => $fup,
                                'cantidad_gestaciones' => $cantidad_g,
                                'cantidad_partos' => $cantidad_p, 
                                'cantidad_cesarea' => $cantidad_c, 
                                'cantidad_abortos' => $cantidad_a, 
                                'cantidad_vivo' => $cantidad_v,
                                'planifica' => $planifica, 
                                'id_tipo_planificacion' => $tipo_planificacion,
                                'dismenorreas' => $dismenorreas,
                                'fecha_ultima_citologia' => $fecha_citologia,
                                'citologia_normal' => $tipo_citologia,
                                'observacion' => $observacion];
                            $add = $this->salud_model->modificar_datos($data, 'salud_antecedentes_gineco',$id);
                            $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }
            }
        }
        echo json_encode($resp); 
    }

    public function add_habito(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_usuario_registra = $_SESSION['persona'];  
                $id_persona = $this->input->post('id_persona');
                $habito =  $this->input->post('habito');
                $frecuencia = $this->input->post('id_frecuencia');
                $tipo = $this->input->post('tipo_ejercicio');
                $cantidad = $this->input->post('cantidad');
                if($cantidad == '') $cantidad = null;
                $fecha_desde = $this->input->post('fecha_desde');
                $fecha_hasta = $this->input->post('fecha_hasta');
                $duracion = $this->input->post('id_duracion');
                if($duracion == '') $duracion = null;
                $str = $this->verificar_campos_string(['Hábito' => $habito,'Fecha Desde' => $fecha_desde,'Fecha Hasta' => $fecha_hasta,'Frecuencia' => $frecuencia]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{ 
                    $val_datos = $this->salud_model->validar_dato_paciente($habito, 'salud_habitos_paciente', 'id_habito','id_persona',$id_persona);
                    if($val_datos){
                        $resp = ['mensaje'=>"El Hábito ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        $data = [                  
                            'id_persona' => $id_persona,
                            'id_habito' => $habito,
                            'id_frecuencia' => $frecuencia,
                            'tipo' => $tipo,
                            'cantidad' => $cantidad,
                            'fecha_desde' => $fecha_desde,
                            'fecha_hasta' => $fecha_hasta,
                            'id_duracion' => $duracion,
                            'id_usuario_registra' => $id_usuario_registra];
                        $add = $this->salud_model->guardar_datos($data, 'salud_habitos_paciente');
                        $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($add == 1){
                            $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function editar_habito(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post('id_dato');
                $id_persona = $this->input->post('id_persona');
                $id_solicitud = $this->input->post('id_solicitud');
                $habito =  $this->input->post('habito');
                $frecuencia = $this->input->post('id_frecuencia');
                $tipo = $this->input->post('tipo_ejercicio');
                $cantidad = $this->input->post('cantidad');
                if($cantidad == '') $cantidad = null;
                $fecha_desde = $this->input->post('fecha_desde');
                $fecha_hasta = $this->input->post('fecha_hasta');
                $duracion = $this->input->post('id_duracion');
                $sw = true;
                if($duracion == '') $duracion = null;
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Hábito' => $habito,'Fecha Desde' => $fecha_desde,'Fecha Hasta' => $fecha_hasta,'Frecuencia' => $frecuencia]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{ 
                        $examen = $this->salud_model->traer_ultima_solicitud($id,'salud_habitos_paciente','id');
                        if(($examen->{'id_habito'} == $habito) && ($examen->{'id_frecuencia'} == $frecuencia) && ($examen->{'fecha_desde'} == $fecha_desde) && ($examen->{'fecha_hasta'} == $fecha_hasta) && ($examen->{'tipo'} == $tipo) && ($examen->{'cantidad'} == $cantidad) && ($examen->{'id_duracion'} == $duracion)) {
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en el hábito.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }else{
                            $data = ['id_frecuencia' => $frecuencia,
                                'tipo' => $tipo,
                                'cantidad' => $cantidad,
                                'fecha_desde' => $fecha_desde,
                                'fecha_hasta' => $fecha_hasta,
                                'id_duracion' => $duracion];
                            if($examen->{'id_habito'} != $habito){
                                $val_datos = $this->salud_model->validar_dato_paciente($habito, 'salud_habitos_paciente', 'id_habito','id_persona',$id_persona);
                                if($val_datos){
                                    $resp = ['mensaje'=>"El Hábito ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                                    $sw = false;
                                }else{
                                    $data['id_habito'] =$habito;
                                }
                            }else{
                                $data['id_habito'] =$habito;
                            }                        
                            if($sw){    
                                $add = $this->salud_model->modificar_datos($data, 'salud_habitos_paciente',$id);
                                $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                                if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                            }
                         }
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }
             }
        }    
        echo json_encode($resp);
    }

    public function add_revision_sistemas(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $sistema = $this->input->post('id_sistema');
                $observacion = $this->input->post('observacion_rev');
                $id_usuario_registra = $_SESSION['persona'];  
                $str = $this->verificar_campos_string(['Sistema/Órgano' => $sistema]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{ 
                    $val_datos = $this->salud_model->validar_dato_paciente($sistema, 'salud_revision_sistema', 'id_tipo_sistema','id_solicitud',$id_solicitud);
                    if($val_datos){
                        $resp = ['mensaje'=>"El Sistema - Órgano ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        $data = [                  
                            'id_solicitud' => $id_solicitud,
                            'id_tipo_sistema' => $sistema,
                            'observacion' => $observacion,
                            'id_usuario_registra' => $id_usuario_registra];
                        $add = $this->salud_model->guardar_datos($data, 'salud_revision_sistema');
                        $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($add == 1){
                            $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function editar_revision_sistemas(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $id = $this->input->post('id_dato');
                $sistema = $this->input->post('id_sistema');
                $observacion = $this->input->post('observacion_rev');
                $id_persona = $this->input->post('id_persona');
                $sw = false; 
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){          
                    $examen = $this->salud_model->traer_ultima_solicitud($id,'salud_revision_sistema','id');
                    if($examen->{'id_tipo_sistema'} != $sistema){
                        $val_datos = $this->salud_model->validar_dato_paciente($sistema, 'salud_revision_sistema', 'id_tipo_sistema','id_solicitud',$id_solicitud);
                        if($val_datos){
                            $resp = ['mensaje'=>"El Sistema - Órgano ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }else{
                            $data['id_tipo_sistema'] = $sistema;
                            $sw = true;
                        }
                    }else if($examen->{'observacion'} != $observacion){
                        $data['observacion'] = $observacion;
                        $sw = true;
                    }else{
                        $resp = ['mensaje'=>"Debe realizar alguna modificación en la revisión por sistemas.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }
                    if($sw){
                        $add = $this->salud_model->modificar_datos($data, 'salud_revision_sistema',$id);
                        $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }     
            }
        }
        echo json_encode($resp);
    }

    public function add_examen_fisico(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $id_tipo_examen = $this->input->post('id_tipo_examen');
                $estado = $this->input->post('id_estado_examenf');
                $observacion = $this->input->post('observacion_examen_fisico');
                $id_usuario_registra = $_SESSION['persona'];  
                $str = $this->verificar_campos_string(['Tipo Examen' => $id_tipo_examen,'Estado' => $estado]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{ 
                    $val_datos = $this->salud_model->validar_dato_paciente($id_tipo_examen, 'salud_examen_fisico', 'id_tipo_examen','id_solicitud',$id_solicitud);
                    if($val_datos){
                        $resp = ['mensaje'=>"El Examen ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        $data = [                  
                            'id_solicitud' => $id_solicitud,
                            'id_tipo_examen' => $id_tipo_examen,
                            'id_tipo_estado' => $estado,
                            'observacion' => $observacion,
                            'id_usuario_registra' => $id_usuario_registra];
                        $add = $this->salud_model->guardar_datos($data, 'salud_examen_fisico');
                        $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($add == 1){
                            $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function editar_examen_fisico(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post('id_dato');
                $id_solicitud = $this->input->post('id_solicitud');
                $id_tipo_examen = $this->input->post('id_tipo_examen');
                $estado = $this->input->post('id_estado_examenf');
                $observacion = $this->input->post('observacion_examen_fisico');
                $id_persona = $this->input->post('id_persona');
                $sw = false;
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){  
                    $str = $this->verificar_campos_string(['Tipo Examen' => $id_tipo_examen,'Estado' => $estado]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{ 
                        $examen = $this->salud_model->traer_ultima_solicitud($id,'salud_examen_fisico','id');
                        if($examen->{'id_tipo_examen'} != $id_tipo_examen){
                            $val_datos = $this->salud_model->validar_dato_paciente($id_tipo_examen, 'salud_examen_fisico', 'id_tipo_examen','id_solicitud',$id_solicitud);
                            if($val_datos){
                                $resp = ['mensaje'=>"El Examen ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                            }else{
                                $data['id_tipo_examen'] = $id_tipo_examen;
                                $sw = true;
                            }
                        }else if($examen->{'id_tipo_estado'} != $estado){
                            $data['id_tipo_estado'] = $estado;
                            $sw = true;
                        }else if($examen->{'observacion'} != $observacion){
                            $data['observacion'] = $observacion;
                            $sw = true;
                        }else{
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en el examen físico.",'tipo'=>"info",'titulo'=> "Oops.!"];                    
                        }
                        if($sw){
                            $add = $this->salud_model->modificar_datos($data, 'salud_examen_fisico', $id);
                            $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }    
            }
        }
        echo json_encode($resp);
    }

    public function add_signos_vitales(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_persona = $this->input->post('id_persona');
                $id_solicitud = $this->input->post('id_solicitud');
                $peso = $this->input->post('peso');
                $talla = $this->input->post('talla');
                $temp = $this->input->post('temp');
                $ta_sistolica = $this->input->post('ta_sistolica');
                $ta_diastolica = $this->input->post('ta_diastolica');
                $fr = $this->input->post('fr');
                $fc =  $this->input->post('fc');
                $id_mano = $this->input->post('id_mano');
                $detalle = $this->input->post('detalle');
                $id_usuario_registra = $_SESSION['persona'];                
                $str = $this->verificar_campos_string(['Peso' => $peso,'Talla' => $talla,'Temperatura' => $temp,'Tension Arterial Sistólica' => $ta_sistolica,'Tension Arterial Diastólica' => $ta_diastolica,'Frecuencia Respiratoria' => $fr,'Frecuencia Cardiaca' => $fc,'Mano Dominante' => $id_mano]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $num=explode(".",$talla);
                    if (count($num) <= 1){
                        $resp = ['mensaje'=>"El campo Talla debe ser decimal separado por punto.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $data = [                
                            'id_solicitud' => $id_solicitud,
                            'peso' => $peso,
                            'talla' => $talla,
                            'temperatura' => $temp,
                            'ta_sistolica' => $ta_sistolica,
                            'ta_diastolica' => $ta_diastolica,
                            'frecuencia_cardiaca' => $fc,
                            'frecuencia_respiratoria' => $fr,
                            'observacion' => $detalle,
                            'mano_dominante' => $id_mano,
                            'id_usuario_registra' => $id_usuario_registra];
                        $add = $this->salud_model->guardar_datos($data, 'salud_signos_vitales');
                        $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($add == 1){
                            $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }                    
                }
            }
        }
        echo json_encode($resp);
    }

    public function editar_signos_vitales(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $peso = $this->input->post('peso');
                $talla = $this->input->post('talla');
                $temp = $this->input->post('temp');
                $ta_sistolica = $this->input->post('ta_sistolica');
                $ta_diastolica = $this->input->post('ta_diastolica');
                $fr = $this->input->post('fr');
                $fc =  $this->input->post('fc');
                $id_mano = $this->input->post('id_mano');
                $detalle = $this->input->post('detalle');
                $id_usuario_registra = $_SESSION['persona'];
                $id_persona = $this->input->post('id_persona');
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){               
                    $str = $this->verificar_campos_string(['Peso' => $peso,'Talla' => $talla,'Temperatura' => $temp,'Tension Arterial Sistólica' => $ta_sistolica,'Tension Arterial Diastólica' => $ta_diastolica,'Frecuencia Respiratoria' => $fr,'Frecuencia Cardiaca' => $fc,'Mano Dominante' => $id_mano]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $num=explode(".",$talla);
                        if (count($num) <= 1){
                            $resp = ['mensaje'=>"El campo Talla debe ser decimal separado por punto.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else{
                            $examen = $this->salud_model->traer_ultima_solicitud($id_solicitud,'salud_signos_vitales','id_solicitud');
                            if(($examen->{'peso'} == $peso) && ($examen->{'talla'} == $talla) && ($examen->{'temperatura'} == $temp) && ($examen->{'ta_sistolica'} == $ta_sistolica) && ($examen->{'ta_diastolica'} == $ta_diastolica) && ($examen->{'frecuencia_cardiaca'} == $fc) && ($examen->{'frecuencia_respiratoria'} == $fr) && ($examen->{'observacion'} == $detalle) && ($examen->{'mano_dominante'} == $id_mano)) {
                                $resp = ['mensaje'=>"Debe realizar alguna modificación en los signos vitales.",'tipo'=>"info",'titulo'=> "Oops.!"];
                            }else{
                                $data = ['peso' => $peso,
                                        'talla' => $talla,
                                        'temperatura' => $temp,
                                        'ta_sistolica' => $ta_sistolica,
                                        'ta_diastolica' => $ta_diastolica,
                                        'frecuencia_cardiaca' => $fc,
                                        'frecuencia_respiratoria' => $fr,
                                        'observacion' => $detalle,
                                        'mano_dominante' => $id_mano];
                                $add = $this->salud_model->modificar_datos($data, 'salud_signos_vitales', $examen->{'id'});
                                $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                                if($add == 1)$resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                            }
                        }                    
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }
            }
        }
        echo json_encode($resp);
    }

    public function add_examenpar(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $examen = $this->input->post('id_examenpar');
                $estado = $this->input->post('id_estado_examen');
                $observacion = $this->input->post('observacion_paraclinicos');
                $id_usuario_registra = $_SESSION['persona'];
                $adjunto = null;
                $nombre_adjunto = null;
                $sw = true;  
                $str = $this->verificar_campos_string(['Examen Paraclínico' => $examen, 'Estado' => $estado]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{ 
                    $val_datos = $this->salud_model->validar_dato_paciente($examen, 'salud_examenes_paraclinicos', 'id_tipo_examen_par','id_solicitud',$id_solicitud);
                    if($val_datos){
                        $resp = ['mensaje'=>"El Examen ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        $file = $this->cargar_archivo("adjunto_resultado", $this->ruta_adjuntos, 'resultado');
                        if ($file[0] == -1){
                            $error = $file[1];
                            if ($error == "<p>You did not select a file to upload.</p>") {
                                $adjunto = null;
                                $nombre_adjunto = null;
                                $sw = true;
                            }else{
                                $resp = ['mensaje'=>"Error al cargar soporte.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                                $sw = false;
                            } 
                        }else{
                            $adjunto = $file[1];
                            $nombre_adjunto = $_FILES["adjunto_resultado"]["name"];
                        }

                        if($sw){
                            $data = [                  
                                'id_solicitud' => $id_solicitud,
                                'id_tipo_examen_par' => $examen,
                                'id_estado_examen' => $estado,
                                'observacion' => $observacion,
                                'adjunto' => $adjunto,
                                'nombre_real' =>  $nombre_adjunto,
                                'id_usuario_registra' => $id_usuario_registra];
                            $add = $this->salud_model->guardar_datos($data, 'salud_examenes_paraclinicos');
                            $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($add == 1){
                                $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                            }
                        }
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function editar_examenpar(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $id = $this->input->post('id_dato');
                $id_tipo_examen = $this->input->post('id_examenpar');
                $estado = $this->input->post('id_estado_examen');
                $observacion = $this->input->post('observacion_paraclinicos'); 
                $id_persona = $this->input->post('id_persona');
                $sw = false; 
                $adjunto = null;
                $nombre_adjunto = null;
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Examen Paraclínico' => $id_tipo_examen, 'Estado' => $estado]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $examen = $this->salud_model->traer_ultima_solicitud($id,'salud_examenes_paraclinicos','id');
                        
                        $file = $this->cargar_archivo("adjunto_resultado", $this->ruta_adjuntos, 'resultado');
                        if ($file[0] == -1){
                            $error = $file[1];
                            if ($error == "<p>You did not select a file to upload.</p>") {
                                $adjunto = $examen->{'adjunto'};
                                $nombre_adjunto = $examen->{'nombre_real'};
                                $sw = true;
                            }else{
                                $resp = ['mensaje'=>"Error al cargar soporte.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                                $sw = false;
                            } 
                        }else{
                            $adjunto = $file[1];
                            $nombre_adjunto = $_FILES["adjunto_resultado"]["name"];
                            $sw = true;
                        }

                        if($examen->{'id_tipo_examen_par'} != $id_tipo_examen){
                            $val_datos = $this->salud_model->validar_dato_paciente($id_tipo_examen, 'salud_examenes_paraclinicos', 'id_tipo_examen_par','id_solicitud',$id_solicitud);
                            if($val_datos){
                                $resp = ['mensaje'=>"El Examen ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                                $sw = false;
                            }else{
                                $data['id_tipo_examen_par'] = $id_tipo_examen;
                                $sw = true;
                            }
                        }else if($examen->{'id_estado_examen'} != $estado){
                                $data['id_estado_examen'] = $estado;
                                $sw = true;
                        }else if($examen->{'observacion'} != $observacion){
                                $data['observacion'] = $observacion;
                                $sw = true;
                        }else{
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en el examen.",'tipo'=>"info",'titulo'=> "Oops.!"];
                            $sw = false;
                        }

                        if($sw){
                            $data['adjunto'] = $adjunto;
                            $data['nombre_real'] = $nombre_adjunto;   
                            $add = $this->salud_model->modificar_datos($data, 'salud_examenes_paraclinicos', $id);
                            $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }
            }
        }
        echo json_encode($resp);
    }

    public function add_diagnostico(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $id_diagnostico = $this->input->post('id_dato');
                $id_usuario_registra = $_SESSION['persona'];
                $id_persona = $this->input->post('id_persona');
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $val_datos = $this->salud_model->validar_dato_paciente($id_diagnostico, 'salud_diagnosticos_paciente', 'id_diagnostico','id_solicitud',$id_solicitud);
                    if($val_datos){
                        $resp = ['mensaje'=>"El Diagnóstico ya se encuentra asignado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        $data = [                  
                            'id_solicitud' => $id_solicitud,
                            'id_diagnostico' => $id_diagnostico,
                            'id_usuario_registra' => $id_usuario_registra];
                        $add = $this->salud_model->guardar_datos($data, 'salud_diagnosticos_paciente');
                        $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($add == 1)$resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }    

            }
        }
        echo json_encode($resp);
    }

    public function buscar_diagnostico(){
        $resp = array();
        $dato = $this->input->post("data");
        $id_solicitud = $this->input->post("solicitud");
        if (!empty($dato)) $resp = $this->Super_estado == true ? $this->salud_model->buscar_diagnostico($dato,$id_solicitud) : array();
        echo json_encode($resp);
    }

    public function listar_tablas_historia(){
        $id_solicitud = $this->input->post('id_solicitud');
        $model = $this->input->post('model');
        $data = $this->Super_estado == true ? $this->salud_model->$model($id_solicitud) : array();
        $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
        $btn_modificar = '<span title="Modificar" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
        $valores = array();
        if($model != 'listar_diagnosticos'){
                $accion = $btn_modificar.' '.$btn_eliminar; 
            }else{
                $accion = $btn_eliminar; 
            }
        foreach ($data as $row) {
            $row['accion'] = $accion; 
            array_push($valores,$row);
        }
        echo json_encode($valores);
    }

    public function eliminar_dato_paciente(){       
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_elimina == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $resp = array();
                $id = $this->input->post('id');
                $tabla = $this->input->post('tabla_salud');
                $id_usuario_registra = $_SESSION['persona'];
                $fecha = date("Y-m-d H:i");
                $data = [                
                'id_usuario_elimina' => $id_usuario_registra,
                'fecha_elimina' => $fecha,
                'estado'  => 0];
                $del = $this->salud_model->modificar_datos($data, $tabla, $id);
                if($del == 1) $resp = ['mensaje'=>"Error al gestionar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }
        }
     echo json_encode($resp);
    }

    public function traer_ultima_atencion(){ 
        $id = $this->input->post('id_buscar');
        $tabla = $this->input->post('tabla_salud');
        $col = $this->input->post('filtro');
        $resp = $this->Super_estado == true ? $this->salud_model->traer_ultima_solicitud($id,$tabla,$col) : array();
        echo json_encode($resp);
    }

    public function validar_ultima_atencion(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id_persona = $this->input->post('id_persona');
            $tipo_solicitud = $this->input->post('tipo_solicitud');
            $id_aux = 'Tiem_Mod_salud';
            $id_parametro = 20;            
            $valor = $this->salud_model->valor_parametro_id_aux($id_aux, $id_parametro);
            $duracion_min = $valor->{'valor'};
            $resp = ['editando'=> 0,'solicitud' => ''];
            $solicitud = $this->salud_model->validar_ultima_atencion($id_persona,$tipo_solicitud);
            if(!empty($solicitud)){
                $fecha_registra = $solicitud->{'fecha_registra'};
                $fecha_actual = date("Y-m-d H:i:s");
                $fecha_fin = $this->obtener_fecha_fin($fecha_registra, $duracion_min,'Y-m-d H:i:s');
                if($fecha_fin >= $fecha_actual){
                    $resp = ['editando'=> 1,'solicitud' => $solicitud->{'id'}];
                }
            }
        }
        echo json_encode($resp); 
    }

    public function editar_ultima_atencion(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $resp = array();
                $id_solicitud = $this->input->post('id_solicitud');
                $val = $this->salud_model->traer_ultima_solicitud($id_solicitud,'salud_solicitudes','id');
                if($val->{'editando'} == 0){
                    $data_solicitud = ['editando' => 1];
                    $mod = $this->salud_model->modificar_datos($data_solicitud, 'salud_solicitudes', $id_solicitud);
                    if($mod == 1){
                          $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }else $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!",'editando' => 1];
                }
            }
        }  
     echo json_encode($resp);
    }

    public function add_dato_familiar(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $id_persona = $this->input->post('id_persona');
                $nombre_acomp = $this->input->post('nombre_acomp');
                $telefono_acomp = $this->input->post('telefono_acomp');
                $nombre_resp = $this->input->post('nombre_resp');
                $telefono_resp = $this->input->post('telefono_resp');
                $id_parentesco = $this->input->post('id_parentesco_hm');
                $id_usuario_registra = $_SESSION['persona'];
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Nombre acompañante' => $nombre_acomp, 'Teléfono acompañante' => $telefono_acomp,'Nombre persona responsable' => $nombre_resp, 'Teléfono persona responsable' => $telefono_resp, 'Parenresco' => $id_parentesco]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{ 
                        $solicitud = $this->salud_model->traer_ultima_solicitud($id_solicitud, 'salud_datos_acompanante', 'id_solicitud');
                        if(!empty($solicitud)){
                            if(($solicitud->{'nombre_acomp'} == $nombre_acomp) && ($solicitud->{'telefono_acomp'} == $telefono_acomp) && ($solicitud->{'nombre_resp'} == $nombre_resp) && ($solicitud->{'telefono_resp'} == $telefono_resp) && ($solicitud->{'id_parentesco'} == $id_parentesco)) {
                                $resp = ['mensaje'=>"Debe realizar alguna modificación en los datos.",'tipo'=>"info",'titulo'=> "Oops.!"];
                            }else{                   
                                $data_solicitud = [              
                                    'nombre_acomp' => $nombre_acomp,
                                    'telefono_acomp' => $telefono_acomp,
                                    'nombre_resp' => $nombre_resp,
                                    'telefono_resp' => $telefono_resp,
                                    'id_parentesco' => $id_parentesco,
                                    'id_usuario_registra' => $id_usuario_registra];
                                $mod = $this->salud_model->modificar_datos($data_solicitud, 'salud_datos_acompanante', $solicitud->{'id'});
                                $resp = ['mensaje'=>"La información fue gestionada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                                if($mod == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                            }
                        }else{
                            $data_solicitud = [                
                                'id_solicitud' => $id_solicitud,
                                'nombre_acomp' => $nombre_acomp,
                                'telefono_acomp' => $telefono_acomp,
                                'nombre_resp' => $nombre_resp,
                                'telefono_resp' => $telefono_resp,
                                'id_parentesco' => $id_parentesco,
                                'id_usuario_registra' => $id_usuario_registra];
                            $add = $this->salud_model->guardar_datos($data_solicitud, 'salud_datos_acompanante');
                            $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($add == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }    
            }
        }
        echo json_encode($resp);
    }

    public function add_anamnesis(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $id_persona = $this->input->post('id_persona');
                $motivo_consulta = $this->input->post('motivo_consulta');
                $enfermedad_actual = $this->input->post('enfermedad_actual');
                $id_usuario_registra = $_SESSION['persona'];
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Motivo de la Consulta' => $motivo_consulta, 'Enfermedad Actual' => $enfermedad_actual]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{ 
                        $solicitud = $this->salud_model->traer_ultima_solicitud($id_solicitud, 'salud_solicitudes', 'id');
                        if(($solicitud->{'motivo_consulta'} == $motivo_consulta) && ($solicitud->{'enfermedad_actual'} == $enfermedad_actual)) {
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en los datos.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }else{                   
                            $data_solicitud = [                
                            'motivo_consulta' => $motivo_consulta,
                            'enfermedad_actual' => $enfermedad_actual,
                            'id_usuario_modifica' => $id_usuario_registra];
                            $mod = $this->salud_model->modificar_datos($data_solicitud, 'salud_solicitudes', $id_solicitud);
                            $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($mod == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }    
            }
        }
        echo json_encode($resp);
    }


    public function add_plan_terapeutico(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $id_persona = $this->input->post('id_persona');
                $plan = $this->input->post('plan');
                $id_usuario_registra = $_SESSION['persona'];
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Plan Terapéutico' => $plan]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{ 
                        $solicitud = $this->salud_model->traer_ultima_solicitud($id_solicitud, 'salud_solicitudes', 'id');
                        if($solicitud->{'control'} == $plan) {
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en el plan terapéutico.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }else{                   
                            $data_solicitud = [                
                            'control' => $plan,
                            'id_usuario_modifica' => $id_usuario_registra];
                            $mod = $this->salud_model->modificar_datos($data_solicitud, 'salud_solicitudes', $id_solicitud);
                            $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($mod == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }    
            }
        }
        echo json_encode($resp);
    }

    public function add_valoracion(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $id_persona = $this->input->post('id_persona');
                $aplazamiento = $this->input->post('aplazamiento');
                $recomendaciones = $this->input->post('recomendaciones');
                $valoracion = $this->input->post('valoracion');
                $control = $this->input->post('control_valoracion');
                $id_usuario_registra = $_SESSION['persona'];
                $dispo = $this->validar_tiempo_edicion($id_solicitud,'Tiem_Mod_salud');
                if($dispo){
                    $str = $this->verificar_campos_string(['Resultado de Valoración' => $valoracion]);
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{ 
                        $solicitud = $this->salud_model->traer_ultima_solicitud($id_solicitud, 'salud_solicitudes', 'id');
                        if(($solicitud->{'aplazamiento'} == $aplazamiento) && ($solicitud->{'recomendaciones'} == $recomendaciones) && ($solicitud->{'valoracion'} == $valoracion) && ($solicitud->{'control'} == $control)) {
                            $resp = ['mensaje'=>"Debe realizar alguna modificación en la valoración.",'tipo'=>"info",'titulo'=> "Oops.!"];
                        }else{                   
                            $data_solicitud = [                
                            'aplazamiento' => $aplazamiento,
                            'valoracion' => $valoracion,
                            'recomendaciones'  => $recomendaciones,
                            'control'  => $control,
                            'id_usuario_modifica' => $id_usuario_registra];
                            $mod = $this->salud_model->modificar_datos($data_solicitud, 'salud_solicitudes', $id_solicitud);
                            $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                            if($mod == 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"El tiempo para la edición de ha agotado, contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!",'editando'=> 0];
                }    
            }
        }
        echo json_encode($resp);
    }


    public function modificar_paciente(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                 $data_paciente = array();
                 $id_persona =  $this->input->post('id_persona');
                 $id_solicitud = $this->input->post('id_solicitud');
                 $fecha_nacimiento = $this->input->post('fecha_nacimiento_hmo'); 
                 $genero_hmo = $this->input->post('genero_hmo');
                 $id_tipo_estadocivil = $this->input->post('id_estadocivil'); 
                 $direccion = $this->input->post('direccion');
                 $lugar_nacimiento = $this->input->post('lugar_nacimiento');
                 $servicio_militar = $this->input->post('smilitar');
                 $eps = $this->input->post('eps');
                 $arl = $this->input->post('arl');
                 $profesion = $this->input->post('profesion');
                 $fecha_ingreso = $this->input->post('fecha_ingreso');
                 $id_tipo_persona =  $this->input->post('id_tipo_persona');
                 $query = $this->salud_model->valor_parametro_id_aux($id_tipo_persona);
                 $tipo = $query->{'valorx'};
                 if($tipo == 1) $tabla = 'personas';
                 else $tabla = 'visitantes';
                 $gen = $this->salud_model->listar_valor_parametro(187);
                 foreach($gen as $g) if($g['valory'] == $genero_hmo) $genero = $g['id'];

                $str = $this->verificar_campos_string(['Fecha Nacimiento' => $fecha_nacimiento,
                        'Genero' => $genero,
                        'Estado Civil' => $id_tipo_estadocivil,
                        'Dirección' => $direccion,
                        'Lugar Nacimiento' => $lugar_nacimiento,
                        'EPS' => $eps,'ARL' => $arl,
                        'Profesion' => $profesion,
                        'Fecha Ingreso' => $fecha_ingreso]);
                // if (is_array($str)) {
                //     $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                // }else{
                    $paciente = $this->salud_model->buscar_paciente($id_persona, $tipo);
                    if(($paciente->{'fecha_nacimiento'} == $fecha_nacimiento) && ($paciente->{'genero'} == $genero) && ($paciente->{'id_tipo_estadocivil'} == $id_tipo_estadocivil) && ($paciente->{'direccion'} == $direccion) && ($paciente->{'lugar_nacimiento'} == $lugar_nacimiento) && ($paciente->{'servicio_militar'} == $servicio_militar) && ($paciente->{'arl'} == $arl) && ($paciente->{'eps'} == $eps) && ($paciente->{'profesion'} == $profesion) && ($paciente->{'fecha_ingreso'} == $fecha_ingreso)) {
                        $resp = ['mensaje'=>"Debe realizar alguna modificación en los datos del paciente.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        $data_paciente = [                
                            'fecha_nacimiento' => $fecha_nacimiento,
                            'genero' => $genero,
                            'id_tipo_estadocivil'  => $id_tipo_estadocivil,
                            'direccion'  => $direccion,
                            'lugar_nacimiento'  => $lugar_nacimiento,
                            'servicio_militar'  => $servicio_militar,
                            'eps'  => $eps,
                            'arl'  => $arl,
                            'profesion'  => $profesion,
                            'fecha_ingreso'  => $fecha_ingreso];
                        if(!empty($data_paciente)){
                            $mod = $this->salud_model->modificar_datos($data_paciente, $tabla, $id_persona);
                            $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];  
                            if($mod == 1) $resp= ['mensaje'=>"Error al guardar la información del paciente, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                // }
            }
        }
        echo json_encode($resp);
    }

    public function listar_historial_ocupacional(){
        $resp = array();
        $id_persona = $this->input->post("id_persona");
        $data = $this->Super_estado == true ? $this->salud_model->listar_historial_ocupacional($id_persona) : array();
        $ver_finalizado = '<span  style="background-color: #39b23b;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
        $btn_imprimir = '<span title="Imprimir Historia" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-print btn btn-default imprimir"></span>';
        foreach ($data as $row) {
            $row['ver'] = $ver_finalizado;
            $row['accion'] = $btn_imprimir;
            array_push($resp,$row);
        }
        echo json_encode($resp);
    }

    public function listar_historial_mgeneral(){
        $resp = array();
        $id_persona = $this->input->post("id_persona");
        $data = $this->Super_estado == true ? $this->salud_model->listar_historial_mgeneral($id_persona) : array();
        $ver_finalizado = '<span  style="background-color: #39b23b;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';
        $btn_imprimir = '<span title="Imprimir Historia" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-print btn btn-default imprimir"></span>';
        foreach ($data as $row) {
            $row['ver'] = $ver_finalizado;
            $row['accion'] = $btn_imprimir;
            array_push($resp,$row);
        }
        echo json_encode($resp);
    }

    public function filtrar_pacientes(){
        $resp = array();
        $habito = $this->input->post("habito");
        $antecedente = $this->input->post("antecedente");
        $diagnostico = $this->input->post("cod_diagnostico");
        $fecha_inicio = $this->input->post("fecha_inicio");
        $fecha_fin = $this->input->post("fecha_fin");
        $data = $this->Super_estado == true ? $this->salud_model->filtrar_pacientes($habito,$antecedente,$diagnostico,$fecha_inicio,$fecha_fin) : array();
        foreach ($data as $row) {
            if($row['tipo_examen'] == '') $row['tipo_examen'] = 'HISTORIA MEDICINA GENERAL';
            array_push($resp,$row);
        }
        echo json_encode($resp);
    }


    public function consultar_bitacoras(){
        $resp = array();
        $id_persona = $this->input->post("id_persona");
        $data = $this->Super_estado == true ? $this->salud_model->consultar_bitacoras($id_persona) : array();
        $ver_finalizado = '<span  style="background-color: #39b23b;color: white;width: 100%;" class="pointer form-control"><span >ver</span></span>';      
        $btn_imprimir = '<span title="Imprimir Bitacora" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-print btn btn-default imprimir"></span>';        
        $btn_bitacora = '<span title="Crear Bitácora" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-edit btn btn-default bitacora"></span>';
        foreach ($data as $row) {
            $row['ver'] = $ver_finalizado;
            if($row['id_bitacora']){
                $row['accion'] = $btn_imprimir;
            }else{
                $row['accion'] = $btn_bitacora;
            }
            array_push($resp,$row);
        }
        echo json_encode($resp);
    }

    public function add_bitacora(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_usuario_registra = $_SESSION['persona'];  
                $id_solicitud = $this->input->post('id_solicitud');
                $observacion_ingreso = $this->input->post('observacion_ingreso');
                $motivo_ingreso = $this->input->post('motivo_ingreso');
                $condiciones_pac = $this->input->post('condiciones_pac');
                $reporte_atencion = $this->input->post('reporte_atencion');
                $observacion_salida = $this->input->post('observacion_salida');
                $str = $this->verificar_campos_string(['Observaciones de Ingreso' => $observacion_ingreso,'Motivo de Ingreso' => $motivo_ingreso, 'Condiciones Generales' => $condiciones_pac,'Reporte de Atención' => $reporte_atencion,'Observaciones de Salida' => $observacion_salida]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data_solicitud = [                        
                        'id_solicitud' => $id_solicitud,
                        'observacion_ingreso' => $observacion_ingreso,
                        'observacion_salida' => $observacion_salida,
                        'motivo_ingreso' => $motivo_ingreso,
                        'condicion_general' => $condiciones_pac,
                        'reporte_atencion' => $reporte_atencion,
                        'id_usuario_registra' => $id_usuario_registra];
                    $add = $this->salud_model->guardar_datos($data_solicitud, 'salud_bitacora');
                    $resp = ['mensaje'=>"La información fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if($add == 1){
                        $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function cargar_archivo($mi_archivo, $ruta, $nombre){
		$nombre .= uniqid();
		$tipo_archivos = $this->genericas_model->obtener_valores_parametro_aux("For_Adm", 20);
		$tipo_archivos = empty($tipo_archivos) ? "*": $tipo_archivos[0]["valor"];
		$real_path = realpath(APPPATH . '../' . $ruta);
		$config['upload_path'] = $real_path;
		$config['file_name'] = $nombre;
		$config['allowed_types'] = $tipo_archivos;
		$config['max_size'] = "0";
		$config['max_width'] = "0";
		$config['max_height'] = "0";

		$this->load->library('upload', $config);
		if (!$this->upload->do_upload($mi_archivo)) {
			$data['uploadError'] = $this->upload->display_errors();
			return array(-1, $data['uploadError']);
		}
		$data['uploadSuccess'] = $this->upload->data();
		return array(1, $data['uploadSuccess']["file_name"]);
	}
//Agregado por Neyla
public function GuardarReporCov(){
    if(!$this->Super_estado){
        $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    }else{
        if ($this->Super_agrega == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{
            $estado='Sal_Fin_E';
            $tipo_solicitud = $this->input->post('tipo_solicitud');
            $subclasificacion = $this->input->post("subclasifi_salud");            
            $eps = $this->input->post("eps_salud");
            $id_persona = $this->input->post("id_persona");
            $barrio = $this->input->post("barrio");
            $mediorepo = $this->input->post("med_rep");
            $motivorepo = $this->input->post("mot_rep");
            $tiporepo = $this->input->post("tipo_rep");
            $observaciones = $this->input->post("observacion_salud");
            $fecha = $this->input->post("fe_ini_sinto");
            $sintomas = $this->input->post("sintomas");
            $act_protocolos = $this->input->post("act_pro");
            $id_usuario_registra = $_SESSION['persona'];
            $id_tipo_persona = $this->input->post('id_tipo_persona');
            $query = $this->salud_model->valor_parametro_id_aux($id_tipo_persona);
            $tipo_solicitante = $query->{'valorx'};
            if($act_protocolos==""){$act_protocolos=0;}else{$estado="Sal_Pro_E";}
            $sw = false;     
            if($id_tipo_persona !='Per_emp'){
                $str = $this->verificar_campos_string(['Medio Reporte' => $mediorepo, 'Motivo reporte' => $motivorepo,'Tipo Reporte' => $tiporepo]);
                
            }else{
              if($sintomas==""){  
                  $sintomas=0; 
                  $str = $this->verificar_campos_string(['Subclasificación' => $subclasificacion,'EPS' => $eps,'Barrio' => $barrio,'Medio Reporte' => $mediorepo,
                    'Motivo reporte' => $motivorepo,'Tipo Reporte' => $tiporepo]);
            }else{
                   $str = $this->verificar_campos_string(['Subclasificación' => $subclasificacion,'EPS' => $eps,'Barrio' => $barrio,'Medio Reporte' => $mediorepo,
                    'Motivo reporte' => $motivorepo,'Tipo Reporte' => $tiporepo,'Fecha de inicio de Sintomas' => $fecha]);
            }
             
            }
            
            if (is_array($str)) {
                $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
            }else{
                $data_solicitud = [
                'id_persona' => $id_persona,
                'id_profesional' => $id_usuario_registra,
                'id_usuario_registra' => $id_usuario_registra,
                'tipo_solicitante' => $tipo_solicitante,
                'id_tipo_solicitud' => "Sal_Sol_Cov",
                'tipo_persona_sol'=>$id_tipo_persona, //Tipo de persona paciente
                'id_estado_sol'=>$estado,
                'observacion'=> $observaciones,
                ]; 
                
                $add = $this->salud_model->guardar_datos($data_solicitud, 'salud_solicitudes');
                if($add == 0){
                    
                   
                 $id_solicitud = $this->salud_model->traer_ultima_solicitud($id_usuario_registra, 'salud_solicitudes', 'id_usuario_registra');
                    $data_detalle = [
                        'subclasificacion' => $subclasificacion,
                        'eps' => $eps,
                        'barrio' => $barrio,
                        'med_reporte' => $mediorepo,
                        'mot_reporte' => $motivorepo,
                        'tipo_reporte' => $tiporepo,
                        'fecha_sintomas' => $fecha,
                        'observaciones' => $observaciones,
                        'act_protocolos' => $act_protocolos,
                        'sintomas' => $sintomas,
                        'id_solicitud' =>$id_solicitud->{'id'},
                        'estado' =>1,
                    ]; 

                    $add = $this->salud_model->guardar_datos($data_detalle, 'salud_protocolo_covid');

                    $data_observacion = [
                        'observacion' => $observaciones,
                        'id_solicitud' => $id_solicitud->{'id'},
                        'usuario_registra' => $id_usuario_registra,
                        ]; 
                        $obser = $this->salud_model->guardar_datos($data_observacion, 'salud_observaciones');
                    if($add == 0){
                        $resp = ['mensaje'=>"El reporte fue guardado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                   }else{
                        $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
               }else{
                    $resp = ['mensaje'=>"Error al 1 guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
   
            }
        }
    }
    echo json_encode($resp);    
}
//protocolo covid-19

public function EditarEstadoCovid(){
    if(!$this->Super_estado){
        $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    }else{
        if ($this->Super_agrega == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{
            $id = $this->input->post('id');
            $Estado = $this->input->post('Estado');
            $motivocambio = $this->input->post("motivocambio");  
            $observacionescambio = $this->input->post("observacionescambio");           
            $str = $this->verificar_campos_string(['Motivo' => $motivocambio,'Observaciones' => $observacionescambio, 'Id' => $id]);
            if (is_array($str)) {
                $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
            }else{
                $estado_solicitud="";
                if($Estado=='Final' || $Estado=='FINAL'){
                    $data_protocolo = [
                    'act_protocolos' => 0,
                    'estado_final' =>  $motivocambio,
                    ]; 
                    $estado_solicitud='Sal_Fin_E';
                }else{
                    $data_protocolo = [
                        'act_protocolos' => 1,
                        'estado_inicial' =>  $motivocambio,
                        ]; 
                    $estado_solicitud='Sal_Pro_E';
                }
                $mod = $this->salud_model->modificar_datoscv($data_protocolo,'salud_protocolo_covid', $id);
                if($mod != 0){
                    $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $data_solicitud = [
                        'id_estado_sol' => $estado_solicitud,
                        'observacion_mod' => $observacionescambio,
                        'motivo_consulta' => $motivocambio,
                        ]; 
                    $resp = $this->salud_model->modificar_datos($data_solicitud,'salud_solicitudes', $id);
                    if($resp == 0){
                        $resp = ['mensaje'=>"El reporte fue actualizado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    }
                }
   
            }
        }
    }
    echo json_encode($resp);    
}


//
public function EditarTpReporte(){
    if(!$this->Super_estado){
        $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    }else{
        if ($this->Super_agrega == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{
            $id = $this->input->post('idsolic_rep');
            $tipo_reporte = $this->input->post("treportecambio");           
            $str = $this->verificar_campos_string(['Tipo de reporte' => $tipo_reporte, 'Id' => $id]);
            if (is_array($str)) {
                $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
            }else{
                    $data_solicitud = [
                        'tipo_reporte' => $tipo_reporte
                        ]; 
                    $resp = $this->salud_model->modificar_datoscv($data_solicitud,'salud_protocolo_covid', $id);
                    if($resp == 0){
                        $resp = ['mensaje'=>"Actualización realizada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    }   
            }
        }
    }
    echo json_encode($resp);    
}
//Nueva Observacion
public function AgregarNuevaObservacion(){
    if(!$this->Super_estado){
        $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    }else{
        if ($this->Super_agrega == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{
            $id = $this->input->post('idsolic'); 
            $observacion = $this->input->post("newobserv"); 
            $id_usuario_registra = $_SESSION['persona'];        
            $str = $this->verificar_campos_string(['Observación' => $observacion]);
            if (is_array($str)) {
                $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
            }else{
                    $data_solicitud = [
                        'observacion' => $observacion,
                        'id_solicitud' => $id,
                        'usuario_registra' => $id_usuario_registra,
                        ]; 
                        $resp = $this->salud_model->guardar_datos($data_solicitud, 'salud_observaciones');
                    if($resp == 0){
                        $data_ob = [
                            'observacion' => $observacion,
                            'id' => $id,
                            ]; 
                        $obsr = $this->salud_model->modificar_datos($data_ob,'salud_solicitudes', $id);
                        $resp = ['mensaje'=>"Observacion agregada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    }
   
            }
        }
    }
    echo json_encode($resp);    
}
public function listar_salud_observaciones(){
    $solicitud  = $this->input->post("solicitud"); 
    $resp = $this->Super_estado == true ? $this->salud_model->listar_observaciones($solicitud) : array();
    echo json_encode($resp);
}

}