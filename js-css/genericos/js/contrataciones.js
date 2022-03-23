let ruta = `${Traer_Server()}index.php/contrataciones_control/`;
const adjs_path = `${Traer_Server()}archivos_adjuntos/contrataciones/`;
let tcs = "";
let num_archivos = 0;
let myDropzone = 0;
let cargados = 0;
let tipos_adjs = [];
let adjs_names = [];
let adjs_garantia = [];
let adjs_garantia_names = [];
let id_tps_selected = "";
let tipo_persona = "";
let solicitud = "";
let tipo_tabla = "";
let id_cc_modifica = "";
let cont_id = "";
var TablaContratos = "";

$(document).ready(function () {
	listar_tipo_personas();
	garantia_contrato();

	$('#modal_adjuntar_contrato #adjs_contrato').change(function () {
		let inputval = document.getElementById('adjs_contrato').files[0].name;
		document.getElementById('Contrato').value = inputval;
	});

	$('#nuevo_contrato').click(function () {
		$("#modal_nuevo_contrato").modal();
	});

	$('#nueva_prorroga').click(function () {
		$("#modal_nueva_prorroga").modal();
	});

	/* Evento para filtro por fecha de inicio de contrato */
	$('#form_filtro').on('submit', function () {
		let dato = $('.filtro[name="fecha_i"]').val();
		listar_contratos(dato);
		$('#tabla_contra .mensaje-filtro').removeClass('oculto');
		$('#modal_filtrar').modal('hide');
		$(this).trigger('reset');
		return false;
	});

	/* Eventos para listar contrataciones */
	$('#lista_contratos').click(function () {
		listar_contratos();
	});

	$('#regresar_menu').click(function () {
		$('.lista_contratos').addClass('oculto').fadeOut(100);
		$('#menu_principal').removeClass('oculto').fadeIn(800);
	});

	/* Evento para el modal de numero de contrato y la busqueda del mismo */
	$('#btn_buscar_numcontra').click(function () {
		$('#txt_dato_numcontra').val('');
		buscar_ncm("undefined");
		$('#modal_buscar_numcontra').modal();
		callbak_activo = ({
			contrato,
			ncm_id
		}) => {
			$('#num_contrato').val(contrato).attr('data-ncm', ncm_id);
			$("#form_buscar_numcontra").trigger('reset');
			$("#modal_buscar_numcontra").modal('hide');
		}
	});

	$('.btn_busc_numcontra').click(function () {
		let valor = "";
		valor = $('#txt_dato_numcontra').val();
		buscar_ncm(valor, callbak_activo);
		return false;
	});

	/* Evento para el modal de buscar codigo SAP y la busqueda del mismo */
	$('.btn_buscar_codsap').click(function () {
		$('#txt_dato_codsap').val('');
		buscar_codsap("undefined");
		$('#modal_buscar_codsap').modal();
		callbak_activo = ({
			cod_nombre,
			cod_id,
			tipo_contrato
		}) => {
			$('#num_sap').val(cod_nombre).attr("data-codsap_id", cod_id).attr("data-tipo_cont", tipo_contrato);
			$("#form_buscar_codsap").trigger('reset');
			$("#modal_buscar_codsap").modal('hide');
		}
	});

	$('.btn_busc_codsap').click(function () {
		let valor = $('#txt_dato_codsap').val();
		if (valor === "") {
			$('#txt_dato_codsap').focus();
			return false;
		}
		buscar_codsap(valor, callbak_activo);
		return false;
	});

	/* Evento para el modal de buscar codigo del contrato */
	$('.btn_buscar_numcontra_prorroga').click(function () {
		let val = $('#txt_dato_id_contra').val();
		$('#modal_buscar_contrato').modal();
		callbak_activo = ({
			nombre_tista,
			id,
			num_contrato,
			contrato
		}) => {
			$('#num_contrato_prorroga').val(contrato).attr("data-codcontra_id", id).attr("data-tipo_cont", num_contrato);
			$("#form_buscar_contrato").trigger('reset');
			$("#modal_buscar_contrato").modal('hide');
		}
		buscar_contrato(val, callbak_activo);
	});

	$('.btn_busc_contra').click(function () {
		let valor = $('#txt_dato_id_contra').val();
		buscar_contrato(valor, callbak_activo);
		return false;
	});

	buscar_contratante();

	/* Evento para el modal de contratista y la busqueda del mismo */
	$('.btn_buscar_contratista').click(function () {
		$('#txt_dato_contratista').val('');		
		$('#modal_buscar_contratista').modal();
		callbak_activo = ({
			nombre,
			id
		}) => {
			$('#contratista').val(nombre).attr('data-tista', id);
			$("#form_buscar_contratista").trigger('reset');
			$("#modal_buscar_contratista").modal('hide');
		}
		buscar_contratista('', callbak_activo);
	});

	$('.btn_busc_contratista').click(function () {
		let valor = $('#txt_dato_contratista').val();
		if (valor == "") {
			$('#txt_dato_contratista').focus();
		}
		buscar_contratista(valor, callbak_activo);
		return false;
	});

	/* Evento notificaciones */
	$('#btn_notificaciones').click(function () {
		ver_notificaciones();
	});

	/* Eventons para btn administrar contratos */
	$('#btn_admin_solicitudes').click(function () {
		$("#nav_admin_compras li").removeClass("active");
		$("#s_persona").html('');
		$('#modal_administrar_contratos').modal();
		listar_actividades();
	});

	/* Administrar contratistas - evento */
	$('#admin_tista').click(async function () {
		$("#nav_admin_compras li").removeClass("active");
		$(this).addClass("active");
		let tista = await find_idParametro('contra_tistas');
		administrar_modulo('tista', tista.idpa);
	});

	/* Administrar contratos macro - evento */
	$('#admin_ncm').click(async function () {
		$("#nav_admin_compras li").removeClass("active");
		$(this).addClass("active");
		let ncm = await find_idParametro('ncm');
		administrar_modulo('ncm', ncm.idpa);
	});

	/* Administrar permisos - evento */
	$('#admin_permisos').click(function () {
		$("#nav_admin_compras li").removeClass("active");

		$(this).addClass("active");
		listar_actividades(undefined);
	});

	/* Evento para formulario de guardar nuevo parametro */
	$("#form_guardar_valor_parametro").submit(() => {
		guardar_valor_parametro();
		return false;
	});

	/* Evento para formulario de modificar parametro */
	$("#form_modificar_valor_parametro").submit(() => {
		modificar_valor_parametro();
		return false;
	});

	/* Evento para el formulario de nuevo contrato */
	$("#form_nuevo_contrato").submit(function () {
		MensajeConClase("validando info", "add_inv", "Oops...");
		let otros_adjs = [];
		for (const key in otros_adjs_names) {
			let num = parseInt(key, 0) + 1
			let file = document.getElementById(otros_adjs_names[key]).files[0];
			if (typeof file === 'undefined') {
				otros_adjs.push('Otros adjuntos ' + num)
			} else {
				otros_adjs.push(document.getElementById(otros_adjs_names[key]).files[0].name)
			}
		}
		tipos_adj = [].concat(adjs_garantia, tipos_adjs, otros_adjs);
		adjs_name = [].concat(adjs_garantia_names, adjs_names, otros_adjs_names);
		let formdata = new FormData($('#form_nuevo_contrato')[0]);
		let ncm = $('#num_contrato');
		let tista = $('#contratista');
		let prog_aca = $("#programa_academico");
		let asig = $("#asignatura");
		let codsap_input = $("#num_sap");
		formdata.append("codSap_id", $("#num_sap").attr('data-codsap_id'));
		formdata.append("ncm", ncm.attr('data-ncm'));
		formdata.append("tista_id", tista.attr('data-tista'));
		formdata.append("programa_academico", prog_aca.attr("data-pro_aca"));
		formdata.append("asignatura", asig.attr("data-asignatura"));
		formdata.append("tipo_contrato", codsap_input.attr("data-tipo_cont"));
		formdata.append("tipos_adj", JSON.stringify(tipos_adj));
		formdata.append("adjs_names", JSON.stringify(adjs_name));
		enviar_formulario(`${ruta}guardar_contrato`, formdata, res => {
			if (res.tipo == "success") {
				MensajeConClase(res.mensaje, res.tipo, res.titulo);
				$(`#form_nuevo_contrato`).trigger('reset');
				$("#adjs_container").html("");
				$('#modal_nuevo_contrato').modal('hide');
				setTimeout(() => {
					enviar_correo_contrato_solicitado();
				}, 1300);
			} else {
				MensajeConClase(res.mensaje, res.tipo, res.titulo);
			}
		});

		return false;
	});

	/* Evento para buscar personas en administrar */
	$("#s_persona, #sele_perso").click(() => {
		$("#modal_elegir_persona").modal();
		listar_personas();
		$("#txt_persona").val('');

		$("#frm_buscar_persona").submit(e => {
			e.preventDefault();
			const persona = $("#txt_persona").val();
			listar_personas(persona);
		});

		/*$('#txt_persona').on('input', function () {
			let persona = $(this).val();
			listar_personas(persona);
		});*/

		$('#btn_buscar_persona').click(() => {
			const persona = $("#txt_persona").val();
			listar_personas(persona);
		});
	});

	$('.input_numerico').on('keypress', function (e) {
		if (num_o_string("num", e.keyCode) == false) {
			return false;
		}
	});

	$(".download_contrato").on('click', function () {
		consulta_ajax(`${ruta}/verificar_contrato_adj`, { id: solicitud.id, tipo: 'adj_contrato' }, res => {
			let data = res['data'];
			if (res['status'] === 1) {
				url = adjs_path + data.adj_contrato;
				window.open(url, "_blank");
			}
		})
	})

	$(".download_prorroga").on('click', function () {
		consulta_ajax(`${ruta}/verificar_contrato_adj`, { id: solicitud.id, tipo: 'prorroga_adj' }, res => {
			let data = res['data'];
			if (res['status'] === 1) {
				url = adjs_path + data.prorroga_adj;
				window.open(url, "_blank");
			}
		})
	})

	$("#form_nueva_prorroga").submit((e) => {
		e.preventDefault();
		let formdata = new FormData($('#form_nueva_prorroga')[0]);
		formdata.append("id", $("#num_contrato_prorroga").attr('data-codcontra_id'));
		formdata.append("tipos_adj", 'prorroga_adj_name');
		formdata.append("adjs_names", 'prorroga_adj');
		enviar_formulario(`${ruta}guardar_prorroga`, formdata, res => {
			if (res.tipo == "success") {
				$("#modal_nueva_prorroga").modal('hide');
				MensajeConClase(res.mensaje, res.tipo, res.titulo);
				$(`#form_nueva_prorroga`).trigger('reset');
			} else {
				MensajeConClase(res.mensaje, res.tipo, res.titulo);
			}
		});
		return false;
	});

	$("#form_adjuntar_contrato").submit((e) => {
		e.preventDefault();
		let data = solicitud;
		rechazar_aceptar_swal(data.nombre_tista, data.estado_cont, data.id, 1, data.correo_inst, data.contrato, data.solicitante, data.soli);
	});

	$("#form_solicitar_firma").submit((e) => {
		e.preventDefault();
		let data = solicitud;
		rechazar_aceptar_swal(data.nombre_tista, data.estado_cont, data.id, 1, data.correo_inst, data.contrato, data.solicitante, data.soli);
	})

	let otros_adjs_names = [];
	let num = 0;
	$('.otros_adjuntos').on('click', function () {
		num++;
		let name_and_id = 'input' + num;
		let name = 'Otros adjuntos ' + num;
		otros_adjs_names.push(name_and_id);
		let adjuntos = `<div class="agrupado">
			<div class="input-group">
				<label class="input-group-btn">
				<span class="btn btn-primary">
					<span class="fa fa-folder-open"></span> Buscar
					<input type="file" style="display: none;" id='${name_and_id}' name='${name_and_id}'>
				</span>
				</label>
				<input type="text" class="form-control" id="${name}" data-text="${name_and_id}" readonly placeholder='Otros adjuntos'>
				<span class="fa fa-close" style="color:red;font-size: 25px;position: absolute;top: 4px;padding-left: 6px;cursor:pointer;" id="remove_${name_and_id}" data-id="${num}"></span>
			</div>
		</div>`;
		$("#otros_adjs").append(adjuntos);

		/* Los inputs tomen el nombre del documento que se elige */
		$(`#${name_and_id}`).change(function () {
			let inputval = document.getElementById(name_and_id).files[0].name;
			document.getElementById(name).value = inputval;
		});

		/** remover los inputs agregados */
		$(`#remove_${name_and_id}`).click(function () {
			padre = this.parentNode.parentNode.parentNode;
			hijo = this.parentNode.parentNode;
			obj = document.getElementById('Otros adjuntos ' + $(this).attr('data-id'))
			id = $(obj).attr('id')
			name = $(obj).attr('data-text')
			otros_adjs_names.splice(name, 1);
			padre.removeChild(hijo);
		});
	});

	$('#prorroga_adj').change(function () {
		let inputval = document.getElementById('prorroga_adj').files[0].name;
		document.getElementById('prorroga_adj_name').value = inputval;
	});

	$("#form_guardar_nuevo_contratista").submit((e) => {
		e.preventDefault();
		let name = $("#form_guardar_nuevo_contratista input[name=nombre]").val();
		let cc_nit = $("#form_guardar_nuevo_contratista input[name=cc_nit]").val();
		let email = $("#form_guardar_nuevo_contratista input[name=correo]").val();
		data = { name: name, identity: cc_nit, email: email }
		consulta_ajax(`${ruta}guardar_contratista`, data, async res => {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			if (res.tipo == 'success') {
				$("#form_guardar_nuevo_contratista input[name=nombre]").val('');
				$("#form_guardar_nuevo_contratista input[name=cc_nit]").val('');
				$("#form_guardar_nuevo_contratista input[name=correo]").val('');
				let tista = await find_idParametro('contra_tistas');
				listar_administrar_contratistas(tista.idpa);
			}
		})
	})

	$("#form_modificar_contratista").submit((e) => {
		e.preventDefault();
		let id = $("#form_modificar_contratista input[name=id]").val();
		let name = $("#form_modificar_contratista input[name=nombre]").val();
		let cc_nit = $("#form_modificar_contratista input[name=cc_nit]").val();
		let email = $("#form_modificar_contratista input[name=correo]").val();
		data = { name: name, identity: cc_nit, email: email, id: id }
		consulta_ajax(`${ruta}actualizar_contratista`, data, async res => {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			if (res.tipo == 'success') {
				$("#form_modificar_contratista input[name=id]").val('');
				$("#form_modificar_contratista input[name=nombre]").val('');
				$("#form_modificar_contratista input[name=cc_nit]").val('');
				$("#form_modificar_contratista input[name=correo]").val('');
				let tista = await find_idParametro('contra_tistas');
				listar_administrar_contratistas(tista.idpa);
				$("#modal_modificar_contratista").modal('hide');
			}
		})
	})
});

