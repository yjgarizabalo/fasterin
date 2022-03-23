let ruta = `${Traer_Server()}index.php/publicaciones_control/`;
let autores = [];
let porcentaje = 100;
let cambio = false;
let proyecto = {};
let revista = {};
let callbak_activo = (resp) => {};
let id_pub_global = null;
let fecha_pos = "";
let fecha_ace = "";
let fecha_rec = "";
let fecha_pub = "";
let inicial = {};
let url_pub = "";
let id_estado_global = "";
let idiomas = [];
let afiliacion = {};
let id_temp = null;
let sw_rev = false;
let status_selected = {};
let tipos_adjs = [];
let campo_fechaa = "";
let id_solicitud = null;
let id_porcentaje = null;
let id_porcentaje_cp = null;
let porcentajes = [];
let id_proyecto_bon = null;
let articulos_suscritos = [];
let articulos_cumplidos = [];
let info_articulos_suscritos = [];
let info_articulos_cumplidos = [];
let autores_profesores = [];
let autores_estudiantes = [];
let autores_externos = [];
let tabla_buscar = "";
let id_afiliacion = "";
let id_autor_bon = null;
let afil_inst = [];
let id_persona_afil_inst = "";
let data_preguntas = [];
let data_tipos_escrituras = [];
let tipo_gestion = "";
let cargados = 0;
let id_comite = "";
let idpais = "";
let id_persona = "";
let actividad_selec = "";
let total_liquidacion = 0;
let porcentajes_cp = [];
let data_solicitante = { nombre: null, correo: null };
let data_permisos_not = { nombre: null, correo: null };
const ruta_archivos = "archivos_adjuntos/publicaciones/";
const ruta_documentos = `${Traer_Server()}archivos_adjuntos/bonificaciones/`;

