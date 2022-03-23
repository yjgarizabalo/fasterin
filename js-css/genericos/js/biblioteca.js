let ruta = `${Traer_Server()}index.php/biblioteca_control/`;
let estudiantes = [];
let callbak_activo = resp => { };
let tipo_solicitud = null;
let id_solicitud = null;
let id_especifica = null;
let id_libro = null;
let id_aux = null;
let libros = [];
let capacitaciones = [];
let aux_entrega = {};
let aux_retira = {};
let solicitante = {};
let datos_estado = {};
let estado_solicitud = "";
let tipo_busqueda = null;
let callbak_estado = (data) => { };
let cambio = false;
let sw_cal = 0;
let close_cal = false;
let causas_gen = [];
let materias = [];
$(document).ready(() => {
	$("#bloque").change(function () {
		const bloque = $(this).val();
		listar_bloque_salon(bloque);
	});
	$("#bloque_cap").change(function () {
		const bloque = $(this).val();
		listar_bloque_salon(bloque);
	})
	$(".cbx_aux_bib").change(function () {
		const aux_perm = $(this).val();
		listar_procesos(aux_perm);
	});
	$("#hora_inicio").change(() => {
		let hora_inicio = $("#hora_inicio").val();
		$("#hora_fin").val(asignar_hora_fin(hora_inicio));
	});
	$("#recurso_cap").change(() => {
		let recurso = $("#recurso_cap").val();
		if (recurso == 'Bib_Com_R') {
			$("#bloque_cap").attr("required", true);
			$("#salon_cap").attr("required", true);
			$("#cont_bloque_salon").show();
		} else {
			$("#bloque_cap").removeAttr("required");
			$("#salon_cap").removeAttr("required");
			$("#cont_bloque_salon").hide();
		}
		listar_bloques_cap(recurso);
	});
	$("#form_modificar_solicitud input[name=hora_entrega]").change(() => {
		if (tipo_solicitud == 'Bib_Cap') {
			let hora_inicio = $("#form_modificar_solicitud input[name=hora_entrega]").val()
			$("#form_modificar_solicitud input[name=hora_retiro]").val(asignar_hora_fin(hora_inicio, capacitaciones));
		}
	});
	$("#form_modificar_solicitud select[name=id_bloque]").change(async function () {
		const id_bloque = $(this).val();
		let salones = await obtener_bloque_salon(id_bloque);
		pintar_datos_combo(
			salones,
			"#form_modificar_solicitud select[name=id_salon]",
			"Seleccione Salon"
		);
	});
	$('#admin_perm').click(function () {
		$("#nav_admin_bib li").removeClass("active");
		$(this).addClass("active");
		$("#container_admin_bib").fadeIn(1000);
		$("#container_turnos_bib").css("display", "none");
	});
	$('#admin_turn').click(function () {
		$("#nav_admin_bib li").removeClass("active");
		$(this).addClass("active");
		$("#container_turnos_bib").fadeIn(1000);
		$("#container_admin_bib").css("display", "none");
	});

	$(".regresar_menu").click(function () {
		$("#container-listado-eventos").css("display", "none");
		$("#menu_principal").fadeIn(1000);
	});
	$("#agregarLibro").click(function () {
		$("#modal_agregar_libro").modal();
	});
	$("#agregarCapa").click(function () {
		listar_nivel_capa();
		$("#modal_agregar_capacitacion").modal();
	});
	$("#agregarCapaMod").click(function () {
		listar_nivel_capa();
		$("#modal_agregar_capacitacion").modal();
	});
	$("#agregar_libro_nuevo").click(function () {
		$("#modal_agregar_libro_nuevo").modal();
	});
	$("#agregar_auxiliar").click(function () {
		close_cal = false
		tipo_busqueda = "aux_bib";
		tabla = "#tabla_empleado_busqueda"
		$("#txt_dato_buscar").val("");
		listar_acciones(tipo_solicitud);
		let month = new Date().getMonth() + 1
		buscar_empleado("", "", tabla, month, '', id_solicitud);
		$("#modal_buscar_empleado").modal();
	});
	$("#agregar_auxiliar_mod").click(function () {
		close_cal = true;
		tipo_busqueda = "aux_bib";
		tabla = "#tabla_empleado_busqueda"
		$("#txt_dato_buscar").val("");
		listar_acciones(tipo_solicitud);
		buscar_empleado("", "", tabla, new Date().getMonth() + 1, { sw: false }, id_solicitud);
		$("#modal_auxiliares_mod").modal("hide");
		$("#modal_detalle_solicitud").modal("hide");
		$("#modal_buscar_empleado").modal();
	});
	$("#btn_cerrar_aux").click(function () {
		if (close_cal) {
			$("#modal_auxiliares_mod").modal();
			$("#modal_detalle_solicitud").modal();
			$("#modal_buscar_empleado").modal('hide');
		} else {
			$("#modal_buscar_empleado").modal('hide');
		}
	})
	$(".btn_new_turn").click(function () {
		$("#modal_agregar_turno").modal();
	});
	$("#tabla_libro_solicitudes .btnAgregar").click(function () {
		$("#modal_agregar_libro_cod").modal();
	});
	$("#tabla_preparacion_solicitud .btnAgregar").click(function () {
		$("#modal_agregar_libro_cod").modal();
	});
	$("#removerLibro").click(function () {
		eliminar_libros_agregados();
	});
	$("#removerCapa").click(function () {
		eliminar_capacitaciones_agregadas("new");
	});
	$("#removerCapaMod").click(function () {
		eliminar_capacitaciones_agregadas("mod");
	});
	$("#listado_solicitudes").click(function () {
		$("#menu_principal").css("display", "none");
		listar_solicitud();
		$("#container-listado-eventos").fadeIn(1000);
	});
	$("#nueva_solicitud").click(function () {
		$("#tematicas_bib_lib").hide();
		$("#niveles_bib_cap").hide();
		$("#modal_agregar_solicitud").modal();
		estudiantes = [];
		libros = [];
		$(".f_inicio").removeAttr("placeholder");
		$(".f_inicio").attr("placeholder", "Fecha de Prestamo");
		$("#hora_fin").removeAttr("disabled");
		//pintar_materias_docente("**F");
		$("#tematicas_bib_lib").show();
		libros_agregados();
		tipo_solicitud = "Bib_Lib";
		$("#form_agregar_solicitud")
			.get(0)
			.reset();
		listar_estudiantes();
	});

	$("#nueva_sol_capa").click(function () {
		$("#tematicas_bib_lib").hide();
		$("#niveles_bib_cap").hide();
		$("#modal_agregar_solicitud").modal();
		estudiantes = [];
		capacitaciones = [];
		$(".f_inicio").removeAttr("placeholder");
		$(".f_inicio").attr("placeholder", "Fecha de Solicitud");
		$("#hora_fin").attr("disabled", "disabled");
		//pintar_materias_docente("**F");
		$("#niveles_bib_cap").show();
		capacitaciones_agregadas("new");
		tipo_solicitud = "Bib_Cap";
		$("#form_agregar_solicitud")
			.get(0)
			.reset();
		listar_estudiantes();
	})
	$("#agregar_estudiantes").click(function () {
		tipo_busqueda = null;
		cambio = true;
		$("#form_buscar_estudiante")
			.get(0)
			.reset();
		callbak_activo = data => {
			let estudiante = estudiantes.find(element => element.identificacion == data.identificacion);
			if (estudiante)
				MensajeConClase("El estudiante ya fue asignado.", "info", "Oops.!");
			else {
				estudiantes.push(data);
				let table = $("#tabla_estudiantes_busqueda").DataTable();
				table.clear().draw();
				MensajeConClase(
					"Estudiante asignado con exito",
					"success",
					"Proceso Exitoso"
				);
			}
		};
		buscar_estudiante();
		$("#modal_buscar_estudiante").modal();
	});
	$("#btn_nuevo_estudiante").click(() => {
		$("#modal_nuevo_estudiante").modal();
	});

	$("#form_nuevo_estudiante").submit(() => {
		guardar_nuevo_estudiante();
		return false;
	});
	$("#btn_calendario").click(() => {
		sw_cal = 2;
		listar_empleados();
		carga_empleado();
		$("#botones_cal").show();
		$("#modal_carga").modal();
	});
	$("#form_agregar_turno").submit(() => {
		guardar_turno();
		return false;
	})

	$("#agregar_estudiantes_nuevos").click(function () {
		tipo_busqueda = null
		cambio = false;
		$("#form_buscar_estudiante")
			.get(0)
			.reset();
		callbak_activo = data => {
			swal(
				{
					title: "Estas Seguro ?",
					text: `Esta seguro de guardar a ${data.nombre_completo
						}, si desea continuar presione la opción de 'Si, Entiendo'.!`,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Si, Entiendo!",
					cancelButtonText: "No, Cancelar!",
					allowOutsideClick: true,
					closeOnConfirm: false,
					closeOnCancel: true
				},
				function (isConfirm) {
					if (isConfirm) {
						id_persona = data.id;
						guardar_estudiante_nuevo(id_persona, id_solicitud, data.tabla, data.identificacion);
					}
				}
			);
		};
		buscar_estudiante();
		$("#modal_buscar_estudiante").modal();
	});
	$("#form_buscar_estudiante").submit(() => {
		let dato = $("#txt_est_buscar").val();
		let tabla = $("input[name='tabla']:checked").val();
		if (tabla == null) buscar_estudiante(dato, callbak_activo, "");
		else buscar_estudiante(dato, callbak_activo, tabla);
		return false;
	});
	$("#form_buscar_empleado").submit(() => {
		let dato = $("#txt_dato_buscar").val();
		tabla = ("#tabla_empleado_busqueda")
		buscar_empleado(dato, "", tabla, new Date().getMonth() + 1, { sw: false }, id_solicitud);
		return false;
	});
	$(".event").click(() => {
		$("#events-modal").modal();
	});
	$("#form_buscar_empleado_sol").submit(() => {
		let dato = $("#txt_dato_buscar_sol").val();
		tabla = ("#tabla_empleado_busqueda_sol");
		buscar_empleado(dato, callbak_activo, tabla);
		return false;
	});
	$("#form_agregar_solicitud").submit(() => {
		guardar_solicitud();
		return false;
	});
	$("#form_agregar_libro_nuevo").submit(() => {
		guardar_libros_nuevo(id_solicitud, "form_agregar_libro_nuevo");
		$("#libro_input_new").val("");
		return false;
	});
	$("#form_agregar_libro_cod").submit(() => {
		guardar_libros_nuevo(id_solicitud, "form_agregar_libro_cod");
		$("#libro_codigo").val("");
		$("#nombre_libro_cod").val("");
		return false;
	});
	$("#form_gestionar").submit(() => {
		callbak_estado(datos_estado);
		return false;
	});
	$("#form_agregar_libro").submit(() => {
		let libro = $("#libro_input").val();
		libros.push(libro);
		libros_agregados();
		$("#form_agregar_libro")
			.get(0)
			.reset();
		MensajeConClase("El libro fue agregado correctamente.", "success", "Proceso Exitoso!");
		return false;
	});

	$("#btn_solicitante").click(() => {
		tipo_busqueda = null;
		tabla = "#tabla_empleado_busqueda_sol";
		container_activo = "#txt_solicitante";
		$("#txt_dato_buscar_sol").val("");
		callbak_activo = resp => {
			mostrar_nombre_persona(resp, 3);
		};
		buscar_empleado("**WEF*we", callbak_activo, tabla);
		$("#modal_buscar_empleado_sol").modal();
	});

	$("#btn_estudiantes").click(() => {
		$("#modal_estudiantes_solicitud").modal();
	});

	$(".btnLog").click(() => {
		$("#modal_historial_solicitud").modal();
		historial_solicitud(id_solicitud);
	});

	$(".btnAsig").click(() => {
		$("#modal_historial_libro").modal();
		historial_libro(id_libro);
	});

	$(".btn_log_aux").click(() => {
		$("#modal_historial_auxiliar").modal();
		historial_auxiliar(id_aux);
	});

	$(".btnEncuesta").click(() => {
		$("#modal_encuestas").modal();
		listar_encuestas(id_solicitud);
	});

	$("#btn_administrar").click(() => {
		listar_procesos();
		listar_empleados();
		listar_turnos();
		$("#administrar_biblioteca").modal();
	});

	$('#con_bib_lib').click(function () {
		$("#nav_con_bib li").removeClass("active");
		$(this).addClass("active");
		$("#container_bib_lib").fadeIn(1000);
		$("#container_bib_cap").css("display", "none");
		listar_consolidado("tabla_con_bib_lib");
	});
	$('#con_bib_cap').click(function () {
		$("#nav_con_bib li").removeClass("active");
		$(this).addClass("active");
		$("#container_bib_cap").fadeIn(1000);
		$("#container_bib_lib").css("display", "none");
		listar_consolidado("tabla_con_bib_cap");
	});

	$("#btn_consolidado").click(() => {
		listar_consolidado("tabla_con_bib_lib");
		$("#modal_consolidado_biblioteca").modal();
	})

	$("#btn_modificar").click(async () => {
		if (id_solicitud) {
			if (tipo_solicitud == 'Bib_Lib') {
				$("#niveles_capa").hide();
				if (estado_solicitud != 'Bib_Sol_E') {
					MensajeConClase("No se puede modificar la solicitud, debido que se encuentra en procesamiento o ya fue finalizada.", "info", "Oops")
				} else {
					capacitaciones = [];
					$("#modal_modificar_solicitud").modal();
					detalles_modificar_sol(id_solicitud);
					$("#form_modificar_solicitud input[name=hora_retiro]").removeAttr("disabled", "disabled");
					$("#form_modificar_solicitud").off("submit");
					$("#form_modificar_solicitud").submit(() => {
						modificar_solicitud();
						listar_solicitud();
						return false;
					});
				}
			} else if (tipo_solicitud == 'Bib_Cap') {
				$("#niveles_capa").show();
				if (estado_solicitud != 'Bib_Sol_E') {
					MensajeConClase("No se puede modificar la solicitud, debido que se encuentra en procesamiento o ya fue finalizada.", "info", "Oops")
				} else {
					capacitaciones = [];
					capacitaciones = await obtener_capacitaciones(id_solicitud);
					capacitaciones_agregadas("mod");
					$("#modal_modificar_solicitud").modal();
					detalles_modificar_sol(id_solicitud);
					$("#form_modificar_solicitud input[name=hora_retiro]").attr("disabled", "disabled");
					$("#form_modificar_solicitud").off("submit");
					$("#form_modificar_solicitud").submit(() => {
						modificar_solicitud();
						listar_solicitud();
						return false;
					});
				}
			}
		} else {
			MensajeConClase("Seleccione solicitud a modificar.", "info", "Oops");
		}
	});

	$("#limpiar_filtros").click(() => {
		$("#modal_crear_filtros select[name='id_tipo_solicitud']").val('');
		$("#modal_crear_filtros select[name='id_estado_solicitud']").val('');
		$("#modal_crear_filtros input[name='fecha_inicial']").val('');
		$("#modal_crear_filtros input[name='fecha_final']").val('');
		listar_solicitud();
	});
	$("#btn_filtrar").click(() => {
		listar_solicitud();
	});
	$("#logeo_biblioteca").submit(() => {
		logear_biblioteca();
		return false;
	});
	$("#btn_filtrar_cal").click(() => {
		let id = $(".cbx_aux_bib").val();
		let tipo = $(".cbx_tipo_bib").val();
		let fecha_inicio = $("#fecha_ini_cal").val();
		let fecha_fin = $("#fecha_fin_cal").val();
		carga_empleado(id, tipo, fecha_inicio, fecha_fin, '', sw_cal);
	});
	$("#form_agregar_solicitud select[name=id_materia]").change(async function () {
		MensajeConClase("Estamos validando la información...", "add_inv", "Oops...");
		let materia = $(this).val();
    let codigo_grupo = $(`#form_agregar_solicitud select[name=id_materia]`).find(":selected").data("grupo");
		estudiantes = await obtener_estudiantes_por_materia(codigo_grupo);
		listar_estudiantes();
	});
	$("#btn_descargar_reseña").click(() => {
		if (id_solicitud && tipo_solicitud == 'Bib_Cap') {
			$("#btn_descargar_reseña").attr("href", `${Traer_Server()}index.php/biblioteca/descargar_nivel/${id_solicitud}`);
		} else {
			$("#btn_descargar_reseña").removeAttr("href");
			MensajeConClase("No ha seleccionado una solicitud o la solicitud seleccionada no cuenta con formato a descargar", "info", "Oops.!");
		}
	});
});

