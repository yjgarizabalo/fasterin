const tipo_modulo = 'Inv_Man';
let articulos = [];
let operarios = [];
let id_solicitud = "";
let id_persona_solici_tabla = '';
let categoria_s = "";
let sol_state = "";
let usuario_s = '';
let correo_s = '';
let nombre_s = '';
let sw = false;
let lim = '';
let id_mantenimiento = null;
let tipo_mantenimiento = "";
let fecha_matt = "";
let callback_activo =  (resp) =>{ };
let objetos = [];
let id_lugar = null;
let id_ubicacion = null;
let id_historial_mtto = null;
let id_objeto = null;
let id_mantenimiento_periodico_matto = null;
let id_registro_matto_lugar = null;
let id_mantenimiento_periodico = null;
let datos_detalle_edit = '';
let tomo_foto = false;
let id_estado_mtto = "";
let objetos_sin_estado = [];
let tipo_de_registro = "";


// let ruta_firmas = 'archivos_adjuntos/almacen/firmas/';
// let firma = false;
// let solo_encuesta = '';
const url = `${Traer_Server()}index.php/mantenimiento_control/`;
let modulo = '';
$(document).ready(() => {
	$("#form_filter").submit(e => {
		e.preventDefault();
		listar_solicitudes();
	});

	$("#solt3").click(() => {
		$('.solicitudes').fadeIn();
		$('#menu_principal').css('display', 'none');
		$(".div_inv").removeAttr('required').hide();
		$('#elementos').hide();
	});

	$(".rating").click(() => {
		($("#rate1").prop('checked') || $("#rate2").prop('checked') || $("#rate3").prop('checked'))
			? $("#rate_observation").prop('required', true)
			: $("#rate_observation").prop('required', false);
	})

	$("#btnoperarios").click(() => {
		$("#modalOperarios").modal();
		get_operarios();
	});

	$("input:radio").click(function () { $(this).blur() });

	$("#guardar_calificacion").submit(() => {
		const data = formDataToJson(new FormData(document.getElementById("guardar_calificacion")));
		// const image = document.getElementById("canvas").toDataURL();
		// data.image = image;
		data.id = id_solicitud;
		data.sw = sw;
		calificar_solicitud(data);
		return false;
	});

	$("#btn_administrar").click(() => {
		$("#Modal-categorias").modal();
		get_categorias();
	});

	$("#frm_agregar_operario").submit(() => {
		const operario = $("#cbx_agregar_operario").val();
		agregar_nuevo_operario(operario);
		return false;
	});

	$("#FrmgestionarSolicitud").submit(() => {
		gestionar_solicitud();
		return false;
	});

	$("#form_registrar_evidencia").submit(() => {
		guardar_evidencia();
		return false;
	});

	$("#btn_asignar").click(() => agregar_operario());

	$("#chkelementos").click(function () { $(this).prop('checked') ? $("#elementos").show('slow') : $("#elementos").hide('slow'); });
	$("#chkprogramar").click(function () { $(this).prop('checked') ? $("#fechas").show('slow') : $("#fechas").hide('slow'); });

	$("#tiempo").change(e => {
		if (e.target.value === 'programar') {
			$("#fechas").fadeIn('slow');
		} else {
			$("#fechas").fadeOut('slow');
			$(".CampoGeneral").val('');
		};
	});

	$('.btn_return').click(() => {
		$('#menu_principal').fadeIn();
		$('.solicitudes').css('display', 'none');
	});

	$("#sel_art").click(() => {
		buscar_articulo(-1);
		$("#Buscar_Articulo").modal();
		$("#txtarticulo").val("");
	});

	$("#FrmBuscar_Articulo").submit(() => {
		buscar_articulo($("#txtarticulo").val());
		return false;
	});

	$("#nueva_solicitud").click(() => {
		articulos = [];
		$('#chkelementos').prop('checked', false);
		if (lim) {
			MensajeConClase('Por favor califique las solicitudes pendientes para poder registrar una nueva solicitud.', 'info', 'Solicitudes sin Calificar!');
			$('.solicitudes').fadeIn('slow');
			$('#menu_principal').css('display', 'none');
			$(".div_inv").removeAttr('required').hide();
			listar_solicitudes('x');
			return;
		}
		$('#elementos').hide();
		usuario_s = '';
		$('#persona').html('Seleccione Persona');
		get_articulos_agregados(articulos);
		$("#modalSolicitud").modal();
	});

	$("#frmAgregarSolicitud").submit(() => {
		// if ($('#chkelementos').prop('checked') && articulos.length == 0) {
		// 	MensajeConClase('Por favor agregue al menos un artículo.', 'info', 'Ooops!');
		// } else {
		guardar_solicitud();
		$("#frmAgregarSolicitud").get(0).reset();
		$("#modalSolicitud").modal('hide');
		// }
		return false;
	});

	$('#cbxcategoria').change(function () {
		const categoria = $(this).val();
		traer_operarios(categoria);
		operarios = [];
		cargar_operarios(operarios);
	});

	$("#detalle_persona_solicita").click(() => {
		obtener_datos_persona_id_completo(id_persona_solici_tabla, ".nombre_perso", ".apellido_perso", ".identi_perso", ".tipo_id_perso", ".foto_perso", ".ubica_perso", ".depar_perso", ".cargo_perso", ".perfil_perso", ".celular");
		$("#Mostrar_detalle_persona").modal("show");
	});

	$("#ver_operarios").click(() => {
		traer_operarios_solicitud();
		$("#Modal-info-operarios").modal();
	});

	$("#ver_historial").click(() => {
		traer_historial_solicitud();
		$("#Modal-info-historial").modal();
	});

	$("#frm_solicitante").submit(() => {
		const password = $("#txtpassword").val();
		sw = 1;
		verificarPassword("", password);
		return false;
	});

	$("#frm_otro").submit(() => {
		const user = $("#txt_usuario").val();
		const password = $("#txt_password").val();
		verificarPassword(user, password);
		return false;
	});

	$("#btn_limpiar_filtros").click(() => {
		$(".filtro").val('');
		listar_solicitudes();
	});

	$("#btn_limpiar_filtros_gestion").click(() => {
		$(".filtro").val('');
		listar_solicitudes_gestion();
	});

	$("#persona").click(() => {
		$('#txtbuscarPersona').val('');
		$('#modalPersonas').modal();
		cargar_personas();
	});

	$('#btn_borrar_persona').click(() => {
		usuario_s = '';
		$('#persona').html('Seleccione Persona');
		MensajeConClase('Persona desasignada exitosamente!', 'success', 'Proceso Exitoso!');
	});

	$('#frmbuscar_persona').submit(() => {
		const buscar = $('#txtbuscarPersona').val();
		cargar_personas(buscar);
		return false;
	});

	$('#form_estado_lugares_mantenimiento').submit(() => {
		let estado = $("#modal_lugares_estados_matto select[name='estado_lugares_matto']").val();
		if(!estado){
			MensajeConClase('Antes de continuar debe seleccionar un estado', 'info', 'Atención.!')
		}else if(estado){
			guardar_estado_lugares_mantenimientos(estado);
		}
		return false;
	});

	$('#frm_editar_prioridad').submit(() => {
		const prioridad = $("#cbxChangePriority").val();
		const nombre = $("#cbxChangePriority option:selected").text();
		cambiar_prioridad(prioridad, nombre);
		return false;
	});

	$('#form_agregar_mantenimiento').submit((e) => {
		e.preventDefault();
		guardar_sol_mantenimiento('form_agregar_mantenimiento');
	});

	$('#form_inspeccion_preventivo').submit((e) => {
		e.preventDefault();
		guardar_sol_mantenimiento('form_inspeccion_preventivo');
		//const nombre = $("#cbxChangePriority option:selected").text();
		//cambiar_prioridad(prioridad, nombre);
		// return false;
	});

	$('#form_estado_objetos_mantenimiento').submit((e) => {
		e.preventDefault();
		guardar_estado_objetos_mantenimiento('form_estado_objetos_mantenimiento');
	});


	$("#cambiar_prioridad").click(() => {
		$("#Modal-change-priority").modal();
	});
	
	
	$("#nav_admin_mantenimiento li").click(function () {
		$("#nav_admin_mantenimiento li").removeClass("active");
		$(this).addClass("active");
		if ($(this)[0].classList.contains("btn_admin_operarios")) {
			$("div.adm_proceso").hide();
			$("div.container_admin_valores").fadeIn();
		} else if ($(this)[0].classList.contains("btn_admin_mantenimiento")) {
			tipo_mantenimiento = "T_Matt_anual";
			$("div.adm_proceso").hide();
			$("div.articulos_cumplidos").fadeIn();
			get_solicitud_matenimiento('T_Matt_anual');
		} else if ($(this)[0].classList.contains("btn_admin_preventivo")) {
			tipo_mantenimiento = "T_Matt_preventivo";
			$("div.adm_proceso").hide();
			$("div.mantenimiento_preventivo").fadeIn();
			get_solicitud_inspeccion_preventiva('T_Matt_preventivo');
		}
	});


	
	$("#AgregarNuevoMtto").click(() => {
		tipo_de_registro = "guardar";
		$("#Modal_Add_Mtto").modal();
		return false;
	});

	$("#AgregarNuevaInspeccionPreventiva").click(() => {
		$("#modal_inspeccion_preventiva").modal();
		return false;
	});

	$("#form_agregar_mantenimiento select[name='lugar']").change(function () {
		const id = $(this).val().trim();
		listar_ubicaciones(id, '#form_agregar_mantenimiento select[name="ubicacion"]');
	});

	$("#form_inspeccion_preventivo select[name='lugar']").change(function () {
		const id = $(this).val().trim();
		listar_ubicaciones(id, '#form_inspeccion_preventivo select[name="ubicacion"]');
	});
	
	$("#mas_objetos_reparar, #mas_objetos_reparar2").click(() => {
		callback_activo = (data) =>{
			let { id, valor } = data;
			$("#objeto_asignado_reparacion, #objeto_asignado_reparacion2").html('');
			if(objetos.length === 0){
				 objetos.push({'id': id, 'nombre': valor, 'cantidad': 1});
			}else{
				const dep = objetos.find((element) => element.id === id);
   				 if (!dep) objetos.push({'id': id, 'nombre': valor, 'cantidad': 1});	
				else dep.cantidad += 1;
			}
			$("#objeto_asignado_reparacion, #objeto_asignado_reparacion2").html(`<option value="">${objetos.length} Objetos Agregados</option>`);
			objetos.forEach((element) => $("#objeto_asignado_reparacion, #objeto_asignado_reparacion2").append(`<option value="${element.id}">${element.cantidad} ${element.nombre}</option>`));
			$("#modal_seleccionar_objeto").modal('hide');
		}
		// El idparametro = 267 es utilizado para listar  parametros de lugares en ambientes de Development
		// El idparametro = 342 es utilizado para listar  parametros de lugares en ambientes de Productions
		listar_objetos_mantenimiento(342, callback_activo);
		$("#modal_seleccionar_objeto").modal();
		return false;
	});

	$("#retirar_objeto_sele, #retirar_objeto_sele2").click(function () {
		var s1 = $("#objeto_asignado_reparacion").val();
		var s2 = $("#objeto_asignado_reparacion2").val();

		if (s1.length == 0 && s2.length == 0) {
			MensajeConClase("Seleccione Recurso a Retirar..!", "info", "Oops...")
		} else {
			swal({
				title: "Estas Seguro ?",
				text: "Atencion antes de continuar tener en cuenta que al retirar el recurso este no se tendra en cuenta al momento de terminar la reserva.!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Retirar!",
				cancelButtonText: "No, Regresar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true
			},
				function (isConfirm) {
					if (isConfirm) {
						let dato = s1 ? s1 : s2;
						Retirar_objeto_seleccionado(dato);
					}
				});

		}

	});

	$("#btn_evidencia_mantenimiento").click(() => {
		$("#Modal-categorias").modal();
		get_categorias();
	});

	

	$('#frm_buscar_objeto').submit((e) => {
		let dato = $("#txt_objeto_buscado").val();
		buscar_objetos_inspeccion_mantenimiento(dato, callback_activo);
		return false;
	});

	$('#frm_buscar_lugar').submit((e) => {
		let dato = $("#txt_lugar").val();
		buscar_lugar_mantenimiento_periodico(dato, callback_activo);
		return false;
	});

	$("#btn_agregar_objeto_nuevo").click(() => {
		$("#modal_guardar_objetos_mantenimiento").modal();
	});

	$('#GuardarObjetosMantenimiento').submit((e) => {
		guardar_objeto_valor_parametro();
		return false;
	});


	$('#btn_guardar_historial_mantenimiento').click(() => {
		guardar_historial_mantenimiento();
		return false;
	});


	$("#form_filter_gestion select[name='lugar']").change((e) => {
		const id = e.target.value;
		listar_ubicaciones(id, '#form_filter_gestion select[name="ubicacion"]');
	});

	$("#btn_filtrar_mantenimiento_periodico").click(()  => {
		$("#form_filter_mantenimiento_periodico").get(0).reset();
		listar_mantenimiento_periodico_filtro('', '', '', '', '', '',);
		$("#modal_filtro_mantenimiento_periodico").modal('show');
	})

	$("#btn_generar_filtro_mantenimiento_periodico").click(() =>{
		let id_lugar = $("#form_filter_mantenimiento_periodico select[name='lugar']").val();
		let id_periodicidad = $("#form_filter_mantenimiento_periodico select[name='periodicidad']").val();
		let estado = $("#form_filter_mantenimiento_periodico select[name='estado']").val();
		let id_tipo = $("#form_filter_mantenimiento_periodico select[name='tipo']").val();
		let fecha_inicio = $("#form_filter_mantenimiento_periodico input[name='fecha_inicio']").val();
		let fecha_fin = $("#form_filter_mantenimiento_periodico input[name='fecha_fin']").val();
		listar_mantenimiento_periodico_filtro(id_lugar, id_periodicidad, estado, id_tipo, fecha_inicio, fecha_fin);
	})

	$("#btn_filtrar_gestion").click(()  => {
		$("#form_filter_gestion").get(0).reset();
		listar_mantenimiento_gestion_filtro('', '', '', '', '');
		$("#modal_filtro_gestion").modal('show');

	})

	$("#btn_generar_filtro").click(() =>{
		let id_lugar = $("#form_filter_gestion select[name='lugar']").val();
		let id_ubicacion = $("#form_filter_gestion select[name='ubicacion']").val();
		let estado = $("#form_filter_gestion select[name='estado']").val();
		let fecha_inicio = $("#form_filter_gestion input[name='fecha_inicio']").val();
		let fecha_fin = $("#form_filter_gestion input[name='fecha_fin']").val();
		let id_estado_objeto = $("#form_filter_gestion select[name='tipo']").val();
		listar_mantenimiento_gestion_filtro(id_lugar, id_ubicacion, estado, fecha_inicio, fecha_fin, id_estado_objeto);
	})

	
	$("#btn_agregar_objeto_mantenimiento_gestion").click(() => {
		$("#txt_objeto_buscado").val('');
		buscar_objetos_inspeccion_mantenimiento();
		$("#modal_buscar_objetos_mantenimiento_gestion").modal();
	});

	$("#editar_solicitud").click(() => {
		tipo_de_registro = "modificar";
		

		$("#nombre_mantenimiento").val(datos_detalle_edit.nombre_mantenimiento);
		$("#periodicidad").val(datos_detalle_edit.id_periodicidad)
		$("#numero_notificaciones").val(datos_detalle_edit.numero_notificaciones);
		$("#Modal_Add_Mtto select[name='mes_inicio_notificacion']").val(datos_detalle_edit.id_mes_inicio_not);
		$("#dia_entre_notificacion").val(datos_detalle_edit.dia_entre_notificacion);
		$("#observacion_mantenimiento").val(datos_detalle_edit.observacion_mantenimiento);
		$("#id_solicitud_mantenimiento").val(datos_detalle_edit.id);

		$("#Modal_Add_Mtto").modal("show");
	})


});

