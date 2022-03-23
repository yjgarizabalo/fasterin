let aux_id = null;
let idpermisoparametro = 0;
let id_auxvalorparametro = 0;
let idparametro = 0;
let idvalorparametro = 0;
let tipo_Re = "";
let Arraycomboaux = [];
let ruta_genericas = `${Traer_Server()}index.php/genericas_control/`;
$(document).ready(function () {
	$('.btn-Efecto-men').popover();
	// $("#listado_parametros").change(function () {
	// 	idparametro = $(this).val();
	// 	Listar_valor_Parametros(idparametro);
	// });
	$("#GuardarParametro").submit(() => {
		registrarParametro();
		return false;
	});
	$("#GuardarValorParametro").submit(() => {
		registrar_valor_Parametro();
		return false;
	});
	$("#Modificar_valor_parametro").submit(() => {
		modificar_valor_Parametro(idvalorparametro);
		return false;
	});

	$("#AgregarValorParametro").click(function () {
		$('#GuardarValorParametro').get(0).reset();
		if (idparametro == 0) MensajeConClase("Antes de continuar seleccione el parametro al cual va a registrar un nuevo valor..!", "info", "Oops...");
		else $("#ValorParmetro").modal();
	});

	$("#listado_parametros_permiso").change(function () {
		let idparametro = $(this).val();		
		traer_valores_permisos(idparametro);
	});

	// Modal entre permisos y buscar parametro
	$(".close_modal_p").click(function () {        		
		$("#ModalPermiso").modal();	
		$("#modal_buscador_permisos").modal("hide");		
        return false;
    });

	// Buscar parametro inicial
	$(".btn_buscar_parametro").click(function () {        
		buscar_parametro("",0);
		$("#txt_parametro_buscado").val("");
		$("#modal_buscador_parametros").modal();		
        return false;
    });

	// Form de parametro inicial
	$("#frm_buscar_parametro").submit(() => {		
        let valor_buscado = $("#txt_parametro_buscado").val();		
        buscar_parametro(valor_buscado,0);
        return false;
    });


	// Buscar parametro para permisos
	$(".btn_buscar_permiso").click(function ()  {		
		buscar_parametro("",1);
		$("#txt_parametro_permiso").val("");
		$("#modal_buscador_permisos").modal();
		$("#ModalPermiso").modal("hide");
        return false;
    });

	// Form de parametro para permisos
	$("#frm_buscar_permiso").submit(() => {		
        let valor_buscado = $("#txt_parametro_permiso").val();		
        buscar_parametro(valor_buscado,1);
        return false;
    });
});


const Listar_Parametros = () => {
	idparametro = 0;
	consulta_ajax(`${ruta_genericas}Cargar_Parametros`, '', (resp) => {
		$("#listado_parametros").html(`<option value=''>Seleccione Parametro</option>`);
		resp.map((elemento) => {
			let { nombre, id } = elemento;
			$("#listado_parametros").append(`<option value= "${id}">${id} - ${nombre}</option>`);
		})
	});
}

