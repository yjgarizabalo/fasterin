const ruta = `${Traer_Server()}index.php/evaluacion_control/`;
let callbak_activo = (resp) => { };
let callbak_ = (resp) => { };
let info_solicitud = [];
let id_solicitud = null;
let id_persona = null;
let peso_porcentaje = null;
let peso_estado = null;
let data_preguntas = [];
let data_preguntas_metas = [];
let data_competencia = [];
let data_tipo_evaluador = [];
let index_tipo_evaluador = null;
let data_indicadores = [];
let id_jefe = null;
let id_coevaluado = null;
let id_evaluado = null;
let data_actas = [];
let resultado_competencia = [];
let idcompetencia = null;
let periodo_eval = null;
let id_asignacion_persona = null;

$(document).ready(function () {
	$('input[type="text"]').attr('maxlength', '500');
	const administrar_modulo = tipo => {
		if (tipo == 'solicitudes') {
			listar_solicitudes();
			$("#container_solicitudes").fadeIn(1000);
			$("#menu_principal").css("display", "none");
		} else if (tipo == 'menu') {
			$("#menu_principal").fadeIn(1000);
			$("#container_solicitudes").css("display", "none");
		}
	}

	$('.regresar_menu').click(() => {
		administrar_modulo('menu');
		listar_solicitudes();
	});

	$("#btn_regresar").click(() => regresar());

	$('#listado_solicitudes').click(() => administrar_modulo('solicitudes'));

	$("#btn_filtros").click(() => $("#Modal_filtro").modal());

	$("#form_filtro").submit(e => {
		e.preventDefault();
		filtrar_solicitudes();
		return false;
	});

	$("#btn_limpiar").click(() => {
		$("#form_filtro").get(0).reset();
		let tipo = $("#Modal_filtro select[name='tipo']").val();
		let estado = $("#Modal_filtro select[name='estado']").val();
		let fecha_i = $("#Modal_filtro input[name='fecha_i']").val();
		let fecha_f = $("#Modal_filtro input[name='fecha_f']").val();
		let periodo = $("#Modal_filtro input[name='filtro_periodo']").val();
		data = {
			'tipo': tipo,
			'estado': estado,
			'fecha_i': fecha_i,
			'fecha_f': fecha_f,
			'periodo': periodo
		}
		// $("#btn_exportar").attr("href", `${Traer_Server()}index.php/evaluacion/exportar_evaluacion/${0}/${0}/${0}/${0}/${0}/${0}`);
		// $("#btn_resultados").attr("href", `${Traer_Server()}index.php/evaluacion/exportar_resultados/${0}/${0}/${0}/${0}`);
		listar_solicitudes(0, data);
	});

	$("#btnConfiguraciones").click(() => {
		$("#menu_administrar li.metodo_evaluacion").trigger('click');
		listar_valorparametro(214, 'tabla_metodo_evaluacion');
		$("#modal_administrar").modal();
	});

	$("#menu_administrar li").click(function () {
		$("#menu_administrar li").removeClass('active');
		$(this).addClass('active');
		if ($(this)[0].classList.contains('metodo_evaluacion')) {
			$("div.adm_proceso").hide();
			$("div.metodo_evaluacion").fadeIn();
			listar_valorparametro(214, 'tabla_metodo_evaluacion');
		} else if ($(this)[0].classList.contains('tipo_evaluacion')) {
			$("div.adm_proceso").hide();
			$("div.tipo_evaluacion").fadeIn();
			listar_valorparametro(215, 'tabla_tipo_evaluacion');
		} else if ($(this)[0].classList.contains('categoria')) {
			$("div.adm_proceso").hide();
			$("div.categoria").fadeIn();
			listar_valorparametro(216, 'tabla_categoria');
		} else if ($(this)[0].classList.contains('pasos')) {
			$("div.adm_proceso").hide();
			$("div.pasos").show();
			listar_valorparametro(217, 'tabla_pasos');
		} else if ($(this)[0].classList.contains('preguntas')) {
			$("div.adm_proceso").hide();
			$("div.preguntas").show();
			listar_valorparametro(218, 'tabla_preguntas');
		} else if ($(this)[0].classList.contains('personas')) {
			$("div.adm_proceso").hide();
			$("#s_persona").html('Seleccione Persona');
			$("div.asignacion_personas").show();
			listar_asignacion_personas('');
		}
	});

	$(".add_metodo_evaluacion").click(() => {
		$("#form_valor_parametro").get(0).reset();
		$(".valory").hide();
		$(".valorz").hide();
		$(".apreciacion").hide();
		$("#modal_valor_parametro").modal();
		callbak_activo = (resp) => guardar_parametro(214, 'tabla_metodo_evaluacion');
	});

	$(".add_tipo_evaluacion").click(() => {
		$("#form_valor_parametro").get(0).reset();
		$(".valory").hide();
		$(".valorz").hide();
		$(".apreciacion").hide();
		$(".nombre_parametro").html('Peso porcentual');
		$("#modal_valor_parametro").modal();
		callbak_activo = (resp) => guardar_parametro(215, 'tabla_tipo_evaluacion');
	});

	$(".add_categoria").click(() => {
		$("#form_valor_parametro").get(0).reset();
		$(".valory").hide();
		$(".valorz").hide();
		$(".apreciacion").hide();
		$("#modal_valor_parametro").modal();
		callbak_activo = (resp) => guardar_parametro(216, 'tabla_categoria');
	});

	$(".add_paso").click(() => {
		$("#form_valor_parametro").get(0).reset();
		$(".valory").hide();
		$(".valorz").hide();
		$(".apreciacion").show();
		$("#modal_valor_parametro").modal();
		callbak_activo = (resp) => guardar_parametro(217, 'tabla_pasos');
	});

	$(".add_pregunta").click(() => {
		$("#form_valor_parametro").get(0).reset();
		$(".valory").show();
		$(".valorz").show();
		$(".apreciacion").hide();
		$(".nombre_parametro").html('Número');
		$("#modal_valor_parametro").modal();
		callbak_activo = (resp) => guardar_parametro(218, 'tabla_preguntas');
	});

	$("#form_valor_parametro").submit(e => {
		callbak_activo();
		return false;
	});

	$("#nueva_solicitud").click(() => {
		$("#form_nueva_solicitud").get(0).reset();
		$(".nombre_completo").html('');
		$(".cargo").html('');
		callbak_activo = (data) => {
			const { id, identificacion, nombre_completo, cargo } = data;
			id_persona = identificacion;
			$(".nombre_completo").html(nombre_completo);
			$(".cargo").html(cargo);
			$("#modal_buscar_persona").modal('hide');
		};
		$("#modal_nueva_solicitud").modal();
	});

	$(".input_buscar_persona").click(() => {
		$("#txt_dato_buscar").val('');
		buscar_persona('', callbak_activo);
		$("#modal_buscar_persona").modal();
	});

	$("#btn_buscar_jefe").click(() => {
		$("#txt_dato_buscar").val('');
		callbak_activo = (data) => {
			const { identificacion, nombre_completo } = data;
			id_jefe = identificacion;
			$("#txt_nombre_jefe").val(nombre_completo);
			$("#modal_buscar_persona").modal('hide');
		};
		buscar_persona('', callbak_activo);
		$("#modal_buscar_persona").modal();
	});

	$("#btn_buscar_coevaluado").click(() => {
		$("#txt_dato_buscar").val('');
		callbak_activo = (data) => {
			const { identificacion, nombre_completo } = data;
			id_coevaluado = identificacion;
			$("#txt_nombre_coevaluado").val(nombre_completo);
			$("#modal_buscar_persona").modal('hide');
		};
		buscar_persona('', callbak_activo);
		$("#modal_buscar_persona").modal();
	});

	$('#form_buscar_persona').submit(() => {
		let dato = $("#txt_dato_buscar").val();
		buscar_persona(dato, callbak_activo);
		return false;
	});

	$('#form_nueva_solicitud').submit(() => {
		guardar_solicitud();
		return false;
	});

	$(".btn_inicio_encuesta").click(async () => {
		$(".btn_inicio_encuesta").hide('fast');
		$("#container_encuesta").empty();
		continuar_encuesta(id_solicitud);
	});

	$(".btn_agil").click(() => {
		$(".btn_agil").attr("href", `${Traer_Server()}index.php`);
	});

	$("#menu_admin_indicadores li").click(function () {
		$("#menu_admin_indicadores li").removeClass('active');
		$(this).addClass('active');
		if ($(this)[0].classList.contains('evaluacion')) {
			$("div.resp").hide();
			$("div.preguntas").fadeIn();
			evaluacion_respuestas_indicadores(id_solicitud, id_evaluado, '4', 'get_evaluacion_respuestas', 'tabla_preguntas_evaluacion');
		} else if ($(this)[0].classList.contains('indicadores')) {
			$("div.resp").hide();
			$("div.preguntas_indicadores").fadeIn();
			evaluacion_respuestas_indicadores(id_solicitud, id_evaluado, '', 'get_respuestas_indicadores', 'tabla_preguntas_indicadores');
		}else if ($(this)[0].classList.contains('formacion_Esc')) {
			$("div.resp").hide();
			$("div.preguntas_formacion_Esc").fadeIn();
			evaluacion_respuestas_formacionForm(id_evaluado,'get_respuestas_formacionEsc', 'tabla_formacion_Esc');
		}else if ($(this)[0].classList.contains('funciones')) {
			$("div.resp").hide();
			$("div.preguntas_funciones").fadeIn();
			evaluacion_respuestas_formacionForm(id_evaluado,'get_respuestas_funciones', 'tabla_funciones');
		}
	});

	$("#btnNotificaciones").click(() => {
		$("#Modal_notificacion").modal();
	});

	$("#form_notificacion input[name='vb_cal']").click(function () {
		let radioValue = $("#form_notificacion input[name='vb_cal']:checked").val();
		if (radioValue == 1) {
			$("#form_notificacion select[name='id_estado']").prop('disabled', true);
			$("#form_notificacion select[name='id_estado']").val('');
		} else {
			$("#form_notificacion select[name='id_estado']").prop('disabled', false);
		}
	});

	$('#form_notificacion').submit(() => {
		enviar_notificaciones();
		return false;
	});

	$("#agregar_pesonal").click(() => {
		$("#txt_dato_buscar").val('');
		callbak_activo = (data) => {
			const { identificacion, nombre_completo } = data;
			msj_confirmacion('¿ Estas Seguro ?', `Desea agregar a ${nombre_completo}?`, () => guardar_persona_acargo(id_solicitud, identificacion));
		};
		buscar_persona('', callbak_activo);
		$("#modal_buscar_persona").modal();
	});

	$(".btn_ver_resultado").click(() => {
		$("#menu_resultados li").removeClass('active');
		$(".detalles").addClass('active');
		$("div.resultado_tevaluador").hide();
		$("div.resultado_detalle").fadeIn();
		get_resultados_detalles(id_solicitud);
		$("#modal_resultados").modal();
	});

	$("#menu_resultados li").click(function () {
		$("#menu_resultados li").removeClass('active');
		$(this).addClass('active');
		if ($(this)[0].classList.contains('detalles')) {
			$(".detalles").addClass('active');
			$("div.resultado_tevaluador").hide();
			$("div.resultado_detalle").fadeIn();
			get_resultados_detalles(id_solicitud);
		} else if ($(this)[0].classList.contains('tipoEvaluador')) {
			$(".tipoEvaluador").addClass('active');
			$("div.resultado_detalle").hide();
			$("div.resultado_tevaluador").fadeIn();
			get_resultados_tipoevaluador(id_solicitud, 'tabla_resultados_tevaluador');
		}
	});

	$("#btn_sugerencias").click(() => {
		let { id_solicitud_evaluado } = info_solicitud;
		listar_sugerencias_formacion(id_evaluado, id_solicitud_evaluado);
		$("#modal_sugerencias").modal();
	});

	$("#agregar_sugerencia").click(() => {
		let { id_solicitud_evaluado } = info_solicitud;
		$("#form_sugerencias").get(0).reset();
		callbak_activo = (resp) => guardar_sugerencias(id_solicitud_evaluado);
		$("#modal_add_sugerencias").modal();
	});

	$("#btn_mejora").click(() => {
		let { id_solicitud_evaluado } = info_solicitud;
		listar_oportunidades_mejora(id_evaluado, id_solicitud_evaluado);
		$("#modal_mejoras").modal();
	});

	$("#agregar_compromiso").click(() => {
		let { identificacion, id_solicitud_evaluado } = info_solicitud;
		$("#form_compromisos").get(0).reset();
		callbak_activo = (resp) => guardar_compromisos(identificacion, id_solicitud_evaluado);
		$("#modal_add_compromiso").modal();
	});

	$("#btn_detalle_resultados").click(() => {
		resultado_competencia = [];
		let { id_solicitud_evaluado, identificacion, periodo } = info_solicitud;
		$("#btn_competencias").attr("href", `${Traer_Server()}index.php/evaluacion/exportar_competencias/${identificacion}/${id_solicitud_evaluado}`);
		get_detalle_resultados(identificacion, id_solicitud_evaluado, periodo);
		$("#menu_resultados_acta li").removeClass('active');
		$(".detalles_acta").addClass('active');
		$("div.resultado_metas_acta").hide();
		$("div.resultado_tipoevaluador_acta").hide();
		$("div.resultado_detalle_acta").fadeIn();
		$("#modal_detalle_resultados").modal();
	});

	$("#menu_resultados_acta li").click(function () {
		let { id_solicitud_evaluado, identificacion, periodo } = info_solicitud;
		$("#menu_resultados_acta li").removeClass('active');
		$(this).addClass('active');
		if ($(this)[0].classList.contains('detalles_acta')) {
			$(".detalles_acta").addClass('active');
			$("div.resultado_metas_acta").hide();
			$("div.resultado_tipoevaluador_acta").hide();
			$("#btn_resul_competencia").fadeIn();
			$("div.resultado_detalle_acta").fadeIn();
			get_detalle_resultados(identificacion, id_solicitud_evaluado, periodo);
		} else if ($(this)[0].classList.contains('metas_acta')) {
			$(".metas_acta").addClass('active');
			$("div.resultado_detalle_acta").hide();
			$("div.resultado_tipoevaluador_acta").hide();
			$("#btn_resul_competencia").hide();
			$("div.resultado_metas_acta").fadeIn();
			get_resultados_metas(id_solicitud, identificacion);
		} else if ($(this)[0].classList.contains('tipoevaluador_acta')) {
			$(".tipoevaluador_acta").addClass('active');
			$("div.resultado_metas_acta").hide();
			$("div.resultado_detalle_acta").hide();
			$("#btn_resul_competencia").hide();
			$("div.resultado_tipoevaluador_acta").fadeIn();
			get_resultados_tipoevaluador(id_solicitud_evaluado, 'tabla_resultados_tevaluador_acta');
		}
	});

	$("#form_sugerencias").submit(() => {
		callbak_activo();
		return false;
	});

	$("#form_compromisos").submit(() => {
		callbak_activo();
		return false;
	});

	$("#btn_resul_competencia").click(async () => {
		let { identificacion, id_solicitud_evaluado, periodo } = info_solicitud;
		let found = '';
		let i = null;
		let datos = await listar_detalle_resultados(identificacion, id_solicitud_evaluado, periodo);
		datos.forEach(elemento => {
			if (elemento.id == null) {
				found = resultado_competencia.find((row) => row.id_competencia === elemento.id_competencia);
				if (!found) i++;
			}
		});
		if (i) MensajeConClase(`Competencia(s) sin responder: ${i}. Para continuar debe marcar todas!.`, "info", "Oops.!");
		else guardar_resultado_competencias(identificacion, id_solicitud_evaluado);
	});

	$("#btnResultadoMasivo").click(() => {
		$("#tabla_evaluacion_resultados tbody").empty();
		$("#Modal_resultados").modal();
	});

	$("#generar_resultados").click(() => {
		let periodo = $("#form_resultados input[name='periodo']").val();
		if(!periodo) MensajeConClase(`Por favor digite el periodo!.`, "info", "Oops.!");
		else{
			msj_confirmacion('¿ Estas Seguro ?', `Desea generar resultados del periodo ${periodo}?.`, () =>{
				swal.close();
				$("#tabla_evaluacion_resultados tbody").empty();
				$("#evaluaciones_resultados").append(`<div class="cargando_data text-center"><img src="${Traer_Server()}/imagenes/loading.gif" style='width:5%;'><h3>Cargando...</h3></div>`);
				$("#modal_evaluacion_resultados").modal();
				const myTable = $("#tabla_evaluacion_resultados").DataTable({
					destroy: true,
					searching: true,
					processing: true,
					data: [],
					language: get_idioma(),
					dom: 'Bfrtip',
					buttons: [],
				});
				calcularResultadosMasivo('calcularResultadosMasivo');
				return false;
			});
		}
	});

	$("#resetear_resultados").click(() => {
		let periodo = $("#form_resultados input[name='periodo']").val();
		if(!periodo) MensajeConClase(`Por favor digite el periodo!.`, "info", "Oops.!");
		else{
			msj_confirmacion('¿ Estas Seguro ?', `Desea resetear resultados del periodo ${periodo}?.`, () =>{
				swal.close();
				$("#tabla_evaluacion_resultados tbody").empty();
				$("#evaluaciones_resultados").append(`<div class="cargando_data text-center"><img src="${Traer_Server()}/imagenes/loading.gif" style='width:5%;'><h3>Cargando...</h3></div>`);
				$("#modal_evaluacion_resultados").modal();
				const myTable = $("#tabla_evaluacion_resultados").DataTable({
					destroy: true,
					searching: true,
					processing: true,
					data: [],
					language: get_idioma(),
					dom: 'Bfrtip',
					buttons: [],
				});
				calcularResultadosMasivo('Resetear_resultados');
				return false;
			});
		}
	});

	$("#form_confirmar_acta").submit(() => {
		if (!$("#form_confirmar_acta input[name='calificacion']").is(':checked')) {
			MensajeConClase(`Por favor marque el nivel de satisfacción!.`, "info", "Oops.!");
		} else {
			pedir_firma(id_solicitud);
		}
		return false;
	});

	$("#btnConfiAsignaciones").click(() => {
		$("#menu_administrar_asignaciones li.personas").trigger('click');
		$("#s_persona").html('Seleccione Persona');
		$(".add_persona").addClass("oculto");
		listar_asignacion_personas('');
		$("#modal_administrar_asignaciones").modal();
	});

	// $("#menu_administrar_asignaciones li").click(function () {
	// 	$("#menu_administrar_asignaciones li").removeClass('active');
	// 	$(this).addClass('active');
	// 	if ($(this)[0].classList.contains('personas')) {
	// 		$("#s_persona").html('Seleccione Persona');
	// 		$(".add_persona").addClass("oculto");
	// 		listar_asignacion_personas('');
	// 		$("div.asignacion_indicadores").hide();
	// 		$("div.asignacion_personas").fadeIn();
	// 	} else if ($(this)[0].classList.contains('indicadores')) {
	// 		$("#s_persona_ind").html('Seleccione Persona');
	// 		$(".add_indicador").addClass("oculto");
	// 		listar_asignacion_indicadores('');
	// 		$("div.asignacion_personas").hide();
	// 		$("div.asignacion_indicadores").fadeIn();
	// 	}
	// });

	$('#s_persona').click(() => {
		$("#txt_dato_buscar").val('');
		callbak_activo = (data) => {
			const { identificacion, nombre_completo } = data;
			id_persona = identificacion;
			$("#s_persona").html(nombre_completo);
			$(".add_persona").removeClass("oculto");
			listar_asignacion_personas(id_persona);
			$("#modal_buscar_persona").modal('hide');
		};
		buscar_persona('', callbak_activo);
		$("#modal_buscar_persona").modal();
	});
	$('.add_persona').click(() => {
		$("#form_nueva_asignacion").get(0).reset();
		$(".nombre_completo").html('');
		$(".identificacion").html('');
		$(".cargo").html('');
		callbak_activo = (data) => {
			const { id, identificacion, nombre_completo, cargo } = data;
			id_evaluado = identificacion;
			$(".nombre_completo").html(nombre_completo);
			$(".identificacion").html(identificacion);
			$(".cargo").html(cargo);
			$("#modal_buscar_persona").modal('hide');
		};
		$("#modal_nueva_asignacion").modal();
	});
	$("#form_nueva_asignacion").submit(() => {
		guardar_asignacion_persona(id_evaluado, id_persona);
		return false;
	});

	$('.btn_formacion').click(async () => {
		let { id, id_evaluado } = await get_detalle_solicitud(id_solicitud);
		listar_planFormacion(id_evaluado);
		$("#modal_plan_formacion").modal();
	});

	$('.btn_planentramiento').click(() => {
		listar_planEntrenamiento();
	});

	$("#btninforme").click(function () {
		$("#Modal_informe").modal();
	});

	$(`#form_informe`).submit(e => {
		e.preventDefault();
		generar_informe();
		return false;
	});

	$("#btn_exportar").click(function () {
		$("#Modal_desc_reporte").modal();
	});

	$(`#form_desc_reporte`).submit(e => {
		e.preventDefault();
		let tipo = $("#Modal_desc_reporte select[name='tipo']").val();
		let estado = $("#Modal_desc_reporte select[name='estado']").val();
		let fecha_i = $("#Modal_desc_reporte input[name='fecha_i']").val();
		let fecha_f = $("#Modal_desc_reporte input[name='fecha_f']").val();
		let periodo = $("#Modal_desc_reporte input[name='filtro_periodo']").val();
		let id = 0;
		estado = estado ? estado : 'vacio';
		tipo = tipo ? tipo : 'vacio';
		fecha_i = fecha_i ? fecha_i : 0;
		fecha_f = fecha_f ? fecha_f : 0;
		periodo = periodo ? periodo : 0;
		window.location = `${Traer_Server()}index.php/evaluacion/exportar_evaluacion/${id}/${estado}/${tipo}/${fecha_i}/${fecha_f}/${periodo}`;
		return false;
	});

	$("#btnperiodo").click(async function () {
		let data = await get_periodo_activo(246);
		data.map(({ id, valor }) => {
			$("#form_periodo input[name='periodo']").val(valor);
			callbak_activo = (resp) => guardar_periodo_activo(id);
		});
		$("#modal_periodo_activo").modal();
	});

	$(`#form_periodo`).submit(e => {
		e.preventDefault();
		callbak_activo();
		return false;
	});

	 /* Para inputs numericos */
	 $('.input_numerico').on('keypress', function (e) {
		if (num_o_string("int", e.keyCode) == false) {
		  return false;
		}
	  });
});