const modificar_solicitud = () => {
	let fordata = new FormData(document.getElementById("form_modificar_solicitud"));
	let data = formDataToJson(fordata);
	data.tipo_solicitud = tipo_solicitud;
	data.id_solicitud = id_solicitud;
	data.id_estado_solicitud = estado_solicitud;
	data.capacitaciones = capacitaciones;
	consulta_ajax(`${ruta}modificar_solicitud`, data, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_modificar_solicitud")
				.get(0)
				.reset();
			$("#modal_modificar_solicitud").modal("hide");
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}


const detalles_modificar_sol = (id) => {
	consulta_ajax(
		`${ruta}detalle_libros_a_tu_clase`,
		{ id },
		async resp => {
			let {
				fecha_inicio,
				fecha_fin,
				id_bloque,
				id_salon,
			} = resp;
			fecha_prestamo = fecha_inicio.substr(0, 10);
			let hora_entrega = fecha_inicio.substr(11, 8);
			let hora_retiro = fecha_fin.substr(11, 8);
			$("#form_modificar_solicitud input[name=fecha_prestamo]").val(
				fecha_prestamo
			);
			$("#form_modificar_solicitud input[name=hora_entrega]").val(
				hora_entrega
			);
			$("#form_modificar_solicitud input[name=hora_retiro]").val(
				hora_retiro
			);
			$("#form_modificar_solicitud select[name=id_bloque]").val(
				id_bloque
			);
			let salones = await obtener_bloque_salon(id_bloque);
			pintar_datos_combo(
				salones,
				"#form_modificar_solicitud select[name=id_salon]",
				"Seleccione Salon",
				id_salon
			);
		}
	);
}
const libros_agregados = () => {
	$('#form_agregar_solicitud select[name="libros"]').html(
		`<option value = ''>Temática/Libros</option>`
	);
	libros.map((elemento, indice) => {
		$('#form_agregar_solicitud select[name="libros"]').append(
			`<option value = '${indice}'>${elemento}</option>`
		);
	});
};
const capacitaciones_agregadas = (type) => {
	if (type == "new") {
		$('#form_agregar_solicitud select[name="capacitaciones"]').html(
			`<option value = ''>Niveles</option>`
		);
		capacitaciones.map((elemento, indice) => {
			$('#form_agregar_solicitud select[name="capacitaciones"]').append(
				`<option value = '${indice}'>${elemento.nombre}</option>`
			);
		});
	} else if (type == "mod") {
		$('#form_modificar_solicitud select[name="capacitaciones"]').html(
			`<option value = ''>Capacitaciones</option>`
		);
		capacitaciones.map((elemento, indice) => {
			$('#form_modificar_solicitud select[name="capacitaciones"]').append(
				`<option value = '${indice}'>${elemento.nombre}</option>`
			);
		});
	}
};

const eliminar_libros_agregados = () => {
	let libro_sele = $("#libro_select").val();
	let libro_text = $('#libro_select option[value="' + libro_sele + '"]').text();
	swal(
		{
			title: "Estas Seguro ?",
			text: `Esta seguro de eliminar el libro ${libro_text}, si desea continuar presione la opción de 'Si, Entiendo'.!`,
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Entiendo!",
			cancelButtonText: "No, Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function (isConfirm) {
			if (isConfirm) {
				if (libro_sele == "") {
					MensajeConClase("Seleccione una temática/libro.", "info", "Oops...!");
				} else {
					let libro = libros.indexOf(libro_text);
					libros.splice(libro, 1);
					libros_agregados();
					MensajeConClase(
						"Se eliminó con éxito",
						"success",
						"Proceso Exitoso!"
					);
				}
			}
		}
	);
};

const asignar_hora_fin = (hora_inicio) => {
	hora_inicio += ":00";
	let fhora = "1970-01-01 " + hora_inicio;
	let dt = new Date(fhora);
	let sum = 0;
	capacitaciones.forEach(element => {
		let n = parseInt(element.duracion);
		sum += n;
	});
	dt.setMinutes(dt.getMinutes() + sum);
	let h = dt.getHours() < 10 ? "0" + dt.getHours() : dt.getHours();
	let m = dt.getMinutes() < 10 ? "0" + dt.getMinutes() : dt.getMinutes();
	let hora_final = h + ":" + m;
	return hora_final;
}

const eliminar_capacitaciones_agregadas = (type) => {
	let capa_sele = "";
	let capa_text = "";
	if (type == "new") {
		capa_sele = $(".capa_select").val();
		capa_text = $('.capa_select option[value="' + capa_sele + '"]').text();
	} else if (type == "mod") {
		capa_sele = $("#capa_select").val();
		capa_text = $('#capa_select option[value="' + capa_sele + '"]').text();
	}
	swal(
		{
			title: "Estas Seguro ?",
			text: `Esta seguro de eliminar la capacitación ${capa_text} de la solicitud, si desea continuar presione la opción de 'Si, Entiendo'.!`,
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Entiendo!",
			cancelButtonText: "No, Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function (isConfirm) {
			if (isConfirm) {
				if (capa_sele == "") {
					MensajeConClase("Seleccione una capacitación.", "info", "Oops...!");
				} else {
					capacitaciones.splice(capa_sele, 1);
					if (type == "new") {
						capacitaciones_agregadas("new");
						let hora_inicio = $("#hora_inicio").val();
						$("#hora_fin").val(asignar_hora_fin(hora_inicio));
					} else if (type == "mod") {
						capacitaciones_agregadas("mod");
						let hora_inicio_mod = $("#form_modificar_solicitud input[name=hora_entrega]").val();
						$("#form_modificar_solicitud input[name=hora_retiro]").val(asignar_hora_fin(hora_inicio_mod));
					}
					MensajeConClase(
						"Se eliminó con éxito",
						"success",
						"Proceso Exitoso!"
					);
				}
			}
		}
	);
}

const historial_solicitud = (id) => {
	consulta_ajax(`${ruta}listar_historial_solicitud`, { id }, resp => {
		const myTable = $("#tabla_estado_solicitud").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					data: "estado"
				},
				{
					data: "bloque"
				},
				{
					data: "salon"
				},
				{
					data: "fecha_resgistro"
				},
				{
					data: "nombre_persona_registra"
				},
				{
					data: "observacion"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: []
		});
	});
}

const historial_libro = (id) => {
	consulta_ajax(`${ruta}listar_historial_libro`, { id }, resp => {
		let i = 0;
		const myTable = $("#tabla_asignaciones_libro").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					}
				},
				{
					data: 'estudiante_asignado'
				},
				{
					data: 'fecha_registro'
				},
				{
					data: 'nombre_persona_registra'
				},
				{
					data: 'observacion'
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: []
		});
	});
}

const historial_auxiliar = (id) => {
	consulta_ajax(`${ruta}listar_historial_auxiliar`, { id }, resp => {
		let i = 0;
		const myTable = $("#tabla_asignaciones_auxiliar").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					}
				},
				{
					data: 'nombre_auxiliar'
				},
				{
					data: 'fecha_registro'
				},
				{
					data: 'carga'
				},
				{
					data: 'modificador'
				},
				{
					data: 'observacion'
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: []
		});
	});
}

