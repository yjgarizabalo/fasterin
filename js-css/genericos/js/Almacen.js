let arts = 0;
let id_modifica = 0;
let id_articulo_sele = 0;
let idsolicitud = 0;
let idarticulo = 0;
let id_art = 0;
let guardar = 0;
let art_codigo = 0;
let art_sele = 0;
let articulos_sele = [];
let server1 = 'localhost';
let restr = 0;
let op = 0;
let id_persona_solici_tabla = 0;
let ruta_firmas = 'archivos_adjuntos/almacen/firmas/';
let firma = false;
let usuario = 0;
let email = 0;
let tipo_modulo = null;
let ruta = 0;
let estado_aux = 'Alm_Ent';

$(document).ready(() => {
	//Carga todas las solicitudes de almacen en la tabla solicitudes.
	server = Traer_Server();
	//LLama a la función guardar_solicitud al enviar el formulario.
	$("#Agregar_Solicitud").submit(() => {
		articulos_sele.length != 0 ? guardar_solicitud(articulos_sele) : MensajeConClase("", "info", "Por favor Agregar al menos un artículo");
		return false;
	});

	//LLama a la función guardar_Articulo al enviar el formulario.
	$('#Agregar_Articulos').submit(() => {
		guardar_articulo();
		return false;
	});

	//Llamada a la función modificar solicitud al presionar el botón.
	$("#modificar_solicitud").submit(() => {
		modificar_solicitud(idsolicitud);
		return false;
	});

	$(".sel_art").click(() => {
		buscar_articulo(-1);
		$("#Buscar_Articulo").modal();
		$("#txtarticulo").val("");
	});

	$("#solt1").click(() => {
		$('.inventario').fadeIn('slow');
		$('#menu_principal').css('display', 'none');
		$(".div_sol").show();
	});

	$("#solt2").click(() => {
		if (!restr) {
			arts = 0;
			articulos_sele = [];
			$(".div_sol").hide();
			$(".div_inv").removeAttr('required');
			$("#modalSolicitud").modal();
			Listar_articulos_solicitados(articulos_sele);
		} else {
			MensajeConClase("Por favor califique las solicitudes entregadas.", "info", "Oops...");
			listar_solicitudes(-1);
			$('.solicitudes').fadeIn();
			$('#menu_principal').css('display', 'none');
			$(".div_inv").removeAttr('required').hide();
		}
	});

	//Muestra el la tabla de solicitudes de almacen al seleccionar la opción en el menú.
	$("#solt3").click(() => {
		$('.solicitudes').fadeIn();
		$('#menu_principal').css('display', 'none');
		$(".div_inv").removeAttr('required').hide();
	});

	$("#btnInventario").click(() => {
		$('.solicitudes').hide();
		$('.inventario').fadeIn();
		guardar = 1;
		$('#art_titulo').html('Agregar Artículo');
		$(".msgboton").html('Agregar');
	});

	$("#btnSolicitudes").click(() => {
		$('.inventario').hide();
		$('.solicitudes').fadeIn();
		guardar = 3;
		$('#art_titulo').html('Agregar Artículo');
		$(".msgboton").html('Agregar');
	});

	//Abre el modal de modificar solicitud si se elige previamente una desde la tabla.
	$(".modSoli").click(() => idsolicitud ? traer_solicitud(idsolicitud) : MensajeConClase("Seleccione una Solicitud.", "info", "Oops..."));

	$("#btnmodificar").click(() => idarticulo ? traer_articulo(idarticulo) : MensajeConClase("Seleccione un artículo.", "info", "Oops..."));

	$('.btn_return').click(() => {
		$('#menu_principal').fadeIn();
		$('.solicitudes').css('display', 'none');
		$('.inventario').css('display', 'none');
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

	//Asigna valor de 2 a la variable para identificar que abre el modal de articulos desde al momento de crear una solicitud
	$("#btsagregar").click(() => {
		guardar = 2;
		$("#div_articulo").show();
		$('#art_titulo').html('Agregar Artículo');
		$(".msgboton").html('Agregar');
		$("#Agregar_Articulos").get(0).reset();
		$("#cod_articulo").html('Seleccione Artículo');
		$('.div_sol').fadeIn('fast');
		$('.div_inv').fadeOut('fast');
		$("#modalArticulos").modal();
	});

	$("#btn_calificar").click(() => {
		calificar_solicitud(idsolicitud);
		return false;
	});

	$("input:radio[name='rating']").click(() => $(this).blur());

	$("#btn_historial").click(() => historial_estados(idsolicitud));

	$("#btnlimpiar_solicitudes").click(() => {
		$("#estado_filtro").val('');
		$("#fecha_filtro").val('');
		listar_solicitudes();
	});

	$("#buscar_art").click(() => {
		const art = $("#txtarticulo").val();
		buscar_articulo(art);
	});

	$("#txtarticulo").keypress(e => {
		const code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) {
			const art = $("#txtarticulo").val();
			buscar_articulo(art);
			return false;
		}
	});

	$("#btnreporte_solicitudes").click(() => listar_solicitudes());

	$("#detalle_persona_solicita").click(function () {
		obtener_datos_persona_id_completo(id_persona_solici_tabla, ".nombre_perso", ".apellido_perso", ".identi_perso", ".tipo_id_perso", ".foto_perso", ".ubica_perso", ".depar_perso", ".cargo_perso", ".perfil_perso", ".celular");
		$("#Mostrar_detalle_persona").modal("show");
	});

	$("#frm_solicitante").submit(() => {
		const password = $("#txtpassword").val();
		verificarPassword("", password);
		return false;
	});

	$("#frm_otro").submit(() => {
		const user = $("#txt_usuario").val();
		const password = $("#txt_password").val();
		verificarPassword(user, password);
		return false;
	});

	$("#btn_encuestas").on('click', () => {
		listEncuestas();
	});

	$('#generar_filtro_encuestas').on('click', function(){		
		let ini = $('#fecha_inicio_encuestas').val();
		let fin = $('#fecha_fin_encuestas').val();
		if (ini != '' || fin != '') {
			$('#fecha_inicio_encuestas').val('');
			$('#fecha_fin_encuestas').val('');
			$('#Modal_filtrar_encuestas').modal('hide');
			$('#tabla_encuestas .nombre_tabla').html(`ENCUESTAS<br><span class="mensaje-filtro" id="textAlerta_solicitudes"><span class="fa fa-bell red"></span>La tabla tiene algunos filtros aplicados.</span>`);
			listEncuestas(ini, fin);
		}		
	})
	
	$('#btnlimpiar_encuestas').on('click', function(){		
		$('#tabla_encuestas .nombre_tabla').html(`ENCUESTAS`);
		listEncuestas();
	})
});

const listEncuestas = (fechaInicio = "", FechaFin = "") => {
	consulta_ajax(`${server}index.php/almacen_control/obtener_encuestas_soli_ent`, {fechaInicio, FechaFin}, solicitudes => {
		let num = 1;
		let myTable = $("#tabla_encuestas").DataTable({
			destroy: true,
			searching: false,
			data: solicitudes,
			processing: true,
			columns: [
				{ render: () => num++},
				{ data: "solicitante" },
				{ render: (data, type, { fecha_registra }, meta) => { 									
					let fecha = new Date(fecha_registra.split("-"));
					if (fecha == "Invalid Date") return date;								
					let formateador = new Intl.DateTimeFormat('es-CO', { dateStyle: 'long'});						
					return formateador.format(fecha);
				}},
				{ render: (data, type, { stars }, meta) => {					
					const template = document.querySelector('#modal_encuestas template');
					const content = template.content.cloneNode(true);			
					for (let i = 1; i <= 5; i++) {
						if (i <= stars) {
							content.getElementById(`${i}one`).style.color = 'orange';
						} else {
							content.getElementById(`${i}one`).style.color = 'gray';
						}
					}
					content.querySelector(".ratingstar").blur();
					return $(content).children()[0].outerHTML
				}},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});
		let x = 0; 
		let sum = 0;
		solicitudes.forEach(data => {
			if (data.stars > 0) {
				sum += parseInt(data.stars);				
				x++;	
			}				
		});
		let prom = parseFloat(sum / x);

		let temp = document.querySelector('#modal_encuestas template');
		let cont = temp.content.cloneNode(true);	
		for (var i = 1; i <= 5; i++) {
			if (i <= Math.round(prom)) {
				cont.getElementById(`${i}one`).style.color = 'orange';
			} else {
				cont.getElementById(`${i}one`).style.color = 'gray';
			}
		}
		cont.querySelector(".ratingstar").blur();
		cont.getElementById("ratingstar-num").style.fontSize = '37px';
		cont.getElementById("ratingstar-num").style.fontWeight = 'bold';
		cont.getElementById("ratingstar-num").append(prom.toPrecision(2));		
		if (isNaN(prom)) {
			cont = 'Ningún dato disponible en esta tabla';
		}
		$('#prom_stars').html(cont);

		/*let div = document.createElement('div');
		let promedio = document.createElement('span');
		promedio.style.fontSize = '37px';
		promedio.style.fontWeight = 'bold';
		let star = document.createElement('span');
		star.setAttribute("class", 'fa fa-star ratingstar');
		star.style.color = 'orange';
		promedio.append(prom.toPrecision(2), ' ', star);
		div.append(promedio)
		$('#prom_stars').html(div);*/
	});
}

const verificarPassword = (user = "", password) => {
	if (!password) {
		MensajeConClase("Por favor digite su contraseña.", "info", "");
		return false;
	}
	if (user != "") {
		usuario = user;
	}
	$.ajax({
		url: `${server}index.php/pages/verificar_password`,
		type: "post",
		dataType: "json",
		data: { usuario, password },
	}).done(resp => {
		let { datos, existe } = resp;
		if (datos.length == 0) {
			MensajeConClase("El usuario no se encuentra registrado en AGIL, contacte con el administrador.", "info", "");
			return;
		}
		switch (existe) {
			case 2:
				usuario = datos[0].id;
				const nombre = $(".info_sol_sol").html();
				$("#client_password").hide('fast');
				$("#div_firmar").html(`<p><span class="fa fa-edit"></span>Firma Receptor</p>
				<div id="content_firmas"><p style="text-align:center;">Loading Canvas...</p></div>
				<div class=" margin1">
					<span class="btn btn-danger entregar" onclick="entregar_solicitud(${idsolicitud}, '${nombre}', '${estado_aux}')"><span class="fa fa-check"></span> Terminar</span>
					<span class="btn btn-default active" onclick="newCanvas()"> <span class="fa fa-refresh"></span> Limpiar</span>
				</div>`);
				newCanvas();
				break;
			case 1:
				MensajeConClase("La contraseña es incorrecta.  Por favor intente de nuevo.", "info", "");
				break;
			default:
				break;
		}
	});
}

const guardar_solicitud = articulos => {
	const data = JSON.stringify(articulos);
	$.ajax({
		url: `${server}index.php/almacen_control/guardar_solicitud`,
		type: "post",
		dataType: "json",
		data: { data, tipo_modulo, },
	}).done(datos => {
		switch (datos[0]) {
			case 'sin_session':
				close();
				break;
			case -1302:
				MensajeConClase("No tiene Permisos Para Realizar Esta Operaci&oacuten", "error", "Oops...");
				break;
			case 1:
				let ser = `<a href="${server}index.php/${ruta}/${datos[1]}"><b>agil.cuc.edu.co</b></a>`
				let mensaje = `Se informa que la solicitud realizada por usted,  fue <b>ENVIADA</b> y se encuentra en proceso, Apartir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra su solicitud.<br><br>M&aacutes informaci&oacuten en :${ser}`;
				MensajeConClase("Solicitud guardada con exito", "success", "Proceso Exitoso!");
				enviar_correo_personalizado("comp", mensaje, "", "", "Almacen CUC", "Solicitud de Almacen", "ParCodAdm", -1);
				listar_solicitudes();
				$("#modalSolicitud").modal('hide');
				break;
			case -1:
				MensajeConClase("Los artículos solicitados sobrepasan el stock", "info", "Oops...");
				break;
			default:
				break;
		};
	});
};

const open_modal_guardar = () => {
	guardar = 5;
	$("#div_articulo").show();
	$('#art_titulo').html('Agregar Artículo');
	$(".msgboton").html('Agregar');
	$("#Agregar_Articulos").get(0).reset();
	$("#cod_articulo").html('Seleccione Artículo');
	$('.div_sol').fadeIn('fast');
	$('.div_inv').removeAttr('required').fadeOut('fast');
	$("#modalArticulos").modal();
}

const guardar_articulo = () => {
	const data = new FormData(document.getElementById("Agregar_Articulos"));
	data.append('tipo', guardar);
	data.append('tipo_modulo', tipo_modulo);
	switch (guardar) {
		case 4:
			data.append('id', id_art);
			data.append('solicitud', idsolicitud);
			break;
		case 5:
			data.append('id', idsolicitud);
			break;
		default:
			break;
	}
	$.ajax({
		url: `${server}index.php/almacen_control/guardar_articulo`,
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
			case -14:
				MensajeConClase("La solicitud ha sido tramitada. No se puede modificar el articulo o no tiene permisos para realizar esta operación.", "info", `Ooops!`);
				listar_solicitudes();
				traer_articulos_solicitud(idsolicitud, id_persona_solici_tabla);
				$("#Agregar_Articulos").modal('hide');
				break;
			default:
				break;
		};
	});
};

//Trae las solicitudes a la tabla_solicitudes
const listar_solicitudes = (id = '') => {
	const estado = $("#estado_filtro").val() === '' ? "%%" : $("#estado_filtro").val();
	const mes = $("#fecha_filtro").val() === '' ? "" : $("#fecha_filtro").val();
	id_persona_solici_tabla = 0;
	$('#tabla_solicitudes tbody')
		.off('dblclick', 'tr')
		.off('click', 'tr')
		.off('click', 'tr span.gestionar')
		.off('click', 'tr span.denegar')
		.off('click', 'tr span.cancelar')
		.off('click', 'tr span.calificar')
		.off('click', 'tr span.en_almacen')
		.off('click', 'tr span.finalizar')
		.off('click', 'tr td:nth-of-type(1)');
	const myTable = $("#tabla_solicitudes").DataTable({
		destroy: true,
		ajax: {
			url: `${server}index.php/almacen_control/Listar_solicitudes`,
			dataType: "json",
			data: { estado, mes, id, tipo_modulo, },
			type: "post",
			dataSrc: ({ data, fil, lim }) => {
				fil ?
					$('#textAlerta_solicitudes').html('<span class="fa fa-bell red"></span>La tabla tiene algunos filtros aplicados.')
					: $('#textAlerta_solicitudes').html('');
				restr = lim;
				return data ? data : Array();
			},
		},
		processing: true,
		columns: [
			{ data: "ver" },
			{ data: "num" },
			{ data: "fullname" },
			{ data: "departamento" },
			{ data: "fecha" },
			{ render: (data, type, { fecha_entrega }, meta) => fecha_entrega ? fecha_entrega : '----' },
			{ render: (data, type, { tiempo_entrega }, meta) => tiempo_entrega ? tiempo_entrega : '----' },
			{ render: (data, type, { calificacion }, meta) => calificacion ? calificacion : '----' },
			{ data: "estado" },
			{ data: "gestion" },
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: get_botones(),
	});

	myTable.column(5).visible(false);
	myTable.column(6).visible(false);
	myTable.column(7).visible(false);

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_solicitudes tbody').on('click', 'tr', function () {
		$("#tabla_solicitudes tbody tr").removeClass("warning");
		$(this).addClass("warning");
		const data = myTable.row(this).data();
		const { resp, } = data;
		id_persona_solici_tabla = resp;
	});

	$('#tabla_solicitudes tbody').on('dblclick', 'tr', function () {
		const data = myTable.row(this).data();
		const { id, resp, state, } = data;
		id_persona_solici_tabla = resp;
		idsolicitud = id;
		(state === "Alm_Ent" || state === "Alm_Cer") ? $("#panel_firma").show() : $("#panel_firma").hide();
		detalles_solicitud(data);
	});

	$('#tabla_solicitudes tbody').on('click', 'tr td:nth-of-type(1)', function () {
		const data = myTable.row($(this).parent()).data();
		const { id, state, } = data;
		idsolicitud = id;
		(state === "Alm_Ent" || state === "Alm_Cer") ? $("#panel_firma").show() : $("#panel_firma").hide();
		detalles_solicitud(data);
	});

	$('#tabla_solicitudes tbody').on('click', 'tr span.gestionar', function () {
		const data = myTable.row($(this).parent()).data();
		estado_aux = 'Alm_Ent'
		gestinar_entrega_firma(data);
	});

	$('#tabla_solicitudes tbody').on('click', 'tr span.denegar', function () {
		const { id } = myTable.row($(this).parent()).data();
		denegar_solicitud(id);
	});

	$('#tabla_solicitudes tbody').on('click', 'tr span.calificar', function () {
		const { id } = myTable.row($(this).parent()).data();
		calificar(id);
	});

	$('#tabla_solicitudes tbody').on('click', 'tr span.cancelar', function () {
		const { id } = myTable.row($(this).parent()).data();
		cancelar_solicitud(id);
	});

	$('#tabla_solicitudes tbody').on('click', 'tr span.en_almacen', function () {
		const { id } = myTable.row($(this).parent()).data();
		mercancia_en_almacen(id);
	});
	
	$('#tabla_solicitudes tbody').on('click', 'tr span.finalizar', function () {
		const data = myTable.row($(this).parent()).data();
		estado_aux = 'Alm_Mer'
		gestinar_entrega_firma(data)
	});
};

