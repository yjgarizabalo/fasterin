<?php
class talento_humano_control extends CI_Controller {
	//Variables encargadas de los permisos que tiene el usuario en session
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;

	var $super_admin = false;
	var $admin = false;
	var $admin_th = false;
	// var $permisos = null;
	var $estados = null;
	var $actividades = null;
	var $es_decano = null;
	var $fun_arl = false;
	var $sw_jefe = false;

	// const ESTADOS = ['solicitado'=>'Man_Sol', 'cancelado'=>'Man_Can','rechazado'=>'Man_Rec', 'ejecutado'=>'Man_Eje', 'finalizado'=>'Man_Fin', 'recibido'=>'Man_Rcbd','pausa'=>'Man_Pau'];

	var $ruta_hojas = "archivos_adjuntos/talentohumano/hojas_vidas/";
	var $ruta_volantes = "archivos_adjuntos/talentohumano/volantes/";
	var $ruta_adjuntos = "archivos_adjuntos/talentohumano/solicitudes/";
	var $ruta_pruebas = "archivos_adjuntos/talentohumano/documentos_seleccion/";
	var $ruta_certificados = "archivos_adjuntos/talentohumano/certificados/";
	var $ruta_requisicion = "archivos_adjuntos/talentohumano/requisicion/";
	var $ruta_arl = "archivos_adjuntos/talentohumano/certificado_arl/";
	var $ruta_soporte_licencia = "archivos_adjuntos/talentohumano/soportes_licencia/";
	var $ruta_gestion = 'archivos_adjuntos/talentohumano/documentos_gestion/';
	var $ruta_ecargo = 'archivos_adjuntos/talentohumano/ecargo/';
    
	//Construtor del controlador, se importa el modelo inventario_model y se inicia la session
	public function __construct() {
		parent::__construct();
		$this->load->model('talento_humano_model');
		$this->load->model('genericas_model');
		$this->load->model('personas_model');
		$this->load->model('pages_model');
		session_start();
		date_default_timezone_set("America/Bogota");
		//la variable Super_estado es la encargada de notificar si el usuario esta en sesion, si no esta en sesion no podra ejecutar ninguna funcion, cuando pasa eso se retorna sin_session en la funcion que se esta ejecutando,por otro lado las variables Super_elimina, Super_modifica, Super_agrega se encarga de delimitar los permisos que tiene el perfil del usuario en la actividad que esta trabajando, si no tiene permiso las variables toman un valor de 0 y no les permite ejecutar la funcion retornando -1302.
		if (isset($_SESSION["usuario"])) {
			$this->Super_estado = true;
			$this->Super_elimina = 1;
			$this->Super_modifica = 1;
			$this->Super_agrega = 1;

			if ($_SESSION['perfil'] === 'Per_Admin') {
				$this->super_admin = true;
				$this->admin = true;
			} else {
				if ($_SESSION['perfil'] === 'Per_Admin_Tal') $this->admin_th = true;
				else if ($_SESSION['perfil'] === 'Per_Fun_arl') $this->fun_arl = true;
				else if ($_SESSION['perfil'] === 'Per_Adm_Bec') $this->admin_beca = true;
				$decano = $this->talento_humano_model->get_decano();
				$this->es_decano = $decano ? $decano->departamento : null;
				$this->actividades = array_values($this->talento_humano_model->get_actividades());
			}
			$this->estados = $this->talento_humano_model->get_actividades_asignadas();
		}
	}
	/**
	 * Se encarga de pintar el modulo de talento humano, se carga el header alterno y la ventana inventario
	 * @return Void
	 */
	public function index($vista = 'talento_humano', $id = '') {
		$pages = "inicio";
		$data['js'] = "";
		$data['actividad'] = "Ingresar";
		if ($this->Super_estado) {
			$datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $vista);
			$datos_actividad_adm = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'talento_humano_adm/talento_hum');
			if(!empty($datos_actividad_adm)) $datos_actividad = array_merge($datos_actividad,$datos_actividad_adm);
			if (!empty($datos_actividad)) {
				$cuotas = $this->genericas_model->obtener_valores_parametro_aux("Par_Cuo_Th", 20);
				$pages = $vista;
				$data['vista'] = $vista;
				$data['id'] = $id;
				$data['js'] ='talento_humano';
				$data['cuotas'] = empty($cuotas) ? 10 : $cuotas[0]["valor"];
				$data['actividad'] = $datos_actividad[0]["id_actividad"];
			}else{
				$pages = "sin_session";
				$data['js'] = "";
				$data['actividad'] = "Permisos";    
			}
			// Actividades asignadas a la pesona en sesión
			$data['actividades'] = $this->actividades;
			// Estados por actividad asignados a la pesona en sesión
			$data['estados'] = $this->estados;
		}
		$this->load->view('templates/header',$data);
		$this->load->view("pages/".$pages);
		$this->load->view('templates/footer');
	}

	public function adm($vista = 'talento_humano_adm') {
		$pages = "inicio";
		$data['js'] = "";
		$data['actividad'] = "Ingresar";
		if ($this->Super_estado) {
			$datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'talento_humano_adm');
			$permiso = $this->pages_model->Listar_permisos_perfil_talento_adm();
			if (!empty($datos_actividad)) {
				$pages = $vista;
				$data['vista'] = $vista;
				$data['js'] ='talento_humano';
				$data['permisos'] = !$permiso ? [] : $permiso;
				$data['actividad'] = $datos_actividad[0]["id_actividad"];
			}else{
				$pages = "sin_session";
				$data['js'] = "";
				$data['actividad'] = "Permisos";    
			}
		}
		$this->load->view('templates/header',$data);
		$this->load->view("pages/".$pages);
		$this->load->view('templates/footer');
	}

	public function descargar_informe($solicitud, $candidato, $descargar){
		if ($this->Super_estado){
			$sw = false;
			if($this->admin || $this->admin_th) $sw = true;
			else if($this->estados){
				foreach ($this->estados as $estado) {
					if($estado['actividad'] == 'Hum_Sele' && ($estado['estado'] == 'Tal_Env' || $estado['estado'] == 'Tal_Pro')) $sw = true;
				}
			}
			if($sw){
				$nombre_archivo = 'informe' . uniqid() . '.pdf';
				$data = $this->talento_humano_model->get_full_info_candidato($solicitud, $candidato);
				$data->estudios = $this->talento_humano_model->info_candidato_reporte($data->{'candidato_seleccion_id'});
				$data->competencias = $this->talento_humano_model->info_candidato_competencias($solicitud, $candidato);
				$data->nombre_archivo = $nombre_archivo;
				$data->descargar = $descargar;
				$this->load->view("pages/generar_informe", $data);
				$this->talento_humano_model->modificar_datos(['informe_seleccion' => $nombre_archivo], 'candidatos_seleccion', $data->{'candidato_seleccion_id'});
			} else redirect('/', 'refresh');
		}else redirect('/', 'refresh');
	}

	public function descargar_certificado($persona){
		if ($this->Super_estado){
			if($persona == $_SESSION['persona']){
				$info = $this->talento_humano_model->get_info_persona_cert($persona);
				$salario = $this->pages_model->convertirNumeroALetras($info->sueldo);
				$fecha_inicio = $this->pages_model->convertirFechaALetras($info->fecha_inicio_contrato);
				$fecha_hoy = $this->pages_model->convertirFechaALetras(date("Y-m-d"), true);
				if(!empty($info->fullname) && !empty($info->identificacion) && !empty($info->tipo_contrato) && !empty($info->cargo) && !empty($info->sueldo) && !empty($info->fecha_inicio_contrato)){
					$data = [
						'nombre_archivo' => 'certificado' . uniqid() . '.pdf',
						'nombre' => $info->fullname,
						'identificacion' => $info->identificacion,
						'lugar_expedicion' => $info->lugar_expedicion,
						'tipo_contrato' => $info->tipo_contrato,
						'cargo' => $info->cargo,
						'fecha_inicio_contrato' => $fecha_inicio,
						'salario' => $salario,
						'valor_salario' => $info->sueldo,
						'fecha_hoy' => $fecha_hoy,
					];
					$this->load->view("pages/generar_certificado", $data);
				} else redirect('/', 'refresh');
			} else redirect('/', 'refresh');
		} else redirect('/', 'refresh');
	}

	public function enviar_invitacion_induccion($solicitud, $candidato){
		if ($this->Super_estado){
			$data = $this->talento_humano_model->get_full_info_candidato($solicitud, $candidato);
			$this->load->view("pages/invitacion_induccion", $data);
		}
	}
		/**
	 * lista las personas registradas en la aplicacion
	 * @return Array
	 */
	public function buscar_postulante(){
		$personas = array();
		if ($this->Super_estado) {
			$dato = $this->input->post('dato');
			$buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1";
			if (!empty($dato)) $personas = $this->talento_humano_model->buscar_postulante($buscar);  
		}
		echo json_encode($personas);
	}
		/**
	 * Guarda las personas en la aplicacion, la funcion valida por numero de identificacion que no exista otra persona registrada
	 * @return Integer
	 */

	public function agregar_postulante()
	{
		if (!$this->Super_estado) $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega == 0) $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else {
				$identificacion = $this->input->post('identificacion');
				$id_tipo_identificacion =  $this->input->post('id_tipo_identificacion');
				$lugar_expedicion =  $this->input->post('lugar_expedicion');
				//$fecha_expedicion =  $this->input->post('fecha_expedicion');
				$fecha_expedicion =  null;
				$fecha_nacimiento =  $this->input->post('fecha_nacimiento');
				$id_tipo_persona = 'PerExt';
				$nombre = $this->input->post('nombre');
				$apellido = $this->input->post('apellido');
				$segundo_nombre = $this->input->post('segundo_nombre');
				$segundo_apellido = $this->input->post('segundo_apellido');
				$correo = $this->input->post('correo');
				$genero = $this->input->post('genero');
				$telefono = $this->input->post('telefono');
				$usuario_registra = $_SESSION['persona'];

				$strFields = [
					'Lugar Expedicion' => $lugar_expedicion,
					'Fecha Nacimiento' => $fecha_nacimiento,
					'Nombre' => $nombre,
					'Apellido' => $apellido,
					// 'Segundo Apellido' => $segundo_apellido
				];
				$str = $this->verificar_campos_string($strFields);
				$num = $this->verificar_campos_numericos(['Identificacion'=>$identificacion,'Tipo identificacion'=>$id_tipo_identificacion]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"El campo $campo no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else if (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					if($this->validateDate($fecha_nacimiento,'Y-m-d') == false) {
						$resp = ['mensaje'=>"El fecha de nacimiento no es valida.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
					}else{
						$buscar = "(p.identificacion = '$identificacion' OR p.correo = '$correo')";
						$existe = $this->talento_humano_model->buscar_postulante($buscar);
						if(!empty($existe)){
							$dato = ($existe[0]['correo'] === $correo) ? 'correo' : 'numero de cédula';
							$resp = [
								'mensaje' => "El $dato ya se encuentra registrado en el sistema.",
								'tipo' => "info",
								'titulo' => "Oops.!"
							];
						}else{
							$data = [
								'identificacion' => $identificacion,
								'id_tipo_identificacion' => $id_tipo_identificacion,
								'lugar_expedicion' => $lugar_expedicion,
								'fecha_expedicion' => $fecha_expedicion,
								'fecha_nacimiento' => $fecha_nacimiento,
								'id_tipo_persona' => $id_tipo_persona,
								'nombre' => $nombre,
								'apellido' => $apellido,
								'correo_personal' => $correo ? $correo : null,
								'correo' => $correo ? $correo : null,
								'telefono' => $telefono ? $telefono : 0,
								'genero' => $genero ? $genero : 25819,
								'segundo_nombre' => $segundo_nombre,
								'segundo_apellido' => $segundo_apellido,
								'usuario_registra' => $usuario_registra,
							];
							$add = $this->talento_humano_model->guardar_datos($data, "personas");
							
							if (!$add) {
								$resp = [
									'mensaje' => "Error al guardar el postulante, contacte con el administrador.",
									'tipo' => "error",
									'titulo' => "Oops.!"
								];
							}else{
								$postulante = $this->talento_humano_model->traer_ultimo_postulante_usuario($usuario_registra);
								$resp= [
									'mensaje' => "El postulante fue registrado con exito.",
									'tipo' => "success",
									'titulo' => "Proceso Exitoso.!",
									'postulante' => $postulante
								];
							}
						}
					}                   
				}
			}
		}
		echo json_encode($resp);
	}

			/**
	 * modificar las personas en la aplicacion, la funcion valida por numero de identificacion que no exista otra persona registrada
	 * @return Integer
	 */

	public function modificar_postulante()
	{
		if (!$this->Super_estado) $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if (!$this->Super_modifica) $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else {
				$id = $this->input->post('id');
				$identificacion =$this->input->post('identificacion');
				$id_tipo_identificacion = $this->input->post('id_tipo_identificacion');
				$lugar_expedicion =  $this->input->post('lugar_expedicion');
				$fecha_expedicion =  $this->input->post('fecha_expedicion');
				$fecha_nacimiento =  $this->input->post('fecha_nacimiento');
				$id_tipo_persona = 'PerExt';
				$nombre = $this->input->post('nombre');
				$genero = $this->input->post('genero');
				$apellido = $this->input->post('apellido');
				$segundo_nombre = $this->input->post('segundo_nombre');
				$segundo_apellido = $this->input->post('segundo_apellido');
				$telefono = $this->input->post('telefono');
				$correo = $this->input->post('correo');
				$usuario_registra = $_SESSION['persona'];

				$str = $this->verificar_campos_string(['Nombre'=>$nombre,'Apellido'=>$apellido,'Segundo Apellido'=>$segundo_apellido,]);
				$num = $this->verificar_campos_numericos(['Identificacion'=>$identificacion,'Tipo identificacion'=>$id_tipo_identificacion,'ID'=>$id, 'Genero' => $genero]);

				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"El campo $campo no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else if (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					$buscar = "p.id = $id";
					$postulante = $this->talento_humano_model->buscar_postulante($buscar)[0];  
					$sw = true;
					if ($postulante['identificacion'] != $identificacion) {
						$buscar = "p.identificacion = '$identificacion'";
						$existe = $this->talento_humano_model->buscar_postulante($buscar);
						if(!empty($existe)){
							$resp = ['mensaje'=>"El numero de cedula ya se encuentra registrado en el sistema.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
							$sw = false;
						}
					}
					if($sw){
						if($this->validateDate($fecha_nacimiento,'Y-m-d') === false) {
							$resp = ['mensaje'=>"El fecha de nacimiento no es valida.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
						}else{
							$data = array(
								'identificacion'=>$identificacion,
								'id_tipo_identificacion'=>$id_tipo_identificacion,
								'lugar_expedicion'=>$lugar_expedicion,
								'fecha_expedicion'=>$fecha_expedicion ? $fecha_expedicion : null,
								'fecha_nacimiento'=>$fecha_nacimiento,
								'id_tipo_persona'=>$id_tipo_persona,
								'nombre'=>$nombre,
								'genero'=>$genero,
								'apellido'=>$apellido,
								'segundo_nombre'=>$segundo_nombre,
								'segundo_apellido'=>$segundo_apellido,
								'telefono'=>$telefono,
								'correo'=>$correo,
							);
							$mod = $this->talento_humano_model->modificar_datos($data, "personas", $id);
							if ($mod != 0) {
								$resp = ['mensaje'=>"Error al modificar el postulante, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
							}else{
								$buscar = "p.id = $id";
								$postulante = $this->talento_humano_model->buscar_postulante($buscar)[0];  
								$resp= ['mensaje'=>"El postulante fue modificado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!",'postulante' => $postulante];
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
	public function validateDate($date, $format = 'Y-m-d H:i:s'){
		$d = DateTime::createFromFormat($format, $date);
		return ($d && $d->format($format) == $date);
	}

	public function validar_fechas($id_tipo_solicitud, $date = '', $format = 'Y-m-d H:i', $idparametro){      
        $data_tiempo = $this->genericas_model->obtener_valores_parametro_aux($id_tipo_solicitud, $idparametro);
        $fecha_actual = date($format);  
        $dias_solicitud = empty($data_tiempo) ? 0 : $data_tiempo[0]['valory'];
        $fecha_inicio = $date ? date($format,strtotime($date)) : date($format,strtotime($fecha_actual." + $dias_solicitud days")) ;
        $resp = 0;
        $hoy = date($format);
        $total = $dias_solicitud + $resp;
        $fecha_inicio_valida = date(
            $format,
            strtotime($fecha_actual . " + $total days")
        );
        $sw = $fecha_inicio < $fecha_inicio_valida ? false : true;
        return [
            "total" => $total,
            "dias_solicitud" => $dias_solicitud,
            "sw" => $sw,
        ];
    }
    public function validar_fechas_vacaciones(
        $id_tipo_solicitud,
        $date = "",
        $format = "Y-m-d H:i",
        $idparametro
    ) {
        $data_tiempo = $this->genericas_model->obtener_valores_parametro_aux(
            $id_tipo_solicitud,
            $idparametro
        );
        $fecha_actual = date($format);
        $dias_solicitud = empty($data_tiempo) ? 0 : $data_tiempo[0]["valorz"];
        $fecha_inicio = $date
            ? date($format, strtotime($date))
            : date(
                $format,
                strtotime($fecha_actual . " + $dias_solicitud days")
            );
        $resp = 0;
        $hoy = date($format);
        $total = $dias_solicitud + $resp;
        $fecha_inicio_valida = date(
            $format,
            strtotime($fecha_actual . " + $total days")
        );
        $sw = $fecha_inicio < $fecha_inicio_valida ? false : true;
        $sw2 = $total < $fecha_inicio_valida ? false : true;
        return [
            "total" => $total,
            "dias_solicitud" => $dias_solicitud,
            "sw" => $sw,
            "sw2" => $sw,
        ];
    }

    public function asignar_postulante_solicitud()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            if (!$this->Super_agrega) {
                $resp = [
                    "mensaje" =>
                        "No tiene Permisos Para Realizar Esta operación.",
                    "tipo" => "error",
                    "titulo" => "Oops.!",
                ];
            } else {
                $id_postulante = $this->input->post("id");
                $id_tipo = $this->input->post("id_tipo");
                $nombre_postulante = $this->input->post("nombre_completo");
                $procedencia = $this->input->post("procedencia");
                $id_departamento = (int) $this->input->post("id_departamento");
                $id_cargo = (int) $this->input->post("id_cargo");
                $id_cargo_actual = null;
                $id_departamento_actual = null;
                $id_formacion = (int) $this->input->post("id_formacion");
                $observaciones = $this->input->post("observaciones");
                $usuario_registra = (int) $_SESSION["persona"];
                $hoja_vida = null;
                $prueba_psicologia = null;
                $str = $this->verificar_campos_string([
                    "Postulante" => $id_postulante,
                    "Procedencia" => $procedencia,
                    "Departamento" => $id_departamento,
                    "Cargo" => $id_cargo,
                    "Formacion" => $id_formacion,
                    "Tipo" => $id_tipo,
                ]);
                if (is_array($str) && $id_tipo != "Tip_Cam_Plan") {
                    $campo = $str["field"];
                    $resp = [
                        "mensaje" => "El campo $campo no puede estar vació.",
                        "tipo" => "info",
                        "titulo" => "Oops.!",
                    ];
                } else {
                    $file =
                        $id_tipo != "Tip_Cam_Plan"
                            ? $this->cargar_archivo(
                                "hoja_vida",
                                $this->ruta_hojas,
                                "hoja"
                            )
                            : [1, null];
                    if ($file[0] == -1) {
                        $error = $file[1];
                        $resp =
                            $error ===
                            "<p>You did not select a file to upload.</p>"
                                ? [
                                    "mensaje" =>
                                        "Debe adjuntar la hoja de vida del postulante.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ]
                                : [
                                    "mensaje" =>
                                        "Error al cargar la hoja de vida del postulante.",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                    } else {
                        $sw = true;
                        $file2 =
                            $id_tipo != "Tip_Cam_Plan"
                                ? $this->cargar_archivo(
                                    "prueba_psicologia",
                                    $this->ruta_hojas,
                                    "prueba"
                                )
                                : [1, null];
                        if ($file2[0] == -1) {
                            $error = $file2[1];
                            if (
                                $error ==
                                "<p>You did not select a file to upload.</p>"
                            ) {
                                $resp = [
                                    "mensaje" =>
                                        "Debe adjuntar el informe evaluativo del postulante.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            } else {
                                $resp = [
                                    "mensaje" =>
                                        "Error al cargar el informe evaluativo del postulante.",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }
                        } else {
                            $prueba_psicologia = $file2[1];
                        }
                        if (
                            $id_tipo == "Tip_Cam_Plan" &&
                            (empty($observaciones) ||
                                ctype_space($observaciones))
                        ) {
                            $resp = [
                                "mensaje" =>
                                    "Especifique en el campo de observaciones el cambio de plan de trabajo.",
                                "tipo" => "info",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        }
                        if ($sw) {
                            $postulado = "";
                            if (
                                $id_tipo == "Tip_Cam" ||
                                $id_tipo == "Tip_Cam_Plan"
                            ) {
                                $id_cargo_actual = (int) $this->input->post(
                                    "id_cargo_actual"
                                );
                                $id_departamento_actual = (int) $this->input->post(
                                    "id_departamento_actual"
                                );
                                $postulado = $this->talento_humano_model->buscar_ultima_postulacion(
                                    "ps.id_postulante = $id_postulante AND ps.id_estado_solicitud = 'Pos_Con'"
                                );
                                if (
                                    empty($postulado) &&
                                    empty($id_cargo_actual)
                                ) {
                                    $resp = [
                                        "mensaje" =>
                                            "El postulante seleccionado no registra un proceso de contratación  en AGIL, por favor especificar la información actual del postulante.",
                                        "tipo" => "info",
                                        "titulo" => "Oops.!",
                                    ];
                                    $sw = false;
                                }
                            }

                            if ($sw) {
                                $id_solicitud = $this->talento_humano_model->buscar_solicitud_hoy(
                                    "Hum_Csep"
                                );
                                $notifica = false;
                                if (is_null($id_solicitud)) {
                                    $data = [
                                        "id_tipo_solicitud" => "Hum_Csep",
                                        "usuario_registro" => $usuario_registra,
                                    ];
                                    $add = $this->talento_humano_model->guardar_datos(
                                        $data,
                                        "solicitudes_talento_hum"
                                    );
                                    if (!$add) {
                                        $resp = [
                                            "mensaje" =>
                                                "Error al guardar la solicitud del día, contacte con el administrador.",
                                            "tipo" => "error",
                                            "titulo" => "Oops.!",
                                        ];
                                        $sw = false;
                                    } else {
                                        $notifica = true;
                                        $id_solicitud = $this->talento_humano_model->buscar_solicitud_hoy(
                                            "Hum_Csep"
                                        );
                                    }
                                }
                                if ($sw) {
                                    $hoja_vida = $file[1];
                                    $data = [
                                        "id_tipo" => $id_tipo,
                                        "id_solicitud" => $id_solicitud,
                                        "id_postulante" => $id_postulante,
                                        "procedencia" =>
                                            $id_tipo != "Tip_Cam_Plan"
                                                ? $procedencia
                                                : null,
                                        "id_formacion" =>
                                            $id_tipo != "Tip_Cam_Plan"
                                                ? $id_formacion
                                                : null,
                                        "hoja_vida" => $hoja_vida,
                                        "observaciones" => $observaciones,
                                        "usuario_registra" => $usuario_registra,
                                        "id_postulacion" => empty($postulado)
                                            ? null
                                            : $postulado->{'id'},
                                        "prueba_psicologia" => $prueba_psicologia,
                                        "id_departamento_postulante" =>
                                            $id_tipo != "Tip_Cam_Plan"
                                                ? $id_departamento
                                                : null,
                                        "id_cargo_postulante" =>
                                            $id_tipo != "Tip_Cam_Plan"
                                                ? $id_cargo
                                                : null,
                                        "id_departamento_actual_postulante" => $id_departamento_actual,
                                        "id_cargo_actual_postulante" => $id_cargo_actual,
                                    ];
                                    $add = $this->talento_humano_model->guardar_datos(
                                        $data,
                                        "postulantes_csep"
                                    );
                                    $resp = [
                                        "mensaje" =>
                                            "El postulante fue registrado con exito.",
                                        "tipo" => "success",
                                        "titulo" => "Proceso Exitoso.!",
                                        "notifica" => $notifica,
                                        "id" => $id_solicitud,
                                    ];
                                    if (!$add) {
                                        $resp = [
                                            "mensaje" =>
                                                "Error al guardar el postulante, contacte con el administrador.",
                                            "tipo" => "error",
                                            "titulo" => "Oops.!",
                                        ];
                                    } else {
                                        $id = $this->talento_humano_model->traer_ultimo_registro_postulante_sol_usuario(
                                            $usuario_registra
                                        )->{'id'};
                                        $data = [
                                            "id_estado" => "Pos_Env",
                                            "id_postulante" => $id,
                                            "usuario_registra" => $usuario_registra,
                                        ];
                                        $add = $this->talento_humano_model->guardar_datos(
                                            $data,
                                            "estados_postulantes"
                                        );
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

    public function cargar_archivo($mi_archivo, $ruta, $nombre)
    {
        $nombre .= uniqid();
        $tipo_archivos = $this->genericas_model->obtener_valores_parametro_aux(
            "For_Adm",
            20
        );
        $tipo_archivos = empty($tipo_archivos)
            ? "*"
            : $tipo_archivos[0]["valor"];
        $real_path = realpath(APPPATH . "../" . $ruta);
        $config["upload_path"] = $real_path;
        $config["file_name"] = $nombre;
        $config["allowed_types"] = $tipo_archivos;
        $config["max_size"] = "0";
        $config["max_width"] = "0";
        $config["max_height"] = "0";

        $this->load->library("upload", $config);
        if (!$this->upload->do_upload($mi_archivo)) {
            $data["uploadError"] = $this->upload->display_errors();
            return [-1, $data["uploadError"]];
        }
        $data["uploadSuccess"] = $this->upload->data();
        return [1, $data["uploadSuccess"]["file_name"]];
    }

    public function listar_postulantes_csep()
    {
        $vista = $this->input->post("vista");
        $id = $this->input->post("id");
        $tipo = $this->input->post("tipo");
        $postulantes =
            $this->Super_estado == true
                ? $this->talento_humano_model->listar_postulantes_csep(
                    $vista,
                    $id,
                    $tipo
                )
                : [];
        echo json_encode($postulantes);
        return;
    }

    public function cambiar_estado_postulantes_csep()
    {
        if ($this->Super_estado == false) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = [
                    "mensaje" =>
                        "No tiene Permisos Para Realizar Esta operación.",
                    "tipo" => "error",
                    "titulo" => "Oops.!",
                ];
            } else {
                $id = $this->input->post("id");
                $id_programa = $this->input->post("id_programa");
                $estado = $this->input->post("estado");
                $mensaje = $this->input->post("mensaje");
                $id_comite = $this->input->post("id_comite");
                $fecha_inicio_contrato = $this->input->post(
                    "fecha_inicio_contrato"
                );
                $fecha_fin_contrado = $this->input->post("fecha_fin_contrado");
                $con_fecha = $this->input->post("con_fecha");
                $observaciones = $this->input->post("observaciones");
                $usuario_registra = $_SESSION["persona"];
                $fecha_registra = date("Y-m-d H:i");
                $data = [
                    "id_estado_solicitud" => $estado,
                ];
                $valido = $this->validar_estado_postulante($estado, $id);
                $notifica = false;
                $datos_postulante = [];
                if ($valido) {
                    $sw = true;
                    if ($estado == "Pos_Act") {
                        $str = $this->verificar_campos_string([
                            "campo" => $mensaje,
                            "Comite" => $id_comite,
                            "Programa" => $id_programa,
                        ]);
                        $estado_comite = $this->talento_humano_model->listar_comites(
                            $id_comite
                        )[0]["id_estado_comite"];
                        if (is_array($str)) {
                            $campo = $str["field"];
                            $resp = [
                                "mensaje" => "El $campo no puede estar vacio.",
                                "tipo" => "info",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        } elseif ($estado_comite != "Com_Ini") {
                            $resp = [
                                "mensaje" =>
                                    "No es posible continuar, el comité fue gestionado anteriormente.",
                                "tipo" => "info",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        } else {
                            $data["plan_trabajo"] = $mensaje;
                            $data["id_comite"] = $id_comite;
                            $data["id_programa"] = $id_programa;
                        }
                    } elseif ($estado == "Pos_Rev" || $estado == "Pos_Can") {
                        $str = $this->verificar_campos_string([
                            "campo" => $mensaje,
                        ]);
                        if (is_array($str)) {
                            $campo = $str["field"];
                            $resp = [
                                "mensaje" => "Debe ingresar el motivo.",
                                "tipo" => "info",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        } else {
                            $data["motivo"] = $mensaje;
                        }
                    } elseif ($estado == "Pos_Con") {
                        $str = $this->verificar_campos_string([
                            "Fecha Inicio" => $fecha_inicio_contrato,
                            "Fecha Fin" => $fecha_fin_contrado,
                        ]);
                        if (is_array($str) && $con_fecha == 1) {
                            $campo = $str["field"];
                            $resp = [
                                "mensaje" => "El campo $campo no puede estar vacio.",
                                "tipo" => "info",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        } elseif (
                            $this->validateDate(
                                $fecha_inicio_contrato,
                                "Y-m-d"
                            ) == false &&
                            $con_fecha == 1
                        ) {
                            $resp = [
                                "mensaje" =>
                                    "El fecha de inicio de contrato no es valida.",
                                "tipo" => "info",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        } elseif (
                            $this->validateDate($fecha_fin_contrado, "Y-m-d") ==
                                false &&
                            $con_fecha == 1
                        ) {
                            $resp = [
                                "mensaje" =>
                                    "El fecha de fin de contrato no es valida.",
                                "tipo" => "info",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        } elseif (empty($observaciones)) {
                            $resp = [
                                "mensaje" =>
                                    "El campo observaciones no puede estar vacio.",
                                "tipo" => "info",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        } else {
                            $data["fecha_inicio_contrato"] = empty(
                                $fecha_inicio_contrato
                            )
                                ? null
                                : $fecha_inicio_contrato;
                            $data["fecha_fin_contrado"] = empty(
                                $fecha_fin_contrado
                            )
                                ? null
                                : $fecha_fin_contrado;
                            $data["observaciones_contrato"] = $observaciones;
                        }
                    } elseif ($estado == "Pos_Apr" || $estado == "Pos_Neg") {
                        $cargo = $this->talento_humano_model->aprueba_persona_csep(
                            $usuario_registra
                        );
                        $datos_postulante = $this->talento_humano_model->buscar_postulantes_csep_id(
                            "ps.id = $id"
                        );
                        $tipo_doc = $datos_postulante[0]["cargo_aux"];
                        $apruba_cat =
                            $tipo_doc == "Pro_Cat" && $cargo == "Apr_Csep_Cat"
                                ? true
                                : false;
                        if ($cargo != "Apr_Csep" && !$apruba_cat) {
                            $sw = false;
                            $tiene = $this->talento_humano_model->tiene_visto_bueno_persona(
                                $id,
                                $usuario_registra
                            );
                            if (empty($tiene)) {
                                $me =
                                    $estado == "Pos_Apr"
                                        ? "Aprobado"
                                        : "Negado";
                                $estado =
                                    $estado == "Pos_Apr"
                                        ? "Pos_Bue"
                                        : "Pos_Mal";
                                $resp = [
                                    "mensaje" => "El postulante fue $me con exito.",
                                    "tipo" => "success",
                                    "titulo" => "Proceso Exitoso.!",
                                    "notifica" => false,
                                ];
                                $data = [
                                    "id_estado" => $estado,
                                    "id_postulante" => $id,
                                    "usuario_registra" => $usuario_registra,
                                ];
                                $add = $this->talento_humano_model->guardar_datos(
                                    $data,
                                    "estados_postulantes"
                                );
                                if (!$add) {
                                    $resp = [
                                        "mensaje" =>
                                            "Error al aprobar o negar el postulante, contacte con el administrador.",
                                        "tipo" => "error",
                                        "titulo" => "Oops.!",
                                    ];
                                }
                            } else {
                                $resp = [
                                    "mensaje" =>
                                        "No es posible continuar,El postulante ya cuenta con el aprobado o el negado por parte de usted.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                            }
                        }
                    }
                    if ($sw) {
                        $add = $this->talento_humano_model->modificar_datos(
                            $data,
                            "postulantes_csep",
                            $id
                        );
                        $resp = [
                            "mensaje" => "El estado fue modificado con exito.",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso.!",
                            "notifica" => true,
                        ];
                        if ($add != 0) {
                            $resp = [
                                "mensaje" =>
                                    "Error al modificar el estado, contacte con el administrador.",
                                "tipo" => "error",
                                "titulo" => "Oops.!",
                            ];
                        } else {
                            $data = [
                                "id_estado" => $estado,
                                "id_postulante" => $id,
                                "usuario_registra" => $usuario_registra,
                            ];
                            $add = $this->talento_humano_model->guardar_datos(
                                $data,
                                "estados_postulantes"
                            );
                            $datos_postulante = empty($datos_postulante)
                                ? $this->talento_humano_model->buscar_postulantes_csep_id(
                                    "ps.id = $id"
                                )
                                : $datos_postulante;
                            if ($estado == "Pos_Apr" || $estado == "Pos_Neg") {
                                $id_comite = $datos_postulante[0]["id_comite"];
                                $total = $this->talento_humano_model->faltantes_aprobar_comite(
                                    $id_comite
                                );
                                if ($total == 0) {
                                    $data = [
                                        "id_estado_comite" => "Com_Ter",
                                    ];
                                    $mod = $this->talento_humano_model->modificar_datos(
                                        $data,
                                        "comites",
                                        $id_comite
                                    );
                                    $resp = [
                                        "mensaje" =>
                                            "El estado fue modificado con exito",
                                        "tipo" => "success",
                                        "titulo" => "Proceso Exitoso.!",
                                        "up_comite" => "si",
                                        "notifica" => true,
                                    ];
                                    if ($mod != 0) {
                                        $resp = [
                                            "mensaje" =>
                                                "Error al modificar el estado del comité, contacte con el administrador.",
                                            "tipo" => "error",
                                            "titulo" => "Oops.!",
                                        ];
                                    }
                                }
                            }
                            if (
                                $estado != "Pos_Bue" &&
                                $estado != "Pos_Mal" &&
                                $estado != "Pos_Apr" &&
                                $estado != "Pos_Act"
                            ) {
                                $id_solicitud =
                                    $datos_postulante[0]["id_solicitud"];
                                $total = $this->talento_humano_model->faltantes_contratar_solicitud(
                                    $id
                                );
                                if ($total == 0) {
                                    $data = [
                                        "id_estado_solicitud" => "Tal_Ter",
                                    ];
                                    $mod = $this->talento_humano_model->modificar_datos(
                                        $data,
                                        "solicitudes_talento_hum",
                                        $id_solicitud
                                    );
                                    $resp = [
                                        "mensaje" =>
                                            "El estado fue modificado con exito",
                                        "tipo" => "success",
                                        "titulo" => "Proceso Exitoso.!",
                                        "up_solicitud" => "si",
                                        "notifica" => false,
                                    ];
                                    if ($mod != 0) {
                                        $resp = [
                                            "mensaje" =>
                                                "Error al modificar el estado de la solicitud, contacte con el administrador.",
                                            "tipo" => "error",
                                            "titulo" => "Oops.!",
                                        ];
                                    }
                                }
                            }
                            if ($estado == "Pos_Con") {
                                $resp["notifica"] = true;
                            }
                        }
                    }
                } else {
                    $resp = [
                        "mensaje" =>
                            "No es posible continuar, La solicitud fue gestionada anteriormente o se encuentra terminada.",
                        "tipo" => "info",
                        "titulo" => "Oops.!",
                    ];
                }
            }
        }

        echo json_encode($resp);
    }
    public function validar_estado_postulante($estado_nue, $id)
    {
        $estado_actual = $this->talento_humano_model->buscar_postulantes_csep_id(
            "ps.id = $id"
        )[0]["id_estado_solicitud"];
        if (
            $estado_actual == "Pos_Env" &&
            ($estado_nue == "Pos_Act" ||
                $estado_nue == "Pos_Rev" ||
                $estado_nue == "Pos_Can")
        ) {
            return true;
        } elseif (
            $estado_actual == "Pos_Act" &&
            ($estado_nue == "Pos_Apr" || $estado_nue == "Pos_Neg")
        ) {
            return true;
        } elseif (
            $estado_actual == "Pos_Apr" &&
            ($estado_nue == "Pos_Con" || $estado_nue == "Pos_Can")
        ) {
            return true;
        }
        return false;
    }

    public function listar_solicitudes_csep()
    {
        if (!$this->Super_estado) {
            echo json_encode(["tipo" => "sin_session"]);
            return;
        }
        $solicitudes_csep = [];
        $id = $this->input->post("id");
        $tipo_solicitud = $this->input->post("tipo_solicitud");
        $estado = $this->input->post("estado");
        $fecha_inicio = $this->input->post("fecha_inicio");
        $fecha_fin = $this->input->post("fecha_fin");
        $btn_modificar =
            '<span title="Modificar Solicitud" data-toggle="popover" data-trigger="hover" style="color: #337ab7" class="btn btn-default fa fa-wrench modificar"></span>';
        $btn_revisar =
            '<span title="Revisar" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn btn-default fa fa-check revisar"></span>';
        $btn_negar =
            '<span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #d9534f" class="btn btn-default fa fa-ban negar"></span>';
        $btn_cancelar =
            '<span title="Cancelar" data-toggle="popover" data-trigger="hover" style="color: #d9534f" class="btn btn-default fa fa-remove cancelar"></span>';
        $btn_inhabil =
            '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
        $btn_espera =
            '<span title="Procesando" data-toggle="popover" data-trigger="hover" style="color: #5bc0de" class="btn fa fa-hourglass-half"></span>';

        $solicitudes = $this->talento_humano_model->listar_solicitudes_csep(
            $id,
            $tipo_solicitud,
            $estado,
            $fecha_inicio,
            $fecha_fin
        );
        $bg_color = "white";
        $color = "black";
        if (sizeof($solicitudes) > 0) {
            foreach ($solicitudes as $solicitud) {
                switch ($_SESSION["perfil"]) {
                    case "Per_Admin":
                        switch ($solicitud["id_tipo_solicitud"]) {
                            case "Hum_Prec":
                                switch ($solicitud["id_estado_solicitud"]) {
                                    case "Env_Csea":
                                        $solicitud[
                                            "gestion"
                                        ] = "$btn_revisar $btn_modificar $btn_negar";
                                        $color = "black";
                                        $bg_color = "white";
                                        break;
                                    case "Tal_Env":
                                    case "Tal_Pro":
                                    case "Tal_Esp":
                                        $solicitud["gestion"] = $btn_espera;
                                        $color = "black";
                                        $bg_color = "white";
                                        break;

                                    case "Tal_Neg":
                                    case "Tal_Can":
                                        $solicitud["gestion"] = $btn_inhabil;
                                        $bg_color = "#d9534f";
                                        $color = "white";
                                        break;

                                    case "Tal_Ter":
                                        $solicitud["gestion"] = $btn_inhabil;
                                        $bg_color = "#5cb85c";
                                        $color = "white";
                                        break;
                                }
                                break;

                            case "Hum_Csep":
                                switch ($solicitud["id_estado_solicitud"]) {
                                    case "Tal_Env":
                                        $solicitud["gestion"] = $btn_inhabil;
                                        $color = "black";
                                        $bg_color = "white";
                                        break;

                                    case "Tal_Ter":
                                        $solicitud["gestion"] = $btn_inhabil;
                                        $bg_color = "#5cb85c";
                                        $color = "white";
                                        break;
                                }
                                break;

                            default:
                                $solicitud["gestion"] = $btn_inhabil;
                                $bg_color = "white";
                                $color = "black";
                                break;
                        }
                        break;

                    case "Per_Csep":
                        switch ($solicitud["id_tipo_solicitud"]) {
                            case "Hum_Prec":
                                switch ($solicitud["id_estado_solicitud"]) {
                                    case "Tal_Env":
                                        $solicitud["gestion"] = "$btn_espera";
                                        $color = "black";
                                        $bg_color = "white";
                                        break;
                                    case "Env_Csea":
                                        $solicitud[
                                            "gestion"
                                        ] = "$btn_revisar $btn_modificar $btn_negar";
                                        $color = "black";
                                        $bg_color = "white";
                                        break;
                                    case "Tal_Pro":
                                    case "Tal_Esp":
                                        $solicitud["gestion"] = "$btn_espera";
                                        $color = "black";
                                        $bg_color = "white";
                                        break;

                                    case "Tal_Neg":
                                    case "Tal_Can":
                                        $solicitud["gestion"] = "$btn_inhabil";
                                        $bg_color = "#d9534f";
                                        $color = "white";
                                        break;

                                    case "Tal_Ter":
                                        $solicitud["gestion"] = $btn_inhabil;
                                        $bg_color = "#5cb85c";
                                        $color = "white";
                                        break;
                                }
                                break;
                            case "Hum_Csep":
                                switch ($solicitud["id_estado_solicitud"]) {
                                    case "Tal_Env":
                                        $solicitud["gestion"] = $btn_inhabil;
                                        $color = "black";
                                        $bg_color = "white";
                                        break;

                                    case "Tal_Ter":
                                        $solicitud["gestion"] = $btn_inhabil;
                                        $bg_color = "#5cb85c";
                                        $color = "white";
                                        break;
                                }
                                break;

                            default:
                                $solicitud["gestion"] = $btn_inhabil;
                                $bg_color = "white";
                                $color = "black";
                                break;
                        }
                        break;

                    default:
                        switch ($solicitud["id_tipo_solicitud"]) {
                            case "Hum_Prec":
                            case "Hum_Admi":
                                switch ($solicitud["id_estado_solicitud"]) {
                                    case "Tal_Env":
                                        $solicitud[
                                            "gestion"
                                        ] = "$btn_modificar $btn_cancelar";
                                        $color = "black";
                                        break;

                                    case "Tal_Neg":
                                    case "Tal_Can":
                                        $solicitud["gestion"] = $btn_inhabil;
                                        $bg_color = "#d9534f";
                                        $color = "white";
                                        break;

                                    case "Tal_Ter":
                                        $solicitud["gestion"] = $btn_inhabil;
                                        $bg_color = "#5cb85c";
                                        $color = "white";
                                        break;
                                }
                                break;
                            case "Hum_Csep":
                                switch ($solicitud["id_estado_solicitud"]) {
                                    case "Tal_Env":
                                        $solicitud["gestion"] = $btn_inhabil;
                                        $color = "black";
                                        $bg_color = "white";
                                        break;

                                    case "Tal_Ter":
                                        $solicitud["gestion"] = $btn_inhabil;
                                        $bg_color = "#5cb85c";
                                        $color = "white";
                                        break;
                                }
                                break;
                            default:
                                $solicitud["gestion"] = $btn_inhabil;
                                $bg_color = "white";
                                $color = "black";
                                break;
                        }
                        break;
                }
                $solicitud[
                    "ver"
                ] = "<span  style='background-color: $bg_color;color: $color; width: 100%;' class='pointer form-control'><span >ver</span></span>";
                $solicitudes_csep[] = $solicitud;
            }
        }
        echo json_encode($solicitudes_csep);
        return;
    }

    public function listar_solicitudes()
    {
        $id = $this->input->post("id");
        $estado = $this->input->post("estado");
        $tipo = $this->input->post("tipo");
        $fecha_i = $this->input->post("fecha_i");
        $fecha_f = $this->input->post("fecha_f");
        $data = $this->talento_humano_model->get_departamentos_asignados(
            "Hum_Posg"
        );
        $data_prec = $this->talento_humano_model->get_departamentos_asignados(
            "Hum_Prec"
        );
		$data_per_ecargo = $this->estados = $this->talento_humano_model->get_actividades_ecargo();
		if (!$this->Super_estado) {
            $solicitudes_th["tipo"] = "sin_session";
        } else {
            $date_i = !empty($fecha_i)
                ? ($date_i = $this->validateMonth($fecha_i, "Y-m"))
                : false;
            $date_f = !empty($fecha_f)
                ? ($date_f = $this->validateMonth($fecha_f, "Y-m"))
                : false;
            $solicitudes_th["data"] = [];
            $tipo = $this->input->post("tipo");
            $solicitudes = $this->talento_humano_model->listar_solicitudes(
                $id,
                $estado,
                $tipo,
                $date_i,
                $date_f
            );

            $bg_color = "white";
            $color = "white";

            $btn_inhabil =
                '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
            $btn_espera =
                '<span title="Procesando" data-toggle="popover" data-trigger="hover" style="color: #5bc0de" class="btn fa fa-hourglass-half"></span>';
            $btn_cancelar =
                '<span title="Cancelar" data-toggle="popover" data-trigger="hover" style="color: #d9534f" class="btn btn-default fa fa-remove cancelar"></span>';
            $btn_revisar =
                '<span title="Revisar" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn btn-default fa fa-check revisar"></span>';
            $btn_negar =
                '<span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #d9534f" class="btn btn-default fa fa-ban negar"></span>';
            $btn_vistob =
                '<span title="Aprobar Solicitud" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn btn-default fa fa-thumbs-up vistob"></span>';
            $btn_vistom =
                '<span title="Desaprobar" data-toggle="popover" data-trigger="hover" style="color: #d9534f" class="btn btn-default fa fa-thumbs-down vistom"></span>';
			$btn_vistobe =
                '<span title="Aprobar Solicitud" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn btn-default fa fa-thumbs-up vistobe"></span>';
            $btn_vistome =
                '<span title="Desaprobar" data-toggle="popover" data-trigger="hover" style="color: #d9534f" class="btn btn-default fa fa-thumbs-down vistome"></span>';
			$btn_aprobar =
                '<span title="Aprobar" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn btn-default fa fa-check-square-o aprobar"></span>';
            $btn_cruce =
                '<span title="Matrícula cruzada" data-toggle="popover" data-trigger="hover" style="color: #777" class="btn btn-default fa fa-random cruce"></span>';
            $btn_procesar =
                '<span title="Procesar" data-toggle="popover" data-trigger="hover" style="color: #337ab7" class="btn btn-default fa fa-refresh procesar"></span>';
            $btn_contabilizar =
                '<span title="Contabilizar" data-toggle="popover" data-trigger="hover" style="color: #337ab7" class="btn btn-default fa fa-book contabilizar"></span>';
            $btn_desembolzar =
                '<span title="Desembolzar" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn btn-default fa fa-money desembolsar"></span>';
            $btn_add_candidato =
                '<span title="Agregar Candidato" data-toggle="popover" data-trigger="hover" style="color: #337ab7" class="btn btn-default fa fa-user-plus add_candidato"></span>';
            $btn_modificar =
                '<span title="Modificar Solicitud" data-toggle="popover" data-trigger="hover" style="color: #337ab7" class="btn btn-default fa fa-wrench modificar"></span>';
            $btn_espera =
                '<span title="Solicitud en proceso" data-toggle="popover" data-trigger="hover" style="color: #5bc0de" class="btn fa fa-hourglass-half"></span>';
            $btn_aprobar_arl =
                '<span title="Aprobar Solicitud" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn btn-default fa fa-check-square-o aprobar"></span>';
            $btn_finalizar =
                '<span title="Finalizar" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn btn-default fa fa-check finalizar"></span>';
			$btn_agregar =
                '<span title="Entrega de cargo" data-toggle="popover" data-trigger="hover" style="color: #337ab7" class="btn btn-default fa fa-wpforms agregar "></span>';
				// $perfil_th = !$this->admin ? $this->talento_humano_model->get_permisos_rol() : false;
            foreach ($solicitudes as $solicitud) {
                $permiso_estado = $solicitud["permiso_estado"];
                $bg_color = "white";
                $color = "white";
                $sele_sw = false;
                if (!$this->admin && $this->estados) {
                    foreach ($this->estados as $estado) {
                        if (
                            $estado["actividad"] == "Hum_Sele" &&
                            ($estado["estado"] == "Tal_Env" ||
                                $estado["estado"] == "Tal_Pro")
                        ) {
                            $sele_sw = true;
                        }
                    }
                }
                $color = "white";
                $solicitud["gestion"] = $btn_inhabil;
                switch ($solicitud["id_tipo_solicitud"]) {
                    // Solicitudes de Certificados

                    case "Hum_Cert":
                    case "Hum_Cir":
                        switch ($solicitud["id_estado_solicitud"]) {
                            case "Tal_Env":
                                $solicitud["gestion"] =
                                    $this->admin || !is_null($permiso_estado)
                                        ? "$btn_revisar $btn_negar"
                                        : $btn_espera;
                                $color = "black";
                                break;
                            case "Tal_Ter":
                                $bg_color = "#5cb85c";
                                break;
                            case "Tal_Neg":
                                $bg_color = "#d9534f";
                                break;
                        }
                        break;
                    // Solicitudes CSEP

                    case "Hum_Csep":
                        switch ($solicitud["id_estado_solicitud"]) {
                            case "Tal_Env":
                                $color = "black";
                                break;
                            case "Tal_Ter":
                                $bg_color = "#5cb85c";
                                break;
                        }
                        break;
                    // Solicitudes de Prestamo

                    case "Hum_Pres":
                        switch ($solicitud["id_estado_solicitud"]) {
                            case "Tal_Env":
                                if ($this->admin || !is_null($permiso_estado)) {
                                    $solicitud[
                                        "gestion"
                                    ] = "$btn_revisar $btn_negar ";
                                }
                                if (
                                    $solicitud["usuario_registro"] ==
                                        $_SESSION["persona"] &&
                                    !is_null($permiso_estado)
                                ) {
                                    $solicitud["gestion"] .= $btn_cancelar;
                                } elseif (
                                    $solicitud["usuario_registro"] ==
                                        $_SESSION["persona"] &&
                                    is_null($permiso_estado)
                                ) {
                                    $solicitud["gestion"] = $btn_cancelar;
                                }
                                $color = "black";
                                break;
                            case "Tal_Rev":
                                $solicitud["gestion"] =
                                    $this->admin || !is_null($permiso_estado)
                                        ? "$btn_vistob $btn_vistom"
                                        : $btn_espera;
                                $bg_color = "#337ab7";
                                break;
                            case "Tal_Vis":
                            case "Tal_Mal":
                                $solicitud["gestion"] =
                                    $this->admin || !is_null($permiso_estado)
                                        ? "$btn_aprobar $btn_negar"
                                        : $btn_espera;
                                $bg_color =
                                    $solicitud["id_estado_solicitud"] ===
                                    "Tal_Vis"
                                        ? "#5bc0de"
                                        : "#d9534f";
                                break;
                            case "Tal_Apr":
                                $solicitud["gestion"] =
                                    $this->admin || !is_null($permiso_estado)
                                        ? $btn_procesar
                                        : $btn_espera;
                                $bg_color = "#337ab7";
                                break;
                            case "Tal_Cru":
                                $solicitud["gestion"] =
                                    $this->admin || !is_null($permiso_estado)
                                        ? $btn_cruce
                                        : $btn_espera;
                                $bg_color = "#777";
                                break;
                            case "Tal_Pro":
                                $solicitud["gestion"] =
                                    $this->admin || !is_null($permiso_estado)
                                        ? $btn_contabilizar
                                        : $btn_espera;
                                $bg_color = "#f0ad4e";
                                break;
                            case "Tal_Tra":
                                $solicitud["gestion"] =
                                    $this->admin || !is_null($permiso_estado)
                                        ? $btn_desembolzar
                                        : $btn_espera;
                                $bg_color = "#5bc0de";
                                break;
                            case "Tal_Des":
                                $bg_color = "#5cb85c";
                                break;
                            case "Tal_Can":
                            case "Tal_Neg":
                                $bg_color = "#d9534f";
                                break;
                        }
                        break;
                    // Solicitudes de Requisición Posgrado

                    case "Hum_Posg":
                        $solicitud["gestion"] = $btn_inhabil;
                        switch ($solicitud["id_estado_solicitud"]) {
                            case "Tal_Pro":
                                $bg_color = "#337ab7";
                                $solicitud["gestion"] =
                                    $this->super_admin ||
                                    (!is_null($permiso_estado) &&
                                        in_array(
                                            [
                                                "departamento" =>
                                                    $solicitud[
                                                        "id_departamento"
                                                    ],
                                            ],
                                            $data["departamentos"]
                                        ))
                                        ? "$btn_revisar $btn_negar"
                                        : $btn_espera;
                                break;
                            case "Tal_Env":
                                if (
                                    $this->super_admin ||
                                    (!is_null($permiso_estado) &&
                                        in_array(
                                            [
                                                "departamento" =>
                                                    $solicitud[
                                                        "id_departamento"
                                                    ],
                                            ],
                                            $data["departamentos"]
                                        ))
                                ) {
                                    $solicitud[
                                        "gestion"
                                    ] = "$btn_revisar $btn_modificar $btn_negar ";
                                } else {
                                    $solicitud["gestion"] =
                                        $solicitud["usuario_registro"] ==
                                        $_SESSION["persona"]
                                            ? $btn_cancelar
                                            : $btn_espera;
                                }
                                $color = "black";
                                break;
                            case "Tal_Neg":
                            case "Tal_Can":
                                $bg_color = "#d9534f";
                                break;
                            case "Tal_Ter":
                                $bg_color = "#5cb85c";
                                break;
                        }
                        break;
                    // Solicitudes de Requisición Académicas

                    case "Hum_Prec":
                        $solicitud["gestion"] = $btn_espera;
                        switch ($solicitud["id_estado_solicitud"]) {
                            case "Tal_Env":
                                $solicitud["gestion"] =
                                    $this->super_admin ||
                                    (!is_null($permiso_estado) &&
                                        in_array(
                                            [
                                                "departamento" =>
                                                    $solicitud[
                                                        "departamento_id"
                                                    ],
                                            ],
                                            $data_prec["departamentos"]
                                        ))
                                        ? "$btn_revisar $btn_modificar $btn_negar"
                                        : $btn_espera;
                                $color = "black";
                                break;
                            case "Env_Csea":
                                $solicitud["gestion"] = $btn_espera;
                                $bg_color = "#f0ad4e";
                                break;
                            case "Tal_Neg":
                            case "Tal_Can":
                                $bg_color = "#d9534f";
                                $solicitud["gestion"] = $btn_inhabil;
                                break;
                            case "Tal_Esp":
                                $solicitud["gestion"] =
                                    $this->super_admin ||
                                    (!is_null($permiso_estado) &&
                                        in_array(
                                            [
                                                "departamento" =>
                                                    $solicitud[
                                                        "departamento_id"
                                                    ],
                                            ],
                                            $data_prec["departamentos"]
                                        ))
                                        ? $btn_aprobar
                                        : $btn_espera;
                                $bg_color = "#f0ad4e";
                                break;
                            case "Tal_Ter":
                                $bg_color = "#5cb85c";
                                $solicitud["gestion"] = $btn_inhabil;
                                break;
                            default:
                                $solicitud["gestion"] = $btn_inhabil;
                                $color = "black";
                        }
                        break;
                    // Solicitudes de Requisición Administrativas

                    case "Hum_Admi":
                    case "Hum_Apre":
                        $solicitud["gestion"] = $btn_inhabil;
                        switch ($solicitud["id_estado_solicitud"]) {
                            case "Tal_Env":
                                $solicitud["gestion"] =
                                    $this->admin || !is_null($permiso_estado)
                                        ? "$btn_revisar $btn_modificar $btn_negar"
                                        : $btn_espera;
                                $color = "black";
                                break;

                            case "Tal_Neg":
                            case "Tal_Can":
                                $bg_color = "#d9534f";
                                break;
                            case "Tal_Ter":
                                $bg_color = "#5cb85c";
                                break;
                        }
                        break;
                    // Solicitudes de Selección

                    case "Hum_Sele":
                        $sw =
                            $solicitud["usuario_registro"] ==
                                $_SESSION["persona"] ||
                            $solicitud["responsable_id"] == $_SESSION["persona"]
                                ? true
                                : false;
                        switch ($solicitud["id_estado_solicitud"]) {
                            case "Tal_Env":
                                $color = "black";
                                $solicitud["gestion"] =
                                    $this->admin || !is_null($permiso_estado)
                                        ? "$btn_add_candidato $btn_modificar $btn_cancelar"
                                        : ($sw
                                            ? $btn_add_candidato
                                            : $btn_inhabil);
                                break;
                            case "Tal_Ter":
                                $solicitud["gestion"] = $btn_inhabil;
                                $bg_color = "#5cb85c";
                                break;
                            case "Tal_Neg":
                            case "Tal_Can":
                                $bg_color = "#d9534f";
                                break;
                            case "Tal_Pro":
                                $solicitud["gestion"] =
                                    $this->admin || !is_null($permiso_estado)
                                        ? "$btn_add_candidato $btn_modificar $btn_cancelar"
                                        : ($sw
                                            ? $btn_add_candidato
                                            : $btn_inhabil);
                                $bg_color = "#337ab7";
                                break;
                            default:
                                $color = "black";
                        }
                        break;
                    case "Hum_Vac":
                    case "Hum_Lic":
                        $sw_jefe =
                            $solicitud["jefe_inmediato"] == $_SESSION["persona"]
                                ? true
                                : false;
                        $sw =
                            $solicitud["usuario_registro"] ==
                            $_SESSION["persona"]
                                ? true
                                : false;
                        switch ($solicitud["id_estado_solicitud"]) {
                            case "Tal_Env":
                                $color = "black";
                                $solicitud["gestion"] =
                                    $this->super_admin ||
                                    $this->admin ||
                                    $sw_jefe == true
                                        ? ($sw
                                            ? "$btn_cancelar $btn_vistob $btn_vistom"
                                            : "$btn_vistob $btn_vistom")
                                        : ($sw
                                            ? "$btn_modificar $btn_cancelar"
                                            : $btn_inhabil);
                                break;
                            case "Tal_Ter":
                                $solicitud["gestion"] = $btn_inhabil;
                                $bg_color = "#5cb85c";
                                break;
                            case "Tal_Neg":
                            case "Tal_Can":
                                $solicitud["gestion"] = $btn_inhabil;
                                $bg_color = "#d9534f";
                                break;
                            case "Tal_vb_Jefe":
                                $solicitud["gestion"] =
                                    $this->super_admin ||
                                    $this->admin ||
                                    !is_null($permiso_estado)
                                        ? "$btn_revisar"
                                        : ($sw
                                            ? $btn_espera
                                            : $btn_inhabil);
                                $bg_color = "#337ab7";
                                break;
                            case "Tal_Pro":
                                $solicitud["gestion"] =
                                    $this->super_admin ||
                                    !is_null($permiso_estado)
                                        ? "$btn_finalizar $btn_negar"
                                        : ($sw
                                            ? $btn_espera
                                            : $btn_inhabil);
                                $bg_color = "#337ab7";
                                break;
                            default:
                                $color = "black";
                        }
                        break;
                    // Solicitudes de arl
                    case "Hum_Cob_Arl":
                    case "Hum_Afi_Arl":
                        $sw =
                            $solicitud["usuario_registro"] ==
                            $_SESSION["persona"]
                                ? true
                                : false;
                        switch ($solicitud["id_estado_solicitud"]) {
                            case "Tal_Env":
                                $color = "black";
                                $solicitud["gestion"] =
                                    $this->admin || $this->fun_arl
                                        ? $btn_revisar
                                        : ($sw
                                            ? "$btn_modificar $btn_cancelar"
                                            : $btn_espera);
                                break;
                            case "Tal_Pro":
                                $bg_color = "#337ab7";
                                $solicitud["gestion"] =
                                    $this->admin || $this->fun_arl
                                        ? "$btn_aprobar_arl $btn_modificar"
                                        : $btn_espera;
                                break;
                            case "Tal_Neg":
                            case "Tal_Can":
                                $bg_color = "#d9534f";
                                break;
                            case "Tal_Ter":
                                $bg_color = "#5cb85c";
                                break;
                            default:
                                $color = "black";
                        }
                        break;
                    //Botones
					case "Hum_Tras_Afp":
                        $sw =
                            $solicitud["usuario_registro"] ==
                            $_SESSION["persona"]
                                ? true
                                : false;
					switch ($solicitud["id_estado_solicitud"]) {
						case "Tal_Env":
							$color = "black";
							$solicitud["gestion"] = $this->admin || $this->fun_arl
								? "$btn_revisar $btn_negar"
								: ($sw
									? "$btn_cancelar"
									: $btn_espera);
							break;
						case "Tal_Pro":
							$bg_color = "#5cb85c";
							break;
						case "Tal_Neg":
						case "Tal_Can":
							$bg_color = "#5cb85c";
							break;
						default:
							$color = "black";
					}
						break;
					case "Hum_Entr_Cargo":
						$sw_jefe = $solicitud["jefe_inmediato"] === $_SESSION["persona"] ? true : false;
						$sw_jefe2 = $solicitud["jefe_inmediato2"] === $_SESSION["persona"] ? true : false;
						$sw = $solicitud["id_solicitante"] === $_SESSION["persona"] ? true : false;
                        switch ($solicitud["id_estado_solicitud"]) {
                            case "Tal_Env":
                                $color = "black";
                                $solicitud["gestion"] =  $this->admin || $sw_jefe || $sw_jefe2  == true  ? "$btn_revisar $btn_negar" 
                                    : ($sw ? " $btn_agregar $btn_cancelar" : $btn_espera);
                                break;
                            case "Tal_Pro":
                                $bg_color = "#337ab7";
									$solicitud["gestion"] = $sw_jefe  ? "$btn_vistob $btn_vistom" : (!empty($data_per_ecargo) ? $btn_aprobar_arl : $sw ? " $btn_agregar" : $btn_espera)   ;
                                break;
							case "Tal_vb_Jefe_ecargo1":
									$bg_color = "#FFA200";
										$solicitud["gestion"] =  (!empty($data_per_ecargo) ? $btn_aprobar_arl : $btn_espera )    ;
								break;
							case "Tal_vm_Jefe_ecargo1":
									$bg_color = "#FFA200";
										$solicitud["gestion"] =  (!empty($data_per_ecargo) ? $btn_aprobar_arl : $btn_espera )    ;
								break;
							case "Tal_vm_Jefe_ecargo":
								$bg_color = "#FFA200";
									$solicitud["gestion"] = $sw_jefe2 ? "$btn_vistobe $btn_vistome" :(!empty($data_per_ecargo) ? $btn_aprobar_arl : $btn_espera )    ;
								break;
							case "Tal_vb_Jefe_ecargo":
								$bg_color = "#FFA200";
									$solicitud["gestion"] = $sw_jefe2 ? "$btn_vistobe $btn_vistome" : (!empty($data_per_ecargo) ? $btn_aprobar_arl : $btn_espera  )    ;
								break;
							case "Tal_Vb_Ter":
									$bg_color = "#2ac254 ";
										$solicitud["gestion"] =  $this->admin || $this->fun_arl || !is_null($permiso_estado) ? "$btn_finalizar $btn_negar"  : $btn_espera ;
								break;
                            case "Tal_Neg":
                            case "Tal_Can":
                                $bg_color = "#d9534f";
                                break;
							case "Tal_Ter":
								$bg_color = "#C300F8";
								break;
                            default:
                                $color = "black";
                        }
						break;
                    case "Hum_Inc_Eps":
                    case "Hum_Cam_Eps":
                    case "Hum_Inc_Caja":
					
                        $sw =
                            $solicitud["usuario_registro"] ==
                            $_SESSION["persona"]
                                ? true
                                : false;
                        switch ($solicitud["id_estado_solicitud"]) {
                            case "Tal_Env":
                                $color = "black";
                                $solicitud["gestion"] = $this->admin || $this->fun_arl
                                    ? "$btn_revisar $btn_negar" 
                                    : ($sw
                                        ? "$btn_cancelar"
                                        : $btn_espera);
                                break;
                            case "Tal_Pro":
                                $bg_color = "#337ab7";
                                $solicitud["gestion"] =  $this->admin || $this->fun_arl
                                    ? "$btn_aprobar_arl $btn_negar"
                                    : $btn_espera;
                                break;
                            case "Tal_Neg":
                            case "Tal_Can":
                                $bg_color = "#d9534f";
                                break;
                            default:
                                $color = "black";
                        }

                        break;
                }
                $solicitud[
                    "ver"
                ] = "<span  style='background-color: $bg_color;color: $color; width: 100%;' class='pointer form-control'><span >ver</span></span>";
                if ($solicitud["id_tipo_solicitud"] === "Hum_Posg") {
                    $sw =
                        in_array(
                            ["estado" => $solicitud["id_estado_solicitud"]],
                            $data["estados"]
                        ) &&
                        in_array(
                            ["departamento" => $solicitud["id_departamento"]],
                            $data["departamentos"]
                        );
                    if (
                        $this->admin ||
                        $sw ||
                        $solicitud["usuario_registro"] == $_SESSION["persona"]
                    ) {
                        $solicitudes_th["data"][] = $solicitud;
                    }
                } elseif ($solicitud["id_tipo_solicitud"] === "Hum_Prec") {
                    $sw =
                        !is_null($permiso_estado) &&
                        in_array(
                            ["departamento" => $solicitud["departamento_id"]],
                            $data_prec["departamentos"]
                        );
                    if (
                        $this->admin ||
                        $sw ||
                        $solicitud["usuario_registro"] == $_SESSION["persona"]
                    ) {
                        $solicitudes_th["data"][] = $solicitud;
                    }
                } else {
                    $solicitudes_th["data"][] = $solicitud;
                }
            }
        }
        if (
            !empty($estado) ||
            !empty($tipo) ||
            !empty($fecha_i) ||
            !empty($fecha_f)
        ) {
            $solicitudes_th["filter"] = true;
        }
        $solicitudes_th["deps"] = $data;
        echo json_encode($solicitudes_th);
        return;
    }

	public function modificar_postulante_solicitud()
	{
		if ($this->Super_estado == false) {
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		} else {
			if ($this->Super_modifica == 0) {
				$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			} else {
				
				$id = $this->input->post('id');
				$id_postulante = $this->input->post('id_postulante');
				$id_tipo = $this->admin || $this->admin_th ? $this->input->post('id_tipo') : 'Tip_Cam';
				$procedencia =  $this->input->post('procedencia');
				$id_departamento = (int) $this->input->post('id_departamento');
				$id_cargo =  (int) $this->input->post('id_cargo');
				$id_cargo_actual =  null;
				$id_departamento_actual = null;
				$id_formacion = (int) $this->input->post('id_formacion');
				$observaciones =  $this->input->post('observaciones');
				$usuario_registra = (int) $_SESSION['persona'];
				$hoja_vida = null;
				$prueba_psicologia = null;
				$str = $this->verificar_campos_string(['Postulante'=>$id_postulante,'Procedencia'=>$procedencia,'Departamento'=>$id_departamento,'Cargo'=>$id_cargo,'Formacion'=>$id_formacion,'Tipo'=>$id_tipo,]);
				if (is_array($str) && $id_tipo != 'Tip_Cam_Plan') {
					$campo = $str['field'];
					$resp = ['mensaje'=>"El campo $campo no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					$postulante = $this->talento_humano_model->buscar_postulantes_csep_id("ps.id = $id");
					$estado = $postulante[0]['id_estado_solicitud'];
					$registrado_por = $postulante[0]['usuario_registra'];
					if($estado == 'Pos_Env'){
						if ($registrado_por == $usuario_registra || $this->admin) {
							$sw = true;
							$postulado = '';
							if ($id_tipo == 'Tip_Cam' || $id_tipo == 'Tip_Cam_Plan') {
								$id_cargo_actual =  (int) $this->input->post('id_cargo_actual');
								$id_departamento_actual =  (int) $this->input->post('id_departamento_actual');
								$postulado = $this->talento_humano_model->buscar_ultima_postulacion("ps.id_postulante = $id_postulante AND ps.id_estado_solicitud = 'Pos_Con'");
								if (empty($postulado) && empty($id_cargo_actual)) {
									$resp = ['mensaje'=>"El postulante seleccionado no registra un proceso de contratación  en AGIL, por favor especificar la información actual del postulante.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
									$sw = false;
								}
							}
							if($sw){
								$file = $id_tipo != 'Tip_Cam_Plan' ? $this->cargar_archivo("hoja_vida", $this->ruta_hojas, 'hoja'): [1,null];
								if ($file[0] == -1){
									$error = $file[1];
									if ($error != "<p>You did not select a file to upload.</p>") {
										$resp = ['mensaje'=>"Error al cargar la hoja de vida del postulante.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
										$sw = false;
									}else{
										if (!is_null($postulante[0]['hoja_vida'])) {
											$hoja_vida = $postulante[0]['hoja_vida'];
										}else{
											$resp = ['mensaje'=>"Debe adjuntar la hoja de vida del postulante.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
											$sw = false;
										}
									}
								}else{
									$hoja_vida = $file[1];
								}      
								$file2 = $id_tipo != 'Tip_Cam_Plan' ? $this->cargar_archivo("prueba_psicologia", $this->ruta_hojas, 'prueba'): [1,null];
								if ($file2[0] == -1){
									$error = strpos($file2[1], 'You did not select a file to upload');
									if ($error === false) {
										$resp = ['mensaje'=>"Error al cargar el informe evaluativo del postulante.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
										$sw = false;
									}else{
										if (!is_null($postulante[0]['prueba_psicologia'])) {
											$prueba_psicologia = $postulante[0]['prueba_psicologia'];
										}else{
											$resp = ['mensaje'=>"Debe adjuntar el informe evaluativo del postulante.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
											$sw = false;
										}
									}
								}else{
									$prueba_psicologia = $file2[1];
								}
								
								if($sw){
									$data = array(
										'id_tipo'=>$id_tipo,
										'id_postulante'=>$id_postulante,
										'procedencia'=>$id_tipo != 'Tip_Cam_Plan' ? $procedencia : null,
										'id_formacion'=>$id_tipo != 'Tip_Cam_Plan' ? $id_formacion: null,
										'hoja_vida'=>$hoja_vida,
										'prueba_psicologia'=>$prueba_psicologia,
										'observaciones'=>$observaciones,
										'id_postulacion'=>empty($postulado) ? null : $postulado->{'id'},
										'id_departamento_postulante' => $id_tipo != 'Tip_Cam_Plan' ? $id_departamento: null,
										'id_cargo_postulante' => $id_tipo != 'Tip_Cam_Plan' ? $id_cargo: null,
										'id_departamento_actual_postulante' => $id_departamento_actual,
										'id_cargo_actual_postulante' => $id_cargo_actual,
									);
									
									$add = $this->talento_humano_model->modificar_datos($data, "postulantes_csep",$id);
									$resp= ['mensaje'=>"El postulante fue modificado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
									if ($add != 0) $resp = ['mensaje'=>"Error al modificar el postulante, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
								}
							}
						}else{
							$resp = ['mensaje'=>"No tiene permisos para realizar esta acción.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
						}
					}else{
						$resp = ['mensaje'=>"No es posible continuar, La solicitud fue gestionada anteriormente o se encuentra terminada.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
					}
				
				}
			
			}
		}
		
		echo json_encode($resp);
	}

	public function listar_estados_csep()
	{
		$id_postulante = $this->input->post('id_postulante');
		$estados = $this->Super_estado == true ? $this->talento_humano_model->listar_estados_csep($id_postulante) : array();
		echo json_encode($estados);
	}

	public function listar_comites()
	{
		$estado = $this->input->post('estado');
		$vista = $this->input->post('vista');
		$comites = $this->Super_estado == true ? $this->talento_humano_model->listar_comites(null, $estado, $vista) : array();
		echo json_encode($comites);
	}

	public function cambiar_estado_comite()
	{
		if ($this->Super_estado == false) {
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		} else {
			if ($this->Super_modifica == 0) {
				$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			} else {
				$id = $this->input->post('id');
				$estado = $this->input->post('estado');
				$usuario_registra = $_SESSION['persona'];
				$str = $this->verificar_campos_string(['ID'=>$id,'Estado'=>$estado,]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"Error al cargar el $campo, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
				}else{
					$valido = $this->validar_estado_comite($estado,$id);
					if($valido){
						$data = array('id_estado_comite'=>$estado,);
						$add = $this->talento_humano_model->modificar_datos($data, "comites",$id);
						$resp= ['mensaje'=>"El estado fue modificado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
						if ($add != 0) $resp = ['mensaje'=>"Error al cambiar el estado, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
					}else{
						$resp = ['mensaje'=>"No es posible continuar, el comité fue gestionado anteriormente o no cuenta con postulantes asignados.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
					}

				}
			}

		}
		echo json_encode($resp);
	}
	public function validar_estado_comite($estado_nue,$id)
	{
		$comite = $this->talento_humano_model->listar_comites($id)[0];
		$estado_actual =  $comite['id_estado_comite'];
		$total =  $comite['total'];
		if($total > 0){
			if($estado_actual == 'Com_Ini' && $estado_nue == 'Com_Not' ){
				return true;
			}else if($estado_actual == 'Com_Not' && $estado_nue == 'Com_Ter'){
				return true;
			}
		} 
		return false;
	}
	public function aprobar_todos_postulantes_comite()
	{
		$add = 0;
		if ($this->Super_estado == false) {
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		} else {
			if ($this->Super_modifica == 0) {
				$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			} else {
				$id_comite = $this->input->post('id_comite');
				$estado = $this->input->post('estado');
				$usuario_registra = $_SESSION['persona'];

				$cargo = $this->talento_humano_model->aprueba_persona_csep($usuario_registra);
				$estado = $cargo != 'Apr_Csep'? 'Pos_Bue':'Pos_Apr';
				$add = $this->talento_humano_model->aprobar_todos_postulantes_comite($id_comite,$usuario_registra,$estado);
				$resp= ['mensaje'=>"Todos los postulantes disponibles fueron aprobados.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
				if ($add == 0){
					$resp = ['mensaje'=>"No es posible continuar, todos los postulantes disponibles ya se encuentran aprobado o Negados.",'tipo'=>"info",'titulo'=> "Oops.!"];   
				}else{
					if ($estado == 'Pos_Apr') {
						$total = $this->talento_humano_model->faltantes_aprobar_comite($id_comite);
						if ($total == 0) {
							$data = array('id_estado_comite'=>'Com_Ter');
							$mod = $this->talento_humano_model->modificar_datos($data, "comites",$id_comite);
							$resp= ['mensaje'=>"El estado fue modificado con exito y el comité pasa a estado terminado",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!",'up_comite' => 'si'];
							if ($mod != 0)$resp = ['mensaje'=>"Error al modificar el estado del comité, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];        
						}
					}
				}
			}
		}
		echo json_encode($resp);
	}
	public function obtener_programas(){
		if($this->Super_estado == true){
			$buscar = $this->input->post('buscar');
			switch ($buscar) {
				case 1:
					$programas = $this->talento_humano_model->get_where('valor_parametro', ['idparametro' => 91, 'estado' => 1])->result_array();
					break;
				case 2:
					$programas = $this->talento_humano_model->obtener_programas($buscar);
					break;
				case 3:
					$programas = $this->talento_humano_model->cargar_dependencias();
					break;
			}
		} else $programas = array();
		echo json_encode($programas);
	}

	public function modificar_plan_trabajo(){
		if ($this->Super_estado == false) {
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		} else {
			if ($this->Super_modifica == 0) {
				$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			} else {
				$id = $this->input->post('id');
				$mensaje = $this->input->post('mensaje');

				$str = $this->verificar_campos_string(['Plan de Trabajo'=>$mensaje,]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"Debe ingresar el $campo.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					$postulante = $this->talento_humano_model->buscar_postulantes_csep_id("ps.id = $id");
					$estado = $postulante[0]['id_estado_solicitud'];
					if ($estado == 'Pos_Act') {
						$data = array('plan_trabajo'=>$mensaje,);
						$add = $this->talento_humano_model->modificar_datos($data, "postulantes_csep",$id);
						$resp= ['mensaje'=>"El plan de trabajo fue modificado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
						if ($add != 0) $resp = ['mensaje'=>"Error al modificar el plan de trabajo, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
					}else{
						$resp = ['mensaje'=>"No es posible continuar, La solicitud fue gestionada anteriormente o se encuentra terminada.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
					}
					
				}
				
			}
		}
		echo json_encode($resp);
	}

	public function listar_personas_vb_csep()
	{
		$personas = $this->Super_estado == true ? $this->talento_humano_model->listar_personas_vb_csep() : array();
		echo json_encode($personas);
	}
	public function obtener_programas_persona()
	{
		$id = $this->input->post('id');
		$personas = $this->Super_estado == true && !empty($id) ? $this->talento_humano_model->obtener_programas_persona($id) : array();
		echo json_encode($personas);
	}
	public function asignar_programa_persona()
	{
		if ($this->Super_estado == false) {
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		} else {
			if ($this->Super_agrega == 0) {
				$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			} else {
				$id_persona = (int) $this->input->post('id_persona');
				$id_permiso = (int) $this->input->post('id_permiso');
				//$id_tipo = $this->input->post('id_tipo');
				$id_tipo = 'Apr_Nor';
				$usuario_registra = $_SESSION['persona'];

				$str = $this->verificar_campos_string(['Persona'=>$id_persona,'Programa'=>$id_permiso,'Tipo'=>$id_tipo]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = [
						'mensaje' => "Seleccione $campo.",
						'tipo' => "info",
						'titulo' => "Oops.!"
					];
				}else{
					$tiene = $this->talento_humano_model->tiene_programa_persona($id_permiso, $id_persona);
					if (empty($tiene)) {
						$data = [
							'id_persona'=>$id_persona,
							'id_permiso'=>$id_permiso,
							'usuario_registra'=>$usuario_registra,
							'id_tipo'=>$id_tipo,
						];
						
						$add = $this->talento_humano_model->guardar_datos($data, "permisos_personas_csep");
						$resp = [
							'mensaje' => "El programa fue asignado con exito.",
							'tipo' => "success",
							'titulo' => "Proceso Exitoso.!"
						];
						if (!$add) $resp = [
							'mensaje' => "Error al asignar el programa, contacte con el administrador.",
							'tipo' => "error",
							'titulo' => "Oops.!"
						];
					}else{
						$resp = [
							'mensaje' => "No es posible continuar, ya que La persona seleccionada cuenta con este programa asignado.",
							'tipo' => "info",
							'titulo' => "Oops.!"
						];
					}
		
				}
			}
			
		
		}
		echo json_encode($resp);

		}

		public function retirar_programa_persona()
		{
			if ($this->Super_estado == false) {
				$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
			} else {
				if ($this->Super_elimina == 0) {
					$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
				} else {
					$id = $this->input->post('id');
					$resp= ['mensaje'=>"Programa retirado de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
					$del = $this->talento_humano_model->eliminar_datos("permisos_personas_csep",$id);
					if ($del != 0) $resp = ['mensaje'=>"Error al retirar el programa, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
					
				}
			}
			echo json_encode($resp);
		}

	public function asignar_todos_programas()
	{
		$add = 0;
		if ($this->Super_estado == false) {
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		} else {
			if ($this->Super_agrega == 0) {
				$resp = [
					'mensaje' => "No tiene Permisos Para Realizar Esta operación.",
					'tipo' => "error",
					'titulo' => "Oops.!"
				];
			} else {
				$id_persona = $this->input->post('id_persona');
				$id_tipo = 'Apr_Nor';
				$usuario_registra = $_SESSION['persona'];
				$str = $this->verificar_campos_string(['Persona'=>$id_persona,'Tipo'=>$id_tipo]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"Seleccione $campo.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					$add = $this->talento_humano_model->asignar_todos_programas($id_persona,$usuario_registra,$id_tipo);
					$resp= [
						'mensaje' => "Todos los programas faltantes fueron asignados.",
						'tipo' => "success",
						'titulo' => "Proceso Exitoso.!"
					];
					if (!$add) $resp = [
						'mensaje' => "No es posible continuar, la persona ya cuenta con todos los programas asignados.",
						'tipo' => "info",
						'titulo' => "Oops.!"
					];
				}
			}
		}
		echo json_encode($resp);
	}
	public function retirar_todos_programas()
	{
		if ($this->Super_estado == false) {
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		} else {
			if ($this->Super_elimina == 0) {
				$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			} else {
				$id_persona = $this->input->post('id_persona');
				$str = $this->verificar_campos_string(['Persona'=>$id_persona]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"Seleccione $campo.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					$resp= ['mensaje'=>"Programas retirados de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
					$del = $this->talento_humano_model->retirar_todos_programas($id_persona);
					if ($del != 0) $resp = ['mensaje'=>"Error al retirar los programas, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
				}
			}
		}
		echo json_encode($resp);
	}

	public function cambiar_persona(){
		if ($this->Super_estado == false) {
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		} else {
			if ($this->Super_modifica== 0) {
				$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			} else {
				$id_persona = $this->input->post('id_persona');
				$str = $this->verificar_campos_string(['Persona'=>$id_persona]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"Seleccione $campo.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					$resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Cambio Realizado.!"];
					$mod = $this->talento_humano_model->modificar_datos(['cod_encargado' => null], "personas",'Apr_Csep','cod_encargado');
					if ($mod != 0){
						$resp = ['mensaje'=>"Error al cambiar la persona, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
					}else{
						$mod = $this->talento_humano_model->modificar_datos(['cod_encargado' => 'Apr_Csep'], "personas",$id_persona);
						if ($mod != 0) $resp = ['mensaje'=>"Error al cambiar la persona, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
					}   
				}
			}
			echo json_encode($resp);
		}
	}

	public function agregar_prestamo(){
		if (!$this->Super_estado) $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if (!$this->Super_agrega) {
				$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			} else {
				$valor = $this->input->post('valor');
				$cuotas =  $this->input->post('cuotas');
				$motivo =  $this->input->post('motivo');
				$tipo =  $this->input->post('tipo_prestamo');
				$usuario_registra = $_SESSION['persona'];
				$volante = null;
				// Retorna la cantidad de cuotas maximo del tipo de solicitud recibido
				$max_cuotas = $this->talento_humano_model->get_max_cuotas($tipo);
				if ($max_cuotas == 0) {
					echo json_encode(['mensaje'=>"Verifique el tipo de prestamo.",'tipo'=>"info",'titulo'=> "Oops.!"]);
					return;
				}else if($cuotas > $max_cuotas){
					echo json_encode(['mensaje'=>"El valor debe ser menor a $max_cuotas.",'tipo'=>"info",'titulo'=> "Oops.!"]);
					return;
				}else if($cuotas < 1) {
					$resp = ['mensaje'=>"El Numero de cuotas  debe ser mayor a 0.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
					echo json_encode($resp);
					return;
				}
				$str = $this->verificar_campos_string(['Motivo' => $motivo, 'Tipo Prestamo' => $tipo]);
				$num = $this->verificar_campos_numericos(['Valor' => $valor,'Cuotas' => $cuotas]);

				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje' => "El campo $campo no puede estar vació.",'tipo' =>"info",'titulo' => "Oops.!"]; 
				}else if (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else if ($valor < 1) {
					$resp = ['mensaje'=>"El valor debe ser mayor a 0.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else {
					$sw = true;
					if ($tipo == 'Pre_Cru') {
						$file = $this->cargar_archivo("volante", $this->ruta_volantes, 'volante');
						if ($file[0] == -1){
							$error = $file[1];
							if ($error == "<p>You did not select a file to upload.</p>") {
								$resp = ['mensaje'=>"Debe adjuntar el volante de matrícula.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
								$sw = false;
							}else{
								$resp = ['mensaje'=>"Error al cargar el volante de matrícula.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
								$sw = false;
							} 
						}else $volante = $file[1];
					}
					if ($sw) {
						$data = array('id_tipo_solicitud'=>'Hum_Pres','usuario_registro'=>$usuario_registra,);
						$add = $this->talento_humano_model->guardar_datos($data, "solicitudes_talento_hum");
						if (!$add){
							$resp = [
								'mensaje' => "Error al guardar la solicitud, contacte con el administrador.",
								'tipo' => "error",
								'titulo' => "Oops.!"
							];
						}else{
							$id_solicitud = $this->talento_humano_model->buscar_solicitud_hoy('Hum_Pres');
							if(!is_null($id_solicitud)){
								$data = array(
									'valor' => $valor,
									'cuotas' => $cuotas,
									'motivo' => $motivo,
									'tipo_prestamo' => $tipo,
									'id_usuario_registra' => $usuario_registra,
									'id_solicitud' => $id_solicitud,
									'volante' => $volante,
								);
								$add = $this->talento_humano_model->guardar_datos($data, "prestamos_th");
								if (!$add) {
									$resp = [
										'mensaje' => "Error al guardar el prestamo, contacte con el administrador.",
										'tipo' => "error",
										'titulo' => "Oops.!"
									];
								}else{
									$data = [
										"solicitud_id" => $id_solicitud,
										"estado_id" => 'Tal_Env',
										"usuario_id" => $_SESSION['persona']
									];
									$res = $this->talento_humano_model->guardar_datos($data, 'estados_solicitudes_talento');
									$resp = $res ? [
										'mensaje' => "El prestamo fue registrado con exito.",
										'tipo' => "success",
										'titulo' => "Proceso Exitoso.!", 
										'id' => $id_solicitud
									] : [
										'mensaje' => 'Ha ocurrido un error al intentar registrar el prestamo',
										'tipo' => 'error',
										'titulo' => 'Ooops!'
									];
								}
							} else $resp = [
								'mensaje' => "Error al traer la solicitud, contacte con el administrador.",
								'tipo' => "error",
								'titulo' => "Oops.!"
							];
						}
					}
				}
			}
		}
		echo json_encode($resp);
	}

	public function traer_correo_notifica_comite(){
		$id = $this->input->post('id');
		$correos = $this->Super_estado
			? $this->talento_humano_model->traer_correo_notifica_comite($id)
			: array();
		echo json_encode($correos);
	}

	public function detalle_solicitud(){
		$tipo = $this->input->post('tipo');
		$id = $this->input->post('id');
		$datos = $this->talento_humano_model->detalle_solicitud($id, $tipo);
		echo json_encode($datos);
		return;
	}
	public function get_info_ausentismo_vacaciones(){
		$id = $this->input->post('id');
		$datos = $this->talento_humano_model->get_info_ausentismo_vacciones($id);
		echo json_encode($datos);
		return;
	}
	public function get_info_ausentismo_licencia(){
		$id = $this->input->post('id');
		$datos = $this->talento_humano_model->get_info_ausentismo_licencia($id);
		echo json_encode($datos);
		return;
	}

	public function get_descuentos(){
		if (!$this->Super_estado) $datos = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$id = $this->input->post('id');
			$datos = $this->talento_humano_model->get_descuentos($id);
		}
		echo json_encode($datos);
	}

	public function traer_descuentos(){
		if (!$this->Super_estado) $descuentos = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$descuentos = [];
			$descuentos['salud'] = $this->genericas_model->obtener_valores_parametro_aux('DescSalud', '20');
			$descuentos['pension'] = $this->genericas_model->obtener_valores_parametro_aux('DescPension', '20');
			$descuentos['correo_th'] = $this->genericas_model->obtener_valores_parametro_aux('Par_TH', '20');
			$cuotas = $this->talento_humano_model->get_cuotas();
			$descuentos['libre'] = (int) $cuotas->{'libre'};
			$descuentos['cruce'] = (int) $cuotas->{'cruce'};
		}
		echo json_encode($descuentos);
	}

	public function modificar_configuraciones(){
		if (!$this->Super_estado) $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$sw = false;
			if ($this->Super_modifica && $this->admin) {
				$tipo = $this->input->post('tipo');
				switch ($tipo) {
					case 'descuentos':
						$salud = $this->input->post('salud');
						$pension = $this->input->post('pension');
						$fields = ['Salud'=>$salud,'Pension'=>$pension];
						break;
					case 'cuotas':
						$libre = $this->input->post('cuotas_libre');
						$cruce = $this->input->post('cuotas_cruce');
						$fields = ['Cuotas Prestamo Cruce'=>$cruce,'Cuotas Prestamo Libre'=>$libre];
						break;
					case 'responsable':
						$correo = $this->input->post('correo');
						$sw = true;
						break;
				}
				$num = !$sw ? $this->verificar_campos_numericos($fields) : '';
				if (is_array($num) && !$sw) {
					$campo = $num['field'];
					$resp = ['mensaje'=>"El campo $campo debe ser numérico, no puede estar vacio y debe estar entre 0 y 100.",'tipo'=>"info",'titulo'=> "Oops.!"];
				}else{
					switch ($tipo) {
					case 'descuentos':
						$res = $this->talento_humano_model->modificar_porcentajes($salud, $pension);
						break;
					case 'cuotas':
						$res = $this->talento_humano_model->modificar_cuotas($libre, $cruce);
						break;
					case 'responsable':
						$res = $this->talento_humano_model->modificar_datos(['valor' => $correo], 'valor_parametro', 'Par_TH', 'id_aux');
						break;
					}
					$resp = $res 
						? ['mensaje'=>"Ha ocurrido un error al actualizar los datos.",'tipo'=>"error",'titulo'=> "Oops.!"]
						: ['mensaje'=>"Los datos han sido actualizados exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
				}
			} else $resp = ['mensaje' => 'No tiene Permisos Para Realizar Esta operación.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
		}
		echo json_encode($resp);
	}

	public function get_historial(){
		if (!$this->Super_estado) $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$id = $this->input->post('id');
			$data = $this->talento_humano_model->get_historial($id);
		}
		echo json_encode($data);
	}

	public function listar_personas(){
		$texto = $this->input->post('texto');
		$data = $texto ? $this->talento_humano_model->listar_personas($texto) : [];
		echo json_encode($data);
	}

	public function listar_actividades(){
		$persona = $this->input->post('persona');
		$data = (isset($persona) && !empty($persona))
			? $this->talento_humano_model->listar_actividades($persona)
			: [];
		echo json_encode($data);
	}

	public function asignar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_agrega) {
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			$ok = $this->talento_humano_model->validar_asignacion_actividad($actividad, $persona);
			if ($ok) {
				$data = ['actividad_id'=>$actividad, 'persona_id'=>$persona, 'usuario_registra'=>$_SESSION['persona']];
				$resp = $this->talento_humano_model->guardar_datos($data, 'actividad_persona_th');
				$res = $resp ? [
					'mensaje' => "Actividad asignada exitosamente.",
					'tipo' => "success",
					'titulo' => "Proceso Exitoso!"
				] : [
					'mensaje' => "Ha ocurrido un error al asignar la actividad.",
					'tipo' => "info",
					'titulo' => "Ooops!"
				];
			} else $res = [
				'mensaje' => "El usuario ya tiene asignada esta actividad.",
				'tipo' => "info",
				'titulo' => "Ooops!"
			];
		} else $res = [
			'mensaje' => 'No tiene Permisos Para Realizar Esta operación.',
			'tipo' => 'error',
			'titulo' => 'Oops.!'
		];
		echo json_encode($res);
	}

	public function quitar_actividad(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else if ($this->Super_modifica) {
			$id = $this->input->post('asignado');
			$actividad = $this->input->post('id');
			$persona = $this->input->post('persona');
			// Verifico si actividad ya está asignada o no. Esta función retorna 0 si no está asignada la actividad y 1 si lo está.
			$ok = $this->talento_humano_model->validar_asignacion_actividad($actividad, $persona);
			if (!$ok) {
				$resp = $this->talento_humano_model->quitar_actividad($id);
				if ($resp) {
					$res = $resp ? [
						'mensaje' => "Actividad Desasignada exitosamente.",
						'tipo' => "success",
						'titulo' => "Proceso Exitoso!"
					] : [
						'mensaje' => "Ha ocurrido un error al desasignar la actividad.",
						'tipo' => "info",
						'titulo' => "Ooops!"
					];
				} else $res = [
					'mensaje' => "Ha ocurrido un error al desasignar la actividad.",
					'tipo' => "info",
					'titulo' => "Ooops!"
				];
			} else $res = [
				'mensaje' => "El usuario no tiene asignada esta actividad.",
				'tipo' => "info",
				'titulo' => "Ooops!"
			];
		} else $res = [
			'mensaje' => 'No tiene Permisos Para Realizar Esta operación.',
			'tipo' => 'error',
			'titulo' => 'Oops.!'
		];
		echo json_encode($res);
	}

	public function listar_estados(){
		if (!$this->Super_estado) $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$actividad = $this->input->post('actividad');
			$data = $this->talento_humano_model->listar_estados($actividad);
		}
		echo json_encode($data);
	}

	public function asignar_estado(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->talento_humano_model->validar_asignacion_estado($estado, $actividad, $persona);
				if ($ok) {
					$data = [
						'estado_id' => $estado,
						'actividad_id' => $actividad,
						'usuario_registra' => $_SESSION['persona']
					];
					$resp = $this->talento_humano_model->guardar_datos($data, 'estados_actividades_th');
					$res = $resp ? [
						'mensaje' => "Estado asignado exitosamente.",
						'tipo' =>"success",
						'titulo' => "Proceso Exitoso!",
					] : [
						'mensaje' => "Ha ocurrido un error al asignar el estado.",
						'tipo' => "info",
						'titulo' => "Ooops!"
					];
				} else $res = [
					'mensaje' => "El usuario ya tiene asignada esta actividad.",
					'tipo' => "info",
					'titulo' => "Ooops!"
				];
			} else $res = [
				'mensaje' => 'No tiene Permisos Para Realizar Esta operación.',
				'tipo' => 'error',
				'titulo' => 'Oops.!',
			];
		}
		echo json_encode($res);
	}

	public function quitar_estado(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$id = $this->input->post('id');
				$ok = $this->talento_humano_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$resp = $this->talento_humano_model->quitar_estado($id);
					$res = $resp ? [
						'mensaje' => "Estado Desasignada exitosamente.",
						'tipo' => "success",
						'titulo' => "Proceso Exitoso!"
					] : [
						'mensaje' => "Ha ocurrido un error al desasignar el estado.",
						'tipo' => "info",
						'titulo' => "Ooops!"
					];
				}else $res = [
					'mensaje' => "El usuario no tiene asignado este estado.",
					'tipo' => "info",
					'titulo' => "Ooops!"
				];
			}else $resp = [
				'mensaje' => 'No cuenta con permisos para realizar esta acción.',
				'tipo' => 'info',
				'titulo' => 'Ooops!'
			];
		}
		echo json_encode($res);
	}

	public function activar_notificacion(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->admin || $this->admin_th) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->talento_humano_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$id = $this->talento_humano_model->get_where('estados_actividades_th', ['actividad_id' => $actividad, 'estado_id' => $estado])->row()->id;
					$resp = $this->talento_humano_model->modificar_datos(['notificacion' => 1], 'estados_actividades_th', $id);
					$res = !$resp ? [
						'mensaje' => "Notificación activada exitosamente.",
						'tipo' => "success",
						'titulo' => "Proceso Exitoso!"
					] : [
						'mensaje' => "Ha ocurrido un error al desasignar el estado.",
						'tipo' => "info",
						'titulo' => "Ooops!"
					];
				} else $res = [
					'mensaje' => "El usuario no tiene asignado este estado.",
					'tipo' => "info",
					'titulo' => "Ooops!"
				];
			}else $resp = [
				'mensaje' => 'No cuenta con permisos para realizar esta acción.',
				'tipo' => 'info',
				'titulo' => 'Ooops!'
			];
		}
		echo json_encode($res);
	}

	public function desactivar_notificacion() {
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->admin || $this->admin_th) {
				$estado = $this->input->post('estado');
				$actividad = $this->input->post('actividad');
				$persona = $this->input->post('persona');
				$ok = $this->talento_humano_model->validar_asignacion_estado($estado, $actividad, $persona);			
				if (!$ok) {
					$id = $this->talento_humano_model->get_where('estados_actividades_th', ['actividad_id' => $actividad, 'estado_id' => $estado])->row()->id;
					$resp = $this->talento_humano_model->modificar_datos(['notificacion' => 0], 'estados_actividades_th', $id);
					$res = !$resp
						? ['mensaje'=>"Estado Desasignada exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						: ['mensaje'=>"Ha ocurrido un error al desasignar el estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
				} else $res = ['mensaje'=>"El usuario no tiene asignado este estado.",'tipo'=>"info",'titulo'=> "Ooops!"];
			}else $resp = ['mensaje' => 'No cuenta con permisos para realizar esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
		}
		echo json_encode($res);
	}

	public function revisar_solicitud(){
		$id = $this->input->post('id');
		$salario = $this->input->post('salario');
		$cupo = $this->input->post('cupo');
		$descuentos = $this->input->post('descuentos');
		$saldo = $this->input->post('saldo');
		$num = $this->verificar_campos_numericos(['Salario'=>$salario,'Cupo'=>$cupo, 'Id'=>$id]);
		if (!is_array($num)) {
			$desc = array();
			if (is_array($descuentos)) {
				foreach ($descuentos as $row) {
					array_push($desc, array('solicitud_id'=>$id, 'concepto'=>$row['concepto'], 'valor'=>$row['valor'],'tipo_descuento'=>$row['tipo_descuento'], 'usuario_registra'=>$_SESSION['persona']));
				}
			}
			$data = ['salario' => $salario, 'cupo' => $cupo, 'saldo' => $saldo];
			return ['tipo'=>'success', 'mensaje'=>'', 'data'=>$data, 'descuentos'=>$desc];
		}else{
			$campo = $num['field'];
			$resp = ['mensaje'=>"El campo $campo debe ser numérico, no puede estar vacio y debe estar entre 0 y 100.",'tipo'=>"info",'titulo'=> "Oops.!"];
			return ['tipo'=>'error', 'mensaje'=>$resp];
		}
	}

	public function aprobar_prestamo(){
		$valor = $this->input->post('valor');
		$cuotas = $this->input->post('cuotas');
		$id = $this->input->post('id');
		$info = $this->talento_humano_model->info_solicitud($id);
		$valor = (isset($valor) && !empty($valor)) ? $valor : $info->{'valor'};
		$cuotas = (isset($cuotas) && !empty($cuotas)) ? $cuotas : $info->{'cuotas'};
		$data = ['valor_aprobado'=>$valor, 'cuotas_aprobadas'=>$cuotas];
		$resp = ['mensaje'=>"Solicitud Aprobada Exitosamente",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
		return ['mensaje'=>$resp, 'data'=>$data];
	}

	public function gestionar_solicitud(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
				$id = $this->input->post('id');
				$nextState = $this->input->post('nextState');
				$success = $this->input->post('success');
				$type = $this->input->post('type');
				$aux = $this->validar_estado($id, $nextState);
				$comentario = "";
				if ($aux == 1) {
					switch ($type) {
						case 'Hum_Cert':
						case 'Hum_Cir':
							$msj = $this->input->post("msj");
							$data = ['id_estado_solicitud' => $nextState, 'observacion' => $msj]; 
							$mod = $this->talento_humano_model->modificar_datos($data, 'solicitudes_talento_hum', $id);
							$resp = !$mod
								? ['mensaje' => 'Solicitud denegada exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
								: ['mensaje' => 'Ha ocurrido un error al intentar denegar la solicitud!', 'tipo' => 'error', 'titulo' => 'Ooops!'];
							break;
						case 'Hum_Pres':
							$sw = true;
							$info = $this->talento_humano_model->info_solicitud($id);
							$data = ['id_estado_solicitud' => $nextState];
							switch ($nextState) {
								case 'Tal_Rev':
									$data_prestamo = $this->revisar_solicitud();
									if ($data_prestamo['tipo'] == 'error') {
										$resp = $data_prestamo['mensaje'];
										$sw = false;
									}
									break;
								case 'Tal_Apr':
									$data_prestamo = $this->aprobar_prestamo();
									$resp = $data_prestamo['mensaje'];
									break;
								case 'Tal_Mal':
								case 'Tal_Neg':
									$comentario = $this->input->post('msj');
									break;
							}
							if ($sw) {
								$cambio = $this->talento_humano_model->modificar_datos($data, 'solicitudes_talento_hum', $id);
								$this->talento_humano_model->modificar_datos(['id_estado_solicitud'=>$nextState], 'prestamos_th', $id, 'id_solicitud');
								if (!$cambio) {
									$resp = ['mensaje' => $success, 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!', 'info'=>$info];
									switch ($nextState) {
										case 'Tal_Rev':
											if ($data_prestamo['descuentos']) $this->talento_humano_model->guardar_datos($data_prestamo['descuentos'], 'descuentos_prestamo', 2);
											$this->talento_humano_model->modificar_datos($data_prestamo['data'], 'prestamos_th', $id, 'id_solicitud');	
											break;
										case 'Tal_Apr':
											$this->talento_humano_model->modificar_datos($data_prestamo['data'], 'prestamos_th', $id, 'id_solicitud');
											break;
										case 'Tal_Neg':
										case 'Tal_Mal':
											$resp['msj'] = $comentario;
											break;
									}
									$data = [
										'solicitud_id' => $id,
										'estado_id' => $nextState,
										'usuario_id' => $_SESSION['persona'],
										'comentario' => $comentario
									];
									$this->talento_humano_model->guardar_datos($data, 'estados_solicitudes_talento');
								} else $resp = [
									'mensaje' => 'Se presentó un error al modificar la solicitud. Por favor contacte con el administrador del sistema.',
									'tipo' => 'error',
									'titulo' => 'Ooops'
								];
							}
							break;
						
						case 'Hum_Sele':
							if($nextState === 'Tal_Can'){
								$data = [
									'proceso_actual_id' => 'Sel_Des',
									'observacion' => 'Proceso Cancelado',
								];
								$mod = $this->talento_humano_model->modificar_datos($data, 'candidatos_seleccion', $id, 'solicitud_id');
							}else $mod = $this->talento_humano_model->modificar_datos(['id_estado_solicitud' => $nextState], 'solicitudes_talento_hum', $id, 'id');
							if(!$mod){
								$resp = ['mensaje' => "Solicitud gestionada exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
								$this->talento_humano_model->guardar_datos(['solicitud_id'=> $id, 'estado_id'=>$nextState, 'usuario_id'=>$_SESSION['persona'], 'comentario'=>$comentario], 'estados_solicitudes_talento');
							}else $resp = ['mensaje' => 'Se presentó un error al modificar la solicitud. Por favor contacte con el administrador del sistema.','tipo' => 'error','titulo' => 'Ooops'];
						case 'Hum_Prec':
						case 'Hum_Admi':
						case 'Hum_Apre':
						case 'Hum_Posg':
						case 'Hum_Afi_Arl':
						case 'Hum_Cob_Arl':	
							$this->talento_humano_model->modificar_datos(['id_estado_solicitud' => $nextState], 'solicitudes_talento_hum', $id, 'id');
							$this->talento_humano_model->guardar_datos(['solicitud_id'=> $id, 'estado_id'=>$nextState, 'usuario_id'=>$_SESSION['persona'], 'comentario'=>$comentario], 'estados_solicitudes_talento');
							$resp = ['mensaje' => "Solicitud gestionada exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
						break;
						case 'Hum_Vac':
						case 'Hum_Lic':
							$comentario = $nextState == 'Tal_Neg' || $nextState == 'Tal_Can' ? $this->input->post('msj') : '';
							$this->talento_humano_model->modificar_datos(['id_estado_solicitud' => $nextState], 'solicitudes_talento_hum', $id, 'id');
							$this->talento_humano_model->guardar_datos(['solicitud_id'=> $id, 'estado_id'=>$nextState, 'usuario_id'=>$_SESSION['persona'], 'comentario'=>$comentario], 'estados_solicitudes_talento');
							$resp = ['mensaje' => "Solicitud gestionada exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
							
						break;
						case 'Hum_Entr_Cargo':
							$comentario = $nextState == 'Tal_vm_Jefe_ecargo' ||  $nextState == 'Tal_vm_Jefe_ecargo1' ||  $nextState == 'Tal_Can'? $this->input->post('msj') : '';
							$add1=$this->talento_humano_model->guardar_datos(['solicitud_id'=> $id, 'estado_id'=>$nextState, 'usuario_id'=>$_SESSION['persona'], 'comentario'=>$comentario], 'estados_solicitudes_talento');
							$id_solicitud=$id;
							$motivo=$this->talento_humano_model->detalle_entrecargo($id);
							$vb=$this->talento_humano_model->get_cantidad_vb_ecargo($id_solicitud);
							if($nextState === 'Tal_Can' || $nextState === 'Tal_Ter' || $nextState === 'Tal_Pro' || $nextState === 'Tal_vm_Jefe_ecargo'  || $nextState === 'Tal_vb_Jefe_ecargo' || $nextState === 'Tal_vb_Jefe_ecargo1' || $nextState === 'Tal_vm_Jefe_ecargo1'){
								$add=$this->talento_humano_model->modificar_datos(['id_estado_solicitud' => $nextState], 'solicitudes_talento_hum', $id, 'id');
							}else if(  $motivo->motivo == "Renuncia"){
								if($vb >= 6){
									$data_vb = ["id_estado_solicitud" => "Tal_Vb_Ter",];
									$this->talento_humano_model->modificar_datos($data_vb, "solicitudes_talento_hum",$id_solicitud,'id');
								}
							}else{
								if($vb >= 5){
									$data_vb = ["id_estado_solicitud" => "Tal_Vb_Ter",];
									$this->talento_humano_model->modificar_datos($data_vb, "solicitudes_talento_hum",$id_solicitud,'id');
								}
							}
							$resp = ['mensaje' => "Solicitud gestionada exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
						break;
						case 'Hum_Cam_Eps':
						case 'Hum_Inc_Eps':
						case 'Hum_Inc_Caja':
						case 'Hum_Tras_Afp':
							$comentario = $nextState == 'Tal_Neg' || $nextState == 'Tal_Can' ? $this->input->post('msj') : '';
							$this->talento_humano_model->modificar_datos(['id_estado_solicitud' => $nextState], 'solicitudes_talento_hum', $id, 'id');
							$this->talento_humano_model->guardar_datos(['solicitud_id'=> $id, 'estado_id'=>$nextState, 'usuario_id'=>$_SESSION['persona'], 'comentario'=>$comentario], 'estados_solicitudes_talento');
							$resp = ['mensaje' => "Solicitud gestionada exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
						break;
					}
				}elseif($aux === -1) $resp = ['mensaje' => 'Esta solicitud ya ha sido gestionada o no tiene permisos para esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
				elseif ($aux === -2) $resp = ['mensaje' => 'La solicitud ya fue gestionada anteriormente.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
				else $resp = ['mensaje' => 'No tiene permisos para gestionar esta solicitud.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
			} else $resp = ['mensaje' => 'No tiene permisos para gestionar esta solicitud.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
			 echo json_encode($resp);
		}
	}

	public function cambiar_estado(){
		$id = $this->input->post('id');
		$state = $this->input->post('state');
		$res = $this->talento_humano_model->cambiar_estado($id, $state);
		echo json_encode(['mensaje' => "Solicitud gestionada exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"]);
	}

	public function validar_estado($id, $nextState){
		$info = $this->talento_humano_model->get_solicitud_prestamo($id);
		$tipo_solicitud = $info->{'id_tipo_solicitud'};
		if($tipo_solicitud === 'Hum_Pres') $tipo_prestamo = $info->{'tipo_prestamo'};
		$state = $info->{'id_estado_solicitud'};
		$solicitante = $info->{'usuario_registro'};
		$aux = false;
		if ($this->estados) {
			foreach ($this->estados as $estado) {
				if (($estado['actividad'] == $tipo_solicitud && $estado['estado'] == $state)) $aux = true;
			}
		}
		$aux_user = ($state == 'Tal_Env' && $solicitante == $_SESSION['persona']) ? true : false;
		$aux_jefe = ($state == 'Tal_Env' && $info->{'jefe_inmediato'} == $_SESSION['persona']) ? true : false;
		if ($this->admin || $aux || $aux_user || $aux_jefe || ($_SESSION['perfil'] === 'Per_Csep' && $tipo_solicitud === 'Hum_Prec')) {
			// SOLICITUDES DE PRESTAMO
			if ($tipo_solicitud === 'Hum_Pres') {
				if (($state === 'Tal_Env' && ($nextState === 'Tal_Can' || $nextState === 'Tal_Neg' || $nextState === 'Tal_Rev')) || 
				($state === 'Tal_Rev' && ($nextState === 'Tal_Mal' || $nextState === 'Tal_Vis' )) || 
				(($state === 'Tal_Vis' || $state === 'Tal_Mal') && (($tipo_prestamo === 'Pre_Lib' && $nextState === 'Tal_Apr') || $nextState === 'Tal_Neg' || ($tipo_prestamo === 'Pre_Cru' && $nextState === 'Tal_Cru'))) || 
				($state === 'Tal_Apr' && $nextState === 'Tal_Pro') || 
				($state === 'Tal_Cru' && $nextState === 'Tal_Apr') ||
				($state === 'Tal_Pro' && ($nextState === 'Tal_Tra')) || 
				($state === 'Tal_Tra' && ($nextState === 'Tal_Des'))) {
					// Cambio de estado permitido.
					return 1;
				}
				// SOLICITUDES DE REQUISICIÓN
			}else if($tipo_solicitud === 'Hum_Prec' || $tipo_solicitud === 'Hum_Admi' || $tipo_solicitud === 'Hum_Apre'){
				if(($state === 'Tal_Env' && ($nextState === 'Tal_Can' || $nextState === 'Tal_Neg' || $nextState === 'Tal_Ter' || $nextState === 'Env_Csea')) || 
					($state === 'Env_Csea' && ($nextState === 'Tal_Ter' || $nextState === 'Tal_Neg'))){
					// Cambio de estado permitido.
					return 1;
				}
				// SOLICITUDES DE SELECCIÓN
			}else if($tipo_solicitud === 'Hum_Sele'){
				if(($state === 'Tal_Env' && ($nextState === 'Tal_Can' || $nextState === 'Tal_Pro')) || 
					($state === 'Tal_Pro' && ($nextState === 'Tal_Ter' || $nextState === 'Tal_Can'))){
					// Cambio de estado permitido. Solicitudes de Selección
					return 1;
				}
				// SOLICITUDES DE CERTIFICADO
			}else if($tipo_solicitud === 'Hum_Cert'){
				if($state === 'Tal_Env' && ($nextState === 'Tal_Ter' || $nextState === 'Tal_Neg' || $nextState === 'Tal_Can')){
					// Cambio de estado permitido. Solicitudes de Certificados
					return 1;
				}
			} else if($tipo_solicitud === 'Hum_Posg'){
				if(
					($state === 'Tal_Env' && ($nextState === 'Tal_Can' || $nextState === 'Tal_Neg' || $nextState === 'Tal_Ter' || $nextState === 'Tal_Pro')) ||
					($state === 'Tal_Pro' && ($nextState === 'Tal_Can' || $nextState === 'Tal_Neg' || $nextState === 'Tal_Ter' ))
				){
					// Cambio de estado permitido.
					return 1;
				}
				// SOLICITUD DE ARL
			} else if($tipo_solicitud === 'Hum_Afi_Arl' || $tipo_solicitud === 'Hum_Cob_Arl'){
				if(($state === 'Tal_Env' && ($nextState === 'Tal_Pro' || $nextState === 'Tal_Can')) || ( $state === 'Tal_Pro' &&  ($nextState === 'Tal_Neg' || $nextState === 'Tal_Can' || $nextState === 'Tal_Ter'))){
					// Cambio de estado permitido. Solicitudes de ARL
					return 1;
				}
			} else if($tipo_solicitud === 'Hum_Cir'){
				if($state === 'Tal_Env' && ($nextState === 'Tal_Ter' || $nextState === 'Tal_Neg' || $nextState === 'Tal_Can')) {
					// Cambio de estado permitido. Solicitudes de ARL
					return 1;
				}
			}else if($tipo_solicitud === 'Hum_Vac' || $tipo_solicitud === 'Hum_Lic'){
				if(($state === 'Tal_Env' && ($nextState === 'Tal_Neg' || $nextState === 'Tal_Can' || $nextState === 'Tal_vb_Jefe')) || ( $state === 'Tal_vb_Jefe' &&  ($nextState === 'Tal_Can' || $nextState === 'Tal_Pro')) || ( $state === 'Tal_Pro' &&  ($nextState === 'Tal_Can' || $nextState === 'Tal_Ter'))){
					// Cambio de estado permitido. Solicitudes ausentismo
					return 1;
				}
			}else if($tipo_solicitud === 'Hum_Cam_Eps' || $tipo_solicitud === 'Hum_Inc_Eps' || $tipo_solicitud === 'Hum_Inc_Caja'){
				//Estados Cambio Eps
				if(($state === 'Tal_Env' && ($nextState === 'Tal_Neg' || $nextState === 'Tal_Can' || $nextState === 'Tal_Pro')) || ( $state === 'Tal_Pro' &&  ($nextState === 'Tal_Can' || $nextState === 'Tal_Apr' ))){
					// Cambio de eps
					return 1;
				}
			}else if ($tipo_solicitud === 'Hum_Tras_Afp'){
				//Estados traslado
				if(($state === 'Tal_Env' && ($nextState === 'Tal_Neg' || $nextState === 'Tal_Can' || $nextState === 'Tal_Pro'))){
					return 1;
				}
			}else if ($tipo_solicitud === 'Hum_Entr_Cargo'){
				if (($state === 'Tal_Env' && ( $nextState === 'Tal_Pro' || $nextState === 'Tal_Can' || $nextState === 'Tal_Neg'))  
				|| ( $state === 'Tal_Pro' &&  (  $nextState === 'Tal_vb_Jefe_ecargo1'  || $nextState === 'Tal_vb_Jefe_ecargo'  || $nextState === 'Tal_vm_Jefe_ecargo' || $nextState === 'Tal_vm_Jefe_ecargo1')) 
				|| ( $state === 'Tal_vm_Jefe_ecargo' &&  (  $nextState === 'Tal_vb_Jefe_ecargo1'  || $nextState === 'Tal_Vb_Ter' || $nextState === 'Tal_vm_Jefe_ecargo1' ))
				|| ( $state === 'Tal_vb_Jefe_ecargo' &&  (  $nextState === 'Tal_vb_Jefe_ecargo1'  || $nextState === 'Tal_Vb_Ter' || $nextState === 'Tal_vm_Jefe_ecargo1' )) 
				|| ( $state === 'Tal_vm_Jefe_ecargo1' &&  (  $nextState === 'Tal_vb_Jefe_ecargo'  || $nextState === 'Tal_Vb_Ter' || $nextState === 'Tal_vm_Jefe_ecargo' )) 
				|| ( $state === 'Tal_vb_Jefe_ecargo1' &&  (  $nextState === 'Tal_vb_Jefe_ecargo'  || $nextState === 'Tal_Vb_Ter' || $nextState === 'Tal_vm_Jefe_ecargo' )) 
				|| ( $state === 'Tal_Vb_Ter' &&  ( $nextState === 'Tal_Ter'  || $nextState === 'Tal_Can' ))){
					return 1;
				}
			}
			// Cambio de estado no permitido.
			return -2;
		}
		// No tiene los permisos para realizar la operación
		return -1;
	}

	public function cargar_archivo_solicitud(){
		if (!$this->Super_estado) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$id = $this->input->post("id");
			$nombre = $_FILES["file"]["name"];
			$cargo = $this->cargar_archivo("file", $this->ruta_adjuntos, "compdw");
			if ($cargo[0] == -1) {
				header("HTTP/1.0 400 Bad Request");
				echo ("$nombre .l.");
				return;
			}
			$data = [
				'id_solicitud' => $id,
				'nombre_real' => $nombre,
				'nombre_archivo' => $cargo[1],
				'usuario_registra' => $_SESSION['persona']
			];
			$res = $this->talento_humano_model->guardar_datos($data, 'archivos_adj_th');
			if ($res === "error") {
				header("HTTP/1.0 400 Bad Request");
				echo ("$nombre asas");
				return;
			}
		}
		echo json_encode($res);
	}

	public function listar_archivos_adjuntos(){
		$id = $this->input->post("id");
		$resp = $this->Super_estado ? $this->talento_humano_model->listar_archivos_adjuntos($id) : array();
		echo json_encode($resp);
	}

	public function buscar_ultima_postulacion(){
		$id = $this->input->post('id');
		$tipo = $this->input->post('tipo');
		$where = $tipo == 1 ? "ps.id_postulante = $id AND ps.id_estado_solicitud = 'Pos_Con'" :"ps.id = $id";
		$postulante = $this->Super_estado == true ? $this->talento_humano_model->buscar_ultima_postulacion($where) : array();
		echo json_encode($postulante);
		return;
	}

	public function get_departamentos(){
		$tipo = $this->input->post("tipo");
		$data = $tipo === 'Vac_Aca'
			? ['idparametro' => 91, 'estado' => 1]
			: [ 'idparametro' => 208, 'estado' => 1];
		$departamentos = $this->talento_humano_model->get_where('valor_parametro', $data)->result_array();
		echo json_encode($departamentos);
	}

	public function listar_cargos(){
		$opt = $this->input->post("opt");
		$cargos = $this->talento_humano_model->get_cargos($opt);
		echo json_encode($cargos);
	}

	public function listar_cargos_departamento_nuevo(){
		$id_departamento = $this->input->post("id_departamento");
		$opt = $this->input->post("opt");
		$cargos = array();
		if($this->Super_estado){
			$cargos = $this->talento_humano_model->listar_cargos_departamento_nuevo($id_departamento, $opt);
		}
		echo json_encode($cargos);
	}

	public function obtener_postulacion_id(){
		$id = $this->Super_estado == true ? $this->input->post("id") : -1 ;
		$datos = $this->talento_humano_model->obtener_postulacion_id($id);
		echo json_encode($datos);
	}

	public function validateMonth($date, $format = 'Y-m'){
		$d = DateTime::createFromFormat($format, $date);
		return $d->format($format) == $date;
	}

	public function cargar_permisos(){
		if ($this->Super_estado) {
			$permisos = $this->talento_humano_model->cargar_permisos();
		}
		echo json_encode($permisos);
	}

	public function uploadImgBase64(){
		$base64 = $this->input->post('image');
		$name = $this->input->post('name');
		$path = $this->input->post('path');
		// decodificamos el base64
		$datosBase64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
		// guardamos la imagen en el server y  retorno si falla o si fue bien
		echo json_encode(!file_put_contents(APPPATH . $path, $datosBase64) ? false : true);
	}

	public function guardar_vacante(){
		if (!$this->Super_estado) $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_modifica == 0) $resp = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else {
				$accion = $this->input->post('accion');
				$id = $this->input->post('id');
				$id_solicitud = $this->input->post('id_solicitud');
				$departamento = $this->input->post('id_programa');
				$cargo_id = $this->input->post('cargo_id');
				$tipo_solicitud = $this->input->post('tipo_solicitud');
				$tipo_vacante = $this->input->post('tipo_vacante');
				$tipo_cargo = $this->input->post('tipo_cargo');
				$observaciones = $this->input->post('observaciones');
				$comentarios = $this->input->post('comentarios');
				$string_fields = [];
				$num_fields = [];
				if($accion){
					$info = $this->talento_humano_model->get_detalle_vacante($id_solicitud);
					$tipo_solicitud = $info['vacante']->{'tipo_solicitud'};
					$tipo_vacante = $info['vacante']->{'tipo_vacante'};
				}
				switch ($tipo_cargo) {
					case 'Vac_Aca':
						$horas = $this->input->post('horas');
						$id_programa = $this->input->post('id_programa');
						$investigacion = $this->input->post('investigacion');
						$linea_investigacion = $this->input->post('linea_investigacion');
						$pregrado = $this->input->post('pregrado');
						$posgrado = $this->input->post('posgrado');
						$reemplazado = $this->input->post('reemplazado');
						$materias = !empty($this->input->post('materias')) ? json_decode($this->input->post('materias'), true) : [];
						$programas = !empty($this->input->post('programas')) ? json_decode($this->input->post('programas'), true) : [];
						$plan_trabajo = $this->input->post('plan_trabajo');
						$observaciones = $this->input->post('observaciones');
						$anos = $this->input->post('anos_experiencia');
						if($accion){
							$pregrado = $info['vacante']->{'pregrado'};
							$posgrado = $info['vacante']->{'posgrado'};
							$horas = $info['vacante']->{'horas'};
						}
						$num_fields = [
							'Horas' => $horas,
							'Programa' => $id_programa,
							'Cargo' => $cargo_id,
							'Departamento' => $departamento
						];
						$string_fields = [
							'Tipo de Solicitud' => $tipo_solicitud,
							'Tipo de Vacante' => $tipo_vacante,
						];
						if ($tipo_solicitud === "Tcsep_Con") {
							$string_fields['Pregrado'] = $pregrado;
							$string_fields['Posgrado'] = $posgrado;
						}
						if ($tipo_vacante === 'Vac_Ree') $num_fields['Persona a Reemplazar'] = $reemplazado;
						if(isset($investigacion) && !empty($investigacion)){
							$string_fields['Linea de Investigacion'] = $linea_investigacion;
							$num_fields['Años de Experiencia'] = $anos;
						}
						break;
					case 'Vac_Apr':
						$departamento = $this->input->post('id_programa');
						$num_fields['Departamento'] = $departamento;
						$string_fields = [
							'Tipo de solicitud' => $tipo_solicitud,
							'Tipo de Vacante' => $tipo_vacante,
							'Observaciones' => $observaciones,
							'Tipo de Cargo' => $tipo_cargo,
						];
						break;
					case 'Vac_Adm':
						$pregrado = $this->input->post('pregrado');
						$posgrado = $this->input->post('posgrado');
						$experiencia = $this->input->post('experiencia');
						$conocimientos_especificos = $this->input->post('conocimientos_especificos');
						$departamento = $this->input->post('id_programa');
						$nombre_cargo = $this->input->post('nombre_cargo');
						$num_fields['Departamento'] = $departamento;
						$string_fields = [
							'Tipo de solicitud' => $tipo_solicitud,
							'Tipo de Vacante' => $tipo_vacante,
							'Nombre del cargo' => $nombre_cargo,
							'Experiencia laboral' => $experiencia,
							'Conocimientos especificos' => $conocimientos_especificos,
							'Observaciones' => $observaciones,
							'Nombre del cargo' => $nombre_cargo,
							'Pregrado' => $pregrado,
							'Posgrado' => $posgrado,
							'Tipo de Cargo' => $tipo_cargo,
						];
						break;
				}
				if (isset($tipo_solicitud) && !empty($tipo_solicitud) && isset($tipo_vacante) && !empty($tipo_vacante)) {
					$str = $this->verificar_campos_string($string_fields);
					$num = $this->verificar_campos_numericos($num_fields);
					if (is_array($str)) {
						$campo = $str['field'];
						// Mensaje respuesta
						$resp = ['mensaje' => "El campo $campo no puede estar vacio.",'tipo' => "info",'titulo' => "Oops.!"];
					}else if (is_array($num)) {
						$campo = $num['field'];
						// Mensaje respuesta
						$resp = ['mensaje' => "El campo $campo debe ser numérico y no puede estar vació.",'tipo' => "info",'titulo' => "Oops.!"];
					}else {
						$data['solicitud'] = ['usuario_registro' => $_SESSION['persona']];
						$data['solicitud']['id_estado_solicitud'] = "Tal_Env";
						if($tipo_cargo === 'Vac_Aca'){
							$data['solicitud']['id_tipo_solicitud'] = 'Hum_Prec';
							$data['solicitud']['id_estado_solicitud'] = ($tipo_solicitud === 'Tcsep_Con') ? 'Tal_Env' : 'Tal_Esp';
						} else if($tipo_cargo === 'Vac_Apr'){
							$data['solicitud']['id_tipo_solicitud'] = 'Hum_Apre';
						}else {
							$data['solicitud']['id_tipo_solicitud'] = 'Hum_Admi';
						}
						$sw = true;
						$file = [1, null];
						if (isset($tipo_solicitud) && $tipo_solicitud == 'Tcsep_Eva' && ($tipo_cargo === 'Vac_Adm' || $tipo_cargo === 'Vac_Aca')) {
							$adj_hoja_vida = $_FILES['hoja_vida']['size'];
							if(empty($adj_hoja_vida) && !$accion){
								$sw = false;
								$resp = ['mensaje' => "Debe adjuntar la Hoja de Vida.",'tipo' => "info",'titulo' => "Oops.!"];
							}else if($accion){
								$data = $this->talento_humano_model->get_detalle_vacante($id_solicitud);
								if (is_null($data['vacante']->{'hoja_vida'}) && empty($adj_hoja_vida)) {
									$sw = false;
									$resp = ['mensaje' => "Debe adjuntar la Hoja de Vida.",'tipo' => "info",'titulo' => "Oops.!"];
								}else $file = [1, $data['vacante']->{'hoja_vida'}];
							}
						}
						if($sw){
							$file = $tipo_solicitud == 'Tcsep_Eva' && !empty($adj_hoja_vida) ? $this->cargar_archivo("hoja_vida", $this->ruta_hojas, 'hoja') : $file;
							if($file[0] == -1){
								$resp = ['mensaje' => "Error al cargar la Hoja de Vida.", 'tipo' => "error", 'titulo'=> "Oops.!"];
							}else{
								$hoja_vida = $file[1];
								if($tipo_cargo === 'Vac_Aca'){
									$data['vacante'] = [
										'anos_experiencia' => $anos ? $anos : null,
										'cargo_id' => $cargo_id,
										'horas' => $horas,
										'linea_investigacion' => $linea_investigacion ? $linea_investigacion : null,
										'pregrado' => $pregrado ? $pregrado : null,
										'posgrado' => $posgrado ? $posgrado : null,
										'tipo_solicitud' => $tipo_solicitud,
										'tipo_vacante' => $tipo_vacante,
										'departamento_id' => $departamento,
										'tipo_cargo' => $tipo_cargo,
										'observaciones' => $observaciones,
										'comentarios' => $comentarios,
										'hoja_vida' => $hoja_vida ? $hoja_vida : null,
										'plan_trabajo' => $plan_trabajo ? $plan_trabajo : null,
									];
									$data['vacante']['reemplazado_id'] = (isset($reemplazado) && !empty($reemplazado)) ? $reemplazado : null;
									$data['materias'] = $materias;
									$data['programas'] = $programas;
									// Guardar solicitud de Talento Humano
									if ($accion) {
										$estado = $this->talento_humano_model->get_estado_solicitud($id_solicitud);
										if($estado == 'Tal_Env' || $estado == 'Env_Csea'){
											$mod = $this->talento_humano_model->modificar_datos($data['vacante'], "vacantes", $id);
											if($mod != 0){
												$resp = ['mensaje' => "Error al modificar los datos de la vacante, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
											}else{
												$resp = ['mensaje' => "Solicitud modificada exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
												$mat = $this->talento_humano_model->modificar_datos(['estado' => 0], "materias_vacante", $id_solicitud,'solicitud_id');
												if (count($data["materias"]) > 0) {
													$materias = [];
													foreach ($data["materias"] as $materia) {
														$subject["solicitud_id"] = $id_solicitud;
														$subject["materia"] = $materia["materia"];
														$materias[] = $subject;
													}
													$this->talento_humano_model->guardar_datos($materias, 'materias_vacante', 2);
												}
											}
										} else $resp = ['mensaje' => "No se permite modificar la solicitud ya ha sido gestionada.", 'tipo' => "info", 'titulo' => "Oops.!"];
									}else{
										$res = $this->talento_humano_model->guardar_vacante($data);
										$estado = $this->talento_humano_model->get_estado_solicitud($res);
										$resp = $res
											// Mensaje respuesta
											? ['mensaje' => "Solicitud guardada exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!", 'id' => $res, 'id_estado' => $estado]
											: ['mensaje' => "Ha ocurrido un error al intentar guardar la solicitud!", 'tipo' => "info", 'titulo' => "Ooops!"];
									} 
								} else if($tipo_cargo === 'Vac_Adm' || $tipo_cargo === 'Vac_Apr'){
									if($tipo_cargo === 'Vac_Apr') $nombre_cargo = '';
									$data['vacante'] = [
										'cargo_id' => $cargo_id,
										'nombre_cargo' => $nombre_cargo,
										'tipo_solicitud' => $tipo_solicitud,
										'tipo_vacante' => $tipo_vacante,
										'tipo_cargo' => $tipo_cargo,
										'departamento_id' => $departamento,
										'observaciones' => $observaciones,
										'hoja_vida' => $hoja_vida ? $hoja_vida : null,
									];
									if($tipo_cargo === 'Vac_Adm') {
										$data['vacante']['pregrado'] = $pregrado ? $pregrado : null;
										$data['vacante']['posgrado'] = $posgrado ? $posgrado : null;
										$data['vacante']['experiencia_laboral'] = $experiencia;
										$data['vacante']['plan_trabajo'] = $conocimientos_especificos;
									}
									if ($accion) {
										$estado = $this->talento_humano_model->get_estado_solicitud($id_solicitud);
										if($estado == 'Tal_Env'){
											$mod = $this->talento_humano_model->modificar_datos($data['vacante'], "vacantes", $id);
											$resp = $mod != 0
												? ['mensaje' => "Error al modificar los datos de la vacante, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"]
												: ['mensaje' => "Solicitud modificada exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
										} else $resp = ['mensaje' => "No se permite modificar la solicitud ya ha sido gestionada.", 'tipo' => "info", 'titulo' => "Oops.!"];
									}else{
										$res = $this->talento_humano_model->guardar_vacante($data);
										$resp = $res
											// Mensaje respuesta
											? ['mensaje' => "Solicitud guardada exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!", 'id' => $res]
											: ['mensaje' => "Ha ocurrido un error al intentar guardar la solicitud!", 'tipo' => "info", 'titulo' => "Ooops!"];
									}
								}
							}
						} else $resp = ['mensaje' => "Ha ocurrido un error al intentar guardar la solicitud!", 'tipo' => "info", 'titulo' => "Ooops!"];
					}
					// Mensaje respuesta
				}else $resp = ['mensaje' => "Por favor elija un tipo de solicitud.", 'tipo' => "info", 'titulo' => "Oops.!"];
			}
		}
		echo json_encode($resp);
	}

	public function get_detalle_vacante(){
		$id = $this->input->post('id');
		$data = $this->talento_humano_model->get_detalle_vacante($id);
		echo json_encode($data);
	}

	public function buscar_persona(){
		$personas = array();
		if ($this->Super_estado) {
			$dato = $this->input->post('dato');
			if (!empty($dato)) $personas = $this->talento_humano_model->buscar_postulante($dato);  
		}
		echo json_encode($personas);
	}

	public function buscar_dependencia(){
		$dependencias = array();
		if ($this->Super_estado) {
			$buscar = $this->input->post('dep');
			if (!empty($buscar)) $dependencias = $this->talento_humano_model->buscar_dependencia($buscar);  
		}
		echo json_encode($dependencias);
	}

	public function get_estados_solicitud() {
		$estados = array();
		if ($this->Super_estado) {
			$id = $this->input->post('id');
			if (!empty($id)) $estados = $this->talento_humano_model->get_estados_solicitud($id);  
		}
		echo json_encode($estados);
	}
	public function get_estados_ecargo() {
		$estados = array();
		if ($this->Super_estado) {
			$id = $this->input->post('id');
			if (!empty($id)) $estados = $this->talento_humano_model->get_estados_ecargo($id);  
		}
		echo json_encode($estados);
	}

	public function get_personas_notificar(){
		$personas = array();
		if ($this->Super_estado) {
			$actividad = $this->input->post('actividad');
			$estado_id = $this->input->post('estado_id');
			$departamento = $this->input->post('departamento');
			$personas = $this->talento_humano_model->get_personas_notificar($actividad, $estado_id, $departamento);
		}
		echo json_encode($personas);
	}

	public function cargar_materias_solicitud(){
		$materias = array();
		if ($this->Super_estado) {
			$id = $this->input->post('id');
			$materias = $this->talento_humano_model->cargar_materias_solicitud($id);
		}
		echo json_encode($materias);
	}

	public function cargar_programas_solicitud(){
		$programas = array();
		if ($this->Super_estado) {
			$id = $this->input->post('id');
			$programas = $this->talento_humano_model->cargar_programas_solicitud($id);
		}
		echo json_encode($programas);
	}

	public function agregar_materia() {
		if (!$this->Super_estado) $resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
		else {
			if (!$this->Super_agrega) $resp = ['mensaje' => 'No tiene Permisos Para Realizar Esta operación.', 'tipo' => 'error', 'titulo' => 'Oops.!'];
			else {
				$id = $this->input->post('id');
				$estado = $this->talento_humano_model->get_estado_solicitud($id);
				if ($estado === 'Tal_Env') {
					$materia = $this->input->post('materia');
					$data = [
						'solicitud_id' => $id, 
						'materia' => $materia,
					];
					$res = $this->talento_humano_model->guardar_datos($data, 'materias_vacante');
					$resp = $res ? [
						'mensaje' => 'Materia guardada exitosamente',
						'tipo' => 'success',
						'titulo' => 'Proceso Exitoso!'
					] : [
						'mensaje' => 'Error al guardar la materia',
						'tipo' => 'error',
						'titulo' => 'Ooops!'
					];
				} else $resp = [
					'mensaje' => 'No se puede agregar materias. Esta solicitud ya ha sido gestionada.',
					'tipo' => 'info',
					'titulo' => 'Ooops!'
				];
			}
		}
		echo json_encode($resp);
	}

	public function eliminar_materia() {
		if (!$this->Super_estado) $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_elimina == 0) $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else {
				$id = $this->input->post('id');
				$estado = $this->talento_humano_model->get_estado_solicitud($id);
				if ($estado === 'Tal_Env') {
					$materia = $this->input->post('materia');
					$data = ['estado'=> -1];
					$res = $this->talento_humano_model->modificar_datos($data, 'materias_vacante', $materia);
					$resp = $res == 0
						? ['mensaje' => 'Materia eliminada exitosamente', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
						: ['mensaje' => 'Ha ocurrido un error al intentar eliminar la materia', 'tipo' => 'error', 'titulo' => 'Ooops!'];
				} else $resp = ['mensaje' => 'No se puede eliminar materias. Esta solicitud ya ha sido gestionada.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
			}
		}
		echo json_encode($resp);
	}

	public function agregar_dependencia(){
		if (!$this->Super_estado) $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_agrega == 0) $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else {
				$id = $this->input->post('id');
				$estado = $this->talento_humano_model->get_estado_solicitud($id);
				if ($estado === 'Tal_Env') {
					$dep = $this->input->post('dep');
					$data = [
						'programa_id' => $dep,
						'solicitud_id' => $id,
					];
					$res = $this->talento_humano_model->guardar_datos($data, 'programas_vacante');
					$resp = $res ? [
						'mensaje' => 'Dependencia guardada exitosamente',
						'tipo' => 'success',
						'titulo' => 'Proceso Exitoso!'
					] : [
						'mensaje' => 'No se pudo guardar la Dependencia',
						'tipo' => 'error',
						'titulo' => 'Ooops!'
					];
				} else $resp = [
					'mensaje' => 'No se puede agregar dependencias. Esta solicitud ya ha sido gestionada.',
					'tipo' => 'info',
					'titulo' => 'Ooops!'
				];
			}
		}
		echo json_encode($resp);
	}

	public function eliminar_dependencia() {
		if (!$this->Super_estado) $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_elimina == 0) $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else {
				$id = $this->input->post('id');
				$dep = $this->input->post('dep');
				$estado = $this->talento_humano_model->get_estado_solicitud($id);
				if ($estado === 'Tal_Env') {
					$data = ['estado'=> -1];
					$res = $this->talento_humano_model->modificar_datos($data, 'programas_vacante', $dep);
					$resp = $res == 0
						? ['mensaje' => 'Dependencia eliminada exitosamente', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
						: ['mensaje' => "No se pudo eliminar la Dependencia", 'tipo' => 'error', 'titulo' => 'Ooops!'];
				} else $resp = ['mensaje' => 'No se puede eliminar dependencias. Esta solicitud ya ha sido gestionada.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
			}
		}
		echo json_encode($resp);
	}

	public function get_estados_asignados_actividad(){
		$estados = array();
		if ($this->Super_estado) {
			$id = $this->input->post('id');
			$estados = $this->talento_humano_model->get_estados_asignados_actividad($id);
		}
		echo json_encode($estados);
	}

	public function cargar_tipos_solicitudes_filtro(){
		$tipos = array();
		if ($this->Super_estado) {
			$vista = $this->input->post('vista');
			$tipos = $this->talento_humano_model->cargar_tipos_solicitudes_filtro($vista);
		}
		echo json_encode($tipos);
	}

	public function revisar_vacante(){
		if (!$this->Super_estado) $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			if ($this->Super_modifica == 0) $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else {
				$id_solicitud = $this->input->post('id');
				$cargo = $this->input->post('cargo_id');
				$plan_trabajo = $this->input->post('plan_trabajo');
				$id_tipo_solicitud = $this->input->post('id_tipo_solicitud');
				$fecha_ingreso = !$this->input->post('fecha_ingreso') ? null : $this->input->post('fecha_ingreso');							
				$tipo_contrato = null;
				$duracion_contrato = null;
				$estado = $this->talento_humano_model->get_estado_solicitud($id_solicitud);
				$num = $this->verificar_campos_numericos(['Cargo' => $cargo, 'Id' => $id_solicitud]);
				$str = $this->verificar_campos_string(['Plan de Trabajo' => $plan_trabajo]);
				if($id_tipo_solicitud === 'Hum_Admi'){
					$tipo_contrato = $this->input->post('tipo_contrato');
					$duracion_contrato = $this->input->post('duracion_contrato');	
					$num = $this->verificar_campos_numericos(['Cargo' => $cargo, 'Id' => $id_solicitud, 'Duración Contrato' => $duracion_contrato]);
					$str = $this->verificar_campos_string(['Plan de Trabajo' => $plan_trabajo, 'Tipo Contrato' => $tipo_contrato]);
				}else if($id_tipo_solicitud === 'Hum_Prec'){
					if($estado === 'Tal_Pro') $str = $this->verificar_campos_string(['Plan de Trabajo' => $plan_trabajo, 'Fecha de Ingreso' => $fecha_ingreso]);
				}
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else if (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					if($id_tipo_solicitud === 'Hum_Prec'){
						$data = [
							'cargo_id' => $cargo,
							'plan_trabajo' => $plan_trabajo,
							'fecha_ingreso' => $fecha_ingreso,
						];
					}else{
						$data = [
							'cargo_id' => $cargo,
							'plan_trabajo' => $plan_trabajo,
							'tipo_contrato' => $tipo_contrato,
							'duracion_contrato' => $duracion_contrato,
						];
					}
					$id = $this->talento_humano_model->get_id_vacante($id_solicitud);
					$mod = $this->talento_humano_model->modificar_datos($data, "vacantes", $id);
					// $this->talento_humano_model->modificar_datos(['id_estado_solicitud' => 'Tal_Ter'], "solicitudes_talento_hum", $id);
					$resp = $mod != 0
						? ['mensaje' => 'Error al modificar el postulante, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"]
						: ['mensaje' => 'Requisición Revisada Exitosamente!', 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
				}
			}
		}
		echo json_encode($resp);
	}

	public function modificar_postulante_cmt()
	{
		if ($this->Super_estado == false) {
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		} else {
			if ($this->Super_modifica == 0) {
				$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			} else {
				
				$id = $this->input->post('id');
				$id_programa =  $this->input->post('id_programa');
				$id_comite =  $this->input->post('id_comite');
				$plan_trabajo =   $this->input->post('plan_trabajo');

				$str = $this->verificar_campos_string(['Postulante'=>$id,'Programa'=>$id_programa,'Comité'=>$id_comite,'Plan de Trabajo'=>$plan_trabajo,]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"El campo $campo no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					$postulante = $this->talento_humano_model->buscar_postulantes_csep_id("ps.id = $id");
					if (!empty($postulante)) {

						$vb = $postulante[0]['vb'];
						$vm = $postulante[0]['vm'];

						if ($vb == 0 && $vm == 0) {
							$data = array(
								'id_comite'=>$id_comite,
								'id_programa'=>$id_programa,
								'plan_trabajo'=>$plan_trabajo,
							);
	
							$add = $this->talento_humano_model->modificar_datos($data, "postulantes_csep",$id);
							$resp= ['mensaje'=>"El postulante fue modificado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
							if ($add != 0) $resp = ['mensaje'=>"Error al modificar el postulante, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
						}else{
							$resp = ['mensaje'=>"El postulante ya cuenta con aprobados o negados por parte de los encargados, por tal motivo no es posible continuar.",'tipo'=>"info",'titulo'=> "Oops.!","sw" => true]; 
						}

					}else{
						$resp = ['mensaje'=>"Error al cargar la información el postulante, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
					}
				
				}
			
			}
		}
		
		echo json_encode($resp);
	}

	public function gestionar_personas_apr_cate(){
		if ($this->Super_estado == false) {
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		} else {
			if ($this->Super_modifica== 0) {
				$resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			} else {
				$id_persona = $this->input->post('id_persona');
				$tipo = $this->input->post('tipo');
				$str = $this->verificar_campos_string(['Persona'=>$id_persona,'Tipo'=>$tipo]);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"Seleccione $campo.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					$resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Cambio Realizado.!"];
					$cod_encargado = null;
					if($tipo == 1) $cod_encargado = "Apr_Csep_Cat";
					$mod = $this->talento_humano_model->modificar_datos(['cod_encargado' => $cod_encargado], "personas",$id_persona);
					if ($mod != 0)	$resp = ['mensaje'=>"Error al realizar las acción, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
				}
			}
			echo json_encode($resp);
		}
	}

	public function guardar_solicitud_seleccion(){
		if (!$this->Super_estado) $resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
		else {
			if ($this->Super_agrega == 0) $resp= ['mensaje'=>'No tiene permisos para realizar esta operación.','tipo'=>'error','titulo'=> 'Oops.!'];
			else {
				$nombre = $this->input->post('nombre_vacante');
				$numero = $this->input->post('numero_vacantes');
				$tipo = $this->input->post('tipo_cargo');
				$departamento = $this->input->post('departamento');
				// $tipo_seleccion
				// 1 -> Solicitud de Pregrado
				// 0 -> Solicitud de Posgrado
				// $tipo_seleccion = $this->input->post('tipo_seleccion');
				$perfil = $this->input->post('perfil');
				$accion = (bool)$this->input->post('accion');
				$cargo = $this->input->post('cargo');
				$requisicion = $this->input->post('requisicion');
				$id_responsable = $this->input->post('id');
				$jefe_inmediato = $this->input->post('jefe_inmediato');
				$num_fields = [
					'Número de Vacantes' => $numero, 
					'Persona' => $id_responsable,
					'Jefe Inmediato' => $jefe_inmediato,
					'Cargo' => $cargo,
					// 'Tipo de Solicitud de Selección' => $tipo_seleccion,
				];
				$str_fields = [
					'Nombre de Vacante' => $nombre,
					'Tipo de Vacante' => $tipo,
					'Perfil' => $perfil,
					'Departamento' => $departamento
				];
				
				$num = $this->verificar_campos_numericos($num_fields);
				$str = $this->verificar_campos_string($str_fields);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>'info','titulo'=> 'Oops.!']; 
				}else if (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vacio.",'tipo'=>'info','titulo'=> 'Oops.!']; 
				}else{
					// Si accion es negativo se guardará una nueva solicitud
					// En caso de ser positivo se modificará la solicitud
					if($accion == 1){
						// id_solicitud returnará un un objeto con los id de la solicitud en las tablas 'solicitudes_talento_hum' y 'seleccion'
						// Con la llave 'seleccion_id' está almacenado el id de la solicitud en la tabla 'seleccion'
						// En la segunda posicion se almancerá el id de la solicitud en la tabla 'solicitud_talento_humano'
						$id = json_decode($this->input->post('id_solicitud'), true);
						$data = [
							'nombre_vacante' => $nombre,
							'numero_vacantes' => (int)$numero,
							'tipo_cargo_id' => $tipo,
							'perfil' => $perfil,
							'cargo_id' => $cargo,
							'departamento_id' => $departamento
						];
						// Se modifican los datos en la tabla 'seleccion'
						$res = $this->talento_humano_model->modificar_datos($data, 'seleccion', $id['seleccion_id']);
						$resp = $res != 0
							? 	['mensaje' => 'Ha ocurrido un error al intenar modificar la solicitud. Por favor contactese con el administrador del sistema.', 'tipo' => 'error', 'titulo' => 'Oops.!']
							: 	['mensaje' => 'Proceso de selección modificado exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
						if($requisicion != null && $requisicion > 0) $data_responsable = ['responsable_id' => $id_responsable,'id_requisicion' => $requisicion,'jefe_inmediato' => $jefe_inmediato];
						else $data_responsable = ['responsable_id' => $id_responsable,'jefe_inmediato' => $jefe_inmediato];
						// Se modifican los datos en la tabla 'solicitudes_talento_humano'
						$this->talento_humano_model->modificar_datos($data_responsable, 'solicitudes_talento_hum', $id['id']);
					}else {
							if($requisicion != null && $requisicion > 0){
								$solicitud = [
									'id_tipo_solicitud' => 'Hum_Sele',
									'usuario_registro' => $_SESSION['persona'],
									'responsable_id' => $id_responsable,
									'jefe_inmediato' => $jefe_inmediato,
									'id_requisicion' => $requisicion,
								];
							}else{
								$solicitud = [
									'id_tipo_solicitud' => 'Hum_Sele',
									'usuario_registro' => $_SESSION['persona'],
									'responsable_id' => $id_responsable,
									'jefe_inmediato' => $jefe_inmediato,
								];
							}							
							$solicitud_id = $this->talento_humano_model->guardar_datos($solicitud, 'solicitudes_talento_hum');
							if ($solicitud_id) {
								$seleccion = [
									'solicitud_id' => $solicitud_id,
									'nombre_vacante' => $nombre,
									'numero_vacantes' => (int)$numero,
									'tipo_cargo_id' => $tipo,
									'cargo_id' => (int)$cargo,
									'perfil' => $perfil,
									'departamento_id' => $departamento,
								];
								
								$res = $this->talento_humano_model->guardar_datos($seleccion, 'seleccion');
								
								$this->talento_humano_model->guardar_datos([
									'solicitud_id' => $solicitud_id, 
									'estado_id' => 'Tal_Env',
									'usuario_id' => $_SESSION['persona'],
								], 'estados_solicitudes_talento');
								$resp = !$res ? [
									'mensaje' => 'Ha ocurrido un error al intenar guardar la solicitud',
									'tipo' => 'error',
									'titulo' => 'Oops.!'
								] : [
									'mensaje' => 'Proceso de selección creado exitosamente!',
									'tipo' => 'success',
									'titulo' => 'Proceso Exitoso!'
								];
							} else $resp = [
								'mensaje' => 'Ha ocurrido un error al intenar guardar la solicitud',
								'tipo' => 'error',
								'titulo' => 'Oops.!'
							];
						
					}
				}
			}
		}
		echo json_encode($resp);
	}
	public function guardar_solicitud_ausentismo_vacaciones(){
		if (!$this->Super_estado) $resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
		else {
			if ($this->Super_agrega == 0) $resp= ['mensaje'=>'No tiene permisos para realizar esta operación.','tipo'=>'error','titulo'=> 'Oops.!'];
			else {
				$fecha_inicio = $this->input->post('fecha_inicio');
				$fecha_terminacion = $this->input->post('fecha_terminacion');
				$observaciones_ausentismo = $this->input->post('observaciones_ausentismo');
				$jefe_inmediato = $this->input->post('id_jefe_directo');
				$id_tipo_ausentismo = $this->input->post('id_tipo_ausentismo');
				$str = $this->verificar_campos_string(['jefe directo' => $jefe_inmediato,'Fecha inicio' => $fecha_inicio,'fecha  de terminacion' => $fecha_terminacion,
				]);
				 if (is_array($str)) {
					 $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
				 }else{                  
						 $data_solicitud = [
 
							 'id_tipo_solicitud' => $id_tipo_ausentismo,
							 'usuario_registro' => $id_persona,	
						 ];
						 $solicitud = $this->talento_humano_model->guardar_datos($data_solicitud,'solicitudes_talento_hum');
						 $resp = ['mensaje'=>"La solicitud fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
						 if(!$solicitud){
							 $resp = ['mensaje'=>"Error al crear la solicitud de vacaciones, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
						 }else{
							 $data_estado = ["solicitud_id" => $solicitud,"estado_id" => "Tal_Env","usuario_id" => $_SESSION['persona']];
							 $estado_sol = $this->talento_humano_model->guardar_datos($data_estado, 'estados_solicitudes_talento');
							 $data = [
								 'fecha_inicio' => $fecha_inicio,
								 'fecha_terminacion' => $fecha_terminacion,
								 'observaciones' => $observaciones,
								 'id_solicitud' => $solicitud,
								 'id_tipo_ausentismo' =>$id_tipo_ausentismo,
								 'id_jefe_directo' =>$jefe_directo,
	
							 ];
							 $data_cob = $this->talento_humano_model->guardar_datos($data, 'solicitudes_ausentismo_vacaciones');
							 if(!$data_cob){
								 $resp = ['mensaje'=>"Error al guardar detalle de la solicitud, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
							 }
						 }
				 }
		 }
 
		 
		}
		echo json_encode($resp);
	}

	public function guardar_solicitud_ausentismo_licencia(){
	if (!$this->Super_estado) $resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
	else {
		if ($this->Super_agrega == 0) $resp= ['mensaje'=>'No tiene permisos para realizar esta operación.','tipo'=>'error','titulo'=> 'Oops.!'];
		else {
			$fecha_inicio = $this->input->post('fecha_inicio');
			$fecha_terminacion = $this->input->post('fecha_terminacion');
			$observaciones = $this->input->post('observaciones');
			$jefe_directo = $this->input->post('id_jefe_directo');
			$id_tipo_ausentismo = $this->input->post('id_tipo_ausentismo');
			$str = $this->verificar_campos_string(['jefe directo' => $jefe_directo,'Fecha inicio' => $fecha_inicio,'fecha  de terminacion' => $fecha_terminacion,
			]);
			 if (is_array($str)) {
				 $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
			 }else{                  
					 $data_solicitud = [

						 'id_tipo_solicitud' => $id_tipo_ausentismo,
						 'usuario_registro' => $id_persona,	
					 ];
					 $solicitud = $this->talento_humano_model->guardar_datos($data_solicitud,'solicitudes_talento_hum');
					 $resp = ['mensaje'=>"La solicitud fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
					 if(!$solicitud){
						 $resp = ['mensaje'=>"Error al crear la solicitud de vacaciones, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
					 }else{
						 $data_estado = ["solicitud_id" => $solicitud,"estado_id" => "Tal_Env","usuario_id" => $_SESSION['persona']];
						 $estado_sol = $this->talento_humano_model->guardar_datos($data_estado, 'estados_solicitudes_talento');
						 $data = [
							 'fecha_inicio' => $fecha_inicio,
							 'fecha_terminacion' => $fecha_terminacion,
							 'observaciones_licencia' => $observaciones_licencia,
							 'id_solicitud' => $solicitud,
							 'id_tipo_ausentismo' =>$id_tipo_ausentismo,
							 'id_jefe_directo' =>$jefe_directo,

						 ];
						 $data_cob = $this->talento_humano_model->guardar_datos($data, 'solicitudes_ausentismo_licencia');
						 if(!$data_cob){
							 $resp = ['mensaje'=>"Error al guardar detalle de la solicitud, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
						 }
					 }
			 }
	 }

	 
	}
	echo json_encode($resp);
}

	public function agregar_candidato(){
		if (!$this->Super_estado) $resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
		else {
			if ($this->Super_agrega == 0) $resp = ['mensaje'=>'No tiene permisos para realizar esta operación.','tipo'=>'error','titulo'=> 'Oops.!'];
			else {
				$candidatos = 1;
				$id = $this->input->post('candidato');
				$solicitud = $this->input->post('solicitud');
				$estado_solicitud = $this->input->post('id_estado_solicitud');
				$res = $this->talento_humano_model->candidato_asignado($solicitud, $id);
				if($res) $resp = [
					'mensaje' => 'El Candidato ya está asignado a este proceso de selección.',
					'tipo' => 'info',
					'titulo' => 'Proceso Exitoso!'
				];
				else{
					$reload = false;
					$num = $this->verificar_campos_numericos(['Candidato' => $id,'Solicitud' => $solicitud,]);
					if (is_array($num)) {
						$campo = $num['field'];
						$resp = [
							'mensaje' => "Por favor seleccione $campo.",
							'tipo' => 'info',
							'titulo' => 'Oops.!'
						]; 
					}else{
						$data = [
							'solicitud_id' => $solicitud,
							'candidato_id' => $id,
							'usuario_registra' => $_SESSION['persona'],
						];
						if($estado_solicitud === 'Tal_Env') {
							$candidatos = $this->talento_humano_model->get_cantidad_candidatos($solicitud);
							if(!$candidatos) {
								$this->talento_humano_model->modificar_datos(['id_estado_solicitud' => 'Tal_Pro'], 'solicitudes_talento_hum', $solicitud);
								$data_c = [
									"solicitud_id" => $solicitud,
									"estado_id" => 'Tal_Pro',
									"usuario_id" => $_SESSION['persona']
								];
								$this->talento_humano_model->guardar_datos($data_c, 'estados_solicitudes_talento');
							}
						}
						$res = $this->talento_humano_model->guardar_datos($data, 'candidatos_seleccion');
						if($res){
							$seleccion_candidato = $this->talento_humano_model->get_where('candidatos_seleccion', $data)->row();
							$data_proceso = [
								'candidato_seleccion_id' => $seleccion_candidato->{'id'},
								'proceso_id' => 'Sel_Reg',
								'usuario_registra' => $_SESSION['persona']
							];
							$this->talento_humano_model->guardar_datos($data_proceso, 'procesos_candidatos');
							$resp = [
								'mensaje' => 'El Candidato ha sido agregado exitosamente al proceso de selección.',
								'tipo' =>'success',
								'titulo' => 'Proceso Exitoso!', 
								'sw' => !$candidatos ? 1 : 0,
								'candidato' => $seleccion_candidato,
							];
						}else{
							$resp = [
								'mensaje' => 'Ha ocurrido un error al intentar agregar al candidato al proceso de selección.',
								'tipo' => 'error',
								'titulo' => 'Oops.!'
							];}
						}
				}
			}
		}
		echo json_encode($resp);
	}

	public function listar_candidatos(){
		$candidatos = [];
		if (!$this->Super_estado) $candidatos = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
		else {
			$id = $this->input->post('id');
			$tipo = $this->input->post('tipo');
			$data = $this->talento_humano_model->listar_candidatos($id);
			$responsable_proceso = $this->talento_humano_model->esresponsable_proceso($id);
			$btn_retirar = '<span class="btn btn-default retirar" title="Retirar Candidato"><span class="fa fa-user-times" style="color:#d9534f"></span></span>';
			$btn_inhabil = '<span class="btn" title="Sin Acción"><span class="fa fa-toggle-off" style="color:#000000"></span></span>';
			$btn_aprobar = '<span class="btn btn-default contratar" title="Aprobar Candidato"><span class="fa fa-check-square-o" style="color:#5cb85c"></span></span>';
			$btn_negar	 = '<span class="btn btn-default negar" title="Rechazar Candidato"><span class="fa fa-ban" style="color:#d9534f"></span></span>';
			$btn_procesos = '<span title="Procesos de Selección" data-toggle="popover" data-trigger="hover" style="color: #337ab7" class="btn btn-default fa fa-tasks procesos"></span>';
			$aux = false;
			$info = $this->talento_humano_model->solicitud_cerrada($id);
			$solicitud_cerrada = $info['cerrada'];
			$estado_solicitud = $info['estado'];
			if ($this->estados) {
				foreach ($this->estados as $estado) {
					if ($estado['actividad'] == 'Hum_Sele' && ($estado['estado'] == 'Tal_Pro' || $estado['estado'] == 'Tal_Env')) $aux = true;
				}
			}
			$esResponsable = $this->talento_humano_model->esResponsable($id);
			if($aux || $this->admin || $this->admin_th){
				if($estado_solicitud != 'Tal_Ter'){
					foreach ($data as $candidato) {
						switch ($candidato['proceso_actual_id']) {
							case 'Sel_Des':
								$candidato['gestion'] = $btn_inhabil;
								break;
							default:
								if ($solicitud_cerrada) {
									$candidato['gestion'] = (int)$candidato['contratado']
										? "$btn_procesos $btn_retirar"
										: $candidato['gestion'] = $btn_inhabil;
								} else {
									$gestion = '';
									// if($esResponsable && in_array('Sel_Inf', $candidato['procesos']) && in_array('Sel_Seg', $candidato['procesos']) && !$candidato['aprobacion_jefe']) $gestion = $btn_aprobar;
									if($esResponsable && !$candidato['aprobacion_jefe'] && $candidato['solicitar_vb_jefe']) $gestion = $btn_aprobar.' '.$btn_negar;
									// if($esResponsable && in_array('Sel_Inf', $candidato['procesos']) && in_array('Sel_Seg', $candidato['procesos']) && !$candidato['aprobacion_jefe'] && $tipo != 'Vac_Apr') $gestion = $btn_aprobar;
									$gestion .= " $btn_procesos $btn_retirar";
									$candidato['gestion'] = $gestion;
								}
								break;
						}
						$candidatos[] = $candidato;
					}
				} else {
					foreach ($data as $candidato) {
						$candidato['gestion'] = $btn_inhabil;
						$candidatos[] = $candidato;
					}
				}
			}else{
				foreach ($data as $candidato) {
					$p = $candidato['procesos'];
					// $candidato['gestion'] = in_array('Sel_Inf', $p) && in_array('Sel_Seg', $p) && !$candidato['aprobacion_jefe']
					// 	? $btn_aprobar : $btn_inhabil;
					$candidato['gestion'] = ($esResponsable && !$candidato['aprobacion_jefe']  && $candidato['solicitar_vb_jefe']) ? $btn_aprobar.' '.$btn_negar : $btn_inhabil;
					$candidatos[] = $candidato;
				}
			}
		}
		echo json_encode([$candidatos, $solicitud_cerrada, $responsable_proceso]);
	}

	public function retirar_candidato(){
		if (!$this->Super_estado) $resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
		else {
			if (!$this->Super_elimina) $resp = ['mensaje'=>'No tiene permisos para realizar esta operación.','tipo'=>'error','titulo'=> 'Oops.!'];
			else {
				$candidato = $this->input->post('candidato');
				$solicitud = $this->input->post('solicitud');
				$num = $this->verificar_campos_numericos([
					'Candidato' => $candidato,
					'Solicitud' => $solicitud,
				]);
				if (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje' => "Por favor seleccione $campo.", 'tipo' => 'info', 'titulo' => 'Oops.!']; 
				}else{
					$candidato_asignado = $this->talento_humano_model->candidato_asignado($solicitud, $candidato);
					if ($candidato_asignado) {
						$data = ['estado' => -1];
						$res = $this->talento_humano_model->modificar_datos($data, 'candidatos_seleccion', $candidato_asignado);
						$resp = !$res
							? ['mensaje' => 'El candidato ha sido retirado de la solicitud exitosamente.', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!']
							: ['mensaje' => 'Ha ocurrido un error al intentar retirar al candidato.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
					} else $resp = ['mensaje' => 'El candidato no está asignado a esta solicitud.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
				}
			}
		}
		echo json_encode($resp);
	}

	public function get_cargos_departamento(){
		$cargos = [];
		if (!$this->Super_estado) $cargos = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
		else {
			$id = $this->input->post('id');
			$cargos = $this->talento_humano_model->get_cargos_departamento($id);
		}
		echo json_encode($cargos);
	}

	public function cargar_ubicaciones(){
		$ubicaciones = [];
		if (!$this->Super_estado) $ubicaciones = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
		else {
			$lugar = $this->input->post('lugar');
			$ubicaciones = $this->talento_humano_model->cargar_ubicaciones($lugar);
		}
		echo json_encode($ubicaciones);
	}

	public function gestionar_candidato(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
				$pruebas_seleccionadas = null;
				$candidato = $this->input->post('candidato');
				$solicitud = $this->input->post('solicitud');
				$nextProcess = $this->input->post('nextProcess');
				$success = $this->input->post('success');
				$observaciones = $this->input->post('observaciones');
				$duracion_contrato = NULL;
				$msj = $this->input->post('msj');
				$num_fields = [
					'Candidato' => $candidato,
					'Solicitud' => $solicitud,
				];
				$str_fields = ['Estado' => $nextProcess];
				if($nextProcess === 'Sel_Con'){
					$fecha_contratacion = $this->input->post('fecha_contratacion');
					$tipo_contrato = $this->input->post('tipo_contrato');
					// $reemplazado = $this->input->post('reemplazado');
					// $salario = $this->input->post('salario');
					$str_fields['Tipo de Contrato'] = $tipo_contrato;
					// $num_fields['Salario'] = $salario;
					if($tipo_contrato != 'Cont_Ind') {
						$duracion_contrato = $this->input->post('duracion_contrato');
						$num_fields['Duración del Contrato'] = $duracion_contrato;
					}
					// if($reemplazado) $num_fields['Reemplazado'] = $reemplazado;
					if(!$this->validateDate($fecha_contratacion,'Y-m-d')) {
						echo json_encode(['mensaje'=>"El fecha de citación no es valida.",'tipo'=>"info",'titulo'=> "Oops.!"]);
						return;
					}
				}
				if($nextProcess === 'Sol_Sel_Con'){
					$reemplazado = $this->input->post('reemplazado');
					$num_fields['Reemplazado'] = $reemplazado;
				}
				$num = $this->verificar_campos_numericos($num_fields);
				$str = $this->verificar_campos_string($str_fields);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>'info','titulo'=> 'Oops.!']; 
				}elseif (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vacio.",'tipo'=>'info','titulo'=> 'Oops.!']; 
				}else{
					$info = $this->talento_humano_model->get_full_info_candidato($solicitud, $candidato);
					// $aux = $this->validar_estado_candidato($solicitud, $candidato, $nextProcess);
					// if ($aux[0] == 1) {
						$candidato_id = $info->{'candidato_seleccion_id'}; 
						$data = ['proceso_actual_id' => $nextProcess];
						if($nextProcess === 'Sol_Sel_Con'){
							$data['reemplazado'] = $reemplazado;
						}else if($nextProcess === 'Sel_Con') {
							$data['contratado'] = 1;
							// $data['salario'] = $salario;
							$data['tipo_contrato_id'] = $tipo_contrato;
							$data['fecha_ingreso'] = $fecha_contratacion;
							$data['duracion_contrato'] = $duracion_contrato;
						} else if($nextProcess == 'Sel_Des') $data['contratado'] = 0;
						if($msj) $data['observacion'] = $msj;
						if($observaciones) $data['observacion_contratacion'] = $observaciones;
						if($nextProcess === 'Sel_Sol_VB') $data['solicitar_vb_jefe'] = 1;
						$res = $this->talento_humano_model->modificar_datos($data, 'candidatos_seleccion', $candidato_id);
						if(!$res){
							// Enviar Pruebas
							$pruebas = $this->input->post('pruebas');
							if(is_array($pruebas) && $nextProcess === 'Sel_Psi' && sizeof($pruebas) > 0){
								$pruebas_seleccionadas = $this->talento_humano_model->get_pruebas_seleccionadas($pruebas);
							}
							if($nextProcess === 'Sel_Con') {
								$cerrada = $this->talento_humano_model->solicitud_cerrada($solicitud);
								if($cerrada) $this->talento_humano_model->modificar_datos(['cerrada' => 1], 'seleccion', $solicitud, 'solicitud_id');
							}else if($nextProcess === 'Sel_CPre'){
								$log_data = [
									'candidato_seleccion_id' => $candidato_id, 
									'proceso_id' => 'Sel_Cse', 
									'usuario_registra' => $_SESSION['persona']
								];
								$this->talento_humano_model->guardar_datos($log_data, 'procesos_candidatos');
							}
							$resp = [
								'mensaje' => 'El candidato ha sido gestionado exitosamente.', 
								'tipo' => 'success', 
								'titulo' => 'Proceso Exitoso!', 
								'pruebas_seleccionadas' => $pruebas_seleccionadas ? $pruebas_seleccionadas : null,
							];
							$log_data = [
								'candidato_seleccion_id' => $candidato_id, 
								'proceso_id' => $nextProcess, 
								'usuario_registra' => $_SESSION['persona']
							];
							$this->talento_humano_model->guardar_datos($log_data, 'procesos_candidatos');
						} else $resp = ['mensaje' => 'Ha ocurrido un error al intentar gestionar al candidato.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
					// } else if($aux[0] === -1) $resp = ['mensaje' => 'Hay procesos pendientes antes de este o no tiene permisos para esta acción.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
					// 	else if($aux[0] === -2) $resp = ['mensaje' => 'El Candidato ya fue gestionada anteriormente.', 'tipo' => 'info', 'titulo' => 'Ooops!'];
					// 	else $resp = ['mensaje' => 'Ha ocurrido un error al intentar gestionar el candidato.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
				}
			} else $resp = ['mensaje' => 'No tiene permisos para gestionar esta solicitud.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
			echo json_encode($resp);
		}
	}

	public function validar_estado_candidato($solicitud, $candidato, $nextProcess){
		$info = $this->talento_humano_model->get_full_info_candidato($solicitud, $candidato);
		$tipo_cargo = $info->{'tipo_cargo_id'};
		$procesos = $this->talento_humano_model->get_historial_procesos($solicitud, $candidato);
		if ($info){
			if($nextProcess === 'Sel_Con'){
				if($tipo_cargo === 'Vac_Aca'){
					// return (in_array('Sel_Seg', $procesos) && in_array('Sel_Med', $procesos) && in_array('Sel_Inf', $procesos) && in_array('Sel_Cse', $procesos))
					// 	? [1, $info] // Cambio de proceso permitido
					// 	: [-2, null];// Cambio de proceso no permitido
					return (in_array('Sel_Seg', $procesos) && in_array('Sel_Med', $procesos) && in_array('Sel_Inf', $procesos))
						? [1, $info] // Cambio de proceso permitido
						: [-1, null];// Cambio de proceso no permitido
				} else{
					return (in_array('Sel_Seg', $procesos) && in_array('Sel_Med', $procesos) && in_array('Sel_Inf', $procesos))
						? [1, $info] // Cambio de proceso permitido
						: [-2, null];// Cambio de proceso no permitido
				}
			}
			return [1, $info];	// Cambio de proceso permitido
		} else return [-1, null]; // Usuario no existente
	}

	public function get_pruebas_asignadas(){
		$pruebas = [];
		if (!$this->Super_estado) $pruebas = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
				$pruebas = $this->talento_humano_model->get_pruebas_asignadas();
			}
		}
		echo json_encode($pruebas);
	}

	public function get_info_seleccion(){
		$id = $this->input->post('id');
		$candidato_id = $this->input->post('candidato_id');
		$info_seleccion = $this->talento_humano_model->get_info_seleccion($id, $candidato_id);
		echo json_encode($info_seleccion);
	}

	public function get_info_entrevista(){
		$id = $this->input->post('id');
		$candidato_id = $this->input->post('candidato_id');
		$info_seleccion['data'] = $this->talento_humano_model->get_info_seleccion($id, $candidato_id);
		$info_seleccion['correo_responsable'] = $this->talento_humano_model->get_where('valor_parametro', ['id_aux' => 'Par_TH', 'idparametro' => 20])->row()->valor;
		echo json_encode($info_seleccion);
	}

	public function get_info_entrevista_jefe(){
		$id = $this->input->post('id');
		$candidato_id = $this->input->post('candidato_id');
		$info_seleccion['data'] = $this->talento_humano_model->get_info_entrevista_jefe($id, $candidato_id);
		$info_seleccion['correo_responsable'] = $this->talento_humano_model->get_where('valor_parametro', ['id_aux' => 'Par_TH', 'idparametro' => 20])->row()->valor;
		echo json_encode($info_seleccion);
	}

	public function generar_informe(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
				$solicitud = $this->input->post('solicitud');
				$candidato = $this->input->post('candidato');
				$data = [
					'solicitud_id' => $solicitud,
					'candidato_id' => $candidato,
				];
				$categoria = $this->input->post('categoria_colciencias');
				$indiceh = $this->input->post('indiceh');
				$formacion = $this->input->post('formacion');
				$exp_docente = $this->input->post('exp_docente');
				$exp_investigacion = $this->input->post('exp_investigacion');
				$exp_profesional = $this->input->post('exp_profesional');
				$produccion = $this->input->post('produccion');
				$pruebas = $this->input->post('pruebas');
				$cvlac = $this->input->post('cvlac');
				$estudios = !empty($this->input->post('estudios')) ? json_decode($this->input->post('estudios'), true) : [];
				$competencias = !empty($this->input->post('competencias')) ? json_decode($this->input->post('competencias'), true) : [];
				$suficiencia_ingles = $this->input->post('suficiencia_ingles');
				$concepto = $this->input->post('concepto');
				$accion = $this->input->post('accion');
				$str_fields = [
					'Categoría Colciencias' => $categoria,
					'Indice H' => $indiceh,
					'Experiencia en Docencia' => $exp_docente,
					'Experiencia en Investigación' => $exp_investigacion,
					'Experiencia Profesional' => $exp_profesional,
					'Producción' => $produccion,
					'Suficiencia Inglés' => $suficiencia_ingles,
					'Concepto' => $concepto,
					'Formacion' => $formacion,
					'Pruebas' => $pruebas,
					'CVLAC' => $cvlac,
				];
				$num_fields = [
					'Candidato' => $candidato,
					'Solicitud' => $solicitud,
				];
				$num = $this->verificar_campos_numericos($num_fields);
				$str = $this->verificar_campos_string($str_fields);
				if (is_array($str)) {
					$campo = $str['field'];
					$resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>'info','titulo'=> 'Oops.!']; 
				}elseif (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vacio.",'tipo'=>'info','titulo'=> 'Oops.!']; 
				} else {
					$data = ['candidato_id' => $candidato, 'solicitud_id' => $solicitud];
					$id = $this->talento_humano_model->get_where('candidatos_seleccion', $data)->row()->id;
					$data_mod = [
						'categoria_colciencias' => $categoria,
						'indiceh' => $indiceh,
						'formacion' => $formacion,
						'exp_docente' => $exp_docente,
						'exp_investigacion' => $exp_investigacion,
						'exp_profesional' => $exp_profesional,
						'produccion' => $produccion,
						'suficiencia_ingles' => $suficiencia_ingles,
						'concepto' => $concepto,
						'cvlac' => $cvlac,
						'pruebas' => $pruebas,
					];
					$res = $this->talento_humano_model->modificar_datos($data_mod, 'candidatos_seleccion', $id);
					if(!$res){
						if($accion) $this->talento_humano_model->modificar_datos(['estado' => -1], 'estudios_candidatos', $id, 'candidato_seleccion_id');
						if(count($estudios) > 0){
							$data_estudios = [];
							foreach ($estudios as $estudio) {
									$estudio['candidato_seleccion_id'] = $id;
									$estudio['usuario_registra'] = $_SESSION['persona'];
									$data_estudios[] = $estudio;
							}
							$this->talento_humano_model->guardar_datos($data_estudios, 'estudios_candidatos', 2);
						}
						
						if($accion) $this->talento_humano_model->modificar_datos_2(['estado' => -1], 'competencias_talento_cuc',"id_persona = $candidato and id_solicitud_th = $solicitud");						
						if(count($competencias) > 0){
							$data_competencias = [];
							foreach ($competencias as $competencia) {
									unset($competencia['nombre']);
									$competencia['id_persona'] = $candidato;
									$competencia['id_solicitud_th'] = $solicitud;
									$competencia['usuario_registra'] = $_SESSION['persona'];
									$data_competencias[] = $competencia;
							}
							$this->talento_humano_model->guardar_datos($data_competencias, 'competencias_talento_cuc', 2);
						}
						$resp = ['mensaje' => "Informe Guardado Exitosamente", 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
					}else $resp = ['mensaje'=>"Ha ocurrido un error al intentar crear el informe",'tipo'=>'error','titulo'=> 'Oops.!'];
				}
			}
		}

		echo json_encode($resp);
	}

	public function get_full_info_candidato(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
				$solicitud = $this->input->post('id');
				$candidato = $this->input->post('candidato');
				$num = $this->verificar_campos_numericos(['Solicitud' => $solicitud, 'Candidato' => $candidato]);
				if (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje' => "Por favor seleccione $campo.",'tipo' => 'info','titulo' => 'Oops.!'];
				}else{
					$resp = $this->talento_humano_model->get_full_info_candidato($solicitud, $candidato);
					if(!$resp)$resp = 'Ha ocurrido un error.';
				}
			}
		}
		echo json_encode($resp);
	}

	public function adjuntar_aval(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
				$form = $this->input->post();
				$solicitud = $form['solicitud'];
				$candidato = $form['candidato'];
				$tipo = $form['tipo'];
				$aprobacion = '';
				if($tipo === 'Sel_Seg') $aprobacion = $form['vb_seguridad'];
				else if ($tipo === 'Sel_Med') $aprobacion = $form['vb_medico'];
				else{
					echo json_encode(['mensaje' => 'Tipo de aval no reconocido', 'tipo' => 'error', 'info' => 'Error']);
					return;
				}
				$num_fields = [
					'Candidato' => $candidato,
					'Solicitud' => $solicitud,
					'Aprobación' => $aprobacion,
				];
				$num = $this->verificar_campos_numericos($num_fields);
				if (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje' => "El campo $campo no puede estar vacio.", 'tipo' => 'info', 'titulo' => 'Oops.!']; 
				} else {
					$file = $this->cargar_archivo("aval", $this->ruta_adjuntos, 'seg');
					if ($file[0] == -1){
						$error = $file[1];
						if ($error == "<p>You did not select a file to upload.</p>") {
							$resp = ['mensaje'=>"Debe adjuntar el Aval de Seguridad.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
							$sw = false;
						}else{
							$resp = ['mensaje'=>"Error al cargar el Aval de Seguridad.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
							$sw = false;
						} 
					}else {
						// Traer el id del candidato dentro de la solicitud de seleccion
						$seleccion_candidato = $this->talento_humano_model->get_where('candidatos_seleccion', ['solicitud_id' => $solicitud, 'candidato_id' => $candidato])->row();
						$aval = $file[1];
						$data = [
							'id_solicitud' => $solicitud,
							'nombre_real' => $tipo === 'Sel_Seg' ? 'Aval de Seguridad' : 'Aval Médico',
							'nombre_archivo' => $aval,
							'tipo' => 'persona',
							'id_persona' => $seleccion_candidato->{'id'},
							'usuario_registra' => $_SESSION['persona'],
						];
						$res = $this->talento_humano_model->guardar_datos($data, 'archivos_adj_th');
						if($res){
							// Actualiza el proceso del candidato al Aval en proceso
							$data_cs = [];
							if($aprobacion == 1) $proceso = $tipo;
							else{
								$proceso = 'Sel_Des';
								$data_cs['observacion'] = $tipo === 'Sel_Med' ? 'Descartado por aval médico' : 'Descartado por aval de Seguridad';
							}
							$data_cs['proceso_actual_id'] = $proceso;
							$this->talento_humano_model->modificar_datos($data_cs, 'candidatos_seleccion', $seleccion_candidato->{'id'});
							$data_proceso = [
								'candidato_seleccion_id' => $seleccion_candidato->{'id'},
								'proceso_id' => $tipo,
								'usuario_registra' => $_SESSION['persona'],
							];
							$this->talento_humano_model->guardar_datos($data_proceso, 'procesos_candidatos');
							$resp = ['mensaje' => 'Aval adjuntado Exitosamente', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
						} else {
							$resp = ['mensaje' => 'Ha ocurrido un error al intentar adjuntar el aval', 'tipo' => 'info', 'titulo' => 'Ooops!'];
						}
					}
				}
			}
		}
		echo json_encode($resp);
	}

	public function adjuntar_certificado(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id = $this->input->post('id');
			$file = $this->cargar_archivo("file_certificado", $this->ruta_certificados, 'cert');
			// Si es un certificado de ingresos y retención y tiene contrato por prestación de servicios
			$ops = $this->input->post('ops');
			$observaciones = $this->input->post('observaciones');
			$tipo_certificado = $this->input->post('tipo_certificado');
			$resp = ['mensaje' => "Certificado adjunto exitosamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
				if ($file[0] == -1 && $tipo_certificado != 'Hum_Cir'){
					$error = $file[1];
					$resp = $error == "<p>You did not select a file to upload.</p>"
						? ['mensaje'=>"Por favor seleccione un archivo para adjuntar.",'tipo'=>"info",'titulo'=> "Oops.!"]
						: ['mensaje'=>"Error al cargar el Aval de Seguridad.",'tipo'=>"error",'titulo'=> "Oops.!"];
				} else {
					$nombre_archivo = $this->input->post('certificado');
					$archivo = $file[1] == "<p>You did not select a file to upload.</p>" ? null : $file[1];
					$data = [
						'certificado' => $archivo,
						'nombre_archivo' => $nombre_archivo,
						'usuario_adjunta' => $_SESSION['persona'],
						'fecha_adjunto' => date("Y-m-d H:i")
					];
					$mod = $this->talento_humano_model->modificar_datos($data, 'certificados', $id, 'solicitud_id');
					if(!$mod) {
						$data = ['id_estado_solicitud' => 'Tal_Ter'];
						if($tipo_certificado === 'Hum_Cir' && $ops) {
							if($observaciones) {
								$data['observacion'] = $observaciones;
							}
						}
						$mod = $this->talento_humano_model->modificar_datos($data, 'solicitudes_talento_hum', $id);
						$resp = !$mod
							? [
								'mensaje' => "Certificado adjuntado exitosamente",
								'tipo' => "success",
								'titulo' => "Proceso Exitoso!",
								'certificado' => $archivo
							]
							: [
								'mensaje' => "Ha ocurrido un error al intentar cambiar el estado de la solicitud",
								'tipo' => "error",
								'titulo' => "Ooops!"
							];
					} else $resp = [
						'mensaje' => "Ha ocurrido un error al intentar adjuntar el certificado",
						'tipo' => "error",
						'titulo' => "Ooops!"
					];
				}
		}
		echo json_encode($resp);
	}

	public function cargar_procesos_disponibles(){
		$procesos = [];
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
				$solicitud = $this->input->post('solicitud');
				$candidato = $this->input->post('candidato');
				// Tipo seleccion = 1 -> Selección de Pregrado
				// Tipo seleccion = 2 -> Selección de Posgrado
				// $tipo_seleccion = $this->input->post('tipo_seleccion');
				$tipo_seleccion = 0;
				$num = $this->verificar_campos_numericos(['Solicitud' => $solicitud, 'Candidato' => $candidato]);
				if (is_array($num)) {
					$campo = $num['field'];
					$resp = ['mensaje' => "Por favor seleccione $campo.",'tipo' => 'info','titulo' => 'Oops.!'];
				}else {
					$procesos = $this->talento_humano_model->cargar_procesos_disponibles($solicitud, $candidato);
					$full_info = $this->talento_humano_model->get_full_info_candidato($solicitud, $candidato);
					$aprobacion_jefe = $full_info->aprobacion_jefe;
					$tipo_cargo = $full_info->tipo_cargo_id;
					$avales = [];
					$csep = 0;
					$contratado = 0;
					$p_seleccion = '';
					$p_contratacion = '';
					$show_seleccion = 0;
					$show_contratacion = 0;
					$sw_proceso = false;
					if($tipo_cargo === 'Vac_Aca') $tipo_seleccion = 1;
					else if($tipo_cargo === 'Vac_Pos') $tipo_seleccion = 2;
					foreach ($procesos as $proceso) {
						$tipo = (int)$proceso['tipo'];
						if (($proceso['id'] === 'Sel_Seg' && $proceso['ok']) || ($proceso['id'] === 'Sel_Inf' && $proceso['ok']) || ($proceso['id'] === 'Sel_Med' && $proceso['ok'])) array_push($avales, $proceso['id']);
						// if(($proceso['id'] === 'Sel_Exo') && $proceso['ok']) $contratado = 1;
						if(($proceso['id'] === 'Sel_Con' || $proceso['id'] === 'Sel_Exo') && $proceso['ok']) $contratado = 1;
						if($tipo == 1){
							$p_seleccion .= $proceso['ok']
								? "<a class='list-group-item disabled' id=" . $proceso['id'] . ">" . $proceso['nombre'] . "</a >"
								: "<a class='list-group-item' id=" . $proceso['id'] . ">" . $proceso['nombre'] . "</a >";
							if(!$proceso['ok']) $show_seleccion = 1;
						}elseif($tipo == 2){
							$p_contratacion .= $proceso['ok']
								? "<a class='list-group-item disabled' id=" . $proceso['id'] . ">" . $proceso['nombre'] . "</a >"
								: "<a class='list-group-item' id=" . $proceso['id'] . ">" . $proceso['nombre'] . "</a >";
							if(!$proceso['ok']) $show_contratacion = 1;
							// Las solicitudes de Selección de Posgrado (0) no tienen proceso de CSEA
						}elseif($tipo === 3 && $tipo_cargo === 'Vac_Aca' && $tipo_seleccion == 1){
							if($proceso['ok'] && ($proceso['id'] === 'Sel_CVir' || $proceso['id'] === 'Sel_CPre')){
								$csep = $proceso['id'] === 'Sel_CVir' ? 1 : 2;
								$estado_postulante = ""; 
								$estado_postulante = $csep == 1 
									? $this->talento_humano_model->get_where('postulantes_csep', ['id' => $full_info->id_csep])->row()->id_estado_solicitud
									: "";
								$p_seleccion .= "<a class='list-group-item disabled' id=" . $proceso['id'] . ">" . $proceso['nombre'] . "</a >";
							}else if(!$proceso['ok'] && $proceso['id'] != 'Sel_CVir' && $proceso['id'] != 'Sel_CPre') $p_seleccion .= "<a class='list-group-item' id=" . $proceso['id'] . ">" . $proceso['nombre'] . "</a >";
						}
					}
					if(
						(
							($tipo_cargo === 'Vac_Aca') 
							&& (($csep == 1 &&  $estado_postulante === "Pos_Apr") || $csep == 2) 
							&& in_array('Sel_Seg', $avales) 
							&& in_array('Sel_Med', $avales) 
							&& in_array('Sel_Inf', $avales)) 
						|| 
							(($tipo_cargo === 'Vac_Adm' || $tipo_cargo === 'Vac_Apr') 
							&& in_array('Sel_Seg', $avales) 
							&& in_array('Sel_Inf', $avales) 
							&& in_array('Sel_Med', $avales)));
							// $p_seleccion .= !$contratado ? "<a class='list-group-item' id='Sel_Con'>Visto Bueno Jefe</a>" : "<a class='list-group-item disabled' id='Sel_Con'>Visto Bueno Jefe</a>";
					// $p_seleccion .= !$contratado && ($this->admin || $this->admin_th)  ? "<a class='list-group-item' id='Sel_Con'>Visto Bueno Jefe TH</a>" : "<a class='list-group-item disabled' id='Sel_Con'>Visto Bueno Jefe Th</a>";
					$p_seleccion .= !$contratado ? "<a class='list-group-item' id='Sel_Exo'>Enviado a Contratación</a>" : "<a class='list-group-item disabled' id='Sel_Exo'>Enviado a Contratación</a>";
					$resp['seleccion'] = $p_seleccion;
					$resp['contratacion'] = $contratado ? $p_contratacion : "<h4 style='text-align:center;'>Ningun Proceso disponible</h4>";
					$resp['show_c'] = $contratado ? $contratado : 0;
					$resp['show_s'] = $show_seleccion;
				}
			}
		}
		echo json_encode($resp);
	}

	function citar_entrevista(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
				$candidato = $this->input->post('candidato');
				$solicitud = $this->input->post('solicitud');
				$ubicacion = $this->input->post('ubicacion');
				$fecha_entrevista = $this->input->post('fecha_entrevista');
				$res = $this->talento_humano_model->candidato_asignado($solicitud, $candidato);
				if(!$res) $resp = ['mensaje' => 'El Candidato no está asignado a este proceso de selección.','tipo'=>'info','titulo'=> 'Proceso Exitoso!'];
				else{
					if(!$this->validateDate($fecha_entrevista,'Y-m-d H:i')) {
						$resp = ['mensaje'=>"El fecha de citación no es valida.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
					}else{
						$num = $this->verificar_campos_numericos([
							'Candidato' => $candidato,
							'Solicitud' => $solicitud,
							'Ubicación' => $ubicacion,
						]);
						if (is_array($num)) {
							$campo = $num['field'];
							$resp = ['mensaje' => "Por favor seleccione $campo.", 'tipo' => 'info', 'titulo' => 'Oops.!']; 
						}else{
							$data = [
								'fecha_entrevista' => $fecha_entrevista,
								'ubicacion_entrevista_id' => $ubicacion,
								'proceso_actual_id' => 'Sel_Ent',
							];
							$where = ['candidato_id' => $candidato, 'solicitud_id' => $solicitud,];
							$this->db->update('candidatos_seleccion', $data, $where);
							$error = $this->db->_error_message(); 
							if($error) $resp = ['mensaje' => "Ha ocurrido un error al intentar asignar la entrevista",'error' => "info",'titulo' => "Oops.!"];
							else{
								$seleccion_candidato = $this->talento_humano_model->get_where('candidatos_seleccion', $data)->row();
								$data_proceso = [
									'candidato_seleccion_id' => $seleccion_candidato->{'id'},
									'proceso_id' => 'Sel_Ent',
									'usuario_registra' => $_SESSION['persona'],
								];
								$this->talento_humano_model->guardar_datos($data_proceso, 'procesos_candidatos');
								$resp = ['mensaje' => "Entrevista asignada exitosamente",'tipo' => "success",'titulo' => "Proceso Exitoso!"];
							}
						}
					}
				}
			}
		}
		echo json_encode($resp);
	}

	public function citar_examenes(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
				$candidato = $this->input->post('candidato');
				$solicitud = $this->input->post('solicitud');
				$fecha_examenes = $this->input->post('fecha_examenes');
				$ayuno = $this->input->post('ayuno');
				$res = $this->talento_humano_model->candidato_asignado($solicitud, $candidato);
				if(!$res) $resp = ['mensaje' => 'El Candidato no está asignado a este proceso de selección.','tipo'=>'info','titulo'=> 'Proceso Exitoso!'];
				else{
					if(!$this->validateDate($fecha_examenes,'Y-m-d H:i')) {
						$resp = ['mensaje'=>"El fecha de citación no es valida.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
					}else{
						$num = $this->verificar_campos_numericos([
							'Candidato' => $candidato,
							'Solicitud' => $solicitud,
						]);
						if (is_array($num)) {
							$campo = $num['field'];
							$resp = ['mensaje' => "Por favor seleccione $campo.", 'tipo' => 'info', 'titulo' => 'Oops.!']; 
						}else{
							if($ayuno != 1 && $ayuno != 0){
								$resp = ['mensaje' => "Por favor seleccione una opción para Ayuno", 'tipo' => 'info', 'titulo' => 'Oops.!'];
							}else{
								$data = [
									'fecha_examenes' => $fecha_examenes,
									'ayunas' => $ayuno,
									'proceso_actual_id' => 'Sel_Exa',
								];
								$where = ['candidato_id' => $candidato, 'solicitud_id' => $solicitud,];
								$this->db->update('candidatos_seleccion', $data, $where);
								$error = $this->db->_error_message(); 
								if($error) $resp = ['mensaje' => "Ha ocurrido un error al intentar asignar la entrevista",'error' => "info",'titulo' => "Oops.!"];
								else{
									$seleccion_candidato = $this->talento_humano_model->get_where('candidatos_seleccion', $where)->row();
									$data_proceso = [
										'candidato_seleccion_id' => $seleccion_candidato->{'id'},
										'proceso_id' => 'Sel_Exa',
										'usuario_registra' => $_SESSION['persona'],
									];
									$this->talento_humano_model->guardar_datos($data_proceso, 'procesos_candidatos');
									$resp = ['mensaje' => "Entrevista asignada exitosamente",'tipo' => "success",'titulo' => "Proceso Exitoso!"];
								}
							}
						}
					}
				}
			}
		}
		echo json_encode($resp);
	}

	public function validar_proceso(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$candidato = $this->input->post('candidato');
			$solicitud = $this->input->post('solicitud');
			$proceso = $this->input->post('proceso');
			$str_fields = ['Proceso' => $proceso,];
			$num_fields = ['Candidato' => $candidato, 'Solicitud' => $solicitud,];
			$num = $this->verificar_campos_numericos($num_fields);
			$str = $this->verificar_campos_string($str_fields);
			if (is_array($str)) {
				$campo = $str['field'];
				$resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>'info','titulo'=> 'Oops.!'];
			}elseif (is_array($num)) {
				$campo = $num['field'];
				$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vacio.",'tipo'=>'info','titulo'=> 'Oops.!'];
			} else {
				$ok = false;
				if(!$this->admin){
					foreach ($this->estados as $estado) {
						# Valido si el proceso que se desea ejecutar tenga el permiso requerido
						if($estado['actividad'] === 'Hum_Sele' && $estado['estado'] === $proceso) $ok = true;
					}
				}
				# Pasa si tiene permisos o es administrador 
				if($ok || $this->admin){
					$sw = true;
					$info = $this->talento_humano_model->get_full_info_candidato($solicitud, $candidato);
					// if($proceso == 'Sel_Con' && ($info->{'tipo_cargo_id'} == 'Vac_Adm' || $info->{'tipo_cargo_id'} == 'Vac_Apr')){
					// 	if(!$this->admin || !$this->admin_th){
						// 	if(!$info->{'aprobacion_jefe'}) {
						// 		$resp = 2;
						// 		$sw = false;
						// 	}
						// } else {
							// $resp = 3;
							// $sw = false;
						// }
					if($proceso == 'Sel_Con'){
						$sw = false;
						$historial = $this->talento_humano_model->get_historial_procesos($solicitud, $candidato);
						if(in_array($proceso, $historial)) $resp = 1;
						else $resp = 0;	
					}else if($proceso == 'Sel_Cse'){
						$sw = false;
						$resp = $this->talento_humano_model->validar_csep($info->{'candidato_seleccion_id'});
					} else if($proceso === 'Sel_Ind'){
						$historial = $this->talento_humano_model->get_historial_procesos($solicitud, $candidato);
						$rData = ['idparametro' => 19, 'valory' => 2,];
						$procesos = $this->talento_humano_model->get_where('valor_parametro', $rData)->result_array();
						$process = [];
						foreach ($procesos as $row) array_push($process, $row['id_aux']);
						$index = array_search('Sel_Ind', $process);
						unset($process[$index]);
						$req = false;
						foreach ($process as $p) if(!in_array($p, $historial)) $req = true;
						if($req){
							$resp = 2;
							$sw = false;
						}
					} else if($proceso === 'Sel_Sol_Ex'){
						$sw = false;
						$historial = $this->talento_humano_model->get_historial_procesos($solicitud, $candidato);
						if(in_array($proceso, $historial)) $resp = 1;
						else $resp = 0;
					} else if($proceso === 'Sel_Exa'){
						$sw = false;
						$historial = $this->talento_humano_model->get_historial_procesos($solicitud, $candidato);
						if(in_array($proceso, $historial)) $resp = 1;
						else $resp = 0;
						// else {
							// $resp = ($info->{'tipo_cargo_id'} === 'Vac_Aca')
							// 	? ((in_array('Sel_Seg', $historial) && in_array('Sel_Cse', $historial)) ? 0 : 2)
							// 	: ((in_array('Sel_Seg', $historial) && !in_array('Sel_Exa', $historial)) ? 0 : 2);
								// : ((in_array('Sel_Seg', $historial) && $info->{'aprobacion_jefe'} && !in_array('Sel_Exa', $historial)) ? 0 : 2);
						// }
					} else if($proceso === 'Sel_Med'){
						$sw = false;
						$historial = $this->talento_humano_model->get_historial_procesos($solicitud, $candidato);
						if(in_array($proceso, $historial)) $resp = 1;
						else $resp = 0;
						// else $resp = (in_array('Sel_Inf', $historial) && in_array('Sel_Seg', $historial) && in_array('Sel_Exa', $historial)) ? 0 : 4;
					}else if($proceso === 'Sel_Exo'){
						$sw = false;
						$historial = $this->talento_humano_model->get_historial_procesos($solicitud, $candidato);
						if(in_array($proceso, $historial)) $resp = 1;
						else $resp = 0;
					}
					if($sw) {
						$res = $this->talento_humano_model->get_where('procesos_candidatos', ['candidato_seleccion_id' => $info->{'candidato_seleccion_id'}, 'proceso_id' => $proceso])->row();
						$resp =  $res ? 1 : 0;
					}
				} else $resp = 3;
			}
		}
		echo json_encode($resp);
	}
	

	public function get_historial_candidato(){
		if(!$this->Super_estado) {
			echo json_encode(['tipo' =>'sin_session']);
			return;
		}
		$candidato = $this->input->post('candidato');
		$data = ['candidato_seleccion_id' => $candidato];
		$procesos = $this->talento_humano_model->get_historial_candidato($candidato);
		echo json_encode($procesos);
	}

	public function get_procesos_candidato(){
		$procesos = [];
		if (!$this->Super_estado) $procesos = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id = $this->input->post('id');
			$data = $this->talento_humano_model->get_where('procesos_candidatos', ['candidato_seleccion_id' => $id])->result_array();
			foreach ($data as $proceso) {
				$procesos[] = $proceso['proceso_id'];
			}
		}
		echo json_encode($procesos);
	}

	public function validar_permisos_gestion_candidato(){
		if (!$this->Super_estado) $permiso = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id_solicitud = $this->input->post('solicitud');
			$info = $this->talento_humano_model->solicitud_cerrada($id_solicitud);
			$resp['cerrada'] = $info['cerrada'] == 1 ? true : false;
			$resp['admin'] = $this->admin || $this->admin_th ? true : false;
			$resp['procesos'] = $this->estados;
		}
		echo json_encode($resp);
	}

	public function aprobar_contratacion() {
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id = $this->input->post('candidatos_seleccion_id');
			$fecha_vb_Jef = date("Y-m-d");
			$data = array('aprobacion_jefe' => 1, 'proceso_actual_id' => 'Sel_VB_Jef', 'fecha_VB_Jef' => $fecha_vb_Jef);
			$res = $this->talento_humano_model->modificar_datos($data, 'candidatos_seleccion', $id);
			if(!$res){
				$data_proceso = [
					'candidato_seleccion_id' => $id,
					'proceso_id' => 'Sel_VB_Jef',
					'usuario_registra' => $_SESSION['persona']
				];
				$this->talento_humano_model->guardar_datos($data_proceso, 'procesos_candidatos');
				$resp = ['mensaje' => "Candidato Aprobado Exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
			}else $resp = ['mensaje' => "Ha ocurrido un error al intentar aprobar al candidato. Por favor comuniquese con el administrador del sistema.", 'tipo' => "error", 'titulo' => "Error!"];
		}
		echo json_encode($resp);
	}

	public function rechazar_contratacion() {
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id = $this->input->post('candidatos_seleccion_id');
			$motivo = $this->input->post('motivo_rechazo');
			$data = array('aprobacion_jefe' => 2, 'proceso_actual_id' => 'Sel_VB_Jef', 'motivo_rechazo_jefe' => $motivo);
			$res = $this->talento_humano_model->modificar_datos($data, 'candidatos_seleccion', $id);
			if(!$res){
				$data_proceso = [
					'candidato_seleccion_id' => $id,
					'proceso_id' => 'Sel_VB_Jef',
					'usuario_registra' => $_SESSION['persona']
				];
				$this->talento_humano_model->guardar_datos($data_proceso, 'procesos_candidatos');
				$resp = ['mensaje' => "Candidato Rechazado Exitosamente!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
			}else $resp = ['mensaje' => "Ha ocurrido un error al intentar aprobar al candidato. Por favor comuniquese con el administrador del sistema.", 'tipo' => "error", 'titulo' => "Error!"];
		}
		echo json_encode($resp);
	}

	public function asignar_csep() {
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$proceso = 'Sel_CVir';
			$tipo = 'Tip_Nue';
			$solicitud = $this->input->post('solicitud');
			$candidato = $this->input->post('candidato');
			$formacion = $this->input->post('id_formacion');
			$observaciones = $this->input->post('observaciones');
			$procedencia = $this->input->post('procedencia');
			$str_fields = [
				'Procedencia' => $procedencia,
				'Formación' => $formacion,
			];
			$num_fields = [
				'Candidato' => $candidato,
				'Solicitud' => $solicitud,
			];
			$num = $this->verificar_campos_numericos($num_fields);
			$str = $this->verificar_campos_string($str_fields);
			if (is_array($str)) {
				$campo = $str['field'];
				$resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>'info','titulo'=> 'Oops.!'];
			}elseif (is_array($num)) {
				$campo = $num['field'];
				$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vacio.",'tipo'=>'info','titulo'=> 'Oops.!'];
			} else {
				$sw = true;
				$info_candidato = $this->talento_humano_model->get_full_info_candidato($solicitud, $candidato);
				$id_solicitud = $this->talento_humano_model->buscar_solicitud_hoy('Hum_Csep');

				$resp = $info_candidato;
				$user = $_SESSION['persona'];
				$notifica = false;
				if(is_null($id_solicitud)){
					$data = [
						'id_tipo_solicitud' => 'Hum_Csep',
						'usuario_registro' => $user,
					];
					$add = $this->talento_humano_model->guardar_datos($data, "solicitudes_talento_hum");
					if (!$add){
						$resp = ['mensaje'=>"Error al guardar la solicitud del día, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
						$sw = false;
					}else{
						$notifica = true;
						$id_solicitud = $this->talento_humano_model->buscar_solicitud_hoy('Hum_Csep');
					}
				}
				if($sw){
					$data = array(
						'id_tipo' => $tipo,
						'id_solicitud' => $id_solicitud,
						'id_postulante' => $candidato,
						'procedencia' => $procedencia,
						// 'id_cargo' => $info_candidato->{'cargo_id'},
						'id_formacion' => $formacion,
						'hoja_vida' => $info_candidato->{'hoja_vida'},
						'observaciones' => $observaciones,
						'usuario_registra' => $user,
						'prueba_psicologia' => $info_candidato->{'informe_seleccion'},
						'candidato_seleccion_id' => $info_candidato->{'candidato_seleccion_id'},
						'id_departamento_postulante' => $tipo != 'Tip_Cam_Plan' ? $info_candidato->{'departamento_id'} : null,
						'id_cargo_postulante' => $tipo != 'Tip_Cam_Plan' ? $info_candidato->{'cargo_id'} : null,
					);
					$add = $this->talento_humano_model->guardar_datos($data, "postulantes_csep");
					$resp = ['mensaje'=>"El postulante fue registrado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!", 'notifica' => $notifica, "id_solicitud_Csep" => $id_solicitud];
					if (!$add){
						$resp = ['mensaje'=>"Error al guardar el postulante, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
					}else{
						$id_postulante = $this->talento_humano_model->traer_ultimo_registro_postulante_sol_usuario($user)->{'id'};
						$data = [
							'id_estado' => 'Pos_Env',
							'id_postulante' => $id_postulante,
							'usuario_registra' => $user,
						];
						$add = $this->talento_humano_model->guardar_datos($data, "estados_postulantes");
						if($add){
							$id = $this->talento_humano_model->get_info_candidato($solicitud, $candidato)->{'id'};
							$this->talento_humano_model->modificar_datos([
								'proceso_actual_id' => $proceso,
								'id_csep' => $id_postulante,
							], 'candidatos_seleccion', $id);
							$data = [
								[
									'candidato_seleccion_id' => $id,
									'proceso_id' => 'Sel_Cse',
									'usuario_registra' => $_SESSION['persona']
								],[
									'candidato_seleccion_id' => $id,
									'proceso_id' => $proceso,
									'usuario_registra' => $_SESSION['persona']
								],
							];
							$this->talento_humano_model->guardar_datos($data, 'procesos_candidatos', 2);
							$resp = ['mensaje' => "Candidato asignado a CSEP exitosamente.", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
						} else $resp = ['mensaje' => "Ha ocurrido un error. Por favor comuniquese con el administrador.", 'tipo' => "error", 'titulo' => "Proceso Exitoso!"];

					}
				}
			}
		}
		echo json_encode($resp);
	}

	public function citar_entrevista_jefe(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega) {
				$proceso = 'Sel_Jef';
				$candidato = $this->input->post('candidato');
				$solicitud = $this->input->post('solicitud');
				$fecha = $this->input->post('fecha_entrevista');
				$responsable = $this->input->post('responsable');
				$ubicacion = $this->input->post('ubicacion');
				$recomendaciones = $this->input->post('recomendaciones');
				if ($this->validateDate($fecha,'Y-m-d H:i') == false){
					$resp = ['mensaje' => "La fecha ingresada no es valida.", 'tipo' => "info", 'titulo' => "Oops.!"];
				}else {
					$num_fields = [
						'Candidato' => $candidato,
						'Solicitud' => $solicitud,
						'Responsable' => $responsable,
						'Ubicacion' => $ubicacion,
					];
					$num = $this->verificar_campos_numericos($num_fields);
					if (is_array($num)) {
						$campo = $num['field'];
						$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vacio.",'tipo'=>'info','titulo'=> 'Oops.!'];
					} else {
						$file = [1, null];
						$adj_hoja_vida = $_FILES['hoja_vida']['size'];
						if(empty($adj_hoja_vida)){
							$sw = false;
							$resp = ['mensaje' => "Debe adjuntar la Hoja de Vida.",'tipo' => "info",'titulo' => "Oops.!"];
						}else {
							$file = $this->cargar_archivo("hoja_vida", $this->ruta_hojas, 'hoja');
							if($file[0] == -1){
								$resp = ['mensaje' => "Error al cargar la Hoja de Vida.", 'tipo' => "error", 'titulo'=> "Oops.!"];
							}else{
								$hoja_vida = $file[1];
								$data = [
									'fecha_entrevista' => $fecha,
									'encargado_entrevista' => $responsable,
									'ubicacion_entrevista_jefe' => $ubicacion,
									'hoja_vida' => $hoja_vida,
									'observacion' => $recomendaciones,
								];
								$id = $this->talento_humano_model->get_info_candidato($solicitud, $candidato)->{'id'};
								$res = $this->talento_humano_model->modificar_datos($data, 'candidatos_seleccion', $id);
								$res = 0;
								if(!$res){
									$this->talento_humano_model->modificar_datos(['proceso_actual_id' => $proceso], 'candidatos_seleccion', $id);
									$data = [
										'candidato_seleccion_id' => $id,
										'proceso_id' => $proceso,
										'usuario_registra' => $_SESSION['persona'],
									];
									$this->talento_humano_model->guardar_datos($data, 'procesos_candidatos');
									$resp = ['mensaje' => "Entrevista asignada exitosamente.", 'tipo' => "success", 'titulo' => "Oops.!"];
								}else $resp = ['mensaje' => "Ha ocurrido un error al citar la entrevista.", 'tipo' => "error", 'titulo' => "Oops.!"];
							}
						}
					}
				}
			}
		}
		echo json_encode($resp);
	}

	public function guardar_fecha_ingreso(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
				$proceso = 'Sel_Ing';
				$fecha = $this->input->post('fecha_ingreso');
				// $tipo_contrato = $this->input->post('tipo_contrato');
				$observaciones = $this->input->post('observaciones');
				$candidato = $this->input->post('candidato');
				if ($this->validateDate($fecha,'Y-m-d') == false){
					$resp = ['mensaje' => "La fecha ingresada no es valida.", 'tipo' => "info", 'titulo' => "Oops.!"];
				}else {
					// $str = $this->verificar_campos_string(['Tipo Contrato'=>$tipo_contrato]);
					// if (is_array($str)) {
					// 	$campo = $str['field'];
					// 	$res = ['mensaje'=>"El campo $campo no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
					// }else{
						$res = $this->talento_humano_model->modificar_datos(['fecha_final_ingreso' => $fecha, 'observacion_ingreso_final' => $observaciones], 'candidatos_seleccion', $candidato);
						if(!$res){
							$this->talento_humano_model->modificar_datos(['proceso_actual_id' => $proceso], 'candidatos_seleccion', $candidato);
							$data = [
								'candidato_seleccion_id' => $candidato,
								'proceso_id' => $proceso,
								'usuario_registra' => $_SESSION['persona'],
							];
							$this->talento_humano_model->guardar_datos($data, 'procesos_candidatos');
							$resp = ['mensaje' => "Fecha de ingreso registrada exitosamente.", 'tipo' => "success", 'titulo' => "Oops.!"];
						} else $resp =['mensaje' => "Error al intentar registrar la fecha de ingreso.", 'tipo' => "info", 'titulo' => "Oops.!"];
					// }
				}
			}
		}
		echo json_encode($resp);
	}

	public function get_info_contratacion(){
		$info = [];
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica) {
				$id = $this->input->post('id');
				$candidato = $this->input->post('candidato');
				$info = $this->talento_humano_model->get_full_info_candidato($id, $candidato);
				$data = [
					'id_solicitud' => $id,
					'id_persona' => $info->{'candidato_seleccion_id'},
				];
				$info->{'avales'} = $this->talento_humano_model->get_where('archivos_adj_th', $data)->result_array();
			}
		}
		echo json_encode($info);
	}

	public function get_correos_participantes_descartados(){
		$info = [];
		if (!$this->Super_estado) $info = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id = $this->input->post('solicitud');
			// if($this->admin || $this->admin_th){
			$info = $this->talento_humano_model->get_correos_participantes_descartados($id);
			// } else $info = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		}
		echo json_encode($info);
	}

	public function get_correo_encargado(){
		if (!$this->Super_estado) $info = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$info = $this->talento_humano_model->get_where('valor_parametro', ['id_aux' => 'Par_TH', 'idparametro' => 20])->row()->valor;
		}
		echo json_encode($info);
	}

	public function documentos_rc()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $motivo = $this->input->post('motivo');
            $resp = $this->talento_humano_model->documentos_rc($motivo);
        }
        echo json_encode($resp);
    }


	public function validar_permiso_invitacion(){
		$sw = false;
		if (!$this->Super_estado) $info = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if($this->admin) $sw = true;
			else if($this->estados) {
				foreach ($this->estados as $estado) {
					if($estado['actividad'] == 'Hum_Sele' && ($estado['estado'] == 'Tal_Env' || $estado['estado'] == 'Tal_Pro')) $sw = true;
				}
			}
		}
		echo json_encode($sw);
	}

	public function get_usuarios_a_notificar(){
		if (!$this->Super_estado) $encargados = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$actividad = $this->input->post('actividad');
			$estado = $this->input->post('estado');
			$motivo = $this->input->post('motivo');
			$encargados = $this->talento_humano_model->get_usuarios_a_notificar($actividad, $estado, $motivo);
		}
		echo json_encode($encargados);
	}

	public function get_correo_solicitante(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id_solicitud = $this->input->post('id');
			$resp = $this->talento_humano_model->get_correo_solicitante($id_solicitud);
		}
		echo json_encode($resp);
	}
	public function get_correo_colaborador_ecargo(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id_solicitud = $this->input->post('id');
			$resp = $this->talento_humano_model->get_correo_colaborador_ecargo($id_solicitud);
		}
		echo json_encode($resp);
	}
	public function get_correo_jefe_inmediato2(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id_solicitud = $this->input->post('id');
			$resp = $this->talento_humano_model->get_correo_jefe_inmediato2($id_solicitud);
		}
		echo json_encode($resp);
	}

	public function get_usuarios_notificar_fecha_final(){ 
		$encargados = [];
		if (!$this->Super_estado) $info = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id = $this->input->post('id');
			$encargados = $this->talento_humano_model->get_usuarios_a_notificar('Hum_Sele', 'Sel_Ing');
			$jefe = $this->talento_humano_model->get_correo_responsable_th($id);
			$solicitante = $this->talento_humano_model->get_correo_solicitante($id);
			array_push($encargados, $jefe, $solicitante);
		}
		echo json_encode($encargados);
	}

	public function get_usuarios_notificar_aval(){
		$encargados = [];
		if (!$this->Super_estado) $info = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id = $this->input->post('id');
			$encargados = $this->talento_humano_model->get_usuarios_a_notificar('Hum_Sele', 'Sel_Con');
		}
		echo json_encode($encargados);
	}

	public function get_actividades_asignadas(){
		$info = !$this->Super_estado 
			? ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]
			: $this->talento_humano_model->get_actividades_asignadas();
		echo json_encode($info);
	}
	public function get_actividades_ecargo(){
		$id_solicitud = $this->input->post("id");
		$info = !$this->Super_estado 
			? ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]
			: $this->talento_humano_model->get_actividades_ecargo($id_solicitud);
		echo json_encode($info);
	}

	public function cargar_requisiciones(){
		echo json_encode(
			!$this->Super_estado
				? ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""] 
				: $this->talento_humano_model->cargar_requisiciones()
			);
	}

	public function cerrar_proceso_candidato(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$candidato = $this->input->post('id');
			$candidato_seleccion_id = $this->input->post('candidato_seleccion_id');
			$data_proceso = [
				'candidato_seleccion_id' => $candidato,
				'proceso_id' => 'Sel_Ind',
				'usuario_registra' => $_SESSION['persona'],
			];
			$res = $this->talento_humano_model->guardar_datos($data_proceso, 'procesos_candidatos');
			$resp = ['mensaje' => 'Proceso Cerrado Exitosamente', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
			if($res){
				$data = ['proceso_actual_id' => 'Sel_Ind'];
				$this->talento_humano_model->modificar_datos($data, 'candidatos_seleccion',$candidato_seleccion_id);
			}else $resp = ['mensaje' => 'Ha ocurrido un error al cerrar el proceso.', 'tipo' => 'error', 'titulo' => 'Ooops!'];
		}
		echo json_encode($resp);
	}

	public function traer_correos_responsables_estado(){
		if (!$this->Super_estado) $responsables = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$state = $this->input->post("state");
			$tipo = $this->input->post("tipo");
			$id_estado = $this->talento_humano_model->get_where('valor_parametro', ['id_aux' => $state])->row()->id;
			$responsables = $this->talento_humano_model->traer_correos_responsables_estado($id_estado, $tipo);
			echo json_encode($responsables);
		}
	}

	public function get_info_persona(){
		$res = [];
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$persona_id = $this->input->post('id');
			$res = $this->talento_humano_model->get_info_persona($persona_id);
		}
		echo json_encode($res);
	}

	public function solicitar_certificado(){
		$res = ['mensaje'=>"Certificado solicitado exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else {
				$error = false;
				$tipo_certificado = $this->input->post('tipo_certificado');
				
				if($tipo_certificado == 1){
					$data = [
						'id_tipo_solicitud' => 'Hum_Cert',
						'usuario_registro' => $_SESSION['persona'],
						'id_estado_solicitud' => $tipo_certificado == 1 ? 'Tal_Ter' : 'Tal_Env',
					];
					$add = $this->talento_humano_model->guardar_datos($data, "solicitudes_talento_hum");
					if(!$add) $error = true;
				} else if($tipo_certificado == 0){
					$especificaciones = $this->input->post('especificaciones');
					$opciones = $this->input->post("opciones");
					if($especificaciones != '' || $opciones){
						$data = [
							'id_tipo_solicitud' => 'Hum_Cert',
							'usuario_registro' => $_SESSION['persona'],
							'id_estado_solicitud' => $tipo_certificado == 1 ? 'Tal_Ter' : 'Tal_Env',
						];
						$add = $this->talento_humano_model->guardar_datos($data, "solicitudes_talento_hum");
						if($add){
							$ultima_solicitud = $this->talento_humano_model->get_ultima_solicitud('Hum_Cert');
							$added = $this->talento_humano_model->guardar_datos([
								'solicitud_id' => $ultima_solicitud->id,
								'especificaciones' => $especificaciones,
							], "certificados");
							if(!$added) $error = true;
							else {
								$data_opciones = [];
								$certificado = $this->talento_humano_model->get_where('certificados', ['solicitud_id' => $ultima_solicitud->id, 'estado' => 1])->row();
								if($opciones){
									foreach ($opciones as $opcion) {
										array_push($data_opciones, ['certificado_id' => $certificado->id, 'opcion_id' => $opcion]);
									}
									$add = $this->talento_humano_model->guardar_datos($data_opciones, 'opciones_certificados', 2);
									$res = $add
										? ['mensaje' => "Certificado laboral solicitado exitosamente", 'tipo' => "success",'titulo' => "Proceso Exitoso!"]
										: ['mensaje' => "Ha ocurridon un error al intentar generar el certificado laboral", 'tipo' => "success",'titulo' => "Proceso Exitoso!"];
								} else $res = ['mensaje' => "Certificado laboral solicitado exitosamente", 'tipo' => "success",'titulo' => "Proceso Exitoso!"];
							}
						}
					} else $res = ['mensaje' => "Por favor seleccione alguna de las opciones o escriba alguna especificación para el certificado", 'tipo' => "info",'titulo' => "Ooops!"];
				} 
				if($error) $res = [
					'mensaje' => "Ha ocurrido un error al intentar guardar la solicitud. Contacte con el administrador",
					'tipo' => "error",
					'titulo' => "Oops.!"
				];
			}
		}
		echo json_encode($res);
	}

	public function entregar_certificado(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else {
				$id = $this->input->post('id');
				$data = ['id_estado_solicitud' => 'Tal_Ter'];
				$resp = $this->talento_humano_model->modificar_datos($data, 'solicitudes_talento_hum', $id);
				$res = !$resp 
					? ['mensaje' => 'Certificado entregado exitosamente!', 'titulo' => 'Proceso Exitoso!', 'tipo' => 'success']
					: ['mensaje' => 'Ha ocurrido un error al intentar enviar el certificado!', 'titulo' => 'Ooops!', 'tipo' => 'error'];
			}
		}
		echo json_encode($res);
	}

	public function listar_opciones_certificados(){
		$opciones = !$this->Super_estado 
			? ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]
			: $this->talento_humano_model->get_opciones_certificado();
		echo json_encode($opciones);
	}

	public function listar_opciones_certificados_activos(){
		$opciones = !$this->Super_estado 
			? ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""]
			: $this->talento_humano_model->get_opciones_certificado(true);
		echo json_encode($opciones);
	}

	public function activar_opcion_certificado(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$id = $this->input->post('id');
				$asignado = $this->input->post('asignado');
				$opcion = $this->input->post('opcion');
				if(!$asignado) {
					$data_req = ["id_aux" => 'Hum_Cert', 'estado' => 1];
					$proc_certificado = $this->talento_humano_model->get_where('valor_parametro', $data_req)->row()->id;
					$data_req = [
						'vp_principal' => 'Hum_Cert', 
						'vp_principal_id' => $proc_certificado, 
						'vp_secundario_id' => $id
					];
					$resp = $this->talento_humano_model->guardar_datos($data_req, 'permisos_parametros');
					$res = $resp
						 ? ['mensaje'=>"Opción '$opcion' activada exitosamente!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						 : ['mensaje'=>"Ha ocurrido un error al intentar activar !",'tipo'=>"success",'titulo'=> "Ooops!"];
				} else {
					$data = [
						'vp_principal' => 'Hum_Cert',
						'estado' => 1,
						'vp_secundario_id' => $id
					];
					$permiso = $this->talento_humano_model->get_where('permisos_parametros', $data)->row();
					$resp = $this->talento_humano_model->eliminar_datos('permisos_parametros', $permiso->id);
					$res = !$resp
						? ['mensaje'=>"Opción '$opcion' desactivada exitosamente!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
						: ['mensaje'=>"Ha ocurrido un error al intentar desactivar la opción '$opcion'",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
				}
			}
		}
		echo json_encode($res);
	}

	public function crear_nueva_opcion(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$nombre_opcion = $this->input->post('nombre_item');
				$nombre_clave = $this->input->post('nombre_clave_item');
				$str = $this->verificar_campos_string(['Nombre opción'=>$nombre_opcion,'Nombre clave'=>$nombre_clave]);
				if (is_array($str)) {
					$campo = $str['field'];
					$res = ['mensaje'=>"El campo $campo no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
				}else{
					$validar = $this->talento_humano_model->get_where('valor_parametro', [ 'valor' => $nombre_opcion, 'estado' => 1, 'id_aux' => $nombre_clave, 'valorx'=>'Opc_Cert'])->result_array();
					if(count($validar)) $res = ['mensaje'=>"Ya existe una opción con este nombre",'tipo'=>"info",'titulo'=> "Oops.!"];
					else {
						$data = [
							'idparametro' => 189,
							'id_aux' => $nombre_clave,
							'valor' => $nombre_opcion,
							'valorx' => 'Opc_Cert',
							'usuario_registra' => $_SESSION['persona']
						];
						$add = $this->talento_humano_model->guardar_datos($data, 'valor_parametro');
						$res = $add
							? ['mensaje'=>"Opción creada exitosamente!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
							: ['mensaje'=>"Ha ocurrido un error al intentar crear la opción '$nombre_opcion'",'tipo'=>"info",'titulo'=> "Oops.!"];
					}
				}
			}
		}
		echo json_encode($res);
	}

	public function guardar_requisicion_posgrado(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$id_departamento = $this->input->post("id_departamento");
				$persona_id = $this->input->post("persona_id");
				$tipo_vacante = $this->input->post("tipo_vacante");
				$reemplazado = $this->input->post("reemplazado_id");
				$nombre_modulo = $this->input->post("nombre_modulo");
				$horas_modulo = $this->input->post("horas_modulo");
				$numero_promocion = $this->input->post("numero_promocion");
				$cargo = $this->input->post("cargo");
				$valor_hora = $this->input->post("valor_hora");
				$ciudad_origen = $this->input->post("ciudad_origen");
				$fecha_inicio = $this->input->post("fecha_inicio");
				$fecha_terminacion = $this->input->post("fecha_terminacion");
				$tipo_orden = $this->input->post("tipo_orden");
				$tipo_programa = $this->input->post("tipo_programa");
				$req_programa = $this->input->post("req_programa");
				$observaciones = $this->input->post("observaciones");
				
				if($tipo_vacante === 'Vac_Ree') $num["Persona a Reemplazar"] = $reemplazado;

				$str = $this->verificar_campos_string([
					'Nombre del Modulo' => $nombre_modulo,
					'Ciudad de Origen' => $ciudad_origen,
				]);

				$num = $this->verificar_campos_numericos([
					'Persona' => $persona_id,
					'Tipo de Programa' => $tipo_programa,
					'Programa' => $req_programa,
					'Departamento' => $id_departamento,
					'Horas Módulo' => $horas_modulo,
					'Número Promoción' => $numero_promocion,
					// 'Valor Hora' => $valor_hora,
					'Cargo' => $cargo,
				]);
				
				if($this->validateDate($fecha_inicio,'Y-m-d') == false) {
					$res = [
						'mensaje' => "El fecha de nacimiento no es valida.", 
						'tipo' => "info",
						'titulo' => "Oops.!"
					]; 
				} else if ($this->validateDate($fecha_terminacion,'Y-m-d') == false){
					$res = [
						'mensaje' => "El fecha de nacimiento no es valida.",
						'tipo' => "info",
						'titulo' => "Oops.!"
					];
				} else {
					if (is_array($str)) {
						$campo = $str['field'];
						$res = [
							'mensaje' => "El campo $campo no puede estar vació.",
							'tipo' => "info",
							'titulo' => "Oops.!"
						];
					}else if(is_array($num)){
						$campo = $num['field'];
						$res = [
							'mensaje' => "El campo $campo debe ser numérico.",
							'tipo' => "info",
							'titulo' => "Oops.!"
						];
					} else if ($valor_hora == '') {
						$res = [
							'mensaje' => "El campo Valor a pagar por hora no puede estar vació.",
							'tipo' => "info",
							'titulo' => "Oops.!"
						];
					} else {
						$data_sol = [
							"id_tipo_solicitud" => "Hum_Posg",
							"usuario_registro" => $_SESSION['persona'],
							"id_estado_solicitud" => "Tal_Env"
						];

						if(in_array(['actividad' => "Hum_Posg", 'estado' => 'Tal_Env'], $this->estados)){
							$data_sol['id_estado_solicitud'] = "Tal_Pro";
						} else if(in_array(['actividad' => "Hum_Posg", 'estado' => 'Tal_Pro'], $this->estados)){
							$data_sol['id_estado_solicitud'] = "Tal_Ter";
						}
						$solicitud = $this->talento_humano_model->guardar_datos($data_sol, 'solicitudes_talento_hum');
						if($solicitud){
							$data_estado = [
								"solicitud_id" => $solicitud,
								"estado_id" => $data_sol['id_estado_solicitud'],
								"usuario_id" => $_SESSION['persona']
							];
							$this->talento_humano_model->guardar_datos($data_estado, 'estados_solicitudes_talento');
							$file = $this->cargar_archivo("hoja_vida", $this->ruta_requisicion, 'req');
							if ($file[0] == -1){
								$res = $file[1] === "<p>You did not select a file to upload.</p>"
									? [
										'mensaje' => "Debe adjuntar la hoja de vida del postulante.",
										'tipo' => "info",
										'titulo' => "Oops.!"
										]
									: [
										'mensaje' => "Error al cargar la hoja de vida del postulante.",
										'tipo' => "error",
										'titulo' => "Oops.!"
									];
							} else {
								$data_detalle = [
									"solicitud_id" => $solicitud,
									"tipo_vacante" => $tipo_vacante,
									"id_candidato" => $persona_id,
									"id_departamento" => $id_departamento,
									"nombre_modulo" => $nombre_modulo,
									"horas_modulo" => $horas_modulo,
									"numero_promocion" => $numero_promocion,
									"id_cargo" => $cargo,
									"valor_hora" => $valor_hora,
									"ciudad_origen" => $ciudad_origen,
									"fecha_inicio" => $fecha_inicio,
									"fecha_terminacion" => $fecha_terminacion,
									"tipo_programa" => $tipo_programa,
									"id_programa" => $req_programa,
									"documentos" => $file[1],
									"observacion" => $observaciones
								];
								if(isset($tipo_orden) && !empty($tipo_orden)) {
									$data_detalle['tipo_orden'] = $tipo_orden;
								}
								if($tipo_vacante === 'Vac_Ree') $data_detalle["id_reemplazado"] = $reemplazado;
								$resp = $this->talento_humano_model->guardar_datos($data_detalle, 'detalle_requisicion');
								if($resp){
									// if($data_sol['id_estado_solicitud'] === 'Tal_Env'){
									// 	$personas_notificar = $this->talento_humano_model->get_correos_decanos($id_departamento,);
									// } else {
									// 	$personas_notificar = $this->talento_humano_model->get_usuarios_a_notificar("Hum_Posg", $data_sol['id_estado_solicitud']);
									// }
									$personas_notificar = $this->talento_humano_model->get_usuarios_a_notificar_estado_posgrado($data_sol['id_estado_solicitud'], $id_departamento);
									
									$res = [
										'mensaje' => "Solicitud de requisición creada exitosamente!",
										'tipo' => "success",
										'titulo' => "Proceso Exitoso!",
										'personas_notificar' => count($personas_notificar) ? $personas_notificar : false,
										'estado' => $data_sol['id_estado_solicitud'],
										'id' => $solicitud,
										'files' => $file[1]
									];
								} else {
									$res = [
										'mensaje' => "Ha ocurrido un error al intentar guardar el detalle de la solicitud",
										'tipo' => "info",
										'titulo' => "Oops.!"
									];
								}
							}
						} else $res = [
							'mensaje' => "Ha ocurrido un error al intentar crear la solicitud",
							'tipo' => "info",
							'titulo' => "Oops.!"
						];
					}
				}
			}
		}
		echo json_encode($res);
	}

	public function detalle_requisicion_posgrado() {
		$res = [
			'mensaje' => "Ha ocurrido un error",
			'tipo' => "error",
			'titulo' => "Ooops!"
		];
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega == 0) $res = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
			else{
				$id = $this->input->post("id");
				$res = $this->talento_humano_model->detalle_requisicion_posgrado($id);
			}
		}
		echo json_encode($res);
	}

	public function avalar_perfil(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$id = $this->input->post("id");
				$departamento = $this->input->post("departamento");
				$estado = "Tal_Pro";
				$data_aval = [
					"aval_decano" => 1,
					"fecha_aval" => date("Y-m-d"),
					"decano_id" => $_SESSION['persona']
				];
				$mod = $this->talento_humano_model->modificar_datos($data_aval, 'detalle_requisicion', $id, 'solicitud_id');
				if(!$mod){
					$data_sol = ["id_estado_solicitud" => $estado];
					$mod = $this->talento_humano_model->modificar_datos($data_sol, 'solicitudes_talento_hum', $id);
					if($mod) $res = ['mensaje' => "Ha ocurrido un error al intetnar procesar la solicitud", 'tipo' => "info", 'titulo' => "Ooops!"];
					else {
						$data_estado = [
							"solicitud_id" => $id,
							"estado_id" => $estado,
							"usuario_id" => $_SESSION['persona']
						];
						$this->talento_humano_model->guardar_datos($data_estado, 'estados_solicitudes_talento');
						$file = $this->talento_humano_model->get_where('detalle_requisicion', ['solicitud_id' => $id])->row()->documentos;
						// $personas_notificar = $this->talento_humano_model->get_usuarios_a_notificar("Hum_Posg", $estado);
						$personas_notificar = $this->talento_humano_model->get_usuarios_a_notificar_estado_posgrado($estado, $departamento);
						$res = [
							'mensaje' => "Perfil avalado exitosamente!", 
							'tipo' => "success", 
							'titulo' => "Proceso Exitoso!",
							'personas_notificar' => count($personas_notificar) ? $personas_notificar : false,
							'file' => $file
						];
					}
				}
			}
		}
		echo json_encode($res);
	}

	public function terminar_requisicion() {
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$estado = "Tal_Ter";				
				$codigo_sap = $this->input->post("codigo_sap");
				$id = $this->input->post("id");
				$tipo_orden = $this->input->post("tipo_orden");
				$departamento = $this->input->post("id_departamento");

				$num = $this->verificar_campos_numericos([
					'Código SAP' => $codigo_sap,
					'Tipo de Orden' => $tipo_orden,
				]);

				if (is_array($num)) {
					$campo = $num['field'];
					$res = [
						'mensaje' => "El campo $campo debe ser numérico.",
						'tipo' => "info",
						'titulo' => "Oops.!"
					];
				} else {
					$mod_det = $this->talento_humano_model->modificar_datos(["tipo_orden" => $tipo_orden, "codigo_sap" => $codigo_sap], 'detalle_requisicion', $id, 'solicitud_id');
					if(!$mod_det){
						$mod_sol = $this->talento_humano_model->modificar_datos(["id_estado_solicitud" => $estado], 'solicitudes_talento_hum', $id);
						if(!$mod_sol){
							$data_estado = [
								"solicitud_id" => $id,
								"estado_id" => $estado,
								"usuario_id" => $_SESSION['persona']
							];
							$this->talento_humano_model->guardar_datos($data_estado, 'estados_solicitudes_talento');
							$documentos = $this->talento_humano_model->get_where('detalle_requisicion', ['solicitud_id' => $id,'estado' => 1])->row()->documentos;
							// $personas_notificar = $this->talento_humano_model->get_usuarios_a_notificar("Hum_Posg", $estado);
							$personas_notificar = $this->talento_humano_model->get_usuarios_a_notificar_estado_posgrado($estado, $departamento);
							$estados_anteriores = $this->talento_humano_model->get_usuarios_a_notificar_estado_anteriores($id, $personas_notificar);
							$personas = array_merge($personas_notificar,$estados_anteriores);
							$res = [
								'mensaje' => "Solicitud Aprobada Exitosamente!",
								'tipo' => "success",
								'titulo' => "Proceso Exitoso!",
								'personas_notificar' => count($personas) ? $personas : false,
								'documentos' => $documentos
							];
						} else $res = ['mensaje' => "Ha ocurrido un error al intentar aprobar la solicitud", 'tipo' => "error", 'titulo' => "Ooops!"];
					}
				}
			}
		}
		echo json_encode($res);
	}

	public function modificar_requisicion_posgrado(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$tipo_vacante = $this->input->post("tipo_vacante");
				$tipo_programa = $this->input->post("tipo_programa");
				$req_programa = $this->input->post("req_programa");
				$solicitud = $this->input->post("solicitud");
				$departamento = $this->input->post("id_departamento");
				$nombre_modulo = $this->input->post("nombre_modulo");
				$horas_modulo = $this->input->post("horas_modulo");
				$numero_promocion = $this->input->post("numero_promocion");
				$cargo = $this->input->post("cargo");
				$valor_hora = $this->input->post("valor_hora");
				$ciudad_origen = $this->input->post("ciudad_origen");
				$fecha_inicio = $this->input->post("fecha_inicio");
				$fecha_terminacion = $this->input->post("fecha_terminacion");
				$reemplazado = $this->input->post("reemplazado_id");
				$candidato = $this->input->post("candidato");
				$observacion = $this->input->post("observaciones");

				$str = $this->verificar_campos_string([
					'Nombre del Modulo' => $nombre_modulo,
					'Ciudad de Origen' => $ciudad_origen,
				]);

				$numFields = [
					'Solicitud' => $solicitud,
					'Candidato' => $candidato,
					'Departamento' => $departamento,
					'Horas Módulo' => $horas_modulo,
					'Número Promoción' => $numero_promocion,
					// 'Valor Hora' => $valor_hora,
					'Tipo de programa' => $tipo_programa,
					'Programa' => $req_programa,
					'Cargo' => $cargo,
				];
				if($tipo_vacante === 'Vac_Ree') $numFields["Persona a Reemplazar"] = $reemplazado;

				$num = $this->verificar_campos_numericos($numFields);
				
				if($this->validateDate($fecha_inicio,'Y-m-d') == false) {
					$res = [
						'mensaje' => "El fecha de nacimiento no es valida.", 
						'tipo' => "info",
						'titulo' => "Oops.!"
					]; 
				} else if ($this->validateDate($fecha_terminacion,'Y-m-d') == false){
					$res = [
						'mensaje' => "El fecha de nacimiento no es valida.",
						'tipo' => "info",
						'titulo' => "Oops.!"
					];
				} else {
					if (is_array($str)) {
						$campo = $str['field'];
						$res = [
							'mensaje' => "El campo $campo no puede estar vació.",
							'tipo' => "info",
							'titulo' => "Oops.!"
						];
					}else if(is_array($num)){
						$campo = $num['field'];
						$res = [
							'mensaje' => "El campo $campo debe ser numérico.",
							'tipo' => "info",
							'titulo' => "Oops.!"
						];
					} else if ($valor_hora == '') {
						$res = [
							'mensaje' => "El campo Valor a pagar por hora no puede estar vació.",
							'tipo' => "info",
							'titulo' => "Oops.!"
						];	
					} else {
						$data_mod = [
							"id_candidato" => $candidato,
							"id_departamento" => $departamento,
							"tipo_vacante" => $tipo_vacante,
							"nombre_modulo" => $nombre_modulo,
							"horas_modulo" => $horas_modulo,
							"numero_promocion" => $numero_promocion,
							"id_cargo" => $cargo,
							"valor_hora" => $valor_hora,
							"ciudad_origen" => $ciudad_origen,
							"fecha_inicio" => $fecha_inicio,
							"fecha_terminacion" => $fecha_terminacion,
							"tipo_programa" => $tipo_programa,
							"observacion" => $observacion,
							"id_programa" => $req_programa,
						];
						if($tipo_vacante === 'Vac_Ree') $data_mod['id_reemplazado'] = $reemplazado;
						$mod = $this->talento_humano_model->modificar_datos($data_mod, 'detalle_requisicion', $solicitud, 'solicitud_id');
						$res = !$mod
							? ['mensaje'=>"Solicitud modificada exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
							: ['mensaje'=>"ha ocurrido un error al intentar modificar la solicitud.",'tipo'=>"error",'titulo'=> "Ooops!"];
					}
				}
			}
		}
		echo json_encode($res);
	}

	public function exonerar_candidato(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$proceso = "Sel_Exo";
				$candidato = $this->input->post("candidato");
				$motivo = $this->input->post("motivo");
				$data_mod = [
					"proceso_actual_id" => $proceso,
					"motivo_exoneracion" => $motivo,
					"contratado" => 1,
				];
				$mod = $this->talento_humano_model->modificar_datos($data_mod, 'candidatos_seleccion', $candidato);
				$res = [
					'mensaje' => "Ha ocurrido un error al intentar cambiar de estado.",
					'tipo' => "error",
					'titulo' => "Oops.!"
				];
				if(!$mod){
					$data = [
						"proceso_id" => $proceso,
						"candidato_seleccion_id" => $candidato,
						"usuario_registra" => $_SESSION['persona']
					];
					$add = $this->talento_humano_model->guardar_datos($data, 'procesos_candidatos');
					if($add){
						$res = [
							'mensaje' => "",
							'tipo' => "success",
							'titulo' => "Proceso Exitoso!"
						];
					}
				}
			}
		}
		echo json_encode($res);
	}

	public function vb_pedagogico(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$id = $this->input->post("id");
				$vacante_id = $this->input->post("vacante_id");
				$vb = (int) $this->input->post("vb");
				$estado = "Tal_Env";
				if($vb === 1 || $vb === 0) {
					$num = $this->verificar_campos_numericos(['Solicitud' => $id]);
					if (is_array($num)) {
						$campo = $num['field'];
						$res = ['mensaje' => "El campo $campo debe ser numérico y no puede estar vació.", 'tipo' => "info", 'titulo' => "Oops.!"]; 
					}else{
						$txt = $vb ? 'aprobada' : 'desaprobada';
							$mod_vb = $this->talento_humano_model->modificar_datos(["vb_pedagogico" => $vb], "vacantes", $vacante_id);
							$mod_sol = $this->talento_humano_model->modificar_datos(["id_estado_solicitud" => $estado], "solicitudes_talento_hum", $id);
							if(!$mod_sol){
								$res = ['mensaje' => "Solicitud $txt exitosamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
								$data_est = [
									"solicitud_id" => $id,
									"estado_id" => $estado,
									"usuario_id" => $_SESSION['persona']
								];
								$this->talento_humano_model->guardar_datos($data_est, "estados_solicitudes_talento");
							} else $res = ['mensaje' => "Ha ocurrido un error al intentar gestionar la solicitud", 'tipo' => "error", 'titulo' => "Ooops!"];
					}
				} else $res = ['mensaje' => "Seleccione una opción válida", 'tipo' => "error", 'titulo' => "Oops.!"];
			}
		}
		echo json_encode($res);
	}

	public function buscar_persona_arl(){
		$personas = array();
		if ($this->Super_estado) {
			$dato = $this->input->post('dato');
			$tipo = $this->input->post('tipo_persona');
			$buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1";
			if (!empty($dato)) $personas = $this->talento_humano_model->buscar_persona_arl($buscar,$tipo);  
		}
		echo json_encode($personas);
	}
	public function buscar_persona_ausentismo(){
		$personas = array();
		if ($this->Super_estado) {
			$dato = $this->input->post('dato');
			$buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1";
			if (!empty($dato)) $personas = $this->talento_humano_model->buscar_persona_ausentismo($buscar);  
		}
		echo json_encode($personas);
	}

	public function afiliacion_arl(){
		if(!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$id_persona = $this->input->post("id_persona");
				$fecha_nacimiento = $this->input->post("fecha_nacimiento");
				$genero = $this->input->post("id_genero");
				$eps = $this->input->post("eps");
				$ciudad = $this->input->post("ciudad");
				$empresa = $this->input->post("empresa");
				$id_nriesgo = $this->input->post("id_nriesgo");
				$fecha_inicio_lab = $this->input->post("fecha_inicio_lab");
				$fecha_fin_lab = $this->input->post("fecha_fin_lab");
				$str = $this->verificar_campos_string(['Fecha de Nacimiento' => $fecha_nacimiento,'Genero' => $genero,'EPS' => $eps,'Empresa' => $empresa,'Ciudad' => $ciudad,'Nivel de Riesgo' => $id_nriesgo,'Fecha Inicio Labor' => $fecha_inicio_lab,'Fecha Fin Labor' => $fecha_fin_lab,]);
				$sw = true;
				$data_persona = array();
				if (is_array($str)) { 
					$campo = $str['field'];
					$res = ['mensaje' => "El campo $campo no puede estar vació.", 'tipo' => "info", 'titulo' => "Oops.!"];
				}else{
					$val_fecha = $this->validar_fechas("Hum_Afi_Arl",$fecha_inicio_lab,'Y-m-d',54);
					if (!$val_fecha['sw']) {
						$res = ['mensaje'=> "Su solicitud debe tener ".$val_fecha['dias_solicitud']."  dias de anticipacion, seleccione otra fecha de inicio válida.", 'tipo'=>"info", 'titulo'=> "Oops."];
					}else{
						$buscar = "p.id = $id_persona";
						$persona = $this->talento_humano_model->buscar_persona_arl($buscar,1);
						foreach ($persona as $row) {
							if(empty($row['fecha_nacimiento']) || $row['fecha_nacimiento'] != $fecha_nacimiento){
								$data_persona['fecha_nacimiento'] = $fecha_nacimiento;
							}
							if(empty($row['genero']) || $row['genero'] != $genero){
								$data_persona['genero'] = $genero;
							}
							if(empty($row['eps']) || $row['eps'] != $eps){
								$data_persona['eps'] = $eps;
							}
						}

						if(!empty($data_persona)){
							$mod = $this->talento_humano_model->modificar_datos($data_persona, "visitantes", $id_persona);
							if($mod != 0){
								$res = ['mensaje'=>"Error al actualizar información del funcionario, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
								$sw = false;
							}else $sw = true;
						}

						if($sw){
							$data_sol = [
								"id_tipo_solicitud" => "Hum_Afi_Arl",
								"usuario_registro" => $_SESSION['persona'],
								"id_estado_solicitud" => "Tal_Env"];
							$solicitud = $this->talento_humano_model->guardar_datos($data_sol, 'solicitudes_talento_hum');
							if(!$solicitud){
								$res = ['mensaje'=>"Error al crear la solicitud de afiliación, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
							}else{
								$data_estado = ["solicitud_id" => $solicitud,"estado_id" => "Tal_Env","usuario_id" => $_SESSION['persona']];
								$estado_sol = $this->talento_humano_model->guardar_datos($data_estado, 'estados_solicitudes_talento');

								$res = ['mensaje'=>"La solicitud fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
								
								$data = [
									"id_solicitud" => $solicitud,
									"id_persona" => $id_persona,
									"empresa" => $empresa,
									"ciudad" => $ciudad,
									"id_nivel_riesgo" => $id_nriesgo,
									"fecha_inicio_labor" => $fecha_inicio_lab,
									"fecha_fin_labor" => $fecha_fin_lab,
									"id_usuario_registra" => $_SESSION['persona']];
								$data_afil = $this->talento_humano_model->guardar_datos($data, 'afiliacion_arl_th');
								if(!$data_afil){
									$res = ['mensaje'=>"Error al guardar detalle de la solicitud, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
								}
							}
						}
					}							
				}
			}
		}
		echo json_encode($res);
	}


	public function cobertura_arl(){
		if(!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_agrega == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$id_persona = $this->input->post("id_persona");
				$fecha_nacimiento = $this->input->post("fecha_nacimiento");
				$genero = $this->input->post("id_genero");
				$eps = $this->input->post("eps");
				$fecha_viaje = $this->input->post("fecha_viaje");
				$fecha_regreso = $this->input->post("fecha_regreso");
				$id_cobertura = $this->input->post("id_cobertura");  
				$destino = $this->input->post("destino");
				$empresa = $this->input->post("empresa");
				$idioma = $this->input->post("idioma");
				$actividad = $this->input->post("actividad");
				$str = $this->verificar_campos_string(['Fecha de Nacimiento' => $fecha_nacimiento,'Genero' => $genero,'EPS' => $eps,'Empresa' => $empresa,'Ciudad - País' => $destino,'Fecha de Viaje' => $fecha_viaje,'Fecha Regreso' => $fecha_regreso,'Idioma' => $idioma,'Cobertura' => $id_cobertura, 'Actividad' => $actividad]);
				$sw = true;
				$data_persona = array();
				if (is_array($str)) { 
					$campo = $str['field'];
					$res = ['mensaje' => "El campo $campo no puede estar vació.", 'tipo' => "info", 'titulo' => "Oops.!"];
				}else{
					//validar fecha anticipada
					$val_fecha = $this->validar_fechas("Hum_Cob_Arl",$fecha_viaje,'Y-m-d',54);
					if (!$val_fecha['sw']) {
						$res = ['mensaje'=> "Su solicitud debe tener ".$val_fecha['dias_solicitud']."  dias de anticipacion, seleccione otra fecha de viaje válida.", 'tipo'=>"info", 'titulo'=> "Oops."];
					}else{
						$buscar = "p.id = $id_persona";
						$persona = $this->talento_humano_model->buscar_persona_arl($buscar);
						foreach ($persona as $row) {
							if(empty($row['fecha_nacimiento']) || $row['fecha_nacimiento'] != $fecha_nacimiento){
								$data_persona['fecha_nacimiento'] = $fecha_nacimiento;
							}
							if(empty($row['genero']) || $row['genero'] != $genero){
								$data_persona['genero'] = $genero;
							}
							if(empty($row['eps']) || $row['eps'] != $eps){
								$data_persona['eps'] = $eps;
							}
						}

						if(!empty($data_persona)){
							$mod = $this->talento_humano_model->modificar_datos($data_persona, "personas", $id_persona);
							if($mod != 0){
								$res = ['mensaje'=>"Error al actulizar información del funcionario, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
								$sw = false;
							}else $sw = true;
						}

						if($sw){
							$data_sol = [
								"id_tipo_solicitud" => "Hum_Cob_Arl",
								"usuario_registro" => $_SESSION['persona'],
								"id_estado_solicitud" => "Tal_Env"];
							$solicitud = $this->talento_humano_model->guardar_datos($data_sol, 'solicitudes_talento_hum');
							if(!$solicitud){
								$res = ['mensaje'=>"Error al crear la solicitud de Cobertura, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
							}else{
								$data_estado = ["solicitud_id" => $solicitud,"estado_id" => "Tal_Env","usuario_id" => $_SESSION['persona']];
								$estado_sol = $this->talento_humano_model->guardar_datos($data_estado, 'estados_solicitudes_talento');

								$res = ['mensaje'=>"La solicitud fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
								
								$data = [
									"id_solicitud" => $solicitud,
									"id_persona" => $id_persona,
									"tipo_cobertura" => $id_cobertura,
									"fecha_viaje" => $fecha_viaje,
									"fecha_regreso" => $fecha_regreso,
									"actividad" => $actividad,
									"empresa" => $empresa,
									"ciudad" => $destino,
									"idioma" => $idioma,
									"id_usuario_registra" => $_SESSION['persona']];
								$data_cob = $this->talento_humano_model->guardar_datos($data, 'cobertura_arl_th');
								if(!$data_cob){
									$res = ['mensaje'=>"Error al guardar detalle de la solicitud, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
								}
							}
						}
					}
				}
			}
		}
		echo json_encode($res);
	}

	public function get_detalle_solicitud_arl(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$id_solicitud = $this->input->post("id_solicitud");
			$id_tipo_solicitud = $this->input->post("id_tipo_solicitud");
			if($id_tipo_solicitud === 'Hum_Afi_Arl'){
				$tabla = 'afiliacion_arl_th';
				$riesgo = ',vp.valor as riesgo';
			 }else{
				$tabla = 'cobertura_arl_th';
				$riesgo = '';
			 }
			$res = $this->talento_humano_model->get_detalle_solicitud_arl($id_solicitud, $tabla, $riesgo);
		}
		echo json_encode($res);
	}

	public function aprobar_solicitud_arl(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$id = $this->input->post("id");
				$vb = (int) $this->input->post("vb_arl");
				$estado = $vb ? 'Tal_Ter' : 'Tal_Neg';
				$id_tipo_solicitud = $this->input->post("id_tipo_solicitud");
				$sw = true;
				if($vb === 1 || $vb === 0) {
					$num = $this->verificar_campos_numericos(['Solicitud' => $id]);
					if (is_array($num)) {
						$campo = $num['field'];
						$res = ['mensaje' => "El campo $campo debe ser numérico y no puede estar vació.", 'tipo' => "info", 'titulo' => "Oops.!"]; 
					}else{
						$data_cert = array();
						$certificado_arl = '';
						if($vb === 1){
							$nombre = $_FILES["certificado_arl"]["name"];
							$file = $this->cargar_archivo("certificado_arl", $this->ruta_arl, 'cert_arl');
							if ($file[0] == -1){
								$error = $file[1];
								if ($error == "<p>You did not select a file to upload.</p>") {
									$res = ['mensaje'=>"Debe adjuntar el certificado de ARL.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
									$sw = false;
								}else{
									$res = ['mensaje'=>"Error al cargar el certificado de ARL.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
									$sw = false;
								} 
							}else{
								$certificado_arl = $file[1];
								if($id_tipo_solicitud === 'Hum_Afi_Arl') $tabla = 'afiliacion_arl_th';
								else $tabla = 'cobertura_arl_th';
								$detalle = $this->talento_humano_model->get_detalle_solicitud_arl($id, $tabla, '');
								$data_cert = [
									'id_solicitud' => $id,
									'nombre_real' => $nombre,
									'nombre_archivo' => $certificado_arl,
									'usuario_registra' => $_SESSION['persona'],
								];
							} 
						}

						if($sw){
							$txt = $vb ? 'aprobada' : 'desaprobada';
							$mod_sol = $this->talento_humano_model->modificar_datos(["id_estado_solicitud" => $estado], "solicitudes_talento_hum", $id);
							if(!$mod_sol){
								$res = ['mensaje' => "Solicitud $txt exitosamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso!",'certificado' => $certificado_arl];
								$data_est = [
									"solicitud_id" => $id,
									"estado_id" => $estado,
									"usuario_id" => $_SESSION['persona']
								];
								$this->talento_humano_model->guardar_datos($data_est, "estados_solicitudes_talento");
								if($data_cert) $this->talento_humano_model->guardar_datos($data_cert, 'archivos_adj_th');
							} else $res = ['mensaje' => "Ha ocurrido un error al intentar gestionar la solicitud", 'tipo' => "error", 'titulo' => "Ooops!"];
						}
					}
				} else $res = ['mensaje' => "Seleccione una opción válida", 'tipo' => "error", 'titulo' => "Oops.!"];
			}
		}
		echo json_encode($res);
	}
	public function aprobar_solicitud_entidades(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$id = $this->input->post("id");
				$vb = (int) $this->input->post("vb_eps");
				$estado = $vb ? 'Tal_Apr' : 'Tal_Neg';
				$id_tipo_solicitud = $this->input->post("id_tipo_solicitud");
				$sw = true;
				if($vb === 1 || $vb === 0) {
					$num = $this->verificar_campos_numericos(['Solicitud' => $id]);
					if (is_array($num)) {
						$campo = $num['field'];
						$res = ['mensaje' => "El campo $campo debe ser numérico y no puede estar vació.", 'tipo' => "info", 'titulo' => "Oops.!"];
					}	else{
							$data_cert = array();
							$certificado_entidades = '';
								if($vb === 1 || $vb === 0){
								$nombre = $_FILES["certificado_entidades"]["name"];
								$file = $this->cargar_archivo("certificado_entidades", $this->ruta_gestion, 'cert_cam_eps');
								if($vb === 1 || $vb === 0){
								$nombre = $_FILES["certificado_entidades"]["name"];
								$file = $this->cargar_archivo("certificado_entidades", $this->ruta_gestion, 'cert_cam_eps');
									if ($file[0] == -1){
										$error = $file[1];
									if ($error == "<p>You did not select a file to upload.</p>") {
										$res = ['mensaje'=>"Debe adjuntar el certificado.",'tipo'=>"info",'titulo'=> "Oops.!"];
										$sw = false;
									}else{
										$res = ['mensaje'=>"Error al cargar el certificado.",'tipo'=>"error",'titulo'=> "Oops.!"];
										$sw = false;
									}
									}else{
									$certificado_entidades = $file[1];
									$data_cert = [
										'id_solicitud' => $id,
										'nombre_real' => $nombre,
										'nombre_archivo' => $certificado_entidades,
										'usuario_registra' => $_SESSION['persona'],
									];
								}
									if($sw){
										$txt = $vb ? 'aprobada' : 'desaprobada';
										$mod_sol = $this->talento_humano_model->modificar_datos(["id_estado_solicitud" => $estado], "solicitudes_talento_hum", $id);
										if(!$mod_sol){
											$res = ['mensaje' => "Solicitud $txt exitosamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso!",'certificado' => $certificado_entidades];
											$data_est = [
												"solicitud_id" => $id,
												"estado_id" => $estado,
												"usuario_id" => $_SESSION['persona']
											];
											$this->talento_humano_model->guardar_datos($data_est, "estados_solicitudes_talento");
											if($data_cert) $this->talento_humano_model->guardar_datos($data_cert, 'archivos_adj_th');
										} else $res = ['mensaje' => "Ha ocurrido un error al intentar gestionar la solicitud", 'tipo' => "error", 'titulo' => "Ooops!"];
								}
						}
					} else $res = ['mensaje' => "Seleccione una opción válida", 'tipo' => "error", 'titulo' => "Oops.!"];
				}

			}
			echo json_encode($res);
		}
	}
}

	public function modificar_afiliacion_arl(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$id_solicitud = $this->input->post("id_solicitud");
				$id_estado_solicitud = $this->input->post("id_estado");
				$id_persona = $this->input->post("id_persona");
				$fecha_nacimiento = $this->input->post("fecha_nacimiento");
				$genero = $this->input->post("id_genero");
				$eps = $this->input->post("eps");
				$ciudad = $this->input->post("ciudad");
				$empresa = $this->input->post("empresa");
				$id_nriesgo = $this->input->post("id_nriesgo");
				$fecha_inicio_lab = $this->input->post("fecha_inicio_lab");
				$fecha_fin_lab = $this->input->post("fecha_fin_lab");
				$motivo = NULL;			
				$str = $this->verificar_campos_string(['Empresa' => $empresa,'Ciudad' => $ciudad,'Nivel de Riesgo' => $id_nriesgo,'Fecha Inicio de labor' => $fecha_inicio_lab,'Fecha Fin labor' => $fecha_fin_lab, 'Fecha de Nacimiento' => $fecha_nacimiento,'Genero' => $genero,'EPS' => $eps]);
				$sw = true;
				if (is_array($str)) {
					$campo = $str['field']; 						
					$res = ['mensaje' => "El campo $campo no puede estar vació.", 'tipo' => "info", 'titulo' => "Oops.!"];
					$sw = false;
				}else{		
					$data_persona = array();
					$buscar = "p.id = $id_persona";
					$persona = $this->talento_humano_model->buscar_persona_arl($buscar,1);
					foreach ($persona as $row) {
						if(empty($row['fecha_nacimiento']) || $row['fecha_nacimiento'] != $fecha_nacimiento){
							$data_persona['fecha_nacimiento'] = $fecha_nacimiento;
						}
						if(empty($row['genero']) || $row['genero'] != $genero){
							$data_persona['genero'] = $genero;
						}
						if(empty($row['eps']) || $row['eps'] != $eps){
							$data_persona['eps'] = $eps;
						}
					}
					if(!empty($data_persona)){
						$mod_per = $this->talento_humano_model->modificar_datos($data_persona, "visitantes", $id_persona);
						if($mod_per != 0){
							$res = ['mensaje'=>"Error al actualizar información del funcionario, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
							$sw = false;
						}else $sw = true;
					}	
					$detalle = $this->talento_humano_model->get_detalle_solicitud_arl($id_solicitud, 'afiliacion_arl_th', '');
					if($id_estado_solicitud === 'Tal_Pro'){	
						$motivo = $this->input->post("motivo");						
						$str_mot = $this->verificar_campos_string(['Motivo' => $motivo]);
						if (is_array($str_mot)) {		
							$res = ['mensaje' => "El campo ". $str_mot['field']." no puede estar vació.", 'tipo' => "info", 'titulo' => "Oops.!"];
							$sw = false;
						}

						if(!empty($data_persona) && ($detalle->{'empresa'} == $empresa) && ($detalle->{'ciudad'} == $ciudad) && ($detalle->{'id_nivel_riesgo'} == $id_nriesgo) && ($detalle->{'fecha_inicio_labor'} == $fecha_inicio_lab) && ($detalle->{'fecha_fin_labor'} == $fecha_fin_lab) && (empty($data_persona)) ) {
							$res = ['mensaje'=>"Debe realizar alguna modificación en la solicitud.",'tipo'=>"info",'titulo'=> "Oops.!"];
							$sw = false;
						}else{
							if($detalle->{'motivo'} == $motivo) {
								$res = ['mensaje'=>"Debe realizar alguna modificación en la observación.",'tipo'=>"info",'titulo'=> "Oops.!"];
								$sw = false;
							}
						}
					}else{
						if(!empty($data_persona) && ($detalle->{'empresa'} == $empresa) && ($detalle->{'ciudad'} == $ciudad) && ($detalle->{'id_nivel_riesgo'} == $id_nriesgo) && ($detalle->{'fecha_inicio_labor'} == $fecha_inicio_lab) && ($detalle->{'fecha_fin_labor'} == $fecha_fin_lab) && (empty($data_persona)) ) {
							$res = ['mensaje'=>"Debe realizar alguna modificación en la solicitud.",'tipo'=>"info",'titulo'=> "Oops.!"];
							$sw = false;
						}else{
							$val_fecha = $this->validar_fechas("Hum_Afi_Arl",$fecha_inicio_lab,'Y-m-d',54);
							if (!$val_fecha['sw']) {
								$res = ['mensaje'=> "Su solicitud debe tener ".$val_fecha['dias_solicitud']."  dias de anticipacion, seleccione otra fecha de inicio válida.", 'tipo'=>"info", 'titulo'=> "Oops."];
								$sw = false;
							}
						}
					}
					
					if($sw){
						$data_solicitud = [    
							'id_persona'=>$id_persona,           
							'empresa'=>$empresa,
							'ciudad'=>$ciudad,
							'id_nivel_riesgo'=> $id_nriesgo,
							'fecha_inicio_labor'=>$fecha_inicio_lab,
							'fecha_fin_labor'=>$fecha_fin_lab,
							'motivo'  => $motivo];
						$mod_sol = $this->talento_humano_model->modificar_datos($data_solicitud, 'afiliacion_arl_th', $id_solicitud, 'id_solicitud');
						if ($mod_sol != 0) $res = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
						else $res = ['mensaje'=>"La solicitud fue gestionada exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
					}
				}
			}
		}
		echo json_encode($res);
	}

	public function modificar_cobertura_arl(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			if ($this->Super_modifica == 0) $res = ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
			else{
				$id_solicitud = $this->input->post("id_solicitud");
				$id_estado_solicitud = $this->input->post("id_estado");
				$id_persona = $this->input->post("id_persona");
				$fecha_nacimiento = $this->input->post("fecha_nacimiento");
				$genero = $this->input->post("id_genero");
				$eps = $this->input->post("eps");
				$fecha_viaje = $this->input->post("fecha_viaje");
				$fecha_regreso = $this->input->post("fecha_regreso");
				$id_cobertura = $this->input->post("id_cobertura");
				$destino = $this->input->post("destino");
				$empresa = $this->input->post("empresa");
				$actividad = $this->input->post("actividad");
				$idioma = $this->input->post("idioma");
				$motivo = NULL;
				$str = $this->verificar_campos_string(['Fecha Viaje' => $fecha_viaje,'Fecha Regreso' => $fecha_regreso,'Cobertura' => $id_cobertura,'Destino' => $destino, 'Empresa'=> $empresa, 'Idioma'=> $idioma, 'Actividad' => $actividad,'Fecha de Nacimiento' => $fecha_nacimiento,'Genero' => $genero,'EPS' => $eps]);
				$sw = true;
				if (is_array($str)) {
					$campo = $str['field']; 						
					$res = ['mensaje' => "El campo $campo no puede estar vació.", 'tipo' => "info", 'titulo' => "Oops.!"];
					$sw = false;
				}else{
					$data_persona = array();
					$buscar = "p.id = $id_persona";
					$persona = $this->talento_humano_model->buscar_persona_arl($buscar);
					foreach ($persona as $row) {
						if(empty($row['fecha_nacimiento']) || $row['fecha_nacimiento'] != $fecha_nacimiento){
							$data_persona['fecha_nacimiento'] = $fecha_nacimiento;
						}
						if(empty($row['genero']) || $row['genero'] != $genero){
							$data_persona['genero'] = $genero;
						}
						if(empty($row['eps']) || $row['eps'] != $eps){
							$data_persona['eps'] = $eps;
						}
					}
					if(!empty($data_persona)){
						$mod_per = $this->talento_humano_model->modificar_datos($data_persona, "personas", $id_persona);
						if($mod_per != 0){
							$res = ['mensaje'=>"Error al actualizar información del funcionario, Contacte con el administrador.",'tipo'=>"info",'titulo'=> "Oops.!"];
							$sw = false;
						}else $sw = true;
					}
					$detalle = $this->talento_humano_model->get_detalle_solicitud_arl($id_solicitud, 'cobertura_arl_th', '');
					if($id_estado_solicitud === 'Tal_Pro'){	
						$motivo = $this->input->post("motivo");						
						$str_mot = $this->verificar_campos_string(['Motivo' => $motivo]);
						if (is_array($str_mot)) {		
							$res = ['mensaje' => "El campo ". $str_mot['field']." no puede estar vació.", 'tipo' => "info", 'titulo' => "Oops.!"];
							$sw = false;
						}

						if(!empty($data_persona) && ($detalle->{'tipo_cobertura'} == $id_cobertura) && ($detalle->{'fecha_viaje'} == $fecha_viaje) && ($detalle->{'fecha_regreso'} == $fecha_regreso) && ($detalle->{'actividad'} == $actividad) && ($detalle->{'empresa'} == $empresa) && ($detalle->{'ciudad'} == $destino) && ($detalle->{'idioma'} == $idioma) && (empty($data_persona)) ) {
							$res = ['mensaje'=>"Debe realizar alguna modificación en la solicitud.",'tipo'=>"info",'titulo'=> "Oops.!"];
							$sw = false;
						}else{
							if($detalle->{'motivo'} == $motivo) {
								$res = ['mensaje'=>"Debe realizar alguna modificación en la observación.",'tipo'=>"info",'titulo'=> "Oops.!"];
								$sw = false;
							}
						}
					}else{
						if(!empty($data_persona) && ($detalle->{'tipo_cobertura'} == $id_cobertura) && ($detalle->{'fecha_viaje'} == $fecha_viaje) && ($detalle->{'fecha_regreso'} == $fecha_regreso) && ($detalle->{'actividad'} == $actividad) && ($detalle->{'empresa'} == $empresa) && ($detalle->{'ciudad'} == $destino) && ($detalle->{'idioma'} == $idioma) && (empty($data_persona)) ) {
						$res = ['mensaje'=>"Debe realizar alguna modificación en la solicitud.",'tipo'=>"info",'titulo'=> "Oops.!"];
						$sw = false;
						}else{
							$val_fecha = $this->validar_fechas("Hum_Cob_Arl",$fecha_viaje,'Y-m-d',54);
							if (!$val_fecha['sw']) {
								$res = ['mensaje'=> "Su solicitud debe tener ".$val_fecha['dias_solicitud']."  dias de anticipacion, seleccione otra fecha de viaje válida.", 'tipo'=>"info", 'titulo'=> "Oops."];
								$sw = false;
							}
						}
					}

					if($sw){
						$data_solicitud = [    
							'id_persona'=>$id_persona,           
							'tipo_cobertura'=>$id_cobertura,
							'fecha_viaje'=> $fecha_viaje,
							'fecha_regreso'=>$fecha_regreso,
							'actividad'=>$actividad,
							'ciudad'=>$destino,
							'empresa'=>$empresa,
							'idioma'=>$idioma,
							'motivo'  => $motivo];
						$mod_sol = $this->talento_humano_model->modificar_datos($data_solicitud, 'cobertura_arl_th', $id_solicitud, 'id_solicitud');
						if ($mod_sol != 0) $res = ['mensaje'=>"Error al guardar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
						else $res = ['mensaje'=>"La solicitud fue gestionada exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
					}

				}	
			}
		}
		echo json_encode($res);
	}

	public function get_codigos_sap(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$res = $this->talento_humano_model->get_where('valor_parametro', ['idparametro' => 25, 'estado' => 1])->result_array();
		}
		echo json_encode($res);
	}

	public function guardar_solicitud_cert_ingresos(){
		if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$estado = "Tal_Env";
			$year = $this->input->post('anio_certificado');
			$contrato = $this->input->post('check_prestacion_servicio');
			$num = $this->verificar_campos_numericos(['Año del certificado'=>$year]);
			if (is_array($num)) {
				$campo = $num['field'];
				$resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
			} else {
				$data_sol = [
					"id_tipo_solicitud" => "Hum_Cir",
					"usuario_registro" => $_SESSION['persona'],
					"id_estado_solicitud" => $estado,
					"aux" => $year
				];
				$solicitud = $this->talento_humano_model->guardar_datos($data_sol, 'solicitudes_talento_hum');
				if($solicitud){
					$data_estado = [
						"solicitud_id" => $solicitud,
						"estado_id" => $estado,
						"usuario_id" => $_SESSION['persona']
					];
					$this->talento_humano_model->guardar_datos($data_estado, 'estados_solicitudes_talento');
					$data_certificado = [
						'solicitud_id' => $solicitud
					];
					if(!!$contrato){
						$data_certificado['especificaciones'] = 'ops';
					}
					$this->talento_humano_model->guardar_datos($data_certificado, 'certificados');
					$personas_notificar = $this->talento_humano_model->get_usuarios_a_notificar("Hum_Cir", $estado);
					$info_persona = $this->talento_humano_model->get_info_persona($_SESSION['persona']);
					array_push($personas_notificar, ['persona' => $info_persona->{'fullname'}, 'correo' => $info_persona->{'correo'}]);
					$resp = [
						'mensaje' => "Solicitud registrada exitosamente",
						'tipo' => "success",
						'titulo' => "Proceso Exitoso!",
						'personas_notificar' => $personas_notificar
					]; 
				} else {
					$resp = ['mensaje'=>"Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.",'tipo'=>"error",'titulo'=> "Ooops!"]; 
				}
			}
		}
		echo json_encode($resp);
	}

	public function get_usuarios_a_notificar_estado_posgrado() {
		$encargados = [];
		if (!$this->Super_estado) $encargados = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$departamento = $this->input->post('id_departamento');
			$estado = $this->input->post('id_estado');
			$encargados = $this->talento_humano_model->get_usuarios_a_notificar_estado_posgrado($estado, $departamento);
		}
		echo json_encode($encargados);
	}

	public function traer_programas_req() {
		$programas = [];
		if (!$this->Super_estado) $programas = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$tipo_programa = $this->input->post('tipo_programa');
			$data = ['id' => $tipo_programa];
			$tipo = $this->talento_humano_model->get_where('valor_parametro', $data)->row();
			$programas = $this->talento_humano_model->traer_programas_req($tipo->{'valor'});
		}
		echo json_encode($programas);
	}

	public function exportar_detalle_req_posgrado($id_solicitud){
        if ($this->Super_estado){
            $data = [];
            $sol = $this->talento_humano_model->get_solicitud($id_solicitud);
            $row = $this->talento_humano_model->detalle_requisicion_posgrado(
                $id_solicitud
            );
            array_push($data, [
                "id_solicitud" => $id_solicitud,
                "solicitante" => $sol->{'solicitante'},
                "estado" => $sol->{'state'},
                "fecha_solicitud" => $sol->{'fecha_registro'},
                "tipo_solicitud" => $sol->{'tipo_solicitud'},
                "tipo_vacante" => $row->{'tipo_vacante'},
                "candidato" => $row->{'candidato'},
                "reemplazo" => $row->{'reemplazado'},
                "departamento" => $row->{'departamento'},
                "tipo_programa" => $row->{'tipo_programa'},
                "programa" => $row->{'programa'},
                "nombre_modulo" => $row->{'nombre_modulo'},
                "horas_modulo" => $row->{'horas_modulo'},
                "numero_promocion" => $row->{'numero_promocion'},
                "cargo" => $row->{'cargo'},
                "ciudad_origen" => $row->{'ciudad_origen'},
                "valor_hora" => $row->{'valor_hora'},
                "fecha_inicio" => $row->{'fecha_inicio'},
                "fecha_terminacion" => $row->{'fecha_terminacion'},
                "tipo_orden" => $row->{'tipo_orden'},
                "codigo_sap" => $row->{'codigo_sap'},
                "observaciones" => $row->{'observacion'},
            ]);

            $info["datos"] = $data;
            $this->load->view("templates/exportar_req_posgrado", $info);
            return;
        }
        redirect("/", "refresh");
    }

    public function exportar_detalle_requisicion($id_solicitud)
    {
        if ($this->Super_estado) {
            $data = [];
            $sol = $this->talento_humano_model->get_solicitud($id_solicitud);
            $row = $this->talento_humano_model->get_detalle_vacante(
                $id_solicitud
            );
            array_push($data, [
                "id_solicitud" => $id_solicitud,
                "solicitante" => $sol->{'solicitante'},
                "estado" => $sol->{'state'},
                "fecha_solicitud" => $sol->{'fecha_registro'},
                "tipo_solicitud" => $sol->{'tipo_solicitud'},
                "id_tipo_solicitud" => $sol->{'id_tipo_solicitud'},
                "t_vacante" => $row["vacante"]->{'t_vacante'},
                "t_solicitud_vac" => $row["vacante"]->{'tipo_solicitud'},
                "t_solicitud" => $row["vacante"]->{'t_solicitud'},
                "cargo" => $row["vacante"]->{'cargo'},
                "departamento" => $row["vacante"]->{'departamento'},
                "observaciones" => $row["vacante"]->{'observaciones'},
                "programs" => $row["programs"],
                "horas" => $row["vacante"]->{'horas'},
                "plan_trabajo" => $row["vacante"]->{'plan_trabajo'},
                "subjects" => $row["subjects"],
                "pregrado" => $row["vacante"]->{'pregrado'},
                "posgrado" => $row["vacante"]->{'posgrado'},
                "reemplazado" => $row["vacante"]->{'fullname'},
                "linea_investigacion" =>
                    $row["vacante"]->{'linea_investigacion'},
                "anos_experiencia" => $row["vacante"]->{'anos_experiencia'},
                "nombre_cargo" => $row["vacante"]->{'nombre_cargo'},
                "experiencia_laboral" =>
                    $row["vacante"]->{'experiencia_laboral'},
                "nombre_tipo_contrato" =>
                    $row["vacante"]->{'nombre_tipo_contrato'},
                "duracion_contrato" => $row["vacante"]->{'duracion_contrato'},
                "vb_pedagogico" =>
                    $row["vacante"]->{'vb_pedagogico'} > 0
                        ? "Aprobado"
                        : "Desaprobado",
            ]);

            $info["datos"] = $data;
            $this->load->view("templates/exportar_requisicion", $info);
            return;
        }
        redirect("/", "refresh");
    }

    public function buscar_requisiciones()
    {
        echo json_encode(
            !$this->Super_estado
                ? ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""]
                : $this->talento_humano_model->buscar_requisiciones()
        );
    }

    public function buscar_competencias()
    {
        $competencias = [];
        if ($this->Super_estado) {
            $buscar = $this->input->post("dep");
            $competencias = $this->talento_humano_model->buscar_competencias(
                $buscar
            );
        }
        echo json_encode($competencias);
    }

    public function get_correo_jefe_inmediato()
    {
        if (!$this->Super_estado) {
            $info = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $id_solicitud = $this->input->post("id");
            $candidato = $this->input->post("candidato");
            $info = $this->talento_humano_model->get_correo_jefe_inmediato(
                $id_solicitud,
                $candidato
            );
        }
        echo json_encode($info);
    }

    public function get_correo_responsable_th()
    {
        if (!$this->Super_estado) {
            $info = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $id = $this->input->post("id");
            $info = $this->talento_humano_model->get_correo_responsable_th($id);
        }
        echo json_encode($info);
    }

    public function get_correo_jefe_th()
    {
        if (!$this->Super_estado) {
            $info = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $info = $this->talento_humano_model->get_correo_jefe_th();
        }
        echo json_encode($info);
    }

    public function guardar_vacaciones()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $id_persona = $_SESSION["persona"];
            $fecha_inicio = $this->input->post("fecha_inicio");
            $dias_solicitados = $this->input->post("dias_solicitados");
            $observaciones_ausentismo = $this->input->post(
                "observaciones_ausentismo"
            );
            $id_tipo_ausentismo = $this->input->post("id_tipo_ausentismo");
            $jefe_inmediato = $this->input->post("jefe_inmediato");
            $str = $this->verificar_campos_string([
                "fecha inicio" => $fecha_inicio,
                "" => $dias_solicitados,
            ]);
            if (is_array($str)) {
                $resp = [
                    "mensaje" =>
                        "El campo " . $str["field"] . "  no debe estar vacio.",
                    "tipo" => "info",
                    "titulo" => "Oops.!",
                ];
            } else {
                $val_fecha = $this->validar_fechas(
                    "Hum_Vac",
                    $fecha_inicio,
                    "Y-m-d",
                    54
                );
                $val_fecha_vac = $this->validar_fechas_vacaciones(
                    "Hum_Vac",
                    $fecha_inicio,
                    "Y-m-d",
                    54
                );
                if (!$val_fecha["sw"]) {
                    $resp = [
                        "mensaje" =>
                            "Su solicitud debe tener " .
                            $val_fecha["dias_solicitud"] .
                            "  dias de anticipacion, seleccione otra fecha de inicio válida.",
                        "tipo" => "info",
                        "titulo" => "Oops.",
                    ];
                } else {
                    $data_solicitud = [
                        "id_tipo_solicitud" => "Hum_Vac",
                        "jefe_inmediato" => $jefe_inmediato,
                        "usuario_registro" => $id_persona,
                    ];
                    $solicitud = $this->talento_humano_model->guardar_datos(
                        $data_solicitud,
                        "solicitudes_talento_hum"
                    );
                    if (!$solicitud) {
                        $resp = [
                            "mensaje" =>
                                "Error al crear la solicitud de vacaciones, Contacte con el administrador.",
                            "tipo" => "info",
                            "titulo" => "Oops.!",
                        ];
                    } else {
                        $info = $this->talento_humano_model->get_correo_jefe_inmediato(
                            $solicitud,
                            null
                        );
                        $data_correo = [
                            "id" => $solicitud,
                            "correo_jefe" => $info->{'correo'},
                            "nombre_jefe" => $info->{'fullname'},
                            "tipo_solicitud" => "Vacaciones",
                        ];
                        $resp = [
                            "mensaje" =>
                                "La solicitud fue guardada de forma exitosa.",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso.!",
                            "data" => $data_correo,
                        ];
                        $data_estado = [
                            "solicitud_id" => $solicitud,
                            "estado_id" => "Tal_Env",
                            "usuario_id" => $_SESSION["persona"],
                        ];
                        $estado_sol = $this->talento_humano_model->guardar_datos(
                            $data_estado,
                            "estados_solicitudes_talento"
                        );
                        $data = [
                            "fecha_inicio" => $fecha_inicio,
                            "dias_solicitados" => $dias_solicitados,
                            "observaciones" => $observaciones_ausentismo,
                            "id_solicitud" => $solicitud,
                            "id_tipo_ausentismo" => $id_tipo_ausentismo,
                            "usuario_registro" => $id_persona,
                        ];
                        $data_cob = $this->talento_humano_model->guardar_datos(
                            $data,
                            "solicitudes_ausentismo_vacaciones"
                        );
                        if (!$data_cob) {
                            $resp = [
                                "mensaje" =>
                                    "Error al guardar detalle de la solicitud, Contacte con el administrador.",
                                "tipo" => "info",
                                "titulo" => "Oops.!",
                            ];
                        }
                    }
                }
            }
        }

        echo json_encode($resp);
    }

    public function guardar_licencia()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $id_persona = $_SESSION["persona"];
            $fecha_inicio = $this->input->post("fecha_inicio");
            $dias_solicitados = $this->input->post("dias_solicitados");
            $observaciones = $this->input->post("observaciones");
            $motivo_licencia = $this->input->post("motivo_licencia");
            $id_tipo_ausentismo = $this->input->post("id_tipo_ausentismo");
            $jefe_inmediato = $this->input->post("jefe_inmediato");
            $tipo_licencia = $this->input->post("tipo_licencia");
            $str = $this->verificar_campos_string([
                "fecha inicio" => $fecha_inicio,
                "" => $dias_solicitados,
            ]);
            if (is_array($str)) {
                $resp = [
                    "mensaje" =>
                        "El campo " . $str["field"] . "  no debe estar vacio.",
                    "tipo" => "info",
                    "titulo" => "Oops.!",
                ];
            } else {
                $val_fecha = $this->validar_fechas(
                    "Hum_Lic",
                    $fecha_inicio,
                    "Y-m-d",
                    54
                );
                if (!$val_fecha["sw"]) {
                    $resp = [
                        "mensaje" =>
                            "Su solicitud debe tener " .
                            $val_fecha["dias_solicitud"] .
                            "  dias de anticipacion, seleccione otra fecha de inicio válida.",
                        "tipo" => "info",
                        "titulo" => "Oops.",
                    ];
                } else {
                    $data_solicitud = [
                        "id_tipo_solicitud" => "Hum_Lic",
                        "jefe_inmediato" => $jefe_inmediato,
                        "usuario_registro" => $id_persona,
                    ];
                    $solicitud = $this->talento_humano_model->guardar_datos(
                        $data_solicitud,
                        "solicitudes_talento_hum"
                    );
                    if (!$solicitud) {
                        $resp = [
                            "mensaje" =>
                                "Error al crear la solicitud de vacaciones, Contacte con el administrador.",
                            "tipo" => "info",
                            "titulo" => "Oops.!",
                        ];
                    } else {
                        $info = $this->talento_humano_model->get_correo_jefe_inmediato(
                            $solicitud,
                            null
                        );
                        $data_correo = [
                            "id" => $solicitud,
                            "correo_jefe" => $info->{'correo'},
                            "nombre_jefe" => $info->{'fullname'},
                            "tipo_solicitud" => "Licencias",
                        ];
                        $resp = [
                            "mensaje" =>
                                "La solicitud fue guardada de forma exitosa.",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso.!",
                            "data" => $data_correo,
                        ];
                        $file = $this->pages_model->cargar_archivo(
                            "archivo_adjunto",
                            $this->ruta_soporte_licencia,
                            "soporte_lic"
                        );
                        if ($file[0] == -1) {
                            $error = $file[1];
                            if (
                                $error ==
                                "<p>You did not select a file to upload.</p>"
                            ) {
                                $resp = [
                                    "mensaje" =>
                                        "Debe seleccionar una archivo de soporte",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                            } else {
                                $resp = [
                                    "mensaje" =>
                                        "Error al cargar archivo, contacte con el administrador.!",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                            }
                        } else {
                            $data_estado = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => "Tal_Env",
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            $estado_sol = $this->talento_humano_model->guardar_datos(
                                $data_estado,
                                "estados_solicitudes_talento"
                            );
                            $data = [
                                "fecha_inicio" => $fecha_inicio,
                                "tipo_licencia" => $tipo_licencia,
                                "motivo_licencia" => $motivo_licencia,
                                "dias_solicitados" => $dias_solicitados,
                                "observaciones" => $observaciones,
                                "id_solicitud" => $solicitud,
                                "id_tipo_ausentismo" => $id_tipo_ausentismo,
                                "nombre_adjunto" =>
                                    $_FILES["archivo_adjunto"]["name"],
                                "ruta_adjunto" => $file[1],
                                "usuario_registro" => $id_persona,
                            ];
                            $data_cob = $this->talento_humano_model->guardar_datos(
                                $data,
                                "solicitudes_ausentismo_licencia"
                            );
                            if (!$data_cob) {
                                $resp = [
                                    "mensaje" =>
                                        "Error al guardar detalle de la solicitud, Contacte con el administrador.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                            }
                        }
                    }
                }
            }
        }

        echo json_encode($resp);
    }

    public function detalles_ausentismo()
    {
        $data = [];
        if ($this->Super_estado) {
            $id_solicitud = $this->input->post("solicitud");
            $id_tipo_solicitud = $this->input->post("id_tipo_solicitud");
            if ($id_tipo_solicitud == "Hum_Vac") {
                $data = $this->talento_humano_model->get_info_ausentismo_vacaciones(
                    $id_solicitud
                );
            } else {
                $data = $this->talento_humano_model->get_info_ausentismo_licencia(
                    $id_solicitud
                );
            }
        }

        echo json_encode($data);
    }

    public function generar_reportes($tipo, $fecha_inicio = "", $fecha_fin = "")
    {
        $valores = [];
        $participantes = 0;
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $i = 0;
            $tipo_solicitud = $this->talento_humano_model
                ->get_where("valor_parametro", [
                    "id_aux" => $tipo,
                    "estado" => 1,
                ])
                ->row();
            $informe = "Reporte_" . $tipo_solicitud->{'valor'};
            switch ($tipo) {
                case "Hum_Prec":
                    $i = 8;
                    $sel = "(SELECT st1.fecha FROM estados_solicitudes_talento st1 WHERE sth.id=st1.solicitud_id and st1.estado_id='Tal_Esp' AND st1.estado=1 ORDER BY st1.fecha DESC LIMIT 1) VB_EDAGOGICO,
							(SELECT st2.fecha FROM estados_solicitudes_talento st2 WHERE sth.id=st2.solicitud_id and st2.estado_id='Tal_Env' AND st2.estado=1 ORDER BY st2.fecha DESC LIMIT 1) VB_DECANO,
							(SELECT st3.fecha FROM estados_solicitudes_talento st3 WHERE sth.id=st3.solicitud_id and st3.estado_id='Env_Csea' AND st3.estado=1 ORDER BY st3.fecha DESC LIMIT 1) VB_CSEA,
							(SELECT st4.fecha FROM estados_solicitudes_talento st4 WHERE sth.id=st4.solicitud_id and st4.estado_id='Tal_Ter' AND st4.estado=1 ORDER BY st4.fecha DESC LIMIT 1) TERMINADO";
                    break;
                case "Hum_Posg":
                    $i = 6;
                    $sel = "(SELECT st1.fecha FROM estados_solicitudes_talento st1 WHERE sth.id=st1.solicitud_id and st1.estado_id='Tal_Pro' AND st1.estado=1 ORDER BY st1.fecha DESC LIMIT 1) PROCESANDO,
							(SELECT st2.fecha FROM estados_solicitudes_talento st2 WHERE sth.id=st2.solicitud_id and st2.estado_id='Tal_Ter' AND st2.estado=1 ORDER BY st2.fecha DESC LIMIT 1) TERMINADO";
                    break;
					case "Hum_Vac":
						$i = 8;
						$sel = "(SELECT st1.fecha_inicio FROM solicitudes_ausentismo_vacaciones st1 WHERE sth.id=st1.id_solicitud AND st1.estado=1 ORDER BY st1.fecha_inicio DESC LIMIT 1) FECHA_INICIO,
						(SELECT st2.dias_solicitados FROM solicitudes_ausentismo_vacaciones st2 WHERE sth.id=st2.id_solicitud AND st2.estado=1 ORDER BY st2.fecha_inicio DESC LIMIT 1) DIAS_SOLICITADOS,
						(SELECT st3.observaciones FROM solicitudes_ausentismo_vacaciones st3 WHERE sth.id=st3.id_solicitud  AND st3.estado=1 ORDER BY st3.fecha_inicio DESC LIMIT 1) OBSERVACIONES,
						(SELECT st4.fecha FROM estados_solicitudes_talento st4 WHERE sth.id=st4.solicitud_id and st4.estado_id='Tal_Ter' AND st4.estado=1 ORDER BY st4.fecha DESC LIMIT 1) TERMINADO";
					break;
                default:
                    $i = 5;
                    $sel =
                        "(SELECT st.fecha FROM estados_solicitudes_talento st WHERE sth.id=st.solicitud_id and st.estado_id='Tal_Ter' AND st.estado=1 ORDER BY st.fecha DESC LIMIT 1) TERMINADO";
                    break;
            }
            $valores = $this->talento_humano_model->get_reporte(
                $tipo,
                $fecha_inicio,
                $fecha_fin,
                $sel
            );
            $participantes = count($valores);
            $datos["cantidad"] = $participantes;
            $datos["datos"] = $valores;
            $datos["nombre"] = $informe;
            $datos["leyenda"] = "";
            $datos["titulo"] = strtoupper($tipo_solicitud->{'valor'});
            $datos["version"] = "VERSIÓN: 09";
            $datos["trd"] = "TRD: 700-730-90";
            $datos["fecha"] = date("F") . " " . date("Y");
            $datos["col"] = $i;
            $this->load->view("templates/exportar_excel", $datos);
        }
    }

    public function vb_ausentismo()
    {
        $resp = [];
        if ($this->Super_estado) {
            $id_solicitud = $this->input->post("id");
            $id_tipo_solicitud = $this->input->post("id_tipo_solicitud");
            $vb = (int) $this->input->post("vb_ausentismo");
            $estado = $vb ? "Tal_Pro" : "Tal_Can";
            $num = $this->verificar_campos_numericos([
                "Solicitud" => $id_solicitud,
            ]);
            if (is_array($num)) {
                $resp = [
                    "mensaje" =>
                        "El campo " .
                        $num["field"] .
                        " debe ser numérico y no puede estar vació.",
                    "tipo" => "info",
                    "titulo" => "Oops.!",
                ];
            } else {
                $txt = $vb ? "aprobada" : "desaprobada";
                $mod_sol = $this->talento_humano_model->modificar_datos(
                    ["id_estado_solicitud" => $estado],
                    "solicitudes_talento_hum",
                    $id_solicitud
                );
                if (!$mod_sol) {
                    $resp = [
                        "mensaje" => "Solicitud $txt exitosamente",
                        "tipo" => "success",
                        "titulo" => "Proceso Exitoso!",
                    ];
                    $data = [
                        "solicitud_id" => $id_solicitud,
                        "estado_id" => $estado,
                        "usuario_id" => $_SESSION["persona"],
                    ];
                    $this->talento_humano_model->guardar_datos(
                        $data,
                        "estados_solicitudes_talento"
                    );
                } else {
                    $resp = [
                        "mensaje" =>
                            "Ha ocurrido un error al intentar gestionar la solicitud",
                        "tipo" => "error",
                        "titulo" => "Ooops!",
                    ];
                }
            }
        }
        echo json_encode($resp);
    }

    public function guardar_cambio_eps()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $sw = true;
            $estado = "Tal_Env";
            $direccion = $this->input->post("direccion");
            $barrio = $this->input->post("barrio");
            $ciudad = $this->input->post("ciudad");
            $telefono = $this->input->post("telefono");
            $email = $this->input->post("email");
            $eps_actual = $this->input->post("eps_actual");
            $eps_destino = $this->input->post("eps_destino");
            $text = $this->verificar_campos_string([
                "Dirección" => $direccion,
                "Barrio" => $barrio,
                "Ciudad" => $ciudad,
                "Correo Electronico" => $email,
                "Eps Actual" => $eps_actual,
                "Eps Destino" => $eps_destino,
            ]);
            $num = $this->verificar_campos_numericos(["Telefono" => $telefono]);
            if (is_array($num)) {
                $campo = $num["field"];
                $resp = [
                    "mensaje" => "El campo $campo debe ser numérico y no puede estar vació.",
                    "tipo" => "info",
                    "titulo" => "Oops.!",
                ];
            } elseif (is_array($text)) {
                $campo = $text["field"];
                $resp = [
                    "mensaje" => "El campo $campo debe ser numérico y no puede estar vació.",
                    "tipo" => "info",
                    "titulo" => "Oops.!",
                ];
            } else {
                $data_cert = [];
                $documentos_eps = "";
                $nombre = $_FILES["documentos_eps"]["name"];
                $arrayString = explode(".", $nombre);
                $extension = end($arrayString);
                if ($extension != "pdf") {
                    $resp = [
                        "mensaje" => "Debe adjuntar un archivo PDF.",
                        "tipo" => "info",
                        "titulo" => "Oops.!",
                    ];
                    $sw = false;
                } else {
                    $nombre = $_FILES["documentos_eps"]["name"];
                    $file = $this->cargar_archivo(
                        "documentos_eps",
                        $this->ruta_gestion,
                        "doc_cam_eps"
                    );
                    if ($file[0] == -1) {
                        $error = $file[1];
                        if (
                            $error ==
                            "<p>You did not select a file to upload.</p>"
                        ) {
                            $resp = [
                                "mensaje" =>"Debe adjuntar el documento de identidad.",
                                "tipo" => "info",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        } else {
                            $resp = [
                                "mensaje" =>
                                    "Error al cargar el documento de identidad.",
                                "tipo" => "error",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        }
                    }
                }

                if ($sw) {
                    $data_sol = [
                        "id_tipo_solicitud" => "Hum_Cam_Eps",
                        "usuario_registro" => $_SESSION["persona"],
                        "id_estado_solicitud" => $estado,
                        "eps_actual" => $eps_actual,
                        "eps_destino" => $eps_destino,
                    ];
                    $solicitud = $this->talento_humano_model->guardar_datos(
                        $data_sol,
                        "solicitudes_talento_hum"
                    );

                    $resp = [
                        "mensaje" => "Solicitud has sido guardada exitosamente",
                        "tipo" => "success",
                        "titulo" => "Proceso Exitoso!",
                        "id_solicitud" => $solicitud,
                    ];
                    if ($solicitud) {
                        $data_estado = [
                            "solicitud_id" => $solicitud,
                            "estado_id" => $estado,
                            "usuario_id" => $_SESSION["persona"],
                        ];
                        $this->talento_humano_model->guardar_datos(
                            $data_estado,
                            "estados_solicitudes_talento"
                        );
                        $correo_personal = $email;
                        $data_persona = [
                            "correo_personal" => $correo_personal,
                            "telefono" => $telefono,
                            "direccion" => $direccion,
                            "lugar_residencia" => $ciudad,
                            "barrio" => $barrio,
                        ];
                        $this->talento_humano_model->modificar_datos(
                            $data_persona,
                            "personas",
                            $_SESSION["persona"]
                        );
                        $documentos_eps = $file[1];
                        $data_cert = [
                            "id_solicitud" => $solicitud,
                            "nombre_real" => $nombre,
                            "nombre_archivo" => $documentos_eps,
                            "usuario_registra" => $_SESSION["persona"],
                        ];
                        $data_est = [
                            "solicitud_id" => $solicitud,
                            "estado_id" => $estado,
                            "usuario_id" => $_SESSION["persona"],
                        ];
                        if ($data_cert) {
                            $this->talento_humano_model->guardar_datos(
                                $data_cert,
                                "archivos_adj_th"
                            );
                        } else {
                            $resp = [
                                "mensaje" =>
                                    "Ha ocurrido un error al intentar gestionar la solicitud",
                                "tipo" => "error",
                                "titulo" => "Ooops!",
                            ];
                        }

                        $personas_notificar = $this->talento_humano_model->get_usuarios_a_notificar("Hum_Cir", $estado);
                    } else {
                        $resp = [
                            "mensaje" =>
                                "Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.",
                            "tipo" => "error",
                            "titulo" => "Ooops!",
                        ];
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function guardar_ben_eps()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $sw = true;
            $estado = "Tal_Env";
            $mayor = $this->input->post("check_documento");
            $tipo_beneficiario = $this->input->post("tipo_beneficiario");
            $tipo_documento = $this->input->post("tipo_documento");
            $documento = $this->input->post("documento");
            $direccion = $this->input->post("direccion");
            $barrio = $this->input->post("barrio");
            $ciudad = $this->input->post("ciudad");
            $telefono = $this->input->post("telefono");
            $email = $this->input->post("email");
            $text = $this->verificar_campos_string([
                "Dirección" => $direccion,
                "Barrio" => $barrio,
                "Ciudad" => $ciudad,
                "Correo Electronico" => $email,
                "Tipo Beneficiario" => $tipo_beneficiario,
                "Tipo Documento" => $tipo_documento,
            ]);
            $num = $this->verificar_campos_numericos([
                "Telefono" => $telefono,
                "Documento" => $documento,
            ]);
            if (is_array($num)) {
                $campo = $num["field"];
                $resp = [
                    "mensaje" => "El campo $campo debe ser numérico y no puede estar vacio.",
                    "tipo" => "info",
                    "titulo" => "Oops.!",
                ];
                $sw = false;
            } elseif (is_array($text)) {
                $campo = $text["field"];
                $resp = [
                    "mensaje" => "El campo $campo debe ser numérico y no puede estar vacio.",
                    "tipo" => "info",
                    "titulo" => "Oops.!",
                ];
                $sw = false;
            } else {
                if ($mayor){
                    $data_cest = [];
                    $certificado_estudio = "";
                    $nombre2 = $_FILES["certificado_estudio"]["name"];
                    $arrayString2 = explode(".", $nombre2);
                    $extension2 = end($arrayString2);
                    $data_reg = [];
                    $registro_civil = "";
                    $nombre1 = $_FILES["registro_civil"]["name"];
                    $arrayString1 = explode(".", $nombre1);
                    $extension1 = end($arrayString1);
                    $data_cert = [];
                    $documentos_eps = "";
                    $nombre = $_FILES["documentos_eps"]["name"];
                    $arrayString = explode(".", $nombre);
                    $extension = end($arrayString);
                    if ($extension != "pdf" || $extension1 != "pdf"|| $extension2 != "pdf") {
                        $resp = [
                            "mensaje" => "Debe adjuntar un archivo PDF.",
                            "tipo" => "info",
                            "titulo" => "Oops.!",
                        ];
                        $sw = false;
                    }else {
                        $nombre = $_FILES["documentos_eps"]["name"];
                        $nombre1 = $_FILES["registro_civil"]["name"];
                        $nombre2 = $_FILES["certificado_estudio"]["name"];
                        $file2 = $this->cargar_archivo(
                            "certificado_estudio",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        $file1 = $this->cargar_archivo(
                            "registro_civil",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        $file = $this->cargar_archivo(
                            "documentos_eps",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        if ($file[0] == -1 || $file1[0] == -1 || $file2[0] == -1) {
                            $error = $file[1];
                            $error1 = $file1[1];
                            $error2 = $file2[1];
                            if ($error == "<p>You did not select a file to upload.</p>" || $error1 == "<p>You did not select a file to upload.</p>" || $error2 == "<p>You did not select a file to upload.</p>") {
                                $resp = [
                                    "mensaje" => "Debe adjuntar el documento requerido.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }  else {
                                $resp = [
                                    "mensaje" =>
                                        "Error al cargar el documento de identidad.",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }
                        }
                    }
    
                    if ($sw) {
                        $data_sol = [
                            "id_tipo_solicitud" => "Hum_Inc_Eps",
                            "usuario_registro" => $_SESSION["persona"],
                            "id_estado_solicitud" => $estado,
                        ];
                        $solicitud = $this->talento_humano_model->guardar_datos(
                            $data_sol,
                            "solicitudes_talento_hum"
                        );
                        $resp = [
                            "mensaje" => "Solicitud has sido guardada exitosamente",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso!",
                            "id_solicitud" => $solicitud,
                        ];
                        if ($solicitud) {
                            $data_estado = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_estado,
                                "estados_solicitudes_talento"
                            );
                            $data_persona = [
                                "id_solicitud" => $solicitud,
                                "tipo_beneficiario" => $tipo_beneficiario,
                                "tipo_identificacion" => $tipo_documento,
                                "identificacion" => $documento,
                                "correo_ben" => $email,
                                "telefono" => $telefono,
                                "direccion" => $direccion,
                                "lugar_residencia" => $ciudad,
                                "barrio" => $barrio,
                                "id_usuario_registra" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_persona,
                                "talento_humano_inc_bene"
                            );
                            $documentos_eps = $file[1];
                            $data_cert = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre,
                                "nombre_archivo" => $documentos_eps,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $registro_civil = $file1[1];
                            $data_reg = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre1,
                                "nombre_archivo" => $registro_civil,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $certificado_estudio = $file2[1];
                            $data_cest = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre2,
                                "nombre_archivo" => $certificado_estudio,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $data_est = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            if ($data_cert && $data_reg && $data_cest) {
                                $this->talento_humano_model->guardar_datos(
                                    $data_cert,
                                    "archivos_adj_th"
                                );
                                $this->talento_humano_model->guardar_datos(
                                    $data_cest,
                                    "archivos_adj_th"
                                );
                                $this->talento_humano_model->guardar_datos(
                                    $data_reg,
                                    "archivos_adj_th"
                                );
                            } else {
                                $resp = [
                                    "mensaje" =>"Ha ocurrido un error al intentar gestionar la solicitud", "tipo" => "error", "titulo" => "Ooops!",
                                ];
                            }
                        } else {
                            $resp = [
                                "mensaje" =>"Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.","tipo" => "error", "titulo" => "Ooops!",
                            ];
                        }
                    }
                   
                }elseif ($tipo_beneficiario === "par_hi" && $tipo_documento == 'TI' ) {
                    $data_reg = [];
                    $registro_civil = "";
                    $nombre1 = $_FILES["registro_civil"]["name"];
                    $arrayString1 = explode(".", $nombre1);
                    $extension1 = end($arrayString1);
                    $data_cert = [];
                    $documentos_eps = "";
                    $nombre = $_FILES["documentos_eps"]["name"];
                    $arrayString = explode(".", $nombre);
                    $extension = end($arrayString);
                    if ($extension != "pdf" || $extension1 != "pdf") {
                        $resp = [
                            "mensaje" => "Debe adjuntar un archivo PDF.",
                            "tipo" => "info",
                            "titulo" => "Oops.!",
                        ];
                        $sw = false;
                    } else {
                        $nombre = $_FILES["documentos_eps"]["name"];
                        $nombre1 = $_FILES["registro_civil"]["name"];
                        $file1 = $this->cargar_archivo(
                            "registro_civil",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        $file = $this->cargar_archivo(
                            "documentos_eps",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        if ($file[0] == -1 || $file1[0] == -1) {
                            $error = $file[1];
                            $error1 = $file1[1];
                            if ($error == "<p>You did not select a file to upload.</p>" || $error1 == "<p>You did not select a file to upload.</p>") {
                                $resp = [
                                    "mensaje" => "Debe adjuntar el documento de identidad.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }  else {
                                $resp = [
                                    "mensaje" =>
                                        "Error al cargar el documento de identidad.",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }
                        }
                    }
    
                    if ($sw) {
                        $data_sol = [
                            "id_tipo_solicitud" => "Hum_Inc_Eps",
                            "usuario_registro" => $_SESSION["persona"],
                            "id_estado_solicitud" => $estado,
                        ];
                        $solicitud = $this->talento_humano_model->guardar_datos(
                            $data_sol,
                            "solicitudes_talento_hum"
                        );
                        $resp = [
                            "mensaje" => "Solicitud has sido guardada exitosamente",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso!",
                            "id_solicitud" => $solicitud,
                        ];
                        if ($solicitud) {
                            $data_estado = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_estado,
                                "estados_solicitudes_talento"
                            );
                            $data_persona = [
                                "id_solicitud" => $solicitud,
                                "tipo_beneficiario" => $tipo_beneficiario,
                                "tipo_identificacion" => $tipo_documento,
                                "identificacion" => $documento,
                                "correo_ben" => $email,
                                "telefono" => $telefono,
                                "direccion" => $direccion,
                                "lugar_residencia" => $ciudad,
                                "barrio" => $barrio,
                                "id_usuario_registra" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_persona,
                                "talento_humano_inc_bene"
                            );
                            $documentos_eps = $file[1];
                            $data_cert = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre,
                                "nombre_archivo" => $documentos_eps,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $registro_civil = $file1[1];
                            $data_reg = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre1,
                                "nombre_archivo" => $registro_civil,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $data_est = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            if ($data_cert && $data_reg) {
                                $this->talento_humano_model->guardar_datos(
                                    $data_cert,
                                    "archivos_adj_th"
                                );
                                $this->talento_humano_model->guardar_datos(
                                    $data_reg,
                                    "archivos_adj_th"
                                );
                            } else {
                                $resp = [
                                    "mensaje" =>"Ha ocurrido un error al intentar gestionar la solicitud", "tipo" => "error", "titulo" => "Ooops!",
                                ];
                            }
                        } else {
                            $resp = [
                                "mensaje" =>"Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.","tipo" => "error", "titulo" => "Ooops!",
                            ];
                        }
                    }
                   
                } elseif ($tipo_beneficiario == "par_con" ){
                    $data_cert = [];
                    $documentos_eps = "";
                    $nombre = $_FILES["documentos_eps"]["name"];
                    $arrayString = explode(".", $nombre);
                    $extension = end($arrayString);
                    if ($extension != "pdf") {
                        $resp = [
                            "mensaje" => "Debe adjuntar un archivo PDF.",
                            "tipo" => "info",
                            "titulo" => "Oops.!",
                        ];
                        $sw = false;
                    } else {
                        $nombre = $_FILES["documentos_eps"]["name"];
                        $file = $this->cargar_archivo(
                            "documentos_eps",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        if ($file[0] == -1) {
                            $error = $file[1];
                            if (
                                $error ==
                                "<p>You did not select a file to upload.</p>"
                            ) {
                                $resp = [
                                    "mensaje" => "Debe adjuntar el documento de identidad.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            } else {
                                $resp = [
                                    "mensaje" =>
                                        "Error al cargar el documento de identidad.",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }
                        }
                    }
    
                    if ($sw) {
                        $data_sol = [
                            "id_tipo_solicitud" => "Hum_Inc_Eps",
                            "usuario_registro" => $_SESSION["persona"],
                            "id_estado_solicitud" => $estado,
                        ];
                        $solicitud = $this->talento_humano_model->guardar_datos(
                            $data_sol,
                            "solicitudes_talento_hum"
                        );
                        $resp = [
                            "mensaje" => "Solicitud has sido guardada exitosamente",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso!",
                            "certificado" => $documentos_eps,
                        ];
                        if ($solicitud) {
                            $data_estado = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_estado,
                                "estados_solicitudes_talento"
                            );
                            $data_persona = [
                                "id_solicitud" => $solicitud,
                                "tipo_beneficiario" => $tipo_beneficiario,
                                "tipo_identificacion" => $tipo_documento,
                                "identificacion" => $documento,
                                "correo_ben" => $email,
                                "telefono" => $telefono,
                                "direccion" => $direccion,
                                "lugar_residencia" => $ciudad,
                                "barrio" => $barrio,
                                "id_usuario_registra" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_persona,
                                "talento_humano_inc_bene"
                            );
                            $documentos_eps = $file[1];
                            $data_cert = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre,
                                "nombre_archivo" => $documentos_eps,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $data_est = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            if ($data_cert) {
                                $this->talento_humano_model->guardar_datos(
                                    $data_cert,
                                    "archivos_adj_th"
                                );
                            } else {
                                $resp = [
                                    "mensaje" =>"Ha ocurrido un error al intentar gestionar la solicitud", "tipo" => "error", "titulo" => "Ooops!",
                                ];
                            }
                        } else {
                            $resp = [
                                "mensaje" =>"Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.","tipo" => "error", "titulo" => "Ooops!",
                            ];
                        }
                    }
                } elseif ($tipo_beneficiario === "par_hi"){
                     $data_cert = [];
                    $registro_civil = "";
                    $nombre = $_FILES["registro_civil"]["name"];
                    $arrayString = explode(".", $nombre);
                    $extension = end($arrayString);
                    if ($extension != "pdf") {
                        $resp = [
                            "mensaje" => "Debe adjuntar un archivo PDF.",
                            "tipo" => "info",
                            "titulo" => "Oops.!",
                        ];
                        $sw = false;
                    } else {
                        $nombre = $_FILES["registro_civil"]["name"];
                        $file = $this->cargar_archivo(
                            "registro_civil",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        if ($file[0] == -1) {
                            $error = $file[1];
                            if (
                                $error ==
                                "<p>You did not select a file to upload.</p>"
                            ) {
                                $resp = [
                                    "mensaje" => "Debe adjuntar el documento de identidad.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            } else {
                                $resp = [
                                    "mensaje" =>
                                        "Error al cargar el documento de identidad.",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }
                        }
                    }
    
                    if ($sw) {
                        $data_sol = [
                            "id_tipo_solicitud" => "Hum_Inc_Eps",
                            "usuario_registro" => $_SESSION["persona"],
                            "id_estado_solicitud" => $estado,
                        ];
                        $solicitud = $this->talento_humano_model->guardar_datos(
                            $data_sol,
                            "solicitudes_talento_hum"
                        );
                        $resp = [
                            "mensaje" => "Solicitud has sido guardada exitosamente",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso!",
                            "id_solicitud" => $solicitud,
                             ];
                        if ($solicitud) {
                            $data_estado = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_estado,
                                "estados_solicitudes_talento"
                            );
                            $data_persona = [
                                "id_solicitud" => $solicitud,
                                "tipo_beneficiario" => $tipo_beneficiario,
                                "tipo_identificacion" => $tipo_documento,
                                "identificacion" => $documento,
                                "correo_ben" => $email,
                                "telefono" => $telefono,
                                "direccion" => $direccion,
                                "lugar_residencia" => $ciudad,
                                "barrio" => $barrio,
                                "id_usuario_registra" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_persona,
                                "talento_humano_inc_bene"
                            );
                            $registro_civil = $file[1];
                            $data_cert = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre,
                                "nombre_archivo" => $registro_civil,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $data_est = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            if ($data_cert) {
                                $this->talento_humano_model->guardar_datos(
                                    $data_cert,
                                    "archivos_adj_th"
                                );
                            } else {
                                $resp = [
                                    "mensaje" =>"Ha ocurrido un error al intentar gestionar la solicitud", "tipo" => "error", "titulo" => "Ooops!",
                                ];
                            }
                        } else {
                            $resp = [
                                "mensaje" =>"Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.","tipo" => "error", "titulo" => "Ooops!",
                            ];
                        }
                    }
                    
                } elseif ($tipo_beneficiario === "par_pad"){
                    $data_reg = [];
                    $registro_civilp = "";
                    $nombre1 = $_FILES["registro_civilp"]["name"];
                    $arrayString1 = explode(".", $nombre1);
                    $extension1 = end($arrayString1);
                    $data_cert = [];
                    $documentos_eps = "";
                    $nombre = $_FILES["documentos_eps"]["name"];
                    $arrayString = explode(".", $nombre);
                    $extension = end($arrayString);
                    if ($extension != "pdf" || $extension1 != "pdf") {
                        $resp = [
                            "mensaje" => "Debe adjuntar un archivo PDF.",
                            "tipo" => "info",
                            "titulo" => "Oops.!",
                        ];
                        $sw = false;
                    } else {
                        $nombre = $_FILES["documentos_eps"]["name"];
                        $nombre1 = $_FILES["registro_civilp"]["name"];
                        $file1 = $this->cargar_archivo(
                            "registro_civilp",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        $file = $this->cargar_archivo(
                            "documentos_eps",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        if ($file[0] == -1 || $file1[0] == -1) {
                            $error = $file[1];
                            $error1 = $file1[1];
                            if ($error == "<p>You did not select a file to upload.</p>" || $error1 == "<p>You did not select a file to upload.</p>") {
                                $resp = [
                                    "mensaje" => "Debe adjuntar el documento de identidad.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }  else {
                                $resp = [
                                    "mensaje" =>
                                        "Error al cargar el documento de identidad.",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }
                        }
                    }
    
                    if ($sw) {
                        $data_sol = [
                            "id_tipo_solicitud" => "Hum_Inc_Eps",
                            "usuario_registro" => $_SESSION["persona"],
                            "id_estado_solicitud" => $estado,
                        ];
                        $solicitud = $this->talento_humano_model->guardar_datos(
                            $data_sol,
                            "solicitudes_talento_hum"
                        );
                        $resp = [
                            "mensaje" => "Solicitud has sido guardada exitosamente",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso!",
                            "id_solicitud" => $solicitud,
                           ];
                        if ($solicitud) {
                            $data_estado = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_estado,
                                "estados_solicitudes_talento"
                            );
                            $data_persona = [
                                "id_solicitud" => $solicitud,
                                "tipo_beneficiario" => $tipo_beneficiario,
                                "tipo_identificacion" => $tipo_documento,
                                "identificacion" => $documento,
                                "correo_ben" => $email,
                                "telefono" => $telefono,
                                "direccion" => $direccion,
                                "lugar_residencia" => $ciudad,
                                "barrio" => $barrio,
                                "id_usuario_registra" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_persona,
                                "talento_humano_inc_bene"
                            );
                            $documentos_eps = $file[1];
                            $data_cert = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre,
                                "nombre_archivo" => $documentos_eps,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $registro_civilp = $file1[1];
                            $data_reg = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre1,
                                "nombre_archivo" => $registro_civilp,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $data_est = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            if ($data_cert && $data_reg) {
                                $this->talento_humano_model->guardar_datos(
                                    $data_cert,
                                    "archivos_adj_th"
                                );
                                $this->talento_humano_model->guardar_datos(
                                    $data_reg,
                                    "archivos_adj_th"
                                );
                            } else {
                                $resp = [
                                    "mensaje" =>"Ha ocurrido un error al intentar gestionar la solicitud", "tipo" => "error", "titulo" => "Ooops!",
                                ];
                            }
                        } else {
                            $resp = [
                                "mensaje" =>"Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.","tipo" => "error", "titulo" => "Ooops!",
                            ];
                        }
                    }
                   
                }else{
                    $resp = [
                        "mensaje" => "No ha seleccionado un tipo de beneficiario",
                        "tipo" => "info",
                        "titulo" => "Oops.!",
                    ];
                }
            }
        }
         echo json_encode($resp);
    }

    public function guardar_ben_caja()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $sw = true;
            $estado = "Tal_Env";
            $edad = $this->input->post("check_edad");
            $tipo_beneficiario = $this->input->post("tipo_beneficiario");
            $tipo_documento = $this->input->post("tipo_documento");
            $documento = $this->input->post("documento");
            $direccion = $this->input->post("direccion");
            $barrio = $this->input->post("barrio");
            $ciudad = $this->input->post("ciudad");
            $telefono = $this->input->post("telefono");
            $email = $this->input->post("email");
            $text = $this->verificar_campos_string([
                "Dirección" => $direccion,
                "Barrio" => $barrio,
                "Ciudad" => $ciudad,
                "Correo Electronico" => $email,
                "Tipo Beneficiario" => $tipo_beneficiario,
                "Tipo Documento" => $tipo_documento,
            ]);
            $num = $this->verificar_campos_numericos([
                "Telefono" => $telefono,
                "Documento" => $documento,
            ]);
            if (is_array($num)) {
                $campo = $num["field"];
                $resp = [
                    "mensaje" => "El campo $campo debe ser numérico y no puede estar vacio.",
                    "tipo" => "info",
                    "titulo" => "Oops.!",
                ];
                $sw = false;
            } elseif (is_array($text)) {
                $campo = $text["field"];
                $resp = [
                    "mensaje" => "El campo $campo debe ser numérico y no puede estar vacio.",
                    "tipo" => "info",
                    "titulo" => "Oops.!",
                ];
                $sw = false;
            } else {
                if ($edad) {
                    $data_cest = [];
                    $certificado_estudio = "";
                    $nombre2 = $_FILES["certificado_estudio"]["name"];
                    $arrayString2 = explode(".", $nombre2);
                    $extension2 = end($arrayString2);
                    $data_cert = [];
                    $documentos_eps = "";
                    $nombre = $_FILES["documentos_eps"]["name"];
                    $arrayString = explode(".", $nombre);
                    $extension = end($arrayString);
                    if ($extension != "pdf" || $extension2 != "pdf") {
                        $resp = [
                            "mensaje" => "Debe adjuntar un archivo PDF.",
                            "tipo" => "info",
                            "titulo" => "Oops.!",
                        ];
                        $sw = false;
                    } else {
                        $nombre = $_FILES["documentos_eps"]["name"];
                        $nombre2 = $_FILES["certificado_estudio"]["name"];
                        $file2 = $this->cargar_archivo(
                            "certificado_estudio",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        $file = $this->cargar_archivo(
                            "documentos_eps",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        if ($file[0] == -1 || $file2[0] == -1) {
                            $error = $file[1];
                            $error2 = $file2[1];
                            if ($error == "<p>You did not select a file to upload.</p>"  || $error2 == "<p>You did not select a file to upload.</p>") {
                                $resp = [
                                    "mensaje" => "Debe adjuntar el documento requerido.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }  else {
                                $resp = [
                                    "mensaje" =>
                                        "Error al cargar el documento de identidad.",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }
                        }
                    }
    
                    if ($sw) {
                        $data_sol = [
                            "id_tipo_solicitud" => "Hum_Inc_Caja",
                            "usuario_registro" => $_SESSION["persona"],
                            "id_estado_solicitud" => $estado,
                        ];
                        $solicitud = $this->talento_humano_model->guardar_datos(
                            $data_sol,
                            "solicitudes_talento_hum"
                        );
                        $resp = [
                            "mensaje" => "Solicitud has sido guardada exitosamente",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso!",
                            "id_solicitud" => $solicitud,
                        ];
                        if ($solicitud) {
                            $data_estado = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_estado,
                                "estados_solicitudes_talento"
                            );
                            $data_persona = [
                                "id_solicitud" => $solicitud,
                                "tipo_beneficiario" => $tipo_beneficiario,
                                "tipo_identificacion" => $tipo_documento,
                                "identificacion" => $documento,
                                "correo_ben" => $email,
                                "telefono" => $telefono,
                                "direccion" => $direccion,
                                "lugar_residencia" => $ciudad,
                                "barrio" => $barrio,
                                "id_usuario_registra" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_persona,
                                "talento_humano_inc_bene"
                            );
                            $documentos_eps = $file[1];
                            $data_cert = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre,
                                "nombre_archivo" => $documentos_eps,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $certificado_estudio = $file2[1];
                            $data_cest = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre2,
                                "nombre_archivo" => $certificado_estudio,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $data_est = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            if ($data_cert && $data_cest) {
                                $this->talento_humano_model->guardar_datos(
                                    $data_cert,
                                    "archivos_adj_th"
                                );
                                $this->talento_humano_model->guardar_datos(
                                    $data_cest,
                                    "archivos_adj_th"
                                );
                            } else {
                                $resp = [
                                    "mensaje" =>"Ha ocurrido un error al intentar gestionar la solicitud", "tipo" => "error", "titulo" => "Ooops!",
                                ];
                            }
                        } else {
                            $resp = [
                                "mensaje" =>"Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.","tipo" => "error", "titulo" => "Ooops!",
                            ];
                        }
                    }
                   
                } elseif ($tipo_beneficiario == "par_con" ){
                    $data_con = [];
                    $convivencia = "";
                    $nombre1 = $_FILES["convivencia"]["name"];
                    $arrayString1 = explode(".", $nombre1);
                    $extension1 = end($arrayString1);
                    $data_cert = [];
                    $documentos_eps = "";
                    $nombre = $_FILES["documentos_eps"]["name"];
                    $arrayString = explode(".", $nombre);
                    $extension = end($arrayString);
                    if ($extension != "pdf" || $extension1 != "pdf") {
                        $resp = [
                            "mensaje" => "Debe adjuntar un archivo PDF.",
                            "tipo" => "info",
                            "titulo" => "Oops.!",
                        ];
                        $sw = false;
                    } else {
                        $nombre = $_FILES["documentos_eps"]["name"];
                        $nombre1 = $_FILES["convivencia"]["name"];
                        $file1 = $this->cargar_archivo(
                            "convivencia",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        $file = $this->cargar_archivo(
                            "documentos_eps",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        if ($file[0] == -1 || $file1[0] == -1) {
                            $error = $file[1];
                            $error1 = $file1[1];
                            if ($error == "<p>You did not select a file to upload.</p>" || $error1 == "<p>You did not select a file to upload.</p>") {
                                $resp = [
                                    "mensaje" => "Debe adjuntar el documento de identidad.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }  else {
                                $resp = [
                                    "mensaje" =>
                                        "Error al cargar el documento de identidad.",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }
                        }
                    }
    
                    if ($sw) {
                        $data_sol = [
                            "id_tipo_solicitud" => "Hum_Inc_Caja",
                            "usuario_registro" => $_SESSION["persona"],
                            "id_estado_solicitud" => $estado,
                        ];
                        $solicitud = $this->talento_humano_model->guardar_datos(
                            $data_sol,
                            "solicitudes_talento_hum"
                        );
                        $resp = [
                            "mensaje" => "Solicitud has sido guardada exitosamente",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso!",
                            "id_solicitud" => $solicitud,
                        ];
                        if ($solicitud) {
                            $data_estado = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_estado,
                                "estados_solicitudes_talento"
                            );
                            $data_persona = [
                                "id_solicitud" => $solicitud,
                                "tipo_beneficiario" => $tipo_beneficiario,
                                "tipo_identificacion" => $tipo_documento,
                                "identificacion" => $documento,
                                "correo_ben" => $email,
                                "telefono" => $telefono,
                                "direccion" => $direccion,
                                "lugar_residencia" => $ciudad,
                                "barrio" => $barrio,
                                "id_usuario_registra" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_persona,
                                "talento_humano_inc_bene"
                            );
                            $documentos_eps = $file[1];
                            $data_cert = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre,
                                "nombre_archivo" => $documentos_eps,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $convivencia = $file1[1];
                            $data_con = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre1,
                                "nombre_archivo" => $convivencia,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $data_est = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            if ($data_cert && $data_con) {
                                $this->talento_humano_model->guardar_datos(
                                    $data_cert,
                                    "archivos_adj_th"
                                );
                                $this->talento_humano_model->guardar_datos(
                                    $data_con,
                                    "archivos_adj_th"
                                );
                            } else {
                                $resp = [
                                    "mensaje" =>"Ha ocurrido un error al intentar gestionar la solicitud", "tipo" => "error", "titulo" => "Ooops!",
                                ];
                            }
                        } else {
                            $resp = [
                                "mensaje" =>"Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.","tipo" => "error", "titulo" => "Ooops!",
                            ];
                        }
                    }
                } elseif ($tipo_beneficiario === "par_pad"){
                    $data_con = [];
                    $convivenciap = "";
                    $nombre2 = $_FILES["convivenciap"]["name"];
                    $arrayString2 = explode(".", $nombre2);
                    $extension2 = end($arrayString2);
                    $data_reg = [];
                    $registro_civilp = "";
                    $nombre1 = $_FILES["registro_civilp"]["name"];
                    $arrayString1 = explode(".", $nombre1);
                    $extension1 = end($arrayString1);
                    $data_cert = [];
                    $documentos_eps = "";
                    $nombre = $_FILES["documentos_eps"]["name"];
                    $arrayString = explode(".", $nombre);
                    $extension = end($arrayString);
                    if ($extension != "pdf" || $extension1 != "pdf"|| $extension2 != "pdf") {
                        $resp = [
                            "mensaje" => "Debe adjuntar un archivo PDF.",
                            "tipo" => "info",
                            "titulo" => "Oops.!",
                        ];
                        $sw = false;
                    } else {
                        $nombre = $_FILES["documentos_eps"]["name"];
                        $nombre1 = $_FILES["registro_civilp"]["name"];
                        $nombre2 = $_FILES["convivenciap"]["name"];
                        $file1 = $this->cargar_archivo(
                            "registro_civilp",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        $file2 = $this->cargar_archivo(
                            "convivenciap",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        $file = $this->cargar_archivo(
                            "documentos_eps",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        if ($file[0] == -1 || $file1[0] == -1 || $file2[0] == -1) {
                            $error = $file[1];
                            $error1 = $file1[1];
                            $error2 = $file2[1];
                            if ($error == "<p>You did not select a file to upload.</p>" || $error1 == "<p>You did not select a file to upload.</p>" || $error2 == "<p>You did not select a file to upload.</p>") {
                                $resp = [
                                    "mensaje" => "Debe adjuntar el documento requeridp.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }  else {
                                $resp = [
                                    "mensaje" =>
                                    "Error al cargar el documento.",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }
                        }
                    }
    
                    if ($sw) {
                        $data_sol = [
                            "id_tipo_solicitud" => "Hum_Inc_Caja",
                            "usuario_registro" => $_SESSION["persona"],
                            "id_estado_solicitud" => $estado,
                        ];
                        $solicitud = $this->talento_humano_model->guardar_datos(
                            $data_sol,
                            "solicitudes_talento_hum"
                        );
                        $resp = [
                            "mensaje" => "Solicitud has sido guardada exitosamente",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso!",
                            "id_solicitud" => $solicitud,
                        ];
                        if ($solicitud) {
                            $data_estado = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_estado,
                                "estados_solicitudes_talento"
                            );
                            $data_persona = [
                                "id_solicitud" => $solicitud,
                                "tipo_beneficiario" => $tipo_beneficiario,
                                "tipo_identificacion" => $tipo_documento,
                                "identificacion" => $documento,
                                "correo_ben" => $email,
                                "telefono" => $telefono,
                                "direccion" => $direccion,
                                "lugar_residencia" => $ciudad,
                                "barrio" => $barrio,
                                "id_usuario_registra" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_persona,
                                "talento_humano_inc_bene"
                            );
                            $documentos_eps = $file[1];
                            $data_cert = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre,
                                "nombre_archivo" => $documentos_eps,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $registro_civilp = $file1[1];
                            $data_reg = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre1,
                                "nombre_archivo" => $registro_civilp,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $convivenciap = $file2[1];
                            $data_con = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre2,
                                "nombre_archivo" => $convivenciap,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $data_est = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            if ($data_cert && $data_reg && $data_con) {
                                $this->talento_humano_model->guardar_datos(
                                    $data_cert,
                                    "archivos_adj_th"
                                );
                                $this->talento_humano_model->guardar_datos(
                                    $data_con,
                                    "archivos_adj_th"
                                );
                                $this->talento_humano_model->guardar_datos(
                                    $data_reg,
                                    "archivos_adj_th"
                                );
                            } else {
                                $resp = [
                                    "mensaje" =>"Ha ocurrido un error al intentar gestionar la solicitud", "tipo" => "error", "titulo" => "Ooops!",
                                ];
                            }
                        } else {
                            $resp = [
                                "mensaje" =>"Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.","tipo" => "error", "titulo" => "Ooops!",
                            ];
                        }
                    }
                   
                }elseif ($tipo_beneficiario === "par_hi"){
                    $data_cert = [];
                    $documentos_eps = "";
                    $nombre = $_FILES["documentos_eps"]["name"];
                    $arrayString = explode(".", $nombre);
                    $extension = end($arrayString);
                    if ($extension != "pdf") {
                        $resp = [
                            "mensaje" => "Debe adjuntar un archivo PDF.",
                            "tipo" => "info",
                            "titulo" => "Oops.!",
                        ];
                        $sw = false;
                    } else {
                        $nombre = $_FILES["documentos_eps"]["name"];
                        $file = $this->cargar_archivo(
                            "documentos_eps",
                            $this->ruta_gestion,
                            "doc_inc_eps"
                        );
                        if ($file[0] == -1) {
                            $error = $file[1];
                            if (
                                $error ==
                                "<p>You did not select a file to upload.</p>"
                            ) {
                                $resp = [
                                    "mensaje" => "Debe adjuntar el documento de identidad.",
                                    "tipo" => "info",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            } else {
                                $resp = [
                                    "mensaje" =>
                                        "Error al cargar el documento de identidad.",
                                    "tipo" => "error",
                                    "titulo" => "Oops.!",
                                ];
                                $sw = false;
                            }
                        }
                    }
    
                    if ($sw) {
                        $data_sol = [
                            "id_tipo_solicitud" => "Hum_Inc_Caja",
                            "usuario_registro" => $_SESSION["persona"],
                            "id_estado_solicitud" => $estado,
                        ];
                        $solicitud = $this->talento_humano_model->guardar_datos(
                            $data_sol,
                            "solicitudes_talento_hum"
                        );
                        $resp = [
                            "mensaje" => "Solicitud has sido guardada exitosamente",
                            "tipo" => "success",
                            "titulo" => "Proceso Exitoso!",
                            "id_solicitud" => $solicitud,
                        ];
                        if ($solicitud) {
                            $data_estado = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_estado,
                                "estados_solicitudes_talento"
                            );
                            $data_persona = [
                                "id_solicitud" => $solicitud,
                                "tipo_beneficiario" => $tipo_beneficiario,
                                "tipo_identificacion" => $tipo_documento,
                                "identificacion" => $documento,
                                "correo_ben" => $email,
                                "telefono" => $telefono,
                                "direccion" => $direccion,
                                "lugar_residencia" => $ciudad,
                                "barrio" => $barrio,
                                "id_usuario_registra" => $_SESSION["persona"],
                            ];
                            $this->talento_humano_model->guardar_datos(
                                $data_persona,
                                "talento_humano_inc_bene"
                            );
                            $documentos_eps = $file[1];
                            $data_cert = [
                                "id_solicitud" => $solicitud,
                                "nombre_real" => $nombre,
                                "nombre_archivo" => $documentos_eps,
                                "usuario_registra" => $_SESSION["persona"],
                            ];
                            $data_est = [
                                "solicitud_id" => $solicitud,
                                "estado_id" => $estado,
                                "usuario_id" => $_SESSION["persona"],
                            ];
                            if ($data_cert) {
                                $this->talento_humano_model->guardar_datos(
                                    $data_cert,
                                    "archivos_adj_th"
                                );
                            } else {
                                $resp = [
                                    "mensaje" =>"Ha ocurrido un error al intentar gestionar la solicitud", "tipo" => "error", "titulo" => "Ooops!",
                                ];
                            }
                        } else {
                            $resp = [
                                "mensaje" =>"Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.","tipo" => "error", "titulo" => "Ooops!",
                            ];
                        }
                    }
                    
                }else{
                    $resp = [
                        "mensaje" => "No ha seleccionado un tipo de beneficiario",
                        "tipo" => "info",
                        "titulo" => "Oops.!",
                    ];
                }
            }
        }
			echo json_encode($resp);
    }

	public function guardar_traslado_afp()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $sw = true;
            $estado = "Tal_Env";
			$data_cert = [];
 			$documento_traslado = "";
			$nombre = $_FILES["documento_traslado"]["name"];
            $arrayString = explode(".", $nombre);
            $extension = end($arrayString);
            if ($extension != "pdf") {
                 $resp = [
                    "mensaje" => "Debe adjuntar un archivo PDF.",
                     "tipo" => "info",
                    "titulo" => "Oops.!",
            	 ];
                $sw = false;
                } else {
                    $nombre = $_FILES["documento_traslado"]["name"];
                    $file = $this->cargar_archivo(
                        "documento_traslado",
                        $this->ruta_gestion,
                        "doc_traslado"
                    );
                    if ($file[0] == -1) {
                        $error = $file[1];
                        if (
                            $error ==
                            "<p>You did not select a file to upload.</p>"
                        ) {
                            $resp = [
                                "mensaje" =>"Debe adjuntar el documento de identidad.",
                                "tipo" => "info",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        } else {
                            $resp = [
                                "mensaje" =>
                                    "Error al cargar el documento de identidad.",
                                "tipo" => "error",
                                "titulo" => "Oops.!",
                            ];
                            $sw = false;
                        }
                    }
                }
                if ($sw) {
					$data_sol = [
						"id_tipo_solicitud" => "Hum_Tras_Afp",
						"usuario_registro" => $_SESSION["persona"],
						"id_estado_solicitud" => $estado,
					];
					$solicitud = $this->talento_humano_model->guardar_datos(
						$data_sol,
						"solicitudes_talento_hum"
					);
					$resp = [
						"mensaje" => "Solicitud has sido guardada exitosamente",
						"tipo" => "success",
						"titulo" => "Proceso Exitoso!",
						"id_solicitud" => $solicitud,
					];
                    if ($solicitud) {
                        $data_estado = [
                            "solicitud_id" => $solicitud,
                            "estado_id" => $estado,
                            "usuario_id" => $_SESSION["persona"],
                        ];
                        $this->talento_humano_model->guardar_datos(
                            $data_estado,
                            "estados_solicitudes_talento"
                        );
                        $documento_traslado = $file[1];
                        $data_cert = [
                            "id_solicitud" => $solicitud,
                            "nombre_real" => $nombre,
                            "nombre_archivo" => $documento_traslado,
                            "usuario_registra" => $_SESSION["persona"],
                        ];
						$resp = [
                        "mensaje" => "Solicitud has sido guardada exitosamente",
                        "tipo" => "success",
                        "titulo" => "Proceso Exitoso!",
                        "id_solicitud" => $solicitud,
                    ];
                        $data_est = [
                            "solicitud_id" => $solicitud,
                            "estado_id" => $estado,
                            "usuario_id" => $_SESSION["persona"],
                        ];
                        if ($data_cert) {
                            $this->talento_humano_model->guardar_datos(
                                $data_cert,
                                "archivos_adj_th"
                            );
                        } else {
                            $resp = [
                                "mensaje" =>"Ha ocurrido un error al intentar gestionar la solicitud",
                                "tipo" => "error",
                                "titulo" => "Ooops!",
                            ];
                        }
                    } 
					else {
                       		 $resp = [
                           	 	"mensaje" =>"Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.",
                           		 "tipo" => "error",
                           		 "titulo" => "Ooops!",
                        	];
                   		}
				}
            }
    	echo json_encode($resp);
    }

	public function guardar_ecargo()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
					$sw = true;
					$estado = "Tal_Env";
					$id_jefe_cargo = $this->input->post("id_jefe_cargo");
					$id_jefe_cargo1 = $this->input->post("id_jefe_cargo1");
					$id_colaborador = $this->input->post("id_colaborador");
					$motivo = $this->input->post("motivo");
					$ecargo_admin = $this->input->post("ecargo_admin");
					$logros_ecargo = $this->input->post("logros_ecargo");
					$comites_ecargo = $this->input->post("comites_ecargo");
					$responsabilidades_ecargo = $this->input->post("responsabilidades_ecargo");
					$accesos_ecargo=$this->input->post("accesos_ecargo");
					$informes_ecargo=$this->input->post("informes_ecargo");
					$data_cert = [];
                    $adjunto_ecargo = "";
                    $nombre = $_FILES["adjunto_ecargo"]["name"];
                    $arrayString = explode(".", $nombre);
                    $extension = end($arrayString);
						$text = $this->verificar_campos_string([
							"Motivo de Entrega de Cargo" => $motivo,
							"Logros" => $logros_ecargo,
							"Comites" => $comites_ecargo,
							"Responsabilidades" => $responsabilidades_ecargo,
							"Accesos" => $accesos_ecargo,
							"Informes" => $informes_ecargo,
					]);
					$num = $this->verificar_campos_numericos([
							"Jefe Inmediato" => $id_jefe_cargo
					]);
					if ( is_array($num)) {
						$campo = $num["field"];
						$resp = [
							"mensaje" => "El campo $campo no puede estar vacio.",
							"tipo" => "info",
							"titulo" => "Oops.!",
						];
						$sw = false;
					} elseif ($ecargo_admin === 1){
						if( is_array($text) ) {
							$campo = $text["field"];
							$resp = [
								"mensaje" => "El campo $campo no puede estar vacio.",
								"tipo" => "info",
								"titulo" => "Oops.!",
							];
							
								$sw = false;		
						}
					}else{
						if ($ecargo_admin === 1) {
							if ( $extension != "pdf") {
								$resp = [
									"mensaje" => "Debe adjuntar un archivo PDF.",
									"tipo" => "info",
									"titulo" => "Oops.!",
								];
								$sw = false;
							}else{
								$nombre = $_FILES["adjunto_ecargo"]["name"];
								$file = $this->cargar_archivo(
									"adjunto_ecargo",
									$this->ruta_ecargo,
									"doc_ecargo"
								);
								if ($ecargo_admin === 1) {
									if ($file[0] == -1) {
										$error = $file[1];
										if ($error == "<p>You did not select a file to upload.</p>") {
											$resp = [
												"mensaje" => "Debe adjuntar el documento requerido.",
												"tipo" => "info",
												"titulo" => "Oops.!",
											];
											$sw = false;
										}  else {
											$resp = [
												"mensaje" =>
													"Error al cargar el documento de identidad.",
												"tipo" => "error",
												"titulo" => "Oops.!",
											];
											$sw = false;
										}
		
									}
								}
							}
						}elseif ($sw) {
							$data_sol = [
								"id_tipo_solicitud" => "Hum_Entr_Cargo",
								"usuario_registro" => $_SESSION["persona"],
								"id_estado_solicitud" => $estado,
								"jefe_inmediato" => $id_jefe_cargo,
								"jefe_inmediato2" => $id_jefe_cargo1,
								"motivo_ec" => $motivo,
								"responsabilidades_ecargo" => $responsabilidades_ecargo,
								"accesos_ecargo" => $accesos_ecargo,
								"informes_ecargo" => $informes_ecargo,
								"comites_ecargo" => $comites_ecargo,
								"logros_ecargo" => $logros_ecargo,
								"id_solicitante" => $id_colaborador == "null" ? $_SESSION["persona"] : $id_colaborador,
							];
							$solicitud = $this->talento_humano_model->guardar_datos(
								$data_sol,
								"solicitudes_talento_hum"
							);
							$info = $this->talento_humano_model->get_correo_jefe_inmediato(
								$solicitud,
								null
							);
							$data_correo = [
								"solicitud_id" => $solicitud,
								"correo_jefe" => $info->{'correo'},
								"nombre_jefe" => $info->{'fullname'},
								"tipo_solicitud" => "Entrega de Cargo",
							];
							$resp = [
								"mensaje" => "Solicitud has sido guardada exitosamente",
								"tipo" => "success",
								"titulo" => "Proceso Exitoso!",
								"id_solicitud" => $solicitud,
								"data_correo" => $data_correo,
							];
							if ($solicitud) {
								$data_estado = [
									"solicitud_id" => $solicitud,
									"estado_id" => $estado,
									"usuario_id" => $_SESSION["persona"],
								];
								$this->talento_humano_model->guardar_datos(
									$data_estado,
									"estados_solicitudes_talento"
								);
							}
							if($ecargo_admin === 1){
								$adjunto_ecargo = $file[1];
								$data_cert = [
									"id_solicitud" => $id_solicitud,
									"nombre_real" => $nombre,
									"nombre_archivo" => $adjunto_ecargo,
									"usuario_registra" => $_SESSION["persona"],
								];
							}
							if ($data_cert) {
                                $this->talento_humano_model->guardar_datos(
                                    $data_cert,
                                    "archivos_adj_th"
                                );
                            }
                    } else {
                        $resp = [
                            "mensaje" =>
                                "Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.",
                            "tipo" => "error",
                            "titulo" => "Ooops!",
                        ];
                    }
				}
					
			}
        echo json_encode($resp);
    }

    public function obtener_adjunto()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $id = $this->input->post("id");
            $resp = $this->talento_humano_model->obtener_adjunto($id);   
        }
        echo json_encode($resp);
    }
	public function detalle_inc_bene()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $id = $this->input->post("id");
            $resp = $this->talento_humano_model->detalle_inc_bene($id);
        }
        echo json_encode($resp);
    }
	public function detalle_entrecargo()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $id = $this->input->post("id");
            $resp = $this->talento_humano_model->detalle_entrecargo($id);
        }
        echo json_encode($resp);
    }


	public function guadar_estado_ecargo(){
		if (!$this->Super_estado) $res = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {	
			$id_solicitud = $this->input->post("id_solicitud");
			$visto_bueno = $this->input->post("visto_bueno");
			$comentario= $this->input->post("comentario");	
			$data_est = [
					"solicitud_id" => $id_solicitud,
					"estado_id" => $visto_bueno,
					"comentario"=>$comentario,
					"usuario_id" => $_SESSION['persona']
				];
			$res = ['mensaje' => "Solicitud  exitosamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso!", 'estado' => ""];
			$add = $this->talento_humano_model->guardar_datos($data_est, "estados_solicitudes_talento");
			if(!$add) $res = ['mensaje' => "Ha ocurrido un error al intentar gestionar la solicitud", 'tipo' => "error", 'titulo' => "Ooops!"];
			else{
				$id=$id_solicitud;
				$motivo=$this->talento_humano_model->detalle_entrecargo($id);
				$vb=$this->talento_humano_model->get_cantidad_vb_ecargo($id_solicitud);
				if($motivo->motivo == "Renuncia"){
					if($vb >= 6){
						$data_vb = [
							"id_estado_solicitud" => "Tal_Vb_Ter",
						];
						$res = ['mensaje' => "Solicitud  exitosamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso!", 'estado' => "Tal_Vb_Ter"];
						$add_vb=$this->talento_humano_model->modificar_datos($data_vb, "solicitudes_talento_hum",$id_solicitud,'id');
						if($add_vb) $res = ['mensaje' => "Ha ocurrido un error al intentar cambiar el estado de la solicitud", 'tipo' => "error", 'titulo' => "Ooops!"];
					}
				}else{
					if($vb >= 5){
						$data_vb = [
							"id_estado_solicitud" => "Tal_Vb_Ter",
						];
						$res = ['mensaje' => "Solicitud  exitosamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso!", 'estado' => "Tal_Vb_Ter"];
						$add_vbe=$this->talento_humano_model->modificar_datos($data_vb, "solicitudes_talento_hum",$id_solicitud,'id');
						if($add_vbe) $res = ['mensaje' => "Ha ocurrido un error al intentar cambiar el estado de la solicitud", 'tipo' => "error", 'titulo' => "Ooops!"];
					}
				}
				
			}
			
		}
		 echo json_encode($res);
	}

	public function modificar_agregar_cargo()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
					$sw = true;
					$id_solicitud = $this->input->post("id_solicitud");
					$logros_ecargo = $this->input->post("logros_ecargo");
					$comites_ecargo = $this->input->post("comites_ecargo");
					$responsabilidades_ecargo = $this->input->post("responsabilidades_ecargo");
					$accesos_ecargo=$this->input->post("accesos_ecargo");
					$informes_ecargo=$this->input->post("informes_ecargo");
					$data_cert = [];
                    $adjunto_ecargo = "";
                    $nombre = $_FILES["adjunto_ecargo"]["name"];
                    $arrayString = explode(".", $nombre);
                    $extension = end($arrayString);
					$text = $this->verificar_campos_string([
						"Logros" => $logros_ecargo,
						"Comites" => $comites_ecargo,
						"Responsabilidades" => $responsabilidades_ecargo,
						"Accesos" => $accesos_ecargo,
						"Informes" => $informes_ecargo,
					]);
					if (is_array($text)) {
						$campo = $text["field"];
						$resp = [
							"mensaje" => "El campo $campo no puede estar vacio.",
							"tipo" => "info",
							"titulo" => "Oops.!",
						];
						$sw = false;
					}else{
						if($nombre){
							if ($extension != "pdf") {
								$resp = [
									"mensaje" => "Debe adjuntar un archivo PDF.",
									"tipo" => "info",
									"titulo" => "Oops.!",
								];
								$sw = false;
							}else{
								$nombre = $_FILES["adjunto_ecargo"]["name"];
								$file = $this->cargar_archivo(
									"adjunto_ecargo",
									$this->ruta_ecargo,
									"doc_ecargo"
								);
								if ($file[0] == -1) {
									$error = $file[1];
									if ($error == "<p>You did not select a file to upload.</p>") {
										$resp = [
											"mensaje" => "Debe adjuntar el documento requerido.",
											"tipo" => "info",
											"titulo" => "Oops.!",
										];
										$sw = false;
									}  else {
										$resp = [
											"mensaje" =>
												"Error al cargar el documento de identidad.",
											"tipo" => "error",
											"titulo" => "Oops.!",
										];
										$sw = false;
									}
								}
							}
						}
						if ($sw) {
							$data_sol = [
								"responsabilidades_ecargo" => $responsabilidades_ecargo,
								"accesos_ecargo" => $accesos_ecargo,
								"informes_ecargo" => $informes_ecargo,
								"comites_ecargo" => $comites_ecargo,
								"logros_ecargo" => $logros_ecargo,
							];
							$solicitud = $this->talento_humano_model->modificar_datos(
								$data_sol,"solicitudes_talento_hum", $id_solicitud, 'id'
							);
							$resp = [
								"mensaje" => "Solicitud has sido guardada exitosamente",
								"tipo" => "success",
								"titulo" => "Proceso Exitoso!"
							];
							if($nombre){
								$adjunto_ecargo = $file[1];
								$data_cert = [
									"id_solicitud" => $id_solicitud,
									"nombre_real" => $nombre,
									"nombre_archivo" => $adjunto_ecargo,
									"usuario_registra" => $_SESSION["persona"],
								];
							}
							if ($data_cert) {
                                $this->talento_humano_model->guardar_datos(
                                    $data_cert,
                                    "archivos_adj_th"
                                );
                            }
							
                    } else {
                        $resp = [
                            "mensaje" =>
                                "Ha ocurrido un error al intentar registrar la solicitud. Contacte al administrador.",
                            "tipo" => "error",
                            "titulo" => "Ooops!",
                        ];
                    }
				}
					
			}
        echo json_encode($resp);
    }


}
