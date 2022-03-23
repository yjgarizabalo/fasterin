<?php
date_default_timezone_set('America/Bogota');
class facturacion_control extends CI_Controller
{
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
	var $admin = false;
	var $super_admin = false;
	var $ruta_banco = "archivos_adjuntos/facturacion/banco/";
	var $ruta_facturas = "archivos_adjuntos/facturacion/facturas/";



	public function __construct()
	{
		parent::__construct();
		$this->load->model('facturacion_model');
		$this->load->model('genericas_model');
		session_start();
		if (isset($_SESSION["usuario"])) {
			$this->Super_estado = true;
			$this->Super_elimina = 1;
			$this->Super_modifica = 1;
			$this->Super_agrega = 1;
			if ($_SESSION['perfil'] == 'Per_Admin') {
				$this->super_admin = true;
				$this->admin = true;
			}
			//if ($_SESSION['perfil'] == 'Per_Admin_Man') $this->admin = true;
		}
	}
	public function index($id = '')
	{

		if ($this->Super_estado) {
			$datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'facturacion');
			if (!empty($datos_actividad)) {
				$pages = "facturacion";
				$data['js'] = "Facturacion";
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

	public function buscar_valor_parametro()
	{
		$resp = array();
		$data = array();
		if ($this->Super_estado) {
			$codigo = $this->input->post('codigo');
			$idparametro = $this->input->post('idparametro');
			if (empty($codigo)) {
				$resp = ['mensaje' => 'Ingrese datos a buscar.', 'tipo' => 'info', 'titulo' => 'Oops.!', 'data' => $data];
			} else {
				$data = $this->facturacion_model->buscar_valor_parametro($codigo, $idparametro);
				$resp = ['mensaje' => '', 'tipo' => 'success', 'titulo' => 'Busqueda realizada.!', 'data' => $data];
			}
		} else {
			$resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => 'Oops.!',];
		}

		echo json_encode($resp);
	}
	

	public function listar_facturas()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}      
		$id = $this->input->post("id");
        $estado = $this->input->post("estado");
        $empresa = $this->input->post("empresa");
		$banco = $this->input->post("banco");
		$plazo = $this->input->post("plazo");
        $fecha = $this->input->post("fecha");
        $resp = array();
		$data = $this->facturacion_model->listar_facturas($id, $estado,$empresa, $banco, $plazo, $fecha);
		$tiempo = $this->genericas_model->obtener_valores_parametro_aux('Fac_Limit',20)[0]['valor'];

		$solicitudes = array();
		$perfil = $_SESSION['perfil'];
		$persona = $_SESSION['persona'];
        $ver_solicitado = '<span  style="background-color: #ffff;color: #000;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';      
        $ver_tramitada = '<span  style="background-color: #f0ad4e;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';      
        $ver_rojo = '<span  style="background-color: #d9534f;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';      
        $ver_finalizado = '<span  style="background-color: #39b23b;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';      
		$btn_tramitar = '<span title="Tramitar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px" class="pointer fa fa-retweet btn btn-default tramitar"></span>';
		$btn_aprobar = '<span title="Aprobar" data-toggle="popover" data-trigger="hover" style="color: #00cc00;margin-left: 5px" class="pointer fa fa-check btn btn-default aprobar"></span>';
		$btn_negar = '<span title="Negar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px" class="pointer fa fa-ban btn btn-default negar"></span>';
		$btn_cancelar = '<span title="Cancelar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px" class="pointer fa fa-remove btn btn-default cancelar"></span>';
		$btn_cerrada = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
		$btn_copiar = '<span title="Copiar Solicitud" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px" class="pointer fa fa-copy btn btn-default copiar"></span>';
		$notifica = 0;
		foreach ($data as $row) {
            $row['ver'] = $ver_solicitado;
            $row['accion'] = $btn_aprobar;
			$id_estado_solicitud = $row['id_estado_solicitud'];
            if ($id_estado_solicitud == 'Fact_Sol') {
				$notifica ++;
				$row['ver'] = $ver_solicitado;
				if($perfil != 'Per_Admin' && $perfil != 'Per_Fac'){
					$row['accion'] = $btn_cancelar;
				}else {
					$row['accion'] = $btn_aprobar.' '.$btn_tramitar.' '.$btn_negar;
					if ($perfil == 'Per_Admin')  $row['accion'] = $row['accion'].' '.$btn_cancelar;
				}
            }else if ($id_estado_solicitud == 'Fact_Tra') {
				$row['ver'] = $ver_tramitada;
				if($perfil != 'Per_Admin' && $perfil != 'Per_Fac'){
					$row['accion'] = $btn_cerrada;
				}else {
					$row['accion'] = $btn_aprobar.' '.$btn_negar;
				}
            }else if ($id_estado_solicitud == 'Fact_Neg') {
                $row['ver'] = $ver_rojo;
                $row['accion'] = $btn_cerrada;
            }else if ($id_estado_solicitud == 'Fact_Fin') {
				$row['ver'] = $ver_finalizado;
				if(($persona == $row['id_usuario_registra'] || $perfil == "Per_Admin") &&  $row['dias_trans'] < $tiempo) $row['accion'] = $btn_copiar;
				else $row['accion'] = $btn_cerrada;
            }else if ($id_estado_solicitud == 'Fact_Can') {
                $row['ver'] = $ver_rojo;
                $row['accion'] = $btn_cerrada;
			}
			$row["valor"] = $this->convertir_moneda($row["valor"],true,2);

            array_push($solicitudes,$row);
		}
		$notifica = $perfil == 'Per_Fac' ? $notifica : 0;
		
