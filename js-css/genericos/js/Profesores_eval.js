let ruta = `${Traer_Server()}index.php/profesores_eval_control/`;
let callbak_activo = (resp) => {};
let id = 0;
let DATA = null;
let fecha_inicial = null;
let fecha_final = null;
let id_persona = 0;

$(document).ready(function () {
	$(".regresar_menu").click(() => {
		administrar_elementos("menu");
	});

	$("#listado_solicitudes").click(() => {
		administrar_elementos("solicitudes");
	});

	$("#plan_profesores").click(() => {
		$("#modal_abrir_plan_profesores").modal("show");
	});

	$(".container_cat").hide("fast");
	$(".container_ind").hide("fast");
	$(".container_meta").hide("fast");
	$(".container_tipo").hide("fast");

	$("#informacion_categoria").click(() => {
		$(".container_cat").toggle("fast");
	});

	$("#informacion_indicador").click(() => {
		$(".container_ind").toggle("fast");
	});

	$("#informacion_meta").click(() => {
		$(".container_meta").toggle("fast");
	});

	$("#informacion_tipo").click(() => {
		$(".container_tipo").toggle("fast");
	});

	$("#clickaqui").click(() => {
		$("#modal_detalle_evaluacion").modal("show");
		mostrar_detalle_evaluacion(DATA.data[0].id_evaluacion);
	});

	$("#filtrar_solicitudes").click(function () {
		obtener_estado("cbxestado", "Filtrar por Estado");
		$("#modal_crear_filtros").modal();
	});

	$("#form_filtros").submit((e) => {
		e.preventDefault();
		filtrar_solicitudes();
		//return false;
	});

	$("#btn_limpiar_filtros").click(() => {
		listar_solicitudes();
	});

	$("#btn-descargar_plan_profesores").click((data) => {
		if (data) {
			$("#btn-descargar_plan_profesores").attr(
				"href",
				`${Traer_Server()}index.php/profesores_eval/descargar_plan_profe/${
					DATA.data[0].id_evaluacion
				}`
			);
		} else {
			$("#btn-descargar_plan_profesores").removeAttr("href");
			MensajeConClase("No ha seleccionado una evaluación", "info", "Oops.!");
		}
	});
});

const administrar_elementos = (item) => {
	if (item == "menu") {
		$("#menu_principal").fadeIn(1000);
		$("#container_solicitudes").css("display", "none");
	} else if (item == "solicitudes") {
		$("#menu_principal").css("display", "none");
		$("#container_solicitudes").fadeIn(1000);
	}
};