//Busca los artículos y los lleva a la tabla_buscar_articulos
const buscar_articulo = art => {
	if (art.length == 0) {
		MensajeConClase("Ingrese nombre del articulo", "info", "Ups!");
		return;
	}
	$('#tabla_buscar_articulos tbody').off('dblclick', 'tr').off('click', 'tr');
	let myTable = $("#tabla_buscar_articulos").DataTable({
		destroy: true,
		searching: false,
		ajax: {
			url: `${server}index.php/almacen_control/buscar_articulo`,
			data: { art, tipo_modulo, },
			dataSrc: json => json.length == 0 ? Array() : json.data,
			dataType: "json",
			type: "post",
		},
		processing: true,
		columns: [
			{ data: "num" },
			{ data: "nombre" },
			{ data: "unidades" },
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_buscar_articulos tbody').on('dblclick', 'tr', function () {
		let data = myTable.row(this).data();
		$("#tabla_buscar_articulos tbody tr").removeClass("success");
		$(this).attr("class", "success");
		const { id, nombre } = data;
		art_sele = data;
		$("#cod_articulo").html(nombre);
		$("#input_articulo").val(id);
		$("#Buscar_Articulo").modal('hide');
	});
}

const traer_solicitud = id => {
	$.ajax({
		url: `${server}index.php/almacen_control/traer_solicitud`,
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
			const { nombre, observaciones, cod_est, departamento, bodega, } = datos[0];
			$("#mod_nombre_solicitud").val(nombre);
			$("#mod_txtobservaciones").val(observaciones);
			$("#cbxmod_estado").val(cod_est);
			$("#cbxmod_departamento").val(departamento);
			$("#cbxmod_bodega").val(bodega);
		},
		error: () => {
			MensajeConClase("Ha ocurrido un error", "error", "Oops...");
		}
	});
	$("#modalModificarSolicitud").modal();
}