const Listar_valor_Parametros = parametro => {
	idvalorparametro = 0;
	id_auxvalorparametro = 0;
	$('#tablavalorparametros tbody').off('dblclick', 'tr');
	$('#tablavalorparametros tbody').off('click', 'tr');
	let myTable = $("#tablavalorparametros").DataTable({
		"destroy": true,
		"ajax": {
			url: Traer_Server() + "index.php/genericas_control/Cargar_valor_Parametros",
			dataType: "json",
			data: {
				idparametro: parametro
			},
			"dataSrc": function (json) {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			},
			type: "post",
		},
		"processing": true,

		"columns": [
			{
				"data": "id"
			},
			{
				"data": "id_aux"
			},
			{
				"data": "valor"
			},
			{
				"data": "valorx"
			},
			{
				"data": "valory"
			},
			{
				"data": "valorz"
			},
			{
				"data": "valora"
			},
			{
				"data": "valorb"
			},
			{
				"data": "estado"
			},
			{
				"data": "op"
			},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": [get_botones()],
	});

	$('#tablavalorparametros tbody').on('click', 'tr', function () {
		let data = myTable.row(this).data();
		idvalorparametro = data.id;
		id_auxvalorparametro = data.id_aux;
		$("#tablavalorparametros tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});

}

const registrarParametro = () => {
	let formData = new FormData(document.getElementById("GuardarParametro"));
	enviar_formulario(`${ruta_genericas}guardar_Parametro`, formData, (resp) => {
		if (resp == 1) {
			MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
		} else if (resp == 2) {
			$("#GuardarParametro").get(0).reset();
			MensajeConClase("Parametro Guardado", "success", "Proceso Exitoso!");
			Listar_Parametros();
		} else if (resp == 3) {
			MensajeConClase("El Nombre del Parametro ya esta en el sistema", "info", "Oops...");
		} else if (resp == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al Guardar el Parametro", "error", "Oops...");
		}
	});

}

const registrar_valor_Parametro = () => {
	let formData = new FormData(document.getElementById("GuardarValorParametro"));
	formData.append("idparametro", idparametro);
	enviar_formulario(`${ruta_genericas}agregar_valor_parametro`, formData, (resp) => {
		if (resp == 1) {
			MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
		} else if (resp == 2) {
			$("#GuardarValorParametro").get(0).reset();
			MensajeConClase("Valor Parametro Guardado", "success", "Proceso Exitoso!");
			Listar_valor_Parametros(idparametro);
		} else if (resp == 3) {
			MensajeConClase("El Nombre/Id Aux del Parametro ya esta en el sistema", "info", "Oops...");
		} else if (resp == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else if (resp == 4) {
			MensajeConClase("Hay algunos campos que son obligatorios", "info", "Oops...");
		} else {
			MensajeConClase("Error al Guardar el Parametro", "error", "Oops...");
		}
	});
}
const cambio_estado_parametro = (parametro, estado) => {
	consulta_ajax(`${ruta_genericas}cambio_estado_parametro`, { 'idparametro': parametro, estado }, (resp) => {
		if (resp == 1) {
			//MensajeConClase("Valor Parametro Eliminado con exito", "success", "Proceso Exitoso!");
			swal.close();
			Listar_valor_Parametros(idparametro);
		} else if (resp == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else {
			MensajeConClase("Error al Eliminar el Parametro", "error", "Oops...");
		}
	})
}

const obtener_valor_parametro_id = parametro => {
	consulta_ajax(`${ruta_genericas}obtener_valor_parametro_id`, { 'idparametro': parametro }, (resp) => {
		if (resp.length == 0) {
			MensajeConClase("El valor parametro se encuentra en estado oculto, para modificar debe activarlo.!", "info", "Oops...");
		} else {
			let { id, id_aux, valory, valor, valorx, valorz, valora, valorb } = resp[0];
			$('#id').val(id);
			$("#txtIdAux_modificar").val(id_aux);
			$(".txt_nombre_centro").val(valory);
			$("#txtValor_modificar").val(valor);
			$("#txtDescripcion_modificar").val(valorx);
			$("#txtValory_modificar").val(valory);
			$("#txtValorz_modificar").val(valorz);
			$("#txtValora_modificar").val(valora);
			$("#txtValorb_modificar").val(valorb);
			$("#ModalModificarParametro").modal();
			aux_id = id_aux;
		}
	})
}


const buscar_datos_valor_parametro = (id, tipo = 1) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta_genericas}obtener_datos_valor_parametro`, { id, tipo }, resp => {
			resolve(resp);
		});
	});
}

const traer_valores_parametro = (id_parametro, tipo = '') => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta_genericas}obtener_valores_parametro`, { 'idparametro': id_parametro }, (resp) => {
			if (tipo == '') {
				resolve(resp);
			} else {
				resolve(resp.filter(elemento => elemento.valorb == tipo));
			}
		});
	});
}

const obtener_valores_parametro = (parametro) => {
	return new Promise(resolve => {
		consulta_ajax(`${ruta_genericas}obtener_valores_parametro`, { 'idparametro': parametro }, (resp) => {
			resolve(resp);
		});
	});
}

const Cargar_parametro_buscado = (parametro, combo, mensaje, tipo = 'select') => {
	consulta_ajax(`${ruta_genericas}obtener_valores_parametro`, { 'idparametro': parametro }, (resp) => {
		$(combo).html(tipo == 'select' ? `<option value = ''>${mensaje}</option>` : ``);
		resp.map((elemento) => {
			let { valor, valorx, id } = elemento;
			if (valorx == 'Ninguna') valorx = '';
			$(combo).append(tipo == 'select' ? `<option value = "${id}" title="${valorx}">${valor}</option>` : ` <option title="${valorx}" id="${id}" value = '${valor}' > `);
		})
	});
}

const Cargar_parametro_buscado_aux = (parametro, combo, mensaje, tipo = 'select') => {
	consulta_ajax(`${ruta_genericas}obtener_valores_parametro`, { 'idparametro': parametro }, (resp) => {
		if (parametro == 21) PasarArray(resp);
		if (parametro == 26) PasarArray2(resp);
		if (parametro == 23) PasarArray3(resp);
		if (parametro == 33) Obtener_estados_compras(resp);
		$(combo).html(tipo == 'select' ? `<option value>${mensaje}</option>` : ``);
		resp.map((elemento) => {
			let { valor, valorx, id_aux } = elemento;
			if (valorx == 'Ninguna') valorx = '';
			$(combo).append(tipo == 'select' ? `<option value = "${id_aux}" title="${valorx}">${valor}</option>` : ` <option value = '${valor}' > `);
		});
	});
}

const modificar_valor_Parametro = (parametro) => {
	let formData = new FormData(document.getElementById("Modificar_valor_parametro"));
	formData.append("idparametro", parametro);
	formData.append("aux_id", aux_id);
	enviar_formulario(`${ruta_genericas}editar_valor_parametro`, formData, (resp) => {
		if (resp == 1) {
			MensajeConClase("Valor Parametro Modificado con exito", "success", "Proceso Exitoso!");
			$("#Modificar_valor_parametro").get(0).reset();
			$("#ModalModificarParametro").modal("hide");
			Listar_valor_Parametros(idparametro);
		} else if (resp == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else if (resp == 2) {
			MensajeConClase("Todos Los Campos Son Obligatorios", "info", "Oops...");
		} else if (resp == 3) {
			MensajeConClase("El Nombre/Id Aux que desea guardar ya esta en el sistema", "info", "Oops...");
		} else if (resp == 4) {
			MensajeConClase("Hay algunos campos que son obligatorios", "info", "Oops...");
		} else {
			MensajeConClase("Error al Modificar el Parametro", "error", "Oops...");
		}
	});
}

const Listar_cargos_departamento_combo = (combo, mensaje, iddepartamento, idesele) => {
	consulta_ajax(`${ruta_genericas}Listar_cargos_departamento`, { iddepartamento, 'general': 1 }, (resp) => {
		$(combo).html(`<option value=''>${mensaje}</option>`);
		resp.map((elemento) => {
			let { valor, id } = elemento;
			$(combo).append(`<option value= "${id}">${valor}</option>`);
		})
		if (idesele != 0) $("#cbxcargos_modifica").val(idesele);
	});

}

const confirmar_cambio_estado_parametro = (id, estado) => {

	swal({
		title: "Estas Seguro .. ?",
		text: "Tener en cuenta que al Modificar el estado del Parámetro se afectaran los Módulos en los cuales este se Utilizaba !",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Entiendo!",
		cancelButtonText: "No, cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				cambio_estado_parametro(id, estado);
			}
		});
}

const Mostrar_modal_modificar = id => {
	if (id == 0) MensajeConClase("Antes de Continuar Seleccione el Parametro a Modificar", "info", "Oops...");
	else obtener_valor_parametro_id(id);

}

const traerArraycargado = () => {
	return Arraycomboaux;
}

const buscar_parametro_id = (parametro) => {
	return new Promise(resolve => {
		$.ajax({
			url: Traer_Server() + "index.php/genericas_control/obtener_valor_parametro_id",
			dataType: "json",
			data: {
				idparametro: parametro
			},
			type: "post",
		}).done(datos => {
			resolve(datos);
		});
	});
}

const ver_detalle_parametro = data => {
	let { valory, valor, valorx, id, idparametro, relacion, des_relacion } = data;
	if (idparametro == 25) {
		$("#valory_parametro").html(relacion);
		$("#des_valory_parametro").html(des_relacion);
		$("#text_valory").html('Centro Costo');
		$("#text_valory_des").html('Descripción centro costo');
		$(".tr_valory").show('fast');
	} else {
		$(".tr_valory").hide('fast');
	}
	$("#nombre_parametro").html(valor);
	$("#descripcion_parametro").html(valorx);
	$("#modal_detalle_parametro").modal();
}


const mostrar_modal_permisos = id => {
	if (id == 0) MensajeConClase("Favor selecione el valor del parametro", "info", "Oops");
	else {
		$("#nombre_parametro_per").val('');
		traer_valores_permisos(-1);
		$("#ModalPermiso").modal();
	}
}

//optimizar lista_de_Permiso
const Listar_permiso = () => {
	consulta_ajax(`${Traer_Server()}index.php/genericas_control/Cargar_permiso`, { idpermisoparametro }, resp => {
		$("#listado_parametros_permiso").html("<option value=''> Seleccione Parametro</option>");
		resp.map((elemento) => {
			let { id, valorx, nombre } = elemento;
			$("#listado_parametros_permiso").append(`<option  title='${valorx}' data-toggle='popover' data-trigger='hover' value= '${id}'> ${id} - ${nombre}</option>`);
		});
	})

}
// traer valores permiso   
const traer_valores_permisos = idparametro => {
	consulta_ajax(`${Traer_Server()}index.php/genericas_control/traer_valores_permisos`, { idparametro, idvalorparametro }, resp => {
		$(`#tablapermisoparametro tbody`).off('click', '.habilitar').off('click', 'tr');
		$(`#tablapermisoparametro tbody`).off('click', '.deshabilitar').off('click', 'tr');
		const myTable = $("#tablapermisoparametro").DataTable({
			"destroy": true,
			"processing": true,
			'data': resp,
			"columns": [
				{
					"data": "id"
				},
				{
					"data": "valor"
				},
				{
					"data": "valorx"
				},
				{
					"render": function (data, type, full, meta) {
						let { id_permiso } = full;
						let resp = '<div class="btn-group btn-group-toggle" data-toggle="buttons"><label class="btn btn-primary active habilitar">Habilitar</label></div>';
						if (id_permiso != null) resp = '<div class="btn-group btn-group-toggle" data-toggle="buttons"><label class="btn btn-primary active deshabilitar">Deshabilitar</label></div>';
						return resp;
					}
				}

			],
			"language": get_idioma(),
			dom: 'Bfrtip',
			"buttons": [],
		});

		$('#tablapermisoparametro tbody').on('click', 'tr', function () {
			$("#tablapermisoparametro tbody tr").removeClass("warning");
			$(this).attr("class", "warning");
		});

		$('#tablapermisoparametro tbody').on('click', '.habilitar', function () {
			let { id, id_aux } = myTable.row($(this).parent().parent()).data();
			confirmar_cambio_permiso('Habilitar Permiso .?', '', () => {
				habilitar(id_auxvalorparametro, idvalorparametro, id_aux, id);
			});

		});
		$('#tablapermisoparametro tbody').on('click', '.deshabilitar', function () {
			let { id_permiso } = myTable.row($(this).parent().parent()).data();
			confirmar_cambio_permiso('Deshabilitar Permiso .?', '', () => {
				deshabilitar(id_permiso);
			});
		});

	});
	//agregar y quitar permisos
	const habilitar = (vp_principal, vp_principal_id, vp_secundario, vp_secundario_id) => {
		consulta_ajax(`${Traer_Server()}index.php/genericas_control/habilitar`, { vp_principal, vp_principal_id, vp_secundario, vp_secundario_id }, resp => {
			let { mensaje, tipo, titulo } = resp;
			let id_parametro_permiso = $("#nombre_parametro_per").val();
			if (tipo == 'success') {
				swal.close();
				traer_valores_permisos(id_parametro_permiso);
			} else MensajeConClase(mensaje, tipo, titulo);
		});
	}

	const deshabilitar = (id_permiso) => {
		consulta_ajax(`${Traer_Server()}index.php/genericas_control/deshabilitar`, { id_permiso }, resp => {
			let { mensaje, tipo, titulo } = resp;
			let id_parametro_permiso = $("#nombre_parametro_per").val();
			if (tipo == 'success') {
				swal.close();
				traer_valores_permisos(id_parametro_permiso);
			} else MensajeConClase(mensaje, tipo, titulo);
		});
	}

}

const confirmar_cambio_permiso = (title, text, callback) => {
	swal({
		title,
		text,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Entiendo!",
		cancelButtonText: "No, cancelar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				callback();
			}
		});
}

