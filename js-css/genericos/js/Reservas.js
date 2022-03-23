
let administra = false;
let correo_soli_fin = 0;
let nombre_completo_soli_fin = 0;
let nombre_completo_soli = "";
let max_recursos_prestamo = 1;
let id_usuario_sol = 0;
let recursos_sele = [];
let recursos_en_reserva = [];
let estado_Reserva = "";
let idpersona_seleccionada_re = 0;
let id_persona_reserva = 0;
let idreserva = 0;
let idreserva_fuera = 0;
let tipo_recurso = 0;
let idinventario = 0;
let tipo_repor = 0;
let correo_soli = "";
let modifica = 0;
let server_rese = "localhost";
let recurso_a_asignar = 0;
let fecha_salida_mas = "";
let fecha_entrega_mas = "";
let tipo_agregando = 1;
let tipo_reserva = 'Res_Nor';
let data_reserva = {
	persona_soli: 0,
	fecha_entrega: null,
	fecha_salida: null,
	tipo_estudio: null,
	tipo_prestamo: null,
	tipo_entrega: null,
	lugar: "Audiovisuales",
	asignatura: "Evaluación Sumativa"
}
let callback_solicitante = (resp) => {
	asignar(resp);
};
$(document).ready(function () {
	$("#mover_ojo").css("-webkit-animation", "tiembla 0.4s infinite");
	server_rese = Traer_Server();
	$("#listado").click(function () {
		$("#menu_principal").css("display", "none");
		$(".listado_reservas").fadeIn(1000);


	});

	$("#regresar_add").click(function () {
		$(".listado_reservas").css("display", "none");
		$("#menu_principal").fadeIn(1000);

	});
	$("input[name='estrellas']").click(function () {
		$(this).blur();
	});

	iniciar_tabla_persona();
	$("#por_fecha_reserva").change(function () {
		$("#fecha_sale_agrega").val("");
		$("#fecha_sale_agrega").removeAttr("required", "true");
		$("#fecha_sale_agrega").removeAttr("name", "fecha_salida");
		$("#fecha_sale_agrega").removeAttr("id", "fecha_sale_agrega");


		if ($(this).is(':checked')) {

			$("#div_entre_horas").hide("fast");
			$("#div_entre_fecha").show("fast");
			$("#div_entre_fecha input").attr("required", "true");
			$("#div_entre_fecha input").attr("name", "fecha_salida");
			$("#div_entre_fecha input").attr("id", "fecha_sale_agrega");

		} else {

			$("#div_entre_fecha").hide("fast");
			$("#div_entre_horas").show("fast");
			$("#div_entre_horas input").attr("required", "true");
			$("#div_entre_horas input").attr("name", "fecha_salida");
			$("#div_entre_horas input").attr("id", "fecha_sale_agrega");
		}
	});

	$("#generar_reporte").click(function () {
		Listar_reservas_audivisuales(0);
	});

	$("#comentar").click(function () {
		if (estado_Reserva == "Res_Recib" || estado_Reserva == "Res_Canc") {
			MensajeConClase("La Reserva esta en proceso o Terminada por lo cual no es posible Continuar.!", "info", "Oops...");
			return;
		}
		var comentario = $("#comentario").val().trim();
		guardar_comentario(idreserva, comentario);
	});
	$("#listar_comentarios").click(function () {
		listar_comentario(idreserva);
	});

	$("#retirar_recurso_sele").click(function () {
		var s = $("#recursos_agregados").val();

		if (s.length == 0) {
			MensajeConClase("Seleccione Recurso a Retirar..!", "info", "Oops...")
		} else {
			swal({
				title: "Estas Seguro ?",
				text: "Atencion antes de continuar tener en cuenta que al retirar el recurso este no se tendra en cuenta al momento de terminar la reserva.!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#D9534F",
				confirmButtonText: "Si, Retirar!",
				cancelButtonText: "No, Regresar!",
				allowOutsideClick: true,
				closeOnConfirm: false,
				closeOnCancel: true
			},
				function (isConfirm) {
					if (isConfirm) {
						Retirar_recurso_reserva(s);
					}
				});

		}

	});
	$("#mas_recursos").click(function () {
		tipo_agregando = 1;
		var fecha_entrega = $("#fecha_entrega_agrega").val();
		var fecha_Salida = $("#fecha_sale_agrega").val();
		Validar_fechas(fecha_entrega, fecha_Salida);
		$("#Guardar_mas_recursos").hide("fast");
		return;

	});
	$("#Guardar_mas_recursos").click(function () {
		if (tipo_agregando == 2) {
			Guardar_mas_recursos();
		} else {
			MensajeConClase("Seleccione la Reserva para continuar con la asignacion de los recursos.!", "info", "Oops");
		}

	});

	$("#agregar_mas_recurso").click(function () {
		if (idreserva != 0) {
			if (estado_Reserva == "Res_Recib" || estado_Reserva == "Res_Canc") {
				MensajeConClase("La Reserva esta en proceso o Terminada por lo cual no es posible Continuar.!", "info", "Oops...");
				return;
			}
			tipo_agregando = 2;
			recursos_sele = [];
			recursos_en_reserva = [];
			obtener_recursos_reservados_por_reserva(idreserva);
			Validar_fechas(fecha_entrega_mas, fecha_salida_mas, 2);
			$(".rec_sele").html("0");
			$("#Guardar_mas_recursos").show("fast");
			return;
		} else {
			MensajeConClase("Seleccione la Reserva para continuar con la asignacion de los recursos.!", "info", "Oops");
		}

	});

	$("#btn_gestionar_entrega_retiro").click(function () {
		if (estado_Reserva != "Res_Soli") {
			MensajeConClase("La Reserva esta en proceso o Terminada por lo cual no es posible Continuar.!", "info", "Oops...");
			return;
		}
		var persona = $("#cbx_persona_reserva_entrega").val().trim();
		if (persona.length == 0) {
			MensajeConClase("Para continuar debe seleccionar la persona que gestiona la entrega de los recursos para la reserva!", "info", "Oops...");
			return;
		}
		confirmar_entrega_recibe(persona, idreserva, 1);
		return;

	});
	$("#btn_terminar_solicitud_reserva").click(function () {
		if (estado_Reserva != "Res_Entre") {
			MensajeConClase("La Reserva ya fue atendida por lo cual no es posible Continuar.!", "info", "Oops...");
			return;
		}
		var persona = $("#cbx_persona_reserva_terminar").val().trim();
		if (persona.length == 0) {
			MensajeConClase("Para continuar debe seleccionar la persona que gestiona la salida de los recursos en la reserva seleccionada!", "info", "Oops...");
			return;
		}
		confirmar_entrega_recibe(persona, idreserva, 2);
		return;

	});
	$("#ver-recursos-reserva").click(function () {

		traer_recursos_por_reserva(idreserva, estado_Reserva);
	});
	$("#detalle_persona_solicita").click(function () {

		obtener_datos_persona_id_completo(id_usuario_sol, ".nombre_perso", ".apellido_perso", ".identi_perso", ".tipo_id_perso", ".foto_perso", ".ubica_perso", ".depar_perso", ".cargo_perso", ".perfil_perso", ".celular");
		$("#Mostrar_detalle_persona").modal("show");
	});

	$("#form-ingresar-persona-identidades").submit(function () {
		registrarPersona_identidades(1);
		return false;
	});


	$("#departamento_sele_guardar").change(function () {
		var valory = $(this).val().trim();
		Listar_cargos_departamento_combo(".cbxcargos", "Seleccione Cargo", valory, 0);
	});
	$("#fechas_filtro").change(function () {
		var tipo = $(this).val().trim();
		if (tipo == 4 || tipo == 5) {
			$("#div_fecha_inicio_filtro").show("slow");
			$("#div_fecha_salida_filtro").hide("slow");
		} else if (tipo == 6 || tipo == 7) {
			$("#div_fecha_salida_filtro").show("slow");
			$("#div_fecha_inicio_filtro").hide("slow");
		} else if (tipo == 8) {
			$("#div_fecha_inicio_filtro").show("slow");
			$("#div_fecha_salida_filtro").show("slow");
		} else {
			$("#div_fecha_inicio_filtro").hide("slow");
			$("#div_fecha_salida_filtro").hide("slow");
		}
	});

	// Cargar_parametro_buscado_aux(6, ".cbx_tipo_recurso", "Seleccione Tipo recurso");
	$("#Guardar_reserva").submit(function () {
		if (recursos_sele.length == 0) {
			MensajeConClase("Seleccione Recursos a reservar", "info", "Oops...");
			return false;
		}
		Guardar_Reserva();
		return false;

	});
	$("#Modificar_reserva").submit(function () {

		Modificar_Reserva();
		return false;

	});
	$("#guardar_calificacion").submit(function () {

		Guardar_calificacion(idreserva);
		return false;

	});

	$("#buscar_sele_perso").click(function () {
		var datos = $("#input_persona_reserva").val().trim();
		if (datos.length == 0) {
			MensajeConClase("Ingrese Datos a Buscar", "info", "Oops...")
		} else {
			listar_Personas_reserva(datos);
		}
	});
	$("#marcar_rec_fuera").click(function () {

		if (idreserva_fuera == 0) {
			MensajeConClase("Seleccione Reserva Fuera de Fecha a Terminar", "info", "Oops...")
		} else {

			Marcar_entrega_recibe(-1, idreserva_fuera, 1, -22);
		}


	});

	$("#input_persona_reserva").keypress(function (e) {
		if (e.which == 13) {
			var datos = $("#input_persona_reserva").val().trim();
			if (datos.length == 0) {
				MensajeConClase("Ingrese Datos a Buscar", "info", "Oops...")
			} else {
				listar_Personas_reserva(datos);
			}
		}
	});

	$("#persona_solicita_seleccionada").click(function () {
		$("#input_persona_reserva").val("")
		listar_Personas_reserva("");
		$("#Modal_seleccionar_persona").modal("show");
	});

	$("#persona_solicita_seleccionada_modi").click(function () {
		$("#input_persona_reserva").val("")
		listar_Personas_reserva("");
		$("#Modal_seleccionar_persona").modal("show");
	});
	$("#agregar_nueva_persona").click(function () {

		$("#Registrar-persona").modal("show");

	});
	$("#cerrar_apartado").click(function () {
		$(".apartado_persona").hide("slow");
		$("#persona_existente").show("slow");
		$(".footer-add-persona").show("slow");



	});
	$("#sele_perso").click(function () {
		$("#input_persona_reserva").val("")
		listar_Personas_reserva("");
		$("#Modal_seleccionar_persona").modal("show");
	});
	$("#sele_perso_modi").click(function () {
		$("#input_persona_reserva").val("")
		listar_Personas_reserva("");
		$("#Modal_seleccionar_persona").modal("show");
	});
	$("#inicial_fecha").change(function () {
		$("#h_inicial").html($(this).val());


	});
	$("#final_fecha").change(function () {
		$("#h_final").html($(this).val());

	});

	$("#Recargar").click(function () {
		location.reload();
	});
	$(".agregar_reserva").click(function () {
		// $("#btn_mostrar_pruebas_estudiante").hide("fast");
		$("#Guardar_reserva").get(0).reset();
		limpiar_data_reserva();
		tipo_reserva = 'Res_Nor';
		$(".container_reserva_normal").show("fast");
		$(".container_reserva_normal .requerido_nor").attr("required", "true");
		callback_solicitante = (resp) => {
			asignar(resp);
		}
		$("#Modal-add-reserva").modal("show");
	});
	$("#btnlistar_inventario").click(function () {

		$(".tablaReservas").hide("fast")
		$(".tablainventario").show("slow")
	});
	$("#btnlistar_reservas").click(function () {

		$(".tablainventario").hide("fast")
		$(".tablaReservas").show("slow")

	});
	$('#reporte_fecha_filtro').on('click', function () {
		if ($(this).is(':checked')) {
			// Hacer algo si el checkbox ha sido seleccionado
			$("#usar_filtro").show("slow");
			$("#inicial_fecha").val("");
			$("#final_fecha").val("");
			$("#h_final").html("-");
			$("#h_inicial").html("-");
		} else {
			$("#usar_filtro").hide("slow");
			$("#inicial_fecha").val("");
			$("#final_fecha").val("");
			var fecha = new Date();
			$("#h_inicial").html(fecha.getDate() + "/" + (fecha.getMonth() + 1) + "/" + fecha.getFullYear() + " a las " + fecha.getHours() + ":" + fecha.getMinutes());
			$("#h_final").html((fecha.getHours() + 1) + ":" + fecha.getMinutes());

		}
	});


	$("#detalle_inventario").click(function () {
		obtener_detalle_inventario(idinventario, tipo_inventario);
	});



	$("#filtrar_datos_reserva").click(function () {

		$("#Modal_filtrar_reservas").modal("show");
	});
	$("#limpiar_filtros_reserva").click(function () {

		sin_filtros();
	});

	$("#ocultar_detalle_persona_solicita").click(function () {
		$("#tabla_persona_reserva").hide("slow");
	});

	$("#btn_calificar").click(function () {
		if (idreserva == 0) {
			MensajeConClase("Seleccione Reserva a Calificar", "info", "Oops...")
		} else {

			Puede_Calificar(idreserva);
		}
	});
	$("#btnmodificar_reserva").click(function () {
		if (idreserva == 0) {
			MensajeConClase("Seleccione Reserva a Modificar", "info", "Oops...")
		} else {
			if (estado_Reserva != "Res_Soli") {
				MensajeConClase("La Reserva esta en proceso o Terminada por lo cual no es posible Modificar.!", "info", "Oops...")
				return;
			}
			else if (tipo_reserva_sele != "Res_Nor") {
				MensajeConClase("Esta opción no esta disponible para este tipo de reserva.!", "info", "Oops...")
				return;
			}
			modifica = 1;
			obtener_info_reserva_id(idreserva, "no");

		}
	});

	$("#btn_reserva_pru_gen").click(function () {
		if (tipo_reserva != "Res_Pru") limpiar_data_reserva();
		tipo_reserva = 'Res_Pru';
		$(".container_reserva_normal").hide("fast");
		$(".container_reserva_normal .requerido_nor").removeAttr("required");

		callback_solicitante = (resp) => {
			asignar(resp);
			data_reserva.persona_soli = resp.id
			data_reserva.identificacion = resp.identificacion
			// traer_pruebas_estudiante(resp.identificacion);
			// $("#btn_mostrar_pruebas_estudiante").show("fast");
		}
	});

	$("#btn_reserva_nor").click(function () {
		// $("#btn_mostrar_pruebas_estudiante").hide("fast");
		if (tipo_reserva != "Res_Nor") limpiar_data_reserva();
		tipo_reserva = 'Res_Nor';
		$(".container_reserva_normal").show("fast");
		$(".container_reserva_normal .requerido_nor").attr("required", "true");
		callback_solicitante = (resp) => {
			asignar(resp);
		}
	});
	// $("#btn_mostrar_pruebas_estudiante").click(function () {
	// 	if (tipo_reserva == "Res_Pru") {
	// 		if (data_reserva.identificacion) traer_pruebas_estudiante(data_reserva.identificacion);
	// 		else MensajeConClase("Seleccione Estudiante para visualizar las pruebas.", "info", "Oops!");
	// 	} else MensajeConClase("Opción no habilitada para este tipo de reservas.", "info", "Oops!");

	// });



});

