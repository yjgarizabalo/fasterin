var idioma = {
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
}
var id_cargo_sele = 0;
var idDepartamento = 0;
var exceptions = Array();
$(document).ready(function () {
	server = Traer_Server();

	$("#Recargar").click(function () {
		location.reload();
	});
	$("#listado_departamento_modulo").change(function () {
		idDepartamento = $(this).val();
		Listar_cargos_departamento(idDepartamento);
	});


	$("#Asignar_cargo").click(function () {
		if (idDepartamento == 0) {
			MensajeConClase("Seleccione Departamento al Cual desea Asignar El Nuevo Cargo", "info", "Oops...")
		} else {
			Listar_cargos_sin_Asignar_Departamento();
		}
	});


	$("#check_excep").click(() => {
		if ($("#check_excep").is(':checked')) {
			$(".excepciones").show('slow');
			Listar_cargos_departamento_combo("#cbx_cargo_excep", "Seleccione Cargo", idDepartamento, 0);
		} else {
			$(".excepciones").hide('slow');
			exceptions.map(obj => $("#cbx_cargo_excep").append(`<option value=${obj.index}>${obj.val}</option>`));
			limpiar_cargos();
		}
	});

	$("#Guardar_cargo_Departamento").submit(function () {
		Agregar_cargos_departamento();
		return false;
	});

	$("#Guardar_Jefe").submit(() => {
		if (idDepartamento != 0 && id_cargo_sele != 0) {
			asignar_jefe_individual();
		} else {
			const exc = get_excepciones(exceptions);
			asignar_jefe(exc);
		}
		Listar_cargos_departamento();
		return false;
	});

	$("#Retirar_cargo").click(function () {
		Confirmar_Retirar_cargo();
	});

	$("#cbx_depar").change(() => {
		limpiar_cargos();
		const valory = $("#cbx_depar").val().trim();
		Listar_cargos_departamento_combo(".cbx_cargos", "Seleccione Cargo", valory, 0);
	});

	$("#btnadd_excep").click(() => {
		const cargos = $("#cbx_cargo_excep");
		if (cargos.val() != '') {
			const index = cargos.val();
			const val = $("#cbx_cargo_excep option:selected").text();
			exceptions.push({
				index,
				val
			});
			$("#lista_excluidos").append(`<li>${val}</li>`);
			$(`#cbx_cargo_excep option[value=${index}]`).remove();
		} else {
			MensajeConClase("Seleccione una excepción", "info", "Oops...")
		}
		return false;
	});
});

const limpiar_cargos = () => {
	$('.cbx_cargos')
		.find('option')
		.remove()
		.end();
	$('.cbx_cargos').append("<option value=''>Seleccione Cargo</option>").val('');
	exceptions = Array();
	$('#lista_excluidos')
		.find('li')
		.remove()
		.end();
}

const asignar_jefe_individual = () => {
	const jefe = $("#cbxjefe").val();
	const dep = $("#cbx_depar").val();
	const cargo = id_cargo_sele;
	const data = {
		jefe,
		dep,
		cargo,
	};
	$.ajax({
		url: server + "index.php/genericas_control/asignar_jefe_individual",
		type: "post",
		dataType: "html",
		data
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}
		//Recibo los datos del php
		//si es -1 quiere decir que los campos estan vacios
		if (datos == -1) {
			MensajeConClase("Complete todos los campos", "info", "Oops...")
			return true;
			//si es 1 es porque asignó el jefe a todos los cargos
		} else if (datos == 1) {
			MensajeConClase("Jefe Asignado con exito", "success", "Proceso Exitoso.!")
			return true;
			//si es -1302 es porque no tiene permisos.
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...")
		} else {
			MensajeConClase("Error al Asignar el cargo, contacte al administrador", "error", "Oops...");
		}
	});
}

const asignar_jefe = excepciones => {
	const jefe = $("#cbxjefe").val();
	const dep = $("#cbx_depar").val();
	const data = {
		jefe,
		dep,
		excepciones
	};
	$.ajax({
		url: server + "index.php/genericas_control/asignar_jefe",
		type: "post",
		dataType: "html",
		data
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}
		//Recibo los datos del php
		//si es -1 quiere decir que los campos estan vacios
		if (datos == -1) {
			MensajeConClase("Complete todos los campos", "info", "Oops...")
			return true;
			//si es 1 es porque asignó el jefe a todos los cargos
		} else if (datos == 1) {
			MensajeConClase("Jefe Asignado con exito", "success", "Proceso Exitoso.!")
			return true;
			//si es -1302 es porque no tiene permisos.
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...")
		} else {
			MensajeConClase("Error al Asignar el cargo, contacte al administrador", "error", "Oops...");
		}
	});
}

//Retorna array con los ID de los cargos que seran exceptuados para la asignación de jefe.
const get_excepciones = excepciones => {
	let exc = Array();
	excepciones.map(ex => {
		exc.push(ex.index);
	});
	return exc;
}