const buscar_empleado = (dato, callbak, tabla, month = new Date().getMonth() + 1, mod = { sw: false }, id_solicitud) => {
	if (tabla == "#tabla_empleado_busqueda") {
		consulta_ajax(`${ruta}buscar_empleado`, { dato, tipo_busqueda, id_solicitud, month }, resp => {
			$(`#tabla_empleado_busqueda tbody`)
				.off("click", "tr td .estudiante")
				.off("click", "tr td .ver")
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
						defaultContent:
							'<span style="background-color: white;color: black; width: 100%; ;" class="pointer form-control ver" ><span >ver</span></span>'
					},
					{
						data: "nombre_completo"
					},
					{
						data: "identificacion"
					},
					{
						data: "total"
					},
					{
						defaultContent:
							'<span style="color: #39B23B;" title="Seleccionar Persona" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default estudiante" ></span>'
					}
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: []
			});
			$("#tabla_empleado_busqueda tbody").on("click", "tr", function () {
				$("#tabla_empleado_busqueda tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});
			$("#tabla_empleado_busqueda tbody").on("dblclick", "tr", function () {
				let data = myTable
					.row(
						$(this)
							.parent()
							.parent()
					)
					.data();
			});
			$("#tabla_empleado_busqueda tbody").on(
				"click",
				"tr td .estudiante",
				function () {
					let data = myTable
						.row(
							$(this)
								.parent()
								.parent()
						)
						.data();
					data.gestion = true;
					$("#modal_seleccion_carga").modal();
					$("#save_accion").off("click");
					if (mod.sw == true) {
						mod.new_id_aux = data.id;
						$("#acciones_auxiliar").val(mod.accion);
						$("#save_accion").on("click", function () {
							mod.accion = $("#acciones_auxiliar").val();
							swal({
								title: "¿ Modificar ?",
								text: "",
								type: "input",
								showCancelButton: true,
								confirmButtonColor: "#D9534F",
								confirmButtonText: "Aceptar!",
								cancelButtonText: "Cancelar!",
								allowOutsideClick: true,
								closeOnConfirm: false,
								closeOnCancel: true,
								inputPlaceholder: `Ingrese la razon del cambio`
							}, function (message) {
								if (message === false) return false;
								if (message === "") swal.showInputError(`Debe Ingresar el codigo`);
								else {
									mod.observacion = message;
									modificar_auxiliar(mod);
								}
							});
						});
					} else {
						$("#save_accion").on("click", function () {
							data.accion = $("#acciones_auxiliar").val();
							if (tipo_solicitud == "Bib_Lib") {
								if (data.accion == "Acc_Cap") MensajeConClase("No puede asignar esta acción en esta solicitud", "info", "Oops.!");
								else guardar_auxiliares(data);
							} else if (tipo_solicitud == "Bib_Cap") {
								if (data.accion != "Acc_Cap") MensajeConClase("No puede asignar esta acción en esta solicitud", "info", "Oops.!");
								else guardar_auxiliares(data);
							}
						});
					}
				}
			);
			$("#tabla_empleado_busqueda tbody").on(
				"click",
				"tr td .ver",
				function () {
					let { id } = myTable
						.row(
							$(this)
								.parent()
								.parent()
						)
						.data();
					sw_cal = 1
					carga_empleado(id, 'asig');
					$("#botones_cal").hide();
					$("#modal_carga").modal();
				}
			)
		});
	} else if (tabla == "#tabla_empleado_busqueda_sol") {
		tipo_busqueda = "";
		consulta_ajax(`${ruta}buscar_empleado`, { dato, tipo_busqueda }, resp => {
			$(`${tabla} tbody`)
				.off("click", "tr td .estudiante")
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
						}
					},
					{
						data: "nombre_completo"
					},
					{
						data: "identificacion"
					},
					{
						defaultContent:
							'<span style="color: #39B23B;" title="Seleccionar Estudiante" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default estudiante" ></span>'
					}
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: []
			});
			$(`${tabla} tbody`).on("click", "tr", function () {
				$(`${tabla} tbody tr`).removeClass("warning");
				$(this).attr("class", "warning");
			});
			$(`${tabla} tbody`).on("dblclick", "tr", function () {
				let data = myTable
					.row(
						$(this)
							.parent()
							.parent()
					)
					.data();
				callbak(data);
			});
			$(`${tabla} tbody`).on(
				"click",
				"tr td .estudiante",
				function () {
					let data = myTable
						.row(
							$(this)
								.parent()
								.parent()
						)
						.data();
					callbak(data);
					$("#form_buscar_empleado_sol")
						.get(0)
						.reset();
				}
			);
		});
	}
};

const carga_empleado = (id = '', tipo, fecha_inicio, fecha_fin, estado, sw = sw_cal) => {
	consulta_ajax(`${ruta}carga_empleado`, { id, tipo, fecha_inicio, fecha_fin, estado, sw }, resp => {
		let eventos = [];
		resp.forEach(({ tipo_solicitud, id, start, end, id_tipo_solicitud, hora_inicio, hora_fin }) => {
			const callback = async (id) => {
				let data = await consulta_solicitud_id(id);
				ver_detalle_solicitud(data, false);
				$("#cont_btn_aux").html('');
			}
			color = id_tipo_solicitud == 'Bib_Lib' ? 'event-success' : 'event-info';
			tipo_solicitud = tipo_solicitud + ` ${hora_inicio} - ${hora_fin}`
			let evento = crear_evento(id, tipo_solicitud, "", color, start, end, callback);
			eventos.push(evento);
		});
		let calendar = $("#calendario").calendar(
			{
				tmpl_path: `${Traer_Server()}js-css/estaticos/tmpls/`,
				events_source: eventos,
				modal: "#modal_detalle_solicitud",
				modal_type: "template",
			}
		);
		$("#btn_semana").click(() => {
			calendar.view('week');
		});
		$("#btn_mes").click(() => {
			calendar.view('month');
		});
		$("#btn_ano").click(() => {
			calendar.view('year');
		});
	});
}

const crear_evento = (id, titulo, url, color, fin, inicio, callback) => {
	let evento = {
		"id": id,
		"title": titulo,
		"url": url,
		"class": color,
		"start": inicio,
		"end": fin,
		callback,
	}

	return evento;
}

const buscar_estudiante =
	(dato, callbak, tabla) => {
		if (tabla == "") {
			MensajeConClase("Por favor seleccione el tipo de persona a buscar", "info", "Oops.!");
		} else {
			consulta_ajax(`${ruta}buscar_estudiante`, { dato, tabla }, resp => {
				$(`#tabla_estudiantes_busqueda tbody`)
					.off("click", "tr td .estudiante")
					.off("dblclick", "tr")
					.off("click", "tr")
					.off("click", "tr td:nth-of-type(1)");
				let i = 0;
				const myTable = $("#tabla_estudiantes_busqueda").DataTable({
					destroy: true,
					searching: false,
					processing: true,
					data: resp,
					columns: [
						{
							render: function (data, type, full, meta) {
								i++;
								return i;
							}
						},
						{
							data: "nombre_completo"
						},
						{
							data: "identificacion"
						},
						{
							defaultContent:
								'<span style="color: #39B23B;" title="Seleccionar Estudiante" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default estudiante" ></span>'
						}
					],
					language: get_idioma(),
					dom: "Bfrtip",
					buttons: []
				});
				$("#tabla_estudiantes_busqueda tbody").on("click", "tr", function () {
					$("#tabla_estudiantes_busqueda tbody tr").removeClass("warning");
					$(this).attr("class", "warning");
				});
				$("#tabla_estudiantes_busqueda tbody").on("dblclick", "tr", function () {
					let data = myTable
						.row(
							$(this)
								.parent()
								.parent()
						)
						.data();
					data.tabla = tabla
					callbak(data);
				});
				$("#tabla_estudiantes_busqueda tbody").on(
					"click",
					"tr td .estudiante",
					function () {
						let data = myTable
							.row(
								$(this)
									.parent()
									.parent()
							)
							.data();
						data.tabla = tabla
						callbak(data);
						$("#form_buscar_estudiante")
							.get(0)
							.reset();
						//listar_estudiantes();
					}
				);
			});
		};
	}

