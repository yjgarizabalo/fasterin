<?php
class bienestar_laboral_control extends CI_Controller {
    // Variables encargadas de los permisos que tiene el usuario que esta en session 
    var $Super_estado = false;
    var $Super_agrega = 0;
    var $Super_modifica = 0;
    var $Super_elimina = 0;
    var $ruta_archivos_seguridad = "/archivos_adjuntos/bienestar_laboral/seguridad_trabajo";
    
    var $super_admin = false;
    // var $admin = false;
    // var $estado = null;
    //Se crea el contructor del controlador y se importa el modelo de becas_model y se inicia la session
    public function __construct(){
        parent::__construct();
        $this->load->model('bienestar_laboral_model');
        $this->load->model('genericas_model');
        $this->load->model('pages_model');
        session_start();
        date_default_timezone_set('America/Bogota');
        //la variable Super_estado es la encargada de notificar si el usuario esta en sesion, si no esta en sesion no podra ejecutar ninguna funcion, cuando pasa eso se retorna sin_session en la funcion que se esta ejecutando, por otro lado las variables Super_elimina, Super_modifica, Super_agrega se encarga de delimitar los permisos que tiene el perfil del usuario en la actividad que esta trabajando, si no tiene permiso las variables toman un valor de 0 y no les permite ejecutar la funcion retornando -1302.
        if(isset($_SESSION["usuario"])){
            $this->Super_estado = true;
            $this->Super_agrega = 1;
            $this->Super_modifica = 1;
            $this->Super_elimina = 1;
            $this->super_admin = $_SESSION["perfil"] == "Per_Admin";
        }
    }

