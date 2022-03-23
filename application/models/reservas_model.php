<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class reservas_Model extends CI_Model
{

    var $table_reserva = "reservas";
    var $table__recursos_reserva = "recursos_reserva";
    var $select_column = array("id", "id_inventario", "id_usuario", "fecha_entrega", "fecha_salida", "estado", "observaciones", "id_tipo_entrega", "lugar", "id_tipo_prestamo", "asignatura", "fecha_real_entrega", "fecha_real_recibe");

    public function make_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table_reserva);
    }

    public function guardar($asignatura, $usuario_registra, $fecha_salida, $tipo_prestamo, $lugar, $tipo_entrega, $observaciones, $fecha_entrega, $clase, $estado, $persona_soli, $tipo_reserva)
    {

        $this->db->insert($this->table_reserva, array(
            "id_usuario" => $persona_soli,
            "fecha_entrega" => $fecha_entrega,
            "fecha_salida" => $fecha_salida,
            "observaciones" => $observaciones,
            "id_tipo_entrega" => $tipo_entrega,
            "lugar" => $lugar,
            "id_tipo_prestamo" => $tipo_prestamo,
            "asignatura" => $asignatura,
            "id_tipo_clase" => $clase,
            "estado" => $estado,
            "usuario_registra" => $usuario_registra,
            "tipo_reserva" => $tipo_reserva,
        ));

        return $this->obtener_ultimo_registro_usuario_soladm($usuario_registra);
    }

    public function guardar_mas_recursos($id_tipo_recurso, $usuario_registra, $id_reserva)
    {

        $this->db->insert($this->table__recursos_reserva, array(
            "id_tipo_recurso" => $id_tipo_recurso,
            "usuario_registra" => $usuario_registra,
            "id_reserva" => $id_reserva,
        ));

        return 1;
    }

    public function Modificar_Reserva($asignatura, $fecha_salida, $tipo_prestamo, $lugar, $tipo_entrega, $observaciones, $fecha_entrega, $clase, $persona_soli, $id_reserva, $op)
    {
       
       /* if ($op == 1) {
            $this->db->set('tipo_recurso', $tipo);
            $this->db->set('fecha_entrega', $fecha_entrega);
            $this->db->set('fecha_salida', $fecha_salida);
        }*/


        $this->db->set('id_usuario', $persona_soli);

        $this->db->set('observaciones', $observaciones);
        $this->db->set('id_tipo_entrega', $tipo_entrega);
        $this->db->set('lugar', $lugar);
        $this->db->set('id_tipo_prestamo', $tipo_prestamo);
        $this->db->set('asignatura', $asignatura);
        $this->db->set('id_tipo_clase', $clase);

        $this->db->where('id', $id_reserva);
        $this->db->update($this->table_reserva);
        return 0;
    }

    public function Listar_reservas($estado, $fecha, $finicial, $ffinal, $entrega,$id)
    {
        date_default_timezone_set("America/Bogota");
        $this->db->select("r.tipo_reserva,IF(r.tipo_reserva = 'Res_Nor',(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM personas pa WHERE pa.id = r.id_usuario LIMIT 1),(SELECT CONCAT(pa.nombre,' ',pa.apellido,' ',pa.segundo_apellido) FROM visitantes pa WHERE pa.id = r.id_usuario LIMIT 1)) AS id_usuario, IF(r.tipo_reserva = 'empleado' OR r.tipo_reserva = 'res_nor',(SELECT pa.correo FROM personas pa WHERE pa.id = r.id_usuario LIMIT 1),(SELECT pa.correo FROM visitantes pa WHERE pa.id = r.id_usuario LIMIT 1)) AS correo_soli,r.fecha_entrega,r.fecha_salida,u1.valor estado,r.observaciones, u.valor id_tipo_entrega,r.lugar,r.id_tipo_prestamo,r.estado estado2,r.calificacion,TIMESTAMPDIFF(HOUR, r.fecha_entrega, r.fecha_salida ) horas,r.id_usuario id_usuario_sol,r.id,CONCAT(p2.nombre,' ',p2.apellido,' ',p2.segundo_apellido) as datos_registra_completo,CONCAT(p3.nombre,' ',p3.apellido,' ',p3.segundo_apellido) as datos_entrega_completo,CONCAT(p4.nombre,' ',p4.apellido,' ',p4.segundo_apellido) as datos_retira_completo,r.asignatura,u2.valor tipo_clase,u3.valor tipo_prestamo,CONCAT(p5.nombre,' ',p5.apellido,' ',p5.segundo_apellido) as usuario_cancela,r.fecha_cancela,r.fecha_real_entrega,r.fecha_real_recibe,r.observaciones_cali", false);
        $this->db->from('reservas r');
        $this->db->join('valor_parametro u', 'r.id_tipo_entrega=u.id');
        $this->db->join('personas p2', 'r.usuario_registra=p2.id');
        $this->db->join('personas p3', 'r.id_persona_entrega=p3.id', "left");
        $this->db->join('personas p4', 'r.id_persona_retira=p4.id', "left");
        $this->db->join('personas p5', 'r.usuario_cancela=p5.id', "left");
        $this->db->join('valor_parametro u2', 'r.id_tipo_clase=u2.id');
        $this->db->join('valor_parametro u3', 'r.id_tipo_prestamo=u3.id');
        $this->db->join('valor_parametro u1', 'r.estado=u1.id_aux');
        $sw = false;
        if ($_SESSION['perfil'] != "Per_Admin" && $_SESSION['perfil'] != "Per_Aud" && $_SESSION['perfil'] != "Admin_Aud") {
            $sw = true;
        }
        if (empty($estado) && empty($fecha) && empty($entrega)) {
            if ($id > 0) {
                $this->db->where("r.id", $id);
            }else if ($sw) {
                //$fecha_actual = date("Y-m");
                //$this->db->where("DATE_FORMAT(fecha_entrega,'%Y-%m')", $fecha_actual);
                
            } else {
                $fecha_actual = date("Y-m-d");
                $this->db->where("DATE_FORMAT(fecha_entrega,'%Y-%m-%d')", $fecha_actual);
                $this->db->or_where("DATE_FORMAT(fecha_salida,'%Y-%m-%d')", $fecha_actual);
            }
        } else {
            if (!empty($estado)) {
                $this->db->where("r.estado", $estado);
            }
            if (!empty($entrega)) {
                $this->db->where("r.id_tipo_entrega", $entrega);
            }
            if (!empty($fecha)) {
                if ($fecha == 2) {
                    $fecha_actual = date("Y-m");
                    $this->db->where("DATE_FORMAT(r.fecha_entrega,'%Y-%m')", $fecha_actual);
                } else if ($fecha == 3) {
                    $fecha_actual = date("Y");
                    $this->db->where("DATE_FORMAT(r.fecha_entrega,'%Y')", $fecha_actual);
                } else if ($fecha == 4) {
                    $fecha_actual = date("Y-m-d");
                    if (empty($finicial)) {
                        $finicial = $fecha_actual;
                    }
                    $this->db->where("DATE_FORMAT(r.fecha_entrega,'%Y-%m-%d')", $finicial);
                } 
            }
        }

        if ($sw) {
            $this->db->where("r.id_usuario", $_SESSION['persona']);
        }

        $this->db->_protect_identifiers = false;
        $this->db->order_by("FIELD (r.estado,'Res_Soli','Res_Entre','Res_Recib','Res_Canc')");
        $this->db->order_by("r.fecha_entrega");
        $this->db->_protect_identifiers = true;

        $query = $this->db->get();
        return $query->result_array();
    }



    public function Cargar_recurso_en_reserva_agrupados()
    {
        $this->db->select('*');
        $this->db->from('reservas r');
        $this->db->where("r.estado", "Res_Soli");
        $this->db->or_where("r.estado", "Res_Entre");
        $this->db->group_by('r.id_inventario');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Listar_reservas_detalle($id)
    {
        $this->db->select("r.id,u5.valor departamento,u6.valor id_tipo_identificacion ,p.correo,p.telefono,p.identificacion,p.segundo_nombre,p.segundo_apellido,p.nombre,p.apellido,r.fecha_entrega,r.fecha_salida,u1.valor estado,r.observaciones, u.valor id_tipo_entrega,r.lugar,u3.valor id_tipo_prestamo,u4.valor id_tipo_clase,r.asignatura,pr.nombre nombre_recibe,pr.apellido apellido_recibe,pr.segundo_apellido segundo_apellido_Recibe,pe.nombre nombre_entrega,pe.apellido apellido_entrega,pe.segundo_apellido segundo_apellido_entrega,r.fecha_real_entrega,fecha_real_recibe,calificacion,p2.nombre nombre_registra,p2.apellido apellido_registra,p2.apellido segundo_apellido_registra,p.id id_usuario_sol");
        $this->db->from('reservas r');
        $this->db->join('valor_parametro u', 'r.id_tipo_entrega=u.id');

        $this->db->join('personas p', 'r.id_usuario=p.id');
        $this->db->join('personas pr', 'r.id_persona_retira=pr.id', 'left');
        $this->db->join('personas pe', 'r.id_persona_entrega=pe.id', 'left');
        $this->db->join('personas p2', 'r.usuario_registra=p2.id');
        $this->db->join('valor_parametro u3', 'r.id_tipo_prestamo=u3.id');
        $this->db->join('valor_parametro u4', 'r.id_tipo_clase=u4.id');
        $this->db->join('cargos_departamentos c', 'p.id_cargo=c.id');
        $this->db->join('valor_parametro u5', 'c.id_departamento=u5.id');
        $this->db->join('valor_parametro u6', 'p.id_tipo_identificacion=u6.id');
        $this->db->join('valor_parametro u1', 'r.estado=u1.id_aux');
        $this->db->where("r.id", $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Listar_reservas_detalle_general($id)
    {
        $this->db->select("r.id id_reserva,r.id_usuario,r.fecha_entrega,r.fecha_salida,r.observaciones,r.id_tipo_entrega,r.lugar,r.id_tipo_prestamo,r.asignatura,r.id_tipo_clase,p.segundo_nombre,p.segundo_apellido,p.nombre,p.apellido,p.id, TIMESTAMPDIFF(HOUR ,r.fecha_entrega, r.fecha_salida ) AS horas,r.estado");
        $this->db->from('reservas r');
        $this->db->join('personas p', 'r.id_usuario=p.id');
        $this->db->where("r.id", $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Buscar_Recurso_Reserva($tipo, $fecha_inicial, $fecha_final)
    {
//        $query = $this->db->query("SELECT COUNT(rs.id_tipo_recurso) en_reserva FROM reservas r 
//INNER JOIN recursos_reserva rs ON r.id = rs.id_reserva
//WHERE  (r.fecha_entrega BETWEEN '$fecha_inicial' AND '$fecha_final' OR r.fecha_salida BETWEEN '$fecha_inicial' AND '$fecha_final') AND (r.estado = 'Res_Soli' OR r.estado = 'Res_Entre') AND rs.id_tipo_recurso = $tipo AND rs.estado = 1");
        $query = $this->db->query("SELECT COUNT(rs.id_tipo_recurso) en_reserva FROM reservas r 
INNER JOIN recursos_reserva rs ON r.id = rs.id_reserva
WHERE  ('$fecha_inicial' BETWEEN r.fecha_entrega AND r.fecha_salida OR '$fecha_final' BETWEEN r.fecha_entrega AND r.fecha_salida OR r.fecha_entrega BETWEEN '$fecha_inicial' AND '$fecha_final' OR  r.fecha_salida BETWEEN '$fecha_inicial' AND '$fecha_final') AND (r.estado = 'Res_Soli' OR r.estado = 'Res_Entre') AND rs.id_tipo_recurso = $tipo AND rs.estado = 1");

        $row = $query->row();
        return $row->en_reserva;
    }
    public function buscar_reservas_activas_usuario_fecha($fecha_inicial, $fecha_final,$usuario)
    {
        $query = $this->db->query("SELECT * FROM reservas r 
        WHERE  ('$fecha_inicial' BETWEEN r.fecha_entrega AND r.fecha_salida OR '$fecha_final' BETWEEN r.fecha_entrega AND r.fecha_salida OR r.fecha_entrega BETWEEN '$fecha_inicial' AND '$fecha_final' OR  r.fecha_salida BETWEEN '$fecha_inicial' AND '$fecha_final') AND (r.estado = 'Res_Soli' OR r.estado = 'Res_Entre') AND r.id_usuario = $usuario");
        return $query->result_array();
    }


    public function traer_recursos_por_reserva($id)
    {
        $this->db->select("r.id,r.id_tipo_recurso,u.valor tipo,i.codigo_interno,r.id_recurso,re.estado estado_reserva,r.fecha_registra");
        $this->db->from('recursos_reserva r');
        $this->db->join('valor_parametro u', 'r.id_tipo_recurso=u.id');
        $this->db->join('reservas re', 'r.id_reserva=re.id');
        $this->db->join('inventario i', 'r.id_recurso=i.id', "left");
        $this->db->where('r.id_reserva', $id);
        $this->db->where("r.estado", "1");
        $query = $this->db->get();
        return $query->result_array();
    }
    public function buscar_estados_recursos_por_reserva($idreserva, $estado)
    {
        $this->db->select("COUNT(id_reserva) total,COUNT(id_recurso) asig");
        $this->db->from($this->table__recursos_reserva);
        $this->db->where('id_reserva', $idreserva);
        $this->db->where("estado", $estado);
        $query = $this->db->get();
        $row = $query->row();
        return $row;

    }
    public function Cargar_Recursos_Audiovisuales()
    {
        if ($_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Per_Aud" || $_SESSION["perfil"] == "Admin_Aud") {
            $query = $this->db->query("SELECT tr.id,tr.valor recurso, count(i.tipo) disponibles,count(i.tipo) total,SUM(if(i.estado_recurso = 'RecEsp', 1, 0)) especiales,SUM(if(i.estado_recurso = 'RecAct', 1, 0)) normales 
            FROM inventario i 
            INNER JOIN valor_parametro tr ON tr.id = i.tipo
            INNER JOIN inventario_lugares il ON il.id_inventario = i.id
            INNER JOIN valor_parametro cr ON cr.id = il.id_ubicacion
            WHERE (i.estado_recurso = 'RecEsp' OR i.estado_recurso= 'RecAct') AND i.tipo_modulo = 'Inv_Aud' AND i.estado = 1 AND cr.id_aux = 'Rec_Res' AND il.estado = 'ResAct'
            GROUP by i.tipo ORDER by tr.valor ASC;
            ");
            return $query->result_array();
        }
        $query = $this->db->query("SELECT tr.id,tr.valor recurso, count(i.tipo) disponibles,count(i.tipo) total,0 as total,0 as especiales,0 as normales
        FROM inventario i 
        INNER JOIN valor_parametro tr ON tr.id = i.tipo
        INNER JOIN inventario_lugares il ON il.id_inventario = i.id
        INNER JOIN valor_parametro cr ON cr.id = il.id_ubicacion
        WHERE  i.estado_recurso= 'RecAct' AND i.tipo_modulo = 'Inv_Aud' AND i.estado = 1 AND cr.id_aux = 'Rec_Res' AND il.estado = 'ResAct'
        GROUP by i.tipo ORDER by tr.valor ASC;");
        return $query->result_array();
    }

    public function Cargar_Recursos_en_prestamos_Audiovisuales($tipo)
    {
        $query = $this->db->query('SELECT r.id,rs.id_recurso,rs.id_tipo_recurso FROM reservas r INNER JOIN recursos_reserva rs ON rs.id_reserva = r.id WHERE (r.estado = "Res_Soli" OR r.estado = "Res_Entre") AND rs.id_tipo_recurso = ' . $tipo . ' AND rs.estado=1 AND rs.id_recurso IS NOT NULL ORDER BY rs.id_recurso');
        return $query->result_array();
    }

    public function Marcar_persona_entrega_recibe_reserva($idreserva, $idpersona, $hora, $tipo)
    {
        if ($tipo == 1) {
            $this->db->set('id_persona_entrega', $idpersona);
            $this->db->set('estado', "Res_Entre");
            $this->db->set('fecha_real_entrega', $hora);
        } else {
            $this->db->set('id_persona_retira', $idpersona);
            $this->db->set('estado', "Res_Recib");
            $this->db->set('fecha_real_recibe', $hora);
        }
        $this->db->where('id', $idreserva);
        $this->db->update($this->table_reserva);
        return 4;
    }

    public function Marcar_persona_recurso_entrega_recibe($id, $id_recurso, $usuario, $fecha, $tipo)
    {
        if ($tipo == 1) {
            $this->db->set('id_recurso', $id_recurso);
            $this->db->set('id_usuario_entrega', $usuario);
            $this->db->set('fecha_entrega', $fecha);
        } else if ($tipo == -1) {
            $this->db->set('estado', 0);
            $this->db->set('usuario_elimina', $usuario);
            $this->db->set('fecha_elimina', $fecha);
        } else {

            $this->db->set('id_usuario_retira', $usuario);
            $this->db->set('fecha_retira', $fecha);
        }

        $this->db->where('id', $id);
        $this->db->update($this->table__recursos_reserva);
        return 0;
    }

    public function Agregar_Calificacion($idreserva, $cal, $fecha, $observacion)
    {
        $estado = $this->obtener_Estado_Reserva($idreserva);
        if ($estado == "Res_Recib") {
            $concalificacion = $this->con_calificacion($idreserva);
            if ($concalificacion == true) {
                return 2;
            } else {
                $this->db->set('calificacion', $cal);
                $this->db->set('fecha_calificacion', $fecha);
                $this->db->set('observaciones_cali', $observacion);
                $this->db->where('id', $idreserva);
                $this->db->update($this->table_reserva);
                return 4;
            }
        } else {
            return 1;
        }
    }

    public function Marcar_persona_recibe($idreserva, $idpersona, $hora)
    {
        $this->db->set('id_persona_retira', $idpersona);
        $this->db->set('estado', "Res_Recib");
        $this->db->set('fecha_real_recibe', $hora);
        $this->db->where('id', $idreserva);
        $this->db->update($this->table_reserva);
        return 4;
    }

    public function Modificar_estado_reserva($id, $estado, $usuario, $fecha)
    {
        if ($estado == "Res_Canc") {
            $estado_actual = $this->obtener_Estado_Reserva($id);
            if ($estado_actual != "Res_Soli") {
                return 2;
            }
            $this->db->set('usuario_cancela', $usuario);
            $this->db->set('fecha_cancela', $fecha);
        }
        $this->db->set('estado', $estado);
        $this->db->where('id', $id);
        $this->db->update($this->table_reserva);

        return 1;
    }

    public function obtener_Estado_Reserva($id)
    {
        $this->db->select("estado");
        $this->db->from('reservas');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();
        return $row->estado;
    }

    public function con_calificacion($id)
    {
        $this->db->select('calificacion');
        $this->db->from($this->table_reserva);
        $this->db->where('id', $id);
        $result = $this->db->get();
        $cal = $result->result_array();
        if ($cal[0]["calificacion"] == null) {
            return false;
        } else {
            return true;;
        }
    }

    public function obtener_ultimo_registro_usuario_soladm($usuario)
    {
        $this->db->select("id");
        $this->db->from($this->table_reserva);
        $this->db->order_by("id", "desc");
        $this->db->where("DATE_FORMAT(fecha_registra,'%Y-%m-%d') = DATE_FORMAT(now(),'%Y-%m-%d')");
        $this->db->where('usuario_registra', $usuario);
        $this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row();
        return $row->id;
    }
    public function guardar_comentario($comentario, $usuario_registra, $id_reserva)
    {
        $this->db->insert('comentarios_reserva', array(
            "usuario_registra" => $usuario_registra,
            "comentario" => $comentario,
            "id_reserva" => $id_reserva,
        ));
        return 1;
    }

    public function listar_comentario($id)
    {
        $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona,p.usuario,v.*", false);
        $this->db->from("comentarios_reserva v");
        $this->db->join('personas p', 'v.usuario_registra=p.id');
        $this->db->where('v.id_reserva', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function cargar_personas_reserva($dato, $tipo)
    {
        if($tipo == "Res_Nor") $query = $this->db->query("SELECT CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre,p.identificacion,p.id,p.correo FROM personas p WHERE  (CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%' OR p.correo LIKE '%" . $dato . "%')AND p.estado=1 ;");
        else $query = $this->db->query("SELECT CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre,p.identificacion,p.id,p.correo FROM visitantes p  WHERE  (CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%' OR p.correo LIKE '%" . $dato . "%') AND p.estado=1 AND (tipo = 'PerEstCUC' OR tipo = 'PerEst') GROUP BY p.id;");
        return $query->result();

    }

    public function traer_data_prueba()
    {
        $this->db->select("id,valor,id_aux");
        $this->db->from("valor_parametro");
        $this->db->where('id_aux = "Pru_Sum" OR id_aux = "Est_Pre" OR id_aux = "Ent_Sol"');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function traer_pruebas_estudiante($identificacion)
    {
        $this->db->select("*");
        $this->db->from("pruebas_estudiantes");
        $this->db->where('identificacion', $identificacion);
        $query = $this->db->get();
        return $query->result_array();
    }

}
