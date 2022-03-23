let traslados_solcitud = [];
let server_app = "localhost";
let busqueda_orden_nuevo = '';
let id_traslado_nuevo = 1;
let id_comite = 0;
let ano_activo = null;
let traslado_nuevo = {
	'nombre_orden_origen': null,
	'id_orden_origen': null,
	'nombre_cuenta_origen': null,
	'id_cuenta_origen': null,
	'nombre_orden_destino': null,
	'id_orden_destino': null,
	'nombre_cuenta_destino': null,
	'id_cuenta_destino': null,
	'valor': null,
	'justificacion': null,
	'tipo': '1',
	'tipo_traslado': 'OO',
	'id': id_traslado_nuevo,
	'id_ano': null
};
let solicitante = {
	'id': null
};
let adm_activo = null;
let id_traslado_sele = null;
let buscar_origen = 25;
let buscar_destino = 25;
let accion = 'add';
let administra = false;
let orden_centro = {
	'id': null,
	'nombre': null
}
let data_comite = [];
let tipos_traslados = [
	{ 'id_aux': 'OO', 'valor': 'Traslado de Orden a Orden' },
	{ 'id_aux': 'CC', 'valor': 'Centro costo - Centro costo' },
	{ 'id_aux': 'OC', 'valor': 'Orden - Centro Costo' },
	{ 'id_aux': 'CO', 'valor': 'Centro costo - Orden' },
	{ 'id_aux': 'CA', 'valor': 'Adición a Centro costo' },
	{ 'id_aux': 'OA', 'valor': 'Adición a Orden' },
	{ 'id_aux': 'CD', 'valor': 'Centro costo Disminución' },
	{ 'id_aux': 'OD', 'valor': 'Orden Disminución' },
];
let data_solicitante = {
	'nombre': null,
	'correo': null,
}
$(document).ready(() => {
	server_app = Traer_Server();
	$(".detalle_persona_solicita").click(() => {
		obtener_datos_persona_id_completo(solicitante.id, ".nombre_perso", ".apellido_perso", ".identi_perso", ".tipo_id_perso", ".foto_perso", ".ubica_perso", ".depar_perso", ".cargo_perso", ".perfil_perso", ".celular");
		$("#Mostrar_detalle_persona").modal("show");
	});
	$('#btn_notificaciones').click(() => {
		mostrar_notificaciones_comentarios_comite('presupuesto', (id) => { abrir_traslados_comite_comentario(id) });
		$("#modal_notificaciones").modal();
	});
	$("#form_guardar_valor_parametro").submit(() => {
		guardar_valor_parametro();
		return false;
	});
	$("#imprimir_acta").click(() => {
		crear_acta_comite(data_comite);
	});

	$("#form_guardar_comentario").submit(() => {
		guardar_comentario_comite(id_comite);
		return false;
	});
	$("#form_guardar_comite").submit(() => {
		confirmar_guardar_comite();
		return false;
	});
	$("#form_gestionar_solicitud").submit(() => {
		let estado = $("#form_gestionar_solicitud select[name='estado']").val();
		let titulo = 'Traslado Aprobado .?';
		if (estado == 'Tras_Neg' || estado == 'Tras_Com') {
			titulo = estado == 'Tras_Neg' ? 'Traslado Negado .?' : 'Traslado a Comité .?';
			gestionar_solicitud_texto(estado, id_traslado_sele, titulo);
		} else {
			gestionar_solicitud(estado, id_traslado_sele, titulo);
		}
		return false;

	});
	$("#form_modificar_comite").submit(() => {
		modificar_comite_general(id_comite);
		return false;
	});
	$("#form_modificar_valor_parametro").submit(() => {
		modificar_valor_parametro();
		return false;
	});

	$("#btn_admin_solicitudes").click(() => {
		$("#Modal_administrar_solicitudes").modal("show");
	});
	$("#tipo_traslado").change(() => {
		let tipo = $("#tipo_traslado").val();
		if (accion == 'add') administrar_tipo_traslado(tipo);
		else MensajeConClase('Acción invalida', 'info', 'Oops.!');
	});

	$("#btn_filtrar").click(() => {
		listar_traslados_solicitudes();
	});

	$('#admin_cuentas').click(function () {
		$("#nav_admin_compras li").removeClass("active");
		$(this).addClass("active");
		administrar_modulo('cuentas', 45);
	});
	$('#admin_ordenes').click(function () {
		$("#nav_admin_compras li").removeClass("active");
		$(this).addClass("active");
		administrar_modulo('ordenes', 25);
	});
	$('#admin_costo').click(function () {
		$("#nav_admin_compras li").removeClass("active");
		$(this).addClass("active");
		administrar_modulo('costo', 51);
	});
	$('#admin_comite').click(function () {
		$("#nav_admin_compras li").removeClass("active");
		$(this).addClass("active");
		administrar_modulo('comite');
	});
	$('#admin_presupuestos').click(function () {
		$("#nav_admin_compras li").removeClass("active");
		$(this).addClass("active");
		administrar_modulo('presupuestos', 49);
	});

	$(".regresar_menu").click(() => {
		administrar_elementos('menu');
	});
	$("#listado_solicitudes").click(() => {
		administrar_elementos('solicitudes');
	});
	$("#btn_agregar_traslados").click(() => {
		accion = 'add';
		$("#tipo_traslado").show('fast');
		limpiar_array_traslado();
		$("#modal_agregar_traslado .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Agregar Traslado</span>');
		$("#modal_agregar_traslado").modal();
	});
	$("#btn_nueva_solicitud").click(() => {
		obtener_anos_activos();
		listar_traslados_nueva_solicitud(traslados_solcitud);
		$("#modal_nueva_solicitud").modal();
	});
	$("#btn_orden_origen").click(() => {
		busqueda_orden_nuevo = 'origen';
		configurar_busqueda_codigo('');
		$("#modal_buscar_codigo").modal();
	});
	$("#btn_orden_destino").click(() => {
		busqueda_orden_nuevo = 'destino';
		configurar_busqueda_codigo('');
		$("#modal_buscar_codigo").modal();

	});
	$("#btn_cuenta_origen").click(() => {
		busqueda_orden_nuevo = 'cuenta_origen';
		configurar_busqueda_codigo('');
		$("#modal_buscar_codigo").modal();

	});
	$("#btn_cuenta_destino").click(() => {
		busqueda_orden_nuevo = 'cuenta_destino';
		configurar_busqueda_codigo('');
		$("#modal_buscar_codigo").modal();

	});
	$("#btn_limpiar_filtros").click(() => {
		$("#estado_filtro").val('');
		$("#fecha_filtro").val('');
		listar_traslados_solicitudes();
	});
	$("#btn_limpiar_filtros_comite").click(() => {
		listar_comites(2);
	});
	$("#form_buscar_codigo").submit(() => {
		configurar_busqueda_codigo($("#txt_codigo_sap").val());
		return false;
	});
	$("#form_agregar_solicitud").submit(() => {
		agregar_solicitud();
		return false;
	});
	$("#form_agregar_traslado").submit(() => {
		traslado_nuevo.valor = ($("#form_agregar_traslado input[name=valor]").val()).replace(/\D/g, '');
		traslado_nuevo.justificacion = $("#form_agregar_traslado textarea[name=justificacion]").val();
		traslado_nuevo.id_ano = $("#form_agregar_traslado select[name=ano]").val();
		guardar_traslado();
		return false;
	});
	$('#modal_buscar_codigo').on('shown.bs.modal', () => {
		$('#txt_codigo_sap').focus().val('');
	});
});

const administrar_elementos = item => {
	if (item == 'menu') {
		$("#menu_principal").fadeIn(1000);
		$("#container_solicitudes").css("display", "none");
	} else if (item == 'solicitudes') {
		$("#menu_principal").css("display", "none");
		$("#container_solicitudes").fadeIn(1000);
	}
}

const listar_traslados_solicitudes = (id_solicitud = -1) => {
	let {
		estado,
		fecha
	} = obtener_filtros();
	$(`#tabla_traslados_detalle_solicitud tbody`).off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
	let myTable = $("#tabla_traslados_detalle_solicitud").DataTable({
		"destroy": true,
		"ajax": {
			url: `${server_app}index.php/presupuesto_control/listar_traslados_solicitudes`,
			dataType: "json",
			type: "post",
			data: {
				id_solicitud,
				estado,
				fecha
			},
			"dataSrc": json => {
				return json.length == 0 ? Array() : json.data;
			},
		},
		"processing": true,
		"columns": [{
			"data": "ver"
		}, {
			"data": "tipo_traslado"
		},
		{
			data: 'nombre_ano'
		},
		{
			data: 'nombre_solicitante'
		},
		{
			"data": "nombre_orden_origen"
		},
		{
			"data": "nombre_cuenta_origen"
		},
		{
			"data": "nombre_orden_destino"
		},
		{
			"data": "nombre_cuenta_destino"
		},
		{
			"data": "centro_origen"
		},
		{
			"data": "centro_destino"
		},
		{
			"data": "justificacion"
		},
		{
			"data": "valor_final"
		},
		{
			"data": "estado_traslado"
		},
		{
			"data": "gestion"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": get_botones([3,4,5,6,7,8,9,10,11]),
	});

	myTable.column(8).visible(false);
	myTable.column(9).visible(false);
	myTable.column(10).visible(false);

	$('#tabla_traslados_detalle_solicitud tbody').on('click', 'tr', function () {
		$("#tabla_traslados_detalle_solicitud tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		let { nombre_solicitante, correo } = myTable.row($(this)).data();
		data_solicitante.nombre = nombre_solicitante;
		data_solicitante.correo = correo;
	});

	$('#tabla_traslados_detalle_solicitud tbody').on('dblclick', 'tr', function () {
		let data = myTable.row($(this)).data();
		ver_detalle_traslados(data);

	});

	$('#tabla_traslados_detalle_solicitud tbody').on('click', 'tr td:nth-of-type(1)', function () {
		let data = myTable.row($(this).parent()).data();
		ver_detalle_traslados(data);
	});

	let column = myTable.column(1);
	column.visible(administra);

}
const ver_detalle_traslados = (data, tabla = '#tabla_detalle_traslado', modal = '#modal_detalle_traslado', tipo = null) => {
	let {
		nombre_solicitante,
		fecha_registra,
		estado_traslado,
		valor,
		justificacion,
		nombre_orden_origen,
		nombre_cuenta_origen,
		nombre_orden_destino,
		nombre_cuenta_destino,
		des_orden_origen,
		des_cuenta_origen,
		des_orden_destino,
		des_cuenta_destino,
		id,
		mensaje,
		usuario_registra,
		valor_aprobado,
		valor_final,
		nombre_ano,
		justificacion_comite,
		tipo_traslado,
		des_centro_destino,
		centro_destino,
		des_centro_origen,
		centro_origen,
		valor_format,
		valor_aprobado_format,
		nombre_avala,
		id_estado_traslado
	} = data;
	let tipo_tras = obtener_nombre_tipo_traslado(tipo_traslado);
	$(`${tabla} .tipo_traslado`).html(tipo_tras.valor.toUpperCase());
	$(`${tabla} .nombre_solicitante`).html(nombre_solicitante);
	$(`${tabla} .fecha_registra`).html(fecha_registra);
	$(`${tabla} .estado_traslado`).html(estado_traslado);
	$(`${tabla} .valor`).html(tipo == null ? valor_format : valor_final);
	$(`${tabla} .justificacion`).html(justificacion);
	$(`${tabla} .justificacion_comite`).html(justificacion_comite);
	$(`${tabla} .orden_origen`).html(nombre_orden_origen);
	$(`${tabla} .orden_destino`).html(nombre_orden_destino);
	$(`${tabla} .nombre_avala`).html(nombre_avala);

	$(`${tabla} .ano_traslado`).html(nombre_ano);

	if (nombre_orden_origen == null || nombre_orden_origen.length == 0 || !administra) {
		$(`${tabla} .tr_cuenta_origen`).hide('fast');
	} else {
		$(`${tabla} .cuenta_origen`).html(`<b>De:</b> ${nombre_orden_origen}, ${des_orden_origen};\n<b>Cuenta:</b> ${nombre_cuenta_origen},${des_cuenta_origen}`);
		$(`${tabla} .tr_cuenta_origen`).show('fast');
	}
	if (nombre_orden_destino == null || nombre_orden_destino.length == 0 || !administra) {
		$(`${tabla} .tr_cuenta_destino`).hide('fast');
	} else {
		$(`${tabla} .cuenta_destino`).html(`<b>Para:</b> ${nombre_orden_destino}, ${des_orden_destino};\n<b>Cuenta:</b> ${nombre_cuenta_destino},${des_cuenta_destino}`);
		$(`${tabla} .tr_cuenta_destino`).show('fast');
	}


	if (mensaje == null || mensaje.length == 0) {
		$(`${tabla} .tr_mensaje`).hide('fast');
	} else {
		$(`${tabla} .mensaje`).html(mensaje);
		$(`${tabla} .tr_mensaje`).show('fast');
	}
	if (centro_origen == null || centro_origen.length == 0) {
		$(`${tabla} .tr_centro_origen`).hide('fast');
	} else {
		$(`${tabla} .centro_origen`).html(`<b>De:</b> ${centro_origen}, ${des_centro_origen}.`);
		$(`${tabla} .tr_centro_origen`).show('fast');
	}
	if (centro_destino == null || centro_destino.length == 0 || !administra) {
		$(`${tabla} .tr_centro_destino`).hide('fast');
	} else {
		$(`${tabla} .centro_destino`).html(`<b>Para:</b> ${centro_destino}, ${des_centro_destino}.`);
		$(`${tabla} .tr_centro_destino`).show('fast');
	}
	if (valor_aprobado == null || valor_aprobado.length == 0 || !administra) {
		$(`${tabla} .tr_valor_aprobado`).hide('fast');
	} else {
		$(`${tabla} .valor_aprobado`).html(valor_aprobado_format);
		$(`${tabla} .tr_valor_aprobado`).show('fast');
	}

	if (id_estado_traslado == 'Tras_Vis') $(`${tabla} .tr_persona_avala`).show('fast');
	else $(`${tabla} .tr_persona_avala`).hide('fast');

	solicitante.id = usuario_registra;
	listar_estados_tralados(id);
	$(modal).modal('show');
}

const listar_estados_tralados = id_traslado => {
	$(`#tabla_estados_solicitud tbody`).off('click', 'tr');
	let myTable = $("#tabla_estados_solicitud").DataTable({
		"destroy": true,
		"ajax": {
			url: `${server_app}index.php/presupuesto_control/listar_estados_tralados`,
			dataType: "json",
			type: "post",
			data: {
				id_traslado
			},
			"dataSrc": json => {
				return json.length == 0 ? Array() : json.data;
			},
		},
		"processing": true,
		"order": [
			[1, "asc"]
		],
		"columns": [
			{
				"data": 'estado_traslado'
			},
			{
				"data": "fecha_registro"
			},
			{
				"data": "persona"
			},
			{
				"data": "observaciones"
			},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [],
	});

	$('#tabla_estados_solicitud tbody').on('click', 'tr', function () {
		$("#tabla_estados_solicitud tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});
}

const listar_traslados_nueva_solicitud = (datos, tabla = '#tabla_traslados_solicitud') => {
	datos.map(i => i.valor_convert = new Intl.NumberFormat().format(i.valor))
	$(`${tabla} tbody`).off('dblclick', 'tr').off('click', 'tr').off('click', '.modificar');
	const myTable = $(tabla).DataTable({
		"destroy": true,
		"processing": true,
		data: datos,
		columns: [{
			data: 'nombre_orden_origen'
		}, {
			data: 'nombre_cuenta_origen'
		},
		{
			data: 'nombre_orden_destino'
		},
		{
			data: 'nombre_cuenta_destino'
		},
		{
			data: 'valor_convert'
		},
		{
			data: 'justificacion'
		},

		{

			"render": function (data, type, full, meta) {
				return `<span title='Eliminar' style='color: #DE4D4D;'  data-toggle='popover' data-trigger='hover' class='fa fa-trash-o pointer btn btn-default' onclick='eliminar_traslado_array(${full.id})'></span> <span style='color: #2E79E5;' title='Editar' data-toggle='popover' data-trigger='hover' class='fa fa-wrench pointer btn btn-default modificar'></span>`;
			}
		}

		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$(`${tabla} tbody`).on('click', 'tr', function () {
		$(`${tabla} tbody tr`).removeClass("warning");
		$(this).attr("class", "warning");

	});

	$(`${tabla} tbody`).on('click', '.modificar', function () {
		let data = myTable.row($(this).parents('tr')).data();
		mostrar_traslado_modificar(data);
	});

}


const mostrar_traslado_modificar = (data, tipo = 3) => {

	let {
		nombre_orden_origen,
		id_orden_origen,
		nombre_cuenta_origen,
		id_cuenta_origen,
		nombre_orden_destino,
		id_orden_destino,
		nombre_cuenta_destino,
		id_cuenta_destino,
		valor,
		justificacion,
		id,
		id_ano,
		tipo_traslado
	} = data;
	accion = 'modi';
	administrar_tipo_traslado(tipo_traslado);
	$("#tipo_traslado").hide('fast');
	traslado_nuevo = {
		nombre_orden_origen,
		id_orden_origen,
		nombre_cuenta_origen,
		id_cuenta_origen,
		nombre_orden_destino,
		id_orden_destino,
		nombre_cuenta_destino,
		id_cuenta_destino,
		valor,
		justificacion,
		tipo,
		id,
		id_ano,
		tipo_traslado
	};
	if (tipo == 4) obtener_anos_activos('mod', id_ano);
	else $("#form_agregar_traslado select[name=ano]").val(id_ano);
	$("#txt_nombre_orden_origen").val(nombre_orden_origen);
	$("#txt_nombre_orden_destino").val(nombre_orden_destino);
	$("#txt_nombre_cuenta_origen").val(nombre_cuenta_origen);
	$("#txt_nombre_cuenta_destino").val(nombre_cuenta_destino);
	$("#form_agregar_traslado input[name=valor]").val(valor);
	$("#form_agregar_traslado textarea[name=justificacion]").val(justificacion);
	$("#modal_agregar_traslado .modal-title").html('<span class="fa fa-retweet"></span> <span id="text_add_arts">Modificar Traslado</span>');
	$("#modal_agregar_traslado").modal();

}

const buscar_codigo = (buscar, callback, idparametro = '25') => {
	$('#tabla_codigos tbody').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(3)');
	const myTable = $("#tabla_codigos").DataTable({
		"destroy": true,
		"searching": false,
		"ajax": {
			url: `${server_app}index.php/presupuesto_control/buscar_codigo_sap`,
			data: {
				buscar,
				idparametro
			},
			"dataSrc": json => {
				return json.length == 0 ? Array() : json.data;

			},
			dataType: "json",
			type: "post",
		},
		"processing": true,
		"columns": [{
			"data": "valor"
		},
		{
			"data": "valorx"
		},
		{
			"data": "gestion"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_codigos tbody').on('dblclick', 'tr', function () {
		let data = myTable.row(this).data();
		callback(data);
	});
	$('#tabla_codigos tbody').on('click', 'tr td:nth-of-type(3)', function () {
		let data = myTable.row($(this).parent()).data();
		callback(data);
	});

}
const configurar_busqueda_codigo = codigo => {
	if (busqueda_orden_nuevo == "origen") {
		$("#titulo_modal_buscar").html(`${buscar_origen == 51 ? 'Centro' : 'Orden'} de Origen`);
		buscar_codigo(codigo, (data) => {
			traslado_nuevo.id_orden_origen = data.id;
			traslado_nuevo.nombre_orden_origen = data.valor;
			$("#txt_nombre_orden_origen").val(data.valor);
			$("#modal_buscar_codigo").modal('hide');
		}, buscar_origen);
	} else if (busqueda_orden_nuevo == "destino") {
		$("#titulo_modal_buscar").html(`${buscar_destino == 51 ? 'Centro' : 'Orden'} de Destino`);
		buscar_codigo(codigo, (data) => {
			traslado_nuevo.id_orden_destino = data.id;
			traslado_nuevo.nombre_orden_destino = data.valor;
			$("#txt_nombre_orden_destino").val(data.valor);
			$("#modal_buscar_codigo").modal('hide');
		}, buscar_destino);
	} else if (busqueda_orden_nuevo == "cuenta_origen") {
		$("#titulo_modal_buscar").html('Cuenta de Origen');
		buscar_codigo(codigo, (data) => {
			traslado_nuevo.id_cuenta_origen = data.id;
			traslado_nuevo.nombre_cuenta_origen = data.valor;
			$("#txt_nombre_cuenta_origen").val(data.valor);
			$("#modal_buscar_codigo").modal('hide');
		}, 45);
	} else if (busqueda_orden_nuevo == "cuenta_destino") {
		$("#titulo_modal_buscar").html('Cuenta de Destino');
		buscar_codigo(codigo, (data) => {
			traslado_nuevo.id_cuenta_destino = data.id;
			traslado_nuevo.nombre_cuenta_destino = data.valor;
			$("#txt_nombre_cuenta_destino").val(data.valor);
			$("#modal_buscar_codigo").modal('hide');
		}, 45);

	} else if (busqueda_orden_nuevo == "costo") {
		$("#titulo_modal_buscar").html('Centro Costo');
		buscar_codigo(codigo, (data) => {
			orden_centro.id = data.id;
			orden_centro.nombre = data.valor;
			$(".txt_nombre_centro").val(data.valor);
			$("#modal_buscar_codigo").modal('hide');
		}, 51);

	}
}
const guardar_traslado = () => {
	let url = `${server_app}index.php/presupuesto_control/guardar_traslado`;
	consulta_ajax(url, traslado_nuevo, (resp) => {
		let {
			mensaje,
			tipo,
			titulo,
			tipo_traslado
		} = resp;

		if (tipo == "sin_session") {
			close();
		} else if (tipo == 'success') {
			$("#form_agregar_traslado").get(0).reset();
			if (tipo_traslado == 1) {
				guardar_traslado_array();
			} else if (tipo_traslado == 3) {
				modificar_traslado_array();
				return;
			} else if (tipo_traslado == 4) {
				listar_traslados_solicitudes();
				$("#modal_agregar_traslado").modal("hide");
			}
		}
		MensajeConClase(mensaje, tipo, titulo);

	});
	return;
}
const agregar_solicitud = () => {
	let url = `${server_app}index.php/presupuesto_control/agregar_solicitud`;
	let data = {
		'traslados': traslados_solcitud
	};
	consulta_ajax(url, data, (resp) => {
		let {
			mensaje,
			tipo,
			titulo,
			id
		} = resp;

		if (tipo == "sin_session") {
			close();
		} else if (tipo == 'success') {
			listar_traslados_solicitudes();
			id_traslado_nuevo = 0;
			traslados_solcitud = [];
			enviar_correo_estado('Tras_Soli', id, '');
			$("#modal_nueva_solicitud").modal('hide');
		}
		MensajeConClase(mensaje, tipo, titulo);

	});
	return;
}

const guardar_traslado_array = () => {
	traslados_solcitud.push(traslado_nuevo);
	listar_traslados_nueva_solicitud(traslados_solcitud);
	id_traslado_nuevo++;
	limpiar_array_traslado();
}
const limpiar_array_traslado = (tipo_traslado = 'OO') => {
	traslado_nuevo = {
		'nombre_orden_origen': null,
		'id_orden_origen': null,
		'nombre_cuenta_origen': null,
		'id_cuenta_origen': null,
		'nombre_orden_destino': null,
		'id_orden_destino': null,
		'nombre_cuenta_destino': null,
		'id_cuenta_destino': null,
		'valor': null,
		'justificacion': null,
		'tipo': '1',
		'tipo_traslado': tipo_traslado,
		'id': id_traslado_nuevo,
		'id_ano': null
	};
	$("#form_agregar_traslado").get(0).reset();
};
const modificar_traslado_array = () => {
	let sw = false;
	traslados_solcitud.map(function (key, index) {
		if (key.id == traslado_nuevo.id) {
			traslados_solcitud[index] = traslado_nuevo;
			sw = true;
		}
	});
	if (sw) {
		listar_traslados_nueva_solicitud(traslados_solcitud);
		MensajeConClase('', 'success', 'Traslado Modificado');
		$("#modal_agregar_traslado").modal('hide');
	} else {
		MensajeConClase("El traslado no fue encontrado, intente de nuevo.", "info", 'Oops.!');
	}
	return;
}

const eliminar_traslado_array = traslado => {
	swal({
		title: "Estas Seguro ?",
		text: "El traslado será eliminado",
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
				let sw = false;
				traslados_solcitud.map(function (key, index) {
					if (key.id == traslado) {
						traslados_solcitud.splice(index, 1);
						sw = true;
					}
				});
				if (sw) {
					listar_traslados_nueva_solicitud(traslados_solcitud);
					//MensajeConClase("", "success", 'Traslado Retirado.!');
					swal.close();
				} else {
					MensajeConClase("El traslado no fue encontrado, intente de nuevo.", "info", 'Oops.!');
				}
				return;
			}
		});
}

const gestionar_solicitud = (estado, id, title, text = `Tener en cuenta que no podrá revertir esta acción, si desea continuar debe presionar la opción de 'Si, Entiendo'`, id_alt = '') => {
	swal({
		title,
		text,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Entiendo!",
		cancelButtonText: "No, Regresar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				terminar_gestionar_solicitud(estado, id, '', id_alt);
			}
		});
}

const gestionar_solicitud_texto = (estado, id, title = '', msj = 'Justificación') => {

	swal({
		title,
		text: "",
		type: "input",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Aceptar!",
		cancelButtonText: "Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true,
		inputPlaceholder: `Ingrese ${msj}`
	}, async function (mensaje) {

		if (mensaje === false)
			return false;
		if (mensaje === "") {
			swal.showInputError(`Debe Ingresar el/la ${msj}`);
		} else {
			if (estado == 'Tras_Vis') {
				let { mensaje: msj_resp, tipo, titulo, persona } = await buscar_persona_where(mensaje);
				if (tipo == 'success') {
					let { id_persona, nombre_completo, nombre, correo } = persona;
					data_solicitante = { 'nombre': nombre_completo, correo };
					gestionar_solicitud(estado, id, `ENVIAR A ${nombre} .?`, `Tener en cuenta que el traslado sera enviado al usuario ${nombre_completo} para su respectiva aprobación. Si desea continuar debe presionar la opción de 'Si, Entiendo'`, id_persona);
				} else {
					MensajeConClase(msj_resp, tipo, titulo);
				}
			} else terminar_gestionar_solicitud(estado, id, mensaje);
			return false;
		}
	});
}

const terminar_gestionar_solicitud = (estado, id, mensaje = '', id_alt = '') => {
	console.log(id_alt);
	let mensaje_send = mensaje;
	let url = `${server_app}index.php/presupuesto_control/gestionar_solicitud`;
	let data = {
		estado,
		id,
		mensaje,
		id_alt
	};
	consulta_ajax(url, data, (resp) => {
		let {
			mensaje,
			tipo,
			titulo,
			traslado
		} = resp;
		if (tipo == "sin_session") {
			close();
		} else if (tipo == 'success') {
			swal.close();
			listar_traslados_solicitudes();
			$("#modal_gestionar_solicitud").modal('hide');
			$("#modal_detalle_traslado_comite").modal('hide');
			enviar_correo_estado(estado, traslado.id_solicitud, mensaje_send);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}

	});
}
const administrar_modulo = (tipo, parametro = '') => {
	adm_activo = {
		tipo,
		parametro,
		'valor_parametro': null
	};
	$("#container_costo").html('');
	$("#container_costo_modi").html('');
	if (tipo == 'comite') {
		listar_comites();
		$("#container_admin_comite").fadeIn(1000);
		$("#container_admin_valores").css("display", "none");
	} else if (tipo == 'ordenes') {
		pintar_html("#container_costo");
		pintar_html("#container_costo_modi", 'btn_modificar_costo');
		$("#container_admin_valores").css("display", "none");
		listar_valores_parametros(parametro);
		$("#modal_nuevo_valor .modal-title").html('<span class="fa fa-pencil-square-o"></span> Nueva Orden');
		$("#ModalModificarParametro .modal-title").html('<span class="fa fa-pencil-square-o"></span> Modificar Orden');
		$("#nombre_tabla_cu_or").html('TABLA ORDENES SAP');
		$("#container_admin_valores").fadeIn(1000);
		$("#container_admin_comite").css("display", "none");
	} else if (tipo == 'cuentas') {
		$("#container_admin_valores").css("display", "none");
		listar_valores_parametros(parametro);
		$("#modal_nuevo_valor .modal-title").html('<span class="fa fa-fax "></span> Nueva Cuenta');
		$("#ModalModificarParametro .modal-title").html('<span class="fa fa-fax "></span> Modificar Cuenta');
		$("#nombre_tabla_cu_or").html('TABLA CUENTAS');
		$("#container_admin_valores").fadeIn(1000);
		$("#container_admin_comite").css("display", "none");
	} else if (tipo == 'presupuestos') {
		$("#container_admin_valores").css("display", "none");
		listar_valores_parametros(parametro);
		$("#modal_nuevo_valor .modal-title").html('<span class="fa fa-calendar"></span> Nuevo Año Traslado');
		$("#ModalModificarParametro .modal-title").html('<span class="fa fa-calendar"></span> Modificar Año Traslado');
		$("#nombre_tabla_cu_or").html('TABLA AÑOS TRASLADOS ACTIVOS');
		$("#container_admin_valores").fadeIn(1000);
		$("#container_admin_comite").css("display", "none");
	} else if (tipo == 'costo') {
		$("#container_admin_valores").css("display", "none");
		listar_valores_parametros(parametro);
		$("#modal_nuevo_valor .modal-title").html('<span class="fa fa-sitemap"></span> Nuevo Centro Costo');
		$("#ModalModificarParametro .modal-title").html('<span class="fa fa-sitemap"></span> Modificar Centro Costo');
		$("#nombre_tabla_cu_or").html('TABLA CENTRO DE COSTO');
		$("#container_admin_valores").fadeIn(1000);
		$("#container_admin_comite").css("display", "none");
	}

}

const guardar_valor_parametro = () => {
	let url = `${server_app}index.php/genericas_control/guardar_valor_Parametro`;
	let data = new FormData(document.getElementById("form_guardar_valor_parametro"));
	data.append("idparametro", adm_activo.parametro);
	data.append("valory", orden_centro.id);
	enviar_formulario(url, data, (resp) => {
		if (resp == "sin_session") {
			close();
		} else if (resp == 1) {
			MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
		} else if (resp == 2) {
			$("#form_guardar_valor_parametro").get(0).reset();
			MensajeConClase("", "success", "Datos Guardados!");
			listar_valores_parametros(adm_activo.parametro);
		} else if (resp == 3) {
			MensajeConClase("El Nombre que desea guardar ya esta en el sistema", "info", "Oops...");
		} else if (resp == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al Guardar la información, contacte con el administrador.", "error", "Oops...");
		}
	})
}

const listar_valores_parametros = idparametro => {
	$('#tabla_valores_parametros tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)');
	let myTable = $("#tabla_valores_parametros").DataTable({
		"destroy": true,
		"ajax": {
			url: `${server_app}index.php/genericas_control/Cargar_valor_Parametros/true/2`,
			dataType: "json",
			type: "post",
			data: {
				idparametro
			},
			"dataSrc": function (json) {
				return json.length == 0 ? Array() : json.data;
			},
		},
		"processing": true,
		"columns": [{

			"render": function (data, type, full, meta) {
				return `<span  style="background-color:white;color: black; width: 100%; ;" class="pointer form-control" ><span >ver</span></span>`;
			}
		}, {
			"data": "valor"
		},
		{
			"data": "valorx"
		},
		{
			"data": "op"
		},
		],
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": []
	});

	$('#tabla_valores_parametros tbody').on('click', 'tr', function () {
		let data = myTable.row(this).data();
		$("#tabla_valores_parametros tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		adm_activo.valor_parametro = data.id;
	});
	$('#tabla_valores_parametros tbody').on('dblclick', 'tr', function () {
		let data = myTable.row(this).data();
		ver_detalle_parametro(data);
	});

	$('#tabla_valores_parametros tbody').on('click', 'tr td:nth-of-type(1)', function () {
		let data = myTable.row($(this).parent()).data();
		ver_detalle_parametro(data);
	});

}

const modificar_valor_parametro = () => {
	let url = `${server_app}index.php/genericas_control/Modificar_valor_Parametro`;
	let data = new FormData(document.getElementById("form_modificar_valor_parametro"));
	data.append("idparametro", adm_activo.valor_parametro);
	data.append("valory", orden_centro.id);
	enviar_formulario(url, data, (resp) => {
		if (resp == "sin_session") {
			close();
		} else if (resp == 1) {
			$("#form_modificar_valor_parametro").get(0).reset();
			$("#ModalModificarParametro").modal("hide");
			MensajeConClase("", "success", "Datos Modificados!");
			listar_valores_parametros(adm_activo.parametro);
		} else if (resp == 2) {
			MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
		} else if (resp == 3) {
			MensajeConClase("El Nombre que desea guardar ya esta en el sistema", "info", "Oops...");
		} else if (resp == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al Modificar la información, contacte con el administrador.", "error", "Oops...");
		}
	})
}

const confirmar_eliminar_parametro = (id, estado) => {

	swal({
		title: "Estas Seguro .. ?",
		text: "Tener en cuenta que al Eliminar este valor no estara disponible en las solicitudes de presupuesto, si desea continuar debe presionar la opción de 'Si, Entiendo'.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Entiendo!",
		cancelButtonText: "No, cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		isConfirm => {
			if (isConfirm) {
				eliminar_parametro(id, estado);
			}
		});
}

function eliminar_parametro(idparametro, estado) {
	let url = `${server_app}index.php/genericas_control/cambio_estado_parametro`;
	let data = {
		idparametro,
		estado
	};
	consulta_ajax(url, data, (resp) => {
		if (resp == "sin_session") {
			close();
		} else if (resp == 1) {
			//MensajeConClase("", "success", "Dato Eliminado!");
			swal.close();
			listar_valores_parametros(adm_activo.parametro);
		} else if (resp == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al eliminar la información, contacte con el administrador.", "error", "Oops...");
		}
	})
}

const listar_comites = (tipo = 1) => {
	$('#tabla_comite tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)');
	let columns = [{
		"data": "codigo"
	},
	{
		"data": "nombre"
	},
	{
		"data": "descripcion"
	},
	{
		"data": "solicitudes"
	},
	{
		"data": "estado_alt"
	},
	{
		"data": "gestion"
	},
	];
	tipo == 2 ? columns.splice(5, 1) : '';
	let myTable = $("#tabla_comite").DataTable({
		"destroy": true,
		"ajax": {
			url: `${server_app}index.php/presupuesto_control/listar_comites`,
			dataType: "json",
			type: "post",
			"dataSrc": json => {
				return json.length == 0 ? Array() : json.data;
			},
		},
		"processing": true,
		columns,
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": []
	});

	$('#tabla_comite tbody').on('click', 'tr', function () {
		let data = myTable.row(this).data();
		id_comite = data.id;
		data_comite = data;
		$("#tabla_comite tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});

	$('#tabla_comite tbody').on('dblclick', 'tr', function () {
		let data = myTable.row(this).data();
		listar_traslados_por_comite(data.id, tipo);
		listar_comentarios_comite(data.id);
	});

	$('#tabla_comite tbody').on('click', 'tr td:nth-of-type(1)', function () {
		const data = myTable.row($(this).parent()).data();
		listar_traslados_por_comite(data.id, tipo);
		listar_comentarios_comite(data.id);
	});

}
const abrir_modal_gestionar_solicitud = (id, estado_actual) => {
	obtener_estados(estado_actual);
	$("#modal_gestionar_solicitud").modal();
	id_traslado_sele = id;
}

const mostrar_estados_disponibles = (estado_actual, estados) => {
	let disponibles = [];
	let resp = [];
	//if (estado_actual == 'Tras_Pros') disponibles = ['Tras_Apro', 'Tras_Neg', 'Tras_Com', 'Tras_Pros', 'Tras_Vis',];
	if (estado_actual == 'Tras_Soli') disponibles = ['Tras_Vis',];
	else if (estado_actual == 'Tras_Com') disponibles = ['Tras_Apro', 'Tras_Neg'];
	disponibles.forEach(dis => {
		estados.forEach(estado => {
			if (estado.id_aux == dis) resp.push(estado);
		});
	});
	pintar_datos_combo(resp, "#form_gestionar_solicitud select[name='estado']", 'Seleccione Estado');
}

const obtener_estados = estado_actual => {
	let url = `${server_app}index.php/genericas_control/obtener_valores_parametro`;
	let data = {
		'idparametro': 48
	}
	consulta_ajax(url, data, (resp) => {
		if (resp == "sin_session") {
			close();
		} else {
			mostrar_estados_disponibles(estado_actual, resp);
		}
	})

};
const pintar_datos_combo = (datos, combo, mensaje) => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach(elemento => {
		$(combo).append(`<option value='${elemento.id_aux}'> ${elemento.valor}</option>`);
	});

}

const listar_traslados_por_comite = (id_comite, tipo, modal = 'si') => {
	$(`#tabla_traslados_comite tbody`).off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
	const myTable = $(`#tabla_traslados_comite`).DataTable({
		"destroy": true,
		"pageLength": 100,
		"processing": true,
		"ajax": {
			url: `${server_app}index.php/presupuesto_control/listar_traslados_por_comite`,
			dataType: "json",
			type: "post",
			data: {
				id_comite,
				tipo
			},
			"dataSrc": json => {
				return json.length == 0 ? Array() : json.data;
			},
		},
		columns: [{
			data: 'ver'
		}, {
			data: 'departamento'
		}, {
			data: 'nombre_orden_origen'
		}, {
			data: 'nombre_orden_destino'
		},
		{
			data: 'justificacion_comite'
		},
		{
			data: 'valor_format'
		},
		{
			data: 'aprobados'
		},
		{
			data: 'negados'
		},
		{
			data: 'gestion'
		}

		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_traslados_comite tbody').on('click', 'tr', function () {
		$("#tabla_traslados_comite tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});
	$('#tabla_traslados_comite tbody').on('dblclick', 'tr', function () {
		let data = myTable.row($(this)).data();
		ver_detalle_traslados(data, '#tabla_detalle_traslado_comite', '#modal_detalle_traslado_comite');
		listar_aprobados_traslado_comite(data.id);
		if (administra) activar_evento_mod_valor(data.id, data.id_estado_traslado);
	});

	$('#tabla_traslados_comite tbody').on('click', 'tr td:nth-of-type(1)', function () {
		let data = myTable.row($(this).parent()).data();
		ver_detalle_traslados(data, '#tabla_detalle_traslado_comite', '#modal_detalle_traslado_comite');
		listar_aprobados_traslado_comite(data.id);
		if (administra) activar_evento_mod_valor(data.id, data.id_estado_traslado);
	});
	modal == 'si' ? $("#modal_solicitudes_comite").modal() : '';
}

const aprobar_revertir_traslado_comite = (id_traslado, estado, tipo_ap = '', id = 0) => {
	swal({
		title: estado == 1 ? tipo_ap == 'Aprobado' ? 'Aprobar Traslado .?' : 'Negar Traslado .?' : 'Revertir aprobado .?',
		text: "si desea continuar debe presionar la opción de 'Si, Aceptar'.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Aceptar!",
		cancelButtonText: "No, cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		isConfirm => {
			if (isConfirm) {
				let url = `${server_app}index.php/presupuesto_control/aprobar_revertir_traslado_comite`;
				let data = {
					id_traslado,
					estado,
					id,
					tipo_ap
				};
				consulta_ajax(url, data, (resp) => {
					let {
						mensaje,
						tipo,
						titulo,
					} = resp;
					if (tipo == "sin_session") {
						close();
					} else if (tipo == 'success') {
						listar_traslados_por_comite(id_comite, 2);
						swal.close();
					} else {
						MensajeConClase(mensaje, tipo, titulo);
					}
				})
			}
		});
}

const listar_aprobados_traslado_comite = id_traslado => {
	const myTable = $(`#tabla_aprobados_traslados`).DataTable({
		"destroy": true,
		"processing": true,
		"ajax": {
			url: `${server_app}index.php/presupuesto_control/listar_aprobados_traslado_comite`,
			dataType: "json",
			type: "post",
			data: {
				id_traslado
			},
			"dataSrc": json => {
				return json.length == 0 ? Array() : json.data;
			},
		},
		columns: [{
			data: 'tipo'
		}, {
			data: 'nombre'
		}, {
			data: 'fecha_registra'
		}],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [],
	});

}

const modificar_valor_aprobado = (id_traslado) => {
	swal({
		title: 'Nuevo Valor aprobado.!',
		text: "",
		type: "input",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Aceptar!",
		cancelButtonText: "Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true,
		inputPlaceholder: "Ingrese valor"
	}, function (valor) {

		if (valor === false) {
			return false;
		} else if (valor === "") {
			swal.showInputError("Debe Ingresar el valor.!");
		} else if (isNaN(valor)) {
			swal.showInputError("Debe Ingresar solo numeros en el valor.!");
		} else if (valor < 1) {
			swal.showInputError("Debe Ingresar solo numeros mayores a 0 en el valor.!");
		} else {
			let url = `${server_app}index.php/presupuesto_control/modificar_valor_aprobado`;
			let data = {
				id_traslado,
				valor
			};
			consulta_ajax(url, data, (resp) => {
				let {
					mensaje,
					tipo,
					titulo,
				} = resp;
				if (tipo == "sin_session") {
					close();
				} else if (tipo == 'success') {
					swal.close();
					$("table .valor").html(valor);
					listar_traslados_solicitudes();
					listar_traslados_por_comite(id_comite, 1, 'no');
					$("#modal_detalle_traslado_comite").modal('hide');
				} else {
					MensajeConClase(mensaje, tipo, titulo);
				}
			})
			return false;
		}
	});

}

const obtener_filtros = () => {
	let estado = $("#estado_filtro").val();
	let fecha = $("#fecha_filtro").val();
	(estado.length != 0 || fecha.length != 0) ? $(".mensaje-filtro").show("fast") : $(".mensaje-filtro").css("display", "none");
	estado = `%${estado}%`
	fecha = `%${fecha}%`
	return {
		estado,
		fecha,
	}
}

const obtener_anos_activos = (tipo = 'add', id) => {
	let url = `${server_app}index.php/genericas_control/obtener_valores_parametro`;
	let data = {
		'idparametro': 49
	}
	consulta_ajax(url, data, (resp) => {
		let sw = false;
		if (resp == "sin_session") {
			close();
		} else if (resp.length == 0 && tipo == 'add') {
			$(".cbx_anos").html(`<option value=''> Seleccione año del traslado</option>`).show();
			MensajeConClase("No se encontró ningún año activo para los traslados, contacte con el administrador del modulo.", "info", "Oops.!");
		} else {
			$(".cbx_anos").html(``);
			if (tipo != 'add') {
				resp.forEach(elemento => {
					if (elemento.id == id) {
						sw = true;
					}
				});
			}
			if (resp.length > 1 || sw) $(".cbx_anos").html(`<option value=''> Seleccione año del traslado</option>`).show();
			else $(".cbx_anos").hide();
			resp.forEach(elemento => {
				$(".cbx_anos").append(`<option value='${elemento.id}'> ${elemento.valor}</option>`);
			});
			if (!sw) obtener_ano_inactivo(id);
			if (tipo != 'add' && sw) $(".cbx_anos").val(id);;
		}
	})

};

const obtener_ano_inactivo = (id) => {
	let url = `${server_app}index.php/genericas_control/obtener_valor_parametro_id`;
	let data = {
		'idparametro': id
	}
	consulta_ajax(url, data, (resp) => {
		resp.forEach(elemento => {
			$(".cbx_anos").append(`<option value='${elemento.id}'> ${elemento.valor}</option>`);
		});
		$(".cbx_anos").val(id);
	})

};

const abrir_traslado_comite = (data) => {
	let {
		id,
		id_estado_traslado
	} = data;
	ver_detalle_traslados(data, '#tabla_detalle_traslado_comite', '#modal_detalle_traslado_comite', 1);
	listar_aprobados_traslado_comite(id);
	activar_evento_mod_valor(id, id_estado_traslado);

}

const activar_evento_mod_valor = (id, id_estado_traslado) => {
	if (id_estado_traslado == 'Tras_Com') {
		$("#editar_valor_traslado").show('fast');
		$("#editar_valor_traslado").click(() => {
			modificar_valor_aprobado(id);

		});
		$("#modal_detalle_traslado_comite .modal-footer").html(`<button type="button" class="btn btn-danger active" id="btn_terminar_comite"><span class="glyphicon glyphicon-ok"></span> Terminar</button> <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`);
		$("#btn_terminar_comite").click(() => {
			abrir_modal_gestionar_solicitud(id, id_estado_traslado);
		});
	} else {
		$("#editar_valor_traslado").hide('fast');
		$("#modal_detalle_traslado_comite .modal-footer").html(`<button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`);
	}
}

const administrar_tipo_traslado = (tipo) => {
	if (accion == 'add') limpiar_array_traslado(tipo);

	const config_requerido = tipo => {
		if (tipo == 'OO' || tipo == 'CC' || tipo == 'OC' || tipo == 'CO' || tipo == 'CD' || tipo == 'OD') {
			$("#cont_orden_origen").show("fast");
			$("#cont_cuenta_origen").show("fast");
			$("#cont_orden_origen input").attr("required", "true");
			$("#cont_cuenta_origen input").attr("required", "true");
		} else {
			$("#cont_orden_origen").hide("fast");
			$("#cont_cuenta_origen").hide("fast");
			$("#cont_orden_origen input").removeAttr("required", "true");
			$("#cont_cuenta_origen input").removeAttr("required", "true");
		}

		if (tipo == 'OO' || tipo == 'CC' || tipo == 'OC' || tipo == 'CO' || tipo == 'CA' || tipo == 'OA') {
			$("#cont_orden_destino").show("fast");
			$("#cont_cuenta_destino").show("fast");
			$("#cont_orden_destino input").attr("required", "true");
			$("#cont_cuenta_destino input").attr("required", "true");
		} else {
			$("#cont_orden_destino").hide("fast");
			$("#cont_cuenta_destino").hide("fast");
			$("#cont_orden_destino input").removeAttr("required", "true");
			$("#cont_cuenta_destino input").removeAttr("required", "true");
		}
	}
	config_requerido(tipo);
	if (tipo == 'OO') {
		buscar_origen = 25;
		buscar_destino = 25;

		$("#btn_orden_origen").html(`<span class='red fa fa-search'></span> Orden Origen`);
		$("#btn_orden_destino").html(`<span class='red fa fa-search'></span> Orden Destino`);

	} else if (tipo == 'CC') {
		buscar_origen = 51;
		buscar_destino = 51;

		$("#btn_orden_origen").html(`<span class='red fa fa-search'></span> Centro Origen`);
		$("#btn_orden_destino").html(`<span class='red fa fa-search'></span> Centro Destino`);

	} else if (tipo == 'OC') {
		buscar_origen = 25;
		buscar_destino = 51;

		$("#btn_orden_origen").html(`<span class='red fa fa-search'></span> Orden Origen`);
		$("#btn_orden_destino").html(`<span class='red fa fa-search'></span> Centro Destino`);

	} else if (tipo == 'CO') {
		buscar_origen = 51;

		$("#btn_orden_origen").html(`<span class='red fa fa-search'></span> Centro Origen`);
		$("#btn_orden_destino").html(`<span class='red fa fa-search'></span> Orden Destino`);

	} else if (tipo == 'CA') {
		buscar_origen = 0;
		buscar_destino = 51;

		$("#btn_orden_destino").html(`<span class='red fa fa-search'></span> Centro Destino`);
	} else if (tipo == 'OA') {
		buscar_origen = 0;
		buscar_destino = 25;

		$("#btn_orden_destino").html(`<span class='red fa fa-search'></span> Orden Destino`);
	} else if (tipo == 'CD') {
		buscar_origen = 51;
		buscar_destino = 0;

		$("#btn_orden_origen").html(`<span class='red fa fa-search'></span> Centro Origen`);
	} else if (tipo == 'OD') {
		buscar_origen = 25;
		buscar_destino = 0;

		$("#btn_orden_origen").html(`<span class='red fa fa-search'></span> Orden Origen`);
	} else {
		MensajeConClase('Tipo de traslado invalido', 'info', 'Oops.!');
	}
	$("#tipo_traslado").val(tipo);
}

const obtener_permisos = adm => {
	administra = adm ? true : false;
}

const obtener_nombre_tipo_traslado = tipo => {
	let resp = tipos_traslados.find(element => { return element.id_aux == tipo; });
	return resp;
}

const listar_tipos_traslados = () => {
	/*if (!administra) {
		tipos_traslados = [
			{ 'id_aux': 'OO', 'valor': 'Traslado de Orden a Orden' },
			{ 'id_aux': 'CA', 'valor': 'Adición a Centro costo' },
			{ 'id_aux': 'OA', 'valor': 'Adición a Orden' },
		];
	}*/
	pintar_datos_combo(tipos_traslados, '#tipo_traslado', 'Seleccione Tipo');
}
const pintar_html = (contenedor, btn = 'btn_centro_costo') => {
	$(contenedor).html(`
<div class="input-group margin1">
	<input type="text" class="form-control sin_margin sin_focus txt_nombre_centro" placeholder=""  required="true">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" id="${btn}" style='width:120px'><span	class='red fa fa-search'></span> Centro Costro</button>
	</span>
</div>`);
	$(".txt_nombre_centro").focus(function () {
		$(this).blur()
	});
	$(`#${btn}`).click(() => {
		busqueda_orden_nuevo = 'costo';
		configurar_busqueda_codigo('');
		$("#modal_buscar_codigo").modal();
	});
}

const mostrar_parametro_modificar = async (buscar) => {
	let data = await buscar_parametro_id(buscar);
	let { valory, valor, valorx, id, idparametro, relacion } = data[0];
	if (idparametro == 25) {
		orden_centro.id = valory;
		orden_centro.nombre = relacion;
		$(".txt_nombre_centro").val(relacion);
	}

	$("#txtValor_modificar").val(valor);
	$("#txtDescripcion_modificar").val(valorx);
	$("#ModalModificarParametro").modal();
}

const enviar_correo_estado = async (estado, id, motivo) => {
	let sw = false;
	let { nombre, correo } = data_solicitante;
	let ser = `<a href="${server}index.php/presupuesto/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = -1;
	let titulo = 'Solicitud de Presupuesto';
	let mensaje = `Se informa que los traslados presupuestales realizados por usted,  fueron recibidos y se encuentran en proceso de verificaci&oacuten. A partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentran sus traslados.<br><br>M&aacutes informaci&oacuten en:${ser}`;
	if (estado == 'Tras_Neg') {
		sw = true;
		tipo = 1;
		mensaje = `Se informa que su traslado presupuestal ha sido negado por no cumple con los siguientes requisitos ${motivo} para recepci&oacuten de la misma.<br><br>M&aacutes informaci&oacuten en: ${ser}`;
	} else if (estado == 'Tras_Apro') {
		sw = true;
		tipo = 1;
		mensaje = `Se informa que su traslado presupuestal fue aprobado.<br><br>M&aacutes informaci&oacuten en: ${ser}`;
	} else if (estado == 'Tras_Com') {
		ser = `<a href="${server}index.php/comite_presupuesto/${id}"><b>agil.cuc.edu.co</b></a>`;
		sw = true;
		tipo = 3;
		nombre = 'Comit&eacute; Presupuesto';
		mensaje = `Se informa que un nuevo traslado presupuestal se encuentra en comit&eacute; puede validar la informaci&oacute;n en ${ser}`;
		correo = await obtener_correos_comite();
	} else if (estado == 'Tras_Vis') {
		sw = true;
		tipo = 1;
		mensaje = `Se informa que se ha realizado un traslado que requiere de su visto bueno, por favor ingrese al aplicativo AGIL para dar el AVAL de la solicitud.<br><br>M&aacutes informaci&oacuten en: ${ser}`;
	} else if (estado == 'Tras_Soli') {
		sw = true;
	}
	if (sw) enviar_correo_personalizado("presu", mensaje, correo, nombre, "Traslados Presupuesto CUC", titulo, "ParCodAdm", tipo);
}
const obtener_correos_comite = () => {
	return new Promise(resolve => {
		let url = `${server_app}index.php/presupuesto_control/obtener_correos_comite`;
		consulta_ajax(url, '', (resp) => {
			resolve(resp);
		});
	});
}
const crear_acta_comite = async (data) => {
	let { id, nombre, fecha_inicio, fecha_fin, ano } = data;
	let imprimir = document.querySelector("#acta_comite");
	let personas = await obtener_correos_comite();
	let traslados = await obtener_traslados_comite(id);

	$("#nombre_pri").html(nombre.toUpperCase());
	$("#ano_pri").html(ano);
	$("#fecha_inicio_pri").html(fecha_inicio);
	$("#fecha_fin_pri").html(fecha_fin);

	$("#tabla_miembros tbody").html('').append(`<tr><td rowspan='${personas.length + 1}'>MIEMBROS: </td></tr> `);
	personas.map((elemento) => {
		let { persona, departamento } = elemento;
		$("#tabla_miembros tbody").append(`<tr><td>${persona} - ${departamento}</td></tr> `);
	})

	$("#tabla_traslados_pri tbody").html('');
	traslados.map((elemento, index) => {
		let { nombre_orden_origen, departamento, valor, valor_aprobado, justificacion_comite, id_estado_traslado, estado_traslado, nombre_orden_destino } = elemento;
		valor_aprobado = id_estado_traslado == 'Tras_Apro' ? valor_aprobado : 0;
		valor = parseFloat(`${valor}.`).toLocaleString();
		valor_aprobado = valor_aprobado == null ? null : parseFloat(`${valor_aprobado}.`).toLocaleString();
		$("#tabla_traslados_pri tbody").append(`<tr><td>${index + 1}</td><td>${departamento}</td><td>${nombre_orden_origen}</td><td>${nombre_orden_destino}</td><td>${valor}</td><td>${justificacion_comite}</td><td>${valor_aprobado == null ? valor : valor_aprobado}</td><td>${estado_traslado}</td></tr> `);
	})

	imprimirDIV(imprimir);
}
const obtener_traslados_comite = (id_comite) => {
	return new Promise(resolve => {
		let url = `${server_app}index.php/presupuesto_control/obtener_traslados_comite`;
		consulta_ajax(url, { id_comite }, (resp) => {
			resolve(resp);
		});
	});
}

const abrir_traslados_comite_comentario = (id) => {
	listar_traslados_por_comite(id, 2, 'si');
	listar_comentarios_comite(id);
}

const number_sin_punto = () => {
	const number = document.querySelector('.valor_sin_punto');

	const formatNumber = (n) => {
		n = String(n).replace(/\D/g, "");
		return n === '' ? n : Number(n).toLocaleString();
	}

	return number.addEventListener('keyup', (e) => {
		const element = e.target;
		const value = element.value;
		element.value = formatNumber(value);
	});
}