    public function index($id = 0){
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'bienestar_laboral');
            if (!empty($datos_actividad)) {
              $pages = "bienestar_laboral";
              $data['js'] = "Bienestar_laboral";
              $data['id'] = $id;
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

    public function listar_solicitudes(){

        $estado = $this->input->post('filtro_estados');
        $tipo = $this->input->post('filtro_tipos');
        $clasificacion = $this->input->post('filtro_clasificacion');
        $fecha_i = $this->input->post('filtro_fecha_inicio');
        $fecha_f = $this->input->post('filtro_fecha_termina');
        $id = $this->input->post('id');

        $admin = $_SESSION["perfil"] === "Per_Admin" ? true : false;
        $adm_lab = $_SESSION["perfil"] === "Per_Adm_Lab" ? true : false;
        $aux_lab = $_SESSION["perfil"] === "Per_Aux_Lab" ? true : false;
        $ase_lab = $_SESSION["perfil"] === "Per_Ase_Lab" ? true : false;
        
        $persona = $_SESSION["persona"];

        $ver_solicitado = '<span  style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" ><span >ver</span></span>';
        $ver_tramitado = '<span  style="background-color: #f0ad4e;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
        $ver_finilizada = '<span style="background-color: #5cb85c;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
        $ver_aprobado = '<span  style="background-color: #428bca;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
        $ver_canc_rech = '<span  style="background-color: #d9534f;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';

        //Acciones módulo de SST
        $btn_modificar = '<span title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default modificar" style="color:#000000;"></span>';
        $btn_tramitar_seg = '<span title="Tramitar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="pointer fa fa-retweet btn btn-default tramitar_seg"></span>';
        $btn_finalizar_seg = '<span title="Finalizar" data-toggle="popover" data-trigger="hover" style="color: #00cc00;"class="pointer fa fa-check btn btn-default finalizar_seg"></span>';
        $btn_aceptar = '<span title="Aceptar" data-toggle="popover" data-trigger="hover" style="color: #00cc00;"class="pointer fa fa-check btn btn-default aceptar"></span>';
        $btn_enviar_mtto_seg = '<span title="Enviar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px" class="pointer fa fa-reply btn btn-default enviar_mtto_seg"></span>';
        $btn_rechazar_seg = '<span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;" class="pointer fa fa-ban btn btn-default rechazar_seg"></span>';
        $btn_cancelar_seg = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default cancelar_seg" style="color:#d9534f"></span>';

        //Acciones módulo de asesorías
        $btn_tramitar_ase = '<span title="Tramitar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="fa fa-calendar pointer btn btn-default tramitar_ase"></span>';
        $btn_cancelar_ase = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default cancelar_ase" style="color:#d9534f"></span>';
        $btn_modificar_ase = '<span title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default modificar_ase" style="color:#000000;"></span>';
        $btn_rechazar_ase = '<span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;" class="pointer fa fa-ban btn btn-default rechazar_ase"></span>';
        $btn_finalizar_ase = '<span title="Finalizar" data-toggle="popover" data-trigger="hover" style="color: #00cc00;"class="pointer fa fa-check btn btn-default finalizar_ase"></span>';


        $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
        $btn_abierta = '<span title="En proceso..." data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half" style="color:#428bca"></span>';

        $resp = []; 
        $solicitudes = $this->Super_estado ? $this->bienestar_laboral_model->listar_solicitudes($estado, $tipo, $clasificacion, $fecha_i, $fecha_f, $id) : array();
        foreach ($solicitudes as $key) {
            $key['TipoSolicitud'] = $key['clasificacion'] ?$key['clasificacion']:$key['TipoSolicitud'];
            $permiso = $key['permiso'];
            $push = true;
            if($key['id_tipo'] === 'Lab_Seg_Tra'){
                if($key['id_estado_solicitud'] === 'B_Lab_Soli'){
                    $key['ver'] = $ver_solicitado;
                    $key['accion'] = ($admin  || !is_null($permiso))? "$btn_tramitar_seg $btn_modificar" : "$btn_modificar $btn_cancelar_seg";
                }else if($key['id_estado_solicitud'] === 'B_Lab_Prog'){
                    $key['ver'] = $ver_tramitado;
                    $key['accion'] = ($admin  || !is_null($permiso))? "$btn_finalizar_seg $btn_rechazar_seg" : $btn_abierta;
                }else if($key['id_estado_solicitud'] === 'B_Lab_Tram'){
                    if($key['mtto'] == 1){
                        $key['ver'] = $ver_aprobado;
                        if($key['estado_mtto'] == 0){
                            $key['accion'] = $btn_abierta;
                        }else{
                            $key['accion'] = ($admin  || !is_null($permiso))? "$btn_finalizar_seg $btn_enviar_mtto_seg $btn_rechazar_seg" : $btn_abierta;
                        }
                    }else{
                        $key['ver'] = $ver_tramitado;
                        $key['accion'] = ($admin  || !is_null($permiso))? "$btn_finalizar_seg $btn_rechazar_seg" : $btn_abierta;
                    }
                }else if(($key['id_estado_solicitud'] === 'B_Lab_Env') || ($key['id_estado_solicitud'] === 'B_Lab_Tram')){
                    $key['ver'] = $ver_tramitado;
                    if($key['estado_mtto'] == 0){
                        $key['accion'] = $btn_abierta;
                    }else{
                        $key['accion'] = ($admin  || !is_null($permiso))? "$btn_finalizar_seg $btn_enviar_mtto_seg $btn_rechazar_seg" : $btn_abierta;
                    }
                }else if($key['id_estado_solicitud'] === 'B_Lab_Fina'){
                    $key['ver'] = $ver_finilizada;
                    $key['accion'] = "$btn_cerrada";
                }else if($key['id_estado_solicitud'] === 'B_Lab_Canc' || $key['id_estado_solicitud'] === 'B_Lab_Rech'){
                    $key['ver'] = $ver_canc_rech;
                    $key['accion'] = "$btn_cerrada";
                } 
            }else if($key['id_tipo'] === 'Lab_Ases'){
                if($key['id_estado_solicitud'] === 'B_Lab_Soli'){
                    $key['ver'] = $ver_solicitado;
                    $key['accion'] = ($admin  || !is_null($permiso))? "$btn_tramitar_ase $btn_rechazar_ase $btn_modificar_ase" : "$btn_modificar_ase $btn_cancelar_ase";
                }else if($key['id_estado_solicitud'] === 'B_Lab_Tram'){
                    $key['ver'] = $ver_tramitado;
                    if($key['id_clasificacion'] === 'Ase_Tip_Fin' || $key['id_clasificacion'] === 'Ase_Tip_Jur'){
                        $key['accion'] = ($ase_lab) ? "$btn_finalizar_ase $btn_rechazar_ase" : "$btn_abierta";
                    }else{
                        $key['accion'] = ($admin  || !is_null($permiso))? "$btn_finalizar_ase $btn_rechazar_ase" : "$btn_abierta";
                    }
                }else if($key['id_estado_solicitud'] === 'B_Lab_Fina'){
                    $key['ver'] = $ver_finilizada;
                    $key['accion'] = "$btn_cerrada";
                }else if($key['id_estado_solicitud'] === 'B_Lab_Rech'){
                    $key['ver'] = $ver_canc_rech;
                    $key['accion'] = "$btn_cerrada";
                }else if($key['id_estado_solicitud'] === 'B_Lab_Canc'){
                    $key['ver'] = $ver_canc_rech;
                    $key['accion'] = "$btn_cerrada";
                }
            }
            if (($adm_lab || $aux_lab || $ase_lab) && is_null($permiso)) $push = false;
            if($push) array_push($resp, $key);
        }
        echo json_encode($resp);
    }

    /*
    INICIO DE LAS FUNCIONES DEL MÓDULO SST
    */
    // Funcion el módulo de tipo SST
    public function agregar_solicitud_validacion(){
        $sw = true; 
        $lugar = $this->input->post('id_lugar');
        $ubicacion = $this->input->post('id_ubicacion'); 
        $desp = $this->input->post('descripcion');
        $str = $this->verificar_campos_string(['Lugar' => $lugar, 'Ubicación' => $ubicacion, 'Descripción' => $desp]);

        if(is_array($str)){
            $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"]; 
            $sw = false;
        }
        if($sw) $resp = ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
        echo json_encode($resp);
    }

    public function agregar_solicitud(){
        $resp = [];
        if(!$this->Super_estado == true) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $lugar = $this->input->post('id_lugar');
                $ubicacion = $this->input->post('id_ubicacion'); 
                $desp = $this->input->post('descripcion');
                $tipo = $this->input->post('tipo');
                $str = $this->verificar_campos_string(['Lugar' => $lugar, 'Ubicación' => $ubicacion, 'Descripción' => $desp]);

                if(is_array($str)){
                    $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"]; 
                }else{
                    $resp = ['mensaje'=>"Información almacenada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                    $data = [
                        'id_lugar' => $lugar? $lugar : NULL,
                        'id_ubicacion' => $ubicacion? $ubicacion : NULL,
                        'id_tipo' => $tipo,
                        'descripcion' => $desp,
                        'id_usuario_registro' => $_SESSION['persona'],
                        'id_solicitante' => $_SESSION['persona']
                    ];

                    $add_solicitud = $this->pages_model->guardar_datos($data, 'laboral_solicitudes');
                    if ($add_solicitud == -1) {
                        $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }else{
                        $solicitud = $this->bienestar_laboral_model->consulta_ultima_solicitud_id($_SESSION['persona']);
                        $resp['id'] = $solicitud->{'id'};
                        $resp['nombre'] = $solicitud->{'nombre'};
                        $resp['correo'] = $solicitud->{'correo'};
                        $data_estado = [
                            'id_solicitud' => $solicitud->{'id'},
                            'id_estado' => $solicitud->{'id_estado_solicitud'},
                            'id_usuario_registro' => $_SESSION['persona'],
                        ];
                        $add_estado = $this->pages_model->guardar_datos($data_estado, 'laboral_estados');
                        if($add_estado == -1){
                            $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_solicitud(){
        if(!$this->Super_estado ==  true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if($this->Super_agrega == 0){
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post('id');
                $lugar = $this->input->post('id_lugar');
                $ubicacion = $this->input->post('id_ubicacion'); 
                $desp = $this->input->post('descripcion');
                $estado = $this->bienestar_laboral_model->consulta_solicitud_id($id);
                if($estado->{'id_estado_solicitud'} === 'B_Lab_Soli'){
                    $data = [
                        'id_lugar' => $lugar? $lugar : NULL,
                        'id_ubicacion' => $ubicacion? $ubicacion : NULL,
                        'descripcion' => $desp,
                        'id_usuario_registro' => $_SESSION['persona']
                    ];
                    $modi_soli = $this->pages_model->modificar_datos($data, 'laboral_solicitudes', $id);
                    if($modi_soli == -1){
                        $resp = ['mensaje'=>"Error al guardar la solicitud apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Información modificada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                }else{
                    $resp = ['mensaje'=>"No puede modificar la solicitud una vez tramitada",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_archivos_seguridad(){
        $resp = [];
        if(!$this->Super_estado){
            $resp = ['mensaje' => "", 'tipo'=>"sin_session", 'titulo'=>""];
        }else{
            $btn_eliminar = "<span title='Eliminar' style='color: #DE4D4D;' data-toggle='popover' data-trigger='hover' class='fa fa-trash-o pointer btn btn-default eliminar_adj'></span>";
            $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            $id_sol = $this->input->post('id_solicitud');
            $archivos = $this->bienestar_laboral_model->listar_archivos_seguridad($id_sol);
            $solicitud = $this->bienestar_laboral_model->consulta_solicitud_id($id_sol);

            foreach ($archivos as $key) {
                ($solicitud->{'id_estado_solicitud'} === 'B_Lab_Soli')? $key['acciones'] = $btn_eliminar : $key['acciones'] = $btn_cerrada;
                array_push($resp,  $key);
            }
        }
        echo json_encode($resp);
    }

    public function detalle_estados(){
        
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $admin = ($_SESSION["perfil"] === "Per_Admin")? true : false;
            $id = $this->input->post('id_estado');
            $resp = $this->bienestar_laboral_model->listar_estados($id);
        }
        echo json_encode($resp);
    }

    public function detalle_mantenimiento(){
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $admin = ($_SESSION["perfil"] === "Per_Admin")? true : false;
            $id = $this->input->post('id_sol');
            $mtto = $this->bienestar_laboral_model->listar_estados_mtto($id);

            foreach($mtto as $i){
                $i['ver'] = '<span style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" ><span >ver</span></span>';
                array_push($resp, $i);
            }
        }
        echo json_encode($resp);
    }

    public function cambiar_estado_adj(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $admin = $_SESSION["perfil"] == "Per_Admin" ? true : false;
            $id = $this->input->post('id');
            $id_solicitud = $this->input->post('id_solicitud');
            $cant_adj = $this->bienestar_laboral_model->listar_archivos_seguridad($id_solicitud);
            $data = [
                'estado' => 0
            ];
            $adj = $this->pages_model->modificar_datos($data, 'laboral_adj_seguridad', $id);
            $resp = ['mensaje'=>"Archivo eliminado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
            if($adj == -1){
                $resp = ['mensaje'=>"Error al eliminar el archivo apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
            }
        }
        echo json_encode($resp);
    }

    public function cambiarEstado(){
        if(!$this->Super_estado){
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		}else{
			$id_solicitud = $this->input->post("id");
            $estado = $this->input->post("estado");
            $id_usuario_registra = $_SESSION["persona"];
            $tipo = $this->input->post('tipo'); // tipo dependiendo el tipo de solicitud
            $descripcion = $this->input->post('descripcion');
            $lugar = $this->input->post('lugar');
            $ubicacion = $this->input->post('ubicacion');
            $mtto = $this->input->post('mtto');
            $observaciones = $this->input->post('observaciones');
            $tel = $this->input->post('tel');
            $data_razon = $this->input->post('data_razon');
            $personas = $this->input->post('personas');
            $id_tipo_persona = $this->input->post('id_tipo_persona');
            $desc_acto = $this->input->post('desp_acto');
            $tipo_solicitud = $this->input->post('id_tipo'); //Tipo de solicitud

            $valido = $this->validar_estado($id_solicitud, $estado);
            
            if($valido){
                $data = [
                    'id_solicitud' => $id_solicitud,
                    'id_estado' => $estado,
                    'id_usuario_registro' => $id_usuario_registra
                ];
                $data_solicitud = [
                    'id_estado_solicitud' => $estado
                ];
                if(($estado === 'B_Lab_Canc' || $estado === 'B_Lab_Rech')){
                    $data_rc = [
                        'observacion' => $observaciones
                    ];
                    $add_rc = $this->pages_model->modificar_datos($data_rc, 'laboral_solicitudes', $id_solicitud);
                    if ($add_rc == -1){
                        $resp = ['mensaje' => 'Error al guardar la solicitud, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
                    }
                }
                if($tipo_solicitud === "Lab_Seg_Tra" && $estado === 'B_Lab_Env'){
                    $data_man = [
                        'solicitante_id' => $id_usuario_registra,
                        'descripcion' => $observaciones,
                        'ubicacion' => "$lugar - $ubicacion",
                        'id_seguridad' => $id_solicitud,
                        'telefono' => $tel
                    ];
                    $data_man_sol = [
                        'estado_mtto' => 0
                    ];
                    $add_man = $this->pages_model->guardar_datos($data_man, 'solicitudes_mantenimiento');
                    $add_man_sol = $this->pages_model->modificar_datos($data_man_sol, 'laboral_solicitudes', $id_solicitud);
                    if ($add_man == -1 || $add_man_sol == -1){
                        $resp = ['mensaje' => 'Error al guardar la solicitud, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
                    }
                }
                if($tipo_solicitud === "Lab_Seg_Tra" && ($estado === 'B_Lab_Prog' || $estado === 'B_Lab_Tram')){
                    $data_cla = [
                        'id_clasificacion' => $tipo,
                        'mtto' => $mtto
                    ];
                    if($estado === 'B_Lab_Prog'){
                        $data_acto = [
                            'detalle_acto' => $desc_acto
                        ];
                        $add_desc_acto = $this->pages_model->modificar_datos($data_acto, 'laboral_solicitudes', $id_solicitud);
                        if ($add_desc_acto == -1){
                            $resp = ['mensaje' => 'Error al modificar la solicitud, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
                        }
                    }
                    if($estado === 'B_Lab_Prog' && $id_tipo_persona === 'Tipo_Interna'){
                        $personas_acto = array();
                        if(!empty($personas)){
                            foreach ($personas as $user) {
                                array_push($personas_acto, array(
                                    'id_solicitud' => $id_solicitud,
                                    'id_persona' => $user['id'],
                                    'id_usuario_registro' => $id_usuario_registra
                                ));
                            }
                            $data_acto2 = [
                                'detalle_acto' => $desc_acto
                            ];
                            $add_desc_acto2 = $this->pages_model->modificar_datos($data_acto2, 'laboral_solicitudes', $id_solicitud);
                            $add_personas = $this->pages_model->guardar_datos($personas_acto, 'laboral_personas', 2);
                            if($add_personas == -1 || $add_desc_acto2 == -1){
                                $resp = ['mensaje'=>"Error al guardar la solicitud apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                            }
                        }
                    }
                    if(($estado === 'B_Lab_Tram' || $estado === 'B_Lab_Env') && $tipo === 'Seg_Tip_Cond'  && $mtto == 1 ){
                        $data_man = [
                            'solicitante_id' => $id_usuario_registra,
                            'descripcion' => $observaciones,
                            'ubicacion' => "$lugar - $ubicacion",
                            'id_seguridad' => $id_solicitud,
                            'telefono' => $tel
                        ];
                        $data_man_sol = [
                            'estado_mtto' => 0
                        ];
                        $add_man = $this->pages_model->guardar_datos($data_man, 'solicitudes_mantenimiento');
                        $add_man_sol = $this->pages_model->modificar_datos($data_man_sol, 'laboral_solicitudes', $id_solicitud);
                        if ($add_man == -1 || $add_man_sol == -1){
                            $resp = ['mensaje' => 'Error al guardar la solicitud, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
                        }
                    }
                    $add_cla = $this->pages_model->modificar_datos($data_cla, 'laboral_solicitudes', $id_solicitud);
                    if($add_cla == -1 ){
                        $resp = ['mensaje' => 'Error al guardar la solicitud, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
                    }
                }
                if($tipo_solicitud === "Lab_Seg_Tra" && $estado === 'B_Lab_Fina'){
                    $razones = array();
                    if(!empty($data_razon)){
                        foreach ($data_razon as $tool) {
                            array_push($razones, array(
                                'id_solicitud' => $id_solicitud,
                                'id_usuario_registro' => $id_usuario_registra,
                                'razon' => $tool['causa']
                            ));
                        }
                        $add_razones = $this->pages_model->guardar_datos($razones, 'laboral_fina_razones', 2);
                        if($add_razones == -1){
                            $resp = ['mensaje'=>"Error al guardar la solicitud apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }
                    }
                }
                $add = $this->pages_model->guardar_datos($data, 'laboral_estados');
                if($add == 1){
                    $add_sol = $this->pages_model->modificar_datos($data_solicitud, 'laboral_solicitudes', $id_solicitud);
                }
                if ($add == -1){
                    $resp = ['mensaje' => 'Error al modificar la solicitud, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
                }else{
                    $resp = ['mensaje' => "Proceso finalizado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                }
            }else{
                $resp = ['mensaje' => "No tiene permisos para realizar esta accion.", 'tipo' => "info", 'titulo' => "Oops.!"];
            }
	    }
		echo json_encode($resp);
    }

    public function gestionar_validacion(){
        $tipo = $this->input->post('id_clasificacion');
        $tel = $this->input->post('telefono');
        $mmto = $this->input->post('mtto');
        $id_tipo_persona = $this->input->post('id_tipo_persona');
        $personas = $this->input->post('personas');
        $descripcion_acto = $this->input->post('descripcion_acto');
        $str = $this->verificar_campos_string(['Clasificación' => $tipo]);
        $num = $this->verificar_campos_numericos(['Teléfono' => $tel]);
        $sw = true;
        if($tipo === ''){
            $resp = ['mensaje'=>"Debe diligenciar el campo Clasificación", 'tipo'=>"info", 'titulo'=>"Oops..!"];
            $sw = false;
        }
        if($tipo === 'Seg_Tip_Cond'){
            if(is_array($str)){
                $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false;
            }else if($mmto == 1){
                if(is_array($num)){
                    $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                    $sw = false;
                } 
            }
        }else if($tipo === 'Seg_Tip_Acto'){
            if($descripcion_acto === ''){
                $resp = ['mensaje'=>"Debe diligenciar el campo Detalle", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false;
            }
            if($id_tipo_persona === 'Tipo_Interna'){
                if($personas == '0'){
                    $resp = ['mensaje'=>"Debe agregar por lo menos 1 persona", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                    $sw = false;
                }
            }
        }
        if($sw){
            $resp = ['mensaje' => "", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
        } 
        echo json_encode($resp);
    }

    public function mostrar_notificaciones_seguridad(){
        if ($this->Super_estado == false) {
            echo json_encode('sin_session');
            return;
        }
        $data = array();
        $tipo = $this->input->post("tipo");
        $notificacion = $this->bienestar_laboral_model->mostrar_notificaciones_seguridad();
        echo json_encode($notificacion);
    }

    public function buscar_persona(){
        $personas = array();
        if ($this->Super_estado == true) {
                $dato = $this->input->post('dato');
                $buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1";
                $personas = $this->bienestar_laboral_model->buscar_persona($buscar);
            }
        echo json_encode($personas);
    }

    public function buscar_razones(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $buscar = $this->input->post('buscar');
            $causas = $this->Super_estado == true ? $this->bienestar_laboral_model->buscar_razones($buscar) : array();
            $resp = [];
            foreach ($causas as $row) {
                $row['agregado'] = 0;
                array_push($resp,$row);
            }
        }
        echo json_encode($resp);
    }


    public function verificar_causas(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        } else {
            $resp = array();
            $btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" style="color:#2E79E5"></span>';
            $btn_desasignar = '<span title="Desasignar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default eliminar" style="color:#d9534f"></span>';
            $causas = $this->input->post('causas');
            foreach ($causas as $row ) {
                $row['accion'] = $row['agregado'] == 1 ? $btn_desasignar : $btn_asignar;
                array_push($resp, $row);
            }
        }
        echo json_encode($resp);
    }
    /*
    FIN DE LAS FUNCIONES PARA EL MÓDULO SST
    */

    /*
    INICION DE LAS FUNCIONES PARA EL MÓDULO ASESORIA
    */
    public function agregar_solicitud_asesoria(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        } else {
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $tipo = $this->input->post('id_tipo');
                $clasificacion = $this->input->post('id_clasificacion');
                $desc = $this->input->post('descripcion_asesoria');
                $num_contacto = $this->input->post('numero_contacto');
                $nom_persona = $this->input->post('nombre_persona');
                $parentesco_persona = $this->input->post('id_beneficiario');
                $id_solicitante= $this->input->post('id_trabajador_solicitante');
                $str = $this->verificar_campos_string(['Clasificación' => $tipo]);
                if(is_array($str)){
                    $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"]; 
                }else{
                    $resp = ['mensaje'=>"Información almacenada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                    $data = [
                        'id_tipo' => $tipo,
                        'id_clasificacion' => $clasificacion,
                        'descripcion' => $desc,
                        'numero_contacto' => ($num_contacto == '') ? NULL : $num_contacto,
                        'nombre_persona' => ($nom_persona == '') ? NULL : $nom_persona,
                        'parentesco_persona' =>  ($parentesco_persona == '') ? NULL : $parentesco_persona, 
                        'id_usuario_registro' => $_SESSION['persona'],
                        'id_solicitante'=>($id_solicitante == '') ? $_SESSION['persona'] : $id_solicitante,
                    ];
                    $add_solicitud = $this->pages_model->guardar_datos($data, 'laboral_solicitudes');
                    if ($add_solicitud == -1) {
                        $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }else{
                        $solicitud = $this->bienestar_laboral_model->consulta_ultima_solicitud_id($_SESSION['persona']);
                        $resp['id'] = $solicitud->{'id'};
                        $resp['nombre'] = $solicitud->{'nombre'};
                        $resp['correo'] = $solicitud->{'correo'};
                        $data_estado = [
                            'id_solicitud' => $solicitud->{'id'},
                            'id_estado' => $solicitud->{'id_estado_solicitud'},
                            'id_usuario_registro' => $_SESSION['persona']
                        ];
                        $add_estado = $this->pages_model->guardar_datos($data_estado, 'laboral_estados');
                        if($add_estado == -1){
                            $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_solicitud_asesoria(){
        if(!$this->Super_estado ==  true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if($this->Super_agrega == 0){
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post('id');
                $desp = $this->input->post('descripcion_asesoria_md');
                $numero_contacto = $this->input->post('numero_contacto_md');
                $nom_persona = $this->input->post('nombre_persona_md');
                $parentesco_persona = $this->input->post('id_beneficiario_md');
                $estado = $this->bienestar_laboral_model->consulta_solicitud_id($id);
                if($estado->{'id_estado_solicitud'} === 'B_Lab_Soli'){
                    $data = [
                        'descripcion' => $desp,
                        'numero_contacto' => ($numero_contacto == '') ? NULL : $numero_contacto,
                        'nombre_persona' => ($nom_persona == '') ? NULL : $nom_persona,
                        'parentesco_persona' =>  ($parentesco_persona == '') ? NULL : $parentesco_persona, 
                    ];
                    $modi_soli = $this->pages_model->modificar_datos($data, 'laboral_solicitudes', $id);
                    if($modi_soli == -1){
                        $resp = ['mensaje'=>"Error al guardar la solicitud apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Información modificada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                }else{
                    $resp = ['mensaje'=>"$numero_contacto",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }
            }
        }
        echo json_encode($resp);
    }

    public function agregar_solicitud_asesoria_validacion(){
        if(!$this->Super_estado){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            $sw = true; 
            $desp = $this->input->post('descripcion_asesoria');
            $str = $this->verificar_campos_string(['Descripción' => $desp]);
            $numero_contacto = $this->input->post('numero_contacto');
            $num = $this->verificar_campos_numericos_opcional(['Numero de Contacto' => $numero_contacto]);
    
            if(is_array($str)){
                $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"]; 
                $sw = false;
            }
    
            if(is_array($num)){
                $resp = ['mensaje'=>"Debe diligenciar un valor valido en el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"]; 
                $sw = false;
            }
    
            if($sw) $resp = ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
        }
        echo json_encode($resp);
    }

    /*
    FIN DE LAS FUNCIONES PARA EL MÓDULO ASESORIA
    */

    /*
    INICIO FUNCIONES AUXILIARES PARA LOS MOSULOS
    */
    public function recibir_archivos(){
        $id_solicitud = $_POST['solicitud'];
        $tipo_adj = '1';
        if($id_solicitud !== 'null'){
            $solicitud = $this->bienestar_laboral_model->consulta_solicitud_id($id_solicitud);
            $sw = $id_solicitud;
            if($solicitud->{'id_estado_solicitud'} === 'B_Lab_Fina'){
                $tipo_adj = '2';
            }
        }else{
            $solicitud = $this->bienestar_laboral_model->consulta_ultima_solicitud_id($_SESSION['persona']);
            $sw = $solicitud->{'id'};
        }
        $ver_estado = $this->verificar_estado($solicitud->{'id_estado_solicitud'});
        if ($ver_estado) {
        $nombre = $_FILES["file"]["name"];
        $cargo = $this->cargar_archivo("file", $this->ruta_archivos_seguridad, "lab");
            if ($cargo[0] == -1) {
                header("HTTP/1.0 400 Bad Request");
                echo ($nombre);
                return;
            }

            $data = [
                "id_solicitud" => $sw,
                "nombre_real" => $nombre,
                "nombre_guardado" => $cargo[1],
                "id_usuario_registra" => $_SESSION['persona'],
                'tipo_adj' => $tipo_adj
            ];

            $res = $this->pages_model->guardar_datos($data, 'laboral_adj_seguridad');
            if ($res == -1) {
                header("HTTP/1.0 400 Bad Request");
                echo ($nombre);
                return;
            }
            $res = ['mensaje'=>"Todos Los archivos fueron cargados.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
        }else{
            $res = ['mensaje'=>"No puede agregar archivos a la solicitud en esta etapa del proceso",'tipo'=>"info",'titulo'=> "Oops.!"];
        }
        echo json_encode($res);
        return;
    }

    function cargar_archivo($mi_archivo, $ruta, $nombre){
        $nombre .= uniqid();
        $real_path = realpath(APPPATH . '../' . $ruta);
        $config['upload_path'] = $real_path;
        $config['file_name'] = $nombre;
        $config['allowed_types'] = "*";
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

    public function validar_estado($id, $estado_nuevo){
        $persona = $_SESSION["persona"];
        $solicitud = $this->bienestar_laboral_model->consulta_solicitud_id($id);
        $solicitante = $solicitud->{'id_usuario_registro'};
        $estado_actual = $solicitud->{'id_estado_solicitud'};
        $tipo = $solicitud->{'id_tipo'};
        $clasificacion = $solicitud->{'id_clasificacion'};

        $permisos = $this->bienestar_laboral_model->validar_permisos($persona, $tipo, $clasificacion, $estado_actual);
        $admin = ($permisos || $_SESSION['perfil'] == 'Per_Admin' ) ? true : false;
           
        $resp = false;
        if($tipo == 'Lab_Seg_Tra'){
            if( $admin  && $estado_actual == 'B_Lab_Soli' && ($estado_nuevo == 'B_Lab_Prog' || $estado_nuevo == 'B_Lab_Tram')) $resp = true;
            else if ($persona && $estado_actual == 'B_Lab_Soli' && $estado_nuevo == 'B_Lab_Canc') $resp = true;
            else if(($admin) && ($estado_actual == 'B_Lab_Prog' || $estado_actual == 'B_Lab_Tram') && ($estado_nuevo == 'B_Lab_Fina' || $estado_nuevo == 'B_Lab_Env' || $estado_nuevo == 'B_Lab_Rech')) $resp = true;
            else if(($admin) && $estado_actual == 'B_Lab_Env' && ($estado_nuevo == 'B_Lab_Fina' || $estado_nuevo == 'B_Lab_Rech')) $resp = true;
            else return $resp = false;
        }else if($tipo == 'Lab_Ases'){
            if( $admin && $estado_actual == 'B_Lab_Soli' && ($estado_nuevo == 'B_Lab_Tram' || $estado_nuevo == 'B_Lab_Rech')) $resp = true;
            else if ($persona && $estado_actual == 'B_Lab_Soli' && $estado_nuevo == 'B_Lab_Canc' ) $resp = true;
            else if($admin && ($estado_actual == 'B_Lab_Tram') && ($estado_nuevo == 'B_Lab_Fina' || $estado_nuevo == 'B_Lab_Rech')) $resp = true;
        }
        return $resp;
    }

    public function obtener_empleados(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Lab" ? true : false;
            $persona = $_SESSION["persona"];
            
            $resp = array();
            
            $tipoPermiso = $this->input->post('tipoPermiso');
            ($tipoPermiso === 'Aux') ? $where = "p.id_perfil = 'Per_Adm_Lab' OR p.id_perfil = 'Per_Aux_Lab'" : $where = "p.id_perfil = 'Per_Ase_Lab'";;
            $resp = $this->bienestar_laboral_model->obtener_empleados($where);
        }
        echo json_encode($resp);
    }

    public function listar_procesos_lab(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $resp = array();
            $btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar_lab" style="color:#2E79E5"></span>';
            $btn_desasignar = '<span title="Desasignar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default retirar_lab" style="color:#d9534f"></span>';
            $btn_administrar = '<span class="fa fa-cog btn btn-default administrar_lab" title="Administrar" data-toggle="popover" data-trigger="hover" style="color:black;"></span>';
            $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Lab" ? true : false;
            $id = $this->input->post('id');
            $tipoPermiso = $this->input->post('tipoPermiso');
            $procesos = $administra ? ($tipoPermiso === 'Aux') ? $this->bienestar_laboral_model->listar_procesos_lab($id) : $this->bienestar_laboral_model->listar_asesorias_lab($id) : array();
            foreach ($procesos as $row) {
                $row['accion'] = is_null($row['tipo']) ? $btn_asignar : "$btn_desasignar $btn_administrar";
                array_push($resp,$row);
            }
        }
        echo json_encode($resp);
    }

    public function listar_estados_lab(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $resp = array();
            $btn_asignar = '<span title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar_est" style="color:#2E79E5"></span>';
            $btn_desasignar = '<span title="Desasignar" data-toggle="popover" data-trigger="hover" class="fa fa-times btn btn-default retirar_est" style="color:#d9534f"></span>';
            $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Lab" ? true : false;
            $id = $this->input->post('id');
            $estados = $administra ? $this->bienestar_laboral_model->listar_estados_lab($id) : array();
            foreach ($estados as $row) {
                $row['accion'] = is_null($row['tipo']) ? $btn_asignar : $btn_desasignar;
                array_push($resp,$row);
            }
        }
        echo json_encode($resp);
    }

    public function asignar_proceso_persona(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Lab" ? true : false;
            $persona = $_SESSION["persona"];
            $id_auxiliar = $this->input->post('id');
            $id_proceso = $this->input->post('id_aux');
            $tipoPermiso = $this->input->post('tipoPermiso');
            $ver = $this->bienestar_laboral_model->obtener_asigacion_aux($id_auxiliar,$id_proceso,false);
            $data = [
                'id_tipo_sol' => $id_proceso,
                'id_auxiliar' => $id_auxiliar,
                'tipo_permiso' => $tipoPermiso,
                'id_usuario_registro' => $persona
            ];
            if(empty($ver)){
                $add = $this->pages_model->guardar_datos($data,'laboral_personas_proceso');
                $resp = ['mensaje'=>"Asignacion exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                if($add == -1){
                    $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }else{
                $resp = ['mensaje'=>"El auxiliar ya se encuentra asignado a este proceso.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }
        }
        echo json_encode($resp);
    }

    public function retirar_procesos_persona(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Lab" ? true : false;
            $id = $this->input->post('tipo');
            $ver = $this->bienestar_laboral_model->exist($id,'laboral_personas_proceso');
            if(empty($ver)){
                $resp = ['mensaje'=>"El proceso ya le fue retirado al auxiliar.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $ret = $this->bienestar_laboral_model->eliminar_registro($id,'laboral_personas_proceso');
                $resp = ['mensaje'=>"Retiro exitoso.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                if ($ret != 0) {
                    $resp = ['mensaje'=>"Error al retirar persona, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function asignar_estado_proceso(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Lab" ? true : false;
            $persona = $_SESSION["persona"];
            $id_proceso_persona = $this->input->post('id');
            $id_estado = $this->input->post('id_aux');
            $id_auxiliar = $this->input->post('id_auxiliar');
            $id_tipo_sol = $this->input->post('tipo_sol');
            $ver = $this->bienestar_laboral_model->obtener_asigacion_aux($id_auxiliar,$id_tipo_sol,$id_estado);
            $data = [
                'id_proceso_persona' => $id_proceso_persona,
                'id_estado' => $id_estado,
                'id_usuario_registro' => $persona
            ];
            if(empty($ver)){
                $add = $this->pages_model->guardar_datos($data,'laboral_estados_procesos');
                $resp = ['mensaje'=>"Asignacion exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                if($add == -1){
                    $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }else{
                $resp = ['mensaje'=>"El auxiliar ya se encuentra asignado a este estado.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }
        }
        echo json_encode($resp); 
    }


    public function retirar_estado_proceso(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $administra = $_SESSION["perfil"] == "Per_Admin" || $_SESSION['perfil'] == "Per_Adm_Lab" ? true : false;
            $id = $this->input->post('tipo');
            $ver = $this->bienestar_laboral_model->exist($id,'laboral_estados_procesos');
            if (empty($ver)) {
                $resp = ['mensaje'=>"El estado ya le fue retirado al auxiliar.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $ret = $this->bienestar_laboral_model->eliminar_registro($id,'laboral_estados_procesos');
                $resp = ['mensaje'=>"Retiro exitoso.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                if ($ret != 0) {
                    $resp = ['mensaje'=>"Error al retirar persona, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function generar_carta($id){
        if($this->Super_estado){
            $persona = $this->bienestar_laboral_model->consulta_solicitud_id($id);
            if($persona){
                if($persona->{'id_clasificacion'} === 'Ase_Tip_Psi'){
                    $data['nombre_completo'] = $persona->{'nombre_completo'};
                    $data['nombre_archivo'] = $id . '.pdf';
                    $data['cedula'] = $persona->{'cedula'};
                    $this->load->view("templates/descarga_carta_asesoria", $data);
                    return;
                }else if($persona->{'id_clasificacion'} === 'Ase_Tip_Jur'){
                    $data['nombre_archivo'] = $id . '.pdf';
                    $this->load->view("templates/descarga_carta_asesoria_2", $data);
                    return;
                }
            }
        } 
        redirect('/', 'refresh');
    }


    public function listar_razones(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $id = $this->input->post('id');
            $resp = $this->bienestar_laboral_model->listar_razones($id);
        }
        echo json_encode($resp);
    }

    public function consultar_solicitud(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $id = $this->input->post('id');
            $resp = $this->bienestar_laboral_model->consulta_solicitud_id($id);
        }
        echo json_encode($resp);
    }

    public function listar_personas(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $id = $this->input->post('id');
            $resp = $this->bienestar_laboral_model->listar_personas($id);
        }
        echo json_encode($resp);
    }

    public function obtener_personas_permisos(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $clasificacion = $this->input->post('clasificacion');
            $estado = $this->input->post('estado');
            $resp = $this->bienestar_laboral_model->obtener_personas_permisos($clasificacion, $estado);
        }
        echo json_encode($resp);
    }

    public function obtener_parametros_generales(){
        if(!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>'sin_session','titulo'=>""];
        }else {
            $id_aux = $this->input->post('id_aux');
            $resp = $this->bienestar_laboral_model->obtener_parametros_generales($id_aux);
        }
        echo json_encode($resp);
    }

    public function verificar_estado($estado)
    {
      if ($estado === 'B_Lab_Soli' || $estado === 'B_Lab_Fina') return true;
      else return false;
    }

    public function verificar_campos_string($array){
		foreach ($array as $row) {
			if (empty($row) || ctype_space($row)) {
				return ['type' => -2, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
    }
    
	public function verificar_campos_numericos($array){
		foreach ($array as $row) {
			if (!is_numeric($row)) {
				return ['type' => -1, 'field' => array_search($row, $array, true)];
			}
		}
	    return 1;
    }

    public function verificar_campos_numericos_opcional($array){
		foreach ($array as $row) {
            if($row == ''){
                return 1;
            }else if (!is_numeric($row) ) {
				return ['type' => -1, 'field' => array_search($row, $array, true)];
			}
		}
	    return 1;
    }

    public function buscar_solicitantes(){
        $resp = array();
        $tipo = $this->input->post("tipo");
        $dato = $this->input->post("dato");
        $tabla = 'personas';
	    if (!empty($dato)) $resp = $this->Super_estado == true ? $this->bienestar_laboral_model->buscar_solicitantes( $tabla, $dato) : array();
        echo json_encode($resp);
    }

    /*
    FIN FUNCIONES AUXILIARES PARA LOS MOSULOS
    */
}