const listar_solicitudes = (id = '') => {
	// id_persona_solici_tabla = 0;
	const filter = new FormData(document.getElementById("form_filter"));
	filter.append('id', id);
	consulta_ajax(`${url}Listar_solicitudes`, formDataToJson(filter), data => {
		const datos = data.filtro ? '<span class="fa fa-bell red"></span>La tabla tiene algunos filtros aplicados.' : '';
		lim = data.lim;
		$('#textAlerta_solicitudes').html(datos);
		$('#tabla_solicitudes tbody')
			.off('dblclick', 'tr')
			.off('click', 'tr')
			.off('click', 'tr td:nth-of-type(1)')
			.off('click', 'tr span.pausar')
			.off('click', 'tr span.denegar')
			.off('click', 'tr span.ejecutar')
			.off('click', 'tr span.encuesta')
			.off('click', 'tr span.gestionar');
		let num = 0;
		const myTable = $("#tabla_solicitudes").DataTable({
			destroy: true,
			data: data.data,
			processing: true,
			columns: [
				{ "data": "ver" },
				{ render: (data, type, { num }, meta) => num ? num : '----' },
				{ "data": "fullname" },
				{ "data": "departamento" },
				{ render: (data, type, { categoria }, meta) => categoria ? categoria : '----' },
				{ "data": "fecha" },
				{ render: (data, type, { f_recibido }, meta) => f_recibido ? f_recibido : '----' },
				{ render: (data, type, { f_ejecutado }, meta) => f_ejecutado ? f_ejecutado : '----' },
				{ render: (data, type, { f_ejecutado }, meta) => f_ejecutado ? f_ejecutado : '----' },
				{ "data": "estado" },
				{ render: (data, type, { calificacion }, meta) => calificacion ? calificacion : '----' },
				// { render: (data, type, { tiempo }, meta) => tiempo ? tiempo : '----' },
				{ "data": "gestion" },
				{ render: (data, type, { observacion }, meta) => observacion ? observacion : '----' },
			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": get_botones(),
		});
		myTable.column(6).visible(false);
		myTable.column(7).visible(false);
		myTable.column(10).visible(false);
		// myTable.column(10).visible(false);
		myTable.column(12).visible(false);


		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tabla_solicitudes tbody').on('click', 'tr', function () {
			$("#tabla_solicitudes tbody tr").removeClass("warning");
			$(this).addClass("warning");
			const { resp, state, usuario } = myTable.row(this).data();
			id_persona_solici_tabla = resp;
			sol_state = state;
			usuario_s = usuario;
		});

		$('#tabla_solicitudes tbody').on('dblclick', 'tr', function () {
			const data = myTable.row(this).data();
			categoria_s = data.cat;
			$("#Modal-info-solicitud").modal();
			detalles_solicitud(data);
			sol_state = data.state;
			id_persona_solici_tabla = data.resp;
			usuario_s = data.usuario;
		});
		
		$('#tabla_solicitudes tbody').on('click', 'tr td:nth-of-type(1)', function () {
			const data = myTable.row(this).data();
			categoria_s = data.cat;
			sol_state = data.state;
			usuario_s = data.usuario;
			$("#Modal-info-solicitud").modal();
			detalles_solicitud(data);
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.gestionar', function () {
			$("#FrmgestionarSolicitud").get(0).reset();
			$('#cbxoperarios').html(`<option value=''>Seleccione Operario</option>`);
			$("#fechas").hide('fast');
			const { id, correo, fullname } = myTable.row($(this).parent()).data();
			modal_gestionar(id);
			correo_s = correo;
			nombre_s = fullname;
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.pausar', function () {
			const { id, correo, fullname } = myTable.row($(this).parent()).data();
			pausar_solicitud(id, correo, fullname);
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.denegar', function () {
			const { id, correo, fullname } = myTable.row($(this).parent()).data();
			denegar_solicitud(id, correo, fullname);
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.ejecutar', function () {
			const { id } = myTable.row($(this).parent()).data();
			ejecutar(id);
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.encuesta', function () {
			const { id } = myTable.row($(this).parent()).data();
			mostrar_encuesta(id);
		});

		$('#tabla_solicitudes tbody').on('click', 'tr span.cancelar', function () {
			const { id } = myTable.row($(this).parent()).data();
			cancelar_solicitud(id);
		});
	});
};

const buscar_articulo = art => {
	let num = 1;
	if (art === '') {
		MensajeConClase("Digite una letra o frase para buscar artículo.", "info", "Oops...");
	}
	$('#tabla_buscar_articulos tbody').off('dblclick', 'tr').off('click', 'tr td:nth-last-child(1) span:first-of-type');
	let myTable = $("#tabla_buscar_articulos").DataTable({
		destroy: true,
		searching: false,
		ajax: {
			url: `${server}index.php/mantenimiento_control/buscar_articulo`,
			data: { art, tipo_modulo, },
			dataSrc: data => data ? data : [],
			dataType: 'json',
			type: 'post',
		},
		processing: true,
		columns: [{ render: () => num++ }, { data: 'nombre' },
		{
			render: () => `<span title='Mas Informacion' data-toggle='popover' data-trigger='hover' class='btn btn-default red fa fa-plus'></span>`
		}],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_buscar_articulos tbody').on('dblclick', 'tr', function () {
		const data = myTable.row(this).data();
		$("#tabla_buscar_articulos tbody tr").removeClass("success");
		$(this).attr("class", "success");
		agregar_articulo(data);
	});

	$('#tabla_buscar_articulos tbody').on('click', 'tr td:nth-last-child(1) span:first-of-type', () => {
		const data = myTable.row($('#tabla_buscar_articulos tbody tr')).data();
		agregar_articulo(data);
	});
}

const get_articulos_agregados = data => {
	$('#tblarticulos_agregados').DataTable({
		data,
		destroy: true,
		processing: true,
		columns: [{ "data": "nombre" }, { "data": "cantidad" }, { "data": "opc" },],
		language: get_idioma(),
		dom: 'Bfrtip',
		searching: false,
		buttons: [],
	});
}

const btn_eliminar = id => {
	return `<span style='color: #d9534f;margin-left: 5px;' title='Eliminar artículo' data-toggle='popover' data-trigger='hover' class='pointer fa fa-remove btn btn-default' onclick='eliminar_articulo(${id})'></span>`;
}

const agregar_articulo = ({ id, nombre }) => {
	swal({
		title: "Agregar este artículo",
		text: `Por favor ingrese la cantidad de ${nombre} que desea solicitar.`,
		type: "input",
		showCancelButton: true,
		confirmButtonColor: "#d9534f",
		confirmButtonText: "Si, Agregar!",
		cancelButtonText: "No, cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		cantidad => {
			let arts = articulos.find(art => art.id === id);
			if (arts) {
				MensajeConClase('Este artículo ya ha sido asignado.', 'info', 'Ooops!');
			} else {
				verificar_cantidad(id, cantidad, disponible => {
					if (disponible == 1) {
						if (cantidad != '' && /^([0-9])*$/.test(cantidad)) {
							articulos.push({ nombre, cantidad, opc: btn_eliminar(id), id });
							get_articulos_agregados(articulos);
							MensajeConClase('Artículo agregado', 'success', 'Proceso Exitoso!');
							$("#Buscar_Articulo").modal('hide');
						} else MensajeConClase('Por favor digite un valor numérico.', 'info', 'Proceso Exitoso!');
					} else MensajeConClase('En el momento no contamos con esta cantidad artículo.', 'info', 'Ooops!');
				});
			};
		});
}

const eliminar_articulo = art => {
	articulos.map(({ id }, key) => {
		if (id == art) {
			articulos.splice(key, 1);
			MensajeConClase('Artículo eliminado.', 'success', 'Proceso Exitoso!');
			get_articulos_agregados(articulos);
		}
	});
}

const guardar_solicitud = () => {
	const data = formDataToJson(new FormData(document.getElementById("frmAgregarSolicitud")));
	if ($('#chkelementos').prop('checked')) data.articulos = articulos;
	if (usuario_s) data.persona = usuario_s;
	consulta_ajax(`${url}guardar_solicitud`, data, ({ mensaje, titulo, tipo, id }) => {
		MensajeConClase(mensaje, tipo, titulo);
		const link = `<a href="${server}index.php/${modulo}/${id}"><b>agil.cuc.edu.co</b></a>`
		const msj = `Se informa que su solicitud fue <b>ENVIADA</b> y se encuentra en espera a ser procesada, Apartir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra su solicitud.
		<br><br>M&aacutes informaci&oacuten en :${link}`;
		listar_solicitudes();
		enviar_correo_personalizado("comp", msj, "", "", "Mantenimiento CUC", "Solicitud de Mantenimiento", "ParCodAdm", -1);
	});
}