const traer_articulo = id => {
	$.ajax({
		url: `${server}index.php/almacen_control/traer_articulo`,
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

const modificar_solicitud = id => {
	let data = new FormData(document.getElementById("modificar_solicitud"));
	data.append('id_solicitud', id);
	$.ajax({
		url: `${server}index.php/almacen_control/modificar_solicitud`,
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
			case '-1302':
				MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
				break;
			case '-1':
				MensajeConClase("Digite un nombre para la solicitud!", "info", "Oops...");
				break;
			case '-2':
				MensajeConClase("Seleccione un estado para la solicitud!", "info", "Oops...");
				break;
			case '-3':
				MensajeConClase("Seleccione dependencia!", "info", "Oops...");
				break;
			case '1':
				MensajeConClase("Los datos de la solicitud fueron modificados con exito.", "success", "Proceso Exitoso!");
				listar_solicitudes();
				$("#modalModificarSolicitud").modal("hide");
				$("#modificar_solicitud").get(0).reset();
				break;
			default:
				break;
		}
	});
}

const Listar_articulos_solicitados = data => {
	modificando_ini = 0;
	id_articulo_sele = 0;
	$('#tabla_articulos_solicitados tbody').off('dblclick', 'tr').off('click', 'tr');
	const myTable = $("#tabla_articulos_solicitados").DataTable({
		destroy: true,
		processing: true,
		data,
		columns: [
			{ data: 'nombre_art' },
			{ data: 'cantidad_art' },
			{ data: 'marca' },
			{ data: 'referencia' },
			{ data: 'observaciones' },
			{
				render: function (data, type, full, meta) {
					return `<span class="btn btn-default">
								<span title="Eliminar" style="color: #DE4D4D;"  data-toggle="popover" data-trigger="hover" class="fa fa-trash-o pointer" onclick="eliminar_articulo_solicitud(${full.id})"></span>
							</span> 
							<span class="btn btn-default">
								<span style="color: #2E79E5;" title="Editar" data-toggle="popover" data-trigger="hover" class="fa fa-wrench pointer" onclick="mostrar_modificar_articulo_solicitud(${full.id})"></span>
							</span>`;
				}
			}
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: [],
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tabla_articulos_solicitados tbody').on('click', 'tr', function () {
		var { id } = myTable.row(this).data();
		id_articulo_sele = id;
		$("#tabla_articulos_solicitados tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});

	$('#tabla_articulos_solicitados tbody').on('dblclick', 'tr', function () {
		$("#tabla_articulos_solicitados tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});
}

const eliminar_articulo_solicitud = articulo => {
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
				for (let i = 0; i < articulos_sele.length; i++) {
					const id = articulos_sele[i].id;
					if (id == articulo) {
						articulos_sele.splice(i, 1);
						Listar_articulos_solicitados(articulos_sele);
						//MensajeConClase("", "success", "Artículo Retirado");
						swal.close();
						return false;
					}
				}
			}
		});
}

const mostrar_modificar_articulo_solicitud = articulo => {
	guardar = 3;
	id_articulo_sele = articulo;
	for (let i = 0; i < articulos_sele.length; i++) {
		const codigo = articulos_sele[i].id;
		if (codigo == articulo) {
			art_sele = articulos_sele[i];
			$("#cod_articulo").html(articulos_sele[i].nombre_art);
			$("#txtcantidad").val(articulos_sele[i].cantidad_art);
			$("#input_articulo").val(articulos_sele[i].codigo);
			$("#txtobservaciones").val(articulos_sele[i].observaciones);
			modificando_ini = 1;
			$('#art_titulo').html('Modificar Artículo');
			$(".msgboton").html('Modificar');
			$("#modalArticulos").modal();
			return false;
		}
	}
}

const traer_articulos_solicitud = id => {
	const tabla = $("#tbl_articulos_solicitud");
	$('#tbl_articulos_solicitud tbody').off('dblclick', 'tr').off('click', 'tr').off('click', 'tr td:first-of-type span');
	const myTable = tabla.DataTable({
		destroy: true,
		ajax: {
			url: `${server}index.php/almacen_control/traer_articulos_solicitud`,
			dataType: "json",
			data: { id, persona: id_persona_solici_tabla, },
			type: "post",
			dataSrc: json => json.length == 0 ? Array() : json.data,
		},
		processing: true,
		columns: [
			{ data: "ver" },
			{ data: "code" },
			{ data: "nombre" },
			{ data: "cantidad" },
			{ data: "unidades" },
			{ data: "gestion" },
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: get_botones(),
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tbl_articulos_solicitud tbody').on('click', 'tr', function () {
		$("#tbl_articulos_solicitud tbody tr").removeClass("warning");
		$(this).addClass("warning");
	});

	$('#tbl_articulos_solicitud tbody').on('dblclick', 'tr', function () {
		const data = myTable.row(this).data();
		detalles_articulos_solicitud(data);
	});

	$('#tbl_articulos_solicitud tbody').on('click', 'tr td:first-of-type span', function () {
		const data = myTable.row($(this).parent()).data();
		detalles_articulos_solicitud(data);
	});
};

const detalles_articulos_solicitud = ({
	code,
	nombre,
	marca,
	referencia,
	cantidad,
	observacion,
	unidades,
}) => {
	$("#tbl_articulos_solicitud tbody tr").removeClass("warning");
	$(this).attr("class", "warning");
	$("#modal_detalle_articulo").modal();
	$(".info_art_cod").html(code);
	$(".info_art_nom").html(nombre);
	$(".info_art_can").html(cantidad);
	$(".info_art_mar").html(marca);
	$(".info_art_uni").html(unidades);
	$(".info_art_ref").html(referencia);
	$(".info_art_obs").html(observacion);
}

const traer_articulo_solicitud = ({
	id,
	cantidad,
	observacion,
	codigo,
}) => {
	$(".div_inv").removeAttr('required');
	$("#txtcantidad").val(cantidad);
	$("#txtobservaciones").val(observacion);
	$("#modalArticulos").modal();
	$(".msgboton").html('Modificar');
	$(".div_sol").fadeIn('fast');
	$(".div_inv").fadeOut('fast');
	$("#div_articulo").hide();
	$("#input_articulo").val(codigo);
	id_art = id;
	guardar = 4;
}

const detalles_solicitud = ({
	id,
	fullname,
	departamento,
	fecha,
	estado,
	fecha_entrega,
	stars,
	state,
	agregar,
	firma,
	f_fullname,
	comentario,
	observaciones,
}) => {
	(state === 'Alm_Rec' && agregar == 1) ?
		$("#gestion").html("<span class='fa fa-plus pointer' title='Agregar Artículo' style='padding-right: 35px;' tittle='Agregar Artículo' data-trigger='hover' onclick='open_modal_guardar();'></span> ") : $("#gestion").html('');
	if (stars) {
		$("#star_rating").removeClass('hide');
		starmark(stars);
	} else {
		$("#star_rating").addClass('hide');
	}
	if (state === 'Alm_Ent' || state === 'Alm_Cer') {
		$('#row_fecha_entrega').show();
		$(".info_sol_ent").html(fecha_entrega);
	} else {
		$('#row_fecha_entrega').hide();
	}
	$(".info_sol_sol").html(fullname);
	$(".info_sol_dep").html(departamento);
	$(".info_sol_fec").html(fecha);
	$(".info_sol_est").html(estado);
	$(".info_sol_obs").html(observaciones);
	traer_articulos_solicitud(id, id_persona_solici_tabla);
	$('#art_titulo').html('Modificar Artículo');
	$(".msgboton").html('Modificar');
	$("#div_firmar").html(firma ? `<p><span class="fa fa-edit"></span>Firma Receptor</p><div id="content_firmas"><img src="${server + ruta_firmas + firma}"></div>` : '<p><span class="fa fa-edit"></span>Sin Firmar.</p>');
	$("#titulo_panel").html(firma ? `Recibió: ${f_fullname}` : "¿Quien recibe?");
	firma ? $("#client_password").hide() : $("#client_password").show();
	$(".limp_log").val("");
	$("#modalInfo_Solicitud").modal();
	observaciones ? $('#observacion_solicitud').removeClass('hide') : $('#observacion_solicitud').addClass('hide');
};

const entregar_solicitud = (id, nombre, estado = 'Alm_Ent') => {
	let endpoint = estado == 'Alm_Ent' ? 'entregar_solicitud' : 'finalizar_solicitud'
	swal({
		title: "Estas Seguro ?",
		text: "La solicitud cambiará a estado Entregada",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Entregar!",
		cancelButtonText: "No, Regresar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		isConfirm => {
			if (isConfirm) {
				let image = document.getElementById("canvas").toDataURL();
				$.ajax({
					url: `${server}index.php/almacen_control/${endpoint}`,
					type: "post",
					dataType: "json",
					data: { id, image, usuario },
				}).done(datos => {
					switch (datos) {
						case 'sin_session':
							close();
							break;
						case -1302:
							MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
							break;
						case 1:
							const ser = `<a href="${server}index.php/${ruta}/${id}"><b>agil.cuc.edu.co</b></a>`
							const mensaje = `Se informa que la solicitud realizada por usted,  fue <b>ENTREGADA</b> satisfactoriamente, A partir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra su solicitud.<br><br>Más informaci&oacuten en: ${ser}`;
							//MensajeConClase("Solicitud entregada exitosamente!", "success", "Entregada!");
							swal.close();
							enviar_correo_personalizado("comp", mensaje, email, nombre, "Almacen CUC", "Solicitud de Almacen", "ParCodAdm", 2);

							const mensaje_satisfaction = `Se informa que su solicitud ha finalizado. A partir de este momento tiene 24 horas para dar respuesta a la encuesta, de lo contrario se asumirá que recibió todo a conformidad.<br><br>Más informaci&oacuten en: ${ser}`;
							enviar_correo_personalizado("comp", mensaje_satisfaction, email, nombre, "Almacen CUC", "Encuesta de satisfacción en almacen", "ParCodAdm", 2);
							$("#modalInfo_Solicitud").modal("hide");
							listar_solicitudes();
							break;
						case -1:
							MensajeConClase("Por favor seleccione una solicitud!", "info", "Oops...");
							break;
						case -2:
							MensajeConClase("Artículos insuficientes para entregar la solicitud!", "info", "Oops...");
							break;
						case -3:
							MensajeConClase("No se puede cambiar el estado de la solicitud!", "info", "Oops...");
							listar_solicitudes();
							break;
						case -4:
							MensajeConClase("Error al cargar la firma!", "info", "Oops...");
							break;
						default:
							MensajeConClase(`Por favor contactese con el administrador`, "error", "Oops...");
							break;
					}
				});
			}
		});
}

const cancelar_solicitud = id => {
	swal({
		title: "Estas Seguro ?",
		text: "La solicitud será cancelada",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Cancelar!",
		cancelButtonText: "No, Regresar!",
		allowOutsideClick: true,
		closeOnConfirm: true,
		closeOnCancel: true
	},
		isConfirm => {
			if (isConfirm) {
				$.ajax({
					url: `${server}index.php/almacen_control/cancelar_solicitud`,
					type: "post",
					dataType: "json",
					data: { id, },
				}).done(datos => {
					switch (datos) {
						case 'sin_session':
							close();
							break;
						case -1302:
							MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
							break;
						case 1:
							//MensajeConClase("Solicitud Cancelada Exitosamente", "success", "Cancelada!");
							swal.close();
							break;
						case -3:
							MensajeConClase("No se puede cambiar el estado de la solicitud!", "info", "Ooops!");
							break;
					}
					listar_solicitudes();
				});
			}
		});
}

const mercancia_en_almacen = id => {
	swal({
		title: "Mercancia en Almacen",
		text: "Tener en cuenta que, al realizar esta accíon la solicitud sera habilitada para el siguiente proceso, si desea continuar debe presionar la opción de 'Si, Entiendo' !",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Entiendo!",
		cancelButtonText: "No, Regresar!",
		allowOutsideClick: true,
		closeOnConfirm: true,
		closeOnCancel: true
	},
		isConfirm => {
			if (isConfirm) {
				const { usuario, fullname } = $("#tabla_solicitudes").DataTable().row('.warning').data();
				$.ajax({
					url: `${server}index.php/almacen_control/mercancia_en_almacen`,
					type: "post",
					dataType: "json",
					data: { id, },
				}).done(datos => {
					switch (datos) {
						case 'sin_session':
							close();
							break;
						case -1302:
							MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
							break;
						case 1:
							let ser = `<a href="${server}index.php/${ruta}/${id}"><b>agil.cuc.edu.co</b></a>`
							let mensaje = `Se informa que la solicitud realizada por usted se encuentra en el estado <b>MERCANCIA EN ALMACEN</b>. A partir de este momento puede pasar al departamento de almacén para la entrega de lo solicitado e ingresar al aplicativo AGIL para tener conocimiento del estado en que se encuentra su solicitud.<br><br>M&aacutes informaci&oacuten en :${ser}`;
							enviar_correo_personalizado("comp", mensaje, `${usuario}@cuc.edu.co`, fullname, "Almacen CUC", "Solicitud de Almacen", "ParCodAdm", 2);						
							swal.close();
							listar_solicitudes();
							break;
						case -2:
							MensajeConClase("Artículos insuficientes para entregar la solicitud!", "info", "Oops...");
							break;
						case -3:
							MensajeConClase("No se puede cambiar el estado de la solicitud!", "info", "Ooops!");
							break;
					}
				});
			}
		});
}

const calificar = id => {
	$("#Modal_calificar_Solicitud").modal();
	idsolicitud = id;
}

const calificar_solicitud = id => {
	const data = new FormData(document.getElementById("guardar_calificacion"));
	data.append('id', id);
	$.ajax({
		url: `${server}index.php/almacen_control/calificar_solicitud`,
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
				MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
				break;
			case 1:
				MensajeConClase("", "success", "Solicitud Calificada");
				$("#Modal_calificar_Solicitud").modal('hide');
				listar_solicitudes(-1);
				break;
			case -1:
				MensajeConClase("Seleccione una solicitud", "info", "Oops...");
				break;
			case -2:
				MensajeConClase("Por favor califique la solicitud", "info", "Oops...");
				break;
			default:
				break;
		}
	});
}

const eliminar_articulo = id => {
	const nFilas = $("#tbl_articulos_solicitud tr").length;
	if ((nFilas - 2) == 1) {
		MensajeConClase("Este es el único artículo de la solicitud. Si desea eliminarlo, puede cancelar la solicitud.", "info", "No se puede eliminar el Artículo");
	} else {
		swal({
			title: "Estas Seguro ?",
			text: "El artículo será eliminado",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Cancelar!",
			cancelButtonText: "No, Regresar!",
			allowOutsideClick: true,
			closeOnConfirm: true,
			closeOnCancel: true,
		},
			function (isConfirm) {
				if (isConfirm) {
					$.ajax({
						url: `${server}index.php/almacen_control/eliminar_articulo`,
						type: "post",
						dataType: "json",
						data: { id, solicitud: idsolicitud, },
					}).done(datos => {
						switch (datos) {
							case 'sin_session':
								close();
								break;
							case -1302:
								MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
								break;
							case 1:
								//MensajeConClase("", "success", "Artículo Eliminado");
								swal.close();
								traer_articulos_solicitud(idsolicitud, id_persona_solici_tabla);
								break;
							case -1:
								MensajeConClase("La solicitud ha sido tramitada. No se puede modificar el articulo", "info", `Ooops!`);
								listar_solicitudes();
								traer_articulos_solicitud(idsolicitud, id_persona_solici_tabla);
								$("#Agregar_Articulos").modal('hide');
								break;
							case -2:
								MensajeConClase("Por favor seleccione un artículo para eliminar.", "info", "Oops...");
								break;
							default:
								break;
						}
					});
				}
			});
	}
}

const denegar_solicitud = id => {
	swal({
		title: "¿Está seguro?",
		text: "Si desea denegar la solicitud, por favor ingrese el motivo por el cual la rechaza.",
		type: "input",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Denegar!",
		cancelButtonText: "No, Cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true,
		inputPlaceholder: "Observación"
	}, text => {
		if (!text) {
			swal.showInputError("Debe Ingresar la observación!");
			return false;
		} else {
			const { usuario, fullname } = $("#tabla_solicitudes").DataTable().row('.warning').data();
			const email = `${usuario}@cuc.edu.co`;
			const comment = text.trim();
			$.ajax({
				url: `${server}index.php/almacen_control/denegar_solicitud`,
				type: "post",
				dataType: "json",
				data: { id, comment, },
			}).done(datos => {
				switch (datos) {
					case 'sin_session':
						close();
						break;
					case -1302:
						MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
						break;
					case 1:
						const ser = `<a href="${server}index.php/${ruta}/${id}"><b>agil.cuc.edu.co</b></a>`
						const mensaje = `<p>Se informa que la solicitud realizada por usted,  fue <b>DENEGADA</b>, Apartir de este momento puede ingresar al aplicativo AGIL para  tener conocimiento del estado en que se encuentra su solicitud.</p>
						<p>El motivo de rechazo de la solicitud es: ${text}</p>	
						<p>M&aacutes informaci&oacuten en :${ser}</p>`;
						MensajeConClase("", "success", "Solicitud Denegada!");
						swal.close();
						enviar_correo_personalizado("comp", mensaje, email, fullname, "Almacen CUC", "Solicitud de Almacen", "ParCodAdm", 2);
						listar_solicitudes();
						break;
					case -1:
						MensajeConClase("Por favor seleccione una solicitud.", "info", "Oops...");
						break;
					case -2:
						MensajeConClase("Por favor Digite una observación.", "info", "Oops...");
						break;
					case -3:
						MensajeConClase("La solicitud ya ha sido tramitada!", "info", "Oops...");
						listar_solicitudes();
						break;
					default:
						break;
				}
			});
			return false;
		}
	});
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

const historial_estados = id => {
	$('#tbl_historial_estados tbody').off('dblclick', 'tr').off('click', 'tr');
	const myTable = $("#tbl_historial_estados").DataTable({
		destroy: true,
		"searching": false,
		ajax: {
			url: `${server}index.php/almacen_control/historial_estados`,
			data: { id },
			dataSrc: json => json.length == 0 ? Array() : json.data,
			dataType: "json",
			type: "post",
		},
		processing: true,
		columns: [{
			data: "num"
		},
		{
			data: "estado"
		},
		{
			data: "fecha"
		},
		{
			data: "fullname"
		},
		],
		language: get_idioma(),
		dom: 'Bfrtip',
		buttons: get_botones(),
	});

	//EVENTOS DE LA TABLA ACTIVADOS
	$('#tbl_historial_estados tbody').on('click', 'tr', function () {
		$("#tbl_historial_estados tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
	});
}

const cantidad_disponible = id => {
	$.ajax({
		url: `${server}index.php/almacen_control/cantidad_disponible`,
		type: "post",
		dataType: "json",
		data: { id, },
	}).done(datos => {
		if (datos[0] >= 0) {
			MensajeConClase(`- ${datos[1]} Artículos solicitados\n- ${datos[0]} Artículos disponibles`, "", 'Disponibilidad del artículo');
			listar_solicitudes();
		} else if (datos[0] == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Operación", "error", "Oops...");
		} else if (datos == -1) {
			MensajeConClase("Por favor seleccione una solicitud.", "info", "Oops...");
		} else if (datos[0] == 'sin_session') {
			close();
		}
	});
}

const gestinar_entrega_firma = data => {
	const { state } = data;
	(state === "Alm_Rec" || state === "Alm_Mer") ? $("#panel_firma").show() : $("#panel_firma").hide();
	$("#client_password").show();
	detalles_solicitud(data);
	firma = data.firma;
	usuario = data.usuario;
	idsolicitud = data.id;
	email = `${data.usuario}@cuc.edu.co`;
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

const mostrar_perfiles = (vp_p, id_p) => {
	listar_permisos_por_parametro(vp_p, id_p);
	$("#Modal_perfiles").modal();
}

const listar_permisos_por_parametro = (id_p, vp_p) => {
	$('#tabla_perfiles tbody').off('dblclick', 'tr').off('click', 'tr');
	const myTable = $("#tabla_perfiles").DataTable({
		destroy: true,
		ajax: {
			url: `${server}index.php/almacen_control/listar_permisos_por_parametro`,
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
					url: `${server}index.php/almacen_control/gestionar_perfil`,
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
							MensajeConClase("Categoría asignada exitosamente", "success", "Proceso Exitoso!");
							$(`#btn${btn}`).removeClass('fa-toggle-off').addClass('fa-toggle-on').css('color', 'green').attr('title', 'Quitar perfil');
							break;
						case 2:
							MensajeConClase("Categoría removida exitosamente", "success", "Proceso Exitoso!");
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

const gestionar_ruta = route => {
	const pos = route.indexOf("index.php/");
	route = route.slice(pos + 10, route.length);
	ruta = route.replace(/[0-9]+/g, '');
	if (ruta[ruta.length - 1] === '/') ruta = ruta.substr(0, ruta.length - 1);
	if (ruta === 'almacen' || ruta === 'almacenADM/solicitudes') {
		tipo_modulo = "Inv_Alm";
	} else if (ruta === 'tecnologia/almacen') {
		tipo_modulo = "Inv_Tec";
	} else {
		tipo_modulo = null;
	}

}
