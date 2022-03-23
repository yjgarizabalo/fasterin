<?php
	class evaluacion_control extends CI_Controller {
	//Variables encargadas de los permisos que tiene el usuario en session
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;

	var $super_admin = false;
    var $admin = false;

    public function __construct(){
        parent::__construct();
        $this->load->model('genericas_model');
        $this->load->model('evaluacion_model');
        $this->load->model('pages_model');
        date_default_timezone_set("America/Bogota");
        session_start();
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
            $this->administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Adm_Eval' || $_SESSION['perfil'] == 'Per_Admin_Tal' ? true : false;
        }
    }

    public function index($id = 0){
        $pages = "inicio";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $data["id"] = $id;
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], "evaluacion");
            $datos_actividad_adm = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'talento_humano_adm/evaluacion');
			if(!empty($datos_actividad_adm)) $datos_actividad = array_merge($datos_actividad,$datos_actividad_adm);
            if (!empty($datos_actividad)) {
                $pages = "evaluacion";
                $data['js'] = "evaluacion";
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

    public function encuesta($id=0){
        $pages = "inicio";
        $data['id_solicitud'] = $id;
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $data['metodo_evaluacion'] = '';
        $data['id_metodo'] = '';
        if ($this->Super_estado) {
            $solicitud = $this->evaluacion_model->get_solicitud($id,$_SESSION["persona"]);
            if (!empty($solicitud)) {
                $id_evaluador = $solicitud->{'id_evaluado'};
                $pages = "evaluacion_encuesta";
                $data['js'] = "evaluacion";
                $estado_solicitud = ($solicitud->{'id_estado_eval'} == 'Eval_Env' || $solicitud->{'id_estado_eval'} == 'Eval_Pro') ? true : false;
                $data['estado_solicitud'] = $estado_solicitud;  
                $data['estado_tipo_evaluador'] = $solicitud->{'tipo_evaluador'};  
                $data['metodo_evaluacion'] = $solicitud->{'tipo'};
                $data['id_metodo'] =  $solicitud->{'id_metodo_eval'};
                $data['parte1'] = $solicitud->{'parte1'};
                $tipo_evaluador = array();
                $res = array();
                $progress = 100;
                $nombre_tipo_evaluador = '';
                if($estado_solicitud){
                    $indicadores = $this->evaluacion_model->obtener_indicadores($id);
                    $data['indicadores'] = $indicadores;
                    $progress = 0;
                    $tipo_evaluador = $this->evaluacion_model->obtener_tipo_evaluador($id);
                    foreach ($tipo_evaluador as $key  => $valor){
                        if($solicitud->{'parte1'} == 0){
                            if($key < $solicitud->{'tipo_evaluador'}){
                                $progress = $progress + $valor["porcentaje"];
                                $valor['completado'] = 1;
                            }else $valor['completado'] = 0;
                        }else $valor['completado'] = 1;
                        if($valor['valory'] == '3'){
                            $valor['nombre_evaluado'] = '- '.$solicitud->{'nombre_jefe'};
                        }else if($valor['valory'] == '2'){
                            $valor['nombre_evaluado'] = '- '.$solicitud->{'nombre_coevaluado'};
                        }else if($valor['valory'] == '4'){
                            $indicadores = $this->evaluacion_model->obtener_indicadores($id);
                            $data['indicadores'] = $indicadores;
                        }else{
                            $valor['nombre_evaluado'] = '';
                        }
                        array_push($res,$valor);
                    }

                    if($solicitud->{'parte1'} == 1){
                        if($indicadores){
                            $nombre_tipo_evaluador = 'INDICADORES';
                            $cant = count($indicadores);
                            $completado = $this->evaluacion_model->obtener_indicadores($id,'','Eval_Ter');
                            $i = count($completado);
                            $porcentaje = ($i/$cant)*100;
                            $progress = round($porcentaje);
                        }
                    }else  $nombre_tipo_evaluador = $tipo_evaluador[$solicitud->{'tipo_evaluador'}]['valorx'];
                } 
                $data['tipo_evaluador'] = $res;
                $data['progress'] = $progress.'%'; 
                $data['nombre_tipo_evaluador'] = $nombre_tipo_evaluador;              
            }else{
                $pages = "sin_session";
                $data['js'] = "";
            }
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function acta($id=0){
        $pages = "inicio";
        $data['id_solicitud'] = $id;
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $data['indicadores'] = '';
        $data['estado_actas'] = false;
        $data['id_evaluacion'] = "";
        if ($this->Super_estado) {
            $solicitud = $this->evaluacion_model->get_solicitud($id,$_SESSION["persona"]);
            if (!empty($solicitud)) {
                $pages = "evaluacion_acta";
                $data['js'] = "evaluacion";
                $progress = 0;
                $indicadores = $this->evaluacion_model->listar_personal_actas($id);
                if($indicadores){
                    $i=0;
                    foreach ($indicadores as $row) if($row['acta_retro'] == 1 ) $i++;
                    $porcentaje = ($i/count($indicadores))*100;
                    $progress = round($porcentaje);
                }
                $data['progress'] = $progress.'%'; 
                $data['estado_actas'] = ($i == count($indicadores)) ? false : true;                 
            }else{
                $pages = "sin_session";
                $data['js'] = "";
            }
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function confirmar_acta($id=0){
        $pages = "inicio";
        $data['id_solicitud'] = $id;
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $data['nombre_evaluado'] =  '';
        if ($this->Super_estado) {
            $solicitud = $this->evaluacion_model->get_solicitud($id,$_SESSION["persona"]);
            if (!empty($solicitud) && $solicitud->{'acta'} == 1) {
                $pages = "evaluacion_confirmacion_acta";
                $data['js'] = "evaluacion"; 
                $data['estado'] = ($solicitud->{'recibido'} == 0) ? true : false;
                $data['nombre_evaluado'] = $solicitud->{'nombre_completo'};                 
            }else{
                $pages = "sin_session";
                $data['js'] = "";
            }
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

    public function array_sort_by($arrIni, $col, $order = SORT_ASC, $filtro = null){
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
        $datos = $this->Super_estado ? $this->evaluacion_model->get_solicitud($id_solicitud) : array();
        echo json_encode($datos);
    }

    public function listar_solicitudes(){
        if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $id = $this->input->post('id');
            $estado = $this->input->post('estado');
            $tipo = $this->input->post('tipo');
            $fecha_i = $this->input->post('fecha_i');
            $fecha_f = $this->input->post('fecha_f');
            $periodo = $this->input->post('periodo');
            $persona = $_SESSION['persona'];
            $admin =  $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Adm_Eval' ||  $_SESSION['perfil'] == 'Per_Admin_Tal' ? true : false;
            $res = array();
            $btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            $btn_gestionar= '<a target="_blank" class="encuesta"><span title="Gestionar" data-toggle="popover" data-trigger="hover" class="fa fa-edit btn btn-default" style="color:#2E79E5"></span></a>';
            $btn_cancelar = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default cancelar" style="color:#d9534f"></span>';
            $btn_enviar = '<span title="Notificar" data-toggle="popover" data-trigger="hover" class="fa fa-bell btn btn-default notificar" style="color:#2E79E5"></span>';
            $btn_resultado = '<span title="Resultado" data-toggle="popover" data-trigger="hover" class="fa fa-check-square btn btn-default resultado" style="color:#5cb85c"></span>';
            $btn_enviar_acta = '<span title="Enviar acta" data-toggle="popover" data-trigger="hover" class="fa fa-send btn btn-default acta" style="color:#f0ad4e"></span>';
            $bg_color = 'white';
            $color = 'white';
            $per_actas = array();
            $data = $this->evaluacion_model->listar_solicitudes($id, $estado, $tipo, $fecha_i, $fecha_f, $periodo);
            foreach ($data as $row) {
                $per_actas = $this->evaluacion_model->obtener_personal_sinActas($row['id']);
                switch($row['id_estado_eval']){
                    case 'Eval_Cer':
                        $bg_color = 'white';
                        $color = 'black';
                        $row['gestion'] = $btn_inhabil;
                        break;
                    case 'Eval_Sol':
                        $bg_color = 'white';
                        $color = 'black';
                        $row['gestion'] = ($admin) ? $btn_enviar .' '. $btn_cancelar : $btn_inhabil;
                        break;
                    case 'Eval_Env':
                        $bg_color = 'white';
                        $color = 'black';
                        $row['gestion'] = ($persona == $row['idpersona_evaluado'] ? $btn_gestionar : ($admin ? $btn_enviar.' '.$btn_cancelar : $btn_inhabil));
                        break;
                    case 'Eval_Pro':
                        $bg_color = '#337ab7';
                        $color = 'white';
                        $row['gestion'] = ($persona == $row['idpersona_evaluado'] ?  $btn_gestionar : ($admin ? $btn_enviar : $btn_inhabil));
                        break;    
                    case 'Eval_Ter':
                    case 'Eval_Act_Pro':
                    case 'Eval_Act_Env':
                    case 'Eval_Act_Fin':
                    case 'Eval_Form':
                        $bg_color = '#5cb85c';
                        $color = 'white';
                        $row['gestion'] = !$row['resultado'] ? ($per_actas ? $btn_resultado.' '.$btn_enviar_acta : $btn_resultado) : ($per_actas ? $btn_enviar_acta : $btn_inhabil); 
                        break;   
                    case 'Eval_Can':
                        $bg_color = '#d9534f';
                        $color = 'white';
                        $row['gestion'] = $btn_inhabil;
                        break;
                    case 'Eval_Form':
                        $bg_color = '#5cb85c';
                        $color = 'white';
                        $row['gestion'] = $per_actas ? $btn_enviar_acta : $btn_inhabil; 
                        break;     
                }
                $row['ver'] = "<span  style='background-color: $bg_color;color: $color; width: 100%;' class='pointer form-control'><span >ver</span></span>";
                array_push($res,$row);
            }
		}
		echo json_encode($res);
    }

    public function buscar_persona(){
		$personas = array();
		if ($this->Super_estado) {
			$dato = $this->input->post('dato');
			$buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1";
			if (!empty($dato)) $personas = $this->evaluacion_model->buscar_persona($buscar);  
		}
		echo json_encode($personas);
	}

    public function listar_valorparametro(){
        if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $idparametro = $this->input->post('parametro');
            $btn_area = '<span title="Asignar área de apreciación" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-crosshairs btn btn-default area_apreciacion"></span>';
            $btn_config = '<span title="Asignar Permiso" data-toggle="popover" data-trigger="hover" style="color: #39b23b;margin-left: 5px" class="pointer fa fa-gears btn btn-default asignar"></span>';
            $btn_modificar = '<span title="Modificar Servicio" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
            $btn_eliminar = '<span title="Eliminar Servicio" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
            $valores = array();
            $data = $this->evaluacion_model->listar_valorparametro($idparametro);
            foreach ($data as $row) {
                $row['peso_porcentual'] = $this->obtener_peso_porcentual($row['id_aux']);
                if($idparametro == 218){
                    $row['accion'] = $btn_modificar .' '. $btn_eliminar;
                }else if($idparametro == 215){
                        $row['accion'] = $btn_area .' '. $btn_config .' '. $btn_modificar .' '. $btn_eliminar;
                }else  $row['accion'] = $btn_config .' '. $btn_modificar .' '. $btn_eliminar;
                array_push($valores,$row);
            }
		}
		echo json_encode($valores);
    }

    public function obtener_peso_porcentual($id_aux){
        $data = $this->evaluacion_model->obtener_peso_porcentual($id_aux);
        $peso = $data->{'peso'};
        if($peso == null) $peso = 0;
        return $peso;
    }

    public function guardar_parametro(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
				$idparametro = $this->input->post('idparametro');
                $valor = $this->input->post('valor');
                $valorx = $this->input->post('valorx');
                $valory = $this->input->post('valory');
                $valorz = $this->input->post('valorz');
                $area_apreciacion = $this->input->post('area_apreciacion');
                if($idparametro == 217) $valorx = $area_apreciacion;
                $str = $this->verificar_campos_string(['Nombre' => $valor, 'idparametro' => $idparametro]);
                $sw = true;
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $dato = $this->evaluacion_model->valor_parametro($idparametro,$valor);
                    if(!empty($dato)){
                        $resp = ['mensaje' => "El Nombre ya se encuentra registrado", 'tipo' => "info", 'titulo' => "Oops.!"];
                        $sw = false;                        
                    }

                    if($sw){
                        $data = [
                            'idparametro' => $idparametro,
                            'valor' => $valor,
                            'valorx' => $valorx,
                            'valory' => $valory,
                            'valorz' => $valorz,
                            'usuario_registra' => $_SESSION['persona'],
                        ];
                        $add = $this->pages_model->guardar_datos($data, 'valor_parametro');
                        $resp = ['mensaje' => "La información fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                        if($add != 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }   
			}
		}
		echo json_encode($resp);
    }

    public function modificar_parametro(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
                $id = $this->input->post('id');
				$idparametro = $this->input->post('idparametro');
                $valor = $this->input->post('valor');
                $valorx = $this->input->post('valorx');
                $valory = $this->input->post('valory');
                $valorz = $this->input->post('valorz');
                $area_apreciacion = $this->input->post('area_apreciacion');
                if($idparametro == 217) $valorx = $area_apreciacion;
                $str = $this->verificar_campos_string(['Nombre' => $valor, 'idparametro' => $idparametro]);
                $dato = $this->evaluacion_model->valor_parametro($idparametro,'',$id);
                $sw = true;
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    $sw = false;
                }else if($dato->{'valor'} == $valor && $dato->{'valorx'} == $valorx && $dato->{'valory'} == $valory && $dato->{'valorz'} == $valorz){
                    $resp = ['mensaje' => "Debe realizar alguna modificación ", 'tipo' => "info", 'titulo' => "Oops.!"];
                    $sw = false;
                }else{
                    $dato = $this->evaluacion_model->valor_parametro($idparametro,$valor);
                    if(!empty($dato) && $dato->{'id'} != $id){
                        $resp = ['mensaje' => "El Nombre ya se encuentra registrado", 'tipo' => "info", 'titulo' => "Oops.!"];
                        $sw = false;                        
                    }

                    if($sw){
                        $data = [
                            'idparametro' => $idparametro,
                            'valor' => $valor,
                            'valorx' => $valorx,
                            'valory' => $valory,
                            'valorz' => $valorz,
                            'usuario_registra' => $_SESSION['persona'],
                        ];
                        $add = $this->pages_model->modificar_datos($data, 'valor_parametro', $id);
                        $resp = ['mensaje' => "La información fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                        if($add != 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }   
			}
		}
		echo json_encode($resp);
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

    function traer_valores_permisos() {
        $idparametro = $this->input->post("idparametro");
        $idvalorparametro = $this->input->post("idvalorparametro");
        $datos = $this->Super_estado ? $this->evaluacion_model->traer_valores_permisos($idparametro, $idvalorparametro) : array();
        echo json_encode($datos);
    }

    public function obtener_permisos_parametros(){
      $parametro = $this->input->post('id');
      $permisos = $this->Super_estado == true ? $this->evaluacion_model->obtener_permisos_parametro($parametro) : array();
      echo json_encode($permisos);
    }
    
    public function habilitar_permiso() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $vp_secundario = $this->input->post("vp_secundario");
            $vp_principal= $this->input->post("vp_principal");
            $vp_principal_id = $this->input->post("vp_principal_id");
            $vp_secundario_id = $this->input->post("vp_secundario_id");
            $peso_porcentaje = $this->input->post("peso_porcentaje");
            $idparametro = $this->input->post("id_parametro_permiso");
            if (empty($vp_principal_id)) {
                $resp= ['mensaje'=>"Seleccione Valor parametro principal",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (empty($vp_secundario_id)) {
                $resp= ['mensaje'=>"Seleccione Valor parametro segundario",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                if($idparametro === 215 || $idparametro === 223){
                    if (empty($peso_porcentaje)) $resp= ['mensaje'=>"Debe Ingresar el peso porcentual",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else{
                    $existe = $this->genericas_model->verificar_permiso($vp_principal_id,$vp_secundario_id);
                    if (empty($existe)) {           
                        $vp_secundario = empty($vp_secundario) ? null :  $vp_secundario;
                        $vp_principal = empty($vp_principal) ? null :  $vp_principal;
                        $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Permiso Asignado.!"];
                        $data = [
                            'vp_principal' => $vp_principal,
                            'vp_secundario' => $vp_secundario,
                            'vp_principal_id' => $vp_principal_id,
                            'vp_secundario_id' => $vp_secundario_id,
                        ];
                        $add = $this->pages_model->guardar_datos($data,'permisos_parametros');
                        if($add != 1) $resp= ['mensaje'=>"Error al asignar el permiso, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        else{
                            if($idparametro == 215 || $idparametro == 223){
                                $id_vp = $this->evaluacion_model->traer_registro_id($vp_principal, 'permisos_parametros', 'vp_principal');
                                $id_permiso_parametro = $id_vp ->{'id'};
                                $data_eval = [
                                    'id_permiso_parametro' => $id_permiso_parametro,
                                    'porcentaje' => $peso_porcentaje,
                                    'id_usuario_registra' => $_SESSION['persona'],
                                ];
                                $add_per = $this->pages_model->guardar_datos($data_eval,'evaluacion_permisos');
                                if($add_per != 1) $resp= ['mensaje'=>"Error al asignar el peso porcentual, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                            }
                        }
                    }else{
                        $resp= ['mensaje'=>"El permiso ya fue habilitado anteriormente.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    }
                }

            }  
        }
      echo json_encode($resp);
    }

    public function deshabilitar_permiso() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id_permiso = $this->input->post("id_permiso");
            $idparametro = $this->input ->post("id_parametro_permiso");
            if (empty($id_permiso)) {
                $resp= ['mensaje'=>"Seleccione el permiso a Deshabilitar",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $add = $this->genericas_model->eliminar_datos($id_permiso,'permisos_parametros');
                if($add != 2) $resp= ['mensaje'=>"Error al Deshabilitado el permiso, contacte con el administrador",'tipo'=>"error",'titulo'=> "Oops.!"];
                else{   
                    $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Permiso Deshabilitado.!",'porcentaje'=> ''];
                    if($idparametro == 215 || $idparametro == 223){
                        $data = $this->evaluacion_model->traer_registro_id($id_permiso, 'evaluacion_permisos', 'id_permiso_parametro');
                        $id_permiso_eval = $data->{'id'};
                        $fecha_elimina = date("Y-m-d H:i:s");
                        $data_eval = [
                            'estado' => 0,
                            'fecha_elimina' => $fecha_elimina,
                            'id_usuario_elimina' => $_SESSION['persona'],
                        ];                        
                        $mod = $this->pages_model->modificar_datos($data_eval,'evaluacion_permisos', $id_permiso_eval);
                        $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Permiso Deshabilitado.!",'porcentaje'=> $data->{'porcentaje'}];
                        if($mod != 1) $resp= ['mensaje'=>"Error al Deshabilitar el peso porcentual, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }             
            }  
        }
      echo json_encode($resp);
    }


    public function guardar_solicitud(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $evaluado = $this->input->post('evaluado');
                $id_metodo = $this->input->post('id_metodo');
                $id_jefe = $this->input->post('id_jefe');
                $id_coevaluado = $this->input->post('id_coevaluado');
                $periodo = $this->input->post('periodo');
                $str = $this->verificar_campos_string(['Método de Evaluación' => $id_metodo, 'Evaluado' => $evaluado, 'Jefe Inmediato' => $id_jefe, 'Coevaluado' => $id_coevaluado, 'Periodo' => $periodo]);
                $sw = true;
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $info = $this->evaluacion_model->validar_funcionario_evalucion($evaluado,$periodo);
                    if($info){
                        $resp = ['mensaje'=>"El funcionario ya tiene una evaluación asignada sin gestionar!.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        $sw = false;
                    }
                    if($sw){
                        $data = [
                            'id_metodo_eval' => $id_metodo,
                            'id_evaluado' => $evaluado,
                            'id_estado_eval' => 'Eval_Env',
                            'jefe_inmediato' => $id_jefe,
                            'coevaluacion' => $id_coevaluado,
                            'periodo' => $periodo,
                            'id_usuario_registra' => $_SESSION['persona'],
                        ];
                        $add = $this->pages_model->guardar_datos($data, 'evaluacion_solicitud');
                        if($add){
                            $id_solicitud = $this->evaluacion_model->traer_registro_id($_SESSION['persona'], 'evaluacion_solicitud', 'id_usuario_registra');
                            $resp = ['mensaje' => "La solicitud fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id' => $id_solicitud->{'id'}];
                            $data = [
                              'solicitud_id' => $id_solicitud->{'id'},
                              'estado_id' => 'Eval_Env',
                              'id_usuario_registra' => $_SESSION['persona'],
                            ];
                            $res_estado = $this->pages_model->guardar_datos($data, 'evaluacion_estado_solicitudes');
                        }else $resp = ['mensaje'=>"Error al guardar la solicitud, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }   
			}
		}
		echo json_encode($resp);
    }

    public function gestionar_solicitud(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
				$id = $this->input->post('id');
				$nextState = $this->input->post('nextState');
				$success = $this->input->post('success');
				$aux = $this->validar_estado($id, $nextState);
				// if ($aux == 1) {
                    if($nextState == 'Eval_Act_Env') $data = ['id_estado_eval' => $nextState, 'acta_enviada' => 1];
                    else $data = ['id_estado_eval' => $nextState];
                    $mod = $this->pages_model->modificar_datos($data, 'evaluacion_solicitud', $id);
                    if ($mod) {
                        $resp = ['mensaje' => $success, 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
                        $data = [
                            'solicitud_id' => $id,
                            'estado_id' => $nextState,
                            'id_usuario_registra' => $_SESSION['persona']
                        ];
                        $res_estado = $this->pages_model->guardar_datos($data, 'evaluacion_estado_solicitudes');
                    }else $resp = ['mensaje' => 'Se presentó un error al modificar la solicitud. Por favor contacte con el administrador del sistema.','tipo' => 'error','titulo' => 'Ooops'];
				// }elseif ($aux === -2) $resp = ['mensaje' => 'La solicitud ya fue gestionada anteriormente.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
			} else $resp = ['mensaje' => 'No tiene permisos para gestionar esta solicitud.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
        }
        echo json_encode($resp);
    }

    public function listar_tipo_evaluador(){
        if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $res = array();
            $id_solicitud = $this->input->post('id_solicitud');
            $ver = '<span title="Ver Evaluación" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default seleccionar red"></span>';
            $editar = '<span title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-edit btn btn-default editar red"></span>';
            $info = $this->evaluacion_model->get_solicitud($id_solicitud);
            $sw =  ($info->{'tipo_evaluador'} == 0 && $info->{'parte1'} == 0 && $info->{'parte2'} == 0) ? true : false;
            $data = $this->evaluacion_model->obtener_tipo_evaluador($id_solicitud);
            foreach ($data as $row) {
                if($row['valory'] != 4){
                    switch($row['valory']){
                        case '1':
                            $row['nombre_evaluado'] = $info->{'nombre_completo'};
                            $row['gestion'] = $ver;
                            break;
                        case '2':
                            $row['nombre_evaluado'] = $info->{'nombre_coevaluado'};
                            $row['gestion'] = ($sw) ? $ver .' '. $editar : $ver;
                            break;
                        case '3':
                            $row['nombre_evaluado'] = $info->{'nombre_jefe'};
                            $row['gestion'] = ($sw) ? $ver .' '. $editar : $ver;
                            break;
                    }
                    array_push($res,$row);
                }                
            }
        }
		echo json_encode($res);
    }
    
    public function validar_estado($id, $nextState){
		$info = $this->evaluacion_model->get_solicitud($id);
		$tipo_solicitud = $info->{'id_metodo_eval'};
		$state = $info->{'id_estado_eval'};
        $aux_user = ($state == 'Eval_Sol' || $state == 'Eval_Env' || $state == 'Eval_Pro' ) ? true : false;
        if ($this->admin || $aux_user) {
            if($state === 'Eval_Sol' && ($nextState === 'Eval_Env' || $nextState === 'Eval_Can')){
                // Cambio de estado permitido.
                return 1;
            }else{
                if($state === 'Eval_Env' &&  ($nextState === 'Eval_Env' || $nextState === 'Eval_Can')){
                    // Cambio de estado permitido.
                    return 1;
                }else{
                    if($state === 'Eval_Pro' &&  $nextState === 'Eval_Pro'){
                        // Cambio de estado permitido.
                        return 1;
                    }
                }
            }
        }else{
            // Cambio de estado no permitido.
			return -2;
        }
    }

    public function obtener_tipo_evaluador(){
        $id_solicitud = $this->input->post('id_solicitud');
        $data = $this->Super_estado == true ? $this->evaluacion_model->obtener_tipo_evaluador($id_solicitud) : array();
        echo json_encode($data);
    }

    public function obtener_indicadores(){
        $id_solicitud = $this->input->post('id_solicitud');
        $estado = $this->input->post('estado');
        $filtro = $this->input->post('id_estado');
        $sindicador = $this->evaluacion_model->obtener_personal_sinindicadores($id_solicitud);
        if($sindicador){ // si hay personas sin indicadores, se les asigna terminado
            foreach ($sindicador as $row){
                $mod = $this->pages_model->modificar_datos(['evaluacion' => '1','id_estado' => 'Eval_Ter'], 'evaluacion_asignacion_persona', $row['id_asignacion']);        
            }
        }
        $data = $this->Super_estado == true ? $this->evaluacion_model->obtener_indicadores($id_solicitud,$estado,$filtro) : array();
        echo json_encode($data);
    }
    
    public function obtener_preguntas(){
        if (!$this->Super_estado) $resp = array();
		else {
            $tipo_evaluador = $this->input->post('id_tipo_evaluador');
            $id_aux = $this->input->post('id_aux');
            $permisos = [];
            if($id_aux === 'Eval_Per'){
                $id_evaluado = $this->input->post('ind_evaluado');
                $periodo = $this->input->post('periodo_eval');
                $info = $this->evaluacion_model->get_solicitud('',$id_evaluado,'',$periodo);
                $tipoE = $this->evaluacion_model->obtener_permisos_parametro('', $info->{'id_metodo_eval'}, '');
                $clave = array_search(1, array_column($tipoE, 'valory'));
                $tipo_evaluador = $tipoE[$clave]['id'];
                $per = $this->evaluacion_model->obtener_preguntas($tipo_evaluador);
                $data = $this->evaluacion_model->traer_registro_id($id_aux, 'valor_parametro', 'id_aux');
                $id_personal_cargo = $data->{'id'};
                foreach ($per as $row) {
                    $row['id_tipo_evaluador'] = $id_personal_cargo;
                    array_push($permisos,$row);
                }
            }else $permisos = $this->evaluacion_model->obtener_preguntas($tipo_evaluador);

            $resp = $permisos;
        }
        echo json_encode($resp);
    }

    public function obtener_preguntas_indicador(){
        if (!$this->Super_estado) $resp = array();
		else {
            $id_evaluado = $this->input->post('id_evaluado');
            $periodo = $this->input->post('periodo');
            $permisos = $this->evaluacion_model->obtener_preguntas_indicador($id_evaluado, $periodo);
            $resp = $permisos;
        }
        echo json_encode($resp);
    }

    public function guardar_respuestas(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $data_respuestas = $this->input->post('data_respuestas');
                $id_solicitud = $this->input->post('id_solicitud');
                $tipo_evaluador = $this->input->post('tipo_evaluador');
                $tipo = $this->input->post('tipo');
                $id_asignacion_persona = $this->input->post('id_asignacion_persona');
                $data = array();
                $nextState = 'Eval_Pro';
                $parte1 = 0;
                $parte2 = 0;
                $sw = true;
                $id_pregunta = 0;
                $info = $this->evaluacion_model->get_solicitud($id_solicitud);
                if($info->{'parte1'} == 0 && ($info->{'id_estado_eval'} == 'Eval_Pro' || $info->{'id_estado_eval'} == 'Eval_Env')){ //validar que el estado de la primerar parte de la eval no este finalizado y el estado de la solicitud                 
                    foreach ($data_respuestas as $row) {
                        if($row['id_respuesta'] == ''){
                             $sw = false;
                             $id_pregunta = $row['id_pregunta'];
                            break;
                        }                       
                    }   
                    if(!$sw){
                        $q = $this->evaluacion_model->traer_registro_id($id_pregunta, 'valor_parametro', 'id');
                        $resp = ['mensaje'=>"No ha respondido:". $q->{'valor'},'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        foreach ($data_respuestas as $row) {
                            $row['id_usuario_registra'] = $_SESSION['persona'];
                            array_push($data,$row);
                        }
                        $add = $this->pages_model->guardar_datos($data, 'evaluacion_respuesta', 2);
                        $add = true;
                        if($add){
                            $indicadores = $this->evaluacion_model->obtener_indicadores($id_solicitud,'','vacio'); // valida si hay indicadores                           
                            if($tipo == 1){ // personal a cargo 
                                $state = null;
                                if(!$indicadores) $state = 'Eval_Ter';
                                $get_ind = $this->evaluacion_model->obtener_indicadores_funciones_formacion($id_asignacion_persona);
                                if(!$get_ind) $state = 'Eval_Ter';
                                $mod_respuesta = $this->pages_model->modificar_datos(['id_estado' => $state, 'evaluacion' => 1], 'evaluacion_asignacion_persona', $id_asignacion_persona);
                                $evaluaciones = $this->evaluacion_model->obtener_indicadores($id_solicitud,'0'); // valida si aun hay personas por evaluar
                                if(!$evaluaciones) $tipo_evaluador++;
                            }else $tipo_evaluador++; // se incrementa key tipo_evaluador

                            $data_tipo_evaluador = $this->evaluacion_model->obtener_tipo_evaluador($id_solicitud);
                            $sw = (!array_key_exists($tipo_evaluador, $data_tipo_evaluador)) ? false : true;
                            if($sw){
                                if($data_tipo_evaluador[$tipo_evaluador]['valory'] == '4'){
                                    if(!$indicadores) $tipo_evaluador++;
                                }
                            }
                            $sw = (!array_key_exists($tipo_evaluador, $data_tipo_evaluador)) ? false : true;
                            $indicadores = $this->evaluacion_model->obtener_indicadores($id_solicitud,'','vacio',1); // valida si hay indicadores 
                            if(!$indicadores && !$sw){ // si el siguiente tipo evaluador no existe y si no hay indicadores se termina la evaluacion
                                $tipo_evaluador = 0; 
                                $nextState = 'Eval_Ter';
                                $parte1 = 1;
                                $parte2 = 1;
                            }else{
                                if($indicadores && !$sw){ // si hay indicadores y no existe tipo evaluador, se finaliza la primera parte
                                    $tipo_evaluador = 0; 
                                    $parte1 = 1;
                                    $parte2 = 0;
                                }
                            }
                            $resp = ['mensaje' => "Evaluación guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id_estado' => $nextState];
                            $data_estado = ['id_estado_eval' => $nextState,'tipo_evaluador' => $tipo_evaluador,'parte1' => $parte1,'parte2' => $parte2];
                            $mod_estado = $this->pages_model->modificar_datos($data_estado, 'evaluacion_solicitud', $id_solicitud);
                            if(!$mod_estado) $resp = ['mensaje'=>"Error al guardar estado de la evaluación, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!", 'id_estado' => $nextState];
                            else{
                                $estado = $this->evaluacion_model->traer_registro_id($id_solicitud, 'evaluacion_estado_solicitudes', 'solicitud_id');
                                if($estado->{'estado_id'} != $nextState){
                                    $data_estado = [
                                        'solicitud_id' => $id_solicitud,
                                        'estado_id' => $nextState,
                                        'id_usuario_registra' => $_SESSION['persona']
                                    ];
                                    $add_estado = $this->pages_model->guardar_datos($data_estado, 'evaluacion_estado_solicitudes');
                                }  
                            }              
                            
                        }else $resp = ['mensaje'=>"Error al guardar respuestas, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }
            }
        } 
        echo json_encode($resp);
    }

    public function guardar_respuestas_indicadores(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $data_respuestas_metas = $this->input->post('data_metas');
                $data_respuesta_funciones = $this->input->post('data_funciones');
                $id_solicitud = $this->input->post('id_solicitud');
                $id_asignacion_persona = $this->input->post('id_asignacion_persona');
                $data = array();
                $nextState = 'Eval_Pro';
                $parte2 = 0;
                $sw = true;
                $swf = true;
                $id_pregunta = 0;
                $info = $this->evaluacion_model->get_solicitud($id_solicitud);
                if($info->{'parte2'} == 0 && ($info->{'id_estado_eval'} == 'Eval_Pro' || $info->{'id_estado_eval'} == 'Eval_Env')){ 
                    if($data_respuestas_metas){
                        foreach ($data_respuestas_metas as $row) {
                            if($row['resultado'] == ''){
                                $sw = false;
                                $id_pregunta = $row['id_pregunta'];
                                break;
                            }                       
                        }    
                    }  
                    if(!$sw){
                        $q = $this->evaluacion_model->traer_registro_id($id_pregunta, 'talentocuc_indicadores', 'id');
                        $resp = ['mensaje'=>"No ha respondido:". $q->{'pregunta'},'tipo'=>"info",'titulo'=> "Oops.!"];
                    }else{
                        if($data_respuesta_funciones){
                            foreach ($data_respuesta_funciones as $row) {
                                if($row['respuesta'] == ''){
                                    $swf = false;
                                    $id_pregunta = $row['id_pregunta'];
                                    break;
                                }                       
                            }
                        }

                        if(!$swf){
                            $p = $this->evaluacion_model->traer_registro_id($id_pregunta, 'talentocuc_funciones', 'id');
                            $resp = ['mensaje'=>"No ha respondido:". $p->{'pregunta'},'tipo'=>"info",'titulo'=> "Oops.!"];
                        }
                    }    

                    if($sw && $swf){
                        $canf=0;
                        $x=0;
                        $sum_funciones = 0;
                        if($data_respuesta_funciones){
                            $canf = count($data_respuesta_funciones);
                            foreach ($data_respuesta_funciones as $row) {
                                $mod_respuesta = $this->pages_model->modificar_datos(['respuesta' => $row['respuesta']], 'talentocuc_funciones', $row['id_pregunta']);
                                if($mod_respuesta){
                                    $x++;
                                    $p = $this->evaluacion_model->traer_registro_id($row['respuesta'], 'valor_parametro', 'id');
                                    $sum_funciones += $p->{'valorx'};
                                }
                            }
                        }

                        $can=0;
                        $i=0;
                        $suma=0;
                        if($data_respuestas_metas){
                            $can = count($data_respuestas_metas);
                            foreach ($data_respuestas_metas as $row) {
                                $mod_respuesta = $this->pages_model->modificar_datos(['resultado' => $row['resultado'],'cumplimiento' => $row['cumplimiento']], 'talentocuc_indicadores', $row['id_pregunta']);
                                if($mod_respuesta){
                                    $i++;
                                    $suma += $row['cumplimiento'];
                                }
                            }  
                        }  
                                    
                        if($i == $can && $canf = $x){
                            $p = $info->{'periodo'} != '2020' ? $info->{'periodo'} : null;
                            $evaluado = $this->evaluacion_model->traer_registro_id($id_asignacion_persona, 'evaluacion_asignacion_persona', 'id');
                            $puntos = $this->evaluacion_model->obtener_puntuacion_mayor($p);

                            $puntuacion_func = round(($sum_funciones/$x), 1, PHP_ROUND_HALF_ODD);
                            $res = $this->evaluacion_model->valor_parametro(224,$puntuacion_func, null, $p, 'valory');
                            $promedio_funciones = (float)$res->{'valorx'};    

                            $cant = $this->evaluacion_model->obtener_formacion_esencial($evaluado->{'evaluado'}, $info->{'periodo'});
                            $cant_s = $this->evaluacion_model->obtener_formacion_esencial($evaluado->{'evaluado'}, $info->{'periodo'}, 1);
                            $promedio_formacion = (count($cant_s)/count($cant))*100;
                            $promedio_meta = 0;
                            $resultado = 0;
                            $puntuacion_meta = 0;
                            if($suma > 0){
                                $promedio_meta = $suma/$i;  
                                $resultado = ($promedio_meta*$puntos)/100; 
                                $puntuacion_meta = round($resultado, 1, PHP_ROUND_HALF_ODD);
                            }
                            $pgeneral = ($promedio_funciones+$promedio_formacion+$promedio_meta)/3;
                            $promedio_general = round($pgeneral, 1, PHP_ROUND_HALF_ODD);

                            $data = [
                                'evaluado' => $evaluado->{'evaluado'},
                                'periodo' => $info->{'periodo'},
                                'promedio_funciones' => $promedio_funciones,
                                'promedio_formacion' => $promedio_formacion,
                                'promedio_meta' => $promedio_meta,
                                'promedio_general' => $promedio_general,
                                'resultado_meta' => $resultado,
                                'puntuacion_meta' => $puntuacion_meta,
                                'id_usuario_registra' => $_SESSION['persona'],
                            ];
                            $add = $this->pages_model->guardar_datos($data, 'evaluacion_asignacion_preguntas');
                            if(!$add)$resp = ['mensaje'=>"Error al guardar el resultado de los indicadores, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!", 'id_estado' => $nextState];
                            else{
                                $mod_estado_ind = $this->pages_model->modificar_datos(['id_estado' => 'Eval_Ter'], 'evaluacion_asignacion_persona', $id_asignacion_persona);
                                $indicadores = $this->evaluacion_model->obtener_indicadores($id_solicitud,'','vacio',1);
                                if(!$indicadores){ // si no hay indicadores se termina la evaluacion
                                    $nextState = 'Eval_Ter';
                                    $parte2 = 1;
                                }
                                $resp = ['mensaje' => "Evaluación guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id_estado' => $nextState];
                                $data_estado = ['id_estado_eval' => $nextState,'parte2' => $parte2];
                                $mod_estado = $this->pages_model->modificar_datos($data_estado, 'evaluacion_solicitud', $id_solicitud);
                                if(!$mod_estado) $resp = ['mensaje'=>"Error al guardar estado de la evaluación, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!", 'id_estado' => $nextState];
                                else{
                                    $estado = $this->evaluacion_model->traer_registro_id($id_solicitud, 'evaluacion_estado_solicitudes', 'solicitud_id');
                                    if($estado->{'estado_id'} != $nextState){
                                        $data_estado = [
                                            'solicitud_id' => $id_solicitud,
                                            'estado_id' => $nextState,
                                            'id_usuario_registra' => $_SESSION['persona']
                                        ];
                                        $add_estado = $this->pages_model->guardar_datos($data_estado, 'evaluacion_estado_solicitudes');
                                    }  
                                } 
                            }             
                            
                        }else $resp = ['mensaje'=>"Error al guardar respuestas, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];

                    }
                }
            }
        } 
        echo json_encode($resp);
    }

    public function listar_personal_cargo(){
        if (!$this->Super_estado) $res = array();
		else {
            $res = array();
            $id_solicitud = $this->input->post('id_solicitud');
            $id_estado_eval = $this->input->post('id_estado_eval');
            $btn_eliminar = '<span style="color: #6e1f7c;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash btn btn-default eliminar" ></span>';
            $btn_ver = '<span style="color: #6e1f7c;" title="Ver evaluacion" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default seleccionar" ></span>';
            $sw = ($id_estado_eval == 'Eval_Sol' || $id_estado_eval == 'Eval_Env' || $id_estado_eval == 'Eval_Pro') ? true : false;
            $data = $this->evaluacion_model->listar_personal_cargo($id_solicitud);
            foreach ($data as $row) {
                $row['accion'] = ($this->administra) ? $btn_ver.' '.$btn_eliminar :  $btn_ver;
                array_push($res,$row);
            }
        }
        echo json_encode($res);
    }

    public function get_evaluacion_respuestas(){
        $id_solicitud = $this->input->post('id_solicitud');
        $tipoevaluador = $this->input->post('tipoevaluador');
        $id_evaluado = $this->input->post('id_evaluado');
        // $id_aux_evaluado = $id_aux;
        // $solicitud = $this->evaluacion_model->get_solicitud($id_solicitud);
        // if($id_aux == 'Eval_Coe' ||  $id_aux == 'Eval_Coe_270'){
        //     $evaluado_sol = $this->evaluacion_model->get_solicitud('',$solicitud->{'id_coevaluado'},'');
        //     if($evaluado_sol->{'id_metodo_eval'} == 'Eval_360') $id_aux_evaluado = 'Eval_Coe';
        //     else $id_aux_evaluado = 'Eval_Coe_270';
        // }else if($id_aux == 'Eval_Per'){
        //     $id_aux_evaluado = 'Eval_Jef';
        // }
        // $data = $this->Super_estado == true ? $this->evaluacion_model->get_evaluacion_respuestas($id_solicitud, $id_aux_evaluado, $id_evaluado) : array();
        $data = $this->Super_estado == true ? $this->evaluacion_model->get_evaluacion_respuestas($id_solicitud, $tipoevaluador, $id_evaluado) : array();
        echo json_encode($data);
        // echo json_encode(['id_solicitud' => $id_solicitud, 'tipo_evaluador' => $tipoevaluador, 'evaluado' => $id_evaluado]);
    }

    public function get_respuestas_indicadores(){
        $resp = [];
        if ($this->Super_estado){
            $id_solicitud = $this->input->post('id_solicitud');
            $id_persona = $this->input->post('id_evaluado');
            $periodo = $this->input->post('periodo');
            $data = $this->evaluacion_model->get_respuestas_indicadores($id_solicitud,$id_persona,$periodo);
            foreach ($data as $row) {
                $row['respuesta'] = is_null($row['respuesta']) && !is_null($row['cumplimiento']) ? $row['cumplimiento'] : $row['respuesta'];
                array_push($resp, $row);
            }
        }
        echo json_encode($resp);
    }

    public function get_respuestas_formacionEsc(){
        $resp = [];
        if ($this->Super_estado){
            $id_solicitud = $this->input->post('id_solicitud');
            $id_persona = $this->input->post('id_evaluado');
            $periodo = $this->input->post('periodo');
            $resp = $this->evaluacion_model->evaluacion_respuestas_formacionForm($id_persona,$periodo,'talentocuc_formacion_esencial');
        }
        echo json_encode($resp);
    }

    public function get_respuestas_funciones(){
        $resp = [];
        if ($this->Super_estado){
            $id_solicitud = $this->input->post('id_solicitud');
            $id_persona = $this->input->post('id_evaluado');
            $periodo = $this->input->post('periodo');
            $resp = $this->evaluacion_model->evaluacion_respuestas_formacionForm($id_persona,$periodo,'talentocuc_funciones');
        }
        echo json_encode($resp);
    }

    public function enviar_notificaciones(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $resultado = $this->input->post('vb_cal');
                $periodo = $this->input->post('filtroperiodo');
                $id_estado = $this->input->post('id_estado');
                $fecha_inicio = $this->input->post('fecha_inicio');
                $fecha_fin = $this->input->post('fecha_fin');
                $mensaje = $this->input->post('mensaje');
                $str = $this->verificar_campos_string(['Estado' => $id_estado, 'Mensaje' => $mensaje]);
                $sw = false;
                $data = array(); 
                $new_estado = '';                    
                if($resultado == 1){
                    $data_evaluaciones = $this->evaluacion_model->obtener_evaluaciones_anotificar($id_estado, 'Eval_360', $fecha_inicio, $fecha_fin, $periodo, $resultado);
                    // $new_estado = 'Eval_Act_Env';
                    $data_estado = ['id_estado_eval' => 'Eval_Act_Env','acta_enviada' => 1];
                    $sw = true;
                }else{
                    $data_evaluaciones = $this->evaluacion_model->obtener_evaluaciones_anotificar($id_estado, '', $fecha_inicio, $fecha_fin, $periodo);
                    // $new_estado = 'Eval_Env';
                    $data_estado = ['id_estado_eval' => 'Eval_Env'];
                    $sw = $id_estado == 'Eval_Sol' ? true : false;
                }
                
                if($data_evaluaciones){
                    $resp = ['mensaje'=>"",'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'data_correos' => $data_evaluaciones, 'msj' => $mensaje];
                    if($sw){                                                  
                        foreach ($data_evaluaciones as $row) {
                            $mod = $this->pages_model->modificar_datos($data_estado, 'evaluacion_solicitud', $row['id']);
                            if ($mod) {
                                array_push($data, [ 
                                    "solicitud_id" => $row['id'], 
                                    "estado_id" => $new_estado,
                                    "id_usuario_registra" => $_SESSION['persona'],
                                    ]);
                            }
                        }

                        $add = $this->pages_model->guardar_datos($data, 'evaluacion_estado_solicitudes', 2);
                        if(!$add) $resp = ['mensaje'=>"Error al actualizar estado solicitud, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }else $resp = ['mensaje'=>"No se encontraron evaluaciones, intente de nuevo!.",'tipo'=>"info",'titulo'=> "Oops.!"];
            }
        }
        echo json_encode($resp);
    }
    
    public function exportar_excel_evaluacion($id, $estado, $tipo, $fecha_i, $fecha_f, $periodo){
        if($id == 0) $id = '';
		if($estado === 'vacio') $estado = '';
		if($tipo === 'vacio') $tipo = '';
		if($fecha_i == 0) $fecha_i = '';
		if($fecha_f == 0) $fecha_f = '';
        $data = array();
        $solicitudes = $this->evaluacion_model->exportar_evaluaciones($id, $estado, $tipo, $fecha_i, $fecha_f, $periodo);
        foreach ($solicitudes as $sol) {
            array_push($data, [ 
                "Periodo" => $sol['periodo'],
                "Departamento" => $sol['departamento'], 
                "Nombre Evaluador" => $sol['nombre_evaluador'],
                "Cédula Evaluador" => $sol['cc_evaluador'],
                "Estado" => $sol['estado'],
                "Tipo Evaluación" => $sol['tipo_evaluador'],
                "Nombre Evaluado" => $sol['nombre_evaluado'],
                "Cédula Evaluado" => $sol['identificacion_evaluado'],                
                "Area/Meta" => $sol['area_co'],
                "Competencia/Resultado" => $sol['competencia'],
                "Pregunta" => $sol['pregunta'],
                "Respuesta/Cumplimiento" => $sol['respuesta']
                ]);
        }

        $ind = $this->evaluacion_model->exportar_evaluaciones_indicadores($id, $estado, $tipo, $fecha_i, $fecha_f, $periodo);
        $respuesta = '';
        $area_co = '';
        $competencia = '';
        foreach ($ind as $rows) {
                if($rows['periodo'] != '2020'){
                    $area_co = $rows['meta'];
                    $competencia = $rows['resultado'];
                    $respuesta = $rows['cumplimiento'];
                }else{
                    $respuesta = $rows['respuesta'];
                    $area_co = $rows['area_co'];
                    $competencia = $rows['competencia'];
                }
            array_push($data, [ 
                "Periodo" => $rows['periodo'],
                "Departamento" => $rows['departamento'], 
                "Nombre Evaluador" => $rows['nombre_evaluador'],
                "Cédula Evaluador" => $rows['cc_evaluador'],
                "Estado" => $rows['estado'],
                "Tipo Evaluación" => $rows['tipo_evaluador'],
                "Cédula Evaluado" => $rows['nombre_evaluado'],
                "Nombre Evaluado" => $rows['cc_evaluado'],
                "Area/Meta" => $area_co,
                "Competencia/Resultado" => $competencia,
                "Pregunta" => $rows['pregunta'],
                "Respuesta/Cumplimiento" => $respuesta
                ]);
        }
        
        $datos["datos"] = $data;
        $datos["nombre"] = "Evaluacion_administrativa";
        $datos["leyenda"] = "";
        $datos["titulo"] = "Evaluación Administrativa";
        $datos["version"] = "";
        $datos["trd"] = "";
        $datos["fecha"] = "";
        $datos["col"] = 12;
        $datos["cantidad"] = '';
        $this->load->view('templates/exportar_excel', $datos);
    }

    public function exportar_excel_resultados($id,$estado,$fecha_i,$fecha_f){
        $data_res = array();
        $solicitudes = $this->evaluacion_model->listar_solicitudes($id, $estado, '', $fecha_i, $fecha_f,'');

        $datos["datos"] = $data_res;
        $datos["nombre"] = "Evaluacion_administrativa_resultados";
        $datos["leyenda"] = "";
        $datos["titulo"] = "Evaluación Administrativa Resultados";
        $datos["version"] = "";
        $datos["trd"] = "";
        $datos["fecha"] = "";
        $datos["col"] = 9;
        $this->load->view('templates/exportar_excel', $datos);
    }

    public function gestionar_personal_acargo(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
				$id = $this->input->post('id_asignacion_persona');
                $data = ['estado' => 0];
                $mod = $this->pages_model->modificar_datos($data, 'evaluacion_asignacion_persona', $id);
                $resp = ['mensaje' => 'Eliminado con exito!.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
                if (!$mod) $resp = ['mensaje' => 'Se presentó un error al elimniar personal a cargo. Por favor contacte con el administrador.','tipo' => 'error','titulo' => 'Ooops'];
            } else $resp = ['mensaje' => 'No tiene permisos para gestionar esta solicitud.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
        }
        echo json_encode($resp);
    }

    public function guardar_persona_acargo(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_solicitud = $this->input->post('id_solicitud_evaluado');
                $cc_persona = $this->input->post('id_evaluado');
                $solicitud = $this->evaluacion_model->get_solicitud($id_solicitud);
                $dato = $this->evaluacion_model->validar_personal_aCargo($cc_persona,$solicitud->{'id_evaluado'},$solicitud->{'periodo'});
                if($dato){
                    $resp = ['mensaje' => 'EL funcionario ya se encuentra asignado.','tipo' => 'info','titulo' => 'Ooops', 'id_estado_eval' => $solicitud->{'id_estado_eval'}];
                }else{
                    $data = [
                        'evaluador' => $solicitud->{'id_evaluado'},
                        'evaluado' => $cc_persona,
                        'periodo' => $solicitud->{'periodo'},
                        'id_usuario_registra' => $_SESSION['persona']
                        ];
                    $add = $this->pages_model->guardar_datos($data, 'evaluacion_asignacion_persona');
                    $resp = ['mensaje' => 'Guardado con exito!.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!', 'id_estado_eval' => $solicitud->{'id_estado_eval'}];
                    if (!$add) $resp = ['mensaje' => 'Se presentó un error al guardar personal a cargo. Por favor contacte con el administrador.','tipo' => 'error','titulo' => 'Ooops', 'id_estado_eval' => $solicitud->{'id_estado_eval'}];
                }
            } else $resp = ['mensaje' => 'No tiene permisos para gestionar esta solicitud.', 'tipo' => 'error', 'titulo' => 'Ooops!', 'id_estado_eval' => $solicitud->{'id_estado_eval'}];
        }
        echo json_encode($resp);
    }

    public function modificar_solicitud(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
                $id_solicitud = $this->input->post('id_solicitud');
                $cc_persona = $this->input->post('identificacion');
                $evaluacion = $this->input->post('evaluacion');
                $dato = null;
                if($evaluacion == 2) $dato = 'coevaluacion';
                else if($evaluacion == 3) $dato = 'jefe_inmediato';
                $data = [$dato => $cc_persona];
                $mod = $this->pages_model->modificar_datos($data, 'evaluacion_solicitud', $id_solicitud);
                $solicitud = $this->evaluacion_model->listar_solicitudes($id_solicitud,'','','','','');
                $resp = ['mensaje' => 'Solicitud gestionada con exito!.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!', 'data' => $solicitud[0]];
                if (!$mod) $resp = ['mensaje' => 'Se presentó un error al modificar la solicitud. Por favor contacte con el administrador.','tipo' => 'error','titulo' => 'Ooops'];
            } else $resp = ['mensaje' => 'No tiene permisos para gestionar esta solicitud.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
        }
        echo json_encode($resp);
    }

    public function obtener_resultado_evaluacion(){
        if (!$this->Super_estado) $resp = array();
		else {        
            $id_solicitud = $this->input->post('id_solicitud');
            $puntuacion_directa = 0;
            $puntuacion_centil = 0;
            $valoracion = '';
            $resultado = $this->evaluacion_model->obtenerResultado($id_solicitud);
            if($resultado){
                $puntuacion_directa = $resultado->{'puntuacion_directa'};
                $puntuacion_centil = $resultado->{'puntuacion_centil'};
                $valoracion = $resultado->{'valoracion'};
            }
            $resp = ['puntuacion_directa'=> $puntuacion_directa, 'puntuacion_centil' => $puntuacion_centil, 'valoracion' => $valoracion];
        }
        echo json_encode($resp);
    }
    public function calcularResultados(){
        $id_solicitud = $this->input->post('id_solicitud');
        $solicitud = $this->evaluacion_model->get_solicitud($id_solicitud);
        $resp = $this->ejecutarCalculoResultados($id_solicitud, $solicitud->{'id_evaluado'},$solicitud->{'periodo'},$solicitud->{'resultado'});
        echo json_encode($resp);
    }

    public function calcularResultadosMasivo(){
        $resp = [];
        if (!$this->Super_estado) $resp;
		else {
            $id_metodo = $this->input->post('id_metodo_eval');
            $fecha_inicio = $this->input->post('fecha_inicio');
            $fecha_fin = $this->input->post('fecha_fin');
            $periodo = $this->input->post('periodo');
            $estado = $this->input->post('id_estado');
            $btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            $btn_detalles  = '<span title="Detalle Resultados" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default detalle_resultado" style="color:#6e1f7c"></span>';
            
            $solicitudes = $this->evaluacion_model->listar_solicitudes('', $estado, $id_metodo, $fecha_inicio, $fecha_fin, $periodo);
            foreach ($solicitudes as $solicitud) {
                $data = $this->ejecutarCalculoResultados($solicitud['id'], $solicitud['id_evaluado'], $solicitud['periodo'], $solicitud['resultado']);
                $data_resp = [ 
                    'id_solicitud_evaluado' => $solicitud['id'],
                    'metodo' => $solicitud['tipo'],
                    'evaluado' => $solicitud['evaluado'],
                    'cc_evaluado' => $solicitud['cc_evaluado'],
                    'resultado' => $data['resultado'],
                    'valoracion' => (!$data['resultado']) ? $data['mensaje'] :  $data['valoracion'],
                    'accion' => ($data['resultado']) ? $btn_detalles : $btn_inhabil,
                ];
                array_push($resp, $data_resp); 
            }
        }
        echo json_encode($resp);
    }
    
    public function ejecutarCalculoResultados($id_solicitud, $identificacion, $periodo, $resultado_eval){
        if (!$this->Super_estado) $resp = array();
		else { 
            // $id_solicitud = $this->input->post('id_solicitud');
            // $identificacion = $solicitud->{'id_evaluado'};
            // $periodo = $solicitud->{'periodo'};
            $metodo = null;
            $sw = true;
            $ev = null;
            $puntuacion_directa = null;
            $valoracion = null;
            $respuestas_jefe = [];
            $respuestas_coe = [];
            $respuestas_auto = [];
            $respuestas_personal = [];
            // if(is_null($solicitud->{'resultado'})){
            if(is_null($resultado_eval)){

                $porc_areas_apre = $this->evaluacion_model->obtenerPorcentajes($id_solicitud);
                 // preguntas y respuestas
                $tipoEvaluador_solicitud = $this->evaluacion_model->obtener_tipo_evaluador($id_solicitud);
                foreach ($tipoEvaluador_solicitud as $row) {
                    switch($row['valory']){                    
                        case 3:
                            $respuestas_jefe = $this->evaluacion_model->obtenerPreguntasAreaAprec_jefe($identificacion,$periodo);
                            if(!$respuestas_jefe){
                                $sw = false;
                                $ev = $row['valorx'];
                            }
                            break;
                        case 2:
                            $respuestas_coe = $this->evaluacion_model->obtenerPreguntasAreaAprec_coe($identificacion,$periodo);
                            if(!$respuestas_coe){
                                $sw = false;
                                $ev = $row['valorx'];
                            }          
                            break;
                        case 1:
                            $respuestas_auto = $this->evaluacion_model->obtenerPreguntasAreaAprec_auto($identificacion,$periodo);
                            if(!$respuestas_auto){
                                $sw = false;
                                $ev = $row['valorx'];
                            }
                            break;
                        case 4:
                            $metodo = true;
                            $respuestas_personal = $this->evaluacion_model->obtenerPreguntasAreaAprec_per($identificacion,$periodo);
                            if(!$respuestas_personal){
                                $sw = false;
                                $ev = $row['valorx'];
                            }                            
                            break;      
                    }
                }
                if($sw){
                    $validar_ind = $this->evaluacion_model->validar_indicadores_funciones($identificacion,$periodo);
                    if($periodo != '2020' && ($validar_ind->{'indicadores'} > 0 || $validar_ind->{'funciones'} > 0) && !$validar_ind->{'promedio_general'}) $resp = ['mensaje' => "La evaluación no ha sido terminada en Indicadores.", 'tipo' => 'info','titulo' => 'Oops','resultado' => $puntuacion_directa,'valoracion' => $valoracion];
                    else{
                        $respuestas_metas = $this->evaluacion_model->obtenerPreguntasAreaAprec_Met($identificacion,$periodo);
                        if($respuestas_metas){
                            $respuestas_jefe_metas = array_merge($respuestas_jefe,$respuestas_metas);
                        }else $respuestas_jefe_metas = $respuestas_jefe;
                        //calcular promedios
                        if($respuestas_auto) $contarRespuestas_aut = $this->contarRespuestasAreaApre($respuestas_auto, $porc_areas_apre, $id_solicitud);
                        if($respuestas_jefe_metas) $contarRespuestas_jef = $this->contarRespuestasAreaApre($respuestas_jefe_metas, $porc_areas_apre, $id_solicitud, 0, $periodo, 3);
                        if($respuestas_coe) $contarRespuestas_coe = $this->contarRespuestasAreaApre($respuestas_coe, $porc_areas_apre, $id_solicitud);                
                        if($metodo)$contarRespuestas_per = $this->contarRespuestasAreaApre_personal($respuestas_personal, $porc_areas_apre, $id_solicitud);
                        //se suman los resultados de cada area de apreciacion para obtner el valor de cada tipo de evaluador
                        if($respuestas_auto) $data = $this->calcularResultado_tipoEvaluador($contarRespuestas_aut,[],[],$id_solicitud);
                        if($respuestas_jefe_metas) $data = $this->calcularResultado_tipoEvaluador($contarRespuestas_jef, $data[0], $data[1], $id_solicitud);
                        if($respuestas_coe) $data = $this->calcularResultado_tipoEvaluador($contarRespuestas_coe, $data[0], $data[1], $id_solicitud);

                        //llenar tablas 1 y 2
                        if($metodo){
                            $data_detalle = array_merge($data[0],$contarRespuestas_per['detalle']) ; // tabla resultado detalle concatenado con la de personal a cargo
                            $data_tipoEvaluador = array_merge($data[1],$contarRespuestas_per['respFinEvaluador']); // tabla resultado tipo evaluador concatenado con la de personal a cargo
                        }else{
                            $data_detalle = $data[0] ;
                            $data_tipoEvaluador = $data[1];
                        }
                        //llenar tabla final de resultado
                        $suma_final = 0;
                        foreach ($data_tipoEvaluador as $key ) {
                            $suma_final += $key['producto'];
                        }
                        
                        //consultar la valoracion y puntuación centil
                        $p = $periodo == '2020' ? $periodo : null;
                        $puntuacion_directa = round($suma_final, 1);
                        $resultado = $this->evaluacion_model->valor_parametro(224,$puntuacion_directa, null, $p, 'valory');
                        $puntuacion_centil = (float)$resultado->{'valorx'};                
                        $estado = $this->evaluacion_model->listar_valor_parametro(222,null,null,$periodo,'valorb');
                        foreach ($estado as $es) {
                            if($puntuacion_centil >= (int)$es['valorx'] && $puntuacion_centil <= (int)$es['valory']){
                                $valoracion = $es['valor'];
                                break;
                            }
                        }
                        
                        $data_final = [
                            'id_solicitud' => $id_solicitud, 
                            'valor_completo' => $suma_final, 
                            'puntuacion_directa' => $puntuacion_directa, 
                            'puntuacion_centil' => $puntuacion_centil,
                            'valoracion' => $valoracion];
                        
                        $add1 = $this->pages_model->guardar_datos($data_detalle, 'evaluacion_resultado_detalle', 2);
                        $add2 = $this->pages_model->guardar_datos($data_tipoEvaluador, 'evaluacion_resultado_tipo_evaluador', 2);
                        $add3 = $this->pages_model->guardar_datos($data_final, 'evaluacion_resultado_final');
                        $add4 = $this->pages_model->modificar_datos(['resultado' => 1], 'evaluacion_solicitud', $id_solicitud);

                        $resp = ['mensaje' => "Puntuación directa: ".$puntuacion_directa,'tipo' => 'success','titulo' => 'Resultado!','resultado' => $puntuacion_directa,'valoracion' => $valoracion];
                    }
                        // echo json_encode([$data_detalle,$data_tipoEvaluador,$data_final]); 
                }else $resp = ['mensaje' => "La evaluación no ha sido terminada en ".$ev, 'tipo' => 'info','titulo' => 'Oops','resultado' => $puntuacion_directa,'valoracion' => $valoracion];
            }else{
                $resultado = $this->evaluacion_model->obtenerResultado($id_solicitud);
                if($resultado){
                    $resp = ['mensaje' => "Puntuación Directa: ".$resultado->{'puntuacion_directa'},'tipo' => 'success','titulo' => 'Resultado!','resultado' => $resultado->{'puntuacion_directa'},'valoracion' => $resultado->{'valoracion'}];
                }  
            }
        }
        // echo json_encode($resp);
        return $resp;
    }

    public function calcularResultado_tipoEvaluador($respuestas,$detalle,$evaluadores,$id_solicitud){
        $suma = 0;
        foreach ($respuestas as $key ) {
            $suma += $key['final'];
            array_push($detalle, $key);
        }
        array_push($evaluadores, [
            'id_solicitud' => $id_solicitud, 
            'id_tipo_evaluador' => $respuestas[0]['id_tipo_evaluador'], 
            'suma' => $suma, 
            'producto' => $suma*($respuestas[0]['porcentaje_tipo_evaluador']/100),
            'porcentaje' => $respuestas[0]['porcentaje_tipo_evaluador'] ]);
        return [$detalle,$evaluadores];
    }

    public function contarRespuestasAreaApre($respuestas, $porc_areas_apre, $id_solicitud,$id_evaluado=0, $periodo = '', $tipo = ''){
        $resp_g = [];
        $resp_c = [];
        foreach ($respuestas as $r) {
            $suma = 0;
            $total = 0;
            $porcentaje = 0;
            $porcentaje_tipo_evaluador = 0;
            if (!in_array($r['area_apre'],$resp_g, true)) {
                foreach ($respuestas as $r2) {
                    if ($r['area_apre'] == $r2['area_apre']) {
                        $suma += $r2['rs'];
                        $total++;
                    }
                }
                foreach ($porc_areas_apre as $ap) {
                    if($ap['id_tipo_evaluador'] == $r['id_tipo_evaluador']){
                        $porcentaje_tipo_evaluador = $ap['porcentaje_tipo_evaluador'];                       
                    }
                    if($ap['id_tipo_evaluador'] == $r['id_tipo_evaluador'] && $ap['area_apre'] == $r['area_apre']){
                        if($periodo == '2021P' && $tipo == 3){// si es la evaluación de profesores 2021P
                            $porcentaje = $ap['id_aux_area_apre'] == 'Eval_Comp' ? 90 : $ap['porcentaje_area'];
                        }else $porcentaje = $ap['porcentaje_area'];                       
                    }
                }
                array_push($resp_g,$r['area_apre']);
                array_push($resp_c, 
                    [ 
                    "id_solicitud" => $id_solicitud,
                    "id_evaluador" => $id_evaluado,    
                    "id_tipo_evaluador" => $r['id_tipo_evaluador'],   
                    "area_apre" => $r['area_apre'],
                    "suma" => $suma, 
                    "total" => $total, 
                    "promedio" => $suma / $total, 
                    "porcentaje" => $porcentaje, 
                    "final" => (($suma/$total) * ($porcentaje / 100)),
                    "porcentaje_tipo_evaluador" => $porcentaje_tipo_evaluador,
                    ]);
            }
        }
        return $resp_c;
    }

    public function contarRespuestasAreaApre_personal($respuestas, $porc_areas_apre, $id_solicitud){
        $resp_g = [];
        $resp_c = [];
        $respFinal = [[],[]];
        $areas_agrupadas_persona = [];
        $suma = 0;
        $total = 0;
        $respFinEvaluador = [];
        foreach ($respuestas as $r) {
            $respuestas_indi = [];
            if (!in_array($r['id_evaluado'],$resp_g, true)) {        

                foreach ($respuestas as $r2) {
                    if ($r['id_evaluado'] == $r2['id_evaluado']) {
                        array_push($respuestas_indi,$r2);
                    }
                }
                $resultados = $this->contarRespuestasAreaApre($respuestas_indi, $porc_areas_apre, $id_solicitud, $r['id_evaluado']);
                array_push($resp_c,['resultados' => $resultados, 'evaluado' => $r['id_evaluado']] );                  
                array_push($resp_g,$r['id_evaluado']); 
            }  
        }
        
        foreach($resp_c as $row){
            $respFinal = $this->calcularResultado_tipoEvaluador($row['resultados'],$respFinal[0], $respFinal[1],$id_solicitud);         
        }

        foreach ($porc_areas_apre as $ap) {
            if($ap['id_aux_tipo_evaluador'] == 'Eval_Per'){
                $data_evaluador = $ap;
            break;                      
            }
        }
        $areas_agrupadas_persona2 = [];
        foreach ($respFinal[0] as $r1) {
            $suma_a = 0;
            if (!in_array($r1['id_evaluador'],$areas_agrupadas_persona, true) && $r1['id_evaluador']) {
                foreach ($respFinal[0] as $r2) {
                    if ($r1['id_evaluador'] == $r2['id_evaluador']) {
                        $suma_a += $r2['final'];
                    }    
                }
                $suma += $suma_a;
                $total++;
                array_push($areas_agrupadas_persona, $r1['id_evaluador']); 
                array_push($areas_agrupadas_persona2, $suma_a); 
            }
        }
         array_push($respFinEvaluador,[ 
                'id_solicitud' => $id_solicitud, 
                'id_tipo_evaluador' => $data_evaluador['id_tipo_evaluador'], 
                'suma' => $suma/$total, 
                'producto' => ($suma / $total )*($data_evaluador['porcentaje_tipo_evaluador']/100),
                'porcentaje' => $data_evaluador['porcentaje_tipo_evaluador']
                ]);
          
        return ['respFinEvaluador' => $respFinEvaluador, 'detalle' => $respFinal[0]];
    }

    public function ejecutarReset_resultado($id_solicitud){
        if (!$this->Super_estado) $resp = array();
		else { 
            $w = "id_solicitud = $id_solicitud";
            $final = $this->evaluacion_model->eliminar_datos($w,'evaluacion_resultado_final');
            $tipo_evaluador = $this->evaluacion_model->eliminar_datos($w,'evaluacion_resultado_tipo_evaluador');
            $detalle = $this->evaluacion_model->eliminar_datos($w,'evaluacion_resultado_detalle');
            $mod = $this->pages_model->modificar_datos(['resultado' => NULL], 'evaluacion_solicitud', $id_solicitud);
            $resultado = $this->evaluacion_model->obtenerResultado($id_solicitud);
            if($resultado) $resp = ['mensaje' => "Puntuación Directa: ".$resultado->{'puntuacion_directa'},'resultado' => $resultado->{'puntuacion_directa'},'valoracion' => $resultado->{'valoracion'}];
            else $resp = ['mensaje' => "",'resultado' => '','valoracion' => ''];
        }
        return $resp;
    }

    public function Resetear_resultados(){
        $resp = [];
        if (!$this->Super_estado) $resp;
		else {
            $id_metodo = $this->input->post('id_metodo_eval');
            $fecha_inicio = $this->input->post('fecha_inicio');
            $fecha_fin = $this->input->post('fecha_fin');
            $periodo = $this->input->post('periodo');
            $estado = $this->input->post('id_estado');
            $btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            $btn_detalles  = '<span title="Detalle Resultados" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default detalle_resultado" style="color:#6e1f7c"></span>';
            
            $solicitudes = $this->evaluacion_model->listar_solicitudes('', $estado, $id_metodo, $fecha_inicio, $fecha_fin, $periodo);
            foreach ($solicitudes as $solicitud) {
                $data = $this->ejecutarReset_resultado($solicitud['id']);
                $data_resp = [ 
                    'id_solicitud_evaluado' => $solicitud['id'],
                    'metodo' => $solicitud['tipo'],
                    'evaluado' => $solicitud['evaluado'],
                    'cc_evaluado' => $solicitud['cc_evaluado'],
                    'resultado' => $data['resultado'],
                    'valoracion' => ($data['resultado']) ? $data['valoracion'] : $data['mensaje'],
                    'accion' => ($data['resultado']) ? $btn_detalles : $btn_inhabil,
                ];
                array_push($resp, $data_resp); 
            }
        }
        echo json_encode($resp);
    }

    public function get_resultados_detalles(){
        $data = array();
		if ($this->Super_estado) {
			$id_solicitud = $this->input->post('id_solicitud');
			$data = $this->evaluacion_model->get_resultados_detalles($id_solicitud);  
		}
		echo json_encode($data);
    }

    public function get_resultados_tipoevaluador(){
        $data = array();
		if ($this->Super_estado) {
			$id_solicitud = $this->input->post('id_solicitud');
			$data = $this->evaluacion_model->get_resultados_tipoevaluador($id_solicitud);  
		}
		echo json_encode($data);
    }

    public function listar_personal_actas(){
        if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $id_solicitud = $this->input->post('id_solicitud');
            $btn_config = '<span title="Gestionar Acta" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;font-size: large;" class="btn btn-default fa fa-edit gestionar"></span>';
            $btn_inhabil  = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            $btn_finalizar = '<span title="Finalizar acta" data-toggle="popover" data-trigger="hover" style="color: #5cb85c;font-size: large;" class="btn btn-default fa fa-thumbs-up fin"></span>';;
            $valores = array();
            $sw = false;
            $bg_color = 'white';
            $color = 'black';
            $data = $this->evaluacion_model->listar_personal_actas($id_solicitud);
            foreach ($data as $row) {
                $info = $this->evaluacion_model->traer_registro_id($row['identificacion'], 'evaluacion_resultado_competencia','id_persona');
                $sw = !$info ? false : true;           
                switch($row['id_estado_eval']){
                    case 'Eval_Env':
                    case 'Eval_Pro':
                        $bg_color = 'white';
                        $color = 'black';
                        $row['accion'] =  $btn_inhabil;
                        break;
                    case 'Eval_Ter':
                        $bg_color = 'white';
                        $color = 'black';
                        $row['accion'] =  $row['resultado'] == null ? $btn_inhabil :  ($sw ? $btn_config .' '. $btn_finalizar : $btn_config);
                        break;
                    case'Eval_Act_Env':                           
                            $bg_color = $row['acta_retro'] == 1 ? '#5cb85c' : ($sw ? '#337ab7' : 'white');
                            $color = $row['acta_retro'] == 1 ? 'white' : ($sw ? 'white' : 'black');
                            $row['accion'] =  $row['acta_retro'] == 1 ? $btn_inhabil : ($row['resultado'] == null ? $btn_inhabil : ($sw ? $btn_config .' '. $btn_finalizar : $btn_config));
                        break;
                    case 'Eval_Act_Pro':
                            $bg_color = $row['acta_retro'] == 1 ? '#5cb85c' : '#337ab7';
                            $color = 'white';
                            $row['accion'] =  $row['acta_retro'] == 1 ? $btn_inhabil : $btn_config .' '. $btn_finalizar;
                        break;
                    case 'Eval_Act_Fin':
                    case 'Eval_Form':
                    case 'Eval_Cer':
                        $bg_color = 'white';
                        $color = 'black';
                        $row['accion'] =  $row['resultado'] == null || $row['acta_retro'] == 1 ? $btn_inhabil : ($sw ? $btn_config .' '. $btn_finalizar : $btn_config);
                        break;
                }
                $row['ver'] = "<span  style='background-color: $bg_color;color: $color; width: 100%;' class='pointer form-control'><span >ver</span></span>";
                array_push($valores,$row);
            }
		}
		echo json_encode($valores);  
    }

    public function listar_oportunidades_mejora(){
        if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $id_evaluado = $this->input->post('id_evaluado');
            $id_solicitud = $this->input->post('id_solicitud_evaluado');
            $btn_modificar = '<span title="Modificar" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
            $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
            $valores = array();
            $data = $this->evaluacion_model->listar_oportunidades_mejora($id_evaluado,$id_solicitud);
            foreach ($data as $row) {
                $row['accion'] = $btn_modificar. ' '.$btn_eliminar;
                array_push($valores,$row);
            }
		}
		echo json_encode($valores); 
    }

    public function listar_sugerencias_formacion(){
        if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $id_evaluado = $this->input->post('id_evaluado');
            $id_solicitud = $this->input->post('id_solicitud_evaluado');
            $btn_modificar = '<span title="Modificar" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-wrench btn btn-default modificar"></span>';
            $btn_eliminar = '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
            $valores = array();
            $data = $this->evaluacion_model->listar_sugerencias_formacion($id_evaluado,$id_solicitud);
            foreach ($data as $row) {
                $row['accion'] = $btn_modificar. ' '.$btn_eliminar;
                array_push($valores,$row);
            }
		}
		echo json_encode($valores); 
    }

    public function get_detalle_resultados(){
        if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $id_evaluado = $this->input->post('id_evaluado');
            $id_solicitud_evaluado = $this->input->post('id_solicitud_evaluado');
            $periodo = $this->input->post('periodo');
            $respuestas_jefe = [];
            $respuestas_coe = [];
            $respuestas_auto = [];
            $respuestas_personal = [];
            $cant=0;
            // $info = $this->evaluacion_model->traer_registro_id($id_evaluado, 'evaluacion_resultado_competencia','id_persona');
            $info = $this->evaluacion_model->listar_detalle_resultados($id_evaluado,$id_solicitud_evaluado);
            if(!$info){   
                $tipoEvaluador_solicitud = $this->evaluacion_model->obtener_tipo_evaluador($id_solicitud_evaluado);
                foreach ($tipoEvaluador_solicitud as $rows) {
                    switch($rows['valory']){
                        case 1:
                            $respuestas_auto = $this->evaluacion_model->obtenerPreguntasAreaAprec_auto($id_evaluado,$periodo);
                            break; 
                        case 2:
                            $respuestas_coe = $this->evaluacion_model->obtenerPreguntasAreaAprec_coe($id_evaluado,$periodo);          
                            break;
                        case 3:
                            $respuestas_jefe = $this->evaluacion_model->obtenerPreguntasAreaAprec_jefe($id_evaluado,$periodo);
                            break;
                        case 4:
                            $respuestas_personal = $this->evaluacion_model->obtenerPreguntasAreaAprec_per($id_evaluado,$periodo);
                            $personas_aCargo = $this->evaluacion_model->obtener_indicadores($id_solicitud_evaluado);
                            $cant = count($personas_aCargo); 
                            break;      
                    }
                }      
                // $data_personal = ($respuestas_personal) ? $this->agrupar_resultado_areaCompetencia($respuestas_personal,$tipoEvaluador_solicitud,null,null,$cant) : [];          
                $data_personal = ($respuestas_personal) ? $this->agrupar_resultado_Competencia_personal_Acargo($respuestas_personal,$tipoEvaluador_solicitud,null,null,$cant) : [];          
                $data = array_merge($respuestas_jefe,$respuestas_coe,$respuestas_auto,$data_personal);
                $val = $this->agrupar_resultado_areaCompetencia($data,$tipoEvaluador_solicitud,$id_evaluado,$id_solicitud_evaluado); 
            }else{
                $val = $this->listar_detalle_resultados($id_evaluado, $id_solicitud_evaluado);
            }
             // Ordenar array           
            $valores = $this->array_sort_by($val, 'puntaje', $order = SORT_DESC);
		}
		echo json_encode($valores); 
    }

    public function agrupar_resultado_areaCompetencia($data,$tipoEvaluador_solicitud,$id_evaluado=null,$id_solicitud_evaluado=null,$cant=null){
        $data_resp = [];
        $valores = [];
        $valores2 = [];
        foreach ($data as $row) {
            $suma_a = 0;
            if(!in_array($row['id_competencia'],$valores2, true) && $row['id_competencia']){
                foreach ($data as $r2) {
                    if ($row['id_competencia'] == $r2['id_competencia']) {
                        $clave = array_search($r2['id_tipo_evaluador'], array_column($tipoEvaluador_solicitud, 'id'));
                        $porcentaje = $tipoEvaluador_solicitud[$clave]['porcentaje'];
                        $suma_a += ($r2['rs'] * ($porcentaje/100));
                    }    
                }
                
                if(!$id_evaluado && !$id_solicitud_evaluado) $suma_a = ($suma_a/$cant);  

                $data_resp = [ 
                    'id_persona' => $id_evaluado, 
                    'id_solicitud' => $id_solicitud_evaluado, 
                    'area_apreciacion' => $row['area_apreciacion'],
                    'competencia' => $row['competencia'],
                    'descripcion' => $row['pregunta'],
                    'id_pregunta' => $row['id_pregunta'],
                    'id_competencia' => $row['id_competencia'],
                    'fortaleza' => '<span class="btn btn-default fortaleza" id="f'.$row['id_competencia'].'" style="color: #5cb85c"><span class="fa fa-toggle-off f'.$row['id_competencia'].'" ></span></span>', 
                    'mejora' => '<span class="btn btn-default mejora" id="m'.$row['id_competencia'].'" style="color: #6e1f7c"><span class="fa fa-toggle-off m'.$row['id_competencia'].'" ></span></span>', 
                    'puntaje' => round($suma_a, 1),
                    'id_usuario_registra' => $_SESSION['persona'],
                    'rs' => $suma_a,
                    'id_tipo_evaluador' => $row['id_tipo_evaluador'],
                ];
                array_push($valores2, $row['id_competencia']); 
                array_push($valores, $data_resp); 
            }
        }
        return $valores;
    }

    public function agrupar_resultado_Competencia_personal_Acargo($data,$tipoEvaluador_solicitud,$id_evaluado=null,$id_solicitud_evaluado=null,$cant=null){
        $data_resp = [];
        $valores = [];
        $valores2 = [];
        $promedio = 0;
        foreach ($data as $row) {
            $suma_a = 0;
            if(!in_array($row['id_competencia'],$valores2, true) && $row['id_competencia']){
                foreach ($data as $r2) {
                    if ($row['id_competencia'] == $r2['id_competencia']) {
                        $suma_a += $r2['rs'];
                    }    
                }
                
                $promedio = ($suma_a/$cant);  

                $data_resp = [ 
                    'id_persona' => $id_evaluado, 
                    'id_solicitud' => $id_solicitud_evaluado, 
                    'area_apreciacion' => $row['area_apreciacion'],
                    'competencia' => $row['competencia'],
                    'descripcion' => $row['pregunta'],
                    'id_pregunta' => $row['id_pregunta'],
                    'id_competencia' => $row['id_competencia'],
                    'fortaleza' => '<span class="btn btn-default fortaleza" id="f'.$row['id_competencia'].'" style="color: #5cb85c"><span class="fa fa-toggle-off f'.$row['id_competencia'].'" ></span></span>', 
                    'mejora' => '<span class="btn btn-default mejora" id="m'.$row['id_competencia'].'" style="color: #6e1f7c"><span class="fa fa-toggle-off m'.$row['id_competencia'].'" ></span></span>', 
                    'puntaje' => round($promedio, 1),
                    'id_usuario_registra' => $_SESSION['persona'],
                    'rs' => $promedio,
                    'id_tipo_evaluador' => $row['id_tipo_evaluador'],
                ];
                array_push($valores2, $row['id_competencia']); 
                array_push($valores, $data_resp); 
            }
        }
        return $valores;
    }

    public function listar_detalle_resultados($id_evaluado, $id_solicitud_evaluado){
        $valores = [];
        $classF = '';
        $classM = '';
        $data = $this->evaluacion_model->listar_detalle_resultados($id_evaluado, $id_solicitud_evaluado);
        foreach ($data as $row) {
            $classF = $row['fortaleza'] == 1 ? 'fa-toggle-on' : 'fa-toggle-off'; 
            $classM = $row['mejora'] == 1 ? 'fa-toggle-on' : 'fa-toggle-off';
            $data_resp = [ 
                'id' => $row['id'],
                'id_persona' => $row['id_persona'],
                'id_solicitud' => $row['id_solicitud'],
                'area_apreciacion' => $row['area_apreciacion'],
                'competencia' => $row['competencia'],
                'descripcion' => $row['pregunta'],
                'id_competencia' => $row['id_competencia'],
                'fortaleza' => '<span class="btn btn-default fortaleza" id="f'.$row['id_competencia'].'" style="color: #5cb85c"><span class="fa '.$classF.' f'.$row['id_competencia'].'" ></span></span>', 
                'mejora' => '<span class="btn btn-default mejora" id="m'.$row['id_competencia'].'" style="color: #6e1f7c"><span class="fa '.$classM.' m'.$row['id_competencia'].'" ></span></span>', 
                'puntaje' => $row['puntaje'],
                'id_usuario_registra' => $_SESSION['persona'],
            ];
            array_push($valores, $data_resp); 
        }
        return $valores;
    }

    public function guardar_resultado_competencias(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $data_respuestas = $this->input->post('data_respuestas');
                $id_persona = $this->input->post('id_persona');
                $id_solicitud_evaluado = $this->input->post('idsolicitud_evaluado');
                // $info = $this->evaluacion_model->traer_registro_id($id_persona, 'evaluacion_resultado_competencia','id_persona');  
                $info = $this->evaluacion_model->listar_detalle_resultados($id_persona,$id_solicitud_evaluado);              
                if(!$info){
                    $add = $this->pages_model->guardar_datos($data_respuestas, 'evaluacion_resultado_competencia', 2);
                    $resp = ['mensaje' => "Resultados guardados exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                    if(!$add) $resp = ['mensaje'=>"Error al guardar resultados, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    else{
                        $dato = $this->evaluacion_model->get_solicitud($id_solicitud_evaluado);
                        if($dato->{'id_estado_eval'} == 'Eval_Ter'){
                            $data = ['id_estado_eval' => 'Eval_Act_Pro','fecha_retroalimentacion' => date("Y-m-d")];
                            $mod = $this->pages_model->modificar_datos($data, 'evaluacion_solicitud', $id_solicitud_evaluado);                 
                            $data_estado = [
                                'solicitud_id' => $id_solicitud_evaluado,
                                'estado_id' => 'Eval_Act_Pro',
                                'id_usuario_registra' => $_SESSION['persona'],
                            ];
                            $res_estado = $this->pages_model->guardar_datos($data_estado, 'evaluacion_estado_solicitudes'); 
                        }
                    } 

                }else{
                    if(!$data_respuestas) $resp = ['mensaje'=>"Debe realizar alguna modificación.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    else{
                        foreach ($data_respuestas as $row){
                            $data = ['fortaleza' => $row['fortaleza'],'mejora' => $row['mejora']];
                            $mod = $this->pages_model->modificar_datos($data, 'evaluacion_resultado_competencia', $row['id']);        
                        }
                        $resp = ['mensaje' => "Resultados guardados exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                    }
                }
            }
        }
        echo json_encode($resp); 
    }

    public function guardar_sugerencias(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_solicitud = $this->input->post('id_solicitud_evaluado');
                $id_evaluado = $this->input->post('id_evaluado');
                $sugerencias = $this->input->post('sugerencias');
                $str = $this->verificar_campos_string(['sugerencias' => $sugerencias]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data = [ 
                        'observacion' => $sugerencias,
                        'id_evaluado' => $id_evaluado, 
                        'id_solicitud' => $id_solicitud,
                        'id_usuario_registra' => $_SESSION['persona']
                    ];
                    $add = $this->pages_model->guardar_datos($data, 'evaluacion_sugerencias_formacion');
                    $resp = ['mensaje' => "La información fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                    if($add != 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp); 
    }

    public function modificar_sugerencia(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
                $id = $this->input->post('id_sugerencia');
                $sugerencias = $this->input->post('sugerencias');
                $str = $this->verificar_campos_string(['Sugerencia' => $sugerencias]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $dato = $this->evaluacion_model->traer_registro_id($id, 'evaluacion_sugerencias_formacion','id');
                    if($dato->{'observacion'} == $sugerencias){
                        $resp = ['mensaje' => "Debe realizar alguna modificación ", 'tipo' => "info", 'titulo' => "Oops.!"];
                    }else{
                        $data = ['observacion' => $sugerencias];
                        $add = $this->pages_model->modificar_datos($data, 'evaluacion_sugerencias_formacion', $id);
                        $resp = ['mensaje' => "La información fue modificada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                        if($add != 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function guardar_compromisos(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_evaluado = $this->input->post('id_evaluado');
                $id_solicitud_evaluado = $this->input->post('id_solicitud_evaluado');
                $compromiso = $this->input->post('compromiso');
                $str = $this->verificar_campos_string(['Compromiso' => $compromiso]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $data = [ 
                        'id_solicitud' => $id_solicitud_evaluado, 
                        'id_evaluado' => $id_evaluado, 
                        'actividad' => $compromiso, 
                        'id_usuario_registra' => $_SESSION['persona'],
                    ];
                    $add = $this->pages_model->guardar_datos($data, 'evaluacion_compromisos');
                    $resp = ['mensaje' => "La información fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                    if($add != 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    // else{
                    //     $dato = $this->evaluacion_model->get_solicitud($id_solicitud_evaluado);
                    //     if($dato->{'id_estado_eval'} === 'Eval_Ter'){
                    //         $data = ['id_estado_eval' => 'Eval_Act_Pro','fecha_retroalimentacion' => date("Y-m-d")];
                    //         $add = $this->pages_model->modificar_datos($data, 'evaluacion_solicitud', $id_solicitud_evaluado);                 
                    //         $data_estado = [
                    //             'solicitud_id' => $id_solicitud_evaluado,
                    //             'estado_id' => 'Eval_Act_Pro',
                    //             'id_usuario_registra' => $_SESSION['persona'],
                    //         ];
                    //         $res_estado = $this->pages_model->guardar_datos($data_estado, 'evaluacion_estado_solicitudes'); 
                    //     }
                    // }
                }
            }
        }
        echo json_encode($resp); 
    }

    public function modificar_compromisos(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
                $id_compromiso = $this->input->post('id_compromiso');
                $compromiso = $this->input->post('compromiso');
                $str = $this->verificar_campos_string(['Compromiso' => $compromiso]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $dato = $this->evaluacion_model->traer_registro_id($id_compromiso, 'evaluacion_compromisos','id');
                    if($dato->{'actividad'} == $compromiso){
                        $resp = ['mensaje' => "Debe realizar alguna modificación ", 'tipo' => "info", 'titulo' => "Oops.!"];
                    }else{
                        $data = ['actividad' => $compromiso];
                        $add = $this->pages_model->modificar_datos($data, 'evaluacion_compromisos', $id_compromiso);
                        $resp = ['mensaje' => "La información fue modificada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                        if($add != 1) $resp = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function finalizar_acta(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
                $id_solicitud_evaluado = $this->input->post('id_solicitud_evaluado');
                $id_solicitud = $this->input->post('id_solicitud');
                $firma = $this->adjuntar_firma("firma");
                $val = $this->evaluacion_model->validar_resultado_competencia($id_solicitud_evaluado);
                if(!$val){
                    $resp = ['mensaje'=>"Antes de continuar debe guardar los resultados por competencias.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if($firma == -2 ){
                    $resp = ['mensaje'=>"Antes de continuar debe firmar el acta.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }else if($firma == -1){
                    $resp = ['mensaje'=>"Error al cargar la firma.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $data = ['acta' => 1, 'firma_jefe' => $firma];               
                    $add = $this->pages_model->modificar_datos($data, 'evaluacion_solicitud', $id_solicitud_evaluado);
                    if($add != 1){
                        $resp = ['mensaje'=>"Error al finalizar acta, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }else{
                        $data_actas = $this->evaluacion_model->listar_personal_actas($id_solicitud);
                        $per_actas = $this->evaluacion_model->obtener_personal_sinActas($id_solicitud);
                        if(!$per_actas){
                            $mod = $this->pages_model->modificar_datos(['acta_enviada' => 0, 'fecha_retroalimentacion' => date("Y-m-d")], 'evaluacion_solicitud', $id_solicitud);
                        }
                        $resp = ['mensaje' => "La información fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'data_actas' => $data_actas];
                    }
                }
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
            $success = file_put_contents('archivos_adjuntos/talentohumano/actas/firmas/'.$file, $data);
            return $success ? $file : -1;
        }
          return -2;
    }

    public function guardar_confirmacion_acta(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
                $calificacion = $this->input->post('calificacion');
                $observaciones = $this->input->post('observaciones');
                $id_solicitud = $this->input->post('id_solicitud');
                $firma = $this->adjuntar_firma("firma");
                if($firma == -2 ){
                    $resp = ['mensaje'=>"Antes de continuar debe firmar el recibido.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else if($firma == -1){
                    $resp = ['mensaje'=>"Error al cargar la firma.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $data = ['id_estado_Eval' => 'Eval_Act_Fin', 'recibido' => 1, 'calificacion' => $calificacion, 'observacion' => $observaciones, 'firma' => $firma];               
                    $add = $this->pages_model->modificar_datos($data, 'evaluacion_solicitud', $id_solicitud);
                    if($add != 1){
                        $resp = ['mensaje'=>"Error al confirmar acta, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }else{
                        $resp = ['mensaje' => "La información fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];              
                        $data_estado = [
                            'solicitud_id' => $id_solicitud,
                            'estado_id' => 'Eval_Act_Fin',
                            'id_usuario_registra' => $_SESSION['persona'],
                        ];
                        $res_estado = $this->pages_model->guardar_datos($data_estado, 'evaluacion_estado_solicitudes');
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function exportar_acta($id_solicitud){
        if ($this->Super_estado){
            $data = [];
            $row = $this->evaluacion_model->get_solicitud($id_solicitud);
            $id_evaluado = $row->{'id_evaluado'};
            $jefe = $this->evaluacion_model->get_jefe_asignacionPersonas($id_evaluado, $row->{'periodo'});
            array_push($data, [ 
                "id_solicitud" => $id_solicitud, 
                "nombre_completo" => $row->{'nombre_completo'}, 
                "identificacion" => $id_evaluado,
                "departamento" => $row->{'departamento'},
                "nombre_jefe" => $jefe->{'nombre_jefe'},
                "departamento_jefe" => $jefe->{'departamento_jefe'},
                "cc_jefe_inmediato" => $jefe->{'cc_jefe'},
                "periodo" => $row->{'periodo'},                
                "fecha" => $row->{'fecha_retroalimentacion'},
                "metodo" => $row->{'tipo'},
                "firma_colaborador" => $row->{'firma'},
                "firma_jefe" => $row->{'firma_jefe'},
                ]);
            
            $competencias = $this->evaluacion_model->listar_detalle_resultados($id_evaluado, $id_solicitud);
            $metas = $this->evaluacion_model->obtener_preguntas_indicador($id_evaluado, $row->{'periodo'});
            $resultado_metas = $this->evaluacion_model->obtener_resultado_metas($id_evaluado, $row->{'periodo'});
            $tipo_evaluador = $this->evaluacion_model->get_resultados_tipoevaluador($id_solicitud);
            $resultado_final = $this->evaluacion_model->obtenerResultado($id_solicitud);
            $compromisos = $this->evaluacion_model->listar_oportunidades_mejora($id_evaluado,$id_solicitud);
            $sugerencias = $this->evaluacion_model->listar_sugerencias_formacion($id_evaluado,$id_solicitud);
            $peso_comp = $this->evaluacion_model->obtener_area_apreciacion('Eval_Jef', 'Eval_Comp');
            $peso_cump = $this->evaluacion_model->obtener_area_apreciacion('Eval_Jef', 'Eval_Cump');
            $peso_metas = $this->evaluacion_model->obtener_area_apreciacion('Eval_Jef', 'Eval_Met');

            $info["datos"] = $data;
            $info["competencias"] = $competencias;
            $info["metas"] = $metas;
            $info["resultado_meta"] = $resultado_metas;
            $info["tipo_evaluador"] = $tipo_evaluador;
            $info["puntuacion_centil"] = $resultado_final->{'puntuacion_centil'};
            $info["puntuacion_directa"] = $resultado_final->{'puntuacion_directa'};
            $info["valoracion"] = $resultado_final->{'valoracion'};
            $info["compromisos"] = $compromisos;
            $info["sugerencias"] = $sugerencias;
            $info["peso_comp"] = $peso_comp->{'peso'};
            $info["peso_cump"] = $peso_cump->{'peso'};
            $info["peso_metas"] = $peso_metas->{'peso'};
            $info["version"] = 'VERSIÓN: 11';
            $info["fecha"] = 'NOVIEMBRE 2021';
            $info["trd"] = 'TRD: 700-730-90';
            $this->load->view('templates/exportar_acta_retro', $info);
            return;
        } 
        redirect('/', 'refresh');    
    }

    public function exportar_resultados_competencias($id_evaluado, $id_solicitud){
        if ($this->Super_estado){            
            $valores = [];
            $classF = '';
            $classM = '';
            $data = $this->evaluacion_model->listar_detalle_resultados($id_evaluado, $id_solicitud);
            foreach ($data as $row) {
                $classF = $row['fortaleza'] == 1 ? 'X' : ''; 
                $classM = $row['mejora'] == 1 ? 'X' : '';
                $data_resp = [ 
                    'area_apreciacion' => $row['area_apreciacion'],
                    'competencia' => $row['competencia'],
                    'fortaleza' => $classF, 
                    'mejora' => $classM, 
                    'puntaje' => $row['puntaje'],
                ];
                array_push($valores, $data_resp); 
            }

            $datos["datos"] = $valores;
            $datos["nombre"] = "resultados_".$id_evaluado;
            $datos["leyenda"] = "";
            $datos["titulo"] = "RESULTADO DE LA EVALUACIÓN DE DESEMPEÑO";
            $datos["version"] = "VERSIÓN: 09";
            $datos["trd"] = "TRD: 700-730-90";
            $datos["fecha"] = date("F").' '.date("Y");
            $datos["col"] = 5;
            $this->load->view('templates/exportar_excel', $datos);
        }
            
    }

    public function listar_asignacion_personas(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $id_evaluador = $this->input->post('id_evaluador');
            $resp = $this->evaluacion_model->listar_asignacion_personas($id_evaluador);
		}
		echo json_encode($resp); 
    }

    public function listar_asignacion_indicadores(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $id_evaluado = $this->input->post('id_evaluado');
            $resp = $id_evaluado === '' ? array() : $this->evaluacion_model->obtener_preguntas_indicador($id_evaluado);
		}
		echo json_encode($resp); 
    }

    public function guardar_asignacion_persona(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
                $id_evaluador = $this->input->post('id_evaluador');
                $id_evaluado = $this->input->post('id_evaluado');
                $periodo = $this->input->post('periodo_evaluado');
                $str = $this->verificar_campos_string(['Evaluado' => $id_evaluado, 'Periodo' => $periodo]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $validar = $this->evaluacion_model->validar_personal_aCargo($id_evaluado,$id_evaluador,$periodo);
                    if($validar){
                        $resp = ['mensaje' => "La persona ya se encuentra asignada en el periodo ".$periodo, 'tipo' => "info", 'titulo' => "Oops.!"];
                    }else{
                        $data = [
                            'evaluador' => $id_evaluador, 
                            'evaluado' => $id_evaluado, 
                            'periodo' => $periodo, 
                            'id_usuario_registra ' => $_SESSION['persona']
                        ];               
                        $add = $this->pages_model->guardar_datos($data, 'evaluacion_asignacion_persona');
                        $resp = ['mensaje' => "La información fue guardada exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
                        if($add != 1) $resp = ['mensaje'=>"Error al guardar la asignación, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_planFormacion(){       
        if (!$this->Super_estado) $resp_final = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $id_evaluado = $this->input->post('id_evaluado');
            // $row = $this->evaluacion_model->get_solicitud($id_evaluado);
            $resp_final = $this->evaluacion_model->listar_detalle_resultados($id_evaluado,'',1);         
		}
		echo json_encode($resp_final); 
    }

    public function listar_planformacion_personal(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $resp = [];
            $id_persona = $this->input->post('id_persona');
            $id_competencia = $this->input->post('id_competencia');
            $resp = $this->evaluacion_model->listar_planformacion_personal($id_persona, $id_competencia);
        }
		echo json_encode($resp);
    }

    public function listar_plan_entrenamiento(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            $this->load->model('talento_cuc_model');
            $id_persona = $this->input->post('idpersona');
            $resp = $this->talento_cuc_model->listar_plan_entrenamiento($id_persona);
        }
		echo json_encode($resp);
    }

    public function guardar_periodo_activo(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
            if ($this->Super_agrega) {
                $periodo = $this->input->post('periodo');
                $id = $this->input->post('id');
                $resp = ['mensaje'=>"Documento guardado exitosamente.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
                $add = $this->pages_model->modificar_datos(['valor' => $periodo],'valor_parametro', $id);
                if(!$add) $resp = ['mensaje'=>"Error al guardar información, contacte con el administrador.!",'tipo'=>"info",'titulo'=> "Oops!"];
            }
        }
        echo json_encode($resp);
    }

    public function generar_informe($periodo,$metodo,$tipo_informe){
        $valores = [];
        $personas = 0;
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {   
            $i=0; 
            $tipo = $this->evaluacion_model->traer_registro_id($tipo_informe, 'valor_parametro', 'id_aux');
            switch($tipo_informe){
                case 'Inf_Res':
                    $informe = 'Informe_resultados';
                    $info = $this->evaluacion_model->listar_informe_resultados($metodo, $periodo);
                    $participantes = count($info['resfinal']);
                    foreach($info['resfinal'] as $row){

                        array_push($valores,[
                            'CEDULA' =>  $row['identificacion'],
                            'NOMBRE' =>  $row['nombre_completo'],
                            'VALORACION' =>  $row['valoracion'],
                            'PUNTUACION' => $row['puntuacion'],                           
                            'TIPO EVALUADOR' => 'RESULTADO FINAL',
                        ]);                       
                        foreach($info['evaluador'] as $x){
                            if($x['id_evaluacion'] === $row['id_evaluacion']){
                                array_push($valores,[
                                    'CEDULA' =>  $row['identificacion'],
                                    'NOMBRE' =>  $row['nombre_completo'],
                                    'VALORACION' =>  '',
                                    'PUNTUACION' => round($x['resultado'], 2, PHP_ROUND_HALF_ODD), 
                                    'TIPO EVALUADOR' => $x['tipo_evaluador'],
                                ]);                            
                            }                           
                        }
                    }                    
                    $i=5;
                    break; 
                case 'Inf_Com':
                    $informe = 'Informe_compromisos';
                    $valores = $this->evaluacion_model->listar_informe_compromisos($metodo, $periodo);
                    $participantes = $this->evaluacion_model->get_cantidad_personas($metodo, $periodo, 'evaluacion_compromisos');
                    $i=3;
                    break;     
                case 'Inf_Act':
                    $informe = 'Informe_competencias';
                    $info = $this->evaluacion_model->listar_informe_competencias($metodo, $periodo);
                    $participantes = $this->evaluacion_model->get_cantidad_personas($metodo, $periodo, 'evaluacion_resultado_competencia');
                    foreach($info as $row){
                        array_push($valores,[
                            'IDENTIFICACION' => $row['identificacion'],
                            'NOMBRE' => $row['nombre_completo'],
                            'COMPETENCIA' => $row['competencia'],
                            'OPORTUNIDAD/FORTALEZA' => $row['mejora'] == 1 ? 'OPORTUNIDAD' : 'FORTALEZA',
                        ]);
                    }
                    $i=4;
                    break;     
                case 'Inf_Sug':
                    $informe = 'Informe_sugerencias';
                    $valores = $this->evaluacion_model->listar_informe_sugerencias($metodo, $periodo);
                    $participantes = $this->evaluacion_model->get_cantidad_personas($metodo, $periodo, 'evaluacion_sugerencias_formacion');
                    $i=4;
                    break;     
            }

            $datos["cantidad"] = $participantes;
            $datos["datos"] = $valores;
            $datos["nombre"] = $informe;
            $datos["leyenda"] = "";
            $datos["titulo"] = strtoupper($tipo->{'valor'});
            $datos["version"] = "VERSIÓN: 09";
            $datos["trd"] = "TRD: 700-730-90";
            $datos["fecha"] = date("F").' '.date("Y");
            $datos["col"] = $i;
            $this->load->view('templates/exportar_excel', $datos);
        }
    }

    public function obtener_funciones(){
        if (!$this->Super_estado) $resp = array();
		else {
            $id_evaluado = $this->input->post('id_evaluado');
            $periodo = $this->input->post('periodo');
            $permisos = $this->evaluacion_model->obtener_funciones($id_evaluado, $periodo);
            $resp = $permisos;
        }
        echo json_encode($resp);
    }

    public function obtener_formacion_esencial(){
        if (!$this->Super_estado) $resp = array();
		else {
            $id_evaluado = $this->input->post('id_evaluado');
            $periodo = $this->input->post('periodo');
            $permisos = $this->evaluacion_model->obtener_formacion_esencial($id_evaluado, $periodo);
            $resp = $permisos;
        }
        echo json_encode($resp);
    }

    public function obtener_valor_parametro(){
        if (!$this->Super_estado) $resp = array();
		else {
            $id = $this->input->post('id');
            $resp = $this->genericas_model->obtener_valor_parametro_id_2($id);
        }
        echo json_encode($resp);
    }

}
    