const obtener_valores_parametros_gen = idparametro => {
	return new Promise(resolve => {
		let url = `${ruta_genericas}obtener_valores_parametro`;
		consulta_ajax(url, { idparametro }, (resp) => {
			resolve(resp);
		});
	});
}

const pintar_datos_combo_gen = (datos, combo, mensaje, sele = '', clave = 'id') => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach(elemento => {
		let { id, id_aux, valor } = elemento;
		let llave = clave == 'id' ? id : id_aux;
		$(combo).append(`<option value='${llave}'> ${valor}</option>`);
	});
	$(combo).val(sele);
}

const obtener_valores_permiso = (vp_principal, idparametro, tipo = 1) => {
	return new Promise(resolve => {
		const ruta = `${Traer_Server()}index.php/pages/`;
		let url = `${ruta}obtener_valores_permiso`;
		consulta_ajax(url, { vp_principal, idparametro, tipo }, resp => {
			resolve(resp);
		});
	});
}

const pintar_datos_combo_general = (datos, combo, mensaje, sele = "") => {
	$(combo).html(`<option value=''> ${mensaje}</option>`);
	datos.forEach(element => {
		$(combo).append(
			`<option value='${element.id}'> ${element.valor} </option>`
		);
	});
	$(combo).val(sele);
}

