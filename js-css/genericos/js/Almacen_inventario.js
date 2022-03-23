let tipo_modulo = '';
let idarticulo = 0;
let op = 0;
let art_codigo = 0;
let ruta = null;
let articulos_compras = []
let articulos_compras_guardados = []
let id_articulo_compra = ''
let id_pjefe = 0
let correo_jefe = null

$(document).ready(() => {
	server = Traer_Server();
	cargar_bodegas();
	//LLama a la función guardar_Articulo al enviar el formulario.
	$('#Agregar_Articulos').submit(() => {
		guardar_articulo();
		return false;
	});

	$("#btn_agregar_unidad").click(() => {
		swal({
			title: 'Agregar Unidades',
			text: "",
			type: "input",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Agregar!",
			cancelButtonText: "Regresar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true,
			inputPlaceholder: `Digite el nombre de las nuevas unidades`
		}, function (mensaje) {

			if (mensaje === false)
				return false;
			if (mensaje === "") {
				swal.showInputError(`Por favor ingrese el nombre de las unidades a crear`);
			} else guardar_nuevas_unidades(mensaje);
		});
	});

	$('#modificar_Articulo').submit(() => {
		modificar_articulo(idarticulo);
		return false;
	});

	$("#btnadministrar").click(() => {
		listar_unidades();
		$("#modal_administrar").modal();
	})

	$("#restar_articulos").submit(() => {
		const cant = $("#txtrest_cantidad").val();
		const obs = $("#txtrest_observacion").val();
		(cant != '' && obs != '') ? cambiar_cant_articulo(idarticulo, cant, obs, op) : MensajeConClase("Por favor complete todos los campos", "info", "Oops...");
		return false;
	});
	//Asigna valor de 1 a la variable para identificar que abre el modal de articulos desde el modulo de inventario
	$("#btnabrir").click(() => {
		cargar_bodegas();
		guardar = 1;
		$("#Agregar_Articulos").get(0).reset();
		$("#modalArticulos").modal();
		$("#div_articulo").fadeOut('fast');
		$('.div_inv').fadeIn('fast');
		$('.div_sol').fadeOut('fast');
	});

	$("#btnmodificar").click(() => {
		idarticulo 
			? cargar_bodegas(() => get_info_articulo())
			: MensajeConClase("Seleccione un artículo.", "info", "Oops...")
	});

	$("#administrar_inventario").click(() => listar_categorias());

	$("#btnLimpiar_articulos").click(() => {
		listar_articulos();
		$("#categoria_filtro").val('');
		$("#bodega_filtro").val('');
	});

	$('#Bod_Ase').click(async function () {
		articulos_compras_guardados = []
		$("#nav_compras li").removeClass("active");
		$(this).addClass("active");
		pintar_codigos_sap('Bod_Ase')
		let datos = await traer_articulos_a_comprar('Bod_Ase')
		articulos_compras = datos
		listar_articulos_compras()
	});
  
	$('#Bod_Caf').click(async function () {
		articulos_compras_guardados = []
		$("#nav_compras li").removeClass("active");
		$(this).addClass("active");
		pintar_codigos_sap('Bod_Caf')
		let datos = await traer_articulos_a_comprar('Bod_Caf')
		articulos_compras = datos
		listar_articulos_compras()
	});
  
	$('#Bod_Fer').click(async function () {
		articulos_compras_guardados = []
		$("#nav_compras li").removeClass("active");
		$(this).addClass("active");
		pintar_codigos_sap('Bod_Fer')
		let datos = await traer_articulos_a_comprar('Bod_Fer')
		articulos_compras = datos
		listar_articulos_compras()
	});
  
	$('#Bod_Pap').click(async function () {
		articulos_compras_guardados = []
		$("#nav_compras li").removeClass("active");
		$(this).addClass("active");
		pintar_codigos_sap('Bod_Pap')
		let datos = await traer_articulos_a_comprar('Bod_Pap')
		articulos_compras = datos
		listar_articulos_compras()
	});

	$("#btncompras").click(async function () {
		articulos_compras_guardados = []
		let li = $("#nav_compras li:first");
		let primer_item = $("#nav_compras li:first")[0].id
		let datos = await traer_articulos_a_comprar(primer_item)
		articulos_compras = datos
		listar_articulos_compras()
		pintar_codigos_sap(primer_item)
		$('#jefe_compras').val('');
		id_pjefe = 0
		correo_jefe = null
		$("#nav_compras li").removeClass("active");
		$(li).addClass("active");
		$("#modal_compras").modal();
	});

	$("#enviar_compra").click(() => {
		let cod_sap = $('#cbx_cod_sap').val();
		enviar_compra(cod_sap)
	})

	$('.buscar_jefe_compra').click(() => {
		$('#txt_dato_buscar').val('');
		buscar_jefe();
		$('#modal_buscar_jefe').modal();
	});

	$('#form_buscar_persona').submit((e) => {
		e.preventDefault();
		let dato = $('#txt_dato_buscar').val();
		buscar_jefe(dato);
	});
});

