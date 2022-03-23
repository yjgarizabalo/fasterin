<?php
class inventario_control extends CI_Controller {
    //Variables encargadas de los permisos que tiene el usuario en session
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
	var $permisos = null;
    var $admin = false;
    var $ruta_documentos = "archivos_adjuntos/laboratorios/documentos/";
    var $tipo_modulo = '';
    //Construtor del controlador, se importa el modelo inventario_model y se inicia la session
    public function __construct() {
        parent::__construct();
        $this->load->model('inventario_model');
        $this->load->model('talento_humano_model');
        $this->load->model('genericas_model');
        $this->load->model('pages_model');
        session_start();
        date_default_timezone_set("America/Bogota");
        //la variable Super_estado es la encargada de notificar si el usuario esta en sesion, si no esta en sesion no podra ejecutar ninguna funcion, cuando pasa eso se retorna sin_session en la funcion que se esta ejecutando,por otro lado las variables Super_elimina, Super_modifica, Super_agrega se encarga de delimitar los permisos que tiene el perfil del usuario en la actividad que esta trabajando, si no tiene permiso las variables toman un valor de 0 y no les permite ejecutar la funcion retornando -1302.
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
			$_SESSION['perfil'] === 'Per_Admin' || $_SESSION['perfil'] === 'Per_Sop' || $_SESSION['perfil'] === 'Admin_Aud' || $_SESSION['perfil'] === 'Per_Lab'
			    ? $this->admin = true
				: $this->permisos = $this->inventario_model->get_permisos_asignados();
        }
    }
    /**
     * Se encarga de pintar el modulo de inventario, se carga el header alterno y la ventana inventario
     * @return Void
     */
    public function index() {
        $periodicidad = null;
        $uri = explode('/index.php/', $_SERVER["REQUEST_URI"])[1];
        if($uri === 'tecnologia/inventarioAUD'){
            $this->tipo_modulo = 'Inv_Aud';
        } else if($uri === 'laboratorios') {
            $periodicidad = $this->input->post("periodicidad");
            $this->tipo_modulo = 'Inv_Lab';
        } else {
            $this->tipo_modulo = 'Inv_Tec';
        }
        $data['tipo_modulo'] = $this->tipo_modulo;
        $pages = "inicio";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $pages = $this->get_route();
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $pages);
            if (!empty($datos_actividad)) {
                $pages = "inventario";
                $data['js'] ="Inventario";
                $data['en_fecha'] = $this->admin ? $this->inventario_model->en_fecha_proyecto() : 0;
                $data['en_garantia'] = $this->admin ? $this->inventario_model->en_fecha_garantia($this->tipo_modulo) : 0;
                $data['mantenimiento_a_vencer'] = $this->admin ? $this->inventario_model->en_fecha_a_vencer($periodicidad) : 0;
				$data['actividad'] = $datos_actividad[0]["id_actividad"];
				$data['admin'] = $this->admin;
				$data['permisos'] = $this->permisos;
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
    /**
     * Se encarga de mostrar de listar la informacion del inventario, si la persona en sesion tiene el perfil de Admin_Aud se muestra unicamente el inventario de audiovisuales.
     * @return Array
     */
    function Cargar_inventario() {
        $articulos = array();
        if ($this->Super_estado == false) {
            echo json_encode($articulos);
            return;
        }
        $tipo_modulo = $this->input->post("tipo_modulo");
        $ubicacion = $this->input->post("ubicacion");
        $lugar = $this->input->post("lugar");
        $aux = $this->input->post("aux");
        $datos = $this->inventario_model->Listar_tipo_articulos($tipo_modulo, $ubicacion, $aux, $this->admin, $lugar);
        foreach ($datos as $row) { $articulos[] = $row; }
        echo json_encode($articulos);
        return;
	}

	public function cargar_tipo_recursos(){
		$tipos = array();
        if ($this->Super_estado == false) {
            echo json_encode($tipos);
            return;
		}
		$tipo_modulo = $this->input->post('tipo_modulo');
		$tipos = $this->inventario_model->cargar_tipo_recursos($tipo_modulo);
		echo json_encode($tipos);
	}

	function Cargar_articulos(){
		$data = [];
        if ($this->Super_estado) {
            $tipo_modulo = $this->input->post("tipo_modulo");
            $buscar = $this->input->post("buscar");
            $estado = $this->input->post("estado");
            $lugar = $this->input->post("lugar");
            $aux = $this->input->post("aux");
            $en_fecha = $this->input->post("en_fecha");
            $ubicacion = $this->input->post("ubicacion");
            $datos = $this->inventario_model->Listar($tipo_modulo, $buscar, $ubicacion, $aux, $estado, $this->admin, $en_fecha, $lugar);
            $bg_ver = '#2E79E5';
            $color_ver = 'white';
    
            $btn_perifericos = '<span title="Agregar Periferico" data-toggle="popover" data-trigger="hover" style="color: #458bca;margin-left: 5px" class="pointer fa fa-plug btn btn-default perifericos"></span>';
            $btn_edit = '<span title="Editar Activo" data-toggle="popover" data-trigger="hover" style="color: #6E1F7C;margin-left: 5px" class="pointer fa fa-edit btn btn-default edit_act"></span>';
            $btn_baja = '<span title="Dar de Baja" data-toggle="popover" data-trigger="hover" style="color: #d9534f;margin-left: 5px" class="pointer fa fa-download btn btn-default dar_baja"></span>';
            $btn_responsables = '<span title="Agregar Responsable" data-toggle="popover" data-trigger="hover" style="color: #00cc00;margin-left: 5px" class="pointer fa fa-user btn btn-default responsables"></span>';
            $btn_lugares = '<span title="Agregar Lugar" data-toggle="popover" data-trigger="hover" style="color: #5bc0de;margin-left: 5px" class="pointer fa fa-building btn btn-default lugares"></span>';
            $btn_mantenimiento = '<span title="Mantenimiento" data-toggle="popover" data-trigger="hover" style="color: #f0ad4e;margin-left: 5px" class="pointer fa fa-cogs btn btn-default mantenimiento"></span>';
            // $btn_documentos = '<span title="Documentos" data-toggle="popover" data-trigger="hover" style="color: #337ab7;margin-left: 5px" class="pointer fa fa-archive btn btn-default documentos"></span>';
            $btn_documentos = '';
            $btn_inhabil = '<span data-toggle="popover" data-trigger="hover" style="margin-left: 5px" class="pointer fa fa-toggle-off btn"></span>';
            $btn_perifericos_lab = '<span title="Agregar accesorio" data-toggle="popover" data-trigger="hover" style="color: #458bca;margin-left: 5px" class="pointer fa fa-plug btn btn-default accesorios"></span>';
            $recursos = [];
            
            foreach ($datos as $row) {
                $estado = $row['estado_aux'];
                $cod = $row['cod'];
                $row["accion"] = (!$this->admin && $this->permisos[0]['gestionar'] != '1') ||  $estado == "RecBaja"
                    ? $btn_inhabil 
                    : ($tipo_modulo == 'Inv_Lab' && $this->admin) ? "$btn_responsables $btn_lugares $btn_baja $btn_mantenimiento $btn_edit"
                    : "$btn_responsables $btn_lugares $btn_baja $btn_mantenimiento";
                if($estado == "RecBaja") $row["accion"] . $row["accion"];
                else $row["accion"] = "$btn_perifericos_lab $btn_documentos " . $row["accion"];
                if ($estado == "RecAct") {
                    $bg_ver = 'white';
                    $color_ver = 'black';
                } else if ($estado == "RecMat"){
                    $bg_ver = '#EABD32';
                    $color_ver = 'white';
                } else if ($estado == "RecBaja"){
                    $bg_ver = '#CE4949';
                    $color_ver = 'white';
                } else if($estado == "RecEsp"){
                    $bg_ver = '#337ab7';
                    $color_ver = 'white';
                }
                $row["codigo"] = "<span title='Mas Informacion' data-toggle='popover' data-trigger='hover' style='background-color: $bg_ver;color: $color_ver; width: 100%; ;' class='pointer form-control' ><span >ver</span></span>";
                $recursos[] = $row;
            }
            $data = ['recursos' => $recursos,'ad' => $this->admin,'per' => $this->permisos];
		}
        echo json_encode($data);

	}
	
    /**
     * Se encarga de listar todos los matenimientos con el id del recurso enviado por post
     * @return Array
     */
    function Cargar_mantenimiento() {
        $mantenimientos = array();
        if ($this->Super_estado == false) {
            echo json_encode($mantenimientos);
            return;
        }
        $id = $this->input->post('id');
        $datos = $this->inventario_model->listar_mantenimientos($id);
        //Para que la libreria de la tabla con la que se esta trabajando muestra la informacion es necesario enviarle los datos en una matriz.
        foreach ($datos as $row) { $mantenimientos["data"][] = $row; }
        echo json_encode($mantenimientos);
        return;
	}
	
    /**
     * Se encarga de listar todos los perifericos que tiene conectado o en historial un recurso
     * @return Array
     */
    function Listar_perifericos() {
        $perifericos = array();
        if ($this->Super_estado == false) {
            echo json_encode($perifericos);
            return;
        }
        $id = $this->input->post('id');
		$datos = $this->inventario_model->Listar_perifericos($id);
        $i = 1;
        //Para que la libreria de la tabla con la que se esta trabajando muestra la informacion es necesario enviarle los datos en una matriz. 
        foreach ($datos as $row) {
			$row["indice"] = $i;
			if ($row['estado']) {
				$row["ver"] = ' <span title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control"><span>ver</span></span>';
				$row["accion"] = ' <span title="Retirar Periférico" data-toggle="popover" data-trigger="hover" class="btn btn-default" style="color:#d9534f" onclick="confirmar_retirar_periferico(' . $row["id"] . ')"><span class="pointer glyphicon glyphicon-remove"></span></span>';
			}else{
                $row["ver"] = ' <span title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: #d9534f;color: white; width: 100%; ;" class="pointer form-control"><span>ver</span></span>';
				$row["accion"] = ' <span title="Periférico retirado" data-toggle="popover" data-trigger="hover"><span style="color:#a0a0a0" class="pointer fa fa-toggle-off"></span></span>';
			}
			$perifericos["data"][] = $row;
            $i++;
        }
        echo json_encode($perifericos);
        return;
    }

    /**
     * Lista el historial de responsables que ha tenido el id del recurso que se envia por post
     * @return Array
     */
    function Cargar_responsables() {
        $responsables = array();
        if ($this->Super_estado == false) {
            echo json_encode($responsables);
            return;
        }
        $id = $this->input->post('id');
        $datos = $this->inventario_model->Cargar_responsables($id);

        $i = 1;
        foreach ($datos as $row) {
            $estado = $row['estado'];
            if ($estado == 1) {
                $row["ver"] = "<span title='Mas Informacion' data-toggle='popover' data-trigger='hover' style='background-color: white;color: black; width: 100%;' class='pointer form-control' ><span >ver</span></span>";
				$row['accion'] = ($this->admin || $this->permisos[0]['gestionar'] == 1) 
					? "<span title='Retirar Responsable' data-toggle='popover' data-trigger='hover' class='btn btn-default fa fa-trash-o eliminar' style='color:red'></span>"
					: "<span class='fa fa-toggle-off'></span>";
            }else {
                $row["ver"] = "<span title='Mas Informacion' data-toggle='popover' data-trigger='hover' style='background-color:#d9534f;color: white;width: 100%; ' class='pointer form-control' ><span >ver</span></span>";
                $row['accion'] = "<span title='Retirar Responsable' class='fa fa-toggle-off'></span>";

            }
            $row["indice"] = $i;
            $responsables["data"][] = $row;
            $i++;
        }
        echo json_encode($responsables);
        return;
    }

    public function dar_baja() {
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{           
                $id = $this->input->post("id");
                $mensaje = $this->input->post("mensaje");
                $perifericos = $this->inventario_model->Listar_perifericos($id, true);
                if (!empty($perifericos)) {
                    $resp = ['mensaje'=>"El recurso tiene perifericos asignados por lo cual no es posible Dar de Baja, retire los perifericos e intente de nuevo", 'tipo'=>"info", 'titulo'=> "Oops..."];
                }else{
                    $data = [
                        "estado_recurso" => 'RecBaja',
                        "motivo_baja" => $mensaje,
                        "fecha_de_baja" => date("Y-m-d H:i:s"),
                        "usuario_de_baja" => $_SESSION["persona"], 
                    ];
                    $mod = $this->pages_model->modificar_datos($data, 'inventario', $id);
                    if($mod != 1) $resp = ['mensaje'=>"Error al modificar el registro, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    else $resp = ['mensaje'=>"Recurso dado de baja exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                }
            }
        }
        echo json_encode($resp);
    }




    /**
     * Cambia el estado del recurso a especial
     * @return Integer
     */
    public function pasar_especial() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("id");
                $estado = $this->input->post("estado");
                if ($estado == "RecEsp") {
                    $estado = "RecAct";
                } else if ($estado == "RecAct") {
                    $estado = "RecEsp";
                } else {
                    echo json_encode(2);
                    return;
                }
                $usuario = $_SESSION["persona"];
                $fecha = date("Y-m-d H:i:s");
                $resultado = $this->inventario_model->Modificar_estado_recurso($id, $estado, $usuario, $fecha);
                echo json_encode($resultado);
            }
        }
        return;
    }

    /**
     * retirar un periferico de un recurso
     * @return Integer
     */
    public function retirar_periferico() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_elimina == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("id");
                $usuario = $_SESSION["persona"];
                $fecha = date("Y-m-d H:i:s");
                $resultado = $this->inventario_model->retirar_periferico($id, $usuario, $fecha);

                echo json_encode($resultado);
            }
        }
        return;
    }

    /**
     * Obtiene los datos de un recurso en especifico, la consulta se have por el id del recurso que se envia por post.
     * @return Arary
     */
    function obtener_valores_inventario() {
        $id = $this->input->post('id');
        $resp = $this->Super_estado ? $this->inventario_model->obtener_Datos_inventario($id) : array();
        echo json_encode($resp);
    }

    /**
     * Obtiene en detalle la informacion de un recurso con sus relaciones, la consulta se have por el id del recurso que se envia por post.
     * @return Array
     */
    function obtener_valores_inventario_info() {
        $id = $this->input->post('id');
        $resp = $this->Super_estado ? $this->inventario_model->obtener_informacion_inventario($id) : array();
        echo json_encode($resp);
    }

    /**
     * Obtiene el detalle de un recurso, depende del tipo de recurso
     * @return Array
     */
    function obtener_detalle_inventario_info() {

        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        
        $id = $this->input->post("id");
        $tipo = $this->input->post("tipo");
        $tipo_modulo = $this->input->post("tipo_modulo");
        //Si el recurso es de tipo portatil o de tipo torre tiene detalle 
        if ($tipo == "Torre" || $tipo == "Port" || $tipo == "PortMini") {
            $datos = $this->inventario_model->obtener_detalle_Recurso($id);
            echo json_encode($datos);
        } else {
            if($tipo_modulo == 'Inv_Lab'){
                $datos = $this->inventario_model->get_datos_lab($id);
                echo json_encode($datos);
            } else {
                // Se verifica si el recurso es un periférico y se retorna el id del dispositivo al cual esté asignado
                $datos = $this->inventario_model->es_periferico($id);
                echo json_encode(count($datos) > 0 ? $datos : "");
            }
        }
    }
    /**
     * Se encarga de registrar un nuevo recurso al inventario, dependiendo del tipo de recurso que se desea guardar se hacen difertes registros
     * @return Integer
     */
	public function guardar_inventario() {
        if ($this->Super_estado == false) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
				$departamento = $this->input->post("departamento");
				$marca = $this->input->post("marca");
				$modelo = $this->input->post("modelo");
				$recurso = $this->input->post("recurso");
				$tipo_modulo = $this->input->post("tipo_modulo");
                $valor = $this->retornar_null($this->input->post("valor"));
                $fecha_garantia = $this->retornar_null($this->input->post("fecha_garantia"));
                $fecha_ingreso =$this->retornar_null($this->input->post("fecha_ingreso"));
                $descripcion = $this->input->post("descripcion");
                $responsables = json_decode($this->input->post("responsables"), true);
                $id_lugar = $this->input->post("id_lugar");
                $id_ubicacion = $this->input->post("id_ubicacion");
                $fecha_inicio_proyecto = $this->input->post("fecha_inicio_proyecto");
                $fecha_fin_proyecto = $this->input->post("fecha_fin_proyecto");
                $codigo_interno = $this->input->post("codigo_interno");
				$serial = $this->input->post("serial");
                $id_codigo_sap = $this->input->post("id_codigo_sap");
                $procesador = $this->input->post("procesador");
                $memoria = $this->input->post("memoria");
                $discoDuro = $this->input->post("discoDuro");
                $sistemaOperativo = $this->input->post("sistemaOperativo");
                $data_responsables = array();
                $tipo_modulo = $this->input->post("tipo_modulo");
                $fechas_proyecto = $this->validateFechaMayor($fecha_inicio_proyecto,$fecha_fin_proyecto);

                // Campos inventario Laboratorios
                $uso_activo = $this->input->post("uso_activo");
                $nombre_activo = $this->input->post("nombre_activo");
                $referencia = $this->input->post("referencia");
                $lugar_origen = $this->input->post("lugar_origen");
                $observaciones = $this->input->post("observaciones");
                $id_proveedor = $this->input->post("id_proveedor");

                $sw = true;
                $sw_resp = true;
                $error = array();
 
                $ver_str = [
                    'Lugar' => $id_lugar,
                    'Ubicacion' => $id_ubicacion,
                    'Responsables' => $responsables,
                    'Serial' => $serial,
                    'Tipo Modulo' => $tipo_modulo,
                    'Descripcion adicional' => $descripcion,
                ];

                $ver_num = [
                    'marca' => $marca,
                    'modelo' => $modelo,
                    'recurso' => $recurso
                ];

                if($tipo_modulo == "Inv_Lab") {
                    $ver_num['Uso del Activo'] = $uso_activo;
                    $ver_str['Nombre del Activo'] = $nombre_activo;
                    $ver_str['Referencia'] = $referencia;
                    $ver_str['Lugar de Origen'] = $lugar_origen;
                }

                $num = $this->verificar_campos_numericos($ver_num);
                $str = $this->verificar_campos_string($ver_str);
                $recurso_GEN = $this->genericas_model->obtener_valor_parametro_id_2($recurso);
                $prefijo = $this->add_prefijo($recurso_GEN);
                $validar_codigo_serial = $this->validar_codigo_serial($prefijo, $codigo_interno, $serial);
                $fecha_g = $this->validar_fecha($fecha_garantia);
                $fecha_i = $this->validar_fecha($fecha_ingreso);
                $fechas_info = $this->validateFechaMayor($fecha_ingreso,$fecha_garantia);

                if(empty($responsables)){   
                    $resp = ['mensaje'=>"Seleccione responsables.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else if ($validar_codigo_serial) {
                    $resp = $validar_codigo_serial;
                }else if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else if (is_array($num)) {
                    $resp = ['mensaje'=>"El campo ". $num['field'] ."  no debe estar vacio y debe ser numerico.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else if (!$fecha_i){
                    $resp = ['mensaje'=>"Digite una fecha de ingreso valida.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                } else if (!$fecha_g){
                    $resp = ['mensaje'=>"Digite una fecha de garantia valida.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                } else if ($fechas_info == -1) {
                    $resp = ['mensaje'=>"La fecha de ingreso no puede ser mayor a la fecha de garantia.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else {
                    
                    if($tipo_modulo === 'Inv_Aud' && empty($codigo_interno)){
                        $sw = false;
                        $resp = ['mensaje'=>"El campo código interno es obligatorio", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }

                    if ($recurso_GEN == 'Torre' || $recurso_GEN == 'PortMini' || $recurso_GEN == 'Port') {
                        $str_recurso = $this->verificar_campos_string(['Procesador'=>$procesador,'Sistema Operativo'=>$sistemaOperativo,'Memoria'=>$memoria,'Disco Duro'=>$discoDuro ]);
                        if (is_array($str_recurso)) {
                            $sw = false;
                            $resp = ['mensaje'=>"El campo ". $str_recurso['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }
                    }          
                    if (!empty($valor) && $sw) {
                        if (!is_numeric($valor)) {
                            $sw = false;
                            $resp = ['mensaje'=>"El campo valor debe ser numerico.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else if ($valor < 0) {
                            $sw = false;
                            $resp = ['mensaje'=>"El campo valor debe ser mayor a 0.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }
                    }    
                    if ((!empty($fecha_inicio_proyecto) || !empty($fecha_fin_proyecto)) && $sw) {
                        if (empty($fecha_inicio_proyecto)) {
                            $sw = false;
                            $resp = ['mensaje'=>"El campo fecha inicio del proyecto no puede estar vacia.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else  if (empty($fecha_fin_proyecto)) {
                            $sw = false;
                            $resp = ['mensaje'=>"El campo fecha fin del proyecto no puede estar vacia.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        } else {
                            $fecha_g = $this->validar_fecha($fecha_fin_proyecto);
                            $fecha_i = $this->validar_fecha($fecha_inicio_proyecto);
                            if (!$fecha_i){
                                $sw = false;
                                $resp = ['mensaje'=>"Ingrese una fecha inicio de proyecto valida.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                            } else if (!$fecha_g){
                                $sw = false;
                                $resp = ['mensaje'=>"Ingrese una fecha fin de proyecto valida.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                            }else{
                                $fechas_info = $this->validateFechaMayor($fecha_inicio_proyecto,$fecha_fin_proyecto);
                                if ($fechas_info == -1) {
                                    $sw = false;
                                    $resp = ['mensaje'=>"La fecha inicio de proyecto no puede ser mayor a la fecha fin de proyecto.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                                }
                            } 
                        }
                        if ($id_codigo_sap == 'null' || $id_codigo_sap == null) {
                            $sw = false;
                            $resp = ['mensaje'=>"El codigo sap no puede estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }
                    }
                    

                    if($sw){
                        $data_inventario = [
                            "serial" => $serial,
                            "descripcion" => $descripcion,
                            "fecha_ingreso" => $fecha_ingreso,
                            "fecha_garantia" => $fecha_garantia,
                            "valor" => $valor,
                            "tipo" => $recurso,
                            "id_modelo" => $modelo,
                            "id_marca" => $marca,
                            "usuario_registra" => $_SESSION['persona'],
                            "codigo_interno" => $codigo_interno ? "$prefijo$codigo_interno" : null,
                            "tipo_modulo" => $tipo_modulo,
                            "id_codigo_sap" => $id_codigo_sap != 'null' ? $id_codigo_sap : null,
                            "fecha_inicio_proyecto" => $fecha_inicio_proyecto ? $fecha_inicio_proyecto : null,
                            "fecha_fin_proyecto" => $fecha_fin_proyecto ? $fecha_fin_proyecto : null,
                            "uso_del_activo" => $uso_activo ? $uso_activo : null,
                            "nombre_activo" => $nombre_activo ? $nombre_activo : null,
                            "referencia" => $referencia ? $referencia : null,
                            "lugar_origen" => $lugar_origen ? $lugar_origen : null,
                            "observaciones" => $observaciones ? $observaciones : null,
                            "proveedor" => $id_proveedor ? $id_proveedor : null,
                        ];
                        
                        
                        $id_recurso = $this->inventario_model->guardar_datos($data_inventario, 'inventario');

                        if(!$id_recurso) $resp = ['mensaje'=>"Error al guardar el registro, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        else{
                            $id_recurso = $this->inventario_model->obtener_ultimo_registro()[0]["id"];
                            if ($recurso_GEN == 'Torre' || $recurso_GEN == 'PortMini' || $recurso_GEN == 'Port') {
                                $data_detalle = [
                                    "id_inventario" => $id_recurso,
                                    "procesador" => $procesador,
                                    "memoria" => $memoria,
                                    "disco_duro" => $discoDuro,
                                    "sistema_operativo" => $sistemaOperativo,
                                    "usuario_registra" => $_SESSION['persona'],
                                ];
                                $add_detalle = $this->inventario_model->guardar_datos($data_detalle, 'detalle_inventario');
                                $resp= ['mensaje'=>"Error al guardar el registro, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                                if($add_detalle != 1) array_push($error, 'Error al guardar el detalle del artículo.');
                            }

                            $data_lugar = [
                                "id_lugar" => $id_lugar,
                                "id_ubicacion" => $id_ubicacion,
                                "id_inventario" => $id_recurso,
                                "id_usuario_asigna" => $_SESSION['persona'],
                            ];

                            $add_lugar = $this->inventario_model->guardar_datos($data_lugar, 'inventario_lugares');
                            if($add_lugar != 1) array_push($error, 'Error al asignar un lugar.');

                            foreach($responsables as $responsable){
                                array_push($data_responsables,[
                                    "id_inventario" => $id_recurso,
                                    "id_persona" => $responsable['id'],
                                    "id_usuario_asigna" => $_SESSION['persona'],
                                ]);
                            }

                            $add_respon = $this->inventario_model->guardar_datos($data_responsables, 'inventario_responsables',2);
                            if($add_respon != 1) array_push($error, 'Error al guardar el responsable.');

                            $resp = [
                                'mensaje' => "Registro guardado exitosamente.",
                                'tipo' => "success",
                                'titulo' => "Proceso Exitoso.!",
                                "errores" => $error,
                                "id" => $id_recurso
                            ];
                        }
                    }
                }

            }
            echo json_encode($resp);

        }

    }


	public function verificar_perifericos(){
		if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
                $tipo = $this->input->post("tipo");
				$serial = trim($this->input->post("serial"));
				$codigo_interno = trim($this->input->post("codigo_interno"));
				$marca = $this->input->post("marca");
				$modelo = $this->input->post("modelo");
				$tipo_periferico = $this->input->post("tipo_periferico");
				// $fecha_ingreso = $this->input->post("fecha_ingreso");
				// $fecha_garantia = $this->input->post("fecha_garantia");
				// $marca_periferico = $this->input->post("marca_periferico");
				// $modelo_periferico = $this->input->post("modelo_periferico");
				$valor = $this->input->post("valor");
				$descripcion = $this->input->post("descripcion");
				$num = $this->verificar_campos_numericos(['tipo'=>$tipo]);
				// $num = $this->verificar_campos_numericos(['marca'=>$marca,'modelo'=>$modelo, 'tipo'=>$tipo]);
                $str = $this->verificar_campos_string(['serial'=>$serial]);
                //  $fechas_info = $this->validateFechaMayor($fecha_ingreso,$fecha_garantia);

                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else  if (!empty($valor) && (!is_numeric($valor) || $valor < 0)) {
                    $resp = ['mensaje'=>"El campo valor debe ser un numero mayor a 0.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if (is_array($num)) {
                    $campo = $num['field'];
                    $resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vació.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else{
                    $sw = true;
                    // if (!empty($fecha_ingreso)) {
                    //     $fecha_i = $this->validar_fecha($fecha_ingreso);
                    //     if (!$fecha_i){
                    //         $resp = ['mensaje'=>"Ingrese una fecha de ingreso valida.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    //         $sw = false;
                    //     }
                    // }
                    //  if (!empty($fecha_garantia)) {
                    //     $fecha_g = $this->validar_fecha($fecha_garantia);
                    //     if (!$fecha_g){
                    //         $resp = ['mensaje'=>"Ingrese una fecha de garantia valida.",'tipo'=>"info",'titulo'=> "Oops.!"];
                    //         $sw = false;
                    //     }
                    // }

                    if ($sw) {
                        $recurso_GEN = $this->genericas_model->obtener_valor_parametro_id_2($tipo);
                        $prefijo = $this->add_prefijo($recurso_GEN);
                        if($codigo_interno || $codigo_interno != '') $existe_codigo_serial = $this->validar_codigo_serial($prefijo, $codigo_interno, $recurso_GEN);
                        // if($codigo_interno || $codigo_interno != '') $existe_codigo_serial = $this->validar_codigo_serial("$prefijo$codigo_interno", $recurso_GEN);
                        else $existe_codigo_serial = null;

                        // $existe_codigo_serial = $this->validar_codigo_serial("$prefijo$codigo_interno", $recurso_GEN);
                        if ($existe_codigo_serial) {
                            $resp = $existe_codigo_serial;
                        }else{
                            $data = [
                            'tipo' => $tipo,
                            'serial' => $serial,
                            'codigo_interno' => $codigo_interno ? $codigo_interno : '',
                            // 'marca' => $marca,
                            // 'modelo' => $modelo,
                            // 'fecha_ingreso' => $fecha_ingreso,
                            // 'fecha_garantia' => $fecha_garantia,
                            'valor' => $valor,
                            'descripcion' => $descripcion,
                            'prefijo' => $codigo_interno ? $prefijo : '', 
                            'tipo_periferico' => $tipo_periferico,
                            // 'marca_periferico' => $marca_periferico,
                            // 'modelo_periferico' => $modelo_periferico,
                            ];
                            $resp = ['mensaje'=>"Periférico Asignado exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso!", "data" => $data];

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
			if (!is_numeric($row)) {
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

    /**
     * Se guarda uno o varios perifericos a un recurso en especificos, se valida que el periferico seleccionado no lo tenga asignado otro recurso
     * @return Integer
     */
    public function guardar_perifericos() {
        if ($this->Super_estado == false) {
            echo json_encode(array("sin_session"));
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(Array(-1302));
            } else {
                $ya_Asignados = Array();
                $id_recurso = $this->input->post("id_recurso");
                $perifericos = $this->input->post("perifericos");
                $usuario = $_SESSION['persona'];
                if (empty($perifericos)) {
                    echo json_encode(Array(3));
                    return;
                }
                if (empty($id_recurso)) {
                    echo json_encode(Array(4));
                    return;
                }



                for ($index2 = 0; $index2 < count($perifericos); $index2++) {
                    $asignado = $this->inventario_model->Periferico_ya_asignado($perifericos[$index2]);

                    if (!empty($asignado)) {
                        array_push($ya_Asignados, $asignado[0]["serial"]);
                    } else {
                        $perif = $this->inventario_model->guardar_perifericos($id_recurso, $perifericos[$index2], $usuario);
                    }
                }
                if (empty($ya_Asignados)) {
                    echo json_encode(Array(1));
                    return;
                }
                echo json_encode(Array(2, $ya_Asignados));
                return;
            }
        }
    }


    /**
     * Cambia a estado terminado el mantenimiento en curso de un recurso en especifico
     * @return integer
     */
    public function Terminar_Mantenimiento() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {
                $mantenimiento = $this->input->post("id");
                $inventario = $this->input->post("id_inventario");
                $usuario_termina = $_SESSION['persona'];
                $fecha = date("Y-m-d");
                $operar = $this->inventario_model->Terminar_Mantenimiento($mantenimiento, $inventario, $fecha, $usuario_termina);

                echo json_encode($operar);
            }
        }
	}
	
	public function terminar_mantenimiento_masivo(){
		$res = [];
		if ($this->Super_estado == false) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$recursos = $this->input->post('recursos');
			if(is_array($recursos) && count($recursos) > 0){
				$data = ['estado_mant' => 'Mat_Term', 'fecha_termina' => date("Y-m-d H:i:s"),'usuario_termina' => $_SESSION['persona']];
				foreach ($recursos as $recurso) {
					$this->pages_model->modificar_datos($data, 'mantenimiento', $recurso, 'id_inventario');
					$this->inventario_model->Modificar_estado_recurso($recurso, "RecAct", "", "");
				}
				$res = ['mensaje' => "Equipos reparados exitosamente",'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
			}else $res = ['mensaje' => "Debe seleccionar al menos un recurso para gestionar", 'tipo' => "info", 'titulo' => "Oops.!"];
		}
		echo json_encode($res);
	}

    /**
     * valdia si un periferico ya se encuentra asignado en un recurso en especifico
     * @return array
     */
    public function Periferico_ya_asignado() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            $periferico = $this->input->post("periferico");
            $asignado = $this->inventario_model->Periferico_ya_asignado($periferico);
            echo json_encode($asignado);
        }
    }

    /**
     * Actualiza el codigo interno de un recurso valida que el codigo no se modifique 
     * @return Integer
     */
    public function Modificar_codigo_interno() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
                return;
            } else {
                $id = $this->input->post("id");
                $codigo_interno = $this->input->post("codigo_interno");

                $existe_codigo = $this->inventario_model->Existe_codigo_interno($codigo_interno);
                if ($existe_codigo == true) {
                    echo json_encode(-11);
                    return;
                }
                $operar = $this->inventario_model->Modificar_codigo_interno($codigo_interno, $id);
                echo json_encode($operar);
                return;
            }
        }
	}
	
	/**
     * Trae los perifericos que que contengan la cadena de caractéres enviados.
     * @return String
     */
	public function Traer_perifericos(){
        $perifericos = [];
        $id_inventario = $this->input->post('id_inventario');
        $text = trim($this->input->post('text'));
		if ($this->Super_estado && $text && $id_inventario) {
            $tipo = $this->inventario_model->obtener_informacion_inventario($id_inventario)[0]['tipo'];
            $res = $this->inventario_model->Traer_perifericos($text, $tipo);
            foreach ($res as $row) {
                $row["accion"] = '<span style="color: #39B23B;" title="Seleccionar Persona" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>';
                $perifericos["data"][] = $row;
            }
        }
		echo json_encode($perifericos);
	}

    public function relacionar_periferico(){
        if ($this->Super_estado == false) {
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        } else {
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
				$recurso = $this->input->post("recurso");
				$id = $this->input->post("id");
                $num = $this->verificar_campos_numericos(['recurso' => $recurso, 'periferico' => $id]);
                $asignado = $this->inventario_model->Periferico_ya_asignado($id);
                if (is_array($num)) {
                    $campo = $num['field'];
                    $resp = ['mensaje' => "El campo $campo debe ser numérico y no puede estar vació.", 'tipo' => "info", 'titulo' => "Oops.!"]; 
                }else if (count($asignado)) {
                    $resp = ['mensaje'=>"El periferico selecionado se encuentra asignado al recurso con serial ".$asignado[0]['serial_recurso'].'.','tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else{
					if($id != $recurso) {
						$data = ["id_recurso" => $recurso,"id_periferico" => $id,"usuario_registra" => $_SESSION['persona']];
						$res = $this->pages_model->guardar_datos($data, 'perifericos_recursos');
						$resp = ['mensaje'=>"Periférico asignado exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"]; 
						if($res != 1) $resp = ['mensaje'=>"Error al relacional el periferico, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
					} else {
						$resp = [
							'mensaje' => "No se pude asignar el mismo recurso como periférico.",
							'tipo' => "info",
							'titulo' => "Oops!"
						];
					}
                }
			}
        }
        echo json_encode($resp);
	}

	public function validar_fecha($fecha){
        if(!empty($fecha)){
            $valores = explode('-', $fecha);
            if(count($valores) == 3 && checkdate($valores[1], $valores[2], $valores[0])){
                return true;
            }
        }
		return false;
	}

    public function modificar_inventario() {
        if ($this->Super_estado == false) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
                $tipo_modulo = $this->input->post("tipo_modulo");
                $sistemaOperativo = $this->input->post("sistema_operativo");
                $idinventario = $this->input->post("id_inventario");
                $uso_equipo = $this->input->post("uso_equipo");
                $procesador = $this->input->post("procesador");
                $discoDuro = $this->input->post("disco_duro");
                $memoria = $this->input->post("memoria");
                $descripcion = $this->input->post("descripcion");
                $fecha_inicio_proyecto = $this->input->post("fecha_inicio_proyecto");
                $fecha_fin_proyecto = $this->input->post("fecha_fin_proyecto");
                $id_codigo_sap = $this->input->post("id_codigo_sap");
                $serial = $this->input->post("serial");
                $codigo_interno = $this->input->post("codigo_interno");
                if($tipo_modulo == "Inv_Lab"){
                    $nombre_activo = $this->input->post("nombre_activo_modi");
                    $referencia = $this->input->post("referencia");
                    $lugar_origen = $this->input->post("lugar_origen");
                    $proveedor = $this->input->post("id_proveedor");
                    $observaciones = $this->input->post("observaciones");
                    $id_marca = $this->input->post("marca");
                    $id_modelo = $this->input->post("modelo_mod");
                    $valor = $this->input->post("valor");
                    $fecha_ingreso = $this->input->post("fecha_ingreso");
                    $fecha_garantia = $this->input->post("fecha_garantia");
                    $tipo = $this->input->post("tipo_activo");
                }
                
                $sw = true;
                $sw_resp = true;
                $error = array();
                $val_serial = null;
                $val_codigo_interno = null;

                $data_inventario = [];

                $inventario_datos = $this->inventario_model->obtener_inventario_id($idinventario);
                if($serial != $inventario_datos->{'serial'}){
                    $val_serial = $this->inventario_model->get_where('inventario', ['serial' => $serial, 'estado' => 1])->result_array();
                    if(count($val_serial) > 0){
                        echo json_encode(['mensaje'=>"El serial digitado ya se encuentra registrado.", 'tipo'=>"info", 'titulo'=> "Oops.!"]);
                        return;
                    }
                    $data_inventario["serial"] = $serial;
                }
                if($codigo_interno != $inventario_datos->{'codigo_interno'}){
                    $val_codigo_interno = $this->inventario_model->get_where('inventario', ['codigo_interno' => $codigo_interno, 'estado' => 1])->result_array();
                    if(count($val_codigo_interno) > 0){
                        echo json_encode(['mensaje'=>"El código interno digitado ya se encuentra registrado.", 'tipo'=>"info", 'titulo'=> "Oops.!"]);
                        return;
                    }
                    $data_inventario["codigo_interno"] = $serial;
                }
                
                $recurso = $inventario_datos->{'tipo'};
                $recurso_GEN = $this->genericas_model->obtener_valor_parametro_id_2($recurso);

                if ($recurso_GEN == 'Torre' || $recurso_GEN == 'PortMini' || $recurso_GEN == 'Port') {
                    $data = [];
                    if($tipo_modulo == 'Inv_Lab'){
                        $data = ['Uso del Activo' => $uso_equipo,
                        'Nombre del activo' => $nombre_activo,
                        'Referencia' => $referencia,
                        'Lugar de origen' => $lugar_origen,
                        'Proveedor' => $proveedor,
                        'Observacionesz' => $observaciones,
                        'Marca' => $id_marca,
                        'Modelo' => $id_modelo,
                    ];
                    } else{
                        $data = [
                            'Sistema Operativo' => $sistemaOperativo,
                            'Procesador' => $procesador,
                            'Disco Duro' => $discoDuro,
                            'Memoria' => $memoria
                        ];
                    }
                    $str_recurso = $this->verificar_campos_string($data);
                    if (is_array($str_recurso)) {
                        $sw = false;
                        $resp = ['mensaje'=>"El campo ". $str_recurso['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }
                }

                if ((!empty($fecha_inicio_proyecto) || !empty($fecha_fin_proyecto)) && $sw) {
                    if (empty($fecha_inicio_proyecto)) {
                        $sw = false;
                        $resp = ['mensaje'=>"El campo fecha inicio del proyecto no puede estar vacia.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else  if (empty($fecha_fin_proyecto)) {
                        $sw = false;
                        $resp = ['mensaje'=>"El campo fecha fin del proyecto no puede estar vacia.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    } else {
                        $fecha_g = $this->validar_fecha($fecha_fin_proyecto);
                        $fecha_i = $this->validar_fecha($fecha_inicio_proyecto);
                        $fecha_ingreso = $this->validar_fecha($fecha_ingreso);
                        $fecha_garantia = $this->validar_fecha($fecha_garantia);
                        if (!$fecha_i){
                            $sw = false;
                            $resp = ['mensaje'=>"Ingrese una fecha inicio de proyecto valida.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        } else if (!$fecha_g){
                            $sw = false;
                            $resp = ['mensaje'=>"Ingrese una fecha fin de proyecto valida.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else if(!$fecha_garantia){
                            $sw = false;
                            $resp = ['mensaje'=>"Ingrese una fecha de garantía valida.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else if(!$fecha_ingreso){
                            $sw = false;
                            $resp = ['mensaje'=>"Ingrese una fecha de ingreso valida.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }else{
                            $fechas_info = $this->validateFechaMayor($fecha_inicio_proyecto,$fecha_fin_proyecto);
                            if ($fechas_info == -1) {
                                $sw = false;
                                $resp = ['mensaje'=>"La fecha inicio de proyecto no puede ser mayor a la fecha fin de proyecto.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                            }
                        } 

                        if ($id_codigo_sap  == '0' || $id_codigo_sap == 'null' || !$id_codigo_sap) {
                            $sw = false;
                            $resp = ['mensaje'=>"El codigo sap no puede estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                        }
                    }
                } 

                if($sw){
                    if($tipo_modulo != 'Inv_Lab'){
                        if($descripcion != $inventario_datos->{'descripcion'}) $data_inventario['descripcion'] = $descripcion;
                        if($id_codigo_sap != $inventario_datos->{'id_codigo_sap'}) $data_inventario['id_codigo_sap'] = $id_codigo_sap;
                        if($fecha_inicio_proyecto != $inventario_datos->{'fecha_inicio_proyecto'}) $data_inventario['fecha_inicio_proyecto'] = $fecha_inicio_proyecto;
                        if($fecha_fin_proyecto != $inventario_datos->{'fecha_fin_proyecto'}) $data_inventario['fecha_fin_proyecto'] = $fecha_fin_proyecto;
                        if($fecha_fin_proyecto != $inventario_datos->{'fecha_fin_proyecto'}) $data_inventario['fecha_fin_proyecto'] = $fecha_fin_proyecto;
                        $data_inventario['id_usuario_modifica'] = $_SESSION['persona'];
                    } else $data_inventario = [
                        'uso_del_activo' => $uso_equipo,
                        'nombre_activo'=> $nombre_activo,
                        'referencia'=> $referencia,
                        'lugar_origen'=> $lugar_origen,
                        'proveedor'=> $proveedor,
                        'observaciones'=> $observaciones,
                        'id_marca'=> $id_marca,
                        'id_modelo'=> $id_modelo,
                        'valor'=> $valor,
                        'fecha_ingreso'=> $fecha_ingreso,
                        'fecha_garantia'=> $fecha_garantia,
                        'descripcion' => $descripcion,
                        'tipo'=> $tipo,
                        'serial'=> $serial,
                        'codigo_interno'=> $codigo_interno,
                        "id_usuario_modifica" => $_SESSION['persona'],
                    ];

                    $mod_inventario = $this->pages_model->modificar_datos($data_inventario, 'inventario', $idinventario);
                    if($mod_inventario != 1) $resp = ['mensaje'=>"Error al modificar el registro, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    else{
                        
                        if ($recurso_GEN == 'Torre' || $recurso_GEN == 'PortMini' || $recurso_GEN == 'Port') {
                            $detalle_datos = $this->inventario_model->obtener_detalle_Recurso($idinventario);

                            if(count($detalle_datos)){
                                $id_detalle = $detalle_datos['id'];
                                $data_detalle = [
                                    "sistema_operativo" => $sistemaOperativo,
                                    "procesador" => $procesador,
                                    "disco_duro" => $discoDuro,
                                    "memoria" => $memoria,
                                    "id_usuario_modifica" => $_SESSION['persona'],
                                ];
                                $mod_detalle = $this->pages_model->modificar_datos($data_detalle, 'detalle_inventario', $id_detalle);
                                if($mod_detalle != 1) array_push($error, 'Error al modificar el detalle del artículo.');
                                $resp = ['mensaje'=>"Registro modificado exitosamente.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!", "errores" => $error];
                            } else {
                                $data_detalle = [
                                    "id_inventario" => $idinventario,
                                    "sistema_operativo" => $sistemaOperativo,
                                    "procesador" => $procesador,
                                    "disco_duro" => $discoDuro,
                                    "memoria" => $memoria,
                                    "usuario_registra" => $_SESSION['persona'],
                                ];
                                $add = $this->talento_humano_model->guardar_datos($data_detalle, 'detalle_inventario');
                                $resp = !$add 
                                    ? ['mensaje'=>"Recurso modificado exitosamente!",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"]
                                    : ['mensaje'=>"Ha ocurrido un error al modificar el recurso.",'tipo'=>"info",'titulo'=> "Ooops!"];
                            }
                        } else $resp = ['mensaje'=>"Recurso modificado exitosamente!",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    }
                } else $resp = ["mensaje" => 'No se pudo modificar el recurso', 'tipo' => 'info', 'titulo' => 'Ooops!'];
            }
        }
        echo json_encode($resp);
    }
    



    /**
     * Modifica un recurso en especifico teniendo en cuenta que el serial y el codigo interno no se repita
     * @return Array
     */
    public function modificar_inventario_old() {
        if ($this->Super_estado == false) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            } else {
                $serial = $this->input->post("serial");
                $descripcion = $this->input->post("descripcion");
                $marca = $this->input->post("marca");
                $modelo = $this->input->post("modelo");
                $id = $this->input->post("id");
                $sistema_operativo = $this->input->post("sistema_operativo");
                $procesador = $this->input->post("procesador");
                $memoria = $this->input->post("memoria");
                $disco_duro = $this->input->post("disco_duro");
                $codigo_interno = $this->input->post("codigo_interno");
                $id_detalle_inventario = $this->input->post("id_detalle_inventario");
                $valor = $this->retornar_null($this->input->post("valor"));
                $fecha_garantia = $this->retornar_null($this->input->post("fecha_garantia"));
                $fecha_ingreso =$this->retornar_null($this->input->post("fecha_ingreso"));
                $num = $this->verificar_campos_numericos(['marca'=>$marca,'modelo'=>$modelo]);
                $num_detalle = $this->verificar_campos_string(['Sistema Operativo'=>$sistema_operativo,'Procesador'=>$procesador, 'Disco Duro'=>$disco_duro, 'Memoria'=>$memoria]);
                $str = $this->verificar_campos_string(['serial'=>$serial, 'codigo_interno'=>$codigo_interno]);
                $datos = $this->inventario_model->obtener_Datos_inventario($id);
                $recurso = $datos[0]['tipo'];
                $fecha_inicio_proyecto = $this->input->post("fecha_inicio_proyecto");
                $fecha_fin_proyecto = $this->input->post("fecha_fin_proyecto");
                $id_codigo_sap = $this->input->post("id_codigo_sap");

                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if (is_array($num)) {
                    $campo = $num['field'];
                    $resp = ['mensaje'=>"El campo $campo debe ser numérico y no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else {
                    $sw = true;
                    $recurso_GEN = $this->genericas_model->obtener_valor_parametro_id_2($recurso);
                    if (is_array($num_detalle) && ($recurso_GEN == 'Torre' || $recurso_GEN == 'PortMini' || $recurso_GEN == 'Port')) {
                        $campo = $num_detalle['field'];
                        $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                    }else {
                        if (!empty($valor)) {
                            if ( (!is_numeric($valor) || $valor < 0)) {
                                $resp = ['mensaje'=>"El campo valor debe ser un numero mayor a 0.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                                $sw = false;
                            }
                        }else{
                            $valor = null;
                        }
    
                        if (!empty($fecha_ingreso) && $sw) {
                            $fecha_i = $this->validar_fecha($fecha_ingreso);
                            if (!$fecha_i){
                                $resp = ['mensaje'=>"Ingrese una fecha de ingreso valida.",'tipo'=>"info",'titulo'=> "Oops.!"];
                                $sw = false;
                            }
                        }else{
                            $fecha_ingreso = null;
                        }
    
                         if (!empty($fecha_garantia) && $sw) {
                            $fecha_g = $this->validar_fecha($fecha_garantia);
                            if (!$fecha_g){
                                $resp = ['mensaje'=>"Ingrese una fecha de garantia valida.",'tipo'=>"info",'titulo'=> "Oops.!"];
                                $sw = false;
                            }
                        }else{
                            $fecha_garantia = null;
                        }
                        if ($sw) {
                            //Verifico si exite el código interno
                            if ($datos[0]['codigo_interno'] != $codigo_interno) {
                                $existe = $this->inventario_model->Existe_codigo_interno($codigo_interno);
                                if ($existe) {
                                    $resp = ['mensaje'=>"El codigo interno ya se encuentra registrado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                                    $sw = false;
                                }
                            }
                
                            //Verifico si existe serial
                            if ($datos[0]['serial'] != $serial && $sw) {
                                $existe = $this->inventario_model->Existe_serial($serial);
                                if ($existe) {
                                    $resp = ['mensaje'=>"El serial ya se encuentra registrado.",'tipo'=>"info",'titulo'=> "Oops.!"];
                                    $sw = false;
                                }
                            }
                            if ($sw) {
                                $resp= ['mensaje'=>"Los datos fueron modificados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                                $resultado = $this->inventario_model->Modificar($marca, $modelo, $serial, $descripcion, $id, $codigo_interno,$valor,$fecha_garantia,$fecha_ingreso,$id_codigo_sap, $fecha_inicio_proyecto, $fecha_fin_proyecto);                              
                            }
                            if($recurso_GEN == 'Torre' || $recurso_GEN == 'PortMini' || $recurso_GEN == 'Port'){
                                $data_detalle = [
                                    "sistema_operativo" => $sistema_operativo,
                                    "procesador" => $procesador,
                                    "memoria" => $memoria,
                                    "disco_duro" => $disco_duro,
                                ];
                                $del = $this->pages_model->modificar_datos($data_detalle,'detalle_inventario',$id_detalle_inventario);
                                if($del != 1) $resp = ['mensaje'=>"Error al modificar el detalle del inventario, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                            }

                        }
                    }
           
                }
            }
        }
        echo json_encode($resp);
    }
    public function get_route(){
		$pages = $_SERVER['REQUEST_URI'];
		$pos = strrpos($pages, "index.php/");
		$pages =  preg_replace('/[0-9]+/', '', substr($pages, $pos+10, strlen($pages)));
		$cant = strlen($pages);
		if($pages[$cant-1] == '/') $pages = substr($pages, 0, -1);
		return $pages;
    }
    public function retornar_null($text){
		return empty($text) ? null : $text;
    }
    public function listar_personas_cargos() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            $id = $this->input->post("id");
            $resp = $this->inventario_model->listar_personas_cargos($id);
            echo json_encode($resp);
        }
    }
    public function listar_permisos_parametros(){
        $id_principal = $this->input->post("id_principal");
        $resp = $this->Super_estado == true ? $this->inventario_model->listar_permisos_parametros( $id_principal ) : array();
        echo json_encode($resp);
	}
    public function buscar_persona(){
        $personas = array();
        if ($this->Super_estado) {
            $dato = $this->input->post('dato');
            $buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1";
            if (!empty($dato)) $personas = $this->inventario_model->buscar_persona($buscar);  
        }
        echo json_encode($personas);
    }

    public function listar_lugares(){
        $id_inventario = $this->input->post("id_inventario");
        $datos = $this->Super_estado == true ? $this->inventario_model->listar_lugares( $id_inventario ) : array();
        $lugares = array();
        
        $i = 1;
        $ver_solicitado = "<span title='Mas Informacion' data-toggle='popover' data-trigger='hover' style='background-color: white;color: black; width: 100%;' class='pointer form-control ver' ><span >ver</span></span>";
        $ver_inactivo = '<span  style="background-color: #d9534f;color: white;width: 100%;" class="pointer form-control" id="ver_detalle"><span >ver</span></span>';      

        foreach ($datos as $row) {
            $row['ver'] = $ver_solicitado;
            $estado = $row['estado'];
            if ($estado == 'ResTras') {
                $row['ver'] = $ver_inactivo;
            }
            $row["indice"] = $i;
            $lugares["data"][] = $row;
            $i++;
        }
        echo json_encode($lugares);
        return;
    }
    public function guardar_lugar_nuevo() {
		if ($this->Super_estado == false) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
           } else {
                if ($this->Super_agrega == 0) {
                    $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
                } else {
					$articulos = $this->input->post("articulos");
                    $id_lugar = $this->input->post("id_lugar");
					$id_ubicacion = $this->input->post("id_ubicacion");
					if((!is_array($articulos) && !is_numeric($articulos)) || (is_array($articulos) && count($articulos) <= 0)){
						$resp = ['mensaje' => "Debe seleccionar al menos un recurso para gestionar", 'tipo' => "info", 'titulo' => "Oops.!"];
					}else {
                        $sw = false;
                        $data_traslado = ["estado" => 'ResTras', "fecha_retira" => date("Y-m-d H:i:s"), "id_usuario_retira" => $_SESSION['persona'] ];
						$str = $this->verificar_campos_string(['Ubicacion'=>$id_ubicacion, 'Lugar'=>$id_lugar]);
						if (is_array($str)) {
							$resp = ['mensaje' => "El campo ". $str['field'] ."  no debe estar vacio.", 'tipo' => "info", 'titulo' => "Oops.!"];
						}else if(is_numeric($articulos)){
                            $ultimo = $this->inventario_model->traer_ultimo_lugar_inventario($articulos);
                            $id_ultimo =  $ultimo ? $ultimo[0]['id'] : null;
                            $lugar_ultimo = $ultimo ?  $ultimo[0]['id_lugar'] : null;
                            $ubicacion_ultimo = $ultimo ? $ultimo[0]['id_ubicacion'] : null;
                            if(($lugar_ultimo == $id_lugar)  && ($ubicacion_ultimo == $id_ubicacion)){
                                $resp = ['mensaje' => "Ingrese un lugar y una ubicacion diferente a la actual.", 'tipo' => "info", 'titulo' => "Oops."];
                            }else{
                                $del = $this->pages_model->modificar_datos($data_traslado, 'inventario_lugares', $id_ultimo);
                                if($del != 1){
                                    $resp = [
                                        'mensaje' => "Error al modificar ultimo lugar, contacte con el administrador.",
                                        'tipo' => "error",
                                        'titulo' => "Oops.!"
                                    ];
                                }else{
                                    $data = [
                                        "id_lugar" => $id_lugar,
                                        "id_ubicacion" => $id_ubicacion,
                                        "id_anterior" => $id_ultimo,
                                        "id_inventario" => $articulos,
                                        "id_usuario_asigna" => $_SESSION['persona'],
                                    ];
                                    $add = $this->pages_model->guardar_datos($data, 'inventario_lugares');
                                    $resp = $add != 1 ? ['mensaje'=>"Error al guardar el lugar nuevo, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]: ['mensaje'=>"El lugar del recurso fue modificado.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                                }
                            }
                        }else{
                            $data_articulos = array();
                            foreach ($articulos as $articulo) {
                                $ultimo = $this->inventario_model->traer_ultimo_lugar_inventario($articulo);
                                $id_ultimo =  $ultimo ? $ultimo[0]['id'] : null;
                                $lugar_ultimo = $ultimo ?  $ultimo[0]['id_lugar'] : null;
                                $ubicacion_ultimo = $ultimo ? $ultimo[0]['id_ubicacion'] : null;
                                if(($lugar_ultimo != $id_lugar)  || ($ubicacion_ultimo != $id_ubicacion)){
                                    $del = $this->pages_model->modificar_datos($data_traslado, 'inventario_lugares', $id_ultimo);
                                    if($del == 1){
                                        $sw = true;
                                        $data_lugar = [
                                            "id_lugar" => $id_lugar,
                                            "id_ubicacion" => $id_ubicacion,
                                            "id_anterior" => $id_ultimo,
                                            "id_inventario" => $articulo,
                                            "id_usuario_asigna" => $_SESSION['persona'],
                                        ];
                                        array_push($data_articulos, $data_lugar);
                                    }
                                }
                            }
							if($sw){
								$add = $this->pages_model->guardar_datos($data_articulos, 'inventario_lugares', 2);
								$resp = $add != 1 ? ['mensaje'=>"Error al guardar el lugar nuevo, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]: ['mensaje'=>"El lugar del recurso fue modificado.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
							}else  $resp = ['mensaje' => "Ingrese un lugar y una ubicacion diferente a la actual.", 'tipo' => "info", 'titulo' => "Oops."];
                            
						}
					}
					
                }
			}
        echo json_encode($resp);
	} 
	
    function eliminar_responsable_asignado()
    {
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_elimina == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                $id = $this->input->post("id");
                $id_inventario = $this->input->post("id_inventario");
                $usuario_retira = $_SESSION["persona"];
                $fecha = date("Y-m-d H:i:s");
                $total_resp = $this->inventario_model->cantidad_responsables($id_inventario);
                if ($total_resp == 1) {
                    $resp = ['mensaje'=> "El recurso no puede estar sin responsable.", 'tipo'=>"info", 'titulo'=> "Oops."];
                }else{
                    $data = [
                        "id_usuario_retira" => $usuario_retira,
                        "fecha_elimina" => $fecha,
                        "estado" => 0,
                        ];
                    $resp= ['mensaje'=>"El responsable fue eliminado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    $del = $this->pages_model->modificar_datos($data,'inventario_responsables',$id);
                    if($del != 1)$resp= ['mensaje'=>"Error al eliminar el responsable, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }
    
    public function guardar_nuevo_responsable() {
		if ($this->Super_estado == false) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
           } else {
                if ($this->Super_agrega == 0) {
                    $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
                } else {
                    $id_persona = $this->input->post("id");
                    $id_inventario = $this->input->post("idinventario");
                    $id_usuario_asigna = $_SESSION["persona"];

                    $num = $this->verificar_campos_numericos(['Id persona' => $id_persona,'Inventario' => $id_inventario]);
                    $persona_db = $this->inventario_model->buscar_responsable_id($id_persona, $id_inventario);

                    if (is_array($num)) {
                        $resp = ['mensaje'=>"El campo ". $num['field'] ."  no debe estar vacio y debe ser numerico.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else if(!is_null($persona_db)){
                        $resp = ['mensaje'=> "El usuario ya se encuentra asignado", 'tipo'=>"info", 'titulo'=> "Oops."];
                    }else{                     
                        $data = [
                            "id_inventario" => $id_inventario,
                            "id_persona" => $id_persona,
                            "id_usuario_asigna" => $_SESSION['persona'],
                        ];
                        $add = $this->pages_model->guardar_datos($data, 'inventario_responsables');
    
                        $resp = ['mensaje'=>"La solicitud fue guardada de forma exitosa.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($add != 1) $resp = ['mensaje'=>"Error al guardar el lugar nuevo, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    
                    }
                   }

			}
        echo json_encode($resp);
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
				$data = $this->inventario_model->buscar_valor_parametro($codigo, $idparametro);
				$resp = ['mensaje' => '', 'tipo' => 'success', 'titulo' => 'Busqueda realizada.!', 'data' => $data];
			}
		} else {
			$resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => 'Oops.!',];
		}

		echo json_encode($resp);
    }
    public function validateFechaMayor($fecha_inicio,$fecha_fin){
         
        ($fecha_fin >= $fecha_inicio) ? $resp = 1 : $resp = -1;
        return $resp;  
    }
    public function validar_codigo_serial($prefijo, $codigo_interno, $serial){
        $resp = '';
        $codigo_interno_ = $prefijo.''.$codigo_interno;
        if($codigo_interno){
            $existe_codigo_interno = $this->inventario_model->Existe_codigo_interno($codigo_interno_);
            if ($existe_codigo_interno) $resp = ['mensaje'=>"El código interno $codigo_interno ya se encuentra asignado.", 'tipo'=>"info", 'titulo'=> "Oops.!"]; 
        }
        $existe_serial = $this->inventario_model->Existe_serial($serial);
        if ($existe_serial) $resp = ['mensaje'=>"El serial $serial ya se encuentra asignado.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
        return $resp;
    }
    public function add_prefijo($recurso_GEN){
        $prefijo = '';
        if ($recurso_GEN == 'Teclado')  $prefijo = 'T-';
        else if ($recurso_GEN == 'Mouse')  $prefijo = 'R-';
        else if ($recurso_GEN == 'Monitor')  $prefijo = 'M-';
        return  $prefijo;
    }
    public function listar_recursos_inventario()  {        
        $id = $this->input->post("id_tipo");
        $resp = $this->Super_estado ? $this->inventario_model->listar_recursos_inventario($id) : array();
        echo json_encode($resp);
        return;
    }


    public function asignar_mantenimiento() {
		if ($this->Super_estado == false) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
           } else {
                if ($this->Super_agrega == 0) {
                    $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
                } else {
                    $id_mantenimiento = $this->input->post("tipo_mantenimiento");
                    $descripcion = $this->input->post("descripcion");
					$articulos = $this->input->post("articulos");
					$fecha_mantenimiento = $this->input->post("fecha_mantenimiento");
					$str = $this->verificar_campos_string(['Tipo de Mantenimiento' => $id_mantenimiento,'Descripcion' => $descripcion]);
                    
                    if (is_array($str)) {
                        $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    } else if((!is_array($articulos) && !is_numeric($articulos)) || (is_array($articulos) && count($articulos) <= 0)){
						$resp = ['mensaje'=>"Por favor seleccione al menos un artículo para modificar.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
					}else{
						if(is_array($articulos)){
							$data_articulos = [];
							foreach ($articulos as $item) {
								$data = [
									"id_inventario" => $item,
									"id_usuario" => $_SESSION['persona'],
									"fecha" => $fecha_mantenimiento,
									"id_tipo" => $id_mantenimiento,
									"descripcion" => $descripcion,
									"usuario_registra" => $_SESSION['persona'],
								];
								array_push($data_articulos, $data);
							}
							$mod = $this->pages_model->guardar_datos($data_articulos,'mantenimiento', 2);
						} else {
							$data = [
								"id_inventario" => $articulos,
								"id_usuario" => $_SESSION['persona'],
								"fecha" => $fecha_mantenimiento,
								"id_tipo" => $id_mantenimiento,
								"descripcion" => $descripcion,
								"usuario_registra" => $_SESSION['persona'],
							];
							$mod = $this->pages_model->guardar_datos($data,'mantenimiento');
						}
                        $resp = ['mensaje'=>"Mantenimiento asignado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                        if($mod != 1) $resp = ['mensaje'=>"Error al asignar el mantenimiento, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        else{
							$data_inventario = ["estado_recurso" => 'RecMat'];
							if(is_array($articulos)){
								foreach ($articulos as $item) {
									$this->pages_model->modificar_datos($data_inventario, 'inventario', $item);
								}
							} else $this->pages_model->modificar_datos($data_inventario, 'inventario', $articulos);
                        }
                    }
                   }
			}
        echo json_encode($resp);
    } 

    public function en_fecha_proyecto()  {        
        $resp = $this->Super_estado ? $this->inventario_model->en_fecha_proyecto() : array();
        echo json_encode($resp);
        return;
    }

    public function listar_modificaciones(){
        $id = $this->input->post("id");
        $resp = $this->Super_estado == true ? $this->inventario_model->listar_modificaciones( $id ) : array();
        echo json_encode($resp);
    }

    public function buscar_serial(){
        $serial = $this->input->post("serial");
        $resp = '';
        $existe_serial = $this->inventario_model->Existe_serial($serial);
        $recurso = $this->inventario_model->recurso_serial($serial);
        if($recurso){
            $id_recurso = $recurso -> {'id'};
            $tipo_modulo = $recurso -> {'tipo_modulo'};
        }
        if ($existe_serial) $resp = ['mensaje'=>"El serial $serial ya se encuentra asignado.", 'tipo'=>"info", 'id_recurso'=> $id_recurso,  'tipo_modulo'=> $tipo_modulo];
        else $resp = ['mensaje'=>"El serial <b>$serial</b> no esta registrado, diligencie los siguientes datos para crear el recurso.", 'tipo'=>"nuevo", 'serial'=> $serial];
        echo json_encode($resp);
	}
	
	public function listar_dependencias(){
		$data = [];
		if ($this->Super_estado == false) $data = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$tipo_modulo = $this->input->post('tipo_modulo');
            $tipo = $this->input->post('tipo');
            // Si tipo === 'dep' listará por dependencias
            // Si tipo === 'ubi' listará por ubicaciones
			if ($this->admin) {
                if($tipo === 'dep'){
                    $dependencias = $this->inventario_model->listar_dependencias($tipo_modulo);
                } else if($tipo === 'ubi'){
                    $dependencias = $this->inventario_model->listar_ubicaciones($tipo_modulo);
                }
				$data = [
					'dependencias' => $dependencias,
                    'ad' => $this->admin,
                    'tipo' => $tipo,
				];
			} else {
				$asignados = false;
				foreach ($this->permisos as $permiso) {
					if ($permiso['permiso'] === 'asignados') $asignados = true;
				}
                // $cantidad = $this->inventario_model->contar_cantidad_dep($tipo_modulo, $asignados, $_SESSION['persona']);
                // echo json_encode($cantidad);
                $dependencias = ($tipo_modulo == 'Inv_Lab') ? $this->inventario_model->listar_dependencias_usuario_lab($tipo_modulo) : $this->inventario_model->listar_dependencias_usuario($tipo_modulo, $asignados, $_SESSION['persona']); 
				$data = [
					'dependencias' => $dependencias,
					'ad' => $this->admin,
					'per' => $this->permisos
				];
			}
		}
		echo json_encode($data);
	}

	public function ubicaciones_dependencias(){
		$ubicaciones = [];
		if ($this->Super_estado == false) $ubicaciones = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$dependencia = $this->input->post('dependencia');
			$tipo_modulo = $this->input->post('tipo_modulo');
			$tipo_listar = $this->input->post('tipo_listar_dep');
            $ubicaciones = $this->admin 
                ? $this->inventario_model->ubicaciones_dependencias($dependencia, $tipo_modulo, $tipo_listar)
			    : ($tipo_modulo == 'Inv_Lab') ? $this->inventario_model->ubicaciones_dependencias_usuario_lab($dependencia, $tipo_modulo, $tipo_listar) : $this->inventario_model->ubicaciones_dependencias_usuario($dependencia, $tipo_modulo, $tipo_listar);
		}
		echo json_encode($ubicaciones);
	}

	public function agregar_responsable(){
		$res = [];
		if ($this->Super_estado == false) $res = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$articulos = $this->input->post('articulos');
			$retirar = $this->input->post('retirar');
			$responsable = $this->input->post('id');
			$num = $this->verificar_campos_numericos(['Reponsable' => $responsable]);
			$responsables_asignados = [];
			if (is_array($num)) {
				$resp = ['mensaje' => "El campo ". $num['field'] ."  no debe estar vacio y debe ser numerico.", 'tipo' => "info", 'titulo' => "Oops.!"];
			} else if((!is_array($articulos) && !is_numeric($articulos)) || (is_array($articulos) && count($articulos) <= 0)){
				$res = ['mensaje' => 'Por favor seleccione al menos un recurso para modificar', 'info', 'Ooops!'];
			} else {
                $res = ['mensaje' => 'El responsable ya se encuentra asignado al recurso.!', 'tipo' => 'info', 'titulo' => 'Ooops!'];
                $sw = false;
				if(is_array($articulos)){
					$data_articulos = [];
					foreach ($articulos as $item) {
						$persona_db = $this->inventario_model->buscar_responsable_id($responsable, $item);
						if(is_null($persona_db)) {
                            $sw = true;
							$data = ['id_inventario' => $item,'id_persona' => $responsable,'id_usuario_asigna' => $_SESSION['persona']];
							array_push($data_articulos, $data);
							if($retirar) $this->inventario_model->retirar_responsables_actuales($item);
						}
                    }
                    if($sw){
                        $res =['mensaje' => 'Responsable asignado exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
                        $resp = $this->pages_model->guardar_datos($data_articulos, 'inventario_responsables',2);
                        if($resp != 1) $res =['mensaje' => 'Error al intentar asignar el responsable', 'tipo' => 'error', 'titulo' => 'Ooops!'];
                    }
				} else {
                    $persona_db = $this->inventario_model->buscar_responsable_id($responsable, $articulos);
                    if(is_null($persona_db)) {
                        $res =['mensaje' => 'Responsable asignado exitosamente!', 'tipo' => 'success', 'titulo' => 'Proceso Exitoso!'];
                        $data_articulos = ['id_inventario' => $articulos,'id_persona' => $responsable,'id_usuario_asigna' => $_SESSION['persona']];
                        if($retirar) $this->inventario_model->retirar_responsables_actuales($articulos);
                        $resp = $this->pages_model->guardar_datos($data_articulos, 'inventario_responsables');
                        if($resp != 1)$res =['mensaje' => 'Error al intentar asignar el responsable', 'tipo' => 'error', 'titulo' => 'Ooops!'];
                    }
                }
			}
		}
		echo json_encode($res);
	}

	function get_modelos_marca() {
		$modelos = [];
		if (!$this->Super_estado) $modelos = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        else {
            $marca = $this->input->post("marca");
            $modelos= empty($marca)
                ? ['mensaje' => "Por favor seleccione una marca",'tipo'=>"info", 'titulo'=> "Oops.!"]
            	: $this->inventario_model->get_modelos_marca($marca);
        }
      	echo json_encode($modelos);
	}

	function gestionar_modelo(){
		$modelos = [];
		if ($this->Super_estado == false) {
            $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
			if ($this->Super_agrega == 0) {
				$resp= ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
			} else {
				$modelo = $this->input->post("id");
				$marca = $this->input->post("marca");
				$accion = $this->input->post("permiso");
				$res = $accion
					? $this->pages_model->eliminar_datos($accion, 'permisos_parametros')
					: $this->pages_model->guardar_datos(['vp_principal_id' => $marca,'vp_secundario_id' => $modelo], 'permisos_parametros');
				if($accion && $res == 1){
					$resp = ['mensaje' => "Modelo Desasignado exitosamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
				} else if($accion && $res == -1){
					$resp = ['mensaje' => "Error al intentar asignar este modelo. Por favor comuníquese con el administrador.", 'tipo' => "error", 'titulo' => "Ooops!"];
				} else if(!$accion && $res == 1){
					$resp = ['mensaje' => "Modelo Asignado exitosamente", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
				} else if(!$accion && $res == -1){
					$resp = ['mensaje' => "Error al intentar Desasignar este modelo. Por favor comuníquese con el administrador.", 'tipo' => "error", 'titulo' => "Ooops!"];
				} else $resp = ['mensaje' => "Ha ocurrido un error. Por favor comuníquese con el administrador.", 'tipo' => "error", 'titulo' => "Ooops!"];
			}
		}
		echo json_encode($resp);
	}

	function listar_personas(){
		if ($this->Super_estado == false) {
        	$personas = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
			$text = $this->input->post('text');
			$personas = $this->inventario_model->listar_personas($text);
		}
		echo json_encode($personas);
	}

	function gestionar_permiso_persona() {
		if ($this->Super_estado == false) {
        	$resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
			$persona = $this->input->post("persona");
			$permiso = $this->input->post("permiso");
			$sw = $this->input->post("sw");
			$accion = $this->input->post("accion");
			$num = $this->verificar_campos_numericos(['Persona'=>$persona]);
			$str = $this->verificar_campos_string(['Permiso'=>$permiso, 'Accion' => $accion]);
			if (is_array($str)) {
				$resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
			}else if (is_array($num)) {
				$resp = ['mensaje'=>"El campo ". $num['field'] ."  no debe estar vacio y debe ser numerico.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
			}else {
				$res = $this->inventario_model->get_where('permisos_personas_inventario', ['persona_id' => $persona, 'permiso' => $permiso])->row();
				if(!$res->id){
					$data = ['persona_id' => $persona, 'permiso' => $permiso, "$accion" => 1];
					$saved = $this->pages_model->guardar_datos($data, 'permisos_personas_inventario');
					$resp = $saved == 1
						? 	['mensaje' => 'Permiso asignado exitosamente', 'titulo' => 'Proceso Exitoso!', 'tipo' => 'success']
						: 	['mensaje' => 'Error al asignar el permiso', 'titulo' => 'Ooops!', 'tipo' => 'error'];
				} else {
					$data = ["$accion" => !$res->$accion];
					$modified = $this->pages_model->modificar_datos($data, 'permisos_personas_inventario', $res->id);
					$resp = $modified == 1
						? 	['mensaje' => 'Permiso cambiado exitosamente', 'titulo' => 'Proceso Exitoso!', 'tipo' => 'success']
						: 	['mensaje' => 'Error al cambiar el permiso', 'titulo' => 'Ooops!', 'tipo' => 'error'];
				}
			}
		}
		echo json_encode($resp);
	}

	public function get_permisos_persona(){
		if ($this->Super_estado == false) {
        	$personas = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        } else {
			$persona = $this->input->post('persona');
			$permisos = $this->inventario_model->get_where('permisos_personas_inventario', ['persona_id' => $persona])->row();
		}
		echo json_encode($permisos);
	}

	public function listar_tipos_recursos_asignados(){
		if ($this->Super_estado == false) {
        	$personas = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        } else {
			$recurso = $this->input->post('recurso');
			$data = $this->inventario_model->listar_tipos_recursos_asignados($recurso);
		}
		echo json_encode($data);
    }
    
    public function guardar_datos_tecnicos(){
        $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        if($this->Super_estado){
            $id = $this->input->post("id");
            $estado_activo = $this->input->post("estado_activo");
            $peso = $this->input->post("peso");
            $unidades = $this->input->post("unidades");
            $potencia = $this->input->post("potencia");
            $tecnologia = $this->input->post("tecnologia");
            $vida_util = $this->input->post("vida_util");
            $voltaje = $this->input->post("voltaje");
            $fase = $this->input->post("fase");
            $tipo_modulo = $this->input->post("tipo_modulo");

            $num_fields = [
                "Recurso" => $id,
                "Peso" => $peso,
                "Vida Util" => $vida_util,
                "Unidades" => $unidades,
                "Voltaje" => $voltaje,
                "Fases" => $fase,
                "Tecnología" => $tecnologia,
                'Potencia' => $potencia,
            ];
            $num = $this->verificar_campos_numericos($num_fields);
			$str = $this->verificar_campos_string(['Estado del artìculo' => $estado_activo]);
			if (is_array($str)) {
				$resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
			}else if (is_array($num)) {
				$resp = ['mensaje'=>"El campo ". $num['field'] ."  no debe estar vacio y debe ser numerico.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
			}else {
                $validar = $this->inventario_model->get_where("datos_tecnicos", ["inventario_id" => $id])->result_array();
                $sw = count($validar) ? false : true;
                if($sw){
                    $data = [
                        "inventario_id" => $id,
                        "tecnologia" => $tecnologia,
                        "fase" => $fase,
                        "vida_util" => $vida_util,
                        "peso" => $peso,
                        "potencia" => $potencia,
                        "unidades_id" => $unidades,
                        "voltaje" => $voltaje,
                        "usuario_registra" => $_SESSION['persona'],
                    ];
                    $add = $this->inventario_model->guardar_datos($data, 'datos_tecnicos');
                    if($add){
                       $this->inventario_model->modificar_datos(['estado_recurso' => $estado_activo], 'inventario', $id);
                        $resp = [
                            'mensaje' => "Datos técnicos agregados exitosamente al recurso.",
                            'tipo' => "success",
                            'titulo' => "Proceso Exitoso!"
                        ];
                    }
                } else {
                    $data = [
                        "inventario_id" => $id,
                        "tecnologia" => $tecnologia,
                        "fase" => $fase,
                        "vida_util" => $vida_util,
                        "peso" => $peso,
                        "potencia" => $potencia,
                        "unidades_id" => $unidades,
                        "voltaje" => $voltaje,
                    ];
                    $upd = $this->inventario_model->modificar_datos($data, 'datos_tecnicos', $id, 'inventario_id');
                    if ($upd) $this->inventario_model->modificar_datos(['estado_recurso' => $estado_activo], 'inventario', $id, 'inventario_id');
                    $resp = [
                            'mensaje' => "Datos técnicos actualizados con exito.",
                            'tipo' => "success",
                            'titulo' => "Proceso Exitoso!"
                        ];
                }// } else {$resp = ['mensaje'=>"El Recurso ya tiene datos técnicos", 'tipo'=>"info", 'titulo'=> "Oops.!"];}
            }
        }
        echo json_encode($resp);
    }

    public function cargar_requerimientos_tecnicos() {
        $requerimientos = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        if($this->Super_estado) {
            $id = $this->input->post("id");
            $num = $this->verificar_campos_numericos(["Recurso" => $id]);
			$requerimientos = is_array($num)
				? ['mensaje'=>"El campo ". $num['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"]
			    : $this->inventario_model->cargar_requerimientos_tecnicos($id);
        }
        echo json_encode($requerimientos);
    }

    public function gestionar_requerimiento(){
        $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        if($this->Super_estado) {
            $recurso_id = $this->input->post("id");
            $requerimiento = $this->input->post("requerimiento");
            $asignado = $this->input->post("asignado");

            $num = $this->verificar_campos_numericos(["Recurso" => $recurso_id]);
			if (is_array($num)) {
				$resp = ['mensaje'=>"El campo ". $num['field'] ."  no debe estar vacio y debe ser numerico.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
			} else {
                if(!$asignado){
                    $data = [
                        "inventario_id" => $recurso_id,
                        "requerimiento_id" => $requerimiento,
                        "usuario_registra" => $_SESSION['persona']
                    ];
                    $add = $this->inventario_model->guardar_datos($data, 'requerimientos_recurso');
                    $resp = $add ? 
                        [
                            "mensaje" => "Requerimiento agregado exitosamente",
                            'tipo' => "success",
                            'titulo' => "Proceso Exitoso!"
                        ] :
                        [
                            "mensaje" => "Ha ocurrido un error al intentar agregar el requerimiento al recurso",
                            'tipo' => "error",
                            'titulo' => "Ooops!"
                        ];
                } else {
                    $data = [
                        "estado" => 0,
                        "usuario_elimina" => $_SESSION['persona'],
                        "fecha_elimina" => date("Y-m-d H:i:s"),
                    ];
                    $mod = $this->pages_model->modificar_datos($data, "requerimientos_recurso", $asignado);
                    $resp = $mod == 1 ? 
                        [
                            "mensaje" => "Requerimiento retirado exitosamente",
                            'tipo' => "success",
                            'titulo' => "Proceso Exitoso!"
                        ] :
                        [
                            "mensaje" => "Ha ocurrido un error al intentar retirar el requerimiento al recurso",
                            'tipo' => "error",
                            'titulo' => "Ooops!"
                        ];
                }
            }

        }
        echo json_encode($resp);
    }

    public function cargar_documentos_disponibles(){
        $documentos = [
            'mensaje' => "",
            'tipo' => "sin_session",
            'titulo' => ""
        ];
        if($this->Super_estado) {
            $id = $this->input->post("id");
            $documentos = $this->inventario_model->cargar_documentos_disponibles($id);
        }
        echo json_encode($documentos);
    }

    public function agregar_mantenimiento(){
        $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        if ($this->Super_estado) {
            $sw = false;
            $file = $this->cargar_archivo("file_cotizacion", $this->ruta_adjuntos, 'cot');
            if ($file[0] == -1){
                $error = $file[1];
                $resp = $error == "<p>You did not select a file to upload.</p>"
                    ? ['mensaje'=>"Debe adjuntar la cotización.",'tipo'=>"info",'titulo'=> "Oops.!"]
                    : ['mensaje'=>"Error al cargar la cotización.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
            }else {
                $id = $this->input->post('id');
                $tipo = $this->input->post('tipo_mantenimiento');
				$nombre_archivo = $this->input->post('cotizacion');
				$descripcion = $this->input->post('descripcion');
				$fecha = $this->input->post('fecha_mantenimiento');
				$cotizacion = $file[1];
				$data = [
                    'id_inventario' => $id,
                    'id_tipo' => $tipo,
                    'id_usuario' => $_SESSION['persona'],
                    'estado_mant' => 'Mat_Pend',
					'documento' => $cotizacion,
					'nombre_documento' => $nombre_archivo,
					'usuario_registra' => $_SESSION['persona'],
                    'fecha' => $fecha,
                    'descripcion' => $descripcion
				];
                $add = $this->inventario_model->guardar_datos($data, 'mantenimiento');
                if($add) $sw = true;
            }
            if($sw) $resp = ['mensaje'=>"Mantenimiento registrado exitosamente!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
        }
		echo json_encode($resp);
    }

    public function cargar_documento_articulo(){
		if (!$this->Super_estado) $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$id = $this->input->post("id");
			$tipo = $this->input->post("tipo");
            $nombre = $_FILES["file"]["name"];
            
            $num_fields = [
                "Artículo" => $id,
                "Tipo de Documento" => $tipo,
            ];

            $num = $this->verificar_campos_numericos($num_fields);

			if (is_array($num)) {
				$resp = ['mensaje'=>"El campo ". $num['field'] ."  no debe estar vacio y debe ser numerico.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
			}else {
                $cargo = $this->cargar_archivo("file", $this->ruta_documentos, "docum");
                if ($cargo[0] == -1) {
                    header("HTTP/1.0 400 Bad Request");
                    echo ("$nombre .l.");
                    return;
                }

                $data = [
                    'articulo_id' => $id,
                    'tipo_id' => $tipo, 
                    'nombre_documento' => $nombre,
                    'ruta_documento' => $cargo[1], 
                    'usuario_adjunta' => $_SESSION['persona'],
                ];

                $resp = $this->inventario_model->guardar_datos($data, 'documentos_inventario');
                if (!$resp) {
                    header("HTTP/1.0 400 Bad Request");
                    echo ("$nombre asas");
                    return;
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

    public function get_documentos(){
        $tipo_modulo = $this->input->post('tipo_modulo');
        $data = array();
        $btn_eliminar =
				'<span title="Eliminar Documento" data-toggle="popover" data-trigger="hover" style="color: #DE4D4D;margin-left: 5px" class="pointer fa fa-trash btn btn-default eliminar"></span>';
		$btn_mostrar =
				'<span title="Mostrar información" data-toggle="popover" data-trigger="hover" style="color: #000000;margin-left: 5px" class="pointer fa fa-eye btn btn-default ver"></span>';

        if (!$this->Super_estado) $documentos = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
            $tipo_id = $this->input->post("tipo_id");
            $articulo_id = $this->input->post("inventario_id");
            $documentos = $this->inventario_model->get_where('documentos_inventario', ["tipo_id" => $tipo_id, 'articulo_id' => $articulo_id])->result_array();
            foreach ($documentos as $documento) {
                switch ($documento['estado']) {
                    case '1':
                        $documento['acciones'] = ($documento) ? $btn_mostrar .''. $btn_eliminar : [];
                    break;
                    case '0':
                        $documento['acciones'] = ($documento) ? $btn_mostrar : [];
                    break;
                }
                
                array_push($data, $documento);
            }
            
        }
        echo json_encode($data);
    }

    public function agregar_mantenimiento_lab(){
        if (!$this->Super_estado) $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
            $id = $this->input->post("id");
            $tipo = $this->input->post("tipo");
            $periodicidad = $this->input->post("periodicidad");
            $fecha_mantenimiento = $this->input->post("fecha_mantenimiento");
			$descripcion = $this->input->post("descripcion");

            $num_fields = [
                "Tipo de Acción" => $tipo
            ];

            $num = $this->verificar_campos_numericos($num_fields);

			if (is_array($num)) {
				$resp = ['mensaje'=>"El campo ". $num['field'] ."  no debe estar vacio y debe ser numerico.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
			}else {
                if(!$this->validateDate($fecha_mantenimiento,'Y-m-d')) {
                    $resp = ['mensaje'=>"La fecha no es valida.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                } else {
                        if ($periodicidad == 'Peri_Anual'){
                            $fecha_prox_mtto = date("Y-m-d",strtotime($fecha_mantenimiento."+ 1 year"));
                        }else if ($periodicidad == 'Peri_Semanal'){
                            $fecha_prox_mtto = date("Y-m-d",strtotime($fecha_mantenimiento."+ 7 days"));
                        }else if ($periodicidad == 'Peri_Quncenal'){
                            $fecha_prox_mtto = date("Y-m-d",strtotime($fecha_mantenimiento."+ 15 days"));
                        }else if ($periodicidad == 'Peri_Mensual'){
                            $fecha_prox_mtto = date("Y-m-d",strtotime($fecha_mantenimiento."+ 1 month"));
                        }else if ($periodicidad == 'Peri_Bimensual'){
                            $fecha_prox_mtto = date("Y-m-d",strtotime($fecha_mantenimiento."+ 2 month"));
                        }else if ($periodicidad == 'Peri_Trimestral'){
                            $fecha_prox_mtto = date("Y-m-d",strtotime($fecha_mantenimiento."+ 3 month"));
                        }else if ($periodicidad == 'Peri_Cuatrimestral'){
                            $fecha_prox_mtto = date("Y-m-d",strtotime($fecha_mantenimiento."+ 4 month"));
                        }else if ($periodicidad == 'Peri_Semestral') {
                            $fecha_prox_mtto = date("Y-m-d",strtotime($fecha_mantenimiento."+ 6 month"));
                        }
                        $data = [
                            "tipo_id" => $tipo,
                            "inventario_id" => $id,
                            "ultima_fecha" => $fecha_mantenimiento,
                            "fecha_prox_mtto" => $fecha_prox_mtto,
                            "periodicidad" => $periodicidad,
                            "usuario_registra" => $_SESSION['persona'],
                            "usuario_modifica" => $_SESSION['persona'],
                            "descripcion" => $descripcion,
                        ];
                        $add = $this->inventario_model->guardar_datos($data, "mantenimientos_laboratorios");
                    $resp = $add 
                        ? ['mensaje'=>"Proceso registrado exitosamente!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"]
                        : ['mensaje'=>"Ha ocurrido un error! Contacte con el administrador",'tipo'=>"error",'titulo'=> "Ooops!"]; 
                }
            }
        }
        echo json_encode($resp);
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s'){
		$d = DateTime::createFromFormat($format, $date);
		return ($d && $d->format($format) == $date);
    }
    
    public function get_mantenimientos_lab(){
        if (!$this->Super_estado) $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
            $id = $this->input->post("id");
            $resp = $this->inventario_model->get_mantenimientos_lab($id);
        }

        echo json_encode($resp);
	}
	
	public function guardar_tipo_recurso() {
		if (!$this->Super_estado) $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$idLab = $this->inventario_model->get_where('valor_parametro', ['id_aux' => 'Inv_Lab'])->row()->id;

            $nombre = $this->input->post('nombre');
            $descripcion = $this->input->post('descripcion');
			$idparametro = $this->input->post('idparametro');
			$tipo_modulo = $this->input->post('tipo_modulo');

			$ver_str = ['Nombre del tipo de recurso' => $nombre];
			$ver_num = ["idparametro" => $idparametro];

			$num = $this->verificar_campos_numericos($ver_num);
			$str = $this->verificar_campos_string($ver_str);

			if (is_array($str)) {
				$resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
			}else if (is_array($num)) {
				$resp = ['mensaje'=>"El campo ". $num['field'] ."  no debe estar vacio y debe ser numerico.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
			} else {
				$data = [
					"idparametro" => $idparametro,
					"valor" => $nombre,
					"valorx" => $descripcion,
					"usuario_registra" => $_SESSION['persona']
				];
				$valorId = $this->inventario_model->guardar_datos($data, 'valor_parametro');
				if($valorId) {
					$idLab = $this->inventario_model->get_where('valor_parametro', ['id_aux' => $tipo_modulo])->row();
					$permiso = [
						'vp_principal' => $idLab->id_aux,
						'vp_principal_id' => $idLab->id,
						'vp_secundario_id' => $valorId
					];
					$this->inventario_model->guardar_datos($permiso, 'permisos_parametros');
					$resp = [
						'mensaje' => "Tipo de recurso creado exitosamente",
						'tipo' => "success",
						'titulo' => "Proceso Exitoso!"
					];
				} else $resp = [
					'mensaje' => "Ha ocurrido un error al intentar guardar el tipo de recurso",
					'tipo' => "error",
					'titulo' => "Oops!"
				];
			}
        }

        echo json_encode($resp);
	}

	public function eliminar_tipo_recurso() {
		if (!$this->Super_estado) $resp = ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
		else {
			$id = $this->input->post('id');
			$tipo_modulo = $this->input->post('tipo_modulo');
			$recurso = $this->input->post('recurso');

			$tipos_modulo = $this->inventario_model->get_where('valor_parametro', ['idparametro' => 46])->result_array();
			$sw = false;
			foreach ($tipos_modulo as $row) { 
				if($row['id_aux'] == $tipo_modulo) {
					$sw = true;
					break;
				}
			}
			if($sw) {
				$parametroId = $this->inventario_model->get_where('valor_parametro', ['id_aux' => $tipo_modulo])->row()->id;
				$data = [
					'vp_principal' => $tipo_modulo,
					'vp_principal_id' => $parametroId,
					'vp_secundario_id' => $id
				];
				$idPermiso = $this->inventario_model->get_where('permisos_parametros', $data)->row()->id;
				$res = $this->pages_model->modificar_datos(['estado' => 0], 'permisos_parametros', $idPermiso);
				if($res != 'error') {
					$resp = [
						'mensaje' =>  $recurso . ' eliminado exitosamente',
						'tipo' => 'success',
						'titulo' => 'Proceso Exitoso!'
					];
				} else $resp = [
					'mensaje' => 'Ha ocurrido un error al intentar eliminar ' . $recurso,
					'tipo' => 'error',
					'titulo' => 'Ooops!'
				];
			} else $resp = [
				'mensaje' =>  'Tipo de módulo no definido',
				'tipo' => 'error',
				'titulo' => 'Ooops!'
			];
		}

		echo json_encode($resp);
	}

	public function get_accesorios() {
		if (!$this->Super_estado) $accesorios = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
		else {
			$text = $this->input->post('text');
			$tipo_modulo = $this->input->post('tipo_modulo');
			$accesorios = $this->inventario_model->get_accesorios($text, $tipo_modulo);
		}

		echo json_encode($accesorios);
    }
    public function buscar_proveedor()
    {
        $proveedor = array();
        if ($this->Super_estado == true) {
            $dato = $this->input->post('dato');
			if (!empty($dato)) $proveedor = $this->inventario_model->buscar_proveedor($dato);
			 
		}
        echo json_encode($proveedor);
    }

    public function mostrar_notificaciones_mtto() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $tipo_modulo = $this->input->post('tipo_modulo');
            $resp = $this->inventario_model->mostrar_notificaciones_mtto($tipo_modulo);
        }
        echo json_encode($resp);
    }
    public function mostrar_notificaciones_garantia() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $tipo_modulo = $this->input->post('tipo_modulo');
            $resp = $this->inventario_model->mostrar_notificaciones_garantia($tipo_modulo);
        }
        echo json_encode($resp);
    }
    public function mostrar_notificaciones_investigacion() {
        $resp = [];
        if (!$this->Super_estado) {
            $resp = ['mensaje'=>'', 'tipo'=>'sin_session', 'titulo'=>''];
        } else {
            $tipo_modulo = $this->input->post('tipo_modulo');
            $resp = $this->inventario_model->mostrar_notificaciones_investigacion($tipo_modulo);
        }
        echo json_encode($resp);
    }
    public function listar_proyecto_id() {
        $id = $this->input->post('id');
        $proyecto = $this->Super_estado == true && !empty($id) ? $this->proyectos_index_model->listar_proyecto_id($id) : array();
        echo json_encode($proyecto);
    }

    public function buscar__nombre() {
        $id = $this->input->post('id');
        $persona = $this->inventario_model->buscar__nombre($id);
        echo json_encode($persona);
        return $persona;
    }

    function eliminar__documenento()
    {
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $inventario_id = $this->input->post("inventario_id");
            $tipo_id = $this->input->post("tipo_id");
            $usuario_retira = $_SESSION["persona"];
            $fecha = date("Y-m-d H:i:s");
                $data = [
                    "usuario_elimina" => $usuario_retira,
                    "fecha_elimina" => $fecha,
                    "estado" => 0,
                ];
                $resp= ['mensaje'=>"El documento se ha eliminado con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                $del = $this->pages_model->modificar_datos($data,'documentos_inventario',$inventario_id);
                if($del != 1)$resp= ['mensaje'=>"Error al eliminar el documento, contactese con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
        }
        echo json_encode($resp);
    }

    public function exportar_excel_inventario(){
        $data = array();
        $solicitudes = $this->inventario_model->exportar_inventario();
        foreach ($solicitudes as $sol) {
            array_push($data, [ 
                "ID" => $sol['id'],
                "Serial" => $sol['serial'], 
                "Codigo Interno" => $sol['codigo_interno'],
                "Referencia" => $sol['referencia'],
                "Nombre" => $sol['nombre_activo'],
                "Fecha Ingreso" => $sol['fecha_ingreso'],
                "Fecha garantia" => $sol['fecha_garantia'],
                "Responsable" => $sol['responsable'],
                "Cedula Responsable" => $sol['identificacion_responsable'],               
                "Activo" => $sol['tipo'],
                "Marca" => $sol['marca'],
                "Modelo" => $sol['modelo'],
                "Proveedor" => $sol['proveedor'],
                "Lugar de Origen" => $sol['lugar_origen'],
                "Uso del Activo" => $sol['uso_activo'],
                "Estado Recurso" => $sol['estado_recurso'],
                "descripcion" => $sol['descripcion'],
                "Observaciones" => $sol['observaciones'],
                "Ultima Revisión (Mantenimiento)" => $sol['ultima_fecha_mantenimiento'],
                "Tipo (Mantenimiento)" => $sol['tipo_mantenimiento'],
                "Lugar" => $sol['nom_lugar'],
                "Ubicacion" => $sol['nom_ubicacion'],
                "Valor " => $sol['valor_activo'],
            ]);
        }
        
        $datos["datos"] = $data;
        $datos["nombre"] = "Inventario_laboratorio";
        $datos["leyenda"] = "";
        $datos["titulo"] = "Inventario modulo laboratorio";
        $datos["version"] = "";
        $datos["trd"] = "";
        $datos["fecha"] = "";
        $datos["col"] = 24;
        $this->load->view('templates/exportar_excel_inventario', $datos);
    }

    function buscar__datos__tecnicos() {
        $id = $this->input->post('id');
        $resp = $this->Super_estado ? $this->inventario_model->buscar__datos__tecnicos($id) : array();
        echo json_encode($resp);
    }
}
?>