const generar_informe = () => {
	let periodo = $("#form_informe input[name='filtro_periodo']").val();
	let metodo = $("#form_informe select[name='metodo']").val();
	let tipo_informe = $("#form_informe select[name='tipo_informe']").val();
	window.open(`${ruta}generar_informe/${periodo}/${metodo}/${tipo_informe}`);
}

const get_periodo_activo = (parametro) => {
	return new Promise(resolve => {
		let url = `${ruta}listar_valorparametro`;
		consulta_ajax(url, { parametro }, (resp) => {
			resolve(resp);
		});
	});
}

const guardar_periodo_activo = (id) => {
	let data = new FormData(document.getElementById("form_periodo"));
	data.append('id', id);
	enviar_formulario(`${ruta}guardar_periodo_activo`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const calcularResultadosMasivo = (control) => {
	let data = new FormData(document.getElementById("form_resultados"));
	enviar_formulario(`${ruta}${control}`, data, resp => {
		$('.cargando_data').fadeIn(1000).html(resp);
		$(`#tabla_evaluacion_resultados tbody`).off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td .detalle_resultado');
		const myTable = $("#tabla_evaluacion_resultados").DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{ data: 'metodo' },
				{ data: 'evaluado' },
				{ data: 'cc_evaluado' },
				{
					"render": function (resp, type, full, meta) {
						if (full.resultado) return full.resultado;
						return '--';
					}
				},
				{ data: 'valoracion' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});
		$('#tabla_evaluacion_resultados tbody').on('click', 'tr', function () {
			$("#tabla_evaluacion_resultados tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_evaluacion_resultados tbody').on('dblclick', 'tr', function () {
			$("#tabla_evaluacion_resultados tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_evaluacion_resultados tbody').on('click', 'tr td .detalle_resultado', function () {
			let { id_solicitud_evaluado } = myTable.row($(this).parent()).data();
			id_solicitud = id_solicitud_evaluado;
			$("#menu_resultados li").removeClass('active');
			$(".detalles").addClass('active');
			$("div.resultado_tevaluador").hide();
			$("div.resultado_detalle").fadeIn();
			get_resultados_detalles(id_solicitud);
			$("#modal_resultados").modal();
		});

		listar_solicitudes();
	});
}

const listar_detalle_resultados = async (id_evaluado, id_solicitud_evaluado, periodo) => {
	return new Promise(resolve => {
		let url = `${ruta}get_detalle_resultados`;
		consulta_ajax(url, { id_evaluado, id_solicitud_evaluado, periodo }, resp => {
			resolve(resp);
		});
	});
}

const filtrar_solicitudes = () => {
	let tipo = $("#Modal_filtro select[name='tipo']").val();
	let estado = $("#Modal_filtro select[name='estado']").val();
	let fecha_i = $("#Modal_filtro input[name='fecha_i']").val();
	let fecha_f = $("#Modal_filtro input[name='fecha_f']").val();
	let periodo = $("#Modal_filtro input[name='filtro_periodo']").val();
	data = {
		'tipo': tipo,
		'estado': estado,
		'fecha_i': fecha_i,
		'fecha_f': fecha_f,
		'periodo': periodo
	}
	estado = estado ? estado : 'vacio';
	tipo = tipo ? tipo : 'vacio';
	fecha_i = fecha_i ? fecha_i : 0;
	fecha_f = fecha_f ? fecha_f : 0;
	periodo = periodo ? periodo : 0;
	let id = 0;
	// $("#btn_exportar").attr("href", `${Traer_Server()}index.php/evaluacion/exportar_evaluacion/${id}/${estado}/${tipo}/${fecha_i}/${fecha_f}/${periodo}`);
	// $("#btn_resultados").attr("href", `${Traer_Server()}index.php/evaluacion/exportar_resultados/${id}/${estado}/${fecha_i}/${fecha_f}`);
	listar_solicitudes(0, data);
}

const pintar_datos_combo = (datos, combo, mensaje, sele = '') => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach(elemento => {
		$(combo).append(`<option value='${elemento.id}'> ${elemento.valor}</option>`);
	});
	$(combo).val(sele);
}

const  obtener_valor_parametro = async (id) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_valor_parametro`;
		consulta_ajax(url, { id }, resp => {
			resolve(resp);
		});
	});
}

const obtener_permisos_parametros = async (id) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_permisos_parametros`;
		consulta_ajax(url, { id }, resp => {
			resolve(resp);
		});
	});
}

