<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class personas_model extends CI_Model
{

    //Tabla a la cual se conecta el modelo
    var $table_persona = "personas";
    //Tabla de perfiles por persona
    var $table_actividades_personas = "actividades_personas";
    //datos que trae de la tabla cuando realiza una consulta
    var $select_column = array("id", "nombre", "segundo_nombre", "apellido", "segundo_apellido", "identificacion", "correo", "telefono", "id_tipo_identificacion", "id_cargo", "foto", "usuario", "id_perfil", "id_tipo_persona");

    /**
     * Realiza una consulta general la tabla personas y Lista todos los datos
     * @return Array
     */
    public function make_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table_persona);
    }

    /**
     * Realiza una consulta a la tabla persona y lista aquellas personas que se encuentran en estado activo
     * @return Array
     */


    public function Listar($dato, $id_tipo_cargo, $id_tipo_persona, $id_tipo_contrato, $id_tipo_perfil, $fecha_inicial, $fecha_final)
    {

        $this->db->select("u2.valor id_tipo_identificacion,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,p.identificacion,p.telefono,p.correo,DATE_FORMAT(p.fecha_registra, '%d-%m-%Y')fecha_registra,p.tipo_contrato,p.id,p.usuario,u4.valor id_perfil,u5.valor tipo_persona, p.estado estado, v.valor cargo_sap", false);
        $this->db->from('personas p');
        $this->db->group_by('p.id');
        $this->db->join('valor_parametro v', 'p.id_cargo_sap=v.id', 'left');
        $this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
        $this->db->join('valor_parametro u5', 'p.id_tipo_persona=u5.id_aux');
        $this->db->join('valor_parametro u4', 'p.id_perfil = u4.id_aux', "left");

        if ($dato) $this->db->where("(CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%' OR p.correo LIKE '%" . $dato . "%' OR u4.valor LIKE '%" . $dato . "%')");
        if ($id_tipo_persona) $this->db->where("p.id_tipo_persona", $id_tipo_persona);
        if ($id_tipo_cargo) $this->db->where("p.id_cargo_sap", $id_tipo_cargo);
        if ($id_tipo_contrato) $this->db->where("p.tipo_contrato", $id_tipo_contrato);
        if ($id_tipo_perfil) $this->db->where("p.id_perfil", $id_tipo_perfil);
        if ($fecha_inicial && $fecha_final) $this->db->where("(DATE_FORMAT(p.fecha_registra,'%Y-%m-%d') >= DATE_FORMAT('$fecha_inicial','%Y-%m-%d') AND DATE_FORMAT(p.fecha_registra,'%Y-%m-%d') <= DATE_FORMAT('$fecha_final','%Y-%m-%d'))");

        $query = $this->db->get();
        return $query->result_array();
    }
    /**
     * Lista las personas cuyo nombres, apellidos o identificacion contengan el dato a buscar
     * @param String $dato
     * @return Array
     */
    public function Listar_dato($dato)
    {
        $query = $this->db->query("SELECT CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre,p.identificacion,p.id,p.correo FROM personas p WHERE  (CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) LIKE '%" . $dato . "%' OR p.identificacion LIKE '%" . $dato . "%' OR p.correo LIKE '%" . $dato . "%')AND p.estado=1 ;");
        return $query->result();
    }

    /**
     * Lista las personas que se encuentran activas por departamento
     * @param Integer $id
     * @return Array
     */
    public function Listar_por_departamento($id)
    {
        $this->db->select("*");
        $this->db->from("personas p");

        $this->db->join('cargos_departamentos c', 'p.id_cargo=c.id');
        $this->db->where('c.id_departamento', $id);
        $this->db->where('p.estado', "1");
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Lista los datos de las persoans que se encuentran en el area de audiovisuales
     * @return Array
     */
    public function Listar_por_departamento_audiovisual()
    {
        $this->db->select("p.id,p.nombre,p.apellido,p.segundo_apellido");
        $this->db->from("personas p");
        // $this->db->join('cargos_departamentos c', 'p.id_cargo=c.id');
        // $this->db->join('valor_parametro u', 'c.id_cargo=u.id');
        $this->db->join('valor_parametro u', 'u.id = p.id_cargo_sap');
        // $this->db->join('valor_parametro u1', 'c.id_departamento=u1.id');
        $this->db->where('p.estado', "1");
        // $this->db->where('u.id_aux', "aux_aud");
        $this->db->like('u.valor', "AUX 1 DE DPT TECNOLOGÍA");
        $this->db->where('p.id_perfil', "Admin_Aud");
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Lista los datos de las personas que tienen asignado un perfil y la actividad pasada por parametro.
     * @return Array
     */
    public function Listar_personas_por_perfil($perfil = null, $actividad = 'comite')
    {
        $this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona,p.correo,u1.valor departamento", false);
        $this->db->from("personas p");
        $this->db->join('actividades_personas ap', "ap.id_persona = p.id AND ap.id_actividad ='$actividad'");
        $this->db->join('cargos_departamentos c', 'p.id_cargo=c.id', 'left');
        $this->db->join('valor_parametro u1', 'c.id_departamento=u1.id', 'left');
        if (!is_null($perfil)) $this->db->where('p.id_perfil', $perfil);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtiene los datos de la persona, la consulta se realiza por el id de la persona
     * @param Integer $id
     * @return Array
     */
    public function obtener_Datos_persona($id)
    {
        $this->db->select("p.nombre,p.apellido,p.segundo_nombre,p.segundo_apellido,p.identificacion,p.telefono,p.correo,p.foto,p.id,p.usuario,p.id_tipo_persona,p.id_tipo_identificacion,p.id_perfil,v.id,v.valor,p.id_cargo_sap,p.estado,p.sueldo,p.fecha_inicio_contrato,p.tipo_contrato");

        $this->db->from("personas p");
        $this->db->join('valor_parametro v', 'v.id=p.id_cargo_sap', 'left');

        $this->db->where('p.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtiene los datos de la persona cuyo identificacion y tipo de identificacion coinciden con lo que se envia por parametro
     * @param Integer $identificacion
     * @param String $tipoidentificacion
     * @return Array
     */
    public function obtener_Datos_persona_identificacion($identificacion, $tipoidentificacion)
    {
        $this->db->select("u2.valor id_tipo_identificacion,u.valor id_cargo,u1.valor id_departamento,u1.valorx ubicacion,p.nombre,p.apellido,p.segundo_nombre,p.segundo_apellido,p.identificacion,p.telefono,p.correo,p.foto,p.id");
        $this->db->from('personas p');
        $this->db->join('cargos_departamentos c', 'p.id_cargo=c.id', "left");
        $this->db->join('valor_parametro u', 'c.id_cargo=u.id', "left");
        $this->db->join('valor_parametro u1', 'c.id_departamento=u1.id', "left");
        $this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
        $this->db->where('p.estado', "1");
        $this->db->where('p.identificacion', $identificacion);
        $this->db->where('p.id_tipo_identificacion', $tipoidentificacion);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtiene los datos completos  de la personas cuyo id coincide con el enviado por parametro y se encuentra activa
     * @param Integer $id
     * @return Array
     */
    public function obtener_Datos_persona_id_completos($id)
    {
        $this->db->select("
        u1.valor id_departamento,
        u2.valor id_tipo_identificacion,
        p.nombre,
        p.apellido,
        p.segundo_nombre,
        p.segundo_apellido,
        p.identificacion,
        p.telefono,
        p.correo_personal,
        p.barrio,
        p.lugar_residencia,
        p.foto,
        p.id,
        p.direccion,
        p.usuario,
        u4.valor id_perfil,
        u5.valor tipo_persona,
        u6.valor cargo");
        $this->db->from('personas p');
        $this->db->join('cargos_departamentos c', 'p.id_cargo_sap=c.id','left');
        $this->db->join('valor_parametro u1', 'c.id_departamento=u1.id','left');
        $this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
        $this->db->join('valor_parametro u4', 'p.id_perfil=u4.id_aux', "left");
        $this->db->join('valor_parametro u5', 'p.id_tipo_persona=u5.id_aux');
        $this->db->join('valor_parametro u6', 'p.id_cargo_sap =u6.id');

        $this->db->where('p.estado', "1");
        $this->db->where('p.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * agrega un nuevo registro a la tabla persona
     * @param Integer $identificacion
     * @param String $tipo_identificacion
     * @param String $nombre
     * @param String $apellido
     * @param Integer $celular
     * @param String $correo
     * @param String $imagen
     * @param Integer $cargo
     * @param Integer $departamento
     * @param String $segundoapellido
     * @param String $segundonombre
     * @param String $usuario
     * @param String $contrasena
     * @param String $tipo_persona
     * @return Integer
     */
    public function guardar($identificacion, $tipo_identificacion, $nombre, $apellido, $celular, $correo, $imagen, $segundoapellido, $segundonombre, $usuario, $contrasena, $tipo_persona, $usuario_registra, $id_perfil, $id_cargo_sap, $fecha, $sueldo, $tipo_contrato)
    {

        $this->db->insert($this->table_persona, array(
            "identificacion" => $identificacion,
            "nombre" => $nombre,
            "segundo_nombre" => $segundonombre,
            "apellido" => $apellido,
            "segundo_apellido" => $segundoapellido,
            "correo" => $correo,
            "telefono" => $celular,
            "id_tipo_identificacion" => $tipo_identificacion,
            "foto" => $imagen,
            "usuario" => $usuario,
            "contrasena" => $contrasena,
            "id_tipo_persona" => $tipo_persona,
            "usuario_registra" => $usuario_registra,
            "id_perfil" => $id_perfil,
            "id_cargo_sap" => $id_cargo_sap,
            "fecha_inicio_contrato" => $fecha,
            "sueldo" => $sueldo,
            "tipo_contrato" => $tipo_contrato,

        ));

        return 4;
    }

    /**
     * Valida si existe una persona con el numero de identificacion enviado por parametro
     * @param Integer $identificacion
     * @return Array
     */
    public function Existe_Identificacion($identificacion)
    {
        $this->make_query();
        $this->db->where('identificacion', $identificacion);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Valida si existe una persona con el usuario enviado por parametro
     * @param String $usuario
     * @return Array
     */
    public function Existe_usuario($usuario)
    {
        $this->make_query();
        $this->db->where('usuario', $usuario);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Valida si existe una persona con el correo enviado por parametro
     * @param String $usuario
     * @return Array
     */
    public function Existe_correo($correo)
    {
        $this->make_query();
        $this->db->where('correo', $correo);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Des habilita la persona cuyo id coincide con el enviado por parametro
     * @param Integer $id
     * @return Integer
     */
    public function Eliminar_Persona($id, $usuario, $fecha, $nuevo_estado)
    {
        $this->db->set('usuario_elimina	', $usuario);
        $this->db->set('fecha_elimina', $fecha);
        $this->db->set('estado', $nuevo_estado);
        $this->db->where('id', $id);
        $this->db->update($this->table_persona);
        return 1;
    }

    /**
     * 
     * Valida si existe una persona con el id pasado por parametro
     * @param Integer $id
     * @return Array
     */
    public function existe_Persona_id($id)
    {

        $this->make_query();
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Modifica los datos de la persona cuyo id coincide con el dato que se pasa por parametro
     * @param Integer $id
     * @param Integer $identificacion
     * @param String $tipo_identificacion
     * @param String $nombre
     * @param String $apellido
     * @param Integer $celular
     * @param String $correo
     * @param String $imagen
     * @param Integer $cargo
     * @param Integer $departamento
     * @param String $segundoapellido
     * @param String $segundonombre
     * @param String $usuario
     * @param String $perfil
     * @param String $tipo_persona
     * @param String $Id_cargo_sap
     * @param String $fecha
     * @param Integer $sueldo
     * @param String $tipo_contrato
     * @return Integer
     */
    public function Modificar_Persona($id, $identificacion, $tipo_identificacion, $nombre, $apellido, $celular, $correo, $segundoapellido, $segundonombre, $usuario, $perfil, $tipo_persona,  $id_cargo_sap, $fecha, $sueldo, $tipo_contrato, $imagen)
    {
        if (!is_null($perfil)) $this->db->set('id_perfil', $perfil);
        $this->db->set('usuario', $usuario);
        $this->db->set('identificacion', $identificacion);
        $this->db->set('nombre', $nombre);
        $this->db->set('segundo_nombre', $segundonombre);
        $this->db->set('apellido', $apellido);
        $this->db->set('segundo_apellido', $segundoapellido);
        $this->db->set('correo', $correo);
        $this->db->set('telefono', $celular);
        $this->db->set('id_tipo_identificacion', $tipo_identificacion);
        $this->db->set('id_tipo_persona', $tipo_persona);
        $this->db->set('id_cargo_sap', $id_cargo_sap);
        $this->db->set('fecha_inicio_contrato', $fecha);
        $this->db->set('sueldo', $sueldo);
        $this->db->set('tipo_contrato', $tipo_contrato);

        $this->db->where('id', $id);
        $this->db->update($this->table_persona);
        return 4;
    }

    /**
     * Lista la persona cuyo usuario y contraseña coinciden con los datos pasados por parametros
     * @param String $usuario
     * @param String $contrasena
     * @return Array
     */
    public function Listar_usuario_contrasena($usuario, $contrasena)
    {
        $this->db->select("u2.valor id_tipo_identificacion,p.nombre,p.apellido,p.segundo_nombre,p.segundo_apellido,p.identificacion,p.telefono,p.correo,p.foto,p.id,p.usuario,p.acept_politicas,p.id_perfil");
        $this->db->from('personas p');
        $this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
        $this->db->where('p.estado', "1");
        $this->db->where('p.usuario', $usuario);
        $this->db->where('p.contrasena', $contrasena);
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Lista los datos de la persona cuyo usuario coincide con el que se pasa por parametro
     * @param String $usuario
     * @return Array
     */
    public function Listar_solo_usuario($usuario)
    {
        $this->db->select("u2.valor id_tipo_identificacion,p.nombre,p.apellido,p.segundo_nombre,p.segundo_apellido,p.identificacion,p.telefono,p.correo,p.foto,p.id,p.usuario,p.acept_politicas,p.id_perfil");
        $this->db->from('personas p');
        $this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
        $this->db->where('p.estado', "1");
        $this->db->where('p.usuario', $usuario);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Se modifica el campo acept_politicas de la persona cuyo id coincide con el que se pasa por parametro
     * @param Integer $idpersona
     * @return Integer
     */
    public function Aceptar_Politicas($idpersona)
    {
        $this->db->set('acept_politicas', "1");
        $this->db->where('id', $idpersona);
        $this->db->update($this->table_persona);
        return 4;
    }

    /**
     * Asigna un perfil a la persona cuyo id coincide con el pasado por parametro
     * @param String $idperfil
     * @param Integer $id
     * @return Integer
     */
    public function Asignar_Perfil($idperfil, $id)
    {
        $this->db->insert($this->table_actividades_personas, array(
            "id_persona" => $id,
            "id_actividad" => $idperfil
        ));
        return 4;
    }

    /**
     * Valida si la persona cuyo id es igual al que se pasa por parametro tiene un perfil asignado
     * @param Integer $id
     * @return String
     */
    public function Tiene_Perfil($id)
    {
        $this->db->select("id_perfil");
        $this->db->from($this->table_persona);
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();
        return $row->id_perfil;
    }

    
    /**  
     * Se conecta a la base de datos del software de identidades y busca lista los datos de la persona cuyo numero de identificacion es igual al que se pasa por parametro
     * @param Integer $identificacion
     * @return Array
     */
    public function Traer_Persona_Identidades($identificacion)
    {
        header("Content-Type: text/html;charset=utf-8");
        // Conectando, seleccionando la base de datos
        $link = mysqli_connect('10.2.0.61', 'ide_ca', 'CA_MySql818') or die('No se pudo conectar: ' . mysqli_error());
        mysqli_select_db($link, 'Identidades') or die('No se pudo seleccionar la base de datos');
        if (!$link->set_charset("utf8")) return '';
        // Realizar una consulta MySQL
        $query = "SELECT a.nombres,a.primer_apellido,a.segundo_apellido,a.logon_name,a.num_documento,a.celular FROM inf_identidades a WHERE a.num_documento = " . $identificacion . ";";

        $result = mysqli_query($link, $query) or die('Consulta fallida: ' . mysqli_error());
        if ($line = mysqli_fetch_array($result)) {
            return $line;
        }
        return -1;

	}

	public function Cargar_perfiles_persona($id){
		    $this->db->select("pp.id, per.valor perfil,pp.id_actividad");
        $this->db->from('actividades_personas pp');
        $this->db->join('valor_parametro per', 'per.id_aux = pp.id_actividad');
        $this->db->where('pp.id_persona', $id);
        $this->db->where('per.estado', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function eliminar_perfil_persona($id)
    {
        $this->db->delete($this->table_actividades_personas, array('id' => $id));
        return 1;
    }

    public function Existe_perfil($persona, $perfil)
    {
        $this->db->select('count(id) as existe');
        $this->db->from($this->table_actividades_personas);
        $this->db->where('id_persona', $persona);
        $this->db->where('id_actividad', $perfil);
        $query = $this->db->get();
        $row = $query->row();
        $existe = $row->existe > 0 ? true : false;
        return $existe;
    }

    public function Cargar_perfiles_faltantes($id)
    {
        $query = $this->db->query("(SELECT vp.valor as nombre, vp.id_aux as codigo, pp.id FROM valor_parametro vp LEFT JOIN actividades_personas pp ON pp.id_persona = $id AND pp.id_actividad = vp.id_aux WHERE vp.idparametro = 18 AND pp.id IS NULL GROUP BY vp.id_aux)", false);
        return $query->result_array();
    }

    public function traer_correos_perfil($perfil)
    {
        $this->db->select("p.correo");
        $this->db->from("personas p");
        $this->db->where('p.id_perfil', $perfil);
        $query = $this->db->get();
        return $query->result_array();
    }


    public function buscar_persona_where($where){
        $this->db->select("u2.valor id_tipo_identificacion,u.valor id_cargo,u1.valor id_departamento,u1.valorx ubicacion,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,p.identificacion,p.telefono,p.correo,p.foto,p.id as id_persona,p.usuario,u4.valor id_perfil,u5.valor tipo_persona,p.nombre", false);
        $this->db->from('personas p');
        $this->db->join('cargos_departamentos c', 'p.id_cargo=c.id', 'left');
        $this->db->join('valor_parametro u', 'c.id_cargo=u.id', 'left');
        $this->db->join('valor_parametro u1', 'c.id_departamento=u1.id', 'left');
        $this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
        $this->db->join('valor_parametro u5', 'p.id_tipo_persona=u5.id_aux');
        $this->db->join('valor_parametro u4', 'p.id_perfil = u4.id_aux', "left");
        $this->db->where("$where AND p.estado=1");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }


    public function buscar_cargos_sap($buscar)
    {
        $this->db->select("vp.idparametro, vp.valor nombre_cargo, vp.id", false);
        $this->db->from('valor_parametro vp');
        $this->db->where("vp.idparametro", 188);
        //$this->db->where("valory", 2);
        $this->db->like("vp.valor", $buscar);
        $this->db->order_by("vp.valor", 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function actualizar_perfil($id, $identificacion, $perfil)
    {
        $this->db->set('id_perfil', $perfil);

        $this->db->where('id', $id);
        $this->db->update($this->table_persona);
        return 4;
    }


    
    public function Existe_perfil_persona($persona, $perfil)
    {
        $this->db->select("count(id) AS existe FROM perfiles_personas WHERE id_persona = '$persona' AND id_perfil = '$perfil'");
        $query = $this->db->get();
        $row = $query->row();
        $existe = $row->existe > 0 ? true : false;
        return $existe;
    }

    public function Asignar_Perfiles_usuario($id, $idperfil)
    {
        $this->db->insert('perfiles_personas', array(
            "id_persona" => $id,
            "id_perfil" => $idperfil
        ));
        return 4;
    }

    public function traerPerfilesPersona($id)
    {

        $this->db->select("pp.id, per.valor perfil, pp.id_perfil, id_persona");
        $this->db->from('perfiles_personas pp');
        $this->db->join('valor_parametro per', 'per.id_aux = pp.id_perfil');
        $this->db->where('pp.id_persona', $id);
        $this->db->where('per.estado', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function eliminar_perfil($id)
    {
        $this->db->delete('perfiles_personas', array('id' => $id));
        return 1;
    }

    public function buscarPredeterminado($id)
    {
        $this->db->select("per.valor perfil, p.id_perfil");
        $this->db->from('personas p');
        $this->db->join('valor_parametro per', 'per.id_aux = p.id_perfil');
        $this->db->where('p.id', $id);
        $this->db->where('per.estado', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function perfiles_faltantes($id)
    {
        $query = $this->db->query("(SELECT vp.valor as nombre, vp.id_aux as codigo, pp.id FROM valor_parametro vp LEFT JOIN perfiles_personas pp ON pp.id_persona = $id AND pp.id_perfil = vp.id_aux WHERE vp.idparametro = 17 AND pp.id IS NULL GROUP BY vp.id_aux)", false);
        return $query->result_array();
    }

    public function perfiles_usuario($id)
    {
        $this->db->select("pp.id, per.valor perfil, pp.id_perfil");
        $this->db->from('perfiles_personas pp');
        $this->db->join('valor_parametro per', 'per.id_aux = pp.id_perfil');
        $this->db->where('pp.id_persona', $id);
        $this->db->where('per.estado', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function obtener_datos_usuario($id)
    {
        $this->db->select("p.id, p.identificacion");
        $this->db->from("personas p");
        $this->db->join('valor_parametro per', 'per.id_aux = p.id_perfil');
        $this->db->where('p.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function datos_persona($identificacion)
    {
        $this->db->select("p.id, p.id_perfil");
        $this->db->from("personas p");
        $this->db->join('valor_parametro per', 'per.id_aux = p.id_perfil');
        $this->db->where('p.identificacion', $identificacion);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function traerTipoId()
    {
        $this->db->select("vp.idparametro, vp.valor tipoIdentificacion, vp.id", false);
        $this->db->from('valor_parametro vp');
        $this->db->where("vp.idparametro", 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function traer_cargos()
    {
        $this->db->select("vp.idparametro, vp.valor nombre_cargo , vp.id", false);
        $this->db->from('valor_parametro vp');
        $this->db->where("vp.idparametro", 188);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function nuevo_cargo($cargo)
    {
        if ($cargo) {
            $this->db->insert('valor_parametro', array(
                "valor" => $cargo,
                "idparametro" => 188
            ));
        }
        
        return 4;
    }



    public function guardarExcel($identificacion, $tipo_identificacion, $nombre, $apellido, $segundoapellido, $segundonombre, $contrasena, $tipo_persona, $usuario_registra, $id_perfil, $id_cargo_sap, $sueldo, $tipo_contrato, $fecha)
    {

        $this->db->insert($this->table_persona, array(
            "identificacion" => $identificacion,
            "nombre" => $nombre,
            "segundo_nombre" => $segundonombre,
            "apellido" => $apellido,
            "segundo_apellido" => $segundoapellido,
            "id_tipo_identificacion" => $tipo_identificacion,
            "contrasena" => $contrasena,
            "id_tipo_persona" => $tipo_persona,
            "usuario_registra" => $usuario_registra,
            "id_perfil" => $id_perfil,
            "id_cargo_sap" => $id_cargo_sap,
            "sueldo" => $sueldo,
            "tipo_contrato" => $tipo_contrato,
            "fecha_inicio_contrato" => $fecha,
            // "correo" => $correo,

        ));
        $error = $this->db->_error_message(); 
		if ($error) {
			return $this->db->_error_message();
		}else{
            return 4;
        }
    }
    
    public function Modificar_Datos_Excel($array)
    {
        $identificacion = $array['identificacion'];
        $tipo_identificacion = !empty($array['id_tipoID']) ? $array['id_tipoID'] : false;
        $nombre = !empty($array['PrimerNombre']) ? $array['PrimerNombre'] : false; 
        $apellido = !empty($array['PrimerApellido']) ? $array['PrimerApellido'] : false;  
        $segundoapellido = !empty($array['SegundoApellido']) ? $array['SegundoApellido'] : false; 
        $segundonombre = !empty($array['SegundoNombre']) ? $array['SegundoNombre'] : false; 
        $id_cargo_sap = !empty($array['id_cargo']) ? $array['id_cargo'] : false; 
        $sueldo = !empty($array['Importe']) ? $array['Importe'] : false ; 
        $tipo_contrato = !empty($array['claseContrato']) ? $array['claseContrato'] : false ;
        $fecha = !empty($array['Fecha']) ? $array['Fecha'] : false ;
        // $correo = !empty($array['correo']) ? $array['correo'] : false ;
        

        if ($nombre) $this->db->set('nombre', $nombre);
        if ($segundonombre) $this->db->set('segundo_nombre', $segundonombre);
        if ($apellido) $this->db->set('apellido', $apellido);
        if ($segundoapellido) $this->db->set('segundo_apellido', $segundoapellido);
        if ($tipo_identificacion) $this->db->set('id_tipo_identificacion', $tipo_identificacion);
        if ($id_cargo_sap) $this->db->set('id_cargo_sap', $id_cargo_sap);
        if ($fecha ) $this->db->set('fecha_inicio_contrato', $fecha);
        if ($sueldo ) $this->db->set('sueldo', $sueldo);
        if ($tipo_contrato) $this->db->set('tipo_contrato', $tipo_contrato);
        // if ($correo) $this->db->set('correo', $correo);
        
        $this->db->where('identificacion', $identificacion);

        $this->db->update($this->table_persona);
        $error = $this->db->_error_message(); 
		if ($error) {
			return $this->db->_error_message();
		}else{
            return 4;
        }
    }

    public function registrarPersonaIntegracion($identificacion, $nombre, $segundoNombre, $apellido, $segundoApellido, $id_perfil, $usuario, $correo, $contrasena, $tipo_persona)
    {

        $this->db->insert($this->table_persona, array(
            "identificacion" => $identificacion,
            "nombre" => $nombre,
            "segundo_nombre" => $segundoNombre,
            "apellido" => $apellido,
            "segundo_apellido" => $segundoApellido,
            "id_perfil" => $id_perfil,
            "usuario" => $usuario,
            "correo" => $correo,
            "contrasena" => $contrasena,
            "id_tipo_persona" => $tipo_persona,
            "id_tipo_identificacion" => 1, //para pruebas se agrega el codigo de tipo de identificacion 'cedula de ciudadania'
        ));
        $error = $this->db->_error_message();
        if ($error) {
            return 1;
        } else {
            return 2;
        }
    }

    public function actualizarPersonaIntegracion($identificacion, $nombre, $segundoNombre, $apellido, $segundoApellido, $id_perfil, $usuario, $correo)
    {

        if ($nombre != null) $this->db->set('nombre', $nombre);
        if ($segundoNombre != null) $this->db->set('segundo_nombre', $segundoNombre);
        if ($apellido != null) $this->db->set('apellido', $apellido);
        if ($segundoApellido != null) $this->db->set('segundo_apellido', $segundoApellido);
        if ($id_perfil != null) $this->db->set('id_perfil', $id_perfil);
        if ($usuario != null) $this->db->set('usuario', $usuario);
        if ($correo != null) $this->db->set('correo', $correo);

        $this->db->where('identificacion', $identificacion);
        $this->db->update($this->table_persona);
        $error = $this->db->_error_message();
        if ($error) {
            return 3;
        } else {
            return 4;
        }
    }

    public function buscarPersonaIntegracion($identificacion){
        $this->db->select("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo", false);
        $this->db->from('personas p');
        $this->db->where("p.identificacion", $identificacion);
        $query = $this->db->get();
        return $query->result_array();
    }


}
