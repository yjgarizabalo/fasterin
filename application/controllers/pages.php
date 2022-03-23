<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class pages extends CI_Controller
{

    var $Super_estado = false;
    var $Super_elimina = 0;
    var $Super_modifica = 0;
    var $Super_agrega = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('personas_model');
        $this->load->model('genericas_model');
        $this->load->model('pages_model');
        session_start();
        if (isset($_SESSION["usuario"])) {
            $this->Super_estado = true;
            $this->Super_elimina = 1;
            $this->Super_modifica = 1;
            $this->Super_agrega = 1;
        }
    }

    public function Index($page = "inicio", $mensaje = "")
    {

        if (!file_exists(APPPATH . 'views/pages/' . $page . '.php')) {
            show_404();
            return;
        }


        $data['js'] = "";
        $data['actividad'] = "";
        $data['cargar'] = "si";
        $data['actividad'] = "Inicio";
        $data['mensaje'] = $mensaje;
        if ($this->Super_estado) {
            $actividades_user = $this->Listar_Actividades_perfil();
            $data['actividades'] = $actividades_user;
            $page = "cuc";
            $data['title'] = "Inicio";
        } else {
            $data['actividad'] = "Ingresar";
            if ($page != "sin_session") $page = "inicio";
        }
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $page);
        $this->load->view('templates/footer');
    }

    public function cargar_modulo($page)
    {

        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], $page);
            if (!empty($datos_actividad)) {
                $actividades_user = $this->Listar_Actividades_perfil($page);
                $data['actividades'] = $actividades_user;
                $pages = "submodulos";
                $data['title'] = "Inicio";
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

    /* Funcion para verificar si el usuario existe */
    public function check_users($user_to_check)
    {
        $r = [];
        $query = $this->pages_model->check_users($user_to_check);
        if ($query) {
            $id_persona = $query->usuario;
            $numero_limite = 4;
            $codigo = 0;
            while (strlen($codigo) < $numero_limite) {
                $num = rand(0, 9999);
                $codigo += $num;
            }
            $query->check_code = $codigo;
            $arrayToSend = ["validation_code" => $codigo];
            $send_code = $this->pages_model->update_inf($id_persona, $arrayToSend);
            if ($send_code == 1) {
                $mail_body = "Su código de verificación es: <strong>$codigo</strong>";
                $correo_recibe = $query->correo;
                $nombre_recibe = $query->full_name;
                $usuario = $query->usuario;
                $mail_title = "código de verificación para inicio de sesión.";
                $tipo = 1;
                $r = [
                    "mail_body" => $mail_body,
                    "correo_recibe" => $correo_recibe,
                    "nombre_recibe" => $nombre_recibe,
                    "mail_title" => $mail_title,
                    "usuario" => $usuario,
                    "tipo" => $tipo,
                    "send_sw" => "si",
                    "resp" => 1000
                ];
            }
        } else {
            $r = ["mensaje" => "Usuario y/o contraseña incorrectos!", "tipo" => "info", "titulo" => "Oops!"];
        }
        exit(json_encode($r));
    }

    /* Check codigo subministrado por el usuario */
    public function check_validation_code()
    {
        $r = [];
        $codigo = $this->input->post("codigo");
        $correo = $this->input->post("correo");
        $query = $this->pages_model->check_validation_code($correo, $codigo);
        if ($query) {
            $r = ["resp" => "valido"];
        } else {
            $r = ["mensaje" => "El codigo de verificación insertado no es correcto; intente nuevamente.", "tipo" => "info", "titulo" => "Oops!", "resp" => "no_valido"];
        }
        exit(json_encode($r));
    }

    public function Logear()
    {
        $logear_elda = "no";
        $usuario = $this->input->post("usuario");
        $contrasena = $this->input->post("contrasena");
        $checked = $this->input->post("checked");
        //exit(json_encode($checked));

        if (empty($usuario) || empty($contrasena) || ctype_space($usuario) || ctype_space($contrasena)) {
            echo json_encode(2);
            return;
        }
        $int_elda = $this->genericas_model->obtener_valores_parametro_aux("IntLdap", 20);
        if (!empty($int_elda)) {
            $int_elda = $int_elda[0];
            $logear_elda = $int_elda["valor"];
        }
        if (empty($int_elda) || $logear_elda != "si") {
            $existe = $this->personas_model->Listar_usuario_contrasena($usuario, md5($contrasena));
        } else {
            $existe_elda = $this->Logear_LDAP($usuario, $contrasena);
            if ($checked == "si") {
                $existe_elda = 2;
            } else {
                $existe_elda = $this->Logear_LDAP($usuario, $contrasena);
            }

            if ($existe_elda == 1) {
                //AQUI
                // $check_user = $this->check_users($usuario);
                // exit($check_user);
                echo json_encode(33);
                return;
            } else {
                $existe = $this->personas_model->Listar_solo_usuario($usuario);
            }
        }

        if (empty($existe)) {

            echo json_encode(3);
            return;
        } else {
            $arrUsuario = $existe[0];
            if (is_null($arrUsuario['id_perfil'])) {
                echo json_encode(5);
                return;
            }


            if ($arrUsuario['acept_politicas'] == 0) {
                $acept = $this->input->post("acept");
                if ($acept == 0) {
                    echo json_encode(4);
                    return;
                } else {
                    $aceptando = $this->personas_model->Aceptar_Politicas($arrUsuario['id']);
                    if ($aceptando != 4) {
                        echo json_encode(-8);
                        return;
                    }
                }
            }
            $_SESSION['usuario'] = $arrUsuario['usuario'];
            $_SESSION['perfil'] = $arrUsuario['id_perfil'];
            $_SESSION['persona'] = $arrUsuario['id'];
            $_SESSION['nombre'] = $arrUsuario['nombre'];
            $_SESSION['apellido'] = $arrUsuario['apellido'];
            $_SESSION['correo'] = $arrUsuario['correo'];

            echo json_encode(1);
            return;
        }
    }

    public function cerrar()
    {
        session_destroy();
        session_unset();
        echo json_encode(1);
    }

    public function Ensession()
    {

        if (empty($_SESSION["usuario"]) || empty($_SESSION['apellido']) || empty($_SESSION['nombre'])) {
            return false;
        } else {
            return true;
        }
        return false;
    }

    public function Permisos_perfil()
    {
        $this->load->model('genericas_model');
        $idperfil = $_SESSION["perfil"];
        $actividad = $_SESSION["actividad"];
        $datos = $this->genericas_model->Listar_permisos_perfil_actividad($idperfil, $actividad);
        if (empty($datos)) {
            return false;
        } else {
            $datosx = $datos[0];
            $_SESSION["agrega"] = $datosx["agrega"];
            $_SESSION["modifica"] = $datosx["modifica"];
            $_SESSION["elimina"] = $datosx["elimina"];
            return true;
        }
    }

    public function Permisos_perfil_vista()
    {
        $this->load->model('genericas_model');
        $idperfil = $_SESSION["perfil"];
        $actividad = $this->input->post("actividad");
        if (!$this->Super_estado) {
            echo json_encode(-2);
            return;
        } else {
            $datos = $this->genericas_model->Listar_permisos_perfil_actividad($idperfil, $actividad);
            if (empty($datos)) {
                echo json_encode(-2);
                return;
            }
            echo json_encode($datos);
            return;
        }
    }

    public function Perfil_session()
    {
        echo json_encode($_SESSION['perfil']);
    }

    public function Listar_Actividades_perfil($sw = null)
    {
        $this->load->model('genericas_model');
        $idperfil = $_SESSION["perfil"];
        $datos = $this->genericas_model->Listar_Actividades_perfil($idperfil, $sw);
        return $datos;
    }

    public function Logear_LDAP($usr, $pass)
    {
        require_once(APPPATH . '../LDAP/ldap.php');
        $existe = mailboxpowerloginrd($usr, $pass);
        if ($existe == "0" || $existe == '') {
            return 1;
        } else {
            return 2;
        }
    }

    public function verificar_password()
    {
        $usuario = $this->input->post("usuario");
        $password = $this->input->post("password");
        $int_elda = $this->genericas_model->obtener_valores_parametro_aux("IntLdap", 20);
        if (!empty($int_elda)) {
            $int_elda = $int_elda[0];
            $logear_elda = $int_elda["valor"];
        }
        if (empty($int_elda) || $logear_elda != "si") {
            $data = $this->personas_model->Listar_usuario_contrasena($usuario, md5($password));
            $loggedIn = empty($data) ? 1 : 2;
            $resp = ["existe" => $loggedIn, "datos" => $data];
        } else {
            $data = $this->personas_model->Listar_solo_usuario($usuario);
            $loggedIn = $this->Logear_LDAP($usuario, $password);
            $resp = ["existe" => $loggedIn, "datos" => $data];
        }
        echo json_encode($resp);
        return;
    }

    function obtener_datos_persona_usuario_session()
    {
        if ($this->Super_estado == false) {
            echo json_encode("sin_session");
            return;
        }
        $id = $_SESSION["persona"];
        $this->load->model('personas_model');
        $datos = $this->personas_model->obtener_Datos_persona_id_completos($id);
        echo json_encode($datos);
    }

    public function enviar_correo_personalizado()
    {
        $estructura = "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'><html xmlns='http://www.w3.org/	1999/xhtml'>
			<head>
				<style>
					body{
						font-size: 16px;
						font-family: 'Helvetica Neue', 'Helvetica', 'Arial', 'sans-serif';
						line-height: 1.5;
					}
					table {
						border-collapse: collapse;
						width: 100%;
					}
					td, th {
						border: 1px solid #ddd;
						padding: 8px; 
					}
					tr:hover {background-color: #ddd;}
					.fila_principal {
						padding-top: 12px;
						padding-bottom: 12px;
						text-align: center;
						background-color: #337ab7;
						color: white;
					}
				</style>
			</head>
			<body>
				<p style='padding:0px;margin:0px;'>Señor(a)</p> ";
        $this->load->model('genericas_model');
        $mensaje = $this->input->post("mensaje");
        $correo = $this->input->post("correo");
        $cod = $this->input->post("codigo");
        $from = $this->input->post("from");
        $adj = $this->input->post("adjunto");
        $tipo = $this->input->post("tipo");
        $archivo = $this->input->post('archivo');
        $nombre_completo = $this->input->post("nombre");
        $externo = $this->input->post("externo");
        if ($tipo == -1) {
            $nombre_completo = $_SESSION["nombre"] . " " . $_SESSION['apellido'];
            $correo = $_SESSION["correo"];
        }

        $estructura .= $externo ? "<h3>" . strtoupper($nombre_completo) . "</h3>" : "<h3>" . strtoupper($nombre_completo) . "</h3><h4>CUC</h4></br></br>";

        if (empty($correo)) {
            echo json_encode(-2);
            return;
        } else if (empty($mensaje)) {
            echo json_encode(-3);
            return;
        } else if (empty($cod)) {
            echo json_encode(-4);
            return;
        }
        $datos_correo = $this->genericas_model->obtener_valores_parametro_aux($cod, 20);
        if (empty($datos_correo)) {
            echo json_encode(-1);
            return;
        }
        $mensaje = $estructura . $mensaje . "<p>Estamos atentos a cualquier inquietud o sugerencia.</p></body></html>";
        $datos_correo = $datos_correo[0];
        $email = $datos_correo["valor"];
        $password = $datos_correo["valory"];
        require_once(APPPATH . 'libraries/phpmailer/autoload.php');
        $mail = new PHPMailer(true); // Passing `true` enables exceptions
        try {
            $mail->SMTPDebug = 0; // Enable verbose debug output
            $mail->isSMTP(); // Set mailer to use SMTP
            $mail->Host = 'smtp.office365.com'; // Specify main and backup SMTP servers
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->CharSet = 'utf8';
            $mail->Username = $email; // SMTP username
            $mail->Password = $password; // SMTP password
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587; // TCP port to connect to
            $mail->setFrom($email, $from);
            if ($tipo == 3) {
                foreach ($correo as $row) {
                    $mail->addAddress($row["correo"], ucfirst(strtolower($row["persona"]))); // Add a recipient
                }
            } else $mail->addAddress($correo, ucfirst(strtolower($nombre_completo))); // Add a recipient
            $mail->Subject = $adj;
            $mail->Body = $mensaje;
            // 0 Path
            // 1 File Name
            if (!empty($archivo)) {
                if (is_array($archivo[0])) {
                    foreach ($archivo as $row) {
                        $mail->AddAttachment(realpath(APPPATH . $row[0]), $row[1]);
                    }
                } else $mail->AddAttachment(realpath(APPPATH . $archivo[0]), $archivo[1]);
            }
            $mail->isHTML(true);                                  // Set email format to HTML
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->send();
            return (json_encode(1));
        } catch (Exception $e) {
            exit(json_encode(0));
        }
    }

    public function cambiar_perfil()
    {
        $perfil = $this->input->post("perfil");
        $resp = $this->personas_model->Existe_perfil($_SESSION['persona'], $perfil);
        if ($resp == true) $_SESSION['perfil'] = $perfil;
        echo json_encode($resp);
        return;
    }

    public function guardar_comentario_general()
    {
        if ($this->Super_estado == false) {
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        } else {
            if ($this->Super_agrega == 0) {
                $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
                $tipo = $this->input->post("tipo");
                $id_solicitud =  $this->input->post("id_solicitud");
                $comentario = $this->input->post("comentario");
                $id_comentario = $this->input->post("id_comentario");
                $usuario_registra = $_SESSION['persona'];
                if (empty($id_solicitud) || empty($tipo)) {
                    $resp = ['mensaje' => "Error al cargar la información, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                } else if (ctype_space($comentario) || empty($comentario)) {
                    $resp = ['mensaje' => "Ingrese Comentario.", 'tipo' => "info", 'titulo' => "Oops.!"];
                } else {
                    if (!isset($id_comentario) || empty($id_comentario)) $id_comentario = null;
                    $data = array("id_solicitud" => $id_solicitud, "comentario" => $comentario, "usuario_registra" => $usuario_registra, "id_comentario" => $id_comentario, "tipo" => $tipo);
                    $resp = ['mensaje' => "", 'tipo' => "success", 'titulo' => "Comentario Guardado.!"];
                    $add = $this->pages_model->guardar_datos($data, 'comentarios_generales');
                    if ($add == -1) $resp = ['mensaje' => "Error al guardar el comentario, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function listar_comentarios_general()
    {
        $id_solicitud = $this->input->post("id_solicitud");
        $tipo = $this->input->post("tipo");
        $datos = $this->Super_estado ? $this->pages_model->listar_comentarios_general($id_solicitud, $tipo) : array();
        echo json_encode($datos);
    }
    public function listar_respuestas_comentario_general()
    {
        $id = $this->input->post("id");
        $datos = $this->Super_estado ? $this->pages_model->listar_respuestas_comentario_general($id) : array();
        echo json_encode($datos);
    }

    public function terminar_comentario_general()
    {
        if ($this->Super_estado == false) {
            $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        } else {
            if ($this->Super_modifica == 0) {
                $resp = ['mensaje' => "No tiene Permisos Para Realizar Esta operación.", 'tipo' => "error", 'titulo' => "Oops.!"];
            } else {
                $id = $this->input->post("id");
                $usuario_termina = $_SESSION['persona'];
                $fecha_termina = date("Y-m-d H:i:s");
                if (empty($id)) {
                    $resp = ['mensaje' => "Error al cargar el ID del comentario, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                } else {
                    $data = array("estado_notificacion" => 0, "usuario_termina" => $usuario_termina, "fecha_termina" => $fecha_termina);
                    $resp = ['mensaje' => "", 'tipo' => "success", 'titulo' => "Comentario Terminado.!"];
                    $mod = $this->pages_model->modificar_datos($data, 'comentarios_generales', $id);
                    if ($mod == -1) $resp = ['mensaje' => "Error al terminar el comentario, contacte con el administrador.", 'tipo' => "error", 'titulo' => "Oops.!"];
                }
            }
        }
        echo json_encode($resp);
    }
    public function listar_notificaciones_comentarios_general()
    {
        $tipos = $this->input->post("tipos");
        $adms = $this->input->post("adms");
        $datos = $this->Super_estado ? $this->pages_model->listar_notificaciones_comentarios_general($tipos, $adms) : array();
        echo json_encode($datos);
    }

    public function obtener_valores_permiso()
    {
        $vp_principal = $this->input->post('vp_principal');
        $idparametro = $this->input->post('idparametro');
        $tipo = $this->input->post('tipo');
        $resp = $this->Super_estado ? $this->pages_model->obtener_valores_permiso($vp_principal, $idparametro, $tipo) : array();
        echo json_encode($resp);
    }

    public function obtener_notificacion_talentocuc($resp)
    {
        $num = null;
        $id_persona = $_SESSION["persona"];
        $this->load->model('talento_cuc_model');
        $info = $this->talento_cuc_model->obtener_info_persona($id_persona);
        $data = $this->talento_cuc_model->cantidad_asistencias_entrenamiento($info[0]['identificacion']);
        $send = $this->talento_cuc_model->get_encuesta_enviada($info[0]['identificacion']);
        $y = $data['aprobados'];
        $x = $data['cantidad'];
        if ($x != $y) {
            $num = $x - $y;
            array_push($resp, [
                'nombre' => "Plan de Entrenamiento",
                'descripcion' => "Asistencia(s) nuevas por confirmar",
                'cantidad' => $num,
                'accion' => "window.open('" . base_url() . "index.php/talento_cuc/asistencia_entrenamiento/$id_persona')"
            ]);
        }

        if ($send) {
            array_push($resp, [
                'nombre' => "Plan de Entrenamiento",
                'descripcion' => "Encuesta de Entrenamiento por gestionar",
                'cantidad' => 1,
                'accion' => "window.open('" . base_url() . "index.php/talento_cuc/encuesta_entrenamiento/$id_persona')"
            ]);
        }

        $acta = $this->talento_cuc_model->estado_entrenamiento($id_persona);
        if (!empty($acta) && $acta->{'acta_enviada'} == 1 && $acta->{'firma_fun'} == null && $acta->{'terminado'} == 0) {
            array_push($resp, [
                'nombre' => "Acta de Aceptación de Cargo",
                'descripcion' => 'Acta por confirmar.',
                'cantidad' => 1,
                'accion' => "window.open('" . base_url() . "index.php/talento_cuc/acta_cargo/$id_persona')"
            ]);
        }

        $sop = $this->talento_cuc_model->get_soportes_Avalar();
        foreach ($sop as $row) {
            if ($row['cantidad'] > 0) {
                array_push($resp, [
                    'nombre' => "Plan de Formación",
                    'descripcion' => $row['nombre_completo'],
                    'cantidad' => $row['cantidad'] . " Soporte(s) por avalar de",
                    'accion' => "window.open('" . base_url() . "index.php/talento_cuc')"
                ]);
            }
        }

        $actas = $this->talento_cuc_model->listar_actas_personas($id_persona);
        if ($actas[2] > 0) {
            array_push($resp, [
                'nombre' => "Actas de Aceptación de Cargo",
                'descripcion' => "Acta(s) por firmar como Jefe Inmediato",
                'cantidad' => $actas[2],
                'accion' => "window.open('" . base_url() . "index.php/talento_cuc/validar_actas_entrenamiento/$id_persona')"
            ]);
        }

        return $resp;
    }

    public function obtener_notificaciones_agil($data)
    {
        $id_persona = $_SESSION["persona"];
        $this->load->model('encuesta_detalle_model');
        $encuesta_detalle = $this->encuesta_detalle_model->get_encuesta_detalle($id_persona);
        $info = $this->pages_model->obtener_info_notificacion();

        if (empty($encuesta_detalle)) {
            foreach ($info as $row) {
                if ($row['valorz'] === '') {
                    array_push($data, [
                        'nombre' => $row['valor'],
                        'descripcion' => "encuesta disponible para realizar.",
                        'cantidad' => 1,
                        'accion' => "window.open('" . base_url() . "index.php/encuesta/encuesta_agil/" . $row['id'] . "')"
                    ]);
                }
            }
        } else {
            foreach ($info as $row) {
                foreach ($encuesta_detalle as $row2) {
                    //Manejo de una encuesta diferente no realizada
                    if ($row['id'] !== $row2['id_encuesta'] && $row['valorz'] === '') {
                        array_push($data, [
                            'nombre' => $row['valor'],
                            'descripcion' => "encuesta disponible para realizar.",
                            'cantidad' => 1,
                            'accion' => "window.open('" . base_url() . "index.php/encuesta/encuesta_agil/" . $row['id'] . "')"
                        ]);
                    }
                }
            }
        }

        //Manejo de link externo
        foreach ($info as $row) {
            if ($row['valorz'] !== '') {
                array_push($data, [
                    'nombre' => $row['valor'],
                    'descripcion' => "encuesta disponible para realizar.",
                    'cantidad' => 1,
                    'accion' => "window.open('" . $row['valorz'] . "')"
                ]);
            }
        }

        return $data;
    }

    public function obtener_notificacion_evaluacion($data)
    {
        $id_persona = $_SESSION["persona"];
        $this->load->model('evaluacion_model');
        $evaluacion = $this->evaluacion_model->get_solicitudes_persona($id_persona);
        foreach ($evaluacion as $row) {
            if ($row['id_estado_eval'] === 'Eval_Env' || $row['id_estado_eval'] === 'Eval_Pro') {
                array_push($data, [
                    'nombre' => "Evaluación Administrativa",
                    'descripcion' => $row['metodo'] . " asignada del periodo " . $row['periodo'],
                    'cantidad' => 1,
                    'accion' => "window.open('" . base_url() . "index.php/evaluacion/encuesta/" . $row['id'] . "')"
                ]);
            }

            if ($row['acta_enviada'] == 1) {
                array_push($data, [
                    'nombre' => "Actas de Retroalimentación",
                    'descripcion' => "Actas por gestionar del periodo " . $row['periodo'],
                    'cantidad' => '',
                    'accion' => "window.open('" . base_url() . "index.php/evaluacion/acta/" . $row['id'] . "')"
                ]);
            }

            if ($row['acta'] == 1 && $row['recibido'] == 0) {
                array_push($data, [
                    'nombre' => "Confirmar Retroalimentación",
                    'descripcion' => "Acta de Retroalimentación por confirmar del periodo " . $row['periodo'],
                    'cantidad' => 1,
                    'accion' => "window.open('" . base_url() . "index.php/evaluacion/confirmar_acta/" . $row['id'] . "')"
                ]);
            }
        }
        return $data;
    }

    public function obtener_notificacion_mantenimiento($data){
        
        $id_persona = $_SESSION["persona"];
        $this->load->model('mantenimiento_model');
        $sin_calificacion = $this->mantenimiento_model->get_mantenimiento_por_evaluar($id_persona);
        if($sin_calificacion){
            foreach($sin_calificacion as $row){
                array_push($data,[
                    'nombre'=> "Calificación Mantenimiento",
                    'descripcion' => "calificacion(es) pendiente(s)", 
                    'cantidad' => $row['cantidad'],
                    'accion' => "mantenimiento(".$row['id'].")"
                ]);
            }
        }
        
        return $data;
    }

    public function obtener_notificacion_almacen($data)
    {
        $num = null;
        $id_persona = $_SESSION["persona"];
        $this->load->model('almacen_model');
        $res = $this->almacen_model->listar_num_solicitudes_pendientes();
        $texto = ($res[0]['num'] > 1) ? 'solicitudes' : 'solicitud';
        if ($res[0]['num'] > 0) {
            array_push($data, [
                'nombre' => "Almacén",
                'descripcion' =>  $texto . ' por gestionar.',
                'cantidad' => $res[0]['num'],
                'accion' => "window.open('" . base_url() . "index.php/almacenADM/solicitudes')"
            ]);
        }
        return $data;
    }


    public function mostrar_notificaciones_general()
    {
        $resp = [];
        if (!$this->Super_estado) $resp;
        else {
            $data = [];
            $url = strtolower($this->input->post('url'));
            $ruta = strtolower(base_url() . 'index.php/');
            $ruta2 = strtolower(base_url() . 'index.php');

            if ($url == $ruta || $url == $ruta2 || $url == strtolower(base_url())) {
                $data = $this->obtener_notificaciones_agil([]);
            }

            if ($_SESSION['perfil'] == 'Per_Alm') {
                $data = $this->obtener_notificacion_almacen($data);
            }

            $data_talentocuc = $this->obtener_notificacion_talentocuc($data);
            $resp = $this->obtener_notificacion_evaluacion($data_talentocuc);
        }
        echo json_encode(['resp' => $resp]);
    }
}