/* FUNCIONES FUERA DEL READY */
const call_adjs = (tps) => {
	$("#adjs_container").html("");
	tipos_adjs = [];
	adjs_names = [];
	consulta_ajax(`${ruta}call_adjs`, {
		"tps": tps
	}, res => {
		res.forEach(element => {
			tipos_adjs.push(element.doc_name);
			adjs_names.push(element.name_and_id);
			$("#adjs_container").append(
				`<div class="agrupado">
					<div class="input-group">
						<label class="input-group-btn">
						<span class="btn btn-primary">
							<span class="fa fa-folder-open"></span>
							Buscar <input type="file" style="display: none;" id='${element.name_and_id}' name='${element.name_and_id}'>
						</span>
						</label>
						<input style="cursor: context-menu;" type="text" class="form-control" id="${element.doc_name}" data-text="${element.name_and_id}" readonly placeholder='${element.doc_name}' value='' data-trigger="hover" data-placement="bottom" data-toggle="popover" data-content="${element.popover}">
					</div>
				</div>`
			);
			$('[data-toggle="popover"]').popover();
			/* Los inputs tomen el nombre del documento que se elige */
			$(`#${element.name_and_id}`).change(function () {
				let inputval = document.getElementById(element.name_and_id).files[0].name;
				document.getElementById(element.doc_name).value = inputval;
			});
		});
	});
}

function garantia_contrato() {
	$('#contrato_garantia').html(`<option value="0">Seleccione garantia del contrato</option>`);
	consulta_ajax(`${ruta}listar_tipo_garantia`, "", data => {
		for (let x = 0; x < data.length; x++) {
			$('#modal_nuevo_contrato #contrato_garantia').append(`<option value="${data[x].id}" data-idaux="${data[x].idaux}">${data[x].tipo_persona}</option>`);
		}
	});
}