function Listar_departamentos() {
	idDepartamento = 0;
	$.ajax({
		url: server + "index.php/genericas_control/Cargar_valor_Parametros_normal",
		dataType: "json",
		data: {
			idparametro: 3
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			$("#listado_departamento_modulo").html("");

			$("#listado_departamento_modulo").append("<option value=''> Seleccione Departamento</option>");
			for (var i = 0; i <= datos.length - 1; i++) {
				$("#listado_departamento_modulo").append("<option  title='" + datos[i].valorx + "' data-toggle='popover' data-trigger='hover' value= " + datos[i].id + ">" + datos[i].valor + "</option>");

			}

			;
		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function Listar_cargos_departamento() {

	id_cargo_sele = 0;
	$('#tabla_cargos_departamentos tbody').off('dblclick', 'tr');
	$('#tabla_cargos_departamentos tbody').off('click', 'tr');


	var myTable = $("#tabla_cargos_departamentos").DataTable({
		"destroy": true,
		"ajax": {
			url: server + "index.php/genericas_control/Listar_cargos_departamento",
			dataType: "json",
			data: {
				iddepartamento: idDepartamento,
				general: 0,
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

		"columns": [{
			"data": "indice"
		},
		{
			"data": "valor"
		},
		{
			"data": "jefe"
		},
		{
			"data": "estado"
		},
		{
			"data": "op"
		},
		],
		"language": idioma,
		dom: 'Bfrtip',
		"buttons": [{
			extend: 'excelHtml5',
			text: '<i class="fa fa-file-excel-o"></i>',
			titleAttr: 'Excel',
			className: 'btn btn-success',
		},
		{
			extend: 'csvHtml5',
			text: '<i class="fa fa-file-text-o"></i>',
			titleAttr: 'CSV',
			className: 'btn btn-default',
		},
		{
			extend: 'pdfHtml5',
			text: '<i class="fa fa-file-pdf-o"></i>',
			titleAttr: 'PDF',
			className: 'btn btn-danger2',
		}
		],
	});

	$('#tabla_cargos_departamentos tbody').on('click', 'tr', function () {
		var data = myTable.row(this).data();
		id_cargo_sele = data.id;
		$("#tabla_cargos_departamentos tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});
	$('#tabla_cargos_departamentos tbody').on('dblclick', 'tr', function () {
		var data = myTable.row(this).data();


	});


}
//En este metodo Guardo los parametros que maneja el sistema
function Agregar_cargos_departamento() {

	//obtengo el formulario de registro de parametros
	var formData = new FormData(document.getElementById("Guardar_cargo_Departamento"));
	formData.append("iddepartamento", idDepartamento);
	// Envio los datos a mi archivo PHP y le envio por get la funcion que va a realizar
	$.ajax({
		url: server + "index.php/genericas_control/Agregar_cargos_departamento",
		type: "post",
		dataType: "html",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {

		if (datos == "sin_session") {
			close();
			return;
		}
		//Recibo los datos del php
		//si es un quiere decir que los campos estan vacios
		if (datos == 1) {

			MensajeConClase("Seleccione Cargo", "info", "Oops...")
			return true;
			//si es dos es por que guardo el parametro
		} else if (datos == 2) {

			MensajeConClase("El Cargo seleccionado solo esta disponible para el departamento de Sistemas.", "info", "Oops...")
			return true;
			//si es dos es por que guardo el parametro
		} else if (datos == 0) {

			MensajeConClase("Cargo Asignado Con exito", "success", "Proceso Exitoso!")

			Listar_cargos_departamento();
			Listar_cargos_sin_Asignar_Departamento();
			return true;
			// si es tres es por que el nombre del parametro existe
		} else if (datos == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...")
		} else {
			MensajeConClase("Error al Asignar el cargo, contacte al administrador", "error", "Oops...");
		}
	});
}

function Listar_cargos_sin_Asignar_Departamento() {

	$.ajax({
		url: server + "index.php/genericas_control/Listar_cargos_sin_Asignar_Departamento",
		dataType: "json",
		data: {
			iddepartamento: idDepartamento,
		},
		type: "post",
		success: function (datos) {

			var sw = 0;

			if (datos == "sin_session") {
				close();
				return;
			}
			$("#cbx_cargo").html("");
			$("#cbx_cargo").append("<option value=''>Seleccione Cargo</option>");
			for (var i = 0; i <= datos.length - 1; i++) {

				if (datos[i].id_cargo == null) {
					$("#cbx_cargo").append("<option value=" + datos[i].id + ">" + datos[i].valor + "</option>");
					sw = 1;
				}

			}
			if (sw == 0) {
				$("#cbx_cargo").html("");
				$("#cbx_cargo").append("<option value=''>Sin Cargos Por Asignar</option>");
			}

			$("#myModal").modal("show");
		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function confirmar_cambio_estado(id, estado) {
	if (id == 0) {
		MensajeConClase("Seleccione Cargo a Retirar", "info", "Oops...");
	} else {
		swal({
			title: "Estas Seguro .. ?",
			text: "Tener en cuenta que al Retirar el Cargo afectara a la persona que lo tenga asociado...",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#D9534F",
			confirmButtonText: "Si, Retirar!",
			cancelButtonText: "No, cancelar!",
			allowOutsideClick: true,
			closeOnConfirm: false,
			closeOnCancel: true
		},
			function (isConfirm) {
				if (isConfirm) {
					cambiar_estado_cargo(id, estado);
				}
			});
	}
}

function cambiar_estado_cargo(id, estado) {

	$.ajax({
		url: server + "index.php/genericas_control/cambiar_estado_cargo",
		dataType: "json",
		data: {
			id: id,
			estado: estado,
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			if (datos == 1) {
				//MensajeConClase("Estado Modificado Con éxito", "success", "Proceso Exitoso!");
				swal.close();
				Listar_cargos_departamento();

			} else if (datos == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...")
			} else {

				MensajeConClase("Error al cambair el estado", "error", "Oops...")
			}
		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});
}

function pasar_id_jefe(id) {
	id_cargo_sele = id;
	limpiar_cargos();
	$(".cbxj").val('');
	$("#cbxjefe").val('');
	if (idDepartamento == 0) {
		MensajeConClase("Seleccione Departamento al Cual desea Asignar Jefe", "info", "Oops...")
	} else if (idDepartamento != 0 && id_cargo_sele != 0) {
		$('#modalJefe').modal();
		$(".ind").fadeOut('fast');
	} else {
		$('#modalJefe').modal();
		$(".ind").fadeIn('slow');
	}
}
