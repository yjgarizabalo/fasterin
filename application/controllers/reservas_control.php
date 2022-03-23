<?php

class reservas_control extends CI_Controller
{

	var $Super_estado = false;
	var $Super_elimina = 0;
	var $Super_modifica = 0;
	var $Super_agrega = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('genericas_model');
        $this->load->model('inventario_model');
        $this->load->model('reservas_model');
        date_default_timezone_set("America/Bogota");
        session_start();
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
        }
    }

    public function index($id = 0)
    {
        $pages = "inicio";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $data["id"] = $id;
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], "tecnologia/reservas");
            if (!empty($datos_actividad)) {
                $pages = "reservas";
                $data['js'] = "Reservas";
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

    function Cargar_inventario_audiovisuales()
    {
        $parametros = array();
        if ($this->Super_estado == false) {
            echo json_encode($parametros);
            return;
        }
        $datos = $this->inventario_model->Listar_audiovisual_general();

        $i = 1;

        foreach ($datos as $row) {
            // $row["indice"] = $i;
            $parametros["data"][] = $row;
            //   $i++;
        }

        echo json_encode($parametros);
    }



    function obtener_datos_reserva()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $this->input->post("id");
        $conasig = $this->input->post("conasig");
        if ($conasig == "no") {
            $datos = $this->reservas_model->Listar_reservas_detalle_general($id);
        } else {
            $datos = $this->reservas_model->Listar_reservas_detalle($id);
        }

        echo json_encode($datos);
    }

    function buscar_Recurso_en_reserva_reporte($id)
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $datos_res = $this->reservas_model->Cargar_recurso_en_reserva_agrupados();
        for ($index2 = 0; $index2 < count($datos_res); $index2++) {
            $datos_index2 = $datos_res[$index2];
            if ($datos_index2["id_inventario"] == $id) {
                return true;
            }
        }
        return false;
    }

    function obtener_recursos_audiovisuales_combo()
    {
        $recursos = array();
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $fecha_entrega = $this->input->post('fecha_entrega');
        $fecha_salida = $this->input->post('fecha_salida');
        $fecha_salida = $this->Transformar_Fecha($fecha_entrega, $fecha_salida);
        $datos = $this->reservas_model->Cargar_Recursos_Audiovisuales();
        foreach ($datos as $row) {
            $resultado_cruze = $this->BuscarCruzes($fecha_entrega, $fecha_salida, $row["id"]);
            $disponible = $row["disponibles"] - $resultado_cruze;
            if ($disponible < 0) {
                $disponible = 0;
            }
            if ($resultado_cruze < 0) {
                $disponible = 0;
            }
            $row["disponibles"] = $disponible;


            $recursos["data"][] = $row;
            //   $i++;
        }
        echo json_encode($recursos);
    }
    function buscar_reservas_activas_usuario_fecha()
    {
        $recursos = array();
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $fecha_entrega = $this->input->post('fecha_entrega');
        $fecha_salida = $this->input->post('fecha_salida');
        $fecha_salida = $this->Transformar_Fecha($fecha_entrega, $fecha_salida);

        if ($_SESSION["perfil"] != "Per_Admin" && $_SESSION["perfil"] != "Per_Aud" && $_SESSION["perfil"] != "Admin_Aud") {
            $datos = $this->reservas_model->buscar_reservas_activas_usuario_fecha($fecha_entrega,$fecha_salida,$_SESSION["persona"]);
            if (empty($datos)) {
                echo json_encode(0);
                return;
            }
            echo json_encode(-1);
            return;
        }
        echo json_encode(0);
        return;

    }

    function obtener_recursos_audiovisuales_disponibles()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $recursos = array();
        $tipo = $this->input->post("tipo");
        $listado_actual = $this->inventario_model->Listar_audiovisual_general_tipo($tipo);
        $listado_prestamo = $this->reservas_model->Cargar_Recursos_en_prestamos_Audiovisuales($tipo);


        foreach ($listado_actual as $row) {
            $sw = true;
            foreach ($listado_prestamo as $row_p) {

                if ($row["id"] == $row_p["id_recurso"]) {
                    $sw = false;
                }
            }
            if ($sw) {
                $recursos["data"][] = $row;
            }
        }

        echo json_encode($recursos);
    }

    function Guardar()
    {
        if ($this->Super_estado == false) {
            echo json_encode(array("sin_session"));
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(array(-1302));
            } else {
                $reservaGuardada = array();
                $recursos_reserva = $this->input->post('recursos');
                $tipo_reserva = $this->input->post('tipo_reserva');
                $tipo_reserva = $tipo_reserva ? $tipo_reserva : "Res_Nor";
                $clase = $this->input->post('tipo_estudio');
                $fecha_entrega = $this->input->post('fecha_entrega');
                $fecha_salida = $this->input->post('fecha_salida');
                $fecha_salida = $this->Transformar_Fecha($fecha_entrega, $fecha_salida);

                $tipo_prestamo = $this->input->post('tipo_prestamo');
                $tipo_entrega = $this->input->post('tipo_entrega');
                $lugar = $this->input->post('lugar');
                $asignatura = $this->input->post('asignatura');
                $observaciones = $this->input->post('descripcion');
                $usuario = $_SESSION['persona'];
                $persona_soli = $usuario;
                if (empty($recursos_reserva)) {
                    echo json_encode(array(-1));
                    return;
                }


                if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Aud" || $_SESSION["perfil"] == "Admin_Aud") {
                    $persona_soli = $this->input->post('persona_soli');
                    if (empty($persona_soli)) {
                        echo json_encode(array(-8));
                        return;;
                    }
                }

                if (empty($usuario) || empty($fecha_entrega) || empty($fecha_salida) || empty($lugar) || ctype_space($fecha_entrega) || ctype_space($fecha_salida) || ctype_space($lugar)) {
                    echo json_encode(array(-5));
                } else {
                    $resultado_fecha = $this->Validar_fechas($fecha_entrega, $fecha_salida);
                    if ($resultado_fecha == 1) {
                        // Se realiza la validación de la fecha y que no sea mayor a la actual
                        if ($_SESSION["perfil"] != "Per_Admin" && $_SESSION["perfil"] != "Per_Aud" && $_SESSION["perfil"] != "Admin_Aud") {
                            $fecha_actual = date("d-m-Y");
                            $fecha_lim = $this->genericas_model->obtener_valores_parametro_aux("limFec", 20);
                            $calc_fecha_fin = date("d-m-Y",strtotime($fecha_actual."+ ".$fecha_lim[0]['valor']." days"));
                            if($calc_fecha_fin < date('d-m-Y', strtotime($fecha_entrega))){
                                echo json_encode(array(-9, $calc_fecha_fin));
                                return false;
                            }
                        }
                        $id_reserva = $this->reservas_model->guardar($asignatura, $usuario, $fecha_salida, $tipo_prestamo, $lugar, $tipo_entrega, $observaciones, $fecha_entrega, $clase, "Res_Soli", $persona_soli,$tipo_reserva);

                        if ($id_reserva > 0) {
                            $detalle = 0;
                            $recursos_reserva = $tipo_reserva == "Res_Nor" ? explode(",", $recursos_reserva) : $recursos_reserva;
                            $no_agregados = array();
                            for ($index = 0; $index < count($recursos_reserva); $index++) {

                                $resultado_cruze = $this->BuscarCruzes($fecha_entrega, $fecha_salida, $recursos_reserva[$index]);

                                if ($resultado_cruze >= 0) {
                                    $listado_tipo = count($this->inventario_model->Listar_audiovisual_general_tipo($recursos_reserva[$index]));
                                    $disponible = $listado_tipo - $resultado_cruze;
                                    if ($disponible > 0) {
                                        $detalle = $this->reservas_model->guardar_mas_recursos($recursos_reserva[$index], $usuario, $id_reserva);
                                    } else {
                                        array_push($no_agregados, $recursos_reserva[$index]);
                                    }
                                } else {
                                    array_push($no_agregados, $recursos_reserva[$index]);
                                }


                            }
                            if (!empty($no_agregados)) {
                                echo json_encode(array(-6, $no_agregados, $id_reserva));
                                return;
                            }
                            echo json_encode(array(0, $id_reserva));
                            return;
                        }
                        echo json_encode(array(-2));
                        return;
                    } else {
                        echo json_encode($resultado_fecha);
                        return;
                    }
                }
            }
        }
    }

    function Modificar_Reserva()
    {
        if ($this->Super_estado == false) {
            echo json_encode(array("sin_session"));
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(array(-1302));
            } else {
                $reservaGuardada = array();
                $id_reserva = $this->input->post('id');
                $clase = $this->input->post('tipo_estudio');
                $tipo_prestamo = $this->input->post('tipo_prestamo');
                $tipo_entrega = $this->input->post('tipo_entrega');
                $lugar = $this->input->post('lugar');
                $asignatura = $this->input->post('asignatura');
                $observaciones = $this->input->post('descripcion');
                $usuario = $_SESSION['persona'];
                $persona_soli = $usuario;
                $fecha_salida = "";
                $fecha_entrega = "";
                $op = 1;

                if (empty($id_reserva)) {
                    echo json_encode(array(1));
                    return;
                }

                if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Aud" || $_SESSION["perfil"] == "Admin_Aud") {
                    $persona_soli = $this->input->post('persona_soli');
                    if (empty($persona_soli)) {
                        echo json_encode(array(3));
                        return;;
                    }
                }
                if (empty($usuario) || empty($lugar) || ctype_space($lugar)) {
                    echo json_encode(array(2));
                } else {
                    $resu = $this->reservas_model->Modificar_Reserva($asignatura, $fecha_salida, $tipo_prestamo, $lugar, $tipo_entrega, $observaciones, $fecha_entrega, $clase, $persona_soli, $id_reserva, $op);
                    echo json_encode(array($resu));
                    return;
                }
            }

        }
        echo json_encode(array(1));
        return;
    }

    function Guardar_mas_recursos()
    {
        if ($this->Super_estado == false) {
            echo json_encode(array("sin_session"));
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(array(-1302));
            } else {

                $id_reserva = $this->input->post('id');
                $recursos_reserva = $this->input->post('recursos');
                $fecha_entrega = $this->input->post('fecha_entrega');
                $fecha_salida = $this->input->post('fecha_salida');
                $fecha_salida = $this->Transformar_Fecha($fecha_entrega, $fecha_salida);
                $usuario = $_SESSION['persona'];

                if (empty($recursos_reserva)) {
                    echo json_encode(array(-1));
                    return;
                }

                if (empty($usuario) || empty($fecha_entrega) || empty($fecha_salida) || ctype_space($fecha_entrega) || ctype_space($fecha_salida)) {
                    echo json_encode(array(-5));
                } else {
                    $resultado_fecha = $this->Validar_fechas($fecha_entrega, $fecha_salida,2);
                    if ($resultado_fecha == 1) {
                        $detalle = 0;
                        $no_agregados = array();
                        for ($index = 0; $index < count($recursos_reserva); $index++) {

                            $resultado_cruze = $this->BuscarCruzes($fecha_entrega, $fecha_salida, $recursos_reserva[$index]);

                            if ($resultado_cruze >= 0) {
                                $listado_tipo = count($this->inventario_model->Listar_audiovisual_general_tipo($recursos_reserva[$index]));
                                $disponible = $listado_tipo - $resultado_cruze;
                                if ($disponible > 0) {
                                    $detalle = $this->reservas_model->guardar_mas_recursos($recursos_reserva[$index], $usuario, $id_reserva);
                                } else {
                                    array_push($no_agregados, $recursos_reserva[$index]);
                                }
                            } else {
                                array_push($no_agregados, $recursos_reserva[$index]);
                            }


                        }
                        if (!empty($no_agregados)) {
                            echo json_encode(array(-6, $no_agregados));
                            return;
                        }
                        echo json_encode(array($detalle));
                        return;

                    } else {
                        echo json_encode($resultado_fecha);
                        return;
                    }
                }
            }
        }
    }
    function Validar_disponibilidad()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            $tipo = $this->input->post('recurso');

            $fecha_entrega = $this->input->post('fecha_entrega');
            $fecha_salida = $this->input->post('fecha_salida');
            $fecha_salida = $this->Transformar_Fecha($fecha_entrega, $fecha_salida);
            $resultado_cruze = $this->BuscarCruzes($fecha_entrega, $fecha_salida, $tipo);
            if ($resultado_cruze >= 0) {
                $listado_tipo = count($this->inventario_model->Listar_audiovisual_general_tipo($tipo));
                $disponible = $listado_tipo - $resultado_cruze;
                if ($disponible > 0) {
                    echo json_encode(1);
                    return;
                }
                echo json_encode(-1);
                return;
            }
        }
        echo json_encode(-1);
        return;
    }

    function Validar_fechas_post()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            $tipo = $this->input->post('tipo');
            $fecha_entrega = $this->input->post('fecha_entrega');
            $fecha_salida = $this->input->post('fecha_salida');
            if (empty($fecha_entrega) || empty($fecha_salida)) {
                echo json_encode(-1);
                return;
            }
            $fecha_salida = $this->Transformar_Fecha($fecha_entrega, $fecha_salida);
            $resultado_cruze = $this->Validar_fechas($fecha_entrega, $fecha_salida,$tipo);
            echo json_encode($resultado_cruze);
            return;

        }
    }

    function Cargar_Reservas()
    {
        $reservas = array();
        if ($this->Super_estado == false) {
            echo json_encode($reservas);
            return;
        }
        $estado_fil = $this->input->post('estado');
        $entrega_fil = $this->input->post('entrega');
        $finicial = $this->input->post('finicial');
        $ffinal = $this->input->post('ffinal');
        $fecha = $this->input->post('fecha');
        $id = $this->input->post('id');

        $datos = $this->reservas_model->Listar_reservas($estado_fil, $fecha, $finicial, $ffinal, $entrega_fil,$id);

        $i = 1;

        foreach ($datos as $row) {
            $row["gestionar"] = '<span  title="Reserva Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
            $row["codigo"] = '<span   class="pointer" ><span >ver</span></span>';
            $estado = $row['estado2'];
            if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Aud" || $_SESSION["perfil"] == "Admin_Aud") {

                if ($estado == "Res_Soli") {
                    $row["codigo"] = '<span  style="background-color: #EABD32;color: white; ;" class="pointer form-control" ><span >ver</span></span>';

                    $row["gestionar"] = '<span style="color: #2E79E5;" title="Entregar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-sign-in btn btn-default" onclick="traer_recursos_por_reserva(' . $row["id"] . ',1)"></span>';
                    $row["gestionar"] = $row["gestionar"] . '<span style="color: #DE4D4D;margin-left: 5px" title="Cancelar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-remove btn btn-default" onclick="confirmacion_cancelar_reserva(' . $row["id"] . ')"></span>';
                } else if ($estado == "Res_Entre") {
                    $row["codigo"] = '<span  style="background-color: #2E79E5;color: white; ;" class="pointer form-control" ><span >ver</span></span>';

                    $row["gestionar"] = '<span style="color: #0EA239;" title="Recibir" data-toggle="popover" data-trigger="hover" class="fa fa-sign-out pointer btn btn-default" onclick="Gestionar_solicitud(' . $row["id"] . ')"></span>';
                } else if ($estado == "Res_Recib") {
                    $row["codigo"] = '<span  style="background-color: #39B23B;color: white; ;" class="pointer form-control" ><span >ver</span></span>';
                    $calificacion = $row['calificacion'];
                    if (is_null($calificacion) && ($row["id_usuario_sol"] == $_SESSION["persona"] || $_SESSION["perfil"] == 'Per_Admin')) {
                        $row["gestionar"] = '<span style="color: #f0ad4e;" title="Calificar" data-toggle="popover" data-trigger="hover" class="glyphicon glyphicon-star pointer btn btn-default" onclick="Puede_Calificar(' . $row["id"] . ')"></span>';
                    } 

                } else if ($estado == "Res_Canc") {

                    $row["codigo"] = '<span   style="background-color: #d9534f;color: white; ;" class="pointer form-control" ><span >ver</span></span>';
                }
            } else {
                if ($estado == "Res_Soli") {
                    $row["codigo"] = '<span  style="background-color: #EABD32;color: white; ;" class="pointer form-control" ><span >ver</span></span>';

                    $row["gestionar"] = '<span style="color: #DE4D4D;" title="Cancelar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-remove btn btn-default" onclick="confirmacion_cancelar_reserva(' . $row["id"] . ')"></span>';
                } else if ($estado == "Res_Recib") {
                    $row["codigo"] = '<span  style="background-color: #39B23B;color: white; ;" class="pointer form-control" ><span >ver</span></span>';

                    $calificacion = $row['calificacion'];
                    if (is_null($calificacion)) {
                        $row["gestionar"] = '<span style="color: #f0ad4e;" title="Calificar" data-toggle="popover" data-trigger="hover" class="glyphicon glyphicon-star pointer btn btn-default" onclick="Puede_Calificar(' . $row["id"] . ')"></span>';
                    } 
                } else if ($estado == "Res_Entre") {
                    $row["gestionar"] = '<span  title="Reserva Abierta" data-toggle="popover" data-trigger="hover" class="fa fa-hourglass-half btn" style="color:#428bca"></span>';
                    $row["codigo"] = '<span  style="background-color: #2E79E5;color: white; ;" class="pointer form-control" ><span >ver</span></span>';

                } else if ($estado == "Res_Canc") {

                    $row["codigo"] = '<span style="background-color: #d9534f;color: white; ;" class="pointer form-control" ><span >ver</span></span>';
                }
            }
            $color = $row["tipo_reserva"] != "Res_Nor" ?  "#6e1f7c" : "#6e1f7";
            $row["codigo"] = "<span><span style='border-left: 5px solid $color;color:white'>|</span>".$row["codigo"]."</span>";
            $reservas["data"][] = $row;
            $i++;
        }

        echo json_encode($reservas);
    }

    function traer_recursos_por_reserva()
    {
        $recursos_reserva = array();
        if ($this->Super_estado == false) {
            echo json_encode($recursos_reserva);
            return;
        }
        $idreserva = $this->input->post("idreserva");
        $datos = $this->reservas_model->traer_recursos_por_reserva($idreserva);

        $i = 1;

        foreach ($datos as $row) {



            if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Aud" || $_SESSION["perfil"] == "Admin_Aud") {
                if ($row["estado_reserva"] == "Res_Soli" || $row["estado_reserva"] == "Res_Entre") {
                    $row["gestionar"] = '<span style="color: #2E79E5;" title="Asignar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-pencil-square-o btn btn-default asignar"></span>';
                    $row["gestionar"] = $row["gestionar"] . ' <span style="color: #DE4D4D;" title="Retirar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-trash-o btn btn-default eliminar"></span>';
                } else {
                    $row["gestionar"] = '<span title="Reserva Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
                }
            } else {
                if ($row["estado_reserva"] == "Res_Soli") {
                    $row["gestionar"] = ' <span style="color: #DE4D4D;" title="Retirar" data-toggle="popover" data-trigger="hover" class="pointer fa fa-trash-o btn btn-default entregado"></span>';
                } else {
                    $row["gestionar"] = '<span title="Reserva Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>';
                }
            }





            $recursos_reserva["data"][] = $row;
        }




        echo json_encode($recursos_reserva);
    }

    public function BuscarCruzes($strStart, $strEnd, $tipo)
    {
        if ($this->Super_estado == false) {
            return -1;
        }

        $reserva = $this->reservas_model->Buscar_Recurso_Reserva($tipo, $strStart, $strEnd);
        return $reserva;

    }
    public function Validar_fechas($strStart, $strEnd, $tipo = 1)
    {
        $fecha_actual = date("Y-m-d H:i");

        $fecha_entrega_solicitado = date_create($strStart);
        $fecha_salida_solicitado = date_create($strEnd);
        $forma = date_format($fecha_entrega_solicitado, 'Y-m-d H:i');
        $hora_entrega_solicitado = date_format($fecha_entrega_solicitado, 'H:i');
        $hora_salida_solicitado = date_format($fecha_salida_solicitado, 'H:i');

        $horas_dispo = $this->genericas_model->obtener_valores_parametro_aux("IniFin", 20);
        if (empty($horas_dispo)) {
            $hora_inicio = date_format(date_create("06:30"), 'H:i');
            $hora_fin = date_format(date_create("19:30"), 'H:i');
        } else {
            $horas_dispo = explode(",", $horas_dispo[0]["valor"]);

            $hora_inicio = date_create($horas_dispo[0]);
            $hora_inicio = date_format($hora_inicio, 'H:i');

            $hora_fin = date_create($horas_dispo[1]);
            $hora_fin = date_format($hora_fin, 'H:i');
        }



        if ($forma <= $fecha_actual && $tipo==1) {
            return -4;
        }

        if ($fecha_salida_solicitado <= $fecha_entrega_solicitado) {
            return -3;
        }
        if ($hora_entrega_solicitado < $hora_inicio || $hora_entrega_solicitado > $hora_fin) {
            return -14;
        }

        if ($hora_salida_solicitado > $hora_fin || $hora_salida_solicitado < $hora_inicio) {
            return -15;
        }

        return 1;
    }
    public function Marcar_persona_entrega_recibe_reserva()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {
                $idpersona = $this->input->post("idpersona");
                $idreserva = $this->input->post("idreserva");
                $tipo = $this->input->post("tipo");
                $fecha = date("Y-m-d H:i");
                if (empty($tipo) ||  empty($idreserva)) {
                    echo json_encode(-1);
                    return;
                }
                if (empty($idpersona) && $tipo==1) {
                    echo json_encode(-2);
                    return;
                }

                if ($tipo == 1) {
                    $total = $this->reservas_model->buscar_estados_recursos_por_reserva($idreserva, "1");
                    if (!empty($total)) {
                        if ($total->{'total'} == 0 || $total->{'asig'} == 0) {
                            echo json_encode(-1);
                            return;
                        }
                    } else {
                        echo json_encode(-1);
                        return;
                    }

                }
                if ($tipo == 2) {
                    $idpersona = $_SESSION["persona"];
                }
                $resultado = $this->reservas_model->Marcar_persona_entrega_recibe_reserva($idreserva, $idpersona, $fecha, $tipo);
                echo json_encode($resultado);
            }
        }
    }

    public function Modificar_estado_reserva()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("id");
                $estado = $this->input->post("estado");
                $usuario = $_SESSION["persona"];
                $fecha = date("Y-m-d H:i:s");
                $resultado = $this->reservas_model->Modificar_estado_reserva($id, $estado, $usuario, $fecha);

                echo json_encode($resultado);
            }
        }
    }
    public function guardar_comentario()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {
                $id_reserva = $this->input->post("id");
                $comentario = $this->input->post("comentario");
                $usuario = $_SESSION["persona"];
                if (empty($comentario) || ctype_space($comentario)) {
                    echo json_encode(-2);
                    return;
                }
                if (empty($id_reserva) || ctype_space($id_reserva) || $id_reserva == 0) {
                    echo json_encode(-5);
                    return;
                }
                $resultado = $this->reservas_model->guardar_comentario($comentario, $usuario, $id_reserva);
                echo json_encode($resultado);
                return;
            }
        }
    }

    function listar_comentario()
    {
        $comentarios = array();

        if ($this->Super_estado == false) {
            echo json_encode($comentarios);
            return;
        }
        $id = $this->input->post('id');
        $datos = $this->reservas_model->listar_comentario($id);
        $i = 1;
        foreach ($datos as $row) {
            $row["indice"] = $i;
            $comentarios["data"][] = $row;
            $i++;
        }

        echo json_encode($comentarios);
    }

    public function Marcar_persona_recurso_entrega_recibe()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_modifica == 0) {
                echo json_encode(-1302);
            } else {
                $id = $this->input->post("id");
                $id_recurso = $this->input->post("id_recurso");
                //echo json_encode($id." ".$id_recurso);return;

                $usuario = $_SESSION["persona"];
                $fecha = date("Y-m-d H:i:s");
                $tipo = $this->input->post("tipo");

                if (empty($id) || empty($id_recurso) || empty($tipo) || $id == 0) {
                    echo json_encode(-1);
                    return;
                }
                $resultado = $this->reservas_model->Marcar_persona_recurso_entrega_recibe($id, $id_recurso, $usuario, $fecha, $tipo);

                echo json_encode($resultado);
            }
        }
    }

    public function Agregar_Calificacion_Reserva()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        } else {
            if ($this->Super_agrega == 0) {
                echo json_encode(-1302);
            } else {
                $fecha = date("Y-m-d H:i");
                $idreserva = $this->input->post("id");
                $cal = $this->input->post("estrellas");
                $observacion = $this->input->post("observacion");
                $resultado = $this->reservas_model->Agregar_Calificacion($idreserva, $cal, $fecha, $observacion);

                echo json_encode($resultado);
            }
        }
    }


    public function Puede_Calificar()
    {
        $idreserva = $this->input->post("id");
        $estado = $this->reservas_model->obtener_Estado_Reserva($idreserva);
        if ($estado == "Res_Recib") {
            $concalificacion = $this->reservas_model->con_calificacion($idreserva);
            if ($concalificacion == true) {
                echo json_encode(2);
            } else {
                echo json_encode(4);
            }
        } else {
            echo json_encode(1);
        }
    }

    public function obtener_recursos_reservados_por_reserva()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $idreserva = $this->input->post("id");
        $recursos = $this->reservas_model->traer_recursos_por_reserva($idreserva);
        echo json_encode($recursos);
        return;
    }
    public function Transformar_Fecha($fecha_entrega, $fecha_salida)
    {
        if (is_numeric($fecha_salida)) {
            if ($fecha_salida <= 0) {
                $fecha_salida = 1;
            }
            $fecha_salida = strtotime('+' . $fecha_salida . ' hour', strtotime($fecha_entrega));
            $fecha_salida = date('Y-m-d H:i', $fecha_salida);
        }
        return $fecha_salida;
    }


    public function cargar_personas_reserva()
    {
        $dato = $this->input->post('dato');
        $tipo = $this->input->post('tipo');
        $resp = $this->Super_estado && !empty($dato) ? $this->reservas_model->cargar_personas_reserva($dato,$tipo) : array();
        echo json_encode($resp);
    }

    public function traer_data_prueba()
    {
        $resp = $this->Super_estado ? $this->reservas_model->traer_data_prueba() : array();
        echo json_encode($resp);
    }
    public function traer_pruebas_estudiante()
    {   $identificacion = $this->input->post('identificacion');
        $resp = $this->Super_estado ? $this->reservas_model->traer_pruebas_estudiante($identificacion) : array();
        echo json_encode($resp);
    }



}

?>
