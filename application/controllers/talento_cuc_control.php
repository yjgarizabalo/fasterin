<?php
	class talento_cuc_control extends CI_Controller {
	//Variables encargadas de los permisos que tiene el usuario en session
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
    var $ruta_archivos = "archivos_adjuntos/talento_cuc/";
    var $ruta_imagenes = "imagenes_personas/";

	var $super_admin = false;
    var $admin = false;

    public function __construct(){
        parent::__construct();
        $this->load->model('genericas_model');
        $this->load->model('talento_cuc_model');
        $this->load->model('pages_model');
        date_default_timezone_set("America/Bogota");
        session_start();
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
            $this->administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Adm_Eval' || $_SESSION['perfil'] == 'Per_Admin_Tal' || $_SESSION['perfil'] == 'Per_Adm_Talcuc' ? true : false;
        }
    }

    public function index($id = 0){
        $pages = "inicio";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $data["id"] = $id;
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], "talento_cuc");
            $datos_actividad_adm = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'talento_humano_adm/talento_cuc');
			if(!empty($datos_actividad_adm)) $datos_actividad = array_merge($datos_actividad,$datos_actividad_adm);
            if (!empty($datos_actividad)) {
                $pages = "talento_cuc";
                $data['js'] = "talento_cuc";
                $data['actividad'] = $datos_actividad[0]["id_actividad"];
            }else{
                $pages = "sin_session";
                $data['js'] = "";
                $data['actividad'] = "Permisos";
            }
        }
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function asistencia_formacion($id=0){
        $pages = "inicio";
        $data['id_formacion'] = $id;
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $data['nombre_completo'] =  '';
        $data['estado_formacion'] = false;
        if ($this->Super_estado) {
            $persona = $this->talento_cuc_model->traer_registro_id($_SESSION['persona'], 'personas', 'id');
            $formacion = $this->talento_cuc_model->get_formacion($id);
            $competencias = $this->talento_cuc_model->get_competencias_formacion($id);
            // if (!empty($formacion)) {
                $pages = "talento_cuc_asistencia";
                $data['js'] = "talento_cuc"; 
                $data['actividad'] = 'Encuesta de satisfacción';
                $data['estado_formacion'] = true;
                $data['nombre_completo'] = $persona->{'nombre_completo'};
                $data['competencias'] = $competencias;
                $data['facilitador'] = $formacion->{'funcionario'};
            // }else{
            //     $pages = "sin_session";
            //     $data['js'] = "";
            // }  
        }else{
            $pages = "inicio";
            $data['js'] = "";
        } 

        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function asistencia_entrenamiento($id=0){
        $data = [];
        $pages = "sin_session";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $render = false;
        $progress = 100;
        if ($this->Super_estado) {
            $render = $_SESSION["persona"] == $id || $_SESSION['perfil'] == 'Per_Admin' ? true : false;
            if($render){
                $info = $this->talento_cuc_model->obtener_info_persona($id);
                $cantidades = $this->talento_cuc_model->cantidad_asistencias_entrenamiento($info[0]['identificacion']);
                $porcentaje = $cantidades['cantidad'] > 0 ? ($cantidades['aprobados']/$cantidades['cantidad'])*100 : 0;
                $progress = round($porcentaje);
                $pages = "talento_cuc_asistencia_entrenamiento";
                $data['js'] = "talento_cuc"; 
                $data['actividad'] = 'Asistencia de Entrenamientos';
                $data['identificacion'] = $info[0]['identificacion'];
                $data['progress'] = $progress.'%'; 
            }
        }
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function validar_actas_entrenamiento($id=0){
        $data = [];
        $pages = "sin_session";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $render = false;
        $progress = 100;
        if ($this->Super_estado) {
            $render = $_SESSION["persona"] == $id || $_SESSION['perfil'] == 'Per_Admin' ? true : false;
            if($render){
                $info = $this->talento_cuc_model->listar_actas_personas($id);
                $porcentaje = $info[0] ? ($info[1]/count($info[0]))*100 : 0;
                $progress = round($porcentaje);
                $pages = "talento_cuc_actas_entrenamiento";
                $data['js'] = "talento_cuc"; 
                $data['actividad'] = 'Actas Aceptación de Cargo';
                $data['id'] = $id;
                $data['progress'] = $progress.'%'; 
            }
        }
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function encuesta_entrenamiento($id=0){
        $data = [];
        $pages = "sin_session";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $render = false;
        if ($this->Super_estado) {
            $render = $_SESSION["persona"] == $id ? true : false;
            if($render){
                $info = $this->talento_cuc_model->obtener_info_persona($id);
                $send = $this->talento_cuc_model->get_encuesta_enviada($info[0]['identificacion']);
                $pages = "talento_cuc_encuesta_entrenamiento";
                $data['js'] = "talento_cuc"; 
                $data['actividad'] = 'Encuesta de Entrenamiento';
                $data['nombre_completo'] = $info[0]['nombre_completo'];
                $data['estado_encuesta'] = $send ? true : false;
                $data['identificacion'] = $info[0]['identificacion'];
            }
        }
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function acta_aceptacion_cargo($id=0){
        $data = [];
        $pages = "sin_session";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $render = false;
        if ($this->Super_estado) {
            $render = $_SESSION["persona"] == $id ? true : false;
            if($render){
                $info = $this->talento_cuc_model->obtener_info_persona($id);
                $row = $this->talento_cuc_model->estado_entrenamiento($id, $info[0]['id_cargo_sap']);
                $pages = "talento_cuc_acta_cargo";
                $data['js'] = "talento_cuc"; 
                $data['actividad'] = 'Acta Aceptación Cargo';
                $data['nombre_completo'] = $info[0]['nombre_completo'];
                $data['codigo_cargo'] = $info[0]['codigo_cargo'];
                $data['cargo'] = $info[0]['cargo'];
                $data['jefe_inmediato'] = $row->{'nombre_jefe'};
                $data['estado'] = $row->{'acta_enviada'} && !$row->{'firma_fun'} ? true : false;
            }
        }
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function preparar_data_formacion($id_persona, $id_evaluacion = null ){
        $resp = [];
        $descarga = false;
        $tiempo = 0;
        $data = $this->talento_cuc_model->listar_detalle_resultados($id_persona,1,$id_evaluacion);
        $horas_formacion = 0;
        $horas_acumuladas = 0;
        foreach ($data as $row) {
            $horas_s = $this->talento_cuc_model->get_tiempo_formacion_soporte($id_persona, $row['id_competencia']);
            $horas_a = $this->talento_cuc_model->get_tiempo_formacion_asistencia($id_persona, $row['id_competencia']);
            $row["tiempo"] = $horas_s->{'tiempo'} +  $horas_a->{'tiempo'};
            $horas_formacion += $row['hora_formacion'];
            $horas_acumuladas += $row["tiempo"];
            array_push($resp,$row);
        }
        $descarga = $data && $horas_acumuladas >= $horas_formacion ? true : false;
		return [$resp, $descarga, $horas_acumuladas];
    }

    public function hoja_vida($id) {
        $data = [];
        $pages = "sin_session";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $render = false;
        if ($this->Super_estado) {
            $persona = $_SESSION["persona"] == $id ? true : false;
            $info = $this->talento_cuc_model->obtener_info_persona($id);
            if ($info) {
                $periodo_actual = $this->talento_cuc_model->get_periodo_actual();
                $periodos_evaluados = $this->talento_cuc_model->get_periodos_evaluados($info[0]['identificacion']);
                $entrenamiento = $this->talento_cuc_model->listar_plan_entrenamiento($info[0]['identificacion'],'',1);
                $plan_formacion = $this->preparar_data_formacion($info[0]['identificacion']);
                $resultado_ind = $this->talento_cuc_model->get_resultado_indicadores($info[0]['identificacion'], $periodo_actual);
                $sw = $resultado_ind ? true : false;
                $render = true;
                $pages =  "hoja_vida";
                $data['js'] ="talento_cuc";
                $data['actividad'] = 'Hoja de Vida';
                $data['plan'] = $info[0];
                $data['foto'] = $info[0]['foto'] == 'Myfoto.png' || $info[0]['foto'] == 'User.png' ? 'empleado.png' : $info[0]['foto'];
                $data['persona_estado'] = $persona;
                $data['entrenamiento'] = !$entrenamiento ? false : true;
                $data['plan_formacion'] = $plan_formacion[0];
                $data['plan_entrenamiento'] = $entrenamiento;
                $data['descarga'] = $plan_formacion[1];
                $data['promedio_metas'] = $sw ? $resultado_ind->{'promedio_meta'} : 0;
                $data['promedio_formacion'] = $sw ? $resultado_ind->{'promedio_formacion'} : 0;
                $data['promedio_funciones'] = $sw ? $resultado_ind->{'promedio_funciones'} : 0;
                $data['periodos_evaluados'] = $periodos_evaluados;
            }
        }
        if ($render)  $this->load->view("pages/".$pages,$data);
        else{
            $this->load->view('templates/header',$data);
            $this->load->view("pages/".$pages);
            $this->load->view('templates/footer'); 
        }
    }

    public function descargar_certificado($id,$id_evaluacion=null) {
        $data = [];
        $pages = "sin_session";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $render = false;
        $persona = $_SESSION["persona"] == $id ? true : false;
        if ($this->Super_estado && ($persona || $this->administra)) {
            $info = $this->talento_cuc_model->obtener_info_persona($id);
            $competencias = $this->preparar_data_formacion($id,$id_evaluacion);
            // $horas = 0;
            // $detalle = $this->talento_cuc_model->listar_detalle_resultados($info[0]["id_persona"],1);
            // foreach ($detalle as $row) {
            //     $horas_s = $this->talento_cuc_model->get_tiempo_formacion_soporte($info[0]["id_persona"], $row['id_competencia']);
            //     $horas_a = $this->talento_cuc_model->get_tiempo_formacion_asistencia($info[0]["id_persona"], $row['id_competencia']);
            //     $row['horas'] = $horas_s->{'tiempo'} +  $horas_a->{'tiempo'};
            //     $horas += $row['horas'];
            //     array_push($competencias, $row);
            // }
            if ($info) {
                $render = true;
                $pages =  "descargar_certificado_talento_cuc";
                $data['js'] ="talento_cuc";
                $data['actividad'] = 'Certificado';
                $data['horas'] = $competencias[2];
                $data['nombre_completo'] = $info[0]["nombre_completo"];
                $data['identificacion'] = $info[0]["id_persona"];
                $data['lugar_expedicion'] = $info[0]["lugar_expedicion"];
                $data['competencias'] = $competencias[0];
            }
        }
        if ($render)  $this->load->view("templates/".$pages,$data);
        else{
            $this->load->view('templates/header',$data);
            $this->load->view("pages/".$pages);
            $this->load->view('templates/footer'); 
        }
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

    public function array_sort_by(&$arrIni, $col, $order = SORT_ASC, $filtro = null){
        $horas = $this->genericas_model->obtener_valores_parametro(241);
        $arrAux = array();
        foreach ($arrIni as $key=> $row){
            $arrAux[$key] = is_object($row) ? $arrAux[$key] = $row->$col : $row[$col];
            $arrAux[$key] = strtolower($arrAux[$key]);
            $arrIni[$key]['observaciones'] ='Puntaje bajo';
            foreach ($horas as $es) {
                if($row[$col] >= (int)$es['valory'] && $row[$col] <= (float)$es['valorz']){
                     $h = $es['valorx'];
                     break;
                }
            }
            $arrIni[$key]['hora_formacion'] = $h;
        }
        array_multisort($arrAux, $order, $arrIni);
        return $filtro ? array_slice($arrIni, 0, 5) : $arrIni;
    }

    public function get_detalle_solicitud(){
        $id_solicitud = $this->input->post("id_solicitud");
        $datos = $this->Super_estado ? $this->talento_cuc_model->get_solicitud($id_solicitud) : array();
        echo json_encode($datos);
    }

    public function listar_personas(){
        if (!$this->Super_estado) $res = array();
		else {
            $buscar = $this->input->post("buscar");
            $id_persona = $this->input->post("id");
            $fecha_i = $this->input->post("fecha_i");
            $fecha_f = $this->input->post("fecha_f");
            $periodo = $this->input->post("periodo");
            $res = array();
            $btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            $btn_calcular_plan = '<span title="Plan de formacion" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="pointer fa fa-book btn btn-default formacion"></span>';
            $btn_cal_plan_nuevos = '<span title="Plan de formacion de Ingreso" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;" class="pointer fa fa-book btn btn-default formacion_nuevo"></span>';
            $btn_plan_entrenamiento = '<span title="Plan de entrenamiento" data-toggle="popover" data-trigger="hover" class="pointer fa fa-user-plus btn btn-default red entrenamiento"></span>';
            $btn_hv = '<a class="pointer sinlink hv" target="_blank" title="Abrir hoja de vida"><span data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="fa fa-folder-open btn btn-default"></span></a>';
            $btn_revisar = '<span title="Revisar soportes" data-toggle="popover" data-trigger="hover" class="pointer fa fa-bell tiembla btn btn-default avalar_soporte" style="color: #f0ad4e; -webkit-animation: tiembla .5s infinite;"></span>';
            $bg_color = 'white';
            $color = 'black';
            $rev = '';
            if (!empty($buscar) || !empty($id_persona) || !empty($fecha_i) || !empty($fecha_f) || !empty($periodo)){
                $data = $this->talento_cuc_model->listar_personas($buscar,$id_persona,$fecha_i,$fecha_f,$periodo);
                foreach ($data as $row) {
                    $rev = $row['soportes'] ? $btn_revisar : '';
                    if($this->administra){
                        $row['gestion'] = $row['id_solicitud'] ? $rev .' '. $btn_calcular_plan .' '. $btn_plan_entrenamiento .' '. $btn_hv : $rev .' '. $btn_plan_entrenamiento .' '. $btn_hv;
                        $row['gestion'] .= $row['ingreso'] ? ' '. $btn_cal_plan_nuevos : ''; 
                    }else{
                        $row['gestion'] = $rev.' '.$btn_hv; 
                    }
                    $row['ver'] = "<span  style='background-color: $bg_color;color: $color; width: 100%;' class='pointer form-control'><span >ver</span></span>";
                    array_push($res,$row);
                }
            }
		}
		echo json_encode([$res, $this->administra]);
    }

    public function mostrar_notificaciones(){
        $resp = array();
		if ($this->Super_estado) {
            $id_persona = $_SESSION['persona'];
			$data = $this->talento_cuc_model->get_soportes_Avalar();  
            foreach ($data as $row){
                if($row['cantidad'] > 0 ){
                $row['descripcion'] = $row['cantidad']." Soporte(s) nuevo(s).";
                $row['accion'] = "revisar_notificacion(".$row['id_persona'].",".$row['identificacion'].")";
                    array_push($resp,$row); 
                }
            }
            
            $info = $this->talento_cuc_model->obtener_info_persona($id_persona);
            $dato = $this->talento_cuc_model->cantidad_asistencias_entrenamiento($info[0]['identificacion']);
            $y = $dato['aprobados'];
            $x = $dato['cantidad'];
            if($x != $y){
                $num = $x - $y;
                array_push($resp,[
                    'nombre_completo'=> "Asistencias de Plan de Entrenamiento",
                    'descripcion'=> $num." Asistencias por avalar.",
                    'accion' => "window.open('".base_url()."index.php/talento_cuc/asistencia_entrenamiento/$id_persona')"]);
            }

            $send = $this->talento_cuc_model->get_encuesta_enviada($info[0]['identificacion']);
            if($send){
                array_push($resp,[
                    'nombre_completo'=> "Encuesta de Entrenamiento",
                    'descripcion'=> '1 Encuesta por gestionar.',
                    'accion' => "window.open('".base_url()."index.php/talento_cuc/encuesta_entrenamiento/$id_persona')"]);
            }

            $acta = $this->talento_cuc_model->estado_entrenamiento($id_persona, $info[0]['id_cargo_sap']);
            if($acta->{'acta_enviada'} == 1 && $acta->{'firma_fun'} == null && $acta->{'terminado'} == 0){
                array_push($resp,[
                    'nombre_completo'=> "Acta de Aceptación de Cargo",
                    'descripcion'=> '1 Acta por confirmar.',
                    'accion' => "window.open('".base_url()."index.php/talento_cuc/acta_cargo/$id_persona')"]);
            }

            $actas = $this->talento_cuc_model->listar_actas_personas($id_persona); 
            if($actas[2] > 0 ){
                array_push($resp,[
                    'nombre_completo'=> "Actas de Aceptación de Cargo",
                    'descripcion' => $actas[2]." Acta(s) por firmar como Jefe Inmediato",
                    'accion' => "window.open('".base_url()."index.php/talento_cuc/validar_actas_entrenamiento/$id_persona')"]);
            } 

            if($this->administra){
                $form = $this->talento_cuc_model->listar_asistencias_formacion(1); 
                if($form){
                    array_push($resp,[
                        'nombre_completo'=> "Plan de formación",
                        'descripcion' => count($form)." Asistencia(s) de Formación",
                        'accion' => "ver_asistencias_formacion(1,{})"]);
                } 
            }
            
		}
		echo json_encode($resp);
    }

    public function obtener_info_persona(){
        $resp = [];
		if ($this->Super_estado) {
            $id_persona = $this->input->post("idpersona");
            $identificacion = $this->input->post("identificacion");
			$data = $this->talento_cuc_model->obtener_info_persona($id_persona, $identificacion);        
            $resp = $data ? $data[0] : [];
		}
		echo json_encode($resp);
    }

    public function get_resultados_detalles(){
        $data = array();
		if ($this->Super_estado) {
			$id_solicitud = $this->input->post('id_solicitud');
			$data = $this->talento_cuc_model->get_resultados_detalles($id_solicitud);  
		}
		echo json_encode($data);
    }

    public function get_resultados_tipoevaluador(){
        $data = array();
		if ($this->Super_estado) {
			$id_solicitud = $this->input->post('id_solicitud');
			$data = $this->talento_cuc_model->get_resultados_tipoevaluador($id_solicitud);  
		}
		echo json_encode($data);
    }

    public function obtener_resultado_evaluacion(){
        if (!$this->Super_estado) $resp = array();
		else {        
            $id_solicitud = $this->input->post('id_solicitud');
            $puntuacion_directa = 0;
            $puntuacion_centil = 0;
            $valoracion = '';
            $resultado = $this->talento_cuc_model->traer_registro_id($id_solicitud, 'evaluacion_resultado_final', 'id_solicitud'); 
            if($resultado){
                $puntuacion_directa = $resultado->{'puntuacion_directa'};
                $puntuacion_centil = $resultado->{'puntuacion_centil'};
                $valoracion = $resultado->{'valoracion'};
            }
            $resp = ['puntuacion_directa'=> $puntuacion_directa, 'puntuacion_centil' => $puntuacion_centil, 'valoracion' => $valoracion];
        }
        echo json_encode($resp);
    }

    public function buscar_persona(){
		$personas = array();
		if ($this->Super_estado) {
			$dato = $this->input->post('dato');
			$buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1";
			if (!empty($dato)) $personas = $this->talento_cuc_model->buscar_persona($buscar);  
		}
		echo json_encode($personas);
	}

    public function eliminar_datos() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if (!$this->Super_elimina) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{            
                $id = $this->input->post("id");
                $tabla = $this->input->post("tabla_bd");
                $id_usuario_elimina = $_SESSION['persona']; 
                if (empty($id)) {
                    $resp = ['mensaje'=>"Error al cargar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                }else{
                  if($tabla == 'valor_parametro'){
                    $data = ['estado' => 0];
                  }else{
                    $data = ['fecha_elimina' => date("Y-m-d H:i:s"),'id_usuario_elimina' => $id_usuario_elimina,'estado' => 0,];
                  }
                    $query = $this->pages_model->modificar_datos($data, $tabla ,$id);
                    $resp= ['mensaje'=>"Los datos fueron eliminados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if($query == -1) $resp= ['mensaje'=>"Error al eliminar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }  
            }
        }
         echo json_encode($resp);
    }

    public function avalar_soporte(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
				$id = $this->input->post('id');
				$observacion = $this->input->post('observacion');
				$nextState = $this->input->post('nextState');
				$success = $this->input->post('success');
				$data = ['estado_apro' => $nextState, 'observacion' => $observacion];
                $mod = $this->pages_model->modificar_datos($data, 'talentocuc_soportes_plan_formacion', $id);
                if ($mod) $resp = ['mensaje' => $success, 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
                else $resp = ['mensaje' => 'Se presentó un error al avalar soporte. Por favor contacte con el administrador del sistema.','tipo' => 'error','titulo' => 'Ooops'];
			} else $resp = ['mensaje' => 'No tiene permisos para gestionar.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
        }
        echo json_encode($resp);
    }

    public function avalar_soportes_masivo(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
				$soportes = $this->input->post('id');
				$nextState = $this->input->post('nextState');
				$success = $this->input->post('success');
				$data = ['estado_apro' => $nextState];
                $sw = true;
                foreach ($soportes as $row){
                    $mod = $this->pages_model->modificar_datos($data, 'talentocuc_soportes_plan_formacion', $row['id']);
                    if(!$mod)$sw = false;
                }
                if ($sw) $resp = ['mensaje' => $success, 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
                else $resp = ['mensaje' => 'Se presentó un error al avalar soporte. Por favor contacte con el administrador del sistema.','tipo' => 'error','titulo' => 'Ooops'];
			} else $resp = ['mensaje' => 'No tiene permisos para gestionar.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
        }
        echo json_encode($resp);
    }

    public function competencias_Oportunidades($data,$filtro=null){
        $resp = [];
        if(!$filtro){
            foreach ($data as $row){                    
                if($row['mejora'] == 1) array_push($resp, $row);
            }
        }else{
            foreach ($data as $row){                    
                if($row['tipo'] == 0) array_push($resp, $row);
            }
        }
        return $resp;
    }

    public function calcular_planFormacion(){       
        if (!$this->Super_estado) $resp_final = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp_final = [];
            $id_solicitud = $this->input->post('id_solicitud');
            $row = $this->talento_cuc_model->get_solicitud($id_solicitud);
            if($row->{'id_estado_eval'} == 'Eval_Form'){
                $resp_final = $this->talento_cuc_model->listar_detalle_resultados($row->{'id_evaluado'},1);
            }else $resp_final = $this->get_competencia_formacion($row->{'id_evaluado'},$id_solicitud); 
		}
		echo json_encode($resp_final); 
    }

    public function get_competencia_formacion($id_evaluado, $id_solicitud){
        $resp_final = [];
        $data = $this->talento_cuc_model->listar_detalle_resultados($id_evaluado,0,$id_solicitud);
        if($data){
            $resp_op = $this->competencias_Oportunidades($data); 
            $resp_baja = $this->array_sort_by($data, 'puntaje', $order = SORT_ASC, 1);
            $val = $this->talento_cuc_model->traer_registro_id('Opt_Mej', 'valor_parametro', 'id_aux');         
            foreach ($resp_op as $row){ // unir marcadas como oportunidad y puntaje bajo
                $sw = array_search($row['id_competencia'], array_column($resp_baja, 'id_competencia'));
                if($sw >= 0) $row['observaciones'] = 'Oportunidad de Mejora y Puntaje Bajo';
                else $row['observaciones'] = 'Oportunidad de Mejora';  
                $row['hora_formacion'] = $val->{'valorx'};
                array_push($resp_final, $row);
            }

            foreach ($resp_baja as $row){ // marcadas como baja que no se encuentran en array final     
                $sw = array_search($row['id_competencia'], array_column($resp_final, 'id_competencia'));              
                if($sw === false) array_push($resp_final, $row);
            } 
        }
        return $resp_final;
    }

    public function generar_formacion_masiva(){
        $resp = [];
        if (!$this->Super_estado) $resp;
		else {
            $data = $this->talento_cuc_model->listar_personas('','','','','');
            foreach ($data as $row){                    
                if($row['estado_eval'] != 'Eval_Form'){                    
                    $resp_final = $this->get_competencia_formacion($row['identificacion']);
                    if(!empty($resp_final)) array_merge($resp, $resp_final);
                }
            }
        }  
        echo json_encode($resp);     
    }

    public function guardar_plan_formacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_solicitud = $this->input->post('id_solicitud');
                $data_formacion = $this->input->post('data_formacion');
                // $info = $this->talento_cuc_model->traer_registro_id($id_solicitud, 'evaluacion_solicitud', 'id');
                // $form_actual = $this->preparar_data_formacion($info->{'id_evaluado'});
                // $i = 0;
                // $h = 0;
                $sw = true;
                // if(count($form_actual[0]) > 0){
                //     //calcular horas de periodo ant
                //     foreach ($form_actual[0] as $row){
                //         $i++;
                //         if($row['hora_formacion'] > $row['tiempo']){
                //             $h = ($row['hora_formacion'] - $row['tiempo']);
                //             $x = array_search($row['id_competencia'], array_column($data_formacion, 'id_competencia'));
                //             if($x >= 0){
                //                  $h = $h + $data_formacion[$x]['hora_formacion'];
                //                  $data = ['id_solicitud' => $id_solicitud, 'observaciones' => $data_formacion[$x]['observaciones'].' y '.$h.' horas pendientes periodo anterior.', 'hora_formacion' => $h, 'id_anterior' => $row['id_solicitud']]; 
                //                  $add = $this->pages_model->modificar_datos($data, 'evaluacion_resultado_competencia', $data_formacion[$x]['id']); 
                //                  $mod = $this->pages_model->modificar_datos(['estado' => 0], 'evaluacion_resultado_competencia', $row['id']); 
                //             }else{
                //                   $data = ['id_solicitud' => $id_solicitud, 'observaciones' => $h.' horas pendientes periodo anterior.', 'hora_formacion' => $h, 'id_anterior' => $row['id_solicitud']]; 
                //                   $add = $this->pages_model->modificar_datos($data, 'evaluacion_resultado_competencia', $row['id']); 
                //             }
                //         }
                //         if(!$add) $sw = false;
                //     }
                //     if(count($form_actual[0]) == $i){
                //         //se guarda las que no existan en formacion actual
                //         foreach ($data_formacion as $row){
                //             $x = array_search($row['id_competencia'], array_column($form_actual[0], 'id_competencia'));
                //             if(!($x >= 0)){
                //                 $data = ['estado_formacion' => 1, 'observaciones' => $row['observaciones'], 'hora_formacion' => $row['hora_formacion']];               
                //                 $add = $this->pages_model->modificar_datos($data, 'evaluacion_resultado_competencia', $row['id']);
                //                 if(!$add) $sw = false;
                //             }
                //         }
                //     }
                // }else{
                    // guadar periodo nuevo
                    foreach ($data_formacion as $row){
                        $data = ['estado_formacion' => 1, 'observaciones' => $row['observaciones'], 'hora_formacion' => $row['hora_formacion']];               
                        $add = $this->pages_model->modificar_datos($data, 'evaluacion_resultado_competencia', $row['id']);
                        if(!$add) $sw = false;
                    }
                // }
                if($sw){
                    $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'estado_eval' => 'Eval_Form'];
                    $data_estado = ['id_estado_eval' => 'Eval_Form'];
                    $mod_estado = $this->pages_model->modificar_datos($data_estado, 'evaluacion_solicitud', $id_solicitud);
                    if($mod_estado){
                        $data = [
                            'solicitud_id' => $id_solicitud,
                            'estado_id' => 'Eval_Form',
                            'id_usuario_registra' => $_SESSION['persona'],
                          ];
                          $res_estado = $this->pages_model->guardar_datos($data, 'evaluacion_estado_solicitudes');
                    }else $resp = ['mensaje'=>"Error al guardar estado de la evaluación, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!", 'estado_eval' => '']; 
                }else $resp = ['mensaje'=>"Error al guardar la Información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!", 'estado_eval' => '']; 
            }
        }
        echo json_encode($resp);
    }

    public function listar_planFormacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = array();
            $fecha_i = $this->input->post('fecha_i');
            $fecha_f = $this->input->post('fecha_f');
            $texto = $this->input->post('texto');
            $id_lugar = $this->input->post('id_lugar');
            $btn_mod = '<span title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-edit btn btn-default modificar red"></span>';
            $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
            $btn_competencias = '<span title="Agregar competencias" data-toggle="popover" data-trigger="hover" style="color: #5cb85c;" class="pointer fa fa-cogs btn btn-default competencias"></span>';
            $btn_link = '<span title="Generar Link" data-toggle="popover" data-trigger="hover" class="red pointer fa fa-link btn btn-default get_link"></span>';
            $btn_finalizar = '<span title="Finalizar Formación" data-toggle="popover" data-trigger="hover" style="color: #5cb85c;" class="red pointer fa fa-check btn btn-default finalizar"></span>';
            $btn_actualizar = '<span title="Actualizar link" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;" class="red pointer fa fa-refresh btn btn-default actualizar"></span>';
            $btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            $data = $this->talento_cuc_model->listar_planFormacion($fecha_i, $fecha_f, $texto, $id_lugar);
            foreach ($data as $row) {
                if($row['estado_encuesta'] == 2)$row['gestion'] = $btn_inhabil;
                else{
                    if($row['estado_link'] == 1){
                        $row['gestion'] = $btn_link.' '.$btn_actualizar.' '.$btn_finalizar;
                    }else $row['gestion'] = $btn_link.' '.$btn_competencias .' '. $btn_mod.' '.$btn_eliminar;
                }
                array_push($resp,$row);               
            }
        }
		echo json_encode($resp);
    }

    public function get_competencia(){
        $id_formacion = $this->input->post("id_formacion");
        $resp = $this->Super_estado == true ? $this->talento_cuc_model->get_competencia($id_formacion) : array();
        echo json_encode($resp);
    }

    public function habilitar_permiso(){
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id_valor_parametro = $this->input->post("id_valor_parametro");
            $id_formacion = $this->input->post("id_formacion");
            $resp = ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Permiso Asignado.!"];
            $data = [
                'id_competencia' => $id_valor_parametro,
                'id_formacion' => $id_formacion,
                'id_usuario_registra' => $_SESSION['persona'],
            ];
            $add = $this->pages_model->guardar_datos($data,'talentocuc_formacion_competencia');
            if($add != 1) $resp= ['mensaje'=>"Error al asignar competencia, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }
      echo json_encode($resp);
    }

    public function deshabilitar_permiso(){
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id_permiso = $this->input->post("id_permiso");
            if (empty($id_permiso)) {
                $resp= ['mensaje'=>"Seleccione el permiso a Deshabilitar",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $fecha_elimina = date("Y-m-d H:i:s");
                $data_eval = [
                    'estado' => 0,
                    'fecha_elimina' => $fecha_elimina,
                    'id_usuario_elimina' => $_SESSION['persona'],
                ];                        
                $mod = $this->pages_model->modificar_datos($data_eval,'talentocuc_formacion_competencia', $id_permiso);
                $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Permiso Deshabilitado.!"];
                if(!$mod) $resp= ['mensaje'=>"Error al Deshabilitado competencia, contacte con el administrador",'tipo'=>"error",'titulo'=> "Oops.!"];          
            }  
        }
      echo json_encode($resp);
    }

    public function guardar_planformacion_gen(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $funcionario = $this->input->post('funcionario');
                $id_lugar = $this->input->post('id_lugar'); 
                $tema = $this->input->post('tema');
                $duracion = $this->input->post('duracion');
                $fecha_formacion = $this->input->post('fecha_formacion');
                $str = $this->verificar_campos_string(['Funcionario' => $funcionario, 'Lugar' => $id_lugar, 'Tema' => $tema, 'Duración' => $duracion, 'Fecha Formación' => $fecha_formacion]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data = [
                        'funcionario' => $funcionario,                        
                        'tema' => $tema,
                        'id_lugar' => $id_lugar,
                        'duracion' => $duracion,
                        'fecha_formacion' => $fecha_formacion,
                        'id_usuario_registra' => $_SESSION['persona'],
                    ];
                    $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                    $add = $this->pages_model->guardar_datos($data, 'talentocuc_plan_formacion');
                    if($add != 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];                
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_plan_formacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id = $this->input->post('id');
                $funcionario = $this->input->post('funcionario');
                $id_lugar = $this->input->post('id_lugar'); 
                $tema = $this->input->post('tema');
                $duracion = $this->input->post('duracion');
                $fecha_formacion = $this->input->post('fecha_formacion');
                $info = $this->talento_cuc_model->traer_registro_id($id, 'talentocuc_plan_formacion', 'id');         
                $str = $this->verificar_campos_string(['Funcionario' => $funcionario, 'Tema' => $tema, 'Duración' => $duracion, 'Fecha Formación' => $fecha_formacion, 'Lugar' => $id_lugar]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    if($info->{'id_lugar'} == $id_lugar && $info->{'tema'} == $tema && $info->{'duracion'} == $duracion && $info->{'fecha_formacion'} == $fecha_formacion && $info->{'funcionario'} == $funcionario){
                        $resp = ['mensaje'=>"Debe realizar alguna modificación!.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $data = [
                            'funcionario' => $funcionario,                        
                            'tema' => $tema,
                            'id_lugar' => $id_lugar,
                            'duracion' => $duracion,
                            'fecha_formacion' => $fecha_formacion,
                            'id_usuario_registra' => $_SESSION['persona'],
                        ];
                        $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                        $add = $this->pages_model->modificar_datos($data, 'talentocuc_plan_formacion', $id);
                        if(!$add) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];  
                    }              
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_plaformacion_personal(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_persona = $this->input->post('id_persona');
            $id_competencia = $this->input->post('id_competencia');
            $data = $this->talento_cuc_model->listar_plaformacion_personal($id_persona, $id_competencia);
            $btn_sinasistencia = '<span title="Sin Asistencia" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            $btn_asistencia = '<span style="color: #5cb85c"><span title="Asistencia" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-on"></span></span>';
            foreach ($data as $row) {
                $row['asistencia'] = $row['id_asistencia'] != null ? $btn_asistencia : $btn_sinasistencia;
                array_push($resp,$row);               
            }
        }
		echo json_encode($resp);
    }

    public function listar_plan_entrenamiento(){
        $resp = [];
        $sw = false;
        $i=0;
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $id_persona = $this->input->post('idpersona');
            $data = $this->talento_cuc_model->listar_plan_entrenamiento($id_persona);
            $btn_mod = '<span title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-edit btn btn-default modificar red"></span>';
            $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
            $btn_inhabil = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            $btn_link = '<span title="Link del entrenamiento" data-toggle="popover" data-trigger="hover" class="red pointer fa fa-link btn btn-default get_link"></span>';
            foreach ($data as $row) {
                // $row['gestion'] = $row['enviado'] ? ($row['asistencia'] ? $btn_inhabil : $btn_link) : $btn_link.' '. $btn_mod .' '. $btn_eliminar;
                $row['gestion'] = $row['asistencia'] == 1 ? $btn_inhabil : $btn_link.' '. $btn_mod .' '. $btn_eliminar;
                // if($row['asistencia'] == 0) $i++;
                array_push($resp,$row);               
            }
            // $sw = $i > 0 ? true : false;
        }
		echo json_encode($resp);
    }

    public function guardar_encuesta_entrenamiento_general(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_persona = $_SESSION['persona'];
            $answer_1 = $this->input->post('answer_1');
            $answer_2 = $this->input->post('answer_2');
            $calificacion = $this->input->post('calificacion');
            $observaciones = $this->input->post('sugerencias');
            $str = $this->verificar_campos_numericos(['Calificación' => $calificacion]);
            if($answer_1 == ''){
                $resp = ['mensaje'=>"Debe responder: Los conocimientos que adquirió durante este proceso han sido suficientes para desempeñar su cargo?", 'tipo'=>"info", 'titulo'=> "Oops.!"];
            }else if($answer_2 == ''){    
                $resp = ['mensaje'=>"Debe responder: Considera necesario recibir inducción/entrenamiento al cargo nuevamente?", 'tipo'=>"info", 'titulo'=> "Oops.!"];
            }else if (is_array($str)) {
                $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
            }else{
                $info = $this->talento_cuc_model->obtener_info_persona($id_persona);
                $data_enc = [
                    'id_evaluado' => $info[0]['identificacion'], 
                    'respuesta1' => $answer_1, 
                    'respuesta2' => $answer_2,
                    'puntuacion' => $calificacion, 
                    'observacion' => $observaciones, 
                    'id_usuario_registra' => $id_persona
                ];
                $add = $this->pages_model->guardar_datos($data_enc, 'talentocuc_encuesta_entrenamiento');
                $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'persona_id' => $id_persona];
                if($add){
                    $mod = $this->pages_model->modificar_datos(['encuesta_enviada' => 0], 'talentocuc_plan_entrenamiento', $info[0]['identificacion'], 'id_evaluado');
                }else ['mensaje'=>"Error al guardar la encuesta, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];  
            }      
        }
		echo json_encode($resp);
    }

    public function guardar_asistencia_entrenamiento(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_entrenamiento = $this->input->post('id_entrenamiento');
            $id_evaluado = $this->input->post('id_persona');
            $calificacion = $this->input->post('calificacion');
            $sugerencias = $this->input->post('sugerencias');
            $str = $this->verificar_campos_numericos(['Calificación' => $calificacion]);
            if (is_array($str)) {
                $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
            }else{
                $data = [
                    'id_evaluado' => $id_evaluado, 
                    'asistencia' => 1,
                    'calificacion' => $calificacion, 
                    'sugerencias' => $sugerencias, 
                    'id_usuario_registra' => $_SESSION['persona']
                ];
                $add = $this->pages_model->modificar_datos($data, 'talentocuc_plan_entrenamiento', $id_entrenamiento);
                $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                if(!$add)['mensaje'=>"Error al guardar la encuesta, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];  
            }      
        }
		echo json_encode($resp);
    }

    public function guardar_planentrenamiento(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_evaluado = $this->input->post('id_evaluado');
                $facilitador = $this->input->post('facilitador');
                $id_lugar = $this->input->post('id_lugar'); 
                $link_reunion = $this->input->post('link_reunion');
                $duracion = $this->input->post('duracion');
                $fecha_entrenamiento = $this->input->post('fecha_entrenamiento');
                $id_oferta = $this->input->post('id_oferta');
                $str = $this->verificar_campos_string(['Facilitador' => $facilitador, 'Lugar' => $id_lugar, 'Link' => $link_reunion, 'Duración' => $duracion, 'Fecha Entrenamiento' => $fecha_entrenamiento]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data = [
                        'id_evaluado' => $id_evaluado,
                        'id_funcionario' => $facilitador,                        
                        'link' => $link_reunion,
                        'id_lugar' => $id_lugar,
                        'duracion' => $duracion,
                        'fecha_entrenamiento' => $fecha_entrenamiento,
                        'id_oferta' => $id_oferta,
                        'id_usuario_registra' => $_SESSION['persona'],
                    ];
                    
                    $add = $this->pages_model->guardar_datos($data, 'talentocuc_plan_entrenamiento');                    
                    if($add != 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];  
                    else{
                        $plan = $this->talento_cuc_model->traer_registro_id($id_evaluado, 'talentocuc_plan_entrenamiento', 'id_evaluado');  
                        $info = $this->talento_cuc_model->listar_plan_entrenamiento($id_evaluado,$plan->{'id'});  
                        $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'data_entrenamiento' => $info[0]];
                    }              
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_plan_entrenamiento(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id = $this->input->post('id');
                $facilitador = $this->input->post('facilitador');
                $id_lugar = $this->input->post('id_lugar'); 
                $link_reunion = $this->input->post('link_reunion');
                $duracion = $this->input->post('duracion');
                $fecha_entrenamiento = $this->input->post('fecha_entrenamiento');
                $id_oferta = $this->input->post('id_oferta');
                $info = $this->talento_cuc_model->traer_registro_id($id, 'talentocuc_plan_entrenamiento', 'id');         
                $str = $this->verificar_campos_string(['Facilitador' => $facilitador, 'Link' => $link_reunion, 'Duración' => $duracion, 'Fecha Entrenamiento' => $fecha_entrenamiento, 'Lugar' => $id_lugar, 'Oferta de Entrenamiento' => $id_oferta]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    if($info->{'id_lugar'} == $id_lugar && $info->{'link'} == $link_reunion && $info->{'duracion'} == $duracion && $info->{'fecha_entrenamiento'} == $fecha_entrenamiento && $info->{'id_funcionario'} == $facilitador && $info->{'id_oferta'} == $id_oferta){
                        $resp = ['mensaje'=>"Debe realizar alguna modificación!.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $data = [
                            'id_funcionario' => $facilitador,                        
                            'link' => $link_reunion,
                            'id_lugar' => $id_lugar,
                            'duracion' => $duracion,
                            'fecha_entrenamiento' => $fecha_entrenamiento,
                            'id_oferta' => $id_oferta,
                            'id_usuario_registra' => $_SESSION['persona'],
                        ];
                        $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                        $add = $this->pages_model->modificar_datos($data, 'talentocuc_plan_entrenamiento', $id);
                        if(!$add) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];  
                    }              
                }
            }
        }
        echo json_encode($resp);
    }

    public function obtener_valor_parametros(){
        $parametro = $this->input->post('id');
        $permisos = $this->Super_estado == true ? $this->talento_cuc_model->obtener_permisos_parametro($parametro) : array();
        echo json_encode($permisos);
    }

    public function guardar_pregunta(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_clasificacion = $this->input->post('id_clasificacion');
                $pregunta = $this->input->post('pregunta');
                $id_tipo_respuesta = $this->input->post('id_tipo_respuesta');
                $str = $this->verificar_campos_string(['Pregunta' => $pregunta]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $str2 = $this->verificar_campos_numericos(['Clasificación' => $id_clasificacion,'Tipo Respuesta' => $id_tipo_respuesta]);
                    if (is_array($str2)) {
                        $resp = ['mensaje'=>"El campo ". $str2['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $data = ['idparametro' => 243, 'valor' => $pregunta, 'valorz' => $id_tipo_respuesta, 'valora' => $id_clasificacion, 'usuario_registra' => $_SESSION['persona']];
                        $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                        $add = $this->pages_model->guardar_datos($data, 'valor_parametro');
                        if(!$add) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    } 
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_pregunta(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_pregunta = $this->input->post('id_pregunta');
                $id_clasificacion = $this->input->post('id_clasificacion');
                $pregunta = $this->input->post('pregunta');
                $id_tipo_respuesta = $this->input->post('id_tipo_respuesta');
                $info = $this->talento_cuc_model->traer_registro_id($id_pregunta, 'valor_parametro', 'id');
                $str = $this->verificar_campos_string(['Pregunta' => $pregunta]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                     $str2 = $this->verificar_campos_numericos(['Clasificación' => $id_clasificacion,'Tipo Respuesta' => $id_tipo_respuesta]);
                    if (is_array($str2)) {
                        $resp = ['mensaje'=>"El campo ". $str2['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        if($info->{'valor'} == $pregunta && $info->{'valorz'} == $id_tipo_respuesta && $info->{'valora'} == $id_clasificacion ){
                            $resp = ['mensaje'=>"Debe realizar alguna modificación!.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else{
                            $data = ['valor' => $pregunta, 'valorz' => $id_tipo_respuesta, 'valora' => $id_clasificacion ];
                            $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                            $add = $this->pages_model->modificar_datos($data, 'valor_parametro', $id_pregunta);
                            if(!$add) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                        }
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_valor_parametro(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_parametro = $this->input->post('id_parametro');
            $selec = $id_parametro == 243 ? "vp.id, vp.valor, tr.valor valorx, tp.valor valora, vp.valorz id_tipo_respuesta, vp.valora id_clasificacion" : "vp.*";
            $data = $this->talento_cuc_model->listar_valor_parametro($id_parametro, $selec);
            $btn_mod = '<span title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-edit btn btn-default modificar red"></span>';
            $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
            $btn_inhabil = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            foreach ($data as $row) {
                $row['accion'] = $id_parametro == 241 ? $btn_mod : $btn_mod .' '. $btn_eliminar;
                array_push($resp,$row);               
            }
        }
		echo json_encode($resp);
    }

    public function obtener_preguntas_encuesta(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_parametro = $this->input->post('id_parametro');
            $selec = "vp.id id_pregunta, vp.valor pregunta, vp.valorz id_tipo_respuesta, vp.valora id_tipo_pregunta";
            $data = $this->talento_cuc_model->listar_valor_parametro($id_parametro, $selec);
            foreach ($data as $row) {
                $row['id_respuesta'] = '';               
                array_push($resp,$row);               
            }
        }
		echo json_encode($resp);
    }

    public function modificar_horas(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id = $this->input->post('id');
                $valor = $this->input->post('valor_nuevo');
                $id_parametro = $this->input->post('id_parametro');
                $info = $this->talento_cuc_model->traer_registro_id($id, 'valor_parametro', 'id');
                if($info->{'valorx'} == $valor ){
                    $resp = ['mensaje'=>"Debe realizar alguna modificación!.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data = ['valorx' => $valor ];
                    $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                    $add = $this->pages_model->modificar_datos($data, 'valor_parametro', $id);
                    if(!$add) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                }
            }
        }
        echo json_encode($resp);
    }

    public function guardar_asistencia(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $data_preguntas = $this->input->post('data_pregunta');
                $asistencia = $this->input->post('answer_asistencia');
                $sugerencias = $this->input->post('sugerencias');
                $id_formacion = $this->input->post('id_formacion');
                $id_pregunta = '';
                $data_respuestas = [];
                $p = $this->talento_cuc_model->traer_registro_id($_SESSION['persona'], 'personas', 'id');
                $id_persona = $p->{'identificacion'};
                $sw = true;
                foreach ($data_preguntas as $row) {
                    if($row['id_respuesta'] == ''){
                            $sw = false;
                            $id_pregunta = $row['id_pregunta'];
                        break;
                    }                       
                }      
                if(!$sw){
                    $q = $this->talento_cuc_model->traer_registro_id($id_pregunta, 'valor_parametro', 'id');
                    $resp = ['mensaje'=>"No ha respondido:". $q->{'valor'},'tipo'=>"info",'titulo'=> "Oops.!"];
                }else{
                    $periodo = $this->talento_cuc_model->get_periodo_actual();
                    $data = [
                        'id_persona' => $id_persona,
                        'id_formacion' => $id_formacion,
                        'acepta_politicas' => 1,
                        'sugerencias' => $sugerencias,
                        'periodo' => $periodo,
                        'id_usuario_registra' => $_SESSION['persona']
                    ];
                    $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                    $add = $this->pages_model->guardar_datos($data, 'talentocuc_asistencia_formacion');
                    if($add){
                        $asistencia = $this->talento_cuc_model->traer_registro_id($id_persona, 'talentocuc_asistencia_formacion', 'id_persona');
                        foreach ($data_preguntas as $row) {
                            $dato['id_asistencia'] = $asistencia->{'id'};
                            $dato['id_pregunta'] = $row['id_pregunta'];
                            $dato['id_tipo_respuesta'] = $row['id_tipo_respuesta'];
                            $dato['id_tipo_pregunta'] = $row['id_tipo_pregunta'];
                            $dato['id_respuesta'] = $row['id_respuesta'];
                            $dato['id_usuario_registra'] = $_SESSION['persona']; 
                            array_push($data_respuestas,$dato);  
                        }
                        $add = $this->pages_model->guardar_datos($data_respuestas, 'talentocuc_encuesta_satisfaccion', 2);
                    }else $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function obtener_observacion_perfil_persona(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_persona = $this->input->post('idpersona');
            $data = $this->talento_cuc_model->obtener_observacion_perfil_persona($id_persona);
            foreach ($data as $row) {
                $row['state'] = $this->administra ||  $id_persona == $_SESSION['persona'] ? '' : 'oculto';            
                array_push($resp,$row);            
            }
        }
		echo json_encode($resp);
    }

    public function listar_plaformacion_persona_hv(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $tiempo = 0;
            $id_persona = $this->input->post('idpersona');
            $data = $this->talento_cuc_model->listar_detalle_resultados($id_persona,1);
            $btn_adjuntar = '<span title="Adjuntar soporte" data-toggle="popover" data-trigger="hover" class="fa fa-upload btn btn-default adjuntar red"></span>';
            $btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            $btn_espera   = '<span title="Esperando" data-toggle="popover" data-trigger="hover" style="color: #5bc0de" class="btn fa fa-hourglass-half"></span>';            
            $btn_certificado   = '<span title="Descargar Certificado" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn fa fa-user-graduate certificado"></span>';            
            foreach ($data as $row) {
                $horas = $this->talento_cuc_model->get_tiempo_formacion($id_persona, $row['id_competencia']);
                $tiempo = $horas ? $horas->{'tiempo'} : 0;
                if(!$tiempo){
                    $bg_color = 'white';
                    $color = 'black';
                    $row['tiempo_realizado'] = $btn_espera; 
                    $row['accion'] = $btn_inhabil;
                }else{
                     if($tiempo < $row['hora_formacion']){
                        $bg_color = '#337ab7';
                        $color = 'white';
                        $row['tiempo_realizado'] = $tiempo;
                        $row['accion'] = $btn_adjuntar;
                     }else{
                        $bg_color = '#5cb85c';
                        $color = 'white';
                        $row['tiempo_realizado'] = $tiempo;
                        $row['accion'] = $btn_adjuntar;  
                     }
                }                
                $row['id_formacion'] =  $horas ? $horas->{'id_formacion'} : 0;
                $row['ver'] = "<span  style='background-color: $bg_color;color: $color; width: 100%;' class='pointer form-control'><span >ver</span></span>";          
                array_push($resp,$row);            
            }
        }
		echo json_encode($resp);
    }

    public function guardar_observacion_perfil_persona(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_persona = $this->input->post('id_persona');
                $observacion = $this->input->post('observacion');
                $obs = [];
                $str = $this->verificar_campos_string(['Observación' => $observacion]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data = [
                        'id_persona' => $id_persona, 
                        'observacion' => $observacion,
                        'id_usuario_registra' => $_SESSION['persona']];                    
                    $add = $this->pages_model->guardar_datos($data, 'observacion_perfil_persona');
                    $obs = $this->talento_cuc_model->obtener_observacion_perfil_persona($id_persona);
                    $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'observaciones' => $obs];
                    if(!$add) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!",'observaciones' => $obs];
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_observacion_perfil_persona(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id = $this->input->post('id');
                $observacion = $this->input->post('valor_nuevo');
                $obs = [];
                $str = $this->verificar_campos_string(['Observación' => $observacion]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $row = $this->talento_cuc_model->traer_registro_id($id, 'observacion_perfil_persona', 'id');
                    if($row->{'observacion'} == $observacion ){
                        $resp = ['mensaje'=>"Debe realizar alguna modificación.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $data = ['observacion' => $observacion];
                        $add = $this->pages_model->modificar_datos($data, 'observacion_perfil_persona', $id);
                        $obs = $this->talento_cuc_model->obtener_observacion_perfil_persona($row->{'id_persona'});
                        $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'observaciones' => $obs];
                        if(!$add) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!",'observaciones' => $obs];
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function generar_link(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id = $this->input->post('id_formacion');
                $tiempo = $this->input->post('minutos');
                $fecha_actual = date('Y-m-d H:i:s');
                $fecha_cierre = strtotime('+'.$tiempo.'minute', strtotime($fecha_actual));
                $fecha_cierre = date('Y-m-d H:i:s', $fecha_cierre);
                $data = ['estado_link' => 1, 'fecha_cierre_link' => $fecha_cierre];
                $add = $this->pages_model->modificar_datos($data, 'talentocuc_plan_formacion', $id);
                $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                if(!$add) $resp = ['mensaje'=>"Error al generar link, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }
        }
        echo json_encode($resp);
    }

    public function finalizar_formacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id = $this->input->post('id_formacion');
                $data = ['estado_encuesta' => 2];
                $add = $this->pages_model->modificar_datos($data, 'talentocuc_plan_formacion', $id);
                $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                if(!$add) $resp = ['mensaje'=>"Error al finalizar plan de formación, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }
        }
        echo json_encode($resp);
    }

    public function obtener_formacion_academica(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $idpersona = $this->input->post('idpersona');
            $data = $this->talento_cuc_model->obtener_formacion_academica($idpersona);
            foreach ($data as $row) {
                $row['state'] = $this->administra ||  $idpersona == $_SESSION['persona'] ? '' : 'oculto';            
                array_push($resp,$row);            
            }
        }
		echo json_encode($resp);
    }

    public function listar_soportes_formacion_academica(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_formacion = $this->input->post('id_formacion');
            // $btn_ver = '<a target="_blank" class="ver_adjunto"><span title="Ver" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default red"></span></a>';
            // $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;" class="pointer fa fa-trash btn btn-default eliminar"></span>';            
            $data = $this->talento_cuc_model->listar_soportes_formacion_academica($id_formacion);
            foreach ($data as $row) {
                $btn_ver = "<a target='_blank' class='btn btn-secondary ver_adjunto ver_adjunto_sop".$row['id']."' title='Descargar'><span class='fa fa-download'></span></a>";
                $btn_eliminar = "<span class='btn btn-danger eliminar eliminar_sop".$row['id']."' title='Eliminar'><span class='fa fa-trash'></span></span>";
                $row['accion'] = $btn_ver. ' '. $btn_eliminar;            
                array_push($resp,$row);            
            }
        }
		echo json_encode($resp);
    }

    public function guardar_formacion_academica(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_persona = $this->input->post('id_persona');
                $id_formacion = $this->input->post('id_formacion');
                $nombre = $this->input->post('nombre');
                $formacion = [];
                $str = $this->verificar_campos_string(['Nombre' => $nombre]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $str2 = $this->verificar_campos_numericos(['Formación' => $id_formacion,]);
                    if (is_array($str2)) {
                        $resp = ['mensaje'=>"El campo ". $str2['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $data = [
                            'id_persona' => $id_persona, 
                            'id_tipo_formacion' => $id_formacion, 
                            'nombre' => $nombre,
                            'id_usuario_registra' => $_SESSION['persona']];                        
                        $add = $this->pages_model->guardar_datos($data, 'formacion_academica_personas');
                        $formacion = $this->talento_cuc_model->obtener_formacion_academica($id_persona);
                        $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!",'formacion' => $formacion];
                        if(!$add) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!",'formacion' => $formacion];
                    } 
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_formacion_academica(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id = $this->input->post('id');
                $id_formacion = $this->input->post('id_formacion');
                $nombre = $this->input->post('nombre');
                $formacion = [];
                $str = $this->verificar_campos_string(['Nombre' => $nombre]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $str2 = $this->verificar_campos_numericos(['Formación' => $id_formacion]);
                    if (is_array($str2)) {
                        $resp = ['mensaje'=>"El campo ". $str2['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $row = $this->talento_cuc_model->traer_registro_id($id, 'formacion_academica_personas', 'id');
                        if( $row->{'id_tipo_formacion'} == $id_formacion && $row->{'nombre'} == $nombre){
                            $resp = ['mensaje'=>"Debe realizar alguna modificación.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else{
                            $data = [
                                'id_tipo_formacion' => $id_formacion, 
                                'nombre' => $nombre];
                            $add = $this->pages_model->modificar_datos($data, 'formacion_academica_personas', $id);
                            $formacion = $this->talento_cuc_model->obtener_formacion_academica($row->{'id_persona'});
                            $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!",'formacion' => $formacion];
                            if(!$add) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!",'formacion' => $formacion];
                        }
                    } 
                }
            }
        }
        echo json_encode($resp);
    }

    public function cargar_archivo($mi_archivo, $ruta, $nombre)
    {
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

    public function guardar_soporte_plan_formacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $nombre = [];
                // $id_formacion = $this->input->post('id_formacion');
                $id_competencia = $this->input->post('id_competencia');
                $id_persona = $this->input->post('id_persona');
                $nombre_formacion = $this->input->post('nombre_formacion');
                $horas_formacion = $this->input->post('horas_formacion');
                $fecha_formacion = $this->input->post('fecha_formacion');
                $link_soporte = $this->input->post('link_soporte');
                $nombre_real = '';
                $nombre_archivo = '';
                $sw = true;
                $periodo = $this->talento_cuc_model->get_periodo_actual();
                $str = $this->verificar_campos_string(['Nombre Formación' => $nombre_formacion, 'Horas Formación' => $horas_formacion, 'Fecha Formación' => $fecha_formacion]);
                if (is_array($str)) {
                    $sw = false;
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $cargo = $this->pages_model->cargar_archivo("soporte_plan_for", $this->ruta_archivos.'/plan_formacion/', "sop");
                    if ($cargo[0] == -1) {
                        if($cargo[1] != "<p>You did not select a file to upload.</p>"){
                            $sw=false;
                            $resp = ['mensaje'=>"Error al cargar archivo, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                        }
                    }else{
                        $nombre_real = $_FILES["soporte_plan_for"]["name"];
                        $nombre_archivo = $cargo[1];
                    }
                    if($nombre_archivo == '' && $link_soporte == ''){
                        $sw=false;
                        $resp = ['mensaje'=>"Debe ingresar algún soporte de formación.!",'tipo'=>"info",'titulo'=> "Oops!"];
                    }
                    if($sw){
                        $data = [
                            // 'id_plan_formacion' => $id_formacion,
                            'id_competencia' => $id_competencia,
                            'id_persona' => $id_persona,
                            'nombre_formacion' => $nombre_formacion,
                            'horas_formacion' => $horas_formacion,
                            'fecha_formacion' => $fecha_formacion,
                            'nombre_real' => $nombre_real,
                            'nombre_archivo' => $nombre_archivo,
                            'link_soporte' => $link_soporte,
                            'periodo' => $periodo,
                            'usuario_registra' => $_SESSION['persona'],
                        ];
                        $info = $this->talento_cuc_model->info_personas_notificar($id_competencia);
                        $resp = ['mensaje'=>"Documento guardado exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!", 'info_correos' => $info];
                        $add = $this->pages_model->guardar_datos($data,'talentocuc_soportes_plan_formacion');
                        if(!$add) $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                    }
                }
                echo json_encode($resp);
            }
        }
    }

    public function modificar_soporte_plan_formacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
                $nombre = [];
                $id = $this->input->post('id_soporte');
                $nombre_formacion = $this->input->post('nombre_formacion');
                $horas_formacion = $this->input->post('horas_formacion');
                $fecha_formacion = $this->input->post('fecha_formacion');
                $link_soporte = $this->input->post('link_soporte');
                $nombre_real = '';
                $sw=true;
                $info = $this->talento_cuc_model->traer_registro_id($id, 'talentocuc_soportes_plan_formacion', 'id');
                $str = $this->verificar_campos_string(['Nombre Formación' => $nombre_formacion, 'Horas Formación' => $horas_formacion, 'Fecha Formación' => $fecha_formacion]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $cargo = $this->pages_model->cargar_archivo("soporte_plan_for", $this->ruta_archivos.'/plan_formacion/', "sop");
                    if ($cargo[0] == -1) {
                        if($cargo[1] != "<p>You did not select a file to upload.</p>"){
                            $sw=false;
                            $resp = ['mensaje'=>"Error al cargar archivo, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                        }else{
                            if (!is_null($info->{'nombre_real'})) {
                                $nombre_real = $info->{'nombre_real'};
                                $nombre_archivo = $info->{'nombre_archivo'};
                            }
                        }
                    }else{
                        $nombre_real = $_FILES["soporte_plan_for"]["name"];
                        $nombre_archivo = $cargo[1];
                    }
                    if($info->{'nombre_formacion'} == $nombre_formacion && $info->{'horas_formacion'} == $horas_formacion && $info->{'fecha_formacion'} == $fecha_formacion && $info->{'link_soporte'} == $link_soporte && $info->{'nombre_real'} == $nombre_real){
                        $sw=false;
                        $resp = ['mensaje'=>"Debe realizar alguna modificación.!",'tipo'=>"info",'titulo'=> "Oops!"];
                    }else if($nombre_archivo == '' && $link_soporte == ''){
                        $sw=false;
                        $resp = ['mensaje'=>"Debe ingresar algún soporte de formación.!",'tipo'=>"info",'titulo'=> "Oops!"];
                    }

                    if($sw){
                        $data = [
                            'nombre_formacion' => $nombre_formacion,
                            'horas_formacion' => $horas_formacion,
                            'fecha_formacion' => $fecha_formacion,
                            'nombre_real' => $nombre_real,
                            'nombre_archivo' => $nombre_archivo,
                            'link_soporte' => $link_soporte,
                        ];
                        $info = $this->talento_cuc_model->info_personas_notificar($info->{'id_competencia'});
                        $resp = ['mensaje'=>"Documento guardado exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!", 'info_correos' => $info];
                        $add = $this->pages_model->modificar_datos($data,'talentocuc_soportes_plan_formacion',$id);
                        if(!$add) $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                    }
                }
                echo json_encode($resp);
            }
        }
    }

    public function listar_soportes_plan_formacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_persona = $this->input->post('id_persona');
            $id_competencia = $this->input->post('id_competencia');
            $btn_ver = '<a target="_blank" class="btn btn-secondary ver" title="Soporte adjunto"><span class="fa fa-download"></span></a>';
            $btn_link = '<a target="_blank" class="btn btn-secondary link" title="Link del soporte"><span class="fa fa-link"></span></a>';
            $btn_eliminar = '<span class="btn btn-danger eliminar" title="Eliminar"><span class="fa fa-trash"></span></span>';
            $btn_mod = '<span class="btn btn-primary editar" title="Editar"><span class="fa fa-edit"></span></span>';
            $btn_espera   = '<span title="Esperando" data-toggle="popover" data-trigger="hover" style="color: #5bc0de" class="btn fa fa-hourglass-half"></span>';
            $data = $this->talento_cuc_model->listar_soportes_plan_formacion($id_persona, $id_competencia);
            foreach ($data as $row) {
                $ver = $row['nombre_real'] ? $btn_ver.' ' : '';
                $link = $row['link_soporte'] ? $btn_link.' ' : '';
                $row['state'] = $row['estado_apro'] > 0 ? ($row['estado_apro'] == 1 ? 'Aprobado' : 'Desaprobado') : 'En espera';
                $row['accion'] = $row['estado_apro'] > 0 ? $ver.$link : $ver.$link. ' '. $btn_mod. ' '. $btn_eliminar;            
                array_push($resp,$row);            
            }
        }
		echo json_encode($resp);
    }

    public function listar_avalar_soportes(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_persona = $this->input->post('identificacion');
            $btn_vistob   = '<span title="Aprobar" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn btn-default fa fa-thumbs-up vistob"></span>';
            $btn_vistom   = '<span title="Desaprobar" data-toggle="popover" data-trigger="hover" style="color: #d9534f" class="btn btn-default fa fa-thumbs-down vistom"></span>';
            $btn_ver = '<a target="_blank" data-toggle="popover" data-trigger="hover" class="btn btn-default ver_soporte" title="Soporte adjunto"><span class="fa fa-download red"></span></a>';
            $btn_link = '<a target="_blank" data-toggle="popover" data-trigger="hover" class="btn btn-default link_soporte" title="Link del soporte"><span class="fa fa-link red"></span></a>';            
            $data = $this->talento_cuc_model->listar_avalar_soportes($id_persona,$this->administra);
            foreach ($data as $row) {
                $ver = $row['nombre_real'] ? $btn_ver.' ' : '';
                $link = $row['link_soporte'] ? $btn_link.' ' : '';
                $row['gestion'] = $btn_vistob. ' '. $btn_vistom;
                $row['ver'] = $ver.' '.$link;
                $row['id_persona'] = $id_persona;
                array_push($resp,$row);
            }
        }
		echo json_encode($resp);
    }

    public function recibir_archivos(){
        $resp = ['mensaje'=>"Todos Los archivos fueron cargados.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
        $nombre = [];
        $id_formacion = $_POST['id_formacion'];
        $id_persona = $_POST['id_persona'];
        $id_usuario_registra= $_SESSION['persona']; 
        $nombre_real = $_FILES["file"]["name"];
        if($id_formacion == 0){
            $tabla = 'personas_soportes';
            $cargo = $this->pages_model->cargar_archivo("file", $this->ruta_archivos.'/otros_soportes/', "sop");
            if ($cargo[0] == -1) {
                header("HTTP/1.0 400 Bad Request");
                echo ($nombre);
                return;
            }
            $row = $this->talento_cuc_model->traer_registro_id($id_persona, $tabla, 'id_persona');
            $data = [
                'nombre_real' => $nombre_real,
                'nombre_archivo' => $cargo[1],
                'id_persona' => $id_persona,
                'usuario_registra' => $id_usuario_registra,
            ];
           
        }else{
            $tabla = 'formacion_academica_soportes';
            $cargo = $this->pages_model->cargar_archivo("file", $this->ruta_archivos.'/formacion_academica/', "sop");
            if ($cargo[0] == -1) {
                header("HTTP/1.0 400 Bad Request");
                echo ($nombre);
                return;
            }
            $data = [
                'id_formacion' => $id_formacion,
                'nombre_real' => $nombre_real,
                'nombre_archivo' => $cargo[1],
                'id_persona' => $id_persona,
                'usuario_registra' => $id_usuario_registra,
            ];            
        }
        $add = $this->pages_model->guardar_datos($data,$tabla);
        echo json_encode($resp);
      }

      public function guardar_info_contacto(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_persona = $this->input->post('id_persona');
                $lugar_residencia = $this->input->post('info_lugar_residencia');
                $direccion = $this->input->post('info_direccion');
                $oficina = $this->input->post('info_oficina');
                $correo_personal = $this->input->post('info_correo_personal');
                $telefono = $this->input->post('info_telefono');
                $info = $this->talento_cuc_model->traer_registro_id($id_persona,'personas','id');
                $str = $this->verificar_campos_string(['Lugar Residencia' => $lugar_residencia, 'Dirección' => $direccion, 'Oficina' => $oficina, 'Correo Personal' => $correo_personal, 'Telefono' => $telefono]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    if ($info->{'lugar_residencia'} == $lugar_residencia && $info->{'direccion'} == $direccion &&  $info->{'oficina'} == $oficina &&  $info->{'correo_personal'} == $correo_personal &&  $info->{'telefono'} == $telefono) {
                        $resp = ['mensaje'=>"Debe realizar alguna modificación.",'tipo'=>"info",'titulo'=> "Oops!"];
                    }else{
                        $data = [
                            'lugar_residencia' => $lugar_residencia,
                            'direccion' => $direccion,
                            'oficina' => $oficina,
                            'correo_personal' => $correo_personal,
                            'telefono' => $telefono,
                        ];
                        $add = $this->pages_model->modificar_datos($data,'personas', $id_persona);
                        $info = $this->talento_cuc_model->traer_registro_id($id_persona,'personas','id');
                        $resp = ['mensaje'=>"Información guardado exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!", 'info' => $info];
                        if(!$add) $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_otros_soporte(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_persona = $this->input->post('persona_id');
            $btn_ver = '<a target="_blank" class="ver_adjunto"><span title="Ver" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default red"></span></a>';
            $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;" class="pointer fa fa-trash btn btn-default eliminar"></span>';
            $data = $this->talento_cuc_model->listar_otros_soporte($id_persona);
            foreach ($data as $row) {
                $row['accion'] = $btn_ver. ' '. $btn_eliminar;            
                array_push($resp,$row);            
            }
        }
		echo json_encode($resp);
    }

    public function guardar_avatar(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_persona = $this->input->post('id_persona');                      
                $info = $this->talento_cuc_model->traer_registro_id($id_persona,'personas','id');     
                $file = $this->pages_model->cargar_archivo("avatarInput", $this->ruta_imagenes, $info->{'identificacion'});
                if ($file[0] == -1) {
                    $error = $file[1];
                    if ($error == "<p>You did not select a file to upload.</p>") {
                        $resp = ['mensaje'=>"Debe seleccionar una imagen .png ó .png",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        $resp = ['mensaje'=>"Error al cargar archivo, contacte con el administrador.!",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }else{
                    $data = ['foto' => $file[1]];
                    $resp = ['mensaje'=>"Imagen guardada exitosamente!.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!", 'avatar' => $file[1]];
                    $mod = $this->pages_model->modificar_datos($data,'personas',$id_persona);
                    if(!$mod) $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function get_competencias_req(){
        $id_persona = $this->input->post("idpersona");
        $filtro = $this->input->post("filtro");
        // $id_solicitud = $this->input->post("id_solicitud");
        $datos = $this->Super_estado ? $this->talento_cuc_model->get_competencias_req($id_persona,$filtro) : array();
        echo json_encode($datos);
    }

    public function calcular_planFormacion_ingreso(){       
        if (!$this->Super_estado) $resp_final = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp_final = [];
            $id_persona = $this->input->post('idpersona');
            $datos = $this->talento_cuc_model->get_competencias_req($id_persona,1);
            if($datos) $resp_final = $datos;
            else{
                $data = $this->talento_cuc_model->get_competencias_req($id_persona);
                $resp_op = $this->competencias_Oportunidades($data,2); 
                $resp_baja = $this->array_sort_by($data, 'puntaje', $order = SORT_ASC, 1);
                $val = $this->talento_cuc_model->traer_registro_id('Opt_Mej', 'valor_parametro', 'id_aux');         
                foreach ($resp_op as $row){ // unir marcadas como oportunidad y puntaje bajo
                    $sw = array_search($row['id_competencia'], array_column($resp_baja, 'id_competencia'));
                    if($sw >= 0) $row['observaciones'] = 'Oportunidad de Mejora y Puntaje Bajo';
                    else $row['observaciones'] = 'Oportunidad de Mejora';
                    $row['hora_formacion'] = $val->{'valorx'};
                    array_push($resp_final, $row);
                }

                foreach ($resp_baja as $row){ // marcadas como baja que no se encuentran en array final 
                    $sw = array_search($row['id_competencia'], array_column($resp_final, 'id_competencia'));
                    if($sw === false) array_push($resp_final, $row);
                }            
            }            
		}
		echo json_encode($resp_final); 
    }

    public function guardar_plan_formacion_ingreso(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        else {
            if ($this->Super_agrega) {
                $idpersona = $this->input->post('idpersona');
                $identificacion = $this->input->post('identificacion');
                $data_formacion = $this->input->post('data_formacion');
                $data_insert = [];
                $resp = ['mensaje' => "Información guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                $plan_actual = $this->talento_cuc_model->listar_detalle_resultados($identificacion,1);
                foreach ($data_formacion as $row){
                    if(!array_search($row['id_competencia'], array_column($plan_actual, 'id_competencia'))){
                        $data_comp = [
                            'id_persona'=> $row['identificacion'],
                            'id_competencia'=> $row['id_competencia'],
                            'puntaje'=> $row['puntaje'],
                            'fortaleza'=> $row['tipo'] == 1 ? 1 : 0,
                            'mejora'=> $row['tipo'] == 0 ? 1 : 0,
                            'estado_formacion' => 1, 
                            'observaciones' => 'Formación de Ingreso', 
                            'hora_formacion' => $row['hora_formacion'],
                            'id_usuario_registra' => $_SESSION['persona'],
                        ];
                        array_push($data_insert, $data_comp);
                    }else{
                        $clave = array_search($row['id_competencia'], array_column($plan_actual, 'id_competencia'));
                        $data_mod = ['hora_formacion' => ($row['hora_formacion'] + $plan_actual[$clave]['hora_formacion']), 'observaciones' => $plan_actual[$clave]['observaciones'].' y Formación de Ingreso']; 
                        $add = $this->pages_model->modificar_datos($data_mod, 'evaluacion_resultado_competencia', $plan_actual[$clave]['id']);
                    }
                    $data = [ 'estado_formacion' => 1, 'observaciones' => $row['observaciones'], 'hora_formacion' => $row['hora_formacion']]; 
                    $add = $this->pages_model->modificar_datos($data, 'competencias_talento_cuc', $row['id']);
                }

                if(count($data_insert) > 0){
                     $add = $this->pages_model->guardar_datos($data_insert, 'evaluacion_resultado_competencia',2);
                    if(!$add) $resp = ['mensaje'=>"Error al guardar la Información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                }
            }
        }
        echo json_encode($resp);
    }

    public function get_competencias_formacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_formacion = $this->input->post('id_formacion');
            $resp = $this->talento_cuc_model->get_competencias_formacion($id_formacion);
        }
		echo json_encode($resp);
    }

    public function guardar_soporte_academico(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            if ($this->Super_agrega) {
                $id_formacion = $this->input->post('id');
                $id_persona = $this->input->post('id_persona');
                $id_usuario_registra= $_SESSION['persona']; 
                $carga = $this->pages_model->cargar_archivo("soporte_aca", $this->ruta_archivos.'/formacion_academica/', "sop");
                if ($carga[0] == -1) {
                    $resp = ['mensaje'=>"Error al cargar archivo, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                }else{
                    $nombre_real = $_FILES["soporte_aca"]["name"];
                    $data = [
                        'id_formacion' => $id_formacion,
                        'id_persona' => $id_persona,
                        'nombre_real' => $nombre_real,
                        'nombre_archivo' => $carga[1],
                        'usuario_registra' => $_SESSION['persona'],
                        ];
                    $resp = ['mensaje'=>"Documento guardado exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
                    $add = $this->pages_model->guardar_datos($data,'formacion_academica_soportes');
                    if(!$add) $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                }
            }
        }
		echo json_encode($resp);
    }

    public function listar_actividades(){
		$persona = $this->input->post('persona');
		$data = (isset($persona) && !empty($persona))
			? $this->talento_cuc_model->listar_actividades($persona)
			: [];
		echo json_encode($data);
	}

    public function asignar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_agrega) {
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			$ok = $this->talento_cuc_model->validar_asignacion_actividad($actividad, $persona);
			if ($ok) {
				$data = ['actividad_id'=>$actividad, 'persona_id'=>$persona, 'usuario_registra'=>$_SESSION['persona']];
				$resp = $this->pages_model->guardar_datos($data, 'talentocuc_actividad_persona');
				$res = $resp ? ['mensaje' => "Actividad asignada exitosamente.",'tipo' => "success",'titulo' => "Proceso Exitoso!"]
				             : ['mensaje' => "Ha ocurrido un error al asignar la actividad.",'tipo' => "info",'titulo' => "Ooops!"];
			} else $res = ['mensaje' => "El usuario ya tiene asignada esta actividad.",'tipo' => "info",'titulo' => "Ooops!"];
		} else $res = ['mensaje' => 'No tiene Permisos Para Realizar Esta operación.','tipo' => 'error','titulo' => 'Oops.!'];
		echo json_encode($res);
	}

	public function quitar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_modifica) {
			$id = $this->input->post('asignado');
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			// Verifico si actividad ya está asignada o no. Esta función retorna 0 si no está asignada la actividad y 1 si lo está.
			$ok = $this->talento_cuc_model->validar_asignacion_actividad($actividad, $persona);
			if (!$ok) {
				$resp = $this->talento_cuc_model->quitar_actividad($id);
				if ($resp) {
					$res = $resp ? ['mensaje' => "Actividad Desasignada exitosamente.",'tipo' => "success",'titulo' => "Proceso Exitoso!"]
					             : ['mensaje' => "Ha ocurrido un error al desasignar la actividad.",'tipo' => "info",'titulo' => "Ooops!"];
				} else $res = ['mensaje' => "Ha ocurrido un error al desasignar la actividad.",'tipo' => "info",'titulo' => "Ooops!"];
			} else $res = ['mensaje' => "El usuario no tiene asignada esta actividad.",'tipo' => "info",'titulo' => "Ooops!"];
		} else $res = ['mensaje' => 'No tiene Permisos Para Realizar Esta operación.','tipo' => 'error','titulo' => 'Oops.!'];
		echo json_encode($res);
	}

    public function activar_notificacion(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->administra) {
				$id = $this->input->post('id');
				$resp = $this->pages_model->modificar_datos(['notificacion' => 1], 'talentocuc_actividad_persona', $id);
				$res = $resp 
                    ? ['mensaje' => "Notificación activada exitosamente.",'tipo' => "success", 'titulo' => "Proceso Exitoso!"]
				    : ['mensaje' => "Ha ocurrido un error al activar notificación.",'tipo' => "info",'titulo' => "Ooops!"];
			}else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.','tipo' => 'info','titulo' => 'Ooops!'];
		}
		echo json_encode($res);
	}

	public function desactivar_notificacion() {
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->administra) {
				$id = $this->input->post('id');
                $resp = $this->pages_model->modificar_datos(['notificacion' => 0], 'talentocuc_actividad_persona', $id);
                $res = $resp
                    ? ['mensaje'=>"Notificación desactivada exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
                    : ['mensaje'=>"Ha ocurrido un error al desactivar notificación.",'tipo'=>"info",'titulo'=> "Ooops!"];
			}else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		}
		echo json_encode($res);
    }
    
    public function enviar_entrenamiento(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            if ($this->Super_agrega) {
                $id_persona = $this->input->post('idpersona');
                $resp = ['mensaje'=>"Plan de entrenamiento enviado exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
                $mod = $this->pages_model->modificar_datos(['enviado' => 1], 'talentocuc_plan_entrenamiento' , $id_persona, 'id_evaluado');
                if(!$mod) $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
            }
        }
		echo json_encode($resp);
    }

    public function obtener_entrenamiento(){
        $sw = false;
        if (!$this->Super_estado) $resp = array();
		else {
            $id_persona = $this->input->post('idpersona');
            $id_entrenamiento = $this->input->post('id_entrenamiento');
            $resp = $this->talento_cuc_model->obtener_entrenamiento($id_persona,$id_entrenamiento);
            if(!$id_persona){
                $asignadas = $this->talento_cuc_model->obtener_entrenamiento($resp->{'id_evaluado'});
                $cant = count($asignadas);
                $aprobadas = $this->talento_cuc_model->entrenamiento_finalizado($resp->{'id_evaluado'});
                $sw = $aprobadas == $cant && $cant > 1 ? true : false;
            }
        }
        echo json_encode([$resp, $sw]);
    }

    public function listar_asistencias_entrenamiento(){
        $resp = [];
        if (!$this->Super_estado) $resp = array();
		else {
            $id_funcionario = $this->input->post('id_funcionario');
            $data = $this->talento_cuc_model->listar_asistencias_entrenamiento($id_funcionario);
            $btn_confirmar = '<span class="btn btn-success seleccionar" title="Confirmar Asistencia"><span class="fa fa-check"></span></span></td>';
            $btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            foreach($data as $row){
                $row['gestion'] = $row['aprobacion'] ? $btn_inhabil : $btn_confirmar;
                array_push($resp,$row); 
            }
        }
        echo json_encode($resp);
    }

    public function adjuntar_firma($name){
        if ( isset($_POST[$name]) && !empty($_POST[$name]) ) {    
            $dataURL = $_POST[$name];  
            $parts = explode(',', $dataURL);  
            $data = $parts[1];  
            $data = base64_decode($data);  
            $file =  uniqid() . '.png';
            $success = file_put_contents('archivos_adjuntos/talento_cuc/entrenamiento/firmas/'.$file, $data);
            return $success ? $file : -1;
        }
          return -2;
    }

    public function guardar_confirmacion_entrenamiento(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            if ($this->Super_modifica) {
                $id_entrenamiento = $this->input->post('id_entrenamiento');
                $id_funcionario = $this->input->post('funcionario');
                $firma = $this->adjuntar_firma("firma");
                if($firma == -2 ){
                    $resp = ['mensaje'=>"Antes de continuar debe firmar el recibido.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else if($firma == -1){
                    $resp = ['mensaje'=>"Error al cargar la firma.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $mod = $this->pages_model->modificar_datos(['aprobacion' => 1, 'firma' => $firma, 'encuesta_enviada' => 1], 'talentocuc_plan_entrenamiento' , $id_entrenamiento);
                    if($mod){
                        $cantidades = $this->talento_cuc_model->cantidad_asistencias_entrenamiento($id_funcionario);
                        $progress = $cantidades['cantidad'] > 0 ? ($cantidades['aprobados']/$cantidades['cantidad'])*100 : 0;
                        $progress = round($progress);
                        $resp = ['mensaje'=>"Informacion enviada exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!", 'progress' => $progress];
                    }else  $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                }
            }
        }
		echo json_encode($resp);
    }

    public function guardar_correos_notificacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            if ($this->Super_modifica) {
                $correos = $this->input->post('correos_th');
                $str = $this->verificar_campos_string(['Correos Responsable TH' => $correos ]);
                $data = $this->talento_cuc_model->get_personas_notificar_th(249);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    if($data === $correos){
                        $resp = ['mensaje'=>"Debe realizar alguna modificación.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $resp = ['mensaje'=>"Informacion enviada exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
                        $mod = $this->pages_model->modificar_datos(['valor' => $correos], 'valor_parametro' , 249, 'idparametro');
                        if(!$mod) $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                    }
                }
            }
        }
		echo json_encode($resp);
    }

    public function get_personas_notificar_th(){
        $data = [];
        $encargados = [];
		if (!$this->Super_estado) $encargados = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $idparametro = $this->input->post('idparametro');
			$data = $this->talento_cuc_model->get_personas_notificar_th($idparametro);
            if($data){
                $info = explode(";", $data);
                for($i=0; $i < count($info); $i++){
                    array_push($encargados, ["correo" => $info[$i]]);        
                }
            }
		}
		echo json_encode([$encargados, $data]);
	}

    public function exportar_detalle_entrenamiento($idpersona,$tipo){
        if ($this->Super_estado){
            $data = [];
			$per = $this->talento_cuc_model->obtener_info_persona(null,$idpersona);
			$datos = $this->talento_cuc_model->obtener_entrenamiento($idpersona,null,$tipo);
            array_push($data, [ 
                "id_persona" => $idpersona, 
                "nombre_completo" => $per[0]['nombre_completo'], 
                "cargo" => $per[0]['cargo'],
                "fecha_ingreso" => $per[0]['fecha_ingreso'],
            ]);

            $info["persona"] = $data;
            $info["archivo"] = !$tipo ? 'Entrenamiento_' : 'Plan_Entrenamiento_';
            $info["datos"] = $datos;
            $this->load->view('templates/exportar_entrenamiento', $info);
            return;
        } 
        redirect('/', 'refresh');   
    }

    public function guardar_oferta_entrenamiento(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            if ($this->Super_agrega) {
                $tema = $this->input->post('tema');
                $dept_adscrito = $this->input->post('dept_adscrito');
                $departamento = $this->input->post('departamento');
                $area_especifica = $this->input->post('area_especifica');
                $str = $this->verificar_campos_string(['Tema' => $tema, 'Departamento Adscrito' => $dept_adscrito, 'Departamento' => $departamento, 'Área especifica' => $area_especifica]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data = [
                        'idparametro' => 244,
                        'valor' => $tema, 
                        'valorx' => $dept_adscrito,
                        'valory' => $departamento,
                        'valorz' => $area_especifica,
                    ];
                    $resp = ['mensaje'=>"Informacion enviada exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
                    $mod = $this->pages_model->guardar_datos($data, 'valor_parametro');
                    if(!$mod) $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                }
            }
        }
		echo json_encode($resp);
    }

    public function modificar_oferta_entrenamiento(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            if ($this->Super_agrega) {
                $id = $this->input->post('id');
                $tema = $this->input->post('tema');
                $dept_adscrito = $this->input->post('dept_adscrito');
                $departamento = $this->input->post('departamento');
                $area_especifica = $this->input->post('area_especifica');
                $str = $this->verificar_campos_string(['Tema' => $tema, 'Departamento Adscrito' => $dept_adscrito, 'Departamento' => $departamento, 'Área especifica' => $area_especifica]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $info = $this->talento_cuc_model->traer_registro_id($id, 'valor_parametro', 'id'); 
                    if($info->{'valor'} == $tema && $info->{'valorx'} == $dept_adscrito && $info->{'valory'} == $departamento && $info->{'valorz'} == $area_especifica){
                        $resp = ['mensaje'=>"Debe realizar alguna modificación!.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $data = [
                            'valor' => $tema, 
                            'valorx' => $dept_adscrito,
                            'valory' => $departamento,
                            'valorz' => $area_especifica,
                        ];
                        $resp = ['mensaje'=>"Informacion enviada exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
                        $mod = $this->pages_model->modificar_datos($data, 'valor_parametro', $id);
                        if(!$mod) $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                    }
                }
            }
        }
		echo json_encode($resp);
    }

    public function listar_ofertas_entrenamiento(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = array();
            $btn_mod = '<span title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-edit btn btn-default modificar red"></span>';
            $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
            $data = $this->talento_cuc_model->listar_ofertas_entrenamiento();
            foreach ($data as $row) {
                 $row['gestion'] = $btn_mod.' '.$btn_eliminar;
                array_push($resp,$row);               
            }
        }
		echo json_encode($resp);
    }

    public function buscar_dependencia(){
		$dependencias = array();
		if ($this->Super_estado) {
			$buscar = $this->input->post('buscar');
			$id_departamento = $this->input->post('departamento');
			if (!empty($buscar)) $dependencias = $this->talento_cuc_model->buscar_dependencia($buscar, $id_departamento);  
		}
		echo json_encode($dependencias);
	}

    public function buscar_oferta(){
		$resp = array();
		if ($this->Super_estado) {
			$dato = $this->input->post('dato');
			$buscar = "(vo.valor LIKE '%" . $dato . "%' OR vp.valor LIKE '%" . $dato . "%' OR vd.valor LIKE '%" . $dato . "%' OR va.valor LIKE '%" . $dato . "%') AND vo.estado=1 AND vo.idparametro = 244";
			if (!empty($dato)) $resp = $this->talento_cuc_model->buscar_oferta($buscar);  
		}
		echo json_encode($resp);
	}

    public function estado_entrenamiento(){
        $resp = array();
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id_persona = $this->input->post('idpersona');
			$resp = $this->talento_cuc_model->estado_entrenamiento($id_persona);  
		}
		echo json_encode($resp);
	}

    public function enviar_acta_aceptacion_cargo(){
        $resp = array();
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id_persona = $this->input->post('id_persona');
			$req_firma = $this->input->post('req_firma');
			$id_jefe_inmediato = $this->input->post('id_jefe');
			$cargo_id = $this->input->post('cargo_id');
			$codigo_cargo = $this->input->post('codigo_cargo');
            $str = $this->verificar_campos_string(['Jefe inmediato' => $id_jefe_inmediato, 'Código Cargo' => $codigo_cargo]);
            if (is_array($str)) {
                $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
            }else{
                $data = [
                    'id_evaluado' => $id_persona, 
                    'jefe_inmediato' => $id_jefe_inmediato, 
                    'solicitar_firma_jefe' => $req_firma,
                    'acta_enviada' => 1,
                    'fecha_entrega' => date("Y-m-d H:i:s"),
                    'id_cargo_sap' => $cargo_id,
                    'codigo_cargo' => $codigo_cargo,
                    'id_usuario_registra' => $_SESSION['persona']
                ];
               
                $mod = $this->pages_model->guardar_datos($data, 'talentocuc_aceptacion_cargo');
                if($mod){
                    $mod_p = $this->pages_model->modificar_datos(['codigo_cargo' => $codigo_cargo, 'id_cargo_sap' => $cargo_id], 'personas', $id_persona, 'identificacion');
                    $resp = ['mensaje'=>"Informacion enviada exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!", 'codigo_cargo' => $codigo_cargo];
                }else $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
            }
		}
		echo json_encode($resp);
	}

    public function guardar_aceptacion_cargo(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            if ($this->Super_modifica) {
                $id = $_SESSION['persona'];
                $observaciones = $this->input->post('observaciones');
                $firma = $this->adjuntar_firma("firma_fun");
                $persona = $this->talento_cuc_model->obtener_info_persona($id);
                $cargo_sap = $persona[0]['id_cargo_sap'];
                $identificacion = $persona[0]['id_persona'];
                if($firma == -2 ){
                    $resp = ['mensaje'=>"Antes de continuar debe firmar el recibido.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else if($firma == -1){
                    $resp = ['mensaje'=>"Error al cargar la firma.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $info = $this->talento_cuc_model->estado_entrenamiento($id, $persona[0]['id_cargo_sap']);
                    if($info->{'solicitar_firma_jefe'} == 1){
                         $data = ['observacion_acta' => $observaciones, 'firma_fun' => $firma, 'fecha_recibido' => date("Y-m-d H:i:s")];
                    }else $data = ['observacion_acta' => $observaciones, 'firma_fun' => $firma, 'fecha_recibido' => date("Y-m-d H:i:s"), 'terminado' => 1];

                    $mod = $this->talento_cuc_model->modificar_datos_mult($data, 'talentocuc_aceptacion_cargo', "id_evaluado = '$identificacion' and id_cargo_sap = '$cargo_sap'");
                    if(!$mod){
                        $info = $this->talento_cuc_model->estado_entrenamiento($id, $persona[0]['id_cargo_sap']);
                        $resp = ['mensaje'=>"Informacion enviada exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!", 'info' => $info, 'persona_id' => $id];
                    }else $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                }
            }
        }
		echo json_encode($resp);
    }

    public function exportar_acta_cargo($id = null){
        if ($this->Super_estado){
            $id_persona = !$id ? $_SESSION['persona'] : $id;
			$persona = $this->talento_cuc_model->obtener_info_persona($id_persona);
			$datos = $this->talento_cuc_model->estado_entrenamiento($id_persona, $persona[0]['id_cargo_sap']);
            $info["persona"] = $persona;
            $info["datos"] = $datos;
            $info["version"] = "VERSIÓN: 09";
            $info["trd"] = "TRD: 700-730-90";
            $info["fecha"] = 'DICIEMBRE '.date("Y");
            $this->load->view('templates/exportar_acta_aceptacion_cargo', $info);
            return;
        } 
        redirect('/', 'refresh');
    }

    public function listar_actas_personas(){
        $resp = [];
        if (!$this->Super_estado) $resp = array();
		else {
            $id_jefe = $this->input->post('id_funcionario_jefe');
            $data = $this->talento_cuc_model->listar_actas_personas($id_jefe);
            $btn_confirmar = '<span class="btn btn-success seleccionar" title="Confirmar acta"><span class="fa fa-check"></span></span></td>';
            $btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            foreach($data[0] as $row){
                if($row['terminado']){
                    $row['gestion'] = $btn_inhabil;
                    $bg_color = '#5cb85c';
                    $color = 'white';
                }else{
                    $row['gestion'] = $btn_confirmar;
                    $bg_color = 'white';
                    $color = 'black';
                }
                $row['ver'] = "<span  title='Ver Acta' style='background-color: $bg_color;color: $color; width: 100%;' class='pointer form-control'><span >ver</span></span>";
                array_push($resp,$row); 
            }
        }
        echo json_encode($resp);
    }

    public function guardar_confirmacion_jefe(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            if ($this->Super_modifica) {
                $id = $this->input->post('id_acta');
                $firma = $this->adjuntar_firma("firma_jefe");
                if($firma == -2 ){
                    $resp = ['mensaje'=>"Antes de continuar debe firmar el acta.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else if($firma == -1){
                    $resp = ['mensaje'=>"Error al cargar la firma.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $data = ['firma_jefe' => $firma, 'terminado' => 1];
                    $mod = $this->pages_model->modificar_datos($data, 'talentocuc_aceptacion_cargo', $id);
                    if($mod){
                        $info = $this->talento_cuc_model->listar_actas_personas($_SESSION['persona']);
                        $porcentaje = $info[0] ? ($info[1]/count($info[0]))*100 : 0;
                        $progress = round($porcentaje);
                        $resp = ['mensaje'=>"Informacion guardada exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!", 'progress' => $progress];
                    }else $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
                }
            }
        }
		echo json_encode($resp);
    }

    public function get_info_seleccion(){
        $resp = [];
		if (!$this->Super_estado) $resp;
		else {
			$id_persona = $this->input->post('id');
			$resp = $this->talento_cuc_model->get_info_seleccion($id_persona);  
		}
		echo json_encode($resp);
	}

    public function listar_asignacion_indicadores(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $id_evaluado = $this->input->post('id_evaluado');
            $resp = $id_evaluado === '' ? array() : $this->talento_cuc_model->obtener_preguntas_indicador($id_evaluado);
		}
		echo json_encode($resp); 
    }

    public function guardar_asignacion_indicador(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_evaluado = $this->input->post('id_evaluado');
                // $tipo_pregunta = $this->input->post('tipo_pregunta_ind');
                $tipo_meta_ind = $this->input->post('tipo_meta_ind');
                $meta = $this->input->post('meta_indicador');
                $periodo = $this->input->post('periodo_indicador');
                $descripcion = $this->input->post('descripcion_ind');
                $str = $this->verificar_campos_string(['Evaluado' => $id_evaluado, 'Periodo' => $periodo, 'Tipo de Meta' => $tipo_meta_ind, 'Descripcion Indicador' => $descripcion]);
                $num = $this->verificar_campos_numericos(['Meta' => $meta]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else if (is_array($num)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data = [
                        'evaluado' => $id_evaluado, 
                        'pregunta' => $descripcion, 
                        'periodo' => $periodo,
                        'id_tipo_meta' => $tipo_meta_ind, 
                        'meta' => $meta, 
                        'id_usuario_registra ' => $_SESSION['persona']
                    ];               
                    $add = $this->pages_model->guardar_datos($data, 'talentocuc_indicadores');
                    $resp = ['mensaje' => "La información fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                    if($add != 1) $resp = ['mensaje'=>"Error al guardar la asignación, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_asignacion_indicador(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_evaluado = $this->input->post('id_evaluado');
                $id_pregunta = $this->input->post('id_pregunta');
                // $tipo_pregunta = $this->input->post('tipo_pregunta_ind');
                $tipo_meta_ind = $this->input->post('tipo_meta_ind');
                $meta = $this->input->post('meta_indicador');
                $periodo = $this->input->post('periodo_indicador');
                $descripcion = $this->input->post('descripcion_ind');
                $str = $this->verificar_campos_string(['Evaluado' => $id_evaluado, 'Periodo' => $periodo, 'Tipo de Meta' => $tipo_meta_ind, 'Descripcion Indicador' => $descripcion]);
                $num = $this->verificar_campos_numericos(['Meta' => $meta]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else if (is_array($num)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $info = $this->talento_cuc_model->traer_registro_id($id_pregunta, 'talentocuc_indicadores','id');
                    if($info->{'periodo'} === $periodo && $info->{'pregunta'} === $descripcion && $info->{'id_tipo_meta'} === $tipo_meta_ind && $info->{'meta'} === $meta){
                        $resp = ['mensaje' => "Debe realizar alguna modificación.", 'tipo' => "info", 'titulo' => "Oops.!"];
                    }else{
                        $data = [ 
                            'pregunta' => $descripcion, 
                            'periodo' => $periodo, 
                            'id_tipo_meta' => $tipo_meta_ind,
                            'meta' => $meta,
                        ];               
                        $add = $this->pages_model->modificar_datos($data, 'talentocuc_indicadores', $id_pregunta);
                        $resp = ['mensaje' => "La información fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                        if($add != 1) $resp = ['mensaje'=>"Error al guardar la asignación, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_asignaciones(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $persona_id = $this->input->post('persona_id');
            $tabla = $this->input->post('tabla_bd');
            $resp = $persona_id === '' ? array() : $this->talento_cuc_model->obtener_preguntas($persona_id, $tabla);
		}
		echo json_encode($resp); 
    }

    public function guardar_asignacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_persona = $this->input->post('id_persona');
                $tabla_bd = $this->input->post('tabla_bd');
                $periodo = $this->input->post('periodo_fun');
                $tipo_pregunta_fun = $this->input->post('tipo_pregunta_fun');
                $descripcion = $this->input->post('descripcion_fun');
                $formacion = $tabla_bd == 'talentocuc_formacion_esencial' ? $this->input->post('formacion_es') : null;
                $str = $this->verificar_campos_string(['Evaluado' => $id_persona, 'Periodo' => $periodo, 'Tipo Pregunta' => $tipo_pregunta_fun, 'Descripcion' => $descripcion]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else if($formacion != null && $formacion == ''){
                    $resp = ['mensaje'=>"El campo Formación no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    if($formacion != null){
                        $data = [
                            'evaluado' => $id_persona, 
                            'pregunta' => $descripcion, 
                            'periodo' => $periodo,
                            'respuesta' => $formacion,
                            'id_tipo_respuesta' => $tipo_pregunta_fun,
                            'id_usuario_registra ' => $_SESSION['persona']
                        ]; 
                    }else{
                        $data = [
                            'evaluado' => $id_persona, 
                            'pregunta' => $descripcion, 
                            'periodo' => $periodo,
                            'id_tipo_respuesta' => $tipo_pregunta_fun,
                            'id_usuario_registra ' => $_SESSION['persona']
                        ];   
                    }            
                    $add = $this->pages_model->guardar_datos($data, $tabla_bd);
                    $resp = ['mensaje' => "La información fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                    if($add != 1) $resp = ['mensaje'=>"Error al guardar la asignación, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_asignacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id = $this->input->post('id_asignacion');
                $tabla_bd = $this->input->post('tabla_bd');
                $periodo = $this->input->post('periodo_fun');
                $tipo_pregunta_fun = $this->input->post('tipo_pregunta_fun');
                $descripcion = $this->input->post('descripcion_fun');
                $formacion =  $tabla_bd == 'talentocuc_formacion_esencial' ? $this->input->post('formacion_es') : null;
                $str = $this->verificar_campos_string(['Periodo' => $periodo, 'Tipo Pregunta' => $tipo_pregunta_fun, 'Descripcion' => $descripcion]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else if($formacion != null && $formacion == ''){
                    $resp = ['mensaje'=>"El campo formación no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $info = $this->talento_cuc_model->traer_registro_id($id, $tabla_bd,'id');
                    if($tabla_bd === 'talentocuc_formacion_esencial' && $info->{'periodo'} === $periodo && $info->{'pregunta'} === $descripcion && $info->{'id_tipo_respuesta'} === $tipo_pregunta_fun && $info->{'respuesta'} === $formacion ){
                        $resp = ['mensaje' => "Debe realizar alguna modificación.", 'tipo' => "info", 'titulo' => "Oops.!"];
                    }else if($tabla_bd === 'talentocuc_funciones' && $info->{'periodo'} === $periodo && $info->{'pregunta'} === $descripcion && $info->{'id_tipo_respuesta'} === $tipo_pregunta_fun){
                        $resp = ['mensaje' => "Debe realizar alguna modificación.", 'tipo' => "info", 'titulo' => "Oops.!"];
                    }else{                    
                        if($formacion != null){
                            $data = [ 
                                'pregunta' => $descripcion, 
                                'periodo' => $periodo,
                                'respuesta' => $formacion,
                                'id_tipo_respuesta' => $tipo_pregunta_fun,
                            ]; 
                        }else{
                            $data = [ 
                                'pregunta' => $descripcion, 
                                'periodo' => $periodo,
                                'id_tipo_respuesta' => $tipo_pregunta_fun,
                            ]; 
                        }              
                        $add = $this->pages_model->modificar_datos($data, $tabla_bd, $id);
                        $resp = ['mensaje' => "La información fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                        if($add != 1) $resp = ['mensaje'=>"Error al guardar la asignación, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_asistencias_formacion(){
        if (!$this->Super_estado) $resp = [];
		else {
            $filtro = $this->input->post('data');
            $fecha_i = $this->input->post('fecha_i');
            $fecha_f = $this->input->post('fecha_f');
            $texto = $this->input->post('texto');
            $id_lugar = $this->input->post('id_lugar');
            $resp = $this->talento_cuc_model->listar_asistencias_formacion($filtro, $fecha_i, $fecha_f, $texto, $id_lugar);
		}
		echo json_encode($resp); 
    }

    public function buscar_cargo(){
        $resp = array();
        if (!$this->Super_estado) $resp;
		else {
            $dato = $this->input->post('dato');
            if (!empty($dato)) $resp = $this->talento_cuc_model->buscar_cargo($dato);
		}
		echo json_encode($resp); 
    }

    public function obtener_tipo_respuesta(){
        $resp = array();
        if (!$this->Super_estado) $resp;
		else {
            $id_aux = $this->input->post('id_aux');
            $resp = $this->talento_cuc_model->obtener_tipo_respuesta($id_aux);
		}
		echo json_encode($resp); 
    }

    public function listar_actas_cargo(){
        $resp = array();
        if (!$this->Super_estado) $resp;
		else {
            $id_persona = $this->input->post('idpersona');
            $resp = $this->talento_cuc_model->listar_actas_cargo($id_persona);
		}
		echo json_encode($resp); 
    }

    public function listar_detalle_indicadores(){
        $resp = array();
        if (!$this->Super_estado) $resp;
		else {
            $id_persona = $this->input->post('idpersona');
            $tipo = $this->input->post('tipo');
            $info = $this->talento_cuc_model->traer_registro_id($id_persona, 'personas','id');
            $periodo_actual = $this->talento_cuc_model->get_periodo_actual();
            if($tipo == 1) $resp = $this->talento_cuc_model->obtener_preguntas_indicador($info->{'identificacion'}, $periodo_actual);
            else $resp = $this->talento_cuc_model->obtener_preguntas($info->{'identificacion'}, 'talentocuc_formacion_esencial', $periodo_actual);
		}
		echo json_encode($resp); 
    }

    public function listar_plan_formacion(){
        $resp = array();
        if (!$this->Super_estado) $resp;
		else {
            $id_persona = $this->input->post('identificacion_persona');
            $id_evaluacion = $this->input->post('id_evaluacion');
            $info = $this->talento_cuc_model->obtener_info_persona($_SESSION["persona"] );
            $persona_estado = $info[0]['identificacion'] == $id_persona ? true : false;
            $administra = $persona_estado || $this->administra ? true : false;
            $plan_formacion = $this->preparar_data_formacion($id_persona,$id_evaluacion);
            $resp = ["plan_formacion" => $plan_formacion[0], 'descarga' => $plan_formacion[1], 'administra' => $administra];

		}
		echo json_encode($resp); 
    }

}
    