const detalles_solicitud = async ({ id_evento_com, cat, ubicacion, descripcion, num, fecha, fullname, estado, state, categoria, id, prioridad, observacion, fecha_inicio, fecha_fin, calificacion, firma, comentario, telefono, participantes, fecha_calificacion, start_date, end_date, tiempo_habil }) => {
	if (id_evento_com != null) {
		let data_comu = await consulta_solicitud_comunicaciones_id(id_evento_com);
		fecha_inicio = data_comu.fecha_inicio_evento;
		fecha_fin = data_comu.fecha_fin_evento;
	}
	tiempo_habil == -1 ? $(".terceros").show() : $(".terceros").hide();
	$("#row_firma").hide();
	$(".valor_servicio").html(descripcion);
	$(".valor_participantes").html(participantes | 0);
	$(".valor_ubicacion").html(ubicacion);
	$(".valor_fecha").html(fecha);
	$(".valor_solicitante").html(fullname);
	$(".valor_estado").html(estado);
	$(".valor_telefono").html(telefono);
	participantes ? $(".tr_evento").show() : $(".tr_evento").hide();
	if (comentario) {
		$('.comentario').show();
		$('.valor_comentario').html(comentario);
	} else $('.comentario').hide();
	if (fecha_inicio && fecha_fin) {
		$(".evento").show();
		$(".valor_inicio").html(fecha_inicio);
		$(".valor_fin").html(fecha_fin);
	} else {
		$(".evento").hide();
		$(".valor_inicio").html(fecha_inicio);
		$(".valor_fin").html(fecha_fin);
	}
	if (calificacion) {
		$('.star_rating').show();
		starmark(calificacion);
		$(".valor_fecha_calificacion").html(fecha_calificacion);
		$(".observacion_cal").html(observacion);
	} else $('.star_rating').hide();

	$(".observacion").hide();

	$("#mensaje_programado").html(`Esta solicitud fue programada para el día ${start_date} hasta el día ${end_date}.`);
	if (start_date) $(".con_programacion").show();
	else $(".con_programacion").hide();

	// $("#row_firma").hide();
	$('.procesado').hide();
	switch (state) {
		case 'Man_Sol':
		case 'Man_Pau':
			$(".procesado").hide();
			break;
		case 'Man_Rec':
			if (observacion) {
				$(".observacion").show();
				$(".observacion>.ttitulo").html('Observación:');
				$('.valor_observacion').html(observacion);
			}
			$('.observacion').show();
			$(".observacion>.ttitulo").html('Motivo del Rechazo:');
			$('.valor_observacion').html(observacion);
			// $("#row_firma").hide();
			break;
		case 'Man_Fin':
			$('.procesado').show();
			$('.valor_categoria').html(categoria);
			$('.valor_prioridad').html(prioridad);
			// $("#row_firma").show();
			// $("#div_firma").html(firma ? `<div id="content_firmas" style="margin: 0 auto;"><img src="${server + ruta_firmas + firma}"></div>` : '<p><span class="fa fa-edit"></span>Sin Firmar.</p>');
			break;
		default:
			$('.procesado').show();
			$('.valor_categoria').html(categoria);
			$('.valor_prioridad').html(prioridad);
			$(".valor_num").html(num);
			state == "Man_Rcbd"
				? $(".cambiar_prioridad").removeClass("oculto")
				: $(".cambiar_prioridad").addClass("oculto");
			$(".valor_num").html(num);
			break;
	}
	id_solicitud = id;
	listar_articulos_solicitudes(id);
}

