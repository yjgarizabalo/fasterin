<?php

use Mpdf\Tag\P;

date_default_timezone_set('America/Bogota');

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class plan_accion_model extends CI_Model
{

	/* Listar solicitudes */
	public function listar_solicitudes($id_meta = '', $idFormato = '', $row = false)
	{
		$administrar = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro" ? true : false;
		$ps = $_SESSION['persona'];
		$this->db->select(
			"mpa.id,
			mpa.id_area,
			mpa.id_reto reto_id,
			mpa.id_plan_desarrollo plan_desarrollo_id,
			mpa.id_indicador_estrategico indicador_estrategico_id,
			mpa.id_formato,
			mpa.meta_plan_accion meta_20xx,
			mpa.id_indicador_operativo tipo_indicador_id,
			mpa.indicador_operativo,
			mpa.cifra_referencia,
			mpa.meta,
			mpa.codigo_item,
			mpa.nombre_accion,
			mpa.enunciado_accion,
			mpa.meta_estado,
			mpa.id_programa idPrograma,
			mpa.id_entregable entregable,
			mpa.id_recomendacion idRecomendacion,
			mpa.id_meta_institucional idmi,
			mpa.observaciones obs,
			mpa.fecha_corrige,
			mpad.nombre_accion tituloMetaInsti,
			rn.valor recomendacion,
			vp.valor area_est,
			vpt.valor reto,
			pd.valor meta_plan_desarrollo,
			ie.valor indicador_estrategico,
			ioo.valor tipo_indicador_operativo,
			vptr.valor estado_meta,
			pn.valor programName,
			rolName.valor liderRol,
			rpa.id_responsable,
			ppe.id idPermiso,
			ppl.id_lider originalLider,

			#Id Lider Provicional:
			ppedd.id_lider liderProvi,

			mpa.id_usuario_registra,
			format.valor formatoName,
			p.correo usuario_regis_mail,
			CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) usuario_registra",
			false
		);
		$this->db->from("metas_plan_accion mpa");
		$this->db->join('valor_parametro vp', 'vp.id = mpa.id_area', "left");
		$this->db->join('valor_parametro vpt', 'vpt.id = IF(mpa.id_reto IS NULL, 102, mpa.id_reto)', "left");
		$this->db->join('valor_parametro pd', 'pd.id = IF(mpa.id_plan_desarrollo IS NULL, 102, mpa.id_plan_desarrollo)', "left");
		$this->db->join('valor_parametro ie', 'ie.id = IF(mpa.id_indicador_estrategico IS NULL OR mpa.id_indicador_estrategico = 0, 102, mpa.id_indicador_estrategico)', "left");
		$this->db->join('valor_parametro ioo', 'ioo.id = IF(mpa.id_indicador_operativo IS NULL, 102, mpa.id_indicador_operativo)', "left");
		$this->db->join('valor_parametro vptr', 'vptr.id_aux = mpa.meta_estado', "left");
		$this->db->join('valor_parametro pn', "pn.id = IF(mpa.id_programa IS NULL, 102, mpa.id_programa)", "left");
		$this->db->join('valor_parametro rn', "rn.id = IF(mpa.id_recomendacion IS NULL, 102, mpa.id_recomendacion)", "left");
		$this->db->join('personas p', 'p.id = IF(mpa.id_usuario_registra IS NULL, 102, mpa.id_usuario_registra)', "left");
		$this->db->join('metas_plan_accion mpad', 'mpad.id = mpa.id_meta_institucional', "left");

		//Joins para traer el RolName y pintarlo en el formulario principal de programas
		$this->db->join('plan_accion_permisos_equipos ppee', 'ppee.id_director = mpad.id_usuario_registra AND ppee.estado = 1 AND mpa.estado = 1', "left");
		$this->db->join('plan_accion_permisos_lideres ppll', 'ppll.id_lider = ppee.id_lider AND ppee.estado = 1 AND ppll.estado = 1', "left");
		$this->db->join('valor_parametro rolName', 'rolName.id = ppll.id_rol AND rolName.estado = 1 AND ppll.estado = 1', "left");
		//fin de los joins

		$this->db->join("plan_accion_permisos_equipos ppe", "ppe.id_director = mpa.id_usuario_registra AND ppe.id_lider = $ps AND ppe.estado = 1", "left");
		$this->db->join('plan_accion_permisos_lideres ppl', 'ppl.id_lider = ppe.id_lider AND ppl.estado = 1 AND ppl.estado = 1', "left");

		#Join para traer el id del lider que tenga cada solicitud y asi poder comprarar y anexarlo a la sra gloria.
		$this->db->join("plan_accion_permisos_equipos ppedd", "ppedd.id_director = mpa.id_usuario_registra AND ppedd.estado = 1", "left");

		$this->db->join('responsables_plan_accion rpa', "rpa.id_meta = mpa.id AND mpa.estado = 1 AND rpa.id_responsable = $ps AND rpa.estado = 1", "left");
		$this->db->join('valor_parametro format', "format.id = mpa.id_formato AND mpa.estado = 1 AND format.estado = 1", "left");
		!empty($id_meta) ? $this->db->where('mpa.id', $id_meta) : false;
		$this->db->where("mpa.estado", 1);
		$this->db->where("vp.estado", 1);
		$this->db->where("vpt.estado", 1);
		$this->db->where("pd.estado", 1);
		$this->db->where("ie.estado", 1);
		$this->db->where("ioo.estado", 1);
		$this->db->where("vptr.estado", 1);
		$this->db->where("pn.estado", 1);
		$this->db->where("rn.estado", 1);
		$this->db->where("p.estado", 1);

		if (!empty($idFormato)) $this->db->where("mpa.id_formato", $idFormato);

		/* Si un responsable realiza un cambio en una meta/accion, el id_usuario_registra cambia y puede dañar joins ya existentes.
		por lo que hay que verificar que btns de accion se le asignan a esas personas. */

		//$this->db->order_by("vp.valor", "ASC");
		$this->db->group_by("mpa.id", "ASC");

		$query = $this->db->get();

		if ($row) {
			return $query->row();
		} else {
			return $query->result_array();
		}
	}

	/* Listar formatos de plan de acción */
	public function listar_formatos_planAccion($idpa)
	{
		$this->db->select("vp.id, vp.id_aux idaux, vp.valor area, vp.valorx descripcion, vp.valory ruta_img");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idpa);
		$this->db->where("vp.estado", 1);
		$this->db->order_by("vp.valorz", "ASC");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Formatos asignados segun tabla plan_accion_permisos_lideres */
	public function permisosLideres($formato = '')
	{
		$this->db->select(
			"pl.id, pl.id_formato,
			vp.valor formatoName,
			vp.id_aux,
			pl.id_usuario_registra,
			pl.id_lider,
			pl.id_rol,
			vpt.valor rolName,
			pl.estado,
			CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) fullName",
			false
		);
		$this->db->from("plan_accion_permisos_lideres pl");
		$this->db->join("valor_parametro vp", "vp.id = pl.id_formato");
		$this->db->join("valor_parametro vpt", "vpt.id = pl.id_rol");
		$this->db->join("personas p", "p.id = pl.id_lider");
		$this->db->where("(pl.estado = 1 AND pl.id_usuario_elimina IS NULL)");
		if ($formato) $this->db->where("vp.id_aux", $formato);
		$this->db->where("vp.estado", 1);
		$this->db->where("p.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar los directores asignados a un lider determinado */
	public function directoresAssigned($idLider = '', $idFormato = '')
	{
		$this->db->select(
			"ppe.id,
			ppe.id_usuario_registra,
			ppe.id_lider,
			ppe.id_director,
			ppe.estado,
			ppe.presupuesto presu,
			ppl.id_formato,
			vptro.valor formatoName,
			vptro.id_aux idauxFormat,
			CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) liderName,
			p.usuario,
			vp.valor liderCargo,
			CONCAT(pd.nombre,' ',pd.apellido,' ',pd.segundo_apellido) directorName,
			pd.usuario,
			vpp.valor directorCargo",
			false
		);
		$this->db->from("plan_accion_permisos_equipos ppe");
		$this->db->join("personas p", "p.id = ppe.id_lider");
		$this->db->join("valor_parametro vp", "vp.id = p.id_cargo_sap");
		$this->db->join("personas pd", "pd.id = ppe.id_director");
		$this->db->join("valor_parametro vpp", "vpp.id = pd.id_cargo_sap");
		$this->db->join("plan_accion_permisos_lideres ppl", "ppl.id_lider = ppe.id_lider");
		$this->db->join("valor_parametro vptro", "vptro.id = ppl.id_formato", "left");
		$this->db->where("(ppe.id_lider = $idLider AND ppl.id_formato = $idFormato AND ppe.estado = 1 AND ppe.id_usuario_elimina IS NULL AND ppl.id_usuario_elimina IS NULL)");
		$this->db->or_where("(ppe.id_director = $idLider AND ppl.id_formato = $idFormato AND ppe.estado = 1 AND ppe.id_usuario_elimina IS NULL AND ppl.id_usuario_elimina IS NULL)");
		$this->db->where("vptro.estado", 1);
		$this->db->where("p.estado", 1);
		$this->db->where("pd.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar areas estrategicas */
	public function listar_areas_estrategicas($idparametro)
	{
		$this->db->select("vp.id, vp.id_aux idaux, vp.valor area, vp.valorx descripcion, vp.valory ruta_img");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idparametro);
		$this->db->where("vp.estado", 1);
		$this->db->order_by("vp.valorz", "ASC");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Traer valores parametros segun permiso area estrategica electa */
	public function buscar_retos_etc($area_selected, $reto_meta_indicador)
	{
		$this->db->select("vp.id, vpt.valor area, vp.valor, vp.idparametro, vp.valora v_a");
		$this->db->from("permisos_parametros pp");
		$this->db->join("valor_parametro vp", "vp.id = pp.vp_principal_id");
		$this->db->join("valor_parametro vpt", "vpt.id = pp.vp_secundario_id");
		$this->db->where("pp.vp_secundario_id", $area_selected);
		$this->db->where("vp.idparametro", $reto_meta_indicador);
		$this->db->where("pp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Busca los planes de accion y sus indicadores segun el reto seleccionado */
	public function buscar_plan_des($area_selected, $valora_or_b, $reto_etc, $ind_est)
	{
		$this->db->select("vp.id, vp.valor, vp.idparametro, vp.valora v_a, vp.valorb v_b");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $reto_etc);
		$this->db->where("vp.valora", $valora_or_b);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Busca los indicadores segun el plan de desarrollo elegido */
	public function buscar_indis($area_selected, $valora_or_b, $reto_etc, $ind_est)
	{
		$this->db->select("vp.id, vp.valor, vp.idparametro, vp.valora v_a, vp.valorb v_b");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.valora", $valora_or_b);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Buscar personas, responsables */
	public function buscar_responsable($persona)
	{
		$this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) full_name, p.usuario, vp.valor cargo_sap", false);
		$this->db->from("personas p");
		$this->db->join("valor_parametro vp", "p.id_cargo_sap = vp.id");
		$this->db->like("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido)", $persona);
		$this->db->or_like("p.usuario", $persona);
		$this->db->or_like("p.identificacion", $persona);
		$this->db->where('p.estado', 1);
		$this->db->where('vp.estado', 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Buscar responsables ya asignados segun la id de meta */
	public function responsables_asignados($idm)
	{
		$this->db->select(
			"rpa.id,
			rpa.id_meta,
			CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) full_name,
			p.usuario,
			p.id,
			vp.valor cargo_sap",
			false
		);
		$this->db->from("responsables_plan_accion rpa");
		$this->db->join("personas p", "rpa.id_responsable = p.id");
		$this->db->join("valor_parametro vp", "p.id_cargo_sap = vp.id");
		$this->db->where("rpa.id_meta", $idm);
		$this->db->where("rpa.estado", 1);
		$this->db->where("p.estado", 1);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Buscar idparametro a partir del idaux - Puede mejorar esta funcion */
	public function buscar_idparametro($id = "", $idaux = "")
	{
		$this->db->select("vp.idparametro, vp.valor");
		$this->db->from("valor_parametro vp");

		if (empty($id) and !empty($idaux)) {
			$this->db->where("vp.id_aux", $idaux);
		} else if (empty($idaux) and !empty($id)) {
			$this->db->where("vp.id", $id);
		}

		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	/* Traer datos de valor_parametro */
	public function traer_datos_valorp($idparametro = "", $id = "", $row = false)
	{
		$this->db->select("vp.id, vp.id_aux idaux, vp.valor dato, vp.valorx, vp.valory, vp.valorz");
		$this->db->from("valor_parametro vp");

		if (empty($idparametro) and !empty($id)) {
			$this->db->where("vp.id", $id);
		} else if (empty($id) and !empty($idparametro)) {
			$this->db->where("vp.idparametro", $idparametro);
		}

		$query = $this->db->get();
		if ($row) {
			return $query->row();
		} else {
			return $query->result_array();
		}
	}

	/* Esta funcion pretende traer un unico idparametro a partir de varias coincidencias, sea por id_aux, etc. */
	public function find_idParametro($codigo)
	{
		$this->db->select("vp.id, vp.id_aux idaux, vp.valor dato, vp.valorx vx, vp.idparametro idpa, vp.valory vy, vp.valorz vz, vp.valora va, vp.valorb vb");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.id", $codigo);
		$this->db->or_where("vp.id_aux", $codigo);
		$this->db->or_where("vp.valory", $codigo);
		$this->db->or_where("vp.valorz", $codigo);
		$this->db->or_where("vp.valora", $codigo);
		$this->db->or_where("vp.valorb", $codigo);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}


	/* Listar metas segun el area estrategica escogida */
	public function listar_metas($area_est, $id_meta)
	{
		$this->db->select(
			"mpa.id,
			mpa.id_reto reto_id,
			mpa.id_area,
			vppt.valor area_est,
			mpa.id_entregable entregable,
			mpa.id_formato,
			mpa.codigo_item,
			mpa.id_plan_desarrollo plan_desarrollo_id,
			mpa.id_indicador_estrategico indicador_estrategico_id,
			mpa.id_indicador_operativo tipo_indicador_id,
			mpa.id_recomendacion idRecomendacion,
			mpa.cifra_referencia,
			mpa.id_programa idPrograma,
			mpa.meta,
			mpa.nombre_accion,
			mpa.enunciado_accion,
			mpa.meta_estado,
			mpa.id_meta_institucional idmi,
			mpa.observaciones obs,
			mpa.fecha_corrige,
			mpad.nombre_accion tituloMetaInsti,
			vp.valor reto,
			vpt.valor meta_plan_desarrollo,
			vptr.valor indicador_estrategico,
			vptro.valor tipo_indicador_operativo,
			vvptro.valor formato_plan,
			mpa.meta_plan_accion meta_20xx,
			mpa.indicador_operativo,
			rn.valor recomendacion,
			pn.valor programName,
			rolName.valor liderRol,
			format.valor formatoName,
			format.id_aux formatoIdAux,
			p.correo usuario_regis_mail,
			CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) usuario_registra",
			false
		);
		$this->db->from("metas_plan_accion mpa");
		$this->db->join("valor_parametro vppt", "vppt.id = IF(mpa.id_area IS NULL, 102, mpa.id_area)", "left");
		$this->db->join("valor_parametro vp", "vp.id = IF(mpa.id_reto IS NULL, 102, mpa.id_reto)", "left");
		$this->db->join("valor_parametro vpt", "vpt.id = IF(mpa.id_plan_desarrollo IS NULL, 102, mpa.id_plan_desarrollo)", "left");
		$this->db->join("valor_parametro vptr", "vptr.id = IF(mpa.id_indicador_estrategico IS NULL, 102, mpa.id_indicador_estrategico)", "left");
		$this->db->join("valor_parametro vptro", "vptro.id = IF(mpa.id_indicador_operativo IS NULL, 102, mpa.id_indicador_operativo)", "left");
		$this->db->join("valor_parametro vvptro", "vvptro.id = IF(mpa.id_formato IS NULL, 102, mpa.id_formato)", "left");
		$this->db->join('valor_parametro pn', 'pn.id = IF(mpa.id_programa IS NULL, 102, mpa.id_programa)');
		$this->db->join('valor_parametro rn', "rn.id = IF(mpa.id_recomendacion IS NULL, 102, mpa.id_recomendacion)", "left");
		$this->db->join("personas p", "p.id = IF(mpa.id_usuario_registra IS NULL, 102, mpa.id_usuario_registra)", "left");
		$this->db->join('metas_plan_accion mpad', 'mpad.id = mpa.id_meta_institucional', "left");
		$this->db->join('plan_accion_permisos_equipos ppe', 'ppe.id_director = mpad.id_usuario_registra AND ppe.estado = 1 AND mpa.estado = 1', "left");
		$this->db->join('plan_accion_permisos_lideres ppl', 'ppl.id_lider = ppe.id_lider AND ppl.estado = 1 AND ppl.estado = 1', "left");
		$this->db->join('valor_parametro rolName', 'rolName.id = ppl.id_rol AND rolName.estado = 1 AND ppl.estado = 1', "left");
		$this->db->join('valor_parametro format', "format.id = mpa.id_formato AND mpa.estado = 1 AND format.estado = 1", "left");
		$this->db->where("vppt.estado", 1);
		$this->db->where("vp.estado", 1);
		$this->db->where("vpt.estado", 1);
		$this->db->where("vptr.estado", 1);
		$this->db->where("vptro.estado", 1);
		$this->db->where("pn.estado", 1);
		$this->db->where("rn.estado", 1);
		$this->db->where("vvptro.estado", 1);
		$this->db->where("mpa.id_usuario_registra", $_SESSION['persona']);
		$this->db->where("mpa.meta_estado", "Meta_En_Ela");
		$this->db->where("p.estado", 1);
		$this->db->where("mpa.estado", 1);
		$this->db->where("mpa.id_area", $area_est);
		$this->db->group_by("mpa.id", "DESC");
		if (!empty($id_meta)) {
			$this->db->where("mpa.id", $id_meta);
			$query = $this->db->get();
			return $query->row();
		} else {
			$query = $this->db->get();
			return $query->result_array();
		}
	}

	/* Listar factores insitucionales */
	public function listar_factores_ins($idParametro = '')
	//El idParametro es la variable que me ayudara a traer los factores institucionales o programas
	{
		$this->db->select("vp.id, vp.valor, vp.valora detalles");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idParametro);
		$this->db->where("vp.estado", 1);
		$this->db->order_by("vp.id", "ASC");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Traer los factores institucionales guardados en BD segun la meta */
	public function factores_checked($id_meta)
	{
		$this->db->select("paf.*");
		$this->db->from("factores_plan_accion paf");
		$this->db->where("paf.id_meta", $id_meta);
		$this->db->where("paf.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Traer las caracteristicas seleccionadas segun el factor y/o programa seleccionado aquii */
	public function caracteristicas_checked($id_meta)
	{
		$this->db->select("pac.*");
		$this->db->from("plan_accion_caracteristicas pac");
		$this->db->where("pac.id_meta", $id_meta);
		$this->db->where("pac.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Ver detalles del factor seleccionado */
	public function detalles_facts($dato_buscado, $idParametro = '')
	{
		$this->db->select("vp.id, vp.valor caracteristica");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idParametro);
		$this->db->where("vp.valora", $dato_buscado);
		$this->db->where("vp.estado", 1);
		$this->db->order_by("vp.valorb", "ASC");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar las categorias de los presupuestos y listar las categorias de los presupuestos */
	public function categorias_presupuestos($codigo)
	{
		$this->db->select("vp.id, vp.id_aux idaux, vp.valor catego, vp.valory catego_num");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $codigo);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function tipos_presupuestos($cat_sel, $idpa)
	{
		$this->db->select("vp.id, vp.valor tipo, vp.valory catego_num, vp.valorz tipo_num, vp.valora tipo_inf");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.valory", $cat_sel);
		$this->db->where("vp.idparametro", $idpa);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function items_presupuestos($tipo_sel, $idpa)
	{
		$this->db->select("vp.id, vp.valor tipo, vp.valory catego_num, vp.valorz tipo_num");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.valorz", $tipo_sel);
		$this->db->where("vp.idparametro", $idpa);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar presupuestos */
	public function listar_presupuestos($id_meta)
	{
		$this->db->select(
			"ppa.id,
			ppa.id_meta idmeta,
			ppa.id_categoria idcategoria,
			vp.valor categoria,
			ppa.id_tipo idtipo,
			vpp.valor tipo,
			ppa.id_item iditem,
			vpt.valor item,
			ppa.descripcion,
			ppa.valor_solicitado,
			ppa.valor_aprobado,
			ppa.valor_ejecutado,
			ppa.cuenta_sap,
			ppa.id_usuario_registra usuario_regis,
			ppa.fecha_registra"
		);
		$this->db->from("presupuestos_plan_accion ppa");
		$this->db->join('valor_parametro vp', 'vp.id = ppa.id_categoria and vp.estado = 1 and ppa.estado = 1', "left");
		$this->db->join('valor_parametro vpp', 'vpp.id = IF(ppa.id_tipo IS NULL, 102, ppa.id_tipo) and vpp.estado = 1 and ppa.estado = 1', "left");
		$this->db->join('valor_parametro vpt', 'vpt.id = IF(ppa.id_item IS NULL, 102, ppa.id_item) and vpt.estado = 1 and ppa.estado = 1', "left");
		$this->db->where("ppa.id_meta", $id_meta);
		$this->db->where("ppa.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Guardar info sin condiciones - Reutilizable*/
	public function save_inf($tabla, $datos, $where = '')
	{
		if (!empty($where)) {
			$this->db->where($where);
		}
		$this->db->insert($tabla, $datos);
		$query = $this->db->_error_message();
		return $query;
	}

	/* Actualizar info - Reutilizable */
	public function upd_inf($tabla, $datos, $where)
	{
		$this->db->set($datos);
		$this->db->where($where);
		$this->db->update($tabla);
		$query = $this->db->_error_message();
		return $query;
	}

	/* ELiminar info de la tabla - Reutilizable */
	public function del_inf($tabla, $where)
	{
		$this->db->where($where);
		$query = $this->db->delete($tabla);
		return $query;
	}

	public function datos_cronograma($idm)
	{
		$this->db->select("cpa.*, vp.valorx trimestreName");
		$this->db->from("cronograma_plan_accion cpa");
		$this->db->join("valor_parametro vp", 'vp.id = cpa.item');
		$this->db->where("cpa.id_meta", $idm);
		$this->db->where("(cpa.estado = 1 AND cpa.id_usuario_elimina IS NULL)");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar acciones de cronograma */
	public function listar_acciones($idCrono)
	{
		$this->db->select(
			"paa.id, paa.accion,
			paa.id_usuario_registra usuario_registra, 
			CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) usuario_reg_name",
			false
		);
		$this->db->from("plan_accion_acciones paa");
		$this->db->join('personas p', 'p.id = paa.id_usuario_registra');
		$this->db->where("paa.id_cronograma", $idCrono);
		$this->db->where("p.estado", 1);
		$this->db->where("paa.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Traer todos los presupuestos de un director */
	public function presupuestosDirector()
	{
		$personaEnSession = $_SESSION['persona'];

		$this->db->select(
			"ppa.id,
			ppa.id_meta idMeta,
			ppa.id_categoria idCategoria,
			ppa.id_tipo idTipo,
			ppa.id_item,
			ppa.descripcion,
			pap.presupuesto directorPresupuesto,
			ppa.valor_solicitado valorS"
		);
		$this->db->from("presupuestos_plan_accion ppa");
		$this->db->join("metas_plan_accion mpa", "mpa.id = ppa.id_meta");
		$this->db->join("plan_accion_permisos_equipos pap", "pap.id_director = ppa.id_usuario_registra");
		$this->db->where("ppa.id_usuario_registra", $personaEnSession);
		$this->db->where("ppa.estado", 1);
		$this->db->where("pap.estado", 1);
		$this->db->where("mpa.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Sacar el ultimo id de una tabla */
	public function last_id()
	{
		$this->db->select_max('id');
		$this->db->from("cronograma_plan_accion");
		$this->db->where("estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	/* Sacar el ultimo id de una tabla */
	public function acciones_inf($idMeta, $idCrono, $row = false)
	{
		$this->db->select('cpa.id');
		$this->db->from("plan_accion_acciones cpa");
		$this->db->where("cpa.id_meta", $idMeta);
		$this->db->where("cpa.id_cronograma", $idCrono);
		$this->db->where("cpa.estado", 1);
		$query = $this->db->get();
		if ($row == true) {
			return $query->row();
		} else {
			return $query->result_array();
		}
	}

	/* Buscar personas */
	public function buscarPersonas($personaBuscada)
	{
		if (!empty($personaBuscada)) {
			$this->db->select("p.id, CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) fullName", false);
			$this->db->from("personas p");
			$this->db->like("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido)", $personaBuscada);
			$this->db->or_like("p.usuario", $personaBuscada);
			$this->db->or_like("p.identificacion", $personaBuscada);
			$this->db->where("p.estado", 1);
			$query = $this->db->get();
			return $query->result_array();
		} else {
			return [];
		}
	}

	/* listar formatos plan accion para los gestores */
	public function listarFormatosPlanAccion($idpa)
	{
		$this->db->select("vp.id, vp.valor formatName, vp.id_aux idaux");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idpa);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar roles de de los vicerectores */
	public function listarRolesVices($idpa)
	{
		$this->db->select("vp.id, vp.valor rolName");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idpa);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Traer personas con formatos asignados */
	public function gestoresAsignados($idGestor)
	{
		$this->db->select("pag.id, pag.id_formato idFormat, pag.id_gestor idGestor");
		$this->db->from("plan_accion_gestores pag");
		$this->db->where("(pag.id_gestor = $idGestor AND pag.estado = 1)");
		$this->db->or_where("(pag.id = $idGestor AND pag.estado = 1)");
		$this->db->where("pag.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar estados de metas */
	public function listarMetasEstados($idpar)
	{
		$this->db->select("vp.id, vp.valor statusName, vp.id_aux idaux");
		$this->db->from("valor_parametro vp");
		$this->db->where("vp.idparametro", $idpar);
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Traer personas con formatos asignados */
	public function estadosAsignados($idFormato)
	{
		$this->db->select("pame.id, pame.id_formato idFormat, pame.id_estado, pame.notificacion noti");
		$this->db->from("plan_accion_metas_estados pame");
		$this->db->where("pame.id_formato", $idFormato);
		$this->db->where("pame.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Traer factores institucionales para los detalles de meta */
	public function traerFactoresIns($idMeta)
	{
		$this->db->select("fpa.id_meta, fpa.id_factor, vp.valor factorName, vp.valora");
		$this->db->from("factores_plan_accion fpa");
		$this->db->join('valor_parametro vp', 'vp.id = fpa.id_factor AND vp.estado = 1', "left");
		$this->db->where("fpa.id_meta", $idMeta);
		$this->db->where("fpa.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function traerCaractsFactoresIns($idMeta, $valora)
	{
		$this->db->select("pac.id_meta, vp.valor caractName, vp.valora");
		$this->db->from("plan_accion_caracteristicas pac");
		$this->db->join('valor_parametro vp', "vp.id = pac.id_caracteristica AND vp.estado = 1", "left");
		$this->db->where("pac.id_meta", $idMeta);
		$this->db->where("vp.valora", $valora);
		$this->db->where("pac.estado", 1);
		$this->db->order_by("vp.valor", "ASC");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function traerPresupuestos($idmeta)
	{
		$this->db->select(
			"ppa.id,
			ppa.id_meta,
			vp.valor categoriaName,
			vpt.valor tipoName,
			vptr.valor itemName,
			ppa.descripcion,
			ppa.valor_solicitado"
		);
		$this->db->from("presupuestos_plan_accion ppa");
		$this->db->join('valor_parametro vp', "vp.id = ppa.id_categoria", "left");
		$this->db->join('valor_parametro vpt', "vpt.id = ppa.id_tipo", "left");
		$this->db->join('valor_parametro vptr', "vptr.id = ppa.id_item", "left");
		$this->db->where("ppa.id_meta", $idmeta);
		$this->db->where("ppa.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}


	/* Listar solicitudes */
	public function listar_solicitudes_crearmeta($id_meta = '', $idFormato = '', $row = false)
	{
		$ps = $_SESSION['persona'];
		$administrar = $_SESSION["perfil"] == "Per_Admin" || $_SESSION["perfil"] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro" ? true : false;

		$this->db->select(
			"mpa.id,
			mpa.id idMeta,
			mpa.id_area,
			mpa.id_reto reto_id,
			mpa.id_plan_desarrollo plan_desarrollo_id,
			mpa.id_indicador_estrategico indicador_estrategico_id,
			mpa.id_formato,
			mpa.meta_plan_accion meta_20xx,
			mpa.id_indicador_operativo tipo_indicador_id,
			mpa.indicador_operativo,
			mpa.cifra_referencia,
			mpa.meta,
			mpa.codigo_item,
			mpa.nombre_accion,
			mpa.enunciado_accion,
			mpa.meta_estado,
			mpa.id_entregable entregable,
			vp.valor area_est,
			vpt.valor reto,
			pd.valor meta_plan_desarrollo,
			ie.valor indicador_estrategico,
			ioo.valor tipo_indicador_operativo,
			vptr.valor estado_meta,
			CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) usuario_registra",
			false
		);
		$this->db->from("metas_plan_accion mpa");
		$this->db->join('valor_parametro vp', 'vp.id = mpa.id_area', "left");
		$this->db->join('valor_parametro vpt', 'vpt.id = IF(mpa.id_reto IS NULL, 102, mpa.id_reto)', "left");
		$this->db->join('valor_parametro pd', 'pd.id = IF(mpa.id_plan_desarrollo IS NULL, 102, mpa.id_plan_desarrollo)', "left");
		$this->db->join('valor_parametro ie', 'ie.id = IF(mpa.id_indicador_estrategico IS NULL OR mpa.id_indicador_estrategico = 0, 102, mpa.id_indicador_estrategico)', "left");
		$this->db->join('valor_parametro ioo', 'ioo.id = IF(mpa.id_indicador_operativo IS NULL, 102, mpa.id_indicador_operativo)', "left");
		$this->db->join('valor_parametro vptr', 'vptr.id_aux = mpa.meta_estado', "left");
		$this->db->join('personas p', 'p.id = IF(mpa.id_usuario_registra IS NULL, 102, mpa.id_usuario_registra)', "left");
		!empty($id_meta) ? $this->db->where('mpa.id', $id_meta) : "";
		$this->db->where(
			"(mpa.meta_estado = 'Meta_En_Ela'
			OR mpa.meta_estado = 'Meta_En_Cor' 
			OR mpa.meta_estado = 'meta_aprobada' 
			OR mpa.meta_estado = 'Meta_En_Cor' 
			OR mpa.meta_estado = 'Meta_Cor_Planeacion')"
		);
		$this->db->where("mpa.estado", 1);
		$this->db->where("vp.estado", 1);
		$this->db->where("vpt.estado", 1);
		$this->db->where("pd.estado", 1);
		$this->db->where("ie.estado", 1);
		$this->db->where("ioo.estado", 1);
		$this->db->where("p.estado", 1);
		$this->db->where("mpa.id_usuario_registra", $ps);
		$this->db->order_by("vp.valor", "ASC");
		$this->db->group_by("mpa.id", "DESC");

		$query = $this->db->get();
		if ($row) {
			return $query->row();
		} else {
			return $query->result_array();
		}

	}

	/* Traer cronogramas en detalles de meta. */
	public function traerCronograma($idMeta)
	{
		$this->db->select("cpa.id cronoId, cpa.estado, cpa.item, cpa.codigo_item, cpa.especificaciones, cpa.cantidad, CONCAT(vp.valorx,' ',cpa.codigo_item)triName", false);
		$this->db->from("cronograma_plan_accion cpa");
		$this->db->join("valor_parametro vp", "vp.id = cpa.item AND cpa.estado = 1 AND vp.estado = 1");
		$this->db->where("cpa.id_meta", $idMeta);
		$this->db->where("cpa.estado", 1);
		$this->db->order_by("cpa.codigo_item", "ASC");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Traer docs soporte para renderizar en detalles. */
	public function traerDocsSoporte($idMeta, $idCrono)
	{
		$this->db->select("paa.accion docName");
		$this->db->from("plan_accion_acciones paa");
		$this->db->where("paa.id_meta", $idMeta);
		$this->db->where("paa.id_cronograma", $idCrono);
		$this->db->where("paa.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar metas de programas */
	//Revisar linea 673 y 674 por si hay conflicto al mostrar resultados
	public function listar_metas_programa($idArea = '', $idLider = '')
	{
		$this->db->select(
			"mpa.id,
			mpa.id_area,
			mpa.id_reto reto_id,
			mpa.id_plan_desarrollo plan_desarrollo_id,
			mpa.id_indicador_estrategico indicador_estrategico_id,
			mpa.id_formato,
			mpa.meta_plan_accion titulo,
			mpa.meta_plan_accion meta_20xx,
			mpa.id_indicador_operativo tipo_indicador_id,
			mpa.indicador_operativo,
			mpa.cifra_referencia,
			mpa.meta,
			mpa.codigo_item,
			mpa.nombre_accion,
			mpa.enunciado_accion,
			mpa.meta_estado,
			mpa.id_entregable entregable,
			mpa.id_meta_institucional idmi,
			mpa.observaciones obs,
			mpa.fecha_corrige,
			mpad.nombre_accion tituloMetaInsti,
			vp.valor area_est,
			vpt.valor reto,
			pd.valor meta_plan_desarrollo,
			ie.valor indicador_estrategico,
			ioo.valor tipo_indicador_operativo,
			vptr.valor estado_meta,
			ppe.id_lider,
			rolName.valor liderRol,
			format.valor formatoName,
			p.correo usuario_regis_mail,
			CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido) usuario_registra",
			false
		);
		$this->db->from("metas_plan_accion mpa");
		$this->db->join('valor_parametro vp', 'vp.id = mpa.id_area');
		$this->db->join('valor_parametro vpt', 'vpt.id = IF(mpa.id_reto IS NULL, 102, mpa.id_reto)');
		$this->db->join('valor_parametro pd', 'pd.id = IF(mpa.id_plan_desarrollo IS NULL, 102, mpa.id_plan_desarrollo)');
		$this->db->join('valor_parametro ie', 'ie.id = IF(mpa.id_indicador_estrategico IS NULL OR mpa.id_indicador_estrategico = 0, 102, mpa.id_indicador_estrategico)');
		$this->db->join('valor_parametro ioo', 'ioo.id = IF(mpa.id_indicador_operativo IS NULL, 102, mpa.id_indicador_operativo)');
		$this->db->join('valor_parametro vptr', 'vptr.id_aux = mpa.meta_estado');
		$this->db->join('valor_parametro vptrt', 'vptrt.id = mpa.id_formato');
		$this->db->join('personas p', 'p.id = IF(mpa.id_usuario_registra IS NULL, 102, mpa.id_usuario_registra)');
		$this->db->join('metas_plan_accion mpad', 'mpad.id = IF(mpa.id_meta_institucional IS NULL, 102, mpa.id_meta_institucional)', "left");
		$this->db->join('plan_accion_permisos_equipos ppe', "ppe.id_director = mpa.id_usuario_registra AND ppe.id_lider = $idLider AND ppe.estado = 1");
		$this->db->join('plan_accion_permisos_lideres ppl', "ppl.id_lider = ppe.id_lider AND ppl.estado = 1 AND ppe.estado = 1", 'left');
		$this->db->join('valor_parametro rolName', "rolName.id = ppl.id_rol AND rolName.estado = 1 AND ppl.estado = 1", 'left');
		$this->db->join('valor_parametro format', "format.id = mpa.id_formato AND mpa.estado = 1 AND format.estado = 1", "left");
		$this->db->where("mpa.estado", 1);
		$this->db->where("mpa.id_area", $idArea);
		$this->db->where("vptrt.id_aux", 'formato_institucional');
		$this->db->where("vp.estado", 1);
		$this->db->where("vpt.estado", 1);
		$this->db->where("pd.estado", 1);
		$this->db->where("ie.estado", 1);
		$this->db->where("ioo.estado", 1);
		$this->db->where("p.estado", 1);

		$this->db->order_by("vp.valor", "ASC");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar programas */
	public function listarProgramas($idMeta)
	{
		$perSession = $_SESSION['persona'];
		$this->db->select("vp.id, vp.valor programaName, vp.valorz codigoAcre, ppa.id ppaid, ppa.id_programa programachecked, ppa.id_meta metachecked");
		$this->db->from("valor_parametro vp");
		$this->db->join("plan_accion_programas_acreditados ppa", "ppa.id_programa = vp.id AND ppa.id_meta = $idMeta AND ppa.estado = 1", "left");
		$this->db->join("plan_accion_personas_programas ppp", "ppp.id_programa = vp.id AND ppp.id_persona = $perSession AND ppp.estado = 1 AND ppp.id_usuario_elimina IS NULL");
		$this->db->where("vp.idparametro", 86); //Este id es antiguo por lo que se entiende que esta asi mismo en produccion.
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar recomendacion de programas */
	public function listarRecsPrograms($idpa, $idPrograma, $idMeta)
	{
		$this->db->select(
			"pp.id ppid,
			vp.id vpid,
			vp.valor recomendacion,
			rp.id_meta idmeta,
			ppa.id_programa idprograma"
		);
		$this->db->from("permisos_parametros pp");
		$this->db->join("valor_parametro vp", "vp.id = pp.vp_secundario_id");
		$this->db->join("plan_accion_recomendaciones_programas rp", "rp.id_recomendacion = pp.vp_secundario_id AND rp.id_meta = $idMeta AND rp.estado = 1 AND pp.estado = 1", "left");
		$this->db->join("plan_accion_programas_acreditados ppa", "ppa.id_meta = rp.id_meta AND ppa.id_programa = $idPrograma AND ppa.estado = 1 AND rp.estado = 1", "left");
		$this->db->where("pp.vp_principal_id", $idPrograma);
		$this->db->where("vp.idparametro", $idpa);
		$this->db->where("vp.estado", 1);
		$this->db->where("pp.estado", 1);
		$this->db->where("vp.valorz", 'recs_programs');
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar aspectos positivos AQUIII */
	public function listarAspectosPositivos($idpa, $idPrograma, $idMeta)
	{
		$this->db->select(
			"pp.id ppid,
			vp.id id_aspecto,
			vp.valor aspecto_positivo,
			ap.id_meta idmeta"
		);
		$this->db->from("permisos_parametros pp");
		$this->db->join("valor_parametro vp", "vp.id = pp.vp_secundario_id");
		$this->db->join("plan_accion_aspectos_positivos ap", "ap.id_aspecto = pp.vp_secundario_id AND ap.id_meta = $idMeta AND ap.estado = 1 AND pp.estado = 1", "left");
		$this->db->where("pp.vp_principal_id", $idPrograma);
		$this->db->where("vp.idparametro", $idpa);
		$this->db->where("vp.estado", 1);
		$this->db->where("pp.estado", 1);
		$this->db->where("vp.valorz", 'aspectos_positivos');
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Listar programas CUC para asignar en el administrar - MEJORAR */
	public function listarProgramasCuc($idPersona = '')
	{
		$this->db->select("vp.id, vp.valor programaName, vp.valorz codigoAcre, pep.id_programa programaAsignado, pep.id_persona personaAsignado");
		$this->db->from("valor_parametro vp");
		if (empty($idPersona)) {
			$this->db->join("plan_accion_personas_programas pep", "pep.id_programa = vp.id AND pep.estado = 1", "left");
		} else {
			$this->db->join("plan_accion_personas_programas pep", "pep.id_programa = vp.id AND pep.id_persona = $idPersona AND pep.estado = 1", "left");
		}
		$this->db->where("vp.idparametro", 86); //Este id es antiguo por lo que se entiende que esta asi mismo en produccion.
		$this->db->where("vp.estado", 1);
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Traer el formato activo que tenga la persona a partir de su lider o si es lider como tal */
	public function formatoActivo()
	{
		$ps = $_SESSION['persona'];
		$this->db->select(
			"ppl.id,
			ppl.id_formato idFormato,
			ppl.id_lider,
			ppl.estado estadol,
			ppe.estado estadoe,
			na.valor area,
			na.id_aux idaux,
			na.valory ruta_img,
			na.valorx descripcion"
		);
		$this->db->from("plan_accion_permisos_lideres ppl");
		$this->db->join("plan_accion_permisos_equipos ppe", "ppl.id_lider = ppe.id_lider AND ppl.estado = 1 AND ppe.estado = 1 OR ppl.id_lider = $ps AND ppl.estado = 1", "left");
		$this->db->join("valor_parametro na", "na.id = ppl.id_formato AND ppl.estado = 1 AND na.estado = 1", "left");
		$this->db->where("(ppe.id_director = $ps AND ppe.estado = 1 OR ppl.id_lider = $ps AND ppl.estado = 1)");
		$this->db->where("ppl.estado", 1);
		$this->db->group_by("ppl.id_formato");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* Funcion para corroborar entregable seteado en las solicitudes */
	public function check_entregable($idmeta)
	{
		$this->db->select("mpa.id_entregable entregable");
		$this->db->from("metas_plan_accion mpa");
		$this->db->where("mpa.id", $idmeta);
		$this->db->where("mpa.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	/* Funcion para verificar que se hayan completado todos los campos de una meta como cronograma, responsables y etc.s */
	public function verificarDatosMetas($idm)
	{
		$this->db->select(
			"mpa.id,
			mpa.id_meta_institucional idmi,
			mpa.id_formato idFormato,
			mpa.id_area idArea,
			mpa.id_reto idReto,
			mpa.id_plan_desarrollo idPlanDesarrollo,
			mpa.id_indicador_estrategico idIndicadorEst,
			mpa.id_indicador_operativo idIndicadorOp,
			mpa.id_programa idPrograma,
			mpa.id_recomendacion idRecomendacion,
			mpa.meta_plan_accion metaPlanAccion,
			mpa.indicador_operativo indicadorOp,
			mpa.cifra_referencia cifraRef,
			mpa.meta,
			mpa.nombre_accion,
			mpa.id_entregable idEntregable,
			group_concat(distinct fpa.id_factor separator '; ') factores_seleccionados,
			group_concat(distinct pac.id_caracteristica separator '; ') caracteristicas_factor,
			group_concat(distinct rpa.id_responsable separator '; ') accion_responsables,
			group_concat(distinct cpa.item separator '; ') meta_cronograma,
			SUM(distinct cpa.cantidad) totalAlcanzar,
			group_concat(distinct paa.accion separator '; ') meta_docs_soporte,
			ppa.id_categoria categoria_presupuestos,
			ppa.id_tipo tipo_prespuesto,
			ppa.id_item item_presupuestos,
			ppa.descripcion descripcion_presupuesto,
			ppa.valor_solicitado presupuesto_valor_solicitado",
			false
		);
		$this->db->from("metas_plan_accion mpa");
		$this->db->join("factores_plan_accion fpa", "fpa.id_meta = mpa.id AND fpa.estado = 1", "left");
		$this->db->join("plan_accion_caracteristicas pac", "pac.id_meta = mpa.id AND pac.estado = 1", "left");
		$this->db->join("responsables_plan_accion rpa", "rpa.id_meta = mpa.id AND rpa.estado = 1", "left");
		$this->db->join("cronograma_plan_accion cpa", "cpa.id_meta = mpa.id AND cpa.id_meta = $idm AND mpa.estado = 1 AND cpa.estado = 1", "left");
		$this->db->join("plan_accion_acciones paa", "paa.id_cronograma = cpa.id AND paa.estado = 1", "left");
		$this->db->join("presupuestos_plan_accion ppa", "ppa.id_meta = mpa.id AND ppa.estado = 1", "left");
		$this->db->where("mpa.id", $idm);
		$this->db->where("mpa.estado", 1);
		$this->db->group_by("mpa.id");
		$this->db->group_by("cpa.cantidad");
		$query = $this->db->get();
		return $query->row();
	}

	public function sumacantidad($idm)
	{
		$this->db->select("SUM(cpa.cantidad) cantidad");
		$this->db->from("cronograma_plan_accion cpa");
		$this->db->where("cpa.id_meta", $idm);
		$this->db->where("cpa.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	/* Verificar las recomendaciones de programas acreditados */
	public function verificarRecomendacion($idm)
	{
		$this->db->select("prp.id, prp.id_meta, prp.id_recomendacion idreco, pp.vp_principal_id idPrograma");
		$this->db->from("plan_accion_recomendaciones_programas prp");
		$this->db->join("permisos_parametros pp", "pp.vp_secundario_id = prp.id_recomendacion AND pp.estado = 1 AND prp.estado = 1");
		$this->db->where("prp.id_meta", $idm);
		$this->db->where("prp.estado", 1);
		$this->db->where("(prp.id_usuario_elimina IS NULL)");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function programaAsignado($idReco)
	{
		$this->db->select("pp.vp_principal_id idPrograma, pp.vp_secundario_id idReco");
		$this->db->from("permisos_parametros pp");
		$this->db->where("pp.vp_secundario_id", $idReco);
		$this->db->where("pp.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	/* Verificar aspectos positivos */
	public function verificarAspectos($idm)
	{
		$this->db->select("pap.id, pap.id_meta, pap.id_aspecto idasp, pp.vp_principal_id idPrograma");
		$this->db->from("plan_accion_aspectos_positivos pap");
		$this->db->join("permisos_parametros pp", "pp.vp_secundario_id = pap.id_aspecto AND pp.estado = 1 AND pap.estado = 1");
		$this->db->where("pap.id_meta", $idm);
		$this->db->where("pap.estado", 1);
		$this->db->where("(pap.id_usuario_elimina IS NULL)");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function aspectoPrograma($idasp)
	{
		$this->db->select("pp.vp_principal_id idPrograma, pp.vp_secundario_id idAsp");
		$this->db->from("permisos_parametros pp");
		$this->db->where("pp.vp_secundario_id", $idasp);
		$this->db->where("pp.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	public function recoPrograma($idreco)
	{
		$this->db->select("pp.vp_principal_id idPrograma, pp.vp_secundario_id idReco");
		$this->db->from("permisos_parametros pp");
		$this->db->where("pp.vp_secundario_id", $idreco);
		$this->db->where("pp.estado", 1);
		$query = $this->db->get();
		return $query->row();
	}

	/* Consulta para generar los datos y exportar en excel, PDA DB de programas */
	public function generarDatosDBPro($formato = '')
	{
		$ps = $_SESSION['persona'];
		$admin = false;
		$_SESSION['perfil'] == "Per_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro" ? $admin = true : $admin = false;

		$this->db->select(
			"mpa.id codigo,
			CONCAT(ur.nombre,' ', ur.apellido,' ',ur.segundo_apellido) usuario_registra,
			sap.valor cargo_sap,
			ae.valor area_estrategica,
			IF(rto.valor IS NULL, 'N/D', rto.valor) reto,
			IF(pd.valor IS NULL, 'N/D', pd.valor) meta_plan_desarrollo,
			IF(ie.valor IS NULL, 'N/D', ie.valor) indicador_estaretgico,
			mpa.meta_plan_accion meta_PA,
			IF(mpa.indicador_operativo IS NULL, 'N/D', mpa.indicador_operativo) indicador_operativo,
			IF(ioo.valor IS NULL, 'N/D', ioo.valor) tipo_indicador_operativo,
			IF(mpa.cifra_referencia IS NULL, 0, mpa.cifra_referencia) cifra_referencia,
			IF(mpa.meta IS NULL, 'N/D', mpa.meta) meta,
			IF(mpa.nombre_accion IS NULL, 'N/D', mpa.nombre_accion) nombre_accion,
			
			#Factores
			(
			#Factores o lineamientos de acreditacion
			
			SELECT 
			GROUP_CONCAT(DISTINCT fn.valor SEPARATOR'; ')
			
			FROM `metas_plan_accion` AS mpad
			
			#Join de factores_plan_accion
			LEFT JOIN `factores_plan_accion` fpa
			ON fpa.id_meta = mpad.id AND fpa.estado = 1
			
			LEFT JOIN `valor_parametro` fn
			ON fn.id = fpa.id_factor AND fn.estado = 1 AND fpa.estado = 1
			
			WHERE mpa.id = mpad.id AND mpad.estado = 1 LIMIT 1
			) factores,
			
			
			#Caracteristicas de ese factor seleccionado
			(
			SELECT 
			GROUP_CONCAT(
				DISTINCT
				CONCAT_WS(
							': ',
							(SELECT	fn.valora FROM `factores_plan_accion` fpad WHERE fpad.id_meta = fpa.id_meta AND fpa.estado = 1 AND mpa.estado = 1 LIMIT 1),
							(SELECT GROUP_CONCAT(DISTINCT nc.valor SEPARATOR ', ') FROM `valor_parametro` nc WHERE nc.id = pac.id_caracteristica LIMIT 1)
					)
					SEPARATOR '; '
			)
			
			FROM `metas_plan_accion` AS mpad
			
			#Join de factores_plan_accion
			LEFT JOIN `factores_plan_accion` fpa
			ON fpa.id_meta = mpad.id AND fpa.estado = 1
			
			LEFT JOIN `valor_parametro` fn
			ON fn.id = fpa.id_factor AND fn.estado = 1 AND fpa.estado = 1
			
			#Caratcteristicas del factor seleccionado
			LEFT JOIN `plan_accion_caracteristicas` pac
			ON pac.id_meta = fpa.id_meta AND pac.estado = 1 AND fpa.estado = 1 
			
			LEFT JOIN `valor_parametro` cn
			ON cn.id = pac.id_caracteristica AND cn.estado = 1 AND pac.estado = 1
			
			WHERE mpa.id = mpad.id AND mpad.estado = 1 AND fn.valora = cn.valora AND cn.id = pac.id_caracteristica LIMIT 1
			) factor_caracteristicas,
			
			
			#Responsables
			(
			SELECT 
			GROUP_CONCAT(DISTINCT CONCAT(pr.nombre,' ',pr.apellido,' ',pr.segundo_apellido) SEPARATOR '; ')
			FROM `metas_plan_accion` AS mpad
			
			#Join de repsonsables
			LEFT JOIN `responsables_plan_accion` rpa
			ON rpa.id_meta = mpad.id AND rpa.estado = 1 AND mpad.estado = 1
			
			LEFT JOIN `personas` pr
			ON pr.id = rpa.id_responsable AND pr.estado = 1 AND rpa.estado = 1
			
			WHERE mpa.id = mpad.id AND mpad.estado = 1 AND rpa.id_responsable IS NOT NULL LIMIT 1
			) responsables,
			
			
			#Cronogramas
			(
			SELECT 
			GROUP_CONCAT(DISTINCT CONCAT(itm.valorx,' ',cpa.codigo_item) SEPARATOR '; ') trimestre
			
			FROM `metas_plan_accion` AS mpad
			
			#Join de tabla de cronogramas
			LEFT JOIN `cronograma_plan_accion` cpa
			ON cpa.id_meta = mpad.id AND cpa.estado = 1 AND mpad.estado = 1
			
			#Join de valor parametro para traer el nombre de trimestre
			LEFT JOIN `valor_parametro` itm
			ON itm.id = cpa.item AND cpa.estado = 1 AND itm.estado = 1
			
			#Join de docs soportes
			LEFT JOIN `plan_accion_acciones` paa
			ON mpad.id = paa.id_meta AND mpad.estado = 1 AND paa.estado = 1
			
			WHERE mpa.id = mpad.id AND mpad.estado = 1 AND paa.id_cronograma = cpa.id LIMIT 1
			) trimestre,
			
			
			#Cantidad
			(
			#Datos de cronograma
			
			SELECT 
			IF(
			GROUP_CONCAT(
				DISTINCT
				CONCAT_WS(
							'; ',
							(SELECT	GROUP_CONCAT(cpadd.cantidad SEPARATOR '; ') FROM `cronograma_plan_accion` cpadd WHERE cpa.id_meta = cpadd.id_meta AND cpadd.estado = 1 AND cpa.estado = 1 LIMIT 1)
					)
					SEPARATOR '; '
			) = '', 'x',
			GROUP_CONCAT(
				DISTINCT
				CONCAT_WS(
							'; ',
							(SELECT	GROUP_CONCAT(cpadd.cantidad SEPARATOR '; ') FROM `cronograma_plan_accion` cpadd WHERE cpa.id_meta = cpadd.id_meta AND cpadd.estado = 1 AND cpa.estado = 1 LIMIT 1)
					)
					SEPARATOR '; '
			)
			
			)
			
			FROM `metas_plan_accion` AS mpad
			
			#Join de tabla de cronogramas
			LEFT JOIN `cronograma_plan_accion` cpa
			ON cpa.id_meta = mpad.id AND cpa.estado = 1 AND mpad.estado = 1
			
			#Join de valor parametro para traer el nombre de trimestre
			LEFT JOIN `valor_parametro` itm
			ON itm.id = cpa.item AND cpa.estado = 1 AND itm.estado = 1
			
			#Join de docs soportes
			LEFT JOIN `plan_accion_acciones` paa
			ON mpad.id = paa.id_meta AND mpad.estado = 1 AND paa.estado = 1
			
			WHERE mpa.id = mpad.id AND mpa.estado = 1 AND paa.id_cronograma = cpa.id LIMIT 1
			) cantidad,
			
			#Docs soporte
			(
			#Datos de cronograma
			
			SELECT 
			GROUP_CONCAT(
				DISTINCT
				CONCAT_WS(
							': ',
							(SELECT	CONCAT(itm.valorx,' ',cpa.codigo_item) FROM `cronograma_plan_accion` cpad WHERE cpa.id_meta = cpad.id_meta AND cpad.estado = 1 AND cpa.estado = 1 LIMIT 1),
							(SELECT	GROUP_CONCAT(DISTINCT paaa.accion SEPARATOR ', ') FROM `plan_accion_acciones` paaa WHERE paaa.id_meta = mpa.id AND paaa.id_cronograma = cpa.id AND cpa.estado = 1 AND paaa.estado = 1 AND mpa.estado = 1 LIMIT 1)
					)
					SEPARATOR '; '
			)
			
			FROM `metas_plan_accion` AS mpad
			
			#Join de tabla de cronogramas
			LEFT JOIN `cronograma_plan_accion` cpa
			ON cpa.id_meta = mpad.id AND cpa.estado = 1 AND mpad.estado = 1
			
			#Join de valor parametro para traer el nombre de trimestre
			LEFT JOIN `valor_parametro` itm
			ON itm.id = cpa.item AND cpa.estado = 1 AND itm.estado = 1
			
			#Join de docs soportes
			LEFT JOIN `plan_accion_acciones` paa
			ON mpad.id = paa.id_meta AND mpad.estado = 1 AND paa.estado = 1
			
			WHERE mpa.id = mpad.id AND mpad.estado = 1 AND paa.id_cronograma = cpa.id LIMIT 1
			
			) nombre_docs_soporte,
			
			fto.valor formato,
			est.valor estado,

			IF(
				pnom.valor IS NULL,
				'No diligenciado',
				GROUP_CONCAT(DISTINCT CONCAT(pnom.id,': ',pnom.valor) SEPARATOR '; ')
				) programas_seleccionados,
				
				
				IF(
				
				rn.valor IS NULL,
				'No diligenciado',
				GROUP_CONCAT(
				DISTINCT
				CONCAT_WS(
				': ',
				(SELECT pp.vp_principal_id FROM `permisos_parametros` pp WHERE pp.vp_secundario_id = rn.id AND pp.estado = 1 AND prp.estado = 1 LIMIT 1),
				rn.valor
				)
				SEPARATOR '; '
				
				)
				
				) recomendaciones_programa,
				
				IF(
				
				apn.valor IS NULL,
				'No diligenciado',
				GROUP_CONCAT(
				DISTINCT
				CONCAT_WS(
				': ',
				(SELECT ppp.vp_principal_id FROM `permisos_parametros` ppp WHERE ppp.vp_secundario_id = apn.id AND ppp.estado = 1 AND pap.estado = 1 LIMIT 1),
				apn.valor
				)
				SEPARATOR '; '
				
				)
				
				) aspecto_positivo_programa,

				IF(
					rol.valor IS NULL,
					'No diligenciado',
					rol.valor
					) dependencia,

			mpa.fecha_registra,
			
			#Accion_institucional
			IF(
			mpa.id_meta_institucional IS NULL,
			'No Data',
			(SELECT mpad.nombre_accion FROM `metas_plan_accion` mpad WHERE mpa.id_meta_institucional = mpad.id AND mpa.estado = 1 AND mpad.estado = 1 LIMIT 1)
			) accion_institucional,

			#Codigo institucional
			IF(
			mpa.id_meta_institucional IS NULL,
			'No data',
			(SELECT mpad.id FROM `metas_plan_accion` mpad WHERE mpa.id_meta_institucional = mpad.id AND mpa.estado = 1 AND mpad.estado = 1 LIMIT 1)
			) codigo_institucional",
			false
		);

		$this->db->from("metas_plan_accion mpa");

		#Formato
		$this->db->join("valor_parametro fto", "fto.id = mpa.id_formato AND fto.estado = 1", "left");

		#Area estrategica
		$this->db->join("valor_parametro ae", "ae.id = mpa.id_area AND ae.estado = 1", "left");

		#Reto
		$this->db->join("valor_parametro rto", "rto.id = mpa.id_reto AND rto.estado = 1", "left");

		#Plan de desarrollo
		$this->db->join("valor_parametro pd", "pd.id = mpa.id_plan_desarrollo AND pd.estado = 1", "left");

		#Indicador estrategico
		$this->db->join("valor_parametro ie", "ie.id = mpa.id_indicador_estrategico AND ie.estado = 1", "left");

		#Tipo de indicador operativo
		$this->db->join("valor_parametro ioo", "ioo.id = mpa.id_indicador_operativo AND ioo.estado = 1", "left");

		#Usuario registra - Nombre
		$this->db->join("personas ur", "ur.id = mpa.id_usuario_registra AND ur.estado = 1", "left");

		#Cargo SAP
		$this->db->join("personas cs", "cs.id = mpa.id_usuario_registra AND ae.estado = 1 AND mpa.estado = 1", "left");
		$this->db->join("valor_parametro sap", "cs.id_cargo_sap = sap.id AND cs.estado = 1 AND sap.estado = 1", "left");

		#Estado de la meta
		$this->db->join("valor_parametro est", "est.id_aux = mpa.meta_estado AND est.estado = 1 AND mpa.estado = 1", "left");

		#Programas acreditados - Formato de programas
		$this->db->join("plan_accion_programas_acreditados ppac", "ppac.id_meta = mpa.id AND ppac.estado = 1 AND mpa.estado = 1", "left");
		$this->db->join("valor_parametro pnom", "pnom.id = ppac.id_programa AND pnom.estado = 1 AND ppac.estado = 1", "left");

		#Join de recomendaciones de programa
		$this->db->join("plan_accion_recomendaciones_programas prp", "prp.id_meta = mpa.id AND prp.estado = 1 AND mpa.estado = 1", "left");
		$this->db->join("valor_parametro rn", "rn.id = prp.id_recomendacion AND rn.estado = 1 AND prp.estado = 1", "left");

		#Join de aspectos positivos
		$this->db->join("plan_accion_aspectos_positivos pap", "pap.id_meta = mpa.id AND pap.estado = 1 AND mpa.estado = 1", "left");
		$this->db->join("valor_parametro apn", "apn.id = pap.id_aspecto AND apn.estado = 1 AND pap.estado = 1", "left");

		#Joins para roles
		$this->db->join("plan_accion_permisos_lideres ppl", "ppl.id_lider = mpa.id_usuario_registra AND ppl.estado = 1 AND mpa.estado = 1", "left");
		$this->db->join("plan_accion_permisos_equipos ppe", "ppe.id_director = mpa.id_usuario_registra AND ppe.estado = 1 AND mpa.estado = 1", "left");
		$this->db->join("valor_parametro rol", "rol.id = ppl.id_rol AND ppl.estado = 1 AND rol.estado = 1", "left");

		$this->db->where("mpa.estado", 1);

		if (!empty($formato)) $this->db->where("mpa.id_formato", $formato);

		$this->db->group_by("mpa.id");

		$query = $this->db->get();
		return $query->result_array();
	}

	public function generarDatosDBpresu($formato = '')
	{
		$ps = $_SESSION['persona'];
		$admin = false;
		$_SESSION['perfil'] == "Per_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin" || $_SESSION['perfil'] == "Plan_Accion_Admin_Pro" ? $admin = true : $admin = false;

		$this->db->select(
			"mpa.id codigo,
			cat.valor categoria_presupuesto,
			IF(tip.valor IS NULL, 'No Aplica', tip.valor) tipo_presupuesto,
			IF(itm.valor IS NULL, 'No Aplica', itm.valor) item_presupuesto,
			pla.descripcion,
			pla.valor_solicitado,
			IF(pla.valor_aprobado IS NULL, 0, pla.valor_aprobado) valor_aprobado,
			IF(pla.valor_ejecutado IS NULL, 0, pla.valor_ejecutado) valor_ejecutado,
			IF(pla.cuenta_sap IS NULL, 'No data', pla.cuenta_sap) cuenta_sap,
			IF(pla.estado_presupuesto IS NULL, 'No data', pla.estado_presupuesto) estado_presupuesto,

			#Dependencia
			IF(
			ppl.id IS NULL,
			(SELECT rol2.valor FROM `plan_accion_permisos_lideres` ppld LEFT JOIN `valor_parametro` rol2 ON rol2.id = ppld.id_rol AND ppld.estado = 1 AND rol2.estado = 1 WHERE ppe.id_lider = ppld.id_lider LIMIT 1),
			rol.valor
			) dependencia,

			CONCAT(res.nombre,' ',res.apellido,' ',res.segundo_apellido) usuario_registra,

			#Estado de la meta
			est.valor meta_estado,

			#Formato de la accion
			fto.valor formato",
			false
		);

		$this->db->from("metas_plan_accion mpa");
		
		#Join tabla de presupuestos
		$this->db->join("presupuestos_plan_accion pla", "pla.id_meta = mpa.id AND pla.estado = 1 AND mpa.estado = 1", "left");

		#Join para traer nombre de categorias
		$this->db->join("valor_parametro cat", "cat.id = pla.id_categoria AND cat.estado = 1 AND pla.estado = 1", "left");

		#Join para traer nombre de los tipos
		$this->db->join("valor_parametro tip", "tip.id = pla.id_tipo AND tip.estado = 1 AND pla.estado = 1", "left");

		#Join para traer nombre de los items //
		$this->db->join("valor_parametro itm", "itm.id = pla.id_item AND itm.estado = 1 AND pla.estado = 1", "left");

		#Join para personas
		$this->db->join("personas res", "res.id = pla.id_usuario_registra AND res.estado = 1 AND pla.estado = 1", "left");

		#Estado de la meta
		$this->db->join("valor_parametro est", "est.id_aux = mpa.meta_estado AND est.estado = 1 AND mpa.estado = 1", "left");

		#Joins para roles y traer dependencia
		$this->db->join("plan_accion_permisos_lideres ppl", "ppl.id_lider = pla.id_usuario_registra AND ppl.estado = 1 AND pla.estado = 1", "left");
		$this->db->join("plan_accion_permisos_equipos ppe", "ppe.id_director = pla.id_usuario_registra AND ppe.estado = 1 AND pla.estado = 1", "left");
		$this->db->join("valor_parametro rol", "rol.id = ppl.id_rol AND ppl.estado = 1 AND rol.estado = 1", "left");

		#Formato de la accion
		$this->db->join("valor_parametro fto", "fto.id = mpa.id_formato AND fto.estado = 1", "left");

		$this->db->where("mpa.estado", 1);
		$this->db->where("pla.estado", 1);
		$this->db->where("pla.id_categoria IS NOT NULL");

		if (!empty($formato)) $this->db->where("mpa.id_formato", $formato);

		$this->db->order_by("mpa.id", "ASC");
		$query = $this->db->get();
		return $query->result_array();
	}

	/* public function CAMBIAR()
	{
		$this->db->select("");
		$this->db->from("");
		$this->db->like("CONCAT(p.nombre,' ',p.apellido,' ',p.segundo_apellido)", $dato_buscado);
		$this->db->where("");
		$query = $this->db->get();
		return $query->result_array();
	} */
}
