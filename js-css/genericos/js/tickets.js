const ruta = `${Traer_Server()}index.php/tickets_control/`;
let ruta_ = `${Traer_Server()}index.php/bienestar_control/`;
const ruta_tickets = () => `${Traer_Server()}index.php/tickets_control/`;
const ruta_evidencia = "archivos_adjuntos/tickets/ev_solucion/";
let callback_activo = (resp) => {};
let callback_activo_aux = (resp) => {};
let callbak__activo = (resp) => {};
let id_solicitud_global = null;
let nombre_usuario_global = null;
let correo_global = null;
let id_persona = 0;
let tipo_solicitud_id = null;
let id_estado_solicitud = null;
let tipo_busqueda = null;
let actividad_selec = null;
let permisos = [];
let notificacion = null;
let id_horario = "";
let data_solicitante = { nombre: null, correo: null };
let id_nvl_impacto = "00:00:00";
let id_nvl_urgencia = "00:00:00";
let id_nvl_prioridad = "00:00:00";

$(document).ready(function () {
	$("#listado_solicitudes").click(() => {
		administrar_modulo("listado_solicitudes");
	});

	$(".regresar_menu").click(function () {
		administrar_modulo("regresar_menu");
	});

	$("#nuevo_ticket").click(() => {
		$("#form_crear_ticket").get(0).reset();
		$("#modal_crear_ticket").modal();
	});
	$("#form_crear_ticket").submit((e) => {
		e.preventDefault();
		guardar_ticket();
	});

	$("#form_suspender_servicio").submit(() => {
		suspender();
		return false;
	});

	$(".btn_log").click(function () {
		listar_historial_estados();
	});

	$("#btn_especialista").click(async () => {
		let impacto = $("#impacto").val();
		let urgencia = $("#urgencia").val();
		let prioridad = $("#prioridad").val();
		let categoria = $("#categoria").val();
		let subcategoria = $("#subcategoria").val();
		if (!impacto || !urgencia || !prioridad || !categoria || !subcategoria) {
			swal({
				title: "Alerta!",
				text:
					"Debe completar todos los campos anteriores, para despues asignar un especialista!",
				type: "warning",
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Entiendo!",
			});
		} else {
			let id_nvl_impacto = await obtener_valory(impacto);
			let id_nvl_urgencia = await obtener_valory(urgencia);
			let id_nvl_prioridad = await obtener_valory(prioridad);
			buscar_especialista(
				"",
				id_nvl_impacto,
				id_nvl_urgencia,
				id_nvl_prioridad,
				callback_activo
			);
			$("#modal_buscar_persona").modal();
		}
	});
	$("#form_buscar_persona").submit(() => {
		let dato = $("#txt_documento_espec").val();
		buscar_especialista(
			dato,
			id_nvl_impacto,
			id_nvl_urgencia,
			id_nvl_prioridad,
			callback_activo
		);
		return false;
	});
	$("#form_asignar_especialista").submit(() => {
		asignar_especialista();
		return false;
	});
	const callback_activo = (data) => {
		const { id, nombre_completo } = data;
		id_persona = id;
		$("#txt_especialista").val(nombre_completo);
		$("#modal_buscar_persona").modal("hide");
	};

	$("#btn_buscar_persona").click(() => {
		buscar_empleado("", "", callbak_activo);
		$("#modal_buscar_empleado").modal();
	});
	$("#form_buscar_empleado").submit(() => {
		let dato = $("#txt_documento_empleado").val();
		buscar_empleado(dato, callbak_activo);
		return false;
	});
	const callbak_activo = (data) => {
		const { id, nombre_completo } = data;
		id_persona = id;
		$("#txt_buscar_persona").val(nombre_completo);
		$("#modal_buscar_empleado").modal("hide");
	};

	//Administrar
	$("#s_persona").click(() => {
		$("#modal_elegir_persona").modal();
		listar_personas();
		$("#txt_persona").val("");
	});
	$("#sele_perso").click(() => {
		$("#modal_elegir_persona").modal();
		listar_personas();
		$("#txt_persona").val("");
	});
	$("#frm_buscar_persona").submit((e) => {
		e.preventDefault();
		const persona = $("#txt_persona").val();
		listar_personas(persona);
	});

	$("#categoria").change(async () => {
		let categoria = $("#categoria").val();
		let datos = await obtener_permisos_parametros(categoria);
		pintar_datos_combo(datos, "#subcategoria", "Seleccione Subcategoria");
	});

	//Administrar btn

	$("#btnConfiguraciones").click(() => {
		$("#modal_administrar").modal();
		listar_valor_parametro(237, "tabla_permisos");
	});
	$("#tabla_actividades tbody").on("click", "tr span.config", function () {
		const { asignado, id } = myTable.row($(this).parent()).data();
		actividad_selec = asignado;
		$("#modal_elegir_estado").modal();
		listar_estados(asignado);
	});

	$("#solucionado").click(() => {
		$("#modal_solucionado").modal();
	});

	$("#form_solucionado").submit(() => {
		SolucionarTicket();
		return false;
	});
	$("#Notificaciones").click(() => {
		$("#notificaciones").modal();
	});

	$("#horario_funcionario").click(() => {
		$("#container_permisos").addClass("oculto");
		$("#container_horario_func").removeClass("oculto");
		$("#permisos").addClass("oculto");
		listar_horarios_funcionarios();
		$("#permisos").removeClass("active");
		$("#horario_funcionario").addClass("active");
	});
	$("#permisos").click(() => {
		$("#horario_funcionario").removeClass("active");
		$("#permisos").addClass("active");
		$("#container_horario_func").addClass("oculto");
		$("#container_permisos").removeClass("oculto");
		$("#permisos").addClass("active");
	});
	$(".btn_horario").click(() => {
		id_horario = "";
		$(".titulo_modal").html("Nuevo Horario");
		$("#form_guardar_horario").get(0).reset();
		$("#modal_crear_horario").modal();
		return false;
	});
	$("#form_guardar_horario").submit(() => {
		guardar_horario_funcionario();
		return false;
	});

	$("#asignar_funcionario_horario").click(function () {
		$("#form_buscar_persona_horario").get(0).reset();
		callbak__activo = (data) => {
			swal(
				{
					title: "Estas Seguro ?",
					text: `¿Esta seguro de asignar a ${data.nombre_completo} a este horario?, si desea continuar presione la opción de 'Si, Entiendo'.!`,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Si, Entiendo!",
					cancelButtonText: "No, Cancelar!",
					allowOutsideClick: true,
					closeOnConfirm: false,
					closeOnCancel: true,
				},
				function (isConfirm) {
					if (isConfirm) {
						id_persona = data.id;
						guardar_funcionario_horario(id_persona, id_horario);
					}
				}
			);
		};
		let data_activa = { filtro_funcionario: 1, dato: "" };
		buscar_persona(data_activa, callbak__activo);
		$("#modal_buscar_persona_horario").modal();
	});
	$("#form_buscar_persona_horario").submit(() => {
		let dato = $("#txt_per_buscar").val();
		buscar_persona({ dato }, callbak__activo);
		// buscar_persona({dato}, callbak_activo);
		return false;
	});
	$("#filtrar_solicitudes").click(function () {
		obtener_tipos_solicitud("cbxtiposol", "Filtrar por tipo solicitud");
		obtener_estado("cbxestado", "Filtrar por Estado");
		$("#modal_crear_filtros").modal();
	});

	$("#form_filtros").submit((e) => {
		e.preventDefault();
		filtrar_solicitudes();
		return false;
	});
	$("#btn_limpiar_filtros").click(function () {
		listado_solicitudes();
	});

	$("#tipo_solicitud").change(function () {
		let tipo = $(this).val();
		if (tipo == "TP_Sol_Agil" || tipo == "TP_Sol_Emma") {
			$(".btn_evidencia_form_principal").removeClass("oculto");
			$(".btn_evidencia_form_principal").addClass("active");
			// $("#btn_evidencia_form_principal").add("oculto");
			// btn_evidencia_form_principal;
		}else{
			$(".btn_evidencia_form_principal").addClass("oculto");
			$(".btn_evidencia_form_principal").removeClass("active");
		}
	});
});

