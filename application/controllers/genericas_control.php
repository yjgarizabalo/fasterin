<?php

class genericas_control extends CI_Controller {

	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
    public function __construct() {
        parent::__construct();
        $this->load->model('genericas_model');
        session_start();
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
 
        }
    }

    public function index($pages = "genericas") {
        $buscar = "genericas";
        $data['js'] = "";
        if ($pages=="actividades_perfil") {
            $buscar="permisos";
        }else  if ($pages=="cargos_departamento") {
            $buscar="cargos";
            $data['js'] = "Cargos_departamento";
        }
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $buscar);
            if (!empty($datos_actividad)) {
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
        $this->load->view('templates/header',$data);
        $this->load->view("pages/".$pages);
        $this->load->view('templates/footer');
	}
	
	function asignar_jefe() {
		$where = null;
		if ($this->Super_estado == false) {
            echo ("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {
                $dep = $this->input->post("dep");
				$jefe = $this->input->post("jefe");
				$exc = $this->input->post("excepciones");
				if (!empty($exc)) {
					$where = "id <> ". $exc[0];
					if (count($exc) > 1) {
						for ($i=1; $i < count($exc); $i++) { 
							$where .= " and id <> ". $exc[$i];
						}
					}
				}
                if (empty($dep) || empty($jefe) || ctype_space($dep) || ctype_space($jefe) || is_nan($dep) || is_nan($jefe)) {
                    echo json_encode(-1);
                } else {
					$resp = $this->genericas_model->Asignar_Jefe($dep, $jefe, $where);
					echo json_encode($resp);
                }
            }
        }
	}

	function asignar_jefe_individual() {
		if ($this->Super_estado == false) {
            echo ("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {
                $dep = $this->input->post("dep");
				$jefe = $this->input->post("jefe");
				$cargo = $this->input->post("cargo");
                if (empty($dep) || empty($jefe) || ctype_space($dep) || ctype_space($jefe) || is_nan($dep) || is_nan($jefe) || empty($cargo) || ctype_space($cargo) || is_nan($cargo)) {
                    echo json_encode(-1);
                } else {
					$resp = $this->genericas_model->asignar_jefe_individual($dep, $jefe, $cargo);
					echo json_encode($resp);
                }
            }
        }
	}

    function Cargar_Parametros() {
        $datos = $this->Super_estado == true ? $this->genericas_model->Listar() : array();
        echo json_encode($datos);
    }

    function Listar_cargos_departamento() {

        $cargos = array();

        if ($this->Super_estado == false) {
            echo json_encode($cargos);
            return;
        }
        $iddepartamento = $this->input->post("iddepartamento");
        $general = $this->input->post("general");
        $datos = $this->genericas_model->Listar_cargos_departamento($iddepartamento, $general);
        if ($general == 1) {
            echo json_encode($datos);
            return;
        }
        $i = 1;
        $sw = FALSE;
        foreach ($datos as $row) {


            if ($row["estado"] == 1) {
                $row["estado"] = "Visible";
                $row["op"] = ' <span title="Cambiar a oculto" data-toggle="popover" data-trigger="hover" class="fa fa-eye-slash btn btn-default" onclick="confirmar_cambio_estado(' . $row["id"] . ',0)"></span>';
            } else {
                $row["estado"] = "Oculto";
                $row["op"] = ' <span title="Cambiar a visible" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default" onclick="confirmar_cambio_estado(' . $row["id"] . ',1)"></span>';
            }
            $row["op"] .= ' <span style="color: #2E79E5;" title="Asignar Jefe" data-toggle="popover" data-trigger="hover" class="fa fa-group btn btn-default" onclick="pasar_id_jefe(' . $row["id"] . ')"></span>';


            if ($this->Super_modifica == 0) {
                $row["op"] = '----';
            }
            $row["indice"] = $i;
            $cargos["data"][] = $row;
            $i++;
        }

        echo json_encode($cargos);
    }

    function Cargar_valor_Parametros($exte = false,$alt = 1) {

        $parametros = array();

        if ($this->Super_estado == false) {
            echo json_encode($parametros);
            
            return;
        }
        $estado = null;
        if ($exte) {
            $estado = 1;
        }
        $idparametro = $this->input->post("idparametro");
        
        $datos = $this->genericas_model->Listar_valor($idparametro,$estado);

        $i = 1;
        $function = $alt == 1 ? 'Mostrar_modal_modificar' : 'mostrar_parametro_modificar';
        foreach ($datos as $row) {

          
            $row["op"] = '';

            if ($this->Super_modifica == 1) {
                if (!$exte) {
                    $row["op"] = '<span style="color: #aa66cc;" title="Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-cog pointer btn btn-default" onclick="mostrar_modal_permisos('.$row["id"].')"></span>';

                    if ($row["estado"] == 1) {
                        $row["estado"] = "Visible";
                        $row["op"] = $row["op"] . ' <span title="Cambiar a oculto" data-toggle="popover" data-trigger="hover" class="fa fa-eye-slash pointer btn btn-default" onclick="confirmar_cambio_estado_parametro(' . $row["id"] . ',0)"></span>';
                    } else {
                        $row["estado"] = "Oculto";
                        $row["op"] = $row["op"] . ' <span title="Cambiar a visible" data-toggle="popover" data-trigger="hover" class="fa fa-eye pointer btn btn-default" onclick="confirmar_cambio_estado_parametro(' . $row["id"] . ',1)"></span>';
                    }
                }else{
                     $row["op"] = $row["op"] . '<span title="Eliminar" style="color: #DE4D4D;"  data-toggle="popover" data-trigger="hover" class="fa fa-trash-o pointer btn btn-default" onclick="confirmar_eliminar_parametro(' . $row["id"] . ',0)"></span>';
                }
               
                $row["op"] = $row["op"] . ' <span style="color: #2E79E5;" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench pointer btn btn-default" onclick="'.$function.'(' . $row["id"] . ')"></span>';
            }else{
                  $row["op"] = '-----';
            }
            $row["indice"] = $i;
            $parametros["data"][] = $row;
            $i++;
        }

        echo json_encode($parametros);
    }
    //Funcion cargar permisos
    function Cargar_permiso() {
        $parametros = array();
        if ($this->Super_estado == false) {
            echo json_encode($parametros);
            return;
        }
        $datos = $this->genericas_model->listadepermiso();
       //  $i = 1;
       //foreach ($datos as $row) {
            //   $row["indice"] = $i;
         //   $parametros["data"][] = $row;
            //   $i++;
       // }

        echo json_encode($datos);
    }

    function Cargar_valor_Parametros_normal() {

        $parametros = array();

        if ($this->Super_estado == false) {
            echo json_encode($parametros);
            return;
        }
        $estado = null;
        $idparametro = $this->input->post("idparametro");
        $datos = $this->genericas_model->Listar_valor($idparametro,$estado);
        echo json_encode($datos);
    }

    function Listar_permisos_perfil() {

        $permisos = array();

        if ($this->Super_estado == false) {
            echo json_encode($permisos);
            return;
        }
        $idperfil = $this->input->post("idperfil");
        $datos = $this->genericas_model->Listar_permisos_perfil($idperfil);

        $i = 1;

        foreach ($datos as $row) {
            $row["op"] = "";
            $row["indice"] = $i;
            if ($this->Super_elimina != 0) {
                $row["op"] = ' <span title="Retirar" data-toggle="popover" data-trigger="hover" class="fa fa-remove pointer btn btn-default" style="color: #DE4D4D;" onclick="Confirmar_Retirar_Actividad(' . $row["id"] . ')"></span>';
            }
            
            $permisos["data"][] = $row;
            $i++;
        }

        echo json_encode($permisos);
    }

    function Eliminar_Actividad() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_elimina == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("id");
                $datos = $this->genericas_model->Eliminar_Actividad($id);
                echo json_encode($datos);
            }
        }
    }

    function obtener_valores_parametro_valox() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            $idparametro = $this->input->post("idparametro");
            $valor = $this->input->post("valor");
            $datos = $this->genericas_model->obtener_valores_parametro_valox($idparametro, $valor);
            echo json_encode($datos);
        }
    }

    function cambiar_estado_cargo() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_elimina == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("id");
                $estado = $this->input->post("estado");
                $datos = $this->genericas_model->cambiar_estado_cargo($id, $estado);
                echo json_encode($datos);
            }
        }
    }

    function Cambiar_estado_Permiso() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("id");
                $estado = $this->input->post("estado");
                $col = $this->input->post("col");
                if ($col == 0) {
                    $col = 'agrega';
                } else if ($col == 1) {
                    $col = 'elimina';
                } else if ($col == 2) {
                    $col = 'modifica';
                }
                $datos = $this->genericas_model->Cambiar_estado_Permiso($id, $estado, $col);
                echo json_encode($datos);
            }
        }
    }

    function Administra_estado_Permiso() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("id");
                $datos = $this->genericas_model->Administra_estado_Permiso($id);
                echo json_encode($datos);
            }
        }
    }

    function obtener_valor_parametro_idaux() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id_aux = $this->input->post("idaux");
        $idparametro = $this->input->post("idparametro");
        $datos = $this->genericas_model->obtener_valores_parametro_aux($id_aux, $idparametro);
        echo json_encode($datos);
    }

    function obtener_valor_parametro_valory() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $valory = $this->input->post("valory");
        $idparametro = $this->input->post("idparametro");
        $datos = $this->genericas_model->obtener_valores_parametro_valoy($idparametro, $valory);
        echo json_encode($datos);
    }

    function Listar_Actividades_Sin_Asignar_Perfil() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $idperfil = $this->input->post("idperfil");
        $datos = $this->genericas_model->Listar_Actividades_Sin_Asignar_Perfil($idperfil);
        echo json_encode($datos);
    }

    function Listar_cargos_sin_Asignar_Departamento() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $iddepartamento = $this->input->post("iddepartamento");
        $datos = $this->genericas_model->Listar_cargos_sin_Asignar_Departamento($iddepartamento);
        echo json_encode($datos);
    }

    function obtener_valor_parametro_id() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $idparametro = $this->input->post("idparametro");

        $datos = $this->genericas_model->obtener_valor_parametro_id($idparametro);



        echo json_encode($datos);
    }

    function obtener_valor_parametro_id_2() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $idparametro = $this->input->post("idparametro");

        $datos = $this->genericas_model->obtener_valor_parametro_id_2($idparametro);



        echo json_encode($datos);
    }

    function obtener_valores_parametro() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $idparametro = $this->input->post("idparametro");

        $datos = $this->genericas_model->obtener_valores_parametro($idparametro);



        echo json_encode($datos);
    }

    public function obtener_datos_valor_parametro() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("id");
        $tipo = $this->input->post("tipo");
        $datos = $this->genericas_model->obtener_datos_valor_parametro($id, $tipo);

        echo json_encode($datos);
    }

    function TieneActividad() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $perfil = $_SESSION["perfil"];
        $actividad = "AppReserva";

        $datos = $this->genericas_model->TieneActividad($perfil, $actividad);
        if ($datos == true) {
            echo json_encode(1);
            return;
        } else {
            echo json_encode(0);
            return;
        }
        echo json_encode(0);
        return;
    }

    public function guardar_Parametro() {
        if ($this->Super_estado == false) {
            echo ("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {

                $nombre = $this->input->post("nombre");
                $descripcion = $this->input->post("descripcion");
                if (empty($nombre) || empty($descripcion) || ctype_space($nombre) || ctype_space($descripcion)) {
                    echo json_encode(1);
                } else {
                    $existe = $this->genericas_model->Existe_Nombre_Parametro($nombre);
                    if ($existe == true) {
                        echo json_encode(3);
                    } else {
                        $nombre = trim($nombre);
                        $descripcion = trim($descripcion);
                        $resultado = $this->genericas_model->guardar(
                                $nombre, $descripcion
                        );

                        echo json_encode($resultado);
                    }
                }
            }
        }
    }
   
    public function guardar_actividad_perfil() {
        if ($this->Super_estado == false) {
            echo ("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {
                $idperfil = $this->input->post("idperfil");
                $idactividad = $this->input->post("idactividad");
                if (empty($idperfil) || empty($idactividad) || ctype_space($idperfil) || ctype_space($idactividad)) {
                    echo json_encode(1);
                } else {

                    $resultado = $this->genericas_model->Agregar_permisos_perfil(
                            $idperfil, $idactividad
                    );

                    echo json_encode($resultado);
                }
            }
        }
    }

    public function Agregar_cargos_departamento() {
        if ($this->Super_estado == false) {
            echo ("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {
                $idcargo = $this->input->post("idcargo");
                $iddepartamento = $this->input->post("iddepartamento");
                $dep = $this->genericas_model->obtener_valor_parametro_id($iddepartamento);
                $car = $this->genericas_model->obtener_valor_parametro_id($idcargo);

                if (empty($dep) || empty($car)) {
                    echo json_encode(-1);
                    return;
                }
                if (empty($idcargo) || empty($iddepartamento) || ctype_space($idcargo) || ctype_space($iddepartamento)) {
                    echo json_encode(1);
                }if (($car[0]["id_aux"] == "aux_aud" || $car[0]["id_aux"] == "ResAud") && $dep[0]["id_aux"] != "Dep_Sis") {
                    echo json_encode(2);
                } else {

                    $resultado = $this->genericas_model->Agregar_cargos_departamento(
                            $idcargo, $iddepartamento
                    );

                    echo json_encode($resultado);
                }
            }
        }
    }

    public function agregar_valor_parametro() {
        if ($this->Super_estado == false) {
            echo ("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {
                $idparametro = $this->input->post("idparametro");
                $id_aux      = $this->input->post("id_aux");
                $descripcion = $this->input->post("descripcion");
                $nombre      = $this->input->post("nombre");
                $valory      = $this->input->post('valory');
                $valorz      = $this->input->post('valorz');
                $valora      = $this->input->post('valora');
                $valorb      = $this->input->post('valorb');

                if (empty($nombre) || empty($idparametro) || ctype_space($idparametro)) {
                    echo json_encode(4);
                } else {
                    if ($this->genericas_model->Existe_Nombre_valor_Parametro($nombre, $idparametro) || $this->genericas_model->Existe_Nombre_Parametro($nombre) || ($id_aux && $this->genericas_model->Existe_Id_Aux($id_aux))) {
                        echo json_encode(3);
                    } else {
                        $data = array(
                            'idparametro' => $idparametro,
                            'id_aux'      => $id_aux ? $id_aux : null,
                            'valor'       => $nombre,
                            'valorx'      => $descripcion,
                            'valory'      => $valorz,
                            'valorz'      => $valorz,
                            'valora'      => $valora,
                            'valorb'      => $valorb,
                        );

                        $agregar = $this->genericas_model->agregar_valor_parametro($data);
                        echo json_encode($agregar);
                    }
                }
            }
        }
    }

    public function editar_valor_parametro() {
        if ($this->Super_estado == false) {
            echo ("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {
                $idparametro = $this->input->post("idparametro");
                $id          = $this->input->post('id');
                $id_aux      = $this->input->post("id_aux");
                $aux_id      = $this->input->post("aux_id");
                $descripcion = $this->input->post("descripcion");
                $nombre      = $this->input->post("nombre");
                $valory      = $this->input->post('valory');
                $valorz      = $this->input->post('valorz');
                $valora      = $this->input->post('valora');
                $valorb      = $this->input->post('valorb');

                if (empty($nombre) || empty($idparametro) || ctype_space($idparametro)) {
                    echo json_encode(4);
                } else {
                    if ($this->genericas_model->Existe_Nombre_valor_Parametro($nombre, $idparametro) || $this->genericas_model->Existe_Nombre_Parametro($nombre) || ($id_aux && $this->genericas_model->Existe_Id_Aux($id_aux, $id))) {
                        echo json_encode(3);
                    } else {
                        $data = array(
                            'valor'  => $nombre,
                            'valorx' => $descripcion,
                            'valory' => $valory,
                            'valorz' => $valorz,
                            'valora' => $valora,
                            'valorb' => $valorb,
                        );
                        if($aux_id != $id_aux) {
                            $data['id_aux'] = $id_aux ? $id_aux : null;
                        }
                        $agregar = $this->genericas_model->editar_valor_parametro($data, $id);
                        echo json_encode($agregar);
                    }
                }
            }
        }
    }

    public function guardar_valor_Parametro() {
        if ($this->Super_estado == false) {
            echo ("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {

                $valory = "";
                $nombre = $this->input->post("nombre");
                $descripcion = $this->input->post("descripcion");
                $idparametro = $this->input->post("idparametro");
                $id_aux = $this->input->post("id_aux");
                if (isset($_POST["valory"])) {
                    $valory = $this->input->post("valory");
                }
                if (empty($nombre) || empty($idparametro) || ctype_space($idparametro) || ctype_space($nombre) || ctype_space($descripcion) || ($idparametro == 25 && empty($valory))) {
                    echo json_encode(1);
                } else {
                    if(empty($descripcion)){
                        $descripcion= "Ninguna";
                    }
                    $nombre = trim($nombre);
                    $descripcion = trim($descripcion);
                    $existe = $idparametro != 20 ? $this->genericas_model->Existe_Nombre_valor_Parametro($nombre, $idparametro) : false;
                    if ($existe == true) {
                        echo json_encode(3);
                    } else {
                        $resultado = $this->genericas_model->guardar_valor( $nombre, $descripcion, $idparametro, $id_aux, $valory);
                        echo json_encode($resultado);
                    }
                }
            }
        }
    }

    public function cambio_estado_parametro() {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("idparametro");
                $estado = $this->input->post("estado");

                $resultado = $this->genericas_model->cambio_estado_parametro($id, $estado);

                echo json_encode($resultado);
            }
        }
    }

    public function Modificar_valor_Parametro() {
        if ($this->Super_estado == false) {
            echo ("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("idparametro");
                $nombre = $this->input->post("nombre");
                $descripcion = $this->input->post("descripcion");
                $valory = null;
                
                if ( empty($id) || ctype_space($id) ) {
                    echo json_encode(2);
                    return;
                }
                

                $datos = $this->genericas_model->obtener_valor_parametro_id($id)[0];
                $idparametro = $datos['idparametro'];

                if (isset($_POST["valory"]) && $idparametro == 25)  $valory = $this->input->post("valory");

                if (empty($nombre) || empty($descripcion)  || ctype_space($nombre) || ctype_space($descripcion) || ($idparametro == 25 && empty($valory))) {
                    echo json_encode(2);
                } else {
                    $nombre = trim($nombre);
                    $descripcion = trim($descripcion);
                    $existe = $idparametro != 20 ? $this->genericas_model->Existe_Nombre_valor_Parametro($nombre, $idparametro) : false;
                    if ($existe == true &&  $datos['valor'] != $nombre) {
                        echo json_encode(3);
                        return;
                    }
                    $resultado = $this->genericas_model->Modificar_Valor_parametro($id, $nombre, $descripcion,$valory);
                    echo json_encode($resultado);
                }
            }
        }
    }
    
    function traer_valores_permisos() {
        $idparametro = $this->input->post("idparametro");
        $idvalorparametro = $this->input->post("idvalorparametro");
        $datos = $this->Super_estado ? $this->genericas_model->traer_valores_permisos($idparametro, $idvalorparametro) : array();
        echo json_encode($datos);
    }
    function traer_valores_permisos_2() {
        $idparametro = $this->input->post("idparametro");
        $idvalorparametro = $this->input->post("idvalorparametro");
        $datos = $this->Super_estado ? $this->genericas_model->traer_valores_permisos_2($idparametro, $idvalorparametro) : array();
        echo json_encode($datos);
    }
    //habilitar
    function habilitar() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $vp_secundario = $this->input->post("vp_secundario");
            $vp_principal= $this->input->post("vp_principal");
            $vp_principal_id = $this->input->post("vp_principal_id");
            $vp_secundario_id = $this->input->post("vp_secundario_id");
            if (empty($vp_principal_id)) {
                $resp= ['mensaje'=>"Seleccione Valor parametro principal",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else if (empty($vp_secundario_id)) {
                $resp= ['mensaje'=>"Seleccione Valor parametro segundario",'tipo'=>"info",'titulo'=> "Oops.!"];
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
                    $add = $this->genericas_model->guardar_datos($data,'permisos_parametros');
                    if($add != 2) $resp= ['mensaje'=>"Error al asignar el permiso, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else{
                    $resp= ['mensaje'=>"El permiso ya fue habilitado anteriormente.",'tipo'=>"info",'titulo'=> "Oops.!"];
                }

            }  
        }
      echo json_encode($resp);
    }

    function deshabilitar() {
        if (!$this->Super_estado) {
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            $id_permiso = $this->input->post("id_permiso");
            
            if (empty($id_permiso)) {
                $resp= ['mensaje'=>"Seleccione el permiso a Deshabilitar",'tipo'=>"info",'titulo'=> "Oops.!"];
            }else{
                $resp= ['mensaje'=>"",'tipo'=>"success",'titulo'=> "Permiso Deshabilitado.!"];
                 $add = $this->genericas_model->eliminar_datos($id_permiso,'permisos_parametros');
                if($add != 2) $resp= ['mensaje'=>"Error al Deshabilitado el permiso, contacte con el administrador",'tipo'=>"error",'titulo'=> "Oops.!"];
            }  
        }
      echo json_encode($resp);
    } 
    
    public function nuevo_valor_Parametro() {
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_agrega == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{               
                $nombre = $this->input->post("nombre");
                $descripcion = !empty($_POST["descripcion"]) ? $this->input->post("descripcion") : "Ninguna";
                $idparametro = $this->input->post("idparametro");
                $valory = isset($_POST["valory"]) ? $this->input->post("valory") : '';
                $id_aux = isset($_POST["id_aux"]) ? $this->input->post("id_aux") : NULL;             
                if( $idparametro == 25){ 
                      $str = $this->verificar_campos_string(['Nombre'=>$nombre, 'Descripcion'=>$descripcion, "valory"=>$valory]);
                }else $str = $this->verificar_campos_string(['Nombre'=>$nombre, 'Descripcion'=>$descripcion]);
                if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                }else{
                    $existe = $idparametro != 20 ? $this->genericas_model->Existe_Nombre_valor_Parametro($nombre, $idparametro) : false;
                    if ($existe) {
                        $resp = ['mensaje'=>"El Nombre que desea guardar ya existe en el sistema.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $add = $this->genericas_model->guardar_valor( $nombre, $descripcion, $idparametro, $id_aux, $valory);                        
                        if($add != 2){
                              $resp= ['mensaje'=>"Error al guardar información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }else $resp= ['mensaje'=>"Datos Guardados.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    }
                }
            }
        }
        echo json_encode($resp);
    }

    public function mod_valor_parametro() {
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{              
                $id = $this->input->post("id_idparametro");
                $nombre = $this->input->post("nombre");
                $descripcion = $this->input->post("descripcion");
                $datos = $this->genericas_model->obtener_valor_parametro_id($id)[0];
                $idparametro = $datos['idparametro'];
                $valory = (isset($_POST["valory"]) && $idparametro == 25) ? $this->input->post("valory") : NULL;              
                
                if( $idparametro == 25){ 
                      $str = $this->verificar_campos_string(['Nombre'=>$nombre, 'Descripcion'=>$descripcion, "valory"=>$valory]);
                }else $str = $this->verificar_campos_string(['Nombre'=>$nombre, 'Descripcion'=>$descripcion]);
                 if (is_array($str)) {
                    $resp = ['mensaje'=>"El campo ". $str['field'] ."  no debe estar vacio.", 'tipo'=>"info", 'titulo'=> "Oops.!"];  
                }else{
                    $existe = $idparametro != 20 ? $this->genericas_model->Existe_Nombre_valor_Parametro($nombre, $idparametro) : false;
                    if ($existe == true &&  $datos['valor'] != $nombre) {
                          $resp = ['mensaje'=>"El Nombre que desea guardar ya existe en el sistema.", 'tipo'=>"info", 'titulo'=> "Oops.!"];
                    }else{
                        $mod = $this->genericas_model->Modificar_Valor_parametro($id, $nombre, $descripcion,$valory);
                        if($mod != 1){
                            $resp= ['mensaje'=>"Error al guardar información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                        }else $resp= ['mensaje'=>"Datos Guardados.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];
                    }                
                }
            }
        }
        echo json_encode($resp);
    }
    

    public function cambiar_estado_parametro() {
        if(!$this->Super_estado){
            $resp= ['mensaje'=>"",'tipo'=>"sin_session",'titulo'=> ""];
        }else{
            if ($this->Super_modifica == 0) {
                $resp= ['mensaje'=>"No tiene Permisos Para Realizar Esta operación.",'tipo'=>"error",'titulo'=> "Oops.!"];
            }else{  
                $id = $this->input->post("id_idparametro");
                $estado = $this->input->post("estado");
                $mod = $this->genericas_model->cambio_estado_parametro($id, $estado);
                if($mod != 1){ $resp= ['mensaje'=>"Error al eliminar la información, contacte con el administrador.",'tipo'=>"error",'titulo'=> "Oops.!"];
                }else $resp= ['mensaje'=>"Datos eliminados.",'tipo'=>"success",'titulo'=> "Proceso Exitoso.!"];                
            }           
        }
        echo json_encode($resp);
    }
    
	public function verificar_campos_string($array){
		foreach ($array as $row) {
			if (empty($row) || ctype_space($row)) {
				return ['type' => -2, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
    }
    
    public function buscar_parametro(){
        $datos = array();
        if (!$this->Super_estado) {
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        } else {            
            $nombre_p = $this->input->post("valor_buscado");                
            if (!empty($nombre_p)) $datos = $this->genericas_model->buscar_parametro($nombre_p);            

        };
        echo json_encode($datos); 
    }
}


?>