/* Buscar numero de contrato macro */
const buscar_ncm = (dato, callback) => {
	let x = 1;
	consulta_ajax(`${ruta}buscar_ncm`, {
		"dato_buscar": dato
	}, res => {
		$("#tabla_numcontra_busqueda tbody").off("click", "tr td .seleccionar");
		const myTable = $('#tabla_numcontra_busqueda').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data: res,
			columns: [
				{
					render: function () {
						return x++;
					}
				},
				{
					data: 'contrato'
				},
				{
					data: 'entidad'
				},
				{
					data: 'codsap'
				},
				{
					defaultContent: '<span style="color: #39B23B;" title="Seleccionar COD.SAP" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>'
				}
			],
			language: get_idioma(),
			buttons: [],
			dom: 'Bfrtip',
		});
		$("#tabla_numcontra_busqueda tbody").on("click", "tr td .seleccionar", function () {
			let data = myTable.row($(this).parent()).data();
			callback(data);
		});
	});
}

/* Funcion administrar modulo */
const administrar_modulo = (tipo, parametro = '') => {
	adm_activo = {
		tipo,
		parametro,
		'valor_parametro': null
	};
	if (tipo == 'tista') {
		listar_administrar_contratistas(parametro);
		$("#container_admin_ncm").css("display", "none");
		$("#modal_nuevo_valor .modal-title").html('<span class="fa fa-fax "></span> Nuevo Contratista');
		$("#ModalModificarParametro .modal-title").html('<span class="fa fa-fax "></span> Modificar Contratista');
		$("#nombre_tabla_cu_or").html('TABLA DE CONTRATISTAS');
		$("#container_admin_tistas").fadeIn(1000);
		$("#container_admin_ncm").css("display", "none");
		$("#adm_permi").addClass("oculto");
	} else if (tipo == 'ncm') {
		listar_administrar_ncm(parametro);
		$("#container_admin_ncm").css("display", "none");
		$("#modal_nuevo_valor .modal-title").html('<span class="fa fa-file-text "></span> Nuevo Contrato Macro');
		$("#ModalModificarParametro .modal-title").html('<span class="fa fa-fax "></span> Modificar Empresa');
		$("#nombre_tabla_cu_or").html('TABLA DE CONTRATOS MACRO');
		$("#container_admin_ncm").fadeIn(1000);
		$("#container_admin_tistas").css("display", "none");
		$("#adm_permi").addClass("oculto");
	}

}

