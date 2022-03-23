<?php

use PhpOffice\PhpSpreadsheet\Shared\Date;

class personas_control extends CI_Controller
{
    //Variables encargadas de los permisos que tiene el usuario en session
    public $Super_estado = false;
    public $Super_elimina = 0;
    public $Super_modifica = 0;
    public $Super_agrega = 0;

    //Construtor del controlador, se importa el modelo personas_model y se inicia la session
    public function __construct()
    {
        parent::__construct();
        $this->load->model('personas_model');
        $this->load->model('genericas_model');
        session_start();
        date_default_timezone_set("America/Bogota");
        //la variable Super_estado es la encargada de notificar si el usuario esta en sesion, si no esta en sesion no podra ejecutar ninguna funcion, cuando pasa eso se retorna sin_session en la funcion que se esta ejecutando,por otro lado las variables Super_elimina, Super_modifica, Super_agrega se encarga de delimitar los permisos que tiene el perfil del usuario en la actividad que esta trabajando, si no tiene permiso las variables toman un valor de 0 y no les permite ejecutar la funcion retornando -1302.
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
        }
    }
    /**
     * Mustra la pagina persona cargando el header alterno
     *
     * @return void
     */
    public function index()
    {
        $pages = "inicio";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], "personas");
            if (!empty($datos_actividad)) {
                $pages = "personas";
                $data['js'] = "Personas";
                $data['actividad'] = $datos_actividad[0]["id_actividad"];
            } else {
                $pages = "sin_session";
                $data['js'] = "";
                $data['actividad'] = "Permisos";
            }
        }
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    /**
     * lista las personas registradas en la aplicacion
     * @return Array
     */

    public function Cargar_personas()
    {
        $personas = array();
        if ($this->Super_estado == false) {
            echo json_encode($personas);
            return;
        }
        $i = 1;
        $id = $this->input->post('buscar');
        $id_tipo_cargo = $this->input->post('id_tipo_cargo');
        $id_tipo_persona = $this->input->post('id_tipo_persona');
        $id_tipo_contrato = $this->input->post('id_tipo_contrato');
        $id_tipo_perfil = $this->input->post('id_tipo_perfil');
        $fecha_inicial = $this->input->post('fecha_inicial');
        $fecha_final = $this->input->post('fecha_final');
        //Para que la libreria de la tabla con la que se esta trabajando muestra la informacion es necesario enviarle los datos en una matriz.
        // $datos = $this->personas_model->Listar($dato, $id_tipo_cargo, $id_tipo_persona, $id_tipo_contrato, $id_tipo_perfil, $fecha_inicial, $fecha_final);

        if (!empty($id) || !empty($id_tipo_cargo) || !empty($id_tipo_persona) || !empty($id_tipo_contrato) || !empty($id_tipo_perfil) || !empty($fecha_inicial) || !empty($fecha_final)) {
            $admin = false;
            $datos = $this->personas_model->Listar($id, $id_tipo_cargo, $id_tipo_persona, $id_tipo_contrato, $id_tipo_perfil, $fecha_inicial, $fecha_final);

            if (($_SESSION['perfil'] == 'Per_Admin')) {
                $admin = true;
            } else {
                $admin = false;
            }

            foreach ($datos as $row) {
                $row["codigo"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; " class="pointer form-control" ><span> ver</span></span>';
                $row["admin"] = $admin;
                $personas["data"][] = $row;
                $i++;
            }
        }

        echo json_encode($personas);
        return;
    }

    public function sesionActiva()
    {
        $admin = false;
        if (($_SESSION['perfil'] == 'Per_Admin')) {
            $admin = true;
        } else {
            $admin = false;
        }
        echo json_encode($admin);
        return;
    }
    /**
     * Muestra los datos de las personas cuyo nombre, apellido o identificacion contengan la cadana que se envia por post
     * @return Array
     */
    public function Cargar_personas_Dato()
    {
        $personas = array();
        $dato = $this->input->post('dato');
        if ($this->Super_estado == false) {
            echo json_encode($personas);
            return;
        }
        if (!empty($dato)) {
            $datos = $this->personas_model->Listar_dato($dato);
            foreach ($datos as $row) {
                $personas["data"][] = $row;
            }
        }
        echo json_encode($personas);
        return;
    }

    /**
     * Muestra las personas por departamento, se envia por post el id del departamento y e realiza la consulta
     * @return Array
     */

    public function Cargar_personas_departamento()
    {
        $id = $this->input->post('id');
        $personas = array();
        if ($this->Super_estado == false) {
            echo json_encode($personas);
            return;
        }
        $datos = $this->personas_model->Listar_por_departamento($id);

        foreach ($datos as $row) {
            $personas["data"][] = $row;
        }

        echo json_encode($personas);
        return;
    }

    /**
     * Modifica los datos de las personas, se envian por post los datos nuevos y el id de la persona a modificar, ademas valida por numero de identificacion que la persona no se encuetra registrada en el software
     * @return Integer
     */

    public function modificar_persona($modulo = 'otro')
    {
        if ($this->Super_estado == false) {
            echo json_encode(-1000);
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post('id');
                $identificacion = $this->input->post('identificacion');
                $tipo_identificacion = $this->input->post('tipo_identificacion');
                $tipo_persona = $this->input->post('tipo_persona');
                $nombre = $this->input->post('nombre');
                $apellido = $this->input->post('apellido');
                $segundonombre = $this->input->post('segundonombre');
                $segundoapellido = $this->input->post('segundoapellido');
                $celular = $this->input->post('celular');
                $correo = $this->input->post('correo');
                $usuario = $this->input->post('usuario');
                $id_cargo_sap = $this->input->post('codigo_cargo_sap');
                $fecha = $this->input->post('fecha');
                $sueldo = $this->input->post('sueldo');
                $tipo_contrato = $this->input->post('tipo_contrato');

                if ($this->input->post('valor_tipo_perfil_modificar') == 0) {
                    $perfil = "Per_Gen";
                } else {
                    if ($this->input->post('valor_tipo_perfil_modificar') == 1) {
                        $perfil = "Doc";
                    } else {
                        $perfil = isset($perfil) && !empty($perfil) ? $perfil : null;
                    }
                }

                $name = "Myfoto.png";
                $sw = true;
                if (ctype_space($nombre) || ctype_space($apellido) || ctype_space($identificacion) || ctype_space($segundoapellido) || empty($nombre) || empty($apellido) || empty($identificacion) || empty($segundoapellido)) {
                    echo json_encode(-1);
                    return;
                } else {

                    $datos_persona = $this->personas_model->obtener_Datos_persona($id)[0];

                    $existe = $this->personas_model->Existe_Identificacion($identificacion);
                    if (!empty($existe) && $datos_persona['identificacion'] != $identificacion) {
                        echo json_encode(-2);
                        return;
                    } else {
                        if ($tipo_persona == 'PerInt') {
                            // if (empty($id_cargo_sap)) {
                            //     echo json_encode(-3);
                            //     return;
                            // }else
                            if (empty($correo)) {
                                echo json_encode(-4);
                                return;
                            } else if (empty($usuario)) {
                                echo json_encode(-5);
                                return;
                            } else {
                                $existe_usuario = $this->personas_model->Existe_usuario($usuario);
                                if (!empty($existe_usuario) && $datos_persona['usuario'] != $usuario) {
                                    echo json_encode(-6);
                                    return;
                                }
                                $existe_correo = $this->personas_model->Existe_correo($correo);
                                if (!empty($existe_correo) && $datos_persona['correo'] != $correo) {
                                    echo json_encode(-7);
                                    return;
                                }
                            }
                        } else {
                            $cargo = null;
                            $departamento = null;
                            $usuario = null;
                            $perfil = null;
                            if (!empty($correo)) {
                                $existe_correo = $this->personas_model->Existe_correo($correo);
                                if (!empty($existe_correo) && $datos_persona['correo'] != $correo) {
                                    echo json_encode(-7);
                                    return;
                                }
                            } else {
                                $correo = null;
                            }
                        }

                        $id_cargo_sap = $id_cargo_sap ? $id_cargo_sap : null;
                        $fecha = $fecha ? $fecha : null;
                        $sueldo = $sueldo ? $sueldo : null;
                        $tipo_contrato = $tipo_contrato ? $tipo_contrato : null;
                        $resultado = $this->personas_model->Modificar_Persona($id, $identificacion, $tipo_identificacion, $nombre, $apellido, $celular, $correo, $segundoapellido, $segundonombre, $usuario, $perfil, $tipo_persona, $id_cargo_sap, $fecha, $sueldo, $tipo_contrato, '');
                        $this->personas_model->Asignar_Perfiles_usuario($id, $perfil);

                        echo json_encode($resultado);
                    }
                }
            }
        }
        return;
    }

    /**
     * Guarda las personas en la aplicacion, la funcion valida por numero de identificacion que no exista otra persona registrada
     * @return Integer
     */

    public function guardar_persona($modulo = 'otro')
    {
        if ($this->Super_estado == false) {
            echo json_encode(-1000);
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {

                $identificacion = $this->input->post('identificacion');
                $tipo_identificacion = $this->input->post('tipo_identificacion');
                $tipo_persona = "PerInt";
                $nombre = $this->input->post('nombre');
                $apellido = $this->input->post('apellido');
                $segundonombre = $this->input->post('segundonombre');
                $segundoapellido = $this->input->post('segundoapellido');
                $celular = $this->input->post('celular');
                $id_cargo_sap = $this->input->post('codigo_cargo_sap');
                $fecha = $this->input->post('fecha');
                $sueldo = $this->input->post('sueldo');
                $tipo_contrato = $this->input->post('tipo_contrato');
                $correo = $this->input->post('correo');
                $usuario = $this->input->post('usuario');

                if ($this->input->post('valor_tipo_perfil') == 0) {
                    $perfil = "Per_Gen";
                } else {
                    if ($this->input->post('valor_tipo_perfil') == 1) {
                        $perfil = "Doc";
                    } else {
                        $perfil = isset($perfil) && !empty($perfil) ? $perfil : null;
                    }
                }

                $name = "Myfoto.png";
                $sw = true;
                if (ctype_space($nombre) || ctype_space($apellido) || ctype_space($identificacion) || ctype_space($segundoapellido) || empty($nombre) || empty($apellido) || empty($identificacion) || empty($segundoapellido)) {
                    echo json_encode(-1);
                    return;
                } else {
                    $existe = $this->personas_model->Existe_Identificacion($identificacion);
                    if (!empty($existe)) {
                        echo json_encode(-2);
                        return;
                    } else {
                        if ($modulo == 'personas' && $tipo_persona == 'PerInt') {
                            // if (empty($id_cargo_sap)) {
                            //     echo json_encode(-3);
                            //     return;
                            // }else
                            if (empty($correo)) {
                                echo json_encode(-4);
                                return;
                            } else if (empty($usuario)) {
                                echo json_encode(-5);
                                return;
                            } else {
                                $existe_usuario = $this->personas_model->Existe_usuario($usuario);
                                if (!empty($existe_usuario)) {
                                    echo json_encode(-6);
                                    return;
                                }
                                $existe_correo = $this->personas_model->Existe_correo($correo);
                                if (!empty($existe_correo)) {
                                    echo json_encode(-7);
                                    return;
                                }
                            }
                        } else {

                            $cargo = null;
                            $departamento = null;
                            $usuario = null;
                            $perfil = null;
                            if (!empty($correo)) {
                                $existe_correo = $this->personas_model->Existe_correo($correo);
                                if (!empty($existe_correo)) {
                                    echo json_encode(-7);
                                    return;
                                }
                            } else {
                                $correo = null;
                            }
                        }
                        $id_cargo_sap = $id_cargo_sap ? $id_cargo_sap : null;
                        $fecha = $fecha ? $fecha : null;
                        $sueldo = $sueldo ? $sueldo : null;
                        $tipo_contrato = $tipo_contrato ? $tipo_contrato : null;
                        $resultado = $this->personas_model->guardar($identificacion, $tipo_identificacion, $nombre, $apellido, $celular, $correo, $name, $segundoapellido, $segundonombre, $usuario, md5($identificacion), $tipo_persona, $_SESSION['persona'], $perfil, $id_cargo_sap, $fecha, $sueldo, $tipo_contrato);
                        $res = $this->personas_model->datos_persona($identificacion);
                        $this->personas_model->Asignar_Perfiles_usuario($res[0]["id"], $res[0]["id_perfil"]);

                        echo json_encode($resultado);
                    }
                }
            }
        }
        return;
    }

    /**
     * Des habilita la persona enviada por post
     * @return Integer
     */

    public function Eliminar_persona()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_elimina == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("idpersona");
                $persona = $this->personas_model->obtener_Datos_persona($id);
                $nuevo_estado = $persona->{'estado'} ? '0' : '1';
                $usuario = $_SESSION["persona"];
                $fecha = date("Y-m-d H:i:s");
                $resultado = $this->personas_model->Eliminar_Persona($id, $usuario, $fecha, $nuevo_estado);
                echo json_encode($resultado);
            }
        }
        return;
    }

    /**
     * Valida que una persona tenga o no un perfil asignado
     * @return String
     */

    public function Tiene_Perfil()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("idpersona");
        $resultado = $this->personas_model->Tiene_Perfil($id);
        echo json_encode($resultado);
        return;
    }

    /**
     * Le asigna el perfil enviado por post a una persona.
     * @return Integer
     */

    public function Asignar_Perfil()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("id");
                $idperfil = $this->input->post("idperfil");
                $resp = $this->personas_model->Existe_perfil($id, $idperfil);
                $resultado = $resp ? -1 : $this->personas_model->Asignar_Perfil($idperfil, $id);
                echo json_encode($resultado);
            }
        }
        return;
    }

    /**
     * Muestra los datos de una persona, la consulta se realiza por el id
     * @return Array
     */

    public function obtener_datos_persona()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("id");

        $datos = $this->personas_model->obtener_Datos_persona($id);
        echo json_encode($datos);
        return;
    }

    /**
     * Muestra los datos de las personas que pertenecen al area de audiovisuales
     * @return Array
     */

    public function obtener_datos_personas_audiovisuales()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $datos = $this->personas_model->Listar_por_departamento_audiovisual();
        echo json_encode($datos);
        return;
    }

    /**
     * Muestra los datos de una persona la consulta se realiza por el numero de identificacion
     * @return Array
     */

    public function obtener_datos_persona_identificacion()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $identificacion = $this->input->post("identificacion");
        $tipoidentificacion = $this->input->post("id_tipo");
        $datos = $this->personas_model->obtener_Datos_persona_identificacion($identificacion, $tipoidentificacion);
        echo json_encode($datos);
        return;
    }

    /**
     * Muestra los datos de una persona la consulta se realiza por el id de la persona
     * @return Array
     */

    public function obtener_datos_persona_id_completo()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("id");

        $datos = $this->personas_model->obtener_Datos_persona_id_completos($id);
        echo json_encode($datos);
        return;
    }

    /**
     * Se conecta a la base de datos del software de identidades y busca la persona por el numero de identificacion enviado por post.
     * @return Array
     */

    public function Traer_Persona_Identidades()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("id");

        $datos = $this->personas_model->Traer_Persona_Identidades($id);
        echo json_encode($datos);
        return;
    }

    public function Cargar_perfiles_persona($sw = 0)
    {
        $perfiles = array();
        if ($this->Super_estado == false) {
            echo json_encode($perfiles);
            return;
        }
        $id = $sw == 1 ? $_SESSION['persona'] : $this->input->post("id");
        $datos = $this->personas_model->Cargar_perfiles_persona($id);
        if ($sw == 1) {
            echo json_encode($datos);
            return;
        }
        $i = 1;
        foreach ($datos as $row) {
            $row["gestion"] = "<span style='color:#d9534f' title='Eliminar Artículo' data-toggle='popover' data-trigger='hover' class='btn btn-default pointer fa fa-trash-o' onclick='eliminar_perfil_persona(" . $row['id'] . ")'>";
            $row["num"] = $i;
            $perfiles["data"][] = $row;
            $i++;
        }
        echo json_encode($perfiles);
        return;
    }

    public function eliminar_perfil_persona()
    {
        if ($this->Super_elimina == 0) {
            echo json_encode(-1302);
            return;
        }
        $id = $this->input->post("id");
        $resp = $this->personas_model->eliminar_perfil_persona($id);
        echo json_encode($resp);
        return;
    }

    public function Cargar_perfiles_faltantes()
    {
        $perfiles = array();
        if ($this->Super_estado == false) {
            echo json_encode($perfiles);
            return;
        }
        $id = $this->input->post("id");
        $datos = $this->personas_model->Cargar_perfiles_faltantes($id);
        echo json_encode($datos);
        return;
    }
    public function traer_correos_perfil()
    {
        $perfil = $this->input->post("perfil");
        $correos = $this->Super_estado == true ? $this->personas_model->traer_correos_perfil($perfil) : array();
        echo json_encode($correos);
    }
    public function buscar_persona_where()
    {
        $dato = $this->input->post("dato");
        if (!$this->Super_estado) {
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        } else {
            if (empty($dato)) {
                $resp = ['mensaje' => "Ingrese datos de la persona a buscar.", 'tipo' => "info", 'titulo' => "Oops.!"];
            } else {
                $where = "(p.identificacion = '$dato' OR p.correo = '$dato' OR p.usuario = '$dato')";
                $persona = $this->personas_model->buscar_persona_where($where);
                if (empty($persona)) {
                    $resp = ['mensaje' => "Persona no encontrada.", 'tipo' => "info", 'titulo' => "Oops.!"];
                } else {
                    $resp = ['mensaje' => "Persona encontrada.", 'tipo' => "success", 'titulo' => "Oops.!", 'persona' => $persona];
                }
            }
        }
        echo json_encode($resp);
    }

    public function buscar_cargos_sap()
    {
        $cargos = array();
        if ($this->Super_estado) {
            $buscar = $this->input->post('dato_buscar');
            if (!empty($buscar)) {
                $cargos = $this->personas_model->buscar_cargos_sap($buscar);
            }

        }
        echo json_encode($cargos);
    }

    public function actualizar_perfil()
    {
        if ($_SESSION['perfil'] == 'Per_Admin') {
            if ($this->Super_estado == false) {
                echo json_encode(-1000);
                return;
            } else {
                if ($this->Super_modifica == 0) {
                    echo json_encode(-1302);
                } else {

                    $identificacion = $this->input->post("identificacion");
                    $id = $this->input->post("id");
                    $perfil = $this->input->post("id_perfil");
                    $sw = true;

                    $datos_persona = $this->personas_model->obtener_Datos_persona($id)[0];
                    $existe = $this->personas_model->Existe_Identificacion($identificacion);
                    if (!empty($existe) && $datos_persona['identificacion'] != $identificacion) {
                        echo json_encode(-2);
                        return;
                    } else {
                        $resultado = $this->personas_model->actualizar_perfil($id, $identificacion, $perfil, '');
                        echo json_encode($resultado);
                    }
                }
            }
            return;
        }
    }

    public function Asignar_Perfiles_usuario()
    {
        if ($_SESSION['perfil'] == 'Per_Admin') {

            if ($this->Super_estado == false) {
                echo json_encode("sin_session");
                return;
            } else {
                if ($this->Super_agrega == 0) {
                    echo json_encode(-1302);
                } else {
                    $id = $this->input->post("id");
                    $idperfil = $this->input->post("idperfil");
                    $resp = $this->personas_model->Existe_perfil_persona($id, $idperfil);
                    $resultado = $resp ? -1 : $this->personas_model->Asignar_Perfiles_usuario($id, $idperfil);
                    echo json_encode($resultado);
                }
            }
            return;
        } else {
            echo json_encode(-1302);
            return;
        }
    }

    public function traerPerfilesPersona($sw = 0)
    {
        $perfiles = array();
        if ($this->Super_estado == false) {
            echo json_encode($perfiles);
            return;
        }

        $id = $sw == 1 ? $_SESSION['persona'] : $this->input->post("id");

        $datos = $this->personas_model->traerPerfilesPersona($id);
        $predeterminado = $this->personas_model->buscarPredeterminado($id);

        if ($sw == 1) {
            echo json_encode($datos);
            return;
        }

        $i = 1;
        $prede = null;

        if ($_SESSION['perfil'] == 'Per_Admin') {

            foreach ($predeterminado as $row) {

                $prede = $row['id_perfil'];
            }

            foreach ($datos as $row) {

                if ($row['id_perfil'] != $prede) {
                    //se muestran normalmente el resto de perfiles que no son predeterminados
                    $row["pre"] = '<span style="color: #2E79E5;" title="Elegir Predeterminado" data-toggle="popover" data-trigger="hover" class="btn btn-default pointer fa fa-check-circle form-control predeterminado"></span>';
                    $row["gestion"] = "<span style='color:#2E79E5' title='Eliminar perfil' data-toggle='popover' data-trigger='hover' class='btn btn-default pointer fa fa-trash-o' onclick='eliminar_perfil(" . $row['id'] . ")'> ";
                } else {
                    //se marca el perfil que esta predeterminado y se hinabilita que pueda eliminarse, primero tiene que cambiar el el perfil predeterminado para poder eliminarlo
                    $row["pre"] = '<span style="color: #2ECC71 ;" title="Predeterminado" class="btn btn-default pointer fa fa-check-circle form-control predeterminado_asig "></span>';
                    $row["gestion"] = "<span style='color:#2ECC71 ' title='Eliminar perfil' class='btn btn-default pointer fa fa-trash-o form-control predeterminado_asig' ></span>";
                }

                $row["num"] = $i;
                $perfiles["data"][] = $row;
                $i++;
            }
        } else {

            foreach ($datos as $row) {

                $row["gestion"] = "<span style='color:#BCB8AA' title='Sin Permisos' data-toggle='popover' data-trigger='hover' class='btn btn-default pointer fa fa-trash-o'>";

                $row["pre"] = '<span style="color: #BCB8AA;" class="pointer fa fa-check-circle form-control "><span ></span></span>';

                $row["num"] = $i;
                $perfiles["data"][] = $row;
                $i++;
            }
        }

        echo json_encode($perfiles);
        return;
    }

    public function eliminar_perfil()
    {
        if ($_SESSION['perfil'] == 'Per_Admin') {
            if ($this->Super_elimina == 0) {
                echo json_encode(-1302);
                return;
            }
            $id = $this->input->post("id");
            $resp = $this->personas_model->eliminar_perfil($id);
            echo json_encode($resp);
            return;
        }
    }

    public function perfiles_faltantes()
    {
        if ($_SESSION['perfil'] == 'Per_Admin') {
            $perfiles = array();
            if ($this->Super_estado == false) {
                echo json_encode($perfiles);
                return;
            }
            $id = $this->input->post("id");
            $datos = $this->personas_model->perfiles_faltantes($id);
            echo json_encode($datos);
            return;
        }
    }

    public function obtener_persona_sesion()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $_SESSION["persona"];
        $perfil = $_SESSION["perfil"];
        $perfiles = $this->personas_model->perfiles_usuario($id);
        $enviar = [];

        array_push($enviar, $perfil);
        array_push($enviar, $perfiles);
        echo json_encode($enviar);
    }

    public function perfilEnSesion()
    {

        if ($this->Super_estado == false) {
            echo json_encode(-1000);
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {

                $identificacion = $this->input->post("identificacion");
                $id = $this->input->post("id");
                $perfil = $this->input->post("id_perfil");

                $datos_persona = $this->personas_model->obtener_Datos_persona($id)[0];
                $existe = $this->personas_model->Existe_Identificacion($identificacion);
                if (!empty($existe) && $datos_persona['identificacion'] != $identificacion) {
                    echo json_encode(-2);
                    return;
                } else {
                    $resultado = $this->personas_model->actualizar_perfil($id, $identificacion, $perfil, '');
                    $_SESSION['perfil'] = $perfil;
                    echo json_encode($resultado);
                }
            }
        }
        return;
    }

    public function obtener_datos_usuario()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $_SESSION["persona"];
        $datos = $this->personas_model->obtener_datos_usuario($id);
        echo json_encode($datos);
        return;
    }

    public function guardar_datos_excel()
    {

        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }

        $registra = [];
        $no_registra = [];
        $modifica = [];
        $no_modifica = [];

        $datos = $this->input->post("info");

        $tipoId = $this->personas_model->traerTipoId();
        $listaPersonas = $this->personas_model->Listar("", "", "", "", "", "", "");

        $cargos_creados = $this->crearCargos($datos);
        $resp = $this->agregar_tipoId_idCargo($datos, $tipoId);

        $personas = $this->registrarPersonas($resp, $listaPersonas);

        foreach ($personas as $row) {
            if ($row['success'] == true && $row['accion'] == "registra") {
                array_push($registra, $row);
            }

            if ($row['success'] == false && $row['accion'] == "registra") {
                array_push($no_registra, $row);
            }

            if ($row['success'] == true && $row['accion'] == "modifica") {
                array_push($modifica, $row);
            }

            if ($row['success'] == false && $row['accion'] == "modifica") {
                array_push($no_modifica, $row);
            }

        }

        $enviar = [];

        array_push($enviar, $cargos_creados);
        array_push($enviar, $registra);
        array_push($enviar, $no_registra);
        array_push($enviar, $modifica);
        array_push($enviar, $no_modifica);

        echo json_encode($enviar);
        return;
    }

    public function crearCargos($datos)
    {
        $cargos = $this->personas_model->traer_cargos();
        $aux = [];
        foreach ($datos as $key) {
            //$existe = array_search($key['cargos'], array_column($cargos, 'nombre_cargo'));
            $existe = false;
            foreach ($cargos as $row) {
                if ($key['cargos'] == $row['nombre_cargo']) {
                    $existe = true;
                    break;
                }
            }
            if ($existe == false) {
                //$existe_aux = array_search($key['cargos'], array_column($aux, 'cargo'));
                $existe_aux = false;

                foreach ($aux as $row) {
                    if ($key['cargos'] == $row['cargo']) {
                        $existe_aux = true;
                        break;
                    }
                }

                if ($existe_aux == false) {
                    array_push($aux, ['cargo' => $key['cargos']]);
                }
            }
        }
        foreach ($aux as $key) {
            $this->personas_model->nuevo_cargo($key['cargo']);
        }
        return $aux;
    }

    public function agregar_tipoId_idCargo($datos, $tipoId)
    {
        $cargos = $this->personas_model->traer_cargos();
        $resp = [];

        foreach ($datos as $key) {
            $primerNombre = !empty($key['PrimerNombre']) ? $key['PrimerNombre'] : "";
            $segundoNombre = !empty($key['SegundoNombre']) ? $key['SegundoNombre'] : null;
            $primerApellido = !empty($key['PrimerApellido']) ? $key['PrimerApellido'] : "";
            $segundoApellido = !empty($key['SegundoApellido']) ? $key['SegundoApellido'] : null;
            $identificacion = !empty($key['ID']) ? $key['ID'] : null;
            $cargo = !empty($key['cargos']) ? $key['cargos'] : null;
            $fecha = !empty($key['Fecha']) ? $key['Fecha'] : null;
            $importe = !empty($key['Importe']) ? $key['Importe'] : null;
            $claseContrato = !empty($key['claseContrato']) ? $key['claseContrato'] : null;
            $tipoIdentificacion = !empty($key['TipoIdentificacion']) ? $key['TipoIdentificacion'] : null;
            // $correo = !empty($key['Correo']) ? $key['Correo'] : null;

            $existe = array_search($key['cargos'], array_column($cargos, 'nombre_cargo'));
            foreach ($tipoId as $row) {
                $tipoidentificacion = $this->quitar_tildes($row['tipoIdentificacion']);
                if (strtoupper($key['TipoIdentificacion']) == strtoupper($tipoidentificacion)) {
                    array_push($resp, ['identificacion' => $identificacion, 'TipoIdentificacion' => $tipoIdentificacion, 'PrimerNombre' => $primerNombre, 'SegundoNombre' => $segundoNombre, 'PrimerApellido' => $primerApellido, 'SegundoApellido' => $segundoApellido, 'cargos' => $cargo, 'Fecha' => $fecha, 'claseContrato' => $claseContrato, 'Importe' => $importe, 'id_cargo' => $cargos[$existe]["id"], 'id_tipoID' => $row['id']]);
                    //array_push($resp, ['identificacion' => $identificacion, 'TipoIdentificacion' => $tipoIdentificacion, 'PrimerNombre' => $primerNombre, 'SegundoNombre' => $segundoNombre, 'PrimerApellido' => $primerApellido, 'SegundoApellido' => $segundoApellido, 'cargos' => $cargo, 'Fecha' => $fecha, 'claseContrato' => $claseContrato, 'Importe' => $importe, 'id_cargo' => $cargos[$existe]["id"], 'id_tipoID' => $row['id'], 'correo' => $correo]);
                }
            }
        }
        return $resp;
    }

    public function registrarPersonas($resp, $listaPersonas)
    {
        $tipo_persona = "PerInt";
        $perfil = "Per_Gen";
        $personas = [];
        foreach ($resp as $key) {

            // $existe = array_search($key['identificacion'], array_column($listaPersonas, 'identificacion'));
            $existe = false;
            foreach ($listaPersonas as $row) {
                if ($key['identificacion'] == $row['identificacion']) {
                    $existe = true;
                    break;
                }
            }

            if ($existe == false) {
                $str = $this->verificar_campos_string(['identificacion' => $key['identificacion'], 'Primer Nombre' => $key['PrimerNombre'], 'Primer Apellido' => $key['PrimerApellido']]);
                if ($str == 1) {
                    $result = $this->personas_model->guardarExcel($key['identificacion'], $key['id_tipoID'], $key['PrimerNombre'], $key['PrimerApellido'], $key['SegundoApellido'], $key['SegundoNombre'], md5($key['identificacion']), $tipo_persona, $_SESSION['persona'], $perfil, $key['id_cargo'], $key['Importe'], $key['claseContrato'], $key['Fecha']);
                    // $result = $this->personas_model->guardarExcel($key['identificacion'], $key['id_tipoID'], $key['PrimerNombre'], $key['PrimerApellido'], $key['SegundoApellido'], $key['SegundoNombre'],  md5($key['identificacion']), $tipo_persona, $_SESSION['persona'], $perfil, $key['id_cargo'], $key['Importe'], $key['claseContrato'], $key['Fecha'], $key['correo']);
                    if ($result == 4) {
                        $key["accion"] = "registra";
                        $key["success"] = true;
                        $personas[] = $key;
                    } else {
                        $key["accion"] = "registra";
                        $key["success"] = false;
                        $key["errores"] = "Error... Póngase en contacto con el administrador para obtener más información";
                        $personas[] = $key;
                    }
                } else {
                    $key["accion"] = "registra";
                    $key["success"] = false;
                    $key["errores"] = implode(", ", $str);
                    $personas[] = $key;
                }
            } else {
                $str = $this->verificar_campos_string(['identificacion' => $listaPersonas[$existe]["identificacion"]]);
                if ($str == 1) {

                    $sin_vacios = $this->verificar_campos_vacios($key);
                    $result = $this->personas_model->Modificar_Datos_Excel($sin_vacios);

                    if ($result == 4) {
                        $key["accion"] = "modifica";
                        $key["success"] = true;
                        $personas[] = $key;
                    } else {
                        $key["accion"] = "modifica";
                        $key["success"] = false;
                        $key["errores"] = "Error... Póngase en contacto con el administrador para obtener más información";
                        $personas[] = $key;
                    }
                } else {
                    $key["accion"] = "modifica";
                    $key["success"] = false;
                    $key["errores"] = implode(", ", $str);
                    $personas[] = $key;
                }
            }
        }

        return $personas;
    }

    public function verificar_campos_string($array)
    {
        $aux = [];
        foreach ($array as $row) {
            if (empty($row) || ctype_space($row)) {
                $aux = array_keys($array, $row, false);
                return $aux;
            }
        }
        return 1;
    }

    public function verificar_campos_vacios($array)
    {
        $aux = [];
        foreach ($array as $row) {
            if (!empty($row)) {
                $llave = array_keys($array, $row, false);
                $aux = array_merge($aux, [$llave[0] => $row]);
            }
        }
        return $aux;
    }

    public function quitar_tildes($cadena)
    {
        $no_permitidas = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹");
        $permitidas = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E");
        $texto = str_replace($no_permitidas, $permitidas, $cadena);
        return $texto;
    }

    public function registrarPersonaIntegracion()
    {
        $clave = 'Integr@ci0nSicuc@g1l';

        //recibe el post de una persona enviado desde el componente en python
        $persona = json_decode(file_get_contents("php://input"), true);

        if ($persona['clave'] == $clave) {

            $identificacion = !empty($persona['identificacion']) ? $persona['identificacion'] : null;
            $primerNombre = !empty($persona['nombre']) ? $persona['nombre'] : null;
            $segundoNombre = !empty($persona['seg_nombre']) ? $persona['seg_nombre'] : null;
            $primerApellido = !empty($persona['apellido']) ? $persona['apellido'] : null;
            $segundoApellido = !empty($persona['seg_apellido']) ? $persona['seg_apellido'] : null;
            $id_perfil = !empty($persona['perfil']) ? $persona['perfil'] : null;
            $usuario = !empty($persona['usuario']) ? $persona['usuario'] : null;
            $correo = !empty($persona['correo']) ? $persona['correo'] : null;
            $tipo_persona = "PerInt";

            $existe = $this->personas_model->buscarPersonaIntegracion($identificacion);

            $resultado = 0;

            if (empty($existe)) {
                $resultado = $this->personas_model->registrarPersonaIntegracion($identificacion, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $id_perfil, $usuario, $correo, md5($identificacion), $tipo_persona);
            } else {
                $resultado = $this->personas_model->actualizarPersonaIntegracion($identificacion, $primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $id_perfil, $usuario, $correo);
            }

            switch ($resultado) {
                //devuelve los datos registrados o actualizados exitosamente
                case 2: //registro exitoso
                    $envio = array("respuesta" => true, "registrado" => true, "primerNombre" => $primerNombre, "primerApellido" => $primerApellido);
                    echo json_encode($envio);
                    break;
                case 4: //actualizacion exitosa
                    $envio = array("respuesta" => true, "registrado" => false, "primerNombre" => $primerNombre, "primerApellido" => $primerApellido);
                    echo json_encode($envio);
                    break;
                //devuelve los datos que tuvieron errores y por ende no se actualizaron ni registraron.
                case 1: //registro fallido
                    $envio = array("respuesta" => false, "registrado" => true, "primerNombre" => $primerNombre, "primerApellido" => $primerApellido);
                    echo json_encode($envio);
                    break;
                case 3: //actualizacion fallida
                    $envio = array("respuesta" => false, "registrado" => false, "primerNombre" => $primerNombre, "primerApellido" => $primerApellido);
                    echo json_encode($envio);
                    break;
                default:
                    $envio = array("respuesta" => false, "primerNombre" => $primerNombre, "primerApellido" => $primerApellido);
                    echo json_encode($envio);
                    break;
            }
        } else {
            $envio = array("respuesta" => false, "primerNombre" => "no tiene permisos para esta accion", "primerApellido" => " ");
            echo json_encode($envio);
        }

    }

}
