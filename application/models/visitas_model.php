<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Modelo que se encarga de manejar la informacion de las tablas visitantes, visitantes_departamento.
 */
class visitas_model extends CI_Model {

    var $table_visitas_dep = "visitas_departamento";
    var $table_visitantes= "visitantes";

/**
 * Obtiene los datos del visitante por el numero de identificacion
 * @param Integer $identificacion 
 * @return Array
 */
    public function buscar_visitante($identificacion)
    {
        $this->db->select("*,CONCAT(nombre,' ',apellido,' ',segundo_apellido) nombre_completo,IF((SELECT COUNT(s.id) sanciones FROM sanciones_visitante s WHERE s.id_visitante = visitantes.id AND s.estado = 1) > 0, 1 , 0) as sancionado",false);
        $this->db->from($this->table_visitantes);
        $this->db->where('estado', "1");
        $this->db->where('identificacion', $identificacion);
        $query = $this->db->get();
        return $query->row();
    }

    public function buscar_visitante_id($id)
    {
        $this->db->select("*");
        $this->db->from($this->table_visitantes);
        $this->db->where('estado', "1");
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }
/**
 * Muestra los visitantes por nombre o numero de identificacion
 * @param Integer $dato 
 * @return Array
 */
    public function listar_participantes($dato)
    {
        $this->db->select("*,CONCAT(nombre,' ',apellido,' ',segundo_apellido) nombre_completo",false);
        $this->db->from($this->table_visitantes);
        $this->db->where("identificacion LIKE '%$dato%' OR CONCAT(nombre,' ',apellido,' ',segundo_apellido) LIKE '%$dato%' ");
        $this->db->where('estado', "1");
        $query = $this->db->get();
        return $query->result_array();
    }

/**
 * Se encarga de guardar los datos que se le pasen por el controlador en la tabla indicada.
 * @param Array $data 
 * @param String $tabla 
 * @return Int
 */
    public function guardar_datos($data, $tabla)
    {
      $this->db->insert($tabla,$data);
      $error = $this->db->_error_message(); 
      if ($error) {
        return "error";
      }
      return 0;
    }
/**
 * Se encarga de modificar los datos que se le pasen por el controlador en la tabla indicada.
 * @param Array $data 
 * @param String $tabla 
 * @param Int $id 
 * @return Int
 */
    public function modificar_datos($data, $tabla , $id)
    {
        $this->db->where('id', $id);
        $this->db->update($tabla, $data);
      $error = $this->db->_error_message(); 
      if ($error) {
        return "error";
      }
      return 0;
    }

/**
 * Se conecta a la base de datos del software de identidades y busca lista los datos de la persona cuyo numero de identificacion es igual al que se pasa por parametro, y valida si es estudiante o es empleado
 * @param Integer $identificacion 
 * @return Array
 */
    public function is_estudiante_is_empleado($identificacion,$tipo = -1)
    {
        header("Content-Type: text/html;charset=utf-8");
        // Conectando, seleccionando la base de datos
        $link = mysqli_connect('10.2.0.61', 'ide_ca', 'CA_MySql818') or die('No se pudo conectar: ' . mysqli_error());
        mysqli_select_db($link, 'Identidades') or die('No se pudo seleccionar la base de datos');
        if (!$link->set_charset("utf8"))return '';
        // Realizar una consulta MySQL
        if ($tipo > 0)$query = "SELECT a.nombres,a.primer_apellido,a.segundo_apellido,a.num_documento,v.id_persona tipo,v.estado FROM inf_identidades a INNER JOIN inf_vinculacion v ON  v.id_usuario = a.id_usuario WHERE a.num_documento = $identificacion AND v.id_persona = $tipo";
        else $query = "SELECT a.nombres,a.primer_apellido,a.segundo_apellido,a.num_documento,v.id_persona tipo,v.estado FROM inf_identidades a INNER JOIN inf_vinculacion v ON  v.id_usuario = a.id_usuario WHERE a.num_documento = $identificacion";
        
        $result = mysqli_query($link, $query) or die('Consulta fallida: ' . mysqli_error());
        if ($line = mysqli_fetch_array($result))return $line;
        return '';
    
    }