/* Listar los contratistas en el boton de administrar del listar contratos */
const listar_administrar_contratistas = idparametro => {
	$('#tabla_adm_tistas tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)');
	let num = 1;
	let myTable = $("#tabla_adm_tistas").DataTable({
		"destroy": true,
		"ajax": {
			url: `${ruta}listar_administrar_contratistas`,
			dataType: "json",
			type: "post",
			data: {
				idparametro
			},
			dataSrc: function (json) {
				return json.length == 0 ? Array() : json;
			},
		},
		"processing": true,
		"columns": [
			{
				"render": function (data, type, full, meta) {
					return num++;
				}
			}, {
				"data": "nombre"
			},
			{
				"data": "identy"
			},
			{
				"data": "correo"
			},
			{
				"data": "accion"
			},
		],
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": [],
	});

	$('#tabla_adm_tistas tbody').on('click', 'tr', function () {
		let data = myTable.row(this).data();
		$("#tabla_adm_tistas tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		adm_activo.valor_parametro = data.id;
	});

	$('#tabla_adm_tistas tbody').on('dblclick', 'tr', function () {
		let data = myTable.row(this).data();
		ver_detalle_parametro(data);
	});

	$('#tabla_adm_tistas tbody').on('click', 'tr td:nth-of-type(1)', function () {
		let data = myTable.row($(this).parent()).data();
		ver_detalle_parametro(data);
	});

}

/* Listar los contratos macro (ncm) en el boton de administrar del listar contratos */
const listar_administrar_ncm = idparametro => {
	$('#tabla_adm_ncm tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td:nth-of-type(1)');
	let myTable = $("#tabla_adm_ncm").DataTable({
		"destroy": true,
		"ajax": {
			url: `${Traer_Server()}index.php/genericas_control/Cargar_valor_Parametros/true/2`,
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
		"columns": [
			{

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
		"buttons": [],
	});

	$('#tabla_adm_ncm tbody').on('click', 'tr', function () {
		let data = myTable.row(this).data();
		$("#tabla_adm_ncm tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		adm_activo.valor_parametro = data.id;
	});
	$('#tabla_adm_ncm tbody').on('dblclick', 'tr', function () {
		let data = myTable.row(this).data();
		ver_detalle_parametro(data);
	});

	$('#tabla_adm_ncm tbody').on('click', 'tr td:nth-of-type(1)', function () {
		let data = myTable.row($(this).parent()).data();
		ver_detalle_parametro(data);
	});

}

/* Guardar valor */
const guardar_valor_parametro = () => {
	let url = `${Traer_Server()}index.php/genericas_control/guardar_valor_Parametro`;
	let data = new FormData(document.getElementById("form_guardar_valor_parametro"));
	data.append("idparametro", adm_activo.parametro);
	enviar_formulario(url, data, (resp) => {
		if (resp == "sin_session") {
			close();
		} else if (resp == 1) {
			MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
		} else if (resp == 2) {
			$("#form_guardar_valor_parametro").get(0).reset();
			MensajeConClase("", "success", "Datos Guardados!");
			listar_administrar_ncm(adm_activo.parametro);
		} else if (resp == 3) {
			MensajeConClase("El Nombre que desea guardar ya esta en el sistema", "info", "Oops...");
		} else if (resp == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al Guardar la información, contacte con el administrador.", "error", "Oops...");
		}
	})
}

/* Modificar valor parametro */
const modificar_valor_parametro = () => {
	let url = `${Traer_Server()}index.php/genericas_control/Modificar_valor_Parametro`;
	let data = new FormData(document.getElementById("form_modificar_valor_parametro"));
	data.append("idparametro", adm_activo.valor_parametro);
	enviar_formulario(url, data, (resp) => {
		if (resp == "sin_session") {
			close();
		} else if (resp == 1) {
			$("#form_modificar_valor_parametro").get(0).reset();
			$("#ModalModificarParametro").modal("hide");
			MensajeConClase("", "success", "Datos Modificados!");
			listar_administrar_ncm(adm_activo.parametro);
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

/* Confirmar eliminar valor parametro */
const confirmar_eliminar_parametro = (id, estado, tipo = 0) => {

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
	}, isConfirm => {
		if (isConfirm) {
			eliminar_parametro(id, estado, tipo);
		}
	});
}

/* Eliminar valor parametro */
function eliminar_parametro(idparametro, estado, tipo) {
	let url = `${Traer_Server()}index.php/genericas_control/cambio_estado_parametro`;
	let data = {
		idparametro,
		estado
	};
	consulta_ajax(url, data, async (resp) => {
		if (resp == "sin_session") {
			close();
		} else if (resp == 1) {
			MensajeConClase("", "success", "Dato Eliminado!");
			swal.close();			
			if(tipo == 1) {
				let tista = await find_idParametro('contra_tistas');
				listar_administrar_contratistas(tista.idpa);
			}else{
				listar_administrar_ncm(adm_activo.parametro);
			}
		} else if (resp == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al eliminar la información, contacte con el administrador.", "error", "Oops...");
		}
	})
}

const mostrar_parametro_modificar = async (buscar) => {
	let data = await buscar_parametro_id(buscar);
	let {
		valory,
		valor,
		valorx,
		id,
		idparametro,
		relacion
	} = data[0];
	if (idparametro == 25) {
		orden_centro.id = valory;
		orden_centro.nombre = relacion;
		$(".txt_nombre_centro").val(relacion);
	}

	$("#txtValor_modificar").val(valor);
	$("#txtDescripcion_modificar").val(valorx);
	$("#ModalModificarParametro").modal();
}


const mostrar_contratista_modificar = async (buscar) => {
	let data = await buscar_parametro_id(buscar);
	let {
		valory,
		valor,
		valorx,
		valorz,
		id,
		idparametro,
		relacion
	} = data[0];
	if (idparametro == 25) {
		orden_centro.id = valory;
		orden_centro.nombre = relacion;
		$(".txt_nombre_centro").val(relacion);
	}
	$("#form_modificar_contratista input[name=id]").val(buscar);
	$("#form_modificar_contratista input[name=nombre]").val(valor);
	$("#form_modificar_contratista input[name=cc_nit]").val(valory);
	$("#form_modificar_contratista input[name=correo]").val(valorz);
	$("#modal_modificar_contratista").modal();
}

/* Listar contratos */
const listar_contratos = async (dato) => {
	cont_id = dato;
	$('.lista_contratos').fadeIn(800).removeClass('oculto');
	$('#menu_principal').fadeOut(100).addClass('oculto');
	$('#tabla_contra tbody tr .crear_contrato').off('click');
	$("#tabla_contra tr td .ver_contratos").off('click');
	$("#tabla_contra tbody tr td").off('dblclick');
	$("#tabla_contra thead #limpiar_filtros_conts").off('click');
	$('#tabla_contra tbody tr .aceptar').off('click');
	$('#tabla_contra tbody tr .rechazar').off('click');
	$('.btnEstados').off('click');
	$('.btnArchivos').off('click');
	$('.btnFirmas').off('click');
	let contratos_p = await obtener_contratos_pendientes();
	$('#noti_n').html(contratos_p.length);
	consulta_ajax(`${ruta}listar_contratos`, {
		"dato_buscar": dato
	}, res => {
		TablaContratos = $('#tabla_contra').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{
					data: 'ver'
				},
				{
					data: 'modelo_contrato_valor'
				},
				{
					data: 'solicitante'
				},
				{
					data: 'fecha_ini'
				},
				{
					data: 'fecha_ter'
				},
				{
					data: 'estado_solicitud'
				},
				{
					data: 'accion'
				}
			],
			language: get_idioma(),
			buttons: [],
			dom: 'Bfrtip',
			"buttons": get_botones(),
		});
	});
}

/* Eventos activos de la tabla_contra */

/* Ver detalle */
$(document).on('click', '#tabla_contra tr td .ver_contratos', function () {
	solicitud = TablaContratos.row($(this).parent()).data();
	ver_detalles_contratos(solicitud);
	$('#modal_detalle_contrato').modal();
});

/* Ver detalle */
$(document).on('dblclick', "#tabla_contra tbody tr td", function () {
	solicitud = TablaContratos.row($(this).parent()).data();
	ver_detalles_contratos(solicitud);
	$('#modal_detalle_contrato').modal();
});

/* Aceptar contrato */
$(document).on('click', '#tabla_contra tbody tr .aceptar', function () {
	let data = TablaContratos.row($(this).parent()).data();
	rechazar_aceptar_swal(data.nombre_tista, data.estado_cont, data.id, 1, data.correo_inst, data.contrato, data.solicitante, data);
});

/* Rechazar contrato */
$(document).on('click', '#tabla_contra tbody tr .rechazar', function () {
	let data = TablaContratos.row($(this).parent()).data();
	rechazar_aceptar_swal(data.nombre_tista, data.estado_cont, data.id, 2, data.correo_inst, data.contrato, data.solicitante, data);
});

$(document).on('click', '#tabla_contra tbody tr .adj_contrato', function () {
	solicitud = TablaContratos.row($(this).parent()).data();
	$("#modal_adjuntar_contrato").modal();
});

$(document).on('click', '#tabla_contra .listar_tareas', function () {
	solicitud = TablaContratos.row($(this).parent()).data();
	listarTareas(solicitud);
})

/* Limpia filtros */
$(document).on('click', "#tabla_contra thead #limpiar_filtros_conts", function () {
	let url = location.href;
	url = url.replace(`${Traer_Server()}index.php/contrataciones/`, '')
	if (url >= 0 && url != '/') window.location = `${Traer_Server()}index.php/contrataciones`;
	listar_contratos();
	$('#tabla_contra .mensaje-filtro').addClass('oculto');
});

$(document).on('click', '#tabla_contra tbody tr .firma', function () {
	solicitud = TablaContratos.row($(this).parent()).data();
	$("#modal_solicitar_firma").modal();
})

$(document).on('click', '#tabla_contra tbody tr .espera', async function () {
	await listar_contratos();
	solicitud = TablaContratos.row($(this).parent()).data();
	if (solicitud.firma_contratante == 1 && solicitud.firma_contratista == 1) {
		MensajeConClase("El contrato ya fue firmado correctamente", "success", "Firmado!");
	} else {
		MensajeConClase("Esperando la firma del contratista", "info", "Esperando...");
	}
})

$(document).on('click', '#tabla_contra tbody tr .listar_tareas', function () {
	solicitud = TablaContratos.row($(this).parent()).data();
	listarTareas(solicitud)
})

$(document).on('click', '#tabla_contra tbody tr .enviar_compras', function () {
	solicitud = TablaContratos.row($(this).parent()).data();
	soli = solicitud;
	rechazar_aceptar_swal(soli.nombre_tista, soli.estado_cont, soli.id, 1, soli.correo_inst, soli.contrato, soli.solicitante, soli);
});

/** listar tareas */
const listarTareas = (soli) => {
	$('#modal_tareas').modal();
	$('#modal_tareas .modal-body ul').html('');
	if (soli.garantia_id === 'con_g') {
		$('#modal_tareas .modal-body ul').append(`<li class="list-group-item">
			<div class="input-group">
				<label class="input-group-btn">
					<span class="btn" style="color:#337ab7;">
						<span class="fa fa-folder-open"></span>
						Buscar <input type="file" style="display: none;" id="adj_garantia" name="adj_garantia" accept=".pdf">
					</span>
				</label>
				<input type="text" class="input-bordered" id="adj_garantia_name" data-text="Garantia" readonly="" placeholder="Adjuntar la poliza de garantia." value="" style="margin: 0;padding-top: 7px;">
			</div>
		</li>`);
	}

	$("#form_tareas").submit((e) => {
		e.preventDefault();
		rechazar_aceptar_swal(soli.nombre_tista, soli.estado_cont, soli.id, 1, soli.correo_inst, soli.contrato, soli.solicitante, soli);
	});

	$('#adj_garantia').change(function () {
		let inputval = document.getElementById('adj_garantia').files[0].name;
		document.getElementById('adj_garantia_name').value = inputval;
	});
}

const rechazar_aceptar_swal = (nombre_tista, estado_cont, idcon, accion, correo_inst, contrato, solicitante, soli) => {
	let action = "";
	let tipo = "";
	accion == 1 ? action = "aceptar" : action = "rechazar";
	accion == 2 ? tipo = "input" : "";
	swal({
		title: 'Atención!',
		text: `Está a punto de ${action} la solicitud de contrato "${contrato}" de ${nombre_tista} y avanzar al siguiente estado.`,
		type: tipo,
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Ok, comprendo",
		cancelButtonText: "Cancelar",
		inputPlaceholder: `Ingrese una observación...`,
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true,
	}, function (mensaje) {
		if (accion != 1 && mensaje == "") {
			swal.showInputError(`Por favor, ingrese una observación!`);
		} else if (mensaje) {
			if (accion == 1) {
				mensaje = "";
				if (estado_cont === 'Cont_En_Ela') {
					let formdata = new FormData($('#form_adjuntar_contrato')[0]);
					formdata.append("tipos_adj", 'Contrato');
					formdata.append("adjs_names", 'adjs_contrato');
					formdata.append("id", idcon);
					enviar_formulario(`${ruta}cargarContrato`, formdata, res => {
						if (res.tipo == "success") {
							$("#modal_adjuntar_contrato").modal('hide');
							aval_contratos(estado_cont, idcon, accion, mensaje, correo_inst, contrato, solicitante, mensaje, soli.soli);
							$(`#form_adjuntar_contrato`).trigger('reset');
						} else {
							MensajeConClase(res.mensaje, res.tipo, res.titulo);
						}
					});
				} else if (estado_cont === 'Cont_Secr_Avl') {
					consulta_ajax(`${ruta}/consultar_contratista`, { 'id_solicitud': idcon }, res => {
						let datacontrato = res[0][0];
						let datacontratista = res[1][0];
						let contrato = datacontrato.num_contrato
						let observacion = '';
						if (datacontratista.correo == '' || datacontratista.identity == '' || datacontratista.correo == null) {
							swal({
								title: "El correo o NIT/CC esta vacio!",
								text: "Actualice la informacion del contratista para poder continuar con el proceso.",
								type: "error",
								showCancelButton: false,
								confirmButtonColor: "#D9534F",
								confirmButtonText: "Si, Entiendo!",
								closeOnConfirm: true,
								closeOnCancel: false
							});
						} else {
							aval_contratos(estado_cont, idcon, accion, mensaje, correo_inst, contrato, solicitante, observacion, soli.soli);
						}
					});
				} else if (estado_cont === 'Cont_En_Firm') {
					let formdata = new FormData($('#form_solicitar_firma')[0]);
					formdata.append("id", idcon);
					enviar_formulario(`${ruta}guardar_firma`, formdata, res => {
						if (res.tipo == "success") {
							$("#modal_solicitar_firma").modal('hide');
							MensajeConClase(res.mensaje, res.tipo, res.titulo);
							aval_contratos(estado_cont, idcon, accion, mensaje, correo_inst, contrato, solicitante, mensaje, soli.soli);
							$(`#form_solicitar_firma`).trigger('reset');
						} else {
							MensajeConClase(res.mensaje, res.tipo, res.titulo);
						}
					});
				} else if (estado_cont === 'Cont_En_Ver') {
					if (soli.garantia_id === 'con_g') {
						let adj_names = [];
						let adj_tips = [];
						let elem_names = document.querySelectorAll('#modal_tareas .modal-body ul input[type=file]')
						let elm_tip = document.querySelectorAll('#modal_tareas .modal-body ul input[type=text]')
						elem_names.forEach(element => {
							adj_names.push($(element).attr('id'))
						});

						elm_tip.forEach(element => {
							adj_tips.push($(element).attr('data-text'))
						});
						let formdata = new FormData($('#form_tareas')[0]);
						formdata.append("id", idcon);
						formdata.append("adj_names", JSON.stringify(adj_names));
						formdata.append("adj_tips", JSON.stringify(adj_tips));
						enviar_formulario(`${ruta}cargarTareas`, formdata, res => {
							if (res.tipo == "success") {
								$("#modal_tareas").modal('hide');
								MensajeConClase(res.mensaje, res.tipo, res.titulo);
								aval_contratos(estado_cont, idcon, accion, mensaje, correo_inst, contrato, solicitante, mensaje, soli.soli);
								$(`#form_tareas`).trigger('reset');
							} else {
								MensajeConClase(res.mensaje, res.tipo, res.titulo);
							}
						});
					} else {
						aval_contratos(estado_cont, idcon, accion, mensaje, correo_inst, contrato, solicitante, mensaje, soli.soli);
					}

				} else if (estado_cont === 'Cont_En_Comp') {
					aval_contratos(estado_cont, idcon, accion, mensaje, correo_inst, contrato, solicitante, mensaje, soli.soli);
				} else {
					aval_contratos(estado_cont, idcon, accion, mensaje, correo_inst, contrato, solicitante, mensaje, soli.soli);
				}
			} else {
				aval_contratos(estado_cont, idcon, accion, mensaje, correo_inst, contrato, solicitante, mensaje, soli.soli);
			}
		} else if (mensaje == false) {
			return false;
		}
	});
}

const aval_contratos = (estado_sol, idcon, action, mensaje, correo_inst, contrato, solicitante, observacion, soli) => {
	MensajeConClase("validando info", "add_inv", "Oops...");
	consulta_ajax(`${ruta}aval_contratos`, {
		"idc": idcon,
		"ids": estado_sol,
		"accion": action,
		"msg": mensaje,
		"soli": soli
	}, async res => {
		if (res.tipo == "success") {
			consulta_ajax(`${ruta}obtener_correos`, { id_solicitud: idcon }, async datosPersona => {
				for (const key in datosPersona) {
					const datos = datosPersona[key];
					if (key === 'usuario_registra') {
						//console.log(datos);				
					} else if (key === 'contratista') {
						mensaje = `Se informa que su contrato: <b>${contrato}</b> se encuentra en Firma <br>Puede ingresar al aplicativo AGIL para realizarla.<br><br>Mas informaci&oacuten en: <a href="${server}index.php/contratista"><b>agil.cuc.edu.co/contratista</b></a><br>`;
					}
					await enviar_correo_estado(datos, idcon, mensaje);
				}
				await listar_contratos(cont_id);
				MensajeConClase(res.mensaje, res.tipo, res.titulo);
			});
		} else {
			await listar_contratos(cont_id);
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
		}
	});
}

const enviar_correo_estado = async ({ persona, correo, estado }, id, motivo) => {
	let sw = false;
	let ser = `<a href="${server}index.php/contrataciones/${id}"><b>agil.cuc.edu.co</b></a>`;
	let tipo = 3;
	let titulo = '';
	let mensaje = `Se informa que hay una solicitud realizada por ${persona}, lista para ser gestionada por usted, a partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;

	switch (estado) {
		case 'Cont_Soli_E':
			tipo = 1;
			sw = true;
			titulo = 'Solicitud Creada';
			mensaje = (motivo != "") ? motivo : `Se informa que la solicitud: <b>${id}</b> ha sido creada con exito, a partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
			break;
		case 'Cont_En_Ela':
			tipo = 1;
			sw = true;
			titulo = 'Solicitud en elaboración.';
			mensaje = `Se informa que la solicitud: <b>${id}</b> se encuentra en elaboración del contrato <br>A partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
			break;
		case 'Cont_Jefe_Avl':
			tipo = 1;
			sw = true;
			titulo = 'Solicitud en espera del aval de la Jefa de contrataciones.';
			mensaje = `Se informa que la solicitud: <b>${id}</b> se encuentra en espera del aval de la Jefa de contrataciones. <br>A partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud.<br><br>Mas informaci&oacuten en ${ser}`;
			break;
		case 'Cont_Secr_Avl':
			tipo = 1;
			sw = true;
			titulo = 'Solicitud en espera del aval del Secretario General.';
			mensaje = `Se informa que la solicitud: <b>${id}</b> ha sido aprobada con exito y se encuentra en espera del aval del Secretario General. <br>A partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud. <br><br>Mas informaci&oacuten en ${ser}`;
			break;
		case 'Cont_En_Firm':
			tipo = 1;
			sw = true;
			titulo = 'Solicitud en Firma.';
			mensaje = (motivo != "") ? motivo : `Se informa que la solicitud: <b>${id}</b> se encuentra en firma, a partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
			break;
		case 'Cont_En_Ver':
			tipo = 1;
			sw = true;
			titulo = 'Solicitud en verificaión.';
			mensaje = `Se informa que la solicitud: <b>${id}</b> ha sido firmada con exito y se encuentra en verificaión, a partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud.<br><br>Mas informaci&oacuten en: ${ser}<br>`;
			break;
		case 'Cont_En_Comp':
			tipo = 1;
			sw = true;
			titulo = 'Solicitud en compras.';
			mensaje = `Se informa que la solicitud: <b>${id}</b> se encuentra en espera del aval para enviar a compras, a partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud. <br><br>Mas informaci&oacuten en ${ser}`;
			break;
		case 'Cont_Ok_E':
			tipo = 1;
			sw = true;
			titulo = 'Solicitud finalizada.';
			mensaje = `Se informa que la solicitud: <b>${id}</b> a finalizada con exito, a partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud. <br><br>Mas informaci&oacuten en ${ser}`;
			break;
		case 'Cont_Rec_E':
			tipo = 1;
			sw = true;
			titulo = 'Solicitud rechazada.';
			mensaje = `Se informa que la solicitud: <b>${id}</b> fue rechazada por el motivo: <b>"${motivo}"</b>, a partir de este momento puede ingresar al aplicativo AGIL para revisar su solicitud. <br><br>Mas informaci&oacuten en ${ser}`;
			break;
	}
	if (sw) return new Promise(resolve => {
		enviar_correo_personalizado("cont", mensaje, correo, persona, "Contrataciones CUC", `Contrataciones AGIL - ${titulo}`, "ParCodAdm", tipo)
		resolve(true);
	});
}

/* Ver detalles del contrato */
const ver_detalles_contratos = (datos) => {
	$('.btnEstados').off('click');
	$('.btnArchivos').off('click');
	$('.btnFirmas').off('click');
	let {
		id,
		codSAP,
		fecha_ini,
		fecha_ini_gar,
		fecha_sus,
		fecha_ter,
		garantia,
		garantia_val,
		nombre_tante,
		nombre_tista,
		num_con,
		contrato,
		objetivo,
		plazo,
		tista_cedula_nit,
		valor,
		solicitante,
		type_person,
		modelo_contrato
	} = datos;

	//let tipo_contrato = "Convenio";
	let codSAP_tr = $('.tabla_contrataciones tr.codSAP_tr');
	//$(".tr_conv").removeClass("oculto");
	if (modelo_contrato == 'tipo_prorroga') {
		$('.tr_conv').addClass('oculto');
		$('.tr_contra').addClass('oculto');
		$('.btnEstados').attr('style', 'display: none;')
		$('.btnArchivos').attr('style', 'display: none;')
		$('.download_contrato').attr('style', 'display: none;')
		$('.download_prorroga').removeAttr('style');
	} else {
		$('.tr_conv').removeClass('oculto');
		$('.tr_contra').removeClass('oculto');
		$('.btnEstados').removeAttr('style');
		$('.btnArchivos').removeAttr('style');
		$('.download_contrato').removeAttr('style');
		$('.download_prorroga').attr('style', 'display: none;');
	}

	$('.solicitante_space').html(solicitante);
	$('.contratom_space').html(contrato);
	$('.num_contrato_space').html(num_con);
	codSAP == null || codSAP == 0 ? codSAP_tr.addClass('oculto') : codSAP_tr.removeClass('oculto');
	$('.codSAP_space').html(codSAP);
	$('.tante_space').html(nombre_tante);
	$('.tista_space').html(nombre_tista);
	$('.objetivo_space').html(objetivo);
	$('.valor_space').html(new Intl.NumberFormat('es-CO', {
		style: "currency",
		currency: "COP"
	}).format(valor));
	$('.fechasus_space').html(formatDate(fecha_sus));
	$('.fechaini_space').html(formatDate(fecha_ini));
	$('.fechafin_space').html(formatDate(fecha_ter));
	$('.cedunit_space').html(tista_cedula_nit);
	$('.plazo_space').html(plazo);
	$('.tipo_pers_space').html(type_person);
	$('.garantia_space').html(garantia);

	$('.btnEstados').on('click', function () {
		listar_estados(id);
	});

	$(".btnArchivos").on('click', function () {
		$("#modal_archivos_gestion").modal();
		listar_archivos_contratos(id);
	});
}

/* Listar adjuntos de contratos */
const listar_archivos_contratos = (ids) => {
	consulta_ajax(`${ruta}listar_archivos_contratos`, {
		"id_solicitud": ids
	}, res => {
		$("#tabla_adjs_cont").DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{
					data: "nombre_guardado",
					render: function (file) {
						return `<a href="${Traer_Server()}archivos_adjuntos/contrataciones/${file}" target="_blank" style="background-color: #5cb85c;color: white;" class="pointer form-control">Ver</a>`;
					}
				},
				{
					data: 'nombre_real'
				},
				{
					data: 'nombre_guardado'
				},
				{
					data: 'fecha_registra'
				}
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: []
		});
	});
}

/* Listar estados */
const listar_estados = (id_solicitud) => {
	let x = 1;
	consulta_ajax(`${ruta}listar_estados`, {
		'id': id_solicitud
	}, res => {
		const myTable = $('#tabla_estados').DataTable({
			destroy: true,
			processing: true,
			searching: true,
			data: res,
			columns: [
				{
					render: function () {
						return x++;
					}
				},
				{
					data: 'estado'
				},
				{
					data: 'fecha_registra'
				},
				{
					data: 'persona_mod'
				},
				{
					data: 'observacion'
				},
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: []
		});
	});
}

/* Listar estados de permisos */
const listar_estados_permisos = actividad => {
	const desasignar = '<span class="btn btn-default desasignar" title="Desasignar Estado"><span class="fa fa-toggle-on" style="color: #5cb85c"></span></span> ';
	const asignar = '<span class="btn btn-default asignar" title="Asignar Estado"><span class="fa fa-toggle-off"></span></span> ';
	const notificar = '<span class="btn btn-default notificar" title="Activar Notificación"><span class="fa fa-bell-o"></span></span> ';
	const no_notificar = '<span class="btn btn-default no_notificar" title="Desactivar Notificación"><span class="fa fa-bell red"></span></span> ';
	consulta_ajax(`${ruta}listar_estados_permisos`, {
		actividad,
		persona: id_persona
	}, data => {
		$(`#tabla_elegir_estados tbody`)
			.off('click', 'tr')
			.off('click', 'tr span.asignar')
			.off('click', 'tr span.desasignar')
			.off('click', 'tr span.no_notificar')
			.off('click', 'tr span.notificar')
			.off('dblclick', 'tr');
		const myTable = $("#tabla_elegir_estados").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{
					data: 'parametro'
				},
				{
					data: 'nombre'
				},
				{
					render: (data, type, {
						asignado,
						notificacion
					}, meta) => {
						return asignado ?
							notificacion == 1 ?
								desasignar + no_notificar :
								desasignar + notificar :
							asignar;
					}
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_elegir_estados tbody').on('click', 'tr', function () {
			$("#tabla_elegir_estados tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tabla_elegir_estados tbody').on('click', 'tr span.asignar', function () {
			const {
				estado
			} = myTable.row($(this).parent()).data();
			console.log(estado)
			asignar_estado(estado, actividad_selec, id_persona);
		});

		$('#tabla_elegir_estados tbody').on('click', 'tr span.desasignar', function () {
			const {
				asignado,
				estado
			} = myTable.row($(this).parent()).data();
			quitar_estado(estado, actividad_selec, id_persona, asignado);
		});

		$('#tabla_elegir_estados tbody').on('click', 'tr span.notificar', function () {
			const {
				estado
			} = myTable.row($(this).parent()).data();
			activar_notificacion(estado, actividad_selec, id_persona);
		});

		$('#tabla_elegir_estados tbody').on('click', 'tr span.no_notificar', function () {
			const {
				estado
			} = myTable.row($(this).parent()).data();
			desactivar_notificacion(estado, actividad_selec, id_persona);
		});

		const activar_notificacion = (estado, actividad, persona) => {
			consulta_ajax(`${ruta}activar_notificacion`, {
				estado,
				actividad,
				persona
			}, ({
				mensaje,
				tipo,
				titulo
			}) => listar_estados_permisos(actividad));
		}

		const desactivar_notificacion = (estado, actividad, persona) => {
			consulta_ajax(`${ruta}desactivar_notificacion`, {
				estado,
				actividad,
				persona
			}, ({
				mensaje,
				tipo,
				titulo
			}) => listar_estados_permisos(actividad));
		}

		const asignar_estado = (estado, actividad, persona) => {
			consulta_ajax(`${ruta}asignar_estado`, {
				estado,
				actividad,
				persona
			}, ({
				mensaje,
				tipo,
				titulo
			}) => listar_estados_permisos(actividad));
		}

		const quitar_estado = (estado, actividad, persona, id) => {
			consulta_ajax(`${ruta}quitar_estado`, {
				estado,
				actividad,
				persona,
				id
			}, ({
				mensaje,
				tipo,
				titulo
			}) => listar_estados_permisos(actividad));
		}
	});
}

/* Buscar codSap */
const buscar_codsap = (dato_buscar, callback) => {
	$("#tabla_codsap_busqueda tbody").off("click", "tr td .seleccionar");
	consulta_ajax(`${ruta}buscar_codsap`, {
		dato_buscar
	}, data => {
		const myTable = $('#tabla_codsap_busqueda').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{
					data: "cod_sap"
				},
				{
					data: 'cod_nombre'
				},
				{
					defaultContent: '<span style="color: #39B23B;" title="Seleccionar COD.SAP" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>'
				}
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: []
		});

		$("#tabla_codsap_busqueda tbody").on("click", "tr td .seleccionar", function () {
			let data = myTable.row($(this).parent()).data();
			callback(data);
		});

	});
}