function obtener_detalle_inventario(id, tipo) {

	$.ajax({
		url: server_rese + "index.php/inventario_control/obtener_detalle_inventario_info",
		dataType: "json",
		data: {
			id: id,
			tipo: tipo,
		},
		type: "post",
	}).done(function (datos) {

		if (datos == "") {
			MensajeConClase("El dispositivo no Tiene Informacion Adicional", "info", "Oops...")
		} else
			if (tipo == "Port") {

				$(".valor_procesador").html(datos[0].procesador);
				$(".valor_discoduro").html(datos[0].disco_duro);
				$(".valor_memoria").html(datos[0].memoria);
				$(".valor_sistemaope").html(datos[0].sistema_operativo);
				$("#tabla_info_portatil").show("slow");

			} else if (tipo == "Torre" || tipo == "Mouse" || tipo == "Teclado" || tipo == "Monitor") {
				$(".valor_procesador").html(datos[0].procesador);
				$(".valor_discoduro").html(datos[0].disco_duro);
				$(".valor_memoria").html(datos[0].memoria);
				$(".valor_sistemaope").html(datos[0].sistema_operativo);
				$(".valor_torre").html(datos[0].torre);
				$(".valor_mouse").html(datos[0].mouse);
				$(".valor_teclado").html(datos[0].teclado);
				$(".valor_monitor").html(datos[0].monitor);
				$("#tabla_info_computador").show("slow");

			} else if (datos == "sin_session") {
				close();
			} else {
				MensajeConClase("El dispositivo no Tiene Informacion Adicional", "info", "Oops...")
			}

	});
}

