<?php
class profesores_eval_control extends CI_Controller {
    // Variables encargadas de los permisos que tiene el usuario que esta en session
    var $Super_estado = false;
    var $Super_agrega = 0;
    var $Super_modifica = 0;
    var $Super_elimina = 0;

    var $super_admin = false;
    // var $admin = false;
    // var $estado = null;
    //Se crea el contructor del controlador y se importa el modelo de profesores_eval_model y se inicia la session
    public function __construct(){
        parent::__construct();
        $this->load->model('profesores_eval_model');
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
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'profesores_eval');
            if (!empty($datos_actividad)) {
              $pages = "profesores_eval";
              $data['js'] = "Profesores_eval";
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
        $persona = $_SESSION["persona"];
        $fecha_inicial = $this->input->post("fecha_inicial");
        $fecha_final = $this->input->post("fecha_final");
        $id_estado_sol = $this->input->post("id_estado_sol");

        $ver_solicitado = '<span  style="background-color: #fff; color: black; width: 100%" class="pointer form-control ver" ><span >ver</span></span>';
        $ver_finalizado = '<span  style="background-color: green; color: white; width: 100%" class="pointer form-control ver_finalizar" ><span >ver</span></span>';

        $ver_detalle = '<span title="Detalle" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;" class="pointer fa fa-send btn btn-default detalle_eval"></span>';
        $btn_finalizar = '<span title="Finalizar" data-toggle="popover" data-trigger="hover" style="color: #00cc00;"class="pointer fa fa-check btn btn-default finalizar"></span>';
        $btn_chat = '<span title="Comentar" data-toggle="popover" data-trigger="hover" style="color: black;"class="pointer fa fa-comment btn btn-default chat"></span>';

        $solicitud = $this->Super_estado ? $this->profesores_eval_model->listar_solicitudes($fecha_inicial, $fecha_final, $id_estado_sol) : array();
        $resp = [];
        foreach ($solicitud as $key) {
            $id = $key['id'];
            $categorias = $this->profesores_eval_model->listar_categorias($id);
            $data = $this->profesores_eval_model->listar_detalle_evaluacion($id);
            if($key['estado_verificacion'] === 'Eval_Rev'){
                $key['ver'] = $ver_solicitado;
                $key['acciones'] = "$ver_detalle $btn_finalizar $btn_chat";
                $key['data'] = $data;
                $key['categoria'] = $categorias;
            }else if($key['estado_verificacion'] === 'Eval_Fin'){
                $key['ver'] = $ver_finalizado;
                $key['acciones'] = "$ver_detalle $btn_chat";
                $key['data'] = $data;
                $key['categoria'] = $categorias;
            }
            array_push($resp, $key);
        }
        echo json_encode($resp);
    }

    public function listar_detalle_evaluacion(){
        $ver_rol = '<span style="background-color: #ecb400; color: black; width: 100%" class="pointer form-control ver_rol"><span >ver</span></span>';
        $ver_rol2 = '<span style="background-color: #403100; color: white; width: 100%" class="pointer form-control ver_rol2" ><span >ver</span></span>';
        $ver_inv = '<span style="background-color: #fffbed; color: black; width: 100%" class="pointer form-control ver_inv" ><span >ver</span></span>';
        $ver_doc = '<span style="background-color: #b28800; color: white; width: 100%" class="pointer form-control ver_doc" ><span >ver</span></span>';
        $ver_rol3 = '<span style="background-color: #3c2e00; color: black; width: 100%" class="pointer form-control ver_rol3" ><span >ver</span></span>';

        $id = $this->input->post('id_evaluacion');
        $resp = [];
        $solicitud = $this->profesores_eval_model->listar_detalle_evaluacion($id);
        foreach ($solicitud as $key){
            $categoria = $key['categoria'];
            if($categoria == 'DOC'){
                $key['ver'] = $ver_doc;
            }else if($categoria == 'INV'){
                $key['ver'] = $ver_inv;
            }else if($categoria == 'ROL'){
                $key['ver'] = $ver_rol;
            }else if($categoria == 'ROL2'){
                $key['ver'] = $ver_rol2;
            }else{
                $key['ver'] = $ver_rol3;
            }
            array_push($resp, $key);
        }
        echo json_encode($resp);
    }