/* Buscar contratantes */
const buscar_contratante = () => {
	consulta_ajax(`${ruta}buscar_contratante`, "", data => {
		for (let x = 0; x < data.length; x++) {
			$('#modal_nuevo_contrato #tante_select').append(`<option value="${data[x].id}">${data[x].nombre}</option>`);
		}
	});
}

/* Buscar contratista */
const buscar_contratista = (dato_buscar, callback) => {
	$("#tabla_contratista_busqueda tbody").off("click", "tr td .seleccionar");
	let i = 1;
	consulta_ajax(`${ruta}buscar_contratista`, {
		dato_buscar
	}, data => {
		const myTable = $('#tabla_contratista_busqueda').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{
					render: () => {
						return i++;
					}
				},
				{
					data: "nombre"
				},
				{
					data: "identy"
				},
				{
					data: "correo"
				},
				{
					defaultContent: '<span style="color: #39B23B;" title="Seleccionar Contratista" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>'
				}
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: []
		});

		$("#tabla_contratista_busqueda tbody").on("click", "tr td .seleccionar", function () {
			let data = myTable.row($(this).parent()).data();
			callback(data);
		});

	});
}

/* Listar tipos de personas, natural o juridica */
const listar_tipo_personas = () => {
	$('#tipo_persona').html(`<option value="0">Seleccione tipo de persona</option>`);
	consulta_ajax(`${ruta}listar_tipo_personas`, "", data => {
		for (let x = 0; x < data.length; x++) {
			$('#modal_nuevo_contrato #tipo_persona').append(`<option value="${data[x].id}" data-idaux="${data[x].idaux}">${data[x].tipo_persona}</option>`);
		}
	});

	$("#tipo_persona").change(function () {
		id_tps_selected = $(this).val();
		tipo_persona = $(this).find("option:selected").attr('data-idaux');
		if (id_tps_selected != 0) {
			call_adjs(tipo_persona);
		} else {
			$("#adjs_container").html("");
			return false;
		}
	});
}

