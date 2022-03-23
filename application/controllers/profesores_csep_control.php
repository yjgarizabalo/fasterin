<?php
class profesores_csep_control extends CI_Controller {
    //Variables encargadas de los permisos que tiene el usuario en session
	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
    var $administra = false;
    var $ruta_archivos_soportes = "/archivos_adjuntos/profesores/soportes";
    var $ruta_firmas = "/archivos_adjuntos/profesores/firmas";
    //Construtor del controlador, se importa el modelo inventario_model y se inicia la session
    public function __construct() {
        parent::__construct();
        $this->load->model('pages_model');
        $this->load->model('profesores_csep_model');
        $this->load->model('genericas_model');
        session_start();
        date_default_timezone_set("America/Bogota");
        //la variable Super_estado es la encargada de notificar si el usuario esta en sesion, si no esta en sesion no podra ejecutar ninguna funcion, cuando pasa eso se retorna sin_session en la funcion que se esta ejecutando,por otro lado las variables Super_elimina, Super_modifica, Super_agrega se encarga de delimitar los permisos que tiene el perfil del usuario en la actividad que esta trabajando, si no tiene permiso las variables toman un valor de 0 y no les permite ejecutar la funcion retornando -1302.
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
            $this->administra = $_SESSION["perfil"] == "Per_Admin"  || $_SESSION["perfil"] == "Per_Adm_plan"  || $_SESSION["perfil"] == "Per_Csep"? true: false;
        }
    }
    /**
     * Se encarga de pintar el modulo de inventario, se carga el header alterno y la ventana inventario
     * @return Void
     */
    public function index() {
        $pages = "inicio";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], 'profesores');
            if (!empty($datos_actividad)) {
                $pages = "profesores_csep";
                $data['js'] ="profesores_csep";
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

    public function ver_plan_profesor($id, $periodo = '') {
        $data = [];
        $pages = "sin_session";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $render = false;
        $directores = array();
        $permiso = false;
        if ($this->Super_estado) {
            $datos_profesor =$this->profesores_csep_model->buscar_profesor("p.id =  $id AND cp.id IS NOT NULL", false ,$periodo);
            if (!empty($datos_profesor)) {
                $data_directores = $this->profesores_csep_model->parametros_persona($datos_profesor[0]['id_departamento'], '', '', '');
                if (!empty($data_directores)) {
                    foreach ($data_directores as $row) {
                        if ($row['tipo'] == 'decano') array_push($directores,$row);
                        if ($row['id_persona'] == $_SESSION["persona"]) $permiso = true;
                    }
                }
                if ($permiso || $this->administra || $_SESSION["persona"] == $id) {
                    $render = true;
                    $pages =  "plan_trabajo";
                    $data['actividad'] = 'Plan de Trabajo';
                    $data['js'] ="profesores_csep";
                    $data['profesor'] = $_SESSION["persona"] == $id;
                    $data['descargar'] = $periodo == $this->profesores_csep_model->obtener_periodo_actual() ? true : false;;
                    $data['plan'] = $datos_profesor[0];
                }
            }
        }
        if ($render)  $this->load->view("pages/".$pages,$data);
        else{
            $this->load->view('templates/header',$data);
            $this->load->view("pages/".$pages);
            $this->load->view('templates/footer'); 
        }
    }

    public function descargar_plan($id) {
        $data = [];
        $pages = "sin_session";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $render = false;
        $directores = array();
        $permiso = false;
        if ($this->Super_estado) {
            $datos_profesor =$this->profesores_csep_model->buscar_profesor("p.id =  $id AND cp.id IS NOT NULL");
            if (!empty($datos_profesor)) {
                $data_directores = $this->profesores_csep_model->parametros_persona($datos_profesor[0]['id_departamento'], '', '', '');
                if (!empty($data_directores)) {
                    foreach ($data_directores as $row) {
                        if ($row['tipo'] == 'decano') array_push($directores,$row);
                        if ($row['id_persona'] == $_SESSION["persona"]) $permiso = true;
                    }
                }
                if ($permiso || $this->administra || $_SESSION["persona"] == $id) {
                    $render = true;
                    $pages =  "descargar_plan";
                    $data['actividad'] = 'Plan de Trabajo';
                    $data['js'] ="profesores_csep";
                    $data['permiso'] = $permiso;
                    $data['datos'] = $datos_profesor[0];
                    $id_profesor = $datos_profesor[0]['id'];
                    $data['formacion'] =  $this->profesores_csep_model->formacion_profesor($id_profesor);
                    $data['indicadores'] =  $this->profesores_csep_model->indicadores_profesor($id_profesor);
                    $data['asignaturas'] =  $this->profesores_csep_model->asignaturas_profesor($id_profesor);
                    $data['atencion'] =  $this->profesores_csep_model->atencion_profesor($id_profesor);
                    $data['horas'] =  $this->profesores_csep_model->horas_programa_profesor($id_profesor);
                    $data['objetivos'] =  $this->profesores_csep_model->objetivos_profesor($id_profesor);
                    $data['perifles'] =  $this->profesores_csep_model->perfiles_profesor($id_profesor);
                    $data['lineas'] =  $this->profesores_csep_model->lineas_profesor($id_profesor);
                    $data['politicas'] =  $this->profesores_csep_model->obtener_valores_parametros(101) ;
                    $data['notas'] =  $this->profesores_csep_model->obtener_valores_parametros(102) ;
                    $data['directores'] = $directores;
                }

            }
        }

        if ($render)  $this->load->view("pages/".$pages,$data);
        else{
            $this->load->view('templates/header',$data);
            $this->load->view("pages/".$pages);
            $this->load->view('templates/footer'); 
        }
       
    }

    public function descargar_excel($periodo = 'actual')
    {
        $data['js'] = "";
        $data['actividad'] = "Permiso";
        if ($this->Super_estado && $this->administra) {
            $periodo = $periodo == 'actual' ? $this->profesores_csep_model->obtener_periodo_actual() : $periodo;
            $data['datos_excel'] = array(
                'profesores' => array(
                    array('Nombre Completo', 'Identificación', 'Departamento', 'Programa', 'Área', 'Dedicación', 'Escalafón', 'Contrato', 'Fecha Inicial', 'Fecha Final', 'Grupo', 'CVLAC', 'Google', 'Scopus', 'Estado', 'Periodo'),
                    $this->profesores_csep_model->buscar_profesores_excel($periodo),
                ),
                'profesores lineas' => array(
                    array('Identificación', 'Nombre Completo', 'Línea', 'Sub Línea'),
                    $this->profesores_csep_model->buscar_profesores_lineas_excel($periodo),
                ),
                'profesores asignatura' => array(
                    array('Identificación', 'Nombre Completo', 'Asignatura', 'Creditos', 'Cupos', 'Día', 'Grupo', 'Horario', 'Salón'),
                    $this->profesores_csep_model->buscar_profesores_asignatura_excel($periodo),
                ),
                'profesores atencion' => array(
                    array('Identificación', 'Nombre Completo', 'Día', 'Hora Inicio', 'Hora Fin', 'Lugar', 'Fecha Registra', 'Fecha Elimina'),
                    $this->profesores_csep_model->buscar_profesores_atencion_excel($periodo),
                ),
                'profesores formacion' => array(
                    array('Identificación', 'Nombre Completo', 'Formación', 'Nombre'),
                    $this->profesores_csep_model->buscar_profesores_formacion_excel($periodo),
                ),
                'profesores horas' => array(
                    array('Identificación', 'Nombre Completo', 'Programa', 'Hora', 'Cantidad'),
                    $this->profesores_csep_model->buscar_profesores_horas_excel($periodo),
                ),
                // 'profesores indicadores' => array(
                //     array('Identificación', 'Nombre Completo', 'Tipo', 'Indicador', 'Estado Inicial', 'Fecha Inicial', 'Estado Final', 'Fecha Final', 'Estado Actual'),
                //     $this->profesores_csep_model->buscar_profesores_indicadores_excel($periodo),
                // ),
                'profesores objetivos' => array(
                    array('Identificación', 'Nombre Completo', 'Objetivo'),
                    $this->profesores_csep_model->buscar_profesores_objetivos_excel($periodo),
                ),
                'profesores perfil' => array(
                    array('Identificación', 'Nombre Completo', 'Perfil', 'Rol', 'Cobertura' ),
                    $this->profesores_csep_model->buscar_profesores_perfil_excel($periodo),
                ),
            );

            $data['nombre_excel'] = 'Profesores - ' . date('d-m-Y_H-i-s');
            $this->load->view('templates/descargar_excel_dinamico', $data);
        } else {
            $this->load->view('templates/header', $data);
            $this->load->view('pages/sin_session');
            $this->load->view('templates/footer'); 
        }
    }

    public function buscar_profesor(){
        $personas = array();
        if ($this->Super_estado) {
            $dato = $this->input->post('dato');
            $periodo = $this->input->post('periodo');
            $filtro_firma = $this->input->post('filtro_firma');
            if (empty($dato))  $buscar = 'cp.id IS NOT NULL';
            else $buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%' OR p.correo LIKE '%" . $dato . "%') AND p.estado=1";
            $resp = $this->profesores_csep_model->buscar_profesor($buscar, true, $periodo, $filtro_firma);
            foreach ($resp as $row) {
                $detalle ='<span  style="color:#2E79E5" title="Abrir" data-toggle="popover" data-trigger="hover" class="fa fa-folder-open btn btn-default abrir" ></span>';
                $gestiona = '<span title="Administrar" data-toggle="popover" data-trigger="hover" class="fa fa-cog red btn btn-default administrar" ></span>';
                $firma = $row['firma_decano'] ? '<span style="color: #DBAA04" title="Modificar Firma" data-toggle="popover" data-trigger="hover" class="fa fa-pencil-square-o btn btn-default firma_mod" ></span>' : '<span style="color: #00CC00" title="Adjuntar Firma" data-toggle="popover" data-trigger="hover" class="fa fa-upload btn btn-default firma" ></span>';
                // $row["accion"] = $this->administra? "$detalle $gestiona $firma" : $detalle;

                $row["accion"] = $_SESSION["perfil"] == "Per_Admin" ? "$detalle $firma $gestiona" : (($_SESSION["perfil"] == "Per_Adm_plan"  || $_SESSION["perfil"] == "Per_Csep") ? "$detalle $gestiona" : "$detalle $firma" );
                array_push($personas,$row);
              }
        }
        echo json_encode($personas);
    }
    public function formacion_profesor(){
        $id_profesor = $this->input->post('id_profesor');
        $formacion = $this->Super_estado ?  $this->profesores_csep_model->formacion_profesor($id_profesor) : array();
        echo json_encode($formacion);
    }
    public function indicadores_profesor(){
        $id_profesor = $this->input->post('id_profesor');
        $tipo = $this->input->post('tipo');
        $fecha_inicio = $this->input->post('fecha_inicio');
        $fecha_fin = $this->input->post('fecha_fin');
        $indicadores = $this->Super_estado ?  $this->profesores_csep_model->indicadores_profesor($id_profesor , $tipo, $fecha_inicio, $fecha_fin) : array();
        echo json_encode($indicadores);
    }
    public function asignaturas_profesor(){
        $id_profesor = $this->input->post('id_profesor');
        $asignaturas = $this->Super_estado ?  $this->profesores_csep_model->asignaturas_profesor($id_profesor) : array();
        echo json_encode($asignaturas);
    }
    public function obtener_asignaturas_agrupadas(){
        $id_profesor = $this->input->post('id_profesor');
        $asignaturas = $this->Super_estado ?  $this->profesores_csep_model->obtener_asignaturas_agrupadas($id_profesor) : array();
        echo json_encode($asignaturas);
    }    
    public function atencion_profesor(){
        $id_profesor = $this->input->post('id_profesor');
        $atencion = $this->Super_estado ?  $this->profesores_csep_model->atencion_profesor($id_profesor) : array();
        echo json_encode($atencion);
    }
    public function horas_programa_profesor(){
        $id_profesor = $this->input->post('id_profesor');
        $ho_proghrama = $this->Super_estado ?  $this->profesores_csep_model->horas_programa_profesor($id_profesor) : array();
        echo json_encode($ho_proghrama);
    }

    public function objetivos_profesor() {
        $id_profesor = $this->input->post('id_profesor');
        $objetivos = $this->Super_estado == true ? $this->profesores_csep_model->objetivos_profesor($id_profesor) : array();
        echo json_encode($objetivos);
    }
    public function perfiles_profesor() {
        $id_profesor = $this->input->post('id_profesor');
        $perfiles = $this->Super_estado == true ? $this->profesores_csep_model->perfiles_profesor($id_profesor) : array();
        echo json_encode($perfiles);
    }
    public function lineas_profesor() {
        $id_profesor = $this->input->post('id_profesor');
        $lineas = $this->Super_estado == true ? $this->profesores_csep_model->lineas_profesor($id_profesor) : array();
        echo json_encode($lineas);
    }
    public function obtener_parametros() {
        $ids = $this->input->post('ids');
        $datos = $this->Super_estado == true ? $this->profesores_csep_model->obtener_parametros($ids) : array();
        echo json_encode($datos);
    }

    public function obtener_valores_parametros() {
        $idparametro = $this->input->post('idparametro');
        $datos = $this->Super_estado == true ? $this->profesores_csep_model->obtener_valores_parametros($idparametro) : array();
        echo json_encode($datos);
    }
    public function obtener_valores_permisos() {
        $idparametro = $this->input->post('idparametro');
        $id_valor = $this->input->post('id_valor');
        $tipo = $this->input->post('tipo');
        $datos = $this->Super_estado == true ? $this->profesores_csep_model->obtener_valores_permisos($idparametro, $id_valor, $tipo) : array();
        echo json_encode($datos);
    }
    public function mostrar_plan_sesion() {
        $id = $_SESSION["persona"];
        $datos = $this->Super_estado == true ? $this->profesores_csep_model->buscar_profesor("p.id =  $id AND cp.id IS NOT NULL") : array();
        echo json_encode($datos);
    }

    public function habilitar_relacion() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id_principal = $this->input->post("id_principal");
            $id_secundario= $this->input->post("id_secundario");
            $id_usuario_registra = $_SESSION['persona']; 
            $validos = $this->verificar_campos_string(['Valor parametro principal' => $id_principal,'Valor parametro secundario' => $id_secundario]);
            if (is_array($validos)) {
                $campo = $validos['field'];
                $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
            }else{
                $existe = $this->profesores_csep_model->verificar_relacion($id_principal,$id_secundario);
                if (empty($existe)) {           
                    $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Parametros Relacionados.!"];
                    $data = [
                        'id_principal' => $id_principal,
                        'id_secundario' => $id_secundario,
                        'id_usuario_registra' => $id_usuario_registra,
                    ];
                    $add = $this->pages_model->guardar_datos($data,'csep_relaciones');
                    if($add == -1) $resp= ['mensaje'=>"Error al relacionar los parametros, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $resp= ['mensaje'=>"Los parametros ya fueron relacionados anteriormente.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }

            }  
        }
      echo json_encode($resp);
    }

    public function deshabilitar_relacion() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id = $this->input->post("id");
            
            if (empty($id)) {
                $resp= ['mensaje'=>"Seleccione relacion a deshabilitar",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Permiso Deshabilitado.!"];
                $add = $this->pages_model->eliminar_datos($id,'csep_relaciones');
                if($add == -1) $resp= ['mensaje'=>"Error al Deshabilitado el permiso, contacte con el administrador",'tipo'=>"error",'titulo'=> "Oops.!"];
            }  
        }
      echo json_encode($resp);
    }   

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
    }

    public function obtener_valores_parametros_bloque() {
        $idparametros = "vp.idparametro > 79 AND vp.idparametro < 102";
        $datos = $this->Super_estado == true ? $this->profesores_csep_model->obtener_valores_parametros($idparametros, 2) : array();
        echo json_encode($datos);
    }

    public function guardar_plan_profesor() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if (!$this->Super_agrega) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                
                $id_persona = $this->input->post("id_persona");
                $id_programa = $this->input->post("id_programa");
                $id_departamento = $this->input->post("id_departamento");
                $id_area = $this->input->post("id_area");
                $id_dedicacion = $this->input->post("id_dedicacion");
                $id_escalafon = $this->input->post("id_escalafon");
                $id_contrato = $this->input->post("id_contrato");
                $fecha_inicio = $this->input->post("fecha_inicio");
                $fecha_fin = $this->input->post("fecha_fin");
                $fecha_inicial_valida = $this->validateDate($fecha_inicio, 'Y-m-d');
                $fecha_final_valida = $this->validateDate($fecha_fin, 'Y-m-d');
                $id_grupo = $this->input->post("id_grupo");
                $cvlac = $this->input->post("cvlac");
                $google = $this->input->post("google");
                $scopus = $this->input->post("scopus");
                $id_estado = $this->input->post("id_estado");
                $id_usuario_registra = $_SESSION['persona']; 
                $str= $this->verificar_campos_string(['Persona' => $id_persona, 'Programa' => $id_programa, 'Departamento' => $id_departamento, 'Area de Conocimiento' => $id_area, 'Dedicacion' => $id_dedicacion, 'Escalafon' => $id_escalafon, 'Contrato' => $id_contrato, 'Grupo Investigación' => $id_grupo, 'Fecha inicio' => $fecha_inicio,'Estado' => $id_estado,]);
                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if (!$fecha_inicial_valida) {
                    $resp = ['mensaje'=>"Ingrese una fecha inicial valida.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if (!$fecha_final_valida && !empty($fecha_fin)) {
                    $resp = ['mensaje'=>"Ingrese una fecha final valida.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else{
                    
                    $data = [
                        'id_programa' => $id_programa,
                        'id_departamento' => $id_departamento,
                        'id_area' => $id_area,
                        'id_dedicacion' => $id_dedicacion,
                        'id_escalafon' => $id_escalafon,
                        'id_contrato' => $id_contrato,
                        'fecha_inicio' => $fecha_inicio,
                        'fecha_fin' => $fecha_fin ? $fecha_fin : null,
                        'id_grupo' => $id_grupo,
                        'cvlac' => $cvlac,
                        'google' => $google,
                        'scopus' => $scopus,
                        'id_estado' => $id_estado,
                    ];
                    $datos_profesor =$this->profesores_csep_model->buscar_profesor("p.id =  $id_persona")[0];
                    $id = $datos_profesor['id'];
                    if (is_null($id)) {
                        $data["id_usuario_registra"] = $id_usuario_registra;
                        $data["id_persona"] = $id_persona;
                        $query = $this->pages_model->guardar_datos($data,'csep_profesores');
                    }else{
                        $data["id_usuario_modifca"] = $id_usuario_registra;
                        $data["fecha_modifca"] = date("Y-m-d H:i:s");
                        $query = $this->pages_model->modificar_datos($data,'csep_profesores',$id);
                    }
                    $resp = ['mensaje'=>"Datos Guardados",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!","id" =>''];
                    if($query == -1) $resp= ['mensaje'=>"Error guardar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    else{
                        if (is_null($id)) {
                            $datos_profesor =$this->profesores_csep_model->buscar_profesor("p.id =  $id_persona")[0];
                            $resp['id'] = $datos_profesor['id'];
                        }
                    }
                }  
            }
        }
      echo json_encode($resp);
    }

    public function guardar_indicador() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if (!$this->Super_agrega) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                
                $id = $this->input->post("id");
                $id_profesor = $this->input->post("id_profesor");
                $tipo = $this->input->post("tipo");
                $id_indicador = $this->input->post("id_indicador");
                $estado_inicial = $this->input->post("estado_inicial");
                $fecha_inicial = $this->input->post("fecha_inicial");
                $estado_final = $this->input->post("estado_final");
                $fecha_final = $this->input->post("fecha_final");
                $estado_actual = $this->input->post("estado_actual");
                $id_usuario_registra = $_SESSION['persona']; 
                $fecha_inicial_valida = $this->validateDate($fecha_inicial, 'Y-m-d');
                $fecha_final_valida = $this->validateDate($fecha_final, 'Y-m-d');
                $fechas = $this->validateFechaMayor($fecha_inicial,$fecha_final);
                $str = $this->verificar_campos_string(['Tipo' => $tipo,'Profesor' => $id_profesor, 'Indicador' => $id_indicador, 'Estado Inicial' => $estado_inicial, 'Fecha Inicial' => $fecha_inicial, 'Estado Final' => $estado_final, 'Fecha Final' => $fecha_final, 'Estado Actual' => $estado_actual]);
                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if (!$fecha_inicial_valida) {
                    $resp = ['mensaje'=>"Ingrese una fecha inicial valida.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if (!$fecha_final_valida) {
                    $resp = ['mensaje'=>"Ingrese una fecha final valida.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if ($fechas == -1) {
                    $resp = ['mensaje'=> "La fecha de inicio debe ser superior a la fecha fin.", 'tipo'=>"info", 'titulo'=> "Oops."];
                }else{
                    $data = [
                        'tipo' => $tipo,
                        'id_indicador' => $id_indicador,
                        'estado_inicial' => $estado_inicial,
                        'fecha_inicial' => $fecha_inicial,
                        'estado_final' => $estado_final,
                        'fecha_final' => $fecha_final,
                        'estado_actual' => $estado_actual,
                    ];
                    if (!empty($id)) $query = $this->pages_model->modificar_datos($data,'csep_profesor_indicadores',$id);
                    else{
                        $data["id_usuario_registra"] = $id_usuario_registra;
                        $data["id_profesor"] = $id_profesor;
                        $query = $this->pages_model->guardar_datos($data,'csep_profesor_indicadores');
                    }
                    $resp= ['mensaje'=>"Los datos del indicador fueron guardados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!" , "id_profesor" => $id_profesor, "id" => $id];
                    if($query == -1) $resp= ['mensaje'=>"Error al guardar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
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
                $tipo = $this->input->post("tipo");
                $id_profesor = $this->input->post("id_profesor");
                $id_usuario_elimina = $_SESSION['persona']; 
                if (empty($id)) {
                    $resp = ['mensaje'=>"Error al cargar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"]; 
                }else{
                    $data = ['fecha_elimina' => date("Y-m-d H:i:s"),'id_usuario_elimina' => $id_usuario_elimina,'estado' => 0,];
                    $tabla = 'csep_profesor_indicadores';
                    if ($tipo == 2) $tabla = 'csep_profesor_asignatura';
                    else if ($tipo == 3) $tabla = 'csep_profesor_formacion';
                    else if ($tipo == 4) $tabla = 'csep_profesor_objetivos';
                    else if ($tipo == 5) $tabla = 'csep_profesor_atencion';
                    else if ($tipo == 6) $tabla = 'csep_profesor_perfil';
                    else if ($tipo == 7) $tabla = 'csep_profesor_horas';
                    else if ($tipo == 8) $tabla = 'csep_profesores_lineas';
                    else if ($tipo == 9) $tabla = 'csep_profesores_soportes';
                    $query = $this->pages_model->modificar_datos($data, $tabla ,$id);
                    $resp= ['mensaje'=>"Los datos fueron eliminados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    if($query == -1) $resp= ['mensaje'=>"Error al eliminar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }  
            }
        }
      echo json_encode($resp);
    }

    public function guardar_asignatura() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if (!$this->Super_agrega) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                
                $id = $this->input->post("id");
                $id_profesor = $this->input->post("id_profesor");
                $id_asignatura = $this->input->post("id_asignatura");
                $creditos = $this->input->post("creditos");
                $cupo = $this->input->post("cupo");
                $id_dia = $this->input->post("id_dia");
                $grupo = $this->input->post("grupo");
                $horario = $this->input->post("horario");
                $salon = $this->input->post("salon");
                $id_usuario_registra = $_SESSION['persona']; 
                $str = $this->verificar_campos_string(['Asignatura' => $id_asignatura, 'Creditos' => $creditos, 'Cupo' => $cupo, 'Día' => $id_dia, 'Grupo' => $grupo, 'Horario' => $horario, 'Salon' => $salon]);
                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else{
                    $data = [
                        'id_asignatura' => $id_asignatura,
                        'creditos' => $creditos,
                        'cupo' => $cupo,
                        'id_dia' => $id_dia,
                        'grupo' => $grupo,
                        'horario' => $horario,
                        'salon' => $salon,
                    ];
                    if (!empty($id)) $query = $this->pages_model->modificar_datos($data,'csep_profesor_asignatura',$id);
                    else{
                        $data["id_usuario_registra"] = $id_usuario_registra;
                        $data["id_profesor"] = $id_profesor;
                        $query = $this->pages_model->guardar_datos($data,'csep_profesor_asignatura');
                    }
                    $resp= ['mensaje'=>"Los datos de la asignatura fueron guardados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!" , "id_profesor" => $id_profesor, "id" => $id];
                    if($query == -1) $resp= ['mensaje'=>"Error al guardar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }  
            }
        }
      echo json_encode($resp);
    }

    public function guardar_formacion() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if (!$this->Super_agrega) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                
                $personal = $this->input->post("personal")  ? true : false;
                $id = $this->input->post("id");
                $id_profesor = $this->input->post("id_profesor");
                $id_formacion = $this->input->post("id_formacion");
                $nombre = $this->input->post("nombre");
                $id_usuario_registra = $_SESSION['persona']; 
                $str = $this->verificar_campos_string(['Formacion' => $id_formacion, 'Nombre' => $nombre]);
                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else{
                    $data = [
                        'id_formacion' => $id_formacion,
                        'nombre' => $nombre,
                    ];
                    if (!empty($id)) $query = $this->pages_model->modificar_datos($data,'csep_profesor_formacion',$id);
                    else{
                        $data["id_usuario_registra"] = $id_usuario_registra;
                        $data["id_profesor"] = $id_profesor;
                        $query = $this->pages_model->guardar_datos($data,'csep_profesor_formacion');
                    }
                    $resp= ['mensaje'=>"Los datos de la formación fueron guardados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!" , "id_profesor" => $id_profesor, "id" => $id];
                    if($query == -1) $resp= ['mensaje'=>"Error al guardar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }  
            }
        }
      echo json_encode($resp);
    }
    public function guardar_objetivo() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if (!$this->Super_agrega) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                
                $id = $this->input->post("id");
                $id_profesor = $this->input->post("id_profesor");
                $objetivo = $this->input->post("objetivo");
                $id_usuario_registra = $_SESSION['persona']; 
                $str = $this->verificar_campos_string(['Objetivo' => $objetivo]);
                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else{
                    $data = [
                        'objetivo' => $objetivo,
                    ];
                    if (!empty($id)) $query = $this->pages_model->modificar_datos($data,'csep_profesor_objetivos',$id);
                    else{
                        $data["id_usuario_registra"] = $id_usuario_registra;
                        $data["id_profesor"] = $id_profesor;
                        $query = $this->pages_model->guardar_datos($data,'csep_profesor_objetivos');
                    }
                    $resp= ['mensaje'=>"Los datos fueron guardados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!" , "id_profesor" => $id_profesor, "id" => $id];
                    if($query == -1) $resp= ['mensaje'=>"Error al guardar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }  
            }
        }
      echo json_encode($resp);
    }

    public function guardar_atencion() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if (!$this->Super_agrega) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                
                $id = $this->input->post("id");
                $id_tipo = $this->input->post("id_tipo");
                $id_profesor = $this->input->post("id_profesor");
                $id_dia = $this->input->post("id_dia");
                $hora_inicio = $this->input->post("hora_inicio");
                $hora_fin = $this->input->post("hora_fin");
                $lugar = $this->input->post("lugar");
                $id_asignatura = $this->input->post("id_asignatura");
                $id_usuario_registra = $_SESSION['persona']; 
                $hora_inicial_valida = $this->validateDate($hora_inicio, 'H:i');
                $hora_final_valida = $this->validateDate($hora_fin, 'H:i');
                $fechas = $this->validateFechaMayor($hora_inicio,$hora_fin);
                $str = $this->verificar_campos_string(['Tipo' => $id_tipo,'Día' => $id_dia, 'Hora Inicio' => $hora_inicio, 'Hora Fin' => $hora_inicio, 'Lugar' => $lugar]);
                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if (!$hora_inicial_valida) {
                    $resp = ['mensaje'=>"Ingrese una Hora de inicio valida.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if (!$hora_final_valida) {
                    $resp = ['mensaje'=>"Ingrese una Hora de fin valida.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if ($fechas == -1) {
                    $resp = ['mensaje'=> "La Hora de inicio debe ser superior a la hora fin.", 'tipo'=>"info", 'titulo'=> "Oops."];
                }else{
                    $cruze =  $this->profesores_csep_model->verificar_cruze_atencion_profesor($id_profesor, $hora_inicio, $hora_fin, $id_dia);
                    if (empty($cruze)) {
                        $data = [
                            'id_dia' => $id_dia,
                            'hora_inicio' => $hora_inicio,
                            'hora_fin' => $hora_fin,
                            'id_tipo' => $id_tipo,
                            'lugar' => $lugar,
                            'id_asignatura' => $id_asignatura,
                        ];
                        if (!empty($id)) $query = $this->pages_model->modificar_datos($data,'csep_profesor_atencion',$id);
                        else{
                            $data["id_usuario_registra"] = $id_usuario_registra;
                            $data["id_profesor"] = $id_profesor;
                            $query = $this->pages_model->guardar_datos($data,'csep_profesor_atencion');
                        }
                        $resp= ['mensaje'=>"Los datos del horario de atención fueron guardados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!" , "id_profesor" => $id_profesor, "id" => $id];
                        if($query == -1) $resp= ['mensaje'=>"Error al guardar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    
                    }else{
                        $resp = ['mensaje'=> "Verifique las horas seleccionadas, estas se cruzan con las que se encuentran ya asignadas.", 'tipo'=>"info", 'titulo'=> "Oops."];
                    }
                }  
            }
        }
      echo json_encode($resp);
    }
    public function guardar_perfil() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if (!$this->Super_agrega) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                
                $id = $this->input->post("id");
                $id_profesor = $this->input->post("id_profesor");
                $id_perfil = $this->input->post("id_perfil");
                $id_rol = $this->input->post("id_rol");
                $id_cobertura = $this->input->post("id_cobertura");
                $id_usuario_registra = $_SESSION['persona']; 
                $str = $this->verificar_campos_string(['Perfil' => $id_perfil, 'Rol' => $id_rol, 'Cobertura' => $id_cobertura]);
                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else{
                    $data = [
                        'id_perfil' => $id_perfil,
                        'id_rol' => $id_rol,
                        'id_cobertura' => $id_cobertura,
                    ];
                    if (!empty($id)) $query = $this->pages_model->modificar_datos($data,'csep_profesor_perfil',$id);
                    else{
                        $data["id_usuario_registra"] = $id_usuario_registra;
                        $data["id_profesor"] = $id_profesor;
                        $query = $this->pages_model->guardar_datos($data,'csep_profesor_perfil');
                    }
                    $resp= ['mensaje'=>"Los datos del perfil fueron guardados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!" , "id_profesor" => $id_profesor, "id" => $id];
                    if($query == -1) $resp= ['mensaje'=>"Error al guardar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }  
            }
        }
      echo json_encode($resp);
    }
    public function guardar_hora() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if (!$this->Super_agrega) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                
                $id = $this->input->post("id");
                $id_profesor = $this->input->post("id_profesor");
                $id_programa = $this->input->post("id_programa");
                $id_hora = $this->input->post("id_hora");
                $cantidad = $this->input->post("cantidad");
                $id_usuario_registra = $_SESSION['persona']; 
                $str = $this->verificar_campos_string(['Programa' => $id_programa, 'Hora' => $id_hora]);
                $num = $this->verificar_campos_numericos(['Cantidad' => $cantidad]);
                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if (is_array($num)) {
                    $campo = $num['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vacio y debe ser numerico.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else if ($cantidad < 0) {
                    $resp = ['mensaje'=>"El campo cantidad debe ser mayor a 0.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else{
                    $data = [
                        'id_programa' => $id_programa,
                        'id_hora' => $id_hora,
                        'cantidad' => $cantidad,
                    ];
                    if (!empty($id)) $query = $this->pages_model->modificar_datos($data,'csep_profesor_horas',$id);
                    else{
                        $data["id_usuario_registra"] = $id_usuario_registra;
                        $data["id_profesor"] = $id_profesor;
                        $query = $this->pages_model->guardar_datos($data,'csep_profesor_horas');
                    }
                    $resp= ['mensaje'=>"Los datos fueron guardados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!" , "id_profesor" => $id_profesor, "id" => $id];
                    if($query == -1) $resp= ['mensaje'=>"Error al guardar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }  
            }
        }
      echo json_encode($resp);
    }
    public function guardar_linea() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if (!$this->Super_agrega) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{
                
                $id = $this->input->post("id");
                $id_profesor = $this->input->post("id_profesor");
                $id_linea = $this->input->post("id_linea");
                $id_sub_linea = $this->input->post("id_sub_linea");
                $id_usuario_registra = $_SESSION['persona']; 
                $str = $this->verificar_campos_string(['Linea' => $id_linea, 'Sub-Linea' => $id_sub_linea]);
                if (is_array($str)) {
                    $campo = $str['field'];
                    $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                }else{
                    $data = [
                        'id_linea' => $id_linea,
                        'id_sub_linea' => $id_sub_linea,
                    ];
                    if (!empty($id)) $query = $this->pages_model->modificar_datos($data,'csep_profesores_lineas',$id);
                    else{
                        $data["id_usuario_registra"] = $id_usuario_registra;
                        $data["id_profesor"] = $id_profesor;
                        $query = $this->pages_model->guardar_datos($data,'csep_profesores_lineas');
                    }
                    $resp= ['mensaje'=>"Los datos fueron guardados con exito.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!" , "id_profesor" => $id_profesor, "id" => $id];
                    if($query == -1) $resp= ['mensaje'=>"Error al guardar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }  
            }
        }
      echo json_encode($resp);
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function parametros_persona() {
        $id = $this->input->post('id');
        $limit = $this->input->post('limit');
        $tipo = $this->input->post('tipo');
        $id_persona = $this->input->post('id_persona');
        $datos = $this->Super_estado == true ? $this->profesores_csep_model->parametros_persona($id, $id_persona, $limit, $tipo) : array();
        echo json_encode($datos);
    }

    public function buscar_persona(){
        $dato = $this->input->post('dato');
        $buscar = "(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%') AND p.estado=1";
        $personas = $this->Super_estado && !empty($dato)?  $this->pages_model->buscar_persona($buscar) : array();  
        echo json_encode($personas);
    }

    public function asignar_persona_parametro() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id_parametro = $this->input->post("id_parametro");
            $id_persona= $this->input->post("id_persona");
            $tipo = $this->input->post("tipo");
            $id_usuario_registra = $_SESSION['persona']; 
            $validos = $this->verificar_campos_string(['Valor parametro' => $id_parametro,'Persona' => $id_persona ,'Tipo' => $tipo]);
            if (is_array($validos)) {
                $campo = $validos['field'];
                $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
            }else{
                $existe = $this->profesores_csep_model->parametros_persona($id_parametro, $id_persona);
                if (empty($existe)) {
                    $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Persona Asignada.!"];
                    $data = [
                        'id_parametro' => $id_parametro,
                        'id_persona' => $id_persona,
                        'tipo' => $tipo,
                        'id_usuario_registra' => $id_usuario_registra,
                    ];
                    $add = $this->pages_model->guardar_datos($data,'csep_parametros_persona');
                    if($add == -1) $resp= ['mensaje'=>"Error al asignar la persona, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $resp= ['mensaje'=>"La persona ya fue asignada anteriormente.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }

            }  
        }
      echo json_encode($resp);
    }

    public function eliminar_persona_parametro() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id = $this->input->post("id");
            $id_usuario_elimina = $_SESSION['persona']; 
            $fecha_elimina =  date("Y-m-d H:i:s");
            if (empty($id)){
                $resp= ['mensaje'=>"Seleccione persona a retirar",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Persona Retirada.!"];
                $data = [
                    'id_usuario_elimina' => $id_usuario_elimina,
                    'fecha_elimina' => $fecha_elimina,
                    'estado' => 0,
                ];
                $add = $this->pages_model->modificar_datos($data,'csep_parametros_persona', $id);
                if($add == -1) $resp= ['mensaje'=>"Error al retirar la persona, contacte con el administrador",'tipo'=>"error",'titulo'=> "Oops.!"];
            }  
        }
      echo json_encode($resp);
    } 
    
    public function validateFechaMayor($fecha_inicio,$fecha_fin){
        ($fecha_fin >= $fecha_inicio) ? $resp = 1 : $resp = -1;
        return $resp;  
    }

    public function calcular_horas_docente($id_profesor, $id_tipo){
        $mentoring = 20;
        $minutos_atencion = 0;
        $minutos_mentoring = 0;
        $horas_atencion = 0;
        $horas_mentoring = 0;

        $cantidad = $this->profesores_csep_model->contar_horas($id_profesor, $id_tipo);
        
        $atencion_semana = (($cantidad/3) - $mentoring) / 16;
        $mentoring_semana = 20/16;
        
        $data_atencion = explode(".",$atencion_semana);
        $data_mentoring = explode(".",$mentoring_semana);
        
        $horas_atencion = $data_atencion[0] ? $data_atencion[0] : 0;
        $horas_mentoring = $data_mentoring[0] ? $data_mentoring[0] : 0;
        $minutos_atencion = $data_atencion[1] ? (($data_atencion[1]*60)/100) : 0;
        $minutos_mentoring = $data_mentoring[1] ? (($data_mentoring[1]*60)/100): 0;

        return ["horas_atencion" => $horas_atencion,"minutos_atencion" => $minutos_atencion,"horas_mentoring" => $horas_mentoring, "minutos_mentoring" => $minutos_mentoring];
    }

    public function calcular_horas_docente_post(){
        $id_profesor = $this->input->post("id_profesor");
        $id_tipo = $this->input->post("id_tipo");
        echo json_encode($this->calcular_horas_docente($id_profesor, $id_tipo));
    }

    public function recibir_archivos(){
        $resp = ['mensaje'=>"Todos Los archivos fueron cargados.!",'tipo'=>"success",'titulo'=> "Proceso Exitoso!"];
        $id_formacion = $_POST['id_formacion'];
        $id_usuario_registra= $_SESSION['persona']; 
        $nombre_real = $_FILES["file"]["name"];
        $cargo = $this->pages_model->cargar_archivo("file", $this->ruta_archivos_soportes, "sop");
        if ($cargo[0] == -1) {
            header("HTTP/1.0 400 Bad Request");
            // echo ($nombre);
            return;
        }
        $data = [
            'id_alterno' => $id_formacion,
            'nombre_real' => $nombre_real,
            'nombre_guardado' => $cargo[1],
            'id_usuario_registra' => $id_usuario_registra,
            'tipo' => 'formacion',
        ];
        $add = $this->pages_model->guardar_datos($data,'csep_profesores_soportes');
        echo json_encode($resp);
    }

    public function listar_soportes() {
        $tipo = $_POST['tipo'];
        $id_alterno = $_POST['id_alterno'];
        $datos = $this->Super_estado == true ? $this->profesores_csep_model->listar_soportes($id_alterno, $tipo) : array();
        echo json_encode($datos);
    }

    public function obtener_periodos() {
        $tipo = $_POST['tipo'];
        $id_persona = $_POST['id_persona']; 
        $datos = $this->Super_estado == true ? $this->profesores_csep_model->obtener_periodos($id_persona, $tipo) : array();
        echo json_encode($datos);
    }

    public function guardar_firma(){
        if (!$this->Super_estado) $resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
        else {
            $firma_digital = null;
            $file = $this->cargar_archivo("firma_digital", $this->ruta_firmas, 'firma_digital');
            if ($file[0] == -1) {
                $error = $file[1];
                if($error === "<p>You did not select a file to upload.</p>") $resp = ['mensaje' => "Debe adjuntar la firma digital.", 'tipo' => "info", 'titulo' => "Oops.!"];
                else if($error === "<p>The filetype you are attempting to upload is not allowed.</p>") $resp = ['mensaje' => "Debe adjuntar la firma en formato de imagen( jpg, png, jpeg ).", 'tipo' => "info", 'titulo' => "Oops.!"];
                else $resp = ['mensaje' => "Error al cargar la firma.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
                $firma_digital = $file[1];
                $id_usuario = $_SESSION['persona'];
                $id_plan = $this->input->post('id_plan');;
                $tipo = $this->input->post('tipo');;

                $data = [
                    'id_plan' => $id_plan,
                    'id_usuario_registro' => $id_usuario,
                    'tipo' => $tipo,
                    'firma' => $firma_digital
                ];
                $add = $this->pages_model->guardar_datos($data, "csep_profesores_firmas");
                if ($add == -1) {
                    $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                } else {
                    $resp = ['mensaje' => "Firma guardada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function cargar_archivo($mi_archivo, $ruta, $nombre){
        $nombre .= uniqid();
        $real_path = realpath(APPPATH . '../' . $ruta);
        $config['upload_path'] = $real_path;
        $config['file_name'] = $nombre;
        $config['allowed_types'] = 'jpg|png|jpeg';
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

    public function guardar_un_soporte_formacion(){
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        else {
            if ($this->Super_agrega) {
                $id_formacion = $this->input->post('id_formacion');
                $id_usuario_registra = $_SESSION['persona'];
                $carga = $this->pages_model->cargar_archivo("soporte_form", $this->ruta_archivos_soportes, "sop");
                if ($carga[0] == -1) {
                    if($carga[1] === "<p>You did not select a file to upload.</p>") $resp = ['mensaje' => "Debe adjuntar un archivo.", 'tipo' => "info", 'titulo' => "Oops.!"];
                    else $resp = ['mensaje' => "Error al cargar el archivo, contacte con el administrador.!", 'tipo' => "error", 'titulo' => "Oops!"];
                } else {
                    $nombre_real = $_FILES["soporte_form"]["name"];
                    $data = [
                        'id_alterno' => $id_formacion,
                        'nombre_real' => $nombre_real,
                        'nombre_guardado' => $carga[1],
                        'id_usuario_registra' => $id_usuario_registra,
                        'tipo' => 'formacion',
                    ];
                    $resp = ['mensaje' => "Documento guardado exitosamente.!", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
                    $add = $this->pages_model->guardar_datos($data, 'csep_profesores_soportes');
                    if (!$add) $resp = ['mensaje' => "Error al guardar información, contacte con el administrador.!", 'tipo' => "info", 'titulo' => "Oops!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function modificar_firma_digital () {
        if (!$this->Super_estado) $resp = ['mensaje' => '', 'tipo' => 'sin_session', 'titulo' => ''];
        else {
            $firma_digital = null;
            $file = $this->cargar_archivo("firma_digital_mod", $this->ruta_firmas, 'firma_digital');
            if ($file[0] == -1) {
                $error = $file[1];
                if ($error === "<p>You did not select a file to upload.</p>") $resp = ['mensaje' => "Debe adjuntar la firma digital.", 'tipo' => "info", 'titulo' => "Oops.!"];
                else if ($error === "<p>The filetype you are attempting to upload is not allowed.</p>") $resp = ['mensaje' => "Debe adjuntar la firma en formato de imagen( jpg, png, jpeg ).", 'tipo' => "info", 'titulo' => "Oops.!"];
                else $resp = ['mensaje' => "Error al cargar la firma.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
                $firma_digital = $file[1];
                $id_usuario = $_SESSION['persona'];
                $id_plan = $this->input->post('id_plan');;
                $tipo = $this->input->post('tipo');;

                $data = [
                    'id_plan' => $id_plan,
                    'id_usuario_registro' => $id_usuario,
                    'tipo' => $tipo,
                    'firma' => $firma_digital
                ];
                $del = $this->profesores_csep_model->eliminar_firma($id_plan, $tipo);
                if ($del == -1) {
                    $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                } 
                else {
                    $add = $this->pages_model->guardar_datos($data, "csep_profesores_firmas");
                    if ($add == -1) {
                        $resp = ['mensaje' => "Error al guardar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                    } else {
                        $resp = ['mensaje' => "Firma modificada con exito.", 'tipo' => "success", 'titulo' => "Proceso Exitoso!"];
                    }
                }
            }
        }
        echo json_encode($resp);
    }


    public function guardar_import() {
        if (!$this->Super_estado) 
        $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        else {
                $datos = json_decode($this->input->post('array'), true);
                $guardados=array();
                $noguardados=array();
                if(empty($datos)){
                    $resp= ['mensaje'=>"Antes de guardar debe seleccionar un archivo.",'tipo'=>"warning",'titulo'=> "Oops.!"];
                }else{
                    $conta=0;
                    foreach ($datos as $value) {
                        $id_persona =  $value['id_persona'];
                        $id_programa = $value['id_programa'];
                        $id_departamento = $value['id_departamento'];
                        $id_area = $value['id_area'];
                        $id_dedicacion = $value['id_dedicacion'];
                        $id_escalafon = $value['id_escalafon'];
                        $id_contrato = $value['id_contrato'];
                        $fecha_inicio =($value['fecha_inicio']=="NULL")?null :$value['fecha_inicio'];
                        $fecha_fin = ($value['fecha_fin']=="NULL")?null :$value['fecha_fin'];
                        $id_grupo = $value['id_grupo'];
                        $cvlac = $value['cvlac'];
                        $google = $value['google'];
                        $scopus = $value['scopus'];
                        $id_estado = $value['id_estado'];
                        $id_usuario_registra = $_SESSION['persona'];
                        $str= $this->verificar_campos_string(['Persona' => $id_persona, 'Programa' => $id_programa, 'Departamento' => $id_departamento, 'Area de Conocimiento' => $id_area, 'Dedicacion' => $id_dedicacion, 'Escalafon' => $id_escalafon, 'Contrato' => $id_contrato, 'Grupo Investigación' => $id_grupo, 'Fecha inicio' => $fecha_inicio,'Estado' => $id_estado,]);
                        if (is_array($str)) {
                            $campo = $str['field'];
                            $resp = ['mensaje'=>"El campo $campo no puede estar vacio.",'tipo'=>"info",'titulo'=> "Oops.!"]; 
                            array_push($noguardados,$value);
                        }else{
                            
                            $data = [
                                'id_programa' => $id_programa,
                                'id_departamento' => $id_departamento,
                                'id_area' => $id_area,
                                'id_dedicacion' => $id_dedicacion,
                                'id_escalafon' => $id_escalafon,
                                'id_contrato' => $id_contrato,
                                'fecha_inicio' => $fecha_inicio,
                                'fecha_fin' => $fecha_fin ? $fecha_fin : null,
                                'id_grupo' => ($id_grupo=="NULL" ||$id_grupo=="N/A")?null :$id_grupo,
                                'cvlac' => ($cvlac=="NULL")?null :$cvlac,
                                'google' => ($google=="NULL")?null :$google,
                                'scopus' => ($scopus=="NULL")?null :$scopus,
                                'id_estado' => $id_estado,
                            ];
                            $datos_profesor =$this->profesores_csep_model->buscar_profesor("p.id =  $id_persona")[0];
                            $id = $datos_profesor['id'];
                            if (is_null($id)) {
                                $data["id_usuario_registra"] = $id_usuario_registra;
                                $data["id_persona"] = $id_persona;
                                $query = $this->pages_model->guardar_datos($data,'csep_profesores');
                            }else{
                                $data["id_usuario_modifca"] = $id_usuario_registra;
                                $data["fecha_modifca"] = date("Y-m-d H:i:s");
                                $query = $this->pages_model->modificar_datos($data,'csep_profesores',$id);
                            }
                            if($query == -1){ 
                                $resp= ['mensaje'=>"Error guardar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                                array_push($noguardados,$value);
                            }else{
                                if (is_null($id)) {
                                    $datos_profesor =$this->profesores_csep_model->buscar_profesor("p.id =  $id_persona")[0];
                                    $resp['id'] = $datos_profesor['id'];
                                }
                                array_push($guardados,$value);
                                $conta=$conta+1;
                            }
                        } 
                    }
                    if($conta==0){
                        $resp= ['mensaje'=>"Error guardar los datos, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                    }else{
                        $resp = ['mensaje'=>"$conta Datos Guardados",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!","id" =>'', 'guardados' =>$guardados,'noguardados' =>$noguardados];
                    }
            }
            
            }
            
            echo json_encode($resp);
        } 
  }