const listar_estudiantes = () => {
	$("#tabla_estudiantes tbody")
		.off("click", "tr")
		.off("click", "tr .eliminar");
	let i = 0;
	const myTable = $("#tabla_estudiantes").DataTable({
		destroy: true,
		searching: false,
		processing: true,
		data: estudiantes,
		columns: [
			{
				render: function (data, type, full, meta) {
					i++;
					return i;
				}
			},
			{
				data: "nombre_completo"
			},
			{
				data: "identificacion"
			},
			{
				defaultContent: `<span style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>`
			}
		],
		language: idioma,
		dom: "Bfrtip",
		buttons: []
	});

	swal.close();

	//EVENTOS DE LA TABLA ACTIVADOS
	$("#tabla_estudiantes tbody").on("click", "tr", function () {
		$("#tabla_estudiantes tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});
	$("#tabla_estudiantes tbody").on("click", "tr .eliminar", function () {
		let data = myTable
			.row(
				$(this)
					.parent()
					.parent()
			)
			.data();
		estudiantes.forEach((key, indice) => {
			if (key.id == data.id) {
				if (estudiantes.length <= 1) {
					swal("Oops", "Debe tener por lo menos un estudiante", "info");
					return;
				} else {
					estudiantes.splice(indice, 1);
					return;
				}
			}
		});
		listar_estudiantes();
	});
};

const guardar_solicitud = () => {
  MensajeConClase("Estamos validando la información...", "add_inv", "Oops...");
	let fordata = new FormData(document.getElementById("form_agregar_solicitud"));
	let data = formDataToJson(fordata);
	data.estudiantes = estudiantes;
	data.id_tipo_solicitud = tipo_solicitud;
	data.libros = libros;
	data.capacitaciones = capacitaciones;
	data.solicitante = solicitante;
	let materia_doc = materias.filter(element => {
		return element.id === $("#form_agregar_solicitud select[name=id_materia]").val();
	});
	//data.id_materia_doc = materia_doc.length > 0 ? materia_doc[0].id_mat : null;
	data.id_materia_doc = materia_doc.length > 0 ? materia_doc[0].id : null;

	consulta_ajax(`${ruta}guardar_solicitud`, data, resp => {
		let { titulo, mensaje, tipo, data_sol } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_agregar_solicitud")
				.get(0)
				.reset();
			$("#modal_agregar_solicitud").modal("hide");
			libros = [];
			data_sol.estado = "Bib_Sol_E";
			enviar_correo(data_sol);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};

const guardar_nuevo_estudiante = () => {
	let fordata = new FormData(document.getElementById("form_nuevo_estudiante"));
	let info = formDataToJson(fordata);
	info.tipo = 'PerEstCUC';
	info.id_solicitud = id_solicitud;
	consulta_ajax(`${ruta}guardar_nuevo_estudiante`, info, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_nuevo_estudiante").get(0).reset();
			$("#modal_nuevo_estudiante").modal("hide");
			dato = info.identificacion;
			callbak_activo = data => {
				if (!cambio) {
					swal(
						{
							title: "Estas Seguro ?",
							text: `Esta seguro de guardar a ${data.nombre_completo
								}, si desea continuar presione la opción de 'Si, Entiendo'.!`,
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: "#D9534F",
							confirmButtonText: "Si, Entiendo!",
							cancelButtonText: "No, Cancelar!",
							allowOutsideClick: true,
							closeOnConfirm: false,
							closeOnCancel: true
						},
						function (isConfirm) {
							if (isConfirm) {
								id_persona = data.id;
								guardar_estudiante_nuevo(id_persona, id_solicitud, 'visitantes', data.identificacion);
							}
						}
					);
				} else {
					let estudiante = estudiantes.find(element => element.id == data.id);
					if (estudiante)
						MensajeConClase("El estudiante ya fue asignado.", "info", "Oops.!");
					else {
						estudiantes.push(data);
						let table = $("#tabla_estudiantes_busqueda").DataTable();
						table.clear().draw();
						MensajeConClase(
							"Estudiante asignado con exito",
							"success",
							"Proceso Exitoso"
						);
					}
				}
			};
			buscar_estudiante(dato, callbak_activo, 'visitantes');
			$("#modal_buscar_estudiante").modal();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const guardar_turno = () => {
	let fordata = new FormData(document.getElementById("form_agregar_turno"));
	let info = formDataToJson(fordata);
	consulta_ajax(`${ruta}guardar_nuevo_turno`, info, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_agregar_turno").get(0).reset();
			$("#modal_agregar_turno").modal("hide");
			listar_turnos();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	})
}

const obtener_programas = buscar => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_programas`;
		consulta_ajax(url, { buscar }, resp => {
			resolve(resp);
		});
	});
}
const consulta_solicitud_id = id => {
	return new Promise(resolve => {
		let url = `${ruta}consulta_solicitud_id`;
		consulta_ajax(url, { id }, resp => {
			resolve(resp);
		});
	});
}

const obtener_empleados = buscar => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_empleados`;
		consulta_ajax(url, { buscar }, resp => {
			resolve(resp);
		});
	});
}

const obtener_capacitaciones = (buscar) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta}obtener_capacitaciones`, { buscar }, resp => {
			resolve(resp);
		});
	});
}

const listar_capa_solicitud = async (id, tabla) => {
	let capacitaciones = await obtener_capacitaciones(id);
	if (tabla == "#tabla_capacitaciones_solicitud") {
		$(`${tabla} tbody`)
			.off("click", "tr")
			.off("click", "tr .eliminar");
		const myTable = $(tabla).DataTable({
			destroy: true,
			processing: true,
			data: capacitaciones,
			columns: [
				{
					data: "nivel"
				},
				{
					data: "nombre"
				},
				{
					data: "duracion"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones(),
		});
	}
}

const listar_libros = async (id, tabla) => {
	let books = await obtener_libros(id, estado_solicitud);
	if (estado_solicitud == 'Bib_Sol_E' || estado_solicitud == '') {
		$("#tabla_libros").attr("hidden", true);
	} else {
		$("#tabla_libros").attr("hidden", false);
		if (tabla == "#tabla_preparacion_solicitud" || tabla == "#tabla_libro_solicitudes") {
			let codeBooks = [];
			books.forEach(elemento => {
				if (elemento.codigo_de_barras) codeBooks.push(elemento);
			});
			$(`${tabla} tbody`)
				.off("click", "tr")
				.off("click", "tr .asignar")
				.off("click", "tr .reasignar")
				.off("click", "tr .desasignar")
				.off("dblclick", "tr")
				.off("click", "tr td:nth-of-type(1)");
			let i = 0;
			const myTable = $(tabla).DataTable({
				destroy: true,
				processing: true,
				data: codeBooks,
				columns: [
					{
						data: "ver"
					},
					{
						data: "codigo_de_barras"
					},
					{
						data: "nombre_libro"
					},
					{
						data: "estudiante_asignado"
					},
					{
						data: "accion"
					}
				],
				language: idioma,
				dom: "Bfrtip",
				buttons: get_botones(),
			});
			//EVENTOS DE LA TABLA ACTIVADOS
			$(`${tabla} tbody`).on("click", "tr", function () {
				$(`${tabla} tbody tr`).removeClass("warning");
				$(this).attr("class", "warning");
			});
			$(`${tabla} tbody`).on("dblclick", "tr", function () {
				let data = myTable.row(this).data();
				ver_detalle_libro(data);
			});
			$(`${tabla} tbody`).on(
				"click",
				"tr td:nth-of-type(1)",
				function () {
					let data = myTable.row($(this).parent()).data();
					id_libro = data.id;
					ver_detalle_libro(data);
				}
			);

			$(`${tabla} tbody`).on(
				"click",
				"tr .asignar",
				function () {
					let { id, id_solicitud } = myTable
						.row(
							$(this)
								.parent()
								.parent()
						)
						.data();
					asignar_libros(id, id_solicitud);
				}
			);

			$(`${tabla} tbody`).on(
				"click",
				"tr .reasignar",
				function () {
					let { id, id_solicitud, id_asignado } = myTable
						.row(
							$(this)
								.parent()
								.parent()
						)
						.data();
					asignar_libros(id, id_solicitud, id_asignado);
				}
			);

			$(`${tabla} tbody`).on(
				"click",
				"tr .desasignar",
				function () {
					let { id, id_solicitud, id_asignado, id_estado } = myTable
						.row(
							$(this)
								.parent()
								.parent()
						)
						.data();
					desasignar_libros(id, id_solicitud, id_asignado, id_estado);
				}
			);
		}
	}

	if (tabla == "#tabla_tematicas_solicitud") {
		$("#tabla_tematicas_solicitud tbody")
			.off("click", "tr")
			.off("click", "tr .eliminar");
		let nocodeBooks = [];
		books.forEach(elemento => {
			if (estado_solicitud == 'Bib_Rev_E' || estado_solicitud == 'Bib_Sol_E') {
				elemento.accion = `<span style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>`;
			} else {
				elemento.accion = '<span  title="Sin Acción" data-toggle="popover" data-trigger="hover" class="fa fa-toggle-off"></span>';
			}
			if (!elemento.codigo_de_barras) nocodeBooks.push(elemento)
		});
		$("#tabla_tematicas_solicitud tbody")
			.off("click", "tr")
			.off("click", "tr .eliminar");
		let i = 0;
		const myTable = $("#tabla_tematicas_solicitud").DataTable({
			destroy: true,
			processing: true,
			data: nocodeBooks,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					}
				},
				{
					data: "nombre_libro"
				},
				{
					data: "nombre_persona_registra"
				},
				{
					data: "fecha_registra"
				},
				{
					data: "accion"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones(),
		});
		//EVENTOS DE LA TABLA ACTIVADOS
		$("#tabla_tematicas_solicitud tbody").on("click", "tr", function () {
			$("#tabla_tematicas_solicitud tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_tematicas_solicitud tbody").on(
			"click",
			"tr .eliminar",
			function (isConfirm) {
				if (isConfirm) {
					let { id, id_solicitud } = myTable
						.row(
							$(this)
								.parent()
								.parent()
						)
						.data();
					if (myTable.rows().count() <= 1) {
						MensajeConClase("Debe tener por lo una tematica o libro", "info", "Oops.!");
					} else {
						eliminar_libro_solicitud(id, id_solicitud);
					}
				}
			}
		);
	}
}

const listar_solicitud = (id = '') => {
	let id_tipo_solicitud = $("#modal_crear_filtros select[name='id_tipo_solicitud']").val();
	let id_estado_solicitud = $("#modal_crear_filtros select[name='id_estado_solicitud']").val();
	let fecha_inicial = $("#modal_crear_filtros input[name='fecha_inicial']").val();
	let fecha_final = $("#modal_crear_filtros input[name='fecha_final']").val();
	$("#tabla_solicitudes tbody")
		.off("click", "tr")
		.off("dblclick", "tr")
		.off("click", "tr td:nth-of-type(1)")
		.off("click", "tr .revisar")
		.off("click", "tr .preparar")
		.off("click", "tr .negar")
		.off("click", "tr .finalizar")
		.off("click", "tr .entregar")
		.off("click", "tr .cancelar");
	consulta_ajax(`${ruta}listar_solicitud`, { id, id_tipo_solicitud, id_estado_solicitud, fecha_inicial, fecha_final }, resp => {
		const myTable = $("#tabla_solicitudes").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "ver"
				},
				{
					data: "tipo_solicitud"
				},
				{
					data: "solicitante"
				},
				{
					data: "fecha_inicio"
				},
				{
					data: "programa"
				},
				{
					data: "materia"
				},
				{
					data: "num_est"
				},
				{
					data: "accion"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones()
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$("#tabla_solicitudes tbody").on("click", "tr", function () {
			$("#tabla_solicitudes tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
			let data = myTable.row(this).data();
			tipo_solicitud = data.id_tipo_solicitud;
			id_solicitud = data.id;
			estado_solicitud = data.id_estado_solicitud;
		});
		$("#tabla_solicitudes tbody").on("dblclick", "tr", function () {
			let data = myTable.row(this).data();
			id_solicitud = data.id;
			ver_detalle_solicitud(data, true);
		});
		$("#tabla_solicitudes tbody").on(
			"click",
			"tr td:nth-of-type(1)",
			function () {
				let data = myTable.row($(this).parent()).data();
				ver_detalle_solicitud(data, true);
			}
		);
		$("#tabla_solicitudes tbody").on("click", "tr .revisar", function () {
			let data = myTable.row($(this).parent()).data();
			let { id, id_estado_solicitud, tipo_solicitud, id_tipo_solicitud, correo, id_bloque, id_salon, tipo_solicitud_full } = data;
			$("#modal_gestionar").modal();
			if (id_tipo_solicitud == 'Bib_Lib') $("#capa_info").hide();
			else if (id_tipo_solicitud == 'Bib_Cap') {
				$("#capa_info").show();
				listar_recursos();
			}
			listar_auxiliares(id, '#tabla_auxiliares_solicitud');
			$("#btn_save_aux").off("click");
			$("#btn_save_aux").on("click", async function () {
				let auxs = await obtener_auxiliares(id);
				let ent = false;
				let ret = false;
				let cap = false;
				auxs.forEach(element => {
					if (element.carga == "Acc_Ent") ent = true;
					if (element.carga == "Acc_Ret") ret = true;
					if (element.carga == "Acc_Cap") cap = true;
				});
				if (id_tipo_solicitud == "Bib_Lib") {
					if (ent == false) MensajeConClase("Debe tener por lo menos un auxiliar de entrega", "info", "Oops.!");
					else if (ret == false) MensajeConClase("Debe tener por lo menos un auxiliar de retiro", "info", "Oops.!");
					else gestionar_solicitud({ id, estado: 'Bib_Rev_E', tipo_solicitud, id_tipo_solicitud, correo, tipo_solicitud_full }, "revisar");
				} else if (id_tipo_solicitud == "Bib_Cap") {
					if (cap == false) MensajeConClase("Debe tener por lo menos un auxiliar para realizar la capacitación", "info", "Oops.!");
					else {
						$("#modal_ubicacion_capa").modal();
						$("#cont_bloque_salon").hide();
						$("#form_ubicacion_capa").off("submit");
						$("#form_ubicacion_capa").submit(() => {
							let fordata = new FormData(document.getElementById("form_ubicacion_capa"));
							let data = formDataToJson(fordata);
							let recursos = data.id_recurso;
							let bloque_cap = recursos == 'Bib_Com_R' ? data.id_bloque : id_bloque;
							let salon_cap = recursos == 'Bib_Com_R' ? data.id_salon : id_salon;
							gestionar_solicitud({ id, estado: 'Bib_Rev_E', tipo_solicitud, tipo_solicitud_full, id_tipo_solicitud, correo, bloque_cap, salon_cap, recursos }, "revisar");
							$("#modal_ubicacion_capa").modal('hide');
							return false;
						});
					}
				}
			})
		});

		$("#tabla_solicitudes tbody").on("click", "tr .cancelar", function () {
			let { id, id_aux, tipo_solicitud, id_tipo_solicitud, correo, tipo_solicitud_full, id_bloque, id_salon } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			let bloque_cap = id_bloque;
			let salon_cap = id_salon;
			gestionar_solicitud({ id, estado: 'Bib_Can_E', tipo_solicitud, tipo_solicitud_full, id_tipo_solicitud, correo, bloque_cap, salon_cap }, "cancelar");
		});

		$("#tabla_solicitudes tbody").on("click", "tr .negar", function () {
			let { id, id_aux, tipo_solicitud, id_tipo_solicitud, correo, solicitante, id_bloque, id_salon, tipo_solicitud_full } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			let bloque_cap = id_bloque;
			let salon_cap = id_salon;
			gestionar_solicitud({ id, estado: 'Bib_Rec_E', tipo_solicitud, id_tipo_solicitud, correo, solicitante, tipo_solicitud_full, bloque_cap, salon_cap }, "negar");
		});

		$("#tabla_solicitudes tbody").on("click", "tr .preparar", function () {
			$("#modal_preparacion_solicitud").modal();
			let { id, id_estado_solicitud, tipo_solicitud, id_tipo_solicitud, correo, tipo_solicitud_full } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			estado_solicitud = id_estado_solicitud;
			listar_libros(id, "#tabla_preparacion_solicitud");
			$("#btn_save").on("click", async function () {
				let books = await obtener_libros(id, 'Bib_Pre_E');
				let codeBooks = [];
				let asigBooks = [];
				books.forEach(elemento => {
					if (elemento.codigo_de_barras) codeBooks.push(elemento);
				});
				codeBooks.forEach(elemento => {
					if (elemento.estudiante_asignado) asigBooks.push(elemento);
				});
				if (codeBooks.length <= 0) {
					MensajeConClase("Debe tener por lo menos un libro", "info", "Oops.!");
				} else if (codeBooks.length != asigBooks.length) {
					MensajeConClase("Por favor asigne todos los libros", "info", "Oops.!");
				} else {
					gestionar_solicitud({ id, estado: 'Bib_Pre_E', tipo_solicitud, id_tipo_solicitud, correo, tipo_solicitud_full }, "preparar");
				}
			});
		});

		$("#tabla_solicitudes tbody").on("click", "tr .entregar", function () {
			let { id, tipo_solicitud, id_tipo_solicitud, correo, solicitante, tipo_solicitud_full } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			gestionar_solicitud({ id, estado: 'Bib_Ent_E', tipo_solicitud, id_tipo_solicitud, correo, solicitante, tipo_solicitud_full }, "entregar");
			$("#agregar_libro_cod").attr("hidden", true);
		});

		$("#tabla_solicitudes tbody").on("click", "tr .finalizar", function () {
			let { id, tipo_solicitud, id_tipo_solicitud, correo, solicitante, tipo_solicitud_full } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			gestionar_solicitud({ id, estado: 'Bib_Fin_E', tipo_solicitud, id_tipo_solicitud, correo, solicitante, tipo_solicitud_full }, "finalizar");
		});
	});
};

const ver_detalle_libro = data => {
	let {
		id,
		id_solicitud,
		nombre_libro,
		fecha_registra,
		nombre_persona_registra,
		estudiante_asignado,
		codigo_de_barras,
		id_estado,
		nota_retiro,
		persona_retira,
		peso
	} = data
	peso += " g";
	$("#tabla_detalle_libro .nombre_libro").html(nombre_libro);
	$("#tabla_detalle_libro .fecha_registra").html(fecha_registra);
	$("#tabla_detalle_libro .solicitante").html(nombre_persona_registra);
	$("#tabla_detalle_libro .codigo").html(codigo_de_barras);
	$("#tabla_detalle_libro .estudiante_asignado").html(estudiante_asignado);
	$("#tabla_detalle_libro .nota_retiro").html(nota_retiro);
	$("#tabla_detalle_libro .persona_retira").html(persona_retira);
	$("#tabla_detalle_libro .peso").html(peso);
	$("#estudiante_a").hide();
	$("#retiro_l").hide();
	if (!estudiante_asignado) $("#estudiante_a").hide()
	else $("#estudiante_a").show();
	if (id_estado != 2) $("#retiro_l").hide()
	else $("#retiro_l").show()
	$("#modal_detalle_libro").modal();
}

const ver_detalle_auxiliar = data => {
	let {
		id,
		id_solicitud,
		nombre_completo,
		identificacion,
		carga,
		nota_retiro,
		persona_retira,
		estado
	} = data
	$("#tabla_detalle_auxiliar .nombre_aux").html(nombre_completo);
	$("#tabla_detalle_auxiliar .identificacion_aux").html(identificacion);
	$("#tabla_detalle_auxiliar .carga").html(carga);
	$("#tabla_detalle_auxiliar .razon_retiro").html(nota_retiro);
	$("#tabla_detalle_auxiliar .persona_retiro").html(persona_retira);
	$("#retiro_aux").hide();
	if (estado == 1) $("#retiro_aux").hide();
	else $("#retiro_aux").show();
	$("#modal_detalle_auxiliar").modal();
}



const gestionar_solicitud = (data, accion) => {
	datos_estado = data;
	const gestionar_solicitud_normal = (data, title = "¿ Cancelar Solicitud ?") => {
		swal(
			{
				title,
				text:
					"Tener en cuenta que no podrá revertir esta acción, si desea continuar debe presionar la opción de 'Si, Entiendo'",
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
					data.mensaje = data.mensaje != '' ? data.mensaje : "";
					ejecutar_gestion(data);
				}
			}
		);
	};

	const gestionar_solicitud_texto = (data, title = "¿ Negar Solicitud ?", msj) => {
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
			inputPlaceholder: `Ingrese el ${msj}`,
			inputType: data.estado == 'Bib_Pre_E' ? "number" : "text",
		}, function (mensaje) {
			if (mensaje === false) return false;
			if (mensaje === "") swal.showInputError(`Debe Ingresar el/la ${msj}`);
			else {
				data.mensaje = mensaje;
				ejecutar_gestion(data);
			}
		});
	}

	const preparar_negar = async (data) => {
		let { id } = data;
		$("#modal_negar_solicitud").modal();
		causas_gen = await obtener_causas(129);
		listar_causas(causas_gen);
		$("#btn_negar").off("click");
		$("#btn_negar").click(() => {
			data.mensaje = ''
			causas_gen.forEach(element => {
				if (element.agregado == 1) data.mensaje += element.causa + ', ';
			});
			if (!data.mensaje) {
				MensajeConClase("Por favor Ingrese por lo menos una causa de negación", "info", "Oops.!")
			} else {
				gestionar_solicitud_normal(data, "¿ Negar Solicitud ?");
				$("#modal_negar_solicitud").modal('hide');
			}
		});
	}

	const listar_causas = (causas) => {
		$('#tabla_causas_negar tbody')
			.off("click", "tr .asignar")
			.off("click", "tr .eliminar");
		consulta_ajax(`${ruta}verificar_causas`, { causas }, resp => {
			const myTable = $('#tabla_causas_negar').DataTable({
				destroy: true,
				processing: true,
				data: resp,
				columns: [
					{
						data: "causa"
					},
					{
						data: "accion"
					}
				],
				language: idioma,
				dom: "Bfrtip",
				buttons: []
			});

			$("#tabla_causas_negar tbody").on(
				"click",
				"tr .asignar",
				function () {
					let { causa, id_aux } = myTable
						.row(
							$(this)
								.parent()
								.parent()
						)
						.data();
					if (id_aux == 'Ot_Ca') {
						swal(
							{
								title: "Otro",
								text: "Escriba la causas por la cual niega la solicitud.",
								type: "input",
								showCancelButton: true,
								confirmButtonColor: "#D9534F",
								confirmButtonText: "Guardar!",
								cancelButtonText: "Cancelar!",
								allowOutsideClick: true,
								closeOnConfirm: false,
								closeOnCancel: true,
								inputPlaceholder: `Causa`
							},
							function (message) {
								if (message === false) return false;
								if (message === "") swal.showInputError(`Por Favor Ingrese la causa por la cual niega la solicitud`);
								let sw = 0;
								causas_gen.forEach(element => {
									if (element.causa == message) sw = 1;
								});
								if (sw == 1) swal.showInputError(`Ya ingreso una causa igual a esta.`);
								else {
									let data = {
										'causa': message,
										'agregado': 1,
									}
									causas_gen.push(data);
								}
								swal.close();
								listar_causas(causas_gen);
							}
						);
					} else {
						let index = causas_gen.findIndex(obj => obj.causa == causa);
						causas_gen[index].agregado = 1;
						listar_causas(causas_gen);
					}
				}
			)

			$("#tabla_causas_negar tbody").on(
				"click",
				"tr .eliminar",
				function () {
					let { causa } = myTable
						.row(
							$(this)
								.parent()
								.parent()
						)
						.data();
					let index = causas_gen.findIndex(obj => obj.causa == causa);
					causas_gen[index].agregado = 0;
					listar_causas(causas_gen);
				}
			)
		});
	}

	const ejecutar_gestion = (data) => {
		consulta_ajax(`${ruta}gestionar_solicitud`, data, resp => {
			let { titulo, mensaje, tipo, recargar } = resp;
			if (tipo == "success") {
				enviar_correo(data);
				listar_solicitud();
				$("#modal_gestionar").modal('hide');
				$("#modal_preparacion_solicitud").modal("hide");
				swal.close();
			} else {
				MensajeConClase(mensaje, tipo, titulo);
				if (recargar) listar_solicitud();
			}
		});
	};

	if (accion == "cancelar") gestionar_solicitud_normal(data);
	else if (accion == 'negar') {
		if (data.id_tipo_solicitud == 'Bib_Cap') {
			preparar_negar(data);
		} else {
			gestionar_solicitud_texto(data, '¿ Negar Solicitud ?', "Motivo");
		}
	}
	else if (accion == 'finalizar') gestionar_solicitud_normal(data, '¿ Finalizar Solicitud ?');
	else if (accion == 'preparar') gestionar_solicitud_texto(data, '¿ Preparar Solicitud ?', "Peso");
	else if (accion == 'entregar') gestionar_solicitud_normal(data, '¿ Entregar Solicitud ?');
	else if (accion == 'entregar') gestionar_solicitud_normal(data, '¿ Entregar Solicitud ?');
	else if (accion == 'revisar') gestionar_solicitud_normal(data, '¿ Revisar Solicitud ?');
};

const obtener_auxiliares = (buscar) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta}obtener_auxiliares`, { buscar }, resp => {
			resolve(resp);
		});
	});
}