const obtener_contratos_pendientes = () => {
	return new Promise(resolve => {
		let url = `${ruta}obtener_contratos_pendientes`;
		consulta_ajax(url, {}, async resp => {
			resolve(resp);
		})
	})
}

/* Buscar contrato */
const buscar_contrato = (dato_buscar, callback) => {
	$("#tabla_contrato_busqueda tbody").off("click", "tr td .seleccionar");
	consulta_ajax(`${ruta}buscar_contrato`, {
		dato_buscar
	}, data => {
		const myTable = $('#tabla_contrato_busqueda').DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{
					render: (num_contra) => '<span style="width: 100%; border: 1px solid rgba(0,0,0, 0.3); color: white; background-color: rgba(46,204,113,0.9);" class="action-buttons btn btn-default ver_contratos">ver</span>'
				},
				{
					data: "contrato"
				},
				{
					data: 'solicitante'
				},
				{
					data: 'nombre_tista'
				},
				{
					defaultContent: '<span style="color: #39B23B;" title="Seleccionar Contrato" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>'
				}
			],
			language: get_idioma(),
			dom: "Bfrtip",
			buttons: []
		});

		$("#tabla_contrato_busqueda tbody").on("click", "tr td .seleccionar", function () {
			let data = myTable.row($(this).parent()).data();
			callback(data);
		});

		$("#tabla_contrato_busqueda tbody").on("click", "tr td .ver_contratos", function () {
			solicitud = myTable.row($(this).parent()).data();
			ver_detalles_contratos(solicitud);
			$('#modal_detalle_contrato').modal();
		});
	});
}

