<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class solicitudes_adm_model extends CI_Model
{

    var $table_sol_adm = "solicitudes_adm";
    var $tabla_tiquetes_viaticos = "solic_tiquetes_viaticos";
    var $tabla_trasporte = "sol_trasnporte_buses";
    var $tabla_solic_general = "solic_general";
    var $tabla_bodega = "sol_arriendo_bodega";

    public function Listar($usuario, $estado, $tipo, $fecha)
    {

        $sw = false;
        if ($_SESSION['perfil'] != "Per_Admin" && $_SESSION['perfil'] != "Per_Admin_adm") {
            $sw = true;
        }
        $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as solicitante,v.id_tipo_solicitud tipo_gen,v.id,v.estado_solicitud estado_gen,u.valor tipo,u2.valor tipo_evento,u1.valor estado,p.id id_persona_solicita,p.correo correo_persona_solciita,v.id_tipo_evento tipo_evento_gen,v.*, u3.valor valor_clasificacion", false);
        $this->db->from("solicitudes_adm v");
        $this->db->join('valor_parametro u', 'v.id_tipo_solicitud=u.id_aux');
        $this->db->join('valor_parametro u1', 'v.estado_solicitud=u1.id_aux');
        $this->db->join('valor_parametro u2', 'v.id_tipo_evento=u2.id_aux');
        $this->db->join('valor_parametro u3', 'v.id_tipo_calificacion=u3.id_aux');
        $this->db->join('personas p', 'v.id_usuario_registra=p.id');
        $this->db->where('v.estado_registro', "1");
        $sx = true;
        if (!empty($tipo)) {
            $this->db->where('v.id_tipo_solicitud', $tipo);
            $sx = false;
        }
        if (!empty($estado)) {
            $this->db->where('v.estado_solicitud', $estado);
            $sx = false;
        }
        if (!empty($fecha)) {

            $this->db->where("DATE_FORMAT(v.fecha_inicio_evento,'%Y-%m')", $fecha);
            $sx = false;
        }

        if ($sx) {
            //$this->db->where('v.estado_solicitud !=', "Sol_Apro");

            if ($sw) {
                //$fecha = date("Y");
                //$this->db->where("DATE_FORMAT(v.fecha_inicio_evento,'%Y')", $fecha);
            } else {
                //$fecha = date("Y-m");
                $this->db->where('v.estado_solicitud = "Sol_soli" OR estado_solicitud = "Sol_Trami"');
            }

        }

        if ($sw) {
            $this->db->where('v.id_usuario_registra', $usuario);
        }
        $this->db->_protect_identifiers = false;
        $this->db->order_by("FIELD (v.estado_solicitud,'Sol_soli','Sol_Trami','Sol_Apro','Sol_Den')");
        $this->db->order_by("v.fecha_inicio_evento");
        $this->db->_protect_identifiers = true;
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Listar_solicitudes_usuario($usuario)
    {
        $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as solicitante,v.id_tipo_solicitud tipo_gen,v.id,v.estado_solicitud estado_gen,u.valor tipo,v.nombre_evento,u2.valor tipo_evento,v.fecha_inicio_evento,v.fecha_fin_evento,u1.valor estado,v.requiere_inscripcion,v.fecha_registro,p.id id_persona_solicita,p.correo correo_persona_solciita", false);
        $this->db->from("solicitudes_adm v");
        $this->db->join('valor_parametro u', 'v.id_tipo_solicitud=u.id_aux');
        $this->db->join('valor_parametro u1', 'v.estado_solicitud=u1.id_aux');
        $this->db->join('valor_parametro u2', 'v.id_tipo_evento=u2.id_aux');
        $this->db->join('personas p', 'v.id_usuario_registra=p.id');
        $this->db->where('v.estado_registro', "1");
        $this->db->where('v.id_usuario_registra', $usuario);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Listar_detalle_tiquetes_id($id)
    {
        $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona,v.fecha_registro,v.id,v.lugar_origen,v.lugar_destino,v.req_tiquete,v.fecha_salida,v.fecha_retorno,v.req_viaticos,u.valor cod_sap,v.id_solicitud,p.identificacion,v.req_seguro,v.observaciones,v.req_hotel,v.fecha_ingreso_hotel,v.fecha_salida_hotel,v.archivo_adjunto,v.archivo_visa,v.archivo_agenda,ad.estado_solicitud", false);
        $this->db->from("solic_tiquetes_viaticos v");
        $this->db->join('personas p', 'v.id_persona=p.id');
        $this->db->join('solicitudes_adm ad', 'v.id_solicitud=ad.id');
        $this->db->join('valor_parametro u', 'v.cod_sap=u.id', "left");
        $this->db->where('v.estado_registro', "1");
        $this->db->where('v.id_solicitud', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Listar_detalle_tiquetes_id_persona($id)
    {
        $this->db->select("v.id,v.lugar_origen,v.lugar_destino,v.req_tiquete,v.fecha_salida,v.fecha_retorno,v.req_viaticos,u.valor cod_sap,v.id_solicitud,v.req_seguro,v.observaciones,v.req_hotel,v.fecha_ingreso_hotel,v.fecha_salida_hotel,v.archivo_adjunto,v.archivo_visa,v.archivo_agenda");
        $this->db->from("solic_tiquetes_viaticos v");
        $this->db->join('valor_parametro u', 'v.cod_sap=u.id', "left");
        $this->db->where('v.estado_registro', "1");
        $this->db->where('v.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function Listar_detalle_tiquetes_id_persona_2($id)
    {
        $this->db->select("v.id,v.lugar_origen,v.lugar_destino,v.req_tiquete,v.fecha_salida,v.fecha_retorno,v.req_viaticos,u.valor cod_sap,v.id_solicitud,v.req_seguro,v.observaciones,v.req_hotel,v.fecha_ingreso_hotel,v.fecha_salida_hotel,v.archivo_adjunto");
        $this->db->from("solic_tiquetes_viaticos v");
        $this->db->join('valor_parametro u', 'v.cod_sap=u.id', "left");
        $this->db->where('v.estado_registro', "1");
        $this->db->where('v.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function listar_info_solicitud_tipo3($id)
    {
        $query = $this->db->query("SELECT s.id,s.id_categoria tipo_reserva_gen ,v.valor categoria,v4.valor codigo_sap,s.fecha_entrega_reserva,s.observaciones,s.columna1,s.columna2,s.columna3,s.columna4,s.columna5,s.columna6,v1.valor proveedor,v2.valor tipo_refrigerios,s.id_solicitud,v3.valor tipo_poliza,v3.id_aux tipo_poliza_general,s.tipo_refrigerios tipo_refrigerios_gen,s.proveedor proveedor_gen FROM solic_general s INNER JOIN valor_parametro v on v.id_aux = s.id_categoria LEFT JOIN valor_parametro v1 ON v1.id=s.proveedor LEFT JOIN valor_parametro v2 ON v2.id=s.tipo_refrigerios LEFT JOIN valor_parametro v3 ON s.id_tipo_poliza=v3.id_aux LEFT JOIN valor_parametro v4 ON s.codigo_sap=v4.id WHERE s.id_solicitud=$id ORDER by id DESC  LIMIT 1");
        return $query->row();
    }

    public function listar_info_solicitud_tipo4($id)
    {
        $query = $this->db->query('SELECT sb.id_tipo_mesa,sb.id_tipo_plato,sb.id_tipo_cuchara,sb.id_responsable,sb.id,sb.manteles,sb.sillas,sb.carpas,sb.vasos,sb.tenedores,sb.mesas,sb.cuchillos,sb.platos,sb.cucharas,sb.lugar_entrega,sb.fecha_entrega,sb.fecha_retiro,sb.observaciones,v.valor codigosap,v1.valor tipo_mesa,v2.valor tipo_plato,v3.valor tipo_cuchara, CONCAT(p.nombre," ",p.apellido," ",p.segundo_apellido) responsable, p.telefono,sb.valor_flores,sb.num_personas,vr.valor tipo_refrigerios,sb.cantidad_refrigerios,sb.tipo_entrega_refri,sb.tipo_entrega_cafe,sb.con_almuerzo,sb.con_video_beam,sb.con_portatil,sb.con_sonido,vc.valor id_categoria,sb.id_categoria id_categoria_gen,sb.tipo_refrigerios tipo_refrigerios_gen,sb.coctel FROM sol_arriendo_bodega sb LEFT JOIN valor_parametro v on v.id = sb.codigo_sap left JOIN valor_parametro v1 on v1.id = sb.id_tipo_mesa LEFT JOIN valor_parametro v2 on v2.id = sb.id_tipo_plato LEFT JOIN valor_parametro v3 on v3.id = sb.id_tipo_cuchara LEFT JOIN valor_parametro vr ON  sb.tipo_refrigerios=vr.id INNER JOIN personas p on p.id = sb.id_responsable INNER JOIN valor_parametro vc ON vc.id_aux = sb.id_categoria WHERE sb.id = ' . "$id" . '  ORDER by id DESC  LIMIT 1');
        return $query->row();
    }

    public function listar_info_solicitud_tipo3_id($id)
    {
        $query = $this->db->query("SELECT s.id,s.id_categoria tipo_reserva_gen ,v.valor categoria,v4.valor codigo_sap,s.fecha_entrega_reserva,s.observaciones,s.columna1,s.columna2,s.columna3,s.columna4,s.columna5,s.columna6,v1.valor proveedor,v2.valor tipo_refrigerios,s.id_solicitud,v3.valor tipo_poliza,v3.id_aux tipo_poliza_general,s.tipo_refrigerios tipo_refrigerios_gen,s.proveedor proveedor_gen FROM solic_general s INNER JOIN valor_parametro v on v.id_aux = s.id_categoria LEFT JOIN valor_parametro v1 ON v1.id=s.proveedor LEFT JOIN valor_parametro v2 ON v2.id=s.tipo_refrigerios LEFT JOIN valor_parametro v3 ON s.id_tipo_poliza=v3.id_aux LEFT JOIN valor_parametro v4 ON s.codigo_sap=v4.id WHERE s.id=$id");
        return $query->row();
    }

    public function Listar_responsables_buses_id($id)
    {
        $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,v.fecha_registro,p.identificacion,v.id id_res,t.id_solicitud,t.id idtransporte,ad.estado_solicitud,p.*", false);
        $this->db->from("responsables_buses v");
        $this->db->join('personas p', 'v.id_resposnable=p.id');
        $this->db->join('sol_trasnporte_buses t', 'v.id_sol_transporte=t.id');
        $this->db->join('solicitudes_adm ad', 't.id_solicitud=ad.id');
        $this->db->where('v.estado_registro', "1");
        $this->db->where('v.id_sol_transporte', $id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function Listar_responsables_tipo3_id($id)
    {
        $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,v.fecha_registro,p.identificacion,v.id id_res,t.id_solicitud,t.id idtransporte,ad.estado_solicitud,p.*", false);
        $this->db->from("responsables_general v");
        $this->db->join('personas p', 'v.id_responsable=p.id');
        $this->db->join('solic_general t', 'v.id_general=t.id');
        $this->db->join('solicitudes_adm ad', 't.id_solicitud=ad.id');
        $this->db->where('v.estado_registro', "1");
        $this->db->where('v.id_general', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Listar_detalle_bus_id($id)
    {
        $this->db->select("v.id,v.id_solicitud,u.valor codigo_sap,v.num_personas,v.direccion_salida,v.direccion_destino,v.hora_salida,v.hora_regreso,v.fecha_registro,v.usuario_registra,v.observaciones,ad.estado_solicitud");
        $this->db->from("sol_trasnporte_buses v");
        $this->db->join('solicitudes_adm ad', 'v.id_solicitud=ad.id');
        $this->db->join('valor_parametro u', 'v.codigo_sap=u.id', "left");
        $this->db->where('v.estado_registro', "1");
        $this->db->where('v.id_solicitud', $id);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function Listar_detalle_pedidos_id($id)
    {
        $this->db->select("v.*,ad.estado_solicitud,c.valor categoria,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as responsable,p.telefono,p.correo,u.valor sap,mes.valor tipo_mesa,pla.valor tipo_plato,cuc.valor tipo_cuchara,ref.valor tipo_refrigerios",false);
        $this->db->from("sol_arriendo_bodega v");
        $this->db->join('solicitudes_adm ad', 'v.id_solicitud=ad.id');
        $this->db->join('valor_parametro u', 'v.codigo_sap=u.id');
        $this->db->join('valor_parametro c', 'v.id_categoria=c.id_aux');

        $this->db->join('valor_parametro mes', 'v.id_tipo_mesa=mes.id','left');
        $this->db->join('valor_parametro pla', 'v.id_tipo_plato=pla.id','left');
        $this->db->join('valor_parametro cuc', 'v.id_tipo_cuchara=cuc.id','left');
        $this->db->join('valor_parametro ref', 'v.tipo_refrigerios=ref.id','left');

        $this->db->join('personas p', 'v.id_responsable=p.id');
        $this->db->where('v.estado', "1");
        $this->db->where('v.id_solicitud', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function Buscar_transporte_id($id)
    {
        $this->db->select("v.id,v.id_solicitud,u.valor codigo_sap,v.num_personas,v.direccion_salida,v.direccion_destino,v.hora_salida,v.hora_regreso,v.fecha_registro,v.usuario_registra,v.observaciones,");
        $this->db->from("sol_trasnporte_buses v");
        $this->db->join('valor_parametro u', 'v.codigo_sap=u.id', "left");
        $this->db->where('v.estado_registro', "1");
        $this->db->where('v.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function Buscar_Solicitud_id($id)
    {
        $this->db->select("v.correo_contacto,v.web_contacto,v.celular_contacto,v.telefono_contacto,v.contacto,v.descuento_inscripcion,v.valor_inscripcion,u3.valor departamento,u3.valory codigo_Sap,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as solicitante,v.id_tipo_solicitud tipo_gen,v.id,v.estado_solicitud estado_gen,u.valor tipo,v.nombre_evento,u2.valor tipo_evento,v.fecha_inicio_evento,v.fecha_fin_evento,u1.valor estado,v.requiere_inscripcion,v.fecha_registro,v.id_tipo_evento tipo_evento_gen", false);
        $this->db->from("solicitudes_adm v");
        $this->db->join('valor_parametro u', 'v.id_tipo_solicitud=u.id_aux');
        $this->db->join('valor_parametro u1', 'v.estado_solicitud=u1.id_aux');
        $this->db->join('valor_parametro u2', 'v.id_tipo_evento=u2.id_aux');
        $this->db->join('personas p', 'v.id_usuario_registra=p.id');
        $this->db->join('cargos_departamentos c', 'p.id_cargo=c.id','left');
        $this->db->join('valor_parametro u3', 'c.id_departamento=u3.id','left');
        $this->db->where('v.estado_registro', "1");
        $this->db->where('v.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function guardar($tipo_solicitud, $nombre_evento, $tipo_evento, $fecha_inicio_evento, $fecha_final_evento, $idpersona, $con_inscrip, $valor, $contacto, $descuento, $telefono, $celular, $pagina, $correo,$tipo_calificacion)
    {
        $this->db->insert($this->table_sol_adm, array(
            "id_tipo_solicitud" => $tipo_solicitud,
            "nombre_evento" => $nombre_evento,
            "id_tipo_calificacion" => $tipo_calificacion,
            "id_tipo_evento" => $tipo_evento,
            "fecha_inicio_evento" => $fecha_inicio_evento,
            "fecha_fin_evento" => $fecha_final_evento,
            "requiere_inscripcion" => $con_inscrip,
            "valor_inscripcion" => $valor,
            "descuento_inscripcion" => $descuento,
            "contacto" => $contacto,
            "celular_contacto" => $celular,
            "telefono_contacto" => $telefono,
            "web_contacto" => $pagina,
            "correo_contacto" => $correo,
            "id_usuario_registra" => $idpersona,
        ));

        return 0;
    }

    public function Guardar_Solicicitudes_tipo4($id_solicitud, $codigo_sap, $manteles, $sillas, $carpas, $vasos, $tenedores, $id_tipo_mesa, $mesas, $cuchillos, $id_tipo_plato, $platos, $id_tipo_cuchara, $cucharas, $lugar_entrega, $id_responsable, $fecha_entrega, $fecha_retiro, $observaciones, $id_usuario_registra, $num_personas, $valor_flores, $tipo_refrigerios, $cantidad_refrigerios, $tipo_entrega_refri, $tipo_entrega_cafe, $con_almuerzo, $con_video_beam, $categoria, $con_portatil, $con_sonido,$coctel,$nombre_add)
    {
        $this->db->insert($this->tabla_bodega, array(
            "id_solicitud" => $id_solicitud,
            "codigo_sap" => $codigo_sap,
            "manteles" => $manteles,
            "sillas" => $sillas,
            "carpas" => $carpas,
            "vasos" => $vasos,
            "tenedores" => $tenedores,
            "id_tipo_mesa" => $id_tipo_mesa,
            "mesas" => $mesas,
            "cuchillos" => $cuchillos,
            "id_tipo_plato" => $id_tipo_plato,
            "platos" => $platos,
            "id_tipo_cuchara" => $id_tipo_cuchara,
            "cucharas" => $cucharas,
            "lugar_entrega" => $lugar_entrega,
            "id_responsable" => $id_responsable,
            "fecha_entrega" => $fecha_entrega,
            "fecha_retiro" => $fecha_retiro,
            "observaciones" => $observaciones,
            "id_usuario_registra" => $id_usuario_registra,
            "valor_flores" => $valor_flores,
            "num_personas" => $num_personas,
            "tipo_refrigerios" => $tipo_refrigerios,
            "cantidad_refrigerios" => $cantidad_refrigerios,
            "tipo_entrega_refri" => $tipo_entrega_refri,
            "tipo_entrega_cafe" => $tipo_entrega_cafe,
            "con_almuerzo" => $con_almuerzo,
            "con_video_beam" => $con_video_beam,
            "con_portatil" => $con_portatil,
            "con_sonido" => $con_sonido,
            "id_categoria" => $categoria,
            "coctel" => $coctel,
            "adjunto" => $nombre_add,
        ));

        return 0;
    }

    public function Modificar_Solicicitudes_tipo4($id, $codigo_sap, $manteles, $sillas, $carpas, $vasos, $tenedores, $id_tipo_mesa, $mesas, $cuchillos, $id_tipo_plato, $platos, $id_tipo_cuchara, $cucharas, $lugar_entrega, $id_responsable, $fecha_entrega, $fecha_retiro, $observaciones, $num_personas, $valor_flores, $tipo_refrigerios, $cantidad_refrigerios, $tipo_entrega_refri, $tipo_entrega_cafe, $con_almuerzo, $con_video_beam, $categoria, $con_portatil, $con_sonido,$coctel,$nombre_add)
    {
        
        $this->db->set('codigo_sap', $codigo_sap);
        $this->db->set('manteles', $manteles);
        $this->db->set('sillas', $sillas);
        $this->db->set('carpas', $carpas);
        $this->db->set('vasos', $vasos);
        $this->db->set('tenedores', $tenedores);
        $this->db->set('id_tipo_mesa', $id_tipo_mesa);
        $this->db->set('mesas', $mesas);
        $this->db->set('cuchillos', $cuchillos);
        $this->db->set('id_tipo_plato', $id_tipo_plato);
        $this->db->set('platos', $platos);
        $this->db->set('id_tipo_cuchara', $id_tipo_cuchara);
        $this->db->set('cucharas', $cucharas);
        $this->db->set('lugar_entrega', $lugar_entrega);
        $this->db->set('id_responsable', $id_responsable);
        $this->db->set('fecha_entrega', $fecha_entrega);
        $this->db->set('fecha_retiro', $fecha_retiro);
        $this->db->set('observaciones', $observaciones);

        $this->db->set("valor_flores", $valor_flores);
        $this->db->set("num_personas", $num_personas);
        $this->db->set("tipo_refrigerios", $tipo_refrigerios);
        $this->db->set("cantidad_refrigerios", $cantidad_refrigerios);
        $this->db->set("tipo_entrega_refri", $tipo_entrega_refri);
        $this->db->set("tipo_entrega_cafe", $tipo_entrega_cafe);
        $this->db->set("con_almuerzo", $con_almuerzo);
        $this->db->set("con_video_beam", $con_video_beam);
        $this->db->set("con_portatil", $con_portatil);
        $this->db->set("con_sonido", $con_sonido);
        $this->db->set("id_categoria", $categoria);
        $this->db->set("coctel", $coctel);
        if(!is_null($nombre_add)) $this->db->set('adjunto', $nombre_add);
        $this->db->where('id', $id);

        $this->db->update($this->tabla_bodega);
        return 0;
    }

    public function guardar_sol_reserva($id_categoria, $codigo_sap, $fecha_entrega_reserva, $observaciones, $columna1, $columna2, $columna3, $columna4, $columna5, $columna6, $id_solicitud, $usuario_registra, $proveedor, $tipo_refrigerios, $tipo_poliza)
    {
        $this->db->insert($this->tabla_solic_general, array(
            "id_categoria" => $id_categoria,
            "codigo_sap" => $codigo_sap,
            "fecha_entrega_reserva" => $fecha_entrega_reserva,
            "observaciones" => $observaciones,
            "columna1" => $columna1,
            "columna2" => $columna2,
            "columna3" => $columna3,
            "columna4" => $columna4,
            "columna5" => $columna5,
            "columna6" => $columna6,
            "id_solicitud" => $id_solicitud,
            "usuario_registra" => $usuario_registra,
            "proveedor" => $proveedor,
            "tipo_refrigerios" => $tipo_refrigerios,
            "id_tipo_poliza" => $tipo_poliza,
        ));

        return 0;
    }

    public function modificar($id, $nombre_evento, $tipo_evento, $fecha_inicio_evento, $fecha_final_evento, $con_inscrip, $valor, $contacto, $descuento, $telefono, $celular, $pagina, $correo)
    {

        $this->db->set('nombre_evento', $nombre_evento);
        $this->db->set('id_tipo_evento', $tipo_evento);
        $this->db->set('fecha_inicio_evento', $fecha_inicio_evento);
        $this->db->set('fecha_fin_evento', $fecha_final_evento);
        $this->db->set('requiere_inscripcion', $con_inscrip);
        $this->db->set('valor_inscripcion', $valor);
        $this->db->set('descuento_inscripcion', $descuento);
        $this->db->set('contacto', $contacto);
        $this->db->set('celular_contacto', $celular);
        $this->db->set('telefono_contacto', $telefono);
        $this->db->set('web_contacto', $pagina);
        $this->db->set('correo_contacto', $correo);
        $this->db->where('id', $id);
        $this->db->update($this->table_sol_adm);

        return 0;
    }

    public function Modificar_tipo3($id, $codigo_sap, $fecha_entrega_reserva, $observaciones, $columna1, $columna2, $columna3, $columna4, $columna5, $columna6, $proveedor, $tipo_refrigerios, $tipo_poliza)
    {

        $this->db->set('codigo_sap', $codigo_sap);
        $this->db->set('fecha_entrega_reserva', $fecha_entrega_reserva);
        $this->db->set('observaciones', $observaciones);
        $this->db->set('columna1', $columna1);
        $this->db->set('columna2', $columna2);
        $this->db->set('columna3', $columna3);
        $this->db->set('columna4', $columna4);
        $this->db->set('columna5', $columna5);
        $this->db->set('columna6', $columna6);
        $this->db->set('proveedor', $proveedor);
        $this->db->set('tipo_refrigerios', $tipo_refrigerios);
        $this->db->set('id_tipo_poliza', $tipo_poliza);
        $this->db->where('id', $id);
        $this->db->update($this->tabla_solic_general);

        return 0;
    }

    public function guardar_tiquetes_viaticos($id_solicitud, $lugar_origen, $lugar_destino, $re_tiquete, $fecha_salida_tiquetes, $fecha_retorno_tiquetes, $req_viaticos, $persona, $usuario_registra, $cod_sap, $req_seguro, $observaciones, $req_hotel, $fecha_ingreso_hotel, $fecha_salida_hotel, $archivo_adjunto, $archivo_visa, $archivo_agenda)
    {
        $this->db->insert($this->tabla_tiquetes_viaticos, array(
            "id_solicitud" => $id_solicitud,
            "lugar_origen" => $lugar_origen,
            "lugar_destino" => $lugar_destino,
            "req_tiquete" => $re_tiquete,
            "fecha_salida" => $fecha_salida_tiquetes,
            "fecha_retorno" => $fecha_retorno_tiquetes,
            "req_viaticos" => $req_viaticos,
            "id_persona" => $persona,
            "usuario_registra" => $usuario_registra,
            "cod_sap" => $cod_sap,
            "req_seguro" => $req_seguro,
            "observaciones" => $observaciones,
            "req_hotel" => $req_hotel,
            "fecha_ingreso_hotel" => $fecha_ingreso_hotel,
            "fecha_salida_hotel" => $fecha_salida_hotel,
            "archivo_adjunto" => $archivo_adjunto,
            "archivo_visa" => $archivo_visa,
            "archivo_agenda" => $archivo_agenda,
        ));

        return 0;
    }

    public function modificar_tiquetes_viaticos($id, $lugar_origen, $lugar_destino, $re_tiquete, $fecha_salida, $fecha_retorno, $req_viaticos, $cod_sap, $req_seguro, $observaciones, $req_hotel, $fecha_ingreso_hotel, $fecha_salida_hotel, $archivo_pasaporte, $archivo_visa, $archivo_agenda)
    {


        $this->db->set('lugar_origen', $lugar_origen);
        $this->db->set('lugar_destino', $lugar_destino);
        $this->db->set('req_tiquete', $re_tiquete);
        $this->db->set('fecha_salida', $fecha_salida);
        $this->db->set('fecha_retorno', $fecha_retorno);
        $this->db->set('fecha_ingreso_hotel', $fecha_ingreso_hotel);
        $this->db->set('fecha_salida_hotel', $fecha_salida_hotel);
        $this->db->set('req_viaticos', $req_viaticos);
        $this->db->set('req_seguro', $req_seguro);
        $this->db->set('cod_sap', $cod_sap);
        $this->db->set('observaciones', $observaciones);
        $this->db->set('req_hotel', $req_hotel);
        $this->db->set('archivo_adjunto', $archivo_pasaporte);
        $this->db->set('archivo_visa', $archivo_visa);
        $this->db->set('archivo_agenda', $archivo_agenda);
        $this->db->where('id', $id);
        $this->db->update($this->tabla_tiquetes_viaticos);
        return 0;
    }

    public function guardar_trasporte($id_solicitud, $lugar_origen, $lugar_destino, $hora_salida, $hora_retorno, $numpersona, $usuario_registra, $codigo_sap, $observaciones)
    {
        $this->db->insert($this->tabla_trasporte, array(
            "id_solicitud" => $id_solicitud,
            "direccion_salida" => $lugar_origen,
            "direccion_destino" => $lugar_destino,
            "hora_salida" => $hora_salida,
            "hora_regreso" => $hora_retorno,
            "num_personas" => $numpersona,
            "usuario_registra" => $usuario_registra,
            "codigo_sap" => $codigo_sap,
            "observaciones" => $observaciones,
        ));

        return 0;
    }

    public function modificar_transporte($id_transprote, $lugar_origen, $lugar_destino, $hora_salida, $hora_retorno, $numpersona, $codigo_sap, $observaciones)
    {


        $this->db->set('direccion_salida', $lugar_origen);
        $this->db->set('direccion_destino', $lugar_destino);
        $this->db->set('hora_salida', $hora_salida);
        $this->db->set('hora_regreso', $hora_retorno);
        $this->db->set('num_personas', $numpersona);
        $this->db->set('codigo_sap', $codigo_sap);
        $this->db->set('observaciones', $observaciones);
        $this->db->where('id', $id_transprote);
        $this->db->update($this->tabla_trasporte);
        return 0;
    }

    public function guardar_responsable($data,$tabla)
    {
        $this->db->insert($tabla, $data);

        return 0;
    }

    public function obtener_ultimo_registro_usuario_soladm($usuario)
    {
        $this->db->select("id");
        $this->db->from("solicitudes_adm");
        $this->db->order_by("id", "desc");
        $this->db->where('id_usuario_registra', $usuario);
        $this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row();
        return $row->id;
    }

    public function obtener_ultimo_registro_usuario_buses($usuario, $soli)
    {
        $this->db->select("id");
        $this->db->from("sol_trasnporte_buses");
        $this->db->order_by("id", "desc");
        $this->db->where("id_solicitud", $soli);
        $this->db->where('usuario_registra', $usuario);
        $this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row();
        return $row->id;
    }
    public function obtener_ultimo_registro_usuario_otras($usuario, $soli)
    {
        $this->db->select("id");
        $this->db->from("solic_general");
        $this->db->order_by("id", "desc");
        $this->db->where("id_solicitud", $soli);
        $this->db->where('usuario_registra', $usuario);
        $this->db->limit(1);
        $query = $this->db->get();
        $row = $query->row();
        return $row->id;
    }

    public function persona_Tiene_Tiquetes($id, $idsol)
    {
        $this->db->select("id");
        $this->db->from($this->tabla_tiquetes_viaticos);
        $this->db->where('id_persona', $id);
        $this->db->where('id_solicitud', $idsol);
        $this->db->where('estado_registro', "1");
        $query = $this->db->get();
        $row = $query->row();
        if (empty($row)) {
            return 1;
        }
        return 2;
    }

    public function persona_Tiene_es_responsable_bus($id, $idsol_bus)
    {
        $this->db->select("id");
        $this->db->from("responsables_buses");
        $this->db->where('id_resposnable', $id);
        $this->db->where('id_sol_transporte', $idsol_bus);
        $this->db->where('estado_registro', "1");
        $query = $this->db->get();
        $row = $query->row();
        if (empty($row)) {
            return 1;
        }
        return 2;
    }
    public function persona_Tiene_es_responsable_tipo3($id, $idsol_bus)
    {
        $this->db->select("id");
        $this->db->from("responsables_general");
        $this->db->where('id_responsable', $id);
        $this->db->where('id_general', $idsol_bus);
        $this->db->where('estado_registro', "1");
        $query = $this->db->get();
        $row = $query->row();
        if (empty($row)) {
            return 1;
        }
        return 2;
    }

    public function Gestionar_solicitud($estado, $id,$mensaje)
    {
        if ($estado == 1) {
            $this->db->set('estado_solicitud', "Sol_Trami");
        } else if ($estado == 2) {
            $this->db->set('motivo_den', $mensaje);
            $this->db->set('estado_solicitud', "Sol_Apro");
        } else if ($estado == 3) {
            $this->db->set('motivo_den', $mensaje);
            $this->db->set('estado_solicitud', "Sol_Den");
        } else if ($estado == 4) {
            $this->db->set('estado_solicitud', "Sol_soli");
        } else {
            return -1;
        }


        $this->db->where('id', $id);
        $this->db->update($this->table_sol_adm);
        return 4;
    }

    public function Retirar_persona_tiquete($id, $usuario, $fecha)
    {

        $this->db->set('estado_registro', "0");
        $this->db->set('id_usuario_elimina', $usuario);
        $this->db->set('fecha_elimina', $fecha);
        $this->db->where('id', $id);
        $this->db->update($this->tabla_tiquetes_viaticos);
        return 4;
    }
    public function Retirar_solicitud_bus($id, $usuario, $fecha)
    {

        $this->db->set('estado_registro', "0");
        $this->db->set('usuario_elimina', $usuario);
        $this->db->set('fecha_elimina', $fecha);
        $this->db->where('id', $id);
        $this->db->update($this->tabla_trasporte);
        return 4;
    }
    public function Retirar_solicitud_pedido($id, $usuario, $fecha)
    {

        $this->db->set('estado', "0");
        $this->db->set('usuario_elimina', $usuario);
        $this->db->set('fecha_elimina', $fecha);
        $this->db->where('id', $id);
        $this->db->update($this->tabla_bodega);
        return 4;
    }
    public function Retirar_persona_responsable($id, $usuario, $fecha,$tipo)
    {

        $this->db->set('estado_registro', "0");
        $this->db->set('usuario_elimina', $usuario);
        $this->db->set('fecha_elimina', $fecha);
        $this->db->where('id', $id);
        if ($tipo==3) {
            $this->db->update("responsables_buses");
        }else{
            $this->db->update("responsables_general");
        }
        
        return 4;
    }

    public function guardar_datos($data,$tabla)
	{
		$this->db->insert_batch($tabla, $data);
		$error = $this->db->_error_message(); 
		if ($error) {
			return "error";
		}
		return 0;
	}

    public function cargar_select($clasificacion){
		$this->db->select("vp.valor nombre_tipo, vp.id_aux id_tipo");
		$this->db->from('permisos_parametros pp');
		$this->db->join("valor_parametro vp", "vp.id_aux = pp.vp_secundario");
		$this->db->where('pp.vp_principal', $clasificacion);
		$this->db->where('vp.idparametro', 23);
		$tipos = $this->db->get();
		return $tipos->result_array();
	}
    public function documentos_adm($clasificacion){
		$this->db->select("vp.valor nombre_tipo, vp.id_aux id_tipo");
		$this->db->from('permisos_parametros pp');
		$this->db->join("valor_parametro vp", "vp.id_aux = pp.vp_secundario");
		$this->db->where('pp.vp_principal', $clasificacion);
		$this->db->where('vp.idparametro', 340);
		$query = $this->db->get();
		return $query->row();
	}

}
