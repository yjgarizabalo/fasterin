let {
	departamentos,
	departamento_sele,
} = {
	"departamentos": [],
	"departamento_sele": 0,
};
let foto_requerida = false;
let tomo_foto = false;
let id_evento = '';
let server_app = "localhost";
let id_participante = '';
let tiene_camara = true;
let ruta_fotos = `archivos_adjuntos/visitas/fotos_visitantes`;
let ruta_firmas = `archivos_adjuntos/visitas/firmas`;
let data_visitante = null;
let callback_activo_add = (resp) => { };
let continuar_ingreso_sancion = () => { };
let continuar_ingreso_visita = () => { };
let action = '';
let idvisitante = '';
$(document).ready(() => {
	server_app = Traer_Server();
	$("#codigo_departamento").val("").focus();

	$("#form_registrar_visitante").submit(() => {
		guardar_datos_visitante((resp) => {
			callback_activo_add(resp)
		});
		return false;
	});
	$("#form_agregar_nuevo_participante").submit(() => {
		guardar_participante_evento(id_participante, id_evento);
		return false;
	});
	$("#form_agregar_nuevo_participante_auto").submit(() => {
		auto_guardar_participante_evento();
		return false;
	});

	$("#form_filtrar_eventos").submit(() => {
		let fecha_inicio = $("#form_filtrar_eventos input[name='fecha_inicio']").val();
		let fecha_fin = $("#form_filtrar_eventos input[name='fecha_fin']").val();
		if (fecha_inicio.length == 0 || fecha_fin.length == 0) {
			MensajeConClase("Seleccione Fecha Inicio y fecha de fin", 'info', 'Oops.!');
		} else {
			listar_eventos();
		}
		return false;
	});
	$("#formato_horas").click(() => {
		if ($("#formato_horas").is(':checked')) {
			$("#form_consulta_ingresos input[type='date']").attr("type", "datetime-local");
			$("#todas_visitas").prop('checked', false);
			$("#fecha_inicial_consulta").show("slow");
		} else {
			$("#form_consulta_ingresos input[type='datetime-local']").attr("type", "date");
		}
	});
	$("#tipo_persona").click(() => {
		let tipo = $("#tipo_persona").val();
		if (tipo == 'PerEst') $("#datos_adicionales .req").attr("required", "true").show("slow");
		else $("#datos_adicionales .req").removeAttr("required", "true").hide("slow");
	});
	$("#con_cupos").click(() => {
		if ($("#con_cupos").is(':checked')) {
			$("#form_registrar_evento input[name='cupos']").attr("required", "true").show('slow').val('');
		} else {
			$("#form_registrar_evento input[name='cupos']").removeAttr("required", "true").hide('slow').val('');
		}
	});
	$("#con_vehiculo").click(() => {
		if ($("#con_vehiculo").is(':checked')) {
			$(".dato_vehi").show('slow');
			$(".dato_vehi input").attr("required", "true").val('');
		} else {
			$(".dato_vehi").hide('slow');
			$(".dato_vehi input").removeAttr("required", "true").val('');
		}
	});
	$("#con_cupos_modi").click(() => {
		if ($("#con_cupos_modi").is(':checked')) {
			$("#form_modificar_evento input[name='cupos']").attr("required", "true").show('slow').val('');
		} else {
			$("#form_modificar_evento input[name='cupos']").removeAttr("required", "true").hide('slow').val('');
		}
	});

	$("#entre_fechas").click(() => {
		if ($("#entre_fechas").is(':checked')) {
			$("#fecha_final_consulta").show("slow");
			$("#todas_visitas").prop('checked', false);
			$("#fecha_inicial_consulta").show("slow");
		} else {
			$("#fecha_final_consulta").hide("slow");
		}
	});
	$("#btn_modificar_evento").click(() => {
		if (id_evento == '') {
			MensajeConClase("Seleccione Evento a modificar.", "info", "Oops.!");
		} else {
			buscar_evento(id_evento);
		}
	});
	$("#btn_buscar_participante").click(() => {
		buscar_participante_evento();
	});
	$("#btn_nuevo_participante").click(() => {
		reiniciar_form_visitantes();
		action = 'guardar_datos_visitante';
		$("#modal_registrar_visitante").modal();
	});
	$("#txt_buscar_participante").keypress(e => {
		const code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) {
			buscar_participante_evento();
			return false;
		}
	});

	$("#todas_visitas").click(() => {
		if ($("#todas_visitas").is(':checked')) {
			$("#identificacion_consulta").show("slow");
			$("#fecha_final_consulta").hide("slow");
			$("#fecha_inicial_consulta").hide("slow");
			$("#form_consulta_ingresos input[type='datetime-local']").attr("type", "date");
			$("#entre_fechas").prop('checked', false);
			$("#formato_horas").prop('checked', false);
			$("#todas_visitas_general").prop('checked', false);
		} else {
			$("#fecha_inicial_consulta").show("slow");
		}
	});

	$("#todas_visitas_general").click(() => {
		if ($("#todas_visitas_general").is(':checked')) {
			$("#identificacion_consulta").hide("slow");
			$("#fecha_inicial_consulta").show("slow");
			$("#todas_visitas").prop('checked', false);
		} else {
			$("#identificacion_consulta").show("slow");
		}
	});

	$("#form_consulta_ingresos").submit(() => {
		let identificacion = $("#identificacion_consulta").val();
		let fecha_inicial = $("#fecha_inicial_consulta").val();
		let fecha_final = $("#fecha_final_consulta").val();
		let formato = 0;
		let entre_fechas = 0;
		let tipo = 2;
		let sw = true;
		if ($("#todas_visitas").is(':checked')) {
			sw = false;
			formato = 0;
			tipo = 4;
			if (identificacion.length == 0) {
				MensajeConClase("Ingrese Numero de identificación.", "info", "Oops.!");
				return false;
			}
		} else if ($("#todas_visitas_general").is(':checked')) {
			sw = false;
			identificacion = "";
			if (fecha_inicial.length == 0) {
				MensajeConClase("Seleccione Fecha de Inicio.", "info", "Oops.!");
				return false;
			}
		}

		if ($("#entre_fechas").is(':checked')) {
			entre_fechas = 1;
			tipo = 3;
			if (fecha_inicial.length == 0) {
				MensajeConClase("Seleccione Fecha Inicio.", "info", "Oops.!");
				return false;
			} else if (fecha_final.length == 0) {
				MensajeConClase("Seleccione Fecha Final.", "info", "Oops.!");
				return false;
			}

		}
		if ($("#formato_horas").is(':checked')) {
			formato = 1;
		}

		if (fecha_inicial.length == 0 && sw) {
			MensajeConClase("Seleccione Fecha Inicio.", "info", "Oops.!");
			return false;
		} else if (identificacion.length == 0 && sw) {
			MensajeConClase("Ingrese Numero de identificación.", "info", "Oops.!");
			return false;
		}

		listar_ingresos_departamentos(identificacion, tipo, entre_fechas, fecha_inicial, fecha_final, formato);
		return false;
	});

	$("#guardar_visita_departamento").submit(() => {
		let departamento = $("#codigo_departamento").val();
		let identificacion = $("#identificacion_departamento").val();
		if (departamento == 0) {
			traer_ultimo_ingreso_visitante(identificacion);
		} else {
			const resp = departamentos.find(data => data.valory == departamento);
			if (resp) {
				buscar_visitante(resp.id);
			} else {
				MensajeConClase('El Codigo del departamento no se encuentra registrado.', 'info', 'Oops.!')
			};
		}
		return false;
	});
	$("#form_registrar_evento").submit(() => {
		guardar_evento();
		return false;
	});
	$("#form_modificar_evento").submit(() => {
		modificar_evento();
		return false;
	});
	$("#limpiar-filtros-eventos").click(() => {
		$("#form_filtrar_eventos").get(0).reset();
		listar_eventos();
		return false;
	});

	$("#identificacion_departamento").on("keydown", function (event) {
		if (event.which == 16) {
			$("#codigo_departamento").val("").focus();
		}
	});
	$("input[name='identificacion']").on("keydown", function (event) {
		let valor = $(this).val().replace(/^0+/, '');
		$(this).val(valor);
	});

	$("#consultar_ingresos_persona").click(() => {
		listar_ingresos_departamentos("****", 2);
		$("#modal_ingresos_departamentos").modal();
		return;
	});

	$(".regresar_menu").click(function () {
		$("#container-listado-eventos").css("display", "none");
		$("#container_visitas_departamento").css("display", "none");
		$("#container_visitantes").css("display", "none");
		$("#menu_principal").fadeIn(1000);
	});
	$("#listado_departamentos").click(function () {
		administrar_vista(1);
	});
	$("#listado_eventos").click(function () {
		administrar_vista(2);
	});
	$("#agregar_visita").click(function () {
		administrar_vista(3);
	});
	$("#agregar_evento").click(function () {
		administrar_vista(4);
	});
	$("#adm_visitantes").click(function () {
		administrar_vista(5);
	});
	$("#btn_buscar_visitante").click(() => {
		let dato = $("#txt_buscar_visitantes").val().trim();
		dato.length == 0 ? MensajeConClase("Ingrese Dato a Buscar", "info", "Oops...") : listar_visitantes(dato);
	});

	$("#txt_buscar_visitantes").keypress(e => {
		if (e.which == 13) {
			let dato = $("#txt_buscar_visitantes").val().trim();
			dato.length == 0 ? MensajeConClase("Ingrese Dato a Buscar", "info", "Oops...") : listar_visitantes(dato);
		}
	});

	$("#btn_sancionar_visitante").click(() => {
		sancionar_visitante(data_visitante);
	});

	$("#btn_continuar_ingreso_sancion").click(() => {
		continuar_ingreso_sancion();
	});

	$("#agregar_hijo").click(() => {
		listar_participantes("**WEFe+f", (resp) => {
			console.log(resp);
		}, "#tabla_hijos");
		$("#modal_asignar_hijo").modal();
	});

	$("#txt_buscar_hijo").keypress(e => {
		const code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) {
			let dato = $("#txt_buscar_hijo").val();
			listar_participantes(dato, ({ id }) => {
				asignar_hijo(id, id_participante);
			}, "#tabla_hijos");
			return false;
		}
	});

	$("#btn_buscar_hijo").click(() => {
		let dato = $("#txt_buscar_hijo").val();
		listar_participantes(dato, ({ id }) => {
			asignar_hijo(id, id_participante);
		}, "#tabla_hijos");
	});
	$("#eliminar_hijo").click(() => {
		let hijo = $("#form_agregar_nuevo_participante select[name='id_hijo']").val();
		if (hijo) {
			swal({
				title: `¿ Eliminar Hijo ?`,
				text: ``,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Eliminar!",
				cancelButtonText: "No, Cerrar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true
			},
				function (isConfirm) {
					if (isConfirm) {
						asignar_hijo(hijo, id_participante, 'retirado');
					}
				});
		} else MensajeConClase("Seleccione Hijo a retirar", 'info', 'Oops.!');
	});

	$("#form_agregar_nuevo_participante select[name='id_tipo']").change(async function () {
		pintar_datos_combo([], ".cbx_hijos", "Seleccione Hijo");
		let id = $(this).val();
		let select = "#form_agregar_nuevo_participante select[name='id_hijo']";
		let contai = "#container_hijos";
		if (id) {
			let { data } = await obtener_data_tipo_participante(id);
			if (data == 'Tip_Pad') {
				pintar_hijos(id_participante);
				$(select).attr("required", "true")
				$(contai).show();
			} else {
				$(select).removeAttr("required", "true");
				$(contai).hide();
			}
		} else {
			$(contai).hide();
			$(select).removeAttr("required", "true");
		}

	});

	$("#pre_inscripcion").change(function () {
		let id = $(this).val();
		if (id == 2) $(".div_firma").show();
		else $(".div_firma").hide();
	});

	$("#pre_inscripcion_mod").change(function () {
		let id = $(this).val();
		if (id == 2) $(".div_firma_mod").show();
		else $(".div_firma_mod").hide();
	});

	$("#btn_buscar_participantes").click(() => {
		activar_buscar_participante(true);
	});

	$("#btn_visitante").click(() => {
		reiniciar_form_visitantes();
		action = "guardar_datos_visitante";
		$("#modal_registrar_visitante").modal();
	});
});