const listar_solicitudes = (filtros = {}) => {
	let { fecha_inicial, fecha_final, id_estado_sol } = filtros;
	// let sw = false;
	let tabla = "#tabla_listado_solicitudes_profesores_evaluacion";
	$(`${tabla} tbody`)
		.off("click", ".ver")
		.off("click", ".ver_finalizar")
		.off("click", ".chat")
		.off("click", ".detalle_eval")
		.off("click", ".finalizar")
		.off("dblclick", "tr")
		.off("click", "tr");
	consulta_ajax(
		`${ruta}listar_solicitudes`,
		{
			fecha_inicial,
			fecha_final,
			id_estado_sol,
		},
		(resp) => {
			const myTable = $(`${tabla}`).DataTable({
				destroy: true,
				processing: true,
				data: resp,
				columns: [
					{
						data: "ver",
					},
					{
						data: "fullname",
					},
					{
						data: "fecha_registro",
					},
					{
						data: "estado",
					},
					{
						data: "acciones",
					},
				],
				language: idioma,
				dom: "Bfrtip",
				buttons: get_botones(),
			});

			$(`${tabla} tbody`).on("click", "tr", function () {
				$(`${tabla} tbody tr`).removeClass("warning");
				let data = myTable.row(this).data();
				if (!data) {
					$(`${tabla} tbody tr`).removeClass("warning");
				} else {
					$(this).attr("class", "warning");
				}
			});

			$(`${tabla} tbody`).on("dblclick", "tr", function () {
				let data = myTable.row(this).data();
				if (!data) {
					MensajeConClase("No hay informacion que mostrar", "info", "Oops.!");
				} else {
					$("#modal_abrir_plan_profesores").modal("show");
				}
			});

			$(`${tabla} tbody`).on("click", ".ver", function () {
				let data = myTable.row($(this).parent()).data();
				DATA = data;
				$("#modal_abrir_plan_profesores").modal("show");
				obtener_tipos_solicitud("Eva_desemp", "cbxeva_desemp");
				obtener_tipos_solicitud("Logros_Prof_Eval", "cbxlogros");
				obtener_tipos_solicitud("PlanTrab_Prof_Eval", "cbxplanTrab");
				mostrar_informacion_completa_evaluacion(data);
			});

			$(`${tabla} tbody`).on("click", ".ver_finalizar", function () {
				let data = myTable.row($(this).parent()).data();
				DATA = data;
				$("#modal_abrir_plan_profesores").modal("show");
				mostrar_informacion_completa_evaluacion(data);
			});

			$(`${tabla} tbody`).on("click", ".detalle_eval", function () {
				let data = myTable.row($(this).parent()).data();
				$("#modal_detalle_evaluacion").modal("show");
				mostrar_detalle_evaluacion(data.data[0].id_evaluacion);
			});

			$(`${tabla} tbody`).on("click", ".finalizar", function () {
				let data = myTable.row($(this).parent()).data();
				let { id } = data;
				cambiar_estado_solicitud(id, "Eval_Fin", "Finalizar revisión", 2);
			});

			$(`${tabla} tbody`).on("click", ".chat", function () {
				let data = myTable.row($(this).parent()).data();
				let { id } = data;
				comentarios(id, "Describa su comentario");
			});
		}
	);
	// Con_filtros(sw)
};

mostrar_detalle_evaluacion = (id_evaluacion) => {
	let tabla = "#tabla_detalle_evaluacion";
	$(`${tabla} tbody`)
		.off("click", "ver_rol")
		.off("click", "ver_rol2")
		.off("click", "ver_rol3")
		.off("click", "ver_doc")
		.off("click", "ver_inv");
	consulta_ajax(
		`${ruta}listar_detalle_evaluacion`,
		{ id_evaluacion },
		(resp) => {
			const myTable = $(`${tabla}`).DataTable({
				destroy: true,
				processing: true,
				data: resp,
				columns: [
					{
						data: "ver",
					},
					{
						data: "categoria",
					},
					{
						data: "indicador",
					},
					{
						data: "tipo",
					},
					{
						data: "nota",
					},
					{
						render: function (data, type, full, meta) {
							let { nota } = full;
							let not = Number(nota);
							if (not <= 3.7) {
								return `<span style="color: red; font-weight: bold;">D</span>`;
							} else if (not > 3.7 && not <= 4) {
								return `<span style="color: orange; font-weight: bold;">A</span>`;
							} else if ((not > 4) & (not <= 4.5)) {
								return `<span style="color: green; font-weight: bold;">B</span>`;
							} else {
								return `<span style="color: blue; font-weight: bold;">S</span>`;
							}
						},
					},
				],
				language: idioma,
				dom: "Bfrtip",
				buttons: get_botones(),
			});

			$(`${tabla} tbody`).on("click", ".ver_doc", function () {
				let data = myTable.row($(this).parent()).data();
				$("#modal_detalle_indicadores_evaluacion").modal("show");
				mostrar_detalle_indicador_evaluacion(data);
			});

			$(`${tabla} tbody`).on("click", ".ver_inv", function () {
				let data = myTable.row($(this).parent()).data();
				$("#modal_detalle_indicadores_evaluacion").modal("show");
				mostrar_detalle_indicador_evaluacion(data);
			});

			$(`${tabla} tbody`).on("click", ".ver_rol", function () {
				let data = myTable.row($(this).parent()).data();
				$("#modal_detalle_indicadores_evaluacion").modal("show");
				mostrar_detalle_indicador_evaluacion(data);
			});

			$(`${tabla} tbody`).on("click", ".ver_rol2", function () {
				let data = myTable.row($(this).parent()).data();
				$("#modal_detalle_indicadores_evaluacion").modal("show");
				mostrar_detalle_indicador_evaluacion(data);
			});

			$(`${tabla} tbody`).on("click", ".ver_rol3", function () {
				let data = myTable.row($(this).parent()).data();
				$("#modal_detalle_indicadores_evaluacion").modal("show");
				mostrar_detalle_indicador_evaluacion(data);
			});
		}
	);
};