/* Ver notificaciones */
const ver_notificaciones = async () => {
	let contratos_p = await obtener_contratos_pendientes();
	if (contratos_p.length == 0) {
		$("#panel_notificaciones").html(`
			<ul class="list-group">
				<li class="list-group-item active">
					<span class="badge">0</span>
					Pendientes por revisar
				</li>
			</ul>
		`);
		return false;
	}
	$("#noti_n").html(contratos_p.length);
	let res = ``;
	for (let index = 0; index < contratos_p.length; index++) {
		let {
			id,
			contrato,
			persona_solicita,
			ncm,
			estado
		} = contratos_p[index];
		let abrir = `listar_contratos(${id})`;
		res = `${res}
		<a href="#" class="list-group-item" style="text-align: left;">
			<span class="badge btn-danger close_me openn" id="n-${id}" onclick="${abrir}"> Abrir</span>
			<h4>Contrato: ${contrato}</h4>
			<p>Solicitante: ${persona_solicita}</p>
			<p>Estado actual: ${estado}</p>
		</a>`
	}
	$("#panel_notificaciones").html(`
		<ul class="list-group">
			<li class="list-group-item active">
				<span class="badge">${contratos_p.length}</span>
				Pendientes por revisar
			</li>
			${res}
		</ul>
	`);
	$("#modal_notificaciones").modal();

	$("span.openn").click(function () {
		$('#tabla_contra .mensaje-filtro').removeClass('oculto');
	});

	$("#modal_notificaciones .close_me").on("click", function () {
		$("#modal_notificaciones").modal('hide');
	});
}

/* Para los formularios */
const guardar_inf = (url, formdata, formulario) => {
	enviar_formulario(url, formdata, res => {
		if (res.tipo == "success") {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
			$(`#${formulario}`).trigger('reset');
			$("#adjs_container").html("");
			$('#modal_nuevo_contrato').modal('hide');
		} else {
			MensajeConClase(res.mensaje, res.tipo, res.titulo);
		}
	});
}

/* Listar personas */
const listar_personas = (texto = '') => {
	consulta_ajax(`${ruta}listar_personas`, {
		texto
	}, data => {
		$(`#tabla_personas tbody`)
			.off('click', 'tr')
			.off('click', 'tr span.asignar')
			.off('dblclick', 'tr');
		const myTable = $("#tabla_personas").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{
					data: 'fullname'
				},
				{
					render: (data, type, full, meta) => '<span class="btn btn-default asignar" title="Seleccionar Persona" style="color: #5cb85c"><span class="fa fa-check"></span></span>'
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_personas tbody').on('click', 'tr', function () {
			$("#tabla_personas tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tabla_personas tbody').on('click', 'tr span.asignar', function () {
			let {
				id,
				fullname
			} = myTable.row($(this).parent().parent()).data();
			id_persona = id;
			$("#modal_elegir_persona").modal('hide');
			$("#s_persona").html(fullname);
			listar_actividades(id);
		});

		$('#tabla_personas tbody').on('dblclick', 'tr', function () {
			let {
				id,
				fullname
			} = myTable.row($(this)).data();
			id_persona = id;
			$("#modal_elegir_persona").modal('hide');
			$("#s_persona").html(fullname);
			listar_actividades(id);
		});
	});
}