function Guardar_Reserva() {
	let formData = null;
	let recurso_id = [];

	let parametros = {
		url: server_rese + "index.php/reservas_control/Guardar",
		type: "post",
		dataType: "json",
	};
	for (let i = 0; i < recursos_sele.length; i++) {
		recurso_id.push(recursos_sele[i].id);
	}
	if (tipo_reserva == 'Res_Nor') {
		formData = new FormData(document.getElementById("Guardar_reserva"));
		formData.append("recursos", recurso_id);
		formData.append("tipo_reserva", tipo_reserva);
		parametros.cache = false;
		parametros.contentType = false;
		parametros.processData = false;
	} else {
		data_reserva.recursos = recurso_id;
		data_reserva.tipo_reserva = tipo_reserva;
		data_reserva.fecha_entrega = $("#fecha_entrega_agrega").val();
		data_reserva.fecha_salida = $("#fecha_sale_agrega").val();
		data_reserva.descripcion = $("#observaciones_reserva").val();
		data_reserva.lugar = $("#lugar_entrega").val();
		formData = data_reserva;

	}
	parametros.data = formData;
	$.ajax(parametros).done(function (datos) {
		let datos0 = datos[0];
		if (datos0 == -1) {
			MensajeConClase("Seleccione Recursos a reservar", "info", "Oops...");
			return true;
		} else if (datos0 == -2) {
			MensajeConClase("Error al Registrar la Reserva", "error", "Oops...");
			return true;
		} else if (datos0 == -3) {
			MensajeConClase("La Hora de Salida debe Ser Mayor a la Hora de Entrega del Recurso", "info", "Oops...");
			return true;
		} else if (datos0 == -4) {
			MensajeConClase("La Fecha y hora  de Entrega debe Ser Mayor a la Fecha y Hora Actual", "info", "Oops...");
			return true;
		} else if (datos0 == -5) {
			MensajeConClase("Todos los campos son Obligatorios, a exepcion de la Asignatura y las Observaciones", "info", "Oops...");
			return true;
		} else if (datos0 == -6) {
			$("#Modal-add-reserva").modal("hide");
			let mensaje = armar_no_disponibles(datos[1]);
			MensajeConClase("La reserva fue guardada con exito, pero algunos recursos no fueron asignados ya que no se encuentran disponibles:\n\n" + mensaje, "info", "Oops...");
			recursos_en_reserva = [];
			recursos_sele = [];
			$("#Guardar_reserva").get(0).reset();
			$(".recursos_agregados").html("");
			$("#persona_solicita_seleccionada").html("Seleccione Persona Solicita");
			$(".recursos_agregados").append("<option value=''>" + "0 Recurso(s) a Reservar" + "</option>");
			Listar_reservas_audivisuales(0);
			return;
		} else if (datos0 == "sin_session") {
			close();
		} else if (datos0 == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
			return;
		} else if (datos0 == -8) {
			MensajeConClase("Buscar Persona que Solicita el recurso", "info", "Oops...");
			return;
		} else if (datos0 == 0) {
			MensajeConClase("Reserva Guardada con exito", "success", "Proceso Exitoso!");
			$("#Modal-add-reserva").modal("hide");
			$(".recursos_agregados").html("");
			$("#persona_solicita_seleccionada").html("Seleccione Persona Solicita");
			recursos_sele = [];
			$(".recursos_agregados").append("<option value=''>" + "0 Recurso(s) a Reservar" + "</option>");
			Listar_reservas_audivisuales(0);
			let ser = '<a href="' + server_rese + '/index.php/tecnologia/reservas"><b>AQUI</b></a>'
			let ds = " con una duracion de " + $("#fecha_sale_agrega").val() + " Horas";
			if ($("#fecha_sale_agrega").val().length > 3) ds = " Hasta el dia " + $("#fecha_sale_agrega").val();

			mensaje = "Su solicitud para la reserva de recursos educativos fue ingresada con exito para el dia " + $("#fecha_entrega_agrega").val() + ds + " lugar de entrega " + $("#lugar_entrega").val() + ".<br><br>puede validar la informaci&oacuten " + ser;
			let tipo = -1;
			$("#Guardar_reserva").get(0).reset();
			if (correo_soli.length != 0) tipo = 1;
			enviar_correo_personalizado("res", mensaje, correo_soli, nombre_completo_soli, "Solicitud Reserva CUC", "Solicitud Reserva AUD", "ParCod", tipo);

			return;
		}else if(datos0 == -9) {
			MensajeConClase("La fecha debe ser menor o igual a "+ datos[1], "info", "Oops...");
			return true;
		}
	});

}

function Guardar_mas_recursos() {

	var recurso_id = [];
	for (var i = 0; i < recursos_sele.length; i++) {
		recurso_id.push(recursos_sele[i].id);

	}

	$.ajax({
		url: server_rese + "index.php/reservas_control/Guardar_mas_recursos",
		type: "post",
		dataType: "json",
		data: {
			fecha_entrega: fecha_entrega_mas,
			fecha_salida: fecha_salida_mas,
			id: idreserva,
			recursos: recurso_id,
		},
	}).done(function (datos) {
		var datos0 = datos[0];
		if (datos0 == -1) {
			MensajeConClase("Seleccione Recursos a reservar", "info", "Oops...");
			return true;
		} else if (datos0 == -2) {
			MensajeConClase("Error al Registrar la Reserva", "error", "Oops...");
			return true;
		} else if (datos0 == -3) {
			MensajeConClase("La Hora de Salida debe Ser Mayor a la Hora de Entrega del Recurso", "info", "Oops...");
			return true;
		} else if (datos0 == -4) {
			MensajeConClase("La Fecha y hora  de Entrega debe Ser Mayor a la Fecha y Hora Actual", "info", "Oops...");
			return true;
		} else if (datos0 == -5) {
			MensajeConClase("Error al cargar la fecha de entrega o la fecha de salida", "info", "Oops...");
			return true;
		} else if (datos0 == -6) {

			var mensaje = armar_no_disponibles(datos[1]);
			MensajeConClase("Listado de  recursos que no fueron asignados ya que no se encuentran disponibles:\n\n" + mensaje, "info", "Oops...");
			traer_recursos_por_reserva(idreserva, estado_Reserva);
			$("#Modal_seleccionar_recursos").modal("hide");
			recursos_en_reserva = [];
			recursos_sele = [];
			return true;
		} else if (datos0 == "sin_session") {
			close();
		} else if (datos0 == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
		} else if (datos0 > 0) {
			MensajeConClase("", "success", "Recursos Asignados!");
			traer_recursos_por_reserva(idreserva, estado_Reserva);
			$("#Modal_seleccionar_recursos").modal("hide");
			recursos_en_reserva = [];
			recursos_sele = [];
			return;
		}
	});

}