detalle_grafica_evaluacion = (data) => {
	let ctx = document.getElementById("myChart");
	let labels = data.categoria.map((i) => i.categoria);
	let datos = data.categoria.map((i) => Number(i.calificacion_cat));
	let evaluacion = new Chart(ctx, {
		// The type of chart we want to create
		type: "radar",
		// The data for our dataset
		data: {
			labels: labels,
			datasets: [
				{
					label: "Evaluación de desempeño",
					backgroundColor: "rgba(255, 218, 99, 0.3)",
					data: datos,
				},
			],
		},
		// Configuration options go here
		options: {
			scale: {
				angleLines: {
					display: false,
				},
				ticks: {
					max: 5,
					min: 0,
					stepSize: 1,
				},
				pointLabels: {
					display: true,
					fontSize: 15,
				},
				gridLines: {
					zeroLineWidth: 5,
				},
			},
		},
	});
	return evaluacion;
};

detalle_grafica_porcentaje = (data) => {
	let ctx2 = document.getElementById("myChart2");
	let labels = data.categoria.map((i) => i.categoria);
	let datos = data.categoria.map((i) => Number(i.porcentaje));
	let porcentaje = new Chart(ctx2, {
		type: "doughnut",
		data: {
			labels: labels,
			datasets: [
				{
					label: "Porcentaje evaluación de desempeño",
					backgroundColor: [
						"#ecb400",
						"#ffe89e",
						"#fff6d9",
						"#b28800",
						"#3c2e00",
						"#fffbed",
					],
					data: datos,
				},
			],
		},
	});
	return porcentaje;
};

mostrar_informacion_completa_evaluacion = (data) => {
	const nota_c = (data) => {
		let { calificacion_profesor, evaluacion } = data;
		let nota = Number(calificacion_profesor);
		if (nota < 3.75) {
			$(".nota_cuantitativa").html(calificacion_profesor).css("color", "red");
			$(".nota_cualitativa").html(evaluacion).css("color", "#F1948A");
		} else if (nota >= 3.75 && nota < 4) {
			$(".nota_cuantitativa")
				.html(calificacion_profesor)
				.css("color", "orange");
			$(".nota_cualitativa").html(evaluacion).css("color", "#F4D03F");
		} else if ((nota >= 4) & (nota <= 4.5)) {
			$(".nota_cuantitativa").html(calificacion_profesor).css("color", "green");
			$(".nota_cualitativa").html(evaluacion).css("color", "#48C9B0");
		} else {
			$(".nota_cuantitativa").html(calificacion_profesor).css("color", "blue");
			$(".nota_cualitativa").html(evaluacion).css("color", "#85C1E9");
		}
	};

	$(".nombre-completo-profesor").html(data.fullname);

	nota_c(data);
	// $('.nota_cualitativa').html(data.evaluacion)
	// $('.nota_cuantitativa').html(data.calificacion_profesor)

	detalle_grafica_evaluacion(data);
	detalle_grafica_porcentaje(data);
};