//Trae los artículos a la tabla #tabla_articulos
const listar_articulos = (accion = 1, categoria = '%', bodega = '%') => {
	idarticulo = 0;
	$('#tabla_articulos tbody').off('dblclick', 'tr');
	$('#tabla_articulos tbody').off('click', 'tr');
	$('#tabla_articulos tbody').off('click', 'tr td:nth-of-type(1)');
	const myTable = $("#tabla_articulos").DataTable({
		destroy: true,
		ajax: {
			url: `${server}index.php/almacen_inventario_control/Listar_articulos`,
			dataType: "json",
			data: { accion, categoria, bodega, tipo_modulo, },
			type: "post",
			dataSrc: ({ sw, data }) => {
				sw 	? $("#textAlerta").html("<span class='fa fa-bell fa-2x red tiembla' style='-webkit-animation: tiembla .5s infinite;'></span> Existen artículos con poca existencia <a onclick='listar_articulos(2);' style='text-decoration: none;' class='pointer'><b>Ver</b></a>").show() 
					: $("#textAlerta").html("");
				return data ? data : Array();
			},
		},
		processing: true,
		columns: [
			{ data: "ver" },
			{ data: "codigo" },
			{ data: "nombre" },
			{ data: "valor" },
			{ data: "bodega" },
			{ data: "cantidad" },
			{ data: "unidades" },
			{ data: "stock" },
			{ data: "categoria" },
			{ data: "gestion" },
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: get_botones(),
	});
	myTable.column(3).visible(false);

	myTable.column(3).visible(false);

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_articulos tbody').on('click', 'tr', function () {
		const data = myTable.row(this).data();
		const { id, codigo } = data;
		art_codigo = codigo;
		idarticulo = id;
		$("#tabla_articulos tbody tr").removeClass("warning");
		$(this).addClass("warning");
	});


	$('#tabla_articulos tbody').on('dblclick', 'tr', function () {
		const data = myTable.row(this).data();
		detalles_articulo(data);
	});

	$('#tabla_articulos tbody').on('click', 'tr td:nth-of-type(1)', function () {
		const data = myTable.row($(this).parent()).data();
		detalles_articulo(data);
	});

	$("#btnreporte_inventario").click(() => {
		const categoria = $('#categoria_filtro').val();
		const bodega = $('#bodega_filtro').val();
		listar_articulos(1, categoria, bodega);
	});

  $("#form_agregar_articulos").submit((e) => {
    e.preventDefault()
    agregar_articulo_compra();
  });
};


