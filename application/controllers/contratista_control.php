<?php
date_default_timezone_set('America/Bogota');
class contratista_control extends CI_Controller
{
    var $ruta_adjuntos = "archivos_adjuntos/contrataciones/";
    var $isSession = false;
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->load->model('contrataciones_model');
        $this->load->model('genericas_model');
        if (isset($_SESSION['contratistaData'])) {
            if (!is_null($_SESSION['contratistaData'])) {
                $this->isSession = $_SESSION['contratistaData']['session'];
                $nombre = $_SESSION['contratistaData']['Nombre'];
                $identity = $_SESSION['contratistaData']['identity'];
                $correo = $_SESSION['contratistaData']['correo'];
            }
        }
    }

    /* Funcion cargar archivos */
    public function cargar_archivo($mi_archivo, $ruta, $nombre)
    {
        $nombre .= uniqid();
        $tipo_archivos = $this->genericas_model->obtener_valores_parametro_aux("For_Adm", 20);
        if (empty($tipo_archivos)) {
            $tipo_archivos = "*";
        } else {
            $tipo_archivos = $tipo_archivos[0]["valor"];
        }
        $real_path = realpath(APPPATH . '../' . $ruta);
        $config['upload_path'] = $real_path;
        $config['file_name'] = $nombre;
        $config['allowed_types'] = $tipo_archivos;
        $config['max_size'] = "0";
        $config['max_width'] = "0";
        $config['max_height'] = "0";

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($mi_archivo)) {
            //*** ocurrio un error
            $data['uploadError'] = $this->upload->display_errors();

            return array(0, $data['uploadError']);
        }

        $data['uploadSuccess'] = $this->upload->data();

        return array(1, $data['uploadSuccess']["file_name"]);
    }

    public function index($token = "")
    {        
        /*$tabla = 'contrataciones';
        $datos = ['contratista'];
        $where = ['token' => $token];
        $res = $this->contrataciones_model->Buscar_Info($tabla, $datos, $where);
        if (!empty($res)) {
            $dataUser = $this->contrataciones_model->Buscar_Info('valor_parametro', ['id', 'valor as nombre', 'valorx as identity', 'valory as correo', 'valorz as password'], ['id' => $res[0]['contratista'], 'idparametro' => 37, 'idparametro' => 1234]);
            if (!empty($dataUser)) {                        
                if (!is_null($dataUser[0]['identity']) && !is_null($dataUser[0]['correo']) && !is_null($dataUser[0]['password'])) {
                    $estado = 1;
                    $mensaje = '';
                }else{
                    $estado = 0;
                    $mensaje = 'No tiene acceso a esta pagina';
                }
            }else{
                $estado = 0;
                $mensaje = 'No tiene acceso a esta pagina';
            }    
        } else {
            $estado = 0;
            $mensaje = 'El link esta errado';
        }*/
        $nombre = '';
        $correo = '';
        $identity = '';

        if (isset($_SESSION['contratistaData'])) {
            if (!is_null($_SESSION['contratistaData'])) {
                $this->isSession = $_SESSION['contratistaData']['session'];
                $nombre = $_SESSION['contratistaData']['Nombre'];
                $identity = $_SESSION['contratistaData']['identity'];
                $correo = $_SESSION['contratistaData']['correo'];
            }
        }

        $data = [
            //'estado' => $estado, 
            'token' => $token,
            //'mensaje' => $mensaje,
            'nombre' => $nombre,
            'identity' => $identity,
            'correo' => $correo,
            'session' => $this->isSession
        ];
        $this->load->view("pages/contratista", $data);
    }

    /* Verificar estados de contratos por si ya esta diligenciado */
    public function verificar_estado($id_solicitud, $estado_nuevo)
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
            $status_check = array();
            $check_cont = $this->contrataciones_model->check_maxId($id_solicitud);
            for ($x = 0; $x < count($check_cont); $x++) {
                $query = $this->contrataciones_model->Verificar_Contratos($id_solicitud, $check_cont[$x]['id']);
                foreach ($query as $row) {
                    array_push($status_check, $row);
                }
            }

            for ($x = 0; $x < count($status_check); $x++) {
                if ($status_check[$x]['id_estado'] == $estado_nuevo) {
                    return true;
                }
            }
        }
    }

    public function guardar_firma()
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
            $id = $this->input->post('id');
            $checkbox = $this->input->post('checkbox');
            $tabla = 'contrataciones';
            $datos = ['firma_contratista' => $checkbox];
            $contrato = $this->contrataciones_model->Listar_Contratos('', $id, true);
            $personasCorreos = [];
            if ($checkbox == 1) {
                $this->contrataciones_model->Actualizar_Info($tabla, $datos, $id);
                if (!is_null($contrato->firma_contratante)) {
                    $nuevo_estado = "Cont_En_Ver";
                    $msg = "Contrato firmado por ambas partes. Pasa a verificacíon para adjuntar la garanita si se requiere.";

                    $check = $this->verificar_estado($id, $nuevo_estado);
                    $this->contrataciones_model->Actualizar_Info($tabla, $datos, $id);
                    if ($check) {
                        $r = ["mensaje" => "Los contratos no pueden pasar por un mismo estado dos veces, consulte con sistemas sobre este error.", "tipo" => "info", "titulo" => "Oops!"];
                        exit(json_encode($r));
                    } else {
                        $arrayToUpd = [
                            "id_solicitud" => $id,
                            "id_estado" => $nuevo_estado,
                            "id_usuario_registra" => $contrato->id_usuario_registra,
                            "observacion" => $msg
                        ];
                        $upd_status = $this->contrataciones_model->Guardar_Info("contrataciones_estados", $arrayToUpd);
                        if ($upd_status) {					
                            $r = ["mensaje" => "El contrato ha sido diligenciado correctamente!", "tipo" => "success", "titulo" => "Muy bien!"];
                            $arrayToAval = ["contrato_estado" => $nuevo_estado];	
                            $this->contrataciones_model->Actualizar_Info("contrataciones", $arrayToAval, $id);					
                        } else {
                            $r = ["mensaje" => "Error al guardar el estado", "tipo" => "error", "titulo" => "Error!"];
                            exit(json_encode($r));
                        }
                    }
                    $personasCorreos = $this->obtener_correos($id);
                }
                
                $r = ["mensaje" => "La firma se guardo correctamente!", "tipo" => "success", "titulo" => "Proceso  exitoso!", 'persona' => $personasCorreos];
            } else {
                $r = ['mensaje' => "Debe marcar la casilla", 'tipo' => "error", 'titulo' => "Oops"];
            }
            exit(json_encode($r));
        }
    }

    public function login()
    {
        try {
            $user = $this->input->post("user");
            $pass = md5($this->input->post("pass"));
            $data = ['id', 'valor as nombre', 'valory as identity', 'valorz as correo'];
            $where = "idparametro IN (37, ".$this->find_idParametro('Contratista')->id.") and valory = '$user' and valora = '$pass' and estado = 1";
            $res = $this->contrataciones_model->Buscar_Info('valor_parametro', $data, $where);
            if (!empty($res)) {
                $cont = $this->contrataciones_model->Listar_Contratos_Tistas($res[0]['id']);
                if (!empty($cont)) {
                    $this->isSession = true;
                    $_SESSION['contratistaData']['session'] = true;
                    $_SESSION['contratistaData']['id'] = $res[0]['id'];
                    $_SESSION['contratistaData']['Nombre'] = $res[0]['nombre'];
                    $_SESSION['contratistaData']['identity'] = $res[0]['identity'];
                    $_SESSION['contratistaData']['correo'] = $res[0]['correo'];
                    $r = ['status' => 0, 'message' => 'Se inicio session correctamente'];
                } else {
                    $r = ['status' => 1, 'message' => 'No tiene acceso a este apartado'];
                }
            } else {
                $r = ['status' => 1, 'message' => 'El usuario o la contraseña son incorrectos'];
            }
        } catch (\Throwable $th) {
            $r = ['status' => 1, 'message' => 'Ocurrio un error inesperado', 'error' => ['Mensaje' => $th->getMessage(), 'Linea' => $th->getLine(), 'Archivo' => $th->getFile()]];
        }
        exit(json_encode($r));
    }

    public function logout()
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
            $this->isSession = false;
            $_SESSION['contratistaData'] = null;
            exit(json_encode(0));
        }
    }

    public function listarContratos()
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        }else{
            $admin = $this->input->post("id");
            if (empty($admin)) {
                $admin = $_SESSION['contratistaData']['id'];
            }

            $res = $this->contrataciones_model->Listar_Contratos_Tistas($admin);
            if (!empty($res)) {
                $contratos = [];
                $idparametro = $this->find_idParametro('Cont_Soli_E')->idpa;
                $estados = $this->contrataciones_model->Buscar_Info('valor_parametro', '*', "idparametro = $idparametro AND estado = 1");
                foreach ($res as $row) {
                    $objContra = (object)$row;
                    $soli_compra = $this->contrataciones_model->Buscar_Info('solicitud_compra', 'id', "id_contrato = $objContra->id");
                    
                    $estados_contrato = $this->contrataciones_model->listar_estados($objContra->id);
                    $contractive = "active";
                    $row['ver'] = '<span class="d-block btn btn-primary ver_contratos" onclick="verContrato(' . $objContra->id . ')"><span class="fa fa-list"></span> Ver Contrato</span>';
                    $row['ver_cronogramas'] = '<span class="d-block btn btn-primary ver_cronogramas" data-id="' . $objContra->id . '"><span class="fa fa-list"></span> Ver Pagos</span>';
                    $li = "";
                    $num = 1;
                    if (!empty($soli_compra)) {
                        $cronograma = $this->stepper_cronograma($soli_compra[0]['id']);
                    } else {
                        $cronograma = $this->stepper_cronograma();
                    }

                    if ($cronograma['active'] == $contractive) {
                        $contractive = "";
                    }
                    $rechazo_colocado = true;
                    foreach ($estados as $data) {
                        $class = "";
                        $estado = (object)$data;
                        foreach ($estados_contrato as $estados_cont) {
                            if (in_array($estado->id_aux, $estados_cont)) {
                                $class = "completed";
                            }                                            
                        }    
                        
                        if ($row['estado_cont'] == "Cont_En_Comp" || $row['estado_cont'] == "Cont_En_Ver") $stepdata = "El contrato ya se encuentra firmado.";

                        if ($row['estado_cont'] == $estado->id_aux) {
                            $class = "active";
                            $stepdata = $estado->valorx;
                        }

                        if ($row['estado_cont'] == "Cont_En_Firm") {
                            if (!is_null($row['firma_contratista']) && $row['firma_contratista'] == 1) {
                                $stepdata = "El contrato ya se encuentra firmado.";
                            } else {
                                $stepdata = '<span title="Firmar" data-id="' . $row['id'] . '" data-toggle="popover" data-trigger="hover" class="btn btn-primary btnfirma"><i class="fa fa-edit"></i> Firmar</span>';                            
                            }
                        }

                        if ($row['estado_cont'] == "Cont_Ok_E") {
                            $class = "completed";
                        }

                        if ($estado->id_aux != "Cont_Ace_E" && $estado->id_aux != "Cont_Rec_E" && $estado->id_aux != "Cont_En_Comp" && $estado->id_aux != "Cont_En_Ver") {
                            if ($row['estado_cont'] == "Cont_Rec_E" && $class == "" && $rechazo_colocado) {
                                $rechazo_colocado = false;
                                $stepdata = "El contrato fue rechazado";
                                $li .= "<li class='warning'>
                                    <span class='line'></span>
                                    <a>
                                        <span class='circle'><span>$num</span></span>
                                        <span class='label'>Rechazado</span>
                                    </a>                            
                                </li>";
                                $num++;
                            }

                            $li .= "<li class='$class'>
                                <span class='line'></span>
                                <a>
                                    <span class='circle'><span>$num</span></span>
                                    <span class='label'>$estado->valor</span>
                                </a>                            
                            </li>";
                            $num++;
                        }
                    }

                    $row['stepper_contra'] = "<div class='content-contra p-4 $contractive'>
                        <h4 class='m-4 text-center text-dark'>Estados del contrato</h4>
                        <ul class='stepper'> 
                            $li                                   
                        </ul>
                        <div class='step-content text-center'>
                            <div class='step-data'>$stepdata</div>
                        </div>
                    </div>";

                    $row['contractive'] = $contractive;
                    $row['cronoactive'] = $cronograma['active'];
                    $row['stepper_crono'] = $cronograma['stepper'];
                    $row['fecha_pago'] = $cronograma['fecha'];
                    $row['crono_estado'] = $cronograma['estado'];
                    $row['item_crono'] = $cronograma['item'];

                    array_push($contratos, $row);
                }
                $r = $contratos;
            } else {
                $r = ['message' => 'error'];
            }
            exit(json_encode($r));
        }        
    }

    public function listar_cronogramas()
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
            $id = $this->input->post('id_solicitud');
            $soli_compra = $this->contrataciones_model->Buscar_Info('solicitud_compra', 'id', "id_contrato = $id");
            $res = 0;
            if (!empty($soli_compra)) {
                $res = $this->contrataciones_model->Listar_Cronogramas($soli_compra[0]['id']);
                $cronogramas = [];
                if (!empty($res)) {
                    foreach ($res as $row) {
                        $row['ver'] = '<span class="btn btn-success btn-sm adjuntados_cronogramas" data-id="' . $row['id'] . '">ver</span>';
                        $btn_select = '<span class="btn btn-success btn-sm pointer select_crono" data-id="' . $row['id'] . '"><i class="fas fa-check"></i></span>';
                        $row['acciones'] = $btn_select;
                        array_push($cronogramas, $row);
                    }
                    $res = $cronogramas;
                }
            }
            exit(json_encode($res));
        }
    }

    public function cargar_adj_cronograma()
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
            $ruta_compras = "archivos_adjuntos/compras/solicitudes/";
            $id = $this->input->post("id");
            $nombre = $_FILES["file"]["name"];
            $cargo = $this->cargar_archivo("file", $ruta_compras, "compdw");
            if ($cargo[0] == -1) {
                header("HTTP/1.0 400 Bad Request");
                echo ($nombre);
                return;
            }
            $soli_compra = $this->contrataciones_model->Buscar_Info('compras_cronograma', 'id_solicitud', "id = $id");
            $dataSend = [
                'id_compra' => $soli_compra[0]['id_solicitud'],
                'id_cronograma' => $id,
                'nombre_real' => $nombre,
                'nombre_guardado' => $cargo[1],
                'usuario_registra' => 1
            ];
            $res = $this->contrataciones_model->Guardar_Info("archivos_adj_compras", $dataSend);
            if ($res) {
                $res = 1;
            } else {
                header("HTTP/1.0 400 Bad Request");
                echo ($nombre);
                return;
            }
            echo json_encode($res);
            return;
        }
    }

    public function listar_estados()
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
            $id = $this->input->post('id');
            $query = $this->contrataciones_model->listar_estados($id);
            if ($query) {
                $r = $query;
            }
            echo json_encode($r);
        }
    }


    /** Actual month last day **/
    public function _data_last_month_day()
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
            $month = date('m');
            $year = date('Y');
            $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

            return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
        }
    }

    /** Actual month first day **/
    public function _data_first_month_day()
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
            $month = date('m');
            $year = date('Y');
            return date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
        }
    }

    /* Listar adjuntos de contratos */
    public function listar_archivos_contratos()
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
            $ids = $this->input->post("id_solicitud");
            $r = $this->contrataciones_model->listar_archivos_contratos($ids);
            echo json_encode($r);
        }
    }

    /* stepper de los cronogramas*/
    public function stepper_cronograma($id_soli_comp = "", $id_crono = "")
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
            $idSoliCompra = $this->input->post("id_soli_comp");
            $idCrono = $this->input->post("id_crono");
            $active = "";
            $cronograma = "";
            $data = [];

            if (isset($idSoliCompra) && !empty($idSoliCompra)) {
                $cronogramas = $this->contrataciones_model->Listar_Cronogramas($idSoliCompra);
            } else if (!empty($id_soli_comp)) {
                $cronogramas = $this->contrataciones_model->Listar_Cronogramas($id_soli_comp);
            }

            if (isset($idCrono) && !empty($idCrono)) {
                $cronogramas = $this->contrataciones_model->Listar_Cronogramas('', $idCrono);
            } else if (!empty($id_crono)) {
                $cronogramas = $this->contrataciones_model->Listar_Cronogramas('', $id_crono);
            }
            
            if (!empty($cronogramas)) {
                $idpa = $this->find_idParametro('Crono_No_Fin')->idpa;
                $estados_cronogramas = $this->contrataciones_model->Buscar_Info('valor_parametro', '*', "idparametro = $idpa AND estado = 1 order by valorx asc");
                foreach ($cronogramas as $crono) {
                    $cronograma = $crono;
                }           
            } 
            
            if ($cronograma != "") {
                $active = "active";
                $li = "";
                $num = 1;
                $num_estado = 0;
                $step_data = "";
                foreach ($estados_cronogramas as $estado) {
                    if ($cronograma['estado_cronograma'] == $estado['id_aux']) {
                        $num_estado = $estado['valorx'];
                    }
                }

                foreach ($estados_cronogramas as $estado) {
                    $class = "";
                    if ($num_estado > $estado['valorx']) {
                        $class = "completed";
                    }

                    if ($cronograma['estado_cronograma'] == $estado['id_aux']) {
                        $class = "active";
                        $step_data = "El pago ha pasado a " . strtolower($estado['valor']) . " en espera de aprovación";
                        if ($cronograma['estado_cronograma'] == 'Crono_Si_Fin') {
                            $class = "completed";
                            $step_data = "El pago fue " . strtolower($estado['valor']);
                        } else if ($cronograma['estado_cronograma'] == 'Crono_No_Fin') {
                            $step_data = '<span class="btn btn-primary pointer adjs_cronograma" data-id="' . $cronograma['id'] . '"><span class="fas fa-folder-plus"></span></span> Adjuntar archivos solicitados para el pago ';
                        } else if ($cronograma['estado_cronograma'] == 'Crono_Dene') {
                            $step_data = "El pago fue rechazado";
                            $li .= "<li class='warning'>
                                <span class='line'></span>
                                <a>
                                    <span class='circle'><span>$num</span></span>
                                    <span class='label'>Rechazado</span>
                                </a>                            
                            </li>";
                            $num++;
                        }

                        $estado_actual = $estado['valor'];
                    }

                    if ($estado['id_aux'] != 'Crono_Dene') {
                        $li .= "<li class='$class'>
                            <span class='line'></span>
                            <a>
                                <span class='circle'><span>$num</span></span>
                                <span class='label'>$estado[valor]</span>
                            </a>                            
                        </li>";
                        $num++;
                    }
                }

                $data['stepper'] = "<h4 class='m-4 text-center text-dark'>Así va tu proceso de pago!</h4>
                <ul class='stepper'> 
                    $li                                  
                </ul>
                <div class='step-content text-center'>
                    <div class='step-data'>$step_data</div>
                </div>";

                $data['fecha'] = $cronograma['especificaciones'];
                $data['estado'] = $estado_actual;
                $data['item'] = $cronograma['codigo_item'];
            } else {
                $data['stepper'] = "<h4 class='m-4 text-center text-dark'>Aún no hay pagos disponibles</h4>
                <div class='step-content text-center'>
                    <div class='step-data'></div>
                </div>";

                $data['fecha'] = 'No disponible';
                $data['estado'] = 'No disponible';
                $data['item'] = 'No disponible';
            }
            $data['active'] = $active;
            if ((isset($idCrono) && !empty($idCrono)) || (isset($idSoliCompra) && !empty($idSoliCompra))) {
                echo json_encode($data);
            } else {
                return $data;
            }
        }
    }

    /* listar adjuntos cronograma */
    public function listar_adjuntos_cronograma()
    {
        if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
            $ruta_archivos_solicitudes = "archivos_adjuntos/compras/solicitudes";
            $idCrono = $this->input->post('id');
            $r = $this->contrataciones_model->listar_adjuntos_cronograma($idCrono);
            $arrayCrono = [];
            if (!empty($r)) {
                foreach ($r as $crono) {
                    $crono['ver'] = '<a href="' . base_url() . $ruta_archivos_solicitudes . '/' . $crono['nombre_guardado'] . '" target="_blank" title="Ver Archivo" data-toggle="popover" data-trigger="hover" class="btn btn-success btn-sm">Ver</a>';
                    array_push($arrayCrono, $crono);
                }
            }
            exit(json_encode($arrayCrono));
        }
    }

    public function find_idParametro($codigoo = '', $return = true)
	{
        if (empty($codigoo)) {
            $codigoo = $this->input->post('codigo');
            $return =  false;
        }
        if (empty($codigoo)) {
            $r = [];
        } else {
            $query = $this->contrataciones_model->find_idParametro($codigoo);
            $r = $query;
        }
        if ($return) {
            return $r;
        } else {
            exit(json_encode($r));
        }
	}

    /* obtener permisos actividades*/
	public function obtener_permisos_actividades($estado_contrato = "", $tipo_contrato = "", $notificacion = "", $persona = ""){
		if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {
			$actividades = $this->contrataciones_model->obtener_permisos_actividades($estado_contrato, $tipo_contrato, $notificacion, $persona);
			$resp = isset($actividades) && !empty($actividades) ? $actividades: false;		
            return $resp;		
		}		
	}

	public function obtener_correos($id_solicitud){
		if (!$this->isSession) {
            exit(json_encode(['mensaje' => "", 'tipo' => "no_session", 'titulo' => ""]));
        } else {		
			$contrato = $this->contrataciones_model->listar_contratos('', $id_solicitud);
			$valor_parametro = $this->find_idParametro('contra_tistas');
			$estado_actual = $contrato[0]['estado_cont'];
			$correos = [];			
			$correos = $this->obtener_permisos_actividades($estado_actual, $contrato[0]['tipo_contrato'], 1);
			$usuario_registra = $this->contrataciones_model->Buscar_Info('personas', ["correo", "CONCAT(nombre, ' ', apellido, ' ', segundo_apellido) persona", false], 'id = '.$contrato[0]['id_usuario_registra']);
			if (!empty($valor_parametro) && ($estado_actual == "Cont_En_Firm" || $estado_actual == "Cont_Soli_E")) {
				$idpa = $valor_parametro->idpa;
				$resul_contratista = $this->contrataciones_model->Buscar_Contratista('', $idpa, $contrato[0]['contratista']);
				$correos['contratista'] = ['persona' => strtoupper($resul_contratista[0]['nombre']), 'correo' => $resul_contratista[0]['correo'], 'estado' => $estado_actual];
			}			
			$correos['usuario_registra'] = ['persona' => $usuario_registra[0]['persona'], 'correo' => $usuario_registra[0]['correo'], 'estado' => $estado_actual];
			$resp = $correos;
            return $resp;
		}
	}
}