const listar_articulos_solicitudes = sol_id => {
	$('#tblarticulos_solicitud tbody').off('dblclick', 'tr').off('click', 'tr');
	let num = 1;
	let myTable = $("#tblarticulos_solicitud").DataTable({
		destroy: true,
		searching: false,
		ajax: {
			url: `${server}index.php/mantenimiento_control/articulos_solicitados`,
			data: { sol_id, tipo_modulo, },
			dataSrc: data => {
				if (data.length) {
					$('#tabla_articulos_solicitud').show();
					return data;
				} else {
					$('#tabla_articulos_solicitud').hide();
					return [];
				}
			},
			dataType: "json",
			type: "post",
		},
		processing: true,
		columns: [{
			render: (data, type, full, meta) => num++
		},
		{
			"data": "nombre"
		},
		{
			"data": "cantidad"
		},
		{
			render: (data, type, full, meta) =>
				`<span><span class='fa fa-toggle-off '></span></span>`
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": get_botones(),
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tblarticulos_solicitud tbody').on('click', 'tr', function () {
		let data = myTable.row(this).data();
		$("#tblarticulos_solicitud tbody tr").removeClass("warning");
		$(this).addClass("warning");
	});

	$('#tblarticulos_solicitud tbody').on('dblclick', 'tr', function () {
		let data = myTable.row(this).data();
		$("#tblarticulos_solicitud tbody tr").removeClass("warning");
		$(this).addClass("warning");
	});
}

const cancelar_solicitud = id => {
	swal({
		title: "Cancelar Solicitud",
		text: "Está seguro que desea cancelar la solicitud?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Cancelar!",
		cancelButtonText: "No!",
		allowOutsideClick: true,
		closeOnConfirm: true,
		closeOnCancel: true
	},
		isConfirm => {
			if (isConfirm) {
				const link = `${url}cancelar_solicitud`;
				consulta_ajax(link, { id }, ({ mensaje, titulo, tipo }) => {
					if (tipo == 'success') {
						swal.close();
						listar_solicitudes();
					} else MensajeConClase(mensaje, tipo, titulo);
				})
			}
		});
}

const modal_gestionar = id => {
	operarios = [];
	cargar_operarios();
	$('#gestionarSolicitud').modal();
	id_solicitud = id;
}

const denegar_solicitud = (id, email, nombre) => {
	swal({
		title: "Rechazar Solicitud",
		text: "Por favor digite la razón por la cual se rechaza la solicitud",
		type: "input",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Rechaza!",
		cancelButtonText: "Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		obs => {
			if (obs) {
				const link = `${url}rechazar_solicitud`;
				consulta_ajax(link, { id, obs }, ({ mensaje, tipo, titulo }) => {
					if (tipo == 'success') {
						swal.close();
						listar_solicitudes();
						const link = `<a href="${server}index.php/${modulo}/${id}"><b>agil.cuc.edu.co</b></a>`
						const msj = `Se informa que su solicitud ha sido <b>RECHAZADA</b>, A partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra su solicitud.
						<br><br>Motivo de rechazo: "${obs}".
						<br><br>M&aacutes informaci&oacuten en:${link}.`;
						enviar_correo_personalizado("comp", msj, email, nombre, "Mantenimiento CUC", "Solicitud de Mantenimiento", "ParCodAdm", 2);
					} else {
						MensajeConClase(mensaje, tipo, titulo);
					}
				})
			} else {
				MensajeConClase('No ha digitado una razón para denegar la solicitud.', 'info', 'Operación Incompleta');
			}

		});
}

const traer_operarios = categoria => {
	consulta_ajax(`${url}traer_operarios`, ({ categoria }), resp => {
		let data = [];
		resp.forEach(element => {
			let v = operarios.find(e => element.p_id == e.id);
			if (!v) data.push(element);
		});
		$('.cbxoperarios').html(`<option value=''>Seleccione Operario</option>`);
		data.length != 0
			? data.map(({ p_id, fullname }) => $('.cbxoperarios').append(`<option value=${p_id}>${fullname}</option>`))
			: MensajeConClase('No hay operarios disponibles en esta categoría.', 'info', 'Ooops!');
	});
}

const cargar_operarios = (operarios = []) => {
	$('#tbl_operarios tbody').off('click', 'tr');
	const myTable = $("#tbl_operarios").DataTable({
		destroy: true,
		data: operarios,
		processing: true,
		searching: false,
		columns: [{
			render: (data, type, full, meta) => `<span class='form-control' style='width: 100%'>ver</span>`
		}, { data: "name" },
		{
			render: (data, type, full, meta) => `<span class='btn btn-default' onclick='desasignar_operario(${full.id})'><span class='fa fa-close' style='color: #d9534f'></span></span>`
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tbl_operarios tbody').on('click', 'tr', function () {
		let data = myTable.row(this).data();
		$("#tbl_operarios tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});
}

const agregar_operario = () => {
	const operario = $("#cbxoperarios").val();
	if (operario == '') {

	}
	const res = operarios.find(({ id }) => id === operario);
	const name = $("#cbxoperarios option:selected").text();
	if (operario) {
		if (res) MensajeConClase('Este Operario ya ha sido asignado a este servicio.', 'info', 'Oooops!');
		else {
			operarios.push({ id: operario, name });
			MensajeConClase('Operario asignado exitosamente.', 'success', 'Proceso Exitoso');
			cargar_operarios(operarios);
			$('#cbxoperarios option:selected').remove();
		}
	} else MensajeConClase('Por favor seleccione un operario.', 'info', 'Oooops!');

}

const desasignar_operario = id => {
	swal({
		title: "Desasignar operario",
		text: "Está seguro que desea retirar a este operario de la solicitud?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Desasignar!",
		cancelButtonText: "No!",
		allowOutsideClick: true,
		closeOnConfirm: true,
		closeOnCancel: true
	},
		isConfirm => {
			if (isConfirm) {
				const index = operarios.indexOf(id);
				operarios.splice(index, 1);
				cargar_operarios(operarios);
				//MensajeConClase('Operario retirado de la solicitud.', 'success', 'Oooops!');
				swal.close();
				traer_operarios($('#cbxcategoria').val());
			}
		});
}

const gestionar_solicitud = () => {
	if (operarios.length == 0) {
		MensajeConClase('Debe asignar al menos una persona al servicio.', 'info', 'Ooops!');
		return;
	}
	let msj = '';
	const data = formDataToJson(new FormData(document.getElementById("FrmgestionarSolicitud")));
	data.operarios = operarios;
	data.solicitud = id_solicitud;
	const link = `<a href="${server}index.php/${modulo}/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`
	msj = !data.comentario
		? `<p>Se informa que su solicitud fue <b>RECIBIDA</b> y se encuentra en espera a ser ejecutada, A partir de este momento puede ingresar al aplicativo <strong>AGIL</strong> para  tener conocimiento del estado en que se encuentra su solicitud.<br><br>M&aacutes informaci&oacuten en :${link}</p>`
		: `<p>Se informa que su solicitud fue <b>RECIBIDA</b> y se encuentra en espera a ser ejecutada, A partir de este momento puede ingresar al aplicativo <strong>AGIL</strong> para  tener conocimiento del estado en que se encuentra su solicitud.</p><br>
	 	<p><strong>Nota: </strong>${data.comentario}</p><br>`;
	const {
		fecha_inicio_servicio,
		fecha_fin_servicio
	} = data;

	if (fecha_inicio_servicio && fecha_fin_servicio) {
		msj += `Esta solicitud fue programada para el día ${fecha_inicio_servicio} hasta el día ${fecha_fin_servicio}.`;
	}
	msj += `<p>M&aacutes informaci&oacuten en: ${link}</p><br>`;

	consulta_ajax(`${url}gestionar_solicitud`, data, ({ mensaje, tipo, titulo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		listar_solicitudes();
		$("#gestionarSolicitud").modal('hide');
		operarios = [];
		cargar_operarios(operarios);
		enviar_correo_personalizado("comp", msj, correo_s, nombre_s, "Mantenimiento CUC", "Solicitud de Mantenimiento", "ParCodAdm", 2);
	});
}

const ejecutar_solicitud = id => {
	consulta_ajax(`${url}ejecutar_solicitud`, { id }, ({ mensaje, tipo, titulo }) => {
		if (tipo == 'success') {
			swal.close();
			listar_solicitudes();
			const link = `<a href="${server}index.php/${modulo}/${id_solicitud}"><b>agil.cuc.edu.co</b></a>`
			const msj = `Se informa que su solicitud fue <b>EJECUTADA</b> exitosamente y se encuentra en espera a ser calificada, A partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra su solicitud.
		<br><br><strong>Nota: </strong> A partir de ahora tiene 24 horas para calificar la solicitud. En caso de que no realizar la calificaci&oacute;n en este tiempo, se tomar&aacute; cinco (5) como la puntuaci&oacute;n al servicio de esta solicitud.
		<br><br>M&aacutes informaci&oacuten en :${link}.`;
			enviar_correo_personalizado("comp", msj, correo_s, nombre_s, "Mantenimiento CUC", "Solicitud de Mantenimiento", "ParCodAdm", 2);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}

	});
}

const traer_operarios_solicitud = () => {
	const link = `${url}traer_operarios_solicitud`;
	let num = 1;
	id = id_solicitud;
	consulta_ajax(link, { id }, data => {
		if (sol_state != 'Man_Rcbd') {
			$("#td_agregar_operario").html("");
		} else {
			$("#td_agregar_operario").html(`<span onclick="modal_agregar()" class="btn btn-default red" title="Limpiar Filtros" data-toggle='popover' data-trigger='hover'> <span class="fa fa-plus" ></span> Agregar</span>`);
		};
		operarios = data;
		$('#tbl_operarios_solicitud tbody').off('dblclick', 'tr').off('click', 'tr');
		const myTable = $("#tbl_operarios_solicitud").DataTable({
			destroy: true,
			data,
			processing: true,
			searching: false,
			columns: [
				{ render: (data, type, full, meta) => num++ },
				{ data: "fullname" },
				{
					render: (data, type, full, meta) => (sol_state == 'Man_Rcbd')
						? (`<span class='btn btn-default' onclick='retirar_operario(${full.id})'><span class='fa fa-close' style='color: #d9534f'></span></span>`)
						: (`<span class='btn'><span class='fa fa-toggle-off'></span></span>`)
				},
			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_operarios_solicitud tbody').on('click', 'tr', function () {
			$("#tbl_operarios_solicitud tbody tr").removeClass("warning");
			$(this).addClass("warning");
			const data = myTable.row(this).data();
		});

		$('#tbl_operarios_solicitud tbody').on('dblclick', 'tr', function () {
			const data = myTable.row(this).data();
		});
	});
}

const retirar_operario = id => {
	swal({
		title: "Desasignar operario",
		text: "Está seguro que desea retirar a este operario de la solicitud?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Desasignar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		isConfirm => {
			if (isConfirm && ($("#tbl_operarios_solicitud tr").length - 2) > 1) {
				const link = `${url}retirar_operario`;
				consulta_ajax(link, { id, id_solicitud }, ({ mensaje, tipo, titulo }) => {
					if (tipo == 'success') {
						traer_operarios_solicitud();
						swal.close();
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			} else {
				MensajeConClase('La solicitud debe tener al menos un operario asignado.', 'info', 'Oops!');
			}
		});
}

const agregar_nuevo_operario = id => {
	const link = `${url}agregar_nuevo_operario`;
	consulta_ajax(link, { id, id_solicitud }, ({ mensaje, tipo, titulo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		traer_operarios_solicitud();
		$("#modal_agregar_operario").modal('hide');
	});
}

const cambiar_prioridad = (id, nombre) => {
	const link = `${url}cambiar_prioridad`;
	consulta_ajax(link, { prioridad: id, id_solicitud }, ({ mensaje, tipo, titulo }) => {
		$("#Modal-change-priority").modal("hide");
		MensajeConClase(mensaje, tipo, titulo);
		listar_solicitudes();
		$("#Modal-info-solicitud").modal();
		$(".valor_prioridad").html("");
		$(".valor_prioridad").html(nombre);
	});
}

const mostrar_encuesta = (id, opt = false) => {
	id_solicitud = id;
	solo_encuesta = opt;
	sw = 0;
	$("#Modal_calificar_Solicitud").modal();
	// $("#frm_solicitante").get(0).reset();
	// $("#frm_otro").get(0).reset();
	//newCanvas();
	// $("#client_password").hide();
	// $("#div_firmar").html("");
}

const starmark = stars => {
	$(".ratingstar").css('color', 'gray');
	var subid = 'one';
	for (var i = 0; i < stars; i++) {
		if (i < stars) {
			$(`#${i + 1}${subid}`).css('color', 'orange');
		} else {
			$(`#${i + 1}${subid}`).css('color', 'gray');
		}
	}
	$(".ratingstar").blur();
}

const ejecutar = id => {
	swal({
		title: "¿Solicitud Ejecutada?",
		text: `¿Está seguro que desea cambiar el estado de la solicitud a Ejecutado?`,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#d9534f",
		confirmButtonText: "Si, Ejecutar!",
		cancelButtonText: "No, Regresar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		isConfirm => {
			if (isConfirm) {
				ejecutar_solicitud(id);
			}
		});
}

const modal_agregar = () => {
	$("#modal_agregar_operario").modal();
	traer_operarios(categoria_s);
}

const openCity = (evt, option) => {
	let i, tabcontent, tablinks;
	tabcontent = document.getElementsByClassName("tabcontent");
	for (i = 0; i < tabcontent.length; i++) {
		tabcontent[i].style.display = "none";
	}
	tablinks = document.getElementsByClassName("tablinks");
	for (i = 0; i < tablinks.length; i++) {
		tablinks[i].className = tablinks[i].className.replace(" active", "");
	}
	document.getElementById(option).style.display = "block";
	evt.currentTarget.className += " active";
}

// const verificarPassword = (user = "", password) => {
// 	if (!password) {
// 		MensajeConClase("Por favor digite su contraseña.", "info", "");
// 		return false;
// 	}
// 	if (user != "") {
// 		user = usuario_s;
// 		$("#rate_observation").prop('required', false);
// 	}
// 	$.ajax({
// 		url: `${server}index.php/pages/verificar_password`,
// 		type: "post",
// 		dataType: "json",
// 		data: { user, password },
// 	}).done(resp => {
// 		let { datos, existe } = resp;
// 		if (datos.length == 0) {
// 			MensajeConClase("El usuario no se encuentra registrado en AGIL, contacte con el administrador.", "info", "");
// 			return;
// 		}
// 		switch (existe) {
// 			case 2:
// 				$("#rate1").prop('checked', true);
// 				usuario = datos[0].id;
// 				$("#panel_firma").hide();
// 				if (solo_encuesta) {
// 					$("#div_calificacion").show();
// 					sw = -1;
// 				} else {
// 					if (sw || user === $("#txt_usuario").val()) {
// 						$("#div_calificacion").show();
// 						sw = 1;
// 					} else {
// 						$("#div_calificacion").hide();
// 						sw = 0;
// 						const data = { id: id_solicitud, sw: 0 };
// 						calificar_solicitud(data);
// 						$("#Modal_calificar_Solicitud").modal('hide');
// 					}
// 				}
// 				$("#client_password").hide('fast');
// 				// $("#div_firmar").html(`<p><span class="fa fa-edit"></span>Firma Receptor</p>
// 				// <div id="content_firmas"><p style="text-align:center;">Loading Canvas...</p></div>
// 				// <div class=" margin1">
// 				// 	<!--<span class="btn btn-default active" onclick="newCanvas()"> <span class="fa fa-refresh"></span> Limpiar</span>-->
// 				// </div>`);
// 				// newCanvas();
// 				$("#btn_calificar").show();
// 				break;
// 			case 1:
// 				MensajeConClase("La contraseña es incorrecta.  Por favor intente de nuevo.", "info", "");
// 				break;
// 			default:
// 				break;
// 		}
// 	});
// }

const calificar_solicitud = data => {
	consulta_ajax(`${url}calificar`, data, ({ mensaje, titulo, tipo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		listar_solicitudes();
		$("#Modal_calificar_Solicitud").modal('hide');
	});
}

const get_categorias = () => {
	consulta_ajax(`${url}get_categorias`, {}, data => {
		let num = 1;
		$('#tbl_categorias tbody').off('dblclick', 'tr').off('click', 'tr');
		const myTable = $("#tbl_categorias").DataTable({
			destroy: true,
			data,
			processing: true,
			searching: false,
			columns: [
				{ render: (data, type, full, meta) => num++ },
				{ data: "nombre" },
				{
					render: (data, type, full, meta) => `<span class="btn btn-default red opciones"><span class="fa fa-cog"></span></span>`
				},
			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_categorias tbody').on('click', 'tr', function () {
			$("#tbl_categorias tbody tr").removeClass("warning");
			$(this).addClass("warning");
		});

		$('#tbl_categorias tbody').on('click', 'tr td:nth-last-child(1) span.opciones', function () {
			const { id_aux } = myTable.row($(this).parent()).data();
			mostrar_operarios(id_aux);
		});
	});
}

const cargar_evidencias_mantenimiento = () => {
	consulta_ajax(`${url}get_categorias`, {}, data => {
		let num = 1;
		$('#tbl_crear_mantenimiento tbody').off('dblclick', 'tr').off('click', 'tr');
		const myTable = $("#tbl_categorias").DataTable({
			destroy: true,
			data,
			processing: true,
			searching: false,
			columns: [
				{ render: (data, type, full, meta) => num++ },
				{ data: "nombre" },
				{
					render: (data, type, full, meta) => `<span class="btn btn-default red opciones"><span class="fa fa-cog"></span></span>`
				},
			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_crear_mantenimiento tbody').on('click', 'tr', function () {
			$("#tbl_categorias tbody tr").removeClass("warning");
			$(this).addClass("warning");
		});

		$('#tbl_crear_mantenimiento tbody').on('click', 'tr td:nth-last-child(1) span.opciones', function () {
			const { id } = myTable.row($(this).parent()).data();
		});
	});
}

const mostrar_operarios = categoria => {
	categoria_s = categoria;
	$("#Modal_operarios_categoria").modal();
	get_operarios_categoria();
}

const get_operarios = () => {
	consulta_ajax(`${url}get_operarios`, { categoria_s }, data => {
		let num = 1;
		$('#tbloperarios tbody').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-last-child(1) span.option');
		const myTable = $("#tbloperarios").DataTable({
			destroy: true,
			data,
			processing: true,
			columns: [
				{ render: (data, type, full, meta) => num++ },
				{ data: "fullname" },
				{
					render: (data, type, full, meta) => `<span class="btn btn-default red option"><span class="fa fa-user-plus"></span></span>`
				},
			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbloperarios tbody').on('click', 'tr', function () {
			$(`${this} tbody tr`).removeClass("warning");
			$(this).addClass("warning");
		});

		$('#tbloperarios tbody').on('click', 'tr td:nth-last-child(1) span.option', function () {
			const { id } = myTable.row($(this).parent()).data();
			add_operario_categoria(id);
			get_operarios();
		});
	});
}

const get_operarios_categoria = () => {
	consulta_ajax(`${url}get_operarios_categoria`, { categoria_s }, data => {
		let num = 1;
		$('#tbloperarios_categoria tbody').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-last-child(1) span.option');
		const myTable = $("#tbloperarios_categoria").DataTable({
			destroy: true,
			data,
			processing: true,
			columns: [
				{ render: (data, type, full, meta) => num++ },
				{ data: "fullname" },
				{
					render: () => `<span class="btn btn-default red option"><span class="fa fa-user-times"></span></span>`
				},
			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbloperarios_categoria tbody').on('click', 'tr', function () {
			$(`${this} tbody tr`).removeClass("warning");
			$(this).addClass("warning");
		});

		$('#tbloperarios_categoria tbody').on('click', 'tr td:nth-last-child(1) span.option', function () {
			const { id } = myTable.row($(this).parent()).data();
			quitar_operario(id);
		});
	});
}

const quitar_operario = id => {
	consulta_ajax(`${url}quitar_operario`, { id, categoria_s }, ({ mensaje, tipo, titulo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		get_operarios_categoria();
	});
}

const add_operario_categoria = id => {
	consulta_ajax(`${url}add_operario_categoria`, { id, categoria_s }, ({ mensaje, tipo, titulo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		get_operarios_categoria();
		get_operarios();
	});
}

const verificar_cantidad = (id, cantidad, callback) => consulta_ajax(`${url}verificar_cantidad`, { id, cantidad }, resp => callback(resp));

const traer_historial_solicitud = () => {
	let num = 1;
	consulta_ajax(`${url}traer_historial_solicitud`, { id: id_solicitud }, data => {
		$('#tbl_historial_solicitud tbody').off('dblclick', 'tr').off('click', 'tr');
		const myTable = $("#tbl_historial_solicitud").DataTable({
			destroy: true,
			data,
			processing: true,
			searching: false,
			columns: [
				{ render: (data, type, full, meta) => num++ },
				{ data: "estado" },
				{ data: 'fecha' },
				{ data: 'fullname' },
			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_historial_solicitud tbody').on('click', 'tr', function () {
			$("#tbl_historial_solicitud tbody tr").removeClass("warning");
			$(this).addClass("warning");
			const data = myTable.row(this).data();
		});

		$('#tbl_historial_solicitud tbody').on('dblclick', 'tr', function () {
			const data = myTable.row(this).data();
		});
	});
}

const pausar_solicitud = (id, email, nombre) => {
	swal({
		title: "Pausar Solicitud",
		text: "Por favor digite la razón por la cual se pausará la solicitud",
		type: "input",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Pausar!",
		cancelButtonText: "Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		obs => {
			if (obs) {
				consulta_ajax(`${url}pausar_solicitud`, { id, obs }, ({ mensaje, tipo, titulo }) => {
					if (tipo = 'success') {
						swal.close();
						listar_solicitudes();
						const link = `<a href="${server}index.php/${modulo}/${id}"><b>agil.cuc.edu.co</b></a>`
						const msj = `Se informa que su solicitud ha sido <b>PAUSADA</b>, A partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra su solicitud.
						<br><br><strong>Motivo de pausado:<strong> "${obs}".
						<br><br>M&aacutes informaci&oacuten en :${link}.`;
						enviar_correo_personalizado("comp", msj, email, nombre, "Mantenimiento CUC", "Solicitud de Mantenimiento", "ParCodAdm", 2);
					} else MensajeConClase(mensaje, tipo, titulo);
				})
			} else MensajeConClase('No ha digitado una razón para pausar la solicitud.', 'info', 'Operación Incompleta');
		});
}

(function get_modulo_actual() {
	let route = window.location.href;
	const pos = route.indexOf("index.php/");
	route = route.slice(pos + 10, route.length);
	const ruta = route.replace(/[0-9]+/g, '');
	modulo = ruta;
})()

const cargar_personas = (opt = '') => {
	consulta_ajax(`${url}cargar_personas`, { opt }, data => {
		$('#tblpersonas tbody').off('click', 'tr').off('click', 'tr span .asignar').off('dblclick', 'tr').off('click', 'tr span.asignar');
		const myTable = $("#tblpersonas").DataTable({
			destroy: true,
			data,
			processing: true,
			searching: false,
			columns: [
				{ data: "identificacion" },
				{ data: 'fullname' },
				{ render: () => "<span class='fa fa-user-plus red btn btn-default asignar'></span>" },
			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tblpersonas tbody').on('click', 'tr', function () {
			$("#tblpersonas tbody tr").removeClass("warning");
			$(this).addClass("warning");
		});

		$('#tblpersonas tbody').on('click', 'tr span.asignar', function () {
			$("#tblpersonas tbody tr").removeClass("warning");
			$(this).addClass("warning");
			const { id, fullname } = myTable.row($(this).parent()).data();
			usuario_s = id;
			$('#persona').html(fullname);
			MensajeConClase('Persona asignada a la solicitud exitosamente', 'success', 'Proceso Exitoso!');
			$("#modalPersonas").modal('hide');
		});

		$('#tblpersonas tbody').on('dblclick', 'tr', function () {
			$("#tblpersonas tbody tr").removeClass("warning");
			$(this).addClass("warning");
			const { id, fullname } = myTable.row(this).data();
			usuario_s = id;
			$('#persona').html(fullname);
			MensajeConClase('Persona asignada a la solicitud exitosamente', 'success', 'Proceso Exitoso!');
			$("#modalPersonas").modal('hide');
		});
	})
}

const obtener_permisos_parametros = (id_principal) => {
	return new Promise((resolve) => {
		let ruta = `${url}listar_permisos_parametros`;
		consulta_ajax(ruta, { id_principal }, (resp) => {
			resolve(resp);
		});
	});
};


const listar_ubicaciones = async (id_lugar, container) => {
	let ubicaciones = await obtener_permisos_parametros(id_lugar);
	pintar_datos_combo(ubicaciones, container, "Seleccione Ubicacion");
};

const pintar_datos_combo = (datos, combo, mensaje, sele = "") => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach((elemento) => {
		$(combo).append(
			`<option value='${elemento.id}'> ${elemento.nombre}</option>`
		);
	});
	$(combo).val(sele);
};

const guardar_sol_mantenimiento = (form) => {
	const data = formDataToJson(new FormData(document.getElementById(form)));
	data.tipo_mantenimiento = tipo_mantenimiento;
	data.objeto = objetos;
	data.tipo_de_registro = tipo_de_registro;
	consulta_ajax(`${url}guardar_sol_mantenimiento`, data, ({ mensaje, tipo, titulo }) => {
		if (tipo == 'success') {
			MensajeConClase(mensaje, tipo, titulo);
			$("#Modal_Add_Mtto, modal_inspeccion_preventiva").modal('hide');
			$("#modal_detalles_mantemiento").modal('hide');
			$("#form_agregar_mantenimiento, #form_inspeccion_preventivo").get(0).reset();
			get_solicitud_matenimiento();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}

	});
}

const guardar_estado_objetos_mantenimiento = (form) => {
	const data = formDataToJson(new FormData(document.getElementById(form)));
	data.objeto = objetos;
	consulta_ajax(`${url}guardar_estado_objetos_mantenimiento`, {data, id_objeto, id_lugar, id_ubicacion, id_estado_mtto, id_historial_mtto}, ({ mensaje, tipo, titulo }) => {
		if (tipo == 'success') {
			MensajeConClase(mensaje, tipo, titulo);
			listar_objetos_mant_gest(id_ubicacion,id_lugar,id_historial_mtto, 'iniciar_mantenimiento');
			$("#form_estado_objetos_mantenimiento").get(0).reset();
			$("#modal_objetos_estados_gest_mant").modal('hide');
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

// MANTENIMIENTO ANUAL - PERIODICO

const get_solicitud_matenimiento = (tipo_mantenimiento) => {
	let num =1;
	$('#tbl_crear_mantenimiento tbody')
	.off("click", "tr").off('dblclick', 'tr').off('click', 'tr').off('click', 'tr  span.ver').off('click', 'tr  span.agregar_lugar').off('click', 'tr td .iniciar_mantenimiento').off('click', 'tr td .gestionar_mantenimiento');
	consulta_ajax(`${url}get_solicitud_matenimiento`, { tipo_mantenimiento }, (resp) => {
		const myTable = $('#tbl_crear_mantenimiento').DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
            columns: [
				{
					render: function (data) {
						return `<span class='form-control ver' style='width: 100%'>ver</span>`
					}
				},
				{ render: () => num++ },
				{ data: 'nombre_mantenimiento', },
				{ data: 'cantidad_mantenimiento', },
				{ data: "periodicidad", },
				{ data: 'estado_mantenimiento', },
				{ data: 'accion', },
				
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_crear_mantenimiento tbody').on("click", "tr", function () {
			$('#tbl_crear_mantenimiento tbody tr').removeClass("warning");
			$(this).attr("class", "warning");
		});


		$('#tbl_crear_mantenimiento tbody').on('click', 'tr  span.ver', function () {
			let datos = myTable.row($(this).parent().parent()).data();
			get_detalle_solicitud_mantenimiento(datos)
			datos_detalle_edit = datos;
			$('#tbl_crear_mantenimiento tbody').removeClass("warning");
			const {id} =  myTable.row($(this).parent()).data();
			id_mantenimiento = id;
			listar_detalles_solictud_mantenimiento_periodico(id_mantenimiento);
			$("#modal_detalles_mantemiento").modal("show");

		});

		$('#tbl_crear_mantenimiento tbody').on('click', 'tr td:nth-last-child(1) span.opciones', function () {
			$('#tbl_crear_mantenimiento tbody').removeClass("warning");
			const {id} =  myTable.row($(this).parent()).data();
			id_mantenimiento = id;
			$("#modal_enviar_archivos").modal("show");
		});

		$('#tbl_crear_mantenimiento tbody').on('click', 'tr  span.agregar_lugar', function () {
			$('#tbl_crear_mantenimiento tbody').removeClass("warning");
			const data =  myTable.row($(this).parent()).data();
			id_mantenimiento_periodico = data.id;
			listar_lugares_mantenimientos_periodico('agregar');
			$("#modal_lugares_mantenimiento_periodico").modal("show");
		});

		$("#btn_agregar_lugar_mantenimiento_periodico").click(() => {
			$("#txt_lugar").val('');
			buscar_lugar_mantenimiento_periodico();
			$("#modal_buscar_lugar_mantenimiento_periodico").modal("show");

		})

		$('#tbl_crear_mantenimiento tbody').on('click', 'tr td .iniciar_mantenimiento', async function() {
			let {id, id_estado_matto } = myTable.row($(this).parent().parent()).data();
			// id_mantenimiento_periodico_matto = id;
			id_mantenimiento_periodico = id;
			id_estado_mtto = id_estado_matto;

			let validar = await validar_existencia_mantenimiento(id_mantenimiento_periodico, id);
			if(validar.length === 0){
				msj_confirmacion('¿ Estas Seguro ?', `Desea crear un nuevo mantenimiento ?. Tener en cuenta que no podra revertir esta acción.!`, () => generar_mantenimiento(id, 'periodico'));
			}else{
				listar_lugares_mantenimiento_periodicos(id_ubicacion, id_estado_mtto, 'iniciar_mantenimiento');
			}
        });

		$('#tbl_crear_mantenimiento tbody').on('click', 'tr td .gestionar_mantenimiento', async function() {
			let {id, id_estado_matto } = myTable.row($(this).parent().parent()).data();
			id_mantenimiento_periodico = id;
			id_estado_mtto = id_estado_matto;

			// let validar = await validar_existencia_mantenimiento(id_mantenimiento_periodico, id);
			generar_mantenimiento(id, 'periodico');
        });

	});
};


const buscar_lugar_mantenimiento_periodico = (dato) => {
	let num = 0;
	consulta_ajax(`${url}buscar_lugar_mantenimiento_periodico`, {dato}, (resp) => {
		$(`#tbl_lugares_buscado tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .seleccionar');

        const myTable = $('#tbl_lugares_buscado').DataTable({
            destroy: true,
            searching: false,
            processing: true,
            data: resp,
            columns: [
				{render: () => ++num},
                { data: 'valor' },
				{ defaultContent: '<span style="color: #39B23B" class="btn btn-default red seleccionar" title="seleccionar"><span class="fa fa-check"></span></span>' },
			],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: []
        });

		$('#tbl_lugares_buscado tbody').on('click', 'tr', function () {
			$('#tbl_lugares_buscado tbody tr').removeClass("warning");
			$(this).attr("class", "warning");
        });

		$('#tbl_lugares_buscado tbody').on('dblclick', 'tr', function () {
            let data = myTable.row($(this).parent().parent()).data();
            callbak(data);
        });

		$('#tbl_lugares_buscado tbody').on('click', 'tr td .seleccionar', function () {
			let data = myTable.row($(this).parent().parent()).data();
			guardar_lugar_mantenimiento(data.id, id_mantenimiento_periodico);
        });
	});
}

const guardar_lugar_mantenimiento = (id_lugar, id_mantenimiento_periodico) =>  {
	consulta_ajax(`${url}guardar_lugar_mantenimiento`, {id_lugar, id_mantenimiento_periodico}, ({ mensaje, titulo, tipo, }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo == 'success') {
			listar_lugares_mantenimientos_periodico('iniciar');
			MensajeConClase(mensaje, tipo, titulo);
			$("#modal_buscar_lugar_mantenimiento_periodico").modal('hide');
			get_solicitud_matenimiento('T_Matt_anual');
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
		// if (id_historial_mtto) listar_objetos_mant_gest(id_ubicacion, id_lugar, id_historial_mtto, 'iniciar_mantenimiento');
	});
}

const listar_lugares_mantenimientos_periodico = (tipo) => {
	let num = 0;
	$('#tbl_lugares_mantenimaiento_periodico tbody').off("click", "tr").off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td span.evidencia').off('click', 'tr td .estado').off('click', 'tr span.eliminar');
	consulta_ajax(`${url}listar_lugares_mantenimientos_periodico`, {id_mantenimiento_periodico, tipo}, (resp) => {
		const myTable = $('#tbl_lugares_mantenimaiento_periodico').DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
            columns: [
				{render: () => ++num},
                { data: 'lugar' },
				{ data: 'tipo_mtto' },
				{ data: 'accion' },
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_lugares_mantenimaiento_periodico tbody').on("click", "tr", function () {
			$('#tbl_lugares_mantenimaiento_periodico tbody tr').removeClass("warning");
			$(this).attr("class", "warning");
		});


		$('#tbl_lugares_mantenimaiento_periodico tbody').on('click', 'tr td .estado', function () {
			let data = myTable.row($(this).parent().parent()).data();
			id_mantenimiento_periodico_matto = data.id;
			$("#modal_lugares_estados_matto").modal("show");
        });

		$('#tbl_lugares_mantenimaiento_periodico tbody').on('click', 'tr td span.evidencia', function () {
			let data = myTable.row($(this).parent().parent()).data();
			id_registro_matto_lugar = data.id;
			pintar_evidencias_mantenimiento_periodico();
			$("#id_solicitud").val(data.id);
			$("#modal_evidencias_mantenimiento_periodico").modal("show");
        });

		$('#tbl_lugares_mantenimaiento_periodico tbody').on('click', 'tr span.eliminar', function () {
			let datos = myTable.row($(this).parent().parent()).data();
			eliminar_datos({ id: datos.id, title: "Eliminar datos?", tabla_bd: 'mantenimiento_periodicos_lugares' }, () => {
                listar_objetos_inspeccion_mantenimiento(id_mantenimiento_periodico);
				listar_lugares_mantenimientos_periodico('iniciar');
				get_solicitud_matenimiento();

            });

			// if(resp[1]) $("#btn_finalizar_mantenimeinto1").removeClass('oculto');
			// else  $("#btn_finalizar_mantenimeinto1").addClass('oculto');
		});


	});

	$('#btn_finalizar_mantenimeinto1').click( async () => {
		// let { id } = await validar_existencia_mantenimiento_periodico(id_mantenimiento_periodico_matto);
		msj_confirmacion('¿ Estas Seguro ?', `Desea finalizar el mantenimiento ?. Tener en cuenta que no podra revertir esta acción.!`, () => finalizar_solicitud_mantenimiento_periodico(id_mantenimiento_periodico));
		return false;
	});
};


const finalizar_solicitud_mantenimiento_periodico =  (id_mantenimiento_periodico) => {
	consulta_ajax(`${url}finalizar_solicitud_mantenimiento_periodico`, {id_mantenimiento_periodico},  (resp) => {
		let { mensaje,tipo,titulo } = resp;
		if (tipo == 'success') {
			listar_lugares_mantenimientos_periodico();
			get_solicitud_matenimiento('T_Matt_anual');
			$("#modal_lugares_mantenimiento_periodico").modal("hide");
		}
		MensajeConClase(mensaje, tipo, titulo);
		
	});
	// swal.close();
}


const guardar_estado_lugares_mantenimientos = (estado) => {
	console.log(id_mantenimiento_periodico, id_mantenimiento_periodico_matto);
	consulta_ajax(`${url}guardar_estado_lugares_mantenimientos`, {estado, id_mantenimiento_periodico, id_mantenimiento_periodico_matto }, ({ mensaje, tipo, titulo }) => {
		if (tipo == 'success') {
			MensajeConClase(mensaje, tipo, titulo);
			listar_lugares_mantenimientos_periodico("iniciar");
			get_solicitud_matenimiento("T_Matt_anual");
			// listar_lugares_mantenimientos_periodico(estado, id_mantenimiento_periodico, id_mantenimiento_periodico_matto);
			$("#form_estado_lugares_mantenimiento").get(0).reset();
			$("#modal_lugares_estados_matto").modal('hide');
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const pintar_evidencias_mantenimiento_periodico = () => {
	$("#content_evidencias_mantenimiento_periodico").html("");
	$("#content_evidencias_mantenimiento_periodico").css("display", "block");

	$("#content_evidencias_mantenimiento_periodico").append(`
		<span class="btn btn-default" id="btn_add_evidence">
			<span class="fa fa-plus-circle" id="add_evidence"></span>
			Agregar Evidencia
		</span>
	`);

	let i = 0;
	$(`#btn_add_evidence`).off("click");
	$(`#btn_add_evidence`).click(async function () {
		
		activarfile();
		$("#content_evidencias_mantenimiento_periodico").append(``);
		$("#content_evidencias_mantenimiento_periodico").append(`
		<div class="evidencias_unique">
			<input type="text" class="form-control rounded" name='Comentario_Evidencia_${i}' placeholder='Comentario'></input> 
			<div id="campo_evidencia_mantenimiento_periodico_${i}" class="input-group agrupado"> 
			<label class="input-group-btn">
				<span class="btn btn-primary">
					<span class="fa fa-folder-open"></span> 
					Buscar <input name="adj_evidencia_mtto_${i}" id="adj_evidencia_mtto_${i}" type="file" style="display: none;">
				</span>
			</label><input type="text" id="evidencia_mtto_${i}" class="form-control" readonly placeholder='Adjuntar' required></div> 
		</div>
		`);
		$("#cantidad_evidencia").val(i+1);
		activarfile();
		i += 1;
	});

};



// VALIDAR EXISTENCIA MANTENIMIENTO PERIODICO

const validar_existencia_mantenimiento_periodico = async (id_lugar, id_ubicacion) => {
	return new Promise(resolve => {
		let ruta = `${url}validar_existencia_mantenimiento_periodico`;
		consulta_ajax(ruta, { id_lugar, id_ubicacion }, resp => {
			resolve(resp);
		});
	});
}

const listar_detalle_mantenimiento = (id, id_periodico) =>{
	consulta_ajax(`${url}listar_detalle_mantenimiento`, {id, id_periodico}, (resp) => {
		// console.log(resp);
		$(`#tbl_detalle_gest_mant tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .ver_evidencia').off('click', 'tr td .ver');
        const myTable = $('#tbl_detalle_gest_mant').DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data: resp,
            columns: [
				{ defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control ver"><span >ver</span></span>' },
                { data: 'nombre' },
				{ data: 'fecha_registra' },
			],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: [],
        });

        $('#tbl_detalle_gest_mant tbody').on('click', 'tr', function () {
            $('#tbl_detalle_gest_mant tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });


		$('#tbl_detalle_gest_mant tbody').on('click', 'tr td .ver', function () {
			let data = myTable.row($(this).parent().parent()).data();
			listar_objetos_mant_gest(data.id_ubicacion, data.id_lugar, data.id);			
        });
	});
}


// LISTAR DETALLES SOLICITUD MANTENIMIENTO PERIODICO

const listar_detalles_solictud_mantenimiento_periodico = (id_mantenimiento_periodico) => {
	consulta_ajax(`${url}listar_detalles_solictud_mantenimiento_periodico`, {id_mantenimiento_periodico} , resp => {
        $(`#tbl_detalle_matenimiento_periodico tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .ver_evidencia').off('click', 'tr td .ver');
        const myTable = $('#tbl_detalle_matenimiento_periodico').DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data: resp,
            columns: [
				{ defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control ver"><span >ver</span></span>' },
                { data: 'nombre' },
				{ data: 'fecha_inicio' },
				{ data: 'fecha_fin' },
				
			],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: [],
        });

        $('#tbl_detalle_matenimiento_periodico tbody').on('click', 'tr', function () {
            $('#tbl_detalle_matenimiento_periodico tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });


		$('#tbl_detalle_matenimiento_periodico tbody').on('click', 'tr td .ver', function () {
			$('#tbl_detalle_matenimiento_periodico tbody').removeClass("warning");
			const data =  myTable.row($(this).parent()).data();
			listar_lugares_detalles_mantenimiento_periodico(data.id);
			$("#modal_lugares_mantenimiento_periodico_detalles_evidencia").modal("show");		
        });

    });
}

// LISTAR EVIDENCIAS MANTENIMIENTOS PERIODICOS

const listar_lugares_detalles_mantenimiento_periodico = (id) => {
	$('#tbl_lugares_mantenmiento_periodicos_detalles_evidencias tbody').off("click", "tr").off('dblclick', 'tr').off('click', 'tr').off('click', 'tr span.ver');
	consulta_ajax(`${url}listar_lugares_detalles_mantenimiento_periodico`, { id_mantenimiento, id }, (resp) => {
		const myTable = $('#tbl_lugares_mantenmiento_periodicos_detalles_evidencias').DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
            columns: [
				{ defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control ver"><span >ver</span></span>' },
                { data: 'lugar' },
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_lugares_mantenmiento_periodicos_detalles_evidencias tbody').on("click", "tr", function () {
			$('#tbl_lugares_mantenmiento_periodicos_detalles_evidencias tbody tr').removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tbl_lugares_mantenmiento_periodicos_detalles_evidencias tbody').on('click', 'tr span.ver', function () {
			let data = myTable.row($(this).parent().parent()).data();
			evidencias_mantenimiento_periodico(data.id);
			$("#modal_evidencia_mantenimiento_periodico").modal("show");	
		});

	});
};


// LISTA EVIDENCIA MANTENIMIENTOS PERIODICOS

const evidencias_mantenimiento_periodico = (id) => {
    consulta_ajax(`${url}evidencias_mantenimiento_periodico`, {id}, resp => {
        $(`#tbl_evidencia_mantenimiento_periodico tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .evidencia');
        const myTable = $('#tbl_evidencia_mantenimiento_periodico').DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data: resp,
            columns: [
				{ data: 'nombre' },
				{ data: 'comentario' },
                { data: 'fecha_registra' },
				{ defaultContent: '<a target="_blank" class="evidencia"><span style="color: #337ab7" class="btn btn-default red" title="Evidencia"><span class="fa fa-eye"></span></span></a>' },
			],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: []
        });


        $('#tbl_evidencia_mantenimiento_periodico tbody').on('click', 'tr', function () {
            $('#tbl_evidencia_mantenimiento_periodico tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });

		$('#tbl_evidencia_mantenimiento_periodico tbody').on('click', 'tr td .evidencia', function () {
			let data = myTable.row($(this).parent().parent()).data();
			$(".evidencia").attr("href", `${Traer_Server()}archivos_adjuntos/mantenimiento/evidencias/${data.nombre}`);
        });

    });
	
}


// MANTENIMIENTO PREVENTIVO

const get_solicitud_inspeccion_preventiva = (tipo_mantenimiento) => {
	$('#tbl_crear_mantenimiento_preventivo tbody').off("click", "tr").off('dblclick', 'tr').off('click', 'tr').off('click', 'tr span.ver');
	consulta_ajax(`${url}listar_lugares_mantenimiento`, { tipo_mantenimiento }, (resp) => {
		const myTable = $('#tbl_crear_mantenimiento_preventivo').DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
            columns: [
				{ defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control ver"><span >ver</span></span>' },
                { data: 'lugar' },
                { data: 'cantidad_ubicaciones' },
				
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_crear_mantenimiento_preventivo tbody').on("click", "tr", function () {
			$('#tbl_crear_mantenimiento_preventivo tbody tr').removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tbl_crear_mantenimiento_preventivo tbody').on('click', 'tr span.ver', function () {
			let datos = myTable.row($(this).parent().parent()).data();
			id_lugar = datos.id;
			listar_ubiaciones_mantenimiento(datos.id)
			$("#modal_ubiaciones_mantenimiento").modal("show");
		});

	});
};


const listar_ubiaciones_mantenimiento = (id_lugar) => {
	$('#tbl_ubiaciones_mantenimaiento tbody').off("click", "tr").off('dblclick', 'tr').off('click', 'tr').off('click', 'tr span.ver');
	consulta_ajax(`${url}listar_ubiaciones_mantenimiento`, { id_lugar }, (resp) => {
		const myTable = $('#tbl_ubiaciones_mantenimaiento').DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
            columns: [
				{ defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control ver"><span >ver</span></span>' },
                { data: 'ubicacion' },
                { 
					render: (data, type, { cantidad_objetos }, meta) =>{
						if(!cantidad_objetos) return 0;
						else return cantidad_objetos;
					}
				 },
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_ubiaciones_mantenimaiento tbody').on("click", "tr", function () {
			$('#tbl_ubiaciones_mantenimaiento tbody tr').removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tbl_ubiaciones_mantenimaiento tbody').on('click', 'tr span.ver', function () {
			let datos = myTable.row($(this).parent().parent()).data();
			id_ubicacion = datos.id;
			listar_objetos_inspeccion_mantenimiento(datos.id)
			$("#modal_objetos_mantenimiento").modal("show");
		});

	});

	$("#btn_agregar_objeto_mantenimiento").click(() => {
		$("#txt_objeto_buscado").val('');
		buscar_objetos_inspeccion_mantenimiento();
		$("#modal_buscar_objetos_mantenimiento").modal();

	})


};

const  guardar_objeto_mantenimiento = (id_objeto, id_lugar, id_ubicacion) => {

	consulta_ajax(`${url}guardar_objeto_mantenimiento`, {id_objeto, id_lugar, id_ubicacion}, ({ mensaje, titulo, tipo, id }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo == 'success') {
			MensajeConClase(mensaje, tipo, titulo);
			$("#modal_buscar_objetos_mantenimiento").modal('hide');
			listar_ubiaciones_mantenimiento(id_lugar);
			listar_objetos_inspeccion_mantenimiento(id_ubicacion);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
		if (id_historial_mtto) listar_objetos_mant_gest(id_ubicacion, id_lugar, id_historial_mtto, 'iniciar_mantenimiento');
	});
}

const listar_objetos_inspeccion_mantenimiento = (id_ubicacion) => {
	$('#tbl_objetos_mantenimaiento tbody').off("click", "tr").off('dblclick', 'tr').off('click', 'tr').off('click', 'tr span.eliminar');
	consulta_ajax(`${url}listar_objetos_matto`, { id_ubicacion }, (resp) => {
		const myTable = $('#tbl_objetos_mantenimaiento').DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
            columns: [
                { data: 'objeto' },
                // { data: 'cantidad_objetos' },
				{ defaultContent: '<span title="Eliminar" data-toggle="popover" data-trigger="hover" style="color:#d9534f;" class="fa fa-trash pointer btn btn-default eliminar"></span>' },
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_objetos_mantenimaiento tbody').on("click", "tr", function () {
			$('#tbl_objetos_mantenimaiento tbody tr').removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tbl_objetos_mantenimaiento tbody').on('click', 'tr span.eliminar', function () {
			let datos = myTable.row($(this).parent().parent()).data();
			eliminar_datos({ id: datos.id, title: "Eliminar datos?", tabla_bd: 'objetos_mantenimiento' }, () => {
                listar_objetos_inspeccion_mantenimiento(id_ubicacion);
				listar_ubiaciones_mantenimiento(datos.id_lugar);

            });
		});

	});
};

const get_detalle_solicitud_mantenimiento = (datos) => {
	let {nombre_mantenimiento, periodicidad, numero_notificaciones, mes_inicio_notificacion, dia_entre_notificacion, observacion_mantenimiento, fecha_inicio, fecha_fin } = datos;
	$("#tbl_detalles_mantenimiento .nombre_mantenimiento").html(nombre_mantenimiento);
	$("#tbl_detalles_mantenimiento .periodicidad").html(periodicidad);
	$("#tbl_detalles_mantenimiento .numero_notificaciones").html(numero_notificaciones);
	$("#tbl_detalles_mantenimiento .mes_inicio_notificacion").html(mes_inicio_notificacion);
	$("#tbl_detalles_mantenimiento .dia_entre_notificacion").html(dia_entre_notificacion);
	$("#tbl_detalles_mantenimiento .observaciones").html(observacion_mantenimiento);
	$("#tbl_detalles_mantenimiento .fecha_inicio").html(fecha_inicio);
	$("#tbl_detalles_mantenimiento .fecha_fin").html(fecha_fin);

}


const listar_objetos_mantenimiento = (id, callback) => {
    consulta_ajax(`${url}listar_objetos_mantenimiento`, {id }, resp => {
        $(`#tbl_obejtos_mantenimiento tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .seleccionar');
        const myTable = $('#tbl_obejtos_mantenimiento').DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data: resp,
            columns: [
                { data: 'valor' },
                { defaultContent: '<span style="color: #39B23B;" title="Seleccionar Objeto" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar"' },
				
            ],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: []
        });

        $('#tbl_obejtos_mantenimiento tbody').on('click', 'tr', function () {
            $('#tbl_obejtos_mantenimiento tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });

        $('#tbl_obejtos_mantenimiento tbody').on('click', 'tr td .seleccionar', function () {
			let data = myTable.row($(this).parent().parent()).data();
			callback(data);
        });
    });
}

// MANTENIMIENTO PPREVENTIVOS

const listar_mantenimiento_gestion = () => {
	consulta_ajax(`${url}listar_lugares_mantenimiento`, {} , resp => {
        $(`#table_mant_gest tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .ver');
        const myTable = $('#table_mant_gest').DataTable({
            destroy: true,
            processing: true,
            searching: true,
            data: resp,
            columns: [
                { defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control ver"><span >ver</span></span>' },
                { data: 'lugar' },
                { data: 'cantidad_ubicaciones' },
			],
            language: get_idioma(),
            dom: 'Bfrtip',
			buttons: [],
        });

        $('#table_mant_gest tbody').on('click', 'tr', function () {
            $('#table_mant_gest tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });

		// GESTION MANTENIMIENTO

        $('#table_mant_gest tbody').on('click', 'tr td .ver', function () {
			let {id} = myTable.row($(this).parent().parent()).data();
			id_lugar = id;
			listar_ubicaciones_mant_gest(id);
			$("#modal_ubicaciones_gest_mant").modal("show");
        });


    });
}

const listar_ubicaciones_mant_gest = (id_lugar) => {
	consulta_ajax(`${url}listar_ubiaciones_mantenimiento`, {id_lugar} , resp => {
        $(`#tbl_ubicaciones_gest_mant tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .ver').off('click', 'tr td .iniciar_mantenimiento');
        const myTable = $('#tbl_ubicaciones_gest_mant').DataTable({
            destroy: true,
            processing: true,
            searching: true,
            data: resp,
            columns: [
                { defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control ver"><span >ver</span></span>' },
                { data: 'ubicacion' },
                { data: 'cantidad_objetos' },
				{ data: 'fecha_inicio' },
				{ data: 'estado_mtto' },
				{ data: 'accion' },
			],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: []
        });

        $('#tbl_ubicaciones_gest_mant tbody').on('click', 'tr', function () {
            $('#tbl_ubicaciones_gest_mant tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });

				
		$('#tbl_ubicaciones_gest_mant tbody').on('click', 'tr td .ver', function () {
			let { ubicacion, fecha_inicio, fecha_fin, lugar, id, id_historial } = myTable.row($(this).parent().parent()).data();
			$(".info_lugar").html(lugar);
			$(".info_ubicacion").html(ubicacion);
			$(".info_fecha").html(fecha_inicio);
			$(".info_fecha_fin").html(fecha_fin);
			listar_detalles_solictud(id_lugar, id);
			$('#modal_detalle').modal();
		});


		$('#tbl_ubicaciones_gest_mant tbody').on('click', 'tr td .iniciar_mantenimiento', async function() {
			let {id, id_historial, id_estado_matto } = myTable.row($(this).parent().parent()).data();
			id_ubicacion = id;
			id_estado_mtto = id_estado_matto;
			id_historial_mtto = id_historial;
			let validar = await validar_existencia_mantenimiento(id_lugar, id);
			if(validar.length === 0){
				msj_confirmacion('¿ Estas Seguro ?', `Desea crear un nuevo mantenimiento ?. Tener en cuenta que no podra revertir esta acción.!`, () => generar_mantenimiento(id));
			}else{
				listar_objetos_mant_gest(id_ubicacion, id_lugar, id_historial, 'iniciar_mantenimiento');
				$("#modal_objetos_gest_mant").modal("show");
			}
        });


    });
}


const listar_objetos_mant_gest = (id_ubicacion, id_lugar, id_historial, ruta='listar_objetos_matto_gest') => {
	let cantidad = 0;
    consulta_ajax(`${url}`+ruta, {id_ubicacion, id_lugar, id_historial}, resp => {
		cantidad = resp[2];
        $(`#tbl_objetos_gest_mant tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .ver').off('click', 'tr td .evidencia').off('click', 'tr td .estado').off('click', 'tr td .eliminar');
        const myTable = $('#tbl_objetos_gest_mant').DataTable({
            destroy: true,
            processing: true,
            searching: true,
            data: resp[0],
            columns: [
                { defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control ver"><span >ver</span></span>' },
                { data: 'nombre_obj' },
				{ data: 'estado_obj' },
				{ data: 'accion' },
				// { defaultContent: '<span style="color: #337ab7" class="btn btn-default red evidencia" title="Evidencia"><span class="fa fa-upload"></span></span>' },
			],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: []
        });

        $('#tbl_objetos_gest_mant tbody').on('click', 'tr', function () {
            $('#tbl_objetos_gest_mant tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });

		$('#tbl_objetos_gest_mant tbody').on('click', 'tr td .ver', function () {
			let data = myTable.row($(this).parent().parent()).data();
			listar_evidencia_mant_gest(data.id_objeto_mantenimiento, id_historial);
			$('#modal_evidencia_gest_mant').modal("show");
			// $(".ver_evidencia").attr("href", `${Traer_Server()}archivos_adjuntos/mantenimiento/evidencias/${data.foto}`);
        });

		$('#tbl_objetos_gest_mant tbody').on('click', 'tr td .estado', function () {
			let data = myTable.row($(this).parent().parent()).data();
			id_objeto = data.id_objeto_mantenimiento;
			$("#modal_objetos_estados_gest_mant").modal("show");
        });



		$('#tbl_objetos_gest_mant tbody').on('click', 'tr td .evidencia', function () {
			let data = myTable.row($(this).parent().parent()).data();
			id_objeto = data.id_objeto_mantenimiento;
			configuracion_camara();
			$("#modal_registrar_evidencia").modal("show");

        });

		$('#tbl_objetos_gest_mant tbody').on('click', 'tr td .eliminar', function () {
			let data = myTable.row($(this).parent().parent()).data();
			eliminar_datos({ id: data.id, title: "Eliminar datos?", tabla_bd: 'objetos_mantenimiento' }, () => {
				listar_objetos_mant_gest(id_ubicacion, id_lugar, id_historial, 'iniciar_mantenimiento');
				listar_ubiaciones_mantenimiento(id_lugar);
			});
		});

			if(resp[1]) $("#btn_finalizar_mantenimeinto").removeClass('oculto');
			else  $("#btn_finalizar_mantenimeinto").addClass('oculto');

			objetos_sin_estado = resp[3];
    });



	$('#btn_finalizar_mantenimeinto').click( async () => {
		let { id } = await validar_existencia_mantenimiento(id_lugar, id_ubicacion);
		msj_confirmacion('¿ Estas Seguro ?', `Desea finalizar el mantenimiento ?. Tener en cuenta que no podra revertir esta acción.!`, () => finalizar_solicitud_mantenimiento(id, objetos_sin_estado));
		return false;
	});
	
	$("#modal_objetos_gest_mant").modal("show");
}

const listar_detalles_solictud = (id_lugar, id_ubicacion) => {
	consulta_ajax(`${url}listar_detalles_solictud`, {id_lugar, id_ubicacion} , resp => {
        $(`#tbl_detalle_gest_mant tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .ver_evidencia').off('click', 'tr td .ver');
        const myTable = $('#tbl_detalle_gest_mant').DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data: resp,
            columns: [
				{ defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control ver"><span >ver</span></span>' },
                { data: 'nombre' },
				{ data: 'fecha_inicio' },
			],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: [],
        });

        $('#tbl_detalle_gest_mant tbody').on('click', 'tr', function () {
            $('#tbl_detalle_gest_mant tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });


		$('#tbl_detalle_gest_mant tbody').on('click', 'tr td .ver', function () {
			let data = myTable.row($(this).parent().parent()).data();
			listar_objetos_mant_gest(data.id_ubicacion, data.id_lugar, data.id);			
        });

    });
}

const listar_evidencia_mant_gest = (id_objeto, id_historial) => {
    consulta_ajax(`${url}listar_evidencia_matto_gest`, {id_objeto, id_historial}, resp => {
        $(`#tbl_evidencia_gest_mant tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .evidencia');
        const myTable = $('#tbl_evidencia_gest_mant').DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data: resp,
            columns: [
				{ data: 'objeto' },
                { data: 'fecha_registra' },
				{ defaultContent: '<a target="_blank" class="evidencia"><span style="color: #337ab7" class="btn btn-default red" title="Evidencia"><span class="fa fa-eye"></span></span></a>' },
			],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: []
        });

		if(!id_objeto) myTable.column(0).visible(true);
		else myTable.column(0).visible(false);

        $('#tbl_evidencia_gest_mant tbody').on('click', 'tr', function () {
            $('#tbl_objetos_gest_mant tbody tr').removeClass('warning');
            $(this).attr('class', 'warning');
        });

		$('#tbl_evidencia_gest_mant tbody').on('click', 'tr td .evidencia', function () {
			let data = myTable.row($(this).parent().parent()).data();
			$(".evidencia").attr("href", `${Traer_Server()}archivos_adjuntos/mantenimiento/evidencias/${data.foto}.png`);
        });

    });
	
}

const listar_mantenimiento_periodico_filtro = (id_lugar='', id_periodicidad='', estado='', id_tipo='', fecha_inicio='', fecha_fin='') => {
	consulta_ajax(`${url}listar_mantenimiento_periodico_filtro`, {id_lugar, id_periodicidad, estado, id_tipo, fecha_inicio, fecha_fin}, data => {
		$('#tbl_filtros_mantenimiento_perodico tbody')
			.off('dblclick', 'tr')
			.off('click', 'tr')
			.off('click', 'tr td .ver')
			.off('click', 'tr td:nth-of-type(1)');
		const myTable = $("#tbl_filtros_mantenimiento_perodico").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: data,
			columns: [
				{ defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control ver"><span >ver</span></span>' },
				{ "data": "lugar" },
				{ "data": "periodicidad" },
				{ "data": "estado" },
				{ "data": "fecha_inicio" },
				{ "data": "fecha_fin"}
			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": get_botones(),
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_filtros_mantenimiento_perodico tbody').on('click', 'tr', function () {
			$("#tbl_filtros_mantenimiento_perodico tbody tr").removeClass("warning");
			$(this).addClass("warning");
		});

		$('#tbl_filtros_mantenimiento_perodico tbody').on('dblclick', 'tr', function () {
			let data = myTable.row(this).data();
			categoria_s = data.cat;
			$("#modal_filtros_gestion_mantenimiento").modal();
		});

		$('#tbl_filtros_mantenimiento_perodico tbody').on('click', 'tr td .ver', function () {
			let data = myTable.row($(this).parent()).data();
			evidencias_mantenimiento_periodico(data.id);
			$("#modal_evidencia_mantenimiento_periodico").modal("show");	
        }); 
	

	});
};


const listar_mantenimiento_gestion_filtro = (id_lugar, id_ubicacion, estado, fecha_inicio, fecha_fin, id_estado_objeto) => {
	consulta_ajax(`${url}listar_mantenimiento_gestion_filtro`, {id_lugar, id_ubicacion, estado, fecha_inicio, fecha_fin, id_estado_objeto}, data => {
		$('#tbl_filtros_gestion_mantenimiento tbody')
			.off('dblclick', 'tr')
			.off('click', 'tr')
			.off('click', 'tr td .ver')
			.off('click', 'tr td:nth-of-type(1)');
		const myTable = $("#tbl_filtros_gestion_mantenimiento").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: data,
			columns: [
				{ defaultContent: '<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control ver"><span >ver</span></span>' },
				{ "data": "lugar" },
				{ "data": "ubicacion" },
				{ "data": "estado" },
				{ "data": "fecha_inicio" },
				{ "data": "fecha_fin"}
			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": get_botones(),
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$('#tbl_filtros_gestion_mantenimiento tbody').on('click', 'tr', function () {
			$("#tbl_filtros_gestion_mantenimiento tbody tr").removeClass("warning");
			$(this).addClass("warning");
		});

		$('#tbl_filtros_gestion_mantenimiento tbody').on('dblclick', 'tr', function () {
			let data = myTable.row(this).data();
			categoria_s = data.cat;
			$("#Modal-info-modal_filtros_gestion_mantenimiento").modal();
		});

		$('#tbl_filtros_gestion_mantenimiento tbody').on('click', 'tr td .ver', function () {
			let data = myTable.row($(this).parent().parent()).data();
			listar_evidencia_mant_gest('', data.id);
			$('#modal_evidencia_gest_mant').modal("show");
        });
	

	});
};


const configuracion_camara = ( btn = 'botonFoto', camara = 'camara', foto = 'foto') => {

    let foto_requerida = true;
    let video = document.getElementById(camara);

    //Nos aseguramos que estén definidas
    //algunas funciones básicas
    window.URL = window.URL || window.webkitURL;
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || 
        function () {
            if (foto_requerida) MensajeConClase("Su navegador no soporta navigator.getUserMedia().", "info", "Oops.!");
            $(".div_camara").remove();
            tiene_camara = false;
            foto_requerida = false;
        };
 
    //Este objeto guardará algunos datos sobre la cámara
    window.datosVideo = {
        'StreamVideo': null,
        'url': null
    }

    //Pedimos al navegador que nos da acceso a 
    //algún dispositivo de video (la webcam)


	// Video FacingMode: "environment" Es para camara trasera
	// Video FacingMode: "user" Es para camara delantera

    navigator.getUserMedia
	({
        'audio': false,
        'video': {
			facingMode: "environment"
		}
    }, streamVideo => {
        if (foto_requerida) {
            video.srcObject = streamVideo;
            video.play();
        } else {
            $(".div_camara").remove();
            tiene_camara = false;
        }
    }, () => {
        if (foto_requerida) MensajeConClase("No fue posible obtener acceso a la cámara.", "info", "Oops.!");
        $(".div_camara").remove();
        tiene_camara = false;
        foto_requerida = false;
    });
 
    jQuery(`#${btn}`).on('click', e => {
        tomo_foto = true;
        let oCamara, oFoto, oContexto, w, h;
 
        oCamara = jQuery(`#${camara}`);
        oFoto = jQuery(`#${foto}`);
        w = oCamara.width();
        h = oCamara.height();
        oFoto.attr({
            'width': w,
            'height': h
        });
        oContexto = oFoto[0].getContext('2d');
        oContexto.drawImage(oCamara[0], 0, 0, w, h);
 
    });
}


const buscar_objetos_inspeccion_mantenimiento = (dato) => {
	let num = 0;
	consulta_ajax(`${url}buscar_objetos_inspeccion_mantenimiento`, {dato} , resp => {
        $(`#tbl_objeto_buscado tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .seleccionar');
        const myTable = $('#tbl_objeto_buscado').DataTable({
            destroy: true,
            processing: true,
            searching: false,
            data: resp,
            columns: [
				{render: () => ++num},
                { data: 'valor' },
				{ defaultContent: '<span style="color: #39B23B" class="btn btn-default red seleccionar" title="seleccionar"><span class="fa fa-check"></span></span>' },
			],
            language: get_idioma(),
            dom: 'Bfrtip',
            buttons: []
        });

		$('#tbl_objeto_buscado tbody').on('click', 'tr', function () {
			$('#tbl_objeto_buscado tbody tr').removeClass("warning");
			$(this).attr("class", "warning");
        });

		
		$('#tbl_objeto_buscado tbody').on('dblclick', 'tr', function () {
            let data = myTable.row($(this).parent().parent()).data();
            callbak(data);
        });
        $('#tbl_objeto_buscado tbody').on('click', 'tr td .seleccionar', function () {
            let data = myTable.row($(this).parent().parent()).data();
			guardar_objeto_mantenimiento(data.id, id_lugar, id_ubicacion);
        });


    });
}


function Retirar_objeto_seleccionado(id) {
	let posicion_array = objetos.find((element) => element.id === id);
	let objeto_a_eliminar = objetos.indexOf(posicion_array);
	objetos.splice(objeto_a_eliminar, 1);


	if (posicion_array) {
		// si encuentra la posicion
		$("#objeto_asignado_reparacion, #objeto_asignado_reparacion2").html(" ");

		// Continua el proceso
		$("#objeto_asignado_reparacion, #objeto_asignado_reparacion2").html(`<option value="">${objetos.length} Objetos Agregados</option>`);
		objetos.forEach((element) => $("#objeto_asignado_reparacion, #objeto_asignado_reparacion2").append(`<option value="${element.id}">${element.cantidad} ${element.nombre}</option>`));
		MensajeConClase("", "success", "Recurso Retirado..!");

	} else {
		MensajeConClase("No fue posible retirar el recurso.!!", "info", "Oops..!");
	}
}


function guardar_objeto_valor_parametro() {
	const data = new FormData(document.getElementById("GuardarObjetosMantenimiento"));
	enviar_formulario(`${url}guardar_objeto_valor_parametro`, data, ({ mensaje, titulo, tipo, id }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo == 'success') {
			MensajeConClase(mensaje, tipo, titulo);
			$("#modal_guardar_objetos_mantenimiento").modal('hide');

		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
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
				swal.close();
                consulta_ajax(`${url}eliminar_datos`, { id, tabla_bd }, resp => {
                    let { tipo, mensaje, titulo } = resp;
                    if (tipo == 'success') {
                        callback();
                    }
					MensajeConClase(mensaje, tipo, titulo);
                });
            }
        });
}

const guardar_evidencia = async () =>{
	let data = new FormData(document.getElementById("form_registrar_evidencia"));
	data.append('id_objeto', id_objeto);
	let {id} = await validar_existencia_mantenimiento(id_lugar, id_ubicacion);
	data.append('id_historial_preventivo', id);
	if (tomo_foto) {
		canvas = document.getElementById("foto");
		let foto = canvas.toDataURL("image/jpeg");
		let info = foto.split(",", 2);
		data.append("foto", info[1]);
	} else {
		MensajeConClase("Antes de continuar debe tomar la foto del objeto.!", 'info', 'Oops.!');
		return;
	}
	enviar_formulario(`${url}guardar_evidencia_mtto`, data, (resp) => {
		let { mensaje,tipo,titulo } = resp;
		if (mensaje == "sin_session") {
			close();
		} else if (tipo == 'success') {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_registrar_evidencia").get(0).reset();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}

	});
	return;
}


const guardar_historial_mantenimiento = () => {
	consulta_ajax(`${url}guardar_historial_mantenimiento`, data, (resp) => {
		let { mensaje,tipo,titulo } = resp;
		if (mensaje == "sin_session") {
			close();
		} else if (tipo == 'success') {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_registrar_evidencia").get(0).reset();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}

	});
}


const validar_existencia_mantenimiento = async (id_lugar, id_ubicacion) => {
	return new Promise(resolve => {
		let ruta = `${url}validar_existencia_mantenimiento`;
		consulta_ajax(ruta, { id_lugar, id_ubicacion }, resp => {
			resolve(resp);
		});
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

const generar_mantenimiento = (id_ubicacion, tipo_sol) => {
	consulta_ajax(`${url}generar_mantenimiento`, {id_ubicacion, id_lugar, tipo_sol},  (resp) => {
		let { mensaje,tipo,titulo, id_historial, existe} = resp;
		if (tipo == 'success' && existe == false) {
			swal.close();
			id_historial_mtto = id_historial;
			if(tipo_sol != "periodico"){
				listar_objetos_mant_gest(id_ubicacion, id_lugar, id_historial);
				listar_ubicaciones_mant_gest(id_lugar);
				listar_objetos_mant_gest(id_ubicacion,id_lugar,id_historial, 'iniciar_mantenimiento');
				$("#modal_objetos_gest_mant").modal("show");
			}else{
				get_solicitud_matenimiento('T_Matt_anual');
				listar_lugares_mantenimientos_periodico('iniciar');
				$("#modal_lugares_mantenimiento_periodico").modal("show");
				// colocar algo cuando la solicitud no sea periodica

			}
		} else if(existe == true){
			swal.close();
			get_solicitud_matenimiento('T_Matt_anual');
			listar_lugares_mantenimientos_periodico('iniciar');
			$("#modal_lugares_mantenimiento_periodico").modal("show");
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}

	});
}

const finalizar_solicitud_mantenimiento =  (id_historial, objetos_sin_estado) => {
	consulta_ajax(`${url}finalizar_solicitud_mantenimiento`, {id_historial, objetos_sin_estado, id_ubicacion, id_lugar},  (resp) => {
		let { mensaje,tipo,titulo } = resp;
		if (tipo == 'success') {
			listar_ubicaciones_mant_gest(id_lugar);
			$("#modal_objetos_gest_mant").modal("hide");
		}
		MensajeConClase(mensaje, tipo, titulo);
		
	});
	// swal.close();
}