const listar_historial = art => {
	const myTable = $("#tblhistorial").DataTable({
		destroy: true,
		ajax: {
			url: `${server}index.php/almacen_control/listar_historial`,
			dataType: "json",
			data: {
				art
			},
			type: "post",
			dataSrc: json => json.length == 0 ? Array() : json.data,
		},
		processing: true,
		columns: [{
			data: "fecha"
		},
		{
			data: "c_anterior"
		},
		{
			data: "cantidad"
		},
		{
			data: "fullname"
		},
		{
			data: "observacion"
		},
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: get_botones(),
	});
};
const cargar_bodegas = (callback = () => {}) => {
	const combo = ".cbxbodega";
	$.ajax({
		url: `${server}index.php/almacen_inventario_control/listar_permisos_por_parametro`,
		dataType: "json",
		data: {
			vp_p: tipo_modulo,
		},
		type: "post",
		success: datos => {
			$(combo).html("").append("<option value=''>Seleccione Bodega</option>");
			datos.data.forEach(({ id, nombre, estado }) => {
				if (estado) {
					$(combo).append(`<option value=${id}>${nombre}</option>`);
				}
			});
			callback();
		},
		error: () => {
			console.log('Something went wrong', status, err);
		}
	});
}

const detalles_articulos_solicitud = ({
	code,
	nombre,
	marca,
	referencia,
	cantidad,
	observacion
}) => {
	$("#tbl_articulos_solicitud tbody tr").removeClass("warning");
	$(this).attr("class", "warning");
	$("#modal_detalle_articulo").modal();
	$(".info_art_cod").html(code);
	$(".info_art_nom").html(nombre);
	$(".info_art_can").html(cantidad);
	$(".info_art_mar").html(marca);
	$(".info_art_ref").html(referencia);
	$(".info_art_obs").html(observacion);
}

const guardar_articulo = () => {
	const data = new FormData(document.getElementById("Agregar_Articulos"));
	data.append('tipo', guardar);
	data.append('tipo_modulo', tipo_modulo);
	$.ajax({
		url: `${server}index.php/almacen_inventario_control/guardar_articulo`,
		type: "post",
		dataType: "json",
		data,
		cache: false,
		contentType: false,
		processData: false
	}).done(datos => {
		switch (datos) {
			case 'sin_session':
				close();
				break;
			case -1302:
				MensajeConClase("No tiene Permisos Para Realizar Esta Operaci&oacuten", "error", "Oops...");
				break;
			case 1:
				MensajeConClase("Artículo guardado existosamente", "success", "Proceso Exitoso!");
				$("#Agregar_Articulos").get(0).reset();
				listar_articulos();
				break;
			case 2:
				articulo = $('#Agregar_Articulos').serializeJSON();
				const {
					nombre,
					marca,
					referencia,
				} = art_sele;
				const {
					cantidad_art,
					observaciones,
				} = articulo;
				codigo = articulo.articulo;
				// Valida si existe el artículo ingresado
				const exist = articulos_sele.find(art => art.codigo === codigo);
				if (exist) {
					MensajeConClase("Si desea una mayor cantidad por favor modifique el artículo ya solicitado.", "info", `${nombre} ya ha sido registrado`);
				} else {
					// Si el artículo no existe se guardará en el array de artículos solicitados.
					articulos_sele.push({
						"id": arts,
						"codigo": codigo,
						"nombre_art": nombre,
						"cantidad_art": cantidad_art,
						"marca": marca,
						"referencia": referencia,
						"observaciones": observaciones,
					});
					arts++;
					MensajeConClase("", "success", "Artículo Guardado");
				}
				Listar_articulos_solicitados(articulos_sele);
				$("#Agregar_Articulos").get(0).reset();
				$("#cod_articulo").html("Seleccione Artículo");
				break;
			case 3:
				if ($("#input_codigo_orden").val() != '') {
					article = $('#Agregar_Articulos').serializeJSON();
					const nombre_art = $("#cod_articulo").html();
					const {
						cantidad_art,
						observaciones,
					} = article;
					let {
						codigo,
						marca,
						referencia,
					} = art_sele;
					articulos_sele.map((art, index) => {
						if (art.id == id_articulo_sele) {
							articulos_sele.splice(index, 1);
							return;
						}
					});
					articulos_sele.push({
						"id": id_articulo_sele,
						"codigo": codigo,
						"nombre_art": nombre_art,
						"cantidad_art": cantidad_art,
						"marca": marca,
						"referencia": referencia,
						"observaciones": observaciones,
					});
					arts++;
					MensajeConClase("", "success", "Artículo Modificado");
					Listar_articulos_solicitados(articulos_sele);
					$("#Agregar_Articulos").get(0).reset();
					$("#modalArticulos").modal('hide');
					return;
				} else {
					MensajeConClase("", "info", "");
				}
				break;
			case 4:
				traer_articulos_solicitud(idsolicitud, id_persona_solici_tabla);
				MensajeConClase("", "success", "Artículo modificado exitosamente");
				break;
			case 5:
				MensajeConClase("", "success", "Artículo modificado exitosamente!");
				traer_articulos_solicitud(idsolicitud, id_persona_solici_tabla);
				$("#Agregar_Articulos").get(0).reset();
				$("#modalArticulos").modal('hide');
				break;
			case 6:
				traer_articulos_solicitud(idsolicitud, id_persona_solici_tabla);
				MensajeConClase("", "success", "Artículo agregado exitosamente");
				$("#Agregar_Articulos").get(0).reset();
				$("#modalArticulos").modal('hide');
				break;
			case -1:
				MensajeConClase("Por favor digite una cantidad de Stock", "info", "Oops...");
				break;
			case -2:
				MensajeConClase("El Código del artículo debe ser de máximo 10 caracteres", "info", "Oops...");
				break;
			case -3:
				MensajeConClase("Este código ya existe", "info", "Oops...");
				break;
			case -4:
				MensajeConClase("Por favor digite una cantidad valida", "info", "Oops...");
				break;
			case -5:
				MensajeConClase("Por favor digite un nombre para el artículo", "info", "Oops...");
				break;
			case -6:
				MensajeConClase("por favor digite un valor para el artículo", "info", "Oops...");
				break;
			case -7:
				MensajeConClase("Por favor elija una Bodega", "info", "Oops...");
				break;
			case -8:
				MensajeConClase("Por favor elija una categoría", "info", "Oops...");
				break;
			case -9:
				MensajeConClase("Por favor elija una artículo", "info", "Oops...");
				break;
			case -10:
				MensajeConClase("Por favor elija una solicitud", "info", "Oops...");
				break;
			case -11:
				MensajeConClase("Por favor elija una solicitud para guardar el artículo", "info", "Oops...");
				break;
			case -12:
				MensajeConClase("En el momento no hay esta cantidad disponible en Almacen.", "info", "Lo sentimos!");
				break;
			case -13:
				MensajeConClase("Si desea una mayor cantidad por favor modifique el artículo ya solicitado.", "info", `El artículo ya ha sido registrado`);
				break;
			case -15:
				MensajeConClase("Por favor digite el nombre de las unidades del artículo.", "info", 'Ooops!');
				break;
			default:
				break;
		};
	});
};

const cambiar_cant_articulo = (art, cant, obs, op) => {
	$.ajax({
		url: `${server}index.php/almacen_inventario_control/cambiar_cant_articulo`,
		type: "post",
		dataType: "json",
		data: { art, cant, obs, op, },
	}).done(datos => {
		switch (datos) {
			case 'sin_session':
				close();
				break;
			case -1302:
				MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
				break;
			case -1:
				MensajeConClase("Seleccione un artículo", "info", "Oops...");
				break;
			case -2:
				MensajeConClase("Digite una cantidad", "info", "Oops...");
				break;
			case -3:
				MensajeConClase("Digite una Observación", "info", "Oops...");
				break;
			case -4:
				MensajeConClase("La cantidad debe ser menor o igual al Stock actual", "info", "Oops...");
				break;
			case 1:
				if (op === 'sum') {
					MensajeConClase("", "success", "Artículos agregados exitosamente");
				} else {
					MensajeConClase("", "success", "Artículos restados exitosamente");
				}
				listar_articulos();
				$("#restar_articulos").get(0).reset();
				$("#modal_restar_articulos").modal('hide');
				break;
			default:
				break;
		}
	})
}

const cantidad_disponible = id => {
	$.ajax({
		url: `${server}index.php/almacen_inventario_control/cantidad_disponible`,
		type: "post",
		dataType: "json",
		data: { id, },
	}).done(datos => {
		if (datos[0] >= 0) {
			MensajeConClase(`- ${datos[1]} Artículos solicitados\n- ${datos[0]} Artículos disponibles`, "", 'Disponibilidad del artículo');
		} else if (datos[0] == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
		} else if (datos == -1) {
			MensajeConClase("Por favor seleccione una solicitud.", "info", "Oops...");
		} else if (datos[0] == 'sin_session') {
			close();
		}
	});
}

const listar_permisos_por_parametro = (id_p, vp_p) => {
	$('#tabla_perfiles tbody').off('dblclick', 'tr').off('click', 'tr');
	const myTable = $("#tabla_perfiles").DataTable({
		destroy: true,
		ajax: {
			url: `${server}index.php/almacen_inventario_control/listar_permisos_por_parametro`,
			dataType: "json",
			type: "post",
			data: {
				vp_p, id_p, aux: 1,
			},
			dataSrc: json => json.length == 0 ? Array() : json.data,
		},
		processing: true,
		columns: [{
			data: "num"
		},
		{
			data: "nombre"
		},
		{
			data: "opciones"
		},
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: [],
	});
	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_perfiles tbody').on('click', 'tr', function () {
		$("#tabla_perfiles tbody tr").removeClass("warning");
		$(this).addClass("warning");
	});
};