		$resp = ['data' => $solicitudes, 'notifica' => $notifica];
		echo json_encode($resp);
	}


	public function guardarFactura()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => 'Oops.!',];
		}else{
			if (!$this->Super_agrega) {
				$resp = ['mensaje' => 'No tiene permisos para realizar esta operación.', 'tipo' => 'error', 'titulo' => 'Oops.!',];
			}else{
				$id_codigo_sap = $this->input->post('id_codigo_sap');
				$id_empresa = $this->input->post('id_empresa');
				$valor = $this->input->post('valor');
				$concepto = $this->input->post('concepto');
				$id_plazo = $this->input->post('id_plazo');
				$id_banco = $this->input->post('id_banco');
				$id_entrega = $this->input->post('id_entrega');
				$id_tipo_cuenta = $this->input->post('id_tipo_cuenta');
				$num_cuenta = $this->input->post('num_cuenta');
				$checkbox = $this->input->post('checkbox');
				$id_usuario_registra = $_SESSION["persona"];
				$adj_banco = $_FILES['adj_banco']['size'];
				$adj_rut = $_FILES['adj_rut']['size'];

				$str = $this->verificar_campos_string(['Codigo sap' => $id_codigo_sap,'Concepto' => $concepto, 'Plazo' => $id_plazo ,'Tipo Entrega' => $id_entrega]);
				$str_check = $this->verificar_campos_string(['Banco' => $id_banco, 'Tipo de cuenta' => $id_tipo_cuenta, 'Numero de cuenta' => $num_cuenta, 'certificación Bancaria' => $adj_banco]);
				$valor_total = $this->convertir_moneda($valor,false);

				if (ctype_space($valor) || empty($valor)) {
					$resp = ['mensaje' => "El valor de la factura no puede estar vacio o contener espacios en blanco.", 'tipo' => "info", 'titulo' => "Oops.!"];
				}else if (!is_numeric($valor_total)) {
					$resp = ['mensaje' => "Ingrese datos numericos en el campo valor", 'tipo' => "info", 'titulo' => "Oops.!"];
                }else if(empty($id_empresa) && empty($adj_rut)){
					$resp = ['mensaje' => "Ingrese empresa o RUT.", 'tipo' => "info", 'titulo' => "Oops.!"];
				}else if (is_array($str)){
					$campo = $str['field'];
					$resp = ['mensaje' => "El campo $campo no puede estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
				}else if($checkbox && is_array($str_check)){
					$campo = $str_check['field'];
					$resp = ['mensaje' => "El campo $campo no puede estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
				}else{
					$sw = true;
					if (empty($id_empresa)) {
						$file_rut_ = $this->cargar_archivo("adj_rut", $this->ruta_banco, 'Banco_');
						if ($file_rut_[0] == -1){
							$sw = false;
							$resp = ['mensaje'=>"Error al cargar al cargar el RUT.",'tipo'=>"error",'titulo'=> "Oops.!"]; 				
						}else{
							$adj_rut = $file_rut_[1];
							$id_empresa = null;
						}
					}

					if ($checkbox && $sw) {
						$file = $this->cargar_archivo("adj_banco", $this->ruta_banco, 'Banco_');
						if ($file[0] == -1){
							$sw = false;
							$resp = ['mensaje'=>"Error al cargar al cargar la certificacion bancaria.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
						}else{
							$adj_banco = $file[1];
						} 
					}

					if ($sw) {
						$data = [
							'id_codigo_sap' => $id_codigo_sap,
							'id_empresa' => $id_empresa,
							'adj_rut' => empty($id_empresa) ? $adj_rut : null,
							'valor' => $valor_total,
							'concepto' => $concepto,
							'id_plazo' => $id_plazo,
							'id_tipo_entrega' => $id_entrega,
							'id_banco' => $checkbox ? $id_banco : null,
							'id_tipo_cuenta' => $checkbox ? $id_tipo_cuenta : null,
							'num_cuenta' => $checkbox ? $num_cuenta : null,
							'adj_banco' => $checkbox ? $adj_banco : null,
							'id_usuario_registra' => $id_usuario_registra,
						];

						$add = $this->facturacion_model->guardar_datos($data, 'facturas');

						$solicitud = $this->facturacion_model->traer_ultima_solicitud($id_usuario_registra);
						$id_solicitud_correo = $solicitud -> {'id'};

						if ($add != 0) 	$resp = ['mensaje' => 'Error al guardar la factura, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
						else{
							$resp = ['mensaje' => "La factura fue registrada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id' => $id_solicitud_correo];
							$resp['id'] = $solicitud -> {'id'};
							$data_estado  = [ 'id_solicitud' => $solicitud -> {'id'},'id_estado' => 'Fact_Sol', 'id_usuario_registra' => $id_usuario_registra];
							$add_estado = $this->facturacion_model->guardar_datos($data_estado,'factura_estados');
						}
					}
				} 
			}
		}
		echo json_encode($resp);
	}

	public function modificarFactura()
	{
		if (!$this->Super_estado) {
			$resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => 'Oops.!',];
		}else{
			if (!$this->Super_modifica) {
				$resp = ['mensaje' => 'No tiene permisos para realizar esta operación.', 'tipo' => 'error', 'titulo' => 'Oops.!',];
			}else{
		
				$id = $this->input->post('id');
				$id_codigo_sap = $this->input->post('id_codigo_sap');
				$id_empresa = $this->input->post('id_empresa');
				$valor = $this->input->post('valor_mod');
				$concepto = $this->input->post('concepto_mod');
				$id_plazo = $this->input->post('id_plazo_mod');
				$id_banco = $this->input->post('id_banco_mod');
				$id_entrega = $this->input->post('id_entrega_mod');
				$id_tipo_cuenta = $this->input->post('id_tipo_cuenta_mod');
				$num_cuenta = $this->input->post('num_cuenta_mod');
				$checkbox = $this->input->post('modcheckbox');
				$adj_banco = $_FILES['adj_banco_mod']['size'];
				$adj_rut = $_FILES['adj_rut_mod']['size'];
				$str = $this->verificar_campos_string(['Codigo sap' => $id_codigo_sap,'Valor' => $valor,'Concepto' => $concepto, 'Plazo' => $id_plazo ,'Tipo Entrega' => $id_entrega]);
				$str_check = $this->verificar_campos_string(['Banco' => $id_banco, 'Tipo de cuenta' => $id_tipo_cuenta, 'Numero de cuenta' => $num_cuenta]);
				
				
				
				if (empty($id)) {
					$resp = ['mensaje' => "Error al cargar la información. contacte con el amdinistrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
				}else{
					$solicitud = $this->facturacion_model->consulta_solicitud_id($id);
					$valor_total = $this->convertir_moneda($valor,false);

					$adj_rut_bd = $solicitud->{'adj_rut'};    
					$adj_banco_bd = $solicitud->{'adj_banco'};    
					$id_empresa_bd = $solicitud->{'id_empresa'};  

					if (ctype_space($valor) || empty($valor)) {
						$resp = ['mensaje' => "El valor de la factura no puede estar vacio o contener espacios en blanco.", 'tipo' => "info", 'titulo' => "Oops.!"];
					}else if (!is_numeric($valor_total)) {
						$resp = ['mensaje' => "Ingrese datos numericos en el campo valor", 'tipo' => "info", 'titulo' => "Oops.!"];
					}else if(empty($id_empresa) && empty($adj_rut) && empty($adj_rut_bd)){
						$resp = ['mensaje' => "Ingrese empresa o RUT.", 'tipo' => "info", 'titulo' => "Oops.!"];
					}else if (is_array($str)){
						$campo = $str['field'];
						$resp = ['mensaje' => "El campo $campo no puede estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
					}else if($checkbox && is_array($str_check)){
						$campo = $str_check['field'];
						$resp = ['mensaje' => "El campo $campo no puede estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
					}else if($checkbox && empty($adj_banco) && empty($adj_banco_bd)){
						$resp = ['mensaje' => "Ingrese empresa o RUT.", 'tipo' => "info", 'titulo' => "Oops.!"];
					}else{
						$sw = true;
						if (!empty($adj_rut)) {
							$file_rut_ = $this->cargar_archivo("adj_rut_mod", $this->ruta_banco, 'Banco_');
							if ($file_rut_[0] == -1){
								$sw = false;
								$resp = ['mensaje'=>"Error al cargar al cargar el RUT.",'tipo'=>"error",'titulo'=> "Oops.!"]; 				
							}else{
								$adj_rut = $file_rut_[1];
								$id_empresa = null;
							}
						}else{
							$id_empresa = !empty($id_empresa) ?  $id_empresa : null;
							$adj_rut = empty($id_empresa) ?  $adj_rut_bd : null;
						}
	
						if ($checkbox && !empty($adj_banco)) {
							$file = $this->cargar_archivo("adj_banco_mod", $this->ruta_banco, 'Banco_');
							if ($file[0] == -1){
								$sw = false;
								$resp = ['mensaje'=>"Error al cargar al cargar la certificacion bancaria.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
							}else{
								$adj_banco = $file[1];
							} 
						}else{
							$adj_banco = $adj_banco_bd;
						}
	
						if ($sw) {
							$data = [
								'id_codigo_sap' => $id_codigo_sap,
								'id_empresa' =>  $id_empresa,
								'adj_rut' => $adj_rut,
								'valor' => $valor_total,
								'concepto' => $concepto,
								'id_plazo' => $id_plazo,
								'id_tipo_entrega' => $id_entrega,
								'id_banco' => $checkbox ? $id_banco : null,
								'id_tipo_cuenta' => $checkbox ? $id_tipo_cuenta : null,
								'num_cuenta' => $checkbox ? $num_cuenta : null,
								'adj_banco' => $checkbox ? $adj_banco : null,
							];
							$resp = ['mensaje' => "La factura fue registrada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
							$add = $this->facturacion_model->modificar_datos($data, 'facturas', $id);
							if ($add != 0) 	$resp = ['mensaje' => 'Error al modificar la factura, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
						}
					} 
				}
		
			}
		}
		echo json_encode($resp);
	}

	public function cambiarEstado()
	{
		if(!$this->Super_estado){
			$resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		}else{


			$id_solicitud = $this->input->post("id");
			$estado = $this->input->post("estado");
			$msj_negado = $this->input->post("mensaje");
			$id_usuario_registra = $_SESSION["persona"];
			$valido = $this->validar_estado($id_solicitud, $estado);

			if ($valido) {
			$data_fac = [
				'id_estado_solicitud' => $estado,
				'msj_negado' => $msj_negado,
				];
			$add = $this->facturacion_model->modificar_datos($data_fac, 'facturas', $id_solicitud);
				if ($add != 0) 	$resp = ['mensaje' => 'Error al modificar la factura, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
				else {
					$data = [
						'id_solicitud' => $id_solicitud,
						'id_estado' => $estado,
						'id_usuario_registra' => $id_usuario_registra,
					];
					$add = $this->genericas_model->guardar_datos($data, 'factura_estados');
					$solicitud = $this->facturacion_model->consulta_solicitud_id($id_solicitud);
					$id_solicitud_correo = $solicitud -> {'id'};

					$resp = ['mensaje' => "Proceso finalizado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!", 'id' => $id_solicitud_correo];

				}
			}else{
				$resp = ['mensaje' => "La solicitud ya fue gestionada anteriormente o no esta autorizado para realizar esta operación.", 'tipo' => "info", 'titulo' => "Oops.!"];
			}
		} 
		echo json_encode($resp);
	}

    public function cargar_archivo($mi_archivo, $ruta, $nombre)
    {
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

	public function guardar_factura_aprobada()
	{
		if ($this->Super_estado) {

			$id_solicitud = $this->input->post("id");
			$estado = 'Fact_Fin';
			$adj_aprobar = null;
			$id_usuario_registra = $_SESSION["persona"];
			$fecha_registra = date("Y-m-d");


				$file_factura = $this->cargar_archivo("adj_aprobar", $this->ruta_facturas, 'Factura_');
				$valido = $this->validar_estado($id_solicitud, $estado);
				if ($valido) {
					if ($file_factura[0] == -1){
					$error = $file_factura[1];
						if ($error == '<p>You did not select a file to upload.</p>') {
							$resp = ['mensaje'=>"Debe adjuntar una factura.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
						}else{
							$resp = ['mensaje'=>"Error al cargar la factura.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
						}
					}else{
						$adj_aprobar = $file_factura[1];

						$data_aprobada = [					
							'id' => $id_solicitud,
							'adj_aprobado' => $adj_aprobar,
							'id_estado_solicitud' => 'Fact_Fin',
						];

						$add = $this->facturacion_model->modificar_datos($data_aprobada, 'facturas',$id_solicitud);
						if($add == 0) $resp = ['mensaje' => "Proceso finalizado con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso.!"];
						$data_estado  = [
							"id_solicitud" =>$id_solicitud,
							'id_estado' => 'Fact_Fin',
							'fecha' => $fecha_registra,
							'id_usuario_registra' => $id_usuario_registra,

							];
						$add_estado = $this->facturacion_model->guardar_datos($data_estado,'factura_estados');
						if ($add != 0) 	$resp = ['mensaje' => 'Error al guardar la factura, contacte con el administrador.', 'tipo' => "error", 'titulo' => "Oops.!"];
					} 
					
				}else{
					$resp = ['mensaje'=>"La solicitud ya fue gestionada anteriormente o no esta autorizado para realizar esta operación.",'tipo'=>"info",'titulo'=> "Oops.!", 'refres'=> 1]; 
				}
		} else {
			$resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => 'Oops.!',];
		}
		echo json_encode($resp);
	}
	/**
	 * Recibe un array con clave-valor con los campos a verificar. 
	 * En caso de que uno de los campos esté vacio retorna el error -2 y el nombre del campo respectivo.
	 * @param Array $array 
	 * @return Integer
	 */
	public function verificar_campos_string($array)
	{
		foreach ($array as $row) {
			if (empty($row) || ctype_space($row)) {
				return ['type' => -2, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
	}

	public function verificar_campos_numericos($array)
	{
		foreach ($array as $row) {
			if (empty($row) || ctype_space($row) || !is_numeric($row)) {
				return ['type' => -1, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
	}
	public function listar_estados()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		$id_solicitud = $this->input->post("id_solicitud");

		$data = $this->facturacion_model->listar_estados($id_solicitud);        
		echo json_encode($data);

	}
	public function consulta_solicitud_id()
	{
		if ($this->Super_estado == false) {
			echo json_encode("sin_session");
			return;
		}
		$id = $this->input->post("id");
		$data = $this->facturacion_model->consulta_solicitud_id($id); 
		$data->{"valor"} = $this->convertir_moneda($data->{"valor"},true,2);
		echo json_encode($data);

	}
	public function validar_estado($id,$estado_nuevo){
		$solicitud = $this->facturacion_model->consulta_solicitud_id($id);

		$estado_actual = $solicitud->{'id_estado_solicitud'};
		$solicitante = $solicitud->{'id_usuario_registra'};
		$administra = $_SESSION['perfil'] == 'Per_Admin' ||  $_SESSION['perfil'] == 'Per_Fac'? true : false; 
		$persona = $_SESSION["persona"];
		if ($administra && $estado_actual == 'Fact_Sol' && ($estado_nuevo == 'Fact_Fin' || $estado_nuevo == 'Fact_Tra' || $estado_nuevo == 'Fact_Neg'|| $estado_nuevo == 'Fact_Can')) {
			return true; 
		}else if($administra && $estado_actual == 'Fact_Tra'  && ($estado_nuevo == 'Fact_Fin' || $estado_nuevo == 'Fact_Neg')){
			return true; 
		}else if(($persona == $solicitante || $_SESSION['perfil'] == 'Per_Admin') && $estado_actual == 'Fact_Sol' && $estado_nuevo == 'Fact_Can'){
			return true;
		}else if(($persona == $solicitante) && $estado_actual == 'Fact_Can'){
			return true;
		}
		return false;
	  }


	public function tiempo_finalizado($id_solicitud)
	{
		$solicitud = $this->facturacion_model->consulta_solicitud_id_estado($id_solicitud,'Fact_Fin');
		$fecha_actual = date("Y-m-d");
		$fecha_fin = date("Y-m-d",strtotime($solicitud->{'fecha'}));

		$inicio = new DateTime($fecha_actual);
		$fin = new DateTime($fecha_fin);
		$interval = $inicio->diff($fin);
		$resp = $interval->format('%R%a');
		// echo json_encode($resp);
		// return;
	} 
	public function milisegundos()
	{
        $minutos = $this->genericas_model->obtener_valores_parametro_aux("Tiem_Fact", 20);
		echo json_encode($minutos[0]);
	} 

	public function convertir_moneda($number,$format , $decimal = 2){

        if (!$format) {
            $number = str_replace(".", "", $number);
            $number = str_replace(",", ".", $number);
           return $number;
        }
        return number_format($number,$decimal ,",", ".");
    }
}