    public function validar_estado($id, $estado_nuevo){
        $persona = $_SESSION["persona"];
        $solicitud = $this->profesores_eval_model->consulta_solicitud_id($id);
        $solicitante = $solicitud->{'persona'};
        $estado_actual = $solicitud->{'estado_verificacion'};
        $admin = ($_SESSION['perfil'] === 'Per_Admin')? true : false;
        $resp = false;
        if(($_SESSION['perfil'] === 'Per_Admin' || $persona === $solicitante) && $estado_actual === 'Eval_Rev' && ($estado_nuevo === 'Eval_Fin')) $resp = true;
        return $resp;
    }

    public function cambiarEstado(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id_solicitud = $this->input->post("id_solicitud");
            $estado = $this->input->post("estado");
            $id_usuario_registra = $_SESSION["persona"];
            $observaciones = $this->input->post('observaciones');
            $valido = $this->validar_estado($id_solicitud, $estado);
            if($valido){
                $data = [
                    'id_evaluacion' => $id_solicitud,
                    'id_estado' => $estado,
                    'id_usuario_registro' => $id_usuario_registra,
                    'observaciones' => $observaciones ? $observaciones : NULL,
                ];
                $data_solicitud = [
                    'estado_verificacion' => $estado,
                ];
                $add = $this->pages_model->guardar_datos($data, 'profesores_estados');
                $add_sol = $this->pages_model->modificar_datos($data_solicitud, 'profesores_evaluacion', $id_solicitud);
                if ($add_sol == -1){
                    $resp = ['mensaje' => 'Error al guardar la información, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
                }else if($add == -1)
                    $resp = ['mensaje' => 'Error al modificar la información, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
                else{
                    $resp = ['mensaje' => "Proceso finalizado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                }
            }else{
                $resp = ['mensaje' => "Este proceso ya fue gestionado.", 'tipo' => "info", 'titulo' => "Oops.!"];
            }
        }
        echo json_encode($resp);
    }


    public function comentar(){
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id_solicitud = $this->input->post("id_solicitud");
            $id_usuario_registra = $_SESSION["persona"];
            $comentario = $this->input->post('mensaje');
                $data = [
                    'id_evaluacion' => $id_solicitud,
                    'id_usuario_registra' => $id_usuario_registra,
                    'comentario' => $comentario ? $comentario : NULL,
                ];
                $add = $this->pages_model->guardar_datos($data, 'profesores_comentarios');
                if($add == -1)
                    $resp = ['mensaje' => 'Error al modificar la información, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
                else{
                    $resp = ['mensaje' => "Proceso finalizado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                }
        }
        echo json_encode($resp);
    }

    public function obtener_valor_parametro()
  	{
    	$parametro = $this->input->post('id');
    	$valores = $this->Super_estado == true ? $this->profesores_eval_model->obtener_valor_parametro($parametro) : array();
    	echo json_encode($valores);
    }
    
    public function obtener_vParametro()
  	{
    	$parametro = $this->input->post('id');
    	$valores = $this->Super_estado == true ? $this->profesores_eval_model->obtener_vParametro($parametro) : array();
    	echo json_encode($valores);
    }
    public function descargar_eval($id){
        if($this->Super_estado == true){
            $data = [];
          $datos = $this->profesores_eval_model->consulta_evaluacion_id($id);
          if($_SESSION["perfil"] == "Per_Admin" ){
            $data['datos'] = [
            "nombre_completo" => $datos->{'fullname'},
            "identificacion" => $datos->{'identificacion'},
            "id" => $id ];
            return $this->load->view('templates/descargar_eva_prof', $data);
          }  
        }
        redirect('/', 'refresh');
    }   
}