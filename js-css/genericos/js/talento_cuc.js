const ruta = `${Traer_Server()}index.php/talento_cuc_control/`;
let callbak_activo = (resp) => { };
let callbak_ = (resp) => { };
let info_solicitud = [];
let id_solicitud = null;
let id_persona = null;
let idcompetencia = null;
let data_preguntas = [];
let valor_nuevo = null;
let facilitador = null;
let administra = false;
let id_departamento = false;
let tabla = null;
let identificacion_persona = null;
let id_evaluacion_cerfificado = null;

$(document).ready(function () {
	$('input[type="text"]').attr('maxlength', '500');

	$("#btn_buscar_persona").click(() => {
		let datos = $("#txt_buscar_persona").val().trim();
		datos.length == 0 ? MensajeConClase("Ingrese Datos a Buscar", "info", "Oops...") : listar_personas(datos);
	});

	$("#txt_buscar_persona").keypress(e => {
		if (e.which == 13) {
			let datos = $("#txt_buscar_persona").val().trim();
			datos.length == 0 ? MensajeConClase("Ingrese Datos a Buscar", "info", "Oops...") : listar_personas(datos);
		}
	});

	// const administrar_modulo = tipo => {
	//     if (tipo == 'solicitudes') {
	//         listar_personas();
	//         $("#container_solicitudes").fadeIn(1000);
	//         $("#menu_principal").css("display", "none");
	//     } else if (tipo == 'menu') {
	//         $("#menu_principal").fadeIn(1000);
	//         $("#container_solicitudes").css("display", "none");
	//     }
	// }
	// $("#btn_regresar").click(() => regresar());

	// $('#listado_solicitudes').click(() => administrar_modulo('solicitudes'));
	$('.regresar_menu').click(() => {
		// administrar_modulo('menu');
		// listar_personas();
		regresar();
	});

	$("#btn_filtros").click(() => $("#Modal_filtro").modal());

	$("#form_filtro").submit(e => {
		e.preventDefault();
		filtrar_solicitudes();
		return false;
	});

	$("#btn_limpiar").click(() => {
		$("#form_filtro").get(0).reset();
		let fecha_i = $("#Modal_filtro input[name='fecha_i']").val();
		let fecha_f = $("#Modal_filtro input[name='fecha_f']").val();
		let periodo = $("#Modal_filtro input[name='filtro_periodo']").val();
		data = {
			'fecha_i': fecha_i,
			'fecha_f': fecha_f,
			'periodo': periodo
		}
		listar_personas('', 0, data);
	});

	$("#btnConfiguraciones").click(() => {
		$("#menu_administrar li.btn_planformacion").trigger('click');
		// listar_planFormacion();
		$("#modal_administrar").modal();
	});

	$("#menu_administrar li").click(async function () {
		$("#menu_administrar li").removeClass('active');
		$(this).addClass('active');
		if ($(this)[0].classList.contains('btn_planformacion')) {
			$("div.adm_proceso").hide();
			$("div.planformacion").fadeIn();
			$('#btnguardar_config').addClass('oculto');
			listar_planFormacion();
		}else if ($(this)[0].classList.contains('btn_oferta')) {
			$("div.adm_proceso").hide();
			$("div.ofertaEntrenamiento").fadeIn();
			$('#btnguardar_config').addClass('oculto');
			listar_ofertas_entrenamiento();
		} else if ($(this)[0].classList.contains('btn_horas_formacion')) {
			$("div.adm_proceso").hide();
			$("div.hora_formacion").fadeIn();
			$('#btnguardar_config').addClass('oculto');
			listar_valor_parametro(241, 'tabla_horas_formacion');
		} else if ($(this)[0].classList.contains('btn_encuesta')) {
			$("div.adm_proceso").hide();
			$("div.preguntas").fadeIn();
			$('#btnguardar_config').addClass('oculto');
			listar_valor_parametro(243, 'tabla_preguntas');
		} else if ($(this)[0].classList.contains('btn_notificacion')) {
			$("div.adm_proceso").hide();
			$("div.notificaciones").fadeIn();
			$('#btnguardar_config').removeClass('oculto');
			let valor = await get_personas_notificar_th(249);
			$("#txt_correo_responsable").val(valor[1]);
		}
	});

	$("#btnasignaciones").click(() => {
		$("#menu_administrar_asignaciones li.indicadores").trigger('click');
		$("#modal_administrar_asignacion").modal();
	});

	$("#menu_administrar_asignaciones li").click(function () {
		$("#menu_administrar_asignaciones li").removeClass('active');
		$(this).addClass('active');
		tabla = '';
		if ($(this)[0].classList.contains('indicadores')) {
			$("div.adm_proceso").hide();
			$("#s_persona_ind").html('Seleccione Persona');
			$("div.asignacion_indicadores").show();
			tabla = '';
			listar_asignacion_indicadores('');
		}else if ($(this)[0].classList.contains('form_esencial')) {
			$("#form_nueva_asignacion .formacion").removeClass('oculto');
			$("div.adm_proceso").hide();
			$("#selec_persona_fun").html('Seleccione Persona');
			$("#nombre_tab").html('FORMACION ESENCIAL');
			$("div.asignacion_fun").show();
			callbak_activo = (data) => {
				const { id, identificacion, nombre_completo } = data;
				valor_nuevo = identificacion;
				$("#selec_persona_fun").html(nombre_completo);
				$(".add_asignacion").removeClass("oculto");
				listar_asignaciones(identificacion,'talentocuc_formacion_esencial');
				$("#modal_buscar_persona").modal('hide');
			};
			tabla = 'talentocuc_formacion_esencial';
			listar_asignaciones('','talentocuc_formacion_esencial');
		}else if ($(this)[0].classList.contains('funciones')) {
			$("#form_nueva_asignacion .formacion").addClass('oculto');
			$("div.adm_proceso").hide();
			$("#selec_persona_fun").html('Seleccione Persona');
			$("#nombre_tab").html('FUNCIONES');
			$("div.asignacion_fun").show();
			callbak_activo = (data) => {
				const { id, identificacion, nombre_completo } = data;
				valor_nuevo = identificacion;
				$("#selec_persona_fun").html(nombre_completo);
				$(".add_asignacion").removeClass("oculto");
				listar_asignaciones(identificacion, 'talentocuc_funciones');
				$("#modal_buscar_persona").modal('hide');
			};
			tabla = 'talentocuc_funciones';
			listar_asignaciones('','talentocuc_funciones');
		}
	});

	$('.add_asignacion').click(async () => {
		$(".formacion_escencial").html('');
		callbak_ = (resp) => guardar_asignacion(valor_nuevo,tabla);
		let datos = await obtener_tipo_respuesta('Opc_Check');
		datos.forEach(elemento => {
			$(".formacion_escencial").append(`
				<div class="funkyradio-${elemento.valorz}" style="display: inline-block;width:48%;">
					<input type="radio" id="form_${elemento.valor}" name="formacion_es" value="${elemento.id}">
					<label for="form_${elemento.valor}"> ${elemento.valor}</label>
				</div>`);
		});
		$("#form_nueva_asignacion").get(0).reset();
		$("#modal_nueva_asignacion").modal();
	});

	$('#selec_persona_ind').click(() => {
		$("#txt_dato_buscar").val('');
		callbak_activo = (data) => {
			const { identificacion, nombre_completo } = data;
			valor_nuevo = identificacion;
			$("#selec_persona_ind").html(nombre_completo);
			$(".add_indicador").removeClass("oculto");
			listar_asignacion_indicadores(identificacion);
			$("#modal_buscar_persona").modal('hide');
		};
		buscar_persona('', callbak_activo);
		$("#modal_buscar_persona").modal();
	});

	$('.add_indicador').click(() => {
		$("#form_nueva_asignacion_indicador").get(0).reset();
		callbak_activo = (resp) => guardar_asignacion_indicador(valor_nuevo);
		$("#modal_nueva_asignacion_indicador").modal();
	});

	$("#form_nueva_asignacion_indicador").submit(() => {
		callbak_activo();
		return false;
	});

	$('#selec_persona_fun').click(() => {
		$("#txt_dato_buscar").val('');
		buscar_persona('', callbak_activo);
		$("#modal_buscar_persona").modal();
	});

	$("#form_nueva_asignacion").submit(e => {
		e.preventDefault();
		callbak_();
		return false;
	});

	$(".input_buscar_persona").click(() => {
		$("#txt_dato_buscar").val('');
		callbak_activo = (data) => {
			const { id, identificacion, nombre_completo } = data;
			facilitador = identificacion;
			$(".nombre_completo").html(nombre_completo);
			$(".identificacion").html(identificacion);
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

	$(".btn_agil").click(() => {
		$(".btn_agil").attr("href", `${Traer_Server()}index.php`);
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

	$('.btn_nuevo_planformacion').click(() => {
		$("#form_planformacion").get(0).reset();
		$("#txt_dato_buscar").val('');
		callbak_ = (resp) => guardar_planformacion_gen();
		$("#modal_nuevo_planformacion").modal();
	});

	$("#form_planformacion").submit(() => {
		callbak_();
		return false;
	});

	$('.add_pregunta').click(() => {
		$("#form_valor_parametro").get(0).reset();
		callbak_activo = (resp) => guardar_pregunta(243, 'tabla_preguntas');
		$("#modal_nuevo_valor_parametro").modal();
	});

	$("#form_valor_parametro").submit(() => {
		callbak_activo();
		return false;
	});

	$('.btn_nuevo_entrenamiento').click(() => {
		$("#form_planentrenamiento").get(0).reset();
		$("#txt_dato_buscar").val('');
		$(".nombre_completo").html('');
		$(".identificacion").html('');
		callbak_ = (resp) => guardar_planentrenamiento();
		$("#modal_nuevo_planentrenamiento").modal();
	});

	$('.buscar_oferta').click(() => {
		$("#txt_buscar_oferta").val('');		
		callbak_activo = (data) => {
			const { id, nombre } = data;
			$("#form_planentrenamiento input[name='oferta']").val(nombre);
			$("#form_planentrenamiento input[name='id_oferta']").val(id);
			$("#modal_buscar_oferta").modal('hide');
		};
		buscar_oferta('', callbak_activo);
		$("#modal_buscar_oferta").modal();
	});

	$("#form_buscar_oferta").submit(() => {
		let dato = $("#txt_buscar_oferta").val();
		buscar_oferta(dato, callbak_activo);
		return false;
	});

	$("#form_planentrenamiento").submit(() => {
		callbak_();
		return false;
	});

	$("#form_asistencia").submit(() => {
		msj_confirmacion('Políticas', 'He leído y acepto la Política de Protección de Datos de la universidad de la costa CUC.', () => guardar_asistencia(), 'Si, aceptar!', 'No, Cancelar!');
		return false;
	});

	$("#form_guardar_formacion_academica").submit(() => {
		callbak_activo();
		return false;
	});

	// $("#admin_hv li").click(function () {
	// 	let { id, identificacion } = info_solicitud;
	// 	$("#admin_hv li").removeClass('active');
	// 	$(this).addClass('active');
	// 	if ($(this)[0].classList.contains('btn_hoja_vida')) {
	// 		$(".btn_hoja_vida").addClass('active');
	// 		$("div#entrenamiento_hv").hide();
	// 		$("div#otros").hide();
	// 		$("div#planformacion_hv").hide();
	// 		$("div#datos_basicos").fadeIn();
	// 	} else if ($(this)[0].classList.contains('btn_planformacion_hv')) {
	// 		listar_plaformacion_persona_hv(identificacion);
	// 		$(".btn_planformacion_hv").addClass('active');
	// 		$("div#entrenamiento_hv").hide();
	// 		$("div#otros").hide();
	// 		$("div#datos_basicos").hide();
	// 		$("div#planformacion_hv").fadeIn();
	// 	} else if ($(this)[0].classList.contains('btn_entrenamiento_hv')) {
	// 		listar_plan_entrenamiento_hv(identificacion);
	// 		$(".btn_entrenamiento_hv").addClass('active');
	// 		$("div#planformacion_hv").hide();
	// 		$("div#otros").hide();
	// 		$("div#datos_basicos").hide();
	// 		$("div#entrenamiento_hv").fadeIn();
	// 	} else if ($(this)[0].classList.contains('btn_otro_hv')) {
	// 		listar_otros_soporte(id);
	// 		$(".btn_otro_hv").addClass('active');
	// 		$("div#entrenamiento_hv").hide();
	// 		$("div#planformacion_hv").hide();
	// 		$("div#datos_basicos").hide();
	// 		$("div#otros").fadeIn();
	// 	}
	// });
	$("#form_guardar_observacion").submit(() => {
		callbak_activo();
		return false;
	});

	$("#from_soporte_capacitaciones").submit(() => {
		callbak_activo();
		return false;
	});

	$("#from_info_contacto").submit(() => {
		guardar_info_contacto();
		return false;
	});

	$('#avatarImage').click(() => {
		$("#modal_cambiar_avatar").modal();
	});

	$("#from_avatar").submit(() => {
		let filePath = $('#from_avatar input[name=avatarInput]').val();
		let allowedExtensions = /(.jpg|.jpeg|.png)$/i;
		if (!allowedExtensions.exec(filePath)) {
			MensajeConClase('Por favor seleccione un archivo de extención .jpeg/.jpg/.png', 'info', 'Oops');
			$("#from_avatar").get(0).reset();
		} else guardar_avatar();

		return false;
	});

	$("#ver_notificaciones").click(function () {
		mostrar_notificaciones();
		$("#modal_notificaciones_compras").modal("show");
	});

	$(`#from_soporte_academico`).submit(e => {
		e.preventDefault();
		callbak_activo();
		return false;
	});

	$("#btn_detalle").click(function () {
		$("#modal_funciones_cargo").modal("show");
	});

	$(".btn_filtrar_formacion").click(function () {
		callbak_activo = () => filtrar_formacion();
		$("#modal_filtrar_formacion").modal("show");
	});

	$(".btn_filtrar_asistencias").click(function () {
		callbak_activo = () => filtrar_asistencias_formacion();
		$("#modal_filtrar_formacion").modal("show");
	});
	
	$("#btnasistencias").click(function () {
		ver_asistencias_formacion(1,{});
	});

	$("#btn_buscar_hv").click(function () {
		$("#txt_dato_buscar").val('');
		buscar_persona_hv('');
		$("#modal_buscar_persona").modal();
	});

	$('#form_buscar_persona_hv').submit(() => {
		let dato = $("#txt_dato_buscar").val();
		buscar_persona_hv(dato);
		return false;
	});

	$(`#form_filtro_formacion`).submit(e => {
		e.preventDefault();
		callbak_activo();
		return false;
	});

	$("#btnPermisos").click(function () {
		id_persona = null;
		listar_actividades(id_persona);
		$("#modal_permisos").modal();
	});

	$('#s_persona').click(() => {
		callbak_activo = (data) => {
			const { id, nombre_completo } = data;
			id_persona = id;
			$("#input_sele_re").val(id);
			$("#s_persona").html(nombre_completo);
			$("#modal_buscar_persona").modal('hide');
			listar_actividades(id_persona);
		};
		$('#txt_dato_buscar').val('');
		buscar_persona('', callbak_activo);
		$('#modal_buscar_persona').modal();
	});

	$('.buscar_jefe').click(() => {
		callbak_activo = (data) => {
			const { id, nombre_completo } = data;
			$("#form_acta_cargo input[name='nombre_jefe']").val(nombre_completo);
			$("#form_acta_cargo input[name='id_jefe']").val(id);
			$("#modal_buscar_persona").modal('hide');
		};
		$('#txt_dato_buscar').val('');
		buscar_persona('', callbak_activo);
		$('#modal_buscar_persona').modal();
	});

	$('.buscar_cargo').click(() => {
		callbak_activo = (data) => {
			const { id, valor } = data;
			$("#form_acta_cargo input[name='nombre_cargo']").val(valor);
			$("#form_acta_cargo input[name='cargo_id']").val(id);
			$("#modal_buscar_cargo").modal('hide');
		};
		$('#txt_cargo_buscar').val('');
		buscar_cargo('', callbak_activo);
		$('#modal_buscar_cargo').modal();
	});

	$('#form_buscar_cargo').submit(() => {
		let dato = $("#txt_cargo_buscar").val();
		buscar_cargo(dato, callbak_activo);
		return false;
	});

	$("#btnformacionGen").click(function () {
		generar_formacion_masiva();
		$("#modal_plan_formacion_masivo").modal();
	});

	$('.btn_nueva_oferta').click(() => {
		$("#nombre_modal").html('Nueva Oferta de Entrenamiento');
		$("#form_oferta_entrenamiento").get(0).reset();
		$("#modal_nuevo_oferta_entrenamiento").modal();
		callbak_ = (resp) => guardar_oferta_entrenamiento();
	});

	$("#form_oferta_entrenamiento").submit(e => {
		e.preventDefault();
		callbak_();
		return false;
	});

	$('.add_dept_adscrito').click(() => {
		$('#modal_buscar_departamento').modal();
		$('#form_buscar_departamento').get(0).reset();
		callbak_activo = (data) => {
			const { id, nombre } = data;
			$("#form_oferta_entrenamiento input[name='text_depto_adscrito']").val(nombre);
			$("#form_oferta_entrenamiento input[name='dept_adscrito']").val(id);
			$('#modal_buscar_departamento').modal('hide');
		};
		id_departamento = 3;
		buscar_dependencia('', id_departamento, callbak_activo);
	});

	$('.add_departamento').click(() => {
		$('#modal_buscar_departamento').modal();
		$('#form_buscar_departamento').get(0).reset();
		callbak_activo = (data) => {
			const { id, nombre } = data;
			$("#form_oferta_entrenamiento input[name='text_departamento']").val(nombre);
			$("#form_oferta_entrenamiento input[name='departamento']").val(id);
			$('#modal_buscar_departamento').modal('hide');
		};
		id_departamento = 3;
		buscar_dependencia('', id_departamento, callbak_activo);
	});

	$('.add_area_especifica').click(() => {
		$('#modal_buscar_departamento').modal();
		$('#form_buscar_departamento').get(0).reset();
		callbak_activo = (data) => {
			const { id, nombre } = data;
			$("#form_oferta_entrenamiento input[name='text_area_especifica']").val(nombre);
			$("#form_oferta_entrenamiento input[name='area_especifica']").val(id);
			$('#modal_buscar_departamento').modal('hide');
		};
		id_departamento = 90;
		buscar_dependencia('', id_departamento, callbak_activo);
	});

	$('#form_buscar_departamento').submit((e) => {
		e.preventDefault();
		const buscar = $(`#form_buscar_departamento input[name=departamento]`).val();
		buscar_dependencia(buscar,id_departamento, callbak_activo);
	});

	$('#form_administrar').submit(e => {
		let correo = $("#txt_correo_responsable").val();
		var arrayDeCadenas = correo.split(";");
		let i = 0;
		arrayDeCadenas.forEach((elemento) => {
			if(!isValidEmail(elemento)) i++;
		});
		
		if(!i){
			e.preventDefault();
			guardar_correos_notificacion();
		}else MensajeConClase('Los correos no son válidos, por favor verifique.', 'info', 'Oops.!');

		return false;
	});

	$('#form_encuesta_entrenamiento_general').submit(() => {
		msj_confirmacion('¿ Estas Seguro ?', `Desea guardar la encuesta del plan de entrenamiento?.`, () => guardar_encuesta_entrenamiento_general());
		return false;
	});

	$('#form_acta_cargo').submit(() => {
		enviar_acta_aceptacion_cargo();
		return false;
	});	

	$('#form_confirmar_acta').submit(() => {
		callbak_activo = () => guardar_aceptacion_cargo();
		pedir_firma('¿ Confirmar acta ?', 'Desea confirmar el acta de aceptación del cargo?. Tener en cuenta que no podra revertir esta acción.');
		return false;
	}); 

	$("#btn_detalle_formacion").click(function () {
		$("#nombre_modal").html('DETALLE FORMACIÓN ESENCIAL');
		listar_detalle_indicadores(id_persona, 2);
		$("#modal_detalle_indicadores").modal();
	});

	$("#btn_detalle_metas").click(function () {
		$("#nombre_modal").html('DETALLE INDICADORES');
		listar_detalle_indicadores(id_persona, 1);
		$("#modal_detalle_indicadores").modal();
	});

	$('.input_numerico').on('keypress', function (e) {
		if (num_o_string("int", e.keyCode) == false) {
		  return false;
		}
	  });
});

/* Funcion para obligar a que sea numeros o string */
const num_o_string = (tipo, key) => {
	if (tipo == "int") {
	  if (key < 48 && key != 46 || key > 57) {
		return false;
	  }
	} else if (tipo == "str") {
	  if (key > 47 && key < 58) {
		return false;
	  }
	}
  }


const listar_actividades = (persona) => {
	const desasignar = '<span class="btn btn-default desasignar" title="Desasignar"><span class="fa fa-toggle-on" style="color: #5cb85c"></span></span> ';
	const asignar = '<span class="btn btn-default asignar" title="Asignar"><span class="fa fa-toggle-off"></span></span> ';
	const notificar = '<span class="btn btn-default notificar" title="Activar Notificación"><span class="fa fa-bell-o"></span></span> ';
	const no_notificar = '<span class="btn btn-default no_notificar" title="Desactivar Notificación"><span class="fa fa-bell red"></span></span> ';
	
	let num = 0;
	consulta_ajax(`${ruta}listar_actividades`, { persona }, (data) => {
		$(`#tabla_actividades tbody`)
			.off('click', 'tr')
			.off('click', 'tr span.asignar')
			.off('click', 'tr span.desasignar')
			.off('click', 'tr span.no_notificar')
			.off('click', 'tr span.notificar')
			.off('dblclick', 'tr');
		const myTable = $('#tabla_actividades').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ render: () => ++num },
				{ data: 'nombre' },
				{	

					render: (data, type, { asignado, notificacion }, meta) => {
						return asignado
							? notificacion == 1 ? desasignar + no_notificar : desasignar + notificar
							: asignar;
					}
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_actividades tbody').on('click', 'tr', function() {
			$('#tabla_actividades tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_actividades tbody').on('dblclick', 'tr', function() {
			$('#tabla_actividades tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_actividades tbody').on('click', 'tr span.asignar', function() {
			const { asignado, id } = myTable.row($(this).parent()).data();
			asignar_actividad(asignado, id);
		});

		$('#tabla_actividades tbody').on('click', 'tr span.desasignar', function() {
			const { asignado, id } = myTable.row($(this).parent()).data();
			quitar_actividad(asignado, id);
		});

		$('#tabla_actividades tbody').on('click', 'tr span.notificar', function() {
			const { asignado, id } = myTable.row($(this).parent()).data();
			activar_notificacion(asignado);
		});

		$('#tabla_actividades tbody').on('click', 'tr span.no_notificar', function() {
			const { asignado, id } = myTable.row($(this).parent()).data();
			desactivar_notificacion(asignado);
		});
	});

	const asignar_actividad = (asignado, id) => {
		consulta_ajax(
			`${ruta}asignar_actividad`,{ id, persona: id_persona, asignado },
			({ mensaje, tipo, titulo }) => {
				MensajeConClase(mensaje, tipo, titulo);
				listar_actividades(id_persona);
			}
		);
	}; 
	const quitar_actividad = (asignado, id) => {
		msj_confirmacion('¿ Desasignar Actividad ?', 'Desea desasignar este actividad?. Tener en cuenta que no podra revertir esta acción.', () => { 
			consulta_ajax(`${ruta}quitar_actividad`,{ id, persona: id_persona, asignado },(resp) => {
				let { mensaje, tipo, titulo } = resp;
					listar_actividades(id_persona);
				}
			);
			swal.close();
		});
	};

	const activar_notificacion = (asignado) => {
		consulta_ajax(`${ruta}activar_notificacion`,{ id: asignado },(resp) => {
			let { mensaje, tipo, titulo } = resp;
				MensajeConClase(mensaje, tipo, titulo);
				listar_actividades(id_persona);
			}
		);
	};

	const desactivar_notificacion = (asignado) => {
		consulta_ajax(`${ruta}desactivar_notificacion`,{ id: asignado },(resp) => {
			let { mensaje, tipo, titulo } = resp;
				MensajeConClase(mensaje, tipo, titulo);
				listar_actividades(id_persona);
			}
		);
	};
};

const generar_formacion_masiva = () => {
	consulta_ajax(`${ruta}generar_formacion_masiva`, { }, (data) => {
		$(`#tabla_planformacion_masivo tbody`).off('click', 'tr').off('dblclick', 'tr');
		const myTable = $('#tabla_planformacion_masivo').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
				{ data: 'nombre_completo' },
				{ data: 'competencia' },
				{ data: 'observaciones' },
				{ data: 'puntaje' },
				{ data: 'hora_formacion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_planformacion_masivo tbody').on('click', 'tr', function () {
			$('#tabla_planformacion_masivo tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_planformacion_masivo tbody').on('dblclick', 'tr', function () {
			$('#tabla_planformacion_masivo tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		if(data.length === 0) $("#btn_guardar_planformacion_masivo").addClass('oculto');
		else $("#btn_guardar_planformacion_masivo").removeClass('oculto');
	});
}

const filtrar_formacion = () =>{
	let fecha_i = $("#form_filtro_formacion input[name='fecha_i']").val();
	let fecha_f = $("#form_filtro_formacion input[name='fecha_f']").val();
	let text = $("#form_filtro_formacion input[name='text_filtro']").val();
	let id_lugar = $("#form_filtro_formacion select[name='filtro_id_lugar']").val();
	data = {
		'fecha_i': fecha_i,
		'fecha_f': fecha_f,
		'id_lugar': id_lugar,
		'texto': text
	}
	listar_planFormacion(data);
}

const habilitarDescargueCertificado = (id) => {
	$("#descargar_certificado").click(() => {
		window.open(`${Traer_Server()}index.php/talento_cuc/certificado/${id}/${id_evaluacion_cerfificado}`);
	});

}

const mostrar_notificaciones = () => {
	consulta_ajax(`${ruta}mostrar_notificaciones`, {}, async (datos) => {
		dibujar_comentario(datos, "#panel_notificaciones");
		$(".n_notificaciones").html(datos.length);
		if (datos.length > 0) $("#modal_notificaciones_compras").modal("show");
	});
}

const dibujar_comentario = async (datos, panel) => {
	if (datos == "sin_session") {
		close();
		return;
	}
	let normales = 0;
	let notificaciones = "";
	for (let index = 0; index < datos.length; index++) {
		const element = datos[index];
		notificaciones += `<a class="list-group-item pointer">
				<span class="badge pointer" onclick="${element.accion}">Ver</span>
					<h4 class="list-group-item-heading">${element.nombre_completo} </h4>
					<p class="list-group-item-text">Tiene ${element.descripcion}</p>
				</a>`;
		normales++;
	}
	$(panel).html(`
		<ul class="list-group">
			<li class="list-group-item active">
			<span class="badge">${normales}</span>
			Tareas Pendientes
		</li>
		${notificaciones}
		</ul>
		`);
}

const revisar_notificacion = (id_persona, identificacion) => {
	listar_personas('',id_persona);
	listar_avalar_soportes(identificacion);
	$("#modal_avalar_soportes_plan_formacion").modal();

}

const guardar_avatar = () => {
	let data = new FormData(document.getElementById("from_avatar"));
	data.append('id_persona', id_persona);
	enviar_formulario(`${ruta}guardar_avatar`, data, (resp) => {
		let { tipo, mensaje, titulo, avatar } = resp;
		if (tipo == 'success') {
			$("#imagen_empleado").attr("src", `${Traer_Server()}imagenes_personas/${avatar}`);
			$("#from_avatar").get(0).reset();
			$("#modal_cambiar_avatar").modal('hide');
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const guardar_correos_notificacion = () => {
	let data = new FormData(document.getElementById("form_administrar"));
	enviar_formulario(`${ruta}guardar_correos_notificacion`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const obtener_formacion_academica = (idpersona) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_formacion_academica`;
		consulta_ajax(url, { idpersona }, (resp) => {
			resolve(resp);
		});
	});
}

const obtener_tipo_respuesta = (id_aux) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_tipo_respuesta`;
		consulta_ajax(url, { id_aux }, (resp) => {
			resolve(resp);
		});
	});
}

const pintar_formacion_academica = (data_formacion) => {
	$("#formacion").html("");
	$("#formacion").append(`
		${administra ?
		`<div class="col-md-6 col-lg-4 mb-5">
			<div class="card">
				<img class="img-fluid"
					src="`+Traer_Server()+`imagenes/producto_formacion.png"
					alt="" />
				<div class="card-body">
					<h6 class="card-title" style='color : black'>NUEVO</h6>
					<p class="card-text" style='color : black'> Aquí puedes agregar tu formación.</p>
					<button class="btn btn-primary btn-block" id='btn_nueva_formacion_academica'>
						Agregar
					</button>
				</div>
			</div>
		</div>` : ''}`);

		$(`#btn_nueva_formacion_academica`).off('click');
		$("#btn_nueva_formacion_academica").click(() => {
			callbak_activo = (resp) => guardar_formacion_academica();
			$("#modal_guardar_formacion_academica .modal-title").html(`<span class="fa fa-book"></span> Nueva Formación`);
			$("#modal_guardar_formacion_academica").modal();
		});	

	data_formacion.map(({ formacion, nombre, id, id_persona, id_tipo_formacion, state }, indice) => {
		$("#formacion").append(`
		<div class="col-md-6 col-lg-4 mb-5">
			<div class="card">
				<img class="img-fluid"
					src="${Traer_Server()}imagenes/test.png"
					alt="" />
				<div class="card-body">
					<h6 class="card-title" style='color : black'>${formacion}</h6>
					<p class="card-text" style='color : black'> ${nombre} </p>
					${administra ? `
					<div class="dropdown" style='width : 100%'>
					<button class="btn btn-secondary dropdown-toggle btn-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Acciones
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						<span class="dropdown-item btn_eliminar_formacion`+id+`" ><span class='fa fa-trash'></span> Eliminar</span>
						<span class="dropdown-item btn_modificar_formacion`+id+`" ><span class='fa fa-edit'></span> Modificar</span>
						<span class="dropdown-item btn_agregar_sop_formacion`+id+`" ><span class='fa fa-upload'></span> Agregar Soportes</span>
						<span class="dropdown-item btn_ver_sop_formacion`+id+`" ><span class='fa fa-eye'></span> Ver soportes</span>
					</div>
					</div>
					`: ''}
				</div>
			</div>
		</div>
	`);

		$(`.btn_agregar_sop_formacion${id}`).off('click');
		$(`.btn_agregar_sop_formacion${id}`).click(function () {
			$("#id_formacion_archivo").val(id);
			$("#id_persona_soporte").val(id_persona);
			callbak_activo = (resp) => guardar_soporte_academico(id, id_persona);
			$("#modal_enviar_archivos").modal();
		});

		$(`.btn_ver_sop_formacion${id}`).off('click');
		$(`.btn_ver_sop_formacion${id}`).click(function () {
			listar_soportes_formacion_academica(id, id_persona);
			$("#modal_soportes_formacion_academica").modal();
		});

		$(`.btn_eliminar_formacion${id}`).off('click');
		$(`.btn_eliminar_formacion${id}`).click(async function () {
			eliminar_datos({ id, title: "Eliminar datos?", tabla_bd: 'formacion_academica_personas' }, () => {
				let obj = data_formacion.find((element) => element.id === id);
				let subjectIndex = data_formacion.indexOf(obj);
				data_formacion.splice(subjectIndex, 1);
				pintar_formacion_academica(data_formacion);
			});
		});

		$(`.btn_modificar_formacion${id}`).click(function () {
			$("#form_guardar_formacion_academica select[name='id_formacion']").val(id_tipo_formacion);
			$("#form_guardar_formacion_academica input[name='nombre']").val(nombre);
			callbak_activo = (resp) => modificar_formacion_academica(id);
			$("#modal_guardar_formacion_academica").modal();
		});

	});
}

const guardar_soporte_academico = (id, id_persona) =>{
	let data = new FormData(document.getElementById("from_soporte_academico"));
	data.append('id', id);
	data.append('id_persona', id_persona);
	enviar_formulario(`${ruta}guardar_soporte_academico`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_enviar_archivos").modal("hide");
			$("#from_soporte_academico").get(0).reset();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const obtener_observacion_perfil_persona = (idpersona) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_observacion_perfil_persona`;
		consulta_ajax(url, { idpersona }, (resp) => {
			resolve(resp);
		});
	});
}

const pintar_observacion_perfil_personal = (observaciones) => {
	$("#tab_observaciones").html("");
	$("#tab_observaciones").append(`
	${administra ?
		`<div class="col-md-6 col-lg-4 mb-5">
			<div class="card">
				<img class="img-fluid"
					src="`+Traer_Server()+`imagenes/impactos.png"
					alt="" />
				<div class="card-body">
					<h6 class="card-title" style='color : black'>NUEVO</h6>
					<p class="card-text" style='color : black'> Aquí puedes agregar un nuevo tema.</p>
					<button id='btn_observacion' class="btn btn-primary btn-block">
						Agregar
					</button>
				</div>
			</div>
		</div>` : ''}`);

		$(`#btn_observacion`).off('click')
		$('#btn_observacion').click(() => {
			callbak_activo = (resp) => guardar_observacion_perfil_persona();
			$("#modal_observaciones").modal();
		});	

	observaciones.map(({ observacion, id, id_persona, state }, indice) => {
		$("#tab_observaciones").append(`
		<div class="col-md-6 col-lg-4 mb-5">
			<div class="card">
				<img class="img-fluid"
					src="${Traer_Server()}imagenes/soste.png"
					alt="" />
				<div class="card-body">
					<h6 class="card-title" style='color : black'>Tema/Proceso</h6>
					<p class="card-text" style='color : black'> ${observacion} </p>
			${administra ?
				`<div class="dropdown" style='width : 100%'>
					<button class="btn btn-secondary dropdown-toggle btn-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Acciones
					</button>
					<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						<span class="dropdown-item btn_eliminar_observacion`+id+`" ><span class='fa fa-trash'></span> Eliminar</span>
						<span class="dropdown-item btn_modificar_obsesrvacion`+id+`" ><span class='fa fa-edit'></span> Modificar</span>
					</div>
				</div>	
				`	: ''}
				</div>
			</div>
		</div>
	`);
		$(`.btn_modificar_obsesrvacion${id}`).off('click');
		$(`.btn_modificar_obsesrvacion${id}`).click(function () {
			gestionar_permiso_texto('Modificar observación.?', '', 'Te puedo ayudar con:', 'text', () => { modificar_observacion_perfil_persona(id) });
		});

		$(`.btn_eliminar_observacion${id}`).off('click');
		$(`.btn_eliminar_observacion${id}`).click(async function () {
			eliminar_datos({ id, title: "Eliminar datos?", tabla_bd: 'observacion_perfil_persona' }, () => {
				let obj = observaciones.find((element) => element.id === id);
				let subjectIndex = observaciones.indexOf(obj);
				observaciones.splice(subjectIndex, 1);
				pintar_observacion_perfil_personal(observaciones);
			});
		});

	})
}

const listar_soportes_formacion_academica = (id_formacion, id_persona) => {
	consulta_ajax(`${ruta}listar_soportes_formacion_academica`, { id_formacion }, (data) => {
		$(`#tabla_soportes_academicos tbody`).html("").off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .ver_adjunto').off('click', 'tr td .eliminar');
		data.map(({ id, nombre_real, nombre_archivo, nombre_completo, accion }) => {
			$("#tabla_soportes_academicos tbody").append(`<tr><td>${nombre_real}</td><td>${accion}</td></tr>`);

			$(`#tabla_soportes_academicos .eliminar_sop${id}`).on('click', function () {
				eliminar_datos({ id: id, title: "Eliminar datos?", tabla_bd: 'formacion_academica_soportes' }, () => {
					listar_soportes_formacion_academica(id_formacion);
				});
			});
	
			$(`#tabla_soportes_academicos .ver_adjunto_sop${id}`).on("click", function () {
				$(".ver_adjunto").attr("href", `${Traer_Server()}archivos_adjuntos/talento_cuc/formacion_academica/${nombre_archivo}`);
			});
		})
	});
}

const listar_otros_soporte = (persona_id) => {
	consulta_ajax(`${ruta}listar_otros_soporte`, { persona_id }, (data) => {
		$(`#tabla_otros_soportes tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .ver_adjunto').off('click', 'tr td .eliminar');
		const myTable = $('#tabla_otros_soportes').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
				{ data: 'nombre_real' },
				{ data: 'fecha_registra' },
				{ data: 'nombre_completo' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_otros_soportes tbody').on('click', 'tr', function () {
			$('#tabla_otros_soportes tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_otros_soportes tbody').on('dblclick', 'tr', function () {
			$('#tabla_otros_soportes tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_otros_soportes tbody').on('click', 'tr td .eliminar', function () {
			let data = myTable.row($(this).parent()).data();
			eliminar_datos({ id: data.id, title: "Eliminar datos?", tabla_bd: 'personas_soportes' }, () => {
				listar_otros_soporte(persona_id);
			});
		});

		$('#tabla_otros_soportes tbody').on("click", "tr td .ver_adjunto", function () {
			let data = myTable.row($(this).parent()).data();
			$(".ver_adjunto").attr("href", `${Traer_Server()}archivos_adjuntos/talento_cuc/otros_soportes/${data.nombre_archivo}`);
		});
	});

	$(`.btn_agregar_otro_soporte`).click(function () {
		$("#id_formacion_archivo").val(0);
		$("#id_persona_soporte").val(persona_id);
		$("#modal_enviar_archivos").modal();
	});
}

// const listar_plan_entrenamiento_hv = (idpersona) => {
// 	consulta_ajax(`${ruta}listar_plan_entrenamiento`, { idpersona }, (data) => {
// 		$(`#tabla_entrenamiento_hv tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .seleccionar').off('click', 'tr td .get_link');
// 		const myTable = $('#tabla_entrenamiento_hv').DataTable({
// 			destroy: true,
// 			processing: true,
// 			searching: true,
// 			data,
// 			columns: [
// 				{ data: 'facilitador' },
// 				{ data: 'oferta' },
// 				{ data: 'duracion' },
// 				{ data: 'lugar' },
// 				{ data: 'fecha_entrenamiento' },
// 				{
// 					"render": function (data, type, full, meta) {
// 						let { asistencia } = full;
// 						let resp = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
// 						if (asistencia == 0) resp = '<span title="Link del entrenamiento" data-toggle="popover" data-trigger="hover" class="red pointer fa fa-link btn btn-default get_link"></span> <span title="Marcar asistencia" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar red"></span>';
// 						return resp;
// 					}
// 				}
// 			],
// 			language: get_idioma(),
// 			dom: 'Bfrtip',
// 			buttons: get_botones(),
// 		});

// 		$('#tabla_entrenamiento_hv tbody').on('click', 'tr', function () {
// 			$('#tabla_entrenamiento_hv tbody tr').removeClass('warning');
// 			$(this).attr('class', 'warning');
// 		});

// 		$('#tabla_entrenamiento_hv tbody').on('dblclick', 'tr', function () {
// 			$('#tabla_entrenamiento_hv tbody tr').removeClass('warning');
// 			$(this).attr('class', 'warning');
// 		});
// 		$(`#tabla_entrenamiento_hv tbody`).on("click", "tr td .get_link", function () {
// 			let { link } = myTable.row($(this).parent()).data();
// 			MensajeConClase(`${link}`, 'success', 'Link de Entrenamiento!.');
// 		});
// 		$('#tabla_entrenamiento_hv tbody').on("click", "tr td .seleccionar", function () {
// 			let data = myTable.row($(this).parent()).data();
// 			msj_confirmacion('¿ Marcar Asistencia ?', 'Desea marcar la asistencias al plan de entrenamiento?. Tener en cuenta que no podra revertir esta acción.', () => marcar_asistencia_entrenamiento(data.id, data.id_evaluado));
// 		});
// 	});
// }

const listar_soportes_plan_formacion = (id_competencia, id_formacion, id_persona) => {
	consulta_ajax(`${ruta}listar_soportes_plan_formacion`, { id_competencia, id_formacion, id_persona }, (resp) => {
		$(`#tabla_soportes_plan_formacion tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .editar').off('click', 'tr td .eliminar').off('click', 'tr td .ver');
		const myTable = $('#tabla_soportes_plan_formacion').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: resp,
			columns: [
				{ data: 'nombre_formacion' },
				{ data: 'fecha_formacion' },
				{ data: 'horas_formacion' },
				{ data: 'state' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_soportes_plan_formacion tbody').on('click', 'tr', function () {
			$('#tabla_soportes_plan_formacion tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_soportes_plan_formacion tbody').on('dblclick', 'tr', function () {
			$('#tabla_soportes_plan_formacion tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_soportes_plan_formacion tbody').on("click", "tr td .editar", function () {
			let data = myTable.row($(this).parent()).data();
			$("#from_soporte_capacitaciones input[name='nombre_formacion']").val(data.nombre_formacion);
			$("#from_soporte_capacitaciones input[name='horas_formacion']").val(data.horas_formacion);
			$("#from_soporte_capacitaciones input[name='fecha_formacion']").val(data.fecha_formacion);
			$("#modal_soporte_capacitaciones").modal();
		});

		$('#tabla_soportes_plan_formacion tbody').on("click", "tr td .eliminar", function () {
			let data = myTable.row($(this).parent()).data();
			eliminar_datos({ id: data.id, title: "Eliminar datos?", tabla_bd: 'talentocuc_soportes_plan_formacion' }, () => {
				listar_soportes_plan_formacion(id_competencia, id_formacion, id_persona);
			});
		});

		$('#tabla_soportes_plan_formacion tbody').on("click", "tr td .ver", function () {
			let data = myTable.row($(this).parent()).data();
			$(".ver").attr("href", `${Traer_Server()}archivos_adjuntos/talento_cuc/plan_formacion/${data.nombre_archivo}`);
		});
	});
}

const listar_soportes_plan_formacion_hv = (id_competencia, id_persona) => {
	consulta_ajax(`${ruta}listar_soportes_plan_formacion`, { id_competencia, id_persona }, (resp) => {
		$(`#tabla_soportes_plan_formacion tbody`).html("").off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .editar').off('click', 'tr td .eliminar').off('click', 'tr td .ver');

		resp.map((data, indice) => {
			let { nombre_formacion, horas_formacion, state, accion } = data;
			$("#tabla_soportes_plan_formacion tbody").append(`<tr class='tbl_soportes_tr_${indice}'><td>${nombre_formacion}</td><td>${horas_formacion}</td><td>${state}</td><td>${accion}</td><</tr>`);

			$(`#tabla_soportes_plan_formacion .tbl_soportes_tr_${indice} .eliminar`).on("click", function () {
				eliminar_datos({ id: data.id, title: `Eliminar ${nombre_formacion}?`, tabla_bd: 'talentocuc_soportes_plan_formacion' }, () => {
					listar_soportes_plan_formacion_hv(id_competencia, id_persona);
				});
			});

			$(`#tabla_soportes_plan_formacion .tbl_soportes_tr_${indice} .editar`).on("click", function () {
				$("#from_soporte_capacitaciones input[name='nombre_formacion']").val(data.nombre_formacion);
				$("#from_soporte_capacitaciones input[name='horas_formacion']").val(data.horas_formacion);
				$("#from_soporte_capacitaciones input[name='fecha_formacion']").val(data.fecha_formacion);
				$("#from_soporte_capacitaciones input[name='link_soporte']").val(data.link_soporte);
				callbak_activo = () => modificar_soporte_plan_formacion(data.id, id_competencia, id_persona);
				$("#modal_soporte_capacitaciones").modal();
			});

			$(`#tabla_soportes_plan_formacion .tbl_soportes_tr_${indice} .ver`).on("click", function () {
				if(data.nombre_archivo) window.open(`${Traer_Server()}archivos_adjuntos/talento_cuc/plan_formacion/${data.nombre_archivo}`);
			});

			$(`#tabla_soportes_plan_formacion .tbl_soportes_tr_${indice} .link`).on("click", function () {
				if(data.link_soporte) window.open(`${data.link_soporte}`);
			});
		})

	});
}

const listar_soportes_hv = (id_competencia, id_persona) => {
	listar_soportes_plan_formacion_hv(id_competencia, id_persona);
	$("#modal_soportes_plan_formacion").modal();
}

const guardar_soportes_hv = (id_competencia, id_persona) => {
	callbak_activo = () => guardar_soporte_plan_formacion(id_persona, { id_competencia }, false);
	$("#modal_soporte_capacitaciones").modal();
}

const listar_plaformacion_persona_hv = (idpersona) => {
	consulta_ajax(`${ruta}listar_plaformacion_persona_hv`, { idpersona }, (data) => {
		$(`#tabla_planformacion_hv tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .adjuntar').off('click', 'tr td .certificado').off('click', 'tr td:nth-of-type(1)');
		const myTable = $('#tabla_planformacion_hv').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			paging: false,
			info: false,
			data,
			columns: [
				{ data: 'ver' },
				{ data: 'competencia' },
				{ data: 'hora_formacion' },
				{ data: 'tiempo_realizado' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_planformacion_hv tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let { id_competencia, id_formacion, id_persona } = myTable.row($(this).parent()).data();
			listar_soportes_plan_formacion(id_competencia, id_formacion, id_persona);
			$("#modal_soportes_plan_formacion").modal();
		});

		$('#tabla_planformacion_hv tbody').on('click', 'tr', function () {
			$('#tabla_planformacion_hv tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_planformacion_hv tbody').on('dblclick', 'tr', function () {
			$('#tabla_planformacion_hv tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_planformacion_hv tbody').on("click", "tr td .adjuntar", function () {
			let data = myTable.row($(this).parent()).data();
			callbak_activo = () => guardar_soporte_plan_formacion(idpersona, data);
			$("#modal_soporte_capacitaciones").modal();
		});

		$('#tabla_planformacion_hv tbody').on("click", "tr td .certificado", function () {
			let data = myTable.row($(this).parent()).data();
		});
	});
}

const guardar_info_contacto = () => {
	let data = new FormData(document.getElementById("from_info_contacto"));
	data.append('id_persona', id_persona);
	enviar_formulario(`${ruta}guardar_info_contacto`, data, (resp) => {
		let { tipo, mensaje, titulo, info } = resp;
		if (tipo == 'success') {
			$("#from_info_contacto").get(0).reset();
			$("#contacto").empty();
			ver_info_hv(info, administra);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const obtener_info_persona = async (idpersona,identificacion=null) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_info_persona`;
		consulta_ajax(url, { idpersona, identificacion }, resp => {
			resolve(resp);
		});
	});
}

const enviar_correo_soportes = async (info_correos, idsolicitud) => {
	let { id, nombre_completo } = await obtener_info_persona(id_persona);
	let ser = `<a href="${Traer_Server()}index.php/talento_cuc/hoja_vida/${id}/${idsolicitud}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 3;
	let titulo = 'Talento CUC';
	let mensaje = `Se le informa que la persona <strong>${nombre_completo}</strong>, tiene soportes nuevos por avalar.
	<br><br>
	Para ver la información, haga click en el siguiente enlace:
	<br><br>
	${ser}`;
	enviar_correo_personalizado("Tal", mensaje, info_correos, 'Funcionario', "AGIL Talento CUC", titulo, "ParCodAdm", tipo);
}

const guardar_soporte_plan_formacion = (idpersona, datos, callbak = true) => {
	let data = new FormData(document.getElementById("from_soporte_capacitaciones"));
	data.append('id_persona', idpersona);
	data.append('id_competencia', datos.id_competencia);
	enviar_formulario(`${ruta}guardar_soporte_plan_formacion`, data, (resp) => {
		let { tipo, mensaje, titulo, info_correos } = resp;
		if (tipo == 'success') {
			$("#from_soporte_capacitaciones").get(0).reset();
			enviar_correo_soportes(info_correos, id_solicitud);
			if (callbak) {
				listar_plaformacion_persona_hv(idpersona);
			}
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_soporte_plan_formacion = (id,id_competencia,idpersona) => {
	let data = new FormData(document.getElementById("from_soporte_capacitaciones"));
	data.append('id_soporte', id);
	enviar_formulario(`${ruta}modificar_soporte_plan_formacion`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#from_soporte_capacitaciones").get(0).reset();
			listar_soportes_plan_formacion_hv(id_competencia,idpersona);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const guardar_observacion_perfil_persona = () => {
	let data = new FormData(document.getElementById("form_guardar_observacion"));
	data.append('id_persona', id_persona);
	enviar_formulario(`${ruta}guardar_observacion_perfil_persona`, data, (resp) => {
		let { tipo, mensaje, titulo, observaciones } = resp;
		if (tipo == 'success') {
			$("#modal_observaciones").modal("hide");
			$("#form_guardar_observacion").get(0).reset();
			pintar_observacion_perfil_personal(observaciones);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_observacion_perfil_persona = (id) => {
	consulta_ajax(`${ruta}modificar_observacion_perfil_persona`, { valor_nuevo, id }, (resp) => {
		let { tipo, mensaje, titulo, observaciones } = resp;
		if (tipo == 'success') {
			$("#modal_observaciones").modal("hide");
			$("#form_guardar_observacion").get(0).reset();
			pintar_observacion_perfil_personal(observaciones);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const ver_info_hv = async (data, admin = false) => {
	administra = admin;
	info_solicitud = data;
	let { id, identificacion, nombre_completo, cargo, proposito, perfil_persona, correo, correo_personal, telefono, direccion, lugar_residencia, oficina } = data;
	id_persona = id;
	identificacion_persona = identificacion;
	$(`#nombre_completo`).html(nombre_completo);
	$(`#cargo`).html(cargo);
	$(`#codigo_cargo`).html();
	$(`#rol`).html();
	$(`#proposito_cargo`).html(proposito);
	$(`#perfil_persona`).html(perfil_persona);
	let formacion = await obtener_formacion_academica(id);
	pintar_formacion_academica(formacion);

	let observaciones = await obtener_observacion_perfil_persona(id);
	pintar_observacion_perfil_personal(observaciones);
	$("#contacto").append(`
		<p><span class="text-primary fa fa-flag"></span> Lugar de Residencia (ciudad/departamento): ${lugar_residencia}</p>	
		<p><span class="text-primary fa fa-location-arrow"></span> Dirección: ${direccion}</p>	
		<p><span class="text-primary fa fa-bookmark"></span> Oficina: ${oficina}</p>	
		<p><span class="text-primary fa fa-envelope"></span> Correo institucional: ${correo}</p>	
		<p><span class="text-primary fa fa-envelope"></span> Correo Personal: ${correo_personal}</p>
		<p><span class="text-primary fa fa-mobile"></span> Teléfono: ${telefono}</p>
		<p><span class="text-primary fa fa-phone"></span> Teléfono universidad: 3030923</p>
	`);

	$('#btn_info_contacto').click(() => {
		$("#from_info_contacto input[name='info_lugar_residencia']").val(lugar_residencia);
		$("#from_info_contacto input[name='info_direccion']").val(direccion);
		$("#from_info_contacto input[name='info_oficina']").val(oficina);
		$("#from_info_contacto input[name='info_correo_personal']").val(correo_personal);
		$("#from_info_contacto input[name='info_telefono']").val(telefono);
		$("#modal_info_contacto").modal();
	});
}

const guardar_formacion_academica = () => {
	let data = new FormData(document.getElementById("form_guardar_formacion_academica"));
	data.append('id_persona', id_persona);
	enviar_formulario(`${ruta}guardar_formacion_academica`, data, (resp) => {
		let { tipo, mensaje, titulo, formacion } = resp;
		if (tipo == 'success') {
			$("#modal_guardar_formacion_academica").modal("hide");
			$("#form_guardar_formacion_academica").get(0).reset();
			pintar_formacion_academica(formacion);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_formacion_academica = (id) => {
	let data = new FormData(document.getElementById("form_guardar_formacion_academica"));
	data.append('id', id);
	enviar_formulario(`${ruta}modificar_formacion_academica`, data, (resp) => {
		let { tipo, mensaje, titulo, formacion } = resp;
		if (tipo == 'success') {
			$("#modal_guardar_formacion_academica").modal("hide");
			$("#form_guardar_formacion_academica").get(0).reset();
			pintar_formacion_academica(formacion);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const listar_valor_parametro = (id_parametro, tabla) => {
	consulta_ajax(`${ruta}listar_valor_parametro`, { id_parametro }, (data) => {
		$(`#${tabla} tbody`).off('click', 'tr').off('click', 'tr td .modificar').off('click', 'tr td .eliminar');
		const myTable = $(`#${tabla}`).DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data,
			columns: [
				{ data: 'valora' },
				{ data: 'valor' },
				{ data: 'valorx' },
				{ data: 'accion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		if (id_parametro == 241) myTable.column(0).visible(false);
		else myTable.column(0).visible(true);

		$(`#${tabla} tbody`).on('click', 'tr', function () {
			$(`#${tabla} tbody tr`).removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$(`#${tabla} tbody`).on("click", "tr td .modificar", function () {
			let data = myTable.row($(this).parent()).data();
			if (id_parametro == 241) {
				gestionar_permiso_texto('Modificar Horas.?', '', 'Ingrese la cantidad de horas.', 'number', () => { modificar_horas(data.id, id_parametro, tabla) });
			} else {
				$("#form_valor_parametro select[name='id_clasificacion']").val(data.id_clasificacion);
				$("#form_valor_parametro textarea[name='pregunta']").val(data.valor);
				$("#form_valor_parametro select[name='id_tipo_respuesta']").val(data.id_tipo_respuesta);
				callbak_activo = (resp) => modificar_pregunta(data.id, 243, 'tabla_preguntas');
				$("#modal_nuevo_valor_parametro").modal();
			}
		});

		$(`#${tabla} tbody`).on("click", "tr td .eliminar", function () {
			let data = myTable.row($(this).parent()).data();
			eliminar_datos({ id: data.id, title: "Eliminar datos?", tabla_bd: 'valor_parametro' }, () => {
				listar_valor_parametro(id_parametro, tabla);
			});
		});
	});
}

const modificar_horas = (id, id_parametro, tabla) => {
	consulta_ajax(`${ruta}modificar_horas`, { id, valor_nuevo, id_parametro }, resp => {
		const { mensaje, titulo, tipo } = resp;
		if (tipo === 'success') {
			listar_valor_parametro(id_parametro, tabla);
			swal.close();
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const guardar_pregunta = (id_parametro, tabla) => {
	let data = new FormData(document.getElementById("form_valor_parametro"));
	enviar_formulario(`${ruta}guardar_pregunta`, data, resp => {
		const { mensaje, titulo, tipo } = resp;
		if (tipo === 'success') {
			listar_valor_parametro(id_parametro, tabla);
			swal.close();
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_pregunta = (id_pregunta, id_parametro, tabla) => {
	let data = new FormData(document.getElementById("form_valor_parametro"));
	data.append('id_pregunta', id_pregunta);
	enviar_formulario(`${ruta}modificar_pregunta`, data, resp => {
		const { mensaje, titulo, tipo } = resp;
		if (tipo === 'success') {
			listar_valor_parametro(id_parametro, tabla);
			swal.close();
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const filtrar_solicitudes = () => {
	let fecha_i = $("#Modal_filtro input[name='fecha_i']").val();
	let fecha_f = $("#Modal_filtro input[name='fecha_f']").val();
	let periodo = $("#Modal_filtro input[name='filtro_periodo']").val();
	data = {
		'fecha_i': fecha_i,
		'fecha_f': fecha_f,
		'periodo': periodo
	}
	fecha_i = fecha_i ? fecha_i : 0;
	fecha_f = fecha_f ? fecha_f : 0;
	id = 0;
	listar_personas('', 0, data);
}

const pintar_datos_combo = (datos, combo, mensaje, sele = '') => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach(elemento => {
		$(combo).append(`<option value='${elemento.id}'> ${elemento.valor}</option>`);
	});
	$(combo).val(sele);
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

const get_personas_notificar_th = (idparametro) => {
	return new Promise((resolve) => {
		consulta_ajax(
			`${ruta}get_personas_notificar_th`,{idparametro},(data) => resolve(data)
		);
	});
};

const enviar_encuesta_entrenamiento_general = async (persona_id) =>{
	let data  = await obtener_info_persona(persona_id);
	let { nombre_completo, correo } = data;
	let ser = `<a href="${Traer_Server()}index.php/talento_cuc/encuesta_entrenamiento/${persona_id}"><b>agil.cuc.edu.co</b></a>`;
	let titulo = 'Talento CUC';
	let mensaje = `Estimado ${nombre_completo},
	Para UNICOSTA es importante su opinión, por eso queremos invitarte a diligenciar la siguiente encuesta sobre tu proceso de entrenamiento.
	<br>
	Para ingresar haga Aquí.
	<br><br>
	${ser}`; 
	enviar_correo_personalizado("Tal", mensaje, correo, "Funcionario", "AGIL Talento CUC", titulo, "ParCodAdm", 1);
}

const enviar_correo_confirmacion = async (id_entrenamiento) => {
	let data  = await obtener_entrenamiento(null, id_entrenamiento);
	let { nombre_persona, correo_persona, persona_id, oferta } = data[0];
	let sw = false;
	let titulo = 'Talento CUC';
	let mensaje = `Estimado,
	Le informamos que fue confirmada su asistencia y finalización del entrenamiento <strong>${oferta}</strong>.`;
	enviar_correo_personalizado("Tal", mensaje, correo_persona, nombre_persona, "AGIL Talento CUC", titulo, "ParCodAdm", 1);

	let correos = await get_personas_notificar_th(249);
	let ser = `<a href="${Traer_Server()}index.php/talento_cuc"><b>agil.cuc.edu.co</b></a>`;
	let msj = `Le informamos que fue confirmada la asistencia y finalización del entrenamiento <strong>${oferta}</strong> del funcionario <strong>${nombre_persona}</strong>.
	Para mas información ingrese Aquí.
	<br><br>
	${ser}`; 
	if(data[1]) sw = true;
	enviar_correo_personalizado("Tal", msj, correos[0], 'Funcionario', "AGIL Talento CUC", titulo, "ParCodAdm", 3);
	if(sw) enviar_encuesta_entrenamiento_general(persona_id);
}

const bar_estado = async (progress) => {
	$('#bar_estado').css('width', progress + '%');
	$(".text_barra").html(progress+'%');
}

const guardar_confirmacion_entrenamiento = (id_entrenamiento, id_funcionario) =>{
	let image = document.getElementById("canvas").toDataURL();
	consulta_ajax(`${ruta}guardar_confirmacion_entrenamiento`, {id_entrenamiento: id_entrenamiento, firma: image, funcionario: id_funcionario}, (resp) => {
		let { tipo, mensaje, titulo, progress } = resp;
		if (tipo == 'success') {
			swal.close();
			$("#modal_solicitar_firma").modal("hide");
			bar_estado(progress);
			listar_asistencias_entrenamiento(id_funcionario);
			enviar_correo_confirmacion(id_entrenamiento);
		}else MensajeConClase(mensaje, tipo, titulo);
	});
}

const pedir_firma = (title, detalle) => {
	$("#modal_solicitar_firma").modal();
	$("#div_firmar").html(`<p class="text-justify"><span class="fa fa-edit"></span> Registra tu firma:</p>
	<div id="content_firmas"><p style="text-align:center;">Configurando...</p></div>`);
	newCanvas();
	$("#enviar_firma").off("click");
	$("#enviar_firma").click(() => {
		let image = document.getElementById("canvas").toDataURL();
		msj_confirmacion(title, detalle, () => callbak_activo());
	});
  }

const listar_asistencias_entrenamiento = (id_funcionario) => {
	consulta_ajax(`${ruta}listar_asistencias_entrenamiento`, { id_funcionario }, (data) => {
		$(`#tabla_entrenamientos_persona tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)').off("click", "tr td .seleccionar");
		const myTable = $('#tabla_entrenamientos_persona').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			paging: false,
			info: false,
			data,
			columns: [
				{ data: 'nombre_completo' },
				{ data: 'identificacion' },
				{ data: 'oferta' },
				{ data: 'fecha' },
				{ data: 'gestion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_entrenamientos_persona tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let { } = myTable.row($(this).parent()).data();
		});

		$('#tabla_entrenamientos_persona tbody').on('click', 'tr', function () {
			$('#tabla_entrenamientos_persona tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_entrenamientos_persona tbody').on('dblclick', 'tr', function () {
			$('#tabla_entrenamientos_persona tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_entrenamientos_persona tbody').on("click", "tr td .seleccionar", function () {
			let { id, id_funcionario } = myTable.row($(this).parent()).data();
			callbak_activo = (resp) => guardar_confirmacion_entrenamiento(id, id_funcionario);
			pedir_firma('¿ Confirmar asistencia ?', 'Desea confirmar la asistencia al entrenamiento?. Tener en cuenta que no podra revertir esta acción.');
		});
	});
}

const buscar_persona_hv = (dato) => {
	consulta_ajax(`${ruta}buscar_persona`, { dato }, data => {
		$(`#tabla_busqueda_hv tbody`).html("").off('click', 'tr td .seleccionar').off('dblclick', 'tr').off('click', 'tr');
		data.map(({ id, nombre_completo, identificacion }) => {
			$("#tabla_busqueda_hv tbody").append(`<tr><td>${nombre_completo}</td><td>${identificacion}</td><td><span class="btn btn-primary seleccionar${id}"><span class="fa fa-check"></span></span></td></tr>`);
			$(`#tabla_busqueda_hv .seleccionar${id}`).on('click', function () {
				window.open(`${Traer_Server()}index.php/talento_cuc/hoja_vida/${id}`);
			});
		})
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

const gestionar_permiso_texto = (title, text, inputPlaceholder, inputType, callback, value='') => {
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
		inputPlaceholder: inputPlaceholder,
		inputType: inputType,
		inputValue: value,
	}, function (message) {
		if (message === false) return false;
		if (message === "") swal.showInputError(inputPlaceholder);
		else {
			valor_nuevo = message;
			callback();
		}
	});
}

const msj_confirmacion = (title, text, callback, confirm = 'Si, aceptar!', cancel = 'No, Cancelar!') => {
	swal({
		title,
		text,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: confirm,
		cancelButtonText: cancel,
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	}, confirm => {
		if (confirm) callback();
	});
};

const avalar_soporte = (data, callback = () => { }) => {
	data.observacion = valor_nuevo;
	consulta_ajax(`${ruta}avalar_soporte`, data, resp => {
		const { mensaje, titulo, tipo } = resp;
		if (tipo === 'success') {
			swal.close();
			listar_avalar_soportes(data.identificacion_persona);
			listar_personas();
			mostrar_notificaciones();
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}
const avalar_soportes_masivo = (data) => {
	consulta_ajax(`${ruta}avalar_soportes_masivo`, data, resp => {
		const { mensaje, titulo, tipo } = resp;
		if (tipo === 'success') {
			swal.close();
			listar_avalar_soportes(data.identificacion_persona);
			mostrar_notificaciones();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const listar_personas = (buscar='', id = '', filtros = {}) => {
	let { fecha_i, fecha_f, periodo } = filtros
	$(`#tabla_solicitudes tbody`)
		.off('click', 'tr td:nth-of-type(1)')
		.off('click', 'tr')
		.off('click', 'tr td')
		.off('dblclick', 'tr')
		.off("click", "tr td .formacion")
		.off("click", "tr td .entrenamiento")
		.off("click", "tr td .hv")
		.off("click", "tr td .avalar_soporte")
		.off("click", "tr td .formacion_nuevo");
	consulta_ajax(`${ruta}listar_personas`, { buscar, id, fecha_i, fecha_f, periodo }, resp => {
		const myTable = $("#tabla_solicitudes").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: resp[0],
			columns: [
				{ data: 'ver' },
				{ data: 'nombre_completo' },
				{ data: 'identificacion' },
				{ data: 'cargo' },
				{ data: 'gestion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_solicitudes tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let data = myTable.row($(this).parent()).data();
			id_persona = data.id;
			id_solicitud = data.id_solicitud;
			info_solicitud = data;
			$("#btnActa").attr("href", `${Traer_Server()}archivos_adjuntos/talentohumano/actas/ACTA_${id_solicitud}.pdf`);
			ver_detalle_evalucaion(info_solicitud);
		});

		$('#tabla_solicitudes tbody').on('click', 'tr td', function () {
			const data = myTable.row($(this).parent()).data();
			info_solicitud = data;
			id_persona = data.id;
		});

		$('#tabla_solicitudes tbody').on('dblclick', 'tr', function () {
			let data = myTable.row($(this).parent()).data();
			id_persona = data.id;
		});

		$('#tabla_solicitudes tbody').on("click", "tr td .formacion", function () {
			let data = myTable.row($(this).parent()).data();
			id_persona = data.id;
			id_solicitud = data.id_solicitud;
			if (data.estado_eval === 'Eval_Form') $("#btn_calcular_planformacion").addClass('oculto');
			else $("#btn_calcular_planformacion").removeClass('oculto');
			calcular_planFormacion(id_solicitud, data.estado_eval);
			$("#modal_plan_formacion").modal();
		});

		$('#tabla_solicitudes tbody').on("click", "tr td .entrenamiento", async function () {
			let data = myTable.row($(this).parent()).data();
			id_persona = data.id;
			info_solicitud = data;
			listar_plan_entrenamiento(data.identificacion);
			$("#modal_listar_plan_entrenamiento").modal();
		});

		$('#tabla_solicitudes tbody').on("click", "tr td .hv", function () {
			let data = myTable.row($(this).parent()).data();
			info_solicitud = data;
			$(".hv").attr("href", `${Traer_Server()}index.php/talento_cuc/hoja_vida/${data.id}`);
		});

		$('#tabla_solicitudes tbody').on("click", "tr td .avalar_soporte", function () {
			let data = myTable.row($(this).parent()).data();
			listar_avalar_soportes(data.identificacion);
			$("#modal_avalar_soportes_plan_formacion").modal();
		});

		$('#tabla_solicitudes tbody').on("click", "tr td .formacion_nuevo", async function () {
			let data = myTable.row($(this).parent()).data();
			id_persona = data.id;
			let datos = await get_competencias_req(id_persona, 1);
			let cant = datos.length;
			if (cant > 0) $("#btn_calcular_planformacion").addClass('oculto');
			else $("#btn_calcular_planformacion").removeClass('oculto');
			calcular_planFormacion_ingreso(id_persona, cant, data.identificacion);
			$("#modal_plan_formacion").modal();
		});
	});

}

const get_competencias_req = async (idpersona, filtro) => {
	return new Promise(resolve => {
		let url = `${ruta}get_competencias_req`;
		consulta_ajax(url, { idpersona, filtro }, resp => {
			resolve(resp);
		});
	});
}

const estado_entrenamiento = async (idpersona) => {
	return new Promise(resolve => {
		let url = `${ruta}estado_entrenamiento`;
		consulta_ajax(url, { idpersona }, resp => {
			resolve(resp);
		});
	});
}

const listar_avalar_soportes = (identificacion) => {
	consulta_ajax(`${ruta}listar_avalar_soportes`, { identificacion }, (data) => {
		$(`#tabla_avalar_soportes_plan_formacion tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)').off('click', 'tr td .vistob').off('click', 'tr td .vistom');
		const myTable = $('#tabla_avalar_soportes_plan_formacion').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
				{ data: 'ver' },
				{ data: 'competencia' },
				{ data: 'nombre_formacion' },
				{ data: 'fecha_formacion' },
				{ data: 'horas_formacion' },
				{ data: 'gestion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_avalar_soportes_plan_formacion tbody').on('click', 'tr td:nth-of-type(1)', function () {
			$('#tabla_entrenamiento tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
			let info = myTable.row($(this).parent()).data();
			$(".ver_soporte").attr("href", `${Traer_Server()}archivos_adjuntos/talento_cuc/plan_formacion/${info.nombre_archivo}`);
			$(".link_soporte").attr("href", `${info.link_soporte}`);
		});

		$('#tabla_avalar_soportes_plan_formacion tbody').on('click', 'tr', function () {
			$('#tabla_entrenamiento tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_avalar_soportes_plan_formacion tbody').on('dblclick', 'tr', function () {
			$('#tabla_entrenamiento tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$(`#tabla_avalar_soportes_plan_formacion tbody`).on("click", "tr td .vistob", function () {
			let info = myTable.row($(this).parent()).data();
			const dato = {
				id: info.id,
				identificacion_persona: info.id_persona,
				nextState: 1,
				success: 'Soporte Aprobado Exitosamente!'
			};
			msj_confirmacion('¿ Aprobar soporte ?', '', () => avalar_soporte(dato));
		});

		$(`#tabla_avalar_soportes_plan_formacion tbody`).on("click", "tr td .vistom", function () {
			let info = myTable.row($(this).parent()).data();
			let dato = {
				id: info.id,
				identificacion_persona: info.id_persona,
				nextState: 2,
				success: 'Soporte Desaprobado Exitosamente!'
			};
			gestionar_permiso_texto('¿ Desaprobar soporte ?', 'Por favor digite motivo de rechazo.', 'Motivo de rechazo', 'text',
				(msj) => {
					data.msj = msj;
					avalar_soporte(dato);
				}
			);
		});

		$(".btn_aprobar_todo").click(function () {
			const dato = {
				id: data,
				identificacion_persona: identificacion,
				nextState: 1,
				success: 'Soportes Aprobados Exitosamente!'
			};
			msj_confirmacion('¿ Aprobar Todos ?', 'Desea aprobar todos los soportes de formación?. Tener en cuenta que no podra revertir esta acción.', () => avalar_soportes_masivo(dato));
		});
	});
}

const get_info_seleccion = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}get_info_seleccion`;
		consulta_ajax(url, { id }, (resp) => resolve(resp));
	});
};

const listar_plan_entrenamiento = (idpersona) => {
	consulta_ajax(`${ruta}listar_plan_entrenamiento`, { idpersona }, (resp) => {
		$(`#tabla_entrenamiento tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .modificar').off('click', 'tr td .eliminar').off('click', 'tr td .get_link');
		const myTable = $('#tabla_entrenamiento').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: resp,
			columns: [
				{ data: 'oferta' },
				{ data: 'facilitador' },
				{ data: 'duracion' },
				{ data: 'lugar' },
				{ data: 'fecha_entrenamiento' },
				{ data: 'gestion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_entrenamiento tbody').on('click', 'tr', function () {
			$('#tabla_entrenamiento tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_entrenamiento tbody').on('dblclick', 'tr', function () {
			$('#tabla_entrenamiento tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$(`#tabla_entrenamiento tbody`).on("click", "tr td .modificar", function () {
			let { id, id_lugar, duracion, fecha_entrenamiento, facilitador, id_facilitador, link, id_oferta, oferta } = myTable.row($(this).parent()).data();
			$("#form_planentrenamiento .nombre_completo").html(facilitador);
			$("#form_planentrenamiento .identificacion").html(id_facilitador);
			$("#form_planentrenamiento input[name='id_oferta']").val(id_oferta);
			$("#form_planentrenamiento input[name='oferta']").val(oferta);
			$("#form_planentrenamiento select[name='id_lugar']").val(id_lugar);
			$("#form_planentrenamiento input[name='link_reunion']").val(link);
			$("#form_planentrenamiento input[name='duracion']").val(duracion);
			$("#form_planentrenamiento input[name='fecha_entrenamiento']").val(fecha_entrenamiento);
			callbak_activo = (data) => {
				const { id, identificacion, nombre_completo, cargo } = data;
				facilitador = identificacion;
				$(".nombre_completo").html(nombre_completo);
				$(".identificacion").html(identificacion);
				$("#modal_buscar_persona").modal('hide');
			};
			callbak_ = (resp) => modificar_plan_entrenamiento(id);
			$("#modal_nuevo_planentrenamiento").modal();
		});

		$(`#tabla_entrenamiento tbody`).on("click", "tr td .eliminar", function () {
			let data = myTable.row($(this).parent()).data();
			eliminar_datos({ id: data.id, title: "Eliminar datos?", tabla_bd: 'talentocuc_plan_entrenamiento' }, () => {
				listar_plan_entrenamiento(data.id_evaluado);
			});
		});

		$(`#tabla_entrenamiento tbody`).on("click", "tr td .get_link", function () {
			let { id, link } = myTable.row($(this).parent()).data();
			MensajeConClase(`${link}`, 'success', 'Link de Entrenamiento!.');
		});

		if(resp.length > 0) $(".btn_enviar_entrenamiento").show();
		else $(".btn_enviar_entrenamiento").hide();
	});

	$(".btn_enviar_entrenamiento").click(function () {
		msj_confirmacion('¿ Estas Seguro ?', `Desea enviar el Plan de Entrenamiento?`, () => enviar_entrenamiento(idpersona));
	});

	$(".btn_send_acta").click( async function () {
		let { id_jefe, nombre_jefe, cargo_id, cargo } = await get_info_seleccion(id_persona);
		$("#form_acta_cargo input[name='nombre_jefe']").val(nombre_jefe);
		$("#form_acta_cargo input[name='id_jefe']").val(id_jefe);
		$("#form_acta_cargo input[name='cargo_id']").val(cargo_id);
		$("#form_acta_cargo input[name='nombre_cargo']").val(cargo);
		$("#modal_enviar_acta_cargo").modal();
	});

	$(".btn_ver_actas").click(() => {
		listar_actas_cargo(idpersona);
		$("#modal_actas_cargo").modal();
	});
}

const generar_pdf = (idpersona, tipo = 0) => {
	console.log("generando");
	const route = `${Traer_Server()}index.php/talento_cuc/detalle_entrenamiento/${idpersona}/${tipo}`;
	window.open(route, '_blank');
	window.focus()
	console.log("generado");
	return true;
}

const enviar_entrenamiento = (idpersona) =>{
	consulta_ajax(`${ruta}enviar_entrenamiento`, { idpersona }, resp => {
		const { mensaje, titulo, tipo } = resp;
		if (tipo === 'success') {
			generar_pdf(idpersona);
			enviar_correo_entrenamiento(idpersona);
			notificar_responsable_entrenamiento(idpersona);
			listar_plan_entrenamiento(idpersona);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const enviar_correo_entrenamiento = async (identificacion) => {
	generar_pdf(identificacion,1);
	let { id, nombre_completo, correo } = await obtener_info_persona('',identificacion);
	let ser = `<a href="${Traer_Server()}index.php/talento_cuc/hoja_vida/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 1;
	let titulo = 'Talento CUC';
	let mensaje = `Estimado, 
	Te damos la bienvenida nuevamente a tu casa Unicosta, queremos que conozcas tus principales procesos de interés, por eso te enviamos tu plan de entrenamiento, 
	recuerda que deberás ir reportando el avance de estos y que te llegarán citaciones a través del calendario de Outlook.
	<br> 
	Adjunto te enviamos también la oferta de entrenamientos, esperamos que la veas y que si hay algún tema de interés para ti en ella nos los hagas saber para agendártelo 
	dentro de tu plan.  
	<br>
	<p>Ver <a href="${Traer_Server()}archivos_adjuntos/talento_cuc/entrenamiento/Plan_Entrenamiento_${identificacion}.pdf"><b>Plan de Entrenamiento</b></a></p>
	Haz clic aquí para acceder:
	<br><br>
	${ser}`;
	enviar_correo_personalizado("Tal", mensaje, correo, nombre_completo, "AGIL Talento CUC", titulo, "ParCodAdm", tipo);
}

const obtener_entrenamiento = async (idpersona, id_entrenamiento=null) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_entrenamiento`;
		consulta_ajax(url, { idpersona,id_entrenamiento }, resp => {
			resolve(resp);
		});
	});
}

const notificar_responsable_entrenamiento = async (idpersona) => {
	let data  = await obtener_entrenamiento(idpersona);
	data[0].map(function (dato) {
		let modalidad = dato.tipo_mod == 'Bib_Acd' ? 'Virtual' : 'Presencial';
		let tipo = 1;
		let titulo = 'Talento CUC';
		let mensaje = `Estimado, 
		Le informamos que fue asignado para proveer el entrenamiento <strong>${dato.oferta}</strong> 
		a las <strong>${dato.hora}</strong> el día <strong>${dato.fecha}</strong> en la modalidad <strong>${modalidad}</strong> en <strong>${dato.lugar}</strong>.
		<p>Click aquí para unirse al <a href="${dato.link}" target="_blank"><b>Entrenamiento</b></a>.</p>
		Agradecemos su oportuna colaboración.`;
		enviar_correo_personalizado("Tal", mensaje, dato.correo, dato.facilitador, "AGIL Talento CUC", titulo, "ParCodAdm", tipo);
	});
}

const guardar_planentrenamiento = () => {
	let { identificacion } = info_solicitud;
	let data = new FormData(document.getElementById("form_planentrenamiento"));
	data.append('id_evaluado', identificacion);
	data.append('facilitador', facilitador);
	enviar_formulario(`${ruta}guardar_planentrenamiento`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_nuevo_planentrenamiento").modal("hide");
			$("#form_planentrenamiento").get(0).reset();
			listar_plan_entrenamiento(identificacion);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_plan_entrenamiento = (id_entrenamiento) => {
	let { identificacion } = info_solicitud;
	let data = new FormData(document.getElementById("form_planentrenamiento"));
	data.append('id', id_entrenamiento);
	data.append('facilitador', facilitador);
	enviar_formulario(`${ruta}modificar_plan_entrenamiento`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_nuevo_planentrenamiento").modal("hide");
			$("#form_planentrenamiento").get(0).reset();
			listar_plan_entrenamiento(identificacion);
		}
		MensajeConClase(mensaje, tipo, titulo);
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
	const { nombre_completo, identificacion, cargo } = data;
	let { fecha_registra, tipo, state, acta, recibido, calificacion, observacion, firma, periodo } = await get_detalle_solicitud(id_solicitud);
	$(".info_funcionario").html(nombre_completo);
	$(".info_identificacion").html(identificacion);
	$(".info_fecha").html(fecha_registra);
	$(".info_metodo").html(tipo);
	$(".info_estado").html(state);
	$(".info_periodo").html(periodo);
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
	if (acta == 1) $(".btn_ver_acta").removeClass('oculto');
	else $(".btn_ver_acta").addClass('oculto');
	$("#modal_detalle_evaluacion").modal();
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

const guardar_plan_formacion = (data, id_solicitud_evaluado) => {
	consulta_ajax(`${ruta}guardar_plan_formacion`, { id_solicitud: id_solicitud_evaluado, data_formacion: data }, resp => {
		const { mensaje, titulo, tipo, estado_eval } = resp;
		if (tipo === 'success') {
			swal.close();
			$("#btn_calcular_planformacion").addClass('oculto');
			calcular_planFormacion(id_solicitud_evaluado, estado_eval);
			listar_personas();
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const calcular_planFormacion = (id_solicitud_evaluado, estado_eval) => {
	consulta_ajax(`${ruta}calcular_planFormacion`, { id_solicitud: id_solicitud_evaluado }, (data) => {
		$(`#tabla_planformacion tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .seleccionar');
		const myTable = $('#tabla_planformacion').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
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
			buttons: get_botones(),
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
			listar_plaformacion_personal(id_persona, id_competencia);
			$("#modal_detalle_planformacion").modal();
		});

		$('#btn_calcular_planformacion').on('click', function () {
			msj_confirmacion('¿ Estas Seguro ?', `Desea guardar el plan de formación?. Tener en cuenta que no podra revertir esta acción.!`, () => guardar_plan_formacion(data, id_solicitud_evaluado));
		});

		if (data.length > 0 && estado_eval != 'Eval_Form') $("#btn_calcular_planformacion").removeClass('oculto');
		else $("#btn_calcular_planformacion").addClass('oculto');
	});
}

const guardar_plan_formacion_ingreso = (data, id_evaluado, identificacion) => {
	consulta_ajax(`${ruta}guardar_plan_formacion_ingreso`, { idpersona: id_evaluado, data_formacion: data, identificacion: identificacion }, resp => {
		const { mensaje, titulo, tipo } = resp;
		if (tipo === 'success') {
			swal.close();
			$("#btn_calcular_planformacion").addClass('oculto');
			calcular_planFormacion_ingreso(id_evaluado);
			listar_personas();
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const calcular_planFormacion_ingreso = (id_evaluado, cant, identificacion) => {
	consulta_ajax(`${ruta}calcular_planFormacion_ingreso`, { idpersona: id_evaluado }, (data) => {
		$(`#tabla_planformacion tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .seleccionar');
		const myTable = $('#tabla_planformacion').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
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
			buttons: get_botones(),
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
			listar_plaformacion_personal(id_persona, id_competencia);
			$("#modal_detalle_planformacion").modal();
		});

		$('#btn_calcular_planformacion').on('click', function () {
			msj_confirmacion('¿ Estas Seguro ?', `Desea guardar el plan de formación?. Tener en cuenta que no podra revertir esta acción.!`, () => guardar_plan_formacion_ingreso(data, id_evaluado, identificacion));
		});

		if (data.length > 0 && cant == 0) $("#btn_calcular_planformacion").removeClass('oculto');
		else $("#btn_calcular_planformacion").addClass('oculto');
	});
}

const listar_plaformacion_personal = (id, id_competencia) => {
	consulta_ajax(`${ruta}listar_plaformacion_personal`, { id, id_competencia }, (data) => {
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
				{ data: 'asistencia' },
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

const listar_competencias = (id_formacion) => {
	consulta_ajax(`${ruta}get_competencias_formacion`, { id_formacion }, (data) => {
		$(`#tabla_competencias_formacion tbody`).off('click', 'tr').off('dblclick', 'tr');
		let i = 0;
		const myTable = $('#tabla_competencias_formacion').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
				{ 
					render: function (data, type, full, meta) {
						i++;
						return i;
					} 
				},
				{ data: 'competencia' },
				{ data: 'pregunta' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_competencias_formacion tbody').on('click', 'tr', function () {
			$('#tabla_competencias_formacion tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_competencias_formacion tbody').on('dblclick', 'tr', function () {
			$('#tabla_competencias_formacion tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});
	});
}

const listar_planFormacion = (filtros = {}) => {
	let { fecha_i, fecha_f, texto, id_lugar } = filtros
	consulta_ajax(`${ruta}listar_planFormacion`, {fecha_i, fecha_f, texto, id_lugar}, (data) => {
		$(`#tabla_planformacion tbody`)
			.off('click', 'tr')
			.off('dblclick', 'tr')
			.off('click', 'tr td .modificar')
			.off('click', 'tr td .eliminar')
			.off('click', 'tr td .get_link')
			.off('click', 'tr td .finalizar')
			.off('click', 'tr td .competencias')
			.off('click', 'tr td .actualizar')
			.off('click', 'tr td:nth-of-type(1)');
		const myTable = $('#tabla_pformacion').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
				{ defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span>Ver</span></span>' },
				{ data: 'tema' },
				// { data: 'funcionario' },
				// { data: 'duracion' },
				// { data: 'lugar' },
				{ data: 'fecha_formacion' },
				{ data: 'fecha_cierre_link' },
				{ data: 'gestion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_pformacion tbody').on('click', 'tr', function () {
			$('#tabla_pformacion tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_pformacion tbody').on('dblclick', 'tr', function () {
			$('#tabla_pformacion tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_pformacion tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let { id, tema, lugar, duracion, fecha_formacion, funcionario } = myTable.row($(this).parent()).data();
			$(".info_facilitador").html(funcionario);
			$(".info_lugar").html(lugar);
			$(".info_tema").html(tema);
			$(".info_duracion").html(duracion);
			$(".info_fecha").html(fecha_formacion);
			$(".info_fecha_cierre").html(fecha_formacion);
			listar_competencias(id);
			$("#modal_detalle_formacion").modal();
		});

		$(`#tabla_pformacion tbody`).on("click", "tr td .modificar", function () {
			let { id, tema, id_lugar, duracion, fecha_formacion, funcionario } = myTable.row($(this).parent()).data();
			$("#form_planformacion input[name='funcionario']").val(funcionario);
			$("#form_planformacion select[name='id_lugar']").val(id_lugar);
			$("#form_planformacion input[name='tema']").val(tema);
			$("#form_planformacion input[name='duracion']").val(duracion);
			$("#form_planformacion input[name='fecha_formacion']").val(fecha_formacion);
			callbak_ = (resp) => modificar_plan_formacion(id);
			$("#modal_nuevo_planformacion").modal();
		});

		$(`#tabla_pformacion tbody`).on("click", "tr td .eliminar", function () {
			let { id } = myTable.row($(this).parent()).data();
			eliminar_datos({ id, title: "Eliminar datos?", tabla_bd: 'talentocuc_plan_formacion' }, () => {
				listar_planFormacion();
			});
		});

		$(`#tabla_pformacion tbody`).on("click", "tr td .competencias", function () {
			let { id } = myTable.row($(this).parent()).data();
			get_competencia(id);
			$('#modal_buscar_competencia').modal();
		});

		$(`#tabla_pformacion tbody`).on("click", "tr td .get_link", function () {
			let { id, estado_link, duracion } = myTable.row($(this).parent()).data();
			valor_nuevo = duracion * 60;
			if (estado_link == 1) MensajeConClase(`${Traer_Server()}index.php/talento_cuc/asistencia_formacion/${id}`, 'success', 'Link generado!.');
			else msj_confirmacion('¿ Estas Seguro ?', `Desea generar el link de formación?. Tener en cuenta que no podra revertir esta acción.!`, () => generar_link(id));
		});

		$(`#tabla_pformacion tbody`).on("click", "tr td .actualizar", function () {
			let { id, estado_link } = myTable.row($(this).parent()).data();
			gestionar_permiso_texto('Estas Seguro.?', 'Desea actualizar el tiempo del link asistencia de formación?. Tener en cuenta que no podra revertir esta acción.!', 'Por favor indique el tiempo en minutos:', 'number', () => { generar_link(id) });
		});

		$(`#tabla_pformacion tbody`).on("click", "tr td .finalizar", function () {
			let { id } = myTable.row($(this).parent()).data();
			msj_confirmacion('¿ Estas Seguro ?', `Desea finalizar el plan formación?. Tener en cuenta que no podra revertir esta acción.!`, () => finalizar_formacion(id));
		});
	});
}

const generar_link = (id) => {
	consulta_ajax(`${ruta}generar_link`, { id_formacion: id, minutos: valor_nuevo }, resp => {
		let { mensaje, tipo, titulo } = resp;
		if (tipo == 'success') {
			swal.close();
			MensajeConClase(`${Traer_Server()}index.php/talento_cuc/asistencia_formacion/${id}`, 'success', 'Link generado!.');
			listar_planFormacion();
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const finalizar_formacion = (id) => {
	consulta_ajax(`${ruta}finalizar_formacion`, { id_formacion: id }, resp => {
		let { mensaje, tipo, titulo } = resp;
		if (tipo == 'success') {
			swal.close();
			listar_planFormacion();
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const get_competencia = (id_formacion) => {
	consulta_ajax(`${ruta}get_competencia`, { id_formacion }, (data) => {
		$(`#tabla_buscar_competencias tbody`).off('click', '.habilitar').off('click', 'tr');
		$(`#tabla_buscar_competencias tbody`).off('click', '.desabilitar').off('click', 'tr');
		const myTable = $('#tabla_buscar_competencias').DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data,
			columns: [
				{ data: 'competencia' },
				{ data: 'pregunta' },
				{
					"render": function (data, type, full, meta) {
						let { id_permiso } = full;
						let resp = '<div class="btn-group btn-group-toggle" data-toggle="buttons"><label class="btn btn-primary active habilitar">Habilitar</label></div>';
						if (id_permiso != null) resp = '<div class="btn-group btn-group-toggle" data-toggle="buttons"><label class="btn btn-primary active desabilitar">Desabilitar</label></div>';
						return resp;
					}
				}

			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_buscar_competencias tbody').on('click', 'tr', function () {
			$('#tabla_buscar_competencias tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_buscar_competencias tbody').on('click', '.habilitar', function () {
			let { id_valor_parametro } = myTable.row($(this).parent().parent()).data();
			confirmar_cambio_permiso('Habilitar Permiso .?', '', () => {
				habilitar_permiso(id_valor_parametro, id_formacion);
			});
		});

		$('#tabla_buscar_competencias tbody').on('click', '.desabilitar', function () {
			let { id_permiso } = myTable.row($(this).parent().parent()).data();
			confirmar_cambio_permiso('Deshabilitar Permiso .?', '', () => {
				deshabilitar_permiso(id_permiso, id_formacion);
			});
		});
	});
}

const habilitar_permiso = (id_valor_parametro, id_formacion) => {
	consulta_ajax(`${ruta}habilitar_permiso`, { id_valor_parametro, id_formacion }, resp => {
		let { mensaje, tipo, titulo } = resp;
		if (tipo == 'success') {
			swal.close();
			get_competencia(id_formacion);
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const deshabilitar_permiso = (id_permiso, id_formacion) => {
	consulta_ajax(`${ruta}deshabilitar_permiso`, { id_permiso }, resp => {
		let { mensaje, tipo, titulo } = resp;
		if (tipo == 'success') {
			swal.close();
			get_competencia(id_formacion);
		} else MensajeConClase(mensaje, tipo, titulo);
	});
}

const guardar_planformacion_gen = () => {
	let data = new FormData(document.getElementById("form_planformacion"));
	enviar_formulario(`${ruta}guardar_planformacion_gen`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_nuevo_planformacion").modal("hide");
			$("#form_planformacion").get(0).reset();
			listar_planFormacion();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_plan_formacion = (id_formacion) => {
	let data = new FormData(document.getElementById("form_planformacion"));
	data.append('id', id_formacion);
	enviar_formulario(`${ruta}modificar_plan_formacion`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_nuevo_planformacion").modal("hide");
			$("#form_planformacion").get(0).reset();
			listar_planFormacion();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const cambiar_respuesta = (idpregunta, id_idtipo_respuesta) => {
	data_preguntas.map(function (dato) {
		if (dato.id_pregunta == idpregunta) {
			dato.id_respuesta = id_idtipo_respuesta;
		}
	});
}

const obtener_valor_parametros = async (id) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_valor_parametros`;
		consulta_ajax(url, { id }, resp => {
			resolve(resp);
		});
	});
}

const pintar_respuestas = async (id_pregunta, id_tipo_respuesta) => {
	let respuestas = await obtener_valor_parametros(id_tipo_respuesta);
	respuestas.forEach(elemento => {
		$(`#question_${id_pregunta}`).append(`
			<div class="custom-control custom-radio text-left" style="padding-left:40px;">
				<input type="radio" class="custom-control-input" id="answer_${id_pregunta}_${elemento.id}" name="answer_${id_pregunta}" onclick="cambiar_respuesta('${id_pregunta}','${elemento.id}')">
				<label class="custom-control-label" for="answer_${id_pregunta}_${elemento.id}"> ${elemento.valor}</label>
			</div>`);
	});
}

const obtener_preguntas = async (id_parametro) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_preguntas_encuesta`;
		consulta_ajax(url, { id_parametro }, resp => {
			resolve(resp);
		});
	});
}

const listar_preguntas = async () => {
	data_preguntas = await obtener_preguntas(243);
	data_preguntas.forEach(elemento => {
		$(`#conten_preguntas`).append(`
		<div class="col-md-12 text-left" style="padding-left: 40px;padding-bottom:10px;" id="question_${elemento.id_pregunta}">
			<h4><ul><li>${elemento.pregunta}</li></ul></h4>                     
		</div>`);
		pintar_respuestas(elemento.id_pregunta, elemento.id_tipo_respuesta);
	});
}

const guardar_asistencia = () => {
	let sugerencia = $("#form_asistencia textarea[name='sugerencias']").val();
	let asistencia = $('#form_asistencia input:radio[name=answer_asistencia]:checked').val()
	consulta_ajax(`${ruta}guardar_asistencia`, { data_pregunta: data_preguntas, id_formacion: id_solicitud, answer_asistencia: asistencia, sugerencias: sugerencia }, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$(`#contenido_actas`).empty();
			$("#contenido_actas").append(`<div class="col-md-12 text-center">
				<img src="${Traer_Server()}/imagenes/final.png" alt="..." style='width:30%;'> 
				<h4><b>ENCUESTA FINALIZADA</b></h4>
				</br><a href="${Traer_Server()}index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
				</div>`);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const marcar_califiacacion = () =>{
	let nivel = $('input:radio[name=calificacion]:checked').val();
	$("#nivel").html(nivel);
}

const marcar_asistencia_entrenamiento = (id_entrenamiento, id_evaluado) =>{
	$('#form_asistencia_entrenamiento').get(0).reset();
	$("#encuesta_entrenamiento").modal();
	$('#form_asistencia_entrenamiento').submit(() => {
		msj_confirmacion('¿ Estas seguro ?', 'Desea guardar la asistencia de la oferta de entrenamiento?. Tener en cuenta que no podra revertir esta acción.!', () => guardar_asistencia_entrenamiento(id_entrenamiento, id_evaluado));
		return false;
	});
}

const guardar_asistencia_entrenamiento = (id_entrenamiento, id_evaluado) =>{
	let data = new FormData(document.getElementById("form_asistencia_entrenamiento"));
	data.append('id_entrenamiento', id_entrenamiento);
	data.append('id_persona', id_evaluado);
	enviar_formulario(`${ruta}guardar_asistencia_entrenamiento`,data,(resp) => {
		let { mensaje, tipo, titulo } = resp;
			if (tipo == 'success') {
				swal.close();
				$(`#${id_entrenamiento}`).html('');
				$(`#${id_entrenamiento}`).append(`<span class="dropdown-item"><span class='fa fa-toggle-off'></span> Sin acciones</span>`);
				$("#encuesta_entrenamiento").modal('hide');
				notificar_asistencia_responsable_entrenamiento(id_entrenamiento);
			}else MensajeConClase(mensaje, tipo, titulo);
		}
	);
}
const notificar_asistencia_responsable_entrenamiento = async (id_entrenamiento) =>{
	let data  = await obtener_entrenamiento('',id_entrenamiento);
	let { nombre_persona, correo, facilitador, facilitador_id } = data[0];
	let ser = `<a href="${Traer_Server()}index.php/talento_cuc/asistencia_entrenamiento/${facilitador_id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 1;
	let titulo = 'Talento CUC';
	let mensaje = `Estimado,
	El colaborador <strong>${nombre_persona}</strong> reportó un entrenamiento realizado por usted en la plataforma de Ágil. 
	Por favor ingrese Aquí  para confirmar esta información.
	<br><br>
	${ser}`;
	enviar_correo_personalizado("Tal", mensaje, correo, facilitador, "AGIL Talento CUC", titulo, "ParCodAdm", tipo);
}

const guardar_encuesta_entrenamiento_general = () => {
	let data = new FormData(document.getElementById("form_encuesta_entrenamiento_general"));
	enviar_formulario(`${ruta}guardar_encuesta_entrenamiento_general`, data, (resp) => {
		let { tipo, mensaje, titulo, persona_id } = resp;
		if (tipo == 'success') {
			swal.close();
			$("#contenido_actas").empty();
			$("#contenido_actas").append(`<div class="col-md-12">
				<img src="${Traer_Server()}/imagenes/final.png" alt="..." style='width:30%;'> 
				<h4><b>ENCUESTA FINALIZADA</b></h4>
				</br>
				<a href="${Traer_Server()}index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
			</div>`);
			notificar_th(persona_id);
		}else MensajeConClase(mensaje, tipo, titulo);
	});
}

const notificar_th = async (persona_id) =>{
	let { identificacion, nombre_completo } = await obtener_info_persona(persona_id);
	let correos = await get_personas_notificar_th(249);
	let titulo = 'Talento CUC';
	let ser = `<a href="${Traer_Server()}index.php/talento_cuc/${persona_id}"><b>agil.cuc.edu.co</b></a>`;
	msj = `Le informamos que fue finalizado el proceso de entrenamiento del funcionario <strong>${nombre_completo}</strong>.
	<br>
	<p>Ver <a href="${Traer_Server()}archivos_adjuntos/talento_cuc/entrenamiento/Entrenamiento_${identificacion}.pdf"><b>Plan de Entrenamiento.</b></a></p>
	Para mas información ingrese Aquí.
	<br><br>
	${ser}`; 
	enviar_correo_personalizado("Tal", msj, correos[0], 'Funcionario', "AGIL Talento CUC", titulo, "ParCodAdm", 3);
}

const guardar_oferta_entrenamiento = () => {
	let data = new FormData(document.getElementById("form_oferta_entrenamiento"));
	enviar_formulario(`${ruta}guardar_oferta_entrenamiento`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#form_oferta_entrenamiento").get(0).reset();
			listar_ofertas_entrenamiento();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_oferta_entrenamiento= (id) => {
	let data = new FormData(document.getElementById("form_oferta_entrenamiento"));
	data.append('id', id);
	enviar_formulario(`${ruta}modificar_oferta_entrenamiento`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#form_oferta_entrenamiento").get(0).reset();
			listar_ofertas_entrenamiento();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const enviar_acta_aceptacion_cargo = () => {
	let data = new FormData(document.getElementById("form_acta_cargo"));
	data.append('id_persona', info_solicitud.identificacion);
	enviar_formulario(`${ruta}enviar_acta_aceptacion_cargo`, data, (resp) => {
		let { tipo, mensaje, titulo, codigo_cargo } = resp;
		if (tipo == 'success') {
			enviar_correo_acta(info_solicitud, codigo_cargo);
			$("#modal_enviar_acta_cargo").modal('hide');
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const enviar_correo_acta = (info, codigo) =>{
	let { id, correo, nombre_completo} = info;
	let ser = `<a href="${Traer_Server()}index.php/talento_cuc/acta_cargo/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 1;
	let titulo = 'Talento CUC';
	let fecha = new Date();
	fecha.setMonth(fecha.getMonth() + 1); // Añades los meses
	var date=new Date(fecha);
	var fecha_final=date.toLocaleDateString('en-GB');
	let mensaje = `Estimado <strong>${nombre_completo}</strong>, 
	Como parte del proceso de inducción al cargo, te enviamos Manual de Cargo y el acta para la aceptación de este, el cual deberás diligenciar de la mano con tu jefe inmediato, 
	como constancia de tu entrenamiento en el cargo. 
	La fecha límite para devolver el formato firmado es <strong>${fecha_final}</strong>.
	<br>
	Código del cargo: <strong>${codigo}</strong>, 
	<a href="https://app.powerbi.com/view?r=eyJrIjoiYjY4OGI5M2QtNGVmZC00NTEzLWFjMzgtZTRiMmYxOWY5ZWE3IiwidCI6IjA1MDdlNWNlLTBmOTUtNDlhYS1hYmRlLWM5MGRjZGVkYmQxMiIsImMiOjR9&pageName=ReportSection" target="blan_">Haga clic aquí para consultar las Funciones por Cargo!.</a> 
	<br><br>
	Para confirmar el acta, haga click en el siguiente enlace:
	<br>
	${ser}`;
	enviar_correo_personalizado("Tal", mensaje, correo, 'Funcionario', "AGIL Talento CUC", titulo, "ParCodAdm", tipo);
}

const notificar_aceptacion_acta = async (info) => {
	let { id_persona, identificacion, nombre_completo, solicitar_firma_jefe, codigo_cargo, nombre_jefe, correo_jefe, id_jefe } = info;
	let ser = `<a href="${Traer_Server()}index.php/talento_cuc/validar_actas_entrenamiento/${id_jefe}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 1;
	let titulo = 'Talento CUC';
	let acta = `<p>Ver <a href="${Traer_Server()}archivos_adjuntos/talento_cuc/entrenamiento/${codigo_cargo}_${identificacion}.pdf"><b>Acta de aceptación de cargo</b></a></p>`;
	let mensaje = `Estimado, 
	Se le informa que la persona <strong>${nombre_completo}</strong>, firmó su acta de aceptación de Cargo <strong>${codigo_cargo}</strong>.
	${acta}
	${solicitar_firma_jefe == 0 ? 'Para más información, haga click en el siguiente enlace: <br>'+ser
	: 'Para registrar su firma como jefe inmediato, haga click en el siguiente enlace: <br>'+ser}`

	enviar_correo_personalizado("Tal", mensaje, correo_jefe, nombre_jefe, "AGIL Talento CUC", titulo, "ParCodAdm", tipo);
	if(solicitar_firma_jefe == 0) notificar_th_acta(id_persona);
}

const generar_pdf_acta_cargo = (persona_id) => {
	console.log("generando");
	const route = `${Traer_Server()}index.php/talento_cuc/exportar_acta_cargo/${persona_id}`;
	window.open(route, '_blank');
	window.focus()
	console.log("generado");
	return true;
}

const guardar_aceptacion_cargo = () =>{
	let image = document.getElementById("canvas").toDataURL();
	let data = new FormData(document.getElementById("form_confirmar_acta"));
	data.append('firma_fun', image);
	enviar_formulario(`${ruta}guardar_aceptacion_cargo`, data, (resp) => {
		let { tipo, mensaje, titulo, info, persona_id} = resp;
		if (tipo == 'success') {
			generar_pdf_acta_cargo(persona_id);
			notificar_aceptacion_acta(info);
			$("#modal_solicitar_firma").modal('hide');
			$(`#contenido_actas`).empty();
			$("#contenido_actas").append(`<div class="col-md-12 text-center">
				<img src="${Traer_Server()}/imagenes/final.png" alt="..." style='width:30%;'> 
				<h4><b>ACTA CONFIRMADA</b></h4>
				</br><a href="${Traer_Server()}index.php" class="btn btn-danger btn-lg btn_agil" style="background-color: #d57e1c!important;">Regresar a Agil</a>               
				</div>`);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const buscar_dependencia = (buscar = '', departamento, callback) => {
	consulta_ajax(`${ruta}buscar_dependencia`, { buscar, departamento }, (data) => {
		let num = 0;
		$(`#tabla_dependencia tbody`)
			.off('click', 'tr')
			.off('click', 'tr span.asignar')
			.off('click', 'tr span.agregar')
			.off('dblclick', 'tr');
		const myTable = $('#tabla_dependencia').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ render: () => ++num },
				{ data: 'nombre' },
				{
					defaultContent:
						"<span class='btn btn-default asignar'><span class='fa fa-check' style='color:#5cb85c'></span></span>"
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_dependencia tbody').on('click', 'tr', function() {
			$('#tabla_dependencia tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_dependencia tbody').on('dblclick', 'tr', function() {
			const data = myTable.row($(this).parent().parent()).data();
			callback(data);
		});

		$('#tabla_dependencia tbody').on('click', 'tr span.asignar', function() {
			const data = myTable.row($(this).parent().parent()).data();
			callback(data);
		});

		$('#tabla_dependencia tbody').on('click', 'tr span.agregar', function() {
			const data = myTable.row($(this).parent().parent()).data();
			agregar_dependencia(data);
		});
	});
};

const listar_ofertas_entrenamiento = () => {
	consulta_ajax(`${ruta}listar_ofertas_entrenamiento`, {}, (data) => {
		$(`#tabla_ofertaEntrenamiento tbody`)
			.off('click', 'tr')
			.off('dblclick', 'tr')
			.off('click', 'tr td .modificar')
			.off('click', 'tr td .eliminar')
			.off('click', 'tr td:nth-of-type(1)');
		const myTable = $('#tabla_ofertaEntrenamiento').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
				{ data: 'tema' },
				{ data: 'vice' },
				{ data: 'departamento' },
				{ data: 'area' },
				{ data: 'gestion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_ofertaEntrenamiento tbody').on('click', 'tr', function () {
			$('#tabla_ofertaEntrenamiento tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_ofertaEntrenamiento tbody').on('dblclick', 'tr', function () {
			$('#tabla_ofertaEntrenamiento tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_ofertaEntrenamiento tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let { id, tema  } = myTable.row($(this).parent()).data();
		});

		$(`#tabla_ofertaEntrenamiento tbody`).on("click", "tr td .modificar", function () {
			let { id, valorx, valory, valorz, tema, vice, departamento, area } = myTable.row($(this).parent()).data();
			$("#nombre_modal").html('Modificar Oferta de Entrenamiento');
			$("#form_oferta_entrenamiento input[name='tema']").val(tema);
			$("#form_oferta_entrenamiento input[name='text_depto_adscrito']").val(vice);
			$("#form_oferta_entrenamiento input[name='dept_adscrito']").val(valorx);
			$("#form_oferta_entrenamiento input[name='text_departamento']").val(departamento);
			$("#form_oferta_entrenamiento input[name='departamento']").val(valory);
			$("#form_oferta_entrenamiento input[name='text_area_especifica']").val(area);
			$("#form_oferta_entrenamiento input[name='area_especifica']").val(valorz);
			callbak_ = (resp) => modificar_oferta_entrenamiento(id);
			$("#modal_nuevo_oferta_entrenamiento").modal();
		});

		$(`#tabla_ofertaEntrenamiento tbody`).on("click", "tr td .eliminar", function () {
			let { id } = myTable.row($(this).parent()).data();
			eliminar_datos({ id, title: "Eliminar datos?", tabla_bd: 'valor_parametro' }, () => {
				listar_ofertas_entrenamiento();
			});
		});
	});
}

const buscar_oferta = (dato, callbak) => {
	consulta_ajax(`${ruta}buscar_oferta`, { dato }, resp => {
		$(`#tabla_ofertas tbody`).off('click', 'tr td .seleccionar').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
		const myTable = $("#tabla_ofertas").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{ data: 'nombre' },
				{ data: 'vicerrectoria' },
				{ data: "departamento" },
				{ data: 'area' },
				{
					defaultContent: '<span style="color: #39B23B;" title="Seleccionar Persona" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>'
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_ofertas tbody').on('click', 'tr', function () {
			$("#tabla_ofertas tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_ofertas tbody').on('dblclick', 'tr', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});
		$('#tabla_ofertas tbody').on('click', 'tr td .seleccionar', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});
	});
}

const bar_estado_actas = (progress) => {
	$('#bar_estado').css('width', progress + '%');
	$(".text_barra").html(progress+'%');
}

const notificar_th_acta = async (persona_id) =>{
	let { identificacion, nombre_completo, codigo_cargo } = await obtener_info_persona(persona_id);
	let correos = await get_personas_notificar_th(249);
	let titulo = 'Talento CUC';
	let ser = `<a href="${Traer_Server()}index.php/talento_cuc/${persona_id}"><b>agil.cuc.edu.co</b></a>`;
	msj = `Se le informa que finalizó el proceso de aceptación de Cargo <strong>${codigo_cargo}</strong> del funcionario <strong>${nombre_completo}</strong>.
	<p>Ver <a href="${Traer_Server()}archivos_adjuntos/talento_cuc/entrenamiento/Entrenamiento_${identificacion}.pdf"><b>Plan de Entrenamiento.</b></a></p>
	<p>Ver <a href="${Traer_Server()}archivos_adjuntos/talento_cuc/entrenamiento/${codigo_cargo}_${identificacion}.pdf"><b>Acta de Aceptación de Cargo</b></a></p>
	Para mas información ingrese Aquí.
	<br><br>
	${ser}`; 
	enviar_correo_personalizado("Tal", msj, correos[0], 'Funcionario', "AGIL Talento CUC", titulo, "ParCodAdm", 3);
}

const guardar_confirmacion_jefe = (id, persona_id, id_funcionario_jefe) =>{
	let image = document.getElementById("canvas").toDataURL();
	consulta_ajax(`${ruta}guardar_confirmacion_jefe`, {firma_jefe: image, id_acta: id}, (resp) => {
		let { tipo, mensaje, titulo, progress } = resp;
		if (tipo == 'success') {
			$("#modal_solicitar_firma").modal('hide');
			bar_estado_actas(progress);
			generar_pdf_acta_cargo(persona_id);
			listar_actas_personas(id_funcionario_jefe);
			notificar_th_acta(persona_id);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const listar_actas_personas = (id_funcionario_jefe) => {
	consulta_ajax(`${ruta}listar_actas_personas`, { id_funcionario_jefe }, (data) => {
		$(`#tabla_actas_personas tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)').off("click", "tr td .seleccionar");
		const myTable = $('#tabla_actas_personas').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			paging: false,
			info: false,
			data,
			columns: [
				{ data: 'ver' },
				{ data: 'nombre_completo' },
				{ data: 'identificacion' },
				{ data: 'codigo_cargo' },
				{ data: 'gestion' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$('#tabla_actas_personas tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let { id_evaluado, codigo_cargo} = myTable.row($(this).parent()).data();
			window.open(`${Traer_Server()}/archivos_adjuntos/talento_cuc/entrenamiento/${codigo_cargo}_${id_evaluado}.pdf`);
		});

		$('#tabla_actas_personas tbody').on('click', 'tr', function () {
			$('#tabla_actas_personas tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_actas_personas tbody').on('dblclick', 'tr', function () {
			$('#tabla_actas_personas tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_actas_personas tbody').on("click", "tr td .seleccionar", function () {
			let { id, persona_id} = myTable.row($(this).parent()).data();
			callbak_activo = (resp) => guardar_confirmacion_jefe(id, persona_id, id_funcionario_jefe);
			pedir_firma('¿ Confirmar acta ?', 'Desea confirmar el acta de aceptación de cargo del funcionario?. Tener en cuenta que no podra revertir esta acción.');
		});
	});
}

const listar_asignacion_indicadores = (id_evaluado) => {
	consulta_ajax(`${ruta}listar_asignacion_indicadores`, { id_evaluado }, (data) => {
		$(`#tabla_asignacion_indicadores tbody`).off('click', 'tr').off('click', 'tr span.quitar').off('click', 'tr span.editar').off('dblclick', 'tr');
		const myTable = $('#tabla_asignacion_indicadores').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ data: 'pregunta' },
				{ data: 'tipo_meta' },
				{ data: 'meta' },
				{ data: 'periodo' },
				{
					defaultContent: '<span style="color: #d9534f;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash btn btn-default quitar" ></span> <span style="color: #337ab7;" title="Modificar Indicador" data-toggle="popover" data-trigger="hover" class="fa fa-edit btn btn-default editar" ></span>'
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_asignacion_indicadores tbody').on('click', 'tr', function () {
			$('#tabla_asignacion_indicadores tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_asignacion_indicadores tbody').on('dblclick', 'tr', function () {
			$('#tabla_asignacion_indicadores tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_asignacion_indicadores tbody').on('click', 'tr span.quitar', function () {
			const { id_pregunta, evaluado } = myTable.row($(this).parent()).data();
			eliminar_datos({ id: id_pregunta, title: "Eliminar Dato?", tabla_bd: 'talentocuc_indicadores' }, () => {
				listar_asignacion_indicadores(evaluado);
			});
		});

		$('#tabla_asignacion_indicadores tbody').on('click', 'tr span.editar', function () {
			const { id_pregunta, pregunta, periodo, id_tipo_meta, meta, evaluado } = myTable.row($(this).parent()).data();
			$("#form_nueva_asignacion_indicador input[name='periodo_indicador']").val(periodo);
			// $("#form_nueva_asignacion_indicador select[name='tipo_pregunta_ind']").val(id_tipo_respuesta);
			$("#form_nueva_asignacion_indicador input[name='meta_indicador']").val(meta);
			$("#form_nueva_asignacion_indicador select[name='tipo_meta_ind']").val(id_tipo_meta);
			$("#form_nueva_asignacion_indicador textarea[name='descripcion_ind']").val(pregunta);
			callbak_activo = (resp) => modificar_asignacion_indicador(evaluado, id_pregunta);
			$("#modal_nueva_asignacion_indicador").modal();
		});
	});
};

const guardar_asignacion_indicador = (id_evaluado) => {
	let data = new FormData(document.getElementById("form_nueva_asignacion_indicador"));
	data.append('id_evaluado', id_evaluado);
	enviar_formulario(`${ruta}guardar_asignacion_indicador`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_nueva_asignacion_indicador").modal("hide");
			$("#form_nueva_asignacion_indicador").get(0).reset();
			listar_asignacion_indicadores(id_evaluado);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_asignacion_indicador = (id_evaluado, id_pregunta) => {
	let data = new FormData(document.getElementById("form_nueva_asignacion_indicador"));
	data.append('id_evaluado', id_evaluado);
	data.append('id_pregunta', id_pregunta);
	enviar_formulario(`${ruta}modificar_asignacion_indicador`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_nueva_asignacion_indicador").modal("hide");
			$("#form_nueva_asignacion_indicador").get(0).reset();
			listar_asignacion_indicadores(id_evaluado);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const listar_asignaciones = (persona_id, tabla_bd) => {
	consulta_ajax(`${ruta}listar_asignaciones`, { persona_id, tabla_bd }, (data) => {
		$(`#tabla_asignaciones tbody`).off('click', 'tr').off('click', 'tr span.quitar').off('click', 'tr span.editar').off('dblclick', 'tr');
		const myTable = $(`#tabla_asignaciones`).DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ data: 'pregunta' },
				{ data: 'periodo' },
				{
					defaultContent: '<span style="color: #d9534f;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-trash btn btn-default quitar" ></span> <span style="color: #337ab7;" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-edit btn btn-default editar" ></span>'
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$(`#tabla_asignaciones tbody`).on('click', 'tr', function () {
			$(`#tabla_asignacionestbody tr`).removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$(`#tabla_asignaciones tbody`).on('dblclick', 'tr', function () {
			$(`#tabla_asignaciones tbody tr`).removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$(`#tabla_asignaciones tbody`).on('click', 'tr span.quitar', function () {
			const { id } = myTable.row($(this).parent()).data();
			eliminar_datos({ id: id, title: "Eliminar Dato?", tabla_bd: tabla_bd }, () => {
				listar_asignaciones(persona_id, tabla_bd);
			});
		});

		$(`#tabla_asignaciones tbody`).on('click', 'tr span.editar', async function () {
			const { id, id_persona, pregunta, id_tipo_respuesta, periodo, respuesta } = myTable.row($(this).parent()).data();
			$("#form_nueva_asignacion input[name='periodo_fun']").val(periodo);
			$("#form_nueva_asignacion select[name='tipo_pregunta_fun']").val(id_tipo_respuesta);
			$("#form_nueva_asignacion textarea[name='descripcion_fun']").val(pregunta);
			if(tabla_bd == 'talentocuc_formacion_esencial'){
				let datos = await obtener_tipo_respuesta('Opc_Check');
				$(".formacion_escencial").html('');
				let res = '';
				datos.forEach(elemento => {
					res = elemento.id == respuesta ? 'checked' : '';
					$(".formacion_escencial").append(`
						<div class="funkyradio-${elemento.valorz}" style="display: inline-block;width:48%;">
							<input type="radio" id="form_${elemento.valor}" name="formacion_es" value="${elemento.id}" res>
							<label for="form_${elemento.valor}"> ${elemento.valor}</label>
						</div>`);
				});
				$(`#form_nueva_asignacion input[name=formacion_es][value='${respuesta}']`).prop("checked",true);
			}
			callbak_ = (resp) => modificar_asignacion(id, id_persona, tabla_bd);
			$("#modal_nueva_asignacion").modal();
		});
	});
};

const guardar_asignacion = (persona_id, tabla_bd) => {
	let data = new FormData(document.getElementById("form_nueva_asignacion"));
	data.append('id_persona', persona_id);
	data.append('tabla_bd', tabla_bd);
	enviar_formulario(`${ruta}guardar_asignacion`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_nueva_asignacion").modal("hide");
			$("#form_nueva_asignacion").get(0).reset();
			listar_asignaciones(persona_id, tabla_bd);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_asignacion = (id_asignacion, id_persona, tabla_bd) => {
	let data = new FormData(document.getElementById("form_nueva_asignacion"));
	data.append('id_asignacion', id_asignacion);
	data.append('tabla_bd', tabla_bd);
	enviar_formulario(`${ruta}modificar_asignacion`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$("#modal_nueva_asignacion").modal("hide");
			$("#form_nueva_asignacion").get(0).reset();
			listar_asignaciones(id_persona, tabla_bd);
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const filtrar_asistencias_formacion = () =>{
	let fecha_i = $("#form_filtro_formacion input[name='fecha_i']").val();
	let fecha_f = $("#form_filtro_formacion input[name='fecha_f']").val();
	let text = $("#form_filtro_formacion input[name='text_filtro']").val();
	let id_lugar = $("#form_filtro_formacion select[name='filtro_id_lugar']").val();
	filtros = {
		'fecha_i': fecha_i,
		'fecha_f': fecha_f,
		'id_lugar': id_lugar,
		'texto': text
	}
	ver_asistencias_formacion('',filtros);
}

const ver_asistencias_formacion = (data='', filtros = {}) => {
	let { fecha_i, fecha_f, texto, id_lugar } = filtros
	consulta_ajax(`${ruta}listar_asistencias_formacion`, {data, fecha_i, fecha_f, texto, id_lugar}, (data) => {
		$(`#tabla_asistencias_formacion tbody`).off('click', 'tr').off('dblclick', 'tr');
		const myTable = $(`#tabla_asistencias_formacion`).DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
				{ data: 'nombre_completo' },
				{ data: 'tema' },
				{ data: 'funcionario' },
				{ data: 'fecha' }
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones(),
		});

		$(`#tabla_asistencias_formacion tbody`).on('click', 'tr', function () {
			$(`#tabla_asistencias_formacion tr`).removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$(`#tabla_asistencias_formacion tbody`).on('dblclick', 'tr', function () {
			$(`#tabla_asistencias_formacion tbody tr`).removeClass('warning');
			$(this).attr('class', 'warning');
		});
	});

	$("#modal_asistencias").modal();
}

const buscar_cargo = (dato, callbak) => {
	consulta_ajax(`${ruta}buscar_cargo`, {dato: dato }, resp => {
		$(`#tabla_cargo_busqueda tbody`).off('click', 'tr td .seleccionar').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
		const myTable = $("#tabla_cargo_busqueda").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					defaultContent: `<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>`
				},
				{ data: "valor" },
				{ data: 'valorx' },
				{
					defaultContent: '<span style="color: #39B23B;" title="Seleccionar Cargo" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>'
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_cargo_busqueda tbody').on('click', 'tr', function () {
			$("#tabla_cargo_busqueda tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_cargo_busqueda tbody').on('dblclick', 'tr', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});
		$('#tabla_cargo_busqueda tbody').on('click', 'tr td .seleccionar', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});
	});
}
const listar_actas_cargo = (idpersona) => {
	let num = 0;
	consulta_ajax(`${ruta}listar_actas_cargo`, { idpersona }, resp => {
		$(`#tabla_actas_cargo tbody`).off('click', 'tr td .seleccionar').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
		const myTable = $("#tabla_actas_cargo").DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{ render: () => ++num },
				{ data: "cargo" },
				{ data: 'fecha_entrega' },
				{ data: 'fecha_recibido' },
				{
					defaultContent: '<a target="_blank" class="seleccionar" title="Descargar acta"><span style="color: #5cb85c" class="fa fa-download btn btn-default"></span></a>'
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_actas_cargo tbody').on('click', 'tr', function () {
			$("#tabla_actas_cargo tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_actas_cargo tbody').on('dblclick', 'tr', function () {
			let data = myTable.row($(this).parent().parent()).data();
			$(".seleccionar").attr("href", `${Traer_Server()}archivos_adjuntos/talento_cuc/entrenamiento/${data.codigo_cargo}_${data.id_evaluado}.pdf`);
		});
		$('#tabla_actas_cargo tbody').on('click', 'tr td .seleccionar', function () {
			let data = myTable.row($(this).parent().parent()).data();
			$(".seleccionar").attr("href", `${Traer_Server()}archivos_adjuntos/talento_cuc/entrenamiento/${data.codigo_cargo}_${data.id_evaluado}.pdf`);
		});
	});
}

const listar_detalle_indicadores = (idpersona,tipo) => {
	$("#tabla_detalles thead").html('');
	let num = 0;
	if(tipo == 1){
		$("#tabla_detalles thead").append(`<tr>
			<th>No.</th>
			<th>DESCRIPCIÓN</th>
			<th>META</th>
			<th>RESULTADO</th>
			<th>CUMPLIMIENTO</th>
	  	</tr>`);
		  let res = 0;
		  let cump = 0;
	  	consulta_ajax(`${ruta}listar_detalle_indicadores`, { idpersona,tipo }, data => {
			$(`#tabla_detalles tbody`).html("").off('dblclick', 'tr').off('click', 'tr');
			data.map(({ pregunta, meta, resultado, cumplimiento }) => {
				res = !resultado ? 0 : resultado;
				cump = !cumplimiento ? 0 : cumplimiento;
				$("#tabla_detalles tbody").append(`<tr><td>${num++}</td><td>${pregunta}</td><td>${meta}</td><td>${res}</td><td>${cump}</td></tr>`);
			});
		});
	}else{
		$("#tabla_detalles thead").append(`<tr>
			<th>No.</th>
			<th>DESCRIPCIÓN</th>
			<th>RESULTADO</th>
	  	</tr>`);
		consulta_ajax(`${ruta}listar_detalle_indicadores`, { idpersona,tipo }, data => {
			$(`#tabla_detalles tbody`).html("").off('dblclick', 'tr').off('click', 'tr');
			data.map(({ pregunta, valor }) => {
				$("#tabla_detalles tbody").append(`<tr><td>${num++}</td><td>${pregunta}</td><td>${valor}</td></tr>`);
			});
		});
	}
	
	
}

const descargar_certificado_filtro = () =>{
	console.log('x');
	window.open(`${Traer_Server()}index.php/talento_cuc/certificado/${id_persona}/${id_evaluacion_cerfificado}`);
}

const listar_plan_formacion = (id_evaluacion = null) =>{
	id_evaluacion_cerfificado = id_evaluacion;
	consulta_ajax(`${ruta}listar_plan_formacion`, { identificacion_persona, id_evaluacion }, data => {
		let{ plan_formacion, descarga, administra } = data;
		$(`#conten_formacion`).html("");
		let html = '';
		let mesj = !plan_formacion ? 'En este momento estamos generando su plan de formación. ' : '';
		let sw = !descarga ? "disabled" : "";
		if(administra){
			html =`<h6 class="card-title">Certificado Institucional</h6>              
			<p class="card-text">${mesj} Al finalizar usted podra generar un certificado en el cual se evidenciara su
				participación en el plan de formación.</p>
			<button class="btn btn-primary btn-block" onclick="descargar_certificado_filtro()" ${sw}> Descargar</button>`;
		}else{ 
			html =`<h6 class="card-title">Certificado Institucional</h6>
				<p class="card-text">En estos momentos me encuentro trabajando en mi plan de formación para obtener mi
					certificado institucional, te invito ha iniciar con el tuyo.</p>
				<button class="btn btn-primary btn-block" onclick="window.open('${Traer_Server()}index.php')">Mi plan</button>`;
		 } 
		$(`#conten_formacion`).append(`
			<div class="col-md-6 col-lg-4 mb-5">
			<div class="card">
				<img class="img-fluid" src="${Traer_Server()}imagenes/formacion.png" alt="" />
				<div class="card-body" style="height:300px">
				${html}
				</div>
			</div>
			</div>
		`);

		if(administra){
			let tiempo = '';
			plan_formacion.forEach(elemento => {
				tiempo =  elemento.tiempo ? elemento.tiempo : 0;
				$(`#conten_formacion`).append(`
					<div class="col-md-6 col-lg-4 mb-5">
						<div class="card">
							<img class="img-fluid" src="${Traer_Server()}imagenes/${elemento.icono}" alt="" />
							<div class="card-body" style="height:300px">
							<h6 class="card-title">${elemento.competencia}</h6>
							<p class="card-text">Usted debe cumplir un total de <b>${elemento.hora_formacion}</b> horas para
								aprobar
								esta competencia, hasta el momento usted lleva un total de
								<b>${tiempo}</b>
								horas.
							</p>
							<div class="dropdown" style='width : 100%'>
								<button class="btn btn-secondary dropdown-toggle btn-block" type="button" id="dropdownMenuButton"
								data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Acciones</button>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
								<span class="dropdown-item" onclick="listar_soportes_hv(${elemento.id_competencia},${elemento.id_persona})"><span
									class='fa fa-list'></span> Ver Soportes</span>
								<span class="dropdown-item" onclick="guardar_soportes_hv(${elemento.id_competencia},${elemento.id_persona})"><span
									class='fa fa-upload'></span> Agregar Soporte</span>
								</div>
							</div>
							</div>
						</div>
					</div>
				`);
			}); 
		} 
		
	});

}