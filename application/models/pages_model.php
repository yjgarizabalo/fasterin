<?php
date_default_timezone_set('America/Bogota');
if (!defined('BASEPATH')) exit('No direct script access allowed');

class pages_model extends CI_Model
{
	/**
	 * Se encarga de guardar los datos que se le pasen por el controlador en la tabla indicada.
	 * @param Array $data 
	 * @param String $tabla 
	 * @return Int
	 */
	public function guardar_datos($data, $tabla, $tipo = 1)
	{
		$tipo == 2	? $this->db->insert_batch($tabla, $data) : $this->db->insert($tabla, $data);
		$error = $this->db->_error_message();
		return $error ? -1 : 1;
	}
	/**
	 * Se encarga de modificar los datos que se le pasen por el controlador en la tabla indicada.
	 * @param Array $data 
	 * @param String $tabla 
	 * @param Int $id 
	 * @return Int
	 */
	public function modificar_datos($data, $tabla, $id, $col = 'id')
	{
		$this->db->where($col, $id);
		$this->db->update($tabla, $data);
		$error = $this->db->_error_message();
		return $error ? -1 : 1;
	}

	public	function eliminar_datos($id, $tabla)
	{
		$this->db->where('id', $id);
		$this->db->delete($tabla);
		$error = $this->db->_error_message();
		return $error ? -1 : 1;
	}