const listar_departamentos = () => {
	let data = [];
	$('#tabla_departamentos tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)');
	let table = $("#tabla_departamentos").DataTable({
		"destroy": true,
		"ajax": {
			url: `${server_app}index.php/visitas_control/listar_departamentos`,
			dataType: "json",
			type: "post",
			"dataSrc": json => {
				if (json.length == 0) {
					return Array();
				}
				departamentos = json.data;
				return json.data;
			}
		},
		"processing": true,
		"order": [
			[1, "asc"]
		],
		"columns": [{
			"data": "indice"
		},
		{
			"data": "valory"
		},
		{
			"data": "empresa"
		},
		{
			"data": "valor"
		},
		{
			"data": "valorx"
		},
		],
		"language": idioma_tabla(),
		dom: 'Bfrtip',
		"buttons": []
	});

	$('#tabla_departamentos tbody').on('click', 'tr', function () {
		$("#tabla_departamentos tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});

	$('#tabla_departamentos tbody').on('dblclick', 'tr', function () {
		data = table.row(this).data();
		listar_ingresos_departamentos(data.id, 1);
	});

	$('#tabla_departamentos tbody').on('click', 'tr td:nth-of-type(1)', function () {
		data = table.row($(this).parent()).data();
		listar_ingresos_departamentos(data.id, 1);
	});

	$("#form_registrar_visita").submit(() => {
		continuar_ingreso_visita();
		return false;
	});
}

const buscar_visitante = id => {
	let url = `${server_app}index.php/visitas_control/buscar_visitante`;
	let data = new FormData(document.getElementById("guardar_visita_departamento"));
	data.append('id_parametro', id);
	enviar_formulario(url, data, (resp) => {
		let {
			mensaje,
			tipo,
			titulo,
			datos,
			departamento
		} = resp;
		if (tipo == "sin_session") {
			close();
		} else if (tipo == 'si_registrar') {
			tipo = 'info';
			departamento_sele = departamento.id;
			let identificacion = $("#identificacion_departamento").val();
			confirmar_agregar_persona(identificacion, () => {
				reiniciar_form_visitantes();
				llenar_form_agregar_visitante(datos);
				callback_activo_add = resp => {
					guardar_visita_departamento(resp.id, departamento_sele);
					$("#identificacion_departamento").val("");
					$("#codigo_departamento").val("").focus();
					$("#modal_registrar_visitante").modal("hide");
					$("#form_registrar_visitante").get(0).reset();
				}
				action = 'guardar_datos_visitante';
				$("#modal_registrar_visitante").modal();
			});
		} else if (tipo == 'success') {
			departamento_sele = departamento.id;
			confirmar_ingreso_visitante(datos, departamento);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
	return;
}
const reiniciar_form_visitantes = () => {
	$("#form_registrar_visitante").get(0).reset();
	$("#cbxtipoIdentificacion").val("1");
	$("#datos_adicionales .req").removeAttr("required", "true").hide();
	//$("#tipo_persona").val("PerExt");
	if (tiene_camara) {
		let canvas = document.getElementById("foto");
		let ctx = canvas.getContext("2d");
		ctx.clearRect(0, 0, canvas.width, canvas.height);
		tomo_foto = false;
	}
}
const guardar_datos_visitante = (callback) => {
	let url = `${server_app}index.php/visitas_control/${action}`;
	let data = new FormData(document.getElementById("form_registrar_visitante"));
	data.append("departamento", departamento_sele);
	if (idvisitante) { data.append("id_visitante", idvisitante); }
	if (foto_requerida) {
		if (tomo_foto) {
			canvas = document.getElementById("foto");
			let foto = canvas.toDataURL("image/jpeg");
			let info = foto.split(",", 2);
			data.append("foto", info[1]);
		} else {
			MensajeConClase("Antes de continuar debe tomar la foto del visitante.!", 'info', 'Oops.!');
			return;
		}
	}
	enviar_formulario(url, data, (resp) => {
		let {
			mensaje,
			tipo,
			titulo,
			visitante
		} = resp;

		if (mensaje == "sin_session") {
			close();
		} else if (tipo == 'success') {
			callback(visitante);
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_registrar_visitante").get(0).reset();
			if (idvisitante) {
				$("#modal_registrar_visitante").modal("hide");
				let dato = $("#txt_buscar_visitantes").val();
				listar_visitantes(dato);
			}
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}

	});
	return;
}

const guardar_visita_departamento = (visitante, departamento) => {
	let url = `${server_app}index.php/visitas_control/guardar_visita_departamento`;
	let data = {
		"departamento": departamento,
		"visitante": visitante,
	};
	consulta_ajax(url, data, (res) => {
		if (res == "sin_session") {
			close();
		} else if (res == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta operación", "error", "Oops.!");
		} else if (res == 0) {
			//swal.close();
			mostrar_mensaje_visita("Proceso Exitoso.!", "la visita fue registrada con exito.");
			$(".modal").modal('hide');
			//MensajeConClase("la visita fue registrada de forma exitosa.", "success", "Proceso Exitoso.!");
			$("#identificacion_departamento").val("").focus();
		} else {
			MensajeConClase("Error al guardar la visita contacte con el administrador del sistema", "error", "Oops");
		}
	});
	return;
}

const confirmar_ingreso_visitante = (visitante, departamento, tipo = 1, id = -1) => {
	reiniciar_canvas('foto_ingreso');
	let {
		nombre_completo,
		foto,
		identificacion,
		hora_entrada,
		id: id_visitante,
		sancionado,
	} = visitante;

	let {
		id_aux: codigo,
		valor: nombre_departamento,
		idparametro: empresa,
		id: id_departamento,
	} = departamento;
	let menssaje = `${tipo == 1 ? "Marcar Ingreso, se dirige" : "Marcar salida, Ingreso"} ${codigo == 'dep_est' || codigo == 'dep_emp' || codigo == 'dep_emp_cul' || codigo == 'dep_est_cul' ? 'como' : "al departamento de"}  ${nombre_departamento} de la ${empresa == 3 ? 'UNIVERSIDAD DE LA COSTA' : 'CORPORACION UNIVERSITARIA LATINOAMEREICA'}${tipo != 1 ? `, hora ingreso ${hora_entrada}` : ""}.`;
	if (sancionado == 1) mostrar_alerta_sancion(visitante, menssaje);
	else mostrar_modal_ingreso(menssaje, nombre_completo);

	if (tipo == 1) {
		$(".div_camara_ingreso").show("fast");
		$("#foto_salida").css("display", "none");
		continuar_ingreso_visita = async () => {
			let { tipo, mensaje, titulo } = await remplazar_foto(visitante);
			if (tipo == 'success') guardar_visita_departamento(id_visitante, id_departamento);
			else MensajeConClase(mensaje, tipo, titulo);
		}
	} else {
		$(".div_camara_ingreso").css("display", "none");
		$("#foto_salida").show("fast");
		$("#foto_salida td").html(`<img src='${server_app}/${ruta_fotos}/${identificacion}.png'' class="img-responsive img-rounded img-thumbnail">`);
		continuar_ingreso_visita = () => {
			marcar_salida_visitante(id);
		}
	}

}

const mostrar_mensaje_visita = (mensaje, cuerpo, icon = "success.png") => {
	Push.clear();
	Push.create(mensaje, {
		body: cuerpo,
		icon: `${server_app}imagenes/${icon}`,
		timeout: 7000,
		onClick: function () {
			console.log(this);
		}
	});
}


/*
// Muestra los ingresos por departamentos
//
*/
const listar_ingresos_departamentos = (dato, tipo, entre_fechas = 0, fecha_inicial = "", fecha_final = "", formato = 0) => {
	if (tipo != 1) {
		$("#form_consulta_ingresos").show("fast");
	} else {
		$("#form_consulta_ingresos").hide("fast");
	}
	let data = [];
	$('#tabla_ingresos_departamento tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)');
	let table = $("#tabla_ingresos_departamento").DataTable({
		"destroy": true,
		"pageLength": 5,
		"ajax": {
			url: `${server_app}index.php/visitas_control/listar_ingresos_departamentos`,
			dataType: "json",
			type: "post",
			data: {
				dato,
				tipo,
				fecha_inicial,
				fecha_final,
				entre_fechas,
				formato
			},
			"dataSrc": json => {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			}
		},
		"processing": true,
		"columns": [{
			"data": "indice"
		},
		{
			"data": "nombre_completo"
		},
		{
			"data": "identificacion"
		}, {
			"data": "departamento"
		},
		{
			"data": "hora_entrada"
		},
		{
			"data": "hora_salida"
		},
		],
		"language": idioma_tabla(),
		dom: 'Bfrtip',
		"buttons": get_botones(),
	});

	$('#tabla_ingresos_departamento tbody').on('click', 'tr', function () {
		data = table.row(this).data();
		$("#tabla_ingresos_departamento tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});

	$('#tabla_ingresos_departamento tbody').on('dblclick', 'tr', function () {
		data = table.row(this).data();
		ver_datalle_visitante("#datos_perso", data, "#modal_detalle_ingreso_departamento_persona");
	});

	$('#tabla_ingresos_departamento tbody').on('click', 'tr td:nth-of-type(1)', function () {
		data = table.row($(this).parent()).data();
		ver_datalle_visitante("#datos_perso", data, "#modal_detalle_ingreso_departamento_persona");
	});
	$("#modal_ingresos_departamentos").modal();
}

const ver_datalle_visitante = (tabla, datos, modal = '') => {
	let {
		nombre_completo,
		identificacion,
		departamento,
		hora_entrada,
		hora_salida,
		nombre_completo_registra,
		nombre_completo_salida,
		foto
	} = datos;
	$(`${tabla} .nombre_visi`).html(nombre_completo);
	$(`${tabla} .identificacion_visi`).html(identificacion);
	$(`${tabla} .departamento_visi`).html(departamento);
	$(`${tabla} .hora_entrada_visi`).html(hora_entrada)
	$(`${tabla} .hora_salida_visi`).html(hora_salida);
	$(`${tabla} .usuario_registra_visi`).html(nombre_completo_registra);
	$(`${tabla} .fecha_registra_visi`).html(nombre_completo_salida);
	$(`${tabla} .foto_visi`).html(`<img src='${server_app}/${ruta_fotos}/${identificacion}.png' class="img-responsive img-rounded img-thumbnail">`);
	if (modal.length != 0) $(modal).modal();
}
const ver_datalle_evento_asignar = (tabla, datos, modal = '') => {
	let {
		nombre,
		segundo_nombre,
		apellido,
		segundo_apellido,
		identificacion,

		foto
	} = datos;
	$(`${tabla} .nombre_visi`).html(`${nombre} ${apellido} ${segundo_apellido}`);
	$(`${tabla} .foto_visi`).html(`<img src='${server_app}/${ruta_fotos}/${identificacion}.png' class="img-responsive img-rounded img-thumbnail">`);
	if (modal.length != 0) $(modal).modal();
}

const traer_ultimo_ingreso_visitante = identificacion => {

	let url = `${server_app}index.php/visitas_control/traer_ultimo_ingreso_visitante`;
	let data = {
		"identificacion": identificacion,
	};

	consulta_ajax(url, data, (resp) => {
		let {
			mensaje,
			tipo,
			titulo,
			datos,
		} = resp;

		if (tipo == 'success') {
			let {
				id,
				nombre_completo,
				identificacion,
				foto,
				hora_entrada,
				valorx,
				valory,
				valor,
				idparametro
			} = datos;

			let visitante = {
				nombre_completo,
				identificacion,
				foto,
				hora_entrada
			}
			let departamento = {
				valorx,
				valory,
				valor,
				idparametro
			}
			confirmar_ingreso_visitante(visitante, departamento, 2, id);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}

	});

}

const marcar_salida_visitante = id => {
	let url = `${server_app}index.php/visitas_control/marcar_salida_visitante`;
	let data = {
		id
	};
	consulta_ajax(url, data, (res) => {
		if (res == "sin_session") {
			close();
		} else if (res == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta operación", "error", "Oops.!");
		} else if (res == 0) {
			//swal.close();
			$(".modal").modal('hide');
			mostrar_mensaje_visita("Proceso Exitoso.!", "la salida fue marcada con exito.");
			//MensajeConClase("la salida fue registrada de forma exitosa.", "success", "Proceso Exitoso.!");
			$("#identificacion_departamento").val("").focus();
			$("#codigo_departamento").val("");
		}
	});
	return;
}

const configuracion_camara = (perfil, btn = 'botonFoto', camara = 'camara', foto = 'foto') => {
	foto_requerida = perfil == 'Per_Admin_vis' || perfil == 'Per_Admin' ? true : false;
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
	navigator.getUserMedia({
		'audio': false,
		'video': true
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

const administrar_vista = item => {
	if (item == 1) {
		$("#menu_principal").css("display", "none");
		$("#container_visitas_departamento").fadeIn(1000);
		listar_departamentos();
	} else if (item == 2) {
		$("#menu_principal").css("display", "none");
		$("#container-listado-eventos").fadeIn(1000);
		listar_eventos();
	} else if (item == 3) {
		$("#modal_registrar_evento .modal-title").html('<span class="fa fa-street-view"></span> Nueva Visita');
		$("#form_registrar_evento select[name=tipo]").val('Visita');
		$(".div_firma").hide();
		$("#modal_registrar_evento").modal();
	} else if (item == 4) {
		$("#modal_registrar_evento .modal-title").html('<span class="fa fa-calendar"></span> Nuevo Evento');
		$("#form_registrar_evento select[name=tipo]").val('Evento');
		$(".div_firma").hide();
		$("#modal_registrar_evento").modal();
	} else if (item == 5) {
		$("#menu_principal").css("display", "none");
		$("#container_visitantes").fadeIn(1000);
	}
}

const guardar_evento = () => {
	let url = `${server_app}index.php/visitas_control/guardar_evento`;
	let data = new FormData(document.getElementById("form_registrar_evento"));
	enviar_formulario(url, data, (resp) => {
		let {
			mensaje,
			tipo,
			titulo,
			evento
		} = resp;
		if (mensaje == "sin_session") {
			close();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
			if (tipo == 'success') {
				$("#form_registrar_evento input[name='cupos']").removeAttr("required", "true").hide('slow').val('');
				$("#modal_registrar_evento").modal("hide");
				$("#form_registrar_evento").get(0).reset();
				if (evento.tipo == 'Visita') {
					id_evento = evento.id;
					abrir_modal_participantes('evento');
				}
			}

		}
	});
	return;
}

/*
// Muestra los eventos registrados
//
*/
const listar_eventos = () => {
	let data = [];
	id_evento = '';

	let fecha_inicio = $("#form_filtrar_eventos input[name='fecha_inicio']").val();
	let fecha_fin = $("#form_filtrar_eventos input[name='fecha_fin']").val();

	if (fecha_inicio.length == 0 || fecha_fin.length == 0) {
		fecha_inicio = '';
		fecha_fin = '';
		$("#mensaje-filtro-evento").hide();
	} else $("#mensaje-filtro-evento").show();

	$('#tabla-eventos tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)');
	let table = $("#tabla-eventos").DataTable({
		"destroy": true,
		"ajax": {
			url: `${server_app}index.php/visitas_control/listar_eventos`,
			dataType: "json",
			type: "post",
			data: {
				fecha_inicio,
				fecha_fin
			},
			"dataSrc": json => {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			}
		},
		"processing": true,
		"columns": [{
			"data": "indice"
		}, {
			"data": "tipo"
		},
		{
			"data": "nombre"
		},
		{
			"data": "fecha_inicio"
		}, {
			"data": "fecha_fin"
		},
		{
			"data": "ubicacion"
		},
		{
			"data": "nombre_completo"
		},
		{
			"data": "estado"
		},
		{
			"data": "gestion"
		},
		],
		"language": idioma_tabla(),
		dom: 'Bfrtip',
		"buttons": get_botones(),
	});

	$('#tabla-eventos tbody').on('click', 'tr', function () {
		data = table.row(this).data();
		id_evento = data.id;
		$("#descargar_participantes").attr("href", `${Traer_Server()}index.php/visitas/exportar_participantes/${id_evento}`)
		$("#tabla-eventos tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});

	$('#tabla-eventos tbody').on('dblclick', 'tr', function () {
		data = table.row(this).data();
		id_evento = data.id;
		ver_datelle_evento(data);
	});

	$('#tabla-eventos tbody').on('click', 'tr td:nth-of-type(1)', function () {
		data = table.row($(this).parent()).data();
		ver_datelle_evento(data);

	});

}
const ver_datelle_evento = (datos) => {
	let { firma, fecha_vigente_codigo, codigo, nombre, fecha_inicio, fecha_fin, ingreso, cupos, ubicacion, descripcion, nombre_completo, estado, fecha_registro, usuario_elimina, nombre_completo_elimina, fecha_elimina, tipo } = datos;
	tipo == 'Evento' ? $("#modal_detalle_evento .modal-title").html('<span class="fa fa-calendar"></span> Detalle Evento') : $("#modal_detalle_evento .modal-title").html('<span class="fa fa-street-view"></span> Detalle Visita');
	$(".evento_nombre").html(nombre);
	$(".evento_inicio").html(fecha_inicio);
	$(".evento_fin").html(fecha_fin);
	$(".evento_ingreso").html(ingreso)
	$(".evento_cupos").html(cupos == null ? 'ilimitados' : cupos);
	$(".evento_firma").html(firma == null ? 'NO' : 'SI');
	$(".evento_ubicacion").html(ubicacion);
	$(".evento_descripcion").html(descripcion);
	$(".evento_solicitado_por").html(nombre_completo);
	$(".evento_estado").html(estado);
	$(".evento_solicitado_fecha").html(fecha_registro);
	$(".evento_codigo").html(codigo);
	$(".fecha_vence_evento").html(fecha_vigente_codigo);
	codigo ? $("#mostrar_codigo").show() : $("#mostrar_codigo").hide();
	if (usuario_elimina != null) {
		$(".tr_evento_cancelado_por").show("fast");
		$(".evento_cancelado_por").html(nombre_completo_elimina);
		$(".evento_cancelado_fecha").html(fecha_elimina);
	} else {
		$(".tr_evento_cancelado_por").hide("fast");
	}
	pintar_btn_para_eventos("#modal_detalle_evento", 'Participantes');
	$("#modal_detalle_evento").modal();
}

const cambiar_estado_evento = id => {
	let url = `${server_app}index.php/visitas_control/cambiar_estado_evento`;
	let data = {
		id
	};
	consulta_ajax(url, data, (resp) => {
		let {
			mensaje,
			tipo,
			titulo
		} = resp;
		if (mensaje == "sin_session") {
			close();
		} else {
			if (tipo == 'success') {
				swal.close();
				listar_eventos();
			} else MensajeConClase(mensaje, tipo, titulo);
		}
	});
	return;
}


const confirmar_cambiar_estado_evento = id => {
	swal({
		title: "Cancelar Evento.?",
		text: "Si desea continuar con esta acción debe presionar la opción de 'Cancelar'",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Cancelar!",
		cancelButtonText: "No, Cerrar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				cambiar_estado_evento(id);
				return;
			}
		});
}


const modificar_evento = () => {
	let url = `${server_app}index.php/visitas_control/modificar_evento`;
	let data = new FormData(document.getElementById("form_modificar_evento"));
	data.append("id", id_evento);
	enviar_formulario(url, data, (resp) => {
		let {
			mensaje,
			tipo,
			titulo
		} = resp;
		if (mensaje == "sin_session") {
			close();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
			if (tipo == 'success') {
				$("#form_modificar_evento input[name='cupos']").removeAttr("required", "true").hide('slow').val('');
				$("#modal_modificar_evento").modal("hide");
				$("#form_modificar_evento").get(0).reset();
				listar_eventos();
			}
		}
	});
	return;
}

const buscar_evento = id => {
	let url = `${server_app}index.php/visitas_control/buscar_evento`;
	let data = {
		id
	};
	consulta_ajax(url, data, (res) => {
		if (res == "sin_session") {
			close();
		}
		const {
			nombre,
			fecha_inicio,
			fecha_fin,
			cupos,
			pre_inscripcion,
			ubicacion,
			estado,
			modifica,
			descripcion,
			firma
		} = res;

		if (estado != 'Eve_Reg') {
			MensajeConClase("El evento o la visita se encuentra en curso o terminado, por tal motivo no es posible continuar.", "info", "Oops.!");
		} else if (modifica == 0) {
			MensajeConClase("No esta autorizado para realizar esta acción.", "info", "Oops.!");
		} else {
			$('#form_modificar_evento input[name = "nombre"]').val(nombre);
			$('#form_modificar_evento input[name = "fecha_inicio"]').val(fecha_inicio);
			$('#form_modificar_evento input[name = "fecha_fin"]').val(fecha_fin);
			$('#form_modificar_evento input[name = "cupos"]').val(cupos);
			// $('#form_modificar_evento input[name = "firma"]').val(firma);
			$('#form_modificar_evento input[name = "ubicacion"]').val(ubicacion);
			$('#form_modificar_evento select[name = "pre_inscripcion"]').val(pre_inscripcion);
			$('#form_modificar_evento textarea[name = "descripcion"]').val(descripcion);
			if (firma != null) $("#firma_modi").prop("checked", true)
			else $("#firma_modi").prop("checked", false);

			if (pre_inscripcion == 2) $(".div_firma_mod").show();
			else $(".div_firma_mod").hide();

			if (cupos != null) {
				$("#con_cupos_modi").prop("checked", true);
				$("#form_modificar_evento input[name='cupos']").attr("required", "true").show('fast');
			} else {
				$("#con_cupos_modi").prop("checked", false);
				$("#form_modificar_evento input[name='cupos']").removeAttr("required", "true").hide('fast');
			}
			$("#modal_modificar_evento").modal();
		}
	});
	return;
}

const confirmar_agregar_persona = (identificacion, callback) => {
	swal({
		title: `No Encontrado .!`,
		text: `El visitante con Numero de identificación ${identificacion} no fue encontrado, Si desea agregar al visitante debe presionar la opción de 'Si, Agregar'`,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Agregar!",
		cancelButtonText: "No, Cerrar!",
		allowOutsideClick: true,
		closeOnConfirm: true,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				callback();
			}
		});
	return;
}

const llenar_form_agregar_visitante = data => {
	if (data.length == 0) return;
	let primer_nombre = '';
	let segundo_nombre = '';
	let {
		nombres,
		primer_apellido,
		segundo_apellido,
		num_documento
	} = data;

	nombres = nombres.split(" ");
	nombres.forEach((element, index) => {
		if (index > 0) {
			segundo_nombre = `${segundo_nombre} ${element}`;
		} else {
			primer_nombre = element;
		}
	});
	$("#txtIdentificacion").val(num_documento);
	$("#txtApellido").val(primer_apellido);
	$("#txtsegundoapellido").val(segundo_apellido);
	$("#txtNombre").val(primer_nombre);
	$("#txtSegundoNombre").val(segundo_nombre);

}
const idioma_tabla = () => {
	return {
		"sProcessing": "Procesando...",
		"sLengthMenu": "Mostrar _MENU_ registros",
		"sZeroRecords": "No se encontraron resultados",
		"sEmptyTable": "Ningún dato disponible en esta tabla",
		"sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
		"sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
		"sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
		"sInfoPostFix": "",
		"sSearch": "Buscar:",
		"sUrl": "",
		"sInfoThousands": ",",
		"sLoadingRecords": "Ningún dato disponible en esta tabla...",
		"oPaginate": {
			"sFirst": "Primero",
			"sLast": "Último",
			"sNext": "Siguiente",
			"sPrevious": "Anterior"
		},
		"oAria": {
			"sSortAscending": ": Activar para ordenar la columna de manera ascendente",
			"sSortDescending": ": Activar para ordenar la columna de manera descendente"
		}
	};

}

const abrir_modal_participantes = tipo => {
	$("#txt_buscar_participante").val('');
	listar_participantes('', () => { })
	$("#modal_asignar_participantes").modal();
	if (tipo == 'evento') {
		pintar_btn_para_eventos('#modal_asignar_participantes');
		callback_activo_add = resp => {
			$("#modal_registrar_visitante").modal("hide");
			$("#form_registrar_visitante").get(0).reset();
			form_nue_participante_evento(resp);
		}
	}
}

const listar_participantes = (dato, callback, tabla = '#tabla_participantes') => {
	$(`${tabla} tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(3)');
	let table = $(`${tabla}`).DataTable({
		"destroy": true,
		searching: false,
		"ajax": {
			url: server + "index.php/visitas_control/listar_participantes",
			dataType: "json",
			type: "post",
			data: {
				dato: dato,
			},
			"dataSrc": function (json) {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			},
		}, //paging: false,
		//scrollY: 400,
		"processing": true,
		"pageLength": 10,
		"columns": [{
			"data": "nombre_completo"
		},
		{
			"data": "identificacion"
		},
		{
			"data": "gestion"
		},
		],
		"language": idioma_tabla(),
		dom: 'Bfrtip',
		"buttons": []

	});


	$(`${tabla} tbody`).on('dblclick', 'tr', function () {
		let data = table.row(this).data();
		callback(data);
		$(`${tabla} tbody tr`).removeClass("success");
		$(this).attr("class", "success");

	});

	$(`${tabla} tbody`).on('click', 'tr td:nth-of-type(3)', function () {
		let data = table.row($(this).parent()).data();
		callback(data);
		$(`${tabla} tbody tr`).removeClass("success");
		$($(this).parent()).attr("class", "success");

	});
}

const buscar_participante_evento = () => {
	let dato = $("#txt_buscar_participante").val();
	if (dato.trim().length < 5) {
		MensajeConClase("Ingre dato del participante a buscar mayor a 4 caracteres.", "info", "Oops.!");
	} else {
		listar_participantes(dato, (resp) => {
			form_nue_participante_evento(resp);
		});
	}
}
const form_nue_participante_evento = data => {
	id_participante = data.id;
	$("input[name='placa_vehiculo']").attr("maxlength", "6");
	$("#container_hijos").hide();
	ver_datalle_evento_asignar('#datos_participante', data, '#modal_agregar_nuevo_participante');
}

const guardar_participante_evento = (id_persona, id_evento) => {
	let url = `${server_app}index.php/visitas_control/guardar_participante_evento`;
	let data = new FormData(document.getElementById("form_agregar_nuevo_participante"));
	data.append("id_persona", id_persona);
	data.append("id_evento", id_evento);
	enviar_formulario(url, data, (resp) => {
		let {
			mensaje,
			tipo,
			titulo
		} = resp;
		if (mensaje == "sin_session") {
			close();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
			if (tipo == 'success') {
				$(".dato_vehi").hide('slow');
				$(".dato_vehi input").removeAttr("required", "true").val('');
				$("#form_agregar_nuevo_participante").get(0).reset();
				listar_participantes('', () => { })
				$("#modal_agregar_nuevo_participante").modal("hide");
			}
		}
	});
	return;
}

const pintar_btn_para_eventos = (modal, texto = 'Listado') => {
	$("#listado_participantes_evento").remove();
	$(`${modal} #footermodal`).html(`<button type="button" class="btn btn-danger active" id="listado_participantes_evento"><span class="fa fa-list"></span> ${texto}</button> <button type="button" class="btn btn-default active" data-dismiss="modal"><span class="glyphicon glyphicon-resize-small"></span>Cerrar</button>`);
	$("#listado_participantes_evento").click(() => {
		activar_buscar_participante(false);
		listar_participantes_en_evento(id_evento, '#tabla_listado_participantes_en_evento');
	})
}

const listar_participantes_en_evento = (id_evento = '', tabla, buscar = '') => {

	$(`${tabla} tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)');
	let table = $(`${tabla}`).DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/visitas_control/listar_participantes_en_evento",
			dataType: "json",
			type: "post",
			data: {
				id_evento,
				buscar
			},
			"dataSrc": function (json) {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			},
		}, //paging: false,
		//scrollY: 400,
		"processing": true,
		"pageLength": 10,
		"columns": [{
			"data": "indice"
		}, {
			"data": "nombre_evento"
		}, {
			"data": "nombre_completo"
		},
		{
			"data": "identificacion"
		}, {
			"data": "tipo"
		},
		{
			"data": "fecha_entrada_evento"
		},
		{
			"data": "fecha_salida_evento"
		}, {
			"data": "correo"
		}, {
			"data": "programa"
		}, {
			"data": "celular"
		},
		{
			"data": "nombre_completo_hijo"
		},
		{
			"data": "identificacion_hijo"
		},
		{
			"data": "programa_hijo"
		},
		{
			"data": "gestion"
		},
		],
		"language": idioma_tabla(),
		dom: 'Bfrtip',
		"buttons": get_botones()

	});


	$(`${tabla} tbody`).on('click', 'tr', function () {
		let data = table.row(this).data();
		$(`${tabla} tbody tr`).removeClass("warning");
		$(this).attr("class", "warning");

	});
	$(`${tabla} tbody`).on('dblclick', 'tr', function () {
		let data = table.row(this).data();
		ver_detalle_participante_en_evento(data);
	});

	$(`${tabla} tbody`).on('click', 'tr td:nth-of-type(1)', function () {
		let data = table.row($(this).parent()).data();
		ver_detalle_participante_en_evento(data);
	});
	table.column(1).visible(false);
	table.column(4).visible(false);
	table.column(7).visible(false);
	table.column(8).visible(false);
	table.column(9).visible(false);
	table.column(10).visible(false);
	table.column(11).visible(false);
	table.column(12).visible(false);

	$("#modal_listado_participantes_en_evento").modal();
}

const ver_detalle_participante_en_evento = (data, tabla = '#tabla_detalle_participante_evento') => {
	let {
		nombre_completo,
		identificacion,
		fecha_entrada_evento,
		fecha_salida_evento,
		placa_vehiculo,
		acom_vehiculo,
		tipo,
		foto,
		fecha_registra,
		nombre_completo_salida,
		nombre_completo_entrada,
		nombre_completo_registra,
		tipo_persona,
		observaciones,
		con_firma
	} = data;
	$(`${tabla} .foto_participante`).html(`<img src='${server_app}${ruta_fotos}/${identificacion}.png' class="img-responsive img-rounded img-thumbnail">`);
	if (con_firma) {
		$(`${tabla} .firma_participante`).html(`<img src='${server_app}${ruta_firmas}/${con_firma}'>`);
		$(".con_firma").show();
	} else $(".con_firma").hide();
	$(`${tabla} .nombre_participante`).html(`${nombre_completo}${tipo_persona == 'PerEst' ? ` <span id="btn_adicional_visitante" class="pointer fa fa-edit red" title="Información Adicional" data-toggle="popover" data-trigger="hover"> </span>` : ''}`);
	$("#btn_adicional_visitante").click(() => {
		mostrar_datos_adicionales_visitante(data);
	});
	$(`${tabla} .identificacion_parti`).html(identificacion);
	$(`${tabla} .tipo_ingreso_participante`).html(tipo);
	$(`${tabla} .placa_participante`).html(placa_vehiculo == null || placa_vehiculo.length == 0 ? 'No registra' : placa_vehiculo);
	$(`${tabla} .acompanantes_participantes`).html(acom_vehiculo == null || acom_vehiculo.length == 0 ? 0 : acom_vehiculo);
	$(`${tabla} .entrada_participante`).html(fecha_entrada_evento);
	$(`${tabla} .observaciones`).html(observaciones);
	$(`${tabla} .entrada_marcada_por`).html(nombre_completo_entrada);
	$(`${tabla} .salida_participante`).html(fecha_salida_evento);
	$(`${tabla} .salida_marcada_por`).html(nombre_completo_salida);
	$(`${tabla} .participante_regi_por`).html(nombre_completo_registra);
	$(`${tabla} .fecha_registro_par`).html(fecha_registra);
	$("#modal_detalle_participante_evento").modal();
}
const marcar_participantes_evento = (id, id_evento, tipo, firma = null) => {
	let url = `${server_app}index.php/visitas_control/marcar_participantes_evento`;
	let data = {
		id,
		id_evento,
		tipo,
		firma
	};
	consulta_ajax(url, data, async (resp) => {
		let {
			mensaje,
			tipo,
			titulo,
			evento,
		} = resp;
		if (tipo == "sin_session") {
			close();
		} else if (tipo == 'success') {
			swal.close();

			$("#modal_solicitar_firma").modal("hide");
			if (firma) {
				await generar_acta(id);
				const acta = `<a href="${Traer_Server()}archivos_adjuntos/visitas/actas/${id}.pdf">ver acta</a>`;
				let mensaje = `
				<p>Hoy, en su primer día en la universidad, queremos que sepan que como Institución y desde el área de Bienestar estudiantil estamos comprometidos a trabajar en su formación como Ciudadanos Integrales.</p>
				<p>Nuestra invitación, es a que durante los próximos años se diviertan y sean felices. Estudien y prepárense, porque aquí han venido a adquirir conocimientos y competencias que les ayudarán a desarrollar su proyecto de vida académico.</p>
				<p>Por ello, la Universidad De la Costa les hace entrega de un obsequio, que más allá de ser un dispositivo móvil; sabemos que representará para ustedes una manera de explorar y conectarse con nuevos modelos de aprendizaje y que seguramente se convertirá, en una herramienta aliada para su proceso de formación profesional.</p>
				<p>En el siguiente enlace puedes encontrar el acta de entrega : ${acta}</p>
				<p><b>¡Disfrútelo, es por y para ustedes!</b></p>
				<p>Vicerrectoría de Bienestar Unicosta</p>
				`;
				enviar_correo_personalizado("vist", mensaje, evento.correo, evento.nombre_completo, "ENTREGA DE TABLET", "ENTREGA DE TABLET", "ParCodAdm", 1);
			}
			listar_participantes_en_evento(id_evento, '#tabla_listado_participantes_en_evento');
		} else MensajeConClase(mensaje, tipo, titulo);
	});
	return;
}
const pedir_firma = (id, id_evento, tipo) => {
	$("#modal_solicitar_firma").modal();
	$("#div_firmar").html(`<p><span class="fa fa-edit"></span> Registra tu firma:</p>
	<div id="content_firmas"><p style="text-align:center;">Configurando...</p></div>
  `);
	newCanvas();
	$("#enviar_firma").off("click");
	$("#enviar_firma").click(() => {
		let image = document.getElementById("canvas").toDataURL();
		confirmar_marcar_participantes_evento(id, id_evento, tipo, -1, image);
	})
}

const confirmar_marcar_participantes_evento = (id, id_evento, tipo, firma, image = null) => {
	if (tipo == 1 && firma == 1) return pedir_firma(id, id_evento, tipo);
	let title = 'Marcar Entrada .?';
	if (tipo == 2) title = 'Marcar Salida .?';
	else if (tipo == 3) title = 'Cancelar Ingreso .?';

	swal({
		title,
		text: `Tener en cuenta que al realizar esta acción no podrá regresar al estado anterior, Si desea continuar debe presionar la opción de 'Si, Entiendo'`,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Entiendo!",
		cancelButtonText: "No, Cerrar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				marcar_participantes_evento(id, id_evento, tipo, image);
			}
		});
	return;
}

const mostrar_datos_adicionales_visitante = data => {
	let {
		correo,
		celular,
		programa,
		tipo_persona
	} = data;
	if (tipo_persona == 'PerEst') {
		$(`.correo_visitante`).html(correo);
		$(`.celular_visitante`).html(celular);
		$(`.programa_visitante`).html(programa);
		$("#modal_datos_adicionales_persona").modal();
	} else {
		MensajeConClase("El visitante no cuenta con información adicional", 'info', 'Oops.!')
	}
}

const listar_visitantes = (dato, callbak) => {
	let url = `${server_app}index.php/visitas_control/listar_visitantes`;
	consulta_ajax(url, { dato }, (resp) => {
		$(`#tabla_visitantes tbody`).off('click', 'tr td .sancionar').off('click', 'tr td .modificar').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:nth-of-type(1)');
		const myTable = $("#tabla_visitantes").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{ "defaultContent": `<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>` },
				{ data: "nombre_completo" },
				{ data: 'tipo' },
				{ data: 'identificacion' },
				{ data: 'estado' },
				{ 'defaultContent': '<span  title="Gestion Cerrada" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off btn"></span> <span class="pointer fa fa-edit modificar" title="Modificar" data-toggle="popover" data-trigger="hover"></span>' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_visitantes tbody').on('click', 'tr', function () {
			let data = myTable.row(this).data();
			data_visitante = data;
			$("#tabla_visitantes tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$('#tabla_visitantes tbody').on('dblclick', 'tr', function () {
			let data = myTable.row(this).data();
			ver_detalle_visitante(data);
		});
		$('#tabla_visitantes tbody').on('click', 'tr td .modificar', function () {
			let data = myTable.row($(this).parent().parent()).data();
			modificar_visitante(data);
		});
		$('#tabla_visitantes tbody').on('click', 'tr td:nth-of-type(1)', function () {
			let data = myTable.row($(this).parent()).data();
			ver_detalle_visitante(data);
		});

		const ver_detalle_visitante = async (data, tabla = '#tabla_detalle_visitante') => {
			let {
				id,
				nombre_completo,
				identificacion,
				tipo,
				fecha_registra,
				foto,
				nombre_completo_registra
			} = data;
			$(`${tabla} .foto`).html(`<img src='${server_app}${ruta_fotos}/${identificacion}.png' class="img-responsive img-rounded img-thumbnail">`);
			$(`${tabla} .nombre`).html(nombre_completo);
			$(`${tabla} .identificacion`).html(identificacion);
			$(`${tabla} .tipo_identificacion`).html(tipo);
			$(`${tabla} .registrado_por`).html(nombre_completo_registra);
			$(`${tabla} .fecha_registra`).html(fecha_registra);
			let ingresos = await obtener_ingresos_visitante(id);
			listar_ingresos_visitante(ingresos);
			let sanciones = await obtener_sanciones_visitante(id);
			listar_sanciones_visitante(sanciones);
			$("#modal_detalle_visitante").modal();
		}

		const modificar_visitante = async (data) => {
			let {
				id,
				tipo_identificacion,
				identificacion,
				tipo_persona,
				nombre,
				segundo_nombre,
				apellido,
				segundo_apellido,
				id_programa,
				celular,
				correo
			} = data;
			idvisitante = id;
			action = 'modificar_datos_visitante';
			$("#tipo_persona").val(tipo_persona);
			if (tipo_persona == 'PerEst') $("#datos_adicionales .req").attr("required", "true").show("slow");
			else $("#datos_adicionales .req").removeAttr("required", "true").hide("slow");
			$("#cbxtipoIdentificacion").val(tipo_identificacion);
			$("#txtIdentificacion").val(identificacion);
			$("#txtApellido").val(apellido);
			$("#txtsegundoapellido").val(segundo_apellido);
			$("#txtNombre").val(nombre);
			$("#txtSegundoNombre").val(segundo_nombre);
			$(".cbxprogramas").val(id_programa);
			$("#celular").val(celular);
			$("#correo").val(correo);
			$("#modal_registrar_visitante").modal();
		}

	});

}

const obtener_ingresos_visitante = id => {
	return new Promise(resolve => {
		let url = `${server_app}index.php/visitas_control/obtener_ingresos_visitante`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
}
const obtener_sanciones_visitante = id => {
	return new Promise(resolve => {
		let url = `${server_app}index.php/visitas_control/obtener_sanciones_visitante`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
}

const listar_ingresos_visitante = (data) => {
	$('#tabla_ingresos_visitante tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)');
	let table = $("#tabla_ingresos_visitante").DataTable({
		"destroy": true,
		"processing": true,
		data,
		"columns": [
			{ "defaultContent": `<span  style="background-color: white;color: black; width: 100%;" class="pointer form-control"><span >ver</span></span>` },
			{
				"data": "departamento"
			},
			{
				"data": "hora_entrada"
			},
			{
				"data": "hora_salida"
			},
		],
		"language": idioma_tabla(),
		dom: 'Bfrtip',
		"buttons": get_botones(),
	});

	$('#tabla_ingresos_visitante tbody').on('click', 'tr', function () {
		$("#tabla_ingresos_visitante tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});

	$('#tabla_ingresos_visitante tbody').on('dblclick', 'tr', function () {
		let data = table.row(this).data();
		ver_datalle_visitante("#datos_perso", data, "#modal_detalle_ingreso_departamento_persona");
	});

	$('#tabla_ingresos_visitante tbody').on('click', 'tr td:nth-of-type(1)', function () {
		let data = table.row($(this).parent()).data();
		ver_datalle_visitante("#datos_perso", data, "#modal_detalle_ingreso_departamento_persona");
	});
}

const sancionar_visitante = ({ id, nombre_completo, identificacion }) => {
	swal({
		title: 'Sancionar Visitante.!',
		text: `El visitante ${nombre_completo} sera sancionado, Tener en cuenta que al sancionar un visitante se le negara el ingreso a la institución.`,
		type: "input",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Entiendo",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true,
		inputPlaceholder: `Ingrese Motivo`
	}, function (motivo) {

		if (motivo === false)
			return false;
		if (motivo === "") {
			swal.showInputError(`Debe Ingresar el Motivo.!`);
		} else {
			let url = `${server_app}index.php/visitas_control/sancionar_visitante`;
			consulta_ajax(url, { id, motivo }, async (resp) => {
				let { tipo, mensaje, titulo } = resp;
				if (tipo == 'success') {
					swal.close();
					let sanciones = await obtener_sanciones_visitante(id);
					listar_sanciones_visitante(sanciones);
				} else MensajeConClase(mensaje, tipo, titulo);

			});
		}
	});
}

const listar_sanciones_visitante = (data, tabla = '#tabla_sanciones_visitante', ocultar = false) => {
	$(`${tabla} tbody`).off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .eliminar');
	let table = $(`${tabla}`).DataTable({
		"destroy": true,
		"processing": true,
		data,
		"columns": [
			{
				"data": "motivo"
			},
			{
				"data": "fecha_registra"
			},
			{
				"data": "sancionado_por"
			},
			{ 'defaultContent': '<span style="color: #d9534f;" title="Eliminar" data-toggle="popover" data-trigger="hover" class="fa fa-remove btn btn-default eliminar" ></span>' },
		],
		"language": idioma_tabla(),
		dom: 'Bfrtip',
		"buttons": !ocultar ? get_botones() : [],
	});

	$(`${tabla} tbody`).on('click', 'tr', function () {
		$(`${tabla} tbody tr`).removeClass("warning");
		$(this).attr("class", "warning");
	});

	$(`${tabla} tbody`).on('click', 'tr td .eliminar', function () {
		let { id, id_visitante } = table.row($(this).parent().parent()).data();
		eliminar_sancion_visitante(id, id_visitante);
	});
	ocultar ? table.column(3).visible(false) : '';
}

const eliminar_sancion_visitante = (id, id_visitante) => {
	swal({
		title: "Eliminar Sanción.?",
		text: "Tener en cuenta que al eliminar la sanción el visitante tendrá acceso a la institución.",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Eliminar!",
		cancelButtonText: "No, Cerrar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				let url = `${server_app}index.php/visitas_control/eliminar_sancion_visitante`;
				consulta_ajax(url, { id }, async (resp) => {
					let { tipo, mensaje, titulo } = resp;
					if (tipo == 'success') {
						swal.close();
						let sanciones = await obtener_sanciones_visitante(id_visitante);
						listar_sanciones_visitante(sanciones);
					} else MensajeConClase(mensaje, tipo, titulo);

				});
			}
		});
}

const mostrar_alerta_sancion = async ({ nombre_completo, id, foto, identificacion }, mensaje) => {
	$("#nombre_sancionado").html(nombre_completo);
	$("#foto_sancionado").html(`<img src='${server_app}/${ruta_fotos}/${identificacion}.png' class="img-responsive img-rounded img-thumbnail">`);
	let sanciones = await obtener_sanciones_visitante(id);
	listar_sanciones_visitante(sanciones, '#tabla_sanciones_visitante_alert', true);
	continuar_ingreso_sancion = () => {
		mostrar_modal_ingreso(mensaje, nombre_completo);
	}
	$("#modal_sanciones_visitante").modal();
}

const mostrar_modal_ingreso = (menssaje, nombre_completo) => {
	$("#mensaje_ingreso").html(menssaje);
	$("#nombre_ingreso").html(nombre_completo);
	$("#modal_registrar_visita").modal();
}

const remplazar_foto = ({ identificacion }) => {
	return new Promise(resolve => {
		let resp = { 'mensaje': 'Registrado sin foto.!', 'tipo': 'success', 'titulo': 'Proceso Exitoso.!' };
		// let resp = { 'mensaje': 'Antes de continuar debe tomar la foto del visitante.!', 'tipo': 'info', 'titulo': 'Oops.!' };
		// if (foto_requerida) {
		if (tomo_foto) {
			let url = `${server_app}index.php/visitas_control/remplazar_foto`;
			let data = new FormData(document.getElementById("form_registrar_visita"));
			canvas = document.getElementById("foto_ingreso");
			let foto_v = '';
			if (canvas) {
				let foto = canvas.toDataURL("image/jpeg");
				let info = foto.split(",", 2);
				foto_v = info[1];
			}
			data.append("foto", foto_v);
			data.append('identificacion', identificacion);
			enviar_formulario(url, data, (resp) => { resolve(resp); });
		} else resolve(resp);

		// }
	});
}

const reiniciar_canvas = c_canvas => {
	let canvas = document.getElementById(c_canvas);
	let ctx = canvas.getContext("2d");
	ctx.clearRect(0, 0, canvas.width, canvas.height);
	tomo_foto = false;
}

const obtener_data_tipo_participante = id => {
	return new Promise(resolve => {
		let url = `${server_app}index.php/visitas_control/obtener_data_tipo_participante`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
}
const obtener_hijos = id => {
	return new Promise(resolve => {
		let url = `${server_app}index.php/visitas_control/obtener_hijos`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
}

const asignar_hijo = (id_hijo, id_padre, tipo_acc = 'asignado') => {
	let url = `${server_app}index.php/visitas_control/asignar_hijo`;
	consulta_ajax(url, { id_hijo, id_padre, 'tipo': tipo_acc }, ({ mensaje, tipo, titulo }) => {
		if (tipo == 'success') {
			id_hijo = tipo_acc == 'asignado' ? id_hijo : '';
			pintar_hijos(id_padre, id_hijo);
			$("#modal_asignar_hijo").modal("hide");
			if (tipo_acc != 'asignado') swal.close();
		}
		if (tipo != 'success' || (tipo_acc == 'asignado')) MensajeConClase(mensaje, tipo, titulo)
	});
}

const pintar_hijos = async (id_padre, id_hijo = '') => {
	let hijos = await obtener_hijos(id_padre);
	pintar_datos_combo(hijos, ".cbx_hijos", "Seleccione Hijo", id_hijo);
}

const pintar_datos_combo = (datos, combo, mensaje, sele = '', ) => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach(elemento => {
		$(combo).append(`<option value='${elemento.id}'> ${elemento.valor}</option>`);
	});
	$(combo).val(sele);
}

const generar_codigo_evento = (id, codigo) => {
	swal({
		title: "Nuevo Codigo.?",
		text: `${!codigo ? 'El evento no tiene codigo asignado, Si desea generar un codigo' : `El codigo actual del evento es ${codigo}, Si desea generar un nuevo codigo`} debe presionar la opción de 'Si, Continuar'`,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Continuar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				let url = `${server_app}index.php/visitas_control/generar_codigo_evento`;
				consulta_ajax(url, { id }, ({ mensaje, tipo, titulo }) => {
					if (tipo == 'success') listar_eventos();
					MensajeConClase(mensaje, tipo, titulo)
				});
			}
		});
}

const auto_guardar_participante_evento = () => {
	let url = `${server_app}index.php/visitas_control/auto_guardar_participante_evento`;
	let data = new FormData(document.getElementById("form_agregar_nuevo_participante_auto"));
	enviar_formulario(url, data, ({ mensaje, tipo, titulo }) => {
		MensajeConClase(mensaje, tipo, titulo);
		if (tipo == 'success') $("#form_agregar_nuevo_participante_auto").get(0).reset();
	});
}

const activar_buscar_participante = (activar = false) => {
	if (activar) {
		$("#descargar_participantes").hide();
		listar_participantes_en_evento(-1, '#tabla_listado_participantes_en_evento');
		$("#btn_buscar_participante_general").off("click");
		$("#txt_buscar_participante_general").off("keypress");
		$("#container_buscar_participante").html(`
			<div class="form-group agrupado col-md-6 text-left">
				<div class="input-group">
					<input id='txt_buscar_participante_general' class="form-control" placeholder="Ingrese identificación o nombre de la persona" value=''>
					<span class="input-group-addon btn btn-default" id="btn_buscar_participante_general"><span class="glyphicon glyphicon-search"></span></span>
				</div>
			</div>
	`);
		$("#btn_buscar_participante_general").click(() => {
			let dato = $("#txt_buscar_participante_general").val();
			if (dato.length == 0) {
				MensajeConClase("Ingre dato del participante a buscar.", "info", "Oops.!");
			} else {
				listar_participantes_en_evento('', '#tabla_listado_participantes_en_evento', dato);
			}
		});

		$("#txt_buscar_participante_general").keypress(e => {
			const code = (e.keyCode ? e.keyCode : e.which);
			if (code == 13) {
				let dato = $("#txt_buscar_participante_general").val();
				if (dato.length == 0) {
					MensajeConClase("Ingre dato del participante a buscar.", "info", "Oops.!");
				} else {
					listar_participantes_en_evento('', '#tabla_listado_participantes_en_evento', dato);
				}
				return false;
			}
		});

		$("#modal_listado_participantes_en_evento").modal();
	} else {
		$("#descargar_participantes").show();
		$("#container_buscar_participante").html(``);
	}
}


const generar_acta = id => {
	console.log("generando");
	const route = `${Traer_Server()}index.php/eventos/generar_acta/${id}`;
	window.open(route, '_blank');
	window.focus()
	console.log("generado");
	return true;
}