const buscar_parametro = (valor_buscado, tipo_modal) => {
	console.log(valor_buscado+" MODAL DE:", tipo_modal);

	consulta_ajax(`${ruta_genericas}buscar_parametro`, { valor_buscado }, (resp) => {
		let myTable;
		if(tipo_modal == 0){
				$("#table_parametro_buscado tbody")
				.off("dblclick", "tr")
				.off("click", "tr")
				.off("click", "tr td:nth-of-type(1)")
				.off("click", "tr .asignar");
			myTable = $("#table_parametro_buscado").DataTable({
				destroy: true,
				searching: false,
				processing: true,
				data: resp,
				columns: [
					{
						data: "id",
					},
					{
						data: "nombre",
					},		
					{					
						defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Parámetro" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" ></span>',
					},
				],
				language: idioma,
				dom: "Bfrtip",
				buttons: [],
			});

			$('#table_parametro_buscado tbody').on('click', 'tr .asignar', function () {
				let { id, nombre } = myTable.row($(this).parent().parent()).data();			
				Listar_valor_Parametros(id);
				$("#modal_buscador_parametros").modal("hide");
				idparametro = id;
				$("#nombre_parametro_x").val(id+" - "+nombre);					
			});
		}else{
			$("#table_parametro_permiso tbody")
				.off("dblclick", "tr")
				.off("click", "tr")
				.off("click", "tr td:nth-of-type(1)")
				.off("click", "tr .asignar");
			myTable = $("#table_parametro_permiso").DataTable({
				destroy: true,
				searching: false,
				processing: true,
				data: resp,
				columns: [
					{
						data: "id",
					},
					{
						data: "nombre",
					},		
					{					
						defaultContent:
						'<span style="color: #39B23B;" title="Seleccionar Parámetro" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" ></span>',
					},
				],
				language: idioma,
				dom: "Bfrtip",
				buttons: [],
			});

			$('#table_parametro_permiso tbody').on('click', 'tr .asignar', function () {
				let { id, nombre } = myTable.row($(this).parent().parent()).data();
				idparametro = id;
				$("#ModalPermiso").modal();
				$("#modal_buscador_permisos").modal("hide");
				$("#nombre_parametro_per").val(id+" - "+nombre);
				console.log(idparametro)				
				traer_valores_permisos(idparametro);				
			});
		}	
	});
}