const ver_detalle_solicitud = (data, modal) => {
	let {
		id,
		solicitante,
		celular,
		id_estado_solicitud,
		id_tipo_solicitud,
		tipo_solicitud,
		fecha_registra,
		fecha_inicio,
		fecha_fin,
		bloque,
		salon,
		id_bloque,
		id_salon,
		bloque_log,
		salon_log,
		recurso
	} = data;
	let ubicacion = "<span id='sp_bloque_salon'>" + bloque_log + " / " + salon_log + "</span>" + ' <span title="Cambiar" id="modificar_ubicacion" data-toggle="popover" data-trigger="hover" class="fa fa-retweet btn btn-default cambiar" style="color:#2E79E5"></span>';
	$("#modifcar_aux").off("click");
	$(".fecha_inicio").html(fecha_inicio);
	$(".fecha_fin").html(fecha_fin);
	$(".id_bloque").html('');
	$(".id_salon").html('');
	$(".id_bloque").html(bloque);
	$(".id_salon").html(salon);
	$(".celular").html(celular);
	if (id_tipo_solicitud == 'Bib_Cap') {
		if (id_estado_solicitud == 'Bib_Sol_E') {
			$("#cont_ubicacion").hide();
		} else {
			$("#cont_ubicacion").show();
			$(".ubicacion").html(ubicacion);
		}
	} else $("#cont_ubicacion").hide();
	$("#cont_btn_aux").html(`<button type="button" class="btn btn-default" id='modifcar_aux'><span class='fa fa-reply red'></span> Auxiliares</button>`);
	$("#modifcar_aux").on("click", function () {
		$("#modal_auxiliares_mod").modal();
		listar_auxiliares(id, '#tabla_auxiliares_mod');
	});
	$("#modificar_ubicacion").on("click", async () => {
		await listar_recursos();
		$("#modal_ubicacion_capa").modal();
		$("#cont_bloque_salon").hide();
		$(".cbxrecurso").val(recurso);
		if (recurso == 'Bib_Com_R') $("#cont_bloque_salon").show();
		else $("#cont_bloque_salon").hide();
		listar_bloques_cap(recurso);
		$("#form_ubicacion_capa").off("submit");
		$("#form_ubicacion_capa").submit(() => {
			let fordata = new FormData(document.getElementById("form_ubicacion_capa"));
			let data = formDataToJson(fordata);
			let recursos = data.id_recurso;
			let bloque_cap = recursos == 'Bib_Com_R' ? data.id_bloque : id_bloque;
			let salon_cap = recursos == 'Bib_Com_R' ? data.id_salon : id_salon;
			modificar_ubicacion({ id, bloque_cap, salon_cap, recursos });
			actualizar_ubicacion(id);
			$("#modal_ubicacion_capa").modal('hide');
			return false;
		});
	});
	if (id_tipo_solicitud == 'Bib_Lib') {
		$("#cont_btn_capacitaciones").html('');
		$("#cont_btn_tematicas").html(`<button type="button" class="btn btn-default" id="btn_tematicas"><span class="fa fa-book red"></span> Tematicas</button>`);
		$("#btn_tematicas").on("click", () => {
			$("#modal_tematicas_solicitud").modal();
			listar_libros(id_solicitud, "#tabla_tematicas_solicitud");
		});
	} else if (id_tipo_solicitud == 'Bib_Cap') {
		$("#cont_btn_tematicas").html('');
		$("#cont_btn_capacitaciones").html(`<button type="button" class="btn btn-default" id="btn_capacitaciones"><span class="fa fa-group red"></span> Niveles</button>`);
		$("#btn_capacitaciones").on("click", () => {
			$("#modal_capacitaciones_solicitud").modal();
			listar_capa_solicitud(id_solicitud, "#tabla_capacitaciones_solicitud");
		});
	}
	$(".tipo_solicitud").html(tipo_solicitud);
	$(".solicitante").html(solicitante);
	$(".fecha_registra").html(fecha_registra);
	estado_solicitud = id_estado_solicitud;

	if (id_tipo_solicitud == "Bib_Lib") {
		$("#tabla_libros").show();
		$("#agregar_libro_cod").hide();
		$("#agregar_libro_nuevo").hide();
		$("#agregar_estudiantes_nuevos").show();
		$("#agregar_libro_cod").hide();
		$("#auxiliares").hide();

		if (estado_solicitud == 'Bib_Sol_E') {
			$("#tabla_libros").hide();
			$("#agregar_estudiantes_nuevos").show();
			$("#agregar_libro_nuevo").show();
			$("#agregar_libro_cod").show();
		} else if (estado_solicitud == 'Bib_Rev_E') {
			$("#tabla_libros").show();
			$("#agregar_libro_nuevo").show();
			$("#agregar_libro_cod").show();
			$("#agregar_estudiantes_nuevos").show();
			$("#auxiliares").show();
			$("#agregar_libro_cod").show();
		} else if (estado_solicitud == 'Bib_Pre_E') {
			$("#tabla_libros").show();
			$("#agregar_libro_cod").hide();
			$("#agregar_estudiantes_nuevos").show();
		} else if (estado_solicitud == 'Bib_Fin_E' || estado_solicitud == 'Bib_Rec_E' || estado_solicitud == 'Bib_Can_E') {
			$("#agregar_estudiantes_nuevos").hide();
		}
		listar_libros(id, "#tabla_libro_solicitudes");
		listar_libros(id, "#tabla_tematicas_solicitud");
	} else if (id_tipo_solicitud == "Bib_Cap") {
		$("#tabla_libros").hide();
		$("#agregar_estudiantes_nuevos").show();
		$("#auxiliares").hide();

		if (estado_solicitud == 'Bib_Sol_E') {
			$("#agregar_estudiantes_nuevos").show();
		} else if (estado_solicitud == 'Bib_Rev_E') {
			$("#auxiliares").show();
		} else if (estado_solicitud == 'Bib_Fin_E' || estado_solicitud == 'Bib_Rec_E' || estado_solicitud == 'Bib_Can_E') {
			$("#agregar_estudiantes_nuevos").hide();
		}
	}

	listar_estudiante_solicitud(id, "#tabla_estudiante_solicitudes", estado_solicitud);
	if (modal) $("#modal_detalle_solicitud").modal();
}

