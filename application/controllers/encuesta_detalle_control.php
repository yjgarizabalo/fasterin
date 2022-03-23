<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use function PHPSTORM_META\type;

class encuesta_detalle_control extends CI_Controller
{
    var $Super_estado = false;
    var $Super_elimina = 0;
    var $Super_modifica = 0;
    var $Super_agrega = 0;
    var $idp = 0;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('encuesta_detalle_model');
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

    public function index($pages = "encuestas", $id = 0)
    {
        if ($this->Super_estado) {
            $datos_actividad = $this->genericas_model->Listar_permisos_perfil_actividad($_SESSION["perfil"], "encuesta");
            if (!empty($datos_actividad)) {
                $data['js'] = "Encuesta";
                $data['id'] = $id;
                $data['actividad'] = $datos_actividad[0]["id_actividad"];
            } else {
                $pages = "sin_session";
                $data['js'] = "";
                $data['actividad'] = "Permisos";
            }
        } else {
            $pages = "sin_session";
            $data['js'] = "";
            $data['actividad'] = "Ingresar";
        }
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function encuesta($id = 0)
    {
        $pages = "inicio";
        $data['js'] = "";
        $data['actividad'] = "Ingresar";
        $data["id"] = $id;
        if ($this->Super_estado) {
            $pages = "encuestas_agil";
            $data['js'] = "Encuesta";
            $data['id'] = $id;
            $data['actividad'] = "Encuesta";
            $encuesta_detalle = $this->encuesta_detalle_model->traer_registro_id($_SESSION["persona"], $id);
            $data['estado_encuesta'] = empty($encuesta_detalle);
        } else {
            $pages = "sin_session";
            $data['js'] = "";
            $data['actividad'] = "Permisos";
        }
        $this->load->view('templates/header', $data);
        $this->load->view("pages/" . $pages);
        $this->load->view('templates/footer');
    }

    public function obtener_pasos($id = 0)
    {
        $id = $this->input->post("idpp");
        $data = $this->encuesta_detalle_model->listar_pasos($id);
        echo json_encode($data);
    }

    public function get_preguntas($id = 0)
    {
        $id = $this->input->post("id_paso");
        $preguntas = $this->encuesta_detalle_model->listar_preguntas($id);
        echo json_encode($preguntas);
    }

    public function obtener_respuesta($id = 0)
    {
        $id = $this->input->post("id_pregunta");
        $respuestas = $this->encuesta_detalle_model->listar_respuestas($id);
        echo json_encode($respuestas);
    }

    public function guardar_respuestas()
    {
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        else {
            if ($this->Super_agrega) {
                $data_respuestas = $this->input->post("data_respuesta");
                /* $id_encuesta = $this->input->post("id_encuesta"); */
                $data = array();

                foreach ($data_respuestas as $row) {
                    $row['id_usuario_registra'] = $_SESSION["persona"];
                    array_push($data, $row);
                }
                $add = $this->pages_model->guardar_datos($data, 'encuesta_detalle', 2);
                if ($add) {
                    $resp = ['mensaje' => "Se guardo correctamente", 'tipo' => "success", 'titulo' => "Excelente!"];
                    $ultimo = $this->encuesta_detalle_model->ultimo_registro($_SESSION['persona']);
                    $add = $this->pages_model->guardar_datos(['id_encuesta_detalle' => $ultimo->{'id'}, 'id_persona' => $_SESSION['persona'], 'id_usuario_registra' => $_SESSION['persona']], 'encuestas');
                } else {
                    $resp = ['mensaje' => "No se guardo correctamente", 'tipo' => "error", 'titulo' => "Oops!"];
                }
            }
        }
        echo json_encode($resp);
    }

    public function get_descripcion($id = 0)
    {
        $id = $this->input->post("idpp");
        $data = $this->encuesta_detalle_model->get_description($id);
        echo json_encode($data);
    }

    //Listado de las personas que realizaron la encuesta
    public function listar_encuestas_usuario()
    {
        $encuestas = array();
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        else {
            $resp = $this->encuesta_detalle_model->listar_encuestas_usuario();
        }
        foreach ($resp as $row) {
            $row["ver"] = '<span style="color: black; background-color: white; width: 100%;" title="ver" data-toggle="popover" data-trigger="hover" class="btn btn-default pointer form-control ver">ver</span>';
            $encuestas["data"][] = $row;
        }

        echo json_encode($encuestas);
    }

    public function listar_encuestas()
    {
        $encuestas = array();
        if (!$this->Super_estado) $resp = ['mensaje' => "", 'tipo' => "sin_session", 'titulo' => ""];
        else {
            $resp = $this->encuesta_detalle_model->listar_encuestas();
        }
        foreach ($resp as $row) {
            $row["ver"] = '<span style="color: black; background-color: white; width: 100%;" title="ver" data-toggle="popover" data-trigger="hover" class="btn btn-default pointer form-control ver">ver</span>';
            $encuestas["data"][] = $row;
        }

        echo json_encode($encuestas);
    }

    public function detalle_encuesta($id = 0)
    {
        if (!$this->Super_estado) $resp = [];
        else {
            $id = $this->input->post("id_persona");
            $resp = $this->encuesta_detalle_model->detalle_encuesta($id);
        }

        echo json_encode($resp);
    }

    public function ver_respuesta()
    {
        if (!$this->Super_estado) $resp = [];
        else {
            $id_paso = $this->input->post("id_paso");
            $id_usuario = $this->input->post("id_persona");
            $resp = $this->encuesta_detalle_model->ver_respuesta($id_paso, $id_usuario);
        }

        echo json_encode($resp);
    }
}