const mostrar_perfiles = (vp_p, id_p) => {
	listar_permisos_por_parametro(vp_p, id_p);
	$("#Modal_perfiles").modal();
}

const gestionar_perfil = (id_s, vp_secundario, id_p, vp_principal, btn) => {
	const clases = $(`#btn${btn}`).attr('class');
	const accion = clases.includes('fa-toggle-on') ? ['removida', 'Remover'] : ['asignada', 'Asignar'];
	swal({
		title: 'Está Seguro?',
		text: `La categoría será ${accion[0]} al perfil que acaba de seleccionar`,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: `Si, ${accion[1]}!`,
		cancelButtonText: "No, Regresar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				$.ajax({
					url: `${server}index.php/almacen_inventario_control/gestionar_perfil`,
					type: "post",
					dataType: "json",
					data: { id_p, id_s, vp_principal, vp_secundario, },
				}).done(datos => {
					switch (datos) {
						case "sin_session":
							close();
							break;
						case "error":
							MensajeConClase("Ha ocurrido un error. Por favor comuniquese con el administrador.", "error", "Oops...");
							break;
						case 1:
							//MensajeConClase("Categoría asignada exitosamente", "success", "Proceso Exitoso!");
							swal.close();
							$(`#btn${btn}`).removeClass('fa-toggle-off').addClass('fa-toggle-on').css('color', 'green').attr('title', 'Quitar perfil');
							break;
						case 2:
							//MensajeConClase("Categoría removida exitosamente", "success", "Proceso Exitoso!");
							swal.close();
							$(`#btn${btn}`).removeClass('fa-toggle-on').addClass('fa-toggle-off').css('color', 'green').attr('title', 'Quitar perfil');
							break;
						case -1302:
							MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
							break;
						case -1:
							MensajeConClase("Por favor seleccione un perfil.", "info", "Oops...");
							break;
						case -2:
							MensajeConClase("Por favor seleccione una categoría.", "info", "Oops...");
							break;
						default:
							break;
					}
				});
			}
		});
}

