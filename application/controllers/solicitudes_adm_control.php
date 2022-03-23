<?php

class solicitudes_adm_control extends CI_Controller
{

	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;
    var $ruta_personas = "archivos_adjuntos/solicitudesADM/archivos_personas/";
    var $ruta_reembolso = "archivos_adjuntos/solicitudesADM/reembolso/";
    var $ruta_polizas = "archivos_adjuntos/solicitudesADM/polizas/";
    var $ruta_matriculas = "archivos_adjuntos/solicitudesADM/matriculas/";
    var $ruta_logistica = "archivos_adjuntos/solicitudesADM/logistica/";
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('solicitudes_adm_model');
        $this->load->model('comunicaciones_model');
        $this->load->model('genericas_model');
        session_start();
        date_default_timezone_set("America/Bogota");
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
        }
    }

    public function index()
    {
        $pages = "inicio";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], "solicitudesADM");
            if (!empty($datos_actividad)) {
                $pages = "solicitudes_adm";
                $data['js'] = "Solicitudes_adm";
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

    function Listar_solciitudes()
    {
        $solicitudes = array();

        if ($this->Super_estado == false) {
            echo json_encode($solicitudes);
            return;
        }
        $usuario = $_SESSION["persona"];

        $estado_filtro = $this->input->post('estado_filtro');
        $fecha_filtro = $this->input->post('fecha_filtro');
        $tipo_filtro = $this->input->post('tipo_filtro');
        $datos = $this->solicitudes_adm_model->Listar($usuario, $estado_filtro, $tipo_filtro, $fecha_filtro);
        $sw = false;
        if ($_SESSION['perfil'] == "Per_Admin" || $_SESSION['perfil'] == "Per_Admin_adm") {
            $sw = true;
        }
        $isapro = false;
        $isdene = false;


        $i = 1;

        foreach ($datos as $row) {
            $row["codigo"] = '<span title="Mas Informacion" data-toggle="popover" data-trigger="hover"  class="pointer" ><span >ver</span></span>';

            if ($row["requiere_inscripcion"] == "1") {
                $row["requiere_inscripcion"] = "SI";
            } else {
                $row["requiere_inscripcion"] = "NO";
            }

            if ($row["estado_gen"] == "Sol_soli") {
                $row["codigo"] = '<span title="Mas Informacion" data-toggle="popover" data-trigger="hover"  style="background-color: #EABD32;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';

                $row["gestion"] = '
            <span title="Tramitar" data-toggle="popover" data-trigger="hover" style="color: #2E79E5;margin-left: 5px"class="pointer fa fa-retweet btn btn-default" onclick="Gestionar_solicitud(1,' . $row["id"] . ')"></span>
            <span title="Denegar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-ban btn btn-default" onclick="Gestionar_solicitud(3,' . $row["id"] . ')"></span>
              ';
            } else if ($row["estado_gen"] == "Sol_Trami") {
                $row["codigo"] = '<span title="Mas Informacion" data-toggle="popover" data-trigger="hover"  style="background-color: #2E79E5;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';

                $row["gestion"] = '
            <span title="Aprobar" data-toggle="popover" data-trigger="hover" style="color: #00cc00;margin-left: 5px"class="pointer fa fa-check btn btn-default" onclick="Gestionar_solicitud(2,' . $row["id"] . ')"></span>
            <span title="Denegar" data-toggle="popover" data-trigger="hover" style="color: #cc0000;margin-left: 5px"class="pointer fa fa-ban btn btn-default" onclick="Gestionar_solicitud(3,' . $row["id"] . ')"></span>
              ';
            } else if ($row["estado_gen"] == "Sol_Apro") {
                $row["codigo"] = '<span title="Mas Informacion" data-toggle="popover" data-trigger="hover"  style="background-color: #39B23B;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';

                $row["gestion"] = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
                $isapro = true;
            } else if ($row["estado_gen"] == "Sol_Den") {
                $row["codigo"] = '<span title="Mas Informacion" data-toggle="popover" data-trigger="hover"   style="background-color: #d9534f;color: white; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>';

                $row["gestion"] = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
                $isdene = true;
            }


            if ($sw == false) {
                if ($isapro || $isdene) {
                    $row["gestion"] = '<span title="Solicitud Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off pointer"></span>';

                } else {
                    $row["gestion"] = '<span title="Solicitud en Proceso" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half pointer btn"  style="color:#428bca"></span>';

                }

            }

            if ($this->Super_modifica == 0) {
                $row["gestion"] = '<span title="Sin Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn  pointer"></span>';
            }

            $solicitudes["data"][] = $row;
            $i++;
        }

        echo json_encode($solicitudes);
    }

    function Listar_detalle_tiquetes_id()
    {
        $presonas = array();

        if ($this->Super_estado == false) {
            echo json_encode($presonas);
            return;
        }
        $id = $this->input->post('id');
        $datos = $this->solicitudes_adm_model->Listar_detalle_tiquetes_id($id);
        $i = 1;
        foreach ($datos as $row) {
            $sw = false;
            $row["codigo"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span>ver</span></span>';
            $row["detalle"] = "";
            if ($row["estado_solicitud"] == "Sol_soli") {

                if ($this->Super_elimina == 1) {
                    $row["detalle"] = ' <span style="color: #DE4D4D;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o btn btn-default pointer btn btn-default" onclick="vaidar_estado_Actual_solicitud(' . $row["id_solicitud"] . ', 2,' . $row["id"] . ')"></span>';
                    $sw = true;
                }
                if ($this->Super_modifica == 1) {
                    $row["detalle"] = $row["detalle"] . '  <span style="color: #2E79E5;" title="Editar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default pointer btn btn-default" onclick="vaidar_estado_Actual_solicitud(' . $row["id_solicitud"] . ', 7,' . $row["id"] . ',0)"></span>';
                    $sw = true;
                }
                if (!$sw) {
                    $row["detalle"] = '<span title="Sin Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
                }

            } else {
                $row["detalle"] = '<span title="Sin Opciones" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
            }

            $presonas["data"][] = $row;
            $i++;
        }

        echo json_encode($presonas);
    }

    function Listar_responsables_buses_id()
    {
        $responsables = array();

        if ($this->Super_estado == false) {
            echo json_encode($responsables);
            return;
        }
        $id = $this->input->post('id');
        $datos = $this->solicitudes_adm_model->Listar_responsables_buses_id($id);
        $i = 1;
        foreach ($datos as $row) {
            if ($row["estado_solicitud"] == "Sol_soli") {
            if ($this->Super_elimina == 0) {
                $row["op"] = '<span title="Sin Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
            } else {
                $row["op"] = ' <span style="color: #DE4D4D;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o btn btn-default pointer" onclick="vaidar_estado_Actual_solicitud(' . $row["id_solicitud"] . ', 3,' . $row["id_res"] . ',' . $row["idtransporte"] . ')"></span>';
            }
        } else {
            $row["op"] = '<span title="Sin Opciones" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
        }
            $responsables["data"][] = $row;
            $i++;
        }

        echo json_encode($responsables);
    }

    function Listar_responsables_tipo3_id()
    {
        $responsables = array();

        if ($this->Super_estado == false) {
            echo json_encode($responsables);
            return;
        }
        $id = $this->input->post('id');
        $datos = $this->solicitudes_adm_model->Listar_responsables_tipo3_id($id);
        $i = 1;
        foreach ($datos as $row) {
            if ($row["estado_solicitud"] == "Sol_soli") {
            if ($this->Super_elimina == 0) {
                $row["op"] = '<span title="Sin Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
            } else {
                $row["op"] = ' <span style="color: #DE4D4D;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o btn btn-default pointer" onclick="vaidar_estado_Actual_solicitud(' . $row["id_solicitud"] . ', 12 ,' . $row["id_res"] . ',' . $row["idtransporte"] . ')"></span>';
            }
        } else {
            $row["op"] = '<span title="Sin Opciones" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
        }
            $responsables["data"][] = $row;
            $i++;
        }

        echo json_encode($responsables);
    }

    function Listar_detalle_bus_id()
    {
        $detalles = array();

        if ($this->Super_estado == false) {
            echo json_encode($detalles);
            return;
        }
        $id = $this->input->post('id');
        $datos = $this->solicitudes_adm_model->Listar_detalle_bus_id($id);
        $row["responsables"] = "";
        $i = 1;
        foreach ($datos as $row) {
            $row["codigo"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span>ver</span></span>';
            if ($row["estado_solicitud"] == "Sol_soli") {
                $sw = false;
                if ($this->Super_elimina == 1) {
                    $row["responsables"] = ' <span style="color: #DE4D4D;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o btn btn-default pointer"  onclick="vaidar_estado_Actual_solicitud(' . $row["id_solicitud"] . ', 10,' . $row["id"] . ',0)"></span> ';
                    $sw = true;
                }

                if ($this->Super_modifica == 1) {
                    $row["responsables"] = $row["responsables"] . '  <span style="color: #2E79E5;" title="Editar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default pointer" onclick="vaidar_estado_Actual_solicitud(' . $row["id_solicitud"] . ', 6,' . $row["id"] . ',0)"></span>';
                    $sw = true;
                }
                if (!$sw) {
                    $row["responsables"] = '<span title="Sin Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
                }
            } else {
                $row["responsables"] = '<span title="Sin Opciones" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
            }
            $detalles["data"][] = $row;
            $i++;
        }


        echo json_encode($detalles);
    }

    function Listar_detalle_pedidos_id()
    {
        $detalles = array();

        if ($this->Super_estado == false) {
            echo json_encode($detalles);
            return;
        }
        $id = $this->input->post('id');
        $datos = $this->solicitudes_adm_model->Listar_detalle_pedidos_id($id);
        $row["gestion"] = "";
        $i = 1;
        foreach ($datos as $row) {
            $row["codigo"] = '<span  title="Mas Informacion" data-toggle="popover" data-trigger="hover" style="background-color: white;color: black; width: 100%; ;" class="pointer form-control" ><span>ver</span></span>';
            if ($row["estado_solicitud"] == "Sol_soli") {
                $sw = false;
                if ($this->Super_elimina == 1) {
                    $row["gestion"] = ' <span style="color: #DE4D4D;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash-o btn btn-default pointer"  onclick="vaidar_estado_Actual_solicitud(' . $row["id_solicitud"] . ', 13,' . $row["id"] . ',0)"></span> ';
                    $sw = true;
                }

                if ($this->Super_modifica == 1) {
                    $row["gestion"] = $row["gestion"] . '  <span style="color: #2E79E5;" title="Editar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench btn btn-default pointer" onclick="vaidar_estado_Actual_solicitud(' . $row["id_solicitud"] . ', 9,' . $row["id"] . ',0)"></span>';
                    $sw = true;
                }
                if (!$sw) {
                    $row["gestion"] = '<span title="Sin Permisos" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
                }
            } else {
                $row["gestion"] = '<span title="Sin Opciones" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn pointer"></span>';
            }
            $detalles["data"][] = $row;
            $i++;
        }


        echo json_encode($detalles);
    }


    function Guardar()
    {

        if ($this->Super_estado == false) {
            echo json_encode(array("sin_session"));
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(array(-1302));
                return;
            } else {
                $usuario_registra = $_SESSION["persona"];
                $tipo_solicitud = $this->input->post('tipo_solicitud');
                $tipo_calificacion = $this->input->post('tipo_calificacion');
                $cont = $this->input->post('cont');
                $nombre_evento =  str_replace(',',';',$this->input->post('nombre_evento'));
                $fecha_inicio_evento = $this->input->post('fecha_inicio_evento');
                $fecha_final_evento = $this->input->post('fecha_final_evento');

                $tipo_evento = $this->input->post('tipo_evento');
                $con_inscrip = $this->input->post('con_inscrip');

                $valor = null;
                $contacto = null;
                $descuento = null;
                $telefono = null;
                $celular = null;
                $pagina = null;
                $correo = null;



                if (empty($tipo_solicitud)) {
                    echo json_encode(array(-1));
                    return;
                } else if (empty($nombre_evento) || ctype_space($nombre_evento)) {
                    echo json_encode(array(-2));
                    return;
                }

//                
                $limite_dias = $this->genericas_model->obtener_valores_parametro_aux("LimAdm", 20);
                if (empty($limite_dias)) {
                    $limite_dias = 3;
                } else {
                    $limite_dias = $limite_dias[0]["valor"];
                }



                if (empty($fecha_inicio_evento) || ctype_space($fecha_inicio_evento)) {
                    echo json_encode(array(-3));
                    return;
                }


                $fecha_actual = date("Y-m-d H:i");
                $fecha_limite = strtotime('+' . $limite_dias . ' days', strtotime($fecha_actual));

                $fecha_limite = date('Y-m-d H:i', $fecha_limite);

                $fecha_inicial_solicitado = date_create($fecha_inicio_evento);

                $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');


                if ($forma <= $fecha_actual) {
                    echo json_encode(array(-13));
                    return;
                }
                if ($forma <= $fecha_limite && $cont == 0) {
                    echo json_encode(array(-15));
                    return;
                }


                if (empty($fecha_final_evento) || ctype_space($fecha_final_evento)) {
                    echo json_encode(array(-4));
                    return;
                }
                   
                $fecha_salida_solicitado = date_create($fecha_final_evento);
                if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                     echo json_encode(array(-14));
                     return;
                }
               


                if ($con_inscrip == 1) {
                    $valor = $this->input->post('valor');
                    $contacto =  str_replace(',',';',$this->input->post('contacto'));
                    $descuento = $this->input->post('descuento');
                    $telefono = $this->input->post('telefono_contacto');
                    $celular = $this->input->post('celular_contacto');
                    $pagina =  str_replace(',',';',$this->input->post('web_contacto'));
                    $correo =  str_replace(',',';',$this->input->post('correo_contacto'));

                    if (empty($valor) || ctype_space($valor)) {
                        echo json_encode(array(-9));
                        return;
                    } else if (empty($contacto) || ctype_space($contacto)) {
                        echo json_encode(array(-10));
                        return;
                    } else if (empty($celular) || ctype_space($celular)) {
                        echo json_encode(array(-11));
                        return;
                    } else if (empty($correo) || ctype_space($correo)) {
                        echo json_encode(array(-12));
                        return;
                    }
                } else {
                    $con_inscrip = 0;
                    $descuento = 0;
                }
                $solicitud = array($tipo_solicitud, $nombre_evento, $fecha_inicio_evento, $fecha_final_evento, $tipo_evento, $con_inscrip, $valor, $contacto, $descuento, $telefono, $celular, $pagina, $correo, $tipo_calificacion);

                echo json_encode(array(0, $solicitud));
                return;

                echo json_encode(array(-17));
                return;
            }
        }
    }
    function Guardar_solicitud_manual($datos)
    {
        $datos = explode(",", $datos);
        $usuario_registra = $_SESSION["persona"];
        $tipo_solicitud = $datos[0];
        $nombre_evento = $datos[1];
        $tipo_evento = $datos[4];
        $fecha_inicio_evento = $datos[2];
        $fecha_final_evento = null;
        $tipo_calificacion=$datos[13];

        if (!empty($datos[3])) {
            $fecha_final_evento = $datos[3];
        }

        $con_inscrip = $datos[5];



        if ($con_inscrip == 1) {
            $valor = $datos[6];
            $contacto = $datos[7];
            $descuento = $datos[8];
            if ($descuento!=1) {
                $descuento=0;
            }
            $telefono = $datos[9];
            $celular = $datos[10];
            $pagina = $datos[11];
            $correo = $datos[12];
        } else {
            $valor = null;
            $contacto = null;
            $descuento = null;
            $telefono = null;
            $celular = null;
            $pagina = null;
            $correo = null;

        }
        $result = $this->solicitudes_adm_model->guardar($tipo_solicitud, $nombre_evento, $tipo_evento, $fecha_inicio_evento, $fecha_final_evento, $usuario_registra, $con_inscrip, $valor, $contacto, $descuento, $telefono, $celular, $pagina, $correo,$tipo_calificacion);
        if ($result == 0) {
            $id_sol = $this->solicitudes_adm_model->obtener_ultimo_registro_usuario_soladm($usuario_registra);
            return $id_sol;
        }
        return -1;
    }

    function Modificar()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
                return;
            } else {
                $usuario_modifica = $_SESSION["persona"];
                $id = $this->input->post('id');
                $tipo_solicitud = $this->input->post('tipo_solicitud');
                $nombre_evento = $this->input->post('nombre_evento');
                $cont = $this->input->post('cont');
                $fecha_inicio_evento = $this->input->post('fecha_inicio_evento');
                $fecha_final_evento = $this->input->post('fecha_final_evento');

                $tipo_evento = $this->input->post('tipo_evento');
                $con_inscrip = $this->input->post('con_inscrip');

                $valor = null;
                $contacto = null;
                $descuento = null;
                $telefono = null;
                $celular = null;
                $pagina = null;
                $correo = null;

                if (empty($id)) {
                    echo json_encode(-15);
                    return;
                } else if (empty($tipo_solicitud)) {
                    echo json_encode(-1);
                    return;
                } else if (empty($nombre_evento) || ctype_space($nombre_evento)) {
                    echo json_encode(-2);
                    return;
                }

                $limite_dias = $this->genericas_model->obtener_valores_parametro_aux("LimAdm", 20);
                if (empty($limite_dias)) {
                    $limite_dias = 3;
                } else {
                    $limite_dias = $limite_dias[0]["valor"];
                }
                if (empty($fecha_inicio_evento) || ctype_space($fecha_inicio_evento)) {
                    echo json_encode(-3);
                    return;
                }

                $fecha_actual = date("Y-m-d H:i");
                $fecha_limite = strtotime('+' . $limite_dias . ' days', strtotime($fecha_actual));

                $fecha_limite = date('Y-m-d H:i', $fecha_limite);

                $fecha_inicial_solicitado = date_create($fecha_inicio_evento);

                $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');


                if ($forma <= $fecha_actual) {
                    echo json_encode(-13);
                    return;
                }
                if ($forma <= $fecha_limite && $cont == 0) {
                    echo json_encode(-16);
                    return;
                }
                
                if (empty($fecha_final_evento) || ctype_space($fecha_final_evento)) {
                    echo json_encode(-4);
                    return;
                }
                $fecha_salida_solicitado = date_create($fecha_final_evento);
                if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                    echo json_encode(-14);
                    return;
                }
            


                if ($con_inscrip == 1) {
                    $valor = $this->input->post('valor');
                    $contacto = $this->input->post('contacto');
                    $descuento = $this->input->post('descuento');
                    $telefono = $this->input->post('telefono_contacto');
                    $celular = $this->input->post('celular_contacto');
                    $pagina = $this->input->post('web_contacto');
                    $correo = $this->input->post('correo_contacto');

                    if (empty($valor) || ctype_space($valor)) {
                        echo json_encode(-9);
                        return;
                    } else if (empty($contacto) || ctype_space($contacto)) {
                        echo json_encode(-10);
                        return;
                    } else if (empty($celular) || ctype_space($celular)) {
                        echo json_encode(-11);
                        return;
                    } else if (empty($correo) || ctype_space($correo)) {
                        echo json_encode(-12);
                        return;
                    }
                    if ($descuento==false) {
                        $descuento=0;
                    }
                }

                $result = $this->solicitudes_adm_model->modificar($id, $nombre_evento, $tipo_evento, $fecha_inicio_evento, $fecha_final_evento, $con_inscrip, $valor, $contacto, $descuento, $telefono, $celular, $pagina, $correo);
                echo json_encode($result);
                return;
            }
        }
    }

    function Guardar_Solicicitudes_tipo3()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
                return;
            } else {
                $solicitudADD = $this->input->post('solicitudADD');
                $usuario_registra = $_SESSION["persona"];
                $id_tipo_reserva = $this->input->post('tipo_reserva_Adm');
                $codigo_sap = $this->input->post('codigo_sap');
                //$fecha_entrega_reserva = $this->input->post('fecha_entrega_reserva');
                $fecha_entrega_reserva = null;
                $observaciones = $this->input->post('observaciones');

                $columna1 = $this->input->post('columna1');
                $columna2 = $this->input->post('columna2');
                $columna3 = $this->input->post('columna3');
                $columna4 = $this->input->post('columna4');
                $columna5 = $this->input->post('columna5');
                $columna6 = $this->input->post('columna6');
                $requiere = $this->input->post('req_viaticos_reserva');
                $proveedor = $this->input->post('proveedor');
                $tipo_refrigerios = $this->input->post('tipo_refrigerios');
                $tipo_polizas = $this->input->post('tipo_poliza');
                $responsables = $this->input->post('personas');

                if (empty($solicitudADD) || ctype_space($solicitudADD)) {
                    echo json_encode(-1);
                    return;
                }
                if (empty($id_tipo_reserva) || ctype_space($id_tipo_reserva)) {
                    echo json_encode(-15);
                    return;
                }
                if ((empty($codigo_sap) || ctype_space($codigo_sap)) && $id_tipo_reserva != "SolT3_matr") {
                    echo json_encode(-16);
                    return;
                }

                if ($id_tipo_reserva != "SolT3_refr") {
                    $proveedor = null;
                    $tipo_refrigerios = null;
                }
                if ($id_tipo_reserva != "SolT3_poli") {
                    $tipo_polizas = null;
                }
                if ($id_tipo_reserva == "SolT3_matr") {
                    $fecha_salida_ti_reserva = $this->input->post('fecha_salida_tiqu_reserva');
                    $fecha_retorno_ti_reserva = $this->input->post('fecha_retorno_tiqu_reserva');
                    $sw_r = false;
                    $columna1 = $requiere;
                    if ($requiere == "Viaticos y tiquetes" || $requiere == "Tiquetes") {
                        $sw_r = true;
                    }
                    if ($sw_r) {
                        if (empty($fecha_salida_ti_reserva) || ctype_space($fecha_salida_ti_reserva)) {
                            echo json_encode(-20);
                            return;
                        } else if (empty($fecha_retorno_ti_reserva) || ctype_space($fecha_retorno_ti_reserva)) {
                            echo json_encode(-21);
                            return;
                        }

                        $fecha_actual = date("Y-m-d H:i");
                        $fecha_inicial_solicitado = date_create($fecha_salida_ti_reserva);
                        $fecha_salida_solicitado = date_create($fecha_retorno_ti_reserva);
                        $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');


                        if ($forma <= $fecha_actual) {
                            echo json_encode(-22);
                            return;
                        }

                        if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                            echo json_encode(-23);
                            return;
                        }
                        $columna2 = $fecha_salida_ti_reserva;
                        $columna3 = $fecha_retorno_ti_reserva;
                    } else {
                        $columna2 = "0000-00-00 00:00:00";
                        $columna3 = "0000-00-00 00:00:00";
                    }
                }



                if ($id_tipo_reserva != "SolT3_matr") {
                    $existe_codigo = $this->genericas_model->obtener_valores_parametro_valox(25, $codigo_sap);
                    if (empty($existe_codigo)) {
                        echo json_encode(-17);
                        return;
                    }
                    $codigo_sap = $existe_codigo[0]["id"];
                } else {
                    $codigo_sap = null;
                }


                if ($id_tipo_reserva == "SolT3_remb") {
                    $nombre = "remb";
                    $cargo = $this->cargar_archivo("archivo0", $this->ruta_reembolso, $nombre);
                    if ($cargo[0] == -1) {
                        echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                        return;
                    }

                    $columna2 = $cargo[1];
                }
                if ($id_tipo_reserva == "SolT3_poli") {
                    if ($tipo_polizas == "Poli_add") {

                        $nombre = "poli";
                        $cargo = $this->cargar_archivo("archivo0", $this->ruta_polizas, $nombre);
                        if ($cargo[0] == -1) {
                            echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                            return;
                        }
                        $columna1 = $cargo[1];

                        $nombre = "cont";
                        $cargo = $this->cargar_archivo("archivo1", $this->ruta_polizas, $nombre);
                        if ($cargo[0] == -1) {
                            echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                            return;
                        }
                        $columna2 = $cargo[1];
                        
                    }else if ($tipo_polizas == "Poli_nue") {

                        $nombre = "contr";
                        $cargo = $this->cargar_archivo("archivo0", $this->ruta_polizas, $nombre);
                        if ($cargo[0] == -1) {
                            echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                            return;
                        }
                        $columna1 = $cargo[1];
                        $columna2 = null;
                        
                    } else {
                        $columna1 = null;
                        $columna2 = null;
                    }
                }
                if ($id_tipo_reserva == "SolT3_matr") {
                    $nombre = "matr";
                    $cargo = $this->cargar_archivo("archivo0", $this->ruta_matriculas, $nombre);
                    if ($cargo[0] == -1) {
                        echo json_encode("Error al cargar los acuerdos(" . $this->ruta_matriculas . ")");
                        return;
                    }

                    $columna4 = $cargo[1];

                    $nombre = "matr";
                    $cargo = $this->cargar_archivo("archivo1", $this->ruta_matriculas, $nombre);
                    if ($cargo[0] == -1) {
                        echo json_encode("Error al cargar el recibo de pago(" . $cargo[1] . ")");
                        return;
                    }

                    $columna5 = $cargo[1];
                }

                if ($id_tipo_reserva == "SolT3_flor") {
                    
                    if (empty($responsables) || ctype_space($responsables)) {
                        echo json_encode(-24);
                        return;
                    }

                    $nombre = "Cap";
                    $cargo = $this->cargar_archivo("archivo0", $this->ruta_personas, $nombre);
                    if ($cargo[0] == -1) {
                        if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                            echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                            return;
                        }else{
                            $columna1 = null;
                        }
                    }else{
                    $columna1 = $cargo[1];
                    }
                }
                if ($id_tipo_reserva == "SolT3_otra") {
                    
                    $nombre = "Otra";
                    $cargo = $this->cargar_archivo("archivo0", $this->ruta_personas, $nombre);
                    if ($cargo[0] == -1) {
                        if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                            echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                            return;
                        }else{
                            $columna1 = null;
                        }
                    }else{
                    $columna1 = $cargo[1];
                    }
                }


                $id_sol = $this->Guardar_solicitud_manual($solicitudADD);
                if ($id_sol == -1) {
                    echo json_encode(-112);
                    return;
                } else {
                    $result_Reserva = $this->solicitudes_adm_model->guardar_sol_reserva($id_tipo_reserva, $codigo_sap, $fecha_entrega_reserva, $observaciones, $columna1, $columna2, $columna3, $columna4, $columna5, $columna6, $id_sol, $usuario_registra, $proveedor, $tipo_refrigerios, $tipo_polizas);
                    if ($id_tipo_reserva == "SolT3_flor") {
                        if ($result_Reserva == 0) {
                            $id_general = $this->solicitudes_adm_model->obtener_ultimo_registro_usuario_otras($usuario_registra, $id_sol);
                            if (!empty($id_general)) {
                                $data = array();
                                $sw = true;
                                $personas = explode(",", $responsables);
                                for ($index2 = 0; $index2 < count($personas); $index2++) {
                                    $x= array(
                                        "id_general" => $id_general,
                                        "id_responsable" => $personas[$index2],
                                        "usuario_registra" => $usuario_registra,
                                    );
                                    array_push($data,$x);      
                                }
                                $result_res = $this->solicitudes_adm_model->guardar_datos($data,"responsables_general");          
                            }
                        }
                    }
                    
                    echo json_encode($result_Reserva);
                    return;
                }
            }
        }
        return;
    }

    function Modificar_tipo3()
    {

        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
                return;
            } else {
                $usuario_registra = $_SESSION["persona"];

                $id = $this->input->post('id');
                $id_tipo_reserva = $this->input->post('tipo_reserva_Adm');
                $codigo_sap = $this->input->post('codigo_sap');
                //$fecha_entrega_reserva = $this->input->post('fecha_entrega_reserva');
                $fecha_entrega_reserva = null;
                $observaciones = $this->input->post('observaciones');
                $columna1 = $this->input->post('columna1');
                $columna2 = $this->input->post('columna2');
                $columna3 = $this->input->post('columna3');
                $columna4 = $this->input->post('columna4');
                $columna5 = $this->input->post('columna5');
                $columna6 = $this->input->post('columna6');
                $requiere = $this->input->post('req_viaticos_reserva');
                $proveedor = $this->input->post('proveedor');
                $tipo_refrigerios = $this->input->post('tipo_refrigerios');
                $tipo_polizas = $this->input->post('tipo_poliza');
                $datos_actuales = $this->solicitudes_adm_model->listar_info_solicitud_tipo3_id($id);
                if (empty($id) || ctype_space($id) || empty($datos_actuales)) {
                    echo json_encode(-14);
                    return;
                }
                if (empty($id_tipo_reserva) || ctype_space($id_tipo_reserva)) {
                    echo json_encode(-15);
                    return;
                }
                if ((empty($codigo_sap) || ctype_space($codigo_sap)) && $id_tipo_reserva != "SolT3_matr") {
                    echo json_encode(-16);
                    return;
                }


                if ($id_tipo_reserva != "SolT3_refr") {
                    $proveedor = null;
                    $tipo_refrigerios = null;
                }
                if ($id_tipo_reserva != "SolT3_poli") {
                    $tipo_polizas = null;
                }
                if ($id_tipo_reserva == "SolT3_matr") {
                    $fecha_salida_ti_reserva = $this->input->post('fecha_salida_tiqu_reserva');
                    $fecha_retorno_ti_reserva = $this->input->post('fecha_retorno_tiqu_reserva');
                    $sw_r = false;
                    $columna1 = $requiere;
                    if ($requiere == "Viaticos y tiquetes" || $requiere == "Tiquetes") {
                        $sw_r = true;
                    }
                    if ($sw_r) {
                        if (empty($fecha_salida_ti_reserva) || ctype_space($fecha_salida_ti_reserva)) {
                            echo json_encode(-20);
                            return;
                        } else if (empty($fecha_retorno_ti_reserva) || ctype_space($fecha_retorno_ti_reserva)) {
                            echo json_encode(-21);
                            return;
                        }

                        $fecha_actual = date("Y-m-d H:i");
                        $fecha_inicial_solicitado = date_create($fecha_salida_ti_reserva);
                        $fecha_salida_solicitado = date_create($fecha_retorno_ti_reserva);
                        $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');


                        if ($forma <= $fecha_actual) {
                            echo json_encode(-22);
                            return;
                        }

                        if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                            echo json_encode(-23);
                            return;
                        }
                        $columna2 = $fecha_salida_ti_reserva;
                        $columna3 = $fecha_retorno_ti_reserva;
                    } else {
                        $columna2 = "0000-00-00 00:00:00";
                        $columna3 = "0000-00-00 00:00:00";
                    }
                }

                if ($id_tipo_reserva != "SolT3_matr") {
                    $existe_codigo = $this->genericas_model->obtener_valores_parametro_valox(25, $codigo_sap);
                    if (empty($existe_codigo)) {
                        echo json_encode(-17);
                        return;
                    }
                    $codigo_sap = $existe_codigo[0]["id"];
                } else {
                    $codigo_sap = null;
                }


                if ($id_tipo_reserva == "SolT3_remb") {
                    $archivo_name = ($datos_actuales->{'columna2'});
                    $nombre = "remb";
                    $cargo = $this->cargar_archivo("archivo0", $this->ruta_reembolso, $nombre);
                    if ($cargo[0] == -1) {
                        if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                            echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                            return;
                        }
                        if ($cargo[1] == "<p>You did not select a file to upload.</p>") {
                            if (is_null($archivo_name) || !file_exists($this->ruta_reembolso . $archivo_name)) {
                                echo json_encode(-24);
                                return;
                            } else {
                                $columna2 = $archivo_name;
                            }
                        }
                    } else {
                        $columna2 = $cargo[1];
                    }
                }
                if ($id_tipo_reserva == "SolT3_poli") {
                    if ($tipo_polizas == "Poli_add") {
                        $archivo_name = ($datos_actuales->{'columna1'});
                        $archivo_name2 = ($datos_actuales->{'columna2'});
                        $nombre = "poli";
                        $cargo = $this->cargar_archivo("archivo0", $this->ruta_polizas, $nombre);
                        if ($cargo[0] == -1) {
                            if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                                echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                                return;
                            }
                            if ($cargo[1] == "<p>You did not select a file to upload.</p>") {
                                if (is_null($archivo_name) || !file_exists($this->ruta_polizas . $archivo_name)) {
                                    echo json_encode(-24);
                                    return;
                                } else {
                                    $columna1 = $archivo_name;
                                }
                            }
                        } else {
                            $columna1 = $cargo[1];
                        }
                        $nombre = "contr";
                        $cargo2 = $this->cargar_archivo("archivo1", $this->ruta_polizas, $nombre);
                        if ($cargo2[0] == -1) {
                            $es = strpos($cargo2[1], "You did not select a file");
                            if ($es === false) {
                                echo json_encode("Error al cargar el archivo(" . $cargo2[1] . ")");
                                return;
                            }
                            if ($es !== false) {
                                if (is_null($archivo_name2) || !file_exists($this->ruta_polizas . $archivo_name2)) {
                                    echo json_encode(-24);
                                    return;
                                } else {
                                    $columna2 = $archivo_name2;
                                }
                            }
                        } else {
                            $columna2 = $cargo2[1];
                        }

                    }else  if ($tipo_polizas == "Poli_nue") {
                        $archivo_name = ($datos_actuales->{'columna1'});
                        $nombre = "contr";
                        $cargo = $this->cargar_archivo("archivo0", $this->ruta_polizas, $nombre);
                        if ($cargo[0] == -1) {
                            if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                                echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                                return;
                            }
                            if ($cargo[1] == "<p>You did not select a file to upload.</p>") {
                                if (is_null($archivo_name) || !file_exists($this->ruta_polizas . $archivo_name)) {
                                    echo json_encode(-24);
                                    return;
                                } else {
                                    $columna1 = $archivo_name;
                                }
                            }
                        } else {
                            $columna1 = $cargo[1];
                            $columna2 = null;
                        }
                    } else {
                        $columna1 = null;
                        $columna2 = null;
                    }
                }
                if ($id_tipo_reserva == "SolT3_matr") {
                    $archivo_name = ($datos_actuales->{'columna4'});

                    $nombre = "matr";
                    $cargo = $this->cargar_archivo("archivo0", $this->ruta_matriculas, $nombre);
                    if ($cargo[0] == -1) {
                        if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                            echo json_encode("Error al cargar los acuerdos(" . $cargo[1] . ")");
                            return;
                        }
                        if ($cargo[1] == "<p>You did not select a file to upload.</p>") {
                            if (is_null($archivo_name) || !file_exists($this->ruta_matriculas . $archivo_name)) {
                                echo json_encode(-24);
                                return;
                            } else {
                                $columna4 = $archivo_name;
                            }
                        }
                    } else {

                        $columna4 = $cargo[1];
                    }
                    $archivo_name = ($datos_actuales->{'columna5'});
                    $cargo = $this->cargar_archivo("archivo1", $this->ruta_matriculas, $nombre);
                    if ($cargo[0] == -1) {
                        $es = strpos($cargo[1], "You did not select a file");
                        if ($es === false) {
                            echo json_encode("Error al cargar el recibo de pago(" . $cargo[1] . ")");
                            return;
                        }
                        if ($cargo[1] == "<p>You did not select a file to upload.</p>") {
                            if (is_null($archivo_name) || !file_exists($this->ruta_matriculas . $archivo_name)) {
                                echo json_encode(-24);
                                return;
                            } else {
                                $columna5 = $archivo_name;
                            }
                        }
                    } else {

                        $columna5 = $cargo[1];
                    }
                }
                if ($id_tipo_reserva == "SolT3_flor") {
                    $archivo_name = ($datos_actuales->{'columna1'});
                    $nombre = "Cap";
                    $cargo = $this->cargar_archivo("archivo0", $this->ruta_personas, $nombre);
                    if ($cargo[0] == -1) {
                        if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                            echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                            return;
                        }else{
                            $columna1 = $archivo_name;
                        }
                    }else{
                    $columna1 = $cargo[1];
                    }
                }
                if ($id_tipo_reserva == "SolT3_otra") {
                    $archivo_name = ($datos_actuales->{'columna1'});
                    $nombre = "Otra";
                    $cargo = $this->cargar_archivo("archivo0", $this->ruta_personas, $nombre);
                    if ($cargo[0] == -1) {
                        if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                            echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                            return;
                        }else{
                            $columna1 = $archivo_name;
                        }
                    }else{
                    $columna1 = $cargo[1];
                    }
                }

                $result = $this->solicitudes_adm_model->Modificar_tipo3($id, $codigo_sap, $fecha_entrega_reserva, $observaciones, $columna1, $columna2, $columna3, $columna4, $columna5, $columna6, $proveedor, $tipo_refrigerios, $tipo_polizas);
                echo json_encode($result);
                return;
            }
        }
    }

    public function Gestionar_solicitud()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
                return;
            }
            $tipo = $this->input->post('tipo');
            $id = $this->input->post('id');
            $mensaje = $this->input->post('mensaje');
            if (!empty($tipo) && !empty($id)) {
                echo $this->solicitudes_adm_model->Gestionar_solicitud($tipo, $id,$mensaje);
                return;
            }
            echo json_encode(-1);
            return;
        }
    }

    public function persona_Tiene_Tiquetes()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {

            $id = $this->input->post('id');
            $idsoli = $this->input->post('idsoli');
            echo $this->solicitudes_adm_model->persona_Tiene_Tiquetes($id, $idsoli);
            return;
        }
    }

    public function persona_Tiene_es_responsable_bus()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {

            $id = $this->input->post('id');
            $idsoli = $this->input->post('idsoli');
            echo $this->solicitudes_adm_model->persona_Tiene_es_responsable_bus($id, $idsoli);
            return;
        }
    }
    public function persona_Tiene_es_responsable_tipo3()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {

            $id = $this->input->post('id');
            $idsoli = $this->input->post('idsoli');
            echo $this->solicitudes_adm_model->persona_Tiene_es_responsable_tipo3($id, $idsoli);
            return;
        }
    }

    public function Guardar_Solicicitudes_tipo1()
    {
        if ($this->Super_estado == false) {
            echo json_encode(array("sin_session"));
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(array(-1302));
                return;
            }
            $sw_fu = false;
            $solicitudADD = $this->input->post('solicitudADD');
            $id_sol = $this->input->post('id');
            $tipo_evento = "";
            if (empty($solicitudADD) && empty($id_sol)) {
                echo json_encode(array(10));
                return;
            }

            if (!empty($solicitudADD)) {
                $sw_fu = true;
                $tipo_evento = explode(",", $solicitudADD);
                $tipo_evento = $tipo_evento[4];

            } else {
                $tipo = $this->solicitudes_adm_model->Buscar_Solicitud_id($id_sol);
                if (empty($tipo)) {
                    echo json_encode(array(10));
                    return;
                }
                $tipo_evento = $tipo[0]["tipo_evento_gen"];

            }


            $observaciones = $this->input->post('observaciones');
            $id_usuario_registra = $_SESSION['persona'];
            $origen = $this->input->post('origen');
            $destino = $this->input->post('destino');
            $re_tiquete = $this->input->post('re_tiquete');
            $re_viaticos = $this->input->post('re_viaticos');
            $re_seguro = $this->input->post('re_seguro');
            $personas = $this->input->post('personas');
            $codsap = $this->input->post('codsap');
            $req_hotel = $this->input->post('re_hotel');
            $fecha_salida_ti = $this->input->post('fecha_salida_tiqu');
            $fecha_retorno_ti = $this->input->post('fecha_retorno_tiqu');
            $fecha_ingreso_hotel = $this->input->post('fecha_ingreso_hotel');
            $fecha_salida_hotel = $this->input->post('fecha_salida_hotel');
            $archivo_visa = null;
            $archivo_pasaporte = null;
            $archivo_agenda = null;
            $nombre = "Doc";
            $ruta = $this->ruta_personas;

            if (empty($origen) || ctype_space($origen)) {
                echo json_encode(array(5));
                return;
            } else if (empty($destino) || ctype_space($destino)) {
                echo json_encode(array(6));
                return;
            } else if (empty($personas) || ctype_space($personas)) {
                echo json_encode(array(9));
                return;
            }

            if ($re_tiquete == 1) {

                if (empty($fecha_salida_ti) || ctype_space($fecha_salida_ti)) {
                    echo json_encode(array(7));
                    return;
                } else if (empty($fecha_retorno_ti) || ctype_space($fecha_retorno_ti)) {
                    echo json_encode(array(8));
                    return;
                }

                $fecha_actual = date("Y-m-d H:i");
                $fecha_inicial_solicitado = date_create($fecha_salida_ti);
                $fecha_salida_solicitado = date_create($fecha_retorno_ti);
                $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');


                if ($forma <= $fecha_actual) {
                    echo json_encode(array(-13));
                    return;
                }

                if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                    echo json_encode(array(-14));
                    return;
                }
            } else {
                $fecha_salida_ti = null;
                $fecha_retorno_ti = null;
            }

            if ($req_hotel == 1) {

                if (empty($fecha_ingreso_hotel) || ctype_space($fecha_ingreso_hotel)) {
                    echo json_encode(array(16));
                    return;
                } else if (empty($fecha_salida_hotel) || ctype_space($fecha_salida_hotel)) {
                    echo json_encode(array(17));
                    return;
                }

                $fecha_actual = date("Y-m-d H:i");
                $fecha_inicial_solicitado = date_create($fecha_ingreso_hotel);
                $fecha_salida_solicitado = date_create($fecha_salida_hotel);
                $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');


                if ($forma <= $fecha_actual) {
                    echo json_encode(array(18));
                    return;
                }

                if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                    echo json_encode(array(19));
                    return;
                }
            } else {
                $fecha_ingreso_hotel = null;
                $fecha_salida_hotel = null;
            }

            if ($codsap != "-----") {
                $existe_codigo = $this->genericas_model->obtener_valores_parametro_valox(25, $codsap);
                if (empty($existe_codigo)) {
                    echo json_encode(array(-17));
                    return;
                }
                $codsap = $existe_codigo[0]["id"];
            } else {
                $codsap = null;
            }
            if ($tipo_evento == "Even_Int" && $re_tiquete == 1) {


                $cargo = $this->cargar_archivo("archivopersona", $ruta, $nombre);
                if ($cargo[0] == -1) {
                    if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                        echo json_encode(array(-19, $cargo[1]));
                        return;
                    }
                    if ($cargo[1] == "<p>You did not select a file to upload.</p>") {
                        echo json_encode(array(-18));
                        return;
                    } else {
                        $archivo_pasaporte = null;
                    }
                } else {
                    $archivo_pasaporte = $cargo[1];
                }

                $cargo_visa = $this->cargar_archivo("archivovisa", $ruta, $nombre);
                if ($cargo_visa[0] == -1) {
                    if ($cargo_visa[1] != "<p>You did not select a file to upload.</p>") {
                        echo json_encode(array(-20, $cargo_visa[1]));
                        return;
                    } else {
                        $archivo_visa = null;
                    }
                } else {
                    $archivo_visa = $cargo_visa[1];
                }
            }

            $cargo_agenda = $this->cargar_archivo("archivootro", $ruta, $nombre);

            if ($cargo_agenda[0] == -1) {
                $es = strpos($cargo_agenda[1], "You did not select a file");
                if ($es === false) {
                    echo json_encode(array(21, $cargo_agenda[1]));
                    return;
                } else {
                    $archivo_agenda = null;
                }
            } else {
                $archivo_agenda = $cargo_agenda[1];
            }

            if ($sw_fu) {
                $id_sol = $this->Guardar_solicitud_manual($solicitudADD);
                if ($id_sol == -1) {
                    echo json_encode(array(-112));
                    return;
                }
            } 
            
                $data = array();
                $sw = true;
                $personas = explode(",", $personas);
               
                for ($index2 = 0; $index2 < count($personas); $index2++) {
                    $x= array(
                        "id_solicitud" => $id_sol,
                        "lugar_origen" => $origen,
                        "lugar_destino" => $destino,
                        "req_tiquete" => $re_tiquete,
                        "fecha_salida" => $fecha_salida_ti,
                        "fecha_retorno" => $fecha_retorno_ti,
                        "req_viaticos" => $re_viaticos,
                        "id_persona" => $personas[$index2],
                        "usuario_registra" => $id_usuario_registra,
                        "cod_sap" => $codsap,
                        "req_seguro" => $re_seguro,
                        "observaciones" => $observaciones,
                        "req_hotel" => $req_hotel,
                        "fecha_ingreso_hotel" => $fecha_ingreso_hotel,
                        "fecha_salida_hotel" => $fecha_salida_hotel,
                        "archivo_adjunto" => $archivo_pasaporte,
                        "archivo_visa" => $archivo_visa,
                        "archivo_agenda" => $archivo_agenda,
                    );
                    array_push($data,$x);      
                }

                $result_res = $this->solicitudes_adm_model->guardar_datos($data,"solic_tiquetes_viaticos");
                if ($result_res == 0) {
                    echo json_encode(array(0, $id_sol));
                    return;
                }
                echo json_encode(array("error", -1));
                return;

            
        }
        echo json_encode(array("error", -1));
        return;

    }

    public function Guardar_Solicicitudes_tipo2()
    {
        if ($this->Super_estado == false) {
            echo json_encode(array("sin_session"));
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(array(-1302));
                return;
            }
            $observaciones = $this->input->post('observaciones');
            $id_usuario_registra = $_SESSION['persona'];
            $origen = $this->input->post('origen');
            $destino = $this->input->post('destino');
            $numpersonas = $this->input->post('num_personas');
            $personas = $this->input->post('personas');
            $codsap = $this->input->post('codsapbuses');
                /*$hora_salida = $this->input->post('hora_salida');
                $hora_retorno = $this->input->post('hora_retorno');
             */
            $hora_salida = null;
            $hora_retorno = null;
            $sw_fu = false;
            $solicitudADD = $this->input->post('solicitudADD');
            $id_sol = $this->input->post('id');


            if (empty($solicitudADD) && empty($id_sol)) {
                echo json_encode(array(10));
                return;
            }
            if (!empty($solicitudADD)) {
                $sw_fu = true;

            }

            if (empty($origen) || ctype_space($origen)) {
                echo json_encode(array(5));
                return;
            } else if (empty($destino) || ctype_space($destino)) {
                echo json_encode(array(6));
                return;
            } else if (empty($personas) || ctype_space($personas)) {
                echo json_encode(array(9));
                return;
            } 
                /*else if (empty($hora_salida) || ctype_space($hora_salida)) {
                    echo json_encode(array(12));
                    return;
                } else if (empty($hora_retorno) || ctype_space($hora_retorno)) {
                    echo json_encode(array(13));
                    return;
                } */

            else if (empty($numpersonas) || ctype_space($numpersonas)) {
                echo json_encode(array(14));
                return;
            } else if (empty($codsap) || ctype_space($codsap)) {
                echo json_encode(array(15));
                return;
            }

           

               /* $fecha_actual = date("Y-m-d H:i");
                $fecha_inicial_solicitado = date_create($hora_salida);
                $fecha_salida_solicitado = date_create($hora_retorno);
                $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');


                if ($forma <= $fecha_actual) {
                    echo json_encode(array(-15));
                    return;
                }

                if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                    echo json_encode(array(-16));
                    return;
                }
             */

            $existe_codigo = $this->genericas_model->obtener_valores_parametro_valox(25, $codsap);
            if (empty($existe_codigo)) {
                echo json_encode(array(-17));
                return;
            }
            $codsap = $existe_codigo[0]["id"];

            if ($sw_fu) {
                $id_sol = $this->Guardar_solicitud_manual($solicitudADD);
                if ($id_sol == -1) {
                    echo json_encode(array(-112));
                    return;
                }
            }

            $result_tra = $this->solicitudes_adm_model->guardar_trasporte($id_sol, $origen, $destino, $hora_salida, $hora_retorno, $numpersonas, $id_usuario_registra, $codsap, $observaciones);
            if ($result_tra == 0) {
                $id_tras = $this->solicitudes_adm_model->obtener_ultimo_registro_usuario_buses($id_usuario_registra, $id_sol);
                if (!empty($id_tras)) {
                    $data = array();
                    $sw = true;
                    $personas = explode(",", $personas);
                    for ($index2 = 0; $index2 < count($personas); $index2++) {
                        $x= array(
                            "id_sol_transporte" => $id_tras,
                            "id_resposnable" => $personas[$index2],
                            "usuario_registra" => $id_usuario_registra,
                        );
                        array_push($data,$x);      
                    }
                    $result_res = $this->solicitudes_adm_model->guardar_datos($data,"responsables_buses");
                    if ($result_res == 0) {
                        echo json_encode(array(0, $id_sol));
                        return;
                    }

                }
                echo json_encode(array(10));
                return;
            }
            echo json_encode(array(-1));
            return;

        }
    }

    public function Guardar_Solicicitudes_tipo4()
    {
        if ($this->Super_estado == false) {
            echo json_encode(array("sin_session"));
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(array(-1302));
                return;
            }
            $sw_fu = false;
            $solicitudADD = $this->input->post('solicitudADD');
            $id_sol = $this->input->post('id');

            if (empty($solicitudADD) && empty($id_sol)) {
                echo json_encode(array(1));
                return;
            }

            if (!empty($solicitudADD)) {
                $sw_fu = true;

            }

            $observaciones = $this->input->post('observaciones');
            $categoria = $this->input->post('tipo_logistica');
            $num_personas = $this->input->post('num_personas');
            $valor_flores = $this->input->post('num_flores');
            $id_usuario_registra = $_SESSION['persona'];
            $codigo_sap = $this->input->post('codigosap');
            $id_responsable = $this->input->post('responsable');
            $lugar_entrega = $this->input->post('lugar_entrega');

            $fecha_entrega = $this->input->post('fecha_entrega');
            //$fecha_retiro = $this->input->post('fecha_retiro');

            //$fecha_entrega = null;
            $fecha_retiro = null;

            $re_manteles = $this->input->post('re_manteles');
            $re_sillas = $this->input->post('re_sillas');
            $re_carpas = $this->input->post('re_carpas');
            $re_vasos = $this->input->post('re_vasos');
            $re_tenedores = $this->input->post('re_tenedores');
            $re_cuchillos = $this->input->post('re_cuchillos');
            $re_mesas = $this->input->post('re_mesas');
            $re_cucharas = $this->input->post('re_cucharas');
            $re_platos = $this->input->post('re_platos');

            $re_flores = $this->input->post('re_flores');
            $re_refri = $this->input->post('re_refri');
            $re_cafe = $this->input->post('re_agua');

            $manteles = $this->input->post('num_manteles');
            $sillas = $this->input->post('num_sillas');
            $carpas = $this->input->post('num_carpas');
            $vasos = $this->input->post('num_vasos');
            $tenedores = $this->input->post('num_tenedores');
            $cuchillos = $this->input->post('num_cuchillos');
            $id_tipo_mesa = $this->input->post('tipo_mesas');
            $mesas = $this->input->post('num_mesas');
            $con_portatil = $this->input->post('re_port');
            $con_sonido = $this->input->post('re_soni');
            $id_tipo_plato = $this->input->post('tipo_platos');
            $platos = $this->input->post('num_platos');
            $id_tipo_cuchara = $this->input->post('tipo_cucharas');
            $cucharas = $this->input->post('num_cucharas');

            $tipo_refrigerios = $this->input->post('tipo_refrigerios');
            $cantidad_refrigerios = $this->input->post('canxperso');
            $tipo_entrega_refri = $this->input->post('tipo_refrigerios_entrega');
            $tipo_entrega_cafe = $this->input->post('tipo_agua_cafe_entrega');
            $con_almuerzo = $this->input->post('re_almuerzo');
            $con_video_beam = $this->input->post('re_vb');
            $coctel = $this->input->post('re_coctel');
            $nombre_add =  null;
            

            if (empty($codigo_sap) || ctype_space($codigo_sap)) {
                echo json_encode(array(3));
                return;
            } else if (empty($id_responsable) || ctype_space($id_responsable)) {
                echo json_encode(array(4));
                return;
            } else if (empty($lugar_entrega) || ctype_space($lugar_entrega)) {
                echo json_encode(array(5));
                return;
            } else if (empty($num_personas) || ctype_space($num_personas)) {
                echo json_encode(array(32));
                return;
            } else if (empty($categoria) || ctype_space($categoria)) {
                echo json_encode(array(38));
                return;
            }else if (empty($fecha_entrega) || ctype_space($fecha_entrega)) {
                  echo json_encode(array(6));
                  return;
            }
            $fecha_actual = date("Y-m-d H:i");
            $fecha_inicial_solicitado = date_create($fecha_entrega);
            $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');
            if ($forma <= $fecha_actual) {
            echo json_encode(array(9));
            return;
            }

                 /*  else if (empty($fecha_retiro) || ctype_space($fecha_retiro)) {
                  echo json_encode(array(7));
                  return;
                  }
                  $fecha_actual = date("Y-m-d H:i");
                  $fecha_inicial_solicitado = date_create($fecha_entrega);
                  $fecha_salida_solicitado = date_create($fecha_retiro);
                  $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');


                  if ($forma <= $fecha_actual) {
                  echo json_encode(array(9));
                  return;
                  }

                  if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                  echo json_encode(array(10));
                  return;
                  }
             */

            $existe_codigo = $this->genericas_model->obtener_valores_parametro_valox(25, $codigo_sap);
            if (empty($existe_codigo)) {
                echo json_encode(array(8));
                return;
            }
            $codigo_sap = $existe_codigo[0]["id"];


            if ($re_manteles == 1) {

                if (empty($manteles) || ctype_space($manteles)) {
                    echo json_encode(array(11));
                    return;
                }
                if ($manteles < 1) {
                    echo json_encode(array(12));
                    return;
                }
            } else {
                $manteles = null;
            }
            if ($re_sillas == 1) {

                if (empty($sillas) || ctype_space($sillas)) {
                    echo json_encode(array(13));
                    return;
                }
                if ($sillas < 1) {
                    echo json_encode(array(14));
                    return;
                }
            } else {
                $sillas = null;
            }

            if ($re_carpas == 1) {

                if (empty($carpas) || ctype_space($carpas)) {
                    echo json_encode(array(15));
                    return;
                }
                if ($carpas < 1) {
                    echo json_encode(array(16));
                    return;
                }
            } else {
                $carpas = null;
            }

            if ($re_vasos == 1) {

                if (empty($vasos) || ctype_space($vasos)) {
                    echo json_encode(array(17));
                    return;
                }
                if ($vasos < 1) {
                    echo json_encode(array(18));
                    return;
                }
            } else {
                $vasos = null;
            }
            if ($re_tenedores == 1) {

                if (empty($tenedores) || ctype_space($tenedores)) {
                    echo json_encode(array(19));
                    return;
                }
                if ($tenedores < 1) {
                    echo json_encode(array(20));
                    return;
                }
            } else {
                $tenedores = null;
            }
            if ($re_cuchillos == 1) {

                if (empty($cuchillos) || ctype_space($cuchillos)) {
                    echo json_encode(array(21));
                    return;
                }
                if ($cuchillos < 1) {
                    echo json_encode(array(22));
                    return;
                }
            } else {
                $cuchillos = null;
            }

            if ($re_mesas == 1) {
                if (empty($id_tipo_mesa) || ctype_space($id_tipo_mesa)) {
                    echo json_encode(array(23));
                    return;
                }
                if (empty($mesas) || ctype_space($mesas)) {
                    echo json_encode(array(24));
                    return;
                }
                if ($mesas < 1) {
                    echo json_encode(array(25));
                    return;
                }
            } else {
                $mesas = null;
                $id_tipo_mesa = null;
            }

            if ($re_cucharas == 1) {
                if (empty($id_tipo_cuchara) || ctype_space($id_tipo_cuchara)) {
                    echo json_encode(array(26));
                    return;
                }
                if (empty($cucharas) || ctype_space($cucharas)) {
                    echo json_encode(array(27));
                    return;
                }
                if ($cucharas < 1) {
                    echo json_encode(array(28));
                    return;
                }
            } else {
                $cucharas = null;
                $id_tipo_cuchara = null;
            }

            if ($re_platos == 1) {
                if (empty($id_tipo_plato) || ctype_space($id_tipo_plato)) {
                    echo json_encode(array(29));
                    return;
                }
                if (empty($platos) || ctype_space($platos)) {
                    echo json_encode(array(30));
                    return;
                }
                if ($platos < 1) {
                    echo json_encode(array(31));
                    return;
                }
            } else {
                $platos = null;
                $id_tipo_plato = null;
            }

            if ($re_flores == 1) {
                if (empty($valor_flores) || ctype_space($valor_flores)) {
                    echo json_encode(array(33));
                    return;
                }
            } else {
                $valor_flores = null;
            }
            if ($re_refri == 1) {
                if (empty($tipo_refrigerios)) {
                    echo json_encode(array(34));
                    return;
                } else if (empty($cantidad_refrigerios)) {
                    echo json_encode(array(35));
                    return;
                } else if (empty($tipo_entrega_refri)) {
                    echo json_encode(array(36));
                    return;
                }
            } else {
                $tipo_refrigerios = null;
                $cantidad_refrigerios = null;
                $tipo_entrega_refri = null;
            }

            if ($re_cafe == 1) {
                if (empty($tipo_entrega_cafe)) {
                    echo json_encode(array(37));
                    return;
                }
            } else {
                $tipo_entrega_cafe = null;
            }

            if ($con_almuerzo == 0) {
                $con_almuerzo = null;
            }
            if ($con_video_beam == 0) {
                $con_video_beam = null;
            }
            if ($con_portatil == 0) {
                $con_portatil = null;
            }
            if ($con_sonido == 0) {
                $con_sonido = null;
            }
            if ($coctel == 0) {
                $coctel = null;
            }

            $cargo = $this->cargar_archivo("archivologistica", $this->ruta_logistica, 'logis');
            if ($cargo[0] == -1) {
                if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                    echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                    return;
                }
            }else{
            $nombre_add = $cargo[1];
            }



            if ($sw_fu) {
                $id_sol = $this->Guardar_solicitud_manual($solicitudADD);
                if ($id_sol == -1) {
                    echo json_encode(array(-112));
                    return;
                }
            }
            $result_ti = $this->solicitudes_adm_model->Guardar_Solicicitudes_tipo4($id_sol, $codigo_sap, $manteles, $sillas, $carpas, $vasos, $tenedores, $id_tipo_mesa, $mesas, $cuchillos, $id_tipo_plato, $platos, $id_tipo_cuchara, $cucharas, $lugar_entrega, $id_responsable, $fecha_entrega, $fecha_retiro, $observaciones, $id_usuario_registra, $num_personas, $valor_flores, $tipo_refrigerios, $cantidad_refrigerios, $tipo_entrega_refri, $tipo_entrega_cafe, $con_almuerzo, $con_video_beam, $categoria, $con_portatil, $con_sonido,$coctel,$nombre_add);
            echo json_encode(array($result_ti,$id_sol));
            return;
            
        }
    }

    public function Modificar_Solicicitudes_tipo4()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
                return;
            }
            $id = $this->input->post('id');

            if (empty($id) || ctype_space($id)) {
                echo json_encode(1);
                return;
            } else {
                $categoria = $this->input->post('tipo_logistica');
                $observaciones = $this->input->post('observaciones');
                $num_personas = $this->input->post('num_personas');
                $codigo_sap = $this->input->post('codigosap');
                $id_responsable = $this->input->post('responsable');
                $lugar_entrega = $this->input->post('lugar_entrega');
                $fecha_entrega = $this->input->post('fecha_entrega');
                //$fecha_retiro = $this->input->post('fecha_retiro');

                //$fecha_entrega = null;
                $fecha_retiro = null;
                $valor_flores = $this->input->post('num_flores');

                $re_manteles = $this->input->post('re_manteles');
                $re_sillas = $this->input->post('re_sillas');
                $re_carpas = $this->input->post('re_carpas');
                $re_vasos = $this->input->post('re_vasos');
                $re_tenedores = $this->input->post('re_tenedores');
                $re_cuchillos = $this->input->post('re_cuchillos');
                $re_mesas = $this->input->post('re_mesas');
                $re_cucharas = $this->input->post('re_cucharas');
                $re_platos = $this->input->post('re_platos');

                $re_flores = $this->input->post('re_flores');
                $re_refri = $this->input->post('re_refri');
                $re_cafe = $this->input->post('re_agua');

                $manteles = $this->input->post('num_manteles');
                $sillas = $this->input->post('num_sillas');
                $carpas = $this->input->post('num_carpas');
                $vasos = $this->input->post('num_vasos');
                $tenedores = $this->input->post('num_tenedores');
                $cuchillos = $this->input->post('num_cuchillos');
                $id_tipo_mesa = $this->input->post('tipo_mesas');
                $mesas = $this->input->post('num_mesas');
                $con_portatil = $this->input->post('re_port');
                $con_sonido = $this->input->post('re_soni');
                $id_tipo_plato = $this->input->post('tipo_platos');
                $platos = $this->input->post('num_platos');
                $id_tipo_cuchara = $this->input->post('tipo_cucharas');
                $cucharas = $this->input->post('num_cucharas');


                $tipo_refrigerios = $this->input->post('tipo_refrigerios');
                $cantidad_refrigerios = $this->input->post('canxperso');
                $tipo_entrega_refri = $this->input->post('tipo_refrigerios_entrega');
                $tipo_entrega_cafe = $this->input->post('tipo_agua_cafe_entrega');
                $con_almuerzo = $this->input->post('re_almuerzo');
                $con_video_beam = $this->input->post('re_vb');
                $coctel = $this->input->post('re_coctel');
                $nombre_add = null;

                if (empty($codigo_sap) || ctype_space($codigo_sap)) {
                    echo json_encode(3);
                    return;
                } else if (empty($id_responsable) || ctype_space($id_responsable)) {
                    echo json_encode(4);
                    return;
                } else if (empty($lugar_entrega) || ctype_space($lugar_entrega)) {
                    echo json_encode(5);
                    return;
                } else if (empty($num_personas) || ctype_space($num_personas)) {
                    echo json_encode(32);
                    return;
                } else if (empty($categoria) || ctype_space($categoria)) {
                    echo json_encode(38);
                    return;
                }else if (empty($fecha_entrega) || ctype_space($fecha_entrega)) {
                  echo json_encode(6);
                  return;
                  } 
                  
                  $fecha_actual = date("Y-m-d H:i");
                  $fecha_inicial_solicitado = date_create($fecha_entrega);
                  //$fecha_salida_solicitado = date_create($fecha_retiro);
                  $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');
                  if ($forma <= $fecha_actual) {
                  echo json_encode(9);
                  return;
                  }
                  
                  
                  /*else if (empty($fecha_retiro) || ctype_space($fecha_retiro)) {
                  echo json_encode(7);
                  return;
                  }


                  if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                  echo json_encode(10);
                  return;
                  }
                 */

                $existe_codigo = $this->genericas_model->obtener_valores_parametro_valox(25, $codigo_sap);
                if (empty($existe_codigo)) {
                    echo json_encode(8);
                    return;
                }
                $codigo_sap = $existe_codigo[0]["id"];



                if ($re_manteles == 1) {

                    if (empty($manteles) || ctype_space($manteles)) {
                        echo json_encode(11);
                        return;
                    }
                    if ($manteles < 1) {
                        echo json_encode(12);
                        return;
                    }
                } else {
                    $manteles = null;
                }
                if ($re_sillas == 1) {

                    if (empty($sillas) || ctype_space($sillas)) {
                        echo json_encode(13);
                        return;
                    }
                    if ($sillas < 1) {
                        echo json_encode(14);
                        return;
                    }
                } else {
                    $sillas = null;
                }

                if ($re_carpas == 1) {

                    if (empty($carpas) || ctype_space($carpas)) {
                        echo json_encode(15);
                        return;
                    }
                    if ($carpas < 1) {
                        echo json_encode(16);
                        return;
                    }
                } else {
                    $carpas = null;
                }

                if ($re_vasos == 1) {

                    if (empty($vasos) || ctype_space($vasos)) {
                        echo json_encode(17);
                        return;
                    }
                    if ($vasos < 1) {
                        echo json_encode(18);
                        return;
                    }
                } else {
                    $vasos = null;
                }
                if ($re_tenedores == 1) {

                    if (empty($tenedores) || ctype_space($tenedores)) {
                        echo json_encode(19);
                        return;
                    }
                    if ($tenedores < 1) {
                        echo json_encode(20);
                        return;
                    }
                } else {
                    $tenedores = null;
                }
                if ($re_cuchillos == 1) {

                    if (empty($cuchillos) || ctype_space($cuchillos)) {
                        echo json_encode(21);
                        return;
                    }
                    if ($cuchillos < 1) {
                        echo json_encode(22);
                        return;
                    }
                } else {
                    $cuchillos = null;
                }

                if ($re_mesas == 1) {
                    if (empty($id_tipo_mesa) || ctype_space($id_tipo_mesa)) {
                        echo json_encode(23);
                        return;
                    }
                    if (empty($mesas) || ctype_space($mesas)) {
                        echo json_encode(24);
                        return;
                    }
                    if ($mesas < 1) {
                        echo json_encode(25);
                        return;
                    }
                } else {
                    $mesas = null;
                    $id_tipo_mesa = null;
                }

                if ($re_cucharas == 1) {
                    if (empty($id_tipo_cuchara) || ctype_space($id_tipo_cuchara)) {
                        echo json_encode(26);
                        return;
                    }
                    if (empty($cucharas) || ctype_space($cucharas)) {
                        echo json_encode(27);
                        return;
                    }
                    if ($cucharas < 1) {
                        echo json_encode(28);
                        return;
                    }
                } else {
                    $cucharas = null;
                    $id_tipo_cuchara = null;
                }

                if ($re_platos == 1) {
                    if (empty($id_tipo_plato) || ctype_space($id_tipo_plato)) {
                        echo json_encode(29);
                        return;
                    }
                    if (empty($platos) || ctype_space($platos)) {
                        echo json_encode(30);
                        return;
                    }
                    if ($platos < 1) {
                        echo json_encode(31);
                        return;
                    }
                } else {
                    $platos = null;
                    $id_tipo_plato = null;
                }

                if ($re_flores == 1) {
                    if (empty($valor_flores) || ctype_space($valor_flores)) {
                        echo json_encode(33);
                        return;
                    }
                } else {
                    $valor_flores = null;
                }
                if ($re_refri == 1) {
                    if (empty($tipo_refrigerios)) {
                        echo json_encode(34);
                        return;
                    } else if (empty($cantidad_refrigerios)) {
                        echo json_encode(35);
                        return;
                    } else if (empty($tipo_entrega_refri)) {
                        echo json_encode(36);
                        return;
                    }
                } else {
                    $tipo_refrigerios = null;
                    $cantidad_refrigerios = null;
                    $tipo_entrega_refri = null;
                }

                if ($re_cafe == 1) {
                    if (empty($tipo_entrega_cafe)) {
                        echo json_encode(37);
                        return;
                    }
                } else {
                    $tipo_entrega_cafe = null;
                }


                if ($con_almuerzo == 0) {
                    $con_almuerzo = null;
                }
                if ($con_video_beam == 0) {
                    $con_video_beam = null;
                }
                if ($con_portatil == 0) {
                    $con_portatil = null;
                }
                if ($con_sonido == 0) {
                    $con_sonido = null;
                }
                if ($coctel == 0) {
                    $coctel = null;
                }

                $cargo = $this->cargar_archivo("archivologistica", $this->ruta_logistica, 'logis');
                if ($cargo[0] == -1) {
                    if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                        echo json_encode("Error al cargar el archivo(" . $cargo[1] . ")");
                        return;
                    }
                }else{
                $nombre_add = $cargo[1];
                }
    

                $result_ti = $this->solicitudes_adm_model->Modificar_Solicicitudes_tipo4($id, $codigo_sap, $manteles, $sillas, $carpas, $vasos, $tenedores, $id_tipo_mesa, $mesas, $cuchillos, $id_tipo_plato, $platos, $id_tipo_cuchara, $cucharas, $lugar_entrega, $id_responsable, $fecha_entrega, $fecha_retiro, $observaciones, $num_personas, $valor_flores, $tipo_refrigerios, $cantidad_refrigerios, $tipo_entrega_refri, $tipo_entrega_cafe, $con_almuerzo, $con_video_beam, $categoria, $con_portatil, $con_sonido,$coctel,$nombre_add );

                echo json_encode($result_ti);
                return;
            }
        }
    }

    public function modificar_transporte()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
                return;
            }

            $id = $this->input->post('id');
            $origen = $this->input->post('origen');
            $destino = $this->input->post('destino');
            $numpersonas = $this->input->post('num_personas');
            $observaciones = $this->input->post('observaciones');
            $codsap = $this->input->post('codsapbuses');
            
            /*$hora_salida = $this->input->post('hora_salida');
            $hora_retorno = $this->input->post('hora_retorno');
             */

            $hora_salida = null;
            $hora_retorno = null;
            if (empty($origen) || ctype_space($origen)) {
                echo json_encode(5);
                return;
            } else if (empty($destino) || ctype_space($destino)) {
                echo json_encode(6);
                return;
            } else if (empty($id) || ctype_space($id)) {
                echo json_encode(10);
                return;
            } 
            /*
            else if (empty($hora_salida) || ctype_space($hora_salida)) {
                echo json_encode(12);
                return;
            } else if (empty($hora_retorno) || ctype_space($hora_retorno)) {
                echo json_encode(13);
                return;
            }*/
            else if (empty($numpersonas) || ctype_space($numpersonas)) {
                echo json_encode(14);
                return;
            } else if (empty($codsap) || ctype_space($codsap)) {
                echo json_encode(15);
                return;
            }


            /*
            $fecha_actual = date("Y-m-d H:i");
            $fecha_inicial_solicitado = date_create($hora_salida);
            $fecha_salida_solicitado = date_create($hora_retorno);
            $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');


            if ($forma <= $fecha_actual) {
                echo json_encode(-15);
                return;
            }

            if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                echo json_encode(-16);
                return;
            }
             */

            $existe_codigo = $this->genericas_model->obtener_valores_parametro_valox(25, $codsap);
            if (empty($existe_codigo)) {
                echo json_encode(-17);
                return;
            }
            $codsap = $existe_codigo[0]["id"];

            $result_tra = $this->solicitudes_adm_model->modificar_transporte($id, $origen, $destino, $hora_salida, $hora_retorno, $numpersonas, $codsap, $observaciones);

            echo json_encode($result_tra);
            return;
        }
    }

    public function modificar_tiquetes_viaticos()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
                return;
            }

            $id = $this->input->post('id');
            $id_solicitud = $this->input->post('id_solicitud');
            $origen = $this->input->post('origen');
            $destino = $this->input->post('destino');
            $re_tiquete = $this->input->post('re_tiquete');
            $re_viaticos = $this->input->post('re_viaticos');
            $re_seguro = $this->input->post('re_seguro');
            $personas = $this->input->post('personas');
            $codsap = $this->input->post('codsap');
            $req_hotel = $this->input->post('re_hotel');
            $observaciones = $this->input->post('observaciones');
            $fecha_salida_ti = $this->input->post('fecha_salida_tiqu');
            $fecha_retorno_ti = $this->input->post('fecha_retorno_tiqu');
            $fecha_ingreso_hotel = $this->input->post('fecha_ingreso_hotel');
            $fecha_salida_hotel = $this->input->post('fecha_salida_hotel');
            $nombre = "Doc";
            $ruta = $this->ruta_personas;
            $id_datos = $this->solicitudes_adm_model->Listar_detalle_tiquetes_id_persona($id);



            if (empty($origen) || ctype_space($origen)) {
                echo json_encode(5);
                return;
            } else if (empty($destino) || ctype_space($destino)) {
                echo json_encode(6);
                return;
            } else if (empty($id) || ctype_space($id) || empty($id_datos)) {
                echo json_encode(10);
                return;
            } else if (empty($codsap) || ctype_space($codsap)) {
                echo json_encode(11);
                return;
            }


            $tipo = $this->solicitudes_adm_model->Buscar_Solicitud_id($id_solicitud);
            if (empty($tipo)) {
                echo json_encode(10);
                return;
            }
            $tipo_evento = $tipo[0]["tipo_evento_gen"];


            if ($re_tiquete == 1) {

                if (empty($fecha_salida_ti) || ctype_space($fecha_salida_ti)) {
                    echo json_encode(7);
                    return;
                } else if (empty($fecha_retorno_ti) || ctype_space($fecha_retorno_ti)) {
                    echo json_encode(8);
                    return;
                }

                $fecha_actual = date("Y-m-d H:i");
                $fecha_inicial_solicitado = date_create($fecha_salida_ti);
                $fecha_salida_solicitado = date_create($fecha_retorno_ti);
                $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');


                if ($forma <= $fecha_actual) {
                    echo json_encode(-13);
                    return;
                }

                if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                    echo json_encode(-14);
                    return;
                }
            } else {
                $fecha_salida_ti = null;
                $fecha_retorno_ti = null;
            }

            if ($req_hotel == 1) {

                if (empty($fecha_ingreso_hotel) || ctype_space($fecha_ingreso_hotel)) {
                    echo json_encode(16);
                    return;
                } else if (empty($fecha_salida_hotel) || ctype_space($fecha_salida_hotel)) {
                    echo json_encode(17);
                    return;
                }

                $fecha_actual = date("Y-m-d H:i");
                $fecha_inicial_solicitado = date_create($fecha_ingreso_hotel);
                $fecha_salida_solicitado = date_create($fecha_salida_hotel);
                $forma = date_format($fecha_inicial_solicitado, 'Y-m-d H:i');


                if ($forma <= $fecha_actual) {
                    echo json_encode(18);
                    return;
                }

                if ($fecha_salida_solicitado <= $fecha_inicial_solicitado) {
                    echo json_encode(19);
                    return;
                }
            } else {
                $fecha_ingreso_hotel = null;
                $fecha_salida_hotel = null;
            }

            if ($codsap != "-----") {
                $existe_codigo = $this->genericas_model->obtener_valores_parametro_valox(25, $codsap);
                if (empty($existe_codigo)) {
                    echo json_encode(-17);
                    return;
                }
                $codsap = $existe_codigo[0]["id"];
            } else {
                $codsap = null;
            }

            $archivo_visa = $id_datos->{'archivo_visa'};
            $archivo_agenda = $id_datos->{'archivo_agenda'};
            $archivo_pasaporte = $id_datos->{'archivo_adjunto'};

            if ($tipo_evento == "Even_Int" && $re_tiquete == 1) {


                $cargo = $this->cargar_archivo("archivopersona", $ruta, $nombre);
                if ($cargo[0] == -1) {
                    if ($cargo[1] != "<p>You did not select a file to upload.</p>") {
                        echo json_encode("Error al cargar el pasaporte(" . $cargo[1] . ")");
                        return;
                    }

                    if ($cargo[1] == "<p>You did not select a file to upload.</p>") {
                        if (is_null($archivo_pasaporte) || !file_exists($ruta . $archivo_pasaporte)) {
                            echo json_encode(-18);
                            return;
                        }
                    }
                } else {
                    $archivo_pasaporte = $cargo[1];
                }

                $cargo_visa = $this->cargar_archivo("archivovisa", $ruta, $nombre);

                if ($cargo_visa[0] == -1) {
                    $es = strpos($cargo_visa[1], "You did not select a file");
                    if ($es === false) {
                        echo json_encode("Error al cargar la VISA(" . $cargo_visa[1] . ")");
                        return;
                    }
                } else {
                    $archivo_visa = $cargo_visa[1];
                }
            }

            $cargo_agenda = $this->cargar_archivo("archivootro", $ruta, $nombre);

            if ($cargo_agenda[0] == -1) {
                $es = strpos($cargo_agenda[1], "You did not select a file");
                if ($es === false) {
                    echo json_encode("Error al cargar la agenda del evento(" . $cargo_agenda[1] . ")");
                    return;
                }
            } else {
                $archivo_agenda = $cargo_agenda[1];
            }
            if ($re_tiquete == 0) {
                $archivo_pasaporte = null;
                $archivo_visa = null;
            }
            $result_ti = $this->solicitudes_adm_model->modificar_tiquetes_viaticos($id, $origen, $destino, $re_tiquete, $fecha_salida_ti, $fecha_retorno_ti, $re_viaticos, $codsap, $re_seguro, $observaciones, $req_hotel, $fecha_ingreso_hotel, $fecha_salida_hotel, $archivo_pasaporte, $archivo_visa, $archivo_agenda);
            echo json_encode($result_ti);
            return;
        }
    }

    function Buscar_Solicitud_id()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("id");
        $datos = $this->solicitudes_adm_model->Buscar_Solicitud_id($id);
        echo json_encode($datos);
    }

    function listar_info_solicitud_tipo3()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("id");
        $datos = $this->solicitudes_adm_model->listar_info_solicitud_tipo3($id);
        echo json_encode($datos);
    }

    function listar_info_solicitud_tipo4()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("id");
        $datos = $this->solicitudes_adm_model->listar_info_solicitud_tipo4($id);
        echo json_encode($datos);
    }

    function Listar_detalle_tiquetes_id_persona()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("id");
        $datos = $this->solicitudes_adm_model->Listar_detalle_tiquetes_id_persona($id);
        echo json_encode($datos);
    }

    function Buscar_transporte_id()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("id");
        $datos = $this->solicitudes_adm_model->Buscar_transporte_id($id);
        echo json_encode($datos);
    }

    function vaidar_estado_Actual_solicitud()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("id");
        $datos = $this->solicitudes_adm_model->Buscar_Solicitud_id($id);
        if (empty($datos)) {
            echo json_encode(-1);
            return;
        }
        echo json_encode($datos[0]["estado_gen"]);
        return;
    }

    function Retirar_persona_tiquete()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        if ($this->Super_elimina == 0) {
            echo json_encode(-1302);
            return;
        }
        $id = $this->input->post("id");
        $fecha = date("Y-m-d H:i:s");
        $usuario = $_SESSION["persona"];
        $datos = $this->solicitudes_adm_model->Retirar_persona_tiquete($id, $usuario, $fecha);
        echo json_encode($datos);
        return;
    }
    function Retirar_solicitud_bus()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        if ($this->Super_elimina == 0) {
            echo json_encode(-1302);
            return;
        }
        $id = $this->input->post("id");
        $fecha = date("Y-m-d H:i:s");
        $usuario = $_SESSION["persona"];
        $datos = $this->solicitudes_adm_model->Retirar_solicitud_bus($id, $usuario, $fecha);
        echo json_encode($datos);
        return;
    }
    function Retirar_solicitud_pedido()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        if ($this->Super_elimina == 0) {
            echo json_encode(-1302);
            return;
        }
        $id = $this->input->post("id");
        $fecha = date("Y-m-d H:i:s");
        $usuario = $_SESSION["persona"];
        $datos = $this->solicitudes_adm_model->Retirar_solicitud_pedido($id, $usuario, $fecha);
        echo json_encode($datos);
        return;
    }
    function Retirar_persona_responsable()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        if ($this->Super_elimina == 0) {
            echo json_encode(-1302);
            return;
        }
        $id = $this->input->post("id");
        $tipo = $this->input->post("tipo");
        $fecha = date("Y-m-d H:i:s");
        $usuario = $_SESSION["persona"];
        $datos = $this->solicitudes_adm_model->Retirar_persona_responsable($id, $usuario, $fecha,$tipo);
        echo json_encode($datos);
        return;
    }

    function Asignar_nuevo_responsable()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        if ($this->Super_agrega == 0) {
            echo json_encode(-1302);
            return;
        }
        $id_tras = $this->input->post("id");
        $personas = $this->input->post('personas');
        $tipo = $this->input->post('tipo');

        $id_usuario_registra = $_SESSION["persona"];

        if (empty($personas) || ctype_space($personas)) {
            echo json_encode(1);
            return;
        } else if (empty($id_tras) || ctype_space($id_tras)) {
            echo json_encode(2);
            return;
        }
        $result_res = 2;
        if ($tipo==3) {
           $data = array(
                "id_sol_transporte" => $id_tras,
                "id_resposnable" => $personas,
                "usuario_registra" => $id_usuario_registra,
        );
        $result_res = $this->solicitudes_adm_model->guardar_responsable($data,"responsables_buses");  
       // $result_res = $this->solicitudes_adm_model->guardar_responsable_buses($id_tras, $personas, $id_usuario_registra);
        }else {
            $data = array(
                "id_general" => $id_tras,
                "id_responsable" => $personas,
                "usuario_registra" => $id_usuario_registra,
            );
            $result_res = $this->solicitudes_adm_model->guardar_responsable($data,"responsables_general");  
        }
        echo json_encode($result_res);
        return;
    }

    function cargar_archivo($mi_archivo, $ruta, $nombre)
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

            return array(-1, $data['uploadError']);
        }

        $data['uploadSuccess'] = $this->upload->data();

        return array(1, $data['uploadSuccess']["file_name"]);
    }
    public function consulta_solicitud_comunicaciones_id()
    {                
        $id = $this->input->post("id");
        $resp = $this->Super_estado ? $this->comunicaciones_model->consulta_solicitud_id($id) : array();
        echo json_encode($resp);
    }
    public function listar_servicios_solicitud()
    {                
        $id = $this->input->post("id");
        $resp = $this->Super_estado ? $this->comunicaciones_model->listar_servicios_solicitud($id,null,'Adm') : array();
        echo json_encode($resp);
    }
    public function listar_archivos_adjuntos()
    {                
        $id = $this->input->post("id");
        $resp = $this->Super_estado ? $this->comunicaciones_model->listar_archivos_adjuntos($id_solicitud) : array();
        echo json_encode($resp);
    }
    public function cargar_select(){
		$tipos = array();
		if ($this->Super_estado) {
			$clasificacion = $this->input->post('clasificacion');
			$tipos = $this->solicitudes_adm_model->cargar_select($clasificacion);
		}
		echo json_encode($tipos);
	}
    public function documentos_adm()
    {
        if (!$this->Super_estado) {
            $resp = ["mensaje" => "", "tipo" => "sin_session", "titulo" => ""];
        } else {
            $clasificacion = $this->input->post('clasificacion');
            $resp = $this->solicitudes_adm_model->documentos_adm($clasificacion);
        }
        echo json_encode($resp);
    }

}

?>