const listar_estudiante_solicitud = (id, tabla, estado) => {
	$(`${tabla} tbody`)
		.off("click", "tr")
		.off("click", "tr .eliminar")
		.off("click", "tr .asignar");
	let i = 0;
	consulta_ajax(`${ruta}listar_estudiante_solicitud`, { id, tabla, estado }, resp => {
		const myTable = $(`${tabla}`).DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "codigo_acceso"
				},
				{
					data: "nombre_completo"
				},
				{
					data: "identificacion"
				},
				{
					data: "fecha_registra"
				},
				{
					data: "correo"
				},
				{
					data: "accion"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones(),
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$("#tabla_estudiante_solicitudes tbody").on("click", "tr", function () {
			$("#tabla_estudiante_solicitudes tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_estudiante_solicitudes tbody").on(
			"click",
			"tr .eliminar",
			function () {
				let { id, id_solicitud } = myTable
					.row(
						$(this)
							.parent()
							.parent()
					)
					.data();
				if (myTable.rows().count() <= 1) {
					MensajeConClase("Debe tener por lo menos un estudiante", "info", "Oops.!");
				} else {
					eliminar_estudiante_solicitud(id, id_solicitud);
				}
			}
		);

		$("#tabla_estudiante_solicitudes tbody").on(
			"click",
			"tr .observacion",
			function () {
				let { id, observacion, id_solicitud } = myTable
					.row(
						$(this)
							.parent()
							.parent()
					)
					.data();
				swal(
					{
						title: "¿Necesita dejar una observación?",
						text: "Si necesita dejar una observación relacionada conesta persona por favor digitela en la parte de abajo.",
						type: "input",
						showCancelButton: true,
						confirmButtonColor: "#D9534F",
						confirmButtonText: "Si, Entiendo!",
						cancelButtonText: "Cancelar!",
						allowOutsideClick: true,
						closeOnConfirm: false,
						closeOnCancel: true,
						inputPlaceholder: `Ingrese una nota`,
						inputValue: observacion
					},
					function (message) {
						if (message === false) return false;
						if (message === "") swal.close();
						else {
							consulta_ajax(
								`${ruta}almacenar_observacion`,
								{ id, message },
								resp => {
									let { titulo, mensaje, tipo } = resp;
									if (tipo == "success") {
										listar_estudiante_solicitud(id_solicitud, tabla, estado);
										swal.close();
									} else MensajeConClase(mensaje, tipo, titulo);
								}
							);
						}
					}
				);
			}
		)
	});
}

const listar_auxiliares = (id, tabla) => {
	$(`${tabla} tbody`)
		.off("click", "tr")
		.off("click", "tr td:nth-of-type(1)")
		.off("click", "tr .cambiar")
		.off("click", "tr .retirar");
	let i = 0;
	consulta_ajax(`${ruta}listar_auxiliares`, { id }, resp => {
		const myTable = $(tabla).DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "ver"
				},
				{
					data: "nombre_completo"
				},
				{
					data: "identificacion"
				},
				{
					data: "fecha_registra"
				},
				{
					data: "carga"
				},
				{
					data: "botones"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones(),
		});
		$(`${tabla} tbody`).on("dblclick", "tr", function () {
			let data = myTable.row(this).data();
			ver_detalle_auxiliar(data);
		});
		$(`${tabla} tbody`).on(
			"click",
			"tr td:nth-of-type(1)",
			function () {
				let data = myTable.row($(this).parent()).data();
				id_aux = data.id;
				ver_detalle_auxiliar(data);
			}
		);
		$(`${tabla} tbody`).on("click", "tr .cambiar", function () {
			let { id, id_auxiliar, id_solicitud, accion } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			let mod = {
				id: id,
				id_auxiliar: id_auxiliar,
				id_solicitud: id_solicitud,
				accion: accion,
				sw: true
			};
			tipo_busqueda = "aux_bib";
			tabla = "#tabla_empleado_busqueda"
			$("#txt_dato_buscar").val("");
			listar_acciones(tipo_solicitud);
			let month = new Date().getMonth() + 1
			buscar_empleado("", "", tabla, month, mod, id_solicitud);
			$("#modal_buscar_empleado").modal();
		});
		$(`${tabla} tbody`).on("click", "tr .retirar", function () {
			let { id, id_solicitud } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			swal(
				{
					title: "Estas Seguro ?",
					text: "Tener en cuenta que este auxiliar ya no estara asignada a esta solictud y por lo tanto no se tendra en cuenta, si desea continuar presione la opción de 'Si, Entiendo'.!",
					type: "input",
					showCancelButton: true,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Si, Entiendo!",
					cancelButtonText: "Cancelar!",
					allowOutsideClick: true,
					closeOnConfirm: false,
					closeOnCancel: true,
					inputPlaceholder: `Ingrese una nota`
				},
				function (message) {
					if (message === false) return false;
					if (message === "") swal.showInputError(`Debe Ingresar la razon del retiro del auxiliar`);
					else {
						consulta_ajax(
							`${ruta}retirar_auxiliar`,
							{ id, id_solicitud, message },
							resp => {
								let { titulo, mensaje, tipo } = resp;
								if (tipo == "success") {
									listar_auxiliares(id_solicitud, tabla);
									swal.close();
								} else MensajeConClase(mensaje, tipo, titulo);
							}
						);
					}
				}
			);
		});
	})
}

const eliminar_estudiante_solicitud = (id, id_solicitud) => {
	swal(
		{
			title: "Estas Seguro ?",
			text:
				"Tener en cuenta que, al eliminar el servicio no se tendra en cuenta para su solicitud, si desea continuar presione la opción de 'Si, Entiendo'.!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Entiendo!",
			cancelButtonText: "No, Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function (isConfirm) {
			if (isConfirm) {
				consulta_ajax(
					`${ruta}eliminar_estudiante_solicitud`,
					{ id, id_solicitud },
					resp => {
						let { titulo, mensaje, tipo } = resp;
						if (tipo == "success") {
							listar_estudiante_solicitud(id_solicitud, "#tabla_estudiante_solicitudes", estado_solicitud);
							swal.close();
						} else MensajeConClase(mensaje, tipo, titulo);
					}
				);
			}
		}
	);
}

const eliminar_libro_solicitud = (id, id_solicitud) => {
	swal(
		{
			title: "Estas Seguro ?",
			text:
				"Tener en cuenta que, al eliminar el servicio no se tendra en cuenta para su solicitud, si desea continuar presione la opción de 'Si, Entiendo'.!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Entiendo!",
			cancelButtonText: "No, Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function (isConfirm) {
			if (isConfirm) {
				consulta_ajax(
					`${ruta}eliminar_libro_solicitud`,
					{ id, id_solicitud },
					resp => {
						let { titulo, mensaje, tipo } = resp;
						if (tipo == "success") listar_libros(id_solicitud, "#tabla_tematicas_solicitud");
						MensajeConClase(mensaje, tipo, titulo);
					}
				);
			}
		}
	);
}

const actualizar_ubicacion = (id) => {
	consulta_ajax(`${ruta}ubicacion_actual`, { id }, resp => {
		let { bloque, salon } = resp;
		let ubicacion = bloque + " / " + salon;
		$("#sp_bloque_salon").html(ubicacion);
	});
}

const guardar_estudiante_nuevo = (id_persona, id_solicitud, tabla, identificacion) => {
	data = { id_persona, id_solicitud, tabla, identificacion };
	consulta_ajax(`${ruta}guardar_estudiante_nuevo`, data, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			$("#form_agregar_solicitud")
				.get(0)
				.reset();
			let table = $("#tabla_estudiantes_busqueda").DataTable();
			table.clear().draw();
			listar_estudiante_solicitud(id_solicitud, "#tabla_estudiante_solicitudes", estado_solicitud);
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const guardar_libros_nuevo = (id_solicitud, formulario) => {
	let fordata = new FormData(document.getElementById(formulario));
	let data = formDataToJson(fordata);
	data.id_solicitud = id_solicitud;
	data.estado_solicitud = estado_solicitud;
	if (formulario == "form_agregar_libro_nuevo") {
		data.codigo_de_barra = null;
		data.formulario = formulario;
	}
	consulta_ajax(`${ruta}guardar_libro_nuevo`, data, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			listar_libros(id_solicitud, "#tabla_libro_solicitudes");
			listar_libros(id_solicitud, "#tabla_tematicas_solicitud");
			listar_libros(id_solicitud, "#tabla_preparacion_solicitud");
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const obtener_libros = (buscar, estado) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta}obtener_libro`, { buscar, estado }, resp => {
			resolve(resp);
		});
	});
}

const asignar_libros = (id_libro, id_solicitud, id_asignado, id_estado) => {
	$("#tabla_estudiante_asignacion tbody")
		.off("click", "tr")
		.off("click", "tr .asignar");
	listar_estudiante_solicitud(id_solicitud, "#tabla_estudiante_asignacion", estado_solicitud);
	$("#modal_estudiantes_asignacion").modal();
	//ACTIVAR EVENTOS TABLA
	$("#tabla_estudiante_asignacion tbody").on("click", "tr", function () {
		$("#tabla_estudiante_asignacion tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});

	$("#tabla_estudiante_asignacion tbody").on(
		"click",
		"tr .asignar",
		function () {
			let { id } = $("#tabla_estudiante_asignacion").DataTable().row($(this).parent().parent()).data();
			if (id_asignado) {
				swal({
					title: "¿ Reasignar libro ?",
					text: "",
					type: "input",
					showCancelButton: true,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Aceptar!",
					cancelButtonText: "Cancelar!",
					allowOutsideClick: true,
					closeOnConfirm: false,
					closeOnCancel: true,
					inputPlaceholder: `Ingrese una nota`
				}, function (mensaje) {
					if (mensaje === false) return false;
					if (mensaje === "") swal.showInputError(`Debe Ingresar una nota de observación`);
					else {
						consulta_ajax(`${ruta}asignar_libro`, { id_libro, id, id_solicitud, mensaje }, resp => {
							let { titulo, mensaje, tipo } = resp;
							if (tipo == "success") {
								MensajeConClase(mensaje, tipo, titulo);
								$("#modal_estudiantes_asignacion").modal("hide");
								listar_libros(id_solicitud, "#tabla_libro_solicitudes");
								listar_libros(id_solicitud, "#tabla_preparacion_solicitud");
							} else {
								MensajeConClase(mensaje, tipo, titulo);
							}
						});
					}
				});
			} else {
				let mensaje = 'Asignado';
				consulta_ajax(`${ruta}asignar_libro`, { id_libro, id, id_solicitud, mensaje }, resp => {
					let { titulo, mensaje, tipo } = resp;
					if (tipo == "success") {
						MensajeConClase(mensaje, tipo, titulo);
						$("#modal_estudiantes_asignacion").modal("hide");
						listar_libros(id_solicitud, "#tabla_libro_solicitudes");
						listar_libros(id_solicitud, "#tabla_preparacion_solicitud");
					} else {
						MensajeConClase(mensaje, tipo, titulo);
					}
				});
			}
		}
	)
}

const desasignar_libros = (id_libro, id_solicitud, id_asignado, id_estado) => {
	if (id_estado == 2) {
		MensajeConClase("El libro ya fue desasignado", "info", "Oops..!")
	} else {
		swal({
			title: "¿ Desasignar libro ?",
			text: "",
			type: "input",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Aceptar!",
			cancelButtonText: "Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder: `Ingrese una nota`
		}, function (mensaje) {
			if (mensaje === false) return false;
			if (mensaje === "") swal.showInputError(`Debe Ingresar una nota de observación`);
			else {
				consulta_ajax(`${ruta}desasignar_libro`, { id_libro, id_asignado, id_solicitud, mensaje }, resp => {
					let { titulo, mensaje, tipo } = resp;
					if (tipo == "success") {
						MensajeConClase(mensaje, tipo, titulo);
						listar_libros(id_solicitud, "#tabla_libro_solicitudes");
						listar_libros(id_solicitud, "#tabla_preparacion_solicitud");
					} else {
						MensajeConClase(mensaje, tipo, titulo);
					}
				});
			}
		});
	}
}

const obtener_causas = buscar => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_causas`;
		consulta_ajax(url, { buscar }, resp => {
			resolve(resp);
		});
	});
}