const listar_categorias = () => {
	$('#tabla_categorias tbody').off('dblclick', 'tr').off('click', 'tr');
	const myTable = $("#tabla_categorias").DataTable({
		destroy: true,
		ajax: {
			url: `${server}index.php/almacen_inventario_control/listar_categorias`,
			dataType: "json",
			type: "post",
			dataSrc: json => json.length == 0 ? Array() : json.data,
		},
		processing: true,
		columns: [{
			data: "num"
		},
		{
			data: "nombre"
		},
		{
			data: "opciones"
		},
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: [],
	});
	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_categorias tbody').on('click', 'tr', function () {
		$("#tabla_categorias tbody tr").removeClass("warning");
		$(this).addClass("warning");
	});
};

const mostrar_restar_cantidad = id => {
	$(".t_sumar_restar").html('Restar Artículos');
	$(".sumar_restar").html(' Restar');
	$("#modal_restar_articulos").modal();
	idarticulo = id;
	op = 'res';
}

const mostrar_agregar_cantidad = id => {
	$(".t_sumar_restar").html('Sumar Artículos');
	$(".sumar_restar").html(' Sumar');
	$("#modal_restar_articulos").modal();
	idarticulo = id;
	op = 'sum';
}

const traer_articulo = id => {
	$.ajax({
		url: `${server}index.php/almacen_inventario_control/traer_articulo`,
		dataType: "json",
		data: { id },
		type: "post",
		success: datos => {
			if (datos == "sin_session") {
				close();
				return;
			}
			if (datos == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
				return;
			}
			const { codigo, nombre, cantidad, marca, referencia, categoria, bodega, valor, stock, observacion, } = datos[0];
			$("#txtinput_codigo").val(codigo);
			$("#txtmod_nom_art").val(nombre);
			$("#txtmod_cant").val(cantidad);
			$("#txtmod_marca").val(marca);
			$("#txtmod_ref").val(referencia);
			$("#cbxmod_categoria").val(categoria);
			$("#txtmod_stock").val(stock);
			$("#cbxmod_bodega").val(bodega);
			$("#txtmod_valor").val(valor);
			$("#txtmod_observaciones").val(observacion);
		},
		error: () => {
			MensajeConClase("Ha ocurrido un error", "error", "Oops...");
		}
	});
	$("#modificar_Articulos").modal();
}

const detalles_articulo = ({
	id,
	nombre,
	codigo,
	cantidad,
	categoria,
	bodega,
	marca,
	referencia,
	valor,
	observacion,
	stock,
	unidades
}) => {
	$(".info_art_nom").html(nombre);
	$(".info_art_cod").html(codigo);
	$(".info_art_can").html(cantidad);
	$(".info_art_cat").html(categoria);
	$(".info_art_sto").html(stock);
	$(".info_art_bod").html(bodega);
	$(".info_art_mar").html(marca);
	$(".info_art_ref").html(referencia);
	$(".info_art_val").html(valor);
	$(".info_art_obs").html(observacion);
	$(".info_art_uni").html(unidades);
	listar_historial(id);
	$("#modalInfo_Articulo").modal();
};