const get_detalle_solicitud = async (id_solicitud) => {
	return new Promise(resolve => {
		let url = `${ruta}get_detalle_solicitud`;
		consulta_ajax(url, { id_solicitud }, resp => {
			resolve(resp);
		});
	});
}

const obtener_preguntas = async (id_tipo_evaluador, id_aux, id_solicitud, ind_evaluado = '', periodo_eval = '') => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_preguntas`;
		consulta_ajax(url, { id_tipo_evaluador, id_aux, id_solicitud, ind_evaluado, periodo_eval }, resp => {
			resolve(resp);
		});
	});
}

const pintar_permisos_parametros = async (id, combo, mensaje) => {
	let datos = await obtener_permisos_parametros(id);
	pintar_datos_combo(datos, combo, mensaje);
}

const obtener_tipo_evaluador = async (id_solicitud) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_tipo_evaluador`;
		consulta_ajax(url, { id_solicitud }, resp => {
			resolve(resp);
		});
	});
}

const obtener_indicadores = async (id_solicitud, estado = '', id_estado = null) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_indicadores`;
		consulta_ajax(url, { id_solicitud, estado, id_estado }, resp => {
			resolve(resp);
		});
	});
}

const obtener_preguntas_indicador = async (id_evaluado, periodo) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_preguntas_indicador`;
		consulta_ajax(url, { id_evaluado, periodo }, resp => {
			resolve(resp);
		});
	});
}

const obtener_funciones = async (id_evaluado, periodo) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_funciones`;
		consulta_ajax(url, { id_evaluado, periodo }, resp => {
			resolve(resp);
		});
	});
}

const obtener_formacion_esencial = async (id_evaluado, periodo) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_formacion_esencial`;
		consulta_ajax(url, { id_evaluado, periodo }, resp => {
			resolve(resp);
		});
	});
}

const personal_actas = async (id_solicitud) => {
	return new Promise(resolve => {
		let url = `${ruta}listar_personal_actas`;
		consulta_ajax(url, { id_solicitud }, resp => {
			resolve(resp);
		});
	});
}

const bar_estado = () => {
	let progreso = 0;
	data_tipo_evaluador.forEach((elemento, indice) => {
		if (indice < index_tipo_evaluador) progreso = progreso + parseInt(elemento.porcentaje);
	});
	$('#bar_estado').css('width', progreso + '%');
	$(".text_barra").html(progreso + '%');
}

const bar_estado_actas = (data_actas) => {
	let progreso = 0;
	let i = 0;
	data_actas.forEach(elemento => {
		if (elemento.acta_retro == 1) i++;
	});
	progreso = Math.round((i / data_actas.length) * 100);
	$('#bar_estado_acta').css('width', progreso + '%');
	$(".text_barra_acta").html(progreso + '%');

	if (i === data_actas.length) {
		$("#contenido_actas").empty();
		$("#contenido_actas").append(`<div class="col-md-12">
			<img src="${Traer_Server()}/imagenes/final.png" alt="..." style='width:30%;'> 
			<h4><b>RETROALIMENTACIÓN FINALIZADA</b></h4>
			</br>
			<a href="${Traer_Server()}index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
		</div>`);
	}
}

const bar_estado_indicadores = async (id_solicitud) => {
	let progreso = 0;
	let indicadores = await obtener_indicadores(id_solicitud);
	let cant = indicadores.length;
	let resp = await obtener_indicadores(id_solicitud, '', 'Eval_Ter');
	let i = resp.length;
	progreso = (i / cant) * 100;
	var porcentaje = Math.round(progreso);
	$('#bar_estado').css('width', porcentaje + '%');
	$(".text_barra").html(porcentaje + '%');
}

const guardar_respuestas = (control, data_respuestas, tipo = '', id_asignacion = 0) => {
	const data = { data_respuestas: data_respuestas, id_solicitud: id_solicitud, tipo_evaluador: index_tipo_evaluador, tipo: tipo, id_asignacion_persona: id_asignacion };
	consulta_ajax(`${ruta}${control}`, data, resp => {
		let { tipo, mensaje, titulo, id_estado } = resp;
		if (tipo == 'success') {
			$(`#container_encuesta`).empty();
			id_asignacion_persona = 0;
			if (id_estado == 'Eval_Ter') {
				$(".nombre_tipo_evaluador").html('');
				$(".nombre_evaluado").html('');
				$("#container_encuesta").append(`<div class="col-md-12 text-center">
					<img src="${Traer_Server()}/imagenes/final.png" alt="..." style='width:30%;'> 
					<h4><b>EVALUACIÓN FINALIZADA</b></h4>
					</br><a href="${Traer_Server()}index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
					</div>`);
				$('#bar_estado').css('width', '100%');
				$(".text_barra").html('100%');				
			} else continuar_encuesta(id_solicitud);
		} else {
			$("#btn_siguiente").attr("disabled", false);
		}

		MensajeConClase(mensaje, tipo, titulo);
	});
}