const obtener_acciones = buscar => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_acciones`;
		consulta_ajax(url, { buscar }, resp => {
			resolve(resp);
		});
	});
}

const obtener_bloque = buscar => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_bloque`;
		consulta_ajax(url, { buscar }, resp => {
			resolve(resp);
		});
	});
};

const obtener_bloque_cap = buscar => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_bloque_cap`;
		consulta_ajax(url, { buscar }, resp => {
			resolve(resp);
		})
	});
}

const obtener_bloque_salon = id => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_bloque_salon`;
		consulta_ajax(url, { id }, resp => {
			resolve(resp);
		});
	});
};

const obtener_recursos = buscar => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_recursos`;
		consulta_ajax(url, { buscar }, resp => {
			resolve(resp);
		});
	});
}

const listar_recursos = async () => {
	let recursos = await obtener_recursos(128);
	pintar_datos_combo(recursos, ".cbxrecurso", "Seleccione Recurso");
}

const listar_bloques = async () => {
	let bloque = await obtener_bloque(115);
	pintar_datos_combo(bloque, ".cbxbloque", "Seleccione Bloque");
};

const listar_bloques_cap = async id => {
	let bloque = await obtener_bloque_cap(id);
	pintar_datos_combo(bloque, ".cbxbloque_cap", "Seleccione Bloque");
}

const listar_bloque_salon = async id => {
	let salon = await obtener_bloque_salon(id);
	pintar_datos_combo(salon, ".cbxsalon", "Seleccione Salon");
};

const listar_programas = async () => {
	let programas = await obtener_programas(50);
	pintar_datos_combo(programas, ".cbxprogramas", "Seleccione Programa");
}

const listar_acciones = async id => {
	let acciones = await obtener_acciones(id);
	pintar_datos_combo(acciones, "#acciones_auxiliar", "Seleccionar Accion");
}

const listar_empleados = async () => {
	let empleados = await obtener_empleados();
	pintar_empleados_combo(empleados, ".cbx_aux_bib", "Seleccionar Persona");
}

const listar_procesos = (id) => {
	$(`#tabla_procesos_bib tbody`)
		.off("click", "tr")
		.off("click", "tr td:nth-of-type(1)")
		.off("click", "tr .asignar")
		.off("click", "tr .retirar")
		.off("click", "tr .administrar");
	consulta_ajax(`${ruta}listar_procesos_bib`, { id }, resp => {
		const myTable = $("#tabla_procesos_bib").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "nombre"
				},
				{
					data: "descripcion"
				},
				{
					data: "accion"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: []
		});
		$(`#tabla_procesos_bib tbody`).on("click", "tr .asignar", function () {
			let { id_aux } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			asignar_proceso_persona(id, id_aux);
		});
		$(`#tabla_procesos_bib tbody`).on("click", "tr .retirar", function () {
			let { tipo } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			retirar_proceso_persona(id, tipo);
		});
		$(`#tabla_procesos_bib tbody`).on("click", "tr .administrar", function () {
			let data = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			$("#administrar_estados_biblioteca").modal();
			listar_estados_procesos(data.tipo, id, data.id_aux);
		});
	});
}

const listar_turnos = () => {
	$("#tabla_turnos_bib tbody")
		.off("click", "tr")
		.off("click", "tr td:nth-of-type(1)")
		.off("click", "tr .administrar")
		.off("click", "tr .eliminar");
	consulta_ajax(`${ruta}listar_turnos_bib`, {}, resp => {
		const myTable = $("#tabla_turnos_bib").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "hora_entrada"
				},
				{
					data: "hora_salida"
				},
				{
					data: "accion"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: []
		});
		$(`#tabla_turnos_bib tbody`).on("click", "tr .administrar", function () {
			let data = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			listar_empleados_turnos(data.id);
			$("#administrar_turnos_biblioteca").modal();
		});
		$(`#tabla_turnos_bib tbody`).on("click", "tr .eliminar", function () {
			let data = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			swal(
				{
					title: "Estas Seguro ?",
					text: `Esta seguro que desea eliminar el turno, esto eliminar todas las asignaciones que este contenga, si desea continuar presione la opción de 'Si, Entiendo'.!`,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Si, Entiendo!",
					cancelButtonText: "No, Cancelar!",
					allowOutsideClick: true,
					closeOnConfirm: false,
					closeOnCancel: true
				},
				function (isConfirm) {
					if (isConfirm) {
						eliminar_turno(data.id);
					}
				}
			);
		});
	})
}

const listar_empleados_turnos = (id) => {
	$("#tabla_empleados_bib tbody")
		.off("click", "tr")
		.off("click", "tr td:nth-of-type(1)")
		.off("click", "tr .asignar")
		.off("click", "tr .retirar");
	consulta_ajax(`${ruta}listar_empleados_turnos`, { id }, resp => {
		const myTable = $("#tabla_empleados_bib").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "nombre_completo"
				},
				{
					data: "identificacion"
				},
				{
					data: "accion"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: []
		});
		$(`#tabla_empleados_bib tbody`).on("click", "tr .asignar", function () {
			let data = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			asignar_turno(id, data.id);
		});
		$(`#tabla_empleados_bib tbody`).on("click", "tr .retirar", function () {
			let data = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			retirar_turno(id, data.tipo);
		});
	});
}

const listar_encuestas = (id_solicitud) => {
	consulta_ajax(`${ruta}listar_encuestas`, { id_solicitud }, resp => {
		const myTable = $("#tabla_encuesta_solicitud").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "programa"
				},
				{
					data: "rol_principal"
				},
				{
					data: "utilidad"
				},
				{
					data: "puntualidad"
				},
				{
					data: "auxiliar"
				},
				{
					data: "recomendacion"
				},
				{
					data: "autorizo"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones()
		});
	});
}

const listar_nivel_capa = () => {
	$("#tabla_capacitaciones_bib tbody")
		.off("click", "tr")
		.off("click", "tr td:nth-of-type(1)")
		.off("click", "tr .asignar")
	consulta_ajax(`${ruta}listar_nivel_capa`, {}, resp => {
		const myTable = $("#tabla_capacitaciones_bib").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "nivel"
				},
				{
					data: "nombre"
				},
				{
					data: "duracion"
				},
				{
					data: "accion"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: []
		});
		$(`#tabla_capacitaciones_bib tbody`).on("click", "tr .asignar", function () {
			let { id, nombre, duracion } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			let capacitacion = {
				"id_capacitacion": id,
				"nombre": nombre,
				"duracion": duracion
			}
			let found = capacitaciones.find(element => element.id_capacitacion === id);
			if (found) MensajeConClase("Esta capacitación ya ha sido agregada a la solicitud.", "info", "Oops..!");
			else {
				MensajeConClase("Capacitación agregada con exito.!", "success", "Proceso Exitoso.!")
				capacitaciones.push(capacitacion);
				capacitaciones_agregadas("new");
				capacitaciones_agregadas("mod");
				let hora_inicio = $("#hora_inicio").val();
				$("#hora_fin").val(asignar_hora_fin(hora_inicio));
				let hora_inicio_mod = $("#form_modificar_solicitud input[name=hora_entrega]").val();
				$("#form_modificar_solicitud input[name=hora_retiro]").val(asignar_hora_fin(hora_inicio_mod));
			}
		});
	});
}

const modificar_ubicacion = (data) => {
	consulta_ajax(`${ruta}modificar_ubicacion`, data, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
		listar_solicitud();
	});
}

const asignar_turno = (id, id_auxiliar) => {
	consulta_ajax(`${ruta}asignar_turno`, { id, id_auxiliar }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
		listar_empleados_turnos(id);
	});
}

const retirar_turno = (id, tipo) => {
	consulta_ajax(`${ruta}retirar_turno`, { tipo }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
		listar_empleados_turnos(id);
	});
}

const eliminar_turno = (id) => {
	consulta_ajax(`${ruta}eliminar_turno`, { id }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
		listar_turnos();
	});
}

const asignar_proceso_persona = (id, id_aux) => {
	consulta_ajax(`${ruta}asignar_proceso_persona`, { id, id_aux }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
		listar_procesos(id);
	});
}

const asignar_estado_proceso = (id, id_aux, id_auxiliar, tipo_sol) => {
	consulta_ajax(`${ruta}asignar_estado_proceso`, { id, id_aux, id_auxiliar, tipo_sol }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
		listar_estados_procesos(id);
	});
}

const retirar_proceso_persona = (id, tipo) => {
	consulta_ajax(`${ruta}retirar_procesos_persona`, { tipo }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
		listar_procesos(id);
	});
}

const retirar_estado_proceso = (id, tipo) => {
	consulta_ajax(`${ruta}retirar_estado_proceso`, { tipo }, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
		listar_estados_procesos(id);
	});
}

const listar_estados_procesos = (id, id_auxiliar, tipo_sol) => {
	$(`#tabla_estados_bib tbody`)
		.off("click", "tr")
		.off("click", "tr td:nth-of-type(1)")
		.off("click", "tr .asignar")
		.off("click", "tr .retirar");
	let i = 0;
	consulta_ajax(`${ruta}listar_estados_bib`, { id }, resp => {
		const myTable = $("#tabla_estados_bib").DataTable({
			destroy: true,
			processing: true,
			"autoWidth": false,
			data: resp,
			columns: [
				{
					data: "nombre"
				},
				{
					data: "accion"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: []
		});
		$(`#tabla_estados_bib tbody`).on("click", "tr .asignar", function () {
			let { id_aux } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			asignar_estado_proceso(id, id_aux, id_auxiliar, tipo_sol);
		});
		$(`#tabla_estados_bib tbody`).on("click", "tr .retirar", function () {
			let { tipo } = myTable
				.row(
					$(this)
						.parent()
						.parent()
				)
				.data();
			retirar_estado_proceso(id, tipo);
		});
	});
}

const pintar_empleados_combo = (datos, combo, mensaje, sele = "") => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach(element => {
		$(combo).append(
			`<option value='${element.id}'> ${element.nombre_completo}</option>`
		);
	});
	$(combo).val(sele);
}

const pintar_datos_combo = (datos, combo, mensaje, sele = "") => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach(elemento => {
		$(combo).append(
			`<option value='${elemento.id}' data-grupo="${elemento.cod_grupo}"> ${elemento.valor}</option>`
		);
	});
	$(combo).val(sele);
};

const mostrar_nombre_persona = (data) => {
	let { id, nombre_completo } = data;
	pintar_materias_docente(id);
	solicitante = { id, nombre_completo };
	$(container_activo).val(nombre_completo);
	$("#modal_buscar_empleado").modal("hide");
	$("#modal_buscar_empleado_sol").modal("hide");
};

const guardar_auxiliares = (data) => {
	data.id_solicitud = id_solicitud;
	consulta_ajax(`${ruta}guardar_auxiliares`, data, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			listar_auxiliares(id_solicitud, '#tabla_auxiliares_solicitud');
			listar_auxiliares(id_solicitud, "#tabla_auxiliares_mod");
			$("#modal_buscar_empleado").modal("hide");
			$("#modal_seleccion_carga").modal("hide");
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};

const modificar_auxiliar = (data) => {
	consulta_ajax(`${ruta}modificar_auxiliares`, data, resp => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			listar_auxiliares(id_solicitud, '#tabla_auxiliares_solicitud');
			listar_auxiliares(id_solicitud, "#tabla_auxiliares_mod");
			$("#modal_buscar_empleado").modal("hide");
			$("#modal_seleccion_carga").modal("hide");
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const obtener_correos = (buscar) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta}obtener_correos`, { buscar }, resp => {
			resolve(resp);
		});
	});
}

const obtener_correos_aux = (buscar) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta}obtener_correos_aux`, { buscar }, resp => {
			resolve(resp);
		});
	});
}

const obtener_estudiantes_cod = (buscar) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta}obtener_estudiantes_cod`, { buscar }, resp => {
			resolve(resp);
		});
	});
}

const verificar_codigo_acceso = (codigo) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta}verificar_codigo_acceso`, { codigo }, resp => {
			resolve(resp);
		});
	});
}