const modificar_articulo = id => {
	let data = new FormData(document.getElementById("modificar_Articulo"));
	data.append('id', id);
	data.append('codigo_actual', art_codigo);
	$.ajax({
		url: `${server}index.php/almacen_inventario_control/modificar_articulo`,
		type: "post",
		dataType: "json",
		data,
		cache: false,
		contentType: false,
		processData: false
	}).done(datos => {
		if (datos == "sin_session") {
			close();
			return;
		}
		if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
			return;
		}
		switch (datos) {
			case -1:
				MensajeConClase("Digite un código válido para el artículo!", "info", "Oops...");
				break;
			case -2:
				MensajeConClase("Digite un nombre para la solicitud!", "info", "Oops...");
				break;
			case -3:
				MensajeConClase("Elija una bodega para el artículo!", "info", "Oops...");
				break;
			case -4:
				MensajeConClase("Digite una cantidad!", "info", "Oops...");
				break;
			case -5:
				MensajeConClase("Digite un valor para el artículo!", "info", "Oops...");
				break;
			case -6:
				MensajeConClase("Elija una categoría para el artículo!", "info", "Oops...");
				break;
			case -7:
				MensajeConClase("El código que quiere ingresar ya existe!", "info", "Oops...");
				break;
			case -8:
				MensajeConClase("El código debe ser de máximo 10 dígitos", "info", "Oops...");
				break;
			case -9:
				MensajeConClase("Digite un stock mínimo para el artículo", "info", "Oops...");
				break;
			case -10:
				MensajeConClase("Por favor digite el nombre de las unidades del artículo", "info", "Oops...");
				break;
			case 1:
				MensajeConClase("Los datos del artículo fueron modificados con exito.", "success", "Proceso Exitoso!");
				listar_articulos();
				$("#modificar_Articulos").modal("hide");
				$("#modificar_Articulo").get(0).reset();
				idarticulo = 0;
				break;
			default:
				break;
		}
	});

}

const get_info_articulo = () => {
	$("#modificar_Articulo").get(0).reset();
	const { 
		codigo,
		id_categoria,
		nombre,
		marca,
		referencia,
		id_bodega,
		valor,
		observacion,
		min_stock,
		unidades_id,
	 } = $("#tabla_articulos").DataTable().row('.warning').data();
	$("#modificar_Articulos").modal();
	$("#txtinput_codigo").val(codigo);
	$("#txtmod_nom_art").val(nombre);
	$("#cbxmod_categoria").val(id_categoria);
	$("#cbxmod_bodega").val(id_bodega);
	$("#txtmod_marca").val(marca);
	$("#txtmod_ref").val(referencia);
	$("#txtmod_stock").val(min_stock);
	$("#cbxmod_unidades").val(unidades_id);
	$("#txtmod_valor").val(valor);
	$("#txtmod_observaciones").val(observacion);
}

const gestionar_ruta = route => {
	const pos = route.indexOf("index.php/");
	route = route.slice(pos + 10, route.length);
	ruta = route.replace(/[0-9]+/g, '');
	if (ruta[ruta.length - 1] === '/') ruta = ruta.substr(0, ruta.length - 1);
	if (ruta === 'almacen' || ruta === 'almacenADM/inventario') {
		tipo_modulo = "Inv_Alm";
	} else if (ruta === 'mantenimientoADM/inventario') {
		tipo_modulo = "Inv_Man";
	} else {
		tipo_modulo = null;
	}

	if (tipo_modulo == 'Inv_Alm') $(`#btncompras`).removeClass("oculto");
}