$(document).ready(() => {

	$(".selectpicker").selectpicker();

	$('button[class="close"]').css({ color: "white" });

	$("#pub_year").on("keypress blur", function (e) {
		if (isNaN($(this).val())) {
			swal({
				title: `Dato incorrecto.`,
				text: "Asegurece de escribir un año valido que no supere al actual.",
				type: "warning",
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Corregir",
				allowOutsideClick: true,
				closeOnConfirm: false,
			});
			$(this).val("");
		}
		if (num_o_string("int", e.keyCode) == false) {
			return false;
		}
		limite_caracteres(4, $(this));
	});

	$("#pubs_status").change(function () {
		let valor = $(this).val();
		status_selected = { idaux: $(this).find(":selected").attr("data-aux") };
		$("#aviso_estados").addClass("oculto");
		call_inputs(status_selected);
	});

	/* Pago Papers */
	$("#nuevo_pago").on("click", function () {
		$("#modal_nuevo_pago").modal();
	});

	const buscar_tipo_pago = (buscar) => {
		consulta_ajax(`${ruta}buscar_tipo_pago`, { buscar }, (resp) => {
			for (let key in resp) {
				$("#tipo_pago_select").append(
					`<option value="${resp[key].id}">${resp[key].pago}</option>`
				);
			}
		});
	};

	buscar_tipo_pago();

	$("#tipo_pago_select").on("change", function () {
		let campo_valor = $(this).val();
		let input_padre = $("#segun");
		inputs_array = [
			"#num_documento_identidad",
			"#banco_select",
			"#tipo_tarjeta_select",
			"#tipo_moneda_select",
			"#pago_valor",
			"#num_identi_articulo",
			"#pago_link",
			".adjs_pagointer",
			".adjs_monedainter",
		];
		hide_or_show(campo_valor, inputs_array, input_padre);
	});

	const hide_or_show = (tipo_pago, inputs, input_padre) => {
		if (tipo_pago == 219564) {
			//Pago nacional
			input_padre.show();
			for (const key in inputs) {
				if (
					inputs[key] == "#pago_link" ||
					inputs[key] == ".adjs_pagointer" ||
					inputs[key] == ".adjs_monedainter"
				) {
					$(inputs[key]).hide();
					$('input[name="adj3"], input[name="adj4"]').attr("disabled", true);
				} else {
					$(inputs[key]).show();
				}
			}
		} else if (tipo_pago == 219565) {
			//Pago mediante link
			input_padre.show();
			for (const key in inputs) {
				if (
					inputs[key] == "#pago_link" ||
					inputs[key] == "#tipo_moneda_select" ||
					inputs[key] == "#pago_valor"
				) {
					$(inputs[key]).show();
					$('input[name="adj3"], input[name="adj4"]').attr("disabled", true);
				} else {
					$(inputs[key]).hide();
				}
			}
		} else if (tipo_pago == 219566) {
			//Pago internacional
			input_padre.show();
			for (const key in inputs) {
				if (
					inputs[key] == ".adjs_pagointer" ||
					inputs[key] == ".adjs_monedainter" ||
					inputs[key] == "#tipo_moneda_select" ||
					inputs[key] == "#pago_valor"
				) {
					$(inputs[key]).show();
					$('input[name="adj3"], input[name="adj4"]').attr("disabled", false);
				} else {
					$(inputs[key]).hide();
				}
			}
		} else {
			input_padre.hide();
		}
	};

	const buscar_tipo_cuentab = (buscar) => {
		consulta_ajax(`${ruta}buscar_tipo_cuentab`, { buscar }, (resp) => {
			for (let key in resp) {
				$("#tipo_tarjeta_select").append(
					`<option value="${resp[key].id_tipo_tarjeta}">${resp[key].tarjeta}</option>`
				);
			}
		});
	};

	$(".btn_buscar_pais").click(function () {
		$("#txt_pais_buscado").val("");
		$("#modal_listar_paises").modal();		
        return false;
    });

	$("#form_listar_paises").submit(() => {		
        let valor_buscado = $("#txt_pais_buscado").val();		
        buscar_pais(valor_buscado);
        return false;
    });

	$("#form_agregar_enlaces").submit((e) => {
		e.preventDefault();
		guardar_links_aut();
		//return false;
	});

	buscar_tipo_cuentab();

	const listar_bancos = (buscar) => {
		consulta_ajax(`${ruta}listar_bancos`, { buscar }, (resp) => {
			for (let key in resp) {
				$("#banco_select").append(
					`<option value="${resp[key].idbanco}">${resp[key].banco}</option>`
				);
			}
		});
	};

	listar_bancos();

	/* Buscar articulo */

	$(".btn_buscar_art").click(function () {
		$("#txt_dato_articulo").val("");
		buscar_articulo("undefined");
		$("#modal_buscar_articulo").modal();
		callbak_activo = ({ titulo_articulo, id }) => {
			$("#nombre_articulo").val(titulo_articulo).attr("data-art_id", id);
			$("#form_buscar_articulo").get(0).reset();
			$("#modal_buscar_articulo").modal("hide");
		};
	});

	$(".btn_busc_art").click(function () {
		let valor = $("#txt_dato_articulo").val();
		if (valor === "") {
			$("#txt_dato_articulo").focus();
			return false;
		}
		buscar_articulo(valor, callbak_activo);
		return false;
	});

	const buscar_articulo = (dato_buscar, callback) => {
		$("#tabla_articulo_busqueda tbody").off("click", "tr td .seleccionar");
		consulta_ajax(`${ruta}buscar_articulos`, { dato_buscar }, (data) => {
			const myTable = $("#tabla_articulo_busqueda").DataTable({
				destroy: true,
				processing: true,
				searching: false,
				data,
				columns: [
					{ data: "titulo_articulo" },
					{
						data: "id",
						render: function (data) {
							return `<span style="color: #39B23B;" data-art_id="${data}" title="Seleccionar Articulo" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>`;
						},
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});

			$("#tabla_articulo_busqueda tbody").on(
				"click",
				"tr td .seleccionar",
				function () {
					let data = myTable.row($(this).parent()).data();
					callback(data);
					buscar_autores(data.titulo_articulo, callbak_activo);
					$("#nombre_revista option").each(function () {
						$(this).next().remove();
					});
					$("#nombre_revista").append(
						`<option value="${data.idrevista}">${data.nombre_revista}</option>`
					);
					MensajeConClase(
						"Datos como Autores, Cuartil y Nombre de Revista, ya están disponibles para ser seleccionados.",
						"success",
						data.titulo_articulo + " seleccionado!"
					);
				}
			);
		});
	};

	const listar_cuartil = (buscar) => {
		consulta_ajax(`${ruta}buscar_cuartil`, { buscar }, (data) => {
			for (const key in data) {
				$('select[name="cuartil"], select[name="cuartil_id"]').append(
					`<option value="${data[key].idcuartil}">${data[key].cuartil}</option>`
				);
			}
		});
	};

	listar_cuartil();

	/* Buscar Autores segun el articulo seleccionado */

	$(".btn_buscar_autor").click(function () {
		let valor = $("#nombre_articulo").val();
		if (valor === "") {
			MensajeConClase(
				"Debe seleccionar primero un artículo, para visualizar sus autores.",
				"warning",
				"Aviso!"
			);
			return false;
		}
		$("#modal_buscar_autores").modal();
		return false;
	});

	const buscar_autores = (dato_buscar, callback) => {
		consulta_ajax(`${ruta}buscar_autores`, { dato_buscar }, (data) => {
			$("#tabla_autor_busqueda").DataTable({
				destroy: true,
				processing: true,
				searching: false,
				data,
				columns: [
					{ data: "full_name" },
					{
						data: "autorid",
						render: function (data) {
							return `<input type="text" value="" class="form-control text-center autores_porcen" name="porcentaje_autor" id="${data}" data-ide="porcentaje_autor" placeholder="Ingrese el procenaje correspondiente" required>`;
						},
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});

			let input_porcen = $('input[name="porcentaje_autor"]');

			$(".btn_createArray").on("click", function () {
				input_porcen.each(function () {
					if ($(this).val() == "") {
						MensajeConClase(
							`Debe completar todos los campos.`,
							"warning",
							"Oops!"
						);
					}
				});
				limitar(input_porcen, 100, "#modal_buscar_autores");
			});

			const limitar = (inputs_a_evaluar, limite, modal_cerrar) => {
				let sumar_elementos = [];
				let elementos = [];
				let n = 0;
				for (let x = 0; x < inputs_a_evaluar.length; x++) {
					sumar_elementos.push(parseFloat(inputs_a_evaluar[x].value));
					let elements = [inputs_a_evaluar[x].id, inputs_a_evaluar[x].value];
					elementos.push(elements);
				}
				sumar_elementos.forEach(function (numero) {
					n += numero;
				});

				if (!isNaN(n)) {
					if (n !== limite) {
						MensajeConClase(
							`La suma de los porcentajes insertados no cumplen con el ${limite}% requerido, favor rectifique.`,
							"warning",
							"Oops!"
						);
						return false;
					} else {
						MensajeConClase(
							`La suma de los porcentajes insertados cumplen con el ${limite}% requerido.`,
							"success",
							"Muy bien!"
						);
						$(modal_cerrar).modal("hide");
					}
				}
			};
		});
	};

	/* Buscar codigo SAP */

	$(".btn_buscar_codsap").click(function () {
		$("#txt_dato_codsap").val("");
		buscar_codsap("undefined");
		$("#modal_buscar_codsap").modal();
		callbak_activo = ({ cod_nombre, cod_id }) => {
			$("#num_sap").val(cod_nombre).attr("data-codsap_id", cod_id);
			$("#form_buscar_codsap").get(0).reset();
			$("#modal_buscar_codsap").modal("hide");
		};
	});
	$("#btn_send_sol").click(function () {
		enviar_solicitud();
	});

	$(".btn_busc_codsap").click(function () {
		let valor = $("#txt_dato_codsap").val();
		if (valor === "") {
			$("#txt_dato_codsap").focus();
			return false;
		}
		buscar_codsap(valor, callbak_activo);
		return false;
	});

	const buscar_codsap = (dato_buscar, callback) => {
		$("#tabla_codsap_busqueda tbody").off("click", "tr td .seleccionar");
		consulta_ajax(`${ruta}buscar_codsap`, { dato_buscar }, (data) => {
			const myTable = $("#tabla_codsap_busqueda").DataTable({
				destroy: true,
				processing: true,
				searching: false,
				data,
				columns: [
					{ data: "cod_sap" },
					{ data: "cod_nombre" },
					{
						defaultContent:
							'<span style="color: #39B23B;" title="Seleccionar COD.SAP" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>',
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});

			$("#tabla_codsap_busqueda tbody").on(
				"click",
				"tr td .seleccionar",
				function () {
					let data = myTable.row($(this).parent()).data();
					callback(data);
				}
			);
		});
	};

	$(".dinero").on("change", function (e) {
		$(this).focus(function () {
			$(this).val("");
		});
		let input_name = `input[name=${e.target.name}]`;
		let input_value = $("#tipo_moneda").attr("data-divisa");
		let decimal = $("#tipo_moneda").attr("data-zeros");
		convertir_moneda(
			$(input_name).val(),
			input_name,
			input_value,
			input_value,
			decimal
		);
	});

	const convertir_moneda = (
		valor,
		input_name,
		tipo_moneda,
		moneda,
		decimal
	) => {
		let nv = valor.replace(/\W/g, "");
		let convierte = new Intl.NumberFormat(tipo_moneda, {
			style: "currency",
			currency: moneda,
			minimumFractionDigits: 0,
		});
		$(input_name).val(convierte.format(nv));
	};

	/*LISTAR LOS TIPOS DE PUBLICACION*/

	const listar_tipos_publicacion = (dato_buscar) => {
		consulta_ajax(`${ruta}tipos_de_publicacion`, { dato_buscar }, (data) => {
			if (data.estado !== "404") {
				data.forEach(listar_tipopub);
				function listar_tipopub(dato) {
					$("#tipo_id").append(
						`<option value="${dato.tipopub_id}">${dato.tipo_pub}</option>`
					);
				}
			}
		});
	};

	listar_tipos_publicacion();

	/*BUSCAR TIPO MONEDA*/

	$(".btn_buscar_moneda").click(function () {
		$("#txt_dato_moneda").val("");
		buscar_tipo_moneda("undefined");
		$("#modal_buscar_moneda").modal();
		callbak_activo = ({ moneda, id }) => {
			$("#tipo_moneda").val(moneda);
			$("#form_buscar_moneda").get(0).reset();
			$("#modal_buscar_moneda").modal("hide");
		};
	});

	$(".btn_busc_money").click(function () {
		let valor = $("#txt_dato_moneda").val();
		if (valor === "") {
			$("#txt_dato_moneda").focus();
			return false;
		}
		buscar_tipo_moneda(valor, callbak_activo);
		return false;
	});

	const buscar_tipo_moneda = (moneda, callback) => {
		$("#tabla_moneda_busqueda tbody").off("click", "tr td .seleccionar");
		consulta_ajax(`${ruta}buscar_tipo_moneda`, { moneda }, (data) => {
			const myTable = $("#tabla_moneda_busqueda").DataTable({
				destroy: true,
				processing: true,
				searching: false,
				data,
				columns: [
					{ data: "abreviado" },
					{ data: "moneda" },
					{
						defaultContent:
							'<span style="color: #39B23B;" title="Seleccionar Moneda" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>',
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});

			$("#tabla_moneda_busqueda tbody").on(
				"click",
				"tr td .seleccionar",
				function () {
					let data = myTable.row($(this).parent()).data();
					callback(data);
					if ($("#tipo_moneda").val() !== "") {
						$(".dinero").attr("disabled", false).val("").focus();
					} else {
						$(".dinero").attr("disabled", true);
					}
					$("#tipo_moneda")
						.attr("data-divisa", data.abreviado)
						.attr("data-zeros", data.decimales)
						.attr("data-moneda_id", data.idmoneda);
				}
			);
		});
	};

	/*Fin de buscar moneda*/

	$(".input_numerico").on("keydown keyup", function () {
		let valor = $(this).val();
		$(this).val(valor.toUpperCase());
	});

	$(".dinero").on("keypress", function (e) {
		if (e.keyCode < 48 || e.keyCode > 58) {
			return false;
		}
	});

	$(".links").blur(function () {
		validar_links($(this), ".links");
	});

	/* Solicitu nuevo pago papers */

	$("#form_solicitar_pago").on("submit", function () {
		let a = $('input[name="porcentaje_autor"]');
		let n = 0;
		for (let x = 0; x < a.length; x++) {
			if (a[x].value == "") {
				MensajeConClase(
					`Se ha detectado que uno o varios autores no tiene su respectivo procentaje asignado. Complete el formulario a continuación.`,
					"warning",
					"Faltan datos!"
				);
				$("#modal_buscar_autores").modal();
				return false;
			} else {
				n += parseFloat(a[x].value);
			}
		}

		if (parseFloat(n) !== 100) {
			MensajeConClase(
				"La suma de los pocentajes de cada autor, no llega 100% requerido.",
				"warning",
				"Aviso!"
			);
			$("#modal_buscar_autores").modal();
			return false;
		}
		//save_auths_procents();
		solicitud_pago_paper();
		return false;
	});

	//Funcion que crea el array con los porcentajes de los autores.

	const save_auths_procents = () => {
		let datos = $('input[name="porcentaje_autor"]').attr(
			"data-ide",
			"porcentaje_autor"
		);
		let id_art = $("#nombre_articulo").attr("data-art_id");
		let array_auths = [];
		datos.each(function () {
			array_auths.push({
				id_articulo: id_art,
				id_autor: $(this).attr("id"),
				puntos: $(this).val(),
			});
		});

		consulta_ajax(
			`${ruta}save_auths_procents`,
			{ datos: array_auths },
			(resp) => {
				if (resp.estado == "ok") {
					//console.log(resp);
					//MensajeConClase(resp.mensaje, resp.tipo, resp.titulo);
					reset_forms("#form_solicitar_pago", "#modal_nuevo_pago");
				} else {
					MensajeConClase(resp.mensaje, resp.tipo, resp.titulo);
				}
			}
		);
	};

	const solicitud_pago_paper = () => {
		let fordata = new FormData($("#form_solicitar_pago")[0]);
		fordata.append("codsap_id", $("#num_sap").attr("data-codsap_id"));
		fordata.append("moneda_id", $("#tipo_moneda").attr("data-moneda_id"));
		fordata.append("art_id", $("#nombre_articulo").attr("data-art_id"));
		enviar_formulario(`${ruta}solicitud_pago_papers`, fordata, (resp) => {
			if (resp.estado == "ok") {
				save_auths_procents();
				MensajeConClase(resp.mensaje, resp.tipo, resp.titulo);
				reset_forms("#form_solicitar_pago", "#modal_nuevo_pago");
				$("#segun").css({ display: "none" });
			} else {
				MensajeConClase(resp.mensaje, resp.tipo, resp.titulo);
			}
		});
	};

	const reset_forms = (formulario, modal_hide) => {
		$(formulario).get(0).reset();
		$(modal_hide).modal("hide");
	};
	/* Fin de pago papers */

	$("#regresar_menu").click(function () {
		$(".listado_solicitudes").css("display", "none");
		$("#menu_principal").fadeIn(1000);
	});

	$("#container-files").change(function () {
		activarfile();
	});

	$("#listado").click(function () {
		$("#menu_principal").css("display", "none");
		$(".listado_solicitudes").fadeIn(1000);
	});

	$("#btn_filtrar").click(function () {
		listar_publicaciones();
	});

	$("#limpiar_filtros_publicaciones").click(function () {
		$("#modal_crear_filtros select[name='id_estado']").val("");
		$("#modal_crear_filtros select[name='id_ranking']").val("");
		listar_publicaciones();
	});

	$("#nueva_solicitud").click(function () {
		$("#modal_agregar_solicitud").modal();
		$(".txt_proyecto").val("");
		$(".txt_revista").val("");
		proyecto = {};
		revista = {};
		autores = [];
		porcentaje = 100;
		idiomas = [];
		listar_indicador();
		listar_ranking();
		listar_autores_iniciales();
		listar_estados_pub();
	});

	$("#listado").click(function () {
		listar_publicaciones();
	});

	$("#txt_issn").attr("maxlength", "8");

	$("#agregar_autor").click(function () {
		$("#form_buscar_autor").get(0).reset();
		callbak_activo = (data) => {
			let autor = autores.find(
				(element) => element.nombre_completo == data.nombre_completo
			);
			if (autor)
				MensajeConClase("El estudiante ya fue asignado.", "info", "Oops.!");
			else {
				autores.push(data);
				let table = $("#tabla_autores_busqueda").DataTable();
				table.clear().draw();
				MensajeConClase(
					"Autor asignado con exito",
					"success",
					"Proceso Exitoso"
				);
			}
		};
		buscar_autor();
		$("#modal_buscar_autor").modal();
	});


	$("#form_buscar_autores_bonificaciones").submit(() => {
		let dato = $("#txt_search_author").val();
		search_author__bonus(dato, callbak_activo, tabla_buscar);
		return false;
	});

	$("#form_buscar_autor").submit(() => {
		let dato = $("#txt_aut_buscar").val();
		let tabla = $("input[name='tabla']:checked").val();
		if (tabla == null) buscar_autor(dato, callbak_activo, "");
		else buscar_autor(dato, callbak_activo, tabla);
		return false;
	});

	$("#btn_nuevo_autor").click(() => {
		$("#modal_nuevo_autor").modal();
	});

	$("#btn_nuevo_autor_boninficaciones").click(() => {
		$("#modal_nuevo_autor_bonificaciones").modal();
	});

	$("#btn_nueva_revista").click(() => {
		listar_cuartiles();
		$("#modal_almacenar_revista").modal();
	});

	$("#btn_nueva_revista_bon").click(() => {
		listar_cuartiles();
		$("#modal_almacenar_revista__bonificaciones").modal();
	});

	$("#form_nuevo_autor").submit(() => {
		cambio = false;
		guardar_nuevo_autor();
		return false;
	});

	$("#form_nuevo_autor_bon").submit((e) => {
		e.preventDefault();
		guardar_nuevo_autor_bon();
		//return false;
	});

	$("#form_almacenar_revista").submit(() => {
		almacenar_revista(callbak_activo);
		return false;
	});

	$("#form_almacenar_revista_bonificaciones").submit(() => {
		almacenar_revista_bonificaciones(callbak_activo);
		return false;
	});

	$("#form_agregar_solicitud").submit(() => {
		guardar_publicacion();
		return false;
	});

	$(".btn_proyecto").click(() => {
		container_activo = ".txt_proyecto";
		$(".txt_dato_proyecto").val("");
		callbak_activo = (resp) => {
			mostrar_nombre_proyecto(resp);
		};
		buscar_proyecto("", callbak_activo);
		$("#modal_buscar_proyecto").modal();
	});

	$(".btn_proyecto__bon").click(() => {
		container_activo = ".txt_proyecto__bon";
		$(".txt_dato_proyecto__bon").val("");
		callbak_activo = (resp) => {
			mostrar_nombre_proyecto(resp);
		};
		buscar_proyecto__bon("", callbak_activo);
		$("#modal_buscar_proyecto__bon").modal();
	});

	$(".btn_revista").click(() => {
		sw_rev = false;
		container_activo = ".txt_revista";
		$(".txt_dato_revista").val("");
		callbak_activo = (resp) => {
			mostrar_nombre_revista(resp);
		};
		buscar_revista("", callbak_activo);
		$("#modal_buscar_revista").modal();
	});

	$(".btn_afilicacion").click(() => {
		container_activo = ".txt_afiliacion";
		$(".txt_dato_afiliacion").val("");
		callbak_activo = (resp) => {
			mostrar_nombre_afiliacion(resp);
		};
		buscar_afiliacion("F**w", callbak_activo);
		$("#modal_buscar_afiliacion").modal();
	});

	$("#form_buscar_proyecto").submit(() => {
		let dato = $("#txt_dato_proyecto").val();
		buscar_proyecto(dato, callbak_activo);
		return false;
	});

	$("#form_buscar_proyecto__bon").submit(() => {
		let dato = $("#txt_dato_proyecto__bon").val();
		buscar_proyecto__bon(dato, callbak_activo);
		return false;
	});

	$("#form_buscar_revista").submit(() => {
		let dato = $("#txt_dato_revista").val();
		buscar_revista(dato, callbak_activo);
		return false;
	});

	$("#form_buscar_revista_bon").submit(() => {
		let dato = $("#txt_dato_revista_bon").val();
		buscar_revista_bon(dato, callbak_activo);
		return false;
	});

	$("#form_buscar_afiliacion").submit(() => {
		let dato = $("#txt_dato_afiliacion").val();
		buscar_afiliacion(dato, callbak_activo);
		return false;
	});

	$("#btn_notificaciones").click(() => {
		ver_notificaciones(open = 1);
	});

	$(".btnModifica").click(() => {
		modificar_publicacion();
	});

	$("#agregarIdioma").click(() => {
		$("#modal_buscar_idioma").modal();
		agregar_idiomas("F**W");
	});

	$("#agregarIdiomaBon").click(() => {
		$("#modal_buscar_idioma_bon").modal();
		agregar_idiomas_Bon("F**W");
	});

	$("#form_buscar_idioma_Bon").submit(() => {
		let dato = $("#txt_idioma_buscar_bon").val();
		agregar_idiomas_Bon(dato);
		return false;
	});

	$("#form_buscar_idioma").submit(() => {
		let dato = $("#txt_idioma_buscar").val();
		agregar_idiomas(dato);
		return false;
	});

	$("#removerIdioma").click(() => {
		eliminar_idioma_agregado();
	});

	$("#btn_administrar_revistas").click(() => {
		sw_rev = true;
		buscar_revista("", callbak_activo);
		$("#modal_buscar_revista").modal();
	});

	$("#bonificaciones").on("click", async function () {
		await validar_cantidad_de_solicitud();
    	if (inicial.cantidad >= "1" && inicial.estado === 'Bon_Sol_Creado') {
			validar_cantidad_de_solicitud();
			guardarSolicitud();
			// editarSolicitud("¿Estas Seguro?", "Tener en cuenta que, al realizar esta acción usted podrá realizar la modificación de la ultima solicitud, si desea continuar debe  presionar la opción de 'Si, Entiendo' !", true);
		} else if (inicial.cantidad === "0") {
			validar_cantidad_de_solicitud();
			guardarSolicitud();
		} else if (inicial.cantidad === "1" && inicial.estado != "Bon_Sol_Creado") {
			validar_cantidad_de_solicitud();
			guardarSolicitud();
		}
	});

	$("#data__principal").click(function () {
		//listar_cuartiles();
		listar_cuart_scopus();
		listar_cuart_wos();
		data_tipos_escrituras = [];
		listar_tipos_escrituras();
		pintar_info_articulo();
		listar_idiomas_bonificaciones();
		$("#txt_valor_asignar_cat").val('');
		$("#modal_principal_bonificaciones").modal("show");
	});

	$("#data__autores").click(async function () {
		$("#autores").html("");
		id_afiliacion = "profesor";
		autores_profesores = await listar_autor_porTipo(id_solicitud, id_afiliacion);
		pintar_autores();
		$("#modal_informacion_autores").modal("show");
	});
	
	$("#data__otros_aspectos").click(function () {
		$(`#modal_otros_aspectos__bon`).off("click");
		listar_otros_aspectos();
		$("#modal_otros_aspectos__bon").modal("show");
	});

	$(".btn_busc_art_bon").click(function () {
		let valor = $("#txt_dato_articulo__bonificaciones").val();
		if (valor === "") {
			$("#txt_dato_articulo__bonificaciones").focus();
			return false;
		}
		buscar_articulo_bon(valor, callbak_activo);
		return false;
	});

	$(".btn_buscar_art_bon").click(function () {
		$("#txt_dato_articulo__bonificaciones").val("");
		buscar_articulo("undefined");
		$("#modal_buscar_articulo__bonificaciones").modal();
		callbak_activo = ({ titulo_articulo, id }) => {
			$("#nombre_articulo_bon").val(titulo_articulo).attr("data-art_id", id);
			$("#form_buscar_articulo__bonificaciones").get(0).reset();
			$("#modal_buscar_articulo__bonificaciones").modal("hide");
		};
	});

	const buscar_articulo_bon = (dato_buscar, callback) => {
		$("#tabla_articulo_busqueda_bonif tbody").off("click", "tr td .seleccionar");
		consulta_ajax(`${ruta}buscar_articulos`, { dato_buscar }, (data) => {
			const myTable = $("#tabla_articulo_busqueda_bonif").DataTable({
				destroy: true,
				processing: true,
				searching: false,
				data,
				columns: [
					{ data: "titulo_articulo" },
					{
						data: "id",
						render: function (data) {
							return `<span style="color: #39B23B;" data-art_id="${data}" title="Seleccionar Articulo" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>`;
						},
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});

			$("#tabla_articulo_busqueda_bonif tbody").on(
				"click",
				"tr td .seleccionar",
				function () {
					let data = myTable.row($(this).parent()).data();
					callback(data);
					buscar_autores(data.titulo_articulo, callbak_activo);
					$("#nombre_revista option").each(function () {
						$(this).next().remove();
					});
					$("#nombre_revista").append(
						`<option value="${data.idrevista}">${data.nombre_revista}</option>`
					);
					MensajeConClase(
						"Nombre del articulo seleccionado con exito!.",
						"success",
						data.titulo_articulo + " seleccionado!"
					);
				}
			);
		});
	};
	$(".btn_revista_bon").click(() => {
		sw_rev = false;
		container_activo = ".txt_revista_bon";
		$(".txt_dato_revista_bon").val("");
		callbak_activo = (resp) => {
			mostrar_nombre_revista(resp);
		};
		buscar_revista_bon("", callbak_activo);
		$("#modal_buscar_revista_bon").modal();
	});

	$("#form_solicitar_bonificacion").submit(() => {
		update_bonificaciones();
		return false;
	});

	$("#bon_cuartil_scopus").change(function () {
		let cuartil = $(this).val();
		if (cuartil == 219763) {
			$("#div_cuartil_nuevo").removeClass("oculto");
		} else if (cuartil != 219763) {
			$("#div_cuartil_nuevo").addClass("oculto");
		}
	});


	$("#index_scopus__bon").change(function () {
		let scopus = $(this).val();
		if (scopus == "selec_si_bonif") {
			$("#div_cuartil_scopus").removeClass("oculto");
			$("#div_url_indexacion_scopus").removeClass("oculto");
		} else if (scopus == "selec_no_bonif") {
			$("#div_cuartil_scopus").addClass("oculto");
			$("#div_url_indexacion_scopus").addClass("oculto");
		}
	});

	$("#index_wos__bon").change(function () {
		let scopus = $(this).val();
		if (scopus == "selec_si_bonif") {
			$("#div_cuartil_wos").removeClass("oculto");
			$("#div_url_indexacion_wos").removeClass("oculto");
		} else if (scopus == "selec_no_bonif") {
			$("#div_cuartil_wos").addClass("oculto");
			$("#div_url_indexacion_wos").addClass("oculto");
		}
	});
	$("#bon_LineaInv").change(function () {
		let id = $(this).val();
		listar_sublineas_bon(id);
	});

	$("#form_ingresar_porcentajes").submit((e) => {
		let fordata = new FormData(
			document.getElementById("form_ingresar_porcentajes")
		);
		fordata.append("usuario", id_porcentaje);
		let data = formDataToJson(fordata);
		if ((parseFloat(data.second_porcentage) + parseFloat(data.third_porcentage)) != data.first_porcentage){
			MensajeConClase(
				`La suma de los porcentajes insertados no cumplen con el 100% requerido, por favor valide e intente nuevamente.`,
				"warning",
				"Oops!"
			);
			return false;
		}else{
			porcentajes = [];
			porcentajes.push({ data });
			update_porcentaje();
		}
		$("#form_ingresar_porcentajes").get(0).reset();
		$("#modal_asignar_porcentajes").modal("hide");
		return false;
	});

	$("#agregar_articulos_cumplidos_aut").click(function () {
		$("#form_articulos_cumplidos").trigger("reset");
		$("#modal_articulos_cumplidos").modal("show");
	});

	$("#form_articulos_cumplidos").submit(() => {
		asignar_articulos_cumplidos();
		return false;
	});

	$("#menu_articulos_adm li").click(function () {
		$("#menu_articulos_adm li").removeClass("active");
		$(this).addClass("active");
		if ($(this)[0].classList.contains("btn_articulos_suscritos")) {
			$("div.adm_proceso").hide();
			$("div.articulos_suscritos").fadeIn();
		} else if ($(this)[0].classList.contains("btn_articulos_cumplidos")) {
			$("div.adm_proceso").hide();
			$("div.articulos_cumplidos").fadeIn();
		}
	});

	$("#nav_ver_bonificaciones li").click(function () {
		$("#nav_ver_bonificaciones li").removeClass("active");
		$(this).addClass("active");
		if ($(this)[0].classList.contains("autores_bonificaciones")) {
			$("div.btn_ver_informacion").hide();
			$("div.tabla_autores_bon").fadeIn();
		} else if ($(this)[0].classList.contains("evidencias_bonificaciones")) {
			$("div.btn_ver_informacion").hide();
			$("div.tabla_ver_evidencias").fadeIn();
			pintar_evidencias_ver();
		} else if ($(this)[0].classList.contains("otros_aspectos_bonificaciones")) {
			$("div.btn_ver_informacion").hide();
			$("div.tabla_ver_otr_asp").fadeIn();
			listar_respuestas_otros_aspectos();
		} else if ($(this)[0].classList.contains("info_bonificaciones")) {
			$("div.btn_ver_informacion").hide();
			$("div.tabla_info_bonificaciones").fadeIn();
			$("div.tabla_categ_ver_bon").fadeIn();
			pintar_informacion_principal();
			pintar_tipos_de_escrituras();
		}else if ($(this)[0].classList.contains("ver_porcentajes_bonificaciones")) {
			$("div.btn_ver_informacion").hide();
			pintar_porcentajes_totales();
			$("div.tabla_ver_porcentajes").fadeIn();
			//pintar_informacion_principal();
		}else if ($(this)[0].classList.contains("ver_historial_bonificaciones")) {
			$("div.btn_ver_informacion").hide();
			listar_estados(id = id_solicitud);
			$("div.tabla_ver_historial").fadeIn();
			//pintar_informacion_principal();
		}else if ($(this)[0].classList.contains("ver_liquidacion_bonificaciones")) {
			$("div.btn_ver_informacion").hide();
			obtener_autores_liquidacion();
			$("div.tabla_ver_liquidacion").fadeIn();
			//pintar_informacion_principal();
		}
	});

	$("#nav_ver_liquidacion li").click(async function () {
		$("#nav_ver_liquidacion li").removeClass("active");
		$("#liquidacion_personas").html("");
		$(".container_liquidacion_total").addClass("oculto");
		if ($(this)[0].classList.contains("info_liquidacion")) {
			$(this).addClass("active");
			$("div.btn_ver_informacion_liq").hide();
			$("div.tabla_info_liquidacion").fadeIn();
			$(".container_liquidacion_total").removeClass("oculto");
			consulta_ajax(`${ruta}obtener_liquidacion_total`, { id_solicitud }, (data) => {
				$("#liquidacion_personas").append(`<strong> Liquidación: </strong> El total de la liquidación es ${valor_peso(data)}`);
			});
		} else if ($(this)[0].classList.contains("autores_liquidacion")) {
			$(".container_liquidacion_total").removeClass("oculto");
			$(this).addClass("active");
			obtener_autores_liquidacion();
			$("div.btn_ver_informacion_liq").hide();
			$("div.tabla_autores_liquidacion").fadeIn();
		}else if ($(this)[0].classList.contains("gestores_liquidacion")) {
			$(".container_liquidacion_total").removeClass("oculto");
			$(this).addClass("active");
			$("div.btn_ver_informacion_liq").hide();
			$("div.tabla_gestores_liquidacion").fadeIn();
			obtener_personas_liquidacion('Bon_Sol_Gest_Aprob');
		}else if($(this)[0].classList.contains("director_liquidacion")){
			$(".container_liquidacion_total").removeClass("oculto");
			$(this).addClass("active");
			$("div.btn_ver_informacion_liq").hide();
			$("div.tabla_directores_liquidacion").fadeIn();
			obtener_director_liquidacion('Bon_Sol_Aprob_Direct_Pub', 'director');
		}
	});
	
	$("#nav_ver_autores li").click(function () {
		$("#nav_ver_autores li").removeClass("active");
		$(this).addClass("active");
		if ($(this)[0].classList.contains("info_autor")) {
			$("div.btn_ver_autor").hide();
			$("div.tabla_info_princ_autor").fadeIn();
		} else if ($(this)[0].classList.contains("info_adic_autors")) {
			$("div.btn_ver_autor").hide();
			$("div.tabla_info_adic_autor").fadeIn();
			pintar_evidencias_ver();
		} else if ($(this)[0].classList.contains("ver_afil_inst")) {
			$("div.btn_ver_autor").hide();
			pintar_afiliaciones_institucionales(id_autor_bon);
			$("div.tabla_afiliaciones_inst").fadeIn();
		} else if ($(this)[0].classList.contains("ver_art_cumpl")) {
			$("div.btn_ver_autor").hide();
			obtener_articulos_cumplidos();
			$("div.tabla_arti_cumpl").fadeIn();
		}else if ($(this)[0].classList.contains("ver_art_susc")) {
			$("div.btn_ver_autor").hide();
			obtener_articulos_suscritos();
			$("div.tabla_arti_susc").fadeIn();
		}
	});

	$("#menu_autores_adm li").click(async function () {
		$("#menu_autores_adm li").removeClass("active");
		let div = "";
		id_afiliacion == "profesor"
			? (div = ".autores_profesores")
			: id_afiliacion == "estudiantes"
			? (div = ".data_estudiantes")
			: (div = ".autores_externos");
		$(".autores").html("");
		tabla_buscar = "";
		$(this).addClass("active");
		autores_profesores = "";
		autores_estudiantes = "";
		autores_externos = "";
		$(".autores_div").addClass("oculto");

		if ($(this)[0].classList.contains("btn_autores_profesores")) {
			$("#data_profesores").html("");
			id_afiliacion = "profesor";
			autores_profesores = await listar_autor_porTipo(id_solicitud, id_afiliacion);
			$(".autores_profesores").removeClass("oculto");
			pintar_autores();
		} else if ($(this)[0].classList.contains("btn_autores_estudiantes")) {
			$("#data_estudiantes").html("");
			id_afiliacion = "estudiante";
			autores_estudiantes = await listar_autor_porTipo(id_solicitud, id_afiliacion);
			$(".autores_estudiantes").removeClass("oculto");
			pintar_autores();
		} else if ($(this)[0].classList.contains("btn_autores_externos")) {
			$("#data_externos").html("");
			id_afiliacion = "externo";
			autores_externos = await listar_autor_porTipo(id_solicitud, id_afiliacion);
			$(".autores_externos").removeClass("oculto");
			pintar_autores();
		}
	});

	$("#data__evidencias").click(function () {
		pintar_evidencias();
		$("#form_evidencias_bonificaciones").trigger("reset");
		$("#modal_evidencias_bonificaciones").modal("show");
	});

	$("#btn_guardar_info_liq").click(function () {
		const cuar_liq_final = $("#cuart_liq_fin").val();
		const cat_liq_final = $("#cat_liq_fin").val();
		if (cuar_liq_final && cat_liq_final) {
			guardar_info_liquid_final(cuar_liq_final, cat_liq_final);
			obtener_autores_liquidacion();
		}
	});

	$("#form_evidencias_bonificaciones").submit(() => {
		//e.preventDefault();
		guardar_evidencias();
		return false;
	});

	$("#form_guardar_comentario_porcentaje").submit(() => {
		guardar_comentario();
		return false;
	});

	$("#txt_nombre_institucion").keydown((event) => {
		if (event.which == 13 || event.keyCode == 13) {
			$("#btn_asignar").trigger("click");
			return false;
		}
		//listar_afiliacion_institucional();
		return true;
	});

	$('#btn_asignar_categoria').click(async () => {
		const bon_categoria = $("#bon_categoria").val();
		if (bon_categoria) {
			const title = await traer_registro_id(bon_categoria);
			if(data_tipos_escrituras.length > 0){
				let existe = data_tipos_escrituras.find((element) => element.tipo == title.valory);
				if(!existe){
					data_tipos_escrituras.push({categoria: bon_categoria, title: title.valor, tipo: title.valory });
					$("#bon_categoria").val("");
					listar_tipos_escrituras();
					MensajeConClase(`Información agregada exitosamente.`, 'success', 'Tipo de escritura');
				}else if(existe){
					$("#bon_categoria").val("");
					listar_tipos_escrituras();
					MensajeConClase(`Solo puede seleccionar un solo valor de la misma categoría.`, 'info', 'Ooops!!!');
				}
			}else{
				data_tipos_escrituras.push({categoria: bon_categoria, title: title.valor, tipo: title.valory });
				$("#bon_categoria").val("");
				listar_tipos_escrituras();
				MensajeConClase(`Información agregada exitosamente.`, 'success', 'Tipo de escritura');
			}
		} else { MensajeConClase('Por favor complete los campos.', 'info', 'Ooops!!!');}
		$("#bon_categoria").val("");

	});

	$('#btn_asignar').click(async () => {
		const nombre_inst = $('#txt_nombre_institucion').val();
		const pais = idpais;
		if (nombre_inst && pais) {
			if (!afil_inst.includes(nombre_inst)) {
				afil_inst.push({ nombre_inst, pais });
				$('#txt_nombre_institucion').val('');
				$('#nombre_pais').val('');
				idpais = '';
				guardar_afiliaciones_institucionales(nombre_inst, pais);
				listar_afiliacion_institucional();
				MensajeConClase(`${nombre_inst} agregada exitosamente.`, 'success', 'Institución');
			} else MensajeConClase('Esta Institución ya fue agregada.', 'info', 'Ooops!!!');
		} else MensajeConClase('Por favor ingrese el nombre de la institución que desea agregar.', 'info', 'Ooops!!!');
	});

	$("#aprobar_solicitud_bon").click(async () => {
		let result = await cambiar_estado(); 
		$("#respuesta_coaut_est").html("");
		$("#respuesta_coaut_ext").html("");
		$("#liquidacion_personas").html("");
		if(result.aprobado == 'Bon_Sol_Aprob_Direct_Pub'){
			pintar_tipos_de_escrituras("table_categorias_liq_bon");
			mostrarDato_Liquidacion(id_solicitud);
			$("#modal_informacion_liquidacion").modal("show");
			$(".container_liquidacion_total").removeClass("oculto");
			consulta_ajax(`${ruta}obtener_liquidacion_total`, { id_solicitud }, (data) => {
				$("#liquidacion_personas").append(`<strong> Liquidación: </strong> El total de la liquidación para los autores es ${valor_peso(data)}`);
			});
			listar_cuar_liq_final(id_solicitud);
			listar_cat_liq_final(id_solicitud);
		} 
		else{
			guardar_gestion_requerimiento('¿Aprobar solicitud?', 'input', '¿Realmente desea aprobar la solicitud? Tenga en cuenta que esta acción no se puede reversar', result.aprobado, false);
		} 
		return false;
	});

	$("#btn_aprobar_liquidacion").click(async() => {
		let result = await cambiar_estado(); 
		guardar_gestion_requerimiento('¿Aprobar solicitud?', 'input', '¿Realmente desea aprobar la solicitud? Tenga en cuenta que esta acción no se puede reversar', result.aprobado, false);
	});

	$("#modal_informacion_liquidacion").submit(async(e) => {
		e.preventDefault();
		let result = await cambiar_estado(); 
		let fordata = new FormData(document.getElementById("form_informacion_liquidacion"));
		let datos = formDataToJson(fordata);
		guardar_gestion_requerimiento('¿Aprobar solicitud?', 'input', '¿Realmente desea aprobar la solicitud? Tenga en cuenta que esta acción no se puede reversar', result.aprobado, false, datos);
	});
	  
	$("#Negar_solicitud_bon").click(async() => {
		let result = await cambiar_estado();
		guardar_gestion_requerimiento('¿Negar solicitud?', 'input', '¿Realmente desea negar la solicitud? Tenga en cuenta que esta acción no se puede reversar', result.negado , true);
		return false;
	});

	$("#corregir_solicitud_bon").click(async () => {
		let result = await cambiar_estado();
		guardar_gestion_requerimiento('¿Devolver solicitud?', 'input', '¿Realmente desea solicitar la corrección de la solicitud? Tenga en cuenta que esta acción no se puede reversar', result.devolver , true);
		return false;
	});

	$("#btn_aceptacion_porcentajes").click(() => {
		firmar_porcentajes('Firmar solicitud', '¿Realmente desea firmar esta solicitud?. Tenga en cuenta que esta acción no se puede reversar', 'warning');
		return false;
	});

	$(".btnMostrarJuicios").click(() => {
		$("#modal_listar_juicios").modal("show");
		//return false;
	});

	$("#ver_aux_pub").click(() => {
		listar_requerimientos_bon(id_solicitud, 'Gest_Aux_Public');
		return false;
	});

	$("#ver_dir_pub").click(() => {
		listar_requerimientos_bon(id_solicitud, "Direct_Public");
		return false;
	});

	$("#ver_Gest_ini").click(() => {
		listar_requerimientos_bon(id_solicitud, "Gest_Ini");
		return false;
	});

	$("#Agregar_otros_evidencias").click(() => {
		$("#modal_otros_Adjuntos").modal("show");
		return false;
	});

	$("#Agregar_instituciones_externas").click(() => {
		pintar_paises();
		$("#modal_agregar_institucion").modal();
		return false;
	});

	$("#cargar_adj_soli").on("click", function () {
		myDropzone.processQueue();
		return false;
	});

	$("#btn_administrar").click(() => {
		listar_comites();
		$("#modal_administrar_pub").modal();
	});

	$("#boton_guardar_comite").click(() => {
		$("#modal_guardar_comite").modal();
	});

	$("#menu_administrar_adm li").click(function () {
		$("#menu_administrar_adm li").removeClass("active");
		$(this).addClass("active");
		if ($(this)[0].classList.contains("btn_comite_ind")) {
			$("div.adm_proceso").hide();
			$("div.admin_comite").fadeIn();
			listar_comites();
		} else if ($(this)[0].classList.contains("btn_adm_permisos")) {
			$("div.adm_proceso").hide();
			$("div.admin_permisos").fadeIn();
			//listar_comites();
		}
	});

	$('#s_persona').click(() => {
		$('#modal_elegir_persona').modal();
		listar_personas();
		$('#txt_persona').val('');
	});

	$('#btn_buscar_persona').click(() => {
		let dato = $("#txt_persona").val();
		listar_personas(dato);
	});

	$('#generar_liquidacion').click(() => {
		liquidar_bonificacion();
		obtener_autores_liquidacion();
	});

	$("#generar_liquidacion_gestor").click(() => {
		liquidar_bonificacion_gestor('Bon_Sol_Gest_Aprob');
	});

	$("#generar_liquidacion_director").click(() => {
		liquidar_bonificacion_director("Bon_Sol_Aprob_Direct_Pub");
	});

	$('#total_liquidacion').click(() => {
		obtener_total_liquidacion();
		obtener_autores_liquidacion();
	});

	$('#boton_aprob_masivo').click(() => {
		gestionar_comites_masivo('Aprobar');
	});

	$("#boton_deneg_masivo").click(() => {
		gestionar_comites_masivo('Rechazar');
	});

	$("#cambiar_porcentaje_aut").click(() => {
		$("#modal_mod_porcentajes_dir").modal("show");
		validar_porcentajes();
	});
	
	$("#frm_buscar_persona").submit((e) => {
		e.preventDefault();
		const persona = $("#txt_persona").val();
		listar_personas(persona);
	});

	$("#form_guardar_comite").submit((e) => {
		e.preventDefault();
		guardar_comite_general('bonificaciones');
		listar_comites();
  	});

	$("#modal_mod_porcentajes_dir").submit((e) => {
		let fordata = new FormData(
			document.getElementById("form_mod_porcentajes_dir")
		);
		fordata.append("usuario", id_porcentaje_cp);
		let data = formDataToJson(fordata);
		if ((parseFloat(data.second_porcentage_cp) + parseFloat(data.third_porcentage_cp)) != data.first_porcentage_cp){
			MensajeConClase(
				`La suma de los porcentajes insertados no cumplen con el 100% requerido, por favor valide e intente nuevamente.`,
				"warning",
				"Oops!"
			);
			return false;
		}else{
			porcentajes_cp = [];
			porcentajes_cp.push({ data });
			modificar_porcentajes_dir();
		}
		$("#form_ingresar_porcentajes").get(0).reset();
		$("#modal_asignar_porcentajes").modal("hide");
		return false;
	});

	$("#form_agregar_institucion").submit((e) => {
		e.preventDefault();
		guardar_guardar_instituciones();
		$("#modal_agregar_institucion").modal("hide");
		return false;
		//guardar_comite_general('bonificaciones');
		//listar_comites();
  	});
	
	$("#form_modificar_comite").submit((e) => {
		e.preventDefault();
		modificar_comite_general(id_comite);
		listar_comites();
  	});

	  
	$("#form_otros_aspectos").submit((e) => {
		e.preventDefault();
		//$("#btn_others_asp").attr("disabled", true);
		cambiar_respuesta();
		let data_respuestas = [];
		data_preguntas.map(function (dato) {
			var objeto = {
				id_pregunta: dato.id,
				id_respuesta: dato.respuesta ? dato.respuesta : "",
				id_alcance: dato.alcance ? parseInt(dato.alcance) : 0,
				id_objetivo_alcance: dato.objetivo_alc ? parseInt(dato.objetivo_alc) : 0,
				id_objetivo: dato.objetivo ? Number(dato.objetivo) : 0,
				id_pacto : dato.pacto ? parseInt(dato.pacto) : 0,
				id_componente: dato.componente ? parseInt(dato.componente) : 0,
				comentario: $("#txt_comentario__answer__" + dato.id).val()
					? $("#txt_comentario__answer__" + dato.id).val()
					: "",
			};
			data_respuestas.push(objeto);
		});

		
		update_otros_aspectos(data_respuestas);
	});

	$("#btn_create_project").click(() => {
		$("#modal_create_new_project").modal("show");
	});

	$("#form_create_new_project").submit((e) => {
		e.preventDefault();
		save_new_project();
  	});

});

const obtener_estados = (buscar) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_estados`;
		consulta_ajax(url, { buscar }, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_sublineas_inv = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_sublineas_inv`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};

const listar_paises = () => {
	return new Promise((resolve) => {
		let url = `${ruta}listar_paises`;
		consulta_ajax(url, {}, (resp) => {
			resolve(resp);
		});
	});
};

const traer_registro_id = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}traer_registro_id`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_cuartiles = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_cuartiles`;
		consulta_ajax(url, {}, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_inst_ext = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_inst_ext`;
		consulta_ajax(url, {}, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_idioma = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_idioma`;
		consulta_ajax(url, {}, (resp) => {
			resolve(resp);
		});
	});
};

/* Pintar en select de formulario los estados */

const listar_estados_pub = () => {
	consulta_ajax(`${ruta}obtener_estados_pub`, "", (res) => {
		$("#pubs_status").html(
			`<option value="0" selected default>Seleccione estado de la publicación</option>`
		);
		for (let x = 0; x < res.length; x++) {
			if (
				res[x].idaux == "Pub_Red_E" ||
				res[x].idaux == "Pub_Red_Pos_E" ||
				res[x].idaux == "Pub_Pos_Ace_E" ||
				res[x].idaux == "Pub_Ace_Pub_E"
			) {
				$("#pubs_status").append(
					`<option value="${res[x].id}" data-aux="${res[x].idaux}">${res[x].estado}</option>`
				);
			}
		}
	});
};

const listar_cuartiles = async () => {
	let cuartiles = await obtener_cuartiles();
	pintar_datos_combo_nombre(cuartiles, ".cbxcuartiles", "Seleccione Cuartil");
};

const listar_cuar_liq_final = async (id) => {
	consulta_ajax(`${ruta}listar_cuar_liq_final`, {id_solicitud: id}, (cuar_final) => {
		pintar_datos_combo_nombre(cuar_final, ".cbxcuart_liq_fin", "Cuartil de Liquidación Final");
	});
};


const listar_cat_liq_final = async (id) => {
	consulta_ajax(`${ruta}listar_cat_liq_final`, {id_solicitud: id}, (cat_final) => {
		pintar_datos_combo_nombre(cat_final, ".cbxcat_liq_fin", "Categoria de Liquidación Final");
	});
};

const pintar_paises = async () => {
	let paises = await listar_paises();
	pintar_datos_combo_nombre(paises, "#select_pais_inst", "Seleccione un pais");
	$('.selectpicker').selectpicker('refresh');
};

const listar_idiomas_bonificaciones = async () => {
	let idioma = await obtener_idioma();
	pintar_datos_combo_nombre(idioma, "#idiomas_select_bon", "Seleccione un idioma");
	$('.selectpicker').selectpicker('refresh');
};

const listar_inst_externas = async () => {
	let instituciones = await obtener_inst_ext();
	pintar_datos_combo_nombre(instituciones, "#inst_ext_bon", "Seleccione la institución externa");
	$('.selectpicker').selectpicker('refresh');
};

const listar_ublineas_bon = async (id) => {
	let sublineas = await obtener_sublineas_inv(id);
	pintar_datos_combo(sublineas, ".cbxsublineainv", "Seleccione la sublinea de investigación");
};

const listar_estados_filt = async () => {
	let estados = await obtener_estados(285);
	pintar_datos_combo(estados, ".cbxestados", "Filtrar por estado");
};

const obtener_indicadores = (buscar) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_indicadores`;
		consulta_ajax(url, { buscar }, (resp) => {
			resolve(resp);
		});
	});
};

const listar_indicador = async () => {
	let indicadores = await obtener_indicadores(291);
	pintar_datos_combo(indicadores, ".cbx_nac_int_inst", "NAC/INT/INST");
};

const obtener_ranking = (buscar) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_ranking`;
		consulta_ajax(url, { buscar }, (resp) => {
			resolve(resp);
		});
	});
};

const listar_ranking = async () => {
	let ranking = await obtener_ranking(284);
	pintar_datos_combo(ranking, ".cbxranking", "Seleccione Ranking");
};

const pintar_datos_combo = (datos, combo, mensaje, sele = "") => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach((element) => {
		$(combo).append(
			`<option value='${element.id}'> ${element.valor} </option>`
		);
	});
	$(combo).val(sele);
};

const pintar_datos_combo_nombre = (datos, combo, mensaje, sele = "") => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach((element) => {
		$(combo).append(
			`<option value='${element.id}'> ${element.valor} </option>`
		);
	});
	$(combo).val(sele);
};

const listar_autores_iniciales = () => {
	$("#tabla_autores_iniciales tbody")
		.off("click", "tr")
		.off("click", "tr .eliminar");
	let i = 0;
	const myTable = $("#tabla_autores_iniciales").DataTable({
		destroy: true,
		searching: false,
		processing: true,
		data: autores,
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
				defaultContent: `<span style="color:red" class="fa fa-trash-o btn btn-default pointer eliminar"></span>`,
			},
		],
		language: idioma,
		dom: "Bfrtip",
		buttons: [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$("#tabla_autores_iniciales tbody").on("click", "tr", function () {
		$("#tabla_autores_iniciales tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});

	$("#tabla_autores_iniciales tbody").on("click", "tr .eliminar", function () {
		let data = myTable.row($(this).parent().parent()).data();
		autores.forEach((key, indice) => {
			if (key.id == data.id) {
				porcentaje += parseInt(autores[indice].puntos, 10);
				autores.splice(indice, 1);
			}
		});
		listar_autores_iniciales();
	});
};

const buscar_autor = (dato, callbak, tabla) => {
	if (tabla == "") {
		MensajeConClase(
			"Por favor seleccione el tipo de autor a buscar",
			"info",
			"Oops.!"
		);
	} else {
		consulta_ajax(`${ruta}buscar_autor`, { dato, tabla }, (resp) => {
			$("#tabla_autores_busqueda tbody")
				.off("click", "tr td .asignar")
				.off("dblclick", "tr")
				.off("click", "tr")
				.off("click", "tr td:nth-of-type(1)");
			let i = 0;
			const myTable = $("#tabla_autores_busqueda").DataTable({
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
						defaultContent:
							'<span style="color: #39B23B;" title="Seleccionar Autor" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" ></span>',
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});

			//EVENTOS DE LA TABLA ACTIVADOS
			$("#tabla_autores_busqueda tbody").on("click", "tr", function () {
				$("#tabla_autores_busqueda tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});

			$("#tabla_autores_busqueda tbody").on("dblclick", "tr", function () {
				let data = myTable.row($(this).parent().parent()).data();
				callbak(data);
			});

			$("#tabla_autores_busqueda tbody").on(
				"click",
				"tr td .asignar",
				function () {
					$("#btn_agregar_autor").off("click");
					let data = myTable.row($(this).parent().parent()).data();
					if (data.tabla != "general") {
						buscar_informacion_investigacion();
						$("#btn_agregar_autor").click(() => {
							agregar_autor_inicial(data);
						});
					} else {
						autores.push({
							id: data.id,
							tabla: data.tabla,
							nombre_completo: data.nombre_completo,
						});
						listar_autores_iniciales();
					}
				}
			);
		});
	}
};

const guardar_nuevo_autor = () => {
	let fordata = new FormData(document.getElementById("form_nuevo_autor"));
	let info = formDataToJson(fordata);
	info.id_referencia = afiliacion.id;
	consulta_ajax(`${ruta}guardar_nuevo_autor`, info, (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_nuevo_autor").get(0).reset();
			$("#modal_nuevo_autor").modal("hide");
			dato = info.nombre_completo;
			callbak_activo = (data) => {
				let autor = autores.find((element) => element.id == data.id);
				if (autor)
					MensajeConClase("El autor ya fue asignado.", "info", "Oops.!");
				else {
					autores.push(data);
					let table = $("#tabla_autores_busqueda").DataTable();
					table.clear().draw();
					MensajeConClase(
						"autor asignado con exito",
						"success",
						"Proceso Exitoso"
					);
				}
			};
			buscar_autor(dato, callbak_activo, "otro");
			$("#modal_buscar_autor").modal();
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};

const guardar_nuevo_autor_bon = async () => {
	let fordata = new FormData(document.getElementById("form_nuevo_autor_bon"));
	fordata.append("id_afiliacion", id_afiliacion);
	let data = [];
	let tabla = id_afiliacion == "estudiante" ? 'estudiante' : id_afiliacion == "externo" ? "externo" : "";
	let info = formDataToJson(fordata);
	let { txtdocumento, nombre, segundonombre, apellido, segundoapellido } = info;
	let nombre_completo = nombre + " " + segundonombre + " " + apellido + " " + segundoapellido;
	consulta_ajax(`${ruta}guardar_nuevo_autor_bon`, info, (resp)=> {
		let { titulo, mensaje, tipo, documento } = resp;
		if (tipo == "success") {
			$("#form_nuevo_autor_bon").get(0).reset();
			$("#modal_nuevo_autor_bonificaciones").modal("hide");
			if(id_afiliacion == 'estudiante'){
				$("#modal_info_estudiante_bon").modal();
				$("#selec_program_est").removeClass("oculto");
				$("#inst_externa").addClass("oculto");
				$("#btn_add_est_bon").click(async () => {
					let resp = await verificar_identificacion(txtdocumento);
					data.push({
						"txtdocumento": txtdocumento,
						"nombre_completo": nombre_completo,
						"tabla": tabla,
						"id": resp[0].id,
					});
					add_author_stud_bon(data[0]);
				});
			}else if(id_afiliacion == 'externo'){
				listar_inst_externas();
				$("#modal_info_estudiante_bon").modal();
				$('#inst_ext_bon').selectpicker();
				$("#inst_externa").removeClass("oculto");
				$("#selec_program_est").addClass("oculto");
				$("#btn_add_est_bon").off("click");
				$("#btn_add_est_bon").click(async () => {
					let resp = await verificar_identificacion(documento);
					data.push({
						tabla: tabla,
						documento: txtdocumento,
						afiliacion: "externo",
						nombre_completo: nombre_completo,
						institucion_ext: $("#inst_ext_bon").val(),
						id: resp[0].id,
						id_solicitud: id_solicitud,
					});
					guardar_autores_bonificaciones(data[0]);
					// add_author_exter_bon(data[0]);
					$("#modal_info_estudiante_bon").modal("hide");
					$("#form_tipo_afiliaciones").trigger("reset");
				});
			}
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			$("#form_nuevo_autor_bon").get(0).reset();
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};
const almacenar_revista = (callbak) => {
	let fordata = new FormData(document.getElementById("form_almacenar_revista"));
	let info = formDataToJson(fordata);
	info.revista = revista.id ? revista.id : null;
	consulta_ajax(`${ruta}almacenar_revista`, info, (resp) => {
		let { titulo, mensaje, tipo, revista_info } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_almacenar_revista").get(0).reset();
			$("#modal_almacenar_revista").modal("hide");
			if (!sw_rev) {
				$("#modal_buscar_revista").modal("hide");
				callbak(revista_info);
			} else {
				let dato = $("#txt_dato_revista").val();
				buscar_revista(dato, callbak);
			}
		}
	});
};

const almacenar_revista_bonificaciones = (callbak) => {
	let fordata = new FormData(
		document.getElementById("form_almacenar_revista_bonificaciones")
	);
	let info = formDataToJson(fordata);
	let information = {
		nombre_revista: info.nombre_revista,
		issn: info.issn_rev__bon,
		cuartil: info.cuartil_rev__bon,
	};
	info.revista = revista.id ? revista.id : null;
	consulta_ajax(`${ruta}almacenar_revista`, information, (resp) => {
		let { titulo, mensaje, tipo, revista_info } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_almacenar_revista_bonificaciones").get(0).reset();
			$("#modal_almacenar_revista__bonificaciones").modal("hide");
			if (!sw_rev) {
				$("#modal_buscar_revista_bon").modal("hide");
				callbak(revista_info);
			} else {
				let dato = $("#txt_dato_revista_bon").val();
				buscar_revista(dato, callbak);
			}
		}
	});
};

const update_bonificaciones = () => {
	let data = new FormData(document.getElementById("form_solicitar_bonificacion"));
	data.append("art_id", $("#nombre_articulo_bon").attr("data-art_id"));
	data.append("id_revista", revista.id);
	data.append("id_solicitud", id_solicitud);
	data.append("id_proyecto_bon", id_proyecto_bon);
	data.append("data_tipos_escrituras", JSON.stringify(data_tipos_escrituras));
	//data.append("idioma", JSON.stringify(idiomas));
	enviar_formulario(`${ruta}update_bonificaciones`, data, (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_solicitar_bonificacion").trigger("reset");
			$("#modal_principal_bonificaciones").modal("hide");
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const guardar_publicacion = () => {
	let fordata = new FormData(document.getElementById("form_agregar_solicitud"));
	fordata.append("autores", JSON.stringify(autores));
	fordata.append("idiomas", JSON.stringify(idiomas));
	fordata.append("id_proyecto", proyecto.id);
	fordata.append("id_revista", revista.id);
	fordata.append("idaux", status_selected.idaux);
	fordata.append("campo_fecha", campo_fechaa);
	fordata.append("tipos_adjs", JSON.stringify(tipos_adjs));
	enviar_formulario(`${ruta}guardar_publicacion`, fordata, (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_agregar_solicitud").trigger("reset");
			$("#modal_agregar_solicitud").modal("hide");
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};

const listar_publicaciones = async (id = "") => {
	let id_estado = $("#modal_crear_filtros select[name='id_estado']").val();
	let id_ranking = $("#modal_crear_filtros select[name='id_ranking']").val();
	let fecha_inicial = $(
		"#modal_crear_filtros input[name='fecha_inicial']"
	).val();
	let fecha_final = $("#modal_crear_filtros input[name='fecha_final']").val();
	let publicaciones = await obtener_publicaciones_pendientes();
	$("#noti_n").html(publicaciones.length);
	$("#tabla_publicaciones tbody")
		.off("click", "tr")
		.off("dbclick", "tr")
		.off("click", "tr td:nth-of-type(1)")
		.off("click", "tr td .postular")
		.off("click", "tr td .aceptar")
		.off("click", "tr td .rechazar")
		.off("click", "tr td .redactar")
		.off("click", "tr td .visto_bueno")
		.off("click", "tr td .visto_buenoP")
		.off("click", "tr td .negar")
		.off("click", "tr td .VoBo_Gest")
		.off("click", "tr td .VoMo_Gest")
		.off("click", "tr td .VoBo_Autor")
		.off("click", "tr td .VoMo_Autor")
		.off("click", "tr td .Env_Cons_Acad")
		.off("click", "tr td .editar_bon")
		.off("click", "tr td .revisar_bon")
		.off("click", "tr td .liquid_bono")
		.off("click", "tr td .bonificaciones");
	consulta_ajax(
		`${ruta}listar_publicaciones`,
		{ id, id_estado, id_ranking, fecha_inicial, fecha_final },
		(resp) => {
			//console.log(resp);
			const myTable = $("#tabla_publicaciones").DataTable({
				destroy: true,
				processing: true,
				data: resp,
				columns: [
					{
						data: "ver",
					},
					{
						data: "titulo_articulo"
					},
					{
						data: "tipo_solicitud",
					},
					{
						data: "persona_registra",
					},
					{
						data: "fecha_registra",
					},
					{
						data: "estado",
					},
					{
						data: "accion",
					},
				],
				language: idioma,
				dom: "Bfrtip",
				buttons: get_botones(),
			});

			//if (tipo_modulo == 'comite_bonificaciones') myTable.column(7).visible(false);

			//EVENTOS ACTIVADOS
			$("#modal_notificaciones .close_me").on("click", function () {
				$("#modal_notificaciones").modal("hide");
			});

			$("#tabla_publicaciones tbody").on("click", "tr", function () {
				$("#tabla_publicaciones tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
				let data = myTable.row(this).data();
				id_pub_global = data.id;
				id_estado_global = data.id_estado;
			});

			$("#tabla_publicaciones tbody").on("dblclick", "tr", function () {
				let data = myTable.row(this).data();
				id_pub_global = data.id;
				ver_detalle_publicación(data);
			});

			$("#tabla_publicaciones tbody").on("click", "tr td:nth-of-type(1)", function () {
					let data = myTable.row($(this).parent()).data();
					ver_detalle_publicación(data);
				}
			);

			$("#tabla_publicaciones tbody").on(
				"click",
				"tr td .postular",
				function () {
					let { id, fecha_postulacion } = myTable.row($(this).parent()).data();
					fecha_pos = fecha_postulacion;
					subir_archivos(id, "Pub_Pos_E");
				}
			);

			$("#tabla_publicaciones tbody").on(
				"click",
				"tr td .aceptar",
				function () {
					let { id, fecha_aceptacion, id_tipo_solicitud } = myTable
						.row($(this).parent())
						.data();
					fecha_ace = fecha_aceptacion;
					if (id_tipo_solicitud === "Pub_Pub") {
						subir_archivos(id, "Pub_Ace_E");
						$("#modal_gestionar .row").css({ display: "block" });
					} else if (id_tipo_solicitud === "Pub_Pag") {
						//subir_archivos(id, "Pub_Ace_E");
						listar_autores_distribucion(id);
						$("#modal_gestionar").modal();
						$("#modal_gestionar .row").css({ display: "none" });
						$("#modal_gestionar #tabla_distribucion").css({ display: "block" });
						$("#form_gestion").submit(function () {
							$("#modal_gestionar").modal("hide");
							return false;
						});
					}
				}
			);

			$("#tabla_publicaciones tbody").on(
				"click",
				"tr td .rechazar",
				function () {
					let {
						id,
						fecha_aceptacion,
						id_tipo_solicitud,
						id_estado,
					} = myTable.row($(this).parent()).data();
					fecha_rec = fecha_aceptacion;

					if (id_tipo_solicitud == "Pub_Pub") {
						$("#modal_gestionar .row").css({ display: "block" });
						subir_archivos(id, "Pub_Rec_E");
					} else if (id_tipo_solicitud == "Pub_Pag") {
						visto_bueno_pagop(id, id_estado);
					}
				}
			);

			$("#tabla_publicaciones tbody").on(
				"click",
				"tr td .publicar",
				function () {
					let { id, fecha_publicacion, url_articulo } = myTable
						.row($(this).parent())
						.data();
					fecha_pub = fecha_publicacion;
					url_pub = url_articulo;
					subir_archivos(id, "Pub_Pub_E");
				}
			);

			$("#tabla_publicaciones tbody").on(
				"click",
				"tr td .redactar",
				function () {
					let { id } = myTable.row($(this).parent()).data();
					$("#modal_autores_validacion").modal();
					listar_autores_distribucion(id);
				}
			);
			
			$("#tabla_publicaciones tbody").on(
				"click",
				"tr td .visto_bueno",
				function () {
					let { id } = myTable.row($(this).parent()).data();
					gestionar_publicacion(id);
				}
				);

			$("#tabla_publicaciones tbody tr td .visto_buenoP").on(
				"click",
				function () {
					let { id, id_estado } = myTable.row($(this).parent()).data();
					visto_bueno_pagop(id, id_estado);
				}
			);
			
			$("#tabla_publicaciones tbody").on("click", "tr td .negar", function () {
				let { id } = myTable.row($(this).parent()).data();
				cerrar_publicacion(id);
			});

			$(".pag_pep").click(function () {
				$(".tabla_papers").attr("hidden", false);
				$(".tabla_publicaciones").attr("hidden", true);
				$(".tabla_bonificaciones").attr("hidden", true);
				$("#tabla_autores").attr("hidden", true);
				$("#tabla_autores_bon").attr("hidden", true);
				$("#tabla_autores_pag").attr("hidden", false);
			});
			
			$(".pub").click(function () {
				$(".tabla_publicaciones").attr("hidden", false);
				$(".tabla_papers").attr("hidden", true);
				$(".tabla_bonificaciones").attr("hidden", true);
				$("#tabla_autores").attr("hidden", false);
				$("#tabla_autores_bon").attr("hidden", true);
				$("#tabla_autores_pag").attr("hidden", true);
			});
			
			$("#tabla_publicaciones tbody").on("click", "tr td .bonificaciones", function () {
				let { id } = myTable.row($(this).parent()).data();
				id_solicitud = id;
				pintar_informacion_principal();
				pintar_tipos_de_escrituras();
				$("#modal_detalle_bonificacion").modal();
			});
			
			$("#tabla_publicaciones tbody").on("click", "tr td .revisar_bon", function () {
				let { id, id_estado } = myTable.row($(this).parent()).data();
				id_solicitud = id;
				$("#modal_gestionar_solicitud_bonificaciones").modal();
				tipo_gestion = id_estado == "Bon_Sol_Rev_Aprob" ? "Gest_Aux_Public" : id_estado == 'Bon_Sol_Env' ? "Gest_Ini" :"Direct_Public";
				obtener_lista_requerimiento_bon(id, tipo_gestion);
			});

			$("#tabla_publicaciones tbody").on("click", "tr td .firmar_bon", async function () {
				let { id } = myTable.row($(this).parent()).data();
				id_solicitud = id;
				verificar = await verificar_firma_por_id();
				if(verificar == "1"){
					$(".btn_aceptacion_porcentajes").addClass('oculto');
				}
				$("#modal_firmar_bonificaciones").modal();
				obtener_porcentajes_firma();
			});

			$("#tabla_publicaciones tbody").on("click", "tr td .VoBo_Gest", function () {
				let { id } = myTable.row($(this).parent()).data();
				id_solicitud = id;
				//$("#modal_listar_juicios").modal("show");
				admin_gest('Dar Visto Bueno', '¿Realmente desea dar Visto Bueno a la gestión?', 'warning', 'VoBo', 'visto_bueno_ges');
			});

			$("#tabla_publicaciones tbody").on("click", "tr td .VoMo_Gest", function () {
				let { id } = myTable.row($(this).parent()).data();
				id_solicitud = id;
				//$("#modal_listar_juicios").modal("show");
				admin_gest('Dar Visto Malo', '¿Realmente desea dar Visto Malo a la gestión?', 'warning', 'VoMo', 'visto_bueno_ges');
			});

			$("#tabla_publicaciones tbody").on("click", "tr td .VoBo_Autor", function () {
				let { id } = myTable.row($(this).parent()).data();
				id_solicitud = id;
				//$("#modal_listar_juicios").modal("show");
				admin_gest('Dar Visto Bueno', '¿Realmente desea dar Visto Bueno a la gestión?', 'warning', 'VoBo', 'visto_bueno_aut');
			});

			$("#tabla_publicaciones tbody").on("click", "tr td .VoMo_Autor", function () {
				let { id } = myTable.row($(this).parent()).data();
				id_solicitud = id;
				//$("#modal_listar_juicios").modal("show");
				admin_gest('Dar Visto Malo', '¿Realmente desea dar Visto Malo a la gestión?', 'warning', 'VoMo', 'visto_bueno_aut');
			});

			$("#tabla_publicaciones tbody").on("click", "tr td .Env_Cons_Acad", function () {
				let { id } = myTable.row($(this).parent()).data();
				id_solicitud = id;
				guardar_gestion_requerimiento('¿Enviar a Consejo Academico?', 'warning', '¿Realmente desea enviar a consejo academico? Tenga en cuenta que esta acción no se puede reversar', 'Bon_Sol_Cons_Acad', false);
			});
			
			$("#tabla_publicaciones tbody").on("click", "tr td .editar_bon", function () {
				let { id } = myTable.row($(this).parent()).data();
				obtenerUltimaSolicitud(id);
			});
		}
	);

	
};

/* Visto bueno para pago paper */
const visto_bueno_pagop = (id, current_status) => {
	/* Taer los datos para enviar por correo */
	let data = [];
	consulta_ajax(`${ruta}listar_archivos`, { id_p: id }, (resp) => {
		data = resp;
		console.log(data[1].length);
	});
	let titulo = "";
	current_status == "Pub_Env_E"
		? (titulo = "Escriba el motivo por el cual rechaza su porcentaje.")
		: (titulo = "Dar visto bueno!");
	swal(
		{
			title: titulo,
			text: "",
			type: "input",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Aceptar!",
			cancelButtonText: "Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder: `Ingrese una obervación.`,
			inputType: "text",
		},
		function (mensaje) {
			if (mensaje === false) return false;
			if (mensaje === "")
				swal.showInputError(`Es obligatorio ingresar una observación.`);
			else {
				consulta_ajax(
					`${ruta}cerrar_publicacion`,
					{ vb: current_status, id, mensaje },
					(resp) => {
						let { mensaje, tipo, titulo } = resp;
						if (tipo == "success") {
							MensajeConClase(mensaje, tipo, titulo);
							listar_publicaciones();
							ver_notificaciones();
							for (let x = 0; x < data[1].length; x++) {
								enviar_correo({
									id: data[1][x].idp,
									estado: data[1][x].estado,
									id_estado: data[1][x].idestado,
									validacion: 0,
									solicitante: data[1][x].usuario_registra,
									correo: data[1][x].mail,
								});
							}
						} else {
							MensajeConClase(mensaje, tipo, titulo);
						}
					}
				);
			}
		}
	);
};
/* Fin de visto bueno pago p */

const ver_detalle_publicación = async (data) => {
	let {
		id,
		titulo_articulo,
		cuartil,
		issn,
		isbn,
		fecha_registra,
		ranking,
		indicador,
		persona_registra,
		proyecto,
		revista,
		idiomas,
		url_articulo,
		fecha_aceptacion,
		fecha_postulacion,
		fecha_publicacion,
		valor_pago,
		nom_cuartil,
		nom_cod,
		link_pago,
		num_art_ide,
		ttlo_art,
		rev_nom,
		tpago_valor,
		banck_name,
		money_type,
		tcbancaria,
		tipo_pub,
		id_tipo_solicitud,
		id_articulo,
	} = data;

	$("#url_container").hide();
	$("#f_postulacion").hide();
	$("#f_aceptacion_rechazo").hide();
	$("#f_publicacion").hide();

	$(".titulo_art").html(titulo_articulo);
	$(".issn").html(issn);
	$(".isbn").html(isbn);
	$(".cuartil").html(cuartil);
	$(".ranking").html(ranking);
	$(".indicador").html(indicador);
	$(".fecha_registro").html(fecha_registra);
	$(".persona_registra").html(persona_registra);
	$(".proyecto").html(proyecto);
	$(".revista").html(revista);
	$(".idiomas").html(idiomas);
	$(".url").html(url_articulo);
	$(".fecha_postulacion").html(fecha_postulacion);
	$(".fecha_aceptacion").html(fecha_aceptacion);
	$(".fecha_publicacion").html(fecha_publicacion);
	$(".rev_o_conf").html(tipo_pub);
	$(".pago_valor").html(valor_pago);
	$(".cuartill").html(nom_cuartil);
	$(".cod_sap").html(nom_cod);
	link_pago === "" || link_pago === ""
		? $(".pago_link").html("N/A")
		: $(".pago_link").html(link_pago);
	$(".num_art_id").html(num_art_ide);
	$(".titulo_articulo").html(ttlo_art);
	$(".rev_name").html(rev_nom);
	$(".tpago_valor").html(tpago_valor);
	banck_name === "" || banck_name === null
		? $(".banck_name").html("N/A")
		: $(".banck_name").html(banck_name);
	$(".money_type").html(money_type);
	tcbancaria === null || tcbancaria === ""
		? $(".tcbancaria").html("N/A")
		: $(".tcbancaria").html(tcbancaria);

	if (url_articulo) $("#url_container").show();
	if (fecha_postulacion) $("#f_postulacion").show();
	if (fecha_aceptacion) $("#f_aceptacion_rechazo").show();
	if (fecha_publicacion) $("#f_publicacion").show();
	
	listar_autores_publicacion(id);
	listar_autores_pagos(id);
	listar_autores_bonificacion(id);
	
	if(id_tipo_solicitud != "Pub_Bon") $("#modal_detalle_publicacion").modal();

	$(".btnArchivos").click(() => {
		listar_archivos(id);
		$("#modal_archivos").modal();
	});

	$(".btnArchivosP").click(() => {
		listar_archivos_pagop(id);
		$("#modal_archivos_pagop").modal();
	});

	$(".btnEstados").click(() => {
		listar_estados(id);
		$("#modal_estados").modal();
	});

	if(id_tipo_solicitud == "Pub_Bon"){
		id_solicitud = id;
		let data_bonificaciones = await obtener_data__bonificaciones(id);
		let {
			cuartil_scopus,
			cuartil_wos,
			id_cuartil_liq_bon,
			doi,
			fecha_publicacion,
			fecha_registra,
			id_estado,
			id_revista,
			editorial,
			id_tipo_solicitud,
			id_titulo_articulo,
			isbn,
			issn,
			nombre_completo,
			url_articulo,
			nombre_proyecto,
			fecha_inicial,
			fecha_final,
		} = data_bonificaciones[0];

		id_titulo_articulo ? $(".titulo_art_bon").html(id_titulo_articulo) : $(".titulo_art_bon").html("");
		fecha_registra ? $(".fecha_registro_bon").html(fecha_registra): $(".fecha_registro_bon").html("");
		nombre_completo ? $(".persona_registra_bon").html(nombre_completo) : $(".persona_registra_bon").html("");
		doi ? $(".doi_articulo__bon").html(doi) : $(".doi_articulo__bon").html("");
		id_revista ? $(".name_revista__bon").html(id_revista) : $(".name_revista__bon").html("");
		issn ? $(".issn__bon").html(issn) : $(".issn__bon").html("");
		cuartil_scopus
			? $(".cuartil_scopus__bon").html(cuartil_scopus)
			: $(".cuartil_scopus__bon").html("");
		cuartil_wos
			? $(".cuartil_wos__bon").html(cuartil_wos)
			: $(".cuartil_wos__bon").html("");
		id_cuartil_liq_bon
			? $(".cuartil_liq_bon__bon").html(id_cuartil_liq_bon)
			: $(".cuartil_liq_bon__bon").html("");
		fecha_publicacion ? $(".date_public__bon").html(fecha_publicacion) : $(".date_public__bon").html("");
		url_articulo ? $(".url_art__bon").html(url_articulo) : $(".url_art__bon").html("");
		editorial ? $(".editorial__bon").html(editorial) : $(".editorial__bon").html("");
		nombre_proyecto ? $(".name_proyecto__bon").html(nombre_proyecto) : $(".name_proyecto__bon").html("");
		fecha_inicial ? $(".i_proyecto__bon").html(fecha_inicial) : $(".i_proyecto__bon").html("");
		fecha_final ? $(".f_proyecto__bon").html(fecha_final) : $(".f_proyecto__bon").html("");
	}
};

const listar_autores_publicacion = (id) => {
	$("#tabla_autores_publicacion tbody")
		.off("click", "tr")
		.off("dbclick", "tr")
		.off("click", "tr td .ver");
	consulta_ajax(`${ruta}listar_autores_publicacion`, { id }, (resp) => {
		let i = 0;
		const myTable = $("#tabla_autores_publicacion").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "ver",
				},
				{
					data: "persona",
				},
				{
					data: "tipo",
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones(),
		});

		$("#tabla_autores_publicacion tbody").on(
			"click",
			"tr td .ver",
			function () {
				let { id } = myTable.row($(this).parent()).data();
				informacion_autor(id);
			}
		);
	});
};

/**Listar Autores pagos papers*/

const listar_autores_pagos = (id) => {
	$("#tabla_autores_pagos tbody")
		.off("click", "tr")
		.off("dbclick", "tr")
		.off("click", "tr td .ver");
	consulta_ajax(`${ruta}listar_autores_pagos`, { id }, (resp) => {
		const myTable = $("#tabla_autores_pagos").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "full_name",
				},
				{
					data: "puntos",
					render: function (data) {
						return `${data}%`;
					},
				},
				{
					data: "puntos",
					render: function (puntos) {
						return `${Math.round(
							(puntos *
								$(".pago_valor")
									.text()
									.replace(/[A-Z\W]/g, "")) /
								100
						)}${$(".pago_valor")
							.text()
							.replace(/[\d\W]/g, " ")}`;
					},
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones(),
		});

		$("#tabla_autores_publicacion tbody").on(
			"click",
			"tr td .ver",
			function () {
				let { id } = myTable.row($(this).parent()).data();
				informacion_autor(id);
			}
		);
	});
};

const listar_autores_bonificacion = (id) => {
	$("#tabla_autores_bonificaciones tbody")
		.off("click", "tr")
		.off("dbclick", "tr")
		.off("click", "tr td .ver");
		consulta_ajax(`${ruta}listar_autores_bonificacion`, { id }, (resp) => {
		let i = 0;
		const myTable = $("#tabla_autores_bonificaciones").DataTable({
			destroy: true,
			processing: true,
			data: resp,
			columns: [
				{
					data: "ver",
				},
				{
					data: "nombre_completo",
				},
				{
					data: "tabla",
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: get_botones(),
		});

		$("#tabla_autores_bonificaciones tbody").on(
			"click",
			"tr td .ver",
			function () {
				let data = myTable.row($(this).parent()).data();
				id_afiliacion = data.afil;
				pintar_afiliaciones_institucionales(data.id_autor);
				ver_detalle_autor_bonificaciones(id = data.id_autor);
				id_autor_bon = data.id_autor;
				obtener_articulos_cumplidos();
				obtener_articulos_suscritos();
			}
		);
	});
};

const mostrar_nombre_proyecto = (data) => {
	let { id, nombre_proyecto } = data;
	proyecto = { id, nombre_proyecto };
	$(container_activo).val(nombre_proyecto);
};

const mostrar_nombre_revista = (data) => {
	let { id, valor, valorx, valory } = data;
	revista = { id, valor, valorx, valory };
	$(container_activo).val(valor);
};

const mostrar_nombre_afiliacion = (data) => {
	let { id, valor } = data;
	afiliacion = { id, valor };
	$(container_activo).val(valor);
};

const buscar_proyecto = (dato, callbak) => {
	consulta_ajax(`${ruta}buscar_proyecto`, { dato }, (resp) => {
		$("#tabla_proyecto_busqueda tbody")
			.off("click", "tr")
			.off("dblclick", "tr")
			.off("click", "tr td .seleccionar");
		let i = 0;
		const myTable = $("#tabla_proyecto_busqueda").DataTable({
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
					data: "nombre_proyecto",
				},
				{
					data: "nombre_investigador",
				},
				{
					defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Proyecto" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>',
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$("#tabla_proyecto_busqueda tbody").on("click", "tr", function () {
			$(`#tabla_proyecto_busqueda tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_proyecto_busqueda tbody").on("dblclick", "tr", function () {
			let data = myTable.row($(this).parent().parent()).data();
			$("#modal_buscar_proyecto").modal("hide");
			callbak(data);
		});

		$("#tabla_proyecto_busqueda tbody").on(
			"click",
			"tr td .seleccionar",
			function () {
				let data = myTable.row($(this).parent().parent()).data();
				callbak(data);
				$("#form_buscar_proyecto").get(0).reset();
				$("#modal_buscar_proyecto").modal("hide");
			}
		);
	});
};

const buscar_revista = (dato, callbak) => {
	consulta_ajax(`${ruta}buscar_revista`, { dato }, (resp) => {
		$("#tabla_revista_busqueda tbody")
			.off("click", "tr")
			.off("dblclick", "tr")
			.off("click", "tr td .seleccionar")
			.off("click", "tr td .editar");
		let i = 0;
		const myTable = $("#tabla_revista_busqueda").DataTable({
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
					data: "valor",
				},
				{
					defaultContent: sw_rev
						? '<span style="color: #2E79E5;" title="Editar Revista" data-toggle="popover" data-trigger="hover" class="fa fa-edit btn btn-default editar" ></span>'
						: '<span style="color: #39B23B;" title="Seleccionar Revista" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>',
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$("#tabla_revista_busqueda tbody").on("click", "tr", function () {
			$(`#tabla_revista_busqueda tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_revista_busqueda tbody").on("dblclick", "tr", function () {
			let data = myTable.row($(this).parent().parent()).data();
			$("#modal_buscar_proyecto").modal("hide");
			callbak(data);
		});

		$("#tabla_revista_busqueda tbody").on(
			"click",
			"tr td .seleccionar",
			function () {
				let data = myTable.row($(this).parent().parent()).data();
				callbak(data);
				$("#form_buscar_revista").get(0).reset();
				$("#modal_buscar_revista").modal("hide");
			}
		);

		$("#tabla_revista_busqueda tbody").on(
			"click",
			"tr td .editar",
			async function () {
				let data = myTable.row($(this).parent().parent()).data();
				await listar_cuartiles();
				revista = data;
				$("#text_nombre_revista").val(data.valor);
				$("#txt_issn").val(data.valorx);
				$("#txt_isbn").val(data.valorz);
				$("#cuartil").val(data.valory);
				$("#modal_almacenar_revista").modal();
			}
		);
	});
};

const buscar_afiliacion = (dato, callbak) => {
	consulta_ajax(`${ruta}buscar_afiliacion`, { dato }, (resp) => {
		$("#tabla_afiliacion_busqueda tbody")
			.off("click", "tr")
			.off("dblclick", "tr")
			.off("click", "tr td .seleccionar");
		let i = 0;
		const myTable = $("#tabla_afiliacion_busqueda").DataTable({
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
					data: "valor",
				},
				{
					defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Institución" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>',
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$("#tabla_afiliacion_busqueda tbody").on("click", "tr", function () {
			$(`#tabla_afiliacion_busqueda tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_afiliacion_busqueda tbody").on("dblclick", "tr", function () {
			let data = myTable.row($(this).parent().parent()).data();
			$("#modal_buscar_afiliacion").modal("hide");
			callbak(data);
		});

		$("#tabla_afiliacion_busqueda tbody").on(
			"click",
			"tr td .seleccionar",
			function () {
				let data = myTable.row($(this).parent().parent()).data();
				callbak(data);
				$("#form_buscar_afiliacion").get(0).reset();
				$("#modal_buscar_afiliacion").modal("hide");
			}
		);
	});
};

const gestionar_publicacion = (id) => {
	$("#modal_archivos_gestion").modal();
	consulta_ajax(`${ruta}listar_archivos`, { id }, (resp) => {
		$("#tabla_archivos_gestion tbody").off("click", "tr").off("dblclick", "tr");
		let i = 0;
		const myTable = $("#tabla_archivos_gestion").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						let { nombre_guardado, estado } = full;
						if (nombre_guardado == null) return "N/A";
						else if (estado == 1)
							return `<a target='_blank' href='${Traer_Server()}${ruta_archivos}${nombre_guardado}' style="background-color: #5cb85c;color: white;width: 100%;" class="pointer form-control"><span>Ver</span></a>`;
						else
							return `<a target='_blank' href='${Traer_Server()}${ruta_archivos}${nombre_guardado}' style="background-color: #d9534f;color: white;width: 100%;" class="pointer form-control"><span>Ver</span></a>`;
					},
				},
				{
					data: "tipo_archivo",
				},
				{
					data: "nombre_guardado",
				},
				{
					data: "fecha_registro",
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		$("#visto_bueno").click(function () {
			swal(
				{
					title: "¿ Desea dejar una observacion ?",
					text: "",
					type: "input",
					showCancelButton: true,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Aceptar!",
					cancelButtonText: "Cancelar!",
					allowOutsideClick: true,
					closeOnConfirm: false,
					closeOnCancel: true,
					inputPlaceholder: `Ingrese la observación`,
					inputType: "text",
				},
				function (mensaje) {
					if (mensaje === false) return false;
					consulta_ajax(
						`${ruta}gestionar_publicacion`,
						{ id, mensaje },
						(resp) => {
							let { mensaje, tipo, titulo, data } = resp;
							if (tipo == "success") {
								swal.close();
								$("#modal_archivos_gestion").modal("hide");
								listar_publicaciones();
								ver_notificaciones();
								enviar_correo({
									id: data.id,
									estado: data.estado,
									id_estado: data.id_estado,
									validacion: 0,
									solicitante: data.persona_registra,
									correo: data.correo_registra,
								});
							} else {
								MensajeConClase(mensaje, tipo, titulo);
							}
						}
					);
				}
			);
		});

		$("#visto_malo").click(function () {
			swal(
				{
					title: "Corregir Publicación",
					text: "",
					type: "input",
					showCancelButton: true,
					confirmButtonColor: "#D9534F",
					confirmButtonText: "Aceptar!",
					cancelButtonText: "Cancelar!",
					allowOutsideClick: true,
					closeOnConfirm: false,
					closeOnCancel: true,
					inputPlaceholder: `Ingrese la observación`,
					inputType: "text",
				},
				function (mensaje) {
					if (mensaje === false) return false;
					if (mensaje === "")
						swal.showInputError(`Debe ingresar el error a corregir`);
					else {
						consulta_ajax(
							`${ruta}corregir_publicacion`,
							{ id, observacion: mensaje },
							(resp) => {
								let { mensaje, tipo, titulo, data } = resp;
								if (tipo == "success") {
									swal.close();
									$("#modal_archivos_gestion").modal("hide");
									listar_publicaciones();
									ver_notificaciones();
									enviar_correo({
										id: data.id,
										estado: data.estado,
										id_estado: data.id_estado,
										validacion: 1,
										solicitante: data.persona_registra,
										correo: data.correo_registra,
										observacion: data.observacion,
									});
								} else {
									MensajeConClase(mensaje, tipo, titulo);
								}
							}
						);
					}
				}
			);
		});
	});
	return false;
};

const subir_archivos = (id, estado_nuevo) => {
	// $("#prim").show();
	// $("#seco").show();
	$("#tabla_distribucion").hide();
	$("#fecha_campo").val("");
	if (estado_nuevo == "Pub_Pos_E" || estado_nuevo == "Pub_Red_Pos_E") {
		configurar_archivos(id, estado_nuevo);
		$("#need_date").html(
			"<span class='fa fa-calendar red'></span> Fecha de postulación"
		);
		$("#fecha_campo").attr("name", "fecha_postulacion");
		$("#fecha_campo").val(fecha_pos);
	} else if (estado_nuevo == "Pub_Ace_E" || estado_nuevo == "Pub_Pos_Ace_E") {
		configurar_archivos(id, estado_nuevo);
		$("#need_date").html(
			"<span class='fa fa-calendar red'></span> Fecha de aceptación"
		);
		$("#fecha_campo").attr("name", "fecha_aceptacion");
		$("#fecha_campo").val(fecha_ace);
		listar_autores_distribucion(id);
		$("#tabla_distribucion").show();
	} else if (estado_nuevo == "Pub_Rec_E") {
		configurar_archivos(id, estado_nuevo);
		$("#need_date").html(
			"<span class='fa fa-calendar red'></span> Fecha del rechazo"
		);
		$("#fecha_campo").attr("name", "fecha_aceptacion");
		$("#fecha_campo").val(fecha_rec);
		$("#tabla_distribucion").show();
	} else if ((estado_nuevo = "Pub_Pub_E")) {
		configurar_archivos(id, estado_nuevo);
		$("#need_date").html(
			"<span class='fa fa-calendar red'></span> Fecha de publicación"
		);
		$("#fecha_campo").attr("name", "fecha_publicacion");
		$("#fecha_campo").val(fecha_pub);
	}

	$("#modal_gestionar").modal();
	$("#form_gestion").off("submit");
	$("#form_gestion").submit(() => {
		let sum = 0;
		let dist = [];
		if (estado_nuevo == "Pub_Ace_E") {
			$(".puntos_dist").each(function () {
				sum += +$(this).val();
				dist.push({
					id_el: $(this).attr("id"),
					puntos: $(this).val(),
				});
			});
		}
		let fordata = new FormData(document.getElementById("form_gestion"));
		fordata.append("id_publicacion", id);
		fordata.append("nuevo_estado", estado_nuevo);
		fordata.append("total", sum);
		fordata.append("dist", JSON.stringify(dist));
		let link = `${ruta}subir_archivos`;
		enviar_formulario(link, fordata, (resp) => {
			let { mensaje, tipo, titulo, mod } = resp;
			//console.log(resp);
			if (tipo == "success") {
				if (mod) MensajeConClase(mensaje, tipo, titulo);
				$("#modal_gestionar").modal("hide");
				enviar_correo({ id: id, id_estado: estado_nuevo, validacion: 2 });
				listar_publicaciones();
			} else {
				MensajeConClase(mensaje, tipo, titulo);
			}
		});
		return false;
	});
};

const listar_autores_distribucion = (id) => {
	consulta_ajax(`${ruta}listar_autores_distribucion`, { id }, (resp) => {
		$("#tabla_autores_distribucion tbody")
			.off("click", "tr")
			.off("dblclick", "tr")
			.off("click", "tr td .aceptar");
		let i = 0;
		let id_publicacion = id;
		const myTable = $("#tabla_autores_distribucion").DataTable({
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
					data: "persona",
				},
				{
					data: "accion",
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		$("#tabla_autores_distribucion tbody").on(
			"click",
			"tr td .aceptar",
			function () {
				let { id } = myTable.row($(this).parent()).data();
				aceptar_distribucion(id, id_publicacion);
			}
		);
	});
};

const aceptar_distribucion = (id, id_publicacion) => {
	//Usar para aceptar o no los porcentajes
	swal(
		{
			title: "¿ Seguro ?",
			text:
				"Recuerde que realizando esta acción, usted acepta el porcentaje de distribución que se le fue asignado y por lo tanto sera valido como constancia, si esta de acuerdo con estos terminos presione la opción, 'Si, entiendo.!'",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Entiendo!",
			cancelButtonText: "No, Regresar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
		},
		function (isConfirm) {
			if (isConfirm) {
				consulta_ajax(`${ruta}aceptar_distribucion`, { id }, (resp) => {
					let { mensaje, tipo, titulo, ok } = resp;
					if (tipo == "success") {
						swal.close();
						MensajeConClase(mensaje, tipo, titulo);
						listar_publicaciones();
						if (ok) {
							$("#modal_gestionar").modal("hide");
							listar_publicaciones();
						} else {
							listar_autores_distribucion(id_publicacion);
						}
					} else {
						MensajeConClase(mensaje, tipo, titulo);
						listar_autores_distribucion(id_publicacion);
					}
				});
			}
		}
	);
};

const configurar_archivos = (id, estado_nuevo) => {
	consulta_ajax(
		`${ruta}obtener_campos_archivos`,
		{ id, estado_nuevo },
		(resp) => {
			console.log(estado_nuevo);
			$("#container-files").html("");
			resp.forEach((element) => {
				if (!element.value) element.value = "";
				$("#container-files").append(`
        <div class="agrupado" id='${element.name}_cont'>
          <div class="input-group">
            <label class="input-group-btn">
              <span class="btn btn-primary">
                <span class="fa fa-folder-open"></span>
                Buscar <input type="file" style="display: none;" name='${
									element.name
								}'>
              </span>
            </label>
            <input type="text" class="form-control" readonly placeholder='${
							element.placeholder
						}' value='${element.value}'>
          </div>
        </div>
        ${
					estado_nuevo === "Pub_Pub_E"
						? '<input type="text" class="form-control" placeholder="URL del Articulo" id="url_articulo" name="url_articulo">'
						: ""
				}
      `);
			});
			$("#url_articulo").val(url_pub);
		}
	);
};

const cerrar_publicacion = (id) => {
	swal(
		{
			title: "Cerrar Publicación",
			text: "",
			type: "input",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Aceptar!",
			cancelButtonText: "Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder: `Ingrese razon del cierre`,
			inputType: "text",
		},
		function (mensaje) {
			if (mensaje === false) return false;
			if (mensaje === "")
				swal.showInputError(
					`Debe ingresar una razon por la cual cerrar la publicación`
				);
			else {
				consulta_ajax(`${ruta}cerrar_publicacion`, { id, mensaje }, (resp) => {
					let { mensaje, tipo, titulo } = resp;
					if (tipo == "success") {
						swal.close();
						listar_publicaciones();
					} else {
						MensajeConClase(mensaje, tipo, titulo);
					}
				});
			}
		}
	);
};

const listar_archivos = (id) => {
	consulta_ajax(`${ruta}listar_archivos`, { id }, (resp) => {
		$("#tabla_archivos_gestion tbody").off("click", "tr").off("dblclick", "tr");
		let i = 0;
		const myTable = $("#tabla_archivos").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{
					render: function (data, type, full, meta) {
						let { nombre_guardado, estado } = full;
						if (nombre_guardado == null) return "N/A";
						else if (estado == 1)
							return `<a target='_blank' href='${Traer_Server()}${ruta_archivos}${nombre_guardado}' style="background-color: #5cb85c;color: white;width: 100%;" class="pointer form-control"><span>Ver</span></a>`;
						else
							return `<a target='_blank' href='${Traer_Server()}${ruta_archivos}${nombre_guardado}' style="background-color: #d9534f;color: white;width: 100%;" class="pointer form-control"><span>Ver</span></a>`;
					},
				},
				{
					data: "tipo_archivo",
				},
				{
					data: "nombre_guardado",
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

/* Buscar archivos adjuntos de pago papers */

const listar_archivos_pagop = (id) => {
	consulta_ajax(`${ruta}listar_archivos_pagop`, { id_p: id }, (resp) => {
		$('a[data-id="adj_ca"]').attr(
			"href",
			`${Traer_Server()}archivos_adjuntos/pago_papers/${resp[0].carta_acept}`
		);
		$('a[data-id="adj_rc"]').attr(
			"href",
			`${Traer_Server()}archivos_adjuntos/pago_papers/${resp[0].cuartil_rev}`
		);
		resp[0].adj_inter !== null
			? $('tr[data-trid="adj_pi"]').attr("hidden", false) +
			  $('a[data-id="adj_pi"]').attr(
					"href",
					`${Traer_Server()}archivos_adjuntos/pago_papers/${resp[0].adj_inter}`
			  )
			: $('tr[data-trid="adj_pi"]').attr("hidden", true);
		resp[0].adj_extran !== null
			? $('tr[data-trid="adj_me"]').attr("hidden", false) +
			  $('a[data-id="adj_me"]').attr(
					"href",
					`${Traer_Server()}archivos_adjuntos/pago_papers/${resp[0].adj_extran}`
			  )
			: $('tr[data-trid="adj_me"]').attr("hidden", true);
	});
};

/* Fin */

const listar_estados = (id) => {
	consulta_ajax(`${ruta}listar_estados`, { id }, (resp) => {
		$("#tabla_estados tbody").off("click", "tr").off("dblclick", "tr");
		let i = 0;
		const myTable = $("#tabla_estados").DataTable({
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
					data: "estado_nombre",
				},
				{
					data: "nombre_completo",
				},
				{
					data: "fecha_registro",
				},
				{
					data: "observacion",
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});
	});
};

const obtener_publicaciones_pendientes = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_publicaciones_pendientes`;
		consulta_ajax(url, {}, async (resp) => {
			resolve(resp);
		});
	});
};

const ver_notificaciones = async (open = 0) => {
	let publicaciones = await obtener_publicaciones_pendientes();
	$("#noti_n").html(publicaciones.length);
	let respuesta = ``;
	for (let index = 0; index < publicaciones.length; index++) {
		let {
			id_publicacion: id,
			titulo,
			docente,
			estado_actual,
			id_tipo_solicitud,
		} = publicaciones[index];
		let abrir = "";
		abrir = `listar_publicaciones(${id})`;
		respuesta = `${respuesta}
      <a href="#" class="list-group-item" style="text-align: left;">
        <span class="badge btn-danger close_me" id="n-${id}" onclick="${abrir}"> Abrir</span>
        <h4>${titulo}</h4>
        <p>Docente: ${docente}</p>
        <p>Estado actual: ${estado_actual}</p>
      </a>`;
	}
	$("#panel_notificaciones").html(`
    <ul class="list-group">
      <li class="list-group-item active">
        <span class="badge">${publicaciones.length}</span>
        Pendientes por revisar
      </li>
      ${respuesta}
    </ul>
  `);
	if(publicaciones.length > 0 || open == 1) $("#modal_notificaciones").modal();
	$("#modal_notificaciones .close_me").on("click", function () {
		$("#modal_notificaciones").modal("hide");
	});
};

const modificar_publicacion = async () => {
	if (id_pub_global) {
		if (id_estado_global == "Pub_Red_E") {
			await listar_indicador();
			await listar_ranking();
			consulta_ajax(
				`${ruta}informacion_publicacion`,
				{ id_pub_global },
				(resp) => {
					let {
						nombre_proyecto,
						titulo_articulo,
						id_ranking,
						id_comite_proyecto,
						revista_nombre,
						revista_id,
						indicador,
					} = resp;
					proyecto.id = id_comite_proyecto;
					revista.id = revista_id;
					$(".txt_proyecto").val(nombre_proyecto);
					$(".txt_revista").val(revista_nombre);
					$(".txt_titulo").val(titulo_articulo);
					$(".indicador").val(indicador);
					$(".ranking").val(id_ranking);
				}
			);
			$("#modal_modificar_publicacion").modal();

			$("#form_modificar_solicitud").submit(() => {
				let fordata = new FormData(
					document.getElementById("form_modificar_solicitud")
				);
				let data = formDataToJson(fordata);
				data.id_proyecto = proyecto.id;
				data.id_publicacion = id_pub_global;
				data.revista_id = revista.id;
				consulta_ajax(`${ruta}modificar_publicacion`, data, (resp) => {
					let { titulo, mensaje, tipo } = resp;
					if (tipo == "success") {
						MensajeConClase(mensaje, tipo, titulo);
						$("#form_modificar_solicitud").get(0).reset();
						$("#modal_modificar_publicacion").modal("hide");
						listar_publicaciones();
					} else {
						MensajeConClase(mensaje, tipo, titulo);
					}
				});
				return false;
			});
		} else {
			MensajeConClase(
				"No se puede realizar esta acción debido a que la publicación se encuentra gestion o ya fue finalizada",
				"info",
				"Oops.!"
			);
		}
	} else {
		MensajeConClase("Debe seleccionar una publicación", "info", "Oops.!");
	}
};

const agregar_idiomas = (dato) => {
	let nuevos_idiomas = idiomas;
	consulta_ajax(`${ruta}listar_idiomas`, { dato, nuevos_idiomas }, (resp) => {
		$("#tabla_idiomas_busqueda tbody")
			.off("click", "tr")
			.off("dblclick", "tr")
			.off("click", "tr td .agregar")
			.off("click", "tr td .eliminar");
		let i = 0;
		const myTable = $("#tabla_idiomas_busqueda").DataTable({
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

		$("#tabla_idiomas_busqueda tbody").on(
			"click",
			"tr td .agregar",
			function () {
				let data = myTable.row($(this).parent()).data();
				idiomas.push(data);
				$("#form_buscar_idioma").get(0).reset();
				agregar_idiomas("F**W");
				pintar_datos_combo(idiomas, "#idiomas_select", "Idiomas");
				MensajeConClase(
					"Idioma agregado de forma exitosa.!",
					"success",
					"Porceso exitoso.!"
				);
			}
		);

		$("#tabla_idiomas_busqueda tbody").on(
			"click",
			"tr td .eliminar",
			function () {
				let data = myTable.row($(this).parent()).data();
				let index = null;
				index = idiomas.findIndex((element) => {
					return element.id == data.id;
				});
				idiomas.splice(index, 1);
				$("#form_buscar_idioma").get(0).reset();
				agregar_idiomas("F**W");
				pintar_datos_combo(idiomas, "#idiomas_select", "Idiomas");
			}
		);
	});
};

const agregar_idiomas_Bon = (dato) => {
	consulta_ajax(`${ruta}listar_idiomas_bon`, { dato }, (resp) => {
		$("#tabla_idiomas_busqueda_bon_bon tbody")
			.off("click", "tr")
			.off("dblclick", "tr")
			.off("click", "tr td .agregar")
			.off("click", "tr td .eliminar");
		let i = 0;
		const myTable = $("#tabla_idiomas_busqueda_bon_bon").DataTable({
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

		$("#tabla_idiomas_busqueda_bon_bon tbody").on("click", "tr td .agregar", function () {
			let data = myTable.row($(this).parent()).data();
			if(idiomas.length > 0) {
				MensajeConClase(
					"Ya existe un idioma seleccionado",
					"warning",
					"Atención.!"
				);
			}else if(idiomas.length == 0){
				idiomas.push(data);
				MensajeConClase(
					"Idioma agregado de forma exitosa.!",
					"success",
					"Porceso exitoso.!"
				);
				pintar_datos_combo(idiomas, "#idiomas_select_bon", "Idiomas");
			}
			$("#form_buscar_idioma_Bon").get(0).reset();
			$("#modal_buscar_idioma_bon").modal("hide");
			agregar_idiomas("F**W");
		});
	});
};

const eliminar_idioma_agregado = () => {
	let idioma_seleccionado = $("#idiomas_select").val();
	let nombre_idioma = $(
		'#idiomas_select option[value="' + idioma_seleccionado + '"]'
	).text();
	swal(
		{
			title: "¿Eliminar idioma?",
			text: `Recuerde que esta eliminando el idioma ${nombre_idioma} y ya no aparecera a menos que lo agregue nuevamente, si desea continuar presione la opción de 'Si, Entiendo!'`,
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
				if (idioma_seleccionado == "")
					MensajeConClase("Seleccione un idioma.", "info", "Oops...!");
				else {
					let idioma = idiomas.findIndex((element) => {
						return element.id == idioma_seleccionado;
					});
					idiomas.splice(idioma, 1);
					swal.close();
					pintar_datos_combo(idiomas, "#idiomas_select", "Idiomas");
				}
			}
		}
	);
};

const buscar_informacion_investigacion = () => {
	$("#modal_informacion_investigacion").modal();
	listar_grupos();
	listar_lineas();
	listar_sublineas();
};

const obtener_grupos = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_grupos`;
		consulta_ajax(url, {}, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_lineas = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_lineas`;
		consulta_ajax(url, {}, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_sublineas = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_sublineas`;
		consulta_ajax(url, {}, (resp) => {
			resolve(resp);
		});
	});
};

const listar_grupos = async () => {
	let grupos = await obtener_grupos();
	pintar_datos_combo(grupos, "#grupos_select", "Grupos de investigación");
};

const listar_lineas = async () => {
	let lineas = await obtener_lineas();
	pintar_datos_combo(lineas, "#lineas_select", "Lineas de investigación");
};

const listar_sublineas = async () => {
	let sublineas = await obtener_sublineas();
	pintar_datos_combo(
		sublineas,
		"#sublineas_select",
		"Sublineas de investigación"
	);
};

const agregar_autor_inicial = (data) => {
	autores.push({
		id: data.id,
		tabla: data.tabla,
		nombre_completo: data.nombre_completo,
		grupo: $("#grupos_select").val(),
		linea: $("#lineas_select").val(),
		sublinea: $("#sublineas_select").val(),
	});

	$("#modal_informacion_investigacion").modal("hide");
	listar_autores_iniciales();
};

const informacion_autor = (id) => {
	$("#modal_detalle_autor").modal();
	consulta_ajax(`${ruta}informacion_autor`, { id }, (resp) => {
		let {
			nombre_completo,
			afiliacion,
			identificacion,
			tipo_identificacion,
			grupo,
			linea,
			sublinea,
		} = resp;
		$("#id_identificacion_autor").hide();
		$("#id_grupo_autor").hide();

		if (identificacion) $("#id_identificacion_autor").show();
		if (grupo) $("#id_grupo_autor").show();

		$(".nombre_completo").html(nombre_completo);
		$(".tipo_identificacion").html(tipo_identificacion);
		$(".identificacion").html(identificacion);
		$(".grupo").html(grupo);
		$(".linea").html(linea);
		$(".sublinea").html(sublinea);
		$(".afiliacion").html(afiliacion);
	});
};

const obtener_autores_distribucion = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}listar_autores_distribucion`;
		consulta_ajax(url, { id }, (resp) => {
			resolve(resp);
		});
	});
};

const call_inputs = () => {
	console.log(status_selected);
	$("#adjs_pubs").html("");
	if (status_selected.idaux == "Pub_Red_E") {
		return false;
	}
	consulta_ajax(`${ruta}obtener_tipos_adjuntos`, status_selected, (res) => {
		$(".file_se").off("change");
		tipos_adjs = [];
		campo_fechaa = res[0].campo_fecha;
		for (const key in res) {
			tipos_adjs.push(res[key].tipo);
			$("#adjs_pubs").removeClass("oculto").append(`
      <div class="agro agrupado">
        <div class="input-group ">
          <label class="input-group-btn"><span class="btn btn-primary">
            <span class="fa fa-folder-open"></span>Buscar <input name="${res[key].tipo}" class="file_se" type="file" style="display: none;"></span>
          </label>
          <input type="text" class="form-control file_selected" id="${key}" readonly placeholder='Adjunte ${res[key].placeholder}' value="">
        </div>
      </div>
      `);
		}
		$("#adjs_pubs").removeClass("oculto").append(`
    <div class="${res[0].pub_link == undefined ? "oculto" : "agrupado"}">
      ${res[0].pub_link == undefined ? "" : res[0].pub_link}
    </div>
    <div class="agro agrupado">
      <div class="agrupado">
        <div class="input-group">
          ${res[0].fecha_request}
        </div>
      </div>
    </div>
    `);
	});
};

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
};

const limite_caracteres = (limite_caracteres, input_evaluar) => {
	let str_count = $(input_evaluar);
	$(input_evaluar).on("keydown", function (e) {
		if (str_count.val().length >= limite_caracteres) {
			if (
				e.key == "Backspace" ||
				e.key == "Alt" ||
				e.key == "F5" ||
				e.key == "Tab" ||
				e.key == "F12" ||
				e.key == "Tab"
			) {
				return true;
			} else {
				return false;
			}
		}
	});
};

const validar_links = (clase) => {
	let er = /^(?!\s\w\d)?http?s?:\/\/[a-z 0-9 /?:'=+-_]*[.]+[a-z]{2,}[\W\w\d]*$/g;
	let evaluar = $(clase).val();
	let evaluado = er.test(evaluar);
	if (evaluado === false && evaluar !== "") {
		swal(
			{
				title: "Link incorrecto",
				text:
					"El enlace suministrado está escrito de manera incorrecta, favor verificar.",
				type: "warning",
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Ok, corregir",
				allowOutsideClick: true,
			},
			function (isConfirm) {
				swal.close();
			}
		);
	}
};

const enviar_correo = async (data) => {
	let { id, correo, id_estado, validacion, solicitante, observacion } = data;
	let ser = `<a href="${server}index.php/publicaciones/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = -1;
	let titulo = `Publicaciones - Solicitud revisada`;
	let correos = correo;
	let nombre = "";
	let body = `Se informa que su solicitud de publicaciones, ha sido revisada con exito y ha cumplido con los requisitos requeridos,
  para continuar con la gestión de su solicitud ingrese a: ${ser}`;
	let sw = false;
	if (validacion == 0) {
		tipo = 1;
		nombre = solicitante;
		sw = true;
	} else if (validacion == 1) {
		sw = true;
		body = `Se informa que su solicitud ha sido revisada y rechazada por la siguinete observación:
    <br><br>
    ${observacion}
    <br><br> 
    para realizar correcciones y continuar con la gestión de su solicitud ingrese a: ${ser}`;
		tipo = 1;
		nombre = solicitante;
	} else if (id_estado == "Pub_Ace_E" || id_estado == "Pub_Rec_E") {
		let autores_distribucion = await obtener_autores_distribucion(id);
		sw = true;
		let datos = "";
		tipo = 3;
		nombre = "Autores";
		correos = autores_distribucion;
		autores_distribucion.forEach((element) => {
			datos += `<tr><th scope="row">${element.identificacion}</th><td>${element.persona}</td><td>${element.puntos}</td></tr>`;
		});
		let tabla = `<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrao.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
      </head>
      <body>
        <style>
          table {
            border-collapse: collapse;
          }
          table, th, td {
            border: 1px solic #000000;
          }
          thead {
            background-color: #d43139;
            color: #ffffff;
          }
        </style>
        <table>
          <thead>
            <tr>
              <th scope="col">Identificación</th>
              <th scope="col">Nombre del autor</th>
              <th scope="col">Porcentaje</th>
            </tr>
          </thead>
          <tbody>
            ${datos}
          </tbody>
        </table>
      </body>
    </html>`;
		body = `Se informa que se ha realizado el proceso de distribución de porcentaje de su publicación de la siguiente forma: 
    <br><br> 
    ${tabla}
    <br><br>
    Para aceptar los porcentajes asignados usted puede ingresar a: ${ser}`;
	}
	if (sw)
		enviar_correo_personalizado(
			"Pub",
			body,
			correos,
			nombre,
			"Publicaciones CUC",
			titulo,
			"ParCodAdm",
			tipo
		);
};

const search_author__bonus = (dato, callbak, tabla) => {
	if (tabla == "") {
		MensajeConClase(
			"Por favor seleccione el tipo de autor a buscar",
			"info",
			"Oops.!"
		);
	} else {
		consulta_ajax(`${ruta}buscar_autor_bon`, { dato, tabla }, (resp) => {
			$("#table_authors_bonus__search tbody")
				.off("click", "tr td .asignar")
				.off("dblclick", "tr")
				.off("click", "tr")
				.off("click", "tr td:nth-of-type(1)");
			let i = 0;
			const myTable = $("#table_authors_bonus__search").DataTable({
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
						defaultContent:
							'<span style="color: #39B23B;" title="Seleccionar Autor" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" ></span>',
					},
				],
				language: get_idioma(),
				dom: "Bfrtip",
				buttons: [],
			});

			//EVENTOS DE LA TABLA ACTIVADOS
			$("#table_authors_bonus__search tbody").on("click", "tr", function () {
				$("#table_authors_bonus__search tbody tr").removeClass("warning");
				$(this).attr("class", "warning");
			});

			$("#table_authors_bonus__search tbody").on("dblclick", "tr", function () {
				let data = myTable.row($(this).parent().parent()).data();
				callbak(data);
			});

			$("#table_authors_bonus__search tbody").on("click", "tr td .asignar", async function () {
				$("#btn_add_doc_bon").off("click");
				$("#btn_add_est_bon").off("click");
				$(".btn_guardar_id_programa").off("click");
				let data = myTable.row($(this).parent().parent()).data();
				if (tabla === "personas") {
					$("#modal_solicitar_prog_dep").modal();
					$(".btn_guardar_id_programa").click(() => {
						let id_programa = $("#modal_solicitar_prog_dep select[name='id_programa']").val();
						if(!id_programa){
							MensajeConClase(
								`Debe completar todos los campos antes de continuar.`,
								"warning",
								"Oops!"
							);
						}else{
							agregar_autor_inicial_bonificaciones(data, id_programa);
						}
						return false;
					});
				} else if (tabla === "estudiante") {
					$("#modal_info_estudiante_bon").modal();
					$("#selec_program_est").removeClass("oculto");
					$("#inst_externa").addClass("oculto");
					$("#btn_add_est_bon").click(() => {
						add_author_stud_bon(data);
					});
				} else if (tabla === "externo") {
					listar_inst_externas();
					$("#modal_info_estudiante_bon").modal();
					$('#inst_ext_bon').selectpicker();
					$("#inst_externa").removeClass("oculto");
					$("#selec_program_est").addClass("oculto");
					$("#btn_add_est_bon").click(() => {
						add_author_exter_bon(data);
					});
				}
			});
		});
	}
};

const guardarSolicitud = () => {
	swal(
		{
			title: "Estas Seguro?",
			text:
				"Tener en cuenta que, al realizar esta acción se creara una nueva solicitud, si desea continuar debe  presionar la opción de 'Si, Entiendo' !",
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
				guardarBorrador();
				swal.close();
			}
		}
		);
};

const guardarBorrador = (id_programa="", id_departamento="") => {
	consulta_ajax(`${ruta}guardarBorrador`, {id_programa, id_departamento}, (resp) => {
		let { tipo, solicitud } = resp;
		if (resp.tipo == "success") {
			id_solicitud = solicitud.id;
			data_solicitante = {nombre: solicitud.nombre_completo, correo: solicitud.correo};
			enviar_correo_estado("Bon_Sol_Creado", id_solicitud, "");			
			//$("#modal_seleccion_info_docente").modal("hide");
			$("#modal_registro_solicitud_bonificacion").modal("show");
		}
	});
};

const editarSolicitud = (title, mensaje, validacion) => {
	swal(
		{
			title: title,
			text: mensaje,
			type: "warning",
			showCancelButton: validacion == true ? true : false,
			confirmButtonColor: "#D9534F",
			confirmButtonText: validacion == true ? "Si, Entiendo!" : "Entiendo",
			cancelButtonText: "No, cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: validacion == true ? false : true,
			closeOnCancel: true,
		},
		function (isConfirm) {
			if (isConfirm && validacion == true) {
				obtenerUltimaSolicitud();
				swal.close();
			}
		}
	);
};

const obtenerUltimaSolicitud = (id = '') => {
	consulta_ajax(`${ruta}obtenerUltimaSolicitud`, {}, (resp) => {
		let { tipo, solicitud } = resp;
		if (resp.tipo == "success") {
			id_solicitud = !id ? solicitud.id : id;
			$("#modal_registro_solicitud_bonificacion").modal("show");
		}
	});
};

const validar_cantidad_de_solicitud = () => {
  return new Promise(resolve => {
    let url = `${ruta}validar_cantidad_de_solicitud`;
    consulta_ajax(url, {}, resp => {
      inicial = resp.inicial
      resolve(resp);
    });
  });
}

const buscar_revista_bon = (dato, callbak) => {
	consulta_ajax(`${ruta}buscar_revista`, { dato }, (resp) => {
		$("#tabla_revista_busqueda_bon tbody")
			.off("click", "tr")
			.off("dblclick", "tr")
			.off("click", "tr td .seleccionar")
			.off("click", "tr td .editar");
		let i = 0;
		const myTable = $("#tabla_revista_busqueda_bon").DataTable({
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
					data: "valor",
				},
				{
					defaultContent: sw_rev
						? '<span style="color: #2E79E5;" title="Editar Revista" data-toggle="popover" data-trigger="hover" class="fa fa-edit btn btn-default editar" ></span>'
						: '<span style="color: #39B23B;" title="Seleccionar Revista" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>',
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$("#tabla_revista_busqueda_bon tbody").on("click", "tr", function () {
			$(`#tabla_revista_busqueda_bon tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_revista_busqueda_bon tbody").on("dblclick", "tr", function () {
			let data = myTable.row($(this).parent().parent()).data();
			$("#modal_buscar_proyecto").modal("hide");
			callbak(data);
		});

		$("#tabla_revista_busqueda_bon tbody").on("click", "tr td .seleccionar", function () {
			let data = myTable.row($(this).parent().parent()).data();
			callbak(data);
			$("#form_buscar_revista_bon").get(0).reset();
			$("#modal_buscar_revista_bon").modal("hide");
			pintar_info_principal(data.id);
		});

		$("#tabla_revista_busqueda_bon tbody").on(
			"click",
			"tr td .editar",
			async function () {
				let data = myTable.row($(this).parent().parent()).data();
				await listar_cuartiles();
				revista = data;
				$("#text_nombre_revista").val(data.valor);
				$("#txt_issn").val(data.valorx);
				$("#txt_isbn").val(data.valorz);
				$("#cuartil").val(data.valory);
				$("#modal_almacenar_revista").modal();
			}
		);
	});
};

const obtener_data__bonificaciones = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_data__bonificaciones`;
		consulta_ajax(url, {id}, async (resp) => {
			resolve(resp);
		});
	});
};

const obtener_data_revista = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_data_revista`;
		consulta_ajax(url, {id}, async (resp) => {
			resolve(resp);
		});
	});
};

const pintar_info_principal = async (data) => {
	let result = await obtener_data_revista(data);
	let { id, idparametro, valor, valorx, cuartil
		//, editorial 
	} = result[0];
	$("#modal_principal_bonificaciones input[name='issn_bon']").val(valorx);
	$("#modal_principal_bonificaciones select[name='cuartil_scopus']").val(
		cuartil
	);
	//$("#modal_principal_bonificaciones input[name='editorial']").val(editorial);
}

const obtener_datos_autor = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}ver_detalle_autor_bonificaciones`;
		consulta_ajax(url, { id, id_solicitud, id_afiliacion }, (resp) => {
			resolve(resp);
		});
	});
};

const ver_detalle_autor_bonificaciones = async (id) => {
	$("#modal_detail_data__bonificaciones").modal();
	let result = await obtener_datos_autor(id);
	let afiliacion = result[0] ? result[0].afiliacion : [];
	
	$(".id_program_acad").addClass("oculto");
	$(".id_inst_per_ext ").addClass("oculto");
	$(".id_departamento_autor").addClass("oculto");
	$(".id_inst_per_ext").addClass("oculto");
	$(".info_adicional_autor").addClass("oculto");
	
	$(".nombre_completo__bon").html("");
	$(".tipo_identificacion__bon").html("");
	$(".identificacion__bon").html("");
	$(".linea__bon").html("");
	$(".sublinea__bon").html("");
	$(".afiliacion__bon").html("");
	$(".Vinculacion__bon").html("");
	
	result[0] ? $(".afiliacion__bon").html(result[0].afiliacion == "profesor" ? "Profesor" : result[0].afiliacion == "estudiante" ? "Estudiante" : "Externo") : "";
	switch (afiliacion) {
		case "profesor":
			$(".info_adicional_autor").removeClass("oculto");
			$(".afil_vinc_bon ").removeClass("oculto");
			$(".table_afiliaciones_inst").removeClass("oculto");
			$(".id_program_acad").addClass("oculto");
			$(".id_inst_per_ext ").addClass("oculto");
			
			$(".nombre_completo__bon").html(
				result[0].nombre_completo
					? result[0].nombre_completo + `${result[0].corresponding_author == 1 ? " <span class='fa fa-envelope' title='Corresponding Author'></span>" : ''}` : ""  
			);
			$(".tipo_identificacion__bon").html(result[0].tipo_identificacion ? result[0].tipo_identificacion : "");
			$(".identificacion__bon").html(result[0].identificacion ? result[0].identificacion : "");
			$(".departamento_aut__bon").html(result[0].departamento);
			$(".Vinculacion__bon").html(result[0].vinculacion);
			
			$(".enlace_cvlac").html(result[0].url_cvlac ? `<a href="${result[0].url_cvlac}" target="_blank">ver</a>` : "");
			$(".enlace_google").html(result[0].url_google_scholar ? `<a href="${result[0].url_google_scholar}" target="_blank">ver</a>` : "");
			$(".enlace_rg").html(result[0].url_research_gate ? `<a href="${result[0].url_research_gate}" target="_blank">ver</a>` : "");
			$(".enlace_red_acad").html(result[0].url_red_acad_disc ? `<a href="${result[0].url_red_acad_disc}" target="_blank">ver</a>` : "");
			$(".enlace_mendeley").html(result[0].url_mendeley ? `<a href="${result[0].url_mendeley}" target="_blank">ver</a>` : "");
			$(".enlace_publons").html(result[0].publons ? `<a href="${result[0].publons}" target="_blank">ver</a>` : "");
			$(".enlace_gruplac").html(result[0].gruplac ? `<a href="${result[0].gruplac}" target="_blank">ver</a>` : "");
			$(".cat_investigador").html(result[0].categ_minciencias);
			$(".departamento_aut_bon").html(result[0].departamento);
			$(".hi_index_scholar").html(result[0].hi_scholar);
			$(".hi_index_scopus").html(result[0].hi_scopus);
			$(".rg_score").html(result[0].research_gate);
			$(".orcid_id_info").html(result[0].orcid ? `<a href="https://orcid.org/${result[0].orcid}" target="_blank">ver</a>` :  "");			
		break;
			
		case "estudiante":
			$(".id_program_acad ").removeClass("oculto");
			$(".id_inst_per_ext ").addClass("oculto");
			$(".afil_vinc_bon").addClass("oculto");
			$(".table_afiliaciones_inst").addClass("oculto");
			
			$(".nombre_completo__bon").html(result[0].nombre_completo);
			$(".tipo_identificacion__bon").html(result[0].tipo_identificacion);
			$(".identificacion__bon").html(result[0].identificacion);
			$(".programa_academico").html("CUC - " + result[0].programa_academico);
		break;
			
		case "externo":
			$(".id_inst_per_ext").removeClass("oculto");
			$(".id_program_acad").addClass("oculto");
			$(".afil_vinc_bon").addClass("oculto");
			$(".table_afiliaciones_inst").addClass("oculto");
			$(".nombre_completo__bon").html(result[0].nombre_completo);
			$(".tipo_identificacion__bon").html(result[0].tipo_identificacion);
			$(".identificacion__bon").html(result[0].identificacion);
			$(".institucion_externa").html(result[0].institucion_ext);
		break;
			
	}

};
const guardar_autores_bonificaciones = (objeto) => {
	consulta_ajax(`${ruta}guardar_autores_bonificaciones2`, { data: objeto}, async (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			autor = [];
			autores_estudiantes = [];
			autores_externos = [];
			autores_profesores = [];
			$("#autores").html("");
			$("#modal_buscar_autor_bonificaciones").modal("hide");
			MensajeConClase(mensaje, tipo, titulo);
			if(id_afiliacion == "profesor"){
				$("#data_profesores").html("");
				autores_profesores = await listar_autor_porTipo(id_solicitud, id_afiliacion);
				pintar_autores();
			}else if(id_afiliacion == "estudiante"){
				$("#data_estudiantes").html("");
				autores_estudiantes = await listar_autor_porTipo(id_solicitud, id_afiliacion);
				pintar_autores();
			}else if(id_afiliacion == "externo"){
				autores_externos = [];
				$("#data_externos").html("");
				autores_externos = await listar_autor_porTipo(id_solicitud, id_afiliacion);
				pintar_autores();
			};
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};

const buscar_proyecto__bon = (dato, callbak) => {
	consulta_ajax(`${ruta}buscar_proyecto`, { dato }, (resp) => {
		$("#tabla_proyecto_busqueda__bon tbody")
			.off("click", "tr")
			.off("dblclick", "tr")
			.off("click", "tr td .seleccionar");
		let i = 0;
		const myTable = $("#tabla_proyecto_busqueda__bon").DataTable({
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
					data: "nombre_proyecto",
				},
				{
					data: "nombre_investigador",
				},
				{
					defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Proyecto" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>',
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		//EVENTOS DE LA TABLA ACTIVADOS
		$("#tabla_proyecto_busqueda__bon tbody").on("click", "tr", function () {
			$(`#tabla_proyecto_busqueda__bon tbody tr`).removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_proyecto_busqueda__bon tbody").on("dblclick", "tr", function () {
			let data = myTable.row($(this).parent().parent()).data();
			$("#modal_buscar_proyecto__bon").modal("hide");
			callbak(data);
		});

		$("#tabla_proyecto_busqueda__bon tbody").on(
			"click",
			"tr td .seleccionar",
			function () {
				let data = myTable.row($(this).parent().parent()).data();
				callbak(data);
				id_proyecto_bon = data.id;
				$("#form_buscar_proyecto").get(0).reset();
				$("#modal_buscar_proyecto__bon").modal("hide");
			}
		);
	});
};

const obtener_opc__bonific = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_opc__bonific`;
		consulta_ajax(url, {}, (resp) => {
			resolve(resp);
		});
	});
};

const pintar_datos_select__bon = (datos, combo, mensaje, sele = "") => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach((element) => {
		$(combo).append(
			`<option value='${element.id_aux}'> ${element.valor} </option>`
		);
	});
	$(combo).val(sele);
};

const listar_cuart_scopus = async () => {
	let select = await obtener_opc__bonific();
	pintar_datos_select__bon(select, ".cbxindex_scopus", "¿Indexado en Scopus (SJR)?");
};

const listar_cuart_wos = async () => {
	let select = await obtener_opc__bonific();
	pintar_datos_select__bon(select, ".cbxindex_wos", "¿Indexado en Web of Science (JCR)?");
};

const agregar_autor_inicial_bonificaciones = async (data, id_programa) => {
	autores = [];
	// Se valida que tenga información la data
	let objeto = {
		id: data.id,
		tabla: data.tabla,
		afiliacion: "profesor",
		documento: data.identificacion,
		nombre_completo: data.nombre_completo,
		id_solicitud : id_solicitud,
		id_programa: id_programa,
	}
	guardar_autores_bonificaciones(objeto);
	$("#modal_solicitar_prog_dep").modal("hide");
	$("#form_solicitar_prog_dep").trigger("reset");
};

const add_author_stud_bon = async (data) => {
	let programa_est_bon = [{ programa_est_bon: $("#programa_est_bon").val(), }];
	// Se valida que tenga información la data
	let validacion = await validateData(tabla_buscar, programa_est_bon);
	// Se obtiene el resultado de la validación
	let {titulo, mensaje, tipo} = validacion;
	if(tipo === "success"){
		let objeto = {
			id: data.id,
			tabla: data.tabla,
			documento: data.identificacion,
			afiliacion: "estudiante",
			nombre_completo: data.nombre_completo,
			programa_acad: $("#programa_est_bon").val(),
			id_solicitud: id_solicitud,
		};
		guardar_autores_bonificaciones(objeto);
	}else if(tipo === "info"){
		MensajeConClase(mensaje, tipo, titulo);
	}
	$("#modal_info_estudiante_bon").modal("hide");
	$("#form_tipo_afiliaciones").trigger("reset");
};

const add_author_exter_bon = async (data) => {
	let inst_ext_bon = [{ inst_ext_bon : $("#inst_ext_bon").val(),}];
	// Se valida que tenga información la data
	let validacion = await validateData(tabla_buscar, inst_ext_bon);
	// Se obtiene el resultado de la validación
	let { titulo, mensaje, tipo } = validacion;
	if (tipo == "success") {
		let objeto = {
			id: data.id,
			tabla: data.tabla,
			documento: data.identificacion,
			afiliacion: "externo",
			nombre_completo: data.nombre_completo,
			institucion_ext: $("#inst_ext_bon").val(),
			id_solicitud: id_solicitud,
		};
		guardar_autores_bonificaciones(objeto);
	} else if(tipo === "info"){
		MensajeConClase(mensaje, tipo, titulo);
	}
	$("#modal_info_estudiante_bon").modal("hide");
	$("#form_tipo_afiliaciones").trigger("reset");
};

const validateData = (afiliacion, data) => {
	return new Promise((resolve) => {
		let url = `${ruta}validateData`;
		consulta_ajax(url, { afiliacion, data }, (resp) => {
			resolve(resp);
		});
	});
};

const update_porcentaje = () => {
	let array_porc = [];
	array_porc.push({ id_articulo: id_solicitud, porcentajes });
	consulta_ajax(
		`${ruta}update_porcentaje`,
		{ datos: porcentajes, id_articulo: id_solicitud },
		(resp) => {
			if (resp.tipo == "success") {
				$("#form_asignar_porcentaje").get(0).reset();
				pintar_autores();
			} else {
				MensajeConClase(resp.mensaje, resp.tipo, resp.titulo);
			}
		}
	);
};

const modificar_porcentajes_dir = () => {
	let array_porc_cp = [];
	array_porc_cp.push({ id_articulo: id_solicitud, porcentajes_cp });
	consulta_ajax(
		`${ruta}modificar_porcentajes_dir`,
		{ datos: porcentajes_cp, id_articulo: id_solicitud },
		(resp) => {
			if (resp.tipo == "success") {
				MensajeConClase(resp.mensaje, resp.tipo, resp.titulo);
				$("#form_mod_porcentajes_dir").get(0).reset();
			} else {
				MensajeConClase(resp.mensaje, resp.tipo, resp.titulo);
			}
		}
	);
};

const update_otros_aspectos = (data_respuestas) => {
	consulta_ajax(`${ruta}update_otros_aspectos`, {data_respuestas, id_solicitud}, (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_otros_aspectos").trigger("reset");
			$("#modal_otros_aspectos__bon").modal("hide");
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
};


const listar_articulos_suscritos = (id) => {
	consulta_ajax(`${ruta}obtener_articulos_suscritos`, {id}, (resp) => {
		let num = 0;
			$(`#table_articulos_suscritos_aut tbody`)
				.off("click", "tr td")
				.off("dblclick", "tr td")
		const myTable = $("#table_articulos_suscritos_aut").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data: resp,
			columns: [
				{
					render: function (resp, type, full, meta) {
                        num++;
                        return num;
                    }
				},
				{ 
					data: 'estado_final' 
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
	});
}

const agregar_articulos_cumplidos = (id) => {
	consulta_ajax(`${ruta}listar_articulos_cumplidos`, { id }, (resp) => {
		$("#table_articulos_cumplidos_aut tbody")
			.off("dblclick", "tr")
			.off("click", "tr")
			.off("click", "tr td:nth-of-type(1)")
			.off("click", "tr .eliminar");
		let i = 0;
		const myTable = $("#table_articulos_cumplidos_aut").DataTable({
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
					data: "cantidad_autor",
				},
				{
					data: "cuartil",
				},
				{
					data: "title",
				},
				{
					data: "link",
				},
				{
					render: function (data, type, full, meta) {
						return `<span style="color:red" id="${i}" class="fa fa-trash-o btn btn-default pointer eliminar"></span>`;
					},
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});
		//EVENTOS DE LA TABLA ACTIVADOS
		$("#table_articulos_cumplidos_aut tbody").on("click", "tr", function () {
			$("#table_articulos_suscritos_aut tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#table_articulos_cumplidos_aut tbody").on("click", "tr .eliminar", function () {
			let {id} = myTable.row($(this).parent().parent()).data();
			modificar_articulos_cumplidos(id);
			agregar_articulos_cumplidos(id);
		});
	});
};

const asignar_articulos_cumplidos = () => {
	let { nombre_completo, id } = info_articulos_cumplidos;
	let cuartil_autor = $("#modal_articulos_cumplidos select[name='cuartil_autor_cump']").val();
	let cantidad_autor = $("#modal_articulos_cumplidos input[name='cantidad_autor_cump']").val();
	let link_autor_cump = $("#modal_articulos_cumplidos input[name='link_autor_cump']").val();
	let title_art = $("#modal_articulos_cumplidos input[name='titulo_art']").val();
	consulta_ajax(
		`${ruta}almacenar_articulos_cumplidos`,
		{ nombre_completo, id_autor_bon, cantidad_autor, link_autor_cump, cuartil_autor, id_solicitud, title_art},
		(resp) => {
			let { titulo, mensaje, tipo } = resp;
			if (tipo == "success") {
				MensajeConClase(mensaje, tipo, titulo);
				$("#form_articulos_cumplidos").trigger("reset");
				$("#form_articulos_cumplidos").modal("hide");
				agregar_articulos_cumplidos(id_autor_bon);
			} else {
				MensajeConClase(mensaje, tipo, titulo);
			}
		}
	);
	$("#modal_articulos_cumplidos").modal("hide");
};

const guardar_evidencias = () => {
	let data = new FormData(document.getElementById("form_evidencias_bonificaciones"));
	data.append("id_solicitud", id_solicitud);
	enviar_formulario(`${ruta}guardar_evidencias`, data, (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_evidencias_bonificaciones").trigger("reset");
			$("#modal_evidencias_bonificaciones").modal("hide");
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const guardar_comentario = () => {
	let data = new FormData(document.getElementById("form_guardar_comentario_porcentaje"));
	data.append("id_bonificacion", id_solicitud);
	data.append("id_afiliacion", id_afiliacion);
	data.append("id_persona", id_autor_bon);
	enviar_formulario(`${ruta}guardar_comentario`, data, (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			$("#form_guardar_comentario_porcentaje").trigger("reset");
			$("#modal_comentario_porcentaje").modal("hide");
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const obtener_preguntas_otros_aspectos = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_preguntas_otros_aspectos`;
		consulta_ajax(url, { }, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_respuestas_otros_aspectos = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_respuestas_otros_aspectos`;
		consulta_ajax(url, { }, (resp) => {
			resolve(resp);
		});
	});
};


const listar_otros_aspectos = async () => {
	data_preguntas = await obtener_preguntas_otros_aspectos();
	let respuestas = await obtener_respuestas_otros_aspectos();
	pintar_otros_aspectos(data_preguntas, respuestas);
};

const pintar_evidencias = () => {
	$("#content_evidencias").html("");
	$("#content_evidencias").css("display", "block");

	$("#content_evidencias").append(`
		<span class="btn btn-default" id="btn_add_evidence">
			<span class="fa fa-plus-circle" id="add_evidence"></span>
			Agregar Evidencia
		</span>
	`);

	let i = 0;
	//datos.forEach((element) => {
		$(`#btn_add_evidence`).off("click");
		$(`#btn_add_evidence`).click(async function () {
			i += 1;
			activarfile();
			$("#content_evidencias").append(``);
			$("#content_evidencias").append(`
			<div class="evidencias_unique">
				<input type="text" class="form-control rounded" name='Comentario_Evidencia_${i}' placeholder='Comentario'></input> 
				<div id="campo_evidencia_bon_${i}" class="input-group agrupado"> 
				<label class="input-group-btn">
					<span class="btn btn-primary">
						<span class="fa fa-folder-open"></span> 
						Buscar <input name="adj_evidencia_bon_${i}" id="adj_evidencia_bon_${i}" type="file" style="display: none;">
					</span>
				</label><input type="text" id="evidencia_bon_${i}" class="form-control" readonly placeholder='Adjuntar' required></div> 
			</div>
			`);
			activarfile();
		});
	//});
	pintar_evidencias_existentes();
};

const pintar_otros_aspectos = (preguntas, respuestas) => {
	$("#container__otros_aspectos").html("");
	$("#container__otros_aspectos").css("display", "block");
	preguntas.forEach((element => {
		$("#container__otros_aspectos").append(`
		<div class="otros_aspectos_unique">
		<p>${element.valor}</p>
		`);
		respuestas.forEach((respuesta)=>{
			$("#container__otros_aspectos").append(
				`
				<label for="pre1_res_1"><input  id="answer_${element.id}_${respuesta.id_aux}" type="radio" name="pregunta_${element.id}" onclick="cambiar_respuesta('${element.id}','${respuesta.id_aux}')" value="${respuesta.id_aux}"><span> ${respuesta.valor}</span></label>
				`
			);
		});
		$("#container__otros_aspectos").append(`
			<div class="container__question__`+element.id+`">
			</div>
		`);
	}))
	pintar_respuestas_ot_as();
}


const cambiar_respuesta = (idpregunta, id_respuesta) => {
	data_preguntas.map(function (dato) {
		if (dato.id == idpregunta) {
			dato.respuesta = id_respuesta;
			$(".container__question__" + idpregunta).html("");
			if (dato.respuesta == "Ans_Otr_Asp_SI") {
				if(dato.valory == "resuelve_problematica"){
					$(".container__question__" + idpregunta).append(
						`<select name="alcan_proble" id="cbxalcan_proble" required class="form-control cbxalcan_proble">
                  			<option value="">Seleccione el alcance</option>
                		</select>
						<div class="otra_info"></div>
						`
					);
					
					Cargar_parametro_buscado(309, ".cbxalcan_proble", "Seleccione el alcance");

					$(".cbxalcan_proble").on("change", async function () {
						let campo_valor = $(this).val();
						let obj = await buscar_datos_valor_parametro("alcanc_reg_boni", 2);
						$(".otra_info").html("");
						
						dato.alcance = obj["id"];
						if(campo_valor == obj['id']){
							dato.objetivo_alc = null;
							$(".otra_info").append(
								`<select name="pacto_plan" id="cbxpacto_plan" required class="form-control cbxpacto_plan">
                  					<option value="">Seleccione el Pacto</option>
                				</select>
								<select name="comp_pacto" id="cbxcomp_pacto" required class="form-control cbxcomp_pacto">
                  					<option value="">Seleccione el componente del Pacto</option>
                				</select>
								<select name="obj_ret" id="cbxobj_ret" required class="form-control cbxobj_ret">
                  					<option value="">Seleccione el Objetivo o Reto</option>
                				</select>
								`
							);
							let pacto = await obtener_valores_permiso(campo_valor, 311, 1);
							pintar_datos_combo_gen(pacto, ".cbxpacto_plan", 'Seleccione un Objetivo');
							$(".cbxpacto_plan").on("change", async function () {
								let valor_pacto = $(this).val();
								let pacto = await obtener_valores_permiso(valor_pacto, 312, 1);
								pintar_datos_combo_gen(pacto, ".cbxcomp_pacto", 'Seleccione un Componente');	
								$(".cbxcomp_pacto").on("change", async function () {
									let valor_objetivo = $(this).val();
									let objetivo = await obtener_valores_permiso(valor_objetivo, 313, 1);
									pintar_datos_combo_gen(objetivo, ".cbxobj_ret", 'Seleccione un Objetivo');	
								});
								
							});	
							$(".cbxobj_ret").on("change", function () {
								dato.pacto = $(".cbxpacto_plan").val();
								dato.componente = $(".cbxcomp_pacto").val();
								dato.objetivo = $(".cbxobj_ret").val();
							});
						}else if(campo_valor != obj['id']){
							dato.pacto = null;
							dato.componente = null;
							dato.objetivo = null;
							$(".otra_info").append(
								`<select name="objet_alcan" id="cbxobjet_alcan" required class="form-control cbxobjet_alcan">
                  					<option value="">Seleccione el objetivo</option>
                				</select>
								<textarea class="form-control inputt" id="txt_comentario__answer__` +idpregunta +`" name="justificacion" placeholder="Justificación"></textarea>
								`
							);
							let objetivo = await obtener_valores_permiso(campo_valor, 310, 1);
							pintar_datos_combo_gen(objetivo, ".cbxobjet_alcan", 'Seleccione un Objetivo');
							$(".cbxobjet_alcan").on("change", function () {
								dato.objetivo_alc = $(".cbxobjet_alcan").val();
							});
						}
					});
				}else{
					$(".container__question__" + idpregunta).append(
						`<input type="text" style="width: 460px;" class="form-control" name="commentario__` +
							idpregunta + `" id="txt_comentario__answer__` +idpregunta +`" placeholder="`+dato.valorz+`">
						</div>`
					);
				}
			}else{
				dato.id_alcance = null;
				dato.alcance = null;
				dato.pacto = null;
				dato.componente = null;
				dato.objetivo = null;
				dato.objetivo_alc = null;
			}
		}
	});
};

const pintar_autores = async () => {
	let div = "";
	$("#data_profesores").html("");
	$("#data_estudiantes").html("");
	$("#data_externos").html("");	
	let autor = [];
	id_afiliacion == "profesor"
		? ((div = "#data_profesores"), (autor = autores_profesores))
	: id_afiliacion == "estudiante"
		? ((div = "#data_estudiantes"), (autor = autores_estudiantes))
	: ((div = "#data_externos"), (autor = autores_externos));
	$(div).html("");
	
	$(div).append(
		`<div class="cargando_data text-center"><img src="${Traer_Server()}/imagenes/loading.gif" style='width:5%;'><h3>Cargando...</h3></div>`
	);
	$(".msg_porcentaje").html("");
	let btn_persona = "";
	porce = await suma_porcentajes();
	if (id_afiliacion == "profesor" && porce > 0 && porce <= 99) $(".msg_porcentaje").append(`
		<div class="alert alert-warning">
			<p class="text-justify">
				<span class="fa fa-info"></span><strong> Tener en cuenta: </strong> Le queda un ` +  (100 - porce) + "%" +
			  ` de distribución del artículo economica disponible para cumplir el 100%.
			</p>
		</div> 
	`);
	$("#btn_agregar_persona").off('click');
	$("#btn_agregar_persona").click(function () {
		$(".btn_nuevo_autor_bon").addClass("oculto");
		$("#form_buscar_autores_bonificaciones").get(0).reset();
		search_author__bonus();
		tabla_buscar = "personas";
		$("#modal_buscar_autor_bonificaciones").modal("show");
	});
	$("#btn_agregar_estudiante").off("click");
	$("#btn_agregar_estudiante").click(function () {
		$(".btn_nuevo_autor_bon").removeClass("oculto");
		$("#form_buscar_autores_bonificaciones").get(0).reset();
		search_author__bonus();
		tabla_buscar = "estudiante";
		$("#modal_buscar_autor_bonificaciones").modal("show");
	});
	$("#btn_agregar_externo").off("click");
	$("#btn_agregar_externo").click(function () {
		$(".btn_nuevo_autor_bon").removeClass("oculto");
		$("#form_buscar_autores_bonificaciones").get(0).reset();
		search_author__bonus();
		tabla_buscar = "externo";
		$("#modal_buscar_autor_bonificaciones").modal("show");
	});	
	if(autor.length == 0 ){
		$(".cargando_data").hide();
		$(".btn_autores_profesores").on("click", function() {return true;});
		$(".btn_autores_profesores").on("click", function() {return true;});
		$(".btn_autores_externos").on("click", function() {return true;});
	} 
	if(autor.length != 0) autor.map(async ({ nombre_completo, afiliacion, id, documento, id_autor, corresponding_author }, indice) => {
		id_porcentaje = id;
		data = await obtener_porcentaje();
		let foto = 
		$(".cargando_data").hide();
		$(div).append(
			`
			<div class="col-md-6 col-lg-3 mb-5">
				<div class="card">
					<img class="img-fluid" style="max-width: 100%; height: 150px !important; margin: 0 auto;" src="https://agil.cuc.edu.co/Fotos/` +
				documento +
				`.jpg" onerror="this.onerror=null; this.src='https://agil.cuc.edu.co/imagenes_personas/empleado.png'" alt="">
						<div class="card-body">
							<h6 class="card-title" style='color: black; font-weight: bold;'>` +
				nombre_completo +
				` ${
					corresponding_author == 1
						? `<span class='fa fa-envelope'></span></span>`
						: ""
				} ` +
				`</h6>
							${
								afiliacion == "profesor"
									? `
							<p class="card-text" style="color: black; text-align: center;">` +
									  ` <strong>` +
									  Math.floor(data[0]["first_porcentage"]) +
									  "% Productividad Asignada" +
									  ` </strong><br>` +
									  Math.floor(data[0]["third_porcentage"]) +
									  "% a Bonificación" +
									  ` <br>` +
									  Math.floor(data[0]["second_porcentage"]) +
									  "% a Plan de Trabajo" +
									  ` <br>
								</p>
							`
									: "<br>"
							}

							<div class="dropdown" style="width: 100%">
								<button class="btn btn-secondary dropdown-toggle btn-block" type="button" id="dropdownMenuButton ` +
				id +
				` "data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acciones</button>
								<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
								<span class="dropdown-item btn_ver_info_autor` +
				id +
				`" ><span class='fa fa-eye'></span> Ver </span>
								
								${
									afiliacion == "profesor"
										? `
									<span class="dropdown-item btn_agregar_enlaces` +
										  id +
										  `" ><span class='fa fa-link'></span> Información del autor </span>
									<span class="dropdown-item btn_asig_porcent_autor` +
										  id +
										  `" ><span class='fa fa-eye'></span> Asignar Porcentajes </span>
									<span class="dropdown-item btn_comentarios_autor` +
										  id +
										  `" ><span class='fa fa-comments'></span> Comentarios </span>
									<span class="dropdown-item btn_articulos_autor` +
										  id +
										  `" ><span class='fa fa-eye'></span> Articulos </span>
								`
										: ""
								}
								${
									corresponding_author == 0 && afiliacion == "profesor"
										? `<span class="dropdown-item btn_corresponding_author_` +
										  id +
										  `" ><span class='fa fa-check'></span> Marcar Corresponding Author</span>`
										: corresponding_author == 1 && afiliacion == "profesor"
										? `<span class="dropdown-item btn_corresponding_author_` +
										  id +
										  `" ><span class='fa fa-minus-circle'></span> Desmarcar Corresponding Author</span>`
										: ""
								}
								${
									afiliacion == "profesor"
										? `
								<span class="dropdown-item btn_afiliacion_institucional` +
										  id +
										  `" ><span class='fa fa-university'></span> Afiliación Institucional </span>
								`
										: ""
								}
								<span class="dropdown-item btn_eliminar_autor` +
				id +
				`" ><span class='fa fa-trash'></span> Eliminar</span>
							</div>
						</div>
				</div>
			</div>
		`
		);

		$(`.btn_afiliacion_institucional${id}`).off("click");
		$(`.btn_afiliacion_institucional${id}`).click(async function () {
			id_persona_afil_inst = id;
			listar_afiliacion_institucional();
			$("#gestionarAfiliaciones").modal();
		});
		$(`.btn_agregar_enlaces${id}`).off("click");
		$(`.btn_agregar_enlaces${id}`).click(async function () {
			id_persona_afil_inst = id;
			//listar_afiliacion_institucional();
			$("#modal_agregar_enlaces").modal();
			pintar_campos_links_aut();
		});
		$(`.btn_corresponding_author_${id}`).off("click");
		$(`.btn_corresponding_author_${id}`).click(async function () {
			if(corresponding_author == 0){
				const valor = 1;
				cambiar_valor_corresponding(valor, id);
			}else{
				let valor = 0;
				cambiar_valor_corresponding(valor, id);
			}
		});
		$(`.btn_articulos_autor${id}`).off("click");
		$(`.btn_articulos_autor${id}`).click(async function () {
			$("#modal_articulos_autor").modal("show");
			let data = { nombre_completo, id };
			listar_articulos_suscritos(id);
			id_autor_bon = id;
			agregar_articulos_cumplidos(id);
		});
		$(`.btn_eliminar_autor${id}`).off("click");
		$(`.btn_eliminar_autor${id}`).click(function () {
			deshabilitar({ id: id_autor, title: "¿Eliminar autor?", tabla: "bonificaciones_autores" }, async () => {
				if(id_afiliacion == "profesor"){
					autores_profesores = await listar_autor_porTipo(id_solicitud, id_afiliacion);
					pintar_autores();
				}else if(id_afiliacion == "estudiante"){
					autores_estudiantes = await listar_autor_porTipo(id_solicitud, id_afiliacion);
					pintar_autores();
				}else if(id_afiliacion == "externo"){
					autores_externos = await listar_autor_porTipo(id_solicitud, id_afiliacion);
					pintar_autores();
				}
			});
		});
		$(`.btn_ver_info_autor${id}`).off("click");
		$(`.btn_ver_info_autor${id}`).click(function () {
			ver_detalle_autor_bonificaciones(id);
			id_autor_bon = id;
		});
		$(`.btn_comentarios_autor${id}`).off("click");
		$(`.btn_comentarios_autor${id}`).click(async function () {
			$("#modal_comentario_porcentaje").modal("show");
			id_autor_bon = id;
			id_porcentaje = id;
			data = await obtener_porcentaje();
			if(data){
				$("#modal_comentario_porcentaje textarea[name='comment']").val(data[0].comentario)
			}else{
				$("#modal_comentario_porcentaje textarea[name='comment']").val("");
			}
		});
		$(`.btn_asig_porcent_autor${id}`).off("click");
		$(`.btn_asig_porcent_autor${id}`).click(function () {
			$("#modal_asignar_porcentajes").modal("show");
			id_porcentaje = id;
			validar_porcentajes();
		});
	});
};

const cambiar_valor_corresponding = (valor, id) => {
	consulta_ajax(
		`${ruta}cambiar_valor_corresponding`,
		{ valor, id, id_afiliacion, id_solicitud},
		async (resp) => {
			let { titulo, mensaje, tipo } = resp;
			if (tipo == "success") {
				MensajeConClase(mensaje, tipo, titulo); 
				autores_profesores = await listar_autor_porTipo(id_solicitud, id_afiliacion);
				pintar_autores();
			} else {
				MensajeConClase(mensaje, tipo, titulo);
			}
		}
	);
}

const validar_porcentajes = async () => {
	data = await obtener_porcentaje();
	if (data) {
		$("#modal_asignar_porcentajes input[name='first_porcentage']").val(
			data[0].first_porcentage
		);
		$("#modal_mod_porcentajes_dir input[name='first_porcentage_cp']").val(
			data[0].first_porcentage
		);
		$("#modal_asignar_porcentajes input[name='second_porcentage']").val(
			data[0].second_porcentage
		);
		$("#modal_mod_porcentajes_dir input[name='second_porcentage_cp']").val(
			data[0].second_porcentage
		);
		$("#modal_asignar_porcentajes input[name='third_porcentage']").val(
			data[0].third_porcentage
		);
		$("#modal_mod_porcentajes_dir input[name='third_porcentage_cp']").val(
			data[0].third_porcentage
		);
	} else {
		$("#modal_asignar_porcentajes input[name='first_porcentage']").val("");
		$("#modal_asignar_porcentajes input[name='second_porcentage']").val("");
		$("#modal_asignar_porcentajes input[name='third_porcentage']").val("");
		$("#modal_mod_porcentajes_dir input[name='first_porcentage_cp']").val("");
		$("#modal_mod_porcentajes_dir input[name='second_porcentage_cp']").val("");
		$("#modal_mod_porcentajes_dir input[name='third_porcentage_cp']").val("");
	}
};

const obtener_porcentaje = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_porcentaje`;
		consulta_ajax(url, {id_persona: id_porcentaje, id_solicitud, id_afiliacion }, (resp) => {
			resolve(resp);
		});
	});
};

const listar_autor_porTipo = (id_solicitud) => {
	return new Promise((resolve) => {
		let url = `${ruta}listar_autor_porTipo`;
		consulta_ajax(url, { id_solicitud, id_afiliacion }, (resp) => {
			resolve(resp);
		});
	});
};

$("#lista_paises").focusout((e) => {
	//const option = $(`#dataRecursos option[value='${e.target.value}']`);
	//pintar_informacion_general();
	Cargar_parametro_buscado(307, ".cbxpaises", "Seleccione Procesador");
});

const suma_porcentajes = () => {
	return new Promise((resolve) => {
		let url = `${ruta}suma_porcentajes`;
		consulta_ajax(url, { id_articulo: id_solicitud }, (resp) => {
			resolve(resp);
		});
	});
};

const deshabilitar = (data, callback) => {
	let { title, id, tabla } = data;
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
		closeOnCancel: true,
	},
	function (isConfirm) {
		if (isConfirm) {
			consulta_ajax(`${ruta}deshabilitar`, { id, tabla }, (resp) => {
				let { tipo, mensaje, titulo } = resp;
				if (tipo == "success") {
					listar_autor_porTipo(id_solicitud, id_afiliacion);
					callback();
					swal.close();
				} else MensajeConClase(mensaje, tipo, titulo);
			});
		}
	});
};

const listar_afiliacion_institucional = () => {
	resp = [];
	consulta_ajax(`${ruta}obtener_afiliaciones_institucionales`, {id_solicitud, id_persona_afil_inst, id_afiliacion }, (resp) => {
		let num = 0;
			$(`#tabla_afiliacion_institucional tbody`)
				.off("click", "tr td")
				.off("click", "tr td .eliminar")
				.off("dblclick", "tr td")
				.off("click", "tr td .editar");
		const myTable = $("#tabla_afiliacion_institucional").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data: resp,
			columns: [
				{ render: () => ++num },
				{ data: "nombre_inst" },
				{ data: "pais" },
				{
					defaultContent:
						"<span class='btn btn-default eliminar' title='Eliminar'><span class='fa fa-trash' style='color:#6e1f7c'></span></span>" 
						//+ "<span class='btn btn-default editar' title='Editar'><span class='fa fa-edit' style='color:#2E79E5'></span></span>",
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
	
		$("#tabla_afiliacion_institucional tbody").on("click","tr td .eliminar", function () {
			let {id} = myTable.row($(this).parent()).data();
			actualizar_afiliaciones_institucionales('Eliminar',id);
			listar_afiliacion_institucional();
		});
	
		$("#tabla_afiliacion_institucional tbody").on("click","tr td .editar", function () {
			let {id} = myTable.row($(this).parent()).data();
			actualizar_afiliaciones_institucionales("Actualizacion", id);
			listar_afiliacion_institucional();
		});
	});
};
const pintar_afiliaciones_institucionales = (id_persona) => {
	resp = [];
	consulta_ajax(`${ruta}obtener_afiliaciones_institucionales`, {id_solicitud, id_persona_afil_inst: id_persona, id_afiliacion }, (resp) => {
		let num = 0;
		const myTable = $("#table_afiliaciones").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data: resp,
			columns: [
				{ render: () => ++num },
				{ data: "nombre_inst" },
				{ data: "pais" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
	});
};

const listar_tipos_escrituras = () => {
	resp = [];
	let num = 0;
	$(`#tabla_tipo_escritura_art tbody`)
		.off("click", "tr td")
		.off("click", "tr td .eliminar")
		.off("dblclick", "tr td")
	const myTable = $("#tabla_tipo_escritura_art").DataTable({
		destroy: true,
		processing: true,
		searching: false,
		data: data_tipos_escrituras,
		columns: [
			{ render: () => ++num },
			{ data: "title" },
			{
				defaultContent:
					"<span class='btn btn-default eliminar' title='Eliminar'><span class='fa fa-trash' style='color:#6e1f7c'></span></span>",
			},
		],
		language: get_idioma(),
		dom: "Bfrtip",
		buttons: [],
	});
	$("#tabla_tipo_escritura_art tbody").on("click","tr td .eliminar",function () {
		let data = myTable.row($(this).parent()).data();
		index = data_tipos_escrituras.findIndex((element) => {
			return element.categoria == data.categoria;
		});
		data_tipos_escrituras.splice(index, 1);
		listar_tipos_escrituras();
		}
	);
};

const guardar_afiliaciones_institucionales = (institucion, pais) => {
	consulta_ajax(
		`${ruta}guardar_afiliacion_institucional`,
		{ id_solicitud, institucion, pais, id_persona_afil_inst , id_afiliacion},
		async (resp) => {
			let { titulo, mensaje, tipo } = resp;
			if (tipo == "success") {
				MensajeConClase(mensaje, tipo, titulo);
				//listar_afiliacion_institucional;
			} else {
				MensajeConClase(mensaje, tipo, titulo);
			}
		}
	);
}


const verificar_firma_por_id = () => {
	return new Promise((resolve) => {
		let url = `${ruta}verificar_firma_por_id`;
		consulta_ajax(url, {id_solicitud}, (resp) => {
			resolve(resp);
		});
	});
}

const obtener_ultimo_estado = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_ultimo_estado`;
		consulta_ajax(url, {id_solicitud}, (resp) => {
			resolve(resp);
		});
	});
}

const obtener_id_parametro = (id, val_bus) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_id_parametro`;
		consulta_ajax(url, {id, val_bus}, (resp) => {
			resolve(resp);
		});
	});
}

const cambiar_estado = async () => {
	let {id_estado} = await obtener_ultimo_estado();
	if(id_estado == 'Bon_Sol_Rev_Aprob'){
		id_cambio_estado = {'aprobado' : 'Bon_Sol_Aprob_Aux_Pub', 'negado' : 'Bon_Sol_Neg_Aux_Pub', 'devolver' : 'Bon_Sol_Dev_Aux_Pub'};
	}else if(id_estado == 'Bon_Sol_Aprob_Aux_Pub'){
		id_cambio_estado = {'aprobado' : 'Bon_Sol_Aprob_Direct_Pub', 'negado' : 'Bon_Sol_Neg_Direct_Pub', 'devolver' : 'Bon_Sol_Dev_Direct_Pub'};
	}else if ((id_estado == "Bon_Sol_Env")) {
		id_cambio_estado = {'aprobado' : 'Bon_Sol_Rev_Aprob', 'negado' : 'Bon_Sol_Rev_Nega', 'devolver' : 'Bon_Sol_Rev_Dev'};
	}
	return id_cambio_estado
}

const actualizar_afiliaciones_institucionales = (tipo_act, id) => {
	consulta_ajax(`${ruta}actualizar_afiliaciones_institucionales`, {tipo_act, id}, async (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}
const modificar_articulos_cumplidos = (id) => {
	consulta_ajax(`${ruta}modificar_articulos_cumplidos`, {id}, async (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const obtener_lista_requerimiento_bon = (id_bon, tipo_gestion) => {
	consulta_ajax(`${ruta}obtener_lista_requerimiento_bon`, {id_bon, tipo_gestion}, (resp) => {
		let num = 0;
			$(`#tabla_validacion_bon tbody`)
				.off("click", "tr td")
				.off("dblclick", "tr td")
				.off("click", "tr td .cumple")
				.off("click", "tr td .no_cumple");
		const myTable = $("#tabla_validacion_bon").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			bPaginate: false,
			data: resp,
			columns: [
				{ render: () => ++num },
				{ data: "valor" },
				{ data: "id_respuesta" },
				{ data: "comentario" },
				{
					render: (data, type, { id_respuesta }, meta) => {
						let datos =
							!id_respuesta
								? '<span title="Aprobar" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn btn-default cumple" style="color:#5cb85c"></span><span title="Rechazar" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn btn-default no_cumple" style="color:#d9534f"></span>'
								: id_respuesta == "Aprobado" ? '<span title="Rechazar" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-down btn btn-default no_cumple" style="color:#d9534f"></span>' : '<span title="Aprobar" data-toggle="popover" data-trigger="hover" class="fa fa-thumbs-up btn btn-default cumple" style="color:#5cb85c"></span>';
						return datos;
					},
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		$("#tabla_validacion_bon tbody").on("click","tr td .cumple", async function () {
			let data = myTable.row($(this).parent().parent()).data();
			let datos =  await obtener_id_parametro('304', 'Ans_Bon_Si');
			//if(id_estado == "Bon_Sol_Rev_Aprob") tipo_gestion = "Gest_Aux_Public";
			id_solicitud = id_bon;
			agregar_comentario('Agregue un comentario', 'input', 'Por favor agregue un comentario', false, data.id, datos[0].id, tipo_gestion);
		});
		$("#tabla_validacion_bon tbody").on("click","tr td .no_cumple", async function () {
			let data = myTable.row($(this).parent().parent()).data();
			let datos = await obtener_id_parametro("304", "Ans_Bon_No");
			id_solicitud = id_bon;
			agregar_comentario('Agregue un comentario', 'input', 'Por favor agregue un comentario', true, data.id, datos[0].id, tipo_gestion);
		});
	});
}

const listar_requerimientos_bon = (id_bon, filtrar) => {
	consulta_ajax(`${ruta}listar_respuestas_requerimientos`, {id_solicitud: id_bon, filtrar}, (resp) => {
		let num = 0;
			$(`#tabla_listar_respuestas_bon tbody`)
				.off("click", "tr td")
				.off("dblclick", "tr td");
		const myTable = $("#tabla_listar_respuestas_bon").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data: resp,
			columns: [
				{ render: () => ++num },
				{ data: "requerimiento" },
				{ data: "tipo_gestion" },
				{ data: "respuesta" },
				{ data: "comentario" },
				{ data: "nombre_completo" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
	});
}

const guardar_respuesta_req_bon = (id_pregunta, id_respuesta, comentario, tipo_gestion) => {
	consulta_ajax(
		`${ruta}almacenar_respuestas_requerimientos`,
		{ id_solicitud, id_pregunta, id_respuesta, comentario, tipo_gestion },
		async (resp) => {
			let { titulo, mensaje, tipo } = resp;
			if (tipo != "success") {
				MensajeConClase(mensaje, tipo, titulo);
			} else {
				obtener_lista_requerimiento_bon(id_solicitud, id_estado = tipo_gestion);
			}
		}
	);
};

const agregar_comentario = (titulo, tipo, msg, requerido, id_pregunta, id_respuesta, tipo_gestion) => {
	swal(
		{
			title: titulo,
			text: msg,
			type: tipo,
			html: msg,
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Aceptar!",
			cancelButtonText: "Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
		},
		function (mensaje) {
			if (mensaje === false) return false;
			if (mensaje === "" && requerido == true) {
				swal.showInputError("Debe Ingresar un comentario.!");
			} else {
				guardar_respuesta_req_bon(id_pregunta, id_respuesta, mensaje, tipo_gestion);
				swal.close();
				return false;
			}
		}
	);
};

const guardar_gestion_requerimiento = (titulo, tipo, msg, estado, requerido, datos) => {

	swal({
		title: titulo,
		text: msg,
		type: tipo,
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Entiendo!",
		cancelButtonText: "No, cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true,
	},
	function (mensaje) {
		if (mensaje === false) return false;
		if (mensaje === "" && requerido == true) {
			swal.showInputError("Debe Ingresar un comentario.!");
		} else {
			let comentario = mensaje;
			consulta_ajax(`${ruta}guardar_gestion_requerimiento`, { id_solicitud, estado, mensaje, datos }, async (resp) => {
				let { titulo, mensaje, tipo } = resp;
				if (tipo != "success") {
					MensajeConClase(mensaje, tipo, titulo);
				} else {
					obtener_lista_requerimiento_bon(id_solicitud);
					MensajeConClase(mensaje, tipo, titulo);
					$("#modal_gestionar_solicitud_bonificaciones").modal("hide");
					$("#modal_informacion_liquidacion").modal("hide");
					enviar_correo_estado(estado, id_solicitud, comentario);
				}
				//swal.close();
				listar_publicaciones();
				return false;
			});
		}
	});
}

const obtener_porcentajes_firma = () => {
	consulta_ajax(`${ruta}obtener_porcentajes_firma`, {id_solicitud}, (resp) => {
		let num = 0;
			$(`#tabla_autores_firma tbody`)
				.off("click", "tr td")
				.off("dblclick", "tr td")
		const myTable = $("#tabla_autores_firma").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data: resp,
			columns: [
				{ render: () => ++num },
				{ data: "nombre_completo" },
				{
					data: "first_porcentage",
				},
				{
					data: "second_porcentage",
				},
				{
					data: "third_porcentage",
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
		//myTable.column(4).visible(false);
	});
}

let recibir_archivos = () => {
  Dropzone.options.Subir = {
    url: `${ruta}recibir_archivos`, //se especifica cuando el form no tiene el aributo action, por de fault toma la url del action en el formulario
    method: "post", //por defecto es post se puede poner get, put, etc.....
    withCredentials: false,
    parallelUploads: 10, //Cuanto archivos subir al mismo tiempo
    uploadMultiple: false,
    maxFilesize: 1000, //Maximo Tama�o del archivo expresado en mg
    paramName: "file", //Nombre con el que se envia el archivo a nivel de parametro
    createImageThumbnails: true,
    maxThumbnailFilesize: 1000, //Limite para generar imagenes (Previsualizacion)
    thumbnailWidth: 154, //Medida de largo de la Previsualizacion
    thumbnailHeight: 154, //Medida alto Previsualizacion
    filesizeBase: 1000,
    maxFiles: 10, //si no es nulo, define cu�ntos archivos se cargaRAN. Si se excede, se llamar� el EVENTO maxfilesexceeded.
    params: {}, //Parametros adicionales al formulario de envio ejemplo {tipo:"imagen"}
    clickable: true,
    ignoreHiddenFiles: true,
    acceptedFiles: "image/*, .pdf, .docx, .doc", //EJEMPLO PARA PDF WORD ETC ,application/pdf,.psd,.DOCX",
    acceptedMimeTypes: null, //Ya no se utiliza paso a ser AceptedFiles
    autoProcessQueue: false, //True sube las imagenes automaticamente, si es false se tiene que llamar a myDropzone.processQueue(); para subirlas

    error: function (response) {
      if (!response.xhr) {
        MensajeConClase("Ningun archivo fue cargado, Solo se permite cargar archivos con formato.\n gif,jpg,jpeg,png,csv!", "info", "Oops!");
      }
    },
    success: function (file, response) {
      let { mensaje, tipo, titulo } = JSON.parse(response)
      if (tipo === 'success') {
        MensajeConClase(mensaje, tipo, titulo);
      } else if (tipo === 'info') {
        MensajeConClase(mensaje, tipo, titulo);
      }
      $("#modal_otros_Adjuntos").modal('hide');
    },

    init: function () {
      num_archivos = 0;
      myDropzone = this;
      this.on("addedfile", function (file) {
        num_archivos++;
      });
      this.on("removedfile", function (file) {
        num_archivos--;
      });
      myDropzone.on("complete", function (file) {
        myDropzone.removeFile(file);
        cargados++;
      });
      myDropzone.on("processing", function (file) {
        this.options.params = { id_solicitud: id_solicitud }
      });
      myDropzone.on("maxfilesexceeded", function (file) {
        MensajeConClase(`Ya has llegado al número máximo de archivos a subir.\n Solo se permite subir ${this.options.maxFiles} archivo(s)`, "info", "Oops!");
      });
    },
    autoQueue: true,
    addRemoveLinks: true, //Habilita la posibilidad de eliminar/cancelar un archivo. Las opciones dictCancelUpload, dictCancelUploadConfirmation y dictRemoveFile se utilizan para la redacci�n.
    previewsContainer: null, //define d�nde mostrar las previsualizaciones de archivos. Puede ser un HTMLElement liso o un selector de CSS. El elemento debe tener la estructura correcta para que las vistas previas se muestran correctamente.
    capture: null,
    dictDefaultMessage: "Arrastra los archivos aqui para subirlos",
    dictFallbackMessage: "Su navegador no soporta arrastrar y soltar para subir archivos.",
    dictFallbackText: "Por favor utilize el formuario de reserva de abajo como en los viejos timepos.",
    dictFileTooBig: "La imagen revasa el tamaño permitido ({{filesize}}MiB). Tam. Max : {{maxFilesize}}MiB.",
    dictInvalidFileType: "No se puede subir este tipo de archivos.",
    dictResponseError: "Server responded with {{statusCode}} code.",
    dictCancelUpload: "Cancel subida",
    dictCancelUploadConfirmation: "¿Seguro que desea cancelar esta subida?",
    dictRemoveFile: "Eliminar archivo",
    dictRemoveFileConfirmation: null,
    dictMaxFilesExceeded: "Se ha excedido el numero de archivos permitidos.",
  };
}

const firmar_porcentajes = (titulo, msg, tipo) => {
	swal({
		title: titulo,
		text: msg,
		type: tipo,
		html: msg,
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Aceptar!",
		cancelButtonText: "Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true,
	},
	function (isConfirm) {
		if (isConfirm) {
			consulta_ajax(`${ruta}firmar_porcentajes`, {id_solicitud}, async (resp) => {
				let { titulo, mensaje, tipo } = resp;
				if (tipo != "success") {
					MensajeConClase(mensaje, tipo, titulo);
				} else {
					$("#modal_firmar_bonificaciones").modal("hide");
					MensajeConClase(mensaje, tipo, titulo);
					swal.close();
				}
				listar_publicaciones();
				return false;
			});
		} else {
			swal.close();
		}
	});
};

const admin_gest = (title, text, type, accion, url) => {
	// $("#msj_aprobar").append('');
	// $("#msj_aprobar").append(`
	// 	<div class="alert alert-info">
	// 		<p class="text-justify">
	// 			<strong>
	// 				<span class="fa fa-warning"></span> Aviso:
	// 			</strong>
	// 			Si está de acuerdo con la información consignada, por favor <a id="Aproba_gest"> haga clic aquí</a>
	// 		</p>
	// 	</div>
	// `);
	
	//$("#Aproba_gest").click(() => {
		swal(
		{
			title: title,
			text: text,
			type: type,
			//html: '¿Está seguro que desea dar su visto bueno? Tenga en cuenta que esta acción no se puede reversar',
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Aceptar!",
			cancelButtonText: "Cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
		},
		function (isConfirm) {
			if (isConfirm) {
				consulta_ajax(`${ruta}`+url, {id_solicitud, accion}, async (resp) => {
					let { titulo, mensaje, tipo } = resp;
					if (tipo != "success") {
						MensajeConClase(mensaje, tipo, titulo);
					} else {
						if(url == "visto_bueno_ges"){
							enviar_correo_estado(accion == "VoBo" ? "Bon_Sol_Gest_Aprob" : "Bon_Sol_Gest_Deni", id_solicitud, "");
						}
						$("#modal_firmar_bonificaciones").modal("hide");
						MensajeConClase(mensaje, tipo, titulo);
						//swal.close();
					}
					listar_publicaciones();
					return false;
				});
			} else {
				swal.close();
			}
      	});
	//});
}

const listar_comites = (tipo_modulo = 'bonificaciones') => {
	consulta_ajax(`${ruta}obtener_comites`, { tipo_modulo }, (resp) => {
		let i = 0;
		$("#table_comite tbody")
			.off("dblclick", "tr")
			.off("click", "tr")
			.off("click", "tr td:nth-of-type(1)")
			.off("click", "tr .ver_sol")
			.off("click", "tr .ver_fin")
			.off("click", "tr .enviar")
			.off("click", "tr .en_proceso")
			.off("click", "tr .terminar")
			.off("click", "tr .modificar");
		const myTable = $("#table_comite").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{data: 'ver'},
				{data: 'nombre'},
				{data: 'fecha_cierre'},
				{data: 'descripcion'},
				{data: 'cantidad'},
				{data: 'nombre_completo'},
				{data: 'estado_comite'},
				{data: 'accion'},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});
		
		if (tipo_modulo == 'comite_bonificaciones') myTable.column(7).visible(false);
		//EVENTOS DE LA TABLA ACTIVADOS
		$("#table_comite tbody").on("click", "tr", function () {
			$("#table_articulos_suscritos_aut tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#table_comite tbody").on("click", "tr .modificar", function () {
			let { id } = myTable.row($(this).parent().parent()).data();
			id_comite = id;
			$("#modal_modificar_comite").modal();
		});
		
		$("#table_comite tbody").on("click", "tr .ver_sol", function () {
			let { id } = myTable.row($(this).parent().parent()).data();
			id_comite = id;
			listar_solicitudes_por_comite(tipo_modulo);
			$("#modal_list_bonificaciones").modal();
		});

		$("#table_comite tbody").on("click", "tr .ver_fin", function () {
			let { id } = myTable.row($(this).parent().parent()).data();
			id_comite = id;
			listar_solicitudes_por_comite(tipo_modulo);
			$("#modal_list_bonificaciones").modal();
		});

		$("#table_comite tbody").on("click", "tr .en_proceso", function () {
			let { id } = myTable.row($(this).parent().parent()).data();
			id_comite = id;
			listar_solicitudes_por_comite(tipo_modulo);
			$("#modal_list_bonificaciones").modal();
		});

		$("#table_comite tbody").on("click", "tr .enviar", function () {
			let { id } = myTable.row($(this).parent().parent()).data();
			mostrar_mensaje('¡Enviar!', '¿Realmente desea enviar a comité?', 'warning', id, 'enviar_comite');
		});

		$("#table_comite tbody").on("click", "tr .terminar", function () {
			let { id } = myTable.row($(this).parent().parent()).data();
			mostrar_mensaje('¡Terminar!', '¿Realmente desea terminar el comité?', 'warning', id, 'terminar_comite');
		});
	});
};

const mostrar_mensaje = (titulo, msg, tipo, datos, url) => {
	swal({
		title: titulo,
		text: msg,
		type: tipo,
		html: msg,
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Aceptar!",
		cancelButtonText: "Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true,
	},
	function (isConfirm) {
		if (isConfirm) {
			consulta_ajax(`${ruta}`+url, { datos }, async (resp) => {
				let { titulo, mensaje, tipo } = resp;
				if (tipo != "success") {
					MensajeConClase(mensaje, tipo, titulo);
				} else {
					$("#modal_firmar_bonificaciones").modal("hide");
					MensajeConClase(mensaje, tipo, titulo);
					if(url == 'enviar_comite') listar_comites();
					if (url == "terminar_comite") listar_comites();
					swal.close();
				}
				listar_publicaciones();
				return false;
			});
		} else {
			swal.close();
		}
	});
}

const listar_solicitudes_por_comite = (tipo_modulo) => {
	consulta_ajax(`${ruta}listar_solicitudes_por_comite`, { id_comite }, (resp) => {
		let i = 0;
		$("#tabla_list_bon tbody")
			.off("dblclick", "tr")
			.off("click", "tr")
			.off("click", "tr .btn_ver")
			.off("click", "tr .aceptar")
			.off("click", "tr .rechazar")
			.off("click", "tr td:nth-of-type(1)");

		const myTable = $("#tabla_list_bon").DataTable({
			destroy: true,
			searching: false,
			processing: true,
			data: resp,
			columns: [
				{ data: "ver" },
				{ data: "tipo" },
				{ data: "nombre_completo" },
				{ data: "fecha_registra" },
				{ data: "estado" },
				{ data: "acciones"}
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});
		if (tipo_modulo == "bonificaciones") myTable.column(0).visible(false);
		if (tipo_modulo == "bonificaciones") myTable.column(5).visible(false);

		$("#tabla_list_bon tbody").on("click", "tr .btn_ver", async function () {
			let { id_bonificacion } = myTable.row($(this).parent().parent()).data();
			id_solicitud = id_bonificacion;
			pintar_informacion_principal();
			pintar_tipos_de_escrituras();
			listar_autores_bonificacion(id_bonificacion);
			$("#modal_detalle_bonificacion").modal();
		});

		$("#tabla_list_bon tbody").on("click", "tr .aceptar", async function () {
			let { id_bonificacion } = myTable.row($(this).parent().parent()).data();
			guardar_respuesta_consejo_acad('Aprob_Cons_Acad', id_bonificacion, tipo_modulo);
		});

		$("#tabla_list_bon tbody").on("click", "tr .rechazar", async function () {
			let { id_bonificacion } = myTable.row($(this).parent().parent()).data();
			guardar_respuesta_consejo_acad('Neg_Cons_Acad', id_bonificacion, tipo_modulo);
		});

	});
};

const obtener_solicitudes = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}listar_publicaciones`;
		consulta_ajax(url, {id}, (resp) => {
			resolve(resp);
		});
	});
}

const verificar_identificacion = (identificacion) => {
	return new Promise((resolve) => {
		let url = `${ruta}verificar_identificacion`;
		consulta_ajax(url, {identificacion}, (resp) => {
			resolve(resp);
		});
	});
}

const obtener_resultado_comite = () => {
	consulta_ajax(`${ruta}obtener_resultado_comite`, { id_comite }, (resp) => {
		$("#tabla_comite tbody").off("dblclick", "tr").off("click", "tr");
			let i = 0;
		const myTable = $("#tabla_comite").DataTable({
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
				{ data: "nombre_completo" },
				{ data: "estado" },
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

	});
}

const guardar_respuesta_consejo_acad  = (estado, id_solicitud, tipo_modulo) => {
	consulta_ajax(`${ruta}guardar_respuesta_consejo_acad`, {estado, id_solicitud}, async (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo != "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			MensajeConClase(mensaje, tipo, titulo);
			swal.close();
		}
		listar_solicitudes_por_comite(tipo_modulo);
		return false;
	});
}

const enviar_solicitud = () => {
	consulta_ajax(`${ruta}enviar_solicitud`, {id_solicitud}, async (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo != "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			$("#modal_registro_solicitud_bonificacion").modal("hide");
			MensajeConClase(mensaje, tipo, titulo);
			listar_publicaciones();
			enviar_correo_estado("Bon_Sol_Regis", id_solicitud, "");
			//swal.close();
		}
	});
}

const buscar_pais = (valor_buscado) => {
	consulta_ajax(`${ruta}buscar_pais`, { valor_buscado }, (resp) => {
		$("#table_list_countries tbody")
			.off("dblclick", "tr")
			.off("click", "tr")
			.off("click", "tr td:nth-of-type(1)")
			.off("click", "tr .asignar");
			let i = 0;
		myTable = $("#table_list_countries").DataTable({
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
					data: "valor",
				},
				{
					defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Pais" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" ></span>',
				},
			],
			language: idioma,
			dom: "Bfrtip",
			buttons: [],
		});

		$("#table_list_countries tbody").on("click", "tr .asignar", function () {
			let { id, valor } = myTable.row($(this).parent().parent()).data();
			//Listar_valor_Parametros(id);
			$("#modal_listar_paises").modal("hide");
			idpais = id;
			$("#nombre_pais").val(id + " - " + valor);
		});
	});
};

const guardar_links_aut = () => {
	let data = new FormData(document.getElementById("form_agregar_enlaces"));
	data.append("id_solicitud", id_solicitud);
	data.append("id_persona", id_persona_afil_inst);
	enviar_formulario(`${ruta}guardar_links_aut`, data, async (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo != "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			$("#modal_agregar_enlaces").modal("hide");
			$("#form_agregar_enlaces").get(0).reset();
			MensajeConClase(mensaje, tipo, titulo);
			$("#autores").html("");
			pintar_autores();
		}
	});
	
}

const guardar_guardar_instituciones = () => {
	let data = new FormData(document.getElementById("form_agregar_institucion"));
	enviar_formulario(`${ruta}guardar_guardar_instituciones`, data, async (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo != "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else {
			$("#modal_agregar_enlaces").modal("hide");
			$("#form_agregar_enlaces").get(0).reset();
			MensajeConClase(mensaje, tipo, titulo);
			$("#autores").html("");
			pintar_autores();
		}
	});
	
}
const pintar_campos_links_aut = () => {
	consulta_ajax(`${ruta}obtener_links_aut`, { id_solicitud, id_persona_afil_inst }, (resp) => {
		$("#modal_crear_filtros select[name='id_estado']").val("");
		$("#modal_agregar_enlaces input[name='hindex_scholar__bon']").val(resp[0].hi_scholar);
		$("#modal_agregar_enlaces input[name='hindex_scopus__bon']").val(resp[0].hi_Scopus);
		$("#modal_agregar_enlaces input[name='ResearchGate__bon']").val(resp[0].research_gate);
		$("#modal_agregar_enlaces select[name='categoria_minciencias__bon']").val(resp[0].categ_minciencias);
		$("#modal_agregar_enlaces select[name='departamento_autor__bon']").val(resp[0].departamento);
		$("#modal_agregar_enlaces input[name='url_cvlac']").val(resp[0].url_cvlac);
		$("#modal_agregar_enlaces input[name='url_google_scholar']").val(resp[0].url_google_scholar);
		$("#modal_agregar_enlaces input[name='url_research_gate']").val(resp[0].url_research_gate);
		$("#modal_agregar_enlaces input[name='url_red_acad_disc']").val(resp[0].url_red_acad_disc);
		$("#modal_agregar_enlaces input[name='url_mendeley']").val(resp[0].url_mendeley);
		$("#modal_agregar_enlaces input[name='url_Gruplac']").val(resp[0].gruplac);
		$("#modal_agregar_enlaces input[name='url_Publons']").val(resp[0].publons);

	});
}

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

		$('#tabla_personas tbody').on('click', 'tr', function() {
			$('#tabla_personas tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_personas tbody').on('click', 'tr span.asignar', function() {
			let { id, fullname } = myTable.row($(this).parent().parent()).data();
			id_persona = id;
			$('#modal_elegir_persona').modal('hide');
			$('#s_persona').html(fullname);
			listar_actividades(id);
		});

		$('#tabla_personas tbody').on('dblclick', 'tr', function() {
			let { id, fullname } = myTable.row($(this)).data();
			id_persona = id;
			$('#modal_elegir_persona').modal('hide');
			$('#s_persona').html(fullname);
			listar_actividades(id);
		});
	});
};

const pintar_respuestas_ot_as = () => {
	consulta_ajax(`${ruta}pintar_respuestas_ot_as`, { id_solicitud, id_persona_afil_inst }, (resp) => {
		resp.forEach(element => {
			let name = `#answer_${element.id_pregunta}_${element.id_respuesta}`;
			$(name).prop("checked", true);
			cambiar_respuesta(element.id_pregunta, element.id_respuesta);
		});
	});
}

const pintar_evidencias_existentes = () => {
	consulta_ajax(
		`${ruta}pintar_evidencias_existentes`,
		{ id_solicitud, id_persona_afil_inst },
		(resp) => {
			resp.forEach((element) => {
				let name = `#modal_evidencias_bonificaciones input[name='${element.tipo_dato}']`;
				$(name).val(resp[0].dato);
			});
		}
	);
}

const obtener_informacion_principal = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_informacion_principal`;
		consulta_ajax(url, { id_solicitud }, (resp) => {
			resolve(resp);
		});
	});
}
const obtener_escrituras = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_tipos_escrituras`;
		consulta_ajax(url, { id_solicitud }, (resp) => {
			resolve(resp);
		});
	});
}

const pintar_informacion_principal = async () => {
	let data = await obtener_informacion_principal();
	$(".titulo_arti").html(data[0].publicacion);
	$(".fecha_registra").html(data[0].fecha_registra);
	$(".persona_registro").html(data[0].nombre_completo);
	$(".issn_ver_bon").html(data[0].issn);
	$(".cuartil_scopus_ver_bon").html(data[0].cuartil_scopus);
	$(".cuartil_wos_ver_bon").html(data[0].cuartil_wos);
	$(".proyecto_ver_bon").html(data[0].proyecto_index);
	if (data[0].ubicacion_proyecto == "index") {
		$(".proyecto_ver_bon").html(data[0].proyecto_index);
	} else if (data[0].ubicacion_proyecto == "manual") {
		$(".proyecto_ver_bon").html(data[0].titulo_proyecto);
	}
	$(".revista_ver_bon").html(data[0].revista);
	$(".url_index_wos").html(`<a href="${data[0].url_indexacion_wos}" target="_blank">ver</a>`);
	$(".url_index_scopus").html(`<a href="${data[0].url_indexacion_scopus}" target="_blank">ver</a>`);
	$(".fecha_publicacion").html(data[0].fecha_publicacion);
	$(".año_indexacion").html(data[0].ano_indexacion);
	$(".doi_ver_bon").html(data[0].doi);
	$(".lineas_ver_bon").html(data[0].linea);
	$(".sublineas_ver_bon").html(data[0].sublinea);
	$(".editorial_ver_bon").html(data[0].editorial);
	$(".urlinea_ver_bon").html(`<a href="${data[0].url_articulo}" target="_blank">ver</a>`);

}

const listar_respuestas_otros_aspectos = () => {
	consulta_ajax(`${ruta}listar_respuestas_otros_aspectos`, { id_solicitud }, (data) => {
		let i = 0;
		const myTable = $('#table_ver_otros_aspectos').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ 
					render: function (data, type, full, meta) {
						i++;
						return i;
					}
				},
				{ data: 'pregunta' },
				{ data: 'respuesta' },
				{ data: 'comentario' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});
	});
}

const pintar_tipos_de_escrituras = (tabla = "table_categorias_bonificacion") => {
	consulta_ajax(`${ruta}obtener_tipos_escrituras`, { id_solicitud }, (data) => {
		let x = 0;
		const myTable = $(`#${tabla}`).DataTable({
			destroy: true,
			processing: true,
			searching: false,
			bPaginate: false,
			bInfo: false,
			data,
			columns: [
				{
					render: function (data, type, full, meta) {
						x++;
						return x;
					},
				},
				{ data: "tipo_escritura" },
				{ data: "nombre_completo" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
	});
}

const pintar_evidencias_ver = () => {
	consulta_ajax(`${ruta}pintar_evidencias_ver`, { id_solicitud }, (data) => {
		let i = 0;
		const myTable = $("#table_ver_evidence").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{
					render: (data, type, { nombre_archivo }, meta) =>
						`<a style="text-decoration: none;" target="_blank" class="form-control" href="${ruta_documentos}evidencias/${nombre_archivo}" style="width: 100%;"> Ver</a>`
				},
				{ data: "comentario" },
				{ data: "nombre_completo" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
	});
}

const obtener_articulos_cumplidos = () => {
	consulta_ajax(`${ruta}obtener_articulos_cumplidos`, { id_solicitud, autor: id_autor_bon }, (data) => {
		let i = 0;
		const myTable = $("#table_art_cump").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{
					render: function (data, type, full, meta) {
						i++;
						return i;
					},
				},
				{ data: "cantidad_autor" },
				{ data: "cuartil" },
				{ data: "titulo_articulo" },
				{ data: "link" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
	});
}

const obtener_articulos_suscritos = () => {
	consulta_ajax(`${ruta}obtener_articulos_suscritos`, {id: id_autor_bon }, (resp) => {
		let num = 0;
			$(`#table_art_susc tbody`)
				.off("click", "tr td")
				.off("dblclick", "tr td")
		const myTable = $("#table_art_susc").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data: resp,
			columns: [
				{
					render: function (resp, type, full, meta) {
                        num++;
                        return num;
                    }
				},
				{ 
					data: 'estado_final' 
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
	});
}

const pintar_porcentajes_totales = () => {
	consulta_ajax(`${ruta}pintar_porcentajes_totales`, {id_solicitud }, (resp) => {
		let num = 0;
			$(`#table_ver_porcentaje tbody`)
				.off("click", "tr td")
				.off("dblclick", "tr td")
		const myTable = $("#table_ver_porcentaje").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data: resp,
			columns: [
				{ data: "identificacion" },
				{ data: "nombre_completo" },
				{ data: "first_porcentage_cp" },
				{ data: "second_porcentage_cp" },
				{ data: "third_porcentage_cp" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
	});
}

const listar_sublineas_bon = async (id) => {
	let sublineas = await obtener_sublineas_inv(id);
	pintar_datos_combo(sublineas, ".cbxsublineainv", "Seleccione la sublinea de investigación");
};

const pintar_info_articulo = () => {
	consulta_ajax(`${ruta}obtener_informacion_principal`, {id_solicitud }, async (resp) => {
		let datos = resp[0];
		$("#modal_principal_bonificaciones input[name='id__doi']").val(datos.doi);
		$("#modal_principal_bonificaciones input[name='editorial']").val(datos.editorial);
		$("#modal_principal_bonificaciones input[name='issn_bon']").val(datos.issn);
		$("#modal_principal_bonificaciones input[name='articulo_link']").val(datos.url_articulo);
		if(datos.ubicacion_proyecto == "index"){
			$("#modal_principal_bonificaciones input[name='name_proyect__bon']").val(datos.proyecto_index);
		}else if(datos.ubicacion_proyecto == "manual"){
			$("#modal_principal_bonificaciones input[name='name_proyect__bon']").val(datos.titulo_proyecto);
		}
		$("#modal_principal_bonificaciones input[name='revista']").val(datos.revista);
		$("#modal_principal_bonificaciones input[name='nombre_articulo_bon']").val(datos.publicacion);
		$("#modal_principal_bonificaciones input[name='url_indexacion_scopus']").val(datos.url_indexacion_scopus);
		$("#modal_principal_bonificaciones input[name='url_indexacion_wos']").val(datos.url_indexacion_wos);
		
		
		$("#modal_principal_bonificaciones select[name='lineaInv__bon']").val(datos.id_linea);
		listar_sublineas_bon(datos.id_linea);
		$("#modal_principal_bonificaciones select[name='SublineaInv__bon']").val(datos.id_sublinea_inv);
		$("#modal_principal_bonificaciones select[name='cuartil_scopus']").val(datos.id_cuartil_scopus);
		$("#modal_principal_bonificaciones select[name='cuartil_wos']").val(datos.id_cuartil_wos);


		$("#modal_principal_bonificaciones input[name='date__initial']").val(datos.fecha_publicacion);
		$("#modal_principal_bonificaciones input[name='date__indexing']").val(datos.ano_indexacion);
		id_proyecto_bon = datos.id_proyecto;
		revista = { id: datos.id_revista, valor: null, valorx: null, valory: null };
		$("#nombre_articulo_bon").val(datos.publicacion).attr("data-art_id", datos.id_titulo_articulo);

		let escrituras = await obtener_escrituras();
		escrituras.forEach(async element => {
			const title = await traer_registro_id(element.id);
			data_tipos_escrituras.push({
				categoria: element.id,
				title: title.valor,
				tipo: title.valory,
			});
			listar_tipos_escrituras();
		});
	});
}

const listar_actividades = (persona) => {
	let num = 0;
	consulta_ajax(`${ruta}listar_tipos_solicitud`, { persona }, (data) => {
		$(`#tabla_actividades tbody`)
			.off('click', 'tr')
			.off('click', 'tr span.asignar')
			.off('click', 'tr span.quitar')
			.off('click', 'tr span.config')
			.off('dblclick', 'tr');
		const myTable = $('#tabla_actividades').DataTable({
			destroy: true,
			processing: true,
			searching: true,
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

		$('#tabla_actividades tbody').on('click', 'tr span.quitar', function() {
			const { asignado, id } = myTable.row($(this).parent()).data();
			quitar_actividad(asignado, id);
		});

		$('#tabla_actividades tbody').on('click', 'tr span.config', function() {
			const { asignado, id } = myTable.row($(this).parent()).data();
			actividad_selec = asignado;
			$('#modal_elegir_estado').modal();
			listar_estados_adm(asignado);
		});

		const asignar_actividad = (asignado, id) => {
			consulta_ajax(`${ruta}asignar_actividad`,{ id, persona: id_persona, asignado },
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
					text: "Tener en cuenta que al desasignarle esta actividad al usuario no podrá visualizar ninguna solicitud de este tipo.",
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
						consulta_ajax(`${ruta}quitar_actividad`, { id, persona: id_persona, asignado },
							({ mensaje, tipo, titulo }) => {
								listar_actividades(id_persona);
							}
						);
						swal.close();
					} else MensajeConClase(mensaje, tipo, titulo);
				}
			);
		};
	});
};

const listar_estados_adm = (actividad) => {
	const desasignar =
		'<span class="btn btn-default desasignar" title="Desasignar Estado"><span class="fa fa-toggle-on" style="color: #5cb85c"></span></span> ';
	const asignar =
		'<span class="btn btn-default asignar" title="Asignar Estado"><span class="fa fa-toggle-off"></span></span> ';
	const notificar =
		'<span class="btn btn-default notificar" title="Activar Notificación"><span class="fa fa-bell-o"></span></span> ';
	const no_notificar =
		'<span class="btn btn-default no_notificar" title="Desactivar Notificación"><span class="fa fa-bell red"></span></span> ';
	consulta_ajax(`${ruta}listar_estados_adm`, { actividad, persona: id_persona }, (data) => {
		$(`#tabla_estados_adm tbody`)
			.off("click", "tr")
			.off("click", "tr span.asignar")
			.off("click", "tr span.desasignar")
			.off("click", "tr span.no_notificar")
			.off("click", "tr span.notificar")
			.off("click", "tr span.gestionar")
			.off("dblclick", "tr");
		const myTable = $("#tabla_estados_adm").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data,
			columns: [
				{ data: "parametro" },
				{ data: "nombre" },
				{
					render: (data, type, { asignado, notificacion }, meta) => {
						return asignado
							? notificacion == 1
								? desasignar + no_notificar
								: desasignar + notificar
							: asignar;
					},
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		$("#tabla_estados_adm tbody").on("click", "tr", function () {
			$("#tabla_estados_adm tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$("#tabla_estados_adm tbody").on("click", "tr span.asignar", function () {
			const { estado } = myTable.row($(this).parent()).data();
			asignar_estado(estado, actividad_selec, id_persona, 't_estado');
		});

		$("#tabla_estados_adm tbody").on("click", "tr span.desasignar", function () {
			const { asignado, estado } = myTable.row($(this).parent()).data();
			quitar_estado(estado, actividad_selec, id_persona, asignado);
		});

		$("#tabla_estados_adm tbody").on("click", "tr span.notificar", function () {
			const { estado } = myTable.row($(this).parent()).data();
			activar_notificacion(estado, actividad_selec, id_persona);
		});

		$("#tabla_estados_adm tbody").on("click", "tr span.gestionar", function () {
			const { asignado } = myTable.row($(this).parent()).data();
			$("#modal_elegir_departamento").modal();
			listar_departamentos_adm(asignado);
		});

		$("#tabla_estados_adm tbody").on(
			"click",
			"tr span.no_notificar",
			function () {
				const { estado } = myTable.row($(this).parent()).data();
				desactivar_notificacion(estado, actividad_selec, id_persona);
			}
		);

		const activar_notificacion = (estado, actividad, persona) => {
			consulta_ajax(
				`${ruta}activar_notificacion`,
				{ estado, actividad, persona },
				({ mensaje, tipo, titulo }) => listar_estados_adm(actividad)
			);
		};

		const desactivar_notificacion = (estado, actividad, persona) => {
			consulta_ajax(`${ruta}desactivar_notificacion`, { estado, actividad, persona },
				({ mensaje, tipo, titulo }) => listar_estados_adm(actividad)
			);
		};

		const asignar_estado = (estado, actividad, persona) => {
			consulta_ajax(`${ruta}asignar_estado`,{ estado, actividad, persona },
				({ mensaje, tipo, titulo }) => {
					MensajeConClase(mensaje, tipo, titulo);
					listar_estados_adm(actividad);
				}
			);
		};

		const quitar_estado = (estado, actividad, persona, id) => {
			consulta_ajax(`${ruta}quitar_estado`,{ estado, actividad, persona, id },
				({ mensaje, tipo, titulo }) => {
					MensajeConClase(mensaje, tipo, titulo);
					listar_estados_adm(actividad);
				}
			);
		};
	});
};

const obtener_datos_solicitud = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_datos_solicitud`;
		consulta_ajax(url, {id}, (resp) => {
			resolve(resp);
		});
	});
}

const obtener_autores = (afiliacion='estudiante') => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_autores`;
		consulta_ajax(url, { id_solicitud, afiliacion }, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_autores_internacionales = () => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_autores_internacionales`;
		consulta_ajax(url, { id_solicitud }, (resp) => {
			resolve(resp);
		});
	});
};

const consultas_liquidaciones = (valory) => {
	return new Promise((resolve) => {
		let url = `${ruta}consultas_liquidaciones`;
		consulta_ajax(url, { valory, id_solicitud }, (resp) => {
			resolve(resp);
		});
	});
};

const obtener_info_autor_liq = (id_persona, identificacion, tipo) => {
	return new Promise((resolve) => {
		let url = `${ruta}obtener_info_autor_liq`;
		consulta_ajax(url, { id_solicitud, id_persona, identificacion, tipo }, (resp) => {
			resolve(resp);
		});
	});
};

const mostrarDato_Liquidacion = async (id) => {

	let datos = await obtener_datos_solicitud(id);
	$(".titulo_arti_liq").html(datos[0].titulo_articulo);
	$(".revista_liq").html(datos[0].revista);
	$(".issn_liq").html(datos[0].issn);
	$(".link_scopus_liq").html(`<a href="${datos[0].url_indexacion_scopus}" target="_blank">ver</a>`);
	$(".link_wos_liq").html(`<a href="${datos[0].url_indexacion_wos}" target="_blank">ver</a>`);
	$(".rev_scopus_liq").html(datos[0].id_cuartil_scopus);
	$(".rev_wos_liq").html(datos[0].id_cuartil_wos);

	if(datos[0].cuartil_final && datos[0].categoria_final){
		$("#modal_informacion_liquidacion select[name='cuart_liq_fin']").val(datos[0].cuartil_final);
		$("#modal_informacion_liquidacion select[name='cat_liq_fin']").val(datos[0].categoria_final);
	}
	
	let author_est = await obtener_autores();
	let author_ext = await obtener_autores_internacionales();
	let correspondencia = await consultas_liquidaciones('Correspondencia');
	let visibilidad = await consultas_liquidaciones('visibilidad_inst');
	let problema = await consultas_liquidaciones('solucion_prob');
	
	$(".div_coaut_est_liq_bon").addClass("oculto");
	if(author_est.length == 0){
		$(".div_coaut_est_liq_bon").addClass("oculto");
		$("#respuesta_coaut_est").append(`<span></span> NO `);
	}else if(author_est.length != 0){
		$(".div_coaut_est_liq_bon").removeClass("oculto");
		$("#respuesta_coaut_est").append(`<span></span> SI `);
		pintar_coautores();
	}
	
	if(author_ext.length == 0){
		$(".div_coaut_ext_liq_bon").addClass("oculto");
		$(".aut_inter_liq").html('NO');
	}else if(author_ext.length != 0){
		$(".aut_inter_liq").html('SI');
		$(".div_coaut_ext_liq_bon").removeClass("oculto");;
		pintar_coautores_ext();
	}
	
	correspondencia.forEach((element) => {
		
		if(element.respuesta == 'Aprobado' && element.tipo_gestion == 'Direct_Public'){
			$(".correspondencia_liq").html("SI");
		}else if(element.respuesta != 'Aprobado' && element.tipo_gestion == 'Direct_Public'){
			$(".correspondencia_liq").html("NO");
		}
	});

	visibilidad.forEach((element) => {
		
		if(element.respuesta == 'Aprobado' && element.tipo_gestion == 'Direct_Public'){
			$(".visibilidad_inst_liq").html("SI");
		}else if(element.respuesta != 'Aprobado' && element.tipo_gestion == 'Direct_Public'){
			$(".visibilidad_inst_liq").html("NO");
		}
	});

	problema.forEach((element) => {
		
		if(element.respuesta == 'Aprobado' && element.tipo_gestion == 'Direct_Public'){
			$(".solucion_prob_liq").html("SI");
		}else if(element.respuesta != 'Aprobado' && element.tipo_gestion == 'Direct_Public'){
			$(".solucion_prob_liq").html("NO");
		}
	});
}

const pintar_coautores = (afiliacion='estudiante') => {
	consulta_ajax(`${ruta}obtener_autores`, { id_solicitud, afiliacion }, (data) => {
		let x = 0;
		const myTable = $(`.table_cout_est_liq_bon`).DataTable({
			destroy: true,
			processing: true,
			searching: false,
			bPaginate: false,
			bInfo: false,
			data,
			columns: [
				{ data: "identificacion" },
				{ data: "nombre_completo" },
				{ data: "programa" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
	});
}

const pintar_coautores_ext = () => {
	consulta_ajax(`${ruta}obtener_autores_internacionales`, { id_solicitud }, (data) => {
		let x = 0;
		const myTable = $(`.table_cout_ext_liq_bon`).DataTable({
			destroy: true,
			processing: true,
			searching: false,
			bPaginate: false,
			bInfo: false,
			data,
			columns: [
				{ data: "identificacion" },
				{ data: "nombre_completo" },
				{ data: "institucion" },
				{ data: "pais" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
	});
}

const obtener_autores_liquidacion = () => {
	consulta_ajax(`${ruta}obtener_autores_liquidacion`, { id_solicitud }, (data) => {
		$(`#table_autores_liq_bon, #table_ver_liq_bon tbody`)
			.off("click", "tr")
			.off("click", "tr span.ver")
			.off("dblclick", "tr");
		const myTable = $(`#table_autores_liq_bon, #table_ver_liq_bon`).DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data: data[0],
			columns: [
				{ data: "ver" },
				{ data: "identificacion" },
				{ data: "nombre_completo" },
				{ data: "liquidacion" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});
		
		$("#table_autores_liq_bon, #table_ver_liq_bon tbody").on("click", "tr span.ver", async function () {
			const data = myTable.row($(this).parent()).data();
			let datos = await obtener_info_autor_liq(data.id_autor, data.identificacion, 'profesor');
			$("#modal_info_autor_liq").modal();
			$(".nombre_autor_liq").html(datos[0].identificacion + " - " + datos[0].nombre_completo);
			$(".categ_invest_liq").html(datos[0].categoria_minciencias);
			$(".cargo_aut_liq").html(datos[0].cargo);
			$(".porc_pdt").html(datos[0].porc_pdt ? datos[0].porc_pdt + "%" : 'n/a');
			$(".porc_bon").html(datos[0].porc_bon ? datos[0].porc_bon + "%" : 'n/a');
			$(".base_liquid").html(valor_peso(datos[0].base_liquidacion));
			$(".bonific_base_liq").html(valor_peso(datos[0].bonificacion_base));
			$(".coaut_est_liq").html(valor_peso(datos[0].coautoria_estudiante));
			$(".aport_vis_liq").html(valor_peso(datos[0].visibilidad));
			$(".sol_prob_liq").html(valor_peso(datos[0].solucion_problema));
			$(".total_liq").html(valor_peso(datos[0].total_autor));
			id_porcentaje_cp = data.id_autor;
			id_porcentaje = data.id_autor;
			id_afiliacion = "profesor";
		});
		total_liquidacion = data[1];
		$("#liquidacion_personas").html("");
		$("#liquidacion_personas").append(`<strong> Liquidación: </strong> El total de la liquidación para los autores es ${valor_peso(data[1])}`);
	});
}
const obtener_personas_liquidacion = (id_estado, estado) => {
	consulta_ajax(`${ruta}obtener_personas_liquidacion`, { id_solicitud, id_estado, estado }, (data) => {
		$(`#table_gestores_liq_bon tbody`)
			.off("click", "tr")
			.off("click", "tr span.ver")
			.off("dblclick", "tr");
		const myTable = $(`#table_gestores_liq_bon`).DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data: data[0],
			columns: [
				//{ data: "ver" },
				{ data: "identificacion" },
				{ data: "nombre_completo" },
				{ data: "liquidacion" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		// $("#table_autores_liq_bon tbody").on("click", "tr span.ver", async function () {
		// 	const data = myTable.row($(this).parent()).data();
		// 	let datos = await obtener_info_autor_liq(data.id_autor, data.identificacion);
		// 	$("#modal_info_autor_liq").modal();
		// 	$(".nombre_autor_liq").html(datos[0].identificacion + " - " + datos[0].nombre_completo);
		// 	$(".categ_invest_liq").html(datos[0].categoria_minciencias);
		// 	$(".cargo_aut_liq").html(datos[0].cargo);
		// 	$(".porc_pdt").html(datos[0].porc_pdt ? datos[0].porc_pdt + "%" : 'n/a');
		// 	$(".porc_bon").html(datos[0].porc_bon ? datos[0].porc_bon + "%" : 'n/a');
		// 	$(".base_liquid").html(valor_peso(datos[0].base_liquidacion));
		// 	$(".bonific_base_liq").html(valor_peso(datos[0].bonificacion_base));
		// 	$(".coaut_est_liq").html(valor_peso(datos[0].coautoria_estudiante));
		// 	$(".aport_vis_liq").html(valor_peso(datos[0].visibilidad));
		// 	$(".sol_prob_liq").html(valor_peso(datos[0].solucion_problema));
		// 	$(".total_liq").html(valor_peso(datos[0].total_autor));
		// });

		$("#liquidacion_personas").append(`<strong> Liquidación: </strong> El total de la liquidación para los gestores es ${valor_peso(data[1])}`);
	});
}

const obtener_director_liquidacion = (id_estado, estado) => {
	consulta_ajax(`${ruta}obtener_personas_liquidacion`, { id_solicitud, id_estado, estado }, (data) => {
		$(`#table_directores_liq_bon tbody`)
			.off("click", "tr")
			.off("click", "tr span.ver")
			.off("dblclick", "tr");
		const myTable = $(`#table_directores_liq_bon`).DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data: data[0],
			columns: [
				//{ data: "ver" },
				{ data: "identificacion" },
				{ data: "nombre_completo" },
				{ data: "liquidacion" },
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: [],
		});

		// $("#table_autores_liq_bon tbody").on("click", "tr span.ver", async function () {
		// 	const data = myTable.row($(this).parent()).data();
		// 	let datos = await obtener_info_autor_liq(data.id_autor, data.identificacion);
		// 	$("#modal_info_autor_liq").modal();
		// 	$(".nombre_autor_liq").html(datos[0].identificacion + " - " + datos[0].nombre_completo);
		// 	$(".categ_invest_liq").html(datos[0].categoria_minciencias);
		// 	$(".cargo_aut_liq").html(datos[0].cargo);
		// 	$(".porc_pdt").html(datos[0].porc_pdt ? datos[0].porc_pdt + "%" : 'n/a');
		// 	$(".porc_bon").html(datos[0].porc_bon ? datos[0].porc_bon + "%" : 'n/a');
		// 	$(".base_liquid").html(valor_peso(datos[0].base_liquidacion));
		// 	$(".bonific_base_liq").html(valor_peso(datos[0].bonificacion_base));
		// 	$(".coaut_est_liq").html(valor_peso(datos[0].coautoria_estudiante));
		// 	$(".aport_vis_liq").html(valor_peso(datos[0].visibilidad));
		// 	$(".sol_prob_liq").html(valor_peso(datos[0].solucion_problema));
		// 	$(".total_liq").html(valor_peso(datos[0].total_autor));
		// });

		$("#liquidacion_personas").append(`<strong> Liquidación: </strong> El total de la liquidación para el director es ${valor_peso(data[1])}`);
	});
}

const guardar_info_liquid_final = (cuar_liq_final, cat_liq_final) => {
	consulta_ajax(`${ruta}guardar_info_liquid_final`, {cuar_liq_final, cat_liq_final, id_solicitud}, (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
		} else if (tipo == "error") {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const liquidar_bonificacion = () => {
	consulta_ajax(`${ruta}liquidar_bonificacion`, {id_solicitud}, (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			obtener_autores_liquidacion();
		} else if (tipo == "error") {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const liquidar_bonificacion_gestor = (id_estado) => {
	consulta_ajax(`${ruta}liquidar_bonificacion_gestor`, {id_solicitud}, (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			$("#liquidacion_personas").html("");
			MensajeConClase(mensaje, tipo, titulo);
			obtener_personas_liquidacion(id_estado, 'profesor');
		} else if (tipo == "error") {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const liquidar_bonificacion_director = (id_estado) => {
	consulta_ajax(`${ruta}liquidar_bonificacion_director`, {id_solicitud}, (resp) => {
		let { titulo, mensaje, tipo } = resp;
		if (tipo == "success") {
			$("#liquidacion_personas").html("");
			MensajeConClase(mensaje, tipo, titulo);
			obtener_director_liquidacion(id_estado, 'director');
		} else if (tipo == "error") {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const save_new_project = () => {
	let formdata = new FormData(document.getElementById("form_create_new_project"));
	formdata.append("id_solicitud", id_solicitud);
	let datos = formDataToJson(formdata);
	consulta_ajax(`${ruta}save_new_project`, datos, (resp) => {
		let { titulo, mensaje, tipo, titulo_proyecto } = resp;
		if (tipo == "success") {
			MensajeConClase(mensaje, tipo, titulo);
			$("#form_create_new_project").get(0).reset();
			$("#modal_create_new_project").modal("hide");
			$("#modal_buscar_proyecto__bon").modal("hide");
			$("#modal_principal_bonificaciones input[name='name_proyect__bon']").val(titulo_proyecto);
		} else if (tipo == "error") {
			MensajeConClase(mensaje, tipo, titulo);
		}
	});
}

const gestionar_comites_masivo = (tipo) => {
	swal({
		title: "Atención.!",
		text: "¿Realmente desea realizar esta acción?, tenga en cuenta que esta acción no se puede reversar.",
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
			consulta_ajax(`${ruta}gestionar_comites_masivo`, {id_comite, tipo}, (resp) => {
				let { titulo, mensaje, tipo } = resp;
				if (tipo == "success") {
					MensajeConClase(mensaje, tipo, titulo);
					listar_solicitudes_por_comite(id_comite);
				} else if (tipo == "warning") {
					MensajeConClase(mensaje, tipo, titulo);
					listar_solicitudes_por_comite(id_comite);
				}
			});
			//swal.close();
		} else{
			swal.close();
		} 
	});
}

const consultar_info_solicitud = (id) => {
	return new Promise((resolve) => {
		let url = `${ruta}consultar_info_solicitud`;
		consulta_ajax(url, { id_solicitud: id }, (resp) => {
			data_solicitante = {nombre: resp[0].nombre_completo, correo: resp[0].correo};
			resolve(data_solicitante);
		});
	});
};

const consultar_notificaciones_personas = (estado) => {
	return new Promise((resolve) => {
		let url = `${ruta}consultar_notificaciones_personas`;
		consulta_ajax(url, { estado }, (resp) => {
			data_permisos_not = {nombre_not: resp[0].nombre_completo, correo_not: resp[0].correo};
			resolve(data_permisos_not);
		});
	});
};

const enviar_correo_estado = async (estado, id, motivo) => {
	let sw = false;
	let sw2 = false;
	await consultar_info_solicitud(id);
	await consultar_notificaciones_personas(estado);
	var { nombre, correo } = data_solicitante;
	var { nombre_not, correo_not } = data_permisos_not;
	let ser = `<a href="${server}index.php/publicaciones/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 3;
	let titulo = "";
	let nombre_solicitante = nombre;
	let mensaje = `Se informa que hay una solicitud realizada por ${nombre}, lista para ser gestionada por usted, a partir de este momento puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentra la solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
	let mensaje_not = `Se informa que hay una solicitud realizada por ${nombre}, lista para ser gestionada por usted, a partir de este momento puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentra la solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;

	switch (estado) {
		case "Bon_Sol_Creado":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Creada";
			mensaje = `Se informa que su solicitud ha sido creada con exito, a partir de este momento puede ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentra la solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
		break;
		case "Bon_Sol_Regis":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Registrada";
			mensaje = `Se le informa que su solicitud ha sido registrada con exito. <br><br>Mas informaci&oacuten en: ${ser}<br>`;
		break;
		case "Bon_Sol_Env":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Enviada";
			mensaje = `Se informa que su solicitud fue enviada con exito, a partir de este momento su solicitud será gestionada.<br> <br><br>Mas informaci&oacuten en: ${ser}<br>`;
		break;
		case "Bon_Sol_Rev_Aprob":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Revisada";
			mensaje = `Se le informa que su solicitud ha sido revisada por el area encargada.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
		break;
		case "Bon_Sol_Rev_Dev":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Devuelta";
			mensaje = `Se informa que su solicitud ha sido devuelta por el siguiente motivo: <br> Motivo: ${motivo}.<br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Bon_Sol_Rev_Nega":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Negada";
			mensaje = `Se informa que su solicitud ha sido negada por el siguiente motivo: <br> motivo: ${motivo}. <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Bon_Sol_Aprob_Aux_Pub":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Revisada";
			mensaje = `Se informa que su solicitud ha sido aprobada revisada  por el area encargada.<br> <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Bon_Sol_Neg_Aux_Pub":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Negada";
			mensaje = `Se informa que su solicitud ha sido negada por el siguiente motivo: <br> motivo: ${motivo}<br> <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Bon_Sol_Dev_Aux_Pub":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Devuelta";
			mensaje = `Se informa que su solicitud ha sido devuelta por el siguiente motivo: <br> Motivo: ${motivo}<br> <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Bon_Sol_Aprob_Direct_Pub":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Revisada";
			mensaje = `Se informa que su solicitud ha sido aprobada revisada  por el area encargada.<br> <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Bon_Sol_Neg_Direct_Pub":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Negada";
			mensaje = `Se informa que su solicitud ha sido negada por el siguiente motivo: <br> motivo: ${motivo}<br> <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Bon_Sol_Dev_Direct_Pub":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Devuelta";
			mensaje = `Se informa que su solicitud ha sido devuelta por el siguiente motivo: <br> Motivo: ${motivo}<br> <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Bon_Sol_Gest_Aprob":
			tipo = 1;
			sw = true;
			titulo = "Solicitud aprobada por Gestor";
			mensaje = `Se informa que su solicitud fue gestionada y aprobada por el gestor.<br> <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Bon_Sol_Gest_Deni":
			tipo = 1;
			sw = true;
			titulo = "Solicitud denegada por Gestor";
			mensaje = `Se informa que su solicitud fue gestionada y denegada por el gestor, por el siguiente motivo: <br> Motivo: ${motivo}<br> <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Bon_Sol_Revi_Gestor":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Revisada por Gestor";
			mensaje = `Se informa que su solicitud fue gestionada y revisada por el gestor. <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Bon_Sol_Cons_Acad":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Enviada a Consejo";
			mensaje = `Se informa que su solicitud fue gestionada y enviada a consejo academico. <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Aprob_Cons_Acad":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Aprobada por Consejo";
			mensaje = `Se informa que su solicitud fue gestionada y aprobada por consejo academico. <br><br>Mas informaci&oacuten en ${ser}`;
		break;
		case "Neg_Cons_Acad":
			tipo = 1;
			sw = true;
			titulo = "Solicitud Negada por Consejo";
			mensaje = `Se informa que su solicitud fue gestionada y negada por consejo academico. <br><br>Mas informaci&oacuten en ${ser}`;
		break;

	}
	//console.log(nombre_not, correo_not);
	if(nombre_not, correo_not){
		sw2 = true;
		tipo = 1;
	}
	if (sw){
		enviar_correo_personalizado("blab", mensaje, correo, nombre, "Publicaciones", `Publicaciones AGIL - ${titulo}`, "ParCodAdm", tipo );
		if(sw2){
			mensaje = mensaje_not;
			correo = correo_not;
			nombre = nombre_not;
			titulo = "Asignación"
			enviar_correo_personalizado("blab", mensaje, correo, nombre, "Publicaciones", `Publicaciones AGIL - ${titulo}`, "ParCodAdm", tipo );
		}

	} 
};