const enviar_correo = async (data) => {
	let { estado, id, tipo_solicitud, id_tipo_solicitud, correo, mensaje, solicitante, tipo_solicitud_full } = data;
	let ser = `<a href="${server}index.php/biblioteca/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = -1;
	let titulo = `Biblioteca - ${tipo_solicitud}`;
	let correos = correo;
	let nombre = "";
	let body = `Se informa que su solicitud de ${tipo_solicitud_full},  fue recibida y se encuentran en proceso de verificaci&oacute;n, a partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentran su solicitud.<br><br>M&aacute;s informaci&oacuten en :${ser}
	<br><br>
	En caso de dudas, inquietud o sugerencias, puede comunicarse a trav&eacute;s de los correos: 
	<br>
	Biblioteca: <a href="mailto:Biblioteca@cuc.edu.co" ><b>Biblioteca@cuc.edu.co</b></a>
	<br>
	Libros a tu clase: <a href="mailto:Librosatuclase@cuc.edu.co"><b>Librosatuclase@cuc.edu.co</b></a>
	<br>
	ACADEMIA UNIQUEST: <a href="mailto:Consultaespecializada@cuc.edu.co"><b>Consultaespecializada@cuc.edu.co</b></a>
	<br><br>
	Biblioteca Universidad de la Costa.
	&#33;Somos un libro abierto!`;
	let = codeBooks = [];
	let sw = false;
	if (estado == 'Bib_Ent_E' && id_tipo_solicitud == 'Bib_Lib') {
		sw = true;
		let libros = await obtener_libros(id);
		correos = await obtener_correos(id);
		let codeBooks = [];
		libros.forEach(elemento => {
			if (elemento.codigo_de_barras && elemento.id_estado == 1) codeBooks.push(elemento);
		});

		titulo = "Asignacion de libro";
		nombre = "Estudiantes";
		let datos = "";
		codeBooks.forEach(element => {
			datos += `<tr><th scope="row">${element.codigo_de_barras}</th><td>${element.nombre_libro}</td><td>${element.estudiante_asignado}</td></tr>`
		});
		tabla = `<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
			</head>
			<body>
				<style>
					table {
						border-collapse: collapse;
					}
					table, th, td {
						border: 1px solid #000000;
					}
					thead {
						background-color: #d43139;
						color: #ffffff;
					}
				</style>
				<table>
					<thead>
						<tr>
							<th scope="col">Codigo de barras</th>
							<th scope="col">Nombre del libro</th>
							<th scope="col">Asignado a</th>
						</tr>
					</thead>
					<tbody>
						${datos}
					</tbody>
				</table>
			</body>
		</html>`
		body = `Le informamos que se cargar&aacute a su cuenta un pr&eacute;stamo de material bibliogr&aacute;fico solicitado a trav&eacute;s del servicio de Libros a tu Clase solicitado por el docente ${solicitante}<br><br>${tabla}
		<br><br>
		En caso de dudas, inquietud o sugerencias, puede comunicarse a trav&eacute;s de los correos: 
		<br>
		Biblioteca: <a href="mailto:Biblioteca@cuc.edu.co"><b>Biblioteca@cuc.edu.co</b></a>
		<br>
		Libros a tu clase: <a href="mailto:Librosatuclase@cuc.edu.co"><b>Librosatuclase@cuc.edu.co</b></a>
		<br>
		ACADEMIA UNIQUEST: <a href="mailto:Consultaespecializada@cuc.edu.co"><b>Consultaespecializada@cuc.edu.co</b></a>
		<br><br>
		Biblioteca Universidad de la Costa.
		&#33;Somos un libro abierto!`;
		tipo = 3;
	} else if (estado == 'Bib_Rec_E') {
		sw = true;
		tipo = 1;
		titulo = `Solicitud ${tipo_solicitud} Negada`;
		nombre = `${solicitante}`;
		body = `Se informa que su solicitud de ${tipo_solicitud_full} realizada por usted,  fue negada por los siguientes motivos: <br> ${mensaje}.<br><br>A partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentran su solicitud.<br><br>M&aacute;s informaci&oacuten en : ${ser}
		<br><br>
		En caso de dudas, inquietud o sugerencias, puede comunicarse a trav&eacute;s de los correos: 
		<br>
		Biblioteca: <a href="mailto:Biblioteca@cuc.edu.co"><b>Biblioteca@cuc.edu.co</b></a>
		<br>
		Libros a tu clase: <a href="mailto:Librosatuclase@cuc.edu.co"><b>Librosatuclase@cuc.edu.co</b></a>
		<br>
		ACADEMIA UNIQUEST: <a href="mailto:Consultaespecializada@cuc.edu.co"><b>Consultaespecializada@cuc.edu.co</b></a>
		<br><br>
		Biblioteca Universidad de la Costa.
		&#33;Somos un libro abierto!`
	} else if (estado == 'Bib_Sol_E') {
		sw = true;
		tipo = 1;
		nombre = `${solicitante}`
	} else if (estado == 'Bib_Fin_E') {
		sw = true;
		tipo = 3;
		correos = await obtener_correos(id);
		titulo = `Solicitud ${tipo_solicitud} Finalizada`;
		ser = `<a href="${server}index.php/biblioteca/libros_a_tu_clase/encuesta"><b>agil.cuc.edu.co</b></a>`;
		body = `Se informa que el servicio de ${tipo_solicitud_full} fue finalizada con exito.<br><br>Lo invitamos a diligenciar la encuesta de satisfacci&oacuten, en el link que aparece a continuaci&oacute;n: <br><br>
		<a href="${server}index.php/biblioteca/libros_a_tu_clase/ingresar/${id}">Encuesta de satisfacci&oacuten</a>
		<br><br>
		En caso de dudas, inquietud o sugerencias, puede comunicarse a trav&eacute;s de los correos: 
		<br>
		Biblioteca: <a href="mailto:Biblioteca@cuc.edu.co"><b>Biblioteca@cuc.edu.co</b></a>
		<br>
		Libros a tu clase: <a href="mailto:Librosatuclase@cuc.edu.co"><b>Librosatuclase@cuc.edu.co</b></a>
		<br>
		ACADEMIA UNIQUEST: <a href="mailto:Consultaespecializada@cuc.edu.co"><b>Consultaespecializada@cuc.edu.co</b></a>
		<br><br>
		Biblioteca Universidad de la Costa.
		&#33;Somos un libro abierto!`;
	} else if (estado == 'Bib_Rev_E') {
		sw = false;
		tipo = 3;
		let correos_aux = await obtener_correos_aux(id);
		let correo_ent = [];
		let correo_ret = [];
		let correo_cap = [];
		correos_aux.forEach(element => {
			if (element.accion == "Acc_Ent") correo_ent.push(element);
			else if (element.accion == "Acc_Ret") correo_ret.push(element);
			else if (element.accion == "Acc_Cap") correo_cap.push(element);
		});
		nombre = "Auxiliar";
		body_ent = `Se informa que se le fue asignado el proceso de Entrega de una solicitud de ${tipo_solicitud_full}.<br><br>Mas informaci&oacuten en: ${ser}`
		body_ret = `Se informa que se le fue asignado el proceso de Retiro de una solicitud de ${tipo_solicitud_full}.<br><br>Mas informaci&oacuten en: ${ser}`;
		body_cap = `Se informa que se le fue asignado el proceso de Capacitar de una solicitud de ${tipo_solicitud_full}.<br><br>Mas informaci&oacuten en: ${ser}`;
		if (id_tipo_solicitud == 'Bib_Lib') {
			enviar_correo_personalizado("Bib", body_ent, correo_ent, nombre, "Libros a tu clase CUC", titulo, "ParCodAdm", tipo);
			enviar_correo_personalizado("Bib", body_ret, correo_ret, nombre, "Libros a tu clase CUC", titulo, "ParCodAdm", tipo);
		} else if (id_tipo_solicitud == 'Bib_Cap') {
			enviar_correo_personalizado("Bib", body_cap, correo_cap, nombre, "Academia Uniquest CUC", titulo, "ParCodAdm", tipo);
		}
	}
	if (sw) enviar_correo_personalizado("Bib", body, correos, nombre, "Libros a tu clase CUC", titulo, "ParCodAdm", tipo);
}

const guardar_encuesta = (code) => {
	let fordata = new FormData(document.getElementById("form_encuesta"));
	let data = formDataToJson(fordata);
	data.codigo = code;
	if (!data.utilidad) MensajeConClase('Por favor seleccione si el servicio fue util y agradable.', "info", "Oops..!");
	else if (!data.puntualidad) MensajeConClase('Por favor seleccione si el servicio fue puntual.', "info", "Oops..!");
	else if (!data.auxiliar) MensajeConClase('Por favor seleccione si el auxiliar fue atento y educado.', "info", "Oops..!");
	else if (!data.recomendacion) MensajeConClase('Por favor indique si recomendaria el servicio.', "info", "Oops..!");
	else {
		consulta_ajax(`${ruta}guardar_encuesta`, data, resp => {
			let { titulo, mensaje, tipo } = resp;
			if (tipo == "success") {
				window.location.replace(`${Traer_Server()}index.php/biblioteca/libros_a_tu_clase/encuesta_enviada`);
			} else {
				MensajeConClase(mensaje, tipo, titulo);
			}
		});
	}
}

const logear_biblioteca = (id) => {
	$("#form_logear input[name='id_solicitud']").val(id);
	$("#modal_logear").modal();
	$("#btn_login_encuesta").click(() => {
		swal(
			{
				title: "Estas Seguro ?",
				text: `En cumplimiento de la Ley 1581 de 2012 y sus decretos reglamentarios en calidad de titular(es) de la información de manera libre, expresa e informada, autorizo a UNIVERSIDAD DE LA COSTA - CUC y/o a la persona natural o jurídica a quién este encargue, a recolectar, almacenar, utilizar, circular, suprimir y en general, a usar mis datos personales para el cumplimiento de las siguientes finalidades: (i) Gestión de PQR, (ii) publicidad y prospección comercial, (iii) Enseñanza universitaria o superior. Declaro que he conocido la Política de tratamiento de datos personales publicada en www.cuc.edu.co.`,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Entiendo!",
				cancelButtonText: "No, Cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true
			},
			function (isConfirm) {
				$("#form_logear").submit();
			}
		);
	});
	/*consulta_ajax(`${ruta}logear_biblioteca`, { usuario, contrasena }, resp => {
		let { mensaje, tipo, titulo } = resp;
		if (tipo == 'success') {
		} else swal.showInputError(mensaje);
	});*/
}

const obtener_materias_por_docente = async (id) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_materias_por_docente`;
		consulta_ajax(url, { id }, resp => {
			resolve(resp);
		});
	});
}
const obtener_estudiantes_por_materia = async (materia) => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_estudiantes_por_materia`;
		consulta_ajax(url, { materia }, resp => {
			resolve(resp);
		});
	});
}

const pintar_materias_docente = async (id) => {
	materias = await obtener_materias_por_docente(id);
	pintar_datos_combo(materias, "#form_agregar_solicitud select[name=id_materia]", "Seleccione Materia");
}
const mostrar_encuesta = (data) => {
	let url = `${ruta}mostrar_encuesta`;
	consulta_ajax(url, { data }, resp => {
		retur;
	});
}

const listar_consolidado = (tabla) => {
	let tipo = tabla == "tabla_con_bib_lib" ? 'Bib_Lib' : 'Bib_Cap';
	$(`#${tabla} tbody`)
		.off("click", "tr")
		.off("dblclick", "tr")
		.off("click", "tr td:nth-of-type(1)")
		.off("click", "tr .roles")
		.off("click", "tr .programas")
		.off("click", "tr .departamentos")
	consulta_ajax(`${ruta}consolidado_encuestas`, { tipo }, resp => {
		const myTable = $(`#${tabla}`).DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "roles"
				},
				{
					data: "programas"
				},
				{
					data: "departamentos"
				},
				{
					data: "q1"
				},
				{
					data: "q2"
				},
				{
					data: "q3"
				},
				{
					data: "q4"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones()
		});

		$(`#${tabla} tbody`).on("click", "tr .roles", function () {
			consolidado_roles(tipo);
		});

		$(`#${tabla} tbody`).on("click", "tr .programas", function () {
			consolidado_programas(tipo)
		});

		$(`#${tabla} tbody`).on("click", "tr .departamentos", function () {
			consolidado_departamentos(tipo)
		});
	})
}

const consolidado_roles = (tipo) => {
	$("#modal_consolidado_roles").modal();
	consulta_ajax(`${ruta}consolidado_roles`, { tipo }, resp => {
		const myTable = $("#tabla_roles").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "roles"
				},
				{
					data: "cantidad"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones()
		});
	});
}

const consolidado_programas = (tipo) => {
	$("#modal_consolidado_programas").modal();
	consulta_ajax(`${ruta}consolidado_programas`, { tipo }, resp => {
		const myTable = $("#tabla_programas").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "programas"
				},
				{
					data: "cantidad"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones()
		})
	})
}

const consolidado_departamentos = (tipo) => {
	$("#modal_consolidado_departamentos").modal();
	consulta_ajax(`${ruta}consolidado_departamentos`, { tipo }, resp => {
		const myTable = $("#tabla_departamentos").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "departamentos"
				},
				{
					data: "cantidad"
				}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones()
		})
	})
}