const listar_unidades = () => {
	consulta_ajax(`${server}index.php/almacen_inventario_control/listar_unidades`, {}, unidades => {
		let i = 0;
		$('#tbl_unidades tbody')
			.off('click', 'tr')
			.off('click', 'tr span.eliminar');
		const myTable = $("#tbl_unidades").DataTable({
			destroy: true,
			searching: true,
			processing: true,
			data: unidades,
			columns: [
				{ render: () => ++i },
				{ data: "valor" },
				{ defaultContent: '<span class="btn btn-default eliminar"><span class="fa fa-trash" style="color:#d9534f;"></span></span>' },
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tbl_unidades tbody').on('click', 'tr', function () {
			$("#tbl_unidades tbody tr").removeClass("warning");
			$(this).addClass("warning");
		});
	
	
		$('#tbl_unidades tbody').on('click', 'tr span.eliminar', function () {
			const { id } = myTable.row($(this).parent().parent()).data();
			eliminar_unidades(id);
		});
	});
}

const guardar_nuevas_unidades = nombre => {
	consulta_ajax(`${server}index.php/almacen_inventario_control/guardar_nuevas_unidades`, { nombre }, ({mensaje, tipo, titulo}) => {
		MensajeConClase(mensaje, tipo, titulo);
		if(tipo==='success') {
			listar_unidades();
			Cargar_parametro_buscado(186, ".cbxunidades", "Seleccione Unidades del Artículo");
		}
	});
}

const eliminar_unidades = id =>  {
	swal({
		title: `¿Eliminar Unidades?`,
		text: '',
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Eliminar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
	isConfirm => {
		if (isConfirm) {
			consulta_ajax(`${server}index.php/almacen_inventario_control/eliminar_unidades`, { id }, ({mensaje, tipo, titulo}) => {
				MensajeConClase(mensaje, tipo, titulo);
				if(tipo==='success') {
					listar_unidades();
					Cargar_parametro_buscado(186, ".cbxunidades", "Seleccione Unidades del Artículo");
				}
			});
		}
	});
}

const listar_articulos_compras = () => {
	let ids_articulos = [];
	articulos_compras_guardados.forEach(art => {
		ids_articulos.push(art.id_art_comp)
	});
	$(`#tbl_articulos_compras tbody`).html('')
	$(`#tbl_articulos_compras tbody`)
		.off('click', 'tr')
		.off('click', '.agregar_articulo')
		.off('click', '.eliminar_articulo')
	const myTable = $('#tbl_articulos_compras').DataTable({
		destroy: true,
		processing: true,
		searching: true,
		data: articulos_compras,
		columns: [
			{
				data: "codigo"
			},
			{
				data: "nombre"
			},
			{
				data: "cantidad"
			},
			{
				data: "stock"
			},
			{

				"render": function (data, type, full, meta) {
					boton = "<span title='Agregar' style='color: #39B23B;' data-toggle='popover' data-trigger='hover' class='fa fa-check pointer btn btn-default agregar_articulo'>"
					if (ids_articulos.indexOf(full.id) != -1) boton = "<span title='Eliminar' style='color: #DE4D4D;' data-toggle='popover' data-trigger='hover' class='fa fa-times pointer btn btn-default eliminar_articulo'>";
					return boton
				}
			}
		],
		language: get_idioma(),
		dom: "Bfrtip",
		buttons: []
	});

	$('#tbl_articulos_compras tbody').on('click', 'tr', function () {
		$("#tbl_articulos_compras tbody tr").removeClass("warning");
		$(this).addClass("warning");
	});

	$('#tbl_articulos_compras tbody').on('click', '.agregar_articulo', function () {
		let { id, nombre, en_sol } = myTable.row($(this).parent()).data();
		id_articulo_compra = id
		$("#form_agregar_articulos").get(0).reset()
		$("#nombre_art_comp").val(nombre)
		if(en_sol > 0) $(`#alert_compras`).removeClass("oculto");
		else $(`#alert_compras`).addClass("oculto");
		$("#modal_agregar_articulos").modal("show")
	});

	$('#tbl_articulos_compras tbody').on('click', '.eliminar_articulo', function () {
		let { id } = myTable.row($(this).parent()).data();
		eliminar_articulo_compra(id)
	});	
}

const agregar_articulo_compra = () => {
	let formulario = 'form_agregar_articulos'
	let formdata = new FormData(document.getElementById(formulario));
	let { cantidad_art_comp, codigo_orden, marca_art_comp, nombre_art_comp, observaciones_comp, referencia_art_comp } = formDataToJson(formdata);
	let mensaje = ''
	let sw = false
	let tipo = 'info'
	let titulo = "Oops..."
	if (!nombre_art_comp) {
		mensaje = 'Ingrese nombre del articulo'
		sw = true
	} else if (!cantidad_art_comp) {
		mensaje = 'Ingrese cantidad'
		sw = true
	} else if (!observaciones_comp) {
		mensaje = 'Ingrese descripción'
		sw = true
	}

	if (!sw) {
		articulos_compras_guardados.push({
			"id_art_comp": id_articulo_compra,
			"nombre_art_comp": nombre_art_comp,
			"cantidad_art_comp": cantidad_art_comp,
			"marca_art_comp": marca_art_comp,
			"referencia_art_comp": referencia_art_comp,
			"observaciones_comp": observaciones_comp,
		})

		id_articulo_compra = 0
		mensaje = 'Articulo Agregado con Exito'
		tipo = 'success'
		titulo = 'Proceso exitoso!'

		$("#form_agregar_articulos").get(0).reset()
		$("#modal_agregar_articulos").modal("hide")
		listar_articulos_compras()
	}
	MensajeConClase(mensaje, tipo, titulo);
}

const enviar_compra = (cod_sap) => {
	swal({
		title: "Estas Seguro ?",
		text: "Tener en cuenta que, al realizar esta acción se creara una nueva solicitud en el modulo de Compras, si desea continuar debe presionar la opción de 'Si, Entiendo' !",
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
				consulta_ajax(`${server}index.php/almacen_inventario_control/enviar_compra`, { articulos: articulos_compras_guardados, jefe: id_pjefe, cod_sap }, resp => {
					let { titulo, mensaje, tipo, usuario } = resp;
					if (tipo == "success") {
						let data_articulos = articulos_compras_guardados
						let jefe_enviar = correo_jefe
						id_pjefe = 0
						correo_jefe = null
						$('#jefe_compras').val('');
						$('#cbx_cod_sap').val('');
						articulos_compras_guardados = []
						listar_articulos_compras()
						enviar_correo_compra(usuario, data_articulos, jefe_enviar)
					}
					MensajeConClase(mensaje, tipo, titulo);
				});
			}
		}
	);
}

const traer_articulos_a_comprar = bodega => {
	return new Promise(resolve => {
		let url = `${server}index.php/almacen_inventario_control/listar_articulos_a_comprar`;
		consulta_ajax(url, { bodega }, resp => {
			resolve(resp);
		});
	});
}

const obtener_valores_permiso_almacen = (vp_principal, idparametro) => {
	return new Promise(resolve => {
		let url = `${server}index.php/almacen_inventario_control/obtener_valores_permiso_almacen`;
		consulta_ajax(url, { vp_principal, idparametro }, resp => {
			resolve(resp);
		});
	});
}

const eliminar_articulo_compra = id => {
	swal({
		title: "Estas Seguro ?",
		text: "El artículo será eliminado",
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
				for (let i = 0; i < articulos_compras_guardados.length; i++) {
					if (articulos_compras_guardados[i].id_art_comp == id) {
						articulos_compras_guardados.splice(i, 1);
						listar_articulos_compras();
						MensajeConClase("", "success", "Artículo Retirado");
						// swal.close();
						return false;
					}

				}
			}
		}
	);
}

