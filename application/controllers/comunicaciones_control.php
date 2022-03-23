
<?php

date_default_timezone_set('America/Bogota');
class comunicaciones_control extends CI_Controller
{

	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
    var $Super_agrega = 0;
    var $ruta_archivos_solicitudes = "archivos_adjuntos/comunicaciones/solicitudes";
    public function __construct()
    {
        parent::__construct();
        include('application/libraries/festivos_colombia.php');
        $this->load->model('comunicaciones_model');
        $this->load->model('mantenimiento_model');
        $this->load->model('almacen_model');
		$this->load->model('genericas_model');
        session_start();
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
        }
    }

    public function index($id = '')
    {
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'comunicaciones');
            if (!empty($datos_actividad)) {
                $tiempos = $this->genericas_model->obtener_valores_parametro(60);
                $tiempos_man =  $this->genericas_model->obtener_valores_parametro_aux('Eve_Man', 20);
				$pages = "comunicaciones";
				$data['js'] = "Comunicaciones";
				$data['id'] = $id;
				$data['tiempos'] = $tiempos;
				$data['tiempos_man'] = $tiempos_man;
                $data['actividad'] = $datos_actividad[0]["id_actividad"];
            }else{
                $pages = "sin_session";
                $data['js'] = "";
                $data['actividad'] = "Permisos";
            }
        }else{
			$pages = "inicio";
			$data['js'] = "";
			$data['actividad'] = "Ingresar";
		}
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function guardar_servicios_nuevos(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $solictud = $this->comunicaciones_model->consulta_solicitud_id($id_solicitud);
                $estado_actual = $solictud->{'id_estado_solicitud'};
                $id_tipo_solicitud = $solictud->{'id_tipo_solicitud'};
                if ($estado_actual == 'Com_Sol_E') {
                    $id_codigo_sap = $this->input->post("id_codigo_sap");
                    $servicios = $this->input->post('servicios');
                    $id_usuario_registra = $_SESSION['persona'];          
                    $data = array();
                    $error = '';
                    $sw = true;
                    $especial = 0;
                    $normales = 0;
                    foreach ($servicios as $servicio) {
                        if ($servicio['estado'] == 1) {
                            $row = $this->comunicaciones_model->obtener_servicio_solicitud($servicio["id_servicio"], $id_solicitud);
                            if(empty($row)){
                                $serv = $this->comunicaciones_model->obtener_info_servicio($servicio["id_servicio"]);
                                if (($id_tipo_solicitud == 'Com_Env' || $id_tipo_solicitud == 'Com_Pub') && $serv->{'id_aux'} != 'Ser_Staff' && $serv->{'id_aux'} != 'Ser_Dif' && empty($id_codigo_sap) &&  (empty($solictud->{'id_codigo_sap'})|| is_null($solictud->{'id_codigo_sap'}) )) {
                                    $sw = false;
                                    $resp = ['mensaje'=>"Los servicios seleccionados requieren codigo SAP", 'tipo'=>"info", 'titulo'=> "Oops.!", 'sw' => true];
                                    break;   
                                }
                                if ($serv->{'id_aux'} == 'Ser_Staff' || $serv->{'id_aux'} == 'Ser_Dif') $especial++;
                                $normales++; 
                                array_push($data,[
                                    'id_servicio' => $servicio['id_servicio'],
                                    'id_solicitud' => $id_solicitud,
                                    'cantidad' => isset($servicio['cantidad']) ? $servicio['cantidad'] : null,
                                    'id_tipo' => isset($servicio['id_tipo']) ? $servicio['id_tipo'] : null,
                                    'observaciones' => isset($servicio['observaciones']) ? $servicio['observaciones'] : null,
                                    'id_tipo_entrega' => isset($servicio['id_tipo_entrega']) ? $servicio['id_tipo_entrega'] : null,
                                    'id_usuario_registra' => $id_usuario_registra]);
                            }else {
                                $error = 'Tenga en cuenta que uno o varios servicios estaban añadidos anteriormente.';
                            }
                        }
                    }
                    if ($sw) {
                        $mod  = 0;
                        $verificar_sap = false;
                        if (($id_tipo_solicitud == 'Com_Env' || $id_tipo_solicitud == 'Com_Pub') && (empty($solictud->{'id_codigo_sap'}) || is_null($solictud->{'id_codigo_sap'})) && $normales > $especial ) {
                            $data_solicitud = ['id_codigo_sap' => $id_codigo_sap];
                            $mod = $this->comunicaciones_model->modificar_datos($data_solicitud, 'comunicaciones_solicitudes',$id_solicitud);
                            $verificar_sap = true;
                        }
                        if ($mod != 0) {
                            $resp= ['mensaje'=>"Error al asignar el codigo sap, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }else{
                            $resp= ['mensaje'=>"Los servicios seleccionados fueron añadidos exitosamente. $error",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!","solicitud" => $id_solicitud];
                            if ($verificar_sap) $resp ['data'] = $this->comunicaciones_model->consulta_solicitud_id($id_solicitud);
                            if(!empty($data)){
                            $add = $this->comunicaciones_model->guardar_datos($data, 'comunicaciones_servicios',2);
                            if($add != 0)$resp= ['mensaje'=>"Error al guardar los servicios, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                            }else{
                                $resp = ['mensaje'=>"No ha seleccionado ningun servicio o ya cuenta con este servicio en su solicitud", 'tipo'=>"info", 'titulo'=> "Oops.!", 'refres' => true];
                            }
                        }
                 
                    }
                }
                else{
                    $resp= ['mensaje'=>"No es posible realizar esta acción ya que La solicitud se encuentra en tramite o terminada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        }          
        echo json_encode($resp); 
    }


    public function guardar_solicitud(){
        
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_tipo_solicitud = $this->input->post('id_tipo_solicitud');
                $nombre_evento = $this->input->post('nombre_evento');
                //$presupuesto = $this->input->post('presupuesto');
                $presupuesto = 1;
                $fecha_inicio_evento = $this->input->post('fecha_inicio_evento');
                $fecha_fin_evento = $this->input->post('fecha_fin_evento');
                $id_tipo_evento = $this->input->post('id_tipo_evento');
                $nombre_lugar = $this->input->post('nombre_lugar');
                $direccion = $this->input->post('direccion');
                $telefono = $this->input->post('telefono');
                $nro_invitados = $this->input->post('nro_invitados');
                $descripcion = $this->input->post('descripcion');
                $id_categoria_divulgacion = $this->input->post('id_categoria_divulgacion');
                $id_codigo_sap = $this->input->post('id_codigo_sap');
                $id_usuario_registra = $_SESSION['persona']; 
                $servicios = $this->input->post('servicios');
                $cont_servicio = $this->input->post('cont_servicio');
                $confirm = $this->input->post('confirm');
                $data_servicios = array();
                $data_servicios_man = array();
                $sw_adm = false;
                $sw_man = false;
                $no_dispo = '';


                foreach ($servicios as $servicio) {
                    if ($servicio['estado'] == 1) {
                        if ($servicio['valory'] != 'Man') {
                            array_push($data_servicios,[
                            'id_servicio' => $servicio['id_servicio'],
                            'cantidad' => isset($servicio['cantidad']) ? $servicio['cantidad'] : null,
                            'id_tipo' => isset($servicio['id_tipo']) &&  $servicio['id_tipo'] ? $servicio['id_tipo'] : null,
                            'observaciones' => isset($servicio['observaciones']) ? $servicio['observaciones'] : null,
                            'id_tipo_entrega' => isset($servicio['id_tipo_entrega']) && $servicio['id_tipo_entrega'] ? $servicio['id_tipo_entrega'] : null,
                            'id_solicitud' => 0,
                            'id_usuario_registra' => $id_usuario_registra]);
                            if($servicio['valory'] == 'Adm')$sw_adm = true;
                        }
                        /*else{
                            $sw_man = true;
                            $existencia = $this->validar_existencias_mant($servicio['id_servicio'],$servicio['cantidad'], $fecha_inicio_evento, $fecha_fin_evento);
                            if ($existencia) {
                                array_push($data_servicios_man,[
                                    'articulo_id' => $servicio['id_servicio'],
                                    'cantidad' => $servicio['cantidad'],
                                    'solicitud_id' => 0,
                                    'id_usuario_registra' => $id_usuario_registra]);
                            }else {
                                $no_dispo .= $servicio['nombre'].',';
                            }
                        }
                        if(!empty($no_dispo)){
                            $resp = ['mensaje'=>"Tener en cuenta que algunos servicios no tienen la cantidad solicitada : $no_dispo", 'tipo'=>"info", 'titulo'=> "Oops.!"]; 
                            echo json_encode($resp);
                            return;
                        }*/
                       
                    }
                }
                // echo json_encode($data_servicios);
                // return;
                if (empty($data_servicios) && empty($data_servicios_man)) {
                    $resp = ['mensaje'=>"Seleccione por lo menos un servicio", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    if($id_tipo_solicitud == "Com_Div"){
                        $str = $this->verificar_campos_string(['Nombre lugar'=>$nombre_lugar, 'Direccion'=>$direccion, 'Categoria'=>$id_categoria_divulgacion,'Descripcion'=>$descripcion]);
                        $num = $this->verificar_campos_numericos(['Telefono' => $telefono, 'Nro invitados' => $nro_invitados]);
                        $fecha_i = $this->validateDate($fecha_inicio_evento,'Y-m-d H:i');
                        $fecha_f = $this->validateDate($fecha_fin_evento,'Y-m-d H:i');
                        if (is_array($str)) {
                            $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else if ($telefono < 0 || $nro_invitados < 0 ) {
                            $resp = ['mensaje'=> "Los campos telefono y Nro invitados deben ser mayores que 0.", 'tipo'=>"info", 'titulo'=> "Oops."];
                        }else if (empty($descripcion)) {
                            $resp = ['mensaje'=>"El campo descripción no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        } else if (!$fecha_i || !$fecha_f) {
                            $resp = ['mensaje'=> "Por favor seleccione fechas validas y superior a la fecha actual.", 'tipo'=>"info", 'titulo'=> "Oops."];
                        }else{
    
                            $data = [
                                'id_tipo_solicitud' => $id_tipo_solicitud,
                                'nombre_evento' => $nombre_evento,                                
                                'fecha_inicio_evento' => $fecha_inicio_evento,
                                'fecha_fin_evento' => $fecha_fin_evento,
                                'id_tipo_evento' => $id_tipo_evento,
                                'nombre_lugar' => $nombre_lugar,
                                'direccion' => $direccion,
                                'telefono' => $telefono,
                                'nro_invitados' => $nro_invitados,
                                'id_categoria_divulgacion' => $id_categoria_divulgacion,
                                'descripcion' => $descripcion,
                                'id_usuario_registra' => $id_usuario_registra,
                                ];

                                $add = $this->comunicaciones_model->guardar_datos($data, 'comunicaciones_solicitudes');
                                if($add != 0){
                                $resp= ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                                }else{
                                    $solicitud = $this->comunicaciones_model->traer_ultima_solicitud($id_usuario_registra);
                                    $data_estado  = [ "id_solicitud" => $solicitud -> {'id'},'id_estado' => 'Com_Sol_E', 'id_usuario_registro' => $id_usuario_registra];
                                    $add_estado = $this->comunicaciones_model->guardar_datos($data_estado,'comunicaciones_estados_sol');
                                    for ($i=0; $i < count($data_servicios); $i++)  $data_servicios[$i]['id_solicitud'] = $solicitud ->{'id'};
                                    $resp= ['mensaje'=>"La solicitud fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!","solicitud" => $solicitud];
                                    if(!empty($data_servicios)){
                                    $add = $this->comunicaciones_model->guardar_datos($data_servicios, 'comunicaciones_servicios',2);
                                    if($add != 0)$resp= ['mensaje'=>"Error al guardar los servicios, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                                    }
                                    
                                }
                        }
                    }else{
                        $responsable  = '';
                        if ($id_tipo_solicitud == "Com_Env" && ($sw_adm || $sw_man)) $responsable = $this->genericas_model->obtener_valores_parametro_aux("Res_Com", 20);
                        $str = $this->verificar_campos_string([ 'Nombre lugar'=>$nombre_lugar, 'Direccion'=>$direccion]);
                        $num = $this->verificar_campos_numericos(['Telefono' => $telefono, 'Nro invitados' => $nro_invitados]);
                        if($id_tipo_solicitud == "Com_Env" && ($sw_adm || $sw_man) && empty($responsable) ){
                            $resp= ['mensaje'=>"Error de configuración en la solicitud, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }else if (is_array($str)) {
                            $resp = ['mensaje'=>"El campo ". $str['field'] ." debe ser un texto y no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else if (is_array($num)) {
                            $resp = ['mensaje'=> "El campo ".$num['field']." debe ser numerico y no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops."];
                        }else if ($telefono < 0 || $nro_invitados < 0 ) {
                            $resp = ['mensaje'=> "Los campos telefono y Nro invitados deben ser mayores que 0.", 'tipo'=>"info", 'titulo'=> "Oops."];
                        //}else if (empty($id_codigo_sap) && (($id_tipo_solicitud == "Com_Env" && $presupuesto == 1) || $id_tipo_solicitud ==  "Com_Pub" )) {
                        }else if (empty($id_codigo_sap) && ($id_tipo_solicitud == "Com_Env" || $id_tipo_solicitud ==  "Com_Pub" )) {
                            $resp = ['mensaje'=> "El Codigo Sap no puede ser vacio.", 'tipo'=>"info", 'titulo'=> "Oops."];
                        }else if (empty($descripcion) && $cont_servicio['diseno'] == 1) {
                            $resp = ['mensaje'=>"El campo descripción no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else{
                            $fecha_i = $this->validateDate($fecha_inicio_evento,'Y-m-d H:i');
                            $fecha_f = $this->validateDate($fecha_fin_evento,'Y-m-d H:i');
                            $val_fecha = $this->validar_fechas($id_tipo_solicitud,$fecha_inicio_evento,'Y-m-d',$presupuesto);
                            if (!$fecha_i || !$fecha_f) {
                                $resp = ['mensaje'=> "Por favor seleccione fechas validas y superior a la fecha actual.", 'tipo'=>"info", 'titulo'=> "Oops."];
                            }else if (!$val_fecha['sw'] && $confirm == -1) {
                                $dias_solicitud = $val_fecha['dias_solicitud'];
                                $tipo_msj = $id_tipo_solicitud == 'Com_Env' ? 'confirm' : 'info';
                                $resp = ['mensaje'=> "La solicitud ingresada se encuentra fuera de los tiempos establecidos, debe tener en cuenta que el departamento encargado del proceso cuenta con $dias_solicitud días hábiles para gestionar la solicitud.", 'tipo'=>$tipo_msj, 'titulo'=> "Oops."];
                            }else{
                             $sw = true;
                                //if(($id_tipo_solicitud == "Com_Env" && $presupuesto == 1) || $id_tipo_solicitud ==  "Com_Pub" ){ 
                                if($id_tipo_solicitud == "Com_Env"  || $id_tipo_solicitud ==  "Com_Pub" ){ 
                                    $existe_codigo = $this->genericas_model->obtener_valores_parametro_valox(25, $id_codigo_sap);
                                    if (empty($existe_codigo)  ) {
                                        $resp= ['mensaje'=>"El codigo sap no existe.",'tipo'=>"info",'titulo'=> "Oops.!"];
                                        $sw = false;
                                    }else{
                                        $id_codigo_sap = $existe_codigo[0]["id"];
                                    }
                                }else{
                                    $id_codigo_sap = null;
                                }
                                if($sw){
                                    $data = [
                                        'id_tipo_solicitud' => $id_tipo_solicitud,
                                        'fecha_inicio_evento' => $fecha_inicio_evento,
                                        'fecha_fin_evento' => $fecha_fin_evento,
                                        'id_tipo_evento' => $id_tipo_evento,
                                        'nombre_lugar' => $nombre_lugar,
                                        'direccion' => $direccion,
                                        'telefono' => $telefono,
                                        'nro_invitados' => $nro_invitados,
                                        'descripcion' => $descripcion,
                                        'id_usuario_registra' => $id_usuario_registra,
                                        'id_codigo_sap' => $id_codigo_sap,
                                        'nombre_evento' => $nombre_evento,
                                        'presupuesto' => $presupuesto,
                                        'id_estado_solicitud' => $presupuesto == 0 && $id_tipo_solicitud == 'Com_Env' ? 'Com_Ent_E' : 'Com_Sol_E',
                                        ];
                                        $add = $this->comunicaciones_model->guardar_datos($data, 'comunicaciones_solicitudes');
                                        if($add != 0){
                                            $resp= ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                                        }else{
                                            $solicitud = $this->comunicaciones_model->traer_ultima_solicitud($id_usuario_registra);
                                            if ($id_tipo_solicitud == "Com_Env" && $sw_adm) {
                                                $data = [
                                                    'id_tipo_solicitud' => 'Even_Com',
                                                    'fecha_inicio_evento' => $fecha_inicio_evento,
                                                    'fecha_fin_evento' => $fecha_fin_evento,
                                                    'nombre_evento' => $nombre_evento,
                                                    'id_tipo_evento' => 'Even_Nac',
                                                    'nombre_evento' => $nombre_evento,
                                                    'id_usuario_registra' => $responsable[0]['valor'],
                                                    'requiere_inscripcion' => 0,
                                                    'estado_solicitud' => null,
                                                    'id_evento_com' =>  $solicitud ->{'id'},
                                                    ];
                                                $add = $this->comunicaciones_model->guardar_datos($data, 'solicitudes_adm');
                                            }
                                            /*else if ($id_tipo_solicitud == "Com_Env" && $sw_man) {
                                                $data = [
                                                    'fecha_inicio' => $fecha_inicio_evento,
                                                    'fecha_fin' => $fecha_fin_evento,
                                                    'descripcion' => $descripcion,
                                                    'telefono' => $telefono,
                                                    'ubicacion' => 'Bloque: '.$nombre_lugar.' - Salon: '.$direccion,
                                                    'solicitante_id' => $responsable[0]['valor'],
                                                    'id_evento_com' =>  $solicitud ->{'id'},
                                                    'estado_solicitud' => 'Man_Sol',
                                                    ];
                                                $this->comunicaciones_model->guardar_datos($data, 'solicitudes_mantenimiento');
                                                $id = $this->mantenimiento_model->ultima_solicitud_por_usuario($responsable[0]['valor']);
                                                for ($i=0; $i < count($data_servicios_man); $i++)  $data_servicios_man[$i]['solicitud_id'] = $id;
                                                $this->comunicaciones_model->guardar_datos($data_servicios_man, 'articulos_solicitudes_man',2);
                                            
                                            }*/
                                            $data_estado  = [ "id_solicitud" => $solicitud -> {'id'},'id_estado' => 'Com_Sol_E', 'id_usuario_registro' => $id_usuario_registra];
                                            $add_estado = $this->comunicaciones_model->guardar_datos($data_estado,'comunicaciones_estados_sol');
                                            for ($i=0; $i < count($data_servicios); $i++)  $data_servicios[$i]['id_solicitud'] = $solicitud ->{'id'};
                                            $resp= ['mensaje'=>"La solicitud fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!","solicitud" => $solicitud];
                                            if(!empty($data_servicios)){
                                            $add = $this->comunicaciones_model->guardar_datos($data_servicios, 'comunicaciones_servicios',2);
                                            if($add != 0)$resp= ['mensaje'=>"Error al guardar los servicios, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                                            }
    
                                        }
                                }                             
                            }
                        }
                    }
                }
              
                
            }
        }
        echo json_encode($resp);

    }

    // Recibe un array con clave-valor con los campos a verificar. 
    // En caso de que uno de los campos esté vacio o no sea numérico retorna el error -1 y el nombre del campo respectivo.
    public function verificar_campos_numericos($array){
        foreach ($array as $row) {
            if (empty($row) || ctype_space($row) || !is_numeric($row)) {
                return ['type' => -1, 'field' => array_search($row, $array, true)];
            }
        }
        return 1;
    }
    // Recibe un array con clave-valor con los campos a verificar. 
    // En caso de que uno de los campos esté vacio retorna el error -2 y el nombre del campo respectivo.
    public function verificar_campos_string($array){
        foreach ($array as $row) {
            if (empty($row) || ctype_space($row)) {
                return ['type' => -2, 'field' => array_search($row, $array, true)];
            }
        }
        return 1;
    }

    //VALIDACION DE LAS FECHAS
    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $fecha_actual = date($format);
        $d = DateTime::createFromFormat($format, $date);
        if ($d->format($format) < $fecha_actual) return false;
        return $d && $d->format($format) == $date;
    }
    public function es_habil($c_day){
		$festivos = new festivos_colombia;
		$festivos->festivos(date("Y",strtotime($c_day)));
        $c_weekDay = (int) $this->getWeekDay($c_day);
		if ($c_weekDay == 0 || $c_weekDay == 6 || $festivos->esFestivo($c_day)) {
			return false;
        }
		return true;
    }
    public function getWeekDay($date){
		return date("w", strtotime($date));
    }

    public function validar_fechas($id_tipo_solicitud, $date, $format = 'Y-m-d H:i:s',$presupuesto = 1){      
        $dias_solicitud = 0; 
        $fecha_actual = date($format);  
        if ($id_tipo_solicitud == 'Com_Env' && $presupuesto == 0) {
            $tiempos_man =  $this->genericas_model->obtener_valores_parametro_aux('Eve_Man', 20);
            $dias_solicitud = empty($tiempos_man) ? 3 : $tiempos_man[0]['valor'];
            $fecha_inicio= date($format,strtotime($date));
            $resp = 0;
            $hoy = date($format); 
            while ($hoy <= $fecha_inicio) {
                if (!$this->es_habil($hoy)) {
                    $resp += 1;
                }
                $hoy = date("Y-m-d",strtotime($hoy." +1 days")); 
            }
            $total = ($dias_solicitud + $resp);
            $fecha_inicio_valida = date($format,strtotime($fecha_actual." + $total days"));
        }else{
            $dias_solicitud = $this->comunicaciones_model->traer_dias_solicitud($id_tipo_solicitud);
            $fecha_inicio_valida = date($format,strtotime($fecha_actual." +$dias_solicitud days"));
            $fecha_inicio= date($format,strtotime($date));  
        }
         $sw = $fecha_inicio < $fecha_inicio_valida ? false : true;
         return ['dias_solicitud' => $dias_solicitud, 'sw' => $sw];
    }

    
    public function listar_solicitud()
    {   
        $id = $this->input->post("id");
        $tipo = $this->input->post("tipo");
        $estado = $this->input->post("estado");
        $fecha = $this->input->post("fecha");
        $resp = array();
        $alertas = array();
        if ($this->Super_estado ) {
           $data =  $this->comunicaciones_model->listar_solicitud($id, $tipo, $estado, $fecha);
           $resp['solicitudes'] = $data;
           $administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Admin_Com'? true : false; 
           if ($administra ) {
                $fecha_actual = date("Y-m-d");
                foreach ($data as $key) {
                    $fecha_inicio = date("Y-m-d",strtotime($key['fecha_registra']));
                    $tipo_solicitud = $key['id_tipo_solicitud'];
                    $estado_solicitud = $key['id_estado_solicitud'];
                    if ($tipo_solicitud == 'Com_Div' && ($estado_solicitud != 'Com_Fin_E' && $estado_solicitud != 'Com_Can_E' && $estado_solicitud != 'Com_Rec_E')) {
                        $notificar = $this->notificaciones_gestion($tipo_solicitud,$key['id_categoria_divulgacion'],$fecha_inicio,$fecha_actual,$key['tiempo_cat_div']);
                        if ($notificar) array_push($alertas,$key);
                    }        
                }
            }
            $resp['alertas'] = $alertas;
        } 
        echo json_encode($resp);
    }
    public function listar_archivos_adjuntos()
    {
        $id_solicitud = $this->input->post("id");
        $resp = $this->Super_estado ? $this->comunicaciones_model->listar_archivos_adjuntos($id_solicitud) : array();
        echo json_encode($resp);
    }
    public function listar_servicios()
    {        
        $id_tipo_solicitud = $this->input->post("id");
        $tipo = $this->input->post("tipo");
        $con_aux = $this->input->post("con_aux");
        $resp = $this->Super_estado ? $this->comunicaciones_model->listar_servicios($id_tipo_solicitud , $tipo , $con_aux) : array();
        echo json_encode($resp);
    }
    public function listar_servicios_nuevos()
    {        
        $id = $this->input->post("id");
        $con_aux = $this->input->post("con_aux");
        $resp = $this->Super_estado ? $this->comunicaciones_model->listar_servicios_nuevos($id,$con_aux) : array();
        echo json_encode($resp);
    }
    public function listar_servicios_solicitud()
    {       
        $resp = array();         
        if( $this->Super_estado){
            $id = $this->input->post("id");
            $resp = $this->comunicaciones_model->listar_servicios_solicitud($id) ;
            $solictud = $this->comunicaciones_model->consulta_solicitud_id($id);
            $presupuesto = $solictud->{'presupuesto'};
            $id_tipo_solicitud = $solictud->{'id_tipo_solicitud'};
            if ($presupuesto == 0 && $id_tipo_solicitud  == 'Com_Env') {
                $man = $this->comunicaciones_model->traer_solicitud_externa($id,'solicitudes_mantenimiento');
                $articulos = $this->mantenimiento_model->articulos_solicitados($man->{'id'});
                foreach ($articulos as $key) {
                    $data = [
                        'cantidad'=> $key['cantidad'],
                        'estado'=> "1",
                        'estadoSolicitud'=> "1",
                        'estado_solicitud'=> $solictud->{'id_estado_solicitud'},
                        'fecha'=>  $key['fecha_registra'],
                        'fecha_elimina'=> null,
                        'fecha_registra'=>  $key['fecha_registra'],
                        'id'=>  $key['id'],
                        'id_aux'=> null,
                        'id_servicio'=> $key['articulo_id'],
                        'id_solicitud'=> $key['solicitud_id'],
                        'id_tipo'=> null,
                        'id_tipo_entrega'=> null,
                        'id_tipo_solicitud'=> "Com_Env",
                        'id_usuario_elimina'=> null,
                        'id_usuario_registra'=> "1",
                        'nombre'=>  $key['nombre'],
                        'solicitante'=> $key['solicitante'],
                        'tipo'=> null,
                        'tipo_entrega'=> null,
                        'tipo_ser'=> "Man",
                    ];
                    array_push($resp,$data);
                }
                
            }
        }
        echo json_encode($resp);
    }
    public function consulta_solicitud_id()
    {                
        $id = $this->input->post("id");
        $resp = $this->Super_estado ? $this->comunicaciones_model->consulta_solicitud_id($id) : array();
        echo json_encode($resp);
    }
    public function listar_estados()
    {
        $id_solicitud = $this->input->post("id");
        $resp = $this->Super_estado ? $this->comunicaciones_model->listar_estados($id_solicitud) : array();
        echo json_encode($resp);
    }
    // ADJUNTAR
    public function recibir_archivos()
    {
        $id_solicitud = $this->input->post("id");
        $nombre = $_FILES["file"]["name"];
        $cargo = $this->cargar_archivo("file", $this->ruta_archivos_solicitudes, "sol");
        if ($cargo[0] == -1) {
            header("HTTP/1.0 400 Bad Request");
            echo ($nombre);
            return;
        }
        $data = [
            "id_solicitud" => $id_solicitud,
            "nombre_real" => $nombre,
            "nombre_guardado" => $cargo[1],
            "usuario_registra" => $_SESSION['persona'],
            ];
        $res = $this->comunicaciones_model->guardar_datos($data,'comunicaciones_adjunto');
        if ($res != 0) {
            header("HTTP/1.0 400 Bad Request");
            echo ($nombre);
            return;
        }
        echo json_encode($res);
        return;
       }
    function cargar_archivo($mi_archivo, $ruta, $nombre)
    {
        $nombre .= uniqid();
        $info = $this->genericas_model->obtener_valores_parametro_aux("For_Adm", 20);
        $tipo_archivos = empty($tipo_archivos) ?  "*" : $info[0]["valor"];
        $real_path = realpath(APPPATH . '../' . $ruta);
        $config['upload_path'] = $real_path;
        $config['file_name'] = $nombre;
        $config['allowed_types'] =$tipo_archivos;
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


    function eliminar_servicio_solicitud()
    {
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_elimina == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post("id");
                $id_solicitud = $this->input->post("id_solicitud");
                $solictud = $this->comunicaciones_model->consulta_solicitud_id($id_solicitud);
                $estado_actual = $solictud->{'id_estado_solicitud'};
                if ($estado_actual == 'Com_Sol_E') {
                    $servicios =  count($this->comunicaciones_model->listar_servicios_solicitud($id_solicitud));
                    if ($servicios == 1) {
                        $resp= ['mensaje'=>"Su solicitud debe tener por lo menos un servicio.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        $fecha = date("Y-m-d H:i:s");
                        $usuario = $_SESSION["persona"];
                        $data = [
                            "id_usuario_elimina" => $usuario,
                            "fecha_elimina" => $fecha,
                            "estado" => 0,
                            ];
                        $resp= ['mensaje'=>"El servicio fue eliminado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        $del = $this->comunicaciones_model->modificar_datos($data,'comunicaciones_servicios',$id);
                        if($del != 0)$resp= ['mensaje'=>"Error al eliminar el servicio, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    
                    }
                }else{
                    $resp= ['mensaje'=>"No es posible realizar esta acción ya que La solicitud se encuentra en tramite o terminada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        
        }
        echo json_encode($resp);
    }   
    
    function eliminar_adjunto_solicitud()
    {
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_elimina == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post("id");
                $adjunto = $this->comunicaciones_model->consulta_adjunto_id($id);
                $id_solicitud = $adjunto->{'id_solicitud'};
                $solictud = $this->comunicaciones_model->consulta_solicitud_id($id_solicitud);
                $estado_actual = $solictud->{'id_estado_solicitud'};
                $persona = $adjunto->{'usuario_registra'};
                $usuario = $_SESSION["persona"];
                $administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Admin_Com'? true : false;
                if ($estado_actual == 'Com_Sol_E' && ($persona == $usuario || $administra)) {
                    $fecha = date("Y-m-d H:i:s");
                    $data = [
                        "usuario_elimina" => $usuario,
                        "fecha_elimina" => $fecha,
                        "estado" => 0,
                        ];
                    $resp= ['mensaje'=>"El archivo adjunto fue eliminado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    $del = $this->comunicaciones_model->modificar_datos($data,'comunicaciones_adjunto',$id);
                    if($del != 0)$resp= ['mensaje'=>"Error al eliminar el archivo adjunto, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación o La solicitud se encuentra en tramite o terminada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }

        
        }
        echo json_encode($resp);
    }  

public function modificar_solicitud(){
        
    if(!$this->Super_estado){
        $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    }else{
        if ($this->Super_modifica == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{           
            $id = $this->input->post("id");
            $solictud = $this->comunicaciones_model->consulta_solicitud_id($id);
            $requiere = $this->comunicaciones_model->listar_servicios_solicitud($id,'si');
            $estado_actual = $solictud->{'id_estado_solicitud'};
            $presupuesto = $solictud->{'presupuesto'};
            $id_tipo_solicitud = $solictud->{'id_tipo_solicitud'};
            if ($presupuesto == 0 && $id_tipo_solicitud == 'Com_Env') {
                $resp= ['mensaje'=>"Esta acción no se encuentra disponible para las solicitudes de eventos sin presupuesto.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                if ($estado_actual == 'Com_Cor_E') {
                    $id_tipo_solicitud = $this->input->post('id_tipo_solicitud');
                    $fecha_inicio_evento = $this->input->post('fecha_inicio_evento');
                    $fecha_fin_evento = $this->input->post('fecha_fin_evento');
                    $id_tipo_evento = $this->input->post('id_tipo_evento');
                    $nombre_lugar = $this->input->post('nombre_lugar');
                    $direccion = $this->input->post('direccion');
                    $telefono = $this->input->post('telefono');
                    $nro_invitados = $this->input->post('nro_invitados');
                    $descripcion = $this->input->post('descripcion');
                    $id_categoria_divulgacion = $this->input->post('id_categoria_divulgacion');
                    $id_codigo_sap = $this->input->post('id_codigo_sap');
                    $nombre_evento = $this->input->post('nombre_evento');

                    // if($id_tipo_solicitud == "Com_Div"){
                    //     $str = $this->verificar_campos_string(['Descripcion'=>$descripcion]);
                    //     if (is_array($str)) {
                    //         $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    //     }else{
                    //         $data = [
                    //             'id_tipo_solicitud' => $id_tipo_solicitud,
                    //             'descripcion' => $descripcion,
                    //             'id_categoria_divulgacion' => $id_categoria_divulgacion,
                    //             ];
                    //             $resp= ['mensaje'=>"La solicitud fue modificada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    //             $add = $this->comunicaciones_model->modificar_datos($data, 'comunicaciones_solicitudes', $id);
                    //             if($add != 0) $resp= ['mensaje'=>"Error al modificar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                                
                    //     }
                    // }else{
                        $str = $this->verificar_campos_string([ 'Nombre lugar'=>$nombre_lugar, 'Direccion'=>$direccion, 'Descripcion'=>$descripcion]);
                        $num = $this->verificar_campos_numericos(['Telefono' => $telefono, 'Nro invitados' => $nro_invitados]);
                        if (is_array($str)) {
                            $resp = ['mensaje'=>"El campo ". $str['field'] ." debe ser un texto y no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else if (is_array($num)) {
                            $resp = ['mensaje'=> "El campo ".$num['field']." debe ser numerico y no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops."];
                        }else if ($telefono < 0 || $nro_invitados < 0 ) {
                            $resp = ['mensaje'=> "Los campos telefono y Nro invitados deben ser mayores que 0.", 'tipo'=>"info", 'titulo'=> "Oops."];
                        }else if (empty($id_codigo_sap) && (($id_tipo_solicitud == "Com_Env" && $presupuesto == 1) || $id_tipo_solicitud ==  "Com_Pub" )) {
                            $resp = ['mensaje'=> "El Codigo Sap no puede ser vacio.", 'tipo'=>"info", 'titulo'=> "Oops."];
                        }else{

                            $fecha_i = $this->validateDate($fecha_inicio_evento,'Y-m-d H:i');
                            $fecha_f = $this->validateDate($fecha_fin_evento,'Y-m-d H:i');

                            $val_fecha = $this->validar_fechas($id_tipo_solicitud,$fecha_inicio_evento,'Y-m-d');

                            // if ($id_tipo_solicitud != "Com_Env" && $id_tipo_solicitud != "Com_Div"){

                                if (!$fecha_i || !$fecha_f) {
                                    $resp = ['mensaje'=> "Por favor seleccione fechas validas y superior a la fecha actual.", 'tipo'=>"info", 'titulo'=> "Oops."];
                                }else if (!$val_fecha['sw']) {
                                    $dias_solicitud = $val_fecha['dias_solicitud'];
                                    $resp = ['mensaje'=> "Este tipo de solicitud deben realizarse con $dias_solicitud dias calendario de anticipación.", 'tipo'=>"info", 'titulo'=> "Oops."];
                                    }
                            // }else{

                            $sw = true;
                                if(($id_tipo_solicitud == "Com_Env" && $presupuesto == 1) || $id_tipo_solicitud ==  "Com_Pub" ) {
                                    $existe_codigo = $this->genericas_model->obtener_valores_parametro_valox(25, $id_codigo_sap);
                                    if (empty($existe_codigo)) {
                                        $resp= ['mensaje'=>"El codigo sap no existe.",'tipo'=>"info",'titulo'=> "Oops.!"];
                                        $sw = false;
                                    }else{
                                        $id_codigo_sap = $existe_codigo[0]["id"];
                                    }
                                }else{
                                    $id_codigo_sap = null;
                                }
                                if($sw){
                                    $data = [
                                        'id_tipo_solicitud' => $id_tipo_solicitud,
                                        'fecha_inicio_evento' => $fecha_inicio_evento,
                                        'fecha_fin_evento' => $fecha_fin_evento,
                                        'id_tipo_evento' => $id_tipo_evento,
                                        'nombre_lugar' => $nombre_lugar,
                                        'direccion' => $direccion,
                                        'telefono' => $telefono,
                                        'nro_invitados' => $nro_invitados,
                                        'descripcion' => $descripcion,
                                        'id_codigo_sap' => $id_codigo_sap,
                                        'nombre_evento' => $nombre_evento,
                                        'id_categoria_divulgacion' => $id_categoria_divulgacion ,
                                        ];

                                        $resp= ['mensaje'=>"La solicitud fue modificada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                                        $add = $this->comunicaciones_model->modificar_datos($data, 'comunicaciones_solicitudes', $id);
                                        if($add != 0) $resp= ['mensaje'=>"Error al modificar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                                }                 
                            //}
                        }
                    //}
                }else{
                    $resp= ['mensaje'=>"No es posible realizar esta acción ya que La solicitud se encuentra en tramite o terminada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        }
    }
    echo json_encode($resp);
}


public function gestionar_solicitud()
{
    if(!$this->Super_estado){
        $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    }else{
        if ($this->Super_modifica == 0) {
            $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }else{
            $id = $this->input->post("id");
            $estado = $this->input->post("estado");
            $mensaje = $this->input->post("mensaje");
            $correo = $this->input->post("correo");
            $diseno = $this->input->post("diseno");
            $usuario = $_SESSION["persona"];
            $valido = $this->validar_estado($id, $estado);
            if ($valido) {
                $valido = $this->validar_estado_externo($id, $estado);
                if (!$valido) {
                    $resp = ['mensaje'=>"La solicitud no puede ser gestionada, verifique el estado en los modulos relacionados.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else{
                    $sw = true;
                    $data = [ "id_estado_solicitud" => $estado,];
                    if ($estado == 'Com_Rec_E'){ 
                        if (empty($mensaje)) {
                            $resp = ['mensaje'=>"Debe ingresar el motivo.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                            $sw = false;
                        }else{
                            $data['msj_negado'] = $mensaje; 
                        }
                    } else if ($estado == 'Com_Ent_E' && $diseno == 1){ 
                        if (empty($correo)) {
                            $resp = ['mensaje'=>"Debe ingresar el correo.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                            $sw = false;
                        }else if (empty($mensaje)) {
                            $resp = ['mensaje'=>"Debe ingresar una descripción.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                            $sw = false;
                        }
                    }else if ($estado == 'Com_Fin_E') {
                        $tiempo = $this->calcular_tiempo_gestion($id);
                        $data['tiempo_gestion'] = $tiempo['tiempo']; 
                        $data['tiempo_demora'] =  $tiempo['intervalo']; 
                    }
                    if ($sw) {
                        $resp= ['mensaje'=>"La solicitud fue gestionada con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        $mod = $this->comunicaciones_model->modificar_datos($data,'comunicaciones_solicitudes',$id);
                        if($mod != 0){
                            $resp= ['mensaje'=>"Error al gestionar la solicitud, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];  
                        }else{
                            $data = [ "id_solicitud" => $id,'id_estado' => $estado, 'id_usuario_registro' => $usuario , 'correo_disenador' => $correo , 'observacion' => $mensaje];
                            $mod = $this->comunicaciones_model->guardar_datos($data,'comunicaciones_estados_sol');
                            if ($estado == 'Com_Ent_E') {
                            $solictud = $this->comunicaciones_model->consulta_solicitud_id($id);
                                $presupuesto = $solictud->{'presupuesto'};
                                $tipo_solictud = $solictud->{'id_tipo_solicitud'};
                                if ($tipo_solictud == 'Com_Env') {
                                    if ($presupuesto == 1) {
                                        $data = [ "estado_solicitud" => 'Sol_soli'];
                                        $mod = $this->comunicaciones_model->modificar_datos($data,'solicitudes_adm',"id_evento_com = $id",2);
                                    }else{
                                        $data = [ "estado_solicitud" => 'Man_Sol'];
                                        $mod = $this->comunicaciones_model->modificar_datos($data,'solicitudes_mantenimiento',"id_evento_com = $id",2);
                                    }
                                }
             
                            }
                        }
                    }
                }

            }else{
                $resp = ['mensaje'=>"La solicitud ya fue gestionada anteriormente o no esta autorizado para realizar esta operación.",'tipo'=>"info",'titulo'=> "Oops.!", 'refres'=> 1]; 
            }
      
        }
    }
    echo json_encode($resp);
} 
public function validar_estado($id,$estado_nuevo){
    $solictud = $this->comunicaciones_model->consulta_solicitud_id($id);
    $estado_actual = $solictud->{'id_estado_solicitud'};
    $solicitante = $solictud->{'id_usuario_registra'};
    $tipo_solicitud = $solictud->{'id_tipo_solicitud'};
    $id_categoria_divulgacion = $solictud->{'id_categoria_divulgacion'};
    $administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Admin_Com'? true : false; 
    $persona = $_SESSION["persona"];

    if ($administra && $estado_actual == 'Com_Sol_E' && ($estado_nuevo == 'Com_Ent_E' || $estado_nuevo == 'Com_Rec_E' || $estado_nuevo == 'Com_Cor_E' || ($estado_nuevo == 'Com_Fin_E' && $id_categoria_divulgacion == 'Req_Vis'))) { 
        return true; 
    } else if($administra && $estado_actual == 'Com_Ent_E'){
         if($tipo_solicitud == 'Com_Div'){
            if($administra && ($estado_nuevo == 'Com_Rev_E' || $estado_nuevo == 'Com_Rec_E')) return true;
            else if($administra && ($estado_nuevo == 'Com_Fin_E' || $id_categoria_divulgacion == 'Req_Vid')) return true;
        }else{
            if($administra  && ($estado_nuevo == 'Com_Rec_E' || $estado_nuevo == 'Com_Fin_E'))return true;
        }
    }else if($administra && $estado_actual == 'Com_Ace_E' && ($estado_nuevo == 'Com_Rec_E' || $estado_nuevo == 'Com_Fin_E')){
        return true;
    }else if($administra && $estado_actual == 'Com_Des_E' && ($estado_nuevo == 'Com_Ent_E' || $estado_nuevo == 'Com_Rec_E')){
        return true;
    }else if($administra && $estado_actual == 'Com_Cor_E' && $estado_nuevo == 'Com_Sol_E'){
        return true;
    }else if(($persona == $solicitante || $_SESSION['perfil'] == 'Per_Admin') && $estado_actual == 'Com_Sol_E' && $estado_nuevo == 'Com_Can_E'){
        return true;
    }else if(($persona == $solicitante || $_SESSION['perfil'] == 'Per_Admin') && $estado_actual == 'Com_Rev_E' && ($estado_nuevo == 'Com_Ace_E' || $estado_nuevo == 'Com_Des_E')){
        return true;
    }else if(($persona == $solicitante) && $estado_actual == 'Com_Rec_E'  || $estado_nuevo == 'Com_Can_E'){
        return true;
    }else if(($persona == $solicitante) && $estado_actual == 'Com_Cor_E' && $estado_nuevo == 'Com_Sol_E'){
        return true;
    }
    return false;
  }
  public function guardar_encuesta_solicitud()
    {
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post("id");
                $calificacion = $this->input->post("rating");
                $fecha = date("Y-m-d H:i:s");
                $usuario = $_SESSION["persona"];
                $observacion = $this->input->post("observacion");
                $rating = $this->input->post("rating");
                $solictud = $this->comunicaciones_model->consulta_solicitud_id($id);
                $con_calificacion = $solictud->{'calificacion'};
                $usuario_solicitud = $solictud->{'id_usuario_registra'};
                if (ctype_space($rating) || empty($rating) || !is_numeric($rating)) {
                    $resp = ['mensaje'=>"Debe seleccionar una calificación.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else if (!is_null($con_calificacion)) {
                    $resp = ['mensaje'=>"La solicitud ya fue calificada anteriormente.", 'tipo'=>"info", 'titulo'=> "Oops.!", 'refres'=> 1];
                }else if ($usuario_solicitud != $usuario && $_SESSION['perfil'] != 'Per_Admin') {
                    $resp = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.", 'tipo'=>"info", 'titulo'=> "Oops.!",'refres'=> 1];
                }else{
                    $data = [
                        "id_usuario_califica" => $usuario,
                        "fecha_califica" => $fecha,
                        "calificacion" => $calificacion ,
                        "obs_califica" => $observacion ,
                        ];
                    $resp= ['mensaje'=>"La solicitud fue calificada con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    $del = $this->comunicaciones_model->modificar_datos($data,'comunicaciones_solicitudes',$id);
                    if($del != 0)$resp= ['mensaje'=>"Error al calificar la solicitud, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
               
                }
        
            }

        
        }
        echo json_encode($resp);
    } 

    
  public function validar_codigo_sap()
  {
      if(!$this->Super_estado){
          $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
      }else{
        $id_codigo_sap = $this->input->post("id_codigo_sap");
        if (ctype_space($id_codigo_sap) || empty($id_codigo_sap)) {
            $resp = ['mensaje'=>"Debe ingresar un codigo Sap.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
        }else{
            $existe_codigo = $this->genericas_model->obtener_valores_parametro_valox(25, $id_codigo_sap);
            if (ctype_space($existe_codigo) || empty($existe_codigo)) {
                $resp = ['mensaje'=>"Debe ingresar un codigo Sap válido.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
            }else {
                $resp= ['mensaje'=>"El codigo sap es válido.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!", 'id' => $existe_codigo[0]['id']];
            }
        }      
      }
      echo json_encode($resp);
  } 

  public function verificar_servicios_normales()
  {
      $id_tipo_solicitud = $this->input->post("id");
      $resp = $this->Super_estado ? $this->comunicaciones_model->listar_servicios_solicitud($id_tipo_solicitud, 'si') : array();
      echo json_encode($resp);
  } 
  public function calcular_tiempo_gestion($id)
  {
    $tiempo_sol = 0; 
    $interval = 0;
    $resp = 0;
    $solicitud = $this->comunicaciones_model->consulta_solicitud_id($id);
    $id_solicitud = $solicitud->{'id'}; 
    $tipo_solicitud = $solicitud->{'id_tipo_solicitud'}; 
    $categoria = $solicitud->{'id_categoria_divulgacion'};  
    $estado_solicitud = $solicitud->{'id_estado_solicitud'};  
    $fecha_actual = date("Y-m-d");
    $fecha_inicio = date("Y-m-d",strtotime($solicitud->{'fecha_registra'}));

    if ($tipo_solicitud == 'Com_Div') {
        $tiempo_sol = $this->genericas_model->obtener_valores_parametro_aux($categoria, 63)[0]['valory'];
        if ($categoria == 'Req_Vis') {
            $resp = 0;
            $fecha_inicio = date("Y-m-d",strtotime($fecha_inicio." +1 days")); 
            while ($fecha_inicio <= $fecha_actual) {
                if ($this->es_habil($fecha_inicio)) {
                    $resp += 1;
                }
                $fecha_inicio = date("Y-m-d",strtotime($fecha_inicio." +1 days")); 
            }
        }else {
            $inicio = new DateTime($fecha_inicio);
            $fin = new DateTime($fecha_actual);            
            $interval = $inicio->diff($fin);
            $resp_interval = $interval->format('%R%a');
            $dias_fuera_gestion = $this->dias_fuera_gestion($id_solicitud);
            $resp = $resp_interval - $dias_fuera_gestion;
        }
    }else{
        $tiempo_sol = $this->genericas_model->obtener_valores_parametro_aux($tipo_solicitud, 60)[0]['valory'];
    }
    return ["intervalo" => $resp,"tiempo"=>$tiempo_sol];
  } 
  public function notificaciones_gestion($tipo_solicitud,$categoria,$fecha_inicio,$fecha_actual,$tiempo_sol)
  {
    $interval = 0;
    $resp = 0;
    if ($tipo_solicitud == 'Com_Div') {
        if ($categoria == 'Req_Vis') {
            $resp = 0;
            $fecha_inicio = date("Y-m-d",strtotime($fecha_inicio." +1 days")); 
            while ($fecha_inicio <= $fecha_actual) {
                if ($this->es_habil($fecha_inicio)) {
                    $resp += 1;
                }
                $fecha_inicio = date("Y-m-d",strtotime($fecha_inicio." +1 days")); 
            }
        }else {
            $inicio = new DateTime($fecha_inicio);
            $fin = new DateTime($fecha_actual);
            $interval = $inicio->diff($fin);
            $resp = $interval->format('%R%a');
        }
    }
    return  $resp >= $tiempo_sol - 1 ? true : false;
  } 
  public function validar_detalle_servicio()
  {
    if(!$this->Super_estado){
        $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
    }else{
        $id_tipo = $this->input->post("id_tipo");
        $id_tipo_entrega = $this->input->post("id_tipo_entrega");
        $cantidad = $this->input->post("cantidad");
        $tipo_servicio = $this->input->post("tipo_servicio");

        $validar_ent = json_encode($id_tipo_entrega);
        $validar_tipo = json_encode($id_tipo);
        $validar_can = json_encode($cantidad);

        if ($validar_tipo != 'false' && empty($id_tipo))$resp = ['mensaje'=>"Seleccione un tipo.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
        else if ($validar_ent != 'false' && empty($id_tipo_entrega)) $resp = ['mensaje'=>"Seleccione el tipo de entrega.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
        else if ($validar_can != 'false' && empty($cantidad)) $resp = ['mensaje'=>"Ingrese una cantidad.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
        else if ($validar_can != 'false' && !is_numeric($cantidad)) $resp = ['mensaje'=>"Ingrese solo datos numericos en la cantidad.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
        else if ($validar_can != 'false' && $cantidad < 1) $resp = ['mensaje'=>"La cantidad debe ser mayor a 0.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
        else{
            $sw = true;
            if ($tipo_servicio == 'Man') {

                $id_servicio = $this->input->post("id_servicio");
                $fecha_inicial = $this->input->post("fecha_inicial");
                $fecha_final = $this->input->post("fecha_final");

                if (empty($id_servicio)){
                    $resp = ['mensaje'=>"Seleccione Servicio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    $sw = false;
                }else if(empty($fecha_inicial)){
                     $resp = ['mensaje'=>"Seleccione fecha de inicio del evento.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                     $sw = false;
                }else if (empty($fecha_final)) {
                    $resp = ['mensaje'=>"Seleccione fecha final del evento.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    $sw = false;
                }else {
                    $disponible =  $this->validar_existencias_mant($id_servicio,$cantidad, $fecha_inicial, $fecha_final);
                    if (!$disponible) {
                        $resp = ['mensaje'=>"La cantidad solicitada no se encuentra disponible.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        $sw = false;
                    }
                }
            }
            if ($sw) $resp= ['mensaje'=>"Servicio seleccionado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];       
        } 
    }
    echo json_encode($resp);
  } 
  public function listar_servicios_mantenimiento()
  {        
      $resp = $this->Super_estado ? $this->almacen_model->Listar_articulos('%%','%%','%%','Inv_Man') : array();
      echo json_encode($resp);
  }

  public function validar_existencias_mant($id,$cantidad , $fecha_inicial, $fecha_final){
    $existencia =  $this->almacen_model->get_existencia_articulo($id);
    $sw = true;
    if ($cantidad > $existencia)$sw = false;
    else {
        $can_sol =  $this->mantenimiento_model->articulos_solicitados_fecha($id, $fecha_inicial, $fecha_final);
        $disponibles = $existencia - $can_sol;
        if ($cantidad > $disponibles)$sw = false;
    } 
    return  $sw;
  }

  public function validar_estado_externo($id,$estado_nuevo){
    $solictud = $this->comunicaciones_model->consulta_solicitud_id($id);
    $presupuesto = $solictud->{'presupuesto'};
    $tipo_solicitud = $solictud->{'id_tipo_solicitud'};
    if ($tipo_solicitud == 'Com_Env') {
        if ($presupuesto == 1) {
            $adm = $this->comunicaciones_model->traer_solicitud_externa($id,'solicitudes_adm');
            if (!empty($adm)) {
                $estado_actual_adm = $adm->{'estado_solicitud'};
                if ($estado_nuevo == 'Com_Fin_E' && $estado_actual_adm != 'Sol_Apro') {
                    return false;
                }else if ($estado_nuevo == 'Com_Rec_E' && ($estado_actual_adm != 'Sol_Den' && $estado_actual_adm != 'Sol_Apro')) {
                    return false;
                }else if ($estado_nuevo == 'Com_Fin_E' && $estado_actual_adm == 'Sol_Den') {
                    return false;
                }
            }
        }else{
            $man = $this->comunicaciones_model->traer_solicitud_externa($id,'solicitudes_mantenimiento');
            if (!empty($man)) {
                $estado_actual_man = $man->{'estado_solicitud'};
                if ($estado_nuevo == 'Com_Fin_E' &&  ($estado_actual_man != 'Man_Eje' && $estado_actual_man != 'Man_Fin')) {
                    return false;
                }else if ($estado_nuevo == 'Com_Rec_E' && ($estado_actual_man != 'Man_Rec' && $estado_actual_man != 'Man_Can' )) {
                    return false;
                }
            }
        }
    }
    return true;
  }

  public function traer_solicitudes_terminadas_man(){
    $resp = $this->Super_estado ? $this->comunicaciones_model->traer_solicitudes_terminadas_man(): array();
    echo json_encode($resp);
  }
  public function traer_solicitudes_terminadas_adm(){
    $resp = $this->Super_estado ? $this->comunicaciones_model->traer_solicitudes_terminadas_adm(): array();
    echo json_encode($resp);
  }
  public function dias_fuera_gestion($id_solicitud){
    $data =  $this->comunicaciones_model->listar_estados_solicitud($id_solicitud);
    $total_dias = 0;
    foreach ($data as $key) {
        $estado = $key['id_estado'];
        $fecha_inicio = $key['fecha_registro'];
        if($estado == 'Com_Des_E' || $estado == 'Com_Ace_E' ) {
            $fecha_fin = $key['fecha_registro'];
            $inicio = new DateTime($fecha_inicio);
            $fin = new DateTime($fecha_fin);            
            $interval = $inicio->diff($fin);
            $resp_interval = $interval->format('%R%a');
            $total_dias = $total_dias + $resp_interval;
        }
    }
    return $total_dias;
}

public function get_personas_notificar(){
    $personas = array();
    if ($this->Super_estado) {
        $personas = $this->comunicaciones_model->get_personas_notificar();
    }
    echo json_encode($personas);
}

}
 