const listar_valor_parametro = (idparametro, tabla_html) => {
	$(`#${tabla_html} tbody`)
		.off("click", "tr")
		.off("click", "tr .asignar")
		.off("click", "tr .eliminar")
		.off("click", "tr .modificar");
	consulta_ajax(`${ruta}listar_valor_parametro`, { idparametro }, (resp) => {
		const myTable = $(`#${tabla_html}`).DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "valor",
				},
				{
					data: "accion",
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$(`#${tabla_html} tbody`).on("click", "tr", function () {
			$(`#${tabla_html} tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});
	});
};

const administrar_modulo = (tipo) => {
	if (tipo == "listado_solicitudes") {
		listado_solicitudes();
		$("#menu_principal").css("display", "none");
		$("#listar_solicitudes").fadeIn();
	} else if (tipo == "regresar_menu") {
		$("#listar_solicitudes").css("display", "none");
		$("#menu_principal").fadeIn(1000);
	}
};
const pintar_datos_combo = (datos, combo, mensaje, sele = "") => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach((elemento) => {
		$(combo).append(
			`<option value='${elemento.id}'> ${elemento.valor}</option>`
		);
	});
	$(combo).val(sele);
};

const pintar_datos_combo_1 = (datos, combo, mensaje, tipo = 0) => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach((element) => {
		if (tipo) {
			$(combo).append(
				`<option value='${element.id_aux}'> ${element.valor} </option>`
			);
		} else {
			$(combo).append(
				`<option value='${element.id}'> ${element.valor} </option>`
			);
		}
	});
};
const obtener_permisos_parametros = async (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_permisos_parametros`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};
const obtener_valory = async (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_valory`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};

const suspender = () => {
	const data = new FormData(document.getElementById("form_suspender_servicio"));
	data.append("id_solicitud", id_solicitud_global);
	enviar_formulario(`${ruta}suspender`, data, ({ tipo, mensaje, titulo }) => {
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#modal_suspender_servicio").modal("hide");
			listado_solicitudes();
			data_solicitante;
			enviar_correo_estado("TIK_Suspen", id_solicitud_global, "");
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
};
const asignar_especialista = () => {
	const data = new FormData(
		document.getElementById("form_asignar_especialista")
	);
	data.append("id_persona", id_persona);
	data.append("id_solicitud", id_solicitud_global);
	enviar_formulario(
		`${ruta}asignar_especialista`,
		data,
		({ tipo, mensaje, titulo }) => {
			if (tipo === "success") {
				$("#form_asignar_especialista").get(0).reset();
				$("#modal_asignar_especialista").modal("hide");
				listado_solicitudes();
				MensajeConClase(mensaje, tipo, titulo);
				data_solicitante;
				enviar_correo_estado("TIK_Asig", id_solicitud_global, "");
			}
			MensajeConClase(mensaje, tipo, titulo);
		}
	);
};

const guardar_ticket = () => {
	const data = new FormData(document.getElementById("form_crear_ticket"));
	data.append("id_persona", id_persona);
	enviar_formulario(`${ruta}guardar_ticket`, data, ({ tipo, mensaje, titulo }) => {
		if (tipo === "success") {
			MensajeConClase(mensaje, tipo, titulo);
			enviar_correo_estado("TIK_Regis", "", "");
			$("#form_crear_ticket").get(0).reset();
			$("#modal_crear_ticket").modal("hide");
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
};

const cambiarEstado = (id, estado, mensaje_c = "") => {
	const link = `${ruta}cambiarEstado`;
	consulta_ajax(link, { id, estado, id, mensaje: mensaje_c }, (data) => {
		const { titulo, mensaje, tipo } = data;
		if (tipo == "success") {
			enviar_correo_estado(id, estado, mensaje_c);
			swal.close();
			listado_solicitudes();
			id = null;
			$("#tabla_solicitudes tbody tr").removeClass("warning");
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};

const SolucionarTicket = () => {
	let data = new FormData(document.getElementById("form_solucionado"));
	data.append("id_solicitud", id_solicitud_global);
	enviar_formulario(`${ruta_tickets()}SolucionarTicket`, data, (resp) => {
		const { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			//swal.close();
			listado_solicitudes();
			id = null;
			$("#form_solucionado").get(0).reset();
			$("#modal_solucionado").modal("hide");
			$("#tabla_solicitudes tbody tr").removeClass("warning");
			MensajeConClase(mensaje, tipo, titulo);
			data_solicitante;
			enviar_correo_estado("TIK_Soluc", id_solicitud_global, "");
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};
const listado_solicitudes = (id = "", filtros = {}) => {
	let {
		fecha_inicial,
		fecha_final,
		hora_inicio_filtro,
		hora_fin_filtro,
		id_tipo_solicitud,
		id_estado_sol,
	} = filtros;
	$("#tabla_solicitudes tbody")
		.off("dblclick", "tr")
		.off("click", "tr")
		.off("click", "tr .ver")
		.off("click", "tr .cancelar")
		.off("click", "tr .revision")
		.off("click", "tr .asignar")
		.off("click", "tr .proceso")
		.off("click", "tr .solucionado")
		.off("click", "tr .suspender");

	consulta_ajax(
		`${ruta_tickets()}listado_solicitudes`,
		{
			id,
			fecha_inicial,
			fecha_final,
			hora_inicio_filtro,
			hora_fin_filtro,
			id_estado_sol,
			id_tipo_solicitud,
		},
		(resp) => {
			let i = 0;
			const myTable = $("#tabla_solicitudes").DataTable({
				destroy: true,
				processing: true,
				search: true,
				data: resp,
				columns: [
					{
						data: "ver",
					},
					{
						data: "funcionario",
					},
					{
						data: "asunto",
					},
					{
						data: "fecha_solicitud",
					},
					{
						data: "t_solucionado",
					},
					{
						render: function (data, type, data, meta) {
							if (
								data.t_solucionado >= 1 &&
								data.t_solucionado <= data.t_solucion
							)
								return "Mal";
							else if (!data.t_solucionado) return "";
							else return "Bien";
						},
					},
					{
						data: "t_asignacion",
					},
					{
						render: function (data, type, data, meta) {
							if (
								data.t_asignacion >= 1 &&
								data.t_asignacion <= data.t_asignado
							)
								return "Mal";
							else if (!data.t_asignacion) return "";
							else return "Bien";
						},
					},
					{
						data: "estado_ticket",
					},
					{
						data: "accion",
					},
				],
				language: idioma,
				dom: "Bfrtip",
				buttons: get_botones(""),
			});
			myTable.column(4).visible(false);
			myTable.column(5).visible(false);
			myTable.column(6).visible(false);
			myTable.column(7).visible(false);

			//EVENTOS DE LA TABLA ACTIVADOS
			$("#tabla_solicitudes tbody").on("click", "tr", function () {
				$("#tabla_solicitudes tbody tr").removeClass("warning");
				$(this).addClass("warning");
				let { id_solicitud_global, funcionario, correo } = myTable
					.row(this)
					.data();
				data_solicitante = { nombre: funcionario, correo };
				id_solicitud = id_solicitud_global;
			});

			$("#tabla_solicitudes tbody").on("click", "tr .ver", function () {
				let data = myTable.row($(this).parent()).data();
				id_solicitud_global = data.id;
				data_solicitud_global = data;
				estado_solicitud = data.state;
				tipo_solicitud_id = data.id_tipo_solicitud;
				id_estado_solicitud = data.id_estado;
				ver_detalle_ticket(data);
			});

			$("#tabla_solicitudes tbody").on("click", "tr .cancelar", function () {
				let { id, state } = myTable.row($(this).parent()).data();
				id_solicitud_global = id;
				estado_solicitud = state;
				finalizar_solicitud(
					id,
					"TIK_Anul",
					"Cancelar Solicitud",
					"¿Está seguro que desea cancelar la solicitud?"
				);
			});
			$("#tabla_solicitudes tbody").on("click", "tr .asignar", function () {
				let {
					id,
					state,
					id_tipo_solicitud,
					id_estado,
					funcionario,
					correo,
				} = myTable.row($(this).parent()).data();
				id_solicitud_global = id;
				estado_solicitud = state;
				tipo_solicitud_id = id_tipo_solicitud;
				id_estado_solicitud = id_estado;
				nombre_usuario_global = funcionario;
				correo_global = correo;
				data_solicitante = { nombre: nombre_usuario_global, correo_global };
				$("#form_asignar_especialista").get(0).reset();

				$("#modal_asignar_especialista").modal();
			});
			$("#tabla_solicitudes tbody").on("click", "tr .proceso", function () {
				let { id, state, funcionario, correo } = myTable
					.row($(this).parent())
					.data();
				id_solicitud_global = id;
				nombre_usuario_global = funcionario;
				estado_solicitud = state;
				correo_global = correo;
				data_solicitante = { nombre: nombre_usuario_global, correo_global };
				finalizar_solicitud(
					id,
					"TIK_Proce",
					"En Proceso",
					"¿Desea cambiar el estado a 'En Proceso'"
				);
			});
			$("#tabla_solicitudes tbody").on("click", "tr .solucionado", function () {
				let { id, state, correo, funcionario } = myTable
					.row($(this).parent())
					.data();
				id_solicitud_global = id;
				estado_solicitud = state;
				nombre_usuario_global = funcionario;
				correo_global = correo;
				data_solicitante = { nombre: nombre_usuario_global, correo_global };
				//tipo_correo = 'Fact_Neg';
				$("#form_solucionado").get(0).reset();
				$("#modal_solucionado").modal();
			});
			$("#tabla_solicitudes tbody").on("click", "tr .suspender", function () {
				let { id, state, funcionario, correo } = myTable
					.row($(this).parent())
					.data();
				id_solicitud_global = id;
				estado_solicitud = state;
				nombre_usuario_global = funcionario;
				correo_global = correo;
				data_solicitante = { nombre: nombre_usuario_global, correo_global };
				//tipo_correo = 'Fact_Neg';
				$("#modal_suspender_servicio").modal();
			});
			$("#tabla_solicitudes tbody").on("click", "tr .negar", function () {
				let { id, state, funcionario, correo } = myTable
					.row($(this).parent())
					.data();
				id_solicitud_global = id;
				estado_solicitud = state;
				nombre_usuario_global = funcionario;
				correo_global = correo;
				data_solicitante = { nombre: nombre_usuario_global, correo_global };
				finalizar_solicitud(
					id,
					"TIK_Negar",
					"Negar Solicitud",
					"¿Está seguro que desea negar esta solicitud",
					data_solicitante
				);
			});
		}
	);
};

const ver_detalle_ticket = async (data) => {
	let {
		funcionario,
		fecha_solicitud,
		estado,
		estado_ticket,
		descripcion,
		fecha_cierre,
		id,
		asunto,
		url_adjunto,
		t_solucionado,
		t_asignacion,
		t_asignado,
		t_solucion,
	} = data;
	t_solucionado = parseInt(t_solucionado);
	t_asignacion = parseInt(t_asignacion);
	t_asignado = parseInt(t_asignado);
	t_solucion = parseInt(t_solucion);
	$(".asunto").html(asunto);
	$(".descripción").html(descripcion);
	$(".solicitante").html(funcionario);
	$(".fecha_registro").html(fecha_solicitud);
	$(".descripcion").html(descripcion);
	$(".motivo").html(estado_ticket);
	if (url_adjunto == null) {
		$(".adjunto").html("");
	} else {
		$(".adjunto").html(
			`<a target='_blank' href='${Traer_Server()}${ruta_evidencia}${url_adjunto}'>${url_adjunto}</span></a>`
		);
	}
	let datos = await listar_fecha_cierre(id);
	if (datos) {
		$(".fecha_cierre").html("");
		$(".description").html("");
		datos.forEach((element) => {
			if (
				element.id_estado_ticket != "TIK_Soluc" &&
				element.id_estado_ticket != "TIK_Negar" &&
				element.id_estado_ticket != "TIK_Anul"
			) {
				$(".fecha_cierre").html("");
				$(".description").html("");
			} else if (
				element.id_estado_ticket == "TIK_Soluc" ||
				element.id_estado_ticket == "TIK_Negar" ||
				element.id_estado_ticket == "TIK_Anul"
			) {
				$(".fecha_cierre").html(`${element.fecha_cierre}`);
				$(".description").html(`${element.descripcion}`);
			}
		});
	}else{
			$(".fecha_cierre").html("");
			$(".description").html("");
	}
	$(".t_solucion").html("");
	$(".c_solucion").html("");
	$(".t_asignacion").html("");
	$(".c_asignacion").html("");
	if (t_solucionado && t_asignacion) {
		let good = `<span style="color: green;"><strong>Bien</strong></span>`;
		let bad = `<span style="color: red;"><strong>Mal</strong></span>`;
		let horas_soluc = Math.trunc(t_solucionado / 60);
		let minutos_soluc = t_solucionado % 60;
		let horas_asig = Math.trunc(t_asignacion / 60);
		let minutos_asig = t_asignacion % 60;
		if (t_solucionado > t_solucion || t_asignacion > t_asignado) {
			if (t_solucionado > t_solucion && t_asignacion < t_asignado) {
				$(".t_asignacion").html(`${horas_asig + "h " + minutos_asig + "m"}`);
				$(".c_asignacion").html(`${good}`);
				$(".t_solucion").html(`${horas_soluc + "h " + minutos_soluc + "m"}`);
				$(".c_solucion").html(`${bad}`);
			} else if (t_asignacion > t_asignado && t_solucionado < t_solucion) {
				$(".t_asignacion").html(`${horas_asig + "h " + minutos_asig + "m"}`);
				$(".c_asignacion").html(`${bad}`);
				$(".t_solucion").html(`${horas_soluc + "h " + minutos_soluc + "m"}`);
				$(".c_solucion").html(`${good}`);
			} else if (t_asignacion > t_asignado && t_solucionado > t_solucion) {
				$(".t_asignacion").html(`${horas_asig + "h " + minutos_asig + "m"}`);
				$(".c_asignacion").html(`${bad}`);
				$(".t_solucion").html(`${horas_soluc + "h " + minutos_soluc + "m"}`);
				$(".c_solucion").html(`${bad}`);
			} else {
				$(".t_asignacion").html(`${horas_asig + "h " + minutos_asig + "m"}`);
				$(".c_asignacion").html(`${good}`);
				$(".t_solucion").html(`${horas_soluc + "h " + minutos_soluc + "m"}`);
				$(".c_solucion").html(`${bad}`);
			}
		} else if (t_solucionado <= t_solucion || t_asignacion <= t_asignado) {
			if (t_solucionado <= t_solucion && t_solucionado >= t_solucion) {
				$(".t_solucion").html(`${horas_soluc + "h " + minutos_soluc + "m"}`);
				$(".c_solucion").html(`${good}`);
			} else if (t_asignacion <= t_asignado && t_asignacion >= t_asignado) {
				$(".t_asignacion").html(`${horas_asig + "h " + minutos_asig + "m"}`);
				$(".c_asignacion").html(`${good}`);
			} else if (t_asignacion <= t_asignado && t_solucionado <= t_solucion) {
				$(".t_asignacion").html(`${horas_asig + "h " + minutos_asig + "m"}`);
				$(".c_asignacion").html(`${good}`);
				$(".t_solucion").html(`${horas_soluc + "h " + minutos_soluc + "m"}`);
				$(".c_solucion").html(`${good}`);
			} else {
				$(".t_asignacion").html(`${horas_asig + "h " + minutos_asig + "m"}`);
				$(".c_signacion").html(`${good}`);
				$(".t_solucion").html(`${horas_soluc + "h " + minutos_soluc + "m"}`);
				$(".c_solucion").html(`${good}`);
			}
		}
	} else {
		$(".t_solucion").html("");
		$(".t_asignacion").html("");
	}
	$("#modal_detalle_solicitud_tickets").modal();
};

const listar_funcionarios = (id_proceso) => {
	return new Promise((resolve) => {
		let url = `${ruta}listar_funcionarios`;
		consulta_ajax(url, { id_proceso }, (resp) => {
			resolve(resp);
		});
	});
};
const listar_fecha_cierre = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}fecha_cierre`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};

const listar_historial_estados = () => {
	$("#modal_historial_solicitud").modal();
	let id_solicitud = id_solicitud_global;
	consulta_ajax(`${ruta}listar_historial_estados`, { id_solicitud }, (resp) => {
		let i = 0;
		const myTable = $("#tabla_estado_solicitud").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					},
				},
				{
					data: "estado",
				},
				{
					render: function (data, type, full, meta) {
						if (!full.observacion) return "---";
						else return full.observacion;
					},
				},
				{
					data: "motivo_suspencion",
				},
				{
					data: "nombre_completo",
				},
				{
					data: "fecha_registro",
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});
	});
};

const finalizar_solicitud = (id, estado, title, mensaje) => {
	const gestionar_solicitud = (
		id,
		estado,
		title,
		mensaje,
		data_solicitante
	) => {
		swal(
			{
				title: title,
				text: mensaje,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, continuar!",
				cancelButtonText: "No, cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true,
			},
			(isConfirm) => {
				if (isConfirm) {
					cambiarEstado(id, estado);
					data_solicitante;
					enviar_correo_estado(estado, id, "");
				}
			}
		);
	};
	const gestionar_solicitud_texto = (
		id,
		estado,
		title,
		mensaje,
		data_solicitante
	) => {
		swal(
			{
				title: title,
				text: mensaje,
				type: "input",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Aceptar!",
				cancelButtonText: "Cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true,
				inputPlaceholder: "Ingrese Motivo",
			},
			function (mensaje) {
				if (mensaje === false) return false;
				if (mensaje === "") {
					swal.showInputError("Debe Ingresar el motivo.!");
				} else {
					cambiarEstado(id, estado, mensaje);
					data_solicitante;
					enviar_correo_estado(estado, id, mensaje);
					return false;
				}
			}
		);
	};

	if (estado == "TIK_Anul")
		gestionar_solicitud_texto(id, estado, title, mensaje);
	else if (estado == "TIK_Proce")
		gestionar_solicitud(id, estado, title, mensaje);
	else if (estado == "TIK_Asig")
		gestionar_solicitud_texto(id, estado, title, mensaje);
	else if (estado == "TIK_Negar")
		gestionar_solicitud_texto(id, estado, title, mensaje);
	else gestionar_solicitud(id, estado, title, mensaje);
};

const buscar_especialista = (
	dato,
	id_nvl_impacto,
	id_nvl_prioridad,
	id_nvl_urgencia,
	callback_activo
) => {
	tabla = "#tabla_empleado_especialista";
	consulta_ajax(
		`${ruta}buscar_especialista`,
		{
			dato,
			tipo_solicitud_id,
			id_estado_solicitud,
			correo_global,
			nombre_usuario_global,
			id_nvl_impacto,
			id_nvl_urgencia,
			id_nvl_prioridad,
		},
		(resp) => {
			const callback_activo = (dato) => {
				const { id, nombre_completo } = dato;
				id_persona = id;
				$("#txt_especialista").val(nombre_completo);
				$("#modal_buscar_persona").modal("hide");
			};
			$(`${tabla} tbody`)
				.off("click", "tr td .especialista")
				.off("dblclick", "tr")
				.off("click", "tr")
				.off("click", "tr td:nth-of-type(1)");
			let i = 0;
			const myTable = $(tabla).DataTable({
				destroy: true,
				searching: false,
				processing: true,
				data: resp,
				columns: [
					{
						render: function (data, type, full, meta) {
							i++;
							return i;
						},
					},
					{
						data: "nombre_completo",
					},
					{
						data: "usuario",
					},
					{
						data: "hora_ini",
					},
					{
						data: "hora_fin",
					},
					{
						defaultContent:
							'<span style="color: #39B23B;" title="Seleccionar Especialista" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default especialista" ></span>',
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});
			$(`${tabla} tbody`).on("click", "tr", function () {
				$(`${tabla} tbody tr`).removeClass("warning");
				$(this).attr("class", "warning");
			});

			$(`${tabla} tbody`).on("dblclick", "tr", function () {
				let data = myTable.row($(this).parent().parent()).data();
				callback_activo(data);
			});

			$(`${tabla} tbody`).on("click", "tr td .especialista", function () {
				let data = myTable.row($(this).parent().parent()).data();
				callback_activo(data);
			});
		}
	);
};

const buscar_empleado = (dato, callbak_activo) => {
	tabla = "#tabla_empleado_buscar";
	consulta_ajax(`${ruta}buscar_empleado`, { dato }, (resp) => {
		const callbak_activo = (dato) => {
			const { id, nombre_completo } = dato;
			id_persona = id;
			$("#txt_buscar_persona").val(nombre_completo);
			$("#modal_buscar_empleado").modal("hide");
		};
		$(`${tabla} tbody`)
			.off("dblclick", "tr")
			.off("click", "tr")
			.off("click", "tr td:nth-of-type(1)")
			.off("click", "tr td .empleado");
		let i = 0;
		const myTable = $(tabla).DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					},
				},
				{
					data: "nombre_completo",
				},
				{
					data: "usuario",
				},
				{
					defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Empleado" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default empleado" ></span>',
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
		$(`${tabla} tbody`).on("click", "tr", function () {
			$(`${tabla} tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});

		$(`${tabla} tbody`).on("dblclick", "tr", function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak_activo(data);
		});

		$(`${tabla} tbody`).on("click", "tr td .empleado", function () {
			let data = myTable.row($(this).parent()).data();
			callbak_activo(data);
		});
	});
};

const listar_personas = (texto = "") => {
	consulta_ajax(`${ruta}listar_personas`, { texto }, (data) => {
		$(`#tabla_personas tbody`)
			.off("click", "tr")
			.off("click", "tr span.asignar")
			.off("dblclick", "tr");
		const myTable = $("#tabla_personas").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ data: "fullname" },
				{
					render: (data, type, full, meta) =>
						'<span class="btn btn-default asignar" title="Seleccionar Persona" style="color: #5cb85c"><span class="fa fa-check"></span></span>',
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		$("#tabla_personas tbody").on("click", "tr", function () {
			$("#tabla_personas tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_personas tbody").on("click", "tr span.asignar", function () {
			let { id, fullname } = myTable.row($(this).parent().parent()).data();
			id_persona = id;
			$("#modal_elegir_persona").modal("hide");
			$("#s_persona").html(fullname);
			listar_actividades(id);
		});

		$("#tabla_personas tbody").on("dblclick", "tr", function () {
			let { id, fullname } = myTable.row($(this)).data();
			id_persona = id;
			$("#modal_elegir_persona").modal("hide");
			$("#s_persona").html(fullname);
			listar_actividades(id);
		});
	});
};
const listar_actividades = (persona) => {
	let num = 0;
	consulta_ajax(`${ruta}listar_actividades`, { persona }, (data) => {
		$(`#tabla_actividades tbody`)
			.off("click", "tr")
			.off("click", "tr span.asignar")
			.off("click", "tr span.quitar")
			.off("click", "tr span.config")
			.off("dblclick", "tr");
		const myTable = $("#tabla_actividades").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ render: () => ++num },
				{ data: "nombre" },
				{
					render: (data, type, { asignado }, meta) => {
						let datos = asignado
							? '<span class="btn btn-default quitar" style="color: #5cb85c"><span class="fa fa-toggle-on"></span></span> <span class="btn btn-default config"><span class="fa fa-cog"></span></span>'
							: '<span class="btn btn-default asignar"><span class="fa fa-toggle-off" ></span></span> ';
						return datos;
					},
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		$("#tabla_actividades tbody").on("click", "tr", function () {
			$("#tabla_actividades tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_actividades tbody").on("dblclick", "tr", function () {
			$("#tabla_actividades tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_actividades tbody").on("click", "tr span.asignar", function () {
			const { asignado, id } = myTable.row($(this).parent()).data();
			asignar_actividad(asignado, id);
		});

		$("#tabla_actividades tbody").on("click", "tr span.quitar", function () {
			const { asignado, id } = myTable.row($(this).parent()).data();
			quitar_actividad(asignado, id);
		});

		$("#tabla_actividades tbody").on("click", "tr span.config", function () {
			const { asignado, id } = myTable.row($(this).parent()).data();
			actividad_selec = asignado;
			$("#modal_elegir_estado").modal();
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
				title: "Desasignar Actividad",
				text:
					"Tener en cuenta que al desasignarle esta actividad al usuario no podrá visualizar ninguna solicitud de este tipo.",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Entiendo!",
				cancelButtonText: "No, cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true,
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

const listar_estados = (actividad, persona) => {
	const desasignar =
		'<span class="btn btn-default desasignar" title="Quitar Estado"><span class="fa fa-toggle-on" style="color: #5cb85c"></span></span> ';
	const asignar =
		'<span class="btn btn-default asignar" title="Asignar Estado"><span class="fa fa-toggle-off"></span></span> ';
	//const notificar =
	//	'<span class="btn btn-default notificar" title="Activar Notificación"><span class="fa fa-bell-o"></span></span> ';
	//const no_notificar =
	//	'<span class="btn btn-default no_notificar" title="Desactivar Notificación"><span class="fa fa-bell red"></span></span> ';
	consulta_ajax(
		`${ruta}listar_estados`,
		{ actividad, persona: id_persona },
		(data) => {
			$(`#tabla_estados tbody`)
				.off("click", "tr")
				.off("click", "tr span.asignar")
				.off("click", "tr span.desasignar")
				.off("click", "tr span.no_notificar")
				.off("click", "tr span.notificar")
				.off("dblclick", "tr");
			const myTable = $("#tabla_estados").DataTable({
				destroy: true,
				processing: true,
				searching: true,
				data,
				columns: [
					{ data: "parametro" },
					{ data: "nombre" },
					{
						render: (data, type, { asignado, notificacion }, meta) => {
							let acciones = "";
							if (asignado) {
								acciones = desasignar;
								//acciones += notificacion === "1" ? no_notificar : notificar;
							} else {
								acciones = asignar;
							}
							return acciones;
						},
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});

			$("#tabla_estados tbody").on("click", "tr", function () {
				$("#tabla_estados tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});

			$("#tabla_estados tbody").on("click", "tr span.asignar", function () {
				const { estado } = myTable.row($(this).parent()).data();
				asignar_estado(estado, actividad_selec, id_persona);
			});

			$("#tabla_estados tbody").on("click", "tr span.desasignar", function () {
				const { asignado, estado } = myTable.row($(this).parent()).data();
				quitar_estado(estado, actividad_selec, id_persona, asignado);
			});

			$("#tabla_estados tbody").on("click", "tr span.notificar", function () {
				const { asignado, estado } = myTable.row($(this).parent()).data();
				activar_notificacion(estado, actividad_selec, id_persona, asignado);
			});

			$("#tabla_estados tbody").on(
				"click",
				"tr span.no_notificar",
				function () {
					const { asignado, estado } = myTable.row($(this).parent()).data();
					desactivar_notificacion(
						estado,
						actividad_selec,
						id_persona,
						asignado
					);
				}
			);

			const activar_notificacion = (estado, actividad, persona, id) => {
				consulta_ajax(
					`${ruta}activar_notificacion`,
					{ estado, actividad, persona, id },
					({ mensaje, tipo, titulo }) => listar_estados(actividad, persona)
				);
			};

			const desactivar_notificacion = (estado, actividad, persona, id) => {
				consulta_ajax(
					`${ruta}desactivar_notificacion`,
					{ estado, actividad, persona, id },
					({ mensaje, tipo, titulo }) => listar_estados(actividad, persona)
				);
			};

			const asignar_estado = (estado, actividad, persona) => {
				consulta_ajax(
					`${ruta}asignar_estado`,
					{ estado, actividad, persona },
					({ mensaje, tipo, titulo }) => listar_estados(actividad)
				);
			};

			const quitar_estado = (estado, actividad, persona, id) => {
				consulta_ajax(
					`${ruta}quitar_estado`,
					{ estado, actividad, persona, id },
					({ mensaje, tipo, titulo }) => listar_estados(actividad)
				);
			};
		}
	);
};
const seleccionar_tickets_indi = (id, thiss) => {
	$("#tbl_tickets tbody tr").removeClass("warning");
	$(thiss).attr("class", "warning");
};

const listar_horarios_funcionarios = (parametro = "") => {
	consulta_ajax(
		`${ruta}listar_horarios_funcionarios`,
		{ parametro },
		(resp) => {
			$(`#tabla_horarios tbody`)
				.off("dblclick", "tr")
				.off("click", "tr")
				.off("click", "tr .funcionario")
				.off("click", "tr .modificar")
				.off("click", "tr .eliminar");
			const myTable = $("#tabla_horarios").DataTable({
				destroy: true,
				searching: true,
				processing: true,
				data: resp,
				columns: [
					{
						data: "dia",
					},
					{
						data: "hora_inicio",
					},
					{
						data: "hora_break",
					},
					{
						data: "tiempo_break",
					},
					{
						data: "hora_fin",
					},
					{
						data: "accion",
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});
			$("#tabla_horarios tbody").on("click", "tr", function () {
				$("#tabla_horarios tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});
			$("#tabla_horarios tbody").on("dblclick", "tr", function () {
				$("#tabla_horarios tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});
			$("#tabla_horarios tbody").on("click", "tr .funcionario", function () {
				let { id } = myTable.row($(this).parent().parent()).data();
				id_horario = id;
				listar_funcionarios_horarios(id_horario);
				$("#modal_funcionarios_horarios").modal();
			});
			$("#tabla_horarios tbody").on("click", "tr .modificar", function () {
				let data = myTable.row($(this).parent()).data();
				ver_horario(data);
			});
			$("#tabla_horarios tbody").on("click", "tr .eliminar", function () {
				let { id } = myTable.row($(this).parent().parent()).data();
				id_horario = id;
				eliminar_horario_funcionario(id);
			});
		}
	);
};
const ver_horario = (data) => {
	let {
		id,
		id_dia,
		hora_inicio,
		hora_break,
		tiempo_break,
		hora_fin,
		observacion,
	} = data;
	id_horario = id;
	$(".titulo_modal").html("Modificar Horario");
	$("#id_dia").val(id_dia);
	$("#hora_inicio").val(hora_inicio);
	$("#hora_fin").val(hora_fin);
	$("#hora_break").val(hora_break);
	$("#tiempo_break").val(tiempo_break);
	$("#descripcion").val(observacion);
	$("#modal_crear_horario").modal();
};
const guardar_horario_funcionario = () => {
	let fordata = new FormData(document.getElementById("form_guardar_horario"));
	let data = formDataToJson(fordata);
	data.id_horario = id_horario;
	consulta_ajax(`${ruta}guardar_horario_funcionario`, data, (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			id_horario = "";
			$("#form_guardar_horario").get(0).reset();
			$("#modal_crear_horario").modal("hide");
			listar_horarios_funcionarios();
		}
		MensajeConClase(mensaje, tipo, titulo);
	});
};
const eliminar_horario_funcionario = (id) => {
	swal(
		{
			title: "Estas Seguro ?",
			text:
				"Si desea eliminar el horario presione la opción de 'Si, Entiendo'.!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Entiendo!",
			cancelButtonText: "No, Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
		},
		function (isConfirm) {
			if (isConfirm) {
				consulta_ajax(`${ruta}eliminar_horario_funcionario`, { id }, (resp) => {
					let { titulo, mensaje, tipo } = resp;
					if (tipo == "success") {
						swal.close();
						listar_horarios_funcionarios();
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
};
const listar_funcionarios_horarios = (id_horario) => {
	consulta_ajax(
		`${ruta}listar_funcionarios_horarios`,
		{ id_horario },
		(resp) => {
			$(`#tabla_funcionarios_horarios tbody`)
				.off("dblclick", "tr")
				.off("click", "tr")
				.off("click", "tr .eliminar");
			let i = 0;
			const myTable = $("#tabla_funcionarios_horarios").DataTable({
				destroy: true,
				searching: true,
				processing: true,
				data: resp,
				columns: [
					{
						render: function (data, type, full, meta) {
							i++;
							return i;
						},
					},
					{
						data: "nombre_completo",
					},
					{
						data: "identificacion",
					},
					{
						data: "accion",
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});
			$("#tabla_funcionarios_horarios tbody").on("click", "tr", function () {
				$("#tabla_funcionarios_horarios tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});
			$("#tabla_funcionarios_horarios tbody").on("dblclick", "tr", function () {
				$("#tabla_funcionarios_horarios tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});
			$("#tabla_funcionarios_horarios tbody").on(
				"click",
				"tr .eliminar",
				function () {
					let { id } = myTable.row($(this).parent().parent()).data();
					eliminar_funcionario_horario(id);
				}
			);
		}
	);
};
const buscar_persona = (data, callbak) => {
	consulta_ajax(`${ruta}buscar_persona`, data, (resp) => {
		$(`#tabla_personas_busqueda tbody`)
			.off("click", "tr td .funcionario")
			.off("dblclick", "tr")
			.off("click", "tr")
			.off("click", "tr td:nth-of-type(1)");
		let i = 0;
		const myTable = $("#tabla_personas_busqueda").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					},
				},
				{
					data: "nombre_completo",
				},
				{
					data: "identificacion",
				},
				{
					defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Funcionario" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default funcionario" ></span>',
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
		$("#tabla_personas_busqueda tbody").on("click", "tr", function () {
			$("#tabla_personas_busqueda tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});
		$("#tabla_personas_busqueda tbody").on("dblclick", "tr", function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
		});
		$("#tabla_personas_busqueda tbody").on(
			"click",
			"tr td .funcionario",
			function () {
				let data = myTable.row($(this).parent().parent()).data();
				callbak(data);
				funcionario_nombre = data.nombre_completo;
				funcionario_correo = data.correo;
				listar_funcionarios();
			}
		);
	});
};
const guardar_funcionario_horario = (id_persona, id_horario) => {
	consulta_ajax(
		`${ruta}guardar_funcionario_horario`,
		{ id_persona, id_horario },
		(resp) => {
			let { titulo, mensaje, tipo } = resp;
			if (tipo == "success") {
				swal.close();
				$("#form_buscar_persona_horario").get(0).reset();
				$("#modal_buscar_persona_horario").modal("hide");
				listar_funcionarios_horarios(id_horario);
			}
			MensajeConClase(mensaje, tipo, titulo);
		}
	);
};
const eliminar_funcionario_horario = (id) => {
	swal(
		{
			title: "Estas Seguro ?",
			text:
				"Si desea eliminar el funcionario presione la opción de 'Si, Entiendo'.!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Entiendo!",
			cancelButtonText: "No, Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
		},
		function (isConfirm) {
			if (isConfirm) {
				consulta_ajax(`${ruta}eliminar_funcionario_horario`, { id }, (resp) => {
					let { titulo, mensaje, tipo } = resp;
					if (tipo == "success") {
						swal.close();
						listar_funcionarios_horarios(id_horario);
					} else MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
};
const obtener_valor_parametro = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_valor_parametro`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};
const obtener_tipos_solicitud = async (select, mensaje) => {
	let tipos = await obtener_valor_parametro(237);
	pintar_datos_combo_1(tipos, `.${select}`, mensaje, 1);
};
const obtener_estado = async (select, mensaje) => {
	let estados = await obtener_valor_parametro(232);
	pintar_datos_combo_1(estados, `.${select}`, mensaje);
};
const filtrar_solicitudes = () => {
	data = {
		id_estado_sol: $("#modal_crear_filtros select[name='id_estado']").val(),
		fecha_inicial: $("#modal_crear_filtros input[name='fecha_inicial']").val(),
		fecha_final: $("#modal_crear_filtros input[name='fecha_final']").val(),
		hora_fin_filtro: $(
			"#modal_crear_filtros input[name='hora_fin_filtro']"
		).val(),
		hora_inicio_filtro: $(
			"#modal_crear_filtros input[name='hora_inicio_filtro']"
		).val(),
		id_tipo_solicitud: $(
			"#modal_crear_filtros select[name='id_tipo_solicitud']"
		).val(),
	};
	listado_solicitudes(0, data);
};
const enviar_correo_estado = async (estado, id, motivo) => {
	let sw = false;
	let { nombre, correo } = data_solicitante;
	let ser = `<a href="${server}index.php/tickets/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 3;
	let titulo = "";
	let nombre_solicitante = nombre;
	let mensaje = `Se informa que hay una solicitud realizada por ${nombre}, lista para ser gestionada por usted, a partir de este momento puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentra la solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;

	switch (estado) {
		case "TIK_Regis":
			tipo = -1;
			sw = true;
			titulo = "Solicitud Registrada";
			mensaje = `Se informa que su solicitud ha sido registrada con exito, a partir de este momento puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentra la solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
			break;
		case "TIK_Proce":
			tipo = 1;
			sw = true;
			titulo = "Solicitud en Proceso";
			mensaje = `Se informa que su solicitud se encuentra en proceso.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
			break;
		case "TIK_Suspen":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Suspendida";
			mensaje = `Se informa que su solicitud se encuentra suspendida, por el siguiente motivo: <br> ${motivo}.<br> <br><br>Mas informaci&oacuten en: ${ser}<br>`;
			break;
		case "TIK_Soluc":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Finalizada";
			mensaje = `Se informa que se ha brindado una solución a su solicitud .<br><br>Mas informaci&oacuten en: ${ser}<br>`;
			break;
		case "TIK_Anul":
			tipo = -1;
			sw = true;
			titulo = "Solicitud Cancelada";
			mensaje = `Se informa que su solicitud ha sido cancelada por el usuario, por el siguiente motivo: <br> ${motivo}.<br><br>Mas informaci&oacuten en ${ser}`;
			break;
		case "TIK_Asig":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Asignada";
			mensaje = `Se informa que se ha asignado un especialista a su solicitud. <br><br>Mas informaci&oacuten en ${ser}`;
			break;
		case "TIK_Negar":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Negada";
			mensaje = `Se informa que su solicitud ha sido negada, por el siguiente motivo: <br> ${motivo}.<br> <br><br>Mas informaci&oacuten en ${ser}`;
			break;
	}
	if (sw)
		enviar_correo_personalizado(
			"blab",
			mensaje,
			correo,
			nombre,
			"Tickets CUC",
			`Tickets AGIL - ${titulo}`,
			"ParCodAdm",
			tipo
		);
};

const guardar_tiempo_ticket = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}guardar_tiempo_ticket`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};