	public function listar_comentarios_general($id_solicitud, $tipo)
	{
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona,c.*", false);
		$this->db->from('comentarios_generales c');
		$this->db->join('personas p', 'c.usuario_registra = p.id');
		$this->db->where('c.id_solicitud', $id_solicitud);
		$this->db->where('c.tipo', $tipo);
		$this->db->where('c.estado', 1);
		$this->db->where('c.id_comentario IS NULL');
		$query = $this->db->get();
		return $query->result_array();
	}
	public function listar_respuestas_comentario_general($id)
	{
		$this->db->select("CONCAT(p.nombre, ' ', p.apellido, ' ', p.segundo_apellido) AS persona,c.*", false);
		$this->db->from('comentarios_generales c');
		$this->db->join('personas p', 'c.usuario_registra = p.id');
		$this->db->where('c.estado', 1);
		$this->db->where('c.id_comentario', $id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function listar_notificaciones_comentarios_general($tipos, $adms)
	{
		$perfil = $_SESSION["perfil"];
		$persona = $_SESSION["persona"];
		$sw = false;
		foreach ($adms as $p) if ($perfil == $p) $sw = true;
		if ($sw)  $query = $this->db->query("SELECT CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona,cc.*,(SELECT COALESCE((SELECT cres.usuario_registra FROM comentarios_generales cres WHERE cres.id_comentario = cc.id ORDER by cres.id DESC LIMIT 1), 0)) res FROM comentarios_generales cc INNER JOIN personas p ON p.id=cc.usuario_registra left join comentarios_generales ccr on cc.id = ccr.id_comentario WHERE  cc.estado_notificacion = 1 AND cc.id_comentario IS null AND $tipos  AND cc.usuario_registra <> $persona 	GROUP BY cc.id HAVING res <> $persona");
		else $query = $this->db->query("SELECT CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as persona,cc.*,(SELECT COALESCE((SELECT cres.usuario_registra FROM comentarios_generales cres WHERE cres.id_comentario = cc.id ORDER by cres.id DESC LIMIT 1), cc.usuario_registra)) res FROM comentarios_generales cc  INNER JOIN personas p ON p.id=cc.usuario_registra left join comentarios_generales ccr on cc.id = ccr.id_comentario WHERE  cc.estado_notificacion = 1 AND cc.id_comentario IS null AND $tipos  AND cc.usuario_registra = $persona GROUP BY cc.id HAVING res <> $persona");
		return $query->result_array();
	}

	public function buscar_persona($where)
	{
		$this->db->select("p.identificacion,p.id,p.nombre,p.segundo_nombre,p.apellido,p.segundo_apellido,p.correo,p.id_tipo_identificacion,p.fecha_expedicion,p.lugar_expedicion,p.fecha_nacimiento,CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) as nombre_completo,u2.valor tipo_identificacion", false);
		$this->db->from('personas p');
		$this->db->join('valor_parametro u2', 'p.id_tipo_identificacion=u2.id');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->result_array();
	}


	public function obtener_valores_permiso($vp_principal, $idparametro, $tipo = 1)
	{
		if ($tipo == 1) {
			$this->db->select("pp.vp_secundario_id id, upper(vp.valor) valor", false);
			$this->db->from("permisos_parametros pp");
			$this->db->join("valor_parametro vp", "vp.id = pp.vp_secundario_id AND vp.idparametro = $idparametro");
			$this->db->where('pp.vp_principal_id', $vp_principal);
		} else {
			$this->db->select("pp.vp_secundario id, upper(vp.valor) valor", false);
			$this->db->from("permisos_parametros pp");
			$this->db->join("valor_parametro vp", "vp.id_aux = pp.vp_secundario AND vp.idparametro = $idparametro");
			$this->db->where('pp.vp_principal', $vp_principal);
		}
		$this->db->where('pp.estado', 1);
		$query = $this->db->get();
		return $query->result_array();
	}


	public function cargar_archivo($mi_archivo, $ruta, $nombre)
	{
		$nombre .= uniqid();
		$real_path = realpath(APPPATH . '../' . $ruta);
		$config['upload_path'] = $real_path;
		$config['file_name'] = $nombre;
		$config['allowed_types'] = "*";
		$config['max_size'] = "0";
		$config['max_width'] = "0";
		$config['max_height'] = "0";
		$this->load->library('upload', $config);
		if (!$this->upload->do_upload($mi_archivo)) {
			$data['uploadError'] = $this->upload->display_errors();
			return array(-1, $data['uploadError']);
		}
		$data['uploadSuccess'] = $this->upload->data();
		return array(1, $data['uploadSuccess']["file_name"]);
	}

	function basico($numero)
	{
		$valor = array('uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve', 'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciseis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte', 'veintiuno', 'veintidos', 'veintitres', 'veinticuatro', 'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho', 'veintinueve', 'treinta', 'treinta y uno');
		return $valor[$numero - 1];
	}

	function decenas($n)
	{
		$decenas = array(30 => 'treinta', 40 => 'cuarenta', 50 => 'cincuenta', 60 => 'sesenta', 70 => 'setenta', 80 => 'ochenta', 90 => 'noventa');
		if ($n <= 29) return $this->basico($n);
		$x = $n % 10;
		return $x == 0
			? $decenas[$n]
			: $decenas[$n - $x] . ' y ' . $this->basico($x);
	}

	function centenas($n)
	{
		$cientos = array(100 => 'cien', 200 => 'doscientos', 300 => 'trecientos', 400 => 'cuatrocientos', 500 => 'quinientos', 600 => 'seiscientos', 700 => 'setecientos', 800 => 'ochocientos', 900 => 'novecientos');
		if ($n >= 100) {
			if ($n % 100 == 0) return $cientos[$n];
			else {
				$u = (int) substr($n, 0, 1);
				$d = (int) substr($n, 1, 2);
				return ($u == 1 ? 'ciento' : $cientos[$u * 100]) . ' ' . $this->decenas($d);
			}
		} else return $this->decenas($n);
	}

	function miles($n)
	{
		if ($n > 999) {
			if ($n == 1000) {
				return 'mil';
			} else {
				$l = strlen($n);
				$c = (int) substr($n, 0, $l - 3);
				$x = (int) substr($n, -3);
				if ($c == 1) $cadena = 'mil ' . $this->centenas($x);
				else if ($x != 0) $cadena = $this->centenas($c) . ' mil ' . $this->centenas($x);
				else $cadena = $this->centenas($c) . ' mil';
				return $cadena;
			}
		} else return $this->centenas($n);
	}

	function millones($n)
	{
		if ($n == 1000000) {
			return 'un millón';
		} else {
			$l = strlen($n);
			$c = (int) substr($n, 0, $l - 6);
			$x = (int) substr($n, -6);
			$cadena = $c == 1 ? ' millón ' : ' millones ';
		}
		return $this->miles($c) . $cadena . (($x > 0) ? $this->miles($x) : '');
	}

	function convertirNumeroALetras($n)
	{
		switch (true) {
			case ($n >= 1 && $n <= 29):
				return $this->basico($n);
				break;
			case ($n >= 30 && $n < 100):
				return $this->decenas($n);
				break;
			case ($n >= 100 && $n < 1000):
				return $this->centenas($n);
				break;
			case ($n >= 1000 && $n <= 999999):
				return $this->miles($n);
				break;
			case ($n >= 1000000):
				return $this->millones($n);
		}
	}

	function convertirFechaALetras($fecha, $certificado = false)
	{
		$months = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		$date = substr($fecha, 0, 10);
		$day = date('d', strtotime($date));
		$month = date('m', strtotime($date));
		$year = date('Y', strtotime($date));
		$monthName = $months[(int) $month];
		$dayString = $this->basico($day);
		$yearString = $this->miles($year);
		return $certificado
			? "$dayString ($day) días del mes de $monthName de $yearString ($year)"
			: "$dayString ($day) de $monthName de $yearString ($year)";
	}

	/* Funcion para guardar datos normales */
	public function update_inf($usuario, $datos)
	{
		$this->db->set($datos);
		$this->db->where("personas.usuario", $usuario);
		$this->db->update("personas");
		$query = $this->db->affected_rows();
		return $query;
	}

	/* Funcion para checkear el codigo de verificacion mediante el correo */
	public function check_validation_code($usuario, $codigo)
	{
		$this->db->select("p.correo");
		$this->db->from("personas p");
		$this->db->where("p.estado", 1);
		$this->db->where("p.usuario", $usuario);
		$this->db->where("p.validation_code", $codigo);
		$query = $this->db->get();
		return $query->row();
	}

	/* Check si el usuario existe en la tabla personas */
	public function check_users($usuario)
	{
		$this->db->select("
		p.usuario,
		CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) full_name,
		p.correo", false);
		$this->db->from("personas p");
		$this->db->where("p.usuario", $usuario);
		$this->db->where("p.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	public function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$fecha_actual = date($format);
		$d = DateTime::createFromFormat($format, $date);
		$valida = $d && $d->format($format) == $date;
		if ($valida && ($d->format($format) < $fecha_actual)) return false;
		return $valida;
	}

	public function verificar_campos_string($array)
	{
		foreach ($array as $row) {
			if (empty($row) || ctype_space($row)) {
				return ['type' => -2, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
	}

	public function verificar_campos_numericos($array)
	{
		foreach ($array as $row) {
			if (!is_numeric($row)) {
				return ['type' => -1, 'field' => array_search($row, $array, true)];
			}
		}
		return 1;
	}

	/* Check permisos para perfiles de talento humanos */
	/* Check permisos para perfiles de talento humano */
	public function Listar_permisos_perfil_talento_adm()
	{
		$this->db->select("ap.*, vp.valor actividad, vp.valory icono");
		$this->db->from("actividades_perfil ap");
		$this->db->join("valor_parametro vp", "vp.id_aux = ap.id_actividad");
		$this->db->where("ap.id_perfil", $_SESSION['perfil']);
		$this->db->where("vp.valorx = 'tal_adm'");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function conexion_oracle(){
		$conn = oci_connect('consulta_sinu', 'B4nk41', "10.238.0.165:1521/sinu");
		if (!$conn) {
			$e = oci_error();
			return trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}return $conn;
	}

	/* public function get_materias_por_docente_sicuc($identificacion){
		$query = [];
		$this->load->model('genericas_model');
		$per = $this->genericas_model->obtener_valores_parametro_aux("Bin_Per_Aca", 277);
        $periodo = $per[0]['valor']; 
		$conn = $this->conexion_oracle();
		if ($conn) {
			$stid = oci_parse($conn, "SELECT C.num_identificacion identificacion_doc,
			d.cod_materia,
			(select nom_materia from sinu.src_materia where D.cod_materia = cod_materia) materia,
			A.id_grupo cod_grupo, 
			D.num_grupo grupo,
			(select num_nivel from sinu.src_mat_pensum where d.cod_materia = cod_materia and rownum <2) semestre,
			d.cod_unidad cod_programa,
			(select nom_unidad from sinu.src_uni_academica where d.cod_unidad = cod_unidad) nom_programa,
			CONCAT(d.cod_materia,A.id_grupo) id
			FROM sinu.src_doc_grupo A
			LEFT JOIN sinu.SRC_VINCULACION B ON A.id_vinculacion = B.ID_VINCULACION
			LEFT JOIN sinu.BAS_TERCERO C ON B.id_tercero = C.id_tercero
			LEFT JOIN sinu.src_grupo D ON A.id_grupo = D.id_grupo
			WHERE C.num_identificacion = '$identificacion' AND D.cod_periodo = '$periodo'");
			oci_execute($stid);
			while (($row = oci_fetch_array($stid, OCI_BOTH)) != false) {
				array_push($query,[
					'identificacion_doc' => $row[0],
					'cod_materia' => $row[1],
					'materia' => $row[2],
					'cod_grupo' => $row[3],
					'grupo' => $row[4],
					'semestre' => $row[5],
					'cod_programa' => $row[6],
					'nom_programa' => $row[7],
					'id' => $row[8],
					'valor' => $row[2].' / '.$row[4], 
					'materia' => $row[2].' / '.$row[4]
				 ]);
			}
			oci_free_statement($stid);
			oci_close($conn);	
		}else ['mensaje'=> "Error al conectar a la base de datos, ".$conn,'tipo'=>"error",'titulo'=> "Oops"];
		return $query;
	} */

	/* public function obtener_estudiantes_por_materia_sicuc($materia){
		$query = [];
		$estudiantes = [];
		$this->load->model('genericas_model');
		$per = $this->genericas_model->obtener_valores_parametro_aux("Bin_Per_Aca", 277);
        $periodo = $per[0]['valor']; 
		$conn = $this->conexion_oracle();
		if ($conn) {
			$stid = oci_parse($conn, "SELECT
			a.nom_tercero,
			a.pri_apellido,
			a.seg_apellido,
			a.num_identificacion  identificacion
			FROM sinu.bas_tercero a,sinu.src_alum_programa b,sinu.src_uni_academica c,sinu.src_alum_periodo d,
			sinu.src_enc_matricula e ,sinu.src_grupo f,sinu.src_mat_pensum g,sinu.src_vinculacion h,sinu.bas_tercero i
			WHERE 1=1
			AND a.id_tercero=b.id_tercero
			AND b.cod_unidad=c.cod_unidad
			AND b.id_alum_programa=d.id_alum_programa
			AND b.id_alum_programa=e.id_alum_programa
			AND f.cod_unidad=g.cod_unidad
			AND f.cod_pensum=g.cod_pensum
			AND f.cod_materia=g.cod_materia
			AND e.id_grupo=f.id_grupo
			AND f.id_vinculacion = h.id_vinculacion(+)
			AND h.id_tercero=i.id_tercero(+)
			AND b.est_alumno IN (1,7)
			AND d.cod_periodo='$periodo'
			AND d.est_mat_fin=1
			AND c.cod_modalidad=1
			AND e.cod_periodo='$periodo' AND CONCAT(f.cod_materia,f.id_grupo) = '$materia'");
			oci_execute($stid);
			$i = 0;
			while (($row = oci_fetch_array($stid, OCI_BOTH)) != false) {
				array_push($query,['nombre_completo' => $row[0].' '.$row[1].' '.$row[2], 'identificacion' => $row[3]]);
				$estudiantes[$i] = $row[3]; // identificaciones
				$i++;
			}
			oci_free_statement($stid);
			oci_close($conn);	
		}else ['mensaje'=> "Error al conectar a la base de datos, ".$conn,'tipo'=>"error",'titulo'=> "Oops"];
		return [$query, $estudiantes];
	} */

	/* Obtener materias por docentes SICUC */
	public function get_materias_por_docente_sicuc($identificacion)
	{
		$this->load->model('genericas_model');
		/* $per = $this->genericas_model->obtener_valores_parametro_aux("Bin_Per_Aca", 277);
		$periodo = $per[0]['valor']; */

		$ch = curl_init();
		$buscar = ["identificacion" => $identificacion];
		//$api = "";
		$api = "https://backend.cuc.edu.co/api/v1.0/integraciones/agil/profesor/materias";
		$data_buscar = http_build_query($buscar);
		curl_setopt($ch, CURLOPT_URL, $api);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_buscar);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		return json_decode($result, true);
		curl_close($ch);
	}

	/* Obtener estudiantes por materia SICUC */
	public function obtener_estudiantes_por_materia_sicuc($materia)
	{
		$this->load->model('genericas_model');
		/* $per = $this->genericas_model->obtener_valores_parametro_aux("Bin_Per_Aca", 277);
		$periodo = $per[0]['valor']; */

		$ch = curl_init();
		$buscar = ["codigo" => $materia];
		//$api = "";
		$api = "https://backend.cuc.edu.co/api/v1.0/integraciones/agil/estudiante/vinculados";
		$data_buscar = http_build_query($buscar);
		curl_setopt($ch, CURLOPT_URL, $api);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_buscar);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		$estudiante_cedula = [];
		$query = json_decode($result, true);

		foreach($query['data'] as $estudiante_individual){
			array_push($estudiante_cedula, $estudiante_individual['identificacion']);
		}

		return [$query['data'], $estudiante_cedula];
		curl_close($ch);
	}

	public function obtener_id_estudiantes($estudiantes){
		$this->db->select("vs.id, CONCAT(vs.nombre, ' ', vs.apellido, ' ', vs.segundo_apellido) nombre_completo, vs.identificacion, 'visitantes' tabla", false);
		$this->db->from("visitantes vs");
		$this->db->where("vs.identificacion IN(".implode(',',$estudiantes).")");
		$this->db->where("vs.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function obtener_info_notificacion () {
		$this->db->select("vp.valor, vp.valorz, vp.id");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro = 332 AND vp.valory = 'si' AND vp.estado = 1");
		$query = $this->db->get();
		return $query->result_array();
	}

}