mostrar_detalle_indicador_evaluacion = (data) => {
	let {
		categoria,
		calificacion_cat,
		fecha_inicio,
		fecha_fin,
		indicador,
		logro,
		meta,
		nota,
		peso_ind,
		porcentaje,
		tipo,
		valor,
	} = data;

	const nota_c = (nota) => {
		let not = Number(nota);
		if (not < 3.75) {
			$(".data_nota_cual")
				.html("D")
				.css("color", "red")
				.css("font-weight", "bold");
		} else if (not >= 3.75 && not < 4) {
			$(".data_nota_cual")
				.html("A")
				.css("color", "orange")
				.css("font-weight", "bold");
		} else if ((not >= 4) & (not <= 4.5)) {
			$(".data_nota_cual")
				.html("B")
				.css("color", "green")
				.css("font-weight", "bold");
		} else {
			$(".data_nota_cual")
				.html("S")
				.css("color", "blue")
				.css("font-weight", "bold");
		}
	};

	$(".data_categoria").html(categoria);
	$(".data_calificacion_cat").html(calificacion_cat);
	$(".data_fecha_fin").html(fecha_fin);
	$(".data_fecha_inicio").html(fecha_inicio);
	$(".data_indicador").html(indicador);
	$(".data_logro").html(logro);
	$(".data_meta").html(meta);
	$(".data_nota_cuan").html(nota);
	nota_c(nota);
	$(".data_peso_ind").html(`${peso_ind}%`);
	$(".data_porcentaje").html(`${porcentaje}%`);
	$(".data_tipo").html(tipo);
	// $('.data_valor').html(valor)
};

const cambiarEstado = (estado, id_solicitud, observaciones = "") => {
	consulta_ajax(
		`${ruta}cambiarEstado`,
		{ estado, id_solicitud, observaciones },
		(resp) => {
			const { titulo, mensaje, tipo } = resp;
			if (tipo == "success") {
				swal.close();
				listar_solicitudes();
			} else {
				MensajeConClase(mensaje, tipo, titulo);
			}
		}
	);
};

const comentar = (id_solicitud, mensaje = "") => {
	consulta_ajax(`${ruta}comentar`, { id_solicitud, mensaje }, (resp) => {
		const { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase("Comentario almacenado con exito", tipo, titulo);
			listar_solicitudes();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};
const comentarios = (id, title) => {
	swal(
		{
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
			inputPlaceholder: `Comentario`,
		},
		function (mensaje) {
			if (mensaje === false) return false;
			if (mensaje === "") {
				swal.showInputError(`Por favor, escriba un comentario!`);
			} else {
				comentar(id, mensaje);
			}
		}
	);
};

const cambiar_estado_solicitud = (id, estado, titulo, tipo, tipo_fin = "") => {
	const confirm_normal = (id, estado, title) => {
		swal(
			{
				title,
				text:
					"Tener en cuenta que, al realizar esta accíon la solicitud sera habilitada para el siguiente  proceso, si desea continuar debe  presionar la opción de 'Si, Entiendo' !",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Entiendo!",
				cancelButtonText: "No, cancelar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true,
			},
			function (isConfirm) {
				if (isConfirm) {
					cambiarEstado(estado, id, "", tipo_fin);
				}
			}
		);
	};

	const confirm_input = (id, estado, title) => {
		swal(
			{
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
				inputPlaceholder: `Observaciones`,
			},
			function (mensaje) {
				if (mensaje === false) return false;
				if (mensaje === "") {
					swal.showInputError(`Debe Ingresar una observación.!`);
				} else {
					cambiarEstado(estado, id, mensaje);
				}
			}
		);
	};
	tipo == 1
		? confirm_normal(id, estado, titulo)
		: confirm_input(id, estado, titulo);
};

const obtener_valor_parametro = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_valor_parametro`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_tipos_solicitud = async (id, label) => {
	let mensaje = await obtener_valor_parametro(id);
	pintarData(`.${label}`, mensaje);
};

const pintarData = (label, mensaje) => {
	$(label).html(`${mensaje.valor}`);
};

const obtener_vParametro = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_vParametro`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_estado = async (select, mensaje) => {
	let estados = await obtener_vParametro(232);
	pintar_datos_combo_1(estados, `.${select}`, mensaje);
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
				`<option value='${element.id_aux}'> ${element.valor} </option>`
			);
		}
	});
};

const filtrar_solicitudes = () => {
	data = {
		id_estado_sol: $("#modal_crear_filtros select[name='id_estado']").val(),
		fecha_inicial: $("#modal_crear_filtros input[name='fecha_inicial']").val(),
		fecha_final: $("#modal_crear_filtros input[name='fecha_final']").val(),
	};
	listar_solicitudes(data);
};
