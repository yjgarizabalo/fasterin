<?php
class becas_control extends CI_Controller {
    // Variables encargadas de los permisos que tiene el usuario que esta en session 
    var $Super_estado = false;
    var $Super_agrega = 0;
    var $Super_modifica = 0;
    var $Super_elimina = 0;
    var $ruta_archivos_becas = "/archivos_adjuntos/becas";
    var $ruta_soportes_becas = "/archivos_adjuntos/becas/soportes";

    
    var $super_admin = false;
    // var $admin = false;
    // var $estado = null;


    //Se crea el contructor del controlador y se importa el modelo de becas_model y se inicia la session
    public function __construct(){
        parent::__construct();
        $this->load->model('becas_model');
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
            $this->super_admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec";
        }
    }
    // lista
    public function index($id = 0){
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'becas');
            if (!empty($datos_actividad)) {
              $pages = "becas";
              $data['js'] = "Becas";
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
    // lista
    public function obtener_valores_parametro(){
        $parametro = $this->input->post('buscar');
        $nivel = $this->Super_estado == true ? $this->becas_model->obtener_valores_parametro($parametro) : array();
        echo json_encode($nivel);
    }
    // Lista
    public function guardarBorrador(){
        if(!$this->Super_estado == true) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id_usuario_registro = $_SESSION['persona'];
            $info_profesor = $this->becas_model->informacion_profesor($id_usuario_registro);
            $id_renovacion = $this->input->post('id_ren');
            $id_departamento = $this->input->post('id_depa');
            $id_programa = $this->input->post('id_prog');
            $id_vinculacion = $this->input->post('id_vinc');

            if (!$info_profesor && !$id_departamento && !$id_programa && !$id_vinculacion) {
                $resp = ['mensaje' => '', 'tipo' => 'sin_info_docente', 'titulo' => ''];
            } else {
                if ($id_renovacion) {
                    $data = [
                        'id_usuario_registro' => $id_usuario_registro,
                        'id_renovacion' => $id_renovacion,
                        'id_tipo' => 'Soli_Tip_Ren'
                    ];
                } else {
                    $data = ['id_usuario_registro' => $id_usuario_registro];
                }

                $data['id_departamento_persona'] = $info_profesor ? $info_profesor->{'id_departamento'} : $id_departamento;
                $data['id_programa_persona'] = $info_profesor ? $info_profesor->{'id_programa'} :  $id_programa;
                $data['id_vinculacion_persona'] = $info_profesor ? $info_profesor->{'id_dedicacion'} : $id_vinculacion;
                $data['salario_persona'] = $info_profesor ? $info_profesor->{'salario'} : NULL;
                
                $resp = ['mensaje' => "Información almacenada con exito", 'tipo' => "success", 'titulo' => "Proceso exitoso!"];
                $add_solicitud = $this->pages_model->guardar_datos($data, 'becas_solicitudes');
                if ($add_solicitud == -1) {
                    $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                } else {
                    $solicitud = $this->becas_model->traer_ultima_solicitud($id_usuario_registro);
                    if ($solicitud->{'id_tipo'} === 'Soli_Tip_Ren') {
                        $info = $this->becas_model->info_solicitud_renovacion($solicitud->{'id'});
                        $solicitud = $info;
                    }
                    $resp['solicitud'] = $solicitud;
                    $data_estado = [
                        'id_solicitud' => $solicitud->{'id'},
                        'id_estado' => $solicitud->{'id_estado_solicitud'},
                        'id_usuario_registra' => $_SESSION['persona'],
                    ];
                    $add_estado = $this->pages_model->guardar_datos($data_estado, 'becas_estado');
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
                $admitido_al_programa = $this->input->post('admitido_al_programa');
                $fecha_inicio = $this->input->post('fecha_inicio');
                $fecha_termina = $this->input->post('fecha_termina');
                $id_year = $this->input->post('id_year');
                $id_nivel_formacion = $this->input->post('id_nivel_formacion');
                $id_semestre = $this->input->post('id_semestre');
                $institucion = $this->input->post('institucion');
                $linea_investigacion = $this->input->post('linea_investigacion');
                $pin = $this->input->post('pin');
                $programa = $this->input->post('programa');
                $ranking = $this->input->post('ranking');
                $tipo_duracion_programa = $this->input->post('tipo_duracion_programa');
                $id_usuario_registro = $_SESSION['persona'];
                $data = [
                    'id_usuario_registro' => $id_usuario_registro,
                    'institucion' => $institucion,
                    'programa' => $programa,
                    'ranking' => $ranking,
                    'tipo_duracion_programa' => $tipo_duracion_programa,
                    'id_duracion' => $id_year,
                    'admitido_al_programa' => $admitido_al_programa,
                    'linea_investigacion' => $linea_investigacion,
                    'pin' => $pin,
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_termina' => $fecha_termina,
                    'id_semestre' => $id_semestre,
                    'id_nivel_formacion' => $id_nivel_formacion
                ];

                $modi_soli = $this->pages_model->modificar_datos($data, 'becas_solicitudes', $id);
                if($modi_soli == -1){
                    $resp = ['mensaje'=>"Error al modificar la solicitud apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                }
                $resp = ['mensaje'=>"Información modificada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
            }
        }
        echo json_encode($resp);
    }

    //REFACTORIZANDO AGREGAR CONCEPTO
    public function agregar_concepto_solicitud(){
        if(!$this->Super_estado == true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $estado = $this->becas_model->estado($id_solicitud);
                if($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr'){
                    $beca_incluye = $this->input->post('beca_incluye');
                    $tipoApoyo = $this->input->post('tipo_apoyo');
                    $incluye_beca = $this->input->post('incluye_beca');
                    $valor_total = $this->input->post('valorTotal');
                    $apoyo_solicitado = $this->input->post('apoyoSolicitado');
                    $id_usuario_registro = $_SESSION['persona'];
                    $conceptos = $this->becas_model->validar_conceptos($id_solicitud, $tipoApoyo);
                    $porcentaje = $this->validar_porcentaje($tipoApoyo, $apoyo_solicitado, $valor_total, $id_solicitud);
                    $salario = $this->validar_viaticos($tipoApoyo, $apoyo_solicitado, $valor_total, $id_solicitud);
      
                    if($conceptos){
                        $resp = ['mensaje'=>"Este concepto ya fue agregado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else if($porcentaje && $tipoApoyo === 'Bec_Pag_M'){
                        $resp = ['mensaje'=>"No puede solicitar un apoyo superior a su modalidad contractual.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else if($salario && $tipoApoyo === 'Bec_Viat'){
                        $resp = ['mensaje'=>"No puede solicitar un apoyo superior para su rango salarial.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        if($beca_incluye){
                            $data = [
                                'incluye_beca' => $incluye_beca,
                            ];
                        }else{
                            $data = [
                                'valor_total' => $valor_total,
                                'apoyo_universidad' => $apoyo_solicitado,
                                'id_concepto' => $tipoApoyo
                            ];
                        }
                        $data['id_solicitud'] = $id_solicitud;
                        $data['id_usuario_registra'] = $id_usuario_registro;
                        $agre_concepto = $this->pages_model->guardar_datos($data, 'becas_concepto');
                        if($agre_concepto == -1){
                            $resp = ['mensaje'=>"Error al guardar la informacion apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }
                        $resp = ['mensaje'=>"Concepto agregado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                    }
                }else{
                    $resp = ['mensaje'=>"No puede agregar esta información, una vez la solicitud este tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    
    public function modificar_concepto_solicitud(){
        if(!$this->Super_estado == true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $estado = $this->becas_model->estado($id_solicitud);
                if($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr'){
                    $id = $this->input->post('id');
                    $beca_incluye = $this->input->post('beca_incluye');
                    $tipoApoyo = $this->input->post('tipo_apoyo');
                    $incluye_beca = $this->input->post('incluye_beca');
                    $valor_total = $this->input->post('valorTotal');
                    $apoyo_solicitado = $this->input->post('apoyoSolicitado');
                    $id_usuario_registro = $_SESSION['persona'];
                    $porcentaje = $this->validar_porcentaje($tipoApoyo, $apoyo_solicitado, $valor_total, $id_solicitud);
                    $salario = $this->validar_viaticos($tipoApoyo, $apoyo_solicitado, $valor_total, $id_solicitud);
                    // $conceptos = $this->becas_model->validar_conceptos($id_solicitud, $tipoApoyo);

                    // if($conceptos){
                    //     $resp = ['mensaje'=>"Este concepto ya fue agregado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    // }
                    if($porcentaje && $tipoApoyo === 'Bec_Pag_M'){
                        $resp = ['mensaje'=>"No puede solicitar un apoyo superior a su modalidad contractual.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else if($salario && $tipoApoyo === 'Bec_Viat'){
                        $resp = ['mensaje'=>"No puede solicitar un apoyo superior para su rango salarial.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        if($beca_incluye){
                            $data = [
                                'incluye_beca' => $incluye_beca,
                            ];
                        }else{
                            $data = [
                                'valor_total' => $valor_total,
                                'apoyo_universidad' => $apoyo_solicitado,
                                'id_concepto' => $tipoApoyo
                            ];
                        }
                        $data['id_usuario_registra'] = $id_usuario_registro;
                        $modi_concepto = $this->pages_model->modificar_datos($data, 'becas_concepto', $id);
                        if($modi_concepto == -1){
                            $resp = ['mensaje'=>"Error al modificar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }
                        $resp = ['mensaje'=>"Concepto modificado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                    }
                }else{
                    $resp = ['mensaje'=>"No puede modificar esta información, una vez la solicitud este tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    // lista
    public function modificar_herramienta_solicitud(){
        if(!$this->Super_estado == true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $estado = $this->becas_model->estado($id_solicitud);
                if(($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                    $id = $this->input->post('id');
                    $id_solicitud = $this->input->post('id_solicitud');
                    $nombre = $this->input->post('nombre_herramienta');
                    $horas = $this->input->post('horas_formacion');
                    $descripcion = $this->input->post('descripcion_herramienta');
                    $id_usuario_registro = $_SESSION['persona'];
    
                    $data = [
                        'nombre' => $nombre,
                        'hora_implementacion' => $horas,
                        'descripcion' => $descripcion,
                        'id_usuario_registra' => $id_usuario_registro
                    ];
                    $modi_herr = $this->pages_model->modificar_datos($data, 'becas_manejo_tech', $id);
                    if($modi_herr == -1){
                        $resp = ['mensaje'=>"Error al modificar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Herramienta modificada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                }else{
                    $resp = ['mensaje'=>"No puede modificar esta información, una vez la solicitud este tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }
    // lista
    public function modificar_experiencia_solicitud(){
        if(!$this->Super_estado == true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $estado = $this->becas_model->estado($id_solicitud);
                if(($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                    $area_g = $this->input->post('area_general_sector_prod');
                    $area_e = $this->input->post('area_especifica_sector_prod');
                    $entidad = $this->input->post('entidad_sector_prod');
                    $id_usuario_registro = $_SESSION['persona'];
                    $id = $this->input->post('id');
                    $year_exp = $this->input->post('year_sector_prod');
    
                    $data = [
                        'area_general' => $area_g,
                        'area_especifica' => $area_e,
                        'entidad' => $entidad,
                        'year_exp' => $year_exp,
                        'id_usuario_registra' => $id_usuario_registro
                    ];
    
                    $modi_exp = $this->pages_model->modificar_datos($data, 'becas_sector_productivo', $id);
                    if($modi_exp == -1){
                        $resp = ['mensaje'=>"Error al modificar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Experiencia modificada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                }else{
                    $resp = ['mensaje'=>"No puede modificar esta información, una vez la solicitud este tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }
    // lista
    public function modificar_intelectual_solicitud(){
        if(!$this->Super_estado == true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $estado = $this->becas_model->estado($id_solicitud);
                if(($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                    $id_producto = $this->input->post('producto_intel');
                    $id = $this->input->post('id');
                    $nombre_prod = $this->input->post('nombre_prod_intel');
                    $id_usuario_registro = $_SESSION['persona'];
                    $entidad_prod = $this->input->post('entidad_prod_intel');
                    $fecha_publi = $this->input->post('fecha_finalizacion');
    
                    $data = [
                        'id_producto' => $id_producto,
                        'nombre_producto' => $nombre_prod,
                        'entidad_publicacion' => $entidad_prod,
                        'fecha_publicacion' => $fecha_publi,
                        'id_usuario_registra' => $id_usuario_registro
                    ];
    
                    $modi_intel = $this->pages_model->modificar_datos($data, 'becas_prod_intelectual', $id);
                    if($modi_intel == -1){
                        $resp = ['mensaje'=>"Error al modificar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Producto modificado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                }else{
                    $resp = ['mensaje'=>"No puede modificar esta información, una vez la solicitud este tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_plan_accion_solicitud(){
        if(!$this->Super_estado == true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $estado = $this->becas_model->estado($id_solicitud);
                if($estado->{'id_estado'} === 'Bec_Form'){
                    $id = $this->input->post('id');
                    $id_usuario_registro = $_SESSION['persona'];
                    $meta = $this->input->post('meta_plan_accion');
    
                    $data = [
                        'meta' => $meta,
                        'id_usuario_registra' => $id_usuario_registro
                    ];
    
                    $modi_plan = $this->pages_model->modificar_datos($data, 'becas_plan_accion', $id);
                    if($modi_plan == -1){
                        $resp = ['mensaje'=>"Error al modificar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Información modificada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                }else{
                    $resp = ['mensaje'=>"No puede modificar esta información, una vez la solicitud este tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_entregable_solicitud(){
        if(!$this->Super_estado == true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $estado = $this->becas_model->estado($id_solicitud);
                if(($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                    $id = $this->input->post('id');
                    $id_usuario_registro = $_SESSION['persona'];
                    $producto = $this->input->post('producto_entregable');
                    $entregable = $this->input->post('compromiso_entregable');
    
                    $data = [
                        'producto' => $producto,
                        'entregable' => $entregable,
                        'id_usuario_registro' => $id_usuario_registro
                    ];
    
                    $modi_entrega = $this->pages_model->modificar_datos($data, 'becas_compromiso_entregable', $id);
                    if($modi_entrega == -1){
                        $resp = ['mensaje'=>"Error al modificar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Entregable modificado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                }else{
                    $resp = ['mensaje'=>"No puede modificar esta información, una vez la solicitud este tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_actividad_solicitud(){
        if(!$this->Super_estado == true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $estado = $this->becas_model->estado($id_solicitud);
                if($estado->{'id_estado'} === 'Bec_Form'){
                    $id = $this->input->post('id');
                    $actividad = $this->input->post('actividad_gestion_plan_accion');
                    $recurso = $this->input->post('recurso_gestion_plan_accion');
                    $fecha_inicio = $this->input->post('fecha_inicio_gestion_plan_accion');
                    $fecha_fin = $this->input->post('fecha_finalizacion_gestion_plan_accion');
                    $id_usuario_registro = $_SESSION['persona'];
                    
                    $data = [
                        'actividad' => $actividad,
                        'recurso' => $recurso,
                        'fecha_inicio' => $fecha_inicio,
                        'fecha_final' => $fecha_fin,
                        'id_usuario_registra' => $id_usuario_registro
                    ];
    
                    $modi_act = $this->pages_model->modificar_datos($data, 'becas_plan_accion_gestion', $id);
                    if($modi_act == -1){
                        $resp = ['mensaje'=>"Error al modificar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Actividad modificada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                }else{
                    $resp = ['mensaje'=>"No puede modificar esta información, una vez la solicitud este tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }
    
    public function modificar_compromiso_solicitud(){
        if(!$this->Super_estado == true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $estado = $this->becas_model->estado($id_solicitud);
                if(($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                    $fecha_periodo = $this->input->post('fecha_periodo');
                    $id = $this->input->post('id');
                    $compromiso = $this->input->post('compromiso_descripcion');
                    $year = $this->input->post('year_compromiso');
                    $id_usuario_registro = $_SESSION['persona'];
                    
                    $data = [
                        'compromiso' => $compromiso,
                        'periodo' => $fecha_periodo,
                        'year' => $year,
                        'id_usuario_registra' => $id_usuario_registro
                    ];
    
                    $modi_comp = $this->pages_model->modificar_datos($data, 'becas_compromisos', $id);
                    if($modi_comp == -1){
                        $resp = ['mensaje'=>"Error al modificar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Compromiso modificada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                }else{
                    $resp = ['mensaje'=>"No puede modificar esta información, una vez la solicitud este tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }
    
    // lista
    public function agregar_herramientas_solicitud(){
        if(!$this->Super_estado == true) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id');
                $estado = $this->becas_model->estado($id_solicitud);
                if(($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                    $nombre = $this->input->post('nombre_herramienta');
                    $descripcion = $this->input->post('descripcion_herramienta');
                    $horas = $this->input->post('horas_formacion');
                    $id_usuario_registro = $_SESSION['persona'];
    
                    $data = [
                        'id_solicitud' => $id_solicitud,
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'hora_implementacion' => $horas,
                        'id_usuario_registra' => $id_usuario_registro
                    ];
    
                    $add_herramienta = $this->pages_model->guardar_datos($data, 'becas_manejo_tech');
                    if($add_herramienta == -1){
                        $resp = ['mensaje'=>"Error al guardar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Herramienta agregada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                }else{
                    $resp = ['mensaje'=>"No puede agregar más herramientas a la solicitud una vez tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }
            }
        }
        echo json_encode($resp);
    }

    //AGREGAR EXPERIENCIA A LA SOLICITUD
    public function agregar_experiencia_solicitud(){
        if(!$this->Super_estado == true) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id');
                $estado = $this->becas_model->estado($id_solicitud);
                if(($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                    $area_g = $this->input->post('area_general_sector_prod');
                    $area_e = $this->input->post('area_especifica_sector_prod');
                    $entidad = $this->input->post('entidad_sector_prod');
                    $id_usuario_registro = $_SESSION['persona'];
                    $year_exp = $this->input->post('year_sector_prod');
                    $sw = true; 
                    $str = $this->verificar_campos_string(['Área general' => $area_g, 'Área especifica' => $area_e, 'Entidad' => $entidad]);
                    $num = $this->verificar_campos_numericos(['Años de experiencia' => $year_exp]);

                    if(is_array($str)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                        $sw = false; 
                    }else if(is_array($num)){
                        $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                        $sw = false;
                    }
                    if($sw){
                        $data = [
                            'id_solicitud' => $id_solicitud,
                            'area_general' => $area_g,
                            'area_especifica' => $area_e,
                            'entidad' => $entidad,
                            'year_exp' => $year_exp,
                            'id_usuario_registra' => $id_usuario_registro
                        ];
                        $add_exp = $this->pages_model->guardar_datos($data, 'becas_sector_productivo');
                        if($add_exp == -1){
                            $resp = ['mensaje'=>"Error al guardar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }
                        $resp = ['mensaje'=>"Experiencia agregada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                    }
                }else{
                    $resp = ['mensaje'=>"No puede agregar más experiencia a la solicitud una vez tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }
            }
        }
        echo json_encode($resp);
    }
    // lista
    public function agregar_intelectual_solicitud(){
        if(!$this->Super_estado == true) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id');
                $estado = $this->becas_model->estado($id_solicitud);
                if(($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                    $id_producto = $this->input->post('producto_intel');
                    $nombre_prod = $this->input->post('nombre_prod_intel');
                    $id_usuario_registro = $_SESSION['persona'];
                    $entidad_prod = $this->input->post('entidad_prod_intel');
                    $fecha_publi = $this->input->post('fecha_finalizacion');

                    $data = [
                        'id_solicitud' => $id_solicitud,
                        'id_producto' => $id_producto,
                        'nombre_producto' => $nombre_prod,
                        'entidad_publicacion' => $entidad_prod,
                        'fecha_publicacion' => $fecha_publi,
                        'id_usuario_registra' => $id_usuario_registro
                    ];

                    $add_intel= $this->pages_model->guardar_datos($data, 'becas_prod_intelectual');
                    if($add_intel == -1){
                        $resp = ['mensaje'=>"Error al guardar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Producto agregado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                }else{
                    $resp = ['mensaje'=>"No puede agregar más productos a la solicitud una vez tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }
            }
        }
        echo json_encode($resp);
    }

    public function agregar_plan_accion_solicitud(){
        if(!$this->Super_estado == true) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id');
                $estado = $this->becas_model->estado($id_solicitud);
                if($estado->{'id_estado'} === 'Bec_Form'){
                    $meta = $this->input->post('meta_plan_accion');
                    $id_usuario_registro = $_SESSION['persona'];
                    $actividades = $this->input->post('actividades');
                    $data_actividades = array();
                    if(!$actividades){
                        $resp = ['mensaje'=>"Por favor agrege las actividades",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        $data = [
                            'id_solicitud' => $id_solicitud,
                            'meta' => $meta,
                            'id_usuario_registra' => $id_usuario_registro
                        ];
                        $add_plan = $this->pages_model->guardar_datos($data, 'becas_plan_accion');
                        if($add_plan == -1){
                            $resp = ['mensaje'=>"Error al guardar la solicitud apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }else{
                            $id_plan = $this->becas_model->traer_ultimo_plan_accion($id_usuario_registro);
    
                            foreach ($actividades as $key) {
                                array_push($data_actividades, array(
                                    'id_plan' => $id_plan->{'id'},
                                    'actividad' => $key['actividad_gestion_plan_accion'],
                                    'recurso' => $key['recurso_gestion_plan_accion'],
                                    'fecha_inicio' => $key['fecha_inicio_gestion_plan_accion'],
                                    'fecha_final' => $key['fecha_finalizacion_gestion_plan_accion'],
                                    'id_usuario_registra' => $_SESSION['persona']
                                ));
                            }
                            $add_actividades = $this->pages_model->guardar_datos($data_actividades, 'becas_plan_accion_gestion', 2);
                            if($add_actividades == -1){
                                $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                            }
                        $resp = ['mensaje'=>"Información almacenada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"No puede agregar más metas a la solicitud una vez tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }
            }
        }
        echo json_encode($resp);
    }
    // lista
    public function agregar_actividad_solicitud(){
        if(!$this->Super_estado == true){
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $id = $this->input->post('id');
                $estado = $this->becas_model->estado($id_solicitud);
                if($estado->{'id_estado'} === 'Bec_Form'){
                    $id_usuario_registro = $_SESSION['persona'];
                    $actividad = $this->input->post('actividad_gestion_plan_accion');
                    $recurso = $this->input->post('recurso_gestion_plan_accion');
                    $fecha_inicio = $this->input->post('fecha_inicio_gestion_plan_accion');
                    $fecha_fin = $this->input->post('fecha_finalizacion_gestion_plan_accion');
    
                    $data = [
                        'id_plan' => $id,
                        'actividad' => $actividad,
                        'recurso' => $recurso,
                        'fecha_inicio' => $fecha_inicio,
                        'fecha_final' => $fecha_fin,
                        'id_usuario_registra' => $id_usuario_registro
                    ];
    
                    $add_act = $this->pages_model->guardar_datos($data, 'becas_plan_accion_gestion');
                    if($add_act == -1){
                        $resp = ['mensaje'=>"Error al guardar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Actividad agregada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                }else{
                    $resp = ['mensaje'=>"No puede agregar más actividades a la solicitud una vez tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"];    
                }
            }
        }
        echo json_encode($resp);
    }
    // lista
    public function agregar_entregable_solicitud(){
        if(!$this->Super_estado == true) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id');
                $estado = $this->becas_model->estado($id_solicitud);
                if(($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                    $producto = $this->input->post('producto_entregable');
                    $entregable = $this->input->post('compromiso_entregable');
                    $id_usuario_registro = $_SESSION['persona'];
                    $compromisos = $this->input->post('compromisos');
                    $data_compromisos = array();
    
                    if(!$compromisos){
                        $resp = ['mensaje'=>"Por favor agrege los compromisos",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        $data = [
                            'id_solicitud' => $id_solicitud,
                            'producto' => $producto,
                            'entregable' => $entregable,
                            'id_usuario_registro' => $id_usuario_registro
                        ];
                        $add_entrega = $this->pages_model->guardar_datos($data, 'becas_compromiso_entregable');
                        if($add_entrega == -1){
                            $resp = ['mensaje'=>"Error al guardar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }else{
                            $id_entrega = $this->becas_model->traer_ultima_entrega($id_usuario_registro);
                            foreach ($compromisos as $key) {
                                array_push($data_compromisos, array(
                                    'id_entregable' => $id_entrega->{'id'},
                                    'year' => $key['year_compromiso'],
                                    'compromiso' => $key['compromiso_descripcion'],
                                    'periodo' => $key['fecha_periodo'],
                                    'id_usuario_registra' => $_SESSION['persona']
                                ));
                            }
                            $add_compromiso = $this->pages_model->guardar_datos($data_compromisos, 'becas_compromisos', 2);
                            if($add_compromiso == -1){
                                $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                            }
                            $resp = ['mensaje'=>"Entregable agregado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"No puede agregar más entregables a la solicitud una vez tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }
            }
        }
        echo json_encode($resp);
    }
    // lista
    public function agregar_compromiso_solicitud(){
        if(!$this->Super_estado == true) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id_solicitud = $this->input->post('id_solicitud');
                $estado = $this->becas_model->estado($id_solicitud);
                if(($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr' ) && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                    $fecha_periodo = $this->input->post('fecha_periodo');
                    $id_entrega = $this->input->post('id');
                    $compromiso = $this->input->post('compromiso_descripcion');
                    $year = $this->input->post('year_compromiso');
                    $id_usuario_registro = $_SESSION['persona'];
                    $data = [
                        'id_entregable' => $id_entrega,
                        'compromiso' => $compromiso,
                        'year' => $year, 
                        'periodo' => $fecha_periodo,
                        'id_usuario_registra' => $id_usuario_registro
                    ];
                    $add_comp = $this->pages_model->guardar_datos($data, 'becas_compromisos');
                    if($add_comp == -1){
                        $resp = ['mensaje'=>"Error al guardar la información apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                    $resp = ['mensaje'=>"Compromiso agregado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
                }else{
                    $resp = ['mensaje'=>"No puede agregar más compromisos a la solicitud una vez tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }
            }
        }
        echo json_encode($resp);
    }

    //REFACTORIZANDO FUNCION GUARDAR INFORMACION PRINCIPAL
    // Lista
    public function agregar_solicitud_validacion(){
      if (!$this->Super_estado) {
         $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
      }else{
         $sw = true;
         $id =$this->input->post('id');
         $admitido_al_programa = $this->input->post('admitido_al_programa');
         $fecha_inicio = $this->input->post('fecha_inicio');
         $fecha_termina = $this->input->post('fecha_termina');
         $id_year = $this->input->post('id_year');
         $id_nivel_formacion = $this->input->post('id_nivel_formacion');
         $id_semestre = $this->input->post('id_semestre');
         $institucion = $this->input->post('institucion');
         $pais_institucion = $this->input->post('pais_insti');
         $ciudad_institucion = $this->input->post('ciudad_insti');
         $linea_investigacion = $this->input->post('linea_investigacion');
         $pin = $this->input->post('pin');
         $programa = $this->input->post('programa');
         $ranking = $this->input->post('ranking');
         $tipo_duracion_programa = $this->input->post('tipo_duracion_programa');
         $id_usuario_registro = $_SESSION['persona'];
         $id_comision_estudio = $this->input->post('id_comision_estudio');
         $id_beca = $this->input->post('id_beca');
         $num = $this->verificar_campos_numericos(['ranking' => $ranking]);
         $str = $this->verificar_campos_string(['Admitido' => $admitido_al_programa, 'Programa' => $programa, 'Nivel de formación' => $id_nivel_formacion, 'Tipo de duracion' => $tipo_duracion_programa,'Institucion' => $institucion, 'Pais de Institucion' => $pais_institucion, 'Ciudad de Institucion' => $ciudad_institucion,  'Duracion' => $id_semestre,  'Semestre o Año Actual' => $id_year, 'Fecha de inicio' => $fecha_inicio, 'Fecha de terminación' => $fecha_termina, 'Linea de Investigacion' => $linea_investigacion, 'Pin a fortalecer' => $pin, 'Comisión de estudio' => $id_comision_estudio]);
         $fecha_i_valida = $this->validateDate($fecha_inicio);
         $fecha_f_valida = $this->validateDate($fecha_termina);

         if(is_array($str)){
            $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
            $sw = false; 
         }else if(is_array($num)){
            $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
            $sw = false; 
         }else if(!$fecha_i_valida || !$fecha_f_valida){
            $resp = ['mensaje'=> "Por favor seleccione fechas validas y superior a la fecha actual.", 'tipo'=>"info", 'titulo'=> "Oops."];
            $sw = false; 
         }else if($fecha_termina <= $fecha_inicio){
            $resp = ['mensaje'=> "La Fecha de Terminación debe ser superior a la Fecha de Inicio.", 'tipo'=>"info", 'titulo'=> "Oops."];
            $sw = false; 
         }
         // else if($id_year < $id_semestre){
         //     $resp = ['mensaje'=> "El Semestre o Año actual no debe ser a la Duracion del Programa.", 'tipo'=>"info", 'titulo'=> "Oops."];
         //     $sw = false; 
         // }
         if ($sw){
            $estado = $this->becas_model->estado($id);
            if(($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
               $data = [
                  'id_usuario_registro' => $id_usuario_registro,
                  'institucion' => $institucion,
                  'pais_institucion' => $pais_institucion,
                  'ciudad_institucion' => $ciudad_institucion,
                  'programa' => $programa,
                  'ranking' => $ranking,
                  'tipo_duracion_programa' => $tipo_duracion_programa,
                  'id_duracion' => $id_year,
                  'admitido_al_programa' => $admitido_al_programa,
                  'linea_investigacion' => $linea_investigacion,
                  'pin' => $pin,
                  'fecha_inicio' => $fecha_inicio,
                  'fecha_termina' => $fecha_termina,
                  'id_semestre' => $id_semestre,
                  'id_nivel_formacion' => $id_nivel_formacion,
                  'id_comision' => $id_comision_estudio,
                  'id_beca' => $id_beca ? $id_beca : NULL
               ];
               $modi_soli = $this->pages_model->modificar_datos($data, 'becas_solicitudes', $id);
               if($modi_soli == -1){
                  $resp = ['mensaje'=>"Error al guardar la informacion de solicitud apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
               }
               $resp = ['mensaje'=>"Información agregada con exito.",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
               $solicitud = $this->becas_model->consulta_solicitud_id($id);
               $resp['solicitud'] = $solicitud;
            }else{
               $resp = ['mensaje'=>"No puede modificar esta información, una vez la solicitud este tramitada.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
            }
         }
        }
        echo json_encode($resp);
    }

    public function agregar_conceptos_validacion(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $tipo_apoyo = $this->input->post('tipo_apoyo');
            $apoyoSolicitado = $this->input->post('apoyoSolicitado');
            $valorTotal = $this->input->post('valorTotal');
            $incluye_beca = $this->input->post('incluye_beca');
            $beca_incluye = $this->input->post('beca_incluye');    
            $sw = true; 
    
            if(!$beca_incluye){
                $str = $this->verificar_campos_string(['Tipo de Apoyo' => $tipo_apoyo]);
                $num = $this->verificar_campos_numericos(['Valor Total' => $valorTotal, 'Apoyo Solicitado' => $apoyoSolicitado]);
            }else{
                $str = $this->verificar_campos_string(['Incluye Beca' => $incluye_beca]);
                $num = '';
            }
    
            if(is_array($str)){
                $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false; 
            }else if(is_array($num)){ 
                $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false;   
            }
    
            if($apoyoSolicitado > $valorTotal){
                $resp = ['mensaje'=> "El Valor Solicitado no debe ser superior al Valor Total.", 'tipo'=>"info", 'titulo'=> "Oops."];
                $sw = false; 
            }
    
            if ($sw) $resp = ['mensaje'=>"Concepto agregado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];

        }
        echo json_encode($resp);
    }
    // lista
    public function agregar_herramientas_validacion(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $nombre_herramienta = $this->input->post('nombre_herramienta');
            $descripcion = $this->input->post('descripcion_herramienta');
            $horas = $this->input->post('horas_formacion');
    
            $sw = true; 
            $str = $this->verificar_campos_string(['Nombre' => $nombre_herramienta, 'Descripcion' => $descripcion]);
            $num = $this->verificar_campos_numericos(['Horas de formación' => $horas]);
    
            if(is_array($str)){
                $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false; 
            }else if(is_array($num)){ 
                $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false;   
            }
            if ($sw) $resp = ['mensaje'=>"Herramienta agregada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
        }
        echo json_encode($resp);
    }
    // lista
    public function agregar_prod_intel_validacion(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $producto = $this->input->post('producto_intel');
            $nombre_producto = $this->input->post('nombre_prod_intel');
            $entidad = $this->input->post('entidad_prod_intel');
            $fecha_finalizacion = $this->input->post('fecha_finalizacion');
    
            $sw = true; 
            $str = $this->verificar_campos_string(['Producto' => $producto, 'Nombre' => $nombre_producto, 'Entidad de publicación' => $entidad, 'Fecha de finalizacion' => $fecha_finalizacion]);
            $fecha_f = $this->validateDate($fecha_finalizacion);
    
            if(is_array($str)){
                $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false; 
            }else if(!$fecha_f){
                $resp = ['mensaje'=> "Por favor seleccione fechas validas", 'tipo'=>"info", 'titulo'=> "Oops."];
            }
            if ($sw) $resp = ['mensaje'=>"Producto agregado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
        }
        echo json_encode($resp);
    }

    public function agregar_sector_prod_validacion(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $area_general = $this->input->post('area_general_sector_prod');
            $area_especifica = $this->input->post('area_especifica_sector_prod');
            $entidad = $this->input->post('entidad_sector_prod');
            $year_sector_prod = $this->input->post('year_sector_prod');
    
            $sw = true; 
            $str = $this->verificar_campos_string(['Área general' => $area_general, 'Área especifica' => $area_especifica, 'Entidad' => $entidad]);
            $num = $this->verificar_campos_numericos(['Años de experiencia' => $year_sector_prod]);
            
    
            if(is_array($str)){
                $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false; 
            }else if(is_array($num)){
                $resp = ['mensaje'=>"Debe diligenciar el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false;
            }
            if ($sw) $resp = ['mensaje'=>"Experiencia agregada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
        }
        echo json_encode($resp);
    }

    public function agregar_plan_accion_validacion(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $meta = $this->input->post('meta_plan_accion');
            $actividades = $this->input->post('actividades');
            $str = $this->verificar_campos_string(['Meta del plan' => $meta]);
            if(is_array($str)) $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
            else if(!$actividades) $resp = ['mensaje'=>"Por favor agrege las actividades.", 'tipo'=>"info", 'titulo'=>"Oops..!"];
            else  $resp = ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
        }
        echo json_encode($resp);
    }

    public function agregar_plan_gestion_accion_validacion(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $actividad = $this->input->post('actividad_gestion_plan_accion');
            $recurso = $this->input->post('recurso_gestion_plan_accion');
            $fecha_inicio = $this->input->post('fecha_inicio_gestion_plan_accion');
            $fecha_fin = $this->input->post('fecha_finalizacion_gestion_plan_accion');
    
            $sw = true; 
            $str = $this->verificar_campos_string(['Actividad' => $actividad, 'Recurso' => $recurso]);
    
            $fecha_i = $this->validateDate($fecha_inicio);
            $fecha_f = $this->validateDate($fecha_fin);
    
            if(is_array($str)){
                $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false; 
            }else if(!$fecha_i || !$fecha_f){
                $resp = ['mensaje'=> "Por favor seleccione fechas validas", 'tipo'=>"info", 'titulo'=> "Oops."];
            }
            if ($sw) $resp = ['mensaje'=>"Actividad agregada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];    
        }
        echo json_encode($resp);
    }
    // lista
    public function agregar_compromisos_validacion(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $compromiso = $this->input->post('compromiso_descripcion');
            $fecha_periodo = $this->input->post('fecha_periodo');
            $year_compromiso = $this->input->post('year_compromiso');
    
            $sw = true; 
            $str = $this->verificar_campos_string(['Compromiso' => $compromiso, 'Periodo' => $fecha_periodo]);
            $num = $this->verificar_campos_numericos(['Año' => $year_compromiso]);
    
            if(is_array($str)){
                $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false; 
            }else if(is_array($num)){
                $resp = ['mensaje'=>"Debe diligenciar un valor valido el campo {$num['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
                $sw = false;
            }
            if ($sw) $resp = ['mensaje'=>"Compromiso agregado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
        }
        echo json_encode($resp);
    }
    // lista
    public function agregar_entregable_validacion(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $producto = $this->input->post('producto_entregable');
            $entregable = $this->input->post('compromiso_entregable');
            $compromisos = $this->input->post('compromisos');
    
            $str = $this->verificar_campos_string(['Producto' => $producto, 'Entregable' => $entregable]);
            
            if(is_array($str)){
                $resp = ['mensaje'=>"Debe diligenciar el campo {$str['field']}", 'tipo'=>"info", 'titulo'=>"Oops..!"];
            }else if(!$compromisos){
                $resp = ['mensaje'=>"Por favor agrege los compromisos.", 'tipo'=>"info", 'titulo'=>"Oops..!"];
            } else {
                $resp = ['mensaje'=>"Entregable agregado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
            }
        }
        echo json_encode($resp);
    }
    // falta nuevos estados
    public function listar_solicitudes_becas(){
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $id = $this->input->post('id');
            $estado = $this->input->post('filtro_estado');
            $admitido = $this->input->post('filtro_admitido_al_programa');
            $nivel_formacion = $this->input->post('filtro_id_nivel_formacion');
            $fecha_i = $this->input->post('filtro_fecha_inicio');
            $fecha_f = $this->input->post('filtro_fecha_termina');
            $tipo = $this->input->post('filtro_tipo');
            $departamento = $this->input->post('filtro_id_departamento');
            $programa = $this->input->post('filtro_id_programa');
            $vinculacion = $this->input->post('filtro_id_vinculacion');
            $fil_persona = $this->input->post('filtro_persona');
            $admin = $_SESSION["perfil"] === "Per_Admin" ? true : false;
            $adm_bec = $_SESSION["perfil"] === "Per_Adm_Bec" ? true : false;
            $persona = $_SESSION["persona"];
            $solicitudes = $this->Super_estado ? $this->becas_model->listar_solicitudes_becas($estado, $admitido, $nivel_formacion, $fecha_i, $fecha_f, $departamento, $programa, $vinculacion, $tipo, $fil_persona, $id) : array();

            $ver_formulacion = '<span  style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" ><span >ver</span></span>';
            $ver_enviado = '<span  style="background-color: #232f85; color: white; width: 100%" class="pointer form-control ver" ><span >ver</span></span>';
            $ver_revision = '<span  style="background-color: #777;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
            $ver_tramitado = '<span  style="background-color: #f0ad4e;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
            $ver_aceptado = '<span  style="background-color: #428bca;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
            $ver_cancelada = '<span  style="background-color: #d9534f;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';
            $ver_finilizada = '<span style="background-color: #5cb85c;color: white;width: 100%;" class="pointer form-control ver"><span >ver</span></span>';

            $btn_formulacion = '<span title="En Formulación" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="pointer fa fa-pencil-square-o btn btn-default formulacion"></span>';
            $btn_enviar = '<span title="Enviar" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;" class="pointer fa fa-send btn btn-default enviar"></span>';
            $btn_revision = '<span title="Enviar a Revision" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="pointer fa fa-retweet btn btn-default revision"></span>';            
            $btn_aprobar = '<span title="Aprobar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;"class="pointer fa fa-check btn btn-default aprobar"></span>';
            $btn_finalizar = '<span title="Finalizar" data-toggle="popover" data-trigger="hover" style="color: #00cc00;"class="pointer fa fa-check btn btn-default finalizar"></span>';
            $btn_negar = '<span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;" class="pointer fa fa-ban btn btn-default negar"></span>';
            $btn_cancelar = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default cancelar" style="color:#d9534f"></span>';
            $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
            $btn_abierta = '<span title="En proceso" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half" style="color:#428bca"></span>';
            $btn_correcion = '<span title="Enviar a Corrección" data-toggle="popover" data-trigger="hover" style="color: #AA66CC;" class="pointer fa fa-wrench btn btn-default correcion"></span>';
            $btn_continuar = '<span title="Continuar Proceso" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;" class="pointer fa fa-send btn btn-default continuar"></span>';

            foreach ($solicitudes as $item){
                $solicitante = $item['id_usuario_registro'];
                $tieneE = $item['tieneE'];
                $tieneP = $item['tieneP'];
                $id = $item['id'];
                $item['ver'] = $ver_formulacion;
                $item['acciones'] = $btn_cerrada;

                switch ($item['id_tipo']) {
                    case 'Soli_Tip_Ini':
                        if($item['id_estado_solicitud'] === 'Bec_Form'){
                            $item['acciones'] = ($persona === $solicitante || $admin ) ? "$btn_formulacion $btn_enviar $btn_cancelar" : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Corr'){
                            $item['acciones'] = ($persona === $solicitante || $admin ) ? "$btn_formulacion $btn_continuar" : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Envi'){
                            $item['ver'] = $ver_enviado;
                            $item['acciones'] = ($admin || ($tieneE && $tieneP)) ? "$btn_revision $btn_correcion $btn_negar" : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Revi'){ 
                            $item['ver'] = $ver_revision;
                            $item['acciones'] = ((($admin || ($tieneE && $tieneP)) && $item['persona_d'] < 1 )) ? $this->config_btn('gestionar') . " $btn_correcion" : $btn_abierta;
                        }else if($item['id_estado_solicitud'] === 'Bec_Gest'){
                            $item['ver'] = $ver_revision;
                            $item['acciones'] = ($admin || ($tieneE && $tieneP) && $item['persona_d'] < 1 ) ? $this->config_btn('gestionar_inve') . " $btn_correcion" : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Gest_Inve'){
                            $item['ver'] = $ver_revision;
                            $item['acciones'] = ($admin || ($tieneE && $tieneP) && $item['persona_d'] < 1) ? $this->config_btn('gestionar_acad') . " $btn_correcion" : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Tram'){
                            $item['ver'] = $ver_tramitado;
                            $item['acciones'] = ($admin || ($tieneE && $tieneP) && $item['persona_d'] < 1) ? $this->config_btn('gestionar_secr') . " $btn_correcion" : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Acep'){
                            $item['ver'] = $ver_aceptado;
                            $item['acciones'] = ($admin || ($tieneE && $tieneP) )? "$btn_aprobar $btn_negar" : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Apro'){
                            $item['ver'] = $ver_finilizada;
                            $item['acciones'] = ($admin || ($tieneE && $tieneP)) ?  "$btn_finalizar" : $item['acciones'];
                        }else if($item['id_estado_solicitud'] === 'Bec_Fina'){
                            $item['ver'] = $ver_finilizada;
                        }else if($item['id_estado_solicitud'] === 'Bec_Rech' || $item['id_estado_solicitud'] === 'Bec_Canc'){
                            $item['ver'] = $ver_cancelada;
                            $item['acciones'] =  "$btn_cerrada";
                        }        
                        break;
                    case 'Soli_Tip_Ren':
                        if($item['id_estado_solicitud'] === 'Bec_Form'){
                            $item['acciones'] = ($persona === $solicitante || $admin ) ? "$btn_formulacion $btn_enviar" : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Corr'){
                            $item['acciones'] = ($persona === $solicitante || $admin ) ? "$btn_formulacion $btn_continuar" : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Envi'){
                            $item['ver'] = $ver_enviado;
                            $item['acciones'] = ($admin  || ($tieneE && $tieneP))? "$btn_revision $btn_correcion $btn_negar" : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Revi'){ 
                            $item['ver'] = $ver_revision;
                            $item['acciones'] = ((($admin || ($tieneE && $tieneP)) && $item['persona_d'] < 1 ))? $this->config_btn('gestionar') . " $btn_correcion": $btn_abierta;
                        }else if($item['id_estado_solicitud'] === 'Bec_Gest'){
                            $item['ver'] = $ver_revision;
                            $item['acciones'] = ($admin || ($tieneE && $tieneP) && $item['persona_d'] < 1 )? $this->config_btn('gestionar_inve') : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Gest_Inve'){
                            $item['ver'] = $ver_revision;
                            $item['acciones'] = ($admin || ($tieneE && $tieneP) && $item['persona_d'] < 1)? $this->config_btn('gestionar_acad') : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Tram'){
                            $item['ver'] = $ver_tramitado;
                            $item['acciones'] = ($admin || ($tieneE && $tieneP) && $item['persona_d'] < 1)? $this->config_btn('gestionar_secr') : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Acep'){
                            $item['ver'] = $ver_aceptado;
                            $item['acciones'] = ($admin || ($tieneE && $tieneP) )? "$btn_aprobar $btn_negar" : "$btn_abierta";
                        }else if($item['id_estado_solicitud'] === 'Bec_Apro' || $item['id_estado_solicitud'] === 'Bec_Fina'){
                            $item['ver'] = $ver_finilizada;
                        }else if($item['id_estado_solicitud'] === 'Bec_Rech' || $item['id_estado_solicitud'] === 'Bec_Canc'){
                            $item['ver'] = $ver_cancelada;
                            $item['acciones'] =  "$btn_cerrada";
                        }   
                        break;
                }
                array_push($resp, $item);
            }
        }
        echo json_encode($resp);
    }
    // lista
    public function config_btn($clase){
        return "<span title='Gestionar' data-toggle='popover' data-trigger='hover' style='color: #000;'class='pointer fa fa-cogs btn btn-default $clase'></span>";
    }
    // lista
    public function cambiarEstado(){
        if(!$this->Super_estado){
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		}else{
			$id_solicitud = $this->input->post("id");
            $estado = $this->input->post("estado");
            $tipo_fin = $this->input->post("tipo_fin");
            $id_usuario_registra = $_SESSION["persona"];
            $observaciones = $this->input->post('observaciones');
            $valido = $this->validar_estado($id_solicitud, $estado);
            $revisiones_minimas = $this->becas_model->traer_minimo_revisiones();
            
            if($valido){
                $data = [
                    'id_solicitud' => $id_solicitud,
                    'id_estado' => $estado,
                    'id_usuario_registra' => $id_usuario_registra,
                    'observacion' => $observaciones ? $observaciones : NULL,
                    'id_tipo_fin' => $tipo_fin ? $tipo_fin : NULL
                ];

                $data_solicitud = [
                    'id_estado_solicitud' => $estado,
                    'observaciones' => $observaciones ? $observaciones : NULL
                ];

                $add = $this->pages_model->guardar_datos($data, 'becas_estado');
                $n_revisiones = $this->becas_model->revisiones($id_solicitud);
                if($add == 1){
                    if($estado !== 'Bec_Vis_Buen') $add_sol = $this->pages_model->modificar_datos($data_solicitud, 'becas_solicitudes', $id_solicitud);
                    else if(($estado === 'Bec_Vis_Buen') && ($revisiones_minimas == $n_revisiones)) {
                        $add_sol = $this->pages_model->modificar_datos(['id_estado_solicitud' => 'Bec_Gest'], 'becas_solicitudes', $id_solicitud);
                        $data['id_estado'] = 'Bec_Gest';
                        $add_estado = $this->pages_model->guardar_datos($data, 'becas_estado');
                    }
                    
                
                    if($estado === 'Bec_Fina'){
                        //Colocando Estado Finalizado a las Renovaciones Asociadas a la Inical finalizada.
                        $batch = array();
                        $ids_renovaciones = $this->becas_model->traer_ids_renovaciones($id_solicitud);
                        if($ids_renovaciones){
                            foreach ($ids_renovaciones as $id_ren) {
                                array_push($batch, [
                                    'id_solicitud' => $id_ren['id'],
                                    'id_estado' => $estado,
                                    'id_usuario_registra' => $id_usuario_registra,
                                    'id_tipo_fin' => $tipo_fin
                                ]);
                            }
                            $add_estados = $this->pages_model->guardar_datos($batch, 'becas_estado', 2);
                        }
                        $ren = $this->becas_model->finalizar_solicitudes($data_solicitud, $id_solicitud);
                    }
                }

                if ($add == -1){
                    $resp = ['mensaje' => 'Error al modificar la información, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
                }else{
                    $resp = ['mensaje' => "Proceso finalizado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                }
            }else{
                $resp = ['mensaje' => "Este proceso ya fue gestionado.", 'tipo' => "info", 'titulo' => "Oops.!"];
            }
		} 
		echo json_encode($resp);
    }
    // lista
    public function detalle_concepto(){
        
        $resp = [];

        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $acciones = "<span title='Eliminar' style='color: #DE4D4D;' data-toggle='popover' data-trigger='hover' class='fa fa-trash-o pointer btn btn-default eliminar_concepto'></span> <span style='color: #2E79E5;' title='Editar' data-toggle='popover' data-trigger='hover' class='fa fa-wrench pointer btn btn-default modificar_concepto'></span>";
            $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec" ? true : false;
            $id = $this->input->post('id_solicitud');
            $concepto = $this->becas_model->listar_conceptos($id);
            $estado = $this->becas_model->estado($id);
            foreach( $concepto as $item ){
                ($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr')? $item['acciones'] = $acciones : $item['acciones'] = $btn_cerrada;
                array_push($resp, $item);
            }
        }
        echo json_encode($resp);
    }
    // lista
    public function detalle_herramientas(){
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $acciones = "<span title='Eliminar' style='color: #DE4D4D;' data-toggle='popover' data-trigger='hover' class='fa fa-trash-o pointer btn btn-default eliminar_herramienta'></span> <span style='color: #2E79E5;' title='Editar' data-toggle='popover' data-trigger='hover' class='fa fa-wrench pointer btn btn-default modificar_herramienta_sol'></span>";
            $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec" ? true : false;
            $id = $this->input->post('id_solicitud');
            $herramientas = $this->becas_model->listar_herramientas($id);
            $estado = $this->becas_model->estado($id);
            
            foreach( $herramientas as $item ){
                ($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') ? $item['acciones'] = $acciones : $item['acciones'] = $btn_cerrada;
                array_push($resp, $item);
            }
        }
        echo json_encode($resp);
    }
    // lista
    public function detalle_produccion_intelectual(){
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $acciones = "<span title='Eliminar' style='color: #DE4D4D;' data-toggle='popover' data-trigger='hover' class='fa fa-trash-o pointer btn btn-default eliminar_intelectual'></span> <span style='color: #2E79E5;' title='Editar' data-toggle='popover' data-trigger='hover' class='fa fa-wrench pointer btn btn-default modificar_intelectual_sol'></span>";
            $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec" ? true : false;
            $id = $this->input->post('id_solicitud');
            $herramientas = $this->becas_model->listar_produccion_intelectual($id);
            $estado = $this->becas_model->estado($id);
            
            foreach( $herramientas as $item ){
                ($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') ? $item['acciones'] = $acciones : $item['acciones'] = $btn_cerrada;
                array_push($resp, $item);
            }
        }
        echo json_encode($resp);
    }

    public function detalle_experiencia_sector(){
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $acciones = "<span title='Eliminar' style='color: #DE4D4D;' data-toggle='popover' data-trigger='hover' class='fa fa-trash-o pointer btn btn-default eliminar_exp'></span> <span style='color: #2E79E5;' title='Editar' data-toggle='popover' data-trigger='hover' class='fa fa-wrench pointer btn btn-default modificar_exp_sol'></span>";
            $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';

            $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec" ? true : false;
            $id = $this->input->post('id_solicitud');
            $herramientas = $this->becas_model->listar_experiencia_sector($id);
            $estado = $this->becas_model->estado($id);
            
            foreach($herramientas as $item ){
                ($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') ? $item['acciones'] = $acciones : $item['acciones'] = $btn_cerrada;
                array_push($resp, $item);
            }
        }
        echo json_encode($resp);
    }

    public function detalle_plan_accion(){
        $resp = [];

        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $acciones = "<span title='Eliminar' style='color: #DE4D4D;' data-toggle='popover' data-trigger='hover' class='fa fa-trash-o pointer btn btn-default eliminar_plan'></span> <span style='color: #2E79E5;' title='Editar' data-toggle='popover' data-trigger='hover' class='fa fa-wrench pointer btn btn-default modificar_plan_sol'></span>";
            $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec" ? true : false;
            $id = $this->input->post('id_solicitud');
            $plan = $this->becas_model->listar_plan_accion($id);
            $estado = $this->becas_model->estado($id);
            foreach($plan as $item ){
                $item['acciones'] = $acciones;
                array_push($resp, $item);
            }
        }
        echo json_encode($resp);
    }

    public function detalle_actividades(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec" ? true : false;
            $id_plan = $this->input->post('id_plan');
            $resp = $this->becas_model->listar_actividades($id_plan);
        }
        echo json_encode($resp);
    }
    // lista
    public function detalle_entregables(){
        $resp = [];

        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $acciones = "<span title='Eliminar' style='color: #DE4D4D;' data-toggle='popover' data-trigger='hover' class='fa fa-trash-o pointer btn btn-default eliminar_entregable'></span> <span style='color: #2E79E5;' title='Editar' data-toggle='popover' data-trigger='hover' class='fa fa-wrench pointer btn btn-default modificar_entregable_sol'></span>";
            $btn_cerrada = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';


            $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec" ? true : false;
            $id = $this->input->post('id_solicitud');
            $entrega = $this->becas_model->listar_entregables($id);
            $estado = $this->becas_model->estado($id);
            foreach($entrega as $item ){
                ($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr') ? $item['acciones'] = $acciones : $item['acciones'] = $btn_cerrada;
                array_push($resp, $item);
            }
        }
        echo json_encode($resp);
    }

    public function detalle_entregables_btn_ver(){
        $resp = [];

        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $btn_cerrada = "<span style='color: #2E79E5;' title='Ver' data-toggle='popover' data-trigger='hover' class='fa fa-eye pointer btn btn-default entregable_compromisos'></span>";
            $id = $this->input->post('id_solicitud');
            $entrega = $this->becas_model->listar_entregables($id);
            foreach($entrega as $item ){
                $item['acciones'] = $btn_cerrada;
                array_push($resp, $item);
            }
        }
        echo json_encode($resp);
    }

    public function detalle_plan_btn_ver(){
        $resp = [];

        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $btn_cerrada = "<span style='color: #2E79E5;' title='Ver' data-toggle='popover' data-trigger='hover' class='fa fa-eye pointer btn btn-default plan_actividades'></span>";
            $id = $this->input->post('id_solicitud');
            $plan = $this->becas_model->listar_plan_accion($id);
            foreach($plan as $item ){
                $item['acciones'] = $btn_cerrada;
                array_push($resp, $item);
            }
        }
        echo json_encode($resp);
    }

    // lista
    public function detalle_compromisos(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec" ? true : false;
            $id_entrega = $this->input->post('id_entrega');
            $resp = $this->becas_model->listar_compromisos($id_entrega);
        }
        echo json_encode($resp);
    }
    // lista
    public function datos_solicitante(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec" ? true : false;
            $id = $this->input->post('id_solicitante');
            $resp = $this->becas_model->listar_datos_solicitante_p($id);
            $aux = $this->becas_model->formacion_solicitante($id);
            array_push($resp[0], $aux);
        }
        echo json_encode($resp);
    }
    // lista
    public function detalle_estados(){
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $admin = ($_SESSION["perfil"] === "Per_Admin" || $_SESSION["perfil"] === "Per_Adm_Bec")? true : false;
            $id = $this->input->post('id_estado');
            $resp = $this->becas_model->listar_estados($id);
        }
        echo json_encode($resp);
    }
    // lista
    public function consulta_solicitud_id(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $id = $this->input->post('id');
            $resp = $this->becas_model->consulta_solicitud_id($id);
        }
        echo json_encode($resp);
    }
    // lista
    public function filtrar_solicitud(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $fecha_i = $this->input->post('filtro_fecha_inicio');
            $fecha_f = $this->input->post('filtro_fecha_termina');
            $admitido = $this->input->post('filtro_admitido_al_programa');
            $nivel_formacion = $this->input->post('filtro_id_nivel_formacion');
            $estado = $this->input->post('filtro_estado');

            $resp = $this->becas_model->filtrar_solicitud($fecha_i, $fecha_f, $admitido, $nivel_formacion, $estado);
        }
        echo json_encode($resp);
    }
    // lista (MODIFICAR SEGUN PERMISOS)
    // visto buenos 
    public function validar_estado($id, $estado_nuevo){ 
        $persona = $_SESSION["persona"];
        $solicitud = $this->becas_model->consulta_solicitud_id($id);
        $solicitante = $solicitud->{'id_usuario_registro'};
        $estado_actual = $solicitud->{'id_estado_solicitud'};
        $tipo_solicitud = $solicitud->{'id_tipo'};
        $permisos = $this->becas_model->validar_permisos_administrar($persona, $estado_actual, $tipo_solicitud);
        $admin = ($_SESSION['perfil'] === 'Per_Admin' || $_SESSION['perfil'] === 'Per_Adm_Bec')? true : false;
        $resp = false;
        
        if(($_SESSION['perfil'] === 'Per_Admin' || $persona === $solicitante) && $estado_actual === 'Bec_Form' && ($estado_nuevo === 'Bec_Envi' ||  $estado_nuevo === 'Bec_Canc')) $resp = true;
        else if(($admin || $permisos) && ($estado_actual === 'Bec_Envi') && ($estado_nuevo === 'Bec_Revi' || $estado_nuevo === 'Bec_Rech' || $estado_nuevo === 'Bec_Corr')) $resp = true;
        else if(($admin || $permisos) && ($estado_actual === 'Bec_Revi') && ($estado_nuevo === 'Bec_Vis_Buen' || $estado_nuevo === 'Bec_Rech' || $estado_nuevo === 'Bec_Corr')) $resp = true;
        else if(($admin || $permisos) && ($estado_actual === 'Bec_Gest') && ($estado_nuevo === 'Bec_Gest_Inve' || $estado_nuevo === 'Bec_Rech' || $estado_nuevo === 'Bec_Corr')) $resp = true;
        else if(($admin || $permisos) && ($estado_actual === 'Bec_Gest_Inve') && ($estado_nuevo === 'Bec_Tram' || $estado_nuevo === 'Bec_Rech' || $estado_nuevo === 'Bec_Corr')) $resp = true;
        else if(($admin || $permisos) && ($estado_actual === 'Bec_Tram') && ($estado_nuevo === 'Bec_Acep' || $estado_nuevo === 'Bec_Rech' || $estado_nuevo === 'Bec_Corr')) $resp = true;
        else if(($admin || $permisos) && ($estado_actual === 'Bec_Acep') && ($estado_nuevo === 'Bec_Apro' || $estado_nuevo === 'Bec_Rech')) $resp = true;
        else if(($admin) && ($estado_actual === 'Bec_Apro') && ($estado_nuevo === 'Bec_Fina')) $resp = true;      
        else if(($_SESSION['perfil'] === 'Per_Admin' || $persona === $solicitante) && $estado_actual === 'Bec_Corr' && ($estado_nuevo === 'Bec_Envi' || $estado_nuevo === 'Bec_Revi' || $estado_nuevo === 'Bec_Gest' || $estado_nuevo === 'Bec_Gest_Inve' || $estado_nuevo === 'Bec_Tram')) $resp = true;
             
        return $resp;
    }
    // lista
    public function cargar_estados(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $id_parametro = $this->input->post('id_parametro');
            $resp = $this->becas_model->cargar_estados($id_parametro);
        }
        echo json_encode($resp);
        return;
    }
    // lista
    public function cambiar_estado_eliminar(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $admin = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Adm_Bec" ? true : false;
            $id = $this->input->post('id');
            $id_solicitud = $this->input->post('id_solicitud');
            $estado = $this->becas_model->estado($id_solicitud);
            $tabla = $this->input->post('tabla');
            $data = [
                'estado' => 0
            ];
            if($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr'){
                if($tabla === 'becas_concepto'){
                    $conc = $this->pages_model->modificar_datos($data, 'becas_concepto', $id);
                    $resp = ['mensaje'=>"Concepto eliminado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                    if($conc == -1){
                        $resp = ['mensaje'=>"Error al eliminar el concepto apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                    }
                }else if($estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                    if($tabla === 'becas_manejo_tech'){
                        $herr = $this->pages_model->modificar_datos($data, 'becas_manejo_tech', $id);
                        $resp = ['mensaje'=>"Herramienta eliminada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                        if($herr == -1){
                            $resp = ['mensaje'=>"Error al eliminar la herramienta apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }
                    }else if($tabla === 'becas_prod_intelectual'){
                        $prod = $this->pages_model->modificar_datos($data, 'becas_prod_intelectual', $id);
                        $resp = ['mensaje'=>"Producto intelectual eliminado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                        if($prod == -1){
                            $resp = ['mensaje'=>"Error al eliminar el producto apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }
                    }else if($tabla === 'becas_sector_productivo'){
                        $exp = $this->pages_model->modificar_datos($data, 'becas_sector_productivo', $id);
                        $resp = ['mensaje'=>"Experiencia eliminada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                        if($exp == -1){
                            $resp = ['mensaje'=>"Error al eliminar la experiencia apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }
                    }else if($tabla === 'becas_compromiso_entregable'){
                        $ent = $this->pages_model->modificar_datos($data, 'becas_compromiso_entregable', $id);
                        $comp = $this->pages_model->modificar_datos($data, 'becas_compromisos', $id, 'id_entregable');
                        $resp = ['mensaje'=>"Entregable eliminado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                        if($ent == -1 || $comp == -1){
                            $resp = ['mensaje'=>"Error al eliminar el entregable apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }
                    }else if($tabla === 'becas_plan_accion'){
                        $meta = $this->pages_model->modificar_datos($data, 'becas_plan_accion', $id);
                        $resp = ['mensaje'=>"Meta eliminada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                        if($meta == -1){
                            $resp = ['mensaje'=>"Error al eliminar la meta apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }
                    }else if($tabla === 'becas_plan_accion_gestion'){
                        $actividad = $this->pages_model->modificar_datos($data, 'becas_plan_accion_gestion', $id);
                        $resp = ['mensaje'=>"Actividad eliminada con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                        if($actividad == -1){
                            $resp = ['mensaje'=>"Error al eliminar la actividad apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }
                    }else if($tabla === 'becas_compromisos'){
                        $compromiso = $this->pages_model->modificar_datos($data, 'becas_compromisos', $id);
                        $resp = ['mensaje'=>"Compromiso eliminado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"];
                        if($compromiso == -1){
                            $resp = ['mensaje'=>"Error al eliminar el compromiso apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                        }
                    }
                }else{
                    $resp = ['mensaje'=>"No se puede eliminar información una vez la solicitud esta tramitada",'tipo'=>"info",'titulo'=>"Oops.!"];  
                }
            }else{
                $resp = ['mensaje'=>"No se puede eliminar información una vez la solicitud esta tramitada",'tipo'=>"info",'titulo'=>"Oops.!"];
            }
        }
        echo json_encode($resp);
    }
    // lista
    public function verificar_campos_string($array){
		foreach ($array as $row) {
			if (empty($row) || ctype_space($row)) {
				return ['type' => -2, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
	}
    // lista
	public function verificar_campos_numericos($array){
		foreach ($array as $row) {
			if (!is_numeric($row)) {
				return ['type' => -1, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
    }
    // lista
    public function validateDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function info_solicitud_renovacion(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $id_solicitud = $this->input->post('id');
            $resp = $this->becas_model->info_solicitud_renovacion($id_solicitud);
        }
        echo json_encode($resp);
    }

    public function validar_cantidad_de_solicitud(){
        $persona = $_SESSION['persona'];
        $inicial = ['id' => NULL, 'cantidad' => '0'];
        $renovar = ['id' => NULL, 'cantidad' => '0'];
        $cantidad_solicitud = $this->becas_model->validar_cantidad_de_solicitud($persona);
        foreach ($cantidad_solicitud as $row) {
			if ($row->{'id_tipo'} === 'Soli_Tip_Ini') {
               $inicial = ['id' => $row->{'id'}, 'cantidad' => $row->{'cantidad'}];
			}else if($row->{'id_tipo'} === 'Soli_Tip_Ren'){
                $renovar = ['id' => $row->{'id'}, 'cantidad' => $row->{'cantidad'}];
            }
        }
        $datos = ['inicial' => $inicial, 'renovar' => $renovar];
        echo json_encode($datos);
    }

    public function solicitud_a_renovar(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $persona = $_SESSION['persona'];
            $resp = $this->becas_model->listar_solicitud_a_renovar($persona);
        }
        echo json_encode($resp);
    }

    public function validar_porcentaje($tipo_apoyo, $apoyo_solicitado, $valor_total, $id_solicitud){
        $tipo_v = $this->becas_model->traer_tipo_vinculacion($id_solicitud);
        $sw = true;
        if($tipo_v){
            $sw = false;
            if($tipo_apoyo === 'Bec_Pag_M'){
                $total = floatval($valor_total) * (floatval($tipo_v->{'valor'})/100);
                $total_solicitado = floatval($apoyo_solicitado);
                if($total_solicitado > $total) $sw = true;
            }
        }
        return $sw;
    }

    public function validar_viaticos($tipo_apoyo, $apoyo_solicitado, $valor_total, $id_soli){
        $solicitud = $this->becas_model->consulta_solicitud_id($id_soli);
        $salario_persona = floatval($solicitud->{'salario_persona'});

        $traer_smlv = $this->becas_model->traer_smlv();
        $smlv = floatval($traer_smlv->{'smlv'});

        $sw = true;
        if($traer_smlv && $solicitud){
            $sw = false;
            if($tipo_apoyo === 'Bec_Viat'){
                if($salario_persona < ($smlv*4)){
                    $porcentaje = 40;
                }else if(($salario_persona >= ($smlv*4)) && ($salario_persona < ($smlv*10))){
                    $porcentaje = 50;
                }else{
                    $porcentaje = 60;
                } 

                $total = floatval($valor_total) * ($porcentaje/100);
                $total_solicitado = floatval($apoyo_solicitado);
                if($total_solicitado > $total) $sw = true;
            }
        }
        return $sw;
    }

    public function listar_solicitudes_notificaciones(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $persona = $_SESSION['persona'];
            $perfil = $_SESSION['perfil'];
            $soli_notificacion = $this->becas_model->listar_solicitudes_notificaciones($persona);
            $resp["solicitudes"] = $soli_notificacion;
            $resp["perfil"] = $perfil;
        }
        echo json_encode($resp);
    }

    public function listar_personas(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $persona = $this->input->post('persona');
            $resp = $persona ? $this->becas_model->listar_personas($persona) : [];
        }
		echo json_encode($resp);
    }

    public function listar_tipo_solicitud(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $persona = $this->input->post('persona');
            $resp = (isset($persona) && !empty($persona))? $this->becas_model->listar_tipo_solicitud($persona): [];
        }
		echo json_encode($resp);
    }

    public function listar_estados_permisos(){
		if(!$this->Super_estado){
            $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else {
            $permiso = $this->input->post('permiso');
            $data = $permiso ? $this->becas_model->listar_estados_permisos($permiso) : [];
		}
		echo json_encode($data);
    }
    
    public function obtener_personas_permisos(){
		if(!$this->Super_estado){
            $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else {
			$tipo = $this->input->post("tipo_soli");
			$estado = $this->input->post("estado");
			$programa = $this->input->post("programa");
			$data = $this->becas_model->obtener_personas_permisos($tipo, $estado, $programa);
		}
        echo json_encode($data);
    }
    
    public function validar_revisiones(){
		if(!$this->Super_estado){
            $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else {
            $id = $this->input->post("id");
            $revisiones_minimas = $this->becas_model->traer_minimo_revisiones();
            $n_revisiones = $this->becas_model->revisiones($id);
            $data = ($revisiones_minimas == $n_revisiones) ? true : false;
		}
        echo json_encode($data);
	}

    public function obtener_ultimo_estado(){
		if(!$this->Super_estado){
            $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else {
            $id = $this->input->post("id");
			$data = $this->becas_model->obtener_ultimo_estado($id);
		}
        echo json_encode($data);
	}    
    
    public function asignar_permiso(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else if($this->Super_agrega){
            $permiso = $this->input->post('id');
			$persona = $this->input->post('persona');
            $validacion = $this->becas_model->validar_permisos_asignados($permiso, $persona);
            if($validacion){
                $data = [
                    'id_tipo' => $permiso,
                    'id_persona' => $persona,
                    'id_usuario_registro' => $_SESSION['persona']
                ];

                $add_permiso = $this->pages_model->guardar_datos($data, 'becas_permisos_solicitudes');
                if($add_permiso == -1){
                    $resp = ['mensaje'=>"Error al guardar la informacion apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                }
                $resp = ['mensaje'=>"Permiso agregado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
            }else{
                $resp = ['mensaje' => "El usuario ya tiene asignado este permiso.", 'tipo' => "info", 'titulo' => "Ooops!"]; 
            }
        }else{
            $resp = ['mensaje' => 'No tiene Permisos Para Realizar Esta operación.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
        }     
		echo json_encode($resp);
    }

    public function desasignar_permiso(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else if($this->Super_agrega){
            $id_permiso = $this->input->post('asignado');
            $permiso = $this->input->post('id');
			$persona = $this->input->post('persona');
            $validacion = $this->becas_model->validar_permisos_asignados($permiso, $persona);
            if(!$validacion){
                $del_permiso = $this->becas_model->eliminar_registro($id_permiso, 'becas_permisos_solicitudes');
                $del_estados_permisos = $this->becas_model->eliminar_registro($id_permiso, 'becas_permisos_estados', 'id_permiso_solicitud');
                if($del_permiso == 1 || $del_estados_permisos == -1){
                    $resp = ['mensaje'=>"Error al guardar la informacion apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                }
                $resp = ['mensaje'=>"Permiso eliminado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
            }else{
                $resp = ['mensaje' => "El usuario no tiene asignado este permiso.", 'tipo' => "info", 'titulo' => "Ooops!"]; 
            }
        }else{
            $resp = ['mensaje' => 'No tiene Permisos Para Realizar Esta operación.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
        }     
		echo json_encode($resp);
    }

    public function asignar_estado_permiso(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else if($this->Super_agrega){
            $id_permiso = $this->input->post('id_permiso');
			$id_estado= $this->input->post('id_estado');
            $validacion = $this->becas_model->validar_estados_asignados($id_permiso, $id_estado);
            if($validacion){
                $data = [
                    'id_permiso_solicitud' => $id_permiso,
                    'id_estado' => $id_estado,
                    'id_usuario_registro' => $_SESSION['persona']
                ];

                $add_estado = $this->pages_model->guardar_datos($data, 'becas_permisos_estados');
                if($add_estado == -1){
                    $resp = ['mensaje'=>"Error al guardar la informacion apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                }
                $resp = ['mensaje'=>"Permiso estado agregado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
            }else{
                $resp = ['mensaje' => "El usuario ya tiene asignado este permiso estado.", 'tipo' => "info", 'titulo' => "Ooops!"]; 
            }
        }else{
            $resp = ['mensaje' => 'No tiene Permisos Para Realizar Esta operación.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
        }     
		echo json_encode($resp);
    }

    public function desasignar_estado_permiso(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else if($this->Super_agrega){
            $estado = $this->input->post('asignado');
            $id_permiso = $this->input->post('id_permiso');
			$id_estado= $this->input->post('id_estado');
            $validacion = $this->becas_model->validar_estados_asignados($id_permiso, $id_estado);
            if(!$validacion){
                $delete_estado = $this->becas_model->eliminar_registro($estado, 'becas_permisos_estados');
                if($delete_estado == -1){
                    $resp = ['mensaje'=>"Error al guardar la informacion apropiadamente, contacte con el administrador",'tipo'=>"error",'titulo'=>"Oops.!"];
                }
                $resp = ['mensaje'=>"Permiso estado eliminado con exito",'tipo'=>"success",'titulo'=> "Proceso exitoso!"]; 
            }else{
                $resp = ['mensaje' => "El usuario no tiene asignado este permiso estado.", 'tipo' => "info", 'titulo' => "Ooops!"]; 
            }
        }else{
            $resp = ['mensaje' => 'No tiene Permisos Para Realizar Esta operación.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
        }     
		echo json_encode($resp);
    }

    public function validarRevision(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $id = $this->input->post('id');
            $sw = true; 
            $estado = $this->becas_model->estado($id);
            $conceptos = $this->becas_model->listar_conceptos($id);
            $produccion = $this->becas_model->listar_produccion_intelectual($id);
            $entregable = $this->becas_model->listar_entregables($id);
            $archivos = $this->becas_model->listar_archivos_adjuntos($id);
            if(empty($conceptos)){
                $resp = ['mensaje'=>"Debe agregar los Conceptos antes de pasar a la siguiente etapa",'tipo'=>"info",'titulo'=>"Oops.!"];
                $sw = false;
            }else if(empty($entregable) && $estado->{'id_tipo'} === 'Soli_Tip_Ini' ){
                $resp = ['mensaje'=>"Debe agregar los Entregables antes de pasar a la siguiente etapa",'tipo'=>"info",'titulo'=>"Oops.!"];
                $sw = false;
            }else if(empty($produccion) && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                $resp = ['mensaje'=>"Debe agregar el Producto Intelectual a realizar antes de pasar la siguiente etapa",'tipo'=>"info",'titulo'=>"Oops.!"];
                $sw = false;
            }else if(empty($archivos) && $estado->{'id_tipo'} === 'Soli_Tip_Ren'){
                $resp = ['mensaje'=>"Debe agregar los Anexos correspondientes al periodo anterior, antes de pasar la siguiente etapa",'tipo'=>"info",'titulo'=>"Oops.!"];
                $sw = false;
            }else if(!empty($entregable)){
                foreach($entregable as $item ){
                    $compromisos = $this->becas_model->listar_compromisos($item['id']);
                    if(empty($compromisos) && $estado->{'id_tipo'} === 'Soli_Tip_Ini'){
                        $resp = ['mensaje'=>"Todos los Entregables deben tener Compromisos",'tipo'=>"info",'titulo'=>"Oops.!"];
                        $sw = false;
                    }
                }
            }
            if($sw) $resp = ['mensaje'=>"",'tipo'=>"success",'titulo'=>""];
        }
        echo json_encode($resp);
    }

    public function recibir_archivos(){
        if(!$this->Super_estado){
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=>""];
        }else{
            $id_solicitud = $_POST['solicitud'];
            $nombre = $_FILES["file"]["name"];
            $estado = $this->becas_model->estado($id_solicitud);

            if($estado->{'id_estado'} === 'Bec_Form' || $estado->{'id_estado'} === 'Bec_Corr'){
                $tabla = 'becas_archivos_adj';
                $ruta = $this->ruta_archivos_becas;
                $listar = 'anexos';
            }else if($estado->{'id_estado'} === 'Bec_Apro'){
                $tabla = 'becas_soportes_fin';
                $ruta = $this->ruta_soportes_becas;
                $listar = 'certificados';
            }else{
                $resp = ['mensaje'=>"No puede subir mas archivos, una vez la solicitud este tramitada. ",'tipo'=>"info",'titulo'=> "Oops.!"];  
                echo json_encode($resp);
                return;
            }

                $cargo = $this->cargar_archivo("file", $ruta, "bec");
                if ($cargo[0] == -1) {
                    header("HTTP/1.0 400 Bad Request");
                    echo ($nombre);
                    return;
                }

                $data = [
                    "id_solicitud" => $id_solicitud,
                    "nombre_real" => $nombre,
                    "nombre_guardado" => $cargo[1],
                    "id_usuario_registro" => $_SESSION['persona']
                ];

                $res = $this->pages_model->guardar_datos($data, $tabla);
                if ($res == -1) {
                    header("HTTP/1.0 400 Bad Request");
                    echo ($nombre);
                    return;
                }
                $resp = ['mensaje'=>"Todos Los archivos fueron cargados.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
                $resp['listar'] = $listar;
        }
        echo json_encode($resp);
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

    public function listar_archivos_adjuntos(){
        if(!$this->Super_estado){
            $resp = ['mensaje' => "", 'tipo'=>"sin_session", 'titulo'=>""];
        }else{
            $id_sol = $this->input->post('id_solicitud');
            $tipo = $this->input->post('tipo');
            $tabla = ($tipo === 'certificados') ? 'becas_soportes_fin' : 'becas_archivos_adj';
            $archivos = $this->becas_model->listar_archivos_adjuntos($id_sol, $tabla);
        }
        echo json_encode($archivos);
    }
}