const guardar_respuestas_indicadores = (data_respuesta_metas = null, data_respuesta_funciones, id_asignacion) => {
	const data = { data_metas: data_respuesta_metas, data_funciones: data_respuesta_funciones, id_asignacion_persona: id_asignacion, id_solicitud: id_solicitud };
	consulta_ajax(`${ruta}guardar_respuestas_indicadores`, data, resp => {
		let { tipo, mensaje, titulo, id_estado } = resp;
		if (tipo == 'success') {
			$(`#container_encuesta`).empty();
			id_asignacion_persona = 0;
			if (id_estado == 'Eval_Ter') {
				$(".nombre_tipo_evaluador").html('');
				$(".nombre_evaluado").html('');
				$("#container_encuesta").append(`<div class="col-md-12 text-center">
					<img src="${Traer_Server()}/imagenes/final.png" alt="..." style='width:30%;'> 
					<h4><b>EVALUACIÓN FINALIZADA</b></h4>
					</br><a href="${Traer_Server()}index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
					</div>`);
				$('#bar_estado').css('width', '100%');
				$(".text_barra").html('100%');
			} else continuar_encuesta(id_solicitud);
		} else {
			$("#btn_siguiente").attr("disabled", false);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const cambiar_respuesta = (idpregunta, id_idtipo_respuesta) => {
	data_preguntas.map(function (dato) {
		if (dato.id_pregunta == idpregunta) {
			dato.respuesta = id_idtipo_respuesta;
		}
	});
}

const pintar_respuestas = async (id_pregunta, id_tipo_pregunta) => {
	let respuestas = await obtener_permisos_parametros(id_tipo_pregunta);
	respuestas.forEach(elemento => {
		$(`#question_${id_pregunta}`).append(`
			<div class="text-left" style="padding-left:40px;${elemento.valory == 1 ? 'display:inline;' : ''}">
				<input type="radio" id="answer_${id_pregunta}_${elemento.id}" name="answer_${id_pregunta}" onclick="cambiar_respuesta('${id_pregunta}','${elemento.id}')">
				<label for="answer_${id_pregunta}_${elemento.id}" style="font-size:18px;"> ${elemento.valor}</label>
			</div>`);
	});
}

const pintar_preguntas = (tipo, periodo) => {
	let id_asignacion = (tipo) ? id_asignacion_persona : 0;
	data_preguntas.forEach(elemento => {
		$(`#container_encuesta`).append(`
		<div class="col-md-12 text-left alert" id="question_${elemento.id_pregunta}">
			<h4><ul><li>${elemento.pregunta}</li></ul></h4>                     
		</div>`);
		pintar_respuestas(elemento.id_pregunta, elemento.id_tipo_pregunta, periodo);
	});
	$(`#container_encuesta`).append(`<div class="col-md-12 alert"><button type="button" id="btn_siguiente" class="btn btn-danger btn-lg active"> GUARDAR Y SIGUIENTE</button></div>`);
	$("#btn_siguiente").click(() => {
		let data_respuestas = [];
		data_preguntas.map(function (dato) {
			var objeto = {
				id_solicitud: id_solicitud,
				id_tipo_evaluador: dato.id_tipo_evaluador,
				id_area_competencia: dato.id_area_competencia,
				id_competencia: dato.id_competencia,
				id_pregunta: dato.id_pregunta,
				id_tipo_pregunta: dato.id_tipo_pregunta,
				id_respuesta: dato.respuesta,
				id_asignacion_persona: id_asignacion,
			}
			data_respuestas.push(objeto);
		});
		msj_confirmacion('¿ Estas Seguro ?', `Desea guardar la evaluación del funcionario?.`, () =>{
			$("#btn_siguiente").attr("disabled", true);
			swal.close();
			guardar_respuestas('guardar_respuestas', data_respuestas, tipo, id_asignacion);
		});
	});
	$('.cargando_data').fadeIn(3000).html(data_preguntas);
}

const calcular_cumplimiento = async (id_pregunta,meta,id_tipo_meta) => {
	let cumplimiento = 0;
	let x = 0;
	let resultado = $(`#resul_${id_pregunta}`).val();
	if(resultado != ''){
		let id_aux  = await obtener_valor_parametro(id_tipo_meta);
		if(id_aux == 'Met_Crec'){
			x = (resultado/meta);
		}else if(id_aux == 'Met_Dec'){
			if(meta == 0 || resultado == 0) x = 1;
			x = (meta / resultado);
		}
		cumplimiento = x*100 > 100 ? 100 : x*100;
	}
	$(`#cump_${id_pregunta}`).html(Math.round(cumplimiento));
}

const pintar_preguntas_indicadores = () => {
	$(`#container_metas`).append(`
		<table class="table table-bordered table-condensed table-responsive" id="metas_des" cellspacing="0" width="100%">
			<tbody style="display:contents;">
				<tr>
					<th class="nombre_tabla" colspan="2">TABLA METAS DE DESEMPEÑO</th>
				</tr>
				<tr>
					<td class="alert alert-info text-right" colspan="4" style="font-size: 14px;"><b>Nota:</b> En Resultado solo se admiten números, para indicar decimal utilice punto(.)</td>
				</tr>
				<tr class="filaprincipal">
					<td>Descripción</td>
					<td>Meta</td>
					<td>Cumplimiento%</td>
					<td width="160px">Resultado</td>
				</tr>
			</tbody>		
		</table>`);
		if(data_preguntas_metas.length > 0){
			data_preguntas_metas.forEach((elemento) => {
			$("#metas_des> tbody").append(`<tr id="question_${elemento.id_pregunta}">
				<td class="text-left">${elemento.pregunta}</td>
				<td>${elemento.meta}</td>
				<td><span id="cump_${elemento.id_pregunta}"></span></td>
				<td><input type="number" class="form-control" required="true" id="resul_${elemento.id_pregunta}" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46" onkeyup="calcular_cumplimiento(${elemento.id_pregunta},${elemento.meta},${elemento.id_tipo_meta})" placeholder="Digite el Resultado"></td>
				</tr>`);
			});
		}else{
			$("#metas_des> tbody").append(`<tr><td colspan="4">No aplica</td></tr>`);
		}
	$(`#container_encuesta`).append(`<div class="col-md-12 alert"><button type="button" id="btn_siguiente" class="btn btn-danger btn-lg active">GUARDAR Y SIGUIENTE</button></div>`);
	$("#btn_siguiente").click(() => {
		let data_respuestas_metas = [];
		let data_respuestas_funciones = [];

		//array de respuesta metas
		data_preguntas_metas.map(function (dato) {
			var objeto = {
				id_pregunta: dato.id_pregunta,
				id_asignacion_persona: id_asignacion_persona,
				resultado: $(`#resul_${dato.id_pregunta}`).val(),
				cumplimiento: $(`#cump_${dato.id_pregunta}`).html(),
			}
			data_respuestas_metas.push(objeto);
		});

		//array de respuesta funciones
		data_preguntas.map(function (dato) {
			var objeto = {
				id_pregunta: dato.id_pregunta,
				respuesta: dato.respuesta,
			}
			data_respuestas_funciones.push(objeto);
		});

		msj_confirmacion('¿ Estas Seguro ?', `Desea guardar los indicadores del funcionario?.`, () =>{
			$("#btn_siguiente").attr("disabled", true);
			swal.close();
			guardar_respuestas_indicadores(data_respuestas_metas, data_respuestas_funciones, id_asignacion_persona);
		});
	});
	$('.cargando_data').fadeIn(3000).html(data_preguntas_metas);
}

const cambiar_respuesta_fun = (idpregunta, id_idtipo_respuesta) => {
	data_preguntas.map(function (dato) {
		if (dato.id_pregunta == idpregunta) {
			dato.respuesta = id_idtipo_respuesta;
		}
	});
}

const pintar_respuestas_func = async (id_pregunta, id_tipo_pregunta, tipo, valor='valor', respuesta, ) => {
	let respuestas = await obtener_permisos_parametros(id_tipo_pregunta);
	let texto = '';
	let check = '';
	respuestas.forEach(elemento => {
		texto = valor == 'valorx' ? elemento.valorx : elemento.valor;
		check = elemento.valorx = respuesta  && tipo == 'question_for_' ? 'checked' : '';
		$(`#${tipo}${id_pregunta}`).append(`
			<div class="text-left" style="padding-left:30px;${texto > 0 || elemento.valory == 1 ? 'display:inline;' : ''}">
				<input type="radio" id="answer_${id_pregunta}_${elemento.id}_${tipo}" name="answer_${id_pregunta}_${tipo}" onclick="cambiar_respuesta_fun('${id_pregunta}','${elemento.id}')" ${check}>
				<label for="answer_${id_pregunta}_${elemento.id}_${tipo}" style="font-size:14px;"> ${texto}</label>
			</div>`);
	});
}

const pintar_funciones_formacion = (funciones,form_esencial) => {
	data_preguntas = funciones;
	$(`#container_encuesta`).append(`<div class="col-md-12" style="padding-left:40px;" id="container_funciones"><h4 class="text-left red"><span class="fa fa-cogs"></span> <b>FUNCIONES</b></h4></div>`);
	$(`#container_funciones`).append(`
		<table class="table table-bordered table-condensed table-responsive" id="funciones_met" cellspacing="0" width="100%">
			<tbody style="display:contents;">
			</tbody>		
		</table>`);
	funciones.forEach((elemento, index) => {
		$(`#funciones_met> tbody`).append(`<tr><td colspan="2" class="text-left" style="border-bottom: none;" id="question_fun_${elemento.id_pregunta}"><h4><ul><li>${elemento.pregunta}</li></ul></h4></td></tr>`);
		pintar_respuestas_func(elemento.id_pregunta, elemento.id_tipo_respuesta, 'question_fun_', 'valor');
	});
	$(`#container_encuesta`).append(`<div class="col-md-12" style="padding-left:40px;" id="container_formacion"><h4 class="text-left red"><span class="fa fa-book"></span> <b>FORMACIÓN ESENCIAL</b></h4></div>`);
	$(`#container_formacion`).append(`
		<table class="table table-bordered table-condensed table-responsive" id="formacion_es" cellspacing="0" width="100%">
			<tbody style="display:contents;">
				<tr>
					<th class="nombre_tabla" colspan="2">TABLA FORMACIÓN</th>
				</tr>
				<tr class="filaprincipal">
					<td>Descripción</td>
					<td width="250px">Formación</td>
				</tr>
			</tbody>		
		</table>`);
	form_esencial.forEach((elemento) => {
		$("#formacion_es> tbody").append(`<tr><td class="text-left">${elemento.pregunta}</td><td>${elemento.respuesta}</td></tr>`);
	});

	$(`#container_encuesta`).append(`<div class="col-md-12" style="padding-left:40px;" id="container_metas"><h4 class="text-left red"><span class="fa fa-bar-chart"></span> <b>METAS DE DESEMPEÑO</b></h4></div>`);
	pintar_preguntas_indicadores();
}

const continuar_encuesta = async (id_solicitud, evaluado='') => {
	$("#container_encuesta").empty();
	let { nombre_completo, tipo_evaluador, parte1, parte2, id_evaluado, nombre_jefe, nombre_coevaluado, jefe_inmediato, coevaluacion, periodo } = await get_detalle_solicitud(id_solicitud);
	$("#container_encuesta").append(`<div class="cargando_data"><img src="${Traer_Server()}/imagenes/loading.gif" style='width:10%;'><h3>Cargando...</h3></div>`);
	let nombre_tipo_evaluador = '';
	if (parte1 == 0) {
		index_tipo_evaluador = tipo_evaluador;
		//tipo evaluador
		let evaluador = await obtener_tipo_evaluador(id_solicitud);
		data_tipo_evaluador = evaluador;
		let id_tipo_evaluador = data_tipo_evaluador[index_tipo_evaluador]['id'];
		let valory_tipo = data_tipo_evaluador[index_tipo_evaluador]['valory'];
		let id_aux = data_tipo_evaluador[index_tipo_evaluador]['id_aux'];
		nombre_tipo_evaluador = data_tipo_evaluador[index_tipo_evaluador]['valorx'];
		$(".nombre_tipo_evaluador").html(nombre_tipo_evaluador);
		let tipo = null;
		let nombre_evaluado = nombre_completo;
		if (valory_tipo === '3') {
			nombre_evaluado = nombre_jefe;
		} else if (valory_tipo === '2') {
			nombre_evaluado = nombre_coevaluado;
		} else if (valory_tipo === '4') tipo = 1;
		$(".nombre_evaluado").html(nombre_evaluado);
		if (valory_tipo != '4') $("#info_evaluado").html(`<h4>${nombre_tipo_evaluador}: <strong>${nombre_evaluado}</strong></h4>`);

		let ind_evaluado = '';
		if (tipo) {
			let indicadores = await obtener_indicadores(id_solicitud, 0);
			data_indicadores = indicadores;
			if(evaluado){
				const resultado = indicadores.find(elemento => elemento.id_persona == evaluado);
				ind_evaluado = resultado['id_persona'];
				nombre_evaluado = resultado['nombre_evaluado'];
				id_asignacion_persona = resultado['id_asignacion_persona'];
			}else{
				ind_evaluado = data_indicadores[0]['id_persona'];
				nombre_evaluado = data_indicadores[0]['nombre_evaluado'];
				id_asignacion_persona = data_indicadores[0]['id_asignacion_persona'];
			}
			$(".nombre_evaluado").html(nombre_evaluado);
			
			$("#info_evaluado").html(`<h4>${nombre_tipo_evaluador}: <strong>${nombre_evaluado}</strong></h4>`);
		}
		//preguntas
		let preguntas = await obtener_preguntas(id_tipo_evaluador, id_aux, id_solicitud, ind_evaluado, periodo);
		data_preguntas = preguntas;
		pintar_preguntas(tipo);
		bar_estado();

	} else if (parte2 == 0) {
		// indicadores
		let id_indicador = '';
		let indicadores = await obtener_indicadores(id_solicitud, '', 'vacio');
		data_indicadores = indicadores;
		if(evaluado){
			const resultado = indicadores.find(elemento => elemento.id_persona == evaluado);
			ind_evaluado = resultado['id_persona'];
			nombre_evaluado = resultado['nombre_evaluado'];
			id_indicador = resultado['evaluado'];
			id_asignacion_persona = resultado['id_asignacion_persona'];
		}else{
			ind_evaluado = data_indicadores[0]['id_persona'];
			nombre_evaluado = data_indicadores[0]['nombre_evaluado'];
			id_indicador = data_indicadores[0]['evaluado'];
			id_asignacion_persona = data_indicadores[0]['id_asignacion_persona'];
		}
		$("#info_evaluado").html(`<h4>INDICADORES: <strong>${nombre_evaluado}</strong></h4>`);
		$(".nombre_tipo_evaluador").html('INDICADORES');
	    $(".nombre_evaluado").html(nombre_evaluado);
		//preguntas
		let preguntas = await obtener_preguntas_indicador(id_indicador, periodo);
		data_preguntas_metas = preguntas;
		let funciones = await obtener_funciones(id_indicador, periodo);
		let form_esencial = await obtener_formacion_esencial(id_indicador, periodo);
		pintar_funciones_formacion(funciones,form_esencial); 
		bar_estado_indicadores(id_solicitud);
	}
	$("#btn_siguiente").attr("disabled", false);
}

const buscar_persona = (dato, callbak) => {
	consulta_ajax(`${ruta}buscar_persona`, { dato }, resp => {
		$(`#tabla_personas_busqueda tbody`).off('click', 'tr td .seleccionar').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
		const myTable = $("#tabla_personas_busqueda").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					defaultContent: `<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>`
				},
				{ data: "nombre_completo" },
				{ data: 'identificacion' },
				{
					defaultContent: '<span style="color: #39B23B;" title="Seleccionar Persona" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>'
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_personas_busqueda tbody').on('click', 'tr', function () {
			$("#tabla_personas_busqueda tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_personas_busqueda tbody').on('dblclick', 'tr', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});
		$('#tabla_personas_busqueda tbody').on('click', 'tr td .seleccionar', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});

		$('#tabla_personas_busqueda tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let data = myTable.row($(this).parent()).data();
			ver_detalle_persona(data);
		});
	});
}

const ver_detalle_persona = (data, container = '#tabla_detalle_persona', modal = '#modal_detalle_persona') => {
	let { id, nombre_completo, fecha_nacimiento, fecha_expedicion, lugar_expedicion, identificacion, tipo_identificacion } = data;
	datos_postulante = { id, nombre_completo }
	$(`${container} .tipo_identificacion`).html(tipo_identificacion);
	$(`${container} .fecha_nacimiento`).html(fecha_nacimiento);
	$(`${container} .fecha_expedicion`).html(fecha_expedicion);
	$(`${container} .lugar_expedicion`).html(lugar_expedicion);
	$(`${container} .nombre_completo`).html(nombre_completo);
	$(`${container} .identificacion`).html(identificacion);
	$(modal).modal();
}

const guardar_parametro = (idparametro, tabla) => {
	let data = new FormData(document.getElementById("form_valor_parametro"));
	data.append('idparametro', idparametro);
	enviar_formulario(`${ruta}guardar_parametro`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#form_valor_parametro").get(0).reset();
			listar_valorparametro(idparametro, tabla);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_parametro = (id, idparametro, tabla) => {
	let data = new FormData(document.getElementById("form_valor_parametro"));
	data.append('id', id);
	data.append('idparametro', idparametro);
	enviar_formulario(`${ruta}modificar_parametro`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#form_valor_parametro").get(0).reset();
			callbak_activo = (resp) => guardar_parametro(idparametro, tabla);
			listar_valorparametro(idparametro, tabla);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const eliminar_datos = (data, callback) => {
	let { title, id, tabla_bd } = data;
	swal({
		title,
		text: "Tener en cuenta que no podra revertir esta acción.!",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Eliminar!",
		cancelButtonText: "No, Regresar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				consulta_ajax(`${ruta}eliminar_datos`, { id, tabla_bd }, resp => {
					let { tipo, mensaje, titulo } = resp;
					if (tipo == 'success') {
						callback();
						swal.close();
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			}
		});
}

const listar_valorparametro = (parametro, tabla) => {
	consulta_ajax(`${ruta}listar_valorparametro`, { parametro: parametro }, resp => {
		$(`#${tabla} tbody`)
			.off('click', 'tr td:nth-of-type(1)')
			.off('click', 'tr')
			.off('dblclick', 'tr')
			.off("click", "tr td .modificar")
			.off("click", "tr td .eliminar")
			.off("click", "tr td .asignar")
			.off("click", "tr td .area_apreciacion");
		const myTable = $(`#${tabla}`).DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{ defaultContent: `<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>` },
				{ data: 'valor' },
				{
					"render": function (data, type, full, meta) {
						return full.peso_porcentual + "%";
					}
				},
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		if (parametro != 214) {
			myTable.column(2).visible(false);
		}

		$(`#${tabla} tbody`).on('click', 'tr', function () {
			let data = myTable.row(this).data();
			idvalorparametro = data.id;
			id_auxvalorparametro = data.id_aux;
			peso_estado = data.peso_porcentual;
			$(`#${tabla} tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});

		$(`#${tabla} tbody`).on('dblclick', 'tr', function () {
			$(`#${tabla} tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});

		$(`#${tabla} tbody`).on('click', 'tr td:nth-of-type(1)', function () {
			let { id, valor, valorx, valory, tipo_respuesta } = myTable.row($(this).parent()).data();
			$('#modal_detalle_permiso .valor').html(valor);
			$('#modal_detalle_permiso .valorx').html(valorx);
			$('#modal_detalle_permiso .valory').html(valory);
			$('#modal_detalle_permiso .valorz').html(tipo_respuesta);
			$("#modal_detalle_permiso").modal();
		});

		$(`#${tabla} tbody`).on("click", "tr td .eliminar", function () {
			let { id } = myTable.row($(this).parent()).data();
			eliminar_datos({ id, title: "Eliminar Dato?", tabla_bd: 'valor_parametro' }, () => {
				listar_valorparametro(parametro, tabla);
			});
		});

		$(`#${tabla} tbody`).on("click", "tr td .modificar", function () {
			let { id, valor, valorx, valory, valorz, idparametro } = myTable.row($(this).parent()).data();
			$('#form_valor_parametro input[name="valor"]').val(valor);
			if (idparametro == 217) $('#form_valor_parametro select[name="area_apreciacion"]').val(valorx);
			else $('#form_valor_parametro textarea[name="valorx"]').val(valorx);
			$('#form_valor_parametro input[name="valory"]').val(valory);
			$('#form_valor_parametro select[name="valorz"]').val(valorz);
			switch (parametro) {
				case 214:
					$(".valory").addClass('oculto');
					$(".valorz").addClass('oculto');
					$(".apreciacion").addClass('oculto');
					break;
				case 215:
					$(".nombre_parametro").html('Peso porcentual');
					$(".valory").addClass('oculto');
					$(".valorz").addClass('oculto');
					$(".apreciacion").addClass('oculto');
					break;
				case 216:
					$(".valory").addClass('oculto');
					$(".valorz").addClass('oculto');
					$(".apreciacion").addClass('oculto');
					break;
				case 217:
					$(".valory").addClass('oculto');
					$(".valorz").addClass('oculto');
					$(".apreciacion").removeClass('oculto');
					break;
				default:
					$(".nombre_parametro").html('Número');
					$(".apreciacion").addClass('oculto');
					$(".valory").removeClass('oculto');
					$(".valorz").removeClass('oculto');
					break;
			}
			callbak_activo = (resp) => modificar_parametro(id, parametro, tabla);
			$("#modal_valor_parametro").modal();
		});

		$(`#${tabla} tbody`).on("click", "tr td .asignar", function () {
			let { id, id_aux, peso_porcentual } = myTable.row($(this).parent()).data();
			idvalorparametro = id;
			id_auxvalorparametro = id_aux;
			peso_estado = peso_porcentual;
			let idparametro = null;
			switch (parametro) {
				case 214:
					$("#listado_parametros_permiso").val(215);
					$(".alertpeso").removeClass("oculto");
					$(".detalle_peso").html(`${peso_porcentual}%`);
					idparametro = 215;
					break;
				case 215:
					$("#listado_parametros_permiso").val(216);
					$(".alertpeso").addClass("oculto");
					idparametro = 216;
					break;
				case 216:
					$("#listado_parametros_permiso").val(217);
					$(".alertpeso").addClass("oculto");
					idparametro = 217;
					break;
				case 217:
					$("#listado_parametros_permiso").val(218);
					$(".alertpeso").addClass("oculto");
					idparametro = 218;
					break;
			}
			obtener_valores_permisos(idparametro, parametro, tabla);
			$("#ModalPermiso").modal();
		});

		$(`#${tabla} tbody`).on("click", "tr td .area_apreciacion", function () {
			let { id, id_aux, peso_porcentual } = myTable.row($(this).parent()).data();
			idvalorparametro = id;
			id_auxvalorparametro = id_aux;
			peso_estado = peso_porcentual;
			let idparametro = 223;
			$("#listado_parametros_permiso").val(idparametro);
			$(".alertpeso").removeClass("oculto");
			$(".detalle_peso").html(`${peso_porcentual}%`);
			obtener_valores_permisos(idparametro, parametro, tabla);
			$("#ModalPermiso").modal();
		});
	});
}

const gestionar_permiso_texto = (title, text, callback) => {
	swal({
		title,
		text,
		type: "input",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Aceptar!",
		cancelButtonText: "Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true,
		inputPlaceholder: `Ingrese el peso porcentual del tipo evaluador`,
		inputType: "number",
	}, function (message) {
		if (message === false) return false;
		if (message === "") swal.showInputError(`Debe Ingresar el peso porcentual.`);
		else {
			peso_porcentaje = message;
			let peso = (parseInt(peso_estado) + parseInt(peso_porcentaje));
			if (peso > 100) {
				swal.showInputError(`El peso porcentual sobrepasa el 100%, digite otra cantidad!`);
			} else callback();
		}
	});
}

// traer valores permiso   
const obtener_valores_permisos = (idparametro, parametro, tabla) => {
	consulta_ajax(`${ruta}traer_valores_permisos`, { idparametro, idvalorparametro }, resp => {
		$(`#tablapermisoparametro tbody`).off('click', '.habilitar').off('click', 'tr');
		$(`#tablapermisoparametro tbody`).off('click', '.desabilitar').off('click', 'tr');
		const myTable = $("#tablapermisoparametro").DataTable({
			"destroy": true,
			"processing": true,
			'data': resp,
			"columns": [
				{
					"data": "id"
				},
				{
					"data": "valor"
				},
				{
					"data": "porcentaje"
				},
				{
					"render": function (data, type, full, meta) {
						let { id_permiso } = full;
						let resp = '<div class="btn-group btn-group-toggle" data-toggle="buttons"><label class="btn btn-primary active habilitar">Habilitar</label></div>';
						if (id_permiso != null) resp = '<div class="btn-group btn-group-toggle" data-toggle="buttons"><label class="btn btn-primary active desabilitar">Desabilitar</label></div>';
						return resp;
					}
				}

			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": [],
		});

		if (parametro != 214 && parametro != 215) {
			myTable.column(2).visible(false);
		}

		$('#tablapermisoparametro tbody').on('click', 'tr', function () {
			$("#tablapermisoparametro tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tablapermisoparametro tbody').on('click', '.habilitar', function () {
			let { id, id_aux } = myTable.row($(this).parent().parent()).data();
			if (idparametro == 215 || idparametro == 223) {
				gestionar_permiso_texto('Habilitar Permiso .?', '', () => {
					habilitar_permiso(id_auxvalorparametro, idvalorparametro, id_aux, id, parametro, tabla);
				});
			} else {
				confirmar_cambio_permiso('Habilitar Permiso .?', '', () => {
					habilitar_permiso(id_auxvalorparametro, idvalorparametro, id_aux, id, parametro, tabla);
				});
			}

		});
		$('#tablapermisoparametro tbody').on('click', '.desabilitar', function () {
			let { id_permiso } = myTable.row($(this).parent().parent()).data();
			confirmar_cambio_permiso('Deshabilitar Permiso .?', '', () => {
				deshabilitar_permiso(id_permiso, parametro, tabla);
			});
		});

	});
	//agregar y quitar permisos
	const habilitar_permiso = (vp_principal, vp_principal_id, vp_secundario, vp_secundario_id, parametro, tabla) => {
		let id_parametro_permiso = $("#listado_parametros_permiso").val();
		consulta_ajax(`${ruta}habilitar_permiso`, { vp_principal, vp_principal_id, vp_secundario, vp_secundario_id, peso_porcentaje, id_parametro_permiso }, resp => {
			let { mensaje, tipo, titulo } = resp;
			if (tipo == 'success') {
				swal.close();
				peso_estado = (parseInt(peso_estado) + parseInt(peso_porcentaje));
				obtener_valores_permisos(id_parametro_permiso, parametro, tabla);
				listar_valorparametro(parametro, tabla);
				$(".detalle_peso").html(`${peso_estado}%`);
			} else MensajeConClase(mensaje, tipo, titulo);
		});
	}

	const deshabilitar_permiso = (id_permiso, parametro, tabla) => {
		let id_parametro_permiso = $("#listado_parametros_permiso").val();
		consulta_ajax(`${ruta}deshabilitar_permiso`, { id_permiso, id_parametro_permiso }, resp => {
			let { mensaje, tipo, titulo, porcentaje } = resp;
			if (tipo == 'success') {
				swal.close();
				peso_estado = (parseInt(peso_estado) - parseInt(porcentaje));
				obtener_valores_permisos(id_parametro_permiso, parametro, tabla);
				listar_valorparametro(parametro, tabla);
				$(".detalle_peso").html(`${peso_estado}%`);
			} else MensajeConClase(mensaje, tipo, titulo);
		});
	}

}

const msj_confirmacion = (title, text, callback) => {
	swal({
		title,
		text,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si!",
		cancelButtonText: "No!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	}, confirm => {
		if (confirm) callback();
	});
};

const gestionar_solicitud = (data, callback = () => { }) => {
	consulta_ajax(`${ruta}gestionar_solicitud`, data, resp => {
		const { mensaje, titulo, tipo } = resp;
		if (tipo === 'success') {
			callback(resp);
			swal.close();
		} else MensajeConClase(mensaje, tipo, titulo);
		listar_solicitudes();
	});
}

const listar_solicitudes = (id = '', filtros = {}) => {
	// $("#btn_exportar").attr("href", `${Traer_Server()}index.php/evaluacion/exportar_evaluacion/${0}/${0}/${0}/${0}/${0}/${0}`);
	// $("#btn_resultados").attr("href", `${Traer_Server()}index.php/evaluacion/exportar_resultados/${0}/${0}/${0}/${0}`);
	let { estado, tipo, fecha_i, fecha_f, periodo } = filtros
	$(`#tabla_solicitudes tbody`)
		.off('click', 'tr td:nth-of-type(1)')
		.off('click', 'tr')
		.off('click', 'tr td')
		.off('dblclick', 'tr')
		.off("click", "tr td .cancelar")
		.off("click", "tr td .gestionar")
		.off("click", "tr td .notificar")
		.off("click", "tr td .resultado")
		.off("click", "tr td .acta");
	consulta_ajax(`${ruta}listar_solicitudes`, { id, estado, tipo, fecha_i, fecha_f, periodo }, resp => {
		const myTable = $("#tabla_solicitudes").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: resp,
			columns: [
				{ data: 'ver' },
				{ data: 'periodo' },
				{ data: 'tipo' },
				{ data: 'evaluado' },
				{ data: 'state' },
				{
					"render": function (resp, type, full, meta) {
						if (full.puntuacion) return full.puntuacion;
						return '--';
					}
				},
				{ data: 'gestion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_solicitudes tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let data = myTable.row($(this).parent()).data();
			const { id, id_metodo_eval } = data;
			id_solicitud = id;
			info_solicitud = data;
			$("#btnActa").attr("href", `${Traer_Server()}archivos_adjuntos/talentohumano/actas/ACTA_${id_solicitud}.pdf`);
			ver_detalle_evalucaion(data);
		});

		$('#tabla_solicitudes tbody').on('click', 'tr td', function () {
			const { id } = myTable.row($(this).parent()).data();
			id_solicitud = id;
			$(".encuesta").attr("href", `${Traer_Server()}index.php/evaluacion/encuesta/${id_solicitud}`);
		});

		$('#tabla_solicitudes tbody').on('dblclick', 'tr', function () {
			let data = myTable.row($(this).parent()).data();
			id_solicitud = data.id;
		});

		$('#tabla_solicitudes tbody').on('click', 'tr td .cancelar', function () {
			const { id, tipo, id_metodo_eval } = myTable.row($(this).parent()).data();
			const data = {
				id,
				nextState: 'Eval_Can',
				success: 'Solicitud Cancelada Exitosamente!',
				type: id_metodo_eval,
			};
			msj_confirmacion('¿ Estas Seguro ?', `La Solicitud de ${tipo} será cancelada.`, () => gestionar_solicitud(data));
		});

		$('#tabla_solicitudes tbody').on('click', 'tr td .notificar', function () {
			const { id, tipo, id_metodo_eval, id_estado_eval } = myTable.row($(this).parent()).data();
			let estado = 'Eval_Env';
			// if (id_estado_eval == 'Eval_Sol' || id_estado_eval == 'Eval_Env') {
			// 	estado = 'Eval_Env';
			// } else 
			if (id_estado_eval == 'Eval_Pro') estado = 'Eval_Pro';

			const data = {
				id,
				nextState: estado,
				success: 'Solicitud Enviada Exitosamente!',
				type: id_metodo_eval,
			};
			data.callback = () => {
				enviar_correo(id)
			}
			msj_confirmacion('¿ Estas Seguro ?', `La Solicitud de ${tipo} será enviada.`, () => gestionar_solicitud(data));
		});

		$('#tabla_solicitudes tbody').on('click', 'tr td .resultado', function () {
			const { id } = myTable.row($(this).parent()).data();
			id_solicitud = id;
			consulta_ajax(`${ruta}calcularResultados`, { id_solicitud }, resp => {
				let { mensaje, tipo, titulo } = resp;
				if (tipo == 'success') {
					listar_solicitudes();
				}
				MensajeConClase(mensaje, tipo, titulo);
			});
		});

		$('#tabla_solicitudes tbody').on('click', 'tr td .acta', function () {
			const { id, id_metodo_eval, id_estado_eval } = myTable.row($(this).parent()).data();
			let estado = id_estado_eval;
			if (id_estado_eval == 'Eval_Ter') {
				estado = 'Eval_Act_Env';
			} else if (id_estado_eval == 'Eval_Act_Env') estado = 'Eval_Act_Env';

			const data = {
				id,
				nextState: estado,
				success: 'Acta Enviada Exitosamente!',
				type: id_metodo_eval,
			};
			data.callback = () => {
				enviar_correo_acta(id)
			}
			msj_confirmacion('¿ Estas Seguro ?', `El acta de retroalimentaciónn será enviada.`, () => gestionar_solicitud(data));
		});
	});

}

const enviar_correo_acta = async (id_solicitud) => {
	let { id, nombre_completo, correo } = await get_detalle_solicitud(id_solicitud);
	let ser = `<a href="${Traer_Server()}index.php/evaluacion/acta/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 1;
	let titulo = 'Acta de Retroalimentación';
	let correos = correo;
	let nombre = nombre_completo;
	let mensaje = `Se le informa que el Acta de Retroalimentación ya se encuentra habilitada para que ingrese a realizarla. 
	<br><br>
	Para ingresar haga clic aquí:
	<br><br>
	${ser}`;
	enviar_correo_personalizado("Eval", mensaje, correos, nombre, "Evaluación", titulo, "Par_TH", tipo);
}

const notificar_evaluado_acta = async (id_solicitud_evaluado) => {
	let { id, nombre_completo, correo } = await get_detalle_solicitud(id_solicitud_evaluado);
	let ser = `<a href="${Traer_Server()}index.php/evaluacion/confirmar_acta/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 1;
	let titulo = 'Acta de Retroalimentación finalizada';
	let correos = correo;
	let nombre = nombre_completo;
	let mensaje = `Se le informa que su Acta de Retroalimentación ha sido finalizada. Para confirmar ingrese al siguiente link. 
	<br><br>
	Para ingresar haga clic aquí:
	<br><br>
	${ser}`;
	enviar_correo_personalizado("Eval", mensaje, correos, nombre, "Evaluación", titulo, "Par_TH", tipo);
}

const modificar_solicitud = (id_solicitud, identificacion, evaluacion) => {
	consulta_ajax(`${ruta}modificar_solicitud`, { id_solicitud, identificacion, evaluacion }, resp => {
		const { mensaje, titulo, tipo, data } = resp;
		swal.close();
		if (tipo === 'success') {
			$("#modal_buscar_persona").modal('hide');
			ver_detalle_evalucaion(data);
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const obtener_resultado_evaluacion = async (id_solicitud) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_resultado_evaluacion`;
		consulta_ajax(url, { id_solicitud }, resp => {
			resolve(resp);
		});
	});
}

const ver_detalle_evalucaion = async (data) => {
	const { evaluado, fecha_registra, cc_evaluado, tipo, state, id_estado_eval, cargo, nombre_coevaluado, nombre_jefe, jefe_inmediato, tipo_evaluador, parte1, parte2, acta, recibido, calificacion, observacion, firma } = data;
	$(".info_funcionario").html(evaluado);
	$(".info_identificacion").html(cc_evaluado);
	$(".info_fecha").html(fecha_registra);
	$(".info_metodo").html(tipo);
	$(".info_estado").html(state);
	$(".info_cargo").html(cargo);
	let { puntuacion_directa, puntuacion_centil, valoracion } = await obtener_resultado_evaluacion(id_solicitud);
	$(".puntuacion_directa").html(puntuacion_directa);
	$(".puntuacion_centil").html(`${puntuacion_centil}%`);
	$(".info_valoracion").html(valoracion);
	if (recibido == 1) {
		$(".calificacion").html(calificacion);
		$(".observacion").html(observacion);
		$("#info_confirmacion").removeClass('oculto');
		$("#btnfirma").attr("href", `${Traer_Server()}archivos_adjuntos/talentohumano/actas/firmas/${firma}`);
	} else $("#info_confirmacion").addClass('oculto');

	// $('.info_valoracion').css('background', color);
	let evaluador = await obtener_tipo_evaluador(id_solicitud);
	const resultado = evaluador.find(elemento => elemento.id_aux === 'Eval_Per');
	if (!resultado || parte1 == 1 && (id_estado_eval == 'Eval_Pro' || id_estado_eval == 'Eval_Ter' || id_estado_eval == 'Eval_Can')) $("#agregar_pesonal").addClass('oculto');
	else $("#agregar_pesonal").removeClass('oculto');
	if (acta == 1) {
		$(".btn_ver_acta").removeClass('oculto');
		$(".btn_formacion").removeClass('oculto');
	} else {
		$(".btn_ver_acta").addClass('oculto');
		$(".btn_formacion").addClass('oculto');
	}
	listar_tipo_evaluador(id_solicitud);
	listar_personal_cargo(id_solicitud, id_estado_eval);
	$("#modal_detalle_evaluacion").modal();
}

const listar_tipo_evaluador = (id_solicitud) => {
	consulta_ajax(`${ruta}listar_tipo_evaluador`, { id_solicitud }, resp => {
		$(`#tabla_tipoEvaluador tbody`).off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td .seleccionar').off('click', 'tr td .editar');
		var i = 0;
		const myTable = $("#tabla_tipoEvaluador").DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					}
				},
				{ data: 'valorx' },
				{ data: 'nombre_evaluado' },
				{ data: 'gestion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_tipoEvaluador tbody').on('click', 'tr', function () {
			$("#tabla_tipoEvaluador tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_tipoEvaluador tbody').on('dblclick', 'tr', function () {
			$("#tabla_tipoEvaluador tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_tipoEvaluador tbody').on('click', 'tr td .seleccionar', function () {
			let { valorx, valory } = myTable.row($(this).parent()).data();
			$(".nombre_evaluacion").html(valorx);
			evaluacion_respuestas(id_solicitud, valory);
		});
		$('#tabla_tipoEvaluador tbody').on('click', 'tr td .editar', function () {
			let { valory } = myTable.row($(this).parent()).data();
			$("#txt_dato_buscar").val('');
			callbak_activo = (data) => {
				const { identificacion, nombre_completo } = data;
				msj_confirmacion('¿ Estas Seguro ?', `Desea agregar a ${nombre_completo} como Jefe Inmediato?`, () => modificar_solicitud(id_solicitud, identificacion, valory));
			};
			buscar_persona('', callbak_activo);
			$("#modal_buscar_persona").modal();
		});
	});
}

const evaluacion_respuestas = (id_solicitud, tipoevaluador = '', id_evaluado = '') => {
	consulta_ajax(`${ruta}get_evaluacion_respuestas`, { id_solicitud, tipoevaluador, id_evaluado }, resp => {
		$(`#tabla_evaluacion_respuestas tbody`).off('dblclick', 'tr').off('click', 'tr');
		const myTable = $("#tabla_evaluacion_respuestas").DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{ data: "pregunta" },
				{ data: "area_apreciacion" },
				{ data: 'competencia' },
				{ data: 'respuesta' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$(`#tabla_evaluacion_respuestas tbody`).on('click', 'tr', function () {
			$(`#tabla_evaluacion_respuestas tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
		$(`#tabla_evaluacion_respuestas tbody`).on('dblclick', 'tr', function () {
			$(`#tabla_evaluacion_respuestas tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
	});
	$("#modal_evaluacion_respuestas").modal();
}

const guardar_persona_acargo = (id_solicitud, identificacion) => {
	consulta_ajax(`${ruta}guardar_persona_acargo`, { id_solicitud_evaluado: id_solicitud, id_evaluado: identificacion }, resp => {
		const { mensaje, titulo, tipo, id_estado_eval } = resp;
		if (tipo === 'success') {
			swal.close();
			$("#modal_buscar_persona").modal('hide');
			listar_personal_cargo(id_solicitud, id_estado_eval);
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const gestionar_personal_acargo = (id_asignacion_persona, id_solicitud, id_estado_eval) => {
	consulta_ajax(`${ruta}gestionar_personal_acargo`, { id_asignacion_persona }, resp => {
		const { mensaje, titulo, tipo } = resp;
		swal.close();
		if (tipo === 'success') {
			listar_personal_cargo(id_solicitud, id_estado_eval);
		} else MensajeConClase(mensaje, tipo, titulo);

	});
}

const listar_personal_cargo = (id_solicitud, id_estado_eval) => {
	consulta_ajax(`${ruta}listar_personal_cargo`, { id_solicitud, id_estado_eval }, resp => {
		$(`#tabla_personas_cargo tbody`).off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td .seleccionar').off('click', 'tr td .eliminar');
		var i = 0;
		const myTable = $("#tabla_personas_cargo").DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					}
				},
				{ data: 'nombre_completo' },
				{ data: 'identificacion' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_personas_cargo tbody').on('click', 'tr', function () {
			$("#tabla_personas_cargo tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_personas_cargo tbody').on('dblclick', 'tr', function () {
			$("#tabla_personas_cargo tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_personas_cargo tbody').on('click', 'tr td .seleccionar', function () {
			let { identificacion, periodo } = myTable.row($(this).parent()).data();
			id_evaluado = identificacion;
			periodo_eval = periodo;
			$("div.resp").hide();
			$("#menu_admin_indicadores li").removeClass('active');
			$(".evaluacion").addClass('active');
			$("div.preguntas").fadeIn();
			evaluacion_respuestas_indicadores(id_solicitud, id_evaluado, '4', 'get_evaluacion_respuestas', 'tabla_preguntas_evaluacion');
		});
		$('#tabla_personas_cargo tbody').on('click', 'tr td .eliminar', function () {
			let { id_asignacion_persona } = myTable.row($(this).parent()).data();
			msj_confirmacion('¿ Estas Seguro ?', `Tener en cuenta que no podra revertir esta acción.!`, () => gestionar_personal_acargo(id_asignacion_persona, id_solicitud, id_estado_eval));
		});
	});
}

const evaluacion_respuestas_indicadores = (id_solicitud, id_evaluado, id_aux_evaluador = '', controller, tabla) => {
	consulta_ajax(`${ruta}${controller}`, { id_solicitud, id_evaluado, id_aux_evaluador, periodo:periodo_eval}, resp => {
		$(`#${tabla} tbody`).off('dblclick', 'tr').off('click', 'tr');
		const myTable = $(`#${tabla}`).DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{ data: "pregunta" },
				{
					"render": function (data, type, full, meta) {
						if (tabla == 'tabla_preguntas_evaluacion') return full.area_apreciacion;
						else return '';
					}
				},
				{
					"render": function (data, type, full, meta) {
						if (tabla == 'tabla_preguntas_evaluacion') return full.competencia;
						else return '';
					}
				},
				{ data: 'respuesta' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		if (tabla == 'tabla_preguntas_indicadores') {
			myTable.column(1).visible(false);
			myTable.column(2).visible(false);
		}

		$(`#${tabla} tbody`).on('click', 'tr', function () {
			$(`#${tabla} tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
		$(`#${tabla} tbody`).on('dblclick', 'tr', function () {
			$(`#${tabla} tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
	});

	$("#modal_personal_acargo").modal();
}

const evaluacion_respuestas_formacionForm = (id_evaluado, controller, tabla) => {
	consulta_ajax(`${ruta}${controller}`, {id_evaluado, periodo:periodo_eval}, resp => {
		$(`#${tabla} tbody`).off('dblclick', 'tr').off('click', 'tr');
		const myTable = $(`#${tabla}`).DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{ data: "pregunta" },
				{ data: 'respuesta' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$(`#${tabla} tbody`).on('click', 'tr', function () {
			$(`#${tabla} tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
		$(`#${tabla} tbody`).on('dblclick', 'tr', function () {
			$(`#${tabla} tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
	});

	$("#modal_personal_acargo").modal();
}

const enviar_correo = async (id_solicitud) => {
	let { id, nombre_completo, correo } = await get_detalle_solicitud(id_solicitud);
	let ser = `<a href="${Traer_Server()}index.php/evaluacion/encuesta/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 1;
	let titulo = 'Evaluación Administrativa - Asignación';
	let correos = correo;
	let nombre = nombre_completo;
	let mensaje = `Se le informa que la Apreciación del Desempeño ya se encuentra habilitada para que ingrese a realizarla. 
	Recuerde que la fecha limite de realización es el 30 de noviembre.
	<br><br>
	Para ingresar haga clic aquí:
	<br><br>
	${ser}`;
	enviar_correo_personalizado("Eval", mensaje, correos, nombre, "Evaluación", titulo, "Par_TH", tipo);
}

const guardar_solicitud = () => {
	let data = new FormData(document.getElementById("form_nueva_solicitud"));
	data.append('evaluado', id_persona);
	data.append('id_jefe', id_jefe);
	data.append('id_coevaluado', id_coevaluado);
	enviar_formulario(`${ruta}guardar_solicitud`, data, (resp) => {
		let { tipo, mensaje, titulo, id } = resp;
		if (tipo == 'success') {
			id_persona = '';
			$("#form_nueva_solicitud").get(0).reset();
			$(".nombre_completo").html('');
			$(".cargo").html('');
			enviar_correo(id);
			listar_solicitudes();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const enviar_corres_general = (data_correos, msj) => {
	let ser = `<a href="${Traer_Server()}index.php"><b>agil.cuc.edu.co</b></a>`
	let tipo = 3;
	let titulo = 'Evaluación Administrativa - Asignación';
	let correos = data_correos;
	// let nombre = data_correos;
	let mensaje = `${msj}.
	<br><br>
	Para ver la información de la solicitud, haga click en el siguiente enlace:
	<br><br>
	${ser}`;
	enviar_correo_personalizado("Eval", mensaje, correos, "Funcionario", "Evaluación", titulo, "Par_TH", tipo);
}

const enviar_notificaciones = () => {
	let data = new FormData(document.getElementById("form_notificacion"));
	enviar_formulario(`${ruta}enviar_notificaciones`, data, (resp) => {
		let { tipo, mensaje, titulo, data_correos, msj } = resp;
		if (tipo == 'success') {
			$("#form_notificacion").get(0).reset();
			enviar_corres_general(data_correos, msj);
			listar_solicitudes();
		} MensajeConClase(mensaje, tipo, titulo);
	});
}

const get_resultados_detalles = (id_solicitud) => {
	consulta_ajax(`${ruta}get_resultados_detalles`, { id_solicitud }, resp => {
		$(`#tabla_resultado_detalles tbody`).off('dblclick', 'tr').off('click', 'tr');
		const myTable = $(`#tabla_resultado_detalles`).DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{ data: 'tipo_evaluador' },
				{ data: 'identificacion_evaluado' },
				{ data: 'area_apreciacion' },
				{ data: 'suma' },
				{ data: 'total' },
				{ data: 'promedio' },
				{ data: 'porcentaje' },
				{ data: 'porcentaje_tipo_evaluador' },
				{ data: 'final' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$(`#tabla_resultado_detalles tbody`).on('click', 'tr', function () {
			$(`#tabla_resultado_detalles tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
		$(`#tabla_resultado_detalles tbody`).on('dblclick', 'tr', function () {
			$(`#tabla_resultado_detalles tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
	});
}

const get_resultados_tipoevaluador = (id_solicitud, tabla) => {
	consulta_ajax(`${ruta}get_resultados_tipoevaluador`, { id_solicitud }, resp => {
		$(`#${tabla} tbody`).off('dblclick', 'tr').off('click', 'tr');
		const myTable = $(`#${tabla}`).DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{ data: 'tipo_evaluador' },
				{ data: 'porcentaje' },
				{ data: 'suma' },
				{ data: 'producto' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$(`#${tabla} tbody`).on('click', 'tr', function () {
			$(`#${tabla} tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
		$(`#${tabla} tbody`).on('dblclick', 'tr', function () {
			$(`#${tabla} tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
	});
}

const listar_personal_actas = (id_solicitud) => {
	consulta_ajax(`${ruta}listar_personal_actas`, { id_solicitud }, resp => {
		$(`#tabla_personal_actas tbody`).off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)').off('click', 'tr td .gestionar').off('click', 'tr td .fin');
		var i = 0;
		const myTable = $("#tabla_personal_actas").DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{ data: 'ver' },
				{ data: 'nombre_completo' },
				{ data: 'identificacion' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_personal_actas tbody').on('click', 'tr', function () {
			$("#tabla_personal_actas tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
			info_solicitud = myTable.row(this).data();
		});
		$('#tabla_personal_actas tbody').on('dblclick', 'tr', function () {
			$("#tabla_personal_actas tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
			info_solicitud = myTable.row(this).data();
		});
		$('#tabla_personal_actas tbody').on('click', 'tr td:nth-of-type(1)', async function () {
			let { nombre_completo, identificacion, cargo_funcionario, nombre_jefe, identificacion_jefe, cargo_jefe, periodo, fecha, id_solicitud_evaluado, acta_retro, metodo } = myTable.row($(this).parent()).data();
			id_evaluado = identificacion;
			periodo_eval = periodo;
			if (fecha == null) {
				var f = new Date();
				fecha = f.getFullYear() + "-" + f.getMonth() + "-" + f.getDate();
			}
			$(".info_funcionario").html(nombre_completo);
			$(".info_identificacion").html(identificacion);
			$(".info_cargo").html(cargo_funcionario);
			$(".info_dependencia").html();
			$(".info_jefe").html(nombre_jefe);
			$(".info_identificacion_jefe").html(identificacion_jefe);
			$(".info_cargo_jefe").html(cargo_jefe);
			$(".info_metodo").html(metodo);
			$(".info_periodo").html(periodo);
			$(".info_fecha_retro").html(fecha);
			let { puntuacion_directa, puntuacion_centil, valoracion } = await obtener_resultado_evaluacion(id_solicitud_evaluado);
			$(".puntuacion_directa").html(puntuacion_directa);
			$(".puntuacion_centil").html(`${puntuacion_centil}%`);
			$(".info_valoracion").html(valoracion);
			if (acta_retro == 1) {
				$("#btnActa").removeClass('oculto');
				$("#btnActa").attr("href", `${Traer_Server()}archivos_adjuntos/talentohumano/actas/ACTA_${id_solicitud_evaluado}.pdf`);
			} else $("#btnActa").addClass('oculto');
			$("#modal_detalle_evaluacion").modal();
		});
		$('#tabla_personal_actas tbody').on('click', 'tr td .gestionar', function () {
			info_solicitud = myTable.row(this).data();
			let { identificacion, nombre_completo, periodo } = myTable.row($(this).parent()).data();
			id_evaluado = identificacion;
			periodo_eval = periodo;
			$("#info_evaluado").html('');
			$("#info_evaluado").append(`<h4>Colaborador: <strong>${nombre_completo}</strong></h4><br>`);
			$("#modal_retroalimentacion").modal();
		});
		$('#tabla_personal_actas tbody').on('click', 'tr td .fin', function () {
			info_solicitud = myTable.row(this).data();
			let { id_solicitud_evaluado, nombre_completo, periodo } = myTable.row($(this).parent()).data();
			periodo_eval = periodo;
			msj_confirmacion('¿ Estas Seguro ?', `Desea finalizar el acta de retroalimentación de ${nombre_completo}?, tener en cuenta que no podra revertir esta acción!.`, () => pedir_firma_jefe(id_solicitud_evaluado));
		});
	});
}

const pedir_firma_jefe = (id_solicitud_evaluado) => {
	swal.close();
	$("#modal_solicitar_firma_jefe").modal();
	$("#div_firmar").html(`<p><span class="fa fa-edit"></span> Registre su firma:</p>
	<div id="content_firmas"><p style="text-align:center;">Configurando...</p></div>
	`);
	newCanvas();
	$("#enviar_firma_jefe").off("click");
	$("#enviar_firma_jefe").click(() => {
		let image = document.getElementById("canvas").toDataURL();
		finalizar_acta(id_solicitud_evaluado);
	});
}

const finalizar_acta = (id_solicitud_evaluado) => {
	let image = document.getElementById("canvas").toDataURL();
	consulta_ajax(`${ruta}finalizar_acta`, { id_solicitud_evaluado, id_solicitud, firma: image }, resp => {
		let { tipo, mensaje, titulo, data_actas } = resp;
		// swal.close();
		if (tipo == 'success') {
			$("#modal_solicitar_firma_jefe").modal("hide");
			generar_acta(id_solicitud_evaluado);
			notificar_evaluado_acta(id_solicitud_evaluado);
			listar_personal_actas(id_solicitud);
			bar_estado_actas(data_actas);
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const generar_acta = id => {
	console.log("generando");
	const route = `${Traer_Server()}index.php/evaluacion/exportar_acta_retro/${id}`;
	window.open(route, '_blank');
	window.focus()
	console.log("generado");
	return true;
}

const guardar_sugerencias = (id_solicitud_evaluado) => {
	let data = new FormData(document.getElementById("form_sugerencias"));
	data.append('id_solicitud_evaluado', id_solicitud_evaluado);
	data.append('id_evaluado', id_evaluado);
	enviar_formulario(`${ruta}guardar_sugerencias`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			listar_sugerencias_formacion(id_evaluado, id_solicitud_evaluado);
			$("#modal_add_sugerencias").modal('hide');
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_sugerencia = (id_sugerencia) => {
	let { identificacion, id_solicitud_evaluado } = info_solicitud;
	let data = new FormData(document.getElementById("form_sugerencias"));
	data.append('id_sugerencia', id_sugerencia);
	enviar_formulario(`${ruta}modificar_sugerencia`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			listar_sugerencias_formacion(identificacion, id_solicitud_evaluado);
			$("#modal_add_sugerencias").modal('hide');
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const guardar_compromisos = (identificacion, id_solicitud_evaluado) => {
	let data = new FormData(document.getElementById("form_compromisos"));
	data.append('id_evaluado', identificacion);
	data.append('id_solicitud_evaluado', id_solicitud_evaluado);
	enviar_formulario(`${ruta}guardar_compromisos`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#form_compromisos").get(0).reset();
			listar_oportunidades_mejora(identificacion, id_solicitud_evaluado);
			listar_personal_actas(id_solicitud);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_compromisos = (id_compromiso) => {
	let { identificacion, id_solicitud_evaluado } = info_solicitud;
	let data = new FormData(document.getElementById("form_compromisos"));
	data.append('id_compromiso', id_compromiso);
	enviar_formulario(`${ruta}modificar_compromisos`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_add_compromiso").modal('hide');
			listar_oportunidades_mejora(identificacion, id_solicitud_evaluado);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const listar_oportunidades_mejora = (id_evaluado, id_solicitud_evaluado) => {
	consulta_ajax(`${ruta}listar_oportunidades_mejora`, { id_evaluado, id_solicitud_evaluado }, resp => {
		$(`#tabla_compromisos tbody`).off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)').off('click', 'tr td .eliminar').off('click', 'tr td .modificar');
		var i = 0;
		const myTable = $("#tabla_compromisos").DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					}
				},
				{ data: 'compromiso' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_compromisos tbody').on('click', 'tr', function () {
			$("#tabla_compromisos tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_compromisos tbody').on('dblclick', 'tr', function () {
			$("#tabla_compromisos tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_compromisos tbody').on("click", "tr td .modificar", function () {
			let { id_compromiso, compromiso, identificacion } = myTable.row($(this).parent()).data();
			$('#form_compromisos textarea[name="compromiso"]').val(compromiso);
			callbak_activo = (resp) => modificar_compromisos(id_compromiso, identificacion);
			$("#modal_add_compromiso").modal();
		});
		$('#tabla_compromisos tbody').on("click", "tr td .eliminar", function () {
			let { identificacion, id_compromiso, solicitud_evaluado } = myTable.row($(this).parent()).data();
			eliminar_datos({ id: id_compromiso, title: "Eliminar Dato?", tabla_bd: 'evaluacion_compromisos' }, () => {
				listar_oportunidades_mejora(identificacion, solicitud_evaluado);
			});
		});
	});
}

const listar_sugerencias_formacion = (id_evaluado, id_solicitud_evaluado) => {
	consulta_ajax(`${ruta}listar_sugerencias_formacion`, { id_evaluado, id_solicitud_evaluado }, resp => {
		$(`#tabla_sugerencias tbody`).off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)').off('click', 'tr td .eliminar').off('click', 'tr td .modificar');
		var i = 0;
		const myTable = $("#tabla_sugerencias").DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					}
				},
				{ data: 'observacion' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_sugerencias tbody').on('click', 'tr', function () {
			$("#tabla_sugerencias tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_sugerencias tbody').on('dblclick', 'tr', function () {
			$("#tabla_sugerencias tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_sugerencias tbody').on("click", "tr td .modificar", function () {
			let { id_sugerencia, observacion, identificacion } = myTable.row($(this).parent()).data();
			$('#form_sugerencias textarea[name="sugerencias"]').val(observacion);
			callbak_activo = (resp) => modificar_sugerencia(id_sugerencia);
			$("#modal_add_sugerencias").modal();
		});
		$('#tabla_sugerencias tbody').on("click", "tr td .eliminar", function () {
			let { identificacion, id_sugerencia, solicitud_evaluado } = myTable.row($(this).parent()).data();
			eliminar_datos({ id: id_sugerencia, title: "Eliminar Dato?", tabla_bd: 'evaluacion_sugerencias_formacion' }, () => {
				listar_sugerencias_formacion(identificacion, solicitud_evaluado);
			});
		});
	});
}

const get_resultados_metas = (id_solicitud, id_evaluado) => {
	consulta_ajax(`${ruta}get_respuestas_indicadores`, { id_solicitud, id_evaluado, periodo: periodo_eval }, resp => {
		$(`#tabla_metas_acta tbody`).off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)').off('click', 'tr td .eliminar').off('click', 'tr td .editar');
		var i = 0;
		const myTable = $("#tabla_metas_acta").DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					}
				},
				{ data: 'pregunta' },
				{ data: 'puntaje' },
				{ data: 'respuesta' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_metas_acta tbody').on('click', 'tr', function () {
			$("#tabla_metas_acta tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_metas_acta tbody').on('dblclick', 'tr', function () {
			$("#tabla_metas_acta tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
	});
}

const guardar_resultado_competencias = (identificacion, id_solicitud_evaluado) => {
	const data = { data_respuestas: resultado_competencia, id_persona: identificacion, idsolicitud_evaluado: id_solicitud_evaluado };
	consulta_ajax(`${ruta}guardar_resultado_competencias`, data, resp => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_detalle_resultados").modal("hide");
			listar_personal_actas(id_solicitud);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const get_respuesta_competencia = (datos, f, m) => {
	const found = resultado_competencia.find((mat) => mat.id_competencia === datos.id_competencia);
	if (!found || resultado_competencia.length === 0) {
		if (datos.id) {
			var objeto = {
				id: datos.id,
				id_persona: datos.id_persona,
				id_solicitud: datos.id_solicitud,
				id_competencia: datos.id_competencia,
				id_pregunta: datos.id_pregunta,
				fortaleza: f,
				mejora: m,
				puntaje: datos.puntaje,
				id_usuario_registra: datos.id_usuario_registra,
			}
		} else {
			var objeto = {
				id_persona: datos.id_persona,
				id_solicitud: datos.id_solicitud,
				id_competencia: datos.id_competencia,
				id_pregunta: datos.id_pregunta,
				fortaleza: f,
				mejora: m,
				puntaje: datos.puntaje,
				id_usuario_registra: datos.id_usuario_registra,
			}
		}
		resultado_competencia.push(objeto);
	} else {
		resultado_competencia.map(function (row) {
			if (row.id_competencia === datos.id_competencia) {
				row.fortaleza = f;
				row.mejora = m;
			}
		});
	}
}

const get_detalle_resultados = (id_evaluado, id_solicitud_evaluado, periodo) => {
	consulta_ajax(`${ruta}get_detalle_resultados`, { id_evaluado, id_solicitud_evaluado, periodo }, resp => {
		$(`#tabla_detalle_resultados tbody`).off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td .fortaleza').off('click', 'tr td .mejora');
		const myTable = $("#tabla_detalle_resultados").DataTable({
			destroy: true,
			searching: true,
			processing: true,
			pageLength: 1000,
			data: resp,
			columns: [
				{ data: 'area_apreciacion' },
				{ data: 'competencia' },
				{ data: 'descripcion' },
				{ data: 'fortaleza' },
				{ data: 'mejora' },
				{ data: 'puntaje' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_detalle_resultados tbody').on('click', 'tr', function () {
			$("#tabla_detalle_resultados tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_detalle_resultados tbody').on('dblclick', 'tr', function () {
			$("#tabla_detalle_resultados tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_detalle_resultados tbody').on('click', 'tr td .fortaleza', function () {
			let data = myTable.row($(this).parent().parent()).data();
			$(`.m${data.id_competencia}`).removeClass("fa-toggle-on");
			$(`.m${data.id_competencia}`).addClass("fa-toggle-off");
			$(`.f${data.id_competencia}`).removeClass("fa-toggle-off");
			$(`.f${data.id_competencia}`).addClass("fa-toggle-on");
			get_respuesta_competencia(data, 1, 0);
		});
		$('#tabla_detalle_resultados tbody').on('click', 'tr td .mejora', function () {
			let data = myTable.row($(this).parent().parent()).data();
			$(`.f${data.id_competencia}`).removeClass("fa-toggle-on");
			$(`.f${data.id_competencia}`).addClass("fa-toggle-off");
			$(`.m${data.id_competencia}`).removeClass("fa-toggle-off");
			$(`.m${data.id_competencia}`).addClass("fa-toggle-on");
			get_respuesta_competencia(data, 0, 1);
		});
	});
}

const marcar_nivel = () => {
	let nivel = $('input:radio[name=calificacion]:checked').val();
	$("#nivel").html(nivel);
}

const guardar_confirmacion_acta = (id_solicitud) => {
	let image = document.getElementById("canvas").toDataURL();
	let data = new FormData(document.getElementById("form_confirmar_acta"));
	data.append('id_solicitud', id_solicitud);
	data.append('firma', image);
	enviar_formulario(`${ruta}guardar_confirmacion_acta`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			generar_acta(id_solicitud);
			$("#modal_solicitar_firma").modal("hide");
			$("#contenido_actas").empty();
			$("#contenido_actas").append(`<div class="col-md-12">
				<img src="${Traer_Server()}/imagenes/final.png" alt="..." style='width:30%;'> 
				<h4><b>RETROALIMENTACIÓN CONFIRMADA</b></h4>
				</br>
				<a href="${Traer_Server()}index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
			</div>`);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const pedir_firma = () => {
	$("#modal_solicitar_firma").modal();
	$("#div_firmar").html(`<p><span class="fa fa-edit"></span> Registra tu firma:</p>
	<div id="content_firmas"><p style="text-align:center;">Configurando...</p></div>
	`);
	newCanvas();
	$("#enviar_firma").off("click");
	$("#enviar_firma").click(() => {
		let image = document.getElementById("canvas").toDataURL();
		guardar_confirmacion_acta(id_solicitud);
	});
}

const listar_asignacion_personas = (id_evaluador) => {
	let num = 0;
	consulta_ajax(`${ruta}listar_asignacion_personas`, { id_evaluador }, (data) => {
		$(`#tabla_asignacion_personas tbody`).off('click', 'tr').off('click', 'tr span.quitar').off('dblclick', 'tr');
		const myTable = $('#tabla_asignacion_personas').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ data: 'nombre_completo' },
				{ data: 'id_evaluado' },
				{ data: 'periodo' },
				{
					defaultContent: '<span style="color: #d9534f;" title="Quitar Persona" data-toggle="popover" data-trigger="hover" class="fa fa-trash btn btn-default quitar" ></span>'
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_asignacion_personas tbody').on('click', 'tr', function () {
			$('#tabla_asignacion_personas tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_asignacion_personas tbody').on('dblclick', 'tr', function () {
			$('#tabla_asignacion_personas tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_asignacion_personas tbody').on('click', 'tr span.quitar', function () {
			const { id } = myTable.row($(this).parent()).data();
			eliminar_datos({ id, title: "Eliminar Dato?", tabla_bd: 'evaluacion_asignacion_persona' }, () => {
				listar_asignacion_personas(id_evaluador);
			});
		});
	});
};

const guardar_asignacion_persona = (id_evaluado, id_evaluador) => {
	let data = new FormData(document.getElementById("form_nueva_asignacion"));
	data.append('id_evaluado', id_evaluado);
	data.append('id_evaluador', id_evaluador);
	enviar_formulario(`${ruta}guardar_asignacion_persona`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_nueva_asignacion").modal("hide");
			$("#form_nueva_asignacion").get(0).reset();
			listar_asignacion_personas(id_evaluador);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const listar_planFormacion = (id_evaluado) => {
	consulta_ajax(`${ruta}listar_planFormacion`, { id_evaluado: id_evaluado }, (data) => {
		$(`#tabla_planformacion tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .seleccionar');
		const myTable = $('#tabla_planformacion').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ data: 'area_apreciacion' },
				{ data: 'competencia' },
				{ data: 'observaciones' },
				{ data: 'puntaje' },
				{ data: 'hora_formacion' },
				{
					defaultContent: '<span title="Ver" data-toggle="popover" data-trigger="hover" class="fa fa-eye btn btn-default seleccionar red"></span>'
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_planformacion tbody').on('click', 'tr', function () {
			$('#tabla_planformacion tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_planformacion tbody').on('dblclick', 'tr', function () {
			$('#tabla_planformacion tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$(`#tabla_planformacion tbody`).on("click", "tr td .seleccionar", function () {
			let { id_persona, id_competencia } = myTable.row($(this).parent()).data();
			listar_planformacion_personal(id_persona, id_competencia);
			$("#modal_detalle_planformacion").modal();
		});

		$(".btn_entrenamiento").click(() => {
			let { id, id_evaluado } = info_solicitud;
			listar_plan_entrenamiento(id_evaluado);
			$("#modal_listar_plan_entrenamiento").modal();
		});
	});
}

const listar_plan_entrenamiento = (idpersona) => {
	consulta_ajax(`${ruta}listar_plan_entrenamiento`, { idpersona }, (data) => {
		$(`#tabla_entrenamiento tbody`).off('click', 'tr').off('dblclick', 'tr');
		const myTable = $('#tabla_entrenamiento').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ data: 'oferta' },
				{ data: 'facilitador' },
				{ data: 'duracion' },
				{ data: 'lugar' },
				{ data: 'fecha_entrenamiento' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_entrenamiento tbody').on('click', 'tr', function () {
			$('#tabla_entrenamiento tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_entrenamiento tbody').on('dblclick', 'tr', function () {
			$('#tabla_entrenamiento tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});
	});
}

const listar_planformacion_personal = (idpersona, id_competencia) => {
	consulta_ajax(`${ruta}listar_planformacion_personal`, { idpersona, id_competencia }, (data) => {
		$(`#tabla_pformacion_persona tbody`).off('click', 'tr').off('dblclick', 'tr');
		const myTable = $('#tabla_pformacion_persona').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ data: 'tema' },
				{ data: 'funcionario' },
				{ data: 'duracion' },
				{ data: 'lugar' },
				{ data: 'fecha_formacion' },
				{ data: 'competencia' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_pformacion_persona tbody').on('click', 'tr', function () {
			$('#tabla_pformacion_persona tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_pformacion_persona tbody').on('dblclick', 'tr', function () {
			$('#tabla_pformacion_persona tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});
	});
}

/* Funcion para obligar a que sea numeros o string */
const num_o_string = (tipo, key) => {
	if (tipo == "int") {
		if (key < 48 || key > 57) {
		return false;
	  }
	} else if (tipo == "str") {
	  if (key > 47 && key < 58) {
		return false;
	  }
	}
  }