function Modificar_Reserva() {



	var formData = new FormData(document.getElementById("Modificar_reserva"));
	formData.append("id", idreserva);
	$.ajax({
		url: server_rese + "index.php/reservas_control/Modificar_Reserva",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {

		var datos0 = datos[0];
		if (datos0 == 1) {
			MensajeConClase("Error al Modificar la Reserva, contacte con el administrador", "error", "Oops...")
			return true;
		}
		if (datos0 == 2) {
			MensajeConClase("Todos los campos son Obligatorios, a exepcion de la Asignatura y las Observaciones", "info", "Oops...")
			return true;
		} else if (datos0 == "sin_session") {
			close();
		} else if (datos0 == -1302) {
			MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...")
		} else if (datos0 == 3) {
			MensajeConClase("Buscar Persona que Solicita el recurso", "info", "Oops...")
		} else if (datos0 == 0) {
			MensajeConClase("Reserva Modificada Con Exito..!", "success", "Preceso Exitoso..!");
			$("#Modificar_reserva").get(0).reset();
			listar_Personas_reserva("");
			Listar_reservas_audivisuales(0);
			$("#Modal-mod-reserva").modal("hide");
		}
	});

}

function Listar_reservas_audivisuales(iddata) {
	let estado_filtro = $("#estados_reserva_filtro").val();
	let fecha_filtro = $("#fechas_filtro").val();
	let finicial_filtro = $("#inicial_fecha_filtro").val();
	let ffinal_filtro = $("#final_fecha_filtro").val();
	let entrega_filtro = $("#tipos_entrega_filtro").val();
	idreserva = iddata;
	estado_Reserva = "";
	correo_soli_fin = 0;
	nombre_completo_soli_fin = 0;
	$('#tablaReservas tbody').off('dblclick', 'tr');
	$('#tablaReservas tbody').off('click', 'tr');
	$('#tablaReservas tbody').off('click', 'tr td:nth-of-type(1)');
	let myTable = $("#tablaReservas").DataTable({
		"destroy": true,
		"ajax": {
			url: server_rese + "index.php/reservas_control/Cargar_Reservas",
			dataType: "json",
			type: "post",
			data: {
				estado: estado_filtro,
				fecha: fecha_filtro,
				finicial: finicial_filtro,
				ffinal: ffinal_filtro,
				entrega: entrega_filtro,
				id: iddata
			},
			"dataSrc": function (json) {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			},
		},
		"processing": true,

		"columns": [{
			"data": "codigo"
		},
		{
			"data": "id_usuario"
		},
		{
			"data": "fecha_entrega"
		},
		{
			"data": "fecha_salida"
		},
		{
			"data": "lugar"
		},
		{
			"data": "id_tipo_entrega"
		},
		{
			"data": "estado"
		},
		{
			"data": "gestionar"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": get_botones(),
	});

	$('#tablaReservas tbody').on('click', 'tr', function () {
		let data = myTable.row(this).data();
		modifica = 0;
		tipo_reserva_sele = data.tipo_reserva;
		idreserva = data.id;
		estado_Reserva = data.estado2;
		tipo_recurso = data.tipo_recurso;
		fecha_salida_mas = data.fecha_salida;
		fecha_entrega_mas = data.fecha_entrega;
		$("#tablaReservas tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		correo_soli_fin = data.correo_soli;
		nombre_completo_soli_fin = data.id_usuario;
	});

	$('#tablaReservas tbody').on('dblclick', 'tr', function () {
		let data = myTable.row(this).data();
		obtener_info_reserva_tabla_id(data);
	});

	$('#tablaReservas tbody').on('click', 'tr td:nth-of-type(1)', function () {
		let data = myTable.row($(this).parent()).data();
		obtener_info_reserva_tabla_id(data);
	});

	Con_filtros(iddata);

}


function Listar_inventario_audivisuales() {


	idinventario = 0;
	$('#tablainventario tbody').off('dblclick', 'tr');
	$('#tablainventario tbody').off('click', 'tr');


	var myTable = $("#tablainventario").DataTable({
		"destroy": true,
		"ajax": {
			url: server_rese + "index.php/reservas_control/Cargar_inventario_audiovisuales",
			dataType: "json",
			type: "post",
			"dataSrc": function (json) {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			},
		},
		"processing": true,

		"columns": [{
			"data": "serial"
		},
		{
			"data": "codigo_interno"
		},
		{
			"data": "recurso"
		},
		{
			"data": "marca"
		},
		{
			"data": "modelo"
		},
		{
			"data": "fecha_ingreso"
		},
		{
			"data": "fecha_garantia"
		},
		{
			"data": "descripcion"
		},
		{
			"data": "estado_recurso"
		},
		],
		"language": get_idioma(),
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

	$('#tablainventario tbody').on('click', 'tr', function () {
		var data = myTable.row(this).data();

		idinventario = data.id;



		$("#tablainventario tbody tr").removeClass("warning");
		$(this).attr("class", "warning");

	});
	$('#tablainventario tbody').on('dblclick', 'tr', function () {
		var data = myTable.row(this).data();
		$("#tabla_info_portatil").hide("fast");
		$("#tabla_info_computador").hide("fast");
		obtener_info_inventario_id(data.id);


	});


}

function obtener_info_inventario_id(id) {


	$.ajax({
		url: server_rese + "index.php/inventario_control/obtener_valores_inventario_info",
		dataType: "json",
		data: {
			id: id
		},
		type: "post",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}

		$(".valor_recurso").html(datos[0].tipo_valor);
		$(".valor_garantia").html(datos[0].fecha_garantia);
		$(".valor_ingreso").html(datos[0].fecha_ingreso);
		$(".valor_cod_in").html(datos[0].codigo_interno);
		$(".valor_marca").html(datos[0].marca);
		$(".valor_modelo").html(datos[0].modelo);
		$(".valor_serial").html(datos[0].serial);
		$(".valor_valor").html(datos[0].valor);
		$(".valor_descripcion").html(datos[0].descripcion);
		tipo_inventario = datos[0].tipo;
		$("#Modal-info-dispositivo").modal("show");

	});
}

function obtener_info_reserva_id(id, con) {
	$.ajax({
		url: server_rese + "index.php/reservas_control/obtener_datos_reserva",
		dataType: "json",
		data: {
			id: id,
			conasig: con,
		},
		type: "post",
	}).done(function (datos) {
		if (datos == "sin_session") {
			close();
			return;
		}

		$("#cbxrecursos_modi").val(datos[0].tipo_recurso);
		$("#input_sele_re_modi").val(datos[0].id_usuario);

		$("#persona_solicita_seleccionada_modi").html(datos[0].nombre + " " + datos[0].apellido + " " + datos[0].segundo_apellido);
		$("#fecha_entrega_modi").val(datos[0].fecha_entrega);
		$("#fecha_salida_modi").val(datos[0].horas);
		$("#tipo_estudio_modi").val(datos[0].id_tipo_clase);
		$("#tipo_prestamo_modi").val(datos[0].id_tipo_prestamo);
		$("#tipo_entrega_modi").val(datos[0].id_tipo_entrega);
		$("#lugar_entrega_modi").val(datos[0].lugar);
		$("#asignatura_modi").val(datos[0].asignatura);
		$("#observaciones_modi").val(datos[0].observaciones);

		$("#Modal-mod-reserva").modal("show");
		return;


	});
}


function obtener_info_reserva_tabla_id(datos) {

	$(".valor_solicitante").html(datos.id_usuario);
	$(".valor_ingreso").html(datos.fecha_entrega);
	$(".valor_salida").html(datos.fecha_salida);
	$(".valor_estado").html(datos.estado);
	$(".valor_observaciones").html(datos.observaciones);

	$(".valor_tipo_entrega").html(datos.id_tipo_entrega);
	$(".valor_lugar").html(datos.lugar);
	$(".valor_tipo_prestamo").html(datos.tipo_prestamo);
	$(".valor_tipo_clase").html(datos.tipo_clase);

	$(".valor_asignatura").html(datos.asignatura);
	$(".valor_registra").html(datos.datos_registra_completo);

	if (datos.calificacion == null) {
		$(".valor_calificacion").html("-----");

	} else {
		$(".valor_calificacion").html(datos.calificacion);
		$(".valor_calificacion_obv").html(datos.observaciones_cali);
	}
	if (datos.usuario_cancela != null) {
		$(".active-re").css("display", "none");
		$(".valor_persona_cancela").html(datos.usuario_cancela + " | " + datos.fecha_cancela);
		$(".is-canc-re").show("fast");
	} else {
		$(".is-canc-re").css("display", "none");
		$(".active-re").show("fast");
	}
	if (datos.datos_retira_completo == null) {
		$(".valor_persona_recibe").html("-----");

	} else {
		$(".valor_persona_recibe").html(datos.datos_retira_completo + " | " + datos.fecha_real_recibe);

	}


	if (datos.datos_entrega_completo == null) {
		$(".valor_persona_entrega").html("-----");

	} else {
		$(".valor_persona_entrega").html(datos.datos_entrega_completo + " | " + datos.fecha_real_entrega);

	}
	id_usuario_sol = datos.id_usuario_sol;
	if (datos.tipo_reserva == 'Res_Nor') $("#detalle_persona_solicita").show("fast")
	else $("#detalle_persona_solicita").hide("fast")
	$("#Modal-info-dispositivo-reserva").modal("show");
}

function Cargar_personas_audiovisuales() {

	$.ajax({
		url: server_rese + "index.php/personas_control/obtener_datos_personas_audiovisuales",
		dataType: "json",
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			$(".cbx_persona_reserva").html("");
			$(".cbx_persona_reserva").append("<option value=''>Seleccione Persona</option>");

			for (var i = 0; i <= datos.length - 1; i++) {
				$(".cbx_persona_reserva").append("<option   value= " + datos[i].id + ">" + datos[i].nombre + " " + datos[i].apellido + " " + datos[i].segundo_apellido + "</option>");

			}

			;
		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function Marcar_persona_entrega_recibe_reserva(idpersona, idreserva, tipo) {

	$.ajax({
		url: server_rese + "index.php/reservas_control/Marcar_persona_entrega_recibe_reserva",
		dataType: "json",
		data: {
			idpersona: idpersona,
			idreserva: idreserva,
			tipo: tipo,
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
				return;
			}
			if (datos == -1) {
				MensajeConClase("No se puede gestionar la entrega ya que la reserva no cuenta recursos asignados.!", "info", "Oops!");
				return;
			} else
				if (datos == -2) {
					MensajeConClase("Seleccione Persona que Entrega los recursos.!", "info", "Oops!");
					return;
				} else if (datos == 4) {

					if (tipo == 1) {
						swal.close();
						//MensajeConClase("", "success", "Reserva Entregada!");
						$("#Modal_mis_recursos").modal("hide");
						$("#Modal-info-dispositivo-reserva").modal("hide");

					} else {
						swal.close();
						//MensajeConClase("", "success", "Reserva Recibida!");
						//$("#Modal_responder_reserva").modal("hide");
						let ser = '<a href="' + server_rese + 'index.php/tecnologia/reservas/' + idreserva + '"><b>agil.cuc.edu.co</b></a>'
						let mensaje = `Se informa que su solicitud de reserva Audiovisual ha finalizado. Favor diligenciar la Encuesta de satisfacci&oacuten al cliente.<br><br>Mas informaci&oacuten en ${ser}`;
						enviar_correo_personalizado("res", mensaje, correo_soli_fin, nombre_completo_soli_fin, "Solicitud Reserva CUC", "Solicitud Reserva AUD", "ParCod", 1);
					}
					Listar_reservas_audivisuales(0);

				} else if (datos == -1302) {
					MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...")
				} else {
					MensajeConClase("Error en la operacion", "error", "Oops...")
				}
		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function Modificar_estado_reserva(id, estado) {

	$.ajax({
		url: server_rese + "index.php/reservas_control/Modificar_estado_reserva",
		dataType: "json",
		data: {
			id: id,
			estado: estado,
		},
		type: "post",
		success: function (datos) {

			if (datos == 1) {
				swal.close();
				Listar_reservas_audivisuales(0);
				//	MensajeConClase("La Reserva Fue cancelada", "success", "Proceso Exitoso!");
			} else if (datos == 2) {
				MensajeConClase("La Reserva ya fue Cancelada Anteriormente o se Encuentra en Proceso", "info", "Oops...")
			} else if (datos == "sin_session") {
				close();
			} else if (datos == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...")
			} else {
				MensajeConClase("Error en la operacion", "error", "Oops...")
			}
		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function guardar_comentario(id, comentario) {

	$.ajax({
		url: server_rese + "index.php/reservas_control/guardar_comentario",
		dataType: "json",
		data: {
			id,
			comentario
		},
		type: "post",
		success: function (datos) {

			if (datos == 1) {
				MensajeConClase("", "success", "Comentario Enviado!");
				$("#comentario").val("");
			} else if (datos == -2) {
				MensajeConClase("Antes de enviar debe Ingresar el comentario", "info", "Oops...");
			} else if (datos == "sin_session") {
				close();
			} else if (datos == -1302) {
				MensajeConClase("No tiene Permisos Para Realizar Esta Opereacion", "error", "Oops...");
			} else {
				MensajeConClase("Error al enviar el comentario", "error", "Oops...");
			}
		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function listar_comentario(id) {


	$('#tabla_comentarios tbody').off('click', 'tr');
	$('#tabla_comentarios tbody').off('dblclick', 'tr');

	var table = $("#tabla_comentarios").DataTable({
		"destroy": true,
		"ajax": {
			url: server_rese + "index.php/reservas_control/listar_comentario",
			dataType: "json",
			type: "post",
			data: {
				id
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
		"columns": [{
			"data": "comentario"
		},
		{
			"data": "usuario"
		},
		{
			"data": "fecha_registro"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []

	});
	$("#Modal_comentarios_reserva").modal("show");
}


function listar_Personas_reserva(dato) {
	if (dato.length > 4 || dato.length == 0) {
		if (dato) $("#cargando_data_persona").show("slow");
		consulta_ajax(`${server_rese}index.php/reservas_control/cargar_personas_reserva`, { dato, "tipo": tipo_reserva }, data => {
			$("#cargando_data_persona").hide("slow");
			idpersona_seleccionada_re = 0;
			$('#tablapersonas_reserva tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .asignar');
			let table = $("#tablapersonas_reserva").DataTable({
				"destroy": true,
				data,
				searching: false,
				"processing": true,
				"columns": [{
					"data": "nombre"
				},
				{
					"data": "identificacion"
				},
				{
					"data": "correo"
				},
				{
					"defaultContent": `<span style="color: #39B23B;" title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default asignar" ></span>`,
				}
				],
				"language": get_idioma(),
				dom: 'Bfrtip',
				"buttons": []

			});

			$('#tablapersonas_reserva tbody').on('click', 'tr', function () {
				$("#tablapersonas_reserva tbody tr").removeClass("success");
				$(this).attr("class", "success");
			});
			$('#tablapersonas_reserva tbody').on('dblclick', 'tr', function () {
				let data = table.row(this).data();
				callback_solicitante(data);
			});
			$('#tablapersonas_reserva tbody').on('click', 'tr td .asignar', function () {
				let data = table.row($(this).parent()).data();
				callback_solicitante(data);
			});

		});
	} else {
		MensajeConClase("Ingrese Mas información para la busqueda", "info", "Oops.!");
	}

}

const asignar = (data) => {
	$("#persona_solicita_seleccionada").html(data.nombre);
	$("#persona_solicita_seleccionada_modi").html(data.nombre);
	idpersona_seleccionada_re = data.id;
	correo_soli = data.correo;
	nombre_completo_soli = data.nombre;
	$("#input_sele_re").val(data.id);
	$("#input_sele_re_modi").val(data.id);
	$("#Modal_seleccionar_persona").modal("hide");
}

function listar_recursos_disponibles(fecha_entrega, fecha_salida) {
	$('#tabla_recursos_disponibles tbody').off('click', 'tr');
	$('#tabla_recursos_disponibles tbody').off('dblclick', 'tr');
	$('#tabla_recursos_disponibles tbody').off('click', 'tr .seleccionar');

	let table = $("#tabla_recursos_disponibles").DataTable({
		"destroy": true,
		"pageLength": 5,
		"ajax": {
			url: server_rese + "index.php/reservas_control/obtener_recursos_audiovisuales_combo",
			dataType: "json",
			type: "post",
			data: {
				fecha_entrega: fecha_entrega,
				fecha_salida: fecha_salida,

			},
			"dataSrc": function (json) {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			},

		},
		"processing": true,
		"columns": [{
			"data": "recurso"
		}, {
			"data": "total"
		}, {
			"data": "normales"
		}, {
			"data": "especiales"
		},
		{
			"data": "disponibles"
		},
		{
			"defaultContent": `<span style="color: #39B23B;" title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>`,
		}
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []

	});

	$('#tabla_recursos_disponibles tbody').on('dblclick', 'tr', function () {
		let data = table.row(this).data();
		sele_articulo_add(data, this);

	});
	$('#tabla_recursos_disponibles tbody').on('click', 'tr .seleccionar', function () {
		let data = table.row($(this).parent().parent()).data();
		sele_articulo_add(data, $(this).parent().parent());
	});

	const sele_articulo_add = (data, thiss) => {
		$("#tabla_recursos_disponibles tbody tr").removeClass("warning");
		$(thiss).attr("class", "warning");
		var fecha_entrega = "";
		var fecha_Salida = "";
		if (tipo_agregando == 1) {
			fecha_entrega = $("#fecha_entrega_agrega").val();
			fecha_Salida = $("#fecha_sale_agrega").val();
		} else {
			fecha_entrega = fecha_entrega_mas;
			fecha_Salida = fecha_salida_mas;
		}
		existe_recurso_selecionado_prestamo(fecha_entrega, fecha_Salida, data);
		return false;
	}
	table.column(1).visible(administra);
	table.column(2).visible(administra);
	table.column(3).visible(administra);
	table.column(4).visible(administra);
	$("#Modal_seleccionar_recursos").modal("show");
}

function existe_recurso_selecionado_prestamo(fecha_entrega, fecha_Salida, data) {
	var total_reserva = 0;
	var total = 0;
	var recurso = data.id;
	var array = [];
	if (tipo_agregando == 2) {
		for (var i = 0; i <= recursos_en_reserva.length - 1; i++) {
			if (recursos_en_reserva[i].id == recurso) {
				total_reserva++;
			}

		}
	}

	for (var i = 0; i <= recursos_sele.length - 1; i++) {
		if (recursos_sele[i].id == recurso) {
			total++;
		}

	}


	if (tipo_agregando == 2) {
		var totalfin = total + total_reserva;
		if (total >= data.disponibles) {
			MensajeConClase("Este Recurso no se encuentra disponible.!", "info", "Oops...");
			return false;
		}
		if (totalfin >= max_recursos_prestamo) {
			MensajeConClase("Ya ha ocupado el limite máximo de recurso por reserva ,tener en cuenta que puede reservar por recurso un total de " + max_recursos_prestamo + ".!", "info", "Oops...");
			return false;
		}
	} else {
		if (total >= max_recursos_prestamo) {
			MensajeConClase("Ya ha ocupado el limite máximo de recurso por reserva ,tener en cuenta que puede reservar por recurso un total de " + max_recursos_prestamo + ".!", "info", "Oops...");
			return false;
		}

		if (total >= data.disponibles) {
			MensajeConClase("Este Recurso no se encuentra disponible.!", "info", "Oops...");
			return false;
		}
	}

	Validar_disponibilidad(recurso, fecha_entrega, fecha_Salida, data);
	return false;

	;


}

function Cargar_recursos_audiovisuales_combo(combo, datos) {

	$(combo).html("");
	$(combo).append("<option  value=''> " + datos.length + " Recursos a Reservar</option>");

	var sw = true;
	var total = 0;
	var mostrar = [];
	for (var i = 0; i <= datos.length - 1; i++) {
		total = 0;
		sw = true;
		for (var j = 0; j <= datos.length - 1; j++) {
			var datos_actuales = datos[i];
			if (datos[j].id == datos[i].id) {
				total++;

			}
		}
		for (let index = 0; index < mostrar.length; index++) {
			if (mostrar[index][0] == datos[i].id) {
				sw = false;

			}
		}
		if (sw) {
			var armar = [datos_actuales.id, datos_actuales.recurso, total];
			mostrar.push(armar);
		}

	}

	for (let index = 0; index < mostrar.length; index++) {
		const element = mostrar[index];
		$(combo).append("<option  value= " + element[0] + ">" + element[2] + " " + element[1] + "</option>");

	}
	$(".rec_sele").html(datos.length);;


}

function iniciar_tabla_persona() {
	idpersona_seleccionada_re = 0;
	correo_soli = "";
	$("#input_persona_reserva").val("")
	$("#input_sele_re").val("");
	$("#input_sele_re_modi").val("");
	$("#persona_solicita_seleccionada").html("Persona Solicita");
	$("#persona_solicita_seleccionada_modi").html("Persona Solicita");
	listar_Personas_reserva("");
}


function Guardar_calificacion(idreserva) {


	var formData = new FormData(document.getElementById("guardar_calificacion"));
	formData.append("id", idreserva);


	$.ajax({
		url: server_rese + "index.php/reservas_control/Agregar_Calificacion_Reserva",
		type: "post",
		dataType: "json",
		data: formData,
		cache: false,
		contentType: false,
		processData: false
	}).done(function (datos) {
		$("#Modal_calificar_reserva").modal("hide");
		if (datos == 1) {
			MensajeConClase("La Reserva Se Encuentra Cancelada o Aun Esta en Proceso", "info", "Oops...");
		} else if (datos == 2) {
			MensajeConClase("Ya se Le Fue Asignada una Calificacion Anteriormente", "info", "Oops...");
		} else if (datos == 4) {
			MensajeConClase("Calificacion Asignada Con Exito", "success", "Proceso Exitoso!");
			$("#guardar_calificacion").get(0).reset();
			Listar_reservas_audivisuales(0);
		} else {
			MensajeConClase("Error Al Calificar la Reserva", "error", "Oops...");
		}
	});

}

function Puede_Calificar(id) {

	$.ajax({
		url: server_rese + "index.php/reservas_control/Puede_Calificar",
		dataType: "json",
		data: {
			id: id,
		},
		type: "post",
		success: function (datos) {

			if (datos == 1) {
				MensajeConClase("La Reserva Se Encuentra Cancelada o Aun Esta en Proceso", "info", "Oops...")
			} else if (datos == 2) {
				MensajeConClase("Ya se Le Fue Asignada una Calificacion Anteriormente", "info", "Oops...")
			} else if (datos == 4) {
				$("#Modal_calificar_reserva").modal("show");
			} else {
				MensajeConClase("Error Al Mostrar el Formulario de Calificacion para la reserva", "error", "Oops...")
			}

		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function Validar_disponibilidad(recurso, fecha_entrega, fecha_salida, data) {

	$.ajax({
		url: server_rese + "index.php/reservas_control/Validar_disponibilidad",
		dataType: "json",
		data: {
			recurso: recurso,
			fecha_entrega: fecha_entrega,
			fecha_salida: fecha_salida,
		},
		type: "post",
		success: function (datos0) {

			if (datos0 == -1) {
				MensajeConClase("El recurso no se encuentra Disponible", "info", "Oops...");

				return true;
			} else if (datos0 == -2) {
				MensajeConClase("Error al Validar la disponibilidad", "error", "Oops...");
				return true;
			} else if (datos0 == -3) {
				MensajeConClase("La Fecha de Salida debe Ser Mayor a la Hora de Entrega del Recurso", "info", "Oops...");
				return true;
			} else if (datos0 == -4) {
				MensajeConClase("La Fecha y hora  de Entrega debe Ser Mayor a la Fecha y Hora Actual", "info", "Oops...");
				return true;
			} else if (datos0 == -14) {
				MensajeConClase("La Hora de Entrega solicitada no esta disponible!", "info", "Oops...");
				return true;
			} else if (datos0 == -15) {
				MensajeConClase("La Hora de salida solicitada no esta disponible!", "info", "Oops...");
				return true;
			} else if (datos0 == "sin_session") {
				close();
			} else if (datos0 == 1) {
				recursos_sele.push(data);
				Cargar_recursos_audiovisuales_combo(".recursos_agregados", recursos_sele);
				MensajeConClase("Atencion, el sistema valida la disponibilidad del recurso al momento de agregarlo,pero no se garantiza la disponibilidad al terminar la reserva ya que puede ser reservado por otro usuario!", "success", "Recurso Agregado.!!");
				return true;
			}
		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function Validar_fechas(fecha_entrega, fecha_salida, tipo = 1) {

	$.ajax({
		url: server_rese + "index.php/reservas_control/Validar_fechas_post",
		dataType: "json",
		data: {
			fecha_entrega: fecha_entrega,
			fecha_salida: fecha_salida,
			tipo
		},
		type: "post",
		success: function (datos0) {
			if (datos0 == -1) {
				MensajeConClase("Ingrese fecha de entrega y seleccione el numero de horas de la reserva .!", "info", "Oops...");
				return true;
			} else if (datos0 == -3) {
				MensajeConClase("La Fecha de Salida debe Ser Mayor a la fecha de Entrega del Recurso", "info", "Oops...");
				return true;
			} else if (datos0 == -4) {
				if (tipo_agregando == 2) {
					MensajeConClase("El tiempo para asignar mas recursos ya ha caducado.!", "info", "Oops...");
				} else {
					MensajeConClase("La Fecha y hora  de Entrega debe Ser Mayor a la Fecha y Hora Actual", "info", "Oops...");
				}

				return true;
			} else if (datos0 == -14) {
				MensajeConClase("La Hora de Entrega solicitada no esta disponible!", "info", "Oops...");
				return true;
			} else if (datos0 == -15) {
				MensajeConClase("La Hora de salida solicitada no esta disponible!", "info", "Oops...");
				return true;
			} else if (datos0 == "sin_session") {
				close();
			} else if (datos0 == 1) {
				if (tipo_agregando == 1) {
					buscar_reservas_activas_usuario_fecha(fecha_entrega, fecha_salida);
					return true;
				}
				listar_recursos_disponibles(fecha_entrega, fecha_salida);
				return true;
			}
		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function Retirar_recurso_reserva(dato) {
	for (var i = 0; i < recursos_sele.length; i++) {
		if (recursos_sele[i].id == dato) {
			recursos_sele.splice(i, 1);
			//MensajeConClase("", "success", "Recurso Retirado..!");
			swal.close();
			Cargar_recursos_audiovisuales_combo(".recursos_agregados", recursos_sele);
			return true;
		}
	}
	MensajeConClase("No fue posible retirar el recurso.!!", "info", "Oops..!");
}

function confirmacion_cancelar_reserva(idreserva) {
	swal({
		title: "Estas Seguro ?",
		text: "Tener en cuenta que al Cancelar la Reserva se Habilitara para su respectivo uso",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Cancelar!",
		cancelButtonText: "No, Regresar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				Modificar_estado_reserva(idreserva, "Res_Canc");
			}
		});
}

function traer_recursos_por_reserva(id, estado) {
	if (estado == 1) {
		estado_Reserva = "Res_Soli";
	} else {
		estado_Reserva = estado;
	}
	if (estado_Reserva == "Res_Soli" || estado_Reserva == "Res_Entre") {

		if (estado_Reserva == "Res_Soli") {
			$(".reserva_atendida").show("fast");
		} else {
			$(".reserva_atendida").hide("fast");
		}
		$("#agregar_mas_recurso").show("fast");
		$("#cerrar_no").hide("fast");
	} else {
		$(".reserva_atendida").hide("fast");
		$("#cerrar_no").show("fast");

	}
	$(".cbx_persona_reserva").val("");
	recurso_a_asignar = 0;
	$('#tabla_recursos_por_reserva tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .asignar').off('click', 'tr td .eliminar').off('click', 'tr td .entregar');

	var table = $("#tabla_recursos_por_reserva").DataTable({
		"destroy": true,
		"pageLength": 5,
		"ajax": {
			url: server_rese + "index.php/reservas_control/traer_recursos_por_reserva",
			dataType: "json",
			type: "post",
			data: {
				idreserva: id,
			},
			"dataSrc": function (json) {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			},
		},
		"processing": true,
		"columns": [{
			"data": "tipo"
		},
		{
			"data": "codigo_interno"
		}, {
			"data": "fecha_registra"
		},
		{
			"data": "gestionar"
		},
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []

	});

	$('#tabla_recursos_por_reserva tbody').on('click', 'tr', function () {
		let data = table.row(this).data();
		recurso_a_asignar = data.id;
		$("#tabla_recursos_por_tipo tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		$("#tabla_recursos_por_tipo .nombre_tabla").html(data.tipo);
	});

	$('#tabla_recursos_por_reserva tbody').on('click', 'tr td .asignar', function () {
		let { id_tipo_recurso } = table.row($(this).parent().parent()).data();
		obtener_recursos_audiovisuales_disponibles(id_tipo_recurso)
	});
	$('#tabla_recursos_por_reserva tbody').on('click', 'tr td .eliminar', function () {
		let { id } = table.row($(this).parent().parent()).data();
		confirmar_retirar_recurso_Reserva(id);
	});
	$('#tabla_recursos_por_reserva tbody').on('click', 'tr td .entregar', function () {
		let { id } = table.row($(this).parent().parent()).data();
		confirmar_retirar_recurso_Reserva(id);
	});


	$("#Modal_mis_recursos").modal("show");
}

function obtener_recursos_audiovisuales_disponibles(id) {

	$('#tabla_recursos_por_tipo tbody').off('click', 'tr').off('dblclick', 'tr').off('click', 'tr td .seleccionar');

	let table = $("#tabla_recursos_por_tipo").DataTable({
		"destroy": true,
		"pageLength": 5,
		"ajax": {
			url: server_rese + "index.php/reservas_control/obtener_recursos_audiovisuales_disponibles",
			dataType: "json",
			type: "post",
			data: {
				tipo: id,
			},
			"dataSrc": function (json) {
				if (json.length == 0) {
					return Array();
				}
				return json.data;
			},
		},
		"processing": true,
		"columns": [
			{
				"data": "estado_gen"
			},
			{
				"data": "codigo_interno"
			},
			{
				"data": "serial"
			},
			{
				"defaultContent": `<span style="color: #39B23B;" title="Asignar" data-toggle="popover" data-trigger="hover" class="fa fa-check btn btn-default seleccionar" ></span>`,
			}
		],
		"language": get_idioma(),
		dom: 'Bfrtip',
		"buttons": []

	});

	$('#tabla_recursos_por_tipo tbody').on('dblclick', 'tr', function () {
		let data = table.row(this).data();
		let recurso = data.id;
		$("#tabla_recursos_por_tipo tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		Marcar_persona_recurso_entrega_recibe(recurso_a_asignar, recurso, 1);
	});
	$('#tabla_recursos_por_tipo tbody').on('click', 'tr td .seleccionar', function () {
		let data = table.row($(this).parent().parent()).data();
		let recurso = data.id;
		$("#tabla_recursos_por_tipo tbody tr").removeClass("warning");
		$(this).attr("class", "warning");
		Marcar_persona_recurso_entrega_recibe(recurso_a_asignar, recurso, 1);
	});

	$("#Modal_mis_recursos_tipo").modal("show");
}

function Marcar_persona_recurso_entrega_recibe(id, id_recurso, tipo) {

	$.ajax({
		url: server_rese + "index.php/reservas_control/Marcar_persona_recurso_entrega_recibe",
		dataType: "json",
		data: {
			id: id,
			id_recurso: id_recurso,
			tipo: tipo
		},
		type: "post",
		success: function (datos) {

			if (datos == -1) {
				MensajeConClase("Error al gestionar la reserva.!", "error", "Oops...");
			} else if (datos == 0) {
				swal.close();
				if (tipo == -1) {
					//MensajeConClase("Recurso retirado con Exito.!", "success", "proceso Exitoso.!");
				} else {
					//MensajeConClase("Recurso Asignado con Exito.!", "success", "proceso Exitoso.!");
					$("#Modal_mis_recursos_tipo").modal("hide");

				}
				traer_recursos_por_reserva(idreserva, estado_Reserva);

			} else {
				MensajeConClase("Error al gestionar la reserva.!", "error", "Oops...");
			}

		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

function confirmar_entrega_recibe(persona, reserva, tipo) {
	var titulo = "";
	if (tipo == 1) {
		titulo = "Tener en cuenta que al hacer entrega de la reserva no podra realizar modificaciones futuras en esta.!";
	} else {
		titulo = "Tener en cuenta que al Terminar la reserva los recursos asignados serán liberados para su respectivo uso.!";
	}
	swal({
		title: "Estas Seguro ?",
		text: titulo,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Continuar!",
		cancelButtonText: "No, Regresar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				Marcar_persona_entrega_recibe_reserva(persona, reserva, tipo);
			}

		});
}

function confirmar_retirar_recurso_Reserva(id) {

	swal({
		title: "Estas Seguro ?",
		text: "Tener en cuenta que al retirar el recurso no se tendra en cuenta para la reserva.!",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#D9534F",
		confirmButtonText: "Si, Continuar!",
		cancelButtonText: "No, Regresar!",
		allowOutsideClick: true,
		closeOnConfirm: false,
		closeOnCancel: true
	},
		function (isConfirm) {
			if (isConfirm) {
				Marcar_persona_recurso_entrega_recibe(id, -1, -1);
			}

		});
}

function Gestionar_solicitud(id) {
	//$(".cbx_persona_reserva").val("");
	idreserva = id;
	//$("#Modal_responder_reserva").modal("show");
	confirmar_entrega_recibe(0, idreserva, 2);
}

function Con_filtros(id) {

	var estado_filtro = $("#estados_reserva_filtro").val();
	var fecha_filtro = $("#fechas_filtro").val();
	var finicial_filtro = $("#inicial_fecha_filtro").val();
	var ffinal_filtro = $("#final_fecha_filtro").val();
	var entrega_filtro = $("#tipos_entrega_filtro").val();

	if (estado_filtro.length != 0 || fecha_filtro.length != 0 || entrega_filtro.length != 0 || id > 0) {
		$(".mensaje-filtro").show("fast");
	} else {
		$(".mensaje-filtro").css("display", "none");
	}
}

function sin_filtros() {
	$("#estados_reserva_filtro").val("");
	$("#fechas_filtro").val("");
	$("#inicial_fecha_filtro").val("");
	$("#final_fecha_filtro").val("");
	$("#tipos_entrega_filtro").val("");
	Listar_reservas_audivisuales(0);
}

function Pasar_valor_max_recursos(valor) {
	max_recursos_prestamo = valor;
}

function obtener_recursos_reservados_por_reserva(id) {

	$.ajax({
		url: server_rese + "index.php/reservas_control/obtener_recursos_reservados_por_reserva",
		dataType: "json",
		data: {
			id: id,
		},
		type: "post",
	}).done(function (datos) {

		if (datos == "") {
			MensajeConClase("La reserva no Tiene recursos Asignados, para continuar con el proceso debe asignar al menos un recurso.!", "info", "Oops...")
		} else if (datos == "sin_session") {
			close();
		} else {

			for (let index = 0; index < datos.length; index++) {
				var data = {
					"id": datos[index].id_tipo_recurso,
				}

				recursos_en_reserva.push(data);
			}
		}
		return;

	});
}
const armar_no_disponibles = (array) => {
	let listado = "";
	for (let index = 0; index < recursos_sele.length; index++) {
		const element = recursos_sele[index];
		for (let jindex = 0; jindex < array.length; jindex++) {
			if (element.id == array[jindex]) {
				listado = listado + element.recurso + ", ";
			}
		}

	}
	return listado;
}

function buscar_reservas_activas_usuario_fecha(fecha_entrega, fecha_salida) {

	$.ajax({
		url: server_rese + "index.php/reservas_control/buscar_reservas_activas_usuario_fecha",
		dataType: "json",
		data: {
			fecha_entrega,
			fecha_salida
		},
		type: "post",
		success: function (datos) {
			if (datos == "sin_session") {
				close();
			} else if (datos == 0) {
				listar_recursos_disponibles(fecha_entrega, fecha_salida);
			} else if (datos == -1) {
				MensajeConClase("Tiene alguna(s) reserva(s) en curso en las horas solicitadas, por tal motivo no es posible registrar una nueva, para mas información se puede comunicar con el área de audiovisuales.!", "info", "Oops.!");
			} else {
				MensajeConClase("Error al consultar las reservas.!", "error", "Oops...");
			}

		},
		error: function () {

			console.log('Something went wrong', status, err);

		}
	});

}

const obtener_permisos = adm => {
	administra = adm ? true : false;
}

const limpiar_data_reserva = () => {
	iniciar_tabla_persona();
	$(".recursos_agregados").html("");
	$(".rec_sele").html("0");
	recursos_sele = [];
	$(".recursos_agregados").append("<option value=''>" + "0 Recurso(s) a Reservar" + "</option>");
}


const traer_data_prueba = (dato) => {
	consulta_ajax(`${server_rese}index.php/reservas_control/traer_data_prueba`, {}, data => {
		data.map((element) => {
			let { id, id_aux } = element;
			if (id_aux == 'Pru_Sum') data_reserva.tipo_prestamo = id;
			else if (id_aux == 'Est_Pre') data_reserva.tipo_estudio = id;
			else if (id_aux == 'Ent_Sol') data_reserva.tipo_entrega = id;
		});
	});

}

// function traer_pruebas_estudiante(identificacion) {
// 	consulta_ajax(`${server_rese}index.php/reservas_control/traer_pruebas_estudiante`, { identificacion }, data => {
// 		idpersona_seleccionada_re = 0;
// 		let table = $("#tabla_pruebas_estudiante").DataTable({
// 			"destroy": true,
// 			data,
// 			searching: false,
// 			"processing": true,
// 			"columns": [{
// 				"data": "fecha"
// 			},
// 			{
// 				"data": "hora_inicio"
// 			},
// 			{
// 				"data": "hora_fin"
// 			},
// 			],
// 			"language": get_idioma(),
// 			dom: 'Bfrtip',
// 			"buttons": []

// 		});

// 	});
// 	$("#modal_pruebas_estudiante").modal();
// }