      /**
     * Valida si existe un visitante con el numero de identificacion enviado por parametro
     * @param Integer $identificacion 
     * @return Array
     */
    public function existe_visitante($identificacion)
    {
        $this->db->select("*");
        $this->db->from($this->table_visitantes);
        $this->db->where('identificacion', $identificacion);
        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * Tra el ultimo registro ingresado por una persona en la tabla visitantes
     * @param Integer $persona 
     * @return Id
     */
    public function traer_id_ultimo_visitante($persona)
	{
		$this->db->select("*");
		$this->db->from("visitantes");
		$this->db->order_by("id", "desc");
		$this->db->where('usuario_registra', $persona);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
    }
        /**
     * Trae  los eventos dependiento del usuario que este en sesion
     *
     * @return Array
     */
    public function listar_eventos($fecha_inicio = '',$fecha_fin = '')
	{
        $sw = false;
        $sw = empty($fecha_inicio) ||empty($fecha_fin) ? false : true;
        $fecha_actual = date("Y-m-d");
        $persona = $_SESSION['persona'];
        $admin = $_SESSION['perfil'] == 'Per_Admin' || $_SESSION['perfil'] == 'Per_Admin_vis' ?  true : false;
        $this->db->select("e.*,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre_completo,CONCAT(pe.nombre,' ',pe.apellido,' ',pe.segundo_apellido) nombre_completo_elimina",false);
		$this->db->from("eventos e");
        $this->db->join('personas p', 'e.usuario_registro=p.id');
        $this->db->join('personas pe', 'e.usuario_elimina=pe.id','left');
        if(!$admin){
            if ($sw) $this->db->where("DATE_FORMAT(e.fecha_inicio,'%Y-%m-%d') BETWEEN  '$fecha_inicio' AND '$fecha_fin' AND e.usuario_registro = $persona");
            else $this->db->where("(DATE_FORMAT('$fecha_actual' ,'%Y-%m-%d') BETWEEN DATE_FORMAT(e.fecha_inicio,'%Y-%m-%d') AND DATE_FORMAT(e.fecha_fin,'%Y-%m-%d')) AND e.usuario_registro = $persona");
        }else{    
            if ($sw) $this->db->where("DATE_FORMAT(e.fecha_inicio,'%Y-%m-%d') BETWEEN  '$fecha_inicio' AND '$fecha_fin'");
            else $this->db->where("DATE_FORMAT('$fecha_actual' ,'%Y-%m-%d') BETWEEN DATE_FORMAT(e.fecha_inicio,'%Y-%m-%d') AND DATE_FORMAT(e.fecha_fin,'%Y-%m-%d')");
        }
		$this->db->order_by("e.fecha_inicio");
		$query = $this->db->get();
		return $query->result_array();
    }
            /**
     * Trae  un evento dependiento del dato que se le pase por parametro
     *
     * @return row
     */
    public function buscar_evento($buscar)
	{
        $this->db->select("*");
		$this->db->from("eventos");
		$this->db->where($buscar);
		$query = $this->db->get();
		return $query->result_array();
    }
    /**
     * Muestra el historial de ingresos por departamento o por el numero de identificacion
     * @param String $dato 
     * @param String $tipo 
     * @return Array
     */
    public function listar_ingresos_departamentos($dato, $tipo, $inicial = null ,$final = null, $formato = 0)
    {
        $this->db->select("vd.*,u.valor departamento,CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) nombre_completo,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre_completo_registra,CONCAT(p1.nombre,' ',p1.apellido,' ',p1.segundo_apellido) nombre_completo_salida,v.*",false);
        $this->db->from("visitas_departamento vd");
        $this->db->join('visitantes v', 'vd.id_visitante=v.id');
        $this->db->join('personas p', 'vd.usuario_registra=p.id');
        $this->db->join('personas p1', 'vd.usuario_marca_salida=p1.id','left');
        $this->db->join('valor_parametro u', 'vd.id_departamento=u.id');
        if ($tipo == 1) {
            $this->db->where('vd.id_departamento', $dato);
            $this->db->where("DATE_FORMAT(vd.hora_entrada,'%Y-%m-%d')", $inicial);
        }else{ 
            if (!empty($dato)) {
                $this->db->where('v.identificacion', $dato);
            }
            
            if ($tipo==2) {
                if ($formato ==0 ) {
                    $this->db->where("DATE_FORMAT(vd.hora_entrada,'%Y-%m-%d')", $inicial);
                }else{
                    $this->db->where("DATE_FORMAT(vd.hora_entrada,'%Y-%m-%d %H:%i') = DATE_FORMAT('$inicial','%Y-%m-%d %H:%i')");
                }
            }else if ($tipo==3) {
                if ($formato ==0 ) {
                    $this->db->where("DATE_FORMAT(vd.hora_entrada,'%Y-%m-%d') BETWEEN DATE_FORMAT('$inicial','%Y-%m-%d')  AND DATE_FORMAT('$final','%Y-%m-%d') ");
                }else{
                    $this->db->where("DATE_FORMAT(vd.hora_entrada,'%Y-%m-%d %H:%i') BETWEEN DATE_FORMAT('$inicial','%Y-%m-%d %H:%i')  AND DATE_FORMAT('$final','%Y-%m-%d %H:%i') ");
                }
            }
        
        }
        $this->db->order_by("vd.hora_entrada", "desc");
        $query = $this->db->get();
        return $query->result_array();
    }

       /**
     * Traer el ultimo ingreso de un visitante en la tabla visitantes_departamentos
     * @param Integer $identificacion 
     * @return Id
     */
    public function traer_ultimo_ingreso_visitante($identificacion)
	{
		$query =$this->db->query("SELECT CONCAT(vt.nombre,' ',vt.apellido,' ',vt.segundo_apellido) nombre_completo,vt.identificacion,vt.foto,vd.*,p.valorx,p.valory,p.valor,p.idparametro FROM visitas_departamento vd INNER JOIN visitantes vt on vt.id = vd.id_visitante INNER JOIN valor_parametro p on p.id=vd.id_departamento WHERE vd.id_visitante = (SELECT v.id FROM visitantes v WHERE v.identificacion = $identificacion)  AND DATE_FORMAT(vd.hora_entrada,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') ORDER BY vd.id DESC LIMIT 1");
		$row = $query->row();
		return $row;
    }
        /**
     * verifica si existe un participante en un evento
     * @param Integer $id_evento
     * @param Integer $id_persona 
     * @return Id
     */
    public function verificar_ingreso_evento($id_evento,$id_persona)
	{
		$this->db->select("*");
		$this->db->from("participantes_evento");
		$this->db->where('id_evento', $id_evento);
        $this->db->where('id_persona', $id_persona);
        $this->db->where('estado', '1');
		$this->db->order_by("id", "desc");
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
    }

          /**
     * verifica si existe un participante en un evento
     * @param Integer $id_evento
     * @param Integer $id_persona 
     * @return Id
     */
    public function buscar_ingreso_evento_id($id)
	{
        $this->db->select("pv.*,v.correo,CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) nombre_completo,ev.firma,ev.fecha_inicio fecha_inicio_evento,ev.fecha_fin fecha_fin_evento,ev.usuario_elimina usuario_elimina_evento", false);
        $this->db->from("participantes_evento pv");
        $this->db->join('visitantes v', 'pv.id_persona=v.id');
        $this->db->join('eventos ev', 'pv.id_evento=ev.id');
		$this->db->where('pv.id', $id);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }
/**
 * Muestra los participantes registrado a un evento
 * @return Array
 */
    public function listar_participantes_en_evento($id_evento = '', $buscar = '')
    {
        $this->db->select("pv.*,ev.firma,ev.nombre nombre_evento,phj.valor programa_hijo,hj.identificacion identificacion_hijo,CONCAT(hj.nombre,' ',hj.apellido,' ',hj.segundo_apellido) nombre_completo_hijo,CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) nombre_completo,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre_completo_registra,v.identificacion,ev.fecha_inicio fecha_inicio_evento,ev.fecha_fin fecha_fin_evento,ev.usuario_elimina usuario_elimina_evento,v.foto,,CONCAT(pe.nombre,' ',pe.apellido,' ',pe.segundo_apellido) nombre_completo_entrada,,CONCAT(ps.nombre,' ',ps.apellido,' ',ps.segundo_apellido) nombre_completo_salida,vp.valor tipo,v.tipo tipo_persona,v.correo,v.celular,vpt.valor programa",false);
        $this->db->from("participantes_evento pv");
        $this->db->join('eventos ev', 'pv.id_evento=ev.id');
        $this->db->join('visitantes v', 'pv.id_persona=v.id');
        $this->db->join('visitantes hj', 'pv.id_hijo=hj.id', 'left');
        $this->db->join('valor_parametro phj', 'hj.id_programa=phj.id','left');
        $this->db->join('valor_parametro vpt', 'v.id_programa=vpt.id','left');
        $this->db->join('personas p', 'pv.usuario_registra=p.id');
        $this->db->join('personas ps', 'pv.usuario_marca_salida=ps.id','left');
        $this->db->join('personas pe', 'pv.usuario_marca_entrada=pe.id','left');
        $this->db->join('valor_parametro vp', 'pv.id_tipo=vp.id');
        $this->db->where('pv.estado', '1');
        if(!empty($id_evento))$this->db->where('pv.id_evento', $id_evento);
        else if(!empty($buscar)) $this->db->where("v.identificacion LIKE '%$buscar%' OR CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) LIKE '%$buscar%' ");
        $this->db->order_by("ev.nombre");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function traer_id_ultimo_evento($persona)
	{
		$this->db->select("*");
		$this->db->from("eventos");
		$this->db->order_by("id", "desc");
		$this->db->where('usuario_registro', $persona);
		$this->db->limit(1);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
    }

    public function listar_visitantes($dato){
        $this->db->select("v.*,CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) as nombre_completo,ti.valor tipo,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre_completo_registra,IF((SELECT COUNT(s.id) sanciones FROM sanciones_visitante s WHERE s.id_visitante = v.id AND s.estado = 1) > 0, 'Sancionado', 'Activo') as estado,v.tipo as tipo_persona", false);
        $this->db->from('visitantes v');
        $this->db->join('personas p', 'v.usuario_registra = p.id');
        $this->db->join('valor_parametro ti', 'v.tipo_identificacion = ti.id');
        $this->db->where("(CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) LIKE '%" . $dato . "%' OR v.identificacion LIKE '%" . $dato . "%')");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function obtener_ingresos_visitante($id)
    {
        $this->db->select("vd.*,u.valor departamento,CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) nombre_completo,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) nombre_completo_registra,CONCAT(p1.nombre,' ',p1.apellido,' ',p1.segundo_apellido) nombre_completo_salida,v.*",false);
        $this->db->from("visitas_departamento vd");
        $this->db->join('visitantes v', 'vd.id_visitante=v.id');
        $this->db->join('personas p', 'vd.usuario_registra=p.id');
        $this->db->join('personas p1', 'vd.usuario_marca_salida=p1.id','left');
        $this->db->join('valor_parametro u', 'vd.id_departamento=u.id');
        $this->db->where('vd.id_visitante', $id);
        $this->db->order_by("vd.hora_entrada", "desc");
        $query = $this->db->get();
        return $query->result_array();
    }
    public function obtener_sanciones_visitante($id)
    {
        $this->db->select("vd.*,CONCAT(pr.nombre,' ',pr.apellido,' ',pr.segundo_apellido) sancionado_por,CONCAT(pe.nombre,' ',pe.apellido,' ',pe.segundo_apellido) retirado_por",false);
        $this->db->from("sanciones_visitante vd");
        $this->db->join('personas pr', 'vd.id_usuario_registra = pr.id');
        $this->db->join('personas pe', 'vd.id_usuario_elimina = pe.id','left');
        $this->db->where('vd.id_visitante', $id);
        $this->db->where('vd.estado', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function obtener_hijos($id)
    {
        $this->db->select("v.id,CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) valor,",false);
        $this->db->from("visitantes v");
        $this->db->where('v.id_padre', $id);
        $this->db->where('v.estado', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
/**
 * Muestra los participantes registrado a un evento
 * @return Array
 */
public function listar_participantes_exporte($id_evento)
{

    $this->db->select("vp.valor tipo,CONCAT(v.nombre,' ',v.apellido,' ',v.segundo_apellido) nombre_completo,v.identificacion,pv.fecha_entrada_evento fecha_ingreso,pv.fecha_salida_evento fecha_salida,v.correo,vpt.valor programa,v.celular,CONCAT(hj.nombre,' ',hj.apellido,' ',hj.segundo_apellido) nombre_hijo,hj.identificacion identificacion_hijo,phj.valor programa_hijo,hj.correo correo_hijo",false);
    $this->db->from("participantes_evento pv");
    $this->db->join('visitantes v', 'pv.id_persona=v.id');
    $this->db->join('visitantes hj', 'pv.id_hijo=hj.id', 'left');
    $this->db->join('valor_parametro phj', 'hj.id_programa=phj.id','left');
    $this->db->join('valor_parametro vpt', 'v.id_programa=vpt.id','left');
    $this->db->join('valor_parametro vp', 'pv.id_tipo=vp.id');
    $this->db->where('pv.estado', '1');
    $this->db->where('pv.id_evento', $id_evento);
    $this->db->order_by("pv.id");
    $query = $this->db->get();
    return $query->result_array();
}
}