const buscar_jefe = (persona = '') => {
	consulta_ajax(`${server}index.php/compras_control/buscar_jefe`, { persona }, (data) => {
		$(`#tabla_jefes tbody`).off('click', 'tr').off('click', 'tr span.selec_persona').off('dblclick', 'tr');
		const myTable = $('#tabla_jefes').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{ data: 'fullname' },
				{ data: 'identificacion' },
				{
					render: (data, type, full, meta) =>
						'<span class="btn btn-default selec_persona" title="Seleccionar Jefe" style="color: #5cb85c"><span class="fa fa-check"></span></span>'
				}
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: []
		});

		$('#tabla_jefes tbody').on('click', 'tr', function () {
			$('#tabla_jefes tbody tr').removeClass('warning');
			$(this).attr('class', 'warning');
		});

		$('#tabla_jefes tbody').on('click', 'tr span.selec_persona', function () {
			let { id, fullname, correo } = myTable.row($(this).parent().parent()).data()
			id_pjefe = id;
			correo_jefe = correo;
			$("#jefe_compras").val(fullname);
			$("#modal_buscar_jefe").modal('hide');
		});
	});
};

const enviar_correo_compra = (usuario, articulos, jefe_enviar) => {
	let correos = [{ "correo": usuario.correo }, { "correo": jefe_enviar }];
	let filas_tabla_art = ''
	articulos.map((articulo) => {
		filas_tabla_art = filas_tabla_art + 
		`<tr>
			<td>${articulo.nombre_art_comp}</td>
			<td>${articulo.cantidad_art_comp}</td>
			<td>${articulo.observaciones_comp}</td>
		</tr> `;
	})
	let ser = '<a href="' + server + 'index.php/compras/' + usuario.solicitud + '"><b>agil.cuc.edu.co</b></a>'
	
	let tabla_articulos = `
		<table>
			<thead style="font-weight: bold;">
				<tr>
				<td>Artículo</td>
				<td>Cantidad</td>
				<td>Descripción</td>
				</tr>
			</thead>
			<tbody>${filas_tabla_art}</tbody>
		</table>`
	let mensaje = `Se le notifica que la solicitud realizada por ${usuario.fullname},  fue recibida y se encuentra en proceso. A partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra la solicitud.
	<br><br>A continuación se relacionan los artículos solicitados :
	<br><br>${tabla_articulos}
	<br>Mas información en : ${ser}`;
	enviar_correo_personalizado("comp", mensaje, correos, usuario.fullname, "Compras CUC", "Solicitud de Compra", "ParCodAdm", 3);
}

const pintar_codigos_sap = async (cod_bodega) => {
	let codigos_sap = await obtener_valores_permiso_almacen(cod_bodega, 25);
	pintar_datos_combo_general(codigos_sap, '#cbx_cod_sap', 'Seleccione Codigo SAP');
}