const ruta = `${Traer_Server()}index.php/talento_humano_control/`;
let listado_postulantes_nue = [];
let callbak_activo = (resp) => { };
let container_activo = '#txt_nombre_postulante';
let datos_postulante = null;
const ruta_hojas = 'archivos_adjuntos/talentohumano/hojas_vidas/';
const ruta_archivos_solicitudes = 'archivos_adjuntos/talentohumano/solicitudes/';
const ruta_archivos_certificados = 'archivos_adjuntos/talentohumano/certificados/';
const ruta_archivos_requisicion = 'archivos_adjuntos/talentohumano/requisicion/';
const ruta_archivos_volantes = 'archivos_adjuntos/talentohumano/volantes/';
const ruta_pruebas = 'archivos_adjuntos/talentohumano/documentos_seleccion/';
const ruta_ri = 'archivos_adjuntos/talentohumano/documentos_seleccion/ri/';
const ruta_archivos_arl = 'archivos_adjuntos/talentohumano/certificado_arl/';
const ruta_documentos_gestion = 'archivos_adjuntos/talentohumano/documentos_gestion/';
const ruta_documentos_ecargo= 'archivos_adjuntos/talentohumano/ecargo/';

let datos_vista = null;
let id_solicitud = null;
let id_vacante = null;
let id_postulante_sele = null;
let id_persona = null;
let id_comite = null;
let tipo_activo = null;
let programa_per = null;
let tipo_asigna_pro = null;
let actividad_selec = null;
let encargados_vb = [];
let descuentos = [];
let pen_sal = [];
let nombre_completo_cont = null;
let num_archivos = 0;
let myDropzone = 0;
let errores = [];
let materias = [];
let tipo_cargue = 1;
let tipo_proceso = null;
let num_archivos_cargados = 0;
let info_persona = {};
let info_candidato = {};
let estudios = [];
let info_solicitud = null;
let programas = [];
let mod_vacante = false;
let accion_tabla_dependencia = null;
let show_menu_principal = false;
let reemplazado_id = '';
let tipo_requisicion = null;
let codigo_sap = null;
let departamento_id = null;
let callbak_activo_aux = (resp) => { };
let tipo_persona = null;
let requisicion_id = null;
let id_persona_jefe = null;
let competencias = [];
let data_competencia = null;
let id_tipo_ausentismo = null;
let id_colaborador = null;
let id_jefe_cargo = null;
let id_jefe_cargo1 = null;
let activePlace = "";
let tablaActiva = '';
let visto_bueno= null;
let ecargo= []; 
let admin_ecargo=0;


$(document).ready(function () {
	$('#span_codigo_sap').click(() => {
		$('#modal_codigo_sap').modal();
		get_codigos_sap();
	});
	//cambiar eventos
	$("#modal_agregar_cargo li").click(function(){
		activePlace = $(this).attr("id");
		tablaActiva = $(this).attr('data-place');
		active_place("#modal_agregar_cargo", activePlace);
		if (tablaActiva == 'responsabilidades') {
		} else if (tablaActiva == 'accesos') {
		} else if (tablaActiva == 'informes') {
		} else if (tablaActiva == 'comites') {
		}else if (tablaActiva == 'logros') {
		}else if (tablaActiva == 'adjunto') {
		}
	});

	$('#buscar_colaborador_cargo').click(() => {
		callbak_activo = (data) => {
			const { id, nombre_completo } = data;
			id_colaborador = id;
			$("#form_ecargo input[name='colaborador']").val(nombre_completo);
			$('#form_buscar_persona').get(0).reset();
			$('#modal_buscar_postulante').modal('hide');
		}
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
	});

	$('#buscar_jefe_cargo').click(() => {
		callbak_activo = (data) => {
			const { id, nombre_completo } = data;
			id_jefe_cargo = id;
			$("#form_ecargo input[name='jefe']").val(nombre_completo);
			$('#modal_buscar_postulante').modal('hide');
		}
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
	});

	

	$('#buscar_jefe_cargo1').click(() => {
		callbak_activo = (data) => {
			const { id, nombre_completo } = data;
			id_jefe_cargo1 = id;
			$("#form_ecargo input[name='jefe1']").val(nombre_completo);
			$('#modal_buscar_postulante').modal('hide');
		}
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
	});


	$(function () {
		$('[data-toggle="popover"]').popover();
	});

	$('#btn_certificado_personalizado').click(() => $('#modal_tipos_certificado_laboral').modal());

	$('#form_req_posgrado').submit((e) => {
		e.preventDefault();
		const formData = new FormData(e.target);
		formData.append('reemplazado_id', reemplazado_id);
		if (mod_vacante) {
			modificar_requisicion_posgrado(formData);
		} else {
			formData.append('persona_id', info_candidato.id);
			guardar_requisicion_posgrado(formData);
		}
		reemplazado_id = null;
	});

	$('#btn_cir').click(() => {
		getAnios();
		$('#modal_cir').modal();
	});

	$('#form_nuevo_cir').submit((e) => {
		e.preventDefault();
		const formData = new FormData(e.target);
		guardar_solicitud_cert_ingresos(formData);
	});

	$('#btn_arl').click(() => {
		id_solicitud = null;
		$('#modal_arl').modal();
	});

	$('#btn_gestionP').click(() => {
		id_solicitud = null;
		$('#modal_gestionP').modal();
	});

	$('#btn_avisoEps').click(() => {
		id_solicitud = null;
		$('#modal_avisoEps').modal();
	});
	$('#btn_incavisoEps').click(() => {
		id_solicitud = null;
		$('#modal_avisoEpsInc').modal();
	});
	$('#btn_avisocaja').click(() => {
		id_solicitud = null;
		$('#modal_avisocaja').modal();
	});
	$('#btn_traslado').click(() => {
		id_solicitud = null;
		$('#modal_traslado').modal();
	});
	$('#mostrar_documentos').click(() => {
		documentos_gestion(id_solicitud);
		$('#modal_detalle_documentos').modal();
	});

	$('#mostrar_documentos_tras').click(() => {
		documentos_gestion(id_solicitud);
		$('#modal_detalle_documentos').modal();
	});
	$('#mostrar_documentos_inc').click(() => {
		documentos_gestion(id_solicitud);
		$('#modal_detalle_documentos').modal();
	});
	$('#mostrar_documentos_ecargo').click(() => {
		documentos_ecargo(id_solicitud);
		$('#modal_detalle_documentos').modal();
	});
	$('#imprimir_ecargo').click(() => {
		imprimir_ecargo()
	});

	$('#mostrar_convivencia').click(() => {
		$('#mostrar_convivencia').attr('href', `${Traer_Server()}${ruta_documentos_gestion}Instrutivo para el diligenciamiento.pdf`)

	});

	$('#mostrar_dconvivencia').click(() => {
		$('#mostrar_dconvivencia').attr('href', `${Traer_Server()}${ruta_documentos_gestion}Instrutivo para el diligenciamiento.pdf`)

	});

	$('#btn_ausentismos').click(() => {
		id_solicitud = null;
		$('#modal_tipo_ausentismo').modal();
	});
	$('#btn_vacaciones').click(() => {
		id_tipo_ausentismo = 'Hum_Vac';
		$('#form_ausentismo_vacaciones').get(0).reset();
		$("#modal_ausentismo_vacaciones").modal();
	});
	$('#btn_licencia').click(() => {
		id_tipo_ausentismo = 'Hum_Lic';
		$('#form_ausentismo_licencia').get(0).reset();
		$("#modal_ausentismo_licencia").modal();
	});


	$('#form_ausentismo_vacaciones').submit(() => {
		guardar_vacaciones();
		return false;
	});
	$('#form_ausentismo_licencia').submit(() => {
		guardar_licencia();
		return false;
	});
	$('#form_agregar_cargo').submit(() => {
		// guardar_agregar_cargo();
		modificar_agregar_cargo();
		return false;
	});


	$('#btn_afiliacion').click(() => {
		tipo_persona = 1;
		$('.titulo_modal_afil_arl').html('Nueva Afiliación ARL');
		$("#form_nuevo_arl textarea[name='motivo']").addClass('oculto');
		$('#form_nuevo_arl').get(0).reset();
		$('#txt_buscar_persona_arl').val('');
		callbak_activo = (data) => {
			let { id, nombre_completo, fecha_nacimiento, genero, eps } = data;
			id_persona = id;
			if (genero == 0) genero = '';
			$("#form_nuevo_arl input[name='candidato']").val(nombre_completo);
			$("#form_nuevo_arl input[name='fecha_nacimiento']").val(fecha_nacimiento);
			$("#form_nuevo_arl select[name='id_genero']").val(genero);
			$("#form_nuevo_arl input[name='eps']").val(eps);
			$('#modal_buscar_persona_arl').modal('hide');
		};
		$('#modal_nuevo_arl').modal();
	});

	$('#form_nuevo_arl').submit((e) => {
		if (id_solicitud) callbak_activo_aux();
		else crear_solicitud_arl('afiliacion_arl', 'form_nuevo_arl');
		return false;
	});

	$('#btn_cobertura').click(() => {
		tipo_persona = '';
		$('.titulo_modal_cob_arl').html('Nueva Cobertura ARL');
		$("#form_cobertura_arl textarea[name='motivo']").addClass('oculto');
		$('#form_cobertura_arl').get(0).reset();
		$('#txt_buscar_persona_arl').val('');
		callbak_activo = (data) => {
			let { id, nombre_completo, fecha_nacimiento, genero, eps } = data;
			id_persona = id;
			if (genero == 0) genero = '';
			$("#form_cobertura_arl input[name='candidato']").val(nombre_completo);
			$("#form_cobertura_arl input[name='fecha_nacimiento']").val(fecha_nacimiento);
			$("#form_cobertura_arl select[name='id_genero']").val(genero);
			$("#form_cobertura_arl input[name='eps']").val(eps);
			$('#modal_buscar_persona_arl').modal('hide');
		};
		$('#modal_cobertura_arl').modal();
	});

	$('#btn_cambio_eps').click(() => {
		$('#modal_cambio_eps .titulo_modal_cam_eps').html('Cambio Eps');
		$('#modal_cambio_eps').modal();
	});

	$('#form_cambio_eps').submit((e) => {
		guardar_cambio_eps();
		return false;
	});

	$('#btn_inc_ben').click(() => {
		$('.titulo_inc_ben').html('Inlcusion de Beneficiarios');
		$('#modal_inc_ben').modal();
	});
	$('#btn_tras').click(() => {

		$('.titulo_tras_afp').html('Notificación de traslados de EPS, AFP, AFC');
		$('#modal_tras_afp').modal();
	});

	$('#btn_inc_eps').click(() => {
		$('#modal_inc_eps .titulo_modal_inc_eps').html('Datos del Beneficiario');
		$('#modal_inc_eps').modal();
	});
	$('#btn_inc_caj').click(() => {
		$('#modal_inc_caj .titulo_modal_inc_caj').html('Datos del Beneficiario');
		$('#modal_inc_caj').modal();
	});

	$('#btn_Ecargo').click(() => {
		$('#modal_ecargo .titulo_modal_ecargo').html('Solicitud Entrega de cargo');
		$('#txt_buscar_persona_cargo').val('');
		$('#form_ecargo').get(0).reset();
		$('#modal_ecargo').modal();
	});

	$('#btn_acargo').click(() => {
		let motivo = $("#form_ecargo select[name='motivo']").val();
		ecargo.push(motivo);
		admin_ecargo=1;
		ecargo.push(admin_ecargo);
		callbak_activo_aux = (resp) => guardar_ecargo();
		$('#form_agregar_cargo').get(0).reset();
		$('#modal_agregar_cargo').modal();
	});

	$('#form_tras_afp').submit((e) => {
		guardar_traslado_afp();
		return false;
	});

	$('#form_inc_eps').submit((e) => {
		guardar_ben_eps();
		return false;
	});

	$('#form_inc_caj').submit((e) => {
		guardar_ben_caja();
		return false;
	});

	$('#btn_guardar_ecargo').click(()=> {
		callbak_activo_aux();
	});

	$('#form_ecargo').submit((e) => {
		let motivo = $("#form_ecargo select[name='motivo']").val();
		ecargo.push(motivo);
		admin_ecargo=0;
		ecargo.push(admin_ecargo);
		guardar_ecargo();
		return false;
	});

	$('#form_cobertura_arl').submit((e) => {
		if (id_solicitud) callbak_activo_aux();
		else crear_solicitud_arl('cobertura_arl', 'form_cobertura_arl');
		return false;
	});

	$('#form_seleccion select[name=tipo_cargo]').change(async (e) => {
		const lista_cargos = await listar_cargos(e.target.value === 'Vac_Aca' || e.target.value === 'Vac_Pos' ? 1 : 2);
		pintar_datos_combo(lista_cargos, "#form_seleccion select[name='cargo']", 'Seleccione Cargo');
	});

	$('#form_vb_arl').submit(async (e) => {
		e.preventDefault();
		const data = $('#tabla_solicitudes').DataTable().row('.warning').data();
		const { id, tipo_solicitud, correo, solicitante, id_tipo_solicitud } = data;
		const radioValue = $("#form_vb_arl input[name='vb_arl']:checked").val();
		let title = 'Aprobar solicitud';
		const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;

		if (radioValue == 0) {
			title = 'Rechazar solicitud';
			msj_confirmacion_input(
				title,
				'Por favor digite motivo de rechazo de la solicitud.',
				'Motivo de Rechazo',
				(msj) => {
					swal.close();
					const mensaje = `
						<p>Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha sido rechazada.</p>
						<p><strong>Motivo de rechazo:</strong> "${msj}"</p>
						<p>Más información en: ${ser}</p>`;
					const asunto = `Solicitud de ${tipo_solicitud} rechazada`;
					aprobar_solicitud_arl(e.target, data, mensaje, asunto);
				}
			);
		} else {
			const mensaje = `
					<p>Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha sido aprobada.</p>
					<p>Más información en: ${ser}</p>`;
			const asunto = `Solicitud de ${tipo_solicitud} aprobada `;

			if (id_tipo_solicitud === 'Hum_Cob_Arl') {
				const dato = await get_detalle_solicitud_arl(id, id_tipo_solicitud);
				if (dato.tipo_cobertura == 2) {
					data.notificar = true;
				} else data.notificar = false;
			}
			aprobar_solicitud_arl(e.target, data, mensaje, asunto);
		}
	});

	$('#form_entidades').submit(async (e) => {
		e.preventDefault();
		const data = $('#tabla_solicitudes').DataTable().row('.warning').data();
		const { id, tipo_solicitud } = data;
		const radioValue = $("#form_entidades input[name='vb_eps']:checked").val();
		let title = 'Aprobar solicitud';
		const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;

		if (radioValue == 0) {
			title = 'Rechazar solicitud';
			msj_confirmacion_input(
				title,
				'Por favor digite motivo de rechazo de la solicitud.',
				'Motivo de Rechazo',
				(msj) => {
					swal.close();
					const mensaje = `
						<p>Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha sido rechazada.</p>
						<p><strong>Motivo de rechazo:</strong> "${msj}"</p>
						<p>Más información en: ${ser}</p>`;
					const asunto = `Solicitud de ${tipo_solicitud} rechazada`;
					aprobar_solicitud_entidades(e.target, data, mensaje, asunto);
				}
			);
		} else {
			msj_confirmacion_input(
				title,
				'Por favor digite comentario de aprobacion de solicitud.',
				'Comentario de aprobacion',
				(msj) => {
					swal.close();
					const mensaje = `
						<p>Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha sido aprobada.</p>
						<p><strong>Comentario de Aprobación:</strong> "${msj}"</p>
						<p>Más información en: ${ser}</p>`;
					const asunto = `Solicitud de ${tipo_solicitud} aprobada `;
					aprobar_solicitud_entidades(e.target, data, mensaje, asunto);
				}
			);
		}

	});


	$('#btnestados_req_posgrado').click(() => {
		$('#modal_estados').modal();
		get_estados_solicitud(id_solicitud);
	});
	$('#btnestados_ausentismo_vacaciones').click(() => {
		$('#modal_estados').modal();
		get_estados_solicitud(id_solicitud);
	});
	$('#btnestados_ausentismo_licencia').click(() => {
		$('#modal_estados').modal();
		get_estados_solicitud(id_solicitud);
	});
	$('#btnestados_ecargo').click(() => {
		$('#modal_estados_ecargo').modal();
		get_estados_ecargo(id_solicitud);
	});

	$('#form_vb_pedagogico').submit((e) => {
		e.preventDefault();
		const data = $('#tabla_solicitudes').DataTable().row('.warning').data();
		const { id_tipo_solicitud, id_estado_solicitud, tipo_cargo } = data;
		// if (id_tipo_solicitud === 'Hum_Prec' && id_estado_solicitud === 'Tal_Esp' && tipo_cargo === 'Vac_Aca') {
		if (id_tipo_solicitud === 'Hum_Prec' && id_estado_solicitud === 'Tal_Esp') {
			vb_pedagogico(e.target);
		} else MensajeConClase('Esta acción no está permitida', 'info', 'Ooops!');
	});

	$('#buscar_director, #modal_administrar .nombre_director').click(() => {
		callbak_activo = (data) => cambiar_director_posgrado(data);
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
	});

	$('.input_buscar_persona').click(() => {
		if (tipo_persona == 1) $(".agregar_postulante_arl").hide();
		else $(".agregar_postulante_arl").show();
		buscar_persona_arl('', callbak_activo);
		$('#modal_buscar_persona_arl').modal();
	});
	$('.input_buscar_jefe').click(() => {
		callbak_activo = (data) => {
			let { id, nombre_completo } = data;
			id_persona = id;
			$("#form_ausentismo_vacaciones input[name='jefe_inmediato']").val(nombre_completo);
			$('#modal_buscar_persona_ausentismo').modal('hide');
		};
		buscar_persona_ausentismo('', callbak_activo);
		$('#modal_buscar_persona_ausentismo').modal();
	});
	$('.input_buscar_jefe_licencia').click(() => {
		callbak_activo = (data) => {
			let { id, nombre_completo } = data;
			id_persona = id;
			$("#form_ausentismo_licencia input[name='jefe_inmediato']").val(nombre_completo);
			$('#modal_buscar_persona_ausentismo').modal('hide');
		};
		buscar_persona_ausentismo('', callbak_activo);
		$('#modal_buscar_persona_ausentismo').modal();
	});

	$('#form_buscar_persona_arl').submit(() => {
		let dato = $('#txt_buscar_persona_arl').val();
		buscar_persona_arl(dato, callbak_activo);
		return false;
	});
	$('#form_buscar_persona_ausentismo').submit(() => {
		let dato = $('#txt_buscar_jefe').val();
		buscar_persona_ausentismo(dato, callbak_activo);
		return false;
	});

	$('#frm_terminar_requisicion_posgrado').submit(function (e) {
		e.preventDefault();
		const tipo_orden = $(`#frm_terminar_requisicion_posgrado select[name='tipo_orden']`).val();
		let tipOrden = $(`#frm_terminar_requisicion_posgrado select[name='tipo_orden']`).find('option:selected').text();
		let codigo_sap = $('#span_codigo_sap').html();
		$("#modal_detalle_vacante_posgrado .tipOrden").html(tipOrden);
		$("#modal_detalle_vacante_posgrado .codigoSap").html(codigo_sap);
		terminar_requisicion(tipo_orden);
	});

	$("#form_req_posgrado input[name='reemplazado'], #form_req_posgrado .txt_persona_reemplazar").click(() => {
		$('#txt_dato_buscar').val('');
		callbak_activo = (data) => {
			const { id, nombre_completo } = data;
			reemplazado_id = id;
			$("#form_req_posgrado input[name='reemplazado']").val(nombre_completo);
			$('#modal_buscar_postulante').modal('hide');
		};
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
	});

	$("#form_req_posgrado input[name='candidato'], #form_req_posgrado .txt_buscar_persona").click(() => {
		// container_activo = '#txt_nombre_postulante';
		$('#txt_dato_buscar').val('');
		callbak_activo = (data) => {
			const val = mostrar_detalle_persona_incompleto(data);
			info_candidato = data;
			if (val) {
				const { id, nombre_completo } = data;
				if (id !== reemplazado_id) {
					id_persona = id;
					$("#form_req_posgrado input[name='candidato']").val(nombre_completo);
					$('#modal_buscar_postulante').modal('hide');
				} else MensajeConClase('No se puede reemplazar por la misma persona', 'info', 'Ooops!');
			} else MensajeConClase('Por favor complete todos los campos', 'info', 'Ooops!');
			// $("#modal_buscar_postulante").modal()
		};
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
	});

	$("#form_req_posgrado select[name='tipo_vacante']").change((e) => {
		if (e.target.value === 'Vac_Ree') $('#div_reemplazado_req_pos').fadeIn('fast');
		else {
			$('#div_reemplazado_req_pos').fadeOut('fast');
			reemplazado_id = null;
			$("#form_req_posgrado input[name='candidato']").html('');
		}
	});

	$('#form_crear_opcion_certificado').submit((e) => {
		e.preventDefault();
		crear_nueva_opcion(e.target);
	});

	$('#btn_regresar').click(() => (show_menu_principal ? regresar() : show_menu(true)));
	$('#btn_regresar_adm').click(() => (regresar()));

	$('#btn_autogestion').click(() => $('#modal_autogestion').modal());

	$('#btn_certificado_laboral').click(() => {
		$('#modal_certificado_personalizado').modal();
		get_opciones_disponibles();
		limpiar_form_certificado();
	});

	$('#btn_req_administrativas').click(async () => {
		tipo_requisicion = 'Vac_Adm';
		const cargos = await listar_cargos(2);
		pintar_datos_combo(cargos, '.cbxcargos', 'Seleccione Cargo');
		config_modal_vacantes(tipo_requisicion);
	});

	$('#btn_req_aprendices').click(async () => {
		tipo_requisicion = 'Vac_Apr';
		const cargos = await listar_cargos(2);
		pintar_datos_combo(cargos, '.cbxcargos', 'Seleccione Cargo');
		config_modal_vacantes(tipo_requisicion);
	});

	$('#btn_req_pregrado').click(async () => {
		tipo_requisicion = 'Vac_Aca';
		id_solicitud = '';
		const cargos = await listar_cargos();
		pintar_datos_combo(cargos, '.cbxcargos', 'Seleccione Cargo');
		config_modal_vacantes(tipo_requisicion);
	});

	$('#btn_req_posgrado').click(async () => {
		tipo_requisicion = 'Vac_Pos';
		const cargos = await listar_cargos();
		pintar_datos_combo(cargos, '.cbxcargos', 'Seleccione Cargo');
		$('.icono_requisicion').removeClass('fa-wrench').addClass('fa-plus');
		$('.accion_requisicion').html('Crear');
		$('#campo_hoja_vida').show('fast');
		$('#modal_requisicion_posgrado').modal();
		$('#form_req_posgrado').get(0).reset();
	});

	$('#modal_tipos_requisicion .opcion__cont').click(async () => {
		mod_vacante = 0;
		const dptos = await get_departamentos(tipo_requisicion);
		pintar_datos_combo(dptos, '.cbxdependencias', 'Seleccione Departamento');
	});

	$('#nueva_seleccion').click(() => {
		$('#div_requisicion').show('fast');
		$('#form_seleccion input[name=requisicion]').attr('required', true);
		$('#modal_seleccion').modal();
		id_persona = null;
		mod_vacante = 0;
		requisicion_id = null;
		$('.texto_seleccion').html('Crear ');
		$('#form_seleccion').get(0).reset();
		$("#form_seleccion select[name='cargo']").html('<option value="">Seleccione Cargo</option>');
	});

	$('#btn_certificado_personalizado').click(() => $('#modal_tipos_certificado_laboral').modal());

	$('#form_certificado_personalizado').submit((e) => {
		e.preventDefault();
		const data = formDataToJson(new FormData(e.target));
		data.tipo_certificado = 0;
		data.opciones = [];
		const opciones_element = document.querySelectorAll('#div_opciones_disponibles span.fa-toggle-on');
		opciones_element.forEach((opc) => data.opciones.push(opc.parentElement.dataset.name));
		solicitar_certificado(data);
	});

	$('#btn_certificado_basico').click(() => solicitar_certificado({ tipo_certificado: 1 }));

	$("#form_certificado_personalizado input[name='tipo_certificado']").change((e) => {
		limpiar_form_certificado();
		e.target.value == 0
			? $('#form_certificado_personalizado').show('fast')
			: $('#form_certificado_personalizado').hide('fast');
	});

	$('#filtros').click(() => $('#Modal_filtro').modal());
	$('#btn_pruebas').click(() => $('#Modal_pruebas').modal());

	$('#btn_agregar_dependencia').click(() => {
		$('#modal_buscar_departamento').modal();
		$('#form_buscar_departamento').get(0).reset();
		accion_tabla_dependencia = 'req';
		buscar_dependencia();
	});

	$("#btn_buscar_reemplazado, #form_add_reemplazo input[name='nombre_reemplazado']").click(() => {
		container_activo = '#txt_nombre_postulante';
		$('#txt_dato_buscar').val('');
		callbak_activo = ({ id, nombre_completo }) => {
			id_persona = id;
			$("#form_add_reemplazo input[name='nombre_reemplazado']").val(nombre_completo);
			$('#modal_buscar_postulante').modal('hide');
		};
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
	});

	$('#form_seleccion input[name=requisicion], #btn_add_requisicion').click((e) => {
		cargar_requisiciones();
		$('#modal_requisicion').modal();
	});

	$('#form_buscar_requisicion').submit((e) => {
		e.preventDefault();
		cargar_requisiciones(e.target.value);
	});

	$('#btn_show_candidatos').click(() => {
		$('#botones_candidatos').html('');
		listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
		$('#modal_candidatos').modal();
	});

	$('#form_filtro').submit((e) => {
		e.preventDefault();
		listar_solicitudes();
	});

	$('#tipo_filtro').change((e) => get_estados_asignados_actividad(e.target.value));

	$('#btn_agregar_materias').click(() => agregar_materia());

	$('#txt_nombre_materia').keydown((event) => {
		if (event.which == 13 || event.keyCode == 13) {
			$('#btnagregar_materia').trigger('click');
			return false;
		}
		return true;
	});

	$('#btn_req_pregrado').click(() => {
		$('#botones_modificar').hide();
		$('#form_vacante').get(0).reset();
		$('.texto_accion').html('Agregar');
		$('.div_evaluacion, #div_apertura, #div_persona, #info_investigacion').html('');
		$('.adicional_info, .no-revisar').show('fast');
		materias = [];
		programas = [];
		listar_materias();
		cargar_programas();
		mod_vacante = 0;
		id_persona = null;
	});

	$('#btn_add_dpto').click(() => {
		$('#modal_buscar_departamento').modal();
		$('#form_buscar_departamento').get(0).reset();
		accion_tabla_dependencia = 'req';
		if (!id_solicitud) mod_vacante = '';
		buscar_dependencia();
	});

	$('#btnagregar_materia').click(() => {
		const materia = $('#txt_nombre_materia').val();
		if (materia) {
			if (!materias.includes(materia)) {
				materias.push({ materia });
				$('#txt_nombre_materia').val('');
				listar_materias();
				MensajeConClase(`${materia} agregada exitosamente.`, 'success', 'Materia Agregada');
			} else MensajeConClase('Esta materia ya fue agregada.', 'info', 'Ooops!!!');
		} else MensajeConClase('Por favor ingrese el nombre de la materia que desea agregar.', 'info', 'Ooops!!!');
	});

	$('#btnagregar_modulo').click(() => {
		const input = "#form_req_posgrado input[name='modulo']";
		const materia = $(input).val();
		if (materia) {
			const found = materias.find((mat) => mat.materia === materia);
			if (!found) {
				materias.push({ materia });
				$(input).val('');
				listar_modulos();
				MensajeConClase(`Módulo ${materia} exitosamente.`, 'success', 'Módulo Agregado');
			} else MensajeConClase('Este módulo ya fue agregado.', 'info', 'Ooops!!!');
		} else MensajeConClase('Por favor ingrese el nombre del módulo que desea agregar.', 'info', 'Ooops!!!');
	});

	$('#btn_buscar_persona').click(() => $('#frm_buscar_persona').trigger('submit'));

	$('#retirar_modulo').click(() => {
		const input = "#form_req_posgrado input[name='modulo']";
		const select = "#form_req_posgrado select[name='modulos_asignados']";
		const subject = $(`${select} option:selected`).text();
		const val = $(select).val();
		if (val) {
			const materia = materias.find((element) => element.materia === subject);
			const subjectIndex = materias.indexOf(materia);
			materias.splice(subjectIndex, 1);
			MensajeConClase(`El módulo ${subject} ha sido eliminado exitosamente.`, 'success', 'Proceso Exitoso!');
			listar_modulos();
		} else MensajeConClase(`Por favor seleccione el módulo que desea retirar.`, 'info', 'Ooops!');
	});

	$('#retirar_materia').click(() => {
		const subject = $('#materias_asignadas option:selected').text();
		const val = $('#materias_asignadas').val();
		if (val) {
			const materia = materias.find((element) => element.materia === subject);
			const subjectIndex = materias.indexOf(materia);
			materias.splice(subjectIndex, 1);
			MensajeConClase(`La materia ${subject} ha sido eliminada exitosamente.`, 'success', 'Proceso Exitoso!');
			listar_materias();
		} else MensajeConClase(`Por favor seleccione la materia que desea retirar.`, 'info', 'Ooops!');
	});

	$("#form_seleccion input[name='dependencia'], #btn_add_dependencia").click(() => {
		accion_tabla_dependencia = 'sel';
		$('#modal_buscar_departamento').modal();
		$('#form_buscar_departamento').get(0).reset();
		// mod_vacante = '';
		buscar_dependencia();
	});

	$("#form_citacion select[name='lugar']").change((e) =>
		cargar_ubicaciones(e.target.value, '#form_citacion select[name="ubicacion"]')
	);
	$("#form_citacion_entrevista_jefe select[name='lugar']").change((e) =>
		cargar_ubicaciones(e.target.value, '#form_citacion_entrevista_jefe select[name="ubicacion"]')
	);

	$('#form_citacion').submit((e) => {
		e.preventDefault();
		msj_confirmacion('¿ Citar a Entrevista ?', '', () => citar_entrevista(info_candidato.id, e.target));
	});

	$('#form_citacion_entrevista_jefe').submit((e) => {
		e.preventDefault();
		msj_confirmacion('¿ Citar a Entrevista con el Jefe Responsable ?', '', () => citar_entrevista_jefe(e.target));
	});

	$('#form_examen_medico').submit((e) => {
		e.preventDefault();
		const radioValue = $("#form_examen_medico input[name='ayuno']:checked").val();
		radioValue
			? msj_confirmacion('¿ Citar a Exámenes Médicos ?', 'El candidato ha sido citado exitosamente!', () =>
				citar_examenes(info_candidato.id, e.target)
			)
			: MensajeConClase('Por favor indique si el candidato debe ir a la cita en ayunas', 'info', 'Ooop!');
	});

	$('#form_enviar_pruebas').submit((e) => {
		enviar_pruebas();
		e.preventDefault();
	});

	$('#form_tipo_csep').submit((e) => {
		e.preventDefault();
		const { csep } = formDataToJson(new FormData(e.target));
		enviar_candidato_csep(info_candidato.id, id_solicitud, csep);
	});

	$('#form_enviar_invitacion').submit((e) => {
		e.preventDefault();
		msj_confirmacion('¿ Enviar Invitación a Inducción ?', '', async () => {
			const permiso = await validar_permiso_invitacion();
			if (permiso) {
				swal.close();
				enviar_invitacion_induccion();
			} else MensajeConClase('No tiene permisos para realizar este proceso', 'info', 'Ooops!');
		});
	});

	$('#form_enviar_invitacion input[name=fecha]').keyup((e) => $('#invitacion_fecha').html(e.target.value));
	$('#form_enviar_invitacion input[name=ubicacion]').keyup((e) => $('#invitacion_ubicacion').html(e.target.value));
	$('#form_enviar_invitacion input[name=jornada]').keyup((e) => $('#invitacion_jornada').html(e.target.value));

	$('#form_fecha_ingreso').submit((e) => {
		e.preventDefault();
		guardar_fecha_ingreso(e.target);
	});

	$('#form_candidato_csep').submit((e) => {
		e.preventDefault();
		const data = new FormData(e.target);
		msj_confirmacion('¿ Enviar a CSEP Virtual ?', '', () => {
			asignar_csep(data);
			swal.close();
			document.getElementById('Sel_Cse').classList.add('disabled');
		});
	});

	$('#form_generar_informe').submit((e) => {
		const data = new FormData(e.target);
		generar_informe(data);
		e.preventDefault();
	});

	$("#form_contratacion select[name='tipo_contrato']").change((e) => {
		e.target.value != 'Cont_Ind'
			? $("#form_contratacion input[name='duracion_contrato']").attr('required', true).show()
			: $("#form_contratacion input[name='duracion_contrato']").attr('required', false).hide();
	});

	$('#form_contratacion').submit((e) => {
		e.preventDefault();
		const data = formDataToJson(new FormData(e.target));
		data.candidato = info_candidato.id;
		// data.reemplazado = id_persona;
		data.solicitud = id_solicitud;
		data.tipo = $("#form_contratacion select[name='tipo_contrato'] :selected").text();
		data.nextProcess = 'Sel_Con';
		data.success = 'Candidato Contratado Exitosamente!';
		data.callback = () => {
			$('#modal_contratacion').modal('hide');
			document.getElementById('Sel_Con').classList.add('disabled');
			e.target.reset();
			swal.close();
		};
		msj_confirmacion('¿ Contratar Candidato ?', 'El candidato será enviado a contratación!', () =>
			gestionar_candidato(data)
		);
	});

	$('#form_formacion').submit((e) => {
		const data = formDataToJson(new FormData(e.target));
		estudios.push(data);
		e.target.reset();
		cargar_estudios();
		e.preventDefault();
	});

	$('#form_aval_seguridad').submit((e) => {
		e.preventDefault();
		const radioValue = $("#form_aval_seguridad input[name='vb_seguridad']:checked").val();
		if (radioValue == 1 || radioValue == 2) {
			const msj =
				radioValue == 1
					? 'Se cargará el aval de seguridad!'
					: 'Se cargará el aval de seguridad y el candidato será descartado de este proceso de selección';
			!$('#file_aval_seguridad').val()
				? MensajeConClase('Por favor adjunte el aval de seguridad', 'info', 'Ooops!')
				: msj_confirmacion('¿ Subir Aval de seguridad ?', msj, () => adjuntar_aval(e.target, 'Sel_Seg'));
		} else MensajeConClase('Por favor indique si el candidato es aprobado o no', 'info', 'Ooop!');
	});

	$('#form_adjuntar_certificado').submit((e) => {
		$("#form_adjuntar_certificado button[type='submit']").prop('disabled', true);
		e.preventDefault();
		const form_data = new FormData(e.target);
		const { id, correo, solicitante, id_tipo_solicitud, especificaciones } = $('#tabla_solicitudes')
			.DataTable()
			.row('.warning')
			.data();
		form_data.append('id', id);
		form_data.append('tipo_certificado', id_tipo_solicitud);
		form_data.append('ops', !!especificaciones);

		enviar_formulario(`${ruta}adjuntar_certificado`, form_data, ({ mensaje, tipo, titulo, certificado }) => {
			MensajeConClase(mensaje, tipo, titulo);
			if (tipo === 'success') {
				if (certificado) {
					const tipo_certificado =
						id_tipo_solicitud === 'Hum_Cert' ? 'Certificado Laboral' : 'Certificado de Ingresos y Retenciones';

					const title = `Respuesta a solicitud de ${tipo_certificado.toLocaleLowerCase()}`;
					const msg = `
					<p>Estimado colaborador</p>
					<p>Se adjunta el ${tipo_certificado.toLocaleLowerCase()}.</p>
					<p>Favor conservar este documento para los trámites pertinentes.</p>
					<p>"Este correo ha sido enviado por un sistema automático, por lo tanto agradecemos no responder"</p>`;

					const ext = certificado.split('.')[1];
					const path = `../${ruta_archivos_certificados}${certificado}`;
					const filename = `${tipo_certificado}.${ext}`;

					enviar_correo_personalizado(
						'th',
						msg,
						correo,
						solicitante,
						'AGIL Talento Humano CUC',
						title,
						'Par_TH',
						1,
						[path, filename]
					);
				}

				$('#modal_adjuntar_certificado').modal('hide');
				listar_solicitudes();
			}
			$("#form_adjuntar_certificado button[type='submit']").prop('disabled', false);
		});
	});

	$('#form_aval_medico').submit((e) => {
		e.preventDefault();
		const radioValue = $("#form_aval_medico input[name='vb_medico']:checked").val();
		if (radioValue == 1 || radioValue == 2) {
			const msj =
				radioValue == 1
					? 'Se cargará el aval de seguridad!'
					: 'Se cargará el aval Médico y el candidato será descartado de este proceso de selección';
			!$('#file_aval_medico').val()
				? MensajeConClase('Por favor adjunte el aval médico', 'info', 'Ooops!')
				: msj_confirmacion('¿ Subir Aval Médico ?', msj, () => adjuntar_aval(e.target, 'Sel_Med'));
		} else MensajeConClase('Por favor indique si el candidato es aprobado o no', 'info', 'Ooop!');
	});

	$('#btn_remove_formacion').click(() => {
		const estudio = $('#cbxestudios').val();
		if (estudio) {
			estudios = estudios.filter((element) => estudios.indexOf(element) != estudio);
			cargar_estudios();
		} else MensajeConClase('Por favor seleccione el estudio que desea eliminar', 'info', 'Ooops!');
	});

	$('#btn_remove_dpto').click(() => {
		const programa = $('#cbxprogramas').val();
		if (programa) {
			const p = programas.find((element) => element.id === programa);
			if (p) {
				const index = programas.indexOf(p);
				programas.splice(index, 1);
				cargar_programas();
				$('#modal_buscar_departamento').modal('hide');
				MensajeConClase('Programa Eliminado Exitosamente', 'success', 'Proceso Exitoso!');
			} else MensajeConClase('Este departamento ya ha sido asignado.', 'info', 'Ooops!');
		} else MensajeConClase('Por favor elija un programa.', 'info', 'Ooops!');
	});

	$('#form_buscar_departamento').submit((e) => {
		e.preventDefault();
		const dep = $(`#form_buscar_departamento input[name=departamento]`).val();
		let callback = '';
		switch (accion_tabla_dependencia) {
			case 'req':
				callback = mod_vacante ? (data) => agregar_dependencia(data) : (data) => add_dependencia(data);
				break;
			case 'sel':
				callback = (data) => asignar_dependencia(data);
				break;
		}
		buscar_dependencia(dep, callback);
	});

	$('#form_seleccion').submit((e) => {
		e.preventDefault();
		guardar_solicitud_seleccion();
	});

	$('#btn_comite').click(() => {
		$('#menu_principal').css('display', 'none');
		listar_comites("(c.id_estado_comite = 'Com_Not' OR c.id_estado_comite = 'Com_Ter' )");
		$('#container_comite').fadeIn('slow');
	});

	$('#btn_modulo_csep').click(() => administrar_modulo('tabla_csep'));


	$('#cbxtipo_solicitud').change(function () {
		if ($(this).val() === 'Tcsep_Con' && tipo_requisicion === 'Vac_Aca') {
			fields_apertura();
			$('.div_evaluacion').html('');
		} else if ($(this).val() === 'Tcsep_Eva') {
			$('#div_apertura').hide('slow').html('');
			fields_evaluacion_candidato();
		} else $('#div_apertura, .div_evaluacion').hide('slow').html('');
	});
	$('#form_inc_eps select[name = tipo_beneficiario]').change((e) => {
		if (e.target.value === 'par_hi') {
			$('#div_hijo').removeClass('oculto');
			$('#div_hijom').removeClass('oculto');
			$('#div_hestudio').removeClass('oculto');
			$('#div_padre').addClass('oculto');
		} else if (e.target.value === 'par_pad') {
			$('#div_padre').removeClass('oculto');
			$('#div_hijo').addClass('oculto');
			$('#div_hijom').addClass('oculto');
			$('#div_hestudio').addClass('oculto');
		} else {
			$('#div_padre').addClass('oculto');
			$('#div_hijo').addClass('oculto');
			$('#div_hijom').addClass('oculto');
			$('#div_hestudio').addClass('oculto');
		}
	});
	$('#form_inc_caj select[name = tipo_beneficiario]').change((e) => {
		if (e.target.value === 'par_hi') {
			$('#div_estudio').removeClass('oculto');
			$('#div_mayor').removeClass('oculto');
			$('#div_documento').removeClass('oculto');
			$('#div_padres').addClass('oculto');
			$('#div_pacon').addClass('oculto');
			$('#div_con').addClass('oculto');
			$('#div_conpadres').addClass('oculto');
			$('#div_conyuge').addClass('oculto');
		} else if (e.target.value === 'par_pad') {
			$('#div_padres').removeClass('oculto');
			$('#div_pacon').removeClass('oculto');
			$('#div_conpadres').removeClass('oculto');
			$('#div_mayor').addClass('oculto');
			$('#div_documento').addClass('oculto');
			$('#div_estudio').addClass('oculto');
			$('#div_con').addClass('oculto');
			$('#div_conyuge').addClass('oculto');
		} else if (e.target.value === 'par_con') {
			$('#div_conyuge').removeClass('oculto');
			$('#div_con').removeClass('oculto');
			$('#div_mayor').addClass('oculto');
			$('#div_estudio').addClass('oculto');
			$('#div_padres').addClass('oculto');
			$('#div_pacon').addClass('oculto');
			$('#div_documento').addClass('oculto');
			$('#div_conpadres').addClass('oculto');
		} else {
			$('#div_padres').addClass('oculto');
			$('#div_pacon').addClass('oculto');
			$('#div_conyuge').addClass('oculto');
			$('#div_con').addClass('oculto');
			$('#div_mayor').addClass('oculto');
			$('#div_documento').addClass('oculto');
			$('#div_estudio').addClass('oculto');
			$('#div_conpadres').addClass('oculto');

		}
	});

	$('#cbxtipo_vacante').change(function () {
		if ($(this).val() === 'Vac_Ree') fields_reemplazo();
		else $('#div_persona').hide('fast').html('');
	});

	$('#form_vacante').submit((e) => {
		guardar_vacante();
		e.preventDefault();
	});

	$('#form_revisar_requisicion').submit((e) => {
		msj_confirmacion('¿ Aprobar Solicitud ?', '', revisar_vacante);
		e.preventDefault();
	});

	$('#inicio_return.regresar').click(() => {
		$('inicio-user').fadeOut();
		$('#menu_principal').fadeIn('slow');
	});

	$('#btn_ver_materias, #btn_materias').click(() => cargar_materias());
	$('#btn_ver_dependencias, #btn_dependencias').click(() => cargar_dependencias());

	$('#inicio_return_csep').click(() => administrar_modulo('menu_csep'));
	$('#inicio_return_comite').click(() => administrar_modulo('menu_csep'));

	$('.conadjuntos').click(() => $('#modal_enviar_archivos').modal());

	$('#menu_administrar li').click(function () {
		$('#menu_administrar li').removeClass('active');
		$(this).addClass('active');
		$('#menu_administrar_prestamos li').removeClass('active');
		$('#menu_administrar_prestamos li.cuotas').addClass('active');
		if ($(this)[0].classList.contains('permisos')) {
			$('#form_administrar .btnAgregar').hide();
			$('div.adm_proceso').hide();
			$('div.cuotas').show();
			$('#s_persona').html('Seleccione Persona');
			$('div.permisos').fadeIn();
			id_persona = null;
			listar_actividades(id_persona);
		} else if ($(this)[0].classList.contains('prestamos')) {
			$('div.adm_proceso').hide();
			$('#form_administrar .btnAgregar')
				.addClass('btnCuotas')
				.removeClass('btnDescuentos btnResponsable btnOpciones')
				.show();
			$('div.prestamos').fadeIn();
		} else if ($(this)[0].classList.contains('seleccion')) {
			$('div.adm_proceso').hide();
			$('#form_administrar .btnAgregar')
				.removeClass('btnCuotas btnDescuentos btnOpciones')
				.addClass('btnResponsable')
				.show();
			$('div.seleccion').show();
		} else if ($(this)[0].classList.contains('certificados')) {
			$('div.adm_proceso').hide();
			$('div.certificados').show();
			$('#form_administrar .btnAgregar')
				.removeClass('btnCuotas btnDescuentos btnResponsable')
				.addClass('btnOpciones')
				.hide();
			listar_opciones_certificados();
		}
	});

	$('#menu_administrar_prestamos li').click(function () {
		$('#menu_administrar_prestamos li').removeClass('active');
		$(this).addClass('active');
		if ($(this)[0].classList.contains('descuentos')) {
			$('div.descuentos').fadeIn();
			$('#form_administrar .btnAgregar').addClass('btnDescuentos').removeClass('btnCuotas').fadeIn();
			$('div.cuotas').hide();
		} else if ($(this)[0].classList.contains('cuotas')) {
			$('#form_administrar .btnAgregar').addClass('btnCuotas').removeClass('btnDescuentos').show();
			$('div.descuentos').hide();
			$('div.cuotas').fadeIn();
		}
	});

	$('#btn_csep').click(() => {
		$('#modal_administrar_solicitudes').modal();
		listar_comites();
	});

	$('#btn_filtros').click(() => $('#Modal_filtro').modal());

	$('#btnver_adjuntos').click(() => {
		$('#modal_listar_archivos_adjuntos').modal();
		listar_archivos_adjuntos(id_solicitud);
	});

	$('#agregar_adjuntos_nuevos').click(() => {
		$('#modal_enviar_archivos').modal();
		$('#modal_enviar_archivos #footermodal').html(
			'<button type="button" class="btn btn-danger active btnAgregar" id="enviar_adjuntos"><span class="glyphicon glyphicon-ok"></span> Temrinar</button> <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>'
		);
		$('#enviar_adjuntos').click(function () {
			if (num_archivos != 0) {
				tipo_cargue = 0;
				$('#id_archivo').val(id_solicitud);
				myDropzone.processQueue();
				$('#modal_enviar_archivos').modal('hide');
				listar_archivos_adjuntos(id_solicitud);
			} else {
				MensajeConClase('Seleccione Archivos a adjuntar.', 'info', 'Oops.!');
			}
		});
	});

	$('#cbxtipo_prestamo').change(() => {
		$('#cbxtipo_prestamo').val() === 'Pre_Cru'
			? $('#volante_matricula').show('slow')
			: $('#volante_matricula').hide('slow');
	});

	$('#s_persona').click(() => {
		$('#modal_elegir_persona').modal();
		listar_personas();
		$('#txt_persona').val('');
	});

	$('#frm_buscar_persona').submit((e) => {
		e.preventDefault();
		const persona = $('#txt_persona').val();
		listar_personas(persona);
	});

	$('#btnConfiguraciones').click(async () => {
		$('#menu_administrar li.permisos').trigger('click');
		$('#form_administrar').get(0).reset();
		$('#modal_administrar').modal();
		const { salud, pension, libre, cruce, correo_th } = await traer_descuentos();
		$('#txtSalud').val(salud[0].valor);
		$('#txt_correo_responsable').val(correo_th[0].valor);
		$('#txtPension').val(pension[0].valor);
		$('#txtcuotas_libre').val(libre);
		$('#txtcuotas_cruce').val(cruce);
	});

	$('#btnhistorial').click(() => {
		$('#modal_historial_prestamo').modal();
		get_historial();
	});

	$('#btnrevisar').click(() => {
		const salario = $('#txtsalario');
		const pendiente = $('#txtsaldo_pendiente').val();
		const saldo = $.isNumeric(pendiente) && pendiente >= 0 ? pendiente : 0;
		!salario.val()
			? MensajeConClase('Por favor digite el salario del solicitante.', 'info', 'Ooops!')
			: msj_confirmacion('¿ Solicitud Revisada ?', '', () => revisar_solicitud(salario.val(), saldo));
	});

	$('#txtsalario').keyup((e) => {
		const saldo = get_valor_peso(calcular_saldo(e.target.value));
		const max = get_valor_peso(calcular_saldo_disponible());
		$('#maximo').html(max);
		$('#saldo').html(saldo);
	});

	$('#cbxtipo_descuento').change((e) => {
		if (e.target.value === 'Tip_Pre') {
			$('#campo_deuda')
				.html(
					`<div id="input_deuda" class="input-group"><div class="input-group-addon"><span class="fa fa-dollar"></span></div> <input type="number" class="form-control" min="0" name="deuda" id="txtdeuda" placeholder="Total Deuda" required></div>`
				)
				.fadeIn();
			$('#txtdeuda').prop('required', 'true');
		} else {
			$('#campo_deuda').html('').fadeOut();
			$('#txtdeuda').prop('required', 'false');
		}
	});

	$('#form_guardar_comentario').submit(() => {
		guardar_comentario_comite(id_comite);
		return false;
	});

	$('#form_guardar_comentario_general').submit(() => {
		let comentario = $("#form_guardar_comentario_general input[name='comentario']").val();
		guardar_comentario_general({ comentario, id_solicitud: id_postulante_sele, tipo: 'postulantes' }, () => {
			$('#form_guardar_comentario_general').get(0).reset();
			pintar_comentarios_generales(
				id_postulante_sele,
				'#panel_comentarios_generales',
				'Comentarios a este postulante',
				'postulantes'
			);
		});
		return false;
	});

	$('#form_administrar').submit((e) => {
		e.preventDefault();
		const button = $('#btnguardar_config');
		let tipo;
		if (button.hasClass('btnCuotas')) tipo = 'cuotas';
		else if (button.hasClass('btnDescuentos')) tipo = 'descuentos';
		else if (button.hasClass('btnResponsable')) tipo = 'responsable';
		else if (button.hasClass('btnOpciones')) $('#modal_opciones_certificados').modal();
		if (tipo) msj_confirmacion('¿Modificar Datos?', '', () => modificar_configuraciones(tipo));
	});

	$('#btnlimpiar_descuentos').click(() => limpiar_salario());

	$('#form_descuentos').submit((e) => {
		e.preventDefault();
		agregar_descuento();
	});

	$('#form_cambiar_persona').submit(() => {
		let dato = $('#cbx_personas_change').val();
		cambiar_persona(dato);
		return false;
	});

	$("#form_modificar_postulante_solicitud select[name='id_tipo']").change(function () {
		if (datos_postulante != null)
			mostrar_info_tipo(
				datos_postulante.id,
				'#msj_tipo_cambio_modi',
				'#form_modificar_postulante_solicitud',
				'.container_tip_Ca_modi',
				'#cont_nuevos_modi'
			);
	});

	$("#form_asignar_postulante select[name='id_tipo']").change(function () {
		if (datos_postulante != null) mostrar_info_tipo(datos_postulante.id);
	});

	$('.regresar_menu').click(() => {
		administrar_modulo('menu');
		listar_solicitudes();
	});
	$('#agregar_prestamo').click(() => administrar_modulo('agregar_prestamo'));
	$('#btn_asignar_todo').click(() => {
		let persona = $('#cbx_personas_vb_csep').val();
		asignar_todos_programas(persona);
	});

	$('#btn_retirar_todo').click(() => {
		let persona = $('#cbx_personas_vb_csep').val();
		retirar_todos_programas(persona);
	});

	$('#limpiar_filtros').click(() => {
		$('#form_filtro').get(0).reset();
		listar_solicitudes_csep();
	});

	$('#limpiar_filtros_comite').click(() =>
		listar_comites("(c.id_estado_comite = 'Com_Not' OR c.id_estado_comite = 'Com_Ter' )")
	);
	$('#listado_solicitudes').click(() => administrar_modulo('solicitudes'));

	$('#btn_nuevo_csep').click(() => administrar_modulo('agregar_csep'));

	$('#btn_agregar_postulante').click(() => {
		$('#modal_agregar_postulante').modal();
		$('#form_agregar_postulante').get(0).reset();
	});

	$('#btn_buscar_postulante').click(() => {
		container_activo = '#txt_nombre_postulante';
		$('#txt_dato_buscar').val('');
		callbak_activo = (resp) => mostrar_postulante_sele(resp);
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
	});

	// $('#form_citacion_entrevista_jefe #btn_buscar_responsable').click(() => {
	// 	$('#txt_dato_buscar').val('');
	// 	callbak_activo = ({ id, nombre_completo }) => {
	// 		id_persona = id;
	// 		$('#form_citacion_entrevista_jefe input[name="nombre_responsable"]').val(nombre_completo);
	// 		$('#modal_buscar_postulante').modal('hide');
	// 	};
	// 	buscar_postulante('', callbak_activo);
	// 	$('#modal_buscar_postulante').modal();
	// 	$('#txt_dato_buscar').focus();
	// });

	$('#form_seleccion #btn_buscar_jefe').click(() => {
		$('#txt_dato_buscar').val('');
		callbak_activo = ({ id, nombre_completo }) => {
			id_persona_jefe = id;
			$('#form_seleccion input[name="nombre_jefe_responsable"]').val(nombre_completo);
			$('#modal_buscar_postulante').modal('hide');
		};
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
		$('#txt_dato_buscar').focus();
	});

	$('#form_seleccion #btn_buscar_responsable').click(() => {
		$('#txt_dato_buscar').val('');
		callbak_activo = ({ id, nombre_completo }) => {
			id_persona = id;
			$('#form_seleccion input[name="nombre_responsable"]').val(nombre_completo);
			$('#modal_buscar_postulante').modal('hide');
		};
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
		$('#txt_dato_buscar').focus();
	});

	$('#responsable_entrevista').click(() => {
		$('#txt_dato_buscar').val('');
		callbak_activo = ({ id, nombre_completo }) => {
			id_persona = id;
			$('#responsable_entrevista').val(nombre_completo);
			$('#modal_buscar_postulante').modal('hide');
		};
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
		$('#txt_dato_buscar').focus();
	});

	$('#btn_buscar_postulante_modi').click(() => {
		container_activo = '#txt_nombre_postulante_modi';
		$('#txt_dato_buscar').val('');
		callbak_activo = (resp) => mostrar_postulante_sele(resp, 'mod');
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
	});
	$('#form_buscar_persona').submit(() => {
		let dato = $('#txt_dato_buscar').val();
		buscar_postulante(dato, callbak_activo);
		return false;
	});

	$('#btn_buscar_administra').click(() => {
		container_activo = '#txt_nombre_administra';
		$('#txt_dato_buscar').val('');
		buscar_postulante('', () => { });
		$('#modal_buscar_postulante').modal();
	});
	$('#form_buscar_postulante').submit(() => {
		let dato = $('#txt_dato_buscar').val();
		buscar_postulante(dato, (resp) => {
			mostrar_postulante_sele(resp);
		});
		return false;
	});
	$('#form_agregar_postulante').submit(() => {
		agregar_postulante();
		return false;
	});
	$('#form_modificar_postulante').submit((e) => {
		e.preventDefault();
		modificar_postulante();
	});
	$('#form_asignar_postulante').submit(() => {
		asignar_postulante_solicitud();
		return false;
	});
	$('#form_modificar_postulante_solicitud').submit(() => {
		modificar_postulante_solicitud();
		return false;
	});

	$('#btn_administrar').click(() => {
		listar_comites();
		$('#modal_administrar_solicitudes').modal();
	});

	$('#form_guardar_comite').submit(() => {
		guardar_comite_general('csep');
		return false;
	});

	$('#form_modificar_comite').submit(() => {
		modificar_comite_general(id_comite);
		return false;
	});

	$('#cbx_personas_vb_csep').change(function () {
		let id = $(this).val().trim();
		listar_programas_persona_tabla(id);
		configurar_elementos_persona_aprueba(id);
		return false;
	});

	$('#btn_limpiar').click(() => {
		$('.filtro').val('');
		listar_solicitudes();
	});

	$('#form_gestionar_postulante').submit(() => {
		let { perfil, vista } = datos_vista;
		let id_comite = $("#form_gestionar_postulante select[name = 'id_comite']").val();
		let id_programa = $("#form_gestionar_postulante select[name = 'id_programa']").val();
		let plan_trabajo = $("#form_gestionar_postulante textarea[name = 'plan_trabajo']").val();
		let observaciones = $("#form_gestionar_postulante textarea[name = 'observaciones']").val();
		let con_fecha = $('#req_fechas_contrato').is(':checked') ? 1 : 0;
		let inicio = $("#form_gestionar_postulante input[name = 'fecha_inicio_contrato']").val();
		let fin = $("#form_gestionar_postulante input[name = 'fecha_fin_contrado']").val();
		let estado = vista == 'talento_humano' ? 'Pos_Con' : 'Pos_Act';
		ejecutar_gestion_postulante(
			id_postulante_sele,
			estado,
			plan_trabajo,
			id_comite,
			inicio,
			fin,
			nombre_completo_cont,
			con_fecha,
			id_programa,
			observaciones
		);
		return false;
	});
	$('#form_modificar_postulante_cmt').submit(() => {
		let data = new FormData(document.getElementById('form_modificar_postulante_cmt'));
		data.append('id', id_postulante_sele);
		modificar_postulante_cmt(data);
		return false;
	});

	$('#form_gestionar_solicitud').submit((e) => {
		msj_confirmacion('¿ Solicitud Procesada ?', '', () => procesar(id_solicitud));
		e.preventDefault();
	});

	$('#admin_comite').click(function () {
		administrar_modulo('admin_comite');
		$('#nav_admin_csep li').removeClass('active');
		$(this).addClass('active');
	});

	$('#admin_csep').click(function () {
		administrar_modulo('admin_csep');
		$('#nav_admin_csep li').removeClass('active');
		$(this).addClass('active');
	});

	$('#form_nuevo_prestamo').submit(() => {
		callbak_activo = () => agregar_prestamo();
		$('#modal_terminos_condiciones').modal();
		return false;
	});

	$('#btn_aceptar_terminos').click(() => callbak_activo());

	$('#btn_notificaciones').click(() => {
		//mostrar_notificaciones_comentarios_comite('csep', (id) => { listar_postulantes_por_comite(id) });
		pintar_notificaciones_comentarios_general(
			'cc.tipo = "postulantes"',
			['Per_Admin', 'Per_Csep'],
			'#panel_notificaciones_generales',
			'.n_notificaciones',
			'#modal_notificaciones',
			'Notificaciones Postulantes',
			abrir_postulacion
		);
		$('#modal_notificaciones').modal();
	});

	$('#req_fechas_contrato').click(function () {
		if ($(this).is(':checked')) {
			$('#container_fechas_cot').show('slow');
			$('#container_fechas_cot input').val('').attr('required', 'true');
			$('.tr_fechas').show('fast');
		} else {
			$('#container_fechas_cot').hide('slow');
			$('#container_fechas_cot input').val('').removeAttr('required', 'true');
			$('.tr_fechas').hide('fast');
		}
	});
	$("#form_gestionar_postulante input[name='fecha_inicio_contrato']").change(function () {
		$('.fecha_inicio_contrato').html($(this).val());
	});
	$("#form_gestionar_postulante input[name='fecha_fin_contrado']").change(function () {
		$('.fecha_fin_contrado').html($(this).val());
	});
	$("#form_gestionar_postulante textarea[name='observaciones']").change(function () {
		$('.observaciones_contrato').html($(this).val());
	});

	$("#form_req_posgrado select[name='tipo_programa']").change(async function () {
		let tipo_programa = $(this).val();
		const datos = await traer_programas_req(tipo_programa);
		pintar_datos_combo(datos, '.cbxreq_programa', 'Seleccione Programa');
	});

	$(".input_buscar_requisicion").click(() => {
		$("#txt_dato_buscar").val('');
		buscar_requisiciones();
		$("#modal_requisicion").modal();
	});

	$("#btn_add_competencia").click(() => {
		$('#form_buscar_competencia').get(0).reset();
		$("#modal_buscar_competencia").modal();
		callbak_activo_aux = () => agregar_competencia();
		buscar_competencias();
	});

	$('#form_buscar_competencia').submit((e) => {
		e.preventDefault();
		const dep = $(`#form_buscar_competencia input[name=competencia]`).val();
		buscar_competencias(dep);
	});

	$('#form_nivel_competencia').submit((e) => {
		e.preventDefault();
		callbak_activo_aux();
	});

	$('#btn_remove_comp').click(() => {
		const comp = $('#cbxcompetencias').val();
		if (comp) {
			const c = competencias.find((element) => element.id_competencia === comp);
			if (c) {
				const index = competencias.indexOf(c);
				competencias.splice(index, 1);
				cargar_competencias();
				MensajeConClase('Competencia eliminada Exitosamente', 'success', 'Proceso Exitoso!');
			}
		} else MensajeConClase('Por favor seleccione la competencia que desea eliminar', 'info', 'Ooops!');
	});

	$('#btn_edit_competencia').click(() => {
		const comp = $('#cbxcompetencias').val();
		if (comp) {
			$('#form_nivel_competencia').get(0).reset();
			competencias.map((element) => {
				if (element.id_competencia === comp) {
					if (element.tipo == 1) {
						$(`#form_nivel_competencia #rd_vb_f`).attr('checked', true);
						$(`#form_nivel_competencia #rd_vb_o`).attr('checked', false);
					} else {
						$(`#form_nivel_competencia #rd_vb_f`).attr('checked', false);
						$(`#form_nivel_competencia #rd_vb_o`).attr('checked', true);
					}
					$(`#form_nivel_competencia select[name=nivel_competencia]`).val(element.nivel);
					$(`#form_nivel_competencia textarea[name=observaciones]`).val(element.observaciones)
				}
			});
			$("#modal_nivel_competencia").modal();
			callbak_activo_aux = () => modificar_competencia(comp);
		} else MensajeConClase('Por favor seleccione la competencia que desea modificar', 'info', 'Ooops!');
	});

	$("#btnReportes").click(function () {
		$("#modal_crear_reportes").modal();
	});

	$("#form_reporte").submit((e) => {
		e.preventDefault();
		generar_reporte();
	});

	$('#form_vb_ausentismo').submit((e) => {
		e.preventDefault();
		// const data = $('#tabla_solicitudes').DataTable().row('.warning').data();
		const data = info_solicitud;
		const { tipo_solicitud } = data;
		const radioValue = $("#form_vb_ausentismo input[name='vb_ausentismo']:checked").val();
		const ser = `<a href="${server}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
		if (radioValue == 0) {
			title = 'Rechazar solicitud';
			msj_confirmacion_input(
				title,
				'Por favor digite motivo de rechazo de la solicitud.',
				'Motivo de Rechazo',
				(msj) => {
					swal.close();
					const mensaje = `
					<p>Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha sido negada.</p>
					<p><strong>Motivo de rechazo:</strong> "${msj}"</p>
					<p>Más información en: ${ser}</p>`;
					const asunto = `Solicitud de ${tipo_solicitud} rechazada`;
					data.notificar_nomina = false;
					vb_ausentismo(e.target, data, mensaje, asunto);
				}
			);
		} else {
			let title = 'Aprobar solicitud';
			const mensaje = `
			<p>Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha sido aprobada.</p>
			<p>Más información en: ${ser}</p>`;
			const asunto = `Solicitud de ${tipo_solicitud} aprobada `;
			data.notificar_nomina = true;
			vb_ausentismo(e.target, data, mensaje, asunto);
		}
	});
});

const traer_programas_req = (tipo_programa) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}traer_programas_req`, { tipo_programa }, (data) => {
			resolve(data);
		});
	});
};

const listar_opciones_certificados = () => {
	consulta_ajax(`${ruta}listar_opciones_certificados`, {}, (data) => {
		let i = 0;
		$('#tabla_opciones_certificado tbody')
			.off('click', 'tr')
			.off('click', 'tr span.activar')
			.off('click', 'tr span.desactivar');
		const myTable = $('#tabla_opciones_certificado').DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data,
			columns: [
				{ render: (data, type, full, meta) => ++i },
				{ data: 'opcion' },
				{
					render: (data, type, { asignado }, meta) => {
						return asignado == '1'
							? "<span class='btn btn-default desactivar'><span class='fa fa-remove color_danger'></span></span>"
							: "<span class='btn btn-default activar'><span class='fa fa-check color_success'></span></span>";
					}
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_opciones_certificado tbody').on('click', 'tr', function () {
			$('#tabla_opciones_certificado tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_opciones_certificado tbody').on('click', 'tr span.activar', function () {
			let data = myTable.row($(this).parent().parent()).data();
			activar_opcion_certificado(data);
		});

		$('#tabla_opciones_certificado tbody').on('click', 'tr span.desactivar', function () {
			let data = myTable.row($(this).parent().parent()).data();
			activar_opcion_certificado(data);
		});
	});
};
const activar_opcion_certificado = (data) => {
	consulta_ajax(`${ruta}activar_opcion_certificado`, data, ({ mensaje, tipo, titulo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') listar_opciones_certificados();
	});
};

const solicitar_certificado = (data) => {
	if (data.tipo_certificado == 1) generar_certificado();
	consulta_ajax(`${ruta}solicitar_certificado`, data, ({ mensaje, tipo, titulo }) => {
		if (data.tipo_certificado == 0) {
			MensajeConClase(mensaje, tipo, titulo);
			if (tipo === 'success') {
				$('#modal_certificado_personalizado').modal('hide');
				if (data.opciones.length > 0 || data.especificaciones) {
					const ser = `<a href="${server}index.php/talento_humano/"><b>agil.cuc.edu.co</b></a>`;
					const mensaje = `
						<p>Su solicitud de certificado ha sido registrada exitosamente. En un máximo de 8 dias hábiles le será enviado su certificado.</p>
						<p>Más información en: ${ser}</p>`;
					const asunto = 'Solicitud de Certificado Registrada';
					enviar_correo_personalizado('th', mensaje, '', '', 'AGIL Talento Humano CUC', asunto, 'Par_TH', -1);
				}
			}
		} else if (titulo == 'error') MensajeConClase(mensaje, tipo, titulo);
		limpiar_form_certificado(true);
	});
};

const generar_certificado = () => {
	const route = `${Traer_Server()}index.php/talento_humano/certificado/${datos_vista.persona}`;
	window.open(route, '_blank');
	window.focus();
};

const limpiar_form_certificado = (clean_all_form = false) => {
	if (clean_all_form) $("input[type='tipo_certificado']").removeAttr('checked');
	$('input[type="radio"].form_certificado__radio').removeAttr('checked');
	$("input[type='checkbox'].form_certificado__check").prop('checked', false);
	$('.form_certificado__textarea').val('');
};

const administrar_modulo = async (tipo) => {
	if (tipo == 'solicitudes') {
		let datos_postulante = null;
		$('#form_asignar_postulante').get(0).reset();
		listar_solicitudes();
		$('#container_solicitudes').fadeIn(1000);
		$('#menu_principal').css('display', 'none');
	} else if (tipo == 'menu') {
		datos_postulante = null;
		$('#menu_principal').fadeIn(1000);
		$('#container_solicitudes').css('display', 'none');
	} else if (tipo == 'agregar_csep') {
		datos_postulante = null;
		$('#btn_ver_postulacion').remove();
		$('#txt_nombre_postulante').val('');
		$('#modal_asignar_postulante').modal();
		const cargos = await listar_cargos(1);
		pintar_datos_combo(cargos, "#form_asignar_postulante select[name='id_cargo']", 'Seleccione Cargo');
		pintar_datos_combo(cargos, "#form_asignar_postulante select[name='id_cargo_actual']", 'Seleccione Cargo');
	} else if (tipo == 'agregar_prestamo') {
		$('#modal_nuevo_prestamo').modal();
		$('#form_nuevo_prestamo').get(0).reset();
		$('#volante_matricula').fadeOut('fast');
		$('#text_terminos').html(`
    <h4 class='text-justify'>CORPORACIÓN UNIVERSIDAD DE LA COSTA C.U.C. concederá préstamos a los empleados que lo soliciten bajo los siguientes términos y condiciones :</h4>
	<ul>
		<li type="disc">Para acceder al préstamo institucional, el empleado deberá tener como mínimo un año de contratación con la institución.</li>
		<li type="disc">Solo se podrá solicitar hasta la cantidad de una y media veces (1.5) del valor de su salario vigente al momento de la petición y siempre que el saldo de la cesantía sea igual o superior a dicha suma.</li>
		<li type="disc"> Este préstamo será cancelado por el trabajador hasta en diez (10) cuotas iguales mensuales, siguientes a la fecha en que se hizo efectivo el préstamo y usted autoriza los descuentos correspondientes a sus salarios y, en caso de retiro, el descuento se hará del total de las prestaciones sociales e indemnizaciones debidas.</li>
		<li type="disc"> Para poder tener derecho a un nuevo préstamo, el trabajador deberá estar a paz y salvo con la totalidad del préstamo anterior.</li>
		<li type="disc">Solo se otorgará préstamo a profesores de planta tiempo completo, medio tiempo y personal administrativo.</li>
		<li type="disc">Esta solicitud está sujeta a verificación y VoBo por parte de Talento Humano y aprobación por parte de Vicerrectoría Financiera.</li>
		<li type="disc">El trámite de verificación y VoBo por parte de talento humano se estima entre los 5 días hábiles siguientes a la fecha de la solicitud.</li>
		<li type="disc">Si se realiza un pago extraordinario parcial o total a uno o más prestamos, la persona deberá llevar el comprobante de pago a la oficina de Talento Humano para hacer el respectivo descuento.</li>
		<li>Los préstamos por cruce de matrícula podrán ser solicitados en un máximo de 5 cuotas mensuales descontables a partir del mes de aprobación.</li>
		<li>Los préstamos serán tramitados en un máximo de 15 días hábiles contados a partir de la fecha de su solicitud.</li>
	</ul>
	<h5 class='text-justify'>Al continuar acepta los términos y condiciones expuestas por la CORPORACION UNIVERSIDAD DE LA COSTA C.U.C. </h5>
	`);
	} else if (tipo == 'admin_comite') {
		listar_comites();
		$('#container_admin_comite').fadeIn(1000);
		$('#container_admin_csep').css('display', 'none');
	} else if (tipo == 'admin_csep') {
		let persona = $('#cbx_personas_vb_csep').val();
		listar_personas_vb_csep(persona);
		$('#container_admin_csep').fadeIn(1000);
		$('#container_admin_comite').css('display', 'none');
	} else if (tipo == 'menu_csep') {
		$('#menu_principal').fadeIn(1000);
		$('.div_table').css('display', 'none');
	} else if (tipo == 'tabla_csep') {
		$('#container_solicitudes').fadeIn(1000);
		listar_solicitudes_csep();
		$('#menu_principal').css('display', 'none');
	}
};

const buscar_persona_arl = (dato, callbak) => {
	consulta_ajax(`${ruta}buscar_persona_arl`, { dato, tipo_persona }, (resp) => {
		$(`#tabla_persona_arl tbody`)
			.off('click', 'tr td .postulante')
			.off('dblclick', 'tr')
			.off('click', 'tr')
			.off('click', 'tr td:nth-of-type(1)');
		const myTable = $('#tabla_persona_arl').DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					defaultContent: `<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>`
				},
				{ data: 'nombre_completo' },
				{ data: 'identificacion' },
				{
					defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Postulante" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default postulante" ></span>'
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_persona_arl tbody').on('click', 'tr', function () {
			$('#tabla_postulantes_busqueda tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});
		$('#tabla_persona_arl tbody').on('dblclick', 'tr', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});
		$('#tabla_persona_arl tbody').on('click', 'tr td .postulante', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});

		$('#tabla_persona_arl tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let data = myTable.row($(this).parent()).data();
			ver_detalle_postulante(data);
		});
	});
};
const buscar_persona_ausentismo = (dato, callbak) => {
	consulta_ajax(`${ruta}buscar_persona_ausentismo`, { dato }, (resp) => {
		$(`#tabla_persona_ausentismo tbody`)
			.off('click', 'tr td .postulante')
			.off('dblclick', 'tr')
			.off('click', 'tr')
			.off('click', 'tr td:nth-of-type(1)');
		const myTable = $('#tabla_persona_ausentismo').DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					defaultContent: `<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>`
				},
				{ data: 'nombre_completo' },
				{ data: 'identificacion' },
				{
					defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Postulante" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default postulante" ></span>'
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_persona_ausentismo tbody').on('click', 'tr', function () {
			$('#tabla_postulantes_busqueda tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});
		$('#tabla_persona_ausentismo tbody').on('dblclick', 'tr', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});
		$('#tabla_persona_ausentismo tbody').on('click', 'tr td .postulante', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});

		$('#tabla_persona_ausenntismo tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let data = myTable.row($(this).parent()).data();
			ver_detalle_postulante(data);
		});
	});
};

const buscar_postulante = (dato, callbak) => {
	consulta_ajax(`${ruta}buscar_postulante`, { dato }, (resp) => {
		$(`#tabla_postulantes_busqueda tbody`)
			.off('click', 'tr td .postulante')
			.off('dblclick', 'tr')
			.off('click', 'tr')
			.off('click', 'tr td:nth-of-type(1)');
		const myTable = $('#tabla_postulantes_busqueda').DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					defaultContent: `<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>`
				},
				{ data: 'nombre_completo' },
				{ data: 'identificacion' },
				{
					defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Postulante" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default postulante" ></span>'
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_postulantes_busqueda tbody').on('click', 'tr', function () {
			$('#tabla_postulantes_busqueda tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});
		$('#tabla_postulantes_busqueda tbody').on('dblclick', 'tr', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});
		$('#tabla_postulantes_busqueda tbody').on('click', 'tr td .postulante', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});

		$('#tabla_postulantes_busqueda tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let data = myTable.row($(this).parent()).data();
			ver_detalle_postulante(data);
		});
	});
};

const agregar_postulante = () => {
	let data = new FormData(document.getElementById('form_agregar_postulante'));
	enviar_formulario(`${ruta}agregar_postulante`, data, (resp) => {
		let { tipo, mensaje, titulo, postulante } = resp;
		if (tipo == 'success') {
			callbak_activo(postulante);
			$('#form_agregar_postulante').get(0).reset();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
};

const modificar_postulante = () => {
	let data = new FormData(document.getElementById('form_modificar_postulante'));
	data.append('id', datos_postulante.id);
	enviar_formulario(`${ruta}modificar_postulante`, data, (resp) => {
		let { tipo, mensaje, titulo, postulante } = resp;
		if (tipo == 'success') {
			$('#msj_tipo_cambio_modi').html(``);
			$('#btn_ver_postulacion').remove();
			callbak_activo(postulante);
			detalle_candidato(postulante);
		}
		$('#modal_modificar_postulante').modal('hide');
		MensajeConClase(mensaje, tipo, titulo);
	});
};
const asignar_postulante_solicitud = () => {
	let data = new FormData(document.getElementById('form_asignar_postulante'));
	let { id, nombre_completo } = datos_postulante;
	data.append('id', id);
	data.append('nombre_completo', nombre_completo);
	enviar_formulario(`${ruta}asignar_postulante_solicitud`, data, (resp) => {
		let { tipo, mensaje, titulo, notifica, id } = resp;
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo == 'success') {
			if (notifica) enviar_correo_estado('Pos_Env', id, '', {}, '/solicitud');
			datos_postulante = null;
			let { vista, perfil } = datos_vista;
			if (vista == 'csep' && (perfil == 'Per_Csep' || perfil == 'Per_Admin')) listar_solicitudes_csep();
			$('#form_asignar_postulante').get(0).reset();
		}
	});
};

const mostrar_postulante_sele = (data, tipo_c = 'add') => {
	const form = '#form_modificar_postulante';
	let {
		id,
		nombre_completo,
		fecha_nacimiento,
		fecha_expedicion,
		lugar_expedicion,
		nombre,
		apellido,
		segundo_nombre,
		segundo_apellido,
		identificacion,
		id_tipo_identificacion
	} = data;
	datos_postulante = { id, nombre_completo };
	if ((lugar_expedicion == null || fecha_nacimiento == null) && tipo_c != 'show') {
		$(`${form} select[name='id_tipo_identificacion']`).val(id_tipo_identificacion);
		$(`${form} input[name='fecha_nacimiento']`).val(fecha_nacimiento);
		$(`${form} input[name='fecha_expedicion']`).val(fecha_expedicion);
		$(`${form} input[name='lugar_expedicion']`).val(lugar_expedicion);
		$(`${form} input[name='nombre']`).val(nombre);
		$(`${form} input[name='apellido']`).val(apellido);
		$(`${form} input[name='segundo_nombre']`).val(segundo_nombre);
		$(`${form} input[name='segundo_apellido']`).val(segundo_apellido);
		$(`${form} input[name='identificacion']`).val(identificacion);
		$('#modal_modificar_postulante').modal();
		callbak_activo = (resp) => {
			$('#form_agregar_postulante').get(0).reset();
			mostrar_postulante_sele(resp);
		};
		MensajeConClase('Le faltan algunos datos al postulante, por favor completar.', 'info', 'Oops.!');
	} else {
		if (tipo_c == 'mod')
			mostrar_info_tipo(
				id,
				'#msj_tipo_cambio_modi',
				'#form_modificar_postulante_solicitud',
				'.container_tip_Ca_modi',
				'#cont_nuevos_modi'
			);
		else if (tipo_c === 'show') id_persona = id;
		else mostrar_info_tipo(id);
		$(container_activo).val(nombre_completo);
		$('#modal_buscar_postulante').modal('hide');
		$('#modal_agregar_postulante').modal('hide');
		$('#modal_modificar_postulante').modal('hide');
	}
};

const ver_detalle_postulante = (data, container = '#tabla_detalle_persona', modal = '#modal_detalle_persona') => {
	let {
		id,
		nombre_completo,
		fecha_nacimiento,
		fecha_expedicion,
		lugar_expedicion,
		nombre,
		apellido,
		segundo_nombre,
		segundo_apellido,
		identificacion,
		id_tipo_identificacion,
		tipo_identificacion
	} = data;
	datos_postulante = { id, nombre_completo };
	$(`${container} .tipo_identificacion`).html(tipo_identificacion);
	$(`${container} .fecha_nacimiento`).html(fecha_nacimiento);
	$(`${container} .fecha_expedicion`).html(fecha_expedicion);
	$(`${container} .lugar_expedicion`).html(lugar_expedicion);
	$(`${container} .nombre_completo`).html(nombre_completo);
	$(`${container} .identificacion`).html(identificacion);
	$(modal).modal();
};

const listar_postulantes_csep = (id, tipo = 'solicitud') => {
	let { perfil, vista, persona } = datos_vista;
	consulta_ajax(`${ruta}listar_postulantes_csep`, { vista, id, tipo }, (resp) => {
		$(`#tabla_postulantes_csep tbody`)
			.off('click', '#btn_aprobar_todo')
			.off('click', 'tr td .postulante')
			.off('dblclick', 'tr')
			.off('click', 'tr')
			.off('click', 'tr td .contratado')
			.off('click', 'tr td .no_apto')
			.off('click', 'tr td .negar')
			.off('click', 'tr td .aprobar')
			.off('click', 'tr td .apto')
			.off('click', 'tr td:nth-of-type(1)')
			.off('click', 'tr td .modificar')
			.off('click', 'tr td .cancelar')
			.off('click', 'tr td .modificar_cmt');
		const myTable = $('#tabla_postulantes_csep').DataTable({
			destroy: true,
			processing: true,
			data: resp,
			order: [[3, 'asc']],
			columns: [
				{
					render: function (data, type, full, meta) {
						let { id_estado_solicitud } = full;
						let bg = 'background-color: white;color: black;';
						if (id_estado_solicitud == 'Pos_Act') bg = 'background-color: #EABD32;color: white;';
						else if (id_estado_solicitud == 'Pos_Apr') bg = 'background-color: #2E79E5;color: white;';
						else if (id_estado_solicitud == 'Pos_Con') bg = 'background-color: #39B23B;color: white;';
						else if (
							id_estado_solicitud == 'Pos_Rev' ||
							id_estado_solicitud == 'Pos_Neg' ||
							id_estado_solicitud == 'Pos_Can'
						)
							bg = 'background-color: #d9534f;color: white;';
						return `<span style="${bg} width: 100%;" class="pointer form-control"><span >ver</span></span>`;
					}
				},
				{ data: 'tipo' },
				{ data: 'nombre_completo' },
				{
					render: function (data, type, full, meta) {
						let { hoja_vida } = full;
						return hoja_vida == null
							? 'N/A'
							: `<a target='_blank' href='${Traer_Server()}${ruta_hojas}${hoja_vida}' style="background-color: white;color: black;width: 100%;" class="pointer form-control"><span >Abrir</span></a>`;
					}
				},
				{
					render: function (data, type, full, meta) {
						let { programa, departamento } = full;
						let { vista } = datos_vista;
						return vista != 'csep' ? departamento : programa;
					}
				},
				{ data: 'cargo' },
				{ data: 'vb' },
				{ data: 'vm' },
				{ data: 'estado_solicitud' },
				{
					render: function (data, type, full, meta) {
						let resp = '';
						if (vista != 'talento_humano') {
							if (
								(perfil == 'Per_Csep' || perfil == 'Per_Admin') &&
								full.id_estado_solicitud == 'Pos_Env'
							) {
								resp = `<span  class="btn btn-default apto " title="Apto" data-toggle="popover" data-trigger="hover"><span class="fa fa-thumbs-up" style='color: #39B23B'></span></span> <span class="btn btn-default no_apto" title="No Apto" data-toggle="popover" data-trigger="hover"><span style='color: #d9534f' class="fa fa-thumbs-down"></span></span>`;
							}

							if (full.id_estado_solicitud == 'Pos_Act' && tipo == 'comite') {
								if (perfil != 'Per_Csep' && full.tiene == 0) {
									resp = `${resp} <span  class="btn btn-default aprobar" title="Aprobar" data-toggle="popover" data-trigger="hover" ><span style="color: #39B23B;" class="fa fa-check-square-o"></span></span> <span  class="btn btn-default negar" title="Negar" data-toggle="popover" data-trigger="hover"><span style='color: #d9534f' class="fa fa-ban"></span></span>`;
								}
								if ((perfil == 'Per_Csep' || perfil == 'Per_Admin') && full.vb == 0 && full.vm == 0) {
									resp = `${resp} <span  class="btn btn-default modificar_cmt fa fa-wrench"  style='color: #2E79E5' title="Modificar" data-toggle="popover" data-trigger="hover"></span>`;
								}
							}
						} else {
							if (perfil == 'Per_Admin_Tal' || perfil == 'Per_Admin') {
								if (full.id_estado_solicitud == 'Pos_Apr') {
									resp = `<span  class="btn btn-default contratado 	fa fa-pencil-square-o"  style='color: #39B23B' title="Contratado" data-toggle="popover" data-trigger="hover"></span> <span  class="btn btn-default cancelar fa fa-close" style='color: #d9534f' title="Cancelar" data-toggle="popover" data-trigger="hover"></span>`;
								}
							}
						}

						if (
							full.id_estado_solicitud == 'Pos_Env' &&
							(full.usuario_registra == persona || perfil == 'Per_Admin')
						) {
							resp = `${resp} <span  class="btn btn-default modificar fa fa-wrench"  style='color: #2E79E5' title="Modificar" data-toggle="popover" data-trigger="hover"></span> <span  class="btn btn-default cancelar fa fa-close" style='color: #d9534f' title="Cancelar" data-toggle="popover" data-trigger="hover"></span>`;
						}
						return resp.length == 0 ? 'Cerrada' : resp;
					}
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones()
		});

		$('#tabla_postulantes_csep tbody').on('click', 'tr', function () {
			let { id } = myTable.row(this).data();
			id_postulante_sele = id;
			$('#tabla_postulantes_csep tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});
		$('#tabla_postulantes_csep tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let data = myTable.row($(this).parent()).data();
			config_btn_contratado();
			ver_detalle_postulante_solicitud(data, '#tabla_detalle_postulante', '#modal_detalle_postulante', tipo);
		});
		$('#tabla_postulantes_csep tbody').on('dblclick', 'tr', function () {
			let data = myTable.row(this).data();
			config_btn_contratado();
			ver_detalle_postulante_solicitud(data, '#tabla_detalle_postulante', '#modal_detalle_postulante', tipo);
		});

		$('#tabla_postulantes_csep tbody').on('click', 'tr td .cancelar', function () {
			let { id, nombre_completo } = myTable.row($(this).parent().parent()).data();
			cambiar_estado_postulantes_csep(id, 'Pos_Can', nombre_completo);
		});
		$('#tabla_postulantes_csep tbody').on('click', 'tr td .modificar', function () {
			let data = myTable.row($(this).parent().parent()).data();
			mostrar_postulante_modificar(data);
		});

		if (vista != 'talento_humano') {
			if (perfil == 'Per_Csep' || perfil == 'Per_Admin') {
				$('#tabla_postulantes_csep tbody').on('click', 'tr td .apto', function () {
					let { id, nombre_completo } = myTable.row($(this).parent().parent()).data();
					cambiar_estado_postulantes_csep(id, 'Pos_Act', nombre_completo);
				});
				$('#tabla_postulantes_csep tbody').on('click', 'tr td .no_apto', function () {
					let { id, nombre_completo } = myTable.row($(this).parent().parent()).data();
					cambiar_estado_postulantes_csep(id, 'Pos_Rev', nombre_completo);
				});
				$('#tabla_postulantes_csep tbody').on('click', 'tr td .modificar_cmt', function () {
					let data = myTable.row($(this).parent().parent()).data();
					mostrar_datos_postulante_comite(data);
				});
			}
			if (perfil != 'Per_Csep') {
				$('#tabla_postulantes_csep tbody').on('click', 'tr td .aprobar', function () {
					let { id, nombre_completo } = myTable.row($(this).parent().parent()).data();
					cambiar_estado_postulantes_csep(id, 'Pos_Apr', nombre_completo);
				});
				$('#tabla_postulantes_csep tbody').on('click', 'tr td .negar', function () {
					let { id, nombre_completo } = myTable.row($(this).parent().parent()).data();
					cambiar_estado_postulantes_csep(id, 'Pos_Neg', nombre_completo);
				});
			}
		} else {
			if (perfil == 'Per_Admin_Tal' || perfil == 'Per_Admin') {
				const limpiar_datos_contrato = () => {
					$('.tr_observaciones_contrato').show('fast');
					$('.tr_fechas').hide('fast');
					$('.fecha_fin_contrado').html('');
					$('.observaciones_contrato').html('');
					$('.fecha_inicio_contrato').html('');
				};
				$('#tabla_postulantes_csep tbody').on('click', 'tr td .contratado', function () {
					let data = myTable.row($(this).parent().parent()).data();
					ver_detalle_postulante_solicitud(
						data,
						'#tabla_detalle_postulante',
						'#modal_detalle_postulante',
						tipo
					);
					limpiar_datos_contrato();
					config_btn_contratado(0);
					$('#btn_pic').click(() => {
						limpiar_datos_contrato();
						cambiar_estado_postulantes_csep(data.id, 'Pos_Con', data.nombre_completo);
					});
				});
			}
		}
		const mostrar_postulante_modificar = async (data) => {
			const form = '#form_modificar_postulante_solicitud';
			$(form).get(0).reset();
			let {
				id_cargo_actual_postulante,
				nombre_completo,
				procedencia,
				id_departamento_postulante,
				id_cargo_postulante,
				id_formacion,
				observaciones,
				id_postulante,
				hoja_vida,
				id,
				id_tipo,
				prueba_psicologia,
				id_cargo_actual,
				id_departamento_actual_postulante
			} = data;
			datos_postulante = { id: id_postulante, nombre_completo };
			$('#txt_nombre_postulante_modi').val(nombre_completo);
			$('#text_hoja_vida').val(hoja_vida);
			$('#text_prueba_psicologia').val(prueba_psicologia);
			$(`${form} select[name='id_tipo']`).val(id_tipo);
			$('#ver_hoja_modi').attr('href', `${Traer_Server()}${ruta_hojas}${hoja_vida}`);
			$('#ver_prueba_modi').attr('href', `${Traer_Server()}${ruta_hojas}${prueba_psicologia}`);
			$(`${form} input[name='procedencia']`).val(procedencia);
			$(`${form} select[name='id_departamento']`).val(id_departamento_postulante);
			$(`${form} select[name='id_formacion']`).val(id_formacion);
			$(`${form} textarea[name='observaciones']`).val(observaciones);
			const cargos = await listar_cargos(1);
			pintar_datos_combo(
				cargos,
				"#form_modificar_postulante_solicitud select[name='id_cargo']",
				'Seleccione Cargo',
				id_cargo_postulante
			);
			mostrar_info_tipo(
				id_postulante,
				'#msj_tipo_cambio_modi',
				form,
				'.container_tip_Ca_modi',
				'#cont_nuevos_modi'
			);
			if ((id_tipo == 'Tip_Cam' || id_tipo == 'Tip_Cam_Plan') && id_cargo_actual_postulante != null) {
				$(`${form} select[name='id_departamento_actual']`).val(id_departamento_actual_postulante);
				pintar_datos_combo(
					cargos,
					"#form_modificar_postulante_solicitud select[name='id_cargo_actual']",
					'Seleccione Cargo',
					id_cargo_actual_postulante
				);
			}
			$('#modal_modificar_postulante_solicitud').modal();
		};

		if (tipo == 'comite' && vista == 'csep' && perfil != 'Per_Csep') {
			const actos = resp.find((element) => element.tiene == 0);
			if (actos) {
				$('#btn_aprobar_todo').show('fast').click(() => {
					aprobar_todos_postulantes_comite(id_comite);
				});
			} else {
				$('#btn_aprobar_todo').css('display', 'none');
			}
		} else {
			$('#btn_aprobar_todo').css('display', 'none');
		}
		if (tipo == 'comite') $('#container_comentarios').show('fast');
		else $('#container_comentarios').css('display', 'none');

		const mostrar_datos_postulante_comite = ({ id, id_programa, plan_trabajo, id_comite }) => {
			pintar_comites_combo(id_comite);
			$('#form_modificar_postulante_cmt').get(0).reset();
			$("#form_modificar_postulante_cmt select[name='id_programa']").val(id_programa);
			$("#form_modificar_postulante_cmt textarea[name='plan_trabajo']").val(plan_trabajo);
			$('#container_fechas_cot').hide('slow');
			$('#container_fechas_cot input').val('').removeAttr('required', 'true');
			$('#modal_modificar_postulante_cmt').modal();
		};
	});
};

const obtener_vista_llama = (vista, perfil, id = '', url, persona) => {
	datos_vista = { vista, perfil, persona };
	if (vista == 'csep' || vista == 'comite_csep') {
		//mostrar_notificaciones_comentarios_comite('csep', (id) => { listar_postulantes_por_comite(id); });
		pintar_notificaciones_comentarios_general(
			'cc.tipo = "postulantes"',
			['Per_Admin', 'Per_Csep'],
			'#panel_notificaciones_generales',
			'.n_notificaciones',
			'#modal_notificaciones',
			'Notificaciones Postulantes',
			abrir_postulacion
		);
	}
	cargar_tipos_solicitudes_filtro(vista);
	cargar_info_correo_id(id, url);
};

const cargar_tipos_solicitudes_filtro = (vista) => {
	consulta_ajax(`${ruta}cargar_tipos_solicitudes_filtro`, { vista }, (data) => {
		$('.cbxtipo').html('<option value="">Filtrar por Tipo de Solicitud</option>');
		data.forEach(({ nombre_tipo, id_tipo }) =>
			$('.cbxtipo').append(`<option value="${id_tipo}">${nombre_tipo}</option>`)
		);
	});
};

const cambiar_estado_postulantes_csep = (id, estado, nombre_completo) => {
	const confirm_normal = (id, estado, title, nombre_completo) => {
		swal(
			{
				title,
				text:
					"Tener en cuenta que, al modificar el estado se habilitara la solicitud para el siguiente  proceso, si desea continuar debe  presionar la opción de 'Si, Entiendo' !",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#D9534F',
				confirmButtonText: 'Si, Entiendo!',
				cancelButtonText: 'No, cancelar!',
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true
			},
			function (isConfirm) {
				if (isConfirm) {
					ejecutar_gestion_postulante(id, estado, '', '', '', '', nombre_completo);
				}
			}
		);
	};

	const confirm_input = (id, estado, title, nombre_completo) => {
		let plac = estado == 'Pos_Act' ? 'Plan de Trabajo' : 'Motivo';
		swal(
			{
				title,
				text: '',
				type: 'input',
				showCancelButton: true,
				confirmButtonColor: '#D9534F',
				confirmButtonText: 'Aceptar!',
				cancelButtonText: 'Cancelar!',
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true,
				inputPlaceholder: `${plac}`
			},
			function (mensaje) {
				if (mensaje === false) return false;
				if (mensaje === '') {
					swal.showInputError(`Debe Ingresar el ${plac}.!`);
				} else {
					ejecutar_gestion_postulante(id, estado, mensaje, '', '', '', nombre_completo);
				}
			}
		);
	};
	let datos = { title: 'Postulante Apto ..?', tipo: 3 };
	if (estado == 'Pos_Rev') datos = { title: 'Postulante No Apto ..?', tipo: 2 };
	else if (estado == 'Pos_Apr') datos = { title: 'Postulante Aprobado ..?', tipo: 1 };
	else if (estado == 'Pos_Neg') datos = { title: 'Postulante Negado ..?', tipo: 1 };
	else if (estado == 'Pos_Can') datos = { title: 'Postulante Cancelado ..?', tipo: 2 };
	else if (estado == 'Pos_Con') datos = { title: 'Postulante Contratado ..?', tipo: 3 };
	let { title, tipo } = datos;
	if (tipo == 1) {
		confirm_normal(id, estado, title, nombre_completo);
	} else if (tipo == 2) {
		confirm_input(id, estado, title, nombre_completo);
	} else if (tipo == 3) {
		nombre_completo_cont = nombre_completo;
		if (estado == 'Pos_Act') pintar_comites_combo();
		$('#form_gestionar_postulante').get(0).reset();
		$('#container_fechas_cot').hide('slow');
		$('#container_fechas_cot input').val('').removeAttr('required', 'true');
		$('#modal_gestionar_postulante').modal();
	}
};

const ejecutar_gestion_postulante = (
	id,
	estado,
	mensaje_v = '',
	id_comite_v = '',
	fecha_inicio_contrato = '',
	fecha_fin_contrado = '',
	nombre_completo = '',
	con_fecha = false,
	id_programa = null,
	observaciones = ''
) => {
	consulta_ajax(
		`${ruta}cambiar_estado_postulantes_csep`,
		{
			id,
			estado,
			mensaje: mensaje_v,
			id_comite: id_comite_v,
			fecha_inicio_contrato,
			fecha_fin_contrado,
			con_fecha,
			id_programa,
			observaciones
		},
		(resp) => {
			let { tipo, mensaje, titulo, up_comite, notifica, up_solicitud } = resp;
			if (tipo == 'success') {
				id_activo = tipo_activo == 'comite' ? id_comite : id_solicitud;
				mensaje_v = estado == 'Pos_Con' ? observaciones : mensaje_v;
				if (notifica) enviar_correo_estado(estado, id_activo, mensaje_v, nombre_completo, '');
				listar_postulantes_csep(id_activo, tipo_activo);
				$('#modal_gestionar_postulante').modal('hide');
				$('#modal_detalle_postulante').modal('hide');
				$('#form_gestionar_postulante').get(0).reset();
				let { vista, perfil } = datos_vista;
				if (up_comite == 'si')
					listar_comites("(c.id_estado_comite = 'Com_Not' OR c.id_estado_comite = 'Com_Ter')");
				if (up_solicitud == 'si' && vista == 'csep' && perfil == 'Per_Csep') listar_solicitudes_csep();
				if (up_solicitud == 'si' && vista == 'talento_humano') listar_solicitudes();
			}
			MensajeConClase(mensaje, tipo, titulo);
		}
	);
};

const ver_detalle_postulante_solicitud = async (
	data,
	container = '#tabla_detalle_persona',
	modal = '#modal_detalle_persona',
	tipo_sol = 'solicitud',
	hide = true,
	add = false
) => {
	let {
		id_cargo_actual_postulante,
		programa,
		fecha_inicio_contrato,
		fecha_fin_contrado,
		motivo,
		id,
		nombre_completo,
		fecha_nacimiento,
		fecha_expedicion,
		lugar_expedicion,
		cargo,
		departamento,
		estado_solicitud,
		hoja_vida,
		identificacion,
		formacion,
		plan_trabajo,
		observaciones,
		tipo_identificacion,
		fecha_registra,
		procedencia,
		id_estado_solicitud,
		id_postulante,
		tipo,
		id_tipo,
		id_postulacion,
		prueba_psicologia,
		id_cargo_postulante: id_cargo_actual,
		cargo_actual,
		departamento_actual,
		vb,
		vm,
		observaciones_contrato
	} = data;
	datos_postulante = { id: id_postulante, nombre_completo };
	$(`${container} .tipo_identificacion`).html(tipo_identificacion);
	$(`${container} .fecha_nacimiento`).html(fecha_nacimiento);
	$(`${container} .fecha_expedicion`).html(fecha_expedicion);
	$(`${container} .lugar_expedicion`).html(lugar_expedicion);
	$(`${container} .observaciones_contrato`).html(observaciones_contrato);
	$(`${container} .nombre_completo`).html(`${nombre_completo}`);
	if (prueba_psicologia != null)
		$(`${container} .prueba_psicologia`).html(
			`<a target='_blank' href='${Traer_Server()}${ruta_hojas}${prueba_psicologia}'><span class='fa fa-eye red'></span> ver Informe Evaluativo <a>`
		);
	else
		$(`${container} .prueba_psicologia`).html(
			`<span><span class='fa fa-eye-slash red'></span> N/A Informe Evaluativo <span>`
		);

	if (hoja_vida != null)
		$(`${container} .hoja_vida`).html(
			`<a target='_blank' href='${Traer_Server()}${ruta_hojas}${hoja_vida}'><span class='fa fa-eye red'></span>ver hoja de vida <a>`
		);
	else $(`${container} .hoja_vida`).html(`<span><span class='fa fa-eye-slash red'></span> N/A hoja de vida <span>`);

	$(`${container} .identificacion`).html(identificacion);
	$(`${container} .vb`).html(vb);
	$(`${container} .vm`).html(vm);

	$(`${container} .tipo`).html(tipo);
	$(`${container} .procedencia`).html(procedencia);
	$(`${container} .formacion`).html(formacion);
	$(`${container} .dependencia`).html(departamento);
	$(`${container} .programa`).html(programa);
	$(`${container} .cargo`).html(cargo);
	$(`${container} .plan_trabajo`).html(plan_trabajo);
	$(`${container} .observaciones`).html(
		observaciones.length == 0 || observaciones == null ? 'Ninguna' : observaciones
	);
	if (motivo != null) {
		$(`${container} .motivo`).html(motivo);
		$(`${container} .tr_motivo`).show('fast');
	} else {
		$(`${container} .tr_motivo`).css('display', 'none');
	}
	if (observaciones_contrato != null) {
		$(`${container} .tr_observaciones_contrato`).show('fast');
	} else {
		$(`${container} .tr_observaciones_contrato`).css('display', 'none');
	}
	if (fecha_inicio_contrato != null) {
		$(`${container} .fecha_inicio_contrato`).html(fecha_inicio_contrato);
		$(`${container} .fecha_fin_contrado`).html(fecha_fin_contrado);
		$(`${container} .tr_fechas`).show('fast');
	} else {
		$(`${container} .tr_fechas`).css('display', 'none');
	}
	if (id_cargo_actual_postulante != null && (id_tipo == 'Tip_Cam' || id_tipo == 'Tip_Cam_Plan')) {
		$(`${container} .dependencia_actual`).html(departamento);
		$(`${container} .cargo_actual`).html(cargo);
		$(`${container} .tr_actuales`).show('fast');
	} else {
		$(`${container} .tr_actuales`).css('display', 'none');
	}
	if (id_tipo != 'Tip_Cam_Plan') $(`.tr_nuevos`).show('fast');
	else $(`.tr_nuevos`).css('display', 'none');

	$(`${container} .estado`).html(estado_solicitud);
	if ((id_tipo == 'Tip_Cam' || id_tipo == 'Tip_Cam_Plan') && !add) {
		$('#btn_ver_actual').off('click');
		$('#btn_ver_nuevo').off('click');
		if (hide) {
			$('#nav_admin_contratos li').removeClass('active');
			$('#nav_admin_contratos li a span').removeClass('fa-folder-open').addClass('fa-folder');
			$('#btn_ver_nuevo').addClass('active');
			$('#btn_ver_nuevo span').addClass('fa-folder-open');
		}
		let buscar = id_postulacion == null || id_postulacion.length == 0 ? 0 : id_postulacion;
		let data_nueva = data;
		let data_actual = await buscar_ultima_postulacion(buscar, 2);
		$('#msj_tipo_cambio_sol').show(`fast`);
		$('#btn_ver_actual').click(() => {
			if (data_actual.length != 0) {
				$('#nav_admin_contratos li').removeClass('active');
				$('#nav_admin_contratos li a span').removeClass('fa-folder-open').addClass('fa-folder');
				$('#btn_ver_actual').addClass('active');
				$('#btn_ver_actual span').addClass('fa-folder-open');
				ver_detalle_postulante_solicitud(
					data_actual,
					'#tabla_detalle_postulante',
					'#modal_detalle_postulante',
					tipo_sol,
					false,
					true
				);
				MensajeConClase('', 'success', 'Datos Cargados.!');
			} else {
				MensajeConClase(
					'El postulante seleccionado no registra un proceso de contratación  en AGIL, la información del contrato actual se encuentra en el indice de DATOS ACTUALES.',
					'info',
					'Oops.!'
				);
			}
		});
		$('#btn_ver_nuevo').click(() => {
			$('#nav_admin_contratos li').removeClass('active');
			$('#nav_admin_contratos li a span').removeClass('fa-folder-open').addClass('fa-folder');
			$('#btn_ver_nuevo').addClass('active');
			$('#btn_ver_nuevo span').addClass('fa-folder-open');
			ver_detalle_postulante_solicitud(
				data_nueva,
				'#tabla_detalle_postulante',
				'#modal_detalle_postulante',
				tipo_sol,
				false
			);
			MensajeConClase('', 'success', 'Datos Cargados.!');
		});
	} else if (hide) $('#msj_tipo_cambio_sol').hide(`fast`);
	listar_estados_csep(id);
	pintar_comentarios_generales(id, '#panel_comentarios_generales', 'Comentarios a este postulante', 'postulantes');
	id_postulante_sele = id;
	$(modal).modal();
};
//Aqui Listar Solicitudes
const listar_solicitudes = (id = '') => {
	let filtros = new FormData(document.getElementById('form_filtro'));
	filtros.append('id', id);
	consulta_ajax(`${ruta}listar_solicitudes`, formDataToJson(filtros), ({ data, filter }) => {
		filter ? $('.mensaje-filtro').show() : $('.mensaje-filtro').hide();
		$(`#tabla_solicitudes tbody`)
			.off('click', 'tr td:nth-of-type(1)')
			.off('click', 'tr')
			.off('click', 'tr span.aprobar')
			.off('click', 'tr span.negar')
			.off('click', 'tr span.revisar')
			.off('click', 'tr span.cancelar')
			.off('click', 'tr span.contabilizar')
			.off('click', 'tr span.desembolsar')
			.off('click', 'tr span.vistom')
			.off('click', 'tr span.vistob')
			.off('click', 'tr span.vistome')
			.off('click', 'tr span.vistobe')
			.off('click', 'tr span.procesar')
			.off('click', 'tr span.modificar')
			.off('click', 'tr span.add_candidato')
			.off('click', 'tr span.cruce')
			.off('click', 'tr span.finalizar')
			.off('click', 'tr span.agregar')
			.off('dblclick', 'tr');
		const myTable = $('#tabla_solicitudes').DataTable({
			destroy: true,
			processing: true,
			select: true,
			data,
			columns: [
				{ data: 'ver' },
				{
					render: (data, type, { id_tipo_solicitud, tipo_solicitud, nombre_vacante, nombre_cargo, especificaciones }, meta) => {
						if (id_tipo_solicitud === 'Hum_Sele') return `${tipo_solicitud} - ${nombre_vacante}`;
						else if (id_tipo_solicitud === 'Hum_Admi')
							return `${tipo_solicitud} - ${nombre_cargo}`;
						else if (id_tipo_solicitud === 'Hum_Cir' && especificaciones === 'ops')
							return `${tipo_solicitud} - OPS`
						return tipo_solicitud;
					}
				},
				{ data: 'solicitante' },
				{ data: 'fecha_registro' },
				{ data: 'state' },
				{ data: 'gestion' }
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: get_botones()
		});

		$('#tabla_solicitudes tbody').on('click', 'tr', function () {
			info_solicitud = myTable.row(this).data();
			let { id, solicitante, correo } = info_solicitud;
			info_persona.nombre = solicitante;
			info_persona.correo = correo;
			id_solicitud = id;
			$('#tabla_solicitudes tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_solicitudes tbody').on('dblclick', 'tr', function () {
			let data = myTable.row(this).data();
			configurar_modal_detalle_sol(data);
			$('#btnaprobar').html('');
		});

		$('#tabla_solicitudes tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let data = myTable.row($(this).parent()).data();
			info_solicitud = myTable.row(this).data();
			id_solicitud = info_solicitud.id;
			configurar_modal_detalle_sol(data);
			$('#btnaprobar').html('');
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.revisar', function () {
			const rowData = myTable.row($(this).parent()).data();
			const {
				id_tipo_solicitud,
				id,
				id_departamento,
				id_estado_solicitud,
				tipo_cargo,
				correo,
				tipo_solicitud,
				solicitante,
				especificaciones
			} = myTable.row($(this).parent()).data();

			switch (id_tipo_solicitud) {
				case 'Hum_Pres':
					if (pen_sal != []) traer_pension_salud();
					id_solicitud = id;
					$('#tabla_descuentos').DataTable({
						destroy: true,
						searching: false,
						processing: true,
						language: get_idioma(),
						dom: 'Bfrtip',
						buttons: []
					});
					revisar_prestamo();
					$('#btnaprobar').html('');
					break;
				case 'Hum_Apre':
				case 'Hum_Admi':
					mod_vacante = 1;
					$('.texto_accion').html('Aprobar');
					id_solicitud = id;
					get_detalle_vacante(
						id,
						async ({ vacante }) => {
							const { id_departamento, cargo_id, plan_trabajo } = vacante;
							const dptos = await get_departamentos(tipo_cargo);
							pintar_datos_combo(dptos, '.cbxdependencias', 'Seleccione Departamento', id_departamento);
							const cargos = await listar_cargos(2);
							pintar_datos_combo(cargos, '.cbxcargos', 'Seleccione Cargo', cargo_id);
							$("#form_revisar_requisicion textarea[name='plan_trabajo']").val(plan_trabajo);
							$("#form_revisar_requisicion #fingreso").addClass('oculto');
							$("#form_revisar_requisicion #btn_materias").addClass('oculto');
							$('#form_revisar_requisicion input[name=fecha_ingreso]').removeAttr('required');
							if (id_tipo_solicitud === 'Hum_Admi') {
								$("#form_revisar_requisicion .rev_final").removeClass('oculto');
								$('#form_revisar_requisicion select[name=tipo_contrato]').prop('required', true);
								$('#form_revisar_requisicion input[name=duracion_contrato]').prop('required', true);
							} else {
								$("#form_revisar_requisicion .rev_final").addClass('oculto');
								$('#form_revisar_requisicion select[name=tipo_contrato]').removeAttr('required');
								$('#form_revisar_requisicion input[name=duracion_contrato]').removeAttr('required');
							}
						},
						1
					);
					$('#modal_revisar_requisicion').modal('show');
					break;
				case 'Hum_Posg':
					switch (id_estado_solicitud) {
						case 'Tal_Env':
							msj_confirmacion('¿ Estas Seguro ?', `¿Seguro que desea avalar este perfil?`, () =>
								avalar_perfil(id, id_departamento)
							);
							break;
						case 'Tal_Pro':
							configurar_modal_detalle_sol(rowData);
							$('#tr_ordensap').show();
							$('#btnaprobar').html('');
							$('#frm_terminar_requisicion_posgrado').get(0).reset();
							$('#modal_terminar_requisicion_posgrado').modal();
							break;
					}
					break;
				case 'Hum_Prec':
					mod_vacante = 1;
					$('.texto_accion').html('Aprobar');
					id_solicitud = id;
					get_detalle_vacante(id, async ({ vacante }) => {
						const { id_departamento, cargo_id, plan_trabajo } = vacante;
						const dptos = await get_departamentos(tipo_cargo);
						pintar_datos_combo(dptos, '.cbxdependencias', 'Seleccione Departamento', id_departamento);
						const cargos = await listar_cargos();
						pintar_datos_combo(cargos, '.cbxcargos', 'Seleccione Cargo', cargo_id);
						$("#form_revisar_requisicion textarea[name='plan_trabajo']").val(plan_trabajo);
						if (id_estado_solicitud === 'Tal_Pro') {
							$("#form_revisar_requisicion #fingreso").removeClass('oculto');
							$('#form_revisar_requisicion input[name=fecha_ingreso]').prop('required', true)
						} else {
							$("#form_revisar_requisicion #fingreso").addClass('oculto');
							$('#form_revisar_requisicion input[name=fecha_ingreso]').prop('required', false)
						}
						$("#form_revisar_requisicion .rev_final").addClass('oculto');
						$('#form_revisar_requisicion select[name=tipo_contrato]').removeAttr('required');
						$('#form_revisar_requisicion input[name=duracion_contrato]').removeAttr('required');
					});
					$('#modal_revisar_requisicion').modal('show');
					break;
				case 'Hum_Cert':
					$('#file_certificado_cir').show();
					$('#modal_adjuntar_certificado').modal();
					$('#form_adjuntar_certificado').get(0).reset();
					break;
				case 'Hum_Cir':
					// if (!especificaciones) $('#file_certificado_cir').css('display', 'none');
					$('#file_certificado_cir').show();
					$('#modal_adjuntar_certificado').modal();
					$('#form_adjuntar_certificado').get(0).reset();
					break;
				case 'Hum_Afi_Arl':
				case 'Hum_Cob_Arl':
					const data = {
						id,
						type: id_tipo_solicitud,
						nextState: 'Tal_Pro',
						success: 'Solicitud Procesada Exitosamente!'
					};
					data.callback = () => {
						swal.close();
						const titulo = `Solicitud de ${tipo_solicitud} en Trámite`;
						const nombre = 'Funcionario Talento Humano';
						const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
						const mensaje = `
							<p>Se le notifica que su solicitud de ${tipo_solicitud}se encuentra en trámite, recuerda que hasta en un máximo de 30 días calendario  recibiremos respuesta de tu EPS donde nos indicara el estado de tu traslado  y si es efectivo la  fecha de inicio de cobertura.</p>
							<p>Para mas información ingrese a: ${ser}</p>
						`;
						enviar_correo_personalizado(
							'th',
							mensaje,
							correo,
							nombre,
							'AGIL Talento Humano CUC',
							titulo,
							'Par_TH',
							2
						);
					};
					msj_confirmacion('¿ Tramitar Solicitud ?', `¿Seguro que desea tramitar la solicitud?`, () =>
						gestionar_solicitud(data)
					);
					break;
				case 'Hum_Vac':
				case 'Hum_Lic':
					$('#modal_vb_ausentismo').modal();
					break;
				//Revisar eps
				case 'Hum_Tras_Afp':
					const datas = {
						id,
						type: id_tipo_solicitud,
						nextState: 'Tal_Pro',
						success: 'Solicitud Procesada Exitosamente!'
					};
					datas.callback = () => {
						swal.close();
						const titulo = `Solicitud de ${tipo_solicitud} en Trámite`;
						const nombre = 'Sr. Colaborador';
						const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
						const mensaje = `
						<p>Se le notifica que su solicitud de ${tipo_solicitud} se encuentra procesada, muchas gracias por su notificación de traslado.</p>
						<p>Para mas información ingrese a: ${ser}</p>
						`;
						enviar_correo_personalizado(
							'th',
							mensaje,
							correo,
							nombre,
							'AGIL Talento Humano CUC',
							titulo,
							'Par_TH',
							2
						);
					};
					msj_confirmacion('¿ Tramitar Solicitud ?', `¿Seguro que desea tramitar la solicitud?`, () =>
						gestionar_solicitud(datas)
					);
					break;
				case "Hum_Entr_Cargo":
					const dto = {
						id,
						type: id_tipo_solicitud,
						nextState: 'Tal_Pro',
						success: 'Solicitud Procesada Exitosamente!'
					};
					dto.callback = () => {
						swal.close();
						const titulo = `Solicitud de ${tipo_solicitud} en Trámite`;
						const nombre = 'Colaborador';
						const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
						const mensaje = `
						<p>Se le notifica que su solicitud de ${tipo_solicitud} ha pasado a la espera de los vistos buenos correspondientes.</p>
						<p>Para mas información ingrese a: ${ser}</p>
						`;
						enviar_correo_personalizado(
							'th',
							mensaje,
							correo,
							nombre,
							'AGIL Talento Humano CUC',
							titulo,
							'Par_TH',
							2
						);
					};
					msj_confirmacion('¿ Tramitar Solicitud ?', `¿Seguro que desea tramitar la solicitud?`, () =>
						gestionar_solicitud(dto)
					);
					break;
				case "Hum_Inc_Caja":
					const datos = {
						id,
						type: id_tipo_solicitud,
						nextState: 'Tal_Pro',
						success: 'Solicitud Procesada Exitosamente!'
					};
					datos.callback = () => {
						swal.close();
						const titulo = `Solicitud de ${tipo_solicitud} en Trámite`;
						const nombre = 'Sr. Colaborador';
						const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
						const mensaje = `
						<p>Se le notifica que su solicitud de ${tipo_solicitud} se encuentra en trámite, recuerda que hasta en un máximo de 15 días calendario  recibiremos respuesta de tu caja de compensación donde nos indicara el estado de tu traslado  y si es efectivo la  fecha de inicio de cobertura.</p>
						<p>Para mas información ingrese a: ${ser}</p>
						`;
						enviar_correo_personalizado(
							'th',
							mensaje,
							correo,
							nombre,
							'AGIL Talento Humano CUC',
							titulo,
							'Par_TH',
							2
						);
					};
					msj_confirmacion('¿ Tramitar Solicitud ?', `¿Seguro que desea tramitar la solicitud?`, () =>
						gestionar_solicitud(datos)
					);
					break;
				case 'Hum_Inc_Eps':
				case 'Hum_Cam_Eps':
					const dato = {
						id,
						type: id_tipo_solicitud,
						nextState: 'Tal_Pro',
						success: 'Solicitud Procesada Exitosamente!'
					};
					dato.callback = () => {
						swal.close();
						const titulo = `Solicitud de ${tipo_solicitud} en Trámite`;
						const nombre = 'Sr. Colaborador';
						const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
						const mensaje = `
							<p>Se le notifica que su solicitud de ${tipo_solicitud} se encuentra en trámite, recuerda que hasta en un máximo de 30 días calendario  recibiremos respuesta de tu EPS donde nos indicara el estado de tu traslado  y si es efectivo la  fecha de inicio de cobertura.</p>
							<p>Para mas información ingrese a: ${ser}</p>
							`;
						enviar_correo_personalizado(
							'th',
							mensaje,
							correo,
							nombre,
							'AGIL Talento Humano CUC',
							titulo,
							'Par_TH',
							2
						);
					};
					msj_confirmacion('¿ Tramitar Solicitud ?', `¿Seguro que desea tramitar la solicitud?`, () =>
						gestionar_solicitud(dato)
					);
					break;
			}
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.cancelar', function () {
			const { id, id_tipo_solicitud, correo, solicitante, tipo_solicitud, id_departamento } = myTable.row($(this).parent()).data();
			const data = {
				id,
				nextState: 'Tal_Can',
				success: 'Solicitud Cancelada Exitosamente!',
				type: id_tipo_solicitud
			};
			if (id_tipo_solicitud === 'Hum_Posg') {
				data.callback = () => {
					const titulo = 'Solicitud de requisición cancelada';
					const nombre = 'Funcionario';
					const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
					const mensaje = `
						<p>Se ha cancelado exitosamente una solicitud de requisición de posgrado.</p>
						<p>Para mas información ingrese a: ${ser}</p>
					`;
					get_usuarios_a_notificar_estado_posgrado(data.nextState, id_departamento).then((correos) => {
						enviar_correo_personalizado(
							'th',
							mensaje,
							correos,
							nombre,
							'AGIL Talento Humano CUC',
							titulo,
							'Par_TH',
							3
						);
					});
				};
			}
			if (id_tipo_solicitud === 'Hum_Vac' || id_tipo_solicitud === 'Hum_Lic') {
				data.callback = () => {
					const titulo = `Solicitud de ${tipo_solicitud} cancelada`;
					const nombre = 'Funcionario Talento Humano';
					const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
					const mensaje = `
						<p>Se ha cancelado exitosamente su solicitud de ${tipo_solicitud}.</p>
						<p>Para mas información ingrese a: ${ser}</p>
					`;
					enviar_correo_personalizado(
						'th',
						mensaje,
						correo,
						solicitante,
						'AGIL Talento Humano CUC',
						titulo,
						'Par_TH',
						1
					);
				};
			}
			msj_confirmacion('¿ Estas Seguro de Cancelar  ?', `La Solicitud de ${tipo_solicitud} será cancelada.`, () =>
				gestionar_solicitud(data)
			);
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.vistom', async function () {
			const { id, id_tipo_solicitud, id_departamento, correo, solicitante } = myTable.row($(this).parent()).data();
			let tipo_solicitud;
			let data = {
				id,
				type: id_tipo_solicitud,
				nextState: id_tipo_solicitud === 'Hum_Entr_Cargo' ? 'Tal_vm_Jefe_ecargo' : 'Tal_Mal',
				success: 'Solicitud Negada Exitosamente!'
			};

			switch (id_tipo_solicitud) {
				case 'Hum_Cert':
					tipo_solicitud = 'Certificado Laboral';
					break;
				case 'Hum_Pres':
					tipo_solicitud = 'Prestamo';
					break;
				case 'Hum_Admi':
					tipo_solicitud = 'Requisición Administrativos';
					break;
				case 'Hum_Posg':
					tipo_solicitud = 'Requisición Posgrado';
					break;
				case 'Hum_Apre':
					tipo_solicitud = 'Requisición Aprendices';
					break;
				case 'Hum_Prec':
					tipo_solicitud = 'Pregrado - Requisición';
					break;
				case 'Hum_Cir':
					tipo_solicitud = 'Certificado de ingresos y retenciones';
					break;
				case 'Hum_Vac':
					tipo_solicitud = 'Vacaciones';
					break;
				case 'Hum_Lic':
					tipo_solicitud = 'Licencia';
					break;
				case 'Hum_Cam_Eps':
					tipo_solicitud = 'Cambio de EPS';
					break;
				case 'Hum_Inc_Eps':
					tipo_solicitud = 'Inclusión beneficiario de EPS';
					break;
				case "Hum_Inc_Caja":
					tipo_solicitud = 'Inclusión beneficiario Caja de Compensacion'
				case "Hum_Tras_Afp":
					tipo_solicitud = 'Notificacion de traslados de EPS, AFP y/o AFC'
					break;
				case 'Hum_Entr_Cargo':
					tipo_solicitud = 'Entrega de cargo'
					break;
				default:
					tipo_solicitud = '';
					break;
			}
				msj_confirmacion_input(
					`Rechazar ${tipo_solicitud}`,
					'Por favor digite motivo de rechazo de la solicitud',
					'Motivo de Rechazo',
					(msj) => {
						data.msj = msj;
						const callback = async () => {
							const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
							let p = `Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha sido Negada por el jefe inmediato.`;
							let asunto = `Solicitud de ${tipo_solicitud} Negada`;
	
							const mensaje = `
								<p>${p}</p>
								<p><strong>Motivo de rechazo:</strong> "${msj}"</p>
								<p>Más información en: ${ser}</p>
							`;
							MensajeConClase('Solicitud Negada exitosamente', 'success', 'Proceso Exitoso!');
							if (id_tipo_solicitud === 'Hum_Posg') {
								if ((data.nextState === 'Tal_Neg' || data.nextState === 'Tal_Can' || data.nextState === 'Tal_Ter')) {
									const correos = await get_usuarios_a_notificar_estado_posgrado(data.nextState, id_departamento)
									// : await get_usuarios_a_notificar(id_tipo_solicitud, data.nextState);
									correos.push({ persona: solicitante, correo });
									enviar_correo_personalizado(
										'th',
										mensaje,
										correos,
										'',
										'AGIL Talento Humano CUC',
										asunto,
										'Par_TH',
										1
									);
								}
							}else {
								enviar_correo_personalizado(
									'th',
									mensaje,
									correo,
									solicitante,
									'AGIL Talento Humano CUC',
									asunto,
									'Par_TH',
									1
								);
	
							};
						}
						gestionar_solicitud(data, callback);
					}
	
				);
			
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.vistome', async function () {
			const { id, id_tipo_solicitud, id_departamento, correo, solicitante } = myTable.row($(this).parent()).data();
			let tipo_solicitud;
			let data = {
				id,
				type: id_tipo_solicitud,
				nextState:  'Tal_vm_Jefe_ecargo1',
				success: 'Solicitud Negada Exitosamente!'
			};

			switch (id_tipo_solicitud) {
				case 'Hum_Entr_Cargo':
					tipo_solicitud = 'Entrega de cargo'
					break;
				default:
					tipo_solicitud = '';
					break;
			}
				msj_confirmacion_input(
					`Rechazar ${tipo_solicitud}`,
					'Por favor digite motivo de rechazo de la solicitud',
					'Motivo de Rechazo',
					(msj) => {
						data.msj = msj;
						const callback = async () => {
							const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
							let p = `Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha sido Negada por el jefe inmediato.`;
							let asunto = `Solicitud de ${tipo_solicitud} Negada`;
	
							const mensaje = `
								<p>${p}</p>
								<p><strong>Motivo de rechazo:</strong> "${msj}"</p>
								<p>Más información en: ${ser}</p>
							`;
							MensajeConClase('Solicitud Negada exitosamente', 'success', 'Proceso Exitoso!');
								enviar_correo_personalizado(
									'th',
									mensaje,
									correo,
									solicitante,
									'AGIL Talento Humano CUC',
									asunto,
									'Par_TH',
									1
								);
						}
						gestionar_solicitud(data, callback);
					}
	
				);
			
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.contabilizar', () => {
			$('#modal_gestionar_solicitud').modal('show');
			tipo_proceso = 2;
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.desembolsar', function () {
			const { id, id_tipo_solicitud } = myTable.row($(this).parent()).data();
			const data = {
				id,
				type: id_tipo_solicitud,
				nextState: 'Tal_Des',
				success: 'Solicitud Desembolzada Exitosamente!'
			};
			msj_confirmacion('¿ Prestamo Desembolzado ?', '', () => gestionar_solicitud(data));
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.vistob', function () {
			const { id, id_tipo_solicitud, id_departamento, correo, solicitante } = myTable.row($(this).parent()).data();
			let tipo_solicitud;
			let sw = false;
			let next = id_tipo_solicitud === 'Hum_Vac' || id_tipo_solicitud === 'Hum_Lic' ? 'Tal_vb_Jefe' : id_tipo_solicitud === 'Hum_Entr_Cargo' ? 'Tal_vb_Jefe_ecargo' : 'Tal_Vis';
			const data = {
				id,
				type: id_tipo_solicitud,
				nextState: next,
				success: 'Solicitud Aprobada Exitosamente!'
			};
			switch (id_tipo_solicitud) {
				case 'Hum_Cert':
					tipo_solicitud = 'Certificado Laboral';
					break;
				case 'Hum_Pres':
					tipo_solicitud = 'Prestamo';
					break;
				case 'Hum_Admi':
					tipo_solicitud = 'Requisición Administrativos';
					break;
				case 'Hum_Posg':
					tipo_solicitud = 'Requisición Posgrado';
					break;
				case 'Hum_Apre':
					tipo_solicitud = 'Requisición Aprendices';
					break;
				case 'Hum_Prec':
					tipo_solicitud = 'Pregrado - Requisición';
					break;
				case 'Hum_Cir':
					tipo_solicitud = 'Certificado de ingresos y retenciones';
					break;
				case 'Hum_Vac':
					tipo_solicitud = 'Vacaciones'; 
					sw = true;
					break;
				case 'Hum_Lic':
					tipo_solicitud = 'Licencia';
					sw = true;
					break;
				case 'Hum_Entr_Cargo':
					tipo_solicitud = 'Entrega de Cargo';
					break;
				default:
					tipo_solicitud = '';
					break;
			}
			const callback = async () => {
				const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
				let p = sw ? `Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha recibido visto bueno por parte de su jefe inmediato. Esta solicitud fue redireccionada al área de Talento Humano para su aprobación o desaprobación.`
					: `Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha sido aprobada por el jefe inmediato`;
				let asunto = sw ? `Solicitud de ${tipo_solicitud} en Visto Bueno` : `Solicitud de ${tipo_solicitud} Aprobada`;

				const mensaje = `
							<p>${p}</p>
							<p>Más información en: ${ser}</p>
						`;
				MensajeConClase('Solicitud Aprobada exitosamente', 'success', 'Proceso Exitoso!');

				enviar_correo_personalizado(
					'th',
					mensaje,
					correo,
					solicitante,
					'AGIL Talento Humano CUC',
					asunto,
					'Par_TH',
					1
				);
				if (sw) notificar_vb('Tal_vb_Jefe');
			};
			if (id_tipo_solicitud== 'Hum_Entr_Cargo') {
				msj_confirmacion('¿ Aprobar solicitud ? ', 'La solicitud sera aprobada. \nRecuerde que a dar el Visto bueno, debe verificar que el Colaborador haya diligenciado su entrega de puesto de Trabajo.', () => gestionar_solicitud(data, callback));
			}else{
				msj_confirmacion('¿ Aprobar solicitud ?', 'La solicitud sera aprobada', () => gestionar_solicitud(data, callback));

			}
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.vistobe', function () {
			const { id, id_tipo_solicitud, id_departamento, correo, solicitante } = myTable.row($(this).parent()).data();
			let tipo_solicitud;
			let sw = false;
			let next =  id_tipo_solicitud === 'Hum_Entr_Cargo' ? 'Tal_vb_Jefe_ecargo1' :  '';
			const data = {
				id,
				type: id_tipo_solicitud,
				nextState: next,
				success: 'Solicitud Aprobada Exitosamente!'
			};
			switch (id_tipo_solicitud) {
				case 'Hum_Entr_Cargo':
					tipo_solicitud = 'Entrega de Cargo';
					sw = true;
					break;
				default:
					tipo_solicitud = '';
					break;
			}
			const callback = async () => {
				const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
				let p = sw ? `Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha recibido visto bueno por parte de su jefe inmediato. Esta solicitud fue redireccionada al área de Talento Humano para su aprobación o desaprobación.`
					: `Se le notifica que los documentos enviado en su solicitud de ${tipo_solicitud} han sido aprobado`;
				let asunto = sw ? `Solicitud de ${tipo_solicitud} en Visto Bueno` : `Documentos de la solicitud de ${tipo_solicitud} han sido Aprobados`;

				const mensaje = `
							<p>${p}</p>
							<p>Más información en: ${ser}</p>
						`;
				MensajeConClase('Solicitud Aprobada exitosamente', 'success', 'Proceso Exitoso!');

				enviar_correo_personalizado(
					'th',
					mensaje,
					correo,
					solicitante,
					'AGIL Talento Humano CUC',
					asunto,
					'Par_TH',
					1
				);
			};
			if (id_tipo_solicitud== 'Hum_Entr_Cargo') {
				msj_confirmacion('¿ Aprobar solicitud ? ', 'La solicitud sera aprobada. \nRecuerde que a dar el Visto bueno, debe verificar que el Colaborador haya diligenciado su entrega de puesto de Trabajo.', () => gestionar_solicitud(data, callback));
			}else{
				msj_confirmacion('¿ Aprobar solicitud ?', 'La solicitud sera aprobada', () => gestionar_solicitud(data, callback));

			}
		});
		$('#tabla_solicitudes tbody').on('click', 'tr span.cruce', function () {
			const { id, id_tipo_solicitud } = myTable.row($(this).parent()).data();
			const data = {
				id,
				type: id_tipo_solicitud,
				nextState: 'Tal_Apr',
				success: 'Matrícula Cruzada Exitosamente!'
			};
			msj_confirmacion('¿ Matricula Cruzada ?', '', () => gestionar_solicitud(data));
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.aprobar', async function (e) {
			const data = myTable.row($(this).parent()).data();
			const { id_estado_solicitud: estado, id, tipo_cargo, id_tipo_solicitud: tipo, departamento_id } = data;
			id_solicitud = id;
			// if (tipo === 'Hum_Prec' && tipo_cargo === 'Vac_Aca') {
			if (tipo === 'Hum_Prec') {
				$('#modal_vb_pedagogico').modal();
			} else if (tipo === 'Hum_Afi_Arl' || tipo === 'Hum_Cob_Arl') {
				$('#form_vb_arl').get(0).reset();
				$('#modal_vb_arl').modal();
			} else if (tipo === 'Hum_Cam_Eps' || tipo === 'Hum_Inc_Eps' || tipo === 'Hum_Inc_Caja' || tipo === 'Hum_Tras_Afp') {
				$('#form_entidades').get(0).reset();
				$('#modal_entidades').modal();
			  } else if (tipo === 'Hum_Entr_Cargo'){
				listar_vb_ecargo(id_solicitud);
				$('#modal_vb_ecargo').modal();
			 } else {
				configurar_modal_detalle_sol(data);
				campos_modificar_prestamo();
				if (estado == 'Tal_Vis' || estado == 'Tal_Mal') boton_aprobar();
			}
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.modificar', function () {
			const data = myTable.row($(this).parent()).data();
			switch (data.id_tipo_solicitud) {
				case 'Hum_Posg':
					mod_vacante = 1;
					modal_modificar_requisicion_posgrado(data);
					break;
				case 'Hum_Prec':
				case 'Hum_Admi':
				case 'Hum_Apre':
					mod_vacante = 1;
					$('#form_vacante span.texto_accion').html('Modificar');
					id_solicitud = data.id;
					$('.adicional_info').hide('fast');
					$('#botones_modificar, .no-revisar').show();
					get_detalle_vacante(data.id, mostrar_info_solicitud);
					$('#modal_solicitud_vacante').modal('show');
					break;
				case 'Hum_Sele':
					mod_vacante = 1;
					$('#div_requisicion').hide('fast');
					cargar_info_solicitud_seleccion(data);
					break;
				case 'Hum_Afi_Arl':
				case 'Hum_Cob_Arl':
					id_solicitud = data.id;
					mostrar_solicitud_arl(data);
					break;
			}
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.finalizar', function () {
			//probando
			(msj) => {
				data.msj = msj;
			}
			const { id, id_tipo_solicitud, id_departamento, correo, solicitante } = myTable.row($(this).parent()).data();
			let tipo_solicitud;
			id_solicitud=id;
			const data = {
				id,
				type: id_tipo_solicitud,
				nextState: 'Tal_Ter',
				success: 'Solicitud Finalizada Exitosamente!'
			};

			switch (id_tipo_solicitud) {
				case 'Hum_Cert':
					tipo_solicitud = 'Certificado Laboral';
					break;
				case 'Hum_Pres':
					tipo_solicitud = 'Prestamo';
					break;
				case 'Hum_Admi':
					tipo_solicitud = 'Requisición Administrativos';
					break;
				case 'Hum_Posg':
					tipo_solicitud = 'Requisición Posgrado';
					break;
				case 'Hum_Apre':
					tipo_solicitud = 'Requisición Aprendices';
					break;
				case 'Hum_Prec':
					tipo_solicitud = 'Pregrado - Requisición';
					break;
				case 'Hum_Cir':
					tipo_solicitud = 'Certificado de ingresos y retenciones';
					break;
				case 'Hum_Vac':
					tipo_solicitud = 'Vacaciones';
					break;
				case 'Hum_Lic':
					tipo_solicitud = 'Licencia';
					break;
				case 'Hum_Entr_Cargo':
					tipo_solicitud = 'Entrega de Cargo';
					break;
				default:
					tipo_solicitud = '';
					break;
			}

			const callback = async () => {
				const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
				let p = `Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha Finalizado`;
				let asunto = `Solicitud de ${tipo_solicitud} Finalizada`;

				const mensaje = `
							<p>${p}</p>
							<p>Más información en: ${ser}</p>
						`;
				MensajeConClase('Solicitud finalizada exitosamente', 'success', 'Proceso Exitoso!');

				//  await get_usuarios_a_notificar(id_tipo_solicitud, data.nextState);

				// correos.push({ persona: solicitante, correo });

				enviar_correo_personalizado(
					'th',
					mensaje,
					correo,
					solicitante,
					'AGIL Talento Humano CUC',
					asunto,
					'Par_TH',
					1
				);

			};
			msj_confirmacion('¿ Finalizar Solicitud ?', 'Tener en cuenta que no podrá revertir esta acción!.', () => gestionar_solicitud(data, callback));
			notificar_finalizada('Hum_Entr_Cargo', "Tal_Ter", id_solicitud);



		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.negar', function () {
			const { id, id_tipo_solicitud, id_departamento, correo, solicitante } = myTable.row($(this).parent()).data();
			let tipo_solicitud;
			let data = {
				id,
				type: id_tipo_solicitud,
				nextState: 'Tal_Can',
				success: 'Solicitud Negada Exitosamente!'
			};

			switch (id_tipo_solicitud) {
				case 'Hum_Cert':
					tipo_solicitud = 'Certificado Laboral';
					break;
				case 'Hum_Pres':
					tipo_solicitud = 'Prestamo';
					break;
				case 'Hum_Admi':
					tipo_solicitud = 'Requisición Administrativos';
					break;
				case 'Hum_Posg':
					tipo_solicitud = 'Requisición Posgrado';
					break;
				case 'Hum_Apre':
					tipo_solicitud = 'Requisición Aprendices';
				case 'Hum_Prec':
					tipo_solicitud = 'Pregrado - Requisición';
					break;
				case 'Hum_Cir':
					tipo_solicitud = 'Certificado de ingresos y retenciones';
					break;
				case 'Hum_Vac':
					tipo_solicitud = 'Vacaciones';
					break;
				case 'Hum_Lic':
					tipo_solicitud = 'Licencia';
					break;
				default:
					tipo_solicitud = '';
					break;
			}

			msj_confirmacion_input(
				`Rechazar ${tipo_solicitud}`,
				'Por favor digite motivo de rechazo de la solicitud',
				'Motivo de Rechazo',
				(msj) => {
					data.msj = msj;
					const callback = async () => {
						const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
						let p = `Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha sido negada.`;
						let asunto = `Solicitud de ${tipo_solicitud} negada`;

						const mensaje = `
							<p>${p}</p>
							<p><strong>Motivo de rechazo:</strong> "${msj}"</p>
							<p>Más información en: ${ser}</p>
						`;
						MensajeConClase('Solicitud negada exitosamente', 'success', 'Proceso Exitoso!');

						enviar_correo_personalizado(
							'th',
							mensaje,
							correo,
							solicitante,
							'AGIL Talento Humano CUC',
							asunto,
							'Par_TH',
							1
						);

					};
					gestionar_solicitud(data, callback);
				}
			);
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.add_candidato', function () {
			const { id, tipo_cargo_id } = myTable.row($(this).parent()).data();
			info_solicitud = myTable.row(this).data();
			validar_permisos_gestion_candidato(id, boton_agregar_candidato);
			listar_candidatos(id, tipo_cargo_id);
			$('#modal_candidatos').modal();
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.agregar', function () {
			const data = myTable.row($(this).parent()).data();
			id_solicitud = data.id;
			switch (data.id_tipo_solicitud) {
				case 'Hum_Entr_Cargo':
					id_solicitud = data.id;
					modal_modificar_entrega_cargo(data);
				break;
			}
		});
		const validar_permisos_gestion_candidato = (solicitud, callback) => {
			let sw = false;
			consulta_ajax(`${ruta}validar_permisos_gestion_candidato`, { solicitud }, ({ cerrada, admin, procesos }) => {
				if (!cerrada) sw = true;
				else {
					procesos.map(({ actividad, estado }) => {
						if (actividad === 'Hum_Sele' && (estado === 'Tal_Env' || estado === 'Tal_Pro') && !cerrada) sw = true;
					});
				}
				const { id_estado_solicitud } = $('#tabla_solicitudes').DataTable().row('.warning').data();
				sw && id_estado_solicitud != 'Tal_Ter' ? callback() : $('#botones_candidatos').html('');
			});
		};

		const boton_agregar_candidato = () => {
			$('#botones_candidatos').html(
				'<span id="btn_add_candidato" class="btn btn-default"><span class="fa fa-user-plus red"></span> Agregar</span>'
			);
			$('#btn_add_candidato').click(() => {
				callbak_activo = (resp) => msj_confirmacion('¿ Agregar Candidato ?', '', () => agregar_candidato(resp));
				buscar_postulante('', callbak_activo);
				$('#modal_buscar_postulante').modal();
			});
		};

		const cargar_info_solicitud_seleccion = ({ id, id_tipo_solicitud, responsable, responsable_id, id_requisicion, id_jefe_inmediato, jefe_responsable }) => {
			$('#form_seleccion input[name=requisicion]').removeAttr('required');
			$('#modal_seleccion').modal();
			consulta_ajax(`${ruta}detalle_solicitud`, { id, tipo: id_tipo_solicitud }, async (resp) => {
				const {
					nombre_vacante,
					numero_vacantes,
					tipo_cargo_id,
					cargo_id,
					perfil,
					seleccion_id,
					departamento,
					departamento_id: dep
				} = resp;
				id_solicitud = { seleccion_id, id };
				$("#form_seleccion input[name='nombre_vacante']").val(nombre_vacante);
				$("#form_seleccion input[name='numero_vacantes']").val(numero_vacantes);
				$("#form_seleccion select[name='tipo_cargo']").val(tipo_cargo_id);
				$("#form_seleccion textarea[name='perfil']").val(perfil);
				$("#form_seleccion input[name='nombre_responsable']").val(responsable);
				$("#form_seleccion input[name='nombre_jefe_responsable']").val(jefe_responsable);
				$("#form_seleccion input[name='dependencia']").val(departamento);

				const tipo = tipo_cargo_id === 'Vac_Aca' || tipo_cargo_id === 'Vac_Pos' ? 1 : 2;

				const cargos = await listar_cargos(tipo);
				pintar_datos_combo(cargos, '#form_seleccion select[name=cargo]', 'Seleccione Cargo', cargo_id);
				id_persona = responsable_id;
				departamento_id = dep;
				requisicion_id = id_requisicion;
				id_persona_jefe = id_jefe_inmediato;
				$('.texto_seleccion').html('Modificar');
			});
		};

		const boton_aprobar = () => {
			$('#btnaprobar').html(
				'<button id="btn_aprobar_prestamo" type="submit" class="btn btn-danger active"><span class="fa fa-check-square-o"></span>Aprobar</button>'
			);
			$('#frmaprobar_prestamo').off('submit').submit((e) => {
				const { tipo_prestamo } = info_solicitud;
				let callback = () => {
					let data = {
						id: id_solicitud,
						type: 'Hum_Pres',
						success: 'Solicitud Aprobada Exitosamente'
					};
					if (tipo_prestamo === 'Pre_Lib') data.nextState = 'Tal_Apr';
					else if (tipo_prestamo === 'Pre_Cru') data.nextState = 'Tal_Cru';
					const valor = $('#txtmodificar_valor').val();
					const cuotas = $('#txtmodificar_cuotas').val();
					if (valor) data.valor = valor;
					if (cuotas) data.cuotas = cuotas;
					gestionar_solicitud(data, () => {
						$('#modal_detalle_solicitud_prestamo').modal('hide');
						$('#frmaprobar_prestamo').get(0).reset();
						$('#btnaprobar').html('');
					});
				};
				msj_confirmacion('¿ Prestamo Aprobado ?', '', () => callback());
				e.preventDefault();
			});
		};

		$('#tabla_solicitudes tbody').on('click', 'tr span.procesar', () => {
			tipo_proceso = 1;
			$('#modal_gestionar_solicitud').modal('show');
		});

		const revisar_prestamo = () => {
			$('#modal_revisar_prestamo').modal();
			limpiar_salario();
		};
	});

	const configurar_modal_detalle_sol = (data) => {
		$('#modificar_prestamo').html('');
		const { id_tipo_solicitud, id, tipo_cargo_id, usuario_registro, aux } = data;
		id_solicitud = id;
		id_persona = usuario_registro;
		switch (id_tipo_solicitud) {
			case 'Hum_Afi_Arl':
			case 'Hum_Cob_Arl':
				$('#modal_detalle_solicitud_arl').modal();
				detalle_solicitud_arl(data);
				break;
			case 'Hum_Cert':
				$('#modal_detalle_solicitud_certificado').modal();
				detalle_solicitud_certificado(data);
				break;
			case 'Hum_Cir':
				$('#modal_detalle_solicitud_certificado').modal();
				detalle_solicitud_certificado_ingresos(data);
				break;
			case 'Hum_Csep':
				tipo_activo = 'solicitud';
				listar_postulantes_csep(id);
				$('#modal_detalle_solicitud').modal();
				$(`#container_tabla_postulantes`).show('fast');
				break;
			case 'Hum_Pres':
				detalle_solicitud(data);
				$('#modal_detalle_solicitud_prestamo').modal();
				break;
			case 'Hum_Posg':
				detalle_vacante_posgrado(data);
				break;
			case 'Hum_Prec':
			case 'Hum_Admi':
			case 'Hum_Apre':
				detalle_vacante(data);
				break;
			case 'Hum_Sele':
				detalle_seleccion(data);
				listar_candidatos(id, tipo_cargo_id);
				break;
			case 'Hum_Vac':
				detalle_solicitud_ausentismo_vacaciones(data);
				break;
			case 'Hum_Lic':
				detalle_solicitud_ausentismo_licencia(data);
				break;
			case 'Hum_Cam_Eps':
				detalle_cambio_eps(data);
				break;
			case 'Hum_Inc_Eps':
				detalle_inc_ben(data);
			case "Hum_Inc_Caja":
				detalle_inc_ben(data);
				break;
			case "Hum_Tras_Afp":
				detalle_traslado_afp(data);
				break;
			case "Hum_Entr_Cargo":
				detalle_ecargo(data);
				break;
			default:
				$(`#container_tabla_postulantes`).css('display', 'none');
				break;
		}
	};

	const detalle_vacante_posgrado = (data) => {
		const { id, solicitante, state, fecha_registro, tipo_solicitud } = data;
		$('.info_solicitante').html(solicitante);
		$('.info_estado').html(state);
		$('.info_fecha').html(fecha_registro);
		$('.info_t_solicitud').html(tipo_solicitud);
		$('#tr_ordensap').css('display', 'none');
		$('#modal_detalle_vacante_posgrado').modal();
		consulta_ajax(`${ruta}detalle_requisicion_posgrado`, { id }, (resp) => {
			const {
				tipo_vacante,
				nombre_modulo,
				horas_modulo,
				numero_promocion,
				valor_hora,
				ciudad_origen,
				fecha_inicio,
				fecha_terminacion,
				observacion,
				candidato,
				reemplazado,
				departamento,
				documentos,
				tipo_programa,
				programa,
				estado,
				tipo_orden,
				codigo_sap,
				cargo
			} = resp;
			if (reemplazado) {
				$('#tr_reemplazo_pos').show('fast');
				$('.info_reemplazo').html(reemplazado);
			} else {
				$('#tr_reemplazo_pos').hide('fast');
				$('.info_reemplazo').html('');
			}
			if (documentos) {
				$('#boton_documentos').html(`
					<a class='btn btn-default' target="_blank" href="${Traer_Server()}${ruta_archivos_requisicion}${documentos}">
						<span class='fa fa-folder-open red'></span> Documentos
					</a>
				`);
			} else $('#boton_documentos').html('');
			if (estado === 'Tal_Ter') {
				$('#tr_tipo_orden').show();
				$('.tipo_orden').html(tipo_orden);
			} else {
				$('.tipo_orden').html('');
				$('#tr_tipo_orden').css('display', 'none');
			}
			$('.info_t_vacante').html(tipo_vacante);
			$('.info_orden_sap').html(codigo_sap);
			$('.nombre_candidato').html(candidato);
			$('.nombre_departamento').html(departamento);
			$('.nombre_modulo').html(nombre_modulo);
			$('.horas_modulo').html(horas_modulo);
			$('.numero_promocion').html(numero_promocion);
			$('.valor_hora').html(get_valor_peso(valor_hora));
			$('.dedicacion').html(cargo);
			$('.ciudad_origen').html(ciudad_origen);
			$('.fecha_inicio').html(fecha_inicio);
			$('.tipo_programa').html(tipo_programa);
			$('.nombre_programa').html(programa);
			$('.fecha_terminacion').html(fecha_terminacion);
			$('.observaciones').html(observacion);
		});
	};

	const detalle_solicitud_certificado = ({
		id,
		solicitante,
		fecha_registro,
		state,
		tipo_solicitud,
		id_tipo_solicitud,
		observacion,
		id_estado_solicitud,
		certificado,
		nombre_archivo,
		fecha_adjunto
	}) => {
		$('#especificaciones_certificado, #especificaciones_certificado_opciones').show('fast');
		$('.label_tipo_certificado').html('Tipo certificado');
		$('#tabla_detalle_certificado .info_solicitante').html(solicitante);
		$('#tabla_detalle_certificado .info_fecha').html(fecha_registro);
		$('#tabla_detalle_certificado .info_estado').html(state);
		$('#tabla_detalle_certificado .info_tipo').html(tipo_solicitud);
		if (id_estado_solicitud === 'Tal_Neg') {
			$('#observaciones_certificado').fadeIn('fast');
			$('.info_observaciones').html(`<strong class="color_danger">${observacion}</strong>`);
		} else {
			$('#observaciones_certificado').fadeOut('fast');
			$('.info_observaciones').html('');
		}
		if (id_estado_solicitud === 'Tal_Ter' && certificado) {
			$('#btn_descargar_certificado')
				.removeClass('oculto')
				.attr('href', `${Traer_Server()}${ruta_archivos_certificados}${certificado}`)
				.attr('target', '_blank')
				.attr('download', nombre_archivo);
			$('.info_fecha_entrega').html();
			$('#info_entrega_certificado').show('fast');
			$('.info_fecha_entrega').html(fecha_adjunto);
		} else {
			$('#btn_descargar_certificado').addClass('oculto').removeAttr('href', 'target');
			$('.info_fecha_entrega').html('');
			$('#info_entrega_certificado').hide('fast');
		}
		consulta_ajax(`${ruta}detalle_solicitud`, { id, tipo: id_tipo_solicitud }, (resp) => {
			if (Array.isArray(resp)) {
				$('#tabla_detalle_certificado .info_tipo_certificado').html('Básico');
				$('#especificaciones_certificado, #especificaciones_certificado_opciones').hide('fast');
			} else {
				$('#tabla_detalle_certificado .info_tipo_certificado').html('Personalizado');
				$('#especificaciones_certificado, #especificaciones_certificado_opciones').show('fast');
				opciones_certificado(resp);
			}
		});
	};

	const opciones_certificado = ({ opciones, especificaciones }) => {
		if (especificaciones) {
			$('#info_especificaciones').show('fast');
			$('.info_especificaciones').html(especificaciones);
		} else {
			$('#info_especificaciones').hide('fast');
			$('.info_especificaciones').html('');
		}
		$('#especificaciones_certificado_opciones').html('');

		Array.isArray(opciones) &&
			opciones.forEach(({ opcion }) =>
				$('#especificaciones_certificado_opciones').append(`
				<li class="list-group-item flex-space-between">
					${opcion}
					<span class="glyphicon glyphicon-ok color_success"></span>
				</li>
			`)
			);
	};

	const detalle_seleccion = (data) => {
		const { solicitante, fecha_registro, state, id_tipo_solicitud, usuario_registro, id, responsable, jefe_responsable } = data;
		id_persona = usuario_registro;
		$('#modal_detalle_seleccion').modal();
		$('.info_estado').html(`<strong id="state_field">${state}</strong>`);
		$('.info_solicitante').html(solicitante);
		$('.info_fecha').html(fecha_registro);
		consulta_ajax(`${ruta}detalle_solicitud`, { id, tipo: id_tipo_solicitud }, (resp) => {
			const { nombre_vacante, numero_vacantes, tipo_cargo, cargo, departamento } = resp;
			$('.info_nombre_vacante').html(nombre_vacante);
			$('.info_cantidad_vacante').html(numero_vacantes);
			$('.info_tipo_cargo').html(tipo_cargo);
			$('.info_responsable').html(responsable);
			$('.info_cargo').html(cargo);
			$('.info_dependencia').html(departamento);
			$('.info_jefe_responsable').html(jefe_responsable);
		});
	};

	const detalle_solicitud = (data) => {
		const { solicitante, fecha_registro, state, id_tipo_solicitud, usuario_registro, id, volante } = data;
		id_persona = usuario_registro;
		$('.info_estado').html(`<strong id="state_field">${state}</strong>`);
		$('.info_solicitante').html(solicitante);
		$('.info_fecha').html(fecha_registro);
		consulta_ajax(
			`${ruta}detalle_solicitud`,
			{ id, tipo: id_tipo_solicitud },
			({
				cuotas,
				valor,
				motivo,
				salario,
				cupo,
				tipo,
				saldo,
				valor_aprobado,
				cuotas_aprobadas,
				comentario,
				id_estado_solicitud,
				msj_negado
			}) => {
				$('tr.comentario').hide();
				$('.info_comentario').html('');
				$('.info_valor').html(get_valor_peso(valor));
				$('.info_motivo').html(motivo);
				$('.info_cuotas').html(cuotas ? cuotas : '-');
				$('.info_cuota').html(cuotas ? get_valor_peso(valor / cuotas) : '-');
				$('.info_tipo').html(tipo);
				volante
					? $('#btn_adjuntos').prop('href', `${Traer_Server()}${ruta_archivos_volantes}${volante}`).show()
					: $('#btn_adjuntos').prop('href', '').css('display', 'none');
				listar_archivos_adjuntos(id_solicitud);
				$('.info_cupo_disponible').html('---');
				if (salario && cupo) {
					$('.revisado').show('fast');
					$('.info_salario').html(get_valor_peso(salario));
					$('.info_saldo').html(saldo ? get_valor_peso(saldo) : '-');
					$('.info_cupo_disponible').html(get_valor_peso(salario * 1.5 - saldo));
					cupo <= 0
						? $('.info_cupo').html(`<strong style="color: #d9534f">${get_valor_peso(cupo)}</strong>`)
						: $('.info_cupo').html(get_valor_peso(cupo));
					if (valor_aprobado || cuotas_aprobadas) {
						$('tr.aprobado').show();
						$('.info_cuotas_aprobadas').html(
							`<strong style="color:#5cb85c;">${cuotas_aprobadas ? cuotas_aprobadas : cuotas}</strong>`
						);
						$('.info_val_aprobado').html(
							`<strong style="color:#5cb85c;">${valor_aprobado
								? get_valor_peso(valor_aprobado)
								: valor}</strong>`
						);
					} else $('tr.aprobado').hide();
				} else {
					$('.revisado').hide('fast');
					$('#tabla_historial').show('fast');
				}
				switch (id_estado_solicitud) {
					case 'Tal_Mal':
						$('#state_field').css('color', '#d9534f');
						$('tr.comentario').show();
						$('.info_comentario').html(`<strong style="color: #d9534f;">${comentario}</strong>`);
						break;
					case 'Tal_Neg':
						$('#state_field').css('color', '#d9534f');
						$('.info_comentario').html(`<strong style="color: #d9534f;">${msj_negado}</strong>`);
						break;
					case 'Tal_Tra':
						$('#btnimprimir').html(
							`<button id="imprimir_solicitud" type="button" class="btn btn-danger active" ><span class="fa fa-print"></span>Imprimir</button>`
						);
						$('#imprimir_solicitud').click(() => imprimir_solicitud());
						break;
					case 'Tal_Can':
						$('#state_field').css('color', '#d9534f');
						break;
					case 'Tal_Des':
					case 'Tal_Vis':
					case 'Tal_Apr':
						$('#state_field').css('color', '#5cb85c');
						break;
					default:
						$('#btnimprimir').html('');
						$('tr.comentario').hide();
						break;
				}
			}
		);
		let num = 0;
		consulta_ajax(`${ruta}get_descuentos`, { id }, (data) => {
			data.length
				? $('#tabla_detalle_prestamo').show('fast')
				: $('#tabla_detalle_prestamo').css('display', 'none');
			const myTable = $('#tabla_descuentos_detalle').DataTable({
				destroy: true,
				searching: false,
				processing: true,
				data,
				columns: [
					{ render: () => ++num },
					{ data: 'tipo_descuento' },
					{ data: 'concepto' },
					{ render: (data, type, { valor }, meta) => get_valor_peso(valor) }
				],
				language: get_idioma(),
				dom: 'Bfrtip',
				buttons: []
			});
		});
	};
};

const mostrar_info_persona = (id = '') => {
	obtener_datos_persona_id_completo(
		id ? id : id_persona,
		'.nombre_perso',
		'.apellido_perso',
		'.identi_perso',
		'.tipo_id_perso',
		'.foto_perso',
		'',
		'.cargo_perso',
		'.celular',
		'.direccion',
		'.barrio',
		'.lugar_residencia',
		'.correo_personal',
		'.departamento'
	);
	$('#Mostrar_detalle_persona').modal();
};

const modificar_postulante_solicitud = () => {
	let data = new FormData(document.getElementById('form_modificar_postulante_solicitud'));
	let { id, nombre_completo } = datos_postulante;
	data.append('id_postulante', id);
	data.append('id', id_postulante_sele);
	enviar_formulario(`${ruta}modificar_postulante_solicitud`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo == 'success') {
			datos_postulante = null;
			listar_postulantes_csep(id_solicitud);
			$('#form_modificar_postulante_solicitud').get(0).reset();
			$('#modal_modificar_postulante_solicitud').modal('hide');
		}
	});
};

const listar_cargos = (opt = 1) => {
	//Si opt == 1 es una solicitud de Requisición Pregrado
	// Si opt == 2 es una solicitud administrativa
	return new Promise((resolve) => {
		let url = `${ruta}listar_cargos`;
		consulta_ajax(url, { opt }, (resp) => resolve(resp));
	});
};

const listar_cargos_departamento = (id_departamento, opt = 1) => {
	//Si opt == 1 es una solicitud de Requisición Pregrado
	return new Promise((resolve) => {
		let url = `${ruta}Listar_cargos_departamento_nuevo`;
		consulta_ajax(url, { id_departamento, opt }, (resp) => resolve(resp));
	});
};

const listar_estados_csep = (id_postulante) => {
	consulta_ajax(`${ruta}listar_estados_csep`, { id_postulante }, (resp) => {
		const myTable = $('#tabla_estados_csep').DataTable({
			destroy: true,
			processing: true,
			order: [[1, 'asc']],
			data: resp,
			columns: [{ data: 'estado' }, { data: 'fecha_registro' }, { data: 'persona' }],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});
	});
};

const listar_comites = (estado = null) => {
	let { perfil, vista } = datos_vista;
	consulta_ajax(`${ruta}listar_comites`, { estado, vista }, (resp) => {
		$(`#tabla_comite tbody`)
			.off('click', 'tr td:nth-of-type(1)')
			.off('click', 'tr')
			.off('dblclick', 'tr')
			.off('click', 'tr td .modificar')
			.off('click', 'tr td .enviar');
		const myTable = $('#tabla_comite').DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						let { id_estado_comite } = full;
						let bg = 'background-color: white;color: black;';
						if (id_estado_comite == 'Com_Not') bg = 'background-color: #EABD32;color: white;';
						else if (id_estado_comite == 'Com_Ter') bg = 'background-color: #39B23B;color: white;';
						return `<span style="${bg} width: 100%;" class="pointer form-control"><span >ver</span></span>`;
					}
				},
				{ data: 'nombre' },
				{ data: 'descripcion' },
				{ data: 'total' },
				{ data: 'estado' },
				{
					render: function (data, type, full, meta) {
						let { id_estado_comite } = full;
						let resp = `<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span>`;
						if (id_estado_comite == 'Com_Ini' && vista === 'csep')
							resp = `<span style="color: #39B23B;" title="Enviar" data-toggle="popover" data-trigger="hover" class="fa fa-send pointer btn btn-default enviar"></span> <span style="color: #2E79E5;" title="Modificar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench pointer btn btn-default modificar"></span>`;
						return resp;
					}
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_comite tbody').on('click', 'tr', function () {
			$('#tabla_comite tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_comite tbody').on('dblclick', 'tr', function () {
			let { id } = myTable.row(this).data();
			listar_postulantes_por_comite(id);
		});

		$('#tabla_comite tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let { id } = myTable.row($(this).parent()).data();
			listar_postulantes_por_comite(id);
		});
		$('#tabla_comite tbody').on('click', 'tr td .modificar', function () {
			let { id } = myTable.row($(this).parent().parent()).data();
			mostrar_datos_comite_modificar(id);
		});
		$('#tabla_comite tbody').on('click', 'tr td .enviar', function () {
			let { id } = myTable.row($(this).parent().parent()).data();
			cambiar_estado_comite(id, 'Com_Not');
		});

		if (vista === 'talento_humano') myTable.column(5).visible(false);
	});
};

const show_menu = (show) => {
	show_menu_principal = show;
	if (show) {
		$('#menu_th').fadeIn(1000);
		$('#th_menu_autogestion').css('display', 'none');
	} else {
		$('#th_menu_autogestion').fadeIn(1000);
		$('#menu_th').css('display', 'none');
	}
};

const cambiar_estado_comite = (id, estado) => {
	swal(
		{
			title: 'Cambiar Estado .?',
			text:
				"Tener en cuenta que, al modificar el estado del comité este se habilitara para el siguiente proceso, si desea continuar debe presionar la opción de 'Si, Entiendo'.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Entiendo!',
			cancelButtonText: 'No, cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				consulta_ajax(`${ruta}cambiar_estado_comite`, { id, estado }, async (resp) => {
					let { tipo, mensaje, titulo } = resp;
					if (tipo == 'success') {
						if (estado == 'Com_Not') enviar_correo_estado('Com_Sen', id, '', '');
						swal.close();
						listar_comites();
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
};

const pintar_comites_combo = (id = '') => {
	consulta_ajax(`${ruta}listar_comites`, { estado: "c.id_estado_comite = 'Com_Ini'" }, (resp) => {
		$('.comites_combo').html(`<option value=''> Seleccione Comité</option>`);
		resp.forEach((elemento) =>
			$('.comites_combo').append(`<option value='${elemento.id}'> ${elemento.nombre}</option>`)
		);
		$('.comites_combo').val(id);
	});
};

const listar_postulantes_por_comite = (id) => {
	tipo_activo = 'comite';
	id_comite = id;
	//listar_comentarios_comite(id_comite);
	listar_postulantes_csep(id, 'comite');
	$('#modal_detalle_solicitud').modal();
	$(`#container_tabla_postulantes`).show('fast');
};

const aprobar_todos_postulantes_comite = (id_comite) => {
	swal(
		{
			title: 'Aprobar Todo .?',
			text:
				"Todos los postulantes seran probados, si desea continuar debe presionar la opción de 'Si, Entiendo'.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Entiendo!',
			cancelButtonText: 'No, cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				consulta_ajax(`${ruta}aprobar_todos_postulantes_comite`, { id_comite }, (resp) => {
					let { tipo, mensaje, titulo, up_comite } = resp;
					if (tipo == 'success') {
						listar_postulantes_csep(id_comite, 'comite');
						swal.close();
						enviar_correo_estado('Com_Apr_To', id_comite, '', '');
						if (up_comite == 'si')
							listar_comites("(c.id_estado_comite = 'Com_Not' OR c.id_estado_comite = 'Com_Ter')");
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
};
const obtener_programas = (buscar) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}obtener_programas`, { buscar }, (resp) => resolve(resp));
	});
};
const listar_departamentos = async (type = 1, dep = '') => {
	let departamentos = await obtener_programas(type);
	pintar_datos_combo(departamentos, '.cbxdepartamento', 'Seleccione Departamento', dep);
};

const get_departamentos = (tipo) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}get_departamentos`, { tipo }, (departamentos) => resolve(departamentos));
	});
};

const listar_programas = async () => {
	let programas = await obtener_programas(2);
	pintar_datos_combo(programas, '.cbxprogramas', 'Seleccione Programa');
};

const modificar_plan_trabajo = (id) => {
	let plac = 'Plan de Trabajo';
	swal(
		{
			title: 'Plan de Trabajo.!',
			text: '',
			type: 'input',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Aceptar!',
			cancelButtonText: 'Cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder: `Ingrese Nuevo plan de trabajo`
		},
		function (mensaje) {
			if (mensaje === false) return false;
			if (mensaje === '') {
				swal.showInputError(`Debe Ingresar el ${plac}.!`);
			} else {
				consulta_ajax(`${ruta}modificar_plan_trabajo`, { id, mensaje }, (resp) => {
					let { tipo, mensaje, titulo } = resp;
					if (tipo == 'success') {
						listar_postulantes_csep(id_comite, 'comite');
						$('#modal_detalle_postulante').modal('hide');
					}
					MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
};

const obtener_programas_persona = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_programas_persona`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};

const listar_programas_persona_tabla = async (id) => {
	programa_per = null;
	let data = await obtener_programas_persona(id);
	$(`#tabla_programas_csep tbody`).off('click', 'tr').off('click', 'tr .retirar').off('click', 'tr .asignar');
	const myTable = $('#tabla_programas_csep').DataTable({
		destroy: true,
		processing: true,
		data,
		columns: [
			{ data: 'valor' },
			{ data: 'valorx' },
			{
				render: function (data, type, full, meta) {
					let { estado, tipo } = full;
					let bg = 'background-color: #39B23B;color: white;';
					let title = 'Asignado';
					if (estado == null) {
						bg = 'background-color: #d9534f;color: white;';
						title = 'Sin Asignar';
					}
					return `<span style="${bg} width: 100%;" class="pointer form-control"><span >${title}</span></span>`;
				}
			},
			{
				render: function (data, type, full, meta) {
					let { estado } = full;
					let resp = `<span class="btn btn-default asignar fa fa-check" style='color: #39B23B' title="Asignar" data-toggle="popover" data-trigger="hover"></span>`;
					if (estado != null)
						resp = `<span class="btn btn-default retirar fa fa-close" style='color: #d9534f' title="Retirar" data-toggle="popover" data-trigger="hover"></span>`;
					return resp;
				}
			}
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: []
	});

	$('#tabla_programas_csep tbody').on('click', 'tr', function () {
		$('#tabla_programas_csep tbody tr').removeClass('warning');
		$(this).attr('class', 'warning');
	});

	$('#tabla_programas_csep tbody').on('click', 'tr .asignar', function () {
		let { id } = myTable.row($(this).parent().parent()).data();
		let persona = $('#cbx_personas_vb_csep').val();
		asignar_programa_persona(persona, id);
	});
	$('#tabla_programas_csep tbody').on('click', 'tr .retirar', function () {
		let { estado } = myTable.row($(this).parent().parent()).data();
		retirar_programa_persona(estado);
	});

	const contar_asignado_no_asignados = (resp) => {
		let si = 0,
			no = 0;
		resp.forEach((elemento) => {
			if (elemento.estado == null) {
				no++;
			} else {
				si++;
			}
		});
		if (no == 0) $('#btn_asignar_todo').css('display', 'none');
		else $('#btn_asignar_todo').show('fast');

		if (si == 0) $('#btn_retirar_todo').css('display', 'none');
		else $('#btn_retirar_todo').show('fast');
	};
	contar_asignado_no_asignados(data);
};

const listar_personas_vb_csep = async (persona = '') => {
	let personas = await obtener_personas_vb_csep();
	encargados_vb = personas;
	$('#cbx_personas_vb_csep').html(`<option value=''> Seleccione Persona</option>`);
	personas.forEach((elemento) => {
		let { id, nombre_completo } = elemento;
		$('#cbx_personas_vb_csep').append(`<option value='${id}'> ${nombre_completo}</option>`);
	});
	$('#cbx_personas_vb_csep').val(persona);
};
const obtener_personas_vb_csep = () => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}listar_personas_vb_csep`, '', (resp) => {
			resolve(resp);
		});
	});
};

const asignar_programa_persona = (id_persona, id_permiso) => {
	swal(
		{
			title: 'Asignar Programa .?',
			text:
				"Tener en cuenta que, al asignar el programa la persona podrá gestionar los postulantes asignados a este, si desea continuar debe presionar  la opción de 'Si, Entiendo'.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Entiendo!',
			cancelButtonText: 'No, cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				consulta_ajax(`${ruta}asignar_programa_persona`, { id_persona, id_permiso }, (resp) => {
					let { tipo, mensaje, titulo } = resp;
					if (tipo == 'success') {
						$('#modal_asignar_programa').modal('hide');
						$('#id_tipo_permiso').val('');
						listar_programas_persona_tabla(id_persona);
						swal.close();
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
};
const retirar_programa_persona = (id) => {
	swal(
		{
			title: 'Retirar Programa .?',
			text:
				"Tener en cuenta que, al retirar el programa la persona no podrá gestionar los postulantes asignados a este, si desea continuar debe presionar  la opción de 'Si, Entiendo'.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Entiendo!',
			cancelButtonText: 'No, cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				let persona = $('#cbx_personas_vb_csep').val();
				consulta_ajax(`${ruta}retirar_programa_persona`, { id }, (resp) => {
					let { tipo, mensaje, titulo } = resp;
					if (tipo == 'success') {
						listar_programas_persona_tabla(persona);
						swal.close();
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
};
const asignar_todos_programas = (id_persona) => {
	swal(
		{
			title: 'Asignar Programas .?',
			text:
				"Tener en cuenta que, al asignar los programas la persona podrá gestionar los postulantes asignados a estos, si desea continuar debe presionar  la opción de 'Si, Entiendo'.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Entiendo!',
			cancelButtonText: 'No, cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				consulta_ajax(`${ruta}asignar_todos_programas`, { id_persona }, (resp) => {
					let { tipo, mensaje, titulo } = resp;
					if (tipo == 'success') {
						$('#modal_asignar_programa').modal('hide');
						$('#id_tipo_permiso').val('');
						listar_programas_persona_tabla(id_persona);
						swal.close();
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
};

const retirar_todos_programas = (id_persona) => {
	swal(
		{
			title: 'Retirar Programa .?',
			text:
				"Tener en cuenta que, al retirar el programa la persona no podrá gestionar los postulantes asignados a este, si desea continuar debe presionar  la opción de 'Si, Entiendo'.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Entiendo!',
			cancelButtonText: 'No, cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				consulta_ajax(`${ruta}retirar_todos_programas`, { id_persona }, (resp) => {
					let { tipo, mensaje, titulo } = resp;
					if (tipo == 'success') {
						listar_programas_persona_tabla(id_persona);
						swal.close();
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
};

const configurar_elementos_persona_aprueba = (id_persona) => {
	let { aprueba } = encargados_vb.find((elemento) => {
		return elemento.id == id_persona;
	});
	$('#btn_cambiar_pa_cat').off('click');
	$('#container_mensaje_ap').html(``);
	$('#container_mensaje_ap_cat').html(
		`<a class="black-color" id="btn_cambiar_pa_cat" href='#'><span class="fa fa-check" style='color: #2E79E5'></span> click aqui si desea asignarle el permiso para aprobar docentes catedraticos.</a> `
	);
	$('#btn_cambiar_pa_cat').click(() => {
		gestionar_personas_apr_cate(id_persona, 1);
	});

	if (aprueba == 'Apr_Csep') {
		$('#container_mensaje_ap').html(`
    <div class="alert alert-warning" role="alert">
      <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
      <p>El usuario seleccionado es el encargado de otorgar el aprobado final para los postulantes del CSEP virtual, se recomienda que tenga todos los programas para que el proceso siga su curso.
      <a class="black-color" id="btn_cambiar_pa" href='#'><span class="fa fa-refresh" style='color: #2E79E5'></span> click aqui para cambiar</a>
      </p>
    </div>`);

		$('#cbx_personas_change').html(`<option value=''> Seleccione Persona</option>`);
		encargados_vb.forEach((elemento) => {
			let { id, nombre_completo } = elemento;
			if (id != id_persona)
				$('#cbx_personas_change').append(`<option value='${id}'> ${nombre_completo}</option>`);
		});
		$('#btn_cambiar_pa').click(() => {
			$('#modal_cambiar_persona').modal();
		});
		$('#container_mensaje_ap_cat').html(``);
	} else if (aprueba == 'Apr_Csep_Cat') {
		$('#container_mensaje_ap_cat').html(`
    <div class="alert alert-info" role="alert">
      <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
      <p>El usuario seleccionado esta autorizado para otorgar el aprobado final de profesores catedraticos en el CSEP virtual.
      <a class="black-color" id="btn_cambiar_pa_cat" href='#'><span class="fa fa-refresh" style='color: #2E79E5'></span> click aqui eliminar permiso</a>
      </p>
    </div>`);
		$('#btn_cambiar_pa_cat').off('click');
		$('#btn_cambiar_pa_cat').click(() => {
			gestionar_personas_apr_cate(id_persona, 2);
		});
		$('#container_mensaje_ap').html(``);
	}
};

const configurar_elementos_persona_aprueba_cat = (id_persona) => {
	let { aprueba } = encargados_vb.find((elemento) => {
		return elemento.id == id_persona;
	});

	if (aprueba == 'Apr_Csep_Cat') {
		$('#container_mensaje_ap_cat').html(`
    <div class="alert alert-warning" role="alert" id='container_mensaje_ap'>
      <h4><span class="fa fa-exclamation-triangle"></span> Aviso!</h4>
      <p>El usuario seleccionado esta autorizado para otorgar el aprobado final de profesores catedraticos en el CSEP virtual.
      <a class="black-color" id="btn_cambiar_pa_cat" href='#'><span class="fa fa-refresh" style='color: #2E79E5'></span> click aqui eliminar permiso</a>
      </p>
    </div>`);
	} else {
		$('#container_mensaje_ap').html(``);
	}
};

const cambiar_persona = (id_persona) => {
	swal(
		{
			title: 'Cambiar Persona .?',
			text:
				"Tener en cuenta que la persona seleccionada podrá otorgar el aprobado final de los postulantes en el CSEP, si desea continuar debe presionar  la opción de 'Si, Entiendo'.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Entiendo!',
			cancelButtonText: 'No, cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				consulta_ajax(`${ruta}cambiar_persona`, { id_persona }, async (resp) => {
					let { tipo, mensaje, titulo } = resp;
					if (tipo == 'success') {
						let id = $('#cbx_personas_vb_csep').val().trim();
						$('#modal_cambiar_persona').modal('hide');
						let personas = await obtener_personas_vb_csep();
						encargados_vb = personas;
						configurar_elementos_persona_aprueba(id);
						swal.close();
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
};
const gestionar_personas_apr_cate = (id_persona, tipo) => {
	swal(
		{
			title: 'Gestionar Permiso .?',
			text: `Tener en cuenta que la persona seleccionada ${tipo == 1
				? ''
				: 'no'} podrá otorgar el aprobado final de los docentes catedraticos en el CSEP, si desea continuar debe presionar  la opción de 'Si, Entiendo'.`,
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Entiendo!',
			cancelButtonText: 'No, cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				consulta_ajax(`${ruta}gestionar_personas_apr_cate`, { id_persona, tipo }, async (resp) => {
					let { tipo, mensaje, titulo } = resp;
					if (tipo == 'success') {
						let personas = await obtener_personas_vb_csep();
						let id = $('#cbx_personas_vb_csep').val().trim();
						encargados_vb = personas;
						configurar_elementos_persona_aprueba(id);
						swal.close();
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
};

const agregar_prestamo = () => {
	let data = new FormData(document.getElementById('form_nuevo_prestamo'));
	enviar_formulario(`${ruta}agregar_prestamo`, data, ({ tipo, mensaje, titulo, id }) => {
		if (tipo === 'success') $('.modal').modal('hide');
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			$('#form_nuevo_prestamo').get(0).reset();
			send_mail({ id, nextState: 'Tal_Env' });
		} else $('#modal_terminos_condiciones').modal('hide');
	});
};

const listar_solicitudes_csep = (id = '') => {
	let { perfil } = datos_vista;
	const { estado, tipo_solicitud, fecha_inicio, fecha_fin } = formDataToJson(
		new FormData(document.getElementById('form_filtro'))
	);
	let is_filtered = estado || tipo_solicitud || fecha_inicio || fecha_fin ? true : false;
	is_filtered ? $('.mensaje-filtro').show() : $('.mensaje-filtro').hide();
	consulta_ajax(
		`${ruta}listar_solicitudes_csep`,
		{
			id,
			estado,
			tipo_solicitud,
			fecha_inicio,
			fecha_fin
		},
		(data) => {
			let i = 0;
			$(`#tabla_solicitudes tbody`)
				.off('click', 'tr td:nth-of-type(1)')
				.off('click', 'tr')
				.off('dblclick', 'tr')
				.off('click', 'tr span.revisar')
				.off('click', 'tr span.modificar')
				.off('click', 'tr span.negar');
			const myTable = $('#tabla_solicitudes').DataTable({
				destroy: true,
				processing: true,
				data,
				columns: [
					{ data: 'ver' },
					{ data: 'tipo_solicitud' },
					{ data: 'fecha_registro' },
					{ data: 'solicitante' },
					{ data: 'estado_solicitud' },
					{ data: 'gestion' }
				],
				language: get_idioma(),
				dom: 'Bfrtip',
				buttons: get_botones()
			});

			$('#tabla_solicitudes tbody').on('click', 'tr', function () {
				$('#tabla_solicitudes tbody tr').removeClass('warning');
				$(this).attr('class', 'warning');
				info_solicitud = myTable.row(this).data();
			});

			$('#tabla_solicitudes tbody').on('dblclick', 'tr', function () {
				const data = myTable.row(this).data();
				const { id, id_tipo_solicitud, usuario_registro } = data;
				id_persona = usuario_registro;
				switch (id_tipo_solicitud) {
					case 'Hum_Csep':
						configurar_modal_detalle_sol(id_tipo_solicitud, id);
						break;
					case 'Hum_Prec':
						detalle_vacante(data);
						break;
					default:
						break;
				}
			});

			$('#tabla_solicitudes tbody').on('click', 'tr td:nth-of-type(1)', function () {
				const data = myTable.row($(this).parent()).data();
				const { id, id_tipo_solicitud, usuario_registro } = data;
				id_persona = usuario_registro;
				switch (id_tipo_solicitud) {
					case 'Hum_Csep':
						configurar_modal_detalle_sol(id_tipo_solicitud, id);
						break;
					case 'Hum_Prec':
						detalle_vacante(data);
						break;
					default:
						break;
				}
			});
			$('#tabla_solicitudes tbody').on('click', 'tr span.negar', function () {
				const { id, id_tipo_solicitud, solicitante, correo } = myTable.row($(this).parent()).data();
				let tipo_solicitud;
				let data = {
					id,
					type: id_tipo_solicitud,
					nextState: 'Tal_Neg',
					success: 'Solicitud Negada Exitosamente!'
				};

				switch (id_tipo_solicitud) {
					case 'Hum_Cert':
						tipo_solicitud = 'Certificado Laboral';
						break;
					case 'Hum_Pres':
						tipo_solicitud = 'Prestamo';
						break;
					case 'Hum_Admi':
						tipo_solicitud = 'Requisición Administrativos';
						break;
					case 'Hum_Posg':
						tipo_solicitud = 'Requisición Posgrado';
						break;
					case 'Hum_Apre':
						tipo_solicitud = 'Requisición Aprendices';
					case 'Hum_Prec':
						tipo_solicitud = 'Pregrado - Requisición';
						break;
					case 'Hum_Cir':
						tipo_solicitud = 'Certificado de ingresos y retenciones';
						break;
					case 'Hum_Vac':
						tipo_solicitud = 'Vacaciones';
						break;
					case 'Hum_Lic':
						tipo_solicitud = 'Licencia';
						break;
					default:
						tipo_solicitud = '';
						break;
				}

				msj_confirmacion_input(
					`Rechazar ${tipo_solicitud}`,
					'Por favor digite motivo de rechazo de la solicitud',
					'Motivo de Rechazo',
					(msj) => {
						data.msj = msj;
						const callback = async () => {
							const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
							let p = `Se le notifica que la solicitud de ${tipo_solicitud} realizada por usted ha sido rechazada.`;
							let asunto = `Solicitud de ${tipo_solicitud} rechazada`;

							const mensaje = `
								<p>${p}</p>
								<p><strong>Motivo de rechazo:</strong> "${msj}"</p>
								<p>Más información en: ${ser}</p>
							`;
							MensajeConClase('Solicitud rechazada exitosamente', 'success', 'Proceso Exitoso!');

							//  await get_usuarios_a_notificar(id_tipo_solicitud, data.nextState);

							// correos.push({ persona: solicitante, correo });

							enviar_correo_personalizado(
								'th',
								mensaje,
								correo,
								solicitante,
								'AGIL Talento Humano CUC',
								asunto,
								'Par_TH',
								1
							);

						};
						gestionar_solicitud(data, callback);
					}
				);
			});



			$('#tabla_solicitudes tbody').on('click', 'tr span.cancelar', function () {
				let { id_tipo_solicitud, id } = myTable.row($(this).parent()).data();
				switch (id_tipo_solicitud) {
					case 'Hum_Prec':
						const data = {
							id,
							type: id_tipo_solicitud,
							nextState: 'Tal_Can'
						};
						msj_confirmacion('¿ Cancelar Solicitud ?', '', () => gestionar_solicitud(data));
						break;
				}
			});

			$('#tabla_solicitudes tbody').on('click', 'tr span.modificar', async function () {
				const { id } = myTable.row($(this).parent()).data();
				mod_vacante = 1;
				$('.texto_accion').html('Modificar');
				id_solicitud = id;
				$('.adicional_info').hide('fast');
				$('#botones_modificar, .no-revisar').show();
				get_detalle_vacante(id, (resp) => mostrar_info_solicitud(resp));
				$('#modal_solicitud_vacante').modal('show');
				let lista_cargos = await listar_cargos(1);
				pintar_datos_combo(
					lista_cargos,
					"#form_modificar_postulante_solicitud select[name='id_cargo']",
					'Seleccione Cargo'
				);
			});

			$('#tabla_solicitudes tbody').on('click', 'tr span.revisar', async function () {
				let { id_tipo_solicitud, id } = myTable.row($(this).parent()).data();
				switch (id_tipo_solicitud) {
					case 'Hum_Prec':
					case 'Hum_Admi':
						mod_vacante = 1;
						$('.texto_accion').html('Aprobar');
						id_solicitud = id;
						const cargos = await listar_cargos();
						get_detalle_vacante(id, ({ vacante }) => {
							const { id_departamento, cargo_id, plan_trabajo } = vacante;
							pintar_datos_combo(cargos, '.cbxcargos', 'Seleccione Cargo', cargo_id);
							$("#form_revisar_requisicion select[name='id_programa']").val(id_departamento);
							$("#form_revisar_requisicion select[name='cargo_id']").trigger('change', [
								{ cargo: cargo_id }
							]);
							$("#form_revisar_requisicion textarea[name='plan_trabajo']").val(plan_trabajo);
						});
						$('#modal_revisar_requisicion').modal('show');
						break;
				}
			});

			if (perfil != 'Per_Admin' && perfil != 'Per_Csep') myTable.column(3).visible(false);
		}
	);

	const configurar_modal_detalle_sol = (tipo, id) => {
		id_solicitud = id;
		tipo_activo = 'solicitud';
		listar_postulantes_csep(id);
		$('#modal_detalle_solicitud').modal();
		$(`#container_tabla_postulantes`).show('fast');
	};
};
const traer_correo_notifica_comite = (id) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}traer_correo_notifica_comite`, { id }, (resp) => {
			resolve(resp);
		});
	});
};
const traer_correos_perfil = (perfil) => {
	return new Promise((resolve) => {
		consulta_ajax(`${Traer_Server()}index.php/personas_control/traer_correos_perfil`, { perfil }, (resp) => {
			resolve(resp);
		});
	});
};

const enviar_correo_estado = async (estado, id, motivo, nombre_completo, tipo_vista = '') => {
	let id_tipo_solicitud = 'Hum_Csep';
	let sw = false;
	let tipo = 3;
	let correo = [];
	let imagen = '';
	let mensaje = '';
	let nombre = 'CSEP virtual';
	let ser = `<a href="${Traer_Server()}index.php/csep${tipo_vista}/${id}"><b>agil.cuc.edu.co</b></a>`;
	let ser_th = `<a href="${Traer_Server()}index.php/talento_humano/csep/${id}"><b>Talento Humano</b></a>`;
	let ser_csep = `<a href="${Traer_Server()}index.php/csep${tipo_vista}/${id}"><b>CSEP Virtual</b></a>`;
	let ser_comite_csep = `<a href="${Traer_Server()}index.php/comite_csep/${id}"><b>Comité CSEP</b></a>`;
	if (estado == 'Pos_Env') {
		sw = true;
		titulo = 'Nuevos Postulantes';
		nombre_completo = 'CSEP virtual';
		mensaje = `Se informa que hay nuevos postulantes para el CSEP virtual, puede validar la informaci&oacuten en: <br><br>${ser}`;
		correo = await traer_correos_perfil('Per_Csep');
	} else if (estado == 'Pos_Neg') {
		sw = true;
		nombre = 'CSEP virtual - Talento Humano';
		titulo = 'Postulante Negado';
		mensaje = `Se informa que el postulante ${nombre_completo} fue Negado por el encargado de este proceso, puede validar la informaci&oacuten en:<br><br>${ser_th}<br>${ser_csep}`;
		let correos_csep = await traer_correos_perfil('Per_Csep');
		correo = await traer_correos_responsables_estado('Tal_Env', id_tipo_solicitud);
		correo = correo.concat(correos_csep);
	} else if (estado == 'Pos_Apr') {
		sw = true;
		nombre = 'CSEP virtual - Talento Humano';
		titulo = 'Postulante Aprobado';
		mensaje = `Se informa que el postulante ${nombre_completo} fue aprobado por el encargado de este proceso, puede validar la informaci&oacuten en:<br><br>${ser_th}<br>${ser_csep}`;
		let correos_csep = await traer_correos_perfil('Per_Csep');
		correo = await traer_correos_responsables_estado('Tal_Env', id_tipo_solicitud);
		correo = correo.concat(correos_csep);
	} else if (estado == 'Pos_Can') {
		sw = true;
		titulo = 'Postulante Cancelado';
		mensaje = `Se informa que el proceso de contrataci&oacuten del postulante ${nombre_completo} fue cancelado, por el siguiente motivo ${motivo} , puede validar la informaci&oacuten en:<br><br>${ser}`;
		correo = await traer_correos_perfil('Per_Csep');
	} else if (estado == 'Pos_Con') {
		let data = {
			canvasId: 'tabla_detalle_postulante',
			filename: 'Detalle Solicitud.png',
			path: '../archivos_adjuntos/talentohumano/detalles_solicitudes/imagen.png',
			nombre: nombre_completo,
			observaciones: motivo
		};
		downloadCanvas(data, async (file_saved) => {
			if (file_saved) {
				const { nombre, path, filename, observaciones } = data;
				let titulo = 'Postulante Contratado';
				let mensaje = `<p>Se informa que el proceso de contrataciónn del postulante ${nombre} fue terminado con exito, puede validar la información en:</p><p>${ser_th}</p><p>${ser_csep}</p></br>
					<p><strong>Observación:</strong> ${observaciones}</p>
					<p><strong>Nota:</strong> En el presente correo electrónico se adjunta el detalle de la solicitud gestionada en el software.</p>`;
				// let correos_csep = await traer_correos_perfil('Per_Csep');
				let correo = await traer_correos_responsables_estado('Tal_Ter', id_tipo_solicitud);
				// correo = correo.concat(correos_csep);
				enviar_correo_personalizado(
					'th',
					mensaje,
					correo,
					'CSEP virtual',
					'CSEP virtual',
					titulo,
					'Par_TH',
					3,
					[path, filename]
				);
			}
		});
	} else if (estado == 'Com_Sen') {
		sw = true;
		titulo = 'Nuevo Comit&eacute; CSEP';
		mensaje = `Se informa que un nuevo comit&eacute; se encuentra activo, puede validar la informaci&oacute;n en <br>${ser_comite_csep}`;
		correo = await traer_correo_notifica_comite(id);
	} else if (estado == 'Com_Apr_To') {
		sw = true;
		nombre = 'CSEP virtual - Talento Humano';
		titulo = 'Postulantes Aprobados';
		let ser_th = `<a href="${Traer_Server()}index.php/talento_humano/${id}"><b>Talento Humano</b></a>`;
		let ser_csep = `<a href="${Traer_Server()}index.php/csep/${id}"><b>CSEP Virtual</b></a>`;
		mensaje = `Se informa que hay postulantes aprobados,puede validar la informaci&oacuten en:<br><br>${ser_th}<br>${ser_csep}`;
		let correos_csep = await traer_correos_perfil('Per_Csep');
		correo = await traer_correos_responsables_estado('Tal_Env', id_tipo_solicitud);
		correo = correo.concat(correos_csep);
	}
	if (sw) enviar_correo_personalizado('th', mensaje, correo, nombre, 'CSEP virtual CUC', titulo, 'Par_TH', tipo);
};

const cargar_info_correo_id = (id, url) => {
	let ruta = '';
	if (id.length != 0) {
		ruta = gestionar_ruta(url);
		if (ruta == 'csep/solicitud') {
			id_solicitud = id;
			tipo_activo = 'solicitud';
			$('#modal_detalle_solicitud').modal();
		} else if (ruta == 'talento_humano/csep' || ruta == 'csep') {
			listar_postulantes_por_comite(id);
		} else if (ruta == 'talento_humano') {
			listar_solicitudes(id);
			$('#form_asignar_postulante').get(0).reset();
			$('#container_solicitudes').fadeIn(1000);
			$('#menu_principal').css('display', 'none');
		}
	}
};

const gestionar_ruta = (route) => {
	const pos = route.indexOf('index.php/');
	route = route.slice(pos + 10, route.length);
	let ruta = route.replace(/[0-9]+/g, '');
	if (ruta[ruta.length - 1] === '/') ruta = ruta.substr(0, ruta.length - 1);
	return ruta;
};

const traer_descuentos = () => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}traer_descuentos`, {}, (data) => {
			resolve(data);
		});
	});
};

const modificar_configuraciones = (tipo) => {
	const data = formDataToJson(new FormData(document.getElementById('form_administrar')));
	let aux = false;
	if (tipo === 'descuentos') {
		const { salud, pension } = data;
		if (salud <= 100 && salud >= 0) {
			if (pension <= 100 && pension >= 0) {
				aux = true;
			} else MensajeConClase('El porcentaje de descuento por pensión debe estar entre 0 y 100', 'info', 'Ooops!');
		} else MensajeConClase('El porcentaje de descuento por salud debe estar entre 0 y 100', 'info', 'Ooops!');
	} else if (tipo === 'cuotas') aux = true;
	else if (tipo === 'responsable') {
		const { correo } = data;
		if (isValidEmail(correo)) aux = true;
	}
	if (aux) {
		data.tipo = tipo;
		consulta_ajax(`${ruta}modificar_configuraciones`, data, ({ mensaje, tipo, titulo }) => {
			MensajeConClase(mensaje, tipo, titulo);
			$('#modal_administrar').modal('hide');
		});
	} else MensajeConClase('Por favor verifique la información que desea modificar.', 'info', 'Ooops!');
};

const validar_campos = (form) => {
	let campos = document.querySelectorAll(`${form} input`);
	let aux = true;
	campos.forEach((element) => {
		if (element.value == '') {
			element.parentElement.classList.add('has-error');
			aux = false;
		} else element.parentElement.classList.remove('has-error');
	});
	return aux;
};

const agregar_descuento = () => {
	const ok = validar_campos('#form_descuentos');
	if (ok) {
		const salario = $('#txtsalario').val();
		if (salario && salario > 0) {
			const data = formDataToJson(new FormData(document.getElementById('form_descuentos')));
			if (data.valor && data.valor > 0) {
				$('#txtsalario').prop('disabled', true);
				data.id = numero_aleatorio();
				descuentos.push(data);
				llenar_tabla(descuentos);
				$('#form_descuentos').get(0).reset();
				const cupo_disponible = calcular_saldo_disponible();
				$('#maximo').html(get_valor_peso(cupo_disponible));
				const saldo = calcular_saldo($('#txtsalario').val());
				$('#saldo').html(get_valor_peso(saldo));
				$('#txtsaldo_pendiente').val(calcular_saldo_pendiente());
				MensajeConClase('Descuento agregado exitosamente.', 'success', 'Proceso Exitoso!');
			} else MensajeConClase('El campo valor debe ser numérico.', 'info', 'Ooops!');
		} else {
			MensajeConClase('Por favor digite un salario.', 'info', 'Ooops!');
			limpiar_salario();
		}
	} else MensajeConClase('Por favor complete todos los campos', 'info', 'Ooops!');
};

const limpiar_salario = () => {
	descuentos = [];
	$('#txtsalario').val('').prop('disabled', false);
	$('#txtsalario').val('');
	$('#maximo').html(get_valor_peso(0));
	$('#saldo').html(get_valor_peso(0));
	llenar_tabla(descuentos);
	$('#form_descuentos').get(0).reset();
	$('#cbxtipo_descuento').trigger('change');
	$('#texto_cupo').css('color', 'black');
};

const numero_aleatorio = () => {
	var num = new Uint32Array(1);
	window.crypto.getRandomValues(num);
	return num[0];
};

const llenar_tabla = (data) => {
	let num = 1;
	$(`#tabla_descuentos tbody`).off('click', 'tr').off('click', 'tr span.eliminar');
	const myTable = $('#tabla_descuentos').DataTable({
		destroy: true,
		processing: true,
		searching: false,
		data,
		columns: [
			{ render: () => num++ },
			{ data: 'concepto' },
			{ data: 'valor' },
			{
				render: (data, type, { deuda }, meta) => (deuda ? deuda : '-')
			},
			{
				render: () =>
					'<span class="btn btn-default eliminar"><span class="fa fa-remove" style="color: #d9534f"></span></span>'
			}
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: []
	});

	$('#tabla_descuentos tbody').on('click', 'tr', function () {
		$('#tabla_descuentos tbody tr').removeClass('warning');
		$(this).attr('class', 'warning');
	});

	$('#tabla_descuentos tbody').on('click', 'tr span.eliminar', function () {
		const { id, concepto, valor } = myTable.row($(this).parent()).data();
		eliminar_descuento(id);
	});
};

const eliminar_descuento = (id) => {
	swal(
		{
			title: 'Eliminar Descuento',
			text: '¿ Seguro que desea eliminar el descuento ?',
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Entiendo!',
			cancelButtonText: 'No, cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				descuentos.forEach((element) => {
					if (element.id === id) {
						const index = descuentos.indexOf(element);
						descuentos.splice(index, 1);
					}
				});
				const saldo = calcular_saldo($('#txtsalario').val());
				$('#saldo').html(get_valor_peso(saldo));
				const cupo_disponible = calcular_saldo_disponible();
				$('#maximo').html(get_valor_peso(cupo_disponible));
				llenar_tabla(descuentos);
				swal.close();
			}
		}
	);
};

const traer_pension_salud = async () => {
	const { salud, pension } = await traer_descuentos();
	pen_sal = { salud: salud[0].valor, pension: pension[0].valor };
};

const calcular_saldo = (salario) => {
	const desc = (Number(pen_sal.salud) + Number(pen_sal.pension)) / 100;
	salario = (salario - salario * desc) / 2;
	if (descuentos.length) descuentos.forEach(({ valor }) => (salario -= valor));
	if (salario < 0) $('#texto_cupo').css('color', 'red');
	else if (salario > 0) $('#texto_cupo').css('color', 'green');
	else $('#texto_cupo').css('color', 'black');
	return salario;
};

const get_valor_peso = (valor) => new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(valor);

const get_historial = () => {
	let num = 0;
	consulta_ajax(`${ruta}get_historial`, { id: id_solicitud }, (data) => {
		const myTable = $('#tabla_historial_estados').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [{ render: () => ++num }, { data: 'estado' }, { data: 'fecha' }, { data: 'fullname' }],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});
	});
};

const listar_personas = (texto = '') => {
	consulta_ajax(`${ruta}listar_personas`, { texto }, (data) => {
		$(`#tabla_personas tbody`).off('click', 'tr').off('click', 'tr span.asignar').off('dblclick', 'tr');
		const myTable = $('#tabla_personas').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ data: 'fullname' },
				{
					render: (data, type, full, meta) =>
						'<span class="btn btn-default asignar" title="Seleccionar Persona" style="color: #5cb85c"><span class="fa fa-check"></span></span>'
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_personas tbody').on('click', 'tr', function () {
			$('#tabla_personas tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_personas tbody').on('click', 'tr span.asignar', function () {
			let { id, fullname } = myTable.row($(this).parent().parent()).data();
			id_persona = id;
			$('#modal_elegir_persona').modal('hide');
			$('#s_persona').html(fullname);
			listar_actividades(id);
		});

		$('#tabla_personas tbody').on('dblclick', 'tr', function () {
			let { id, fullname } = myTable.row($(this)).data();
			id_persona = id;
			$('#modal_elegir_persona').modal('hide');
			$('#s_persona').html(fullname);
			listar_actividades(id);
		});
	});
};

const listar_actividades = (persona) => {
	let num = 0;
	consulta_ajax(`${ruta}listar_actividades`, { persona }, (data) => {
		$(`#tabla_actividades tbody`)
			.off('click', 'tr')
			.off('click', 'tr span.asignar')
			.off('click', 'tr span.quitar')
			.off('click', 'tr span.config')
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
					render: (data, type, { asignado }, meta) => {
						let datos = asignado
							? '<span class="btn btn-default quitar" style="color: #5cb85c"><span class="fa fa-toggle-on"></span></span> <span class="btn btn-default config"><span class="fa fa-cog"></span></span>'
							: '<span class="btn btn-default asignar"><span class="fa fa-toggle-off" ></span></span> ';
						return datos;
					}
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_actividades tbody').on('click', 'tr', function () {
			$('#tabla_actividades tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_actividades tbody').on('dblclick', 'tr', function () {
			$('#tabla_actividades tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_actividades tbody').on('click', 'tr span.asignar', function () {
			const { asignado, id } = myTable.row($(this).parent()).data();
			asignar_actividad(asignado, id);
		});

		$('#tabla_actividades tbody').on('click', 'tr span.quitar', function () {
			const { asignado, id } = myTable.row($(this).parent()).data();
			quitar_actividad(asignado, id);
		});

		$('#tabla_actividades tbody').on('click', 'tr span.config', function () {
			const { asignado, id } = myTable.row($(this).parent()).data();
			actividad_selec = asignado;
			$('#modal_elegir_estado').modal();
			listar_estados(asignado);
		});
	});

	const asignar_actividad = (asignado, id) => {
		consulta_ajax(
			`${ruta}asignar_actividad`,
			{ id, persona: id_persona, asignado },
			({ mensaje, tipo, titulo }) => {
				MensajeConClase(mensaje, tipo, titulo);
				listar_actividades(id_persona);
			}
		);
	};

	const quitar_actividad = (asignado, id) => {
		swal(
			{
				title: 'Desasignar Actividad',
				text:
					'Tener en cuenta que al desasignarle esta actividad al usuario no podrá visualizar ninguna solicitud de este tipo.',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#D9534F',
				confirmButtonText: 'Si, Entiendo!',
				cancelButtonText: 'No, cancelar!',
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true
			},
			(isConfirm) => {
				if (isConfirm) {
					consulta_ajax(
						`${ruta}quitar_actividad`,
						{ id, persona: id_persona, asignado },
						({ mensaje, tipo, titulo }) => {
							listar_actividades(id_persona);
						}
					);
					swal.close();
				} else MensajeConClase(mensaje, tipo, titulo);
			}
		);
	};
};

const listar_estados = (actividad) => {
	const desasignar =
		'<span class="btn btn-default desasignar" title="Desasignar Estado"><span class="fa fa-toggle-on" style="color: #5cb85c"></span></span> ';
	const asignar =
		'<span class="btn btn-default asignar" title="Asignar Estado"><span class="fa fa-toggle-off"></span></span> ';
	const notificar =
		'<span class="btn btn-default notificar" title="Activar Notificación"><span class="fa fa-bell-o"></span></span> ';
	const no_notificar =
		'<span class="btn btn-default no_notificar" title="Desactivar Notificación"><span class="fa fa-bell red"></span></span> ';
	consulta_ajax(`${ruta}listar_estados`, { actividad, persona: id_persona }, (data) => {
		$(`#tabla_estados tbody`)
			.off('click', 'tr')
			.off('click', 'tr span.asignar')
			.off('click', 'tr span.desasignar')
			.off('click', 'tr span.no_notificar')
			.off('click', 'tr span.notificar')
			.off('dblclick', 'tr');
		const myTable = $('#tabla_estados').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ data: 'parametro' },
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

		$('#tabla_estados tbody').on('click', 'tr', function () {
			$('#tabla_estados tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_estados tbody').on('click', 'tr span.asignar', function () {
			const { estado } = myTable.row($(this).parent()).data();
			asignar_estado(estado, actividad_selec, id_persona);
		});

		$('#tabla_estados tbody').on('click', 'tr span.desasignar', function () {
			const { asignado, estado } = myTable.row($(this).parent()).data();
			quitar_estado(estado, actividad_selec, id_persona, asignado);
		});

		$('#tabla_estados tbody').on('click', 'tr span.notificar', function () {
			const { estado } = myTable.row($(this).parent()).data();
			activar_notificacion(estado, actividad_selec, id_persona);
		});

		$('#tabla_estados tbody').on('click', 'tr span.no_notificar', function () {
			const { estado } = myTable.row($(this).parent()).data();
			desactivar_notificacion(estado, actividad_selec, id_persona);
		});

		const activar_notificacion = (estado, actividad, persona) => {
			consulta_ajax(`${ruta}activar_notificacion`, { estado, actividad, persona }, ({ mensaje, tipo, titulo }) =>
				listar_estados(actividad)
			);
		};

		const desactivar_notificacion = (estado, actividad, persona) => {
			consulta_ajax(
				`${ruta}desactivar_notificacion`,
				{ estado, actividad, persona },
				({ mensaje, tipo, titulo }) => listar_estados(actividad)
			);
		};

		const asignar_estado = (estado, actividad, persona) => {
			consulta_ajax(`${ruta}asignar_estado`, { estado, actividad, persona }, ({ mensaje, tipo, titulo }) =>
				listar_estados(actividad)
			);
		};

		const quitar_estado = (estado, actividad, persona, id) => {
			consulta_ajax(`${ruta}quitar_estado`, { estado, actividad, persona, id }, ({ mensaje, tipo, titulo }) =>
				listar_estados(actividad)
			);
		};
	});
};

const campos_modificar_prestamo = () => {
	$('#modificar_prestamo').append(
		'<td class="ttitulo">Modificar Valor:</td><td class="mod_valor" colspan="2"><div class="input-group" style="width:100%"><span class="input-group-addon"><strong>$</strong></span><input type="number" id="txtmodificar_valor" name="modificar_valor" class="form-control text-center" placeholder="Digite Valor"></div></td><td class="ttitulo">Modificar Cuotas:</td><td class="mod_cuotas" colspan="2"><div class= "input-group" style = "width:100%" > <input type="number" id="txtmodificar_cuotas" name="modificar_cuotas" min="1" max="10" class="form-control text-center" placeholder="Digite # de Cuotas "></td >'
	);
};

const campo_volante = () => {
	$('#volante_matricula').html(
		'<div class="input-group "><label class="input-group-btn"><span class="btn btn-primary"><span class="fa fa-folder-open"></span> Buscar <input name="volante" type="file" ="display: none;" id="volante"></span></label><input type="text" class="form-control" readonly placeholder="Volante de Matrícula"></div>'
	);
};

const calcular_saldo_disponible = () => {
	let saldo = $('#txtsalario').val() * 1.5;
	saldo -= calcular_saldo_pendiente();
	return saldo;
};

const calcular_saldo_pendiente = () => {
	let saldo = 0;
	descuentos.forEach(({ deuda }) => {
		if ($.isNumeric(deuda)) saldo += parseInt(deuda);
	});
	return saldo;
};

const msj_confirmacion_input = (title, text, inputPlaceholder, callback) => {
	swal(
		{
			title,
			text,
			type: 'input',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Aceptar!',
			cancelButtonText: 'No, Regresar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder
		},
		(msj) => {
			msj
				? callback(msj)
				: MensajeConClase(`El campo ${inputPlaceholder} no debe estar vacío.`, 'info', 'Ooops!');
		}
	);
};

const gestionar_solicitud = (data, callback = () => { }) => {
	consulta_ajax(`${ruta}gestionar_solicitud`, data, (resp) => {
		const { type } = data;
		const { mensaje, titulo, tipo, msj } = resp;
		const { perfil, vista } = datos_vista;
		if (tipo === 'success') {
			swal.close();
			callback(resp);
			if (type == 'Hum_Pres') {
				if (msj) data.msj = msj;
				send_mail(data);
			}
		} else MensajeConClase(mensaje, tipo, titulo);
		vista == 'talento_humano' ? listar_solicitudes() : listar_solicitudes_csep();
	});
};

const cambiar_estado = (id, state, msj = '') => {
	consulta_ajax(`${ruta}cambiar_estado`, { id, state }, ({ mensaje, titulo, tipo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') listar_solicitudes();
	});
};

const send_mail = async (data) => {
	let id_tipo_solicitud = 'Hum_Pres';
	const { id, nextState, msj, info } = data;
	const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
	let asunto,
		mensaje,
		tipo = 3,
		nombre = 'FUNCIONARIO',
		correo = '';
	switch (nextState) {
		case 'Tal_Env':
			asunto = 'Solicitud de Prestamo Registrada';
			mensaje = `<p>Se le informa que su solicitud ha sido <strong>ENVIADA</strong> satisfactoriamente y ser&aacute; revisada por el personal de Talento Humano, A partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra su solicitud.<br><br>M&aacutes informaci&oacuten en: ${ser}.<p>`;
			tipo = -1;
			correo = 'solicitante';
			break;
		case 'Tal_Neg':
			asunto = 'Solicitud de Prestamo Rechazada';
			mensaje = `<p>Se le informa que su solicitud ha sido <strong>RECHAZADA</strong>.</p>
				<p><strong>Motivo de Rechazo:</strong> ${msj}.</p>
				<p>Puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra su solicitud.<br><br>M&aacutes informaci&oacuten en: ${ser}.</p>`;
			nombre = info_persona.nombre;
			correo = info_persona.correo;
			tipo = 1;
			break;
		case 'Tal_Des':
			asunto = 'Solicitud de Prestamo Aprobada';
			mensaje = `<p>Se le informa que su solicitud ha sido <strong>APROBADA</strong>. El dinero ha sido desembolsado en su cuenta. Puede ingresar al aplicativo AGIL para obtener mas informaci&oacuten de su solicitud.<br><br>M&aacutes informaci&oacuten en: ${ser}.</p>`;
			nombre = info_persona.nombre;
			correo = info_persona.correo;
			tipo = 1;
			break;
		case 'Tal_Rev':
		case 'Tal_Vis':
		case 'Tal_Mal':
			asunto = 'Solicitud de Prestamo Revisada';
			mensaje = `<p>Se le informa que una solicitud ha sido <strong>REVISADA</strong> y est&aacute lista para ser gestionada por usted. Puede ingresar al aplicativo AGIL para obtener mas informaci&oacuten de su solicitud.<br><br>M&aacutes informaci&oacuten en: ${ser}.</p>`;
			correo = await traer_correos_responsables_estado(nextState, id_tipo_solicitud);
			break;
		case 'Tal_Apr':
		case 'Tal_Pro':
			asunto = 'Solicitud de Prestamo Aprobada';
			mensaje = `<p>Se le informa que una solicitud ha sido <strong>APROBADA</strong> y est&aacute lista para ser gestionada por usted. Puede ingresar al aplicativo AGIL para obtener mas informaci&oacuten de su solicitud.<br><br>M&aacutes informaci&oacuten en: ${ser}.</p>`;
			correo = await traer_correos_responsables_estado(nextState, id_tipo_solicitud);
			break;
		case 'Tal_Cru':
			asunto = 'Solicitud de Cruce de Matr&iacutecula Aprobada';
			mensaje = `<p>Se le informa que una solicitud de cruce de matr&iacute ha sido <strong>APROBADA</strong> y est&aacute lista para ser gestionada por usted. Puede ingresar al aplicativo AGIL para obtener mas informaci&oacuten de su solicitud.<br><br>M&aacutes informaci&oacuten en: ${ser}.</p>`;
			correo = await traer_correos_responsables_estado(nextState, id_tipo_solicitud);
			break;
		case 'Tal_Pro':
			asunto = 'Solicitud de Prestamo Procesada';
			mensaje = `<p>Se le informa que una solicitud de cruce de matr&iacute ha sido <strong>PROCESADA</strong> y est&aacute lista para ser gestionada por usted. Puede ingresar al aplicativo AGIL para obtener mas informaci&oacuten de su solicitud.<br><br>M&aacutes informaci&oacuten en: ${ser}.</p>`;
			correo = await traer_correos_responsables_estado(nextState, id_tipo_solicitud);
			break;
		case 'Tal_Tra':
			asunto = 'Solicitud de Prestamo Contabilizada';
			mensaje = `<p>Se le informa que una solicitud de cruce de matr&iacutecula ha sido <strong>CONTABILIZADA</strong> y est&aacute lista para ser gestionada por usted. Puede ingresar al aplicativo AGIL para obtener mas informaci&oacuten de su solicitud.<br><br>M&aacutes informaci&oacuten en: ${ser}.</p>`;
			correo = await traer_correos_responsables_estado(nextState, id_tipo_solicitud);
			break;
	}
	if (correo)
		enviar_correo_personalizado('th', mensaje, correo, nombre, 'AGIL Talento Humano CUC', asunto, 'Par_TH', tipo);
};

const revisar_solicitud = (salario, saldo) => {
	const cupo = calcular_saldo(salario);
	let data = {
		id: id_solicitud,
		cupo,
		saldo,
		salario,
		descuentos,
		type: 'Hum_Pres',
		nextState: 'Tal_Rev',
		success: 'Solicitud Revisada Exitosamente!'
	};
	gestionar_solicitud(data, () => {
		$('#modal_revisar_prestamo').modal('hide');
		$('#form_descuentos').get(0).reset();
		$('#modal_revisar_prestamo input').val('');
		descuentos = [];
		llenar_tabla(descuentos);
	});
};

const procesar = (id) => {
	$('#modal_enviar_archivos #footermodal').html(
		'<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>'
	);
	let data = {
		id,
		type: 'Hum_Pres'
	};
	if (tipo_proceso == 1) {
		data.nextState = 'Tal_Pro';
		data.success = 'Solicitud Procesada Exitosamente!';
	} else if (tipo_proceso == 2) {
		data.nextState = 'Tal_Tra';
		data.success = 'Solicitud Contabilizada Exitosamente!';
	}
	gestionar_solicitud(data, () => {
		if (num_archivos != 0) {
			tipo_cargue = 2;
			$('#id_archivo').val(id);
			myDropzone.processQueue();
		}
		$('#form_gestionar_solicitud').get(0).reset();
		$('#modal_gestionar_solicitud').modal('hide');
	});
};

const msj_confirmacion = (title, text, callback) => {
	swal(
		{
			title,
			text,
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si!',
			cancelButtonText: 'No!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(confirm) => {
			if (confirm) callback();
		}
	);
};

const adjuntar_archivo = () => {
	Dropzone.options.Subir = {
		url: `${ruta}cargar_archivo_solicitud`, //se especifica cuando el form no tiene el aributo action, por de fault toma la url del action en el formulario
		method: 'post', //por defecto es post se puede poner get, put, etc.....
		withCredentials: false,
		parallelUploads: 20, //Cuanto archivos subir al mismo tiempo
		uploadMultiple: false,
		maxFilesize: 1000, //Maximo Tamaño del archivo expresado en mg
		paramName: 'file', //Nombre con el que se envia el archivo a nivel de parametro
		createImageThumbnails: true,
		maxThumbnailFilesize: 1000, //Limite para generar imagenes (Previsualizacion)
		thumbnailWidth: 154, //Medida de largo de la Previsualizacion
		thumbnailHeight: 154, //Medida alto Previsualizacion
		filesizeBase: 1000,
		maxFiles: 20, //si no es nulo, define cuantos archivos se cargaRAN. Si se excede, se llamar� el EVENTO maxfilesexceeded.
		params: {}, //Parametros adicionales al formulario de envio ejemplo {tipo:"imagen"}
		clickable: true,
		ignoreHiddenFiles: true,
		acceptedFiles: 'image/*,application/.odt,.doc,.docx,.odp,.ppt,.ods,.xls,.xlsx,.pdf,.csv,.gz,.gzip,.rar,.zip', //EJEMPLO PARA PDF WORD ETC ,application/pdf,.psd,.DOCX",
		acceptedMimeTypes: null, //Ya no se utiliza paso a ser AceptedFiles
		autoProcessQueue: false, //True sube las imagenes automaticamente, si es false se tiene que llamar a myDropzone.processQueue(); para subirlas
		error: (response) => {
			if (!response.xhr) {
				MensajeConClase(
					'Solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!',
					'info',
					'Oops!'
				);
			} else {
				errores.push(response.xhr.responseText);
				if (num_archivos_cargados == num_archivos) {
					tipo_cargue == 1
						? MensajeConClase(
							'La solicitud fue guardada con exito, pero ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!',
							'info',
							'Oops!'
						)
						: MensajeConClase(
							'Ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!',
							'info',
							'Oops!'
						);
				}
			}
		},
		success: (file, response) => {
			let errorlist = 'No ingresa';
			if (errores.length > 0) {
				errorlist = '';
				errores.forEach((error) => (errorlist += `${error},`));
				tipo_cargue == 1
					? MensajeConClase(
						'La solicitud fue guardada con exito, pero algunos Archivos No fueron cargados:\n\n' +
						errorlist +
						'\n \n solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!',
						'info',
						'Oops!'
					)
					: MensajeConClase(
						'Algunos Archivos No fueron cargados:\n\n' +
						errorlist +
						'\n \n solo se permite cargar archivos con formato gif,jpg,jpeg,png,odt,doc,docx,odp,ppt,ods,xls,pdf,csv,gz,gzip,rar,zip.!',
						'info',
						'Oops!'
					);
			} else {
				tipo_cargue == 1
					? MensajeConClase(
						'La solicitud fue Guardada con exito y Todos Los archivos fueron cargados.!',
						'success',
						'Proceso Exitoso!'
					)
					: MensajeConClase('Todos Los archivos fueron cargados.!', 'success', 'Proceso Exitoso!');
			}
		},

		init: function () {
			num_archivos = 0;
			myDropzone = this;
			this.on('addedfile', (file) => num_archivos++);
			this.on('removedfile', (file) => num_archivos--);
			myDropzone.on('complete', (file) => {
				myDropzone.removeFile(file);
				num_archivos_cargados++;
			});
		},
		autoQueue: true,
		addRemoveLinks: true, //Habilita la posibilidad de eliminar/cancelar un archivo. Las opciones dictCancelUpload, dictCancelUploadConfirmation y dictRemoveFile se utilizan para la redacción.
		previewsContainer: null, //define donde mostrar las previsualizaciones de archivos. Puede ser un HTMLElement liso o un selector de CSS. El elemento debe tener la estructura correcta para que las vistas previas se muestran correctamente.
		capture: null,
		dictDefaultMessage: 'Arrastra los archivos aqui para subirlos',
		dictFallbackMessage: 'Su navegador no soporta arrastrar y soltar para subir archivos.',
		dictFallbackText: 'Por favor utilize el formuario de reserva de abajo como en los viejos timepos.',
		dictFileTooBig: 'La imagen revasa el tamaño permitido ({{filesize}}MiB). Tam. Max : {{maxFilesize}}MiB.',
		dictInvalidFileType: 'No se puede subir este tipo de archivos.',
		dictResponseError: 'Server responded with {{statusCode}} code.',
		dictCancelUpload: 'Cancel subida',
		dictCancelUploadConfirmation: '¿Seguro que desea cancelar esta subida?',
		dictRemoveFile: 'Eliminar archivo',
		dictRemoveFileConfirmation: null,
		dictMaxFilesExceeded: 'Se ha excedido el numero de archivos permitidos.'
	};
};

const listar_archivos_adjuntos = (id) => {
	$('#tabla_adjuntos_th tbody')
		.off('click', 'tr .eliminar')
		.off('click', 'tr .remover')
		.off('click', 'tr .seleccionar')
		.off('click', 'tr');
	consulta_ajax(`${ruta}listar_archivos_adjuntos`, { id }, (resp) => {
		resp.length ? $('#btnver_adjuntos').show() : $('#btnver_adjuntos').css('display', 'none');
		const table = $('#tabla_adjuntos_th').DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					render: (data, type, full, meta) => {
						return `<a class='sin-decoration' href='${Traer_Server()}${ruta_archivos_solicitudes}${full.nombre_archivo}' target='_blank'><span style='background-color: white;color: black; width: 100%;' class='pointer form-control'>ver</span></a>`;
					}
				},
				{ data: 'nombre_real' },
				{ data: 'fecha_registra' },
				{ data: 'solicitante' }
			],
			language: idioma,
			dom: 'Bfrtip',
			buttons: []
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tabla_adjuntos_th tbody').on('click', 'tr', function () {
			$('#tabla_adjuntos_th tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_adjuntos_th tbody').on('click', 'tr .eliminar', function () {
			let { id } = table.row($(this).parent().parent()).data();
			eliminar_adjunto_solicitud(id);
		});
		$('#tabla_adjuntos_th tbody').on('click', 'tr .seleccionar', function () {
			let data = table.row($(this).parent().parent()).data();
			enviar_adjuntos.push(data);
			let pintar =
				'<span class="fa fa-check-square-o red btn btn-default pointer" style="background-color:#eee" disabled="disabled"></span> <span class="fa fa-remove btn btn-default remover" style="color:#cc0000"></span>';
			$(this).parent().html(pintar);
		});
		$('#tabla_adjuntos_th tbody').on('click', 'tr .remover', function () {
			let data = table.row($(this).parent().parent()).data();
			let pintar =
				'<span class="fa fa-check-square-o red btn btn-default pointer seleccionar"></span> <span class="fa fa-remove btn btn-default" style="color:#cc0000; background-color:#eee" disabled="disabled"></span>';
			$(this).parent().html(pintar);
			enviar_adjuntos.forEach((key, indice) => {
				if (key.id == data.id) {
					enviar_adjuntos.splice(indice, 1);
					return;
				}
			});
		});
	});
};

const imprimir_solicitud = () => {
	let imprimir = document.querySelector('#modal_detalle_solicitud_prestamo');
	imprimirDIV(imprimir);
};
const imprimir_ecargo = () => {
	let imprimir = document.querySelector('#modal_detalle_ecargo');
	imprimirDIV(imprimir);
};

const traer_correos_responsables_estado = (state, tipo) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}traer_correos_responsables_estado`, { state, tipo }, (data) => resolve(data));
	});
};

const buscar_ultima_postulacion = (id, tipo = 1) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}buscar_ultima_postulacion`, { id, tipo }, (resp) => resolve(resp));
	});
};

const mostrar_info_tipo = async (
	id,
	container = '#msj_tipo_cambio',
	form = '#form_asignar_postulante',
	para_cambio = '.container_tip_Ca',
	cont_nue = `#cont_nuevos`
) => {
	let { perfil } = datos_vista;
	$(container).html(``);
	$('#btn_ver_postulacion').remove();
	let tipo = $(`${form} select[name='id_tipo']`).val();
	if (tipo == 'Tip_Cam' || tipo == 'Tip_Cam_Plan') {
		let postulacion = await buscar_ultima_postulacion(id);
		if (postulacion.length != 0) {
			$(`${para_cambio}`).css('display', 'none');
			$(`${para_cambio} select`).removeAttr('required', 'true');
			$(container).html(
				`<span id="btn_ver_postulacion" class="pointer"> <span class="fa fa-folder-open red"></span>Clic aquí para ver el contrato actual.</span>`
			);
			if (tipo == 'Tip_Cam_Plan') {
				$(`${cont_nue}`).css('display', 'none');
				$(`${cont_nue} select`).removeAttr('required', 'true');
				$(`${cont_nue} input`).removeAttr('required', 'true');
			} else {
				$(`${cont_nue}`).show('fast');
				$(`${cont_nue} select`).attr('required', 'true');
				$(`${cont_nue} input`).attr('required', 'true');
			}
			$('#btn_ver_postulacion').click(() => {
				ver_detalle_postulante_solicitud(
					postulacion,
					'#tabla_detalle_postulante',
					'#modal_detalle_postulante',
					'',
					true,
					true
				);
			});
		} else {
			$(`${para_cambio}`).show('fast');
			$(`${para_cambio} select`).attr('required', 'true');
			if (tipo == 'Tip_Cam_Plan') {
				$(`${cont_nue}`).css('display', 'none');
				$(`${cont_nue} select`).removeAttr('required', 'true');
				$(`${cont_nue} input`).removeAttr('required', 'true');
			} else {
				$(`${cont_nue}`).show('fast');
				$(`${cont_nue} select`).attr('required', 'true');
				$(`${cont_nue} input`).attr('required', 'true');
			}
			MensajeConClase(
				'El postulante seleccionado no registra un proceso de contratación  en AGIL, por favor especificar la información actual del postulante.',
				'info',
				'Oops.!'
			);
		}
	} else {
		$(`${para_cambio}`).css('display', 'none');
		$(`${para_cambio} select`).removeAttr('required', 'true');
		$(`${cont_nue}`).show('fast');
		$(`${cont_nue} select`).attr('required', 'true');
		$(`${cont_nue} input`).attr('required', 'true');
	}
	$(`${form}  input[name='prueba_psicologia']`).removeAttr('required', 'true');
	$(`${form} input[name='hoja_vida']`).removeAttr('required', 'true');
};
const listar_tipos_postulantes = async () => {
	let { perfil, vista } = datos_vista;
	let datos = await obtener_valores_parametros_gen(71);
	if (vista != 'talento_humano' && perfil != 'Per_Admin' && perfil != 'Per_Csep') {
		datos.forEach((element, indice) => {
			if (element.id_aux == 'Tip_Nue') {
				datos.splice(indice, 1);
				return true;
			}
		});
	}
	pintar_datos_combo_gen(datos, '.cbx_tipo_postulante', 'Seleccione Tipo', '', 'id_aux');
};

const obtener_postulacion = (id) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}obtener_postulacion_id`, { id }, (resp) => {
			resolve(resp);
		});
	});
};
const abrir_postulacion = async (id) => {
	let data = await obtener_postulacion(id);
	ver_detalle_postulante_solicitud(data, '#tabla_detalle_postulante', '#modal_detalle_postulante', 'comite');
	listar_postulantes_por_comite(data.id_comite);
};

const mostrar_csep = () => {
	const { perfil } = datos_vista;
	consulta_ajax(`${ruta}cargar_permisos`, {}, (resp) => {
		const res = resp.find(({ tipo }) => tipo === 'Hum_Csep');
		if (res || perfil == 'Per_Admin' || perfil == 'Per_Admin_Tal') {
			$('#btn_csep')
				.removeClass()
				.addClass('black-color pointer btn btn-default')
				.html('<span class="fa fa-group red" ></span> CSEP');
			$('#capa_csep').html(`<div id="agregar_csep" class="capa_csep">
				<div class="thumbnail">
					<div class="caption">
						<img src="${Traer_Server()}/imagenes/Personas.png" alt="...">
						<span class="btn  form-control btn-Efecto-men">Nuevo CSEP</span>
					</div>
				</div>
			</div>`);
			$('#agregar_csep').off().click(() => administrar_modulo('agregar_csep'));
		}
	});
};

const mostrar_requisicion = () => {
	const { perfil } = datos_vista;
	consulta_ajax(`${ruta}cargar_permisos`, {}, (permisos) => {
		const res = permisos.filter(({ tipo }) => tipo === 'Hum_Prec' || tipo === 'Hum_Admi' || tipo === 'Hum_Posg');
		if (res.length > 0 || perfil == 'Per_Admin') {
			$('#capa_requisicion')
				.html(`<div id="btn_precsep" data-toggle="modal" data-target="#modal_tipos_requisicion">
				<div class="thumbnail">
					<div class="caption text-center">
						<img src="${Traer_Server()}/imagenes/Inventario.png" alt="...">
						<span class="btn  form-control btn-Efecto-men">Requisición</span>
					</div>
				</div>
			</div>`);
		}
	});
};

const config_btn_contratado = (tipo = 1) => {
	let cerrar = `<button type="button" class="btn btn-default active" data-dismiss="modal" style="float: right;"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`;
	let terminar = `<button type="button" id="btn_pic" class="btn btn-danger active red"><span class="fa fa-check"></span> Terminar</button>`;
	$('#modal_detalle_postulante #footermodal').html(tipo ? cerrar : `${terminar} ${cerrar}`);
};

const detalle_vacante = (data) => {
	const { id, solicitante, fecha_registro, state } = data;
	get_detalle_vacante(id, ({ vacante, subjects, programs }) => {
		const {
			t_solicitud,
			tipo_vacante,
			t_vacante,
			cargo,
			horas,
			pregrado,
			posgrado,
			linea_investigacion,
			anos_experiencia,
			experiencia_laboral,
			fullname,
			tipo_solicitud,
			observaciones,
			hoja_vida,
			departamento,
			plan_trabajo,
			id_tipo_solicitud,
			duracion_contrato,
			nombre_tipo_contrato,
			vb_pedagogico
		} = vacante;
		if (observaciones) {
			$('.tr_observaciones').show('fast');
			$('.info_observaciones').html(observaciones);
		} else {
			$('.tr_observaciones').css('display', 'none');
			$('.info_observaciones').html('');
		}
		$('#botones_detalle')
			.html('<span class="btn btn-default estados"><span class="fa fa-tasks red"></span> Estados</span> ')
			.show('fast');
		$('#botones_detalle span.btn.btn-default.estados').click(() => {
			$('#modal_estados').modal();
			get_estados_solicitud(id);
		});
		$('#row_adm').addClass('oculto');
		$('.info_solicitante').html(solicitante);
		$('.info_fecha').html(fecha_registro);
		$('.info_estado').html(`<strong>${state}</strong>`);
		$('.info_t_solicitud').html(t_solicitud);
		$('.info_t_vacante').html(t_vacante);
		$('.info_cargo').html(cargo);
		$('.info_departamento').html(departamento);
		$('.det_req_aca').show('fast');
		$('.tabla_experiencia').css('display', 'none');
		$('.info_experiencia').html(experiencia_laboral);
		$('.info_conocimientos').html(plan_trabajo);
		$('#tr_departamento').css('display', 'none');
		$('.det_req').css('display', 'none');
		if (tipo_solicitud === 'Tcsep_Con') {
			$('#tabla_apertura').show('fast');
			$('.info_pregrado').html(pregrado);
			$('.info_posgrado').html(posgrado);
		} else if (tipo_solicitud === 'Tcsep_Eva') {
			$('#botones_detalle').append(
				`<a class="btn btn-default" href="${Traer_Server()}${ruta_hojas}${hoja_vida}" target="_blank"><i class="fa fa-address-book-o red"></i> Hoja de Vida</a> `
			);
			$('#tabla_apertura').hide('fast');
		}
		if (id_tipo_solicitud === 'Hum_Admi') {
			$('.t_detalle').html('Departamento');
			$('.info_horas').html(departamento);
			$('.tabla_experiencia').show('fast');
			$('.info_experiencia').html(experiencia_laboral);
			$('.info_conocimientos').html(plan_trabajo);
			$('.info_tipo_contrato').html(nombre_tipo_contrato);
			$('.info_duracion_contrato').html(duracion_contrato);
			$('#row_adm').removeClass('oculto');
		} else if (id_tipo_solicitud === 'Hum_Apre') {
			$('#tabla_apertura, .det_req_aca').css('display', 'none');
			$('.det_req').show();
		} else {
			$('#tr_departamento').show('fast');
			$('.t_detalle').html('Horas de Clases');
			$('.info_horas').html(`${horas} horas`);
		}
		$('#tr_reemplazo').hide();
		if (tipo_vacante === 'Vac_Ree') {
			$('#tr_reemplazo').show();
			$('.info_reemplazo').prop('required', true).html(fullname);
		} else $('.info_reemplazo').prop('required', false);
		if (linea_investigacion && anos_experiencia) {
			$('#tabla_investigacion').show();
			$('.info_investigacion').html(linea_investigacion);
			$('.info_experiencia').html(`${anos_experiencia} años`);
		} else $('#tabla_investigacion').hide();
		if (plan_trabajo) {
			$('#tr_plan_trabajo').show();
			$('.info_plan_trabajo').html(plan_trabajo);
		} else $('#tr_plan_trabajo').hide();
		if (id_tipo_solicitud === 'Hum_Prec') {
			let vb = '';
			if (vb_pedagogico == 1) vb = 'Aprobado';
			else if (vb_pedagogico == 0) vb = 'Desaprobado';
			$('#tr_vb_pedagogico').show();
			$('.info_vb_pedagogico').html(vb);
		} else {
			$('#tr_vb_pedagogico').hide();
			$('.info_vb_pedagogico').html('');
		}
		$('#materias_dependencias').html('');
		if (subjects.length) mostrar_btn_materias(subjects);
		if (programs.length) mostrar_btn_programas(programs);
		$('#modal_detalle_vacante').modal();
	});
};

const get_estados_solicitud = (id) => {
	consulta_ajax(`${ruta}get_estados_solicitud`, { id }, (data) => {
		let num = 0;
		$('#tabla_historial_estados_requisicion').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [{ render: () => ++num }, { data: 'estado' }, { data: 'fecha' }, { data: 'fullname' }],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});
	});
};

const get_estados_ecargo = (id) => {
	consulta_ajax(`${ruta}get_estados_ecargo`, { id }, (data) => {
		let num = 0;
		$('#tabla_historial_estados_ecargo').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [{ render: () => ++num }, { data: 'estado' }, { data: 'fecha' }, { data: 'fullname' }, {data: 'comentario'}],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});
	});
};

const get_detalle_vacante = (id, callback, type = 0) => {
	consulta_ajax(`${ruta}get_detalle_vacante`, { id, type }, (resp) => callback(resp));
};

const mostrar_btn_materias = (data) => {
	$('#botones_detalle').append(
		`<span class="btn btn-default materias"><span class="fa fa-list red"></span> Materias</span> `
	);
	$('#botones_detalle span.btn.btn-default.materias').click(() => {
		$('#modal_materias').modal();
		let num = 0;
		$('#tabla_materias').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [{ render: () => ++num }, { data: 'materia' }],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});
	});
};

const mostrar_btn_programas = (data) => {
	$('#botones_detalle').append(
		` <span class="btn btn-default dependencias"><span class="fa fa-building red"></span> Dependencias</span>`
	);
	$('#botones_detalle span.btn.btn-default.dependencias').click(() => {
		$('#modal_programas').modal();
		let num = 0;
		$('#tabla_programas').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [{ render: () => ++num }, { data: 'nombre' }],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});
	});
};

const mostrar_info_solicitud = async (data) => {
	const { vacante, subjects, programs } = data;
	$('#form_vacante').get(0).reset();
	const {
		tipo_solicitud,
		tipo_vacante,
		id_tipo_solicitud,
		id,
		id_departamento,
		cargo_id,
		horas,
		hoja_vida,
		pregrado,
		posgrado,
		fullname,
		persona_id,
		anos_experiencia,
		linea_investigacion,
		plan_trabajo,
		experiencia_laboral,
		observaciones,
		tipo_cargo,
		nombre_cargo
	} = vacante;
	id_vacante = id;
	tipo_requisicion = tipo_cargo;
	mod_vacante = 1;
	config_modal_vacantes(tipo_cargo);
	$("#form_vacante select[name='tipo_solicitud']").val(tipo_solicitud).trigger('change');
	$("#form_vacante select[name='tipo_vacante']").val(tipo_vacante).trigger('change');
	$("#form_vacante select[name='tipo_cargo']").val(tipo_cargo);
	if (subjects.length) {
		materias = subjects;
		listar_materias();
	}
	if (programs.length) {
		programas = programs;
		cargar_programas();
	}
	let opt = null;
	if (tipo_cargo === 'Vac_Adm' || tipo_cargo === 'Vac_Apr') {
		opt = 2;
		$("#form_vacante textarea[name='experiencia']").val(experiencia_laboral);
		$("#form_vacante textarea[name='conocimientos_especificos']").val(plan_trabajo);
		$("#form_vacante textarea[name='observaciones']").val(observaciones);
		$("#form_vacante input[name='nombre_cargo']").val(nombre_cargo);
		$("#form_vacante input[name='pregrado']").val(pregrado);
		$("#form_vacante input[name='posgrado']").val(posgrado);
	} else if (tipo_cargo === 'Vac_Aca') {
		opt = 1;
		$("#form_vacante input[name='horas']").val(horas);
		$("#form_vacante textarea[name='plan_trabajo']").val(plan_trabajo);
		$("#form_vacante textarea[name='observaciones']").val(observaciones);
	}
	const dptos = await get_departamentos(tipo_cargo);
	pintar_datos_combo(dptos, '.cbxdependencias', 'Seleccione Departamento', id_departamento);
	$("#form_vacante select[name='id_programa']").val(id_departamento);
	const cargos = await listar_cargos(opt);
	pintar_datos_combo(cargos, "#form_vacante select[name='cargo_id']", 'Seleccione Cargo', cargo_id);
	if (tipo_solicitud === 'Tcsep_Con') {
		fields_apertura();
		$("#form_vacante input[name='pregrado']").val(pregrado);
		$("#form_vacante input[name='posgrado']").val(posgrado);
		$('.div_evaluacion').hide('fast').html('');
	} else if (tipo_solicitud === 'Tcsep_Eva') {
		fields_evaluacion_candidato();
		$('#form_vacante #hoja_vida').val(hoja_vida);
		$('#div_apertura').hide('fast').html('');
	}
	if (tipo_vacante === 'Vac_Ree') {
		fields_reemplazo();
		$('#txt_nombre_persona').val(fullname);
		id_persona = persona_id;
	} else $('#div_persona').hide().html('');
	if (anos_experiencia && linea_investigacion) {
		fields_investigacion();
		$('#checkinvestigacion').attr('checked', true);
		$('#info_investigacion').show();
		$("#form_vacante input[name='linea_investigacion']").val(linea_investigacion);
		$("#form_vacante input[name='anos_experiencia']").val(anos_experiencia);
	} else {
		$('#checkinvestigacion').attr('checked', false);
		$('#info_investigacion').hide();
	}
};

const fields_apertura = () => {
	$('#div_apertura')
		.html(
			`
		<input id="txtpregrado" type="text" class="form-control apertura_input" placeholder="Pregrado Requerido" name="pregrado">
		<input id="txtpregrado" type="text" class="form-control apertura_input" placeholder="Posgrado Requerido" name="posgrado">
		<label style="padding-top: 10px;"><input id="checkinvestigacion" type="checkbox"> ¿Experiencia en Investigación?</label>
		<div id="info_investigacion" class="div_oculto" hidden></div>
	`
		)
		.show();
	$('#checkinvestigacion').off('click').click(function () {
		$(this).is(':checked') ? fields_investigacion() : $('#info_investigacion').html('').hide('fast');
	});
};

const fields_evaluacion_candidato = () => {
	let field = '';
	switch (tipo_requisicion) {
		case 'Vac_Aca':
			field = '.req_aca .div_evaluacion';
			break;
		case 'Vac_Adm':
		case 'Vac_Apr':
			field = '.req_adm .div_evaluacion';
			break;
	}
	if (field) {
		$('.div_evaluacion').html('');
		$(field)
			.html(
				`
			<div class="agrupado">
				<div class="input-group">
					<label class="input-group-btn">
						<span class="btn btn-primary">
							<span class="fa fa-folder-open"></span>Buscar
							<input name="hoja_vida" type="file" style="display: none;" class="evaluacion_input">
						</span>
					</label>
					<input type="text" id="hoja_vida" class="form-control" readonly placeholder='Hoja de vida' required>
				</div>
			</div>
		`
			)
			.show('fast');
		activarfile();
	} else {
		$('#form_vacante select[name=tipo_solicitud]').val('');
		MensajeConClase('Por favor seleccione un tipo de Cargo', 'info', 'Ooops!');
	}
};

const fields_reemplazo = () => {
	$('#div_persona')
		.html(
			`
		<div class="input-group">
			<input type="text" class="form-control sin_margin sin_focus" placeholder="Seleccione Persona" required id='txt_nombre_persona' readonly>
			<span class="input-group-addon pointer" id='btn_buscar_persona' style='background-color:white'><span class='fa fa-search red'></span> Reemplazado</span>
		</div>
	`
		)
		.show('slow');
	$('#btn_buscar_persona').off('click').click(() => {
		container_activo = '#txt_nombre_persona';
		$('#txt_dato_buscar').val('');
		callbak_activo = (resp) => mostrar_postulante_sele(resp, 'show');
		buscar_postulante('', callbak_activo);
		$('#modal_buscar_postulante').modal();
	});
};

const fields_investigacion = () => {
	$('#info_investigacion')
		.html(
			`
			<input id="txtlinea_investigacion" type="text" class="form-control" placeholder="Linea de Investigacion" name="linea_investigacion" required>
			<input type="number" min='1' class="form-control" placeholder="Años de Experiencia" name="anos_experiencia" required>
		`
		)
		.show('fast');
};

const buscar_dependencia = (dep = '', callback) => {
	consulta_ajax(`${ruta}buscar_dependencia`, { dep }, (data) => {
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

		$('#tabla_dependencia tbody').on('click', 'tr', function () {
			$('#tabla_dependencia tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_dependencia tbody').on('dblclick', 'tr', function () {
			const data = myTable.row($(this).parent().parent()).data();
			callback(data);
		});

		$('#tabla_dependencia tbody').on('click', 'tr span.asignar', function () {
			const data = myTable.row($(this).parent().parent()).data();
			callback(data);
		});

		$('#tabla_dependencia tbody').on('click', 'tr span.agregar', function () {
			const data = myTable.row($(this).parent().parent()).data();
			agregar_dependencia(data);
		});
	});
};

const asignar_dependencia = ({ id, nombre }) => {
	departamento_id = id;
	$("#form_seleccion input[name='dependencia']").val(nombre);
	$('#modal_buscar_departamento').modal('hide');
};

const add_dependencia = ({ id, nombre }) => {
	const dep = programas.find((element) => element.id === id);
	if (!dep) {
		programas.push({ id, nombre });
		cargar_programas();
		$('#modal_buscar_departamento').modal('hide');
	} else MensajeConClase('Este departamento ya ha sido asignado.', 'info', 'Ooops!');
};

const cargar_programas = () => {
	$('#cbxprogramas').html(`<option value="">${programas.length} Programas/Dependencia Asignados</option>`);
	programas.forEach(({ id, nombre }) => $('#cbxprogramas').append(`<option value=${id}>${nombre}</option>`));
};

const listar_materias = () => {
	let i = 0;
	$('#materias_asignadas').html(`<option value="">${materias.length} Materias Asignadas</option>`);
	materias.forEach(({ materia }) => $('#materias_asignadas').append(`<option value="${++i}">${materia}</option>`));
	$('#cont_materias').html(i);
};

const listar_modulos = () => {
	let i = 0;
	const select = "#form_req_posgrado select[name='modulos_asignados']";
	$(select).html(`<option value="">${materias.length} Módulos Asignados</option>`);
	materias.forEach(({ materia }) => $(select).append(`<option value="${++i}">${materia}</option>`));
	$('#cont_materias').html(i);
};

const enviar_notificaciones = async (estado, id_solicitud, id_tipo_solicitud, departamento) => {
	let titulo = '';
	let asunto = '';
	let mensaje = '';
	let detalle = '';
	let sw = true;
	switch (id_tipo_solicitud) {
		case 'Hum_Prec': //pregrado
			titulo = 'Pregrado - Requisición';
			detalle = 'detalle_req_prec';
			get_detalle_vacante(id_solicitud, async (data) => {
				const { tipo_solicitud } = data.vacante;
				if (tipo_solicitud === 'Tcsep_Eva') sw = false;
			});
			break;
		case 'Hum_Admi': //administrativos
			titulo = 'Requisición Administrativos';
			detalle = 'detalle_req_admi';
			break;
		case 'Hum_Apre': //aprendices
			titulo = 'Requisición Aprendices';
			detalle = 'detalle_req_apre';
			break;
	}
	if (estado === 'Tal_Ter') {
		get_detalle_vacante(id_solicitud, async (data) => {
			const { hoja_vida } = data.vacante;
			let link_hv = '';
			if (hoja_vida) link_hv = `<p><a href="${server}archivos_adjuntos/talentohumano/hojas_vidas/${hoja_vida}"><b>Hoja de vida Postulante</b></a></p>`;
			const ser = `<a href="${server}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
			const correos = await get_personas_notificar(id_tipo_solicitud, estado, departamento);
			asunto = 'Requisición Aprobada';
			mensaje = `<p>La ${titulo} ha sido APROBADA.</p>
			<p>Más información en: ${ser}</p>
			${link_hv}
			<p><a href="${server}archivos_adjuntos/talentohumano/detalles_solicitudes/${detalle}${id_solicitud}.pdf"><b>Detalle Requisición</b></a></p>`;
			enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', titulo, asunto, 'Par_TH', 3);

			const { correo, persona } = await get_correo_solicitante(id_solicitud);
			enviar_correo_personalizado('th', mensaje, correo, persona, titulo, asunto, 'Par_TH', 1);
		});
	} else if (estado === 'Tal_Env') {
		const ser = `<a href="${server}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
		asunto = 'Solicitud de Requisición';
		if (sw) {
			mensaje = `<p>Se le informa que su solicitud de ${titulo} ha sido <strong>ENVIADA</strong> satisfactoriamente. A partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra su solicitud.<br><br>Más información en: ${ser}.<p>`;
			const { correo, persona } = await get_correo_solicitante(id_solicitud);
			enviar_correo_personalizado('th', mensaje, correo, persona, titulo, asunto, 'Par_TH', 1);
		}
		mensaje = `<p>Se le informa que una nueva solicitud de ${titulo} ha sido <strong>ENVIADA</strong>. A partir de este momento puede ingresar al aplicativo AGIL para gestionarla.<br><br>Más información en: ${ser}.<p>`;
		const correos = await get_personas_notificar(id_tipo_solicitud, estado, departamento);
		enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', titulo, asunto, 'Par_TH', 3);

	} else if (estado === 'Tal_Esp') {
		const serv = `<a href="${server}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
		asunto = 'Solicitud de Requisición';
		mensaje = `<p>Se le informa que su solicitud de ${titulo} ha sido <strong>ENVIADA</strong> satisfactoriamente. A partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra su solicitud.<br><br>Más información en: ${serv}.<p>`;
		const { correo, persona } = await get_correo_solicitante(id_solicitud);
		enviar_correo_personalizado('th', mensaje, correo, persona, titulo, asunto, 'Par_TH', 1);

		const ser = `<a href="${server}index.php/csep/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
		asunto = 'Solicitud de Requisición';
		mensaje = `<p>Se le informa que una nueva solicitud de ${titulo} ha sido <strong>ENVIADA</strong> para visto bueno Pedagógico. A partir de este momento puede ingresar al aplicativo AGIL para gestionarla.<br><br>Más información en: ${ser}.<p>`;
		const correos = await get_personas_notificar(id_tipo_solicitud, estado, departamento);
		enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', titulo, asunto, 'Par_TH', 3);

	} else if (estado === 'Env_Csea') {
		const ser = `<a href="${server}index.php/csep/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
		asunto = 'Solicitud de Requisición';
		mensaje = `<p>Se le informa que una nueva solicitud de ${titulo} ha sido <strong>ENVIADA A POS</strong>. A partir de este momento puede ingresar al aplicativo AGIL para gestionarla.<br><br>Más información en: ${ser}.<p>`;
		const correos = await get_personas_notificar(id_tipo_solicitud, estado, departamento);
		enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', titulo, asunto, 'Par_TH', 3);
	}
};

const get_correo_solicitante = (id) => {
	return new Promise((resolve) => {
		consulta_ajax(
			`${ruta}get_correo_solicitante`, { id },
			(data) => resolve(data)
		);
	});
};

const get_correo_colaborador_ecargo = (id) => {
	return new Promise((resolve) => {
		consulta_ajax(
			`${ruta}get_correo_colaborador_ecargo`, { id },
			(data) => resolve(data)
		);
	});
};
const get_correo_jefe_inmediato2 = (id) => {
	return new Promise((resolve) => {
		consulta_ajax(
			`${ruta}get_correo_jefe_inmediato2`, { id },
			(data) => resolve(data)
		);
	});
};

const get_personas_notificar = (id_tipo_solicitud, estado, departamento) =>
	new Promise((resolve) => consulta_ajax(`${ruta}get_personas_notificar`, { actividad: id_tipo_solicitud, estado_id: estado, departamento: departamento }, (data) => resolve(data)));

const cargar_materias = () => {
	$('#modal_materias_mod').modal();
	consulta_ajax(`${ruta}cargar_materias_solicitud`, { id: id_solicitud }, (data) => {
		let num = 0;
		$('#tabla_materias_mod tbody').off('click', 'tr td .eliminar');
		const myTable = $('#tabla_materias_mod').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ render: () => ++num },
				{ data: 'materia' },
				{
					render: () =>
						`<span class="btn btn-default eliminar"><span class="fa fa-trash" style="color: #d9534f"></span></span>`
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_materias_mod tbody').on('click', 'tr td .eliminar', function () {
			let { id, materia } = myTable.row($(this).parent().parent()).data();
			eliminar_materia(id, materia);
		});
	});
};

const cargar_dependencias = () => {
	$('#modal_programas_mod').modal();
	consulta_ajax(`${ruta}cargar_programas_solicitud`, { id: id_solicitud }, (data) => {
		let num = 0;
		$('#modal_programas_mod tbody').off('click', 'tr td .eliminar');
		const myTable = $('#tabla_programas_mod').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ render: () => ++num },
				{ data: 'programa' },
				{
					render: () =>
						`<span class="btn btn-default eliminar"><span class="fa fa-trash" style="color: #d9534f"></span></span>`
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#modal_programas_mod tbody').on('click', 'tr td .eliminar', function () {
			let { id } = myTable.row($(this).parent().parent()).data();
			eliminar_dependencia(id);
		});
	});
};

const agregar_materia = () => {
	swal(
		{
			title: 'Agregar Materia',
			text: '',
			type: 'input',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Aceptar!',
			cancelButtonText: 'Cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder: `Digite nombre de la materia a agregar`
		},
		(materia) => {
			if (materia) {
				consulta_ajax(`${ruta}agregar_materia`, { id: id_solicitud, materia }, ({ mensaje, tipo, titulo }) => {
					if (tipo === 'success') {
						cargar_materias();
						swal.close();
					} else {
						MensajeConClase(mensaje, tipo, titulo);
						$('#modal_materias_mod').modal('hide');
						$('#modal_solicitud_vacante').modal('hide');
						listar_solicitudes_csep();
					}
				});
			} else MensajeConClase('Por favor digite el nombre de la materia.', 'info', 'Ooops!');
		}
	);
};

const eliminar_materia = (id_materia, materia) => {
	swal(
		{
			title: `¿Eliminar ${materia}?`,
			text: '',
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Eliminar!',
			cancelButtonText: 'No, Cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				consulta_ajax(
					`${ruta}eliminar_materia`,
					{ id: id_solicitud, materia: id_materia },
					({ mensaje, tipo, titulo }) => {
						if (tipo === 'success') {
							cargar_materias();
							swal.close();
						} else {
							$('#modal_materias_mod').modal('hide');
							$('#modal_solicitud_vacante').modal('hide');
							listar_solicitudes_csep();
							MensajeConClase(mensaje, tipo, titulo);
						}
					}
				);
			}
		}
	);
};

const agregar_dependencia = (data) => {
	swal(
		{
			title: '',
			text: `¿Desea asignar la dependencia ${data.nombre} a la solicitud?`,
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Asignar!',
			cancelButtonText: 'No, Cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				consulta_ajax(
					`${ruta}agregar_dependencia`,
					{ id: id_solicitud, dep: data.id },
					({ mensaje, tipo, titulo }) => {
						$('#modal_buscar_departamento').modal('hide');
						if (tipo === 'success') {
							cargar_dependencias();
							swal.close();
						} else {
							$('#modal_solicitud_vacante').modal('hide');
							$('#modal_programas_mod').modal('hide');
							listar_solicitudes_csep();
							MensajeConClase(mensaje, tipo, titulo);
						}
					}
				);
			}
		}
	);
};

const eliminar_dependencia = (dep) => {
	swal(
		{
			title: `¿Eliminar Dependencia?`,
			text: '',
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#D9534F',
			confirmButtonText: 'Si, Eliminar!',
			cancelButtonText: 'No, Cancelar!',
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		(isConfirm) => {
			if (isConfirm) {
				consulta_ajax(`${ruta}eliminar_dependencia`, { id: id_solicitud, dep }, ({ mensaje, tipo, titulo }) => {
					if (tipo === 'success') {
						cargar_dependencias();
						swal.close();
					} else {
						$('#modal_solicitud_vacante').modal('hide');
						$('#modal_programas_mod').modal('hide');
						listar_solicitudes_csep();
						MensajeConClase(mensaje, tipo, titulo);
					}
				});
			}
		}
	);
};

const get_estados_asignados_actividad = (actividad = '') => {
	consulta_ajax(`${ruta}get_estados_asignados_actividad`, { id: actividad }, (data) => {
		$('#estado_filtro').html('<option value="">Filtrar por Estado</option>');
		data.forEach(({ estado, nombre }) =>
			$('#estado_filtro').append(`<option value="${estado}">${nombre}</option>`)
		);
	});
};

const generar_pdf_req = id => {
	console.log("generando");
	const route = `${Traer_Server()}index.php/talento_humano/detalle_requisicion/${id}`;
	window.open(route, '_blank');
	window.focus()
	console.log("generado");
	return true;
}

const revisar_vacante = () => {
	let { perfil, vista } = datos_vista;
	const { id_estado_solicitud, id_tipo_solicitud } = info_solicitud;
	let data = new FormData(document.getElementById('form_revisar_requisicion'));
	let departamento_id = null;
	get_detalle_vacante(id_solicitud, async ({ vacante }) => {
		if (id_tipo_solicitud === 'Hum_Prec') departamento_id = vacante.id_departamento;
	});
	data.append('id', id_solicitud);
	data.append('id_tipo_solicitud', id_tipo_solicitud);
	enviar_formulario(`${ruta}revisar_vacante`, data, ({ tipo, mensaje, titulo }) => {
		if (tipo === 'success') {
			let next = 'Tal_Ter';
			if (id_tipo_solicitud === 'Hum_Prec') {
				if (id_estado_solicitud === 'Tal_Env') next = 'Env_Csea';
				else if (id_estado_solicitud === 'Env_Csea') next = 'Tal_Ter';
			}
			let callback = () => {
				if (next === 'Tal_Ter') generar_pdf_req(id_solicitud);
				materias = [];
				programas = [];
				listar_materias();
				cargar_programas();
				enviar_notificaciones(next, id_solicitud, id_tipo_solicitud, departamento_id);
				$('#form_revisar_requisicion').get(0).reset();
				$('#modal_revisar_requisicion').modal('hide');
				vista == 'talento_humano' ? listar_solicitudes() : listar_solicitudes_csep();
			};
			gestionar_solicitud(
				{
					id: id_solicitud,
					nextState: next,
					success: 'Solicitud Aprobada Exitosamente!',
					type: id_tipo_solicitud
				},
				callback
			);
		} else MensajeConClase(mensaje, tipo, titulo);
	});
};

const guardar_vacante = () => {
	$(`#form_vacante button[type='submit']`).prop("disabled", true);
	let data = new FormData(document.getElementById('form_vacante'));
	let departamento = $(`#form_vacante select[name='id_programa']`).val();
	if (id_persona) data.append('reemplazado', id_persona);
	data.append('tipo_cargo', tipo_requisicion);
	if ($('#checkinvestigacion').is(':checked')) data.append('investigacion', true);
	/*	Se valida si se va a modificar o guardar la vacante con la variable mod_vacante
		Y se envía al controlador
			true: Se modificará la solicitud
		false: se guardará una nueva solicitud
	*/
	let sw = true;
	if (mod_vacante) {
		sw = false;
		data.append('accion', mod_vacante);
		data.append('id', id_vacante);
		data.append('id_solicitud', id_solicitud);
		if (materias.length > 0) data.append('materias', JSON.stringify(materias));
	} else {
		if (materias.length > 0) data.append('materias', JSON.stringify(materias));
		if (programas.length > 0) data.append('programas', JSON.stringify(programas));
	}
	enviar_formulario(`${ruta}guardar_vacante`, data, ({ tipo, mensaje, titulo, id, id_estado }) => {
		if (tipo === 'success') {              //|| (mod_vacante && tipo != 'success')
			let id_tipo_solicitud = '';
			let estado = 'Tal_Env';
			let id_departamento = null;
			if (tipo_requisicion === 'Vac_Aca') {
				id_tipo_solicitud = 'Hum_Prec';
				id_departamento = departamento;
				if (id_estado) estado = id_estado;
			} else if (tipo_requisicion === 'Vac_Apr') id_tipo_solicitud = 'Hum_Apre';
			else id_tipo_solicitud = 'Hum_Admi';

			if (sw) enviar_notificaciones(estado, id, id_tipo_solicitud, id_departamento);
			materias = [];
			programas = [];
			listar_materias();
			cargar_programas();
			$('#form_vacante').get(0).reset();
			$('#modal_solicitud_vacante').modal('hide');
		}
		MensajeConClase(mensaje, tipo, titulo);
		const { vista } = datos_vista;
		vista == 'talento_humano' ? listar_solicitudes() : listar_solicitudes_csep();
		$(`#form_vacante button[type='submit']`).prop("disabled", false);
	});
	mod_vacante = '';
};

const guardar_solicitud_seleccion = () => {
	const data = new FormData(document.getElementById('form_seleccion'));
	data.append('id', id_persona);
	data.append('jefe_inmediato', id_persona_jefe);
	if (mod_vacante) data.append('id_solicitud', JSON.stringify(id_solicitud));
	data.append('requisicion', requisicion_id);
	data.append('accion', mod_vacante);
	data.append('departamento', departamento_id);
	enviar_formulario(`${ruta}guardar_solicitud_seleccion`, data, ({ tipo, mensaje, titulo }) => {
		if (tipo === 'success') {
			$('#modal_seleccion').modal('hide');
			$('#form_seleccion').get(0).reset();
			if (mod_vacante) listar_solicitudes();
		}
		id = '';
		MensajeConClase(mensaje, tipo, titulo);
	});
};

const guardar_solicitud_aunsentismo = () => {
	const data = new FormData(document.getElementById('form_ausentismo'));
	data.append('id_tipo_ausentismo', id_tipo_ausentismo);
	data.append('id_jefe_directo', id_persona);

	enviar_formulario(`${ruta}guardar_solicitud_ausentismo`, data, ({ tipo, mensaje, titulo }) => {
		if (tipo === 'success') {
			$('#modal_seleccion').modal('hide');
			$('#form_seleccion').get(0).reset();
			if (mod_vacante) listar_solicitudes();
		}
		id = '';
		MensajeConClase(mensaje, tipo, titulo);
	});
};

const agregar_
	= (data_candidato) => {
		// const { id, id_estado_solicitud, tipo_cargo_id } = $('#tabla_solicitudes').DataTable().row('.warning').data();
		const { id, id_estado_solicitud, tipo_cargo_id } = info_solicitud;
		consulta_ajax(
			`${ruta}agregar_candidato`,
			{
				candidato: data_candidato.id,
				solicitud: id_solicitud,
				id_estado_solicitud
			},
			({ mensaje, tipo, titulo, sw }) => {
				if (tipo === 'success') {
					$('#modal_buscar_postulante').modal('hide');
					swal.close();
					listar_candidatos(id, tipo_cargo_id);
					if (sw) listar_solicitudes();
				} else MensajeConClase(mensaje, tipo, titulo);
			}
		);
	};

const listar_candidatos = (id, tipo) => {
	consulta_ajax(
		`${ruta}listar_candidatos`,
		{
			id,
			tipo
		},
		(data) => {
			// const { id_estado_solicitud } = $('#tabla_solicitudes').DataTable().row('.warning').data();
			const { id_estado_solicitud } = info_solicitud;
			// data[1] && id_estado_solicitud != 'Tal_Ter'
			data[2] && id_estado_solicitud != 'Tal_Ter'
				? boton_carta_agradecimiento()
				: $('#boton_agradecimiento').html('');
			$(`#tabla_candidatos tbody`)
				.off('click', 'tr')
				.off('click', 'tr td:nth-of-type(1)')
				.off('dblclick', 'tr')
				.off('click', 'tr span.contratar')
				.off('click', 'tr span.procesos');
			const myTable = $('#tabla_candidatos').DataTable({
				destroy: true,
				processing: true,
				data: data[0],
				columns: [
					{
						render: (data, type, full, meta) => {
							const { contratado, proceso_actual_id } = full;
							let color = '';
							let bg_color = '';
							if (contratado == 1 || proceso_actual_id === 'Sel_Ind') {
								color = '#FFFFFF';
								bg_color = '#5cb85c';
							} else {
								color = '#000000';
								bg_color = '#FFFFFF';
							}
							if (proceso_actual_id === 'Sel_Des') {
								bg_color = '#d9534f';
								color = '#FFFFFF';
							}
							return `<span  style="background-color: ${bg_color};color: ${color}; width: 100%;" class="pointer form-control"><span >ver</span></span>`;
						}
					},
					{ data: 'fullname' },
					{ data: 'proceso_actual' },
					{ data: 'gestion' }
				],
				language: get_idioma(),
				dom: 'Bfrtip',
				buttons: []
			});

			$('#tabla_candidatos tbody').on('click', 'tr', function () {
				info_candidato = myTable.row(this).data();
				$('#tabla_candidatos tbody tr').removeClass('warning');
				$(this).attr('class', 'warning');
			});

			$('#tabla_candidatos tbody').on('dblclick', 'tr', async function () {
				const data = myTable.row(this).data();
				await detalle_candidato(data);
			});

			$('#tabla_candidatos tbody').on('click', 'tr td:nth-of-type(1)', async function () {
				const data = myTable.row($(this).parent()).data();
				await detalle_candidato(data);
			});

			$('#tabla_candidatos tbody').on('click', 'tr span.retirar', function () {
				msj_confirmacion_input('¿ Descartar Candidato ?', '', 'Motivo del Descarte', (msj) => {
					let { id } = myTable.row($(this).parent()).data();
					const data = {
						candidato: id,
						solicitud: id_solicitud,
						nextProcess: 'Sel_Des',
						success: 'Candidato descartado exitosamente!',
						msj: msj,
						callback: () => swal.close()
					};
					gestionar_candidato(data);
				});
			});

			$('#tabla_candidatos tbody').on('click', 'tr span.procesos', function () {
				$('#modal_procesos_seleccion').modal();
				const { id, tipo_seleccion, aprobacion_jefe, motivo_rechazo_jefe } = myTable.row($(this).parent()).data();
				$("#alert_jefe").html('');
				if (aprobacion_jefe == 0) {
					$('#alert_jefe').append('<div class="alert alert-info text-center" role="alert">El jefe inmediato aún no ha dado su visto bueno!.</div>');
				} else if (aprobacion_jefe == 1) {
					$('#alert_jefe').append('<div class="alert alert-success text-center" role="alert">El jefe inmediato indicó APROBADO en el visto bueno!.</div>');
				} else $('#alert_jefe').append(`<div class="alert alert-danger text-center" role="alert">El jefe inmediato indicó RECHAZADO en el visto bueno!.<br>Motivo: ${motivo_rechazo_jefe}.</div>`);

				cargar_procesos_disponibles(id_solicitud, id, tipo_seleccion);
			});

			$('#tabla_candidatos tbody').on('click', 'tr span.contratar', function () {
				const data = myTable.row($(this).parent()).data();
				msj_confirmacion('¿Aprobar Contratación del Candidato?', '', () => aprobar_contratacion(data));
			});

			$('#tabla_candidatos tbody').on('click', 'tr span.negar', function () {
				const data = myTable.row($(this).parent()).data();
				msj_confirmacion_input('¿ Rechazar Candidato ?', '', 'Motivo del Rechazo', (msj) => {
					rechazar_contratacion(data, msj);
				});
			});
		}
	);
};

const aprobar_contratacion = ({ candidatos_seleccion_id }) => {
	consulta_ajax(`${ruta}aprobar_contratacion`, { candidatos_seleccion_id }, ({ mensaje, tipo, titulo }) => {
		if (tipo === 'success') {
			MensajeConClase(mensaje, tipo, titulo);
			listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
			enviar_notificacion_seleccion('Sel_VB_Jef');
		}
	});
};

const rechazar_contratacion = ({ candidatos_seleccion_id }, motivo_rechazo) => {
	consulta_ajax(`${ruta}rechazar_contratacion`, { candidatos_seleccion_id, motivo_rechazo }, ({ mensaje, tipo, titulo }) => {
		if (tipo === 'success') {
			MensajeConClase(mensaje, tipo, titulo);
			listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
			enviar_notificacion_seleccion('Sel_VB_Jef');
		}
	});
};

const mostrar_modal_entrevista = ({ id, identificacion, nombre_completo }) => {
	id_persona = id;
	$('#form_citacion').get(0).reset();
	const data = $('#tabla_solicitudes').DataTable().row('.warning').data();
	consulta_ajax(`${ruta}detalle_solicitud`, { id: data.id, tipo: data.id_tipo_solicitud }, (resp) => {
		const { nombre_vacante, departamento, cargo } = resp;
		$("#form_citacion input[name='nombre_proceso']").val(
			`${nombre_vacante.toUpperCase()} (${cargo} - ${departamento})`
		);
	});
	$("#form_citacion input[name='identificacion']").val(identificacion);
	$("#form_citacion input[name='nombre']").val(nombre_completo);
	$('#modal_citacion').modal();
};

const cargar_ubicaciones = (lugar, input) => {
	consulta_ajax(`${ruta}cargar_ubicaciones`, { lugar }, (ubicaciones) => {
		pintar_datos_combo(ubicaciones, input, 'Seleccione Ubicación');
	});
};

const get_full_info_candidato = (id_solicitud, candidato) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}get_full_info_candidato`, { id: id_solicitud, candidato: candidato }, resolve);
	});
};

const detalle_candidato = async (data) => {
	const {
		fullname,
		identificacion,
		cargo,
		cargo_actual,
		correo,
		telefono,
		proceso_actual,
		fecha_entrevista,
		ubicacion_entrevista,
		lugar_entrevista,
		fecha_registra,
		observacion,
		hoja_vida,
		id,
		jefe_responsable,
		motivo_rechazo_jefe
	} = data;

	consulta_ajax(
		`${ruta}get_full_info_candidato`,
		{
			id: id_solicitud,
			candidato: id
		},
		async ({
			motivo_exoneracion,
			avales
		}) => {
			let motivoExoneracion = motivo_exoneracion;
			let avales_candidato = avales;
			const procesos = await get_procesos_candidato();
			if (procesos.includes('Sel_Inf') && !procesos.includes('Sel_Con')) {
				$('#btn_hv_candidato').html(
					'<button class="btn btn-default" id="btn_modificar_informe" title="Modificar Informe de Selección"><span class="fa fa-fa fa-id-card red"></span> Informe</button>'
				);
				$('#btn_modificar_informe').click(async () => {
					let { departamento,
						cargo,
						categoria_colciencias,
						indiceh,
						cvlac,
						suficiencia_ingles,
						exp_docente,
						exp_investigacion,
						exp_profesional,
						produccion,
						pruebas,
						formacion,
						concepto,
						estudios_candidato,
						competencias_candidato,
						motivo_exoneracion,
						avales } = await get_full_info_candidato(id_solicitud, id);
					motivoExoneracion = motivo_exoneracion;
					avales_candidato = avales;
					const form = '#form_generar_informe';
					$(`${form} input[name='dependencia']`).val(departamento);
					$(`${form} input[name='cargo']`).val(cargo);
					$(`${form} input[name='categoria_colciencias']`).val(categoria_colciencias);
					$(`${form} input[name='indiceh']`).val(indiceh);
					$(`${form} input[name='cvlac']`).val(cvlac);
					$(`${form} input[name='suficiencia_ingles']`).val(suficiencia_ingles);
					$(`${form} textarea[name='exp_docente']`).val(exp_docente);
					$(`${form} textarea[name='exp_investigacion']`).val(exp_investigacion);
					$(`${form} textarea[name='exp_profesional']`).val(exp_profesional);
					$(`${form} textarea[name='produccion']`).val(produccion);
					$(`${form} textarea[name='pruebas']`).val(pruebas);
					$(`${form} textarea[name='formacion']`).val(formacion);
					$(`${form} textarea[name='concepto']`).val(concepto);
					$(`${form} input[name="accion"]`).val('modify');
					estudios = estudios_candidato;
					cargar_estudios();
					competencias = competencias_candidato;
					cargar_competencias();
					$('#modal_informe').modal();
				});
			} else $('#btn_hv_candidato').html('');
			if (lugar_entrevista && ubicacion_entrevista && fecha_entrevista) {
				$('#tr_entrevista').show();
				$('.info_lugar_entrevista').html(`${lugar_entrevista} - ${ubicacion_entrevista}`);
			} else $('#tr_entrevista').hide();
			if (motivoExoneracion) {
				$('#row_exoneracion').show('fast');
				$('.info_exoneración').html(motivoExoneracion);
			} else {
				$('#row_exoneracion').css('display', 'none');
				$('.info_exoneración').html('');
			}
			$('.info_identificacion').html(identificacion);
			$('.info_cargo_candidato').html(cargo_actual);
			$('.info_telefono').html(telefono);
			$('.info_correo').html(correo);
			$('.info_fecha_entrevista').html(fecha_entrevista);
			$('.info_candidato').html(fullname);
			$('.info_proceso_actual').html(proceso_actual);
			$('.info_fecha_asignacion').html(fecha_registra);
			$('.info_jefe_inmediato').html(jefe_responsable);
			$('#btn_hv_candidato').append(
				' <button class="btn btn-default" id="btn_historial_candidatos"><span class="fa fa-vcard-o red"></span> Historial</button>'
			);
			$('#btn_historial_candidatos').click(() => {
				$('#modal_historial_candidato').modal();
				get_historial_candidato();
			});
			if (avales_candidato.length > 0) {
				$('#btn_hv_candidato').append(
					` <button class="btn btn-default adjuntos"><span class="fa fa-folder-open red"></span> Adjuntos</button>`
				);

				$('#btn_hv_candidato .adjuntos').click(async () => {
					let { avales } = await get_full_info_candidato(id_solicitud, id);
					avales_candidato = avales;
					mostrar_adjuntos(avales_candidato);
				});
			}
			let { id_estado_solicitud } = info_solicitud;
			if (id_estado_solicitud === 'Tal_Env' || id_estado_solicitud === 'Tal_Pro') {
				$('#btn_hv_candidato').append(
					' <span class="btn btn-default" id="btn_edit_candidato"><span class="fa fa-edit red"></span> Editar Candidato</span>'
				);
			}
			if (observacion) {
				$('#row_motivo').show();
				$('.info_observación').html(observacion);
			} else {
				$('#row_motivo').hide();
				$('.info_observación').html('');
			}
			if (motivo_rechazo_jefe) {
				$('#row_rechazo_jefe').show();
				$('.info_rechazo_jefe').html(motivo_rechazo_jefe);
			} else {
				$('#row_rechazo_jefe').hide();
				$('.info_rechazo_jefe').html('');
			}

			$('#btn_edit_candidato').click(async () => {
				let info = await get_full_info_candidato(id_solicitud, id);
				datos_postulante = { id: info.persona_id };
				$(`#form_modificar_postulante select[name='id_tipo_identificacion']`).val(info.id_tipo_identificacion);
				$(`#form_modificar_postulante input[name='identificacion']`).val(info.identificacion);
				$(`#form_modificar_postulante input[name='lugar_expedicion']`).val(info.lugar_expedicion);
				$(`#form_modificar_postulante select[name='genero']`).val(info.genero);
				$(`#form_modificar_postulante input[name='fecha_nacimiento']`).val(info.fecha_nacimiento);
				$(`#form_modificar_postulante input[name='apellido']`).val(info.apellido);
				$(`#form_modificar_postulante input[name='segundo_apellido']`).val(info.segundo_apellido);
				$(`#form_modificar_postulante input[name='nombre']`).val(info.nombre);
				$(`#form_modificar_postulante input[name='segundo_nombre']`).val(info.segundo_nombre);
				$(`#form_modificar_postulante input[name='correo']`).val(info.correo);
				$(`#form_modificar_postulante input[name='telefono']`).val(info.telefono);
				callbak_activo = (data) => {
					cargar_detalle_candidato_sele(data);
					listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
				}
				$('#modal_modificar_postulante').modal();
			});
			$('#modal_detalle_candidato').modal();
		}
	);
};

const cargar_detalle_candidato_sele = (data) => {
	$(`.info_candidato`).html(data.nombre_completo);
	$(`.info_identificacion`).val(data.identificacion);
	$(`.info_correo`).val(data.correo);
	$(`.info_telefono`).val(data.telefono);
}

const mostrar_adjuntos = (avales) => {
	$('#modal_adjuntos_seleccion').modal();
	$(`#tabla_descuentos tbody`).off('click', 'tr').off('click', 'tr span.eliminar');
	const myTable = $('#tabla_adjuntos_seleccion').DataTable({
		destroy: true,
		processing: true,
		searching: false,
		data: avales,
		columns: [
			{
				render: (data, type, { nombre_archivo, route }, meta) => {
					const ruta_doc = route ? `${ruta_hojas}` : `${ruta_archivos_solicitudes}`;
					return `<a href="${Traer_Server()}${ruta_doc}${nombre_archivo}" target="_blank" class='pointer form-control' style="width: 100%;text-decoration: none;">Ver</a>`;
				}
			},
			{ data: 'nombre_real' }
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: []
	});
	// <a href="${Traer_Server()}${ruta_hojas}${hoja_vida}" target="_blank"><i class="fa fa-address-book-o red"></i> Hoja de Vida</a>
};

const get_procesos_candidato = () => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}get_procesos_candidato`, { id: info_candidato.candidatos_seleccion_id }, resolve);
	});
};

const gestionar_candidato = (data) => {
	consulta_ajax(`${ruta}gestionar_candidato`, data, ({ mensaje, tipo, titulo, pruebas_seleccionadas }) => {
		if (tipo === 'success') {
			const { nextProcess } = data;
			// const { tipo_seleccion } = $('#tabla_solicitudes').row('.warning').data();
			const { tipo_seleccion } = info_solicitud;
			listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
			cargar_procesos_disponibles(id_solicitud, info_candidato.id, tipo_seleccion);
			if (nextProcess === 'Sel_Psi' && pruebas_seleccionadas.length > 0)
				enviar_correo_seleccion(nextProcess, pruebas_seleccionadas); // Enviar Correo Pruebas Psicotécnicas
			if (nextProcess === 'Sel_Doc') enviar_correo_seleccion(nextProcess); // Enviar Correo Solicitud de Documentos
			if (nextProcess === 'Sel_Con') enviar_correo_seleccion(nextProcess, data); // Enviar Correo Contratación
			if (nextProcess === 'Sel_Sol_VB') enviar_correo_seleccion(nextProcess); // Enviar Correo solicitud visto bueno jefe
			if (nextProcess === 'Sol_Sel_Con') enviar_correo_seleccion(nextProcess); // Enviar Correo solicitud visto bueno jefe th
			if (nextProcess === 'Sel_CPre' || nextProcess === 'Sel_CVir') enviar_correo_seleccion('Sel_Cse', nextProcess); // enviar correo csep
			if (typeof data.callback === 'function') callback();
		} else MensajeConClase(mensaje, tipo, titulo);
	});
};

// const validar_envio_notificacion = (persona, candidato, solicitud) => {
// 	return new Promise(resolve => {
// 		consulta_ajax(`${ruta}validar_envio_notificacion`, {
// 			persona,
// 			candidato,
// 			solicitud
// 		}, data => resolve(data))
// 	})
// }
const get_usuarios_notificar_aval = (id) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}get_usuarios_notificar_aval`, { id }, (data) => resolve(data));
	});
};

const enviar_pruebas = () => {
	const checkboxes = $('#form_enviar_pruebas input:checkbox');
	const pruebas = [];
	checkboxes.map((element) => {
		if (checkboxes[element].checked) {
			pruebas.push(parseInt(checkboxes[element].value));
		}
	});
	const data = {
		candidato: id_persona,
		solicitud: id_solicitud,
		pruebas,
		nextProcess: 'Sel_Psi',
		success: 'Pruebas Psicotécnicas enviadas exitosamente!',
		callback: () => {
			$('#modal_pruebas_psicotecnicas').modal('hide');
			$('#form_enviar_pruebas').get(0).reset();
			document.getElementById('Sel_Psi').classList.add('disabled');
			swal.close();
		}
	};
	msj_confirmacion('¿ Enviar Pruebas Psicotécnicas ?', '', () => gestionar_candidato(data));
};

const get_correo_responsable_th = (id) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}get_correo_responsable_th`, { id }, (data) => resolve(data));
	});
};

const enviar_correo_seleccion = async (proceso, data = null) => {
	const dias = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];
	const meses = [
		'Enero',
		'Febrero',
		'Marzo',
		'Abril',
		'Mayo',
		'Junio',
		'Julio',
		'Agosto',
		'Septiembre',
		'Octubre',
		'Noviembre',
		'Diciembre'
	];
	let titulo = '';
	let mensaje = '';
	let sw = true;
	let nombre = info_candidato.fullname;
	let correo = info_candidato.correo;
	if (proceso === 'Sel_Psi') {
		sw = false;
		let nombre_pruebas = '';
		let links = '';
		data.map(({ prueba, file_name, tipo, route_file_name }) => {
			if (prueba && file_name) nombre_pruebas += !nombre_pruebas ? file_name : `, ${file_name}`;
			links += `<li><a target='_blank' href='${Traer_Server()}${ruta_pruebas}${route_file_name}' style="background-color: white;color: black;width: 100%;" class="pointer form-control"><span >${file_name}</span></a></li>`;
		});
		consulta_ajax(`${ruta}get_correo_encargado`, {}, (email) => {
			titulo = 'Pruebas Psicotécnicas proceso de selección';
			mensaje = `
			<p>Vamos avanzando en el proceso de selección, por eso te confirmamos que a tu correo ha sido enviada una prueba psicotécnica, instrumento de evaluación que hace parte de los procesos del Departamento de Talento Humano de la Universidad, la cual debe aplicarse a todos los candidatos postulados a las diferentes vacantes de la institución.</p>
			<p>Si usted tiene alguna duda estamos atentos al correo: ${email}</p>
			<p>Cordialmente,</p>
			<p>Equipo de selección</p>
			<p>Universidad de la Costa.</p>`;
			enviar_correo_personalizado('th', mensaje, correo, nombre, 'Talento Humano', titulo, 'Par_TH', 1);
		});
	} else if (proceso === 'Sel_Doc') {
		sw = false;
		consulta_ajax(`${ruta}get_correo_encargado`, {}, (email) => {
			titulo = 'Solicitud de documentos de soporte HV';
			mensaje = `
			<p>Amablemente te solicitamos enviar por este medio los soportes de tu Hoja de vida en un único archivo PDF. A continuación te enviamos el listado de estos documentos , por favor envía solo los soportes que se ajusten a tu formación.</p>
			<ul style="padding-bottom:10px;">
				<li>Hoja de vida</li>
				<li>Cédula - cédula de extranjería</li>
				<li>Tarjeta profesional</li>
				<li>Diploma pregrado</li>
				<li>Acta de grado pregrado</li>
				<li>Diploma especialización</li>
				<li>Acta de grado especialización</li>
				<li>Diploma maestría</li>
				<li>Acta de grado maestría</li>
				<li>Diploma doctorado</li>
				<li>Acta de grado doctorado</li>
				<li>Certificado de apostillado</li>
				<li>Resolución de convalidación</li>
				<li>Certificación del trámite de convalidación</li>
				<li>Link CVLAC</li>
				<li>Soportes de tus publicaciones (Doi, Url)</li>
				<li>Certificaciones de tutorías de pregrado, maestría, doctorados</li>
				<li>Certificaciones de consultoría o patentes</li>
				<li>Certificación de inglés nivel B1</li>
				<li>Certificados o cartas laborales</li>
			</ul>
			<p>Te agradecemos enviarlos en el menor tiempo posible, al correo ${email}.</p>`;
			enviar_correo_personalizado('th', mensaje, correo, info_candidato.nombre, 'Talento Humano', titulo, 'Par_TH', 1);
		});
	} else if (proceso === 'Sel_Sol_VB') {
		sw = false;
		consulta_ajax(`${ruta}get_correo_jefe_inmediato`, { id: id_solicitud, candidato: info_candidato.id }, (resp) => {
			const { fullname, correo } = resp;
			const link = `<a href="${Traer_Server()}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
			titulo = 'Solicitud Visto Bueno';
			mensaje = `<p>Se le informa que el candidato <strong>${nombre}</strong> se encuentra a la espera de su visto bueno para ser contratado. Haga click aquí para gestionar el proceso: ${link}</p>`;
			enviar_correo_personalizado('th', mensaje, correo, fullname, 'Talento Humano', titulo, 'Par_TH', 1);
		});
	} else if (proceso === 'Sol_Sel_Con') {
		sw = false;
		// consulta_ajax(`${ruta}get_correo_jefe_th`, {}, (correos) => {});
		const correos = await get_usuarios_a_notificar('Hum_Sele', 'Sol_Sel_Con');
		const link = `<a href="${Traer_Server()}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
		titulo = 'Solicitud Visto Bueno TH';
		mensaje = `<p>Se le informa que el candidato <strong>${nombre}</strong> se encuentra a la espera del visto bueno del jefe de Talento Humano. Haga click aquí para gestionar el proceso: ${link}</p>`;
		enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', 'Talento Humano', titulo, 'Par_TH', 3);

	} else if (proceso === 'Sel_Seg') {
		sw = false;
		consulta_ajax(
			`${ruta}get_info_contratacion`,
			{
				id: id_solicitud,
				candidato: info_candidato.id
			},
			async (response) => {
				const link = `<a href="${Traer_Server()}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
				const { fullname, cargo, departamento, hoja_vida, informe_seleccion, avales } = response;
				titulo = 'Aval de seguridad';
				mensaje = `
				<p>Se le informa que el candidato <strong>${fullname}</strong> ya cuenta con VoBo de seguridad para el cargo <strong>${cargo}</strong>.</p>
				<p>Adjuntos hoja de vida, informe de selección y seguridad.</p>
				<p>Para mas ver y gestionar esta solicitud, hacer click aquí: ${link}</p>`;
				const files = [];
				if (hoja_vida)
					files.push([
						`../${ruta_hojas}${hoja_vida}`,
						`Hoja de vida Candidato.${hoja_vida.substr(hoja_vida.lastIndexOf('.') + 1)}`
					]);
				if (informe_seleccion)
					files.push([
						`../${ruta_hojas}${informe_seleccion}`,
						`Informe de Selección.${informe_seleccion.substr(informe_seleccion.lastIndexOf('.') + 1)}`
					]);
				if (avales.length > 0) {
					avales.forEach(({ nombre_archivo, nombre_real }) => {
						files.push([
							`../${ruta_archivos_solicitudes}${nombre_archivo}`,
							`${nombre_real}.${nombre_archivo.substr(nombre_archivo.lastIndexOf('.') + 1)}`
						]);
					});
				}
				const correos = await get_usuarios_a_notificar('Hum_Sele', 'Sel_Seg');
				enviar_correo_personalizado(
					'th',
					mensaje,
					correos,
					'',
					'Talento Humano',
					titulo,
					'Par_TH',
					3,
					files
				);
			}
		);
	} else if (proceso === 'Sel_Exa') {
		const { fecha_examenes, ayuno } = data;
		titulo = 'Citación Exámenes Médicos';
		mensaje = `
			<p>Dando continuidad al proceso de selección en el cual te encuentras participando, se solicita amablemente presentarse con su cédula el día <strong>${fecha_examenes}</strong> a partir de <strong>7:00 am a 10:00 am</strong>${ayuno
			? '<strong> EN AYUNAS</strong>'
			: ''}. En la Cra 53 # 64-28, Laboratorio Químico Clínico.</p>`;
	} else if (proceso === 'Sel_Ing') {
		sw = false;
		const { fullname, fecha_ingreso, cargo, candidatos_seleccion_id, correo } = info_candidato;
		const correos = await get_usuarios_notificar_fecha_final(candidatos_seleccion_id);
		titulo = 'Notificación Fecha Final de Ingreso';
		mensaje = `<p>Se le informa que el candidato <strong>${fullname}</strong> ya cuenta con fecha de ingreso final el día <strong>${fecha_ingreso}</strong> en el cargo <strong>${cargo}</strong>.</p>`;
		enviar_correo_personalizado('th', mensaje, correos, "Funcionario", 'Talento Humano', titulo, 'Par_TH', 3);
		mensaje = `<p>Se le informa que la fecha para su ingreso es el día <strong>${fecha_ingreso}</strong>.</p>`;
		enviar_correo_personalizado('th', mensaje, correo, fullname, 'Talento Humano', titulo, 'Par_TH', 1);
	} else if (proceso === 'Sel_Con') {
		sw = false;
		const {
			fecha_contratacion,
			tipo,
			tipo_contrato,
			duracion_contrato,
			salario,
			nombre_reemplazado,
			observaciones
		} = data;
		consulta_ajax(
			`${ruta}get_info_contratacion`,
			{
				id: id_solicitud,
				candidato: info_candidato.id
			},
			async (response) => {
				const link = `<a href="${Traer_Server()}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
				const { cargo, departamento, hoja_vida, informe_seleccion, avales } = response;
				titulo = 'Nueva Contratación';
				mensaje = `
				<p>El siguiente candidato debe ser contratado con fecha de ingreso de <strong>${fecha_contratacion}</strong> bajo el tipo de contrato <strong>${tipo}</strong> ${tipo_contrato !=
						'Cont_Ind'
						? 'con duración de ' + duracion_contrato + ' meses '
						: ''}y el cargo <strong>${cargo}</strong> con un salario de <strong>${get_valor_peso(
							salario
						)}</strong>${id_persona
							? '. Esta persona reemplaza a <strong>' +
							nombre_reemplazado +
							'</strong> en <strong>' +
							departamento +
							'</strong>'
							: ' en <strong>' + departamento}</strong>.</p>
				<p>Adjuntos hoja de vida, informe de selección, seguridad y exámenes médicos.</p>
				<p>Para mas ver y gestionar esta solicitud, hacer click aquí: ${link}</p>
				${observaciones ? '<p><strong>Observación:</strong> ' + observaciones + '</p>' : ''}`;
				const files = [];
				if (hoja_vida)
					files.push([
						`../${ruta_hojas}${hoja_vida}`,
						`Hoja de vida Candidato.${hoja_vida.substr(hoja_vida.lastIndexOf('.') + 1)}`
					]);
				if (informe_seleccion)
					files.push([
						`../${ruta_hojas}${informe_seleccion}`,
						`Informe de Selección.${informe_seleccion.substr(informe_seleccion.lastIndexOf('.') + 1)}`
					]);
				if (avales.length > 0) {
					avales.forEach(({ nombre_archivo, nombre_real }) => {
						files.push([
							`../${ruta_archivos_solicitudes}${nombre_archivo}`,
							`${nombre_real}.${nombre_archivo.substr(nombre_archivo.lastIndexOf('.') + 1)}`
						]);
					});
				}
				const correos = await get_usuarios_a_notificar('Hum_Sele', 'Sel_Ing');
				enviar_correo_personalizado(
					'th',
					mensaje,
					correos,
					nombre,
					'Talento Humano',
					titulo,
					'Par_TH',
					3,
					files
				);
			}
		);
	} else if (proceso === 'Sel_Med') {
		sw = false;
		consulta_ajax(
			`${ruta}get_info_contratacion`,
			{
				id: id_solicitud,
				candidato: info_candidato.id
			},
			async (response) => {
				const link = `<a href="${Traer_Server()}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
				const { cargo, hoja_vida, informe_seleccion, avales, fullname } = response;
				titulo = 'Aval Médico';
				mensaje = `
				<p>Se informa que el postulante <strong>${fullname}</strong> para el cargo <strong>${cargo}</strong>, cuenta con el Aval médico. Para mas información haga click aquí: ${link}</p>`;
				const files = [];
				if (hoja_vida)
				files.push([
					`../${ruta_hojas}${hoja_vida}`,
					`Hoja de vida Candidato.${hoja_vida.substr(hoja_vida.lastIndexOf('.') + 1)}`
					]);
					if (informe_seleccion)
					files.push([
						`../${ruta_hojas}${informe_seleccion}`,
						`Informe de Selección.${informe_seleccion.substr(informe_seleccion.lastIndexOf('.') + 1)}`
					]);
					if (avales.length > 0) {
						avales.forEach(({ nombre_archivo, nombre_real }) => {
						files.push([
							`../${ruta_archivos_solicitudes}${nombre_archivo}`,
							`${nombre_real}.${nombre_archivo.substr(nombre_archivo.lastIndexOf('.') + 1)}`
						]);
					});
				}
				const { candidatos_seleccion_id } = info_candidato;
				const { persona, correo } = await get_correo_responsable_th(candidatos_seleccion_id);
				enviar_correo_personalizado(
					'th',
					mensaje,
					correo,
					persona,
					'Talento Humano',
					titulo,
					'Par_TH',
					1,
					files
					);
				}
		);
	} else if (proceso === 'Sel_Rut') {
		sw = false;
		let ri = 'administrativos';
		consulta_ajax(
			`${ruta}get_full_info_candidato`,
			{
				id: id_solicitud,
				candidato: info_candidato.id
			},
			(resp) => {
				const { nombre_vacante, tipo_cargo_id } = resp;
				titulo = 'Ruta de Ingreso';
				mensaje = `<p>Felicitaciones! Le confirmamos su selección para la vacante de <strong>${nombre_vacante.toUpperCase()}</strong>.</p>
			<p>Para la Universidad de la Costa es un honor contar con un Talento como el suyo, a continuación le enviamos su ruta de ingreso:</p>
			<img src="${Traer_Server()}${ruta_ri}logo.png" alt="Ruta de Ingreso"/>`;
				if (tipo_cargo_id === 'Vac_Aca') ri = 'profesores';
				else if (tipo_cargo_id === 'Vac_Apr') ri = 'aprendices';
				enviar_correo_personalizado('th', mensaje, correo, nombre, 'Talento Humano', titulo, 'Par_TH', 1, [
					`../${ruta_ri}${ri}.jpg`,
					'Ruta de Ingreso.png'
				]);
			}
		);
	} else if (proceso === 'Sel_DocC') {
		titulo = 'Solicitud de Documentos de Contratación';
		mensaje = `
		<style>
			ul {
				counter-reset: item;
				padding-left: 10px;
			}
			li {
				list-style-type: none !important;
				display: block;
			}
			li:before {
				content: counters(item, ".") " ";
				counter-increment: item;
			}
		</style>
		<p>Le solicitamos acercarse a la oficina de talento humano en un horario de 8:00am - 11:30am y de 2:00pm a 5:30pm con los siguientes documentos para adelantar su contratación:</p>
		<h3>Documentos Obligatorios</h3>
		<ul style="padding-bottom:10px;">
			<li>Hoja de vida actualizada y firmada.</li>
			<li>Tres (3) copias legibles y ampliadas al 150% de la cedula de ciudadanía.</li>
			<li>Extranjeros: Una copia del pasaporte, Visa y de la cedula de Extranjería.</li>
			<li>Copia de tarjeta o matricula profesional o constancia de su trámite (Si aplica).</li>
			<li>Copia del diploma por cada estudio realizado (Bachiller, Titulo de pregrado y título (s) de postgrados) o constancia de estudios actuales.</li>
			<li>Certificado de E.P.S. fondo de pensiones y Cesantías, en la cual se encuentra afiliado.</li>
			<li>Copia de todos los certificados laborales, reportados en su hoja de vida.</li>
			<li>Convalidaciones de Títulos Educativos.</li>
			<li>Copia de certificado de estudios de inglés.</li>
		</ul>
		<p>Si desea afiliar a algùn beneficiario a la EPS debe traer los siguientes documentos:</p>
		<ul style="padding-bottom:10px;list-style-type: none;">
			<li style="list-style-type: none;"><h4>Conyuge:</h4></li>
			<li>
				<ol>
					<li>Una (1) copia ampliada y legible de la cédula de ciudadanía o extranjería.</li>
					<li>Un (1) registro civil de matrimonio o declaración juramentada de convivencia ante notario.</li>
				</ol>
			</li>
			<li style="list-style-type: none;"><h4>Hijos</h4></li>
			<li>
				<ol>
					<li>Un (1) registro civil de nacimiento por cada hijo.</li>
					<li>Una (1) copia ampliada de la tarjeta de identidad (para hijos mayores de 7 años) y para mayores de 18 años fotocopia de cédula de ciudadanía.</li>
					<li>Para afiliación de hijos con algún tipo de discapacidad historia clínica en original expedido por la EPS en la que se encuentre afiliado actualmente.</li>
				</ol>
			</li>
			<li><h4>Padres</h4></li>
			<li>
				<ol>
					<li>Un (1) copia ampliada y legible de la cédula de ciudadanía de cada uno de los padres.</li>
					<li>Un (1) registro civil de nacimiento del funcionario</li>
				</ol>
			</li>
		</ul>
		<p>Si desea afiliar a algún beneficiario a la caja de compensación (CAJACOPI) debe traer los siguientes documentos:</p>
		<ul style="padding-bottom:10px;">
			<li><h4>Conyuge:</h4></li>
			<li>
				<ol>
					<li>Una (1) copia ampliada y legible de la cedula de ciudadanía.</li>
					<li>Una (1) Constancia laboral original incluyendo salario.</li>
				</ol>
			</li>
			<li><h4>Hijos</h4></li>
			<li>
				<ol>
					<li>Un (1) registro civil de nacimiento por cada hijo.</li>
					<li>Para hijos mayores de 12 años Certificado escolar original.</li>
				</ol>
			</li>
			<li><h4>Padres: Mayores de 60 años</h4></li>
			<li>
				<ol>
					<li>Una (1) copia ampliada y legible de la cedula de ciudadanía de cada uno de los padres.</li>
					<li>Un (1) certificado de la EPS, donde conste el tipo de afiliación.</li>
					<li>Un (1) registro civil de nacimiento del funcionario.</li>
				</ol>
			</li>
		</ul>`;
	} else if (proceso === 'Sel_Ent') {
		sw = false;
		consulta_ajax(
			`${ruta}get_info_entrevista`,
			{
				id: info_solicitud.id,
				candidato_id: info_candidato.id
			},
			({ data, correo_responsable }) => {
				const { nombre_vacante, ubicacion_entrevista, lugar_entrevista, fecha_entrevista } = data;
				const fecha = new Date(fecha_entrevista);
				const wd = dias[fecha.getDay()];
				const d = fecha.getDate();
				const m = meses[fecha.getMonth()];
				const h = `${fecha.getHours().toString()}:${fecha.getMinutes() < 10
					? fecha.getMinutes().toString() + '0'
					: fecha.getMinutes().toString()}`;
				let titulo = 'Citación entrevista proceso de selección';
				let mensaje = `
			<p>Desde la Universidad de la Costa, te enviamos un cordial saludo e informamos que tu perfil se ha tenido en cuenta para hacer parte del proceso de selección en la búsqueda de un <strong>${nombre_vacante}</strong>.</p>
			<p>Nuestros procesos de selección difieren de un cargo a otro pero te contamos algunos de los pasos dentro del mismo:</p>
			<ul>
				<li>Entrevista psicotécnica con Talento Humano.</li>
				<li>Pruebas psicológicas y técnicas.</li>
				<li>Entrevista técnica con el jefe directo.</li>
				<li>Estudio de seguridad por el aliado SECAP LTDA.</li>
				<li>Examen médico ocupacional.</li>
			</ul>
			<br>
			<p>Este proceso consta de varios pasos, uno de estos es la realización de una entrevista de selección. Aprovechamos la oportunidad para confirmarte tu entrevista el día <strong>${wd} ${d} de ${m} a las ${h}</strong> en <strong>${lugar_entrevista} - ${ubicacion_entrevista}</strong> .</p>
			<p>Si la entrevista es remota, tenga en cuenta lo siguiente:</p>
			<ul>
				<li>A tu correo electrónico se te enviará una agenda con el link para la entrevista.</li>
				<li>Descarga la aplicación de teams en tu Smartphone o revisar los permisos web en tu computador.</li>
				<li>La plataforma funciona con cualquier correo desde la web, si deseas usar la aplicación móvil o de escritorio debes hacerlo con un correo de Hotmail/Outlook.</li> 
				<li>Para acceder a la reunión solo debes hacer clic en el link que se encuentra debajo.</li>
			</ul>
			<br>
			<p>Si la entrevista es presencial, ten en cuenta lo siguiente:</p>
			<ul>
				<li>Te sugerimos llegar 10 minutos antes de la entrevista y anunciarte en la recepción.</li>
				<li>Recuerda que en la entrevista abordaremos aspectos sobre tus experiencias y los retos a los que te has enfrentado a lo largo de tu carrera.</li>
				<li>Es importante que tus respuestas sean concisas y estén bien estructuradas.</li>
				<li>No olvides que el uso correcto del tapabocas es obligatorio, así como el distanciamiento mínimo de 2 metros durante tu proceso de entrevista.</li>
			</ul>
			<br>
			<p>Si tienes alguna duda, puedes escribirnos a este correo:  ${correo_responsable}.</p>
			<p>Te agradecemos confirmar tu asistencia.</p>`;
				enviar_correo_personalizado('th', mensaje, correo, info_candidato.nombre, 'Talento Humano', titulo, 'Par_TH', 1);
			}
		);
	} else if (proceso === 'Sel_Jef') {
		sw = false;
		consulta_ajax(
			`${ruta}get_info_entrevista_jefe`,
			{
				id: info_solicitud.id,
				candidato_id: info_candidato.id
			},
			(info) => {
				const { fecha_entrevista, txtubicacion, txtlugar } = data;
				const fecha = new Date(fecha_entrevista);
				const wd = dias[fecha.getDay()];
				const d = fecha.getDate();
				const m = meses[fecha.getMonth()];
				const h = `${fecha.getHours().toString()}:${fecha.getMinutes() < 10
					? fecha.getMinutes().toString() + '0'
					: fecha.getMinutes().toString()}`;
				titulo = 'Citación Entrevista';
				mensaje = `<p>Dando continuidad con el proceso de selección en el que usted se encuentra participando, nos permitimos citarlo a entrevista el día <strong>${wd} ${d} de ${m} a las ${h}</strong> en <strong>${txtlugar} - ${txtubicacion}</strong>.</p>
			<p>La persona encargada de la entrevista es <strong>${info.data.encargado}</strong></p>
			<p>Recomendaciones: ${info.data.observacion}</p>`;
				enviar_correo_personalizado('th', mensaje, correo, nombre, 'Talento Humano', titulo, 'Par_TH', 1);

				// notificar entrevista al jefe responsable
				const files = [];
				if (info.data.hoja_vida) files.push([`../${ruta_hojas}${info.data.hoja_vida}`,`Hoja de vida Candidato.${info.data.hoja_vida.substr(info.data.hoja_vida.lastIndexOf('.') + 1)}`]);
				mensaje = `<p>Dándole continuidad al proceso de selección de la vacante ${info.data.nombre_vacante}, le confirmamos la entrevista el día <strong>${wd} ${d} de ${m} a las ${h}</strong> en <strong>${txtlugar} - ${txtubicacion}</strong>.</p>
				<p>La persona a entrevistar es <strong>${info.data.fullname}</strong></p>`;
				enviar_correo_personalizado('th', mensaje, info.data.correo_encargado, info.data.encargado, 'Talento Humano', titulo, 'Par_TH', 1, files);
			}
		);
	} else if (proceso === 'Sel_Exo') {
		sw = false;
		consulta_ajax(
			`${ruta}get_info_contratacion`,
			{
				id: id_solicitud,
				candidato: info_candidato.id
			},
			async (response) => {
				const link = `<a href="${Traer_Server()}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
				const { cargo, departamento, fecha_ingreso, duracion_contrato, salario, tipo_contrato_id, reemplazado, nombre_reemplazado, nombre_tipo_contrato, motivo_exoneracion, hoja_vida, informe_seleccion, avales } = response;
				titulo = 'Nueva Contratación';
				mensaje = `
				<p>El siguiente candidato se envía a contratación con la siguiente información:
				${fecha_ingreso ? 'Fecha de ingreso de: <strong>' + fecha_ingreso + '</strong><br>' : ''}
				${tipo_contrato_id ? 'Tipo de contrato: <strong>' + nombre_tipo_contrato + '</strong><br>' : ''}
				${tipo_contrato_id != 'Cont_Ind' ? 'Duración de: ' + duracion_contrato + ' meses<br>' : ''}
				Cargo: <strong>${cargo}</strong><br>
				${salario ? 'Salario de: <strong>' + get_valor_peso(salario) + '</strong><br>' : ''}
				${reemplazado ? 'Esta persona reemplaza a: <strong>' + nombre_reemplazado + '</strong><br>' : ''}
				Departamento: <strong>${departamento}</strong>.</p>
				<p>Adjuntos hoja de vida, informe de selección, seguridad y exámenes médicos.</p>
				<p>Para mas ver y gestionar esta solicitud, hacer click aquí: ${link}</p>
				${motivo_exoneracion ? '<p><strong>Observación:</strong> ' + motivo_exoneracion + '</p>' : ''}`;
				const files = [];
				if (hoja_vida)
					files.push([
						`../${ruta_hojas}${hoja_vida}`,
						`Hoja de vida Candidato.${hoja_vida.substr(hoja_vida.lastIndexOf('.') + 1)}`
					]);
				if (informe_seleccion)
					files.push([
						`../${ruta_hojas}${informe_seleccion}`,
						`Informe de Selección.${informe_seleccion.substr(informe_seleccion.lastIndexOf('.') + 1)}`
					]);
				if (avales.length > 0) {
					avales.forEach(({ nombre_archivo, nombre_real }) => {
						files.push([
							`../${ruta_archivos_solicitudes}${nombre_archivo}`,
							`${nombre_real}.${nombre_archivo.substr(nombre_archivo.lastIndexOf('.') + 1)}`
						]);
					});
				}
				const correos = await get_usuarios_a_notificar('Hum_Sele', 'Sel_Exo');
				enviar_correo_personalizado(
					'th',
					mensaje,
					correos,
					'Funcionario',
					'Talento Humano',
					titulo,
					'Par_TH',
					3,
					files
				);
			}
		);
	} else if (proceso === 'Sel_Cse') {
		sw = false;
		consulta_ajax(
			`${ruta}get_full_info_candidato`,
			{
				id: id_solicitud,
				candidato: info_candidato.id
			},
			async (resp) => {
				const link = `<a href="${Traer_Server()}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
				const { fullname, proceso_actual_id } = resp;
				let tipo = 'CSEP Presencial';
				let Process = proceso_actual_id;
				if (data) tipo = 'CSEP Virtual';
				mensaje = `<p>Se le informa que el siguiente candidato <strong>${fullname}</strong> ha sido enviado a ${tipo}</p>
			<p>Para más información hacer click aquí:<br><br>${link}</p>`;
				let titulo = `Nuevo ${tipo}`;
				const correos = await get_usuarios_a_notificar('Hum_Sele', Process);
				enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', 'Talento Humano', titulo, 'Par_TH', 3);
			}
		);
	}
	if (sw) enviar_correo_personalizado('th', mensaje, correo, nombre, 'Talento Humano', titulo, 'Par_TH', 1);
};

const cargar_estudios = () => {
	$('#cbxestudios').html(`<option value="">${estudios.length} Estudios agregados</option>`);
	estudios.map(({ formacion, universidad, fecha_graduacion }, index) => {
		$('#cbxestudios').append(`<option value=${index}>${formacion} - ${universidad} - ${fecha_graduacion}</option>`);
	});
};

const generar_informe = (data) => {
	data.append('candidato', info_candidato.id);
	data.append('solicitud', id_solicitud);
	data.append('estudios', JSON.stringify(estudios));
	data.append('competencias', JSON.stringify(competencias));
	const { accion } = formDataToJson(data);
	const { tipo_seleccion } = $('#tabla_solicitudes').DataTable().row('.warning').data();
	enviar_formulario(`${ruta}generar_informe`, data, ({ mensaje, tipo, titulo }) => {
		if (tipo === 'success') {
			if (!accion) {
				const g_data = {
					candidato: info_candidato.id,
					solicitud: id_solicitud,
					nextProcess: 'Sel_Inf',
					success: 'Candidato descartado exitosamente!',
					callback: () => {
						document.getElementById('Sel_Inf').classList.add('disabled');
						listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
						cargar_procesos_disponibles(id_solicitud, info_candidato.id, tipo_seleccion);
					}
				};
				gestionar_candidato(g_data);
			}
			$('#modal_informe').modal('hide');
			$('#form_generar_informe').get(0).reset();
			let descargar = 0;
			swal(
				{
					title: 'Informe de Selección',
					text: '¿ Desea descargar el informe de selección en este momento ?',
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#D9534F',
					confirmButtonText: 'Si, Descargar!',
					cancelButtonText: 'No, Cerrar!',
					allowOutsideClick: false,
					closeOnConfirm: true,
					closeOnCancel: true
				},
				function (isConfirm) {
					if (isConfirm === true) descargar = 1;
					const route = `${Traer_Server()}index.php/talento_humano/informe/${id_solicitud}/${info_candidato.id}/${descargar}`;
					window.open(route, '_blank');
					window.focus();
				}
			);
		} else MensajeConClase(mensaje, tipo, titulo);
	});
};
const modal_informe = (candidato) => {
	$('#modal_informe').modal();
	$('.adicional_info').show();
	estudios = [];
	cargar_estudios();
	competencias = [];
	cargar_competencias();
	consulta_ajax(
		`${ruta}get_full_info_candidato`,
		{
			id: id_solicitud,
			candidato
		},
		({ departamento, cargo }) => {
			const form = '#form_generar_informe';
			$(`${form} input[name='dependencia']`).val(departamento);
			$(`${form} input[name='cargo']`).val(cargo);
		}
	);
};

const adjuntar_aval = (form, tipo_aval) => {
	const form_data = new FormData(form);
	form_data.append('solicitud', id_solicitud);
	form_data.append('candidato', info_candidato.id);
	form_data.append('tipo', tipo_aval);
	enviar_formulario(`${ruta}adjuntar_aval`, form_data, ({ mensaje, tipo, titulo }) => {
		if (tipo === 'success') {
			const { tipo_seleccion } = info_solicitud;
			$('#modal_aval_seguridad, #modal_aval_medico').modal('hide');
			$('#form_aval_seguridad, form_aval_medico').get(0).reset();
			document.getElementById(tipo_aval).classList.add('disabled');
			// cargar_procesos_disponibles(id_solicitud, info_candidato.id, tipo_seleccion);
			listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
			cargar_procesos_disponibles(0, 0, tipo_seleccion);
			$('#modal_procesos_seleccion').modal('hide');
			enviar_correo_seleccion(tipo_aval);
			swal.close();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
};

const get_info_notificacion_contratacion = (candidato, solicitud) => { };

const citar_entrevista = (candidato, form) => {
	const form_data = new FormData(form);
	form_data.append('candidato', candidato);
	form_data.append('solicitud', id_solicitud);
	enviar_formulario(`${ruta}citar_entrevista`, form_data, ({ mensaje, tipo, titulo }) => {
		if (tipo === 'success') {
			listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
			enviar_correo_seleccion('Sel_Ent');
			$('#modal_citacion').modal('hide');
			swal.close();
			form.reset();
			document.getElementById('Sel_Ent').classList.add('disabled');
		} else MensajeConClase(mensaje, tipo, titulo);
	});
};

const citar_entrevista_jefe = (form) => {
	const form_data = new FormData(form);
	form_data.append('candidato', info_candidato.id);
	form_data.append('solicitud', id_solicitud);
	form_data.append('responsable', info_solicitud.jefe_inmediato);
	form_data.append('lugar', $("#form_citacion_entrevista_jefe select[name='lugar']").text());
	form_data.append(
		'txtubicacion',
		$("#form_citacion_entrevista_jefe select[name='ubicacion']").children('option:selected').text()
	);
	form_data.append(
		'txtlugar',
		$("#form_citacion_entrevista_jefe select[name='lugar']").children('option:selected').text()
	);
	enviar_formulario(`${ruta}citar_entrevista_jefe`, form_data, ({ mensaje, tipo, titulo }) => {
		if (tipo === 'success') {
			listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
			enviar_correo_seleccion('Sel_Jef', formDataToJson(form_data));
			$('#modal_citacion_entrevista_jefe').modal('hide');
			swal.close();
			form.reset();
			document.getElementById('Sel_Jef').classList.add('disabled');
		} else MensajeConClase(mensaje, tipo, titulo);
	});
};

const citar_examenes = (candidato, form) => {
	const form_data = formDataToJson(new FormData(form));
	form_data.candidato = candidato;
	form_data.solicitud = id_solicitud;
	const { ayuno, fecha_examenes } = form_data;
	consulta_ajax(`${ruta}citar_examenes`, form_data, ({ mensaje, tipo, titulo }) => {
		if (tipo === 'success') {
			listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
			enviar_correo_seleccion('Sel_Exa', { fecha_examenes, ayuno });
			$('#modal_examen_medico').modal('hide');
			swal.close();
			form.reset();
			document.getElementById('Sel_Exa').classList.add('disabled');
		} else MensajeConClase(mensaje, tipo, titulo);
	});
};

const cargar_procesos_disponibles = (id_solicitud, id, tipo_seleccion) => {
	$('#procesos_seleccion').html('');
	$('#procesos_contratacion').html('');
	consulta_ajax(
		`${ruta}cargar_procesos_disponibles`,
		{
			solicitud: id_solicitud,
			candidato: id,
			tipo_seleccion
		},
		({ seleccion, contratacion, show_c, show_s }) => {
			// if (!show_c) {
			// 	$('#modal_procesos_seleccion #footermodal').html(`
			// 	<button type="button" id="btn_aprobar_seleccion" class="btn btn-danger active">
			// 		<span class="glyphicon glyphicon-screenshot"></span> Aprobar Selección
			// 	</button>
			// 	<button type="button" class="btn btn-default active" data-dismiss="modal">
			// 		<span class="glyphicon glyphicon-resize-small"></span> Cerrar
			// 	</button>
			// `);

			// 	$('#btn_aprobar_seleccion').click(() => {
			// 		msj_confirmacion_input(
			// 			'Atención',
			// 			'¿Digite el motivo si hay algún proceso de selección que saltar para este candidato?',
			// 			'Motivo exoneración',
			// 			(msj) => exonerar_candidato(info_candidato.candidatos_seleccion_id, msj)
			// 		);
			// 	});
			// } else {
			// 	$('#modal_procesos_seleccion #footermodal').html(
			// 		`<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`
			// 	);
			// }
			$('#modal_procesos_seleccion #footermodal').html(
				`<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`
			);
			$('#procesos_contratacion').html(contratacion);
			$('#procesos_seleccion').html(seleccion);
			!show_s ? $('#btn_contratacion').trigger('click') : $('#btn_seleccion').trigger('click');
			$('#procesos_seleccion a, #procesos_contratacion a').hover(
				function () {
					$(this).css('cursor', 'pointer');
				},
				function () {
					$(this).css('cursor', 'auto');
				}
			);
			$('#procesos_seleccion a.disabled, #procesos_contratacion a.disabled').hover(function () {
				$(this).css('cursor', 'not-allowed');
			});
			eventos_procesos();
		}
	);
};

const eventos_procesos = () => {
	const data = $('#tabla_candidatos').DataTable().row('.warning').data();

	// Asignar Entrevista
	$('#Sel_Ent').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => {
			const form = '#form_citacion';
			consulta_ajax(
				`${ruta}detalle_solicitud`,
				{
					id: id_solicitud,
					tipo: 'Hum_Sele'
				},
				({ nombre_vacante, cargo, departamento }) =>
					$(`${form} input[name='nombre_proceso']`).val(`${nombre_vacante} - (${cargo} - ${departamento})`)
			);
			$(`${form} input[name='identificacion']`).val(data.identificacion);
			$(`${form} input[name='nombre']`).val(data.fullname);
			$('#modal_citacion').modal();
		});
	});

	// Enviar Pruebas Psicotécnicas
	$('#Sel_Psi').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => {
			const { id, correo_responsable } = data;
			id_persona = id;
			info_persona.correo = correo_responsable;
			$('#modal_pruebas_psicotecnicas').modal();
			get_pruebas_asignadas();
		});
	});

	// Abrir Modal Avál Medico
	// $("#Sel_Med").click(async e => {
	// 	const proceso_ejecutado = await validar_proceso(e.target.id);
	// 	accion_proceso(proceso_ejecutado, () => $("#modal_aval_medico").modal());
	// 	$("#form_aval_medico").get(0).reset();
	// });
	$('#Sel_Med').click(() => {
		$('#modal_aval_medico').modal();
		$('#form_aval_medico').get(0).reset();
	});

	// Solicitud de documentos de contratación
	$('#Sel_DocC').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => {
			const data = {
				candidato: info_candidato.id,
				solicitud: id_solicitud,
				nextProcess: e.target.id,
				success: 'Documentos de contratación solicitados exitosamente!',
				callback: () => {
					document.getElementById(e.target.id).classList.add('disabled');
					enviar_correo_seleccion(e.target.id);
					swal.close();
				}
			};
			msj_confirmacion('¿ Solicitar Documentos de Contratación ?', '', () => gestionar_candidato(data));
		});
	});

	// Solicitud de Documento
	$('#Sel_Doc').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => {
			const candidato_data = {
				candidato: info_candidato.id,
				solicitud: id_solicitud,
				nextProcess: 'Sel_Doc',
				success: 'Documentos solicitados exitosamente!',
				callback: () => {
					document.getElementById('Sel_Doc').classList.add('disabled');
					swal.close();
				}
			};
			msj_confirmacion(
				'¿ Solicitar Documentos ?',
				'Le será enviado un correo al candidato solicitando los documentos necesarios para la selección',
				() => gestionar_candidato(candidato_data)
			);
		});
	});

	// Modal Ingreso Final
	$('#Sel_Jef').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		let { jefe_responsable } = info_solicitud;
		$("#form_citacion_entrevista_jefe #responsable_entrevista").val(jefe_responsable);
		accion_proceso(proceso_ejecutado, () => $('#modal_citacion_entrevista_jefe').modal());
	});

	// Modal Ingreso Final
	$('#Sel_Ing').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => $('#modal_fecha_ingreso').modal());
	});

	// Modal Generación de Informe de Selección
	$('#Sel_Inf').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => modal_informe(info_candidato.id));
	});

	// Modal Generación de Informe de Selección
	$('#Sel_Ind').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => $('#modal_invitacion').modal());
	});

	// Informe de Seguridad
	$('#Sel_Seg').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => $('#modal_aval_seguridad').modal());
	});

	// Solicitar Examenes Médicos
	$('#Sel_Sol_Ex').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => {
			const candidato_data = {
				candidato: info_candidato.id,
				solicitud: id_solicitud,
				nextProcess: 'Sel_Sol_Ex',
				success: 'Exámenes médicos solicitados exitosamente!',
				callback: () => {
					document.getElementById('Sel_Sol_Ex').classList.add('disabled');
					swal.close();
					enviar_notificacion_seleccion('Sel_Exa');
				}
			};
			msj_confirmacion(
				'¿ Solicitar Exámenes médicos ?',
				'Le será enviado un correo al responsable de proceso para su gestión.',
				() => gestionar_candidato(candidato_data)
			);
		});
	});

	// Solicitar visto bueno jefe
	$('#Sel_Sol_VB').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => {
			const candidato_data = {
				candidato: info_candidato.id,
				solicitud: id_solicitud,
				nextProcess: 'Sel_Sol_VB',
				success: 'Visto Bueno solicitado exitosamente!',
				callback: () => {
					document.getElementById('Sel_Sol_VB').classList.add('disabled');
					swal.close();
				}
			};
			msj_confirmacion(
				'¿ Solicitar visto bueno del Jefe Inmediato ?',
				'Le será enviado un correo al responsable para su gestión.',
				() => gestionar_candidato(candidato_data)
			);
		});
	});

	// Solicitar visto bueno jefe th
	$('#Sol_Sel_Con').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => $('#modal_add_reemplazo').modal());
		// accion_proceso(proceso_ejecutado, () => {});
		$('#form_add_reemplazo').submit((e) => {
			e.preventDefault();
			const data = formDataToJson(new FormData(e.target));
			data.candidato = info_candidato.id;
			data.reemplazado = id_persona;
			data.solicitud = id_solicitud;
			data.nextProcess = 'Sol_Sel_Con';
			data.success = 'Visto Bueno solicitado exitosamente!';
			data.callback = () => {
				$('#modal_add_reemplazo').modal('hide');
				document.getElementById('Sol_Sel_Con').classList.add('disabled');
				e.target.reset();
				swal.close();
			};
			if (!id_persona)
				MensajeConClase('Debe seleccionar el Reemplazado!.', 'info', 'Ooops!');
			else {
				msj_confirmacion('¿ Solicitar visto bueno del Jefe Talento Humano ?', 'Le será enviado un correo al responsable para su gestión.', () =>
					gestionar_candidato(data)
				);
			}
		});
	});

	// enviar a contratación
	$('#Sel_Exo').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => {
			msj_confirmacion_input(
				'Atención',
				'¿Digite el motivo si hay algún proceso de selección que saltar para este candidato?',
				'Motivo exoneración',
				(msj) => exonerar_candidato(info_candidato.candidatos_seleccion_id, msj)
			);
		});
	});

	// Citación Examenes Médicos
	$('#Sel_Exa').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => $('#modal_examen_medico').modal());
	});

	// Abrir modal de contratación
	$('#Sel_Con').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => {
			id_persona = '';
			$('#modal_contratacion').modal();
		});
	});

	// Enviar RUta de Ingreso al candidato
	$('#Sel_Rut').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => {
			const data = {
				candidato: info_candidato.id,
				solicitud: id_solicitud,
				nextProcess: e.target.id,
				success: 'Ruta de ingreso enviada exitosamente!',
				callback: () => {
					document.getElementById(e.target.id).classList.add('disabled');
					enviar_correo_seleccion(e.target.id);
					swal.close();
				}
			};
			msj_confirmacion(
				'¿ Enviar Ruta de Ingreso ?',
				'Le será enviado un correo al candidato con la ruta de ingreso.',
				() => gestionar_candidato(data)
			);
		});
	});

	$('#Sel_Cse').click(async (e) => {
		const proceso_ejecutado = await validar_proceso(e.target.id);
		accion_proceso(proceso_ejecutado, () => {
			id_persona = '';
			$('#form_tipo_csep').get(0).reset();
			$('#modal_tipo_csep').modal();
		});
	});
};

const accion_proceso = (response, callback) => {
	switch (response) {
		case 0:
			callback();
			break;
		case 1:
			MensajeConClase('Este proceso ya ha sido ejecutado', 'info', 'Ooops!');
			break;
		case 2:
			MensajeConClase(
				'Para realizar la contratación del candidato se necesita el aval de seguridad.',
				'info',
				'Ooops!'
			);
			break;
		case 3:
			MensajeConClase('No tiene permisos para realizar este proceso', 'info', 'Ooops!');
			break;
		case 4:
			MensajeConClase(
				'Para realizar la contratación del candidato se necesita la aprobación del jefe asignado y la citación a exámen médico.',
				'info',
				'Ooops!'
			);
			break;
		case 5:
			MensajeConClase(
				'Para realizar la contratación del candidato se necesita Informe de selección, aval de seguridad y citación a exámen médico.',
				'info',
				'Ooops!'
			);
			break;
		default:
			MensajeConClase('Ha ocurrido un error. Por favor comuniquese con el administrador.', 'info', 'Ooops!');
			break;
	}
};
const enviar_invitacion_induccion = () => {
	$('#modal_invitacion').modal('hide');
	MensajeConClase('Cargando por favor espere...', 'success', 'Proceso Exitoso!');
	const imageName = randomCharacter();
	let data_inv = {
		canvasId: 'imagen_induccion',
		filename: 'Invitación Inducción.png',
		path: `../archivos_adjuntos/talentohumano/detalles_solicitudes/${imageName}.png`
	};
	downloadCanvas(data_inv, (file_saved) => {
		if (file_saved) {
			const { correo, fullname, candidatos_seleccion_id } = info_candidato;
			cerrar_proceso_candidato(candidatos_seleccion_id, () => {
				const { path, filename } = data_inv;
				const titulo = 'Invitación Jornada de Inducción';
				const mensaje = `
						<p>El Departamento de Talento Humano tiene el gusto de invitarte a su jornada de inducción</p>
						<img src="${Traer_Server()}archivos_adjuntos/talentohumano/detalles_solicitudes/${imageName}.png" alt="Invitación Inducción">
					`;
				// $('#modal_invitacion').modal('hide');
				$('#Sel_Ind').addClass('disabled');
				enviar_correo_personalizado('th', mensaje, correo, fullname, 'Talento Humano', titulo, 'Par_TH', 1, [
					path,
					filename
				]);
				swal.close();
			});
		}
	});
};

const randomCharacter = () => {
	const possible = 'abcdefghijklmnñopqrstuvwxyz1234567890';
	let randomNumber = 0;
	for (let i = 0; i < 20; i++) {
		randomNumber += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	return randomNumber;
};

const cerrar_proceso_candidato = (id, callback) => {
	let candidato_seleccion_id = info_candidato.candidatos_seleccion_id;
	consulta_ajax(`${ruta}cerrar_proceso_candidato`, { id, candidato_seleccion_id }, ({ mensaje, tipo, titulo }) => {
		if (tipo === 'success') {
			const { tipo_seleccion, tipo_cargo_id } = info_solicitud;
			listar_candidatos(id_solicitud, tipo_cargo_id);
			cargar_procesos_disponibles(id_solicitud, info_candidato.id, tipo_seleccion);
			callback();
		} else MensajeConClase(mensaje, tipo, titulo);
	});
};

const get_pruebas_asignadas = () => {
	consulta_ajax(`${ruta}get_pruebas_asignadas`, {}, (pruebas) => {
		if (pruebas.length) {
			$('#pruebas_psicotecnicas').html(
				pruebas.map(({ nombre, id }) => {
					return `<div class="checkbox">
							<label><input type="checkbox" value=${id}>${nombre}</label>
						</div>`;
				})
			);
		}
	});
};

const validar_proceso = (proceso) => {
	return new Promise((resolve) => {
		consulta_ajax(
			`${ruta}validar_proceso`,
			{
				proceso,
				candidato: info_candidato.id,
				solicitud: id_solicitud
			},
			(resp) => resolve(resp)
		);
	});
};

const get_historial_candidato = () => {
	const { candidatos_seleccion_id } = $('#tabla_candidatos').DataTable().row('.warning').data();
	consulta_ajax(
		`${ruta}get_historial_candidato`,
		{
			candidato: candidatos_seleccion_id
		},
		(data) => {
			let num = 0;
			const myTable = $('#tabla_historial_candidato').DataTable({
				destroy: true,
				searching: false,
				processing: true,
				data,
				columns: [{ render: () => ++num }, { data: 'proceso' }, { data: 'fecha' }, { data: 'fullname' }],
				language: get_idioma(),
				dom: 'Bfrtip',
				buttons: []
			});
		}
	);
};

const asignar_csep = (data) => {
	const { id } = $('#tabla_candidatos').DataTable().row('.warning').data();
	const { tipo_seleccion } = $('#tabla_solicitudes').DataTable().row('.warning').data();
	data.append('candidato', id);
	data.append('solicitud', id_solicitud);
	consulta_ajax(`${ruta}asignar_csep`, formDataToJson(data), ({ titulo, mensaje, tipo, notifica, id_solicitud_Csep }) => {
		if (tipo === 'success') {
			$('#modal_candidato_csep').modal('hide');
			$('#form_candidato_csep').get(0).reset();
			document.getElementById('Sel_Cse').classList.add('disabled');
			// if(notifica) enviar_correo_seleccion('Sel_Cse',id_solicitud_Csep);
			enviar_correo_seleccion('Sel_Cse',id_solicitud_Csep);
			listar_candidatos(id_solicitud);
			$('#modal_tipo_csep').modal('hide');
			cargar_procesos_disponibles(id_solicitud, info_candidato.id, tipo_seleccion);
		} else MensajeConClase(mensaje, tipo, titulo);
	});
};

const guardar_fecha_ingreso = (form) => {
	const form_data = new FormData(form);
	form_data.append('candidato', info_candidato.candidatos_seleccion_id);
	const data = formDataToJson(form_data);
	info_candidato.fecha_ingreso = data.fecha_ingreso;
	const { tipo_seleccion } = $('#tabla_solicitudes').DataTable().row('.warning').data();
	consulta_ajax(`${ruta}guardar_fecha_ingreso`, data, ({ tipo, mensaje, titulo }) => {
		if (tipo === 'success') {
			$('#modal_fecha_ingreso').modal('hide');
			$('#form_fecha_ingreso').get(0).reset();
			enviar_correo_seleccion('Sel_Ing', info_candidato);
			document.getElementById('Sel_Ing').classList.add('disabled');
			listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
			cargar_procesos_disponibles(id_solicitud, info_candidato.id, tipo_seleccion);
		} else MensajeConClase(mensaje, tipo, titulo);
	});
};

const boton_carta_agradecimiento = () => {
	$('#boton_agradecimiento').html(
		'<span id="btn_agradecimiento" class="btn btn-default"><span class="glyphicon glyphicon-envelope red"></span> Agradecer</span>'
	);
	$('#btn_agradecimiento').click(() => {
		const data = {
			id: id_solicitud,
			nextState: 'Tal_Ter',
			success: 'Solicitud Cerrada Exitosamente!',
			type: 'Hum_Sele'
		};
		msj_confirmacion(
			'¿ Está Seguro ?',
			`La Solicitud de Selección será finalizada y los candidatos descartados serán notificados.`,
			() => {
				enviar_correo_agradecimiento(id_solicitud);
				gestionar_solicitud(data, () => {
					$('#modal_candidatos').hide();
				});
			}
		);
	});
};

const enviar_correo_agradecimiento = (solicitud) => {
	consulta_ajax(`${ruta}get_correos_participantes_descartados`, { solicitud }, (correos) => {
		const asunto = 'Agradecimiento Proceso de Selección';
		const mensaje = `
			<p>Agradecemos su participación en el proceso de selección.</p>
			<p>Conscientes de su potencial personal y profesional, se ha incluido su HV en nuestra base de datos, en espera de un nuevo proceso de selección. No obstante, en caso que no desee conservar su hoja de vida y soportes en nuestra base de datos física puede acercarse a la oficina de Talento Humano hasta la última semana del mes presente para la devolución de la misma.</p>
			<p>Agradecemos su especial interés y le deseamos muchos éxitos en su actividad profesional.</p>
			<p>Atentamente, </p>
			<p>Equipo de Talento humano - Universidad de la Costa</p>`;
		const tipo = 3;
		enviar_correo_personalizado(
			'th',
			mensaje,
			correos,
			'Aspirante en Proceso de Selección',
			'AGIL Talento Humano CUC',
			asunto,
			'Par_TH',
			tipo
		);
	});
};

const enviar_candidato_csep = (candidato, solicitud, tipo) => {
	let nextstate = 'Sel_CPre';
	let text = 'Presencial';
	if(tipo == 1){
		nextstate = 'Sel_CVir';
		text = 'Virtual';
	}
	if (tipo == 1) {
		$('#modal_candidato_csep').modal();
	} else {
		const { tipo_cargo_id } = info_solicitud;
		let tipo_seleccion = 0;
		if (tipo_cargo_id === 'Vac_Aca') tipo_seleccion = 1;
		else if (tipo_cargo_id === 'Vac_Pos') tipo_seleccion = 2;
		const g_data = {
			candidato,
			solicitud,
			nextProcess: nextstate,
			success: `Candidato Asignado a CSEP ${text}`,
			callback: () => {
				document.getElementById('Sel_Cse').classList.add('disabled');
				listar_candidatos(id_solicitud, tipo_cargo_id);
				cargar_procesos_disponibles(id_solicitud, info_candidato.id, tipo_seleccion);
				swal.close();
			}
		};
		msj_confirmacion(`¿ Enviar a CSEP ${text} ?`, '', () => gestionar_candidato(g_data));
		$('#modal_tipo_csep').modal('hide');
	}
};

const validar_permiso_invitacion = () => {
	return new Promise((resolve) =>
		consulta_ajax(`${ruta}validar_permiso_invitacion`, {}, (permiso) => resolve(permiso))
	);
};

const get_usuarios_a_notificar = (actividad, estado, motivo=null) => {
	return new Promise((resolve) => {
		consulta_ajax(
			`${ruta}get_usuarios_a_notificar`,
			{
				actividad,
				estado,
				motivo
			},
			(data) => resolve(data)
		);
	});
};

const get_usuarios_notificar_fecha_final = (id) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}get_usuarios_notificar_fecha_final`, { id }, (data) => resolve(data));
	});
};

const cargar_requisiciones = () => {
	consulta_ajax(`${ruta}cargar_requisiciones`, {}, (data) => {
		$('#tabla_requisiciones tbody').off('click', 'tr td span.requisicion');
		let num = 0;
		const myTable = $('#tabla_requisiciones').DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data,
			columns: [
				{ render: () => ++num },
				{ data: 'fullname' },
				{ data: 'cargo' },
				{ data: 'departamento' },
				{ data: 'tipo_solicitud' },
				{
					defaultContent:
						'<span style="color: #5cb85c" title="Seleccionar Solicitud" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default requisicion" ></span>'
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_requisiciones tbody').on('click', 'tr td span.requisicion', async function () {
			const {
				id,
				tipo_cargo,
				departamento,
				departamento_id,
				cargo_id,
				fullname,
				tipo_solicitud,
				responsable_id
			} = myTable.row($(this).parent()).data();
			id_persona = responsable_id;
			id_solicitud = id;
			$('#form_seleccion select[name=tipo_cargo]').val(tipo_cargo);
			$('#form_seleccion input[name=requisicion]').val(tipo_solicitud);
			$('#form_seleccion input[name=dependencia]').val(departamento);
			$('#form_seleccion input[name=nombre_responsable]').val(fullname);
			const cargos = await listar_cargos();
			pintar_datos_combo(cargos, '#form_seleccion select[name=cargo]', 'Seleccione Cargo', cargo_id);
			$('#modal_requisicion').modal('hide');
		});
	});
};

const enviar_notificacion_seleccion = async (proceso) => {
	let mensaje = '';
	let titulo = '';
	let link = '';
	const files = [];
	const { fullname, candidatos_seleccion_id } = info_candidato;
	switch (proceso) {
		case 'Sel_Exa':
			link = `<a href="${Traer_Server()}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
			titulo = 'Nueva Citación Exámenes Médicos';
			mensaje = `<p>Se le informa que el candidato <strong>${fullname}</strong> se encuentra listo para realizar la citación a exámenes médicos. Haga click aquí para gestionar la citación y resultados médicos: ${link}</p>`;
			let correos = await get_usuarios_a_notificar('Hum_Sele', proceso);
			enviar_correo_personalizado('th', mensaje, correos, '', 'Talento Humano', titulo, 'Par_TH', 3, files);
			break;
		case 'Sel_VB_Jef':
			link = `<a href="${Traer_Server()}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
			titulo = 'Visto Bueno Jefe Inmediato';
			mensaje = `<p>Se le informa que el candidato <strong>${fullname}</strong> ya cuenta con Visto Bueno del Jefe Inmediato. Para más información haga click aquí: ${link}</p>`;
			const { persona, correo } = await get_correo_responsable_th(candidatos_seleccion_id);
			enviar_correo_personalizado('th',mensaje,correo,persona,'Talento Humano',titulo,'Par_TH',1,files);
			break;
	}
};

const modificar_postulante_cmt = (data) => {
	enviar_formulario(`${ruta}modificar_postulante_cmt`, data, (resp) => {
		let { tipo, mensaje, titulo, sw } = resp;
		if (tipo == 'success' || sw) {
			listar_postulantes_csep(id_comite, 'comite');
			$('#modal_modificar_postulante_cmt').modal('hide');
			$('#form_modificar_postulante_cmt').get(0).reset();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
};

const mostrar_detalle_persona_incompleto = (data) => {
	const select = (name) => `select[name='${name}']`;
	const input = (name) => `input[name='${name}']`;
	const form = '#form_modificar_postulante';
	const {
		id,
		nombre_completo,
		fecha_nacimiento,
		fecha_expedicion,
		lugar_expedicion,
		nombre,
		apellido,
		segundo_nombre,
		segundo_apellido,
		identificacion,
		id_tipo_identificacion,
		correo,
		telefono,
		genero
	} = data;
	datos_postulante = { id, nombre_completo };
	if (!lugar_expedicion || !fecha_nacimiento || !genero || !telefono || !correo) {
		$('#modal_modificar_postulante').modal();
		$(`${form} ${select('id_tipo_identificacion')}`).val(id_tipo_identificacion);
		$(`${form} ${select('genero')}`).val(genero);
		$(`${form} ${input('fecha_nacimiento')}`).val(fecha_nacimiento);
		$(`${form} ${input('fecha_expedicion')}`).val(fecha_expedicion);
		$(`${form} ${input('lugar_expedicion')}`).val(lugar_expedicion);
		$(`${form} ${input('nombre')}`).val(nombre);
		$(`${form} ${input('apellido')}`).val(apellido);
		$(`${form} ${input('segundo_nombre')}`).val(segundo_nombre);
		$(`${form} ${input('segundo_apellido')}`).val(segundo_apellido);
		$(`${form} ${input('identificacion')}`).val(identificacion);
		$(`${form} ${input('correo')}`).val(correo);
		$(`${form} ${input('telefono')}`).val(telefono);
		return false;
	}
	return true;
};

const crear_nueva_opcion = (form) => {
	const formData = new FormData(form);
	enviar_formulario(`${ruta}crear_nueva_opcion`, formData, ({ mensaje, tipo, titulo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			listar_opciones_certificados();
			$('#modal_opciones_certificados').modal('hide');
			$('#form_crear_opcion_certificado').get(0).reset();
		}
	});
};

const get_opciones_disponibles = () => {
	$('#div_opciones_disponibles').html('');
	$('#div_opciones_disponibles > label.pointer').off('click');
	consulta_ajax(`${ruta}listar_opciones_certificados_activos`, {}, (data) => {
		data.map(({ asignado, opcion, aux }) => {
			if (asignado) {
				$('#div_opciones_disponibles').append(
					`<label data-name="${aux}" class="pointer"><span class="fa fa-toggle-off color_success"></span> ${opcion}</label>`
				);
			}
		});
		$('#div_opciones_disponibles > label.pointer').on('click', function () {
			$(this).children().toggleClass('fa-toggle-on fa-toggle-off');
		});
	});
};

const guardar_requisicion_posgrado = (formData) => {
	$(`#form_req_posgrado button[type='submit']`).prop("disabled", true);
	enviar_formulario(
		`${ruta}guardar_requisicion_posgrado`,
		formData,
		({ mensaje, tipo, titulo, personas_notificar, estado, id, files }) => {
			MensajeConClase(mensaje, tipo, titulo);
			$('#modal_requisicion_posgrado').modal('hide');
			if (tipo === 'success') {
				const ext = files.split('.')[1];
				const path = `../${ruta_archivos_requisicion}${files}`;
				const filename = `Documento Adjunto.${ext}`;
				let titulo = '';
				let msj = '';
				let nombre = '';
				const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
				switch (estado) {
					case 'Tal_Env':
						titulo = 'Nueva Requisición';
						msj = `
							<p>Se ha creado una nueva solicitud de requisición.</p>
							<p>Para mas información ingrese a: ${ser}</p>
						`;
						nombre = 'Decano';
						break;
					case 'Tal_Pro':
						titulo = 'Nueva Requisición';
						msj = `
							<p>Posgrado una requisición de vinculación posgrado ha sido avalada por un Decano y queda pendiente por su aprobación.</p>
							<p>para confirmar su aprobación haga click aquí: ${ser}</p>
						`;
						nombre = 'Director de Posgrado ';
						break;
					case 'Tal_Ter':
						titulo = 'Nueva Requisición Aprobada';
						msj = `
							<p>Se ha creado una nueva solicitud de requisición.</p>
							<p>Para mas información ingrese a: ${ser}</p>
						`;
						nombre = 'Funcionario Talento Humano';
						break;
				}
				if (personas_notificar)
					enviar_correo_personalizado(
						'th',
						msj,
						personas_notificar,
						nombre,
						'Talento Humano',
						titulo,
						'Par_TH',
						3,
						[path, filename]
				);
			}
			$(`#form_req_posgrado button[type='submit']`).prop("disabled", false);
		}
	);
};

const avalar_perfil = (id, departamento) => {
	consulta_ajax(
		`${ruta}avalar_perfil`,
		{ id, departamento },
		({ mensaje, tipo, titulo, personas_notificar, file }) => {
			MensajeConClase(mensaje, tipo, titulo);
			if (tipo === 'success') {
				const ext = file.split('.')[1];
				const path = `../${ruta_archivos_requisicion}${file}`;
				const filename = `Documentos adjuntos.${ext}`;
				listar_solicitudes();
				const titulo = 'Requisición Avalada';
				const nombre = 'Director de Posgrado';
				const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
				const msj = `
				<p>Una requisición de vinculación posgrado ha sido avalada por un Decano y queda pendiente por su aprobación.</p>
				<p>Para confirmar su aprobación haga clic aquí: ${ser}</p>
			`;
				if (personas_notificar)
					enviar_correo_personalizado(
						'th',
						msj,
						personas_notificar,
						nombre,
						'Talento Humano',
						titulo,
						'Par_TH',
						3,
						[path, filename]
					);
			}
		}
	);
};

const generar_pdf = id => {
	console.log("generando");
	const route = `${Traer_Server()}index.php/talento_humano/detalle_req_posgrado/${id}`;
	window.open(route, '_blank');
	window.focus()
	console.log("generado");
	return true;
}

const terminar_requisicion = (tipo_orden) => {
	const { id, tipo_solicitud_id, id_departamento } = $('#tabla_solicitudes').DataTable().row('.warning').data();
	// const data = {
	// 	canvasId: 'modal_detalle_vacante_posgrado',
	// 	filename: 'Detalle Solicitud.png',
	// 	path: '../archivos_adjuntos/talentohumano/detalles_solicitudes/detalle_req_posgrado.png'
	// };
	// downloadCanvas(data, async (file_saved) => {
	// 	if (file_saved) {
	// const files = [ [ data.path, data.filename ] ];
	const files = [];
	consulta_ajax(
		`${ruta}terminar_requisicion`,
		{ id, tipo_orden, tipo_solicitud_id, codigo_sap, id_departamento },
		({ mensaje, tipo, titulo, personas_notificar, documentos }) => {
			MensajeConClase(mensaje, tipo, titulo);
			const ext = documentos.split('.')[1];
			const path = `../${ruta_archivos_requisicion}${documentos}`;
			const filename = `Hoja de vida.${ext}`;
			files.push([path, filename]);

			if (tipo === 'success') {
				generar_pdf(id);
				$('#modal_terminar_requisicion_posgrado').modal('hide');
				$('#modal_detalle_vacante_posgrado').modal('hide');
				listar_solicitudes();
				const titulo = 'Requisición Aprobada';
				const nombre = 'Funcionario Talento Humano';
				const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
				const msj = `
							<p>La requisición posgrado ha sido aprobada por el director del departamento de Posgrado.</p>
							<p>Más información en: ${ser}</p>
							<p><a href="${server}archivos_adjuntos/talentohumano/detalles_solicitudes/detalle_req_posgrado_${id}.pdf"><b>Detalle Requisición</b></a></p>`;
				if (personas_notificar)
					enviar_correo_personalizado(
						'th',
						msj,
						personas_notificar,
						nombre,
						'Talento Humano',
						titulo,
						'Par_TH',
						3,
						files
					);
			}
		}
	);
	// 	}
	// });
};

const modal_modificar_requisicion_posgrado = ({ id }) => {
	const form = '#form_req_posgrado';
	$('.icono_requisicion').removeClass('fa-plus').addClass('fa-wrench');
	$('.accion_requisicion').html('Modificar');
	$('#modal_requisicion_posgrado').modal();
	$('#campo_hoja_vida').css('display', 'none');
	consulta_ajax(`${ruta}detalle_requisicion_posgrado`, { id }, async (data) => {
		info_candidato = {
			id: data.id_candidato,
			nombre_completo: data.candidato
		};
		if (data.reemplazado_id) {
			reemplazado_id = data.reemplazado_id;
			$("#div_reemplazado_req_pos").show('fast');
			$(`${form} input[name="reemplazado"]`).val(data.reemplazado);
		} else {
			reemplazado_id = null;
			$("#div_reemplazado_req_pos").css('display', 'none');
			$(`${form} input[name="reemplazado"]`).val('');
		}
		const cargos = await listar_cargos();
		pintar_datos_combo(cargos, `${form} select[name="cargo"]`, 'Seleccione Cargo', data.cargo_id);
		$(`${form} select[name="tipo_vacante"]`).val(data.id_tipo_vacante);
		$(`${form} select[name="tipo_programa"]`).val(data.tipo_programa_id);
		$(`${form} select[name="req_programa"]`).val(data.id_programa);
		$(`${form} select[name="id_departamento"]`).val(data.id_departamento);
		$(`${form} input[name="candidato"]`).val(data.candidato);
		$(`${form} input[name="nombre_modulo"]`).val(data.nombre_modulo);
		$(`${form} input[name="horas_modulo"]`).val(data.horas_modulo);
		$(`${form} input[name="numero_promocion"]`).val(data.numero_promocion);
		$(`${form} input[name="valor_hora"]`).val(data.valor_hora);
		$(`${form} input[name="ciudad_origen"]`).val(data.ciudad_origen);
		$(`${form} input[name="fecha_inicio"]`).val(data.fecha_inicio);
		$(`${form} input[name="fecha_terminacion"]`).val(data.fecha_terminacion);
		$(`${form} textarea[name="observaciones"]`).val(data.observacion);
	});
};

const modificar_requisicion_posgrado = (formData) => {
	const data = $('#tabla_solicitudes').DataTable().row('.warning').data();
	formData.append('candidato', info_candidato.id);
	formData.append('solicitud', data.id);
	enviar_formulario(`${ruta}modificar_requisicion_posgrado`, formData, ({ mensaje, tipo, titulo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') $('#modal_requisicion_posgrado').modal('hide');
		$("#form_req_posgrado button[type='submit']").prop("disabled", false);
	});
};

const exonerar_candidato = (candidato, motivo) => {
	const { tipo_seleccion } = $('#tabla_solicitudes').DataTable().row('.warning').data();
	consulta_ajax(`${ruta}exonerar_candidato`, { candidato, motivo }, ({ mensaje, tipo, titulo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			enviar_correo_seleccion('Sel_Exo');
			listar_candidatos(id_solicitud, info_solicitud.tipo_cargo_id);
			cargar_procesos_disponibles(id_solicitud, info_candidato.id, tipo_seleccion);
		}
	});
};

const cambiar_director_posgrado = ({ id, nombre_completo, correo }) => {
	consulta_ajax(`${ruta}cambiar_director_posgrado`, { id, nombre_completo, correo }, ({ mensaje, tipo, titulo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			$('#modal_administrar .nombre_director').html(nombre_completo);
			$('#modal_buscar_postulante').modal('hide');
			$('#director_correo').val(correo);
			$('#director_id').val(id);
		}
	});
};

const vb_pedagogico = (form) => {
	let { vacante_id } = info_solicitud;
	const formData = new FormData(form);
	formData.append('id', id_solicitud);
	formData.append('vacante_id', vacante_id);
	enviar_formulario(`${ruta}vb_pedagogico`, formData, async ({ mensaje, tipo, titulo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			const data = $('#tabla_solicitudes').DataTable().row('.warning').data();
			const { id_tipo_solicitud, departamento_id } = data;
			listar_solicitudes();
			const link = `<a href="${Traer_Server()}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
			titulo = 'Visto bueno pedagógico - Pregrado Requisición';
			mensaje = `
				<p>La solicitud de requisición fue procesada por el coordinador pedagógico y ya puede ser gestionada por usted.</p>
				<p>Para mas información por favor ingrese a ${link}</p>
			`;
			const correos = await get_personas_notificar(id_tipo_solicitud, 'Tal_Env', departamento_id);
			$('#modal_vb_pedagogico').modal('hide');
			enviar_correo_personalizado('th', mensaje, correos, '', 'Talento Humano', titulo, 'Par_TH', 3);
		}
	});
};

const config_modal_vacantes = (tipo) => {
	$('#form_vacante').get(0).reset();
	$('#modal_solicitud_vacante').modal();
	$('.div_evaluacion, #div_apertura, #div_persona, #info_investigacion').html('');
	$('.texto_accion').html(mod_vacante ? 'Modificar' : 'Agregar');
	$('.adicional_info, .no-revisar').show('fast');
	materias = [];
	programas = [];
	listar_materias();
	cargar_programas();
	id_persona = null;
	if (tipo === 'Vac_Adm') {
		$('#botones_modificar, .req_aca, .req_adm').css('display', 'none');
		$('.req_adm').fadeIn('slow');
		$('.req_aca').fadeOut('fast');
	} else if (tipo === 'Vac_Apr') {
		$('#botones_modificar, .req_aca, .req_adm').css('display', 'none');
		$('.req_obs').fadeIn('slow');
	} else {
		$('#botones_modificar, .req_aca, .req_adm').hide();
		$('.req_aca').fadeIn('slow');
		$('.req_adm').fadeOut('fast');
	}
};

const crear_solicitud_arl = (control, form) => {
	let data = new FormData(document.getElementById(form));
	data.append('id_persona', id_persona);
	enviar_formulario(`${ruta}${control}`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$(`#${form}`).get(0).reset();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
};

const modificar_solicitud_arl = (control, form, modal, id, id_estado_solicitud) => {
	let data = new FormData(document.getElementById(form));
	data.append('id_solicitud', id);
	data.append('id_estado', id_estado_solicitud);
	data.append('id_persona', id_persona);
	enviar_formulario(`${ruta}${control}`, data, (resp) => {
		let { tipo, mensaje, titulo } = resp;
		if (tipo == 'success') {
			$(`#${form}`).get(0).reset();
			$(`#${modal}`).modal('hide');
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
};

const get_detalle_solicitud_arl = (id_solicitud, id_tipo_solicitud) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}get_detalle_solicitud_arl`, { id_solicitud, id_tipo_solicitud }, (data) => resolve(data));
	});
};

const detalle_solicitud_arl = async ({
	id,
	solicitante,
	fecha_registro,
	state,
	tipo_solicitud,
	id_tipo_solicitud,
	observacion,
	id_estado_solicitud
}) => {
	$('#tabla_detalle_arl .info_solicitante').html(solicitante);
	$('#tabla_detalle_arl .info_fecha').html(fecha_registro);
	$('#tabla_detalle_arl .info_estado').html(state);
	$('#tabla_detalle_arl .info_tipo').html(tipo_solicitud);
	if (id_estado_solicitud === 'Tal_Neg') {
		$('#observaciones_arl').fadeIn('fast');
		$('.info_observaciones').html(`<strong class="color_danger">${observacion}</strong>`);
	} else {
		$('#observaciones_arl').fadeOut('fast');
		$('.info_observaciones').html('');
	}
	const dato = await get_detalle_solicitud_arl(id, id_tipo_solicitud);
	$('#tabla_detalle_arl .info_beneficiario').html(dato.beneficiario);
	$('#tabla_detalle_arl .info_cc_beneficiario').html(dato.identificacion);
	$('#tabla_detalle_arl .info_fecha_nac_beneficiario').html(dato.fecha_nacimiento);
	$('#tabla_detalle_arl .info_empresa').html(dato.empresa);
	let cobertura = '';
	if (dato.tipo_cobertura == 1) cobertura = 'Nacional';
	else cobertura = 'Internacional';

	if (id_tipo_solicitud === 'Hum_Cob_Arl') {
		$('#tabla_detalle_arl .info_ciudad').html(dato.ciudad);
		$('#tabla_detalle_arl .info_idioma').html(dato.idioma);
		$('#tabla_detalle_arl .info_cobertura').html(cobertura);
		$('#tabla_detalle_arl .info_fviaje').html(dato.fecha_viaje);
		$('#tabla_detalle_arl .info_fregreso').html(dato.fecha_regreso);
		$('#tabla_detalle_arl .info_actividad').html(dato.actividad);
		$('#tabla_detalle_arl .detalle_afiliacion').fadeOut('fast');
		$('#tabla_detalle_arl .detalle_cobertura').fadeIn('fast');
	} else {
		$('#tabla_detalle_arl .info_ciudad').html(dato.ciudad);
		$('#tabla_detalle_arl .info_nriesgo').html(dato.riesgo);
		$('#tabla_detalle_arl .info_fechaini').html(dato.fecha_inicio_labor);
		$('#tabla_detalle_arl .info_fechafin').html(dato.fecha_fin_labor);
		$('#tabla_detalle_arl .detalle_cobertura').fadeOut('fast');
		$('#tabla_detalle_arl .detalle_afiliacion').fadeIn('fast');
	}
	$('#tabla_detalle_arl .info_motivo').html(dato.motivo);
};

const detallle_ausentismo = async (solicitud, id_tipo_solicitud) => {
	return new Promise(resolve => {
		let url = `${ruta}detalles_ausentismo`;
		consulta_ajax(url, { solicitud, id_tipo_solicitud }, resp => {
			resolve(resp);
		});
	});
}

const detalle_solicitud_ausentismo_vacaciones = async ({
	solicitante,
	fecha_registro,
	state,
	tipo_solicitud,
	jefe_responsable,
	id_tipo_solicitud,

}) => {
	let inf = await detallle_ausentismo(id_solicitud, id_tipo_solicitud);
	let comentario = inf.comentario != null ? inf.comentario : inf.msj_negado;
	console.log(comentario);
	$('#tabla_detalle_talento_humano_ausentismo .info_solicitante').html(solicitante);
	$('#tabla_detalle_talento_humano_ausentismo .fecha_inicio').html(inf.fecha_inicio);
	$('#tabla_detalle_talento_humano_ausentismo .dias_solicitados').html(inf.dias_solicitados);
	$('#tabla_detalle_talento_humano_ausentismo .jefe_inmediato').html(jefe_responsable);
	$('#tabla_detalle_talento_humano_ausentismo .observaciones').html(inf.observaciones);
	$('#tabla_detalle_talento_humano_ausentismo .info_fecha').html(fecha_registro);
	$('#tabla_detalle_talento_humano_ausentismo .info_estado').html(state);
	$('#tabla_detalle_talento_humano_ausentismo .info_tipo').html(tipo_solicitud);
	$('#tabla_detalle_talento_humano_ausentismo .info_motivo').html(comentario);
	$('#modal_detalle_solicitud_talento_humano_ausentismo').modal();
};

const detalle_solicitud_ausentismo_licencia = async ({
	solicitante,
	fecha_registro,
	state,
	tipo_solicitud,
	id_tipo_solicitud,
	jefe_responsable,
}) => {
	let info = await detallle_ausentismo(id_solicitud, id_tipo_solicitud);
	let comentario = info.comentario != null ? info.comentario : info.msj_negado;
	console.log(comentario);
	$('#tabla_detalle_talento_humano_licencia .info_solicitante').html(solicitante);
	$('#tabla_detalle_talento_humano_licencia .fecha_inicio').html(info.fecha_inicio);
	$('#tabla_detalle_talento_humano_licencia .dias_solicitados').html(info.dias_solicitados);
	$('#tabla_detalle_talento_humano_licencia .jefe_inmediato').html(jefe_responsable);
	$('#tabla_detalle_talento_humano_licencia .observaciones').html(info.observaciones);
	$('#tabla_detalle_talento_humano_licencia .info_fecha').html(fecha_registro);
	$('#tabla_detalle_talento_humano_licencia .info_estado').html(state);
	$('#tabla_detalle_talento_humano_licencia .info_tipo').html(tipo_solicitud);
	$('#tabla_detalle_talento_humano_licencia .info_motivo').html(comentario);
	$('#tabla_detalle_talento_humano_licencia .info_tipo_licencia').html(info.tipo_lic);
	$('#tabla_detalle_talento_humano_licencia .motivo_licencia').html(info.motivo_licencia);
	$('#tabla_detalle_talento_humano_licencia .archivo_adjunto').html(`<a href='${Traer_Server()}/archivos_adjuntos/talentohumano/soportes_licencia/${info.ruta_adjunto}' target = 'blank_'>${info.nombre_adjunto}</a>`);
	$('#modal_detalle_solicitud_talento_humano_licencia').modal();
};


const mostrar_solicitud_arl = async ({ id, id_tipo_solicitud, id_estado_solicitud }) => {
	let form = '';
	let modal = '';
	const data = await get_detalle_solicitud_arl(id, id_tipo_solicitud);
	id_persona = data.id_persona;
	if (id_tipo_solicitud === 'Hum_Afi_Arl') {
		tipo_persona = 1;
		form = 'form_nuevo_arl';
		modal = 'modal_nuevo_arl';
		$('.titulo_modal_afil_arl').html('Modificar Afiliación ARL');
		$(`#${form} input[name="empresa"]`).val(data.empresa);
		$(`#${form} input[name="ciudad"]`).val(data.ciudad);
		$(`#${form} select[name="id_nriesgo"]`).val(data.id_nivel_riesgo);
		$(`#${form} input[name="fecha_inicio_lab"]`).val(data.fecha_inicio_labor);
		$(`#${form} input[name="fecha_fin_lab"]`).val(data.fecha_fin_labor);
		callbak_activo_aux = (resp) =>
			modificar_solicitud_arl('modificar_afiliacion_arl', form, modal, id, id_estado_solicitud);
	} else {
		tipo_persona = '';
		form = 'form_cobertura_arl';
		modal = 'modal_cobertura_arl';
		$('.titulo_modal_cob_arl').html('Modificar Cobertura ARL');
		$(`#${form} input[name="fecha_viaje"]`).val(data.fecha_viaje);
		$(`#${form} input[name="fecha_regreso"]`).val(data.fecha_regreso);
		$(`#${form} textarea[name="actividad"]`).val(data.actividad);
		$(`#${form} select[name="id_cobertura"]`).val(data.tipo_cobertura);
		$(`#${form} input[name="empresa"]`).val(data.empresa);
		$(`#${form} input[name="destino"]`).val(data.ciudad);
		$(`#${form} input[name="idioma"]`).val(data.idioma);
		callbak_activo_aux = (resp) =>
			modificar_solicitud_arl('modificar_cobertura_arl', form, modal, id, id_estado_solicitud);
	}
	$(`#${form} input[name="candidato"]`).val(data.beneficiario);
	$(`#${form} input[name="fecha_nacimiento"]`).val(data.fecha_nacimiento);
	$(`#${form} select[name="id_genero"]`).val(data.genero);
	$(`#${form} input[name="eps"]`).val(data.eps);

	if (id_estado_solicitud === 'Tal_Pro') {
		$(`#${form} textarea[name="motivo"]`).removeClass('oculto');
		$(`#${form} textarea[name="motivo"]`).val(data.motivo);
	} else $(`#${form} textarea[name="motivo"]`).addClass('oculto');

	callbak_activo = (data) => {
		let { id, nombre_completo, fecha_nacimiento, genero, eps } = data;
		id_persona = id;
		if (genero == 0) genero = '';
		$("#form_nuevo_arl input[name='candidato']").val(nombre_completo);
		$("#form_nuevo_arl input[name='fecha_nacimiento']").val(fecha_nacimiento);
		$("#form_nuevo_arl select[name='id_genero']").val(genero);
		$("#form_nuevo_arl input[name='eps']").val(eps);
		$('#modal_buscar_persona_arl').modal('hide');
	};
	$(`#${modal}`).modal();
};

const aprobar_solicitud_arl = (form, data, mensaje_correo, asunto) => {
	const formData = new FormData(form);
	formData.append('id', id_solicitud);
	formData.append('id_tipo_solicitud', data.id_tipo_solicitud);
	enviar_formulario(`${ruta}aprobar_solicitud_arl`, formData, ({ mensaje, tipo, titulo, certificado }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			listar_solicitudes();
			if (certificado) {
				const ext = certificado.split('.')[1];
				const path = `../${ruta_archivos_arl}${certificado}`;
				const filename = `Certificado Arl.${ext}`;
				enviar_correo_personalizado('th', mensaje_correo, data.correo, data.solicitante, 'AGIL Talento Humano CUC', asunto, 'Par_TH', 1, [path, filename]);
			} else enviar_correo_personalizado('th', mensaje_correo, data.correo, data.solicitante, 'AGIL Talento Humano CUC', asunto, 'Par_TH', 2);

			if (data.notificar) {
				enviar_notificacion_arl(id_solicitud);
			}
			$('#modal_vb_arl').modal('hide');
		}
	});
};
const aprobar_solicitud_entidades = (form, data, mensaje_correo, asunto) => {
	const formData = new FormData(form);
	formData.append('id', id_solicitud);
	formData.append('id_tipo_solicitud', data.id_tipo_solicitud);
	enviar_formulario(`${ruta}aprobar_solicitud_entidades`, formData, ({ mensaje, tipo, titulo, certificado }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			listar_solicitudes();
			if (certificado) {
				const ext = certificado.split('.')[1];
				const path = `../${ruta_documentos_gestion}${certificado}`;
				const filename = `Certificado Entidades.${ext}`;
				enviar_correo_personalizado('th', mensaje_correo, data.correo, data.solicitante, 'AGIL Talento Humano CUC', asunto, 'Par_TH', 1, [path, filename]);
			} else enviar_correo_personalizado('th', mensaje_correo, data.correo, data.solicitante, 'AGIL Talento Humano CUC', asunto, 'Par_TH', 2);

			if (data.notificar) {
				enviar_notificacion_eps(id_solicitud);
			}
			$('#modal_entidades').modal('hide');
		}
	});
};
const guadar_estado_ecargo = (id_solicitud,comentario,visto_bueno)=> {
	consulta_ajax(`${ruta}guadar_estado_ecargo`, {id_solicitud,comentario,visto_bueno}, ({ mensaje, tipo, titulo, estado }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			listar_solicitudes();
			$('#modal_vb_ecargo').modal('hide');
			if(estado === "Tal_Vb_Ter") enviar_notificacion_TH('Hum_Entr_Cargo', "Tal_Vb_Ter", id_solicitud);
		}
	});
};

const enviar_notificacion_arl = async (id_solicitud) => {
	const titulo = 'Solicitud de Cobertura ARL INTERNACIONAL';
	const nombre = 'Funcionario Talento Humano';
	const ser = `<a href="${server}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
	const mensaje = `
		<p>Se le informa que una solicitud de cobertura ARL internacional ha sido <strong>APROBADA</strong> y est&aacute lista para ser gestionada por usted.</p>
		<p>Para mas información ingrese a: ${ser}</p>
	`;
	const correos = await get_usuarios_a_notificar('Hum_Cob_Arl', 'Tal_Ter');
	enviar_correo_personalizado('th', mensaje, correos, nombre, 'AGIL Talento Humano CUC', titulo, 'Par_TH', 3);
};

const enviar_notificacion_eps = async (id_solicitud) => {
	const titulo = 'Solicitud Cambio EPS';
	const nombre = 'Funcionario Talento Humano';
	const ser = `<a href="${server}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
	const mensaje = `
		<p>Se le informa que una solicitud de cambio de EPS ha sido <strong>APROBADA</strong>.</p>
		<p>Para mas información ingrese a: ${ser}</p>
	`;
	const correos = await get_usuarios_a_notificar('Hum_Cam_Eps', 'Tal_Apr');
	enviar_correo_personalizado('th', mensaje, correos, nombre, 'AGIL Talento Humano CUC', titulo, 'Par_TH', 3);
};

const enviar_notificacion_ecargo = async  (id_tipo_solicitud, estado, id, motivo) => {
	let tipo_solicitud = "";
	if (id_tipo_solicitud === 'Hum_Entr_Cargo') {
		tipo_solicitud = 'Entrega de Cargo';
	} 

	const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
	let asunto = `Solicitud de ${tipo_solicitud}`;
	const mensaje = `
		<p>Se le informa que una solicitud de ${tipo_solicitud} necesita su <strong>Visto Bueno</strong>.</p>
		<p>Para mas información ingrese a: ${ser}</p>
	`;
	const correos = await get_usuarios_a_notificar(id_tipo_solicitud, estado, motivo);
	enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', 'AGIL Talento Humano CUC', asunto, 'Par_TH', 3);;

};
const enviar_notificacion_th = async  (id_tipo_solicitud, estado,id,motivo) => {
	let tipo_solicitud = "";
	if (id_tipo_solicitud === 'Hum_Entr_Cargo') {
		tipo_solicitud = 'Entrega de Cargo';
	} 

	const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
	let asunto = `Solicitud de ${tipo_solicitud}`;
	const mensaje = `
		<p>Se le informa que una solicitud de ${tipo_solicitud} ha sido <strong>creada por un colaborador</strong>.</p>
		<p>Para mas información ingrese a: ${ser}</p>
	`;
	const correos = await get_usuarios_a_notificar(id_tipo_solicitud, estado,motivo);
	enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', 'AGIL Talento Humano CUC', asunto, 'Par_TH', 3);;

};
const enviar_notificacion_TH = async  (id_tipo_solicitud, estado,id,motivo) => {
	let tipo_solicitud = "";
	if (id_tipo_solicitud === 'Hum_Entr_Cargo') {
		tipo_solicitud = 'Entrega de Cargo';
	} 

	const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
	let asunto = `Solicitud de ${tipo_solicitud}`;
	const mensaje = `
		<p>Se le informa que una solicitud de ${tipo_solicitud} ya tiene los vistos buenos correspondiete. Esta esperando para ser <strong>tramitada</strong> por usted.</p>
		<p>Para mas información ingrese a: ${ser}</p>
	`;
	const correos = await get_usuarios_a_notificar(id_tipo_solicitud, estado,motivo);
	enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', 'AGIL Talento Humano CUC', asunto, 'Par_TH', 3);;

};

const enviar_notificacion_colaborador = async (id_solicitud) => {	
	const ser = `<a href="${server}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
		asunto = `Solicitud de Entrega de Cargo`;
		mensaje = `
			<p>Se le informa que la Solicitud Entrega de cargo ha sido creada exitosamente. Para continuar con el proceso ingrese al siguiente link.
			<br>Omitir este correo, si fue usted el solicitante de esta.</p>
			<p>Para mas información ingrese a: ${ser}</p>
		`;
		const datos = await get_correo_colaborador_ecargo(id_solicitud);
		enviar_correo_personalizado('th', mensaje, datos.correo, datos.persona, 'AGIL Talento Humano CUC', asunto, 'Par_TH', 1);
}
const enviar_correo_jefe = async (data_correo) => {
	let { id, tipo_solicitud, correo_jefe, nombre_jefe } = data_correo;
	let titulo = `Solicitud de ${tipo_solicitud}`;
	const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
	let mensaje = `
		<p>Se ha creado una solicitud de  ${tipo_solicitud}. Esta necesita su <strong>Visto Bueno</strong>.</p>
		<p>Para mas información ingrese a: ${ser}</p>
	`;
	enviar_correo_personalizado('th', mensaje, correo_jefe, nombre_jefe, 'AGIL Talento Humano CUC', titulo, 'Par_TH', 1);
}
const enviar_notificacion_jefe = async (id_solicitud) => {	
	const ser = `<a href="${server}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
		asunto = `Solicitud de Entrega de Cargo`;
		mensaje = `
		<p>Se ha creado una solicitud de Entrega de cargo. Esta necesita su <strong>Visto Bueno</strong>.</p>
		<p>Para mas información ingrese a: ${ser}</p>
		`;
		const datos = await get_correo_jefe_inmediato2(id_solicitud);
		enviar_correo_personalizado('th', mensaje, datos.correo, datos.persona, 'AGIL Talento Humano CUC', asunto, 'Par_TH', 1);
}

const pintar_datos_combo = (datos, combo, mensaje, sele = '') => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach(({ id, valor }) => $(combo).append(`<option value='${id}'> ${valor}</option>`));
	if (sele) $(combo).val(sele);
};

const get_codigos_sap = () => {
	consulta_ajax(`${ruta}get_codigos_sap`, {}, (data) => {
		$('#tabla_codigos tbody').off('click', 'tr').off('click', 'tr span.codigo_sap');
		const myTable = $('#tabla_codigos').DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data,
			columns: [
				{ data: 'valor' },
				{ data: 'valorx' },
				{
					defaultContent:
						'<span style="color: #5cb85c" title="Seleccionar código" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default codigo_sap" ></span>'
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_codigos tbody').on('click', 'tr', function () {
			$('#tabla_codigos tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_codigos tbody').on('click', 'tr span.codigo_sap', function () {
			const { id, valorx } = myTable.row($(this).parent().parent()).data();
			codigo_sap = id;
			$('#span_codigo_sap').html(valorx);
			$('#modal_codigo_sap').modal('hide');
		});
	});
};

const getAnios = () => {
	const year = new Date().getFullYear();
	const pastYears = [{ id: year, valor: year }];
	for (let index = 1; index <= 5; index++) {
		const newYear = year - index;
		pastYears.unshift({ id: newYear, valor: newYear });
	}
	pintar_datos_combo(pastYears, '#cbx_anios', 'Seleccione año', year);
};

const guardar_solicitud_cert_ingresos = (form) => {
	const check = form.get('check_prestacion_servicio');
	enviar_formulario(
		`${ruta}guardar_solicitud_cert_ingresos`,
		form,
		({ mensaje, tipo, titulo, personas_notificar }) => {
			MensajeConClase(mensaje, tipo, titulo);
			if (tipo === 'success') {
				$('#form_nuevo_cir').get(0).reset();
				$('#modal_cir').modal('hide');
				if (personas_notificar.length) {
					const asunto = `Solicitud de CIR Registrada${!!check ? ' por OPS' : ''}`;
					const ser = `<a href="${server}index.php/talento_humano/"><b>agil.cuc.edu.co</b></a>`;
					const mensaje = `
						<p>Su solicitud de certificado de ingresos y retenciones${!!check
							? ' por prestación de servicios '
							: ' '}ha sido registrada exitosamente. En un máximo de 2 dias hábiles le será enviado su certificado.</p>
						<p>Más información en: ${ser}</p>`;
					enviar_correo_personalizado(
						'th',
						mensaje,
						personas_notificar,
						'',
						'AGIL Talento Humano CUC',
						asunto,
						'Par_TH',
						3
					);
				}
			}
		}
	);
};

const detalle_solicitud_certificado_ingresos = ({
	solicitante,
	fecha_registro,
	state,
	tipo_solicitud,
	aux,
	id_estado_solicitud,
	observacion
}) => {
	$('#tabla_detalle_certificado .info_solicitante').html(solicitante);
	$('#tabla_detalle_certificado .info_fecha').html(fecha_registro);
	$('#tabla_detalle_certificado .info_estado').html(state);
	$('#tabla_detalle_certificado .info_tipo').html(tipo_solicitud);
	$('.label_tipo_certificado').html('Año certificado');
	$('#tabla_detalle_certificado .info_tipo_certificado').html(aux);
	$(
		'#especificaciones_certificado, #especificaciones_certificado_opciones, #observaciones_certificado, #info_entrega_certificado'
	).css('display', 'none');

	if (observacion) {
		const textoObservaciones = id_estado_solicitud === 'Tal_Neg' ? 'Motivo de rechazo' : 'Observaciones';
		$("#observaciones_certificado .ttitulo").html(textoObservaciones);
		$('#tabla_detalle_certificado .info_observaciones').html(observacion);
		$('#observaciones_certificado').show('fast');
	} else $('#observaciones_certificado').css('display', 'none');
};

const get_usuarios_a_notificar_estado_posgrado = (id_estado, id_departamento) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}get_usuarios_a_notificar_estado_posgrado`, {
			id_departamento,
			id_estado
		}, (data) => resolve(data))
	})
}

const buscar_requisiciones = () => {
	consulta_ajax(`${ruta}buscar_requisiciones`, {}, (data) => {
		$('#tabla_requisiciones tbody').off('click', 'tr td span.requisicion');
		let num = 0;
		const myTable = $('#tabla_requisiciones').DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data,
			columns: [
				{ render: () => ++num },
				{ data: 'fullname' },
				{ data: 'cargo' },
				{ data: 'departamento' },
				{ data: 'tipo_solicitud' },
				{
					defaultContent:
						'<span style="color: #5cb85c" title="Seleccionar Solicitud" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default requisicion" ></span>'
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_requisiciones tbody').on('click', 'tr td span.requisicion', async function () {
			const { id, tipo_cargo, departamento, id_departamento, cargo_id, fullname, tipo_solicitud, responsable_id } = myTable.row($(this).parent()).data();
			id_persona = responsable_id;
			requisicion_id = id;
			departamento_id = id_departamento;
			$('#form_seleccion select[name=tipo_cargo]').val(tipo_cargo);
			$('#form_seleccion input[name=requisicion]').val(tipo_solicitud);
			$('#form_seleccion input[name=dependencia]').val(departamento);
			$('#form_seleccion input[name=nombre_responsable]').val(fullname);
			$('#form_seleccion .input_buscar_requisicion').val(tipo_solicitud);
			const cargos = await listar_cargos();
			pintar_datos_combo(cargos, '#form_seleccion select[name=cargo]', 'Seleccione Cargo', cargo_id);
			$('#modal_requisicion').modal('hide');
		});
	});
};

const cargar_competencias = () => {
	$('#cbxcompetencias').html(`<option value="">${competencias.length} Competencias Asignadas</option>`);
	competencias.forEach(({ id_competencia, nombre }) => $('#cbxcompetencias').append(`<option value=${id_competencia}>${nombre}</option>`));
};

const buscar_competencias = (dep = '') => {
	consulta_ajax(`${ruta}buscar_competencias`, { dep }, (data) => {
		let num = 0;
		$(`#tabla_competencias tbody`).off('click', 'tr').off('click', 'tr span.agregar').off('dblclick', 'tr');
		const myTable = $('#tabla_competencias').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ render: () => ++num },
				{ data: 'nombre' },
				{
					defaultContent:
						"<span class='btn btn-default agregar'><span class='fa fa-check' style='color:#5cb85c'></span></span>"
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_competencias tbody').on('click', 'tr', function () {
			$('#tabla_competencias tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_competencias tbody').on('dblclick', 'tr', function () {
			const data = myTable.row($(this).parent().parent()).data();
			marcar_nivel(data);
		});

		$('#tabla_competencias tbody').on('click', 'tr span.agregar', function () {
			const data = myTable.row($(this).parent().parent()).data();
			marcar_nivel(data);
		});
	});

	const marcar_nivel = (data) => {
		const comp = competencias.find((element) => element.id_competencia === data.id_competencia);
		if (!comp) {
			data_competencia = data;
			$('#form_nivel_competencia').get(0).reset();
			$('#modal_nivel_competencia').modal();
		} else MensajeConClase('Esta competencia ya ha sido asignada.', 'info', 'Ooops!');
	}
};

const agregar_competencia = () => {
	competencias.push(data_competencia);
	let tipo = $(`#form_nivel_competencia input[name=vb_comp]:checked`).val();
	let nivel = $(`#form_nivel_competencia select[name=nivel_competencia]`).val();
	let observaciones = $(`#form_nivel_competencia textarea[name=observaciones]`).val();
	competencias.map(function (dato) {
		if (dato.id_competencia == data_competencia.id_competencia) {
			dato.tipo = tipo;
			dato.nivel = nivel;
			dato.observaciones = observaciones;
		}
	});
	cargar_competencias();
	$("#modal_nivel_competencia").modal('hide');
}

const modificar_competencia = (id_competencia) => {
	let tipo = $(`#form_nivel_competencia input[name=vb_comp]:checked`).val();
	let nivel = $(`#form_nivel_competencia select[name=nivel_competencia]`).val();
	let observaciones = $(`#form_nivel_competencia textarea[name=observaciones]`).val();
	competencias.map(function (dato) {
		if (dato.id_competencia == id_competencia) {
			dato.tipo = tipo;
			dato.nivel = nivel;
			dato.observaciones = observaciones;
		}
	});
	cargar_competencias();
	$("#modal_nivel_competencia").modal('hide');
}

const Enviar_correo_sol_ausentismo = async (data) => {
	let { id, tipo_solicitud, correo_jefe, nombre_jefe } = data;
	let titulo = `Solicitud de ${tipo_solicitud}`;
	const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
	let mensaje = `
		<p>Se ha creado una solicitud de  ${tipo_solicitud}.</p>
		<p>Para mas información ingrese a: ${ser}</p>
	`;
	enviar_correo_personalizado('th', mensaje, correo_jefe, nombre_jefe, 'AGIL Talento Humano CUC', titulo, 'Par_TH', 1);

	// solicitante
	const { correo, persona } = await get_correo_solicitante(id);
	mensaje = `
		<p>Su solicitud de ${tipo_solicitud} ha sido creado exitosamente.</p>
		<p>Para mas información ingrese a: ${ser}</p>
	`;
	enviar_correo_personalizado('th', mensaje, correo, persona, 'AGIL Talento Humano CUC', titulo, 'Par_TH', 1);
}


const guardar_vacaciones = () => {

	let data = new FormData(document.getElementById("form_ausentismo_vacaciones"));
	data.append('id_tipo_ausentismo', id_tipo_ausentismo);
	data.append('jefe_inmediato', id_persona);

	enviar_formulario(`${ruta}guardar_vacaciones`, data, resp => {
		const { mensaje, titulo, tipo, data } = resp;
		if (tipo === 'success') {
			Enviar_correo_sol_ausentismo(data);
			$("#modal_ausentismo_vacaciones").modal('hide');
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const guardar_licencia = () => {

	let data = new FormData(document.getElementById("form_ausentismo_licencia"));
	data.append('id_tipo_ausentismo', id_tipo_ausentismo);
	data.append('jefe_inmediato', id_persona);

	enviar_formulario(`${ruta}guardar_licencia`, data, resp => {
		const { mensaje, titulo, tipo, data } = resp;
		if (tipo === 'success') {
			Enviar_correo_sol_ausentismo(data);
			$("#modal_ausentismo_licencia").modal('hide');
		}
		MensajeConClase(mensaje, tipo, titulo);
	});


}

const generar_reporte = () => {
	let tipo = $("#form_reporte select[name='tipo']").val();
	let fecha_inicio = $("#form_reporte input[name='fecha_inicio']").val();
	let fecha_fin = $("#form_reporte input[name='fecha_fin']").val();
	window.open(`${ruta}generar_reportes/${tipo}/${fecha_inicio}/${fecha_fin}`);
}

const vb_ausentismo = (form, data, mensaje_correo, asunto) => {
	const formData = new FormData(form);
	formData.append('id', id_solicitud);
	formData.append('id_tipo_solicitud', data.id_tipo_solicitud);
	enviar_formulario(`${ruta}vb_ausentismo`, formData, ({ mensaje, tipo, titulo, certificado }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			enviar_correo_personalizado('th',mensaje_correo,data.correo,data.solicitante,'AGIL Talento Humano CUC',asunto,'Par_TH',2);
			if(data.notificar_nomina) notificar_nomina(data);
			listar_solicitudes();
			$('#modal_vb_ausentismo').modal('hide');
		}
	});
};

const notificar_nomina = async (data) => {
	const titulo = `Solicitud de ${data.tipo_solicitud}`;
	const ser = `<a href="${server}index.php/talento_humano/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`;
	const mensaje = `
		<p>Se le informa que una solicitud de ${data.tipo_solicitud} ha sido <strong>APROBADA</strong> y est&aacute lista para ser gestionada por usted.</p>
		<p>Para mas información ingrese a: ${ser}</p>
	`;
	const correos = await get_usuarios_a_notificar(data.id_tipo_solicitud, 'Tal_Pro');
	enviar_correo_personalizado('th', mensaje, correos, 'Funcionario Talento Humano', 'AGIL Talento Humano CUC', titulo, 'Par_TH', 3);
}

const notificar_vb = async (nexstate) => {
	let { id, tipo_solicitud, id_tipo_solicitud } = info_solicitud;
	const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
	let asunto = `Solicitud de ${tipo_solicitud}`;
	const mensaje = `
		<p>Se le notifica que una solicitud de ${tipo_solicitud} recibio visto bueno por el jefe inmediato y se encuentra lista para ser gestionada por usted.</p>
		<p>Más información en: ${ser}</p>
	`;
	const correos = await get_usuarios_a_notificar(id_tipo_solicitud, nexstate);
	enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', 'AGIL Talento Humano CUC', asunto, 'Par_TH', 3);
}

const notificar_entidades = async (id_tipo_solicitud, estado, id) => {
	let tipo_solicitud = "";
	if (id_tipo_solicitud === 'Hum_Cam_Eps') {
		tipo_solicitud = 'Cambio de Eps';
	} else if (id_tipo_solicitud === 'Hum_Inc_Caja') {
		tipo_solicitud = 'Inclusión Beneficario Caja de Compensacion';
	} else if (id_tipo_solicitud === 'Hum_Inc_Eps') {
		tipo_solicitud = 'Inclusión Beneficario EPS';
	} else if (id_tipo_solicitud === 'Hum_Tras_Afp') {
		tipo_solicitud = 'Notificacion de traslados de EPS, AFP y/o AFC';
	}
	const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
	let asunto = `Solicitud de ${tipo_solicitud}`;
	const mensaje = `
		<p>Se le notifica que una solicitud de ${tipo_solicitud} ha sido enviada para ser gestionada por usted.</p>
		<p>Más información en: ${ser}</p>
	`;
	const correos = await get_usuarios_a_notificar(id_tipo_solicitud, estado);
	enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', 'AGIL Talento Humano CUC', asunto, 'Par_TH', 3);
}

const notificar_finalizada = async (id_tipo_solicitud, estado, id) => {
	let tipo_solicitud = "";
	if (id_tipo_solicitud === 'Hum_Entr_Cargo') {
		tipo_solicitud = 'Entrega de cargo';
	}
	const ser = `<a href="${server}index.php/talento_humano/${id}"><b>agil.cuc.edu.co</b></a>`;
	let asunto = `Solicitud de ${tipo_solicitud}`;
	const mensaje = `
		<p>Se le notifica que una solicitud de ${tipo_solicitud} esta en su estado final donde usted podra descargar los archivos adjuntos de esta, con el fin de que estos reposen en el expediente laboral del colaborador.</p>
		<p>Más información en: ${ser}</p>
	`;
	const correos = await get_usuarios_a_notificar(id_tipo_solicitud, estado);
	enviar_correo_personalizado('th', mensaje, correos, 'Funcionario', 'AGIL Talento Humano CUC', asunto, 'Par_TH', 3);
}


const guardar_cambio_eps = () => {

	let data = new FormData(document.getElementById("form_cambio_eps"));

	enviar_formulario(`${ruta}guardar_cambio_eps`, data, resp => {
		const { mensaje, titulo, tipo, id_solicitud } = resp;
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			notificar_entidades('Hum_Cam_Eps', "Tal_Env", id_solicitud);
			$("#modal_cambio_eps").modal('hide');
		}

	});
}
const guardar_ben_eps = () => {

	let data = new FormData(document.getElementById("form_inc_eps"));

	enviar_formulario(`${ruta}guardar_ben_eps`, data, resp => {
		const { mensaje, titulo, tipo, id_solicitud } = resp;
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			notificar_entidades('Hum_Inc_Eps', "Tal_Env", id_solicitud);
			$("#modal_inc_eps").modal('hide');
		}

	});
}

const guardar_ben_caja = () => {

	let data = new FormData(document.getElementById("form_inc_caj"));

	enviar_formulario(`${ruta}guardar_ben_caja`, data, resp => {
		const { mensaje, titulo, tipo } = resp;
		MensajeConClase(mensaje, tipo, titulo, id_solicitud);
		if (tipo === 'success') {
			notificar_entidades('Hum_Inc_Caja', "Tal_Env", id_solicitud);
			$("#modal_inc_caj").modal('hide');
		}

	});
}
const guardar_traslado_afp = () => {

	let data = new FormData(document.getElementById("form_tras_afp"));

	enviar_formulario(`${ruta}guardar_traslado_afp`, data, resp => {
		const { mensaje, titulo, tipo, id_solicitud } = resp;
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo === 'success') {
			notificar_entidades('Hum_Tras_Afp', "Tal_Env", id_solicitud);
			$("#modal_tras_afp").modal('hide');
		}

	});
}

const guardar_ecargo = () => {
	let data = new FormData(document.getElementById("form_agregar_cargo"));
	let motivo= ecargo[0];
	let ecargo_admin =ecargo[1];
	data.append('id_colaborador', id_colaborador);
	data.append('id_jefe_cargo', id_jefe_cargo);
	data.append('id_jefe_cargo1', id_jefe_cargo1);
	data.append('motivo', motivo);
	data.append('ecargo_admin', ecargo_admin);
	enviar_formulario(`${ruta}guardar_ecargo`, data,resp => {
		const { mensaje, titulo, tipo, id_solicitud, data_correo } = resp;
		if (tipo === 'success') {
			enviar_notificacion_ecargo('Hum_Entr_Cargo', "Tal_Env", id_solicitud, motivo);
			enviar_notificacion_colaborador(id_solicitud);
			enviar_correo_jefe(data_correo);
			if(id_colaborador == null){
				enviar_notificacion_th('Hum_Entr_Cargo', "Tal_TH", id_solicitud,motivo);
			} else if(id_jefe_cargo1 != null){
				enviar_notificacion_jefe(id_solicitud);
			}
			$("#modal_ecargo").modal('hide');
			$("#modal_agregar_cargo").modal('hide');
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
}

const modificar_agregar_cargo = ( id) => {
	let data = new FormData(document.getElementById("form_agregar_cargo"));
	data.append('id_solicitud', id);
	enviar_formulario(`${ruta}modificar_agregar_cargo`, data,resp => {
		let { tipo, mensaje, titulo } = resp;
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo == 'success') {
			$('#modal_agregar_cargo').modal('hide');
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
};


const obtener_adjunto = (id) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}obtener_adjunto`, { id }, (resp) => resolve(resp));
	});
}
const detalle_inc_bene = (id) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}detalle_inc_bene`, { id }, (resp) => resolve(resp));
	});
}
const detalle_entrecargo = (id) => {
	return new Promise((resolve) => {
		consulta_ajax(`${ruta}detalle_entrecargo`, { id }, (resp) => resolve(resp));
	});
}

const detalle_cambio_eps = async (data) => {
	let {
		solicitante, state, fecha_registro, tipo_solicitud, correo, eps_destino, eps_actual
	} = data;
	$('.info_solicitante').html(solicitante);
	$('.info_estado').html(state);
	$('.info_fecha').html(fecha_registro);
	$('.info_t_solicitud').html(tipo_solicitud);
	$('.info_correo').html(correo);
	$('.info_eps_actual').html(eps_actual);
	$('.info_eps_destino').html(eps_destino);
	$('#tr_ordensap').css('display', 'none');
	$('#modal_detalle_cambio_eps').modal();

}

const detalle_ecargo = async (data) => {
	let { id, solicitante, state, fecha_registro, tipo_solicitud, correo, jefe_responsable, id_solicitante, responsabilidades_ecargo, accesos_ecargo, informes_ecargo, comites_ecargo,logros_ecargo} = data;
	id_solicitud = id;
	let { colaborador, jefe,motivo } = await detalle_entrecargo(id);
	$('#colaborador_id').val(id_solicitante);
	$('.info_solicitante').html(solicitante);
	$('.info_estado').html(state);
	$('.info_fecha').html(fecha_registro);
	$('.info_t_solicitud').html(tipo_solicitud);
	$('.info_correo').html(correo);
	$('#tr_ordensap').css('display', 'none');
	$('.info_motivo').html(motivo);
	$('.info_jefe_e').html(jefe_responsable);
	$('.info_colaborador').html(colaborador);
	$('.info_jefe_e1').html(jefe);
	$('.info_responsabilidades').html(responsabilidades_ecargo);
	$('.info_accesos').html(accesos_ecargo);
	$('.info_informes').html(informes_ecargo);
	$('.info_comites').html(comites_ecargo);
	$('.info_logros').html(logros_ecargo);
	$('#modal_detalle_ecargo').modal();

}

const detalle_inc_ben = async (data) => {
	let {
		id, solicitante, state, fecha_registro, tipo_solicitud, correo
	} = data;
	id_solicitud = id;
	let { tipo, tipo_d, barrio, direccion, correo_ben, lugar_residencia, telefono, identificacion } = await detalle_inc_bene(id);
	$('.info_solicitante').html(solicitante);
	$('.info_estado').html(state);
	$('.info_fecha').html(fecha_registro);
	$('.info_t_solicitud').html(tipo_solicitud);
	$('.info_correo').html(correo);
	$('.info_t_beneficiario').html(tipo);
	$('.info_t_documento').html(tipo_d);
	$('.info_barrio').html(barrio);
	$('.info_direccion').html(direccion);
	$('.info_telefono').html(telefono);
	$('.info_identificacion').html(identificacion);
	$('.info_ciudad').html(lugar_residencia);
	$('.info_correo_ben').html(correo_ben);
	$('#tr_ordensap').css('display', 'none');
	$('#modal_detalle_inc_ben').modal();

}

const documentos_gestion = (id_solicitud) => {
	let num = 0;
	consulta_ajax(`${ruta}obtener_adjunto`, { id: id_solicitud }, (resp) => {
		$(`#documentos_gestion_entidades tbody`)
			.off('dblclick', 'tr')
			.off('click', 'tr')
			.off('click', 'tr td .ver');
		const myTable = $('#documentos_gestion_entidades').DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{ render: () => ++num },
				{ data: 'nombre_real' },
				{
					defaultContent:
						"<a target='blank'class='btn btn-default ver '><span class='fa fa-eye' style='color:#5cb85c'></span></a>"
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#documentos_gestion_entidades tbody').on('click', 'tr', function () {
			$('#documentos_gestion_entidades tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});
		$('#documentos_gestion_entidades tbody').on('dblclick', 'tr', function () {
			let data = myTable.row($(this).parent().parent()).data();

		});
		$('#documentos_gestion_entidades tbody').on('click', 'tr td .ver', function () {
			let data = myTable.row($(this).parent().parent()).data();
			$('.ver').attr('href', `${Traer_Server()}${ruta_documentos_gestion}${data.nombre_archivo}`)
		});
	});
}

const detalle_traslado_afp = async (data) => {
	let {
		solicitante, state, fecha_registro, tipo_solicitud, correo} = data;
	$('.info_solicitante').html(solicitante);
	$('.info_estado').html(state);
	$('.info_fecha').html(fecha_registro);
	$('.info_t_solicitud').html(tipo_solicitud);
	$('.info_correo').html(correo);
	$('#tr_ordensap').css('display', 'none');
	$('#modal_detalle_traslado_afp').modal();

}
/* Funcion para darle el active al item seleccionado */

const active_place = (modal_id, target) => {
	//Cinta de opciones
	$(`${modal_id} li`).removeClass('active');
	$(`${modal_id} li[id="${target}"]`).addClass('active');
	//Tablas
	$(`${modal_id} div[data-sw="sw"]`).hide();
	$(`${modal_id} div[data-place="${tablaActiva}"]`).fadeIn('fast'); 
}

const listar_vb_ecargo = (id_solicitud) => {
	consulta_ajax(`${ruta}get_actividades_ecargo`, { id: id_solicitud }, (resp) => {
		$(`#tabla_vb_ecargo tbody`)
			.off('dblclick', 'tr')
			.off('click', 'tr td ')
			.off('click', 'tr td.vistom')
			.off('click', 'tr td.vistob');
			const myTable = $('#tabla_vb_ecargo').DataTable({
				destroy: true,
				searching: false,
				processing: true,
				data: resp,
				columns: [
					{ data: 'nombre_estado'},
					{
						"render": function (data, type, full, meta) {
							let res = '<span title="Sin Acción" data-toggle="popover" data-trigger="hover" class="btn fa fa-toggle-off"></span>';
							let { valor_b } = full;
								res = '<span title="Desaprobar" data-toggle="popover" data-trigger="hover" style="color: #d9534f" class="btn btn-default fa fa-thumbs-down vistom"></span>';
								if (valor_b == 1) res = '<span title="Aprobar" data-toggle="popover" data-trigger="hover" style="color: #5cb85c" class="btn btn-default fa fa-thumbs-up vistob"></span>';
							return res;
						}
					}
				],
				language: get_idioma(),
				dom: 'Bfrtip',
				buttons: []
			});
		
		$('#tabla_vb_ecargo tbody').on('click', 'tr', function () {
			$('#tabla_vb_ecargo tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_vb_ecargo tbody').on('click', 'tr td .vistom', async function () {
			let datos = myTable.row($(this).parent().parent()).data();
			visto_bueno = datos.estado;
			title = 'Rechazar solicitud';
			msj_confirmacion_input(
				title,
				'Por favor digite motivo de rechazo de la solicitud.',
				'Motivo de Rechazo',
				(comentario) => {
						swal.close();
						guadar_estado_ecargo(id_solicitud,comentario,visto_bueno);
				}
			);
		});

		$('#tabla_vb_ecargo tbody').on('click', 'tr td .vistob', async function () {
			let datos = myTable.row($(this).parent().parent()).data();
			visto_bueno = datos.estado;
			title = 'Motivo Aprobacion solicitud';
			msj_confirmacion_input(
				title,
				'Por favor digite motivo de Aprobación de la solicitud.',
				'Motivo de Aprobación',
				(comentario) => {
						swal.close();
						guadar_estado_ecargo(id_solicitud,comentario,visto_bueno);
				}
			);
		});

	});
}

const modal_modificar_entrega_cargo = (data) => {
		callbak_activo_aux = (resp) => modificar_agregar_cargo(id);
		let { id, responsabilidades_ecargo, accesos_ecargo, informes_ecargo, comites_ecargo,logros_ecargo } = data;
		$(`#form_agregar_cargo textarea[name="responsabilidades_ecargo"]`).val(responsabilidades_ecargo);
		$(`#form_agregar_cargo textarea[name="accesos_ecargo"]`).val(accesos_ecargo);
		$(`#form_agregar_cargo textarea[name="informes_ecargo"]`).val(informes_ecargo);
		$(`#form_agregar_cargo textarea[name="comites_ecargo"]`).val(comites_ecargo);
		$(`#form_agregar_cargo textarea[name="logros_ecargo"]`).val(logros_ecargo);
		$('#modal_agregar_cargo').modal();
};
const documentos_ecargo = (id_solicitud) => {
	let num = 0;
	consulta_ajax(`${ruta}obtener_adjunto`, { id: id_solicitud }, (resp) => {
		$(`#documentos_gestion_entidades tbody`)
			.off('dblclick', 'tr')
			.off('click', 'tr')
			.off('click', 'tr td .ver');
		console.log(resp);
		const myTable = $('#documentos_gestion_entidades').DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{ render: () => ++num },
				{ data: 'nombre_real' },
				{
					defaultContent:
						"<a target='blank'class='btn btn-default ver '><span class='fa fa-eye' style='color:#5cb85c'></span></a>"
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#documentos_gestion_entidades tbody').on('click', 'tr', function () {
			$('#documentos_gestion_entidades tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});
		$('#documentos_gestion_entidades tbody').on('dblclick', 'tr', function () {
			let data = myTable.row($(this).parent().parent()).data();

		});
		$('#documentos_gestion_entidades tbody').on('click', 'tr td .ver', function () {
			let data = myTable.row($(this).parent().parent()).data();
			$('.ver').attr('href', `${Traer_Server()}${ruta_documentos_ecargo}${data.nombre_archivo}`)
		});
	});
}