/* Listar actividades */
const listar_actividades = persona => {
	$("#adm_permi").removeClass("oculto");
	$("#admin_permisos").addClass("active");
	$("#container_admin_ncm").css("display", "none");
	$("#container_admin_tistas").css("display", "none");

	let num = 0;
	consulta_ajax(`${ruta}listar_actividades`, {
		persona
	}, data => {
		$(`#tabla_actividades tbody`)
			.off('click', 'tr')
			.off('click', 'tr span.asignar')
			.off('click', 'tr span.quitar')
			.off('click', 'tr span.config')
			.off('dblclick', 'tr');
		const myTable = $("#tabla_actividades").DataTable({
			destroy: true,
			processing: true,
			searching: false,
			data,
			columns: [
				{
					render: () => ++num
				},
				{
					data: 'nombre'
				},
				{
					render: (data, type, {
						asignado
					}, meta) => {
						let datos = asignado ?
							'<span class="btn btn-default quitar" style="color: #5cb85c"><span class="fa fa-toggle-on"></span></span> <span class="btn btn-default config"><span class="fa fa-cog"></span></span>' :
							'<span class="btn btn-default asignar"><span class="fa fa-toggle-off" ></span></span> ';
						return datos;
					}
				},
			],
			language: get_idioma(),
			dom: 'Bfrtip',
			buttons: [],
		});

		$('#tabla_actividades tbody').on('click', 'tr', function () {
			$("#tabla_actividades tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tabla_actividades tbody').on('dblclick', 'tr', function () {
			$("#tabla_actividades tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tabla_actividades tbody').on('click', 'tr span.asignar', function () {
			const {
				asignado,
				id
			} = myTable.row($(this).parent()).data();
			asignar_actividad(asignado, id);
		});

		$('#tabla_actividades tbody').on('click', 'tr span.quitar', function () {
			const {
				asignado,
				id
			} = myTable.row($(this).parent()).data();
			quitar_actividad(asignado, id);
		});

		$('#tabla_actividades tbody').on('click', 'tr span.config', function () {
			const {
				asignado,
				id
			} = myTable.row($(this).parent()).data();
			actividad_selec = asignado;
			$("#modal_elegir_estado").modal();
			listar_estados_permisos(actividad_selec);
		});
	});

	const asignar_actividad = (asignado, id) => {
		consulta_ajax(`${ruta}asignar_actividad`, {
			id,
			persona: id_persona,
			asignado
		}, ({
			mensaje,
			tipo,
			titulo
		}) => {
			MensajeConClase(mensaje, tipo, titulo);
			listar_actividades(id_persona);
		});
	}

	const quitar_actividad = (asignado, id) => {
		swal({
			title: 'Desasignar Actividad',
			text: "Tener en cuenta que al desasignarle esta actividad al usuario no podrá visualizar ninguna solicitud de este tipo.",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Entiendo!",
			cancelButtonText: "No, cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		}, isConfirm => {
			if (isConfirm) {
				consulta_ajax(`${ruta}quitar_actividad`, {
					id,
					persona: id_persona,
					asignado
				}, ({
					mensaje,
					tipo,
					titulo
				}) => {
					listar_actividades(id_persona);
				});
				swal.close();
			} else MensajeConClase(mensaje, tipo, titulo);
		});
	}
}

/* Funcion para obligar a que sea numeros o string */
const num_o_string = (tipo, key) => {
	if (tipo == "num") {
		if (key < 48 || key > 57) {
			return false;
		}
	} else if (tipo == "str") {
		if (key > 47 && key < 58) {
			return false;
		}
	}
}

/* Function para obligar limite de caracteres */
const limite_caracteres = (limite_caracteres, input_evaluar) => {
	let str_count = $(input_evaluar);
	$(input_evaluar).on("keydown", function (e) {
		if (str_count.val().length >= limite_caracteres) {
			if (e.key == "Backspace" || e.key == "Alt" || e.key == "F5" || e.key == "Tab" || e.key == "F12") {
				return true;
			} else {
				return false;
			}
		}
	});
}

/* Sweet alert de errores */
const error_alert = (msg, title) => {
	swal({
		title: title,
		text: msg,
		type: "warning",
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Ok",
		allowOutsideClick: true,
		closeOnConfirm: true,
	}, isConfirm => {
		if (isConfirm) {
			swal.close();
		}
	});
}

/* Generar avisos con estilo */
const avisos_gen = (msg) => {
	let text = `
      <div class="alert alert-info">
        <p class="text-justify">
          <strong><span class="fa fa-warning"></span> Aviso: </strong>${msg}
        </p>
      </div>`;
	return text;
}

/* Convertir monedas */

const convertir_moneda = (valor) => {
	let convierte = new Intl.NumberFormat("es-CO", {
		style: 'currency',
		currency: "COP",
		minimumFractionDigits: 0
	});
	return convierte.format(valor);
}

const enviar_correo_contrato_solicitado = () => {
	consulta_ajax(`${ruta}obtener_ultimo_contrato_usuario_registra`, null, datos => {
		consulta_ajax(`${ruta}obtener_correos`, { id_solicitud: datos.id, estado: datos.contrato_estado }, async datosPersona => {
			for (const key in datosPersona) {
				let mensaje = '';
				const personas = datosPersona[key];
				if (key === 'contratista') {
					mensaje = `Se informa que se le ha iniciado una solicitud de contrato con el Nº: <b>${datos.num_contrato}</b><br>Puede ingresar al aplicativo AGIL para ver el proceso.<br><br>Mas informaci&oacuten en: <a href="${server}index.php/contratista"><b>agil.cuc.edu.co/contratista</b></a><br>`;
				}
				await enviar_correo_estado(personas, datos.id, mensaje);
			}
		});
	});
}

/* De letras a numeros */

function Unidades(num) {

	switch (num) {
		case 1:
			return "UN";
		case 2:
			return "DOS";
		case 3:
			return "TRES";
		case 4:
			return "CUATRO";
		case 5:
			return "CINCO";
		case 6:
			return "SEIS";
		case 7:
			return "SIETE";
		case 8:
			return "OCHO";
		case 9:
			return "NUEVE";
	}

	return "";
}

function Decenas(num) {

	decena = Math.floor(num / 10);
	unidad = num - (decena * 10);

	switch (decena) {
		case 1:
			switch (unidad) {
				case 0:
					return "DIEZ";
				case 1:
					return "ONCE";
				case 2:
					return "DOCE";
				case 3:
					return "TRECE";
				case 4:
					return "CATORCE";
				case 5:
					return "QUINCE";
				default:
					return "DIECI" + Unidades(unidad);
			}
		case 2:
			switch (unidad) {
				case 0:
					return "VEINTE";
				default:
					return "VEINTI" + Unidades(unidad);
			}
		case 3:
			return DecenasY("TREINTA", unidad);
		case 4:
			return DecenasY("CUARENTA", unidad);
		case 5:
			return DecenasY("CINCUENTA", unidad);
		case 6:
			return DecenasY("SESENTA", unidad);
		case 7:
			return DecenasY("SETENTA", unidad);
		case 8:
			return DecenasY("OCHENTA", unidad);
		case 9:
			return DecenasY("NOVENTA", unidad);
		case 0:
			return Unidades(unidad);
	}
} //Unidades()

function DecenasY(strSin, numUnidades) {
	if (numUnidades > 0)
		return strSin + " Y " + Unidades(numUnidades)

	return strSin;
} //DecenasY()

function Centenas(num) {

	centenas = Math.floor(num / 100);
	decenas = num - (centenas * 100);

	switch (centenas) {
		case 1:
			if (decenas > 0)
				return "CIENTO " + Decenas(decenas);
			return "CIEN";
		case 2:
			return "DOSCIENTOS " + Decenas(decenas);
		case 3:
			return "TRESCIENTOS " + Decenas(decenas);
		case 4:
			return "CUATROCIENTOS " + Decenas(decenas);
		case 5:
			return "QUINIENTOS " + Decenas(decenas);
		case 6:
			return "SEISCIENTOS " + Decenas(decenas);
		case 7:
			return "SETECIENTOS " + Decenas(decenas);
		case 8:
			return "OCHOCIENTOS " + Decenas(decenas);
		case 9:
			return "NOVECIENTOS " + Decenas(decenas);
	}

	return Decenas(decenas);
} //Centenas()

function Seccion(num, divisor, strSingular, strPlural) {
	cientos = Math.floor(num / divisor)
	resto = num - (cientos * divisor)

	letras = "";

	if (cientos > 0)
		if (cientos > 1)
			letras = Centenas(cientos) + " " + strPlural;
		else
			letras = strSingular;

	if (resto > 0)
		letras += "";

	return letras;
} //Seccion()

function Miles(num) {
	divisor = 1000;
	cientos = Math.floor(num / divisor)
	resto = num - (cientos * divisor)

	strMiles = Seccion(num, divisor, "UN MIL", "MIL");
	strCentenas = Centenas(resto);

	if (strMiles == "")
		return strCentenas;

	return strMiles + " " + strCentenas;

	//return Seccion(num, divisor, "UN MIL", "MIL") + " " + Centenas(resto);
} //Miles()

function Millones(num) {
	divisor = 1000000;
	cientos = Math.floor(num / divisor)
	resto = num - (cientos * divisor)

	strMillones = Seccion(num, divisor, "UN MILLON", "MILLONES");
	strMiles = Miles(resto);

	if (strMillones == "")
		return strMiles;

	return strMillones + " " + strMiles;

	//return Seccion(num, divisor, "UN MILLON", "MILLONES") + " " + Miles(resto);
} //Millones()

function NumeroALetras(num) {
	var data = {
		numero: num,
		enteros: Math.floor(num),
		centavos: (((Math.round(num * 100)) - (Math.floor(num) * 100))),
		letrasCentavos: "",
		letrasMonedaPlural: "Pesos Colombianos",
		letrasMonedaSingular: "Peso Colombiano"
	};

	if (data.centavos > 0)
		data.letrasCentavos = "CON " + data.centavos + "/100";

	if (data.enteros == 0)
		return "CERO " + data.letrasMonedaPlural + " " + data.letrasCentavos;
	if (data.enteros == 1)
		return Millones(data.enteros) + " " + data.letrasMonedaSingular + " " + data.letrasCentavos;
	else
		return Millones(data.enteros) + " " + data.letrasMonedaPlural + " " + data.letrasCentavos;
}

$(document).on('input', '.input-number', function () {
	this.value = this.value.replace(/[^0-9]/g, '');
});

$(document).on('click', '.editor-info', function () {
	$('.editor-buttons .info').slideToggle();
})

$(document).on('input', '.text-content', function () {
	txtbox = $(this.parentNode.parentNode).find('#texto');
	txtbox.html($(this).val())
});

const find_idParametro = (codigo) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta}find_idParametro`, { codigo }, res => {
			resolve(res);
		});
	});
}

const formatDate = (date, style = 'medium') => {
	let formateador;
	date = date.split("-");
	let fecha = new Date(date);
	if (fecha == "Invalid Date") {
		return date;
	}
	let tiempo = `${fecha.getHours()}:${fecha.getMinutes()}:${fecha.getSeconds()}`;
	if (tiempo == '0:0:0') {
		formateador = new Intl.DateTimeFormat('es-CO', { dateStyle: style });
	} else {
		formateador = new Intl.DateTimeFormat('es-CO', { dateStyle: style, timeStyle: 'medium' });
	}
	return formateador.format(fecha);
};